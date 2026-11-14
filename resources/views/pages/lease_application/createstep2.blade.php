@extends('layouts.app')
@section('title', 'Step 2 - Basic Info & MIMAS Credentials | GTMS')
@push('styles')
<link href="/css/gtms-lease.css?v=3" rel="stylesheet">
@endpush

@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step2-page" class="lz-page">

      <!-- ========================================================= -->
      <!-- STEPPER PROGRESS BAR (Native GTMS Stepper)               -->
      <!-- ========================================================= -->
      <div class="step-progress">
        <div class="sp-step done">
          <div class="circ"><i class="bi bi-check-lg"></i></div>
          <div class="sp-label">Application</div>
        </div>
        <div class="sp-step active">
          <div class="circ">2</div>
          <div class="sp-label">Basic &amp; MIMAS</div>
        </div>
        <div class="sp-step">
          <div class="circ">3</div>
          <div class="sp-label">Category</div>
        </div>
        <div class="sp-step">
          <div class="circ">4</div>
          <div class="sp-label">Folders</div>
        </div>
        <div class="sp-step">
          <div class="circ">5</div>
          <div class="sp-label">Documents</div>
        </div>
        <div class="sp-step">
          <div class="circ">6</div>
          <div class="sp-label">Handlers</div>
        </div>
        <div class="sp-step">
          <div class="circ">7</div>
          <div class="sp-label">Payment</div>
        </div>
        <div class="sp-step">
          <div class="circ">8</div>
          <div class="sp-label">Review</div>
        </div>
      </div>

      <!-- ========================================================= -->
      <!-- MAIN STEP 2 CARD CONTAINER                                -->
      <!-- ========================================================= -->
      <div class="s2-main-card card border-0 lz-card">
        
        <!-- HEADER BLOCK -->
        <div class="s2-header-block d-flex flex-wrap align-items-center justify-content-between gap-2 lz-head">
          <div>
            <div class="s2-eyebrow d-flex align-items-center gap-1.5 lz-eyebrow">
              <i class="bi bi-shield-check"></i>
              <span>STEP 2 OF 8 • REPRESENTATION &amp; MIMAS CREDENTIALS</span>
            </div>
            <h4 class="s2-title lz-title">Basic Information &amp; MIMAS Credentials</h4>
            <div class="s2-subtitle lz-subtitle">
              Enter official representative contacts and statutory Mineral Management System (MIMAS) portal credentials.
            </div>
          </div>
          <div class="s2-time-tag lz-time">
            <i class="bi bi-clock-history me-1"></i>Est. ~1.5 mins
          </div>
        </div>

        <!-- ========================================================= -->
        <!-- MAIN STEP 2 FORM                                          -->
        <!-- ========================================================= -->
        <form id="step2_form" method="POST" action="{{ route('step2.save') }}">
          @csrf

          @php
            $savedPlainPass = $draft['step6']['mimas_password'] ?? ($draft['step2']['mimas_password'] ?? null);
            if ((empty($savedPlainPass) || $savedPlainPass === '__UNCHANGED__') && !empty($draft['application_id'])) {
                $savedPlainPass = \App\Models\MimasCredential::where('lease_application_id', $draft['application_id'])->value('password');
            }
            // Only the flag reaches the page; the password is fetched on demand.
            $hasSavedPass = !empty($savedPlainPass);
          @endphp

          <!-- SECTION 1: PRIMARY AUTHORIZED REPRESENTATIVE -->
          <div class="s2-section-card mb-3 lz-section">
            <div class="s2-section-header d-flex align-items-center justify-content-between flex-wrap gap-2 lz-section-head">
              <div class="d-flex align-items-center">
                <i class="bi bi-person-check text-primary me-2"></i>
                <span class="s2-section-title lz-section-title">Primary Authorized Representative <span class="text-danger ms-0.5">*</span></span>
              </div>
              <span class="badge s2-status-badge">
                <i class="bi bi-award me-1"></i>Official Signatory
              </span>
            </div>

            <div class="row gx-3 gy-3 s2-form-row lz-row">
              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_contact_person">
                  Contact Person Name <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon lz-addon"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control s2-input lz-input" id="field_contact_person" name="contact_person" 
                         placeholder="Full legal name" 
                         value="{{ $draft['step2']['contact_person'] ?? $draft['step1']['client_name'] ?? '' }}" required>
                </div>
                <div class="form-text-hint s2-hint lz-hint">Authorized individual entitled to sign and receive notices.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_contact_mobile">
                  Primary Mobile Number <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon s2-addon-prefix lz-addon"><i class="bi bi-telephone me-1"></i>+91</span>
                  <input type="tel" class="form-control s2-input lz-input" id="field_contact_mobile" name="contact_mobile" 
                         placeholder="10-digit mobile number" 
                         value="{{ $draft['step2']['contact_mobile'] ?? $draft['step1']['mobile_num'] ?? '' }}" 
                         required maxlength="15" pattern="[0-9]{10,15}">
                </div>
                <div class="form-text-hint s2-hint lz-hint">Primary SMS alerts, status notifications, and OTPs.</div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: SECONDARY CONTACT (SITE OPERATIONS) -->
          <div class="s2-section-card mb-3 lz-section">
            <div class="s2-section-header d-flex align-items-center justify-content-between flex-wrap gap-2 lz-section-head">
              <div class="d-flex align-items-center">
                <i class="bi bi-person-gear text-indigo me-2"></i>
                <span class="s2-section-title lz-section-title">Secondary Contact &amp; Site Operations</span>
              </div>
              <span class="badge s2-opt-badge lz-opt">Optional</span>
            </div>

            <div class="row gx-3 gy-3 s2-form-row lz-row">
              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_secondary_contact_person">
                  Secondary Contact Person&nbsp;<span class="s2-opt-tag lz-opt">(Optional)</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon lz-addon"><i class="bi bi-person-badge"></i></span>
                  <input type="text" class="form-control s2-input lz-input" id="field_secondary_contact_person" name="secondary_contact_person" 
                         placeholder="e.g. Quarry Manager / Site Supervisor" 
                         value="{{ $draft['step2']['secondary_contact_person'] ?? $draft['step1']['secondary_contact_person'] ?? '' }}">
                </div>
                <div class="form-text-hint s2-hint lz-hint">Quarry manager, technical agent, or resident supervisor.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_secondary_contact_mobile">
                  Secondary Mobile Number&nbsp;<span class="s2-opt-tag lz-opt">(Optional)</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon s2-addon-prefix lz-addon"><i class="bi bi-phone me-1"></i>+91</span>
                  <input type="tel" class="form-control s2-input lz-input" id="field_secondary_contact_mobile" name="secondary_contact_mobile" 
                         placeholder="10-digit alternate mobile number" 
                         value="{{ $draft['step2']['secondary_contact_mobile'] ?? $draft['step1']['secondary_mobile_num'] ?? '' }}" 
                         maxlength="15" pattern="[0-9]{10,15}">
                </div>
                <div class="form-text-hint s2-hint lz-hint">Site operational, survey call, and emergency contact.</div>
              </div>
            </div>
          </div>

          <!-- SECTION 3: OFFICIAL MIMAS PORTAL CREDENTIALS -->
          <div class="s2-section-card mb-3 lz-section">
            <div class="s2-section-header d-flex align-items-center justify-content-between flex-wrap gap-2 lz-section-head">
              <div class="d-flex align-items-center">
                <i class="bi bi-shield-lock-fill text-warning me-2"></i>
                <span class="s2-section-title lz-section-title">Official MIMAS Portal Credentials <span class="text-danger ms-0.5">*</span></span>
              </div>
              <span class="badge s2-gov-badge">
                <i class="bi bi-bank me-1"></i>TN Dept. of Geology &amp; Mining
              </span>
            </div>

            <!-- Informational Explanation Banner -->
            <div class="s2-info-banner d-flex align-items-start gap-2.5 mb-3 p-2.5 rounded-3">
              <i class="bi bi-info-circle-fill s2-banner-icon m-4"></i>
              <div class="s2-banner-text mt-3 mb-2 ">
                Store the applicant's official <b>Mineral Management System (MIMAS)</b> login to enable automatic challan verification, statutory clearance tracking, and direct Departmental sync.
              </div>
            </div>

            <div class="row gx-3 gy-3 s2-form-row lz-row">
              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_mimas_user_id">
                  MIMAS User ID <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon lz-addon"><i class="bi bi-key"></i></span>
                  <input type="text" name="mimas_user_id" id="field_mimas_user_id" class="form-control s2-input lz-input" 
                         placeholder="e.g. TN-MMS-SLM-001 or Username" 
                         value="{{ $draft['step6']['mimas_user_id'] ?? $draft['step2']['mimas_user_id'] ?? $draft['step1']['mimas_no'] ?? '' }}" required>
                </div>
                <div class="form-text-hint s2-hint lz-hint">Applicant login ID on the state mineral online system.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_mimas_password">
                  MIMAS Password <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group s2-password-group lz-group">
                  <span class="input-group-text s2-addon lz-addon"><i class="bi bi-lock"></i></span>
                  <input type="password" name="mimas_password" id="field_mimas_password" class="form-control s2-input s2-pass-field lz-input" 
                         data-has-saved="{{ $hasSavedPass ? '1' : '0' }}"
                         data-pass-url="{{ !empty($draft['application_id']) ? route('application.mimasPassword', $draft['application_id']) : '' }}"
                         placeholder="{{ $hasSavedPass ? '•••••••••••• (Stored securely — click to update)' : 'Enter MIMAS portal password' }}" 
                         value="{{ $hasSavedPass ? '__UNCHANGED__' : '' }}" required autocomplete="new-password">
                  <button class="btn s2-btn-addon s2-btn-toggle" type="button" id="btn_toggle_password" title="Show / Hide Password">
                    <!-- Eye Open SVG -->
                    <svg id="svg_eye_open" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                    </svg>
                    <!-- Eye Slashed SVG -->
                    <svg id="svg_eye_closed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="display:none;">
                      <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                      <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                      <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                    </svg>
                  </button>
                  <button class="btn s2-btn-addon s2-btn-copy" type="button" id="btn_copy_password" title="Copy Password to Clipboard">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </div>
                <div class="form-text-hint s2-hint lz-hint">
                  <i class="bi bi-shield-check text-success me-1"></i>Stored encrypted via AES-256. Click eye to reveal or copy.
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_mimas_email">
                  MIMAS Registered Email ID <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon lz-addon"><i class="bi bi-envelope"></i></span>
                  <input type="email" name="mimas_email" id="field_mimas_email" class="form-control s2-input lz-input" 
                         placeholder="official@example.com" 
                         value="{{ $draft['step6']['mimas_email'] ?? $draft['step2']['mimas_email'] ?? $draft['step1']['email'] ?? '' }}" required>
                </div>
                <div class="form-text-hint s2-hint lz-hint">Official email linked with MIMAS departmental login.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s2-label lz-label" for="field_mimas_contact">
                  MIMAS Registered Mobile Number <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s2-input-group lz-group">
                  <span class="input-group-text s2-addon s2-addon-prefix lz-addon"><i class="bi bi-phone me-1"></i>+91</span>
                  <input type="tel" name="mimas_contact" id="field_mimas_contact" class="form-control s2-input lz-input" 
                         placeholder="10-digit mobile number" 
                         value="{{ $draft['step6']['mimas_contact'] ?? $draft['step2']['mimas_contact'] ?? $draft['step1']['mobile_num'] ?? '' }}" 
                         required maxlength="10">
                </div>
                <div class="form-text-hint s2-hint lz-hint">Mobile registered for MIMAS departmental OTP &amp; approval SMS.</div>
              </div>
            </div>
          </div>

          <!-- SYSTEM NOTICE / GOVERNANCE PANEL -->
          <div class="s2-notice-card mt-3 mb-0 p-3 lz-notice">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-shield-lock-fill s2-notice-icon lz-notice-icon"></i>
              <div class="s2-notice-text lz-notice-text">
                <b>Statutory Security Guarantee:</b> All credentials saved on this step are encrypted at rest with hardware-backed AES-256-GCM. Passwords are never logged in plain text.
              </div>
            </div>
          </div>

        </form>

        <!-- ========================================================= -->
        <!-- WIZARD ACTIONS FOOTER                                     -->
        <!-- ========================================================= -->
        <div class="wizard-actions s2-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3 lz-actions">
          <a href="{{ route('step1') }}" class="btn s2-btn-back lz-btn lz-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> Back to Step 1
          </a>
          @can('application.create')
          <div class="d-flex align-items-center gap-2">
            <button type="button" id="btn_save_later_step2" class="btn s2-btn-draft px-3 lz-btn lz-btn-outline">
              <i class="bi bi-save me-1"></i> Save &amp; Continue Later
            </button>
            <button type="button" id="btn_save_step2" class="btn s2-btn-submit px-4 lz-btn lz-btn-primary">
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
<!-- SCOPED CSS FOR STEP 2: ZERO IMPACT ON OTHER PAGES, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step2-page)
     ========================================================================== */
  #gtms-step2-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */

