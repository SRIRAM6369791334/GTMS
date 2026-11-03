<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\EcCompliance;
use App\Models\EcComplianceDocument;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\EnvironmentalB2Project;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnvironmentalStep5Test extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
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
            'environment.view', 'environment.create', 'environment.edit',
            'environment.b2.view', 'environment.b2.create', 'environment.b2.edit',
            'environment.b2.upload', 'environment.b2.review',
            'ec_compliance.view', 'ec_compliance.create', 'ec_compliance.edit',
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
        $this->folder = Folder::first() ?? Folder::create(['folder_name' => 'General', 'status' => 1]);

        $rand = rand(1000, 9999);
        $this->customer = Customer::create([
            'customer_name'            => 'Ranganathan ' . $rand,
            'company_name'             => 'Kaveri Green Quarries ' . $rand,
            'mobile_num'               => '98411' . $rand,
            'secondary_mobile_num'     => '98412' . $rand,
            'secondary_contact_person' => 'M. Kumar',
            'email'                    => 'kaveri' . $rand . '@gtmsenviro.in',
            'mimas_no'                 => 'TN-MMS-ENV-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $filePath) {
            if ($filePath && file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        parent::tearDown();
    }

    protected function trackFile(string $path): void
    {
        $this->createdFiles[] = $path;
    }

    /**
     * =========================================================================
     * 1. ENVIRONMENT CLEARANCE B1 TESTS (EnverionsoneController)
     * =========================================================================
     */

    public function test_enviro_b1_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('mining_plan_approval.pdf', 300, 'application/pdf');

        $response = $this->post(route('enviro_b1.upload_document'), [
            'file'          => $file,
            'document_name' => 'Approved Mining Plan & Progressive Closure Scheme',
            'folder_id'     => $this->folder->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'  => 1,
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_path']);
        $fullPath = storage_path('app/public/' . $data['file_path']);
        $this->trackFile($fullPath);
        $this->assertFileExists($fullPath);

        // Verify session draft persistence
        $sessionDocs = session('enviro_b1_wizard.uploaded_docs', []);
        $this->assertNotEmpty($sessionDocs);
    }

    public function test_enviro_b1_reupload_unlinks_old_file_and_replaces(): void
    {
        // 1. Initial upload
        $firstFile = UploadedFile::fake()->create('draft_eia_v1.pdf', 200, 'application/pdf');
        $resp1 = $this->post(route('enviro_b1.upload_document'), [
            'file'          => $firstFile,
            'document_name' => 'Draft Environmental Impact Assessment (EIA) Report',
            'folder_id'     => $this->folder->id,
        ]);
        $resp1->assertStatus(200);
        $oldRelPath = $resp1->json('file_path');
        $oldFullPath = storage_path('app/public/' . $oldRelPath);
        $this->trackFile($oldFullPath);
        $this->assertFileExists($oldFullPath);

        // 2. Re-upload for the same document
        $secondFile = UploadedFile::fake()->create('draft_eia_v2_final.pdf', 350, 'application/pdf');
        $resp2 = $this->post(route('enviro_b1.upload_document'), [
            'file'          => $secondFile,
            'document_name' => 'Draft Environmental Impact Assessment (EIA) Report',
            'folder_id'     => $this->folder->id,
        ]);
        $resp2->assertStatus(200);
        $newRelPath = $resp2->json('file_path');
        $newFullPath = storage_path('app/public/' . $newRelPath);
        $this->trackFile($newFullPath);

        // Assert old file unlinked and new file exists
        $this->assertFileDoesNotExist($oldFullPath);
        $this->assertFileExists($newFullPath);
    }

    public function test_enviro_b1_remove_document_unlinks_physical_file(): void
    {
        $file = UploadedFile::fake()->create('tor_compliance_matrix.pdf', 250, 'application/pdf');
        $uploadResp = $this->post(route('enviro_b1.upload_document'), [
            'file'          => $file,
            'document_name' => 'Standard Terms of Reference (ToR) Compliance Matrix',
            'folder_id'     => $this->folder->id,
        ]);
        $uploadResp->assertStatus(200);
        $relPath = $uploadResp->json('file_path');
        $fullPath = storage_path('app/public/' . $relPath);
        $this->assertFileExists($fullPath);

        // Remove document
        $removeResp = $this->post(route('enviro_b1.remove_document'), [
            'document_name' => 'Standard Terms of Reference (ToR) Compliance Matrix',
        ]);
        $removeResp->assertStatus(200);
        $removeResp->assertJson([
            'status'  => 1,
            'success' => true,
        ]);

        $this->assertFileDoesNotExist($fullPath);
    }

    public function test_enviro_b1_rejects_disallowed_file_extension(): void
    {
        $badFile = UploadedFile::fake()->create('malicious_script.exe', 150, 'application/x-msdownload');

        $response = $this->postJson(route('enviro_b1.upload_document'), [
            'file'          => $badFile,
            'document_name' => 'Baseline Hydrogeology Survey',
        ]);

        $response->assertStatus(422);
    }

    /**
     * =========================================================================
     * 2. ENVIRONMENT CLEARANCE B2 TESTS (EnvironmentalB2Controller)
     * =========================================================================
     */

    public function test_enviro_b2_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('quarry_lease_deed.pdf', 250, 'application/pdf');

        $response = $this->post(route('enviro_b2.upload_document'), [
            'file'          => $file,
            'doc_item'      => 'doc_0',
            'document_name' => '1. Registered Lease Deed Agreement',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'success'  => true,
            'doc_item' => 'doc_0',
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_path']);
        $fullPath = public_path($data['file_path']);
        $this->trackFile($fullPath);
        $this->assertFileExists($fullPath);

        // Verify session persistence
        $sessionDocs = session('enviro_b2_wizard.uploaded_docs', []);
        $this->assertNotEmpty($sessionDocs);
    }

    public function test_enviro_b2_reupload_unlinks_old_file_and_replaces(): void
    {
        // 1. Initial upload
        $firstFile = UploadedFile::fake()->create('cluster_certificate_v1.pdf', 180, 'application/pdf');
        $resp1 = $this->post(route('enviro_b2.upload_document'), [
            'file'          => $firstFile,
            'doc_item'      => 'doc_1',
            'document_name' => '2. 500m Radius Cluster Certificate',
        ]);
        $resp1->assertStatus(200);
        $oldRelPath = $resp1->json('file_path');
        $oldFullPath = public_path($oldRelPath);
        $this->trackFile($oldFullPath);
        $this->assertFileExists($oldFullPath);

        // 2. Re-upload
        $secondFile = UploadedFile::fake()->create('cluster_certificate_v2_signed.pdf', 220, 'application/pdf');
        $resp2 = $this->post(route('enviro_b2.upload_document'), [
            'file'          => $secondFile,
            'doc_item'      => 'doc_1',
            'document_name' => '2. 500m Radius Cluster Certificate',
        ]);
        $resp2->assertStatus(200);
        $newRelPath = $resp2->json('file_path');
        $newFullPath = public_path($newRelPath);
        $this->trackFile($newFullPath);

        $this->assertFileDoesNotExist($oldFullPath);
        $this->assertFileExists($newFullPath);
    }

    public function test_enviro_b2_remove_document_unlinks_physical_file(): void
    {
        $file = UploadedFile::fake()->create('kml_boundary.zip', 400, 'application/zip');
        $uploadResp = $this->post(route('enviro_b2.upload_document'), [
            'file'          => $file,
            'doc_item'      => 'doc_2',
            'document_name' => '3. DGPS Lease Boundary Map and KML',
        ]);
        $uploadResp->assertStatus(200);
        $this->assertTrue($uploadResp->json('is_zip'));

        $relPath = $uploadResp->json('file_path');
        $fullPath = public_path($relPath);
        $this->assertFileExists($fullPath);

        // Remove document
        $removeResp = $this->post(route('enviro_b2.remove_document'), [
            'doc_item'      => 'doc_2',
            'document_name' => '3. DGPS Lease Boundary Map and KML',
        ]);
        $removeResp->assertStatus(200);
        $removeResp->assertJson([
            'status'  => 1,
            'success' => true,
        ]);

        $this->assertFileDoesNotExist($fullPath);
    }

    public function test_enviro_b2_rejects_disallowed_file_extension(): void
    {
        $badFile = UploadedFile::fake()->create('trojan.bat', 100, 'application/x-bat');

        $response = $this->postJson(route('enviro_b2.upload_document'), [
            'file'          => $badFile,
            'doc_item'      => 'doc_0',
            'document_name' => '1. Registered Lease Deed Agreement',
        ]);

        $response->assertStatus(422);
    }

    /**
     * =========================================================================
     * 3. EC COMPLIANCE TESTS (EcComplianceController)
     * =========================================================================
     */

    public function test_ec_compliance_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('air_quality_report.pdf', 280, 'application/pdf');

        $response = $this->post(route('ec-compliance.upload'), [
            'file'            => $file,
            'document_name'   => '1. Air Quality Monitoring Report',
            'folder_category' => 'site_analysis',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'  => 1,
            'success' => true,
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['file_path']);
        $fullPath = storage_path('app/public/' . $data['file_path']);
        $this->trackFile($fullPath);
        $this->assertFileExists($fullPath);

        // Verify session persistence
        $sessionDocs = session('ec_compliance_wizard.uploaded_docs', []);
        $this->assertNotEmpty($sessionDocs);
    }

    public function test_ec_compliance_reupload_unlinks_old_file_and_replaces(): void
    {
        // 1. Initial upload
        $firstFile = UploadedFile::fake()->create('noise_study_draft.pdf', 210, 'application/pdf');
        $resp1 = $this->post(route('ec-compliance.upload'), [
            'file'            => $firstFile,
            'document_name'   => '2. Noise Level Monitoring Report',
            'folder_category' => 'site_analysis',
        ]);
        $resp1->assertStatus(200);
        $oldRelPath = $resp1->json('file_path');
        $oldFullPath = storage_path('app/public/' . $oldRelPath);
        $this->trackFile($oldFullPath);
        $this->assertFileExists($oldFullPath);

        // 2. Re-upload
        $secondFile = UploadedFile::fake()->create('noise_study_signed_nabl.pdf', 310, 'application/pdf');
        $resp2 = $this->post(route('ec-compliance.upload'), [
            'file'            => $secondFile,
            'document_name'   => '2. Noise Level Monitoring Report',
            'folder_category' => 'site_analysis',
        ]);
        $resp2->assertStatus(200);
        $newRelPath = $resp2->json('file_path');
        $newFullPath = storage_path('app/public/' . $newRelPath);
        $this->trackFile($newFullPath);

        $this->assertFileDoesNotExist($oldFullPath);
        $this->assertFileExists($newFullPath);
    }

    public function test_ec_compliance_remove_document_unlinks_physical_file(): void
    {
        $file = UploadedFile::fake()->create('parivesh_receipt_ack.pdf', 190, 'application/pdf');
        $uploadResp = $this->post(route('ec-compliance.upload'), [
            'file'            => $file,
            'document_name'   => 'Parivesh Acknowledgement Receipt',
            'folder_category' => 'parivesh',
        ]);
        $uploadResp->assertStatus(200);
        $relPath = $uploadResp->json('file_path');
        $fullPath = storage_path('app/public/' . $relPath);
        $this->assertFileExists($fullPath);

        // Remove document
        $removeResp = $this->post(route('ec-compliance.remove'), [
            'document_name'   => 'Parivesh Acknowledgement Receipt',
            'folder_category' => 'parivesh',
        ]);
        $removeResp->assertStatus(200);
        $removeResp->assertJson([
            'status'  => 1,
            'success' => true,
        ]);

        $this->assertFileDoesNotExist($fullPath);
    }

    public function test_ec_compliance_rejects_disallowed_file_extension(): void
    {
        $badFile = UploadedFile::fake()->create('exploit.sh', 80, 'text/x-shellscript');

        $response = $this->postJson(route('ec-compliance.upload'), [
            'file'            => $badFile,
            'document_name'   => '1. 500m Letter / Certificate',
            'folder_category' => 'checklist',
        ]);

        $response->assertStatus(422);
    }

    /**
     * =========================================================================
     * 4. VIEW RENDERING & GTMSOPENWITH ATTRIBUTES ASSERTIONS
     * =========================================================================
     */

    public function test_enviro_b2_wizard_view_renders_interactive_step4_elements(): void
    {
        $response = $this->get(route('environment-b2.step', 4));
        $response->assertStatus(200);
        $response->assertSee('checklist-row', false);
        $response->assertSee('btn-upload-item', false);
        $response->assertSee('btn-view-item', false);
        $response->assertSee('btn-remove-item', false);
        $response->assertSee('row-file-input', false);
    }

    public function test_ec_compliance_wizard_view_renders_interactive_steps(): void
    {
        $response = $this->get(route('ec-compliance.step', 2));
        $response->assertStatus(200);
        $response->assertSee('checklist-row', false);
        $response->assertSee('btn-upload-comp-item', false);
        $response->assertSee('btn-view-comp-item', false);
        $response->assertSee('btn-remove-comp-item', false);
        $response->assertSee('comp_checklist_container', false);
    }

    public function test_eviron_show_view_renders_interactive_document_elements(): void
    {
        $project = EnvironmentProject::create([
            'project_name'   => 'Test EC Granite Quarry ' . rand(100, 999),
            'project_code'   => 'ENV-B1-2026-TEST-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category'       => 'B1',
            'sub_category'   => 'TOR',
            'status'         => 'draft',
            'created_by'     => $this->user->id,
        ]);

        $folder = Folder::firstOrCreate(
            ['module_id' => 1, 'name' => 'Documents'],
            ['sort_order' => 1, 'status' => 1]
        );

        $doc = EnvironmentDocument::firstOrCreate([
            'environment_project_id' => $project->id,
            'document_name'          => '1. 500m Cluster Certificate',
        ], [
            'folder_id'              => $folder->id,
            'status'                 => 'pending',
        ]);

        $response = $this->get(route('eviron.show', $project->id));
        $response->assertStatus(200);
        $response->assertSee('enviro-doc-row', false);
        $response->assertSee('row-file-input', false);
        $response->assertSee('btn-upload-item', false);
        $response->assertSee('btn-view-item', false);
        $response->assertSee('btn-remove-item', false);
    }

    public function test_ec_compliance_show_view_renders_interactive_document_elements(): void
    {
        $comp = EcCompliance::create([
            'project_name'      => 'Kaveri Quarry EC Compliance ' . rand(100, 999),
            'compliance_no'     => 'HYC-2026-TEST-' . rand(100, 999),
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'compliance_period' => 'April 2026 - September 2026',
            'status'            => 'draft',
            'product_value'     => 50000,
            'paid_amount'       => 50000,
            'created_by'        => $this->user->id,
        ]);

        $response = $this->get(route('ec-compliance.show', $comp->id));
        $response->assertStatus(200);
        $response->assertSee('ec-doc-row', false);
        $response->assertSee('row-file-input', false);
        $response->assertSee('btn-upload-item', false);
        $response->assertSee('btn-view-item', false);
        $response->assertSee('btn-remove-item', false);
    }
}
