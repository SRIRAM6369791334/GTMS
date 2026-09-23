@extends('layouts.app')
@section('title', 'PPT Department Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$labels = ['Client','District','Minerals','Folders','Documents','Approval','Handling Team','Payment','Preview'];
$folders = [
  ['Documents','PARIVESH online uploading documents','fa-file-alt'],
  ['EDS & EDS Reply','EDS and EDS reply','fa-comments'],
  ['Demand Note','SPCB payment receipt and comments','fa-file-invoice-dollar'],
  ['File No','Schedule, PPT, Radius KML, NOC and CER','fa-folder-open'],
  ['SEAC Agenda','SEAC agenda','fa-users'],
  ['SEAC Minutes','ADS and ADS reply if any','fa-clipboard-list'],
  ['ADS','ADS reply documents and PPT','fa-file-alt'],
  ['CER Affidavit','CER affidavit','fa-balance-scale'],
  ['SEIAA Agenda','SEIAA agenda','fa-users'],
  ['SEIAA Minutes','ADS and ADS reply if any','fa-clipboard-check'],
  ['Environmental Clearance','Environmental clearance','fa-leaf']
];
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
        <div class="wc-eyebrow">Step {{ $step }} of 9 &middot; PPT Department Statutory Presentation Domain</div>

        <form method="POST" action="{{ $step === 9 ? route('ppt-department.store') : route('ppt-department.saveStep', $step) }}" id="pptWizardForm">
          @csrf

          {{-- STEP 1: CLIENT NAME --}}
          @if($step === 1)
            <h4>Client &amp; Presentation Project</h4>
            <div class="wc-sub">Start the PPT Department presentation process by selecting the registered entity.</div>

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
                <label class="form-label fw-bold text-navy">Select Registered Client *</label>
                <select name="customer_id" id="select_ppt_customer" class="form-select auto-filled-field" required>
                  <option value="">-- Choose Client / Entity --</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                      data-company="{{ $c->company_name }}"
                      data-contact="{{ $c->contact_name }}"
                      data-mobile="{{ $c->mobile_num }}"
                      data-mimas="{{ $c->mimas_no }}"
                      {{ ($draft['customer_id'] ?? '') == $c->id ? 'selected' : ($loop->first && empty($draft['customer_id']) ? 'selected' : '') }}>
                      {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Application No. *</label>
                <input class="form-control font-monospace fw-bold bg-light" name="application_no" readonly
                  value="{{ $draft['application_no'] ?? ('PPT-' . date('Y') . '-' . sprintf('%04d', \App\Models\PptApplication::count() + 1)) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Project / Quarry Name *</label>
                <input class="form-control auto-filled-field" name="project_name" id="field_ppt_project_name" placeholder="Project name"
                  value="{{ $draft['project_name'] ?? 'Kaveri Rough Stone & Gravel Quarry' }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Representative Mobile Number</label>
                <input class="form-control auto-filled-field" id="field_ppt_mobile" placeholder="Mobile number"
                  value="{{ $customers->first()?->mobile_num ?? '9842109876' }}" readonly>
              </div>
            </div>

          {{-- STEP 2: DISTRICT WISE DETAILS --}}
          @elseif($step === 2)
            <h4>District Wise Details</h4>
            <div class="wc-sub">Select the district and location where the mining concession is situated.</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">District *</label>
                <select name="district_id" class="form-select" required>
                  <option value="">Select district</option>
                  @foreach($districts as $dist)
                    <option value="{{ $dist->id }}" {{ ($draft['district_id'] ?? 1) == $dist->id ? 'selected' : '' }}>
                      {{ $dist->name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Taluk / Village</label>
                <input class="form-control" name="taluk_village" placeholder="Taluk or village"
                  value="{{ $draft['taluk_village'] ?? 'Omalur / Semmandapatti' }}">
              </div>
            </div>

          {{-- STEP 3: MINERAL DETAILS --}}
          @elseif($step === 3)
            <h4>Mineral Details</h4>
            <div class="wc-sub">Select the primary mineral concession category for this presentation dossier.</div>
            <div class="row g-2">
              @php
                $selMin = $draft['mineral_id'] ?? ($minerals->first()?->id ?? 1);
              @endphp
              @foreach($minerals as $min)
                <div class="col-md-6">
                  <label class="opt-tile w-100 mb-0 d-block cursor-pointer {{ $selMin == $min->id ? 'selected' : '' }}" style="cursor:pointer;">
                    <div class="d-flex align-items-center">
                      <input type="radio" name="mineral_id" value="{{ $min->id }}" {{ $selMin == $min->id ? 'checked' : '' }} class="me-2" style="accent-color:#0F1E4D;">
                      <div>
                        <div class="opt-title fw-bold text-navy">{{ $min->name }}</div>
                        <div class="opt-desc text-muted small">Statutory concession mineral</div>
                      </div>
                    </div>
                  </label>
                </div>
              @endforeach
            </div>

          {{-- STEP 4: PPT DEPARTMENT FOLDERS --}}
          @elseif($step === 4)
            <h4>PPT Department Folders Overview</h4>
            <div class="wc-sub">The process is organized into eleven statutory presentation folders before committee presentation.</div>
            <div class="row g-3">
              @foreach($folders as [$name, $detail, $icon])
                <div class="col-md-4">
                  <div class="folder-tile">
                    <div class="fico"><i class="fa {{ $icon }}"></i></div>
                    <div class="ftitle">{{ $loop->iteration }}. {{ $name }}</div>
                    <div class="fmeta">{{ $detail }}</div>
                    <span class="badge-status pending">Configured</span>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 5: UPLOAD & VALIDATE DATA --}}
          @elseif($step === 5)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="mb-1">Upload &amp; Validate Data</h4>
                <div class="wc-sub mb-0">Prepare presentation files and validate statutory items with separate black page view preview.</div>
              </div>
              <div class="text-end">
                <span class="text-muted small fw-semibold">OVERALL DOCUMENT COMPLETION:</span>
                <span class="fw-bold text-navy ms-1" id="ppt_doc_counter">0 / {{ count($folders) }} uploaded</span>
                <div class="progress mt-1" style="height: 6px; width: 160px;">
                  <div class="progress-bar bg-success" id="ppt_progress_bar" role="progressbar" style="width: 0%;"></div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
              <div style="font-size:.75rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
                <i class="fa fa-folder-open me-1 text-primary"></i> 1. PPT Statutory &amp; Presentation Folders
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btn_add_ppt_doc" style="font-size:0.75rem;">
                <i class="bi bi-plus-circle me-1"></i>Add Document
              </button>
            </div>

            <div class="folder-checklist-box" id="ppt_checklist_container">
              @foreach($folders as $fIdx => [$name, $detail])
                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom" data-ppt-row="{{ $fIdx }}">
                  <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px; height:34px; background:#f1f5f9; color:#64748b;">
                      <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-semibold small text-dark text-truncate">{{ $loop->iteration }}. {{ $name }}</div>
                      <div class="ci-meta text-muted" style="font-size:11px;">{{ $detail }} &middot; Not uploaded</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status {{ $fIdx < 3 ? 'mandatory' : 'pending' }}">{{ $fIdx < 3 ? 'Mandatory' : 'Optional' }}</span>
                    <input type="file" class="d-none ppt-file-input" data-row-idx="{{ $fIdx }}" accept=".pdf,.ppt,.pptx,.kml,.kmz,.jpg,.png">
                    <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-ppt-item" style="font-size:.72rem;">
                      <i class="bi bi-upload me-1"></i>Upload
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 6: REVIEW, APPROVE & GENERATE REPORTS --}}
          @elseif($step === 6)
            <h4>Review, Approve &amp; Generate Reports</h4>
            <div class="wc-sub">Review the complete PPT presentation set, approve it, and prepare the committee dossier.</div>
            <div class="row g-3">
              <div class="col-md-4">
                <div class="folder-tile">
                  <div class="fico"><i class="fa fa-check"></i></div>
                  <div class="ftitle">Review &amp; Approve</div>
                  <div class="fmeta">Approve verified presentation files</div>
                  <span class="badge-status verified">Ready</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="folder-tile">
                  <div class="fico"><i class="fa fa-chart-bar"></i></div>
                  <div class="ftitle">Generate Reports</div>
                  <div class="fmeta">SEAC/SEIAA appraisal docket</div>
                  <span class="badge-status verified">Completed</span>
                </div>
              </div>
              <div class="col-md-4">
                <div class="folder-tile">
                  <div class="fico"><i class="fa fa-archive"></i></div>
                  <div class="ftitle">Archive &amp; Backup</div>
                  <div class="fmeta">Permanent project repository</div>
                  <span class="badge-status pending">Pending</span>
                </div>
              </div>
            </div>
            <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none">
              <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">PPT Department statutory review stage cleared. Proceed to Handling Team allocation.</span>
            </div>

          {{-- STEP 7: PROJECT HANDLING TEAM --}}
          @elseif($step === 7)
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div>
                <h4 class="mb-1" style="color:#0F1E4D;">Project Handling Team</h4>
                <div class="wc-sub mb-0">Designate the technical team, presentation speakers, and SEAC/SEIAA meeting attendees.</div>
              </div>
              <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_ppt_handler" style="background:#0F1E4D; color:#fff;">
                <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
              </button>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle" id="ppt_handlers_table">
                <thead class="bg-light text-navy" style="font-size:0.85rem;">
                  <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 30%;">Person Name <span class="text-danger">*</span></th>
                    <th style="width: 30%;">Role / Designation <span class="text-danger">*</span></th>
                    <th>Notes &amp; Responsibilities</th>
                    <th style="width: 70px;" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="ppt_handlers_tbody">
                  @php
                    $savedHandlers = $draft['handlers'] ?? [
                      ['name' => 'Er. M. Senthil Kumar', 'role' => 'RQP / EIA Technical Lead', 'notes' => 'SEAC Appraisal meeting presentation & query replies'],
                      ['name' => 'R. Murugan', 'role' => 'Client Representative', 'notes' => 'Authorized quarry signatory attending SEIAA hearing']
                    ];
                  @endphp
                  @foreach($savedHandlers as $hIdx => $h)
                    <tr>
                      <td class="text-center fw-bold row-num">{{ $hIdx + 1 }}</td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][person_name]" class="form-control form-control-sm" value="{{ $h['person_name'] ?? ($h['name'] ?? '') }}" placeholder="e.g. Er. M. Senthil Kumar" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm" value="{{ $h['role'] ?? '' }}" placeholder="e.g. Presentation Speaker" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm" value="{{ $h['notes'] ?? '' }}" placeholder="Responsibilities"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ppt-handler {{ count($savedHandlers) === 1 ? 'disabled' : '' }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="alert alert-info py-2 px-3 small rounded-2 mb-0" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
              <i class="bi bi-info-circle me-1"></i> Roles can be freely typed: <em>RQP Consultant, Technical Presenter, Environmental Coordinator, Legal Advisor</em>.
            </div>

          {{-- STEP 8: PAYMENT DETAILS & BILLING LEDGER --}}
          @elseif($step === 8)
            <h4 class="mb-1" style="color:#0F1E4D;">Payment Details &amp; Billing Ledger</h4>
            <div class="wc-sub">Record the presentation preparation fee, SPCB demand note expenses, and client settlements.</div>

            @php
              $val     = (float)($draft['product_value'] ?? 45000);
              $paid    = (float)($draft['paid_amount'] ?? 25000);
              $pending = max(0, $val - $paid);
              $status  = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
                  <div class="text-muted small fw-semibold text-uppercase">Service / Product Value</div>
                  <div class="h4 fw-bold mb-0 text-navy mt-1" id="disp_ppt_val">₹ {{ number_format($val, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Agreed total quotation</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
                  <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
                  <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_ppt_paid">₹ {{ number_format($paid, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Advance received</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
                  <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
                  <div class="h4 fw-bold mb-0 text-danger mt-1" id="disp_ppt_pending">₹ {{ number_format($pending, 2) }}</div>
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
                    <input type="number" step="0.01" min="0" name="product_value" id="field_ppt_val" class="form-control fw-bold" placeholder="0.00" value="{{ $val }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Paid Amount (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white fw-bold text-success">₹</span>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="field_ppt_paid" class="form-control fw-bold text-success" placeholder="0.00" value="{{ $paid }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Pending Balance (₹)</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light text-muted">₹</span>
                    <input type="number" step="0.01" name="pending_amount" id="field_ppt_pending" class="form-control bg-light fw-bold text-danger" placeholder="0.00" readonly value="{{ number_format($pending, 2, '.', '') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Settlement Status *</label>
                  <select class="form-select" name="payment_status" id="field_ppt_status">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
                    <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial Payment Received</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Transaction Reference / Receipt Notes</label>
                  <input type="text" class="form-control" name="payment_notes" placeholder="e.g. SPCB Demand Note receipt #SPCB-2026-4412" value="{{ $draft['payment_notes'] ?? 'Advance received for presentation dossier' }}">
                </div>
              </div>
            </div>

          {{-- STEP 9: DATA PREVIEW & FINAL CONFIRMATION --}}
          @elseif($step === 9)
            <h4>Data Preview &amp; Verification</h4>
            <div class="wc-sub">Review PPT Department data, allocated technical handlers, and billing status before final confirmation.</div>

            @php
              $customerObj = !empty($draft['customer_id']) ? \App\Models\Customer::find($draft['customer_id']) : $customers->first();
              $districtObj = !empty($draft['district_id']) ? \App\Models\District::find($draft['district_id']) : $districts->first();
              $mineralObj  = !empty($draft['mineral_id']) ? \App\Models\Mineral::find($draft['mineral_id']) : $minerals->first();
              $val         = (float)($draft['product_value'] ?? 45000);
              $paid        = (float)($draft['paid_amount'] ?? 25000);
              $pending     = max(0, $val - $paid);
              $pStatus     = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="card-panel mt-3 mb-3" style="background:var(--navy-soft); border:none;">
              <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="bi bi-person-lines-fill me-2"></i>Project Summary</h6>
              <div class="row g-2" style="font-size: 0.85rem;">
                <div class="col-md-4"><span class="text-muted">Client Name:</span> <br><b>{{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}</b></div>
                <div class="col-md-4"><span class="text-muted">District:</span> <br><b>{{ $districtObj?->name ?: 'Salem' }}</b></div>
                <div class="col-md-4"><span class="text-muted">Mineral:</span> <br><b>{{ $mineralObj?->name ?: 'Rough Stone & Gravel' }}</b></div>
                <div class="col-md-12 mt-2"><span class="text-muted">Folders &amp; Documents:</span> <br><span class="badge bg-success">11 Statutory Presentation Folders Configured</span></div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              {{-- Team Preview --}}
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white h-100">
                  <h6 class="fw-bold text-navy mb-2"><i class="bi bi-people-fill text-primary me-1"></i> Designated Handling Team</h6>
                  @php
                    $previewHandlers = $draft['handlers'] ?? [
                      ['name' => 'Er. M. Senthil Kumar', 'role' => 'RQP / EIA Lead'],
                      ['name' => 'R. Murugan', 'role' => 'Client Rep']
                    ];
                  @endphp
                  @foreach($previewHandlers as $h)
                    <div class="d-flex justify-content-between py-1 {{ !$loop->last ? 'border-bottom' : '' }} small">
                      <span><strong>{{ $h['person_name'] ?? ($h['name'] ?? 'Officer') }}</strong></span>
                      <span class="badge bg-light text-dark border">{{ $h['role'] ?? 'Consultant' }}</span>
                    </div>
                  @endforeach
                </div>
              </div>

              {{-- Financial Preview --}}
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white h-100">
                  <h6 class="fw-bold text-navy mb-2"><i class="bi bi-receipt text-success me-1"></i> Financial Summary</h6>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Quoted Service Fee:</span>
                    <strong>₹ {{ number_format($val, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Paid Advance:</span>
                    <strong class="text-success">₹ {{ number_format($paid, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span class="text-muted">Pending Balance:</span>
                    <strong class="text-danger">₹ {{ number_format($pending, 2) }}</strong>
                  </div>
                  <div class="d-flex justify-content-between pt-2 small">
                    <span class="text-muted">Settlement Status:</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">{{ ucfirst($pStatus) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-panel mb-0" style="background:var(--green-soft);border:none">
              <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">All PPT statutory items, handling officers, and billing records verified. Ready to synchronize with database.</span>
            </div>
          @endif

          {{-- WIZARD NAVIGATION ACTIONS --}}
          <div class="wizard-actions mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="{{ $step === 1 ? route('ppt-department.index') : route('ppt-department.step', $step - 1) }}" class="btn btn-outline-navy btn-sm">
              <i class="bi bi-arrow-left"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
            </a>
            @can('ppt.create')
              <button type="submit" class="btn {{ $step === 9 ? 'btn-green' : 'btn-navy' }} px-4">
                {{ $step === 9 ? 'Finish & Save to Database' : 'Save & Continue' }} <i class="bi {{ $step === 9 ? 'bi-check2-circle' : 'bi-arrow-right' }}"></i>
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
  // Client selection autofill
  const selCust = document.getElementById('select_ppt_customer');
  const fMobile = document.getElementById('field_ppt_mobile');
  if (selCust && fMobile) {
    selCust.addEventListener('change', function() {
      const opt = selCust.options[selCust.selectedIndex];
      if (opt && opt.dataset.mobile) {
        fMobile.value = opt.dataset.mobile;
      }
    });
  }

  // PPT Step 5: Upload & View handlers
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-ppt-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.ppt-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('ppt-file-input')) {
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

          let viewBtn = slot.querySelector('.btn-view-ppt-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-2 btn-view-ppt-item me-1';
            viewBtn.style.fontSize = '.72rem';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.title = 'View document in separate page';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-ppt-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-ppt-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-ppt-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        updatePptDocProgress();
      }
    }
  });

  function updatePptDocProgress() {
    const total = document.querySelectorAll('#ppt_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#ppt_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('ppt_doc_counter');
    const bar = document.getElementById('ppt_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} uploaded`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

  // PPT Step 5: Add Document button
  const btnAddPptDoc = document.getElementById('btn_add_ppt_doc');
  if (btnAddPptDoc) {
    btnAddPptDoc.addEventListener('click', function() {
      const docName = prompt('Enter Presentation Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('ppt_checklist_container');
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
            <div class="ci-meta text-muted" style="font-size:11px;">Custom presentation attachment &middot; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <span class="badge-status pending">Optional</span>
          <input type="file" class="d-none ppt-file-input" accept=".pdf,.ppt,.pptx,.kml,.kmz,.jpg,.png">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-ppt-item" style="font-size:.72rem;">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updatePptDocProgress();
    });
  }

  // Handlers dynamic add/remove
  const btnAdd = document.getElementById('btn_add_ppt_handler');
  const tbody = document.getElementById('ppt_handlers_tbody');

  function reindexRows() {
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
      const delBtn = row.querySelector('.btn-remove-ppt-handler');
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
        <td><input type="text" name="handlers[${count}][person_name]" class="form-control form-control-sm" placeholder="e.g. S. Kumaran" required></td>
        <td><input type="text" name="handlers[${count}][role]" class="form-control form-control-sm" placeholder="e.g. Technical Consultant" required></td>
        <td><input type="text" name="handlers[${count}][notes]" class="form-control form-control-sm" placeholder="e.g. Liaison & coordination"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-ppt-handler">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
      reindexRows();
    });

    tbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-ppt-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const tr = btn.closest('tr');
        if (tr) {
          tr.remove();
          reindexRows();
        }
      }
    });
  }

  // Payment live calculation
  const fVal = document.getElementById('field_ppt_val');
  const fPaid = document.getElementById('field_ppt_paid');
  const fPending = document.getElementById('field_ppt_pending');
  const fStatus = document.getElementById('field_ppt_status');
  const dVal = document.getElementById('disp_ppt_val');
  const dPaid = document.getElementById('disp_ppt_paid');
  const dPending = document.getElementById('disp_ppt_pending');

  function fmtINR(val) {
    return '₹ ' + (Number(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcPptPayment() {
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
    fVal.addEventListener('input', recalcPptPayment);
    fPaid.addEventListener('input', recalcPptPayment);
  }

  // Step 1: Customer Unique ID Lookup & Instant Autofill
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const feedbackBox = document.getElementById('mimas_feedback_box');

  function applyPptCustomerAutofill(c) {
    if (!c) return;
    const selCust = document.getElementById('select_ppt_customer');
    const fProject = document.getElementById('field_ppt_project_name');
    const fMobile = document.getElementById('field_ppt_mobile');

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

    if (fProject) {
      const entity = c.company_name || c.customer_name || 'Presentation';
      fProject.value = entity + ' Presentation Concession';
    }

    if (fMobile) {
      fMobile.value = c.mobile_num || '';
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

  function performPptMimasLookup() {
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
          applyPptCustomerAutofill(res.data);
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

  if (btnLookupMimas) btnLookupMimas.addEventListener('click', performPptMimasLookup);
  if (mimasSearchInput) {
    mimasSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performPptMimasLookup();
      }
    });
    mimasSearchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performPptMimasLookup();
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
