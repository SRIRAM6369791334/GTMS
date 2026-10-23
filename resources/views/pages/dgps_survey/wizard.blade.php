@extends('layouts.app')
@section('title', 'DGPS Survey Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$steps = [
  ['Survey Request & Planning', ['Initial request registered', 'Satellite planning done', 'Client notified of schedule']],
  ['Field Survey & Data Collection', ['Base station established', 'Boundary pillars surveyed', 'Control points checked']],
  ['Process Field Points & Upload Data', ['Raw RINEX & coordinate logs', 'Cadastral map superimposition', 'Boundary polygon KML/KMZ']],
  ['Handling Team Allocation', ['Chief Land Surveyor assigned', 'Instrument Operators designated', 'GIS Mapping Engineers appointed']],
  ['Payment Details & Billing Ledger', ['Service fee recorded', 'Advance received verified', 'Settlement ledger balanced']],
  ['Preview & Final Confirmation', ['Complete survey dossier checked', 'Coordinates audited', 'Ready to finalize']]
];
$labels = ['Request','Field Survey','Process & Upload','Handling Team','Payment','Preview'];
@endphp

<div id="gtms-dgps-wizard-page" class="content-body default-height">
  <div class="container-fluid py-4">
    <div class="dgps-wizard-shell">
      {{-- Stepper Progress Bar --}}
      <div class="dgps-stepper-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
          <div>
            <span class="dgps-stepper-eyebrow">DGPS DEPARTMENT &bull; STATUTORY LAND DEMARCATION</span>
            <h6 class="dgps-stepper-title mb-0">Workflow Navigation Progress</h6>
          </div>
          <span class="dgps-step-count-pill">Step {{ $step }} of 6</span>
        </div>
        <div class="step-progress">
          @foreach($labels as $number => $label)
            <a href="{{ ($number + 1) <= $step ? route('dgps-survey.step', $number + 1) : 'javascript:void(0);' }}" 
               class="sp-step {{ $number + 1 < $step ? 'done' : ($number + 1 === $step ? 'active' : '') }} {{ ($number + 1) <= $step ? 'is-clickable' : '' }}"
               title="Step {{ $number + 1 }}: {{ $steps[$number][0] }}">
              <div class="circ">
                @if($number + 1 < $step)
                  <i class="bi bi-check-lg"></i>
                @else
                  {{ $number + 1 }}
                @endif
              </div>
              <div class="sp-label">{{ $label }}</div>
            </a>
          @endforeach
        </div>
      </div>

      {{-- Main Wizard Content Container --}}
      <div class="wizard-card shadow-sm border-0">
        <form method="POST" action="{{ $step === 6 ? route('dgps-survey.store') : route('dgps-survey.saveStep', $step) }}" id="dgpsWizardForm">
          @csrf

          {{-- STEP 1: SURVEY REQUEST & PLANNING --}}
          @if($step === 1)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-geo-alt-fill me-1"></i> STEP 1 OF 6 &bull; SURVEY REQUEST &amp; PLANNING
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~1-2 mins
                  </span>
                  <span class="dgps-status-pill">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Active Request
                  </span>
                </div>
              </div>
              <h3 class="s1-title">Survey Request Registration &amp; Concession Details</h3>
              <p class="s1-subtitle mb-0">Initiate statutory DGPS survey request, link customer credentials, and configure concession territorial boundaries.</p>
            </div>

            {{-- Customer Unique ID Lookup Card --}}
            <div class="s1-lookup-card mb-4">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="s1-lookup-icon-badge">
                    <i class="fa fa-fingerprint"></i>
                  </div>
                  <div>
                    <h5 class="s1-lookup-label mb-0">Customer Unique ID Lookup</h5>
                    <small class="s1-lookup-desc">Search registered profile by Customer ID, MIMAS number, or applicant name.</small>
                  </div>
                </div>
                <span class="s1-badge-autofill">
                  <i class="fa fa-bolt me-1"></i> Instant Autofill
                </span>
              </div>
              
              <div class="s1-lookup-input-group">
                <span class="s1-lookup-addon"><i class="fa fa-search"></i></span>
                <input type="text" id="mimas_search_input" class="form-control s1-lookup-field" 
                       placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
                <button class="btn s1-btn-fetch" type="button" id="btn_lookup_mimas">
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

              <div id="mimas_feedback_box" class="mt-3" style="display:none;"></div>
            </div>

            {{-- Section 1: Applicant Profile & Enterprise Identity --}}
            <div class="s1-section-card mb-4">
              <div class="s1-section-header">
                <div class="s1-section-icon-badge">
                  <i class="bi bi-building"></i>
                </div>
                <div>
                  <h5 class="s1-section-title mb-0">Applicant Profile &amp; Contact Particulars</h5>
                  <small class="s1-section-desc">Client enterprise profile, verified entity details, and communication channels.</small>
                </div>
              </div>
              <div class="s1-section-body">
                <div class="row g-3">
                  <div class="col-md-12">
                    <label class="s1-label">Client / Applicant Entity <span class="text-danger">*</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-briefcase"></i></span>
                      <select name="customer_id" id="select_dgps_customer" class="form-select s1-input auto-filled-field" required>
                        <option value="">-- Choose Client / Entity --</option>
                        @foreach($customers as $c)
                          <option value="{{ $c->id }}"
                            data-company="{{ $c->company_name }}"
                            data-mobile="{{ $c->mobile_num }}"
                            data-secondary-mobile="{{ $c->secondary_mobile_num }}"
                            data-mimas="{{ $c->mimas_no }}"
                            {{ ($draft['customer_id'] ?? '') == $c->id ? 'selected' : ($loop->first && empty($draft['customer_id']) ? 'selected' : '') }}>
                            {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Primary Phone Number <span class="text-danger">*</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-telephone-fill"></i></span>
                      <input type="text" class="form-control s1-input auto-filled-field" name="primary_phone" id="field_dgps_primary_phone"
                        value="{{ $draft['primary_phone'] ?? ($customers->first()?->mobile_num ?? '') }}" placeholder="10-digit primary contact number" required maxlength="15">
                    </div>
                    <small class="s1-hint">Used for official SMS and field survey scheduling dispatches.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Secondary Phone Number <span class="s1-opt-tag">Optional</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-phone"></i></span>
                      <input type="text" class="form-control s1-input auto-filled-field" name="secondary_phone" id="field_dgps_secondary_phone"
                        value="{{ $draft['secondary_phone'] ?? ($customers->first()?->secondary_mobile_num ?? '') }}" placeholder="Alternate phone / site supervisor" maxlength="15">
                    </div>
                    <small class="s1-hint">Secondary emergency contact or site in-charge person.</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- Section 2: Concession Area & Survey Boundaries --}}
            <div class="s1-section-card mb-4">
              <div class="s1-section-header">
                <div class="s1-section-icon-badge">
                  <i class="bi bi-pin-map-fill"></i>
                </div>
                <div>
                  <h5 class="s1-section-title mb-0">Concession Territory &amp; Survey Parameters</h5>
                  <small class="s1-section-desc">Statutory tracking code, associated mining lease, and area extent.</small>
                </div>
              </div>
              <div class="s1-section-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="s1-label">Survey Request No. <span class="s1-opt-tag">Auto-Generated</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-upc-scan"></i></span>
                      <input class="form-control s1-input font-monospace fw-bold" name="survey_no" readonly
                        value="{{ $draft['survey_no'] ?? ('DGPS-' . date('Y') . '-' . sprintf('%04d', \App\Models\DgpsSurvey::count() + 1)) }}">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Associated Lease Application <span class="s1-opt-tag">Optional</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-link-45deg"></i></span>
                      <select name="lease_application_id" id="select_dgps_lease" class="form-select s1-input auto-filled-field">
                        <option value="">-- Standalone Survey / None --</option>
                        @foreach($leaseApps as $lease)
                          <option value="{{ $lease->id }}" data-customer="{{ $lease->customer_id }}" {{ ($draft['lease_application_id'] ?? '') == $lease->id ? 'selected' : '' }}>
                            Lease #{{ $lease->application_no }} ({{ $lease->area_extent_acres }} acres)
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Lease Area Extent (Ha) <span class="text-danger">*</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-bounding-box-circles"></i></span>
                      <input type="number" step="0.01" class="form-control s1-input auto-filled-field" name="lease_area_ha" id="field_dgps_area" placeholder="Area in hectares (e.g. 3.85)"
                        value="{{ $draft['lease_area_ha'] ?? '3.85' }}" required>
                    </div>
                    <small class="s1-hint">Total proposed quarry or mineral concession extent in hectares.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Location / Village / Taluk <span class="text-danger">*</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-geo-alt"></i></span>
                      <input class="form-control s1-input auto-filled-field" name="location" id="field_dgps_location" placeholder="e.g. Semmandapatti Village, Omalur Taluk, Salem District"
                        value="{{ $draft['location'] ?? 'Salem / Semmandapatti' }}" required>
                    </div>
                    <small class="s1-hint">Revenue village, administrative taluk, and district jurisdiction.</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- Bottom Notice Card --}}
            <div class="s1-notice-card">
              <div class="d-flex align-items-center gap-3">
                <div class="s1-notice-icon-box">
                  <i class="bi bi-info-circle-fill s1-notice-icon"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <strong class="text-navy" style="font-size:0.88rem;">STATUTORY SURVEY STANDARD</strong>
                    <span class="badge bg-primary text-white" style="font-size:10px;">TNMMCR 1959</span>
                  </div>
                  <p class="s1-notice-text mb-0">All cadastral boundaries and pillar coordinates registered in this survey will be cross-referenced with Survey of India permanent control benchmarks and synced to the Departmental Mining Portal.</p>
                </div>
              </div>
            </div>

          {{-- STEP 2: FIELD SURVEY & DATA COLLECTION --}}
          @elseif($step === 2)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-broadcast-pin me-1"></i> STEP 2 OF 6 &bull; FIELD SURVEY &amp; DATA COLLECTION
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~2-3 mins
                  </span>
                  <span class="dgps-status-pill">
                    <i class="bi bi-compass-fill me-1 text-primary"></i> RTK Execution
                  </span>
                </div>
              </div>
              <h3 class="s1-title">Field Survey Execution &amp; Benchmark Calibration</h3>
              <p class="s1-subtitle mb-0">Record on-site execution schedule, establish GNSS RTK base station benchmarks, and calibrate rover receivers.</p>
            </div>

            {{-- Technical Execution Readiness Banner --}}
            <div class="dgps-banner-card mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="dgps-banner-icon-badge">
                    <i class="bi bi-broadcast"></i>
                  </div>
                  <div>
                    <h5 class="dgps-banner-title">RTK GNSS Base Station Calibration &amp; Field Reconnaissance</h5>
                    <p class="dgps-banner-desc mb-0">Dual-frequency Trimble R12i GNSS receiver benchmark fixation, rover initialization, and SOP satellite geometry verification.</p>
                  </div>
                </div>
                <span class="dgps-banner-status-pill">
                  <i class="bi bi-shield-check me-1"></i> RTK Fixed (PDOP &lt; 2.0)
                </span>
              </div>
            </div>

            {{-- 3-Bento Technical Metric Cards --}}
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="dgps-kpi-card dgps-kpi-instrument">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dgps-kpi-label">GNSS RTK System</span>
                    <div class="dgps-kpi-icon-badge dgps-kpi-icon-blue">
                      <i class="bi bi-cpu"></i>
                    </div>
                  </div>
                  <div class="dgps-kpi-val">Trimble R12i</div>
                  <div class="dgps-kpi-sub text-muted">Dual-Freq Base &amp; Rover</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="dgps-kpi-card dgps-kpi-benchmark">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dgps-kpi-label">SOI Benchmark Ref</span>
                    <div class="dgps-kpi-icon-badge dgps-kpi-icon-emerald">
                      <i class="bi bi-geo-fill"></i>
                    </div>
                  </div>
                  <div class="dgps-kpi-val">SOI BM-88 (Salem)</div>
                  <div class="dgps-kpi-sub text-muted">State Triangulation Grid</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="dgps-kpi-card dgps-kpi-tolerance">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="dgps-kpi-label">Target Tolerance</span>
                    <div class="dgps-kpi-icon-badge dgps-kpi-icon-amber">
                      <i class="bi bi-bullseye"></i>
                    </div>
                  </div>
                  <div class="dgps-kpi-val">&plusmn; 5mm (H) / &plusmn; 10mm (V)</div>
                  <div class="dgps-kpi-sub text-muted">Sub-centimeter Grade</div>
                </div>
              </div>
            </div>

            {{-- Section Card: Field Operations & Traverse Log --}}
            <div class="s1-section-card mb-4">
              <div class="s1-section-header">
                <div class="s1-section-icon-badge">
                  <i class="bi bi-journal-text"></i>
                </div>
                <div>
                  <h5 class="s1-section-title mb-0">Field Traverse Log &amp; Satellite Geometry</h5>
                  <small class="s1-section-desc">Date of field observation, field book register reference, and benchmark calibration log.</small>
                </div>
              </div>
              <div class="s1-section-body">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="s1-label">Survey Execution Date <span class="text-danger">*</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-calendar-event"></i></span>
                      <input class="form-control s1-input" type="date" name="survey_date" value="{{ $draft['survey_date'] ?? date('Y-m-d') }}" required>
                    </div>
                    <small class="s1-hint">Official date when on-site GNSS rover observations were conducted.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="s1-label">Field Book Reference No. <span class="s1-opt-tag">SOP Register</span></label>
                    <div class="s1-input-group">
                      <span class="s1-addon"><i class="bi bi-book"></i></span>
                      <input class="form-control s1-input font-monospace" name="field_book_no" readonly value="FB-TN-SLM-042">
                    </div>
                    <small class="s1-hint">Licensed surveyor hardcopy field journal reference volume.</small>
                  </div>
                  <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <label class="s1-label mb-0">Base Station Calibration &amp; Field Traverse Notes</label>
                      <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm dgps-chip-preset" onclick="appendDgpsNote('SOI BM Fixed at Pillar #1')">+ BM Pillar #1</button>
                        <button type="button" class="btn btn-sm dgps-chip-preset" onclick="appendDgpsNote('RTK Rover Tolerance < 5mm')">+ &lt; 5mm Tol</button>
                        <button type="button" class="btn btn-sm dgps-chip-preset" onclick="appendDgpsNote('Clear Sky / Zero Multipath')">+ Clear Sky</button>
                      </div>
                    </div>
                    <textarea class="form-control s1-textarea" name="survey_team_notes" id="survey_team_notes" rows="3" placeholder="Enter benchmark calibration details, rover fix status, satellite geometry observations...">{{ $draft['survey_team_notes'] ?? 'Trimble R12i GNSS base established at SOI benchmark BM-88. Rover calibration verified within 5mm tolerance across all boundary pillars.' }}</textarea>
                    <small class="s1-hint">Detailed technical notes regarding atmospheric conditions, multipath clearance, and control point checks.</small>
                  </div>
                </div>
              </div>
            </div>

            {{-- Quality SOP Checklist Box --}}
            <div class="s1-notice-card">
              <div class="d-flex align-items-center gap-3">
                <div class="s1-notice-icon-box">
                  <i class="bi bi-shield-check s1-notice-icon"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <strong class="text-navy" style="font-size:0.88rem;">QUALITY ASSURANCE PROTOCOL</strong>
                    <span class="badge bg-success text-white" style="font-size:10px;">VERIFIED</span>
                  </div>
                  <p class="s1-notice-text mb-0">&bull; SOI Permanent Benchmark verified on site &bull; Base station tracking 15+ NavSTAR/GLONASS satellites &bull; Rover calibration verified against 2 independent control points.</p>
                </div>
              </div>
            </div>

          {{-- STEP 3: DATA PROCESSING & DOCUMENTS UPLOAD --}}
          @elseif($step === 3)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-cloud-arrow-up-fill me-1"></i> STEP 3 OF 6 &bull; PROCESS FIELD POINTS &amp; UPLOAD DATA
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~3-5 mins
                  </span>
                  <span class="dgps-status-pill">
                    <i class="bi bi-folder-check me-1 text-success"></i> Digital Repository
                  </span>
                </div>
              </div>
              <h3 class="s1-title">Process Field Points &amp; Statutory Deliverables</h3>
              <p class="s1-subtitle mb-0">Upload raw GPS RINEX observations, coordinate tables, cadastral FMB map sketches, and boundary polygon KML files.</p>
            </div>

            {{-- Overall Progress Summary Banner --}}
            <div class="s5-summary-banner mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="s5-folder-icon-box">
                    <i class="bi bi-folder2-open"></i>
                  </div>
                  <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <span class="s5-summary-badge">STATUTORY REPOSITORY</span>
                      <span class="s5-summary-label">Mandatory Survey Deliverables</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                      <span class="s5-counter-text" id="dgps_doc_counter">0 / 6 uploaded</span>
                      <span class="s5-counter-sub text-muted">&bull; WGS84 UTM Zone 44N</span>
                    </div>
                  </div>
                </div>
                <div class="text-end" style="min-width: 180px;">
                  <div class="progress s5-summary-progress mb-1">
                    <div class="progress-bar s5-summary-bar" id="dgps_progress_bar" role="progressbar" style="width: 0%;"></div>
                  </div>
                  <small class="text-muted" style="font-size:11px;">Automated Dossier Sync</small>
                </div>
              </div>
            </div>

            {{-- Folder Checklist Container --}}
            <div class="s1-section-card mb-4">
              <div class="s5-folder-heading-wrap d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="s5-folder-icon-box">
                    <i class="fa fa-folder-open text-primary"></i>
                  </div>
                  <div>
                    <h5 class="s5-folder-title mb-0">1. DGPS Survey Field &amp; Boundary Data</h5>
                    <small class="s5-folder-subtitle">Official survey data formats: RINEX, CSV, PDF, DWG, and KML/KMZ</small>
                  </div>
                </div>
                <button type="button" class="btn btn-sm s5-btn-add-doc" id="btn_add_dgps_doc">
                  <i class="bi bi-plus-circle me-1"></i>+ Add Document
                </button>
              </div>

              <div class="p-3" id="dgps_checklist_container">
                @php
                  $dgpsDocs = [
                    ['name' => '1. Raw RINEX Base & Rover Observation Data', 'meta' => 'RINEX / DAT raw observation logs from dual-frequency receiver', 'type' => 'RINEX/DAT', 'mandatory' => true],
                    ['name' => '2. Benchmark Fixation & Control Network Sheet', 'meta' => 'SOI Benchmark reference and calibration certificate', 'type' => 'PDF', 'mandatory' => true],
                    ['name' => '3. Boundary Pillar Coordinates (Lat/Long & UTM)', 'meta' => 'Tabulated boundary pillar coordinates (CSV / Excel format)', 'type' => 'CSV/XLSX', 'mandatory' => true],
                    ['name' => '4. Cadastral Survey / FMB Map Superimposition', 'meta' => 'Revenue FMB / A-Register boundary overlay CAD sketch', 'type' => 'DWG/PDF', 'mandatory' => true],
                    ['name' => '5. Boundary Polygon KML / KMZ File', 'meta' => 'Google Earth 3D boundary polygon and pillar markers', 'type' => 'KML/KMZ', 'mandatory' => true],
                    ['name' => '6. DGPS Field Traverse & Calibration Log', 'meta' => 'Equipment accuracy log and field verification checklist', 'type' => 'PDF', 'mandatory' => false],
                  ];
                @endphp

                @foreach($dgpsDocs as $dIdx => $dItem)
                  <div class="checklist-row p-3 mb-2 d-flex align-items-center justify-content-between" data-dgps-row="{{ $dIdx }}" data-mandatory="{{ $dItem['mandatory'] ? '1' : '0' }}">
                    <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
                      <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0">
                        <i class="bi bi-file-earmark-text"></i>
                      </div>
                      <div class="text-truncate">
                        <div class="d-flex align-items-center gap-2 mb-1">
                          <span class="ci-name fw-bold text-dark text-truncate" style="font-size:13.5px;">{{ $dItem['name'] }}</span>
                          <span class="badge bg-light text-muted border" style="font-size:10.5px;">{{ $dItem['type'] }}</span>
                        </div>
                        <div class="ci-meta text-muted" style="font-size:11.5px;">{{ $dItem['meta'] }} &bull; <span class="text-secondary">Not uploaded</span></div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                      <select class="form-select form-select-sm doc-req-select {{ $dItem['mandatory'] ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                              name="dgps_doc_req[{{ $dIdx }}]" style="width:115px; font-weight:700;">
                        <option value="mandatory" {{ $dItem['mandatory'] ? 'selected' : '' }}>Mandatory</option>
                        <option value="optional" {{ !$dItem['mandatory'] ? 'selected' : '' }}>Optional</option>
                      </select>
                      <input type="file" class="d-none dgps-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.csv,.xlsx,.kml,.kmz,.dwg,.txt,.dat">
                      <button type="button" class="btn btn-sm s5-btn-upload btn-upload-dgps-item">
                        <i class="bi bi-upload me-1"></i>Upload
                      </button>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- Bottom Notice Card --}}
            <div class="s1-notice-card">
              <div class="d-flex align-items-center gap-3">
                <div class="s1-notice-icon-box">
                  <i class="bi bi-file-earmark-code-fill s1-notice-icon"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center gap-2 mb-1">
                    <strong class="text-navy" style="font-size:0.88rem;">COORDINATE PROJECTION STANDARD</strong>
                    <span class="badge bg-primary text-white" style="font-size:10px;">UTM Zone 44N</span>
                  </div>
                  <p class="s1-notice-text mb-0">Tabulated boundary coordinates must be submitted in WGS84 Geographic (Degrees, Minutes, Seconds) and projected UTM Zone 44N coordinates for seamless automated ingestion by the Mining Department portal.</p>
                </div>
              </div>
            </div>

          {{-- STEP 4: HANDLING TEAM ALLOCATION --}}
          @elseif($step === 4)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-people-fill me-1"></i> STEP 4 OF 6 &bull; HANDLING TEAM ALLOCATION
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~1-2 mins
                  </span>
                  <span class="dgps-status-pill">
                    <i class="bi bi-person-badge-fill me-1 text-primary"></i> Certified Crew
                  </span>
                </div>
              </div>
              <h3 class="s1-title">DGPS Survey Team &amp; In-Charge Personnel</h3>
              <p class="s1-subtitle mb-0">Assign certified land surveyors, instrument operators, and GIS mapping personnel responsible for field observations and certification.</p>
            </div>

            {{-- Section Card: Team Roster Table --}}
            <div class="s1-section-card mb-4">
              <div class="s1-section-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                  <div class="s1-section-icon-badge">
                    <i class="bi bi-person-lines-fill"></i>
                  </div>
                  <div>
                    <h5 class="s1-section-title mb-0">Designated Technical Personnel Roster</h5>
                    <small class="s1-section-desc">Manage survey crew members, official designations, and specific fieldwork duties.</small>
                  </div>
                </div>
                <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_dgps_handler">
                  <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
                </button>
              </div>

              <div class="s1-section-body p-0">
                <div class="table-responsive">
                  <table class="s8-table mb-0" id="dgps_handlers_table">
                    <thead>
                      <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th style="width: 28%;">Person Name <span class="text-danger">*</span></th>
                        <th style="width: 28%;">Role / Designation <span class="text-danger">*</span></th>
                        <th>Notes &amp; Responsibilities</th>
                        <th style="width: 70px;" class="text-center">Action</th>
                      </tr>
                    </thead>
                    <tbody id="dgps_handlers_tbody">
                      @php
                        $savedDgpsHandlers = $draft['handlers'] ?? [
                          ['name' => 'Er. A. Vijayakumar', 'role' => 'Chief Land Surveyor', 'notes' => 'DGPS base station setup & boundary benchmark fixation'],
                          ['name' => 'K. Prakash', 'role' => 'GIS / CAD Mapping Engineer', 'notes' => 'Post-processing rover points, area computation & KML drawing']
                        ];
                      @endphp
                      @foreach($savedDgpsHandlers as $hIdx => $h)
                        <tr>
                          <td class="text-center fw-bold row-num">{{ $hIdx + 1 }}</td>
                          <td>
                            <div class="d-flex align-items-center gap-2">
                              <div class="s8-avatar-icon flex-shrink-0">
                                <i class="bi bi-person-fill"></i>
                              </div>
                              <input type="text" name="handlers[{{ $hIdx }}][person_name]" class="form-control form-control-sm" value="{{ $h['person_name'] ?? ($h['name'] ?? '') }}" placeholder="e.g. Er. A. Vijayakumar" required>
                            </div>
                          </td>
                          <td>
                            <input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm" value="{{ $h['role'] ?? '' }}" placeholder="e.g. Chief Land Surveyor" required>
                          </td>
                          <td>
                            <input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm" value="{{ $h['notes'] ?? '' }}" placeholder="Responsibilities &amp; equipment handled">
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-dgps-handler {{ count($savedDgpsHandlers) === 1 ? 'disabled' : '' }}" title="Remove crew member">
                              <i class="bi bi-trash"></i>
                            </button>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            {{-- Role Preset Guidance Callout --}}
            <div class="s7-tip-box mb-0">
              <div class="d-flex align-items-start gap-3">
                <div class="s7-tip-icon">
                  <i class="bi bi-lightbulb-fill"></i>
                </div>
                <div class="s7-tip-content flex-grow-1">
                  <strong>Standard Technical Roles for DGPS Demarcation:</strong> You can assign specialized survey duties such as <em>Chief Land Surveyor</em> (SOP calibration &amp; certification), <em>Instrument Operator</em> (rover observation collection), and <em>GIS / CAD Mapping Engineer</em> (RINEX post-processing &amp; KML polygon authoring).
                </div>
              </div>
            </div>

          {{-- STEP 5: PAYMENT DETAILS & BILLING LEDGER --}}
          @elseif($step === 5)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-cash-coin me-1"></i> STEP 5 OF 6 &bull; PAYMENT DETAILS &amp; BILLING LEDGER
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~1-2 mins
                  </span>
                  <span class="s7-ledger-pill">
                    <i class="bi bi-shield-check me-1"></i> Active Settlement Ledger
                  </span>
                </div>
              </div>
              <h3 class="s1-title">Survey Service Fee &amp; Commercial Billing Ledger</h3>
              <p class="s1-subtitle mb-0">Track DGPS field survey service fee, advance collected, and balance settlement.</p>
            </div>

            @php
              $val     = (float)($draft['product_value'] ?? 35000);
              $paid    = (float)($draft['paid_amount'] ?? 35000);
              $pending = max(0, $val - $paid);
              $status  = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            {{-- Executive Readiness Banner --}}
            <div class="s7-banner-card mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="s7-banner-icon-badge">
                    <i class="bi bi-cash-coin"></i>
                  </div>
                  <div>
                    <h5 class="s7-banner-title">Financial Settlement &amp; Billing Ledger</h5>
                    <p class="s7-banner-desc mb-0">Record agreed survey fee, verify bank or UPI remittance receipts, and balance outstanding dues.</p>
                  </div>
                </div>
                <span class="s7-banner-status-pill badge bg-navy text-white">
                  <i class="bi bi-credit-card me-1"></i> Commercial Fee Gate
                </span>
              </div>
            </div>

            {{-- 3-Bento KPI Metric Cards --}}
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="s7-kpi-card s7-kpi-contract">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s7-kpi-label">Survey Service Value</span>
                    <div class="s7-kpi-icon-badge s7-kpi-icon-contract">
                      <i class="bi bi-file-earmark-text"></i>
                    </div>
                  </div>
                  <div class="s7-kpi-value text-navy" id="disp_dgps_val">₹ {{ number_format($val, 2) }}</div>
                  <div class="s7-kpi-subtext">Agreed survey contract value</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="s7-kpi-card s7-kpi-paid">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s7-kpi-label">Collected / Paid Advance</span>
                    <div class="s7-kpi-icon-badge s7-kpi-icon-paid">
                      <i class="bi bi-check-circle"></i>
                    </div>
                  </div>
                  <div class="s7-kpi-value text-success" id="disp_dgps_paid">₹ {{ number_format($paid, 2) }}</div>
                  <div class="s7-kpi-subtext">Verified collected remittance</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="s7-kpi-card s7-kpi-pending">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s7-kpi-label">Pending Balance Due</span>
                    <div class="s7-kpi-icon-badge s7-kpi-icon-pending">
                      <i class="bi bi-clock-history"></i>
                    </div>
                  </div>
                  <div class="s7-kpi-value text-danger" id="disp_dgps_pending">₹ {{ number_format($pending, 2) }}</div>
                  <div class="s7-kpi-subtext">Auto-calculated outstanding</div>
                </div>
              </div>
            </div>

            {{-- Integrated Fee Schedule Container --}}
            <div class="s7-form-section mb-4">
              <div class="s7-form-section-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-receipt text-primary fs-5"></i>
                  <span class="s7-form-section-title">Commercial Settlement Schedule</span>
                </div>
                <span class="s7-badge-status-tag">Audit-Locked Ledger</span>
              </div>
              <div class="s7-form-section-body">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="s7-label">Product / Fee Value (₹) <span class="text-danger">*</span></label>
                    <div class="s7-input-group">
                      <span class="s7-input-addon s7-addon-navy">₹</span>
                      <input type="number" step="0.01" min="0" name="product_value" id="field_dgps_val" class="form-control s7-input fw-bold" placeholder="0.00" value="{{ $val }}" required>
                    </div>
                    <small class="s7-field-hint">Total agreed DGPS survey charge.</small>
                  </div>
                  <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <label class="s7-label mb-0">Paid Amount (₹) <span class="text-danger">*</span></label>
                      <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm s7-preset-btn" onclick="setDgpsPaidPreset(1.0)">100%</button>
                        <button type="button" class="btn btn-sm s7-preset-btn" onclick="setDgpsPaidPreset(0.5)">50%</button>
                        <button type="button" class="btn btn-sm s7-preset-btn" onclick="setDgpsPaidPreset(0)">Clear</button>
                      </div>
                    </div>
                    <div class="s7-input-group">
                      <span class="s7-input-addon s7-addon-success">₹</span>
                      <input type="number" step="0.01" min="0" name="paid_amount" id="field_dgps_paid" class="form-control s7-input text-success fw-bold" placeholder="0.00" value="{{ $paid }}" required>
                    </div>
                    <small class="s7-field-hint">Initial advance or complete settlement.</small>
                  </div>
                  <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <label class="s7-label mb-0">Pending Balance (₹)</label>
                      <span class="s7-auto-tag">Auto-Calculated</span>
                    </div>
                    <div class="s7-input-group s7-group-readonly">
                      <span class="s7-input-addon s7-addon-danger">₹</span>
                      <input type="number" step="0.01" name="pending_amount" id="field_dgps_pending" class="form-control s7-input s7-input-readonly fw-bold" placeholder="0.00" readonly value="{{ number_format($pending, 2, '.', '') }}">
                    </div>
                    <small class="s7-field-hint">Outstanding balance receivable.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="s7-label">Settlement Status <span class="text-danger">*</span></label>
                    <select class="form-select s7-select" name="payment_status" id="field_dgps_status">
                      <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
                      <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial Payment Received</option>
                      <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                    </select>
                    <small class="s7-field-hint">Audit classification for financial tracking.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="s7-label">Transaction Reference / Receipt Notes</label>
                    <input type="text" class="form-control s7-notes-input" name="payment_notes" placeholder="e.g. Bank transfer ref / UPI ID / Receipt #" value="{{ $draft['payment_notes'] ?? 'UPI Ref #DGPS-66120 - Full payment received upon field completion' }}">
                    <small class="s7-field-hint">Reference number for accounting reconciliation.</small>
                  </div>
                </div>

                {{-- Live Settlement Assessment Box --}}
                <div class="s7-assessment-wrapper mt-3">
                  <div class="s7-feedback-box d-flex align-items-center justify-content-between" id="dgps_settlement_assessment">
                    <div class="d-flex align-items-center gap-3">
                      <div class="s7-feedback-icon" id="dgps_feedback_icon">
                        <i class="bi {{ $pending == 0 ? 'bi-check-circle-fill text-success' : ($paid > 0 ? 'bi-pie-chart-fill text-warning' : 'bi-exclamation-circle-fill text-danger') }}"></i>
                      </div>
                      <div>
                        <div class="s7-feedback-sub">SETTLEMENT ASSESSMENT</div>
                        <div class="s7-feedback-title fw-bold" id="dgps_feedback_text">
                          {{ $pending == 0 ? 'Full payment verified & balanced.' : ($paid > 0 ? 'Partial payment recorded - balance due before final report release.' : 'Payment pending - fee collection required.') }}
                        </div>
                      </div>
                    </div>
                    <span class="badge s7-feedback-badge {{ $pending == 0 ? 'bg-success text-white' : ($paid > 0 ? 'bg-warning text-dark' : 'bg-danger text-white') }}" id="dgps_feedback_badge">
                      {{ $pending == 0 ? 'Settled (₹0 Balance)' : ($paid > 0 ? 'Partial Collection' : 'Pending Payment') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Guidance Pro-Tip Callout --}}
            <div class="s7-tip-box mb-0">
              <div class="d-flex align-items-start gap-3">
                <div class="s7-tip-icon">
                  <i class="bi bi-info-circle-fill"></i>
                </div>
                <div class="s7-tip-content flex-grow-1">
                  <strong>Accounting Rule:</strong> All DGPS survey charges are subject to statutory accounting audit. Once marked <em>Paid</em>, the transaction receipt number is automatically attached to the client's corporate tracking ledger.
                </div>
              </div>
            </div>

          {{-- STEP 6: PREVIEW & FINAL CONFIRMATION --}}
          @elseif($step === 6)
            {{-- Executive Header Block --}}
            <div class="s1-header-block mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <span class="s1-eyebrow">
                  <i class="bi bi-shield-check me-1"></i> STEP 6 OF 6 &bull; PREVIEW &amp; FINAL CONFIRMATION
                </span>
                <div class="d-flex align-items-center gap-2">
                  <span class="s1-time-tag">
                    <i class="bi bi-clock me-1"></i> Est. ~2-3 mins
                  </span>
                  <span class="s8-scrutiny-pill">
                    <i class="bi bi-patch-check-fill me-1"></i> Executive Review Gate
                  </span>
                </div>
              </div>
              <h3 class="s1-title">DGPS Survey Verification &amp; Final Dossier Audit</h3>
              <p class="s1-subtitle mb-0">Perform executive pre-submission audit across applicant particulars, boundary parameters, survey crew roster, and financial ledger.</p>
            </div>

            @php
              $customerObj = !empty($draft['customer_id']) ? \App\Models\Customer::find($draft['customer_id']) : $customers->first();
              $val         = (float)($draft['product_value'] ?? 35000);
              $paid        = (float)($draft['paid_amount'] ?? 35000);
              $pending     = max(0, $val - $paid);
              $pStatus     = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            {{-- Executive Audit Banner --}}
            <div class="s8-banner-card mb-4">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="s8-banner-icon-badge">
                    <i class="bi bi-patch-check-fill"></i>
                  </div>
                  <div>
                    <h5 class="s8-banner-title">DGPS Survey Verification &amp; Final Dossier Review</h5>
                    <p class="s8-banner-desc mb-0">Cross-examine boundary schedule, technical personnel allocation, and commercial clearance before finalizing.</p>
                  </div>
                </div>
                <span class="s8-banner-status-pill">
                  <i class="bi bi-shield-lock-fill me-1"></i> Pre-Submission Scrutiny
                </span>
              </div>
            </div>

            {{-- 4-Bento KPI Metric Cards --}}
            <div class="row g-3 mb-4">
              <div class="col-md-3">
                <div class="s8-kpi-card s8-kpi-applicant">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s8-kpi-label">Applicant Entity</span>
                    <div class="s8-kpi-icon-badge s8-kpi-icon-blue">
                      <i class="bi bi-building"></i>
                    </div>
                  </div>
                  <div class="s8-kpi-val text-truncate" title="{{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites') }}">
                    {{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites') }}
                  </div>
                  <span class="s8-chip-blue">{{ $customerObj?->mimas_no ?: 'ID: Verified' }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="s8-kpi-card s8-kpi-location">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s8-kpi-label">Survey Extent</span>
                    <div class="s8-kpi-icon-badge s8-kpi-icon-emerald">
                      <i class="bi bi-pin-map-fill"></i>
                    </div>
                  </div>
                  <div class="s8-kpi-val">{{ $draft['lease_area_ha'] ?? '3.85' }} Hectares</div>
                  <span class="s8-chip-emerald">{{ $draft['location'] ?? 'Salem District' }}</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="s8-kpi-card s8-kpi-docs">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s8-kpi-label">Field Personnel</span>
                    <div class="s8-kpi-icon-badge s8-kpi-icon-amber">
                      <i class="bi bi-people-fill"></i>
                    </div>
                  </div>
                  <div class="s8-kpi-val">{{ count($draft['handlers'] ?? []) ?: 2 }} Assigned</div>
                  <span class="s8-chip-amber">Survey Crew Ready</span>
                </div>
              </div>
              <div class="col-md-3">
                <div class="s8-kpi-card s8-kpi-finance">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="s8-kpi-label">Financial Status</span>
                    <div class="s8-kpi-icon-badge s8-kpi-icon-purple">
                      <i class="bi bi-cash-stack"></i>
                    </div>
                  </div>
                  <div class="s8-kpi-val">₹ {{ number_format($val, 2) }}</div>
                  <span class="s8-chip-purple">{{ ucfirst($pStatus) }} (₹{{ number_format($pending, 0) }} Bal)</span>
                </div>
              </div>
            </div>

            {{-- 4 Dedicated Review Panels --}}
            <div class="d-flex flex-column gap-3 mb-4">
              {{-- Panel 1: Survey Request & Applicant Profile --}}
              <div class="s8-section-panel">
                <div class="s8-panel-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <div class="s8-panel-header-icon s8-icon-blue">
                      <i class="bi bi-person-badge"></i>
                    </div>
                    <span class="s8-panel-title">1. Survey Request &amp; Applicant Entity Particulars</span>
                    <span class="s8-panel-step-badge">Step 1</span>
                  </div>
                  <a href="{{ route('dgps-survey.step', 1) }}" class="s8-btn-edit">
                    <i class="bi bi-pencil-square me-1"></i> Edit Step 1
                  </a>
                </div>
                <div class="s8-panel-body">
                  <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Client / Firm Name</span>
                        <span class="s8-tile-value text-truncate">{{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}</span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Customer ID / MIMAS</span>
                        <span class="s8-tile-value font-monospace">{{ $customerObj?->mimas_no ?: 'TN-MMS-SLM-001' }}</span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Primary Mobile Phone</span>
                        <span class="s8-tile-value">{{ $draft['primary_phone'] ?? ($customerObj?->mobile_num ?? '9842109876') }}</span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Secondary Contact</span>
                        <span class="s8-tile-value">{{ $draft['secondary_phone'] ?? ($customerObj?->secondary_mobile_num ?? 'None Specified') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Panel 2: Concession Area & Survey Demarcation --}}
              <div class="s8-section-panel">
                <div class="s8-panel-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <div class="s8-panel-header-icon s8-icon-emerald">
                      <i class="bi bi-geo-alt"></i>
                    </div>
                    <span class="s8-panel-title">2. Concession Area &amp; Boundary Demarcation</span>
                    <span class="s8-panel-step-badge">Step 1 &bull; Step 2</span>
                  </div>
                  <a href="{{ route('dgps-survey.step', 1) }}" class="s8-btn-edit">
                    <i class="bi bi-pencil-square me-1"></i> Edit Boundaries
                  </a>
                </div>
                <div class="s8-panel-body">
                  <div class="row g-3">
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Survey Request No.</span>
                        <span class="s8-tile-value font-monospace text-primary">{{ $draft['survey_no'] ?? 'DGPS-2026-0031' }}</span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Associated Mining Lease</span>
                        <span class="s8-tile-value">
                          @if(!empty($draft['lease_application_id']))
                            Lease #{{ $draft['lease_application_id'] }}
                          @else
                            Standalone Survey
                          @endif
                        </span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Lease Area Extent</span>
                        <span class="s8-tile-value">{{ $draft['lease_area_ha'] ?? '3.85' }} Hectares</span>
                      </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                      <div class="s8-data-tile">
                        <span class="s8-tile-label">Location / Village / Taluk</span>
                        <span class="s8-tile-value text-truncate">{{ $draft['location'] ?? 'Salem / Semmandapatti' }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Panel 3: Technical Survey Team --}}
              <div class="s8-section-panel">
                <div class="s8-panel-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <div class="s8-panel-header-icon s8-icon-amber">
                      <i class="bi bi-people"></i>
                    </div>
                    <span class="s8-panel-title">3. Designated Survey Personnel &amp; In-Charge Crew</span>
                    <span class="s8-panel-step-badge">Step 4</span>
                  </div>
                  <a href="{{ route('dgps-survey.step', 4) }}" class="s8-btn-edit">
                    <i class="bi bi-pencil-square me-1"></i> Edit Team
                  </a>
                </div>
                <div class="s8-panel-body p-0">
                  <div class="table-responsive">
                    <table class="s8-table mb-0">
                      <thead>
                        <tr>
                          <th style="width: 50px;" class="text-center">#</th>
                          <th style="width: 35%;">Person Name</th>
                          <th style="width: 30%;">Designation / Role</th>
                          <th>Responsibilities</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                          $previewHandlers = $draft['handlers'] ?? [
                            ['name' => 'Er. A. Vijayakumar', 'role' => 'Chief Land Surveyor', 'notes' => 'DGPS base station setup & benchmark fixation'],
                            ['name' => 'K. Prakash', 'role' => 'GIS / CAD Mapping Engineer', 'notes' => 'Post-processing rover points, area computation & KML drawing']
                          ];
                        @endphp
                        @foreach($previewHandlers as $hIdx => $h)
                          <tr>
                            <td class="text-center fw-bold">{{ $hIdx + 1 }}</td>
                            <td>
                              <div class="d-flex align-items-center gap-2">
                                <div class="s8-avatar-icon flex-shrink-0">
                                  <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="fw-bold text-dark">{{ $h['person_name'] ?? ($h['name'] ?? 'Survey Staff') }}</span>
                              </div>
                            </td>
                            <td>
                              <span class="s8-role-badge">{{ $h['role'] ?? 'Surveyor' }}</span>
                            </td>
                            <td class="text-muted small">
                              {{ $h['notes'] ?? 'General survey observation & data acquisition' }}
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              {{-- Panel 4: Commercial Ledger & Remittance Breakdown --}}
              <div class="s8-section-panel">
                <div class="s8-panel-header d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <div class="s8-panel-header-icon s8-icon-teal">
                      <i class="bi bi-receipt"></i>
                    </div>
                    <span class="s8-panel-title">4. Commercial Billing Ledger &amp; Remittance Breakdown</span>
                    <span class="s8-panel-step-badge">Step 5</span>
                  </div>
                  <a href="{{ route('dgps-survey.step', 5) }}" class="s8-btn-edit">
                    <i class="bi bi-pencil-square me-1"></i> Edit Ledger
                  </a>
                </div>
                <div class="s8-panel-body">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <div class="s8-ledger-card border-primary-subtle">
                        <span class="s8-ledger-sub text-primary">Agreed Contract Value</span>
                        <div class="s8-ledger-amount text-navy">₹ {{ number_format($val, 2) }}</div>
                        <small class="s8-ledger-hint text-muted">Total survey fee</small>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="s8-ledger-card border-success-subtle">
                        <span class="s8-ledger-sub text-success">Collected / Paid Advance</span>
                        <div class="s8-ledger-amount text-success">₹ {{ number_format($paid, 2) }}</div>
                        <small class="s8-ledger-hint text-muted">Settled remittance</small>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="s8-ledger-card border-warning-subtle">
                        <span class="s8-ledger-sub text-warning">Pending Balance Due</span>
                        <div class="s8-ledger-amount {{ $pending > 0 ? 'text-danger' : 'text-success' }}">₹ {{ number_format($pending, 2) }}</div>
                        <small class="s8-ledger-hint text-muted">{{ $pending > 0 ? 'Outstanding collection' : 'Balanced (₹0 Due)' }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Pre-Submission Compliance Declaration Card --}}
            <div class="s8-declaration-card mb-4">
              <div class="d-flex align-items-start gap-3">
                <div class="s8-declaration-icon-badge">
                  <i class="bi bi-shield-check"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="s8-declaration-title mb-1">Statutory DGPS Survey Compliance Declaration</h6>
                  <p class="s8-declaration-text mb-3">I hereby certify that all geodetic coordinates, boundary pillar schedules, and land extent evaluations have been surveyed on site with a dual-frequency GNSS receiver calibrated against permanent Survey of India benchmarks in accordance with Rule 36 of the Tamil Nadu Minor Mineral Concession Rules 1959.</p>
                  <div class="form-check s8-check-confirm">
                    <input class="form-check-input" type="checkbox" id="confirm_dgps_scrutiny_ack" required checked>
                    <label class="form-check-label fw-bold" for="confirm_dgps_scrutiny_ack">
                      I have scrutinized all survey findings, team allocations, and financial records and authorize final synchronization.
                    </label>
                  </div>
                </div>
              </div>
            </div>
          @endif

          {{-- Wizard Navigation Actions --}}
          <div class="wizard-actions mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="{{ $step === 1 ? route('dgps-survey.index') : route('dgps-survey.step', $step - 1) }}" class="btn btn-outline-navy btn-sm px-3">
              <i class="bi bi-arrow-left me-1"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
            </a>
            @can('dgps.create')
              <button type="submit" class="btn {{ $step === 6 ? 'btn-green' : 'btn-navy' }} px-4 fw-bold" id="btnSubmitDgps">
                {{ $step === 6 ? 'Finish & Save Survey' : 'Save & Continue' }} <i class="bi {{ $step === 6 ? 'bi-check2-circle' : 'bi-arrow-right' }} ms-1"></i>
              </button>
            @endcan
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- JAVASCRIPT FOR DYNAMIC HANDLERS, MIMAS LOOKUP, DOC UPLOAD & PAYMENT MATH --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Append preset notes on Step 2
  window.appendDgpsNote = function(text) {
    const txtArea = document.getElementById('survey_team_notes');
    if (!txtArea) return;
    if (txtArea.value.trim().length > 0) {
      txtArea.value += ' &bull; ' + text;
    } else {
      txtArea.value = text;
    }
  };

  // Preset button for Step 7 Payment
  window.setDgpsPaidPreset = function(multiplier) {
    const fVal = document.getElementById('field_dgps_val');
    const fPaid = document.getElementById('field_dgps_paid');
    if (!fVal || !fPaid) return;
    const val = parseFloat(fVal.value) || 0;
    fPaid.value = (val * multiplier).toFixed(2);
    recalcDgpsPayment();
  };

  // DGPS Step 3: Upload & View handlers
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-dgps-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.dgps-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('dgps-file-input')) {
      const input = e.target;
      const row = input.closest('.checklist-row');
      if (input.files && input.files[0] && row) {
        const file = input.files[0];
        const blobUrl = URL.createObjectURL(file);
        row.classList.add('up');

        const iconDiv = row.querySelector('.ci-icon');
        if (iconDiv) {
          iconDiv.style.background = '#dcfce7';
          iconDiv.style.color = '#15803d';
          iconDiv.innerHTML = '<i class="bi bi-check-lg"></i>';
        }

        const metaDiv = row.querySelector('.ci-meta');
        if (metaDiv) {
          const sizeKb = Math.round(file.size / 1024);
          metaDiv.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ${file.name}</span> &middot; ${sizeKb} KB &bull; <span class="badge bg-success-subtle text-success">Uploaded</span>`;
        }

        const slot = row.querySelector('.action-slot');
        if (slot) {
          let viewBtn = slot.querySelector('.btn-view-dgps-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-2 btn-view-dgps-item me-1';
            viewBtn.style.fontSize = '.72rem';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.title = 'View document in separate page';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-dgps-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-dgps-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-dgps-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        updateDgpsDocProgress();
      }
    }
  });

  function updateDgpsDocProgress() {
    const total = document.querySelectorAll('#dgps_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#dgps_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('dgps_doc_counter');
    const bar = document.getElementById('dgps_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} uploaded`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

  // DGPS Step 3: Add Custom Document button
  const btnAddDgpsDoc = document.getElementById('btn_add_dgps_doc');
  if (btnAddDgpsDoc) {
    btnAddDgpsDoc.addEventListener('click', function() {
      const docName = prompt('Enter DGPS Survey Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('dgps_checklist_container');
      const count = container.querySelectorAll('.checklist-row').length + 1;
      const row = document.createElement('div');
      row.className = 'checklist-row p-3 mb-2 d-flex align-items-center justify-content-between';
      row.setAttribute('data-mandatory', '0');
      row.innerHTML = `
        <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
          <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background:#f1f5f9; color:#64748b;">
            <i class="bi bi-file-earmark-plus"></i>
          </div>
          <div class="text-truncate">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="badge bg-light text-primary border" style="font-size:10px;">Custom</span>
              <span class="ci-name fw-bold text-dark text-truncate" style="font-size:13.5px;">${count}. ${docName.trim()}</span>
            </div>
            <div class="ci-meta text-muted" style="font-size:11.5px;">Custom survey attachment &bull; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <select class="form-select form-select-sm doc-req-select border-secondary-subtle text-muted bg-light"
                  name="dgps_custom_doc_req_${count}" style="width:115px; font-weight:700;">
            <option value="mandatory">Mandatory</option>
            <option value="optional" selected>Optional</option>
          </select>
          <input type="file" class="d-none dgps-file-input" accept=".pdf,.csv,.xlsx,.kml,.kmz,.dwg,.txt,.dat">
          <button type="button" class="btn btn-sm s5-btn-upload btn-upload-dgps-item">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updateDgpsDocProgress();
      bindDgpsDocReqListeners();
    });
  }

  function bindDgpsDocReqListeners() {
    document.querySelectorAll('#dgps_checklist_container .doc-req-select').forEach(function(sel) {
      sel.onchange = function() {
        const row = this.closest('.checklist-row');
        const isMandatory = this.value === 'mandatory';
        if (row) {
          row.setAttribute('data-mandatory', isMandatory ? '1' : '0');
        }
        if (isMandatory) {
          this.className = 'form-select form-select-sm doc-req-select border-danger-subtle text-danger bg-danger-subtle';
        } else {
          this.className = 'form-select form-select-sm doc-req-select border-secondary-subtle text-muted bg-light';
        }
      };
    });
  }
  bindDgpsDocReqListeners();

  // Handlers dynamic add/remove (Step 6)
  const btnAdd = document.getElementById('btn_add_dgps_handler');
  const tbody = document.getElementById('dgps_handlers_tbody');

  function reindexDgpsRows() {
    if (!tbody) return;
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((row, idx) => {
      const numCol = row.querySelector('.row-num');
      if (numCol) numCol.textContent = idx + 1;
      row.querySelectorAll('input').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
          input.setAttribute('name', name.replace(/handlers\[\d+\]/, 'handlers[' + idx + ']'));
        }
      });
      const delBtn = row.querySelector('.btn-remove-dgps-handler');
      if (delBtn) {
        if (rows.length === 1) {
          delBtn.classList.add('disabled');
        } else {
          delBtn.classList.remove('disabled');
        }
      }
    });
  }

  if (btnAdd && tbody) {
    btnAdd.addEventListener('click', function() {
      const count = tbody.querySelectorAll('tr').length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-center fw-bold row-num">${count + 1}</td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <div class="s8-avatar-icon flex-shrink-0">
              <i class="bi bi-person-fill"></i>
            </div>
            <input type="text" name="handlers[${count}][person_name]" class="form-control form-control-sm" placeholder="e.g. S. Suresh" required>
          </div>
        </td>
        <td>
          <input type="text" name="handlers[${count}][role]" class="form-control form-control-sm" placeholder="e.g. GPS Instrument Operator" required>
        </td>
        <td>
          <input type="text" name="handlers[${count}][notes]" class="form-control form-control-sm" placeholder="e.g. Base and rover field observations">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-dgps-handler" title="Remove crew member">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
      reindexDgpsRows();
    });

    tbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-dgps-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const tr = btn.closest('tr');
        if (tr) {
          tr.remove();
          reindexDgpsRows();
        }
      }
    });
  }

  // Payment live calculation (Step 7)
  const fVal = document.getElementById('field_dgps_val');
  const fPaid = document.getElementById('field_dgps_paid');
  const fPending = document.getElementById('field_dgps_pending');
  const fStatus = document.getElementById('field_dgps_status');
  const dVal = document.getElementById('disp_dgps_val');
  const dPaid = document.getElementById('disp_dgps_paid');
  const dPending = document.getElementById('disp_dgps_pending');
  const fIcon = document.getElementById('dgps_feedback_icon');
  const fText = document.getElementById('dgps_feedback_text');
  const fBadge = document.getElementById('dgps_feedback_badge');

  function fmtINR(val) {
    return '₹ ' + (Number(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcDgpsPayment() {
    if (!fVal || !fPaid) return;
    const val = parseFloat(fVal.value) || 0;
    const paid = parseFloat(fPaid.value) || 0;
    const pending = Math.max(0, val - paid);

    if (fPending) fPending.value = pending.toFixed(2);
    if (dVal) dVal.textContent = fmtINR(val);
    if (dPaid) dPaid.textContent = fmtINR(paid);
    if (dPending) dPending.textContent = fmtINR(pending);

    if (fStatus) {
      if (val === 0 && paid === 0) {
        fStatus.value = 'pending';
      } else if (paid >= val && val > 0) {
        fStatus.value = 'paid';
      } else if (paid > 0 && paid < val) {
        fStatus.value = 'partial';
      } else {
        fStatus.value = 'pending';
      }
    }

    // Live feedback box update
    if (fText && fBadge && fIcon) {
      if (pending === 0 && val > 0) {
        fIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success fs-4"></i>';
        fText.textContent = 'Full payment verified & balanced. Zero outstanding balance.';
        fBadge.className = 'badge s7-feedback-badge bg-success text-white';
        fBadge.textContent = 'Settled (₹0 Balance)';
      } else if (paid > 0 && paid < val) {
        fIcon.innerHTML = '<i class="bi bi-pie-chart-fill text-warning fs-4"></i>';
        fText.textContent = 'Partial payment recorded - balance due before final certification release.';
        fBadge.className = 'badge s7-feedback-badge bg-warning text-dark';
        fBadge.textContent = 'Partial Collection';
      } else {
        fIcon.innerHTML = '<i class="bi bi-exclamation-circle-fill text-danger fs-4"></i>';
        fText.textContent = 'Payment pending - survey service fee collection required.';
        fBadge.className = 'badge s7-feedback-badge bg-danger text-white';
        fBadge.textContent = 'Pending Payment';
      }
    }
  }

  if (fVal && fPaid) {
    fVal.addEventListener('input', recalcDgpsPayment);
    fPaid.addEventListener('input', recalcDgpsPayment);
  }

  // Step 1: Customer Unique ID Lookup & Instant Autofill
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const feedbackBox = document.getElementById('mimas_feedback_box');

  function applyDgpsCustomerAutofill(c) {
    if (!c) return;
    const selCust = document.getElementById('select_dgps_customer');
    const fArea = document.getElementById('field_dgps_area');
    const fLoc = document.getElementById('field_dgps_location');
    const selLease = document.getElementById('select_dgps_lease');

    if (selCust) {
      for (let i = 0; i < selCust.options.length; i++) {
        const opt = selCust.options[i];
        const mimas = (opt.dataset.mimas || '').toUpperCase();
        const text = (opt.textContent || '').toUpperCase();
        const queryMimas = (c.mimas_no || '').toUpperCase();
        const queryName = (c.customer_name || '').toUpperCase();
        const queryCompany = (c.company_name || '').toUpperCase();

        if ((queryMimas && mimas === queryMimas) || opt.value == c.id || (queryCompany && text.includes(queryCompany)) || (queryName && text.includes(queryName))) {
          selCust.selectedIndex = i;
          break;
        }
      }
    }

    if (fArea) {
      fArea.value = c.area || '3.85';
    }

    if (fLoc) {
      fLoc.value = (c.address || '') + (c.district_name ? ', ' + c.district_name : '');
    }

    const fPrimaryPhone = document.getElementById('field_dgps_primary_phone');
    const fSecondaryPhone = document.getElementById('field_dgps_secondary_phone');
    if (fPrimaryPhone) {
      fPrimaryPhone.value = c.mobile_num || '';
    }
    if (fSecondaryPhone) {
      fSecondaryPhone.value = c.secondary_mobile_num || '';
    }

    // Try matching customer in lease dropdown
    if (selLease) {
      for (let i = 0; i < selLease.options.length; i++) {
        const opt = selLease.options[i];
        if (opt.dataset.customer == c.id) {
          selLease.selectedIndex = i;
          break;
        }
      }
    }

    // Visual cue on all autofilled fields
    document.querySelectorAll('.auto-filled-field').forEach(el => {
      el.classList.add('field-autofilled');
      setTimeout(() => el.classList.remove('field-autofilled'), 3000);
    });

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

  function performDgpsMimasLookup() {
    const val = mimasSearchInput ? mimasSearchInput.value.trim() : '';
    if (!val) {
      if (feedbackBox) {
        feedbackBox.style.display = 'block';
        feedbackBox.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-exclamation-triangle me-1"></i> Please enter or select a Customer Unique ID first.</div>';
      }
      return;
    }

    if (btnLookupMimas) {
      btnLookupMimas.disabled = true;
      btnLookupMimas.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Searching...';
    }
    if (feedbackBox) feedbackBox.style.display = 'none';

    fetch('/customers/lookup-mimas/' + encodeURIComponent(val))
      .then(res => res.json())
      .then(res => {
        if (btnLookupMimas) {
          btnLookupMimas.disabled = false;
          btnLookupMimas.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (res.status === 1 && res.data) {
          applyDgpsCustomerAutofill(res.data);
        } else {
          if (feedbackBox) {
            feedbackBox.style.display = 'block';
            feedbackBox.innerHTML = `<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-info-circle me-1"></i> ${res.message || 'No customer found.'}</div>`;
          }
        }
      })
      .catch(err => {
        if (btnLookupMimas) {
          btnLookupMimas.disabled = false;
          btnLookupMimas.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (feedbackBox) {
          feedbackBox.style.display = 'block';
          feedbackBox.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small"><i class="fa fa-times-circle me-1"></i> Customer not found. You can enter details manually below.</div>';
        }
      });
  }

  const selectDgpsCustomer = document.getElementById('select_dgps_customer');
  if (selectDgpsCustomer) {
    selectDgpsCustomer.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const fPrimaryPhone = document.getElementById('field_dgps_primary_phone');
      const fSecondaryPhone = document.getElementById('field_dgps_secondary_phone');
      if (opt && fPrimaryPhone) {
        fPrimaryPhone.value = opt.dataset.mobile || '';
      }
      if (opt && fSecondaryPhone) {
        fSecondaryPhone.value = opt.dataset.secondaryMobile || '';
      }
    });
  }

  if (btnLookupMimas) btnLookupMimas.addEventListener('click', performDgpsMimasLookup);
  if (mimasSearchInput) {
    mimasSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performDgpsMimasLookup();
      }
    });
    mimasSearchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performDgpsMimasLookup();
      }
    });
  }
});
</script>

