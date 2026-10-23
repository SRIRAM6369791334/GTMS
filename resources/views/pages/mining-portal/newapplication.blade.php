@extends('layouts.app')
@section('title', 'New Mining Application')
@section('main_content')

<div class="content-body default-height">
    <div class="container-fluid">
        <div id="gtms-mining-page">
            <div class="wizard-wrap">

                <!-- ========================================================== -->
                <!-- EXECUTIVE COCKPIT HEADER                                    -->
                <!-- ========================================================== -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <div class="mining-eyebrow text-uppercase fw-bold mb-0.5" style="font-size: 0.74rem; letter-spacing: 0.05em; color: var(--orange, #ea580c) !important;">
                            <i class="bi bi-signpost-split me-1"></i> Mining Portal &bull; Statutory Intake Wizard
                        </div>
                        <h3 class="mining-title fw-bold mb-0" style="font-size: 1.25rem; color: #0F1E4D;">
                            <i class="bi bi-journal-plus text-primary me-2"></i>Register New Mining Application
                        </h3>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge mining-status-pill" style="font-size: 0.72rem; padding: 5px 12px; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 9999px; font-weight: 600;">
                            <i class="bi bi-shield-check me-1 text-primary"></i>Statutory Compliance Intake
                        </span>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <ul class="mb-0 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- ========================================================== -->
                <!-- 9-STEP PROGRESS BAR (EXECUTIVE DESIGN SYSTEM PARITY)       -->
                <!-- ========================================================== -->
                <div class="step-progress-wrapper mb-4">
                    <div class="step-progress" id="wizardStepper">
                        <div class="sp-step active" id="stepNode_0">
                            <div class="circ">
                                <span class="step-num" id="circle_0">1</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Client Info</div>
                        </div>
                        <div class="sp-step" id="stepNode_1">
                            <div class="circ">
                                <span class="step-num" id="circle_1">2</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Nature of Work</div>
                        </div>
                        <div class="flow-connector" id="conn_mineral" style="display:none;"></div>
                        <div class="sp-step" id="stepNode_2">
                            <div class="circ">
                                <span class="step-num" id="circle_2">3</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Minerals &amp; Plan</div>
                        </div>
                        <div class="sp-step" id="stepNode_3">
                            <div class="circ">
                                <span class="step-num" id="circle_district">4</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">District</div>
                        </div>
                        <div class="sp-step" id="stepNode_4">
                            <div class="circ">
                                <span class="step-num" id="circle_folders">5</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Folders</div>
                        </div>
                        <div class="sp-step" id="stepNode_5">
                            <div class="circ">
                                <span class="step-num" id="circle_upload">6</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Upload Docs</div>
                        </div>
                        <div class="sp-step" id="stepNode_6">
                            <div class="circ">
                                <span class="step-num" id="circle_handlers">7</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Handling Team</div>
                        </div>
                        <div class="sp-step" id="stepNode_7">
                            <div class="circ">
                                <span class="step-num" id="circle_payment">8</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Payment</div>
                        </div>
                        <div class="sp-step" id="stepNode_8">
                            <div class="circ">
                                <span class="step-num" id="circle_preview">9</span>
                                <i class="bi bi-check-lg step-check d-none"></i>
                            </div>
                            <div class="sp-label">Review</div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================== -->
                <!-- RESUMED / LEASE IMPORT BANNER                              -->
                <!-- ========================================================== -->
                @if(!empty($prefillData['mining_app_id']) || !empty($prefillData['lease_application_id']))
                    <div class="mining-banner-card mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="mining-banner-icon-badge">
                                    <i class="bi bi-arrow-repeat fa-spin"></i>
                                </div>
                                <div>
                                    <div class="mining-banner-title fw-bold">
                                        @if(!empty($prefillData['application_no']))
                                            Resuming Application: {{ $prefillData['application_no'] }}
                                        @else
                                            Imported from Approved Lease Application
                                        @endif
                                        @if(!empty($prefillData['common_id']))
                                            <span class="badge bg-light text-dark border ms-1 font-monospace">{{ $prefillData['common_id'] }}</span>
                                        @endif
                                    </div>
                                    <div class="mining-banner-desc">
                                        Client profile, district jurisdiction, and survey extents from Lease Application have been auto-populated. Complete the required Mining fields below to finalize your plan.
                                    </div>
                                </div>
                            </div>
                            <span class="badge mining-badge-success-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Data Auto-Fetched
                            </span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('newapplication.store') }}" id="miningWizardForm" enctype="multipart/form-data">
                    @csrf

                    <div class="card mining-main-card wizard-card">

                        {{-- ========================================================== --}}
                        {{-- STEP 1: CLIENT INFORMATION (UNIVERSAL MIMAS LOOKUP + PROFILE) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane" id="pane_0">
                            <!-- HEADER BANNER -->
                            <div class="s1-header-block mb-3.5">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="wc-eyebrow s1-eyebrow">
                                        <i class="bi bi-shield-check me-1"></i> Step 1 of 9 &bull; Applicant Identity &amp; Profile
                                    </div>
                                    <span class="s1-time-tag">
                                        <i class="bi bi-clock-history me-1"></i> Est. ~2 mins
                                    </span>
                                </div>
                                <h4 class="s1-title">Applicant &amp; Enterprise Details</h4>
                                <div class="wc-sub s1-subtitle">
                                    Lookup an existing registered customer via Customer Unique ID or enter the applicant identity details below.
                                </div>
                            </div>

                            <!-- CUSTOMER UNIQUE ID / MIMAS LOOKUP CARD -->
                            <div class="s1-lookup-card mb-3.5 p-3">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <label class="s1-lookup-label fw-bold mb-0" for="mimas_search_input">
                                        <i class="bi bi-fingerprint me-1.5"></i> Customer Unique ID Lookup
                                    </label>
                                    <span class="badge s1-lookup-badge">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> Instant Autofill
                                    </span>
                                </div>
                                <p class="s1-lookup-desc mt-1.5 mb-2.5">
                                    Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.
                                </p>

                                <div class="input-group s1-input-group s1-lookup-input-group">
                                    <span class="input-group-text s1-addon s1-lookup-addon"><i class="bi bi-search"></i></span>
                                    <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold s1-input s1-lookup-field" 
                                           placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off"
                                           value="{{ $prefillData['mimas_no'] ?? '' }}">
                                    <button class="btn s1-btn-fetch px-3.5 fw-bold" type="button" id="btn_lookup_mimas">
                                        <i class="bi bi-arrow-repeat me-1.5"></i> Fetch Details
                                    </button>
                                </div>

                                <datalist id="mimas_datalist">
                                    @if(isset($customers))
                                        @foreach($customers as $c)
                                            <option value="{{ $c->mimas_no }}">{{ $c->company_name }} ({{ $c->customer_name }})</option>
                                        @endforeach
                                    @endif
                                </datalist>

                                <div id="mimas_feedback_box" class="mt-2.5" style="display:none;"></div>
                            </div>

                            <!-- HIDDEN RESUME, LEASE & CUSTOMER FIELDS -->
                            <input type="hidden" name="mining_app_id" id="field_mining_app_id" value="{{ $prefillData['mining_app_id'] ?? '' }}">
                            <input type="hidden" name="lease_application_id" id="field_lease_application_id" value="{{ $prefillData['lease_application_id'] ?? '' }}">
                            <input type="hidden" name="common_id" id="field_common_id" value="{{ $prefillData['common_id'] ?? '' }}">
                            <input type="hidden" name="customer_id" id="field_customer_id" value="{{ old('customer_id', $prefillData['customer_id'] ?? '') }}">
                            <input type="hidden" name="mimas_no" id="field_mimas_no" value="{{ old('mimas_no', $prefillData['mimas_no'] ?? '') }}">
                            <input type="hidden" name="current_step" id="field_current_step" value="{{ $prefillData['current_step'] ?? 1 }}">

                            <!-- SECTION 1: APPLICANT & ENTERPRISE PROFILE -->
                            <div class="s1-section-card mb-3.5">
                                <div class="s1-section-header">
                                    <i class="bi bi-person-badge text-primary me-2"></i>
                                    <span class="s1-section-title">Applicant &amp; Business Profile</span>
                                </div>

                                <div class="row gx-3 gy-3 s1-form-row">
                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="field_client_name">
                                            Client / Representative Name <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field" id="field_client_name" name="client_name" 
                                                   placeholder="e.g. R. Kumaresan" value="{{ old('client_name', $prefillData['client_name'] ?? '') }}" required>
                                        </div>
                                        <div class="form-text-hint s1-hint">As on official government ID proof.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="field_company_name">
                                            Company / Firm / Quarry Name <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-building"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field" id="field_company_name" name="company_name" 
                                                   placeholder="e.g. Sri Bala Traders" value="{{ old('company_name', $prefillData['company_name'] ?? '') }}" required>
                                        </div>
                                        <div class="form-text-hint s1-hint">Registered business or concession title.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="applicant_type_id">
                                            Applicant Category <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-person-vcard"></i></span>
                                            <select name="applicant_type_id" id="applicant_type_id" class="form-select s1-input auto-filled-field" required>
                                                <option value="">-- Select Category --</option>
                                                @foreach($applicantTypes as $type)
                                                    <option value="{{ $type->id }}" {{ old('applicant_type_id', $prefillData['applicant_type_id'] ?? '') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-text-hint s1-hint">Individual, Partnership, Pvt Ltd, etc.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: CONTACT & SITE REPRESENTATIVES -->
                            <div class="s1-section-card mb-3.5">
                                <div class="s1-section-header">
                                    <i class="bi bi-telephone-inbound text-info me-2"></i>
                                    <span class="s1-section-title">Contact &amp; Site Representatives</span>
                                </div>

                                <div class="row gx-3 gy-3 s1-form-row">
                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="field_mobile_num">
                                            Primary Phone Number <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field" id="field_mobile_num" name="mobile_num" 
                                                   placeholder="10-digit primary phone number" value="{{ old('mobile_num', $prefillData['mobile_num'] ?? '') }}" required>
                                        </div>
                                        <div class="form-text-hint s1-hint">Primary mobile for statutory SMS alerts and official notices.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="field_email">
                                            Email Address&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-envelope"></i></span>
                                            <input type="email" class="form-control s1-input auto-filled-field" id="field_email" name="email" 
                                                   placeholder="customer@example.com" value="{{ old('email', $prefillData['email'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Receives e-copies of proceedings and fee receipts.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="field_secondary_contact_person">
                                            Secondary Contact Person&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-person-gear"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field" id="field_secondary_contact_person" name="secondary_contact_person" 
                                                   placeholder="e.g. Site Supervisor / Manager" value="{{ old('secondary_contact_person', $prefillData['secondary_contact_person'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Site supervisor or operational representative.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="field_secondary_mobile_num">
                                            Secondary Phone Number&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-phone"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field" id="field_secondary_mobile_num" name="secondary_mobile_num" 
                                                   placeholder="10-digit secondary phone number" value="{{ old('secondary_mobile_num', $prefillData['secondary_mobile_num'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Alternate contact number for quarry site.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 3: STATUTORY IDENTIFIERS & REGISTERED ADDRESS -->
                            <div class="s1-section-card mb-3.5">
                                <div class="s1-section-header">
                                    <i class="bi bi-shield-check text-success me-2"></i>
                                    <span class="s1-section-title">Statutory Identifiers &amp; Registered Office Address</span>
                                </div>

                                <div class="row gx-3 gy-3 s1-form-row">
                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="field_pan">
                                            PAN Number <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-credit-card-2-front"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field text-uppercase font-monospace fw-bold" id="field_pan" name="pan" 
                                                   placeholder="AAACS1234F" maxlength="10" value="{{ old('pan', $prefillData['pan'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">10-character alphanumeric Income Tax PAN.</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="field_aadhaar_no">
                                            Aadhaar Number&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-person-bounding-box"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field font-monospace" id="field_aadhaar_no" name="aadhaar_no" 
                                                   placeholder="XXXX-XXXX-XXXX" maxlength="14" value="{{ old('aadhaar_no', $prefillData['aadhaar_no'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">12-digit UIDAI number (automatically formatted).</div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label s1-label" for="field_gstin">
                                            GSTIN&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-receipt"></i></span>
                                            <input type="text" class="form-control s1-input auto-filled-field text-uppercase font-monospace" id="field_gstin" name="gstin" 
                                                   placeholder="33AAACS1234F1Z5" maxlength="15" value="{{ old('gstin', $prefillData['gstin'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Goods &amp; Services Tax Identification Number.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label s1-label" for="field_address">
                                            <i class="bi bi-geo-alt text-muted me-1"></i> Registered Office Address&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <textarea class="form-control s1-input s1-textarea auto-filled-field" id="field_address" name="address" rows="3" 
                                                  placeholder="Street, City, Taluk, Village, Pincode...">{{ old('address', $prefillData['address'] ?? '') }}</textarea>
                                        <div class="form-text-hint s1-hint">Official postal address registered with the Department of Geology and Mining.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 2: NATURE OF WORK (8 CHOICES)                         --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_1">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                                <div>
                                    <div class="s1-eyebrow text-uppercase fw-bold text-primary mb-1">
                                        <i class="bi bi-diagram-3 me-1"></i> STEP 2 OF 9 &bull; STATUTORY WORK CLASSIFICATION
                                    </div>
                                    <h4 class="fw-bold text-navy mt-1 mb-0">Select Nature of Work Scope</h4>
                                    <p class="text-muted small mb-0 mt-1">The statutory software workflow, folder checklists, and required regulatory filings dynamically adapt based on this domain selection.</p>
                                </div>
                                <span class="s1-time-tag">
                                    <i class="bi bi-clock-history me-1"></i> Est. ~1 min
                                </span>
                            </div>
                            
                            <div class="row g-3">
                                @foreach($natureOfWorks as $now)
                                    <div class="col-6 col-md-3">
                                        <input type="radio" class="btn-check nature-work-radio" name="nature_of_work_id"
                                               id="now_{{ $now->id }}" value="{{ $now->id }}"
                                               data-name="{{ $now->name }}"
                                                {{ (old('nature_of_work_id', $prefillData['nature_of_work_id'] ?? '') == $now->id || (empty(old('nature_of_work_id')) && empty($prefillData['nature_of_work_id']) && $loop->first)) ? 'checked' : '' }} required>
                                        <label class="nature-card w-100 text-start p-3 h-100 d-flex flex-column justify-content-between" for="now_{{ $now->id }}">
                                            <div>
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="badge nature-code-badge">
                                                        @if($loop->index == 0) 2.1 @elseif($loop->index == 1) 2.2 @elseif($loop->index == 2) 2.3 @elseif($loop->index == 3) 2.4 @elseif($loop->index == 4) 2.5 @elseif($loop->index == 5) 2.6 @elseif($loop->index == 6) 2.7 @else 2.8 @endif
                                                    </span>
                                                    <span class="nature-check-circle"><i class="bi bi-check-lg"></i></span>
                                                </div>
                                                <div class="fw-bold nature-card-title mb-1">{{ $now->name }}</div>
                                            </div>
                                            <div class="nature-card-desc">
                                                @if(strcasecmp($now->name, 'Mining Plan') == 0)
                                                    Full 6-folder statutory mining plan &amp; scheme
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
                                                    Custom domain processing &amp; filing
                                                @endif
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $selectedNowId = old('nature_of_work_id', $prefillData['nature_of_work_id'] ?? '');
                                $othersNowObj = $natureOfWorks->first(function($item) {
                                    return strtolower(trim($item->name)) === 'others';
                                });
                                $othersNowId = $othersNowObj ? $othersNowObj->id : 8;
                                $isOtherNowSelected = ($selectedNowId == $othersNowId) || !empty(old('other_nature_of_work_name', $prefillData['other_nature_of_work_name'] ?? ''));
                                $otherNowVal = old('other_nature_of_work_name', $prefillData['other_nature_of_work_name'] ?? '');
                            @endphp

                            {{-- Conditional "Other Nature of Work Name" input (appears when Others is checked) --}}
                            <div class="mt-3 {{ $isOtherNowSelected ? '' : 'd-none' }}" id="other_nature_of_work_box_mining">
                                <div class="p-3 rounded-3 border border-warning-subtle" style="background:#fffbeb;">
                                    <label class="form-label fw-bold text-dark small text-uppercase mb-1" for="field_other_nature_of_work_name_mining">
                                        <i class="bi bi-pencil-square text-warning me-1"></i>Specify Nature of Work Scope <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="other_nature_of_work_name" id="field_other_nature_of_work_name_mining" 
                                           class="form-control mining-input" placeholder="e.g. Processing Plant, Mineral Exploration, Quarry Reclamation..." 
                                           maxlength="255" value="{{ $otherNowVal }}">
                                    <div class="form-text text-muted small mt-1">
                                        <i class="bi bi-info-circle me-1"></i>Since you selected &quot;Others&quot;, please specify the custom operational or regulatory scope.
                                    </div>
                                </div>
                            </div>

                            <div class="mining-callout-info mt-4 p-3 rounded-3 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                                <span class="small text-navy">
                                    <strong>Workflow Dynamic Rule:</strong> Selecting <strong>2.1 Mining Plan</strong> activates Mineral and Plan Type configurations (Step 3). Other work types route directly to District Selection (Step 4).
                                </span>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 3: MINERALS & PLAN TYPE (BRANCH: ONLY FOR MINING PLAN) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_2">
                            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom flex-wrap gap-2">
                                <div>
                                    <div class="s1-eyebrow text-uppercase fw-bold text-primary mb-1">
                                        <i class="bi bi-gem me-1"></i> STEP 3 OF 9 &bull; MINERAL CONCESSION &amp; SCHEME
                                    </div>
                                    <h4 class="fw-bold text-navy mt-1 mb-0">2.1 Minerals &amp; 2.1.1 Plan Type</h4>
                                    <p class="text-muted small mb-0 mt-1">This sub-configuration applies exclusively to <strong>Mining Plan</strong> domain applications.</p>
                                </div>
                                <span class="s1-time-tag">
                                    <i class="bi bi-clock-history me-1"></i> Est. ~1 min
                                </span>
                            </div>

                            <!-- SECTION 1: MINERALS SELECTION -->
                            <div class="s3-section-card mb-4">
                                <div class="s3-section-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-gem-fill text-primary me-2 fs-6"></i>
                                        <span class="s3-section-title fw-bold text-navy">2.1 Select Statutory Mineral(s) <span class="text-danger">*</span></span>
                                    </div>
                                    <span class="badge s3-rule-badge">
                                        <i class="bi bi-check2-all me-1"></i>Multi-Select Enabled
                                    </span>
                                </div>

                                <div class="row g-3" id="minerals_selection_grid">
                                    @php
                                        $oldMineralIds = old('mineral_ids', $prefillData['mineral_ids'] ?? []);
                                        if (!is_array($oldMineralIds)) {
                                            $oldMineralIds = $oldMineralIds ? [$oldMineralIds] : [];
                                        }
                                    @endphp
                                    @foreach($minerals as $mineral)
                                        <div class="col-6 col-lg-3 col-md-4">
                                            <input type="checkbox" class="btn-check mineral-checkbox" name="mineral_ids[]"
                                                   id="mineral_{{ $mineral->id }}" value="{{ $mineral->id }}"
                                                   data-name="{{ $mineral->name }}"
                                                   {{ (in_array($mineral->id, $oldMineralIds) || (empty($oldMineralIds) && empty($prefillData['mineral_ids']) && $loop->first)) ? 'checked' : '' }}>
                                            <label class="mineral-card w-100 p-2.5 d-flex align-items-center justify-content-between" for="mineral_{{ $mineral->id }}">
                                                <div class="d-flex align-items-center gap-2 text-truncate">
                                                    <div class="mineral-icon-wrap">
                                                        <i class="bi bi-gem mineral-icon"></i>
                                                    </div>
                                                    <span class="fw-bold mineral-title text-truncate">{{ $mineral->name }}</span>
                                                </div>
                                                <div class="mineral-check-badge">
                                                    <i class="bi bi-check-lg"></i>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Conditional "Other Mineral Name" input (appears when Others id=8 is checked) --}}
                                <div class="mt-3 d-none" id="other_mineral_box_mining">
                                    <div class="p-3 rounded-3 border border-warning-subtle" style="background:#fffbeb;">
                                        <label class="form-label fw-bold text-dark small text-uppercase mb-1" for="field_other_mineral_name_mining">
                                            <i class="bi bi-pencil-square text-warning me-1"></i>Specify Other Mineral Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="other_mineral_name" id="field_other_mineral_name_mining" class="form-control mining-input" placeholder="e.g. Quartz, Feldspar, Laterite..." maxlength="255" value="{{ old('other_mineral_name', $prefillData['other_mineral_name'] ?? '') }}">
                                        <div class="form-text text-muted small mt-1"><i class="bi bi-info-circle me-1"></i>Since you selected &quot;Others&quot;, please specify the official mineral nomenclature.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: PLAN TYPE -->
                            <div class="s3-section-card mb-3">
                                <div class="s3-section-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-file-earmark-ruled-fill text-success me-2 fs-6"></i>
                                        <span class="s3-section-title fw-bold text-navy">2.1.1 Which Plan is Prepared? <span class="text-danger">*</span></span>
                                    </div>
                                    <span class="badge s3-rule-badge" style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;">
                                        <i class="bi bi-journal-check me-1"></i>Statutory Scheme
                                    </span>
                                </div>

                                <div class="row g-3">
                                    @foreach($planTypes as $pt)
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check plan-type-radio" name="plan_type_id" id="pt_{{ $pt->id }}" value="{{ $pt->id }}"
                                                   data-name="{{ $pt->name }}"
                                                   {{ (old('plan_type_id', $prefillData['plan_type_id'] ?? '') == $pt->id || (empty(old('plan_type_id')) && empty($prefillData['plan_type_id']) && $loop->first)) ? 'checked' : '' }}>
                                            <label class="plan-card w-100 p-3 h-100 d-flex align-items-start gap-3" for="pt_{{ $pt->id }}">
                                                <div class="plan-radio-circle mt-1"><i class="bi bi-circle-fill"></i></div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold plan-title mb-1">{{ $pt->name }}</div>
                                                    <div class="plan-desc">
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
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 4: DISTRICT & LOCATION SELECTION                      --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_3">
                            <!-- HEADER BANNER -->
                            <div class="s1-header-block mb-3.5">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="wc-eyebrow s1-eyebrow">
                                        <i class="bi bi-geo-alt me-1"></i> Step 4 of 9 &bull; Territorial Jurisdiction &amp; Location Details
                                    </div>
                                    <span class="s1-time-tag">
                                        <i class="bi bi-clock-history me-1"></i> Est. ~1.5 mins
                                    </span>
                                </div>
                                <h4 class="s1-title">District Administration &amp; Site Demarcation</h4>
                                <div class="wc-sub s1-subtitle">
                                    Assign the designated Mines Department office, administrative taluk, revenue village, and cadastral survey numbers.
                                </div>
                            </div>

                            <!-- SECTION 1: DISTRICT JURISDICTION & EXTENT SPECIFICATIONS -->
                            <div class="s1-section-card mb-3.5">
                                <div class="s1-section-header">
                                    <i class="bi bi-bank text-primary me-2"></i>
                                    <span class="s1-section-title">District Administration &amp; Concession Extent</span>
                                </div>

                                <div class="row gx-3 gy-3 s1-form-row">
                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="district_id">
                                            District Jurisdiction <span class="text-danger ms-1">*</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-geo-alt-fill text-primary"></i></span>
                                            <select name="district_id" id="district_id" class="form-select s1-input auto-filled-field" required>
                                                <option value="">-- Select District Mining Office --</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}" {{ old('district_id', $prefillData['district_id'] ?? '') == $district->id ? 'selected' : '' }}>
                                                        {{ $district->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-text-hint s1-hint">Assigns the Assistant Director / Mines Department jurisdiction.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="field_area_extent_ha">
                                            Area Extent (in Hectares / Patta)&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-bounding-box-circles text-info"></i></span>
                                            <input type="text" name="area_extent_ha" id="field_area_extent_ha" class="form-control s1-input auto-filled-field font-monospace fw-bold" 
                                                   placeholder="e.g. 2.45 or 14.20.20" value="{{ old('area_extent_ha', $prefillData['area_extent_ha'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Quarry concession total area in standard decimal (e.g. 2.4500 Ha) or Revenue Patta format (e.g. 14.20.20).</div>
                                    </div>
                                </div>
                            </div>

                            <!-- SECTION 2: REVENUE LOCATION & CADASTRAL PARCELS -->
                            <div class="s1-section-card mb-3.5">
                                <div class="s1-section-header">
                                    <i class="bi bi-map-fill text-success me-2"></i>
                                    <span class="s1-section-title">Revenue Administration &amp; Cadastral Parcels</span>
                                </div>

                                <div class="row gx-3 gy-3 s1-form-row">
                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="taluk">
                                            Revenue Taluk&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-signpost-2 text-secondary"></i></span>
                                            <input type="text" name="taluk" id="taluk" class="form-control s1-input auto-filled-field" 
                                                   placeholder="e.g. Mettur / Salem South" value="{{ old('taluk', $prefillData['taluk'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Revenue taluk of the quarry site.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label s1-label" for="village">
                                            Revenue Village&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-houses text-secondary"></i></span>
                                            <input type="text" name="village" id="village" class="form-control s1-input auto-filled-field" 
                                                   placeholder="e.g. Mallur Village" value="{{ old('village', $prefillData['village'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Revenue village boundary where the quarry is situated.</div>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label s1-label" for="survey_numbers_text">
                                            Cadastral Survey Number(s)&nbsp;<span class="s1-opt-tag">(Optional)</span>
                                        </label>
                                        <div class="input-group s1-input-group">
                                            <span class="input-group-text s1-addon"><i class="bi bi-pin-map-fill text-danger"></i></span>
                                            <input type="text" name="survey_numbers_text" id="survey_numbers_text" class="form-control s1-input auto-filled-field font-monospace" 
                                                   placeholder="e.g. 112/3A, 112/3B, 115/1" value="{{ old('survey_numbers_text', $prefillData['survey_numbers_text'] ?? '') }}">
                                        </div>
                                        <div class="form-text-hint s1-hint">Comma-separated survey numbers as per revenue records.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- STATUTORY ROUTING NOTICE -->
                            <div class="s1-notice-card mt-3 mb-0 p-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="s1-notice-icon-box">
                                            <i class="bi bi-shield-check s1-notice-icon"></i>
                                        </div>
                                        <div class="s1-notice-text">
                                            <strong>Statutory Routing Notice:</strong> This territorial jurisdiction routes the application to the designated 
                                            @php
                                                $activeDistName = null;
                                                if (!empty($districts)) {
                                                    $foundDist = $districts->firstWhere('id', old('district_id', $prefillData['district_id'] ?? ''));
                                                    if ($foundDist) $activeDistName = $foundDist->name;
                                                }
                                            @endphp
                                            <span id="s4_district_preview_badge" class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold mx-1">{{ $activeDistName ?? 'Salem' }}</span> 
                                            Mines Officer and initializes the regional statutory repository.
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fw-semibold">
                                        <i class="bi bi-check2-circle me-1"></i> Verified Routing
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 5: FOLDER STRUCTURE PREVIEW                           --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_4">
                            <!-- HEADER BANNER -->
                            <div class="s1-header-block mb-3.5">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="wc-eyebrow s1-eyebrow">
                                        <i class="bi bi-folder-check me-1"></i> Step 5 of 9 &bull; Regulatory Document Repository
                                    </div>
                                    <span class="s1-time-tag">
                                        <i class="bi bi-clock-history me-1"></i> Est. ~1 min overview
                                    </span>
                                </div>
                                <h4 class="s1-title">Statutory Folder Architecture</h4>
                                <div class="wc-sub s1-subtitle">
                                    Based on your selection of <strong class="text-navy fw-bold" id="disp_selected_now">Mining Plan</strong>, the statutory documentation checklists are partitioned below into distinct regulatory folders.
                                </div>
                            </div>

                            <!-- OVERALL PROGRESS SUMMARY BANNER (LEASE STEP 4 PARITY) -->
                            <div class="s4-summary-banner mb-4 p-3.5">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="s4-kpi-icon-box bg-primary-subtle text-primary me-2">
                                                <i class="bi bi-folder2-open fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="text-muted small" style="font-size:0.73rem;">Statutory Folders</div>
                                                <div class="fw-bold text-navy" style="font-size:0.95rem;"><span id="kpi_folder_count">6</span> Active Dossiers</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="s4-kpi-icon-box bg-info-subtle text-info me-2">
                                                <i class="bi bi-files fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="text-muted small" style="font-size:0.73rem;">Total Checklist Items</div>
                                                <div class="fw-bold text-navy" style="font-size:0.95rem;"><span id="mining_dossier_total_count">0</span> Required Filings</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <div class="text-muted small" style="font-size:0.73rem;">Dossier Readiness</div>
                                            <div class="fw-bold text-primary s4-summary-pct" id="mining_dossier_pct" style="font-size:0.86rem;">0% Complete</div>
                                        </div>
                                        <div class="progress s4-summary-progress">
                                            <div class="progress-bar s4-summary-bar" id="mining_dossier_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-end text-muted mt-1" style="font-size:0.70rem;">
                                            <span id="mining_dossier_uploaded_count">0</span> of <span id="kpi_total_docs">0</span> files attached
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3" id="folderPreviewCards">
                                <!-- Folder 1: Field Log Data -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_f1" data-folder="7" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-blue">
                                                    <i class="bi bi-journal-text"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 01</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">1. Field Log Data</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_fieldlog" style="font-size:0.77rem; line-height:1.45;">Standard Log Checklist</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">DGPS Survey</span>
                                                <span class="s4-chip">Boundary Coordinates</span>
                                                <span class="s4-chip">Field Notes</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f1_up_count">0</strong> / <span id="f1_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-blue" id="f1_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f1_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="7">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Folder 2: Documents -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_f2" data-folder="8" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-purple">
                                                    <i class="bi bi-folder2-open"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 02</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">2. Documents</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_documents" style="font-size:0.77rem; line-height:1.45;">Statutory Checklist</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">Patta &amp; Chitta</span>
                                                <span class="s4-chip">Land Deeds</span>
                                                <span class="s4-chip">A-Register</span>
                                                <span class="s4-chip">GO Order</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f2_up_count">0</strong> / <span id="f2_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-purple" id="f2_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f2_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="8">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Folder 3: Site Photos -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_f3" data-folder="9" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-cyan">
                                                    <i class="bi bi-camera-fill"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 03</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">3. Site Photos</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_photos" style="font-size:0.77rem; line-height:1.45;">Quarry Site Photographs</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">Quarry Face</span>
                                                <span class="s4-chip">Approach Road</span>
                                                <span class="s4-chip">Pillars</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f3_up_count">0</strong> / <span id="f3_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-cyan" id="f3_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f3_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="9">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Folder 4: Report -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_report" data-folder="10" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-amber">
                                                    <i class="bi bi-file-earmark-text"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 04</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">4. Report</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_report" style="font-size:0.77rem; line-height:1.45;">Loading...</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">Mining Scheme</span>
                                                <span class="s4-chip">Geology Report</span>
                                                <span class="s4-chip">Reserves</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f4_up_count">0</strong> / <span id="f4_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-amber" id="f4_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f4_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="10">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Folder 5: Plan -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_plan" data-folder="11" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-emerald">
                                                    <i class="bi bi-compass"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 05</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">5. Plan</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_plan" style="font-size:0.77rem; line-height:1.45;">Loading...</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">Surface Plan</span>
                                                <span class="s4-chip">Environment Plan</span>
                                                <span class="s4-chip">CAD Drawing</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f5_up_count">0</strong> / <span id="f5_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-emerald" id="f5_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f5_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="11">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Folder 6: Others -->
                                <div class="col-md-4 col-sm-6">
                                    <div class="s4-folder-card" id="card_others" data-folder="12" style="cursor: pointer;">
                                        <div>
                                            <div class="d-flex align-items-start justify-content-between mb-2.5">
                                                <div class="s4-icon-wrap s4-icon-rose">
                                                    <i class="bi bi-pie-chart"></i>
                                                </div>
                                                <span class="s4-folder-no">FOLDER 06</span>
                                            </div>
                                            <h6 class="fw-bold text-navy mb-1" style="font-size:0.95rem;">6. Others</h6>
                                            <div class="text-muted small mb-2.5 folder-meta" id="fc_others" style="font-size:0.77rem; line-height:1.45;">Loading...</div>
                                            <div class="s4-doc-chips mb-3">
                                                <span class="s4-chip">KML Polygon</span>
                                                <span class="s4-chip">Baseline Annexure</span>
                                                <span class="s4-chip">NOC Letters</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="s4-progress-wrap mb-2.5">
                                                <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                                                    <span class="s4-count-label">Files Uploaded</span>
                                                    <span class="s4-count-num"><strong id="f6_up_count">0</strong> / <span id="f6_tot_count">0</span></span>
                                                </div>
                                                <div class="progress s4-card-progress">
                                                    <div class="progress-bar s4-bar-rose" id="f6_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                            <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                                                <span class="badge s4-badge-pending" id="badge_f6_status"><i class="bi bi-circle me-1"></i>Pending</span>
                                                <span class="s4-action-text btn-goto-upload-step" data-target-folder="12">Open Folder &rarr;</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- REGULATORY NOTICE CARD (LEASE STEP 4 PARITY) -->
                            <div class="s4-notice-card">
                                <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap gap-3">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                        <div class="s4-notice-icon-wrap">
                                            <i class="bi bi-shield-check"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <span class="badge s4-rule-pill">Regulatory Dossier Partitioning</span>
                                                <h6 class="mb-0 fw-bold s4-notice-title">Automated Document Architecture</h6>
                                            </div>
                                            <p class="mb-0 text-muted s4-notice-desc">
                                                Files uploaded in Step 6 are automatically validated, tagged with statutory metadata, and securely archived into their designated regulatory folders.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center flex-shrink-0 ms-md-2">
                                        <button type="button" class="btn s4-btn-proceed btn-goto-upload-step" data-target-folder="7">
                                            Proceed to Uploads <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 6: DOCUMENT UPLOAD CHECKLIST (LEASE APPLICATION PATTERN) --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_5">
                            <!-- HEADER BANNER -->
                            <div class="s1-header-block mb-3.5">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
                                    <div class="wc-eyebrow s1-eyebrow">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> Step 6 of 9 &bull; Regulatory Repository &amp; Digital Plans
                                    </div>
                                    <span class="s1-time-tag">
                                        <i class="bi bi-clock-history me-1"></i> Est. ~5-8 mins
                                    </span>
                                </div>
                                <h4 class="s1-title">Statutory Document Checklist &amp; Digital Plans</h4>
                                <div class="wc-sub s1-subtitle">
                                    Upload statutory documents, affidavits, site photographs, and digital survey plans for <strong class="text-navy fw-bold" id="disp_upload_now">Mining Plan</strong> across the statutory folders.
                                </div>
                            </div>

                            <!-- OVERALL PROGRESS SUMMARY BANNER (LEASE STEP 5 PARITY) -->
                            <div class="s5-summary-banner mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="s5-summary-badge">
                                            <i class="bi bi-folder-check me-1"></i>OVERALL COMPLETION
                                        </span>
                                        <span class="s5-summary-label">Mandatory Dossier Checklist Progress</span>
                                    </div>
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span class="s5-counter-text text-primary" id="doc_counter_text">0 / 0</span>
                                        <span class="s5-counter-sub text-muted">files attached</span>
                                        <span class="badge bg-primary text-white ms-2 px-2.5 py-1 rounded-pill" id="doc_percent_badge">0%</span>
                                    </div>
                                </div>
                                <div class="progress s5-summary-progress">
                                    <div class="progress-bar s5-summary-bar" id="doc_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <!-- FOLDER CHECKLISTS GROUPED BY NATURE OF WORK -->
                            <div id="nature_doc_groups">
                                @if(isset($natureOfWorks) && isset($folders) && isset($documentFields))
                                    @foreach($natureOfWorks as $now)
                                        <div class="now-doc-group d-none" id="doc_group_now_{{ $now->id }}" data-now-id="{{ $now->id }}">
                                            @foreach($folders as $folder)
                                                @php
                                                    $fFields = $documentFields->where('nature_of_work_id', $now->id)->where('folder_id', $folder->id);
                                                @endphp
                                                @if($fFields->isNotEmpty())
                                                    <div class="folder-doc-block mb-4">
                                                        <div class="d-flex justify-content-between align-items-center mb-2.5 s5-folder-heading-wrap">
                                                            <div class="s5-folder-heading d-flex align-items-center gap-2.5">
                                                                <div class="s5-folder-icon-box">
                                                                    <i class="bi bi-folder2-open text-primary"></i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="s5-folder-title mb-0">{{ $loop->iteration }}. {{ $folder->name }}</h6>
                                                                    <div class="s5-folder-subtitle">Folder ID: #{{ $folder->id }} &middot; <span class="folder-total-count">{{ $fFields->count() }}</span> Checklist Items</div>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <button type="button" class="btn btn-sm s5-btn-add-doc btn-open-mining-add-doc"
                                                                        data-folder-id="{{ $folder->id }}"
                                                                        data-folder-name="{{ $folder->name }}"
                                                                        data-now-id="{{ $now->id }}">
                                                                    <i class="bi bi-plus-circle me-1"></i>Add Document
                                                                </button>
                                                                <span class="badge s5-folder-pill folder-pill-counter" id="pill_folder_{{ $now->id }}_{{ $folder->id }}">
                                                                    0 / {{ $fFields->count() }} Attached
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="folder-checklist-box" id="folder_checklist_{{ $now->id }}_{{ $folder->id }}">
                                                            @php
                                                                $customCarriedDocs = collect();
                                                                if (!empty($prefillData['existing_docs'])) {
                                                                    $customCarriedDocs = $prefillData['existing_docs']->filter(function($d) use ($folder) {
                                                                        return empty($d->document_field_id) && $d->folder_id == $folder->id && !empty($d->file_path);
                                                                    });
                                                                }
                                                            @endphp

                                                            @foreach($customCarriedDocs as $cDoc)
                                                                <div class="checklist-row up" id="row_carried_{{ $cDoc->id }}">
                                                                    <div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
                                                                        <div class="ci-icon ci-icon-carried">
                                                                            <i class="bi bi-check2-circle ci-status-icon"></i>
                                                                        </div>
                                                                        <div class="min-w-0">
                                                                            <div class="ci-name text-truncate">
                                                                                <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Lease Doc</span>
                                                                                {{ $cDoc->document_name }}
                                                                            </div>
                                                                            <div class="ci-meta text-success text-truncate">
                                                                                <i class="bi bi-link-45deg me-1"></i> Carried from Lease: {{ $cDoc->file_name ?? basename($cDoc->file_path) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">
                                                                        <span class="badge s5-badge-carried">
                                                                            <i class="bi bi-check-lg me-1"></i>Carried
                                                                        </span>
                                                                        <a href="{{ asset($cDoc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-success s5-btn-action" title="View attached document">
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
                                                                <div class="checklist-row {{ $hasDoc ? 'up' : '' }}" id="row_field_{{ $field->id }}" data-field-id="{{ $field->id }}" data-mandatory="{{ $field->required ? '1' : '0' }}">
                                                                    <div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
                                                                        <div class="ci-icon">
                                                                            <i class="bi {{ $hasDoc ? 'bi-check2-circle' : 'bi-file-earmark' }} ci-status-icon"></i>
                                                                        </div>
                                                                        <div class="min-w-0">
                                                                            <div class="ci-name text-truncate">{{ $loop->iteration }}. {{ $field->name }}</div>
                                                                            <div class="ci-meta text-truncate" id="meta_field_{{ $field->id }}">
                                                                                @if($hasDoc)
                                                                                    <span class="text-success fw-semibold"><i class="bi bi-link-45deg me-1"></i> Auto-Imported: {{ $matchedDoc->file_name ?? basename($matchedDoc->file_path) }}</span>
                                                                                @else
                                                                                    Not uploaded &middot; Max 25 MB
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">
                                                                        <select class="form-select form-select-sm doc-req-select {{ $field->required ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                                                                                name="doc_requirements[{{ $field->id }}]"
                                                                                data-field-id="{{ $field->id }}"
                                                                                title="Set requirement status (Mandatory or Optional)">
                                                                            <option value="mandatory" {{ $field->required ? 'selected' : '' }}>Mandatory</option>
                                                                            <option value="optional" {{ !$field->required ? 'selected' : '' }}>Optional</option>
                                                                        </select>

                                                                        @if($hasDoc)
                                                                            <span class="badge s5-badge-attached" id="badge_field_{{ $field->id }}">
                                                                                <i class="bi bi-check-lg me-1"></i>Attached
                                                                            </span>
                                                                            <a href="{{ asset($matchedDoc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info s5-btn-action btn-view-doc" title="View attached document">
                                                                                <i class="bi bi-eye"></i>
                                                                            </a>
                                                                            <button type="button" class="btn btn-sm btn-outline-secondary s5-btn-action btn-trigger-upload" data-field-id="{{ $field->id }}" title="Change file">
                                                                                <i class="bi bi-arrow-repeat"></i>
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-sm s5-btn-upload btn-trigger-upload" data-field-id="{{ $field->id }}">
                                                                                <i class="bi bi-upload me-1"></i> Upload
                                                                            </button>
                                                                        @endif
                                                                        <button type="button" class="btn btn-sm btn-outline-danger s5-btn-action btn-remove-file d-none" data-field-id="{{ $field->id }}" title="Remove attachment">
                                                                            <i class="bi bi-trash3"></i>
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

                            <!-- STATUTORY INTAKE INFORMATION NOTICE (LEASE STEP 5 PARITY) -->
                            <div class="s5-notice-card mt-4 mb-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="s5-notice-icon-wrap">
                                        <i class="bi bi-info-circle-fill"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-0.5">
                                            <span class="badge s5-rule-pill">Flexible Intake Standard</span>
                                            <h6 class="mb-0 fw-bold s5-notice-title">Post-Registration Upload Support</h6>
                                        </div>
                                        <p class="mb-0 text-muted s5-notice-desc">
                                            You can upload all regulatory documents now or complete pending statutory items post-registration inside the <strong>Project Folder Dossier</strong> at any time.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 7: PROJECT HANDLING PERSONS / MULTI-USER ALLOCATION   --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_6">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                                <div>
                                    <div class="s1-eyebrow text-uppercase fw-bold text-primary mb-1">
                                        <i class="bi bi-people-fill me-1"></i> STEP 7 OF 9 &bull; PROJECT EXECUTION TEAM
                                    </div>
                                    <h4 class="fw-bold text-navy mt-1 mb-0">Project Handling Team &amp; In-Charge Persons</h4>
                                    <p class="text-muted small mb-0 mt-1">Assign field officers, surveyors, documentation coordinators, and technical team members responsible for executing this mining application.</p>
                                </div>
                                <span class="badge s6-team-pill">
                                    <i class="bi bi-people-fill me-1.5"></i>Multi-User Allocation
                                </span>
                            </div>

                            <!-- SUMMARY / TEAM READINESS BANNER (LEASE STEP 6 PARITY) -->
                            <div class="s6-banner-card mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="s6-banner-icon-badge">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                        <div>
                                            <div class="s6-banner-title fw-bold">Statutory Personnel Assignment</div>
                                            <div class="s6-banner-desc text-muted small">Designate internal team members, empanelled RQP consultants, and field surveyors for regulatory liaison.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge s6-count-pill" id="mining_handler_count_banner">
                                            {{ (!empty($prefillData['handlers']) && count($prefillData['handlers']) > 0) ? count($prefillData['handlers']) : 0 }} Members Allocated
                                        </span>
                                        <button type="button" class="btn btn-navy btn-sm px-3" id="btn_add_mining_handler">
                                            <i class="bi bi-person-plus-fill me-1"></i>+ Add Person
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive rounded-3 border mb-3">
                                <table class="table table-hover align-middle mb-0 mining-table" id="mining_handlers_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th style="min-width: 220px;"><i class="bi bi-person me-1 text-primary"></i>Person / Executive Name <span class="text-danger">*</span></th>
                                            <th style="min-width: 200px;"><i class="bi bi-briefcase me-1 text-primary"></i>Role / Designation <span class="text-danger">*</span></th>
                                            <th style="min-width: 260px;"><i class="bi bi-card-text me-1 text-primary"></i>Notes / Responsibilities</th>
                                            <th style="width: 70px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="mining_handlers_tbody">
                                        @if(!empty($prefillData['handlers']) && count($prefillData['handlers']) > 0)
                                            @foreach($prefillData['handlers'] as $hIdx => $h)
                                                <tr id="handler_row_{{ $hIdx }}">
                                                    <td class="text-center text-muted fw-bold handler-idx-col">{{ $loop->iteration }}</td>
                                                    <td><input type="text" name="handlers[{{ $hIdx }}][name]" class="form-control form-control-sm mining-input handler-name-input" placeholder="e.g. Ramesh Kumar" value="{{ $h->name ?? '' }}" required></td>
                                                    <td><input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm mining-input handler-role-input" placeholder="e.g. Surveyor / Engineer" value="{{ $h->role ?? '' }}" required list="mining_role_suggestions"></td>
                                                    <td><input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm mining-input" placeholder="e.g. Field inspection & DGPS logs" value="{{ $h->notes ?? '' }}"></td>
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

                            <!-- Role Autocomplete Suggestions Datalist -->
                            <datalist id="mining_role_suggestions">
                                <option value="DGPS Surveyor"></option>
                                <option value="Mining Geologist"></option>
                                <option value="Documentation Coordinator"></option>
                                <option value="Revenue Liaison Officer"></option>
                                <option value="RQP Consultant (Mining Plan)"></option>
                                <option value="Environmental Consultant (EC)"></option>
                                <option value="Drone Survey Pilot"></option>
                                <option value="Field Inspection Officer"></option>
                                <option value="Legal &amp; Statutory Advisor"></option>
                                <option value="Accounts &amp; Billing In-Charge"></option>
                            </datalist>

                            <!-- PRO TIP CALLOUT BOX (LEASE STEP 6 PARITY) -->
                            <div class="s6-tip-box d-flex align-items-start gap-2.5 p-3 rounded-3 mt-3 mb-2">
                                <div class="s6-tip-icon flex-shrink-0">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </div>
                                <div class="s6-tip-content">
                                    <strong>Pro Tip:</strong> You can select a standard role from the suggestions or type any custom designation (e.g. <em>DGPS Surveyor</em>, <em>Mining Geologist</em>, <em>Revenue Liaison</em>, <em>RQP Consultant</em>). All allocated personnel are permanently archived with this application's audit dossier.
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 8: PAYMENT & FINANCIAL SETTLEMENT                     --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_7">
                            <div class="s1-header-block mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div class="s1-eyebrow text-uppercase fw-bold text-primary mb-1">
                                            <i class="bi bi-cash-stack me-1"></i> STEP 8 OF 9 &bull; FINANCIAL SETTLEMENT &amp; BILLING LEDGER
                                        </div>
                                        <h4 class="s1-title mb-1">Payment &amp; Financial Settlement</h4>
                                        <p class="s1-subtitle mb-0">Record the statutory service or product valuation, advance received, and compute the balance settlement in real-time.</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge s1-time-tag"><i class="bi bi-clock-history me-1"></i> Est. ~1-2 mins</span>
                                        <span class="badge s7-ledger-pill">
                                            <i class="bi bi-cash-stack me-1.5"></i>Billing Ledger
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @php
                                $curStatus = $prefillData['payment_status'] ?? 'pending';
                            @endphp

                            <!-- SUMMARY / LEDGER READINESS BANNER (LEASE STEP 7 PARITY) -->
                            <div class="s7-banner-card mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="s7-banner-icon-badge">
                                            <i class="bi bi-credit-card-2-front-fill"></i>
                                        </div>
                                        <div>
                                            <div class="s7-banner-title fw-bold">Statutory Ledger &amp; Fee Calculation</div>
                                            <div class="s7-banner-desc text-muted small">Real-time fee computation, balance due tracking, and transaction status recording for regulatory compliance.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge s7-banner-status-pill {{ $curStatus === 'paid' ? 'bg-success text-white' : ($curStatus === 'partial' ? 'bg-warning text-dark' : 'bg-danger-subtle text-danger border border-danger-subtle') }}" id="mining_banner_status_pill">
                                            @if($curStatus === 'paid')
                                                <i class="bi bi-check-circle-fill me-1.5"></i> Fully Paid
                                            @elseif($curStatus === 'partial')
                                                <i class="bi bi-clock-history me-1.5"></i> Part Payment
                                            @else
                                                <i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- 3-BENTO KPI CARDS (EXACT LEASE STEP 7 PARITY) -->
                            <div class="row g-3 mb-4">
                                <!-- Bento 1: Product Value -->
                                <div class="col-md-4">
                                    <div class="s7-kpi-card s7-kpi-contract">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s7-kpi-label">Product / Service Value</span>
                                            <div class="s7-kpi-icon-badge s7-kpi-icon-contract"><i class="bi bi-receipt-cutoff"></i></div>
                                        </div>
                                        <div class="s7-kpi-value text-navy" id="disp_mining_product_val">₹ {{ number_format((float)($prefillData['product_value'] ?? 0), 2) }}</div>
                                        <div class="s7-kpi-subtext">Total agreed statutory fee or quotation</div>
                                    </div>
                                </div>

                                <!-- Bento 2: Paid Amount -->
                                <div class="col-md-4">
                                    <div class="s7-kpi-card s7-kpi-paid">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s7-kpi-label text-success">Advance / Paid Amount</span>
                                            <div class="s7-kpi-icon-badge s7-kpi-icon-paid"><i class="bi bi-check-circle-fill"></i></div>
                                        </div>
                                        <div class="s7-kpi-value text-success" id="disp_mining_paid_val">₹ {{ number_format((float)($prefillData['paid_amount'] ?? 0), 2) }}</div>
                                        <div class="s7-kpi-subtext">Received advance via Bank / NEFT / Cash</div>
                                    </div>
                                </div>

                                <!-- Bento 3: Pending Balance -->
                                <div class="col-md-4">
                                    <div class="s7-kpi-card s7-kpi-pending">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s7-kpi-label text-danger">Pending Balance Due</span>
                                            <div class="s7-kpi-icon-badge s7-kpi-icon-pending"><i class="bi bi-hourglass-split"></i></div>
                                        </div>
                                        <div class="s7-kpi-value text-danger" id="disp_mining_pending_val">₹ {{ number_format((float)($prefillData['pending_amount'] ?? 0), 2) }}</div>
                                        <div class="s7-kpi-subtext">Remaining balance to be collected</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Breakdown Form Section (Lease Step 7 Parity) -->
                            <div class="s7-form-section mb-4">
                                <div class="s7-form-section-header d-flex align-items-center justify-content-between">
                                    <span class="s7-form-section-title">
                                        <i class="bi bi-currency-rupee me-1.5 text-primary"></i>Fee Entry &amp; Settlement Assessment
                                    </span>
                                    <span class="badge s7-badge-status-tag">Statutory Accounting</span>
                                </div>

                                <div class="s7-form-section-body">
                                    <div class="row g-4 mb-4">
                                        <!-- Product Value -->
                                        <div class="col-md-4">
                                            <label class="form-label s7-label mb-2">
                                                Product / Service Value (₹) <span class="text-danger">*</span>
                                            </label>
                                            <div class="s7-input-group">
                                                <span class="s7-input-addon s7-addon-navy">
                                                    <i class="bi bi-currency-rupee"></i>
                                                </span>
                                                <input type="number" step="0.01" min="0" class="form-control s7-input fw-bold text-navy" 
                                                       id="mining_product_value" name="product_value" 
                                                       value="{{ old('product_value', $prefillData['product_value'] ?? '0.00') }}" placeholder="0.00" required>
                                            </div>
                                            <div class="s7-field-hint mt-2">Total agreed fee for mining application.</div>
                                        </div>

                                        <!-- Paid Amount -->
                                        <div class="col-md-4">
                                            <label class="form-label s7-label mb-2">
                                                Paid Amount (₹) <span class="text-danger">*</span>
                                            </label>
                                            <div class="s7-input-group">
                                                <span class="s7-input-addon s7-addon-success">
                                                    <i class="bi bi-currency-rupee"></i>
                                                </span>
                                                <input type="number" step="0.01" min="0" class="form-control s7-input fw-bold text-success" 
                                                       id="mining_paid_amount" name="paid_amount" 
                                                       value="{{ old('paid_amount', $prefillData['paid_amount'] ?? '0.00') }}" placeholder="0.00" required>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-1">
                                                <span class="s7-field-hint">Advance paid by applicant.</span>
                                                <div class="s7-quick-presets d-flex align-items-center gap-1.5">
                                                    <button type="button" class="btn s7-preset-btn" id="btn_quick_full" title="Set paid equal to value">100% Full</button>
                                                    <button type="button" class="btn s7-preset-btn" id="btn_quick_half" title="Set paid to 50%">50% Advance</button>
                                                    <button type="button" class="btn s7-preset-btn" id="btn_quick_zero" title="Set paid to 0">Clear (₹0)</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pending Balance -->
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <label class="form-label s7-label mb-0">Pending Balance (₹)</label>
                                                <span class="badge s7-auto-tag">Auto-Calculated</span>
                                            </div>
                                            <div class="s7-input-group s7-group-readonly">
                                                <span class="s7-input-addon s7-addon-danger">
                                                    <i class="bi bi-currency-rupee"></i>
                                                </span>
                                                <input type="number" step="0.01" class="form-control s7-input s7-input-readonly fw-bold text-danger" 
                                                       id="mining_pending_amount" name="pending_amount" 
                                                       value="{{ old('pending_amount', $prefillData['pending_amount'] ?? '0.00') }}" readonly placeholder="0.00">
                                            </div>
                                            <div class="s7-field-hint mt-2">Auto-calculated: (Value &minus; Paid).</div>
                                        </div>
                                    </div>

                                    <div class="row g-4 align-items-center">
                                        <!-- Settlement Status -->
                                        <div class="col-md-6">
                                            <label class="form-label s7-label mb-2">
                                                Settlement Status <span class="text-danger">*</span>
                                            </label>
                                            <div class="s7-select-wrap">
                                                <select class="form-select s7-select fw-semibold" id="mining_payment_status" name="payment_status" required>
                                                    <option value="pending" {{ old('payment_status', $prefillData['payment_status'] ?? 'pending') == 'pending' ? 'selected' : '' }}>🔴 Pending (Full Balance Due)</option>
                                                    <option value="partial" {{ old('payment_status', $prefillData['payment_status'] ?? '') == 'partial' ? 'selected' : '' }}>🟡 Partial (Part Payment Received)</option>
                                                    <option value="paid" {{ old('payment_status', $prefillData['payment_status'] ?? '') == 'paid' ? 'selected' : '' }}>🟢 Paid (Fully Cleared)</option>
                                                </select>
                                            </div>
                                            <div class="s7-field-hint mt-2">Adapts dynamically based on amounts, or select manually.</div>
                                        </div>

                                        <!-- Live Settlement Assessment Feedback Box -->
                                        <div class="col-md-6">
                                            <div class="s7-assessment-wrapper">
                                                <div class="s7-feedback-box d-flex align-items-center justify-content-between" id="mining_status_feedback_box">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="s7-feedback-icon" id="mining_status_feedback_icon">
                                                            <i class="bi bi-clock-history"></i>
                                                        </div>
                                                        <div>
                                                            <div class="s7-feedback-sub">Settlement Assessment</div>
                                                            <div class="s7-feedback-title fw-bold" id="mining_status_feedback_text">Pending Payment</div>
                                                        </div>
                                                    </div>
                                                    <span class="badge s7-feedback-badge px-3 py-2 rounded-pill bg-danger text-white" id="mining_status_feedback_badge">
                                                        Pending
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Payment Notes / Transaction Reference -->
                                        <div class="col-12 mt-3">
                                            <label class="form-label s7-label mb-2">Payment Notes / Transaction Reference</label>
                                            <input type="text" class="form-control s7-notes-input" id="mining_payment_notes" name="payment_notes" 
                                                   placeholder="e.g. Advance paid via NEFT Ref: UTR291840, Cheque #10294" 
                                                   value="{{ old('payment_notes', '') }}">
                                            <div class="s7-field-hint mt-2">Optional instrument number, bank name, or receipt remarks.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PRO TIP CALLOUT BOX (LEASE STEP 7 PARITY) -->
                            <div class="s7-tip-box d-flex align-items-center gap-3 mb-2">
                                <div class="s7-tip-icon flex-shrink-0">
                                    <i class="bi bi-lightbulb-fill"></i>
                                </div>
                                <div class="s7-tip-content">
                                    <strong>Pro Tip:</strong> All financial entries recorded here are automatically reflected in <strong>Financial &amp; Billing Ledger</strong> of the official Executive Dossier and statutory reports. You can collect advance fees now and record progressive milestone payments later during scrutiny.
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- STEP 9: APPLICATION SUMMARY & SUBMISSION                   --}}
                        {{-- ========================================================== --}}
                        <div class="wizard-pane d-none" id="pane_8">
                            <div class="s1-header-block mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <div class="s1-eyebrow text-uppercase fw-bold text-primary mb-1">
                                            <i class="bi bi-clipboard2-check-fill me-1"></i> STEP 9 OF 9 &bull; DOSSIER SCRUTINY &amp; FINAL REVIEW
                                        </div>
                                        <h4 class="s1-title mb-1">
                                            <i class="bi bi-clipboard2-check-fill text-primary me-2"></i>Application Scrutiny &amp; Final Review
                                        </h4>
                                        <p class="s1-subtitle mb-0">Verify all applicant metadata, legal work scope, assigned team, uploaded documents, and billing ledger before final launch.</p>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge s1-time-tag"><i class="bi bi-clock-history me-1"></i> Est. ~2-3 mins</span>
                                        <span class="badge s8-scrutiny-pill">
                                            <i class="bi bi-shield-check me-1.5"></i>Pre-Submission Scrutiny
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- EXECUTIVE AUDIT & READINESS BANNER (LEASE STEP 8 PARITY) -->
                            <div class="s8-banner-card mb-4">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="s8-banner-icon-badge">
                                            <i class="bi bi-patch-check-fill"></i>
                                        </div>
                                        <div>
                                            <div class="s8-banner-title fw-bold">Statutory Dossier Audit &amp; Compliance Verification</div>
                                            <div class="s8-banner-desc text-muted small">Complete multi-step mining application dossier assembled. Inspect each section below for legal accuracy before generating the official application number.</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge s8-banner-status-pill">
                                            <i class="bi bi-shield-lock-fill me-1.5"></i>Ready for Final Submission
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- 4-BENTO EXECUTIVE METRIC OVERVIEW (EXACT LEASE STEP 8 PARITY) -->
                            <div class="row g-3 mb-4">
                                <!-- Bento 1: Applicant & Identity -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="s8-kpi-card s8-kpi-applicant">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s8-kpi-label">Applicant Entity</span>
                                            <div class="s8-kpi-icon-badge s8-kpi-icon-blue"><i class="bi bi-person-badge-fill"></i></div>
                                        </div>
                                        <div class="s8-kpi-val text-truncate" id="prev_client">-</div>
                                        <div class="s8-kpi-sub" id="prev_mimas_box">
                                            <span class="badge s8-chip-blue font-monospace">MIMAS: <span id="prev_mimas_val">-</span></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bento 2: Quarry Location & Mineral -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="s8-kpi-card s8-kpi-location">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s8-kpi-label">Jurisdiction &amp; Scope</span>
                                            <div class="s8-kpi-icon-badge s8-kpi-icon-emerald"><i class="bi bi-geo-alt-fill"></i></div>
                                        </div>
                                        <div class="s8-kpi-val text-truncate" id="prev_district">-</div>
                                        <div class="s8-kpi-sub">
                                            <span class="badge s8-chip-emerald" id="prev_extent">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bento 3: Statutory Uploads -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="s8-kpi-card s8-kpi-docs">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s8-kpi-label">Statutory Uploads</span>
                                            <div class="s8-kpi-icon-badge s8-kpi-icon-amber"><i class="bi bi-folder-check"></i></div>
                                        </div>
                                        <div class="s8-kpi-val" id="prev_uploaded_docs">0 Files Attached</div>
                                        <div class="s8-kpi-sub">
                                            <span class="badge s8-chip-amber text-truncate d-inline-block" style="max-width:100%;" id="prev_docs_meta">Ready for dossier initialization</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bento 4: Settlement & Ledger -->
                                <div class="col-xl-3 col-md-6">
                                    <div class="s8-kpi-card s8-kpi-finance">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="s8-kpi-label">Billing Ledger</span>
                                            <div class="s8-kpi-icon-badge s8-kpi-icon-purple"><i class="bi bi-cash-stack"></i></div>
                                        </div>
                                        <div class="s8-kpi-val text-success" id="prev_pay_paid">₹ 0.00</div>
                                        <div class="s8-kpi-sub d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Due: <b id="prev_pay_pending">₹ 0.00</b></span>
                                            <span id="prev_pay_status"><span class="badge bg-secondary rounded-pill px-2 py-0.5" style="font-size:10.5px;">Pending</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STRUCTURED SECTION PANELS (MATCHING LEASE STEP 8 WITH QUICK-JUMP EDIT BUTTONS) -->
                            <!-- 1. APPLICANT & FIRM IDENTITY -->
                            <div class="s8-section-panel mb-4">
                                <div class="s8-panel-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="s8-panel-header-icon s8-icon-blue"><i class="bi bi-person-badge-fill"></i></div>
                                        <h6 class="s8-panel-title mb-0">1. Applicant &amp; Entity Identity (Step 1)</h6>
                                        <span class="badge s8-panel-step-badge">Step 1</span>
                                    </div>
                                    <button type="button" class="btn btn-sm s8-btn-edit s9-btn-edit" data-target-step="0">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Step 1
                                    </button>
                                </div>
                                <div class="s8-panel-body">
                                    <div class="row g-3">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Client / Representative</span>
                                                <span class="s8-tile-value text-navy" id="disp_rev_client_name">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Firm / Enterprise Name</span>
                                                <span class="s8-tile-value text-navy" id="disp_rev_company_name">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Customer Unique ID (MIMAS)</span>
                                                <span class="s8-tile-value font-monospace text-primary" id="disp_rev_mimas">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Contact Phone &amp; Email</span>
                                                <span class="s8-tile-value" id="prev_contact">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Registered Office Address</span>
                                                <span class="s8-tile-value text-muted small" id="disp_rev_address">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. WORK NATURE & LOCATION SCOPE -->
                            <div class="s8-section-panel mb-4">
                                <div class="s8-panel-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="s8-panel-header-icon s8-icon-emerald"><i class="bi bi-compass"></i></div>
                                        <h6 class="s8-panel-title mb-0">2. Work Nature &amp; Location Scope (Steps 2 &ndash; 4)</h6>
                                        <span class="badge s8-panel-step-badge">Steps 2 &ndash; 4</span>
                                    </div>
                                    <button type="button" class="btn btn-sm s8-btn-edit s9-btn-edit" data-target-step="3">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Step 4
                                    </button>
                                </div>
                                <div class="s8-panel-body">
                                    <div class="row g-3">
                                        <div class="col-lg-3 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Nature of Work</span>
                                                <span class="s8-tile-value"><span class="badge bg-primary text-white" id="prev_nature">-</span></span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6" id="prev_mineral_box">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">2.1 Mineral(s)</span>
                                                <span class="s8-tile-value text-navy" id="prev_mineral">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6" id="prev_plan_box">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">2.1.1 Plan Type</span>
                                                <span class="s8-tile-value text-navy" id="prev_plan">-</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Folder Structure</span>
                                                <span class="s8-tile-value"><span class="badge s8-chip-emerald" id="prev_folder_badge">Folders Active</span></span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="s8-data-tile">
                                                <span class="s8-tile-label">Quarry Site Location &amp; Survey Number(s)</span>
                                                <span class="s8-tile-value fw-semibold text-navy" id="prev_location">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. TEAM ALLOCATION & FINANCIAL SUMMARY -->
                            <div class="row g-3 mb-4">
                                <div class="col-lg-7">
                                    <div class="s8-section-panel h-100">
                                        <div class="s8-panel-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="s8-panel-header-icon s8-icon-teal"><i class="bi bi-people-fill"></i></div>
                                                <h6 class="s8-panel-title mb-0">3. Assigned Handling Team (Step 7)</h6>
                                                <span class="badge s8-panel-step-badge">Step 7</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge s8-chip-blue" id="prev_handler_count_badge">0 Members</span>
                                                <button type="button" class="btn btn-sm s8-btn-edit s9-btn-edit" data-target-step="6">
                                                    <i class="bi bi-pencil-square me-1"></i>Edit Step 7
                                                </button>
                                            </div>
                                        </div>
                                        <div class="s8-panel-body" id="prev_handlers_box">
                                            <span class="text-muted small fst-italic">No handlers assigned</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <div class="s8-section-panel h-100">
                                        <div class="s8-panel-header d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="s8-panel-header-icon s8-icon-green"><i class="bi bi-cash-stack"></i></div>
                                                <h6 class="s8-panel-title mb-0">4. Financial Settlement (Step 8)</h6>
                                                <span class="badge s8-panel-step-badge">Step 8</span>
                                            </div>
                                            <button type="button" class="btn btn-sm s8-btn-edit s9-btn-edit" data-target-step="7">
                                                <i class="bi bi-pencil-square me-1"></i>Edit Step 8
                                            </button>
                                        </div>
                                        <div class="s8-panel-body">
                                            <div class="row g-2 text-center">
                                                <div class="col-4">
                                                    <div class="s8-ledger-card s8-ledger-contract">
                                                        <span class="s8-ledger-sub">Product Value</span>
                                                        <div class="s8-ledger-amount text-navy" id="prev_pay_value">₹ 0.00</div>
                                                        <span class="s8-ledger-hint text-muted">Total Fee</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="s8-ledger-card s8-ledger-paid">
                                                        <span class="s8-ledger-sub text-success">Paid Amount</span>
                                                        <div class="s8-ledger-amount text-success" id="prev_pay_paid_card">₹ 0.00</div>
                                                        <span class="s8-ledger-hint text-muted">Received</span>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="s8-ledger-card s8-ledger-pending">
                                                        <span class="s8-ledger-sub text-danger">Pending Due</span>
                                                        <div class="s8-ledger-amount text-danger" id="prev_pay_pending_card">₹ 0.00</div>
                                                        <span class="s8-ledger-hint text-muted">Balance</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. STATUTORY DOCUMENT FOLDERS & DETAILED FILE REGISTRY (LEASE STEP 8 PARITY) -->
                            <div class="s8-section-panel mb-4">
                                <div class="s8-panel-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="s8-panel-header-icon s8-icon-amber"><i class="bi bi-folder-check"></i></div>
                                        <h6 class="s8-panel-title mb-0">5. Statutory Document Folders (Steps 5 &amp; 6)</h6>
                                        <span class="badge s8-panel-step-badge">Steps 5 &amp; 6</span>
                                    </div>
                                    <button type="button" class="btn btn-sm s8-btn-edit s9-btn-edit" data-target-step="5">
                                        <i class="bi bi-pencil-square me-1"></i>Edit Step 6
                                    </button>
                                </div>
                                <div class="s8-panel-body">
                                    <div class="row g-2 mb-3" id="prev_folder_cards_grid">
                                        <!-- Dynamically generated folder count pills -->
                                    </div>

                                    <!-- Summary bar and toggle detailed registry -->
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-muted fw-semibold" style="font-size:13px;">Total Statutory Uploads:</span>
                                            <span class="fw-bold text-navy" style="font-size:13.5px;" id="prev_statutory_docs_count">0 Items Attached</span>
                                            <span class="badge bg-primary ms-1" id="prev_statutory_badge"><i class="bi bi-file-earmark-check me-1"></i> Ready</span>
                                        </div>
                                        <button type="button" class="btn btn-sm s8-btn-registry" data-bs-toggle="collapse" data-bs-target="#docsRegistryList" aria-expanded="false" aria-controls="docsRegistryList">
                                            <i class="bi bi-list-check me-1"></i>Toggle Detailed File Registry <i class="bi bi-chevron-down ms-1"></i>
                                        </button>
                                    </div>

                                    <!-- Collapsible detailed table -->
                                    <div class="collapse mt-3" id="docsRegistryList">
                                        <div class="table-responsive s8-registry-wrapper">
                                            <table class="table s8-table align-middle mb-0" id="prev_registry_table">
                                                <thead>
                                                    <tr>
                                                        <th style="width:45px;" class="text-center">#</th>
                                                        <th style="width:25%;">Target Folder</th>
                                                        <th style="width:45%;">Statutory Document Title</th>
                                                        <th style="width:30%;">Attachment Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prev_registry_tbody">
                                                    <!-- Dynamically populated in populateSummary() -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PRE-SUBMISSION COMPLIANCE CONFIRMATION BANNER (LEASE STEP 8 PARITY) -->
                            <div class="s8-declaration-card mb-4">
                                <div class="d-flex gap-3">
                                    <div class="s8-declaration-icon-badge">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="s8-declaration-title fw-bold mb-1">Pre-Submission Statutory Declaration &amp; Compliance Acknowledgment</div>
                                        <div class="s8-declaration-text mb-2">
                                            You are about to transmit this complete application dossier to the Official Scrutiny Department. Upon submission, an official immutable Application ID (<span class="font-monospace fw-bold">MP-{{ date('Y') }}-NNNN</span>) will be assigned, the draft will be permanently locked, and designated handling personnel will be assigned for statutory vetting.
                                        </div>
                                        <div class="form-check s8-check-confirm">
                                            <input class="form-check-input" type="checkbox" id="confirm_mining_scrutiny_ack">
                                            <label class="form-check-label fw-semibold" for="confirm_mining_scrutiny_ack">
                                                I confirm that all applicant metadata, statutory deeds, quarry extent coordinates, team handlers, and billing records have been verified and audited for submission.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mining-callout-info d-flex align-items-center gap-3 p-3 rounded-3 mb-0">
                                <div class="mining-banner-icon-badge" style="width:36px; height:36px; font-size:16px;">
                                    <i class="bi bi-rocket-takeoff-fill"></i>
                                </div>
                                <div class="small text-navy">
                                    Submitting will generate your unique Mining Plan statutory dossier <strong>MP-{{ date('Y') }}-XXXX</strong> (Common Tracking ID: <strong>GTMS-{{ date('Y') }}-XXXX</strong>) and initialize the <strong>Stage 6.1 – 6.6 Process Flow</strong>.
                                </div>
                            </div>
                        </div>

                        {{-- ========================================================== --}}
                        {{-- WIZARD ACTION BUTTONS DOCK (SAVE DRAFT + BACK + CONTINUE)   --}}
                        {{-- ========================================================== --}}
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top flex-wrap gap-2">
                            <a href="{{ route('miningplan.index') }}" class="btn s1-btn-cancel px-3.5" id="btnCancel">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-outline-slate px-4" id="btnPrev" style="display:none;">
                                <i class="bi bi-arrow-left me-2"></i>Back
                            </button>
                            <div class="d-flex gap-2 ms-auto">
                                <button type="button" class="btn btn-outline-primary s-btn-draft px-3.5" id="btnSaveDraft">
                                    <i class="bi bi-floppy me-2"></i>Save Draft &amp; Exit
                                </button>
                                <button type="button" class="btn btn-navy s-btn-submit px-4" id="btnNext">
                                    Continue<i class="bi bi-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn-emerald px-4" id="btnSubmit" style="display:none;" disabled>
                                    <i class="bi bi-check2-circle me-1.5"></i>Submit Final Application
                                </button>
                            </div>
                        </div>

                    </div>
                </form>

                <!-- ========================================== -->
                <!-- MODAL: ADD CUSTOM DOCUMENT (FOLDER-WISE)   -->
                <!-- ========================================== -->
                <!-- MODAL: ADD CUSTOM STATUTORY DOCUMENT       -->
                <!-- ========================================== -->
                <div class="modal fade" id="modalAddCustomMiningDoc" tabindex="-1" aria-labelledby="modalAddCustomMiningDocLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content mining-modal-content s5-modal-content border-0 shadow">
                            <div class="modal-header s5-modal-header py-3 px-4">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="s5-modal-icon-badge">
                                        <i class="bi bi-folder-plus text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="modal-title fs-6 fw-bold text-white mb-0" id="modalAddCustomMiningDocLabel">
                                            Add Document to Folder
                                        </h5>
                                        <span class="text-white-50 small">Custom Statutory Document Attachment</span>
                                    </div>
                                </div>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="formAddCustomMiningDoc">
                                <div class="modal-body p-4">
                                    <!-- Selected Folder (Locked) -->
                                    <div class="mb-3">
                                        <label class="form-label s5-modal-label text-uppercase mb-1">Target Statutory Folder</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-folder2-open text-primary"></i></span>
                                            <input type="text" class="form-control bg-light border-start-0 fw-semibold text-navy" id="mining_custom_folder_name" readonly value="">
                                        </div>
                                        <input type="hidden" id="mining_custom_folder_id" value="">
                                        <input type="hidden" id="mining_custom_now_id" value="">
                                    </div>

                                    <!-- Document Name -->
                                    <div class="mb-3">
                                        <label for="mining_custom_doc_name" class="form-label s5-modal-label mb-1">
                                            Document Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control mining-input" id="mining_custom_doc_name" placeholder="e.g. Property Tax Receipt, Local Panchayat NOC, Drone Survey Raw Log" required>
                                        <div class="form-text s5-field-hint">Specify a clear statutory or project title for this document.</div>
                                    </div>

                                    <!-- Status (Mandatory / Optional) -->
                                    <div class="mb-3">
                                        <label for="mining_custom_doc_status" class="form-label s5-modal-label mb-1">
                                            Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select mining-select" id="mining_custom_doc_status" required>
                                            <option value="1">Mandatory (Required for Clearance)</option>
                                            <option value="0" selected>Optional (Supporting Document)</option>
                                        </select>
                                        <div class="form-text s5-field-hint">Mark whether this document is mandatory or optional supporting proof.</div>
                                    </div>

                                    <!-- File Upload Option -->
                                    <div class="mb-2">
                                        <label for="mining_custom_doc_file" class="form-label s5-modal-label mb-1">
                                            File Upload <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" class="form-control mining-input" id="mining_custom_doc_file" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml,.dwg,.doc,.docx" required>
                                        <div class="form-text s5-field-hint">Supports PDF, Images, KML, CAD (DWG), and Office docs up to 25 MB.</div>
                                    </div>
                                </div>
                                <div class="modal-footer s5-modal-footer px-4 py-2.5">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-sm btn-navy px-3" id="btn_confirm_add_mining_doc">
                                        <i class="bi bi-cloud-arrow-up me-1"></i>Attach &amp; Add Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ========================================================== -->
<!-- PASS FOLDER COUNTS & CUSTOMER DATA TO JS                    -->
<!-- ========================================================== -->
<script>
    var folderCounts = @json($folderCounts);
    var preloadedCustomers = @json($customers);
</script>

<!-- ========================================================== -->
<!-- SCOPED CSS TO PREVENT GLOBAL BLEEDING                      -->
<!-- ========================================================== -->
<style>
/* -------------------------------------------------------------------------
   SCOPED DESIGN TOKENS FOR MINING WIZARD (#gtms-mining-page)
   ------------------------------------------------------------------------- */
#gtms-mining-page {
    --min-primary: #0F1E4D;
    --min-primary-hover: #162a6b;
    --min-surface: #ffffff;
    --min-surface-subtle: #f8fafc;
    --min-surface-elevated: #ffffff;
    --min-border: #e2e8f0;
    --min-border-strong: #cbd5e1;
    --min-text-dark: #0f172a;
    --min-text-muted: #64748b;
    --min-navy: #0F1E4D;
    --min-emerald: #10b981;
    --min-amber: #f59e0b;
    --min-rose: #ef4444;
    --min-cyan: #06b6d4;
    --min-purple: #8b5cf6;
    --min-input-bg: #ffffff;
}

#gtms-mining-page .wizard-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 8px 10px 48px;
}

/* Main Card */
#gtms-mining-page .mining-main-card {
    background: var(--min-surface);
    border: 1px solid var(--min-border);
    border-radius: 14px;
    padding: 24px 28px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
}

