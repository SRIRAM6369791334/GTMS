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
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\ChunkedUpload;
use App\Services\ChunkedUploadService;

class PptDepartmentController extends Controller
{
    use \App\Http\Controllers\Concerns\WizardStoreHelpers;

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

        // "New" buttons pass ?new=1: start clean instead of reusing an old draft
        if ($request->boolean('new')) {
            session()->forget('ppt_wizard');
        }

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

        $customers = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name', 'mimas_no', 'mobile_num', 'secondary_mobile_num']);
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
            $draft['customer_id']       = $request->input('customer_id');
            $draft['project_name']      = $request->input('project_name');
            $draft['application_no']    = $request->input('application_no');
            $draft['primary_phone']     = $request->input('primary_phone');
            $draft['secondary_phone']   = $request->input('secondary_phone');
            $draft['parivesh_password'] = $request->input('parivesh_password');
            $draft['email_password']    = $request->input('email_password');
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
        $in = $this->wizardInput($request, $draft, [
            'customer_id', 'project_name', 'district_id', 'taluk_village', 'mineral_id', 'application_no',
            'primary_phone', 'secondary_phone', 'parivesh_password', 'email_password',
            'product_value', 'paid_amount', 'payment_status', 'payment_notes',
        ]);

        if ($fail = $this->validateWizard($in, [
            'customer_id'       => 'required|integer|exists:customers,id',
            'project_name'      => 'required|string|max:255',
            'district_id'       => 'required|integer|exists:districts,id',
            'taluk_village'     => 'nullable|string|max:255',
            'mineral_id'        => 'required|integer|exists:minerals,id',
            'application_no'    => 'nullable|string|max:50',
            'primary_phone'     => ['nullable', $this->mobileRule],
            'secondary_phone'   => ['nullable', $this->mobileRule, 'different:primary_phone'],
            'parivesh_password' => 'nullable|string|max:255',
            'email_password'    => 'nullable|string|max:255',
            'product_value'     => 'nullable|numeric|min:0',
            'paid_amount'       => 'nullable|numeric|min:0',
            'payment_status'    => 'nullable|in:pending,partial,paid',
            'payment_notes'     => 'nullable|string|max:1000',
        ], [
            'customer_id' => 1, 'project_name' => 1, 'application_no' => 1, 'primary_phone' => 1, 'secondary_phone' => 1,
            'parivesh_password' => 1, 'email_password' => 1, 'district_id' => 2, 'taluk_village' => 2, 'mineral_id' => 3,
            'product_value' => 8, 'paid_amount' => 8, 'payment_status' => 8, 'payment_notes' => 8,
        ], 'ppt-department.step')) {
            return $fail;
        }

        try {
            [$val, $paid, $pending, $pStatus] = $this->wizardPayment($in);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('ppt-department.step', 8)->withErrors($e->errors())->withInput();
        }

        // Resume / edit: update the same record instead of creating a duplicate
        $existing = !empty($draft['id']) ? PptApplication::find($draft['id']) : null;