<style>
/* =========================================================================
   DGPS SURVEY EXECUTIVE SUITE STYLES (LIGHT THEME)
   ========================================================================= */
#gtms-dgps-wizard-page {
  --dgps-primary: #0F1E4D;
  --dgps-accent: #2563eb;
  --dgps-success: #10b981;
  --dgps-border: #e2e8f0;
  --dgps-surface: #ffffff;
  --dgps-surface-subtle: #f8fafc;
}

#gtms-dgps-wizard-page .dgps-wizard-shell {
  max-width: 1040px;
  margin: 0 auto;
}

/* Stepper Card */
#gtms-dgps-wizard-page .dgps-stepper-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 20px 24px;
  box-shadow: 0 2px 10px rgba(15, 30, 77, 0.04);
}
#gtms-dgps-wizard-page .dgps-stepper-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  color: #0284c7;
  letter-spacing: 0.06em;
  display: block;
  margin-bottom: 2px;
}
#gtms-dgps-wizard-page .dgps-stepper-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .dgps-step-count-pill {
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 0.78rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 20px;
  border: 1px solid #bfdbfe;
}

/* Stepper Bar */
#gtms-dgps-wizard-page .step-progress {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin-top: 10px;
}
#gtms-dgps-wizard-page .step-progress::before {
  content: "";
  position: absolute;
  top: 18px;
  left: 30px;
  right: 30px;
  height: 2px;
  background: #e2e8f0;
  z-index: 1;
}
#gtms-dgps-wizard-page .sp-step {
  position: relative;
  z-index: 2;
  text-align: center;
  flex: 1;
  text-decoration: none;
  cursor: default;
}
#gtms-dgps-wizard-page .sp-step.is-clickable {
  cursor: pointer;
}
#gtms-dgps-wizard-page .sp-step .circ {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #ffffff;
  border: 2px solid #cbd5e1;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.85rem;
  margin: 0 auto 6px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .sp-step.active .circ {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #ffffff;
  box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
}
#gtms-dgps-wizard-page .sp-step.done .circ {
  background: #10b981;
  border-color: #10b981;
  color: #ffffff;
}
#gtms-dgps-wizard-page .sp-label {
  font-size: 0.76rem;
  font-weight: 600;
  color: #64748b;
  transition: color 0.2s ease;
}
#gtms-dgps-wizard-page .sp-step.active .sp-label {
  color: #0F1E4D;
  font-weight: 700;
}
#gtms-dgps-wizard-page .sp-step.done .sp-label {
  color: #10b981;
}