/* Stepper Progress Bar (Lease Application Standard) */
#gtms-mining-page .step-progress {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    padding: 10px 14px;
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    margin-bottom: 22px;
}

#gtms-mining-page .step-progress::before {
    content: "";
    position: absolute;
    top: 26px;
    left: 45px;
    right: 45px;
    height: 2px;
    background: var(--min-border);
    z-index: 1;
}

#gtms-mining-page .sp-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: transparent;
    text-decoration: none;
    min-width: 60px;
    text-align: center;
    cursor: pointer;
    user-select: none;
    transition: transform 0.15s ease;
}

#gtms-mining-page .sp-step:hover .circ {
    transform: scale(1.08);
}

#gtms-mining-page .sp-step .circ {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.80rem;
    font-weight: 700;
    border: 2px solid var(--min-border-strong);
    background: var(--min-surface);
    color: var(--min-text-muted);
    transition: all 0.25s ease;
}

#gtms-mining-page .sp-step.active .circ {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.16);
}

#gtms-mining-page .sp-step.done .circ {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

#gtms-mining-page .sp-step .sp-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--min-text-muted);
    white-space: nowrap;
    letter-spacing: 0.01em;
    margin-top: 4px;
}

#gtms-mining-page .sp-step.active .sp-label {
    color: #0F1E4D;
    font-weight: 800;
}

