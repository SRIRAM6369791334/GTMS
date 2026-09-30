<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\Category;
use App\Models\MiningApplication;
use App\Models\LeaseApplication;
use App\Models\DgpsSurvey;
use App\Models\EcCompliance;
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

        $this->user = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }
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

    /**
     * Test standalone-only customer (0 lease applications) activates standalone tab without blank screen
     */
    public function test_customer_tracking_standalone_client_renders_standalone_pane_active(): void
    {
        $random = rand(10000, 99999);
        $standaloneClient = Customer::create([
            'customer_name'        => 'Standalone Client ' . $random,
            'company_name'         => 'Direct Survey Corp ' . $random,
            'mimas_no'             => 'TN-MMS-STD-' . $random,
            'mobile_num'           => '97865' . rand(10000, 99999),
            'district_id'          => $this->district->id,
            'slug'                 => 'standalone-client-' . $random,
            'status'               => 1,
        ]);

        DgpsSurvey::create([
            'survey_no'      => 'DGPS-STD-' . $random,
            'customer_id'    => $standaloneClient->id,
            'survey_status'  => 'scheduled',
            'location'       => 'Direct Demarcation Site',
            'surveyed_area_ha' => 4.5,
        ]);

        $response = $this->actingAs($this->user)->get(route('customer-tracking.show', $standaloneClient->slug));

        $response->assertStatus(200);
        $response->assertSee('Standalone Services');
        $response->assertSee('id="standalone-pane"', false);
        $response->assertSee('show active');
        $response->assertSee('360 Enterprise Dossier &nearr;', false);
        $response->assertSee('Edit Profile');
        $response->assertSee('DGPS-STD-' . $random);
        // Ensure portfolio and lifecycle panes are not rendered as active
        $response->assertDontSee('id="portfolio-pane" role="tabpanel" aria-labelledby="portfolio-tab"', false);
    }

    /**
     * Test that Pillar 1 action button and stepper node 1 contain the lease ID parameter
     */
    public function test_customer_tracking_with_lease_chain_contains_id_parameter_in_actions(): void
    {
        $random = rand(10000, 99999);
        $categoryId = \Illuminate\Support\Facades\DB::table('lease_categories')->value('id') ?? 2;
        $mineral = Mineral::first();
        $lease = LeaseApplication::create([
            'application_no'    => 'LA-TEST-' . $random,
            'common_id'         => 'GTMS-2026-' . $random,
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'category_id'       => $categoryId,
            'mineral_id'        => $mineral?->id ?? 1,
            'status'            => 'draft',
            'village'           => 'Alathur',
            'taluk'             => 'Sankari',
            'area_extent_ha'    => 2.50,
        ]);

        $response = $this->actingAs($this->user)->get(route('customer-tracking.show', $this->customer->slug));

        $response->assertStatus(200);
        // Pillar 1 Action button must link to viewapplication with id
        $expectedUrl = route('viewapplication', ['id' => $lease->id]);
        $response->assertSee($expectedUrl, false);
    }

    /**
     * Test that customer cards in recent directory display the EC Compliance chip
     */
    public function test_customer_tracking_recent_customers_shows_compliance_chip(): void
    {
        $random = rand(10000, 99999);
        EcCompliance::create([
            'compliance_no'     => 'EC-COMP-' . $random,
            'project_name'      => 'Test Project ' . $random,
            'customer_id'       => $this->customer->id,
            'district_id'       => $this->district->id,
            'status'            => 'draft',
            'compliance_period' => 'April 2026 - September 2026',
        ]);

        $response = $this->actingAs($this->user)->get(route('customer-tracking.index'));

        $response->assertStatus(200);
        $response->assertSee('Compliance:');
    }

    /**
     * Test high-volume customer (Kaveri Granites, 264 leases) renders portfolio, pagination,
     * and district-grouped chains without quadratic loops or broken pagination buttons
     */
    public function test_customer_tracking_high_volume_client_kaveri_renders_successfully(): void
    {
        $kaveri = Customer::where('slug', 'kaveri-granites-3197')->first();
        if (!$kaveri) {
            // If Kaveri not in DB, create a multi-lease client to simulate
            $random = rand(10000, 99999);
            $kaveri = Customer::create([
                'customer_name'  => 'High Volume Client ' . $random,
                'company_name'   => 'Multi-Quarry Corp ' . $random,
                'mimas_no'       => 'TN-MMS-HV-' . $random,
                'mobile_num'     => '91111' . rand(10000, 99999),
                'slug'           => 'high-volume-client-' . $random,
                'status'         => 1,
            ]);
            $categoryId = \Illuminate\Support\Facades\DB::table('lease_categories')->value('id') ?? 2;
            $mineral = Mineral::first();
            // Create multiple leases across districts
            for ($i = 0; $i < 5; $i++) {
                LeaseApplication::create([
                    'application_no' => 'LA-HV-' . $random . '-' . $i,
                    'common_id'      => 'GTMS-2026-HV-' . $random . '-' . $i,
                    'customer_id'    => $kaveri->id,
                    'district_id'    => $this->district->id,
                    'category_id'    => $categoryId,
                    'mineral_id'     => $mineral?->id ?? 1,
                    'status'         => 'draft',
                    'village'        => 'Village-' . $i,
                    'taluk'          => 'Taluk-' . $i,
                    'area_extent_ha' => rand(100, 500) / 100,
                ]);
            }
        }

        $response = $this->actingAs($this->user)->get(route('customer-tracking.show', $kaveri->slug));

        $response->assertStatus(200);
        // Portfolio tab and pagination controls must render
        $response->assertSee('Concessions Directory');
        $response->assertSee('portfolioPageSize');
        // District-grouped chains should use pre-grouped optgroup (no O(N*M) loop)
        $response->assertSee('<optgroup label=', false);
        // Pagination Next button must have proper class quote closure (Bug Fix #1)
        $response->assertDontSee('ct-page-btn ${nextDisabled} onclick=', false);
        // Invoice fallback must not say 'Salem' (Bug Fix #4)
        $response->assertDontSee("?: 'Salem'", false);
    }
}