/* Wizard Main Card */
#gtms-dgps-wizard-page .wizard-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 30px 34px;
  box-shadow: 0 4px 20px rgba(15, 30, 77, 0.05);
}

/* Executive Header Block */
#gtms-dgps-wizard-page .s1-header-block {
  border-bottom: 1.5px solid #e2e8f0;
  padding-bottom: 18px;
}
#gtms-dgps-wizard-page .s1-eyebrow {
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  color: #0284c7;
}
#gtms-dgps-wizard-page .s1-time-tag {
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
#gtms-dgps-wizard-page .dgps-status-pill {
  font-size: 0.75rem;
  font-weight: 700;
  color: #0F1E4D;
  background: #e0f2fe;
  border: 1px solid #bae6fd;
  padding: 4px 12px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
}
#gtms-dgps-wizard-page .s1-title {
  font-size: 1.35rem;
  font-weight: 800;
  color: #0F1E4D;
  margin-bottom: 4px;
}
#gtms-dgps-wizard-page .s1-subtitle {
  font-size: 0.88rem;
  color: #64748b;
  line-height: 1.45;
}

/* Customer Unique ID Lookup Card */
#gtms-dgps-wizard-page .s1-lookup-card {
  background: #f0f7ff;
  border: 1.5px dashed #93c5fd;
  border-radius: 12px;
  padding: 18px 20px;
}
#gtms-dgps-wizard-page .s1-lookup-icon-badge {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #dbeafe;
  color: #1d4ed8;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
}
#gtms-dgps-wizard-page .s1-lookup-label {
  font-size: 0.96rem;
  font-weight: 700;
  color: #1e3a8a;
}
#gtms-dgps-wizard-page .s1-lookup-desc {
  font-size: 0.82rem;
  color: #475569;
}
#gtms-dgps-wizard-page .s1-badge-autofill {
  font-size: 0.72rem;
  font-weight: 700;
  background: #1d4ed8;
  color: #ffffff;
  padding: 4px 10px;
  border-radius: 6px;
}
#gtms-dgps-wizard-page .s1-lookup-input-group {
  display: flex;
  align-items: stretch;
  border: 1.5px solid #93c5fd;
  border-radius: 9px;
  overflow: hidden;
  background: #ffffff;
}
#gtms-dgps-wizard-page .s1-lookup-addon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  background: #f8fafc;
  color: #1d4ed8;
  font-size: 0.95rem;
  border-right: 1.5px solid #93c5fd;
}
#gtms-dgps-wizard-page .s1-lookup-field {
  border: none !important;
  box-shadow: none !important;
  font-weight: 700;
  text-transform: uppercase;
  color: #0F1E4D;
  padding: 8px 14px;
}
#gtms-dgps-wizard-page .s1-btn-fetch {
  background: #1d4ed8;
  color: #ffffff;
  border: none;
  font-weight: 700;
  font-size: 0.84rem;
  padding: 0 18px;
  transition: background 0.2s ease;
}
#gtms-dgps-wizard-page .s1-btn-fetch:hover {
  background: #1e40af;
  color: #ffffff;
}