#gtms-mining-page .sp-step.done .sp-label {
    color: #10b981;
    font-weight: 700;
}

/* ==========================================================================
   STEP 1 (LEASE APPLICATION PARITY DESIGN SYSTEM)
   ========================================================================== */
#gtms-mining-page .s1-header-block {
    margin-bottom: 20px;
}

#gtms-mining-page .s1-eyebrow {
    color: var(--orange, #ea580c);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
    margin-bottom: 0;
}

#gtms-mining-page .s1-time-tag {
    font-size: 0.73rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.2;
}

#gtms-mining-page .s1-title {
    font-weight: 800;
    font-size: 1.25rem;
    color: #0F1E4D;
    line-height: 1.3;
    margin-top: 6px;
    margin-bottom: 5px;
}

#gtms-mining-page .s1-subtitle {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0;
}

/* LOOKUP CARD - LIGHT */
#gtms-mining-page .s1-lookup-card {
    background: #f0f7ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 20px;
    transition: all 0.2s ease;
}

#gtms-mining-page .s1-lookup-label {
    font-size: 0.88rem;
    color: #1e40af;
    line-height: 1.2;
}

#gtms-mining-page .s1-lookup-badge {
    background-color: #0F1E4D;
    color: #ffffff;
    font-size: 0.70rem;
    padding: 4px 8px;
    border-radius: 9999px;
    font-weight: 600;
}

