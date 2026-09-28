<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentProject;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocumentRequirementToggleTest extends TestCase
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

        $rand = rand(1000, 9999);
        $this->customer = Customer::create([
            'customer_name'            => 'Karthik Raja ' . $rand,
            'company_name'             => 'Raja Mining Works ' . $rand,
            'mobile_num'               => '987654' . $rand,
            'secondary_mobile_num'     => '912345' . $rand,
            'secondary_contact_person' => 'S. Murugan (Manager)',
            'email'                    => 'raja' . $rand . '@gtmstest.in',
            'mimas_no'                 => 'TN-MMS-TST-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    /**
     * App 1: Lease Application Step 5 AJAX requirement toggle endpoint works.
     */
    public function test_lease_application_step5_saves_document_requirement(): void
    {
        $response = $this->postJson(route('step5.requirement'), [
            'doc_item' => '1',
            'requirement' => 'optional',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'doc_item' => '1',
            'requirement' => 'optional',
        ]);

        $this->assertEquals('optional', session('lease_draft.doc_requirements.1'));
    }

    /**
     * App 1: Lease Application Step 5 renders doc-req-select dropdowns.
     */
    public function test_lease_application_step5_renders_doc_req_select(): void
    {
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'district_id' => $this->district->id,
                'minerals' => [1],
                'extent' => '2.50.0',
                'applicant_name' => $this->customer->customer_name,
                'company_name' => $this->customer->company_name,
                'mobile_num' => $this->customer->mobile_num,
                'taluk' => 'Salem',
                'village' => 'Omalur',
                'survey_no' => '100/1',
            ]
        ]);

        $response = $this->get(route('step5'));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 2: Mining Plan renders doc-req-select and doc_requirements fields.
     */
    public function test_mining_plan_renders_doc_req_select(): void
    {
        $response = $this->get('/newapplication');
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('name="doc_requirements[', false);
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 3: Environment Clearance show view renders doc-req-select.
     */
    public function test_environment_clearance_show_renders_doc_req_select(): void
    {
        $project = EnvironmentProject::create([
            'project_code' => 'ENV-B1-' . date('Y') . '-' . rand(1000, 9999),
            'customer_id' => $this->customer->id,
            'category' => 'B1',
            'sub_category' => 'TOR',
            'project_name' => 'Test Quarry Project',
            'district_id' => $this->district->id,
            'contact_name' => 'Tester',
            'contact_phone' => '9876543210',
            'status' => 'draft',
            'branch_id' => 1,
        ]);

        $response = $this->get(route('eviron.show', $project->id));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 3: Environment Clearance B2 wizard Step 4 renders doc-req-select.
     */
    public function test_environment_b2_wizard_step4_renders_doc_req_select(): void
    {
        $response = $this->get(route('environment-b2.step', 4));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 4: EC Certificate Wizard Step 2 renders doc-req-select.
     */
    public function test_ec_certificate_step2_renders_doc_req_select(): void
    {
        session([
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => 1,
                    'ec_ref_no' => 'SEIAA-TN/EC/TEST-001',
                    'applicant_name' => 'Test Applicant',
                    'issue_date' => date('Y-m-d'),
                    'validity_years' => 5,
                    'communication_type' => 'Grant',
                    'completed' => true,
                ]
            ]
        ]);

        $response = $this->get(route('ec-certificate.step', 2));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('certificate_file_req');
        $response->assertSee('support_doc_1_req');
    }

    /**
     * App 5: PPT Department Wizard Step 5 renders doc-req-select.
     */
    public function test_ppt_department_step5_renders_doc_req_select(): void
    {
        $response = $this->get(route('ppt-department.step', 5));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('ppt_doc_req');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 6: DGPS Survey Wizard Step 3 renders doc-req-select.
     */
    public function test_dgps_survey_step3_renders_doc_req_select(): void
    {
        $response = $this->get(route('dgps-survey.step', 3));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('dgps_doc_req');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 7: Drone Survey Wizard Step 3 renders doc-req-select.
     */
    public function test_drone_survey_step3_renders_doc_req_select(): void
    {
        $response = $this->get(route('drone-survey.step', 3));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('drone_doc_req');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * App 8: EC Compliance Wizard Step 2 renders doc-req-select.
     */
    public function test_ec_compliance_step2_renders_doc_req_select(): void
    {
        $response = $this->get(route('ec-compliance.step', 2));
        $response->assertStatus(200);
        $response->assertSee('doc-req-select');
        $response->assertSee('comp_doc_req');
        $response->assertSee('Mandatory');
        $response->assertSee('Optional');
    }

    /**
     * Universal Compliance: Codebase contains zero Tamil characters.
     */
    public function test_codebase_contains_zero_tamil_characters(): void
    {
        $scanPaths = [
            app_path(),
            resource_path('views'),
            base_path('routes'),
        ];

        $tamilRegex = '/[\x{0B80}-\x{0BFF}]/u';
        $violations = [];

        foreach ($scanPaths as $path) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;
                $ext = $file->getExtension();
                if (!in_array($ext, ['php', 'js', 'css'])) continue;

                $content = file_get_contents($file->getPathname());
                if (preg_match($tamilRegex, $content)) {
                    $violations[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertEmpty(
            $violations,
            "Tamil characters detected in source files:\n" . implode("\n", $violations)
        );
    }
}
