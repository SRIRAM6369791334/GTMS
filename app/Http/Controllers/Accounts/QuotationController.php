<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuotationController extends Controller implements HasMiddleware
{
    /**
     * Define route middleware for permission gating.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:account.view', only: ['index', 'show', 'print', 'getCustomerConcessions']),
            new Middleware('permission:account.create', only: ['create', 'store']),
            new Middleware('permission:account.edit', only: ['edit', 'update']),
            new Middleware('permission:account.delete', only: ['destroy']),
        ];
    }

    /**
     * Default pre-configured statutory mining services catalog.
     */
    protected array $defaultServices = [
        [
            'service_name' => 'DGPS Demarcation & Boundary Survey',
            'sac_code' => '998334',
            'unit' => 'Ha',
            'unit_rate' => 35000.00,
            'description' => 'Precision DGPS boundary demarcation, pillar fixing, geo-referencing and cadastral overlay map preparation for statutory lease boundary submission.',
        ],
        [
            'service_name' => 'Drone Photogrammetry & Orthomosaic Topo Survey',
            'sac_code' => '998335',
            'unit' => 'Ha',
            'unit_rate' => 50000.00,
            'description' => 'High-resolution UAV drone survey, orthomosaic generation, DEM/DTM extraction, 3D volumetric computation, and topographical contour mapping.',
        ],
        [
            'service_name' => 'Mining Plan Preparation & Processing',
            'sac_code' => '998341',
            'unit' => 'Job',
            'unit_rate' => 80000.00,
            'description' => 'Preparation of Comprehensive Mining Plan / Progressive Mine Closure Plan by Recognized Qualified Person (RQP) and representation before Dept of Geology & Mining.',
        ],
        [
            'service_name' => 'Form-1 & Form-2 Environmental Clearance (SEIAA)',
            'sac_code' => '998342',
            'unit' => 'Job',
            'unit_rate' => 200000.00,
            'description' => 'Preparation of Form-1, Form-2, Pre-Feasibility Report (PFR), Baseline Environmental Impact Assessment, and SEIAA/SEAC presentation documentation.',
        ],
        [
            'service_name' => 'TNPCB Consent to Establish (CTE) / Consent to Operate (CTO)',
            'sac_code' => '998343',
            'unit' => 'Job',
            'unit_rate' => 60000.00,
            'description' => 'Statutory filing and technical processing for Consent to Establish (CTE) and Consent to Operate (CTO) under Air & Water Acts before Tamil Nadu Pollution Control Board.',
        ],
        [
            'service_name' => 'Half-Yearly EC Compliance Monitoring & Filing',
            'sac_code' => '998344',
            'unit' => 'Year',
            'unit_rate' => 65000.00,
            'description' => 'Environmental monitoring, ambient air & water quality testing, green belt progress tracking, and mandatory half-yearly compliance filing with MoEF&CC / SEIAA.',
        ],
    ];

    /**
     * Display a paginated, filterable listing of quotations.
     */
    public function index(Request $request): View
    {
        $query = Quotation::with(['customer', 'district', 'items'])
            ->latest('id');

        // Customer Filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date Range Filters (accepting date_from/start_date and date_to/end_date)
        $dateFrom = $request->get('date_from', $request->get('start_date'));
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        $dateTo = $request->get('date_to', $request->get('end_date'));
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Text Search (accepting search or q)
        if ($request->filled('search') || $request->filled('q')) {
            $search = trim($request->get('search', $request->get('q')));
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('quarry_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Calculate KPI Statistics across quotations
        $stats = [
            'total_count'     => Quotation::count(),
            'total_amount'    => (float) Quotation::sum('total_amount'),
            'accepted_count'  => Quotation::whereIn('status', ['accepted', 'converted'])->count(),
            'accepted_amount' => (float) Quotation::whereIn('status', ['accepted', 'converted'])->sum('total_amount'),
            'pending_count'   => Quotation::whereIn('status', ['draft', 'sent'])->count(),
            'pending_amount'  => (float) Quotation::whereIn('status', ['draft', 'sent'])->sum('total_amount'),
        ];

        // Status counts for quick segmented filter tabs
        $statusCounts = Quotation::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $quotations = $query->paginate(15)->withQueryString();
        $customers  = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name']);

        return view('pages.accounts.quotations.index', compact('quotations', 'customers', 'stats', 'statusCounts'));
    }

    /**
     * Show the form for creating a new quotation.
     */
    public function create(): View
    {
        $customers = Customer::orderBy('customer_name')
            ->get(['id', 'customer_name', 'company_name', 'mobile_num', 'email', 'gstin', 'address', 'district_id']);

        $districts = District::where('status', 1)
            ->orderBy('name')
            ->get();

        if ($districts->isEmpty()) {
            $districts = District::orderBy('name')->get();
        }

        $services = $this->defaultServices;

        return view('pages.accounts.quotations.create', compact('customers', 'districts', 'services'));
    }

    /**
     * Store a newly created quotation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'lease_application_id' => 'nullable|exists:lease_applications,id',
            'customer_name'        => 'nullable|string|max:255',
            'company_name'         => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'email'                => 'nullable|email|max:255',
            'gst_number'           => 'nullable|string|max:50',
            'address'              => 'nullable|string',
            'quarry_name'          => 'nullable|string|max:255',
            'district_id'          => 'nullable|exists:districts,id',
            'taluk'                => 'nullable|string|max:255',
            'village'              => 'nullable|string|max:255',
            'survey_numbers'       => 'nullable|string|max:255',
            'area_extent_ha'       => 'nullable|numeric|min:0',
            'mineral_name'         => 'nullable|string|max:255',
            'validity_days'        => 'nullable|integer|min:1|max:365',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'payment_terms'        => 'nullable|string',
            'exclusions'           => 'nullable|string',
            'notes'                => 'nullable|string',
            'status'               => 'nullable|in:draft,sent,accepted,rejected,converted',
            'items'                => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.sac_code'     => 'nullable|string|max:50',
            'items.*.description'  => 'nullable|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.unit_rate'    => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $quotation = DB::transaction(function () use ($request, $customer, $validated) {
            $quotationNumber = $this->generateQuotationNumber();

            // Calculate item lines and subtotal
            $itemsData = $request->input('items', []);
            $calculatedSubtotal = 0.00;
            $preparedItems = [];

            foreach ($itemsData as $item) {
                $qty = (float) ($item['quantity'] ?? 1);
                $rate = (float) ($item['unit_rate'] ?? 0);
                $lineSubtotal = round($qty * $rate, 2);
                $calculatedSubtotal += $lineSubtotal;

                $preparedItems[] = [
                    'service_name' => trim($item['service_name'] ?? 'Statutory Mining Service'),
                    'sac_code'     => !empty($item['sac_code']) ? trim($item['sac_code']) : '998341',
                    'description'  => trim($item['description'] ?? ''),
                    'quantity'     => $qty,
                    'unit'         => trim($item['unit'] ?? 'Nos'),
                    'unit_rate'    => $rate,
                    'subtotal'     => $lineSubtotal,
                ];
            }

            $taxRate   = (float) ($request->input('tax_rate', 18.00));
            $taxAmount = round($calculatedSubtotal * ($taxRate / 100), 2);
            $totalAmount = $calculatedSubtotal + $taxAmount;

            // Resolve snapshot fields from Customer profile if blank
            $customerName = trim($request->input('customer_name') ?: $customer->customer_name);
            $companyName  = trim($request->input('company_name') ?: ($customer->company_name ?: $customerName));
            $phone        = trim($request->input('phone') ?: ($customer->mobile_num ?: ''));
            $email        = trim($request->input('email') ?: ($customer->email ?: ''));
            $gstNumber    = trim($request->input('gst_number') ?: ($customer->gstin ?: ''));
            $address      = trim($request->input('address') ?: ($customer->address ?: ''));
            $districtId   = $request->filled('district_id') ? $request->input('district_id') : $customer->district_id;

            // Resolve quarry snapshot if linked to a LeaseApplication
            $quarryName    = trim($request->input('quarry_name') ?? '');
            $taluk         = trim($request->input('taluk') ?? '');
            $village       = trim($request->input('village') ?? '');
            $surveyNumbers = trim($request->input('survey_numbers') ?? '');
            $areaExtentHa  = $request->filled('area_extent_ha') ? (float) $request->input('area_extent_ha') : null;
            $mineralName   = trim($request->input('mineral_name') ?? '');

            if ($request->filled('lease_application_id')) {
                $leaseApp = LeaseApplication::with(['surveyNumbers', 'minerals', 'district'])->find($request->lease_application_id);
                if ($leaseApp) {
                    if (empty($quarryName)) {
                        $quarryName = $leaseApp->quarry_name ?? ($leaseApp->village ? $leaseApp->village . ' Quarry' : 'Quarry Concession #' . $leaseApp->id);
                    }
                    if (empty($taluk)) {
                        $taluk = $leaseApp->taluk ?? '';
                    }
                    if (empty($village)) {
                        $village = $leaseApp->village ?? '';
                    }
                    if (empty($districtId)) {
                        $districtId = $leaseApp->district_id;
                    }
                    if (empty($surveyNumbers)) {
                        $surveyNumbers = $leaseApp->surveyNumbers->pluck('survey_no')->filter()->implode(', ');
                    }
                    if (is_null($areaExtentHa)) {
                        $areaExtentHa = (float) $leaseApp->area_extent_ha;
                    }
                    if (empty($mineralName)) {
                        $mineralName = $leaseApp->minerals->pluck('name')->implode(', ') ?: ($leaseApp->other_mineral_name ?? '');
                    }
                }
            }

            $user = Auth::user();

            $quotation = Quotation::create([
                'quotation_number'     => $quotationNumber,
                'customer_id'          => $customer->id,
                'lease_application_id' => $request->input('lease_application_id'),
                'customer_name'        => $customerName,
                'company_name'         => $companyName,
                'phone'                => $phone,
                'email'                => $email,
                'gst_number'           => $gstNumber,
                'address'              => $address,
                'quarry_name'          => $quarryName,
                'district_id'          => $districtId,
                'taluk'                => $taluk,
                'village'              => $village,
                'survey_numbers'       => $surveyNumbers,
                'area_extent_ha'       => $areaExtentHa,
                'mineral_name'         => $mineralName,
                'subtotal'             => $calculatedSubtotal,
                'tax_rate'             => $taxRate,
                'tax_amount'           => $taxAmount,
                'total_amount'         => $totalAmount,
                'validity_days'        => (int) ($request->input('validity_days', 30)),
                'payment_terms'        => $request->input('payment_terms') ?? "1. 50% Mobilization advance along with confirmed work order.\n2. 30% upon preparation & submission of draft statutory mining / environmental documentation.\n3. 20% upon final statutory clearance, presentation approval & dispatch of statutory order copies.",
                'exclusions'           => $request->input('exclusions') ?? "1. Government statutory scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans.\n2. In-person client representation before statutory committees if required.",
                'notes'                => $request->input('notes'),
                'status'               => $request->input('status', 'draft'),
                'branch_id'            => $user?->branch_id,
                'created_by'           => $user?->id,
            ]);

            foreach ($preparedItems as $item) {
                $quotation->items()->create($item);
            }

            return $quotation;
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Quotation generated successfully.',
                'quotation_id' => $quotation->id,
                'redirect_url' => route('accounts.quotations.show', $quotation->id),
            ]);
        }

        return redirect()
            ->route('accounts.quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->quotation_number} generated successfully.");
    }

    /**
     * Display the specified quotation administrative card view.
     */
    public function show(Quotation $quotation): View
    {
        $quotation->load(['customer', 'district', 'leaseApplication', 'items', 'creator', 'branch', 'receipts']);

        return view('pages.accounts.quotations.show', compact('quotation'));
    }

    /**
     * Show the form for editing an existing quotation.
     */
    public function edit(Quotation $quotation): View
    {
        $quotation->load(['items', 'customer', 'district']);

        $customers = Customer::orderBy('customer_name')
            ->get(['id', 'customer_name', 'company_name', 'mobile_num', 'email', 'gstin', 'address', 'district_id']);

        $districts = District::where('status', 1)
            ->orderBy('name')
            ->get();

        if ($districts->isEmpty()) {
            $districts = District::orderBy('name')->get();
        }

        $services = $this->defaultServices;

        return view('pages.accounts.quotations.edit', compact('quotation', 'customers', 'districts', 'services'));
    }

    /**
     * Update the specified quotation in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        $validated = $request->validate([
            'customer_id'          => 'required|exists:customers,id',
            'lease_application_id' => 'nullable|exists:lease_applications,id',
            'customer_name'        => 'nullable|string|max:255',
            'company_name'         => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'email'                => 'nullable|email|max:255',
            'gst_number'           => 'nullable|string|max:50',
            'address'              => 'nullable|string',
            'quarry_name'          => 'nullable|string|max:255',
            'district_id'          => 'nullable|exists:districts,id',
            'taluk'                => 'nullable|string|max:255',
            'village'              => 'nullable|string|max:255',
            'survey_numbers'       => 'nullable|string|max:255',
            'area_extent_ha'       => 'nullable|numeric|min:0',
            'mineral_name'         => 'nullable|string|max:255',
            'validity_days'        => 'nullable|integer|min:1|max:365',
            'tax_rate'             => 'nullable|numeric|min:0|max:100',
            'payment_terms'        => 'nullable|string',
            'exclusions'           => 'nullable|string',
            'notes'                => 'nullable|string',
            'status'               => 'nullable|in:draft,sent,accepted,rejected,converted',
            'items'                => 'required|array|min:1',
            'items.*.service_name' => 'required|string|max:255',
            'items.*.sac_code'     => 'nullable|string|max:50',
            'items.*.description'  => 'nullable|string',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.unit'         => 'nullable|string|max:50',
            'items.*.unit_rate'    => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        DB::transaction(function () use ($request, $quotation, $customer) {
            $itemsData = $request->input('items', []);
            $calculatedSubtotal = 0.00;
            $preparedItems = [];

            foreach ($itemsData as $item) {
                $qty = (float) ($item['quantity'] ?? 1);
                $rate = (float) ($item['unit_rate'] ?? 0);
                $lineSubtotal = round($qty * $rate, 2);
                $calculatedSubtotal += $lineSubtotal;

                $preparedItems[] = [
                    'service_name' => trim($item['service_name'] ?? 'Statutory Mining Service'),
                    'sac_code'     => !empty($item['sac_code']) ? trim($item['sac_code']) : '998341',
                    'description'  => trim($item['description'] ?? ''),
                    'quantity'     => $qty,
                    'unit'         => trim($item['unit'] ?? 'Nos'),
                    'unit_rate'    => $rate,
                    'subtotal'     => $lineSubtotal,
                ];
            }

            $taxRate     = (float) ($request->input('tax_rate', 18.00));
            $taxAmount   = round($calculatedSubtotal * ($taxRate / 100), 2);
            $totalAmount = $calculatedSubtotal + $taxAmount;

            $quotation->update([
                'customer_id'          => $customer->id,
                'lease_application_id' => $request->input('lease_application_id'),
                'customer_name'        => trim($request->input('customer_name') ?: $customer->customer_name),
                'company_name'         => trim($request->input('company_name') ?: ($customer->company_name ?: $customer->customer_name)),
                'phone'                => trim($request->input('phone') ?: ($customer->mobile_num ?: '')),
                'email'                => trim($request->input('email') ?: ($customer->email ?: '')),
                'gst_number'           => trim($request->input('gst_number') ?: ($customer->gstin ?: '')),
                'address'              => trim($request->input('address') ?: ($customer->address ?: '')),
                'quarry_name'          => trim($request->input('quarry_name') ?? ''),
                'district_id'          => $request->filled('district_id') ? $request->input('district_id') : $customer->district_id,
                'taluk'                => trim($request->input('taluk') ?? ''),
                'village'              => trim($request->input('village') ?? ''),
                'survey_numbers'       => trim($request->input('survey_numbers') ?? ''),
                'area_extent_ha'       => $request->filled('area_extent_ha') ? (float) $request->input('area_extent_ha') : null,
                'mineral_name'         => trim($request->input('mineral_name') ?? ''),
                'subtotal'             => $calculatedSubtotal,
                'tax_rate'             => $taxRate,
                'tax_amount'           => $taxAmount,
                'total_amount'         => $totalAmount,
                'validity_days'        => (int) ($request->input('validity_days', 30)),
                'payment_terms'        => $request->input('payment_terms'),
                'exclusions'           => $request->input('exclusions'),
                'notes'                => $request->input('notes'),
                'status'               => $request->input('status', $quotation->status),
            ]);

            // Re-sync line items
            $quotation->items()->delete();
            foreach ($preparedItems as $item) {
                $quotation->items()->create($item);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'message'      => 'Quotation updated successfully.',
                'quotation_id' => $quotation->id,
                'redirect_url' => route('accounts.quotations.show', $quotation->id),
            ]);
        }

        return redirect()
            ->route('accounts.quotations.show', $quotation->id)
            ->with('success', "Quotation {$quotation->quotation_number} updated successfully.");
    }

    /**
     * Remove the specified quotation from storage.
     */
    public function destroy(Request $request, Quotation $quotation)
    {
        DB::transaction(function () use ($quotation) {
            $quotation->items()->delete();
            $quotation->delete();
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Quotation deleted successfully.',
            ]);
        }

        return redirect()
            ->route('accounts.quotations.index')
            ->with('success', 'Quotation deleted successfully.');
    }

    /**
     * Render the high-fidelity standalone A4 printable quotation layout.
     */
    public function print(Quotation $quotation): View
    {
        $quotation->load(['customer', 'district', 'leaseApplication', 'items', 'creator', 'branch']);

        return view('pages.accounts.quotations.print', compact('quotation'));
    }

    /**
     * AJAX endpoint: return customer profile and concessions for dynamic quotation autofill.
     */
    public function getCustomerConcessions($customer): JsonResponse
    {
        if (!($customer instanceof Customer)) {
            $customer = Customer::where('id', $customer)
                ->orWhere('slug', $customer)
                ->firstOrFail();
        }

        $customer->load([
            'district',
            'leaseApplications.district',
            'leaseApplications.surveyNumbers',
            'leaseApplications.minerals',
        ]);

        $concessions = $customer->leaseApplications->map(function ($la) {
            $sfNos = $la->surveyNumbers->pluck('survey_no')->filter()->implode(', ');
            $mineralNames = $la->minerals->pluck('name')->filter()->implode(', ');
            if (empty($mineralNames) && !empty($la->other_mineral_name)) {
                $mineralNames = $la->other_mineral_name;
            }

            $districtName = $la->district?->name ?? ($la->district?->district_name ?? '');

            return [
                'id'             => $la->id,
                'application_no' => $la->application_no,
                'quarry_name'    => $la->quarry_name ?? ($la->village ? $la->village . ' Quarry' : 'Quarry Concession #' . $la->id),
                'district_id'    => $la->district_id,
                'district_name'  => $districtName,
                'taluk'          => $la->taluk ?? '',
                'village'        => $la->village ?? '',
                'survey_numbers' => $sfNos,
                'area_extent_ha' => (float) $la->area_extent_ha,
                'minerals'       => $mineralNames,
                'mineral_name'   => $mineralNames,
            ];
        });

        $customerDistrict = $customer->district?->name ?? ($customer->district?->district_name ?? '');

        return response()->json([
            'success'     => true,
            'customer'    => [
                'id'              => $customer->id,
                'customer_name'   => $customer->customer_name,
                'company_name'    => $customer->company_name,
                'phone'           => $customer->mobile_num,
                'secondary_phone' => $customer->secondary_mobile_num,
                'email'           => $customer->email,
                'gst_number'      => $customer->gstin,
                'pan'             => $customer->pan,
                'address'         => $customer->address,
                'district_id'     => $customer->district_id,
                'district_name'   => $customerDistrict,
            ],
            'concessions' => $concessions,
        ]);
    }

    /**
     * Concurrency-safe sequential quotation number generator.
     * Pattern: GTMS/QTN/{YYYY}/{0001}
     */
    protected function generateQuotationNumber(): string
    {
        $year   = date('Y');
        $prefix = "GTMS/QTN/{$year}/";

        $quotations = Quotation::withTrashed()
            ->where('quotation_number', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->get(['quotation_number']);

        $maxSeq = 0;
        foreach ($quotations as $q) {
            if (preg_match('/\/(\d+)$/', $q->quotation_number, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
        }

        $nextSeq   = $maxSeq + 1;
        $candidate = sprintf('%s%04d', $prefix, $nextSeq);

        while (Quotation::withTrashed()->where('quotation_number', $candidate)->exists()) {
            $nextSeq++;
            $candidate = sprintf('%s%04d', $prefix, $nextSeq);
        }

        return $candidate;
    }
}