/* MAIN CARD */

/* SECTION CARDS - LIGHT */

#gtms-step2-page .s2-status-badge {
    background-color: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-size: 0.69rem;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: 600;
  }

#gtms-step2-page .s2-gov-badge {
    background-color: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 0.69rem;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: 600;
  }

  /* INFO BANNER */
  #gtms-step2-page .s2-info-banner {
    background: #f0f7ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
  }

  #gtms-step2-page .s2-banner-icon {
    font-size: 1rem;
    color: #2563eb;
    flex-shrink: 0;
  }

  #gtms-step2-page .s2-banner-text {
    font-size: 0.79rem;
    line-height: 1.45;
    color: #334155;
  }

  #gtms-step2-page .s2-banner-text b {
    color: #1e40af;
  }

  /* FORM GRID & LABELS */

/* INPUT GROUPS - LIGHT */

#gtms-step2-page .s2-input-group .s2-addon-prefix {
    width: auto;
    padding: 0 10px;
    font-size: 0.80rem;
    font-weight: 600;
  }

/* PASSWORD GROUP WITH INTEGRATED BUTTONS */
  #gtms-step2-page .s2-password-group .s2-pass-field {
    border-right: none !important;
    border-radius: 0 !important;
  }

  /* Eye / copy buttons: same 44px height and 1.5px borders as the input so
     the group's top and bottom edges line up */
  #gtms-step2-page .s2-btn-addon {
    background-color: #f8fafc;
    border: 1.5px solid #cbd5e1 !important;
    border-left: 1px solid #e2e8f0 !important;
    border-right: none !important;
    border-radius: 0 !important;
    color: #64748b;
    height: auto;
    min-height: 44px;
    width: 44px;
    padding: 0;
    margin: 0 !important;
    display: flex;
    align-items: center;
    justify-content: center;
    align-self: stretch;
    box-shadow: none !important;
    transform: none !important;
    transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    cursor: pointer;
  }

  #gtms-step2-page .s2-btn-addon svg {
    transition: transform 0.2s ease;
  }

  #gtms-step2-page .s2-btn-addon:hover {
    background-color: #eff6ff;
    color: #1e40af;
  }

  #gtms-step2-page .s2-btn-addon:hover svg {
    transform: scale(1.12);
  }

  #gtms-step2-page .s2-btn-addon:active svg {
    transform: scale(0.92);
  }

  #gtms-step2-page .s2-btn-copy {
    border-right: 1.5px solid #cbd5e1 !important;
    border-radius: 0 10px 10px 0 !important;
  }

  #gtms-step2-page .s2-password-group:focus-within .s2-btn-addon {
    border-color: #0F1E4D !important;
    border-left-color: #e2e8f0 !important;
  }

  /* SYSTEM NOTICE CARD */

