<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\ApplicantType;
use App\Models\PlanType;
use App\Models\NatureOfWork;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\Folder;
use App\Models\DocumentField;
use App\Models\EnvironmentProject;
use App\Models\EnvironmentDocument;
use App\Models\ActivityLog;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MiningController extends Controller
{
    /**
     * 1. Mining Plan - Master Applications Table
     */
    public function index()
    {
        $totalApplications = MiningApplication::count();
        $draftCount = MiningApplication::where('status', 'draft')->count();
        $approvedCount = MiningApplication::where('status', 'approved')->count();
        $scrutinyCount = MiningApplication::whereIn('status', ['scrutiny', 'inspection', 'presentation'])->count();
        
        $applications = MiningApplication::with(['customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork', 'documents', 'leaseApplication'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.mining-portal.index', compact(
            'totalApplications',
            'draftCount',
            'approvedCount',
            'scrutinyCount',
            'applications'
        ));
    }

    /**
     * New Application Intake Wizard
     */
    public function newApplication(Request $request)
    {
        $customers = Customer::orderBy('customer_name')->get();
        $districts = District::where('status', 1)->orderBy('name')->get();
        $minerals = Mineral::where('status', 1)->orderBy('name')->get();
        $applicantTypes = ApplicantType::where('status', 1)->orderBy('name')->get();
        $planTypes = PlanType::where('status', 1)->orderBy('name')->get();
        $natureOfWorks = NatureOfWork::where('status', 1)->orderBy('id')->get();

        $miningModuleId = DB::table('modules')->where('code', 'mining')->value('id');
        $folderCounts = [];
        $folders = [];
        $documentFields = [];
        if ($miningModuleId) {
            $folders = Folder::where('module_id', $miningModuleId)->orderBy('sort_order')->get();
            foreach ($natureOfWorks as $now) {
                $counts = [];
                foreach ($folders as $folder) {
                    $count = DocumentField::where('folder_id', $folder->id)
                        ->where('nature_of_work_id', $now->id)->count();
                    $counts[$folder->name] = $count;
                }
                $folderCounts[$now->id] = $counts;
            }
            $documentFields = DocumentField::where('status', 1)
                ->whereIn('folder_id', $folders->pluck('id'))
                ->orderBy('sort_order')
                ->get();
        }

        // Prefill Data (from resume ID or lease_id)
        $prefillData = [];
        if ($request->filled('resume')) {
            $resumeApp = MiningApplication::with([
                'customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork',
                'leaseApplication.surveyNumbers', 'leaseApplication.documents', 'documents'
            ])->find($request->input('resume'));

            if ($resumeApp) {
                $customer = $resumeApp->customer;
                $lease = $resumeApp->leaseApplication;
                $mineralIds = $resumeApp->minerals->isNotEmpty()
                    ? $resumeApp->minerals->pluck('id')->toArray()
                    : ($resumeApp->mineral_id ? [$resumeApp->mineral_id] : ($lease?->minerals?->pluck('id')->toArray() ?? []));

                $prefillData = [
                    'mining_app_id'            => $resumeApp->id,
                    'application_no'           => $resumeApp->application_no,
                    'common_id'                => $resumeApp->common_id,
                    'customer_id'              => $resumeApp->customer_id,
                    'client_name'              => $customer?->customer_name ?? ($lease?->contact_person ?? ''),
                    'company_name'             => $customer?->company_name ?? '',
                    'mimas_no'                 => $customer?->mimas_no ?? '',
                    'secondary_contact_person' => $customer?->secondary_contact_person ?? ($lease?->secondary_contact_person ?? ''),
                    'mobile_num'               => $customer?->mobile_num ?? ($lease?->contact_mobile ?? ''),
                    'secondary_mobile_num'     => $customer?->secondary_mobile_num ?? ($lease?->secondary_contact_mobile ?? ''),
                    'email'                    => $customer?->email ?? '',
                    'pan'                      => $customer?->pan ?? '',
                    'aadhaar_no'               => $customer?->aadhaar_no ?? '',
                    'gstin'                    => $customer?->gstin ?? '',
                    'address'                  => $customer?->address ?? ($lease?->taluk ?? ''),
                    'nature_of_work_id'        => $resumeApp->nature_of_work_id,
                    'applicant_type_id'        => $resumeApp->applicant_type_id,
                    'plan_type_id'             => $resumeApp->plan_type_id,
                    'district_id'              => $resumeApp->district_id,
                    'taluk'                    => $resumeApp->taluk ?? ($lease?->taluk ?? ''),
                    'village'                  => $resumeApp->village ?? ($lease?->village ?? ''),
                    'survey_numbers_text'      => $resumeApp->survey_numbers_text ?? ($lease?->surveyNumbers?->pluck('survey_no')->filter()->join(', ') ?? ''),
                    'area_extent_ha'           => $resumeApp->area_extent_ha ?? ($lease?->area_extent_ha ?? ''),
                    'mineral_ids'              => $mineralIds,
                    'other_mineral_name'       => $resumeApp->other_mineral_name ?? ($lease?->other_mineral_name ?? ''),
                    'existing_docs'            => $resumeApp->documents,
                    'handlers'                 => $resumeApp->handlers ?? collect(),
                    'product_value'            => $resumeApp->product_value ?? 0,
                    'paid_amount'              => $resumeApp->paid_amount ?? 0,
                    'pending_amount'           => $resumeApp->pending_amount ?? 0,
                    'payment_status'           => $resumeApp->payment_status ?? 'pending',
                ];
            }
        } elseif ($request->filled('lease_id')) {
            $lease = \App\Models\LeaseApplication::with(['customer', 'district', 'mineral', 'minerals', 'surveyNumbers', 'documents'])->find($request->input('lease_id'));
            if ($lease) {
                $customer = $lease->customer;
                $mineralIds = $lease->minerals->isNotEmpty()
                    ? $lease->minerals->pluck('id')->toArray()
                    : ($lease->mineral_id ? [$lease->mineral_id] : []);

                $prefillData = [
                    'lease_application_id'     => $lease->id,
                    'common_id'                => $lease->common_id,
                    'customer_id'              => $lease->customer_id,
                    'client_name'              => $customer?->customer_name ?? ($lease->contact_person ?? ''),
                    'company_name'             => $customer?->company_name ?? '',
                    'mimas_no'                 => $customer?->mimas_no ?? '',
                    'secondary_contact_person' => $lease->secondary_contact_person ?? ($customer?->secondary_contact_person ?? ''),
                    'mobile_num'               => $customer?->mobile_num ?? ($lease->contact_mobile ?? ''),
                    'secondary_mobile_num'     => $lease->secondary_contact_mobile ?? ($customer?->secondary_mobile_num ?? ''),
                    'email'                    => $customer?->email ?? '',
                    'pan'                      => $customer?->pan ?? '',
                    'aadhaar_no'               => $customer?->aadhaar_no ?? '',
                    'gstin'                    => $customer?->gstin ?? '',
                    'address'                  => $customer?->address ?? ($lease->taluk ?? ''),
                    'district_id'              => $lease->district_id,
                    'taluk'                    => $lease->taluk,
                    'village'                  => $lease->village,
                    'survey_numbers_text'      => $lease->surveyNumbers->pluck('survey_no')->filter()->join(', '),
                    'area_extent_ha'           => $lease->area_extent_ha,
                    'mineral_ids'              => $mineralIds,
                    'other_mineral_name'       => $lease->other_mineral_name ?? '',
                    'existing_docs'            => $lease->documents,
                    'handlers'                 => $lease->handlers ?? collect(),
                    'product_value'            => $lease->product_value ?? 0,
                    'paid_amount'              => $lease->paid_amount ?? 0,
                    'pending_amount'           => $lease->pending_amount ?? 0,
                    'payment_status'           => $lease->payment_status ?? 'pending',
                ];
            }
        }

        return view('pages.mining-portal.newapplication', compact(
            'customers',
            'districts',
            'minerals',
            'applicantTypes',
            'planTypes',
            'natureOfWorks',
            'folderCounts',
            'folders',
            'documentFields',
            'prefillData'
        ));
    }

    /**
     * Store newly created Mining Application
     */
    public function store(Request $request)
    {
        $now = NatureOfWork::find($request->nature_of_work_id);
        $isMiningPlan = $now && strtolower(trim($now->name)) === 'mining plan';

        $rules = [
            'nature_of_work_id'   => 'required|exists:nature_of_works,id',
            'applicant_type_id'   => 'required|exists:applicant_types,id',
            'district_id'         => 'required|exists:districts,id',
            'taluk'               => 'nullable|string|max:255',
            'village'             => 'nullable|string|max:255',
            'survey_numbers_text' => 'nullable|string|max:500',
            'area_extent_ha'      => 'nullable|numeric',
            'other_mineral_name'  => 'nullable|string|max:255',
        ];

        if ($isMiningPlan) {
            $rules['plan_type_id'] = 'required|exists:plan_types,id';
            // Allow either mineral_ids array or single mineral_id
            if ($request->has('mineral_ids')) {
                $rules['mineral_ids'] = 'required|array|min:1';
                $rules['mineral_ids.*'] = 'exists:minerals,id';
            } else {
                $rules['mineral_id'] = 'required|exists:minerals,id';
            }
        } else {
            $rules['plan_type_id'] = 'nullable|exists:plan_types,id';
            $rules['mineral_ids'] = 'nullable|array';
            $rules['mineral_id'] = 'nullable|exists:minerals,id';
        }

        // Check if customer_id is provided or if new customer details are submitted
        if ($request->filled('customer_id')) {
            $rules['customer_id'] = 'required|exists:customers,id';
        } else {
            $rules['client_name']              = 'required|string|max:255';
            $rules['secondary_contact_person'] = 'nullable|string|max:255';
            $rules['company_name']             = 'required|string|max:255';
            $rules['mobile_num']               = 'required|string|max:20';
            $rules['secondary_mobile_num']     = 'nullable|string|max:20';
            $rules['pan']                      = 'nullable|string|max:10';
            $rules['aadhaar_no']               = 'nullable|string|max:14';
        }

        $request->validate($rules);

        // Resolve mineral IDs array
        $mineralIds = $request->input('mineral_ids', []);
        if (empty($mineralIds) && $request->filled('mineral_id')) {
            $mineralIds = [$request->input('mineral_id')];
        }
        $primaryMineralId = !empty($mineralIds) ? $mineralIds[0] : null;

        // Resolve or create Customer record (Lease Application Pattern)
        $customerId = $request->customer_id;
        $customer = null;
        if ($customerId) {
            $customer = Customer::withTrashed()->find($customerId);
        }
        if (!$customer) {
            $mimasNo = $request->mimas_no ?: ('MMS-' . strtoupper(uniqid()));
            $customer = Customer::withTrashed()->where('mimas_no', $mimasNo)->first();
            if (!$customer && !empty($request->aadhaar_no)) {
                $customer = Customer::withTrashed()->where('aadhaar_no', $request->aadhaar_no)->first();
            }
            if ($customer && $customer->trashed()) {
                $customer->restore();
            }
            if (!$customer) {
                $customer = Customer::create([
                    'customer_name'            => $request->client_name,
                    'secondary_contact_person' => $request->secondary_contact_person,
                    'company_name'             => $request->company_name,
                    'mimas_no'                 => $mimasNo,
                    'mobile_num'               => $request->mobile_num,
                    'secondary_mobile_num'     => $request->secondary_mobile_num,
                    'email'                    => $request->email,
                    'district_id'              => $request->district_id,
                    'mineral_id'               => $primaryMineralId,
                    'pan'                      => $request->pan ?: ('PAN' . substr(strtoupper(uniqid()), 0, 7)),
                    'aadhaar_no'               => $request->aadhaar_no ?: ('9999' . str_pad(mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT)),
                    'gstin'                    => $request->gstin,
                    'area'                     => $request->area_extent_ha,
                    'address'                  => $request->address,
                    'status'                   => 1,
                    'created_by'               => Auth::id(),
                ]);
            } else {
                $custUpdates = [];
                if (!empty($request->secondary_contact_person) && empty($customer->secondary_contact_person)) {
                    $custUpdates['secondary_contact_person'] = $request->secondary_contact_person;
                }
                if (!empty($request->secondary_mobile_num) && empty($customer->secondary_mobile_num)) {
                    $custUpdates['secondary_mobile_num'] = $request->secondary_mobile_num;
                }
                if (!empty($custUpdates)) {
                    $customer->update($custUpdates);
                }
            }
            $customerId = $customer->id;
        }

        $prodVal = (float)$request->input('product_value', 0);
        $paidVal = (float)$request->input('paid_amount', 0);
        $pendingVal = max(0, $prodVal - $paidVal);
        $payStatus = $request->input('payment_status') ?: ($paidVal <= 0 ? 'pending' : ($pendingVal <= 0 ? 'paid' : 'partial'));

        if ($request->filled('mining_app_id')) {
            $miningApp = MiningApplication::findOrFail($request->input('mining_app_id'));
            $appUpdates = [
                'customer_id'         => $customerId,
                'nature_of_work_id'   => $request->nature_of_work_id,
                'applicant_type_id'   => $request->applicant_type_id,
                'district_id'         => $request->district_id,
                'mineral_id'          => $isMiningPlan ? $primaryMineralId : null,
                'other_mineral_name'  => $isMiningPlan ? $request->input('other_mineral_name') : null,
                'plan_type_id'        => $isMiningPlan ? $request->plan_type_id : null,
                'taluk'               => $request->taluk,
                'village'             => $request->village,
                'survey_numbers_text' => $request->survey_numbers_text,
                'area_extent_ha'      => $request->area_extent_ha,
                'product_value'       => $prodVal,
                'paid_amount'         => $paidVal,
                'pending_amount'      => $pendingVal,
                'payment_status'      => $payStatus,
                'status'              => 'draft',
            ];

            // Ensure common_id exists if previously empty
            if (empty($miningApp->common_id)) {
                if (preg_match('/(?:MP|MDG)-(\d{4}-\d+)/', $miningApp->application_no, $m)) {
                    $appUpdates['common_id'] = 'GTMS-' . $m[1];
                } else {
                    $appUpdates['common_id'] = 'GTMS-' . date('Y') . '-' . str_pad($miningApp->id, 4, '0', STR_PAD_LEFT);
                }
            }

            $miningApp->update($appUpdates);
            $applicationNo = $miningApp->application_no;
        } else {
            [$applicationNo, $generatedCommonId] = $this->generateMiningAppNumber();
            $commonId = $request->input('common_id') ?: $generatedCommonId;

            $miningApp = MiningApplication::create([
                'common_id'           => $commonId,
                'lease_application_id'=> $request->input('lease_application_id') ?: null,
                'application_no'      => $applicationNo,
                'customer_id'         => $customerId,
                'nature_of_work_id'   => $request->nature_of_work_id,
                'applicant_type_id'   => $request->applicant_type_id,
                'district_id'         => $request->district_id,
                'mineral_id'          => $isMiningPlan ? $primaryMineralId : null,
                'other_mineral_name'  => $isMiningPlan ? $request->input('other_mineral_name') : null,
                'plan_type_id'        => $isMiningPlan ? $request->plan_type_id : null,
                'taluk'               => $request->taluk,
                'village'             => $request->village,
                'survey_numbers_text' => $request->survey_numbers_text,
                'area_extent_ha'      => $request->area_extent_ha,
                'product_value'       => $prodVal,
                'paid_amount'         => $paidVal,
                'pending_amount'      => $pendingVal,
                'payment_status'      => $payStatus,
                'stage'               => '6.1',
                'status'              => 'draft',
                'branch_id'           => Auth::user()->branch_id ?? null,
                'created_by'          => Auth::id(),
            ]);
        }

        // Persist polymorphic application_payments
        ApplicationPayment::updateOrCreate(
            [
                'application_type' => 'mining',
                'application_id'   => $miningApp->id,
            ],
            [
                'payable_type'   => MiningApplication::class,
                'payable_id'     => $miningApp->id,
                'product_value'  => $prodVal,
                'paid_amount'    => $paidVal,
                'pending_amount' => $pendingVal,
                'payment_status' => $payStatus,
            ]
        );

        // Persist Handlers
        $handlers = $request->input('handlers', []);
        if (is_array($handlers)) {
            ApplicationHandler::where('application_type', 'mining')->where('application_id', $miningApp->id)->delete();
            foreach ($handlers as $idx => $h) {
                if (!empty($h['name'])) {
                    ApplicationHandler::create([
                        'application_type' => 'mining',
                        'application_id'   => $miningApp->id,
                        'handlerable_type' => MiningApplication::class,
                        'handlerable_id'   => $miningApp->id,
                        'name'             => trim($h['name']),
                        'role'             => trim($h['role'] ?? ''),
                        'notes'            => trim($h['notes'] ?? ''),
                        'sort_order'       => $idx,
                    ]);
                }
            }
        }

        // Sync multiple minerals to pivot table
        if ($isMiningPlan && !empty($mineralIds)) {
            $miningApp->minerals()->sync($mineralIds);
        }

        $miningModuleId = DB::table('modules')->where('code', 'mining')->value('id');
        if ($miningModuleId) {
            $folders = Folder::where('module_id', $miningModuleId)->get();
            $uploadedFiles = $request->file('doc_files', []);
            $subDir = 'uploads/mining/' . $applicationNo;
            $destPath = public_path($subDir);

            if (!empty($uploadedFiles) && !file_exists($destPath)) {
                mkdir($destPath, 0777, true);
            }

            foreach ($folders as $folder) {
                $fields = DocumentField::where('folder_id', $folder->id)
                    ->where('nature_of_work_id', $request->nature_of_work_id)
                    ->orderBy('sort_order')
                    ->get();

                foreach ($fields as $field) {
                    $hasFile = isset($uploadedFiles[$field->id]) && $uploadedFiles[$field->id]->isValid();
                    $fileName = null;
                    $filePath = null;
                    $fileType = null;
                    $fileSize = null;
                    $status = 'pending';
                    $uploadedBy = null;
                    $uploadedAt = null;

                    if ($hasFile) {
                        $f = $uploadedFiles[$field->id];
                        $origName = $f->getClientOriginalName();
                        $safeName = time() . '_' . $field->id . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
                        $f->move($destPath, $safeName);

                        $fileName = $origName;
                        $filePath = $subDir . '/' . $safeName;
                        $fileType = $f->getClientOriginalExtension();
                        $fileSize = filesize($destPath . '/' . $safeName);
                        $status = 'uploaded';
                        $uploadedBy = Auth::id();
                        $uploadedAt = now();
                    }

                    $existingDoc = MiningDocument::where('mining_application_id', $miningApp->id)
                        ->where('document_field_id', $field->id)
                        ->first();

                    if ($existingDoc) {
                        if ($hasFile) {
                            $existingDoc->update([
                                'folder_id'   => $folder->id,
                                'file_name'   => $fileName,
                                'file_path'   => $filePath,
                                'file_type'   => $fileType,
                                'file_size'   => $fileSize,
                                'status'      => $status,
                                'uploaded_by' => $uploadedBy,
                                'uploaded_at' => $uploadedAt,
                            ]);
                        }
                    } else {
                        MiningDocument::create([
                            'mining_application_id' => $miningApp->id,
                            'folder_id'             => $folder->id,
                            'document_field_id'     => $field->id,
                            'document_name'         => $field->name,
                            'file_name'             => $fileName,
                            'file_path'             => $filePath,
                            'file_type'             => $fileType,
                            'file_size'             => $fileSize,
                            'status'                => $status,
                            'uploaded_by'           => $uploadedBy,
                            'uploaded_at'           => $uploadedAt,
                        ]);
                    }
                }
            }

            // Process Custom Documents added by user in Step 6
            if ($request->has('custom_docs')) {
                $customDocs = $request->input('custom_docs', []);
                $customFiles = $request->file('custom_doc_files', []);

                foreach ($customDocs as $cIdx => $cDoc) {
                    $cFolderId = $cDoc['folder_id'] ?? null;
                    $cDocName  = $cDoc['name'] ?? null;
                    if (!$cFolderId || !$cDocName) continue;

                    $hasFile = isset($customFiles[$cIdx]) && $customFiles[$cIdx]->isValid();
                    $fileName = null;
                    $filePath = null;
                    $fileType = null;
                    $fileSize = null;
                    $status = 'pending';
                    $uploadedBy = null;
                    $uploadedAt = null;

                    if ($hasFile) {
                        if (!file_exists($destPath)) {
                            mkdir($destPath, 0777, true);
                        }
                        $f = $customFiles[$cIdx];
                        $origName = $f->getClientOriginalName();
                        $safeName = time() . '_custom_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
                        $f->move($destPath, $safeName);

                        $fileName = $origName;
                        $filePath = $subDir . '/' . $safeName;
                        $fileType = $f->getClientOriginalExtension();
                        $fileSize = filesize($destPath . '/' . $safeName);
                        $status = 'uploaded';
                        $uploadedBy = Auth::id();
                        $uploadedAt = now();
                    }

                    $existingCustom = MiningDocument::where('mining_application_id', $miningApp->id)
                        ->whereNull('document_field_id')
                        ->where('folder_id', $cFolderId)
                        ->where('document_name', $cDocName)
                        ->first();

                    if ($existingCustom) {
                        if ($hasFile) {
                            $existingCustom->update([
                                'file_name'   => $fileName,
                                'file_path'   => $filePath,
                                'file_type'   => $fileType,
                                'file_size'   => $fileSize,
                                'status'      => $status,
                                'uploaded_by' => $uploadedBy,
                                'uploaded_at' => $uploadedAt,
                            ]);
                        }
                    } else {
                        MiningDocument::create([
                            'mining_application_id' => $miningApp->id,
                            'folder_id'             => $cFolderId,
                            'document_field_id'     => null,
                            'document_name'         => $cDocName,
                            'file_name'             => $fileName,
                            'file_path'             => $filePath,
                            'file_type'             => $fileType,
                            'file_size'             => $fileSize,
                            'status'                => $status,
                            'uploaded_by'           => $uploadedBy,
                            'uploaded_at'           => $uploadedAt,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('projectfolder', ['id' => $miningApp->id])
            ->with('success', 'Mining Application ' . $applicationNo . ' registered successfully!');
    }

    /**
     * 2. Project Folders - Table List (when no ?id=) OR Full Dossier (when ?id=X)
     */
    public function projectFolder(Request $request)
    {
        $appId = $request->query('id');
        $miningModuleId = DB::table('modules')->where('code', 'mining')->value('id');
        $folders = Folder::where('module_id', $miningModuleId)->orderBy('sort_order')->get();

        // If specific ID is provided -> Show Full Application Dossier
        if ($appId) {
            $application = MiningApplication::with(['customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork', 'documents', 'leaseApplication', 'handlers'])->find($appId);
            
            if (!$application) {
                return redirect()->route('projectfolder')->with('error', 'The requested mining application dossier was not found.');
            }

            $folderStats = [];
            $totalDocs = 0;
            $uploadedDocs = 0;

            foreach ($folders as $folder) {
                $docs = MiningDocument::where('mining_application_id', $application->id)
                    ->where('folder_id', $folder->id)
                    ->get();
                $count = $docs->count();
                $uploaded = $docs->whereIn('status', ['uploaded', 'validated', 'approved'])->count();
                $totalDocs += $count;
                $uploadedDocs += $uploaded;
                $folderStats[$folder->id] = [
                    'folder' => $folder,
                    'total' => $count,
                    'uploaded' => $uploaded,
                    'percent' => $count > 0 ? round(($uploaded / $count) * 100) : 0,
                ];
            }

            $overallPercent = $totalDocs > 0 ? round(($uploadedDocs / $totalDocs) * 100) : 0;

            // Also load other applications for quick-switch dropdown
            $allApplications = MiningApplication::with(['customer', 'natureOfWork'])->latest()->get();

            return view('pages.mining-portal.projectfolder', compact(
                'application',
                'folders',
                'folderStats',
                'overallPercent',
                'totalDocs',
                'uploadedDocs',
                'allApplications'
            ));
        }

        // When NO ID is provided -> Show Table of All Applications with Folder Progress
        $applications = MiningApplication::with(['customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork', 'documents', 'leaseApplication'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.mining-portal.projectfolder', compact('applications', 'folders'));
    }

    /**
     * Document Upload and Management View
     */
    public function Document(Request $request)
    {
        $appId = $request->query('id');
        $folderId = $request->query('folder');

        if (!$appId) {
            return redirect()->route('projectfolder')->with('warning', 'Please select an application to view documents.');
        }

        $application = MiningApplication::with(['customer', 'district', 'mineral', 'planType', 'natureOfWork'])->findOrFail($appId);

        $miningModuleId = DB::table('modules')->where('code', 'mining')->value('id');
        $folders = Folder::where('module_id', $miningModuleId)->orderBy('sort_order')->get();

        if (!$folderId && $folders->count() > 0) {
            $folderId = $folders->first()->id;
        }

        $activeFolder = $folders->firstWhere('id', $folderId) ?? $folders->first();

        // Folder summary counts
        $folderCounts = [];
        foreach ($folders as $f) {
            $fDocs = MiningDocument::where('mining_application_id', $application->id)
                ->where('folder_id', $f->id)
                ->get();
            $folderCounts[$f->id] = [
                'total' => $fDocs->count(),
                'uploaded' => $fDocs->whereIn('status', ['uploaded', 'validated', 'approved'])->count(),
            ];
        }

        // Active folder document checklist
        $documents = MiningDocument::where('mining_application_id', $application->id)
            ->where('folder_id', $activeFolder->id)
            ->orderBy('id')
            ->get();

        return view('pages.mining-portal.document', compact(
            'application',
            'folders',
            'activeFolder',
            'folderCounts',
            'documents'
        ));
    }

    /**
     * Upload single document file against checklist item
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'mining_application_id' => 'required|exists:mining_applications,id',
            'document_id' => 'required|exists:mining_documents,id',
            'file' => 'required|file|max:25600|mimes:pdf,doc,docx,jpg,jpeg,png,kml,kmz,zip,dwg,dxf',
        ]);

        $doc = MiningDocument::findOrFail($request->document_id);
        $app = MiningApplication::findOrFail($request->mining_application_id);

        $file = $request->file('file');
        $origName = $file->getClientOriginalName();
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
        $subDir = 'uploads/mining/' . $app->application_no;
        $destPath = public_path($subDir);

        if (!file_exists($destPath)) {
            mkdir($destPath, 0777, true);
        }

        $file->move($destPath, $safeName);

        $doc->update([
            'file_name' => $origName,
            'file_path' => $subDir . '/' . $safeName,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => filesize($destPath . '/' . $safeName),
            'status' => 'uploaded',
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        return back()->with('success', 'Document "' . $doc->document_name . '" uploaded successfully!');
    }

    /**
     * 3. Process Flow - Table List (when no ?id=) OR Full Stage Workflow (when ?id=X)
     */
    public function Process(Request $request)
    {
        $appId = $request->query('id');

        // If specific ID is provided -> Show Full Process Workflow & Validation Loop
        if ($appId) {
            $application = MiningApplication::with([
                'customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork', 'documents',
                'leaseApplication.documents', 'leaseApplication.category', 'leaseApplication.surveyNumbers'
            ])->find($appId);
            
            if (!$application) {
                return redirect()->route('process')->with('error', 'The requested application was not found.');
            }

            $documents = $application->documents;

            $validatedCount = $documents->where('status', 'validated')->count();
            $rejectedCount = $documents->where('status', 'revision_required')->count();
            $uploadedCount = $documents->where('status', 'uploaded')->count();
            $pendingCount = $documents->where('status', 'pending')->count();

            // Also load all applications for quick-switch dropdown
            $allApplications = MiningApplication::with(['customer', 'natureOfWork'])->latest()->get();

            // Unified Lifecycle Activity Logs (Lease + Mining)
            $leaseId = $application->lease_application_id;
            $activityLogs = \App\Models\ActivityLog::where(function($q) use ($application, $leaseId) {
                $q->where(function($sub) use ($application) {
                    $sub->where('loggable_type', 'mining_application')
                        ->where('loggable_id', $application->id);
                });
                if ($leaseId) {
                    $q->orWhere(function($sub) use ($leaseId) {
                        $sub->where('loggable_type', 'lease_application')
                            ->where('loggable_id', $leaseId);
                    });
                }
            })
            ->orderByDesc('created_at')
            ->get();

            return view('pages.mining-portal.process', compact(
                'application',
                'documents',
                'validatedCount',
                'rejectedCount',
                'uploadedCount',
                'pendingCount',
                'allApplications',
                'activityLogs'
            ));
        }

        // When NO ID is provided -> Show Table of All Applications with Stage & Validation Queue
        $applications = MiningApplication::with(['customer', 'district', 'mineral', 'minerals', 'planType', 'natureOfWork', 'documents', 'leaseApplication'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pages.mining-portal.process', compact('applications'));
    }

    /**
     * Review / Validate individual document (Validation Loop)
     */
    public function validateDocument(Request $request, $id)
    {
        $doc = MiningDocument::findOrFail($id);
        $action = $request->input('action');
        $reviewNote = $request->input('review_note');

        if ($action === 'pass') {
            $doc->update([
                'status' => 'validated',
                'review_note' => $reviewNote ?? 'Document validated and verified by Mines Department.',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            $msg = 'Document "' . $doc->document_name . '" passed validation.';
        } else {
            $doc->update([
                'status' => 'revision_required',
                'review_note' => $reviewNote ?? 'Correction required: Resubmit correct document.',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);
            $msg = 'Document "' . $doc->document_name . '" flagged for correction.';
        }

        return back()->with('success', $msg);
    }

    /**
     * Advance stage (6.1 -> 6.2 -> 6.3 -> 6.4 -> 6.5)
     */
    public function advanceStage(Request $request, $id)
    {
        $app = MiningApplication::findOrFail($id);
        $targetStage = $request->input('stage');

        $statusMap = [
            '6.1' => 'draft',
            '6.2' => 'scrutiny',
            '6.3' => 'approved',
            '6.4' => 'approved',
            '6.5' => 'archived',
        ];

        $app->update([
            'stage' => $targetStage,
            'status' => $statusMap[$targetStage] ?? $app->status,
        ]);

        return back()->with('success', 'Application stage updated to ' . $targetStage . '.');
    }

    /**
     * Helper: Concurrency-safe atomic application number & Common ID generator for Mining Applications
     * Returns: [$applicationNo, $commonId]
     */
    private function generateMiningAppNumber(): array
    {
        $year = date('Y');
        $prefix = "MP-{$year}-";

        // Query both MP- and legacy MDG- to find the true max sequence number for this year with row locking
        $maxApp = DB::table('mining_applications')
            ->where(function($q) use ($year) {
                $q->where('application_no', 'like', "MP-{$year}-%")
                  ->orWhere('application_no', 'like', "MDG-{$year}-%");
            })
            ->whereNull('deleted_at')
            ->orderByRaw("CAST(SUBSTRING_INDEX(application_no, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextSeq = 1;
        if ($maxApp && preg_match('/-(\d+)(?:-[A-Za-z]+)?$/', $maxApp->application_no, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }

        $formattedSeq = str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
        $appNo = $prefix . $formattedSeq;
        $commonId = "GTMS-{$year}-{$formattedSeq}";

        return [$appNo, $commonId];
    }

    /**
     * Advance / Transition Mining Plan to Environment Clearance (B1 or B2) with physical document carry-over
     */
    public function moveToEnvironment(Request $request, $id)
    {
        $mining = MiningApplication::with(['customer', 'district', 'mineral', 'documents', 'leaseApplication'])->findOrFail($id);

        // Idempotency: Check if already moved
        $existingEnv = EnvironmentProject::where('mining_application_id', $mining->id)->first();
        if ($existingEnv) {
            $msg = "This Mining Plan is already linked to Environment Project: {$existingEnv->project_code}.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'   => 1,
                    'message'  => $msg,
                    'redirect' => route('environment-b2.show', $existingEnv),
                ]);
            }
            return redirect()->route('environment-b2.show', $existingEnv)->with('info', $msg);
        }

        $category = $request->input('category', 'B2');
        $year = date('Y');

        // Concurrency-safe atomic project code
        $prefix = "ENV-{$category}-{$year}-";
        $maxProj = DB::table('environment_projects')
            ->where('project_code', 'like', "{$prefix}%")
            ->whereNull('deleted_at')
            ->orderByRaw("CAST(SUBSTRING_INDEX(project_code, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextSeq = 1;
        if ($maxProj && preg_match('/-(\d+)$/', $maxProj->project_code, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }
        $projectCode = $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);

        $envProject = EnvironmentProject::create([
            'project_code'          => $projectCode,
            'customer_id'           => $mining->customer_id,
            'mining_application_id' => $mining->id,
            'lease_application_id'  => $mining->lease_application_id,
            'category'              => $category,
            'project_name'          => $mining->customer?->company_name ?: ($mining->customer?->customer_name . ' Environment Project'),
            'district_id'           => $mining->district_id,
            'location'              => ($mining->taluk ? $mining->taluk . ', ' : '') . ($mining->village ?? ''),
            'contact_name'          => $mining->customer?->customer_name,
            'contact_phone'         => $mining->customer?->mobile_num,
            'contact_email'         => $mining->customer?->email,
            'status'                => 'draft',
            'branch_id'             => $mining->branch_id ?? 1,
            'created_by'            => Auth::id() ?? 1,
        ]);

        // Auto-clone Documents into Environment Storage
        $envUploadSubdir = "uploads/environment-b2/{$projectCode}";
        $envUploadPath = public_path($envUploadSubdir);
        if (!file_exists($envUploadPath)) {
            mkdir($envUploadPath, 0777, true);
        }

        $docsFolderId = DB::table('folders')->where('module_id', 3)->where('name', 'Documents')->value('id') ?? 11;
        $gisFolderId = DB::table('folders')->where('module_id', 3)->where('name', 'like', '%GIS%')->value('id') ?? 14;

        $clonedDocNames = [];
        foreach ($mining->documents as $mDoc) {
            if ($mDoc->file_path && file_exists(public_path($mDoc->file_path))) {
                $src = public_path($mDoc->file_path);
                $destFile = basename($src);
                $dest = $envUploadPath . '/' . $destFile;
                @copy($src, $dest);

                $targetFolderId = (str_contains(strtolower($mDoc->document_name), 'kml') || str_contains(strtolower($mDoc->document_name), 'gis'))
                    ? $gisFolderId
                    : $docsFolderId;

                EnvironmentDocument::create([
                    'environment_project_id' => $envProject->id,
                    'folder_id'              => $targetFolderId,
                    'document_field_id'      => null,
                    'document_name'          => $mDoc->document_name,
                    'file_name'              => $destFile,
                    'file_path'              => $envUploadSubdir . '/' . $destFile,
                    'file_type'              => $mDoc->file_type ?? 'application/pdf',
                    'file_size'              => filesize($dest),
                    'status'                 => 'uploaded',
                    'uploaded_by'            => Auth::id() ?? 1,
                    'uploaded_at'            => now(),
                ]);

                $clonedDocNames[] = strtolower($mDoc->document_name);
            }
        }

        // Initialize standard checklist items for the remaining items
        $b2FolderIds = DB::table('folders')->where('module_id', 3)->where('sort_order', '<=', 6)->pluck('id');
        $standardFields = DocumentField::whereIn('folder_id', $b2FolderIds)->where('status', 1)->get();

        foreach ($standardFields as $field) {
            $alreadyAdded = false;
            foreach ($clonedDocNames as $cName) {
                if (str_contains($cName, strtolower(substr($field->name, 0, 10)))) {
                    $alreadyAdded = true;
                    break;
                }
            }

            if (!$alreadyAdded) {
                EnvironmentDocument::create([
                    'environment_project_id' => $envProject->id,
                    'folder_id'              => $field->folder_id,
                    'document_field_id'      => $field->id,
                    'document_name'          => $field->name,
                    'status'                 => 'pending',
                ]);
            }
        }

        ActivityLog::create([
            'loggable_type' => EnvironmentProject::class,
            'loggable_id'   => $envProject->id,
            'user_id'       => Auth::id() ?? 1,
            'action'        => 'Project Initiated from Mining Plan',
            'description'   => "Environment Clearance project {$projectCode} created from Mining Plan {$mining->application_no} with cloned documents.",
        ]);

        $successMsg = "Mining Plan successfully moved to Environment Clearance ({$projectCode}) with document carry-over!";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'   => 1,
                'message'  => $successMsg,
                'redirect' => route('environment-b2.show', $envProject),
            ]);
        }

        return redirect()->route('environment-b2.show', $envProject)->with('success', $successMsg);
    }
}
