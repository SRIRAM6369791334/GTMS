@extends('layouts.app')
@section('title', 'Lease Application - Step 1 | Applicant Identity & Mining Concession')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid py-3">

    <!-- UNIQUE SCOPED CONTAINER FOR STEP 1 (Guarantees zero CSS leakage to other steps/pages) -->
    <div id="gtms-step1-page" class="wizard-wrap">

      <!-- ========================================================= -->
      <!-- STEPPER PROGRESS BAR (NATIVE GTMS HARMONIZED)             -->
      <!-- ========================================================= -->
      <div class="step-progress">
        <div class="sp-step active"><div class="circ">1</div><div class="sp-label">Application</div></div>
        <div class="sp-step"><div class="circ">2</div><div class="sp-label">Basic &amp; MIMAS</div></div>
        <div class="sp-step"><div class="circ">3</div><div class="sp-label">Category</div></div>
        <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
        <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
        <div class="sp-step"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
        <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
        <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
      </div>

      <!-- ========================================================= -->
      <!-- MAIN STEP 1 WIZARD CARD                                   -->
      <!-- ========================================================= -->
      <div class="wizard-card s1-main-card">

        <!-- HEADER BANNER -->
        <div class="s1-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
            <div class="wc-eyebrow s1-eyebrow">
              <i class="bi bi-shield-check me-1"></i> Step 1 of 8 &bull; Dossier Initiation
            </div>
            <span class="s1-time-tag">
              <i class="bi bi-clock-history me-1"></i> Est. ~2 mins
            </span>
          </div>
          <h4 class="s1-title">Applicant Identity &amp; Mineral Concession</h4>
          <div class="wc-sub s1-subtitle">
            Start by entering the applicant's official identity, mineral concession types, and statutory tax credentials.
          </div>
        </div>

        <!-- ========================================================= -->
        <!-- CUSTOMER UNIQUE ID / MIMAS LOOKUP CARD                    -->
        <!-- ========================================================= -->
        <div class="s1-lookup-card mb-3.5 p-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
            <label class="s1-lookup-label fw-bold mb-0" for="mimas_search_input">
              <i class="bi bi-fingerprint me-1.5"></i> Customer Unique ID Lookup
            </label>
            <span class="badge s1-lookup-badge">
              <i class="bi bi-lightning-charge-fill me-1"></i> Instant Autofill
            </span>
          </div>
          <p class="s1-lookup-desc mt-2 mb-2.5">
            Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.
          </p>

          <div class="input-group s1-input-group s1-lookup-input-group">
            <span class="input-group-text s1-addon s1-lookup-addon"><i class="bi bi-search"></i></span>
            <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold s1-input s1-lookup-field" 
                   placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
            <button class="btn s1-btn-fetch px-3 fw-bold" type="button" id="btn_lookup_mimas">
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

        <!-- ========================================================= -->
        <!-- MAIN STEP 1 FORM                                          -->
        <!-- ========================================================= -->
        <form id="step1_form" method="POST" action="{{ route('step1.save') }}">
          @csrf
          @if(!empty($draft['step1']))
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              @foreach($draft['step1'] as $key => $value)
                @if(!is_array($value))
                  var el = document.getElementById('field_{{ $key }}');
                  if (el) el.value = '{!! addslashes($value) !!}';
                @endif
              @endforeach
            });
          </script>
          @endif
          <input type="hidden" name="customer_id" id="field_customer_id">
          <input type="hidden" name="mimas_no" id="field_mimas_no">

          <!-- SECTION 1: APPLICANT & ENTERPRISE PROFILE -->
          <div class="s1-section-card mb-3">
            <div class="s1-section-header">
              <i class="bi bi-person-badge text-primary me-2"></i>
              <span class="s1-section-title">Applicant &amp; Business Profile</span>
            </div>

            <div class="row gx-3 gy-3 s1-form-row">
              <div class="col-md-6">
                <label class="form-label s1-label" for="field_client_name">
                  Client / Representative Name <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_client_name" name="client_name" placeholder="e.g. R. Kumaresan" required>
                </div>
                <div class="form-text-hint s1-hint">As it appears on applicant's official government ID proof.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_company_name">
                  Company / Quarry Name&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-building"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_company_name" name="company_name" placeholder="e.g. Sri Bala Traders">
                </div>
                <div class="form-text-hint s1-hint">Quarry, firm, or entity name if applicable.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_district_id">
                  District Jurisdiction <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-geo-alt"></i></span>
                  <select class="form-select s1-input auto-filled-field" id="field_district_id" name="district_id" required>
                    <option value="" selected disabled>Select District Mining Office</option>
                    @if(isset($districts) && $districts->isNotEmpty())
                      @foreach($districts as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="form-text-hint s1-hint">District Mining Office where the lease is situated.</div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: MINERAL CONCESSION TYPE(S) -->
          <div class="s1-section-card mb-3">
            <div class="s1-section-header d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center">
                <i class="bi bi-gem text-warning me-2"></i>
                <span class="s1-section-title">Mineral Concession Type(s) <span class="text-danger ms-0.5">*</span></span>
              </div>
              <span class="badge s1-multi-badge">
                <i class="bi bi-check2-all me-1"></i>Multi-Select Enabled
              </span>
            </div>

            <div class="row g-2 mt-0.5" id="minerals_selection_grid">
              @php
                $selectedMineralIds = [];
                if (!empty($draft['step1']['mineral_ids']) && is_array($draft['step1']['mineral_ids'])) {
                    $selectedMineralIds = $draft['step1']['mineral_ids'];
                } elseif (!empty($draft['step1']['mineral_id'])) {
                    $selectedMineralIds = [(int)$draft['step1']['mineral_id']];
                }
              @endphp
              @if(isset($minerals))
                @foreach($minerals as $m)
                  @php
                    $isOther = (strtolower($m->name) === 'others' || strtolower($m->name) === 'other');
                    $isChecked = in_array($m->id, $selectedMineralIds);
                  @endphp
                  <div class="col-6 col-md-3">
                    <input type="checkbox" class="btn-check mineral-checkbox" name="mineral_ids[]"
                           id="mineral_{{ $m->id }}" value="{{ $m->id }}"
                           data-name="{{ $m->name }}"
                           data-is-other="{{ $isOther ? '1' : '0' }}"
                           {{ $isChecked ? 'checked' : '' }}>
                    <label class="btn s1-mineral-pill w-100 text-start d-flex align-items-center justify-content-between" for="mineral_{{ $m->id }}">
                      <span class="d-flex align-items-center text-truncate me-1">
                        <i class="bi bi-gem s1-gem-icon"></i>
                        <span class="fw-semibold text-truncate s1-mineral-name">{{ $m->name }}</span>
                      </span>
                      <span class="s1-check-badge">
                        <i class="bi bi-check-lg"></i>
                      </span>
                    </label>
                  </div>
                @endforeach
              @endif
            </div>

            <!-- Dynamic Manual Entry for "Others" -->
            @php
              $hasOtherChecked = in_array(8, $selectedMineralIds) || !empty($draft['step1']['other_mineral_name']);
            @endphp
            <div id="other_mineral_box" class="mt-2.5 p-3 rounded-3 s1-other-box" style="display: {{ $hasOtherChecked ? 'block' : 'none' }};">
              <label class="form-label s1-label fw-bold mb-1" for="field_other_mineral_name">
                <i class="bi bi-pencil-square text-primary me-1"></i> Specify Other Mineral Name <span class="text-danger ms-0.5">*</span>
              </label>
              <input type="text" class="form-control s1-input" id="field_other_mineral_name" name="other_mineral_name" 
                     placeholder="Type manual mineral name (e.g. Quartzite, Silica Sand, Black Granite...)"
                     value="{{ $draft['step1']['other_mineral_name'] ?? '' }}">
              <div class="form-text-hint s1-hint mt-1.5">
                <i class="bi bi-info-circle me-1"></i>Specify the mineral name when "Others" is selected. Stored in statutory dossier.
              </div>
            </div>
          </div>

          <!-- SECTION 3: CONTACT INFORMATION -->
          <div class="s1-section-card mb-3">
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
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_mobile_num" name="mobile_num" placeholder="10-digit primary phone number" required>
                </div>
                <div class="form-text-hint s1-hint">Primary phone for SMS alerts and official notices.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_email">
                  Email Address&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control s1-input auto-filled-field" id="field_email" name="email" placeholder="customer@example.com">
                </div>
                <div class="form-text-hint s1-hint">Receives e-copies of proceedings and fee receipts.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_secondary_contact_person">
                  Secondary Contact Person&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-person-gear"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_secondary_contact_person" name="secondary_contact_person" placeholder="e.g. Quarry Manager / Site In-charge">
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_secondary_mobile_num">
                  Secondary Phone Number&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-phone"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_secondary_mobile_num" name="secondary_mobile_num" placeholder="10-digit secondary phone number" maxlength="15">
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION 4: STATUTORY, TAX & LAND DIMENSIONS -->
          <div class="s1-section-card mb-3">
            <div class="s1-section-header">
              <i class="bi bi-shield-check text-success me-2"></i>
              <span class="s1-section-title">Statutory Identifiers &amp; Land Extent</span>
            </div>

            <div class="row gx-3 gy-3 s1-form-row">
              <div class="col-md-6">
                <label class="form-label s1-label" for="field_pan">
                  PAN Number <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-credit-card-2-front"></i></span>
                  <input type="text" class="form-control text-uppercase s1-input auto-filled-field" id="field_pan" name="pan" placeholder="AAACS1234F" maxlength="10" required>
                </div>
                <div class="form-text-hint s1-hint">10-character alphanumeric Income Tax PAN.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_aadhaar_no">
                  Aadhaar Number <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-person-vcard"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field aadhaar-format" id="field_aadhaar_no" name="aadhaar_no" placeholder="9876-5432-1012" maxlength="14" required>
                </div>
                <div class="form-text-hint s1-hint">12-digit UIDAI number (automatically formatted).</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_gstin">
                  GSTIN (15 Digits)&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-receipt"></i></span>
                  <input type="text" class="form-control text-uppercase s1-input auto-filled-field" id="field_gstin" name="gstin" placeholder="33AAACS1234F1Z5" maxlength="15">
                </div>
                <div class="form-text-hint s1-hint">15-character Goods and Services Tax Identification Number.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s1-label" for="field_area">
                  Quarry Area (Hectares)&nbsp;<span class="s1-opt-tag">(Optional)</span>
                </label>
                <div class="input-group s1-input-group">
                  <span class="input-group-text s1-addon"><i class="bi bi-bounding-box-circles"></i></span>
                  <input type="text" class="form-control s1-input auto-filled-field" id="field_area" name="area" placeholder="e.g. 1.90.57 or 14.20" pattern="^[0-9]+(\.[0-9]+)*$" title="Enter standard decimal (e.g. 1.05) or Hectare-Are-Sq.M format (e.g. 1.90.57)">
                </div>
                <div class="form-text-hint s1-hint">Accepts decimal (e.g. 1.05) or Patta Revenue format (e.g. 1.90.57).</div>
              </div>

              <div class="col-12">
                <label class="form-label s1-label" for="field_address">
                  Registered Business Address&nbsp;<span class="s1-opt-tag">(Official Postal Address)</span>
                </label>
                <textarea class="form-control s1-input s1-textarea auto-filled-field" id="field_address" name="address" rows="3" placeholder="Street, Taluk, Village, Pincode..."></textarea>
              </div>
            </div>
          </div>

          <!-- SYSTEM NOTICE PANEL -->
          <div class="s1-notice-card mt-3 mb-0 p-3">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-info-circle-fill s1-notice-icon m-4"></i>
              <div class="s1-notice-text">
                An <b>application number</b> will be auto-generated once you save this step (format: <code class="s1-code">LA-YYYY-NNNN</code>). You'll use it to track the application through validation, approval and archiving.
              </div>
            </div>
          </div>

        </form>

        <!-- ========================================================= -->
        <!-- WIZARD ACTIONS FOOTER                                     -->
        <!-- ========================================================= -->
        <div class="wizard-actions s1-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3">
          <a href="{{ route('application.index') }}" class="btn btn-outline-secondary s1-btn-cancel">
            <i class="bi bi-x-lg me-1"></i> Cancel
          </a>
          @can('application.create')
          <div class="d-flex align-items-center gap-2">
            <button type="button" id="btn_save_later_step1" class="btn s1-btn-draft px-3">
              <i class="bi bi-save me-1"></i> Save &amp; Continue Later
            </button>
            <button type="button" id="btn_save_step1" class="btn s1-btn-submit px-4">
              <span>Save &amp; Continue</span>
              <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
          @endcan
        </div>

      </div>

    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- SCOPED CSS FOR STEP 1: ZERO IMPACT ON OTHER PAGES, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step1-page)
     ========================================================================== */
  #gtms-step1-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */
  #gtms-step1-page .step-progress {
    margin-bottom: 22px;
  }

  #gtms-step1-page .step-progress .sp-label {
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 6px;
    line-height: 1.2;
  }

  #gtms-step1-page .step-progress .circ {
    width: 32px;
    height: 32px;
    font-size: 0.80rem;
    line-height: 1;
  }

  /* MAIN CARD */
  #gtms-step1-page .s1-main-card {
    border-radius: 14px;
    padding: 26px 30px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
  }

  @media (max-width: 768px) {
    #gtms-step1-page .s1-main-card {
      padding: 20px 16px;
    }
  }

  #gtms-step1-page .s1-header-block {
    margin-bottom: 20px;
  }

  #gtms-step1-page .s1-eyebrow {
    color: var(--orange, #ea580c);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
    margin-bottom: 0;
  }

  #gtms-step1-page .s1-time-tag {
    font-size: 0.73rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.2;
  }

  #gtms-step1-page .s1-title {
    font-weight: 800;
    font-size: 1.25rem;
    color: #0f172a;
    line-height: 1.3;
    margin-top: 6px;
    margin-bottom: 5px;
  }

  #gtms-step1-page .s1-subtitle {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0;
  }

  /* LOOKUP CARD - LIGHT */
  #gtms-step1-page .s1-lookup-card {
    background: #f0f7ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 22px;
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-lookup-label {
    font-size: 0.88rem;
    color: #1e40af;
    line-height: 1.2;
  }

  #gtms-step1-page .s1-lookup-badge {
    background-color: #2563eb;
    color: #ffffff;
    font-size: 0.70rem;
    padding: 3px 7px;
    border-radius: 5px;
    font-weight: 600;
  }

  #gtms-step1-page .s1-lookup-desc {
    color: #475569;
    font-size: 0.80rem;
    line-height: 1.45;
  }

  #gtms-step1-page .s1-lookup-input-group .s1-lookup-addon {
    width: 42px;
    height: 42px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
  }

  #gtms-step1-page .s1-lookup-input-group .s1-lookup-field {
    height: 42px;
    border-left: none;
    border-right: none;
    border-radius: 0;
    font-size: 0.88rem;
    padding: 8px 12px;
  }

  #gtms-step1-page .s1-lookup-input-group .s1-btn-fetch {
    background-color: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    font-size: 0.84rem;
    height: 40px;
    border-radius: 0 8px 8px 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 18px;
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-btn-fetch:hover {
    background-color: #081233;
    border-color: #081233;
    color: #ffffff;
  }

  /* SECTION PANELS - LIGHT */
  #gtms-step1-page .s1-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 18px;
    transition: border-color 0.2s ease;
  }

  #gtms-step1-page .s1-section-card:hover {
    border-color: #cbd5e1;
  }

  #gtms-step1-page .s1-section-header {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    line-height: 1.2;
  }

  #gtms-step1-page .s1-label {
    font-size: 0.80rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.25;
    display: flex;
    align-items: center;
  }

  #gtms-step1-page .s1-form-row {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1.25rem;
  }

  #gtms-step1-page .s1-opt-tag {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: normal;
  }

  #gtms-step1-page .s1-hint {
    font-size: 0.72rem;
    color: #64748b;
    line-height: 1.4;
    margin-top: 6px;
    margin-bottom: 2px;
  }

  /* INPUT GROUPS - LIGHT */
  #gtms-step1-page .s1-input-group {
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10);
    border-radius: 8px;
  }

  #gtms-step1-page .s1-input-group .s1-addon {
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

  #gtms-step1-page .s1-input-group .s1-input {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    /* border-radius: 0 8px 8px 0; */
    font-size: 0.86rem;
    height: 40px;
    padding: 8px 12px;
    color: #0f172a;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-step1-page .s1-textarea {
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 8px !important;
    font-size: 0.86rem;
    padding: 10px 14px !important;
    min-height: 74px !important;
    line-height: 1.5;
    color: #0f172a;
    background-color: #ffffff;
    transition: all 0.2s ease;
    width: 100%;
  }

  #gtms-step1-page .s1-textarea:focus {
    border-color: #0F1E4D !important;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10) !important;
    outline: none;
  }

  #gtms-step1-page .s1-input-group:focus-within .s1-addon,
  #gtms-step1-page .s1-input-group:focus-within .s1-input {
    border-color: #0F1E4D !important;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-step1-page .s1-input-group:focus-within .s1-addon {
    color: #0F1E4D !important;
    background-color: #ffffff !important;
  }

  /* MINERAL PILLS - LIGHT */
  #gtms-step1-page .s1-multi-badge {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.69rem;
    padding: 3px 7px;
    border-radius: 4px;
    font-weight: 600;
    line-height: 1;
  }

  #gtms-step1-page .s1-mineral-pill {
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    border-radius: 8px;
    cursor: pointer;
    height: 42px;
    min-height: 42px;
    padding: 0 10px 0 12px;
    margin-bottom: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.18s ease-in-out;
  }

  #gtms-step1-page .s1-mineral-pill:hover {
    border-color: #0F1E4D;
    background: #f8fafc;
    transform: translateY(-1px);
  }

  #gtms-step1-page .s1-gem-icon {
    color: #64748b;
    font-size: 0.92rem;
    width: 18px;
    margin-right: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    flex-shrink: 0;
    transition: color 0.18s ease;
  }

  #gtms-step1-page .s1-mineral-name {
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1;
  }

  #gtms-step1-page .s1-check-badge {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #f1f5f9;
    color: transparent;
    border: 1px solid #cbd5e1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    transition: all 0.18s ease;
    flex-shrink: 0;
    margin-left: 4px;
  }

  #gtms-step1-page .btn-check:checked + .s1-mineral-pill {
    background-color: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(15, 30, 77, 0.20);
  }

  #gtms-step1-page .btn-check:checked + .s1-mineral-pill .s1-gem-icon {
    color: #fbbf24 !important;
  }

  #gtms-step1-page .btn-check:checked + .s1-mineral-pill .s1-check-badge {
    background-color: #ffffff !important;
    color: #0F1E4D !important;
    border-color: #ffffff !important;
  }

  #gtms-step1-page .s1-other-box {
    background: #f8fafc;
    border: 1.5px dashed #93c5fd;
    padding: 12px 14px;
    border-radius: 8px;
  }

  /* NOTICE CARD - LIGHT */
  #gtms-step1-page .s1-notice-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 18px;
  }

  #gtms-step1-page .s1-notice-icon {
    color: #1d4ed8;
    font-size: 1.15rem;
    line-height: 1;
    flex-shrink: 0;
  }

  #gtms-step1-page .s1-notice-text {
    font-size: 0.79rem;
    color: #1e3a8a;
    line-height: 1.45;
  }

  #gtms-step1-page .s1-code {
    background: #dbeafe;
    color: #1e40af;
    padding: 1px 5px;
    border-radius: 4px;
    font-size: 0.78rem;
  }

  /* ACTIONS - LIGHT */
  #gtms-step1-page .s1-actions {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid #e2e8f0;
  }

  #gtms-step1-page .s1-btn-cancel {
    font-size: 0.83rem;
    font-weight: 600;
    border-radius: 8px;
    height: 40px;
    padding: 0 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #cbd5e1;
    color: #64748b;
    background: #ffffff;
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-btn-cancel:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: #fef2f2;
  }

  #gtms-step1-page .s1-btn-draft {
    border: 1.5px solid #0F1E4D;
    color: #0F1E4D;
    background: transparent;
    font-size: 0.83rem;
    font-weight: 600;
    border-radius: 8px;
    height: 40px;
    padding: 0 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-btn-draft:hover {
    background: #0F1E4D;
    color: #ffffff;
  }

  #gtms-step1-page .s1-btn-submit {
    background: #0F1E4D;
    border: 1.5px solid #0F1E4D;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 8px;
    height: 40px;
    padding: 0 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
    transition: all 0.2s ease;
  }

  #gtms-step1-page .s1-btn-submit:hover {
    background: #081233;
    border-color: #081233;
    color: #ffffff;
  }

  #gtms-step1-page .field-autofilled {
    background-color: #f0fdf4 !important;
    border-color: #86efac !important;
    transition: all 0.4s ease;
  }

  /* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"] #gtms-step1-page)
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step1-page .s1-main-card {
    background-color: #202020 !important;
    border-color: #2d3139 !important;
    color: #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-title {
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-subtitle {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-time-tag {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
  }

  /* LOOKUP CARD - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-lookup-card {
    background: #181c26 !important;
    border-color: #2a3d66 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-lookup-label {
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-lookup-desc {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-lookup-input-group .s1-lookup-addon {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-lookup-input-group .s1-lookup-field {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-fetch {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-fetch:hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
  }

  /* SECTION PANELS - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-section-card {
    background: #1a1b20 !important;
    border-color: #2d3139 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-section-card:hover {
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-section-header {
    color: #f8fafc !important;
    border-bottom-color: #262930 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-label {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-opt-tag {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-hint {
    color: #94a3b8 !important;
  }

  /* INPUTS & ADDONS - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18) !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group .s1-addon {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-right: none !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group .s1-input {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-left: none !important;
    color: #f8fafc !important;
    box-shadow: none !important;
    outline: none !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group:focus-within .s1-addon,
  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group:focus-within .s1-input {
    border-color: #60a5fa !important;
    box-shadow: none !important;
    outline: none !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-input-group:focus-within .s1-addon {
    color: #60a5fa !important;
    background-color: #161717 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-input::placeholder {
    color: #64748b !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-textarea {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-textarea:focus {
    border-color: #60a5fa !important;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18) !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-textarea::placeholder {
    color: #64748b !important;
  }

  /* MINERAL PILLS - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-multi-badge {
    background-color: #1e293b !important;
    color: #60a5fa !important;
    border-color: #2563eb !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-mineral-pill {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #e2e8f0 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-mineral-pill:hover {
    border-color: #60a5fa !important;
    background: #1e2433 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-gem-icon {
    color: #94a3b8 !important;
    width: 18px !important;
    margin-right: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-check-badge {
    background: #262930 !important;
    border-color: #374151 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .btn-check:checked + .s1-mineral-pill {
    background-color: #2563eb !important;
    border-color: #60a5fa !important;
    color: #ffffff !important;
    box-shadow: 0 3px 10px rgba(37, 99, 235, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .btn-check:checked + .s1-mineral-pill .s1-gem-icon {
    color: #fef08a !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .btn-check:checked + .s1-mineral-pill .s1-check-badge {
    background-color: #ffffff !important;
    color: #2563eb !important;
    border-color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-other-box {
    background: #161717 !important;
    border-color: #2563eb !important;
  }

  /* NOTICE CARD - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-notice-card {
    background: #172033 !important;
    border-color: #223761 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-notice-icon {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-notice-text {
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-code {
    background: #1e293b !important;
    color: #93c5fd !important;
  }

  /* BUTTONS - DARK */
  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-cancel {
    border-color: #374151 !important;
    color: #94a3b8 !important;
    background: #161717 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-cancel:hover {
    border-color: #ef4444 !important;
    color: #f87171 !important;
    background: #271718 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-draft {
    border-color: #60a5fa !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-draft:hover {
    background: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-submit {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.40) !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .s1-btn-submit:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
  }

  body[data-theme-version="dark"] #gtms-step1-page .field-autofilled {
    background-color: #064e3b !important;
    border-color: #059669 !important;
    color: #ffffff !important;
  }
