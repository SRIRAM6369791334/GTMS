<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PptApplication;
use App\Models\PptDocument;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\EnvironmentProject;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PptDepartmentController extends Controller
{
    /**
     * Master Index Listing (Dynamic Database Records & KPI Cards)
     */
    public function index(Request $request)
    {
        $query = PptApplication::with(['customer', 'district', 'mineral', 'documents']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('application_no', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'validation') {
                $query->whereIn('status', ['draft', 'agenda_scheduled']);
            } elseif ($status === 'approved') {
                $query->whereIn('status', ['presented', 'approved']);
            } elseif ($status === 'archived') {
                $query->where('status', 'archived');
            }
        }

        $applications = $query->latest()->paginate(10)->withQueryString();

        // Real KPI Counts
        $totalCount      = PptApplication::count();
        $validationCount = PptApplication::whereIn('status', ['draft', 'agenda_scheduled'])->count();
        $approvedCount   = PptApplication::whereIn('status', ['presented', 'approved'])->count();
        $archivedCount   = PptApplication::where('status', 'archived')->count();

        return view('pages.ppt_department.index', compact(
            'applications',
            'totalCount',
            'validationCount',
            'approvedCount',
            'archivedCount'
        ));
    }

    /**
     * Step-by-Step Interactive Wizard
     */
    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 9, 404);

        $draft = session('ppt_wizard', []);

        // Resume an existing application into draft if specified
        if ($request->has('resume')) {
            $existing = PptApplication::with(['customer', 'handlers', 'payments', 'documents'])->find($request->resume);
            if ($existing) {
                $draft = [
                    'id'             => $existing->id,
                    'application_no' => $existing->application_no,
                    'customer_id'    => $existing->customer_id,
                    'project_name'   => $existing->project_name,
                    'district_id'    => $existing->district_id,
                    'taluk_village'  => $existing->taluk_village,
                    'mineral_id'     => $existing->mineral_id,
                    'status'         => $existing->status,
                    'product_value'  => $existing->product_value,
                    'paid_amount'    => $existing->paid_amount,
                    'pending_amount' => $existing->pending_amount,
                    'payment_status' => $existing->payment_status,
                    'handlers'       => $existing->handlers->toArray(),
                ];
                session(['ppt_wizard' => $draft]);
            }
        }

        $customers = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name', 'mimas_no', 'mobile_num']);
        $districts = District::orderBy('name')->get(['id', 'name']);
        $minerals = Mineral::orderBy('name')->get(['id', 'name']);
        $envProjects = EnvironmentProject::orderBy('project_name')->get(['id', 'project_name', 'customer_id']);

        return view('pages.ppt_department.wizard', compact(
            'step',
            'draft',
            'customers',
            'districts',
            'minerals',
            'envProjects'
        ));
    }

    /**
     * Save Step in Wizard Draft
     */
    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 9, 404);

        $draft = session('ppt_wizard', []);

        // Step 1: Client & Basic Info
        if ($step === 1) {
            $draft['customer_id']    = $request->input('customer_id');
            $draft['project_name']   = $request->input('project_name', 'Presentation Project');
            $draft['application_no'] = $request->input('application_no', 'PPT-' . date('Y') . '-' . sprintf('%04d', PptApplication::count() + 1));
        }

        // Step 2: District
        if ($step === 2) {
            $draft['district_id']   = $request->input('district_id');
            $draft['taluk_village'] = $request->input('taluk_village');
        }

        // Step 3: Minerals
        if ($step === 3) {
            $draft['mineral_id'] = $request->input('mineral_id');
        }

        // Step 7: Handlers
        if ($step === 7) {
            $draft['handlers'] = $request->input('handlers', []);
        }

        // Step 8: Payment
        if ($step === 8) {
            $val = (float)$request->input('product_value', 0);
            $paid = (float)$request->input('paid_amount', 0);
            $draft['product_value']  = $val;
            $draft['paid_amount']    = $paid;
            $draft['pending_amount'] = max(0, $val - $paid);
            $draft['payment_status'] = $request->input('payment_status', 'pending');
            $draft['payment_notes']  = $request->input('payment_notes');
        }

        session(['ppt_wizard' => $draft]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'next_step' => $step + 1]);
        }

        if ($step < 9) {
            return redirect()->route('ppt-department.step', $step + 1);
        }

        return redirect()->route('ppt-department.step', 9);
    }

    /**
     * Final Submission & Database Persistence
     */
    public function store(Request $request)
    {
        $draft = session('ppt_wizard', []);

        $customerId = $request->input('customer_id') ?: ($draft['customer_id'] ?? Customer::value('id'));
        $districtId = $request->input('district_id') ?: ($draft['district_id'] ?? District::value('id'));
        $mineralId  = $request->input('mineral_id') ?: ($draft['mineral_id'] ?? Mineral::value('id'));
        $val        = (float)($request->input('product_value', $draft['product_value'] ?? 45000));
        $paid       = (float)($request->input('paid_amount', $draft['paid_amount'] ?? 45000));
        $pending    = max(0, $val - $paid);
        $pStatus    = $request->input('payment_status', $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending')));

        $count = PptApplication::count() + 1;
        $appNo = $request->input('application_no') ?: ($draft['application_no'] ?? sprintf('PPT-%s-%04d', date('Y'), $count));

        DB::beginTransaction();
        try {
            $ppt = PptApplication::create([
                'application_no' => $appNo,
                'customer_id'    => $customerId,
                'project_name'   => $request->input('project_name', $draft['project_name'] ?? 'SEAC/SEIAA Presentation Process'),
                'district_id'    => $districtId,
                'taluk_village'  => $request->input('taluk_village', $draft['taluk_village'] ?? 'Local Village & Taluk'),
                'mineral_id'     => $mineralId,
                'status'         => 'agenda_scheduled',
                'product_value'  => $val,
                'paid_amount'    => $paid,
                'pending_amount' => $pending,
                'payment_status' => $pStatus,
            ]);

            // Save Handlers
            $handlers = $request->input('handlers', $draft['handlers'] ?? []);
            if (!empty($handlers)) {
                foreach ($handlers as $hIdx => $h) {
                    if (!empty($h['person_name']) || !empty($h['name'])) {
                        ApplicationHandler::create([
                            'application_type' => 'ppt',
                            'application_id'   => $ppt->id,
                            'name'             => $h['person_name'] ?? $h['name'],
                            'role'             => $h['role'] ?? 'Consultant',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $hIdx + 1,
                        ]);
                    }
                }
            } else {
                ApplicationHandler::create([
                    'application_type' => 'ppt',
                    'application_id'   => $ppt->id,
                    'name'             => 'Dr. K. Ravichandran',
                    'role'             => 'Lead Presentation Consultant',
                    'notes'            => 'Committee defense and technical queries',
                    'sort_order'       => 1,
                ]);
            }

            // Save Payment
            ApplicationPayment::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $pStatus,
                'notes'            => $request->input('payment_notes', $draft['payment_notes'] ?? 'Consultation and presentation fee settled'),
            ]);

            DB::commit();
            session()->forget('ppt_wizard');

            return redirect()->route('ppt-department.show', $ppt->id)
                ->with('success', "PPT Application {$ppt->application_no} successfully saved and synchronized!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving PPT application: ' . $e->getMessage());
        }
    }

    /**
     * Show Application Dossier
     */
    public function show($id)
    {
        $ppt = PptApplication::with(['customer', 'district', 'mineral', 'handlers', 'payments', 'documents', 'agendas'])->findOrFail($id);
        return view('pages.ppt_department.show', compact('ppt'));
    }

    /**
     * Upload Document via AJAX
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file'           => 'required|file|max:25600',
            'application_id' => 'nullable|integer',
            'folder_id'      => 'nullable|integer',
            'document_name'  => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads/ppt', $fileName, 'public');

        $docId = null;
        if ($appId = $request->input('application_id')) {
            $doc = PptDocument::create([
                'ppt_application_id' => $appId,
                'folder_id'          => $request->input('folder_id', 1),
                'document_name'      => $request->input('document_name'),
                'file_name'          => $file->getClientOriginalName(),
                'file_path'          => $filePath,
                'file_type'          => $file->getClientOriginalExtension(),
                'file_size'          => $file->getSize(),
                'status'             => 'uploaded',
            ]);
            $docId = $doc->id;
        }

        return response()->json([
            'success'   => true,
            'doc_id'    => $docId,
            'file_name' => $file->getClientOriginalName(),
            'file_url'  => asset('storage/' . $filePath),
            'file_size' => round($file->getSize() / 1024) . ' KB',
        ]);
    }
}
