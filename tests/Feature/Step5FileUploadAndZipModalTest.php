<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\LeaseDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Step5FileUploadAndZipModalTest extends TestCase
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
            'customer_name'            => 'Arunachalam ' . $rand,
            'company_name'             => 'Kongu Granites ' . $rand,
            'mobile_num'               => '98401' . $rand,
            'secondary_mobile_num'     => '98402' . $rand,
            'secondary_contact_person' => 'P. Velu',
            'email'                    => 'kongu' . $rand . '@gtmstest.in',
            'mimas_no'                 => 'TN-MMS-TST-' . $rand,
            'district_id'              => $this->district->id,
            'status'                   => 1,
        ]);
    }

    /**
     * Test Step 5 view renders dedicated per-row file inputs and zip download modal.
     */
    public function test_step5_view_renders_per_row_file_inputs_and_zip_modal(): void
    {
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => 'LA-TEST-' . rand(1000, 9999),
                'district_id' => $this->district->id,
                'current_step' => 4,
            ]
        ]);

        $response = $this->get(route('step5'));

        $response->assertStatus(200);
        $response->assertSee('row-file-input', false);
        $response->assertSee('modalZipFilePreview', false);
        $response->assertSee('.zip', false);
        $response->assertSee('.mp4', false);
        $response->assertSee('.docx', false);
    }

    /**
     * Test direct upload to slot 5 correctly sets slot 5 without affecting slot 1.
     */
    public function test_step5_upload_to_slot_5_does_not_affect_slot_1(): void
    {
        $appNo = 'LA-SLOT-' . rand(1000, 9999);
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => $appNo,
                'district_id' => $this->district->id,
                'current_step' => 4,
                'uploaded_docs' => [],
            ]
        ]);

        $file = UploadedFile::fake()->create('land_patta_slot5.pdf', 300, 'application/pdf');

        $response = $this->postJson(route('step5.upload'), [
            'file'         => $file,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'doc_item' => 5,
        ]);

        $uploadedDocs = session('lease_draft.uploaded_docs', []);
        $this->assertArrayHasKey(5, $uploadedDocs, 'Slot 5 must exist in session uploaded_docs');
        $this->assertArrayNotHasKey(1, $uploadedDocs, 'Slot 1 must not be populated when uploading to slot 5');
        $this->assertStringContainsString('land_patta_slot5.pdf', $uploadedDocs[5]['file_name']);
    }

    /**
     * Test Step 5 accepts ZIP compressed archives.
     */
    public function test_step5_upload_zip_archive_succeeds(): void
    {
        $appNo = 'LA-ZIP-' . rand(1000, 9999);
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => $appNo,
                'district_id' => $this->district->id,
                'current_step' => 4,
                'uploaded_docs' => [],
            ]
        ]);

        $file = UploadedFile::fake()->create('statutory_docs_bundle.zip', 1200, 'application/zip');

        $response = $this->postJson(route('step5.upload'), [
            'file'         => $file,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'doc_item' => 5,
        ]);

        $uploadedDocs = session('lease_draft.uploaded_docs', []);
        $this->assertEquals('statutory_docs_bundle.zip', $uploadedDocs[5]['file_name']);
    }

    /**
     * Test Step 5 accepts varied multimedia, CAD, and office extensions (MP4, DOCX, PPTX, KML).
     */
    public function test_step5_upload_varied_supported_extensions(): void
    {
        $extensionsToTest = [
            'drone_aerial.mp4'   => 'video/mp4',
            'legal_opinion.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'presentation.pptx'  => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'boundary_pts.kml'   => 'application/vnd.google-earth.kml+xml',
        ];

        $appNo = 'LA-MULTI-' . rand(1000, 9999);
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => $appNo,
                'district_id' => $this->district->id,
                'current_step' => 4,
                'uploaded_docs' => [],
            ]
        ]);

        $slot = 6;
        foreach ($extensionsToTest as $fileName => $mimeType) {
            $file = UploadedFile::fake()->create($fileName, 400, $mimeType);

            $response = $this->postJson(route('step5.upload'), [
                'file'         => $file,
                'doc_item'     => $slot,
                'folder_id'    => 7,
                'is_mandatory' => 0,
            ]);

            $response->assertStatus(200);
            $response->assertJson([
                'status'   => 1,
                'doc_item' => $slot,
            ]);

            $uploadedDocs = session('lease_draft.uploaded_docs', []);
            $this->assertArrayHasKey($slot, $uploadedDocs);
            $this->assertEquals($fileName, $uploadedDocs[$slot]['file_name']);
            $slot++;
        }
    }

    /**
     * Test Step 5 rejects non-allowed dangerous executables like .exe.
     */
    public function test_step5_upload_unsupported_extension_is_rejected(): void
    {
        $appNo = 'LA-REJECT-' . rand(1000, 9999);
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => $appNo,
                'district_id' => $this->district->id,
                'current_step' => 4,
                'uploaded_docs' => [],
            ]
        ]);

        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

        $response = $this->postJson(route('step5.upload'), [
            'file'         => $file,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['file']);
    }

    /**
     * Test View Application page renders ZIP modal and identifies ZIP files.
     */
    public function test_viewapplication_renders_zip_modal_and_data_attributes(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $rand = rand(1000, 9999);
        $application = LeaseApplication::create([
            'application_no' => 'LA-VIEW-' . $rand,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'submitted',
            'current_step'   => 5,
        ]);

        LeaseDocument::create([
            'lease_application_id' => $application->id,
            'folder_id'            => 7,
            'document_field_id'    => 5,
            'document_name'        => 'Geological Field Package',
            'file_name'            => 'geological_package.zip',
            'file_path'            => 'uploads/lease_applications/LA-VIEW-' . $rand . '/geological_package.zip',
            'file_type'            => 'application/zip',
            'file_size'            => 2048576, // 2MB
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        $response = $this->get(route('viewapplication', ['id' => $application->id]));

        $response->assertStatus(200);
        $response->assertSee('modalZipFilePreview', false);
        $response->assertSee('data-is-zip="1"', false);
        $response->assertSee('View ZIP', false);
        $response->assertSee('geological_package.zip', false);
        $response->assertSee('Download ZIP to Your PC', false);
    }

    /**
     * Test upload docx with long mime type and long file name persists without truncation error.
     */
    public function test_step5_upload_docx_with_long_mimetype_and_long_name_persists_without_truncation(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-LONG-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'draft',
            'current_step'   => 4,
        ]);

        session([
            'lease_draft' => [
                'application_id' => $leaseApp->id,
                'customer_id'    => $this->customer->id,
                'application_no' => $appNo,
                'district_id'    => $this->district->id,
                'current_step'   => 4,
                'uploaded_docs'  => [],
            ]
        ]);

        $longFileName = 'New_Fortune_Website_Scope_Separate_Large_Images.docx';
        $longMimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        $file = UploadedFile::fake()->create($longFileName, 1300, $longMimeType);

        $response = $this->postJson(route('step5.upload'), [
            'file'         => $file,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'doc_item' => 5,
        ]);

        $savedDoc = LeaseDocument::where('lease_application_id', $leaseApp->id)
            ->where('document_field_id', 5)
            ->first();

        $this->assertNotNull($savedDoc, 'LeaseDocument must be saved in database');
        $this->assertEquals($longFileName, $savedDoc->file_name);
        $this->assertEquals($longMimeType, $savedDoc->file_type);
    }

    /**
     * Test removing an uploaded document removes it from session and database.
     */
    public function test_step5_remove_document_clears_session_and_database(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-REM-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'draft',
            'current_step'   => 5,
        ]);

        $dbDoc = LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_field_id'    => 5,
            'document_name'        => '5. Work Order',
            'file_name'            => 'work_order_2026.pdf',
            'file_path'            => 'uploads/lease_applications/' . $appNo . '/work_order_2026.pdf',
            'file_type'            => 'application/pdf',
            'file_size'            => 500000,
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        session([
            'lease_draft' => [
                'application_id' => $leaseApp->id,
                'customer_id'    => $this->customer->id,
                'application_no' => $appNo,
                'district_id'    => $this->district->id,
                'current_step'   => 5,
                'uploaded_docs'  => [
                    5 => [
                        'doc_name'    => '5. Work Order',
                        'file_name'   => 'work_order_2026.pdf',
                        'file_size'   => 500000,
                        'file_type'   => 'application/pdf',
                        'folder_id'   => 7,
                        'draft_path'  => 'uploads/lease_applications/' . $appNo . '/work_order_2026.pdf',
                        'uploaded_at' => now()->format('d M Y, h:i A'),
                    ]
                ],
            ]
        ]);

        $response = $this->postJson(route('step5.remove'), [
            'doc_item'  => 5,
            'folder_id' => 7,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'doc_item' => 5,
        ]);

        $uploadedDocs = session('lease_draft.uploaded_docs', []);
        $this->assertArrayNotHasKey(5, $uploadedDocs, 'Slot 5 must be removed from session');

        $docExists = LeaseDocument::where('lease_application_id', $leaseApp->id)
            ->where('document_field_id', 5)
            ->exists();
        $this->assertFalse($docExists, 'LeaseDocument row in database must be deleted');
    }

    /**
     * Test page reload on Step 5 restores uploaded files including large files.
     */
    public function test_step5_page_reload_restores_uploaded_documents_from_database(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-RELOAD-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'draft',
            'current_step'   => 5,
        ]);

        LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_field_id'    => 5,
            'document_name'        => '5. Work Order',
            'file_name'            => 'drone_survey_400mb_video.mp4',
            'file_path'            => 'uploads/lease_applications/' . $appNo . '/drone_survey_400mb_video.mp4',
            'file_type'            => 'video/mp4',
            'file_size'            => 419430400, // 400 MB
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        // Clear session uploaded_docs to simulate page refresh / new browser session
        session([
            'lease_draft' => [
                'application_id' => $leaseApp->id,
                'customer_id'    => $this->customer->id,
                'application_no' => $appNo,
                'district_id'    => $this->district->id,
                'current_step'   => 5,
                'uploaded_docs'  => [],
            ]
        ]);

        $response = $this->get(route('step5'));

        $response->assertStatus(200);
        $response->assertSee('drone_survey_400mb_video.mp4', false);
        $response->assertSee('400 MB', false);
        $response->assertSee('btn-remove-item', false);
    }

    /**
     * Test Step 5 renders compact icon buttons without text clutter and with zero duplicate remove buttons.
     */
    public function test_step5_renders_compact_icon_buttons_and_no_duplicate_remove_buttons(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-ICONS-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'draft',
            'current_step'   => 5,
        ]);

        // Create standard docs in Folder 7, 8, 9
        LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_field_id'    => 1,
            'document_name'        => '1. Land Document',
            'file_name'            => 'land_doc.pdf',
            'file_path'            => 'uploads/lease_applications/' . $appNo . '/land_doc.pdf',
            'file_type'            => 'application/pdf',
            'file_size'            => 1048576,
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_field_id'    => 5,
            'document_name'        => '5. Work Order',
            'file_name'            => 'work_order.docx',
            'file_path'            => 'uploads/lease_applications/' . $appNo . '/work_order.docx',
            'file_type'            => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'file_size'            => 2048576,
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 8,
            'document_field_id'    => 10,
            'document_name'        => '10. Lease application – signed, FMB, Plan',
            'file_name'            => 'signed_app.zip',
            'file_path'            => 'uploads/lease_applications/' . $appNo . '/signed_app.zip',
            'file_type'            => 'application/zip',
            'file_size'            => 3145728,
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        session([
            'lease_draft' => [
                'application_id' => $leaseApp->id,
                'customer_id'    => $this->customer->id,
                'application_no' => $appNo,
                'district_id'    => $this->district->id,
                'current_step'   => 5,
                'uploaded_docs'  => [],
            ]
        ]);

        $response = $this->get(route('step5'));
        $response->assertStatus(200);

        $html = $response->getContent();

        // 1. Assert icon classes exist
        $this->assertStringContainsString('bi-trash3', $html);
        $this->assertStringContainsString('bi-arrow-repeat', $html);
        $this->assertStringContainsString('bi-eye', $html);
        $this->assertStringContainsString('bi-file-earmark-zip', $html);

        // 2. Parse HTML and ensure each row has at most ONE remove button
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//div[contains(@class, "checklist-row")]');
        $this->assertGreaterThan(0, $rows->length);

        foreach ($rows as $row) {
            $removeBtns = $xpath->query('.//button[contains(@class, "btn-remove-item")]', $row);
            $this->assertLessThanOrEqual(1, $removeBtns->length, 'Row must never contain more than 1 remove button!');

            $uploadedBadges = $xpath->query('.//span[contains(@class, "badge-status") and contains(@class, "uploaded")]', $row);
            if ($uploadedBadges->length > 0) {
                $this->assertEquals(1, $removeBtns->length, 'Uploaded row must have exactly 1 remove button');
                $uploadBtns = $xpath->query('.//button[contains(@class, "btn-upload-item")]', $row);
                $this->assertEquals(1, $uploadBtns->length, 'Uploaded row must have 1 change button');
                $viewBtns = $xpath->query('.//a[contains(@class, "btn-view-item")]', $row);
                $this->assertEquals(1, $viewBtns->length, 'Uploaded row must have 1 view button');
            }
        }
    }

    /**
     * Test re-uploading / changing a document deletes the old file from disk and updates DB & session.
     */
    public function test_step5_reupload_deletes_old_file_and_updates_database(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-REUP-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'draft',
            'current_step'   => 5,
        ]);

        session([
            'lease_draft' => [
                'application_id' => $leaseApp->id,
                'customer_id'    => $this->customer->id,
                'application_no' => $appNo,
                'district_id'    => $this->district->id,
                'current_step'   => 5,
                'uploaded_docs'  => [],
            ]
        ]);

        // 1. Initial upload (Slot 5: Work Order)
        $file1 = UploadedFile::fake()->create('initial_document_v1.pdf', 500, 'application/pdf');
        $res1 = $this->post(route('step5.upload'), [
            'file'         => $file1,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 0,
        ]);
        $res1->assertStatus(200);
        $res1->assertJson(['status' => 1, 'file_name' => 'initial_document_v1.pdf']);

        $path1 = public_path('uploads/lease_applications/' . $appNo . '/initial_document_v1.pdf');
        $this->assertFileExists($path1, 'Initial uploaded file must exist on disk');

        // 2. Change / Re-upload with a different file (updated_document_v2.docx)
        $file2 = UploadedFile::fake()->create('updated_document_v2.docx', 800, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $res2 = $this->post(route('step5.upload'), [
            'file'         => $file2,
            'doc_item'     => 5,
            'folder_id'    => 7,
            'is_mandatory' => 0,
        ]);
        $res2->assertStatus(200);
        $res2->assertJson(['status' => 1, 'file_name' => 'updated_document_v2.docx']);

        $path2 = public_path('uploads/lease_applications/' . $appNo . '/updated_document_v2.docx');
        $this->assertFileExists($path2, 'New replaced file must exist on disk');

        // 3. Old file MUST be deleted from disk
        $this->assertFileDoesNotExist($path1, 'Old file must be deleted from disk to prevent storage waste');

        // 4. Database record must be updated to new file
        $dbDoc = LeaseDocument::where('lease_application_id', $leaseApp->id)->where('document_field_id', 5)->first();
        $this->assertNotNull($dbDoc);
        $this->assertEquals('updated_document_v2.docx', $dbDoc->file_name);

        // 5. Clean up disk test files
        if (file_exists($path2)) {
            @unlink($path2);
        }
        $dir = public_path('uploads/lease_applications/' . $appNo);
        if (is_dir($dir)) {
            @rmdir($dir);
        }
    }

    /**
     * Test direct in-place dossier document upload saves file and updates document status.
     */
    public function test_dossier_direct_upload_saves_file_and_updates_doc_status(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-DOSSUPL-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'submitted',
            'current_step'   => 8,
        ]);

        $doc = LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_name'        => 'Adangal & A-register',
            'document_field_id'    => 3,
            'is_mandatory'         => 1,
            'status'               => 'pending',
        ]);

        $file = UploadedFile::fake()->create('adangal_direct.pdf', 400, 'application/pdf');

        $response = $this->postJson(route('application.document.upload', ['id' => $doc->id]), [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 1,
            'file_name' => 'adangal_direct.pdf',
        ]);

        $doc->refresh();
        $this->assertEquals('adangal_direct.pdf', $doc->file_name);
        $this->assertEquals('uploaded', $doc->status);
        $this->assertNotNull($doc->file_path);

        $savedPath = public_path($doc->file_path);
        $this->assertFileExists($savedPath);

        // Clean up
        if (file_exists($savedPath)) {
            @unlink($savedPath);
        }
        $dir = public_path('uploads/lease_applications/' . $appNo);
        if (is_dir($dir)) {
            @rmdir($dir);
        }
    }

    /**
     * Test Step 5 opened for a submitted application renders in Document Vault mode.
     */
    public function test_step5_for_submitted_dossier_renders_vault_mode_and_returns_to_dossier(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-VAULT-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'submitted',
            'current_step'   => 8,
        ]);

        $response = $this->get(route('step5', ['id' => $leaseApp->id]));

        $response->assertStatus(200);
        $response->assertSee('Document Vault', false);
        $response->assertSee('Return to Dossier', false);
        $response->assertDontSee('sp-step done', false); // Wizard steps hidden in vault mode
    }

    /**
     * Test viewapplication page contains in-place document upload inputs.
     */
    public function test_viewapplication_renders_inline_document_upload_inputs(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-INLINE-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'submitted',
            'current_step'   => 8,
        ]);

        $doc = LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 7,
            'document_name'        => 'Patta Document',
            'document_field_id'    => 4,
            'is_mandatory'         => 1,
            'status'               => 'pending',
        ]);

        $response = $this->get(route('viewapplication', ['id' => $leaseApp->id]));

        $response->assertStatus(200);
        $response->assertSee('input-dossier-file', false);
        $response->assertSee('data-upload-url', false);
    }

    /**
     * Test dossier upload via assembled chunked upload token.
     */
    public function test_dossier_chunked_upload_token_saves_file_and_updates_doc_status(): void
    {
        $mineral = \App\Models\Mineral::first() ?? \App\Models\Mineral::create(['name' => 'Rough Stone', 'mineral_type' => 'minor', 'status' => 1]);
        $appNo = 'LA-CHUNK-' . rand(1000, 9999);
        $leaseApp = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'submitted',
            'current_step'   => 8,
        ]);

        $doc = LeaseDocument::create([
            'lease_application_id' => $leaseApp->id,
            'folder_id'            => 9,
            'document_name'        => 'Drone Survey Video',
            'document_field_id'    => 18,
            'is_mandatory'         => 0,
            'status'               => 'pending',
        ]);

        $uploadDir = public_path('uploads/lease_applications/' . $appNo);
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $relFilePath = 'uploads/lease_applications/' . $appNo . '/drone_video_2026.mp4';
        file_put_contents(public_path($relFilePath), 'fake video binary data for chunk test');

        $token = 'chk_test_' . uniqid();
        $chunkedUpload = \App\Models\ChunkedUpload::create([
            'upload_token'    => $token,
            'original_name'   => 'drone_video_2026.mp4',
            'file_name'       => 'drone_video_2026.mp4',
            'file_path'       => $relFilePath,
            'mime_type'       => 'video/mp4',
            'total_size'      => 45000000,
            'chunk_size'      => 10485760,
            'total_chunks'    => 5,
            'uploaded_chunks' => 5,
            'status'          => 'completed',
        ]);

        $response = $this->postJson(route('application.document.upload', ['id' => $doc->id]), [
            'upload_token' => $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'    => 1,
            'file_name' => 'drone_video_2026.mp4',
        ]);

        $doc->refresh();
        $this->assertEquals('drone_video_2026.mp4', $doc->file_name);
        $this->assertEquals('uploaded', $doc->status);
        $this->assertEquals($relFilePath, $doc->file_path);

        // Clean up
        if (file_exists(public_path($relFilePath))) {
            @unlink(public_path($relFilePath));
        }
        if (is_dir($uploadDir)) {
            @rmdir($uploadDir);
        }
        $chunkedUpload->delete();
    }

    /**
     * Test GTMS Universal Open With modal and dispatcher script are rendered across pages.
     */
    public function test_gtms_universal_open_with_modal_and_js_rendered(): void
    {
        session([
            'lease_draft' => [
                'customer_id' => $this->customer->id,
                'application_no' => 'LA-OPENWITH-' . rand(1000, 9999),
                'district_id' => $this->district->id,
                'current_step' => 4,
            ]
        ]);

        $response = $this->get(route('step5'));

        $response->assertStatus(200);
        $response->assertSee('gtmsOpenWithModal', false);
        $response->assertSee('gtmsOpenWithOptionsList', false);
        $response->assertSee('gtmsOpenWithDirectDownload', false);
        $response->assertSee('gtms-open-with.js', false);
    }
}
