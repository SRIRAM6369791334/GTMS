@extends('layouts.app')
@section('title', 'New Environment Clearance Application')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<div class="content-body default-height" id="gtms-eviron-create-page">
  <div class="container-fluid">

    {{-- Breadcrumb & Top Bar --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-md-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">New Application (5-Step Wizard)</a></li>
        </ol>
      </div>
      <div class="col-md-6 text-end">
        <a href="{{ route('eviron.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fa fa-arrow-left me-1"></i> Back to Applications
        </a>
      </div>
    </div>

    @if (isset($errors) && $errors->any())
      <div class="alert alert-danger py-2 mb-4">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- ================= NATIVE GTMS 5-STEP PROGRESS BAR ================= --}}
    <div class="step-progress mb-4" id="eviron_stepper">
      <div class="sp-step active" data-step="1" id="step_indicator_1" style="cursor:pointer;" onclick="jumpToStep(1)">
        <div class="circ">1</div>
        <div class="sp-label">Category</div>
      </div>
      <div class="sp-step" data-step="2" id="step_indicator_2" style="cursor:pointer;" onclick="jumpToStep(2)">
        <div class="circ">2</div>
        <div class="sp-label">Applicant &amp; Site</div>
      </div>
      <div class="sp-step" data-step="3" id="step_indicator_3" style="cursor:pointer;" onclick="jumpToStep(3)">
        <div class="circ">3</div>
        <div class="sp-label">Documents</div>
      </div>
      <div class="sp-step" data-step="4" id="step_indicator_4" style="cursor:pointer;" onclick="jumpToStep(4)">
        <div class="circ">4</div>
        <div class="sp-label">Handling Team</div>
      </div>
      <div class="sp-step" data-step="5" id="step_indicator_5" style="cursor:pointer;" onclick="jumpToStep(5)">
        <div class="circ">5</div>
        <div class="sp-label">Payment &amp; Review</div>
      </div>
    </div>

    {{-- Multi-Step Intake Form with File Upload Support --}}
    <form method="POST" action="{{ route('eviron.store') }}" id="newEvironForm" enctype="multipart/form-data">
      @csrf

      {{-- ========================================================================= --}}
      {{-- STEP 1: CLEARANCE CATEGORY & STATUTORY FRAMEWORK                           --}}
      {{-- ========================================================================= --}}
      <div class="step-pane wizard-card" id="pane_step_1">
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow" style="color:#0F1E4D; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em;">
              <i class="fa fa-layer-group me-1 text-primary"></i> Step 1 of 5 &bull; Clearance Category &amp; Framework
            </div>
            <div class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
              <i class="fa fa-clock me-1 text-muted"></i> Est. ~1 min
            </div>
          </div>
          <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Select Environmental Clearance Category</h4>
          <div class="text-muted small">Choose between Category B1 (EIA Study Required) or Category B2 (Direct Environment Clearance)</div>
        </div>

        <div class="row g-3 mb-4">
          {{-- B1 Category Card --}}
          <div class="col-md-6">
            <div class="category-select-card h-100 p-3 rounded-3 border position-relative" id="card_cat_b1" style="cursor:pointer; transition: all 0.2s;">
              <div class="d-flex align-items-start gap-3">
                <input type="radio" name="category" value="B1" id="radio_cat_b1" class="form-check-input mt-1" {{ old('category', $category) === 'B1' ? 'checked' : '' }} required>
                <div class="flex-grow-1">
                  <label for="radio_cat_b1" class="d-flex align-items-center justify-content-between mb-1" style="cursor:pointer; width:100%;">
                    <span class="fw-bold fs-6 text-navy" style="color:#0F1E4D;">Category B1</span>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">EIA Study Required</span>
                  </label>
                  <p class="text-muted small mb-2" onclick="document.getElementById('radio_cat_b1').click();">
                    Large-scale mining leases (>5.00.0 Ha or Major Minerals) requiring Terms of Reference (ToR), EIA Baseline Data Collection, Public Hearing &amp; TNPCB submission.
                  </p>

                  {{-- B1 Sub Category Selector (Appears when B1 is checked) --}}
                  <div id="b1_subcategory_box" class="mt-3 pt-3 border-top {{ old('category', $category) === 'B1' ? '' : 'd-none' }}">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <label class="form-label small fw-bold text-dark mb-0">B1 Statutory Stage *</label>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">
                        <i class="fa fa-route me-1"></i> 2-Stage Sequential Flow
                      </span>
                    </div>
                    <div class="p-3 rounded border" id="pill_sc1" style="background:#f0fdf4; border-color:#86efac !important;">
                      <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                          <input type="radio" name="sub_category" value="TOR" id="radio_sc1" class="form-check-input mt-0" checked>
                          <div>
                            <span class="small fw-bold text-success">TOR</span>
                            <div class="text-muted" style="font-size:0.75rem;">ToR &amp; Mining Documents (5 Folders)</div>
                          </div>
                        </div>
                        <span class="badge bg-success text-white" style="font-size:10px;">Stage 1 Active</span>
                      </div>
                    </div>
                    <div class="alert alert-info py-2 px-3 small mt-2 mb-0" style="font-size:0.78rem; background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
                      <i class="fa fa-info-circle me-1"></i> <strong>Sequential B1 Workflow:</strong> All B1 applications start at <strong>TOR</strong>. Once approved by the <strong>PPT Department (ToR Presentation)</strong>, Stage 2 (ETA) unlocks automatically with full EIA baseline folders.
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>

          {{-- B2 Category Card --}}
          <div class="col-md-6">
            <div class="category-select-card h-100 p-3 rounded-3 border position-relative" id="card_cat_b2" style="cursor:pointer; transition: all 0.2s;">
              <div class="d-flex align-items-start gap-3">
                <input type="radio" name="category" value="B2" id="radio_cat_b2" class="form-check-input mt-1" {{ old('category', $category) === 'B2' || (!old('category') && !$category) ? 'checked' : '' }} required>
                <div class="flex-grow-1">
                  <label for="radio_cat_b2" class="d-flex align-items-center justify-content-between mb-1" style="cursor:pointer; width:100%;">
                    <span class="fw-bold fs-6 text-navy" style="color:#0F1E4D;">Category B2</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Direct EC</span>
                  </label>
                  <p class="text-muted small mb-2" onclick="document.getElementById('radio_cat_b2').click();">
                    Standard mining concessions (&le;5.00.0 Ha Minor Minerals) with direct document preparation, Site Photographs (DGPS, Fencing, Greenbelt), EMP, and PARIVESH submission.
                  </p>
                  <div class="text-muted small mt-2">
                    <i class="fa fa-folder-tree me-1 text-primary"></i> 6 Folders: Documents, Site Photos, Report, GIS, Signed Reports, PARIVESH.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- UPSTREAM CONCESSION & MINING PLAN INHERITANCE CARD --}}
        <!-- <div class="card p-3 mb-4" style="background:#f0fdf4; border:1px solid #86efac; border-radius:14px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label fw-bold mb-0 text-success" style="font-size:0.92rem;">
              <i class="fa fa-link me-1"></i> Inherit From Approved Mining Plan (Optional)
            </label>
            <span class="badge bg-success text-white"><i class="fa fa-bolt me-1"></i> Instant Quarry Autofill</span>
          </div>
          <p class="text-muted small mb-2">If this Environment Clearance application originates from an approved Mining Plan, select it below to instantly inherit applicant profile, district, taluk, village, survey numbers, and mineral concession details.</p>

          <div class="row g-2 align-items-center">
            <div class="col-md-9">
              <select class="form-select" name="mining_application_id" id="select_upstream_mining">
                <option value="">-- Direct Environmental Intake (No Linked Mining Plan) --</option>
                @if(isset($miningApplications))
                  @foreach($miningApplications as $m)
                    <option value="{{ $m->id }}"
                      data-customer-id="{{ $m->customer_id }}"
                      data-client-name="{{ $m->customer?->customer_name }}"
                      data-company-name="{{ $m->customer?->company_name }}"
                      data-district-id="{{ $m->district_id }}"
                      data-location="{{ ($m->taluk ? $m->taluk . ', ' : '') . ($m->village ?: '') . ($m->survey_numbers_text ? ' — SF Nos: ' . $m->survey_numbers_text : '') }}"
                      data-project-name="{{ $m->project_name ?: (($m->customer?->customer_name ?: 'Applicant') . ' Quarry EC') }}"
                      data-phone="{{ $m->customer?->mobile_num }}"
                      data-email="{{ $m->customer?->email }}"
                      data-mimas="{{ $m->customer?->mimas_no }}"
                      data-lease-id="{{ $m->lease_application_id }}">
                      {{ $m->application_no }} &middot; {{ $m->customer?->customer_name }} &middot; {{ $m->district?->name }} ({{ $m->mineral?->name ?: 'Mining Plan' }})
                    </option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-success w-100 py-2 fw-semibold" id="btn_apply_mining_data">
                <i class="fa fa-arrow-down me-1"></i> Inherit Details
              </button>
            </div>
          </div>
          <div id="mining_inherit_feedback" class="mt-2" style="display:none;"></div>
        </div> -->

        {{-- Wizard Action Buttons for Step 1 --}}
        <div class="wizard-actions d-flex justify-content-between align-items-center pt-3 border-top">
          <a href="{{ route('eviron.index') }}" class="btn btn-outline-secondary px-4">
            <i class="fa fa-times me-1"></i> Cancel
          </a>
          <button type="button" class="btn btn-navy px-4 py-2" id="btn_step1_next" style="background:#0F1E4D; color:#fff;" onclick="goToStep(2)">
            Continue to Applicant Details <i class="fa fa-arrow-right ms-1"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================================= --}}
      {{-- STEP 2: APPLICANT & QUARRY INFORMATION                                     --}}
      {{-- ========================================================================= --}}
      <div class="step-pane wizard-card d-none" id="pane_step_2">
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow" style="color:#0F1E4D; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em;">
              <i class="fa fa-user-check me-1 text-primary"></i> Step 2 of 5 &bull; Applicant &amp; Quarry Information
            </div>
            <div class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
              <i class="fa fa-clock me-1 text-muted"></i> Est. ~2-3 mins
            </div>
          </div>
          <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Applicant Legal Profile &amp; Concession Demarcation</h4>
          <div class="text-muted small">Enter client credentials, MIMAS registration, revenue boundaries, and quarry site specifications</div>
        </div>

        {{-- CUSTOMER UNIQUE ID LOOKUP CARD --}}
        <div class="s1-lookup-card mb-4">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="s1-lookup-label mb-0" for="mimas_search_input">
              <i class="fa fa-fingerprint me-1.5"></i> Customer Unique ID Lookup
            </label>
            <span class="badge s1-lookup-badge"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
          </div>
          <p class="s1-lookup-desc mb-3">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
          
          <div class="input-group">
            <span class="input-group-text bg-white border-0"><i class="fa fa-search text-primary"></i></span>
            <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold" 
                   placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
            <button class="btn s1-btn-fetch px-3 fw-bold" type="button" id="btn_lookup_mimas">
              <i class="fa fa-sync-alt me-1"></i> Fetch Details
            </button>
          </div>
          
          <datalist id="mimas_datalist">
            @if(isset($customers))
              @foreach($customers as $c)
                @if(!empty($c->mimas_no))
                  <option value="{{ $c->mimas_no }}">{{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }} [{{ $c->district?->name ?? 'N/A' }}]</option>
                @endif
              @endforeach
            @endif
          </datalist>
          <datalist id="customer_datalist">
            @if(isset($customers))
              @foreach($customers as $c)
                @if(!empty($c->mimas_no))
                  <option value="{{ $c->mimas_no }}">{{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }} [{{ $c->district?->name ?? 'N/A' }}]</option>
                @endif
              @endforeach
            @endif
          </datalist>

          <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
        </div>

        <input type="hidden" name="customer_id" id="field_customer_id" value="{{ old('customer_id') }}">
        <input type="hidden" name="lease_application_id" id="field_lease_id" value="{{ old('lease_application_id') }}">

        <div class="row g-3 mb-4">
          {{-- Client Name --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-user me-1 text-muted"></i> Client / Applicant Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control auto-filled-field" name="client_name" id="client_name" value="{{ old('client_name') }}" required placeholder="e.g. R. Kumaresan">
          </div>

          {{-- Company Name --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-building me-1 text-muted"></i> Company / Enterprise Name</label>
            <input type="text" class="form-control auto-filled-field" name="company_name" id="company_name" value="{{ old('company_name') }}" placeholder="e.g. Sri Bala Minerals &amp; Traders">
          </div>

          {{-- Project Name --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-file-contract me-1 text-muted"></i> Project / Quarry Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control auto-filled-field" name="project_name" id="project_name" value="{{ old('project_name') }}" required placeholder="e.g. Salem Rough Stone &amp; Gravel Quarry">
          </div>

          {{-- District --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-map-marker-alt me-1 text-muted"></i> District Mining Office <span class="text-danger">*</span></label>
            <select class="form-select auto-filled-field" name="district_id" id="district_id" required>
              <option value="">-- Select District --</option>
              @foreach($districts as $d)
                <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          {{-- Location / Survey Numbers --}}
          <div class="col-md-12">
            <label class="form-label fw-semibold"><i class="fa fa-compass me-1 text-muted"></i> Location / Survey Numbers</label>
            <input type="text" class="form-control auto-filled-field" name="location" id="location" value="{{ old('location') }}" placeholder="e.g. SF Nos. 124/1, 124/2, Thammampatti Village, Gangavalli Taluk">
          </div>

          {{-- Contact Person --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-id-badge me-1 text-muted"></i> Contact Person</label>
            <input type="text" class="form-control auto-filled-field" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" placeholder="Authorized representative">
          </div>

          {{-- Secondary Contact Person --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-user-friends me-1 text-muted"></i> Secondary Contact Person <span class="text-muted small">(Optional)</span></label>
            <input type="text" class="form-control auto-filled-field" name="secondary_contact_person" id="secondary_contact_person" value="{{ old('secondary_contact_person') }}" placeholder="Site supervisor / alternate contact">
          </div>

          {{-- Primary Phone Number --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-phone me-1 text-muted"></i> Primary Phone Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control auto-filled-field" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" required maxlength="15" placeholder="10-digit primary phone number">
          </div>

          {{-- Secondary Phone Number --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-phone-alt me-1 text-muted"></i> Secondary Phone Number <span class="text-muted small">(Optional)</span></label>
            <input type="text" class="form-control auto-filled-field" name="secondary_phone" id="secondary_phone" value="{{ old('secondary_phone') }}" maxlength="15" placeholder="10-digit secondary phone number">
          </div>

          {{-- Contact Email --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-envelope me-1 text-muted"></i> Contact Email</label>
            <input type="email" class="form-control auto-filled-field" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" placeholder="applicant@example.com">
          </div>

          {{-- Customer Unique ID / MIMAS --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold"><i class="fa fa-hashtag me-1 text-muted"></i> Customer Unique ID / MIMAS No</label>
            <input type="text" class="form-control auto-filled-field" name="mimas_no" id="mimas_no" value="{{ old('mimas_no') }}" placeholder="e.g. TN-MMS-SLM-001">
          </div>

          {{-- Parivesh Portal Password --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa fa-key me-1 text-muted"></i> Parivesh Portal Password
            </label>
            <div class="input-group">
              <input type="text" class="form-control auto-filled-field font-monospace" name="parivesh_password" id="field_parivesh_password" value="{{ old('parivesh_password') }}" placeholder="e.g. Parivesh@Pass2026" autocomplete="off">
              <button class="btn btn-outline-secondary btn-toggle-pass-visibility" type="button" data-target="field_parivesh_password" title="Toggle visibility">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-outline-secondary btn-copy-pass" type="button" data-target="field_parivesh_password" title="Copy password">
                <i class="fa fa-copy"></i>
              </button>
            </div>
            <div class="form-text small text-muted">Statutory clearance portal credential (saved in plain text)</div>
          </div>

          {{-- Portal Email Password --}}
          <div class="col-md-6">
            <label class="form-label fw-semibold">
              <i class="fa fa-lock me-1 text-muted"></i> Portal / Email Password
            </label>
            <div class="input-group">
              <input type="text" class="form-control auto-filled-field font-monospace" name="email_password" id="field_email_password" value="{{ old('email_password') }}" placeholder="e.g. Email@Pass2026" autocomplete="off">
              <button class="btn btn-outline-secondary btn-toggle-pass-visibility" type="button" data-target="field_email_password" title="Toggle visibility">
                <i class="fa fa-eye"></i>
              </button>
              <button class="btn btn-outline-secondary btn-copy-pass" type="button" data-target="field_email_password" title="Copy password">
                <i class="fa fa-copy"></i>
              </button>
            </div>
            <div class="form-text small text-muted">Applicant correspondence email credential (saved in plain text)</div>
          </div>
        </div>

        {{-- Wizard Action Buttons for Step 2 --}}
        <div class="wizard-actions d-flex justify-content-between align-items-center pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary px-4" onclick="goToStep(1)">
            <i class="fa fa-arrow-left me-1"></i> Back to Category
          </button>
          <button type="button" class="btn btn-navy px-4 py-2" id="btn_step2_next" style="background:#0F1E4D; color:#fff;" onclick="validateAndGoToStep(3)">
            Continue to Statutory Documents <i class="fa fa-arrow-right ms-1"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================================= --}}
      {{-- STEP 3: STATUTORY DOCUMENTS & DIGITAL PLANS CHECKLIST (THE NEW 3RD STEP)  --}}
      {{-- ========================================================================= --}}
      <div class="step-pane wizard-card d-none" id="pane_step_3">
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow" style="color:#0F1E4D; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em;">
              <i class="fa fa-folder-open me-1 text-primary"></i> Step 3 of 5 &bull; Statutory Document Repository
            </div>
            <div class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
              <i class="fa fa-clock me-1 text-muted"></i> Est. ~3-5 mins
            </div>
          </div>
          <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Statutory Document Checklist &amp; Digital Plans</h4>
          <div class="text-muted small">Option to attach essential clearance documents and survey plans now or finalize later in the Project Dossier</div>
        </div>

        {{-- Overall Completion Progress Banner (Lease Application Step 5 Parity) --}}
        <div class="p-3 mb-4 rounded-3 border" style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-color:#bfdbfe !important;">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div>
              <span class="badge bg-navy text-white px-2 py-1 me-2" style="background:#0F1E4D;">OVERALL COMPLETION</span>
              <span class="small fw-bold text-navy" style="color:#0F1E4D;">Statutory Intake Checklist Progress:</span>
              <strong class="text-primary ms-1" id="step3_attached_count_badge">0 / 0 files attached</strong>
            </div>
            <span class="badge bg-primary text-white fs-6 px-3 py-1" id="step3_percent_badge">0%</span>
          </div>
          <div class="progress" style="height: 10px; border-radius: 6px; background: #e2e8f0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="step3_progress_bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>

        {{-- Dynamic Category Folder Section Container --}}
        {{-- SECTION FOR CATEGORY B2 FOLDERS (DEFAULT) --}}
        <div id="step3_b2_folders_container">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-navy" style="color:#0F1E4D;">
              <i class="fa fa-folder-tree me-2 text-success"></i> Category B2 Statutory Folders (6 Folders)
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle">Direct EC Mode</span>
          </div>

          {{-- Folder Tabs Navigation for B2 --}}
          <ul class="nav nav-pills gap-2 mb-3" id="b2FolderTabs" role="tablist">
            @if(isset($b2Folders))
              @foreach($b2Folders as $idx => $f)
                <li class="nav-item" role="presentation">
                  <button class="nav-link py-1 px-3 {{ $idx === 0 ? 'active' : '' }}" id="tab-b2-f{{ $f->id }}" 
                          data-bs-toggle="pill" data-bs-target="#content-b2-f{{ $f->id }}" type="button" role="tab" style="font-size:0.82rem; border-radius:8px;">
                    <span class="badge rounded-circle bg-white text-dark me-1 px-1.5">{{ $idx + 1 }}</span>
                    {{ $f->name }}
                    <span class="badge bg-light text-dark ms-1 folder-attach-pill" id="b2_folder_pill_{{ $f->id }}">0/{{ $f->documentFields->count() }}</span>
                  </button>
                </li>
              @endforeach
            @endif
          </ul>

          {{-- Tab Panes for B2 Folders --}}
          <div class="tab-content border rounded-3 p-3 bg-white mb-4" id="b2FolderTabContent">
            @if(isset($b2Folders))
              @foreach($b2Folders as $idx => $f)
                <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="content-b2-f{{ $f->id }}" role="tabpanel">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                    <div>
                      <strong class="text-navy" style="color:#0F1E4D;">{{ $f->name }}</strong>
                      <div class="text-muted small">Checklist items and statutory attachments for {{ $f->name }}</div>
                    </div>
                    <span class="badge bg-light text-muted border">{{ $f->documentFields->count() }} Items</span>
                  </div>

                  <div class="checklist-grid">
                    @foreach($f->documentFields as $df)
                      <div class="checklist-row p-2 mb-2 rounded border d-flex align-items-center justify-content-between gap-3" 
                           style="background:#f8fafc; transition: all 0.2s;" id="doc_row_{{ $df->id }}">
                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                          <div class="ci-icon d-flex align-items-center justify-content-center rounded-circle text-muted" 
                               id="ci_icon_{{ $df->id }}" style="width:34px; height:34px; background:#e2e8f0; font-size:0.9rem;">
                            <i class="fa fa-file"></i>
                          </div>
                          <div>
                            <div class="fw-semibold text-dark small">{{ $df->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                              <span class="badge bg-light text-muted border" style="font-size:10px;">{{ $df->code ?: 'DOC_' . $df->id }}</span>
                              @if($df->is_mandatory)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:10px;">Mandatory</span>
                              @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:10px;">Optional</span>
                              @endif
                              <span class="text-muted small file-chosen-label" id="file_label_{{ $df->id }}" style="font-size:11px;">No file chosen</span>
                            </div>
                          </div>
                        </div>

                        {{-- File Upload Input Box --}}
                        <div class="d-flex align-items-center gap-2">
                          <label for="doc_input_{{ $df->id }}" class="btn btn-sm btn-outline-primary py-1 px-3 mb-0" style="cursor:pointer; font-size:0.8rem;">
                            <i class="fa fa-cloud-upload-alt me-1"></i> Attach File
                          </label>
                          <input type="file" name="documents[{{ $df->id }}]" id="doc_input_{{ $df->id }}" 
                                 class="d-none step3-file-input" 
                                 data-field-id="{{ $df->id }}"
                                 data-field-name="{{ $df->name }}"
                                 accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.kml">
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            @endif
          </div>
        </div>

        {{-- SECTION FOR CATEGORY B1 FOLDERS (SHOWN WHEN B1 IS CHECKED) --}}
        <div id="step3_b1_folders_container" style="display:none;">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="fw-bold mb-0 text-navy" style="color:#0F1E4D;">
              <i class="fa fa-folder-tree me-2 text-primary"></i> Category B1 (ToR) Statutory Folders (5 Folders)
            </h6>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Stage 1: ToR Intake</span>
          </div>

          {{-- Folder Tabs Navigation for B1 --}}
          <ul class="nav nav-pills gap-2 mb-3" id="b1FolderTabs" role="tablist">
            @if(isset($b1Folders))
              @foreach($b1Folders as $idx => $f)
                <li class="nav-item" role="presentation">
                  <button class="nav-link py-1 px-3 {{ $idx === 0 ? 'active' : '' }}" id="tab-b1-f{{ $f->id }}" 
                          data-bs-toggle="pill" data-bs-target="#content-b1-f{{ $f->id }}" type="button" role="tab" style="font-size:0.82rem; border-radius:8px;">
                    <span class="badge rounded-circle bg-white text-dark me-1 px-1.5">{{ $idx + 1 }}</span>
                    {{ $f->name }}
                    <span class="badge bg-light text-dark ms-1 folder-attach-pill" id="b1_folder_pill_{{ $f->id }}">0/{{ $f->documentFields->count() }}</span>
                  </button>
                </li>
              @endforeach
            @endif
          </ul>

          {{-- Tab Panes for B1 Folders --}}
          <div class="tab-content border rounded-3 p-3 bg-white mb-4" id="b1FolderTabContent">
            @if(isset($b1Folders))
              @foreach($b1Folders as $idx => $f)
                <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="content-b1-f{{ $f->id }}" role="tabpanel">
                  <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                    <div>
                      <strong class="text-navy" style="color:#0F1E4D;">{{ $f->name }}</strong>
                      <div class="text-muted small">Checklist items and statutory attachments for {{ $f->name }}</div>
                    </div>
                    <span class="badge bg-light text-muted border">{{ $f->documentFields->count() }} Items</span>
                  </div>

                  <div class="checklist-grid">
                    @foreach($f->documentFields as $df)
                      <div class="checklist-row p-2 mb-2 rounded border d-flex align-items-center justify-content-between gap-3" 
                           style="background:#f8fafc; transition: all 0.2s;" id="doc_row_b1_{{ $df->id }}">
                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                          <div class="ci-icon d-flex align-items-center justify-content-center rounded-circle text-muted" 
                               id="ci_icon_b1_{{ $df->id }}" style="width:34px; height:34px; background:#e2e8f0; font-size:0.9rem;">
                            <i class="fa fa-file"></i>
                          </div>
                          <div>
                            <div class="fw-semibold text-dark small">{{ $df->name }}</div>
                            <div class="d-flex align-items-center gap-2 mt-0.5">
                              <span class="badge bg-light text-muted border" style="font-size:10px;">{{ $df->code ?: 'DOC_' . $df->id }}</span>
                              @if($df->is_mandatory)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:10px;">Mandatory</span>
                              @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:10px;">Optional</span>
                              @endif
                              <span class="text-muted small file-chosen-label" id="file_label_b1_{{ $df->id }}" style="font-size:11px;">No file chosen</span>
                            </div>
                          </div>
                        </div>

                        {{-- File Upload Input Box --}}
                        <div class="d-flex align-items-center gap-2">
                          <label for="doc_input_b1_{{ $df->id }}" class="btn btn-sm btn-outline-primary py-1 px-3 mb-0" style="cursor:pointer; font-size:0.8rem;">
                            <i class="fa fa-cloud-upload-alt me-1"></i> Attach File
                          </label>
                          <input type="file" name="documents[{{ $df->id }}]" id="doc_input_b1_{{ $df->id }}" 
                                 class="d-none step3-file-input" 
                                 data-field-id="{{ $df->id }}"
                                 data-field-name="{{ $df->name }}"
                                 accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip,.kml">
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            @endif
          </div>
        </div>

        {{-- Flexible Intake Standard Notice Card --}}
        <div class="p-3 mb-4 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
          <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-navy text-white px-2 py-0.5" style="background:#0F1E4D; font-size:11px;">FLEXIBLE INTAKE STANDARD</span>
            <span class="fw-semibold small text-dark">Pre-uploading documents at intake is optional</span>
          </div>
          <p class="text-muted small mb-0">
            You can attach any available reports, maps, and photographs now. Any pending statutory files can be easily uploaded, replaced, and scrutinized inside the <strong>Project Dossier</strong> (`/eviron/{id}`) after creation.
          </p>
        </div>

        {{-- Wizard Action Buttons for Step 3 --}}
        <div class="wizard-actions d-flex justify-content-between align-items-center pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary px-4" onclick="goToStep(2)">
            <i class="fa fa-arrow-left me-1"></i> Back to Applicant Details
          </button>
          <button type="button" class="btn btn-navy px-4 py-2" id="btn_step3_next" style="background:#0F1E4D; color:#fff;" onclick="goToStep(4)">
            Continue to Handling Team <i class="fa fa-arrow-right ms-1"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================================= --}}
      {{-- STEP 4: PROJECT HANDLING TEAM & IN-CHARGE PERSONS                          --}}
      {{-- ========================================================================= --}}
      <div class="step-pane wizard-card d-none" id="pane_step_4">
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow" style="color:#0F1E4D; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em;">
              <i class="fa fa-users-gear me-1 text-primary"></i> Step 4 of 5 &bull; Handling Team &amp; In-Charge Persons
            </div>
            <div class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
              <i class="fa fa-clock me-1 text-muted"></i> Est. ~1-2 mins
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Project Handling Team &amp; In-Charge Persons</h4>
              <div class="text-muted small">Assign environmental coordinators, field officers, EIA coordinators, and liaison personnel</div>
            </div>
            <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_env_handler" style="background:#0F1E4D; color:#fff;">
              <i class="fa fa-user-plus me-1 text-warning"></i> + Add Person
            </button>
          </div>
        </div>

        <div class="table-responsive mb-3">
          <table class="table table-bordered align-middle" id="env_handlers_table">
            <thead class="bg-light text-navy" style="font-size:0.85rem;">
              <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th style="width: 30%;">Person Name <span class="text-danger">*</span></th>
                <th style="width: 30%;">Role / Designation <span class="text-danger">*</span></th>
                <th>Notes &amp; Responsibilities</th>
                <th style="width: 70px;" class="text-center">Action</th>
              </tr>
            </thead>
            <tbody id="env_handlers_tbody">
              <tr>
                <td class="text-center fw-bold row-num">1</td>
                <td>
                  <input type="text" name="handlers[0][person_name]" class="form-control form-control-sm" placeholder="e.g. Ramesh Kumar">
                </td>
                <td>
                  <input type="text" name="handlers[0][role]" class="form-control form-control-sm" placeholder="e.g. EIA Coordinator, Field Officer">
                </td>
                <td>
                  <input type="text" name="handlers[0][notes]" class="form-control form-control-sm" placeholder="e.g. Site inspection, Public hearing liaison">
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-outline-danger btn-remove-handler disabled" style="opacity:0.4;">
                    <i class="fa fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="alert alert-info py-2 px-3 small rounded-2 mb-4" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
          <i class="fa fa-info-circle me-1"></i> You can type custom roles manually (e.g. <em>EIA Coordinator, Field Geologist, Environmental Chemist, Documentation In-Charge</em>).
        </div>

        {{-- Wizard Action Buttons for Step 4 --}}
        <div class="wizard-actions d-flex justify-content-between align-items-center pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary px-4" onclick="goToStep(3)">
            <i class="fa fa-arrow-left me-1"></i> Back to Documents
          </button>
          <button type="button" class="btn btn-navy px-4 py-2" id="btn_step4_next" style="background:#0F1E4D; color:#fff;" onclick="goToStep(5)">
            Continue to Financial Settlement <i class="fa fa-arrow-right ms-1"></i>
          </button>
        </div>
      </div>

      {{-- ========================================================================= --}}
      {{-- STEP 5: PAYMENT & FINANCIAL SETTLEMENT & FINAL SUBMIT                      --}}
      {{-- ========================================================================= --}}
      <div class="step-pane wizard-card d-none" id="pane_step_5">
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow" style="color:#0F1E4D; font-weight:700; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.06em;">
              <i class="fa fa-coins me-1 text-primary"></i> Step 5 of 5 &bull; Financial Settlement &amp; Review
            </div>
            <div class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
              <i class="fa fa-clock me-1 text-muted"></i> Est. ~1-2 mins
            </div>
          </div>
          <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Payment Ledger &amp; Application Final Submission</h4>
          <div class="text-muted small">Record statutory clearance quotation, client advance paid, and track pending settlement</div>
        </div>

        <!-- Real-Time Metrics -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
              <div class="text-muted small fw-semibold text-uppercase">Service / Product Value</div>
              <div class="h4 fw-bold mb-0 text-navy mt-1" id="disp_env_product_val">₹ 0.00</div>
              <small class="text-muted" style="font-size:11px;">Clearance package quotation</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
              <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
              <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_env_paid_val">₹ 0.00</div>
              <small class="text-muted" style="font-size:11px;">Advance / received so far</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
              <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
              <div class="h4 fw-bold mb-0 text-danger mt-1" id="disp_env_pending_val">₹ 0.00</div>
              <small class="text-muted" style="font-size:11px;">Auto-calculated outstanding</small>
            </div>
          </div>
        </div>

        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-semibold">Product / Service Value (₹) *</label>
            <div class="input-group">
              <span class="input-group-text bg-white fw-bold">₹</span>
              <input type="number" step="0.01" min="0" name="product_value" id="env_product_value" class="form-control fw-bold" placeholder="0.00" value="{{ old('product_value', '0.00') }}" required>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Paid Amount (₹) *</label>
            <div class="input-group">
              <span class="input-group-text bg-white fw-bold text-success">₹</span>
              <input type="number" step="0.01" min="0" name="paid_amount" id="env_paid_amount" class="form-control fw-bold text-success" placeholder="0.00" value="{{ old('paid_amount', '0.00') }}" required>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Pending Balance (₹)</label>
            <div class="input-group">
              <span class="input-group-text bg-light text-muted">₹</span>
              <input type="number" step="0.01" name="pending_amount" id="env_pending_amount" class="form-control bg-light fw-bold text-danger" placeholder="0.00" readonly value="{{ old('pending_amount', '0.00') }}">
            </div>
          </div>

          {{-- Quick Settlement Presets --}}
          <div class="col-md-12">
            <div class="d-flex align-items-center gap-2">
              <span class="text-muted small fw-semibold">Quick Presets:</span>
              <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 fw-semibold" id="btn_preset_full" style="font-size:11px;">100% Full</button>
              <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 fw-semibold" id="btn_preset_half" style="font-size:11px;">50% Advance</button>
              <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 fw-semibold" id="btn_preset_zero" style="font-size:11px;">Clear (₹0)</button>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Payment Status *</label>
            <select class="form-select" name="payment_status" id="env_payment_status">
              <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
              <option value="partial" {{ old('payment_status') === 'partial' ? 'selected' : '' }}>Partial Payment Received</option>
              <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Payment Notes / Reference</label>
            <input type="text" class="form-control" name="payment_notes" id="env_payment_notes" value="{{ old('payment_notes') }}" placeholder="e.g. Advance paid via Cheque #1029 / NEFT Ref">
          </div>
        </div>

        {{-- Pre-Submission Intake Review Card --}}
        <div class="card p-3 mb-4 rounded-3 border" style="background:#f8fafc; border-color:#e2e8f0;">
          <h6 class="fw-bold text-navy mb-2" style="color:#0F1E4D;">
            <i class="fa fa-clipboard-check me-1 text-primary"></i> Pre-Submission Application Review
          </h6>
          <div class="row g-2 small">
            <div class="col-md-3">
              <span class="text-muted">Selected Category:</span>
              <strong class="d-block text-dark" id="rev_category">Category B2 (Direct EC)</strong>
            </div>
            <div class="col-md-3">
              <span class="text-muted">Applicant Name:</span>
              <strong class="d-block text-dark" id="rev_applicant">&mdash;</strong>
            </div>
            <div class="col-md-3">
              <span class="text-muted">District Mining Office:</span>
              <strong class="d-block text-dark" id="rev_district">&mdash;</strong>
            </div>
            <div class="col-md-3">
              <span class="text-muted">Documents Attached:</span>
              <strong class="d-block text-success" id="rev_docs_count">0 files attached</strong>
            </div>
          </div>
        </div>

        {{-- Wizard Action Buttons for Step 5 --}}
        <div class="wizard-actions d-flex justify-content-between align-items-center pt-3 border-top">
          <button type="button" class="btn btn-outline-secondary px-4" onclick="goToStep(4)">
            <i class="fa fa-arrow-left me-1"></i> Back to Handling Team
          </button>
          <button type="submit" class="btn btn-navy px-5 py-2.5 fw-bold" id="btnSubmitEviron" style="background:#0F1E4D; color:#fff;">
            <i class="fa fa-check-circle me-1 text-success"></i> Create Project &amp; Initialize Folders
          </button>
        </div>
      </div>

    </form>

  </div>
</div>

<style>
/* Scoped 5-Step Stepper Styling (Lease Application Parity) */
#gtms-eviron-create-page .step-progress {
  display: flex;
  align-items: flex-start;
  margin-bottom: 24px;
}
#gtms-eviron-create-page .step-progress .sp-step {
  flex: 1;
  text-align: center;
  position: relative;
}
#gtms-eviron-create-page .step-progress .sp-step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 16px;
  left: 56%;
  width: 88%;
  height: 2px;
  background: #e2e8f0;
  z-index: 0;
}
#gtms-eviron-create-page .step-progress .sp-step.done:not(:last-child)::after {
  background: #10b981;
}
#gtms-eviron-create-page .step-progress .circ {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid #cbd5e1;
  color: #64748b;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 1;
  font-size: 0.82rem;
  transition: all 0.2s ease;
}
#gtms-eviron-create-page .step-progress .sp-step.done .circ {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}
#gtms-eviron-create-page .step-progress .sp-step.active .circ {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #fff;
  box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
}
#gtms-eviron-create-page .step-progress .sp-label {
  font-size: 0.74rem;
  font-weight: 700;
  margin-top: 6px;
  color: #64748b;
  transition: color 0.2s ease;
}
#gtms-eviron-create-page .step-progress .sp-step.done .sp-label {
  color: #10b981;
}
#gtms-eviron-create-page .step-progress .sp-step.active .sp-label {
  color: #0F1E4D;
}

/* Wizard Card Container */
#gtms-eviron-create-page .wizard-card {
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 26px 30px;
  box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
}

/* Checklist Rows in Step 3 */
#gtms-eviron-create-page .checklist-row:hover {
  background: #f1f5f9 !important;
  border-color: #cbd5e1 !important;
  transform: translateY(-1px);
}
#gtms-eviron-create-page .checklist-row.file-attached {
  background: #f0fdf4 !important;
  border-color: #86efac !important;
}
#gtms-eviron-create-page .checklist-row.file-attached .ci-icon {
  background: #10b981 !important;
  color: #ffffff !important;
}

/* Autofilled Field Visual Pulse (Lease App Parity) */
#gtms-eviron-create-page .field-autofilled {
  background-color: #f0fdf4 !important;
  border-color: #86efac !important;
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25) !important;
  transition: all 0.4s ease;
}

/* Dark Mode Harmonization */
body[data-theme-version="dark"] #gtms-eviron-create-page .wizard-card {
  background: #1e293b;
  border-color: #334155;
  color: #f8fafc;
}
body[data-theme-version="dark"] #gtms-eviron-create-page .step-progress .circ {
  background: #0f172a;
  border-color: #334155;
  color: #94a3b8;
}
body[data-theme-version="dark"] #gtms-eviron-create-page .step-progress .sp-step:not(:last-child)::after {
  background: #334155;
}
body[data-theme-version="dark"] #gtms-eviron-create-page .checklist-row {
  background: #0f172a !important;
  border-color: #334155 !important;
}
</style>

@php
  $customerLookupMap = ($customers ?? collect())->map(function($c) {
      return [
          'id'                       => $c->id,
          'mimas_no'                 => $c->mimas_no,
          'mimas_number'             => $c->mimas_number,
          'customer_name'            => $c->customer_name,
          'company_name'             => $c->company_name,
          'secondary_contact_person' => $c->secondary_contact_person,
          'mobile_num'               => $c->mobile_num,
          'secondary_mobile_num'     => $c->secondary_mobile_num,
          'email'                    => $c->email,
          'parivesh_password'        => $c->parivesh_password,
          'email_password'           => $c->email_password,
          'district_id'              => $c->district_id,
          'district_name'            => $c->district?->name,
          'address'                  => $c->address,
          'area'                     => $c->area,
      ];
  })->values();
@endphp

<script>
let currentStep = 1;
const totalSteps = 5;
const localCustomerList = @json($customerLookupMap);

// Function to Navigate to Specific Step with Validation
function goToStep(stepNumber) {
  if (stepNumber < 1 || stepNumber > totalSteps) return;

  // Update step panes visibility
  for (let i = 1; i <= totalSteps; i++) {
    const pane = document.getElementById('pane_step_' + i);
    const indicator = document.getElementById('step_indicator_' + i);

    if (pane) {
      if (i === stepNumber) {
        pane.classList.remove('d-none');
      } else {
        pane.classList.add('d-none');
      }
    }

    if (indicator) {
      if (i < stepNumber) {
        indicator.classList.remove('active');
        indicator.classList.add('done');
        indicator.querySelector('.circ').innerHTML = '<i class="fa fa-check"></i>';
      } else if (i === stepNumber) {
        indicator.classList.add('active');
        indicator.classList.remove('done');
        indicator.querySelector('.circ').textContent = i;
      } else {
        indicator.classList.remove('active', 'done');
        indicator.querySelector('.circ').textContent = i;
      }
    }
  }

  currentStep = stepNumber;

  // Update Category Specific Visibility in Step 3
  syncCategoryWithStep3();

  // If entering Step 5, refresh review summary metrics
  if (stepNumber === 5) {
    refreshStep5Review();
  }

  // Smooth scroll to top of wizard
  const topEl = document.getElementById('eviron_stepper');
  if (topEl) {
    topEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
}

// Validation before proceeding from Step 2
function validateAndGoToStep(targetStep) {
  if (currentStep === 2) {
    const clientName = document.getElementById('client_name');
    const projectName = document.getElementById('project_name');
    const districtId = document.getElementById('district_id');
    const contactPhone = document.getElementById('contact_phone');

    if (!clientName.value.trim()) {
      alert('Please enter Client / Applicant Name before continuing.');
      clientName.focus();
      return;
    }
    if (!projectName.value.trim()) {
      alert('Please enter Project / Quarry Name before continuing.');
      projectName.focus();
      return;
    }
    if (!districtId.value) {
      alert('Please select District Mining Office before continuing.');
      districtId.focus();
      return;
    }
    if (!contactPhone.value.trim()) {
      alert('Please enter Primary Phone Number before continuing.');
      contactPhone.focus();
      return;
    }
  }

  goToStep(targetStep);
}

// Allow jumping to completed steps or next immediate step
function jumpToStep(targetStep) {
  if (targetStep <= currentStep) {
    goToStep(targetStep);
  } else if (targetStep === currentStep + 1) {
    if (currentStep === 2) {
      validateAndGoToStep(targetStep);
    } else {
      goToStep(targetStep);
    }
  }
}

// Synchronize selected Category with Step 3 document folders
function syncCategoryWithStep3() {
  const isB1 = document.getElementById('radio_cat_b1')?.checked;
  const b1Container = document.getElementById('step3_b1_folders_container');
  const b2Container = document.getElementById('step3_b2_folders_container');

  if (isB1) {
    if (b1Container) b1Container.style.display = 'block';
    if (b2Container) b2Container.style.display = 'none';
  } else {
    if (b1Container) b1Container.style.display = 'none';
    if (b2Container) b2Container.style.display = 'block';
  }

  updateStep3CompletionStats();
}

// Compute live file attachment statistics in Step 3
function updateStep3CompletionStats() {
  const isB1 = document.getElementById('radio_cat_b1')?.checked;
  const activeContainer = isB1 ? document.getElementById('step3_b1_folders_container') : document.getElementById('step3_b2_folders_container');
  if (!activeContainer) return;

  const fileInputs = activeContainer.querySelectorAll('.step3-file-input');
  let total = fileInputs.length;
  let attached = 0;

  fileInputs.forEach(input => {
    if (input.files && input.files.length > 0) {
      attached++;
    }
  });

  const percent = total > 0 ? Math.round((attached / total) * 100) : 0;

  const countBadge = document.getElementById('step3_attached_count_badge');
  const percentBadge = document.getElementById('step3_percent_badge');
  const progressBar = document.getElementById('step3_progress_bar');

  if (countBadge) countBadge.textContent = attached + ' / ' + total + ' files attached';
  if (percentBadge) percentBadge.textContent = percent + '%';
  if (progressBar) {
    progressBar.style.width = percent + '%';
    progressBar.setAttribute('aria-valuenow', percent);
  }

  // Update Step 5 review metric
  const revDocs = document.getElementById('rev_docs_count');
  if (revDocs) {
    revDocs.textContent = attached + ' files attached';
  }
}

// Refresh Step 5 Pre-Submission Review Summary
function refreshStep5Review() {
  const isB1 = document.getElementById('radio_cat_b1')?.checked;
  const revCat = document.getElementById('rev_category');
  if (revCat) {
    revCat.textContent = isB1 ? 'Category B1 (ToR Intake)' : 'Category B2 (Direct EC)';
  }

  const clientName = document.getElementById('client_name')?.value || '—';
  const companyName = document.getElementById('company_name')?.value;
  const revApplicant = document.getElementById('rev_applicant');
  if (revApplicant) {
    revApplicant.textContent = companyName ? (companyName + ' (' + clientName + ')') : clientName;
  }

  const districtSelect = document.getElementById('district_id');
  const revDistrict = document.getElementById('rev_district');
  if (revDistrict && districtSelect && districtSelect.selectedIndex > 0) {
    revDistrict.textContent = districtSelect.options[districtSelect.selectedIndex].text;
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // Category Selection Highlighting
  const radioCatB1 = document.getElementById('radio_cat_b1');
  const radioCatB2 = document.getElementById('radio_cat_b2');
  const cardCatB1 = document.getElementById('card_cat_b1');
  const cardCatB2 = document.getElementById('card_cat_b2');
  const b1SubBox = document.getElementById('b1_subcategory_box');

  function updateCategoryHighlight() {
    if (radioCatB1 && radioCatB1.checked) {
      if (cardCatB1) {
        cardCatB1.style.borderColor = '#0F1E4D';
        cardCatB1.style.background = '#f8fafc';
      }
      if (cardCatB2) {
        cardCatB2.style.borderColor = '#e2e8f0';
        cardCatB2.style.background = '#ffffff';
      }
      if (b1SubBox) b1SubBox.classList.remove('d-none');
    } else {
      if (cardCatB2) {
        cardCatB2.style.borderColor = '#059669';
        cardCatB2.style.background = '#f0fdf4';
      }
      if (cardCatB1) {
        cardCatB1.style.borderColor = '#e2e8f0';
        cardCatB1.style.background = '#ffffff';
      }
      if (b1SubBox) b1SubBox.classList.add('d-none');
    }
    syncCategoryWithStep3();
  }

  if (cardCatB1) {
    cardCatB1.addEventListener('click', function(e) {
      if (e.target !== radioCatB1) radioCatB1.click();
    });
  }
  if (cardCatB2) {
    cardCatB2.addEventListener('click', function(e) {
      if (e.target !== radioCatB2) radioCatB2.click();
    });
  }
  if (radioCatB1) radioCatB1.addEventListener('change', updateCategoryHighlight);
  if (radioCatB2) radioCatB2.addEventListener('change', updateCategoryHighlight);

  updateCategoryHighlight();

  // ====== STEP 3: FILE ATTACHMENT HANDLERS ======
  document.querySelectorAll('.step3-file-input').forEach(input => {
    input.addEventListener('change', function() {
      const fieldId = this.dataset.fieldId;
      const file = this.files[0];
      const label = document.getElementById('file_label_' + fieldId) || document.getElementById('file_label_b1_' + fieldId);
      const icon = document.getElementById('ci_icon_' + fieldId) || document.getElementById('ci_icon_b1_' + fieldId);
      const row = document.getElementById('doc_row_' + fieldId) || document.getElementById('doc_row_b1_' + fieldId);

      if (file) {
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        if (label) {
          label.innerHTML = '<strong class="text-success">' + file.name + '</strong> (' + sizeMb + ' MB)';
        }
        if (icon) {
          icon.className = 'ci-icon d-flex align-items-center justify-content-center rounded-circle bg-success text-white';
          icon.innerHTML = '<i class="fa fa-check"></i>';
        }
        if (row) {
          row.classList.add('file-attached');
        }
      } else {
        if (label) {
          label.textContent = 'No file chosen';
        }
        if (icon) {
          icon.className = 'ci-icon d-flex align-items-center justify-content-center rounded-circle text-muted';
          icon.innerHTML = '<i class="fa fa-file"></i>';
        }
        if (row) {
          row.classList.remove('file-attached');
        }
      }

      updateStep3CompletionStats();
    });
  });

  // ====== MIMAS / CUSTOMER LOOKUP (INSTANT & CROSS-MODULE SYNC) ======
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const mimasFeedbackBox = document.getElementById('mimas_feedback_box');

  function populateCustomerFields(c, sourceLabel) {
    if (!c) return;

    if (document.getElementById('field_customer_id')) document.getElementById('field_customer_id').value = c.id || '';
    if (document.getElementById('client_name')) document.getElementById('client_name').value = c.customer_name || '';
    if (document.getElementById('company_name')) document.getElementById('company_name').value = c.company_name || '';
    if (document.getElementById('contact_name')) document.getElementById('contact_name').value = c.customer_name || '';
    if (document.getElementById('secondary_contact_person')) document.getElementById('secondary_contact_person').value = c.secondary_contact_person || '';
    if (document.getElementById('contact_phone')) document.getElementById('contact_phone').value = c.mobile_num || '';
    if (document.getElementById('secondary_phone')) document.getElementById('secondary_phone').value = c.secondary_mobile_num || '';
    if (document.getElementById('contact_email')) document.getElementById('contact_email').value = c.email || '';
    if (document.getElementById('mimas_no')) document.getElementById('mimas_no').value = c.mimas_no || c.mimas_number || '';
    if (document.getElementById('field_parivesh_password') && c.parivesh_password) {
      document.getElementById('field_parivesh_password').value = c.parivesh_password;
    }
    if (document.getElementById('field_email_password') && c.email_password) {
      document.getElementById('field_email_password').value = c.email_password;
    }

    // District Dropdown Auto-Selection
    if (c.district_id && document.getElementById('district_id')) {
      document.getElementById('district_id').value = c.district_id;
    }

    // Location / Survey Numbers (if empty or previously autofilled)
    const locInput = document.getElementById('location');
    if (locInput && (!locInput.value || locInput.dataset.autofilled === '1') && c.address) {
      locInput.value = c.address;
      locInput.dataset.autofilled = '1';
    }

    // Project / Quarry Name suggestion (if empty or previously autofilled)
    const projInput = document.getElementById('project_name');
    if (projInput && (!projInput.value || projInput.dataset.autofilled === '1')) {
      const baseName = c.company_name || c.customer_name || 'Concession';
      projInput.value = baseName + ' Quarry';
      projInput.dataset.autofilled = '1';
    }

    // Visual cue pulse on all autofilled fields (Lease App Parity)
    document.querySelectorAll('.auto-filled-field').forEach(el => {
      el.classList.add('field-autofilled');
      setTimeout(() => el.classList.remove('field-autofilled'), 3000);
    });

    // Feedback alert card
    if (mimasFeedbackBox) {
      mimasFeedbackBox.style.display = 'block';
      mimasFeedbackBox.innerHTML = `
        <div class="alert alert-success py-2 px-3 small mb-0 d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-sm rounded-3 border-success">
          <div>
            <i class="fa fa-check-circle me-1 text-success"></i>
            <strong>Customer Profile Loaded:</strong> ${c.customer_name || ''} 
            ${c.company_name ? '&bull; <strong>' + c.company_name + '</strong>' : ''} 
            ${c.district_name ? '&bull; <span class="text-muted">' + c.district_name + '</span>' : ''}
            &bull; <span class="badge bg-success text-white">ID: ${c.mimas_no || c.id}</span>
          </div>
          <span class="badge bg-success text-white"><i class="fa fa-bolt me-1"></i> Populated</span>
        </div>
      `;
    }

    if (typeof toastr !== 'undefined') {
      toastr.success('Customer details populated: ' + (c.company_name || c.customer_name));
    }
  }

  function findCustomerLocally(query) {
    if (!query || typeof localCustomerList === 'undefined') return null;
    const q = query.trim().toLowerCase();
    const cleanDigits = q.replace(/\D/g, '');

    return localCustomerList.find(c => {
      if (c.mimas_no && c.mimas_no.toLowerCase() === q) return true;
      if (c.mimas_number && c.mimas_number.toLowerCase() === q) return true;
      if (String(c.id) === q) return true;
      if (c.customer_name && c.customer_name.toLowerCase() === q) return true;
      if (c.company_name && c.company_name.toLowerCase() === q) return true;
      if (cleanDigits.length >= 10 && c.mobile_num && c.mobile_num.replace(/\D/g, '').includes(cleanDigits)) return true;
      return false;
    });
  }

  let isPerformingLookup = false;

  function performMimasLookup() {
    if (!mimasSearchInput) return;
    const query = mimasSearchInput.value.trim();
    if (!query) {
      if (mimasFeedbackBox) {
        mimasFeedbackBox.style.display = 'block';
        mimasFeedbackBox.innerHTML = `
          <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
            <i class="fa fa-exclamation-triangle me-1 text-warning"></i>
            Please enter or select a Customer Unique ID or MIMAS number first.
          </div>
        `;
      }
      return;
    }

    if (isPerformingLookup) return;
    isPerformingLookup = true;

    // 1. Instant local match (0ms latency)
    const localMatch = findCustomerLocally(query);
    if (localMatch) {
      populateCustomerFields(localMatch, 'local');
    }

    // 2. Fetch canonical customer record from server
    if (btnLookupMimas) {
      btnLookupMimas.disabled = true;
      btnLookupMimas.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Fetching...';
    }

    const endpoint = `{{ url('/customers/lookup-mimas') }}/${encodeURIComponent(query)}`;
    const altEndpoint = `{{ url('/eviron/api/customer-lookup') }}/${encodeURIComponent(query)}`;

    fetch(endpoint, {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(async res => {
      if (!res.ok) {
        // Fallback to alternate environment route if first returns 403 or 404
        const altRes = await fetch(altEndpoint, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (altRes.ok) return altRes.json();
      }
      return res.json();
    })
    .then(res => {
      if (btnLookupMimas) {
        btnLookupMimas.disabled = false;
        btnLookupMimas.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
      }
      isPerformingLookup = false;

      if (res && res.status === 1 && res.data) {
        populateCustomerFields(res.data, 'server');
      } else if (!localMatch) {
        if (mimasFeedbackBox) {
          mimasFeedbackBox.style.display = 'block';
          mimasFeedbackBox.innerHTML = `
            <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
              <i class="fa fa-exclamation-triangle me-1 text-warning"></i>
              No customer found matching "${query}". You can enter applicant details manually.
            </div>
          `;
        }
      }
    })
    .catch(err => {
      console.error('MIMAS Lookup Error:', err);
      if (btnLookupMimas) {
        btnLookupMimas.disabled = false;
        btnLookupMimas.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
      }
      isPerformingLookup = false;

      if (!localMatch && mimasFeedbackBox) {
        mimasFeedbackBox.style.display = 'block';
        mimasFeedbackBox.innerHTML = `
          <div class="alert alert-warning py-2 px-3 small mb-0 rounded-3">
            <i class="fa fa-exclamation-triangle me-1 text-warning"></i>
            Could not reach customer directory. You can enter applicant details manually.
          </div>
        `;
      }
    });
  }

  if (btnLookupMimas) {
    btnLookupMimas.addEventListener('click', performMimasLookup);
  }

  if (mimasSearchInput) {
    // Enter key
    mimasSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performMimasLookup();
      }
    });

    // Datalist selection or change event
    mimasSearchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performMimasLookup();
      }
    });

    // Instant keystroke matching
    mimasSearchInput.addEventListener('input', function() {
      const val = this.value.trim();
      if (val.length >= 6) {
        const found = findCustomerLocally(val);
        if (found) {
          populateCustomerFields(found, 'instant');
        }
      }
    });
  }

  // ====== UPSTREAM MINING PLAN INHERITANCE ======
  const selectUpstreamMining = document.getElementById('select_upstream_mining');
  const btnApplyMiningData = document.getElementById('btn_apply_mining_data');
  const miningInheritFeedback = document.getElementById('mining_inherit_feedback');

  function applyMiningPlanInheritance() {
    if (!selectUpstreamMining) return;
    const opt = selectUpstreamMining.options[selectUpstreamMining.selectedIndex];
    if (!opt || !opt.value) return;

    const custId = opt.dataset.customerId;
    const clientName = opt.dataset.clientName;
    const companyName = opt.dataset.companyName;
    const districtId = opt.dataset.districtId;
    const location = opt.dataset.location;
    const projectName = opt.dataset.projectName;
    const phone = opt.dataset.phone;
    const email = opt.dataset.email;
    const mimas = opt.dataset.mimas;
    const leaseId = opt.dataset.leaseId;

    if (custId) document.getElementById('field_customer_id').value = custId;
    if (leaseId) document.getElementById('field_lease_id').value = leaseId;
    if (clientName) document.getElementById('client_name').value = clientName;
    if (companyName) document.getElementById('company_name').value = companyName;
    if (districtId) {
      const sel = document.getElementById('district_id');
      if (sel) sel.value = districtId;
    }
    if (location) document.getElementById('location').value = location;
    if (projectName) document.getElementById('project_name').value = projectName;
    if (phone) document.getElementById('contact_phone').value = phone;
    if (email) document.getElementById('contact_email').value = email;
    if (mimas) {
      document.getElementById('mimas_no').value = mimas;
      if (mimasSearchInput) mimasSearchInput.value = mimas;
    }

    if (miningInheritFeedback) {
      miningInheritFeedback.style.display = 'block';
      miningInheritFeedback.innerHTML = `
        <div class="alert alert-success py-2 px-3 small mb-0 d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-link me-1 text-success"></i>
            <strong>Inherited from Mining Plan:</strong> ${opt.text}
          </div>
          <span class="badge bg-success text-white">Quarry details populated</span>
        </div>
      `;
    }
  }

  if (selectUpstreamMining) {
    selectUpstreamMining.addEventListener('change', applyMiningPlanInheritance);
  }
  if (btnApplyMiningData) {
    btnApplyMiningData.addEventListener('click', applyMiningPlanInheritance);
  }

  // ====== FINANCIAL LEDGER & PRESETS ======
  const inpVal = document.getElementById('env_product_value');
  const inpPaid = document.getElementById('env_paid_amount');
  const inpPending = document.getElementById('env_pending_amount');
  const selStatus = document.getElementById('env_payment_status');
  const dispVal = document.getElementById('disp_env_product_val');
  const dispPaid = document.getElementById('disp_env_paid_val');
  const dispPending = document.getElementById('disp_env_pending_val');

  function calcFinance() {
    const v = parseFloat(inpVal.value) || 0;
    const p = parseFloat(inpPaid.value) || 0;
    const bal = Math.max(0, v - p);

    if (inpPending) inpPending.value = bal.toFixed(2);
    if (dispVal) dispVal.textContent = '₹ ' + v.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (dispPaid) dispPaid.textContent = '₹ ' + p.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (dispPending) dispPending.textContent = '₹ ' + bal.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    if (selStatus) {
      if (p <= 0) selStatus.value = 'pending';
      else if (bal <= 0 && v > 0) selStatus.value = 'paid';
      else selStatus.value = 'partial';
    }
  }

  if (inpVal) inpVal.addEventListener('input', calcFinance);
  if (inpPaid) inpPaid.addEventListener('input', calcFinance);

  // Quick Preset Buttons
  document.getElementById('btn_preset_full')?.addEventListener('click', function() {
    const v = parseFloat(inpVal.value) || 0;
    inpPaid.value = v.toFixed(2);
    calcFinance();
  });
  document.getElementById('btn_preset_half')?.addEventListener('click', function() {
    const v = parseFloat(inpVal.value) || 0;
    inpPaid.value = (v / 2).toFixed(2);
    calcFinance();
  });
  document.getElementById('btn_preset_zero')?.addEventListener('click', function() {
    inpPaid.value = '0.00';
    calcFinance();
  });

  calcFinance();

  // ====== DYNAMIC HANDLING TEAM ROWS ======
  let handlerIdx = 1;
  const btnAddHandler = document.getElementById('btn_add_env_handler');
  const handlersTbody = document.getElementById('env_handlers_tbody');

  if (btnAddHandler && handlersTbody) {
    btnAddHandler.addEventListener('click', function() {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-center fw-bold row-num">${handlerIdx + 1}</td>
        <td>
          <input type="text" name="handlers[${handlerIdx}][person_name]" class="form-control form-control-sm" placeholder="e.g. Subashini Devi" required>
        </td>
        <td>
          <input type="text" name="handlers[${handlerIdx}][role]" class="form-control form-control-sm" placeholder="e.g. Environmental Chemist" required>
        </td>
        <td>
          <input type="text" name="handlers[${handlerIdx}][notes]" class="form-control form-control-sm" placeholder="e.g. Sampling, Water testing">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-handler">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      `;
      handlersTbody.appendChild(tr);
      handlerIdx++;
      reindexHandlers();
    });

    handlersTbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const tr = btn.closest('tr');
        if (tr && handlersTbody.children.length > 1) {
          tr.remove();
          reindexHandlers();
        }
      }
    });
  }

  function reindexHandlers() {
    if (!handlersTbody) return;
    const rows = handlersTbody.querySelectorAll('tr');
    rows.forEach((row, i) => {
      const numCell = row.querySelector('.row-num');
      if (numCell) numCell.textContent = i + 1;

      const removeBtn = row.querySelector('.btn-remove-handler');
      if (removeBtn) {
        if (rows.length === 1) {
          removeBtn.classList.add('disabled');
          removeBtn.style.opacity = '0.4';
        } else {
          removeBtn.classList.remove('disabled');
          removeBtn.style.opacity = '1';
        }
      }
    });
  }

  // Toggle password visibility (text <-> password) & copy
  document.addEventListener('click', function(e) {
    const toggleBtn = e.target.closest('.btn-toggle-pass-visibility');
    if (toggleBtn) {
      const targetId = toggleBtn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (input) {
        const isPass = input.type === 'password';
        input.type = isPass ? 'text' : 'password';
        toggleBtn.innerHTML = isPass ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
      }
    }

    const copyBtn = e.target.closest('.btn-copy-pass');
    if (copyBtn) {
      const targetId = copyBtn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      if (input && input.value) {
        navigator.clipboard.writeText(input.value).then(() => {
          const origHtml = copyBtn.innerHTML;
          copyBtn.innerHTML = '<i class="fa fa-check text-success"></i>';
          setTimeout(() => { copyBtn.innerHTML = origHtml; }, 2000);
          if (typeof toastr !== 'undefined') toastr.info('Password copied to clipboard');
        });
      }
    }
  });
});
</script>
@endsection
