<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DgpsDocument;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\DroneDocument;
use App\Models\DroneSurvey;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DroneAndDgpsSurveyStep5Test extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
    protected Folder $folder;

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

        // Ensure user has necessary permissions
        $permissions = ['drone.view', 'drone.create', 'drone.edit', 'dgps.view', 'dgps.create', 'dgps.edit'];
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
        $this->folder = Folder::first() ?? Folder::create(['folder_name' => 'General', 'status' => 1]);

        $rand = rand(1000, 9999);
        $this->customer = Customer::create([
            'customer_name'            => 'Senthil ' . $rand,
            'company_name'             => 'Kaveri Minerals ' . $rand,
            'mobile_num'               => '98401' . $rand,
            'secondary_mobile_num'     => '98402' . $rand,
            'secondary_contact_person' => 'R. Kumar',
            'email'                    => 'kaveri' . $rand . '@gtmsdrone.in',
            'mimas_no'                 => 'TN-MMS-DRN-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    /**
     * Test Drone Survey upload document stores file and returns uniform Step 5 response.
     */
    public function test_drone_survey_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('flight_log.csv', 120, 'text/csv');

        $response = $this->post(route('drone-survey.upload'), [
            'file'          => $file,
            'doc_item'      => 'doc_0',
            'document_name' => '5. Drone Mission Flight Log & DGCA RPC Certificate',
            'folder_id'     => $this->folder->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'success'  => true,
            'doc_item' => 'doc_0',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_path']);
        $this->assertFileExists(public_path($data['file_path']));

        // Clean up physical file
        if (file_exists(public_path($data['file_path']))) {
            @unlink(public_path($data['file_path']));
        }
    }

    /**
     * Test Drone Survey re-upload safely unlinks previous file on disk and updates record.
     */
    public function test_drone_survey_reupload_deletes_old_file_and_updates_database(): void
    {
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-2026-TEST-' . rand(100, 999),
            'customer_id'          => $this->customer->id,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 15000,
        ]);

        // Upload first file
        $file1 = UploadedFile::fake()->create('initial_ortho.jpg', 200, 'image/jpeg');
        $resp1 = $this->post(route('drone-survey.upload'), [
            'file'            => $file1,
            'drone_survey_id' => $survey->id,
            'document_name'   => '7. High-Resolution Orthomosaic Map',
            'folder_id'       => $this->folder->id,
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->assertFileExists(public_path($path1));

        $docRecord = DroneDocument::where('drone_survey_id', $survey->id)->first();
        $this->assertNotNull($docRecord);
        $this->assertEquals($path1, $docRecord->file_path);

        // Re-upload replacement file
        $file2 = UploadedFile::fake()->create('updated_ortho.jpg', 300, 'image/jpeg');
        $resp2 = $this->post(route('drone-survey.upload'), [
            'file'            => $file2,
            'drone_survey_id' => $survey->id,
            'doc_id'          => $docRecord->id,
            'document_name'   => '7. High-Resolution Orthomosaic Map',
            'folder_id'       => $this->folder->id,
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');

        // Verify old file was deleted and new file exists
        $this->assertFileDoesNotExist(public_path($path1));
        $this->assertFileExists(public_path($path2));

        // Clean up
        if (file_exists(public_path($path2))) {
            @unlink(public_path($path2));
        }
    }

    /**
     * Test Drone Survey upload rejects unsupported extensions.
     */
    public function test_drone_survey_upload_rejects_unsupported_extensions(): void
    {
        $badFile = UploadedFile::fake()->create('malicious.exe', 50, 'application/x-msdownload');

        $response = $this->postJson(route('drone-survey.upload'), [
            'file'          => $badFile,
            'doc_item'      => 'doc_exe',
            'document_name' => '1. Request Letter',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test Drone Survey remove document unlinks physical file and deletes database record.
     */
    public function test_drone_survey_remove_document_unlinks_file_and_deletes_database_record(): void
    {
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-2026-DEL-' . rand(100, 999),
            'customer_id'          => $this->customer->id,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 12000,
        ]);

        $file = UploadedFile::fake()->create('delete_me.pdf', 100, 'application/pdf');
        $uploadResp = $this->post(route('drone-survey.upload'), [
            'file'            => $file,
            'drone_survey_id' => $survey->id,
            'document_name'   => '1. Request Letter',
            'folder_id'       => $this->folder->id,
        ]);
        $filePath = $uploadResp->json('file_path');
        $docId = $uploadResp->json('doc_id');

        $this->assertFileExists(public_path($filePath));
        $this->assertDatabaseHas('drone_documents', ['id' => $docId]);

        // Remove document
        $removeResp = $this->post(route('drone-survey.remove'), [
            'drone_survey_id' => $survey->id,
            'doc_id'          => $docId,
            'document_name'   => '1. Request Letter',
        ]);

        $removeResp->assertStatus(200);
        $removeResp->assertJson(['status' => 1, 'success' => true]);

        // Physical file must be unlinked
        $this->assertFileDoesNotExist(public_path($filePath));
        $this->assertDatabaseMissing('drone_documents', ['id' => $docId]);
    }

    /**
     * Test DGPS Survey upload document stores file and returns uniform Step 5 response.
     */
    public function test_dgps_survey_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('rinex_base.obs', 150, 'text/plain');

        $response = $this->post(route('dgps-survey.upload'), [
            'file'          => $file,
            'doc_item'      => 'doc_0',
            'document_name' => '1. Raw RINEX Base & Rover Observation Data',
            'folder_id'     => $this->folder->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'success'  => true,
            'doc_item' => 'doc_0',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_path']);
        $this->assertFileExists(public_path($data['file_path']));

        // Clean up physical file
        if (file_exists(public_path($data['file_path']))) {
            @unlink(public_path($data['file_path']));
        }
    }

    /**
     * Test DGPS Survey re-upload safely unlinks previous file on disk and updates record.
     */
    public function test_dgps_survey_reupload_deletes_old_file_and_updates_database(): void
    {
        $survey = DgpsSurvey::create([
            'survey_no'     => 'DGPS-2026-TEST-' . rand(100, 999),
            'customer_id'   => $this->customer->id,
            'survey_status' => 'completed',
            'lease_area_ha' => 3.85,
        ]);

        // Upload first file
        $file1 = UploadedFile::fake()->create('boundary_v1.kml', 80, 'application/vnd.google-earth.kml+xml');
        $resp1 = $this->post(route('dgps-survey.upload'), [
            'file'          => $file1,
            'survey_id'     => $survey->id,
            'document_name' => '5. Boundary Polygon KML / KMZ File',
            'folder_id'     => $this->folder->id,
        ]);
        $resp1->assertStatus(200);
        $path1 = $resp1->json('file_path');
        $this->assertFileExists(public_path($path1));

        $docRecord = DgpsDocument::where('dgps_survey_id', $survey->id)->first();
        $this->assertNotNull($docRecord);
        $this->assertEquals($path1, $docRecord->file_path);

        // Re-upload replacement file
        $file2 = UploadedFile::fake()->create('boundary_v2.kml', 95, 'application/vnd.google-earth.kml+xml');
        $resp2 = $this->post(route('dgps-survey.upload'), [
            'file'          => $file2,
            'survey_id'     => $survey->id,
            'doc_id'        => $docRecord->id,
            'document_name' => '5. Boundary Polygon KML / KMZ File',
            'folder_id'     => $this->folder->id,
        ]);
        $resp2->assertStatus(200);
        $path2 = $resp2->json('file_path');

        // Verify old file was deleted and new file exists
        $this->assertFileDoesNotExist(public_path($path1));
        $this->assertFileExists(public_path($path2));

        // Clean up
        if (file_exists(public_path($path2))) {
            @unlink(public_path($path2));
        }
    }

    /**
     * Test DGPS Survey upload rejects unsupported extensions.
     */
    public function test_dgps_survey_upload_rejects_unsupported_extensions(): void
    {
        $badFile = UploadedFile::fake()->create('danger.sh', 30, 'application/x-sh');

        $response = $this->postJson(route('dgps-survey.upload'), [
            'file'          => $badFile,
            'doc_item'      => 'doc_bad',
            'document_name' => '1. Raw RINEX Base & Rover Observation Data',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test DGPS Survey remove document unlinks physical file and deletes database record.
     */
    public function test_dgps_survey_remove_document_unlinks_file_and_deletes_database_record(): void
    {
        $survey = DgpsSurvey::create([
            'survey_no'     => 'DGPS-2026-DEL-' . rand(100, 999),
            'customer_id'   => $this->customer->id,
            'survey_status' => 'completed',
            'lease_area_ha' => 4.20,
        ]);

        $file = UploadedFile::fake()->create('control_network.pdf', 140, 'application/pdf');
        $uploadResp = $this->post(route('dgps-survey.upload'), [
            'file'          => $file,
            'survey_id'     => $survey->id,
            'document_name' => '2. Benchmark Fixation & Control Network Sheet',
            'folder_id'     => $this->folder->id,
        ]);
        $filePath = $uploadResp->json('file_path');
        $docId = $uploadResp->json('doc_id');

        $this->assertFileExists(public_path($filePath));
        $this->assertDatabaseHas('dgps_documents', ['id' => $docId]);

        // Remove document
        $removeResp = $this->post(route('dgps-survey.remove'), [
            'survey_id'     => $survey->id,
            'doc_id'        => $docId,
            'document_name' => '2. Benchmark Fixation & Control Network Sheet',
        ]);

        $removeResp->assertStatus(200);
        $removeResp->assertJson(['status' => 1, 'success' => true]);

        // Physical file must be unlinked
        $this->assertFileDoesNotExist(public_path($filePath));
        $this->assertDatabaseMissing('dgps_documents', ['id' => $docId]);
    }

    /**
     * Test Drone Survey views render Drag & Drop, badges, and action trio buttons.
     */
    public function test_drone_survey_views_render_action_trio_and_gtms_open_with(): void
    {
        // 1. Wizard Step 3
        $wizardResp = $this->get(route('drone-survey.step', 3));
        $wizardResp->assertStatus(200);
        $wizardResp->assertSee('checklist-row');
        $wizardResp->assertSee('btn-view-item');
        $wizardResp->assertSee('btn-upload-item');
        $wizardResp->assertSee('btn-remove-item');
        $wizardResp->assertSee('row-file-input');

        // 2. Show Dossier
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-2026-VIEW-' . rand(100, 999),
            'customer_id'          => $this->customer->id,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 18000,
        ]);

        $showResp = $this->get(route('drone-survey.show', $survey->id));
        $showResp->assertStatus(200);
        $showResp->assertSee('deliverable-row');
        $showResp->assertSee('btn-view-item');
        $showResp->assertSee('btn-upload-item');
        $showResp->assertSee('btn-remove-item');
        $showResp->assertSee('data-file-url');
    }

    /**
     * Test DGPS Survey views render Drag & Drop, badges, and action trio buttons.
     */
    public function test_dgps_survey_views_render_action_trio_and_gtms_open_with(): void
    {
        // 1. Wizard Step 3
        $wizardResp = $this->get(route('dgps-survey.step', 3));
        $wizardResp->assertStatus(200);
        $wizardResp->assertSee('checklist-row');
        $wizardResp->assertSee('btn-view-dgps-item');
        $wizardResp->assertSee('btn-upload-dgps-item');
        $wizardResp->assertSee('btn-remove-dgps-item');

        // 2. Show Dossier
        $survey = DgpsSurvey::create([
            'survey_no'     => 'DGPS-2026-VIEW-' . rand(100, 999),
            'customer_id'   => $this->customer->id,
            'survey_status' => 'completed',
            'lease_area_ha' => 3.85,
        ]);

        $showResp = $this->get(route('dgps-survey.show', $survey->id));
        $showResp->assertStatus(200);
        $showResp->assertSee('deliverable-item');
        $showResp->assertSee('btn-view-dgps-item');
        $showResp->assertSee('btn-upload-dgps-item');
        $showResp->assertSee('btn-remove-dgps-item');
    }
}
