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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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

        $this->user = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }

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
        Storage::fake('public');

        for ($i = 1; $i <= 8; $i++) {
            $res = $this->actingAs($this->user)->get(route('ec-compliance.step', $i));
            $res->assertStatus(200);
        }

        // Verify Step 1 renders manual input box for environment project, certificate upload, and cycle/year selectors
        $step1View = $this->actingAs($this->user)->get(route('ec-compliance.step', 1));
        $step1View->assertSee('name="environment_project_name"', false);
        $step1View->assertSee('name="ec_certificate_file"', false);
        $step1View->assertSee('name="primary_contact_person"', false);
        $step1View->assertSee('name="secondary_contact_person"', false);
        $step1View->assertSee('id="select_compliance_cycle"', false);
        $step1View->assertSee('id="select_compliance_year"', false);
        $step1View->assertSee('id="field_compliance_period"', false);
        $step1View->assertSee('April – September (H1 Period)', false);
        $step1View->assertSee('October – March (H2 Period)', false);
        $step1View->assertSee('type="file"', false);

        // Test saving step 1 with manual project name and uploaded certificate file
        $fakeCert = UploadedFile::fake()->create('prior_ec_clearance_order.pdf', 200, 'application/pdf');

        $saveRes = $this->actingAs($this->user)->post(route('ec-compliance.saveStep', 1), [
            'customer_id'              => $this->customer->id,
            'primary_contact_person'   => 'R. Sundararajan',
            'primary_phone'            => '9876543210',
            'secondary_contact_person' => 'K. Manickam',
            'secondary_phone'          => '9123456780',
            'environment_project_name' => 'Kaveri Rough Stone EC Project Phase 2',
            'ec_certificate_file'      => $fakeCert,
            'project_name'             => 'Test EC Half Yearly Compliance Project',
            'compliance_period'        => 'October 2026 - March 2027',
            'compliance_year'          => '2026',
            'submission_due_date'      => '2027-06-01',
            'compliance_no'            => 'HYC-2026-TEST-' . rand(100, 999),
        ]);
        $saveRes->assertRedirect(route('ec-compliance.step', 2));

        // Verify draft in session
        $draft = session('ec_compliance_wizard');
        $this->assertEquals('R. Sundararajan', $draft['primary_contact_person']);
        $this->assertEquals('9876543210', $draft['primary_phone']);
        $this->assertEquals('K. Manickam', $draft['secondary_contact_person']);
        $this->assertEquals('9123456780', $draft['secondary_phone']);
        $this->assertEquals('October 2026 - March 2027', $draft['compliance_period']);
        $this->assertEquals('2026', $draft['compliance_year']);
        $this->assertEquals('2027-06-01', $draft['submission_due_date']);
        $this->assertEquals('Kaveri Rough Stone EC Project Phase 2', $draft['environment_project_name']);
        $this->assertNotEmpty($draft['ec_certificate_file']);
        $this->assertEquals('prior_ec_clearance_order.pdf', $draft['ec_certificate_name']);
        Storage::disk('public')->assertExists($draft['ec_certificate_file']);

        // Test store saves to database
        $storeRes = $this->actingAs($this->user)->post(route('ec-compliance.store'), [
            'customer_id'              => $this->customer->id,
            'primary_contact_person'   => 'R. Sundararajan',
            'primary_phone'            => '9876543210',
            'secondary_contact_person' => 'K. Manickam',
            'secondary_phone'          => '9123456780',
            'environment_project_name' => 'Kaveri Rough Stone EC Project Phase 2',
            'project_name'             => 'Final Test EC Compliance Project',
        ]);

        $created = EcCompliance::where('project_name', 'Final Test EC Compliance Project')->latest()->first();
        $this->assertNotNull($created);
        $this->assertEquals('R. Sundararajan', $created->primary_contact_person);
        $this->assertEquals('9876543210', $created->primary_phone);
        $this->assertEquals('K. Manickam', $created->secondary_contact_person);
        $this->assertEquals('9123456780', $created->secondary_phone);
        $this->assertEquals('October 2026 - March 2027', $created->compliance_period);
        $this->assertEquals('2026', $created->compliance_year);
        $this->assertEquals('Kaveri Rough Stone EC Project Phase 2', $created->environment_project_name);
        $this->assertNotNull($created->ec_certificate_file);
        $this->assertEquals('prior_ec_clearance_order.pdf', $created->ec_certificate_name);

        // Verify document entry was also created in ec_compliance_documents
        $this->assertDatabaseHas('ec_compliance_documents', [
            'ec_compliance_id' => $created->id,
            'document_name'    => 'Prior Environmental Clearance (EC) Certificate',
            'file_name'        => 'prior_ec_clearance_order.pdf',
        ]);
    }
}
