<?php

namespace Tests\Feature;

use App\Models\ApplicationPayment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DgpsDocument;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\LeaseDocument;
use App\Models\LeaseSurveyNumber;
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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MasterPipelineWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    protected MasterPipelineService $service;
    protected Branch $branch;
    protected District $district;
    protected User $officer;
    protected Customer $customer;
    protected Mineral $mineral;
    protected LeaseCategory $leaseCategory;
    protected Folder $vaultFolder;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->service = app(MasterPipelineService::class);

        // Core Branch & District setup
        $this->branch = Branch::first() ?? Branch::create([
            'branch_name' => 'HQ Master Pipeline Branch',
            'status'      => 1,
        ]);

        $this->district = District::firstOrCreate(
            ['name' => 'Salem'],
            ['status' => 1]
        );

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        $this->officer = User::factory()->create([
            'name'      => 'Senior Regulatory Inspector',
            'email'     => 'officer_' . uniqid() . '@gtms.gov.in',
            'branch_id' => $this->branch->id,
            'role_id'   => $role->id,
            'status'    => 1,
        ]);
        $this->officer->assignRole($role);

        $this->customer = Customer::create([
            'customer_name' => 'Kaveri Granites Export Ltd',
            'company_name'  => 'Kaveri Mining Concessions India Pvt Ltd',
            'mobile_num'    => '9840198401',
            'gstin'         => '33AAACK1234F1Z9',
            'branch_id'     => $this->branch->id,
            'created_by'    => $this->officer->id,
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
            'name'      => 'Statutory Verification Vault',
            'status'    => 1,
        ]);
    }

    /**
     * Test 1: Unique Master ID Generation (GTMS-YYYY-XXXX) and atomic sequence.
     */
    public function test_01_unique_master_id_generation_and_atomic_sequence(): void
    {
        $year = 2026;
        $id1 = $this->service->generateMasterId($year);

        // 1. Verify strict format GTMS-YYYY-XXXX
        $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4}$/', $id1);

        // 2. Sequential incremental progression
        $project1 = MasterProject::create([
            'master_application_id' => $id1,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $id2 = $this->service->generateMasterId($year);
        $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4}$/', $id2);

        $seq1 = (int) substr($id1, -4);
        $seq2 = (int) substr($id2, -4);
        $this->assertEquals($seq1 + 1, $seq2, "Next Master ID sequence must increment sequentially.");

        // 3. Collision avoidance loop test:
        // If a high-sequence record already exists (e.g. GTMS-2026-9000), candidate must jump past collisions
        $highSeqCandidate = "GTMS-{$year}-9000";
        MasterProject::create([
            'master_application_id' => $highSeqCandidate,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Next candidate should be GTMS-2026-9001
        $idAfterCollision = $this->service->generateMasterId($year);
        $this->assertEquals("GTMS-{$year}-9001", $idAfterCollision);

        // Pre-create 9001 to simulate another worker claiming it right before commit
        MasterProject::create([
            'master_application_id' => "GTMS-{$year}-9001",
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Collision-guard loop should automatically yield 9002
        $idResolved = $this->service->generateMasterId($year);
        $this->assertEquals("GTMS-{$year}-9002", $idResolved);
    }

    /**
     * Test 2: Master Project creation & auto-fill from Lease Application
     * (customer, district, taluk, village, extent, survey numbers, mineral).
     */
    public function test_02_master_project_creation_and_autofill_from_lease_application(): void
    {
        $lease = LeaseApplication::create([
            'customer_id'    => $this->customer->id,
            'category_id'    => $this->leaseCategory->id,
            'mineral_id'     => $this->mineral->id,
            'district_id'    => $this->district->id,
            'application_no' => 'LA-2026-' . rand(1000, 9999),
            'taluk'          => 'Attur',
            'village'        => 'Peddanayakkanpalayam',
            'area_extent_ha' => 4.8500,
            'survey_numbers' => '112/1A, 112/1B',
            'branch_id'      => $this->branch->id,
            'status'         => 'submitted',
            'created_by'     => $this->officer->id,
        ]);

        // Attach structured survey numbers
        LeaseSurveyNumber::create([
            'lease_application_id' => $lease->id,
            'survey_no'            => '112/1A',
            'extent_ha'            => 2.5000,
        ]);
        LeaseSurveyNumber::create([
            'lease_application_id' => $lease->id,
            'survey_no'            => '112/1B',
            'extent_ha'            => 2.3500,
        ]);

        // Promote / initiate master project from lease
        $master = $this->service->createOrGetMasterProject($lease, $this->officer);

        $this->assertInstanceOf(MasterProject::class, $master);
        $this->assertNotNull($master->id);
        $this->assertMatchesRegularExpression('/^GTMS-\d{4}-\d{4}$/', $master->master_application_id);

        // Verify comprehensive auto-fill
        $this->assertEquals($this->customer->id, $master->customer_id);
        $this->assertEquals($this->district->id, $master->district_id);
        $this->assertEquals('Attur', $master->taluk);
        $this->assertEquals('Peddanayakkanpalayam', $master->village);
        $this->assertEquals(4.8500, (float) $master->area_extent_ha);
        $this->assertStringContainsString('112/1A', $master->survey_numbers);
        $this->assertStringContainsString('112/1B', $master->survey_numbers);
        $this->assertEquals('Black Granite', $master->mineral_name);
        $this->assertEquals('lease', $master->current_stage);
        $this->assertEquals('in_progress', $master->pipeline_status);
        $this->assertEquals($lease->id, $master->lease_application_id);

        // Verify mutual linking on LeaseApplication
        $lease->refresh();
        $this->assertEquals($master->id, $lease->master_project_id);
        $this->assertEquals($master->master_application_id, $lease->common_id);

        // Verify initial audit log entry
        $this->assertDatabaseHas('pipeline_movements', [
            'master_project_id' => $master->id,
            'from_department'   => 'system',
            'to_department'     => 'lease',
            'action_type'       => 'Initiation',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ]);

        // Verify idempotency: calling createOrGetMasterProject again returns the exact same master project
        $duplicateCallMaster = $this->service->createOrGetMasterProject($lease, $this->officer);
        $this->assertEquals($master->id, $duplicateCallMaster->id);
        $this->assertEquals($master->master_application_id, $duplicateCallMaster->master_application_id);
    }

    /**
     * Test 3: Departmental compartmentalization: verify linking department applications
     * (LA-, MP-, DGPS-, ENV-, PPT-) maintains dedicated application numbers, independent
     * document vaults, and separated payment records without cross-contamination.
     */
    public function test_03_departmental_compartmentalization_and_independent_vaults_and_payments(): void
    {
        // 1. Create Umbrella Master Project
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '55/1, 55/2',
            'area_extent_ha'        => 3.2000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // 2. Department 1: Lease Application
        $lease = LeaseApplication::create([
            'application_no'    => 'LA-2026-7001',
            'common_id'         => $master->master_application_id,
            'master_project_id' => $master->id,
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'mineral_id'        => $this->mineral->id,
            'category_id'       => $this->leaseCategory->id,
            'branch_id'         => $this->branch->id,
            'status'            => 'approved',
            'created_by'        => $this->officer->id,
        ]);

        // 3. Department 2: DGPS Survey
        $dgps = DgpsSurvey::create([
            'survey_no'          => '55/1, 55/2',
            'field_book_no'      => 'DGPS-2026-7002',
            'common_id'          => $master->master_application_id,
            'master_project_id'  => $master->id,
            'customer_id'        => $this->customer->id,
            'district_id'        => $this->district->id,
            'survey_status'      => 'completed',
            'branch_id'          => $this->branch->id,
            'created_by'         => $this->officer->id,
        ]);

        // 4. Department 3: Mining Plan
        $mining = MiningApplication::create([
            'application_no'      => 'MP-2026-7003',
            'common_id'           => $master->master_application_id,
            'master_project_id'   => $master->id,
            'customer_id'         => $this->customer->id,
            'district_id'         => $this->district->id,
            'survey_numbers_text' => '55/1, 55/2',
            'status'              => 'approved',
            'branch_id'           => $this->branch->id,
            'created_by'          => $this->officer->id,
        ]);

        // 5. Department 4: Environmental Project
        $env = EnvironmentProject::create([
            'project_code'      => 'ENV-2026-7004',
            'common_id'         => $master->master_application_id,
            'master_project_id' => $master->id,
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'category'          => 'B2',
            'project_name'      => 'Kaveri Black Granite Quarry',
            'status'            => 'in_progress',
            'branch_id'         => $this->branch->id,
            'created_by'        => $this->officer->id,
        ]);

        // 6. Department 5: PPT Presentation
        $ppt = PptApplication::create([
            'application_no'    => 'PPT-2026-7005',
            'common_id'         => $master->master_application_id,
            'master_project_id' => $master->id,
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'mineral_id'        => $this->mineral->id,
            'project_name'      => 'SEAC Presentation - Kaveri Quarry',
            'status'            => 'scheduled',
            'branch_id'         => $this->branch->id,
            'created_by'        => $this->officer->id,
        ]);

        // Update master with all links
        $master->update([
            'lease_application_id'   => $lease->id,
            'dgps_survey_id'         => $dgps->id,
            'mining_application_id'  => $mining->id,
            'environment_project_id' => $env->id,
            'ppt_application_id'     => $ppt->id,
        ]);

        // --- SUB-VERIFICATION A: Dedicated Department Application Numbers ---
        $this->assertStringStartsWith('LA-', $lease->application_no);
        $this->assertStringStartsWith('DGPS-', $dgps->field_book_no);
        $this->assertStringStartsWith('MP-', $mining->application_no);
        $this->assertStringStartsWith('ENV-', $env->project_code);
        $this->assertStringStartsWith('PPT-', $ppt->application_no);

        // Assert zero cross-contamination of application numbers
        $numbers = [
            $lease->application_no,
            $dgps->field_book_no,
            $mining->application_no,
            $env->project_code,
            $ppt->application_no,
        ];
        $this->assertCount(5, array_unique($numbers), "All departmental application numbers must remain completely unique.");

        // --- SUB-VERIFICATION B: Independent Document Vaults ---
        $docLease = LeaseDocument::create([
            'lease_application_id' => $lease->id,
            'folder_id'            => $this->vaultFolder->id,
            'document_name'        => 'Village Map & Patta Copy',
            'file_name'            => 'lease_patta_7001.pdf',
            'file_path'            => 'uploads/lease/lease_patta_7001.pdf',
            'file_type'            => 'application/pdf',
            'file_size'            => 102400,
            'uploaded_by'          => $this->officer->id,
        ]);

        $docDgps = DgpsDocument::create([
            'dgps_survey_id' => $dgps->id,
            'folder_id'      => $this->vaultFolder->id,
            'document_name'  => 'DGPS Demarcation Coordinate Co-ordinates',
            'file_name'      => 'dgps_points_7002.csv',
            'file_path'      => 'uploads/dgps/dgps_points_7002.csv',
            'file_type'      => 'text/csv',
            'file_size'      => 45000,
            'uploaded_by'    => $this->officer->id,
        ]);

        $docMining = MiningDocument::create([
            'mining_application_id' => $mining->id,
            'folder_id'             => $this->vaultFolder->id,
            'document_name'         => 'Approved Mining Scheme & Progressive Plan',
            'file_name'             => 'mining_plan_7003.pdf',
            'file_path'             => 'uploads/mining/mining_plan_7003.pdf',
            'file_type'             => 'application/pdf',
            'file_size'             => 5242880,
            'uploaded_by'           => $this->officer->id,
        ]);

        $docEnv = EnvironmentDocument::create([
            'environment_project_id' => $env->id,
            'folder_id'              => $this->vaultFolder->id,
            'document_name'          => 'Form 1 Baseline Environmental Report',
            'file_name'              => 'env_form1_7004.pdf',
            'file_path'              => 'uploads/env/env_form1_7004.pdf',
            'file_type'              => 'application/pdf',
            'file_size'              => 8388608,
            'uploaded_by'            => $this->officer->id,
        ]);

        $docPpt = PptDocument::create([
            'ppt_application_id' => $ppt->id,
            'folder_id'          => $this->vaultFolder->id,
            'document_name'      => 'SEAC Technical Presentation Slides',
            'file_name'          => 'ppt_slides_7005.pptx',
            'file_path'          => 'uploads/ppt/ppt_slides_7005.pptx',
            'file_type'          => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'file_size'          => 12582912,
            'uploaded_by'        => $this->officer->id,
        ]);

        // Verify independent vault isolation
        $this->assertCount(1, $lease->documents);
        $this->assertEquals($docLease->id, $lease->documents->first()->id);
        $this->assertCount(1, $dgps->documents);
        $this->assertEquals($docDgps->id, $dgps->documents->first()->id);
        $this->assertCount(1, $mining->documents);
        $this->assertEquals($docMining->id, $mining->documents->first()->id);
        $this->assertCount(1, $env->documents);
        $this->assertEquals($docEnv->id, $env->documents->first()->id);
        $this->assertCount(1, $ppt->documents);
        $this->assertEquals($docPpt->id, $ppt->documents->first()->id);

        // Verify no vault cross-leakage: LeaseDocument has 0 records referencing mining, env, or ppt
        $this->assertEquals(0, LeaseDocument::where('lease_application_id', $mining->id)->count());
        $this->assertEquals(0, MiningDocument::where('mining_application_id', $lease->id)->count());

        // --- SUB-VERIFICATION C: Separated Financial Accounting ---
        // Record payment for Lease
        ApplicationPayment::create([
            'application_type' => 'lease',
            'application_id'   => $lease->id,
            'payable_type'     => LeaseApplication::class,
            'payable_id'       => $lease->id,
            'product_value'    => 50000.00,
            'paid_amount'      => 30000.00,
            'pending_amount'   => 20000.00,
            'payment_status'   => 'partial',
            'notes'            => 'Lease Application Scrutiny Fee',
        ]);

        // Record payment for DGPS
        ApplicationPayment::create([
            'application_type' => 'dgps',
            'application_id'   => $dgps->id,
            'payable_type'     => DgpsSurvey::class,
            'payable_id'       => $dgps->id,
            'product_value'    => 25000.00,
            'paid_amount'      => 25000.00,
            'pending_amount'   => 0.00,
            'payment_status'   => 'paid',
            'notes'            => 'DGPS Demarcation Full Settlement',
        ]);

        // Record payment for Mining
        ApplicationPayment::create([
            'application_type' => 'mining',
            'application_id'   => $mining->id,
            'payable_type'     => MiningApplication::class,
            'payable_id'       => $mining->id,
            'product_value'    => 80000.00,
            'paid_amount'      => 40000.00,
            'pending_amount'   => 40000.00,
            'payment_status'   => 'partial',
            'notes'            => 'Mining Plan Preparation Advance',
        ]);

        $leasePaid = ApplicationPayment::where('application_type', 'lease')->where('application_id', $lease->id)->sum('paid_amount');
        $dgpsPaid  = ApplicationPayment::where('application_type', 'dgps')->where('application_id', $dgps->id)->sum('paid_amount');
        $miningPaid = ApplicationPayment::where('application_type', 'mining')->where('application_id', $mining->id)->sum('paid_amount');

        $this->assertEquals(30000.00, (float) $leasePaid);
        $this->assertEquals(25000.00, (float) $dgpsPaid);
        $this->assertEquals(40000.00, (float) $miningPaid);

        // Assert payment for Lease did not alter Mining or DGPS balances
        $this->assertNotEquals($leasePaid, $miningPaid);
        $this->assertNotEquals($dgpsPaid, $miningPaid);
    }

    /**
     * Test 4: Forward stage transitions (Lease -> Survey -> Mining -> Environment -> PPT)
     * with metadata auto-fill and validation.
     */
    public function test_04_forward_stage_transitions_with_metadata_autofill(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '101/1, 101/2',
            'area_extent_ha'        => 3.5000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Step 1: Forward from Lease to Survey (DGPS)
        $m1 = $this->service->forwardStage($master, 'survey_dgps', [
            'reason'   => 'Lease document verification concluded; advancing to DGPS field demarcation.',
            'priority' => 'normal',
        ], $this->officer);

        $this->assertInstanceOf(PipelineMovement::class, $m1);
        $this->assertEquals('Forward_Transfer', $m1->action_type);
        $this->assertEquals('lease', $m1->from_department);
        $this->assertEquals('survey_dgps', $m1->to_department);
        $this->assertEquals('survey_dgps', $master->fresh()->current_stage);

        // Step 2: Forward from Survey to Mining with Auto-Fill Verification
        $autoFillMining = $this->service->getAutoFillData($master, 'mining');
        $this->assertEquals('101/1, 101/2', $autoFillMining['survey_numbers_text']);
        $this->assertEquals($master->customer_id, $autoFillMining['customer_id']);

        $m2 = $this->service->forwardStage($master, 'mining', [
            'reason'   => 'DGPS boundary coordinates verified; formulation of 5-year Mining Plan initiated.',
            'priority' => 'normal',
        ], $this->officer);

        $this->assertEquals('survey_dgps', $m2->from_department);
        $this->assertEquals('mining', $m2->to_department);
        $this->assertEquals('mining', $master->fresh()->current_stage);

        // Step 3: Forward from Mining to Environment
        $autoFillEnv = $this->service->getAutoFillData($master, 'environment');
        $this->assertStringContainsString('Pagalpatti, Omalur', $autoFillEnv['location']);

        $m3 = $this->service->forwardStage($master, 'environment', [
            'reason'   => 'Mining Plan approved by Deputy Director; advancing for SEIAA Environmental Clearance.',
            'priority' => 'high',
        ], $this->officer);

        $this->assertEquals('mining', $m3->from_department);
        $this->assertEquals('environment', $m3->to_department);
        $this->assertEquals('environment', $master->fresh()->current_stage);

        // Step 4: Forward from Environment to PPT
        $m4 = $this->service->forwardStage($master, 'ppt', [
            'reason'   => 'Form 1 scrutiny cleared; scheduled for SEAC Technical Presentation.',
            'priority' => 'high',
        ], $this->officer);

        $this->assertEquals('environment', $m4->from_department);
        $this->assertEquals('ppt', $m4->to_department);
        $this->assertEquals('ppt', $master->fresh()->current_stage);

        // Step 5: Guard Verification: Attempt illegal backward progression via forwardStage must throw exception
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot forward stage backward from 'ppt' to 'lease'. Use returnForCorrection() instead.");

        $this->service->forwardStage($master, 'lease', [
            'reason' => 'Illegal reverse forward attempt',
        ], $this->officer);
    }

    /**
     * Test 5: "Return for Correction": downstream return (e.g. from PPT or EC back to Mining or Lease)
     * with mandatory mistake reason, priority flag (urgent/high/normal), and mistake category.
     */
    public function test_05_return_for_correction_mandatory_fields_and_validation(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Validation 1: Mandatory reason validation (empty string)
        try {
            $this->service->returnForCorrection(
                $master,
                'ppt',
                'mining',
                '',
                'urgent',
                'Boundary Discrepancy',
                $this->officer
            );
            $this->fail('Expected InvalidArgumentException for empty return reason was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('A detailed reason for return is mandatory', $e->getMessage());
        }

        // Validation 2: Mandatory reason validation (whitespace only)
        try {
            $this->service->returnForCorrection(
                $master,
                'ppt',
                'mining',
                '    ',
                'urgent',
                'Boundary Discrepancy',
                $this->officer
            );
            $this->fail('Expected InvalidArgumentException for whitespace return reason was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('A detailed reason for return is mandatory', $e->getMessage());
        }

        // Validation 3: Priority flag validation (invalid priority string)
        try {
            $this->service->returnForCorrection(
                $master,
                'ppt',
                'mining',
                'Defect detected in mine plan coordinates',
                'extreme_critical', // Invalid enum
                'Boundary Discrepancy',
                $this->officer
            );
            $this->fail('Expected InvalidArgumentException for invalid priority flag was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Priority must be one of: urgent, high, normal', $e->getMessage());
        }

        // Positive execution: Return from PPT to Mining with urgent priority
        $movement = $this->service->returnForCorrection(
            $master,
            'ppt',
            'mining',
            'Mine plan production schedule exceeds sustainable extraction capacity in EIA report.',
            'urgent',
            'Production Schedule Mismatch',
            $this->officer,
            'Recalculate Year 1-5 quarry volume to align with SEIAA threshold.'
        );

        $this->assertInstanceOf(PipelineMovement::class, $movement);
        $this->assertEquals('Returned_For_Correction', $movement->action_type);
        $this->assertEquals('ppt', $movement->from_department);
        $this->assertEquals('mining', $movement->to_department);
        $this->assertEquals('urgent', $movement->priority);
        $this->assertEquals('Production Schedule Mismatch', $movement->mistake_category);
        $this->assertEquals('Mine plan production schedule exceeds sustainable extraction capacity in EIA report.', $movement->reason);
        $this->assertEquals('Recalculate Year 1-5 quarry volume to align with SEIAA threshold.', $movement->rectification_notes);
        $this->assertEquals($this->officer->id, $movement->officer_id);
        $this->assertEquals($this->officer->name, $movement->officer_name);
    }

    /**
     * Test 6: Verify active stage rolls back to target department, target application
     * status updates to 'returned_for_correction', and status_notes is populated.
     */
    public function test_06_return_for_correction_rolls_back_active_stage_and_updates_status(): void
    {
        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'status'         => 'approved',
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $ppt = PptApplication::create([
            'application_no' => 'PPT-2026-' . rand(1000, 9999),
            'project_name'   => 'SEAC Kaveri Presentation',
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'status'         => 'scheduled',
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'mining_application_id' => $mining->id,
            'ppt_application_id'    => $ppt->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $reason = 'Quarry safety 7.5m barrier zone buffer missing along northern boundary.';

        // Perform Return
        $this->service->returnForCorrection(
            $master,
            'ppt',
            'mining',
            $reason,
            'urgent',
            'Safety Buffer Deficiency',
            $this->officer
        );

        $master->refresh();
        $mining->refresh();
        $ppt->refresh();

        // 1. Verify Master stage rolled back to target department ('mining')
        $this->assertEquals('mining', $master->current_stage);
        $this->assertEquals('returned_for_correction', $master->pipeline_status);
        $this->assertTrue($master->isReturnedForCorrection());

        // 2. Verify target department application status and status_notes
        $this->assertEquals('returned_for_correction', $mining->status);
        $this->assertEquals($reason, $mining->status_notes);

        // 3. Verify downstream department also reflects returned_for_correction status
        $this->assertEquals('returned_for_correction', $ppt->status);
    }

    /**
     * Test 7: Verify real-time in-app notification alert is generated in `notifications`
     * table (App\Models\Notification) with matching priority, category, title, and action URL.
     */
    public function test_07_realtime_in_app_notification_dispatch_with_matching_payload(): void
    {
        // Target departmental user who authored the mining application
        $miningOfficer = User::factory()->create([
            'name'      => 'Mining Engineer Specialist',
            'email'     => 'mining_' . uniqid() . '@gtms.gov.in',
            'branch_id' => $this->branch->id,
            'status'    => 1,
        ]);

        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'status'         => 'approved',
            'branch_id'      => $this->branch->id,
            'created_by'     => $miningOfficer->id,
        ]);

        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'mining_application_id' => $mining->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $reason = 'Environmental clearance committee requested clarification on topsoil preservation volume.';
        $mistakeCat = 'Topsoil Management Inconsistency';

        // Trigger Return
        $this->service->returnForCorrection(
            $master,
            'ppt',
            'mining',
            $reason,
            'high',
            $mistakeCat,
            $this->officer
        );

        // Query Notification table directly for the mining application creator
        $notification = Notification::where('notifiable_id', $miningOfficer->id)
            ->where('type', 'App\Notifications\ReturnedForCorrection')
            ->latest('created_at')
            ->first();

        $this->assertNotNull($notification, "In-app real-time notification record must be generated.");
        $this->assertNull($notification->read_at, "Notification must be unread upon dispatch.");

        // Verify notification payload attributes
        $data = $notification->data;
        $this->assertStringContainsString("File Returned for Correction: {$master->master_application_id}", $data['title']);
        $this->assertEquals($reason, $data['message']);
        $this->assertEquals('mining', $data['category']);
        $this->assertEquals('high', $data['priority']);
        $this->assertEquals($mistakeCat, $data['mistake_category']);
        $this->assertEquals('ppt', $data['from_department']);
        $this->assertEquals('mining', $data['to_department']);
        $this->assertEquals($master->id, $data['master_project_id']);
        $this->assertEquals($master->master_application_id, $data['master_application_id']);
        $this->assertStringContainsString("/master-pipeline/timeline/{$master->id}", $data['action_url']);
    }

    /**
     * Test 8: Resubmit fix: upstream resolves defect with rectification notes,
     * file advances back, and audit log records 'Resubmitted_Fix'.
     */
    public function test_08_resubmit_fix_restores_pipeline_status_and_advances_stage(): void
    {
        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'status'         => 'returned_for_correction',
            'status_notes'   => 'Boundary overlaps with reserve forest buffer.',
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $ppt = PptApplication::create([
            'application_no' => 'PPT-2026-' . rand(1000, 9999),
            'project_name'   => 'SEAC Presentation - Kaveri Quarry',
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'status'         => 'returned_for_correction',
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'mining_application_id' => $mining->id,
            'ppt_application_id'    => $ppt->id,
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        $rectificationNotes = 'Revised boundary coordinates demarcated outside forest 50m buffer. Forest NOC appended.';

        // Perform Resubmission
        $movement = $this->service->resubmitFix(
            $master,
            'mining',
            'ppt',
            $rectificationNotes,
            $this->officer
        );

        $this->assertInstanceOf(PipelineMovement::class, $movement);
        $this->assertEquals('Resubmitted_Fix', $movement->action_type);
        $this->assertEquals('mining', $movement->from_department);
        $this->assertEquals('ppt', $movement->to_department);
        $this->assertEquals($rectificationNotes, $movement->rectification_notes);
        $this->assertEquals($this->officer->id, $movement->officer_id);
        $this->assertEquals($this->officer->name, $movement->officer_name);

        $master->refresh();
        $mining->refresh();
        $ppt->refresh();

        // Verify Master Project state restored
        $this->assertEquals('ppt', $master->current_stage);
        $this->assertEquals('in_progress', $master->pipeline_status);
        $this->assertFalse($master->isReturnedForCorrection());

        // Verify department statuses updated back to in_progress
        $this->assertEquals('in_progress', $mining->status);
        $this->assertEquals('in_progress', $ppt->status);
    }

    /**
     * Test 9: Comprehensive movement timeline audit trail: every forward, return, and resubmit
     * movement generates an immutable record in `pipeline_movements` (timestamp, from_dept,
     * to_dept, action_type, reason, officer_id, officer_name).
     */
    public function test_09_comprehensive_movement_timeline_audit_trail_immutability(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-8888',
            'customer_id'    => $this->customer->id,
            'mineral_id'     => $this->mineral->id,
            'category_id'    => $this->leaseCategory->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        // 1. Initiation
        $master = $this->service->createOrGetMasterProject($lease, $this->officer);

        // 2. Forward to Survey
        $this->service->forwardStage($master, 'survey_dgps', ['reason' => 'Lease verified'], $this->officer);

        // 3. Forward to Mining
        $this->service->forwardStage($master, 'mining', ['reason' => 'Demarcation ready'], $this->officer);

        // 4. Return to Survey for Correction
        $this->service->returnForCorrection(
            $master,
            'mining',
            'survey_dgps',
            'DGPS pillar #4 coordinate shift detected',
            'high',
            'Survey Discrepancy',
            $this->officer
        );

        // 5. Resubmit fix from Survey back to Mining
        $this->service->resubmitFix(
            $master,
            'survey_dgps',
            'mining',
            'Pillar #4 re-surveyed with dual RTK rover. Coordinate corrected.',
            $this->officer
        );

        // 6. Forward from Mining to Environment
        $this->service->forwardStage($master, 'environment', ['reason' => 'Mining plan signed'], $this->officer);

        // Fetch all movements ordered chronologically
        $movements = PipelineMovement::where('master_project_id', $master->id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(6, $movements, "All 6 pipeline lifecycle transitions must be logged in movements table.");

        $expectedSequence = [
            ['from' => 'system',      'to' => 'lease',        'action' => 'Initiation'],
            ['from' => 'lease',       'to' => 'survey_dgps',  'action' => 'Forward_Transfer'],
            ['from' => 'survey_dgps', 'to' => 'mining',       'action' => 'Forward_Transfer'],
            ['from' => 'mining',      'to' => 'survey_dgps',  'action' => 'Returned_For_Correction'],
            ['from' => 'survey_dgps', 'to' => 'mining',       'action' => 'Resubmitted_Fix'],
            ['from' => 'mining',      'to' => 'environment',  'action' => 'Forward_Transfer'],
        ];

        foreach ($movements as $index => $movement) {
            $expected = $expectedSequence[$index];
            $this->assertEquals($expected['from'], $movement->from_department);
            $this->assertEquals($expected['to'], $movement->to_department);
            $this->assertEquals($expected['action'], $movement->action_type);
            $this->assertNotNull($movement->created_at);
            $this->assertEquals($this->officer->id, $movement->officer_id);
            $this->assertEquals($this->officer->name, $movement->officer_name);
            $this->assertNotEmpty($movement->reason ?? $movement->rectification_notes);
        }

        // Verify immutability: past movement records remain unmodified
        $firstMovement = $movements->first();
        $this->assertEquals('Initiation', $firstMovement->action_type);
        $this->assertEquals('system', $firstMovement->from_department);
    }

    /**
     * Test 10: 360° Timeline dashboard rendering and operational bottleneck metric calculation.
     */
    public function test_10_360_timeline_dashboard_rendering_and_bottleneck_metrics(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-9991',
            'customer_id'    => $this->customer->id,
            'mineral_id'     => $this->mineral->id,
            'category_id'    => $this->leaseCategory->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-9992',
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $ppt = PptApplication::create([
            'application_no' => 'PPT-2026-9993',
            'project_name'   => 'SEAC Presentation - Kaveri Quarry',
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'branch_id'      => $this->branch->id,
            'created_by'     => $this->officer->id,
        ]);

        $master = $this->service->createOrGetMasterProject($lease, $this->officer);
        $master->update([
            'mining_application_id' => $mining->id,
            'ppt_application_id'    => $ppt->id,
        ]);

        // Forward to mining
        $this->service->forwardStage($master, 'mining', ['reason' => 'Forward to mining plan'], $this->officer);

        // Forward to ppt
        $this->service->forwardStage($master, 'ppt', ['reason' => 'Forward to ppt'], $this->officer);

        // Return from ppt to mining (causing a return bottleneck)
        $this->service->returnForCorrection(
            $master,
            'ppt',
            'mining',
            'SEAC committee requested contour elevation adjustments.',
            'urgent',
            'Contour Discrepancy',
            $this->officer
        );

        // --- SUB-VERIFICATION A: Service Bottleneck Calculation ---
        $timelineData = $this->service->getTimelineData($master);

        $this->assertIsArray($timelineData);
        $this->assertArrayHasKey('department_metrics', $timelineData);
        $this->assertArrayHasKey('bottlenecks', $timelineData);
        $this->assertArrayHasKey('movements', $timelineData);
        $this->assertTrue($timelineData['is_returned']);

        // Check mining department returns metrics
        $metrics = $timelineData['department_metrics'];
        $this->assertArrayHasKey('mining', $metrics);
        $this->assertEquals(1, $metrics['mining']['returns_count']);
        $this->assertTrue($metrics['mining']['has_returns']);

        // Check bottlenecks array
        $bottlenecks = $timelineData['bottlenecks'];
        $this->assertEquals(1, $bottlenecks['total_returns']);
        $this->assertArrayHasKey('mining', $bottlenecks['bottleneck_list']);
        $this->assertEquals('high', $bottlenecks['bottleneck_list']['mining']['severity']);

        // --- SUB-VERIFICATION B: HTTP View Rendering ---
        $response = $this->actingAs($this->officer)
            ->get(route('master-pipeline.timeline', $master->id));

        $response->assertStatus(200);
        $response->assertViewIs('pages.master_pipeline.timeline');
        $response->assertViewHas(['master', 'timelineData', 'departments']);

        // Assert crucial UI elements are rendered
        $response->assertSee($master->master_application_id);
        $response->assertSee('UNIVERSAL MASTER PROJECT');
        $response->assertSee('360° Master Project Timeline');
        $response->assertSee('Kaveri Granites Export Ltd');
        $response->assertSee('Returned For Correction');
        $response->assertSee('Operational Bottleneck');
        $response->assertSee('SEAC committee requested contour elevation adjustments.');
        $response->assertSee('Contour Discrepancy');
        $response->assertSee('Forwarded to Next Department');
    }
}
