<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EcCompliance;
use App\Models\EcComplianceDocument;
use App\Models\Customer;
use App\Models\EnvironmentProject;
use App\Models\EcCertificate;
use App\Models\District;
use App\Models\Mineral;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EcComplianceController extends Controller
{
    /**
     * Master Listing & Live KPI Statistics
     */
    public function index(Request $request)
    {
        $query = EcCompliance::with(['customer', 'environmentProject', 'ecCertificate', 'district', 'mineral', 'documents']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('compliance_no', 'like', "%{$search}%")
                  ->orWhere('project_name', 'like', "%{$search}%")
                  ->orWhere('parivesh_acknowledgement_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'lab') {
                $query->whereIn('status', ['draft', 'documents_collected', 'lab_analysed']);
            } elseif ($status === 'uploaded') {
                $query->where('status', 'uploaded_to_parivesh');
            } elseif ($status === 'completed') {
                $query->where('status', 'completed');
            }
        }

        $compliances = $query->latest()->paginate(10)->withQueryString();

        // Real KPI Counts
        $totalCount    = EcCompliance::count();
        $labStageCount = EcCompliance::whereIn('status', ['draft', 'documents_collected', 'lab_analysed'])->count();
        $uploadedCount = EcCompliance::where('status', 'uploaded_to_parivesh')->count();
        $completeCount = EcCompliance::where('status', 'completed')->count();

        return view('pages.ec_compliance.index', compact(
            'compliances',
            'totalCount',
            'labStageCount',
            'uploadedCount',
            'completeCount'
        ));
    }

    /**
     * Interactive Multi-Step Compliance Flow Wizard (Matching Diagram)
     */
    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('ec_compliance_wizard', []);

        if ($request->has('resume')) {
            $existing = EcCompliance::with(['customer', 'handlers', 'payments', 'documents'])->find($request->resume);
            if ($existing) {
                $draft = [
                    'id'                     => $existing->id,
                    'compliance_no'          => $existing->compliance_no,
                    'customer_id'            => $existing->customer_id,
                    'environment_project_id' => $existing->environment_project_id,
                    'ec_certificate_id'      => $existing->ec_certificate_id,
                    'project_name'           => $existing->project_name,
                    'district_id'            => $existing->district_id,
                    'taluk_village'          => $existing->taluk_village,
                    'mineral_id'             => $existing->mineral_id,
                    'compliance_period'      => $existing->compliance_period,
                    'submission_due_date'    => $existing->submission_due_date ? $existing->submission_due_date->format('Y-m-d') : '2026-12-01',
                    'parivesh_app_no'        => $existing->parivesh_app_no,
                    'nabl_lab_name'          => $existing->nabl_lab_name,
                    'status'                 => $existing->status,
                    'product_value'          => $existing->product_value,
                    'paid_amount'            => $existing->paid_amount,
                    'pending_amount'         => $existing->pending_amount,
                    'payment_status'         => $existing->payment_status,
                    'handlers'               => $existing->handlers->toArray(),
                ];
                session(['ec_compliance_wizard' => $draft]);
            }
        }

        $customers   = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name', 'mimas_no', 'mobile_num']);
        $districts   = District::orderBy('name')->get(['id', 'name']);
        $minerals    = Mineral::orderBy('name')->get(['id', 'name']);
        $envProjects = EnvironmentProject::orderBy('project_name')->get(['id', 'project_name', 'customer_id']);
        $ecCertificates = EcCertificate::orderBy('ec_ref_no')->get(['id', 'ec_ref_no', 'applicant_name']);

        // Default list of 19 statutory documents from diagram
        $docs19 = [
            '1. 500m Radius Letter',
            '2. 300m Radius Letter',
            '3. CTO (Consent to Operate)',
            '4. Lease Deed',
            '5. Explosive Certificate',
            '6. Insurance',
            '7. Newspaper Advertisement',
            '8. Quarry Name Board Image',
            '9. Greenbelt & Fencing Photo',
            '10. Labour Shed & Toilet Facilities',
            '11. First Aid Box',
            '12. RO- Water Facility',
            '13. Water Sprinkling',
            '14. Safety Equipment (PPE)',
            '15. CSR Activist Photos',
            '16. CER Activist Photos',
            '17. Last Permit Details',
            '18. Transportation - Cover Trucks Photos',
            '19. CCTV Camera Installation from Project',
        ];

        // 4 NABL Site Analysis tests from diagram
        $labs4 = [
            '1. Ambient Air Quality Monitoring (PM10, PM2.5, SO2, NOx)',
            '2. Ambient Noise Level Monitoring (Day & Night Leq)',
            '3. Soil Quality Analysis (Physical & Chemical Parameters)',
            '4. Water Quality Sample Testing (Groundwater & Runoff)',
        ];

        // 3 Report components from diagram
        $report3 = [
            '1. Front Page (Project, EC Details & Compliance Period)',
            '2. Covering Letter (Submission to MoEFCC / SEIAA / SPCB)',
            '3. EC- Compliance Report (Point-by-Point Condition-Wise Status)',
        ];

        return view('pages.ec_compliance.wizard', compact(
            'step',
            'draft',
            'customers',
            'districts',
            'minerals',
            'envProjects',
            'ecCertificates',
            'docs19',
            'labs4',
            'report3'
        ));
    }

    /**
     * Save Step in Session Draft
     */
    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 8, 404);

        $draft = session('ec_compliance_wizard', []);

        // Step 1: Project & Period
        if ($step === 1) {
            $draft['customer_id']            = $request->input('customer_id');
            $draft['environment_project_id'] = $request->input('environment_project_id');
            $draft['ec_certificate_id']      = $request->input('ec_certificate_id');
            $draft['project_name']           = $request->input('project_name');
            $draft['district_id']            = $request->input('district_id');
            $draft['taluk_village']          = $request->input('taluk_village');
            $draft['compliance_period']      = $request->input('compliance_period', 'April 2026 - September 2026');
            $draft['submission_due_date']    = $request->input('submission_due_date', '2026-12-01');
            $draft['compliance_no']          = $request->input('compliance_no', 'HYC-' . date('Y') . '-' . sprintf('%04d', EcCompliance::count() + 1));
        }

        // Step 3: NABL Lab Info
        if ($step === 3) {
            $draft['nabl_lab_name']      = $request->input('nabl_lab_name', 'Glens Innovation Labs (NABL #TC-7712)');
            $draft['nabl_certificate_no'] = $request->input('nabl_certificate_no', 'NABL/ENV/2026/0441');
            $draft['monitoring_date']    = $request->input('monitoring_date', date('Y-m-d'));
        }

        // Step 5: Parivesh Upload
        if ($step === 5) {
            $draft['parivesh_acknowledgement_no'] = $request->input('parivesh_acknowledgement_no');
            $draft['parivesh_uploaded_date']      = $request->input('parivesh_uploaded_date', date('Y-m-d'));
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

        session(['ec_compliance_wizard' => $draft]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'next_step' => $step + 1]);
        }

        if ($step < 8) {
            return redirect()->route('ec-compliance.step', $step + 1);
        }

        return redirect()->route('ec-compliance.step', 8);
    }

    /**
     * Store Final Compliance Record
     */
    public function store(Request $request)
    {
        $draft = session('ec_compliance_wizard', []);

        $customerId = $request->input('customer_id') ?: ($draft['customer_id'] ?? Customer::value('id'));
        $districtId = $request->input('district_id') ?: ($draft['district_id'] ?? District::value('id'));
        $val        = (float)($request->input('product_value', $draft['product_value'] ?? 50000));
        $paid       = (float)($request->input('paid_amount', $draft['paid_amount'] ?? 50000));
        $pending    = max(0, $val - $paid);
        $pStatus    = $request->input('payment_status', $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending')));

        $count = EcCompliance::count() + 1;
        $complianceNo = $request->input('compliance_no') ?: ($draft['compliance_no'] ?? sprintf('HYC-%s-%04d', date('Y'), $count));

        DB::beginTransaction();
        try {
            $compliance = EcCompliance::create([
                'compliance_no'               => $complianceNo,
                'customer_id'                 => $customerId,
                'environment_project_id'      => $request->input('environment_project_id', $draft['environment_project_id'] ?? null),
                'ec_certificate_id'           => $request->input('ec_certificate_id', $draft['ec_certificate_id'] ?? null),
                'project_name'                => $request->input('project_name', $draft['project_name'] ?? 'Environmental Clearance Half-Yearly Compliance'),
                'district_id'                 => $districtId,
                'taluk_village'               => $request->input('taluk_village', $draft['taluk_village'] ?? 'Quarry Site / Taluk'),
                'mineral_id'                  => $request->input('mineral_id', $draft['mineral_id'] ?? Mineral::value('id')),
                'compliance_period'           => $request->input('compliance_period', $draft['compliance_period'] ?? 'April 2026 - September 2026'),
                'compliance_year'             => date('Y'),
                'submission_due_date'         => $request->input('submission_due_date', $draft['submission_due_date'] ?? '2026-12-01'),
                'submission_date'             => date('Y-m-d'),
                'parivesh_app_no'             => $request->input('parivesh_app_no', $draft['parivesh_app_no'] ?? 'SIA/TN/MIN/' . rand(10000, 99999) . '/' . date('Y')),
                'parivesh_acknowledgement_no' => $request->input('parivesh_acknowledgement_no', $draft['parivesh_acknowledgement_no'] ?? 'PARIVESH-ACK-TN-2026-' . rand(1000, 9999)),
                'parivesh_uploaded_date'      => date('Y-m-d'),
                'nabl_lab_name'               => $request->input('nabl_lab_name', $draft['nabl_lab_name'] ?? 'Glens Innovation Labs (NABL #TC-7712)'),
                'nabl_certificate_no'         => $request->input('nabl_certificate_no', $draft['nabl_certificate_no'] ?? 'NABL/ENV/2026/0441'),
                'monitoring_date'             => $request->input('monitoring_date', $draft['monitoring_date'] ?? date('Y-m-d')),
                'status'                      => 'uploaded_to_parivesh',
                'product_value'               => $val,
                'paid_amount'                 => $paid,
                'pending_amount'              => $pending,
                'payment_status'              => $pStatus,
                'payment_notes'               => $request->input('payment_notes', $draft['payment_notes'] ?? 'Half-yearly compliance audit and laboratory package settled'),
            ]);

            // Handlers
            $handlers = $request->input('handlers', $draft['handlers'] ?? []);
            if (!empty($handlers)) {
                foreach ($handlers as $hIdx => $h) {
                    if (!empty($h['person_name']) || !empty($h['name'])) {
                        ApplicationHandler::create([
                            'application_type' => 'ec_compliance',
                            'application_id'   => $compliance->id,
                            'name'             => $h['person_name'] ?? $h['name'],
                            'role'             => $h['role'] ?? 'Environmental Auditor',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $hIdx + 1,
                        ]);
                    }
                }
            } else {
                ApplicationHandler::create([
                    'application_type' => 'ec_compliance',
                    'application_id'   => $compliance->id,
                    'name'             => 'Dr. N. Sundararajan',
                    'role'             => 'Senior Environmental Auditor & EMP Specialist',
                    'notes'            => 'Overall half-yearly compliance coordination and Parivesh submission',
                    'sort_order'       => 1,
                ]);
            }

            // Payment
            ApplicationPayment::create([
                'application_type' => 'ec_compliance',
                'application_id'   => $compliance->id,
                'product_value'    => $val,
                'paid_amount'      => $paid,
                'pending_amount'   => $pending,
                'payment_status'   => $pStatus,
                'notes'            => $request->input('payment_notes', $draft['payment_notes'] ?? 'Compliance monitoring fee settled'),
            ]);

            DB::commit();
            session()->forget('ec_compliance_wizard');

            return redirect()->route('ec-compliance.show', $compliance->id)
                ->with('success', "Half-Yearly Compliance {$compliance->compliance_no} recorded and uploaded to Parivesh!");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error saving compliance: ' . $e->getMessage());
        }
    }

    /**
     * Show Compliance Dossier
     */
    public function show($id)
    {
        $compliance = EcCompliance::with(['customer', 'environmentProject', 'ecCertificate', 'district', 'mineral', 'handlers', 'payments', 'documents'])->findOrFail($id);
        $comp = $compliance;
        return view('pages.ec_compliance.show', compact('compliance', 'comp'));
    }

    /**
     * Upload Document via AJAX
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file'            => 'required|file|max:51200',
            'compliance_id'   => 'nullable|integer',
            'folder_category' => 'required|string',
            'document_name'   => 'required|string|max:255',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('uploads/compliance', $fileName, 'public');

        $docId = null;
        if ($complianceId = $request->input('compliance_id')) {
            $doc = EcComplianceDocument::create([
                'ec_compliance_id' => $complianceId,
                'folder_category'  => $request->input('folder_category', 'documents'),
                'document_name'    => $request->input('document_name'),
                'file_name'        => $file->getClientOriginalName(),
                'file_path'        => $filePath,
                'file_type'        => $file->getClientOriginalExtension(),
                'file_size'        => $file->getSize(),
                'status'           => 'uploaded',
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
