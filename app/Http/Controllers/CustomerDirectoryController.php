<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerDirectoryController extends Controller
{
    /**
     * Display a listing of customers with KPI stats and filter dropdowns.
     */
    public function index()
    {
        $customers = Customer::with(['district', 'mineral', 'creator'])
            ->latest()
            ->get();

        $districts = District::where('status', 1)->orderBy('name')->get();
        $minerals = Mineral::where('status', 1)->orderBy('name')->get();

        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 1)->count();
        $pendingCustomers = Customer::where('status', 0)->count();
        $totalLeases = \App\Models\LeaseApplication::count();

        return view('pages.customers', compact(
            'customers',
            'districts',
            'minerals',
            'totalCustomers',
            'activeCustomers',
            'pendingCustomers',
            'totalLeases'
        ));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        // Auto-normalize Aadhaar to XXXX-XXXX-XXXX format if 12 digits are provided
        if ($request->filled('aadhaar_no')) {
            $digits = preg_replace('/\D/', '', $request->input('aadhaar_no'));
            if (strlen($digits) === 12) {
                $request->merge([
                    'aadhaar_no' => substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8, 4)
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'mimas_no'      => 'required|string|max:50|unique:customers,mimas_no',
            'customer_name' => 'required|string|max:255',
            'company_name'  => 'required|string|max:255',
            'mobile_num'    => 'required|string|max:15',
            'email'         => 'nullable|email|max:255',
            'district_id'   => 'required|exists:districts,id',
            'pan'           => 'required|string|max:10',
            'aadhaar_no'    => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/', 'unique:customers,aadhaar_no'],
            'gstin'         => 'nullable|string|max:15',
            'address'       => 'nullable|string',
            'status'        => 'required|in:0,1',
        ], [
            'aadhaar_no.regex' => 'The Aadhaar number must be a valid 12-digit number (e.g. 9876-5432-1012 or 987654321012).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['mimas_no'] = trim($data['mimas_no']);
        $data['aadhaar_no'] = trim($data['aadhaar_no']);
        $data['pan'] = strtoupper($data['pan']);
        if (!empty($data['gstin'])) {
            $data['gstin'] = strtoupper($data['gstin']);
        }
        $data['created_by'] = Auth::id();

        $customer = Customer::create($data);

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $customer->company_name . '" created successfully!',
            'data'    => $customer,
        ]);
    }

    /**
     * Return single customer data for View modal (AJAX) or 360° Profile Dossier Page (Web).
     */
    public function show(Request $request, $slug)
    {
        $customer = Customer::with([
            'district',
            'leaseApplications.surveyNumbers',
            'leaseApplications.category',
            'leaseApplications.mineral',
            'miningApplications.planType',
            'miningApplications.mineral',
            'miningApplications.boundaryPoints',
            'miningApplications.productionSchedules',
            'environmentProjects.ecCertificates',
            'pptApplications.agendas',
            'dgpsSurveys.points',
            'droneSurveys',
            'stockpiles.mineral',
            'stockpiles.dispatches' => fn($q) => $q->latest()->take(10),
            'stockpiles.entries' => fn($q) => $q->latest()->take(10),
        ])
        ->where(function ($query) use ($slug) {
            $query->where('slug', $slug)
                  ->orWhere('id', $slug);
        })
        ->first();

        if (!$customer) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 0, 'message' => 'Customer not found.'], 404);
            }
            abort(404, 'Customer not found.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 1,
                'data'   => $customer,
            ]);
        }

        // Calculate KPI summary metrics for this customer
        $totalLeases = $customer->leaseApplications->count();
        $totalMiningPlans = $customer->miningApplications->count();
        $approvedMiningPlans = $customer->miningApplications->where('status', 'approved')->count();
        $activeEcCount = $customer->environmentProjects->flatMap->ecCertificates->where('status', 'active')->count();
        $currentStockpileCbm = $customer->stockpiles->sum('current_stock_cbm');

        return view('pages.customer_show', compact(
            'customer',
            'totalLeases',
            'totalMiningPlans',
            'approvedMiningPlans',
            'activeEcCount',
            'currentStockpileCbm'
        ));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['status' => 0, 'message' => 'Customer not found.'], 404);
        }

        // Auto-normalize Aadhaar to XXXX-XXXX-XXXX format if 12 digits are provided
        if ($request->filled('aadhaar_no')) {
            $digits = preg_replace('/\D/', '', $request->input('aadhaar_no'));
            if (strlen($digits) === 12) {
                $request->merge([
                    'aadhaar_no' => substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8, 4)
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'id'            => 'required|exists:customers,id',
            'mimas_no'      => 'required|string|max:50|unique:customers,mimas_no,' . $id,
            'customer_name' => 'required|string|max:255',
            'company_name'  => 'required|string|max:255',
            'mobile_num'    => 'required|string|max:15',
            'email'         => 'nullable|email|max:255',
            'district_id'   => 'required|exists:districts,id',
            'pan'           => 'required|string|max:10',
            'aadhaar_no'    => ['required', 'string', 'max:20', 'regex:/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/', 'unique:customers,aadhaar_no,' . $id],
            'gstin'         => 'nullable|string|max:15',
            'address'       => 'nullable|string',
            'status'        => 'required|in:0,1',
        ], [
            'aadhaar_no.regex' => 'The Aadhaar number must be a valid 12-digit number (e.g. 9876-5432-1012 or 987654321012).',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['mimas_no'] = trim($data['mimas_no']);
        $data['aadhaar_no'] = trim($data['aadhaar_no']);
        $data['pan'] = strtoupper($data['pan']);
        if (!empty($data['gstin'])) {
            $data['gstin'] = strtoupper($data['gstin']);
        }

        $customer->update($data);

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $customer->company_name . '" updated successfully!',
            'data'    => $customer,
        ]);
    }

    /**
     * Universal Master Lookup: Fetch customer by unique MIMAS number for cross-application autofill.
     */
    public function lookupByMimas(Request $request, $mimas_no)
    {
        $mimas_no = trim(urldecode($mimas_no));

        $customer = Customer::with(['district', 'mineral'])
            ->where('mimas_no', $mimas_no)
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => 0,
                'message' => 'No customer found matching MIMAS Number: "' . $mimas_no . '".',
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Customer found successfully.',
            'data' => [
                'id'              => $customer->id,
                'mimas_no'        => $customer->mimas_no,
                'company_name'    => $customer->company_name,
                'customer_name'   => $customer->customer_name,
                'mobile_num'      => $customer->mobile_num,
                'email'           => $customer->email,
                'district_id'     => $customer->district_id,
                'district_name'   => $customer->district?->name,
                'mineral_id'      => $customer->mineral_id,
                'mineral_name'    => $customer->mineral?->name,
                'pan'             => $customer->pan,
                'aadhaar_no'      => $customer->aadhaar_no,
                'gstin'           => $customer->gstin,
                'area'            => $customer->area,
                'address'         => $customer->address,
                'status'          => $customer->status,
                'slug'            => $customer->slug,
            ],
        ]);
    }

    /**
     * Soft delete the specified customer.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['status' => 0, 'message' => 'Customer not found.'], 404);
        }

        $company = $customer->company_name;
        $customer->delete();

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $company . '" deleted successfully!',
        ]);
    }
}