/* WIZARD ACTIONS FOOTER */

/* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"] #gtms-step2-page)
     ========================================================================== */

/* SECTION CARDS - DARK */

body[data-theme-version="dark"] #gtms-step2-page .s2-status-badge {
    background-color: rgba(5, 150, 105, 0.15) !important;
    color: #34d399 !important;
    border-color: rgba(5, 150, 105, 0.30) !important;
  }

body[data-theme-version="dark"] #gtms-step2-page .s2-gov-badge {
    background-color: rgba(245, 158, 11, 0.15) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.30) !important;
  }

  /* INFO BANNER - DARK */
  body[data-theme-version="dark"] #gtms-step2-page .s2-info-banner {
    background: #13233a !important;
    border-color: #1e3a5f !important;
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-step2-page .s2-banner-icon {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step2-page .s2-banner-text {
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step2-page .s2-banner-text b {
    color: #93c5fd !important;
  }

  /* LABELS & HINTS - DARK */

/* INPUTS & ADDONS - DARK */

body[data-theme-version="dark"] #gtms-step2-page .s2-btn-addon {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step2-page .s2-btn-addon:hover {
    background-color: #1e293b !important;
    color: #60a5fa !important;
  }

body[data-theme-version="dark"] #gtms-step2-page .s2-password-group:focus-within .s2-btn-addon {
    border-color: #60a5fa !important;
  }

