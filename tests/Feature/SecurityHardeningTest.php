<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

/**
 * Regression tests for the security hardening batch:
 * permissions on master pipeline / invoice seal, route order, version disclosure.
 */
class SecurityHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $noPermUser;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $branch = Branch::first() ?? Branch::create([
            'branch_name' => 'Security Test Branch',
            'status'      => 1,
        ]);

        // User with no role and no permissions.
        $this->noPermUser = User::create([
            'name'      => 'No Permission User',
            'email'     => 'noperm_' . Str::random(8) . '@gtms.test',
            'user_code' => 'NOP_' . rand(1000, 9999),
            'password'  => bcrypt(Str::random(16)),
            'branch_id' => $branch->id,
            'status'    => 1,
        ]);
    }

    public function test_master_pipeline_requires_department_permission(): void
    {
        $this->actingAs($this->noPermUser)->get(route('master-pipeline.index'))->assertStatus(403);
        $this->actingAs($this->noPermUser)->post(route('master-pipeline.forward', 1))->assertStatus(403);
        $this->actingAs($this->noPermUser)->post(route('master-pipeline.return', 1))->assertStatus(403);
        $this->actingAs($this->noPermUser)->post(route('master-pipeline.resubmit', 1))->assertStatus(403);
        $this->actingAs($this->noPermUser)->post(route('master-pipeline.create-from-lease', 1))->assertStatus(403);
    }

    public function test_create_from_lease_rejects_get(): void
    {
        $this->actingAs($this->noPermUser)->get('/master-pipeline/create-from-lease/1')->assertStatus(405);
    }

    public function test_invoice_seal_requires_account_permission(): void
    {
        $this->actingAs($this->noPermUser)->post(route('accounts.invoices.seal.upload'))->assertStatus(403);
        $this->actingAs($this->noPermUser)->post(route('accounts.invoices.seal.remove'))->assertStatus(403);
        $this->actingAs($this->noPermUser)->get(route('accounts.invoices.seal.status'))->assertStatus(403);
    }

    public function test_notifications_clear_all_route_is_reachable(): void
    {
        $route = app('router')->getRoutes()->match(
            \Illuminate\Http\Request::create('/notifications/clear-all', 'DELETE')
        );

        $this->assertSame('notifications.clear-all', $route->getName());
    }

    public function test_portal_passwords_are_encrypted_at_rest_and_legacy_plaintext_still_reads(): void
    {
        $customer = \App\Models\Customer::withoutGlobalScopes()->create([
            'customer_name'     => 'Encryption Test',
            'mobile_num'        => '9000000099',
            'parivesh_password' => 'Pv#Secret1',
            'email_password'    => 'Mail#Secret1',
        ]);

        $raw = \Illuminate\Support\Facades\DB::table('customers')->where('id', $customer->id)->first();
        $this->assertNotSame('Pv#Secret1', $raw->parivesh_password);
        $this->assertSame('Pv#Secret1', \Illuminate\Support\Facades\Crypt::decryptString($raw->parivesh_password));
        $this->assertSame('Mail#Secret1', $customer->fresh()->email_password);

        // Row not yet converted by the migration: plaintext must still read, not throw.
        \Illuminate\Support\Facades\DB::table('customers')->where('id', $customer->id)
            ->update(['parivesh_password' => 'legacy-plain']);
        $this->assertSame('legacy-plain', $customer->fresh()->parivesh_password);
    }

    public function test_pipeline_forward_to_completed_marks_pipeline_completed(): void
    {
        $service = app(\App\Services\MasterPipelineService::class);
        $master = $service->createStandaloneMasterProject(['customer_id' => null], 'ppt', $this->noPermUser);

        $service->forwardStage($master, 'completed', ['reason' => 'All approvals received'], $this->noPermUser);

        $this->assertSame('completed', $master->fresh()->pipeline_status);
        $this->assertSame('completed', $master->fresh()->current_stage);
    }

    public function test_standalone_master_ids_come_from_master_sequence(): void
    {
        $service = app(\App\Services\MasterPipelineService::class);
        $a = $service->createStandaloneMasterProject([], 'mining', $this->noPermUser);
        $b = $service->createStandaloneMasterProject([], 'mining', $this->noPermUser);

        $this->assertMatchesRegularExpression('/^GTMS-\d{4}-\d{4,}$/', $a->master_application_id);
        $this->assertNotSame($a->master_application_id, $b->master_application_id);
    }

    public function test_version_endpoint_does_not_disclose_runtime_versions(): void
    {
        $response = $this->getJson('/system/version')->assertOk();

        $this->assertArrayNotHasKey('php', $response->json());
        $this->assertArrayNotHasKey('laravel', $response->json());
    }
}
