<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\PaymentReceipt;
use App\Models\Quotation;
use Illuminate\Http\JsonResponse;
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
            new Middleware('permission:account.view', only: ['index', 'show', 'print', 'getApplicationReceipts']),
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

        // Specific Application ID Filter
        if ($request->filled('application_id')) {
            $query->where('application_id', $request->application_id);
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

        // Precompute installment mapping for paginated receipts to prevent N+1 queries
        $installmentMap = [];
        $uniqueApps = $receipts->filter(fn($r) => $r->application_type && $r->application_id)
            ->map(fn($r) => strtolower(trim($r->application_type)) . '_' . $r->application_id)
            ->unique();

        if ($uniqueApps->isNotEmpty()) {
            $siblingReceipts = PaymentReceipt::where(function ($sub) use ($receipts) {
                foreach ($receipts as $r) {
                    if ($r->application_type && $r->application_id) {
                        $sub->orWhere(function ($q) use ($r) {
                            $type = strtolower(trim($r->application_type));
                            $q->where(function ($tq) use ($type) {
                                $tq->where('application_type', $type);
                                if ($type === 'ec' || $type === 'ec_certificate') {
                                    $tq->orWhere('application_type', 'ec')->orWhere('application_type', 'ec_certificate');
                                }
                            })->where('application_id', $r->application_id);
                        });
                    }
                }
            })->orderBy('id', 'asc')->get(['id', 'application_type', 'application_id']);

            $grouped = $siblingReceipts->groupBy(fn($r) => strtolower(trim($r->application_type === 'ec' ? 'ec_certificate' : $r->application_type)) . '_' . $r->application_id);

            foreach ($grouped as $appKey => $items) {
                $total = $items->count();
                foreach ($items as $idx => $item) {
                    $installmentMap[$item->id] = [
                        'installment' => $idx + 1,
                        'total'       => $total,
                        'label'       => 'Installment #' . ($idx + 1) . ' of ' . $total,
                    ];
                }
            }
        }

        return view('pages.accounts.receipts.index', compact(
            'receipts',
            'customers',
            'totalAmountCollected',
            'monthToDateCollected',
            'totalReceiptsCount',
            'installmentMap'
        ));
    }

    /**
     * Display a dedicated receipt voucher overview in GTMS theme.
     */
    public function show(PaymentReceipt $receipt): View
    {
        $receipt->load(['customer.district', 'creator', 'branch', 'quotation']);

        // Load complete sibling installment chain for the same application
        $siblingReceipts = collect();
        if ($receipt->application_type && $receipt->application_id) {
            $normType = strtolower(trim($receipt->application_type));
            $siblingReceipts = PaymentReceipt::where('customer_id', $receipt->customer_id)
                ->where(function ($q) use ($normType) {
                    $q->where('application_type', $normType);
                    if ($normType === 'ec' || $normType === 'ec_certificate') {
                        $q->orWhere('application_type', 'ec')->orWhere('application_type', 'ec_certificate');
                    }
                })
                ->where('application_id', $receipt->application_id)
                ->with(['creator'])
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('pages.accounts.receipts.show', compact('receipt', 'siblingReceipts'));
    }

    /**
     * Render high-fidelity standalone printable receipt voucher view (A4 and A5 printable formats),
     * or Proforma Invoice (reference PI format) or Tax Invoice (Reference TI format).
     */
    public function print(PaymentReceipt $receipt): View
    {
        $receipt->load([
            'customer.district',
            'customer.mineral',
            'customer.miningApplications.district',
            'customer.miningApplications.mineral',
            'customer.leaseApplications',
            'customer.environmentProjects',
            'customer.ecCertificates',
            'customer.ecCompliances',
            'customer.pptApplications',
            'customer.dgpsSurveys',
            'customer.droneSurveys',
            'creator',
            'branch',
            'quotation.items'
        ]);

        $activeScope = request('scope', 'auto');
        $invoiceData = $this->buildReceiptInvoiceData($receipt, $activeScope);
        $activeType = request('type', request('format', 'tax'));
        if (!in_array($activeType, ['tax', 'proforma'])) {
            $activeType = 'tax';
        }

        return view('pages.accounts.receipts.print', compact('receipt', 'invoiceData', 'activeType', 'activeScope'));
    }

    /**
     * Assemble dynamic Proforma & Tax Invoice data for the given Payment Receipt.
     * Supports Single-Application Auto-Fit Mode and Consolidated Multi-Application Mode.
     */
    protected function buildReceiptInvoiceData(PaymentReceipt $receipt, string $scope = 'auto'): array
    {
        $customer = $receipt->customer;
        $customer?->loadMissing([
            'district',
            'mineral',
            'miningApplications.district',
            'miningApplications.mineral',
            'leaseApplications',
            'environmentProjects',
            'ecCertificates',
            'ecCompliances',
            'pptApplications',
            'dgpsSurveys',
            'droneSurveys',
        ]);

        $miningApp = $customer?->miningApplications?->first();
        $leaseApp = $customer?->leaseApplications?->first();
        $districtName = $customer?->district?->name
            ?: ($miningApp?->district?->name ?: 'Tamil Nadu');
        $mineralName = $customer?->mineral?->name
            ?: ($miningApp?->mineral?->name ?: 'Rough Stone and Gravel');

        // Location & Concession site details
        $app = $receipt->application;
        $surveyNo = $app?->survey_numbers_text ?? ($miningApp?->survey_numbers_text ?? '102/1A, 102/1B');
        $extentHa = $app?->area_extent_ha ?? ($miningApp?->area_extent_ha ?? '3.85.0');
        $village = $app?->village ?? ($miningApp?->village ?? 'Alathur');
        $taluk = $app?->taluk ?? ($miningApp?->taluk ?? 'Sankari');

        $locationDetails = "at S.F.Nos. {$surveyNo}, over an extent of {$extentHa} Hectares of {$village} Village, {$taluk} Taluk, {$districtName} District.";

        $type = strtolower(trim($receipt->application_type ?: 'general'));

        // Count customer's applications across all 8 modules
        $customerAppsCount = 0;
        if ($customer) {
            $customerAppsCount = $customer->miningApplications->count() +
                $customer->leaseApplications->count() +
                $customer->environmentProjects->count() +
                $customer->dgpsSurveys->count() +
                $customer->droneSurveys->count() +
                $customer->pptApplications->count() +
                $customer->ecCertificates->count() +
                $customer->ecCompliances->count();
        }

        $isConsolidated = ($scope === 'consolidated') || ($scope === 'auto' && $type === 'general' && $customerAppsCount > 1);

        $items = [];
        $particulars = 'Statutory Mining Technical Consultancy Services';

        if ($isConsolidated && $customerAppsCount > 0) {
            $particulars = 'Statutory Turnkey Mining Consultancy, Concession Processing, Mine Planning & Environmental Clearances';
            $sl = 1;

            // 1. Lease Applications
            if ($customer->leaseApplications->isNotEmpty()) {
                $targetLease = $customer->leaseApplications->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->leaseApplications->first();
                $val = (float)($targetLease->product_value ?? 0);
                if ($val <= 0) { $val = 35000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'Preparation of Mining Lease Application & Revenue Scrutiny',
                    'ref'          => 'App Ref: ' . ($targetLease->application_no ?: '#' . $targetLease->id),
                    'sac'          => '998341',
                    'bullets'      => [
                        'Scrutiny of revenue records (Patta, Chitta, Adangal, Combined FMB Sketch)',
                        'Combined village cadastral map & MMS online portal statutory filing',
                    ],
                    'amount'       => $val,
                ];
            }

            // 2. Mining Plan
            if ($customer->miningApplications->isNotEmpty()) {
                $targetMining = $customer->miningApplications->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->miningApplications->first();
                $val = (float)($targetMining->product_value ?? 0);
                if ($val <= 0) { $val = 120000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'Preparation of Mining Plan & Progressive Mine Closure Plan (PMCP)',
                    'ref'          => 'S.F.Nos: ' . ($targetMining->survey_numbers_text ?: 'Concession Site') . ' (' . ($targetMining->area_extent_ha ?: '3.85') . ' Ha)',
                    'sac'          => '998343',
                    'bullets'      => [
                        'Topographical field investigation & geological reserve calculation',
                        '5-Year production schedule & 6 hard copies submitted to Dept of Geology & Mining',
                    ],
                    'amount'       => $val,
                ];
            }

            // 3. DGPS Survey
            if ($customer->dgpsSurveys->isNotEmpty()) {
                $targetDgps = $customer->dgpsSurveys->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->dgpsSurveys->first();
                $val = (float)($targetDgps->product_value ?? 0);
                if ($val <= 0) { $val = 35000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'DGPS Boundary Land Survey & Pillar Coordinate Demarcation',
                    'ref'          => 'Survey Ref: ' . ($targetDgps->survey_no ?: '#' . $targetDgps->id),
                    'sac'          => '998341',
                    'bullets'      => [
                        'Differential GPS baseline static observation & precision coordinate fixing',
                        'Geo-referenced KML file preparation & certified cadastral boundary demarcation map',
                    ],
                    'amount'       => $val,
                ];
            }

            // 4. Drone Survey
            if ($customer->droneSurveys->isNotEmpty()) {
                $targetDrone = $customer->droneSurveys->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->droneSurveys->first();
                $val = (float)($targetDrone->product_value ?? 0);
                if ($val <= 0) { $val = 50000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'Drone Aerial Photogrammetry Survey & 3D Volumetric Computation',
                    'ref'          => 'Flight Ref: ' . ($targetDrone->survey_no ?: '#' . $targetDrone->id),
                    'sac'          => '998342',
                    'bullets'      => [
                        'DGCA-compliant UAV aerial photogrammetry survey of quarry concession area',
                        'High-resolution orthomosaic, Digital Elevation Model (DEM) & cut-fill volume report',
                    ],
                    'amount'       => $val,
                ];
            }

            // 5. Environmental Clearance (EC) Project
            if ($customer->environmentProjects->isNotEmpty() || $customer->ecCertificates->isNotEmpty()) {
                $targetEnv = $customer->environmentProjects->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->environmentProjects->first();
                $val = (float)($targetEnv?->product_value ?? 0);
                if ($val <= 0) { $val = 80000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'Environmental Clearance (EC) Project Formulation, Form-1 & EMP',
                    'ref'          => 'Project Code: ' . ($targetEnv?->project_code ?: 'EC-PARIVESH'),
                    'sac'          => '998349',
                    'bullets'      => [
                        'Form-1, Form-2, Pre-Feasibility Report (PFR) & Baseline Environmental Management Plan',
                        'Online portal submission on PARIVESH (MoEFCC / SEIAA-TN)',
                    ],
                    'amount'       => $val,
                ];
            }

            // 6. PPT Presentation
            if ($customer->pptApplications->isNotEmpty()) {
                $targetPpt = $customer->pptApplications->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->pptApplications->first();
                $val = (float)($targetPpt->product_value ?? 0);
                if ($val <= 0) { $val = 30000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'PPT Statutory Presentation Formulation & SEAC Appraisal Defense',
                    'ref'          => 'PPT Ref: #' . $targetPpt->id,
                    'sac'          => '998311',
                    'bullets'      => [
                        'SEAC appraisal slide deck formulation & expert committee queries defense',
                    ],
                    'amount'       => $val,
                ];
            }

            // 7. EC Compliance
            if ($customer->ecCompliances->isNotEmpty()) {
                $targetEcc = $customer->ecCompliances->firstWhere(fn($x) => (float)($x->product_value ?? 0) > 0) ?: $customer->ecCompliances->first();
                $val = (float)($targetEcc->product_value ?? 0);
                if ($val <= 0) { $val = 40000.00; }
                $items[] = [
                    'sl'           => $sl++,
                    'service_name' => 'Half-Yearly EC Compliance Monitoring & NABL Lab Environmental Testing',
                    'ref'          => 'Compliance Ref: ' . ($targetEcc->compliance_no ?: '#' . $targetEcc->id) . ' (' . $targetEcc->compliance_period . ' ' . $targetEcc->compliance_year . ')',
                    'sac'          => '998349',
                    'bullets'      => [
                        'NABL ambient air, water, noise environmental testing & Parivesh portal filing',
                    ],
                    'amount'       => $val,
                ];
            }
        } else {
            // SINGLE APPLICATION AUTO-FIT MODE
            $amount = (float)($app?->product_value ?? ($receipt->amount_paid + $receipt->balance_due));
            if ($amount <= 0) {
                $amount = (float)$receipt->amount_paid ?: 50000.00;
            }

            if ($type === 'lease') {
                $particulars = 'Mining Lease Application, Revenue Scrutiny & Statutory MMS Filing';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Preparation of Mining Lease Application & Revenue Documentation',
                    'ref'          => 'Application No: ' . ($app?->application_no ?: '#' . $receipt->application_id),
                    'sac'          => '998341',
                    'bullets'      => [
                        'Scrutiny of revenue records (Patta, Chitta, Adangal, Combined FMB Sketch)',
                        'Revenue inspection assistance, affidavit verification & MMS online portal statutory filing',
                        'Follow-up with Department of Geology and Mining for field inspection scrutiny',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'mining') {
                $particulars = 'Preparation of Mining Plan and Progressive Mine Closure Plan (PMCP)';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Preparation of Mining Plan & Progressive Mine Closure Plan (PMCP)',
                    'ref'          => 'Concession Site: ' . ($app?->survey_numbers_text ?: 'Quarry Lease') . ' (' . ($app?->area_extent_ha ?: '3.85') . ' Ha)',
                    'sac'          => '998343',
                    'bullets'      => [
                        'Topographical survey data collection at proposed mine lease area',
                        'Geological reserve estimation, 5-year production scheduling & PMCP under Rule 41 of TNMMCR, 1959',
                        'Six hard copies of Mining Plan and submission to Department of Geology & Mining for approval',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'dgps') {
                $particulars = 'DGPS Land Boundary Survey & Pillar Coordinate Demarcation Plan';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'DGPS Land Boundary Survey & Pillar Coordinate Demarcation',
                    'ref'          => 'Survey Ref: ' . ($app?->survey_no ?: '#' . $receipt->application_id),
                    'sac'          => '998341',
                    'bullets'      => [
                        'Differential GPS baseline static observation & high-precision pillar coordinate fixing',
                        'Boundary demarcation and contour mapping on concession perimeter',
                        'Geo-referenced KML file overlaid with Survey of India Topo-sheet and certified survey sketch',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'drone') {
                $particulars = 'Drone Aerial Photogrammetry Survey & 3D Topographical Volumetric Modeling';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Drone Aerial Photogrammetry Survey & 3D Topographical Volumetric Modeling',
                    'ref'          => 'Flight Ref: ' . ($app?->survey_no ?: '#' . $receipt->application_id),
                    'sac'          => '998342',
                    'bullets'      => [
                        'DGCA-compliant UAV aerial photogrammetry survey of quarry concession area',
                        'High-resolution orthomosaic map & Digital Elevation Model (DEM / DTM) generation',
                        'Precise cut-and-fill volumetric excavation computation report & aerial inspection video',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'environment' || $type === 'ec' || $type === 'ec_certificate') {
                $particulars = 'Environmental Clearance (EC) Project Formulation, Form-1, Form-2 & Baseline EMP';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Environmental Clearance (EC) Project Formulation, Form-1, Form-2 & EMP',
                    'ref'          => 'Ref: ' . ($app?->project_code ?? ($app?->ec_ref_no ?: '#' . $receipt->application_id)),
                    'sac'          => '998349',
                    'bullets'      => [
                        'Preparation of statutory Form-1, Form-2, Pre-Feasibility Report (PFR) & Baseline EMP',
                        'Online portal submission on PARIVESH (MoEFCC / SEIAA-TN)',
                        'Thirteen report copies of POD submission to State Appraisal Committee & SEIAA liaison',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'ppt') {
                $particulars = 'SEAC / SEIAA Statutory Technical Presentation & Defense Dossier';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'PPT Statutory Presentation Formulation & SEAC Appraisal Defense',
                    'ref'          => 'PPT Ref: #' . ($app?->id ?: $receipt->application_id),
                    'sac'          => '998311',
                    'bullets'      => [
                        'Comprehensive appraisal slide deck formulation & SEAC queries liaison',
                        'Technical defense presentation in front of State Appraisal Committee',
                    ],
                    'amount'       => $amount,
                ];
            } elseif ($type === 'ec_compliance') {
                $particulars = 'Half-Yearly Environmental Compliance Monitoring & Environmental Testing';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Half-Yearly Environmental Compliance Monitoring & Testing',
                    'ref'          => 'Compliance Ref: ' . ($app?->compliance_no ?: '#' . $receipt->application_id) . ' (' . ($app?->compliance_period ?: '') . ' ' . ($app?->compliance_year ?: '') . ')',
                    'sac'          => '998349',
                    'bullets'      => [
                        'Ambient air, water, and noise monitoring by NABL accredited testing laboratory',
                        'Preparation of half-yearly compliance report & Parivesh portal filing',
                    ],
                    'amount'       => $amount,
                ];
            } else {
                $particulars = 'Statutory Mining Technical Consultation Services';
                $items[] = [
                    'sl'           => 1,
                    'service_name' => 'Statutory Mining Consultancy, Mineral Concession & Technical Advisory',
                    'ref'          => 'Payment Ref: ' . $receipt->receipt_number,
                    'sac'          => '9983',
                    'bullets'      => [
                        'Statutory mining plan processing, revenue scrutiny & field survey liaison',
                        'Statutory documentation and regulatory compliance retainer',
                    ],
                    'amount'       => $amount,
                ];
            }
        }

        // Totals calculation
        // Item amounts are base prices (exclusive of GST). GST @18% (9% CGST + 9% SGST) is added on top.
        $grossTotal = collect($items)->sum('amount');
        if ($receipt->quotation && !$isConsolidated && (float)$receipt->quotation->subtotal > 0) {
            $subtotal = (float)$receipt->quotation->subtotal;
            $cgst = (float)$receipt->quotation->tax_amount / 2;
            $sgst = (float)$receipt->quotation->tax_amount / 2;
            $grandTotal = (float)$receipt->quotation->total_amount;
        } else {
            $subtotal = round($grossTotal, 2);
            $cgst = round($subtotal * 0.09, 2);
            $sgst = round($subtotal * 0.09, 2);
            $grandTotal = $subtotal + $cgst + $sgst;
        }

        $amountInWords = Quotation::convertToIndianCurrencyWords($grandTotal);

        return [
            'customer'          => $customer,
            'districtName'      => $districtName,
            'mineralName'       => $mineralName,
            'locationDetails'   => $locationDetails,
            'particulars'       => $particulars,
            'isConsolidated'    => $isConsolidated,
            'customerAppsCount' => $customerAppsCount,
            'items'             => $items,
            'piItems'           => $items,
            'tiBullets'         => collect($items)->flatMap(fn($i) => $i['bullets'])->values()->all(),
            'subtotal'          => $subtotal,
            'cgst'              => $cgst,
            'sgst'              => $sgst,
            'grandTotal'        => $grandTotal,
            'amountInWords'     => $amountInWords,
            'piInvoiceNo'       => 'GTMS/' . (date('Y') . '-' . substr(date('Y') + 1, 2)) . '/PI' . str_pad($receipt->id, 3, '0', STR_PAD_LEFT),
            'tiInvoiceNo'       => 'GTMS/' . date('M') . '/' . date('Y') . '/' . str_pad($receipt->id, 3, '0', STR_PAD_LEFT),
            'invoiceDate'       => $receipt->transaction_date ? $receipt->transaction_date->format('d/m/Y') : date('d/m/Y'),
            'workOrderVia'      => $receipt->reference_number ? 'Ref: ' . $receipt->reference_number : 'Telephonic Discussion',
            'contactPerson'     => $customer?->customer_name ?: 'Authorized Representative',
            'contactNo'         => $customer?->mobile_num ?: '0000000000',
            'sealImageUrl'      => \App\Http\Controllers\Accounts\InvoiceSealController::getCustomSealUrl(),
        ];
    }

    /**
     * AJAX endpoint: Retrieve full payment & installment history for a specific application.
     */
    public function getApplicationReceipts(string $type, $id): JsonResponse
    {
        $type = strtolower(trim($type));
        $classMap = [
            'lease'          => \App\Models\LeaseApplication::class,
            'mining'         => \App\Models\MiningApplication::class,
            'environment'    => \App\Models\EnvironmentProject::class,
            'ppt'            => \App\Models\PptApplication::class,
            'dgps'           => \App\Models\DgpsSurvey::class,
            'drone'          => \App\Models\DroneSurvey::class,
            'ec'             => \App\Models\EcCertificate::class,
            'ec_certificate' => \App\Models\EcCertificate::class,
            'ec_compliance'  => \App\Models\EcCompliance::class,
        ];

        $appModel = null;
        if (isset($classMap[$type])) {
            $appModel = $classMap[$type]::find($id);
        }

        $receipts = PaymentReceipt::where(function ($q) use ($type) {
                $q->where('application_type', $type);
                if ($type === 'ec' || $type === 'ec_certificate') {
                    $q->orWhere('application_type', 'ec')->orWhere('application_type', 'ec_certificate');
                }
            })
            ->where('application_id', $id)
            ->with(['creator'])
            ->orderBy('id', 'asc')
            ->get();

        $productValue = (float) ($appModel->product_value ?? ($receipts->last()?->balance_due ? $receipts->sum('amount_paid') + $receipts->last()->balance_due : $receipts->sum('amount_paid')));
        $totalPaid = (float) ($appModel->paid_amount ?? $receipts->sum('amount_paid'));
        $pendingAmount = (float) ($appModel->pending_amount ?? ($receipts->last()?->balance_due ?? 0));

        $totalCount = $receipts->count();
        $installments = $receipts->map(function ($r, $index) use ($totalCount) {
            return [
                'id'               => $r->id,
                'installment_no'   => $index + 1,
                'installment_label'=> 'Installment #' . ($index + 1) . ' of ' . $totalCount,
                'receipt_number'   => $r->receipt_number,
                'amount_paid'      => (float) $r->amount_paid,
                'previous_paid'    => (float) ($r->previous_paid ?? 0),
                'balance_due'      => (float) ($r->balance_due ?? 0),
                'payment_mode'     => $r->payment_mode,
                'bank_name'        => $r->bank_name,
                'reference_number' => $r->reference_number,
                'transaction_date' => $r->transaction_date ? $r->transaction_date->format('Y-m-d') : ($r->created_at ? $r->created_at->format('Y-m-d') : ''),
                'formatted_date'   => $r->transaction_date ? $r->transaction_date->format('d M Y') : ($r->created_at ? $r->created_at->format('d M Y') : ''),
                'notes'            => $r->notes,
                'creator_name'     => $r->creator?->name ?? 'System',
                'view_url'         => route('accounts.receipts.show', $r->id),
                'print_url'        => route('accounts.receipts.print', $r->id),
            ];
        });

        return response()->json([
            'success'      => true,
            'application'  => [
                'type'             => $type,
                'id'               => (int) $id,
                'reference'        => $receipts->first()?->application_reference ?? (ucfirst($type) . ' #' . $id),
                'product_value'    => $productValue,
                'total_paid'       => $totalPaid,
                'pending_amount'   => $pendingAmount,
                'payment_status'   => ($pendingAmount <= 0 && $totalPaid > 0) ? 'paid' : ($totalPaid > 0 ? 'partial' : 'pending'),
                'total_installments'=> $totalCount,
                'next_installment' => $totalCount + 1,
            ],
            'installments' => $installments,
        ]);
    }
}
