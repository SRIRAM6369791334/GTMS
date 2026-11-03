<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\EcCertificate;
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
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EnterprisePortalsStep5Test extends TestCase
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
        $this->mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);
        $this->folder = Folder::first() ?? Folder::create(['folder_name' => 'General', 'status' => 1]);

        $rand = rand(1000, 9999);
        $this->customer = Customer::create([
            'customer_name'            => 'Enterprise Applicant ' . $rand,
            'company_name'             => 'Kongu Mines Ltd ' . $rand,
            'mobile_num'               => '98421' . $rand,
            'secondary_mobile_num'     => '98422' . $rand,
            'secondary_contact_person' => 'S. Rajan',
            'email'                    => 'kongu' . $rand . '@gtmsportal.in',
            'mimas_no'                 => 'TN-MMS-ENT-' . $rand,
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
     * 1. PPT DEPARTMENT TESTS (PptDepartmentController)
     * =========================================================================
     */

    public function test_ppt_department_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('seac_presentation_draft.pptx', 450, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');

        $response = $this->post(route('ppt-department.upload'), [
            'file'      => $file,
            'field_id'  => 0,
            'folder_id' => 1,
            'doc_name'  => 'PARIVESH Documents',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'field_id' => '0',
            'is_zip'   => false,
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['file_path']);
        $this->assertNotEmpty($json['file_url']);

        $physPath = public_path($json['file_path']);
        if (!file_exists($physPath)) {
            $physPath = storage_path('app/public/' . $json['file_path']);
        }
        if (!file_exists($physPath)) {
            $physPath = public_path('storage/' . $json['file_path']);
        }
        $this->assertFileExists($physPath);
        $this->trackFile($physPath);

        // Verify session persistence
        $uploadedDocs = session('ppt_wizard.uploaded_docs', []);
        $this->assertArrayHasKey('0', $uploadedDocs);
        $this->assertEquals('seac_presentation_draft.pptx', $uploadedDocs['0']['file_name']);
    }

    public function test_ppt_department_reupload_unlinks_old_file_and_replaces(): void
    {
        $firstFile = UploadedFile::fake()->create('old_agenda.pdf', 300, 'application/pdf');

        $res1 = $this->post(route('ppt-department.upload'), [
            'file'      => $firstFile,
            'field_id'  => '4',
            'folder_id' => 5,
            'doc_name'  => 'SEAC Agenda',
        ]);
        $res1->assertStatus(200);
        $json1 = $res1->json();

        $oldPhysPath = public_path($json1['file_path']);
        if (!file_exists($oldPhysPath)) {
            $oldPhysPath = storage_path('app/public/' . $json1['file_path']);
        }
        if (!file_exists($oldPhysPath)) {
            $oldPhysPath = public_path('storage/' . $json1['file_path']);
        }
        $this->assertFileExists($oldPhysPath);
        $this->trackFile($oldPhysPath);

        // Re-upload replacement file for the same field
        $secondFile = UploadedFile::fake()->create('revised_agenda.zip', 600, 'application/zip');
        $res2 = $this->post(route('ppt-department.upload'), [
            'file'      => $secondFile,
            'field_id'  => '4',
            'folder_id' => 5,
            'doc_name'  => 'SEAC Agenda',
        ]);
        $res2->assertStatus(200);
        $json2 = $res2->json();

        $newPhysPath = public_path($json2['file_path']);
        if (!file_exists($newPhysPath)) {
            $newPhysPath = storage_path('app/public/' . $json2['file_path']);
        }
        if (!file_exists($newPhysPath)) {
            $newPhysPath = public_path('storage/' . $json2['file_path']);
        }
        $this->assertFileExists($newPhysPath);
        $this->trackFile($newPhysPath);

        // Assert old physical file was unlinked
        $this->assertFileDoesNotExist($oldPhysPath);
        $this->assertTrue($json2['is_zip']);
    }

    public function test_ppt_department_remove_document_unlinks_physical_file(): void
    {
        $file = UploadedFile::fake()->create('demand_note.pdf', 200, 'application/pdf');

        $uploadRes = $this->post(route('ppt-department.upload'), [
            'file'      => $file,
            'field_id'  => '2',
            'folder_id' => 3,
            'doc_name'  => 'Demand Note',
        ]);
        $uploadRes->assertStatus(200);
        $uploadJson = $uploadRes->json();

        $physPath = public_path($uploadJson['file_path']);
        if (!file_exists($physPath)) {
            $physPath = storage_path('app/public/' . $uploadJson['file_path']);
        }
        if (!file_exists($physPath)) {
            $physPath = public_path('storage/' . $uploadJson['file_path']);
        }
        $this->assertFileExists($physPath);

        // Remove document
        $removeRes = $this->post(route('ppt-department.remove'), [
            'field_id'   => '2',
            'folder_key' => '2',
        ]);
        $removeRes->assertStatus(200);
        $removeRes->assertJson(['status' => 1]);

        // Physical file must be removed
        $this->assertFileDoesNotExist($physPath);

        // Session entry removed
        $uploadedDocs = session('ppt_wizard.uploaded_docs', []);
        $this->assertArrayNotHasKey('2', $uploadedDocs);
    }

    public function test_ppt_department_rejects_disallowed_file_extension(): void
    {
        $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/x-msdownload');

        $response = $this->post(route('ppt-department.upload'), [
            'file'      => $badFile,
            'field_id'  => '0',
            'folder_id' => 1,
            'doc_name'  => 'PARIVESH Documents',
        ]);

        $this->assertTrue(
            in_array($response->status(), [422, 302], true) || ($response->status() === 200 && ($response->json('status') === 0 || $response->json('status') === false))
        );
    }

    public function test_ppt_department_database_record_document_upload_and_removal(): void
    {
        $ppt = PptApplication::create([
            'application_no'     => 'PPT-ENT-' . rand(1000, 9999),
            'customer_id'        => $this->customer->id,
            'district_id'        => $this->district->id,
            'mineral_id'         => $this->mineral->id,
            'project_name'       => 'Granite Quarry SEAC Presentation',
            'product_value'      => 50000,
            'status'             => 'draft',
            'presentation_stage' => 'b2_presentation',
        ]);

        $docFile = UploadedFile::fake()->create('minutes_extract.pdf', 320, 'application/pdf');

        $uploadRes = $this->post(route('ppt-department.upload'), [
            'file'       => $docFile,
            'ppt_id'     => $ppt->id,
            'folder_id'  => 6,
            'field_id'   => 6,
            'doc_name'   => 'SEAC Minutes',
        ]);
        $uploadRes->assertStatus(200);
        $uploadJson = $uploadRes->json();

        $physPath = public_path($uploadJson['file_path']);
        if (!file_exists($physPath)) {
            $physPath = storage_path('app/public/' . $uploadJson['file_path']);
        }
        if (!file_exists($physPath)) {
            $physPath = public_path('storage/' . $uploadJson['file_path']);
        }
        $this->assertFileExists($physPath);
        $this->trackFile($physPath);

        // Assert database record exists
        $pptDoc = PptDocument::where('ppt_application_id', $ppt->id)->where('folder_id', 6)->first();
        $this->assertNotNull($pptDoc);

        // Remove DB document
        $removeRes = $this->post(route('ppt-department.remove'), [
            'ppt_id'     => $ppt->id,
            'doc_id'     => $pptDoc->id,
            'folder_key' => 6,
        ]);
        $removeRes->assertStatus(200);
        $this->assertFileDoesNotExist($physPath);
        $this->assertSoftDeleted('ppt_documents', ['id' => $pptDoc->id]);
    }

    /**
     * =========================================================================
     * 2. MINING PORTAL TESTS (MiningController)
     * =========================================================================
     */

    public function test_mining_document_upload_and_disk_persistence(): void
    {
        $app = MiningApplication::create([
            'application_no'    => 'MP-ENT-' . rand(1000, 9999),
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'mineral_id'        => $this->mineral->id,
            'current_step'      => 6,
            'status'            => 'draft',
        ]);

        $doc = MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'Geological Plan & Survey',
            'status'                => 'pending',
        ]);

        $file = UploadedFile::fake()->create('mine_geological_map.kml', 500, 'application/vnd.google-earth.kml+xml');

        $response = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'is_zip' => false,
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['file_path']);

        $physPath = public_path($json['file_path']);
        if (!file_exists($physPath)) {
            $physPath = storage_path('app/public/' . $json['file_path']);
        }
        $this->assertFileExists($physPath);
        $this->trackFile($physPath);

        // Verify database model updated
        $doc->refresh();
        $this->assertEquals('uploaded', $doc->status);
        $this->assertEquals('mine_geological_map.kml', $doc->file_name);
    }

    public function test_mining_document_reupload_unlinks_old_file_and_replaces(): void
    {
        $app = MiningApplication::create([
            'application_no' => 'MP-ENT-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'current_step'   => 6,
            'status'         => 'draft',
        ]);

        $doc = MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'Mining Scheme Report',
            'status'                => 'pending',
        ]);

        $file1 = UploadedFile::fake()->create('scheme_v1.pdf', 300, 'application/pdf');
        $res1 = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file1,
        ]);
        $res1->assertStatus(200);
        $json1 = $res1->json();

        $phys1 = public_path($json1['file_path']);
        if (!file_exists($phys1)) {
            $phys1 = storage_path('app/public/' . $json1['file_path']);
        }
        $this->assertFileExists($phys1);
        $this->trackFile($phys1);

        // Replace with ZIP
        $file2 = UploadedFile::fake()->create('scheme_v2.zip', 700, 'application/zip');
        $res2 = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file2,
        ]);
        $res2->assertStatus(200);
        $json2 = $res2->json();

        $phys2 = public_path($json2['file_path']);
        if (!file_exists($phys2)) {
            $phys2 = storage_path('app/public/' . $json2['file_path']);
        }
        $this->assertFileExists($phys2);
        $this->trackFile($phys2);

        // Assert old file unlinked
        $this->assertFileDoesNotExist($phys1);
        $this->assertTrue($json2['is_zip']);
    }

    public function test_mining_document_remove_unlinks_physical_file_and_resets_status(): void
    {
        $app = MiningApplication::create([
            'application_no' => 'MP-ENT-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'current_step'   => 6,
            'status'         => 'draft',
        ]);

        $doc = MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'NOC from PWD',
            'status'                => 'pending',
        ]);

        $file = UploadedFile::fake()->create('pwd_noc.pdf', 250, 'application/pdf');
        $uploadRes = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $file,
        ]);
        $uploadRes->assertStatus(200);
        $uploadJson = $uploadRes->json();

        $phys = public_path($uploadJson['file_path']);
        if (!file_exists($phys)) {
            $phys = storage_path('app/public/' . $uploadJson['file_path']);
        }
        $this->assertFileExists($phys);

        // Remove
        $removeRes = $this->post(route('mining.document.remove'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
        ]);
        $removeRes->assertStatus(200);
        $removeRes->assertJson(['status' => 1]);

        $this->assertFileDoesNotExist($phys);

        $doc->refresh();
        $this->assertEquals('pending', $doc->status);
        $this->assertNull($doc->file_path);
    }

    public function test_mining_document_rejects_disallowed_file_extension(): void
    {
        $app = MiningApplication::create([
            'application_no' => 'MP-ENT-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'current_step'   => 6,
            'status'         => 'draft',
        ]);

        $doc = MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'Boundary Coordinates',
            'status'                => 'pending',
        ]);

        $badFile = UploadedFile::fake()->create('exploit.bat', 50, 'application/x-bat');

        $response = $this->post(route('mining.document.upload'), [
            'mining_application_id' => $app->id,
            'document_id'           => $doc->id,
            'file'                  => $badFile,
        ]);

        $this->assertTrue(
            in_array($response->status(), [422, 302], true) || ($response->status() === 200 && ($response->json('status') === 0 || $response->json('status') === false))
        );
    }

    /**
     * =========================================================================
     * 3. EC CERTIFICATE TESTS (EcCertificateController)
     * =========================================================================
     */

    public function test_ec_certificate_upload_and_session_persistence(): void
    {
        $file = UploadedFile::fake()->create('seiaa_ec_order.pdf', 800, 'application/pdf');

        $response = $this->post(route('ec-certificate.upload'), [
            'file'          => $file,
            'document_type' => 'certificate',
            'doc_name'      => 'Environmental Clearance Certificate',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'is_zip' => false,
        ]);

        $json = $response->json();
        $this->assertNotEmpty($json['file_path']);
        $this->assertNotEmpty($json['file_url']);

        $physPath = public_path($json['file_path']);
        if (!file_exists($physPath)) {
            $physPath = public_path('storage/' . $json['file_path']);
        }
        $this->assertFileExists($physPath);
        $this->trackFile($physPath);

        // Check session
        $s2 = session('ec_certificate_wizard.step2', []);
        $this->assertEquals('seiaa_ec_order.pdf', $s2['file_name']);
        $this->assertEquals($json['file_path'], $s2['file_path']);
    }

    public function test_ec_certificate_reupload_unlinks_old_file_and_replaces(): void
    {
        $file1 = UploadedFile::fake()->create('signed_order_v1.pdf', 400, 'application/pdf');
        $res1 = $this->post(route('ec-certificate.upload'), [
            'file'          => $file1,
            'document_type' => 'certificate',
        ]);
        $res1->assertStatus(200);
        $json1 = $res1->json();

        $phys1 = public_path($json1['file_path']);
        if (!file_exists($phys1)) {
            $phys1 = public_path('storage/' . $json1['file_path']);
        }
        $this->assertFileExists($phys1);
        $this->trackFile($phys1);

        // Replacement certificate
        $file2 = UploadedFile::fake()->create('signed_order_v2.zip', 950, 'application/zip');
        $res2 = $this->post(route('ec-certificate.upload'), [
            'file'          => $file2,
            'document_type' => 'certificate',
        ]);
        $res2->assertStatus(200);
        $json2 = $res2->json();

        $phys2 = public_path($json2['file_path']);
        if (!file_exists($phys2)) {
            $phys2 = public_path('storage/' . $json2['file_path']);
        }
        $this->assertFileExists($phys2);
        $this->trackFile($phys2);

        // Assert old file unlinked
        $this->assertFileDoesNotExist($phys1);
        $this->assertTrue($json2['is_zip']);
    }

    public function test_ec_certificate_remove_unlinks_physical_file(): void
    {
        $file = UploadedFile::fake()->create('draft_ec_cert.pdf', 350, 'application/pdf');
        $uploadRes = $this->post(route('ec-certificate.upload'), [
            'file'          => $file,
            'document_type' => 'certificate',
        ]);
        $uploadRes->assertStatus(200);
        $uploadJson = $uploadRes->json();

        $phys = public_path($uploadJson['file_path']);
        if (!file_exists($phys)) {
            $phys = public_path('storage/' . $uploadJson['file_path']);
        }
        $this->assertFileExists($phys);

        // Remove
        $removeRes = $this->post(route('ec-certificate.remove'), [
            'document_type' => 'certificate',
        ]);
        $removeRes->assertStatus(200);
        $removeRes->assertJson(['status' => 1]);

        $this->assertFileDoesNotExist($phys);

        // Session cleared
        $s2 = session('ec_certificate_wizard.step2', []);
        $this->assertEmpty($s2['file_path'] ?? null);
    }

    public function test_ec_certificate_database_record_upload_and_removal(): void
    {
        $enviroProject = EnvironmentProject::create([
            'project_code' => 'EC-PRJ-' . rand(1000, 9999),
            'project_name' => 'Salem Quarry EC Dossier',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'approved',
        ]);

        $cert = EcCertificate::create([
            'customer_id'            => $this->customer->id,
            'environment_project_id' => $enviroProject->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/' . rand(1000, 9999),
            'applicant_name'         => $this->customer->customer_name,
            'status'                 => 'active',
            'issue_date'             => now()->format('Y-m-d'),
            'expiry_date'            => now()->addYears(5)->format('Y-m-d'),
            'validity_years'         => 5,
        ]);

        $file = UploadedFile::fake()->create('official_ec_order.pdf', 600, 'application/pdf');

        $uploadRes = $this->post(route('ec-certificate.upload'), [
            'certificate_id' => $cert->id,
            'document_type'  => 'certificate',
            'file'           => $file,
        ]);
        $uploadRes->assertStatus(200);
        $uploadJson = $uploadRes->json();

        $phys = public_path($uploadJson['file_path']);
        if (!file_exists($phys)) {
            $phys = public_path('storage/' . $uploadJson['file_path']);
        }
        $this->assertFileExists($phys);
        $this->trackFile($phys);

        $cert->refresh();
        $this->assertNotEmpty($cert->certificate_file);

        // Remove
        $removeRes = $this->post(route('ec-certificate.remove'), [
            'certificate_id' => $cert->id,
            'document_type'  => 'certificate',
        ]);
        $removeRes->assertStatus(200);

        $this->assertFileDoesNotExist($phys);
        $cert->refresh();
        $this->assertNull($cert->certificate_file);
    }

    public function test_ec_certificate_rejects_disallowed_file_extension(): void
    {
        $badFile = UploadedFile::fake()->create('script.sh', 80, 'application/x-sh');

        $response = $this->post(route('ec-certificate.upload'), [
            'file'          => $badFile,
            'document_type' => 'certificate',
        ]);

        $this->assertTrue(
            in_array($response->status(), [422, 302], true) || ($response->status() === 200 && ($response->json('status') === 0 || $response->json('status') === false))
        );
    }

    /**
     * =========================================================================
     * 4. BLADE VIEW INTEGRATION TESTS
     * =========================================================================
     */

    public function test_ppt_wizard_view_renders_interactive_step5_elements(): void
    {
        $response = $this->get(route('ppt-department.step', 5));
        $response->assertStatus(200);
        $response->assertSee('ppt_checklist_container', false);
        $response->assertSee('ppt-file-input', false);
        $response->assertSee('row-dragover', false);
        $response->assertSee('btn-view-item', false);
        $response->assertSee('btn-upload-ppt-item', false);
        $response->assertSee('btn-remove-ppt-item', false);
    }

    public function test_ppt_show_view_renders_interactive_folder_elements(): void
    {
        $ppt = PptApplication::create([
            'application_no'     => 'PPT-ENT-' . rand(1000, 9999),
            'customer_id'        => $this->customer->id,
            'district_id'        => $this->district->id,
            'mineral_id'         => $this->mineral->id,
            'project_name'       => 'Granite Quarry SEAC Presentation',
            'product_value'      => 50000,
            'status'             => 'draft',
            'presentation_stage' => 'b2_presentation',
        ]);

        $response = $this->get(route('ppt-department.show', $ppt->id));
        $response->assertStatus(200);
        $response->assertSee('ppt-show-folder-row', false);
        $response->assertSee('ppt-show-file-input', false);
        $response->assertSee('btn-ppt-show-upload', false);
        $response->assertSee('row-dragover', false);
    }

    public function test_mining_document_view_renders_interactive_elements(): void
    {
        $app = MiningApplication::create([
            'application_no' => 'MP-ENT-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'current_step'   => 6,
            'status'         => 'draft',
        ]);

        MiningDocument::create([
            'mining_application_id' => $app->id,
            'folder_id'             => $this->folder->id,
            'document_name'         => 'Environmental Management Plan',
            'status'                => 'pending',
        ]);

        $response = $this->get(route('mining.documents', $app->id));
        $response->assertStatus(200);
        $response->assertSee('doc-card-item', false);
        $response->assertSee('input-mining-file', false);
        $response->assertSee('row-dragover', false);
        $response->assertSee('uploadMiningDocFile', false);
    }

    public function test_ec_certificate_wizard_renders_interactive_step2_elements(): void
    {
        $enviroProject = EnvironmentProject::create([
            'project_code' => 'EC-PRJ-' . rand(1000, 9999),
            'project_name' => 'Salem Quarry EC Dossier',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'approved',
        ]);

        $response = $this->withSession([
            'ec_wizard' => [
                'step1' => [
                    'environment_project_id' => $enviroProject->id,
                    'ec_ref_no'              => 'SEIAA-TN/EC/2026/001',
                    'applicant_name'         => $this->customer->customer_name,
                    'completed'              => true,
                ]
            ]
        ])->get(route('ec-certificate.step', 2));

        $response->assertStatus(200);
        $response->assertSee('ec_certificate_dropzone', false);
        $response->assertSee('btn_view_ec_cert', false);
        $response->assertSee('btn_remove_ec_cert', false);
        $response->assertSee('row-dragover', false);
    }

    public function test_ec_certificate_show_renders_interactive_dossier_card(): void
    {
        $enviroProject = EnvironmentProject::create([
            'project_code' => 'EC-PRJ-' . rand(1000, 9999),
            'project_name' => 'Salem Quarry EC Dossier',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'category'     => 'B2',
            'status'       => 'approved',
        ]);

        $cert = EcCertificate::create([
            'customer_id'            => $this->customer->id,
            'environment_project_id' => $enviroProject->id,
            'ec_ref_no'              => 'SEIAA-TN/EC/2026/' . rand(1000, 9999),
            'applicant_name'         => $this->customer->customer_name,
            'status'                 => 'active',
            'issue_date'             => now()->format('Y-m-d'),
            'expiry_date'            => now()->addYears(5)->format('Y-m-d'),
            'validity_years'         => 5,
        ]);

        $response = $this->get(route('ec-certificate.show', $cert->id));
        $response->assertStatus(200);
        $response->assertSee('ec_show_cert_card', false);
        $response->assertSee('ec-show-file-input', false);
        $response->assertSee('row-dragover', false);
        $response->assertSee('btn-view-item', false);
    }
}
