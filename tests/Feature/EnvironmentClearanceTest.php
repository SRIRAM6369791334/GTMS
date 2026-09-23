<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\EcCertificate;
use App\Models\EnvironmentProject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class EnvironmentClearanceTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        // Resolve or create an authenticated user with permissions
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->user = $user;
        $this->actingAs($this->user);
    }

    /**
     * Test R1: Direct navigation to step 4 with empty session redirects to step 1
     */
    public function test_deep_linking_step_4_with_empty_session_redirects_to_step_1(): void
    {
        $response = $this->withSession([])->get(route('ec-certificate.step', 4));

        $response->assertRedirect(route('ec-certificate.step', 1));
        $response->assertSessionHas('info');
    }

    /**
     * Test R1: Direct navigation to step 2 with empty session redirects to step 1
     */
    public function test_deep_linking_step_2_with_empty_session_redirects_to_step_1(): void
    {
        $response = $this->withSession([])->get(route('ec-certificate.step', 2));

        $response->assertRedirect(route('ec-certificate.step', 1));
        $response->assertSessionHas('info');
    }

    /**
     * Test R1: Step 1 loads successfully and renders disabled stepper pills for future steps
     */
    public function test_step_1_loads_and_future_stepper_pills_are_disabled(): void
    {
        $response = $this->get(route('ec-certificate.step', 1));

        $response->assertStatus(200);
        $response->assertSee('cursor:not-allowed');
        $response->assertSee('Parivesh Details');
        $response->assertSee('Upload EC PDF');
    }

    /**
     * Test R1: Completing Step 1 unlocks Step 2
     */
    public function test_completing_step_1_unlocks_step_2(): void
    {
        $project = EnvironmentProject::first();
        if (!$project) {
            $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
            $customer = Customer::first() ?? Customer::create([
                'customer_name' => 'Test Customer',
                'mobile_num'    => '9876543210',
            ]);
            $project = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-9999',
                'customer_id'   => $customer->id,
                'category'      => 'B2',
                'project_name'  => 'Test Granite Quarry',
                'district_id'   => $district->id,
                'contact_phone' => '9876543210',
                'status'        => 'approved',
            ]);
        }

        $postData = [
            'environment_project_id' => $project->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/TEST01',
            'parivesh_app_no'        => 'SIA/TN/MIN/10001/2026',
            'applicant_name'         => 'Test Enterprise',
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => 5,
            'communication_type'     => 'Grant',
            'conditions_summary'     => 'Standard safeguards',
        ];

        $saveResponse = $this->post(route('ec-certificate.saveStep', 1), $postData);
        $saveResponse->assertRedirect(route('ec-certificate.step', 2));

        $step2Response = $this->get(route('ec-certificate.step', 2));
        $step2Response->assertStatus(200);
        $step2Response->assertSee('SEIAA-TN/EC/2026/TEST01');
    }

    /**
     * Test R2: EC Certificate Index returns 200 with authentic view link
     */
    public function test_ec_certificate_index_displays_authentic_view_links(): void
    {
        $response = $this->get(route('ec-certificate.index'));

        $response->assertStatus(200);
        $response->assertSee('Environmental Clearance Certificates Register');
        $response->assertSee('dossier-kpi-card');
    }

    /**
     * Test R2: EC Certificate Show returns 200 with official letterhead view
     */
    public function test_ec_certificate_show_displays_authentic_certificate(): void
    {
        $cert = EcCertificate::first();
        if (!$cert) {
            $project = EnvironmentProject::first();
            $cert = EcCertificate::create([
                'ec_ref_no'              => 'SEIAA-TN/EC/2026/TEST99',
                'environment_project_id' => $project?->id,
                'applicant_name'         => 'Authentic Granite Ltd',
                'issue_date'             => date('Y-m-d'),
                'validity_years'         => 5,
                'communication_type'     => 'Grant',
                'status'                 => 'active',
            ]);
        }

        $response = $this->get(route('ec-certificate.show', $cert->id));
        $response->assertStatus(200);
        $response->assertSee($cert->ec_ref_no);
        $response->assertSee('STATE ENVIRONMENT IMPACT ASSESSMENT AUTHORITY');
        $response->assertSee('Open Project Folders');
    }

    /**
     * Test R2 & R3: Environment Clearance index returns 200 with proper KPIs and links
     */
    public function test_eviron_index_renders_properly(): void
    {
        $response = $this->get(route('eviron.index'));

        $response->assertStatus(200);
        $response->assertSee('Environment Clearance');
        $response->assertSee('dossier-kpi-card');
    }

    /**
     * Test R3: Environment Clearance create view renders category pills and customer datalist
     */
    public function test_eviron_create_renders_category_pills_and_datalist(): void
    {
        $response = $this->get(route('eviron.create'));

        $response->assertStatus(200);
        $response->assertSee('Category B1');
        $response->assertSee('Category B2');
        $response->assertSee('customer_datalist');
        $response->assertSee('Sub Category 1');
        $response->assertSee('Sub Category 2');
    }

    /**
     * Test R3: Storing B2 project sets sub_category to null
     */
    public function test_storing_b2_project_intake(): void
    {
        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Intake Test Customer',
            'mobile_num'    => '9876543211',
        ]);

        $postData = [
            'category'      => 'B2',
            'sub_category'  => 'SC1', // Even if submitted, B2 must store null
            'customer_id'   => $customer->id,
            'client_name'   => 'Intake Test Customer',
            'company_name'  => 'Intake Minerals LLP',
            'project_name'  => 'Intake Test Quarry Project',
            'district_id'   => $district->id,
            'location'      => 'SF 999, Intake Village',
            'contact_phone' => '9876543211',
        ];

        $response = $this->post(route('eviron.store'), $postData);
        $response->assertSessionHasNoErrors();

        $created = EnvironmentProject::where('project_name', 'Intake Test Quarry Project')->latest()->first();
        $this->assertNotNull($created);
        $this->assertEquals('B2', $created->category);
        $this->assertNull($created->sub_category);

        $response->assertRedirect(route('eviron.show', $created->id));
    }

    /**
     * Test R1: Deep linking into Step 6 when only Step 1 is completed redirects to Step 2
     */
    public function test_deep_linking_step_6_after_step_1_only_redirects_to_step_2(): void
    {
        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => 1,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/0001',
                    'parivesh_app_no'        => 'SIA/TN/MIN/10001/2026',
                    'applicant_name'         => 'Test Applicant',
                    'issue_date'             => date('Y-m-d'),
                    'validity_years'         => 5,
                    'communication_type'     => 'Grant',
                    'completed'              => true,
                ],
            ],
        ];

        $response = $this->withSession($session)->get(route('ec-certificate.step', 6));
        $response->assertRedirect(route('ec-certificate.step', 2));
        $response->assertSessionHas('info');
    }

    /**
     * Test R1: Navigating back to Step 1 after completing Step 1 keeps Step 2 navigable
     */
    public function test_step_1_retains_step_2_navigation_when_step_1_is_completed(): void
    {
        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => 1,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/0001',
                    'parivesh_app_no'        => 'SIA/TN/MIN/10001/2026',
                    'applicant_name'         => 'Test Applicant',
                    'issue_date'             => date('Y-m-d'),
                    'validity_years'         => 5,
                    'communication_type'     => 'Grant',
                    'completed'              => true,
                ],
            ],
        ];

        $response = $this->withSession($session)->get(route('ec-certificate.step', 1));
        $response->assertStatus(200);
        // Step 2 should have a valid link, not be disabled
        $response->assertSee(route('ec-certificate.step', 2));
    }

    /**
     * Test R1 & R3: Switching project in Step 1 wipes downstream draft artifacts
     */
    public function test_project_switching_in_step_1_purges_stale_downstream_drafts(): void
    {
        $proj1 = EnvironmentProject::first() ?? EnvironmentProject::create([
            'project_code'  => 'ENV-B2-2026-0001',
            'category'      => 'B2',
            'project_name'  => 'Project 1',
            'district_id'   => 1,
            'contact_phone' => '9876543210',
            'status'        => 'approved',
        ]);

        $proj2 = EnvironmentProject::where('id', '!=', $proj1->id)->first();
        if (!$proj2) {
            $proj2 = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-0002',
                'category'      => 'B2',
                'project_name'  => 'Project 2',
                'district_id'   => 1,
                'contact_phone' => '9876543211',
                'status'        => 'approved',
            ]);
        }

        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $proj1->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/0001',
                    'completed'              => true,
                ],
                'step2' => [
                    'file_path' => 'uploads/ec_certificates/PROJ1/EC_cert.pdf',
                    'file_name' => 'PROJ1_cert.pdf',
                ],
                'step3' => [
                    'preview_verified' => true,
                ],
            ],
        ];

        // Switch to proj2 via GET parameter
        $response = $this->withSession($session)->get(route('ec-certificate.step', 1) . '?project_id=' . $proj2->id);
        $response->assertStatus(200);

        // Verify downstream draft was wiped
        $draft = session('ec_wizard');
        $this->assertEquals($proj2->id, $draft['step1']['environment_project_id']);
        $this->assertFalse($draft['step1']['completed']);
        $this->assertArrayNotHasKey('step2', $draft);
        $this->assertArrayNotHasKey('step3', $draft);
    }

    /**
     * Test R2: Environment Clearance Show page renders project details, folders, and actions
     */
    public function test_eviron_show_renders_project_details_and_contextual_actions(): void
    {
        $project = EnvironmentProject::first();
        if (!$project) {
            $project = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-7777',
                'category'      => 'B2',
                'project_name'  => 'Show View Test Project',
                'district_id'   => 1,
                'contact_phone' => '9876543210',
                'status'        => 'approved',
            ]);
        }

        $response = $this->get(route('eviron.show', $project->id));
        $response->assertStatus(200);
        $response->assertSee($project->project_code);
        $response->assertSee('Process Flow');
    }

    /**
     * Test R3: Category B1 intake without sub_category fails validation
     */
    public function test_b1_intake_requires_sub_category(): void
    {
        $postData = [
            'category'      => 'B1',
            // Missing sub_category
            'client_name'   => 'B1 Test Client',
            'project_name'  => 'B1 Test Project',
            'district_id'   => 1,
            'contact_phone' => '9876543210',
        ];

        $response = $this->post(route('eviron.store'), $postData);
        $response->assertSessionHasErrors('sub_category');
    }

    /**
     * Test R1 & R2: Full 6-step wizard workflow compiles and returns HTTP 200 at every step
     */
    public function test_all_wizard_steps_render_http_200_when_unlocked(): void
    {
        $project = EnvironmentProject::first();
        if (!$project) {
            $project = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-8888',
                'category'      => 'B2',
                'project_name'  => 'All Steps Test Project',
                'district_id'   => 1,
                'contact_phone' => '9876543210',
                'status'        => 'approved',
            ]);
        }

        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $project->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/8888',
                    'parivesh_app_no'        => 'SIA/TN/MIN/18888/2026',
                    'applicant_name'         => 'Test Enterprise',
                    'issue_date'             => date('Y-m-d'),
                    'validity_years'         => 5,
                    'communication_type'     => 'Grant',
                    'conditions_summary'     => 'Standard safeguards applied.',
                    'completed'              => true,
                ],
                'step2' => [
                    'file_path'   => null,
                    'file_name'   => 'Auto-generated Digital Clearance Notice.pdf',
                    'file_size'   => 124800,
                    'uploaded_at' => now()->toDateTimeString(),
                ],
                'step3' => [
                    'preview_verified' => true,
                    'verified_at'      => now()->toDateTimeString(),
                ],
                'step4' => [
                    'storage_confirmed' => true,
                    'primary_folder'    => 'EC Certificate & Statutory Grants',
                ],
                'step5' => [
                    'communication_type' => 'Grant',
                    'recipient_email'    => 'applicant@example.com',
                    'recipient_phone'    => '9876543210',
                    'communication_note' => 'Formal grant notice.',
                ],
            ],
        ];

        for ($s = 1; $s <= 6; $s++) {
            $res = $this->withSession($session)->get(route('ec-certificate.step', $s));
            $res->assertStatus(200);
            $res->assertSee('Step ' . $s . ' of 6');
        }
    }

    /**
     * Test Adversarial: Direct POST to saveStep for a locked step redirects and prevents session corruption
     */
    public function test_saving_locked_step_redirects_and_prevents_session_corruption(): void
    {
        // Session has only Step 1 completed, so Step 4 is locked
        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => 1,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/0001',
                    'applicant_name'         => 'Test Enterprise',
                    'completed'              => true,
                ],
            ],
        ];

        $response = $this->withSession($session)->post(route('ec-certificate.saveStep', 4), []);
        $response->assertRedirect(route('ec-certificate.step', 2));
        $response->assertSessionHas('info');

        $draft = session('ec_wizard');
        $this->assertArrayNotHasKey('step4', $draft);
    }

    /**
     * Test Adversarial: Submitting Step 1 with a different project ID purges downstream draft artifacts
     */
    public function test_submitting_step_1_with_different_project_purges_downstream_drafts(): void
    {
        $proj1 = EnvironmentProject::first() ?? EnvironmentProject::create([
            'project_code'  => 'ENV-B2-2026-0001',
            'category'      => 'B2',
            'project_name'  => 'Project 1',
            'district_id'   => 1,
            'contact_phone' => '9876543210',
            'status'        => 'approved',
        ]);

        $proj2 = EnvironmentProject::where('id', '!=', $proj1->id)->first();
        if (!$proj2) {
            $proj2 = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-0002',
                'category'      => 'B2',
                'project_name'  => 'Project 2',
                'district_id'   => 1,
                'contact_phone' => '9876543211',
                'status'        => 'approved',
            ]);
        }

        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $proj1->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/0001',
                    'completed'              => true,
                ],
                'step2' => [
                    'file_path' => 'uploads/ec_certificates/PROJ1/test.pdf',
                    'file_name' => 'proj1.pdf',
                ],
            ],
        ];

        $postData = [
            'environment_project_id' => $proj2->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/0002',
            'parivesh_app_no'        => 'SIA/TN/MIN/20002/2026',
            'applicant_name'         => 'New Project Enterprise',
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => 5,
            'communication_type'     => 'Grant',
        ];

        $response = $this->withSession($session)->post(route('ec-certificate.saveStep', 1), $postData);
        $response->assertRedirect(route('ec-certificate.step', 2));

        $draft = session('ec_wizard');
        $this->assertEquals($proj2->id, $draft['step1']['environment_project_id']);
        $this->assertArrayNotHasKey('step2', $draft);
    }

    /**
     * Test Adversarial: Step 1 validation rejects negative validity years and invalid communication type
     */
    public function test_step_1_validation_rejects_negative_validity_and_invalid_communication_type(): void
    {
        $project = EnvironmentProject::first();

        $postData = [
            'environment_project_id' => $project ? $project->id : 1,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/0099',
            'applicant_name'         => 'Test Enterprise',
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => -5, // Invalid negative
            'communication_type'     => 'InvalidType', // Invalid enum
        ];

        $response = $this->post(route('ec-certificate.saveStep', 1), $postData);
        $response->assertSessionHasErrors(['validity_years', 'communication_type']);
    }

    /**
     * Test Adversarial: Direct store safely clamps validity years and normalizes inputs
     */
    public function test_direct_store_clamps_validity_years_and_normalizes_inputs(): void
    {
        $project = EnvironmentProject::first();
        if (!$project) {
            $project = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-5555',
                'category'      => 'B2',
                'project_name'  => 'Direct Store Project',
                'district_id'   => 1,
                'contact_phone' => '9876543210',
                'status'        => 'approved',
            ]);
        }

        $postData = [
            'environment_project_id' => $project->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/CLAMP01',
            'parivesh_app_no'        => 'SIA/TN/MIN/5555/2026',
            'applicant_name'         => 'Direct Clamping Enterprise',
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => -10, // Must clamp to default 5
            'communication_type'     => 'UnknownDecision', // Must fallback to Grant
        ];

        $response = $this->post(route('ec-certificate.store'), $postData);
        $response->assertRedirect(route('ec-certificate.index'));

        $cert = EcCertificate::where('ec_ref_no', 'SEIAA-TN/EC/2026/CLAMP01')->first();
        $this->assertNotNull($cert);
        $this->assertEquals(5, $cert->validity_years);
        $this->assertEquals('Grant', $cert->communication_type);
    }

    /**
     * Test UI: EC Certificate Show renders flash success alerts
     */
    public function test_ec_certificate_show_renders_flash_alerts(): void
    {
        $cert = EcCertificate::first();
        if (!$cert) {
            $project = EnvironmentProject::first();
            $cert = EcCertificate::create([
                'ec_ref_no'              => 'SEIAA-TN/EC/2026/SHOW01',
                'environment_project_id' => $project?->id,
                'applicant_name'         => 'Flash Test Enterprise',
                'issue_date'             => date('Y-m-d'),
                'validity_years'         => 5,
                'communication_type'     => 'Grant',
                'status'                 => 'active',
            ]);
        }

        $response = $this->withSession(['success' => 'Certificate generated and verified.'])
            ->get(route('ec-certificate.show', $cert->id));

        $response->assertStatus(200);
        $response->assertSee('Certificate generated and verified.');
    }

    /**
     * Test Robustness: Step 2 file upload creates directory safely and purges old files
     */
    public function test_step_2_upload_handles_directory_creation_and_replaces_old_files(): void
    {
        $project = EnvironmentProject::first();
        if (!$project) {
            $project = EnvironmentProject::create([
                'project_code'  => 'ENV-B2-2026-UPLOAD1',
                'category'      => 'B2',
                'project_name'  => 'Upload Test Project',
                'district_id'   => 1,
                'contact_phone' => '9876543210',
                'status'        => 'approved',
            ]);
        }

        $session = [
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $project->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/UP01',
                    'parivesh_app_no'        => 'SIA/TN/MIN/9001/2026',
                    'applicant_name'         => 'Upload Test Enterprise',
                    'issue_date'             => date('Y-m-d'),
                    'validity_years'         => 5,
                    'communication_type'     => 'Grant',
                    'completed'              => true,
                ],
            ],
        ];

        // 1st Upload
        $file1 = UploadedFile::fake()->create('first_ec.pdf', 100, 'application/pdf');
        $res1 = $this->withSession($session)->post(route('ec-certificate.saveStep', 2), [
            'certificate_file' => $file1,
        ]);
        $res1->assertRedirect(route('ec-certificate.step', 3));

        $draft1 = session('ec_wizard');
        $this->assertNotNull($draft1['step2']['file_path']);
        $firstPath = public_path($draft1['step2']['file_path']);
        $this->assertFileExists($firstPath);

        // 2nd Upload (should delete first file and replace)
        $file2 = UploadedFile::fake()->create('second_ec.pdf', 150, 'application/pdf');
        $res2 = $this->withSession($draft1)->post(route('ec-certificate.saveStep', 2), [
            'certificate_file' => $file2,
        ]);
        $res2->assertRedirect(route('ec-certificate.step', 3));

        $draft2 = session('ec_wizard');
        $this->assertNotNull($draft2['step2']['file_path']);
        $secondPath = public_path($draft2['step2']['file_path']);
        $this->assertFileExists($secondPath);
        $this->assertFileDoesNotExist($firstPath);

        // Clean up created test file
        if (file_exists($secondPath)) {
            @unlink($secondPath);
        }
    }

    /**
     * Test Robustness: Direct store with file upload sanitizes directory for tricky project codes
     */
    public function test_direct_store_with_uploaded_file_creates_sanitized_directory(): void
    {
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Tricky Customer',
            'mobile_num'    => '9876543210',
        ]);

        $uniq = strtoupper(substr(uniqid(), -6));
        $projectCode = 'ENV/B2#2026!TRICKY_' . $uniq;
        $ecRefNo = 'SEIAA-TN/EC/2026/TRICKY_' . $uniq;

        $project = EnvironmentProject::create([
            'project_code'  => $projectCode,
            'customer_id'   => $customer->id,
            'category'      => 'B2',
            'project_name'  => 'Tricky Code Project ' . $uniq,
            'district_id'   => 1,
            'contact_phone' => '9876543210',
            'status'        => 'approved',
        ]);

        $file = UploadedFile::fake()->create('official_ec.pdf', 200, 'application/pdf');

        $postData = [
            'environment_project_id' => $project->id,
            'ec_ref_no'              => $ecRefNo,
            'parivesh_app_no'        => 'SIA/TN/MIN/9002/2026',
            'applicant_name'         => 'Tricky Code Minerals',
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => 7,
            'communication_type'     => 'Grant',
            'certificate_file'       => $file,
        ];

        $response = $this->post(route('ec-certificate.store'), $postData);
        $response->assertRedirect(route('ec-certificate.index'));

        $cert = EcCertificate::where('ec_ref_no', $ecRefNo)->first();
        $this->assertNotNull($cert);
        $this->assertNotNull($cert->certificate_file);
        $this->assertStringContainsString('ENV_B2_2026_TRICKY_' . $uniq, $cert->certificate_file);

        $savedFile = public_path($cert->certificate_file);
        $this->assertFileExists($savedFile);

        // Clean up test file and created records
        if (file_exists($savedFile)) {
            @unlink($savedFile);
        }
        $cert->delete();
        $project->delete();
    }

    /**
     * Test Category B1 full sequential statutory lifecycle with PPT Department approval gates:
     * Intake (SC1: 5 Folders) -> Submit SC1 to PPT -> PPT Approves Stage 1 -> Project unlocks SC2 (6 Folders)
     * -> Submit SC2 to PPT -> PPT Approves Stage 2 -> Project Completed & Approved for EC Certificate.
     */
    public function test_category_b1_full_sequential_statutory_lifecycle(): void
    {
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'B1 Test Quarry Owner',
            'mobile_num'    => '9888877771',
        ]);

        $district = District::first() ?? District::create([
            'name'       => 'Salem',
            'state_name' => 'Tamil Nadu',
        ]);

        $uniq = uniqid();

        // 1. Intake: Create B1 project with SC1
        $createData = [
            'category'      => 'B1',
            'sub_category'  => 'SC1',
            'customer_id'   => $customer->id,
            'client_name'   => 'B1 Test Quarry Owner',
            'company_name'  => 'B1 Granites Private Limited',
            'project_name'  => 'B1 Statutory Quarry Flow ' . $uniq,
            'district_id'   => $district->id,
            'location'      => 'Survey No 101/A',
            'contact_phone' => '9888877771',
        ];

        $resCreate = $this->post(route('eviron.store'), $createData);
        $resCreate->assertSessionHasNoErrors();

        $project = EnvironmentProject::where('project_name', 'B1 Statutory Quarry Flow ' . $uniq)->first();
        $this->assertNotNull($project);
        $this->assertEquals('B1', $project->category);
        $this->assertEquals('SC1', $project->sub_category);
        $this->assertEquals('sc1_prep', $project->b1_stage);
        $this->assertEquals('draft', $project->status);
        $this->assertCount(5, $project->folder_names);

        // 2. Submit SC1 to PPT Department (Stage 1 Gate)
        $resSubmit1 = $this->post(route('eviron.submitSc1ToPpt', $project->id));
        $resSubmit1->assertRedirect(route('eviron.show', $project->id));
        $resSubmit1->assertSessionHas('success');

        $project->refresh();
        $this->assertEquals('sc1_ppt_review', $project->b1_stage);
        $this->assertEquals('validation', $project->status);
        $this->assertNotNull($project->ppt_stage_1_id);

        $pptStage1 = $project->pptStage1;
        $this->assertNotNull($pptStage1);
        $this->assertEquals('tor_presentation', $pptStage1->presentation_stage);
        $this->assertEquals('agenda_scheduled', $pptStage1->status);

        // 3. PPT Department Approves Stage 1 ToR Presentation
        $resApprove1 = $this->post(route('ppt-department.approveStage', $pptStage1->id));
        $resApprove1->assertRedirect();
        $resApprove1->assertSessionHas('success');

        $pptStage1->refresh();
        $this->assertEquals('approved', $pptStage1->status);

        $project->refresh();
        $this->assertEquals('SC2', $project->sub_category);
        $this->assertEquals('sc2_prep', $project->b1_stage);
        $this->assertEquals('draft', $project->status);
        $this->assertCount(6, $project->folder_names);

        // Verify show page displays SC2 state and submit button
        $showRes1 = $this->get(route('eviron.show', $project->id));
        $showRes1->assertStatus(200);
        $showRes1->assertSee('Submit SC2 to PPT Department');

        // 4. Submit SC2 to PPT Department (Stage 2 Gate)
        $resSubmit2 = $this->post(route('eviron.submitSc2ToPpt', $project->id));
        $resSubmit2->assertRedirect(route('eviron.show', $project->id));
        $resSubmit2->assertSessionHas('success');

        $project->refresh();
        $this->assertEquals('sc2_ppt_review', $project->b1_stage);
        $this->assertEquals('validation', $project->status);
        $this->assertNotNull($project->ppt_stage_2_id);

        $pptStage2 = $project->pptStage2;
        $this->assertNotNull($pptStage2);
        $this->assertEquals('final_ec_presentation', $pptStage2->presentation_stage);

        // 5. PPT Department Approves Stage 2 Final EC Presentation
        $resApprove2 = $this->post(route('ppt-department.approveStage', $pptStage2->id));
        $resApprove2->assertRedirect();
        $resApprove2->assertSessionHas('success');

        $pptStage2->refresh();
        $this->assertEquals('approved', $pptStage2->status);

        $project->refresh();
        $this->assertEquals('completed', $project->b1_stage);
        $this->assertEquals('approved', $project->status);

        // Verify eviron.show displays completion alert and EC certificate link
        $showRes2 = $this->get(route('eviron.show', $project->id));
        $showRes2->assertStatus(200);
        $showRes2->assertSee('Category B1 Lifecycle Complete!');
        $showRes2->assertSee('Issue EC Certificate');

        // Clean up
        $pptStage1->forceDelete();
        $pptStage2->forceDelete();
        $project->forceDelete();
    }

    /**
     * Test Compliance: Codebase contains zero Tamil Unicode characters
     */
    public function test_codebase_contains_zero_tamil_characters(): void
    {
        $scanDirs = [
            app_path(),
            resource_path('views'),
            base_path('routes'),
        ];

        $tamilRegex = '/[\x{0B80}-\x{0BFF}]/u';
        $violations = [];

        foreach ($scanDirs as $scanDir) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($scanDir));
            foreach ($iterator as $file) {
                if ($file->isFile() && in_array($file->getExtension(), ['php', 'js', 'css'])) {
                    $content = file_get_contents($file->getRealPath());
                    if (preg_match($tamilRegex, $content)) {
                        $violations[] = $file->getRealPath();
                    }
                }
            }
        }

        $this->assertEmpty($violations, 'Tamil Unicode characters detected in files: ' . implode(', ', $violations));
    }
}
