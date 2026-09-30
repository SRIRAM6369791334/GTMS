<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentProject;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StepOnePhoneNumbersTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }
        $this->actingAs($this->user);

        $this->district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);

        $rand = rand(10000, 99999) . rand(100, 999);
        $this->customer = Customer::create([
            'customer_name'            => 'Karthik Raja ' . $rand,
            'company_name'             => 'Raja Mining Works ' . $rand,
            'mobile_num'               => '98765' . rand(10000, 99999),
            'secondary_mobile_num'     => '91234' . rand(10000, 99999),
            'secondary_contact_person' => 'S. Murugan (Manager)',
            'email'                    => 'raja' . $rand . '@gtmstest.in',
            'mimas_no'                 => 'TN-MMS-TST-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    /**
     * Test Universal MIMAS lookup returns both primary and secondary mobile numbers.
     */
    public function test_customer_lookup_returns_primary_and_secondary_phone_numbers(): void
    {
        $response = $this->getJson('/customers/lookup-mimas/' . $this->customer->mimas_no);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'data' => [
                'mobile_num'               => $this->customer->mobile_num,
                'secondary_mobile_num'     => $this->customer->secondary_mobile_num,
                'secondary_contact_person' => $this->customer->secondary_contact_person,
            ],
        ]);
    }

    /**
     * App 1: Lease Application Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_lease_application_step1_renders_primary_and_secondary_phone_fields(): void
    {
        $response = $this->get(route('step1'));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_mobile_num');
        $response->assertSee('field_secondary_mobile_num');
    }

    /**
     * App 2: Mining Plan Intake (Pane 1) renders Primary and Secondary Phone Number fields.
     */
    public function test_mining_plan_intake_renders_primary_and_secondary_phone_fields(): void
    {
        $response = $this->get('/newapplication');

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_mobile_num');
        $response->assertSee('field_secondary_mobile_num');
    }

    /**
     * App 3: Environment Clearance Intake renders Primary and Secondary Phone Number fields and persists them.
     */
    public function test_environment_clearance_intake_renders_and_stores_phone_fields(): void
    {
        $response = $this->get(route('eviron.create'));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('contact_phone');
        $response->assertSee('secondary_phone');

        // Post intake form
        $postData = [
            'category'                 => 'B1',
            'sub_category'             => 'TOR',
            'customer_id'              => $this->customer->id,
            'client_name'              => $this->customer->customer_name,
            'company_name'             => $this->customer->company_name,
            'project_name'             => 'Test Stone Quarry EC',
            'district_id'              => $this->district->id,
            'location'                 => 'SF No 100, Test Village',
            'contact_name'             => $this->customer->customer_name,
            'secondary_contact_person' => 'Site Manager',
            'contact_phone'            => '9876543210',
            'secondary_phone'          => '9123456780',
            'contact_email'            => 'testec@gtms.in',
            'mimas_no'                 => $this->customer->mimas_no,
        ];

        $postResponse = $this->post(route('eviron.store'), $postData);
        $postResponse->assertRedirect();

        $this->assertDatabaseHas('environment_projects', [
            'project_name'    => 'Test Stone Quarry EC',
            'contact_phone'   => '9876543210',
            'secondary_phone' => '9123456780',
        ]);
    }

    /**
     * App 4: EC Certificate Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_ec_certificate_step1_renders_primary_and_secondary_phone_fields(): void
    {
        $r = rand(10000, 99999);
        $proj = EnvironmentProject::create([
            'project_code'    => 'ENV-B2-2026-' . $r,
            'customer_id'     => $this->customer->id,
            'category'        => 'B2',
            'project_name'    => 'Test Quarry Project For EC ' . $r,
            'district_id'     => $this->district->id,
            'status'          => 'approved',
            'contact_phone'   => '9842109876',
            'secondary_phone' => '9842109877',
        ]);

        $response = $this->get(route('ec-certificate.step', 1));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_primary_phone');
        $response->assertSee('field_secondary_phone');

        // Save step 1
        $saveResponse = $this->post(route('ec-certificate.saveStep', 1), [
            'environment_project_id' => $proj->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/' . $r,
            'parivesh_app_no'        => 'SIA/TN/MIN/' . $r . '/2026',
            'applicant_name'         => $this->customer->customer_name,
            'primary_phone'          => '9876543210',
            'secondary_phone'        => '9123456780',
            'issue_date'             => '2026-09-25',
            'validity_years'         => 5,
            'communication_type'     => 'Grant',
        ]);

        $saveResponse->assertRedirect(route('ec-certificate.step', 2));
        $draft = session('ec_wizard');
        $this->assertEquals('9876543210', $draft['step1']['primary_phone']);
        $this->assertEquals('9123456780', $draft['step1']['secondary_phone']);
    }

    /**
     * App 5: PPT Department Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_ppt_department_step1_renders_and_saves_phone_fields(): void
    {
        $response = $this->get(route('ppt-department.step', 1));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_ppt_mobile');
        $response->assertSee('field_ppt_secondary_mobile');

        $saveResponse = $this->post(route('ppt-department.saveStep', 1), [
            'customer_id'     => $this->customer->id,
            'project_name'    => 'PPT Quarry Presentation',
            'application_no'  => 'PPT-2026-' . rand(1000, 9999),
            'primary_phone'   => '9876543210',
            'secondary_phone' => '9123456780',
        ]);

        $saveResponse->assertRedirect(route('ppt-department.step', 2));
        $draft = session('ppt_wizard');
        $this->assertEquals('9876543210', $draft['primary_phone']);
        $this->assertEquals('9123456780', $draft['secondary_phone']);
    }

    /**
     * App 6: DGPS Survey Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_dgps_survey_step1_renders_and_saves_phone_fields(): void
    {
        $response = $this->get(route('dgps-survey.step', 1));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_dgps_primary_phone');
        $response->assertSee('field_dgps_secondary_phone');

        $saveResponse = $this->post(route('dgps-survey.saveStep', 1), [
            'customer_id'   => $this->customer->id,
            'lease_area_ha' => 4.5,
            'location'      => 'Salem Quarry Site',
            'survey_no'     => 'DGPS-2026-' . rand(1000, 9999),
            'primary_phone' => '9876543210',
            'secondary_phone' => '9123456780',
        ]);

        $saveResponse->assertRedirect(route('dgps-survey.step', 2));
        $draft = session('dgps_wizard');
        $this->assertEquals('9876543210', $draft['primary_phone']);
        $this->assertEquals('9123456780', $draft['secondary_phone']);
    }

    /**
     * App 7: Drone Survey Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_drone_survey_step1_renders_phone_fields(): void
    {
        $response = $this->get(route('drone-survey.step', 1));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_drone_primary_phone');
        $response->assertSee('field_drone_secondary_phone');
    }

    /**
     * App 8: EC Compliance Step 1 renders Primary and Secondary Phone Number fields.
     */
    public function test_ec_compliance_step1_renders_and_saves_phone_fields(): void
    {
        $response = $this->get(route('ec-compliance.step', 1));

        $response->assertStatus(200);
        $response->assertSee('Primary Phone Number');
        $response->assertSee('Secondary Phone Number');
        $response->assertSee('field_comp_primary_phone');
        $response->assertSee('field_comp_secondary_phone');

        $saveResponse = $this->post(route('ec-compliance.saveStep', 1), [
            'customer_id'        => $this->customer->id,
            'compliance_period'  => 'April 2026 - September 2026',
            'submission_due_date' => '2026-12-01',
            'project_name'       => 'Test Compliance Filing',
            'compliance_no'      => 'HYC-2026-' . rand(1000, 9999),
            'primary_phone'      => '9876543210',
            'secondary_phone'    => '9123456780',
        ]);

        $saveResponse->assertRedirect(route('ec-compliance.step', 2));
        $draft = session('ec_compliance_wizard');
        $this->assertEquals('9876543210', $draft['primary_phone']);
        $this->assertEquals('9123456780', $draft['secondary_phone']);
    }
}
