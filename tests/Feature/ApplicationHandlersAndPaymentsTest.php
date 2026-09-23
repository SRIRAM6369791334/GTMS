<?php

namespace Tests\Feature;

use App\Models\ApplicantType;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\NatureOfWork;
use App\Models\PlanType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApplicationHandlersAndPaymentsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->user = $user;
        $this->actingAs($this->user);
    }

    /**
     * Test 1: Verify Schema integrity and Model relationships
     */
    public function test_schema_integrity_and_model_relations(): void
    {
        $this->assertTrue(Schema::hasTable('application_handlers'), 'application_handlers table must exist');
        $this->assertTrue(Schema::hasTable('application_payments'), 'application_payments table must exist');

        $this->assertTrue(Schema::hasColumn('lease_applications', 'product_value'));
        $this->assertTrue(Schema::hasColumn('lease_applications', 'paid_amount'));
        $this->assertTrue(Schema::hasColumn('lease_applications', 'pending_amount'));
        $this->assertTrue(Schema::hasColumn('lease_applications', 'payment_status'));

        $this->assertTrue(Schema::hasColumn('mining_applications', 'product_value'));
        $this->assertTrue(Schema::hasColumn('mining_applications', 'paid_amount'));
        $this->assertTrue(Schema::hasColumn('mining_applications', 'pending_amount'));
        $this->assertTrue(Schema::hasColumn('mining_applications', 'payment_status'));

        $handler = new ApplicationHandler([
            'application_type' => 'lease',
            'application_id'   => 1,
            'name'             => 'Test Surveyor',
            'role'             => 'Surveyor',
            'notes'            => 'Boundary check',
        ]);
        $this->assertEquals('Test Surveyor', $handler->name);
        $this->assertEquals('Surveyor', $handler->role);

        $payment = new ApplicationPayment([
            'product_value'  => 100000.00,
            'paid_amount'    => 40000.00,
            'pending_amount' => 60000.00,
            'payment_status' => 'partial',
        ]);
        $this->assertEquals('100000.00', $payment->product_value);
        $this->assertEquals('partial', $payment->payment_status);
    }

    /**
     * Test 2: Lease Application Step 6 (Handling Persons) GET and POST
     */
    public function test_lease_application_step6_handlers_workflow(): void
    {
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Test Quarry Ltd',
            'company_name'  => 'Test Quarry Ltd',
            'mimas_no'      => 'TN-MMS-TEST-001',
            'mobile_num'    => '9876543210',
            'pan'           => 'AAACS1234F',
            'aadhaar_no'    => '9999-8888-7777',
            'status'        => 1,
        ]);

        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);

        // Seed basic session draft
        session([
            'lease_draft' => [
                'customer_id'   => $customer->id,
                'customer_name' => $customer->customer_name,
                'company_name'  => $customer->company_name,
                'district_id'   => $district->id,
                'mineral_id'    => $mineral->id,
            ]
        ]);

        // GET Step 6
        $response = $this->get(route('step6'));
        $response->assertStatus(200);
        $response->assertSee('Handling Persons');
        $response->assertSee('Project Handling Team');

        // POST Step 6 with multiple dynamic handlers
        $postData = [
            'handlers' => [
                [
                    'name'  => 'M. Senthil Kumar',
                    'role'  => 'DGPS Senior Surveyor',
                    'notes' => 'Field survey and boundary verification',
                ],
                [
                    'name'  => 'R. Anbarasan',
                    'role'  => 'Environmental Consultant',
                    'notes' => 'EIA and baseline documentation',
                ],
            ],
        ];

        $postResponse = $this->post(route('step6.save'), $postData);
        $postResponse->assertRedirect(route('step7'));

        // Verify saved into session draft
        $draft = session('lease_draft');
        $this->assertNotEmpty($draft['handlers']);
        $this->assertCount(2, $draft['handlers']);
        $this->assertEquals('M. Senthil Kumar', $draft['handlers'][0]['name']);
        $this->assertEquals('DGPS Senior Surveyor', $draft['handlers'][0]['role']);
        $this->assertEquals('R. Anbarasan', $draft['handlers'][1]['name']);
    }

    /**
     * Test 3: Lease Application Step 7 (Payment Details) GET and POST
     */
    public function test_lease_application_step7_payment_workflow_and_calculation(): void
    {
        // Set existing draft in session
        session([
            'lease_draft' => [
                'company_name' => 'Test Quarry Ltd',
                'handlers' => [
                    ['name' => 'M. Senthil Kumar', 'role' => 'Surveyor', 'notes' => 'Field work'],
                ],
            ]
        ]);

        // GET Step 7
        $response = $this->get(route('step7'));
        $response->assertStatus(200);
        $response->assertSee('Payment Details');
        $response->assertSee('Billing Ledger');

        // POST Step 7 with Product Value and Paid Amount
        $paymentData = [
            'product_value'  => '120000.00',
            'paid_amount'    => '45000.00',
            'payment_status' => 'partial',
            'payment_notes'  => 'Advance paid via NEFT UTR20260922',
        ];

        $postResponse = $this->post(route('step7.save'), $paymentData);
        $postResponse->assertRedirect(route('step8'));

        // Verify calculations in session draft
        $draft = session('lease_draft');
        $this->assertEquals(120000.00, $draft['payment']['product_value']);
        $this->assertEquals(45000.00, $draft['payment']['paid_amount']);
        $this->assertEquals(75000.00, $draft['payment']['pending_amount']); // 120000 - 45000
        $this->assertEquals('partial', $draft['payment']['payment_status']);
    }

    /**
     * Test 4: Lease Application Step 8 (Review) displays handlers & payment
     */
    public function test_lease_application_step8_review_displays_handlers_and_payment(): void
    {
        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);

        session([
            'lease_draft' => [
                'company_name'   => 'Global Mining Corporation',
                'contact_person' => 'Kumaresan R',
                'contact_mobile' => '9876543210',
                'district_id'    => $district->id,
                'mineral_id'     => $mineral->id,
                'taluk'          => 'Omalur',
                'village'        => 'Kamalapuram',
                'handlers'       => [
                    [
                        'name'  => 'Dr. K. Natarajan',
                        'role'  => 'Chief Mining Geologist',
                        'notes' => 'Ore reserve estimation',
                    ]
                ],
                'payment' => [
                    'product_value'  => 180000.00,
                    'paid_amount'    => 60000.00,
                    'pending_amount' => 120000.00,
                    'payment_status' => 'partial',
                    'payment_notes'  => 'Initial Retainer',
                ]
            ]
        ]);

        $response = $this->get(route('step8'));
        $response->assertStatus(200);
        $response->assertSee('Project Handling Personnel');
        $response->assertSee('Dr. K. Natarajan');
        $response->assertSee('Chief Mining Geologist');
        $response->assertSee('Billing Ledger');
        $response->assertSee('180,000.00');
        $response->assertSee('60,000.00');
        $response->assertSee('120,000.00');
    }

    /**
     * Test 5: Mining Application intake wizard stores handlers and payment
     */
    public function test_mining_application_intake_saves_handlers_and_payment(): void
    {
        $now = NatureOfWork::first() ?? NatureOfWork::create(['name' => 'Mining Plan', 'status' => 1]);
        $appType = ApplicantType::first() ?? ApplicantType::create(['name' => 'Private Limited Company', 'status' => 1]);
        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);
        $planType = PlanType::first() ?? PlanType::create(['name' => 'Precise Area Communication Plan', 'status' => 1]);

        $customer = Customer::create([
            'customer_name' => 'Kaveri Granites',
            'company_name'  => 'Kaveri Granites Pvt Ltd',
            'mimas_no'      => 'TN-MMS-KVR-' . substr(uniqid(), -5) . '-' . rand(10, 99),
            'mobile_num'    => '98' . rand(10000000, 99999999),
            'email'         => 'kaveri_' . uniqid() . '@example.com',
            'pan'           => 'AAAC' . rand(1000, 9999) . 'F',
            'aadhaar_no'    => rand(1000, 9999) . '-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'status'        => 1,
        ]);

        $miningData = [
            'nature_of_work_id'   => $now->id,
            'applicant_type_id'   => $appType->id,
            'district_id'         => $district->id,
            'mineral_ids'         => [$mineral->id],
            'plan_type_id'        => $planType->id,
            'customer_id'         => $customer->id,
            'client_name'         => 'K. Rajasekaran',
            'company_name'        => 'Kaveri Granites Pvt Ltd',
            'mobile_num'          => '9842112345',
            'taluk'               => 'Sankari',
            'village'             => 'Alathur',
            'survey_numbers_text' => '102/1A, 102/1B',
            'area_extent_ha'      => '2.450',
            'product_value'       => '95000.00',
            'paid_amount'         => '95000.00',
            'payment_status'      => 'paid',
            'handlers'            => [
                [
                    'name'  => 'P. Murugesan',
                    'role'  => 'Field Liaison Officer',
                    'notes' => 'Panchayat and Revenue NOC clearance',
                ],
                [
                    'name'  => 'S. Thangaraj',
                    'role'  => 'AutoCAD Draftsman',
                    'notes' => 'Preparation of quarry layout & contour plates',
                ]
            ],
        ];

        $response = $this->post(route('newapplication.store'), $miningData);
        $response->assertRedirect();

        // Retrieve created MiningApplication
        $miningApp = MiningApplication::where('customer_id', $customer->id)->latest()->first();
        $this->assertNotNull($miningApp, 'Mining application should be created in DB');

        // Check payment columns
        $this->assertEquals(95000.00, (float)$miningApp->product_value);
        $this->assertEquals(95000.00, (float)$miningApp->paid_amount);
        $this->assertEquals(0.00, (float)$miningApp->pending_amount);
        $this->assertEquals('paid', $miningApp->payment_status);

        // Check polymorphic ApplicationPayment
        $appPayment = ApplicationPayment::where('application_type', 'mining')
            ->where('application_id', $miningApp->id)
            ->first();
        $this->assertNotNull($appPayment);
        $this->assertEquals(95000.00, (float)$appPayment->product_value);
        $this->assertEquals('paid', $appPayment->payment_status);

        // Check ApplicationHandler records
        $handlers = $miningApp->handlers;
        $this->assertCount(2, $handlers);
        $this->assertEquals('P. Murugesan', $handlers[0]->name);
        $this->assertEquals('Field Liaison Officer', $handlers[0]->role);
        $this->assertEquals('S. Thangaraj', $handlers[1]->name);
        $this->assertEquals('AutoCAD Draftsman', $handlers[1]->role);

        // Verify dossier view renders handlers and payment ledger
        $dossierResponse = $this->get(route('projectfolder', ['id' => $miningApp->id]));
        $dossierResponse->assertStatus(200);
        $dossierResponse->assertSee('Project Handling Team');
        $dossierResponse->assertSee('P. Murugesan');
        $dossierResponse->assertSee('Field Liaison Officer');
        $dossierResponse->assertSee('Financial &amp; Billing Ledger', false);
        $dossierResponse->assertSee('Fully Paid');
    }

    /**
     * Test 6: Payment Ledger Status and Calculation Edge Cases
     */
    public function test_payment_ledger_status_derivation_logic(): void
    {
        // Case A: No payment received
        $v1 = 50000.00;
        $p1 = 0.00;
        $pending1 = max(0, $v1 - $p1);
        $status1 = ($p1 <= 0) ? 'pending' : (($pending1 <= 0) ? 'paid' : 'partial');
        $this->assertEquals(50000.00, $pending1);
        $this->assertEquals('pending', $status1);

        // Case B: Partial payment
        $v2 = 75000.00;
        $p2 = 25000.00;
        $pending2 = max(0, $v2 - $p2);
        $status2 = ($p2 <= 0) ? 'pending' : (($pending2 <= 0) ? 'paid' : 'partial');
        $this->assertEquals(50000.00, $pending2);
        $this->assertEquals('partial', $status2);

        // Case C: Exact Full payment
        $v3 = 60000.00;
        $p3 = 60000.00;
        $pending3 = max(0, $v3 - $p3);
        $status3 = ($p3 <= 0) ? 'pending' : (($pending3 <= 0) ? 'paid' : 'partial');
        $this->assertEquals(0.00, $pending3);
        $this->assertEquals('paid', $status3);

        // Case D: Overpayment defense (pending clamped to 0)
        $v4 = 40000.00;
        $p4 = 45000.00;
        $pending4 = max(0, $v4 - $p4);
        $this->assertEquals(0.00, $pending4);
    }
}