#gtms-mining-page .s1-lookup-desc {
    color: #475569;
    font-size: 0.80rem;
    line-height: 1.45;
}

#gtms-mining-page .s1-lookup-input-group .s1-lookup-addon {
    width: 42px;
    height: 42px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-right: none !important;
    border-radius: 8px 0 0 8px;
    color: #64748b;
}

#gtms-mining-page .s1-lookup-input-group .s1-lookup-field {
    height: 42px;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    border-right: none !important;
    border-radius: 0;
    font-size: 0.88rem;
    padding: 8px 12px;
    background-color: #ffffff;
    color: #0f172a;
}

#gtms-mining-page .s1-lookup-input-group .s1-btn-fetch {
    background-color: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    font-size: 0.84rem;
    height: 42px;
    border-radius: 0 8px 8px 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 18px;
    font-weight: 700;
    transition: all 0.2s ease;
}

#gtms-mining-page .s1-lookup-input-group .s1-btn-fetch:hover {
    background-color: #081233;
    border-color: #081233;
    color: #ffffff;
}

/* SECTION PANELS - LIGHT */
#gtms-mining-page .s1-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 18px;
    transition: border-color 0.2s ease;
}

#gtms-mining-page .s1-section-card:hover {
    border-color: #cbd5e1;
}

#gtms-mining-page .s1-section-header {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0F1E4D;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    line-height: 1.2;
}

#gtms-mining-page .s1-label {
    font-size: 0.80rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.25;
    display: flex;
    align-items: center;
}

#gtms-mining-page .s1-form-row {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1.25rem;
}

#gtms-mining-page .s1-opt-tag {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: normal;
    margin-left: 3px;
}

#gtms-mining-page .s1-hint {
    font-size: 0.72rem;
    color: #64748b;
    line-height: 1.4;
    margin-top: 6px;
    margin-bottom: 2px;
}

/* INPUT GROUPS - LIGHT */
#gtms-mining-page .s1-input-group {
    border-radius: 8px;
    transition: all 0.2s ease;
}

#gtms-mining-page .s1-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10);
    border-radius: 8px;
}

#gtms-mining-page .s1-input-group .s1-addon {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-right: none !important;
    border-radius: 8px 0 0 8px;
    color: #64748b;
    font-size: 0.88rem;
    width: 38px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

#gtms-mining-page .s1-input-group .s1-input {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    border-radius: 0 8px 8px 0 !important;
    font-size: 0.86rem;
    height: 40px;
    padding: 8px 12px;
    color: #0f172a;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
}

#gtms-mining-page .s1-input-group:focus-within .s1-addon {
    border-color: #0F1E4D !important;
    color: #0F1E4D;
}

#gtms-mining-page .s1-input-group:focus-within .s1-input {
    border-color: #0F1E4D !important;
}

#gtms-mining-page .s1-textarea {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 0.86rem;
    padding: 10px 14px !important;
    min-height: 80px !important;
    line-height: 1.5;
    color: #0f172a;
    background-color: #ffffff;
    transition: all 0.2s ease;
    width: 100%;
}

#gtms-mining-page .s1-textarea:focus {
    border-color: #0F1E4D !important;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10) !important;
    outline: none;
}

/* NOTICE CARD - LIGHT */
#gtms-mining-page .s1-notice-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    padding: 14px 18px;
    margin-top: 20px;
}
#gtms-mining-page .s1-notice-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #dbeafe;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
#gtms-mining-page .s1-notice-icon {
    color: #1d4ed8;
    font-size: 1.2rem;
    line-height: 1;
}
#gtms-mining-page .s1-notice-text {
    font-size: 0.84rem;
    color: #1e3a8a;
    line-height: 1.5;
}

#gtms-mining-page .field-autofilled {
    background-color: #ecfdf5 !important;
    border-color: #10b981 !important;
    transition: background-color 0.6s ease;
}

#gtms-mining-page .s1-btn-cancel {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #64748b;
    font-size: 0.86rem;
    font-weight: 600;
    height: 40px;
    padding: 0 18px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
}

#gtms-mining-page .s1-btn-cancel:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #334155;
}

#gtms-mining-page .s-btn-draft {
    background: #ffffff;
    border: 1.5px solid #0F1E4D !important;
    color: #0F1E4D !important;
    font-size: 0.86rem;
    font-weight: 700;
    height: 40px;
    padding: 0 18px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

#gtms-mining-page .s-btn-draft:hover {
    background: #eff6ff !important;
    color: #0F1E4D !important;
}

#gtms-mining-page .s-btn-submit {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    font-size: 0.86rem;
    font-weight: 700;
    height: 40px;
    padding: 0 24px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    transition: all 0.2s ease;
}

#gtms-mining-page .s-btn-submit:hover {
    background: #162a6b !important;
    border-color: #162a6b !important;
    box-shadow: 0 4px 10px rgba(15, 30, 77, 0.3);
    transform: translateY(-1px);
}

/* Header & Badges */
#gtms-mining-page .mining-eyebrow {
    font-size: 11px;
    letter-spacing: 0.06em;
}

#gtms-mining-page .mining-title {
    font-size: 1.35rem;
    color: var(--min-text-dark);
}

#gtms-mining-page .mining-subtitle {
    font-size: 13px;
}

#gtms-mining-page .mining-status-pill {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 9999px;
    font-weight: 600;
}

#gtms-mining-page .mining-step-badge {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #0F1E4D;
    margin-bottom: 2px;
}

#gtms-mining-page .mining-badge-success-pill {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    font-size: 12px;
    padding: 6px 14px;
    border-radius: 9999px;
    font-weight: 600;
}

/* Resumed / Import Banner */
#gtms-mining-page .mining-banner-card {
    background: #f0f7ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 14px;
    padding: 16px 20px;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.04);
}

#gtms-mining-page .mining-banner-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #0F1E4D;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

#gtms-mining-page .mining-banner-title {
    font-size: 14.5px;
    color: #0F1E4D;
}

#gtms-mining-page .mining-banner-desc {
    font-size: 12px;
    color: #475569;
}

/* Form Controls */
#gtms-mining-page .mining-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #334155;
    margin-bottom: 6px;
}

#gtms-mining-page .mining-input,
#gtms-mining-page .mining-select {
    height: 42px;
    border-radius: 8px;
    border: 1px solid var(--min-border-strong);
    background: var(--min-input-bg);
    color: var(--min-text-dark);
    font-size: 13.5px;
    transition: all 0.2s ease;
}

#gtms-mining-page .mining-textarea {
    border-radius: 8px;
    border: 1px solid var(--min-border-strong);
    background: var(--min-input-bg);
    color: var(--min-text-dark);
    font-size: 13.5px;
}

#gtms-mining-page .mining-input:focus,
#gtms-mining-page .mining-select:focus,
#gtms-mining-page .mining-textarea:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

#gtms-mining-page .mining-hint {
    font-size: 11px;
    color: var(--min-text-muted);
    margin-top: 4px;
}

/* Lookup Card */
#gtms-mining-page .mining-lookup-card {
    background: #f8fbff;
    border: 1.5px solid #bfdbfe;
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.04);
}

/* Nature of Work Cards */
#gtms-mining-page .nature-card {
    border: 1.5px solid var(--min-border);
    border-radius: 12px;
    background: var(--min-surface);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}

#gtms-mining-page .nature-card:hover {
    border-color: #93c5fd;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.08);
}

#gtms-mining-page .nature-code-badge {
    background: #f1f5f9;
    color: #0F1E4D;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 6px;
    font-weight: 700;
}

#gtms-mining-page .nature-check-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    transition: all 0.2s ease;
}

#gtms-mining-page .nature-card-title {
    font-size: 13px;
    color: var(--min-text-dark);
}

#gtms-mining-page .nature-card-desc {
    font-size: 11px;
    color: var(--min-text-muted);
}

#gtms-mining-page .nature-work-radio:checked + .nature-card {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 6px 16px rgba(15, 30, 77, 0.22);
}

#gtms-mining-page .nature-work-radio:checked + .nature-card .nature-code-badge {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

#gtms-mining-page .nature-work-radio:checked + .nature-card .nature-check-circle {
    background: #ffffff;
    border-color: #ffffff;
    color: #0F1E4D;
}

#gtms-mining-page .nature-work-radio:checked + .nature-card .nature-card-title {
    color: #ffffff !important;
}

#gtms-mining-page .nature-work-radio:checked + .nature-card .nature-card-desc {
    color: #cbd5e1 !important;
}

/* Step 3: Section Cards & Badges */
#gtms-mining-page .s3-section-card {
    background: var(--min-surface);
    border: 1px solid var(--min-border);
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 20px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#gtms-mining-page .s3-section-card:hover {
    border-color: #cbd5e1;
}

#gtms-mining-page .s3-section-header {
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--min-border);
}

#gtms-mining-page .s3-section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0F1E4D;
    letter-spacing: -0.01em;
}

#gtms-mining-page .s3-rule-badge {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.72rem;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
}

/* Step 3: Mineral Cards */
#gtms-mining-page .mineral-card {
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    background: var(--min-surface);
    min-height: 54px;
    padding: 10px 14px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    user-select: none;
}

#gtms-mining-page .mineral-card:hover {
    border-color: #94a3b8;
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
}

#gtms-mining-page .mineral-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #eff6ff;
    color: #2563eb;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

#gtms-mining-page .mineral-card .mineral-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    letter-spacing: -0.01em;
}

#gtms-mining-page .mineral-check-badge {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

#gtms-mining-page .mineral-checkbox:checked + .mineral-card {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 4px 14px rgba(15, 30, 77, 0.20) !important;
    transform: translateY(-1px);
}

#gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-icon-wrap {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #38bdf8 !important;
}

#gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-title {
    color: #ffffff !important;
}

#gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-check-badge {
    background: #ffffff !important;
    border-color: #ffffff !important;
    color: #0F1E4D !important;
}

/* Step 3: Plan Cards */
#gtms-mining-page .plan-card {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    background: var(--min-surface);
    min-height: 72px;
    padding: 14px 16px;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

#gtms-mining-page .plan-card:hover {
    border-color: #94a3b8;
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
}

#gtms-mining-page .plan-radio-circle {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: transparent;
    flex-shrink: 0;
    transition: all 0.2s ease;
}

#gtms-mining-page .plan-title {
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
}

#gtms-mining-page .plan-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.4;
}

#gtms-mining-page .plan-type-radio:checked + .plan-card {
    background: #f0f7ff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 4px 14px rgba(15, 30, 77, 0.12) !important;
}

#gtms-mining-page .plan-type-radio:checked + .plan-card .plan-radio-circle {
    border-color: #0F1E4D;
    background: #0F1E4D;
    color: #ffffff;
}

#gtms-mining-page .plan-type-radio:checked + .plan-card .plan-title {
    color: #0F1E4D;
}

#gtms-mining-page .plan-type-radio:checked + .plan-card .plan-desc {
    color: #334155;
}

/* Callout Banners */
#gtms-mining-page .mining-callout-info {
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    border-left: 4px solid #2563eb;
}

/* Folder Boxes */
#gtms-mining-page .mining-folder-box {
    border: 1px solid var(--min-border);
    border-radius: 12px;
    background: var(--min-surface);
    transition: all 0.2s ease;
}

#gtms-mining-page .folder-accent-blue { border-left: 4px solid #3b82f6 !important; }
#gtms-mining-page .folder-accent-purple { border-left: 4px solid #8b5cf6 !important; }
#gtms-mining-page .folder-accent-cyan { border-left: 4px solid #06b6d4 !important; }
#gtms-mining-page .folder-accent-amber { border-left: 4px solid #f59e0b !important; }
#gtms-mining-page .folder-accent-emerald { border-left: 4px solid #10b981 !important; }
#gtms-mining-page .folder-accent-rose { border-left: 4px solid #ef4444 !important; }

#gtms-mining-page .folder-icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

#gtms-mining-page .icon-blue { background: #eff6ff; color: #2563eb; }
#gtms-mining-page .icon-purple { background: #f5f3ff; color: #8b5cf6; }
#gtms-mining-page .icon-cyan { background: #ecfeff; color: #0891b2; }
#gtms-mining-page .icon-amber { background: #fffbeb; color: #d97706; }
#gtms-mining-page .icon-emerald { background: #ecfdf5; color: #059669; }
#gtms-mining-page .icon-rose { background: #fef2f2; color: #dc2626; }
#gtms-mining-page .icon-navy { background: #e0e7ff; color: #0F1E4D; }

/* Bento KPI Cards */
#gtms-mining-page .mining-kpi-card {
    background: var(--min-surface);
    border: 1px solid var(--min-border);
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    height: 100%;
}

#gtms-mining-page .kpi-val-card { border-top: 3px solid #0F1E4D; }
#gtms-mining-page .kpi-paid-card { border-top: 3px solid #10b981; }
#gtms-mining-page .kpi-pending-card { border-top: 3px solid #ef4444; }
#gtms-mining-page .kpi-amber-card { border-top: 3px solid #f59e0b; }
#gtms-mining-page .kpi-purple-card { border-top: 3px solid #8b5cf6; }

#gtms-mining-page .mining-kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--min-text-muted);
}

#gtms-mining-page .mining-kpi-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

#gtms-mining-page .mining-kpi-val {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 4px;
}

#gtms-mining-page .mining-kpi-sub {
    font-size: 11.5px;
    color: var(--min-text-muted);
}

