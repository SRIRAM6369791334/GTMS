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
                      <label class="form-label small fw-bold text-dark mb-2">Select B1 Sub Category *</label>
                      <div class="row g-2">
                        <div class="col-sm-6">
                          <label class="subcat-pill p-2 rounded border d-block" id="pill_sc1" for="radio_sc1" style="cursor:pointer; background:#f8fafc;">
                            <input type="radio" name="sub_category" value="SC1" id="radio_sc1" class="form-check-input me-1" {{ old('sub_category', $subCat) === 'SC1' || (!old('sub_category') && old('category') === 'B1') ? 'checked' : '' }}>
                            <span class="small fw-semibold">Sub Category 1</span>
                            <div class="text-muted" style="font-size:0.75rem;">ToR &amp; Mining Docs (5 Folders)</div>
                          </label>
                        </div>
                        <div class="col-sm-6">
                          <label class="subcat-pill p-2 rounded border d-block" id="pill_sc2" for="radio_sc2" style="cursor:pointer; background:#f8fafc;">
                            <input type="radio" name="sub_category" value="SC2" id="radio_sc2" class="form-check-input me-1" {{ old('sub_category', $subCat) === 'SC2' ? 'checked' : '' }}>
                            <span class="small fw-semibold">Sub Category 2</span>
                            <div class="text-muted" style="font-size:0.75rem;">EIA &amp; TNPCB (6 Folders)</div>
                          </label>
                        </div>
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

            {{-- Customer Unique ID / MIMAS Instant Lookup Card --}}
            <div class="col-md-12">
              <div class="p-3 rounded mb-2" style="background:#f0f7ff; border:2px dashed #93c5fd; border-radius:12px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <label class="form-label fw-bold mb-0 text-primary" style="font-size:0.92rem;">
                    <i class="fa fa-fingerprint me-1"></i> Customer Unique ID Instant Lookup
                  </label>
                  <span class="badge bg-primary text-white"><i class="fa fa-bolt me-1"></i> Autofill</span>
                </div>
                <p class="text-muted small mb-2">Type or select Customer Unique ID / MIMAS number to automatically populate client details, contact person, mobile, email, and quarry district.</p>
                <div class="input-group">
                  <span class="input-group-text bg-white border-primary"><i class="fa fa-search text-primary"></i></span>
                  <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold border-primary"
                    placeholder="Type Customer Unique ID (e.g. TN-MMS-SLM-001)" list="customer_datalist" autocomplete="off">
                  <select class="form-select border-primary" id="customer_select" name="customer_id" style="max-width:350px;">
                    <option value="">-- Or Choose from List --</option>
                    @foreach($customers as $c)
                      <option value="{{ $c->id }}"
                        data-client="{{ $c->customer_name }}"
                        data-company="{{ $c->company_name }}"
                        data-contact="{{ $c->secondary_contact_person ?: $c->customer_name }}"
                        data-phone="{{ $c->mobile_num }}"
                        data-email="{{ $c->email }}"
                        data-mimas="{{ $c->mimas_no }}"
                        data-district="{{ $c->district_id }}"
                        data-location="{{ $c->address }}"
                        {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->customer_name }} @if($c->company_name) ({{ $c->company_name }}) @endif — {{ $c->mimas_no ?: 'No MIMAS' }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <datalist id="customer_datalist">
                  @foreach($customers as $c)
                    @if($c->mimas_no)
                      <option value="{{ $c->mimas_no }}">{{ $c->company_name ?: $c->customer_name }}</option>
                    @endif
                  @endforeach
                </datalist>
                <div id="mimas_feedback" class="small mt-2" style="display:none;"></div>
              </div>
            </div>

            {{-- Client Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Client / Applicant Name *</label>
              <input type="text" class="form-control" name="client_name" id="client_name" value="{{ old('client_name') }}" required placeholder="e.g. R. Kumaresan">
            </div>

            {{-- Company Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Company / Enterprise Name</label>
              <input type="text" class="form-control" name="company_name" id="company_name" value="{{ old('company_name') }}" placeholder="e.g. Sri Bala Minerals &amp; Traders">
            </div>

            {{-- Project Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Project / Quarry Name *</label>
              <input type="text" class="form-control" name="project_name" id="project_name" value="{{ old('project_name') }}" required placeholder="e.g. Salem Rough Stone &amp; Gravel Quarry">
            </div>

            {{-- District --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">District *</label>
              <select class="form-select" name="district_id" id="district_id" required>
                <option value="">-- Select District --</option>
                @foreach($districts as $d)
                  <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
              </select>
            </div>

            {{-- Location / Survey Numbers --}}
            <div class="col-md-12">
              <label class="form-label fw-semibold">Location / Survey Numbers</label>
              <input type="text" class="form-control" name="location" id="location" value="{{ old('location') }}" placeholder="e.g. SF Nos. 124/1, 124/2, Thammampatti Village, Gangavalli Taluk">
            </div>

            {{-- Contact Person --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Person</label>
              <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" placeholder="Authorized representative">
            </div>

            {{-- Contact Phone --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Mobile Number *</label>
              <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" required maxlength="15" placeholder="10-digit mobile number">
            </div>

            {{-- Contact Email --}}
            <div class="col-md-4">
              <label class="form-label fw-semibold">Contact Email</label>
              <input type="email" class="form-control" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" placeholder="applicant@example.com">
            </div>

            {{-- Customer Unique ID / MIMAS --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Customer Unique ID / MIMAS No</label>
              <input type="text" class="form-control" name="mimas_no" id="mimas_no" value="{{ old('mimas_no') }}" placeholder="e.g. TN-MMS-SLM-001">
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
  const radioSc2 = document.getElementById('radio_sc2');

  const pillSc1 = document.getElementById('pill_sc1');
  const pillSc2 = document.getElementById('pill_sc2');
  const customerSelect = document.getElementById('customer_select');
  const projectNameInput = document.getElementById('project_name');

  function updateSubCategoryHighlight() {
    if (pillSc1 && pillSc2) {
      if (radioSc1 && radioSc1.checked) {
        pillSc1.style.borderColor = '#0F1E4D';
        pillSc1.style.background = '#eff6ff';
        pillSc2.style.borderColor = '#e2e8f0';
        pillSc2.style.background = '#f8fafc';
      } else if (radioSc2 && radioSc2.checked) {
        pillSc2.style.borderColor = '#0F1E4D';
        pillSc2.style.background = '#eff6ff';
        pillSc1.style.borderColor = '#e2e8f0';
        pillSc1.style.background = '#f8fafc';
      } else {
        pillSc1.style.borderColor = '#e2e8f0';
        pillSc1.style.background = '#f8fafc';
        pillSc2.style.borderColor = '#e2e8f0';
        pillSc2.style.background = '#f8fafc';
      }
    }
  }

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
      if (radioSc1 && radioSc2 && !radioSc1.checked && !radioSc2.checked) {
        radioSc1.checked = true;
      }
      updateSubCategoryHighlight();
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
      if (radioSc2) radioSc2.checked = false;
      updateSubCategoryHighlight();
    }
  }

  if (radioCatB1) radioCatB1.addEventListener('change', updateCategoryHighlight);
  if (radioCatB2) radioCatB2.addEventListener('change', updateCategoryHighlight);
  if (radioSc1) radioSc1.addEventListener('change', updateSubCategoryHighlight);
  if (radioSc2) radioSc2.addEventListener('change', updateSubCategoryHighlight);

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

  function applyCustomerAutofill(opt) {
    if (!opt || !opt.value) return;
    const clientNameInput = document.getElementById('client_name');
    const companyNameInput = document.getElementById('company_name');
    const contactNameInput = document.getElementById('contact_name');
    const contactPhoneInput = document.getElementById('contact_phone');
    const contactEmailInput = document.getElementById('contact_email');
    const mimasNoInput = document.getElementById('mimas_no');
    const districtSelect = document.getElementById('district_id');
    const locationInput = document.getElementById('location');

    if (clientNameInput) clientNameInput.value = opt.dataset.client || '';
    if (companyNameInput) companyNameInput.value = opt.dataset.company || '';
    if (contactNameInput) contactNameInput.value = opt.dataset.contact || opt.dataset.client || '';
    if (contactPhoneInput) contactPhoneInput.value = opt.dataset.phone || '';
    if (contactEmailInput) contactEmailInput.value = opt.dataset.email || '';
    if (mimasNoInput) mimasNoInput.value = opt.dataset.mimas || '';
    if (districtSelect && opt.dataset.district) districtSelect.value = opt.dataset.district;
    if (locationInput && opt.dataset.location) locationInput.value = opt.dataset.location;

    if (projectNameInput && (!projectNameInput.value || projectNameInput.dataset.autofilled === '1')) {
      const entityName = opt.dataset.company || opt.dataset.client || 'Mining';
      projectNameInput.value = entityName + ' Quarry Project';
      projectNameInput.dataset.autofilled = '1';
    }

    if (mimasFeedback) {
      mimasFeedback.style.display = 'block';
      mimasFeedback.className = 'small mt-2 text-success fw-bold';
      mimasFeedback.innerHTML = '<i class="fa fa-check-circle me-1"></i> Loaded: ' + (opt.dataset.company || opt.dataset.client) + ' (' + (opt.dataset.mimas || 'No MIMAS') + ')';
    }
  }

  if (customerSelect) {
    customerSelect.addEventListener('change', function() {
      const selected = this.options[this.selectedIndex];
      if (selected && selected.value) {
        if (selected.dataset.mimas && mimasSearchInput) {
          mimasSearchInput.value = selected.dataset.mimas;
        }
        applyCustomerAutofill(selected);
      }
    });
  }

  if (mimasSearchInput && customerSelect) {
    mimasSearchInput.addEventListener('input', function() {
      const val = this.value.trim().toUpperCase();
      if (!val) {
        if (mimasFeedback) mimasFeedback.style.display = 'none';
        return;
      }
      let found = false;
      for (let i = 0; i < customerSelect.options.length; i++) {
        const opt = customerSelect.options[i];
        const mimas = (opt.dataset.mimas || '').toUpperCase();
        const name = (opt.dataset.client || '').toUpperCase();
        const company = (opt.dataset.company || '').toUpperCase();
        if (mimas === val || (val.length >= 3 && (mimas.includes(val) || name.includes(val) || company.includes(val)))) {
          customerSelect.selectedIndex = i;
          applyCustomerAutofill(opt);
          found = true;
          break;
        }
      }
      if (!found && val.length >= 4 && mimasFeedback) {
        mimasFeedback.style.display = 'block';
        mimasFeedback.className = 'small mt-2 text-warning';
        mimasFeedback.innerHTML = '<i class="fa fa-info-circle me-1"></i> No matching registered customer found. You can enter details manually below.';
      }
    });
  }
});
</script>
@endsection
