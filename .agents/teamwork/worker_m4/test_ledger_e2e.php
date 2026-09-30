<?php

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

echo "=== STARTING E2E TRANSACTIONAL ARITHMETIC VERIFICATION ===\n\n";

DB::beginTransaction();

try {
    $admin = User::first();
    Auth::login($admin);

    $customer = Customer::create([
        'customer_name' => 'Dr. E2E Quarryman',
        'company_name'  => 'M/s. Salem Granite Industries',
        'mobile_num'    => '9443219876',
        'email'         => 'quarry_e2e@example.com',
        'mimas_no'      => 'TN/MIN/2026/E2E',
        'address'       => 'S.F. 102/1A, Chinnagoundanur, Salem',
    ]);

    echo "[PASS] Created test customer: {$customer->company_name} (ID: {$customer->id})\n";

    // 1. Create Quotation 1: ₹ 1,50,000.00
    $qtn1 = Quotation::create([
        'quotation_number' => 'GTMS/QTN/2026/9001',
        'customer_id'      => $customer->id,
        'customer_name'    => $customer->customer_name,
        'company_name'     => $customer->company_name,
        'phone'            => $customer->mobile_num,
        'email'            => $customer->email,
        'quarry_name'      => 'Salem Black Granite Quarry',
        'subtotal'         => 127118.64,
        'tax_rate'         => 18.00,
        'tax_amount'       => 22881.36,
        'total_amount'     => 150000.00,
        'status'           => 'accepted',
        'created_by'       => $admin->id,
    ]);

    // 2. Create Payment Receipt 1: ₹ 50,000.00
    $rec1 = PaymentReceipt::create([
        'receipt_number'   => 'GTMS/REC/2026/9001',
        'customer_id'      => $customer->id,
        'quotation_id'     => $qtn1->id,
        'application_type' => 'mining',
        'application_id'   => 1,
        'amount_paid'      => 50000.00,
        'balance_due'      => 100000.00,
        'previous_paid'    => 0.00,
        'payment_mode'     => 'NEFT/RTGS',
        'bank_name'        => 'State Bank of India',
        'reference_number' => 'SBIN98765432101',
        'transaction_date' => now()->toDateString(),
        'notes'            => 'Advance remittance for mining plan',
        'created_by'       => $admin->id,
    ]);

    // 3. Create Quotation 2: ₹ 35,000.00
    $qtn2 = Quotation::create([
        'quotation_number' => 'GTMS/QTN/2026/9002',
        'customer_id'      => $customer->id,
        'customer_name'    => $customer->customer_name,
        'company_name'     => $customer->company_name,
        'phone'            => $customer->mobile_num,
        'email'            => $customer->email,
        'quarry_name'      => 'DGPS Demarcation Survey',
        'subtotal'         => 29661.02,
        'tax_rate'         => 18.00,
        'tax_amount'       => 5338.98,
        'total_amount'     => 35000.00,
        'status'           => 'sent',
        'created_by'       => $admin->id,
    ]);

    // 4. Create Payment Receipt 2: ₹ 35,000.00
    $rec2 = PaymentReceipt::create([
        'receipt_number'   => 'GTMS/REC/2026/9002',
        'customer_id'      => $customer->id,
        'quotation_id'     => $qtn2->id,
        'application_type' => 'dgps',
        'application_id'   => 1,
        'amount_paid'      => 35000.00,
        'balance_due'      => 0.00,
        'previous_paid'    => 0.00,
        'payment_mode'     => 'UPI/GPay',
        'bank_name'        => 'HDFC Bank',
        'reference_number' => 'UPI98765432102',
        'transaction_date' => now()->toDateString(),
        'notes'            => 'Full settlement for DGPS survey',
        'created_by'       => $admin->id,
    ]);

    echo "[PASS] Created test debits (Quotations: 150000 + 35000 = 185000) and credits (Receipts: 50000 + 35000 = 85000)\n";

    // Test Ledger Show calculations
    $ledgerCtrl = app(\App\Http\Controllers\Accounts\CustomerLedgerController::class);
    $req = Request::create("/accounts/ledger/{$customer->slug}", 'GET');
    $res = $ledgerCtrl->show($req, $customer->slug);
    $data = $res->getData();

    echo "\n--- Verifying Customer Ledger Calculations ---\n";
    echo "  - Total Billed: ₹ " . number_format($data['allTimeBilled'], 2) . " (Expected: 185,000.00)\n";
    echo "  - Total Received: ₹ " . number_format($data['allTimeReceived'], 2) . " (Expected: 85,000.00)\n";
    echo "  - Net Balance: ₹ " . number_format($data['netBalance'], 2) . " (Expected: 100,000.00)\n";

    assert(abs($data['allTimeBilled'] - 185000.00) < 0.01, "Total billed mismatch");
    assert(abs($data['allTimeReceived'] - 85000.00) < 0.01, "Total received mismatch");
    assert(abs($data['netBalance'] - 100000.00) < 0.01, "Net balance mismatch");
    assert(count($data['transactions']) === 4, "Expected 4 transactions, got " . count($data['transactions']));

    $finalTx = $data['transactions']->last();
    echo "  - Final Running Balance: ₹ " . number_format($finalTx->running_balance, 2) . " (Expected: 100,000.00)\n";
    assert(abs($finalTx->running_balance - 100000.00) < 0.01, "Final running balance mismatch");
    echo "[PASS] Customer Ledger running balance arithmetic verified!\n";

    // Test Print Statement
    $resPrint = $ledgerCtrl->printStatement($req, $customer->slug);
    $dataPrint = $resPrint->getData();
    echo "  - Statement Net Due in Words: {$dataPrint['netBalanceInWords']}\n";
    assert(str_contains($dataPrint['netBalanceInWords'], 'One Lakh') || str_contains($dataPrint['netBalanceInWords'], '100,000'));
    echo "[PASS] Statement Print rendering and Indian currency conversion verified!\n";

    // Test Financial Reports Filter
    $reportCtrl = app(\App\Http\Controllers\Accounts\FinancialReportController::class);
    $reqFiltered = Request::create("/accounts/reports?customer_id={$customer->id}&payment_mode=UPI/GPay", 'GET');
    $resRep = $reportCtrl->index($reqFiltered);
    $dataRep = $resRep->getData();
    echo "\n--- Verifying Reports Filtering ---\n";
    echo "  - Filtered Receipts Count: " . $dataRep['receipts']->total() . " (Expected: 1)\n";
    assert($dataRep['receipts']->total() === 1, "Expected 1 filtered receipt");
    $recRow = $dataRep['receipts']->first();
    assert($recRow->receipt_number === 'GTMS/REC/2026/9002');
    echo "[PASS] Reports filtering by customer and payment mode verified!\n";

    // Test CSV Export with Filter
    $csvRes = $reportCtrl->exportCsv($reqFiltered);
    ob_start();
    $csvRes->sendContent();
    $csvOutput = ob_get_clean();

    assert(str_contains($csvOutput, 'GTMS/REC/2026/9002'));
    assert(str_contains($csvOutput, 'M/s. Salem Granite Industries'));
    assert(str_contains($csvOutput, 'UPI/GPay'));
    assert(str_contains($csvOutput, '35000.00'));
    echo "[PASS] Streamed CSV contains exact filtered transaction data!\n";

} finally {
    DB::rollBack();
    echo "\n[INFO] Transaction rolled back cleanly. Database restored to original state.\n";
}

echo "\n=== E2E ARITHMETIC & WORKFLOW VERIFICATION 100% COMPLETE AND PASSING! ===\n";
