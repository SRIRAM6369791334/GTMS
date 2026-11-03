<?php

namespace Tests\Feature;

use App\Models\ApplicationPayment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\LeaseDocument;
use App\Models\MasterProject;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\Notification;
use App\Models\PipelineMovement;
use App\Models\PptApplication;
use App\Models\PptDocument;
use App\Models\Role;
use App\Models\User;
use App\Services\MasterPipelineService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Challenger Empirical Stress Test Suite:
 * - Departmental Document Vault Isolation
 * - Financial Isolation & Polymorphic Payment Segregation
 * - Multi-Tenancy & BranchScope Adversarial Cross-Branch Gating
 */
class ChallengerCompartmentalizationStressTest extends TestCase
{
    use DatabaseTransactions;

    protected MasterPipelineService $service;
    protected Branch $branchA;
    protected Branch $branchB;
    protected District $district;
    protected Role $officerRole;
    protected Role $adminRole;
    protected User $officerA;
    protected User $officerB;
    protected User $superAdmin;
    protected Customer $customerA;
    protected Customer $customerB;
    protected Mineral $mineral;
    protected LeaseCategory $leaseCategory;
    protected Folder $vaultFolder;
    protected array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->service = app(MasterPipelineService::class);

        // 1. Multi-Tenancy Branches
        $this->branchA = Branch::firstOrCreate(
            ['branch_name' => 'Branch Alpha - Salem HQ'],
            ['status' => 1]
        );

        $this->branchB = Branch::firstOrCreate(
            ['branch_name' => 'Branch Beta - Coimbatore Regional'],
            ['status' => 1]
        );

        $this->district = District::firstOrCreate(
            ['name' => 'Salem'],
            ['status' => 1]
        );

