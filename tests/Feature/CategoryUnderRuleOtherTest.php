<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CategoryUnderRuleOtherTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected District $district;
    protected Mineral $mineral;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'application.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'application.view', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['application.create', 'application.view']);

        $this->district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $this->mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);
    }

    /** @test */
    public function step3_renders_standard_categories_and_other_tile()
    {
        $response = $this->actingAs($this->user)->get(route('step3'));

        $response->assertStatus(200);
        $response->assertSee('Other Rule');
        $response->assertSee('cat_other');
        $response->assertSee('other_category_box');
        $response->assertSee('field_other_category_rule');
    }

    /** @test */
    public function step3_saves_standard_category()
    {
        $standardCat = LeaseCategory::where('is_custom', false)->first()
            ?? LeaseCategory::create(['code' => 'Rule 44', 'name' => 'TNMIR Rule 44', 'is_custom' => false]);

        $response = $this->actingAs($this->user)->postJson(route('step3.save'), [
            'category_id' => $standardCat->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'redirect' => '/step4',
        ]);

        $draft = session('lease_draft');
        $this->assertEquals($standardCat->id, $draft['step3']['category_id']);
        $this->assertFalse($draft['step3']['is_other']);
        $this->assertNull($draft['step3']['other_category_rule']);
    }

    /** @test */
    public function step3_saves_custom_other_rule_and_creates_lease_category()
    {
        $customRule = 'Rule 38-A Special Mining Order';

        $response = $this->actingAs($this->user)->postJson(route('step3.save'), [
            'category_id'         => 'other',
            'other_category_rule' => $customRule,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status'   => 1,
            'redirect' => '/step4',
        ]);

        $this->assertDatabaseHas('lease_categories', [
            'code'      => $customRule,
            'name'      => $customRule,
            'is_custom' => 1,
        ]);

        $createdCat = LeaseCategory::where('code', $customRule)->first();
        $this->assertNotNull($createdCat);

        $draft = session('lease_draft');
        $this->assertEquals($createdCat->id, $draft['step3']['category_id']);
        $this->assertTrue($draft['step3']['is_other']);
        $this->assertEquals($customRule, $draft['step3']['other_category_rule']);
    }

    /** @test */
    public function step3_fails_validation_when_other_is_selected_without_rule_name()
    {
        $response = $this->actingAs($this->user)->postJson(route('step3.save'), [
            'category_id'         => 'other',
            'other_category_rule' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['other_category_rule']);
    }

    /** @test */
    public function step3_preserves_custom_other_rule_on_reload()
    {
        $customRule = 'Rule 19-B Concession';
        $cat = LeaseCategory::create([
            'code'      => $customRule,
            'name'      => $customRule,
            'land_type' => 'Both',
            'status'    => 1,
            'is_custom' => true,
        ]);

        $sessionData = [
            'lease_draft' => [
                'step3' => [
                    'category_id'         => $cat->id,
                    'is_other'            => true,
                    'other_category_rule' => $customRule,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->withSession($sessionData)->get(route('step3'));

        $response->assertStatus(200);
        $response->assertSee('id="cat_other" value="other" class="d-none cat-radio" checked', false);
        $response->assertSee('value="Rule 19-B Concession"', false);
    }

    /** @test */
    public function step8_review_displays_custom_category_rule()
    {
        $customRule = 'Rule 38-A Special Mining Order';
        $cat = LeaseCategory::create([
            'code'      => $customRule,
            'name'      => $customRule,
            'land_type' => 'Both',
            'status'    => 1,
            'is_custom' => true,
        ]);

        session([
            'lease_draft' => [
                'step1' => ['client_name' => 'Test Quarry Client', 'district_id' => $this->district->id],
                'step2' => ['contact_person' => 'Test', 'contact_mobile' => '9876543210'],
                'step3' => [
                    'category_id'         => $cat->id,
                    'is_other'            => true,
                    'other_category_rule' => $customRule,
                ]
            ]
        ]);

        $response = $this->actingAs($this->user)->get(route('step8'));

        $response->assertStatus(200);
        $response->assertSee($customRule);
    }

    /** @test */
    public function view_application_and_report_display_custom_category_rule()
    {
        $customRule = 'Rule 38-A Special Mining Order';
        $cat = LeaseCategory::create([
            'code'      => $customRule,
            'name'      => $customRule,
            'land_type' => 'Both',
            'status'    => 1,
            'is_custom' => true,
        ]);

        $customer = Customer::create([
            'customer_name' => 'Custom Rule Test Customer',
            'mobile_num'    => '9876543210',
            'district_id'   => $this->district->id,
            'status'        => 1,
        ]);

        $leaseApp = LeaseApplication::create([
            'application_no'      => 'LA-2026-CR01',
            'customer_id'         => $customer->id,
            'district_id'         => $this->district->id,
            'category_id'         => $cat->id,
            'other_category_rule' => $customRule,
            'mineral_id'          => $this->mineral->id,
            'status'              => 'under_scrutiny',
            'contact_person'      => 'Test',
            'contact_mobile'      => '9876543210',
        ]);

        $viewResponse = $this->actingAs($this->user)->get(route('viewapplication', ['id' => $leaseApp->id]));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($customRule);

        $reportResponse = $this->actingAs($this->user)->get(route('application.report', ['id' => $leaseApp->id]));
        $reportResponse->assertStatus(200);
        $reportResponse->assertSee($customRule);
    }
}
