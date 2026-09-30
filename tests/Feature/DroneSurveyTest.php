<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DroneSurvey;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DroneSurveyTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->admin = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))
            ->orWhere('role_id', 1)
            ->first() ?? User::factory()->create(['role_id' => 1]);

        $this->customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Test Drone Customer',
            'company_name'  => 'Test Drone Mines',
            'mobile_num'    => '9876543210',
            'status'        => 1,
            'branch_id'     => 1,
        ]);
    }

    /**
     * Test drone survey register renders with real dynamic records and KPI counts
     */
    public function test_drone_survey_index_renders_with_dynamic_records_and_kpis(): void
    {
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-TEST-DYN-' . rand(1000, 9999),
            'customer_id'          => $this->customer->id,
            'location'             => 'Salem Granite Quarry',
            'lease_area'           => 5.50,
            'survey_status'        => 'deliverables_ready',
            'extracted_volume_cbm' => 1250.75,
            'branch_id'            => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('drone-survey.index'));

        $response->assertStatus(200);
        $response->assertSee('Drone Department — Volumetric Survey');
        $response->assertSee('Survey Requests');
        $response->assertSee('Deliverables Ready');
        $response->assertSee($survey->survey_no);
        $response->assertSee(route('drone-survey.show', $survey->id));
        $response->assertDontSee('DRN-2026-0026'); // Old static dummy data must NOT exist
    }

    /**
     * Test search filter in drone survey register
     */
    public function test_drone_survey_index_search_filter(): void
    {
        $uniqueNo = 'DRN-SEARCH-' . rand(10000, 99999);
        DroneSurvey::create([
            'survey_no'     => $uniqueNo,
            'customer_id'   => $this->customer->id,
            'location'      => 'Unique Search Location',
            'survey_status' => 'scheduled',
            'branch_id'     => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('drone-survey.index', ['search' => $uniqueNo]));

        $response->assertStatus(200);
        $response->assertSee($uniqueNo);
    }

    /**
     * Test status filter in drone survey register
     */
    public function test_drone_survey_index_status_filter(): void
    {
        $readySurvey = DroneSurvey::create([
            'survey_no'     => 'DRN-READY-' . rand(10000, 99999),
            'customer_id'   => $this->customer->id,
            'survey_status' => 'deliverables_ready',
            'branch_id'     => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('drone-survey.index', ['status' => 'deliverables_ready']));

        $response->assertStatus(200);
        $response->assertSee($readySurvey->survey_no);
    }

    /**
     * Test show dossier view
     */
    public function test_drone_survey_show_dossier(): void
    {
        $survey = DroneSurvey::create([
            'survey_no'            => 'DRN-SHOW-' . rand(10000, 99999),
            'customer_id'          => $this->customer->id,
            'location'             => 'Dossier Site',
            'lease_area'           => 3.25,
            'survey_status'        => 'scheduled',
            'extracted_volume_cbm' => 500.0,
            'branch_id'            => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('drone-survey.show', $survey->id));

        $response->assertStatus(200);
        $response->assertSee($survey->survey_no);
        $response->assertSee('Drone Volumetric Survey');
    }
}
