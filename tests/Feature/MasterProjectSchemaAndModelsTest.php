<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\DroneSurvey;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\MasterProject;
use App\Models\MiningApplication;
use App\Models\PipelineMovement;
use App\Models\PptApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MasterProjectSchemaAndModelsTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function test_master_projects_table_has_all_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('master_projects'));

        $expectedColumns = [
            'id',
            'master_application_id',
            'customer_id',
            'branch_id',
            'district_id',
            'taluk',
            'village',
            'survey_numbers',
            'area_extent_ha',
            'mineral_name',
            'lease_application_id',
            'dgps_survey_id',
            'drone_survey_id',
            'mining_application_id',
            'environment_project_id',
            'ppt_application_id',
            'current_stage',
            'pipeline_status',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        foreach ($expectedColumns as $col) {
            $this->assertTrue(
                Schema::hasColumn('master_projects', $col),
                "Missing column [{$col}] in master_projects table."
            );
        }
    }

    #[Test]
    public function test_pipeline_movements_table_has_all_required_columns(): void
    {
        $this->assertTrue(Schema::hasTable('pipeline_movements'));

        $expectedColumns = [
            'id',
            'master_project_id',
            'from_department',
            'to_department',
            'action_type',
            'priority',
            'mistake_category',
            'reason',
            'rectification_notes',
            'officer_id',
            'officer_name',
            'metadata',
            'created_at',
            'updated_at',
        ];

        foreach ($expectedColumns as $col) {
            $this->assertTrue(
                Schema::hasColumn('pipeline_movements', $col),
                "Missing column [{$col}] in pipeline_movements table."
            );
        }
    }

    #[Test]
    public function test_all_department_tables_have_master_project_id_and_common_id(): void
    {
        $tables = [
            'lease_applications',
            'dgps_surveys',
            'drone_surveys',
            'mining_applications',
            'environment_projects',
            'ppt_applications',
        ];

        foreach ($tables as $tbl) {
            $this->assertTrue(
                Schema::hasColumn($tbl, 'master_project_id'),
                "Missing master_project_id column in [{$tbl}]."
            );
            $this->assertTrue(
                Schema::hasColumn($tbl, 'common_id'),
                "Missing common_id column in [{$tbl}]."
            );
        }
    }

    #[Test]
    public function test_mining_applications_status_column_allows_returned_for_correction(): void
    {
        $branch = Branch::first() ?? Branch::create(['branch_name' => 'HQ']);
        $district = District::first() ?? District::create(['name' => 'Salem']);
        $user = User::first() ?? User::factory()->create(['branch_id' => $branch->id]);
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Test Corp',
            'mobile_num'    => '9876543210',
            'branch_id'     => $branch->id,
            'created_by'    => $user->id,
        ]);

        $app = MiningApplication::create([
            'application_no' => 'MP-2026-9999',
            'common_id'      => 'GTMS-2026-9999',
            'customer_id'    => $customer->id,
            'district_id'    => $district->id,
            'status'         => 'returned_for_correction',
            'status_notes'   => 'Boundary overlaps with reserve forest. Please rectify.',
            'branch_id'      => $branch->id,
            'created_by'     => $user->id,
        ]);

        $this->assertDatabaseHas('mining_applications', [
            'id'           => $app->id,
            'status'       => 'returned_for_correction',
            'status_notes' => 'Boundary overlaps with reserve forest. Please rectify.',
        ]);
    }

    #[Test]
    public function test_master_project_generate_master_id(): void
    {
        $masterId = MasterProject::generateMasterId(2026);
        $this->assertMatchesRegularExpression('/^GTMS-2026-\d{4}$/', $masterId);
    }

    #[Test]
    public function test_master_project_model_creation_and_relationships(): void
    {
        $branch = Branch::first() ?? Branch::create(['branch_name' => 'Salem']);
        $district = District::first() ?? District::create(['name' => 'Salem']);
        $user = User::first() ?? User::factory()->create(['branch_id' => $branch->id]);
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Sri Granite Works',
            'mobile_num'    => '9840123456',
            'branch_id'     => $branch->id,
            'created_by'    => $user->id,
        ]);

        $masterId = MasterProject::generateMasterId(2026);

        $project = MasterProject::create([
            'master_application_id' => $masterId,
            'customer_id'           => $customer->id,
            'branch_id'             => $branch->id,
            'district_id'           => $district->id,
            'taluk'                 => 'Omalur',
            'village'               => 'Pagalpatti',
            'survey_numbers'        => '102/1A, 102/1B',
            'area_extent_ha'        => '02.45.00',
            'mineral_name'          => 'Rough Stone',
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $user->id,
        ]);

        $this->assertNotNull($project->id);
        $this->assertTrue($project->isActive());
        $this->assertFalse($project->isReturnedForCorrection());
        $this->assertEquals('Lease Application', $project->stage_label);

        // Test Movement relation
        $movement = PipelineMovement::create([
            'master_project_id' => $project->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey',
            'action_type'       => 'Forward_Transfer',
            'priority'          => 'normal',
            'reason'            => 'Lease verified, handing over for DGPS demarcation.',
            'officer_id'        => $user->id,
            'officer_name'      => $user->name,
            'metadata'          => ['survey_type' => 'DGPS'],
        ]);

        $this->assertTrue($movement->isForward());
        $this->assertFalse($movement->isReturn());
        $this->assertEquals('badge-primary bg-primary text-white', $movement->action_badge_class);

        // Reload project with movements
        $project->refresh();
        $this->assertCount(1, $project->movements);
        $this->assertEquals($movement->id, $project->latestMovement->id);
        $this->assertEquals($customer->id, $project->customer->id);
        $this->assertEquals($branch->id, $project->branch->id);
        $this->assertEquals($district->id, $project->district->id);
        $this->assertEquals($user->id, $project->creator->id);
    }

    #[Test]
    public function test_department_models_master_project_relationship(): void
    {
        $branch = Branch::first() ?? Branch::create(['branch_name' => 'HQ']);
        $user = User::first() ?? User::factory()->create(['branch_id' => $branch->id]);
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Apex Mines',
            'mobile_num'    => '9988776655',
            'branch_id'     => $branch->id,
            'created_by'    => $user->id,
        ]);

        $master = MasterProject::create([
            'master_application_id' => MasterProject::generateMasterId(2026),
            'customer_id'           => $customer->id,
            'branch_id'             => $branch->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $user->id,
        ]);

        $deptModels = [
            'LeaseApplication'   => new LeaseApplication(['master_project_id' => $master->id]),
            'DgpsSurvey'         => new DgpsSurvey(['master_project_id' => $master->id]),
            'DroneSurvey'        => new DroneSurvey(['master_project_id' => $master->id]),
            'MiningApplication'  => new MiningApplication(['master_project_id' => $master->id]),
            'EnvironmentProject' => new EnvironmentProject(['master_project_id' => $master->id]),
            'PptApplication'     => new PptApplication(['master_project_id' => $master->id]),
        ];

        foreach ($deptModels as $name => $model) {
            $relation = $model->masterProject();
            $this->assertInstanceOf(
                \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
                $relation,
                "[{$name}] masterProject() did not return BelongsTo relation."
            );
            $this->assertEquals('master_project_id', $relation->getForeignKeyName());
        }
    }
}
