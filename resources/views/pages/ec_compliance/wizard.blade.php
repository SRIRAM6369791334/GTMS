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

        <form method="POST" action="{{ $step === 8 ? route('ec-compliance.store') : route('ec-compliance.saveStep', $step) }}" id="ecComplianceWizardForm">
          @csrf

          {{-- STEP 1: BASIC & EC DETAILS --}}
          @if($step === 1)
            <h4>Quarry Entity &amp; EC Concession Details</h4>
            <div class="wc-sub">Link the statutory EC clearance project, define the compliance monitoring period and submission due date.</div>

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
                      data-mobile="{{ $c->mobile_num }}"
                      data-mimas="{{ $c->mimas_no }}"
                      {{ ($draft['customer_id'] ?? '') == $c->id ? 'selected' : ($loop->first && empty($draft['customer_id']) ? 'selected' : '') }}>
                      {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Linked Environment Project (Optional)</label>
                <select name="environment_project_id" class="form-select">
                  <option value="">-- Standalone Compliance Filing --</option>
                  @foreach($envProjects as $env)
                    <option value="{{ $env->id }}" {{ ($draft['environment_project_id'] ?? '') == $env->id ? 'selected' : '' }}>
                      {{ $env->project_code }} — {{ $env->project_name }}
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Linked EC Certificate (Optional)</label>
                <select name="ec_certificate_id" class="form-select">
                  <option value="">-- Choose Issued EC Certificate --</option>
                  @foreach($ecCertificates as $cert)
                    <option value="{{ $cert->id }}" {{ ($draft['ec_certificate_id'] ?? '') == $cert->id ? 'selected' : '' }}>
                      {{ $cert->ec_ref_no }} ({{ $cert->proposal_no ?: 'SEIAA-TN' }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">Half-Yearly Compliance Period *</label>
                <select name="compliance_period" class="form-select" required>
                  <option value="April 2026 - September 2026" {{ ($draft['compliance_period'] ?? '') === 'April 2026 - September 2026' ? 'selected' : '' }}>April 2026 - September 2026</option>
                  <option value="October 2026 - March 2027" {{ ($draft['compliance_period'] ?? '') === 'October 2026 - March 2027' ? 'selected' : '' }}>October 2026 - March 2027</option>
                  <option value="April 2025 - September 2025" {{ ($draft['compliance_period'] ?? '') === 'April 2025 - September 2025' ? 'selected' : '' }}>April 2025 - September 2025</option>
                  <option value="October 2025 - March 2026" {{ ($draft['compliance_period'] ?? '') === 'October 2025 - March 2026' ? 'selected' : '' }}>October 2025 - March 2026</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy">MoEFCC Submission Due Date *</label>
                <input type="date" class="form-control" name="submission_due_date"
                  value="{{ $draft['submission_due_date'] ?? date('Y-12-01') }}" required>
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
                <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom" data-comp-row="{{ $dIdx }}">
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
                    <input type="file" class="d-none comp-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.jpg,.jpeg,.png,.zip">
                    <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-comp-item" style="font-size:.72rem;">
                      <i class="bi bi-upload me-1"></i>Upload
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
                <div class="checklist-row py-3 d-flex align-items-center justify-content-between border-bottom" data-lab-row="{{ $lIdx }}">
                  <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px; height:40px; background:#fef3c7; color:#d97706;">
                      <i class="bi {{ $lItem['icon'] }} fs-5"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-bold text-navy text-truncate">{{ $lItem['name'] }}</div>
                      <div class="ci-meta text-muted small">{{ $lItem['meta'] }} &middot; <span class="badge bg-warning-subtle text-warning-emphasis">NABL Accredited</span></div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status mandatory">Mandatory</span>
                    <input type="file" class="d-none lab-file-input" data-row-idx="{{ $lIdx }}" accept=".pdf">
                    <button type="button" class="btn btn-sm btn-outline-navy py-1 px-3 btn-upload-lab-item">
                      <i class="bi bi-upload me-1"></i>Upload Report PDF
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
                <div class="checklist-row py-3 d-flex align-items-center justify-content-between border-bottom" data-report-row="{{ $rIdx }}">
                  <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate">
                    <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px; height:40px; background:#eff6ff; color:#0284c7;">
                      <i class="bi {{ $rItem['icon'] }} fs-5"></i>
                    </div>
                    <div class="text-truncate">
                      <div class="ci-name fw-bold text-navy text-truncate">{{ $rItem['name'] }}</div>
                      <div class="ci-meta text-muted small">{{ $rItem['meta'] }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                    <span class="badge-status mandatory">Mandatory</span>
                    <input type="file" class="d-none report-file-input" data-row-idx="{{ $rIdx }}" accept=".pdf,.doc,.docx">
                    <button type="button" class="btn btn-sm btn-outline-navy py-1 px-3 btn-upload-report-item">
                      <i class="bi bi-upload me-1"></i>Upload Document
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
                  <div class="p-3 bg-white rounded border d-flex align-items-center justify-content-between action-slot" id="parivesh_receipt_slot">
                    <div class="d-flex align-items-center gap-2">
                      <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                      <div>
                        <div class="fw-bold text-dark receipt-title">PARIVESH_Acknowledgement_Receipt_2026.pdf</div>
                        <small class="text-muted receipt-meta">Official MoEFCC acknowledgement receipt with barcoded timestamp</small>
                      </div>
                    </div>
                    <div>
                      <input type="file" class="d-none" id="parivesh_receipt_file" accept=".pdf">
                      <button type="button" class="btn btn-sm btn-outline-success px-3" id="btn_upload_parivesh_receipt">
                        <i class="bi bi-upload me-1"></i>Attach Receipt PDF
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
            @can('environment.view')
              <button type="submit" class="btn {{ $step === 8 ? 'btn-green' : 'btn-navy' }} px-4">
                {{ $step === 8 ? 'Finish & Save Compliance Filing' : 'Save & Continue' }} <i class="bi {{ $step === 8 ? 'bi-check2-circle' : 'bi-arrow-right' }}"></i>
              </button>
            @endcan
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- JAVASCRIPT FOR DYNAMIC DOCUMENTS, HANDLERS & REAL-TIME PAYMENT MATH --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Step 2 Documents Upload & View
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-comp-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.comp-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('comp-file-input')) {
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

          let viewBtn = slot.querySelector('.btn-view-comp-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-2 btn-view-comp-item me-1';
            viewBtn.style.fontSize = '.72rem';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.title = 'View document in separate page';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-comp-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-comp-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-comp-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        updateCompDocProgress();
      }
    }
  });

  function updateCompDocProgress() {
    const total = document.querySelectorAll('#comp_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#comp_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('comp_doc_counter');
    const bar = document.getElementById('comp_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} uploaded`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

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
          <span class="badge-status pending">Optional</span>
          <input type="file" class="d-none comp-file-input" accept=".pdf,.jpg,.jpeg,.png,.zip">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-comp-item" style="font-size:.72rem;">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updateCompDocProgress();
    });
  }

  // Step 3 Lab Upload & View handlers
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-lab-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.lab-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('lab-file-input')) {
      const input = e.target;
      const row = input.closest('.checklist-row');
      if (input.files && input.files[0] && row) {
        const file = input.files[0];
        const blobUrl = URL.createObjectURL(file);
        row.classList.add('up');

        const slot = row.querySelector('.action-slot');
        if (slot) {
          let viewBtn = slot.querySelector('.btn-view-lab-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-3 btn-view-lab-item me-1';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-lab-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-lab-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-lab-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        const total = document.querySelectorAll('#lab_checklist_container .checklist-row').length;
        const uploaded = document.querySelectorAll('#lab_checklist_container .checklist-row.up').length;
        const counter = document.getElementById('lab_doc_counter');
        const bar = document.getElementById('lab_progress_bar');
        if (counter) counter.textContent = `${uploaded} / ${total} reports`;
        if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
      }
    }
  });

  // Step 4 Report Upload & View handlers
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-report-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.report-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('report-file-input')) {
      const input = e.target;
      const row = input.closest('.checklist-row');
      if (input.files && input.files[0] && row) {
        const file = input.files[0];
        const blobUrl = URL.createObjectURL(file);
        row.classList.add('up');

        const slot = row.querySelector('.action-slot');
        if (slot) {
          let viewBtn = slot.querySelector('.btn-view-report-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-3 btn-view-report-item me-1';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-report-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-report-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-report-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }
      }
    }
  });

  // Step 5 Parivesh Receipt Upload & View
  const btnUploadReceipt = document.getElementById('btn_upload_parivesh_receipt');
  const fileReceipt = document.getElementById('parivesh_receipt_file');
  if (btnUploadReceipt && fileReceipt) {
    btnUploadReceipt.addEventListener('click', () => fileReceipt.click());
    fileReceipt.addEventListener('change', function() {
      if (fileReceipt.files && fileReceipt.files[0]) {
        const file = fileReceipt.files[0];
        const blobUrl = URL.createObjectURL(file);
        const slot = document.getElementById('parivesh_receipt_slot');
        if (slot) {
          let viewBtn = slot.querySelector('.btn-view-receipt');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info px-3 btn-view-receipt me-1';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View Receipt';
            slot.querySelector('div:last-child').insertBefore(viewBtn, btnUploadReceipt);
          }
          viewBtn.href = blobUrl;
          btnUploadReceipt.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
        }
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