/* Section Cards */
#gtms-dgps-wizard-page .s1-section-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}
#gtms-dgps-wizard-page .s1-section-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 13px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}
#gtms-dgps-wizard-page .s1-section-icon-badge {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #eff6ff;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s1-section-title {
  font-size: 0.92rem;
  font-weight: 700;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .s1-section-desc {
  font-size: 0.78rem;
  color: #64748b;
  display: block;
}
#gtms-dgps-wizard-page .s1-section-body {
  padding: 20px 22px;
}
#gtms-dgps-wizard-page .s1-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: #0F1E4D;
  margin-bottom: 4px;
  display: block;
}
#gtms-dgps-wizard-page .s1-opt-tag {
  font-size: 0.68rem;
  font-weight: 600;
  color: #64748b;
  background: #f1f5f9;
  padding: 2px 6px;
  border-radius: 4px;
  margin-left: 4px;
}
#gtms-dgps-wizard-page .s1-hint {
  font-size: 0.74rem;
  color: #64748b;
  margin-top: 3px;
  display: block;
}

/* High Contrast Input Groups */
#gtms-dgps-wizard-page .s1-input-group {
  display: flex;
  align-items: stretch;
  height: 42px;
  border: 1.5px solid #cbd5e1;
  border-radius: 8px;
  overflow: hidden;
  background: #ffffff;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s1-input-group:focus-within {
  border-color: #0F1E4D;
  box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
}
#gtms-dgps-wizard-page .s1-addon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  background: #f8fafc;
  border-right: 1.5px solid #cbd5e1;
  color: #64748b;
  font-size: 0.95rem;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s1-input-group:focus-within .s1-addon {
  color: #0F1E4D;
  border-color: #0F1E4D;
}
#gtms-dgps-wizard-page .s1-input {
  border: none !important;
  box-shadow: none !important;
  background: transparent !important;
  font-size: 0.88rem;
  color: #0f172a;
  padding: 6px 12px;
  width: 100%;
}
#gtms-dgps-wizard-page .s1-textarea {
  border: 1.5px solid #cbd5e1 !important;
  border-radius: 8px !important;
  font-size: 0.88rem;
  color: #0f172a;
  padding: 10px 14px;
  width: 100%;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s1-textarea:focus {
  border-color: #0F1E4D !important;
  box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12) !important;
  outline: none;
}

