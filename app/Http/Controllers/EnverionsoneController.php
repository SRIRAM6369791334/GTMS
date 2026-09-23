<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\District;
use App\Models\DocumentField;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use App\Models\PptApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EnverionsoneController extends Controller
{
    // ─── Module ID for Environment folders ────────────────────────────────
    const ENV_MODULE_ID = 3;

    /**
     * UNIFIED Landing Page — lists all Environment Clearance projects (B1 + B2)
     * with real dynamic KPI cards from DB (no static numbers).
     */
    public function index(Request $request)
    {
        $allProjectIds = EnvironmentProject::pluck('id');

        // ── Dynamic KPIs ──────────────────────────────────────────────────
        $kpis = [
            'total'        => EnvironmentProject::count(),
            'b1_count'     => EnvironmentProject::where('category', 'B1')->count(),
            'b2_count'     => EnvironmentProject::where('category', 'B2')->count(),
            'approved'     => EnvironmentProject::where('status', 'approved')->count(),
            'in_progress'  => EnvironmentProject::whereIn('status', ['draft', 'validation'])->count(),
            'doc_uploaded' => EnvironmentDocument::whereIn('environment_project_id', $allProjectIds)
                ->whereNotNull('file_path')->count(),
            'doc_approved' => EnvironmentDocument::whereIn('environment_project_id', $allProjectIds)
                ->where('status', 'approved')->count(),
            'ec_issued'    => \App\Models\EcCertificate::count(),
        ];

        $query = EnvironmentProject::with(['customer', 'district', 'documents', 'ecCertificates'])->latest();

        if ($request->filled('category') && in_array($request->category, ['B1', 'B2'])) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('project_code', 'like', "%{$s}%")
                  ->orWhere('project_name', 'like', "%{$s}%")
                  ->orWhereHas('customer', function($cq) use ($s) {
                      $cq->where('customer_name', 'like', "%{$s}%")
                         ->orWhere('company_name', 'like', "%{$s}%")
                         ->orWhere('mimas_no', 'like', "%{$s}%");
                  });
            });
        }

        $allProjects = $query->paginate(20)->withQueryString();

        $recentActivities = ActivityLog::where('loggable_type', EnvironmentProject::class)
            ->latest()
            ->take(6)
            ->get();

        return view('pages.eviron.index', compact('kpis', 'allProjects', 'recentActivities'));
    }

    /**
     * Category Selection Wizard (GET) — Step 1: Pick B1/B2, Step 2: Project Details
     */
    public function create(Request $request)
    {
        $step     = (int) $request->query('step', 1);
        $category = $request->query('category', null);
        $subCat   = $request->query('sub_category', null);

        $customers = Customer::orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();

        return view('pages.eviron.create', compact('step', 'category', 'subCat', 'customers', 'districts'));
    }

    /**
     * Store new Environment Project (POST) — handles B1-SC1, B1-SC2, B2
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'      => 'required|in:B1,B2',
            'sub_category'  => 'nullable|in:SC1,SC2',
            'customer_id'   => 'nullable|integer|exists:customers,id',
            'client_name'   => 'required|string|max:255',
            'company_name'  => 'nullable|string|max:255',
            'project_name'  => 'required|string|max:255',
            'district_id'   => 'required|exists:districts,id',
            'location'      => 'nullable|string|max:255',
            'contact_name'  => 'nullable|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'mimas_no'      => 'nullable|string|max:50',
        ]);

        // B1 Category defaults to Sub Category 1 (SC1)
        if ($validated['category'] === 'B1') {
            $validated['sub_category'] = $validated['sub_category'] ?: 'SC1';
        }

        return DB::transaction(function () use ($validated, $request) {
            $year = date('Y');
            $cat  = $validated['category'];
            $sc   = ($cat === 'B1') ? ($validated['sub_category'] ?? 'SC1') : null;

            // Generate unique project code
            $prefix      = "ENV-{$cat}-{$year}-";
            $lastProject = EnvironmentProject::where('project_code', 'like', $prefix . '%')
                ->withTrashed()
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING_INDEX(project_code, '-', -1) AS UNSIGNED) DESC")
                ->first();
            $seq  = $lastProject ? (((int) substr($lastProject->project_code, -4)) + 1) : 1;
            $code = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

            // Resolve or link customer
            $customerId = $validated['customer_id'];
            if (!$customerId) {
                $customer = Customer::withTrashed()
                    ->where('customer_name', $validated['client_name'])->first();
                if ($customer) {
                    if ($customer->trashed()) $customer->restore();
                    $customerId = $customer->id;
                } else {
                    $customer = Customer::create([
                        'customer_name' => $validated['client_name'],
                        'company_name'  => $validated['company_name'] ?? null,
                        'mobile_num'    => $validated['contact_phone'],
                        'email'         => $validated['contact_email'] ?? null,
                        'mimas_no'      => $validated['mimas_no'] ?? null,
                        'branch_id'     => auth()->user()->branch_id ?? 1,
                    ]);
                    $customerId = $customer->id;
                }
            }

            $pv = (float) $request->input('product_value', 0);
            $pa = (float) $request->input('paid_amount', 0);
            $pe = max(0, $pv - $pa);
            $status = $request->input('payment_status', ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial')));

            // Create environment project
            $project = EnvironmentProject::create([
                'project_code'   => $code,
                'customer_id'    => $customerId,
                'category'       => $cat,
                'sub_category'   => $sc,
                'b1_stage'       => ($cat === 'B1') ? 'sc1_prep' : null,
                'project_name'   => $validated['project_name'],
                'district_id'    => $validated['district_id'],
                'location'       => $validated['location'] ?? null,
                'contact_name'   => $validated['contact_name'] ?? $validated['client_name'],
                'contact_phone'  => $validated['contact_phone'],
                'contact_email'  => $validated['contact_email'] ?? null,
                'product_value'  => $pv,
                'paid_amount'    => $pa,
                'pending_amount' => $pe,
                'payment_status' => $status,
                'status'         => 'draft',
                'branch_id'      => auth()->user()->branch_id ?? 1,
                'created_by'     => Auth::id(),
            ]);

            // Save Application Handlers
            if ($request->has('handlers') && is_array($request->input('handlers'))) {
                foreach ($request->input('handlers') as $idx => $h) {
                    $name = trim($h['person_name'] ?? ($h['name'] ?? ''));
                    if (!empty($name)) {
                        ApplicationHandler::create([
                            'application_type' => 'environment',
                            'application_id'   => $project->id,
                            'handlerable_type' => EnvironmentProject::class,
                            'handlerable_id'   => $project->id,
                            'name'             => $name,
                            'role'             => $h['role'] ?? 'Field Officer',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $idx + 1,
                        ]);
                    }
                }
            }

            // Save Application Payment
            ApplicationPayment::create([
                'application_type' => 'environment',
                'application_id'   => $project->id,
                'payable_type'     => EnvironmentProject::class,
                'payable_id'       => $project->id,
                'product_value'    => $pv,
                'paid_amount'      => $pa,
                'pending_amount'   => $pe,
                'payment_status'   => $status,
                'notes'            => $request->input('payment_notes', 'Initial Environment Clearance registration fee settlement'),
            ]);

            // Auto-generate document checklist slots for this category
            $this->autoGenerateDocumentSlots($project);

            ActivityLog::create([
                'loggable_type' => EnvironmentProject::class,
                'loggable_id'   => $project->id,
                'action'        => 'project_created',
                'description'   => "Environment Clearance project {$code} ({$cat}" . ($sc ? "/{$sc}" : '') . ") created.",
                'user_id'       => Auth::id(),
            ]);

            return redirect()->route('eviron.show', $project->id)
                ->with('success', "Project {$code} created successfully! You can now upload documents.");
        });
    }

    /**
     * UNIFIED Project Show — dynamic folder tabs based on category + sub_category.
     * Works for B1/SC1, B1/SC2, and B2.
     */
    public function show(int $id)
    {
        $project = EnvironmentProject::with(['customer', 'district', 'ecCertificates', 'handlers', 'payments', 'pptStage1', 'pptStage2', 'pptApplications'])->findOrFail($id);

        // Auto-generate document slots if legacy project has zero slots
        if ($project->documents()->count() === 0) {
            $this->autoGenerateDocumentSlots($project);
        }

        // Get folder names for this project type
        $folderNames = $project->folder_names;

        $folders = Folder::where('module_id', self::ENV_MODULE_ID)
            ->whereIn('name', $folderNames)
            ->orderBy('sort_order')
            ->get();

        // Load documents grouped by folder
        $documentsByFolder = [];
        foreach ($folders as $folder) {
            $documentsByFolder[$folder->id] = EnvironmentDocument::where('environment_project_id', $project->id)
                ->where('folder_id', $folder->id)
                ->orderBy('id')
                ->get();
        }

        // Process flow stage — based on project status
        $processStage = match ($project->status) {
            'draft'      => 1,
            'validation' => 2,
            'approved'   => 3,
            'reported'   => 4,
            'archived'   => 5,
            default      => 1,
        };

        // All projects for switcher dropdown
        $allProjects = EnvironmentProject::with('customer')->latest()->get();

        return view('pages.eviron.show', compact(
            'project', 'folders', 'documentsByFolder', 'processStage', 'allProjects'
        ));
    }

    /**
     * Upload a document for an eviron project.
     */
    public function uploadDocument(Request $request, int $id, int $document)
    {
        $request->validate(['file' => 'required|file|max:25600']);

        $doc     = EnvironmentDocument::where('environment_project_id', $id)->findOrFail($document);
        $project = EnvironmentProject::findOrFail($id);

        $file     = $request->file('file');
        $dir      = "environment/{$project->project_code}";
        $filename = time() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs($dir, $filename, 'public');

        $doc->update([
            'file_name'   => $file->getClientOriginalName(),
            'file_path'   => $path,
            'file_type'   => $file->extension(),
            'file_size'   => $file->getSize(),
            'status'      => 'uploaded',
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        return back()->with('success', "'{$doc->document_name}' uploaded successfully.");
    }

    /**
     * Add and upload a custom document to an environment project folder.
     */
    public function addDocument(Request $request, int $id)
    {
        $request->validate([
            'folder_id'     => 'required|integer',
            'document_name' => 'required|string|max:255',
            'file'          => 'required|file|max:25600',
        ]);

        $project  = EnvironmentProject::findOrFail($id);
        $file     = $request->file('file');
        $dir      = "environment/{$project->project_code}";
        $filename = time() . '_' . $file->getClientOriginalName();
        $path     = $file->storeAs($dir, $filename, 'public');

        EnvironmentDocument::create([
            'environment_project_id' => $id,
            'folder_id'              => $request->folder_id,
            'document_field_id'      => null,
            'document_name'          => $request->document_name,
            'file_name'              => $file->getClientOriginalName(),
            'file_path'              => $path,
            'file_type'              => $file->extension(),
            'file_size'              => $file->getSize(),
            'status'                 => 'uploaded',
            'uploaded_by'            => Auth::id(),
            'uploaded_at'            => now(),
        ]);

        return back()->with('success', "'{$request->document_name}' added and uploaded successfully.");
    }

    /**
     * Review a document (approve / request revision).
     */
    public function reviewDocument(Request $request, int $id, int $document)
    {
        $request->validate([
            'status'      => 'required|in:approved,revision_required',
            'review_note' => 'nullable|string|max:500',
        ]);

        $doc = EnvironmentDocument::where('environment_project_id', $id)->findOrFail($document);

        $doc->update([
            'status'      => $request->status,
            'review_note' => $request->review_note,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Document status updated successfully.');
    }

    /**
     * Update overall project status (validation → approved → reported).
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:draft,validation,approved,reported,archived']);

        $project = EnvironmentProject::findOrFail($id);
        $project->update(['status' => $request->status]);

        // Auto-upgrade uploaded documents to validated when approving
        if ($request->status === 'approved') {
            EnvironmentDocument::where('environment_project_id', $id)
                ->where('status', 'uploaded')
                ->update(['status' => 'validated']);
        }

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'action'        => 'status_updated',
            'description'   => "Project status changed to {$request->status}.",
            'user_id'       => Auth::id(),
        ]);

        return back()->with('success', "Project status updated to '{$request->status}' successfully.");
    }

    /**
     * Download a document file.
     */
    public function downloadDocument(int $document)
    {
        $doc = EnvironmentDocument::findOrFail($document);
        abort_unless($doc->file_path, 404, 'File path not recorded.');

        if (!Storage::disk('public')->exists($doc->file_path)) {
            return back()->with('error', "The requested file '{$doc->file_name}' does not exist on disk.");
        }

        return Storage::disk('public')->download($doc->file_path, $doc->file_name ?? 'document');
    }

    // ─── BACKWARD COMPATIBLE: B1 Sub Category 1 view ─────────────────────
    /**
     * Sub Category 1 (Site & Mining Documentation — 5 Folders)
     * Now: if project_id given → redirect to unified show page
     *       if no project → redirect to create wizard
     */
    public function index1(Request $request)
    {
        $projectId = $request->query('project_id');

        if ($projectId) {
            $project = EnvironmentProject::find($projectId);
            if ($project) {
                return redirect()->route('eviron.show', $project->id);
            }
        }

        // No project id → pick first B1/SC1 project or redirect to create
        $project = EnvironmentProject::where('category', 'B1')
            ->where('sub_category', 'SC1')
            ->latest()->first();

        if (!$project) {
            return redirect()->route('eviron.create', ['category' => 'B1', 'sub_category' => 'SC1'])
                ->with('info', 'No B1 Sub Category 1 application found. Please create a new one.');
        }

        return redirect()->route('eviron.show', $project->id);
    }

    // ─── BACKWARD COMPATIBLE: B1 Sub Category 2 view ─────────────────────
    /**
     * Sub Category 2 (EIA & TNPCB Submission — 6 Folders)
     * Now: redirects to unified show page.
     */
    public function index2(Request $request)
    {
        $projectId = $request->query('project_id');

        if ($projectId) {
            $project = EnvironmentProject::find($projectId);
            if ($project) {
                return redirect()->route('eviron.show', $project->id);
            }
        }

        $project = EnvironmentProject::where('category', 'B1')
            ->where('sub_category', 'SC2')
            ->latest()->first();

        if (!$project) {
            return redirect()->route('eviron.create', ['category' => 'B1', 'sub_category' => 'SC2'])
                ->with('info', 'No B1 Sub Category 2 application found. Please create a new one.');
        }

        return redirect()->route('eviron.show', $project->id);
    }

    /**
     * Submit Sub Category 1 (SC1) to PPT Department for Stage 1 ToR Presentation.
     */
    public function submitSc1ToPpt(int $id)
    {
        $project = EnvironmentProject::findOrFail($id);
        abort_unless($project->category === 'B1', 400, 'Only Category B1 applications require PPT appraisal.');

        $ppt = $project->pptStage1;
        if (!$ppt) {
            $count = PptApplication::count() + 1;
            $appNo = sprintf('PPT-%s-%04d', date('Y'), $count);

            $ppt = PptApplication::create([
                'application_no'         => $appNo,
                'customer_id'            => $project->customer_id,
                'environment_project_id' => $project->id,
                'presentation_stage'     => 'tor_presentation',
                'project_name'           => $project->project_name . ' (Stage 1: ToR Presentation)',
                'district_id'            => $project->district_id,
                'taluk_village'          => $project->location ?? 'Quarry Site',
                'mineral_id'             => 1,
                'status'                 => 'agenda_scheduled',
                'product_value'          => 25000.00,
                'paid_amount'            => 25000.00,
                'pending_amount'         => 0.00,
                'payment_status'         => 'paid',
                'branch_id'              => $project->branch_id ?? 1,
                'created_by'             => Auth::id(),
            ]);

            ApplicationHandler::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'name'             => 'Dr. K. Ravichandran',
                'role'             => 'Lead Technical Consultant',
                'notes'            => 'SEAC ToR presentation defense',
                'sort_order'       => 1,
            ]);
        }

        $project->update([
            'ppt_stage_1_id' => $ppt->id,
            'b1_stage'       => 'sc1_ppt_review',
            'status'         => 'validation',
        ]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'action'        => 'sc1_submitted_to_ppt',
            'description'   => "Sub Category 1 completed and submitted to PPT Department for ToR Presentation ({$ppt->application_no}).",
            'user_id'       => Auth::id(),
        ]);

        return redirect()->route('eviron.show', $project->id)
            ->with('success', "Stage 1 (SC1) submitted to PPT Department ({$ppt->application_no})! Awaiting SEAC ToR Presentation Approval.");
    }

    /**
     * Submit Sub Category 2 (SC2) to PPT Department for Stage 2 Final EC Presentation.
     */
    public function submitSc2ToPpt(int $id)
    {
        $project = EnvironmentProject::findOrFail($id);
        abort_unless($project->category === 'B1' && $project->sub_category === 'SC2', 400, 'Project must be in Sub Category 2.');

        $ppt = $project->pptStage2;
        if (!$ppt) {
            $count = PptApplication::count() + 1;
            $appNo = sprintf('PPT-%s-%04d', date('Y'), $count);

            $ppt = PptApplication::create([
                'application_no'         => $appNo,
                'customer_id'            => $project->customer_id,
                'environment_project_id' => $project->id,
                'presentation_stage'     => 'final_ec_presentation',
                'project_name'           => $project->project_name . ' (Stage 2: Final EC Presentation)',
                'district_id'            => $project->district_id,
                'taluk_village'          => $project->location ?? 'Quarry Site',
                'mineral_id'             => 1,
                'status'                 => 'agenda_scheduled',
                'product_value'          => 25000.00,
                'paid_amount'            => 25000.00,
                'pending_amount'         => 0.00,
                'payment_status'         => 'paid',
                'branch_id'              => $project->branch_id ?? 1,
                'created_by'             => Auth::id(),
            ]);

            ApplicationHandler::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'name'             => 'Dr. K. Ravichandran',
                'role'             => 'Lead Technical Consultant',
                'notes'            => 'Final SEAC / SEIAA EC presentation defense',
                'sort_order'       => 1,
            ]);
        }

        $project->update([
            'ppt_stage_2_id' => $ppt->id,
            'b1_stage'       => 'sc2_ppt_review',
            'status'         => 'validation',
        ]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'action'        => 'sc2_submitted_to_ppt',
            'description'   => "Sub Category 2 completed and submitted to PPT Department for Final EC Presentation ({$ppt->application_no}).",
            'user_id'       => Auth::id(),
        ]);

        return redirect()->route('eviron.show', $project->id)
            ->with('success', "Stage 2 (SC2) submitted to PPT Department ({$ppt->application_no})! Awaiting SEAC/SEIAA Final EC Appraisal.");
    }

    // ─── DOCUMENT SLOTS GENERATOR ────────────────────────────────────────

    /**
     * Auto-generate EnvironmentDocument slots for every document_field
     * belonging to the correct folders for this project's category + sub_category.
     */
    public function autoGenerateDocumentSlots(EnvironmentProject $project): void
    {
        $folderNames = $project->folder_names;

        $folders = Folder::where('module_id', self::ENV_MODULE_ID)
            ->whereIn('name', $folderNames)
            ->get();

        foreach ($folders as $folder) {
            $fields = DocumentField::where('folder_id', $folder->id)
                ->where('status', 1)
                ->orderBy('sort_order')
                ->get();

            foreach ($fields as $field) {
                // Avoid duplicates
                $exists = EnvironmentDocument::where('environment_project_id', $project->id)
                    ->where('folder_id', $folder->id)
                    ->where('document_field_id', $field->id)
                    ->exists();

                if (!$exists) {
                    EnvironmentDocument::create([
                        'environment_project_id' => $project->id,
                        'folder_id'              => $folder->id,
                        'document_field_id'      => $field->id,
                        'document_name'          => $field->name,
                        'status'                 => 'pending',
                    ]);
                }
            }
        }
    }

    public static function generateSlots(EnvironmentProject $project): void
    {
        (new self())->autoGenerateDocumentSlots($project);
    }
}
