<?php

namespace App\Http\Controllers;

use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use App\Models\Customer;
use App\Models\District;
use App\Models\DroneSurvey;
use App\Models\DroneDocument;
use App\Models\ChunkedUpload;
use App\Services\ChunkedUploadService;
use App\Models\Folder;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DroneSurveyController extends Controller
{
    use \App\Http\Controllers\Concerns\WizardStoreHelpers;

    /**
     * Master Index Listing (Dynamic Database Records & KPI Cards)
     */
    public function index(Request $request)
    {
        $query = DroneSurvey::with(['customer.district', 'leaseApplication.district', 'documents']);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('survey_no', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('drone_pilot_name', 'like', "%{$search}%")
                  ->orWhere('drone_model', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%")
                         ->orWhere('company_name', 'like', "%{$search}%")
                         ->orWhere('mimas_no', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'scheduled') {
                $query->where('survey_status', 'scheduled');
            } elseif ($status === 'acquisition' || $status === 'flying' || $status === 'processing') {
                $query->whereIn('survey_status', ['flying', 'data_acquisition', 'processing', 'data_processing']);
            } elseif ($status === 'deliverables_ready') {
                $query->where('survey_status', 'deliverables_ready');
            } elseif ($status === 'completed' || $status === 'uploaded') {
                $query->whereIn('survey_status', ['completed', 'gtms_uploaded']);
            }
        }

        $surveys = $query->latest()->paginate(10)->withQueryString();

        // Real Dynamic KPI Counts
        $totalRequests          = DroneSurvey::count();
        $dataAcquisitionCount   = DroneSurvey::whereIn('survey_status', ['scheduled', 'flying', 'data_acquisition', 'processing', 'data_processing'])->count();
        $deliverablesReadyCount = DroneSurvey::where('survey_status', 'deliverables_ready')->count();
        $gtmsUploadedCount      = DroneSurvey::whereIn('survey_status', ['completed', 'gtms_uploaded'])->count();

        return view('pages.drone_survey.index', compact(
            'surveys',
            'totalRequests',
            'dataAcquisitionCount',
            'deliverablesReadyCount',
            'gtmsUploadedCount'
        ));
    }

    public function show(int $id)
    {
        $survey = DroneSurvey::with([
            'customer.district', 'leaseApplication.district', 'miningApplication',
            'handlers', 'payments', 'documents', 'creator'
        ])->findOrFail($id);

        return view('pages.drone_survey.show', compact('survey'));
    }

    public function wizard(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 6, 404);

        // "New" buttons pass ?new=1: start clean instead of reusing an old draft
        if ($request->boolean('new')) {
            session()->forget('drone_wizard');
        }

        $draft = session('drone_wizard', []);

        // Resume existing survey
        if ($step === 1 && $request->filled('resume') && (int) ($draft['drone_survey_id'] ?? 0) !== (int) $request->input('resume')) {
            $existing = DroneSurvey::with(['customer', 'handlers'])->find($request->input('resume'));
            if ($existing) {
                $draft = [
                    'drone_survey_id'       => $existing->id,
                    'survey_no'             => $existing->survey_no,
                    'survey_status'         => $existing->survey_status,
                    'deliverable_files_path'=> $existing->deliverable_files_path,
                    'handlers'              => $existing->handlers->map(fn ($h) => ['name' => $h->name, 'role' => $h->role, 'notes' => $h->notes])->toArray(),
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
                ];
                session(['drone_wizard' => $draft]);
            }
        }

        if ($step === 1 && $request->filled('customer_id') && empty($draft['customer_id'])) {
            $draft['customer_id'] = (int) $request->input('customer_id');
            session(['drone_wizard' => $draft]);
        }

        $customers = Customer::withTrashed()->orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();
        $leaseApplications = LeaseApplication::with('customer')->orderByDesc('id')->get();
        $uploadedDocs = session('drone_wizard.uploaded_docs', $draft['uploaded_docs'] ?? []);

        return view('pages.drone_survey.wizard', compact('step', 'draft', 'customers', 'districts', 'leaseApplications', 'uploadedDocs'));
    }

    public function saveStep(int $step, Request $request)
    {
        abort_unless($step >= 1 && $step <= 6, 404);

        $draft = session('drone_wizard', []);

        if ($step === 1) {
            $draft['survey_no']             = $request->input('survey_no');
            $draft['customer_id']           = $request->filled('customer_id') ? (int) $request->input('customer_id') : ($draft['customer_id'] ?? null);
            $draft['lease_application_id']  = $request->filled('lease_application_id') ? (int) $request->input('lease_application_id') : null;
            $draft['mining_application_id'] = $request->filled('mining_application_id') ? (int) $request->input('mining_application_id') : null;
            $draft['applicant_name']        = $request->input('applicant_name');
            $draft['location']              = $request->input('location');
            $draft['lease_area']            = $request->input('lease_area');
            $draft['survey_type']           = $request->input('survey_type');
            $draft['concession_type']       = $request->input('concession_type');
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

        if ($step === 4) {
            $draft['handlers'] = $request->input('handlers', []);
        }

        if ($step === 5) {
            $val  = (float) $request->input('product_value', 0);
            $paid = (float) $request->input('paid_amount', 0);
            $draft['product_value']  = $val;
            $draft['paid_amount']    = $paid;
            $draft['pending_amount'] = max(0, $val - $paid);
            $draft['payment_status'] = $request->input('payment_status', 'pending');
            $draft['payment_notes']  = $request->input('payment_notes');
        }

        session(['drone_wizard' => $draft]);

        if ($request->input('save_draft_exit') == '1') {
            return redirect()->route('drone-survey.index')->with('success', 'Drone survey progress saved to draft.');
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'next_step' => $step + 1]);
        }

        if ($step < 6) {
            return redirect()->route('drone-survey.step', $step + 1);
        }

        return redirect()->route('drone-survey.step', 6);
    }

    public function store(Request $request)
    {
        $draft = session('drone_wizard', []);
        $in = $this->wizardInput($request, $draft, [
            'customer_id', 'lease_application_id', 'mining_application_id', 'survey_no', 'lease_area', 'location',
            'flight_date', 'drone_pilot_name', 'pilot_rpc_no', 'drone_uin_no', 'drone_model', 'altitude_meters',
            'gsd_cm_px', 'extracted_volume_cbm', 'survey_status', 'deliverable_files_path', 'gtms_report_file',
            'primary_phone', 'secondary_phone', 'product_value', 'paid_amount', 'payment_status', 'payment_notes',
        ]);
        if ($in['payment_status'] !== null) {
            $in['payment_status'] = strtolower($in['payment_status']);
        }
        // A link to a lease / mining plan that no longer exists (stale draft) is dropped, not an error
        foreach (['lease_application_id' => 'lease_applications', 'mining_application_id' => 'mining_applications'] as $key => $table) {
            if ($in[$key] && !DB::table($table)->where('id', (int) $in[$key])->exists()) {
                $in[$key] = null;
            }
        }

        if ($fail = $this->validateWizard($in, [
            'customer_id'           => 'required|integer|exists:customers,id',
            'lease_application_id'  => 'nullable|integer|exists:lease_applications,id',
            'mining_application_id' => 'nullable|integer|exists:mining_applications,id',
            'survey_no'             => 'nullable|string|max:50',
            'lease_area'            => 'nullable|numeric|min:0',
            'location'              => 'nullable|string|max:255',
            'flight_date'           => 'nullable|date',
            'drone_pilot_name'      => 'nullable|string|max:255',
            'pilot_rpc_no'          => 'nullable|string|max:100',
            'drone_uin_no'          => 'nullable|string|max:100',
            'drone_model'           => 'nullable|string|max:255',
            'altitude_meters'       => 'nullable|numeric|min:0',
            'gsd_cm_px'             => 'nullable|numeric|min:0',
            'extracted_volume_cbm'  => 'nullable|numeric|min:0',
            'survey_status'         => 'nullable|in:scheduled,flying_completed,processing,deliverables_ready,report_signed',
            'primary_phone'         => ['nullable', $this->mobileRule],
            'secondary_phone'       => ['nullable', $this->mobileRule, 'different:primary_phone'],
            'product_value'         => 'nullable|numeric|min:0',
            'paid_amount'           => 'nullable|numeric|min:0',
            'payment_status'        => 'nullable|in:pending,partial,paid',
            'payment_notes'         => 'nullable|string|max:1000',
        ], [
            'customer_id' => 1, 'lease_application_id' => 1, 'mining_application_id' => 1, 'survey_no' => 1,
            'lease_area' => 1, 'location' => 1, 'primary_phone' => 1, 'secondary_phone' => 1,
            'flight_date' => 2, 'drone_pilot_name' => 2, 'pilot_rpc_no' => 2, 'drone_uin_no' => 2, 'drone_model' => 2,
            'altitude_meters' => 2, 'gsd_cm_px' => 2, 'extracted_volume_cbm' => 3, 'survey_status' => 3,
            'product_value' => 5, 'paid_amount' => 5, 'payment_status' => 5, 'payment_notes' => 5,
        ], 'drone-survey.step')) {
            return $fail;
        }

        try {
            [$val, $paid, $pending, $pStatus] = $this->wizardPayment($in);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('drone-survey.step', 5)->withErrors($e->errors())->withInput();
        }

        $num = fn ($v) => $v !== null ? (float) $v : null;
        $existing = !empty($draft['drone_survey_id']) ? DroneSurvey::find($draft['drone_survey_id']) : null;

        DB::beginTransaction();
        try {
            $data = [
                'customer_id'            => (int) $in['customer_id'],
                'lease_application_id'   => $in['lease_application_id'] ? (int) $in['lease_application_id'] : null,
                'mining_application_id'  => $in['mining_application_id'] ? (int) $in['mining_application_id'] : null,
                'lease_area'             => $num($in['lease_area']),
                'location'               => $in['location'],
                'flight_date'            => $in['flight_date'],
                'drone_pilot_name'       => $in['drone_pilot_name'],
                'pilot_rpc_no'           => $in['pilot_rpc_no'],
                'drone_uin_no'           => $in['drone_uin_no'],
                'drone_model'            => $in['drone_model'],
                'altitude_meters'        => $num($in['altitude_meters']),
                'gsd_cm_px'              => $num($in['gsd_cm_px']),
                'extracted_volume_cbm'   => $num($in['extracted_volume_cbm']),
                'survey_status'          => $in['survey_status'] ?? ($existing->survey_status ?? 'scheduled'),
                'deliverable_files_path' => $in['deliverable_files_path'],
                'gtms_report_file'       => $in['gtms_report_file'] ?? ($existing->gtms_report_file ?? null),
                'product_value'          => $val,
                'paid_amount'            => $paid,
                'pending_amount'         => $pending,
                'payment_status'         => $pStatus,
            ];

            if ($existing) {
                $existing->update($data);
                $survey = $existing;
            } else {
                $survey = DroneSurvey::create($data + [
                    'survey_no'  => $this->resolveNumber(DroneSurvey::class, 'survey_no', $in['survey_no'], 'DRN-' . date('Y') . '-'),
                    'branch_id'  => auth()->user()->branch_id ?? null,
                    'created_by' => Auth::id(),
                ]);
            }

            // Handlers: replace with what the wizard holds (no placeholder names)
            ApplicationHandler::where('application_type', 'drone')->where('application_id', $survey->id)->delete();
            foreach ((array) $request->input('handlers', $draft['handlers'] ?? []) as $hIdx => $h) {
                $name = trim((string) ($h['person_name'] ?? $h['name'] ?? ''));
                if ($name !== '') {
                    ApplicationHandler::create([
                        'application_type' => 'drone',
                        'application_id'   => $survey->id,
                        'name'             => $name,
                        'role'             => trim((string) ($h['role'] ?? '')),
                        'notes'            => $h['notes'] ?? null,
                        'sort_order'       => $hIdx + 1,
                    ]);
                }
            }

            ApplicationPayment::updateOrCreate(
                ['application_type' => 'drone', 'application_id' => $survey->id],
                [
                    'product_value'  => $val,
                    'paid_amount'    => $paid,
                    'pending_amount' => $pending,
                    'payment_status' => $pStatus,
                    'notes'          => $in['payment_notes'],
                ]
            );

            foreach ((array) session('drone_wizard.uploaded_docs', $draft['uploaded_docs'] ?? []) as $uDoc) {
                if (empty($uDoc['file_path']) || DroneDocument::where('drone_survey_id', $survey->id)->where('file_path', $uDoc['file_path'])->exists()) {
                    continue;
                }
                DroneDocument::create([
                    'drone_survey_id' => $survey->id,
                    'folder_id'       => $uDoc['folder_id'] ?? 1,
                    'document_name'   => $uDoc['doc_name'] ?? ($uDoc['document_name'] ?? ($uDoc['file_name'] ?? basename($uDoc['file_path']))),
                    'file_path'       => $uDoc['file_path'],
                    'file_size'       => $uDoc['file_size_bytes'] ?? ($uDoc['file_size'] ?? 0),
                    'status'          => 'uploaded',
                ]);
            }

            DB::commit();
            session()->forget('drone_wizard');

            return redirect()->route('drone-survey.show', $survey->id)
                ->with('success', "Drone Survey {$survey->survey_no} " . ($existing ? 'updated.' : 'filed successfully!'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->storeFailed('drone survey', $e, 'drone-survey.step', 6);
        }
    }

    /**
     * Upload Drone Survey Document / Deliverable via AJAX (Direct File or Chunked Upload Token)
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
            'drone_survey_id' => 'nullable',
            'survey_id'       => 'nullable',
            'doc_item'        => 'nullable',
            'doc_id'          => 'nullable',
            'folder_id'       => 'nullable|integer',
            'document_name'   => 'nullable|string|max:255',
            'is_mandatory'    => 'nullable',
        ]);

        $surveyId = $request->input('drone_survey_id') ?: $request->input('survey_id');
        $rawDocItem = $request->input('doc_item') ?? $request->input('doc_id');
        $docNameInput = $request->input('document_name');

        // Resolve existing file path to safely delete old file on replacement/re-upload
        $oldFilePath = null;
        $existingDoc = null;
        if ($request->filled('doc_id')) {
            $existingDoc = DroneDocument::find($request->input('doc_id'));
            $oldFilePath = $existingDoc?->file_path;
        } elseif ($surveyId) {
            $q = DroneDocument::where('drone_survey_id', $surveyId);
            if ($docNameInput) {
                $q->where(function ($sq) use ($docNameInput) {
                    $sq->where('document_name', $docNameInput)
                       ->orWhere('document_name', 'like', '%' . $docNameInput . '%');
                });
            }
            $existingDoc = $q->first();
            $oldFilePath = $existingDoc?->file_path;
        }

        $sessionDocs = session('drone_wizard.uploaded_docs', []);
        $docItemKey = $rawDocItem ?? ($docNameInput ?? 'doc_0');
        if (!$oldFilePath && isset($sessionDocs[$docItemKey]['file_path'])) {
            $oldFilePath = $sessionDocs[$docItemKey]['file_path'];
        }

        // Handle Chunked Upload Token
        if ($request->filled('upload_token')) {
            $upload = ChunkedUpload::where('upload_token', $request->input('upload_token'))->where('user_id', auth()->id())->firstOrFail();
            $surveyId = $surveyId ?? $upload->reference_id;

            if (!$upload->isAssembled()) {
                $assembly = app(ChunkedUploadService::class)->assembleFile($upload->upload_token, 'drone', $surveyId);
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
                        'file_size'     => $fileSize,
                        'status'        => 'uploaded',
                    ]);
                    $docId = $existingDoc->id;
                } else {
                    $doc = DroneDocument::create([
                        'drone_survey_id' => $surveyId,
                        'folder_id'       => $folderId,
                        'document_name'   => $docName,
                        'file_path'       => $filePath,
                        'file_size'       => $fileSize,
                        'status'          => 'uploaded',
                    ]);
                    $docId = $doc->id;
                }

                $upload->update([
                    'target_module' => 'drone',
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
            session(['drone_wizard.uploaded_docs' => $sessionDocs]);

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
                'total'        => 10,
                'percent'      => min(100, round((count($sessionDocs) / 10) * 100)),
            ]);
        }

        // Direct multipart file upload
        $file = $request->file('file');
        $uploadSubdir = 'uploads/drone';
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
                    'file_size'     => $fileSize,
                    'status'        => 'uploaded',
                ]);
                $docId = $existingDoc->id;
            } else {
                $doc = DroneDocument::create([
                    'drone_survey_id' => $surveyId,
                    'folder_id'       => $folderId,
                    'document_name'   => $docName,
                    'file_path'       => $targetRelPath,
                    'file_size'       => $fileSize,
                    'status'          => 'uploaded',
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
        session(['drone_wizard.uploaded_docs' => $sessionDocs]);

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
            'total'        => 10,
            'percent'      => min(100, round((count($sessionDocs) / 10) * 100)),
        ]);
    }

    /**
     * Remove an uploaded Drone Survey document from draft session and database.
     */
    public function removeDocument(Request $request)
    {
        $request->validate([
            'doc_item'        => 'nullable',
            'doc_id'          => 'nullable|integer',
            'drone_survey_id' => 'nullable',
            'survey_id'       => 'nullable',
            'document_name'   => 'nullable|string',
            'folder_id'       => 'nullable|integer',
        ]);

        $surveyId = $request->input('drone_survey_id') ?? $request->input('survey_id');
        $docItem = $request->input('doc_item') ?? $request->input('doc_id') ?? $request->input('document_name');

        // 1. Remove from database
        $dbDoc = null;
        if ($request->filled('doc_id')) {
            $dbDoc = DroneDocument::find($request->input('doc_id'));
        } elseif ($surveyId) {
            $q = DroneDocument::where('drone_survey_id', $surveyId);
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
        $sessionDocs = session('drone_wizard.uploaded_docs', []);
        if ($docItem !== null && isset($sessionDocs[$docItem])) {
            $sessionPath = $sessionDocs[$docItem]['file_path'] ?? null;
            if ($sessionPath) {
                if (file_exists(public_path($sessionPath))) @unlink(public_path($sessionPath));
                if (file_exists(public_path('storage/' . $sessionPath))) @unlink(public_path('storage/' . $sessionPath));
                if (file_exists(storage_path('app/public/' . $sessionPath))) @unlink(storage_path('app/public/' . $sessionPath));
            }
            unset($sessionDocs[$docItem]);
            session(['drone_wizard.uploaded_docs' => $sessionDocs]);
        }

        return response()->json([
            'status'   => 1,
            'success'  => true,
            'message'  => 'Document removed successfully.',
            'doc_item' => $docItem,
            'uploaded' => count($sessionDocs),
            'total'    => 10,
            'percent'  => min(100, round((count($sessionDocs) / 10) * 100)),
        ]);
    }
}