/* Preset Chips */
#gtms-dgps-wizard-page .dgps-chip-preset {
  font-size: 0.72rem;
  font-weight: 600;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #334155;
  border-radius: 5px;
  padding: 2px 7px;
  transition: all 0.15s ease;
}
#gtms-dgps-wizard-page .dgps-chip-preset:hover {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #ffffff;
}

/* Notice Cards */
#gtms-dgps-wizard-page .s1-notice-card {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-left: 4px solid #0F1E4D;
  border-radius: 10px;
  padding: 14px 18px;
}
#gtms-dgps-wizard-page .s1-notice-icon-box {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: #eff6ff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s1-notice-icon {
  color: #2563eb;
  font-size: 1.15rem;
}
#gtms-dgps-wizard-page .s1-notice-text {
  font-size: 0.8rem;
  color: #475569;
  line-height: 1.45;
}

/* DGPS Banner Card */
#gtms-dgps-wizard-page .dgps-banner-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
}
#gtms-dgps-wizard-page .dgps-banner-icon-badge {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: #e0e7ff;
  color: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .dgps-banner-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #0F1E4D;
  margin-bottom: 2px;
}
#gtms-dgps-wizard-page .dgps-banner-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.35;
}
#gtms-dgps-wizard-page .dgps-banner-status-pill {
  background: #0F1E4D;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  padding: 6px 14px;
  border-radius: 9999px;
  letter-spacing: 0.02em;
}

