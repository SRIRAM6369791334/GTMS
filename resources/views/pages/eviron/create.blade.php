@extends('layouts.app')
@section('title', 'New Environment Clearance Application')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<div class="content-body default-height">
  <div class="container-fluid">

    {{-- Breadcrumb & Title --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-md-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">New Application</a></li>
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

    <form method="POST" action="{{ route('eviron.store') }}" id="newEvironForm">
      @csrf

      {{-- ================= SECTION 1: CATEGORY SELECTION ================= --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
            <span class="badge rounded-circle bg-navy text-white me-2 px-2 py-1" style="background:#0F1E4D;">1</span>
            Select Clearance Category
          </h5>
          <small class="text-muted">Choose between B1 (EIA Study Required) or B2 (Direct Environment Clearance)</small>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">

            {{-- B1 Category Card --}}
            <div class="col-md-6">
              <div class="category-select-card h-100 p-3 rounded border position-relative" id="card_cat_b1" style="cursor:pointer; transition: all 0.2s;">
                <div class="d-flex align-items-start gap-3">
                  <input type="radio" name="category" value="B1" id="radio_cat_b1" class="form-check-input mt-1" {{ old('category', $category) === 'B1' ? 'checked' : '' }} required>
                  <div class="flex-grow-1">
                    <label for="radio_cat_b1" class="d-flex align-items-center justify-content-between mb-1" style="cursor:pointer; width:100%;">
                      <span class="fw-bold fs-6 text-navy" style="color:#0F1E4D;">Category B1</span>
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle">EIA Study Required</span>
                    </label>
                    <p class="text-muted small mb-2" onclick="document.getElementById('radio_cat_b1').click();">
                      Large-scale mining leases requiring Terms of Reference (ToR), EIA Baseline Data Collection, Public Hearing &amp; TNPCB submission.
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
                            <input type="radio" name="sub_category" value="SC1" id="radio_sc1" class="form-check-input mt-0" checked required>
                            <div>
                              <span class="small fw-bold text-success">Sub Category 1 (SC1)</span>
                              <div class="text-muted" style="font-size:0.75rem;">ToR &amp; Mining Documents (5 Folders)</div>
                            </div>
                          </div>
                          <span class="badge bg-success text-white" style="font-size:10px;">Stage 1 Active</span>
                        </div>
                      </div>
                      <div class="alert alert-info py-2 px-3 small mt-2 mb-0" style="font-size:0.78rem; background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
                        <i class="fa fa-info-circle me-1"></i> <strong>Sequential B1 Workflow:</strong> All B1 applications start exclusively at <strong>Sub Category 1</strong>. Once SC1 is completed and approved by the <strong>PPT Department (ToR Presentation)</strong>, Sub Category 2 will unlock automatically.
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

            {{-- B2 Category Card --}}
            <div class="col-md-6">
              <div class="category-select-card h-100 p-3 rounded border position-relative" id="card_cat_b2" style="cursor:pointer; transition: all 0.2s;">
                <div class="d-flex align-items-start gap-3">
                  <input type="radio" name="category" value="B2" id="radio_cat_b2" class="form-check-input mt-1" {{ old('category', $category) === 'B2' || (!old('category') && !$category) ? 'checked' : '' }} required>
                  <div class="flex-grow-1">
                    <label for="radio_cat_b2" class="d-flex align-items-center justify-content-between mb-1" style="cursor:pointer; width:100%;">
                      <span class="fw-bold fs-6 text-navy" style="color:#0F1E4D;">Category B2</span>
                      <span class="badge bg-success-subtle text-success border border-success-subtle">Direct EC</span>
                    </label>
                    <p class="text-muted small mb-2" onclick="document.getElementById('radio_cat_b2').click();">
                      Standard mining concessions with direct document preparation, Site Photographs (DGPS, Fencing, Greenbelt), Hydrogeological Report, and PARIVESH submission.
                    </p>
                    <div class="text-muted small mt-2">
                      <i class="fa fa-folder-tree me-1 text-primary"></i> 6 Folders: Documents, Site Photos, Report, GIS, Signed Reports, PARIVESH.
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- ================= SECTION 2: APPLICANT & QUARRY DETAILS ================= --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
            <span class="badge rounded-circle bg-navy text-white me-2 px-2 py-1" style="background:#0F1E4D;">2</span>
            Applicant &amp; Quarry Information
          </h5>
          <small class="text-muted">Enter client credentials and project location details</small>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">

            {{-- CUSTOMER UNIQUE ID LOOKUP CARD --}}
            <div class="col-md-12">
              <div class="card p-3 mb-3" style="background:#f0f7ff; border:2px dashed #93c5fd; border-radius:14px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <label class="form-label fw-bold mb-0 text-primary" style="font-size:0.92rem;">
                    <i class="fa fa-fingerprint me-1"></i> Customer Unique ID Lookup
                  </label>
                  <span class="badge bg-primary text-white"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
                </div>
                <p class="text-muted small mb-2">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
                
                <div class="input-group">
                  <span class="input-group-text bg-white border-primary"><i class="fa fa-search text-primary"></i></span>
                  <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold border-primary" 
                         placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
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
                <datalist id="customer_datalist">
                  @if(isset($customers))
                    @foreach($customers as $c)
                      <option value="{{ $c->mimas_no }}">{{ $c->company_name }} ({{ $c->customer_name }})</option>
                    @endforeach
                  @endif
                </datalist>

                <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
              </div>
            </div>

            <input type="hidden" name="customer_id" id="field_customer_id" value="{{ old('customer_id') }}">

            {{-- Client Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Client / Applicant Name *</label>
              <input type="text" class="form-control auto-filled-field" name="client_name" id="client_name" value="{{ old('client_name') }}" required placeholder="e.g. R. Kumaresan">
            </div>

            {{-- Company Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Company / Enterprise Name</label>
              <input type="text" class="form-control auto-filled-field" name="company_name" id="company_name" value="{{ old('company_name') }}" placeholder="e.g. Sri Bala Minerals &amp; Traders">
            </div>

            {{-- Project Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Project / Quarry Name *</label>
              <input type="text" class="form-control auto-filled-field" name="project_name" id="project_name" value="{{ old('project_name') }}" required placeholder="e.g. Salem Rough Stone &amp; Gravel Quarry">
            </div>

            {{-- District --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">District *</label>
              <select class="form-select auto-filled-field" name="district_id" id="district_id" required>
                <option value="">-- Select District --</option>
                @foreach($districts as $d)
                  <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Location / Survey Numbers --}}
            <div class="col-md-12">
              <label class="form-label fw-semibold">Location / Survey Numbers</label>
              <input type="text" class="form-control auto-filled-field" name="location" id="location" value="{{ old('location') }}" placeholder="e.g. SF Nos. 124/1, 124/2, Thammampatti Village, Gangavalli Taluk">
            </div>

            {{-- Contact Person --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Person</label>
              <input type="text" class="form-control auto-filled-field" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" placeholder="Authorized representative">
            </div>

            {{-- Contact Phone --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Mobile Number *</label>
              <input type="text" class="form-control auto-filled-field" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" required maxlength="15" placeholder="10-digit mobile number">
            </div>

            {{-- Contact Email --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Email</label>
              <input type="email" class="form-control auto-filled-field" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" placeholder="applicant@example.com">
            </div>

            {{-- Customer Unique ID / MIMAS --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Unique ID / MIMAS No</label>
              <input type="text" class="form-control auto-filled-field" name="mimas_no" id="mimas_no" value="{{ old('mimas_no') }}" placeholder="e.g. TN-MMS-SLM-001">
            </div>

          </div>
        </div>
      {{-- ================= SECTION 3: PROJECT HANDLING TEAM ================= --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
          <div>
            <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
              <span class="badge rounded-circle bg-navy text-white me-2 px-2 py-1" style="background:#0F1E4D;">3</span>
              Project Handling Team &amp; In-Charge Persons
            </h5>
            <small class="text-muted">Assign environmental coordinators, field officers, EIA coordinators, and liaison personnel</small>
          </div>
          <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_env_handler" style="background:#0F1E4D; color:#fff;">
            <i class="fa fa-user-plus me-1 text-warning"></i> + Add Person
          </button>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
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
          <div class="alert alert-info py-2 px-3 small rounded-2 mb-0" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
            <i class="fa fa-info-circle me-1"></i> You can type custom roles manually (e.g. <em>EIA Coordinator, Field Geologist, Environmental Chemist, Documentation In-Charge</em>).
          </div>
        </div>
      </div>

      {{-- ================= SECTION 4: PAYMENT & FINANCIAL SETTLEMENT ================= --}}
      <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
            <span class="badge rounded-circle bg-navy text-white me-2 px-2 py-1" style="background:#0F1E4D;">4</span>
            Payment &amp; Financial Settlement
          </h5>
          <small class="text-muted">Record statutory clearance quotation, client advance paid, and track pending settlement</small>
        </div>
        <div class="card-body p-4">
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

          <div class="row g-3">
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
        </div>
      </div>

      {{-- Action Buttons --}}
      <div class="d-flex justify-content-between align-items-center pb-5">
        <a href="{{ route('eviron.index') }}" class="btn btn-outline-secondary px-4">
          <i class="fa fa-times me-1"></i> Cancel
        </a>
        <button type="submit" class="btn btn-navy px-5" id="btnSubmitEviron" style="background:#0F1E4D; color:#fff;">
          <i class="fa fa-check-circle me-1"></i> Create Project &amp; Initialize Folders
        </button>
      </div>

    </form>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const radioCatB1 = document.getElementById('radio_cat_b1');
  const radioCatB2 = document.getElementById('radio_cat_b2');
  const cardCatB1 = document.getElementById('card_cat_b1');
  const cardCatB2 = document.getElementById('card_cat_b2');
  const b1SubBox = document.getElementById('b1_subcategory_box');
  const radioSc1 = document.getElementById('radio_sc1');
  const pillSc1 = document.getElementById('pill_sc1');
  const customerSelect = document.getElementById('customer_select');
  const projectNameInput = document.getElementById('project_name');

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
      if (radioSc1) radioSc1.checked = true;
    } else if (radioCatB2 && radioCatB2.checked) {
      if (cardCatB2) {
        cardCatB2.style.borderColor = '#0F1E4D';
        cardCatB2.style.background = '#f8fafc';
      }
      if (cardCatB1) {
        cardCatB1.style.borderColor = '#e2e8f0';
        cardCatB1.style.background = '#ffffff';
      }
      if (b1SubBox) b1SubBox.classList.add('d-none');
      if (radioSc1) radioSc1.checked = false;
    }
  }

  if (radioCatB1) radioCatB1.addEventListener('change', updateCategoryHighlight);
  if (radioCatB2) radioCatB2.addEventListener('change', updateCategoryHighlight);

  // Card click activation
  if (cardCatB1) {
    cardCatB1.addEventListener('click', function(e) {
      if (b1SubBox && b1SubBox.contains(e.target)) return;
      if (radioCatB1 && !radioCatB1.checked) {
        radioCatB1.checked = true;
        updateCategoryHighlight();
      }
    });
  }

  if (cardCatB2) {
    cardCatB2.addEventListener('click', function() {
      if (radioCatB2 && !radioCatB2.checked) {
        radioCatB2.checked = true;
        updateCategoryHighlight();
      }
    });
  }

  updateCategoryHighlight();

  const mimasSearchInput = document.getElementById('mimas_search_input');
  const mimasFeedback = document.getElementById('mimas_feedback');

  if (projectNameInput) {
    projectNameInput.addEventListener('input', function() {
      this.dataset.autofilled = '0';
    });
  }

  function applyCustomerAutofill(c) {
    if (!c) return;
    const clientNameInput = document.getElementById('client_name');
    const companyNameInput = document.getElementById('company_name');
    const contactNameInput = document.getElementById('contact_name');
    const contactPhoneInput = document.getElementById('contact_phone');
    const contactEmailInput = document.getElementById('contact_email');
    const mimasNoInput = document.getElementById('mimas_no');
    const districtSelect = document.getElementById('district_id');
    const locationInput = document.getElementById('location');
    const customerIdInput = document.getElementById('field_customer_id');

    if (customerIdInput) customerIdInput.value = c.id || '';
    if (clientNameInput) clientNameInput.value = c.customer_name || '';
    if (companyNameInput) companyNameInput.value = c.company_name || '';
    if (contactNameInput) contactNameInput.value = c.secondary_contact_person || c.customer_name || '';
    if (contactPhoneInput) contactPhoneInput.value = c.mobile_num || '';
    if (contactEmailInput) contactEmailInput.value = c.email || '';
    if (mimasNoInput) mimasNoInput.value = c.mimas_no || '';
    if (districtSelect && c.district_id) districtSelect.value = c.district_id;
    if (locationInput) locationInput.value = c.address || '';

    if (projectNameInput) {
      const entityName = c.company_name || c.customer_name || 'Mining';
      projectNameInput.value = entityName + ' Quarry Project';
      projectNameInput.dataset.autofilled = '1';
    }

    // Visual highlight on auto-filled fields
    document.querySelectorAll('.auto-filled-field').forEach(el => {
      el.classList.add('field-autofilled');
      setTimeout(() => el.classList.remove('field-autofilled'), 3000);
    });

    const feedbackBox = document.getElementById('mimas_feedback_box');
    if (feedbackBox) {
      feedbackBox.style.display = 'block';
      feedbackBox.innerHTML = `
        <div class="alert alert-success py-2 px-3 mb-0 small d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-check-circle me-1 text-success"></i>
            <strong>Customer Profile Loaded:</strong> ${c.company_name || c.customer_name}
            &middot; <span class="text-muted">${c.district_name || 'District'}</span>
            &middot; <span class="badge bg-success-subtle text-success border border-success ms-1">Customer ID: ${c.mimas_no || c.id}</span>
          </div>
          <span class="badge bg-success text-white">All details populated</span>
        </div>
      `;
    }
  }

  function performMimasLookup() {
    const searchInput = document.getElementById('mimas_search_input');
    const btn = document.getElementById('btn_lookup_mimas');
    const feedbackBox = document.getElementById('mimas_feedback_box');
    const val = searchInput ? searchInput.value.trim() : '';

    if (!val) {
      if (feedbackBox) {
        feedbackBox.style.display = 'block';
        feedbackBox.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-exclamation-triangle me-1"></i> Please enter or select a Customer Unique ID first.</div>';
      }
      return;
    }

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Searching...';
    }
    if (feedbackBox) feedbackBox.style.display = 'none';

    fetch('/customers/lookup-mimas/' + encodeURIComponent(val))
      .then(res => res.json())
      .then(res => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (res.status === 1 && res.data) {
          applyCustomerAutofill(res.data);
        } else {
          if (feedbackBox) {
            feedbackBox.style.display = 'block';
            feedbackBox.innerHTML = `<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-info-circle me-1"></i> ${res.message || 'No customer found.'}</div>`;
          }
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (feedbackBox) {
          feedbackBox.style.display = 'block';
          feedbackBox.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small"><i class="fa fa-times-circle me-1"></i> Customer not found. You can enter details manually below.</div>';
        }
      });
  }

  const btnLookup = document.getElementById('btn_lookup_mimas');
  const searchInput = document.getElementById('mimas_search_input');

  if (btnLookup) btnLookup.addEventListener('click', performMimasLookup);
  if (searchInput) {
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performMimasLookup();
      }
    });
    searchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performMimasLookup();
      }
    });
  }

  // ====== DYNAMIC HANDLERS TABLE ======
  const btnAddHandler = document.getElementById('btn_add_env_handler');
  const handlersTbody = document.getElementById('env_handlers_tbody');

  function reindexHandlers() {
    const rows = handlersTbody.querySelectorAll('tr');
    rows.forEach((r, idx) => {
      const numCell = r.querySelector('.row-num');
      if (numCell) numCell.textContent = idx + 1;
      const inputs = r.querySelectorAll('input');
      inputs.forEach(inp => {
        if (inp.name.includes('[person_name]')) inp.name = `handlers[${idx}][person_name]`;
        if (inp.name.includes('[role]')) inp.name = `handlers[${idx}][role]`;
        if (inp.name.includes('[notes]')) inp.name = `handlers[${idx}][notes]`;
      });
      const removeBtn = r.querySelector('.btn-remove-handler');
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

  if (btnAddHandler && handlersTbody) {
    btnAddHandler.addEventListener('click', function() {
      const currentRows = handlersTbody.querySelectorAll('tr').length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-center fw-bold row-num">${currentRows + 1}</td>
        <td>
          <input type="text" name="handlers[${currentRows}][person_name]" class="form-control form-control-sm" placeholder="e.g. Ramesh Kumar">
        </td>
        <td>
          <input type="text" name="handlers[${currentRows}][role]" class="form-control form-control-sm" placeholder="e.g. EIA Coordinator, Field Officer">
        </td>
        <td>
          <input type="text" name="handlers[${currentRows}][notes]" class="form-control form-control-sm" placeholder="e.g. Site inspection, Liaison">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-handler">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      `;
      handlersTbody.appendChild(tr);
      reindexHandlers();
    });

    handlersTbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const row = btn.closest('tr');
        if (row && handlersTbody.querySelectorAll('tr').length > 1) {
          row.remove();
          reindexHandlers();
        }
      }
    });
  }

  // ====== REAL-TIME PAYMENT LEDGER ======
  const inpVal = document.getElementById('env_product_value');
  const inpPaid = document.getElementById('env_paid_amount');
  const inpPending = document.getElementById('env_pending_amount');
  const selStatus = document.getElementById('env_payment_status');

  const dispVal = document.getElementById('disp_env_product_val');
  const dispPaid = document.getElementById('disp_env_paid_val');
  const dispPending = document.getElementById('disp_env_pending_val');

  function calculatePayment() {
    const val = parseFloat(inpVal ? inpVal.value : 0) || 0;
    const paid = parseFloat(inpPaid ? inpPaid.value : 0) || 0;
    const pending = Math.max(0, val - paid);

    if (inpPending) inpPending.value = pending.toFixed(2);
    if (dispVal) dispVal.textContent = '₹ ' + val.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (dispPaid) dispPaid.textContent = '₹ ' + paid.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    if (dispPending) dispPending.textContent = '₹ ' + pending.toLocaleString('en-IN', { minimumFractionDigits: 2 });

    if (selStatus) {
      if (val > 0) {
        if (paid >= val) {
          selStatus.value = 'paid';
        } else if (paid > 0) {
          selStatus.value = 'partial';
        } else {
          selStatus.value = 'pending';
        }
      }
    }
  }

  if (inpVal && inpPaid) {
    inpVal.addEventListener('input', calculatePayment);
    inpPaid.addEventListener('input', calculatePayment);
    calculatePayment();
  }
});
</script>
@endsection
