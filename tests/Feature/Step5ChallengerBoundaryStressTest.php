<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DgpsDocument;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\DroneDocument;
use App\Models\DroneSurvey;
use App\Models\EcCertificate;
use App\Models\EcCompliance;
use App\Models\EcComplianceDocument;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\PptApplication;
use App\Models\PptDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Step5ChallengerBoundaryStressTest
 * 
 * Adversarial challenger test suite probing edge cases, regressions,
 * re-upload collisions, non-existent removal exceptions, and wizard draft migrations.
 */
class Step5ChallengerBoundaryStressTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
    protected Mineral $mineral;
    protected Folder $folder;
    protected array $createdFiles = [];

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

        $permissions = [
            'drone.view', 'drone.create', 'drone.edit',
            'dgps.view', 'dgps.create', 'dgps.edit',
            'environment.view', 'environment.create', 'environment.edit',
            'environment.b2.view', 'environment.b2.create', 'environment.b2.edit',
            'environment.b2.upload', 'environment.b2.review',
            'ec_compliance.view', 'ec_compliance.create', 'ec_compliance.edit',
            'ppt.view', 'ppt.create', 'ppt.edit', 'ppt.manage',
            'mining.view', 'mining.create', 'mining.edit',
            'ec_certificate.view', 'ec_certificate.create', 'ec_certificate.edit',
            'ec.certificate.view', 'ec.certificate.create', 'ec.certificate.edit',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }
        $this->user->givePermissionTo($permissions);
        $this->actingAs($this->user);

        $this->district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $this->mineral = Mineral::first() ?? Mineral::create(['name' => 'Granite', 'status' => 1]);
        $this->folder = Folder::first() ?? Folder::create(['folder_name' => 'General', 'status' => 1]);

        $rand = rand(10000, 99999);
        $this->customer = Customer::create([
            'customer_name'            => 'Challenger Client ' . $rand,
            'company_name'             => 'Boundary Stones Ltd ' . $rand,
            'mimas_no'                 => 'MMS-CHAL-' . $rand,
            'mobile_num'               => '98765' . rand(10000, 99999),
            'email'                    => 'challenger' . $rand . '@test.com',
            'district_id'              => $this->district->id,
            'address'                  => 'Test Quarry, Salem',
            'status'                   => 1,
            'created_by'               => $this->user->id,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            if ($path && file_exists(public_path($path))) {
                @unlink(public_path($path));
            }
            if ($path && file_exists(storage_path('app/public/' . $path))) {
                @unlink(storage_path('app/public/' . $path));
            }
        }
        parent::tearDown();
    }

    // =========================================================================
    // PROBE 1: NON-EXISTENT DOCUMENT REMOVAL (GRACEFUL JSON, ZERO 500 CRASHES)
    // =========================================================================

    public function test_drone_survey_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        // Case A: Non-existent doc_id
        $respA = $this->postJson(route('drone_survey.remove_document'), [
            'doc_id'   => 99999999,
            'doc_item' => 'non_existent_item',
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        // Case B: Empty payload
        $respB = $this->postJson(route('drone_survey.remove_document'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_dgps_survey_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('dgps_survey.remove_document'), [
            'doc_id'   => 99999999,
            'doc_item' => 'phantom_slot',
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('dgps_survey.remove_document'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_enviro_b1_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('enviro_b1.remove_document'), [
            'doc_id'     => 99999999,
            'project_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('enviro_b1.remove_document'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_enviro_b2_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('environment-b2.documents.remove_direct'), [
            'doc_id'     => 99999999,
            'project_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('environment-b2.documents.remove_direct'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_ec_compliance_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('ec-compliance.remove'), [
            'doc_id'        => 99999999,
            'compliance_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('ec-compliance.remove'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_ppt_department_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('ppt-department.remove'), [
            'doc_id'         => 99999999,
            'application_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('ppt-department.remove'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_mining_portal_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('mining-portal.remove-document'), [
            'doc_id'                => 99999999,
            'mining_application_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('mining-portal.remove-document'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    public function test_ec_certificate_removal_of_nonexistent_document_returns_graceful_json(): void
    {
        $respA = $this->postJson(route('ec-certificate.remove'), [
            'certificate_id' => 99999999,
        ]);
        $respA->assertStatus(200);
        $respA->assertJson(['status' => 1, 'success' => true]);

        $respB = $this->postJson(route('ec-certificate.remove'), []);
        $respB->assertStatus(200);
        $respB->assertJson(['status' => 1, 'success' => true]);
    }

    // =========================================================================
    // PROBE 2: RE-UPLOAD WITH IDENTICAL FILE NAME DOES NOT DELETE NEW FILE
    // =========================================================================

    public function test_drone_survey_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'Drone Survey Initial Content A - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('identical_name_survey.pdf', $content1);

        $resp1 = $this->postJson(route('drone-survey.upload'), [
            'file'          => $file1,
            'doc_item'      => 'doc_collision_test',
            'document_name' => 'Orthomosaic Map',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;
        $this->assertFileExists(public_path($path1));
        $this->assertEquals($content1, file_get_contents(public_path($path1)));

        // Re-upload with the EXACT same filename after 1 second (diverging time() second)
        sleep(1);
        $content2 = 'Drone Survey Replaced Content B - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('identical_name_survey.pdf', $content2);

        $resp2 = $this->postJson(route('drone-survey.upload'), [
            'file'          => $file2,
            'doc_item'      => 'doc_collision_test',
            'document_name' => 'Orthomosaic Map',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        // Verify the newly uploaded file exists and has content2
        $this->assertFileExists(public_path($path2));
        $this->assertEquals($content2, file_get_contents(public_path($path2)));
    }

    public function test_dgps_survey_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'DGPS Boundary Content A - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('dgps_boundary.csv', $content1);

        $resp1 = $this->postJson(route('dgps-survey.upload'), [
            'file'          => $file1,
            'doc_item'      => 'dgps_slot_collision',
            'document_name' => 'Raw GNSS Coordinates',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;
        $this->assertFileExists(public_path($path1));

        // Re-upload with the EXACT same filename after 1s
        sleep(1);
        $content2 = 'DGPS Boundary Content B - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('dgps_boundary.csv', $content2);

        $resp2 = $this->postJson(route('dgps-survey.upload'), [
            'file'          => $file2,
            'doc_item'      => 'dgps_slot_collision',
            'document_name' => 'Raw GNSS Coordinates',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(public_path($path2));
        $this->assertEquals($content2, file_get_contents(public_path($path2)));
    }

    public function test_enviro_b1_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'B1 Initial Report - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('eia_report.pdf', $content1);

        $resp1 = $this->postJson(route('enviro_b1.upload_document'), [
            'file'          => $file1,
            'doc_item'      => 'b1_slot_test',
            'document_name' => 'Draft EIA Report',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;

        $content2 = 'B1 Updated Report - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('eia_report.pdf', $content2);

        $resp2 = $this->postJson(route('enviro_b1.upload_document'), [
            'file'          => $file2,
            'doc_item'      => 'b1_slot_test',
            'document_name' => 'Draft EIA Report',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(storage_path('app/public/' . $path2));
        $this->assertEquals($content2, file_get_contents(storage_path('app/public/' . $path2)));
    }

    public function test_ec_compliance_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'Water Quality Report Initial - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('water_analysis.pdf', $content1);

        $resp1 = $this->postJson(route('ec-compliance.upload'), [
            'file'          => $file1,
            'doc_item'      => 'pillar_3_item_1',
            'document_name' => 'Water Quality Test Results',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;

        $content2 = 'Water Quality Report Updated - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('water_analysis.pdf', $content2);

        $resp2 = $this->postJson(route('ec-compliance.upload'), [
            'file'          => $file2,
            'doc_item'      => 'pillar_3_item_1',
            'document_name' => 'Water Quality Test Results',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(storage_path('app/public/' . $path2));
        $this->assertEquals($content2, file_get_contents(storage_path('app/public/' . $path2)));
    }

    public function test_ppt_department_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'PPT Slide Deck Initial - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('seac_defense.pptx', $content1);

        $resp1 = $this->postJson(route('ppt-department.upload'), [
            'file'          => $file1,
            'doc_item'      => 'ppt_slot_presentation',
            'document_name' => 'SEAC Presentation PPT',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;

        $content2 = 'PPT Slide Deck Revised - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('seac_defense.pptx', $content2);

        $resp2 = $this->postJson(route('ppt-department.upload'), [
            'file'          => $file2,
            'doc_item'      => 'ppt_slot_presentation',
            'document_name' => 'SEAC Presentation PPT',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(storage_path('app/public/' . $path2));
        $this->assertEquals($content2, file_get_contents(storage_path('app/public/' . $path2)));
    }

    public function test_mining_portal_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'Mining Plan Initial - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('mining_plan_doc.pdf', $content1);

        $resp1 = $this->postJson(route('mining.document.upload'), [
            'file'          => $file1,
            'doc_item'      => 'field_991',
            'field_id'      => 991,
            'document_name' => 'Approved Mining Plan',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;

        sleep(1);
        $content2 = 'Mining Plan Revised - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('mining_plan_doc.pdf', $content2);

        $resp2 = $this->postJson(route('mining.document.upload'), [
            'file'          => $file2,
            'doc_item'      => 'field_991',
            'field_id'      => 991,
            'document_name' => 'Approved Mining Plan',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(public_path($path2));
        $this->assertEquals($content2, file_get_contents(public_path($path2)));
    }

    public function test_ec_certificate_reupload_with_identical_name_preserves_new_file(): void
    {
        $content1 = 'EC Certificate Initial - ' . uniqid();
        $file1 = UploadedFile::fake()->createWithContent('ec_grant_order.pdf', $content1);

        $resp1 = $this->postJson(route('ec-certificate.upload'), [
            'file'     => $file1,
            'doc_item' => 'certificate',
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->createdFiles[] = $path1;

        sleep(1);
        $content2 = 'EC Certificate Signed - ' . uniqid();
        $file2 = UploadedFile::fake()->createWithContent('ec_grant_order.pdf', $content2);

        $resp2 = $this->postJson(route('ec-certificate.upload'), [
            'file'     => $file2,
            'doc_item' => 'certificate',
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');
        $this->createdFiles[] = $path2;

        $this->assertFileExists(public_path($path2));
        $this->assertEquals($content2, file_get_contents(public_path($path2)));
    }

    // =========================================================================
    // PROBE 3: WIZARD SESSION DRAFT PERSISTENCE & DATABASE MIGRATION UPON SUBMIT
    // =========================================================================

    public function test_drone_survey_wizard_session_draft_migrates_to_database_on_final_store(): void
    {
        // 1. Upload file into wizard session draft
        $file = UploadedFile::fake()->create('drone_dsm_surface.tif', 256, 'image/tiff');
        $uploadResp = $this->postJson(route('drone-survey.upload'), [
            'file'          => $file,
            'doc_item'      => 'doc_dsm_elevation',
            'document_name' => 'DSM Surface Model',
        ]);
        $uploadResp->assertStatus(200);
        $filePath = $uploadResp->json('file_path');
        $this->createdFiles[] = $filePath;

        // 2. Verify draft session holds the document
        $draftDocs = session('drone_wizard.uploaded_docs');
        $this->assertIsArray($draftDocs);
        $this->assertArrayHasKey('doc_dsm_elevation', $draftDocs);
        $this->assertEquals($filePath, $draftDocs['doc_dsm_elevation']['file_path']);

        // 3. Finalize wizard by calling store()
        $storeResp = $this->post(route('drone-survey.store'), [
            'customer_id'   => $this->customer->id,
            'location'      => 'Salem Quarry Sector 4',
            'flight_date'   => date('Y-m-d'),
            'product_value' => 60000,
            'paid_amount'   => 40000,
        ]);
        $storeResp->assertRedirect();

        // 4. Verify DroneSurvey record and migrated DroneDocument record exist in DB
        $survey = DroneSurvey::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($survey);

        $doc = DroneDocument::where('drone_survey_id', $survey->id)->where('file_path', $filePath)->first();
        $this->assertNotNull($doc, 'Draft document failed to migrate into drone_documents upon final wizard submit.');
        $this->assertEquals('DSM Surface Model', $doc->document_name);
        $this->assertEquals('uploaded', $doc->status);

        // 5. Verify draft session is cleared
        $this->assertNull(session('drone_wizard'));
    }

    public function test_dgps_survey_wizard_session_draft_migrates_to_database_on_final_store(): void
    {
        // 1. Upload file into wizard session draft
        $file = UploadedFile::fake()->create('dgps_static_obs.rnx', 128);
        $uploadResp = $this->postJson(route('dgps-survey.upload'), [
            'file'          => $file,
            'doc_item'      => 'doc_rinx_data',
            'document_name' => 'RINEX Observation File',
        ]);
        $uploadResp->assertStatus(200);
        $filePath = $uploadResp->json('file_path');
        $this->createdFiles[] = $filePath;

        // 2. Verify session draft holds the item
        $draftDocs = session('dgps_wizard.uploaded_docs');
        $this->assertIsArray($draftDocs);
        $this->assertArrayHasKey('doc_rinx_data', $draftDocs);

        // 3. Finalize wizard
        $storeResp = $this->post(route('dgps-survey.store'), [
            'customer_id'   => $this->customer->id,
            'location'      => 'Salem Pillar #1',
            'survey_date'   => date('Y-m-d'),
            'product_value' => 45000,
            'paid_amount'   => 45000,
        ]);
        $storeResp->assertRedirect();

        // 4. Verify DgpsSurvey and DgpsDocument exist in DB
        $survey = DgpsSurvey::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($survey);

        $doc = DgpsDocument::where('dgps_survey_id', $survey->id)->where('file_path', $filePath)->first();
        $this->assertNotNull($doc, 'Draft document failed to migrate into dgps_documents upon final wizard submit.');
        $this->assertEquals('RINEX Observation File', $doc->document_name);

        // 5. Verify draft session is cleared
        $this->assertNull(session('dgps_wizard'));
    }

    public function test_ec_compliance_wizard_session_draft_migrates_to_database_on_final_store(): void
    {
        // 1. Upload file into wizard session draft
        $file = UploadedFile::fake()->create('ambient_air_monitoring.pdf', 300, 'application/pdf');
        $uploadResp = $this->postJson(route('ec-compliance.upload'), [
            'file'            => $file,
            'doc_item'        => 'doc_air_quality',
            'document_name'   => 'Air Quality NABL Certificate',
            'folder_category' => 'site_analysis',
        ]);
        $uploadResp->assertStatus(200);
        $filePath = $uploadResp->json('file_path');
        $this->createdFiles[] = $filePath;

        // 2. Verify session draft
        $draftDocs = session('ec_compliance_wizard.uploaded_docs');
        $this->assertIsArray($draftDocs);
        $this->assertArrayHasKey('doc_air_quality', $draftDocs);

        // 3. Finalize wizard
        $storeResp = $this->post(route('ec-compliance.store'), [
            'customer_id'                 => $this->customer->id,
            'project_name'                => 'Quarry EC Compliance 2026',
            'district_id'                 => $this->district->id,
            'compliance_period'           => 'April 2026 - September 2026',
            'compliance_year'             => '2026',
            'product_value'               => 75000,
            'paid_amount'                 => 50000,
        ]);
        $storeResp->assertRedirect();

        // 4. Verify EcCompliance and EcComplianceDocument exist in DB
        $compliance = EcCompliance::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($compliance);

        $doc = EcComplianceDocument::where('ec_compliance_id', $compliance->id)
            ->where('file_path', $filePath)
            ->first();
        $this->assertNotNull($doc, 'Draft document failed to migrate into ec_compliance_documents upon final wizard submit.');
        $this->assertEquals('Air Quality NABL Certificate', $doc->document_name);
        $this->assertEquals('site_analysis', $doc->folder_category);

        // 5. Verify draft session cleared
        $this->assertNull(session('ec_compliance_wizard'));
    }

    public function test_ppt_department_wizard_session_draft_migrates_to_database_on_final_store(): void
    {
        // 1. Upload file into wizard session draft
        $file = UploadedFile::fake()->create('baseline_env_defense.pdf', 400, 'application/pdf');
        $uploadResp = $this->postJson(route('ppt-department.upload'), [
            'file'          => $file,
            'doc_item'      => 'folder_1',
            'folder_id'     => 1,
            'document_name' => 'Baseline Environmental Presentation',
        ]);
        $uploadResp->assertStatus(200);
        $filePath = $uploadResp->json('file_path');
        $this->createdFiles[] = $filePath;

        // 2. Verify draft session
        $draftDocs = session('ppt_wizard.uploaded_docs');
        $this->assertIsArray($draftDocs);
        $this->assertArrayHasKey('folder_1', $draftDocs);

        // 3. Finalize wizard
        $storeResp = $this->post(route('ppt-department.store'), [
            'customer_id'   => $this->customer->id,
            'project_name'  => 'Granite Quarry SEIAA Defense',
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'product_value' => 50000,
            'paid_amount'   => 50000,
        ]);
        $storeResp->assertRedirect();

        // 4. Verify PptApplication and PptDocument exist in DB
        $ppt = PptApplication::where('customer_id', $this->customer->id)->latest()->first();
        $this->assertNotNull($ppt);

        $doc = PptDocument::where('ppt_application_id', $ppt->id)->where('file_path', $filePath)->first();
        $this->assertNotNull($doc, 'Draft document failed to migrate into ppt_documents upon final wizard submit.');
        $this->assertEquals('Baseline Environmental Presentation', $doc->document_name);

        // 5. Verify draft session cleared
        $this->assertNull(session('ppt_wizard'));
    }

    public function test_ec_certificate_wizard_session_draft_migrates_to_database_on_final_store(): void
    {
        // Create prerequisite EnvironmentProject
        $project = EnvironmentProject::create([
            'project_code' => 'ENV-CHAL-' . rand(1000, 9999),
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'project_name' => 'Granite Quarry EC Project',
            'status'       => 'approved',
        ]);

        // 1. Upload certificate file into wizard session draft
        $file = UploadedFile::fake()->create('signed_ec_order.pdf', 500, 'application/pdf');
        $uploadResp = $this->postJson(route('ec-certificate.upload'), [
            'file'                   => $file,
            'doc_item'               => 'certificate',
            'environment_project_id' => $project->id,
        ]);
        $uploadResp->assertStatus(200);
        $filePath = $uploadResp->json('file_path');
        $this->createdFiles[] = $filePath;

        // 2. Verify draft session
        $this->assertNotEmpty(session('ec_wizard.step2.file_path'));
        $this->assertEquals($filePath, session('ec_wizard.step2.file_path'));

        // 3. Finalize wizard
        $ecRefNo = 'EC-REF-CHAL-' . rand(1000, 9999);
        $storeResp = $this->post(route('ec-certificate.store'), [
            'environment_project_id' => $project->id,
            'ec_ref_no'              => $ecRefNo,
            'applicant_name'         => $this->customer->customer_name,
            'issue_date'             => date('Y-m-d'),
            'validity_years'         => 5,
            'communication_type'     => 'Grant',
        ]);
        $storeResp->assertRedirect();

        // 4. Verify EcCertificate record exists and holds the migrated certificate_file
        $cert = EcCertificate::where('ec_ref_no', $ecRefNo)->first();
        $this->assertNotNull($cert);
        $this->assertEquals($filePath, $cert->certificate_file, 'Draft certificate file failed to migrate to ec_certificates model.');

        // 5. Verify draft session cleared
        $this->assertNull(session('ec_wizard'));
    }

    // =========================================================================
    // PROBE 4: BLADE VIEWS COMPILE CLEANLY WITH CSRF AND ACTION TRIO ATTRIBUTES
    // =========================================================================

    public function test_all_inscope_blade_views_render_with_csrf_and_action_controls(): void
    {
        // 1. Drone Survey Wizard
        $view1 = $this->get(route('drone-survey.step', 5));
        $view1->assertStatus(200);
        $view1->assertSee('name="csrf-token"', false);
        $view1->assertSee('GtmsOpenWith', false);

        // 2. DGPS Survey Wizard Step 3 (Document intake step)
        $view2 = $this->get(route('dgps-survey.step', 3));
        $view2->assertStatus(200);
        $view2->assertSee('name="csrf-token"', false);
        $view2->assertSee('btn-view-dgps-item', false);
        $view2->assertSee('btn-upload-dgps-item', false);
        $view2->assertSee('btn-remove-dgps-item', false);

        // 3. Enviro B2 Wizard Step 4
        $view3 = $this->get(route('environment-b2.step', 4));
        $view3->assertStatus(200);
        $view3->assertSee('name="csrf-token"', false);
        $view3->assertSee('checklist-row', false);
        $view3->assertSee('btn-upload-item', false);
        $view3->assertSee('gtms-open-with.js', false);

        // 4. EC Compliance Wizard Step 3
        $view4 = $this->get(route('ec-compliance.step', 3));
        $view4->assertStatus(200);
        $view4->assertSee('name="csrf-token"', false);
        $view4->assertSee('checklist-row', false);
        $view4->assertSee('gtms-open-with.js', false);

        // 5. PPT Department Wizard
        $view5 = $this->get(route('ppt-department.step', 5));
        $view5->assertStatus(200);
        $view5->assertSee('name="csrf-token"', false);
        $view5->assertSee('GtmsOpenWith', false);

        // 6. Mining Portal Document View
        $miningApp = MiningApplication::first();
        if ($miningApp) {
            $view6 = $this->get(route('mining.documents', ['id' => $miningApp->id]));
            $view6->assertStatus(200);
            $view6->assertSee('name="csrf-token"', false);
            $view6->assertSee('GtmsOpenWith', false);
        }

        // 7. EC Certificate Wizard (Unlocked at Step 2)
        $enviroProject = EnvironmentProject::create([
            'project_code' => 'EC-PRJ-' . rand(1000, 9999),
            'project_name' => 'Salem Quarry EC Dossier',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'approved',
        ]);

        $view7 = $this->withSession([
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $enviroProject->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/001',
                    'applicant_name'         => $this->customer->customer_name,
                    'completed'              => true,
                ]
            ]
        ])->get(route('ec-certificate.step', 2));

        $view7->assertStatus(200);
        $view7->assertSee('name="csrf-token"', false);
        $view7->assertSee('GtmsOpenWith', false);
    }
}