/* Technical Bento KPI Cards */
#gtms-dgps-wizard-page .dgps-kpi-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 18px;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
#gtms-dgps-wizard-page .dgps-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 14px rgba(15, 30, 77, 0.06);
}
#gtms-dgps-wizard-page .dgps-kpi-instrument {
  border-left: 4px solid #0284c7 !important;
}
#gtms-dgps-wizard-page .dgps-kpi-benchmark {
  border-left: 4px solid #10b981 !important;
}
#gtms-dgps-wizard-page .dgps-kpi-tolerance {
  border-left: 4px solid #f59e0b !important;
}
#gtms-dgps-wizard-page .dgps-kpi-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #64748b;
}
#gtms-dgps-wizard-page .dgps-kpi-val {
  font-size: 16px;
  font-weight: 800;
  color: #0F1E4D;
  margin-bottom: 4px;
}
#gtms-dgps-wizard-page .dgps-kpi-sub {
  font-size: 11.5px;
}
#gtms-dgps-wizard-page .dgps-kpi-icon-badge {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}
#gtms-dgps-wizard-page .dgps-kpi-icon-blue { background: #e0f2fe; color: #0284c7; }
#gtms-dgps-wizard-page .dgps-kpi-icon-emerald { background: #ecfdf5; color: #10b981; }
#gtms-dgps-wizard-page .dgps-kpi-icon-amber { background: #fffbeb; color: #d97706; }

