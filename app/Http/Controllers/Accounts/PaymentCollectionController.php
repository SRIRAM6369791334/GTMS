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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentCollectionController extends Controller implements HasMiddleware
{
    /**
     * Define route middleware for permission gating.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:account.create', only: ['create', 'store']),
            new Middleware('permission:account.view', only: ['getCustomerPendingDues']),
        ];
    }

    /**
     * Display the payment collection desk interface.
     */
    public function create(Request $request): View
    {
        $customers = Customer::orderBy('customer_name')
            ->get(['id', 'customer_name', 'company_name', 'mobile_num', 'gstin', 'slug']);

        $selectedCustomerId = $request->query('customer_id');
        $selectedAppType    = $request->query('application_type');
        $selectedAppId      = $request->query('application_id');

        // Recent receipts for the quick-view panel (last 6)
        $recentReceipts = PaymentReceipt::with('customer')
            ->latest()
            ->take(6)
            ->get(['id', 'receipt_number', 'customer_id', 'amount_paid', 'payment_mode', 'transaction_date', 'created_at']);

        return view('pages.accounts.payments.create', compact(
            'customers',
            'selectedCustomerId',
            'selectedAppType',
            'selectedAppId',
            'recentReceipts'
        ));
    }

    /**
     * AJAX endpoint: Query all outstanding dues across all 7 statutory modules + EC Compliance.
     */
    public function getCustomerPendingDues($customer): JsonResponse
    {
        if (!($customer instanceof Customer)) {
            $customer = Customer::where('id', $customer)
                ->orWhere('slug', $customer)
                ->firstOrFail();
        }

        $dues = [];
        $totalProductValue = 0.0;
        $totalPaid = 0.0;
        $totalPending = 0.0;
        $pendingCount = 0;

        // 1. Lease Applications
        $leases = LeaseApplication::where('customer_id', $customer->id)
            ->with(['district', 'surveyNumbers'])
            ->get();

        foreach ($leases as $lease) {
            $val     = (float) ($lease->product_value ?? 0);
            $paid    = (float) ($lease->paid_amount ?? 0);
            $pending = ($lease->pending_amount > 0) ? (float) $lease->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $lease->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $sfNos = $lease->surveyNumbers->pluck('survey_no')->filter()->implode(', ');
            $loc = array_filter([$sfNos ? "S.F. {$sfNos}" : null, $lease->village, $lease->taluk, $lease->district?->name]);
            $concessionInfo = implode(', ', $loc) ?: ($lease->quarry_name ?: 'Lease Concession');

            $dues[] = [
                'application_type' => 'lease',
                'application_id'   => $lease->id,
                'reference_number' => $lease->application_no ?: ('Lease #' . $lease->id),
                'service_label'    => 'Lease Application / Quarry Concession',
                'concession_info'  => $concessionInfo,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 2. Mining Applications
        $minings = MiningApplication::where('customer_id', $customer->id)
            ->with(['district'])
            ->get();

        foreach ($minings as $mining) {
            $val     = (float) ($mining->product_value ?? 0);
            $paid    = (float) ($mining->paid_amount ?? 0);
            $pending = ($mining->pending_amount > 0) ? (float) $mining->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $mining->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $loc = array_filter([$mining->survey_numbers_text ? "S.F. {$mining->survey_numbers_text}" : null, $mining->village, $mining->taluk, $mining->district?->name]);
            $concessionInfo = implode(', ', $loc) ?: 'Mining Plan Area';

            $dues[] = [
                'application_type' => 'mining',
                'application_id'   => $mining->id,
                'reference_number' => $mining->application_no ?: ('Mining Plan #' . $mining->id),
                'service_label'    => 'Mining Plan Preparation & Processing',
                'concession_info'  => $concessionInfo,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 3. Environment Projects
        $envs = EnvironmentProject::where('customer_id', $customer->id)
            ->with(['district'])
            ->get();

        foreach ($envs as $env) {
            $val     = (float) ($env->product_value ?? 0);
            $paid    = (float) ($env->paid_amount ?? 0);
            $pending = ($env->pending_amount > 0) ? (float) $env->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $env->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $label = 'Environmental Clearance (' . ($env->category ?: 'EC') . ($env->sub_category ? ' - ' . $env->sub_category : '') . ')';
            $loc = array_filter([$env->location, $env->district?->name]);

            $dues[] = [
                'application_type' => 'environment',
                'application_id'   => $env->id,
                'reference_number' => $env->project_code ?: ('Env #' . $env->id),
                'service_label'    => $label,
                'concession_info'  => implode(', ', $loc) ?: ($env->project_name ?: 'EC Project'),
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 4. PPT Applications
        $ppts = PptApplication::where('customer_id', $customer->id)
            ->with(['district'])
            ->get();

        foreach ($ppts as $ppt) {
            $val     = (float) ($ppt->product_value ?? 0);
            $paid    = (float) ($ppt->paid_amount ?? 0);
            $pending = ($ppt->pending_amount > 0) ? (float) $ppt->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $ppt->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $label = 'SEAC / SEIAA Presentation (' . ($ppt->presentation_stage ?: 'Stage') . ')';
            $loc = array_filter([$ppt->taluk_village, $ppt->district?->name]);

            $dues[] = [
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'reference_number' => $ppt->application_no ?: ('PPT #' . $ppt->id),
                'service_label'    => $label,
                'concession_info'  => implode(', ', $loc) ?: ($ppt->project_name ?: 'Presentation Dossier'),
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 5. DGPS Surveys
        $dgpsSurveys = DgpsSurvey::where('customer_id', $customer->id)->get();

        foreach ($dgpsSurveys as $dgps) {
            $val     = (float) ($dgps->product_value ?? 0);
            $paid    = (float) ($dgps->paid_amount ?? 0);
            $pending = ($dgps->pending_amount > 0) ? (float) $dgps->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $dgps->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $dues[] = [
                'application_type' => 'dgps',
                'application_id'   => $dgps->id,
                'reference_number' => $dgps->survey_no ?: ('DGPS #' . $dgps->id),
                'service_label'    => 'DGPS Demarcation & Boundary Survey',
                'concession_info'  => $dgps->location ?: 'DGPS Survey Ground Area',
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 6. Drone Surveys
        $droneSurveys = DroneSurvey::where('customer_id', $customer->id)->get();

        foreach ($droneSurveys as $drone) {
            $val     = (float) ($drone->product_value ?? 0);
            $paid    = (float) ($drone->paid_amount ?? 0);
            $pending = ($drone->pending_amount > 0) ? (float) $drone->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $drone->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $dues[] = [
                'application_type' => 'drone',
                'application_id'   => $drone->id,
                'reference_number' => $drone->survey_no ?: ('Drone #' . $drone->id),
                'service_label'    => 'Drone Photogrammetry & Topo Survey',
                'concession_info'  => $drone->location ?: 'UAV Flight Area',
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 7. EC Certificates
        $ecCerts = EcCertificate::where('customer_id', $customer->id)->get();

        foreach ($ecCerts as $ec) {
            $val     = (float) ($ec->product_value ?? 0);
            $paid    = (float) ($ec->paid_amount ?? 0);
            $pending = ($ec->pending_amount > 0) ? (float) $ec->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $ec->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $ref = $ec->ec_ref_no ?: ($ec->parivesh_app_no ?: ('EC Cert #' . $ec->id));
            $info = $ec->parivesh_app_no ? "Parivesh App: {$ec->parivesh_app_no}" : 'Environmental Clearance Certificate';

            $dues[] = [
                'application_type' => 'ec_certificate',
                'application_id'   => $ec->id,
                'reference_number' => $ref,
                'service_label'    => 'Environmental Clearance Certificate',
                'concession_info'  => $info,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        // 8. EC Compliances
        $ecCompliances = EcCompliance::where('customer_id', $customer->id)
            ->with(['district'])
            ->get();

        foreach ($ecCompliances as $ecc) {
            $val     = (float) ($ecc->product_value ?? 0);
            $paid    = (float) ($ecc->paid_amount ?? 0);
            $pending = ($ecc->pending_amount > 0) ? (float) $ecc->pending_amount : max(0.0, round($val - $paid, 2));
            $status  = $ecc->payment_status ?: (($pending <= 0 && $paid > 0) ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));

            $period = trim(($ecc->compliance_period ?: '') . ' ' . ($ecc->compliance_year ?: ''));
            $loc = array_filter([$ecc->taluk_village, $ecc->district?->name]);

            $dues[] = [
                'application_type' => 'ec_compliance',
                'application_id'   => $ecc->id,
                'reference_number' => $ecc->compliance_no ?: ('EC Compliance #' . $ecc->id),
                'service_label'    => 'Half-Yearly EC Compliance (' . ($period ?: 'Monitoring') . ')',
                'concession_info'  => implode(', ', $loc) ?: ($ecc->project_name ?: 'Compliance Site'),
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $status,
            ];

            $totalProductValue += $val;
            $totalPaid += $paid;
            $totalPending += $pending;
            if ($pending > 0 || $status !== 'paid') {
                $pendingCount++;
            }
        }

        return response()->json([
            'success'  => true,
            'customer' => [
                'id'            => $customer->id,
                'customer_name' => $customer->customer_name,
                'company_name'  => $customer->company_name,
                'mobile_num'    => $customer->mobile_num,
                'email'         => $customer->email,
                'gstin'         => $customer->gstin,
                'pan'           => $customer->pan,
                'address'       => $customer->address,
                'district_name' => $customer->district?->name ?? '',
            ],
            'summary' => [
                'total_product_value' => round($totalProductValue, 2),
                'total_paid'          => round($totalPaid, 2),
                'total_pending'       => round($totalPending, 2),
                'dues_count'          => $pendingCount,
            ],
            'dues' => $dues,
        ]);
    }

    /**
     * Store payment collection and atomically synchronize application tables and application_payments.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'application_type'  => 'required|string|in:lease,mining,environment,ppt,dgps,drone,ec,ec_certificate,ec_compliance,general',
            'application_id'    => 'nullable|required_unless:application_type,general|integer',
            'amount_paid'       => 'required|numeric|min:0.01',
            'payment_mode'      => 'required|string|in:Cash,Cheque,NEFT/RTGS,UPI/GPay,Other',
            'bank_name'         => 'nullable|string|max:150',
            'reference_number'  => 'nullable|string|max:100',
            'transaction_date'  => 'required|date',
            'notes'             => 'nullable|string|max:1000',
            'quotation_id'      => 'nullable|exists:quotations,id',
        ]);

        $receipt = DB::transaction(function () use ($request) {
            $type = strtolower(trim($request->application_type));
            $amountPaid = round((float) $request->amount_paid, 2);
            $previousPaid = 0.00;
            $balanceDue = 0.00;

            $classMap = [
                'lease'          => LeaseApplication::class,
                'mining'         => MiningApplication::class,
                'environment'    => EnvironmentProject::class,
                'ppt'            => PptApplication::class,
                'dgps'           => DgpsSurvey::class,
                'drone'          => DroneSurvey::class,
                'ec'             => EcCertificate::class,
                'ec_certificate' => EcCertificate::class,
                'ec_compliance'  => EcCompliance::class,
            ];

            if ($type !== 'general' && isset($classMap[$type]) && $request->filled('application_id')) {
                $modelClass = $classMap[$type];
                $appModel = $modelClass::where('id', $request->application_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $previousPaid = (float) ($appModel->paid_amount ?? 0);
                $productValue = (float) ($appModel->product_value ?? 0);

                $newPaid = round($previousPaid + $amountPaid, 2);

                if ($productValue > 0) {
                    $newPending = max(0.00, round($productValue - $newPaid, 2));
                } else {
                    $productValue = $newPaid;
                    $newPending = 0.00;
                }

                $newStatus = ($newPending <= 0.00) ? 'paid' : ($newPaid > 0 ? 'partial' : 'pending');

                // Update application table
                $appModel->paid_amount    = $newPaid;
                $appModel->pending_amount = $newPending;
                $appModel->payment_status = $newStatus;
                $appModel->product_value  = $productValue;
                $appModel->save();

                $balanceDue = $newPending;

                // Synchronize polymorphic application_payments table
                $appPaymentType = ($type === 'ec_certificate') ? 'ec' : $type;
                ApplicationPayment::updateOrCreate(
                    [
                        'application_type' => $appPaymentType,
                        'application_id'   => $appModel->id,
                    ],
                    [
                        'payable_type'   => get_class($appModel),
                        'payable_id'     => $appModel->id,
                        'product_value'  => $productValue,
                        'paid_amount'    => $newPaid,
                        'pending_amount' => $newPending,
                        'payment_status' => $newStatus,
                        'notes'          => trim(($appModel->payment_notes ? $appModel->payment_notes . "\n" : '') .
                            "Payment of ₹" . number_format($amountPaid, 2) . " via {$request->payment_mode}" .
                            ($request->reference_number ? " [Ref: {$request->reference_number}]" : '') .
                            " on {$request->transaction_date}"),
                    ]
                );
            }

            // Generate sequential receipt voucher number
            $receiptNumber = PaymentReceipt::generateReceiptNumber();

            // Create immutable PaymentReceipt record
            $receipt = PaymentReceipt::create([
                'receipt_number'   => $receiptNumber,
                'customer_id'      => $request->customer_id,
                'quotation_id'     => $request->quotation_id ?: null,
                'application_type' => $type,
                'application_id'   => ($type !== 'general') ? $request->application_id : null,
                'amount_paid'      => $amountPaid,
                'balance_due'      => $balanceDue,
                'previous_paid'    => $previousPaid,
                'payment_mode'     => $request->payment_mode,
                'bank_name'        => $request->bank_name,
                'reference_number' => $request->reference_number,
                'transaction_date' => $request->transaction_date,
                'notes'            => $request->notes,
                'branch_id'        => Auth::user()?->branch_id,
                'created_by'       => Auth::id(),
            ]);

            return $receipt;
        });

        return redirect()->route('accounts.receipts.show', $receipt->id)
            ->with('success', "Payment of ₹" . number_format($receipt->amount_paid, 2) . " successfully collected under Official Receipt Voucher #{$receipt->receipt_number}.");
    }
}