        DB::beginTransaction();
        try {
            $data = [
                'customer_id'          => (int) $in['customer_id'],
                'project_name'         => $in['project_name'],
                'district_id'          => (int) $in['district_id'],
                'taluk_village'        => $in['taluk_village'],
                'mineral_id'           => (int) $in['mineral_id'],
                'product_value'        => $val,
                'paid_amount'          => $paid,
                'pending_amount'       => $pending,
                'payment_status'       => $pStatus,
                'rep_mobile'           => $in['primary_phone'],
                'rep_secondary_mobile' => $in['secondary_phone'],
                'parivesh_password'    => $in['parivesh_password'],
                'email_password'       => $in['email_password'],
            ];

            if ($existing) {
                $existing->update($data);
                $ppt = $existing;
            } else {
                $ppt = PptApplication::create($data + [
                    'application_no' => $this->resolveNumber(PptApplication::class, 'application_no', $in['application_no'], 'PPT-' . date('Y') . '-'),
                    'status'         => 'agenda_scheduled',
                ]);
            }

            // Handlers: replace with what the wizard holds (no placeholder names)
            ApplicationHandler::where('application_type', 'ppt')->where('application_id', $ppt->id)->delete();
            $handlers = $request->input('handlers', $draft['handlers'] ?? []);
            foreach ((array) $handlers as $hIdx => $h) {
                $name = trim((string) ($h['person_name'] ?? $h['name'] ?? ''));
                if ($name !== '') {
                    ApplicationHandler::create([
                        'application_type' => 'ppt',
                        'application_id'   => $ppt->id,
                        'name'             => $name,
                        'role'             => trim((string) ($h['role'] ?? '')),
                        'notes'            => $h['notes'] ?? null,
                        'sort_order'       => $hIdx + 1,
                    ]);
                }
            }

            ApplicationPayment::updateOrCreate(
                ['application_type' => 'ppt', 'application_id' => $ppt->id],
                [
                    'product_value'  => $val,
                    'paid_amount'    => $paid,
                    'pending_amount' => $pending,
                    'payment_status' => $pStatus,
                    'notes'          => $in['payment_notes'],
                ]
            );

            // Documents uploaded in this wizard session (skip files already on the record)
            foreach ((array) ($draft['uploaded_docs'] ?? []) as $uDoc) {
                if (empty($uDoc['file_path']) || PptDocument::where('ppt_application_id', $ppt->id)->where('file_path', $uDoc['file_path'])->exists()) {
                    continue;
                }
                PptDocument::create([
                    'ppt_application_id' => $ppt->id,
                    'folder_id'          => $uDoc['folder_id'] ?? 1,
                    'document_name'      => $uDoc['document_name'] ?? ($uDoc['file_name'] ?? basename($uDoc['file_path'])),
                    'file_name'          => $uDoc['file_name'] ?? basename($uDoc['file_path']),
                    'file_path'          => $uDoc['file_path'],
                    'file_type'          => $uDoc['file_type'] ?? pathinfo($uDoc['file_path'], PATHINFO_EXTENSION),
                    'file_size'          => $uDoc['file_size_bytes'] ?? ($uDoc['file_size'] ?? null),
                    'status'             => 'uploaded',
                    'uploaded_by'        => Auth::id(),
                    'uploaded_at'        => now(),
                ]);
            }

            DB::commit();
            session()->forget('ppt_wizard');

            return redirect()->route('ppt-department.show', $ppt->id)
                ->with('success', "PPT Application {$ppt->application_no} " . ($existing ? 'updated.' : 'saved.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->storeFailed('PPT application', $e, 'ppt-department.step', 9);
        }
    }

    /**
     * Show Application Dossier
     */
    public function show($id)
    {
        $ppt = PptApplication::with(['customer', 'district', 'mineral', 'handlers', 'payments', 'documents', 'agendas', 'environmentProject'])->findOrFail($id);
        return view('pages.ppt_department.show', compact('ppt'));
    }

    /**
     * Upload Document via AJAX (Direct File or Chunked Upload Token) with safe unlinking and session draft.
     */
    public function uploadDocument(Request $request)
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
                'required_without:upload_token',
                'nullable',
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
            'upload_token'   => 'required_without:file|nullable|string',
            'application_id' => 'nullable|integer',
            'doc_id'         => 'nullable|integer',
            'doc_item'       => 'nullable',
            'field_id'       => 'nullable',
            'folder_id'      => 'nullable|integer',
            'document_name'  => 'nullable|string|max:255',
            'doc_name'       => 'nullable|string|max:255',
        ]);

        $appId = $request->input('application_id') ?: $request->input('ppt_id');
        $docId = $request->input('doc_id');
        $fieldId = $request->input('field_id');
        $rawDocItem = $request->input('doc_item') ?? ($fieldId !== null ? $fieldId : $docId);
        $folderId = $request->input('folder_id', 1);
        $docNameInput = $request->input('document_name') ?: $request->input('doc_name');

        $existingDoc = null;
        $oldFilePath = null;
        if ($docId) {
            $existingDoc = PptDocument::find($docId);
            $oldFilePath = $existingDoc?->file_path;
        } elseif ($appId) {
            $q = PptDocument::where('ppt_application_id', $appId);
            if ($docNameInput) {
                $q->where(function ($sq) use ($docNameInput) {
                    $sq->where('document_name', $docNameInput)
                       ->orWhere('document_name', 'like', '%' . $docNameInput . '%');
                });
            } elseif ($folderId) {
                $q->where('folder_id', $folderId);
            }
            $existingDoc = $q->first();
            $oldFilePath = $existingDoc?->file_path;
        }

        $sessionDocs = session('ppt_wizard.uploaded_docs', []);
        $docItemKey = $rawDocItem !== null && $rawDocItem !== '' ? (string)$rawDocItem : ($docNameInput ?? ('folder_' . $folderId));
        if (!$oldFilePath && isset($sessionDocs[$docItemKey]['file_path'])) {
            $oldFilePath = $sessionDocs[$docItemKey]['file_path'];
        }

        // Handle Chunked Upload Token
        if ($request->filled('upload_token')) {
            $upload = ChunkedUpload::where('upload_token', $request->input('upload_token'))->where('user_id', auth()->id())->firstOrFail();
            $appId = $appId ?? $upload->reference_id;

            if (!$upload->isAssembled()) {
                $assembly = app(ChunkedUploadService::class)->assembleFile($upload->upload_token, 'ppt', $appId);
                $filePath = $assembly['file_path'];
            } else {
                $filePath = $upload->file_path;
            }

            // Unlink old file if different
            if ($oldFilePath && $oldFilePath !== $filePath) {
                if (file_exists(public_path($oldFilePath))) @unlink(public_path($oldFilePath));
                if (file_exists(public_path('storage/' . $oldFilePath))) @unlink(public_path('storage/' . $oldFilePath));
                if (file_exists(storage_path('app/public/' . $oldFilePath))) @unlink(storage_path('app/public/' . $oldFilePath));
            }

            $fileName = substr((string)($upload->original_name ?? $upload->file_name), 0, 255);
            $fileSize = $upload->total_size;
            $docName = $docNameInput ?: ($upload->metadata['document_name'] ?? $fileName);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'], true);
            $formattedSize = $fileSize >= 1048576 ? round($fileSize / 1048576, 2) . ' MB' : round($fileSize / 1024, 1) . ' KB';

            if ($appId) {
                if ($existingDoc) {
                    $existingDoc->update([
                        'folder_id'     => $folderId,
                        'document_name' => $docName,
                        'file_name'     => $fileName,
                        'file_path'     => $filePath,
                        'file_type'     => $ext,
                        'file_size'     => $fileSize,
                        'status'        => 'uploaded',
                        'uploaded_by'   => Auth::id(),
                    ]);
                    $docId = $existingDoc->id;
                } else {
                    $doc = PptDocument::create([
                        'ppt_application_id' => $appId,
                        'folder_id'          => $folderId,
                        'document_name'      => $docName,
                        'file_name'          => $fileName,
                        'file_path'          => $filePath,
                        'file_type'          => $ext,
                        'file_size'          => $fileSize,
                        'status'             => 'uploaded',
                        'uploaded_by'        => Auth::id(),
                    ]);
                    $docId = $doc->id;
                }

                $upload->update([
                    'target_module' => 'ppt',
                    'reference_id'  => $appId,
                    'status'        => 'completed',
                ]);
            }

            $itemData = [
                'doc_item'        => $docItemKey,
                'field_id'        => $fieldId !== null ? (string)$fieldId : (string)$docItemKey,
                'doc_id'          => $docId,
                'folder_id'       => $folderId,
                'document_name'   => $docName,
                'file_name'       => $fileName,
                'file_path'       => $filePath,
                'file_url'        => asset('storage/' . $filePath),
                'file_size'       => $formattedSize,
                'file_size_bytes' => $fileSize,
                'is_zip'          => $isZip,
                'uploaded_at'     => now()->toDateTimeString(),
            ];
            $sessionDocs[$docItemKey] = $itemData;
            if ($fieldId !== null) {
                $sessionDocs[(string)$fieldId] = $itemData;
            }
            session(['ppt_wizard.uploaded_docs' => $sessionDocs]);

            return response()->json([
                'status'        => 1,
                'success'       => true,
                'message'       => $fileName . ' uploaded & saved successfully',
                'doc_id'        => $docId,
                'doc_item'      => $docItemKey,
                'field_id'      => $fieldId !== null ? (string)$fieldId : (string)$docItemKey,
                'folder_id'     => $folderId,
                'document_name' => $docName,
                'file_name'     => $fileName,
                'file_path'     => $filePath,
                'file_url'      => asset('storage/' . $filePath),
                'file_size'     => $formattedSize,
                'is_zip'        => $isZip,
            ]);
        }

        // Direct File Upload
        $file = $request->file('file');
        $origName = substr($file->getClientOriginalName(), 0, 255);
        $fileSize = $file->getSize();
        $ext = strtolower($file->getClientOriginalExtension());
        $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'], true);
        $docName = $docNameInput ?: $origName;
        $formattedSize = $fileSize >= 1048576 ? round($fileSize / 1048576, 2) . ' MB' : round($fileSize / 1024, 1) . ' KB';

        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
        $filePath = $file->storeAs('uploads/ppt', $safeName, 'public');

        // Unlink old file if replacing
        if ($oldFilePath && $oldFilePath !== $filePath) {
            if (file_exists(public_path($oldFilePath))) @unlink(public_path($oldFilePath));
            if (file_exists(public_path('storage/' . $oldFilePath))) @unlink(public_path('storage/' . $oldFilePath));
            if (file_exists(storage_path('app/public/' . $oldFilePath))) @unlink(storage_path('app/public/' . $oldFilePath));
        }

        if ($appId) {
            if ($existingDoc) {
                $existingDoc->update([
                    'folder_id'     => $folderId,
                    'document_name' => $docName,
                    'file_name'     => $origName,
                    'file_path'     => $filePath,
                    'file_type'     => $ext,
                    'file_size'     => $fileSize,
                    'status'        => 'uploaded',
                    'uploaded_by'   => Auth::id(),
                ]);
                $docId = $existingDoc->id;
            } else {
                $doc = PptDocument::create([
                    'ppt_application_id' => $appId,
                    'folder_id'          => $folderId,
                    'document_name'      => $docName,
                    'file_name'          => $origName,
                    'file_path'          => $filePath,
                    'file_type'          => $ext,
                    'file_size'          => $fileSize,
                    'status'             => 'uploaded',
                    'uploaded_by'        => Auth::id(),
                ]);
                $docId = $doc->id;
            }
        }

        $itemData = [
            'doc_item'        => $docItemKey,
            'field_id'        => $fieldId !== null ? (string)$fieldId : (string)$docItemKey,
            'doc_id'          => $docId,
            'folder_id'       => $folderId,
            'document_name'   => $docName,
            'file_name'       => $origName,
            'file_path'       => $filePath,
            'file_url'        => asset('storage/' . $filePath),
            'file_size'       => $formattedSize,
            'file_size_bytes' => $fileSize,
            'is_zip'          => $isZip,
            'uploaded_at'     => now()->toDateTimeString(),
        ];
        $sessionDocs[$docItemKey] = $itemData;
        if ($fieldId !== null) {
            $sessionDocs[(string)$fieldId] = $itemData;
        }
        session(['ppt_wizard.uploaded_docs' => $sessionDocs]);

        return response()->json([
            'status'        => 1,
            'success'       => true,
            'message'       => $origName . ' uploaded & saved successfully',
            'doc_id'        => $docId,
            'doc_item'      => $docItemKey,
            'field_id'      => $fieldId !== null ? (string)$fieldId : (string)$docItemKey,
            'folder_id'     => $folderId,
            'document_name' => $docName,
            'file_name'     => $origName,
            'file_path'     => $filePath,
            'file_url'      => asset('storage/' . $filePath),
            'file_size'     => $formattedSize,
            'is_zip'        => $isZip,
        ]);
    }

    /**
     * Remove Document via AJAX with safe disk unlinking and session draft update.
     */
    public function removeDocument(Request $request)
    {
        $docId = $request->input('doc_id');
        $fieldId = $request->input('field_id');
        $rawDocItem = $request->input('doc_item') ?? ($fieldId !== null ? $fieldId : $docId);
        $appId = $request->input('application_id') ?: $request->input('ppt_id');
        $folderId = $request->input('folder_id');
        $docNameInput = $request->input('document_name') ?: $request->input('doc_name');

        $doc = null;
        if ($docId) {
            $doc = PptDocument::find($docId);
        } elseif ($appId) {
            $q = PptDocument::where('ppt_application_id', $appId);
            if ($docNameInput) {
                $q->where('document_name', $docNameInput);
            } elseif ($folderId) {
                $q->where('folder_id', $folderId);
            }
            $doc = $q->first();
        }

        if ($doc) {
            $path = $doc->file_path;
            if ($path) {
                if (file_exists(public_path($path))) @unlink(public_path($path));
                if (file_exists(public_path('storage/' . $path))) @unlink(public_path('storage/' . $path));
                if (file_exists(storage_path('app/public/' . $path))) @unlink(storage_path('app/public/' . $path));
            }
            $doc->delete();
        }

        // Clean session draft
        $sessionDocs = session('ppt_wizard.uploaded_docs', []);
        $docItemKey = $rawDocItem !== null && $rawDocItem !== '' ? (string)$rawDocItem : ($docNameInput ?? ('folder_' . $folderId));
        $draftPath = null;
        if (isset($sessionDocs[$docItemKey])) {
            $draftPath = $sessionDocs[$docItemKey]['file_path'] ?? null;
            unset($sessionDocs[$docItemKey]);
        }
        if ($fieldId !== null && isset($sessionDocs[(string)$fieldId])) {
            if (!$draftPath) $draftPath = $sessionDocs[(string)$fieldId]['file_path'] ?? null;
            unset($sessionDocs[(string)$fieldId]);
        }
        if ($draftPath) {
            if (file_exists(public_path($draftPath))) @unlink(public_path($draftPath));
            if (file_exists(public_path('storage/' . $draftPath))) @unlink(public_path('storage/' . $draftPath));
            if (file_exists(storage_path('app/public/' . $draftPath))) @unlink(storage_path('app/public/' . $draftPath));
        }
        session(['ppt_wizard.uploaded_docs' => $sessionDocs]);

        return response()->json([
            'status'   => 1,
            'success'  => true,
            'message'  => 'Document removed successfully.',
            'doc_id'   => $docId,
            'doc_item' => $docItemKey,
            'field_id' => $fieldId !== null ? (string)$fieldId : (string)$docItemKey,
        ]);
    }

    /**
     * Approve presentation and advance linked Environment Clearance Project stage.
     */
    public function approvePresentation(Request $request, int $id)
    {
        $ppt = PptApplication::findOrFail($id);

        if ($ppt->status === 'approved') {
            return redirect()->back()->with('info', "PPT Application {$ppt->application_no} is already approved.");
        }
        if (!in_array($ppt->status, self::APPROVABLE_STATUSES, true)) {
            return redirect()->back()->with('error', "Only a scheduled or presented application can be approved (current status: {$ppt->status}).");
        }

        $ppt->update(['status' => 'approved']);
        $this->logPpt($ppt, 'status_updated', "PPT Application {$ppt->application_no} approved.");
        $message = $this->applyApprovalToProject($ppt);

        return redirect()->back()->with('success', $message ?? "PPT Application {$ppt->application_no} status updated to Approved.");
    }

    /** Statuses the PPT status form may set */
    private const STATUSES = ['draft', 'agenda_scheduled', 'rescheduled', 'presented', 'approved', 'rejected', 'archived'];

    /** Statuses from which a presentation can be approved */
    private const APPROVABLE_STATUSES = ['agenda_scheduled', 'rescheduled', 'presented'];

    /**
     * Move the linked environment project forward after SEAC approval.
     * Runs once per approval (callers check the PPT was not already approved).
     */
    private function applyApprovalToProject(PptApplication $ppt): ?string
    {
        if (!$ppt->environment_project_id) {
            return null;
        }
        $project = EnvironmentProject::find($ppt->environment_project_id);
        if (!$project) {
            return null;
        }

        if ($project->category === 'B1' && $ppt->presentation_stage === 'tor_presentation') {
            $project->update([
                'sub_category' => 'ETA',
                'b1_stage'     => 'sc2_prep',
                'status'       => 'draft',
            ]);
            EnverionsoneController::generateSlots($project);
            $this->logProject($project, 'tor_approved_by_ppt', "Stage 1 ToR Presentation ({$ppt->application_no}) approved by SEAC. Advanced to ETA.");
            return "Stage 1 ToR Presentation ({$ppt->application_no}) Approved! Environment Project {$project->project_code} has advanced to ETA (EIA Study & TNPCB Submission).";
        }

        if ($project->category === 'B1' && $ppt->presentation_stage === 'final_ec_presentation') {
            $project->update(['b1_stage' => 'completed', 'status' => 'approved']);
            $this->logProject($project, 'final_ec_approved_by_ppt', "Stage 2 Final EC Presentation ({$ppt->application_no}) approved by SEAC/SEIAA. Project completed and approved for EC Certificate.");
            return "Stage 2 Final EC Presentation ({$ppt->application_no}) Approved! Environment Project {$project->project_code} is now fully Approved and ready for EC Certificate Issuance.";
        }

        if ($project->category === 'B2' || $ppt->presentation_stage === 'b2_presentation') {
            $project->update(['b1_stage' => 'completed', 'status' => 'approved']);
            $this->logProject($project, 'b2_approved_by_ppt', "Category B2 SEAC EC Presentation ({$ppt->application_no}) approved. Project completed and approved for EC Certificate.");
            return "Category B2 SEAC EC Presentation ({$ppt->application_no}) Approved! Environment Project {$project->project_code} is now fully Approved and ready for EC Certificate Issuance.";
        }

        return null;
    }

    private function logPpt(PptApplication $ppt, string $action, string $description): void
    {
        ActivityLog::create([
            'loggable_type' => PptApplication::class,
            'loggable_id'   => $ppt->id,
            'action'        => $action,
            'description'   => $description,
            'user_id'       => Auth::id(),
            'created_at'    => now(),
        ]);
    }

    private function logProject(EnvironmentProject $project, string $action, string $description): void
    {
        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $project->id,
            'action'        => $action,
            'description'   => $description,
            'user_id'       => Auth::id(),
            'created_at'    => now(),
        ]);
    }

    /**
     * Update PPT Application Status from the dossier status form.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status'       => 'required|in:' . implode(',', self::STATUSES),
            'status_notes' => 'nullable|string|max:1000',
        ]);

        $ppt = PptApplication::findOrFail($id);
        $newStatus = $request->status;
        $wasApproved = $ppt->status === 'approved';

        if ($newStatus === 'approved' && !$wasApproved && !in_array($ppt->status, self::APPROVABLE_STATUSES, true)) {
            return redirect()->back()->with('error', "Only a scheduled or presented application can be approved (current status: {$ppt->status}).");
        }

        $updateData = ['status' => $newStatus];
        if ($request->has('status_notes')) {
            $updateData['status_notes'] = $request->status_notes;
        }
        $ppt->update($updateData);

        $noteLog = !empty($request->status_notes) ? " Notes: {$request->status_notes}" : '';
        $this->logPpt($ppt, 'status_updated', "PPT Application status updated to '{$newStatus}'.{$noteLog}");

        // Approval moves the linked environment project forward - once only
        $message = null;
        if ($newStatus === 'approved' && !$wasApproved) {
            $message = $this->applyApprovalToProject($ppt);
        }

        return redirect()->back()->with('success', $message ?? "Status updated to '{$newStatus}' successfully.");
    }
}
