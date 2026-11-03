<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\LeaseSurveyNumber;
use App\Models\MasterProject;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\Notification;
use App\Models\PipelineMovement;
use App\Models\User;
use App\Services\MasterPipelineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Tests\TestCase;

class MasterPipelineServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected MasterPipelineService $service;
    protected Branch $branch;
    protected District $district;
    protected User $officer;
    protected Customer $customer;
    protected Mineral $mineral;
    protected LeaseCategory $leaseCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MasterPipelineService();

        $this->branch = Branch::first() ?? Branch::create(['branch_name' => 'Salem Branch']);
        $this->district = District::first() ?? District::create(['name' => 'Salem']);
        $this->officer = User::first() ?? User::factory()->create(['branch_id' => $this->branch->id]);

        $this->customer = Customer::create([
            'customer_name' => 'Kaveri Minerals Corp',
            'company_name'  => 'Kaveri Minerals Pvt Ltd',
            'mobile_num'    => '9876543210',
            'branch_id'     => $this->branch->id,
            'created_by'    => $this->officer->id,
        ]);

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Granite (Black)'],
            ['category' => 'major', 'default_unit' => 'CBM', 'status' => 1]
        );

        $this->leaseCategory = LeaseCategory::first() ?? LeaseCategory::create([
            'code' => 'PATTA',
            'name' => 'Patta Land',
        ]);
    }

    public function test_generate_master_id_format_and_sequence(): void
    {
        $id1 = $this->service->generateMasterId(2026);
        $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4}$/', $id1);

        // Create a record with a known highest sequence
        $currentMax = MasterProject::withoutGlobalScopes()
            ->where('master_application_id', 'like', 'GTMS-2026-%')
            ->get()
            ->map(function ($p) {
                if (preg_match('/GTMS-2026-(\d+)/', $p->master_application_id, $m)) {
                    return (int) $m[1];
                }
                return 0;
            })->max() ?? 0;

        $targetSeq = $currentMax + 10;
        $targetId = 'GTMS-2026-' . str_pad($targetSeq, 4, '0', STR_PAD_LEFT);
        $expectedNext = 'GTMS-2026-' . str_pad($targetSeq + 1, 4, '0', STR_PAD_LEFT);

        MasterProject::create([
            'master_application_id' => $targetId,
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $id2 = $this->service->generateMasterId(2026);
        $this->assertEquals($expectedNext, $id2);
    }

    public function test_create_or_get_master_project_initializes_master_and_links_lease(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-7777',
            'customer_id'    => $this->customer->id,
            'branch_id'      => $this->branch->id,
            'district_id'    => $this->district->id,
            'category_id'    => $this->leaseCategory->id,
            'taluk'          => 'Omalur',
            'village'        => 'Pagalpatti',
            'area_extent_ha' => '04.50.00',
            'mineral_id'     => $this->mineral->id,
            'created_by'     => $this->officer->id,
        ]);

        LeaseSurveyNumber::create([
            'lease_application_id' => $lease->id,
            'survey_no'            => '102/1A',
            'extent_ha'            => 2.5,
        ]);
        LeaseSurveyNumber::create([
            'lease_application_id' => $lease->id,
            'survey_no'            => '102/1B',
            'extent_ha'            => 2.0,
        ]);

        $master = $this->service->createOrGetMasterProject($lease, $this->officer);

        $this->assertInstanceOf(MasterProject::class, $master);
        $this->assertNotNull($master->id);
        $this->assertMatchesRegularExpression('/^GTMS-\d{4}-\d{4}$/', $master->master_application_id);
        $this->assertEquals($this->customer->id, $master->customer_id);
        $this->assertEquals($this->branch->id, $master->branch_id);
        $this->assertEquals($this->district->id, $master->district_id);
        $this->assertEquals('Omalur', $master->taluk);
        $this->assertEquals('Pagalpatti', $master->village);
        $this->assertEquals('102/1A, 102/1B', $master->survey_numbers);
        $this->assertEquals('04.50.00', $master->area_extent_ha);
        $this->assertEquals('Granite (Black)', $master->mineral_name);
        $this->assertEquals('lease', $master->current_stage);
        $this->assertEquals('in_progress', $master->pipeline_status);

        // Verify Lease Application bidirectional synchronization
        $lease->refresh();
        $this->assertEquals($master->id, $lease->master_project_id);
        $this->assertEquals($master->master_application_id, $lease->common_id);

        // Verify Initiation Movement logged
        $movement = PipelineMovement::where('master_project_id', $master->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals('Initiation', $movement->action_type);
        $this->assertEquals('system', $movement->from_department);
        $this->assertEquals('lease', $movement->to_department);
        $this->assertEquals($this->officer->id, $movement->officer_id);

        // Calling again on same lease returns existing master without re-creating
        $again = $this->service->createOrGetMasterProject($lease, $this->officer);
        $this->assertEquals($master->id, $again->id);
        $this->assertEquals(1, PipelineMovement::where('master_project_id', $master->id)->where('action_type', 'Initiation')->count());
    }

    public function test_forward_stage_advances_stage_and_associates_department_record(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '102/1A',
            'area_extent_ha'        => '03.20.00',
            'mineral_name'          => 'Rough Stone',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $miningApp = MiningApplication::create([
            'application_no'      => 'MP-2026-0044',
            'customer_id'         => $this->customer->id,
            'district_id'         => $this->district->id,
            'branch_id'           => $this->branch->id,
            'taluk'               => 'Omalur',
            'village'             => 'Pagalpatti',
            'survey_numbers_text' => '102/1A',
            'status'              => 'draft',
            'created_by'          => $this->officer->id,
        ]);

        $movement = $this->service->forwardStage(
            $master,
            'mining',
            [
                'mining_application_id' => $miningApp->id,
                'priority'              => 'normal',
                'reason'                => 'Lease scrutiny clear, advancing to Mining Plan preparation',
            ],
            $this->officer
        );

        $this->assertInstanceOf(PipelineMovement::class, $movement);
        $this->assertEquals('Forward_Transfer', $movement->action_type);
        $this->assertEquals('lease', $movement->from_department);
        $this->assertEquals('mining', $movement->to_department);
        $this->assertEquals('normal', $movement->priority);
        $this->assertEquals($this->officer->id, $movement->officer_id);
        $this->assertArrayHasKey('auto_filled', $movement->metadata);
        $this->assertEquals('Pagalpatti', $movement->metadata['auto_filled']['village']);

        $master->refresh();
        $this->assertEquals('mining', $master->current_stage);
        $this->assertEquals('in_progress', $master->pipeline_status);
        $this->assertEquals($miningApp->id, $master->mining_application_id);
    }

    public function test_forward_stage_rejects_illegal_backward_transition(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Cannot forward stage backward from 'ppt' to 'lease'");

        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $this->service->forwardStage(
            $master,
            'lease',
            ['reason' => 'Trying to improperly reverse stage via forward'],
            $this->officer
        );
    }

    public function test_return_for_correction_validates_mandatory_reason_and_priority(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Empty reason must throw
        try {
            $this->service->returnForCorrection($master, 'ppt', 'mining', '   ', 'urgent', null, $this->officer);
            $this->fail('Expected InvalidArgumentException for empty reason.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('reason for return is mandatory', $e->getMessage());
        }

        // Invalid priority must throw
        try {
            $this->service->returnForCorrection($master, 'ppt', 'mining', 'Boundary mismatch', 'critical_unknown', null, $this->officer);
            $this->fail('Expected InvalidArgumentException for invalid priority.');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Priority must be one of: urgent, high, normal', $e->getMessage());
        }
    }

    public function test_return_for_correction_updates_records_creates_audit_and_dispatches_notification(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '102/1A',
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $miningApp = MiningApplication::create([
            'application_no'      => 'MP-2026-9001',
            'customer_id'         => $this->customer->id,
            'district_id'         => $this->district->id,
            'branch_id'           => $this->branch->id,
            'status'              => 'under_scrutiny',
            'created_by'          => $this->officer->id,
            'master_project_id'   => $master->id,
        ]);
        $master->update(['mining_application_id' => $miningApp->id]);

        $reason = 'Safety distance buffer insufficient near western boundary pillar 4.';
        $priority = 'urgent';
        $mistakeCategory = 'Safety Buffer Calculation';
        $rectificationNotes = 'Recalculate 50-meter safety zone and re-upload revised plate 3.';

        $movement = $this->service->returnForCorrection(
            $master,
            'ppt',
            'mining',
            $reason,
            $priority,
            $mistakeCategory,
            $this->officer,
            $rectificationNotes
        );

        $this->assertInstanceOf(PipelineMovement::class, $movement);
        $this->assertEquals('Returned_For_Correction', $movement->action_type);
        $this->assertEquals('ppt', $movement->from_department);
        $this->assertEquals('mining', $movement->to_department);
        $this->assertEquals('urgent', $movement->priority);
        $this->assertEquals($mistakeCategory, $movement->mistake_category);
        $this->assertEquals($reason, $movement->reason);
        $this->assertEquals($rectificationNotes, $movement->rectification_notes);
        $this->assertEquals($this->officer->id, $movement->officer_id);

        // Verify MasterProject updated to returned_for_correction
        $master->refresh();
        $this->assertEquals('mining', $master->current_stage);
        $this->assertEquals('returned_for_correction', $master->pipeline_status);
        $this->assertTrue($master->isReturnedForCorrection());

        // Verify active department record status and notes updated
        $miningApp->refresh();
        $this->assertEquals('returned_for_correction', $miningApp->status);
        $this->assertEquals($reason, $miningApp->status_notes);

        // Verify Notification was dispatched
        $notification = Notification::where('notifiable_id', $this->officer->id)
            ->where('data->master_project_id', $master->id)
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals("File Returned for Correction: {$master->master_application_id}", $notification->title);
        $this->assertEquals($reason, $notification->message);
        $this->assertEquals('mining', $notification->category);
        $this->assertEquals('urgent', $notification->data['priority']);
    }

    public function test_resubmit_fix_restores_pipeline_status_and_logs_audit(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        $miningApp = MiningApplication::create([
            'application_no'    => 'MP-2026-9002',
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'branch_id'         => $this->branch->id,
            'status'            => 'returned_for_correction',
            'status_notes'      => 'Boundary overlap detected.',
            'created_by'        => $this->officer->id,
            'master_project_id' => $master->id,
        ]);
        $master->update(['mining_application_id' => $miningApp->id]);

        $rectificationNotes = 'Safety zone recalculated to 50m and boundary survey revised.';

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

        $master->refresh();
        $this->assertEquals('ppt', $master->current_stage);
        $this->assertEquals('in_progress', $master->pipeline_status);

        $miningApp->refresh();
        $this->assertEquals('in_progress', $miningApp->status);
    }

    public function test_get_timeline_data_and_bottleneck_calculation(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);
        $master->timestamps = false;
        $master->created_at = Carbon::now()->subDays(10);
        $master->updated_at = Carbon::now()->subDays(10);
        $master->save();

        // Movement 1: Initiation (10 days ago)
        $this->createMovementWithTimestamp([
            'master_project_id' => $master->id,
            'from_department'   => 'system',
            'to_department'     => 'lease',
            'action_type'       => 'Initiation',
            'priority'          => 'normal',
            'reason'            => 'Initiated',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ], Carbon::now()->subDays(10));

        // Movement 2: Forward to Mining (7 days ago) -> 3 days in Lease
        $this->createMovementWithTimestamp([
            'master_project_id' => $master->id,
            'from_department'   => 'lease',
            'to_department'     => 'mining',
            'action_type'       => 'Forward_Transfer',
            'priority'          => 'normal',
            'reason'            => 'Forwarded to Mining',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ], Carbon::now()->subDays(7));

        // Movement 3: Forward to PPT (4 days ago) -> 3 days in Mining
        $this->createMovementWithTimestamp([
            'master_project_id' => $master->id,
            'from_department'   => 'mining',
            'to_department'     => 'ppt',
            'action_type'       => 'Forward_Transfer',
            'priority'          => 'normal',
            'reason'            => 'Forwarded to PPT',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ], Carbon::now()->subDays(4));

        // Movement 4: Return for Correction to Mining (2 days ago) -> 2 days in PPT
        $this->createMovementWithTimestamp([
            'master_project_id' => $master->id,
            'from_department'   => 'ppt',
            'to_department'     => 'mining',
            'action_type'       => 'Returned_For_Correction',
            'priority'          => 'urgent',
            'reason'            => 'Survey coordinates discrepancy',
            'mistake_category'  => 'Coordinate Error',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ], Carbon::now()->subDays(2));

        // Movement 5: Resubmit Fix to PPT (1 day ago) -> 1 day in Mining
        $this->createMovementWithTimestamp([
            'master_project_id' => $master->id,
            'from_department'   => 'mining',
            'to_department'     => 'ppt',
            'action_type'       => 'Resubmitted_Fix',
            'priority'          => 'normal',
            'reason'            => 'Rectification uploaded',
            'rectification_notes' => 'Coordinates aligned with FMB',
            'officer_id'        => $this->officer->id,
            'officer_name'      => $this->officer->name,
        ], Carbon::now()->subDays(1));

        $timeline = $this->service->getTimelineData($master);

        $this->assertIsArray($timeline);
        $this->assertEquals($master->id, $timeline['master_project']->id);
        $this->assertEquals('ppt', $timeline['current_stage']);
        $this->assertFalse($timeline['is_returned']);
        $this->assertGreaterThan(0, $timeline['total_duration_hours']);
        $this->assertGreaterThan(0, $timeline['total_duration_days']);
        $this->assertNotEmpty($timeline['total_duration_human']);

        // Department metrics checks
        $this->assertArrayHasKey('lease', $timeline['department_metrics']);
        $this->assertArrayHasKey('mining', $timeline['department_metrics']);
        $this->assertArrayHasKey('ppt', $timeline['department_metrics']);

        // Mining was returned once
        $this->assertEquals(1, $timeline['department_metrics']['mining']['returns_count']);
        $this->assertTrue($timeline['department_metrics']['mining']['has_returns']);

        // Bottlenecks checks
        $this->assertNotNull($timeline['bottlenecks']['slowest_department']);
        $this->assertEquals(1, $timeline['bottlenecks']['total_returns']);
        $this->assertNotEmpty($timeline['bottlenecks']['returned_departments']);
        $this->assertEquals('mining', $timeline['bottlenecks']['returned_departments'][0]['department']);

        // Movements list check
        $this->assertCount(5, $timeline['movements']);
    }

    public function test_get_auto_fill_data(): void
    {
        $master = MasterProject::create([
            'master_application_id' => $this->service->generateMasterId(2026),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '102/1A, 102/1B',
            'area_extent_ha'        => '04.50.00',
            'mineral_name'          => 'Granite (Black)',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $autoFillMining = $this->service->getAutoFillData($master, 'mining');
        $this->assertEquals('102/1A, 102/1B', $autoFillMining['survey_numbers_text']);
        $this->assertEquals($this->customer->id, $autoFillMining['customer_id']);
        $this->assertEquals('Omalur', $autoFillMining['taluk']);
        $this->assertEquals('Pagalpatti', $autoFillMining['village']);

        $autoFillPpt = $this->service->getAutoFillData($master, 'ppt');
        $this->assertEquals('Omalur / Pagalpatti', $autoFillPpt['taluk_village']);
        $this->assertStringContainsString('Quarry Project', $autoFillPpt['project_name']);
    }

    protected function createMovementWithTimestamp(array $attributes, Carbon $timestamp): PipelineMovement
    {
        $m = new PipelineMovement($attributes);
        $m->timestamps = false;
        $m->created_at = $timestamp;
        $m->updated_at = $timestamp;
        $m->save();
        return $m;
    }
}
