<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class QuarryAreaFormatTest extends TestCase
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
        Permission::firstOrCreate(['name' => 'customer.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'customer.view', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['application.create', 'application.view', 'customer.create', 'customer.view']);

        $this->district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $this->mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);
    }

    /** @test */
    public function step1_saves_and_preserves_revenue_ha_are_sqm_format()
    {
        $payload = [
            'mimas_no'     => 'TN-MMS-AREA-01',
            'client_name'  => 'Test Area Applicant',
            'company_name' => 'Sri Bala Mines',
            'district_id'  => $this->district->id,
            'mineral_ids'  => [$this->mineral->id],
            'mobile_num'   => '9876543210',
            'pan'          => 'ABCDE1234F',
            'aadhaar_no'   => '123456789012',
            'area'         => '1.90.57',
            'address'      => 'Kallakurichi Taluk',
        ];

        $response = $this->actingAs($this->user)->postJson(route('step1.save'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $customer = Customer::where('mimas_no', 'TN-MMS-AREA-01')->first();
        $this->assertNotNull($customer);
        $this->assertSame('1.90.57', $customer->area);
        $this->assertEquals(1.9057, $customer->area_numeric);

        $leaseApp = LeaseApplication::where('customer_id', $customer->id)->latest()->first();
        $this->assertNotNull($leaseApp);
        $this->assertSame('1.90.57', $leaseApp->area_extent_ha);
        $this->assertEquals(1.9057, $leaseApp->area_extent_numeric);
    }

    /** @test */
    public function step1_saves_and_preserves_standard_decimal_format()
    {
        $payload = [
            'mimas_no'     => 'TN-MMS-AREA-02',
            'client_name'  => 'Standard Decimal Client',
            'district_id'  => $this->district->id,
            'mineral_ids'  => [$this->mineral->id],
            'mobile_num'   => '9876543211',
            'pan'          => 'ABCDE1234G',
            'aadhaar_no'   => '123456789013',
            'area'         => '1.05',
            'address'      => 'Attur Taluk',
        ];

        $response = $this->actingAs($this->user)->postJson(route('step1.save'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $customer = Customer::where('mimas_no', 'TN-MMS-AREA-02')->first();
        $this->assertNotNull($customer);
        $this->assertSame('1.05', $customer->area);
        $this->assertEquals(1.05, $customer->area_numeric);

        $leaseApp = LeaseApplication::where('customer_id', $customer->id)->latest()->first();
        $this->assertNotNull($leaseApp);
        $this->assertSame('1.05', $leaseApp->area_extent_ha);
        $this->assertEquals(1.05, $leaseApp->area_extent_numeric);
    }

    /** @test */
    public function customer_directory_crud_accepts_revenue_area_format()
    {
        $payload = [
            'mimas_no'      => 'TN-MMS-DIR-01',
            'customer_name' => 'Directory Revenue Client',
            'mobile_num'    => '9876543212',
            'district_id'   => $this->district->id,
            'area'          => '3.85.50',
            'status'        => 1,
        ];

        $response = $this->actingAs($this->user)->postJson(route('customeradd'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $customer = Customer::where('mimas_no', 'TN-MMS-DIR-01')->first();
        $this->assertNotNull($customer);
        $this->assertSame('3.85.50', $customer->area);
        $this->assertEquals(3.8550, $customer->area_numeric);
    }
}
