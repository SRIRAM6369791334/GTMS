<?php

namespace Tests\Feature;

use App\Jobs\ProcessUploadedMediaJob;
use App\Models\Branch;
use App\Models\ChunkedUpload;
use App\Models\ChunkedUploadPart;
use App\Models\Customer;
use App\Models\DgpsDocument;
use App\Models\DgpsSurvey;
use App\Models\DroneDocument;
use App\Models\DroneSurvey;
use App\Models\Folder;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ChunkedUploadPipelineTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Branch $branch;
    protected Customer $customer;
    protected DroneSurvey $droneSurvey;
    protected DgpsSurvey $dgpsSurvey;
    protected Module $module;
    protected Folder $folder;

    /**
     * @var array<string>
     */
    protected array $createdTokens = [];

    /**
     * @var array<string>
     */
    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Ensure required Spatie permissions exist
        foreach (['dgps.edit', 'dgps.create', 'drone.edit', 'drone.create', 'drone.view'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // 2. Set up default Branch
        $this->branch = Branch::first() ?? Branch::create([
            'branch_name'    => 'Salem Head Office',
            'contact_person' => 'Admin Officer',
            'mobile'         => '9443212345',
            'city'           => 'Salem',
            'state'          => 'Tamil Nadu',
            'status'         => 1,
        ]);

        // 3. Set up Admin User
        $this->admin = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'Super Admin']))
            ->orWhere('role_id', 1)
            ->first();

        if (!$this->admin) {
            $this->admin = User::firstOrCreate(
                ['email' => 'admin_test@gtms.com'],
                [
                    'name'          => 'Admin Test User',
                    'user_code'     => 'ADM_' . rand(100, 999),
                    'password'      => bcrypt('password123'),
                    'show_password' => 'password123',
                    'branch_id'     => $this->branch->id,
                    'role_id'       => $adminRole->id,
                    'status'        => 1,
                ]
            );
        }

        if (!$this->admin->hasRole(['Admin', 'Super Admin'])) {
            $this->admin->assignRole($adminRole);
        }

        // 4. Set up Customer
        $this->customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Sri Krishna Quarries',
            'company_name'  => 'Sri Krishna Granites Pvt Ltd',
            'mobile_num'    => '9842100000',
            'status'        => 1,
            'branch_id'     => $this->branch->id,
        ]);

        // 5. Set up DroneSurvey & DgpsSurvey
        $this->droneSurvey = DroneSurvey::create([
            'survey_no'     => 'DRN-TEST-' . rand(10000, 99999),
            'customer_id'   => $this->customer->id,
            'location'      => 'Salem Quarry Sector 4',
            'lease_area'    => 4.50,
            'survey_status' => 'scheduled',
            'branch_id'     => $this->branch->id,
        ]);

        $this->dgpsSurvey = DgpsSurvey::create([
            'survey_no'     => 'DGPS-TEST-' . rand(10000, 99999),
            'customer_id'   => $this->customer->id,
            'location'      => 'Salem Demarcation Sector 4',
            'survey_status' => 'completed',
            'report_status' => 'verified',
            'branch_id'     => $this->branch->id,
        ]);

        // 6. Set up Module & Folder
        $this->module = Module::first() ?? Module::create([
            'code'   => 'SURVEY',
            'name'   => 'Surveys',
            'status' => 1,
        ]);

        $this->folder = Folder::first() ?? Folder::create([
            'module_id'  => $this->module->id,
            'name'       => 'Survey Reports',
            'sort_order' => 1,
            'status'     => 1,
        ]);
    }

    protected function tearDown(): void
    {
        // Purge any temporary test chunk directories
        foreach ($this->createdTokens as $token) {
            $chunkDir = storage_path('app/chunks/' . $token);
            if (File::isDirectory($chunkDir)) {
                File::deleteDirectory($chunkDir);
            }
        }

        // Clean up assembled files created in public uploads
        foreach ($this->createdFiles as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        parent::tearDown();
    }

    /**
     * Helper to initialize a test upload session.
     */
    protected function initTestUpload(
        int $totalChunks = 3,
        int $totalSize = 3072,
        int $chunkSize = 1024,
        ?string $fileHash = null,
        string $targetModule = 'drone',
        ?int $referenceId = null,
        string $fileName = 'survey_flight.mp4'
    ): string {
        $payload = [
            'file_name'     => $fileName,
            'total_size'    => $totalSize,
            'chunk_size'    => $chunkSize,
            'total_chunks'  => $totalChunks,
            'mime_type'     => 'video/mp4',
            'target_module' => $targetModule,
            'reference_id'  => $referenceId ?? $this->droneSurvey->id,
            'document_name' => 'Flight Capture Video',
        ];

        if ($fileHash !== null) {
            $payload['file_hash'] = $fileHash;
        }

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/init', $payload);
        $response->assertStatus(201);

        $token = $response->json('upload_token');
        $this->createdTokens[] = $token;

        return $token;
    }

    /**
     * Helper to create a pre-assembled upload record and file.
     */
    protected function createAssembledUpload(string $module, int $referenceId, string $fileName = 'test_document.pdf'): string
    {
        $token = 'upl_test_' . Str::random(24);
        $this->createdTokens[] = $token;

        $subDir = 'uploads/' . $module . '/REF-' . $referenceId;
        $destDir = public_path($subDir);
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0775, true, true);
        }

        $finalName = time() . '_' . Str::random(6) . '_' . $fileName;
        $filePath = $destDir . DIRECTORY_SEPARATOR . $finalName;
        $relativeFilePath = $subDir . '/' . $finalName;
        $this->createdFiles[] = $filePath;

        $content = "TEST_ASSEMBLED_FILE_CONTENT_FOR_{$module}_{$referenceId}";
        file_put_contents($filePath, $content);

        $checksum = hash_file('sha256', $filePath);

        ChunkedUpload::create([
            'upload_token'    => $token,
            'user_id'         => $this->admin->id,
            'file_name'       => $fileName,
            'original_name'   => $fileName,
            'mime_type'       => 'application/octet-stream',
            'total_size'      => strlen($content),
            'chunk_size'      => 1048576,
            'total_chunks'    => 1,
            'uploaded_chunks' => 1,
            'target_module'   => $module,
            'reference_id'    => $referenceId,
            'file_path'       => $relativeFilePath,
            'checksum_sha256' => $checksum,
            'status'          => 'assembled',
        ]);

        return $token;
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 1: INITIALIZE CHUNKED UPLOAD SESSION
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 1: POST /api/upload/chunk/init returns token, status 201, creates DB record.
     */
    public function test_can_initialize_chunked_upload_session(): void
    {
        $payload = [
            'file_name'        => 'salem_drone_survey_4k.mp4',
            'total_size'       => 31457280, // 30 MB
            'chunk_size'       => 10485760, // 10 MB
            'total_chunks'     => 3,
            'mime_type'        => 'video/mp4',
            'target_module'    => 'drone',
            'reference_id'     => $this->droneSurvey->id,
            'document_name'    => 'Drone Survey Flight 01',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/init', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'success'      => true,
            'chunk_size'   => 10485760,
            'total_chunks' => 3,
            'status'       => 'pending',
        ]);

        $token = $response->json('upload_token');
        $this->assertNotEmpty($token);
        $this->createdTokens[] = $token;

        $this->assertDatabaseHas('chunked_uploads', [
            'upload_token'  => $token,
            'total_chunks'  => 3,
            'total_size'    => 31457280,
            'target_module' => 'drone',
            'reference_id'  => $this->droneSurvey->id,
            'status'        => 'pending',
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 2: UPLOAD INDIVIDUAL CHUNKS
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 2: POST /api/upload/chunk/upload with UploadedFile chunk binary, verifies chunk is stored and record updated.
     */
    public function test_can_upload_individual_chunks(): void
    {
        $token = $this->initTestUpload(totalChunks: 2, totalSize: 2048, chunkSize: 1024);

        $chunkContent = str_repeat('A', 1024);
        $chunkHash = hash('sha256', $chunkContent);
        $file = UploadedFile::fake()->createWithContent('chunk_0.part', $chunkContent);

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk_hash'   => $chunkHash,
            'chunk'        => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'               => true,
            'upload_token'          => $token,
            'chunk_index'           => 0,
            'uploaded_chunks_count' => 1,
            'total_chunks'          => 2,
            'is_complete'           => false,
            'status'                => 'uploading',
        ]);

        $partPath = storage_path("app/chunks/{$token}/chunk_0.part");
        $this->assertFileExists($partPath);
        $this->assertEquals($chunkContent, file_get_contents($partPath));

        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();
        $this->assertEquals(1, $upload->uploaded_chunks);
        $this->assertEquals('uploading', $upload->status);

        $this->assertDatabaseHas('chunked_upload_parts', [
            'chunked_upload_id' => $upload->id,
            'chunk_index'       => 0,
            'is_uploaded'       => true,
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 3: QUERY UPLOAD STATUS & RESUME CHECKPOINTS
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 3: Simulate network drop where chunk 0 and 2 are uploaded but chunk 1 is missing.
     * Verify /api/upload/chunk/status reports uploaded: [0, 2], missing: [1], progress: 66.67%.
     */
    public function test_can_query_upload_status_and_resume_checkpoints(): void
    {
        $token = $this->initTestUpload(totalChunks: 3, totalSize: 3072, chunkSize: 1024);

        // 1. Upload chunk 0
        $chunk0 = str_repeat('A', 1024);
        $file0 = UploadedFile::fake()->createWithContent('chunk_0.part', $chunk0);
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => $file0,
        ])->assertStatus(200);

        // 2. Simulate dropped network on chunk 1; client uploads chunk 2
        $chunk2 = str_repeat('C', 1024);
        $file2 = UploadedFile::fake()->createWithContent('chunk_2.part', $chunk2);
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 2,
            'chunk'        => $file2,
        ])->assertStatus(200);

        // 3. Query status endpoint for resumption
        $response = $this->actingAs($this->admin)->getJson("/api/upload/chunk/status?upload_token={$token}");

        $response->assertStatus(200);
        $response->assertJson([
            'success'               => true,
            'upload_token'          => $token,
            'total_chunks'          => 3,
            'uploaded_chunks_count' => 2,
            'uploaded_chunks'       => [0, 2],
            'missing_chunks'        => [1],
            'progress_percent'      => 66.67,
            'is_complete'           => false,
            'status'                => 'uploading',
        ]);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 4: ASSEMBLE CHUNKS & VERIFY SHA-256 INTEGRITY
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 4: Upload missing chunk 1, call /api/upload/chunk/assemble.
     * Verify concatenated file exists on disk, content matches binary concatenation,
     * and computed SHA-256 matches actual hash_file('sha256', ...).
     */
    public function test_can_assemble_chunks_and_verify_sha256_integrity(): void
    {
        $chunk0Data = str_repeat('X', 1024);
        $chunk1Data = str_repeat('Y', 1024);
        $chunk2Data = str_repeat('Z', 1024);
        $fullContent = $chunk0Data . $chunk1Data . $chunk2Data;
        $expectedSha256 = hash('sha256', $fullContent);

        $token = $this->initTestUpload(
            totalChunks: 3,
            totalSize: strlen($fullContent),
            chunkSize: 1024,
            fileHash: $expectedSha256,
            targetModule: 'drone',
            referenceId: $this->droneSurvey->id
        );

        // Upload chunk 0
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', $chunk0Data),
        ])->assertStatus(200);

        // Upload chunk 2 first (out-of-order)
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 2,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_2.part', $chunk2Data),
        ])->assertStatus(200);

        // Upload missing chunk 1 to complete set
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 1,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_1.part', $chunk1Data),
        ])->assertStatus(200);

        // Verify status confirms completion
        $statusResponse = $this->actingAs($this->admin)->getJson("/api/upload/chunk/status?upload_token={$token}");
        $statusResponse->assertJson(['is_complete' => true, 'uploaded_chunks_count' => 3]);

        // Assemble chunks
        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/assemble', [
            'upload_token'  => $token,
            'target_module' => 'drone',
            'reference_id'  => $this->droneSurvey->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'         => true,
            'upload_token'    => $token,
            'checksum_sha256' => $expectedSha256,
        ]);

        // Verify assembled destination file exists on disk
        $relativeFilePath = $response->json('file_path');
        $this->assertNotEmpty($relativeFilePath);
        $fullPath = public_path($relativeFilePath);
        $this->createdFiles[] = $fullPath;

        $this->assertFileExists($fullPath);
        $this->assertEquals($fullContent, file_get_contents($fullPath));
        $this->assertEquals($expectedSha256, hash_file('sha256', $fullPath));

        // Verify temporary part files were immediately unlinked
        $chunkDir = storage_path("app/chunks/{$token}");
        $this->assertFalse(file_exists($chunkDir));
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 5: DISPATCH BACKGROUND QUEUE JOB UPON ASSEMBLY
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 5: Using Queue::fake(), verify ProcessUploadedMediaJob is dispatched upon assembly.
     */
    public function test_assembled_upload_dispatches_background_queue_job(): void
    {
        Queue::fake();

        $chunk0Data = str_repeat('P', 1024);
        $chunk1Data = str_repeat('Q', 1024);
        $fullContent = $chunk0Data . $chunk1Data;

        $token = $this->initTestUpload(
            totalChunks: 2,
            totalSize: strlen($fullContent),
            chunkSize: 1024,
            targetModule: 'drone',
            referenceId: $this->droneSurvey->id
        );

        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', $chunk0Data),
        ])->assertStatus(200);

        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 1,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_1.part', $chunk1Data),
        ])->assertStatus(200);

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/assemble', [
            'upload_token' => $token,
        ]);

        $response->assertStatus(200);
        $this->createdFiles[] = public_path($response->json('file_path'));

        Queue::assertPushed(ProcessUploadedMediaJob::class, function ($job) use ($token) {
            if ($job->upload instanceof ChunkedUpload) {
                return $job->upload->upload_token === $token;
            }
            $upload = ChunkedUpload::find($job->upload);
            return $upload && $upload->upload_token === $token;
        });
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 6: DGPS CONTROLLER ACCEPTS CHUNKED UPLOAD TOKEN
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 6: Call DgpsSurveyController@uploadDocument passing upload_token.
     * Verify DgpsDocument record is created without calling non-existent SurveyDocument.
     */
    public function test_dgps_survey_controller_accepts_chunked_upload_token(): void
    {
        $token = $this->createAssembledUpload(
            module: 'dgps',
            referenceId: $this->dgpsSurvey->id,
            fileName: 'dgps_cadastral_survey.dwg'
        );

        $response = $this->actingAs($this->admin)->postJson(route('dgps-survey.upload'), [
            'upload_token'  => $token,
            'survey_id'     => $this->dgpsSurvey->id,
            'folder_id'     => $this->folder->id,
            'document_name' => 'DGPS Cadastral Boundary Plan',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'upload_token' => $token,
            'file_name'    => 'dgps_cadastral_survey.dwg',
            'status'       => 'uploaded',
        ]);

        $this->assertDatabaseHas('dgps_documents', [
            'dgps_survey_id' => $this->dgpsSurvey->id,
            'folder_id'      => $this->folder->id,
            'document_name'  => 'DGPS Cadastral Boundary Plan',
            'status'         => 'uploaded',
        ]);

        $doc = DgpsDocument::where('dgps_survey_id', $this->dgpsSurvey->id)
            ->where('document_name', 'DGPS Cadastral Boundary Plan')
            ->first();
        $this->assertNotNull($doc);
        $this->assertNotNull($doc->file_path);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 7: DRONE CONTROLLER ACCEPTS CHUNKED UPLOAD TOKEN
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 7: Call DroneSurveyController@uploadDocument passing upload_token.
     * Verify DroneDocument record is created.
     */
    public function test_drone_survey_controller_accepts_chunked_upload_token(): void
    {
        $token = $this->createAssembledUpload(
            module: 'drone',
            referenceId: $this->droneSurvey->id,
            fileName: 'drone_orthomosaic_highres.tif'
        );

        $response = $this->actingAs($this->admin)->postJson(route('drone-survey.upload'), [
            'upload_token'    => $token,
            'drone_survey_id' => $this->droneSurvey->id,
            'folder_id'       => $this->folder->id,
            'document_name'   => 'Drone Orthomosaic Deliverable',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'upload_token' => $token,
            'file_name'    => 'drone_orthomosaic_highres.tif',
            'status'       => 'uploaded',
        ]);

        $this->assertDatabaseHas('drone_documents', [
            'drone_survey_id' => $this->droneSurvey->id,
            'folder_id'       => $this->folder->id,
            'document_name'   => 'Drone Orthomosaic Deliverable',
            'status'          => 'uploaded',
        ]);

        $doc = DroneDocument::where('drone_survey_id', $this->droneSurvey->id)
            ->where('document_name', 'Drone Orthomosaic Deliverable')
            ->first();
        $this->assertNotNull($doc);
        $this->assertNotNull($doc->file_path);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * TEST CASE 8: CANCEL UPLOAD & PURGE TEMPORARY CHUNKS
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 8: Call /api/upload/chunk/cancel.
     * Verify chunks are purged from disk and DB status is cancelled.
     */
    public function test_can_cancel_upload_and_purge_temporary_chunks(): void
    {
        $token = $this->initTestUpload(totalChunks: 3, totalSize: 3072, chunkSize: 1024);

        // Upload 2 chunks to populate disk and parts table
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', str_repeat('X', 1024)),
        ])->assertStatus(200);

        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 1,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_1.part', str_repeat('Y', 1024)),
        ])->assertStatus(200);

        $chunkDir = storage_path("app/chunks/{$token}");
        $this->assertTrue(File::isDirectory($chunkDir));
        $this->assertFileExists($chunkDir . '/chunk_0.part');
        $this->assertFileExists($chunkDir . '/chunk_1.part');

        // Cancel upload session
        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/cancel', [
            'upload_token' => $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success'      => true,
            'upload_token' => $token,
            'status'       => 'cancelled',
        ]);

        // Verify chunks directory was completely purged from disk
        $this->assertFalse(File::isDirectory($chunkDir));

        // Verify database status is cancelled and parts are cleared
        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();
        $this->assertEquals('cancelled', $upload->status);
        $this->assertEquals(0, $upload->parts()->count());
    }

    /* ══════════════════════════════════════════════════════════════════════
     * EDGE CASE TESTS: INTEGRITY VALIDATION & PROTOCOL GUARDS
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * Test 9: Chunk upload rejects corrupted chunk hash with HTTP 400.
     */
    public function test_chunk_upload_rejects_corrupted_chunk_hash(): void
    {
        $token = $this->initTestUpload(totalChunks: 2, totalSize: 2048, chunkSize: 1024);

        $chunkContent = str_repeat('Z', 1024);
        $wrongHash = hash('sha256', 'completely_different_content');

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk_hash'   => $wrongHash,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', $chunkContent),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test 10: Assembly rejects with HTTP 409 when chunks are missing.
     */
    public function test_assemble_rejects_when_chunks_are_missing(): void
    {
        $token = $this->initTestUpload(totalChunks: 3, totalSize: 3072, chunkSize: 1024);

        // Upload only chunk 0 of 3
        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', str_repeat('A', 1024)),
        ])->assertStatus(200);

        // Attempt premature assembly
        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/assemble', [
            'upload_token' => $token,
        ]);

        $response->assertStatus(409);
        $response->assertJson(['success' => false]);
    }

    /**
     * Test 11: Assembly rejects with HTTP 422 when computed full file hash mismatches expected file_hash.
     */
    public function test_assemble_rejects_corrupted_full_file_hash(): void
    {
        $chunk0Data = str_repeat('M', 1024);
        $chunk1Data = str_repeat('N', 1024);
        $wrongMasterHash = hash('sha256', 'tampered_or_corrupted_expected_hash');

        $token = $this->initTestUpload(
            totalChunks: 2,
            totalSize: 2048,
            chunkSize: 1024,
            fileHash: $wrongMasterHash
        );

        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 0,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_0.part', $chunk0Data),
        ])->assertStatus(200);

        $this->actingAs($this->admin)->postJson('/api/upload/chunk/upload', [
            'upload_token' => $token,
            'chunk_index'  => 1,
            'chunk'        => UploadedFile::fake()->createWithContent('chunk_1.part', $chunk1Data),
        ])->assertStatus(200);

        $response = $this->actingAs($this->admin)->postJson('/api/upload/chunk/assemble', [
            'upload_token' => $token,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);

        $upload = ChunkedUpload::where('upload_token', $token)->firstOrFail();
        $this->assertEquals('failed', $upload->status);
    }

    /**
     * Test 12: Status query returns HTTP 404 for non-existent upload session.
     */
    public function test_status_returns_404_for_unknown_token(): void
    {
        $response = $this->actingAs($this->admin)->getJson('/api/upload/chunk/status?upload_token=upl_unknown_session_token_12345');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }
}