/* NOTICE CARD - DARK */

body[data-theme-version="dark"] #gtms-step2-page .s2-notice-text b {
    color: #e2e8f0 !important;
  }

  /* WIZARD ACTIONS - DARK */

</style>

<script>
$(document).ready(function() {
  // Clear masked placeholder on focus so user can type a new password cleanly
  $('#field_mimas_password').on('focus', function() {
    if ($(this).val() === '__UNCHANGED__') {
      $(this).val('');
    }
  }).on('blur', function() {
    // If applicant blurred without entering anything and has a saved password, restore placeholder
    if ($(this).data('has-saved') == '1' && $(this).val().trim() === '') {
      $(this).val('__UNCHANGED__');
    }
  });

  // Saved password is fetched on demand (not printed in the page source)
  var savedPassCache = null;
  function getSavedPassword() {
    var url = $('#field_mimas_password').attr('data-pass-url');
    if (savedPassCache !== null) return Promise.resolve(savedPassCache);
    if (!url || $('#field_mimas_password').data('has-saved') != '1') return Promise.resolve('');
    return fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }).then(function(res) { return res.json(); }).then(function(data) {
      savedPassCache = data.status === 1 ? data.password : '';
      return savedPassCache;
    }).catch(function() { return ''; });
  }

  // Toggle password visibility (Eye Icon)
  $('#btn_toggle_password').on('click', function(e) {
    e.preventDefault();
    var passInput = $('#field_mimas_password');
    var realPass = savedPassCache || '';
    if (passInput.prop('type') === 'password') {
      var show = function(saved) {
        passInput.prop('type', 'text');
        if (passInput.val() === '__UNCHANGED__' && saved) {
          passInput.val(saved);
        }
        $('#svg_eye_open').hide();
        $('#svg_eye_closed').show();
      };
      if (passInput.val() === '__UNCHANGED__') {
        getSavedPassword().then(show);
      } else {
        show('');
      }
    } else {
      passInput.prop('type', 'password');
      if (passInput.val() === realPass && passInput.data('has-saved') == '1') {
        passInput.val('__UNCHANGED__');
      }
      $('#svg_eye_closed').hide();
      $('#svg_eye_open').show();
    }
  });

  // Copy password to clipboard
  $('#btn_copy_password').on('click', function(e) {
    e.preventDefault();
    var passInput = $('#field_mimas_password');
    if (passInput.val() === '__UNCHANGED__') {
      getSavedPassword().then(copyPassword);
    } else {
      copyPassword(passInput.val());
    }
  });

  function copyPassword(realPass) {
    if (!realPass) {
      if (typeof toastr !== 'undefined') toastr.info('No password entered to copy.');
      else alert('No password entered to copy.');
      return;
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(realPass).then(function() {
        if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
        else alert('MIMAS Password copied to clipboard!');
      });
    } else {
      var temp = $('<input>');
      $('body').append(temp);
      temp.val(realPass).select();
      document.execCommand('copy');
      temp.remove();
      if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
      else alert('MIMAS Password copied to clipboard!');
    }
  }

  // Numeric-only input restriction on mobiles
  $('#field_contact_mobile, #field_secondary_contact_mobile, #field_mimas_contact').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
  });

  // Auto-sync MIMAS contact with primary mobile if empty
  $('#field_contact_mobile').on('blur', function() {
    var val = $(this).val().trim();
    if (val && !$('#field_mimas_contact').val().trim()) {
      $('#field_mimas_contact').val(val);
    }
  });

  // Save & Continue Later
  $('#btn_save_later_step2').on('click', function() {
    var form = document.getElementById('step2_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    var formData = $('#step2_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step2.save") }}',
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
        var msg = 'Validation failed.';
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

  // Save & Continue
  $('#btn_save_step2').on('click', function() {
    var form = document.getElementById('step2_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    var btn = $(this);
    var person = $('#field_contact_person').val().trim();
    var mobile = $('#field_contact_mobile').val().trim();
    var mimasUserId = $('#field_mimas_user_id').val().trim();
    var mimasEmail = $('#field_mimas_email').val().trim();
    
    if (!person) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Contact Name Required',
          text: 'Authorized Contact Person Name is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('Contact Person Name is required.');
      else alert('Contact Person Name is required.');
      return;
    }
    if (!mobile || mobile.length < 10) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Mobile Number Required',
          text: 'Valid 10-digit primary mobile number is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('Valid 10-digit primary mobile number is required.');
      else alert('Valid 10-digit primary mobile number is required.');
      return;
    }
    if (!mimasUserId) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'MIMAS User ID Required',
          text: 'MIMAS Portal User ID is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('MIMAS User ID is required.');
      else alert('MIMAS User ID is required.');
      $('#field_mimas_user_id').focus();
      return;
    }
    if (!mimasEmail) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'MIMAS Email Required',
          text: 'MIMAS Registered Email ID is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('MIMAS Email ID is required.');
      else alert('MIMAS Email ID is required.');
      $('#field_mimas_email').focus();
      return;
    }
    
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    
    $.ajax({
      url: '{{ route("step2.save") }}',
      type: 'POST',
      data: $('#step2_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step3';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<span>Save &amp; Continue</span> <i class="bi bi-arrow-right ms-1"></i>');
        var msg = 'Validation failed.';
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
