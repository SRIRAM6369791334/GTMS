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
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChallengerStep5AdversarialVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
    protected Mineral $mineral;
    protected Folder $folder;
    protected array $trackedFiles = [];

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
        $this->mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $this->folder = Folder::first() ?? Folder::create(['folder_name' => 'General', 'status' => 1]);

        $rand = rand(1000, 9999);
        $this->customer = Customer::create([
            'customer_name'            => 'Adversarial Tester ' . $rand,
            'company_name'             => 'Kongu Challengers Ltd ' . $rand,
            'mobile_num'               => '98401' . $rand,
            'secondary_mobile_num'     => '98402' . $rand,
            'secondary_contact_person' => 'Adv. Auditor',
            'email'                    => 'challenger' . $rand . '@gtmsadversarial.in',
            'mimas_no'                 => 'TN-MMS-ADV-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->trackedFiles as $file) {
            if ($file && file_exists($file)) {
                @unlink($file);
            }
        }
        parent::tearDown();
    }

    protected function resolveDiskPath(string $relPath): string
    {
        if (file_exists(public_path($relPath))) {
            return public_path($relPath);
        }
        if (file_exists(storage_path('app/public/' . $relPath))) {
            return storage_path('app/public/' . $relPath);
        }
        if (file_exists(public_path('storage/' . $relPath))) {
            return public_path('storage/' . $relPath);
        }
        // default fallback
        return public_path($relPath);
    }

    /**
     * 1. DRONE SURVEY EMPIRICAL LIFECYCLE: Create -> Re-upload Delete -> Remove Delete -> Extension Filter -> Zip Check
     */
    public function test_drone_survey_full_empirical_lifecycle_and_edge_cases(): void
    {
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-ADV-' . rand(100, 999),
            'customer_id'          => $this->customer->id,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 10000,
        ]);

        // A. File creation on disk
        $file1 = UploadedFile::fake()->create('drone_pointcloud_v1.las', 500, 'application/octet-stream');
        $resp1 = $this->post(route('drone-survey.upload'), [
            'file'            => $file1,
            'drone_survey_id' => $survey->id,
            'document_name'   => '3D Point Cloud LAS',
            'folder_id'       => $this->folder->id,
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "Drone file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        $docId = $resp1->json('doc_id');
        $this->assertNotNull($docId);

        // B. Re-upload replacement deletes old file on disk (@unlink)
        $file2 = UploadedFile::fake()->create('drone_pointcloud_v2.zip', 800, 'application/zip');
        $resp2 = $this->post(route('drone-survey.upload'), [
            'file'            => $file2,
            'drone_survey_id' => $survey->id,
            'doc_id'          => $docId,
            'document_name'   => '3D Point Cloud LAS',
            'folder_id'       => $this->folder->id,
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "Drone replacement file must physically exist on disk");
        $this->trackedFiles[] = $diskPath2;

        // Old file MUST be deleted from disk
        $this->assertFileDoesNotExist($diskPath1, "Previous Drone file 1 must be deleted from disk on replacement");

        // C. Remove unlinks file from disk and deletes DB record
        $resp3 = $this->post(route('drone-survey.remove'), [
            'drone_survey_id' => $survey->id,
            'doc_id'          => $docId,
            'document_name'   => '3D Point Cloud LAS',
        ]);
        $resp3->assertStatus(200);
        $resp3->assertJson(['status' => 1]);
        $this->assertFileDoesNotExist($diskPath2, "Replacement file must be unlinked from disk upon removal");
        $this->assertDatabaseMissing('drone_documents', ['id' => $docId]);

        // D. Extension whitelisting rejection
        $malicious = UploadedFile::fake()->create('exploit.exe', 100, 'application/x-msdownload');
        $respBad = $this->postJson(route('drone-survey.upload'), [
            'file'            => $malicious,
            'drone_survey_id' => $survey->id,
            'document_name'   => 'Bad File',
        ]);
        $respBad->assertStatus(422);
    }

    /**
     * 2. DGPS SURVEY EMPIRICAL LIFECYCLE: Create -> Re-upload Delete -> Remove Delete -> Extension Filter -> Zip Check
     */
    public function test_dgps_survey_full_empirical_lifecycle_and_edge_cases(): void
    {
        $survey = DgpsSurvey::create([
            'survey_no'     => 'DGPS-ADV-' . rand(100, 999),
            'customer_id'   => $this->customer->id,
            'survey_status' => 'completed',
            'lease_area_ha' => 5.25,
        ]);

        // A. File creation on disk
        $file1 = UploadedFile::fake()->create('raw_rinex_data_v1.obs', 300, 'text/plain');
        $resp1 = $this->post(route('dgps-survey.upload'), [
            'file'          => $file1,
            'survey_id'     => $survey->id,
            'document_name' => '1. Raw RINEX Base Observation Data',
            'folder_id'     => $this->folder->id,
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "DGPS file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        $docId = $resp1->json('doc_id');
        $this->assertNotNull($docId);

        // B. Re-upload replacement deletes old file on disk
        $file2 = UploadedFile::fake()->create('raw_rinex_data_v2.rar', 600, 'application/x-rar-compressed');
        $resp2 = $this->post(route('dgps-survey.upload'), [
            'file'          => $file2,
            'survey_id'     => $survey->id,
            'doc_id'        => $docId,
            'document_name' => '1. Raw RINEX Base Observation Data',
            'folder_id'     => $this->folder->id,
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "DGPS replacement RAR must physically exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous DGPS file 1 must be deleted from disk on replacement");

        // C. Remove unlinks file from disk and deletes DB record
        $resp3 = $this->post(route('dgps-survey.remove'), [
            'survey_id'     => $survey->id,
            'doc_id'        => $docId,
            'document_name' => '1. Raw RINEX Base Observation Data',
        ]);
        $resp3->assertStatus(200);
        $resp3->assertJson(['status' => 1]);
        $this->assertFileDoesNotExist($diskPath2, "DGPS file 2 must be unlinked from disk upon removal");
        $this->assertDatabaseMissing('dgps_documents', ['id' => $docId]);

        // D. Extension whitelisting rejection
        $malicious = UploadedFile::fake()->create('rootkit.bat', 50, 'application/x-bat');
        $respBad = $this->postJson(route('dgps-survey.upload'), [
            'file'          => $malicious,
            'survey_id'     => $survey->id,
            'document_name' => 'Bad Bat',
        ]);
        $respBad->assertStatus(422);
    }

    /**
     * 3. ENVIRONMENT CLEARANCE B1 EMPIRICAL LIFECYCLE
     */
    public function test_enviro_b1_full_empirical_lifecycle_and_edge_cases(): void
    {
        $project = EnvironmentProject::create([
            'project_name' => 'B1 Adversarial Quarry ' . rand(100, 999),
            'project_code' => 'ENV-B1-ADV-' . rand(1000, 9999),
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B1',
            'sub_category' => 'TOR',
            'status'       => 'draft',
            'created_by'   => $this->user->id,
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('eia_baseline_v1.pdf', 400, 'application/pdf');
        $resp1 = $this->post(route('enviro_b1.upload_document'), [
            'file'                   => $file1,
            'environment_project_id' => $project->id,
            'document_name'          => 'EIA Baseline Assessment',
            'folder_id'              => 1,
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "Enviro B1 file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        $docId = $resp1->json('doc_id');
        $this->assertNotNull($docId);

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('eia_baseline_v2.7z', 900, 'application/x-7z-compressed');
        $resp2 = $this->post(route('enviro_b1.upload_document'), [
            'file'                   => $file2,
            'environment_project_id' => $project->id,
            'doc_id'                 => $docId,
            'document_name'          => 'EIA Baseline Assessment',
            'folder_id'              => 1,
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "Enviro B1 replacement file must physically exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous Enviro B1 file 1 must be unlinked on replacement");

        // C. Remove unlinks file
        $resp3 = $this->post(route('enviro_b1.remove_document'), [
            'environment_project_id' => $project->id,
            'doc_id'                 => $docId,
            'document_name'          => 'EIA Baseline Assessment',
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "Enviro B1 file 2 must be unlinked from disk upon removal");

        // D. Extension filter
        $bad = UploadedFile::fake()->create('payload.sh', 40, 'application/x-sh');
        $respBad = $this->postJson(route('enviro_b1.upload_document'), [
            'file'          => $bad,
            'document_name' => 'Bad Script',
        ]);
        $respBad->assertStatus(422);
    }

    /**
     * 4. ENVIRONMENT CLEARANCE B2 EMPIRICAL LIFECYCLE
     */
    public function test_enviro_b2_full_empirical_lifecycle_and_edge_cases(): void
    {
        $project = EnvironmentProject::create([
            'project_name' => 'B2 Adversarial Quarry ' . rand(100, 999),
            'project_code' => 'ENV-B2-ADV-' . rand(1000, 9999),
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'draft',
            'created_by'   => $this->user->id,
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('cluster_500m_v1.pdf', 350, 'application/pdf');
        $resp1 = $this->post(route('enviro_b2.upload_document'), [
            'file'                   => $file1,
            'environment_project_id' => $project->id,
            'doc_item'               => 'cluster_cert',
            'document_name'          => '500m Cluster Certificate',
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "Enviro B2 file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('cluster_500m_v2.tar.gz', 850, 'application/gzip');
        $resp2 = $this->post(route('enviro_b2.upload_document'), [
            'file'                   => $file2,
            'environment_project_id' => $project->id,
            'doc_item'               => 'cluster_cert',
            'document_name'          => '500m Cluster Certificate',
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "Enviro B2 replacement archive must exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous Enviro B2 file 1 must be unlinked on replacement");

        // C. Remove unlinks file
        $resp3 = $this->post(route('enviro_b2.remove_document'), [
            'environment_project_id' => $project->id,
            'doc_item'               => 'cluster_cert',
            'document_name'          => '500m Cluster Certificate',
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "Enviro B2 replacement file must be unlinked on removal");

        // D. Extension filter
        $bad = UploadedFile::fake()->create('trojan.cmd', 50, 'text/plain');
        $respBad = $this->postJson(route('enviro_b2.upload_document'), [
            'file'          => $bad,
            'document_name' => 'Bad Cmd',
        ]);
        $respBad->assertStatus(422);
    }

    /**
     * 5. EC COMPLIANCE MONITORING EMPIRICAL LIFECYCLE
     */
    public function test_ec_compliance_full_empirical_lifecycle_and_edge_cases(): void
    {
        $comp = EcCompliance::create([
            'project_name'      => 'Adversarial Compliance ' . rand(100, 999),
            'compliance_no'     => 'HYC-ADV-' . rand(100, 999),
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'compliance_period' => 'April 2026 - September 2026',
            'status'            => 'draft',
            'product_value'     => 50000,
            'paid_amount'       => 50000,
            'created_by'        => $this->user->id,
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('groundwater_monitoring_v1.pdf', 380, 'application/pdf');
        $resp1 = $this->post(route('ec-compliance.upload'), [
            'file'            => $file1,
            'compliance_id'   => $comp->id,
            'document_name'   => 'Groundwater Depth & Quality Analysis',
            'folder_category' => 'site_analysis',
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "EC Compliance file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        $docId = $resp1->json('doc_id');
        $this->assertNotNull($docId);

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('groundwater_monitoring_v2.zip', 780, 'application/zip');
        $resp2 = $this->post(route('ec-compliance.upload'), [
            'file'            => $file2,
            'compliance_id'   => $comp->id,
            'doc_id'          => $docId,
            'document_name'   => 'Groundwater Depth & Quality Analysis',
            'folder_category' => 'site_analysis',
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "EC Compliance replacement archive must exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous EC Compliance file 1 must be unlinked on replacement");

        // C. Remove unlinks file from disk and deletes DB record
        $resp3 = $this->post(route('ec-compliance.remove'), [
            'compliance_id' => $comp->id,
            'doc_id'        => $docId,
            'document_name' => 'Groundwater Depth & Quality Analysis',
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "EC Compliance file 2 must be unlinked upon removal");
        $this->assertSoftDeleted('ec_compliance_documents', ['id' => $docId]);

        // D. Extension filter
        $bad = UploadedFile::fake()->create('virus.vbs', 40, 'text/plain');
        $respBad = $this->postJson(route('ec-compliance.upload'), [
            'file'          => $bad,
            'document_name' => 'Bad VBS',
        ]);
        $respBad->assertStatus(422);
    }

    /**
     * 6. PPT DEPARTMENT EMPIRICAL LIFECYCLE
     */
    public function test_ppt_department_full_empirical_lifecycle_and_edge_cases(): void
    {
        $ppt = PptApplication::create([
            'application_no'     => 'PPT-ADV-' . rand(1000, 9999),
            'customer_id'        => $this->customer->id,
            'district_id'        => $this->district->id,
            'mineral_id'         => $this->mineral->id,
            'project_name'       => 'Adversarial PPT SEAC Presentation',
            'product_value'      => 45000,
            'status'             => 'draft',
            'presentation_stage' => 'b2_presentation',
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('seac_slides_v1.pptx', 650, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
        $resp1 = $this->post(route('ppt-department.upload'), [
            'file'      => $file1,
            'ppt_id'    => $ppt->id,
            'field_id'  => 3,
            'folder_id' => 2,
            'doc_name'  => 'SEAC Presentation Slides',
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "PPT file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        $docRecord = PptDocument::where('ppt_application_id', $ppt->id)->where('folder_id', 2)->first();
        $this->assertNotNull($docRecord);

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('seac_slides_v2.zip', 950, 'application/zip');
        $resp2 = $this->post(route('ppt-department.upload'), [
            'file'      => $file2,
            'ppt_id'    => $ppt->id,
            'field_id'  => 3,
            'folder_id' => 2,
            'doc_name'  => 'SEAC Presentation Slides',
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "PPT replacement archive must exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous PPT file 1 must be unlinked on replacement");

        // C. Remove unlinks file from disk and deletes DB record
        $resp3 = $this->post(route('ppt-department.remove'), [
            'ppt_id'    => $ppt->id,
            'doc_id'    => $docRecord->id,
            'folder_id' => 2,
            'doc_name'  => 'SEAC Presentation Slides',
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "PPT file 2 must be unlinked upon removal");

        // D. Extension filter
        $bad = UploadedFile::fake()->create('script.bat', 30, 'application/x-bat');
        $respBad = $this->post(route('ppt-department.upload'), [
            'file'     => $bad,
            'field_id' => 1,
            'doc_name' => 'Bad Bat',
        ]);
        $this->assertTrue(
            in_array($respBad->status(), [422, 302], true) || ($respBad->status() === 200 && ($respBad->json('status') === 0 || $respBad->json('status') === false))
        );
    }

    /**
     * 7. MINING PORTAL EMPIRICAL LIFECYCLE
     */
    public function test_mining_portal_full_empirical_lifecycle_and_edge_cases(): void
    {
        $app = MiningApplication::create([
            'application_no' => 'MP-ADV-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'current_step'   => 6,
            'status'         => 'draft',
        ]);

        $doc = MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'Mine Closure Plan & Progressive Rehabilitation',
            'status'                => 'pending',
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('closure_plan_v1.pdf', 450, 'application/pdf');
        $resp1 = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file1,
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "Mining file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('closure_plan_v2.zip', 950, 'application/zip');
        $resp2 = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file2,
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "Mining replacement archive must exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous Mining file 1 must be unlinked on replacement");

        // C. Remove unlinks file from disk and resets DB record
        $resp3 = $this->post(route('mining.document.remove'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "Mining file 2 must be unlinked upon removal");

        $doc->refresh();
        $this->assertEquals('pending', $doc->status);
        $this->assertNull($doc->file_path);

        // D. Extension filter
        $bad = UploadedFile::fake()->create('exploit.exe', 80, 'application/x-msdownload');
        $respBad = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $bad,
        ]);
        $this->assertTrue(
            in_array($respBad->status(), [422, 302], true) || ($respBad->status() === 200 && ($respBad->json('status') === 0 || $respBad->json('status') === false))
        );
    }

    /**
     * 8. EC CERTIFICATE EMPIRICAL LIFECYCLE
     */
    public function test_ec_certificate_full_empirical_lifecycle_and_edge_cases(): void
    {
        $enviroProject = EnvironmentProject::create([
            'project_code' => 'EC-ADV-' . rand(1000, 9999),
            'project_name' => 'Adversarial Quarry EC Dossier',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'approved',
        ]);

        $cert = EcCertificate::create([
            'customer_id'            => $this->customer->id,
            'environment_project_id' => $enviroProject->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/ADV-' . rand(100, 999),
            'applicant_name'         => $this->customer->customer_name,
            'status'                 => 'active',
            'issue_date'             => now()->format('Y-m-d'),
            'expiry_date'            => now()->addYears(5)->format('Y-m-d'),
            'validity_years'         => 5,
        ]);

        // A. File creation
        $file1 = UploadedFile::fake()->create('order_v1.pdf', 520, 'application/pdf');
        $resp1 = $this->post(route('ec-certificate.upload'), [
            'certificate_id' => $cert->id,
            'document_type'  => 'certificate',
            'file'           => $file1,
        ]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['status' => 1, 'is_zip' => false]);
        $path1 = $resp1->json('file_path');
        $diskPath1 = $this->resolveDiskPath($path1);
        $this->assertFileExists($diskPath1, "EC Certificate file 1 must physically exist on disk");
        $this->trackedFiles[] = $diskPath1;

        // B. Re-upload replacement deletes old file
        $file2 = UploadedFile::fake()->create('order_v2.rar', 920, 'application/x-rar-compressed');
        $resp2 = $this->post(route('ec-certificate.upload'), [
            'certificate_id' => $cert->id,
            'document_type'  => 'certificate',
            'file'           => $file2,
        ]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['status' => 1, 'is_zip' => true]);
        $path2 = $resp2->json('file_path');
        $diskPath2 = $this->resolveDiskPath($path2);
        $this->assertFileExists($diskPath2, "EC Certificate replacement archive must exist on disk");
        $this->trackedFiles[] = $diskPath2;

        $this->assertFileDoesNotExist($diskPath1, "Previous EC Certificate file 1 must be unlinked on replacement");

        // C. Remove unlinks file from disk and resets DB record
        $resp3 = $this->post(route('ec-certificate.remove'), [
            'certificate_id' => $cert->id,
            'document_type'  => 'certificate',
        ]);
        $resp3->assertStatus(200);
        $this->assertFileDoesNotExist($diskPath2, "EC Certificate file 2 must be unlinked upon removal");

        $cert->refresh();
        $this->assertNull($cert->certificate_file);

        // D. Extension filter
        $bad = UploadedFile::fake()->create('shell.sh', 40, 'application/x-sh');
        $respBad = $this->post(route('ec-certificate.upload'), [
            'file'          => $bad,
            'document_type' => 'certificate',
        ]);
        $this->assertTrue(
            in_array($respBad->status(), [422, 302], true) || ($respBad->status() === 200 && ($respBad->json('status') === 0 || $respBad->json('status') === false))
        );
    }

    /**
     * 9. LONG FILENAMES & UNUSUAL CHARACTERS STRESS TEST
     */
    public function test_super_long_and_unusual_filenames_across_modules(): void
    {
        $longBaseName = 'TamilNadu_Department_Of_Geology_And_Mining_Granite_Quarry_Detailed_Statutory_Environmental_Baseline_Report_And_Groundwater_Assessment_Documentation_Field_Data_Package_Year_2026_Final_Approved_Version';
        
        // Test in Drone Survey
        $droneFile = UploadedFile::fake()->create($longBaseName . '_drone.kml', 300, 'application/vnd.google-earth.kml+xml');
        $respDrone = $this->post(route('drone-survey.upload'), [
            'file'          => $droneFile,
            'doc_item'      => 'long_doc_test',
            'document_name' => $longBaseName,
        ]);
        $respDrone->assertStatus(200);
        $pathDrone = $respDrone->json('file_path');
        $this->assertFileExists($this->resolveDiskPath($pathDrone));
        $this->trackedFiles[] = $this->resolveDiskPath($pathDrone);

        // Test in PPT Department
        $pptFile = UploadedFile::fake()->create($longBaseName . '_presentation.docx', 400, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $respPpt = $this->post(route('ppt-department.upload'), [
            'file'      => $pptFile,
            'field_id'  => 'long_ppt_test',
            'folder_id' => 1,
            'doc_name'  => $longBaseName,
        ]);
        $respPpt->assertStatus(200);
        $pathPpt = $respPpt->json('file_path');
        $this->assertFileExists($this->resolveDiskPath($pathPpt));
        $this->trackedFiles[] = $this->resolveDiskPath($pathPpt);
    }
}