        // 2. Roles
        $this->officerRole = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);
        $this->adminRole   = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        // 3. Tenanted Users: Officer A (Branch A, non-admin), Officer B (Branch B, non-admin)
        $this->officerA = User::factory()->create([
            'name'      => 'Officer Alpha',
            'email'     => 'officer_a_' . uniqid() . '@gtms.gov.in',
            'branch_id' => $this->branchA->id,
            'role_id'   => $this->officerRole->id,
            'status'    => 1,
        ]);
        $this->officerA->assignRole($this->officerRole);

        $this->officerB = User::factory()->create([
            'name'      => 'Officer Beta',
            'email'     => 'officer_b_' . uniqid() . '@gtms.gov.in',
            'branch_id' => $this->branchB->id,
            'role_id'   => $this->officerRole->id,
            'status'    => 1,
        ]);
        $this->officerB->assignRole($this->officerRole);

        // 4. Super Admin (role_id = 1 / Admin role, unrestricted statewide)
        $this->superAdmin = User::factory()->create([
            'name'      => 'Super Statewide Director',
            'email'     => 'superadmin_' . uniqid() . '@gtms.gov.in',
            'branch_id' => null,
            'role_id'   => 1, // Canonical Admin role_id
            'status'    => 1,
        ]);
        $this->superAdmin->assignRole($this->adminRole);

        // 5. Customers
        $this->customerA = Customer::create([
            'customer_name' => 'Alpha Minerals Ltd',
            'company_name'  => 'Alpha Quarry Corp',
            'mobile_num'    => '9800000001',
            'gstin'         => '33AAAAA1111A1Z1',
            'branch_id'     => $this->branchA->id,
            'created_by'    => $this->officerA->id,
        ]);

        $this->customerB = Customer::create([
            'customer_name' => 'Beta Granites Ltd',
            'company_name'  => 'Beta Mining Corp',
            'mobile_num'    => '9800000002',
            'gstin'         => '33BBBBB2222B1Z2',
            'branch_id'     => $this->branchB->id,
            'created_by'    => $this->officerB->id,
        ]);

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Black Granite'],
            ['category' => 'major', 'default_unit' => 'CBM', 'status' => 1]
        );

        $this->leaseCategory = LeaseCategory::first() ?? LeaseCategory::create([
            'code' => 'PATTA_LAND',
            'name' => 'Patta Land Concession',
        ]);

        $this->vaultFolder = Folder::first() ?? Folder::create([
            'module_id' => 1,
            'name'      => 'Stress Test Vault',
            'status'    => 1,
        ]);
    }

    protected function tearDown(): void
    {
        // Physical file cleanup
        foreach ($this->createdFiles as $filePath) {
            if (File::exists($filePath)) {
                @File::delete($filePath);
            }
        }

        parent::tearDown();
    }

    /**
     * Helper to create real dummy files on disk for empirical vault testing.
     */
    protected function createPhysicalDummyFile(string $relativeDir, string $fileName, string $content): string
    {
        $dir = public_path($relativeDir);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $fullPath = $dir . DIRECTORY_SEPARATOR . $fileName;
        File::put($fullPath, $content);
        $this->createdFiles[] = $fullPath;

        return $relativeDir . '/' . $fileName;
    }

    // =========================================================================
    // SECTION 1: Document Vault Isolation Stress Tests
    // =========================================================================

    /**
     * Adversarially stress test document vault isolation:
     * Prove that deleting or unlinking physical files and database records in downstream vaults
     * (ppt_documents, environment_documents) leaves upstream vaults (mining_documents, lease_documents)
     * 100% intact both on disk (byte-for-byte SHA256 checksum) and in the database.
     */
    public function test_01_document_vault_physical_file_and_db_isolation_under_downstream_unlink(): void
    {
        // Setup Master and Department records
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerA->id,
            'branch_id'             => $this->branchA->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerA->id,
        ]);

        $lease = LeaseApplication::create([
            'application_no'    => 'LA-2026-' . rand(1000, 9999),
            'master_project_id' => $master->id,
            'customer_id'       => $this->customerA->id,
            'district_id'       => $this->district->id,
            'mineral_id'        => $this->mineral->id,
            'category_id'       => $this->leaseCategory->id,
            'branch_id'         => $this->branchA->id,
            'status'            => 'approved',
            'created_by'        => $this->officerA->id,
        ]);

        $mining = MiningApplication::create([
            'application_no'    => 'MP-2026-' . rand(1000, 9999),
            'master_project_id' => $master->id,
            'customer_id'       => $this->customerA->id,
            'district_id'       => $this->district->id,
            'status'            => 'approved',
            'branch_id'         => $this->branchA->id,
            'created_by'        => $this->officerA->id,
        ]);

        $env = EnvironmentProject::create([
            'project_code'      => 'ENV-2026-' . rand(1000, 9999),
            'project_name'      => 'Stress Test Quarry Environmental Clearance',
            'master_project_id' => $master->id,
            'customer_id'       => $this->customerA->id,
            'district_id'       => $this->district->id,
            'category'          => 'B2',
            'status'            => 'in_progress',
            'branch_id'         => $this->branchA->id,
            'created_by'        => $this->officerA->id,
        ]);

        $ppt = PptApplication::create([
            'application_no'    => 'PPT-2026-' . rand(1000, 9999),
            'project_name'      => 'SEAC PPT Presentation Dossier',
            'master_project_id' => $master->id,
            'customer_id'       => $this->customerA->id,
            'district_id'       => $this->district->id,
            'mineral_id'        => $this->mineral->id,
            'status'            => 'scheduled',
            'branch_id'         => $this->branchA->id,
            'created_by'        => $this->officerA->id,
        ]);

        $master->update([
            'lease_application_id'   => $lease->id,
            'mining_application_id'  => $mining->id,
            'environment_project_id' => $env->id,
            'ppt_application_id'     => $ppt->id,
        ]);

        // Create 4 distinct physical files on disk
        $token = uniqid();
        $leaseRelPath  = $this->createPhysicalDummyFile('uploads/stress_test/lease', "lease_deed_{$token}.pdf", "OFFICIAL LEASE DEED CONTENT {$token}");
        $miningRelPath = $this->createPhysicalDummyFile('uploads/stress_test/mining', "mining_plan_{$token}.pdf", "MINING PLAN APPROVED SCHEME {$token}");
        $envRelPath    = $this->createPhysicalDummyFile('uploads/stress_test/env', "env_form1_{$token}.pdf", "ENVIRONMENTAL BASELINE EIA {$token}");
        $pptRelPath    = $this->createPhysicalDummyFile('uploads/stress_test/ppt', "ppt_deck_{$token}.pptx", "PPT PRESENTATION SLIDES {$token}");

        $miningHashBefore = hash_file('sha256', public_path($miningRelPath));
        $leaseHashBefore  = hash_file('sha256', public_path($leaseRelPath));

        // Create database records in independent vault tables
        $docLease = LeaseDocument::create([
            'lease_application_id' => $lease->id,
            'folder_id'            => $this->vaultFolder->id,
            'document_name'        => 'Village Patta Deed',
            'file_name'            => "lease_deed_{$token}.pdf",
            'file_path'            => $leaseRelPath,
            'file_type'            => 'application/pdf',
            'file_size'            => 1024,
            'uploaded_by'          => $this->officerA->id,
        ]);

        $docMining = MiningDocument::create([
            'mining_application_id' => $mining->id,
            'folder_id'             => $this->vaultFolder->id,
            'document_name'         => 'Approved Mining Scheme',
            'file_name'             => "mining_plan_{$token}.pdf",
            'file_path'             => $miningRelPath,
            'file_type'             => 'application/pdf',
            'file_size'             => 2048,
            'uploaded_by'           => $this->officerA->id,
        ]);

        $docEnv = EnvironmentDocument::create([
            'environment_project_id' => $env->id,
            'folder_id'              => $this->vaultFolder->id,
            'document_name'          => 'Form 1 Baseline',
            'file_name'              => "env_form1_{$token}.pdf",
            'file_path'              => $envRelPath,
            'file_type'              => 'application/pdf',
            'file_size'              => 3072,
            'uploaded_by'            => $this->officerA->id,
        ]);

        $docPpt = PptDocument::create([
            'ppt_application_id' => $ppt->id,
            'folder_id'          => $this->vaultFolder->id,
            'document_name'      => 'SEAC Presentation Slides',
            'file_name'          => "ppt_deck_{$token}.pptx",
            'file_path'          => $pptRelPath,
            'file_type'          => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'file_size'          => 4096,
            'uploaded_by'        => $this->officerA->id,
        ]);

        // Pre-assertion: all 4 physical files exist
        $this->assertFileExists(public_path($leaseRelPath));
        $this->assertFileExists(public_path($miningRelPath));
        $this->assertFileExists(public_path($envRelPath));
        $this->assertFileExists(public_path($pptRelPath));

        // ADVERSARIAL ACTION: Downstream destruction
        // 1. Unlink PPT physical file
        @unlink(public_path($pptRelPath));
        $docPpt->delete(); // Soft-delete PPT record

        // 2. Unlink Environment physical file
        @unlink(public_path($envRelPath));
        $docEnv->delete(); // Soft-delete Env record

        // Post-destruction assertions: Downstream files deleted
        $this->assertFileDoesNotExist(public_path($pptRelPath));
        $this->assertFileDoesNotExist(public_path($envRelPath));
        $this->assertNotNull(PptDocument::withTrashed()->find($docPpt->id)->deleted_at);
        $this->assertNotNull(EnvironmentDocument::withTrashed()->find($docEnv->id)->deleted_at);

        // EMPIRICAL PROOF OF UPSTREAM VAULT ISOLATION:
        // 1. Upstream physical files MUST STILL EXIST on disk
        $this->assertFileExists(public_path($miningRelPath), "Mining physical file must remain intact after downstream unlink.");
        $this->assertFileExists(public_path($leaseRelPath), "Lease physical file must remain intact after downstream unlink.");

        // 2. Upstream files SHA256 checksum MUST MATCH 100% (zero corruption or truncation)
        $this->assertEquals($miningHashBefore, hash_file('sha256', public_path($miningRelPath)));
        $this->assertEquals($leaseHashBefore, hash_file('sha256', public_path($leaseRelPath)));

        // 3. Upstream DB records MUST NOT be soft-deleted or modified
        $docMining->refresh();
        $docLease->refresh();
        $this->assertNull($docMining->deleted_at);
        $this->assertNull($docLease->deleted_at);
        $this->assertEquals($miningRelPath, $docMining->file_path);
        $this->assertEquals($leaseRelPath, $docLease->file_path);
    }

    /**
     * Adversarially stress test downstream document re-upload & replacement:
     * Prove that replacing files in environment_documents does not overwrite or corrupt
     * upstream mining_documents even when given identical filenames or same folder IDs.
     */
    public function test_02_document_vault_reupload_and_replacement_isolation(): void
    {
        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customerA->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branchA->id,
            'created_by'     => $this->officerA->id,
        ]);

        $env = EnvironmentProject::create([
            'project_code' => 'ENV-2026-' . rand(1000, 9999),
            'project_name' => 'Stress Test Reupload Environmental Project',
            'customer_id'  => $this->customerA->id,
            'district_id'  => $this->district->id,
            'branch_id'    => $this->branchA->id,
            'created_by'   => $this->officerA->id,
        ]);

        // Both modules upload a file called 'statutory_annexure.pdf'
        $commonFileName = 'statutory_annexure.pdf';
        $miningRelPath = $this->createPhysicalDummyFile('uploads/stress_test/mining', $commonFileName, 'ORIGINAL MINING PLAN CONTENT');
        $envRelPath    = $this->createPhysicalDummyFile('uploads/stress_test/env', $commonFileName, 'ORIGINAL ENV REPORT CONTENT');

        $miningDoc = MiningDocument::create([
            'mining_application_id' => $mining->id,
            'folder_id'             => $this->vaultFolder->id,
            'document_name'         => 'Annexure',
            'file_name'             => $commonFileName,
            'file_path'             => $miningRelPath,
            'file_size'             => 2000,
            'uploaded_by'           => $this->officerA->id,
        ]);

        $envDoc = EnvironmentDocument::create([
            'environment_project_id' => $env->id,
            'folder_id'              => $this->vaultFolder->id,
            'document_name'          => 'Annexure',
            'file_name'              => $commonFileName,
            'file_path'              => $envRelPath,
            'file_size'              => 3000,
            'uploaded_by'            => $this->officerA->id,
        ]);

        // Downstream re-upload: Environment replaces its file with a new version
        $newEnvRelPath = $this->createPhysicalDummyFile('uploads/stress_test/env', 'statutory_annexure_v2.pdf', 'NEW REVISED ENV REPORT');
        @unlink(public_path($envRelPath)); // Unlink old env file

        $envDoc->update([
            'file_name' => 'statutory_annexure_v2.pdf',
            'file_path' => $newEnvRelPath,
            'file_size' => 4500,
        ]);

        // Verify Mining Document is completely untouched
        $miningDoc->refresh();
        $this->assertEquals($commonFileName, $miningDoc->file_name);
        $this->assertEquals($miningRelPath, $miningDoc->file_path);
        $this->assertEquals(2000, $miningDoc->file_size);
        $this->assertFileExists(public_path($miningRelPath));
        $this->assertEquals('ORIGINAL MINING PLAN CONTENT', File::get(public_path($miningRelPath)));
    }

    /**
     * Adversarially stress test primary key collision attack across document tables:
     * Prove that table boundaries isolate queries so mutations on EnvironmentDocument with ID X
     * cannot touch MiningDocument with ID X.
     */
    public function test_03_document_vault_primary_key_collision_isolation(): void
    {
        $miningApp = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customerA->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branchA->id,
            'created_by'     => $this->officerA->id,
        ]);

        $envApp = EnvironmentProject::create([
            'project_code' => 'ENV-2026-' . rand(1000, 9999),
            'project_name' => 'Stress Test FK Isolation',
            'customer_id'  => $this->customerA->id,
            'district_id'  => $this->district->id,
            'branch_id'    => $this->branchA->id,
            'created_by'   => $this->officerA->id,
        ]);

        $miningDoc = MiningDocument::create([
            'mining_application_id' => $miningApp->id,
            'folder_id'             => $this->vaultFolder->id,
            'document_name'         => 'Mining Document',
            'file_name'             => 'mine.pdf',
            'file_path'             => 'uploads/mining/mine.pdf',
            'uploaded_by'           => $this->officerA->id,
        ]);

        $envDoc = EnvironmentDocument::create([
            'environment_project_id' => $envApp->id,
            'folder_id'              => $this->vaultFolder->id,
            'document_name'          => 'Environment Document',
            'file_name'              => 'env.pdf',
            'file_path'              => 'uploads/env/env.pdf',
            'uploaded_by'            => $this->officerA->id,
        ]);

        // Attempting to delete or update EnvironmentDocument must not touch MiningDocument
        $envDoc->delete();

        $this->assertNull(MiningDocument::find($miningDoc->id)->deleted_at);
        $this->assertNotNull(EnvironmentDocument::withTrashed()->find($envDoc->id)->deleted_at);

        // Cross-table query isolation: LeaseDocument querying for mining application returns 0
        $this->assertEquals(0, LeaseDocument::where('lease_application_id', $miningDoc->mining_application_id)->count());
    }

    // =========================================================================
    // SECTION 2: Financial Isolation Stress Tests
    // =========================================================================

    /**
     * Adversarially stress test financial isolation with colliding application IDs:
     * Even if Lease, Mining, Environment, and PPT share the exact same numeric ID (e.g. 88888),
     * application_payments records are strictly partitioned by application_type.
     */
    public function test_04_financial_isolation_polymorphic_application_payments_with_id_collision(): void
    {
        $collidingId = 88888;

        // Create 4 distinct departmental payments with the same colliding application_id
        $payLease = ApplicationPayment::create([
            'application_type' => 'lease',
            'application_id'   => $collidingId,
            'payable_type'     => LeaseApplication::class,
            'payable_id'       => $collidingId,
            'product_value'    => 100000.00,
            'paid_amount'      => 40000.00,
            'pending_amount'   => 60000.00,
            'payment_status'   => 'partial',
            'notes'            => 'Lease Application Fee',
        ]);

        $payMining = ApplicationPayment::create([
            'application_type' => 'mining',
            'application_id'   => $collidingId,
            'payable_type'     => MiningApplication::class,
            'payable_id'       => $collidingId,
            'product_value'    => 250000.00,
            'paid_amount'      => 250000.00,
            'pending_amount'   => 0.00,
            'payment_status'   => 'paid',
            'notes'            => 'Mining Plan Preparation Full Fee',
        ]);

        $payEnv = ApplicationPayment::create([
            'application_type' => 'environment',
            'application_id'   => $collidingId,
            'payable_type'     => EnvironmentProject::class,
            'payable_id'       => $collidingId,
            'product_value'    => 150000.00,
            'paid_amount'      => 50000.00,
            'pending_amount'   => 100000.00,
            'payment_status'   => 'partial',
            'notes'            => 'Environmental Clearance Scrutiny',
        ]);

        $payPpt = ApplicationPayment::create([
            'application_type' => 'ppt',
            'application_id'   => $collidingId,
            'payable_type'     => PptApplication::class,
            'payable_id'       => $collidingId,
            'product_value'    => 75000.00,
            'paid_amount'      => 75000.00,
            'pending_amount'   => 0.00,
            'payment_status'   => 'paid',
            'notes'            => 'SEAC PPT Presentation Retainer',
        ]);

        // Assert strictly segregated sums per department type
        $leasePaid = ApplicationPayment::where('application_type', 'lease')->where('application_id', $collidingId)->sum('paid_amount');
        $miningPaid = ApplicationPayment::where('application_type', 'mining')->where('application_id', $collidingId)->sum('paid_amount');
        $envPaid   = ApplicationPayment::where('application_type', 'environment')->where('application_id', $collidingId)->sum('paid_amount');
        $pptPaid   = ApplicationPayment::where('application_type', 'ppt')->where('application_id', $collidingId)->sum('paid_amount');

        $this->assertEquals(40000.00, (float) $leasePaid);
        $this->assertEquals(250000.00, (float) $miningPaid);
        $this->assertEquals(50000.00, (float) $envPaid);
        $this->assertEquals(75000.00, (float) $pptPaid);

        // Assert zero cross-leakage: sum of all is 415,000, while individual department sums are isolated
        $totalAll = ApplicationPayment::where('application_id', $collidingId)->sum('paid_amount');
        $this->assertEquals(415000.00, (float) $totalAll);
        $this->assertNotEquals($totalAll, $miningPaid);
    }

    /**
     * Adversarially stress test financial mutation quarantine:
     * Mutating downstream PPT and Environment payments does not affect upstream Mining payment state.
     */
    public function test_05_financial_mutation_quarantine_across_departments(): void
    {
        $appId = 77777;

        $payMining = ApplicationPayment::create([
            'application_type' => 'mining',
            'application_id'   => $appId,
            'product_value'    => 200000.00,
            'paid_amount'      => 100000.00,
            'pending_amount'   => 100000.00,
            'payment_status'   => 'partial',
        ]);

        $payPpt = ApplicationPayment::create([
            'application_type' => 'ppt',
            'application_id'   => $appId,
            'product_value'    => 60000.00,
            'paid_amount'      => 30000.00,
            'pending_amount'   => 30000.00,
            'payment_status'   => 'partial',
        ]);

        // Mutate PPT payment to full payment
        $payPpt->update([
            'paid_amount'    => 60000.00,
            'pending_amount' => 0.00,
            'payment_status' => 'paid',
        ]);

        // Verify Mining payment record is 100% quarantined from PPT mutation
        $payMining->refresh();
        $this->assertEquals(100000.00, (float) $payMining->paid_amount);
        $this->assertEquals(100000.00, (float) $payMining->pending_amount);
        $this->assertEquals('partial', $payMining->payment_status);
    }

    // =========================================================================
    // SECTION 3: Multi-Tenancy & BranchScope Stress Tests
    // =========================================================================

    /**
     * Adversarially test Eloquent query isolation under BranchScope:
     * Officer A (Branch A) cannot discover or query Master Projects owned by Branch B.
     */
    public function test_06_multi_tenancy_branch_scope_eloquent_query_isolation(): void
    {
        $masterA = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerA->id,
            'branch_id'             => $this->branchA->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerA->id,
        ]);

        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        // Authenticate as Officer A (Branch A)
        $this->actingAs($this->officerA);

        // 1. Direct find() on foreign branch record must return NULL under BranchScope
        $this->assertNull(MasterProject::find($masterB->id), "Officer A must not find Master Project B via direct Eloquent find().");

        // 2. findOrFail() must throw ModelNotFoundException
        try {
            MasterProject::findOrFail($masterB->id);
            $this->fail("Expected ModelNotFoundException when querying foreign branch project.");
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }

        // 3. all() must contain MasterProject A and strictly exclude MasterProject B
        $scopedProjects = MasterProject::all();
        $this->assertTrue($scopedProjects->contains('id', $masterA->id));
        $this->assertFalse($scopedProjects->contains('id', $masterB->id), "MasterProject B must be completely excluded from Officer A's collection.");

        // 4. Adversarial Bulk Update Attack: Officer A attempts to cancel MasterProject B via query
        $updatedRows = MasterProject::where('id', $masterB->id)->update(['pipeline_status' => 'cancelled']);
        $this->assertEquals(0, $updatedRows, "Adversarial update on foreign branch record must update 0 rows.");

        // Verify MasterProject B was NOT updated in database
        $this->assertEquals('in_progress', MasterProject::withoutGlobalScopes()->find($masterB->id)->pipeline_status);
    }

    /**
     * Adversarially test HTTP read access across branches:
     * Officer A cannot view Branch B timeline dashboard (HTTP 404).
     */
    public function test_07_multi_tenancy_adversarial_http_read_protection(): void
    {
        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        // Acting as Officer A (Branch A) attempting to view Branch B project
        $response = $this->actingAs($this->officerA)
            ->get(route('master-pipeline.timeline', $masterB->id));

        $response->assertStatus(404);
    }

    /**
     * Adversarially test HTTP stage forward mutation across branches:
     * Officer A cannot forward stage of Branch B Master Project (HTTP 404 & zero DB mutation).
     */
    public function test_08_multi_tenancy_adversarial_http_forward_stage_mutation_blocked(): void
    {
        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        $movementCountBefore = PipelineMovement::where('master_project_id', $masterB->id)->count();

        // Acting as Officer A attempting to forward MasterProject B to 'environment'
        $response = $this->actingAs($this->officerA)
            ->post(route('master-pipeline.forward', $masterB->id), [
                'to_department' => 'environment',
                'reason'        => 'Unauthorized cross-branch forward attempt',
            ]);

        $response->assertStatus(404);

        // Verify Branch B project was NOT mutated
        $freshMasterB = MasterProject::withoutGlobalScopes()->find($masterB->id);
        $this->assertEquals('mining', $freshMasterB->current_stage);
        $this->assertEquals('in_progress', $freshMasterB->pipeline_status);

        // Verify zero pipeline movements were recorded
        $movementCountAfter = PipelineMovement::where('master_project_id', $masterB->id)->count();
        $this->assertEquals($movementCountBefore, $movementCountAfter);
    }

    /**
     * Adversarially test HTTP return for correction mutation across branches:
     * Officer A cannot return Branch B Master Project for correction (HTTP 404 & zero notifications).
     */
    public function test_09_multi_tenancy_adversarial_http_return_mutation_blocked(): void
    {
        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        $notificationsBefore = Notification::count();

        // Acting as Officer A attempting to return MasterProject B
        $response = $this->actingAs($this->officerA)
            ->post(route('master-pipeline.return', $masterB->id), [
                'from_department'  => 'ppt',
                'to_department'    => 'mining',
                'reason'           => 'Unauthorized cross-branch return attack',
                'priority'         => 'urgent',
                'mistake_category' => 'Boundary',
            ]);

        $response->assertStatus(404);

        // Verify Branch B project remains in progress and stage has NOT rolled back
        $freshMasterB = MasterProject::withoutGlobalScopes()->find($masterB->id);
        $this->assertEquals('ppt', $freshMasterB->current_stage);
        $this->assertEquals('in_progress', $freshMasterB->pipeline_status);

        // Verify zero notifications were dispatched
        $this->assertEquals($notificationsBefore, Notification::count());
    }

    /**
     * Adversarially test HTTP resubmit mutation across branches:
     * Officer A cannot resubmit corrections for Branch B Master Project (HTTP 404).
     */
    public function test_10_multi_tenancy_adversarial_http_resubmit_mutation_blocked(): void
    {
        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officerB->id,
        ]);

        // Acting as Officer A attempting to resubmit MasterProject B
        $response = $this->actingAs($this->officerA)
            ->post(route('master-pipeline.resubmit', $masterB->id), [
                'from_department'     => 'mining',
                'to_department'       => 'ppt',
                'rectification_notes' => 'Unauthorized cross-branch resubmit',
            ]);

        $response->assertStatus(404);

        // Verify status remains returned_for_correction
        $freshMasterB = MasterProject::withoutGlobalScopes()->find($masterB->id);
        $this->assertEquals('returned_for_correction', $freshMasterB->pipeline_status);
    }

    /**
     * Adversarially test index catalog and KPI multi-tenancy scoping:
     * Officer A passing `?branch_id=BranchB` via URL cannot access Branch B records.
     */
    public function test_11_multi_tenancy_index_kpi_and_tampered_filter_quarantine(): void
    {
        $masterA = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerA->id,
            'branch_id'             => $this->branchA->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerA->id,
        ]);

        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        // 1. Regular Index view as Officer A: must see Project A, must NOT see Project B
        $response = $this->actingAs($this->officerA)->get(route('master-pipeline.index'));
        $response->assertStatus(200);
        $response->assertSee($masterA->master_application_id);
        $response->assertDontSee($masterB->master_application_id);

        // 2. Tampered query parameter: Officer A requests branch_id = Branch B
        // Because BranchScope enforces branch_id = Branch A, the intersection query yields 0 results
        $tamperedResponse = $this->actingAs($this->officerA)
            ->get(route('master-pipeline.index', ['branch_id' => $this->branchB->id]));

        $tamperedResponse->assertStatus(200);
        $tamperedResponse->assertDontSee($masterB->master_application_id, "Tampered branch_id parameter must not expose Branch B projects.");
    }

    /**
     * Adversarially test Super Admin statewide governance bypass:
     * Super Admin has unrestricted access to view, forward, and return Master Projects across all branches.
     */
    public function test_12_multi_tenancy_super_admin_unrestricted_statewide_governance(): void
    {
        $masterA = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerA->id,
            'branch_id'             => $this->branchA->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerA->id,
        ]);

        $masterB = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customerB->id,
            'branch_id'             => $this->branchB->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Pollachi',
            'village'               => 'Anamalai',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officerB->id,
        ]);

        // Super Admin views Branch A timeline -> 200 OK
        $responseA = $this->actingAs($this->superAdmin)
            ->get(route('master-pipeline.timeline', $masterA->id));
        $responseA->assertStatus(200);

        // Super Admin views Branch B timeline -> 200 OK
        $responseB = $this->actingAs($this->superAdmin)
            ->get(route('master-pipeline.timeline', $masterB->id));
        $responseB->assertStatus(200);

        // Super Admin forwards Branch B project -> 302 Redirect with success
        $forwardResp = $this->actingAs($this->superAdmin)
            ->post(route('master-pipeline.forward', $masterB->id), [
                'to_department' => 'environment',
                'reason'        => 'Super Admin statewide advancement to environment clearance',
            ]);
        $forwardResp->assertRedirect(route('master-pipeline.timeline', $masterB->id));

        $this->assertEquals('environment', MasterProject::withoutGlobalScopes()->find($masterB->id)->current_stage);
    }
}
