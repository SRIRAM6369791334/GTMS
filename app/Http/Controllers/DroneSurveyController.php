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

        // Safe customer ID resolution
        $customerId = $request->filled('customer_id')
            ? (int) $request->input('customer_id')
            : (!empty($draft['customer_id']) ? (int) $draft['customer_id'] : null);

        if (!$customerId || !Customer::where('id', $customerId)->exists()) {
            $customerId = Customer::value('id') ?? 1;
        }

        $val     = (float) ($request->input('product_value', $draft['product_value'] ?? 50000));
        $paid    = (float) ($request->input('paid_amount', $draft['paid_amount'] ?? 30000));
        $pending = max(0, $val - $paid);
        $pStatus = strtolower($request->input('payment_status', $draft['payment_status'] ?? ''));
        if (!in_array($pStatus, ['pending', 'partial', 'paid'])) {
            $pStatus = ($pending <= 0) ? 'paid' : (($paid > 0) ? 'partial' : 'pending');
        }

        // Safe unique survey number generation
        $surveyNo = $request->input('survey_no') ?: ($draft['survey_no'] ?? null);
        if (!$surveyNo || DroneSurvey::where('survey_no', $surveyNo)->exists()) {
            $count = DroneSurvey::count() + 1;
            do {
                $candidate = sprintf('DRN-%s-%04d', date('Y'), $count);
                $count++;
            } while (DroneSurvey::where('survey_no', $candidate)->exists());
            $surveyNo = $candidate;
        }

        // Safe foreign key validation for lease_application_id and mining_application_id
        $leaseAppId = $request->filled('lease_application_id')
            ? (int) $request->input('lease_application_id')
            : (!empty($draft['lease_application_id']) ? (int) $draft['lease_application_id'] : null);
        if ($leaseAppId && !DB::table('lease_applications')->where('id', $leaseAppId)->exists()) {
            $leaseAppId = null;
        }

        $miningAppId = $request->filled('mining_application_id')
            ? (int) $request->input('mining_application_id')
            : (!empty($draft['mining_application_id']) ? (int) $draft['mining_application_id'] : null);
        if ($miningAppId && !DB::table('mining_applications')->where('id', $miningAppId)->exists()) {
            $miningAppId = null;
        }

        $branchId = auth()->user()->branch_id ?? 1;
        if (!DB::table('branches')->where('id', $branchId)->exists()) {
            $branchId = DB::table('branches')->value('id');
        }

        $createdBy = Auth::id() ?? 1;
        if (!DB::table('users')->where('id', $createdBy)->exists()) {
            $createdBy = DB::table('users')->value('id');
        }

        $validStatuses = ['scheduled', 'flying_completed', 'processing', 'deliverables_ready', 'report_signed'];
        $surveyStatus = $draft['survey_status'] ?? 'deliverables_ready';
        if (!in_array($surveyStatus, $validStatuses)) {
            $surveyStatus = 'deliverables_ready';
        }

        DB::beginTransaction();
        try {
            $survey = DroneSurvey::create([
                'survey_no'              => $surveyNo,
                'customer_id'            => $customerId,
                'lease_application_id'   => $leaseAppId,
                'mining_application_id'  => $miningAppId,
                'lease_area'             => (float) ($draft['lease_area'] ?? 3.85),
                'location'               => $request->input('location', $draft['location'] ?? 'Quarry Site'),
                'flight_date'            => $request->input('flight_date', $draft['flight_date'] ?? date('Y-m-d')),
                'drone_pilot_name'       => $draft['drone_pilot_name'] ?? 'S. Karthik',
                'pilot_rpc_no'           => $draft['pilot_rpc_no'] ?? null,
                'drone_uin_no'           => $draft['drone_uin_no'] ?? null,
                'drone_model'            => $draft['drone_model'] ?? 'DJI Matrice 300 RTK',
                'altitude_meters'        => (float) ($draft['altitude_meters'] ?? 120),
                'gsd_cm_px'              => (float) ($draft['gsd_cm_px'] ?? 2.5),
                'extracted_volume_cbm'   => (float) ($draft['extracted_volume_cbm'] ?? 18450),
                'survey_status'          => $surveyStatus,
                'deliverable_files_path' => $draft['deliverable_files_path'] ?? null,
                'gtms_report_file'       => $draft['gtms_report_file'] ?? null,
                'product_value'          => $val,
                'paid_amount'            => $paid,
                'pending_amount'         => $pending,
                'payment_status'         => $pStatus,
                'branch_id'              => $branchId,
                'created_by'             => $createdBy,
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
                    'name'             => 'S. Karthik',
                    'role'             => 'DGCA Certified Drone Pilot',
                    'notes'            => 'Autonomous grid flight execution & safety compliance',
                    'sort_order'       => 1,
                ]);
                ApplicationHandler::create([
                    'application_type' => 'drone',
                    'application_id'   => $survey->id,
                    'name'             => 'M. Vignesh',
                    'role'             => 'Photogrammetry & 3D Analyst',
                    'notes'            => 'Image stitching, GCP georeferencing & cut/fill volume calculation',
                    'sort_order'       => 2,
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

            // Migrate wizard documents to DB
            $uploadedDocs = session('drone_wizard.uploaded_docs', $draft['uploaded_docs'] ?? []);
            if (!empty($uploadedDocs)) {
                foreach ($uploadedDocs as $uDoc) {
                    DroneDocument::create([
                        'drone_survey_id' => $survey->id,
                        'folder_id'       => $uDoc['folder_id'] ?? 1,
                        'document_name'   => $uDoc['doc_name'] ?? ($uDoc['document_name'] ?? $uDoc['file_name']),
                        'file_path'       => $uDoc['file_path'],
                        'file_size'       => $uDoc['file_size_bytes'] ?? ($uDoc['file_size'] ?? 0),
                        'status'          => 'uploaded',
                    ]);
                }
            }

            session()->forget('drone_wizard');

            DB::commit();
            return redirect()->route('drone-survey.show', $survey->id)
                ->with('success', "Drone Survey {$surveyNo} filed successfully!");

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Drone Survey store failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->route('drone-survey.step', 6)
                ->with('error', 'Failed to save Drone Survey: ' . $e->getMessage());
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
