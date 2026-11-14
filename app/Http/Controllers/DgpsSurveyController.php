<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DgpsSurvey;
use App\Models\DgpsDocument;
use App\Models\ChunkedUpload;
use App\Services\ChunkedUploadService;
use App\Models\Folder;
use App\Models\Customer;
use App\Models\LeaseApplication;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DgpsSurveyController extends Controller
{
    use \App\Http\Controllers\Concerns\WizardStoreHelpers;

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
        abort_unless($step >= 1 && $step <= 6, 404);

        // "New" buttons pass ?new=1: start clean instead of reusing an old draft
        if ($request->boolean('new')) {
            session()->forget('dgps_wizard');
        }

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

        if ($step === 1 && $request->filled('customer_id') && empty($draft['customer_id'])) {
            $draft['customer_id'] = (int) $request->input('customer_id');
            session(['dgps_wizard' => $draft]);
        }

        $customers = Customer::orderBy('customer_name')->get(['id', 'customer_name', 'company_name', 'mimas_no', 'mobile_num', 'secondary_mobile_num']);
        $leaseApps = LeaseApplication::orderBy('application_no')->get(['id', 'application_no', 'customer_id', 'area_extent_acres']);
        $uploadedDocs = session('dgps_wizard.uploaded_docs', $draft['uploaded_docs'] ?? []);

        return view('pages.dgps_survey.wizard', compact(
            'step',
            'draft',
            'customers',
            'leaseApps',
            'uploadedDocs'
        ));
    }

    /**
     * Save Step in Wizard Draft
     */
    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 6, 404);

        $draft = session('dgps_wizard', []);

        // Step 1: Request & Location
        if ($step === 1) {
            $draft['customer_id']          = $request->input('customer_id');
            $draft['lease_application_id'] = $request->input('lease_application_id');
            $draft['lease_area_ha']        = $request->input('lease_area_ha');
            $draft['location']             = $request->input('location');
            $draft['survey_no']            = $request->input('survey_no');
            $draft['primary_phone']        = $request->input('primary_phone');
            $draft['secondary_phone']      = $request->input('secondary_phone');
        }

        // Step 2: Field Survey
        if ($step === 2) {
            $draft['survey_date']       = $request->input('survey_date', date('Y-m-d'));
            $draft['survey_team_notes'] = $request->input('survey_team_notes');
        }

        // Step 4 (formerly 6): Handlers
        if ($step === 4) {
            $draft['handlers'] = $request->input('handlers', []);
        }

        // Step 5 (formerly 7): Payment
        if ($step === 5) {
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

        if ($step < 6) {
            return redirect()->route('dgps-survey.step', $step + 1);
        }

        return redirect()->route('dgps-survey.step', 6);
    }

    /**
     * Final Submission & Database Persistence
     */
    public function store(Request $request)
    {
        $draft = session('dgps_wizard', []);
        $in = $this->wizardInput($request, $draft, [
            'customer_id', 'lease_application_id', 'survey_no', 'field_book_no', 'lease_area_ha', 'surveyed_area_ha',
            'area_discrepancy_ha', 'location', 'survey_date', 'instrument_model', 'instrument_serial_no',
            'survey_team_notes', 'primary_phone', 'secondary_phone',
            'product_value', 'paid_amount', 'payment_status', 'payment_notes',
        ]);

        // A link to a lease that no longer exists (stale draft) is dropped, not an error
        if ($in['lease_application_id'] && !DB::table('lease_applications')->where('id', (int) $in['lease_application_id'])->exists()) {
            $in['lease_application_id'] = null;
        }

        if ($fail = $this->validateWizard($in, [
            'customer_id'          => 'required|integer|exists:customers,id',
            'lease_application_id' => 'nullable|integer|exists:lease_applications,id',
            'survey_no'            => 'nullable|string|max:50',
            'field_book_no'        => 'nullable|string|max:100',
            'lease_area_ha'        => 'nullable|numeric|min:0',
            'surveyed_area_ha'     => 'nullable|numeric|min:0',
            'area_discrepancy_ha'  => 'nullable|numeric',
            'location'             => 'nullable|string|max:255',
            'survey_date'          => 'nullable|date',
            'instrument_model'     => 'nullable|string|max:255',
            'instrument_serial_no' => 'nullable|string|max:255',
            'survey_team_notes'    => 'nullable|string|max:2000',
            'primary_phone'        => ['nullable', $this->mobileRule],
            'secondary_phone'      => ['nullable', $this->mobileRule, 'different:primary_phone'],
            'product_value'        => 'nullable|numeric|min:0',
            'paid_amount'          => 'nullable|numeric|min:0',
            'payment_status'       => 'nullable|in:pending,partial,paid',
            'payment_notes'        => 'nullable|string|max:1000',
        ], [
            'customer_id' => 1, 'lease_application_id' => 1, 'survey_no' => 1, 'lease_area_ha' => 1, 'location' => 1,
            'primary_phone' => 1, 'secondary_phone' => 1, 'survey_date' => 2, 'survey_team_notes' => 2,
            'product_value' => 5, 'paid_amount' => 5, 'payment_status' => 5, 'payment_notes' => 5,
        ], 'dgps-survey.step')) {
            return $fail;
        }

        try {
            [$val, $paid, $pending, $pStatus] = $this->wizardPayment($in);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('dgps-survey.step', 5)->withErrors($e->errors())->withInput();
        }

        // Resume / edit: update the same survey instead of creating a duplicate
        $existing = !empty($draft['id']) ? DgpsSurvey::find($draft['id']) : null;

        DB::beginTransaction();
        try {
            $data = [
                'field_book_no'        => $in['field_book_no'],
                'customer_id'          => (int) $in['customer_id'],
                'lease_application_id' => $in['lease_application_id'] ? (int) $in['lease_application_id'] : null,
                'lease_area_ha'        => $in['lease_area_ha'] !== null ? (float) $in['lease_area_ha'] : null,
                'surveyed_area_ha'     => $in['surveyed_area_ha'] !== null ? (float) $in['surveyed_area_ha'] : null,
                'area_discrepancy_ha'  => $in['area_discrepancy_ha'],
                'location'             => $in['location'],
                'survey_date'          => $in['survey_date'],
                'instrument_model'     => $in['instrument_model'],
                'instrument_serial_no' => $in['instrument_serial_no'],
                'product_value'        => $val,
                'paid_amount'          => $paid,
                'pending_amount'       => $pending,
                'payment_status'       => $pStatus,
                'survey_team_notes'    => $in['survey_team_notes'],
            ];

            if ($existing) {
                $existing->update($data);
                $survey = $existing;
            } else {
                $survey = DgpsSurvey::create($data + [
                    'survey_no'     => $this->resolveNumber(DgpsSurvey::class, 'survey_no', $in['survey_no'], 'DGPS-' . date('Y') . '-'),
                    'survey_status' => 'scheduled',
                    'report_status' => 'pending',
                ]);
            }

            // Handlers: replace with what the wizard holds (no placeholder names)
            ApplicationHandler::where('application_type', 'dgps')->where('application_id', $survey->id)->delete();
            foreach ((array) $request->input('handlers', $draft['handlers'] ?? []) as $hIdx => $h) {
                $name = trim((string) ($h['person_name'] ?? $h['name'] ?? ''));
                if ($name !== '') {
                    ApplicationHandler::create([
                        'application_type' => 'dgps',
                        'application_id'   => $survey->id,
                        'name'             => $name,
                        'role'             => trim((string) ($h['role'] ?? '')),
                        'notes'            => $h['notes'] ?? null,
                        'sort_order'       => $hIdx + 1,
                    ]);
                }
            }

            ApplicationPayment::updateOrCreate(
                ['application_type' => 'dgps', 'application_id' => $survey->id],
                [
                    'product_value'  => $val,
                    'paid_amount'    => $paid,
                    'pending_amount' => $pending,
                    'payment_status' => $pStatus,
                    'notes'          => $in['payment_notes'],
                ]
            );

            // Documents uploaded in this wizard session, inside the same transaction
            foreach ((array) session('dgps_wizard.uploaded_docs', $draft['uploaded_docs'] ?? []) as $uDoc) {
                if (empty($uDoc['file_path']) || DgpsDocument::where('dgps_survey_id', $survey->id)->where('file_path', $uDoc['file_path'])->exists()) {
                    continue;
                }
                DgpsDocument::create([
                    'dgps_survey_id' => $survey->id,
                    'folder_id'      => $uDoc['folder_id'] ?? 1,
                    'document_name'  => $uDoc['doc_name'] ?? ($uDoc['document_name'] ?? ($uDoc['file_name'] ?? basename($uDoc['file_path']))),
                    'file_path'      => $uDoc['file_path'],
                    'status'         => 'uploaded',
                ]);
            }

            DB::commit();
            session()->forget('dgps_wizard');

            return redirect()->route('dgps-survey.show', $survey->id)
                ->with('success', "DGPS Survey {$survey->survey_no} " . ($existing ? 'updated.' : 'saved.'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->storeFailed('DGPS survey', $e, 'dgps-survey.step', 6);
        }
    }

    /**
     * Show Survey Dossier
     */
    public function show($id)
    {
        $survey = DgpsSurvey::with(['customer', 'leaseApplication', 'handlers', 'payments', 'documents', 'points', 'surveyor'])->findOrFail($id);
        return view('pages.dgps_survey.show', compact('survey'));
    }

    /**
     * Upload Document via AJAX (Direct File or Chunked Upload Token)
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
                'max:524288000', // 500 MB
                function ($attribute, $value, $fail) use ($allowedExtensions) {
                    if ($value instanceof \Illuminate\Http\UploadedFile) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        if (!in_array($ext, $allowedExtensions, true)) {
                            $fail("The {$attribute} must be a file of type: " . implode(', ', $allowedExtensions));
                        }
                    }
                }
            ],
            'upload_token'    => 'required_without:file|nullable|string',
            'dgps_survey_id'  => 'nullable',
            'survey_id'       => 'nullable',
            'doc_item'        => 'nullable',
            'doc_id'          => 'nullable',
            'folder_id'       => 'nullable|integer',
            'document_name'   => 'nullable|string|max:255',
            'is_mandatory'    => 'nullable',
        ]);

        $surveyId = $request->input('dgps_survey_id') ?: $request->input('survey_id');
        $rawDocItem = $request->input('doc_item') ?? $request->input('doc_id');
        $docNameInput = $request->input('document_name');

        // Resolve existing file path to safely delete old file on replacement/re-upload
        $oldFilePath = null;
        $existingDoc = null;
        if ($request->filled('doc_id')) {
            $existingDoc = DgpsDocument::find($request->input('doc_id'));
            $oldFilePath = $existingDoc?->file_path;
        } elseif ($surveyId) {
            $q = DgpsDocument::where('dgps_survey_id', $surveyId);
            if ($docNameInput) {
                $q->where(function ($sq) use ($docNameInput) {
                    $sq->where('document_name', $docNameInput)
                       ->orWhere('document_name', 'like', '%' . $docNameInput . '%');
                });
            }
            $existingDoc = $q->first();
            $oldFilePath = $existingDoc?->file_path;
        }

        $sessionDocs = session('dgps_wizard.uploaded_docs', []);
        $docItemKey = $rawDocItem ?? ($docNameInput ?? 'doc_0');
        if (!$oldFilePath && isset($sessionDocs[$docItemKey]['file_path'])) {
            $oldFilePath = $sessionDocs[$docItemKey]['file_path'];
        }

        // Handle Chunked Upload Token
        if ($request->filled('upload_token')) {
            $upload = ChunkedUpload::where('upload_token', $request->input('upload_token'))->where('user_id', auth()->id())->firstOrFail();
            $surveyId = $surveyId ?? $upload->reference_id;

            if (!$upload->isAssembled()) {
                $assembly = app(ChunkedUploadService::class)->assembleFile($upload->upload_token, 'dgps', $surveyId);
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
            $fullUploadPath = public_path($filePath);
            $fileSize = file_exists($fullUploadPath) ? filesize($fullUploadPath) : $upload->total_size;
            $folderId = (int)($request->input('folder_id') ?? ($upload->metadata['folder_id'] ?? (Folder::first()?->id ?? 1)));
            $docName = $docNameInput ?: ($upload->metadata['document_name'] ?? $fileName);
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'], true);
            $sizeFormatted = $fileSize >= 1048576 ? round($fileSize / 1048576, 2) . ' MB' : round($fileSize / 1024, 1) . ' KB';

            $docId = null;
            if ($surveyId) {
                if ($existingDoc) {
                    $existingDoc->update([
                        'folder_id'     => $folderId,
                        'document_name' => $docName,
                        'file_path'     => $filePath,
                        'status'        => 'uploaded',
                    ]);
                    $docId = $existingDoc->id;
                } else {
                    $doc = DgpsDocument::create([
                        'dgps_survey_id' => $surveyId,
                        'folder_id'      => $folderId,
                        'document_name'  => $docName,
                        'file_path'      => $filePath,
                        'status'         => 'uploaded',
                    ]);
                    $docId = $doc->id;
                }

                $upload->update([
                    'target_module' => 'dgps',
                    'reference_id'  => $surveyId,
                    'status'        => 'completed',
                ]);
            }

            $sessionDocs[$docItemKey] = [
                'doc_item'        => $docItemKey,
                'doc_id'          => $docId,
                'doc_name'        => $docName,
                'file_name'       => $fileName,
                'file_path'       => $filePath,
                'file_url'        => asset($filePath),
                'file_size'       => $sizeFormatted,
                'file_size_bytes' => $fileSize,
                'is_zip'          => $isZip,
                'folder_id'       => $folderId,
                'uploaded_at'     => now()->format('d M Y, h:i A'),
            ];
            session(['dgps_wizard.uploaded_docs' => $sessionDocs]);

            return response()->json([
                'status'       => 1,
                'success'      => true,
                'message'      => 'Document uploaded successfully',
                'doc_id'       => $docId,
                'doc_item'     => $docItemKey,
                'folder_id'    => $folderId,
                'upload_token' => $upload->upload_token,
                'file_name'    => $fileName,
                'file_path'    => $filePath,
                'file_url'     => asset($filePath),
                'file_size'    => $sizeFormatted,
                'size_bytes'   => $fileSize,
                'is_zip'       => $isZip,
                'uploaded'     => count($sessionDocs),
                'total'        => 6,
                'percent'      => min(100, round((count($sessionDocs) / 6) * 100)),
            ]);
        }

        // Direct multipart file upload
        $file = $request->file('file');
        $uploadSubdir = 'uploads/dgps';
        $fullUploadPath = public_path($uploadSubdir);
        if (!file_exists($fullUploadPath)) {
            mkdir($fullUploadPath, 0777, true);
        }

        $origFileName = substr((string)$file->getClientOriginalName(), 0, 255);
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $origFileName);
        $targetRelPath = $uploadSubdir . '/' . $safeName;

        // Delete old file if replacing
        if ($oldFilePath && $oldFilePath !== $targetRelPath) {
            if (file_exists(public_path($oldFilePath))) @unlink(public_path($oldFilePath));
            if (file_exists(public_path('storage/' . $oldFilePath))) @unlink(public_path('storage/' . $oldFilePath));
            if (file_exists(storage_path('app/public/' . $oldFilePath))) @unlink(storage_path('app/public/' . $oldFilePath));
        }

        $file->move($fullUploadPath, $safeName);
        $fileSize = file_exists($fullUploadPath . '/' . $safeName) ? filesize($fullUploadPath . '/' . $safeName) : $file->getSize();
        $folderId = (int)($request->input('folder_id') ?? (Folder::first()?->id ?? 1));
        $docName = $docNameInput ?: $origFileName;
        $ext = strtolower(pathinfo($origFileName, PATHINFO_EXTENSION));
        $isZip = in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'], true);
        $sizeFormatted = $fileSize >= 1048576 ? round($fileSize / 1048576, 2) . ' MB' : round($fileSize / 1024, 1) . ' KB';

        $docId = null;
        if ($surveyId) {
            if ($existingDoc) {
                $existingDoc->update([
                    'folder_id'     => $folderId,
                    'document_name' => $docName,
                    'file_path'     => $targetRelPath,
                    'status'        => 'uploaded',
                ]);
                $docId = $existingDoc->id;
            } else {
                $doc = DgpsDocument::create([
                    'dgps_survey_id' => $surveyId,
                    'folder_id'      => $folderId,
                    'document_name'  => $docName,
                    'file_path'      => $targetRelPath,
                    'status'         => 'uploaded',
                ]);
                $docId = $doc->id;
            }
        }

        $sessionDocs[$docItemKey] = [
            'doc_item'        => $docItemKey,
            'doc_id'          => $docId,
            'doc_name'        => $docName,
            'file_name'       => $origFileName,
            'file_path'       => $targetRelPath,
            'file_url'        => asset($targetRelPath),
            'file_size'       => $sizeFormatted,
            'file_size_bytes' => $fileSize,
            'is_zip'          => $isZip,
            'folder_id'       => $folderId,
            'uploaded_at'     => now()->format('d M Y, h:i A'),
        ];
        session(['dgps_wizard.uploaded_docs' => $sessionDocs]);

        return response()->json([
            'status'       => 1,
            'success'      => true,
            'message'      => 'Document uploaded successfully',
            'doc_id'       => $docId,
            'doc_item'     => $docItemKey,
            'folder_id'    => $folderId,
            'file_name'    => $origFileName,
            'file_path'    => $targetRelPath,
            'file_url'     => asset($targetRelPath),
            'file_size'    => $sizeFormatted,
            'size_bytes'   => $fileSize,
            'is_zip'       => $isZip,
            'uploaded'     => count($sessionDocs),
            'total'        => 6,
            'percent'      => min(100, round((count($sessionDocs) / 6) * 100)),
        ]);
    }

    /**
     * Remove an uploaded DGPS Survey document from draft session and database.
     */
    public function removeDocument(Request $request)
    {
        $request->validate([
            'doc_item'       => 'nullable',
            'doc_id'         => 'nullable|integer',
            'dgps_survey_id' => 'nullable',
            'survey_id'      => 'nullable',
            'document_name'  => 'nullable|string',
            'folder_id'      => 'nullable|integer',
        ]);

        $surveyId = $request->input('dgps_survey_id') ?? $request->input('survey_id');
        $docItem = $request->input('doc_item') ?? $request->input('doc_id') ?? $request->input('document_name');

        // 1. Remove from database
        $dbDoc = null;
        if ($request->filled('doc_id')) {
            $dbDoc = DgpsDocument::find($request->input('doc_id'));
        } elseif ($surveyId) {
            $q = DgpsDocument::where('dgps_survey_id', $surveyId);
            if ($request->filled('document_name')) {
                $name = $request->input('document_name');
                $q->where(function ($sq) use ($name) {
                    $sq->where('document_name', $name);
                });
            }
            $dbDoc = $q->first();
        }

        if ($dbDoc) {
            $filePath = $dbDoc->file_path;
            if ($filePath) {
                if (file_exists(public_path($filePath))) @unlink(public_path($filePath));
                if (file_exists(public_path('storage/' . $filePath))) @unlink(public_path('storage/' . $filePath));
                if (file_exists(storage_path('app/public/' . $filePath))) @unlink(storage_path('app/public/' . $filePath));
            }
            $dbDoc->delete();
        }

        // 2. Remove from wizard session
        $sessionDocs = session('dgps_wizard.uploaded_docs', []);
        if ($docItem !== null && isset($sessionDocs[$docItem])) {
            $sessionPath = $sessionDocs[$docItem]['file_path'] ?? null;
            if ($sessionPath) {
                if (file_exists(public_path($sessionPath))) @unlink(public_path($sessionPath));
                if (file_exists(public_path('storage/' . $sessionPath))) @unlink(public_path('storage/' . $sessionPath));
                if (file_exists(storage_path('app/public/' . $sessionPath))) @unlink(storage_path('app/public/' . $sessionPath));
            }
            unset($sessionDocs[$docItem]);
            session(['dgps_wizard.uploaded_docs' => $sessionDocs]);
        }

        return response()->json([
            'status'   => 1,
            'success'  => true,
            'message'  => 'Document removed successfully.',
            'doc_item' => $docItem,
            'uploaded' => count($sessionDocs),
            'total'    => 6,
            'percent'  => min(100, round((count($sessionDocs) / 6) * 100)),
        ]);
    }
}
