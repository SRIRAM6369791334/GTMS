<?php

namespace Tests\Feature;

use App\Models\ApplicationPayment;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\LeaseSurveyNumber;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\PaymentReceipt;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccountsModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected Branch $branch;
    protected District $district;
    protected Mineral $mineral;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Clean any test artifacts from previous runs
        QuotationItem::whereHas('quotation', function ($q) {
            $q->where('quotation_number', 'like', 'GTMS/QTN/%/9%')
              ->orWhere('quotation_number', 'like', 'GTMS/QTN/%/8%');
        })->delete();
        Quotation::withTrashed()->where('quotation_number', 'like', 'GTMS/QTN/%/9%')
            ->orWhere('quotation_number', 'like', 'GTMS/QTN/%/8%')->forceDelete();
        PaymentReceipt::withTrashed()->where('receipt_number', 'like', 'GTMS/REC/%/9%')
            ->orWhere('receipt_number', 'like', 'GTMS/REC/%/8%')->forceDelete();

        // 1. Ensure required Spatie permissions exist
        $permissions = ['account.view', 'account.create', 'account.edit', 'account.delete'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. Set up default Branch & Master District / Mineral
        $this->branch = Branch::firstOrCreate(
            ['branch_name' => 'Salem Head Office'],
            [
                'contact_person' => 'Accounts Officer',
                'mobile' => '9443212345',
                'address' => '1/237, AR Complex, Meyyanur',
                'city' => 'Salem',
                'state' => 'Tamil Nadu',
                'pincode' => '636004',
                'status' => 1,
            ]
        );

        $this->district = District::firstOrCreate(
            ['name' => 'Salem'],
            ['status' => 1]
        );

        $this->mineral = Mineral::firstOrCreate(
            ['name' => 'Rough Stone'],
            ['status' => 1]
        );

        // 3. Set up Admin User
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->adminUser = User::where('email', 'admin@gtms.com')->first()
            ?: User::where('role_id', 1)->first()
            ?: User::firstOrCreate(
                ['email' => 'admin_test@gtms.com'],
                [
                    'name' => 'System Admin',
                    'user_code' => 'ADM_999',
                    'password' => bcrypt('password123'),
                    'show_password' => 'password123',
                    'branch_id' => $this->branch->id,
                    'role_id' => $adminRole->id,
                    'status' => 1,
                ]
            );

        if (!$this->adminUser->hasRole(['Admin', 'Super Admin'])) {
            $this->adminUser->assignRole($adminRole);
        }

        $this->actingAs($this->adminUser);
    }

    /**
     * Helper: Create a verified Customer record
     */
    protected function createTestCustomer(array $attrs = []): Customer
    {
        $unique = uniqid();
        return Customer::create(array_merge([
            'customer_name' => 'Sri Krishna Quarries',
            'company_name'  => 'Sri Krishna Granites Pvt Ltd',
            'mimas_no'      => 'TN-MMS-SLM-' . $unique,
            'mobile_num'    => '98421' . mt_rand(10000, 99999),
            'email'         => "accounts_{$unique}@krishnaquarries.com",
            'gstin'         => '33AAACK' . mt_rand(1000, 9999) . chr(rand(65, 90)) . '1Z' . mt_rand(1, 9),
            'pan'           => 'AAACK' . mt_rand(1000, 9999) . chr(rand(65, 90)),
            'aadhaar_no'    => mt_rand(1000, 9999) . '-' . mt_rand(1000, 9999) . '-' . mt_rand(1000, 9999),
            'district_id'   => $this->district->id,
            'address'       => 'S.F. 102/1A, Omalur Main Road, Salem - 636005',
            'status'        => 1,
        ], $attrs));
    }

    /**
     * Helper: Create a test User with specific permissions (and NOT an Admin role)
     */
    protected function createNonAdminUserWithPermissions(array $permissions): User
    {
        $unique = uniqid();
        $staffRole = Role::firstOrCreate(['name' => 'Accountant_' . $unique, 'guard_name' => 'web']);
        $staffRole->syncPermissions($permissions);

        $user = User::create([
            'name'          => 'Accountant ' . $unique,
            'email'         => "acc_{$unique}@gtms.com",
            'user_code'     => 'ACC_' . substr(uniqid(), -6),
            'password'      => bcrypt('secret123'),
            'show_password' => 'secret123',
            'branch_id'     => $this->branch->id,
            'role_id'       => $staffRole->id,
            'status'        => 1,
        ]);

        $user->assignRole($staffRole);
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return $user;
    }

    /**
     * Helper: Generate a collision-free unique quotation number for tests
     */
    protected function uniqueQuotationNumber(): string
    {
        $year = date('Y');
        do {
            $num = "GTMS/QTN/{$year}/" . mt_rand(10000, 99999);
        } while (Quotation::withTrashed()->where('quotation_number', $num)->exists());
        return $num;
    }

    /**
     * Helper: Generate a collision-free unique receipt number for tests
     */
    protected function uniqueReceiptNumber(): string
    {
        $year = date('Y');
        do {
            $num = "GTMS/REC/{$year}/" . mt_rand(10000, 99999);
        } while (PaymentReceipt::withTrashed()->where('receipt_number', $num)->exists());
        return $num;
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 1: QUOTATION MANAGEMENT ENGINE & HIGH-FIDELITY PRINT
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R1.1: Verify quotation creation with valid line items, automated subtotal and 18% GST calculation.
     */
    public function test_quotation_creation_with_valid_items_and_automated_tax_calculation(): void
    {
        $customer = $this->createTestCustomer();

        $postData = [
            'customer_id'    => $customer->id,
            'validity_days'  => 45,
            'tax_rate'       => 18.00,
            'notes'          => 'Statutory documentation package for Omalur Granite Quarry.',
            'items'          => [
                [
                    'service_name' => 'DGPS Demarcation & Boundary Survey',
                    'sac_code'     => '998334',
                    'description'  => 'Cadastral overlay and georeferencing',
                    'quantity'     => 2.00,
                    'unit'         => 'Ha',
                    'unit_rate'    => 35000.00,
                ],
                [
                    'service_name' => 'Mining Plan Preparation & Processing',
                    'sac_code'     => '998341',
                    'description'  => 'RQP Mining Plan and Progressive Closure Plan',
                    'quantity'     => 1.00,
                    'unit'         => 'Job',
                    'unit_rate'    => 80000.00,
                ],
            ],
        ];

        $response = $this->post(route('accounts.quotations.store'), $postData);

        $quotation = Quotation::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertNotNull($quotation, 'Quotation record must be created in database');

        $response->assertRedirect(route('accounts.quotations.show', $quotation->id));

        // Subtotal: (2 * 35,000) + (1 * 80,000) = 70,000 + 80,000 = 150,000.00
        $this->assertEquals('150000.00', $quotation->subtotal);
        // Tax @ 18%: 150,000 * 0.18 = 27,000.00
        $this->assertEquals('27000.00', $quotation->tax_amount);
        // Total Amount: 150,000 + 27,000 = 177,000.00
        $this->assertEquals('177000.00', $quotation->total_amount);

        // Verify sequential quotation numbering pattern: GTMS/QTN/{YYYY}/{0001}
        $year = date('Y');
        $this->assertMatchesRegularExpression("/^GTMS\/QTN\/{$year}\/\d{4,}$/", $quotation->quotation_number);

        // Verify quotation_items count and calculated subtotals
        $this->assertCount(2, $quotation->items);
        $this->assertEquals('70000.00', $quotation->items[0]->subtotal);
        $this->assertEquals('80000.00', $quotation->items[1]->subtotal);

        // Verify amount in Indian words accessor
        $amountInWords = $quotation->amount_in_words;
        $this->assertStringContainsString('Rupees', $amountInWords);
        $this->assertStringContainsString('One Lakh Seventy Seven Thousand', $amountInWords);
        $this->assertStringContainsString('Only', $amountInWords);
    }

    /**
     * R1.2: Verify quotation validation failure when required fields or items are missing.
     */
    public function test_quotation_validation_rejection_for_missing_required_fields_and_invalid_items(): void
    {
        // 1. Missing customer and empty items
        $response = $this->post(route('accounts.quotations.store'), [
            'customer_id' => null,
            'items'       => [],
        ]);

        $response->assertSessionHasErrors(['customer_id', 'items']);

        // 2. Invalid line items (non-numeric quantity or missing service name)
        $customer = $this->createTestCustomer();
        $invalidResponse = $this->post(route('accounts.quotations.store'), [
            'customer_id' => $customer->id,
            'items'       => [
                [
                    'service_name' => '',
                    'quantity'     => -5,
                    'unit_rate'    => 'free',
                ],
            ],
        ]);

        $invalidResponse->assertSessionHasErrors([
            'items.0.service_name',
            'items.0.quantity',
            'items.0.unit_rate',
        ]);
    }

    /**
     * R1.3: Verify quotation listing and filtering by customer and status.
     */
    public function test_quotation_listing_and_filtering_by_customer_and_status(): void
    {
        $customer1 = $this->createTestCustomer(['customer_name' => 'Quarry Alpha']);
        $customer2 = $this->createTestCustomer(['customer_name' => 'Quarry Beta']);

        $qtn1 = Quotation::create([
            'quotation_number' => $this->uniqueQuotationNumber(),
            'customer_id'      => $customer1->id,
            'customer_name'    => $customer1->customer_name,
            'subtotal'         => 100000.00,
            'tax_rate'         => 18.00,
            'tax_amount'       => 18000.00,
            'total_amount'     => 118000.00,
            'status'           => 'draft',
            'branch_id'        => $this->branch->id,
        ]);

        $qtn2 = Quotation::create([
            'quotation_number' => $this->uniqueQuotationNumber(),
            'customer_id'      => $customer2->id,
            'customer_name'    => $customer2->customer_name,
            'subtotal'         => 200000.00,
            'tax_rate'         => 18.00,
            'tax_amount'       => 36000.00,
            'total_amount'     => 236000.00,
            'status'           => 'sent',
            'branch_id'        => $this->branch->id,
        ]);

        // Filter by Customer 1
        $resCustomer = $this->get(route('accounts.quotations.index', ['customer_id' => $customer1->id]));
        $resCustomer->assertStatus(200);
        $resCustomer->assertSee($qtn1->quotation_number);
        $resCustomer->assertDontSee($qtn2->quotation_number);

        // Filter by Status = 'sent'
        $resStatus = $this->get(route('accounts.quotations.index', ['status' => 'sent']));
        $resStatus->assertStatus(200);
        $resStatus->assertSee($qtn2->quotation_number);
        $resStatus->assertDontSee($qtn1->quotation_number);
    }

    /**
     * R1.4: Verify quotation show view and standalone high-fidelity A4 print view rendering.
     */
    public function test_quotation_show_and_standalone_a4_print_view_rendering(): void
    {
        $customer = $this->createTestCustomer(['company_name' => 'Royal Minerals Pvt Ltd']);

        $quotation = Quotation::create([
            'quotation_number' => $this->uniqueQuotationNumber(),
            'customer_id'      => $customer->id,
            'customer_name'    => $customer->customer_name,
            'company_name'     => $customer->company_name,
            'quarry_name'      => 'Kallankurichi Granite Concession',
            'village'          => 'Kallankurichi',
            'taluk'            => 'Ariyalur',
            'survey_numbers'   => '145/2A, 145/2B',
            'area_extent_ha'   => 3.2500,
            'subtotal'         => 100000.00,
            'tax_rate'         => 18.00,
            'tax_amount'       => 18000.00,
            'total_amount'     => 118000.00,
            'status'           => 'sent',
            'branch_id'        => $this->branch->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'service_name' => 'Comprehensive Mining Plan & PMCP Preparation',
            'sac_code'     => '998341',
            'quantity'     => 1.00,
            'unit'         => 'Job',
            'unit_rate'    => 100000.00,
            'subtotal'     => 100000.00,
        ]);

        // Show page
        $resShow = $this->get(route('accounts.quotations.show', $quotation->id));
        $resShow->assertStatus(200);
        $resShow->assertSee($quotation->quotation_number);
        $resShow->assertSee('Royal Minerals Pvt Ltd');

        // Standalone A4 Print view
        $resPrint = $this->get(route('accounts.quotations.print', $quotation->id));
        $resPrint->assertStatus(200);
        $resPrint->assertSee('GEO TECHNICAL MINING SOLUTIONS');
        $resPrint->assertSee('QUOTATION');
        $resPrint->assertSee($quotation->quotation_number);
        $resPrint->assertSee('Kallankurichi Granite Concession');
        $resPrint->assertSee('Comprehensive Mining Plan &amp; PMCP Preparation', false);
        $resPrint->assertSee('One Lakh Eighteen Thousand');
    }

    /**
     * R1.5: Verify customer concessions AJAX lookup returns customer metadata and quarry concessions.
     */
    public function test_customer_concessions_ajax_lookup(): void
    {
        $customer = $this->createTestCustomer();

        $categoryId = \Illuminate\Support\Facades\DB::table('lease_categories')->value('id') ?? 1;

        $lease = LeaseApplication::create([
            'customer_id'    => $customer->id,
            'application_no' => 'LA-SLM-' . uniqid(),
            'quarry_name'    => 'Omalur Rough Stone Quarry',
            'category_id'    => $categoryId,
            'mineral_id'     => $this->mineral->id,
            'district_id'    => $this->district->id,
            'taluk'          => 'Omalur',
            'village'        => 'Semmandapatti',
            'area_extent_ha' => 4.5000,
            'branch_id'      => $this->branch->id,
            'status'         => 'approved',
        ]);

        LeaseSurveyNumber::create([
            'lease_application_id' => $lease->id,
            'survey_no'            => '88/1B',
            'extent_ha'            => 4.50,
        ]);

        $response = $this->get(route('accounts.quotations.customer-concessions', $customer->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success'  => true,
            'customer' => [
                'id'            => $customer->id,
                'customer_name' => $customer->customer_name,
                'company_name'  => $customer->company_name,
                'gst_number'    => $customer->gstin,
            ],
        ]);

        $data = $response->json();
        $this->assertNotEmpty($data['concessions']);
        $this->assertEquals($lease->id, $data['concessions'][0]['id']);
        $this->assertEquals('Semmandapatti Quarry', $data['concessions'][0]['quarry_name']);
        $this->assertStringContainsString('88/1B', $data['concessions'][0]['survey_numbers']);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 2: PAYMENT COLLECTION & ATOMIC AUTO-SYNCHRONIZATION
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R2.1: Verify pending dues resolver aggregates active statutory balances across modules.
     */
    public function test_customer_pending_dues_resolver_aggregates_across_statutory_modules(): void
    {
        $customer = $this->createTestCustomer();

        // 1. Create a MiningApplication with outstanding dues
        $mining = MiningApplication::create([
            'customer_id'         => $customer->id,
            'application_no'      => 'MP-' . uniqid(),
            'district_id'         => $this->district->id,
            'survey_numbers_text' => '120/3',
            'product_value'       => 80000.00,
            'paid_amount'         => 30000.00,
            'pending_amount'      => 50000.00,
            'payment_status'      => 'partial',
            'branch_id'           => $this->branch->id,
        ]);

        // 2. Create a DgpsSurvey with outstanding dues
        $dgps = DgpsSurvey::create([
            'customer_id'    => $customer->id,
            'survey_no'      => 'DGPS-' . uniqid(),
            'location'       => 'Salem Site',
            'product_value'  => 35000.00,
            'paid_amount'    => 0.00,
            'pending_amount' => 35000.00,
            'payment_status' => 'pending',
            'branch_id'      => $this->branch->id,
        ]);

        $response = $this->get(route('accounts.payments.customer-dues', $customer->id));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $json = $response->json();
        $appTypes = collect($json['dues'])->pluck('application_type')->toArray();
        $this->assertContains('mining', $appTypes);
        $this->assertContains('dgps', $appTypes);

        $miningDue = collect($json['dues'])->firstWhere('application_type', 'mining');
        $this->assertEquals(80000.00, $miningDue['product_value']);
        $this->assertEquals(30000.00, $miningDue['paid_amount']);
        $this->assertEquals(50000.00, $miningDue['pending_amount']);
    }

    /**
     * R2.2: Verify payment collection atomically synchronizes target application, application_payments, and creates receipt.
     */
    public function test_payment_collection_atomic_sync_for_mining_application(): void
    {
        $customer = $this->createTestCustomer();

        $mining = MiningApplication::create([
            'customer_id'    => $customer->id,
            'application_no' => 'MP-SYNC-' . uniqid(),
            'district_id'    => $this->district->id,
            'product_value'  => 100000.00,
            'paid_amount'    => 20000.00,
            'pending_amount' => 80000.00,
            'payment_status' => 'partial',
            'branch_id'      => $this->branch->id,
        ]);

        $postPayment = [
            'customer_id'      => $customer->id,
            'application_type' => 'mining',
            'application_id'   => $mining->id,
            'amount_paid'      => 50000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'bank_name'        => 'HDFC Bank',
            'reference_number' => 'UTR9988776655',
            'transaction_date' => '2026-09-29',
            'notes'            => 'Interim payment for RQP Mining Plan draft preparation',
        ];

        $response = $this->post(route('accounts.payments.store'), $postPayment);

        // 1. Verify PaymentReceipt created
        $receipt = PaymentReceipt::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertNotNull($receipt);
        $this->assertEquals('50000.00', $receipt->amount_paid);
        $this->assertEquals('20000.00', $receipt->previous_paid);
        $this->assertEquals('30000.00', $receipt->balance_due);
        $this->assertEquals('NEFT/RTGS', $receipt->payment_mode);
        $this->assertEquals('UTR9988776655', $receipt->reference_number);

        $response->assertRedirect(route('accounts.receipts.show', $receipt->id));

        // 2. Verify Atomic Update on mining_applications table
        $mining->refresh();
        $this->assertEquals('70000.00', $mining->paid_amount, 'Paid amount must be 20,000 + 50,000 = 70,000');
        $this->assertEquals('30000.00', $mining->pending_amount, 'Pending amount must be 100,000 - 70,000 = 30,000');
        $this->assertEquals('partial', $mining->payment_status);

        // 3. Verify Atomic Sync on polymorphic application_payments table
        $appPayment = ApplicationPayment::where('application_type', 'mining')
            ->where('application_id', $mining->id)
            ->first();
        $this->assertNotNull($appPayment, 'application_payments row must be synchronized');
        $this->assertEquals('100000.00', $appPayment->product_value);
        $this->assertEquals('70000.00', $appPayment->paid_amount);
        $this->assertEquals('30000.00', $appPayment->pending_amount);
        $this->assertEquals('partial', $appPayment->payment_status);
    }

    /**
     * R2.3: Verify payment collection for DgpsSurvey leading to full settlement (status 'paid' and zero balance due).
     */
    public function test_payment_collection_atomic_sync_for_dgps_survey_and_full_settlement(): void
    {
        $customer = $this->createTestCustomer();

        $dgps = DgpsSurvey::create([
            'customer_id'    => $customer->id,
            'survey_no'      => 'DGPS-SETTLE-' . uniqid(),
            'product_value'  => 35000.00,
            'paid_amount'    => 15000.00,
            'pending_amount' => 20000.00,
            'payment_status' => 'partial',
            'branch_id'      => $this->branch->id,
        ]);

        $postData = [
            'customer_id'      => $customer->id,
            'application_type' => 'dgps',
            'application_id'   => $dgps->id,
            'amount_paid'      => 20000.00,
            'payment_mode'     => 'UPI/GPay',
            'reference_number' => 'UPI-REF-12345678',
            'transaction_date' => '2026-09-29',
            'notes'            => 'Final settlement for DGPS Demarcation',
        ];

        $response = $this->post(route('accounts.payments.store'), $postData);

        $receipt = PaymentReceipt::where('customer_id', $customer->id)->latest('id')->first();
        $response->assertRedirect(route('accounts.receipts.show', $receipt->id));

        $this->assertEquals('0.00', $receipt->balance_due);

        // Verify DGPS application updated to 'paid'
        $dgps->refresh();
        $this->assertEquals('35000.00', $dgps->paid_amount);
        $this->assertEquals('0.00', $dgps->pending_amount);
        $this->assertEquals('paid', $dgps->payment_status);

        // Verify application_payments table
        $appPayment = ApplicationPayment::where('application_type', 'dgps')
            ->where('application_id', $dgps->id)
            ->first();
        $this->assertEquals('paid', $appPayment->payment_status);
        $this->assertEquals('0.00', $appPayment->pending_amount);
    }

    /**
     * R2.4: Verify general retainer payment collection without target statutory application.
     */
    public function test_general_retainer_payment_without_statutory_application(): void
    {
        $customer = $this->createTestCustomer();

        $postData = [
            'customer_id'      => $customer->id,
            'application_type' => 'general',
            'application_id'   => null,
            'amount_paid'      => 25000.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-29',
            'notes'            => 'Initial commercial retainer on customer ledger',
        ];

        $response = $this->post(route('accounts.payments.store'), $postData);

        $receipt = PaymentReceipt::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertNotNull($receipt);
        $this->assertEquals('general', $receipt->application_type);
        $this->assertNull($receipt->application_id);
        $this->assertEquals('25000.00', $receipt->amount_paid);
        $this->assertEquals('0.00', $receipt->balance_due);

        $response->assertRedirect(route('accounts.receipts.show', $receipt->id));
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 3: OFFICIAL RECEIPT VOUCHERS & PRINTING
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R3.1: Verify sequential receipt voucher number generation pattern and collision prevention.
     */
    public function test_sequential_receipt_voucher_number_generation_and_uniqueness(): void
    {
        $year = date('Y');
        $num1 = PaymentReceipt::generateReceiptNumber();
        $this->assertMatchesRegularExpression("/^GTMS\/REC\/{$year}\/\d{4}$/", $num1);

        $customer = $this->createTestCustomer();

        $receipt1 = PaymentReceipt::create([
            'receipt_number'   => $num1,
            'customer_id'      => $customer->id,
            'amount_paid'      => 10000.00,
            'balance_due'      => 0.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-29',
            'branch_id'        => $this->branch->id,
        ]);

        $num2 = PaymentReceipt::generateReceiptNumber();
        $this->assertNotEquals($num1, $num2, 'Subsequent receipt number must not equal previous');

        $receipt2 = PaymentReceipt::create([
            'receipt_number'   => $num2,
            'customer_id'      => $customer->id,
            'amount_paid'      => 15000.00,
            'balance_due'      => 0.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-29',
            'branch_id'        => $this->branch->id,
        ]);

        $this->assertNotEquals($receipt1->receipt_number, $receipt2->receipt_number);
    }

    /**
     * R3.2: Verify standalone printable A4/A5 voucher view rendering with statutory branding.
     */
    public function test_receipt_voucher_standalone_a4_a5_print_rendering(): void
    {
        $customer = $this->createTestCustomer(['company_name' => 'Thangam Quarry Works']);

        $receipt = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9050',
            'customer_id'      => $customer->id,
            'application_type' => 'general',
            'amount_paid'      => 50000.00,
            'previous_paid'    => 0.00,
            'balance_due'      => 25000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'bank_name'        => 'Canara Bank',
            'reference_number' => 'CNR99221100',
            'transaction_date' => '2026-09-29',
            'notes'            => 'Milestone 1 Advance',
            'branch_id'        => $this->branch->id,
        ]);

        // Show page
        $resShow = $this->get(route('accounts.receipts.show', $receipt->id));
        $resShow->assertStatus(200);
        $resShow->assertSee($receipt->receipt_number);
        $resShow->assertSee('Thangam Quarry Works');

        // Print view
        $resPrint = $this->get(route('accounts.receipts.print', $receipt->id));
        $resPrint->assertStatus(200);
        $resPrint->assertSee('OFFICIAL MONEY RECEIPT');
        $resPrint->assertSee('Receipt Voucher No:');
        $resPrint->assertSee($receipt->receipt_number);
        $resPrint->assertSee('Fifty Thousand');
        $resPrint->assertSee('CNR99221100');
        $resPrint->assertSee('Authorized Signatory &bull; Accounts Department', false);
    }

    /**
     * R3.3: Verify receipts directory with payment mode and date filtering.
     */
    public function test_receipts_listing_directory_with_mode_and_date_filtering(): void
    {
        $customer = $this->createTestCustomer();

        $rCash = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9061',
            'customer_id'      => $customer->id,
            'amount_paid'      => 10000.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-10',
            'branch_id'        => $this->branch->id,
        ]);

        $rUpi = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9062',
            'customer_id'      => $customer->id,
            'amount_paid'      => 20000.00,
            'payment_mode'     => 'UPI/GPay',
            'transaction_date' => '2026-09-25',
            'branch_id'        => $this->branch->id,
        ]);

        // Filter by UPI/GPay
        $resMode = $this->get(route('accounts.receipts.index', ['payment_mode' => 'UPI/GPay']));
        $resMode->assertStatus(200);
        $resMode->assertSee($rUpi->receipt_number);
        $resMode->assertDontSee($rCash->receipt_number);

        // Filter by Date Range (start_date = 2026-09-20)
        $resDate = $this->get(route('accounts.receipts.index', ['start_date' => '2026-09-20']));
        $resDate->assertStatus(200);
        $resDate->assertSee($rUpi->receipt_number);
        $resDate->assertDontSee($rCash->receipt_number);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 4: CUSTOMER FINANCIAL LEDGER & STATEMENT OF ACCOUNT
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R4.1: Verify customer ledger directory shows total debits, credits, and net balances.
     */
    public function test_customer_ledger_index_directory_and_kpis(): void
    {
        $companyName = 'Ledger Test Minerals ' . uniqid();
        $customer = $this->createTestCustomer(['company_name' => $companyName]);

        Quotation::create([
            'quotation_number' => 'GTMS/QTN/' . date('Y') . '/' . rand(1000, 9999),
            'customer_id'      => $customer->id,
            'subtotal'         => 100000.00,
            'tax_rate'         => 18.00,
            'tax_amount'       => 18000.00,
            'total_amount'     => 118000.00,
            'status'           => 'accepted',
            'branch_id'        => $this->branch->id,
        ]);

        PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/' . rand(1000, 9999),
            'customer_id'      => $customer->id,
            'amount_paid'      => 45000.00,
            'balance_due'      => 73000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'transaction_date' => '2026-09-29',
            'branch_id'        => $this->branch->id,
        ]);

        $response = $this->get(route('accounts.ledger.index', ['q' => $companyName]));
        $response->assertStatus(200);
        $response->assertSee($companyName);
        $response->assertSee('118,000.00');
        $response->assertSee('45,000.00');
    }

    /**
     * R4.2: Verify customer ledger dossier displays chronological transactions with running balance.
     */
    public function test_customer_ledger_dossier_chronological_running_balance(): void
    {
        $customer = $this->createTestCustomer();

        // Transaction 1: Quotation (Debit 100,000)
        $q1 = Quotation::create([
            'quotation_number' => 'GTMS/QTN/' . date('Y') . '/9081',
            'customer_id'      => $customer->id,
            'subtotal'         => 84745.76,
            'tax_rate'         => 18.00,
            'tax_amount'       => 15254.24,
            'total_amount'     => 100000.00,
            'status'           => 'accepted',
            'branch_id'        => $this->branch->id,
        ]);
        \Illuminate\Support\Facades\DB::table('quotations')
            ->where('id', $q1->id)
            ->update(['created_at' => Carbon::now()->subDays(5)]);

        // Transaction 2: Receipt (Credit 40,000)
        $r1 = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9081',
            'customer_id'      => $customer->id,
            'amount_paid'      => 40000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'transaction_date' => Carbon::now()->subDays(3)->toDateString(),
            'branch_id'        => $this->branch->id,
        ]);
        \Illuminate\Support\Facades\DB::table('payment_receipts')
            ->where('id', $r1->id)
            ->update(['created_at' => Carbon::now()->subDays(3)]);

        // Transaction 3: Quotation (Debit 50,000)
        $q2 = Quotation::create([
            'quotation_number' => 'GTMS/QTN/' . date('Y') . '/9082',
            'customer_id'      => $customer->id,
            'subtotal'         => 42372.88,
            'tax_rate'         => 18.00,
            'tax_amount'       => 7627.12,
            'total_amount'     => 50000.00,
            'status'           => 'accepted',
            'branch_id'        => $this->branch->id,
        ]);
        \Illuminate\Support\Facades\DB::table('quotations')
            ->where('id', $q2->id)
            ->update(['created_at' => Carbon::now()->subDays(1)]);

        $response = $this->get(route('accounts.ledger.show', $customer->id));
        $response->assertStatus(200);

        // Verify view data contains calculated running balances:
        // Tx1: 100,000; Tx2: 100,000 - 40,000 = 60,000; Tx3: 60,000 + 50,000 = 110,000
        $transactions = $response->viewData('transactions');
        $this->assertCount(3, $transactions);
        $this->assertEquals(100000.00, $transactions[0]->running_balance);
        $this->assertEquals(60000.00, $transactions[1]->running_balance);
        $this->assertEquals(110000.00, $transactions[2]->running_balance);
    }

    /**
     * R4.3: Verify printable customer statement rendering with net dues in words and digital seal.
     */
    public function test_printable_customer_statement_view_rendering(): void
    {
        $customer = $this->createTestCustomer(['company_name' => 'Kaveri Minerals & Aggregates']);

        Quotation::create([
            'quotation_number' => 'GTMS/QTN/' . date('Y') . '/9091',
            'customer_id'      => $customer->id,
            'subtotal'         => 100000.00,
            'tax_rate'         => 18.00,
            'tax_amount'       => 18000.00,
            'total_amount'     => 118000.00,
            'status'           => 'accepted',
            'branch_id'        => $this->branch->id,
        ]);

        PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9091',
            'customer_id'      => $customer->id,
            'amount_paid'      => 50000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'transaction_date' => '2026-09-29',
            'branch_id'        => $this->branch->id,
        ]);

        $response = $this->get(route('accounts.ledger.print', $customer->id));
        $response->assertStatus(200);
        $response->assertSee('STATEMENT OF ACCOUNT');
        $response->assertSee('Kaveri Minerals &amp; Aggregates', false);
        $response->assertSee('Net Balance Due (in words):');
        // Net balance = 118,000 - 50,000 = 68,000
        $response->assertSee('Sixty Eight Thousand');
        $response->assertSee('Dr. S. Karuppannan, M.Sc., Ph.D.');
        $response->assertSee('Authorized Signatory');
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 5: FINANCIAL REPORTS & STREAMED CSV EXPORT
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R5.1: Verify financial reports index renders 4 KPI summary cards.
     */
    public function test_financial_reports_index_renders_4_kpi_summary_cards(): void
    {
        $response = $this->get(route('accounts.reports.index'));
        $response->assertStatus(200);

        // Verify the 4 KPI card sections exist in HTML
        $response->assertSee('Total Collected (All-Time)');
        $response->assertSee('Month-to-Date (MTD)');
        $response->assertSee('Outstanding Receivables');
        $response->assertSee('Quotations Pipeline');
    }

    /**
     * R5.2: Verify financial reports multi-parametric filtering by date, customer, and mode.
     */
    public function test_financial_reports_multi_parametric_filtering(): void
    {
        $customerA = $this->createTestCustomer(['company_name' => 'Report Client A']);
        $customerB = $this->createTestCustomer(['company_name' => 'Report Client B']);

        $rA = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9101',
            'customer_id'      => $customerA->id,
            'application_type' => 'mining',
            'amount_paid'      => 40000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'transaction_date' => '2026-09-15',
            'branch_id'        => $this->branch->id,
        ]);

        $rB = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9102',
            'customer_id'      => $customerB->id,
            'application_type' => 'dgps',
            'amount_paid'      => 20000.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-20',
            'branch_id'        => $this->branch->id,
        ]);

        // Filter by Customer A and Mode NEFT/RTGS
        $response = $this->get(route('accounts.reports.index', [
            'customer_id'  => $customerA->id,
            'payment_mode' => 'NEFT/RTGS',
        ]));

        $response->assertStatus(200);
        $response->assertSee($rA->receipt_number);
        $response->assertDontSee($rB->receipt_number);
    }

    /**
     * R5.3: Verify streamed CSV export returns HTTP 200, UTF-8 BOM, text/csv headers, and accurate data rows.
     */
    public function test_streamed_csv_export_endpoint_returns_valid_csv_headers_bom_and_matching_rows(): void
    {
        $customer = $this->createTestCustomer(['company_name' => 'Export Mining Ltd']);

        $receipt = PaymentReceipt::create([
            'receipt_number'   => 'GTMS/REC/' . date('Y') . '/9110',
            'customer_id'      => $customer->id,
            'application_type' => 'mining',
            'amount_paid'      => 75000.00,
            'balance_due'      => 25000.00,
            'payment_mode'     => 'NEFT/RTGS',
            'bank_name'        => 'ICICI Bank',
            'reference_number' => 'UTR1122334455',
            'transaction_date' => '2026-09-29',
            'notes'            => 'CSV Export Test Record',
            'branch_id'        => $this->branch->id,
        ]);

        $response = $this->get(route('accounts.reports.export-csv', ['customer_id' => $customer->id]));
        $response->assertStatus(200);

        // Verify CSV HTTP Headers
        $contentType = $response->headers->get('Content-Type');
        $this->assertStringContainsString('text/csv', $contentType);

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment;', $disposition);
        $this->assertStringContainsString('GTMS_Financial_Report_', $disposition);

        // Get Streamed Content
        $content = $response->streamedContent();

        // Verify UTF-8 BOM
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content, 'CSV must start with UTF-8 BOM for Excel compatibility');

        // Verify CSV Columns
        $this->assertStringContainsString('Receipt No', $content);
        $this->assertStringContainsString('Customer Name', $content);
        $this->assertStringContainsString('Application Type', $content);
        $this->assertStringContainsString('Amount Paid (INR)', $content);
        $this->assertStringContainsString('Balance Due (INR)', $content);

        // Verify Record Data
        $this->assertStringContainsString($receipt->receipt_number, $content);
        $this->assertStringContainsString('Export Mining Ltd', $content);
        $this->assertStringContainsString('75000.00', $content);
        $this->assertStringContainsString('25000.00', $content);
        $this->assertStringContainsString('UTR1122334455', $content);
    }

    /* ══════════════════════════════════════════════════════════════════════
     * REQUIREMENT 6: SPATIE RBAC PERMISSION GATING & SIDEBAR
     * ══════════════════════════════════════════════════════════════════════ */

    /**
     * R6.1: Verify unauthenticated guest is redirected to login for accounts routes.
     */
    public function test_unauthenticated_guest_is_redirected_to_login_on_accounts_routes(): void
    {
        Auth::logout();

        $this->get(route('accounts.quotations.index'))->assertRedirect(route('login'));
        $this->get(route('accounts.payments.create'))->assertRedirect(route('login'));
        $this->get(route('accounts.ledger.index'))->assertRedirect(route('login'));
        $this->get(route('accounts.reports.index'))->assertRedirect(route('login'));
    }

    /**
     * R6.2: Verify authenticated user without account.* permissions receives 403 Forbidden.
     */
    public function test_user_without_account_permissions_receives_403_forbidden(): void
    {
        // User with no account permissions
        $user = $this->createNonAdminUserWithPermissions(['branch.view']);
        $this->actingAs($user);

        $this->get(route('accounts.quotations.index'))->assertStatus(403);
        $this->get(route('accounts.payments.create'))->assertStatus(403);
        $this->get(route('accounts.ledger.index'))->assertStatus(403);
        $this->get(route('accounts.reports.index'))->assertStatus(403);
    }

    /**
     * R6.3: Verify user with account.view can access view routes but receives 403 on create/store.
     */
    public function test_user_with_account_view_can_access_view_routes_but_receives_403_on_create_routes(): void
    {
        $user = $this->createNonAdminUserWithPermissions(['account.view']);
        $this->actingAs($user);

        // Allowed View Routes (200 OK)
        $this->get(route('accounts.quotations.index'))->assertStatus(200);
        $this->get(route('accounts.receipts.index'))->assertStatus(200);
        $this->get(route('accounts.ledger.index'))->assertStatus(200);
        $this->get(route('accounts.reports.index'))->assertStatus(200);

        // Forbidden Create Routes (403 Forbidden)
        $this->get(route('accounts.quotations.create'))->assertStatus(403);
        $this->post(route('accounts.quotations.store'), [])->assertStatus(403);
        $this->get(route('accounts.payments.create'))->assertStatus(403);
        $this->post(route('accounts.payments.store'), [])->assertStatus(403);
    }

    /**
     * R6.4: Verify user with account.create can access creation forms and execute store actions.
     */
    public function test_user_with_account_create_can_access_creation_forms_and_store(): void
    {
        $user = $this->createNonAdminUserWithPermissions(['account.view', 'account.create']);
        $this->actingAs($user);

        $this->get(route('accounts.quotations.create'))->assertStatus(200);
        $this->get(route('accounts.payments.create'))->assertStatus(200);

        $customer = $this->createTestCustomer();

        // Should be authorized to store a payment
        $response = $this->post(route('accounts.payments.store'), [
            'customer_id'      => $customer->id,
            'application_type' => 'general',
            'amount_paid'      => 10000.00,
            'payment_mode'     => 'Cash',
            'transaction_date' => '2026-09-29',
        ]);

        $receipt = PaymentReceipt::where('customer_id', $customer->id)->latest('id')->first();
        $this->assertNotNull($receipt);
        $response->assertRedirect(route('accounts.receipts.show', $receipt->id));
    }

    /**
     * R6.5: Verify sidebar contains Accounts navigation for authorized users.
     */
    public function test_sidebar_displays_accounts_navigation_for_authorized_users(): void
    {
        $user = $this->createNonAdminUserWithPermissions(['account.view']);
        $this->actingAs($user);

        $response = $this->get(route('accounts.quotations.index'));
        $response->assertStatus(200);
        $response->assertSee('<span class="nav-text">Accounts</span>', false);
        $response->assertSee('Quotations');
        $response->assertSee('Payment Receipts');
        $response->assertSee('Customer Ledger');
        $response->assertSee('Financial Reports');
    }
}
