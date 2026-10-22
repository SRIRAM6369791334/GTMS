<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\MimasCredential;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class MimasPasswordViewTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected LeaseApplication $leaseApp;
    protected MimasCredential $credential;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'application.view', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'application.create', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['application.view', 'application.create']);

        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);

        $this->customer = Customer::create([
            'customer_name' => 'Password View Test Customer',
            'mimas_no'      => 'TN-MMS-VIEW-01',
            'mobile_num'    => '9876543299',
            'district_id'   => $district->id,
            'mineral_id'    => $mineral->id,
            'status'        => 1,
        ]);

        $this->leaseApp = LeaseApplication::create([
            'application_no' => 'LA-2026-9999',
            'customer_id'    => $this->customer->id,
            'district_id'    => $district->id,
            'category_id'    => 1,
            'mineral_id'     => $mineral->id,
            'status'         => 'under_scrutiny',
            'contact_person' => 'Password View Test Customer',
            'contact_mobile' => '9876543299',
        ]);

        $this->credential = MimasCredential::create([
            'lease_application_id' => $this->leaseApp->id,
            'user_id'              => 'TN-MMS-VIEW-01',
            'password'             => 'SecretMimasPass@2026',
            'email'                => 'testpass@example.com',
            'contact_number'       => '9876543299',
            'portal_status'        => 'verified',
        ]);
    }

    /** @test */
    public function view_application_renders_mimas_password_with_toggle_and_copy_buttons()
    {
        $response = $this->actingAs($this->user)->get(route('viewapplication', ['id' => $this->leaseApp->id]));

        $response->assertStatus(200);
        $response->assertSee('mimas_pass_masked');
        $response->assertSee('mimas_pass_plain');
        $response->assertSee('SecretMimasPass@2026');
        $response->assertSee('btn_toggle_view_pass');
        $response->assertSee('btn_copy_view_pass');
    }

    /** @test */
    public function step2_renders_with_real_pass_data_attribute_for_decrypted_password()
    {
        session([
            'lease_draft' => [
                'application_id' => $this->leaseApp->id,
                'step1' => ['client_name' => 'Test'],
                'step2' => [
                    'contact_person' => 'Test',
                    'contact_mobile' => '9876543299',
                    'mimas_user_id' => 'TN-MMS-VIEW-01',
                    'mimas_password' => 'SecretMimasPass@2026',
                    'mimas_email' => 'testpass@example.com',
                    'mimas_contact' => '9876543299',
                ]
            ]
        ]);

        $response = $this->actingAs($this->user)->get(route('step2'));

        $response->assertStatus(200);
        $response->assertSee('data-real-pass="SecretMimasPass@2026"', false);
        $response->assertSee('btn_copy_password');
    }

    /** @test */
    public function step8_review_renders_decrypted_password_with_toggle_and_copy()
    {
        session([
            'lease_draft' => [
                'application_id' => $this->leaseApp->id,
                'step1' => ['client_name' => 'Test', 'district_id' => 1],
                'step2' => [
                    'contact_person' => 'Test',
                    'contact_mobile' => '9876543299',
                    'mimas_user_id' => 'TN-MMS-VIEW-01',
                    'mimas_password' => 'SecretMimasPass@2026',
                    'mimas_email' => 'testpass@example.com',
                    'mimas_contact' => '9876543299',
                ]
            ]
        ]);

        $response = $this->actingAs($this->user)->get(route('step8'));

        $response->assertStatus(200);
        $response->assertSee('btn_toggle_preview_pass');
        $response->assertSee('btn_copy_preview_pass');
        $response->assertSee('SecretMimasPass@2026');
    }

    /** @test */
    public function application_report_pdf_renders_decrypted_mimas_password()
    {
        $response = $this->actingAs($this->user)->get(route('application.report', ['id' => $this->leaseApp->id]));

        $response->assertStatus(200);
        $response->assertSee('Portal Password');
        $response->assertSee('SecretMimasPass@2026');
    }
}
