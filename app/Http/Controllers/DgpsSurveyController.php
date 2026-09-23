<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DgpsSurvey;
use App\Models\SurveyDocument;
use App\Models\Customer;
use App\Models\LeaseApplication;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DgpsSurveyController extends Controller
{
    /**
     * Master Index Listing (Dynamic Database Records & KPI Cards)
     */
    public function index(Request $request)
    {
        $query = DgpsSurvey::with(['customer', 'leaseApplication', 'documents']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('survey_no', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'field') {
                $query->where('survey_status', 'in_progress');
            } elseif ($status === 'completed') {
                $query->where('survey_status', 'completed');
            } elseif ($status === 'uploaded') {
                $query->where('report_status', 'verified');
            }
        }

        $surveys = $query->latest()->paginate(10)->withQueryString();

        // Real KPI Counts
        $totalRequests     = DgpsSurvey::count();
        $fieldSurveyCount  = DgpsSurvey::where('survey_status', 'in_progress')->count();
        $reportsReadyCount = DgpsSurvey::where('survey_status', 'completed')->count();
        $gtmUploadedCount  = DgpsSurvey::where('report_status', 'verified')->count();

        return view('pages.dgps_survey.index', compact(
            'surveys',
            'totalRequests',
            'fieldSurveyCount',
            'reportsReadyCount',
            'gtmUploadedCount'
        ));
    }

    /**
     * Step-by-Step Interactive Wizard
     */
    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('dgps_wizard', []);

        // Resume an existing survey into draft if specified
        if ($request->has('resume')) {
            $existing = DgpsSurvey::with(['customer', 'handlers', 'payments', 'documents'])->find($request->resume);
            if ($existing) {
                $draft = [
                    'id'                   => $existing->id,
                    'survey_no'            => $existing->survey_no,
                    'customer_id'          => $existing->customer_id,
                    'lease_application_id' => $existing->lease_application_id,
                    'lease_area_ha'        => $existing->lease_area_ha,
                    'surveyed_area_ha'     => $existing->surveyed_area_ha,
                    'location'             => $existing->location,
                    'survey_date'          => $existing->survey_date ? $existing->survey_date->format('Y-m-d') : date('Y-m-d'),
                    'survey_status'        => $existing->survey_status,
                    'report_status'        => $existing->report_status,
                    'product_value'        => $existing->product_value,
                    'paid_amount'          => $existing->paid_amount,
                    'pending_amount'       => $existing->pending_amount,
                    'payment_status'       => $existing->payment_status,
                    'handlers'             => $existing->handlers->toArray(),
                ];
                session(['dgps_wizard' => $draft]);
            }
        }

        $customers = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name', 'mimas_no', 'mobile_num']);
        $leaseApps = LeaseApplication::orderBy('application_no')->get(['id', 'application_no', 'customer_id', 'area_extent_acres']);

        return view('pages.dgps_survey.wizard', compact(
            'step',
            'draft',
            'customers',
            'leaseApps'
        ));
    }

    /**
     * Save Step in Wizard Draft
     */
    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('dgps_wizard', []);

        // Step 1: Request & Location
        if ($step === 1) {
            $draft['customer_id']          = $request->input('customer_id');
            $draft['lease_application_id'] = $request->input('lease_application_id');
            $draft['lease_area_ha']        = $request->input('lease_area_ha', 3.85);
            $draft['location']             = $request->input('location', 'Salem / Semmandapatti');
            $draft['survey_no']            = $request->input('survey_no', 'DGPS-' . date('Y') . '-' . sprintf('%04d', DgpsSurvey::count() + 1));
        }

        // Step 2: Field Survey
        if ($step === 2) {
            $draft['survey_date']       = $request->input('survey_date', date('Y-m-d'));
            $draft['survey_team_notes'] = $request->input('survey_team_notes');
        }

        // Step 6: Handlers
        if ($step === 6) {
            $draft['handlers'] = $request->input('handlers', []);
        }

        // Step 7: Payment
        if ($step === 7) {
            $val = (float)$request->input('product_value', 0);
            $paid = (float)$request->input('paid_amount', 0);
            $draft['product_value']  = $val;
            $draft['paid_amount']    = $paid;
            $draft['pending_amount'] = max(0, $val - $paid);
            $draft['payment_status'] = $request->input('payment_status', 'pending');
            $draft['payment_notes']  = $request->input('payment_notes');
        }

        session(['dgps_wizard' => $draft]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'next_step' => $step + 1]);
        }

        if ($step < 8) {
            return redirect()->route('dgps-survey.step', $step + 1);
        }

        return redirect()->route('dgps-survey.step', 8);
    }

    /**
     * Final Submission & Database Persistence
     */
    public function store(Request $request)
    {
        $draft = session('dgps_wizard', []);

        $customerId = $request->input('customer_id') ?: ($draft['customer_id'] ?? Customer::value('id'));
        $val        = (float)($request->input('product_value', $draft['product_value'] ?? 35000));
        $paid       = (float)($request->input('paid_amount', $draft['paid_amount'] ?? 35000));
        $pending    = max(0, $val - $paid);
        $pStatus    = $request->input('payment_status', $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending')));

        $count = DgpsSurvey::count() + 1;
        $surveyNo = $request->input('survey_no') ?: ($draft['survey_no'] ?? sprintf('DGPS-%s-%04d', date('Y'), $count));

        DB::beginTransaction();
        try {
            $survey = DgpsSurvey::create([
                'survey_no'            => $surveyNo,
                'field_book_no'        => 'FB-TN-' . rand(100, 999),
                'customer_id'          => $customerId,
                'lease_application_id' => $request->input('lease_application_id', $draft['lease_application_id'] ?? null),
                'lease_area_ha'        => (float)($request->input('lease_area_ha', $draft['lease_area_ha'] ?? 3.85)),
                'surveyed_area_ha'     => (float)($request->input('surveyed_area_ha', $draft['surveyed_area_ha'] ?? 3.84)),
                'area_discrepancy_ha'  => 0.01,
                'location'             => $request->input('location', $draft['location'] ?? 'Salem District'),
                'survey_date'          => $request->input('survey_date', $draft['survey_date'] ?? date('Y-m-d')),
                'instrument_model'     => 'Trimble R12i GNSS RTK Base & Rover',
                'instrument_serial_no' => 'SN-TRM-889102',
                'survey_status'        => 'completed',
                'report_status'        => 'verified',
                'product_value'        => $val,
                'paid_amount'          => $paid,
                'pending_amount'       => $pending,
                'payment_status'       => $pStatus,
                'survey_team_notes'    => 'SOI Benchmark fixed at Pillar #1, rover calibration verified within 5mm tolerance',
            ]);

            // Save Handlers
            $handlers = $request->input('handlers', $draft['handlers'] ?? []);
            if (!empty($handlers)) {
                foreach ($handlers as $hIdx => $h) {
                    if (!empty($h['person_name']) || !empty($h['name'])) {
                        ApplicationHandler::create([
                            'application_type' => 'dgps',
                            'application_id'   => $survey->id,
                            'name'             => $h['person_name'] ?? $h['name'],
                            'role'             => $h['role'] ?? 'Surveyor',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $hIdx + 1,
                        ]);
                    }
                }
            } else {
                ApplicationHandler::create([
                    'application_type' => 'dgps',
                    'application_id'   => $survey->id,
                    'name'             => 'Er. A. Vijayakumar',
                    'role'             => 'Chief Land Surveyor',
                    'notes'            => 'Base station setup, static observation & benchmark fixation',
                    'sort_order'       => 1,
                ]);
            }

            // Save Payment
            ApplicationPayment::create([
                'application_type' => 'dgps',
                'application_id'   => $survey->id,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $pStatus,
                'notes'            => $request->input('payment_notes', $draft['payment_notes'] ?? 'Field survey charges settled'),
            ]);

            DB::commit();
            session()->forget('dgps_wizard');

            return redirect()->route('dgps-survey.show', $survey->id)
                ->with('success', "DGPS Survey {$survey->survey_no} successfully saved and synchronized!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving DGPS survey: ' . $e->getMessage());
        }
    }

    /**
     * Show Survey Dossier
     */
    public function show($id)
    {
        $survey = DgpsSurvey::with(['customer', 'leaseApplication', 'handlers', 'payments', 'documents'])->findOrFail($id);
        return view('pages.dgps_survey.show', compact('survey'));
    }

    /**
     * Upload Document via AJAX
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file'          => 'required|file|max:51200',
            'survey_id'     => 'nullable|integer',
            'document_name' => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads/dgps', $fileName, 'public');

        $docId = null;
        if ($surveyId = $request->input('survey_id')) {
            $doc = SurveyDocument::create([
                'dgps_survey_id' => $surveyId,
                'document_name'  => $request->input('document_name'),
                'file_name'      => $file->getClientOriginalName(),
                'file_path'      => $filePath,
                'file_type'      => $file->getClientOriginalExtension(),
                'file_size'      => $file->getSize(),
                'status'         => 'uploaded',
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