/* Section Panels */
#gtms-mining-page .mining-section-panel {
    background: var(--min-surface);
    border: 1px solid var(--min-border);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
}

#gtms-mining-page .mining-panel-header {
    background: var(--min-surface-subtle);
    padding: 12px 18px;
    border-bottom: 1px solid var(--min-border);
}

#gtms-mining-page .mining-panel-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
}

#gtms-mining-page .mining-panel-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--min-text-dark);
}

#gtms-mining-page .mining-data-tile {
    background: var(--min-surface-subtle);
    border: 1px solid var(--min-border);
    border-radius: 10px;
    padding: 12px 14px;
    height: 100%;
}

#gtms-mining-page .mining-data-tile .tile-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    color: var(--min-text-muted);
    margin-bottom: 4px;
}

#gtms-mining-page .mining-data-tile .tile-val {
    display: block;
    font-size: 13px;
    color: var(--min-text-dark);
    word-break: break-word;
}

/* Buttons */
#gtms-mining-page .btn-navy {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    font-weight: 600;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}

#gtms-mining-page .btn-navy:hover {
    background: #162a6b;
    border-color: #162a6b;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
}

#gtms-mining-page .btn-emerald {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    font-weight: 600;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}

#gtms-mining-page .btn-emerald:hover {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}

#gtms-mining-page .btn-outline-slate {
    background: transparent;
    border: 1.5px solid #cbd5e1;
    color: #475569;
    font-weight: 600;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.2s ease;
}

#gtms-mining-page .btn-outline-slate:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
}

#gtms-mining-page .mining-table thead th {
    background: var(--min-surface-subtle);
    color: var(--min-text-muted);
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 10px 14px;
    border-bottom: 1px solid var(--min-border);
}

#gtms-mining-page .mining-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--min-border);
}

/* Modal Content */
.mining-modal-content {
    background: var(--min-surface);
    border-radius: 14px;
}

.mining-modal-header {
    background: var(--min-surface-subtle);
    border-bottom: 1px solid var(--min-border);
}

/* -------------------------------------------------------------------------
   DARK MODE OVERRIDES (Strictly Scoped)
   ------------------------------------------------------------------------- */
body[data-theme-version="dark"] #gtms-mining-page {
    --min-primary: #38bdf8;
    --min-primary-hover: #0284c7;
    --min-surface: #1e293b;
    --min-surface-subtle: #0f172a;
    --min-surface-elevated: #243247;
    --min-border: #334155;
    --min-border-strong: #475569;
    --min-text-dark: #f8fafc;
    --min-text-muted: #94a3b8;
    --min-navy: #38bdf8;
    --min-input-bg: #0f172a;
}

body[data-theme-version="dark"] #gtms-mining-page .mining-label {
    color: #e2e8f0;
}

body[data-theme-version="dark"] #gtms-mining-page .mining-banner-card,
body[data-theme-version="dark"] #gtms-mining-page .mining-lookup-card,
body[data-theme-version="dark"] #gtms-mining-page .mining-callout-info {
    background: #0f172a;
    border-color: #1e3a8a;
}

body[data-theme-version="dark"] #gtms-mining-page .mining-status-pill {
    background: #0f172a;
    border-color: #334155;
    color: #e2e8f0;
}

body[data-theme-version="dark"] #gtms-mining-page .s3-section-card {
    background: #18191a !important;
    border-color: #2b2d30 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .s3-section-header {
    border-bottom-color: #2b2d30 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .s3-section-title {
    color: #f1f5f9 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .s3-rule-badge {
    background-color: #1e293b !important;
    color: #60a5fa !important;
    border-color: #2563eb !important;
}

body[data-theme-version="dark"] #gtms-mining-page .nature-card,
body[data-theme-version="dark"] #gtms-mining-page .mineral-card,
body[data-theme-version="dark"] #gtms-mining-page .plan-card {
    background: #18191a !important;
    border-color: #2b2d30 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .nature-card:hover,
body[data-theme-version="dark"] #gtms-mining-page .mineral-card:hover,
body[data-theme-version="dark"] #gtms-mining-page .plan-card:hover {
    background: #202225 !important;
    border-color: #3e4451 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .nature-card-title,
body[data-theme-version="dark"] #gtms-mining-page .mineral-title,
body[data-theme-version="dark"] #gtms-mining-page .plan-title {
    color: #f8fafc !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-icon-wrap {
    background: #25282a !important;
    color: #60a5fa !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-check-badge {
    border-color: #4b5563 !important;
    background: #18191a !important;
}

body[data-theme-version="dark"] #gtms-mining-page .nature-work-radio:checked + .nature-card {
    background: #2563eb !important;
    border-color: #2563eb !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-checkbox:checked + .mineral-card {
    background: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25) !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-icon-wrap {
    background: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-title {
    color: #ffffff !important;
}

body[data-theme-version="dark"] #gtms-mining-page .mineral-checkbox:checked + .mineral-card .mineral-check-badge {
    background: #ffffff !important;
    border-color: #ffffff !important;
    color: #2563eb !important;
}

body[data-theme-version="dark"] #gtms-mining-page .plan-radio-circle {
    border-color: #4b5563 !important;
    background: #18191a !important;
}

body[data-theme-version="dark"] #gtms-mining-page .plan-type-radio:checked + .plan-card {
    background: #13233a !important;
    border-color: #3b82f6 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .plan-type-radio:checked + .plan-card .plan-radio-circle {
    border-color: #3b82f6 !important;
    background: #3b82f6 !important;
    color: #ffffff !important;
}

body[data-theme-version="dark"] #gtms-mining-page .plan-type-radio:checked + .plan-card .plan-title {
    color: #60a5fa !important;
}

body[data-theme-version="dark"] #gtms-mining-page .plan-type-radio:checked + .plan-card .plan-desc {
    color: #cbd5e1 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .folder-doc-block {
    background: #1e293b !important;
    border-color: #334155 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .folder-doc-header {
    background: #0f172a !important;
    border-color: #334155 !important;
}

body[data-theme-version="dark"] #gtms-mining-page .ci-name {
    color: #f8fafc !important;
}

body[data-theme-version="dark"] #gtms-mining-page .btn-navy {
    background: #2563eb;
    border-color: #2563eb;
}

body[data-theme-version="dark"] #gtms-mining-page .btn-navy:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

body[data-theme-version="dark"] #gtms-mining-page .btn-outline-slate {
    border-color: #475569;
    color: #cbd5e1;
}

body[data-theme-version="dark"] #gtms-mining-page .btn-outline-slate:hover {
    background: #334155;
    color: #ffffff;
}

body[data-theme-version="dark"] .mining-modal-content {
    background: #1e293b;
    color: #f8fafc;
}

body[data-theme-version="dark"] .mining-modal-header {
    background: #0f172a;
    border-color: #334155;
}

/* -------------------------------------------------------------------------
   HARMONIZED EXECUTIVE SUITE STYLES (LEASE APPLICATION PARITY)
   ------------------------------------------------------------------------- */
#gtms-mining-page .s1-eyebrow {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--min-primary);
}

#gtms-mining-page .s1-time-tag {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
}

/* Quick Jump Edit Buttons (Step 9) */
#gtms-mining-page .s8-btn-edit {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #0F1E4D;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 4px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
}
#gtms-mining-page .s8-btn-edit:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
}

/* Step 5 Dossier Readiness Banner */
#gtms-mining-page .s4-summary-banner {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
}
#gtms-mining-page .s4-summary-badge {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    background: #0F1E4D;
    color: #ffffff;
    padding: 3px 8px;
    border-radius: 6px;
}
#gtms-mining-page .s4-summary-text {
    font-size: 0.84rem;
    color: #334155;
}
#gtms-mining-page .s4-summary-pct {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s4-summary-progress {
    height: 8px;
    border-radius: 999px;
    background: #e2e8f0;
    overflow: hidden;
}
#gtms-mining-page .s4-summary-bar {
    background: linear-gradient(90deg, #2563eb, #10b981);
    transition: width 0.4s ease;
}

/* Step 5 Rich Folder Cards */
#gtms-mining-page .s4-folder-card {
    background: var(--min-surface);
    border: 1.5px solid var(--min-border);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}
#gtms-mining-page .s4-folder-card:hover {
    transform: translateY(-2px);
    border-color: #0F1E4D;
    box-shadow: 0 8px 24px rgba(15, 30, 77, 0.09);
}
#gtms-mining-page .s4-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
#gtms-mining-page .s4-icon-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
#gtms-mining-page .s4-icon-purple { background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
#gtms-mining-page .s4-icon-cyan { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
#gtms-mining-page .s4-icon-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
#gtms-mining-page .s4-icon-emerald { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
#gtms-mining-page .s4-icon-rose { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

#gtms-mining-page .s4-folder-no {
    font-size: 0.68rem;
    font-weight: 700;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px 6px;
    border-radius: 5px;
    letter-spacing: 0.04em;
}
#gtms-mining-page .s4-doc-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
#gtms-mining-page .s4-chip {
    font-size: 0.68rem;
    font-weight: 500;
    background-color: #f1f5f9;
    color: #475569;
    padding: 2px 7px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}
#gtms-mining-page .s4-action-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #0F1E4D;
    transition: all 0.2s ease;
    cursor: pointer;
}
#gtms-mining-page .s4-folder-card:hover .s4-action-text {
    color: #2563eb;
    transform: translateX(2px);
}
#gtms-mining-page .s4-folder-card.is-complete {
    border-color: #10b981;
}
#gtms-mining-page .s4-folder-card.is-progress {
    border-color: #3b82f6;
}
#gtms-mining-page .s4-kpi-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
#gtms-mining-page .s4-progress-wrap {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 10px;
}
#gtms-mining-page .s4-meta-line {
    font-size: 0.72rem;
}
#gtms-mining-page .s4-count-label {
    color: #64748b;
    font-weight: 500;
}
#gtms-mining-page .s4-count-num {
    color: #0F1E4D;
    font-weight: 700;
}
#gtms-mining-page .s4-card-progress {
    height: 6px;
    background-color: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}
#gtms-mining-page .s4-bar-blue { background-color: #2563eb; transition: width 0.3s ease; }
#gtms-mining-page .s4-bar-purple { background-color: #9333ea; transition: width 0.3s ease; }
#gtms-mining-page .s4-bar-cyan { background-color: #0891b2; transition: width 0.3s ease; }
#gtms-mining-page .s4-bar-amber { background-color: #d97706; transition: width 0.3s ease; }
#gtms-mining-page .s4-bar-emerald { background-color: #059669; transition: width 0.3s ease; }
#gtms-mining-page .s4-bar-rose { background-color: #e11d48; transition: width 0.3s ease; }
#gtms-mining-page .s4-card-footer {
    border-top: 1px dashed #e2e8f0;
    margin-top: 8px;
}
#gtms-mining-page .s4-badge-pending {
    background-color: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
}
#gtms-mining-page .s4-badge-progress {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
}
#gtms-mining-page .s4-badge-complete {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
}
#gtms-mining-page .s4-notice-card,
#gtms-mining-page .s4-notice-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #0F1E4D;
    border-radius: 12px;
    padding: 16px 20px;
    margin: 24px 0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .s4-notice-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #1d4ed8;
    flex-shrink: 0;
}
#gtms-mining-page .s4-notice-title {
    font-size: 0.90rem;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s4-notice-desc {
    font-size: 0.80rem;
    color: #64748b;
    line-height: 1.5;
}
#gtms-mining-page .s4-rule-pill {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #e0e7ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    padding: 3px 8px;
    border-radius: 5px;
}
#gtms-mining-page .s4-btn-proceed {
    background: #0F1E4D;
    color: #ffffff;
    border: 1px solid #0F1E4D;
    font-weight: 600;
    font-size: 0.82rem;
    padding: 8px 18px;
    border-radius: 8px;
    white-space: nowrap;
    transition: all 0.2s ease;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.12);
}
#gtms-mining-page .s4-btn-proceed:hover {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.20);
}

/* ==========================================================================
   STEP 6: REGULATORY REPOSITORY & DIGITAL PLANS (LEASE STEP 5 PARITY)
   ========================================================================== */
#gtms-mining-page .min-w-0 {
    min-width: 0;
}
#gtms-mining-page .s5-summary-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .s5-summary-badge {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 0.70rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
#gtms-mining-page .s5-summary-label {
    font-size: 0.84rem;
    font-weight: 600;
    color: #334155;
}
#gtms-mining-page .s5-counter-text {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0F1E4D;
    line-height: 1;
}
#gtms-mining-page .s5-counter-sub {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 600;
}
#gtms-mining-page .s5-summary-progress {
    height: 8px;
    background-color: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
}
#gtms-mining-page .s5-summary-bar {
    background: linear-gradient(90deg, #0F1E4D 0%, #2563eb 100%);
    border-radius: 999px;
    transition: width 0.4s ease;
}

/* FOLDER HEADINGS */
#gtms-mining-page .s5-folder-heading-wrap {
    margin-top: 24px;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1.5px solid #e2e8f0;
}
#gtms-mining-page .s5-folder-heading {
    display: flex;
    align-items: center;
    gap: 12px;
}
#gtms-mining-page .s5-folder-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #1d4ed8;
    flex-shrink: 0;
}
#gtms-mining-page .s5-folder-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #0F1E4D;
    margin-bottom: 2px;
    line-height: 1.25;
}
#gtms-mining-page .s5-folder-subtitle {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}
#gtms-mining-page .s5-btn-add-doc {
    font-size: 0.74rem;
    font-weight: 600;
    color: #0F1E4D;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 12px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}
#gtms-mining-page .s5-btn-add-doc:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.15);
}
#gtms-mining-page .s5-folder-pill {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.74rem;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 6px;
}

/* CHECKLIST ROWS */
#gtms-mining-page .folder-checklist-box {
    margin-bottom: 16px;
}
#gtms-mining-page .checklist-row {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
#gtms-mining-page .checklist-row:hover {
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .checklist-row.up {
    border-color: #cbd5e1;
}
#gtms-mining-page .checklist-row.row-dragover {
    border: 2px dashed #2563eb !important;
    background: #eff6ff !important;
}
#gtms-mining-page .ci-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}
#gtms-mining-page .checklist-row.up .ci-icon {
    background: #ecfdf5;
    color: #059669;
    border-color: #a7f3d0;
}
#gtms-mining-page .ci-icon.ci-icon-carried {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
#gtms-mining-page .ci-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    margin-bottom: 2px;
}
#gtms-mining-page .ci-meta {
    font-size: 0.75rem;
    color: #64748b;
    line-height: 1.3;
}
#gtms-mining-page .doc-req-select {
    font-size: 0.73rem;
    font-weight: 600;
    height: 28px;
    border-radius: 6px;
    padding: 2px 22px 2px 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
}
#gtms-mining-page .doc-req-select:focus {
    box-shadow: 0 0 0 2px rgba(15, 30, 77, 0.15) !important;
}
#gtms-mining-page .s5-badge-attached {
    height: 28px;
    font-size: 0.70rem;
    font-weight: 700;
    border-radius: 6px;
    padding: 0 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
#gtms-mining-page .s5-badge-carried {
    height: 28px;
    font-size: 0.70rem;
    font-weight: 700;
    border-radius: 6px;
    padding: 0 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}
#gtms-mining-page .s5-btn-upload {
    height: 28px;
    font-size: 0.74rem;
    font-weight: 600;
    padding: 0 12px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #cbd5e1;
    color: #0F1E4D;
    background: #ffffff;
    transition: all 0.15s ease-in-out;
}
#gtms-mining-page .s5-btn-upload:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff !important;
}
#gtms-mining-page .s5-btn-action {
    height: 28px;
    width: 30px;
    font-size: 0.80rem;
    padding: 0;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease-in-out;
}
#gtms-mining-page .s5-notice-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 4px solid #0F1E4D;
    border-radius: 12px;
    padding: 16px 20px;
    margin: 24px 0;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .s5-notice-icon-wrap {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #1d4ed8;
    flex-shrink: 0;
}
#gtms-mining-page .s5-notice-title {
    font-size: 0.90rem;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s5-notice-desc {
    font-size: 0.80rem;
    color: #64748b;
    line-height: 1.5;
}
#gtms-mining-page .s5-rule-pill {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #e0e7ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    padding: 3px 8px;
    border-radius: 5px;
}
.s5-modal-header {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%) !important;
    border-bottom: none !important;
}
.s5-modal-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: #ffffff;
    flex-shrink: 0;
}
.s5-modal-label {
    font-size: 0.80rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.35rem;
}
.s5-field-hint {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.35;
}
.s5-modal-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}
#modalAddCustomMiningDoc .form-control,
#modalAddCustomMiningDoc .form-select {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    color: #0f172a !important;
    font-size: 0.86rem !important;
    box-shadow: none !important;
}
#modalAddCustomMiningDoc .form-control:focus,
#modalAddCustomMiningDoc .form-select:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
}
#modalAddCustomMiningDoc #mining_custom_folder_name {
    background: #f1f5f9 !important;
    color: #0F1E4D !important;
    font-weight: 700 !important;
}

/* Step 7 Handling Team Banner & Badges */
#gtms-mining-page .s6-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
}
#gtms-mining-page .s6-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #0F1E4D;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
}
#gtms-mining-page .s6-banner-title {
    color: #0F1E4D;
    font-size: 0.95rem;
}
#gtms-mining-page .s6-count-pill {
    background: #e0e7ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 20px;
}
#gtms-mining-page .s6-team-pill {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 20px;
}
#gtms-mining-page .s6-tip-box {
    background: #fffbeb;
    border: 1px solid #fef3c7;
    border-left: 4px solid #f59e0b;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 0.82rem;
    color: #92400e;
}
#gtms-mining-page .s6-tip-icon {
    font-size: 1.1rem;
    color: #f59e0b;
}

/* Step 8 Billing Ledger Styles (Lease Step 7 Visual Parity) */
#gtms-mining-page .s7-ledger-pill {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-weight: 700;
    font-size: 12px;
    padding: 7px 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
}
#gtms-mining-page .s7-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
}
#gtms-mining-page .s7-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #e0e7ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
#gtms-mining-page .s7-banner-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #0F1E4D;
    margin-bottom: 2px;
}
#gtms-mining-page .s7-banner-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
}
#gtms-mining-page .s7-banner-status-pill {
    font-size: 12px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
}

/* 3-Bento KPI Metric Cards */
#gtms-mining-page .s7-kpi-card {
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
#gtms-mining-page .s7-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.08);
}
#gtms-mining-page .s7-kpi-contract {
    background: #f8fafc;
    border-left: 4px solid #0F1E4D !important;
}
#gtms-mining-page .s7-kpi-paid {
    background: #f0fdf4;
    border-left: 4px solid #10b981 !important;
}
#gtms-mining-page .s7-kpi-pending {
    background: #fff7ed;
    border-left: 4px solid #f97316 !important;
}
#gtms-mining-page .s7-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
}
#gtms-mining-page .s7-kpi-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
#gtms-mining-page .s7-kpi-icon-contract {
    background: #e2e8f0;
    color: #0F1E4D;
}
#gtms-mining-page .s7-kpi-icon-paid {
    background: #dcfce7;
    color: #10b981;
}
#gtms-mining-page .s7-kpi-icon-pending {
    background: #ffedd5;
    color: #f97316;
}
#gtms-mining-page .s7-kpi-value {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 4px;
    font-variant-numeric: tabular-nums;
}
#gtms-mining-page .s7-kpi-subtext {
    font-size: 11.5px;
    color: #64748b;
}

/* Integrated Form Section */
#gtms-mining-page .s7-form-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}
#gtms-mining-page .s7-form-section-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 14px 20px;
}
#gtms-mining-page .s7-form-section-title {
    font-size: 14px;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s7-badge-status-tag {
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 11px;
    border-radius: 6px;
    padding: 4px 10px;
}
#gtms-mining-page .s7-form-section-body {
    padding: 24px;
}
#gtms-mining-page .s7-label {
    font-size: 13px;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s7-auto-tag {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    background: #e2e8f0;
    padding: 3px 8px;
    border-radius: 4px;
}
#gtms-mining-page .s7-input-group {
    display: flex;
    align-items: stretch;
    height: 44px;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    overflow: hidden;
    background: #ffffff;
    transition: all 0.2s ease;
}
#gtms-mining-page .s7-input-group:focus-within {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
}
#gtms-mining-page .s7-group-readonly {
    background: #fff1f2;
    border-color: #fecaca;
}
#gtms-mining-page .s7-group-readonly:focus-within {
    border-color: #fca5a5;
    box-shadow: none;
}
#gtms-mining-page .s7-input-addon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    background: #f8fafc;
    border-right: 1.5px solid #cbd5e1;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
}
#gtms-mining-page .s7-addon-navy {
    color: #0F1E4D;
    background: #eff6ff;
    border-color: #bfdbfe;
}
#gtms-mining-page .s7-addon-success {
    color: #10b981;
    background: #f0fdf4;
    border-color: #bbf7d0;
}
#gtms-mining-page .s7-addon-danger {
    color: #dc2626;
    background: #fee2e2;
    border-color: #fecaca;
}
#gtms-mining-page .s7-input {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    padding: 8px 14px;
    font-size: 14.5px;
    font-weight: 700;
    color: #0f172a;
    width: 100%;
    height: 100%;
}
#gtms-mining-page .s7-input:focus {
    outline: none !important;
}
#gtms-mining-page .s7-input-readonly {
    color: #dc2626 !important;
    cursor: default;
}
#gtms-mining-page .s7-field-hint {
    font-size: 11.5px;
    color: #64748b;
    line-height: 1.35;
}
#gtms-mining-page .s7-preset-btn {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    transition: all 0.15s ease;
}
#gtms-mining-page .s7-preset-btn:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
}
#gtms-mining-page .s7-select {
    height: 44px;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 8px 14px;
    font-size: 13.5px;
    color: #0f172a;
    background-color: #ffffff;
    transition: all 0.2s ease;
}
#gtms-mining-page .s7-select:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
}
#gtms-mining-page .s7-notes-input {
    height: 44px;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 8px 14px;
    font-size: 13.5px;
    color: #0f172a;
    background-color: #ffffff;
    transition: all 0.2s ease;
}
#gtms-mining-page .s7-notes-input:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
}
#gtms-mining-page .s7-assessment-wrapper {
    margin-top: 0;
}
#gtms-mining-page .s7-feedback-box {
    height: 60px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 10px;
    padding: 10px 18px;
    transition: all 0.2s ease;
}
#gtms-mining-page .s7-feedback-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
#gtms-mining-page .s7-feedback-sub {
    color: #64748b;
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 700;
}
#gtms-mining-page .s7-feedback-title {
    font-size: 13.5px;
    color: #0F1E4D;
    line-height: 1.25;
}
#gtms-mining-page .s7-feedback-badge {
    font-size: 12px;
    font-weight: 700;
}
#gtms-mining-page .s7-tip-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 14px 18px;
}
#gtms-mining-page .s7-tip-icon {
    font-size: 20px;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
}
#gtms-mining-page .s7-tip-content {
    font-size: 12.5px;
    line-height: 1.5;
    color: #78350f;
}

