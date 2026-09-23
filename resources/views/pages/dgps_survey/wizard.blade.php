@extends('layouts.app')
@section('title', 'DGPS Survey Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$steps = [
  ['Survey Request & Planning', ['Initial request registered', 'Satellite planning done', 'Client notified of schedule']],
  ['Field Survey & Data Collection', ['Base station established', 'Boundary pillars surveyed', 'Control points checked']],
  ['Process Field Points & Upload Data', ['Raw RINEX & coordinate logs', 'Cadastral map superimposition', 'Boundary polygon KML/KMZ']],
  ['Survey Report Preparation', ['Area extent computed', 'Pillar coordinate table verified', 'Field surveyor certificate signed']],
  ['GTM Portal Upload & Sync', ['DGPS report submitted to Mining Dept', 'GTM receipt generated', 'Boundary layers synced']],
  ['Handling Team Allocation', ['Chief Land Surveyor assigned', 'Instrument Operators designated', 'GIS Mapping Engineers appointed']],
  ['Payment Details & Billing Ledger', ['Service fee recorded', 'Advance received verified', 'Settlement ledger balanced']],
  ['Preview & Final Confirmation', ['Complete survey dossier checked', 'Coordinates audited', 'Ready to finalize']]
];
$labels = ['Request','Field Survey','Process & Upload','Report','GTM Upload','Handling Team','Payment','Preview'];
@endphp

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap" style="max-width:960px;">
      {{-- Stepper Progress Bar --}}
      <div class="step-progress">
        @foreach($labels as $number => $label)
          <div class="sp-step {{ $number + 1 < $step ? 'done' : ($number + 1 === $step ? 'active' : '') }}">
            <div class="circ">
              @if($number + 1 < $step)
                <i class="bi bi-check-lg"></i>
              @else
                {{ $number + 1 }}
              @endif
            </div>
            <div class="sp-label">{{ $label }}</div>
          </div>
        @endforeach
      </div>

      <div class="wizard-card">
        <div class="wc-eyebrow">DGPS Department &mdash; Survey &middot; Sub Process {{ $step }} of 8</div>
        <h4>{{ $steps[$step - 1][0] }}</h4>
        <div class="wc-sub">Complete the listed DGPS survey activities before continuing to the next stage.</div>

        <form method="POST" action="{{ $step === 8 ? route('dgps-survey.store') : route('dgps-survey.saveStep', $step) }}" id="dgpsWizardForm">
          @csrf

          {{-- STEP 1: SURVEY REQUEST --}}
          @if($step === 1)
            {{-- CUSTOMER UNIQUE ID LOOKUP CARD --}}
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

              <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Survey Request No. *</label>
                <input class="form-control font-monospace fw-bold bg-light" name="survey_no" readonly
                  value="{{ $draft['survey_no'] ?? ('DGPS-' . date('Y') . '-' . sprintf('%04d', \App\Models\DgpsSurvey::count() + 1)) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Client / Applicant *</label>
                <select name="customer_id" id="select_dgps_customer" class="form-select auto-filled-field" required>
                  <option value="">-- Choose Client / Entity --</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                      data-company="{{ $c->company_name }}"
                      data-mobile="{{ $c->mobile_num }}"
                      data-mimas="{{ $c->mimas_no }}"
                      {{ ($draft['customer_id'] ?? '') == $c->id ? 'selected' : ($loop->first && empty($draft['customer_id']) ? 'selected' : '') }}>
                      {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Associated Lease Application (Optional)</label>
                <select name="lease_application_id" id="select_dgps_lease" class="form-select auto-filled-field">
                  <option value="">-- Standalone Survey / None --</option>
                  @foreach($leaseApps as $lease)
                    <option value="{{ $lease->id }}" data-customer="{{ $lease->customer_id }}" {{ ($draft['lease_application_id'] ?? '') == $lease->id ? 'selected' : '' }}>
                      Lease #{{ $lease->application_no }} ({{ $lease->area_extent_acres }} acres)
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Lease Area (Ha) *</label>
                <input type="number" step="0.01" class="form-control auto-filled-field" name="lease_area_ha" id="field_dgps_area" placeholder="Area in hectares"
                  value="{{ $draft['lease_area_ha'] ?? '3.85' }}" required>
              </div>
              <div class="col-md-12">
                <label class="form-label fw-bold text-navy">Location / Village / Taluk *</label>
                <input class="form-control auto-filled-field" name="location" id="field_dgps_location" placeholder="e.g. Semmandapatti Village, Omalur Taluk, Salem District"
                  value="{{ $draft['location'] ?? 'Salem / Semmandapatti' }}" required>
              </div>
            </div>

          {{-- STEP 2: FIELD SURVEY --}}
          @elseif($step === 2)
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Survey Execution Date *</label>
                <input class="form-control" type="date" name="survey_date" value="{{ $draft['survey_date'] ?? date('Y-m-d') }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Survey Team Notes / Base Station Setup</label>
                <input class="form-control" name="survey_team_notes" placeholder="Notes on benchmark calibration"
                  value="{{ $draft['survey_team_notes'] ?? 'Trimble R12i GNSS base established at SOI benchmark BM-88' }}">
              </div>
            </div>

          {{-- STEP 3: DATA PROCESSING & DOCUMENTS UPLOAD --}}
          @elseif($step === 3)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="mb-1">Process Field Points &amp; Upload Data</h4>
                <div class="wc-sub mb-0">Prepare coordinates, area calculations, raw GPS data, and survey map deliverables.</div>
              </div>
              <div class="text-end">
                <span class="text-muted small fw-semibold">OVERALL DOCUMENT COMPLETION:</span>
                <span class="fw-bold text-navy ms-1" id="dgps_doc_counter">0 / 6 uploaded</span>
                <div class="progress mt-1" style="height: 6px; width: 160px;">
                  <div class="progress-bar bg-success" id="dgps_progress_bar" role="progressbar" style="width: 0%;"></div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
              <div style="font-size:.75rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
                <i class="fa fa-folder-open me-1 text-primary"></i> 1. DGPS Survey Field &amp; Boundary Data
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btn_add_dgps_doc" style="font-size:0.75rem;">
                <i class="bi bi-plus-circle me-1"></i>Add Document
              </button>
            </div>

            @php
              $dgpsDocs = [
                ['name' => '1. Raw RINEX Base & Rover Observation Data', 'meta' => 'RINEX, DAT, or raw log files from GPS receiver', 'mandatory' => true],
                ['name' => '2. Benchmark Fixation & Control Network Sheet', 'meta' => 'SOI Benchmark reference and calibration certificate', 'mandatory' => true],
                ['name' => '3. Boundary Pillar Coordinates (Lat/Long & UTM)', 'meta' => 'Tabulated boundary pillar coordinates (CSV / Excel)', 'mandatory' => true],
                ['name' => '4. Cadastral Survey / FMB Map Superimposition', 'meta' => 'Revenue FMB / A-Register boundary overlay CAD sketch', 'mandatory' => true],
                ['name' => '5. Boundary Polygon KML / KMZ File', 'meta' => 'Google Earth 3D boundary polygon and pillar markers', 'mandatory' => true],
                ['name' => '6. DGPS Field Traverse & Calibration Log', 'meta' => 'Equipment accuracy log and field verification checklist', 'mandatory' => false],
              ];
            @endphp

            <div class="folder-checklist-box" id="dgps_checklist_container">
              @foreach($dgpsDocs as $dIdx => $dItem)
                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom" data-dgps-row="{{ $dIdx }}">
                  <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px; height:34px; background:#f1f5f9; color:#64748b;">
                      <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-semibold small text-dark text-truncate">{{ $dItem['name'] }}</div>
                      <div class="ci-meta text-muted" style="font-size:11px;">{{ $dItem['meta'] }} &middot; Not uploaded</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status {{ $dItem['mandatory'] ? 'mandatory' : 'pending' }}">{{ $dItem['mandatory'] ? 'Mandatory' : 'Optional' }}</span>
                    <input type="file" class="d-none dgps-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.csv,.xlsx,.kml,.kmz,.dwg,.txt,.dat">
                    <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-dgps-item" style="font-size:.72rem;">
                      <i class="bi bi-upload me-1"></i>Upload
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 4: SURVEY REPORT --}}
          @elseif($step === 4)
            <div class="card-panel" style="min-height:190px;background:#f8fafc">
              <div class="text-center pt-3">
                <i class="fa fa-map" style="font-size:3.5rem;color:var(--navy)"></i>
                <h5 class="mt-3 fw-bold text-navy">DGPS Survey Report &amp; Boundary Demarcation</h5>
                <p class="text-muted small">Official field survey certificate, boundary coordinates sheet, and statutory map sketch.</p>
                <div class="d-flex justify-content-center gap-2">
                  <a href="about:blank" target="_blank" rel="noopener noreferrer" class="btn btn-outline-navy btn-sm">
                    <i class="bi bi-eye me-1"></i> View Report Preview
                  </a>
                </div>
              </div>
            </div>

          {{-- STEP 5: GTM UPLOAD --}}
          @elseif($step === 5)
            <div class="card-panel" style="background:var(--green-soft);border:none">
              <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Output: DGPS Survey Completed and coordinates mapped for GTM Departmental portal.</span>
            </div>

          {{-- STEP 6: PROJECT HANDLING TEAM --}}
          @elseif($step === 6)
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div>
                <h5 class="fw-bold mb-1" style="color:#0F1E4D;">DGPS Survey Team &amp; In-Charge Persons</h5>
                <div class="text-muted small">Assign field surveyors, instrument operators, and GIS mapping personnel.</div>
              </div>
              <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_dgps_handler" style="background:#0F1E4D; color:#fff;">
                <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
              </button>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle" id="dgps_handlers_table">
                <thead class="bg-light text-navy" style="font-size:0.85rem;">
                  <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 30%;">Person Name <span class="text-danger">*</span></th>
                    <th style="width: 30%;">Role / Designation <span class="text-danger">*</span></th>
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
                      <td><input type="text" name="handlers[{{ $hIdx }}][person_name]" class="form-control form-control-sm" value="{{ $h['person_name'] ?? ($h['name'] ?? '') }}" placeholder="e.g. Er. A. Vijayakumar" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm" value="{{ $h['role'] ?? '' }}" placeholder="e.g. Lead Surveyor" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm" value="{{ $h['notes'] ?? '' }}" placeholder="Responsibilities"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-dgps-handler {{ count($savedDgpsHandlers) === 1 ? 'disabled' : '' }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="alert alert-info py-2 px-3 small rounded-2 mb-0" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
              <i class="bi bi-info-circle me-1"></i> You can type custom roles like <em>Chief Surveyor, Instrument Operator, GIS Expert, Field Inspector</em>.
            </div>

          {{-- STEP 7: PAYMENT DETAILS & BILLING LEDGER --}}
          @elseif($step === 7)
            <h5 class="fw-bold mb-1" style="color:#0F1E4D;">Survey Fee &amp; Payment Ledger</h5>
            <div class="wc-sub">Track DGPS field survey service fee, advance collected, and balance settlement.</div>

            @php
              $val     = (float)($draft['product_value'] ?? 35000);
              $paid    = (float)($draft['paid_amount'] ?? 35000);
              $pending = max(0, $val - $paid);
              $status  = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
                  <div class="text-muted small fw-semibold text-uppercase">Survey Service Value</div>
                  <div class="h4 fw-bold mb-0 text-navy mt-1" id="disp_dgps_val">₹ {{ number_format($val, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Agreed survey contract value</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
                  <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
                  <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_dgps_paid">₹ {{ number_format($paid, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Full payment settled</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
                  <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
                  <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_dgps_pending">₹ {{ number_format($pending, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Auto-calculated outstanding</small>
                </div>
              </div>
            </div>

            <div class="card p-3 bg-light border-0 rounded-3 mb-4">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Product / Fee Value (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white fw-bold">₹</span>
                    <input type="number" step="0.01" min="0" name="product_value" id="field_dgps_val" class="form-control fw-bold" placeholder="0.00" value="{{ $val }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Paid Amount (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white fw-bold text-success">₹</span>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="field_dgps_paid" class="form-control fw-bold text-success" placeholder="0.00" value="{{ $paid }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Pending Balance (₹)</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light text-muted">₹</span>
                    <input type="number" step="0.01" name="pending_amount" id="field_dgps_pending" class="form-control bg-light fw-bold text-success" placeholder="0.00" readonly value="{{ number_format($pending, 2, '.', '') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Settlement Status *</label>
                  <select class="form-select" name="payment_status" id="field_dgps_status">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
                    <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial Payment Received</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Transaction Reference / Receipt Notes</label>
                  <input type="text" class="form-control" name="payment_notes" placeholder="e.g. Bank transfer ref / UPI" value="{{ $draft['payment_notes'] ?? 'UPI Ref #DGPS-66120 - Full payment received upon field completion' }}">
                </div>
              </div>
            </div>

          {{-- STEP 8: PREVIEW & FINAL CONFIRMATION --}}
          @elseif($step === 8)
            <h4>DGPS Survey Verification &amp; Final Summary</h4>
            <div class="wc-sub">Review survey findings, technical survey crew, and financial ledger status.</div>

            @php
              $customerObj = !empty($draft['customer_id']) ? \App\Models\Customer::find($draft['customer_id']) : $customers->first();
              $val         = (float)($draft['product_value'] ?? 35000);
              $paid        = (float)($draft['paid_amount'] ?? 35000);
              $pending     = max(0, $val - $paid);
              $pStatus     = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="card-panel mt-3 mb-3" style="background:var(--navy-soft); border:none;">
              <h6 style="color:#0F1E4D; font-weight:600; margin-bottom: 12px;"><i class="bi bi-card-checklist me-2"></i>DGPS Survey Summary</h6>
              <div class="row g-2" style="font-size: 0.85rem;">
                <div class="col-md-6"><span class="text-muted">Client / Applicant:</span> <br><b>{{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}</b></div>
                <div class="col-md-6"><span class="text-muted">Survey Request No:</span> <br><b>{{ $draft['survey_no'] ?? 'DGPS-2026-0031' }}</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Lease Area:</span> <br><b>{{ $draft['lease_area_ha'] ?? '3.85' }} Hectares</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Status:</span> <br><span class="badge bg-success">Field Survey Completed &amp; Benchmarked</span></div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              {{-- Team Preview --}}
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white h-100">
                  <h6 class="fw-bold text-navy mb-2"><i class="bi bi-people-fill text-primary me-1"></i> Field Survey Team</h6>
                  @php
                    $previewHandlers = $draft['handlers'] ?? [
                      ['name' => 'Er. A. Vijayakumar', 'role' => 'Chief Land Surveyor'],
                      ['name' => 'K. Prakash', 'role' => 'GIS / CAD Engineer']
                    ];
                  @endphp
                  @foreach($previewHandlers as $h)
                    <div class="d-flex justify-content-between py-1 {{ !$loop->last ? 'border-bottom' : '' }} small">
                      <span><strong>{{ $h['person_name'] ?? ($h['name'] ?? 'Surveyor') }}</strong></span>
                      <span class="badge bg-light text-dark border">{{ $h['role'] ?? 'Survey Staff' }}</span>
                    </div>
                  @endforeach
                </div>
              </div>

              {{-- Financial Preview --}}
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white h-100">
                  <h6 class="fw-bold text-navy mb-2"><i class="bi bi-receipt text-success me-1"></i> Financial Summary</h6>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Survey Quotation:</span>
                    <strong>₹ {{ number_format($val, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Paid Amount:</span>
                    <strong class="text-success">₹ {{ number_format($paid, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Pending Balance:</span>
                    <strong class="text-success">₹ {{ number_format($pending, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between pt-2 small">
                    <span class="text-muted">Settlement Status:</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($pStatus) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-panel mb-0" style="background:var(--green-soft);border:none">
              <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Please confirm to finalize the DGPS Survey process and synchronize with customer dossier.</span>
            </div>
          @endif

          {{-- WIZARD NAVIGATION ACTIONS --}}
          <div class="wizard-actions mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="{{ $step === 1 ? route('dgps-survey.index') : route('dgps-survey.step', $step - 1) }}" class="btn btn-outline-navy btn-sm">
              <i class="bi bi-arrow-left"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
            </a>
            @can('dgps.create')
              <button type="submit" class="btn {{ $step === 8 ? 'btn-green' : 'btn-navy' }} px-4">
                {{ $step === 8 ? 'Finish & Save Survey' : 'Save & Continue' }} <i class="bi {{ $step === 8 ? 'bi-check2-circle' : 'bi-arrow-right' }}"></i>
              </button>
            @endcan
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- JAVASCRIPT FOR DYNAMIC HANDLERS & REAL-TIME PAYMENT MATH --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
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
          metaDiv.innerHTML = `<span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ${file.name}</span> &middot; ${sizeKb} KB`;
        }

        const slot = row.querySelector('.action-slot');
        if (slot) {
          const statusBadge = slot.querySelector('.badge-status');
          if (statusBadge) {
            statusBadge.className = 'badge-status uploaded';
            statusBadge.textContent = 'Uploaded';
          }

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

  // DGPS Step 3: Add Document button
  const btnAddDgpsDoc = document.getElementById('btn_add_dgps_doc');
  if (btnAddDgpsDoc) {
    btnAddDgpsDoc.addEventListener('click', function() {
      const docName = prompt('Enter DGPS Survey Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('dgps_checklist_container');
      const count = container.querySelectorAll('.checklist-row').length + 1;
      const row = document.createElement('div');
      row.className = 'checklist-row py-2 d-flex align-items-center justify-content-between border-bottom';
      row.innerHTML = `
        <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
          <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px; height:34px; background:#f1f5f9; color:#64748b;">
            <i class="bi bi-file-earmark-plus"></i>
          </div>
          <div class="text-truncate">
            <div class="ci-name fw-semibold small text-dark text-truncate">
              <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
              ${count}. ${docName.trim()}
            </div>
            <div class="ci-meta text-muted" style="font-size:11px;">Custom survey attachment &middot; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <span class="badge-status pending">Optional</span>
          <input type="file" class="d-none dgps-file-input" accept=".pdf,.csv,.xlsx,.kml,.kmz,.dwg,.txt,.dat">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-dgps-item" style="font-size:.72rem;">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updateDgpsDocProgress();
    });
  }

  // Handlers dynamic add/remove
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
        <td><input type="text" name="handlers[${count}][person_name]" class="form-control form-control-sm" placeholder="e.g. S. Suresh" required></td>
        <td><input type="text" name="handlers[${count}][role]" class="form-control form-control-sm" placeholder="e.g. GPS Instrument Operator" required></td>
        <td><input type="text" name="handlers[${count}][notes]" class="form-control form-control-sm" placeholder="e.g. Base and rover field observations"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-dgps-handler">
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

  // Payment live calculation
  const fVal = document.getElementById('field_dgps_val');
  const fPaid = document.getElementById('field_dgps_paid');
  const fPending = document.getElementById('field_dgps_pending');
  const fStatus = document.getElementById('field_dgps_status');
  const dVal = document.getElementById('disp_dgps_val');
  const dPaid = document.getElementById('disp_dgps_paid');
  const dPending = document.getElementById('disp_dgps_pending');

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
.field-autofilled {
  background-color: #ecfdf5 !important;
  border-color: #10b981 !important;
  transition: background-color 0.4s ease, border-color 0.4s ease;
}
</style>
@endpush
@endsection
