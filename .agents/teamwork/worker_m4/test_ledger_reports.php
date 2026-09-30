<?php

require __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Models\User;
use App\Models\Quotation;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== STARTING GTMS ACCOUNTS MILESTONE 4 VERIFICATION ===\n\n";

// 1. Authenticate as Admin user
$admin = User::whereHas('roles', function ($q) {
    $q->whereIn('name', ['Admin', 'Super Admin']);
})->first();

if (!$admin) {
    $admin = User::first();
}

Auth::login($admin);
echo "[PASS] Authenticated as User: {$admin->email} (ID: {$admin->id})\n";

// 2. Test CustomerLedgerController@index
echo "\n--- Testing CustomerLedgerController@index ---\n";
$ledgerCtrl = app(\App\Http\Controllers\Accounts\CustomerLedgerController::class);
$request = Request::create('/accounts/ledger', 'GET');
$response = $ledgerCtrl->index($request);
echo "[PASS] CustomerLedgerController@index rendered view: " . $response->name() . "\n";
$viewData = $response->getData();
echo "  - Total Customers Count: " . $viewData['totalCustomersCount'] . "\n";
echo "  - Overall Total Billed: ₹ " . number_format($viewData['overallTotalBilled'], 2) . "\n";
echo "  - Overall Total Received: ₹ " . number_format($viewData['overallTotalReceived'], 2) . "\n";
echo "  - Overall Total Outstanding: ₹ " . number_format($viewData['overallTotalOutstanding'], 2) . "\n";

// Render view to ensure no Blade compile/syntax errors
$html = $response->render();
assert(str_contains($html, 'Customer Financial Ledger'));
echo "[PASS] CustomerLedger index Blade rendered without errors (" . strlen($html) . " bytes)\n";

// 3. Test CustomerLedgerController@show
echo "\n--- Testing CustomerLedgerController@show ---\n";
$customer = Customer::first();
echo "[INFO] Testing Customer: {$customer->company_name} (Slug: {$customer->slug}, ID: {$customer->id})\n";

$request = Request::create("/accounts/ledger/{$customer->slug}", 'GET');
$response = $ledgerCtrl->show($request, $customer->slug);
echo "[PASS] CustomerLedgerController@show (by slug) rendered view: " . $response->name() . "\n";
$html = $response->render();
assert(str_contains($html, 'Customer Financial Dossier'));
echo "[PASS] CustomerLedger show Blade rendered successfully (" . strlen($html) . " bytes)\n";

// Test resolve by integer ID as well
$request = Request::create("/accounts/ledger/{$customer->id}", 'GET');
$response = $ledgerCtrl->show($request, (string)$customer->id);
echo "[PASS] CustomerLedgerController@show (by ID) resolved and rendered view: " . $response->name() . "\n";

// 4. Test CustomerLedgerController@printStatement
echo "\n--- Testing CustomerLedgerController@printStatement ---\n";
$request = Request::create("/accounts/ledger/{$customer->slug}/print", 'GET');
$response = $ledgerCtrl->printStatement($request, $customer->slug);
echo "[PASS] CustomerLedgerController@printStatement rendered view: " . $response->name() . "\n";
$html = $response->render();
assert(str_contains($html, 'STATEMENT OF ACCOUNT'));
assert(str_contains($html, 'no-print-bar'));
assert(str_contains($html, 'GEO TECHNICAL MINING SOLUTIONS'));
echo "[PASS] Statement print Blade rendered successfully (" . strlen($html) . " bytes)\n";

// 5. Test FinancialReportController@index
echo "\n--- Testing FinancialReportController@index ---\n";
$reportCtrl = app(\App\Http\Controllers\Accounts\FinancialReportController::class);
$request = Request::create('/accounts/reports', 'GET');
$response = $reportCtrl->index($request);
echo "[PASS] FinancialReportController@index rendered view: " . $response->name() . "\n";
$viewData = $response->getData();
echo "  - KPI Total Collected: ₹ " . number_format($viewData['kpis']['total_collected'], 2) . "\n";
echo "  - KPI MTD Collected: ₹ " . number_format($viewData['kpis']['mtd_collected'], 2) . "\n";
echo "  - KPI Outstanding Receivables: ₹ " . number_format($viewData['kpis']['total_outstanding'], 2) . "\n";
echo "  - KPI Quotations Value: ₹ " . number_format($viewData['kpis']['quotations_value'], 2) . " ({$viewData['kpis']['quotations_count']} issued)\n";
$html = $response->render();
assert(str_contains($html, 'Financial Reports &amp; Reconciliation'));
echo "[PASS] Financial Reports Blade rendered successfully (" . strlen($html) . " bytes)\n";

// 6. Test FinancialReportController@exportCsv
echo "\n--- Testing FinancialReportController@exportCsv ---\n";
$request = Request::create('/accounts/reports/export-csv', 'GET');
$csvResponse = $reportCtrl->exportCsv($request);
assert($csvResponse instanceof \Symfony\Component\HttpFoundation\StreamedResponse);
echo "[PASS] Export CSV returned instance of StreamedResponse\n";
echo "  - Content-Type: " . $csvResponse->headers->get('Content-Type') . "\n";
echo "  - Content-Disposition: " . $csvResponse->headers->get('Content-Disposition') . "\n";
assert(str_contains($csvResponse->headers->get('Content-Type'), 'text/csv'));
assert(str_contains($csvResponse->headers->get('Content-Disposition'), 'attachment; filename="GTMS_Financial_Report_'));

// Capture streamed output
ob_start();
$csvResponse->sendContent();
$csvContent = ob_get_clean();

echo "  - Streamed CSV Content Length: " . strlen($csvContent) . " bytes\n";
echo "  - Streamed CSV Raw Preview:\n" . substr($csvContent, 0, 300) . "\n";
assert(str_starts_with($csvContent, "\xEF\xBB\xBF")); // UTF-8 BOM
echo "[PASS] CSV begins with valid UTF-8 BOM\n";
assert(str_contains($csvContent, 'Receipt No') && str_contains($csvContent, 'Amount Paid (INR)'));
echo "[PASS] CSV contains expected header columns\n";

// 7. Verify Indian Currency Words Conversion
echo "\n--- Testing Indian Currency Conversion ---\n";
$testNum = 125430.50;
$words = Quotation::convertToIndianCurrencyWords($testNum);
echo "  - ₹ 1,25,430.50 in words: {$words}\n";
assert(str_contains($words, 'Lakh'));

echo "\n=== ALL MILESTONE 4 VERIFICATION CHECKS PASSED SUCCESSFULLY! ===\n";
