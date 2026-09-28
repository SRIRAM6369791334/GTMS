<?php

namespace App\Http\Controllers;

use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\District;
use App\Models\DroneSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DroneSurveyController extends Controller
{
    public function index()
    {
        $surveys = DroneSurvey::with(['customer', 'leaseApplication'])
            ->latest()
            ->paginate(15);

        return view('pages.drone_survey.index', compact('surveys'));
    }

    public function show(int $id)
    {
        $survey = DroneSurvey::with([
            'customer', 'leaseApplication', 'miningApplication',
            'handlers', 'payments', 'documents', 'creator'
        ])->findOrFail($id);

        return view('pages.drone_survey.show', compact('survey'));
    }

    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('drone_wizard', []);

        // Resume existing survey
        if ($step === 1 && $request->filled('resume') && empty($draft['customer_id'])) {
            $existing = DroneSurvey::with('customer')->find($request->input('resume'));
            if ($existing) {
                $draft = array_merge($draft, [
                    'drone_survey_id'       => $existing->id,
                    'customer_id'           => $existing->customer_id,
                    'lease_application_id'  => $existing->lease_application_id,
                    'mining_application_id' => $existing->mining_application_id,
                    'location'              => $existing->location,
                    'flight_date'           => $existing->flight_date?->format('Y-m-d'),
                    'lease_area'            => $existing->lease_area,
                    'drone_pilot_name'      => $existing->drone_pilot_name,
                    'pilot_rpc_no'          => $existing->pilot_rpc_no,
                    'drone_uin_no'          => $existing->drone_uin_no,
                    'drone_model'           => $existing->drone_model,
                    'altitude_meters'       => $existing->altitude_meters,
                    'gsd_cm_px'             => $existing->gsd_cm_px,
                    'extracted_volume_cbm'  => $existing->extracted_volume_cbm,
                    'product_value'         => $existing->product_value,
                    'paid_amount'           => $existing->paid_amount,
                    'payment_status'        => $existing->payment_status,
                ]);
                session(['drone_wizard' => $draft]);
            }
        }

        if ($step === 1 && $request->filled('customer_id') && empty($draft['customer_id'])) {
            $draft['customer_id'] = (int) $request->input('customer_id');
            session(['drone_wizard' => $draft]);
        }

        $customers = Customer::withTrashed()->orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();

        return view('pages.drone_survey.wizard', compact('step', 'draft', 'customers', 'districts'));
    }

    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('drone_wizard', []);

        if ($step === 1) {
            $draft['customer_id']           = $request->input('customer_id');
            $draft['lease_application_id']  = $request->input('lease_application_id');
            $draft['mining_application_id'] = $request->input('mining_application_id');
            $draft['location']              = $request->input('location');
            $draft['lease_area']            = $request->input('lease_area');
            $draft['primary_phone']         = $request->input('primary_phone');
            $draft['secondary_phone']       = $request->input('secondary_phone');
        }

        if ($step === 2) {
            $draft['flight_date']      = $request->input('flight_date', date('Y-m-d'));
            $draft['drone_pilot_name'] = $request->input('drone_pilot_name');
            $draft['pilot_rpc_no']     = $request->input('pilot_rpc_no');
            $draft['drone_uin_no']     = $request->input('drone_uin_no');
            $draft['drone_model']      = $request->input('drone_model');
            $draft['altitude_meters']  = $request->input('altitude_meters');
            $draft['gsd_cm_px']        = $request->input('gsd_cm_px');
        }

        if ($step === 3) {
            $draft['extracted_volume_cbm']  = $request->input('extracted_volume_cbm');
            $draft['survey_status']         = $request->input('survey_status', 'processing');
            $draft['deliverable_files_path'] = $request->input('deliverable_files_path');
        }

        if ($step === 6) {
            $draft['handlers'] = $request->input('handlers', []);
        }

        if ($step === 7) {
            $val  = (float) $request->input('product_value', 0);
            $paid = (float) $request->input('paid_amount', 0);
            $draft['product_value']  = $val;
            $draft['paid_amount']    = $paid;
            $draft['pending_amount'] = max(0, $val - $paid);
            $draft['payment_status'] = $request->input('payment_status', 'pending');
            $draft['payment_notes']  = $request->input('payment_notes');
        }

        session(['drone_wizard' => $draft]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'next_step' => $step + 1]);
        }

        if ($step < 8) {
            return redirect()->route('drone-survey.step', $step + 1);
        }

        return redirect()->route('drone-survey.step', 8);
    }

    public function store(Request $request)
    {
        $draft = session('drone_wizard', []);

        $customerId = $request->input('customer_id') ?: ($draft['customer_id'] ?? Customer::value('id'));
        $val     = (float) ($request->input('product_value', $draft['product_value'] ?? 50000));
        $paid    = (float) ($request->input('paid_amount', $draft['paid_amount'] ?? 30000));
        $pending = max(0, $val - $paid);
        $pStatus = $request->input('payment_status', $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending')));

        $count   = DroneSurvey::count() + 1;
        $surveyNo = $request->input('survey_no') ?: ($draft['survey_no'] ?? sprintf('DRN-%s-%04d', date('Y'), $count));

        DB::beginTransaction();
        try {
            $survey = DroneSurvey::create([
                'survey_no'             => $surveyNo,
                'customer_id'           => $customerId,
                'lease_application_id'  => $request->input('lease_application_id', $draft['lease_application_id'] ?? null),
                'mining_application_id' => $request->input('mining_application_id', $draft['mining_application_id'] ?? null),
                'lease_area'            => (float) ($draft['lease_area'] ?? 3.85),
                'location'              => $request->input('location', $draft['location'] ?? 'Quarry Site'),
                'flight_date'           => $request->input('flight_date', $draft['flight_date'] ?? date('Y-m-d')),
                'drone_pilot_name'      => $draft['drone_pilot_name'] ?? null,
                'pilot_rpc_no'          => $draft['pilot_rpc_no'] ?? null,
                'drone_uin_no'          => $draft['drone_uin_no'] ?? null,
                'drone_model'           => $draft['drone_model'] ?? null,
                'altitude_meters'       => (float) ($draft['altitude_meters'] ?? 120),
                'gsd_cm_px'             => (float) ($draft['gsd_cm_px'] ?? 2.5),
                'extracted_volume_cbm'  => (float) ($draft['extracted_volume_cbm'] ?? 0),
                'survey_status'         => $draft['survey_status'] ?? 'completed',
                'product_value'         => $val,
                'paid_amount'           => $paid,
                'pending_amount'        => $pending,
                'payment_status'        => $pStatus,
                'branch_id'             => auth()->user()->branch_id ?? 1,
                'created_by'            => Auth::id() ?? 1,
            ]);

            // Save Handlers
            $handlers = $request->input('handlers', $draft['handlers'] ?? []);
            if (!empty($handlers)) {
                foreach ($handlers as $hIdx => $h) {
                    if (!empty($h['person_name']) || !empty($h['name'])) {
                        ApplicationHandler::create([
                            'application_type' => 'drone',
                            'application_id'   => $survey->id,
                            'name'             => $h['person_name'] ?? $h['name'],
                            'role'             => $h['role'] ?? 'Drone Pilot',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $hIdx + 1,
                        ]);
                    }
                }
            } else {
                ApplicationHandler::create([
                    'application_type' => 'drone',
                    'application_id'   => $survey->id,
                    'name'             => 'DGCA Certified Pilot',
                    'role'             => 'Lead Drone Pilot',
                    'notes'            => 'Autonomous grid flight execution and safety compliance',
                    'sort_order'       => 1,
                ]);
            }

            // Save Payment
            ApplicationPayment::create([
                'application_type' => 'drone',
                'application_id'   => $survey->id,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $pStatus,
                'notes'            => $draft['payment_notes'] ?? 'Drone Survey and Volumetric Processing Fee',
            ]);

            session()->forget('drone_wizard');

            DB::commit();
            return redirect()->route('drone-survey.show', $survey->id)
                ->with('success', "Drone Survey {$surveyNo} filed successfully!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Drone Survey store failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('drone-survey.step', 8)
                ->with('error', 'Failed to save Drone Survey: ' . $e->getMessage());
        }
    }
}
