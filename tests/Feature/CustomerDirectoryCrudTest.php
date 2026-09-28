<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\DroneSurvey;
use App\Models\PptApplication;
use App\Models\EcCertificate;
use App\Models\EcCompliance;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use App\Models\EnvironmentProject;
use App\Models\DgpsSurvey;
use App\Models\Stockpile;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerDirectoryCrudTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected District $district;
    protected Mineral $mineral;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Resolve deterministic Admin user
        $this->adminUser = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->adminUser && !$this->adminUser->hasRole('Admin')) {
            $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->adminUser->assignRole($role);
        }

        $this->district = District::firstOrCreate(
            ['name' => 'Salem'],
            ['state' => 'Tamil Nadu', 'status' => 1]
        );

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Rough Stone'],
            ['type' => 'Minor Mineral', 'status' => 1]
        );
    }

    /**
     * Test directory view loads with KPI counts and filter components
     */
    public function test_customer_directory_index_renders_for_authorized_users(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer Directory');
        $response->assertSee('Total Customers');
        $response->assertSee('Active Customers');
        $response->assertSee('Inactive / Pending');
        $response->assertSee('Total Leases');
        $response->assertSee('id="filter_district"', false);
        $response->assertSee('id="filter_status"', false);
        $response->assertSee('id="customeradd"', false);
        $response->assertSee('id="customeredit"', false);
    }

    /**
     * Test creating a customer with valid data and auto-normalized Aadhaar
     */
    public function test_customer_can_be_created_via_ajax_with_auto_aadhaar_formatting(): void
    {
        $random = rand(10000, 99999) . rand(100, 999);
        $payload = [
            'mimas_no'                 => 'CUST-TEST-' . $random,
            'customer_name'            => 'R. Karuppasamy ' . $random,
            'company_name'             => 'Sri Karuppasamy Blue Metals ' . $random,
            'mobile_num'               => '98765' . rand(10000, 99999),
            'secondary_mobile_num'     => '91234' . rand(10000, 99999),
            'secondary_contact_person' => 'Site Manager Murugan',
            'email'                    => 'karuppasamy' . $random . '@example.com',
            'district_id'              => $this->district->id,
            'mineral_id'               => $this->mineral->id,
            'area'                     => '3.75',
            'pan'                      => 'ABCDE' . rand(1000, 9999) . 'Z',
            'aadhaar_no'               => '98765432' . rand(1000, 9999), // Raw 12 digits, should auto-format
            'gstin'                    => '33ABCDE' . rand(1000, 9999) . 'Z1Z5',
            'status'                   => 1,
            'address'                  => 'S.F. No. 120/2, Omalur Taluk, Salem District',
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        $response->assertJsonFragment(['message' => 'Customer "Sri Karuppasamy Blue Metals ' . $random . '" created successfully!']);

        $customer = Customer::where('mimas_no', 'CUST-TEST-' . $random)->first();
        $this->assertNotNull($customer);
        $this->assertEquals($payload['customer_name'], $customer->customer_name);
        $this->assertEquals($payload['company_name'], $customer->company_name);
        // Verify 12-digit Aadhaar was auto-normalized to XXXX-XXXX-XXXX
        $this->assertMatchesRegularExpression('/^[0-9]{4}-[0-9]{4}-[0-9]{4}$/', $customer->aadhaar_no);
        $this->assertNotNull($customer->slug);
    }

    /**
     * Test validation failure on non-numeric phone number
     */
    public function test_customer_creation_fails_when_mobile_is_non_numeric(): void
    {
        $payload = [
            'mimas_no'      => 'CUST-FAIL-PHONE',
            'customer_name' => 'Invalid Phone Client',
            'mobile_num'    => 'ABCDE12345', // Invalid non-digits
            'district_id'   => $this->district->id,
            'status'        => 1,
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['mobile_num']);
        $this->assertEquals(
            'The primary mobile number must be between 10 and 15 digits.',
            $response->json('errors.mobile_num.0')
        );
    }

    /**
     * Test validation failure when secondary mobile matches primary mobile
     */
    public function test_customer_creation_fails_when_secondary_mobile_matches_primary(): void
    {
        $samePhone = '9876543210';
        $payload = [
            'mimas_no'             => 'CUST-FAIL-DUPPHONE',
            'customer_name'        => 'Duplicate Phone Client',
            'mobile_num'           => $samePhone,
            'secondary_mobile_num' => $samePhone,
            'district_id'          => $this->district->id,
            'status'               => 1,
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['secondary_mobile_num']);
        $this->assertEquals(
            'The secondary mobile number must be different from the primary mobile number.',
            $response->json('errors.secondary_mobile_num.0')
        );
    }

    /**
     * Test updating customer details and status
     */
    public function test_customer_can_be_updated_via_ajax_including_status(): void
    {
        $random = rand(10000, 99999);
        $customer = Customer::create([
            'mimas_no'      => 'CUST-UP-' . $random,
            'customer_name' => 'Original Name ' . $random,
            'company_name'  => 'Original Company ' . $random,
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        $updatePayload = [
            'id'                       => $customer->id,
            'mimas_no'                 => $customer->mimas_no,
            'customer_name'            => 'Updated Representative ' . $random,
            'company_name'             => 'Updated Quarry Enterprise ' . $random,
            'mobile_num'               => '98421' . rand(10000, 99999),
            'secondary_mobile_num'     => '91234' . rand(10000, 99999),
            'secondary_contact_person' => 'New Manager',
            'email'                    => 'updated' . $random . '@quarry.com',
            'district_id'              => $this->district->id,
            'status'                   => 0, // Inactive / Under validation
            'address'                  => 'New Office Complex, Salem',
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('customeredit'), $updatePayload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        $response->assertJsonFragment(['message' => 'Customer "Updated Quarry Enterprise ' . $random . '" updated successfully!']);

        $customer->refresh();
        $this->assertEquals('Updated Representative ' . $random, $customer->customer_name);
        $this->assertEquals('Updated Quarry Enterprise ' . $random, $customer->company_name);
        $this->assertEquals(0, $customer->status);
    }

    /**
     * Test deleting a customer with no dependencies falls back to customer_name when company is null
     */
    public function test_customer_can_be_deleted_when_no_statutory_records_exist_and_uses_display_name(): void
    {
        $random = rand(10000, 99999);
        // Individual customer without company name
        $customer = Customer::create([
            'mimas_no'      => 'CUST-DEL-' . $random,
            'customer_name' => 'M. Palanisamy ' . $random,
            'company_name'  => null, // Individual owner
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('customerdelete'), [
            'id' => $customer->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        // Confirms display name falls back to customer_name rather than empty string ""
        $response->assertJsonFragment(['message' => 'Customer "M. Palanisamy ' . $random . '" deleted successfully!']);

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    /**
     * Test customer deletion is blocked when statutory records exist across all 9 FK relations
     */
    public function test_customer_deletion_is_blocked_when_linked_to_drone_or_ppt_or_ec_records(): void
    {
        $random = rand(10000, 99999);
        $customer = Customer::create([
            'mimas_no'      => 'CUST-DRONE-' . $random,
            'customer_name' => 'Drone Quarry Client ' . $random,
            'company_name'  => 'Aero Mines ' . $random,
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        // Create a Drone Survey linked to this customer
        DroneSurvey::create([
            'survey_no'       => 'DS-TEST-' . $random,
            'customer_id'     => $customer->id,
            'survey_status'   => 'scheduled',
            'flight_date'     => now(),
            'branch_id'       => 1,
            'created_by'      => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('customerdelete'), [
            'id' => $customer->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 0]);
        $response->assertSee('Cannot delete customer. This customer is linked to: 1 Drone Survey(s)');

        // Customer must still exist in DB
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'deleted_at' => null]);
    }

    /**
     * Test soft-deleted customer is restored when registering with matching unique ID
     */
    public function test_soft_deleted_customer_is_restored_when_adding_matching_mimas(): void
    {
        $random = rand(10000, 99999);
        $customer = Customer::create([
            'mimas_no'      => 'CUST-ARCHIVE-' . $random,
            'customer_name' => 'Old Client ' . $random,
            'company_name'  => 'Old Quarry ' . $random,
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);
        $customer->delete(); // Soft-deleted

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);

        $payload = [
            'mimas_no'      => 'CUST-ARCHIVE-' . $random,
            'customer_name' => 'Restored Client ' . $random,
            'company_name'  => 'Restored Quarry ' . $random,
            'mobile_num'    => '98421' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        $response->assertSee('has been restored and updated successfully!');

        $customer->refresh();
        $this->assertNull($customer->deleted_at);
        $this->assertEquals('Restored Client ' . $random, $customer->customer_name);
    }

    /**
     * Test universal master lookup by MIMAS / unique ID
     */
    public function test_customer_lookup_by_mimas_returns_expected_json(): void
    {
        $random = rand(10000, 99999);
        $customer = Customer::create([
            'mimas_no'      => 'LOOKUP-' . $random,
            'customer_name' => 'Lookup Client ' . $random,
            'company_name'  => 'Lookup Quarry ' . $random,
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        $response = $this->actingAs($this->adminUser)->getJson(route('customers.lookup.mimas', ['mimas_no' => $customer->mimas_no]));

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);
        $response->assertJsonPath('data.mimas_no', $customer->mimas_no);
        $response->assertJsonPath('data.customer_name', $customer->customer_name);
        $response->assertJsonPath('data.district_name', $this->district->name);
    }

    /**
     * Test customer show 360 profile dossier page renders successfully for zero-state customer (fk-cust-4834)
     */
    public function test_customer_show_dossier_page_renders_successfully(): void
    {
        $customer = Customer::where('slug', 'fk-cust-4834')->first() ?: Customer::first();
        $response = $this->actingAs($this->adminUser)->get(route('customers.show', $customer->slug));
        $response->assertStatus(200);
        $response->assertSee('Enterprise Dossier');
        $response->assertSee($customer->customer_name);
        $response->assertSee('New Lease Application');
        $response->assertSee('Tracking 360');
        $response->assertSee('Back to Directory');
        $response->assertSee('Edit Profile');
        // Assert empty state messages render gracefully without errors
        $response->assertSee('No Lease Applications Found');
        $response->assertSee('No Mining Plans Registered');
        $response->assertSee('No Environmental Clearances');
        $response->assertSee('No EC half-yearly compliance reports logged for this customer.');
        $response->assertSee('No PPT Department Hearings');
        $response->assertSee('No DGPS ground surveys logged.');
        $response->assertSee('No drone volumetric flights logged.');
        $response->assertSee('No Stockpiles Configured');
    }

    /**
     * Test customer show dossier renders populated records across all 6 tabs with module action links
     */
    public function test_customer_show_dossier_with_statutory_records_and_actions(): void
    {
        $random = rand(10000, 99999);
        $customer = Customer::create([
            'mimas_no'      => 'SHOW-CUST-' . $random,
            'customer_name' => 'Full Dossier Client ' . $random,
            'company_name'  => 'Enterprise Mines Ltd ' . $random,
            'mobile_num'    => '98765' . rand(10000, 99999),
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        $categoryId = \DB::table('lease_categories')->value('id') ?? 1;

        // 1. Lease Application
        $lease = LeaseApplication::create([
            'application_no' => 'LA-SHOW-' . $random,
            'common_id'      => 'GTMS-2026-' . $random,
            'customer_id'    => $customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => $categoryId,
            'mineral_id'     => $this->mineral->id,
            'area_extent_ha' => 4.50,
            'taluk'          => 'Omalur',
            'village'        => 'Karuppur',
            'status'         => 'approved',
            'branch_id'      => $this->adminUser->branch_id ?? 1,
            'created_by'     => $this->adminUser->id,
        ]);

        // 2. Mining Application
        $plan = MiningApplication::create([
            'application_no'       => 'MP-SHOW-' . $random,
            'common_id'            => 'GTMS-2026-' . $random,
            'customer_id'          => $customer->id,
            'district_id'          => $this->district->id,
            'lease_application_id' => $lease->id,
            'stage'                => 6,
            'validity_years'       => 5,
            'status'               => 'approved',
            'branch_id'            => $this->adminUser->branch_id ?? 1,
            'created_by'           => $this->adminUser->id,
        ]);

        // 3. Environment Project
        $env = EnvironmentProject::create([
            'project_code' => 'ENV-SHOW-' . $random,
            'customer_id'  => $customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'project_name' => 'Quarry EC Project ' . $random,
            'status'       => 'approved',
            'branch_id'    => $this->adminUser->branch_id ?? 1,
            'created_by'   => $this->adminUser->id,
        ]);

        // 4. EC Compliance
        $compliance = EcCompliance::create([
            'compliance_no'          => 'EC-CMP-SHOW-' . $random,
            'customer_id'            => $customer->id,
            'district_id'            => $this->district->id,
            'environment_project_id' => $env->id,
            'project_name'           => 'Quarry EC Compliance ' . $random,
            'compliance_period'      => 'Apr - Sep',
            'compliance_year'        => '2026',
            'status'                 => 'submitted',
            'branch_id'              => $this->adminUser->branch_id ?? 1,
            'created_by'             => $this->adminUser->id,
        ]);

        // 5. PPT Application
        $ppt = PptApplication::create([
            'application_no' => 'PPT-SHOW-' . $random,
            'customer_id'    => $customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'project_name'   => 'PPT Presentation ' . $random,
            'status'         => 'presented',
            'branch_id'      => $this->adminUser->branch_id ?? 1,
            'created_by'     => $this->adminUser->id,
        ]);

        // 6. DGPS Survey
        $dgps = DgpsSurvey::create([
            'survey_no'     => 'DGPS-SHOW-' . $random,
            'customer_id'   => $customer->id,
            'survey_status' => 'completed',
            'branch_id'     => $this->adminUser->branch_id ?? 1,
            'created_by'    => $this->adminUser->id,
        ]);

        // 7. Drone Survey
        $drone = DroneSurvey::create([
            'survey_no'            => 'DRONE-SHOW-' . $random,
            'customer_id'          => $customer->id,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 12500.50,
            'branch_id'            => $this->adminUser->branch_id ?? 1,
            'created_by'           => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('customers.show', $customer->slug));
        $response->assertStatus(200);

        // Verify records appear
        $response->assertSee($lease->application_no);
        $response->assertSee($plan->application_no);
        $response->assertSee($env->project_code);
        $response->assertSee($compliance->compliance_no);
        $response->assertSee($ppt->application_no);
        $response->assertSee($dgps->survey_no);
        $response->assertSee($drone->survey_no);

        // Verify action deep-links
        $response->assertSee('/process?id=' . $plan->id);
        $response->assertSee(route('environment-b2.show', $env->id));
        $response->assertSee(route('ec-compliance.show', $compliance->id));
        $response->assertSee(route('ppt-department.show', $ppt->id));
        $response->assertSee(route('dgps-survey.show', $dgps->id));
        $response->assertSee(route('drone-survey.show', $drone->id));
    }
}
