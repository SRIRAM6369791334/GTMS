<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\MasterProject;
use App\Models\Mineral;
use App\Models\Notification;
use App\Models\PipelineMovement;
use App\Models\User;
use App\Services\MasterPipelineService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterPipelineControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected Branch $branch;
    protected District $district;
    protected User $officer;
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

        $this->branch = Branch::first() ?? Branch::create(['branch_name' => 'HQ Master Branch']);
        $this->district = District::first() ?? District::create(['name' => 'Salem']);

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        $this->officer = User::factory()->create([
            'name'      => 'Senior Regulatory Officer',
            'email'     => 'officer_' . uniqid() . '@gtms.test',
            'branch_id' => $this->branch->id,
            'role_id'   => $role->id,
            'status'    => 1,
        ]);
        $this->officer->assignRole($role);

        $this->customer = Customer::create([
            'customer_name' => 'Alpha Minerals Ltd',
            'company_name'  => 'Alpha Quarry Holdings',
            'mobile_num'    => '9988776655',
            'branch_id'     => $this->branch->id,
            'created_by'    => $this->officer->id,
        ]);

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Black Granite'],
            ['category' => 'major', 'default_unit' => 'CBM', 'status' => 1]
        );

        $this->leaseCategory = LeaseCategory::first() ?? LeaseCategory::create([
            'code' => 'PATTA_TEST',
            'name' => 'Patta Land Test',
        ]);
    }

    /**
     * Test index page renders cleanly with accurate KPI counters.
     */
    public function test_index_page_loads_with_kpi_counters(): void
    {
        $project1 = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '101/1A',
            'area_extent_ha'        => 2.5000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $project2 = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Mettur',
            'village'               => 'Kolathur',
            'survey_numbers'        => '204/2B',
            'area_extent_ha'        => 4.2000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        $response = $this->actingAs($this->officer)
            ->get(route('master-pipeline.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.master_pipeline.index');
        $response->assertViewHas(['projects', 'kpis', 'branches', 'stages', 'statuses']);

        $kpis = $response->viewData('kpis');
        $this->assertGreaterThanOrEqual(2, $kpis['total_projects']);
        $this->assertGreaterThanOrEqual(1, $kpis['in_progress']);
        $this->assertGreaterThanOrEqual(1, $kpis['returned_for_correction']);

        $response->assertSee('Universal Master Projects Pipeline');
        $response->assertSee($project1->master_application_id);
        $response->assertSee($project2->master_application_id);
        $response->assertSee('Alpha Minerals Ltd');
    }

    /**
     * Test index page filtering by search keywords and status.
     */
    public function test_index_page_filtering(): void
    {
        $uniqueVillage = 'ExclusiveVillage_' . uniqid();

        $matchProject = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => $uniqueVillage,
            'survey_numbers'        => '999/1Z',
            'area_extent_ha'        => 1.8000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        // Search by unique village name
        $searchResponse = $this->actingAs($this->officer)
            ->get(route('master-pipeline.index', ['search' => $uniqueVillage]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee($matchProject->master_application_id);
        $searchResponse->assertSee($uniqueVillage);

        // Filter by status=returned_for_correction
        $statusResponse = $this->actingAs($this->officer)
            ->get(route('master-pipeline.index', ['status' => 'returned_for_correction']));

        $statusResponse->assertStatus(200);
        $statusResponse->assertSee($matchProject->master_application_id);
    }

    /**
     * Test 360° timeline dashboard loads project details, bottleneck metrics, and movement history.
     */
    public function test_timeline_page_loads_with_project_details_and_movements(): void
    {
        $master = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Sankari',
            'village'               => 'Morur',
            'survey_numbers'        => '45/1, 45/2',
            'area_extent_ha'        => 3.1250,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        // Create an audit movement for this project
        PipelineMovement::create([
            'master_project_id'   => $master->id,
            'from_department'     => 'ppt',
            'to_department'       => 'mining',
            'action_type'         => 'Returned_For_Correction',
            'priority'            => 'urgent',
            'mistake_category'    => 'Boundary Discrepancy',
            'reason'              => 'Quarry boundary overlaps with forest buffer zone.',
            'rectification_notes' => 'Resubmit corrected DGPS survey boundary plan.',
            'officer_id'          => $this->officer->id,
            'officer_name'        => $this->officer->name,
        ]);

        $response = $this->actingAs($this->officer)
            ->get(route('master-pipeline.timeline', $master->id));

        $response->assertStatus(200);
        $response->assertViewIs('pages.master_pipeline.timeline');
        $response->assertViewHas(['master', 'timelineData', 'departments']);

        $response->assertSee($master->master_application_id);
        $response->assertSee('FILE RETURNED FOR CORRECTION');
        $response->assertSee('Quarry boundary overlaps with forest buffer zone.');
        $response->assertSee('Boundary Discrepancy');
        $response->assertSee('Operational Bottleneck', false);
        $response->assertSee('Immutable Chronological Audit Trail');
    }

    /**
     * Test promote Lease Application to Master Project via createFromLease.
     */
    public function test_create_from_lease_initializes_master_project(): void
    {
        $lease = LeaseApplication::create([
            'application_no' => 'LA-2026-' . rand(1000, 9999),
            'customer_id'    => $this->customer->id,
            'branch_id'      => $this->branch->id,
            'district_id'    => $this->district->id,
            'category_id'    => $this->leaseCategory->id,
            'taluk'          => 'Edappadi',
            'village'        => 'Avaniperur',
            'area_extent_ha' => 1.5000,
            'mineral_id'     => $this->mineral->id,
            'created_by'     => $this->officer->id,
        ]);

        $response = $this->actingAs($this->officer)
            ->post(route('master-pipeline.create-from-lease', $lease->id));

        $this->assertNotNull($lease->fresh()->master_project_id);
        $master = MasterProject::find($lease->fresh()->master_project_id);
        $this->assertNotNull($master);

        $response->assertRedirect(route('master-pipeline.timeline', $master->id));
        $response->assertSessionHas('success');

        $this->assertEquals($this->customer->id, $master->customer_id);
        $this->assertEquals('Avaniperur', $master->village);
    }

    /**
     * Test forward POST advances stage and logs audit trail.
     */
    public function test_forward_post_advances_stage(): void
    {
        $master = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Tharamangalam',
            'survey_numbers'        => '55/3',
            'area_extent_ha'        => 2.0000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        $response = $this->actingAs($this->officer)
            ->post(route('master-pipeline.forward', $master->id), [
                'to_department' => 'survey_dgps',
                'priority'      => 'normal',
                'reason'        => 'Lease documents vetted. Forwarding for field demarcation.',
            ]);

        $response->assertRedirect(route('master-pipeline.timeline', $master->id));
        $response->assertSessionHas('success');

        $freshMaster = $master->fresh();
        $this->assertEquals('survey_dgps', $freshMaster->current_stage);
        $this->assertEquals('in_progress', $freshMaster->pipeline_status);

        $this->assertDatabaseHas('pipeline_movements', [
            'master_project_id' => $master->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey_dgps',
            'action_type'       => 'Forward_Transfer',
        ]);
    }

    /**
     * Test returnForCorrection POST validates inputs, mutates status, and dispatches notification.
     */
    public function test_return_for_correction_validates_and_dispatches_alert(): void
    {
        $master = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Tharamangalam',
            'survey_numbers'        => '55/3',
            'area_extent_ha'        => 2.0000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'ppt',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $this->officer->id,
        ]);

        // Validation test: reason must be at least 5 characters
        $failResponse = $this->actingAs($this->officer)
            ->post(route('master-pipeline.return', $master->id), [
                'from_department' => 'ppt',
                'to_department'   => 'mining',
                'reason'          => 'bad',
                'priority'        => 'urgent',
            ]);
        $failResponse->assertSessionHasErrors(['reason']);

        // Valid return test
        $successResponse = $this->actingAs($this->officer)
            ->post(route('master-pipeline.return', $master->id), [
                'from_department'     => 'ppt',
                'to_department'       => 'mining',
                'reason'              => 'Production calculations in mining plan exceed environmental carrying capacity.',
                'priority'            => 'urgent',
                'mistake_category'    => 'Calculation Error in Production',
                'rectification_notes' => 'Reduce annual production schedule to 15,000 CBM.',
            ]);

        $successResponse->assertRedirect(route('master-pipeline.timeline', $master->id));
        $successResponse->assertSessionHas('alert');

        $freshMaster = $master->fresh();
        $this->assertEquals('mining', $freshMaster->current_stage);
        $this->assertEquals('returned_for_correction', $freshMaster->pipeline_status);

        $this->assertDatabaseHas('pipeline_movements', [
            'master_project_id' => $master->id,
            'from_department'   => 'ppt',
            'to_department'     => 'mining',
            'action_type'       => 'Returned_For_Correction',
            'priority'          => 'urgent',
            'mistake_category'  => 'Calculation Error in Production',
        ]);

        // Notification assertion
        $this->assertDatabaseHas('notifications', [
            'type'            => 'App\Notifications\ReturnedForCorrection',
            'notifiable_type' => User::class,
        ]);
    }

    /**
     * Test resubmit POST restores pipeline status and records audit movement.
     */
    public function test_resubmit_post_restores_status_and_logs_movement(): void
    {
        $master = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(),
            'customer_id'           => $this->customer->id,
            'branch_id'             => $this->branch->id,
            'district_id'           => $this->district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Tharamangalam',
            'survey_numbers'        => '55/3',
            'area_extent_ha'        => 2.0000,
            'mineral_name'          => 'Black Granite',
            'current_stage'         => 'mining',
            'pipeline_status'       => 'returned_for_correction',
            'created_by'            => $this->officer->id,
        ]);

        $response = $this->actingAs($this->officer)
            ->post(route('master-pipeline.resubmit', $master->id), [
                'from_department'     => 'mining',
                'to_department'       => 'ppt',
                'rectification_notes' => 'Production schedules revised to comply with SEAC threshold. Annexure VI uploaded.',
            ]);

        $response->assertRedirect(route('master-pipeline.timeline', $master->id));
        $response->assertSessionHas('success');

        $freshMaster = $master->fresh();
        $this->assertEquals('ppt', $freshMaster->current_stage);
        $this->assertEquals('in_progress', $freshMaster->pipeline_status);

        $this->assertDatabaseHas('pipeline_movements', [
            'master_project_id'   => $master->id,
            'from_department'     => 'mining',
            'to_department'       => 'ppt',
            'action_type'         => 'Resubmitted_Fix',
            'rectification_notes' => 'Production schedules revised to comply with SEAC threshold. Annexure VI uploaded.',
        ]);
    }
}
