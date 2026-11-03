@extends('layouts.app')
@section('title', 'EC Half-Yearly Compliance Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$labels = [
  'EC Info',
  '1. Documents',
  '2. Site Analysis',
  '3. Report',
  '4. Parivesh Upload',
  'Handling Team',
  'Payment',
  'Preview'
];
@endphp

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap" style="max-width:980px;">
      {{-- Stepper Progress Bar (8 Stages) --}}
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
        <div class="wc-eyebrow">Step {{ $step }} of 8 &middot; Environmental Clearance Half-Yearly Compliance Process</div>

        <form method="POST" action="{{ $step === 8 ? route('ec-compliance.store') : route('ec-compliance.saveStep', $step) }}" id="ecComplianceWizardForm" enctype="multipart/form-data">
          @csrf

          {{-- STEP 1: BASIC & EC DETAILS --}}
          @if($step === 1)
            <h4>Quarry Entity &amp; EC Concession Details</h4>
            <div class="wc-sub">Link the statutory EC clearance project, define the compliance monitoring period and submission due date.</div>

            {{-- CUSTOMER UNIQUE ID LOOKUP CARD --}}
            <div class="s1-lookup-card mb-3.5 p-3">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="s1-lookup-label fw-bold mb-0" for="mimas_search_input">
                  <i class="fa fa-fingerprint me-1.5"></i> Customer Unique ID Lookup
                </label>
                <span class="badge s1-lookup-badge"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
              </div>
              <p class="s1-lookup-desc mt-2 mb-2.5">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
              
              <div class="input-group s1-input-group s1-lookup-input-group">
                <span class="input-group-text s1-addon s1-lookup-addon"><i class="fa fa-search"></i></span>
                <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold s1-input s1-lookup-field" 
                       placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
                <button class="btn s1-btn-fetch px-3 fw-bold" type="button" id="btn_lookup_mimas">
                  <i class="fa fa-sync-alt me-1.5"></i> Fetch Details
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
                <label class="form-label fw-bold text-navy">Compliance Filing No. *</label>
                <input class="form-control font-monospace fw-bold bg-light" name="compliance_no" readonly
                  value="{{ $draft['compliance_no'] ?? ('HYC-' . date('Y') . '-' . sprintf('%04d', \App\Models\EcCompliance::count() + 1)) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Registered Client / Company *</label>
                <select name="customer_id" id="select_comp_customer" class="form-select auto-filled-field" required>
                  <option value="">-- Choose Client / Entity --</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}"
                      data-company="{{ $c->company_name }}"
                      data-name="{{ $c->customer_name }}"
                      data-mobile="{{ $c->mobile_num }}"
                      data-secondary-person="{{ $c->secondary_contact_person }}"
                      data-secondary-mobile="{{ $c->secondary_mobile_num }}"
                      data-mimas="{{ $c->mimas_no }}"
                      {{ ($draft['customer_id'] ?? '') == $c->id ? 'selected' : ($loop->first && empty($draft['customer_id']) ? 'selected' : '') }}>
                      {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Primary Contact Person Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control auto-filled-field" name="primary_contact_person" id="field_comp_primary_contact"
                  value="{{ $draft['primary_contact_person'] ?? ($customers->first()?->customer_name ?? '') }}" placeholder="Enter primary contact person name" required maxlength="255">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Primary Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control auto-filled-field" name="primary_phone" id="field_comp_primary_phone"
                  value="{{ $draft['primary_phone'] ?? ($customers->first()?->mobile_num ?? '') }}" placeholder="10-digit primary phone number" required maxlength="15">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Secondary Contact Person Name <span class="text-muted small fw-normal">(Optional)</span></label>
                <input type="text" class="form-control auto-filled-field" name="secondary_contact_person" id="field_comp_secondary_contact"
                  value="{{ $draft['secondary_contact_person'] ?? ($customers->first()?->secondary_contact_person ?? '') }}" placeholder="Enter secondary contact person name" maxlength="255">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Secondary Phone Number <span class="text-muted small fw-normal">(Optional)</span></label>
                <input type="text" class="form-control auto-filled-field" name="secondary_phone" id="field_comp_secondary_phone"
                  value="{{ $draft['secondary_phone'] ?? ($customers->first()?->secondary_mobile_num ?? '') }}" placeholder="10-digit secondary phone number" maxlength="15">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">
                  <i class="bi bi-diagram-3 me-1 text-primary"></i> Linked Environment Project <span class="text-muted small fw-normal">(Optional)</span>
                </label>
                <input type="text" class="form-control auto-filled-field" name="environment_project_name" id="field_comp_env_project"
                  value="{{ $draft['environment_project_name'] ?? '' }}" placeholder="Enter Environment Project Name / Proposal No. manually">
                <small class="text-muted" style="font-size:11px;">Type project name or reference code manually if linked to a previous clearance.</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">
                  <i class="bi bi-file-earmark-arrow-up me-1 text-primary"></i> Linked EC Certificate <span class="text-muted small fw-normal">(Optional - Upload Image or Document)</span>
                </label>
                <div class="input-group">
                  <input type="file" class="form-control" name="ec_certificate_file" id="field_comp_ec_cert_file" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx">
                  @if(!empty($draft['ec_certificate_file']))
                    <a href="{{ asset('storage/' . $draft['ec_certificate_file']) }}" target="_blank" class="btn btn-outline-info" title="View Uploaded File">
                      <i class="bi bi-eye"></i> View
                    </a>
                  @endif
                </div>
                <div class="d-flex align-items-center justify-content-between mt-1">
                  <small class="text-muted" style="font-size:11px;">
                    @if(!empty($draft['ec_certificate_file']))
                      <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Uploaded: {{ $draft['ec_certificate_name'] ?? basename($draft['ec_certificate_file']) }}</span>
                    @else
                      Upload copy of prior EC Clearance order (PDF, PNG, JPG, DOC up to 50MB).
                    @endif
                  </small>
                  <span id="ec_cert_preview_badge" class="badge bg-light text-primary border" style="display:none; font-size:11px;"></span>
                </div>
              </div>
              @php
                $currentPeriod = $draft['compliance_period'] ?? 'April 2026 - September 2026';
                $selectedCycle = str_contains($currentPeriod, 'October') ? 'October - March' : 'April - September';
                preg_match('/(?:April|October)\s+(\d{4})/i', $currentPeriod, $yMatches);
                $startYear = !empty($yMatches[1]) ? (int)$yMatches[1] : (int)date('Y');

                $baseYear = (int)date('Y');
                $yearsList = [];
                for ($y = $baseYear + 2; $y >= $baseYear - 4; $y--) {
                    $yearsList[] = [
                        'start_year' => $y,
                        'end_year'   => $y + 1,
                        'label'      => $y . ' - ' . ($y + 1),
                    ];
                }
              @endphp

              <div class="col-md-4">
                <label class="form-label fw-bold text-navy">
                  <i class="bi bi-arrow-repeat me-1 text-primary"></i> Half-Yearly Cycle *
                </label>
                <select id="select_compliance_cycle" class="form-select" required>
                  <option value="April - September" {{ $selectedCycle === 'April - September' ? 'selected' : '' }}>
                    April – September (H1 Period)
                  </option>
                  <option value="October - March" {{ $selectedCycle === 'October - March' ? 'selected' : '' }}>
                    October – March (H2 Period)
                  </option>
                </select>
                <small class="text-muted" style="font-size:11px;">Statutory 6-month compliance cycle</small>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold text-navy">
                  <i class="bi bi-calendar3 me-1 text-primary"></i> Compliance Financial Year *
                </label>
                <select id="select_compliance_year" class="form-select" required>
                  @foreach($yearsList as $yItem)
                    <option value="{{ $yItem['start_year'] }}" {{ $startYear == $yItem['start_year'] ? 'selected' : '' }}>
                       {{ $yItem['start_year'] }}
                    </option>
                  @endforeach
                </select>
                <small class="text-muted" style="font-size:11px;">Select compliance audit fiscal year</small>
              </div>

              <div class="col-md-4">
                <label class="form-label fw-bold text-navy">
                  <i class="bi bi-clock-history me-1 text-primary"></i> MoEFCC Submission Due Date *
                </label>
                <input type="date" class="form-control" name="submission_due_date" id="field_submission_due_date"
                  value="{{ $draft['submission_due_date'] ?? date('Y-12-01') }}" required>
                <small class="text-muted" style="font-size:11px;">Statutory portal filing deadline</small>
              </div>

              {{-- Synchronized Compliance Period & Year Hidden Fields --}}
              <input type="hidden" name="compliance_period" id="field_compliance_period" value="{{ $currentPeriod }}">
              <input type="hidden" name="compliance_year" id="field_compliance_year" value="{{ $draft['compliance_year'] ?? $startYear }}">

              <div class="col-md-12 mt-1">
                <div class="p-2 px-3 bg-light rounded border d-flex align-items-center justify-content-between" style="font-size:0.83rem;">
                  <div>
                    <span class="text-muted fw-semibold">Active Compliance Window:</span>
                    <strong class="text-navy ms-1" id="compliance_period_preview_text">{{ $currentPeriod }}</strong>
                  </div>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    <i class="bi bi-info-circle me-1"></i> Auto-synced with statutory deadline
                  </span>
                </div>
              </div>
              <div class="col-md-12">
                <label class="form-label fw-bold text-navy">Quarry Project Title *</label>
                <input type="text" class="form-control auto-filled-field" name="project_name" id="field_comp_project_name"
                  value="{{ $draft['project_name'] ?? 'Kaveri Granites Rough Stone & Gravel Quarry Half-Yearly Compliance' }}" required>
              </div>
            </div>

          {{-- STEP 2: 1. DOCUMENTS (19 STATUTORY ITEMS FROM DIAGRAM) --}}
          @elseif($step === 2)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-folder-open text-primary me-2"></i>1. Documents Checklist (19 Statutory Items)</h4>
                <div class="wc-sub mb-0">Upload all required statutory approvals, certificates, site amenities and photograph proofs.</div>
              </div>
              <div class="text-end">
                <span class="text-muted small fw-semibold">OVERALL DOCUMENT COMPLETION:</span>
                <span class="fw-bold text-navy ms-1" id="comp_doc_counter">0 / 19 uploaded</span>
                <div class="progress mt-1" style="height: 6px; width: 170px;">
                  <div class="progress-bar bg-success" id="comp_progress_bar" role="progressbar" style="width: 0%;"></div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
              <div style="font-size:.75rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
                <i class="fa fa-list-check me-1 text-primary"></i> Statutory Attachments &amp; Photographic Evidence
              </div>
              <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btn_add_comp_doc" style="font-size:0.75rem;">
                <i class="bi bi-plus-circle me-1"></i>Add Document
              </button>
            </div>

            @php
              $docs19 = [
                ['name' => '1. 500m Letter / Certificate', 'meta' => '500m cluster radius certificate from Assistant Director of Geology & Mining', 'mandatory' => true],
                ['name' => '2. 300m Letter / Certificate', 'meta' => '300m habitation / village distance certificate from local revenue authority', 'mandatory' => true],
                ['name' => '3. Consent to Operate (CTO) — TNPCB', 'meta' => 'Valid Consent to Operate order under Air & Water Acts from SPCB', 'mandatory' => true],
                ['name' => '4. Registered Lease Deed Agreement', 'meta' => 'Registered mining lease deed document with boundary schedule', 'mandatory' => true],
                ['name' => '5. Explosive License & Magazine Certificate', 'meta' => 'PESO explosive magazine storage license & shotfirer certificate', 'mandatory' => true],
                ['name' => '6. Quarry Workers Insurance Policy', 'meta' => 'Group workmen compensation / personal accident insurance certificate', 'mandatory' => true],
                ['name' => '7. Newspaper Advertisement (English & Tamil)', 'meta' => 'Public notification tear-sheet in one English and one Tamil daily newspaper', 'mandatory' => true],
                ['name' => '8. Quarry Mandatory Name Board Photo', 'meta' => 'High-resolution photo of display board showing EC, extent & lease particulars', 'mandatory' => true],
                ['name' => '9. Greenbelt Plantation & Barbed Wire Fencing Photos', 'meta' => 'Photographs of peripheral 7.5m greenbelt tree saplings and perimeter safety fence', 'mandatory' => true],
                ['name' => '10. Labour Rest Shed & Sanitation Toilet Photos', 'meta' => 'Photographs of drinking water shelter, rest room, and separate toilets for workers', 'mandatory' => true],
                ['name' => '11. First Aid Box Facility Photo', 'meta' => 'Site office first aid box with emergency medical supplies photo', 'mandatory' => true],
                ['name' => '12. RO Drinking Water Facility Photo', 'meta' => 'Potable reverse osmosis (RO) drinking water dispenser setup at quarry', 'mandatory' => true],
                ['name' => '13. Haul Road Water Sprinkling Facility Photo', 'meta' => 'Water tanker sprinkling system photo for dust suppression on quarry haul roads', 'mandatory' => true],
                ['name' => '14. Safety Equipment PPE Kit Distribution Photo', 'meta' => 'Quarry workers wearing safety helmets, high-vis vests, goggles, and steel-toe boots', 'mandatory' => true],
                ['name' => '15. Corporate Social Responsibility (CSR) Photos', 'meta' => 'Proof of CSR activities in nearby government schools or village panchayats', 'mandatory' => false],
                ['name' => '16. Corporate Environment Responsibility (CER) Photos', 'meta' => 'Proof of environmental community spend (solar lights, sapling distribution, etc.)', 'mandatory' => false],
                ['name' => '17. Last Mineral Transit Permit / Dispatch Details', 'meta' => 'Copy of latest bulk transit permit / e-permit challans downloaded from MMS', 'mandatory' => true],
                ['name' => '18. Tarpaulin Covered Transport Trucks Photo', 'meta' => 'Photographs showing dispatched mineral trucks properly covered with tarpaulins', 'mandatory' => true],
                ['name' => '19. CCTV Camera & Security Installation Photo', 'meta' => 'High-resolution photo showing active IP CCTV security surveillance camera at entrance', 'mandatory' => true],
              ];
            @endphp

            <div class="folder-checklist-box" id="comp_checklist_container" style="max-height: 480px; overflow-y: auto;">
              @foreach($docs19 as $dIdx => $dItem)
                @php
                  $matchDoc = collect($uploadedDocs ?? [])->first(function($d) use ($dItem) {
                    return ($d['document_name'] ?? '') === $dItem['name'];
                  });
                  $isZip = $matchDoc && (!empty($matchDoc['is_zip']) || str_ends_with(strtolower($matchDoc['file_name'] ?? ''), '.zip'));
                @endphp
                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom {{ $matchDoc ? 'up' : '' }}"
                     data-comp-row="{{ $dIdx }}"
                     data-doc-name="{{ $dItem['name'] }}"
                     data-category="checklist"
                     data-mandatory="{{ $dItem['mandatory'] ? '1' : '0' }}">
                  <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:34px; height:34px; background:{{ $matchDoc ? ($isZip ? '#fef3c7' : '#dcfce7') : '#f1f5f9' }}; color:{{ $matchDoc ? ($isZip ? '#d97706' : '#15803d') : '#64748b' }};">
                      <i class="bi {{ $matchDoc ? ($isZip ? 'bi-file-earmark-zip-fill' : 'bi-check-lg') : 'bi-file-earmark' }}"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-semibold small text-dark text-truncate">{{ $dItem['name'] }}</div>
                      <div class="ci-meta text-muted" style="font-size:11px;">
                        @if($matchDoc)
                          <span class="{{ $isZip ? 'text-warning-emphasis' : 'text-success' }} fw-semibold">
                            <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-paperclip' }}"></i> {{ $matchDoc['file_name'] }}
                          </span> &middot; {{ $matchDoc['file_size'] ?? 'Attached' }}
                        @else
                          {{ $dItem['meta'] }} &middot; Not uploaded
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <select class="form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center {{ $dItem['mandatory'] ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                            name="comp_doc_req[{{ $dIdx }}]" style="font-size:0.75rem; width:110px; border-radius:6px;">
                      <option value="mandatory" {{ $dItem['mandatory'] ? 'selected' : '' }}>Mandatory</option>
                      <option value="optional" {{ !$dItem['mandatory'] ? 'selected' : '' }}>Optional</option>
                    </select>

                    <a href="{{ $matchDoc['file_url'] ?? '#' }}"
                       class="btn btn-sm btn-outline-info py-1 px-2 btn-view-item btn-view-comp-item {{ $matchDoc ? '' : 'd-none' }}"
                       style="font-size:.72rem;"
                       data-file-url="{{ $matchDoc['file_url'] ?? '' }}"
                       data-file-name="{{ $matchDoc['file_name'] ?? '' }}"
                       data-file-size="{{ $matchDoc['file_size'] ?? '' }}"
                       data-doc-name="{{ $dItem['name'] }}"
                       data-is-zip="{{ $isZip ? '1' : '0' }}"
                       title="View Document">
                      <i class="bi bi-eye"></i> View
                    </a>

                    <input type="file" class="d-none comp-file-input row-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                    <button type="button" class="btn btn-sm {{ $matchDoc ? 'btn-outline-secondary' : 'btn-outline-navy' }} py-1 px-2 btn-upload-item btn-upload-comp-item" style="font-size:.72rem;">
                      <i class="bi {{ $matchDoc ? 'bi-arrow-repeat' : 'bi-upload' }} me-1"></i><span>{{ $matchDoc ? 'Change' : 'Upload' }}</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-item btn-remove-comp-item {{ $matchDoc ? '' : 'd-none' }}" style="font-size:.72rem;" title="Remove Document">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 3: 2. SITE ANALYSIS STUDY (NABL TESTS) --}}
          @elseif($step === 3)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-flask text-warning me-2"></i>2. Site Analysis Study (4 NABL Laboratory Tests)</h4>
                <div class="wc-sub mb-0">Upload certified laboratory test reports conducted by NABL / MoEFCC recognized environmental laboratories.</div>
              </div>
              <div class="text-end">
                <span class="text-muted small fw-semibold">LAB TEST COMPLETION:</span>
                <span class="fw-bold text-navy ms-1" id="lab_doc_counter">0 / 4 reports</span>
                <div class="progress mt-1" style="height: 6px; width: 160px;">
                  <div class="progress-bar bg-warning" id="lab_progress_bar" role="progressbar" style="width: 0%;"></div>
                </div>
              </div>
            </div>

            @php
              $labTests = [
                ['name' => '1. Air Quality Monitoring Report', 'meta' => 'PM10, PM2.5, SO2, NOx, CO 24-hr continuous ambient monitoring test certificate', 'icon' => 'bi-wind'],
                ['name' => '2. Noise Level Monitoring Report', 'meta' => 'Day & Night equivalent noise decibel Leq dB(A) at buffer zone & quarry boundary', 'icon' => 'bi-volume-up'],
                ['name' => '3. Soil Sample Analysis Report', 'meta' => 'Soil pH, conductivity, organic matter, texture, fertility & heavy metal profile test', 'icon' => 'bi-flower1'],
                ['name' => '4. Water Sample Test Report', 'meta' => 'Groundwater borewell & surface runoff quality test report conforming to IS 10500', 'icon' => 'bi-droplet-half'],
              ];
            @endphp

            <div class="folder-checklist-box" id="lab_checklist_container">
              @foreach($labTests as $lIdx => $lItem)
                @php
                  $matchDoc = collect($uploadedDocs ?? [])->first(function($d) use ($lItem) {
                    return ($d['document_name'] ?? '') === $lItem['name'];
                  });
                  $isZip = $matchDoc && (!empty($matchDoc['is_zip']) || str_ends_with(strtolower($matchDoc['file_name'] ?? ''), '.zip'));
                @endphp
                <div class="checklist-row py-3 d-flex align-items-center justify-content-between border-bottom {{ $matchDoc ? 'up' : '' }}"
                     data-lab-row="{{ $lIdx }}"
                     data-doc-name="{{ $lItem['name'] }}"
                     data-category="site_analysis">
                  <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px; height:40px; background:{{ $matchDoc ? ($isZip ? '#fef3c7' : '#dcfce7') : '#fef3c7' }}; color:{{ $matchDoc ? ($isZip ? '#d97706' : '#15803d') : '#d97706' }};">
                      <i class="bi {{ $matchDoc ? ($isZip ? 'bi-file-earmark-zip-fill' : 'bi-check-lg') : $lItem['icon'] }} fs-5"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-bold text-navy text-truncate">{{ $lItem['name'] }}</div>
                      <div class="ci-meta text-muted small">
                        @if($matchDoc)
                          <span class="{{ $isZip ? 'text-warning-emphasis' : 'text-success' }} fw-semibold">
                            <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-paperclip' }}"></i> {{ $matchDoc['file_name'] }}
                          </span> &middot; {{ $matchDoc['file_size'] ?? 'Attached' }} &middot; <span class="badge bg-warning-subtle text-warning-emphasis">NABL Accredited</span>
                        @else
                          {{ $lItem['meta'] }} &middot; <span class="badge bg-warning-subtle text-warning-emphasis">NABL Accredited</span>
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status mandatory">Mandatory</span>

                    <a href="{{ $matchDoc['file_url'] ?? '#' }}"
                       class="btn btn-sm btn-outline-info py-1 px-3 btn-view-item btn-view-lab-item {{ $matchDoc ? '' : 'd-none' }}"
                       style="font-size:.72rem;"
                       data-file-url="{{ $matchDoc['file_url'] ?? '' }}"
                       data-file-name="{{ $matchDoc['file_name'] ?? '' }}"
                       data-file-size="{{ $matchDoc['file_size'] ?? '' }}"
                       data-doc-name="{{ $lItem['name'] }}"
                       data-is-zip="{{ $isZip ? '1' : '0' }}"
                       title="View Report">
                      <i class="bi bi-eye"></i> View
                    </a>

                    <input type="file" class="d-none lab-file-input row-file-input" data-row-idx="{{ $lIdx }}" accept=".pdf,.doc,.docx,.zip">
                    <button type="button" class="btn btn-sm {{ $matchDoc ? 'btn-outline-secondary' : 'btn-outline-navy' }} py-1 px-3 btn-upload-item btn-upload-lab-item">
                      <i class="bi {{ $matchDoc ? 'bi-arrow-repeat' : 'bi-upload' }} me-1"></i><span>{{ $matchDoc ? 'Change' : 'Upload Report PDF' }}</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-item btn-remove-lab-item {{ $matchDoc ? '' : 'd-none' }}" style="font-size:.72rem;" title="Remove Report">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 4: 3. REPORT PREPARATION --}}
          @elseif($step === 4)
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-file-alt text-info me-2"></i>3. Report Preparation</h4>
                <div class="wc-sub mb-0">Compile the statutory compliance report components before portal submission.</div>
              </div>
            </div>

            @php
              $reportSections = [
                ['name' => '1. Front Page / Cover Sheet', 'meta' => 'Official letterhead cover sheet with proponent, EC reference & monitoring interval', 'icon' => 'bi-file-earmark-font'],
                ['name' => '2. Covering Letter to MoEFCC / SEIAA', 'meta' => 'Formal transmittal covering letter addressed to Regional Officer, MoEFCC IRO Chennai', 'icon' => 'bi-envelope-paper'],
                ['name' => '3. Comprehensive EC-Compliance Report', 'meta' => 'Point-by-point condition-wise environmental compliance verification and action status', 'icon' => 'bi-journal-check'],
              ];
            @endphp

            <div class="folder-checklist-box" id="report_checklist_container">
              @foreach($reportSections as $rIdx => $rItem)
                @php
                  $matchDoc = collect($uploadedDocs ?? [])->first(function($d) use ($rItem) {
                    return ($d['document_name'] ?? '') === $rItem['name'];
                  });
                  $isZip = $matchDoc && (!empty($matchDoc['is_zip']) || str_ends_with(strtolower($matchDoc['file_name'] ?? ''), '.zip'));
                @endphp
                <div class="checklist-row py-3 d-flex align-items-center justify-content-between border-bottom {{ $matchDoc ? 'up' : '' }}"
                     data-report-row="{{ $rIdx }}"
                     data-doc-name="{{ $rItem['name'] }}"
                     data-category="report_preparation">
                  <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px; height:40px; background:{{ $matchDoc ? ($isZip ? '#fef3c7' : '#dcfce7') : '#eff6ff' }}; color:{{ $matchDoc ? ($isZip ? '#d97706' : '#15803d') : '#0284c7' }};">
                      <i class="bi {{ $matchDoc ? ($isZip ? 'bi-file-earmark-zip-fill' : 'bi-check-lg') : $rItem['icon'] }} fs-5"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-bold text-navy text-truncate">{{ $rItem['name'] }}</div>
                      <div class="ci-meta text-muted small">
                        @if($matchDoc)
                          <span class="{{ $isZip ? 'text-warning-emphasis' : 'text-success' }} fw-semibold">
                            <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-paperclip' }}"></i> {{ $matchDoc['file_name'] }}
                          </span> &middot; {{ $matchDoc['file_size'] ?? 'Attached' }}
                        @else
                          {{ $rItem['meta'] }}
                        @endif
                      </div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status mandatory">Mandatory</span>

                    <a href="{{ $matchDoc['file_url'] ?? '#' }}"
                       class="btn btn-sm btn-outline-info py-1 px-3 btn-view-item btn-view-report-item {{ $matchDoc ? '' : 'd-none' }}"
                       style="font-size:.72rem;"
                       data-file-url="{{ $matchDoc['file_url'] ?? '' }}"
                       data-file-name="{{ $matchDoc['file_name'] ?? '' }}"
                       data-file-size="{{ $matchDoc['file_size'] ?? '' }}"
                       data-doc-name="{{ $rItem['name'] }}"
                       data-is-zip="{{ $isZip ? '1' : '0' }}"
                       title="View Document">
                      <i class="bi bi-eye"></i> View
                    </a>

                    <input type="file" class="d-none report-file-input row-file-input" data-row-idx="{{ $rIdx }}" accept=".pdf,.doc,.docx,.zip">
                    <button type="button" class="btn btn-sm {{ $matchDoc ? 'btn-outline-secondary' : 'btn-outline-navy' }} py-1 px-3 btn-upload-item btn-upload-report-item">
                      <i class="bi {{ $matchDoc ? 'bi-arrow-repeat' : 'bi-upload' }} me-1"></i><span>{{ $matchDoc ? 'Change' : 'Upload Document' }}</span>
                    </button>

                    <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-item btn-remove-report-item {{ $matchDoc ? '' : 'd-none' }}" style="font-size:.72rem;" title="Remove Document">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

          {{-- STEP 5: 4. UPLOADING REPORT (PARIVESH PORTAL) --}}
          @elseif($step === 5)
            <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-cloud-upload-alt text-success me-2"></i>4. Uploading Report (MoEFCC Parivesh Portal)</h4>
            <div class="wc-sub">Record official submission on the Government of India PARIVESH portal and attach the acknowledgement slip.</div>

            <div class="card p-3 border rounded-3 bg-light mb-4">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy">Parivesh Acknowledgement / Proposal Reference No. *</label>
                  <input type="text" class="form-control font-monospace fw-bold" name="parivesh_acknowledgement_no"
                    placeholder="e.g. SIA/TN/MIN/HYC/2026/0491" value="{{ $draft['parivesh_acknowledgement_no'] ?? 'SIA/TN/MIN/HYC/2026/0491' }}" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy">Portal Upload &amp; Timestamp Date *</label>
                  <input type="date" class="form-control" name="parivesh_uploaded_date"
                    value="{{ $draft['parivesh_uploaded_date'] ?? date('Y-m-d') }}" required>
                </div>
                <div class="col-md-12">
                  <label class="form-label fw-bold text-navy">Attach Official Parivesh Acknowledgement Receipt (PDF) *</label>
                  @php
                    $matchReceipt = collect($uploadedDocs ?? [])->first(function($d) {
                      return ($d['folder_category'] ?? '') === 'parivesh' || ($d['document_name'] ?? '') === 'Parivesh Acknowledgement Receipt';
                    });
                  @endphp
                  <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between action-slot {{ $matchReceipt ? 'up' : '' }}"
                       id="parivesh_receipt_slot"
                       data-doc-name="Parivesh Acknowledgement Receipt"
                       data-category="parivesh">
                    <div class="d-flex align-items-center gap-2 flex-grow-1 text-truncate">
                      <i class="bi bi-file-earmark-pdf fs-3 text-danger flex-shrink-0"></i>
                      <div class="text-truncate">
                        <div class="fw-bold text-dark receipt-title text-truncate">{{ $matchReceipt['file_name'] ?? 'PARIVESH_Acknowledgement_Receipt_2026.pdf' }}</div>
                        <small class="text-muted receipt-meta">{{ $matchReceipt ? ('Attached: ' . ($matchReceipt['file_size'] ?? '')) : 'Official MoEFCC acknowledgement receipt with barcoded timestamp' }}</small>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                      <a href="{{ $matchReceipt['file_url'] ?? '#' }}"
                         class="btn btn-sm btn-outline-info px-3 btn-view-item btn-view-receipt {{ $matchReceipt ? '' : 'd-none' }}"
                         data-file-url="{{ $matchReceipt['file_url'] ?? '' }}"
                         data-file-name="{{ $matchReceipt['file_name'] ?? '' }}"
                         data-file-size="{{ $matchReceipt['file_size'] ?? '' }}"
                         data-doc-name="Parivesh Acknowledgement Receipt"
                         data-is-zip="0"
                         title="View Receipt">
                        <i class="bi bi-eye"></i> View Receipt
                      </a>
                      <input type="file" class="d-none" id="parivesh_receipt_file" accept=".pdf">
                      <button type="button" class="btn btn-sm {{ $matchReceipt ? 'btn-outline-secondary' : 'btn-outline-success' }} px-3" id="btn_upload_parivesh_receipt">
                        <i class="bi {{ $matchReceipt ? 'bi-arrow-repeat' : 'bi-upload' }} me-1"></i><span>{{ $matchReceipt ? 'Change Receipt' : 'Attach Receipt PDF' }}</span>
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-danger px-2 btn-remove-receipt {{ $matchReceipt ? '' : 'd-none' }}" id="btn_remove_parivesh_receipt" title="Remove Receipt">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          {{-- STEP 6: PROJECT HANDLING TEAM --}}
          @elseif($step === 6)
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
              <div>
                <h5 class="fw-bold mb-1" style="color:#0F1E4D;">Compliance Field Team &amp; Environmental Auditors</h5>
                <div class="text-muted small">Assign environmental consultants, sampling officers, and portal submission leads.</div>
              </div>
              <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_comp_handler" style="background:#0F1E4D; color:#fff;">
                <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
              </button>
            </div>

            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle" id="comp_handlers_table">
                <thead class="bg-light text-navy" style="font-size:0.85rem;">
                  <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 30%;">Person Name <span class="text-danger">*</span></th>
                    <th style="width: 30%;">Role / Designation <span class="text-danger">*</span></th>
                    <th>Notes &amp; Responsibilities</th>
                    <th style="width: 70px;" class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody id="comp_handlers_tbody">
                  @php
                    $savedCompHandlers = $draft['handlers'] ?? [
                      ['name' => 'Dr. K. Ravichandran', 'role' => 'Lead Environmental Auditor', 'notes' => 'Site inspection, compliance verification & condition audit'],
                      ['name' => 'S. Kumaran', 'role' => 'NABL Lab Sampling Officer', 'notes' => 'Collection of ambient air, noise, water & soil samples'],
                      ['name' => 'M. Anand', 'role' => 'Parivesh Submission Specialist', 'notes' => 'Online MoEFCC report compilation & portal filing']
                    ];
                  @endphp
                  @foreach($savedCompHandlers as $hIdx => $h)
                    <tr>
                      <td class="text-center fw-bold row-num">{{ $hIdx + 1 }}</td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][person_name]" class="form-control form-control-sm" value="{{ $h['person_name'] ?? ($h['name'] ?? '') }}" placeholder="e.g. Dr. K. Ravichandran" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][role]" class="form-control form-control-sm" value="{{ $h['role'] ?? '' }}" placeholder="e.g. Environmental Auditor" required></td>
                      <td><input type="text" name="handlers[{{ $hIdx }}][notes]" class="form-control form-control-sm" value="{{ $h['notes'] ?? '' }}" placeholder="Responsibilities"></td>
                      <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-comp-handler {{ count($savedCompHandlers) === 1 ? 'disabled' : '' }}">
                          <i class="bi bi-trash"></i>
                        </button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          {{-- STEP 7: PAYMENT DETAILS & BILLING LEDGER --}}
          @elseif($step === 7)
            <h5 class="fw-bold mb-1" style="color:#0F1E4D;">Compliance Service Fee &amp; Payment Ledger</h5>
            <div class="wc-sub">Track statutory compliance audit fee, NABL laboratory charges, and client advance settlements.</div>

            @php
              $val     = (float)($draft['product_value'] ?? 65000);
              $paid    = (float)($draft['paid_amount'] ?? 65000);
              $pending = max(0, $val - $paid);
              $status  = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
                  <div class="text-muted small fw-semibold text-uppercase">Compliance Service Value</div>
                  <div class="h4 fw-bold mb-0 text-navy mt-1" id="disp_comp_val">₹ {{ number_format($val, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Audit + NABL Testing + Parivesh</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
                  <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
                  <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_comp_paid">₹ {{ number_format($paid, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Payment received</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
                  <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
                  <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_comp_pending">₹ {{ number_format($pending, 2) }}</div>
                  <small class="text-muted" style="font-size:11px;">Auto-calculated outstanding</small>
                </div>
              </div>
            </div>

            <div class="card p-3 bg-light border-0 rounded-3 mb-4">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Service Fee Value (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white fw-bold">₹</span>
                    <input type="number" step="0.01" min="0" name="product_value" id="field_comp_val" class="form-control fw-bold" placeholder="0.00" value="{{ $val }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Paid Amount (₹) *</label>
                  <div class="input-group">
                    <span class="input-group-text bg-white fw-bold text-success">₹</span>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="field_comp_paid" class="form-control fw-bold text-success" placeholder="0.00" value="{{ $paid }}" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold text-navy small mb-1">Pending Balance (₹)</label>
                  <div class="input-group">
                    <span class="input-group-text bg-light text-muted">₹</span>
                    <input type="number" step="0.01" name="pending_amount" id="field_comp_pending" class="form-control bg-light fw-bold text-success" placeholder="0.00" readonly value="{{ number_format($pending, 2, '.', '') }}">
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Settlement Status *</label>
                  <select class="form-select" name="payment_status" id="field_comp_status">
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
                    <option value="partial" {{ $status === 'partial' ? 'selected' : '' }}>Partial Payment Received</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold text-navy small mb-1">Transaction Reference / Receipt Notes</label>
                  <input type="text" class="form-control" name="payment_notes" placeholder="e.g. Bank transfer ref / UPI" value="{{ $draft['payment_notes'] ?? 'NEFT Ref #HYC-99120 - Full audit and testing fees cleared' }}">
                </div>
              </div>
            </div>

          {{-- STEP 8: PREVIEW & FINAL CONFIRMATION --}}
          @elseif($step === 8)
            <h4>EC Half-Yearly Compliance Verification &amp; Final Summary</h4>
            <div class="wc-sub">Review compliance audit findings, 4 pillars checklist, and billing records before committing to database.</div>

            @php
              $customerObj = !empty($draft['customer_id']) ? \App\Models\Customer::find($draft['customer_id']) : $customers->first();
              $val         = (float)($draft['product_value'] ?? 65000);
              $paid        = (float)($draft['paid_amount'] ?? 65000);
              $pending     = max(0, $val - $paid);
              $pStatus     = $draft['payment_status'] ?? ($pending == 0 ? 'paid' : ($paid > 0 ? 'partial' : 'pending'));
            @endphp

            <div class="card-panel mt-3 mb-3" style="background:var(--navy-soft); border:none;">
              <h6 style="color:#0F1E4D; font-weight:600; margin-bottom: 12px;"><i class="bi bi-card-checklist me-2"></i>Compliance Filing Summary</h6>
              <div class="row g-2" style="font-size: 0.85rem;">
                <div class="col-md-6"><span class="text-muted">Client / Entity:</span> <br><b>{{ $customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}</b></div>
                <div class="col-md-6"><span class="text-muted">Filing Number:</span> <br><b>{{ $draft['compliance_no'] ?? 'HYC-2026-0001' }}</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Compliance Period:</span> <br><b>{{ $draft['compliance_period'] ?? 'April 2026 - September 2026' }}</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Parivesh Ack Ref:</span> <br><b class="font-monospace text-success">{{ $draft['parivesh_acknowledgement_no'] ?? 'SIA/TN/MIN/HYC/2026/0491' }}</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Primary Contact:</span> <br><b>{{ $draft['primary_contact_person'] ?? ($customerObj?->customer_name ?? '—') }} ({{ $draft['primary_phone'] ?? '—' }})</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Secondary Contact:</span> <br><b>{{ $draft['secondary_contact_person'] ?? '—' }} ({{ $draft['secondary_phone'] ?? '—' }})</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Linked Environment Project:</span> <br><b>{{ $draft['environment_project_name'] ?? 'Standalone Compliance Filing' }}</b></div>
                <div class="col-md-6 mt-2"><span class="text-muted">Linked EC Certificate:</span> <br>
                  @if(!empty($draft['ec_certificate_file']))
                    <a href="{{ asset('storage/' . $draft['ec_certificate_file']) }}" target="_blank" class="text-primary fw-bold">
                      <i class="bi bi-paperclip"></i> {{ $draft['ec_certificate_name'] ?? basename($draft['ec_certificate_file']) }} (Attached)
                    </a>
                  @else
                    <span class="text-muted">None attached</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="row g-3 mb-3">
              {{-- 4 Pillars Recap --}}
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white h-100">
                  <h6 class="fw-bold text-navy mb-2"><i class="bi bi-shield-check text-primary me-1"></i> 4 Regulatory Pillars Verified</h6>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>1. Statutory Documents Checklist</span>
                    <span class="badge bg-success">19 Items Attached</span>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>2. Site Analysis Study (NABL)</span>
                    <span class="badge bg-success">4 Lab Reports Certified</span>
                  </div>
                  <div class="d-flex justify-content-between py-1 border-bottom small">
                    <span>3. Statutory Compliance Report</span>
                    <span class="badge bg-success">Prepared &amp; Signed</span>
                  </div>
                  <div class="d-flex justify-content-between pt-1 small">
                    <span>4. Parivesh MoEFCC Upload</span>
                    <span class="badge bg-info text-white">Acknowledged</span>
                  </div>
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
              <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">All four compliance pillars, environmental testing reports, and billing ledger verified. Ready to synchronize with database.</span>
            </div>
          @endif

          {{-- WIZARD NAVIGATION ACTIONS --}}
          <div class="wizard-actions mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
            <a href="{{ $step === 1 ? route('ec-compliance.index') : route('ec-compliance.step', $step - 1) }}" class="btn btn-outline-navy btn-sm">
              <i class="bi bi-arrow-left"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
            </a>
            @canany(['ec_compliance.create', 'environment.view'])
              <button type="submit" class="btn {{ $step === 8 ? 'btn-green' : 'btn-navy' }} px-4">
                {{ $step === 8 ? 'Finish & Save Compliance Filing' : 'Save & Continue' }} <i class="bi {{ $step === 8 ? 'bi-check2-circle' : 'bi-arrow-right' }}"></i>
              </button>
            @endcanany
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
.checklist-row.row-dragover, #parivesh_receipt_slot.row-dragover {
  background-color: #f0fdf4 !important;
  outline: 2px dashed #16a34a !important;
  outline-offset: -2px;
}
</style>
{{-- JAVASCRIPT FOR DYNAMIC DOCUMENTS, HANDLERS & REAL-TIME PAYMENT MATH --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const CSRF_TOKEN = '{{ csrf_token() }}';
  const UPLOAD_URL = '{{ route("ec-compliance.upload") }}';
  const REMOVE_URL = '{{ route("ec-compliance.remove") }}';

  function uploadEcFile(row, file, category, docName) {
    const uploadBtn = row.querySelector('.btn-upload-item, .btn-upload-comp-item, .btn-upload-lab-item, .btn-upload-report-item, #btn_upload_parivesh_receipt');
    const origBtnHtml = uploadBtn ? uploadBtn.innerHTML : '';
    if (uploadBtn) {
      uploadBtn.disabled = true;
      uploadBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Uploading...';
    }

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('file', file);
    formData.append('document_name', docName);
    formData.append('folder_category', category);

    fetch(UPLOAD_URL, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(function(res) {
      if (!res.ok) {
        return res.json().then(function(j) { throw new Error(j.message || ('Upload failed with status ' + res.status)); });
      }
      return res.json();
    })
    .then(function(data) {
      if (uploadBtn) {
        uploadBtn.disabled = false;
        uploadBtn.className = uploadBtn.className.replace('btn-outline-navy', 'btn-outline-secondary').replace('btn-outline-success', 'btn-outline-secondary');
        uploadBtn.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i><span>Change</span>';
      }

      if (data.status === 1 || data.success) {
        row.classList.add('up');
        const isZip = data.is_zip || (data.file_name && data.file_name.toLowerCase().endsWith('.zip'));

        // Update icon
        const iconDiv = row.querySelector('.ci-icon');
        if (iconDiv) {
          iconDiv.style.background = isZip ? '#fef3c7' : '#dcfce7';
          iconDiv.style.color = isZip ? '#d97706' : '#15803d';
          iconDiv.innerHTML = '<i class="bi ' + (isZip ? 'bi-file-earmark-zip-fill' : 'bi-check-lg') + (row.hasAttribute('data-lab-row') || row.hasAttribute('data-report-row') ? ' fs-5' : '') + '"></i>';
        }

        // Update meta / receipt title
        const metaDiv = row.querySelector('.ci-meta');
        if (metaDiv) {
          const sizeStr = data.file_size ? ' &middot; ' + data.file_size : '';
          const extraBadge = row.hasAttribute('data-lab-row') ? ' &middot; <span class="badge bg-warning-subtle text-warning-emphasis">NABL Accredited</span>' : '';
          metaDiv.innerHTML = '<span class="' + (isZip ? 'text-warning-emphasis' : 'text-success') + ' fw-semibold"><i class="bi ' + (isZip ? 'bi-file-earmark-zip' : 'bi-paperclip') + '"></i> ' + (data.file_name || docName) + '</span>' + sizeStr + extraBadge;
        }

        const receiptTitle = row.querySelector('.receipt-title');
        if (receiptTitle) receiptTitle.textContent = data.file_name || docName;
        const receiptMeta = row.querySelector('.receipt-meta');
        if (receiptMeta) receiptMeta.textContent = 'Attached: ' + (data.file_size || '');

        // Update status badge
        const statusBadge = row.querySelector('.badge-status');
        if (statusBadge && !statusBadge.classList.contains('mandatory')) {
          statusBadge.className = 'badge-status uploaded';
          statusBadge.textContent = 'Uploaded';
        }

        // Update View button
        let viewBtn = row.querySelector('.btn-view-item, .btn-view-comp-item, .btn-view-lab-item, .btn-view-report-item, .btn-view-receipt');
        if (viewBtn) {
          viewBtn.classList.remove('d-none');
          viewBtn.href = data.file_url || '#';
          viewBtn.setAttribute('data-file-url', data.file_url || '');
          viewBtn.setAttribute('data-file-name', data.file_name || docName);
          viewBtn.setAttribute('data-file-size', data.file_size || '');
          viewBtn.setAttribute('data-doc-name', docName);
          viewBtn.setAttribute('data-is-zip', isZip ? '1' : '0');
        }

        // Show Remove button
        const removeBtn = row.querySelector('.btn-remove-item, .btn-remove-comp-item, .btn-remove-lab-item, .btn-remove-report-item, .btn-remove-receipt, #btn_remove_parivesh_receipt');
        if (removeBtn) {
          removeBtn.classList.remove('d-none');
        }

        updateCompDocProgress();
        updateLabDocProgress();

        if (window.toastr) {
          toastr.success('Document "' + (data.file_name || docName) + '" uploaded successfully!');
        }
      } else {
        alert(data.message || 'Upload failed.');
      }
    })
    .catch(function(err) {
      if (uploadBtn) {
        uploadBtn.disabled = false;
        uploadBtn.innerHTML = origBtnHtml;
      }
      alert('Upload error: ' + err.message);
    });
  }

  function removeEcFile(row, category, docName) {
    if (!confirm('Are you sure you want to remove this attached file?')) return;

    const removeBtn = row.querySelector('.btn-remove-item, .btn-remove-comp-item, .btn-remove-lab-item, .btn-remove-report-item, .btn-remove-receipt, #btn_remove_parivesh_receipt');
    const origBtnHtml = removeBtn ? removeBtn.innerHTML : '';
    if (removeBtn) {
      removeBtn.disabled = true;
      removeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
    }

    const formData = new FormData();
    formData.append('_token', CSRF_TOKEN);
    formData.append('document_name', docName);
    formData.append('folder_category', category);

    fetch(REMOVE_URL, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    })
    .then(function(res) {
      if (!res.ok) {
        return res.json().then(function(j) { throw new Error(j.message || ('Remove failed with status ' + res.status)); });
      }
      return res.json();
    })
    .then(function(data) {
      if (removeBtn) {
        removeBtn.disabled = false;
        removeBtn.innerHTML = origBtnHtml;
        removeBtn.classList.add('d-none');
      }

      if (data.status === 1 || data.success) {
        row.classList.remove('up');

        // Reset file input
        const fileInput = row.querySelector('.comp-file-input, .lab-file-input, .report-file-input, #parivesh_receipt_file');
        if (fileInput) fileInput.value = '';

        // Reset icon
        const iconDiv = row.querySelector('.ci-icon');
        if (iconDiv) {
          iconDiv.style.background = '';
          iconDiv.style.color = '';
          const defIcon = row.hasAttribute('data-lab-row') ? 'bi-flask' : (row.hasAttribute('data-report-row') ? 'bi-file-earmark-text' : 'bi-file-earmark');
          iconDiv.innerHTML = '<i class="bi ' + defIcon + (row.hasAttribute('data-lab-row') || row.hasAttribute('data-report-row') ? ' fs-5' : '') + '"></i>';
        }

        // Reset meta
        const metaDiv = row.querySelector('.ci-meta');
        if (metaDiv) {
          const extraBadge = row.hasAttribute('data-lab-row') ? ' &middot; <span class="badge bg-warning-subtle text-warning-emphasis">NABL Accredited</span>' : '';
          metaDiv.innerHTML = 'Not uploaded' + extraBadge;
        }

        const receiptTitle = row.querySelector('.receipt-title');
        if (receiptTitle) receiptTitle.textContent = 'PARIVESH_Acknowledgement_Receipt_2026.pdf';
        const receiptMeta = row.querySelector('.receipt-meta');
        if (receiptMeta) receiptMeta.textContent = 'Official MoEFCC acknowledgement receipt with barcoded timestamp';

        // Hide View button
        const viewBtn = row.querySelector('.btn-view-item, .btn-view-comp-item, .btn-view-lab-item, .btn-view-report-item, .btn-view-receipt');
        if (viewBtn) {
          viewBtn.classList.add('d-none');
          viewBtn.href = '#';
        }

        // Reset upload button
        const uploadBtn = row.querySelector('.btn-upload-item, .btn-upload-comp-item, .btn-upload-lab-item, .btn-upload-report-item, #btn_upload_parivesh_receipt');
        if (uploadBtn) {
          uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i><span>' + (row.id === 'parivesh_receipt_slot' ? 'Attach Receipt PDF' : (row.hasAttribute('data-lab-row') ? 'Upload Report PDF' : (row.hasAttribute('data-report-row') ? 'Upload Document' : 'Upload'))) + '</span>';
        }

        updateCompDocProgress();
        updateLabDocProgress();

        if (window.toastr) {
          toastr.success('Document "' + docName + '" removed successfully.');
        }
      } else {
        alert(data.message || 'Remove failed.');
      }
    })
    .catch(function(err) {
      if (removeBtn) {
        removeBtn.disabled = false;
        removeBtn.innerHTML = origBtnHtml;
      }
      alert('Remove error: ' + err.message);
    });
  }

  function attachWizardRowEvents(row) {
    const fileInput = row.querySelector('.comp-file-input, .lab-file-input, .report-file-input, #parivesh_receipt_file');
    const uploadBtn = row.querySelector('.btn-upload-item, .btn-upload-comp-item, .btn-upload-lab-item, .btn-upload-report-item, #btn_upload_parivesh_receipt');
    const removeBtn = row.querySelector('.btn-remove-item, .btn-remove-comp-item, .btn-remove-lab-item, .btn-remove-report-item, .btn-remove-receipt, #btn_remove_parivesh_receipt');
    const category = row.getAttribute('data-category') || 'checklist';
    const docName = row.getAttribute('data-doc-name') || row.querySelector('.ci-name')?.textContent.trim() || 'Document';

    if (uploadBtn && fileInput) {
      uploadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
      });
    }

    if (fileInput) {
      fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
          uploadEcFile(row, this.files[0], category, docName);
        }
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', function(e) {
        e.preventDefault();
        removeEcFile(row, category, docName);
      });
    }

    // Drag & Drop
    ['dragenter', 'dragover'].forEach(function(eventName) {
      row.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        row.classList.add('row-dragover');
      }, false);
    });

    ['dragleave', 'dragend', 'drop'].forEach(function(eventName) {
      row.addEventListener(eventName, function(e) {
        e.preventDefault();
        e.stopPropagation();
        row.classList.remove('row-dragover');
      }, false);
    });

    row.addEventListener('drop', function(e) {
      const dt = e.dataTransfer;
      if (dt && dt.files && dt.files.length > 0) {
        uploadEcFile(row, dt.files[0], category, docName);
      }
    });
  }

  document.querySelectorAll('.checklist-row, #parivesh_receipt_slot').forEach(attachWizardRowEvents);

  function updateCompDocProgress() {
    const total = document.querySelectorAll('#comp_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#comp_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('comp_doc_counter');
    const bar = document.getElementById('comp_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} uploaded`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

  function updateLabDocProgress() {
    const total = document.querySelectorAll('#lab_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#lab_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('lab_doc_counter');
    const bar = document.getElementById('lab_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} reports`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

  updateCompDocProgress();
  updateLabDocProgress();

  // Step 2 Add Document Custom Button
  const btnAddCompDoc = document.getElementById('btn_add_comp_doc');
  if (btnAddCompDoc) {
    btnAddCompDoc.addEventListener('click', function() {
      const docName = prompt('Enter Statutory Compliance Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('comp_checklist_container');
      const count = container.querySelectorAll('.checklist-row').length + 1;
      const row = document.createElement('div');
      row.className = 'checklist-row py-2 d-flex align-items-center justify-content-between border-bottom';
      row.setAttribute('data-mandatory', '0');
      row.setAttribute('data-doc-name', docName.trim());
      row.setAttribute('data-category', 'checklist');
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
            <div class="ci-meta text-muted" style="font-size:11px;">Custom compliance attachment &middot; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <select class="form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center border-secondary-subtle text-muted bg-light"
                  name="comp_custom_doc_req_${count}" style="font-size:0.75rem; width:110px; border-radius:6px;">
            <option value="mandatory">Mandatory</option>
            <option value="optional" selected>Optional</option>
          </select>
          <a href="#" class="btn btn-sm btn-outline-info py-1 px-2 btn-view-item btn-view-comp-item d-none" style="font-size:.72rem;" title="View Document">
            <i class="bi bi-eye"></i> View
          </a>
          <input type="file" class="d-none comp-file-input row-file-input" accept=".pdf,.jpg,.jpeg,.png,.zip">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-item btn-upload-comp-item" style="font-size:.72rem;">
            <i class="bi bi-upload me-1"></i><span>Upload</span>
          </button>
          <button type="button" class="btn btn-sm btn-outline-danger py-1 px-2 btn-remove-item btn-remove-comp-item d-none" style="font-size:.72rem;" title="Remove Document">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      `;
      container.appendChild(row);
      attachWizardRowEvents(row);
      updateCompDocProgress();
      bindCompDocReqListeners();
    });
  }

  function bindCompDocReqListeners() {
    document.querySelectorAll('#comp_checklist_container .doc-req-select').forEach(function(sel) {
      sel.onchange = function() {
        const row = this.closest('.checklist-row');
        const isMandatory = this.value === 'mandatory';
        if (row) {
          row.setAttribute('data-mandatory', isMandatory ? '1' : '0');
        }
        if (isMandatory) {
          this.className = 'form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center border-danger-subtle text-danger bg-danger-subtle';
        } else {
          this.className = 'form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center border-secondary-subtle text-muted bg-light';
        }
      };
    });
  }
  bindCompDocReqListeners();

  // EC Compliance Step 2: Relaxed vs Mandatory validation on form submission
  const compForm = document.getElementById('ecComplianceWizardForm');
  if (compForm && @json($step === 2)) {
    compForm.addEventListener('submit', function(e) {
      let missingMandatory = [];
      document.querySelectorAll('#comp_checklist_container .checklist-row').forEach(function(row) {
        const isMandatory = row.getAttribute('data-mandatory') === '1';
        const isUploaded = row.classList.contains('up');
        if (isMandatory && !isUploaded) {
          const docName = row.querySelector('.ci-name')?.textContent.trim() || 'Mandatory Document';
          missingMandatory.push(docName);
        }
      });
      if (missingMandatory.length > 0) {
        e.preventDefault();
        alert('Please upload all mandatory documents before continuing:\n- ' + missingMandatory.join('\n- '));
      }
    });
  }

  // Step 6 Handlers dynamic add/remove
  const btnAdd = document.getElementById('btn_add_comp_handler');
  const tbody = document.getElementById('comp_handlers_tbody');

  function reindexCompRows() {
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
      const delBtn = row.querySelector('.btn-remove-comp-handler');
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
        <td><input type="text" name="handlers[${count}][role]" class="form-control form-control-sm" placeholder="e.g. Environmental Field Auditor" required></td>
        <td><input type="text" name="handlers[${count}][notes]" class="form-control form-control-sm" placeholder="e.g. Monitoring & inspection"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-comp-handler">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
      reindexCompRows();
    });

    tbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-comp-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const tr = btn.closest('tr');
        if (tr) {
          tr.remove();
          reindexCompRows();
        }
      }
    });
  }

  // Step 7 Payment live calculation
  const fVal = document.getElementById('field_comp_val');
  const fPaid = document.getElementById('field_comp_paid');
  const fPending = document.getElementById('field_comp_pending');
  const fStatus = document.getElementById('field_comp_status');
  const dVal = document.getElementById('disp_comp_val');
  const dPaid = document.getElementById('disp_comp_paid');
  const dPending = document.getElementById('disp_comp_pending');

  function fmtINR(val) {
    return '₹ ' + (Number(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcCompPayment() {
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
    fVal.addEventListener('input', recalcCompPayment);
    fPaid.addEventListener('input', recalcCompPayment);
  }

  // Step 1: Customer Unique ID Lookup & Instant Autofill
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const feedbackBox = document.getElementById('mimas_feedback_box');

  function applyCompCustomerAutofill(c) {
    if (!c) return;
    const selCust = document.getElementById('select_comp_customer');
    const fProject = document.getElementById('field_comp_project_name');

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
      const entity = c.company_name || c.customer_name || 'Quarry';
      fProject.value = entity + ' Half-Yearly Compliance Monitoring';
    }

    const fPrimaryContact = document.getElementById('field_comp_primary_contact');
    const fPrimaryPhone = document.getElementById('field_comp_primary_phone');
    const fSecondaryContact = document.getElementById('field_comp_secondary_contact');
    const fSecondaryPhone = document.getElementById('field_comp_secondary_phone');
    if (fPrimaryContact) {
      fPrimaryContact.value = c.customer_name || '';
    }
    if (fPrimaryPhone) {
      fPrimaryPhone.value = c.mobile_num || '';
    }
    if (fSecondaryContact) {
      fSecondaryContact.value = c.secondary_contact_person || '';
    }
    if (fSecondaryPhone) {
      fSecondaryPhone.value = c.secondary_mobile_num || '';
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

  function performCompMimasLookup() {
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
          applyCompCustomerAutofill(res.data);
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

  const selectCompCustomer = document.getElementById('select_comp_customer');
  if (selectCompCustomer) {
    selectCompCustomer.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      const fPrimaryContact = document.getElementById('field_comp_primary_contact');
      const fPrimaryPhone = document.getElementById('field_comp_primary_phone');
      const fSecondaryContact = document.getElementById('field_comp_secondary_contact');
      const fSecondaryPhone = document.getElementById('field_comp_secondary_phone');
      if (opt && fPrimaryContact) {
        fPrimaryContact.value = opt.dataset.name || '';
      }
      if (opt && fPrimaryPhone) {
        fPrimaryPhone.value = opt.dataset.mobile || '';
      }
      if (opt && fSecondaryContact) {
        fSecondaryContact.value = opt.dataset.secondaryPerson || '';
      }
      if (opt && fSecondaryPhone) {
        fSecondaryPhone.value = opt.dataset.secondaryMobile || '';
      }
    });
  }

  if (btnLookupMimas) btnLookupMimas.addEventListener('click', performCompMimasLookup);
  if (mimasSearchInput) {
    mimasSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performCompMimasLookup();
      }
    });
    mimasSearchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performCompMimasLookup();
      }
    });
  }

  // Step 1: EC Certificate file selection instant feedback
  const ecCertFileInput = document.getElementById('field_comp_ec_cert_file');
  const ecCertBadge = document.getElementById('ec_cert_preview_badge');
  if (ecCertFileInput && ecCertBadge) {
    ecCertFileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        const sizeKb = Math.round(file.size / 1024);
        ecCertBadge.style.display = 'inline-block';
        ecCertBadge.innerHTML = `<i class="bi bi-paperclip"></i> Selected: ${file.name} (${sizeKb} KB)`;
      }
    });
  }

  // Step 1: Compliance Period Cycle & Year Sync with MoEFCC Due Date
  const selCycle = document.getElementById('select_compliance_cycle');
  const selYear = document.getElementById('select_compliance_year');
  const fieldPeriod = document.getElementById('field_compliance_period');
  const fieldYear = document.getElementById('field_compliance_year');
  const fieldDueDate = document.getElementById('field_submission_due_date');
  const previewBadge = document.getElementById('compliance_period_preview_text');

  function updateCompliancePeriodAndDueDate() {
    if (!selCycle || !selYear) return;
    const cycle = selCycle.value;
    const startYear = parseInt(selYear.value, 10);
    const nextYear = startYear + 1;

    let fullPeriod = '';
    let autoDueDate = '';

    if (cycle === 'October - March') {
      fullPeriod = `October ${startYear} - March ${nextYear}`;
      autoDueDate = `${nextYear}-06-01`;
    } else {
      fullPeriod = `April ${startYear} - September ${startYear}`;
      autoDueDate = `${startYear}-12-01`;
    }

    if (fieldPeriod) fieldPeriod.value = fullPeriod;
    if (fieldYear) fieldYear.value = String(startYear);
    if (fieldDueDate && !fieldDueDate.dataset.userEdited) {
      fieldDueDate.value = autoDueDate;
    }
    if (previewBadge) {
      previewBadge.textContent = fullPeriod;
    }
  }

  if (selCycle) {
    selCycle.addEventListener('change', function() {
      if (fieldDueDate) delete fieldDueDate.dataset.userEdited;
      updateCompliancePeriodAndDueDate();
    });
  }
  if (selYear) {
    selYear.addEventListener('change', function() {
      if (fieldDueDate) delete fieldDueDate.dataset.userEdited;
      updateCompliancePeriodAndDueDate();
    });
  }
  if (fieldDueDate) {
    fieldDueDate.addEventListener('change', function() {
      this.dataset.userEdited = '1';
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