/* Step 9 Dossier Scrutiny & Final Review Styles (Lease Step 8 Visual Parity) */
#gtms-mining-page .s8-scrutiny-pill {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-weight: 700;
    font-size: 12px;
    padding: 7px 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
}
#gtms-mining-page .s8-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
}
#gtms-mining-page .s8-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
#gtms-mining-page .s8-banner-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #0F1E4D;
    margin-bottom: 2px;
}
#gtms-mining-page .s8-banner-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
}
#gtms-mining-page .s8-banner-status-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
}

/* 4-Bento KPI Metric Cards */
#gtms-mining-page .s8-kpi-card {
    border-radius: 12px;
    padding: 16px 18px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
#gtms-mining-page .s8-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}
#gtms-mining-page .s8-kpi-applicant {
    border-left: 4px solid #0284c7 !important;
}
#gtms-mining-page .s8-kpi-location {
    border-left: 4px solid #10b981 !important;
}
#gtms-mining-page .s8-kpi-docs {
    border-left: 4px solid #f59e0b !important;
}
#gtms-mining-page .s8-kpi-finance {
    border-left: 4px solid #9333ea !important;
}
#gtms-mining-page .s8-kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
}
#gtms-mining-page .s8-kpi-val {
    font-size: 18px;
    font-weight: 800;
    color: #0F1E4D;
    margin-bottom: 8px;
    line-height: 1.25;
}
#gtms-mining-page .s8-kpi-sub {
    font-size: 12px;
}
#gtms-mining-page .s8-kpi-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
#gtms-mining-page .s8-kpi-icon-blue { background: #e0f2fe; color: #0284c7; }
#gtms-mining-page .s8-kpi-icon-emerald { background: #ecfdf5; color: #10b981; }
#gtms-mining-page .s8-kpi-icon-amber { background: #fffbeb; color: #d97706; }
#gtms-mining-page .s8-kpi-icon-purple { background: #f3e8ff; color: #9333ea; }

/* Chips & Tags */
#gtms-mining-page .s8-chip-blue {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}
#gtms-mining-page .s8-chip-emerald {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}
#gtms-mining-page .s8-chip-amber {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}
#gtms-mining-page .s8-chip-purple {
    background: #f3e8ff;
    color: #7e22ce;
    border: 1px solid #e9d5ff;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
}

/* Section Review Panels */
#gtms-mining-page .s8-section-panel {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}
#gtms-mining-page .s8-section-panel:hover {
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .s8-panel-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 13px 20px;
}
#gtms-mining-page .s8-panel-header-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
#gtms-mining-page .s8-icon-blue { background: #e0f2fe; color: #0284c7; }
#gtms-mining-page .s8-icon-emerald { background: #dcfce7; color: #16a34a; }
#gtms-mining-page .s8-icon-teal { background: #ccfbf1; color: #0d9488; }
#gtms-mining-page .s8-icon-green { background: #dcfce7; color: #16a34a; }
#gtms-mining-page .s8-icon-amber { background: #fef3c7; color: #d97706; }
#gtms-mining-page .s8-panel-title {
    font-size: 14px;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s8-panel-step-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
}
#gtms-mining-page .s8-panel-body {
    padding: 20px 22px;
}
#gtms-mining-page .s8-btn-edit {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 12px;
    transition: all 0.2s ease;
}
#gtms-mining-page .s8-btn-edit:hover {
    color: #0F1E4D;
    border-color: #0F1E4D;
    background: #f1f5f9;
}

/* Data Tiles */
#gtms-mining-page .s8-data-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px 16px;
    min-height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: border-color 0.2s ease;
}
#gtms-mining-page .s8-data-tile:hover {
    border-color: #cbd5e1;
}
#gtms-mining-page .s8-tile-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 4px;
}
#gtms-mining-page .s8-tile-value {
    font-size: 13.5px;
    font-weight: 700;
    color: #0f172a;
}

/* Table Styling */
#gtms-mining-page .s8-table {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 13px;
    width: 100%;
}
#gtms-mining-page .s8-table thead {
    background: #f8fafc;
}
#gtms-mining-page .s8-table th {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 14px;
}
#gtms-mining-page .s8-table td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #e2e8f0;
    color: #0f172a;
}
#gtms-mining-page .s8-avatar-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}
#gtms-mining-page .s8-role-badge {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
}
#gtms-mining-page .s8-empty-notice {
    background: #f8fafc;
    border: 1px dashed #e2e8f0;
    border-radius: 8px;
    padding: 16px 20px;
    color: #64748b;
    font-size: 13px;
}

/* Payment Ledger Cards (Step 8 Review) */
#gtms-mining-page .s8-ledger-card {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 10px;
    padding: 14px 16px;
    text-align: center;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
#gtms-mining-page .s8-ledger-sub {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 2px;
}
#gtms-mining-page .s8-ledger-amount {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 2px;
}
#gtms-mining-page .s8-ledger-hint {
    font-size: 10.5px;
}

/* Folder Cards & Detailed Registry */
#gtms-mining-page .s8-folder-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 14px;
    transition: all 0.15s ease;
}
#gtms-mining-page .s8-folder-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}
#gtms-mining-page .s8-folder-icon-wrap {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
#gtms-mining-page .s8-folder-title {
    font-size: 12.5px;
    font-weight: 700;
    color: #0F1E4D;
}
#gtms-mining-page .s8-folder-meta {
    font-size: 11px;
    color: #64748b;
}
#gtms-mining-page .s8-registry-wrapper {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    max-height: 380px;
    overflow-y: auto;
}
#gtms-mining-page .s8-btn-registry {
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    color: #334155;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 4px 10px;
    border-radius: 6px;
    transition: all 0.15s ease;
}
#gtms-mining-page .s8-btn-registry:hover {
    background: #0F1E4D;
    color: #ffffff;
    border-color: #0F1E4D;
}
#gtms-mining-page .s8-doc-attached {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
}
#gtms-mining-page .s8-doc-pending {
    background: #f8fafc;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
    font-size: 11.5px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
}
#gtms-mining-page .s8-btn-action-open {
    background: #ffffff;
    color: #0F1E4D;
    border: 1px solid #cbd5e1;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 5px;
    line-height: 1.2;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.15s ease;
}
#gtms-mining-page .s8-btn-action-open:hover {
    background: #0F1E4D;
    color: #ffffff;
    border-color: #0F1E4D;
}

/* Pre-Submission Declaration Card */
#gtms-mining-page .s8-declaration-card {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 12px;
    padding: 18px 22px;
}
#gtms-mining-page .s8-declaration-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #dcfce7;
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
#gtms-mining-page .s8-declaration-title {
    font-size: 14px;
    font-weight: 700;
    color: #14532d;
}
#gtms-mining-page .s8-declaration-text {
    font-size: 12.5px;
    color: #166534;
    line-height: 1.5;
}
#gtms-mining-page .s8-check-confirm .form-check-input {
    width: 18px;
    height: 18px;
    border: 2px solid #86efac;
    border-radius: 4px;
    margin-top: 2px;
}
#gtms-mining-page .s8-check-confirm .form-check-input:checked {
    background-color: #16a34a;
    border-color: #16a34a;
}
#gtms-mining-page .s8-check-confirm .form-check-label {
    font-size: 13px;
    color: #14532d;
    cursor: pointer;
    padding-left: 4px;
}

/* DARK MODE OVERRIDES FOR NEW HARMONIZED COMPONENTS */
body[data-theme-version="dark"] #gtms-mining-page .s1-time-tag,
body[data-theme-version="dark"] #gtms-mining-page .s6-team-pill,
body[data-theme-version="dark"] #gtms-mining-page .s7-ledger-pill {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-edit {
    background: #1e293b;
    border-color: #334155;
    color: #93c5fd;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-edit:hover {
    background: #2563eb;
    border-color: #2563eb;
    color: #ffffff;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-summary-banner,
