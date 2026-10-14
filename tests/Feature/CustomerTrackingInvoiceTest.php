<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\LeaseApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerTrackingInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
    protected Mineral $mineral;

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

        $this->district = District::firstOrCreate(['name' => 'Salem']);
        $this->mineral = Mineral::firstOrCreate(['name' => 'Rough Stone and Gravel']);

        $rnd = rand(10000, 99999);
        $this->customer = Customer::create([
            'customer_name'  => 'Test MultiApp Client ' . $rnd,
            'company_name'   => 'Test Concession Works LLP ' . $rnd,
            'district_id'    => $this->district->id,
            'mineral_id'     => $this->mineral->id,
            'mobile_num'     => '98765' . rand(10000, 99999),
            'mimas_no'       => 'CUST-INV-' . $rnd,
            'address'        => 'Survey No 45/2, Mining Zone',
            'gstin'          => '33AAAAA' . rand(1000, 9999) . 'A1Z5',
            'created_by'     => $this->user->id,
        ]);

        // Add 2 distinct mining applications and 1 lease application
        MiningApplication::create([
            'customer_id'         => $this->customer->id,
            'district_id'         => $this->district->id,
            'mineral_id'          => $this->mineral->id,
            'application_no'      => 'MP-' . $rnd . '-A',
            'village'             => 'Alathur',
            'taluk'               => 'Sankari',
            'survey_numbers_text' => '101/1A',
            'area_extent_ha'      => 2.50,
            'product_value'       => 150000.00,
            'status'              => 'approved',
            'created_by'          => $this->user->id,
        ]);

        MiningApplication::create([
            'customer_id'         => $this->customer->id,
            'district_id'         => $this->district->id,
            'mineral_id'          => $this->mineral->id,
            'application_no'      => 'MP-' . $rnd . '-B',
            'village'             => 'Komarapalayam',
            'taluk'               => 'Sankari',
            'survey_numbers_text' => '102/3B',
            'area_extent_ha'      => 1.80,
            'product_value'       => 120000.00,
            'status'              => 'scrutiny',
            'created_by'          => $this->user->id,
        ]);

        $leaseCategory = \Illuminate\Support\Facades\DB::table('lease_categories')->first();
        $categoryId = $leaseCategory ? $leaseCategory->id : 2;

        LeaseApplication::create([
            'customer_id'    => $this->customer->id,
            'district_id'    => $this->district->id,
            'category_id'    => $categoryId,
            'mineral_id'     => $this->mineral->id,
            'application_no' => 'LA-' . $rnd . '-1',
            'village'        => 'Alathur',
            'taluk'          => 'Sankari',
            'area_extent_ha' => 4.30,
            'product_value'  => 180000.00,
            'status'         => 'draft',
            'created_by'     => $this->user->id,
        ]);
    }

    /** @test */
    public function tax_invoice_renders_with_high_fidelity_reference_ti_template()
    {
        $response = $this->actingAs($this->user)
            ->get(route('customer-tracking.tax-invoice', $this->customer->id));

        $response->assertStatus(200);
        $response->assertSee('TAX INVOICE');
        $response->assertSee('GEO TECHNICAL MINING SOLUTIONS');
        $response->assertSee('33AAQFG5164R1ZN');
        $response->assertSee('Karur Vysya Bank');
        $response->assertSee('Filter Applications');
    }

    /** @test */
    public function proforma_invoice_renders_with_high_fidelity_reference_pi_template()
    {
        $response = $this->actingAs($this->user)
            ->get(route('customer-tracking.proforma-invoice', $this->customer->id));

        $response->assertStatus(200);
        $response->assertSee('Proforma Invoice');
        $response->assertSee('Indian Overseas Bank');
        $response->assertSee('Union Bank of India');
        $response->assertSee('Filter Applications');
    }

    /** @test */
    public function multi_select_application_filtering_dynamically_calculates_subtotal_and_taxes()
    {
        $appA = $this->customer->miningApplications()->where('application_no', 'like', '%-A')->first();
        $appB = $this->customer->miningApplications()->where('application_no', 'like', '%-B')->first();
        $keyA = 'mining_' . $appA->id;
        $keyB = 'mining_' . $appB->id;

        // 1. Select only Mining A (150,000)
        $respA = $this->actingAs($this->user)
            ->get(route('customer-tracking.tax-invoice', [
                'customer' => $this->customer->id,
                'apps'     => [$keyA],
            ]));

        $respA->assertStatus(200);
        $respA->assertSee($appA->application_no);
        $respA->assertSee('150,000.00'); // Subtotal
        $respA->assertSee('13,500.00');  // 9% CGST & SGST
        $respA->assertSee('177,000.00'); // Grand Total
        $respA->assertDontSee('Rs. 120,000.00'); // Unselected Mining B amount not in invoice items

        // 2. Select both Mining A and Mining B (150,000 + 120,000 = 270,000)
        $respBoth = $this->actingAs($this->user)
            ->get(route('customer-tracking.tax-invoice', [
                'customer' => $this->customer->id,
                'apps'     => [$keyA, $keyB],
            ]));

        $respBoth->assertStatus(200);
        $respBoth->assertSee($appA->application_no);
        $respBoth->assertSee($appB->application_no);
        $respBoth->assertSee('270,000.00'); // Subtotal
        $respBoth->assertSee('24,300.00');  // 9% CGST & SGST
        $respBoth->assertSee('318,600.00'); // Grand Total
    }

    /** @test */
    public function customer_tracking_show_page_includes_multi_select_invoice_modal()
    {
        $response = $this->actingAs($this->user)
            ->get(route('customer-tracking.show', $this->customer->id));

        $response->assertStatus(200);
        $response->assertSee('customerInvoiceModal');
        $response->assertSee('openCustomerInvoiceModal');
        $response->assertSee('Select Applications to Include in Invoice');
        $response->assertSee('Generate Tax Invoice');
        $response->assertSee('Generate Proforma Invoice');
    }
}
