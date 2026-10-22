<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use App\Models\EnvironmentProject;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class CompanyCustomerAssociationTest
 * 
 * Verifies multi-contact company customer workflows:
 * - Datalist suggestions & company contact counting in Customer Directory
 * - Multiple partners sharing the same company without overwriting distinct personal details
 * - Company Partners dossier card in Customer Show
 * - Autocomplete live search with contact counts in Customer Tracking
 * - Unified Corporate Portfolio aggregation across all company partners
 * 
 * Governed strictly by TEST_DATA_SAFETY_PROTOCOL.md using DatabaseTransactions.
 */
class CompanyCustomerAssociationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected District $district;
    protected Mineral $mineral;
    protected string $testRunId;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->testRunId = 'qa_' . substr(md5(uniqid('', true)), 0, 8);

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

    protected function tearDown(): void
    {
        if (!empty($this->testRunId)) {
            $testCustomers = Customer::where('mimas_no', 'like', "%{$this->testRunId}%")
                ->orWhere('company_name', 'like', "%{$this->testRunId}%")
                ->orWhere('customer_name', 'like', "%{$this->testRunId}%")
                ->orWhere('slug', 'like', "%{$this->testRunId}%")
                ->get();

            $ids = $testCustomers->pluck('id')->all();
            if (!empty($ids)) {
                LeaseApplication::whereIn('customer_id', $ids)->forceDelete();
                MiningApplication::whereIn('customer_id', $ids)->forceDelete();
                Customer::whereIn('id', $ids)->forceDelete();
            }
            LeaseApplication::where('application_no', 'like', "%{$this->testRunId}%")->forceDelete();
            MiningApplication::where('application_no', 'like', "%{$this->testRunId}%")->forceDelete();
        }

        parent::tearDown();
    }

    /**
     * Test Customer Directory provides existing companies and counts in view
     */
    public function test_customer_directory_supplies_existing_companies_and_contact_counts(): void
    {
        $companyName = 'Kaveri Blue Granites ' . $this->testRunId;

        // Partner 1
        Customer::create([
            'customer_name' => 'Partner One ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9841' . rand(100000, 999999),
            'email'         => 'partner1_' . $this->testRunId . '@example.com',
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'status'        => 1,
            'slug'          => 'partner-one-' . $this->testRunId,
        ]);

        // Partner 2
        Customer::create([
            'customer_name' => 'Partner Two ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9842' . rand(100000, 999999),
            'email'         => 'partner2_' . $this->testRunId . '@example.com',
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'status'        => 1,
            'slug'          => 'partner-two-' . $this->testRunId,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('customers.index'));

        $response->assertStatus(200);
        $response->assertViewHas('existingCompanies', function ($companies) use ($companyName) {
            return in_array($companyName, $companies->toArray());
        });

        $response->assertViewHas('companyContactCounts', function ($counts) use ($companyName) {
            return isset($counts[$companyName]) && $counts[$companyName] >= 2;
        });

        // HTML assertion: datalist exists and company option is present
        $response->assertSee('id="existing_companies"', false);
        $response->assertSee($companyName);
        $response->assertSee('2 Contacts');
    }

    /**
     * Test creating two customers with the same company name preserves separate personal details
     */
    public function test_two_customers_with_same_company_preserve_distinct_personal_data(): void
    {
        $sharedCompany = 'Thirumurugan Mines Pvt Ltd ' . $this->testRunId;

        $payloadA = [
            'mimas_no'                 => 'MIMAS-A-' . $this->testRunId,
            'customer_name'            => 'Director Raman ' . $this->testRunId,
            'company_name'             => $sharedCompany,
            'mobile_num'               => '9788' . rand(100000, 999999),
            'pan'                      => 'AAAAA' . rand(1000, 9999) . 'A',
            'aadhaar_no'               => '9111-2222-' . rand(1000, 9999),
            'email'                    => 'raman_' . $this->testRunId . '@example.com',
            'district_id'              => $this->district->id,
            'mineral_id'               => $this->mineral->id,
            'status'                   => 1,
            'address'                  => 'Office 1, Salem Main Road',
        ];

        $payloadB = [
            'mimas_no'                 => 'MIMAS-B-' . $this->testRunId,
            'customer_name'            => 'Director Senthil ' . $this->testRunId,
            'company_name'             => $sharedCompany,
            'mobile_num'               => '9888' . rand(100000, 999999),
            'pan'                      => 'BBBBB' . rand(1000, 9999) . 'B',
            'aadhaar_no'               => '9333-4444-' . rand(1000, 9999),
            'email'                    => 'senthil_' . $this->testRunId . '@example.com',
            'district_id'              => $this->district->id,
            'mineral_id'               => $this->mineral->id,
            'status'                   => 1,
            'address'                  => 'Plot 5B, SIDCO Industrial Estate',
        ];

        $resA = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payloadA);
        $resA->assertStatus(200);
        $resA->assertJson(['status' => 1]);

        $resB = $this->actingAs($this->adminUser)->postJson(route('customeradd'), $payloadB);
        $resB->assertStatus(200);
        $resB->assertJson(['status' => 1]);

        $custA = Customer::where('mimas_no', $payloadA['mimas_no'])->first();
        $custB = Customer::where('mimas_no', $payloadB['mimas_no'])->first();

        $this->assertNotNull($custA);
        $this->assertNotNull($custB);
        $this->assertNotEquals($custA->id, $custB->id);
        $this->assertEquals($custA->company_name, $custB->company_name);

        // Strict Check: Personal data must remain completely distinct
        $this->assertNotEquals($custA->customer_name, $custB->customer_name);
        $this->assertNotEquals($custA->mobile_num, $custB->mobile_num);
        $this->assertNotEquals($custA->pan, $custB->pan);
        $this->assertNotEquals($custA->aadhaar_no, $custB->aadhaar_no);
        $this->assertNotEquals($custA->address, $custB->address);
    }

    /**
     * Test Customer Show page displays Company Partners card
     */
    public function test_customer_show_page_displays_company_partners_card(): void
    {
        $companyName = 'Kongu Minerals Corp ' . $this->testRunId;

        $cust1 = Customer::create([
            'customer_name' => 'Managing Partner A ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9123' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'status'        => 1,
            'slug'          => 'cust-one-' . $this->testRunId,
        ]);

        $cust2 = Customer::create([
            'customer_name' => 'Partner B ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9456' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'status'        => 1,
            'slug'          => 'cust-two-' . $this->testRunId,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('customers.show', $cust1->slug));

        $response->assertStatus(200);
        $response->assertViewHas('companyPartners');
        $response->assertSee('Company Partners');
        $response->assertSee('Partner B ' . $this->testRunId);
        $response->assertSee($cust2->mobile_num);
    }

    /**
     * Test live search autocomplete in Customer Tracking returns company contacts count
     */
    public function test_customer_tracking_live_search_returns_company_contacts_count(): void
    {
        $companyName = 'Bhavani Rock Mines ' . $this->testRunId;

        $p1 = Customer::create([
            'customer_name' => 'Bhavani Partner 1 ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9711' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'status'        => 1,
            'slug'          => 'bhavani-p1-' . $this->testRunId,
        ]);

        $p2 = Customer::create([
            'customer_name' => 'Bhavani Partner 2 ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9722' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'status'        => 1,
            'slug'          => 'bhavani-p2-' . $this->testRunId,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->getJson(route('customer-tracking.search', ['q' => 'Bhavani Rock Mines ' . $this->testRunId]));

        $response->assertStatus(200);
        $results = $response->json('results');
        $this->assertNotEmpty($results);

        $matchedItem = collect($results)->firstWhere('name', $p1->customer_name);
        $this->assertNotNull($matchedItem);
        $this->assertEquals(2, $matchedItem['company_contacts_count']);
        $this->assertEquals($companyName, $matchedItem['company']);
    }

    /**
     * Test Customer Tracking unifies concessions across company partners
     */
    public function test_customer_tracking_dossier_unifies_concessions_for_company_partners(): void
    {
        $companyName = 'Cauvery Aggregate Solutions ' . $this->testRunId;

        $custA = Customer::create([
            'customer_name' => 'Director Arun ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9611' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'status'        => 1,
            'slug'          => 'arun-' . $this->testRunId,
        ]);

        $custB = Customer::create([
            'customer_name' => 'Director Bala ' . $this->testRunId,
            'company_name'  => $companyName,
            'mobile_num'    => '9622' . rand(100000, 999999),
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'status'        => 1,
            'slug'          => 'bala-' . $this->testRunId,
        ]);

        $categoryId = \DB::table('lease_categories')->value('id') ?? 1;

        // Lease application filed by Arun
        $lease = LeaseApplication::create([
            'customer_id'    => $custA->id,
            'application_no' => 'LA-TEST-' . $this->testRunId,
            'district_id'    => $this->district->id,
            'category_id'    => $categoryId,
            'mineral_id'     => $this->mineral->id,
            'village'        => 'Thoppur',
            'status'         => 'approved',
            'branch_id'      => $this->adminUser->branch_id ?? 1,
            'created_by'     => $this->adminUser->id,
        ]);

        // Mining Plan filed by Bala
        $mining = MiningApplication::create([
            'customer_id'    => $custB->id,
            'application_no' => 'MP-TEST-' . $this->testRunId,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'village'        => 'Kattur',
            'status'         => 'Approved',
            'branch_id'      => $this->adminUser->branch_id ?? 1,
            'created_by'     => $this->adminUser->id,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('customer-tracking.show', $custA->slug));

        $response->assertStatus(200);
        $dossier = $response->viewData('dossierData');
        $this->assertNotNull($dossier);
        $this->assertTrue($dossier['is_company_unified']);
        $this->assertEquals($companyName, $dossier['company_name']);
        $this->assertCount(2, $dossier['company_partners']);

        // Assert Unified Corporate Portfolio banner is visible in HTML
        $response->assertSee('Unified Corporate Portfolio');
        $response->assertSee('Director Arun ' . $this->testRunId);
        $response->assertSee('Director Bala ' . $this->testRunId);
        $response->assertSee('LA-TEST-' . $this->testRunId);
        $response->assertSee('MP-TEST-' . $this->testRunId);

        // Assert customer applications catalog aggregates both applications
        $catalog = $response->viewData('customerAppsCatalog');
        $this->assertNotNull($catalog);
        $catalogKeys = $catalog->pluck('key')->all();
        $this->assertContains('lease_' . $lease->id, $catalogKeys);
        $this->assertContains('mining_' . $mining->id, $catalogKeys);

        // Assert applicant names are preserved in catalog
        $leaseItem = $catalog->firstWhere('key', 'lease_' . $lease->id);
        $miningItem = $catalog->firstWhere('key', 'mining_' . $mining->id);
        $this->assertEquals($custA->customer_name, $leaseItem['applicant_name']);
        $this->assertEquals($custB->customer_name, $miningItem['applicant_name']);
    }
}
