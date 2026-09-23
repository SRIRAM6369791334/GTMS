<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\District;
use App\Models\DocumentField;
use App\Models\EnvironmentDocument;
use App\Models\EnvironmentProject;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnvironmentalB2Controller extends Controller
{
    /**
     * Display listing of B2 Clearance Applications with real KPI metrics
     */
    public function index()
    {
        $kpis = [
            'total'      => EnvironmentProject::where('category', 'B2')->count(),
            'validation' => EnvironmentProject::where('category', 'B2')->where('status', 'validation')->count(),
            'approved'   => EnvironmentProject::where('category', 'B2')->where('status', 'approved')->count(),
            'archived'   => EnvironmentProject::where('category', 'B2')->where('status', 'archived')->count(),
        ];

        $applications = EnvironmentProject::with(['customer', 'district', 'documents.folder'])
            ->where('category', 'B2')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.enviro_b2.index', compact('applications', 'kpis'));
    }

    /**
     * Display Step-by-step Wizard
     */
    public function wizard(int $step)
    {
        abort_unless($step >= 1 && $step <= 9, 404);

        $customers = Customer::orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();
        $latestProject = EnvironmentProject::where('category', 'B2')->latest()->first();

        return view('pages.enviro_b2.wizard', compact('step', 'customers', 'districts', 'latestProject'));
    }

    /**
     * Store newly initiated B2 Project and auto-generate 6-folder document checklist
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'              => 'nullable|integer',
            'client_name'              => 'required|string|max:255',
            'company_name'             => 'nullable|string|max:255',
            'project_name'             => 'required|string|max:255',
            'district_id'              => 'required|exists:districts,id',
            'location'                 => 'nullable|string|max:255',
            'contact_name'             => 'nullable|string|max:255',
            'contact_phone'            => 'required|string|max:20',
            'contact_email'            => 'nullable|email|max:255',
            'mimas_no'                 => 'nullable|string|max:50',
            'aadhaar_no'               => 'nullable|string|max:14',
        ]);

        $project = DB::transaction(function () use ($validated, $request) {
            // 1. Resolve or Create Customer (Lease / Mining Pattern)
            $customer = null;
            if (!empty($validated['customer_id'])) {
                $customer = Customer::withTrashed()->find($validated['customer_id']);
            }
            if (!$customer && !empty($validated['mimas_no'])) {
                $customer = Customer::withTrashed()->where('mimas_no', $validated['mimas_no'])->first();
            }
            if (!$customer && !empty($validated['aadhaar_no'])) {
                $customer = Customer::withTrashed()->where('aadhaar_no', $validated['aadhaar_no'])->first();
            }

            if ($customer) {
                if ($customer->trashed()) {
                    $customer->restore();
                }
            } else {
                $customer = Customer::create([
                    'customer_name' => $validated['client_name'],
                    'company_name'  => (!empty($validated['company_name'])) ? $validated['company_name'] : ($validated['client_name'] . ' Quarry'),
                    'mimas_no'      => (!empty($validated['mimas_no'])) ? $validated['mimas_no'] : ('TN-MMS-' . strtoupper(substr(md5(uniqid()), 0, 6))),
                    'mobile_num'    => $validated['contact_phone'],
                    'email'         => $validated['contact_email'] ?? null,
                    'district_id'   => $validated['district_id'],
                    'address'       => $validated['location'] ?? null,
                    'status'        => 1,
                    'created_by'    => Auth::id() ?? 1,
                ]);
            }

            // 2. Generate concurrency-safe atomic project code
            $year = date('Y');
            $maxProj = DB::table('environment_projects')
                ->where('project_code', 'like', "ENV-B2-{$year}-%")
                ->whereNull('deleted_at')
                ->orderByRaw("CAST(SUBSTRING_INDEX(project_code, '-', -1) AS UNSIGNED) DESC")
                ->lockForUpdate()
                ->first();

            $nextSeq = 1;
            if ($maxProj && preg_match('/-(\d+)$/', $maxProj->project_code, $matches)) {
                $nextSeq = (int)$matches[1] + 1;
            }
            $projectCode = sprintf("ENV-B2-%s-%04d", $year, $nextSeq);

            // 3. Create Environment Project Record
            $project = EnvironmentProject::create([
                'project_code'  => $projectCode,
                'customer_id'   => $customer->id,
                'district_id'   => $validated['district_id'],
                'category'      => 'B2',
                'project_name'  => $validated['project_name'],
                'location'      => $validated['location'] ?? $customer->address,
                'contact_name'  => $validated['contact_name'] ?? $customer->customer_name,
                'contact_phone' => $validated['contact_phone'] ?? $customer->mobile_num,
                'contact_email' => $validated['contact_email'] ?? $customer->email,
                'status'        => 'draft',
                'branch_id'     => Auth::user()?->branch_id ?? 1,
                'created_by'    => Auth::id() ?? 1,
            ]);

            // 4. Auto-generate B2 Document Checklist across 6 folders
            $b2FolderIds = DB::table('folders')
                ->where('module_id', 3)
                ->where('sort_order', '<=', 6)
                ->orderBy('sort_order')
                ->pluck('id');

            $fields = DocumentField::whereIn('folder_id', $b2FolderIds)
                ->where('status', 1)
                ->orderBy('folder_id')
                ->orderBy('sort_order')
                ->get();

            foreach ($fields as $field) {
                EnvironmentDocument::create([
                    'environment_project_id' => $project->id,
                    'folder_id'              => $field->folder_id,
                    'document_field_id'      => $field->id,
                    'document_name'          => $field->name,
                    'status'                 => 'pending',
                ]);
            }

            // 5. Activity Log
            ActivityLog::create([
                'loggable_type' => EnvironmentProject::class,
                'loggable_id'   => $project->id,
                'user_id'       => Auth::id() ?? 1,
                'action'        => 'Project Created',
                'description'   => "B2 Environment Clearance project {$projectCode} created with 6 folders and checklist.",
            ]);

            return $project;
        });

        return redirect()->route('environment-b2.show', $project)
            ->with('success', "Environment Clearance B2 project '{$project->project_code}' and document checklist created successfully!");
    }

    /**
     * Display B2 Project Dossier & Folder Management View
     */
    public function show(EnvironmentProject $project)
    {
        $project->load(['customer', 'district', 'documents.folder']);

        // Group documents by folder name
        $folders = $project->documents->groupBy(function ($doc) {
            return $doc->folder?->name ?? 'General Documents';
        });

        $summary = [
            'total'    => $project->documents->count(),
            'uploaded' => $project->documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count(),
            'approved' => $project->documents->where('status', 'approved')->count(),
        ];

        $activities = ActivityLog::where('loggable_type', EnvironmentProject::class)
            ->where('loggable_id', $project->id)
            ->latest()
            ->get();

        return view('pages.enviro_b2.show', compact('project', 'folders', 'summary', 'activities'));
    }

    /**
     * Upload real document file against checklist item
     */
    public function upload(Request $request, EnvironmentDocument $document)
    {
        $request->validate([
            'file' => 'required|file|max:25600|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,zip,kml,kmz,dwg',
        ]);

        $project = $document->environmentProject;
        $file = $request->file('file');
        $origName = $file->getClientOriginalName();
        $safeName = time() . '_' . $document->id . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);

        $subDir = 'uploads/environment-b2/' . $project->project_code;
        $destPath = public_path($subDir);

        if (!file_exists($destPath)) {
            mkdir($destPath, 0777, true);
        }

        // Delete previous file if replacing
        if ($document->file_path && file_exists(public_path($document->file_path))) {
            @unlink(public_path($document->file_path));
        }

        $file->move($destPath, $safeName);

        $document->update([
            'file_name'   => $origName,
            'file_path'   => $subDir . '/' . $safeName,
            'file_type'   => $file->getClientOriginalExtension(),
            'file_size'   => filesize($destPath . '/' . $safeName),
            'status'      => 'uploaded',
            'review_note' => null,
            'uploaded_by' => Auth::id() ?? 1,
            'uploaded_at' => now(),
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'user_id'       => Auth::id() ?? 1,
            'action'        => 'Document Uploaded',
            'description'   => "Uploaded '{$document->document_name}' ({$origName}).",
        ]);

        return back()->with('success', "Document '{$document->document_name}' uploaded successfully.");
    }

    /**
     * Review/Validate document (Validation Loop)
     */
    public function review(Request $request, EnvironmentDocument $document)
    {
        $validated = $request->validate([
            'status'      => 'required|in:validated,approved,revision_required',
            'review_note' => 'nullable|string|max:2000',
        ]);

        if (!$document->file_path) {
            return back()->with('error', 'Upload a document before reviewing it.');
        }

        $document->update([
            'status'      => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_by' => Auth::id() ?? 1,
            'reviewed_at' => now(),
        ]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $document->environment_project_id,
            'user_id'       => Auth::id() ?? 1,
            'action'        => 'Document ' . ucfirst(str_replace('_', ' ', $validated['status'])),
            'description'   => "Document '{$document->document_name}' marked as {$validated['status']}." . ($validated['review_note'] ? ' Note: ' . $validated['review_note'] : ''),
        ]);

        return back()->with('success', "Document review recorded as " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".");
    }

    /**
     * Update project stage lifecycle (draft -> validation -> approved -> reported -> archived)
     */
    public function updateStatus(Request $request, EnvironmentProject $project)
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,validation,approved,reported,archived',
        ]);

        $status = $validated['status'];

        if ($status === 'validation' && $project->documents()->whereIn('status', ['uploaded', 'validated', 'approved'])->doesntExist()) {
            return back()->with('error', 'Upload at least one document before moving to validation.');
        }

        if ($status === 'approved' && $project->documents()->where('status', 'approved')->doesntExist()) {
            return back()->with('error', 'Approve at least one document before marking the project as approved.');
        }

        $project->update(['status' => $status]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'user_id'       => Auth::id() ?? 1,
            'action'        => 'Stage Advanced',
            'description'   => "Project status transitioned to " . ucfirst($status) . ".",
        ]);

        return back()->with('success', "Project stage updated to " . ucfirst($status) . ".");
    }

    /**
     * Download document file
     */
    public function download(EnvironmentDocument $document)
    {
        abort_unless($document->file_path && file_exists(public_path($document->file_path)), 404);

        return response()->download(public_path($document->file_path), $document->file_name);
    }
}