/* Step 3: Document Checklist */
#gtms-dgps-wizard-page .s5-summary-banner {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
}
#gtms-dgps-wizard-page .s5-summary-badge {
  font-size: 0.72rem;
  font-weight: 700;
  background: #0F1E4D;
  color: #ffffff;
  padding: 3px 8px;
  border-radius: 6px;
}
#gtms-dgps-wizard-page .s5-summary-label {
  font-size: 0.85rem;
  color: #475569;
}
#gtms-dgps-wizard-page .s5-counter-text {
  font-size: 0.95rem;
  font-weight: 800;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .s5-counter-sub {
  font-size: 0.78rem;
}
#gtms-dgps-wizard-page .s5-summary-progress {
  height: 8px;
  border-radius: 999px;
  background: #e2e8f0;
  overflow: hidden;
}
#gtms-dgps-wizard-page .s5-summary-bar {
  background: linear-gradient(90deg, #2563eb, #10b981);
  transition: width 0.4s ease;
}
#gtms-dgps-wizard-page .s5-folder-heading-wrap {
  padding: 14px 20px;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
}
#gtms-dgps-wizard-page .s5-folder-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #eff6ff;
  color: #2563eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s5-folder-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .s5-folder-subtitle {
  font-size: 0.78rem;
  color: #64748b;
  display: block;
}
#gtms-dgps-wizard-page .s5-btn-add-doc {
  background: #ffffff;
  border: 1px solid #cbd5e1;
  color: #0F1E4D;
  font-weight: 600;
  font-size: 0.78rem;
  padding: 5px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s5-btn-add-doc:hover {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #ffffff;
}
#gtms-dgps-wizard-page .checklist-row {
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .checklist-row:hover {
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(15, 30, 77, 0.04);
}
#gtms-dgps-wizard-page .checklist-row.up {
  border-color: #86efac;
  background: #f0fdf4;
}
#gtms-dgps-wizard-page .ci-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #f1f5f9;
  color: #64748b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
#gtms-dgps-wizard-page .s5-btn-upload {
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  color: #1d4ed8;
  font-weight: 600;
  font-size: 0.76rem;
  padding: 4px 12px;
  border-radius: 6px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s5-btn-upload:hover {
  background: #1d4ed8;
  border-color: #1d4ed8;
  color: #ffffff;
}

/* Step 7 Payment Components (Lease Step 7 Parity) */
#gtms-dgps-wizard-page .s7-ledger-pill {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
  font-weight: 700;
  font-size: 12px;
  padding: 6px 14px;
  border-radius: 9999px;
  display: inline-flex;
  align-items: center;
}
#gtms-dgps-wizard-page .s7-banner-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
}
#gtms-dgps-wizard-page .s7-banner-icon-badge {
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
#gtms-dgps-wizard-page .s7-banner-title {
  font-size: 14.5px;
  font-weight: 700;
  color: #0F1E4D;
  margin-bottom: 2px;
}
#gtms-dgps-wizard-page .s7-banner-desc {
  font-size: 12px;
  color: #64748b;
  line-height: 1.35;
}
#gtms-dgps-wizard-page .s7-banner-status-pill {
  font-size: 12px;
  font-weight: 700;
  padding: 7px 16px;
  border-radius: 9999px;
  letter-spacing: 0.02em;
}
#gtms-dgps-wizard-page .s7-kpi-card {
  border-radius: 12px;
  padding: 18px 20px;
  border: 1px solid #e2e8f0;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
#gtms-dgps-wizard-page .s7-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.08);
}
#gtms-dgps-wizard-page .s7-kpi-contract {
  background: #f8fafc;
  border-left: 4px solid #0F1E4D !important;
}
#gtms-dgps-wizard-page .s7-kpi-paid {
  background: #f0fdf4;
  border-left: 4px solid #10b981 !important;
}
#gtms-dgps-wizard-page .s7-kpi-pending {
  background: #fff7ed;
  border-left: 4px solid #f97316 !important;
}
#gtms-dgps-wizard-page .s7-kpi-label {
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #64748b;
}
#gtms-dgps-wizard-page .s7-kpi-icon-badge {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
}
#gtms-dgps-wizard-page .s7-kpi-icon-contract { background: #e2e8f0; color: #0F1E4D; }
#gtms-dgps-wizard-page .s7-kpi-icon-paid { background: #dcfce7; color: #10b981; }
#gtms-dgps-wizard-page .s7-kpi-icon-pending { background: #ffedd5; color: #f97316; }
#gtms-dgps-wizard-page .s7-kpi-value {
  font-size: 22px;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 4px;
  font-variant-numeric: tabular-nums;
}
#gtms-dgps-wizard-page .s7-kpi-subtext {
  font-size: 11.5px;
  color: #64748b;
}
#gtms-dgps-wizard-page .s7-form-section {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
}
#gtms-dgps-wizard-page .s7-form-section-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 14px 20px;
}
#gtms-dgps-wizard-page .s7-form-section-title {
  font-size: 14px;
  font-weight: 700;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .s7-badge-status-tag {
  background: #e2e8f0;
  color: #475569;
  font-weight: 600;
  font-size: 11px;
  border-radius: 6px;
  padding: 4px 10px;
}
#gtms-dgps-wizard-page .s7-form-section-body {
  padding: 24px;
}
#gtms-dgps-wizard-page .s7-label {
  font-size: 13px;
  font-weight: 700;
  color: #0F1E4D;
}
#gtms-dgps-wizard-page .s7-auto-tag {
  font-size: 11px;
  font-weight: 600;
  color: #475569;
  background: #e2e8f0;
  padding: 3px 8px;
  border-radius: 4px;
}
#gtms-dgps-wizard-page .s7-input-group {
  display: flex;
  align-items: stretch;
  height: 44px;
  border: 1.5px solid #cbd5e1;
  border-radius: 9px;
  overflow: hidden;
  background: #ffffff;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s7-input-group:focus-within {
  border-color: #0F1E4D;
  box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
}
#gtms-dgps-wizard-page .s7-group-readonly {
  background: #fff1f2;
  border-color: #fecaca;
}
#gtms-dgps-wizard-page .s7-group-readonly:focus-within {
  border-color: #fca5a5;
  box-shadow: none;
}
#gtms-dgps-wizard-page .s7-input-addon {
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
#gtms-dgps-wizard-page .s7-addon-navy { color: #0F1E4D; background: #eff6ff; border-color: #bfdbfe; }
#gtms-dgps-wizard-page .s7-addon-success { color: #10b981; background: #f0fdf4; border-color: #bbf7d0; }
#gtms-dgps-wizard-page .s7-addon-danger { color: #dc2626; background: #fee2e2; border-color: #fecaca; }
#gtms-dgps-wizard-page .s7-input {
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
#gtms-dgps-wizard-page .s7-input:focus { outline: none !important; }
#gtms-dgps-wizard-page .s7-input-readonly { color: #dc2626 !important; cursor: default; }
#gtms-dgps-wizard-page .s7-field-hint { font-size: 11.5px; color: #64748b; line-height: 1.35; margin-top: 3px; display: block; }
#gtms-dgps-wizard-page .s7-preset-btn {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 6px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #334155;
  transition: all 0.15s ease;
}
#gtms-dgps-wizard-page .s7-preset-btn:hover { background: #0F1E4D; border-color: #0F1E4D; color: #ffffff; }
#gtms-dgps-wizard-page .s7-select,
#gtms-dgps-wizard-page .s7-notes-input {
  height: 44px;
  border: 1.5px solid #cbd5e1;
  border-radius: 9px;
  padding: 8px 14px;
  font-size: 13.5px;
  color: #0f172a;
  background-color: #ffffff;
}
#gtms-dgps-wizard-page .s7-feedback-box {
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 10px;
  padding: 12px 18px;
}
#gtms-dgps-wizard-page .s7-feedback-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s7-feedback-sub {
  color: #64748b;
  font-size: 10.5px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 700;
}
#gtms-dgps-wizard-page .s7-feedback-title {
  font-size: 13.5px;
  color: #0F1E4D;
  line-height: 1.25;
}
#gtms-dgps-wizard-page .s7-feedback-badge {
  font-size: 12px;
  font-weight: 700;
  padding: 6px 12px;
  border-radius: 20px;
}
#gtms-dgps-wizard-page .s7-tip-box {
  background: #fffbeb;
  border: 1px solid #fde68a;
  border-left: 4px solid #f59e0b;
  border-radius: 10px;
  padding: 14px 18px;
}
#gtms-dgps-wizard-page .s7-tip-icon { font-size: 20px; color: #d97706; flex-shrink: 0; }
#gtms-dgps-wizard-page .s7-tip-content { font-size: 12.5px; line-height: 1.5; color: #78350f; }

/* Step 8 Preview Components (Lease Step 8 Parity) */
#gtms-dgps-wizard-page .s8-scrutiny-pill {
  background: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
  font-weight: 700;
  font-size: 12px;
  padding: 6px 14px;
  border-radius: 9999px;
  display: inline-flex;
  align-items: center;
}
#gtms-dgps-wizard-page .s8-banner-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  padding: 16px 20px;
}
#gtms-dgps-wizard-page .s8-banner-icon-badge {
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
#gtms-dgps-wizard-page .s8-banner-title { font-size: 14.5px; font-weight: 700; color: #0F1E4D; margin-bottom: 2px; }
#gtms-dgps-wizard-page .s8-banner-desc { font-size: 12px; color: #64748b; line-height: 1.35; }
#gtms-dgps-wizard-page .s8-banner-status-pill {
  background: #0F1E4D;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  padding: 7px 16px;
  border-radius: 9999px;
}
#gtms-dgps-wizard-page .s8-kpi-card {
  border-radius: 12px;
  padding: 16px 18px;
  border: 1px solid #e2e8f0;
  background: #ffffff;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}
