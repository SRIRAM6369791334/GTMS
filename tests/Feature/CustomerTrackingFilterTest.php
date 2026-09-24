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

class CustomerTrackingFilterTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::first() ?: User::factory()->create();
        $this->district = District::first();

        $random = rand(10000, 99999) . rand(100, 999);
        $this->customer = Customer::create([
            'customer_name'         => 'Test Track Client ' . $random,
            'company_name'          => 'Test Quarry Mines ' . $random,
            'mimas_no'              => 'TN-MMS-FLT-' . $random,
            'mobile_num'            => '98765' . rand(10000, 99999),
            'secondary_mobile_num'  => '91234' . rand(10000, 99999),
            'district_id'           => $this->district->id,
            'pan'                   => 'FLT' . rand(1000, 9999) . 'Z',
            'aadhaar_no'            => '8888-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'slug'                  => 'test-track-client-' . $random . '-' . uniqid(),
            'status'                => 1,
        ]);
    }

    /**
     * Test tracking page loads with all requested filter controls and no MIMAS labels
     */
    public function test_customer_tracking_index_renders_with_filters(): void
    {
        $response = $this->actingAs($this->user)->get(route('customer-tracking.index'));

        $response->assertStatus(200);
        $response->assertSee('Customer 360 Dossier & Application Tracking', false);
        $response->assertSee('Customer Unique ID');
        $response->assertSee('name="district_id"', false);
        $response->assertSee('name="app_type"', false);
        $response->assertSee('name="date_from"', false);
        $response->assertSee('name="date_to"', false);
        $response->assertSee('filterDistrict');
        $response->assertSee('filterAppType');
        $response->assertDontSee('data-query="MIMAS"', false);
        $response->assertDontSee('MIMAS Number');
    }

    /**
     * Test filtering by district
     */
    public function test_customer_tracking_filters_by_district(): void
    {
        $response = $this->actingAs($this->user)->get(route('customer-tracking.index', [
            'district_id' => $this->district->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Filters:');
        $response->assertSee($this->district->name);
    }

    /**
     * Test filtering by application type
     */
    public function test_customer_tracking_filters_by_application_type(): void
    {
        $response = $this->actingAs($this->user)->get(route('customer-tracking.index', [
            'app_type' => 'mining',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Filters:');
        $response->assertSee('Mining Plan');
    }

    /**
     * Test searching by Customer Unique ID
     */
    public function test_customer_tracking_searches_by_unique_id(): void
    {
        $response = $this->actingAs($this->user)->get(route('customer-tracking.index', [
            'q' => $this->customer->mimas_no,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->customer->customer_name);
        $response->assertSee($this->customer->mimas_no);
    }

    /**
     * Test searching by Secondary Mobile number
     */
    public function test_customer_tracking_searches_by_secondary_mobile(): void
    {
        $response = $this->actingAs($this->user)->get(route('customer-tracking.index', [
            'q' => $this->customer->secondary_mobile_num,
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->customer->customer_name);
    }

    /**
     * Test AJAX autocomplete endpoint returns unique_id and secondary_mobile
     */
    public function test_customer_tracking_ajax_search_returns_unique_id_and_phones(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('customer-tracking.search', [
            'q' => substr($this->customer->customer_name, 0, 15),
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results' => [
                '*' => [
                    'id',
                    'name',
                    'company',
                    'unique_id',
                    'mobile',
                    'secondary_mobile',
                    'district',
                    'active_stage',
                    'url',
                ]
            ]
        ]);
    }
}