</style>

<script>
$(document).ready(function() {
  // Handle mineral checkbox change & Other toggle
  function handleMineralChange() {
    var hasOther = false;
    $('.mineral-checkbox:checked').each(function() {
      if ($(this).data('is-other') == 1 || $(this).data('name').toLowerCase().indexOf('other') !== -1) {
        hasOther = true;
      }
    });

    if (hasOther) {
      $('#other_mineral_box').slideDown(200);
      $('#field_other_mineral_name').focus();
    } else {
      $('#other_mineral_box').slideUp(200);
    }
  }

  $(document).on('change', '.mineral-checkbox', handleMineralChange);

  function performMimasLookup() {
    var mimasNo = $('#mimas_search_input').val().trim();
    if (!mimasNo) {
      $('#mimas_feedback_box').html(
        '<div class="alert alert-warning py-2 px-3 mb-0 small rounded-3 d-flex align-items-center gap-2">' +
          '<i class="bi bi-exclamation-triangle-fill text-warning"></i>' +
          '<span>Please enter or select a Customer Unique ID first.</span>' +
        '</div>'
      ).slideDown(200);
      return;
    }

    $('#btn_lookup_mimas').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Searching...');
    $('#mimas_feedback_box').hide();

    $.ajax({
      url: '/customers/lookup-mimas/' + encodeURIComponent(mimasNo),
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        $('#btn_lookup_mimas').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1.5"></i> Fetch Details');
        if (res.status === 1 && res.data) {
          var c = res.data;
          // Populate form fields
          $('#field_customer_id').val(c.id);
          $('#field_mimas_no').val(c.mimas_no);
          $('#field_client_name').val(c.customer_name);
          $('#field_secondary_contact_person').val(c.secondary_contact_person || '');
          $('#field_company_name').val(c.company_name);
          $('#field_mobile_num').val(c.mobile_num);
          $('#field_secondary_mobile_num').val(c.secondary_mobile_num || '');
          $('#field_email').val(c.email || '');
          $('#field_pan').val(c.pan);
          $('#field_aadhaar_no').val(c.aadhaar_no || '');
          $('#field_gstin').val(c.gstin || '');
          $('#field_area').val(c.area || '');
          $('#field_address').val(c.address || '');

          if (c.district_id) {
            $('#field_district_id').val(c.district_id);
          }
          if (c.mineral_id) {
            $('.mineral-checkbox').prop('checked', false);
            var cb = $('#mineral_' + c.mineral_id);
            if (cb.length) {
              cb.prop('checked', true);
            }
            handleMineralChange();
          }

          // Visual cue on all autofilled fields
          $('.auto-filled-field').addClass('field-autofilled');
          setTimeout(function() {
            $('.auto-filled-field').removeClass('field-autofilled');
          }, 3000);

          // Success feedback card
          $('#mimas_feedback_box').html(
            '<div class="alert alert-success  mt-2 py-2 px-3 mb-0 small rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-2 shadow-sm">' +
              '<div class="d-flex align-items-center gap-2">' +
                '<i class="bi bi-check-circle-fill text-success"></i>' +
                '<span><strong>Customer Profile Loaded:</strong> ' + (c.company_name || c.customer_name) +
                ' &bull; <span class="text-muted">' + (c.district_name || 'District N/A') + '</span>' +
                ' &bull; <span class="badge bg-success-subtle text-success border border-success ms-1">ID: ' + c.mimas_no + '</span></span>' +
              '</div>' +
              '<span class="badge bg-success text-white">All details populated</span>' +
            '</div>'
          ).slideDown(200);

          if (typeof toastr !== 'undefined') {
            toastr.success('Customer details populated from Customer Unique ID #' + c.mimas_no);
          }
        }
      },
      error: function(xhr) {
        $('#btn_lookup_mimas').prop('disabled', false).html('<i class="bi bi-arrow-repeat me-1.5"></i> Fetch Details');
        var msg = 'No registered customer found matching Customer Unique ID: "' + mimasNo + '".';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $('#mimas_feedback_box').html(
          '<div class="alert alert-danger py-2 px-3 mb-0 small rounded-3 d-flex align-items-center gap-2">' +
            '<i class="bi bi-x-circle-fill text-danger"></i>' +
            '<span>' + msg + ' Please verify the number or enter details manually.</span>' +
          '</div>'
        ).slideDown(200);

        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        }
      }
    });
  }

  // Trigger on button click
  $('#btn_lookup_mimas').on('click', performMimasLookup);

  // Trigger on Enter key in search box
  $('#mimas_search_input').on('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      performMimasLookup();
    }
  });

  // Trigger on selection change from datalist
  $('#mimas_search_input').on('change', function() {
    var val = $(this).val().trim();
    if (val.length >= 3) {
      performMimasLookup();
    }
  });

  // Auto-convert / mask Aadhaar input: 987654321001 -> 9876-5432-1001
  function maskAadhaar(value) {
    if (!value) return '';
    var digits = value.replace(/\D/g, '').substring(0, 12);
    var parts = [];
    for (var i = 0; i < digits.length; i += 4) {
      parts.push(digits.substring(i, i + 4));
    }
    return parts.join('-');
  }

  $(document).on('input paste keyup', '#field_aadhaar_no, .aadhaar-format', function() {
    var input = this;
    var current = $(input).val();
    var formatted = maskAadhaar(current);
    if (current !== formatted) {
      $(input).val(formatted);
    }
  });

  // Validate mineral selection helper
  function validateMinerals() {
    if ($('.mineral-checkbox:checked').length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Mineral Type Required',
          text: 'Please select at least one Mineral Type.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.error('Please select at least one Mineral Type.');
      } else {
        alert('Please select at least one Mineral Type.');
      }
      $('html, body').animate({ scrollTop: $('#minerals_selection_grid').offset().top - 100 }, 300);
      return false;
    }

    var isOtherSelected = false;
    $('.mineral-checkbox:checked').each(function() {
      if ($(this).data('is-other') == 1 || $(this).data('name').toLowerCase().indexOf('other') !== -1) {
        isOtherSelected = true;
      }
    });

    if (isOtherSelected && !$('#field_other_mineral_name').val().trim()) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Other Mineral Name Required',
          text: 'Please specify the Other Mineral Name.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.error('Please specify the Other Mineral Name.');
      } else {
        alert('Please specify the Other Mineral Name.');
      }
      $('#field_other_mineral_name').focus();
      return false;
    }

    return true;
  }

  // Save & Continue Later via AJAX (exit = 1)
  $('#btn_save_later_step1').on('click', function() {
    var form = document.getElementById('step1_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    if (!validateMinerals()) {
      return;
    }
    var btn = $(this);
    var mimasSearch = $('#mimas_search_input').val().trim();
    if (mimasSearch && !$('#field_mimas_no').val()) {
      $('#field_mimas_no').val(mimasSearch);
    }
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    
    var formData = $('#step1_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step1.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (typeof toastr !== 'undefined') {
          toastr.success('Draft saved successfully.');
        }
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Validation failed. Please fill basic details to save draft.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            html: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg.replace(/<br>/g, '\n'));
        }
      }
    });
  });

  // Save Step 1 via AJAX
  $('#btn_save_step1').on('click', function() {
    var form = document.getElementById('step1_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    if (!validateMinerals()) {
      return;
    }
    var btn = $(this);
    var mimasSearch = $('#mimas_search_input').val().trim();
    if (mimasSearch && !$('#field_mimas_no').val()) {
      $('#field_mimas_no').val(mimasSearch);
    }
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    
    $.ajax({
      url: '{{ route("step1.save") }}',
      type: 'POST',
      data: $('#step1_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step2';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<span>Save &amp; Continue</span> <i class="bi bi-arrow-right ms-1"></i>');
        var msg = 'Validation failed. Please check all required fields.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            html: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg.replace(/<br>/g, '\n'));
        }
      }
    });
  });
});
</script>
@endsection
