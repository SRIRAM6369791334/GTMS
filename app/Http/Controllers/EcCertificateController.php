<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\EcCertificate;
use App\Models\EnvironmentProject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EcCertificateController extends Controller
{
    /**
     * List all issued Environmental Clearance Certificates with dynamic KPI metrics
     */
    public function index(Request $request)
    {
        $kpis = [
            'total_projects' => EnvironmentProject::count(),
            'approved'       => EnvironmentProject::where('status', 'approved')->count(),
            'ready_download' => EcCertificate::whereNotNull('certificate_file')->count(),
            'issued'         => EcCertificate::where('status', 'active')->count(),
            'communicated'   => EcCertificate::whereNotNull('parivesh_app_no')->count(),
        ];

        $query = EcCertificate::with(['environmentProject.customer', 'customer'])
            ->latest();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('ec_ref_no', 'like', "%{$s}%")
                    ->orWhere('parivesh_app_no', 'like', "%{$s}%")
                    ->orWhere('applicant_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $certificates = $query->paginate(15)->withQueryString();

        return view('pages.ec_certificate.index', compact('certificates', 'kpis'));
    }

    /**
     * View authentic details of an already issued EC Certificate
     */
    public function show(int $id)
    {
        $certificate = EcCertificate::with(['environmentProject.customer', 'environmentProject.district', 'customer'])->findOrFail($id);
        return view('pages.ec_certificate.show', compact('certificate'));
    }

    /**
     * 6-Step Dynamic EC Issuance Wizard
     */
    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 6, 404);

        $draft = session('ec_wizard', []);

        // Compute highest unlocked step based on verified session progress
        $maxUnlockedStep = $this->getMaxUnlockedStep($draft);

        // Guard against deep-linking into future steps without completing prior steps
        if ($step > $maxUnlockedStep) {
            if ($maxUnlockedStep === 1) {
                return redirect()->route('ec-certificate.step', 1)
                    ->with('info', 'Please select a project and verify Step 1 parameters before proceeding.');
            }
            return redirect()->route('ec-certificate.step', $maxUnlockedStep)
                ->with('info', 'Please complete Step ' . ($maxUnlockedStep - 1) . ' before proceeding to Step ' . $step . '.');
        }

        $approvedProjects = EnvironmentProject::with(['customer', 'district'])
            ->latest()
            ->get();

        // Resolve active selected project
        $selectedProject = null;
        if ($request->filled('project_id')) {
            $selectedProject = EnvironmentProject::with(['customer', 'district'])->find($request->input('project_id'));
            if ($selectedProject) {
                $currentDraftProjId = $draft['step1']['environment_project_id'] ?? null;
                if ($currentDraftProjId != $selectedProject->id) {
                    $year = date('Y');
                    $nextCount = EcCertificate::count() + 1;
                    $ecRefNo = sprintf("SEIAA-TN/EC/%s/%04d", $year, $nextCount);
                    $pariveshNo = sprintf("SIA/TN/MIN/%d/%s", 10000 + $selectedProject->id, $year);
                    $applicantName = $selectedProject->customer?->company_name ?: ($selectedProject->customer?->customer_name ?: $selectedProject->contact_name);

                    // Cleanly reset entire draft state when switching project to prevent cross-project artifact leakage
                    $draft = [
                        'step1' => [
                            'environment_project_id' => $selectedProject->id,
                            'ec_ref_no'              => $ecRefNo,
                            'parivesh_app_no'        => $pariveshNo,
                            'applicant_name'         => $applicantName,
                            'issue_date'             => date('Y-m-d'),
                            'validity_years'         => 5,
                            'communication_type'     => 'Grant',
                            'conditions_summary'     => "Environmental Clearance granted subject to standard mining safeguards, strict air/water monitoring, greenbelt maintenance, and EMP compliance for {$selectedProject->project_name}.",
                            'completed'              => false,
                        ],
                    ];
                    session(['ec_wizard' => $draft]);
                }
            }
        } elseif (!empty($draft['step1']['environment_project_id'])) {
            $selectedProject = EnvironmentProject::with(['customer', 'district'])->find($draft['step1']['environment_project_id']);
        } elseif ($approvedProjects->isNotEmpty()) {
            $selectedProject = $approvedProjects->first();
        }

        // Initialize dynamic defaults for step 1 if not already populated
        if ($selectedProject && empty($draft['step1'])) {
            $year = date('Y');
            $nextCount = EcCertificate::count() + 1;
            $ecRefNo = sprintf("SEIAA-TN/EC/%s/%04d", $year, $nextCount);
            $pariveshNo = sprintf("SIA/TN/MIN/%d/%s", 10000 + $selectedProject->id, $year);
            $applicantName = $selectedProject->customer?->company_name ?: ($selectedProject->customer?->customer_name ?: $selectedProject->contact_name);

            $draft['step1'] = [
                'environment_project_id' => $selectedProject->id,
                'ec_ref_no'              => $ecRefNo,
                'parivesh_app_no'        => $pariveshNo,
                'applicant_name'         => $applicantName,
                'issue_date'             => date('Y-m-d'),
                'validity_years'         => 5,
                'communication_type'     => 'Grant',
                'conditions_summary'     => "Environmental Clearance granted subject to standard mining safeguards, strict air/water monitoring, greenbelt maintenance, and EMP compliance for {$selectedProject->project_name}.",
                'completed'              => false,
            ];
            session(['ec_wizard' => $draft]);
        }

        return view('pages.ec_certificate.wizard', compact('step', 'approvedProjects', 'selectedProject', 'draft', 'maxUnlockedStep'));
    }

    /**
     * Compute highest unlocked step based on verified session progress
     */
    protected function getMaxUnlockedStep(array $draft): int
    {
        $max = 1;
        if (!empty($draft['step1']['completed'])) {
            $max = 2;
            if (!empty($draft['step2'])) {
                $max = 3;
                if (!empty($draft['step3']['preview_verified'])) {
                    $max = 4;
                    if (!empty($draft['step4']['storage_confirmed'])) {
                        $max = 5;
                        if (!empty($draft['step5']['recipient_email'])) {
                            $max = 6;
                        }
                    }
                }
            }
        }
        return $max;
    }

    /**
     * Handle state persistence across wizard steps
     */
    public function saveStep(Request $request, int $step)
    {
        abort_unless($step >= 1 && $step <= 6, 404);

        $draft = session('ec_wizard', []);
        $maxUnlockedStep = $this->getMaxUnlockedStep($draft);

        // Guard against saving future steps if prior prerequisite steps are uncompleted
        if ($step > $maxUnlockedStep) {
            if ($maxUnlockedStep === 1) {
                return redirect()->route('ec-certificate.step', 1)
                    ->with('info', 'Please select a project and verify Step 1 parameters before proceeding.');
            }
            return redirect()->route('ec-certificate.step', $maxUnlockedStep)
                ->with('info', 'Please complete Step ' . ($maxUnlockedStep - 1) . ' before proceeding to Step ' . $step . '.');
        }

        switch ($step) {
            case 1:
                $validated = $request->validate([
                    'environment_project_id' => 'required|exists:environment_projects,id',
                    'ec_ref_no'              => 'required|string|max:100',
                    'parivesh_app_no'        => 'nullable|string|max:100',
                    'applicant_name'         => 'required|string|max:255',
                    'issue_date'             => 'required|date',
                    'validity_years'         => 'nullable|integer|min:1|max:30',
                    'communication_type'     => 'required|in:Grant,Rejection,ToR',
                    'conditions_summary'     => 'nullable|string|max:2000',
                ]);

                if (empty($validated['validity_years'])) {
                    $validated['validity_years'] = 5;
                }

                // If project changed, purge downstream draft artifacts to prevent cross-project leakage
                $prevProjId = $draft['step1']['environment_project_id'] ?? null;
                if ($prevProjId && $prevProjId != $validated['environment_project_id']) {
                    unset($draft['step2'], $draft['step3'], $draft['step4'], $draft['step5']);
                }

                $validated['completed'] = true;
                $draft['step1'] = $validated;
                session(['ec_wizard' => $draft]);
                return redirect()->route('ec-certificate.step', 2)
                    ->with('success', 'Step 1 saved. Please upload or attach the EC Certificate PDF.');

            case 2:
                $request->validate([
                    'certificate_file' => 'nullable|file|mimes:pdf,docx,jpg,png|max:25600',
                ]);

                if ($request->hasFile('certificate_file')) {
                    $file = $request->file('certificate_file');
                    $projId = $draft['step1']['environment_project_id'] ?? 1;
                    $proj = EnvironmentProject::find($projId);
                    $projCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($proj?->project_code ?: 'ENV-GENERAL'));
                    $projCode = trim($projCode, '_') ?: 'ENV-GENERAL';

                    $safeName = 'EC_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
                    $subDir = 'uploads/ec_certificates/' . $projCode;
                    $destPath = public_path($subDir);

                    try {
                        File::ensureDirectoryExists($destPath, 0755, true);

                        // Remove old draft file if replacing
                        if (!empty($draft['step2']['file_path']) && file_exists(public_path($draft['step2']['file_path']))) {
                            @unlink(public_path($draft['step2']['file_path']));
                        }

                        $file->move($destPath, $safeName);

                        $draft['step2'] = [
                            'file_path' => $subDir . '/' . $safeName,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => filesize($destPath . '/' . $safeName),
                            'uploaded_at' => now()->toDateTimeString(),
                        ];
                    } catch (\Throwable $e) {
                        Log::error('EC Certificate file upload failed: ' . $e->getMessage(), ['exception' => $e]);
                        return redirect()->route('ec-certificate.step', 2)
                            ->with('error', 'Failed to upload certificate file. Please verify folder permissions and try again.');
                    }
                } elseif (empty($draft['step2'])) {
                    // Mark as pending attachment if not uploaded yet
                    $draft['step2'] = [
                        'file_path' => null,
                        'file_name' => 'Auto-generated Digital Clearance Notice.pdf',
                        'file_size' => 124800,
                        'uploaded_at' => now()->toDateTimeString(),
                    ];
                }

                session(['ec_wizard' => $draft]);
                return redirect()->route('ec-certificate.step', 3)
                    ->with('success', 'Step 2 saved. Preview the generated certificate.');

            case 3:
                $draft['step3'] = [
                    'preview_verified' => true,
                    'verified_at'      => now()->toDateTimeString(),
                ];
                session(['ec_wizard' => $draft]);
                return redirect()->route('ec-certificate.step', 4)
                    ->with('success', 'Step 3 verified. Review document storage repository.');

            case 4:
                $draft['step4'] = [
                    'storage_confirmed' => true,
                    'primary_folder'    => 'EC Certificate & Statutory Grants',
                    'secondary_folder'  => 'Final EIA & Baseline Documentation',
                ];
                session(['ec_wizard' => $draft]);
                return redirect()->route('ec-certificate.step', 5)
                    ->with('success', 'Step 4 saved. Configure communication dispatch.');

            case 5:
                $validated = $request->validate([
                    'communication_type' => 'required|in:Grant,Rejection,ToR',
                    'recipient_email'    => 'required|email|max:255',
                    'recipient_phone'    => 'nullable|string|max:20',
                    'communication_note' => 'nullable|string|max:1000',
                    'send_sms_alert'     => 'nullable|boolean',
                ]);

                $draft['step5'] = $validated;
                session(['ec_wizard' => $draft]);
                return redirect()->route('ec-certificate.step', 6)
                    ->with('success', 'Step 5 saved. Final verification before issuance.');

            case 6:
                // Final submission from Step 6 preview
                return $this->store($request);
        }

        return redirect()->route('ec-certificate.step', 1);
    }

    /**
     * Finalize and Commit EC Certificate to Database
     */
    public function store(Request $request)
    {
        $draft = session('ec_wizard', []);
        $step1 = $draft['step1'] ?? [];

        // Support direct POST request if fields are submitted in request body
        $projectId = $request->input('environment_project_id', $step1['environment_project_id'] ?? null);
        $ecRefNo = $request->input('ec_ref_no', $step1['ec_ref_no'] ?? null);
        $pariveshNo = $request->input('parivesh_app_no', $step1['parivesh_app_no'] ?? null);
        $applicantName = $request->input('applicant_name', $step1['applicant_name'] ?? null);
        $issueDate = $request->input('issue_date', $step1['issue_date'] ?? date('Y-m-d'));
        $rawValidityYears = $request->input('validity_years', $step1['validity_years'] ?? 5);
        $validityYears = is_numeric($rawValidityYears) ? (int) $rawValidityYears : 5;
        if ($validityYears < 1 || $validityYears > 30) {
            $validityYears = 5;
        }
        $communicationType = $request->input('communication_type', $step1['communication_type'] ?? 'Grant');
        if (!in_array($communicationType, ['Grant', 'Rejection', 'ToR'])) {
            $communicationType = 'Grant';
        }
        $conditionsSummary = $request->input('conditions_summary', $step1['conditions_summary'] ?? 'Environmental clearance granted subject to standard environmental management safeguards.');

        if (!$projectId || !$ecRefNo || !$applicantName) {
            return redirect()->route('ec-certificate.step', 1)
                ->with('error', 'Missing mandatory certificate parameters or draft session expired. Please verify Step 1.');
        }

        $project = EnvironmentProject::find($projectId);
        if (!$project) {
            return redirect()->route('ec-certificate.step', 1)
                ->with('error', 'Selected environment project could not be found. Please select a valid project.');
        }

        if ($request->hasFile('certificate_file')) {
            $request->validate([
                'certificate_file' => 'nullable|file|mimes:pdf,docx,jpg,png|max:25600',
            ]);
        }

        try {
            return DB::transaction(function () use (
                $request,
                $project,
                $ecRefNo,
                $pariveshNo,
                $applicantName,
                $issueDate,
                $validityYears,
                $communicationType,
                $conditionsSummary,
                $draft
            ) {
                try {
                    $parsedIssueDate = Carbon::parse($issueDate);
                } catch (\Exception $e) {
                    $parsedIssueDate = Carbon::now();
                    $issueDate = $parsedIssueDate->toDateString();
                }
                $expiryDate = (clone $parsedIssueDate)->addYears($validityYears);

                $filePath = $draft['step2']['file_path'] ?? null;
                if ($request->hasFile('certificate_file')) {
                    $file = $request->file('certificate_file');
                    $safeName = 'EC_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file->getClientOriginalName());
                    $projCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) ($project->project_code ?: 'ENV-GENERAL'));
                    $projCode = trim($projCode, '_') ?: 'ENV-GENERAL';
                    $subDir = 'uploads/ec_certificates/' . $projCode;
                    $destPath = public_path($subDir);

                    try {
                        File::ensureDirectoryExists($destPath, 0755, true);

                        // Remove previous draft file if superseded
                        if ($filePath && file_exists(public_path($filePath))) {
                            @unlink(public_path($filePath));
                        }

                        $file->move($destPath, $safeName);
                        $filePath = $subDir . '/' . $safeName;
                    } catch (\Throwable $e) {
                        Log::error('EC Certificate direct store upload failed: ' . $e->getMessage(), ['exception' => $e]);
                        throw $e;
                    }
                }

                // Truncate strings safely to schema limits
                $ecRefNo = substr(trim((string) $ecRefNo), 0, 100);
                $pariveshNo = $pariveshNo ? substr(trim((string) $pariveshNo), 0, 100) : null;
                $applicantName = substr(trim((string) $applicantName), 0, 255);
                $conditionsSummary = $conditionsSummary ? substr(trim((string) $conditionsSummary), 0, 2000) : null;

                // Upsert or create certificate record
                $certificate = EcCertificate::updateOrCreate(
                    ['ec_ref_no' => $ecRefNo],
                    [
                        'environment_project_id' => $project->id,
                        'customer_id'            => $project->customer_id,
                        'lease_application_id'   => $project->lease_application_id,
                        'parivesh_app_no'        => $pariveshNo ?: ('SIA/TN/MIN/' . (10000 + $project->id) . '/' . date('Y')),
                        'applicant_name'         => $applicantName,
                        'issue_date'             => $issueDate,
                        'expiry_date'            => $expiryDate,
                        'validity_years'         => $validityYears,
                        'communication_type'     => $communicationType,
                        'certificate_file'       => $filePath,
                        'conditions_summary'     => $conditionsSummary,
                        'status'                 => 'active',
                        'created_by'             => Auth::id() ?? 1,
                    ]
                );

                // Promote project status to approved / reported
                $project->update(['status' => 'approved']);

                ActivityLog::create([
                    'loggable_type' => EnvironmentProject::class,
                    'loggable_id'   => $project->id,
                    'user_id'       => Auth::id() ?? 1,
                    'action'        => 'EC Certificate Issued',
                    'description'   => "Environmental Clearance Certificate '{$certificate->ec_ref_no}' issued to {$certificate->applicant_name}.",
                ]);

                // Clear wizard session
                session()->forget('ec_wizard');

                return redirect()->route('ec-certificate.index')
                    ->with('success', "EC Certificate '{$certificate->ec_ref_no}' issued successfully for {$certificate->applicant_name}!");
            });
        } catch (\Throwable $e) {
            Log::error('Failed to issue EC Certificate: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('ec-certificate.step', 6)
                ->with('error', 'Failed to issue EC Certificate: ' . $e->getMessage());
        }
    }
}
