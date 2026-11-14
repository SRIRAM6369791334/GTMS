<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\LeaseDocument;
use App\Models\MimasCredential;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Regression tests for the Lease / Mining page audit fixes (2026-11-14).
 * All DB writes roll back (DatabaseTransactions).
 */
class LeaseMiningAuditFixesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected array $createdDirs = [];

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
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdDirs as $dir) {
            if (is_dir($dir)) {
                File::deleteDirectory($dir);
            }
        }
        parent::tearDown();
    }

    private function makeCustomer(): Customer
    {
        $seq = rand(100000, 999999);
        return Customer::create([
            'customer_name' => 'Audit Test Client',
            'company_name'  => 'Audit Test Co',
            'mimas_no'      => 'TN-MMS-AUD-' . $seq,
            'mobile_num'    => '9876543210',
            'district_id'   => District::value('id'),
            'status'        => 1,
        ]);
    }

    private function makeLease(string $status, ?string $appNo = null): LeaseApplication
    {
        $customer = $this->makeCustomer();
        $appNo = $appNo ?? ('LA-' . date('Y') . '-' . rand(10000000, 99999999));
        return LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id'    => $customer->id,
            'district_id'    => District::value('id'),
            'category_id'    => DB::table('lease_categories')->value('id'),
            'mineral_id'     => Mineral::value('id'),
            'contact_person' => 'Audit Test Client',
            'contact_mobile' => '9876543210',
            'status'         => $status,
            'current_step'   => 7,
            'created_by'     => $this->user->id,
        ]);
    }

    private function step1Draft(LeaseApplication $lease): array
    {
        return [
            'customer_id' => $lease->customer_id,
            'client_name' => 'Audit Test Client',
            'mimas_no'    => $lease->customer->mimas_no,
            'mobile_num'  => '9876543210',
            'district_id' => $lease->district_id,
            'mineral_id'  => $lease->mineral_id,
            'mineral_ids' => [$lease->mineral_id],
        ];
    }

    private function makeMining(): MiningApplication
    {
        $customer = $this->makeCustomer();
        return MiningApplication::create([
            'application_no' => 'MP-AUD-' . rand(10000000, 99999999),
            'customer_id'    => $customer->id,
            'district_id'    => District::value('id'),
            'mineral_id'     => Mineral::value('id'),
            'plan_type_id'   => DB::table('plan_types')->value('id'),
            'stage'          => '6.2',
            'status'         => 'scrutiny',
            'created_by'     => $this->user->id,
        ]);
    }

    // ── MIMAS password ────────────────────────────────────────────────

    public function test_password_is_not_printed_in_report_or_dossier_html(): void
    {
        $lease = $this->makeLease('under_scrutiny');
        MimasCredential::create(['lease_application_id' => $lease->id, 'user_id' => 'aud', 'password' => 'Secret#Audit9']);

        $this->get(route('application.report', $lease->id))
            ->assertOk()
            ->assertDontSee('Secret#Audit9')
            ->assertDontSee('sriramsri@001');

        $this->get(route('viewapplication', ['id' => $lease->id]))
            ->assertOk()
            ->assertDontSee('Secret#Audit9');
    }

    public function test_password_endpoint_returns_password_and_logs_the_reveal(): void
    {
        $lease = $this->makeLease('under_scrutiny');
        MimasCredential::create(['lease_application_id' => $lease->id, 'user_id' => 'aud', 'password' => 'Secret#Audit9']);

        $this->postJson(route('application.mimasPassword', $lease->id))
            ->assertOk()
            ->assertJson(['status' => 1, 'password' => 'Secret#Audit9']);

        $this->assertTrue(ActivityLog::where('loggable_type', 'lease_application')
            ->where('loggable_id', $lease->id)
            ->where('action', 'mimas_password_viewed')
            ->exists());
    }

    public function test_viewapplication_without_id_goes_back_to_the_list(): void
    {
        $this->get(route('viewapplication'))->assertRedirect('/application');
    }

    // ── Submit guards ─────────────────────────────────────────────────

    public function test_submit_without_a_draft_creates_nothing(): void
    {
        $before = Customer::count();
        $this->withSession(['lease_draft' => []])
            ->post(route('application.submit'))
            ->assertRedirect(route('step1'));
        $this->assertSame($before, Customer::count());
        $this->assertFalse(Customer::where('pan', 'PANNA0000P')->where('created_at', '>=', now()->subMinute())->exists());
    }

    public function test_submit_of_an_already_submitted_application_is_blocked(): void
    {
        $lease = $this->makeLease('under_scrutiny');
        $no = $lease->application_no;

        $this->withSession(['lease_draft' => ['application_id' => $lease->id, 'step1' => $this->step1Draft($lease)]])
            ->post(route('application.submit'))
            ->assertRedirect(route('viewapplication', ['id' => $lease->id]));

        $this->assertSame($no, $lease->fresh()->application_no);
    }

    public function test_resubmit_after_revision_keeps_the_official_number_and_custom_docs_are_not_duplicated(): void
    {
        $lease = $this->makeLease('revision_required');
        $no = $lease->application_no;
        $dir = public_path('uploads/lease_applications/' . $no);
        $this->createdDirs[] = $dir;
        File::ensureDirectoryExists($dir);
        file_put_contents($dir . '/custom_audit.pdf', '%PDF-1.4 audit');

        $custom = LeaseDocument::create([
            'lease_application_id' => $lease->id,
            'folder_id'            => 7,
            'document_field_id'    => null,
            'document_name'        => 'Audit Custom Doc',
            'file_name'            => 'custom_audit.pdf',
            'file_path'            => 'uploads/lease_applications/' . $no . '/custom_audit.pdf',
            'file_type'            => 'application/pdf',
            'file_size'            => 14,
            'status'               => 'uploaded',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        $draft = [
            'application_id' => $lease->id,
            'application_no' => $no,
            'step1'          => $this->step1Draft($lease),
            'uploaded_docs'  => [
                'custom_' . $custom->id => [
                    'doc_name'   => 'Audit Custom Doc',
                    'is_custom'  => true,
                    'folder_id'  => 7,
                    'file_type'  => 'application/pdf',
                    'draft_path' => $custom->file_path,
                    'file_path'  => $custom->file_path,
                ],
            ],
        ];

        $this->withSession(['lease_draft' => $draft])->post(route('application.submit'))->assertRedirect('/application');

        $fresh = $lease->fresh();
        $this->assertSame($no, $fresh->application_no);
        $this->assertSame('under_scrutiny', $fresh->status);
        $this->assertSame(1, LeaseDocument::where('lease_application_id', $lease->id)
            ->whereNull('document_field_id')->where('document_name', 'Audit Custom Doc')->count());
    }

    public function test_wizard_step_save_cannot_change_a_submitted_application(): void
    {
        $lease = $this->makeLease('approved');

        $this->withSession(['lease_draft' => ['application_id' => $lease->id]])
            ->postJson(route('step6.save'), ['handlers' => [['name' => 'X']]])
            ->assertStatus(422);
    }

    // ── Payment consistency ──────────────────────────────────────────

    public function test_step7_rejects_amounts_that_do_not_match_the_status(): void
    {
        $lease = $this->makeLease('draft', 'LA-DRAFT-' . date('Y') . '-' . rand(1000000, 9999999));
        $session = ['lease_draft' => ['application_id' => $lease->id]];

        $this->withSession($session)
            ->postJson(route('step7.save'), ['product_value' => 1000, 'paid_amount' => 1500, 'payment_status' => 'paid'])
            ->assertStatus(422)->assertJsonValidationErrors('paid_amount');

        $this->withSession($session)
            ->postJson(route('step7.save'), ['product_value' => 1000, 'paid_amount' => 400, 'payment_status' => 'paid'])
            ->assertStatus(422)->assertJsonValidationErrors('payment_status');

        $this->withSession($session)
            ->postJson(route('step7.save'), ['product_value' => 1000, 'paid_amount' => 400, 'payment_status' => 'partial'])
            ->assertOk();
    }

    // ── Lease review gates ───────────────────────────────────────────

    public function test_validate_and_approve_follow_the_status_order(): void
    {
        $draft = $this->makeLease('draft', 'LA-DRAFT-' . date('Y') . '-' . rand(1000000, 9999999));
        $this->post(route('application.validate', $draft->id), ['action' => 'pass']);
        $this->assertSame('draft', $draft->fresh()->status);

        $scrutiny = $this->makeLease('under_scrutiny');
        $this->post(route('application.approve', $scrutiny->id));
        $this->assertSame('under_scrutiny', $scrutiny->fresh()->status);

        $this->post(route('application.validate', $scrutiny->id), ['action' => 'pass']);
        $this->assertSame('validated', $scrutiny->fresh()->status);
        $this->post(route('application.approve', $scrutiny->id));
        $this->assertSame('approved', $scrutiny->fresh()->status);
    }

    // ── Mining stage / environment gates ─────────────────────────────

    public function test_mining_stage_must_be_valid_and_approval_needs_validated_documents(): void
    {
        $mining = $this->makeMining();
        $doc = MiningDocument::create([
            'mining_application_id' => $mining->id,
            'folder_id'             => DB::table('folders')->value('id'),
            'document_name'         => 'Audit Mining Doc',
            'status'                => 'uploaded',
        ]);

        $this->post(route('mining.application.stage', $mining->id), ['stage' => 'hack'])
            ->assertSessionHasErrors('stage');

        $this->post(route('mining.application.stage', $mining->id), ['stage' => '6.3'])
            ->assertSessionHas('error');
        $this->assertSame('6.2', $mining->fresh()->stage);

        $this->post(route('mining.application.moveToEnvironment', $mining->id))->assertSessionHas('error');
        $this->assertFalse(EnvironmentProject::where('mining_application_id', $mining->id)->exists());

        $doc->update(['status' => 'validated']);
        $this->post(route('mining.application.stage', $mining->id), ['stage' => '6.3'])
            ->assertSessionHas('success');
        $this->assertSame('6.3', $mining->fresh()->stage);
        $this->assertSame('approved', $mining->fresh()->status);
    }

    public function test_mining_aadhaar_is_stored_in_the_lease_format(): void
    {
        $request = \Illuminate\Http\Request::create('/', 'POST', ['aadhaar_no' => '987654321012']);
        $controller = app(\App\Http\Controllers\MiningController::class);
        $method = new \ReflectionMethod($controller, 'normalizeIdentityInput');
        $method->setAccessible(true);
        $method->invoke($controller, $request);

        $this->assertSame('9876-5432-1012', $request->input('aadhaar_no'));
    }
}
