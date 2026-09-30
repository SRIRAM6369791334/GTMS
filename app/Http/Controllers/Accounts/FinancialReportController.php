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

        // 2. Build filtered transactions query
        $query = $this->buildFilteredQuery($request);

        // 3. Paginated results
        $receipts = $query->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // 4. Dropdown list of customers
        $customers = Customer::orderBy('customer_name')
            ->get(['id', 'customer_name', 'company_name']);

        return view('pages.accounts.reports.index', compact(
            'receipts',
            'customers',
            'kpis'
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
}
