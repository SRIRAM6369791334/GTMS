<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\Quotation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReportController extends Controller implements HasMiddleware
{
    /**
     * Define route middleware for permission gating.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:account.view'),
        ];
    }

    /**
     * Display the centralized financial reporting dashboard.
     * Features 4 KPI summary cards, multi-parametric filter bar, and paginated transaction table.
     */
    public function index(Request $request): View
    {
        // 1. KPI Summary Metrics
        $totalCollectedAllTime = (float) PaymentReceipt::sum('amount_paid');
        $totalCollectedMtd = (float) PaymentReceipt::whereMonth('transaction_date', date('m'))
            ->whereYear('transaction_date', date('Y'))
            ->sum('amount_paid');

        $totalOutstandingReceivables = (float) ApplicationPayment::sum('pending_amount');
        if ($totalOutstandingReceivables <= 0) {
            $totalBilled = (float) Quotation::sum('total_amount');
            if ($totalBilled > $totalCollectedAllTime) {
                $totalOutstandingReceivables = round($totalBilled - $totalCollectedAllTime, 2);
            }
        }

        $totalQuotationsCount = Quotation::count();
        $totalQuotationsValue = (float) Quotation::sum('total_amount');

        $kpis = [
            'total_collected'   => $totalCollectedAllTime,
            'mtd_collected'     => $totalCollectedMtd,
            'total_outstanding' => $totalOutstandingReceivables,
            'quotations_count'  => $totalQuotationsCount,
            'quotations_value'  => $totalQuotationsValue,
        ];

        // 2. Build multi-dimensional analytics for charts (Week, Year, District, User, Module, Mode)
        $analytics = $this->buildAnalyticsData($request);

        // 3. Build filtered transactions query
        $query = $this->buildFilteredQuery($request);

        // 4. Paginated results
        $receipts = $query->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // 5. Dropdown list of customers
        $customers = Customer::orderBy('customer_name')
            ->get(['id', 'customer_name', 'company_name']);

        return view('pages.accounts.reports.index', compact(
            'receipts',
            'customers',
            'kpis',
            'analytics'
        ));
    }

    /**
     * Native Streamed CSV download using Symfony StreamedResponse.
     * Applies identical multi-parametric filter criteria and streams data with UTF-8 BOM and chunking.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = $this->buildFilteredQuery($request)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        $timestamp = date('Ymd_His');
        $fileName = "GTMS_Financial_Report_{$timestamp}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Microsoft Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header Row
            fputcsv($handle, [
                'Receipt No',
                'Date',
                'Customer Name',
                'Company Name',
                'Application Type',
                'Application Ref',
                'Payment Mode',
                'Bank Name',
                'Reference / UTR',
                'Amount Paid (INR)',
                'Balance Due (INR)',
                'Recorded By',
                'Notes',
            ]);

            // Stream chunks to ensure zero memory bloat
            $query->chunk(250, function ($receipts) use ($handle) {
                foreach ($receipts as $receipt) {
                    $dateStr = $receipt->transaction_date
                        ? $receipt->transaction_date->format('Y-m-d')
                        : ($receipt->created_at ? $receipt->created_at->format('Y-m-d') : '');

                    $appTypeStr = $receipt->application_type
                        ? ucfirst(str_replace('_', ' ', $receipt->application_type))
                        : 'General';

                    fputcsv($handle, [
                        $receipt->receipt_number,
                        $dateStr,
                        $receipt->customer?->customer_name ?? 'N/A',
                        $receipt->customer?->company_name ?? '',
                        $appTypeStr,
                        $receipt->application_reference,
                        $receipt->payment_mode ?? '',
                        $receipt->bank_name ?? '',
                        $receipt->reference_number ?? '',
                        number_format((float) $receipt->amount_paid, 2, '.', ''),
                        number_format((float) $receipt->balance_due, 2, '.', ''),
                        $receipt->creator?->name ?? 'System',
                        $receipt->notes ?? '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Construct the base filtered query for PaymentReceipt records.
     */
    protected function buildFilteredQuery(Request $request): Builder
    {
        $query = PaymentReceipt::with(['customer.district', 'creator', 'branch']);

        // Date Range: from_date / start_date
        $fromDate = $request->query('from_date', $request->query('start_date'));
        if ($fromDate) {
            $query->whereDate('transaction_date', '>=', $fromDate);
        }

        // Date Range: to_date / end_date
        $toDate = $request->query('to_date', $request->query('end_date'));
        if ($toDate) {
            $query->whereDate('transaction_date', '<=', $toDate);
        }

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Application Module Type Filter
        if ($request->filled('application_type')) {
            $query->where('application_type', $request->application_type);
        }

        // Payment Mode Filter
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        // Search Query (Receipt No, Cheque/UTR, Bank, Customer details)
        if ($request->filled('q') || $request->filled('search')) {
            $searchTerm = trim($request->get('search', $request->get('q')));
            $query->where(function ($sub) use ($searchTerm) {
                $sub->where('receipt_number', 'like', "%{$searchTerm}%")
                    ->orWhere('reference_number', 'like', "%{$searchTerm}%")
                    ->orWhere('bank_name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($cq) use ($searchTerm) {
                        $cq->where('customer_name', 'like', "%{$searchTerm}%")
                            ->orWhere('company_name', 'like', "%{$searchTerm}%")
                            ->orWhere('mobile_num', 'like', "%{$searchTerm}%")
                            ->orWhere('mimas_no', 'like', "%{$searchTerm}%")
                            ->orWhere('mimas_number', 'like', "%{$searchTerm}%");
                    });
            });
        }

        return $query;
    }

    /**
     * Compute multi-dimensional financial analytics for interactive bar & comparison charts:
     * - Week-wise intake velocity
     * - Year-on-Year (YoY) / Month-wise comparative intake
     * - District-wise revenue breakdown
     * - User / Collector performance ranking
     * - Statutory Service module distribution
     * - Payment Mode collection split
     */
    protected function buildAnalyticsData(Request $request): array
    {
        // 1. Weekly collections (Last 8 active weeks)
        $rawWeeks = PaymentReceipt::selectRaw("
                YEAR(COALESCE(transaction_date, created_at)) as yr,
                WEEK(COALESCE(transaction_date, created_at), 1) as wk,
                MIN(COALESCE(transaction_date, created_at)) as start_date,
                MAX(COALESCE(transaction_date, created_at)) as end_date,
                SUM(amount_paid) as total_amount,
                COUNT(id) as receipt_count
            ")
            ->groupBy('yr', 'wk')
            ->orderByDesc('yr')
            ->orderByDesc('wk')
            ->take(8)
            ->get()
            ->reverse()
            ->values();

        $weekLabels = [];
        $weekAmounts = [];
        $weekCounts = [];
        $weekTable = [];

        foreach ($rawWeeks as $w) {
            $label = 'Wk ' . $w->wk . ' (' . date('M d', strtotime($w->start_date)) . ')';
            $amount = (float) $w->total_amount;
            $count = (int) $w->receipt_count;

            $weekLabels[] = $label;
            $weekAmounts[] = $amount;
            $weekCounts[] = $count;
            $weekTable[] = [
                'label'       => $label,
                'year'        => $w->yr,
                'week'        => $w->wk,
                'range'       => date('d M Y', strtotime($w->start_date)) . ' - ' . date('d M Y', strtotime($w->end_date)),
                'amount'      => $amount,
                'count'       => $count,
                'avg_ticket'  => $count > 0 ? round($amount / $count, 2) : 0,
            ];
        }

        // 2. Year-on-Year & Month-wise collections (12 months comparison: Current Year vs Previous Year)
        $currYear = (int) date('Y');
        $prevYear = $currYear - 1;

        $rawMonths = PaymentReceipt::selectRaw("
                YEAR(COALESCE(transaction_date, created_at)) as yr,
                MONTH(COALESCE(transaction_date, created_at)) as mo,
                SUM(amount_paid) as total_amount,
                COUNT(id) as receipt_count
            ")
            ->whereRaw("YEAR(COALESCE(transaction_date, created_at)) IN (?, ?)", [$prevYear, $currYear])
            ->groupBy('yr', 'mo')
            ->get();

        $monthMap = [];
        foreach ($rawMonths as $rm) {
            $monthMap[$rm->yr . '_' . $rm->mo] = [
                'amount' => (float) $rm->total_amount,
                'count'  => (int) $rm->receipt_count,
            ];
        }

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $currYearAmounts = [];
        $prevYearAmounts = [];
        $yoyTable = [];

        for ($m = 1; $m <= 12; $m++) {
            $mName = $monthNames[$m - 1];
            $currVal = $monthMap[$currYear . '_' . $m]['amount'] ?? 0.0;
            $prevVal = $monthMap[$prevYear . '_' . $m]['amount'] ?? 0.0;
            $currCount = $monthMap[$currYear . '_' . $m]['count'] ?? 0;
            $prevCount = $monthMap[$prevYear . '_' . $m]['count'] ?? 0;

            $currYearAmounts[] = $currVal;
            $prevYearAmounts[] = $prevVal;

            $diff = round($currVal - $prevVal, 2);
            $growthPct = ($prevVal > 0) ? round(($diff / $prevVal) * 100, 1) : ($currVal > 0 ? 100.0 : 0.0);

            $yoyTable[] = [
                'month_no'    => $m,
                'month_name'  => $mName,
                'current'     => $currVal,
                'previous'    => $prevVal,
                'diff'        => $diff,
                'growth_pct'  => $growthPct,
                'curr_count'  => $currCount,
                'prev_count'  => $prevCount,
            ];
        }

        // 3. District-wise Revenue Breakdown
        $totalCollectedAllTime = (float) PaymentReceipt::sum('amount_paid');
        $rawDistricts = PaymentReceipt::join('customers', 'customers.id', '=', 'payment_receipts.customer_id')
            ->leftJoin('districts', 'districts.id', '=', 'customers.district_id')
            ->selectRaw("
                COALESCE(districts.name, 'Unassigned') as district_name,
                SUM(payment_receipts.amount_paid) as total_amount,
                COUNT(payment_receipts.id) as receipt_count
            ")
            ->groupBy('districts.name')
            ->orderByDesc('total_amount')
            ->get();

        $districtLabels = [];
        $districtAmounts = [];
        $districtCounts = [];
        $districtTable = [];

        foreach ($rawDistricts as $d) {
            $amount = (float) $d->total_amount;
            $count = (int) $d->receipt_count;
            $share = ($totalCollectedAllTime > 0) ? round(($amount / $totalCollectedAllTime) * 100, 1) : 0;

            $districtLabels[] = $d->district_name;
            $districtAmounts[] = $amount;
            $districtCounts[] = $count;
            $districtTable[] = [
                'district_name' => $d->district_name,
                'amount'        => $amount,
                'count'         => $count,
                'share_pct'     => $share,
                'avg_ticket'    => $count > 0 ? round($amount / $count, 2) : 0,
            ];
        }

        // 4. User / Collector Performance Breakdown
        $rawUsers = PaymentReceipt::leftJoin('users', 'users.id', '=', 'payment_receipts.created_by')
            ->selectRaw("
                COALESCE(users.name, 'System Administrator') as staff_name,
                COALESCE(users.user_code, 'ADM') as staff_code,
                SUM(payment_receipts.amount_paid) as total_amount,
                COUNT(payment_receipts.id) as receipt_count
            ")
            ->groupBy('users.name', 'users.user_code')
            ->orderByDesc('total_amount')
            ->get();

        $userLabels = [];
        $userAmounts = [];
        $userCounts = [];
        $userTable = [];

        foreach ($rawUsers as $u) {
            $amount = (float) $u->total_amount;
            $count = (int) $u->receipt_count;
            $share = ($totalCollectedAllTime > 0) ? round(($amount / $totalCollectedAllTime) * 100, 1) : 0;

            $userLabels[] = $u->staff_name;
            $userAmounts[] = $amount;
            $userCounts[] = $count;
            $userTable[] = [
                'staff_name' => $u->staff_name,
                'staff_code' => $u->staff_code,
                'amount'     => $amount,
                'count'      => $count,
                'share_pct'  => $share,
                'avg_ticket' => $count > 0 ? round($amount / $count, 2) : 0,
            ];
        }

        // 5. Statutory Module Distribution Breakdown
        $moduleNamesMap = [
            'lease'          => 'Lease Applications',
            'mining'         => 'Mining Plans',
            'environment'    => 'Environmental Clearance',
            'ppt'            => 'PPT Department',
            'dgps'           => 'DGPS Surveys',
            'drone'          => 'Drone Surveys',
            'ec'             => 'EC Certificates',
            'ec_certificate' => 'EC Certificates',
            'ec_compliance'  => 'EC Compliance',
            'direct'         => 'General / Retainer',
        ];

        $rawModules = PaymentReceipt::selectRaw("
                COALESCE(application_type, 'direct') as app_type,
                SUM(amount_paid) as total_amount,
                COUNT(id) as receipt_count
            ")
            ->groupBy('app_type')
            ->orderByDesc('total_amount')
            ->get();

        $moduleLabels = [];
        $moduleAmounts = [];
        $moduleCounts = [];
        $moduleTable = [];

        foreach ($rawModules as $m) {
            $cleanType = strtolower(trim($m->app_type));
            $label = $moduleNamesMap[$cleanType] ?? ucfirst(str_replace('_', ' ', $cleanType));
            $amount = (float) $m->total_amount;
            $count = (int) $m->receipt_count;
            $share = ($totalCollectedAllTime > 0) ? round(($amount / $totalCollectedAllTime) * 100, 1) : 0;

            $moduleLabels[] = $label;
            $moduleAmounts[] = $amount;
            $moduleCounts[] = $count;
            $moduleTable[] = [
                'module_type' => $cleanType,
                'module_name' => $label,
                'amount'      => $amount,
                'count'       => $count,
                'share_pct'   => $share,
                'avg_ticket'  => $count > 0 ? round($amount / $count, 2) : 0,
            ];
        }

        // 6. Payment Mode Distribution
        $rawModes = PaymentReceipt::selectRaw("
                COALESCE(payment_mode, 'Cash') as mode_name,
                SUM(amount_paid) as total_amount,
                COUNT(id) as receipt_count
            ")
            ->groupBy('mode_name')
            ->orderByDesc('total_amount')
            ->get();

        $modeLabels = [];
        $modeAmounts = [];
        $modeCounts = [];
        $modeTable = [];

        foreach ($rawModes as $pm) {
            $amount = (float) $pm->total_amount;
            $count = (int) $pm->receipt_count;
            $share = ($totalCollectedAllTime > 0) ? round(($amount / $totalCollectedAllTime) * 100, 1) : 0;

            $modeLabels[] = $pm->mode_name;
            $modeAmounts[] = $amount;
            $modeCounts[] = $count;
            $modeTable[] = [
                'mode_name'  => $pm->mode_name,
                'amount'     => $amount,
                'count'      => $count,
                'share_pct'  => $share,
                'avg_ticket' => $count > 0 ? round($amount / $count, 2) : 0,
            ];
        }

        return [
            'week' => [
                'labels'  => $weekLabels,
                'amounts' => $weekAmounts,
                'counts'  => $weekCounts,
                'table'   => $weekTable,
            ],
            'yoy' => [
                'months'     => $monthNames,
                'curr_year'  => $currYear,
                'prev_year'  => $prevYear,
                'curr_series'=> $currYearAmounts,
                'prev_series'=> $prevYearAmounts,
                'table'      => $yoyTable,
                'curr_total' => array_sum($currYearAmounts),
                'prev_total' => array_sum($prevYearAmounts),
            ],
            'district' => [
                'labels'  => $districtLabels,
                'amounts' => $districtAmounts,
                'counts'  => $districtCounts,
                'table'   => $districtTable,
            ],
            'user' => [
                'labels'  => $userLabels,
                'amounts' => $userAmounts,
                'counts'  => $userCounts,
                'table'   => $userTable,
            ],
            'module' => [
                'labels'  => $moduleLabels,
                'amounts' => $moduleAmounts,
                'counts'  => $moduleCounts,
                'table'   => $moduleTable,
            ],
            'mode' => [
                'labels'  => $modeLabels,
                'amounts' => $modeAmounts,
                'counts'  => $modeCounts,
                'table'   => $modeTable,
            ],
        ];
    }
}
