<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\ApplicationHandler;
use App\Models\Customer;
use App\Models\District;
use App\Models\DroneSurvey;
use App\Models\EnvironmentProject;
use App\Models\Mineral;
use App\Models\PptApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Regression tests for the PPT / DGPS / Drone / EC / Environment audit fixes
 * (2026-11-14). All DB writes roll back (DatabaseTransactions).
 */
class OtherModulesAuditFixesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;

    private const PARIVESH = 'Pv#AuditSecret1';
    private const EMAILPASS = 'Em#AuditSecret2';

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::whereHas('roles', fn ($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();
        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $this->user->assignRole(\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']));
        }
        $this->actingAs($this->user);

        $this->customer = Customer::create([
            'customer_name'     => 'Audit Portal Client',
            'company_name'      => 'Audit Portal Co',
            'mimas_no'          => 'TN-MMS-AUDP-' . rand(100000, 999999),
            'mobile_num'        => '9876501234',
            'district_id'       => District::value('id'),
            'status'            => 1,
            'parivesh_password' => self::PARIVESH,
            'email_password'    => self::EMAILPASS,
        ]);
    }

    private function makeProject(array $extra = []): EnvironmentProject
    {
        return EnvironmentProject::create($extra + [
            'project_code'      => 'ENV-B2-AUD-' . rand(100000, 999999),
            'customer_id'       => $this->customer->id,
            'category'          => 'B2',
            'project_name'      => 'Audit Env Project',
            'district_id'       => District::value('id'),
            'contact_phone'     => '9876501234',
            'status'            => 'draft',
            'parivesh_password' => self::PARIVESH,
            'email_password'    => self::EMAILPASS,
        ]);
    }

    // ── Passwords never printed into HTML ────────────────────────────

    public function test_wizards_and_dossiers_do_not_print_portal_passwords(): void
    {
        $project = $this->makeProject();

        foreach ([
            route('ppt-department.step', ['step' => 1, 'new' => 1]),
            route('ec-certificate.step', 1),
            route('eviron.create'),
            route('eviron.show', $project->id),
        ] as $url) {
            $this->get($url)->assertOk()->assertDontSee(self::PARIVESH)->assertDontSee(self::EMAILPASS);
        }
    }

    public function test_mimas_lookup_does_not_return_passwords(): void
    {
        $this->getJson(route('customers.lookup.mimas', $this->customer->mimas_no))
            ->assertOk()
            ->assertJsonMissing(['parivesh_password' => self::PARIVESH]);
    }

    public function test_credentials_endpoint_returns_passwords_and_logs(): void
    {
        $project = $this->makeProject();

        $this->postJson(route('portal-credentials.show', ['type' => 'environment', 'id' => $project->id]))
            ->assertOk()
            ->assertJson(['parivesh_password' => self::PARIVESH, 'email_password' => self::EMAILPASS]);

        $this->postJson(route('portal-credentials.show', ['type' => 'customer', 'id' => $this->customer->id]))
            ->assertOk()
            ->assertJson(['parivesh_password' => self::PARIVESH]);

        $this->assertTrue(ActivityLog::where('action', 'portal_credentials_viewed')
            ->where('loggable_id', $project->id)->exists());
    }

    // ── Wizard stores ────────────────────────────────────────────────

    public function test_ppt_store_without_customer_is_rejected_and_creates_nothing(): void
    {
        $before = PptApplication::withTrashed()->count();
        $this->withSession(['ppt_wizard' => ['project_name' => 'X']])
            ->post(route('ppt-department.store'))
            ->assertRedirect(route('ppt-department.step', 1))
            ->assertSessionHasErrors('customer_id');
        $this->assertSame($before, PptApplication::withTrashed()->count());
    }

    public function test_ppt_store_uses_zero_not_fake_amounts_and_resume_updates_same_record(): void
    {
        $draft = [
            'customer_id'  => $this->customer->id,
            'project_name' => 'Audit PPT Project',
            'district_id'  => District::value('id'),
            'mineral_id'   => Mineral::value('id'),
        ];
        $this->withSession(['ppt_wizard' => $draft])->post(route('ppt-department.store'))->assertSessionHasNoErrors();

        $ppt = PptApplication::where('project_name', 'Audit PPT Project')->latest('id')->first();
        $this->assertNotNull($ppt);
        $this->assertEquals(0, (float) $ppt->product_value);
        $this->assertSame('pending', $ppt->payment_status);
        $this->assertSame(0, ApplicationHandler::where('application_type', 'ppt')->where('application_id', $ppt->id)->count());

        $before = PptApplication::withTrashed()->count();
        $this->withSession(['ppt_wizard' => array_merge($draft, ['id' => $ppt->id, 'project_name' => 'Audit PPT Project Edited'])])
            ->post(route('ppt-department.store'))->assertSessionHasNoErrors();
        $this->assertSame($before, PptApplication::withTrashed()->count());
        $this->assertSame('Audit PPT Project Edited', $ppt->fresh()->project_name);
        $this->assertSame($ppt->application_no, $ppt->fresh()->application_no);
    }

    public function test_ppt_store_rejects_amounts_that_do_not_match_status(): void
    {
        $this->withSession(['ppt_wizard' => [
            'customer_id'    => $this->customer->id,
            'project_name'   => 'Audit PPT Pay',
            'district_id'    => District::value('id'),
            'mineral_id'     => Mineral::value('id'),
            'product_value'  => 1000,
            'paid_amount'    => 200,
            'payment_status' => 'paid',
        ]])->post(route('ppt-department.store'))
            ->assertRedirect(route('ppt-department.step', 8))
            ->assertSessionHasErrors('payment_status');
    }

    public function test_drone_store_does_not_invent_pilot_or_handlers(): void
    {
        $no = 'DRN-AUD-' . rand(100000, 999999);
        $this->withSession(['drone_wizard' => ['customer_id' => $this->customer->id, 'survey_no' => $no]])
            ->post(route('drone-survey.store'))->assertSessionHasNoErrors();

        $survey = DroneSurvey::where('survey_no', $no)->first();
        $this->assertNotNull($survey);
        $this->assertNull($survey->drone_pilot_name);
        $this->assertNull($survey->extracted_volume_cbm);
        $this->assertEquals(0, (float) $survey->product_value);
        $this->assertSame(0, ApplicationHandler::where('application_type', 'drone')->where('application_id', $survey->id)->count());
    }

    // ── Status gates ─────────────────────────────────────────────────

    public function test_status_updates_only_accept_known_values(): void
    {
        $project = $this->makeProject();
        $this->post(route('eviron.status', $project->id), ['status' => 'hacked'])->assertSessionHasErrors('status');

        $ppt = PptApplication::create([
            'application_no' => 'PPT-AUD-' . rand(100000, 999999),
            'customer_id'    => $this->customer->id,
            'project_name'   => 'Audit PPT',
            'district_id'    => District::value('id'),
            'mineral_id'     => Mineral::value('id'),
            'status'         => 'agenda_scheduled',
        ]);
        $this->post(route('ppt-department.status', $ppt->id), ['status' => 'whatever'])->assertSessionHasErrors('status');
    }

    public function test_environment_approval_needs_documents(): void
    {
        $project = $this->makeProject();
        $this->post(route('eviron.status', $project->id), ['status' => 'approved'])->assertSessionHas('error');
        $this->assertSame('draft', $project->fresh()->status);
    }

    public function test_ppt_approval_runs_once(): void
    {
        $project = $this->makeProject(['status' => 'approved', 'b1_stage' => null]);
        $ppt = PptApplication::create([
            'application_no'         => 'PPT-AUD-' . rand(100000, 999999),
            'customer_id'            => $this->customer->id,
            'environment_project_id' => $project->id,
            'presentation_stage'     => 'b2_presentation',
            'project_name'           => 'Audit B2 PPT',
            'district_id'            => District::value('id'),
            'mineral_id'             => Mineral::value('id'),
            'status'                 => 'agenda_scheduled',
        ]);

        $this->post(route('ppt-department.approveStage', $ppt->id))->assertSessionHas('success');
        $this->post(route('ppt-department.approveStage', $ppt->id))->assertSessionHas('info');

        $this->assertSame(1, ActivityLog::where('action', 'b2_approved_by_ppt')->where('loggable_id', $project->id)->count());
    }

    public function test_environment_intake_validates_phone(): void
    {
        $this->post(route('eviron.store'), [
            'category'      => 'B2',
            'customer_id'   => $this->customer->id,
            'client_name'   => 'Audit Portal Client',
            'project_name'  => 'Audit Phone Check',
            'district_id'   => District::value('id'),
            'contact_phone' => '12345',
        ])->assertSessionHasErrors('contact_phone');
    }
}
