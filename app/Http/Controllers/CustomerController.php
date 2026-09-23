<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseCategory;
use App\Models\Mineral;
use App\Models\Branch;
use App\Models\User;
use App\Models\LeaseApplication;
use App\Models\LeaseSurveyNumber;
use App\Models\MimasCredential;
use App\Models\LeaseDocument;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\DocumentField;
use App\Models\ActivityLog;
use App\Models\Folder;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    // ───────────────────────────────────────
    // Application Listing & Dossier View
    // ───────────────────────────────────────

    public function index()
    {
        $applications = LeaseApplication::with(['customer', 'district', 'category', 'mineral', 'minerals', 'documents', 'miningApplications'])
            ->latest()
            ->get();

        $kpis = [
            'total'            => LeaseApplication::count(),
            'under_validation' => LeaseApplication::whereIn('status', ['submitted', 'under_scrutiny', 'validated'])->count(),
            'approved'         => LeaseApplication::where('status', 'approved')->count(),
            'needs_review'     => LeaseApplication::whereIn('status', ['draft', 'revision_required'])->count(),
        ];

        return view('pages.lease_application.customer', compact('applications', 'kpis'));
    }

    // ───────────────────────────────────────
    // STEP 1: Lease Application (GET & POST)
    // ───────────────────────────────────────

    public function step1()
    {
        $districts = District::where('status', 1)->orderBy('name')->get();
        $customers = Customer::select('id', 'mimas_no', 'company_name', 'customer_name', 'mineral_id')->get();
        $minerals = Mineral::where('status', 1)->orderBy('name')->get();
        if ($minerals->isEmpty()) {
            $minerals = Mineral::orderBy('name')->get();
        }
        $draft = session('lease_draft', []);
        return view('pages.lease_application.createstep1', compact('districts', 'customers', 'minerals', 'draft'));
    }

    public function saveStep1(Request $request)
    {
        $validated = $request->validate([
            'customer_id'              => 'nullable|integer',
            'mimas_no'                 => 'required|string|max:50',
            'client_name'              => 'required|string|max:255',
            'secondary_contact_person' => 'nullable|string|max:255',
            'company_name'             => 'required|string|max:255',
            'district_id'              => 'required|integer|exists:districts,id',
            'mineral_ids'              => 'nullable|array',
            'mineral_ids.*'            => 'integer|exists:minerals,id',
            'mineral_id'               => 'nullable|integer|exists:minerals,id',
            'other_mineral_name'       => 'nullable|string|max:255',
            'mobile_num'               => 'required|string|max:15',
            'secondary_mobile_num'     => 'nullable|string|max:15|different:mobile_num',
            'email'                    => 'nullable|email|max:255',
            'pan'                      => 'required|string|max:10',
            'aadhaar_no'               => 'required|string|max:14',
            'gstin'                    => 'nullable|string|max:15',
            'area'                     => 'nullable|string|max:20',
            'address'                  => 'nullable|string|max:500',
        ]);

        // Resolve mineral IDs and custom other mineral name
        $mineralIds = $request->input('mineral_ids', []);
        if (empty($mineralIds) && $request->filled('mineral_id')) {
            $mineralIds = [(int)$request->input('mineral_id')];
        }
        $primaryMineralId = !empty($mineralIds) ? (int)$mineralIds[0] : 1;
        $otherMineralName = $request->input('other_mineral_name');
        $validated['mineral_id'] = $primaryMineralId;
        $validated['mineral_ids'] = $mineralIds;
        $validated['other_mineral_name'] = $otherMineralName;

        // 1. Resolve or create Customer record
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
            // Update secondary contact if provided and currently empty
            $customerUpdates = [];
            if (!empty($validated['secondary_contact_person']) && empty($customer->secondary_contact_person)) {
                $customerUpdates['secondary_contact_person'] = $validated['secondary_contact_person'];
            }
            if (!empty($validated['secondary_mobile_num']) && empty($customer->secondary_mobile_num)) {
                $customerUpdates['secondary_mobile_num'] = $validated['secondary_mobile_num'];
            }
            if (!empty($customerUpdates)) {
                $customer->update($customerUpdates);
            }
        } else {
            $customer = Customer::create([
                'customer_name'            => $validated['client_name'],
                'secondary_contact_person' => $validated['secondary_contact_person'] ?? null,
                'company_name'             => $validated['company_name'],
                'mimas_no'                 => $validated['mimas_no'],
                'mobile_num'               => $validated['mobile_num'],
                'secondary_mobile_num'     => $validated['secondary_mobile_num'] ?? null,
                'email'                    => $validated['email'] ?? null,
                'district_id'              => $validated['district_id'],
                'mineral_id'               => $primaryMineralId,
                'pan'                      => $validated['pan'],
                'aadhaar_no'               => $validated['aadhaar_no'],
                'gstin'                    => $validated['gstin'] ?? null,
                'area'                     => !empty($validated['area']) ? (float)$validated['area'] : null,
                'address'                  => $validated['address'] ?? null,
                'status'                   => 1,
            ]);
        }
        $validated['customer_id'] = $customer->id;

        // 2. Resolve or create LeaseApplication Draft in Database
        $draft = session('lease_draft', []);
        $draftAppId = $draft['application_id'] ?? null;
        $leaseApp = $draftAppId ? LeaseApplication::find($draftAppId) : null;

        if (!$leaseApp) {
            $appNo = $this->generateDraftAppNumber();
            $defaultCategory = LeaseCategory::first();
            $branch = Branch::first();

            $leaseApp = LeaseApplication::create([
                'application_no'           => $appNo,
                'customer_id'              => $customer->id,
                'district_id'              => $validated['district_id'],
                'category_id'              => $defaultCategory ? $defaultCategory->id : 1,
                'mineral_id'               => $primaryMineralId,
                'other_mineral_name'       => $otherMineralName,
                'taluk'                    => $validated['address'] ?? null,
                'area_extent_ha'           => !empty($validated['area']) ? (float)$validated['area'] : null,
                'contact_person'           => $validated['client_name'],
                'secondary_contact_person' => $validated['secondary_contact_person'] ?? ($customer->secondary_contact_person ?? null),
                'contact_mobile'           => $validated['mobile_num'],
                'secondary_contact_mobile' => $validated['secondary_mobile_num'] ?? ($customer->secondary_mobile_num ?? null),
                'current_step'             => 1,
                'status'                   => 'draft',
                'branch_id'                => $branch ? $branch->id : null,
                'created_by'               => Auth::id() ?? 1,
            ]);
            if (!empty($mineralIds)) {
                $leaseApp->minerals()->sync($mineralIds);
            }
        } else {
            $leaseApp->update([
                'customer_id'              => $customer->id,
                'district_id'              => $validated['district_id'],
                'mineral_id'               => $primaryMineralId,
                'other_mineral_name'       => $otherMineralName,
                'taluk'                    => $validated['address'] ?? $leaseApp->taluk,
                'area_extent_ha'           => !empty($validated['area']) ? (float)$validated['area'] : $leaseApp->area_extent_ha,
                'secondary_contact_person' => $validated['secondary_contact_person'] ?? $leaseApp->secondary_contact_person,
                'secondary_contact_mobile' => $validated['secondary_mobile_num'] ?? $leaseApp->secondary_contact_mobile,
                'current_step'             => max((int)$leaseApp->current_step, 1),
            ]);
            if (!empty($mineralIds)) {
                $leaseApp->minerals()->sync($mineralIds);
            }
        }

        $draft['application_id'] = $leaseApp->id;
        $draft['application_no'] = $leaseApp->application_no;
        $draft['step1'] = $validated;
        session(['lease_draft' => $draft]);

        $isExit = $request->boolean('exit') || $request->input('action') === 'exit';
        if ($isExit) {
            session()->flash('success', 'Draft application saved successfully! You can resume anytime from your applications list.');
            return response()->json([
                'status'   => 1,
                'message'  => 'Draft saved successfully',
                'redirect' => '/application',
                'draft_id' => $leaseApp->id,
            ]);
        }

        return response()->json([
            'status'   => 1,
            'message'  => 'Step 1 saved',
            'redirect' => '/step2',
            'draft_id' => $leaseApp->id,
        ]);
    }

    // ───────────────────────────────────────
    // STEP 2: Basic Information (GET & POST)
    // ───────────────────────────────────────

    public function step2()
    {
        $draft = session('lease_draft', []);
        return view('pages.lease_application.createstep2', compact('draft'));
    }

    public function saveStep2(Request $request)
    {
        $validated = $request->validate([
            'contact_person'           => 'required|string|max:255',
            'contact_mobile'           => 'required|string|max:15',
            'secondary_contact_person' => 'nullable|string|max:255',
            'secondary_contact_mobile' => 'nullable|string|max:15|different:contact_mobile',
            'mimas_user_id'            => 'required|string|max:100',
            'mimas_password'           => 'nullable|string|max:255',
            'mimas_email'              => 'required|email|max:255',
            'mimas_contact'            => 'nullable|string|max:15',
        ]);

        $draft = session('lease_draft', []);

        // Password handling (preserve existing if __UNCHANGED__ or empty)
        if (empty($validated['mimas_password']) || $validated['mimas_password'] === '__UNCHANGED__') {
            $existingPassword = $draft['step6']['mimas_password'] ?? ($draft['step2']['mimas_password'] ?? null);
            if (!$existingPassword && !empty($draft['application_id'])) {
                $cred = MimasCredential::where('lease_application_id', $draft['application_id'])->first();
                $existingPassword = $cred ? $cred->password : 'MimasPass@2026';
            }
            $validated['mimas_password'] = $existingPassword ?? 'MimasPass@2026';
        }

        if (empty($validated['mimas_contact'])) {
            $validated['mimas_contact'] = $validated['contact_mobile'];
        }

        $draft['step2'] = $validated;
        $draft['step6'] = [
            'mimas_user_id'  => $validated['mimas_user_id'],
            'mimas_password' => $validated['mimas_password'],
            'mimas_email'    => $validated['mimas_email'],
            'mimas_contact'  => $validated['mimas_contact'],
        ];
        session(['lease_draft' => $draft]);

        if (!empty($draft['application_id'])) {
            LeaseApplication::where('id', $draft['application_id'])->update([
                'contact_person'           => $validated['contact_person'],
                'contact_mobile'           => $validated['contact_mobile'],
                'secondary_contact_person' => $validated['secondary_contact_person'] ?? null,
                'secondary_contact_mobile' => $validated['secondary_contact_mobile'] ?? null,
                'current_step'             => max((int)(LeaseApplication::where('id', $draft['application_id'])->value('current_step') ?? 1), 2),
            ]);

            MimasCredential::updateOrCreate(
                ['lease_application_id' => $draft['application_id']],
                [
                    'user_id'        => $validated['mimas_user_id'],
                    'password'       => $validated['mimas_password'],
                    'email'          => $validated['mimas_email'],
                    'contact_number' => $validated['mimas_contact'],
                    'portal_status'  => 'verified',
                ]
            );
        }

        $isExit = $request->boolean('exit') || $request->input('action') === 'exit';
        if ($isExit) {
            session()->flash('success', 'Draft application saved! Contact & MIMAS details updated.');
            return response()->json(['status' => 1, 'message' => 'Draft saved', 'redirect' => '/application']);
        }

        return response()->json(['status' => 1, 'message' => 'Step 2 saved', 'redirect' => '/step3']);
    }

    // ───────────────────────────────────────
    // STEP 3: Category Under Rule (GET & POST)
    // ───────────────────────────────────────

    public function step3()
    {
        $categories = LeaseCategory::where('status', 1)->orderBy('code')->get();
        $draft = session('lease_draft', []);
        return view('pages.lease_application.createstep3', compact('categories', 'draft'));
    }

    public function saveStep3(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:lease_categories,id',
        ]);

        $draft = session('lease_draft', []);
        $draft['step3'] = $validated;
        session(['lease_draft' => $draft]);

        if (!empty($draft['application_id'])) {
            LeaseApplication::where('id', $draft['application_id'])->update([
                'category_id'  => $validated['category_id'],
                'current_step' => 3,
            ]);
        }

        $isExit = $request->boolean('exit') || $request->input('action') === 'exit';
        if ($isExit) {
            session()->flash('success', 'Draft application saved! Lease category updated.');
            return response()->json(['status' => 1, 'message' => 'Draft saved', 'redirect' => '/application']);
        }

        return response()->json(['status' => 1, 'message' => 'Step 3 saved', 'redirect' => '/step4']);
    }

    // ───────────────────────────────────────
    // STEP 4: Folders (GET only — read-only overview)
    // ───────────────────────────────────────

    public function step4()
    {
        $draft = session('lease_draft', []);
        if (!empty($draft['application_id'])) {
            $existingStep = (int)(LeaseApplication::where('id', $draft['application_id'])->value('current_step') ?? 1);
            if ($existingStep < 4) {
                LeaseApplication::where('id', $draft['application_id'])->update(['current_step' => 4]);
            }
        }

        $categoryName = 'Rule 44';
        if (!empty($draft['step3']['category_id'])) {
            $cat = LeaseCategory::find($draft['step3']['category_id']);
            if ($cat) $categoryName = $cat->code;
        }

        return view('pages.lease_application.createstep4', compact('draft', 'categoryName'));
    }

    // ───────────────────────────────────────
    // STEP 5: Upload Documents (GET & POST)
    // ───────────────────────────────────────

    public function step5(Request $request)
    {
        $draft = session('lease_draft', []);
        $draftAppId = $draft['application_id'] ?? $request->query('id');
        $uploadedDocs = $draft['uploaded_docs'] ?? [];

        // Milestone Document Loading from DB
        if ($draftAppId) {
            $existingStep = (int)(LeaseApplication::where('id', $draftAppId)->value('current_step') ?? 1);
            if ($existingStep < 5) {
                LeaseApplication::where('id', $draftAppId)->update(['current_step' => 5]);
            }
            $dbDocs = LeaseDocument::where('lease_application_id', $draftAppId)->get();
            foreach ($dbDocs as $doc) {
                $item = $doc->document_field_id;
                if ($item && !isset($uploadedDocs[$item])) {
                    $uploadedDocs[$item] = [
                        'doc_name'    => $doc->document_name,
                        'file_name'   => $doc->file_name,
                        'file_size'   => $doc->file_size,
                        'file_type'   => $doc->file_type,
                        'folder_id'   => $doc->folder_id,
                        'draft_path'  => $doc->file_path,
                        'uploaded_at' => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : 'Saved',
                    ];
                } elseif (!$item) {
                    $customKey = 'custom_' . $doc->id;
                    if (!isset($uploadedDocs[$customKey])) {
                        $uploadedDocs[$customKey] = [
                            'doc_name'     => $doc->document_name,
                            'is_custom'    => true,
                            'is_mandatory' => false,
                            'file_name'    => $doc->file_name,
                            'file_size'    => $doc->file_size,
                            'file_type'    => $doc->file_type,
                            'folder_id'    => $doc->folder_id,
                            'draft_path'   => $doc->file_path,
                            'uploaded_at'  => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : 'Saved',
                        ];
                    }
                }
            }
            $draft['uploaded_docs'] = $uploadedDocs;
            session(['lease_draft' => $draft]);
        }

        return view('pages.lease_application.createstep5', compact('draft', 'uploadedDocs'));
    }

    /**
     * Handle real file upload for individual document items.
     * Persists directly to database (Milestone Document Saving).
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file'         => 'required|file|max:25600|mimes:pdf,png,jpg,jpeg,kml,xml,txt,doc,docx,dwg',
            'doc_item'     => 'nullable',
            'folder_id'    => 'required|integer',
            'doc_name'     => 'nullable|string|max:255',
            'is_mandatory' => 'nullable',
        ]);

        $file = $request->file('file');
        $rawDocItem = $request->input('doc_item');
        $isCustom = $request->filled('doc_name') || (is_string($rawDocItem) && str_starts_with($rawDocItem, 'custom_'));
        $docItem = $isCustom ? ($rawDocItem ?: ('custom_' . time() . '_' . rand(100, 999))) : (int)$rawDocItem;
        $folderId = (int)$request->input('folder_id');
        $isMandatory = $request->input('is_mandatory') == '1' || $request->input('is_mandatory') === true;

        $draft = session('lease_draft', []);
        $draftAppId = $draft['application_id'] ?? null;
        $leaseApp = $draftAppId ? LeaseApplication::find($draftAppId) : null;

        $appNo = $leaseApp ? $leaseApp->application_no : ($draft['application_no'] ?? ('DRAFT-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6))));

        $uploadSubdir = 'uploads/lease_applications/' . $appNo;
        $fullUploadPath = public_path($uploadSubdir);
        if (!file_exists($fullUploadPath)) {
            mkdir($fullUploadPath, 0777, true);
        }

        $fileName = $file->getClientOriginalName();
        $file->move($fullUploadPath, $fileName);
        $fileSize = filesize($fullUploadPath . '/' . $fileName);
        $fileType = mime_content_type($fullUploadPath . '/' . $fileName);

        $docNames = [
            1  => '1. Land Document',
            2  => '2. Consent (If Applicable)',
            3  => '3. Adangal & A-register',
            4  => '4. Patta & Encumbrance Certificate',
            5  => '5. Work Order',
            6  => '6. Gazette',
            7  => '7. Recommendation Letter',
            8  => '8. Mineral Management System – Application',
            9  => '9. Challan downloaded from Mimas',
            10 => '10. Lease application – signed, FMB, Plan',
            11 => '11. Affidavit – Income Tax',
            12 => '12. IT returns (If Applicable)',
            13 => '13. Affidavit – Mining Due',
            14 => '14. Affidavit – Mining Lease',
            15 => '15. Affidavit – 1.5 meter depth',
            16 => '16. Affidavit – Hill Areas',
            17 => '17. Plan Source File',
            18 => '18. KML File',
            19 => '19. Plan PDF',
        ];

        $resolvedDocName = $isCustom ? ($request->input('doc_name') ?: 'Custom Document') : ($docNames[$docItem] ?? ('Item ' . $docItem));

        // Milestone Persistence in MySQL:
        if ($leaseApp) {
            if ($isCustom) {
                $dbDoc = LeaseDocument::create([
                    'lease_application_id' => $leaseApp->id,
                    'folder_id'            => $folderId,
                    'document_field_id'    => null,
                    'document_name'        => $resolvedDocName,
                    'file_name'            => $fileName,
                    'file_path'            => $uploadSubdir . '/' . $fileName,
                    'file_type'            => $fileType,
                    'file_size'            => $fileSize,
                    'status'               => 'uploaded',
                    'uploaded_by'          => Auth::id() ?? 1,
                    'uploaded_at'          => now(),
                ]);
                $docItem = 'custom_' . $dbDoc->id;
            } else {
                LeaseDocument::updateOrCreate(
                    [
                        'lease_application_id' => $leaseApp->id,
                        'document_field_id'    => $docItem,
                    ],
                    [
                        'folder_id'      => $folderId,
                        'document_name'  => $resolvedDocName,
                        'file_name'      => $fileName,
                        'file_path'      => $uploadSubdir . '/' . $fileName,
                        'file_type'      => $fileType,
                        'file_size'      => $fileSize,
                        'status'         => 'uploaded',
                        'uploaded_by'    => Auth::id() ?? 1,
                        'uploaded_at'    => now(),
                    ]
                );
            }
            $leaseApp->update(['current_step' => 5]);
        }

        // Track in session
        $uploadedDocs = session('lease_draft.uploaded_docs', []);
        $uploadedDocs[$docItem] = [
            'doc_name'     => $resolvedDocName,
            'is_custom'    => $isCustom,
            'is_mandatory' => $isMandatory,
            'file_name'    => $fileName,
            'file_size'    => $fileSize,
            'file_type'    => $fileType,
            'folder_id'    => $folderId,
            'draft_path'   => $uploadSubdir . '/' . $fileName,
            'uploaded_at'  => now()->format('d M Y, h:i A'),
        ];
        $draft['uploaded_docs'] = $uploadedDocs;
        session(['lease_draft' => $draft]);

        $customCount = count(array_filter($uploadedDocs, fn($d) => !empty($d['is_custom'])));
        $totalItems = 19 + $customCount;
        $uploadedCount = count($uploadedDocs);

        return response()->json([
            'status'       => 1,
            'message'      => $fileName . ' uploaded & saved successfully',
            'is_custom'    => $isCustom,
            'doc_item'     => $docItem,
            'doc_name'     => $resolvedDocName,
            'folder_id'    => $folderId,
            'is_mandatory' => $isMandatory,
            'file_name'    => $fileName,
            'file_path'    => $uploadSubdir . '/' . $fileName,
            'file_url'     => asset($uploadSubdir . '/' . $fileName),
            'file_size'    => $this->formatFileSize($fileSize),
            'uploaded'     => $uploadedCount,
            'total'        => $totalItems,
            'percent'      => min(100, round(($uploadedCount / $totalItems) * 100)),
        ]);
    }

    // ───────────────────────────────────────
    // STEP 6: MIMAS Registration Details (GET & POST)
    // ───────────────────────────────────────

    // ───────────────────────────────────────
    // STEP 6: Project Handling Persons (GET & POST)
    // ───────────────────────────────────────

    public function step6()
    {
        $draft = session('lease_draft', []);
        if (!empty($draft['application_id'])) {
            $existingStep = (int)(LeaseApplication::where('id', $draft['application_id'])->value('current_step') ?? 1);
            if ($existingStep < 6) {
                LeaseApplication::where('id', $draft['application_id'])->update(['current_step' => 6]);
            }
        }

        $handlers = $draft['handlers'] ?? [];
        if (empty($handlers) && !empty($draft['application_id'])) {
            $dbHandlers = ApplicationHandler::where('application_type', 'lease')
                ->where('application_id', $draft['application_id'])
                ->orderBy('sort_order')
                ->get();
            if ($dbHandlers->isNotEmpty()) {
                $handlers = $dbHandlers->map(fn($h) => [
                    'name'  => $h->name,
                    'role'  => $h->role,
                    'notes' => $h->notes,
                ])->toArray();
                $draft['handlers'] = $handlers;
                session(['lease_draft' => $draft]);
            }
        }

        return view('pages.lease_application.createstep6', compact('draft', 'handlers'));
    }

    public function saveStep6(Request $request)
    {
        $request->validate([
            'handlers'         => 'nullable|array',
            'handlers.*.name'  => 'nullable|string|max:255',
            'handlers.*.role'  => 'nullable|string|max:255',
            'handlers.*.notes' => 'nullable|string|max:1000',
        ]);

        $rawHandlers = $request->input('handlers', []);
        $cleanHandlers = [];
        if (is_array($rawHandlers)) {
            foreach ($rawHandlers as $h) {
                if (!empty($h['name'])) {
                    $cleanHandlers[] = [
                        'name'  => trim($h['name']),
                        'role'  => trim($h['role'] ?? ''),
                        'notes' => trim($h['notes'] ?? ''),
                    ];
                }
            }
        }

        $draft = session('lease_draft', []);
        $draft['handlers'] = $cleanHandlers;
        session(['lease_draft' => $draft]);

        if (!empty($draft['application_id'])) {
            ApplicationHandler::where('application_type', 'lease')
                ->where('application_id', $draft['application_id'])
                ->delete();

            foreach ($cleanHandlers as $idx => $h) {
                ApplicationHandler::create([
                    'application_type' => 'lease',
                    'application_id'   => $draft['application_id'],
                    'handlerable_type' => LeaseApplication::class,
                    'handlerable_id'   => $draft['application_id'],
                    'name'             => $h['name'],
                    'role'             => $h['role'],
                    'notes'            => $h['notes'],
                    'sort_order'       => $idx,
                ]);
            }

            LeaseApplication::where('id', $draft['application_id'])->update(['current_step' => max(6, (int)LeaseApplication::where('id', $draft['application_id'])->value('current_step'))]);
        }

        $isExit = $request->boolean('exit') || $request->input('action') === 'exit';
        if ($isExit) {
            session()->flash('success', 'Draft application saved! Project handling team updated.');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 1, 'message' => 'Draft saved', 'redirect' => '/application']);
            }
            return redirect('/application');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 1, 'message' => 'Step 6 saved', 'redirect' => '/step7']);
        }
        return redirect()->route('step7');
    }

    // ───────────────────────────────────────
    // STEP 7: Payment Details (GET & POST)
    // ───────────────────────────────────────

    public function step7()
    {
        $draft = session('lease_draft', []);
        if (!empty($draft['application_id'])) {
            $existingStep = (int)(LeaseApplication::where('id', $draft['application_id'])->value('current_step') ?? 1);
            if ($existingStep < 7) {
                LeaseApplication::where('id', $draft['application_id'])->update(['current_step' => 7]);
            }
        }

        $payment = $draft['payment'] ?? [];
        if (empty($payment) && !empty($draft['application_id'])) {
            $leaseApp = LeaseApplication::find($draft['application_id']);
            if ($leaseApp) {
                $payment = [
                    'product_value'  => (float)($leaseApp->product_value ?? 0),
                    'paid_amount'    => (float)($leaseApp->paid_amount ?? 0),
                    'pending_amount' => (float)($leaseApp->pending_amount ?? 0),
                    'payment_status' => $leaseApp->payment_status ?? 'pending',
                ];
                $draft['payment'] = $payment;
                session(['lease_draft' => $draft]);
            }
        }

        return view('pages.lease_application.createstep7', compact('draft', 'payment'));
    }

    public function saveStep7(Request $request)
    {
        $validated = $request->validate([
            'product_value'  => 'required|numeric|min:0',
            'paid_amount'    => 'required|numeric|min:0',
            'payment_status' => 'nullable|string|in:paid,partial,pending',
        ]);

        $productVal = (float)$validated['product_value'];
        $paidVal = (float)$validated['paid_amount'];
        $pendingVal = max(0, $productVal - $paidVal);
        $status = $validated['payment_status'] ?? null;

        if (!$status) {
            if ($paidVal <= 0) {
                $status = 'pending';
            } elseif ($pendingVal <= 0 && $productVal > 0) {
                $status = 'paid';
            } else {
                $status = 'partial';
            }
        }

        $paymentData = [
            'product_value'  => $productVal,
            'paid_amount'    => $paidVal,
            'pending_amount' => $pendingVal,
            'payment_status' => $status,
        ];

        $draft = session('lease_draft', []);
        $draft['payment'] = $paymentData;
        session(['lease_draft' => $draft]);

        if (!empty($draft['application_id'])) {
            LeaseApplication::where('id', $draft['application_id'])->update([
                'product_value'  => $productVal,
                'paid_amount'    => $paidVal,
                'pending_amount' => $pendingVal,
                'payment_status' => $status,
                'current_step'   => max(7, (int)LeaseApplication::where('id', $draft['application_id'])->value('current_step')),
            ]);

            ApplicationPayment::updateOrCreate(
                [
                    'application_type' => 'lease',
                    'application_id'   => $draft['application_id'],
                ],
                [
                    'payable_type'   => LeaseApplication::class,
                    'payable_id'     => $draft['application_id'],
                    'product_value'  => $productVal,
                    'paid_amount'    => $paidVal,
                    'pending_amount' => $pendingVal,
                    'payment_status' => $status,
                ]
            );
        }

        $isExit = $request->boolean('exit') || $request->input('action') === 'exit';
        if ($isExit) {
            session()->flash('success', 'Draft application saved! Payment details updated.');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => 1, 'message' => 'Draft saved', 'redirect' => '/application']);
            }
            return redirect('/application');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['status' => 1, 'message' => 'Step 7 saved', 'redirect' => '/step8']);
        }
        return redirect()->route('step8');
    }

    // ───────────────────────────────────────
    // STEP 8: Review & Submit (GET)
    // ───────────────────────────────────────

    public function step8()
    {
        $draft = session('lease_draft', []);
        if (!empty($draft['application_id'])) {
            $existingStep = (int)(LeaseApplication::where('id', $draft['application_id'])->value('current_step') ?? 1);
            if ($existingStep < 8) {
                LeaseApplication::where('id', $draft['application_id'])->update(['current_step' => 8]);
            }
        }
        $previewData = $this->buildPreviewData($draft);
        return view('pages.lease_application.createstep8', compact('draft', 'previewData'));
    }

    // ───────────────────────────────────────
    // RESUME DRAFT APPLICATION
    // ───────────────────────────────────────

    public function resumeDraft($id)
    {
        $app = LeaseApplication::with(['customer', 'district', 'category', 'mineral', 'minerals', 'mimasCredentials', 'documents', 'handlers'])->findOrFail($id);

        // Reconstruct lease_draft session from database
        $draft = [
            'application_id' => $app->id,
            'application_no' => $app->application_no,
            'step1' => [
                'customer_id'  => $app->customer_id,
                'client_name'  => $app->customer->customer_name ?? '',
                'company_name' => $app->customer->company_name ?? '',
                'mimas_no'     => $app->customer->mimas_no ?? '',
                'district_id'  => $app->district_id,
                'mineral_id'   => $app->mineral_id,
                'mineral_ids'  => ($app->minerals && $app->minerals->isNotEmpty()) ? $app->minerals->pluck('id')->toArray() : ($app->mineral_id ? [$app->mineral_id] : []),
                'other_mineral_name' => $app->other_mineral_name ?? '',
                'mobile_num'               => $app->customer->mobile_num ?? '',
                'secondary_contact_person' => $app->secondary_contact_person ?? ($app->customer->secondary_contact_person ?? ''),
                'secondary_mobile_num'     => $app->secondary_contact_mobile ?? ($app->customer->secondary_mobile_num ?? ''),
                'email'                    => $app->customer->email ?? '',
                'pan'                      => $app->customer->pan ?? '',
                'aadhaar_no'               => $app->customer->aadhaar_no ?? '',
                'gstin'                    => $app->customer->gstin ?? '',
                'area'                     => $app->area_extent_ha ?? ($app->customer->area ?? ''),
                'address'                  => $app->taluk ?? ($app->customer->address ?? ''),
            ],
            'step2' => [
                'contact_person'           => $app->contact_person ?? ($app->customer->customer_name ?? ''),
                'contact_mobile'           => $app->contact_mobile ?? ($app->customer->mobile_num ?? ''),
                'secondary_contact_person' => $app->secondary_contact_person ?? ($app->customer->secondary_contact_person ?? ''),
                'secondary_contact_mobile' => $app->secondary_contact_mobile ?? ($app->customer->secondary_mobile_num ?? ''),
                'mimas_user_id' => $app->mimasCredentials->first()->user_id ?? ($app->customer->mimas_no ?? ''),
                'mimas_password' => $app->mimasCredentials->first() ? '__UNCHANGED__' : '',
                'mimas_email'   => $app->mimasCredentials->first()->email ?? ($app->customer->email ?? ''),
                'mimas_contact' => $app->mimasCredentials->first()->contact_number ?? ($app->customer->mobile_num ?? ''),
            ],
            'step3' => [
                'category_id' => $app->category_id,
            ],
            'step6' => [
                'mimas_user_id' => $app->mimasCredentials->first()->user_id ?? ($app->customer->mimas_no ?? ''),
                'mimas_password' => $app->mimasCredentials->first() ? '__UNCHANGED__' : '',
                'mimas_email'   => $app->mimasCredentials->first()->email ?? ($app->customer->email ?? ''),
                'mimas_contact' => $app->mimasCredentials->first()->contact_number ?? ($app->customer->mobile_num ?? ''),
            ],
            'handlers' => $app->handlers->map(fn($h) => [
                'name'  => $h->name,
                'role'  => $h->role,
                'notes' => $h->notes,
            ])->toArray(),
            'payment' => [
                'product_value'  => (float)($app->product_value ?? 0),
                'paid_amount'    => (float)($app->paid_amount ?? 0),
                'pending_amount' => (float)($app->pending_amount ?? 0),
                'payment_status' => $app->payment_status ?? 'pending',
            ],
            'uploaded_docs' => [],
        ];

        foreach ($app->documents as $doc) {
            $docItem = $doc->document_field_id;
            if ($docItem) {
                $draft['uploaded_docs'][$docItem] = [
                    'file_name'   => $doc->file_name,
                    'file_size'   => $doc->file_size,
                    'file_type'   => $doc->file_type,
                    'folder_id'   => $doc->folder_id,
                    'draft_path'  => $doc->file_path,
                    'uploaded_at' => $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : 'Saved',
                ];
            }
        }

        session(['lease_draft' => $draft]);

        $step = $app->current_step ? min(max((int)$app->current_step, 1), 8) : 1;
        return redirect("/step{$step}")->with('success', "Resumed draft application {$app->application_no} at Step {$step} of 8.");
    }

    /**
     * Build a structured preview from session draft data.
     */
    private function buildPreviewData(array $draft): array
    {
        $step1 = $draft['step1'] ?? [];
        $step2 = $draft['step2'] ?? [];
        $step3 = $draft['step3'] ?? [];
        $step6 = $draft['step6'] ?? [];
        $uploadedDocs = $draft['uploaded_docs'] ?? [];

        $preview = [
            'client_name'    => $step1['client_name'] ?? 'N/A',
            'company_name'   => $step1['company_name'] ?? 'N/A',
            'mimas_no'       => $step1['mimas_no'] ?? 'N/A',
            'aadhaar_no'     => $step1['aadhaar_no'] ?? 'N/A',
            'district_name'  => 'N/A',
            'mobile_num'           => $step1['mobile_num'] ?? 'N/A',
            'secondary_mobile_num' => $step1['secondary_mobile_num'] ?? null,
            'email'                => !empty($step1['email']) ? $step1['email'] : 'Not provided',
            'pan'            => $step1['pan'] ?? 'N/A',
            'gstin'          => !empty($step1['gstin']) ? $step1['gstin'] : 'Not provided',
            'area'           => $step1['area'] ?? null,
            'contact_person'           => $step2['contact_person'] ?? ($step1['client_name'] ?? 'N/A'),
            'contact_mobile'           => $step2['contact_mobile'] ?? ($step1['mobile_num'] ?? 'N/A'),
            'secondary_contact_person' => $step2['secondary_contact_person'] ?? ($step1['secondary_contact_person'] ?? null),
            'secondary_contact_mobile' => $step2['secondary_contact_mobile'] ?? ($step1['secondary_mobile_num'] ?? null),
            'category_name'            => 'N/A',
            'category_code'            => 'N/A',
            'mimas_user_id'            => $step6['mimas_user_id'] ?? ($step1['mimas_no'] ?? 'Not entered'),
            'mimas_email'              => $step6['mimas_email'] ?? ($step1['email'] ?? 'Not entered'),
            'mimas_contact'            => $step6['mimas_contact'] ?? ($step1['mobile_num'] ?? 'Not entered'),
            'uploaded_count'           => count($uploadedDocs),
            'uploaded_docs'            => $uploadedDocs,
        ];

        // Resolve District Name
        if (!empty($step1['district_id'])) {
            $dist = District::find($step1['district_id']);
            if ($dist) $preview['district_name'] = $dist->name;
        }

        // Resolve Customer & Minerals strictly from user selection/input
        $customer = null;
        if (!empty($step1['customer_id'])) {
            $customer = Customer::withTrashed()->with('mineral')->find($step1['customer_id']);
        } elseif (!empty($step1['mimas_no'])) {
            $customer = Customer::withTrashed()->with('mineral')->where('mimas_no', $step1['mimas_no'])->first();
        }

        $mineralNames = [];
        $mineralIds = $step1['mineral_ids'] ?? (!empty($step1['mineral_id']) ? [$step1['mineral_id']] : []);
        if (!empty($mineralIds)) {
            $mineralNames = Mineral::whereIn('id', $mineralIds)->pluck('name')->toArray();
        } elseif ($customer && $customer->mineral) {
            $mineralNames = [$customer->mineral->name];
        }
        if (!empty($step1['other_mineral_name'])) {
            $mineralNames[] = 'Other: ' . $step1['other_mineral_name'];
        }

        $preview['mineral_names'] = $mineralNames;
        $preview['mineral_name'] = !empty($mineralNames) ? implode(', ', $mineralNames) : 'Not specified';
        $preview['other_mineral_name'] = $step1['other_mineral_name'] ?? null;

        if (empty($preview['area']) && $customer && $customer->area) {
            $preview['area'] = $customer->area;
        }

        $preview['extent_display'] = !empty($preview['area']) ? ($preview['area'] . ' Ha') : 'Not specified';

        $currentYear = (int)date('Y');
        $preview['lease_period'] = "5 Years ({$currentYear} - " . ($currentYear + 5) . ")";

        // Resolve Category
        if (!empty($step3['category_id'])) {
            $cat = LeaseCategory::find($step3['category_id']);
            if ($cat) {
                $preview['category_name'] = $cat->name;
                $preview['category_code'] = $cat->code;
            }
        }

        // Folder breakdown
        $f7Count = 0;
        $f8Count = 0;
        $f9Count = 0;
        foreach ($uploadedDocs as $item => $doc) {
            $folderId = $doc['folder_id'] ?? null;
            if ($folderId == 7) $f7Count++;
            elseif ($folderId == 8) $f8Count++;
            elseif ($folderId == 9) $f9Count++;
            else {
                $it = (int)$item;
                if ($it >= 1 && $it <= 9) $f7Count++;
                elseif ($it >= 10 && $it <= 16) $f8Count++;
                elseif ($it >= 17 && $it <= 19) $f9Count++;
            }
        }
        $preview['f7_count'] = $f7Count;
        $preview['f8_count'] = $f8Count;
        $preview['f9_count'] = $f9Count;

        // Project Handling Team
        $handlers = $draft['handlers'] ?? [];
        if (empty($handlers) && !empty($draft['application_id'])) {
            $handlers = ApplicationHandler::where('application_type', 'lease')
                ->where('application_id', $draft['application_id'])
                ->orderBy('sort_order')
                ->get()
                ->map(fn($h) => [
                    'name'  => $h->name,
                    'role'  => $h->role,
                    'notes' => $h->notes,
                ])->toArray();
        }
        $preview['handlers'] = $handlers;

        // Payment Details
        $payment = $draft['payment'] ?? [];
        if (empty($payment) && !empty($draft['application_id'])) {
            $existingApp = LeaseApplication::find($draft['application_id']);
            if ($existingApp) {
                $payment = [
                    'product_value'  => (float)($existingApp->product_value ?? 0),
                    'paid_amount'    => (float)($existingApp->paid_amount ?? 0),
                    'pending_amount' => (float)($existingApp->pending_amount ?? 0),
                    'payment_status' => $existingApp->payment_status ?? 'pending',
                ];
            }
        }
        $preview['payment'] = [
            'product_value'  => (float)($payment['product_value'] ?? 0),
            'paid_amount'    => (float)($payment['paid_amount'] ?? 0),
            'pending_amount' => (float)($payment['pending_amount'] ?? 0),
            'payment_status' => $payment['payment_status'] ?? 'pending',
        ];

        return $preview;
    }

    public function submit(Request $request)
    {
        $draft = session('lease_draft', []);
        $step1 = $draft['step1'] ?? [];
        $step2 = $draft['step2'] ?? [];
        $step3 = $draft['step3'] ?? [];
        $step6 = $draft['step6'] ?? [];

        // 1. Resolve Customer (find or create)
        $customer = null;
        if (!empty($step1['customer_id'])) {
            $customer = Customer::withTrashed()->find($step1['customer_id']);
        }
        if (!$customer && !empty($step1['mimas_no'])) {
            $customer = Customer::withTrashed()->where('mimas_no', $step1['mimas_no'])->first();
        }
        if (!$customer && !empty($step1['aadhaar_no'])) {
            $customer = Customer::withTrashed()->where('aadhaar_no', $step1['aadhaar_no'])->first();
        }
        if ($customer) {
            if ($customer->trashed()) {
                $customer->restore();
            }
        } else {
            $customer = Customer::create([
                'customer_name' => $step1['client_name'] ?? $request->input('client_name', 'Applicant'),
                'company_name'  => $step1['company_name'] ?? $request->input('company_name', 'Applicant Quarry'),
                'mimas_no'      => $step1['mimas_no'] ?? $request->input('mimas_no', 'TN-MMS-' . strtoupper(substr(md5(uniqid()), 0, 6))),
                'mobile_num'    => $step1['mobile_num'] ?? $request->input('mobile_num', '0000000000'),
                'email'         => $step1['email'] ?? null,
                'district_id'   => $step1['district_id'] ?? $request->input('district_id', 1),
                'mineral_id'    => $step1['mineral_id'] ?? 1,
                'pan'           => $step1['pan'] ?? 'PANNA0000P',
                'aadhaar_no'    => $step1['aadhaar_no'] ?? null,
                'gstin'         => $step1['gstin'] ?? null,
                'area'          => !empty($step1['area']) ? (float)$step1['area'] : null,
                'address'       => $step1['address'] ?? null,
                'status'        => 1,
            ]);
        }

        // 2. Resolve District
        $districtId = $step1['district_id'] ?? ($customer->district_id ?? 1);
        $district = District::find($districtId) ?? District::first();

        // 3. Resolve Category
        $categoryId = $step3['category_id'] ?? null;
        if ($categoryId) {
            $category = LeaseCategory::find($categoryId) ?? LeaseCategory::first();
        } else {
            $category = LeaseCategory::first();
        }

        // 4. Resolve Mineral
        $mineralId = $step1['mineral_id'] ?? ($customer->mineral_id ?? null);
        $mineral = $mineralId ? (Mineral::find($mineralId) ?? Mineral::first()) : Mineral::first();
        $mineralIds = $step1['mineral_ids'] ?? ($mineralId ? [$mineralId] : [1]);
        $otherMineralName = $step1['other_mineral_name'] ?? null;

        $branch = Branch::first();
        $user = Auth::user() ?? User::first();

        $appNo = null;
        $submittedAppId = null;

        DB::transaction(function() use (&$appNo, &$submittedAppId, $customer, $district, $category, $mineral, $mineralIds, $otherMineralName, $branch, $user, $request, $draft, $step1, $step2, $step6) {
            $currentYear = (int)date('Y');
            $appNo = $this->generateOfficialAppNumber();

            // Check if this was a saved draft in DB
            $existingAppId = $draft['application_id'] ?? null;
            $leaseApp = $existingAppId ? LeaseApplication::find($existingAppId) : null;
            $oldAppNo = $leaseApp ? $leaseApp->application_no : null;

            if ($leaseApp) {
                $leaseApp->update([
                    'common_id'          => 'GTMS-' . substr($appNo, 3),
                    'application_no'     => $appNo,
                    'customer_id'        => $customer->id,
                    'district_id'        => $district->id,
                    'category_id'        => $category->id,
                    'mineral_id'         => $mineral->id,
                    'other_mineral_name' => $otherMineralName,
                    'taluk'              => $step1['address'] ?? null,
                    'area_extent_ha'     => !empty($step1['area']) ? (float)$step1['area'] : ($customer->area ?? null),
                    'start_date'         => "{$currentYear}-10-01",
                    'end_date'           => ($currentYear + 5) . "-09-30",
                    'lease_period_years' => 5,
                    'contact_person'           => $step2['contact_person'] ?? ($step1['client_name'] ?? $customer->customer_name),
                    'secondary_contact_person' => $step2['secondary_contact_person'] ?? ($step1['secondary_contact_person'] ?? ($customer->secondary_contact_person ?? null)),
                    'contact_mobile'           => $step2['contact_mobile'] ?? ($step1['mobile_num'] ?? $customer->mobile_num),
                    'secondary_contact_mobile' => $step2['secondary_contact_mobile'] ?? ($step1['secondary_mobile_num'] ?? ($customer->secondary_mobile_num ?? null)),
                    'current_step'             => 6,
                    'status'                   => 'under_scrutiny',
                    'branch_id'                => $branch ? $branch->id : null,
                ]);
            } else {
                $leaseApp = LeaseApplication::create([
                    'common_id'                => 'GTMS-' . substr($appNo, 3),
                    'application_no'           => $appNo,
                    'customer_id'              => $customer->id,
                    'district_id'              => $district->id,
                    'category_id'              => $category->id,
                    'mineral_id'               => $mineral->id,
                    'other_mineral_name'       => $otherMineralName,
                    'taluk'                    => $step1['address'] ?? null,
                    'village'                  => null,
                    'area_extent_ha'           => !empty($step1['area']) ? (float)$step1['area'] : ($customer->area ?? null),
                    'area_extent_acres'        => null,
                    'start_date'               => "{$currentYear}-10-01",
                    'end_date'                 => ($currentYear + 5) . "-09-30",
                    'lease_period_years'       => 5,
                    'contact_person'           => $step2['contact_person'] ?? ($step1['client_name'] ?? $customer->customer_name),
                    'secondary_contact_person' => $step2['secondary_contact_person'] ?? ($step1['secondary_contact_person'] ?? ($customer->secondary_contact_person ?? null)),
                    'contact_mobile'           => $step2['contact_mobile'] ?? ($step1['mobile_num'] ?? $customer->mobile_num),
                    'secondary_contact_mobile' => $step2['secondary_contact_mobile'] ?? ($step1['secondary_mobile_num'] ?? ($customer->secondary_mobile_num ?? null)),
                    'current_step'             => 6,
                    'status'                   => 'under_scrutiny',
                    'branch_id'                => $branch ? $branch->id : null,
                    'created_by'               => $user ? $user->id : 1,
                ]);
            }

            // Sync multi-minerals into pivot table
            if (!empty($mineralIds)) {
                $leaseApp->minerals()->sync($mineralIds);
            }

            $submittedAppId = $leaseApp->id;

            // Survey numbers bound to applicant
            if (!empty($step1['area'])) {
                LeaseSurveyNumber::updateOrCreate(
                    ['lease_application_id' => $leaseApp->id],
                    [
                        'survey_no'      => 'SF.No 1',
                        'extent_ha'      => (float)$step1['area'],
                        'classification' => 'Patta Dry',
                        'pattadar_name'  => $step1['client_name'] ?? $customer->customer_name,
                    ]
                );
            }

            // MIMAS credentials
            $mimasUserId = $step6['mimas_user_id'] ?? ($step1['mimas_no'] ?? $customer->mimas_no);
            $mimasPassword = $step6['mimas_password'] ?? 'MimasPass@2026';
            $mimasEmail = $step6['mimas_email'] ?? ($step1['email'] ?? ($customer->email ?? 'applicant@portal.tn.gov.in'));
            $mimasContact = $step6['mimas_contact'] ?? ($step1['mobile_num'] ?? ($customer->mobile_num ?? '0000000000'));

            MimasCredential::updateOrCreate(
                ['lease_application_id' => $leaseApp->id],
                [
                    'user_id'        => $mimasUserId,
                    'password'       => $mimasPassword,
                    'email'          => $mimasEmail,
                    'contact_number' => $mimasContact,
                    'mimas_ack_no'   => 'ACK-MMS-' . date('Y') . '-' . rand(1000, 9999),
                    'portal_status'  => 'verified',
                ]
            );

            // Payment details persistence
            $payment = $draft['payment'] ?? [];
            $productVal = (float)($payment['product_value'] ?? 0);
            $paidVal = (float)($payment['paid_amount'] ?? 0);
            $pendingVal = max(0, $productVal - $paidVal);
            $payStatus = $payment['payment_status'] ?? ($paidVal <= 0 ? 'pending' : ($pendingVal <= 0 ? 'paid' : 'partial'));

            $leaseApp->update([
                'product_value'  => $productVal,
                'paid_amount'    => $paidVal,
                'pending_amount' => $pendingVal,
                'payment_status' => $payStatus,
                'current_step'   => 8,
            ]);

            ApplicationPayment::updateOrCreate(
                [
                    'application_type' => 'lease',
                    'application_id'   => $leaseApp->id,
                ],
                [
                    'payable_type'   => LeaseApplication::class,
                    'payable_id'     => $leaseApp->id,
                    'product_value'  => $productVal,
                    'paid_amount'    => $paidVal,
                    'pending_amount' => $pendingVal,
                    'payment_status' => $payStatus,
                ]
            );

            // Handlers persistence
            $handlers = $draft['handlers'] ?? [];
            ApplicationHandler::where('application_type', 'lease')->where('application_id', $leaseApp->id)->delete();
            if (is_array($handlers)) {
                foreach ($handlers as $idx => $h) {
                    if (!empty($h['name'])) {
                        ApplicationHandler::create([
                            'application_type' => 'lease',
                            'application_id'   => $leaseApp->id,
                            'handlerable_type' => LeaseApplication::class,
                            'handlerable_id'   => $leaseApp->id,
                            'name'             => $h['name'],
                            'role'             => $h['role'] ?? '',
                            'notes'            => $h['notes'] ?? null,
                            'sort_order'       => $idx,
                        ]);
                    }
                }
            }

            // Final upload directory
            $uploadSubdir = 'uploads/lease_applications/' . $appNo;
            $fullUploadPath = public_path($uploadSubdir);
            if (!file_exists($fullUploadPath)) {
                mkdir($fullUploadPath, 0777, true);
            }

            // If migrating from draft directory to official app directory, copy existing files
            if ($oldAppNo && $oldAppNo !== $appNo) {
                $oldPath = public_path('uploads/lease_applications/' . $oldAppNo);
                if (file_exists($oldPath) && is_dir($oldPath)) {
                    $files = scandir($oldPath);
                    foreach ($files as $f) {
                        if ($f !== '.' && $f !== '..') {
                            @copy($oldPath . '/' . $f, $fullUploadPath . '/' . $f);
                        }
                    }
                }
            }

            // Document metadata for all 19 items (16 regulatory + 3 plan)
            $docsMeta = [
                ['folder_id' => 7, 'doc_item' => 1,  'doc_name' => '1. Land Document', 'file_name' => 'land_document_title.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 7, 'doc_item' => 2,  'doc_name' => '2. Consent (If Applicable)', 'file_name' => 'landowner_consent_deed.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 7, 'doc_item' => 3,  'doc_name' => '3. Adangal & A-register', 'file_name' => 'adangal_a_register_record.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 7, 'doc_item' => 4,  'doc_name' => '4. Patta & Encumbrance Certificate', 'file_name' => 'patta_chitta_certificate.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 7, 'doc_item' => 5,  'doc_name' => '5. Work Order', 'file_name' => 'work_order_approval.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 7, 'doc_item' => 6,  'doc_name' => '6. Gazette', 'file_name' => 'district_gazette_notification.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 7, 'doc_item' => 7,  'doc_name' => '7. Recommendation Letter', 'file_name' => 'ad_mines_recommendation_letter.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 7, 'doc_item' => 8,  'doc_name' => '8. Mineral Management System – Application', 'file_name' => 'mimas_portal_application.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 7, 'doc_item' => 9,  'doc_name' => '9. Challan downloaded from Mimas', 'file_name' => 'mimas_treasury_challan.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 8, 'doc_item' => 10, 'doc_name' => '10. Lease application – signed, FMB, Plan', 'file_name' => 'signed_lease_application.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 8, 'doc_item' => 11, 'doc_name' => '11. Affidavit – Income Tax', 'file_name' => 'affidavit_income_tax.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 8, 'doc_item' => 12, 'doc_name' => '12. IT returns (If Applicable)', 'file_name' => 'it_returns_assessment.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 8, 'doc_item' => 13, 'doc_name' => '13. Affidavit – Mining Due', 'file_name' => 'affidavit_mining_dues.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 8, 'doc_item' => 14, 'doc_name' => '14. Affidavit – Mining Lease', 'file_name' => 'affidavit_mining_lease.pdf', 'file_type' => 'application/pdf', 'status' => 'validated'],
                ['folder_id' => 8, 'doc_item' => 15, 'doc_name' => '15. Affidavit – 1.5 meter depth', 'file_name' => 'affidavit_depth_safety.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 8, 'doc_item' => 16, 'doc_name' => '16. Affidavit – Hill Areas', 'file_name' => 'affidavit_hill_areas.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 9, 'doc_item' => 17, 'doc_name' => '17. Plan Source File', 'file_name' => 'plan_source_file.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
                ['folder_id' => 9, 'doc_item' => 18, 'doc_name' => '18. KML File', 'file_name' => 'quarry_boundary.kml', 'file_type' => 'application/xml', 'status' => 'uploaded'],
                ['folder_id' => 9, 'doc_item' => 19, 'doc_name' => '19. Plan PDF', 'file_name' => 'quarry_plan_layout.pdf', 'file_type' => 'application/pdf', 'status' => 'uploaded'],
            ];

            $draftUploads = $draft['uploaded_docs'] ?? [];

            foreach ($docsMeta as $d) {
                $existingDoc = LeaseDocument::where('lease_application_id', $leaseApp->id)
                    ->where('document_field_id', $d['doc_item'])
                    ->first();

                $hasRealUpload = false;
                $targetFileName = null;
                $targetFilePath = null;
                $fileType = null;
                $size = 0;

                // Priority 1: Check if user uploaded a real file via Step 5
                if (isset($draftUploads[$d['doc_item']]) && !empty($draftUploads[$d['doc_item']]['draft_path'])) {
                    $draftFile = public_path($draftUploads[$d['doc_item']]['draft_path']);
                    if (file_exists($draftFile)) {
                        $targetFileName = basename($draftFile);
                        $targetFilePath = $fullUploadPath . '/' . $targetFileName;
                        if ($draftFile !== $targetFilePath) {
                            @copy($draftFile, $targetFilePath);
                        }
                        $hasRealUpload = true;
                        $size = filesize($targetFilePath);
                        $fileType = $draftUploads[$d['doc_item']]['file_type'] ?? 'application/pdf';
                    }
                }
                // Priority 2: Check if file was uploaded with final form
                elseif ($request->hasFile($d['file_name'])) {
                    $uploaded = $request->file($d['file_name']);
                    $targetFileName = $d['file_name'];
                    $uploaded->move($fullUploadPath, $targetFileName);
                    $targetFilePath = $fullUploadPath . '/' . $targetFileName;
                    $hasRealUpload = true;
                    $size = filesize($targetFilePath);
                    $fileType = $uploaded->getClientMimeType();
                }
                // Priority 3: Check if existing record had a real uploaded file
                elseif ($existingDoc && $existingDoc->file_path && file_exists(public_path($existingDoc->file_path))) {
                    $targetFileName = $existingDoc->file_name;
                    $targetFilePath = public_path($existingDoc->file_path);
                    $hasRealUpload = true;
                    $size = $existingDoc->file_size;
                    $fileType = $existingDoc->file_type;
                }

                if ($hasRealUpload) {
                    LeaseDocument::updateOrCreate(
                        [
                            'lease_application_id' => $leaseApp->id,
                            'document_field_id'    => $d['doc_item'],
                        ],
                        [
                            'folder_id'     => $d['folder_id'],
                            'document_name' => $d['doc_name'],
                            'file_name'     => $targetFileName,
                            'file_path'     => $uploadSubdir . '/' . $targetFileName,
                            'file_type'     => $fileType,
                            'file_size'     => $size,
                            'status'        => ($existingDoc && in_array($existingDoc->status, ['validated', 'approved'])) ? $existingDoc->status : 'uploaded',
                            'uploaded_by'   => $user->id,
                            'uploaded_at'   => $existingDoc ? ($existingDoc->uploaded_at ?? now()) : now(),
                        ]
                    );
                } else {
                    // Document was NOT uploaded by user: record as pending with no dummy file
                    LeaseDocument::updateOrCreate(
                        [
                            'lease_application_id' => $leaseApp->id,
                            'document_field_id'    => $d['doc_item'],
                        ],
                        [
                            'folder_id'     => $d['folder_id'],
                            'document_name' => $d['doc_name'],
                            'file_name'     => null,
                            'file_path'     => null,
                            'file_type'     => null,
                            'file_size'     => 0,
                            'status'        => 'pending',
                            'uploaded_by'   => null,
                            'uploaded_at'   => null,
                        ]
                    );
                }
            }

            // Priority 4: Custom documents persistence from draft uploads
            foreach ($draftUploads as $k => $cDoc) {
                if (!empty($cDoc['is_custom']) || (is_string($k) && str_starts_with($k, 'custom_'))) {
                    $cDocName = $cDoc['doc_name'] ?? 'Custom Document';
                    $cFolderId = $cDoc['folder_id'] ?? 7;
                    $targetFileName = null;
                    $targetFilePath = null;
                    $fileType = $cDoc['file_type'] ?? 'application/pdf';
                    $size = 0;
                    if (!empty($cDoc['draft_path'])) {
                        $draftFile = public_path($cDoc['draft_path']);
                        if (file_exists($draftFile)) {
                            $targetFileName = basename($draftFile);
                            $targetFilePath = $fullUploadPath . '/' . $targetFileName;
                            if ($draftFile !== $targetFilePath) {
                                @copy($draftFile, $targetFilePath);
                            }
                            $size = filesize($targetFilePath);
                        }
                    }
                    LeaseDocument::create([
                        'lease_application_id' => $leaseApp->id,
                        'document_field_id'    => null,
                        'folder_id'            => $cFolderId,
                        'document_name'        => $cDocName,
                        'file_name'            => $targetFileName,
                        'file_path'            => $targetFilePath ? ($uploadSubdir . '/' . $targetFileName) : null,
                        'file_type'            => $fileType,
                        'file_size'            => $size,
                        'status'               => $targetFilePath ? 'uploaded' : 'pending',
                        'uploaded_by'          => $user->id,
                        'uploaded_at'          => now(),
                    ]);
                }
            }

            // Clean up old draft directory if different from official app directory
            if ($oldAppNo && $oldAppNo !== $appNo) {
                $oldPath = public_path('uploads/lease_applications/' . $oldAppNo);
                if (file_exists($oldPath) && is_dir($oldPath)) {
                    @array_map('unlink', glob("$oldPath/*.*"));
                    @rmdir($oldPath);
                }
            }

            // Log the activity
            $this->logActivity($leaseApp->id, 'application_submitted', 'Lease application ' . $appNo . ' submitted with ' . count($docsMeta) . ' documents.');
        });

        // Clear draft session after successful submission
        session()->forget('lease_draft');

        if ($request->input('move_to_mining') == 1 && $submittedAppId) {
            return $this->moveToMining($request, $submittedAppId);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'   => 1,
                'message'  => 'Lease application ' . $appNo . ' submitted successfully!',
                'redirect' => '/application',
            ]);
        }

        return redirect('/application')->with('success', 'Lease Application ' . $appNo . ' submitted successfully!');
    }

    // ───────────────────────────────────────
    // View Application Dossier
    // ───────────────────────────────────────

    public function viewApplication(Request $request)
    {
        $id = $request->query('id');
        $withRelations = [
            'customer', 'district', 'category', 'mineral', 'minerals', 'surveyNumbers', 'mimasCredentials', 'miningApplications',
            'documents' => function($q) {
                $q->with('folder')->orderBy('folder_id')->orderBy('id');
            }
        ];

        $application = $id
            ? LeaseApplication::with($withRelations)->find($id)
            : LeaseApplication::with($withRelations)->latest()->first();

        if (!$application) {
            return redirect('/application')->with('error', 'The requested lease application dossier was not found.');
        }

        $activityLogs = ActivityLog::where('loggable_type', 'lease_application')
            ->where('loggable_id', $application->id)
            ->orderByDesc('created_at')
            ->take(20)
            ->get();

        return view('pages.lease_application.viewapplication', compact('application', 'activityLogs'));
    }

    // ───────────────────────────────────────
    // Process Flow 6.2: Validate Application
    // ───────────────────────────────────────

    public function validateApplication(Request $request, $id)
    {
        $app = LeaseApplication::findOrFail($id);

        $request->validate([
            'action'  => 'required|in:pass,fail',
            'remarks' => 'nullable|string|max:1000',
        ]);

        if ($request->action === 'pass') {
            $app->update(['status' => 'validated', 'current_step' => 8]);

            // Automatically mark all uploaded documents for this application as validated
            LeaseDocument::where('lease_application_id', $id)
                ->whereNotNull('file_path')
                ->update([
                    'status'      => 'validated',
                    'reviewed_by' => Auth::id() ?? 1,
                    'reviewed_at' => now(),
                ]);

            $this->logActivity($id, 'data_validated', 'Application and uploaded documents validated. ' . ($request->remarks ?? ''));
            $message = 'Application and documents validated successfully.';
        } else {
            $app->update(['status' => 'revision_required']);
            $this->logActivity($id, 'validation_failed', 'Sent back for revision. Reason: ' . ($request->remarks ?? 'No reason provided'));
            $message = 'Application sent back for revision.';
        }

        if ($request->ajax()) {
            return response()->json(['status' => 1, 'message' => $message]);
        }
        return back()->with('success', $message);
    }

    // ───────────────────────────────────────
    // Process Flow: Update Single Document Status
    // ───────────────────────────────────────

    public function updateDocumentStatus(Request $request, $id)
    {
        $doc = LeaseDocument::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,uploaded,validated,approved,revision_required',
            'note'   => 'nullable|string|max:500',
        ]);

        $oldStatus = $doc->status;
        $newStatus = $request->status;

        $doc->update([
            'status'       => $newStatus,
            'review_note'  => $request->note ?? $doc->review_note,
            'reviewed_by'  => Auth::id() ?? 1,
            'reviewed_at'  => now(),
        ]);

        $this->logActivity(
            $doc->lease_application_id,
            'document_' . $newStatus,
            "Document '{$doc->document_name}' status updated to " . ucfirst(str_replace('_', ' ', $newStatus)) . ($request->note ? " (Note: {$request->note})" : "")
        );

        $totalUploaded = LeaseDocument::where('lease_application_id', $doc->lease_application_id)->whereNotNull('file_path')->count();
        $totalValidated = LeaseDocument::where('lease_application_id', $doc->lease_application_id)->where('status', 'validated')->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'          => 1,
                'message'         => "Document status updated to " . ucfirst(str_replace('_', ' ', $newStatus)),
                'doc_id'          => $doc->id,
                'new_status'      => $newStatus,
                'total_uploaded'  => $totalUploaded,
                'total_validated' => $totalValidated,
            ]);
        }

        return back()->with('success', "Document status updated to " . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    // ───────────────────────────────────────
    // Process Flow 6.3: Approve Application
    // ───────────────────────────────────────

    public function approveApplication(Request $request, $id)
    {
        $app = LeaseApplication::findOrFail($id);

        if (!in_array($app->status, ['validated', 'under_scrutiny'])) {
            return back()->with('error', 'Application must be validated before approval.');
        }

        $app->update([
            'status'       => 'approved',
            'current_step' => 9,
        ]);

        $this->logActivity($id, 'application_approved', 'Application approved by ' . (Auth::user()->name ?? 'System') . '. ' . ($request->remarks ?? ''));

        if ($request->ajax()) {
            return response()->json(['status' => 1, 'message' => 'Application approved!']);
        }
        return back()->with('success', 'Application approved successfully!');
    }

    // ───────────────────────────────────────
    // Process Flow 6.2: Reject / Send for Revision
    // ───────────────────────────────────────

    public function rejectApplication(Request $request, $id)
    {
        $app = LeaseApplication::findOrFail($id);

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $app->update([
            'status'       => 'revision_required',
            'current_step' => 5,
        ]);

        $this->logActivity($id, 'application_rejected', 'Sent back for revision. Reason: ' . $request->reason);

        if ($request->ajax()) {
            return response()->json(['status' => 1, 'message' => 'Application sent back for document correction.']);
        }
        return back()->with('warning', 'Application sent back for revision. Reason: ' . $request->reason);
    }

    // ───────────────────────────────────────
    // Process Flow 6.4: Generate Report (PDF)
    // ───────────────────────────────────────

    public function generateReport($id)
    {
        $application = LeaseApplication::with(['customer', 'district', 'category', 'mineral', 'minerals', 'surveyNumbers', 'mimasCredentials', 'documents.folder'])
            ->findOrFail($id);

        $this->logActivity($id, 'report_generated', 'Summary report generated.');

        // Generate a simple HTML-based PDF summary (no external library needed)
        $html = view('pages.lease_application.report_pdf', compact('application'))->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="' . $application->application_no . '_report.html"');
    }

    // ───────────────────────────────────────
    // Helper: Activity Log
    // ───────────────────────────────────────

    private function logActivity(int $referenceId, string $action, string $description): void
    {
        try {
            ActivityLog::create([
                'user_id'       => Auth::id() ?? 1,
                'action'        => $action,
                'description'   => $description,
                'loggable_type' => 'lease_application',
                'loggable_id'   => $referenceId,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'created_at'    => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail — activity logging should never break the main flow
            \Log::warning('Activity log failed: ' . $e->getMessage());
        }
    }

    /**
     * Cross-Module Transition: Promote Lease Application to Mining Plan under Universal Common ID.
     */
    public function moveToMining(Request $request, $id)
    {
        $lease = LeaseApplication::with(['customer', 'district', 'category', 'mineral', 'minerals', 'surveyNumbers', 'documents', 'miningApplications'])->findOrFail($id);

        // Idempotency: Check if already moved
        $existingMining = $lease->miningApplications()->first();
        if ($existingMining) {
            $msg = "This lease application is already linked to Mining Plan: {$existingMining->common_id} ({$existingMining->application_no}).";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status'   => 1,
                    'message'  => $msg,
                    'redirect' => '/process?id=' . $existingMining->id,
                ]);
            }
            return redirect('/process?id=' . $existingMining->id)->with('info', $msg);
        }

        // 1. Resolve Universal Common ID (GTMS-YYYY-XXXX)
        $commonId = $lease->common_id;
        if (empty($commonId)) {
            $year = date('Y', strtotime($lease->created_at ?? 'now'));
            if ($lease->application_no && preg_match('/LA-(\d{4}-\d+)/', $lease->application_no, $m)) {
                $commonId = 'GTMS-' . $m[1];
            } else {
                $commonId = 'GTMS-' . $year . '-' . str_pad($lease->id, 4, '0', STR_PAD_LEFT);
            }
            $lease->update(['common_id' => $commonId]);
        }

        // 2. Generate Mining Application Number (MP-YYYY-XXXX)
        $suffix = substr($commonId, 5); // e.g. '2026-0011'
        $miningAppNo = 'MP-' . $suffix;
        if (MiningApplication::where('application_no', $miningAppNo)->exists()) {
            $miningAppNo = 'MP-' . $suffix . '-A';
        }

        // 3. Resolve Lookup IDs (Nature of Work, Applicant Type, Plan Type)
        $natureOfWorkId = DB::table('nature_of_works')->where('name', 'like', '%Fresh%')->value('id') ?? 1;
        $applicantTypeId = DB::table('applicant_types')->value('id') ?? 1;
        $planTypeId = DB::table('plan_types')->where('name', 'like', '%Mining Plan%')->value('id') ?? 1;

        $surveyText = $lease->surveyNumbers->pluck('survey_no')->filter()->join(', ');
        if (empty($surveyText)) {
            $surveyText = $lease->sf_no ?? 'SF.No 1';
        }

        // 4. Create Mining Application Record
        $miningApp = MiningApplication::create([
            'common_id'            => $commonId,
            'application_no'       => $miningAppNo,
            'customer_id'          => $lease->customer_id,
            'lease_application_id' => $lease->id,
            'nature_of_work_id'    => $natureOfWorkId,
            'applicant_type_id'    => $applicantTypeId,
            'plan_type_id'         => $planTypeId,
            'district_id'          => $lease->district_id,
            'mineral_id'           => $lease->mineral_id,
            'other_mineral_name'   => $lease->other_mineral_name,
            'taluk'                => $lease->taluk,
            'village'              => $lease->village,
            'survey_numbers_text'  => $surveyText,
            'area_extent_ha'       => $lease->area_extent_ha,
            'validity_years'       => $lease->lease_period_years ?? 5,
            'stage'                => '6.1',
            'status'               => 'draft',
            'branch_id'            => $lease->branch_id,
            'created_by'           => Auth::id() ?? 1,
        ]);

        // Sync mineral pivot (all selected minerals from lease application)
        $mineralIds = $lease->minerals->pluck('id')->toArray();
        if (empty($mineralIds) && $lease->mineral_id) {
            $mineralIds = [$lease->mineral_id];
        }
        if (!empty($mineralIds)) {
            $miningApp->minerals()->sync($mineralIds);
        }

        // 5. Physical Document Auto-Cloning to Mining Storage
        $miningUploadSubdir = 'uploads/mining/' . $miningAppNo;
        $miningUploadPath = public_path($miningUploadSubdir);
        if (!file_exists($miningUploadPath)) {
            mkdir($miningUploadPath, 0777, true);
        }

        // Folders in Mining Module: 2 = Documents, 5 = Plan
        $clonedCount = 0;
        foreach ($lease->documents as $lDoc) {
            if (!empty($lDoc->file_path) && file_exists(public_path($lDoc->file_path))) {
                $sourcePath = public_path($lDoc->file_path);
                $destFileName = basename($sourcePath);
                $destPath = $miningUploadPath . '/' . $destFileName;
                @copy($sourcePath, $destPath);

                // Map target folder: KML/Plan -> Folder 5 (Plan), others -> Folder 2 (Documents)
                $targetFolderId = 2;
                $docLower = strtolower($lDoc->document_name . ' ' . $destFileName);
                if (str_contains($docLower, 'kml') || str_contains($docLower, 'plan') || str_contains($docLower, 'drawing') || $lDoc->folder_id == 9) {
                    $targetFolderId = 5;
                }

                MiningDocument::create([
                    'mining_application_id' => $miningApp->id,
                    'folder_id'             => $targetFolderId,
                    'document_field_id'     => null, // Custom/carried document
                    'document_name'         => $lDoc->document_name,
                    'file_name'             => $destFileName,
                    'file_path'             => $miningUploadSubdir . '/' . $destFileName,
                    'file_type'             => $lDoc->file_type ?? 'application/pdf',
                    'file_size'             => file_exists($destPath) ? filesize($destPath) : ($lDoc->file_size ?? 0),
                    'status'                => in_array($lDoc->status, ['validated', 'approved']) ? 'validated' : 'uploaded',
                    'uploaded_by'           => Auth::id() ?? 1,
                    'uploaded_at'           => now(),
                ]);
                $clonedCount++;
            }
        }

        // 6. Initialize default required fields for Mining Folders (marked as pending if not uploaded)
        $natureOfWork = $natureOfWorkId;
        $standardFields = DocumentField::whereIn('folder_id', [1, 2, 3, 4, 5, 6])
            ->where(function($q) use ($natureOfWork) {
                $q->where('nature_of_work_id', $natureOfWork)
                  ->orWhereNull('nature_of_work_id');
            })
            ->get();

        foreach ($standardFields as $field) {
            $alreadyExists = MiningDocument::where('mining_application_id', $miningApp->id)
                ->where('document_field_id', $field->id)
                ->exists();

            if (!$alreadyExists) {
                MiningDocument::create([
                    'mining_application_id' => $miningApp->id,
                    'folder_id'             => $field->folder_id,
                    'document_field_id'     => $field->id,
                    'document_name'         => $field->name,
                    'file_name'             => null,
                    'file_path'             => null,
                    'file_type'             => null,
                    'file_size'             => null,
                    'status'                => 'pending',
                    'uploaded_by'           => null,
                    'uploaded_at'           => null,
                ]);
            }
        }

        // 7. Log Activity in unified audit trail
        $this->logActivity($lease->id, 'moved_to_mining', "Promoted to Mining Plan {$miningAppNo} under Common Tracking ID: {$commonId}. {$clonedCount} verified documents auto-cloned.");

        try {
            ActivityLog::create([
                'user_id'       => Auth::id() ?? 1,
                'action'        => 'created_from_lease',
                'description'   => "Mining Plan initiated from Lease Application {$lease->application_no} under Common Tracking ID: {$commonId}. {$clonedCount} verified statutory documents auto-cloned.",
                'loggable_type' => 'mining_application',
                'loggable_id'   => $miningApp->id,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'created_at'    => now(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Activity log failed: ' . $e->getMessage());
        }

        $successMsg = "🎉 Successfully transitioned to Mining Plan domain under Common ID: {$commonId}! {$clonedCount} statutory documents auto-carried.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status'   => 1,
                'message'  => $successMsg,
                'redirect' => '/process?id=' . $miningApp->id,
            ]);
        }

        return redirect('/process?id=' . $miningApp->id)->with('success', $successMsg);
    }

    // ───────────────────────────────────────
    // Helper: Atomic Application Number Generators
    // ───────────────────────────────────────

    private function generateDraftAppNumber(): string
    {
        $year = date('Y');
        $prefix = "LA-DRAFT-{$year}-";

        $maxApp = LeaseApplication::withTrashed()
            ->where('application_no', 'like', "{$prefix}%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(application_no, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextSeq = 1;
        if ($maxApp && preg_match('/-(\d+)$/', $maxApp->application_no, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }

        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }

    private function generateOfficialAppNumber(): string
    {
        $year = date('Y');
        $prefix = "LA-{$year}-";

        $maxApp = LeaseApplication::withTrashed()
            ->where('status', '!=', 'draft')
            ->where('application_no', 'like', "{$prefix}%")
            ->where('application_no', 'not like', 'LA-DRAFT-%')
            ->orderByRaw("CAST(SUBSTRING_INDEX(application_no, '-', -1) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->first();

        $nextSeq = 1;
        if ($maxApp && preg_match('/-(\d+)$/', $maxApp->application_no, $matches)) {
            $nextSeq = (int)$matches[1] + 1;
        }

        return $prefix . str_pad((string)$nextSeq, 4, '0', STR_PAD_LEFT);
    }

    // ───────────────────────────────────────
    // Helper: Format file size
    // ───────────────────────────────────────

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 0) . ' KB';
        return $bytes . ' B';
    }
}
