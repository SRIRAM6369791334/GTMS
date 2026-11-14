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
            'secondary_contact_person' => 'nullable|string|max:255',
            'contact_phone'            => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'secondary_phone'          => ['nullable', 'regex:/^[6-9][0-9]{9}$/', 'different:contact_phone'],
            'contact_email'            => 'nullable|email|max:255',
            'mimas_no'                 => 'nullable|string|max:50',
            'aadhaar_no'               => ['nullable', 'regex:/^[2-9][0-9]{3}[ -]?[0-9]{4}[ -]?[0-9]{4}$/'],
        ], [
            'contact_phone.regex'   => 'Contact phone must be 10 digits and start with 6, 7, 8 or 9.',
            'secondary_phone.regex' => 'Secondary phone must be 10 digits and start with 6, 7, 8 or 9.',
            'aadhaar_no.regex'      => 'Aadhaar must be 12 digits (e.g. 9876-5432-1012).',
        ]);

        // Same XXXX-XXXX-XXXX form Lease / Customers store
        if (!empty($validated['aadhaar_no'])) {
            $digits = preg_replace('/\D/', '', $validated['aadhaar_no']);
            $validated['aadhaar_no'] = substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8, 4);
        }

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
                    'customer_name'            => $validated['client_name'],
                    // No made-up company name or MIMAS number
                    'company_name'             => $validated['company_name'] ?? null,
                    'mimas_no'                 => $validated['mimas_no'] ?? null,
                    'aadhaar_no'               => $validated['aadhaar_no'] ?? null,
                    'mobile_num'               => $validated['contact_phone'],
                    'secondary_mobile_num'     => $validated['secondary_phone'] ?? null,
                    'secondary_contact_person' => $validated['secondary_contact_person'] ?? null,
                    'email'                    => $validated['contact_email'] ?? null,
                    'district_id'              => $validated['district_id'],
                    'address'                  => $validated['location'] ?? null,
                    'status'                   => 1,
                    'created_by'               => Auth::id(),
                ]);
            }

            // 2. Generate concurrency-safe atomic project code
            $year = date('Y');
            $maxProj = DB::table('environment_projects')
                ->where('project_code', 'like', "ENV-B2-{$year}-%")
                // soft-deleted codes stay in the UNIQUE index, so count them too
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
                'project_code'             => $projectCode,
                'customer_id'              => $customer->id,
                'district_id'              => $validated['district_id'],
                'category'                 => 'B2',
                'project_name'             => $validated['project_name'],
                'location'                 => $validated['location'] ?? $customer->address,
                'contact_name'             => $validated['contact_name'] ?? $customer->customer_name,
                'secondary_contact_person' => $validated['secondary_contact_person'] ?? $customer->secondary_contact_person,
                'contact_phone'            => $validated['contact_phone'] ?? $customer->mobile_num,
                'secondary_phone'          => $validated['secondary_phone'] ?? $customer->secondary_mobile_num,
                'contact_email'            => $validated['contact_email'] ?? $customer->email,
                'status'                   => 'draft',
                'branch_id'                => Auth::user()?->branch_id ?? 1,
                'created_by'               => Auth::id(),
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
                'user_id'       => Auth::id(),
                'action'        => 'Project Created',
                'description'   => "B2 Environment Clearance project {$projectCode} created with 6 folders and checklist.",
                'created_at'    => now(),
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
     * Upload real document file against checklist item (supports AJAX, session draft, and unlinking).
     */
    public function upload(Request $request, $document = null)
    {
        $allowedExtensions = [
            'pdf', 'png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp',
            'zip', 'rar', '7z', 'tar', 'gz',
            'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'csv', 'rtf',
            'kml', 'kmz', 'xml', 'dwg', 'dxf',
            'mp4', 'webm', 'mkv', 'avi', 'mov',
            'las', 'laz', 'tif', 'tiff', 'shp', 'dat', 'ply', 'rnx', 'obs'
        ];

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:524288000',
                function ($attribute, $value, $fail) use ($allowedExtensions) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        if (!in_array($ext, $allowedExtensions, true)) {
                            $fail("The {$attribute} must be a file of type: " . implode(', ', $allowedExtensions));
                        }
                    }
                }
            ],
            'project_id'             => 'nullable',
            'environment_project_id' => 'nullable',
            'document_id'            => 'nullable',
            'doc_id'                 => 'nullable',
            'doc_item'               => 'nullable',
            'folder_id'              => 'nullable|integer',
            'document_name'          => 'nullable|string|max:255',
        ]);

        $docId = ($document instanceof EnvironmentDocument ? $document->id : $document)
            ?: ($request->input('doc_id') ?: ($request->input('document_id') ?: $request->input('doc_item')));
        $docNameInput = $request->input('document_name');
        $projId = $request->input('project_id') ?: $request->input('environment_project_id');

        $doc = null;
        if ($document instanceof EnvironmentDocument) {
            $doc = $document;
        } elseif ($docId && is_numeric($docId)) {
            $doc = EnvironmentDocument::find($docId);
        }

        $project = $doc?->environmentProject;
        if (!$project && $projId) {
            $project = EnvironmentProject::find($projId);
        }

        if (!$doc && $project && $docNameInput) {
            $doc = EnvironmentDocument::where('environment_project_id', $project->id)
                ->where(function ($q) use ($docNameInput) {
                    $q->where('document_name', $docNameInput)
                      ->orWhere('document_name', 'like', '%' . $docNameInput . '%');
                })->first();
        }

        $oldFilePath = $doc?->file_path;
        $sessionDocs = session('enviro_b2_wizard.uploaded_docs', []);
        $docItemKey = $request->input('doc_item') ?? ($doc?->id ?? ($docNameInput ?? 'doc_0'));
        if (!$oldFilePath && isset($sessionDocs[$docItemKey]['file_path'])) {
            $oldFilePath = $sessionDocs[$docItemKey]['file_path'];
        }

        $file = $request->file('file');
        $origName = $file->getClientOriginalName();
        $docIdTag = $doc?->id ?? 'draft';
        $safeName = time() . '_' . $docIdTag . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);

        $projectCode = $project?->project_code ?: 'draft';
        $subDir = 'uploads/environment-b2/' . $projectCode;
        $destPath = public_path($subDir);

        if (!file_exists($destPath)) {
            mkdir($destPath, 0777, true);
        }

        // Delete previous file if replacing
        if ($oldFilePath && $oldFilePath !== ($subDir . '/' . $safeName)) {
            if (file_exists(public_path($oldFilePath))) @unlink(public_path($oldFilePath));
            if (file_exists(public_path('storage/' . $oldFilePath))) @unlink(public_path('storage/' . $oldFilePath));
            if (file_exists(storage_path('app/public/' . $oldFilePath))) @unlink(storage_path('app/public/' . $oldFilePath));
        }

        $file->move($destPath, $safeName);

        $fileSize = file_exists($destPath . '/' . $safeName) ? filesize($destPath . '/' . $safeName) : $file->getSize();
        $formattedSize = $fileSize >= 1048576 ? round($fileSize / 1048576, 2) . ' MB' : round($fileSize / 1024, 1) . ' KB';
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'], true);

        if ($doc) {
            $doc->update([
                'file_name'   => $origName,
                'file_path'   => $subDir . '/' . $safeName,
                'file_type'   => $ext,
                'file_size'   => $fileSize,
                'status'      => 'uploaded',
                'review_note' => null,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            ActivityLog::create([
                'loggable_type' => EnvironmentProject::class,
                'loggable_id'   => $project ? $project->id : $doc->environment_project_id,
                'user_id'       => Auth::id(),
                'action'        => 'Document Uploaded',
                'description'   => "Uploaded '{$doc->document_name}' ({$origName}).",
                'created_at'    => now(),
            ]);
        }

        $sessionDocs[$docItemKey] = [
            'doc_item'        => $docItemKey,
            'doc_id'          => $doc?->id,
            'doc_name'        => $doc?->document_name ?? $origName,
            'file_name'       => $origName,
            'file_path'       => $subDir . '/' . $safeName,
            'file_url'        => asset($subDir . '/' . $safeName),
            'file_size'       => $formattedSize,
            'file_size_bytes' => $fileSize,
            'is_zip'          => $isZip,
            'uploaded_at'     => now()->format('d M Y, h:i A'),
        ];
        session(['enviro_b2_wizard.uploaded_docs' => $sessionDocs]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->is('enviro_b2/*') || $request->is('enviro-b2/*') || $request->is('environment-b2/*')) {
            return response()->json([
                'status'       => 1,
                'success'      => true,
                'message'      => "Document '" . ($doc?->document_name ?? $origName) . "' uploaded successfully.",
                'doc_id'       => $doc?->id,
                'doc_item'     => $docItemKey,
                'file_name'    => $origName,
                'file_path'    => $subDir . '/' . $safeName,
                'file_url'     => asset($subDir . '/' . $safeName),
                'file_size'    => $formattedSize,
                'size_bytes'   => $fileSize,
                'is_zip'       => $isZip,
            ]);
        }

        return back()->with('success', "Document '" . ($doc?->document_name ?? $origName) . "' uploaded successfully.");
    }

    /**
     * Remove an uploaded B2 document
     */
    public function removeDocument(Request $request, $document = null)
    {
        $docId = ($document instanceof EnvironmentDocument ? $document->id : $document)
            ?? $request->input('doc_id')
            ?? $request->input('document_id')
            ?? $request->input('doc_item');

        $doc = null;
        if ($document instanceof EnvironmentDocument) {
            $doc = $document;
        } elseif ($docId && is_numeric($docId)) {
            $doc = EnvironmentDocument::find($docId);
        }

        if ($doc) {
            $filePath = $doc->file_path;
            if ($filePath) {
                if (file_exists(public_path($filePath))) @unlink(public_path($filePath));
                if (file_exists(public_path('storage/' . $filePath))) @unlink(public_path('storage/' . $filePath));
                if (file_exists(storage_path('app/public/' . $filePath))) @unlink(storage_path('app/public/' . $filePath));
            }

            if ($doc->document_field_id) {
                $doc->update([
                    'file_name'   => null,
                    'file_path'   => null,
                    'file_type'   => null,
                    'file_size'   => null,
                    'status'      => 'pending',
                    'uploaded_by' => null,
                    'uploaded_at' => null,
                ]);
            } else {
                $doc->delete();
            }
        }

        $sessionDocs = session('enviro_b2_wizard.uploaded_docs', []);
        $docItemKey = $request->input('doc_item') ?? $docId ?? $request->input('document_name');
        if ($docItemKey && isset($sessionDocs[$docItemKey])) {
            $sessionPath = $sessionDocs[$docItemKey]['file_path'] ?? null;
            if ($sessionPath) {
                if (file_exists(public_path($sessionPath))) @unlink(public_path($sessionPath));
                if (file_exists(public_path('storage/' . $sessionPath))) @unlink(public_path('storage/' . $sessionPath));
                if (file_exists(storage_path('app/public/' . $sessionPath))) @unlink(storage_path('app/public/' . $sessionPath));
            }
            unset($sessionDocs[$docItemKey]);
            session(['enviro_b2_wizard.uploaded_docs' => $sessionDocs]);
        }

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->is('enviro_b2/*') || $request->is('enviro-b2/*') || $request->is('environment-b2/*')) {
            return response()->json([
                'status'   => 1,
                'success'  => true,
                'message'  => 'Document removed successfully.',
                'doc_id'   => $doc?->id ?? $docId,
                'doc_item' => $docItemKey,
            ]);
        }

        return back()->with('success', 'Document removed successfully.');
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
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $document->environment_project_id,
            'user_id'       => Auth::id(),
            'action'        => 'Document ' . ucfirst(str_replace('_', ' ', $validated['status'])),
            'description'   => "Document '{$document->document_name}' marked as {$validated['status']}." . ($validated['review_note'] ? ' Note: ' . $validated['review_note'] : ''),
            'created_at'    => now(),
        ]);

        return back()->with('success', "Document review recorded as " . ucfirst(str_replace('_', ' ', $validated['status'])) . ".");
    }

    /**
     * Update project stage lifecycle (draft -> validation -> approved -> reported -> archived)
     */
    public function updateStatus(Request $request, EnvironmentProject $project)
    {
        $validated = $request->validate([
            'status'       => 'required|in:draft,validation,validated,revision_required,reported,approved,archived',
            'status_notes' => 'nullable|string|max:1000',
        ]);

        $status = trim($validated['status']);

        if ($status === 'validation' && $project->documents()->whereIn('status', ['uploaded', 'validated', 'approved'])->doesntExist()) {
            return back()->with('error', 'Upload at least one document before moving to validation.');
        }

        if ($status === 'approved' && $project->documents()->where('status', 'approved')->doesntExist()) {
            return back()->with('error', 'Approve at least one document before marking the project as approved.');
        }

        $updateData = ['status' => $status];
        if ($request->has('status_notes')) {
            $updateData['status_notes'] = $request->status_notes;
        }
        $project->update($updateData);

        $noteLog = !empty($request->status_notes) ? " Notes: {$request->status_notes}" : '';
        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'user_id'       => Auth::id(),
            'action'        => 'Stage Advanced',
            'description'   => "Project status transitioned to " . ucfirst($status) . ".{$noteLog}",
            'created_at'    => now(),
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
