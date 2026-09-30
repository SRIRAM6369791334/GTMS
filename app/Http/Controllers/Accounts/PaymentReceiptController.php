<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PaymentReceiptController extends Controller implements HasMiddleware
{
    /**
     * Define route middleware for permission gating.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:account.view', only: ['index', 'show', 'print']),
        ];
    }

    /**
     * Display a filterable, paginated directory of official money receipt vouchers.
     */
    public function index(Request $request): View
    {
        $query = PaymentReceipt::with(['customer.district', 'creator', 'branch'])
            ->latest('id');

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Payment Mode Filter
        if ($request->filled('payment_mode')) {
            $query->where('payment_mode', $request->payment_mode);
        }

        // Statutory Application Type Filter
        if ($request->filled('application_type')) {
            $query->where('application_type', $request->application_type);
        }

        // Date Range Filtering (accepting start_date/date_from/from_date and end_date/date_to/to_date)
        $startDate = $request->get('start_date', $request->get('date_from', $request->get('from_date')));
        if (!empty($startDate)) {
            $query->whereDate('transaction_date', '>=', $startDate);
        }

        $endDate = $request->get('end_date', $request->get('date_to', $request->get('to_date')));
        if (!empty($endDate)) {
            $query->whereDate('transaction_date', '<=', $endDate);
        }

        // Search Query (Receipt No, Cheque/UTR, Bank, Customer details - accepting q or search)
        if ($request->filled('q') || $request->filled('search')) {
            $q = trim($request->get('q', $request->get('search')));
            $query->where(function ($sub) use ($q) {
                $sub->where('receipt_number', 'like', "%{$q}%")
                    ->orWhere('reference_number', 'like', "%{$q}%")
                    ->orWhere('bank_name', 'like', "%{$q}%")
                    ->orWhereHas('customer', function ($cq) use ($q) {
                        $cq->where('customer_name', 'like', "%{$q}%")
                           ->orWhere('company_name', 'like', "%{$q}%")
                           ->orWhere('mobile_num', 'like', "%{$q}%");
                    });
            });
        }

        // Summary KPI Metrics
        $totalAmountCollected = (float) PaymentReceipt::sum('amount_paid');
        $monthToDateCollected = (float) PaymentReceipt::whereMonth('transaction_date', date('m'))
            ->whereYear('transaction_date', date('Y'))
            ->sum('amount_paid');
        $totalReceiptsCount   = PaymentReceipt::count();

        $customers = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name']);
        $receipts  = $query->paginate(15)->withQueryString();

        return view('pages.accounts.receipts.index', compact(
            'receipts',
            'customers',
            'totalAmountCollected',
            'monthToDateCollected',
            'totalReceiptsCount'
        ));
    }

    /**
     * Display a dedicated receipt voucher overview in GTMS theme.
     */
    public function show(PaymentReceipt $receipt): View
    {
        $receipt->load(['customer.district', 'creator', 'branch', 'quotation']);

        return view('pages.accounts.receipts.show', compact('receipt'));
    }

    /**
     * Render high-fidelity standalone printable receipt voucher view (A4 and A5 printable formats).
     */
    public function print(PaymentReceipt $receipt): View
    {
        $receipt->load(['customer.district', 'creator', 'branch', 'quotation']);

        return view('pages.accounts.receipts.print', compact('receipt'));
    }
}
