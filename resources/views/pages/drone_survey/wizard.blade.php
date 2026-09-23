@extends('layouts.app')
@section('title', 'Drone Survey Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$labels = ['Survey Request','Data Acquisition','Data Processing','Deliverables','GTMS Upload','Handling Team','Payment','Preview'];
$steps = [
  ['Drone Survey Request',['Request Letter','Lease Area Details','Approved Mining Plan','EC Copy'],'fa-file-alt'],
  ['Drone Data Acquisition',['Drone Flying','Aerial Photography','Image Capture','Video Recording'],'fa-paper-plane'],
  ['Data Processing',['Image Stitching','Orthomosaic Map','Contour Generation','3D Model (if required)'],'fa-cogs'],
  ['Drone Survey Deliverables',['Orthomosaic Map','Contour Map (DSM / DTM)','3D Model / Point Cloud','Survey Report'],'fa-map'],
  ['Report Upload in GTMS Portal',['Upload final drone survey report and deliverables'],'fa-cloud-upload-alt'],
  ['Project Handling Team',['DGCA Certified Drone Pilot','GIS Specialist','Photogrammetry Analyst'],'fa-users'],
  ['Payment Details & Ledger',['Drone Flight Fee','3D Volumetric Processing','Settlement Status'],'fa-file-invoice-dollar'],
  ['Preview & Confirmation',['Review submitted data','Confirm completion'],'fa-eye']
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
        <div class="wc-eyebrow">Drone Department &mdash; Survey &middot; Sub Process {{ $step }} of 8</div>
        <h4>{{ $steps[$step - 1][0] }}</h4>
        <div class="wc-sub">Complete the drone survey activities in this stage before moving to the next step.</div>

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
              <label class="form-label fw-bold text-navy">Drone Survey Request No. *</label>
              <input class="form-control font-monospace fw-bold bg-light" name="survey_no" readonly value="DRN-{{ date('Y') }}-0026">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-navy">Client / Applicant *</label>
              <input class="form-control auto-filled-field" id="field_drone_applicant" name="applicant_name" placeholder="Applicant name" value="Kaveri Granites Pvt Ltd">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-navy">Lease Area (Ha)</label>
              <input class="form-control auto-filled-field" id="field_drone_area" name="lease_area" placeholder="Area in hectares" value="3.85 Hectares">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold text-navy">Location / Quarry Site</label>
              <input class="form-control auto-filled-field" id="field_drone_location" name="location" placeholder="District / village" value="Salem / Semmandapatti">
            </div>
          </div>

        {{-- STEP 2: DATA ACQUISITION --}}
        @elseif($step === 2)
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Flight Date</label>
              <input class="form-control" type="date" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Drone / Pilot Details</label>
              <input class="form-control" placeholder="Drone model and pilot name" value="DJI Matrice 300 RTK &bull; Pilot: S. Karthik (RPC #DRN-TN-8821)">
            </div>
          </div>
          <div class="card-panel mt-4 mb-0" style="background:var(--navy-soft);border:none">
            <i class="bi bi-camera-video"></i> <span style="font-size:.78rem">Capture aerial photographs, high-resolution imagery and video recordings during the survey flight.</span>
          </div>

        {{-- STEP 3: DATA PROCESSING & DOCUMENTS UPLOAD --}}
        @elseif($step === 3)
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
              <h4 class="mb-1">Process Drone Data &amp; Upload Deliverables</h4>
              <div class="wc-sub mb-0">Create stitched imagery, orthomosaic map, point clouds, contours, and volumetric calculation report.</div>
            </div>
            <div class="text-end">
              <span class="text-muted small fw-semibold">OVERALL DOCUMENT COMPLETION:</span>
              <span class="fw-bold text-navy ms-1" id="drone_doc_counter">0 / 6 uploaded</span>
              <div class="progress mt-1" style="height: 6px; width: 160px;">
                <div class="progress-bar bg-success" id="drone_progress_bar" role="progressbar" style="width: 0%;"></div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2 mt-3">
            <div style="font-size:.75rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
              <i class="fa fa-folder-open me-1 text-primary"></i> 1. Drone Flight Logs &amp; Volumetric Deliverables
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" id="btn_add_drone_doc" style="font-size:0.75rem;">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
          </div>

          @php
            $droneDocs = [
              ['name' => '1. Drone Mission Flight Log & DGCA RPC Certificate', 'meta' => 'DGCA Pilot license, flight path log, and mission approval', 'mandatory' => true],
              ['name' => '2. Ground Control Points (GCP) Coordinates Sheet', 'meta' => 'Surveyed ground benchmark and RTK correction points CSV', 'mandatory' => true],
              ['name' => '3. High-Resolution Orthomosaic Map (GeoTIFF / JPG)', 'meta' => 'Georeferenced orthophoto mosaic of quarry lease boundary', 'mandatory' => true],
              ['name' => '4. Digital Surface Model (DSM) / Digital Terrain Model (DTM)', 'meta' => '3D elevation terrain model and height contour map', 'mandatory' => true],
              ['name' => '5. 3D Dense Point Cloud & Surface Mesh File', 'meta' => 'Dense point cloud classification (LAS / PLY / OBJ)', 'mandatory' => true],
              ['name' => '6. Volumetric Excavation & Stockpile Calculation Report', 'meta' => 'Cut / fill earthwork volume and mineral reserve extraction report', 'mandatory' => true],
            ];
          @endphp

          <div class="folder-checklist-box" id="drone_checklist_container">
            @foreach($droneDocs as $dIdx => $dItem)
              <div class="checklist-row py-2 d-flex align-items-center justify-content-between border-bottom" data-drone-row="{{ $dIdx }}">
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
                  <input type="file" class="d-none drone-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dxf">
                  <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-drone-item" style="font-size:.72rem;">
                    <i class="bi bi-upload me-1"></i>Upload
                  </button>
                </div>
              </div>
            @endforeach
          </div>

        {{-- STEP 4: DELIVERABLES --}}
        @elseif($step === 4)
          <div class="card-panel" style="min-height:190px;background:#f8fafc">
            <div class="text-center pt-3">
              <i class="fa fa-map-marked-alt" style="font-size:3.5rem;color:var(--navy)"></i>
              <h5 class="mt-3">Drone Survey Deliverables</h5>
              <p class="text-muted small">Orthomosaic, contour map (DSM / DTM), 3D model / point cloud and volumetric calculation report.</p>
              <div class="d-flex justify-content-center gap-2">
                <a href="javascript:void(0)" onclick="alert('Drone Deliverables & Volumetric Report opened in new window'); window.open('about:blank', '_blank');" class="btn btn-outline-navy btn-sm">
                  <i class="bi bi-eye me-1"></i> View Deliverables
                </a>
              </div>
            </div>
          </div>

        {{-- STEP 5: GTMS UPLOAD --}}
        @elseif($step === 5)
          <div class="card-panel" style="background:var(--green-soft);border:none">
            <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Output: Drone Survey Completed and deliverables uploaded in GTMS portal.</span>
          </div>

        {{-- STEP 6: PROJECT HANDLING TEAM (NEW STEP) --}}
        @elseif($step === 6)
          <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
            <div>
              <h5 class="fw-bold mb-1" style="color:#0F1E4D;">Drone Survey Flight Crew &amp; In-Charge Team</h5>
              <div class="text-muted small">Designate DGCA certified pilots, safety observers, and photogrammetry analysts.</div>
            </div>
            <button type="button" class="btn btn-sm btn-navy px-3" id="btn_add_drone_handler" style="background:#0F1E4D; color:#fff;">
              <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
            </button>
          </div>

          <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle" id="drone_handlers_table">
              <thead class="bg-light text-navy" style="font-size:0.85rem;">
                <tr>
                  <th style="width: 50px;" class="text-center">#</th>
                  <th style="width: 30%;">Person Name <span class="text-danger">*</span></th>
                  <th style="width: 30%;">Role / Designation <span class="text-danger">*</span></th>
                  <th>Notes &amp; Responsibilities</th>
                  <th style="width: 70px;" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody id="drone_handlers_tbody">
                <tr>
                  <td class="text-center fw-bold row-num">1</td>
                  <td><input type="text" name="handlers[0][person_name]" class="form-control form-control-sm" value="S. Karthik" placeholder="e.g. S. Karthik" required></td>
                  <td><input type="text" name="handlers[0][role]" class="form-control form-control-sm" value="DGCA Certified Drone Pilot" placeholder="e.g. Drone Pilot" required></td>
                  <td><input type="text" name="handlers[0][notes]" class="form-control form-control-sm" value="Autonomous grid flight execution & safety compliance" placeholder="Responsibilities"></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler disabled">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td class="text-center fw-bold row-num">2</td>
                  <td><input type="text" name="handlers[1][person_name]" class="form-control form-control-sm" value="M. Vignesh" placeholder="e.g. M. Vignesh"></td>
                  <td><input type="text" name="handlers[1][role]" class="form-control form-control-sm" value="Photogrammetry & 3D Analyst" placeholder="e.g. GIS Analyst"></td>
                  <td><input type="text" name="handlers[1][notes]" class="form-control form-control-sm" value="Image stitching, GCP georeferencing & cut/fill volume calculation" placeholder="Responsibilities"></td>
                  <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="alert alert-info py-2 px-3 small rounded-2 mb-0" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
            <i class="bi bi-info-circle me-1"></i> Custom roles can be manually typed (e.g. <em>DGCA Pilot, Safety Officer, Volumetric Analyst, Ground Control Observer</em>).
          </div>

        {{-- STEP 7: PAYMENT DETAILS & BILLING LEDGER (NEW STEP) --}}
        @elseif($step === 7)
          <h5 class="fw-bold mb-1" style="color:#0F1E4D;">Drone Survey &amp; Volumetric Fee Ledger</h5>
          <div class="wc-sub">Track drone flight service charge, 3D photogrammetry processing fee, and client settlements.</div>

          <!-- Real-Time Metric Cards -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
                <div class="text-muted small fw-semibold text-uppercase">Volumetric Survey Value</div>
                <div class="h4 fw-bold mb-0 text-navy mt-1" id="disp_drone_val">₹ 50,000.00</div>
                <small class="text-muted" style="font-size:11px;">Flight &amp; 3D analysis quotation</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
                <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
                <div class="h4 fw-bold mb-0 text-success mt-1" id="disp_drone_paid">₹ 30,000.00</div>
                <small class="text-muted" style="font-size:11px;">Flight deposit received</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
                <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
                <div class="h4 fw-bold mb-0 text-danger mt-1" id="disp_drone_pending">₹ 20,000.00</div>
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
                  <input type="number" step="0.01" min="0" name="product_value" id="field_drone_val" class="form-control fw-bold" placeholder="0.00" value="50000">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-navy small mb-1">Paid Amount (₹) *</label>
                <div class="input-group">
                  <span class="input-group-text bg-white fw-bold text-success">₹</span>
                  <input type="number" step="0.01" min="0" name="paid_amount" id="field_drone_paid" class="form-control fw-bold text-success" placeholder="0.00" value="30000">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-navy small mb-1">Pending Balance (₹)</label>
                <div class="input-group">
                  <span class="input-group-text bg-light text-muted">₹</span>
                  <input type="number" step="0.01" name="pending_amount" id="field_drone_pending" class="form-control bg-light fw-bold text-danger" placeholder="0.00" readonly value="20000.00">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy small mb-1">Settlement Status *</label>
                <select class="form-select" name="payment_status" id="field_drone_status">
                  <option value="pending">Pending (Full Balance Due)</option>
                  <option value="partial" selected>Partial Payment Received</option>
                  <option value="paid">Paid (Fully Settled)</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-navy small mb-1">Transaction Reference / Receipt Notes</label>
                <input type="text" class="form-control" name="payment_notes" placeholder="e.g. Bank transfer ref / Cheque" value="Cheque #881902 cleared - 60% advance paid for flight and volumetric modeling">
              </div>
            </div>
          </div>

        {{-- STEP 8: PREVIEW & FINAL CONFIRMATION --}}
        @elseif($step === 8)
          <h4>Drone Volumetric Survey Verification &amp; Final Summary</h4>
          <div class="wc-sub">Review survey deliverables, aerial flight crew, and financial ledger status.</div>

          <div class="card-panel mt-3 mb-3" style="background:var(--navy-soft); border:none;">
            <h6 style="color:#0F1E4D; font-weight:600; margin-bottom: 12px;"><i class="bi bi-card-checklist me-2"></i>Drone Survey Summary</h6>
            <div class="row g-2" style="font-size: 0.85rem;">
              <div class="col-md-6"><span class="text-muted">Client / Applicant:</span> <br><b>Kaveri Granites Pvt Ltd</b></div>
              <div class="col-md-6"><span class="text-muted">Survey Request No:</span> <br><b>DRN-2026-0026</b></div>
              <div class="col-md-6 mt-2"><span class="text-muted">Lease Area:</span> <br><b>3.85 Hectares</b></div>
              <div class="col-md-6 mt-2"><span class="text-muted">Status:</span> <br><span class="badge bg-success">Aerial Photogrammetry &amp; 3D Model Verified</span></div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            {{-- Team Preview --}}
            <div class="col-md-6">
              <div class="p-3 border rounded bg-white h-100">
                <h6 class="fw-bold text-navy mb-2"><i class="bi bi-people-fill text-primary me-1"></i> Drone Flight &amp; GIS Crew</h6>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span><strong>S. Karthik</strong></span>
                  <span class="badge bg-light text-dark border">DGCA Pilot (RPC #8821)</span>
                </div>
                <div class="d-flex justify-content-between py-1 small">
                  <span><strong>M. Vignesh</strong></span>
                  <span class="badge bg-light text-dark border">Photogrammetry Analyst</span>
                </div>
              </div>
            </div>

            {{-- Financial Preview --}}
            <div class="col-md-6">
              <div class="p-3 border rounded bg-white h-100">
                <h6 class="fw-bold text-navy mb-2"><i class="bi bi-receipt text-success me-1"></i> Financial Summary</h6>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Volumetric Survey Fee:</span>
                  <strong>₹ 50,000.00</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Paid Advance:</span>
                  <strong class="text-success">₹ 30,000.00</strong>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom small">
                  <span class="text-muted">Pending Balance:</span>
                  <strong class="text-danger">₹ 20,000.00</strong>
                </div>
                <div class="d-flex justify-content-between pt-2 small">
                  <span class="text-muted">Settlement Status:</span>
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Partial</span>
                </div>
              </div>
            </div>
          </div>

          <div class="card-panel mb-0" style="background:var(--green-soft);border:none">
            <i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Please confirm to finalize the Drone Survey process and synchronize with customer dossier.</span>
          </div>
        @endif

        {{-- Checklist Items for Current Step --}}
        @if(isset($steps[$step - 1][1]))
          <div class="mt-4">
            @foreach($steps[$step - 1][1] as $item)
              <div class="checklist-row">
                <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
                <div class="flex-grow-1">
                  <div class="ci-name">{{ $loop->iteration }}. {{ $item }}</div>
                  <div class="ci-meta">{{ $step < 8 ? 'To be completed' : 'Completed' }}</div>
                </div>
                <span class="badge-status {{ $step < 8 ? 'pending' : 'verified' }}">{{ $step < 8 ? 'Pending' : 'Completed' }}</span>
              </div>
            @endforeach
          </div>
        @endif

        {{-- WIZARD NAVIGATION ACTIONS --}}
        <div class="wizard-actions">
          <a href="{{ $step === 1 ? route('drone-survey.index') : route('drone-survey.step', $step - 1) }}" class="btn btn-outline-navy btn-sm">
            <i class="bi bi-arrow-left"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
          </a>
          @can('drone.create')
            <a href="{{ $step === 8 ? route('drone-survey.index') : route('drone-survey.step', $step + 1) }}" class="btn {{ $step === 8 ? 'btn-green' : 'btn-navy' }} px-4">
              {{ $step === 8 ? 'Finish Process' : 'Save & Continue' }} <i class="bi bi-arrow-right"></i>
            </a>
          @endcan
        </div>
      </div>
    </div>
  </div>
</div>

{{-- JAVASCRIPT FOR DYNAMIC HANDLERS & REAL-TIME PAYMENT MATH --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Drone Step 3: Upload & View handlers
  document.addEventListener('click', function(e) {
    const uploadBtn = e.target.closest('.btn-upload-drone-item');
    if (uploadBtn) {
      const row = uploadBtn.closest('.checklist-row');
      const input = row.querySelector('.drone-file-input');
      if (input) input.click();
    }
  });

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('drone-file-input')) {
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

          let viewBtn = slot.querySelector('.btn-view-drone-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-2 btn-view-drone-item me-1';
            viewBtn.style.fontSize = '.72rem';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.title = 'View document in separate page';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-drone-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-drone-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2 btn-upload-drone-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        updateDroneDocProgress();
      }
    }
  });

  function updateDroneDocProgress() {
    const total = document.querySelectorAll('#drone_checklist_container .checklist-row').length;
    const uploaded = document.querySelectorAll('#drone_checklist_container .checklist-row.up').length;
    const counter = document.getElementById('drone_doc_counter');
    const bar = document.getElementById('drone_progress_bar');
    if (counter) counter.textContent = `${uploaded} / ${total} uploaded`;
    if (bar && total > 0) bar.style.width = `${Math.round((uploaded / total) * 100)}%`;
  }

  // Drone Step 3: Add Document button
  const btnAddDroneDoc = document.getElementById('btn_add_drone_doc');
  if (btnAddDroneDoc) {
    btnAddDroneDoc.addEventListener('click', function() {
      const docName = prompt('Enter Drone Survey Deliverable Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('drone_checklist_container');
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
            <div class="ci-meta text-muted" style="font-size:11px;">Custom volumetric deliverable &middot; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <span class="badge-status pending">Optional</span>
          <input type="file" class="d-none drone-file-input" accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dxf">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2 btn-upload-drone-item" style="font-size:.72rem;">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updateDroneDocProgress();
    });
  }

  // Handlers dynamic add/remove
  const btnAdd = document.getElementById('btn_add_drone_handler');
  const tbody = document.getElementById('drone_handlers_tbody');

  function reindexDroneRows() {
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
      const delBtn = row.querySelector('.btn-remove-drone-handler');
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
        <td><input type="text" name="handlers[${count}][role]" class="form-control form-control-sm" placeholder="e.g. Flight Observer" required></td>
        <td><input type="text" name="handlers[${count}][notes]" class="form-control form-control-sm" placeholder="e.g. Battery management & safety"></td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
      reindexDroneRows();
    });

    tbody.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-remove-drone-handler');
      if (btn && !btn.classList.contains('disabled')) {
        const tr = btn.closest('tr');
        if (tr) {
          tr.remove();
          reindexDroneRows();
        }
      }
    });
  }

  // Payment live calculation
  const fVal = document.getElementById('field_drone_val');
  const fPaid = document.getElementById('field_drone_paid');
  const fPending = document.getElementById('field_drone_pending');
  const fStatus = document.getElementById('field_drone_status');
  const dVal = document.getElementById('disp_drone_val');
  const dPaid = document.getElementById('disp_drone_paid');
  const dPending = document.getElementById('disp_drone_pending');

  function fmtINR(val) {
    return '₹ ' + (Number(val) || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  function recalcDronePayment() {
    if (!fVal || !fPaid) return;
    const val = parseFloat(fVal.value) || 0;
    const paid = parseFloat(fPaid.value) || 0;
    const pending = Math.max(0, val - paid);

    if (fPending) {
      fPending.value = pending.toFixed(2);
      if (pending === 0) {
        fPending.classList.remove('text-danger');
        fPending.classList.add('text-success');
      } else {
        fPending.classList.remove('text-success');
        fPending.classList.add('text-danger');
      }
    }
    if (dVal) dVal.textContent = fmtINR(val);
    if (dPaid) dPaid.textContent = fmtINR(paid);
    if (dPending) {
      dPending.textContent = fmtINR(pending);
      if (pending === 0) {
        dPending.classList.remove('text-danger');
        dPending.classList.add('text-success');
      } else {
        dPending.classList.remove('text-success');
        dPending.classList.add('text-danger');
      }
    }

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
    fVal.addEventListener('input', recalcDronePayment);
    fPaid.addEventListener('input', recalcDronePayment);
  }

  // Step 1: Customer Unique ID Lookup & Instant Autofill
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const feedbackBox = document.getElementById('mimas_feedback_box');

  function applyDroneCustomerAutofill(c) {
    if (!c) return;
    const fApplicant = document.getElementById('field_drone_applicant');
    const fArea = document.getElementById('field_drone_area');
    const fLoc = document.getElementById('field_drone_location');

    if (fApplicant) {
      fApplicant.value = c.company_name ? (c.company_name + ' (' + c.customer_name + ')') : c.customer_name;
    }
    if (fArea) {
      fArea.value = (c.area || '3.85') + ' Hectares';
    }
    if (fLoc) {
      fLoc.value = (c.district_name || 'Concession Site') + (c.address ? ' / ' + c.address : '');
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

  function performDroneMimasLookup() {
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
          applyDroneCustomerAutofill(res.data);
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

  if (btnLookupMimas) btnLookupMimas.addEventListener('click', performDroneMimasLookup);
  if (mimasSearchInput) {
    mimasSearchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performDroneMimasLookup();
      }
    });
    mimasSearchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performDroneMimasLookup();
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
