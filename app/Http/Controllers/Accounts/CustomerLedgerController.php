<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\DgpsSurvey;
use App\Models\DroneSurvey;
use App\Models\EcCertificate;
use App\Models\EcCompliance;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use App\Models\PaymentReceipt;
use App\Models\PptApplication;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class CustomerLedgerController extends Controller implements HasMiddleware
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
     * Display a filterable, paginated customer financial directory.
     * Shows each customer's Total Billed (Quotations), Total Received (Receipts), Net Outstanding Balance.
     */
    public function index(Request $request): View
    {
        $query = Customer::query()
            ->with('district')
            ->withSum('quotations', 'total_amount')
            ->withSum('paymentReceipts', 'amount_paid');

        // Search query: customer name, company name, mobile, email, mimas no
        if ($request->filled('q') || $request->filled('search')) {
            $search = trim($request->get('search', $request->get('q')));
            $query->where(function ($sub) use ($search) {
                $sub->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('mobile_num', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mimas_no', 'like', "%{$search}%")
                    ->orWhere('mimas_number', 'like', "%{$search}%");
            });
        }

        // District Filter
        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        // Global KPI Metrics across all customers
        $totalCustomersCount = Customer::count();
        $overallTotalBilled = (float) Quotation::sum('total_amount');
        $overallTotalReceived = (float) PaymentReceipt::sum('amount_paid');
        $overallTotalOutstanding = (float) ApplicationPayment::sum('pending_amount');
        if ($overallTotalOutstanding <= 0 && $overallTotalBilled > $overallTotalReceived) {
            $overallTotalOutstanding = round($overallTotalBilled - $overallTotalReceived, 2);
        }

        $customers = $query->orderBy('customer_name')->paginate(15)->withQueryString();

        return view('pages.accounts.ledger.index', compact(
            'customers',
            'totalCustomersCount',
            'overallTotalBilled',
            'overallTotalReceived',
            'overallTotalOutstanding'
        ));
    }

    /**
     * Single-pane customer financial dossier.
     * Resolves customer by ID or slug. Supports date filtering (from_date, to_date).
     * Chronologically merges Debits (Quotations) and Credits (Payment Receipts) to compute running balance.
     */
    public function show(Request $request, $customer): View
    {
        $customer = $this->resolveCustomer($customer);
        $customer->load(['district', 'mineral']);

        $fromDate = $request->query('from_date', $request->query('start_date'));
        $toDate = $request->query('to_date', $request->query('end_date'));

        // 1. Calculate opening balance if from_date is provided
        $openingBalance = 0.0;
        if ($fromDate) {
            $priorDebits = (float) Quotation::where('customer_id', $customer->id)
                ->whereDate('created_at', '<', $fromDate)
                ->sum('total_amount');

            $priorCredits = (float) PaymentReceipt::where('customer_id', $customer->id)
                ->whereDate('transaction_date', '<', $fromDate)
                ->sum('amount_paid');

            $openingBalance = round($priorDebits - $priorCredits, 2);
        }

        // 2. Fetch Debits (Quotations) in period
        $quotationsQuery = Quotation::where('customer_id', $customer->id);
        if ($fromDate) {
            $quotationsQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $quotationsQuery->whereDate('created_at', '<=', $toDate);
        }
        $quotations = $quotationsQuery->get();

        // 3. Fetch Credits (Payment Receipts) in period
        $receiptsQuery = PaymentReceipt::where('customer_id', $customer->id);
        if ($fromDate) {
            $receiptsQuery->whereDate('transaction_date', '>=', $fromDate);
        }
        if ($toDate) {
            $receiptsQuery->whereDate('transaction_date', '<=', $toDate);
        }
        $receipts = $receiptsQuery->get();

        // 4. Merge chronologically
        $items = collect();

        foreach ($quotations as $q) {
            $date = Carbon::parse($q->created_at);
            $desc = 'Quotation: ' . $q->quotation_number;
            if ($q->quarry_name) {
                $desc .= ' (' . $q->quarry_name . ')';
            } elseif ($q->mineral_name) {
                $desc .= ' (' . $q->mineral_name . ' Quarry)';
            } else {
                $desc .= ' (Statutory Processing)';
            }

            $items->push([
                'id'           => $q->id,
                'date'         => $date,
                'date_sort'    => $date->timestamp,
                'type'         => 'quotation',
                'type_label'   => 'Quotation (Debit)',
                'badge_class'  => 'bg-primary-subtle text-primary border border-primary-subtle',
                'ref_no'       => $q->quotation_number,
                'description'  => $desc,
                'module'       => 'Statutory Quotation',
                'debit'        => (float) $q->total_amount,
                'credit'       => 0.0,
                'print_url'    => route('accounts.quotations.print', $q->id),
                'view_url'     => route('accounts.quotations.show', $q->id),
                'status'       => ucfirst($q->status ?? 'Draft'),
            ]);
        }

        foreach ($receipts as $r) {
            $date = Carbon::parse($r->transaction_date ?: $r->created_at);
            $desc = 'Payment Receipt: ' . $r->receipt_number;
            if ($r->payment_mode) {
                $desc .= ' via ' . $r->payment_mode;
            }
            if ($r->reference_number) {
                $desc .= ' (Ref: ' . $r->reference_number . ')';
            }

            $items->push([
                'id'           => $r->id,
                'date'         => $date,
                'date_sort'    => $date->timestamp,
                'type'         => 'payment',
                'type_label'   => 'Payment (Credit)',
                'badge_class'  => 'bg-success-subtle text-success border border-success-subtle',
                'ref_no'       => $r->receipt_number,
                'description'  => $desc,
                'module'       => $r->application_type ? ucfirst(str_replace('_', ' ', $r->application_type)) : 'Statutory Payment',
                'debit'        => 0.0,
                'credit'       => (float) $r->amount_paid,
                'print_url'    => route('accounts.receipts.print', $r->id),
                'view_url'     => route('accounts.receipts.show', $r->id),
                'status'       => 'Settled',
            ]);
        }

        // Sort ascending by timestamp, debits before credits if identical timestamp
        $sorted = $items->sortBy([
            ['date_sort', 'asc'],
            ['type', 'desc'],
        ])->values();

        $running = $openingBalance;
        $transactions = $sorted->map(function ($item) use (&$running) {
            $running = round($running + $item['debit'] - $item['credit'], 2);
            $item['running_balance'] = $running;
            return (object) $item;
        });

        // Summary financial KPIs
        $periodBilled = (float) $quotations->sum('total_amount');
        $periodReceived = (float) $receipts->sum('amount_paid');
        $allTimeBilled = (float) Quotation::where('customer_id', $customer->id)->sum('total_amount');
        $allTimeReceived = (float) PaymentReceipt::where('customer_id', $customer->id)->sum('amount_paid');
        $netBalance = $fromDate ? $running : round($allTimeBilled - $allTimeReceived, 2);

        // Active statutory application dues breakdown
        $applicationDues = $this->getCustomerApplicationDues($customer);

        return view('pages.accounts.ledger.show', compact(
            'customer',
            'transactions',
            'openingBalance',
            'periodBilled',
            'periodReceived',
            'allTimeBilled',
            'allTimeReceived',
            'netBalance',
            'fromDate',
            'toDate',
            'applicationDues'
        ));
    }

    /**
     * Standalone printable Statement of Account with GTMS branding, statement period,
     * itemized transactions, running balance, net dues in words, and authorized signatory zone.
     */
    public function printStatement(Request $request, $customer): View
    {
        $customer = $this->resolveCustomer($customer);
        $customer->load(['district', 'mineral']);

        $fromDate = $request->query('from_date', $request->query('start_date'));
        $toDate = $request->query('to_date', $request->query('end_date'));

        // 1. Calculate opening balance if from_date is provided
        $openingBalance = 0.0;
        if ($fromDate) {
            $priorDebits = (float) Quotation::where('customer_id', $customer->id)
                ->whereDate('created_at', '<', $fromDate)
                ->sum('total_amount');

            $priorCredits = (float) PaymentReceipt::where('customer_id', $customer->id)
                ->whereDate('transaction_date', '<', $fromDate)
                ->sum('amount_paid');

            $openingBalance = round($priorDebits - $priorCredits, 2);
        }

        // 2. Fetch Debits (Quotations)
        $quotationsQuery = Quotation::where('customer_id', $customer->id);
        if ($fromDate) {
            $quotationsQuery->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $quotationsQuery->whereDate('created_at', '<=', $toDate);
        }
        $quotations = $quotationsQuery->get();

        // 3. Fetch Credits (Payment Receipts)
        $receiptsQuery = PaymentReceipt::where('customer_id', $customer->id);
        if ($fromDate) {
            $receiptsQuery->whereDate('transaction_date', '>=', $fromDate);
        }
        if ($toDate) {
            $receiptsQuery->whereDate('transaction_date', '<=', $toDate);
        }
        $receipts = $receiptsQuery->get();

        // 4. Merge chronologically
        $items = collect();

        foreach ($quotations as $q) {
            $date = Carbon::parse($q->created_at);
            $desc = 'Quotation: ' . $q->quotation_number;
            if ($q->quarry_name) {
                $desc .= ' (' . $q->quarry_name . ')';
            } elseif ($q->mineral_name) {
                $desc .= ' (' . $q->mineral_name . ' Quarry)';
            } else {
                $desc .= ' (Statutory Processing)';
            }

            $items->push([
                'id'          => $q->id,
                'date'        => $date,
                'date_sort'   => $date->timestamp,
                'type'        => 'quotation',
                'type_label'  => 'Quotation',
                'ref_no'      => $q->quotation_number,
                'description' => $desc,
                'debit'       => (float) $q->total_amount,
                'credit'      => 0.0,
            ]);
        }

        foreach ($receipts as $r) {
            $date = Carbon::parse($r->transaction_date ?: $r->created_at);
            $desc = 'Payment: ' . $r->receipt_number;
            if ($r->payment_mode) {
                $desc .= ' via ' . $r->payment_mode;
            }
            if ($r->reference_number) {
                $desc .= ' (Ref: ' . $r->reference_number . ')';
            }

            $items->push([
                'id'          => $r->id,
                'date'        => $date,
                'date_sort'   => $date->timestamp,
                'type'        => 'payment',
                'type_label'  => 'Payment',
                'ref_no'      => $r->receipt_number,
                'description' => $desc,
                'debit'       => 0.0,
                'credit'      => (float) $r->amount_paid,
            ]);
        }

        $sorted = $items->sortBy([
            ['date_sort', 'asc'],
            ['type', 'desc'],
        ])->values();

        $running = $openingBalance;
        $transactions = $sorted->map(function ($item) use (&$running) {
            $running = round($running + $item['debit'] - $item['credit'], 2);
            $item['running_balance'] = $running;
            return (object) $item;
        });

        // Totals
        $periodBilled = (float) $quotations->sum('total_amount');
        $periodReceived = (float) $receipts->sum('amount_paid');
        $allTimeBilled = (float) Quotation::where('customer_id', $customer->id)->sum('total_amount');
        $allTimeReceived = (float) PaymentReceipt::where('customer_id', $customer->id)->sum('amount_paid');
        $netBalance = $fromDate ? $running : round($allTimeBilled - $allTimeReceived, 2);

        $statementRef = 'GTMS/SOA/' . date('Y') . '/' . str_pad((string) $customer->id, 4, '0', STR_PAD_LEFT);
        $statementDate = now()->format('d-M-Y');
        $netBalanceInWords = Quotation::convertToIndianCurrencyWords(max(0.0, (float) $netBalance));

        return view('pages.accounts.ledger.print', compact(
            'customer',
            'transactions',
            'openingBalance',
            'periodBilled',
            'periodReceived',
            'allTimeBilled',
            'allTimeReceived',
            'netBalance',
            'netBalanceInWords',
            'fromDate',
            'toDate',
            'statementRef',
            'statementDate'
        ));
    }

    /**
     * Resolve Customer by Model instance, slug, or ID.
     */
    protected function resolveCustomer($customer): Customer
    {
        if ($customer instanceof Customer) {
            return $customer;
        }

        return Customer::where('slug', $customer)
            ->orWhere('id', $customer)
            ->firstOrFail();
    }

    /**
     * Gather outstanding statutory application dues for the customer.
     */
    protected function getCustomerApplicationDues(Customer $customer): array
    {
        $dues = [];

        // 1. Lease Applications
        $leases = LeaseApplication::where('customer_id', $customer->id)->with('district')->get();
        foreach ($leases as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'Lease Application',
                    'ref'         => $item->application_no ?? ('LA-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'lease',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 2. Mining Plans
        $mining = MiningApplication::where('customer_id', $customer->id)->get();
        foreach ($mining as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'Mining Plan',
                    'ref'         => $item->application_no ?? ('MP-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'mining',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 3. Environmental Clearance
        $env = EnvironmentProject::where('customer_id', $customer->id)->get();
        foreach ($env as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'Environmental Clearance',
                    'ref'         => $item->project_code ?? ('ECP-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'environment',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 4. PPT Applications
        $ppt = PptApplication::where('customer_id', $customer->id)->get();
        foreach ($ppt as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'PPT Department',
                    'ref'         => $item->application_no ?? ('PPT-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'ppt',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 5. DGPS Surveys
        $dgps = DgpsSurvey::where('customer_id', $customer->id)->get();
        foreach ($dgps as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'DGPS Survey',
                    'ref'         => $item->survey_no ?? ('DGPS-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'dgps',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 6. Drone Surveys
        $drone = DroneSurvey::where('customer_id', $customer->id)->get();
        foreach ($drone as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'Drone Survey',
                    'ref'         => $item->survey_no ?? ('DRONE-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'drone',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 7. EC Certificates
        $ecCert = EcCertificate::where('customer_id', $customer->id)->get();
        foreach ($ecCert as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'EC Certificate',
                    'ref'         => $item->ec_ref_no ?? ('ECC-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'ec_certificate',
                    'app_id'      => $item->id,
                ];
            }
        }

        // 8. EC Compliance
        $ecComp = EcCompliance::where('customer_id', $customer->id)->get();
        foreach ($ecComp as $item) {
            $val = (float) ($item->product_value ?? 0);
            $paid = (float) ($item->paid_amount ?? 0);
            $pending = ($item->pending_amount > 0) ? (float) $item->pending_amount : max(0.0, round($val - $paid, 2));
            if ($val > 0 || $pending > 0) {
                $dues[] = (object) [
                    'module'      => 'EC Compliance',
                    'ref'         => $item->compliance_no ?? ('HYC-' . $item->id),
                    'total'       => $val,
                    'paid'        => $paid,
                    'pending'     => $pending,
                    'status'      => $item->payment_status ?: ($pending <= 0 ? 'paid' : 'pending'),
                    'app_type'    => 'ec_compliance',
                    'app_id'      => $item->id,
                ];
            }
        }

        return $dues;
    }
}
