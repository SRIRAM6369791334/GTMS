@extends('layouts.app')
@section('title', 'New Mining Application')
@section('main_content')

<style>
.mineral-checkbox:checked + .mineral-card {
    background-color: #1e3a8a !important;
    color: #ffffff !important;
    border-color: #1e3a8a !important;
    box-shadow: 0 4px 10px rgba(30, 58, 138, 0.25);
}
.mineral-checkbox:checked + .mineral-card .mineral-icon {
    color: #38bdf8 !important;
}
.mineral-checkbox:checked + .mineral-card .badge-select-indicator {
    background-color: #ffffff !important;
    color: #1e3a8a !important;
    font-weight: bold;
    border-color: #ffffff !important;
}
</style>

    <div class="content-body default-height">
        <div class="container-fluid">

            <main class="page" style="max-width:980px; margin:0 auto;">
                <div class="page-head mb-4">
                    <div>
                        <span class="eyebrow"><i class="bi bi-signpost-split"></i> Mining Portal &rsaquo; Intake</span>
                        <h1 class="h3 fw-bold mt-1">Register a new mining application</h1>
                        <p class="text-muted small">Follow the stages below &mdash; lookup or enter applicant details, choose the work scope, and launch the statutory dossier.</p>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Stepper -->
                <div class="surface p-3 p-lg-4 mb-3 rounded-3 shadow-sm border">
                    <div class="flow-stepper" id="wizardStepper">
                        <div class="flow-step active" id="stepNode_0">
                            <div class="node"><div class="circle">1</div><div class="lbl">Client Info</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_1">
                            <div class="node"><div class="circle">2</div><div class="lbl">Nature of Work</div></div>
                        </div>
                        <div class="flow-connector" id="conn_mineral"></div>
                        <div class="flow-step" id="stepNode_2">
                            <div class="node"><div class="circle">3</div><div class="lbl">Minerals &amp; Plan</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_3">
                            <div class="node"><div class="circle" id="circle_district">4</div><div class="lbl">District</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_4">
                            <div class="node"><div class="circle" id="circle_folders">5</div><div class="lbl">Folders</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_5">
                            <div class="node"><div class="circle" id="circle_upload">6</div><div class="lbl">Upload Docs</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_6">
                            <div class="node"><div class="circle" id="circle_handlers">7</div><div class="lbl">Handling Team</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_7">
                            <div class="node"><div class="circle" id="circle_payment">8</div><div class="lbl">Payment</div></div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step" id="stepNode_8">
                            <div class="node"><div class="circle" id="circle_preview">9</div><div class="lbl">Preview</div></div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('newapplication.store') }}" id="miningWizardForm" enctype="multipart/form-data">
                    @csrf

                    @if(!empty($prefillData['mining_app_id']) || !empty($prefillData['lease_application_id']))
                        <div class="alert alert-primary d-flex align-items-center justify-content-between p-3 mb-3 border-primary-subtle shadow-sm rounded-3" style="background:#eef6ff; border: 1.5px solid #93c5fd;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px; height:40px; background:#0F1E4D; color:#fff;">
                                    <i class="fa fa-sync-alt fa-spin fa-fw"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-navy" style="font-size: 15px;">
                                        @if(!empty($prefillData['application_no']))
                                            Resuming Application: {{ $prefillData['application_no'] }}
                                        @else
                                            Imported from Lease Application
                                        @endif
                                        @if(!empty($prefillData['common_id']))
                                            <span class="badge bg-light text-dark border ms-1">{{ $prefillData['common_id'] }}</span>
                                        @endif
                                    </div>
                                    <span class="text-muted small">
                                        Client profile, district, concessions, and survey extents from Lease Application have been auto-filled. Complete the required Mining fields below to finalize your plan.
                                    </span>
                                </div>
                            </div>
                            <span class="badge text-white px-3 py-2 fw-semibold" style="background:#0F1E4D; font-size:12px;">
                                <i class="fa fa-check-circle me-1 text-success"></i> Data Auto-Fetched
                            </span>
                        </div>
                    @endif

                    <div class="surface p-4 p-lg-5 rounded-3 shadow-sm border">

                        {{-- ========================================================== --}}
                        {{-- STEP 1: CLIENT INFORMATION (UNIVERSAL MIMAS LOOKUP + PROFILE) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane" id="pane_0">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-person-badge me-1"></i>Step 1 &middot; Client Information</span>
                            <h2 class="h5 fw-bold mt-1 mb-1">Applicant &amp; Enterprise Details</h2>
                            <p class="text-muted small mb-3">Lookup an existing registered customer via Customer Unique ID or enter the applicant identity details below.</p>

                            <!-- CUSTOMER UNIQUE ID LOOKUP CARD (MATCHING LEASE APPLICATION) -->
                            <div class="card p-3 mb-4" style="background:#f0f7ff; border:2px dashed #93c5fd; border-radius:12px;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold mb-0 text-primary small">
                                        <i class="fa fa-fingerprint me-1"></i> Customer Unique ID Lookup
                                    </label>
                                    <span class="badge bg-primary text-white" style="font-size:11px;"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
                                </div>
                                <p class="text-muted small mb-2">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
                                
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-primary"><i class="fa fa-search text-primary"></i></span>
                                    <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold border-primary" 
                                           placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off"
                                           value="{{ $prefillData['mimas_no'] ?? '' }}">
                                    <button class="btn btn-primary px-3 fw-bold" type="button" id="btn_lookup_mimas">
                                        <i class="fa fa-sync-alt me-1"></i> Fetch Details
                                    </button>
                                </div>
                                
                                <datalist id="mimas_datalist">
                                    @if(isset($customers))
                                        @foreach($customers as $c)
                                            <option value="{{ $c->mimas_no }}">{{ $c->company_name }} ({{ $c->customer_name }})</option>
                                        @endforeach
                                    @endif
                                </datalist>

                                <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
                            </div>

                            <!-- HIDDEN RESUME, LEASE & CUSTOMER FIELDS -->
                            <input type="hidden" name="mining_app_id" id="field_mining_app_id" value="{{ $prefillData['mining_app_id'] ?? '' }}">
                            <input type="hidden" name="lease_application_id" id="field_lease_application_id" value="{{ $prefillData['lease_application_id'] ?? '' }}">
                            <input type="hidden" name="common_id" id="field_common_id" value="{{ $prefillData['common_id'] ?? '' }}">
                            <input type="hidden" name="customer_id" id="field_customer_id" value="{{ old('customer_id', $prefillData['customer_id'] ?? '') }}">
                            <input type="hidden" name="mimas_no" id="field_mimas_no" value="{{ old('mimas_no', $prefillData['mimas_no'] ?? '') }}">

                            <!-- PROFILE FORM FIELDS (LEASE APPLICATION FLOW) -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Client / Representative Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="field_client_name" name="client_name" 
                                           placeholder="e.g. R. Kumaresan" value="{{ old('client_name', $prefillData['client_name'] ?? '') }}" required>
                                    <div class="form-text mt-1 text-muted" style="font-size:11px;">As it appears on the official government ID proof.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Company / Firm / Quarry Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="field_company_name" name="company_name" 
                                           placeholder="e.g. Sri Bala Traders" value="{{ old('company_name', $prefillData['company_name'] ?? '') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Applicant Category <span class="text-danger">*</span></label>
                                    <select name="applicant_type_id" id="applicant_type_id" class="form-select" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($applicantTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('applicant_type_id', $prefillData['applicant_type_id'] ?? '') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Primary Mobile Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="field_mobile_num" name="mobile_num" 
                                           placeholder="10-digit mobile number" value="{{ old('mobile_num', $prefillData['mobile_num'] ?? '') }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Secondary Contact Person (Optional)</label>
                                    <input type="text" class="form-control" id="field_secondary_contact_person" name="secondary_contact_person" 
                                           placeholder="e.g. Site Supervisor / Manager" value="{{ old('secondary_contact_person', $prefillData['secondary_contact_person'] ?? '') }}">
                                    <div class="form-text mt-1 text-muted" style="font-size:11px;">Site supervisor or operational representative.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Secondary Mobile Number (Optional)</label>
                                    <input type="text" class="form-control" id="field_secondary_mobile_num" name="secondary_mobile_num" 
                                           placeholder="10-digit alternative number" value="{{ old('secondary_mobile_num', $prefillData['secondary_mobile_num'] ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email Address</label>
                                    <input type="email" class="form-control" id="field_email" name="email" 
                                           placeholder="customer@example.com" value="{{ old('email', $prefillData['email'] ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">PAN Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control text-uppercase" id="field_pan" name="pan" 
                                           placeholder="AAACS1234F" maxlength="10" value="{{ old('pan', $prefillData['pan'] ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Aadhaar Number</label>
                                    <input type="text" class="form-control" id="field_aadhaar_no" name="aadhaar_no" 
                                           placeholder="XXXX-XXXX-XXXX" maxlength="14" value="{{ old('aadhaar_no', $prefillData['aadhaar_no'] ?? '') }}">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">GSTIN (Optional)</label>
                                    <input type="text" class="form-control text-uppercase" id="field_gstin" name="gstin" 
                                           placeholder="33AAACS1234F1Z5" maxlength="15" value="{{ old('gstin', $prefillData['gstin'] ?? '') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Registered Office Address</label>
                                    <textarea class="form-control" id="field_address" name="address" rows="2" 
                                              placeholder="Street, City, Postal Code">{{ old('address', $prefillData['address'] ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 2: NATURE OF WORK (8 CHOICES) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_1">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-diagram-3 me-1"></i>Step 2 &middot; Nature of Work</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Select the Nature of Work</h2>
                            <p class="text-muted small mb-3">The software process flow and folder document checklist dynamically configure based on this choice.</p>
                            
                            <div class="row g-2">
                                @foreach($natureOfWorks as $now)
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check" name="nature_of_work_id"
                                               id="now_{{ $now->id }}" value="{{ $now->id }}"
                                               data-name="{{ $now->name }}"
                                                {{ (old('nature_of_work_id', $prefillData['nature_of_work_id'] ?? '') == $now->id || (empty(old('nature_of_work_id')) && empty($prefillData['nature_of_work_id']) && $loop->first)) ? 'checked' : '' }} required>
                                        <label class="btn btn-outline-navy w-100 text-start p-3 h-100 d-flex flex-column justify-content-between" for="now_{{ $now->id }}" style="cursor:pointer; border-radius:8px;">
                                            <div>
                                                <div class="badge bg-light text-navy mb-2 px-2 py-1" style="font-size:11px;">
                                                    @if($loop->index == 0) 2.1 @elseif($loop->index == 1) 2.2 @elseif($loop->index == 2) 2.3 @elseif($loop->index == 3) 2.4 @elseif($loop->index == 4) 2.5 @elseif($loop->index == 5) 2.6 @elseif($loop->index == 6) 2.7 @else 2.7 @endif
                                                </div>
                                                <div class="fw-bold small mb-1">{{ $now->name }}</div>
                                            </div>
                                            <div class="text-muted" style="font-size:11px;">
                                                @if(strcasecmp($now->name, 'Mining Plan') == 0)
                                                    Full 6-folder statutory mining plan
                                                @elseif(strcasecmp($now->name, 'Stockyard') == 0)
                                                    Storage &amp; dispatch stockyard records
                                                @elseif(strcasecmp($now->name, 'Mine Closure Plan') == 0)
                                                    Progressive / final closure compliance
                                                @elseif(strcasecmp($now->name, 'Scope Work') == 0)
                                                    Site extent &amp; demarcation checklist
                                                @elseif(strcasecmp($now->name, 'Opening Notice') == 0)
                                                    Statutory commencement notification
                                                @elseif(strcasecmp($now->name, 'E-Tender') == 0)
                                                    Tender gazette &amp; annexure forms
                                                @elseif(strcasecmp($now->name, 'Short Term') == 0)
                                                    Temporary minor mineral permit
                                                @else
                                                    Custom domain processing
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-2" style="background:#e8edf3;">
                                <i class="bi bi-info-circle-fill text-primary"></i>
                                <span class="small text-dark">
                                    <strong>Flow Rule:</strong> Selecting <strong>2.1 Mining Plan</strong> activates Mineral and Plan Type configurations. Other types route directly to District Selection.
                                </span>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 3: MINERALS & PLAN TYPE (BRANCH: ONLY FOR MINING PLAN) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_2">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-gem me-1"></i>Step 3 &middot; Minerals &amp; Plan Configuration</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">2.1 Minerals &amp; 2.1.1 Plan Type</h2>
                            <p class="text-muted small mb-3">This sub-configuration applies exclusively to <strong>Mining Plan</strong> domain applications.</p>

                            <div class="d-flex align-items-center justify-content-between mt-3 mb-2 flex-wrap gap-2">
                                <h6 class="fw-bold small text-uppercase text-secondary mb-0">2.1 Select Mineral(s):</h6>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 11px;">
                                    <i class="bi bi-check2-all me-1"></i>Multi-Select Enabled (Choose one or more)
                                </span>
                            </div>
                            <div class="row g-2 mb-4" id="minerals_selection_grid">
                                @php
                                    $oldMineralIds = old('mineral_ids', $prefillData['mineral_ids'] ?? []);
                                    if (!is_array($oldMineralIds)) {
                                        $oldMineralIds = $oldMineralIds ? [$oldMineralIds] : [];
                                    }
                                @endphp
                                @foreach($minerals as $mineral)
                                    <div class="col-6 col-md-4">
                                        <input type="checkbox" class="btn-check mineral-checkbox" name="mineral_ids[]"
                                               id="mineral_{{ $mineral->id }}" value="{{ $mineral->id }}"
                                               data-name="{{ $mineral->name }}"
                                               {{ (in_array($mineral->id, $oldMineralIds) || (empty($oldMineralIds) && empty($prefillData['mineral_ids']) && $loop->first)) ? 'checked' : '' }}>
                                        <label class="btn btn-outline-navy w-100 text-start py-2 px-3 d-flex align-items-center justify-content-between mineral-card" for="mineral_{{ $mineral->id }}" style="border-radius:6px; cursor:pointer;">
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-gem me-2 text-primary mineral-icon"></i>
                                                <span class="fw-semibold">{{ $mineral->name }}</span>
                                            </span>
                                            <span class="badge bg-light text-secondary border badge-select-indicator ms-2" style="font-size: 10px;">
                                                <i class="bi bi-check-lg"></i>
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Conditional "Other Mineral Name" input (appears when Others id=8 is checked) --}}
                            <div class="mb-4 d-none" id="other_mineral_box_mining" style="transition: all 0.3s ease;">
                                <label class="form-label fw-semibold text-dark"><i class="bi bi-pencil-square text-warning me-1"></i>Specify Other Mineral Name <span class="text-danger">*</span></label>
                                <input type="text" name="other_mineral_name" id="field_other_mineral_name_mining" class="form-control" placeholder="e.g. Quartz, Feldspar, Laterite..." maxlength="255" value="{{ old('other_mineral_name', $prefillData['other_mineral_name'] ?? '') }}">
                                <div class="form-text text-muted"><i class="bi bi-info-circle me-1"></i>Since you selected "Others", please specify the mineral name manually.</div>
                            </div>

                            <h6 class="fw-bold small text-uppercase text-secondary mb-2">2.1.1 Which Plan is Prepared?</h6>
                            <div class="d-flex flex-column gap-2">
                                @foreach($planTypes as $pt)
                                    <label class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="cursor:pointer;">
                                        <input type="radio" name="plan_type_id" value="{{ $pt->id }}"
                                               data-name="{{ $pt->name }}"
                                               {{ (old('plan_type_id', $prefillData['plan_type_id'] ?? '') == $pt->id || (empty(old('plan_type_id')) && empty($prefillData['plan_type_id']) && $loop->first)) ? 'checked' : '' }}>
                                        <div>
                                            <div class="fw-semibold small text-dark">{{ $pt->name }}</div>
                                            <div class="text-muted" style="font-size:11.5px;">
                                                @if(str_contains(strtolower($pt->name), 'fresh'))
                                                    First-time preparation for a new quarry lease area
                                                @elseif(str_contains(strtolower($pt->name), 'revised'))
                                                    Amendment to an existing approved mining plan
                                                @elseif(str_contains(strtolower($pt->name), 'modified'))
                                                    Change of scope or production target within the same lease
                                                @else
                                                    Combined scheme for group / cluster leases
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 4: DISTRICT & LOCATION SELECTION --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_3">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-geo-alt me-1"></i>3.0 District Selection</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">District Wise Processing</h2>
                            <p class="text-muted small mb-3">Assign the administrative jurisdiction and regional quarry site location.</p>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">District <span class="text-danger">*</span></label>
                                    <select name="district_id" id="district_id" class="form-select" required>
                                        <option value="">-- Select District --</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}" {{ old('district_id', $prefillData['district_id'] ?? '') == $district->id ? 'selected' : '' }}>
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Area Extent (in Hectares)</label>
                                    <input type="number" step="0.01" name="area_extent_ha" id="field_area_extent_ha" class="form-control" placeholder="e.g. 2.45" value="{{ old('area_extent_ha', $prefillData['area_extent_ha'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Taluk</label>
                                    <input type="text" name="taluk" id="taluk" class="form-control" placeholder="e.g. Mettur / Salem South" value="{{ old('taluk', $prefillData['taluk'] ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Village</label>
                                    <input type="text" name="village" id="village" class="form-control" placeholder="Village name" value="{{ old('village', $prefillData['village'] ?? '') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Survey Number(s)</label>
                                    <input type="text" name="survey_numbers_text" id="survey_numbers_text" class="form-control" placeholder="e.g. 112/3A, 112/3B, 115/1" value="{{ old('survey_numbers_text', $prefillData['survey_numbers_text'] ?? '') }}">
                                </div>
                            </div>
                            <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-2" style="background:#e8edf3;">
                                <i class="bi bi-shield-check text-primary"></i>
                                <span class="small text-dark">This district assignment determines the designated Mines Officer and regional storage repository.</span>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 5: FOLDER STRUCTURE PREVIEW --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_4">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-folder-check me-1"></i>Step 4 &middot; Folder Structure</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Folder Structure Configuration</h2>
                            <p class="text-muted small mb-3">Based on your selection of <strong class="text-navy" id="disp_selected_now">Mining Plan</strong>, the following folders and checklists will be created:</p>
                            
                            <div class="row g-3" id="folderPreviewCards">
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" style="background:#f8fafc; border-left: 4px solid #3b82f6 !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-primary-subtle text-primary"><i class="fa fa-clone"></i></div>
                                            <h6 class="fw-bold mb-0 small">1. Field Log Data</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_fieldlog">Loading...</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" style="background:#f8fafc; border-left: 4px solid #8b5cf6 !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-purple-subtle text-purple" style="color:#8b5cf6; background:#f5f3ff;"><i class="fa fa-folder-open"></i></div>
                                            <h6 class="fw-bold mb-0 small">2. Documents</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_documents">Loading...</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" style="background:#f8fafc; border-left: 4px solid #06b6d4 !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-cyan-subtle text-info"><i class="fa fa-file-image"></i></div>
                                            <h6 class="fw-bold mb-0 small">3. Site Photos</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_photos">Loading...</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" id="card_report" style="background:#f8fafc; border-left: 4px solid #f59e0b !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-warning-subtle text-warning"><i class="fa fa-pencil-square"></i></div>
                                            <h6 class="fw-bold mb-0 small">4. Report</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_report">Loading...</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" id="card_plan" style="background:#f8fafc; border-left: 4px solid #10b981 !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-success-subtle text-success"><i class="fa fa-hourglass"></i></div>
                                            <h6 class="fw-bold mb-0 small">5. Plan</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_plan">Loading...</div>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="folder-card p-3 rounded-3 border h-100" id="card_others" style="background:#f8fafc; border-left: 4px solid #ef4444 !important;">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <div class="p-2 rounded bg-danger-subtle text-danger"><i class="fa fa-pie-chart"></i></div>
                                            <h6 class="fw-bold mb-0 small">6. Others</h6>
                                        </div>
                                        <div class="text-muted small" id="fc_others">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 6: DOCUMENT UPLOAD CHECKLIST (LEASE APPLICATION PATTERN) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_5">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i>Step 5 &middot; Document Upload Checklist</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Upload Regulatory Documents &amp; Plans</h2>
                            <p class="text-muted small mb-3">Upload statutory documents, affidavits, site photographs, and digital survey plans for <strong class="text-navy" id="disp_upload_now">Mining Plan</strong> across the statutory folders.</p>

                            <!-- Overall Document Progress Header -->
                            <div class="card p-3 mb-4 rounded-3 border shadow-sm" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                    <div class="fw-bold small text-uppercase text-secondary">
                                        <i class="bi bi-pie-chart-fill me-1 text-primary"></i> Overall Document Completion
                                    </div>
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span class="fs-5 fw-bold text-primary" id="doc_counter_text">0 / 0</span>
                                        <span class="small text-muted">files attached</span>
                                        <span class="badge bg-primary text-white ms-2" id="doc_percent_badge">0%</span>
                                    </div>
                                </div>
                                <div class="progress" style="height:8px; border-radius:4px;">
                                    <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="doc_progress_bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>

                            <!-- Interactive Dropzone (Matching Lease Application Step 5) -->
                            <div class="card p-4 mb-4 text-center border-2 border-dashed rounded-3" id="wizard_dropzone" style="border-color:#93c5fd; background:#f0f7ff; cursor:pointer; transition:all 0.2s;">
                                <div>
                                    <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 2.2rem;"></i>
                                    <div class="fw-bold text-navy mt-1">Drag &amp; drop files here, or click to browse</div>
                                    <div class="text-muted small">Supports authentic PDF, PNG, JPG, or KML/CAD files from your computer</div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-navy btn-sm px-3" id="btn_batch_browse">
                                            <i class="bi bi-folder2-open me-1"></i> Browse Files to Match
                                        </button>
                                        <input type="file" id="batch_file_input" multiple class="d-none" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml,.dwg,.doc,.docx">
                                    </div>
                                </div>
                            </div>

                            <!-- Folder Checklists Grouped by Nature of Work -->
                            <div id="nature_doc_groups">
                                @if(isset($natureOfWorks) && isset($folders) && isset($documentFields))
                                    @foreach($natureOfWorks as $now)
                                        <div class="now-doc-group d-none" id="doc_group_now_{{ $now->id }}" data-now-id="{{ $now->id }}">
                                            @foreach($folders as $folder)
                                                @php
                                                    $fFields = $documentFields->where('nature_of_work_id', $now->id)->where('folder_id', $folder->id);
                                                @endphp
                                                @if($fFields->isNotEmpty())
                                                    <div class="folder-doc-block mb-3 border rounded-3 overflow-hidden bg-white shadow-sm">
                                                        <div class="folder-doc-header p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <div class="p-2 rounded bg-white text-primary border shadow-xs"><i class="fa fa-folder-open"></i></div>
                                                                <div>
                                                                    <div class="fw-bold small text-navy mb-0">{{ $loop->iteration }}. {{ $folder->name }}</div>
                                                                    <div class="text-muted folder-meta-text" style="font-size:11px;">Folder ID: #{{ $folder->id }} &middot; <span class="folder-total-count">{{ $fFields->count() }}</span> Checklist Items</div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 btn-open-mining-add-doc"
                                                                        data-folder-id="{{ $folder->id }}"
                                                                        data-folder-name="{{ $folder->name }}"
                                                                        data-now-id="{{ $now->id }}">
                                                                    <i class="bi bi-plus-circle me-1"></i>Add Document
                                                                </button>
                                                                <span class="badge bg-white text-secondary border small folder-pill-counter" id="pill_folder_{{ $now->id }}_{{ $folder->id }}">
                                                                    0 / {{ $fFields->count() }} Attached
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="p-3 folder-checklist-container" id="folder_checklist_{{ $now->id }}_{{ $folder->id }}">
                                                            @php
                                                                $customCarriedDocs = collect();
                                                                if (!empty($prefillData['existing_docs'])) {
                                                                    $customCarriedDocs = $prefillData['existing_docs']->filter(function($d) use ($folder) {
                                                                        return empty($d->document_field_id) && $d->folder_id == $folder->id && !empty($d->file_path);
                                                                    });
                                                                }
                                                            @endphp

                                                            @foreach($customCarriedDocs as $cDoc)
                                                                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom up" id="row_carried_{{ $cDoc->id }}">
                                                                    <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                                                                        <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px; height:32px; background:#ecfdf5; color:#10b981;">
                                                                            <i class="bi bi-check2-circle ci-status-icon"></i>
                                                                        </div>
                                                                        <div class="text-truncate">
                                                                            <div class="ci-name fw-semibold small text-dark text-truncate">{{ $cDoc->document_name }}</div>
                                                                            <div class="ci-meta text-success" style="font-size:11px;">
                                                                                <i class="fa fa-link me-1"></i> Carried from Lease: {{ $cDoc->file_name ?? basename($cDoc->file_path) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0">
                                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                                            <i class="bi bi-check-lg me-1"></i>Carried
                                                                        </span>
                                                                        <a href="{{ asset($cDoc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" title="View attached document">
                                                                            <i class="bi bi-eye"></i>
                                                                        </a>
                                                                        <input type="hidden" class="doc-file-input" data-has-existing="1">
                                                                    </div>
                                                                </div>
                                                            @endforeach

                                                            @foreach($fFields as $field)
                                                                @php
                                                                    $matchedDoc = null;
                                                                    if (!empty($prefillData['existing_docs'])) {
                                                                        $matchedDoc = $prefillData['existing_docs']->first(function($d) use ($field) {
                                                                            if (!empty($d->document_field_id) && $d->document_field_id == $field->id) {
                                                                                return true;
                                                                            }
                                                                            if (!empty($d->file_path)) {
                                                                                $dName = strtolower(trim(preg_replace('/^\d+[\.\s-]+/', '', $d->document_name)));
                                                                                $fName = strtolower(trim(preg_replace('/^\d+[\.\s-]+/', '', $field->name)));
                                                                                return ($dName && $fName && (str_contains($dName, $fName) || str_contains($fName, $dName)));
                                                                            }
                                                                            return false;
                                                                        });
                                                                    }
                                                                    $hasDoc = $matchedDoc && !empty($matchedDoc->file_path);
                                                                @endphp
                                                                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom {{ $hasDoc ? 'up' : '' }}" id="row_field_{{ $field->id }}" data-field-id="{{ $field->id }}">
                                                                    <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                                                                        <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px; height:32px; background:{{ $hasDoc ? '#ecfdf5' : '#f1f5f9' }}; color:{{ $hasDoc ? '#10b981' : '#64748b' }};">
                                                                            <i class="bi {{ $hasDoc ? 'bi-check2-circle' : 'bi-file-earmark' }} ci-status-icon"></i>
                                                                        </div>
                                                                        <div class="text-truncate">
                                                                            <div class="ci-name fw-semibold small text-dark text-truncate">{{ $loop->iteration }}. {{ $field->name }}</div>
                                                                            <div class="ci-meta {{ $hasDoc ? 'text-success' : 'text-muted' }}" style="font-size:11px;" id="meta_field_{{ $field->id }}">
                                                                                @if($hasDoc)
                                                                                    <i class="fa fa-link me-1"></i> Auto-Imported: {{ $matchedDoc->file_name ?? basename($matchedDoc->file_path) }}
                                                                                @else
                                                                                    Not uploaded &middot; Max 25 MB
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0">
                                                                        @if($hasDoc)
                                                                            <span class="badge bg-success-subtle text-success border border-success-subtle" id="badge_field_{{ $field->id }}">
                                                                                <i class="bi bi-check-lg me-1"></i>Attached
                                                                            </span>
                                                                            <a href="{{ asset($matchedDoc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-success py-1 px-2" title="View attached document">
                                                                                <i class="bi bi-eye"></i>
                                                                            </a>
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 btn-trigger-upload" data-field-id="{{ $field->id }}">
                                                                                <i class="bi bi-pencil me-1"></i>Change
                                                                            </button>
                                                                        @else
                                                                            <span class="badge-status {{ $field->required ? 'mandatory' : 'pending' }}" id="badge_field_{{ $field->id }}">
                                                                                {{ $field->required ? 'Mandatory' : 'Optional' }}
                                                                            </span>
                                                                            <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-trigger-upload" data-field-id="{{ $field->id }}">
                                                                                <i class="bi bi-upload me-1"></i>Upload
                                                                            </button>
                                                                        @endif
                                                                        <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-file d-none" data-field-id="{{ $field->id }}" title="Remove attachment">
                                                                            <i class="bi bi-x-lg"></i>
                                                                        </button>
                                                                        <input type="file" name="doc_files[{{ $field->id }}]" id="file_field_{{ $field->id }}" class="doc-file-input d-none" data-field-id="{{ $field->id }}" data-field-name="{{ $field->name }}" data-has-existing="{{ $hasDoc ? '1' : '0' }}" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml,.dwg,.doc,.docx">
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            <div class="alert alert-info d-flex align-items-center gap-2 mt-3 mb-0" role="alert">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <div class="small">
                                    <strong>Flexible Process:</strong> You can upload documents now or register first and upload them through the <strong>Project Folder Dossier</strong> at any time.
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 7: PROJECT HANDLING PERSONS / MULTI-USER ALLOCATION   --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_6">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-people-fill me-1"></i>Step <span class="lbl-step-num-handlers">7</span> &middot; Handling Persons</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Project Handling Team Allocation</h2>
                            <p class="text-muted small mb-3">Assign internal staff, surveyors, or consulting officers responsible for executing this mining application.</p>

                            <div class="card p-3 p-lg-4 mb-4 rounded-3 border shadow-sm" style="background:#f8fafc;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-navy"><i class="bi bi-person-lines-fill text-primary me-2"></i>Designated Application Handlers</h6>
                                        <div class="text-muted small">Add multiple team members and specify their roles manually.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-navy" id="btn_add_mining_handler">
                                        <i class="bi bi-person-plus-fill me-1"></i>+ Add Person
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle bg-white mb-0" id="mining_handlers_table">
                                        <thead class="table-light" style="font-size:0.85rem;">
                                            <tr>
                                                <th style="width: 50px;" class="text-center">#</th>
                                                <th style="min-width: 220px;">Person / Executive Name <span class="text-danger">*</span></th>
                                                <th style="min-width: 200px;">Role / Designation <span class="text-danger">*</span></th>
                                                <th style="min-width: 260px;">Notes / Responsibilities</th>
                                                <th style="width: 70px;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mining_handlers_tbody">
                                            @if(!empty($prefillData['handlers']) && count($prefillData['handlers']) > 0)
                                                @foreach($prefillData['handlers'] as $hIdx => $h)
                                                    <tr id="handler_row_{{ $hIdx }}">
                                                        <td class="text-center text-muted fw-semibold handler-idx-col">{{ $loop->iteration }}</td>
                                                        <td><input type="text" name="handlers[{{ $hIdx }}][name]" class="form-control form-control-sm handler-name-input" placeholder="e.g. Ramesh Kumar" value="{{ $h->name ?? '' }}" required></td>
                                                        <td><input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm handler-role-input" placeholder="e.g. Surveyor / Engineer" value="{{ $h->role ?? '' }}" required></td>
                                                        <td><input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm" placeholder="e.g. Field inspection & DGPS logs" value="{{ $h->notes ?? '' }}"></td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-mining-handler" data-idx="{{ $hIdx }}" title="Remove Person">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                <div class="text-muted small mt-2 d-flex align-items-center gap-1">
                                    <i class="bi bi-info-circle text-primary"></i>
                                    <span>Roles can be freely typed (e.g. <em>Surveyor, Mining Engineer, Liaison Officer, Documentation In-Charge</em>).</span>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 8: PAYMENT & FINANCIAL SETTLEMENT                     --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_7">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-cash-stack me-1"></i>Step <span class="lbl-step-num-payment">8</span> &middot; Payment Details</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Financial &amp; Billing Ledger</h2>
                            <p class="text-muted small mb-3">Record the statutory service or product valuation, advance received, and compute the balance settlement in real-time.</p>

                            <!-- Financial Summary KPI Cards -->
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-white border rounded-3 shadow-xs h-100 border-start border-primary border-4">
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Product Value</div>
                                        <div class="h5 fw-bold text-navy mb-0 mt-1" id="disp_mining_product_val">₹ {{ number_format((float)($prefillData['product_value'] ?? 0), 2) }}</div>
                                        <small class="text-muted" style="font-size: 11px;">Project Billing Cost</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-white border rounded-3 shadow-xs h-100 border-start border-success border-4">
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Paid Amount</div>
                                        <div class="h5 fw-bold text-success mb-0 mt-1" id="disp_mining_paid_val">₹ {{ number_format((float)($prefillData['paid_amount'] ?? 0), 2) }}</div>
                                        <small class="text-muted" style="font-size: 11px;">Received Advance</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-white border rounded-3 shadow-xs h-100 border-start border-danger border-4">
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Pending Balance</div>
                                        <div class="h5 fw-bold text-danger mb-0 mt-1" id="disp_mining_pending_val">₹ {{ number_format((float)($prefillData['pending_amount'] ?? 0), 2) }}</div>
                                        <small class="text-muted" style="font-size: 11px;">Auto-calculated balance</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="p-3 bg-white border rounded-3 shadow-xs h-100 border-start border-info border-4">
                                        <div class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Settlement Status</div>
                                        <div class="mt-1" id="disp_mining_status_badge">
                                            @php
                                                $curStatus = $prefillData['payment_status'] ?? 'pending';
                                            @endphp
                                            @if($curStatus === 'paid')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">🟢 Paid (Settled)</span>
                                            @elseif($curStatus === 'partial')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">🟡 Partial Payment</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">🔴 Pending Full Due</span>
                                            @endif
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">Ledger category</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Interactive Inputs -->
                            <div class="card p-4 rounded-3 border shadow-sm bg-white mb-3">
                                <h6 class="fw-bold text-navy mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Payment Breakdown &amp; Status</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Product / Service Value (₹) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                            <input type="number" step="0.01" min="0" class="form-control fw-bold text-navy" 
                                                   id="mining_product_value" name="product_value" 
                                                   value="{{ old('product_value', $prefillData['product_value'] ?? '0.00') }}" placeholder="0.00" required>
                                        </div>
                                        <div class="form-text" style="font-size:11px;">Total statutory project charges.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Paid Amount (₹) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                            <input type="number" step="0.01" min="0" class="form-control fw-bold text-success" 
                                                   id="mining_paid_amount" name="paid_amount" 
                                                   value="{{ old('paid_amount', $prefillData['paid_amount'] ?? '0.00') }}" placeholder="0.00" required>
                                        </div>
                                        <div class="form-text" style="font-size:11px;">Advance or amount paid so far.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Pending Balance (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light fw-bold text-muted">₹</span>
                                            <input type="number" step="0.01" class="form-control fw-bold text-danger bg-light" 
                                                   id="mining_pending_amount" name="pending_amount" 
                                                   value="{{ old('pending_amount', $prefillData['pending_amount'] ?? '0.00') }}" readonly placeholder="0.00">
                                        </div>
                                        <div class="form-text text-muted" style="font-size:11px;">Auto-calculated: (Value &minus; Paid).</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Settlement Status <span class="text-danger">*</span></label>
                                        <select class="form-select fw-semibold" id="mining_payment_status" name="payment_status" required>
                                            <option value="pending" {{ old('payment_status', $prefillData['payment_status'] ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending (No payment received)</option>
                                            <option value="partial" {{ old('payment_status', $prefillData['payment_status'] ?? '') == 'partial' ? 'selected' : '' }}>Partial (Partially paid, balance pending)</option>
                                            <option value="paid" {{ old('payment_status', $prefillData['payment_status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid (Fully settled)</option>
                                        </select>
                                        <div class="form-text" style="font-size:11px;">Auto-selects based on amounts, or select manually.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Payment Notes / Transaction Reference</label>
                                        <input type="text" class="form-control" id="mining_payment_notes" name="payment_notes" 
                                               placeholder="e.g. Advance paid via NEFT Ref: UTR291840, Cheque #10294" 
                                               value="{{ old('payment_notes', '') }}">
                                        <div class="form-text" style="font-size:11px;">Optional transaction details or instrument reference.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 9: APPLICATION SUMMARY & SUBMISSION                   --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_8">
                            <span class="small-caps-label text-primary fw-semibold"><i class="bi bi-card-checklist me-1"></i>Step <span class="lbl-step-num-preview">9</span> &middot; Review &amp; Launch</span>
                            <h2 class="h5 fw-bold mt-1 mb-2">Application Summary</h2>
                            <p class="text-muted small mb-3">Verify the applicant and project details below before generating the statutory folder dossier.</p>

                            <div class="card-panel mt-3 mb-3 p-4 rounded-3 border" style="background:#f8fafc;">
                                <div class="row g-3" style="font-size: 0.9rem;">
                                    <div class="col-md-4">
                                        <span class="text-muted small">Client / Enterprise:</span> <br>
                                        <strong class="text-dark" id="prev_client">-</strong>
                                        <div class="text-muted" style="font-size:11px;" id="prev_contact">-</div>
                                        <div class="text-muted" style="font-size:11px;" id="prev_mimas_box">Customer Unique ID: <strong class="text-primary" id="prev_mimas_val">-</strong></div>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted small">Nature of Work:</span> <br>
                                        <span class="badge bg-primary text-white" id="prev_nature">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted small">District &amp; Extent:</span> <br>
                                        <strong class="text-dark" id="prev_district">-</strong>
                                        <div class="text-muted" style="font-size:11px;" id="prev_extent">-</div>
                                    </div>
                                    <div class="col-md-4" id="prev_mineral_box">
                                        <span class="text-muted small">2.1 Mineral(s):</span> <br>
                                        <strong class="text-dark" id="prev_mineral">-</strong>
                                    </div>
                                    <div class="col-md-4" id="prev_plan_box">
                                        <span class="text-muted small">2.1.1 Plan Type:</span> <br>
                                        <strong class="text-dark" id="prev_plan">-</strong>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted small">Folder Structure:</span> <br>
                                        <span class="badge bg-success" id="prev_folder_badge">Folders Active</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted small">Uploaded Documents:</span> <br>
                                        <strong class="text-dark" id="prev_uploaded_docs">0 Files Attached</strong>
                                        <div class="text-muted" style="font-size:11px;" id="prev_docs_meta">Additional files can be uploaded later</div>
                                    </div>
                                    <div class="col-12 border-top pt-2">
                                        <span class="text-muted small">Site Location Details:</span> <br>
                                        <span class="text-dark" id="prev_location">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Team Allocation & Financial Ledger Review Row -->
                            <div class="row g-3 mb-4">
                                <div class="col-lg-7">
                                    <div class="card p-3 rounded-3 border bg-white h-100 shadow-xs">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <span class="fw-bold text-navy small"><i class="bi bi-people-fill text-primary me-1"></i>Project Handling Team</span>
                                            <span class="badge bg-light text-dark border" id="prev_handler_count_badge">0 Members</span>
                                        </div>
                                        <div id="prev_handlers_box">
                                            <span class="text-muted small fst-italic">No handlers assigned</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="card p-3 rounded-3 border bg-white h-100 shadow-xs">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-1 border-bottom">
                                            <span class="fw-bold text-navy small"><i class="bi bi-cash-stack text-success me-1"></i>Financial &amp; Billing Ledger</span>
                                            <span id="prev_pay_status"><span class="badge bg-secondary">Pending</span></span>
                                        </div>
                                        <div class="row g-2 text-center mt-1">
                                            <div class="col-4">
                                                <div class="p-2 border rounded bg-light">
                                                    <span class="text-muted d-block" style="font-size:0.7rem;">Product Value</span>
                                                    <b class="text-navy" style="font-size:0.85rem;" id="prev_pay_value">₹ 0.00</b>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 border rounded bg-light">
                                                    <span class="text-muted d-block" style="font-size:0.7rem;">Paid Amount</span>
                                                    <b class="text-success" style="font-size:0.85rem;" id="prev_pay_paid">₹ 0.00</b>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 border rounded bg-light">
                                                    <span class="text-muted d-block" style="font-size:0.7rem;">Pending Balance</span>
                                                    <b class="text-danger" style="font-size:0.85rem;" id="prev_pay_pending">₹ 0.00</b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info d-flex align-items-center gap-2 mb-0" role="alert">
                                <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                                <div class="small">
                                    Submitting will generate your unique Mining Plan dossier <strong>MP-{{ date('Y') }}-XXXX</strong> (Common Tracking ID: <strong>GTMS-{{ date('Y') }}-XXXX</strong>) and initialize the <strong>Stage 6.1 – 6.6 Process Flow</strong>.
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- NAVIGATION BUTTONS --}}
                        {{-- ========================================================== --}}
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-navy px-4" id="btnPrev" style="display:none;">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </button>
                            <div class="d-flex gap-2 ms-auto">
                                <button type="button" class="btn btn-navy px-4" id="btnNext">
                                    Continue<i class="bi bi-arrow-right ms-1"></i>
                                </button>
                                <button type="submit" class="btn btn-success px-4" id="btnSubmit" style="display:none;">
                                    <i class="bi bi-check2-circle me-1"></i>Submit Application
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <!-- ========================================== -->
                <!-- MODAL: ADD CUSTOM DOCUMENT (FOLDER-WISE)   -->
                <!-- ========================================== -->
                <div class="modal fade" id="modalAddCustomMiningDoc" tabindex="-1" aria-labelledby="modalAddCustomMiningDocLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header bg-navy text-white py-3">
                                <h5 class="modal-title fs-6 fw-bold" id="modalAddCustomMiningDocLabel">
                                    <i class="bi bi-plus-circle me-2 text-warning"></i>Add Document to Folder
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formAddCustomMiningDoc">
                                <div class="modal-body p-4">
                                    <!-- Selected Folder (Locked) -->
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-1">Target Statutory Folder</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="fa fa-folder text-primary"></i></span>
                                            <input type="text" class="form-control bg-light border-start-0 fw-semibold text-navy" id="mining_custom_folder_name" readonly value="">
                                        </div>
                                        <input type="hidden" id="mining_custom_folder_id" value="">
                                        <input type="hidden" id="mining_custom_now_id" value="">
                                    </div>

                                    <!-- Document Name -->
                                    <div class="mb-3">
                                        <label for="mining_custom_doc_name" class="form-label small fw-bold text-navy mb-1">
                                            Document Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="mining_custom_doc_name" placeholder="e.g. Property Tax Receipt, Local Panchayat NOC, Drone Survey Raw Log" required>
                                        <div class="form-text text-muted" style="font-size:11px;">Specify a clear statutory or project title for this document.</div>
                                    </div>

                                    <!-- Status (Mandatory / Optional) -->
                                    <div class="mb-3">
                                        <label for="mining_custom_doc_status" class="form-label small fw-bold text-navy mb-1">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select" id="mining_custom_doc_status" required>
                                            <option value="1">Mandatory (Required for Clearance)</option>
                                            <option value="0" selected>Optional (Supporting Document)</option>
                                        </select>
                                        <div class="form-text text-muted" style="font-size:11px;">Mark whether this document is mandatory or optional supporting proof.</div>
                                    </div>

                                    <!-- File Upload Option -->
                                    <div class="mb-2">
                                        <label for="mining_custom_doc_file" class="form-label small fw-bold text-navy mb-1">
                                            File Upload <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control" id="mining_custom_doc_file" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml,.dwg,.doc,.docx" required>
                                        <div class="form-text text-muted" style="font-size:11px;">Supports PDF, Images, KML, CAD (DWG), and Office docs up to 25 MB.</div>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light px-4 py-2 border-top">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-navy px-3" id="btn_confirm_add_mining_doc">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Attach &amp; Add Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </main>

        </div>
    </div>

    <!-- Pass folder document counts and customer data to JS -->
    <script>
        var folderCounts = @json($folderCounts);
        var preloadedCustomers = @json($customers);
    </script>

    <!-- jQuery for AJAX MIMAS lookup (already loaded in layout) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var currentStep = 0;
        var btnNext = document.getElementById('btnNext');
        var btnPrev = document.getElementById('btnPrev');
        var btnSubmit = document.getElementById('btnSubmit');

        // ==========================================
        // 1. UNIVERSAL MIMAS & CUSTOMER LOOKUP
        // ==========================================
        function populateCustomerFields(c) {
            if (!c) return;
            $('#field_customer_id').val(c.id);
            $('#field_mimas_no').val(c.mimas_no);
            $('#field_client_name').val(c.customer_name);
            $('#field_company_name').val(c.company_name);
            $('#field_mobile_num').val(c.mobile_num);
            $('#field_secondary_contact_person').val(c.secondary_contact_person || '');
            $('#field_secondary_mobile_num').val(c.secondary_mobile_num || '');
            $('#field_email').val(c.email || '');
            $('#field_pan').val(c.pan || '');
            $('#field_aadhaar_no').val(c.aadhaar_no || '');
            $('#field_gstin').val(c.gstin || '');
            $('#field_address').val(c.address || '');

            if (c.area) {
                $('#field_area_extent_ha').val(c.area);
            }
            if (c.district_id) {
                $('#district_id').val(c.district_id);
            }
            if (c.mineral_id) {
                var minCb = document.querySelector('input[name="mineral_ids[]"][value="' + c.mineral_id + '"]');
                if (minCb) minCb.checked = true;
            }

            $('#mimas_feedback_box').html(
                '<div class="alert alert-success py-2 px-3 mb-0 small">' +
                '<i class="fa fa-check-circle me-1"></i> Customer <strong>' + (c.company_name || c.customer_name) + '</strong> (' + (c.mimas_no || 'ID: '+c.id) + ') retrieved and autofilled!' +
                '</div>'
            ).slideDown();
        }

        function performMimasLookup() {
            var searchVal = $('#mimas_search_input').val().trim();
            if (!searchVal) {
                Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter or select a Customer Unique ID to lookup.', confirmButtonColor: '#0F1E4D' });
                return;
            }

            // Check if it matches a preloaded customer directly
            var found = preloadedCustomers.find(function(item) {
                return (item.mimas_no && item.mimas_no.toUpperCase() === searchVal.toUpperCase()) ||
                       (item.company_name && item.company_name.toUpperCase() === searchVal.toUpperCase()) ||
                       (item.customer_name && item.customer_name.toUpperCase() === searchVal.toUpperCase());
            });

            if (found) {
                populateCustomerFields(found);
                return;
            }

            // Otherwise fetch via AJAX endpoint
            $('#btn_lookup_mimas').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Searching...');
            $('#mimas_feedback_box').hide();

            $.ajax({
                url: '/customers/lookup-mimas/' + encodeURIComponent(searchVal),
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('#btn_lookup_mimas').prop('disabled', false).html('<i class="fa fa-sync-alt me-1"></i> Fetch Details');
                    if (res.status === 1 && res.data) {
                        populateCustomerFields(res.data);
                    } else {
                        $('#mimas_feedback_box').html(
                            '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-exclamation-circle me-1"></i> No matching registered customer found. You can enter details manually below.</div>'
                        ).slideDown();
                    }
                },
                error: function() {
                    $('#btn_lookup_mimas').prop('disabled', false).html('<i class="fa fa-sync-alt me-1"></i> Fetch Details');
                    $('#mimas_feedback_box').html(
                        '<div class="alert alert-info py-2 px-3 mb-0 small"><i class="fa fa-info-circle me-1"></i> Customer not found in registry. Fill in details manually to create a new customer record.</div>'
                    ).slideDown();
                }
            });
        }

        $('#btn_lookup_mimas').on('click', performMimasLookup);
        $('#mimas_search_input').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performMimasLookup();
            }
        });
        $('#mimas_search_input').on('change', function() {
            if ($(this).val().trim().length >= 3) {
                performMimasLookup();
            }
        });

        // Mobile numbers numeric restriction
        $('#field_mobile_num, #field_secondary_mobile_num').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
        });

        // Aadhaar auto-hyphenation mask
        $('#field_aadhaar_no').on('input', function() {
            var digits = $(this).val().replace(/\D/g, '').substring(0, 12);
            var parts = [];
            for (var i = 0; i < digits.length; i += 4) {
                parts.push(digits.substring(i, i + 4));
            }
            $(this).val(parts.join('-'));
        });

        // ==========================================
        // 2. WIZARD STEPPER NAVIGATION & BRANCHING
        // ==========================================
        function isMiningPlanSelected() {
            var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
            if (!selectedNow) return false;
            var name = selectedNow.getAttribute('data-name') || '';
            return name.trim().toLowerCase() === 'mining plan';
        }

        function updateStepperUI(step) {
            var isMP = isMiningPlanSelected();
            var stepNode2 = document.getElementById('stepNode_2');
            var connMineral = document.getElementById('conn_mineral');

            if (isMP) {
                stepNode2.style.display = 'block';
                connMineral.style.display = 'block';
                document.getElementById('circle_district').textContent = '4';
                document.getElementById('circle_folders').textContent = '5';
                document.getElementById('circle_upload').textContent = '6';
                document.getElementById('circle_handlers').textContent = '7';
                document.getElementById('circle_payment').textContent = '8';
                document.getElementById('circle_preview').textContent = '9';
                $('.lbl-step-num-handlers').text('7');
                $('.lbl-step-num-payment').text('8');
                $('.lbl-step-num-preview').text('9');
            } else {
                stepNode2.style.display = 'none';
                connMineral.style.display = 'none';
                document.getElementById('circle_district').textContent = '3';
                document.getElementById('circle_folders').textContent = '4';
                document.getElementById('circle_upload').textContent = '5';
                document.getElementById('circle_handlers').textContent = '6';
                document.getElementById('circle_payment').textContent = '7';
                document.getElementById('circle_preview').textContent = '8';
                $('.lbl-step-num-handlers').text('6');
                $('.lbl-step-num-payment').text('7');
                $('.lbl-step-num-preview').text('8');
            }

            for (var i = 0; i <= 8; i++) {
                var node = document.getElementById('stepNode_' + i);
                if (node) {
                    node.classList.remove('active', 'done');
                    if (i < step) {
                        node.classList.add('done');
                    } else if (i === step) {
                        node.classList.add('active');
                    }
                }
            }
        }

        // Toggle "Other Mineral" input box based on whether Others (id=8) is checked
        function toggleOtherMineralBox() {
            var othersCheckbox = document.getElementById('mineral_8');
            var otherBox = document.getElementById('other_mineral_box_mining');
            var otherInput = document.getElementById('field_other_mineral_name_mining');
            if (othersCheckbox && otherBox) {
                if (othersCheckbox.checked) {
                    otherBox.classList.remove('d-none');
                    if (otherInput) otherInput.setAttribute('required', 'required');
                } else {
                    otherBox.classList.add('d-none');
                    if (otherInput) { otherInput.removeAttribute('required'); otherInput.value = ''; }
                }
            }
        }

        // Wire mineral checkboxes to toggle Other Mineral box
        document.querySelectorAll('input[name="mineral_ids[]"]').forEach(function(cb) {
            cb.addEventListener('change', toggleOtherMineralBox);
        });
        // Initial check on page load
        toggleOtherMineralBox();

        function showStep(step) {
            for (var i = 0; i <= 8; i++) {
                var p = document.getElementById('pane_' + i);
                if (p) p.classList.add('d-none');
            }

            var activePane = document.getElementById('pane_' + step);
            if (activePane) activePane.classList.remove('d-none');

            updateStepperUI(step);

            btnPrev.style.display = step === 0 ? 'none' : 'inline-flex';
            btnNext.style.display = step === 8 ? 'none' : 'inline-flex';
            btnSubmit.style.display = step === 8 ? 'inline-flex' : 'none';
        }

        function validateCurrentStep(step) {
            if (step === 0) {
                var cname = document.getElementById('field_client_name').value.trim();
                var compname = document.getElementById('field_company_name').value.trim();
                var atid = document.getElementById('applicant_type_id').value;
                var mob = document.getElementById('field_mobile_num').value.trim();

                if (!cname) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter the Client / Representative Name.', confirmButtonColor: '#0F1E4D' }); return false; }
                if (!compname) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter the Company / Firm Name.', confirmButtonColor: '#0F1E4D' }); return false; }
                if (!atid) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select an Applicant Category.', confirmButtonColor: '#0F1E4D' }); return false; }
                if (!mob) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter a valid Mobile Number.', confirmButtonColor: '#0F1E4D' }); return false; }
            }
            if (step === 1) {
                var now = document.querySelector('input[name="nature_of_work_id"]:checked');
                if (!now) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select a Nature of Work.', confirmButtonColor: '#0F1E4D' }); return false; }
            }
            if (step === 2 && isMiningPlanSelected()) {
                var checkedMinerals = document.querySelectorAll('input[name="mineral_ids[]"]:checked');
                var pt = document.querySelector('input[name="plan_type_id"]:checked');
                if (!checkedMinerals || checkedMinerals.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select at least one Mineral for this Mining Plan.', confirmButtonColor: '#0F1E4D' });
                    return false;
                }
                if (!pt) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select Which Plan is Prepared.', confirmButtonColor: '#0F1E4D' });
                    return false;
                }
                var othersCb = document.getElementById('mineral_8');
                var otherNameInput = document.getElementById('field_other_mineral_name_mining');
                if (othersCb && othersCb.checked && (!otherNameInput || !otherNameInput.value.trim())) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please specify the Other Mineral Name.', confirmButtonColor: '#0F1E4D' });
                    if (otherNameInput) otherNameInput.focus();
                    return false;
                }
            }
            if (step === 3) {
                var did = document.getElementById('district_id').value;
                if (!did) { Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select a District.', confirmButtonColor: '#0F1E4D' }); return false; }
            }
            if (step === 6) {
                var handlerRows = $('#mining_handlers_tbody tr');
                var hasInvalidHandler = false;
                handlerRows.each(function() {
                    var n = $(this).find('.handler-name-input').val().trim();
                    var r = $(this).find('.handler-role-input').val().trim();
                    if ((n && !r) || (!n && r)) {
                        hasInvalidHandler = true;
                    }
                });
                if (hasInvalidHandler) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please provide both Name and Role for each assigned handling person.', confirmButtonColor: '#0F1E4D' });
                    return false;
                }
            }
            if (step === 7) {
                var pVal = parseFloat($('#mining_product_value').val()) || 0;
                var pPaid = parseFloat($('#mining_paid_amount').val()) || 0;
                if (pVal < 0 || pPaid < 0) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Product Value and Paid Amount cannot be negative.', confirmButtonColor: '#0F1E4D' });
                    return false;
                }
                if (pPaid > pVal && pVal > 0) {
                    Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Paid Amount cannot exceed Product Value.', confirmButtonColor: '#0F1E4D' });
                    return false;
                }
            }
            return true;
        }

        function populateFolderPreview() {
            var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
            if (!selectedNow) return;

            var nowId = selectedNow.value;
            var nowName = selectedNow.getAttribute('data-name') || 'Mining Plan';
            document.getElementById('disp_selected_now').textContent = nowName;

            var counts = folderCounts[nowId] || {};
            var flCount = counts['Field Log'] || 0;
            var docCount = counts['Documents'] || 0;
            var phCount = counts['Site Photos'] || 0;
            var repCount = counts['Report'] || 0;
            var planCount = counts['Plan'] || 0;
            var othCount = counts['Others'] || 0;

            document.getElementById('fc_fieldlog').textContent = flCount > 0 ? flCount + ' document types' : 'Standard Log Checklist';
            document.getElementById('fc_documents').textContent = docCount > 0 ? docCount + ' statutory document types' : 'Statutory Checklist';
            document.getElementById('fc_photos').textContent = phCount > 0 ? phCount + ' photo types' : 'Quarry Site Photographs';

            var cardReport = document.getElementById('card_report');
            var fcReport = document.getElementById('fc_report');
            if (repCount > 0) {
                cardReport.style.opacity = '1';
                fcReport.textContent = repCount + ' report items';
            } else {
                cardReport.style.opacity = '0.5';
                fcReport.textContent = 'Not required for this work';
            }

            var cardPlan = document.getElementById('card_plan');
            var fcPlan = document.getElementById('fc_plan');
            if (planCount > 0) {
                cardPlan.style.opacity = '1';
                fcPlan.textContent = planCount + ' plan drawings & calculation tables';
            } else {
                cardPlan.style.opacity = '0.5';
                fcPlan.textContent = 'Not required for this work';
            }

            var cardOthers = document.getElementById('card_others');
            var fcOthers = document.getElementById('fc_others');
            if (othCount > 0) {
                cardOthers.style.opacity = '1';
                fcOthers.textContent = othCount + ' data / annexure items';
            } else {
                cardOthers.style.opacity = '0.5';
                fcOthers.textContent = 'Optional / As applicable';
            }
        }

        // ==========================================
        // 3. STEP 6: DOCUMENT UPLOAD MANAGEMENT
        // ==========================================
        function formatFileSize(bytes) {
            if (!bytes || bytes === 0) return '0 Bytes';
            var k = 1024;
            var sizes = ['Bytes', 'KB', 'MB', 'GB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        }

        function getActiveNatureOfWorkId() {
            var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
            return selectedNow ? selectedNow.value : '1';
        }

        function populateDocumentChecklist() {
            var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
            var nowId = selectedNow ? selectedNow.value : '1';
            var nowName = selectedNow ? (selectedNow.getAttribute('data-name') || 'Mining Plan') : 'Mining Plan';

            document.getElementById('disp_upload_now').textContent = nowName;

            // Hide all groups and show active group
            document.querySelectorAll('.now-doc-group').forEach(function(el) {
                el.classList.add('d-none');
            });
            var activeGroup = document.getElementById('doc_group_now_' + nowId);
            if (activeGroup) {
                activeGroup.classList.remove('d-none');
            }

            updateUploadProgress();
        }

        function updateUploadProgress() {
            var nowId = getActiveNatureOfWorkId();
            var activeGroup = document.getElementById('doc_group_now_' + nowId);
            if (!activeGroup) return;

            var allInputs = activeGroup.querySelectorAll('.doc-file-input');
            var totalCount = allInputs.length;
            var uploadedCount = 0;

            allInputs.forEach(function(inp) {
                if ((inp.files && inp.files.length > 0) || inp.getAttribute('data-has-existing') === '1') {
                    uploadedCount++;
                }
            });

            var percent = totalCount > 0 ? Math.round((uploadedCount / totalCount) * 100) : 0;

            document.getElementById('doc_counter_text').textContent = uploadedCount + ' / ' + totalCount;
            document.getElementById('doc_percent_badge').textContent = percent + '%';
            document.getElementById('doc_progress_bar').style.width = percent + '%';

            // Also update per-folder pill counters
            activeGroup.querySelectorAll('.folder-doc-block').forEach(function(fBlock) {
                var fInputs = fBlock.querySelectorAll('.doc-file-input');
                var fTotal = fInputs.length;
                var fUp = 0;
                fInputs.forEach(function(fi) {
                    if ((fi.files && fi.files.length > 0) || fi.getAttribute('data-has-existing') === '1') fUp++;
                });
                var pill = fBlock.querySelector('.folder-pill-counter');
                if (pill) {
                    pill.textContent = fUp + ' / ' + fTotal + ' Attached';
                    if (fUp === fTotal && fTotal > 0) {
                        pill.className = 'badge bg-success text-white border small folder-pill-counter';
                    } else if (fUp > 0) {
                        pill.className = 'badge bg-primary text-white border small folder-pill-counter';
                    } else {
                        pill.className = 'badge bg-white text-secondary border small folder-pill-counter';
                    }
                }
                var totalCountSpan = fBlock.querySelector('.folder-total-count');
                if (totalCountSpan) {
                    totalCountSpan.textContent = fTotal;
                }
            });
        }

        // Single file upload trigger button
        $(document).on('click', '.btn-trigger-upload', function(e) {
            e.preventDefault();
            var fieldId = $(this).data('field-id');
            $('#file_field_' + fieldId).trigger('click');
        });

        // Individual file input change event
        $(document).on('change', '.doc-file-input', function() {
            var fieldId = $(this).data('field-id');
            var row = document.getElementById('row_field_' + fieldId);
            if (!row) return;

            if (this.files && this.files[0]) {
                var file = this.files[0];
                this.setAttribute('data-has-existing', '1');
                row.classList.add('up');
                var iconBox = row.querySelector('.ci-icon');
                if (iconBox) {
                    iconBox.style.background = '#dcfce7';
                    iconBox.style.color = '#15803d';
                    iconBox.innerHTML = '<i class="bi bi-check-lg" style="font-size:1.1rem;"></i>';
                }
                var meta = document.getElementById('meta_field_' + fieldId);
                if (meta) {
                    meta.innerHTML = '<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ' + file.name + '</span> &middot; ' + formatFileSize(file.size);
                }
                var badge = document.getElementById('badge_field_' + fieldId);
                if (badge) {
                    badge.className = 'badge bg-success text-white px-2 py-1';
                    badge.innerHTML = '<i class="bi bi-check2"></i> Uploaded';
                }
                var btn = row.querySelector('.btn-trigger-upload');
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-pencil me-1"></i>Change';
                    btn.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-trigger-upload';
                    var viewLink = row.querySelector('.btn-view-doc');
                    var blobUrl = URL.createObjectURL(file);
                    if (!viewLink) {
                        viewLink = document.createElement('a');
                        viewLink.className = 'btn btn-sm btn-outline-success py-1 px-2 btn-view-doc me-1';
                        viewLink.target = '_blank';
                        viewLink.rel = 'noopener noreferrer';
                        viewLink.title = 'View attached document in separate page';
                        viewLink.innerHTML = '<i class="bi bi-eye"></i>';
                        btn.parentNode.insertBefore(viewLink, btn);
                    }
                    viewLink.href = blobUrl;
                }
                var removeBtn = row.querySelector('.btn-remove-file');
                if (removeBtn) {
                    removeBtn.classList.remove('d-none');
                }
            }
            updateUploadProgress();
        });

        // Remove file handler
        $(document).on('click', '.btn-remove-file', function(e) {
            e.preventDefault();
            var fieldId = $(this).data('field-id');
            var row = document.getElementById('row_field_' + fieldId);
            var fileInput = document.getElementById('file_field_' + fieldId);

            if (fileInput) {
                fileInput.value = '';
                fileInput.setAttribute('data-has-existing', '0');
            }

            if (row) {
                var viewLink = row.querySelector('.btn-view-doc');
                if (viewLink) {
                    viewLink.remove();
                }
                row.classList.remove('up');
                var iconBox = row.querySelector('.ci-icon');
                if (iconBox) {
                    iconBox.style.background = '#f1f5f9';
                    iconBox.style.color = '#64748b';
                    iconBox.innerHTML = '<i class="bi bi-file-earmark ci-status-icon"></i>';
                }
                var meta = document.getElementById('meta_field_' + fieldId);
                if (meta) {
                    meta.textContent = 'Not uploaded · Max 25 MB';
                }
                var badge = document.getElementById('badge_field_' + fieldId);
                if (badge) {
                    var isMandatory = badge.getAttribute('class').indexOf('mandatory') !== -1;
                    badge.className = 'badge-status ' + (isMandatory ? 'mandatory' : 'pending');
                    badge.textContent = isMandatory ? 'Mandatory' : 'Optional';
                }
                var btn = row.querySelector('.btn-trigger-upload');
                if (btn) {
                    btn.innerHTML = '<i class="bi bi-upload me-1"></i>Upload';
                    btn.className = 'btn btn-sm btn-outline-navy py-1 px-2 btn-trigger-upload';
                }
                $(this).addClass('d-none');
            }
            updateUploadProgress();
        });

        // Interactive Dropzone & Batch Upload Matching
        $('#btn_batch_browse').on('click', function(e) {
            e.stopPropagation();
            $('#batch_file_input').trigger('click');
        });

        $('#wizard_dropzone').on('click', function() {
            $('#batch_file_input').trigger('click');
        });

        $('#wizard_dropzone').on('dragover', function(e) {
            e.preventDefault();
            $(this).css({ borderColor: '#2563eb', background: '#eff6ff' });
        });

        $('#wizard_dropzone').on('dragleave', function() {
            $(this).css({ borderColor: '#93c5fd', background: '#f0f7ff' });
        });

        $('#wizard_dropzone').on('drop', function(e) {
            e.preventDefault();
            $(this).css({ borderColor: '#93c5fd', background: '#f0f7ff' });
            var files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
                handleBatchFiles(files);
            }
        });

        $('#batch_file_input').on('change', function() {
            if (this.files && this.files.length > 0) {
                handleBatchFiles(this.files);
            }
        });

        function handleBatchFiles(files) {
            var nowId = getActiveNatureOfWorkId();
            var activeGroup = document.getElementById('doc_group_now_' + nowId);
            if (!activeGroup) return;

            var inputs = Array.from(activeGroup.querySelectorAll('.doc-file-input'));

            Array.from(files).forEach(function(file) {
                var cleanFileName = file.name.toLowerCase().replace(/[^a-z0-9]/g, '');

                // First find direct name match
                var targetInput = inputs.find(function(inp) {
                    var fieldName = (inp.getAttribute('data-field-name') || '').toLowerCase().replace(/[^a-z0-9]/g, '');
                    return fieldName && (cleanFileName.indexOf(fieldName) !== -1 || fieldName.indexOf(cleanFileName) !== -1);
                });

                // If not matched, pick first empty input
                if (!targetInput) {
                    targetInput = inputs.find(function(inp) {
                        return !inp.files || inp.files.length === 0;
                    });
                }

                if (targetInput) {
                    var dt = new DataTransfer();
                    dt.items.add(file);
                    targetInput.files = dt.files;
                    $(targetInput).trigger('change');
                }
            });
        }

        // ==========================================
        // 3.B. CUSTOM DOCUMENT MODAL & HANDLERS
        // ==========================================
        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Open Add Document Modal
        $(document).on('click', '.btn-open-mining-add-doc', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var folderId = $(this).data('folder-id');
            var folderName = $(this).data('folder-name');
            var nowId = $(this).data('now-id');

            $('#mining_custom_folder_id').val(folderId);
            $('#mining_custom_now_id').val(nowId);
            $('#mining_custom_folder_name').val(folderName);
            $('#mining_custom_doc_name').val('');
            $('#mining_custom_doc_status').val('0'); // default optional
            $('#mining_custom_doc_file').val('');

            var modalEl = document.getElementById('modalAddCustomMiningDoc');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        // Submit Add Document Modal
        $('#formAddCustomMiningDoc').on('submit', function(e) {
            e.preventDefault();
            var folderId = $('#mining_custom_folder_id').val();
            var folderName = $('#mining_custom_folder_name').val();
            var nowId = $('#mining_custom_now_id').val();
            var docName = $('#mining_custom_doc_name').val().trim();
            var isMandatory = $('#mining_custom_doc_status').val() === '1';
            var fileInput = document.getElementById('mining_custom_doc_file');

            if (!docName) {
                Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter a Document Name.', confirmButtonColor: '#0F1E4D' });
                return;
            }
            if (!fileInput.files || !fileInput.files[0]) {
                Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please select a file to attach.', confirmButtonColor: '#0F1E4D' });
                return;
            }

            var file = fileInput.files[0];
            var customId = 'c_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            var dispSize = formatFileSize(file.size);

            var container = document.getElementById('folder_checklist_' + nowId + '_' + folderId);
            if (!container) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Folder checklist container not found.', confirmButtonColor: '#0F1E4D' });
                return;
            }

            var rowHtml = '<div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom up" id="row_custom_' + customId + '" data-custom-id="' + customId + '">' +
                '<div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">' +
                    '<div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:32px; height:32px; background:#dcfce7; color:#16a34a;">' +
                        '<i class="bi bi-check-lg ci-status-icon"></i>' +
                    '</div>' +
                    '<div class="text-truncate">' +
                        '<div class="ci-name fw-semibold small text-dark text-truncate">' +
                            '<span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>' +
                            escapeHtml(docName) +
                        '</div>' +
                        '<div class="ci-meta text-muted" style="font-size:11px;" id="meta_custom_' + customId + '">' +
                            '<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ' + escapeHtml(file.name) + '</span> &middot; ' + dispSize +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0">' +
                    '<span class="badge-status ' + (isMandatory ? 'mandatory' : 'pending') + '">' +
                        (isMandatory ? 'Mandatory' : 'Optional') +
                    '</span>' +
                    '<span class="badge-status uploaded">Uploaded</span>' +
                    '<a href="' + URL.createObjectURL(file) + '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-success py-1 px-2 btn-view-doc" title="View attached document in separate page">' +
                        '<i class="bi bi-eye"></i>' +
                    '</a>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-custom-row" data-custom-id="' + customId + '" title="Remove custom document">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                    '<input type="hidden" name="custom_docs[' + customId + '][folder_id]" value="' + folderId + '">' +
                    '<input type="hidden" name="custom_docs[' + customId + '][name]" value="' + escapeHtml(docName) + '">' +
                    '<input type="hidden" name="custom_docs[' + customId + '][required]" value="' + (isMandatory ? '1' : '0') + '">' +
                    '<input type="file" name="custom_doc_files[' + customId + ']" id="file_custom_' + customId + '" class="doc-file-input d-none" data-custom-id="' + customId + '">' +
                '</div>' +
            '</div>';

            $(container).append(rowHtml);

            // Transfer the file into the new file input via DataTransfer
            var dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('file_custom_' + customId).files = dt.files;

            // Close modal
            var modalEl = document.getElementById('modalAddCustomMiningDoc');
            var modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // Update counters and progress
            updateUploadProgress();
            if (typeof toastr !== 'undefined') {
                toastr.success('Document "' + docName + '" attached to ' + folderName);
            }
        });

        // Delete custom document row
        $(document).on('click', '.btn-remove-custom-row', function(e) {
            e.preventDefault();
            var customId = $(this).data('custom-id');
            $('#row_custom_' + customId).remove();
            updateUploadProgress();
        });

        // ==========================================
        // 4. STEP 7: APPLICATION SUMMARY & SUBMISSION
        // ==========================================
        function populateSummary() {
            var comp = document.getElementById('field_company_name').value || '';
            var client = document.getElementById('field_client_name').value || '';
            var mob = document.getElementById('field_mobile_num').value || '';
            var secMob = document.getElementById('field_secondary_mobile_num').value || '';
            var secPerson = document.getElementById('field_secondary_contact_person').value || '';
            var pan = document.getElementById('field_pan').value || '';
            var ds = document.getElementById('district_id');
            var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
            var isMP = isMiningPlanSelected();

            document.getElementById('prev_client').textContent = comp + (client ? ' (' + client + ')' : '');
            var contactInfo = (mob ? 'Mob: ' + mob : '');
            if (secMob) {
                contactInfo += (contactInfo ? ' | ' : '') + 'Alt: ' + secMob + (secPerson ? ' (' + secPerson + ')' : '');
            }
            if (pan) {
                contactInfo += (contactInfo ? ' · ' : '') + 'PAN: ' + pan;
            }
            document.getElementById('prev_contact').textContent = contactInfo || '-';
            var mimasNo = document.getElementById('field_mimas_no').value || '';
            document.getElementById('prev_mimas_val').textContent = mimasNo || 'Not specified';
            document.getElementById('prev_district').textContent = ds.options[ds.selectedIndex] ? ds.options[ds.selectedIndex].text : '-';
            document.getElementById('prev_nature').textContent = selectedNow ? (selectedNow.getAttribute('data-name') || selectedNow.value) : '-';

            var area = document.getElementById('field_area_extent_ha').value;
            document.getElementById('prev_extent').textContent = area ? area + ' Hectares' : 'Extent pending';

            var minBox = document.getElementById('prev_mineral_box');
            var planBox = document.getElementById('prev_plan_box');

            if (isMP) {
                minBox.style.display = 'block';
                planBox.style.display = 'block';
                var otherNameVal = document.getElementById('field_other_mineral_name_mining')?.value?.trim() || '';
                var checkedMinerals = Array.from(document.querySelectorAll('input[name="mineral_ids[]"]:checked'))
                    .map(function(el) {
                        var mName = el.getAttribute('data-name') || el.value;
                        if (el.value == '8' && otherNameVal) {
                            return 'Other: ' + otherNameVal;
                        }
                        return mName;
                    });
                var pt = document.querySelector('input[name="plan_type_id"]:checked');
                document.getElementById('prev_mineral').textContent = checkedMinerals.length > 0 ? checkedMinerals.join(', ') : 'None selected';
                document.getElementById('prev_plan').textContent = pt ? (pt.getAttribute('data-name') || pt.value) : '-';
            } else {
                minBox.style.display = 'none';
                planBox.style.display = 'none';
            }

            var taluk = document.getElementById('taluk').value || '';
            var village = document.getElementById('village').value || '';
            var sf = document.getElementById('survey_numbers_text').value || '';
            var loc = [];
            if (village) loc.push('Village: ' + village);
            if (taluk) loc.push('Taluk: ' + taluk);
            if (sf) loc.push('Survey No: ' + sf);
            document.getElementById('prev_location').textContent = loc.length > 0 ? loc.join(' | ') : 'General site location';

            // Document upload summary count
            var nowId = getActiveNatureOfWorkId();
            var activeGroup = document.getElementById('doc_group_now_' + nowId);
            var attachedCount = 0;
            var totalDocs = 0;
            if (activeGroup) {
                var allInputs = activeGroup.querySelectorAll('.doc-file-input');
                totalDocs = allInputs.length;
                allInputs.forEach(function(inp) {
                    if ((inp.files && inp.files.length > 0) || inp.getAttribute('data-has-existing') === '1') attachedCount++;
                });
            }
            document.getElementById('prev_uploaded_docs').textContent = attachedCount + ' of ' + totalDocs + ' Files Attached';
            document.getElementById('prev_docs_meta').textContent = (totalDocs - attachedCount) + ' files can be uploaded later in dossier';

            // Handlers preview
            var handlerRows = $('#mining_handlers_tbody tr');
            var validHandlers = [];
            handlerRows.each(function() {
                var hName = $(this).find('.handler-name-input').val().trim();
                var hRole = $(this).find('.handler-role-input').val().trim();
                var hNotes = $(this).find('input[name*="[notes]"]').val().trim();
                if (hName) {
                    validHandlers.push({ name: hName, role: hRole, notes: hNotes });
                }
            });

            $('#prev_handler_count_badge').text(validHandlers.length + ' Members');
            if (validHandlers.length > 0) {
                var hHtml = '<table class="table table-sm table-borderless align-middle mb-0" style="font-size:0.83rem;">' +
                    '<thead class="text-muted border-bottom" style="font-size:0.75rem;">' +
                    '<tr><th style="width:30px;">#</th><th>Name</th><th>Role</th><th>Notes</th></tr>' +
                    '</thead><tbody>';
                validHandlers.forEach(function(h, idx) {
                    hHtml += '<tr>' +
                        '<td class="text-muted fw-bold">' + (idx + 1) + '</td>' +
                        '<td class="fw-semibold text-navy"><i class="bi bi-person-badge text-primary me-1"></i> ' + escapeHtml(h.name) + '</td>' +
                        '<td><span class="badge bg-secondary-subtle text-secondary border py-1 px-2">' + escapeHtml(h.role || 'Personnel') + '</span></td>' +
                        '<td class="text-muted small">' + escapeHtml(h.notes || '—') + '</td>' +
                    '</tr>';
                });
                hHtml += '</tbody></table>';
                $('#prev_handlers_box').html(hHtml);
            } else {
                $('#prev_handlers_box').html('<span class="text-muted small fst-italic py-2"><i class="bi bi-info-circle me-1"></i> No project handling personnel assigned.</span>');
            }

            // Payment preview
            var pVal = parseFloat($('#mining_product_value').val()) || 0;
            var pPaid = parseFloat($('#mining_paid_amount').val()) || 0;
            var pPending = Math.max(0, pVal - pPaid);
            var pStat = $('#mining_payment_status').val() || 'pending';

            $('#prev_pay_value').text('₹ ' + pVal.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#prev_pay_paid').text('₹ ' + pPaid.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#prev_pay_pending').text('₹ ' + pPending.toLocaleString('en-IN', { minimumFractionDigits: 2 }));

            var pBadge = '';
            if (pStat === 'paid') {
                pBadge = '<span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟢 Paid</span>';
            } else if (pStat === 'partial') {
                pBadge = '<span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟡 Partial</span>';
            } else {
                pBadge = '<span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🔴 Pending</span>';
            }
            $('#prev_pay_status').html(pBadge);
        }

        // ==========================================
        // 5. STEP 7 & 8: DYNAMIC HANDLERS & REAL-TIME PAYMENT CALCULATION
        // ==========================================
        var miningHandlerIndex = {{ (!empty($prefillData['handlers']) && count($prefillData['handlers']) > 0) ? count($prefillData['handlers']) : 0 }};

        function addMiningHandlerRow(name, role, notes) {
            name = name || '';
            role = role || '';
            notes = notes || '';
            var idx = miningHandlerIndex++;
            var row = '<tr id="handler_row_' + idx + '">' +
                '<td class="text-center text-muted fw-semibold handler-idx-col">1</td>' +
                '<td><input type="text" name="handlers[' + idx + '][name]" class="form-control form-control-sm handler-name-input" placeholder="e.g. Ramesh Kumar" value="' + escapeHtml(name) + '" required></td>' +
                '<td><input type="text" name="handlers[' + idx + '][role]" class="form-control form-control-sm handler-role-input" placeholder="e.g. Surveyor / Engineer" value="' + escapeHtml(role) + '" required></td>' +
                '<td><input type="text" name="handlers[' + idx + '][notes]" class="form-control form-control-sm" placeholder="e.g. Field inspection & DGPS logs" value="' + escapeHtml(notes) + '"></td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-mining-handler" data-idx="' + idx + '" title="Remove Person">' +
                        '<i class="bi bi-trash"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>';
            $('#mining_handlers_tbody').append(row);
            renumberMiningHandlers();
        }

        function renumberMiningHandlers() {
            $('#mining_handlers_tbody tr').each(function(i, tr) {
                $(tr).find('.handler-idx-col').text(i + 1);
            });
        }

        $('#btn_add_mining_handler').on('click', function() {
            addMiningHandlerRow();
        });

        $(document).on('click', '.btn-remove-mining-handler', function() {
            $(this).closest('tr').remove();
            renumberMiningHandlers();
        });

        // Initialize with 1 empty row if no prefilled handlers
        if ($('#mining_handlers_tbody tr').length === 0) {
            addMiningHandlerRow();
        }

        function calcMiningPayment() {
            var val = parseFloat($('#mining_product_value').val()) || 0;
            var paid = parseFloat($('#mining_paid_amount').val()) || 0;
            var pending = Math.max(0, val - paid);

            $('#mining_pending_amount').val(pending.toFixed(2));
            $('#disp_mining_product_val').text('₹ ' + val.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#disp_mining_paid_val').text('₹ ' + paid.toLocaleString('en-IN', { minimumFractionDigits: 2 }));
            $('#disp_mining_pending_val').text('₹ ' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2 }));

            var statusSelect = $('#mining_payment_status');
            if (val > 0) {
                if (paid >= val) {
                    statusSelect.val('paid');
                } else if (paid > 0) {
                    statusSelect.val('partial');
                } else {
                    statusSelect.val('pending');
                }
            }

            var currentStatus = statusSelect.val();
            var badgeHtml = '';
            if (currentStatus === 'paid') {
                badgeHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">🟢 Paid (Settled)</span>';
            } else if (currentStatus === 'partial') {
                badgeHtml = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">🟡 Partial Payment</span>';
            } else {
                badgeHtml = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">🔴 Pending Full Due</span>';
            }
            $('#disp_mining_status_badge').html(badgeHtml);
        }

        $('#mining_product_value, #mining_paid_amount').on('input change', calcMiningPayment);
        $('#mining_payment_status').on('change', function() {
            var val = $(this).val();
            var badgeHtml = val === 'paid' ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">🟢 Paid (Settled)</span>' :
                           (val === 'partial' ? '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">🟡 Partial Payment</span>' :
                                                '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">🔴 Pending Full Due</span>');
            $('#disp_mining_status_badge').html(badgeHtml);
        });

        // Trigger initial calculation
        calcMiningPayment();

        btnNext.addEventListener('click', function() {
            if (!validateCurrentStep(currentStep)) return;

            var isMP = isMiningPlanSelected();

            if (currentStep === 1) {
                currentStep = isMP ? 2 : 3;
            } else if (currentStep === 2) {
                currentStep = 3;
            } else if (currentStep === 3) {
                currentStep = 4;
                populateFolderPreview();
            } else if (currentStep === 4) {
                currentStep = 5;
                populateDocumentChecklist();
            } else if (currentStep === 5) {
                currentStep = 6;
            } else if (currentStep === 6) {
                currentStep = 7;
                calcMiningPayment();
            } else if (currentStep === 7) {
                currentStep = 8;
                populateSummary();
            } else if (currentStep < 8) {
                currentStep++;
            }

            showStep(currentStep);
        });

        btnPrev.addEventListener('click', function() {
            var isMP = isMiningPlanSelected();

            if (currentStep === 3) {
                currentStep = isMP ? 2 : 1;
            } else if (currentStep > 0) {
                currentStep--;
            }

            showStep(currentStep);
        });

        showStep(0);
    });
    </script>

@endsection
