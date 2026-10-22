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

    /**
     * Test all 6 wizard steps render successfully
     */
    public function test_all_six_wizard_steps_render_successfully(): void
    {
        for ($step = 1; $step <= 6; $step++) {
            $response = $this->actingAs($this->admin)->get(route('drone-survey.step', $step));
            $response->assertStatus(200);
            $response->assertSee('Step ' . $step . ' of 6');
        }
    }

    /**
     * Test out of bounds wizard step returns 404
     */
    public function test_wizard_step_out_of_bounds_returns_404(): void
    {
        $response = $this->actingAs($this->admin)->get(route('drone-survey.step', 7));
        $response->assertStatus(404);

        $responseZero = $this->actingAs($this->admin)->get(route('drone-survey.step', 0));
        $responseZero->assertStatus(404);
    }

    /**
     * Test step 1 saves and advances to step 2
     */
    public function test_wizard_save_step_1_advances_to_step_2(): void
    {
        $postData = [
            'survey_no'   => 'DRN-WIZ-001',
            'customer_id' => $this->customer->id,
            'location'    => 'Namakkal Quarry',
            'lease_area'  => 4.50,
            'survey_type' => 'Volumetric Topography',
        ];

        $response = $this->actingAs($this->admin)->post(route('drone-survey.saveStep', 1), $postData);
        $response->assertRedirect(route('drone-survey.step', 2));
        $response->assertSessionHas('drone_wizard.survey_no', 'DRN-WIZ-001');
        $response->assertSessionHas('drone_wizard.location', 'Namakkal Quarry');
    }

    /**
     * Test Save Draft and Exit redirects to index
     */
    public function test_wizard_save_draft_and_exit(): void
    {
        $postData = [
            'survey_no'       => 'DRN-DRAFT-999',
            'customer_id'     => $this->customer->id,
            'location'        => 'Draft Site',
            'save_draft_exit' => '1',
        ];

        $response = $this->actingAs($this->admin)->post(route('drone-survey.saveStep', 1), $postData);
        $response->assertRedirect(route('drone-survey.index'));
        $response->assertSessionHas('success', 'Drone survey progress saved to draft.');
    }

    /**
     * Test full wizard submission creates drone survey record
     */
    public function test_wizard_full_submission_creates_record(): void
    {
        // Populate session draft
        session([
            'drone_wizard' => [
                'survey_no'             => 'DRN-SUBMIT-' . rand(1000, 9999),
                'customer_id'           => $this->customer->id,
                'location'              => 'Salem Final Site',
                'lease_area'            => 6.20,
                'flight_date'           => '2026-10-25',
                'drone_pilot_name'      => 'Capt. Rajesh',
                'pilot_rpc_no'          => 'RPC-78491',
                'drone_uin_no'          => 'UIN-9921',
                'drone_model'           => 'DJI Matrice 350 RTK',
                'altitude_meters'       => 100,
                'gsd_cm_px'             => 2.1,
                'extracted_volume_cbm'  => 18500.50,
                'product_value'         => 75000,
                'paid_amount'           => 50000,
                'payment_status'        => 'partial',
                'handlers'              => [
                    ['person_name' => 'Senthil Kumar', 'role' => 'Drone Pilot', 'notes' => 'Primary Pilot'],
                    ['person_name' => 'Anand Raj', 'role' => 'GIS Analyst', 'notes' => 'Orthomosaic processing'],
                ],
            ]
        ]);

        $response = $this->actingAs($this->admin)->post(route('drone-survey.store'));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('drone_surveys', [
            'customer_id'     => $this->customer->id,
            'location'        => 'Salem Final Site',
            'drone_pilot_name'=> 'Capt. Rajesh',
            'product_value'   => 75000,
            'paid_amount'     => 50000,
            'pending_amount'  => 25000,
        ]);
    }
}
