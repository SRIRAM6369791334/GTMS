<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\MasterProject;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\Notification;
use App\Models\PipelineMovement;
use App\Models\PptApplication;
use App\Models\User;
use App\Services\MasterPipelineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ChallengerPipelineStressTest extends TestCase
{
    use DatabaseTransactions;

    protected MasterPipelineService $service;
    protected Branch $branch1;
    protected Branch $branch2;
    protected District $district;
    protected User $adminOfficer;
    protected User $branch1Officer;
    protected User $branch2Officer;
    protected Customer $customer;
    protected Mineral $mineral;
    protected LeaseCategory $leaseCategory;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->service = app(MasterPipelineService::class);

        // Seed Branches
        $this->branch1 = Branch::firstOrCreate(['branch_name' => 'HQ Master Branch 1'], ['status' => 1]);
        $this->branch2 = Branch::firstOrCreate(['branch_name' => 'Salem Regional Branch 2'], ['status' => 1]);

        $this->district = District::firstOrCreate(['name' => 'Salem'], ['status' => 1]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $officerRole = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);

        // Admin Officer (can access statewide across branches)
        $this->adminOfficer = User::factory()->create([
            'name'      => 'Challenger Admin Officer',
            'email'     => 'challenger_admin_' . uniqid() . '@gtms.test',
            'branch_id' => $this->branch1->id,
            'role_id'   => $adminRole->id,
            'status'    => 1,
        ]);
        $this->adminOfficer->assignRole($adminRole);

        // Branch 1 Officer (scoped to branch 1)
        $this->branch1Officer = User::factory()->create([
            'name'      => 'Branch 1 Scoped Officer',
            'email'     => 'officer_b1_' . uniqid() . '@gtms.test',
            'branch_id' => $this->branch1->id,
            'role_id'   => 99, // non-admin role_id
            'status'    => 1,
        ]);
        $this->branch1Officer->assignRole($officerRole);

        // Branch 2 Officer (scoped to branch 2)
        $this->branch2Officer = User::factory()->create([
            'name'      => 'Branch 2 Scoped Officer',
            'email'     => 'officer_b2_' . uniqid() . '@gtms.test',
            'branch_id' => $this->branch2->id,
            'role_id'   => 99, // non-admin role_id
            'status'    => 1,
        ]);
        $this->branch2Officer->assignRole($officerRole);

        // Seed Customer
        $this->customer = Customer::create([
            'customer_name' => 'Stress Test Minerals Ltd',
            'company_name'  => 'Stress Test Mining Holdings',
            'mobile_num'    => '9876543210',
            'branch_id'     => $this->branch1->id,
            'created_by'    => $this->adminOfficer->id,
        ]);

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Black Granite'],
            ['category' => 'major', 'default_unit' => 'CBM', 'status' => 1]
        );

        $this->leaseCategory = LeaseCategory::first() ?? LeaseCategory::create([
            'code' => 'PATTA_CHALLENGE',
            'name' => 'Patta Land Challenge',
        ]);
    }

    // =========================================================================
    // SECTION 1: generateMasterId() ADVERSARIAL STRESS & COLLISION TESTING
    // =========================================================================

    /**
     * Stress Test 1.1: Rapid consecutive generation of Master IDs.
     * Generates 30 sequential IDs, verifying strict format, strict monotonic incrementation,
     * and zero duplicate values under transaction locks.
     */
    public function test_01_rapid_sequential_generation_monotonicity_and_uniqueness(): void
    {
        $year = 2026;
        $generatedIds = [];
        $count = 30;

        for ($i = 0; $i < $count; $i++) {
            $id = $this->service->generateMasterId($year);

            // Assert standard regex format: GTMS-YYYY-XXXX (where XXXX is 4 or more digits)
            $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4,}$/', $id);
            $this->assertNotContains($id, $generatedIds, "Collision detected: {$id} was generated more than once!");

            $generatedIds[] = $id;

            // Commit record to DB to simulate real continuous workflow
            MasterProject::create([
                'master_application_id' => $id,
                'customer_id'           => $this->customer->id,
                'branch_id'             => $this->branch1->id,
                'district_id'           => $this->district->id,
                'taluk'                 => 'Omalur',
                'village'               => 'TestVillage_' . $i,
                'current_stage'         => 'lease',
                'pipeline_status'       => 'in_progress',
                'created_by'            => $this->adminOfficer->id,
            ]);
        }

        $this->assertCount($count, array_unique($generatedIds));

        // Verify strictly sequential progression: each sequence number is previous + 1
        for ($i = 1; $i < $count; $i++) {
            $prevSeq = (int) substr($generatedIds[$i - 1], strrpos($generatedIds[$i - 1], '-') + 1);
            $currSeq = (int) substr($generatedIds[$i], strrpos($generatedIds[$i], '-') + 1);
            $this->assertEquals($prevSeq + 1, $currSeq, "ID sequence did not increment monotonically at index {$i}.");
        }
    }

    /**
     * Stress Test 1.2: Multi-Year Partitioning & Year Transition Isolation.
     * Verifies that each year maintains an independent sequence starting at 0001,
     * and high sequence numbers in one year do not bleed into or inflate another year.
     */
    public function test_02_multi_year_partitioning_and_isolation(): void
    {
        // Pre-seed year 2024 with high sequence number
        MasterProject::create([
            'master_application_id' => 'GTMS-2024-8888',
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'completed',
            'pipeline_status'       => 'completed',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Pre-seed year 2025 with moderate sequence
        MasterProject::create([
            'master_application_id' => 'GTMS-2025-0042',
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Verify Year 2025 increments from its own max (0042 -> 0043)
        $id2025 = $this->service->generateMasterId(2025);
        $this->assertEquals('GTMS-2025-0043', $id2025);

        // Verify Year 2027 (completely unseeded year) starts at 0001
        $id2027 = $this->service->generateMasterId(2027);
        $this->assertEquals('GTMS-2027-0001', $id2027);

        // Verify Year 2024 increments from 8888 -> 8889
        $id2024 = $this->service->generateMasterId(2024);
        $this->assertEquals('GTMS-2024-8889', $id2024);
    }

    /**
     * Stress Test 1.3: 4-digit rollover boundary (9999 -> 10000 -> 10001).
     * Tests integer expansion beyond 4 digits to ensure no truncation or regex parse crashes.
     */
    public function test_03_four_to_five_digit_rollover_boundary(): void
    {
        $year = 2026;

        // Seed boundary record GTMS-2026-9999
        MasterProject::create([
            'master_application_id' => 'GTMS-2026-9999',
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Next generated ID must cleanly roll over to GTMS-2026-10000 without exception
        $nextId = $this->service->generateMasterId($year);
        $this->assertEquals('GTMS-2026-10000', $nextId);

        // Commit 10000 to DB and verify next is 10001
        MasterProject::create([
            'master_application_id' => $nextId,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        $subsequentId = $this->service->generateMasterId($year);
        $this->assertEquals('GTMS-2026-10001', $subsequentId);
    }

    /**
     * Stress Test 1.4: Soft-Deleted Record Collision Resistance.
     * Verifies that soft-deleted MasterProjects are included in the uniqueness check,
     * so that deleting a project never causes ID re-issue and DB unique constraint failure.
     */
    public function test_04_soft_deleted_project_does_not_cause_id_collision(): void
    {
        $year = 2026;

        $id = $this->service->generateMasterId($year);

        $project = MasterProject::create([
            'master_application_id' => $id,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Soft delete the project
        $project->delete();
        $this->assertSoftDeleted('master_projects', ['id' => $project->id]);

        // Generate next ID: service MUST NOT re-issue the deleted ID
        $nextId = $this->service->generateMasterId($year);
        $this->assertNotEquals($id, $nextId);

        $prevSeq = (int) substr($id, strrpos($id, '-') + 1);
        $currSeq = (int) substr($nextId, strrpos($nextId, '-') + 1);
        $this->assertEquals($prevSeq + 1, $currSeq);
    }

    /**
     * Stress Test 1.5: Concurrency Simulation & Collision-Guard Loop Leap.
     * Simulates a race condition where candidate IDs (e.g. 0501, 0502, 0503) are pre-emptively
     * occupied in the database by concurrent sessions right before resolution.
     */
    public function test_05_concurrency_collision_guard_skips_occupied_slots(): void
    {
        $year = 2026;

        $currentMax = MasterProject::withoutGlobalScopes()
            ->where('master_application_id', 'like', "GTMS-{$year}-%")
            ->get()
            ->map(function ($p) use ($year) {
                if (preg_match("/GTMS-{$year}-(\\d+)/", $p->master_application_id, $m)) {
                    return (int) $m[1];
                }
                return 0;
            })->max() ?? 0;

        $base = $currentMax + 10;
        $id0 = 'GTMS-' . $year . '-' . str_pad($base, 4, '0', STR_PAD_LEFT);
        $id1 = 'GTMS-' . $year . '-' . str_pad($base + 1, 4, '0', STR_PAD_LEFT);
        $id2 = 'GTMS-' . $year . '-' . str_pad($base + 2, 4, '0', STR_PAD_LEFT);
        $id3 = 'GTMS-' . $year . '-' . str_pad($base + 3, 4, '0', STR_PAD_LEFT);
        $expected = 'GTMS-' . $year . '-' . str_pad($base + 4, 4, '0', STR_PAD_LEFT);

        // Base max project
        MasterProject::create([
            'master_application_id' => $id0,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Simulate concurrent transactions that inserted 0501, 0502, 0503
        MasterProject::create([
            'master_application_id' => $id1,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);
        MasterProject::create([
            'master_application_id' => $id2,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);
        MasterProject::create([
            'master_application_id' => $id3,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // The generator must detect all existing records and jump cleanly to expected
        $resolvedId = $this->service->generateMasterId($year);
        $this->assertEquals($expected, $resolvedId);
    }

    /**
     * Stress Test 1.6: Malformed Non-Numeric Suffixes Resilience.
     * Adversarially injects non-standard master IDs (e.g. GTMS-2026-CUSTOM, GTMS-2026-ALPHA)
     * and verifies the generator does not crash with SQL or regex exceptions.
     */
    public function test_06_malformed_and_custom_id_suffixes_resilience(): void
    {
        $year = 2026;

        // Inject non-standard alphanumeric IDs
        MasterProject::create([
            'master_application_id' => 'GTMS-2026-CUSTOM',
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);
        MasterProject::create([
            'master_application_id' => 'GTMS-2026-LEGACY-ARCHIVE',
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Generator should run without crashing and yield a valid numeric ID
        $generated = $this->service->generateMasterId($year);
        $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4,}$/', $generated);
    }

    // =========================================================================
    // SECTION 2: MULTI-HOP RETURN LOOPS & BOTTLENECK METRICS STRESS TESTING
    // =========================================================================

    /**
     * Stress Test 2.1: Exhaustive 12-Step Multi-Hop Return & Resubmit Workflow.
     * Trajectory:
     * Lease -> Survey -> Mining -> Environment -> PPT
     * -> Returned to Environment
     * -> Returned to Mining
     * -> Returned to Lease
     * -> Resubmitted to Mining
     * -> Resubmitted to Environment
     * -> Resubmitted to PPT
     * -> Forwarded to Completed
     *
     * Asserts chronological audit trail integrity, from/to consistency, and bottleneck counters.
     */
    public function test_07_deep_multi_hop_return_and_resubmit_loop_integrity(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'category_id'    => $this->leaseCategory->id,
            'branch_id'      => $this->branch1->id,
            'created_by'     => $this->adminOfficer->id,
        ]);

        $mining = MiningApplication::create([
            'application_no' => 'MP-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'branch_id'      => $this->branch1->id,
            'created_by'     => $this->adminOfficer->id,
        ]);

        $env = EnvironmentProject::create([
            'project_code' => 'ENV-2026-' . rand(1000, 9999),
            'project_name' => 'Environmental Clearance Assessment',
            'customer_id'  => $this->customer->id,
            'district_id'  => $this->district->id,
            'branch_id'    => $this->branch1->id,
            'created_by'   => $this->adminOfficer->id,
        ]);

        $ppt = PptApplication::create([
            'application_no' => 'PPT-2026-' . rand(1000, 9999),
            'project_name'   => 'SEAC PPT Presentation Dossier',
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'branch_id'      => $this->branch1->id,
            'created_by'     => $this->adminOfficer->id,
        ]);

        // Step 1: Initialize Master from Lease
        $master = $this->service->createOrGetMasterProject($lease, $this->adminOfficer);
        $master->update([
            'mining_application_id'  => $mining->id,
            'environment_project_id' => $env->id,
            'ppt_application_id'     => $ppt->id,
        ]);

        // Step 2: Forward Lease -> Survey (DGPS)
        $this->service->forwardStage($master, 'survey_dgps', ['reason' => 'Lease verified'], $this->adminOfficer);

        // Step 3: Forward Survey -> Mining
        $this->service->forwardStage($master, 'mining', ['reason' => 'Demarcation ready'], $this->adminOfficer);

        // Step 4: Forward Mining -> Environment
        $this->service->forwardStage($master, 'environment', ['reason' => 'Mining plan approved'], $this->adminOfficer);

        // Step 5: Forward Environment -> PPT
        $this->service->forwardStage($master, 'ppt', ['reason' => 'Form 1 vetted'], $this->adminOfficer);

        $this->assertEquals('ppt', $master->fresh()->current_stage);

        // Step 6: Downstream Return PPT -> Environment
        $this->service->returnForCorrection(
            $master,
            'ppt',
            'environment',
            'EIA report missing seasonal baseline air quality monitoring dataset.',
            'urgent',
            'Air Quality Baseline Gap',
            $this->adminOfficer,
            'Incorporate post-monsoon ambient air data in chapter 3.'
        );
        $this->assertEquals('environment', $master->fresh()->current_stage);
        $this->assertTrue($master->fresh()->isReturnedForCorrection());

        // Step 7: Cascade Return Environment -> Mining
        $this->service->returnForCorrection(
            $master,
            'environment',
            'mining',
            'Quarry excavation bench slope exceeds 45 degrees without berm terrace.',
            'high',
            'Slope Stability Hazard',
            $this->adminOfficer,
            'Revise bench height to 6m with minimum 6m berm width.'
        );
        $this->assertEquals('mining', $master->fresh()->current_stage);

        // Step 8: Cascade Return Mining -> Lease
        $this->service->returnForCorrection(
            $master,
            'mining',
            'lease',
            'FMB sketch boundaries conflict with Revenue Village Map cadastral sheet.',
            'high',
            'Cadastral Boundary Mismatch',
            $this->adminOfficer,
            'Procure certified survey reconciliation sketch from Tahsildar.'
        );
        $this->assertEquals('lease', $master->fresh()->current_stage);

        // Step 9: Resubmit Lease -> Mining
        $this->service->resubmitFix(
            $master,
            'lease',
            'mining',
            'Certified reconciliation sketch appended by Tahsildar Omalur.',
            $this->adminOfficer
        );
        $this->assertEquals('mining', $master->fresh()->current_stage);
        $this->assertEquals('in_progress', $master->fresh()->pipeline_status);

        // Step 10: Resubmit Mining -> Environment
        $this->service->resubmitFix(
            $master,
            'mining',
            'environment',
            'Excavation bench slopes redesigned to 45 degrees with 6m berm terraces.',
            $this->adminOfficer
        );
        $this->assertEquals('environment', $master->fresh()->current_stage);

        // Step 11: Resubmit Environment -> PPT
        $this->service->resubmitFix(
            $master,
            'environment',
            'ppt',
            'Post-monsoon ambient air quality parameters appended to Chapter 3.',
            $this->adminOfficer
        );
        $this->assertEquals('ppt', $master->fresh()->current_stage);

        // Step 12: Final Forward PPT -> Completed
        $this->service->forwardStage($master, 'completed', ['reason' => 'Clearance granted by SEIAA'], $this->adminOfficer);
        $this->assertEquals('completed', $master->fresh()->current_stage);

        // Verify total movements count
        $movements = PipelineMovement::where('master_project_id', $master->id)
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(12, $movements, "All 12 lifecycle transitions in the multi-hop loop must be recorded.");

        // Verify timeline analysis and bottleneck metrics
        $timeline = $this->service->getTimelineData($master);

        $this->assertFalse($timeline['is_returned'], "Project reached completion; is_returned should be false.");
        $bottlenecks = $timeline['bottlenecks'];

        // Assert 3 return events occurred across the project lifetime
        $this->assertEquals(3, $bottlenecks['total_returns'], "Multi-hop loop must reflect exactly 3 return events.");

        // Returned departments: environment, mining, lease
        $returnedDepts = array_column($bottlenecks['returned_departments'], 'department');
        $this->assertContains('environment', $returnedDepts);
        $this->assertContains('mining', $returnedDepts);
        $this->assertContains('lease', $returnedDepts);

        // Ensure non-returned departments have 0 returns
        $metrics = $timeline['department_metrics'];
        $this->assertEquals(0, $metrics['survey_dgps']['returns_count'] ?? 0);
        $this->assertEquals(0, $metrics['ppt']['returns_count'] ?? 0);
        $this->assertEquals(1, $metrics['environment']['returns_count']);
        $this->assertEquals(1, $metrics['mining']['returns_count']);
        $this->assertEquals(1, $metrics['lease']['returns_count']);

        // Assert duration consistency
        $this->assertGreaterThanOrEqual(0, $timeline['total_duration_seconds']);
        $this->assertNotEmpty($timeline['total_duration_human']);
    }

    /**
     * Stress Test 2.2: Direct Multi-Stage Leap Return (PPT directly to Lease).
     * Downstream department leaps backward over multiple intermediate departments.
     * Asserts stage state, target department notification dispatch, and subsequent recovery.
     */
    public function test_08_direct_multi_stage_leap_return_and_recovery(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'category_id'    => $this->leaseCategory->id,
            'branch_id'      => $this->branch1->id,
            'created_by'     => $this->branch1Officer->id,
        ]);

        $master = $this->service->createOrGetMasterProject($lease, $this->adminOfficer);

        // Advance to PPT
        $master->update(['current_stage' => 'ppt', 'pipeline_status' => 'in_progress']);

        // Leap return PPT -> Lease directly
        $reason = 'Concession patta land classification cancelled by District Collector.';
        $movement = $this->service->returnForCorrection(
            $master,
            'ppt',
            'lease',
            $reason,
            'urgent',
            'Land Title Revocation',
            $this->adminOfficer,
            'Submit valid revised patta copy or government g.o.'
        );

        $this->assertEquals('lease', $master->fresh()->current_stage);
        $this->assertEquals('returned_for_correction', $master->fresh()->pipeline_status);

        // Verify notification dispatched to lease creator
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->branch1Officer->id,
            'type'          => 'App\Notifications\ReturnedForCorrection',
        ]);

        // Resubmit fix directly from Lease back to PPT
        $resubmitMovement = $this->service->resubmitFix(
            $master,
            'lease',
            'ppt',
            'District Revenue Officer issued clarification G.O. confirming patta validity.',
            $this->adminOfficer
        );

        $this->assertEquals('ppt', $master->fresh()->current_stage);
        $this->assertEquals('in_progress', $master->fresh()->pipeline_status);
        $this->assertEquals('Resubmitted_Fix', $resubmitMovement->action_type);
    }

    // =========================================================================
    // SECTION 3: NEGATIVE BOUNDARY CONDITIONS & AUTHORIZATION STRESS TESTING
    // =========================================================================

    /**
     * Stress Test 3.1: Illegal Backward Transition via forwardStage().
     * Forwarding backward (e.g. ppt -> mining, or mining -> lease) must be rejected with InvalidArgumentException.
     */
    public function test_09_illegal_backward_transition_via_forward_stage_rejected(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'district_id'           => $this->district->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot forward stage backward from 'ppt' to 'mining'. Use returnForCorrection() instead.");

        $this->service->forwardStage($master, 'mining', ['reason' => 'Invalid backward step'], $this->adminOfficer);
    }

    /**
     * Stress Test 3.2: Empty & Whitespace-Only Return Reasons.
     * Service must reject empty or whitespace return reasons with clear exceptions.
     */
    public function test_10_empty_and_whitespace_return_reasons_rejected(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'environment',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Test empty string
        try {
            $this->service->returnForCorrection($master, 'environment', 'mining', '', 'urgent', null, $this->adminOfficer);
            $this->fail('Expected InvalidArgumentException for empty return reason was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('A detailed reason for return is mandatory', $e->getMessage());
        }

        // Test whitespace only
        try {
            $this->service->returnForCorrection($master, 'environment', 'mining', "   \t\n   ", 'urgent', null, $this->adminOfficer);
            $this->fail('Expected InvalidArgumentException for whitespace return reason was not thrown.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('A detailed reason for return is mandatory', $e->getMessage());
        }
    }

    /**
     * Stress Test 3.3: Invalid Priority String Rejection.
     * Service must reject priorities outside ['urgent', 'high', 'normal'].
     */
    public function test_11_invalid_priority_strings_rejected(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'environment',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        $invalidPriorities = ['low', 'critical', 'emergency', 'extreme', '1', ''];

        foreach ($invalidPriorities as $badPriority) {
            try {
                $this->service->returnForCorrection(
                    $master,
                    'environment',
                    'mining',
                    'Valid defect description provided here',
                    $badPriority,
                    null,
                    $this->adminOfficer
                );
                $this->fail("Expected InvalidArgumentException for invalid priority '{$badPriority}' was not thrown.");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString('Priority must be one of: urgent, high, normal', $e->getMessage());
            }
        }
    }

    /**
     * Stress Test 3.4: Controller Form Request Validation Negative Bounds.
     * Asserts HTTP 422 / session validation errors for malformed requests across all endpoints.
     */
    public function test_12_controller_form_validation_negative_bounds(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // 1. Return: reason too short (< 5 chars)
        $respShortReason = $this->actingAs($this->adminOfficer)
            ->post(route('master-pipeline.return', $master->id), [
                'from_department' => 'ppt',
                'to_department'   => 'mining',
                'reason'          => 'nope',
                'priority'        => 'urgent',
            ]);
        $respShortReason->assertSessionHasErrors(['reason']);

        // 2. Return: invalid priority
        $respBadPriority = $this->actingAs($this->adminOfficer)
            ->post(route('master-pipeline.return', $master->id), [
                'from_department' => 'ppt',
                'to_department'   => 'mining',
                'reason'          => 'Valid defect reason meeting min 5 characters',
                'priority'        => 'super_critical',
            ]);
        $respBadPriority->assertSessionHasErrors(['priority']);

        // 3. Return: missing required departments
        $respMissingDept = $this->actingAs($this->adminOfficer)
            ->post(route('master-pipeline.return', $master->id), [
                'reason'   => 'Valid defect reason meeting min 5 characters',
                'priority' => 'urgent',
            ]);
        $respMissingDept->assertSessionHasErrors(['from_department', 'to_department']);

        // 4. Forward: missing to_department
        $respMissingForward = $this->actingAs($this->adminOfficer)
            ->post(route('master-pipeline.forward', $master->id), [
                'reason' => 'Forwarding without destination',
            ]);
        $respMissingForward->assertSessionHasErrors(['to_department']);

        // 5. Resubmit: missing rectification notes
        $respMissingNotes = $this->actingAs($this->adminOfficer)
            ->post(route('master-pipeline.resubmit', $master->id), [
                'from_department' => 'mining',
                'to_department'   => 'ppt',
            ]);
        $respMissingNotes->assertSessionHasErrors(['rectification_notes']);
    }

    /**
     * Stress Test 3.5: Guest / Unauthenticated Access Interception.
     * Asserts that unauthenticated guests are challenged with HTTP 302 redirect to login.
     */
    public function test_13_guest_unauthenticated_requests_redirected_to_login(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Unauthenticated index
        $this->get(route('master-pipeline.index'))->assertRedirect(route('login'));

        // Unauthenticated timeline
        $this->get(route('master-pipeline.timeline', $master->id))->assertRedirect(route('login'));

        // Unauthenticated forward POST
        $this->post(route('master-pipeline.forward', $master->id), [
            'to_department' => 'survey_dgps',
        ])->assertRedirect(route('login'));

        // Unauthenticated return POST
        $this->post(route('master-pipeline.return', $master->id), [
            'from_department' => 'ppt',
            'to_department'   => 'mining',
            'reason'          => 'Unauthenticated return attempt',
            'priority'        => 'normal',
        ])->assertRedirect(route('login'));

        // Unauthenticated resubmit POST
        $this->post(route('master-pipeline.resubmit', $master->id), [
            'from_department'     => 'mining',
            'to_department'       => 'ppt',
            'rectification_notes' => 'Unauthenticated resubmit attempt',
        ])->assertRedirect(route('login'));
    }

    /**
     * Stress Test 3.6: Multi-Tenancy Cross-Branch Isolation (BranchScope Guard).
     * Asserts that a scoped non-admin officer belonging to Branch 2 CANNOT access or
     * manipulate a MasterProject owned by Branch 1 (receives 404 ModelNotFoundException).
     */
    public function test_14_multi_tenancy_cross_branch_isolation(): void
    {
        // Project owned by Branch 1
        $projectBranch1 = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch1->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->adminOfficer->id,
        ]);

        // Branch 2 Officer attempts to view timeline of Branch 1 Project
        $response = $this->actingAs($this->branch2Officer)
            ->get(route('master-pipeline.timeline', $projectBranch1->id));

        $response->assertStatus(404);

        // Branch 2 Officer attempts to forward Branch 1 Project
        $forwardResp = $this->actingAs($this->branch2Officer)
            ->post(route('master-pipeline.forward', $projectBranch1->id), [
                'to_department' => 'completed',
            ]);
        $forwardResp->assertStatus(404);

        // Branch 2 Officer attempts to return Branch 1 Project
        $returnResp = $this->actingAs($this->branch2Officer)
            ->post(route('master-pipeline.return', $projectBranch1->id), [
                'from_department' => 'ppt',
                'to_department'   => 'mining',
                'reason'          => 'Unauthorized cross-branch return attempt',
                'priority'        => 'normal',
            ]);
        $returnResp->assertStatus(404);

        // However, Statewide Admin Officer CAN view and manipulate across branches
        $adminResp = $this->actingAs($this->adminOfficer)
            ->get(route('master-pipeline.timeline', $projectBranch1->id));
        $adminResp->assertStatus(200);
    }
}