#gtms-dgps-wizard-page .s8-kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}
#gtms-dgps-wizard-page .s8-kpi-applicant { border-left: 4px solid #0284c7 !important; }
#gtms-dgps-wizard-page .s8-kpi-location { border-left: 4px solid #10b981 !important; }
#gtms-dgps-wizard-page .s8-kpi-docs { border-left: 4px solid #f59e0b !important; }
#gtms-dgps-wizard-page .s8-kpi-finance { border-left: 4px solid #9333ea !important; }
#gtms-dgps-wizard-page .s8-kpi-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; }
#gtms-dgps-wizard-page .s8-kpi-val { font-size: 17px; font-weight: 800; color: #0F1E4D; margin-bottom: 6px; line-height: 1.25; }
#gtms-dgps-wizard-page .s8-kpi-icon-badge {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
}
#gtms-dgps-wizard-page .s8-kpi-icon-blue { background: #e0f2fe; color: #0284c7; }
#gtms-dgps-wizard-page .s8-kpi-icon-emerald { background: #ecfdf5; color: #10b981; }
#gtms-dgps-wizard-page .s8-kpi-icon-amber { background: #fffbeb; color: #d97706; }
#gtms-dgps-wizard-page .s8-kpi-icon-purple { background: #f3e8ff; color: #9333ea; }
#gtms-dgps-wizard-page .s8-chip-blue { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; }
#gtms-dgps-wizard-page .s8-chip-emerald { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; }
#gtms-dgps-wizard-page .s8-chip-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; }
#gtms-dgps-wizard-page .s8-chip-purple { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 6px; }

/* Review Panels */
#gtms-dgps-wizard-page .s8-section-panel {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  overflow: hidden;
  transition: box-shadow 0.2s ease;
}
#gtms-dgps-wizard-page .s8-section-panel:hover {
  box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
}
#gtms-dgps-wizard-page .s8-panel-header {
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
  padding: 13px 20px;
}
#gtms-dgps-wizard-page .s8-panel-header-icon {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}
#gtms-dgps-wizard-page .s8-icon-blue { background: #e0f2fe; color: #0284c7; }
#gtms-dgps-wizard-page .s8-icon-emerald { background: #dcfce7; color: #16a34a; }
#gtms-dgps-wizard-page .s8-icon-amber { background: #fef3c7; color: #d97706; }
#gtms-dgps-wizard-page .s8-icon-teal { background: #ccfbf1; color: #0d9488; }
#gtms-dgps-wizard-page .s8-panel-title { font-size: 14px; font-weight: 700; color: #0F1E4D; }
#gtms-dgps-wizard-page .s8-panel-step-badge { background: #e2e8f0; color: #475569; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
#gtms-dgps-wizard-page .s8-panel-body { padding: 18px 20px; }
#gtms-dgps-wizard-page .s8-btn-edit {
  font-size: 12px;
  font-weight: 600;
  color: #475569;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  padding: 4px 12px;
  text-decoration: none;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .s8-btn-edit:hover {
  color: #0F1E4D;
  border-color: #0F1E4D;
  background: #f1f5f9;
}
#gtms-dgps-wizard-page .s8-data-tile {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 12px 14px;
  min-height: 68px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
#gtms-dgps-wizard-page .s8-tile-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 3px; }
#gtms-dgps-wizard-page .s8-tile-value { font-size: 13.5px; font-weight: 700; color: #0f172a; }

/* Table Styling */
#gtms-dgps-wizard-page .s8-table {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 13px;
  width: 100%;
}
#gtms-dgps-wizard-page .s8-table thead { background: #f8fafc; }
#gtms-dgps-wizard-page .s8-table th {
  font-size: 11.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #64748b;
  border-bottom: 1px solid #e2e8f0;
  padding: 10px 14px;
}
#gtms-dgps-wizard-page .s8-table td {
  padding: 12px 14px;
  vertical-align: middle;
  border-bottom: 1px solid #e2e8f0;
  color: #0f172a;
}
#gtms-dgps-wizard-page .s8-avatar-icon {
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
#gtms-dgps-wizard-page .s8-role-badge {
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #cbd5e1;
  font-size: 11.5px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 5px;
}
#gtms-dgps-wizard-page .s8-ledger-card {
  border: 1px solid #e2e8f0;
  background: #f8fafc;
  border-radius: 10px;
  padding: 14px 16px;
  text-align: center;
  min-height: 85px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
#gtms-dgps-wizard-page .s8-ledger-sub { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 2px; }
#gtms-dgps-wizard-page .s8-ledger-amount { font-size: 17px; font-weight: 800; margin-bottom: 2px; }
#gtms-dgps-wizard-page .s8-ledger-hint { font-size: 10.5px; }

/* Declaration Card */
#gtms-dgps-wizard-page .s8-declaration-card {
  background: #f0fdf4;
  border: 1.5px solid #86efac;
  border-radius: 12px;
  padding: 18px 22px;
}
#gtms-dgps-wizard-page .s8-declaration-icon-badge {
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
#gtms-dgps-wizard-page .s8-declaration-title { font-size: 14px; font-weight: 700; color: #14532d; }
#gtms-dgps-wizard-page .s8-declaration-text { font-size: 12.5px; color: #166534; line-height: 1.5; }
#gtms-dgps-wizard-page .s8-check-confirm .form-check-input {
  width: 18px;
  height: 18px;
  border: 2px solid #86efac;
  border-radius: 4px;
}
#gtms-dgps-wizard-page .s8-check-confirm .form-check-input:checked {
  background-color: #16a34a;
  border-color: #16a34a;
}
#gtms-dgps-wizard-page .s8-check-confirm .form-check-label {
  font-size: 13px;
  color: #14532d;
  cursor: pointer;
  padding-left: 4px;
}

/* Global Navigation Actions */
#gtms-dgps-wizard-page .btn-navy {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #ffffff;
  font-weight: 700;
  border-radius: 8px;
  padding: 8px 22px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .btn-navy:hover {
  background: #1e3a8a;
  border-color: #1e3a8a;
  color: #ffffff;
}
#gtms-dgps-wizard-page .btn-green {
  background: #10b981;
  border-color: #10b981;
  color: #ffffff;
  font-weight: 700;
  border-radius: 8px;
  padding: 8px 22px;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .btn-green:hover {
  background: #059669;
  border-color: #059669;
  color: #ffffff;
}
#gtms-dgps-wizard-page .btn-outline-navy {
  border: 1.5px solid #cbd5e1;
  color: #0F1E4D;
  background: #ffffff;
  font-weight: 600;
  border-radius: 8px;
  padding: 7px 18px;
  text-decoration: none;
  transition: all 0.2s ease;
}
#gtms-dgps-wizard-page .btn-outline-navy:hover {
  background: #f1f5f9;
  border-color: #0F1E4D;
  color: #0F1E4D;
}
.field-autofilled {
  background-color: #ecfdf5 !important;
  border-color: #10b981 !important;
  transition: background-color 0.4s ease, border-color 0.4s ease;
}

/* =========================================================================
   DARK THEME OVERRIDES (Strictly Scoped)
   ========================================================================= */
body[data-theme-version="dark"] #gtms-dgps-wizard-page {
  --dgps-border: #334155;
  --dgps-surface: #1e293b;
  --dgps-surface-subtle: #0f172a;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-stepper-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .wizard-card {
  background: #18191a !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-stepper-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-title {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-stepper-eyebrow,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-eyebrow {
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-step-count-pill {
  background: #0f172a !important;
  border-color: #334155 !important;
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .step-progress::before {
  background: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .sp-step .circ {
  background: #18191a !important;
  border-color: #3e4451 !important;
  color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .sp-step.active .circ {
  background: #2563eb !important;
  border-color: #2563eb !important;
  color: #ffffff !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .sp-step.active .sp-label {
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-header-block {
  border-bottom-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-time-tag {
  background: #25282a !important;
  border-color: #3b4252 !important;
  color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-status-pill {
  background: #0f172a !important;
  border-color: #334155 !important;
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-card {
  background: #141f36 !important;
  border-color: #1e3a8a !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-icon-badge {
  background: #1e293b !important;
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-label {
  color: #93c5fd !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-desc {
  color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-input-group {
  background: #0f172a !important;
  border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-addon {
  background: #1e293b !important;
  border-color: #334155 !important;
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-lookup-field {
  background: #0f172a !important;
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-section-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-section-panel {
  background: #18191a !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-section-header,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-panel-header,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s5-folder-heading-wrap,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-form-section-header {
  background: #1e2229 !important;
  border-bottom-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-section-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-panel-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s5-folder-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-form-section-title {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-label,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-label {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-input-group,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-input-group {
  background: #161717 !important;
  border-color: #3b4252 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-addon,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-input-addon {
  background: #202225 !important;
  border-color: #3b4252 !important;
  color: #94a3b8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-input,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-input,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-select,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-notes-input {
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-textarea {
  background: #161717 !important;
  border-color: #3b4252 !important;
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-banner-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-banner-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-banner-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s5-summary-banner {
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;
  border-color: #334155 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-banner-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-banner-title,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-banner-title {
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-kpi-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-kpi-card,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-kpi-card {
  background: #18191a !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .dgps-kpi-val,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-kpi-value,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-kpi-val {
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-kpi-contract .text-navy {
  color: #38bdf8 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-data-tile,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-ledger-card {
  background: #161717 !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-tile-value {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-table {
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-table thead {
  background: #1e2229 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-table th,
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-table td {
  border-color: #2b2d30 !important;
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .checklist-row {
  background: #18191a !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .checklist-row:hover {
  background: #202225 !important;
  border-color: #3e4451 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .ci-name {
  color: #f8fafc !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s1-notice-card {
  background: #161a24 !important;
  border-color: #223761 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-form-section {
  background: #18191a !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-group-readonly {
  background: #2a1215 !important;
  border-color: #5c1d24 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-input-readonly {
  color: #f87171 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-addon-navy {
  color: #38bdf8 !important;
  background: #1e293b !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-addon-success {
  color: #34d399 !important;
  background: rgba(16, 185, 129, 0.15) !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-addon-danger {
  color: #f87171 !important;
  background: rgba(239, 68, 68, 0.15) !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-preset-btn {
  background: #202225 !important;
  border-color: #3b4252 !important;
  color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-feedback-box {
  background: #161717 !important;
  border-color: #2b2d30 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s7-feedback-title {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-declaration-card {
  background: rgba(16, 185, 129, 0.08) !important;
  border-color: rgba(16, 185, 129, 0.3) !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-declaration-title {
  color: #34d399 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-declaration-text {
  color: #a7f3d0 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .s8-check-confirm .form-check-label {
  color: #f1f5f9 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .btn-outline-navy {
  background: #18191a !important;
  border-color: #3e4451 !important;
  color: #cbd5e1 !important;
}
body[data-theme-version="dark"] #gtms-dgps-wizard-page .btn-outline-navy:hover {
  background: #25282a !important;
  color: #ffffff !important;
}
</style>
@endpush
@endsection
