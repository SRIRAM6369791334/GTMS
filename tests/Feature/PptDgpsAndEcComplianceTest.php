<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\PptApplication;
use App\Models\DgpsSurvey;
use App\Models\EcCompliance;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PptDgpsAndEcComplianceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::first() ?: User::factory()->create();

        $this->customer = Customer::first() ?: Customer::create([
            'customer_name' => 'Kaveri Granites Pvt Ltd',
            'company_name'  => 'Kaveri Granites Pvt Ltd',
            'mimas_no'      => 'TN/MMS/SLM/' . rand(100, 999),
            'mobile_num'    => '9842109876',
            'pan'           => 'ABCDE' . rand(1000, 9999) . 'F',
            'aadhaar_no'    => '9842-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'slug'          => 'kaveri-granites-' . rand(1000, 9999),
            'status'        => 'active',
        ]);
    }

    /**
     * Test PPT Department index and dossier view
     */
    public function test_ppt_department_index_and_dossier(): void
    {
        $response = $this->actingAs($this->user)->get(route('ppt-department.index'));
        $response->assertStatus(200);
        $response->assertSee('PPT Department');
        $response->assertSee('Total Applications');

        $ppt = PptApplication::first();
        if ($ppt) {
            $showRes = $this->actingAs($this->user)->get(route('ppt-department.show', $ppt->id));
            $showRes->assertStatus(200);
            $showRes->assertSee($ppt->application_no);
            $showRes->assertSee('Statutory Presentation Folders');
        }
    }

    /**
     * Test PPT Department Wizard Steps 1 to 9
     */
    public function test_ppt_department_wizard_steps(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            $res = $this->actingAs($this->user)->get(route('ppt-department.step', $i));
            $res->assertStatus(200);
        }

        // Test saving step 1
        $saveRes = $this->actingAs($this->user)->post(route('ppt-department.saveStep', 1), [
            'customer_id'    => $this->customer->id,
            'project_name'   => 'Automated Test Presentation Project',
            'application_no' => 'PPT-2026-TEST-' . rand(100, 999),
        ]);
        $saveRes->assertRedirect(route('ppt-department.step', 2));
    }

    /**
     * Test DGPS Survey index and dossier view
     */
    public function test_dgps_survey_index_and_dossier(): void
    {
        $response = $this->actingAs($this->user)->get(route('dgps-survey.index'));
        $response->assertStatus(200);
        $response->assertSee('DGPS Department');
        $response->assertSee('Survey Requests Register');

        $survey = DgpsSurvey::first();
        if ($survey) {
            $showRes = $this->actingAs($this->user)->get(route('dgps-survey.show', $survey->id));
            $showRes->assertStatus(200);
            $showRes->assertSee($survey->survey_no);
            $showRes->assertSee('Statutory DGPS Survey Deliverables');
        }
    }

    /**
     * Test DGPS Survey Wizard Steps 1 to 8
     */
    public function test_dgps_survey_wizard_steps(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $res = $this->actingAs($this->user)->get(route('dgps-survey.step', $i));
            $res->assertStatus(200);
        }

        // Test saving step 1
        $saveRes = $this->actingAs($this->user)->post(route('dgps-survey.saveStep', 1), [
            'customer_id'   => $this->customer->id,
            'lease_area_ha' => 4.25,
            'location'      => 'Salem Test Village',
            'survey_no'     => 'DGPS-2026-TEST-' . rand(100, 999),
        ]);
        $saveRes->assertRedirect(route('dgps-survey.step', 2));
    }

    /**
     * Test EC Half-Yearly Compliance index and dossier view
     */
    public function test_ec_compliance_index_and_dossier(): void
    {
        $response = $this->actingAs($this->user)->get(route('ec-compliance.index'));
        $response->assertStatus(200);
        $response->assertSee('Half Yearly Compliance');
        $response->assertSee('Total Filings');

        $comp = EcCompliance::first();
        if ($comp) {
            $showRes = $this->actingAs($this->user)->get(route('ec-compliance.show', $comp->id));
            $showRes->assertStatus(200);
            $showRes->assertSee($comp->compliance_no);
            $showRes->assertSee('Statutory Documents Checklist (19 Items)');
            $showRes->assertSee('Site Analysis Study');
        }
    }

    /**
     * Test EC Half-Yearly Compliance Wizard Steps 1 to 8
     */
    public function test_ec_compliance_wizard_steps(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $res = $this->actingAs($this->user)->get(route('ec-compliance.step', $i));
            $res->assertStatus(200);
        }

        // Test saving step 1
        $saveRes = $this->actingAs($this->user)->post(route('ec-compliance.saveStep', 1), [
            'customer_id'         => $this->customer->id,
            'project_name'        => 'Test EC Half Yearly Compliance Project',
            'compliance_period'   => 'April 2026 - September 2026',
            'submission_due_date' => '2026-12-01',
            'compliance_no'       => 'HYC-2026-TEST-' . rand(100, 999),
        ]);
        $saveRes->assertRedirect(route('ec-compliance.step', 2));
    }
}
