<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
            'mimas_no'                 => ['required', 'string', 'max:50', Rule::unique('customers', 'mimas_no')->whereNull('deleted_at')],
            'mimas_number'             => 'nullable|string|max:100',
            'mimas_status'             => 'nullable|string|max:100',
            'customer_name'            => 'required|string|max:255',
            'secondary_contact_person' => 'nullable|string|max:255',
            'company_name'             => 'nullable|string|max:255',
            'mobile_num'               => 'required|string|max:15',
            'secondary_mobile_num'     => 'nullable|string|max:15|different:mobile_num',
            'email'                    => 'nullable|email|max:255',
            'district_id'              => 'required|exists:districts,id',
            'mineral_id'               => 'nullable|exists:minerals,id',
            'area'                     => 'nullable|numeric|min:0',
            'pan'                      => 'nullable|string|max:10',
            'aadhaar_no'               => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/', Rule::unique('customers', 'aadhaar_no')->whereNull('deleted_at')],
            'gstin'                    => 'nullable|string|max:15',
            'address'                  => 'nullable|string',
            'status'                   => 'required|in:0,1',
        ], [
            'aadhaar_no.regex'   => 'The Aadhaar number must be a valid 12-digit number (e.g. 9876-5432-1012 or 987654321012).',
            'mimas_no.unique'    => 'This Customer Unique ID is already registered by an active customer.',
            'aadhaar_no.unique'  => 'This Aadhaar number is already registered by an active customer.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['mimas_no'] = trim($data['mimas_no']);
        $data['company_name'] = !empty($data['company_name']) ? trim($data['company_name']) : null;
        $data['aadhaar_no'] = !empty($data['aadhaar_no']) ? trim($data['aadhaar_no']) : null;
        $data['pan'] = !empty($data['pan']) ? strtoupper(trim($data['pan'])) : null;
        if (!empty($data['gstin'])) {
            $data['gstin'] = strtoupper(trim($data['gstin']));
        } else {
            $data['gstin'] = null;
        }
        $data['created_by'] = Auth::id();

        // If a previously soft-deleted customer exists with this MIMAS or Aadhaar, restore and update
        $trashedCustomer = Customer::onlyTrashed()
            ->where(function ($query) use ($data) {
                $query->where('mimas_no', $data['mimas_no']);
                if (!empty($data['aadhaar_no'])) {
                    $query->orWhere('aadhaar_no', $data['aadhaar_no']);
                }
            })
            ->first();

        if ($trashedCustomer) {
            $trashedCustomer->restore();
            $trashedCustomer->update($data);
            $displayName = $trashedCustomer->company_name ?: $trashedCustomer->customer_name;
            return response()->json([
                'status'  => 1,
                'message' => 'Archived customer "' . $displayName . '" has been restored and updated successfully!',
                'data'    => $trashedCustomer,
            ]);
        }

        $customer = Customer::create($data);
        $displayName = $customer->company_name ?: $customer->customer_name;

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $displayName . '" created successfully!',
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
            'leaseApplications.minerals',
            'leaseApplications.miningApplications',
            'miningApplications.planType',
            'miningApplications.mineral',
            'miningApplications.minerals',
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
            'id'                       => 'required|exists:customers,id',
            'mimas_no'                 => ['required', 'string', 'max:50', Rule::unique('customers', 'mimas_no')->ignore($id)->whereNull('deleted_at')],
            'mimas_number'             => 'nullable|string|max:100',
            'mimas_status'             => 'nullable|string|max:100',
            'customer_name'            => 'required|string|max:255',
            'secondary_contact_person' => 'nullable|string|max:255',
            'company_name'             => 'nullable|string|max:255',
            'mobile_num'               => 'required|string|max:15',
            'secondary_mobile_num'     => 'nullable|string|max:15|different:mobile_num',
            'email'                    => 'nullable|email|max:255',
            'district_id'              => 'required|exists:districts,id',
            'mineral_id'               => 'nullable|exists:minerals,id',
            'area'                     => 'nullable|numeric|min:0',
            'pan'                      => 'nullable|string|max:10',
            'aadhaar_no'               => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/', Rule::unique('customers', 'aadhaar_no')->ignore($id)->whereNull('deleted_at')],
            'gstin'                    => 'nullable|string|max:15',
            'address'                  => 'nullable|string',
            'status'                   => 'required|in:0,1',
        ], [
            'aadhaar_no.regex'   => 'The Aadhaar number must be a valid 12-digit number (e.g. 9876-5432-1012 or 987654321012).',
            'mimas_no.unique'    => 'This Customer Unique ID is already registered by an active customer.',
            'aadhaar_no.unique'  => 'This Aadhaar number is already registered by an active customer.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['mimas_no'] = trim($data['mimas_no']);
        $data['company_name'] = !empty($data['company_name']) ? trim($data['company_name']) : null;
        $data['aadhaar_no'] = !empty($data['aadhaar_no']) ? trim($data['aadhaar_no']) : null;
        $data['pan'] = !empty($data['pan']) ? strtoupper(trim($data['pan'])) : null;
        if (!empty($data['gstin'])) {
            $data['gstin'] = strtoupper(trim($data['gstin']));
        } else {
            $data['gstin'] = null;
        }

        // Check if conflict with an existing archived/soft-deleted record
        $trashedConflict = Customer::onlyTrashed()
            ->where(function ($query) use ($data) {
                $query->where('mimas_no', $data['mimas_no']);
                if (!empty($data['aadhaar_no'])) {
                    $query->orWhere('aadhaar_no', $data['aadhaar_no']);
                }
            })
            ->where('id', '!=', $customer->id)
            ->first();

        if ($trashedConflict) {
            $conflictName = $trashedConflict->company_name ?: $trashedConflict->customer_name;
            return response()->json([
                'status' => 0,
                'errors' => [
                    'mimas_no' => ['This MIMAS or Aadhaar number is currently reserved by an archived customer ("' . $conflictName . '"). Please restore that customer or use a different number.']
                ]
            ], 422);
        }

        $customer->update($data);
        $displayName = $customer->company_name ?: $customer->customer_name;

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $displayName . '" updated successfully!',
            'data'    => $customer,
        ]);
    }

    /**
     * Universal Master Lookup: Fetch customer by unique MIMAS number for cross-application autofill.
     */
    public function lookupByMimas(Request $request, $mimas_no)
    {
        $mimas_no = trim(urldecode($mimas_no));
        $cleanDigits = preg_replace('/[^0-9]/', '', $mimas_no);

        $customer = Customer::withTrashed()->with(['district', 'mineral'])
            ->where(function ($query) use ($mimas_no, $cleanDigits) {
                $query->where('mimas_no', $mimas_no)
                    ->orWhere('id', $mimas_no)
                    ->orWhere('slug', $mimas_no)
                    ->orWhere('mimas_number', $mimas_no)
                    ->orWhere('company_name', 'like', "%{$mimas_no}%")
                    ->orWhere('customer_name', 'like', "%{$mimas_no}%");

                if (!empty($cleanDigits) && strlen($cleanDigits) >= 10) {
                    $query->orWhere('mobile_num', 'like', "%{$cleanDigits}%")
                        ->orWhere('secondary_mobile_num', 'like', "%{$cleanDigits}%")
                        ->orWhereRaw("REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') LIKE ?", ["%{$cleanDigits}%"]);
                }
            })
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => 0,
                'message' => 'No customer found matching Customer Unique ID: "' . $mimas_no . '".',
            ], 404);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Customer found successfully.',
            'data' => [
                'id'              => $customer->id,
                'mimas_no'        => $customer->mimas_no,
                'mimas_number'    => $customer->mimas_number,
                'mimas_status'    => $customer->mimas_status,
                'company_name'    => $customer->company_name,
                'customer_name'            => $customer->customer_name,
                'secondary_contact_person' => $customer->secondary_contact_person,
                'mobile_num'               => $customer->mobile_num,
                'secondary_mobile_num'     => $customer->secondary_mobile_num,
                'email'                    => $customer->email,
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
     * Soft delete the specified customer with active dependency protection.
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['status' => 0, 'message' => 'Customer not found.'], 404);
        }

        // Active statutory dependencies check
        $activeDependencies = [];
        if ($customer->leaseApplications()->exists()) {
            $activeDependencies[] = $customer->leaseApplications()->count() . ' Lease Application(s)';
        }
        if ($customer->miningApplications()->exists()) {
            $activeDependencies[] = $customer->miningApplications()->count() . ' Mining Plan(s)';
        }
        if ($customer->environmentProjects()->exists()) {
            $activeDependencies[] = $customer->environmentProjects()->count() . ' Environmental Project(s)';
        }
        if ($customer->dgpsSurveys()->exists()) {
            $activeDependencies[] = $customer->dgpsSurveys()->count() . ' DGPS Survey(s)';
        }
        if ($customer->stockpiles()->exists()) {
            $activeDependencies[] = $customer->stockpiles()->count() . ' Mineral Stockpile(s)';
        }

        if (!empty($activeDependencies)) {
            return response()->json([
                'status'  => 0,
                'message' => 'Cannot delete customer. This customer is linked to: ' . implode(', ', $activeDependencies) . '. Please reassign or delete these linked records first.'
            ], 422);
        }

        $company = $customer->company_name;
        $customer->delete();

        return response()->json([
            'status'  => 1,
            'message' => 'Customer "' . $company . '" deleted successfully!',
        ]);
    }
}