body[data-theme-version="dark"] #gtms-mining-page .s6-banner-card,
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-card {
    background: #1e293b;
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-summary-text {
    color: #cbd5e1;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-summary-pct {
    color: #60a5fa;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-summary-progress {
    background: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-folder-card {
    background: #1e293b;
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-folder-no,
body[data-theme-version="dark"] #gtms-mining-page .s4-chip {
    background: #0f172a;
    border-color: #334155;
    color: #94a3b8;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-action-text {
    color: #60a5fa;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-progress-wrap {
    background: #0f172a;
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-count-label {
    color: #94a3b8;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-count-num {
    color: #f8fafc;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-card-progress {
    background: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-card-footer {
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-badge-pending {
    background: #0f172a;
    color: #94a3b8;
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-badge-progress {
    background: rgba(37, 99, 235, 0.15);
    color: #60a5fa;
    border-color: rgba(37, 99, 235, 0.3);
}
body[data-theme-version="dark"] #gtms-mining-page .s4-badge-complete {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
}
body[data-theme-version="dark"] #gtms-mining-page .s4-folder-card.is-complete {
    border-color: #10b981;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-folder-card.is-progress {
    border-color: #3b82f6;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-notice-card,
body[data-theme-version="dark"] #gtms-mining-page .s4-notice-panel {
    background: #18191a !important;
    border: 1px solid #2e3440 !important;
    border-left: 4px solid #3b82f6 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-notice-icon-wrap {
    background: #1e293b !important;
    border-color: #2563eb !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-notice-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-notice-desc {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-rule-pill {
    background: #1e293b !important;
    border-color: #3b82f6 !important;
    color: #93c5fd !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-btn-proceed {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s4-btn-proceed:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
}

/* STEP 6: DARK MODE OVERRIDES */
body[data-theme-version="dark"] #gtms-mining-page .s5-summary-banner {
    background: #18191a !important;
    border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-summary-badge {
    background: #2563eb !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-summary-label {
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-counter-text {
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-counter-sub {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-summary-progress {
    background-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-summary-bar {
    background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 100%) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-folder-heading-wrap {
    border-bottom-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-folder-icon-box {
    background: #1e293b !important;
    border-color: #2563eb !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-folder-title {
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-folder-subtitle {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-btn-add-doc {
    background: #18191a !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-btn-add-doc:hover {
    background: #2563eb !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-folder-pill {
    background: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .checklist-row {
    background: #18191a !important;
    border-color: #2b2d30 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .checklist-row:hover {
    border-color: #3e4451 !important;
    background: #1c1d1f !important;
}
body[data-theme-version="dark"] #gtms-mining-page .checklist-row.up {
    border-color: #2b2d30 !important;
    background: #18191a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .ci-icon {
    background: #25282a !important;
    color: #94a3b8 !important;
    border-color: #3b4252 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .checklist-row.up .ci-icon {
    background: #062c20 !important;
    color: #34d399 !important;
    border-color: #059669 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .ci-icon.ci-icon-carried {
    background: #172554 !important;
    color: #60a5fa !important;
    border-color: #1e3a8a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .ci-name {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .ci-meta {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .doc-req-select.border-danger-subtle {
    background-color: #450a0a !important;
    color: #fca5a5 !important;
    border-color: #7f1d1d !important;
}
body[data-theme-version="dark"] #gtms-mining-page .doc-req-select.border-secondary-subtle {
    background-color: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-badge-attached {
    background: #062c20 !important;
    color: #34d399 !important;
    border-color: #059669 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-badge-carried {
    background: #172554 !important;
    color: #60a5fa !important;
    border-color: #1e3a8a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-btn-upload {
    background: #18191a !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-btn-upload:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-notice-card {
    background: #18191a !important;
    border: 1px solid #2e3440 !important;
    border-left: 4px solid #3b82f6 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-notice-icon-wrap {
    background: #1e293b !important;
    border-color: #2563eb !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-notice-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-notice-desc {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s5-rule-pill {
    background: #1e293b !important;
    border-color: #3b82f6 !important;
    color: #93c5fd !important;
}
body[data-theme-version="dark"] .s5-modal-header {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
    border-bottom: 1px solid #334155 !important;
}
body[data-theme-version="dark"] .s5-modal-content {
    background-color: #1a1b1d !important;
    border: 1px solid #2e3440 !important;
}
body[data-theme-version="dark"] .s5-modal-label {
    color: #e2e8f0 !important;
}
body[data-theme-version="dark"] .s5-field-hint {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] .s5-modal-footer {
    background: #18191a !important;
    border-top-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #modalAddCustomMiningDoc .form-control,
body[data-theme-version="dark"] #modalAddCustomMiningDoc .form-select {
    background: #161717 !important;
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #modalAddCustomMiningDoc #mining_custom_folder_name {
    background: #1e293b !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s6-banner-title,
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-title {
    color: #f1f5f9;
}
body[data-theme-version="dark"] #gtms-mining-page .s6-tip-box {
    background: rgba(245, 158, 11, 0.08);
    border-color: rgba(245, 158, 11, 0.25);
    color: #fde68a;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-assessment-wrapper {
    background: #1e293b;
    border-color: #334155;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-feedback-title {
    color: #f1f5f9;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-tip-box {
    background: rgba(59, 130, 246, 0.08);
    border-color: rgba(59, 130, 246, 0.25);
    color: #bfdbfe;
}
body[data-theme-version="dark"] #gtms-mining-page .step-progress::before {
    background: #2a3d66 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .sp-step .circ {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .sp-step.active .circ {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .sp-step.done .circ {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .sp-step.active .sp-label {
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-section-card {
    background: #181c26 !important;
    border-color: #2a3d66 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-section-header {
    color: #f1f5f9 !important;
    border-color: #2a3d66 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-section-title {
    color: #93c5fd !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-label {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-opt-tag {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-hint {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-title {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-subtitle {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-time-tag {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-card {
    background: #181c26 !important;
    border-color: #2a3d66 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-label {
    color: #93c5fd !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-desc {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-input-group .s1-lookup-addon {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-input-group .s1-lookup-field {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-lookup-input-group .s1-btn-fetch {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-addon {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-input {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-textarea {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-textarea:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-textarea::placeholder {
    color: #64748b !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-input-group:focus-within .s1-addon {
    border-color: #60a5fa !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-input-group:focus-within .s1-input {
    border-color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-btn-cancel {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
}
/* NOTICE CARD - DARK */
body[data-theme-version="dark"] #gtms-mining-page .s1-notice-card {
    background: #172033 !important;
    border-color: #223761 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-notice-icon-box {
    background: #1e293b !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-notice-icon {
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s1-notice-text {
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s-btn-draft {
    background: transparent !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s-btn-draft:hover {
    background: #1e3a8a !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s-btn-submit {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
}
/* -------------------------------------------------------------------------
   STEP 8 (FINANCIAL SETTLEMENT) DARK MODE PARITY
   ------------------------------------------------------------------------- */
body[data-theme-version="dark"] #gtms-mining-page .s7-ledger-pill {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-icon-badge {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-desc {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-banner-status-pill {
    background: #38bdf8 !important;
    color: #0f172a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-card {
    border-color: #334155 !important;
    background: #0f172a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-contract {
    background: #0f172a !important;
    border-left: 4px solid #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-contract .text-navy {
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-icon-contract {
    background: #1e293b !important;
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-paid {
    background: #0f172a !important;
    border-left: 4px solid #10b981 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-icon-paid {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-pending {
    background: #0f172a !important;
    border-left: 4px solid #f97316 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-icon-pending {
    background: rgba(249, 115, 22, 0.15) !important;
    color: #fb923c !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-label {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-value {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-kpi-subtext {
    color: #64748b !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-form-section {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-form-section-header {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-form-section-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-badge-status-tag {
    background: #334155 !important;
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-label {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-auto-tag {
    background: #334155 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-input-group {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-input-group:focus-within {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-group-readonly {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-input-addon {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-addon-navy {
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-addon-success {
    color: #34d399 !important;
    background: rgba(16, 185, 129, 0.15) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-addon-danger {
    color: #f87171 !important;
    background: rgba(239, 68, 68, 0.15) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-input {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-input-readonly {
    color: #f87171 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-field-hint {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-preset-btn {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-preset-btn:hover {
    background: #38bdf8 !important;
    border-color: #38bdf8 !important;
    color: #0f172a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-select,
body[data-theme-version="dark"] #gtms-mining-page .s7-notes-input {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-feedback-box {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-feedback-sub {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-tip-box {
    background: rgba(245, 158, 11, 0.08) !important;
    border-color: rgba(245, 158, 11, 0.25) !important;
    color: #fde68a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s7-tip-content {
    color: #fde68a !important;
}

/* -------------------------------------------------------------------------
   STEP 9 (DOSSIER SCRUTINY) DARK MODE PARITY
   ------------------------------------------------------------------------- */
body[data-theme-version="dark"] #gtms-mining-page .s8-scrutiny-pill {
    background: rgba(56, 189, 248, 0.15) !important;
    color: #38bdf8 !important;
    border-color: rgba(56, 189, 248, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-banner-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-banner-icon-badge {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-banner-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-banner-desc {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-banner-status-pill {
    background: #38bdf8 !important;
    color: #0f172a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-card {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-val {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-label {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-sub {
    color: #64748b !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-icon-blue {
    background: rgba(2, 132, 199, 0.18) !important;
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-icon-emerald {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-icon-amber {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #fbbf24 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-kpi-icon-purple {
    background: rgba(147, 51, 234, 0.18) !important;
    color: #c084fc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-chip-blue {
    background: rgba(2, 132, 199, 0.18) !important;
    color: #38bdf8 !important;
    border-color: rgba(56, 189, 248, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-chip-emerald {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34d399 !important;
    border-color: rgba(52, 211, 153, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-chip-amber {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #fbbf24 !important;
    border-color: rgba(251, 191, 36, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-chip-purple {
    background: rgba(147, 51, 234, 0.18) !important;
    color: #c084fc !important;
    border-color: rgba(192, 132, 252, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-section-panel {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-panel-header {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-panel-title {
    color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-panel-step-badge {
    background: #334155 !important;
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-edit {
    background: #1e293b !important;
    border-color: #475569 !important;
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-edit:hover {
    background: #334155 !important;
    border-color: #38bdf8 !important;
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-data-tile {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-data-tile:hover {
    border-color: #475569 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-tile-label {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-tile-value {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-table {
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-table thead {
    background: #0f172a !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-table th,
body[data-theme-version="dark"] #gtms-mining-page .s8-table td {
    border-color: #334155 !important;
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-role-badge {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-avatar-icon {
    background: rgba(2, 132, 199, 0.2) !important;
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-empty-notice {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-ledger-card {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-ledger-sub {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-folder-card {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-folder-card:hover {
    border-color: #475569 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-folder-icon-wrap {
    background: #1e293b !important;
    color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-folder-title {
    color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-folder-meta {
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-registry {
    background: #0f172a !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-registry:hover {
    background: rgba(59, 130, 246, 0.15) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-registry-wrapper {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-doc-attached {
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(52, 211, 153, 0.35) !important;
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-doc-pending {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-action-open {
    background: #0f172a !important;
    color: #38bdf8 !important;
    border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-btn-action-open:hover {
    background: #38bdf8 !important;
    color: #0f172a !important;
    border-color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-declaration-card {
    background: rgba(16, 185, 129, 0.08) !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-declaration-icon-badge {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-declaration-title {
    color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-declaration-text {
    color: #a7f3d0 !important;
}
body[data-theme-version="dark"] #gtms-mining-page .s8-check-confirm .form-check-label {
    color: #f1f5f9 !important;
}
</style>

<!-- ========================================================== -->
<!-- JAVASCRIPT CONTROLLER FOR MINING INTAKE WIZARD             -->
<!-- ========================================================== -->
<script>
function initMiningWizard() {
    var initialStep = {{ (int)($prefillData['current_step'] ?? 1) }};
    try {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('step')) {
            var parsedStep = parseInt(urlParams.get('step'), 10);
            if (!isNaN(parsedStep) && parsedStep >= 1 && parsedStep <= 9) {
                initialStep = parsedStep;
            }
        }
    } catch (e) {}

    var currentStep = Math.max(0, Math.min(8, initialStep - 1));
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
            '<div class="alert alert-success py-2 px-3 mb-0 small rounded-3">' +
            '<i class="bi bi-check-circle-fill me-1"></i> Customer <strong>' + (c.company_name || c.customer_name) + '</strong> (' + (c.mimas_no || 'ID: '+c.id) + ') retrieved and autofilled!' +
            '</div>'
        ).slideDown();

        $('.auto-filled-field').addClass('field-autofilled');
        setTimeout(function() {
            $('.auto-filled-field').removeClass('field-autofilled');
        }, 2000);
    }

    function performMimasLookup() {
        var searchVal = $('#mimas_search_input').val().trim();
        if (!searchVal) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Validation Required', text: 'Please enter or select a Customer Unique ID to lookup.', confirmButtonColor: '#0F1E4D' });
            } else {
                alert('Please enter or select a Customer Unique ID to lookup.');
            }
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
        $('#btn_lookup_mimas').prop('disabled', true).html('<i class="bi bi-arrow-repeat fa-spin me-1"></i> Searching...');
        $('#mimas_feedback_box').hide();

        $.ajax({
            url: '/customers/lookup-mimas/' + encodeURIComponent(searchVal),
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#btn_lookup_mimas').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Fetch Details');
                if (res.status === 1 && res.data) {
                    populateCustomerFields(res.data);
                } else {
                    $('#mimas_feedback_box').html(
                        '<div class="alert alert-warning py-2 px-3 mb-0 small rounded-3"><i class="bi bi-exclamation-circle-fill me-1"></i> No matching registered customer found. You can enter details manually below.</div>'
                    ).slideDown();
                }
            },
            error: function() {
                $('#btn_lookup_mimas').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1"></i> Fetch Details');
                $('#mimas_feedback_box').html(
                    '<div class="alert alert-info py-2 px-3 mb-0 small rounded-3"><i class="bi bi-info-circle-fill me-1"></i> Customer not found in registry. Fill in details manually to create a new customer record.</div>'
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
            if (stepNode2) stepNode2.style.display = 'flex';
            if (connMineral) connMineral.style.display = 'block';
            var cDistrict = document.getElementById('circle_district'); if (cDistrict) cDistrict.textContent = '4';
            var cFolders = document.getElementById('circle_folders'); if (cFolders) cFolders.textContent = '5';
            var cUpload = document.getElementById('circle_upload'); if (cUpload) cUpload.textContent = '6';
            var cHandlers = document.getElementById('circle_handlers'); if (cHandlers) cHandlers.textContent = '7';
            var cPayment = document.getElementById('circle_payment'); if (cPayment) cPayment.textContent = '8';
            var cPreview = document.getElementById('circle_preview'); if (cPreview) cPreview.textContent = '9';
            $('.lbl-step-num-handlers').text('7');
            $('.lbl-step-num-payment').text('8');
            $('.lbl-step-num-preview').text('9');
        } else {
            if (stepNode2) stepNode2.style.display = 'none';
            if (connMineral) connMineral.style.display = 'none';
            var cDistrict = document.getElementById('circle_district'); if (cDistrict) cDistrict.textContent = '3';
            var cFolders = document.getElementById('circle_folders'); if (cFolders) cFolders.textContent = '4';
            var cUpload = document.getElementById('circle_upload'); if (cUpload) cUpload.textContent = '5';
            var cHandlers = document.getElementById('circle_handlers'); if (cHandlers) cHandlers.textContent = '6';
            var cPayment = document.getElementById('circle_payment'); if (cPayment) cPayment.textContent = '7';
            var cPreview = document.getElementById('circle_preview'); if (cPreview) cPreview.textContent = '8';
            $('.lbl-step-num-handlers').text('6');
            $('.lbl-step-num-payment').text('7');
            $('.lbl-step-num-preview').text('8');
        }

        for (var i = 0; i <= 8; i++) {
            var node = document.getElementById('stepNode_' + i);
            if (node) {
                node.classList.remove('active', 'done');
                var numSpan = node.querySelector('.step-num');
                var checkIcon = node.querySelector('.step-check');
                if (i < step) {
                    node.classList.add('done');
                    if (numSpan) numSpan.classList.add('d-none');
                    if (checkIcon) checkIcon.classList.remove('d-none');
                } else {
                    if (numSpan) numSpan.classList.remove('d-none');
                    if (checkIcon) checkIcon.classList.add('d-none');
                    if (i === step) {
                        node.classList.add('active');
                    }
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

    document.querySelectorAll('input[name="mineral_ids[]"]').forEach(function(cb) {
        cb.addEventListener('change', toggleOtherMineralBox);
    });
    toggleOtherMineralBox();

    // Toggle "Other Nature of Work" input box based on whether Others is checked
    function toggleOtherNatureOfWorkBox() {
        var selectedNow = document.querySelector('input[name="nature_of_work_id"]:checked');
        var otherBox = document.getElementById('other_nature_of_work_box_mining');
        var otherInput = document.getElementById('field_other_nature_of_work_name_mining');
        if (selectedNow && otherBox) {
            var name = (selectedNow.getAttribute('data-name') || '').toLowerCase().trim();
            if (name === 'others' || selectedNow.value === '8') {
                otherBox.classList.remove('d-none');
                if (otherInput) otherInput.setAttribute('required', 'required');
            } else {
                otherBox.classList.add('d-none');
                if (otherInput) { otherInput.removeAttribute('required'); }
            }
        }
    }

    document.querySelectorAll('input[name="nature_of_work_id"]').forEach(function(rb) {
        rb.addEventListener('change', toggleOtherNatureOfWorkBox);
    });
    toggleOtherNatureOfWorkBox();

    function showStep(step) {
        var isMP = isMiningPlanSelected();
        if (!isMP && step === 2) {
            step = 3;
        }
        currentStep = step;

        for (var i = 0; i <= 8; i++) {
            var p = document.getElementById('pane_' + i);
            if (p) p.classList.add('d-none');
        }

        var activePane = document.getElementById('pane_' + step);
        if (activePane) activePane.classList.remove('d-none');

        updateStepperUI(step);

        btnPrev.style.display = step === 0 ? 'none' : 'inline-flex';
        var btnCancel = document.getElementById('btnCancel');
        if (btnCancel) {
            btnCancel.style.display = step === 0 ? 'inline-flex' : 'none';
        }
        btnNext.style.display = step === 8 ? 'none' : 'inline-flex';
        btnSubmit.style.display = step === 8 ? 'inline-flex' : 'none';

        if (step === 8) {
            var ack = document.getElementById('confirm_mining_scrutiny_ack');
            btnSubmit.disabled = ack ? !ack.checked : false;
        }

        var fieldCurrentStep = document.getElementById('field_current_step');
        if (fieldCurrentStep) {
            fieldCurrentStep.value = step + 1;
        }

        // Synchronize browser URL parameter (?step=X) without triggering full page reload
        try {
            var currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('step', step + 1);
            window.history.replaceState({ step: step + 1 }, '', currentUrl.toString());
        } catch (e) {}

        // Ensure step components are hydrated
        if (step === 4 && typeof populateFolderPreview === 'function') {
            populateFolderPreview();
        } else if (step === 5 && typeof populateDocumentChecklist === 'function') {
            populateDocumentChecklist();
        } else if (step === 7 && typeof calcMiningPayment === 'function') {
            calcMiningPayment();
        } else if (step === 8 && typeof populateSummary === 'function') {
            populateSummary();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function autoSaveStepData(targetStepNum, callback) {
        var form = document.getElementById('miningWizardForm');
        if (!form) {
            if (typeof callback === 'function') callback();
            return;
        }

        var formData = new FormData(form);
        formData.set('current_step', targetStepNum);

        $.ajax({
            url: "{{ route('newapplication.autosave') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res && res.status === 1) {
                    if (res.mining_app_id) {
                        var mIdInput = document.getElementById('field_mining_app_id');
                        if (mIdInput) mIdInput.value = res.mining_app_id;
                        try {
                            var u = new URL(window.location.href);
                            if (!u.searchParams.has('resume')) {
                                u.searchParams.set('resume', res.mining_app_id);
                                window.history.replaceState({ step: targetStepNum }, '', u.toString());
                            }
                        } catch (err) {}
                    }
                    if (res.common_id) {
                        var cIdInput = document.getElementById('field_common_id');
                        if (cIdInput && !cIdInput.value) cIdInput.value = res.common_id;
                    }
                }
                if (typeof callback === 'function') callback(res);
            },
            error: function(xhr) {
                console.warn('Auto-save step notification:', xhr);
                if (typeof callback === 'function') callback(null);
            }
        });
    }

    function validateCurrentStep(step, onProceedAnyway) {
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
            var nowName = (now.getAttribute('data-name') || '').toLowerCase().trim();
            if (nowName === 'others' || now.value === '8') {
                var otherNowInput = document.getElementById('field_other_nature_of_work_name_mining');
                if (!otherNowInput || !otherNowInput.value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Required',
                        text: 'Please specify the custom Nature of Work scope.',
                        confirmButtonColor: '#0F1E4D'
                    });
                    if (otherNowInput) otherNowInput.focus();
                    return false;
                }
            }
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
        if (step === 5) {
            var nowId = getActiveNatureOfWorkId();
            var activeGroup = document.getElementById('doc_group_now_' + nowId);
            var missingMandatory = [];

            if (activeGroup) {
                activeGroup.querySelectorAll('.checklist-row[data-mandatory="1"]').forEach(function(row) {
                    var isUp = row.classList.contains('up');
                    var fileInp = row.querySelector('.doc-file-input');
                    if (fileInp && ((fileInp.files && fileInp.files.length > 0) || fileInp.getAttribute('data-has-existing') === '1')) {
                        isUp = true;
                    }
                    if (!isUp) {
                        var docNameEl = row.querySelector('.ci-name');
                        var docName = docNameEl ? docNameEl.cloneNode(true) : null;
                        if (docName) {
                            docName.querySelectorAll('.badge').forEach(function(b) { b.remove(); });
                        }
                        var cleanName = docName ? docName.textContent.replace(/^\d+[\.\s-]+/, '').trim() : 'Mandatory Statutory Document';
                        missingMandatory.push(cleanName);
                    }
                });
            }

            if (missingMandatory.length > 0) {
                var missingHtml = '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> ' + missingMandatory.length + ' Mandatory Document(s) Not Uploaded:</p><ul class="mb-2 ps-3 text-muted" style="max-height:160px; overflow-y:auto;">' +
                    missingMandatory.map(function(name) { return '<li>' + escapeHtml(name) + '</li>'; }).join('') +
                    '</ul><p class="text-muted small mb-0">Statutory guidelines require these documents to be attached. Do you want to stay and upload them now, or proceed with draft?</p></div>';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Mandatory Documents Missing!',
                        html: missingHtml,
                        showCancelButton: true,
                        confirmButtonColor: '#0F1E4D',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="bi bi-arrow-right-circle me-1"></i> Proceed Anyway (Save Draft)',
                        cancelButtonText: '<i class="bi bi-upload me-1"></i> Stay &amp; Upload'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            if (typeof onProceedAnyway === 'function') {
                                onProceedAnyway();
                            } else {
                                currentStep = 6;
                                showStep(currentStep);
                                autoSaveStepData(currentStep + 1);
                            }
                        }
                    });
                } else {
                    if (confirm('Warning: ' + missingMandatory.length + ' mandatory documents are missing. Proceed anyway?')) {
                        if (typeof onProceedAnyway === 'function') {
                            onProceedAnyway();
                        } else {
                            currentStep = 6;
                            showStep(currentStep);
                            autoSaveStepData(currentStep + 1);
                        }
                    }
                }
                return false;
            }
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
        var dispNow = document.getElementById('disp_selected_now');
        if (dispNow) dispNow.textContent = nowName;

        var counts = folderCounts[nowId] || {};
        var flCount = counts['Field Log'] || 0;
        var docCount = counts['Documents'] || 0;
        var phCount = counts['Site Photos'] || 0;
        var repCount = counts['Report'] || 0;
        var planCount = counts['Plan'] || 0;
        var othCount = counts['Others'] || 0;

        var fcFieldlog = document.getElementById('fc_fieldlog');
        if (fcFieldlog) fcFieldlog.textContent = flCount > 0 ? flCount + ' document types' : 'Standard Log Checklist';
        var fcDocs = document.getElementById('fc_documents');
        if (fcDocs) fcDocs.textContent = docCount > 0 ? docCount + ' statutory document types' : 'Statutory Checklist';
        var fcPhotos = document.getElementById('fc_photos');
        if (fcPhotos) fcPhotos.textContent = phCount > 0 ? phCount + ' photo types' : 'Quarry Site Photographs';

        var cardReport = document.getElementById('card_report');
        var fcReport = document.getElementById('fc_report');
        if (cardReport && fcReport) {
            if (repCount > 0) {
                cardReport.style.opacity = '1';
                fcReport.textContent = repCount + ' report items';
            } else {
                cardReport.style.opacity = '0.7';
                fcReport.textContent = 'Not required for this work';
            }
        }

        var cardPlan = document.getElementById('card_plan');
        var fcPlan = document.getElementById('fc_plan');
        if (cardPlan && fcPlan) {
            if (planCount > 0) {
                cardPlan.style.opacity = '1';
                fcPlan.textContent = planCount + ' plan drawings & calculations';
            } else {
                cardPlan.style.opacity = '0.7';
                fcPlan.textContent = 'Not required for this work';
            }
        }

        var cardOthers = document.getElementById('card_others');
        var fcOthers = document.getElementById('fc_others');
        if (cardOthers && fcOthers) {
            if (othCount > 0) {
                cardOthers.style.opacity = '1';
                fcOthers.textContent = othCount + ' data / annexure items';
            } else {
                cardOthers.style.opacity = '0.7';
                fcOthers.textContent = 'Optional / As applicable';
            }
        }

        // Calculate Dossier Readiness progress bar & folder readiness
        var activeGroup = document.getElementById('doc_group_now_' + nowId);
        var totalUploaded = 0;
        var totalRequired = 0;
        if (activeGroup) {
            var allInputs = activeGroup.querySelectorAll('.doc-file-input');
            totalRequired = allInputs.length;
            allInputs.forEach(function(inp) {
                if ((inp.files && inp.files.length > 0) || inp.getAttribute('data-has-existing') === '1') {
                    totalUploaded++;
                }
            });
        }
        var overallPct = totalRequired > 0 ? Math.round((totalUploaded / totalRequired) * 100) : 0;
        var elUp = document.getElementById('mining_dossier_uploaded_count');
        var elTot = document.getElementById('mining_dossier_total_count');
        var elPct = document.getElementById('mining_dossier_pct');
        var elBar = document.getElementById('mining_dossier_progress_bar');
        var elKpiDocs = document.getElementById('kpi_total_docs');
        if (elUp) elUp.textContent = totalUploaded;
        if (elTot) elTot.textContent = totalRequired;
        if (elKpiDocs) elKpiDocs.textContent = totalRequired;
        if (elPct) elPct.textContent = overallPct + '% Complete';
        if (elBar) {
            elBar.style.width = overallPct + '%';
            elBar.setAttribute('aria-valuenow', overallPct);
        }

        // Folder mapping for per-card dynamic stats
        var folderMapping = [
            { id: 7, card: 'card_f1', upId: 'f1_up_count', totId: 'f1_tot_count', barId: 'f1_progress_bar', badgeId: 'badge_f1_status' },
            { id: 8, card: 'card_f2', upId: 'f2_up_count', totId: 'f2_tot_count', barId: 'f2_progress_bar', badgeId: 'badge_f2_status' },
            { id: 9, card: 'card_f3', upId: 'f3_up_count', totId: 'f3_tot_count', barId: 'f3_progress_bar', badgeId: 'badge_f3_status' },
            { id: 10, card: 'card_report', upId: 'f4_up_count', totId: 'f4_tot_count', barId: 'f4_progress_bar', badgeId: 'badge_f4_status' },
            { id: 11, card: 'card_plan', upId: 'f5_up_count', totId: 'f5_tot_count', barId: 'f5_progress_bar', badgeId: 'badge_f5_status' },
            { id: 12, card: 'card_others', upId: 'f6_up_count', totId: 'f6_tot_count', barId: 'f6_progress_bar', badgeId: 'badge_f6_status' }
        ];

        folderMapping.forEach(function(item) {
            var container = document.getElementById('folder_checklist_' + nowId + '_' + item.id);
            var cardEl = document.getElementById(item.card);
            var upEl = document.getElementById(item.upId);
            var totEl = document.getElementById(item.totId);
            var barEl = document.getElementById(item.barId);
            var badgeEl = document.getElementById(item.badgeId);

            var folderTotal = 0;
            var folderUploaded = 0;

            if (container) {
                var inputs = container.querySelectorAll('.doc-file-input');
                folderTotal = inputs.length;
                inputs.forEach(function(inp) {
                    if ((inp.files && inp.files.length > 0) || inp.getAttribute('data-has-existing') === '1') {
                        folderUploaded++;
                    }
                });
            }

            if (upEl) upEl.textContent = folderUploaded;
            if (totEl) totEl.textContent = folderTotal;

            var folderPct = folderTotal > 0 ? Math.round((folderUploaded / folderTotal) * 100) : (folderUploaded > 0 ? 100 : 0);
            if (barEl) {
                barEl.style.width = folderPct + '%';
                barEl.setAttribute('aria-valuenow', folderPct);
            }

            if (badgeEl) {
                if (folderTotal > 0 && folderUploaded >= folderTotal) {
                    badgeEl.className = 'badge s4-badge-complete';
                    badgeEl.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Complete';
                    if (cardEl) {
                        cardEl.classList.remove('is-progress');
                        cardEl.classList.add('is-complete');
                    }
                } else if (folderUploaded > 0) {
                    badgeEl.className = 'badge s4-badge-progress';
                    badgeEl.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>' + folderUploaded + '/' + folderTotal + ' Saved';
                    if (cardEl) {
                        cardEl.classList.remove('is-complete');
                        cardEl.classList.add('is-progress');
                    }
                } else if (folderTotal === 0) {
                    badgeEl.className = 'badge s4-badge-pending';
                    badgeEl.innerHTML = '<i class="bi bi-dash-circle me-1"></i>Optional';
                    if (cardEl) {
                        cardEl.classList.remove('is-complete', 'is-progress');
                    }
                } else {
                    badgeEl.className = 'badge s4-badge-pending';
                    badgeEl.innerHTML = '<i class="bi bi-circle me-1"></i>Pending';
                    if (cardEl) {
                        cardEl.classList.remove('is-complete', 'is-progress');
                    }
                }
            }
        });
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
                    pill.className = 'badge bg-success-subtle text-success border border-success-subtle folder-pill-counter s5-folder-pill';
                } else if (fUp > 0) {
                    pill.className = 'badge bg-primary-subtle text-primary border border-primary-subtle folder-pill-counter s5-folder-pill';
                } else {
                    pill.className = 'badge s5-folder-pill folder-pill-counter';
                }
            }
            var totalCountSpan = fBlock.querySelector('.folder-total-count');
            if (totalCountSpan) {
                totalCountSpan.textContent = fTotal;
            }
        });
    }

    $(document).on('click', '.btn-trigger-upload', function(e) {
        e.preventDefault();
        var fieldId = $(this).data('field-id');
        $('#file_field_' + fieldId).trigger('click');
    });

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
                iconBox.removeAttribute('style');
                iconBox.innerHTML = '<i class="bi bi-check2-circle ci-status-icon"></i>';
            }
            var meta = document.getElementById('meta_field_' + fieldId);
            if (meta) {
                meta.innerHTML = '<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ' + escapeHtml(file.name) + '</span> &middot; ' + formatFileSize(file.size);
            }
            var badge = document.getElementById('badge_field_' + fieldId);
            if (badge) {
                badge.className = 'badge s5-badge-attached';
                badge.innerHTML = '<i class="bi bi-check-lg me-1"></i>Attached';
            } else {
                badge = document.createElement('span');
                badge.id = 'badge_field_' + fieldId;
                badge.className = 'badge s5-badge-attached';
                badge.innerHTML = '<i class="bi bi-check-lg me-1"></i>Attached';
                var actionCol = row.querySelector('.ms-2.flex-shrink-0');
                if (actionCol) {
                    var btnUpload = actionCol.querySelector('.btn-trigger-upload');
                    actionCol.insertBefore(badge, btnUpload);
                }
            }
            var btn = row.querySelector('.btn-trigger-upload');
            if (btn) {
                btn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
                btn.className = 'btn btn-sm btn-outline-secondary s5-btn-action btn-trigger-upload';
                btn.title = 'Change file';
                var viewLink = row.querySelector('.btn-view-doc');
                var blobUrl = URL.createObjectURL(file);
                if (!viewLink) {
                    viewLink = document.createElement('a');
                    viewLink.className = 'btn btn-sm btn-outline-info s5-btn-action btn-view-doc me-1';
                    viewLink.target = '_blank';
                    viewLink.rel = 'noopener noreferrer';
                    viewLink.title = 'View attached document';
                    viewLink.innerHTML = '<i class="bi bi-eye"></i>';
                    btn.parentNode.insertBefore(viewLink, btn);
                }
                viewLink.href = blobUrl;
            }
            var removeBtn = row.querySelector('.btn-remove-file');
            if (removeBtn) {
                removeBtn.className = 'btn btn-sm btn-outline-danger s5-btn-action btn-remove-file';
                removeBtn.classList.remove('d-none');
            }
        }
        updateUploadProgress();
    });

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
                iconBox.removeAttribute('style');
                iconBox.innerHTML = '<i class="bi bi-file-earmark ci-status-icon"></i>';
            }
            var meta = document.getElementById('meta_field_' + fieldId);
            if (meta) {
                meta.textContent = 'Not uploaded · Max 25 MB';
            }
            var badge = document.getElementById('badge_field_' + fieldId);
            if (badge) {
                badge.remove();
            }
            var btn = row.querySelector('.btn-trigger-upload');
            if (btn) {
                btn.innerHTML = '<i class="bi bi-upload me-1"></i> Upload';
                btn.className = 'btn btn-sm s5-btn-upload btn-trigger-upload';
                btn.title = 'Upload file';
            }
            $(this).addClass('d-none');
        }
        updateUploadProgress();
    });

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
        $('#mining_custom_doc_status').val('0');
        $('#mining_custom_doc_file').val('');

        var modalEl = document.getElementById('modalAddCustomMiningDoc');
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });

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

        var rowHtml = '<div class="checklist-row up" id="row_custom_' + customId + '" data-custom-id="' + customId + '" data-mandatory="' + (isMandatory ? '1' : '0') + '">' +
            '<div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">' +
                '<div class="ci-icon ci-icon-carried">' +
                    '<i class="bi bi-check2-circle ci-status-icon"></i>' +
                '</div>' +
                '<div class="min-w-0">' +
                    '<div class="ci-name text-truncate">' +
                        '<span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>' +
                        escapeHtml(docName) +
                    '</div>' +
                    '<div class="ci-meta text-muted text-truncate" id="meta_custom_' + customId + '">' +
                        '<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ' + escapeHtml(file.name) + '</span> &middot; ' + dispSize +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="d-flex align-items-center gap-2 ms-2 flex-shrink-0">' +
                '<select class="form-select form-select-sm doc-req-select ' + (isMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light') + '" name="custom_docs[' + customId + '][requirement]" data-custom-id="' + customId + '" title="Set requirement status (Mandatory or Optional)">' +
                    '<option value="mandatory" ' + (isMandatory ? 'selected' : '') + '>Mandatory</option>' +
                    '<option value="optional" ' + (!isMandatory ? 'selected' : '') + '>Optional</option>' +
                '</select>' +
                '<span class="badge s5-badge-attached"><i class="bi bi-check-lg me-1"></i>Attached</span>' +
                '<a href="' + URL.createObjectURL(file) + '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info s5-btn-action btn-view-doc" title="View attached document">' +
                    '<i class="bi bi-eye"></i>' +
                '</a>' +
                '<button type="button" class="btn btn-sm btn-outline-danger s5-btn-action btn-remove-custom-row" data-custom-id="' + customId + '" title="Remove custom document">' +
                    '<i class="bi bi-trash3"></i>' +
                '</button>' +
                '<input type="hidden" name="custom_docs[' + customId + '][folder_id]" value="' + folderId + '">' +
                '<input type="hidden" name="custom_docs[' + customId + '][name]" value="' + escapeHtml(docName) + '">' +
                '<input type="hidden" name="custom_docs[' + customId + '][required]" class="custom-doc-required-input" value="' + (isMandatory ? '1' : '0') + '">' +
                '<input type="file" name="custom_doc_files[' + customId + ']" id="file_custom_' + customId + '" class="doc-file-input d-none" data-custom-id="' + customId + '">' +
            '</div>' +
        '</div>';

        $(container).append(rowHtml);

        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('file_custom_' + customId).files = dt.files;

        var modalEl = document.getElementById('modalAddCustomMiningDoc');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        updateUploadProgress();
        if (typeof toastr !== 'undefined') {
            toastr.success('Document "' + docName + '" attached to ' + folderName);
        }
    });

    $(document).on('change', '.doc-req-select', function() {
        var select = $(this);
        var val = select.val();
        var isMandatory = val === 'mandatory';
        var row = select.closest('.checklist-row');
        row.attr('data-mandatory', isMandatory ? '1' : '0');
        row.find('.custom-doc-required-input').val(isMandatory ? '1' : '0');

        if (isMandatory) {
            select.removeClass('border-secondary-subtle text-muted bg-light').addClass('border-danger-subtle text-danger bg-danger-subtle');
        } else {
            select.removeClass('border-danger-subtle text-danger bg-danger-subtle').addClass('border-secondary-subtle text-muted bg-light');
        }
    });

    $(document).on('click', '.btn-remove-custom-row', function(e) {
        e.preventDefault();
        var customId = $(this).data('custom-id');
        $('#row_custom_' + customId).remove();
        updateUploadProgress();
    });

    // ==========================================
    // 4. STEP 9: APPLICATION SUMMARY & REVIEW
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

        document.getElementById('prev_client').textContent = comp ? comp + (client ? ' (' + client + ')' : '') : (client || '-');
        document.getElementById('disp_rev_client_name').textContent = client || '-';
        document.getElementById('disp_rev_company_name').textContent = comp || '-';
        document.getElementById('disp_rev_address').textContent = document.getElementById('field_address').value || 'Address not entered';

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
        document.getElementById('disp_rev_mimas').textContent = mimasNo || 'Not specified';

        var distText = ds.options[ds.selectedIndex] ? ds.options[ds.selectedIndex].text : '-';
        document.getElementById('prev_district').textContent = distText;

        var nowName = selectedNow ? (selectedNow.getAttribute('data-name') || selectedNow.value) : '-';
        if (selectedNow && ((selectedNow.getAttribute('data-name') || '').toLowerCase().trim() === 'others' || selectedNow.value === '8')) {
            var customNow = document.getElementById('field_other_nature_of_work_name_mining')?.value?.trim();
            if (customNow) {
                nowName = 'Others (' + customNow + ')';
            }
        }
        document.getElementById('prev_nature').textContent = nowName;

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

        // Document upload summary count & detailed registry
        var nowId = getActiveNatureOfWorkId();
        var activeGroup = document.getElementById('doc_group_now_' + nowId);
        var attachedCount = 0;
        var totalDocs = 0;
        var folderCardsHtml = '';
        var registryRowsHtml = '';

        if (activeGroup) {
            var allInputs = activeGroup.querySelectorAll('.doc-file-input');
            totalDocs = allInputs.length;
            allInputs.forEach(function(inp) {
                if ((inp.files && inp.files.length > 0) || inp.getAttribute('data-has-existing') === '1') attachedCount++;
            });

            // Populate folder cards grid
            var folderBlocks = activeGroup.querySelectorAll('.folder-doc-block');
            folderBlocks.forEach(function(fBlock, fIdx) {
                var fHeader = fBlock.querySelector('.folder-doc-header');
                var fTitle = fHeader ? (fHeader.querySelector('.fw-bold.small.text-navy')?.textContent || 'Folder ' + (fIdx + 1)) : 'Folder ' + (fIdx + 1);
                fTitle = fTitle.replace(/^\d+[\.\s-]+/, '').trim();
                var fInputs = fBlock.querySelectorAll('.doc-file-input');
                var fTot = fInputs.length;
                var fUp = 0;
                fInputs.forEach(function(fi) {
                    if ((fi.files && fi.files.length > 0) || fi.getAttribute('data-has-existing') === '1') fUp++;
                });

                var badgeClass = 'bg-secondary text-white';
                var statusText = 'Pending';
                if (fUp === fTot && fTot > 0) {
                    badgeClass = 'bg-success text-white';
                    statusText = 'Complete';
                } else if (fUp > 0) {
                    badgeClass = 'bg-primary text-white';
                    statusText = 'In Progress';
                }

                folderCardsHtml += '<div class="col-md-4 col-sm-6">' +
                    '<div class="s8-folder-card d-flex align-items-center justify-content-between p-2.5">' +
                        '<div class="d-flex align-items-center gap-2.5 text-truncate">' +
                            '<div class="s8-folder-icon-wrap"><i class="bi bi-folder2-open text-primary"></i></div>' +
                            '<div class="text-truncate">' +
                                '<div class="s8-folder-title text-truncate">' + escapeHtml(fTitle) + '</div>' +
                                '<div class="s8-folder-meta text-muted">' + fUp + ' of ' + fTot + ' attached</div>' +
                            '</div>' +
                        '</div>' +
                        '<span class="badge ' + badgeClass + ' rounded-pill px-2 py-1" style="font-size:10.5px;">' + statusText + '</span>' +
                    '</div>' +
                '</div>';
            });

            // Populate detailed registry table
            var checkRows = activeGroup.querySelectorAll('.checklist-row');
            checkRows.forEach(function(row, rIdx) {
                var parentBlock = row.closest('.folder-doc-block');
                var parentHeader = parentBlock ? parentBlock.querySelector('.folder-doc-header') : null;
                var fTitle = parentHeader ? (parentHeader.querySelector('.fw-bold.small.text-navy')?.textContent || 'General') : 'General';
                fTitle = fTitle.replace(/^\d+[\.\s-]+/, '').trim();

                var docNameEl = row.querySelector('.ci-name');
                var docName = docNameEl ? docNameEl.cloneNode(true) : null;
                if (docName) {
                    docName.querySelectorAll('.badge').forEach(function(b) { b.remove(); });
                }
                var cleanDocName = docName ? docName.textContent.replace(/^\d+[\.\s-]+/, '').trim() : 'Document #' + (rIdx + 1);

                var isMandatory = row.getAttribute('data-mandatory') === '1';
                var isAttached = row.classList.contains('up');
                var fileInp = row.querySelector('.doc-file-input');
                if (fileInp && ((fileInp.files && fileInp.files.length > 0) || fileInp.getAttribute('data-has-existing') === '1')) {
                    isAttached = true;
                }

                var viewLink = row.querySelector('.btn-view-doc');
                var viewUrl = viewLink ? viewLink.getAttribute('href') : '';

                registryRowsHtml += '<tr>' +
                    '<td class="text-center text-muted fw-bold">' + (rIdx + 1) + '</td>' +
                    '<td>' +
                        '<span class="badge s8-chip-blue py-1 px-2 fw-semibold">' +
                            '<i class="bi bi-folder2 text-primary me-1"></i>' + escapeHtml(fTitle) +
                        '</span>' +
                    '</td>' +
                    '<td>' +
                        '<div class="fw-semibold text-navy" style="font-size:13px;">' + escapeHtml(cleanDocName) + '</div>' +
                        (isMandatory ? '<span class="badge bg-danger-subtle text-danger border border-danger-subtle mt-0.5" style="font-size:9.5px;">Mandatory</span>' : '<span class="badge bg-light text-muted border mt-0.5" style="font-size:9.5px;">Optional</span>') +
                    '</td>' +
                    '<td>' +
                        (isAttached ?
                            '<div class="d-flex align-items-center gap-1.5 flex-wrap">' +
                                '<span class="badge s8-doc-attached py-1 px-2.5">' +
                                    '<i class="bi bi-check-circle-fill me-1"></i>Attached' +
                                '</span>' +
                                (viewUrl ? '<a href="' + viewUrl + '" target="_blank" rel="noopener noreferrer" class="btn btn-xs s8-btn-action-open" title="View attached document"><i class="bi bi-box-arrow-up-right me-1"></i>View</a>' : '') +
                            '</div>' :
                            '<span class="badge s8-doc-pending py-1 px-2">' +
                                '<i class="bi bi-dash-circle me-1"></i>Pending Upload' +
                            '</span>') +
                    '</td>' +
                '</tr>';
            });
        }

        var prevUpEl = document.getElementById('prev_uploaded_docs');
        if (prevUpEl) prevUpEl.textContent = attachedCount + ' of ' + totalDocs + ' Files Attached';
        
        var prevMetaEl = document.getElementById('prev_docs_meta');
        if (prevMetaEl) prevMetaEl.textContent = (totalDocs - attachedCount) + ' files can be attached later in dossier';

        var prevStatCount = document.getElementById('prev_statutory_docs_count');
        if (prevStatCount) prevStatCount.textContent = attachedCount + ' of ' + totalDocs + ' Items Attached';

        var prevStatBadge = document.getElementById('prev_statutory_badge');
        if (prevStatBadge) {
            if (attachedCount === totalDocs && totalDocs > 0) {
                prevStatBadge.className = 'badge bg-success ms-1';
                prevStatBadge.innerHTML = '<i class="bi bi-check2-circle me-1"></i> 100% Ready';
            } else if (attachedCount > 0) {
                prevStatBadge.className = 'badge bg-primary ms-1';
                prevStatBadge.innerHTML = '<i class="bi bi-file-earmark-check me-1"></i> In Progress';
            } else {
                prevStatBadge.className = 'badge bg-warning text-dark ms-1';
                prevStatBadge.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Pending';
            }
        }

        var fGrid = document.getElementById('prev_folder_cards_grid');
        if (fGrid) {
            fGrid.innerHTML = folderCardsHtml || '<div class="col-12 text-muted small fst-italic py-2"><i class="bi bi-info-circle me-1"></i> No folder structure found.</div>';
        }

        var regTbody = document.getElementById('prev_registry_tbody');
        if (regTbody) {
            regTbody.innerHTML = registryRowsHtml || '<tr><td colspan="4" class="text-center text-muted small fst-italic py-3">No statutory documents defined for this nature of work.</td></tr>';
        }

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
            var hHtml = '<table class="table s8-table mb-0">' +
                '<thead>' +
                '<tr><th style="width:40px;" class="text-center">#</th><th style="width:30%;">Personnel Name</th><th style="width:25%;">Role / Designation</th><th>Notes &amp; Responsibilities</th></tr>' +
                '</thead><tbody>';
            validHandlers.forEach(function(h, idx) {
                hHtml += '<tr>' +
                    '<td class="text-center text-muted fw-bold">' + (idx + 1) + '</td>' +
                    '<td class="fw-bold text-navy">' +
                        '<div class="d-flex align-items-center gap-2">' +
                            '<div class="s8-avatar-icon"><i class="bi bi-person-fill"></i></div>' +
                            '<span>' + escapeHtml(h.name) + '</span>' +
                        '</div>' +
                    '</td>' +
                    '<td><span class="badge s8-role-badge"><i class="bi bi-briefcase-fill me-1"></i>' + escapeHtml(h.role || 'Personnel') + '</span></td>' +
                    '<td class="text-muted small">' + escapeHtml(h.notes || 'No specific notes recorded') + '</td>' +
                '</tr>';
            });
            hHtml += '</tbody></table>';
            $('#prev_handlers_box').html(hHtml);
        } else {
            $('#prev_handlers_box').html('<div class="s8-empty-notice d-flex align-items-center gap-2"><i class="bi bi-info-circle-fill text-muted"></i><span>No dedicated handling personnel assigned yet. You can allocate project managers or surveyors in Step 7.</span></div>');
        }

        // Payment preview
        var pVal = parseFloat($('#mining_product_value').val()) || 0;
        var pPaid = parseFloat($('#mining_paid_amount').val()) || 0;
        var pPending = Math.max(0, pVal - pPaid);
        var pStat = $('#mining_payment_status').val() || 'pending';

        var formattedVal = '₹ ' + pVal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        var formattedPaid = '₹ ' + pPaid.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        var formattedPending = '₹ ' + pPending.toLocaleString('en-IN', { minimumFractionDigits: 2 });

        $('#prev_pay_value').text(formattedVal);
        $('#prev_pay_paid').text(formattedPaid);
        $('#prev_pay_paid_card').text(formattedPaid);
        $('#prev_pay_pending').text(formattedPending);
        $('#prev_pay_pending_card').text(formattedPending);

        var pBadge = '';
        if (pStat === 'paid') {
            pBadge = '<span class="badge bg-success text-white px-2 py-0.5 rounded-pill" style="font-size:10.5px;">Paid</span>';
        } else if (pStat === 'partial') {
            pBadge = '<span class="badge bg-warning text-dark px-2 py-0.5 rounded-pill" style="font-size:10.5px;">Partial</span>';
        } else {
            pBadge = '<span class="badge bg-danger text-white px-2 py-0.5 rounded-pill" style="font-size:10.5px;">Pending</span>';
        }
        $('#prev_pay_status').html(pBadge);
    }

    // ==========================================
    // 5. STEP 7 & 8: DYNAMIC HANDLERS & PAYMENT
    // ==========================================
    var miningHandlerIndex = {{ (!empty($prefillData['handlers']) && count($prefillData['handlers']) > 0) ? count($prefillData['handlers']) : 0 }};

    function addMiningHandlerRow(name, role, notes) {
        name = name || '';
        role = role || '';
        notes = notes || '';
        var idx = miningHandlerIndex++;
        var row = '<tr id="handler_row_' + idx + '">' +
            '<td class="text-center text-muted fw-bold handler-idx-col">1</td>' +
            '<td><input type="text" name="handlers[' + idx + '][name]" class="form-control form-control-sm mining-input handler-name-input" placeholder="e.g. Ramesh Kumar" value="' + escapeHtml(name) + '" required></td>' +
            '<td><input type="text" name="handlers[' + idx + '][role]" list="mining_role_suggestions" class="form-control form-control-sm mining-input handler-role-input" placeholder="e.g. Surveyor / Engineer" value="' + escapeHtml(role) + '" required></td>' +
            '<td><input type="text" name="handlers[' + idx + '][notes]" class="form-control form-control-sm mining-input" placeholder="e.g. Field inspection & DGPS logs" value="' + escapeHtml(notes) + '"></td>' +
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
        var count = $('#mining_handlers_tbody tr').length;
        $('#mining_handlers_tbody tr').each(function(i, tr) {
            $(tr).find('.handler-idx-col').text(i + 1);
        });
        var bannerBadge = document.getElementById('mining_handler_count_banner');
        if (bannerBadge) {
            bannerBadge.textContent = count + (count === 1 ? ' Member Allocated' : ' Members Allocated');
        }
    }

    $('#btn_add_mining_handler').on('click', function() {
        addMiningHandlerRow();
    });

    $(document).on('click', '.btn-remove-mining-handler', function() {
        $(this).closest('tr').remove();
        renumberMiningHandlers();
    });

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
            badgeHtml = '<span class="badge bg-success text-white px-2.5 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Paid (Settled)</span>';
        } else if (currentStatus === 'partial') {
            badgeHtml = '<span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill"><i class="bi bi-clock-history me-1"></i> Partial Payment</span>';
        } else {
            badgeHtml = '<span class="badge bg-danger text-white px-2.5 py-1 rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i> Pending Full Due</span>';
        }
        $('#disp_mining_status_badge').html(badgeHtml);

        // Update Step 8 banner status pill
        var bannerPill = document.getElementById('mining_banner_status_pill');
        if (bannerPill) {
            if (currentStatus === 'paid') {
                bannerPill.className = 'badge s7-banner-status-pill bg-success text-white';
                bannerPill.innerHTML = '<i class="bi bi-check-circle-fill me-1.5"></i> Fully Paid';
            } else if (currentStatus === 'partial') {
                bannerPill.className = 'badge s7-banner-status-pill bg-warning text-dark';
                bannerPill.innerHTML = '<i class="bi bi-clock-history me-1.5"></i> Part Payment';
            } else {
                bannerPill.className = 'badge s7-banner-status-pill bg-danger-subtle text-danger border border-danger-subtle';
                bannerPill.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment';
            }
        }

        // Update live settlement assessment feedback box
        var fbText = document.getElementById('mining_status_feedback_text');
        var fbBadge = document.getElementById('mining_status_feedback_badge');
        var fbIcon = document.getElementById('mining_status_feedback_icon');
        var fbBox = document.getElementById('mining_status_feedback_box');
        var isDark = document.body.getAttribute('data-theme-version') === 'dark';

        if (fbText && fbBadge && fbBox) {
            if (currentStatus === 'paid') {
                fbText.textContent = 'Account Fully Settled';
                fbBadge.textContent = 'Fully Paid';
                fbBadge.className = 'badge s7-feedback-badge px-3 py-2 rounded-pill bg-success text-white';
                if (fbIcon) fbIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
                if (isDark) {
                    fbBox.style.background = 'rgba(16, 185, 129, 0.12)';
                    fbBox.style.border = '1px solid rgba(16, 185, 129, 0.3)';
                } else {
                    fbBox.style.background = '#ecfdf5';
                    fbBox.style.border = '1px solid #a7f3d0';
                }
            } else if (currentStatus === 'partial') {
                fbText.textContent = 'Part Payment Received';
                fbBadge.textContent = 'Partial Payment';
                fbBadge.className = 'badge s7-feedback-badge px-3 py-2 rounded-pill bg-warning text-dark';
                if (fbIcon) fbIcon.innerHTML = '<i class="bi bi-clock-history text-warning"></i>';
                if (isDark) {
                    fbBox.style.background = 'rgba(245, 158, 11, 0.12)';
                    fbBox.style.border = '1px solid rgba(245, 158, 11, 0.3)';
                } else {
                    fbBox.style.background = '#fffbeb';
                    fbBox.style.border = '1px solid #fde68a';
                }
            } else {
                fbText.textContent = 'Full Payment Outstanding';
                fbBadge.textContent = 'Pending Payment';
                fbBadge.className = 'badge s7-feedback-badge px-3 py-2 rounded-pill bg-danger text-white';
                if (fbIcon) fbIcon.innerHTML = '<i class="bi bi-exclamation-circle-fill text-danger"></i>';
                if (isDark) {
                    fbBox.style.background = 'rgba(239, 68, 68, 0.12)';
                    fbBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
                } else {
                    fbBox.style.background = '#fef2f2';
                    fbBox.style.border = '1px solid #fecaca';
                }
            }
        }
    }

    $('#mining_product_value, #mining_paid_amount').on('input change', calcMiningPayment);
    $('#mining_payment_status').on('change', function() {
        var val = $(this).val();
        var badgeHtml = val === 'paid' ? '<span class="badge bg-success text-white px-2.5 py-1 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Paid (Settled)</span>' :
                       (val === 'partial' ? '<span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill"><i class="bi bi-clock-history me-1"></i> Partial Payment</span>' :
                                            '<span class="badge bg-danger text-white px-2.5 py-1 rounded-pill"><i class="bi bi-exclamation-circle-fill me-1"></i> Pending Full Due</span>');
        $('#disp_mining_status_badge').html(badgeHtml);
    });

    // Quick presets
    $('#btn_quick_full').on('click', function() {
        var val = parseFloat($('#mining_product_value').val()) || 0;
        $('#mining_paid_amount').val(val.toFixed(2)).trigger('input');
    });
    $('#btn_quick_half').on('click', function() {
        var val = parseFloat($('#mining_product_value').val()) || 0;
        $('#mining_paid_amount').val((val / 2).toFixed(2)).trigger('input');
    });
    $('#btn_quick_zero').on('click', function() {
        $('#mining_paid_amount').val('0.00').trigger('input');
    });

    calcMiningPayment();

    btnNext.addEventListener('click', function() {
        var isMP = isMiningPlanSelected();
        var nextStep = currentStep;
        if (currentStep === 1) {
            nextStep = isMP ? 2 : 3;
        } else if (currentStep === 2) {
            nextStep = 3;
        } else if (currentStep < 8) {
            nextStep++;
        }

        if (!validateCurrentStep(currentStep, function() {
            currentStep = nextStep;
            showStep(currentStep);
            autoSaveStepData(currentStep + 1);
        })) return;

        currentStep = nextStep;
        showStep(currentStep);
        autoSaveStepData(currentStep + 1);
    });

    btnPrev.addEventListener('click', function() {
        var isMP = isMiningPlanSelected();

        if (currentStep === 3) {
            currentStep = isMP ? 2 : 1;
        } else if (currentStep > 0) {
            currentStep--;
        }

        showStep(currentStep);
        autoSaveStepData(currentStep + 1);
    });

    // Allow user to click directly on steps in stepper
    $('#wizardStepper').on('click', '.sp-step', function() {
        var targetStepId = $(this).attr('id');
        if (!targetStepId) return;
        var targetIdx = parseInt(targetStepId.replace('stepNode_', ''), 10);
        if (isNaN(targetIdx)) return;

        var isMP = isMiningPlanSelected();
        var resolvedTarget = targetIdx;
        if (!isMP && resolvedTarget === 2) {
            resolvedTarget = 3;
        }

        // If advancing forward, validate current step first
        if (resolvedTarget > currentStep) {
            if (!validateCurrentStep(currentStep, function() {
                showStep(resolvedTarget);
                autoSaveStepData(resolvedTarget + 1);
            })) return;
        }

        showStep(resolvedTarget);
        autoSaveStepData(resolvedTarget + 1);
    });

    window.addEventListener('popstate', function() {
        try {
            var urlParams = new URLSearchParams(window.location.search);
            var s = parseInt(urlParams.get('step'), 10);
            if (!isNaN(s) && s >= 1 && s <= 9) {
                var targetIdx = s - 1;
                var isMP = isMiningPlanSelected();
                if (!isMP && targetIdx === 2) targetIdx = 3;
                showStep(targetIdx);
            }
        } catch (e) {}
    });

    // ==========================================
    // 6. ACTION DOCK, DRAFT SAVE & COMPLIANCE
    // ==========================================
    $('#confirm_mining_scrutiny_ack').on('change', function() {
        var isChecked = $(this).is(':checked');
        $('#btnSubmit').prop('disabled', !isChecked);
    });

    $('#miningWizardForm').on('submit', function(e) {
        var ack = document.getElementById('confirm_mining_scrutiny_ack');
        if (ack && !ack.checked) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Compliance Acknowledgment Required',
                    text: 'Please confirm the statutory declaration checkbox before submitting.',
                    confirmButtonColor: '#0F1E4D'
                });
            } else {
                alert('Please confirm the statutory declaration checkbox before submitting.');
            }
            ack.focus();
            return false;
        }
        $('#btnSubmit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Submitting Application...');
    });

    $('#btnSaveDraft').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Saving Draft...');

        autoSaveStepData(currentStep + 1, function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('Draft saved successfully. Returning to Mining Plans...');
            }
            setTimeout(function() {
                window.location.href = "{{ route('miningplan.index') }}";
            }, 300);
        });
    });

    // Quick-jump edit buttons in Step 9 Review
    $(document).on('click', '.s9-btn-edit', function(e) {
        e.preventDefault();
        var targetStep = parseInt($(this).data('target-step'), 10);
        if (!isNaN(targetStep) && targetStep >= 0 && targetStep <= 8) {
            var isMP = isMiningPlanSelected();
            if (!isMP && targetStep === 2) {
                targetStep = 3;
            }
            showStep(targetStep);
            autoSaveStepData(targetStep + 1);
        }
    });

    // Step 5 Folder Cards click & navigation to Step 6
    $(document).on('click', '.s4-folder-card, .btn-goto-upload-step', function(e) {
        if ($(e.target).is('input, button, select') && !$(e.target).hasClass('btn-goto-upload-step')) {
            return;
        }
        e.preventDefault();
        var targetFolder = $(this).data('target-folder') || $(this).data('folder') || $(this).closest('.s4-folder-card').data('folder');
        showStep(5);
        autoSaveStepData(6);

        if (targetFolder) {
            setTimeout(function() {
                var selectedNow = $('input[name="nature_of_work_id"]:checked').val() || '1';
                var $target = $('#folder_checklist_' + selectedNow + '_' + targetFolder);
                if ($target.length) {
                    var $block = $target.closest('.folder-doc-block');
                    if ($block.length) {
                        $('html, body').animate({
                            scrollTop: $block.offset().top - 90
                        }, 350);
                        $block.addClass('border-primary shadow-sm');
                        setTimeout(function() {
                            $block.removeClass('border-primary shadow-sm');
                        }, 1500);
                    }
                }
            }, 200);
        }
    });

    // Dynamic District preview badge update in Step 4
    function updateDistrictBadge() {
        var opt = $('#district_id option:selected');
        if (opt.length && opt.val()) {
            $('#s4_district_preview_badge').text(opt.text().trim());
        } else {
            $('#s4_district_preview_badge').text('District Office');
        }
    }
    $('#district_id').on('change', updateDistrictBadge);
    updateDistrictBadge();

    // Initialize to resolved active step (survives browser reload)
    var isMPInit = isMiningPlanSelected();
    if (!isMPInit && currentStep === 2) {
        currentStep = 3;
    }
    showStep(currentStep);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMiningWizard);
} else {
    initMiningWizard();
}
</script>

@endsection
