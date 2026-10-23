@extends('layouts.app')
@section('title', 'Drone Survey Workflow | DGCA Photogrammetric Dossier')
@section('main_content')

@php
$labels = [
  'Survey Request',
  'Data Acquisition',
  'Processing & Upload',
  'Handling Team',
  'Payment',
  'Review & Submit'
];
$stepTitles = [
  1 => ['Survey Request & Concession Details', 'Enter applicant identity, quarry coordinates, and statutory requisition info.'],
  2 => ['Drone Flight & Aerial Acquisition', 'Configure DGCA flight operation date, drone hardware specs, and pilot credentials.'],
  3 => ['Data Processing & Deliverables', 'Upload applicant statutory deeds, flight logs, 3D point cloud, and volumetric report.'],
  4 => ['Project Handling Team', 'Assign DGCA certified drone pilots, GIS analysts, and photogrammetry team members.'],
  5 => ['Payment Details & Billing Ledger', 'Record quoted volumetric survey value, advance deposits, and settlement status.'],
  6 => ['Application Scrutiny & Final Review', 'Verify all mission flight data, deliverables registry, crew, and financial ledger.']
];
@endphp

<div class="content-body default-height">
  <div class="container-fluid py-3">

    <!-- UNIQUE SCOPED CONTAINER FOR DRONE SURVEY WIZARD (Strict Lease Application Parity) -->
    <div id="gtms-drone-wizard" class="wizard-wrap">

      <!-- ========================================================= -->
      <!-- 6-STEP PROGRESS BAR (HARMONIZED WITH LEASE APPLICATION)   -->
      <!-- ========================================================= -->
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

      <!-- ========================================================= -->
      <!-- MAIN WIZARD CARD                                          -->
      <!-- ========================================================= -->
      <div class="card s-main-card wizard-card">

        <!-- HEADER BANNER -->
        <div class="s-header-block mb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1">
            <div class="wc-eyebrow s-eyebrow">
              <i class="bi bi-shield-check me-1"></i> Step {{ $step }} of 6 &bull; {{ $stepTitles[$step][0] }}
            </div>
            <span class="s-time-tag">
              <i class="bi bi-clock-history me-1"></i> Est. ~1.5 mins
            </span>
          </div>
          <h4 class="s-title">{{ $stepTitles[$step][0] }}</h4>
          <div class="wc-sub s-subtitle">{{ $stepTitles[$step][1] }}</div>
        </div>

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Submission Notice:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form method="POST" action="{{ $step === 6 ? route('drone-survey.store') : route('drone-survey.saveStep', $step) }}" id="droneWizardForm" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="customer_id" id="drone_customer_id" value="{{ $draft['customer_id'] ?? ($customers->first()?->id ?? '') }}">
          <input type="hidden" name="save_draft_exit" id="save_draft_exit" value="0">

        <!-- ========================================================= -->
        <!-- STEP 1: SURVEY REQUEST & APPLICANT IDENTITY               -->
        <!-- ========================================================= -->
        @if($step === 1)
          <!-- CUSTOMER UNIQUE ID / MIMAS LOOKUP CARD -->
          <div class="s-lookup-card mb-3.5 p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-1.5">
              <label class="s-lookup-label fw-bold mb-0" for="mimas_search_input">
                <i class="bi bi-fingerprint me-1.5"></i> Customer Unique ID Lookup
              </label>
              <span class="badge s-lookup-badge">
                <i class="bi bi-lightning-charge-fill me-1"></i> Instant Autofill
              </span>
            </div>
            <p class="s-lookup-desc mt-2 mb-2.5">
              Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.
            </p>

            <div class="input-group s-input-group s-lookup-input-group">
              <span class="input-group-text s-addon s-lookup-addon"><i class="bi bi-search"></i></span>
              <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold s-input s-lookup-field" 
                     placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
              <button class="btn s-btn-fetch px-3 fw-bold" type="button" id="btn_lookup_mimas">
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

          <!-- SECTION 1: APPLICANT IDENTITY -->
          <div class="s-section-card mb-3">
            <div class="s-section-header">
              <i class="bi bi-person-badge text-primary me-2"></i>
              <span class="s-section-title">Applicant &amp; Request Identification</span>
            </div>

            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-6">
                <label class="form-label s-label">
                  Drone Survey Request No. <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-hash"></i></span>
                  <input class="form-control s-input font-monospace fw-bold bg-light" name="survey_no" readonly value="{{ $draft['survey_no'] ?? ('DRN-' . date('Y') . '-0026') }}">
                </div>
                <div class="form-text-hint s-hint">Auto-generated sequential tracking reference.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Client / Applicant Entity <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-building"></i></span>
                  <input class="form-control s-input auto-filled-field fw-semibold" id="field_drone_applicant" name="applicant_name" placeholder="Applicant / Company Name" value="{{ $draft['applicant_name'] ?? 'Kaveri Granites Pvt Ltd' }}" required>
                </div>
                <div class="form-text-hint s-hint">Official concession holder name as per revenue records.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Primary Phone Number <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-telephone"></i></span>
                  <input type="text" class="form-control s-input auto-filled-field" id="field_drone_primary_phone" name="primary_phone" placeholder="10-digit primary phone number" value="{{ $draft['primary_phone'] ?? ($customers->first()?->mobile_num ?? '9840123456') }}" required maxlength="15">
                </div>
                <div class="form-text-hint s-hint">Primary contact for DGCA site clearance updates.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Secondary Phone Number <span class="s-opt-tag ms-1">(Optional)</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-telephone-plus"></i></span>
                  <input type="text" class="form-control s-input auto-filled-field" id="field_drone_secondary_phone" name="secondary_phone" placeholder="10-digit secondary phone number" value="{{ $draft['secondary_phone'] ?? ($customers->first()?->secondary_mobile_num ?? '') }}" maxlength="15">
                </div>
                <div class="form-text-hint s-hint">Quarry site manager or liaison representative phone.</div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: QUARRY CONCESSION SITE & BOUNDARY EXTENT -->
          <div class="s-section-card mb-3">
            <div class="s-section-header">
              <i class="bi bi-geo-alt-fill text-info me-2"></i>
              <span class="s-section-title">Quarry Concession Site &amp; Boundary Extent</span>
            </div>

            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-6">
                <label class="form-label s-label">
                  Lease Area (Hectares) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-bounding-box-circles"></i></span>
                  <input class="form-control s-input auto-filled-field" id="field_drone_area" name="lease_area" placeholder="e.g. 3.85 or 14.20.20" value="{{ $draft['lease_area'] ?? '3.85 Hectares' }}" required>
                </div>
                <div class="form-text-hint s-hint">Supports standard decimal (e.g. 3.85) or Patta revenue format.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Location / Quarry Site <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-geo"></i></span>
                  <input class="form-control s-input auto-filled-field" id="field_drone_location" name="location" placeholder="District / Taluk / Village" value="{{ $draft['location'] ?? 'Salem / Semmandapatti' }}" required>
                </div>
                <div class="form-text-hint s-hint">Site coordinates and cadastral boundary region.</div>
              </div>
            </div>

            <input type="hidden" name="lease_application_id" value="{{ $draft['lease_application_id'] ?? '' }}">
            <input type="hidden" name="mining_application_id" value="{{ $draft['mining_application_id'] ?? '' }}">
          </div>

          <!-- SYSTEM NOTICE BANNER -->
          <div class="s-notice-card mt-3 mb-0 p-3">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-info-circle-fill s-notice-icon"></i>
              <div class="s-notice-text">
                An official <strong>photogrammetric survey mission</strong> will be registered under this dossier. Unmanned aerial flight logs, RTK base station benchmarks, and 3D terrain reconstruction synchronize directly with the central mineral ledger.
              </div>
            </div>
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- STEP 2: DATA ACQUISITION & DGCA FLIGHT OPERATIONS         -->
        <!-- ========================================================= -->
        @if($step === 2)
          <!-- SUMMARY BANNER -->
          <div class="s-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s-banner-icon-badge">
                  <i class="bi bi-airplane-engines-fill"></i>
                </div>
                <div>
                  <div class="s-banner-title fw-bold">DGCA Flight Mission Parameters &amp; Aerial Platform</div>
                  <div class="s-banner-desc">Configure unmanned aircraft specs, pilot certification, and photogrammetric resolution geometry.</div>
                </div>
              </div>
              <span class="badge s-team-pill">
                <i class="bi bi-shield-check me-1.5"></i>DGCA Compliant
              </span>
            </div>
          </div>

          <!-- SECTION 1: HARDWARE & FLIGHT MISSION -->
          <div class="s-section-card mb-3">
            <div class="s-section-header">
              <i class="bi bi-send text-primary me-2"></i>
              <span class="s-section-title">Aerial Hardware &amp; Mission Schedule</span>
            </div>

            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-4">
                <label class="form-label s-label">
                  Flight Operation Date <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-calendar3"></i></span>
                  <input class="form-control s-input" type="date" name="flight_date" value="{{ $draft['flight_date'] ?? date('Y-m-d') }}" required>
                </div>
                <div class="form-text-hint s-hint">Scheduled or actual field flight execution date.</div>
              </div>

              <div class="col-md-4">
                <label class="form-label s-label">
                  Drone Platform &amp; Model <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-airplane-engines"></i></span>
                  <input class="form-control s-input fw-semibold" name="drone_model" placeholder="e.g. DJI Matrice 300 RTK" value="{{ $draft['drone_model'] ?? 'DJI Matrice 300 RTK' }}" required>
                </div>
                <div class="form-text-hint s-hint">RTK-enabled multirotor or fixed-wing platform.</div>
              </div>

              <div class="col-md-4">
                <label class="form-label s-label">
                  Drone Unique ID (UIN No.) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-upc-scan"></i></span>
                  <input class="form-control s-input font-monospace" name="drone_uin_no" placeholder="e.g. UIN-TN-DRN-2026-9901" value="{{ $draft['drone_uin_no'] ?? 'UIN-TN-DRN-2026-9901' }}" required>
                </div>
                <div class="form-text-hint s-hint">DGCA DigitalSky registered UIN identification.</div>
              </div>
            </div>
          </div>

          <!-- SECTION 2: PILOT CREDENTIALS & MISSION GEOMETRY -->
          <div class="s-section-card mb-3">
            <div class="s-section-header">
              <i class="bi bi-person-badge-fill text-info me-2"></i>
              <span class="s-section-title">DGCA Pilot Certification &amp; Photogrammetric Geometry</span>
            </div>

            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-6">
                <label class="form-label s-label">
                  DGCA Certified Pilot Name <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-person-check"></i></span>
                  <input class="form-control s-input" name="drone_pilot_name" placeholder="e.g. S. Karthik" value="{{ $draft['drone_pilot_name'] ?? 'S. Karthik' }}" required>
                </div>
                <div class="form-text-hint s-hint">Designated Remote Pilot in Command (RPIC).</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Pilot RPC License No. <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-award"></i></span>
                  <input class="form-control s-input font-monospace" name="pilot_rpc_no" placeholder="e.g. RPC #DRN-TN-8821" value="{{ $draft['pilot_rpc_no'] ?? 'RPC #DRN-TN-8821' }}" required>
                </div>
                <div class="form-text-hint s-hint">Remote Pilot Certificate verified under DGCA Rules.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Flight Altitude (Meters AGL) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-arrow-up-right-square"></i></span>
                  <input type="number" step="1" min="10" max="120" class="form-control s-input fw-bold" name="altitude_meters" placeholder="120" value="{{ $draft['altitude_meters'] ?? '120' }}" required>
                </div>
                <div class="form-text-hint s-hint">Above Ground Level (DGCA Green Zone maximum: 120 meters).</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Ground Sampling Distance (GSD) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-aspect-ratio"></i></span>
                  <input type="number" step="0.1" min="0.5" class="form-control s-input fw-bold" name="gsd_cm_px" placeholder="2.5" value="{{ $draft['gsd_cm_px'] ?? '2.5' }}" required>
                </div>
                <div class="form-text-hint s-hint">Pixel resolution in cm/px for sub-centimeter volumetric precision.</div>
              </div>
            </div>
          </div>

          <!-- NOTICE BOX -->
          <div class="s-notice-card mt-3 mb-0 p-3">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-camera-video-fill s-notice-icon"></i>
              <div class="s-notice-text">
                <strong>Mission Flight Overlap Standard:</strong> Autonomous grid execution maintains 75% forward and 70% lateral photographic overlap with RTK base station corrections for high-precision 3D point cloud generation.
              </div>
            </div>
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- STEP 3: DATA PROCESSING & DOCUMENTS UPLOAD                -->
        <!-- ========================================================= -->
        @if($step === 3)
          <input type="hidden" name="survey_status" value="completed">

          <!-- COMPLETION BANNER -->
          <div class="s-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s-banner-icon-badge" style="background:#dcfce7; color:#10b981;">
                  <i class="bi bi-folder-check"></i>
                </div>
                <div>
                  <div class="s-banner-title fw-bold">Process Drone Data &amp; Upload Deliverables</div>
                  <div class="s-banner-desc">Upload applicant requisition deeds, flight mission logs, orthomosaic map, point clouds, contours, and volumetric report.</div>
                </div>
              </div>
              <div class="text-end">
                <span class="text-muted small fw-semibold text-uppercase">Completion:</span>
                <span class="fw-bold text-navy ms-1" id="drone_doc_counter" style="font-size:0.85rem;">0 / 10 uploaded</span>
                <div class="progress mt-1.5" style="height: 7px; width: 160px; border-radius: 999px;">
                  <div class="progress-bar bg-success" id="drone_progress_bar" role="progressbar" style="width: 0%;"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- VOLUMETRIC EXCAVATION PANEL -->
          <div class="s-section-card mb-3">
            <div class="s-section-header d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <i class="bi bi-box-seam text-primary me-2"></i>
                <span class="s-section-title">Volumetric Earthwork &amp; Stockpile Extraction</span>
              </div>
              <span class="badge s-multi-badge">
                <i class="bi bi-check-circle me-1"></i> Photogrammetry Computed
              </span>
            </div>

            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-6">
                <label class="form-label s-label">
                  Extracted Volume (CBM) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-calculator"></i></span>
                  <input type="number" step="0.01" min="0" class="form-control s-input fw-bold" name="extracted_volume_cbm" id="field_extracted_volume" placeholder="e.g. 18450.00" value="{{ $draft['extracted_volume_cbm'] ?? '18450.00' }}" required>
                </div>
                <div class="form-text-hint s-hint">Total cut / fill excavation volume calculated from DSM elevation surfaces.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Photogrammetry Processing Engine <span class="s-opt-tag ms-1">(Optional)</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-cpu"></i></span>
                  <input type="text" class="form-control s-input" name="deliverable_files_path" placeholder="e.g. Pix4Dmapper / DJI Terra Engine" value="{{ $draft['deliverable_files_path'] ?? 'Pix4Dmapper & Cloud Photogrammetry Suite' }}">
                </div>
                <div class="form-text-hint s-hint">Photogrammetric bundle block adjustment software utilized.</div>
              </div>
            </div>
          </div>

          @php
            $statutoryDocs = [
              ['name' => '1. Request Letter', 'meta' => 'Drone survey requisition letter from applicant / quarry owner', 'mandatory' => true, 'tag' => 'Requisition'],
              ['name' => '2. Lease Area Details & Combined FMB Sketch', 'meta' => 'FMB sketch, village cadastral map, and boundary coordinates', 'mandatory' => true, 'tag' => 'Requisition'],
              ['name' => '3. Approved Mining Plan (MP Copy)', 'meta' => 'Approved mining plan copy, progressive mine closure plan & grant order', 'mandatory' => true, 'tag' => 'Statutory'],
              ['name' => '4. Environmental Clearance (EC) Copy', 'meta' => 'State / MoEFCC Environmental Clearance certificate', 'mandatory' => true, 'tag' => 'Statutory'],
            ];

            $deliverableDocs = [
              ['name' => '5. Drone Mission Flight Log & DGCA RPC Certificate', 'meta' => 'DGCA Pilot license, flight path log, and mission approval', 'mandatory' => true, 'tag' => 'Flight Log'],
              ['name' => '6. Ground Control Points (GCP) Coordinates Sheet', 'meta' => 'Surveyed ground benchmark and RTK correction points CSV', 'mandatory' => true, 'tag' => 'GCP RTK'],
              ['name' => '7. High-Resolution Orthomosaic Map (GeoTIFF / JPG)', 'meta' => 'Georeferenced orthophoto mosaic of quarry lease boundary', 'mandatory' => true, 'tag' => 'Orthomosaic'],
              ['name' => '8. Digital Surface Model (DSM) / Digital Terrain Model (DTM)', 'meta' => '3D elevation terrain model and height contour map', 'mandatory' => true, 'tag' => 'Terrain DSM'],
              ['name' => '9. 3D Dense Point Cloud & Surface Mesh File', 'meta' => 'Dense point cloud classification (LAS / PLY / OBJ)', 'mandatory' => true, 'tag' => 'Point Cloud'],
              ['name' => '10. Volumetric Excavation & Stockpile Calculation Report', 'meta' => 'Cut / fill earthwork volume and mineral reserve extraction report', 'mandatory' => true, 'tag' => 'Volumetric'],
            ];
          @endphp

          <div class="folder-checklist-box" id="drone_checklist_container">
            <!-- FOLDER 01: STATUTORY REQUISITION DOCUMENTS -->
            <div class="s-section-header d-flex align-items-center justify-content-between mt-3 mb-2 px-3 py-2 rounded-3" style="background:#f0f7ff; border:1px solid #bfdbfe;">
              <div class="fw-bold text-navy small">
                <i class="fa fa-folder-open me-2 text-primary"></i>FOLDER 01 &middot; STATUTORY REQUISITION DOCUMENTS (FROM APPLICANT)
              </div>
              <span class="badge bg-primary text-white" style="font-size:10px;">4 Documents</span>
            </div>

            @foreach($statutoryDocs as $dIdx => $dItem)
              <div class="checklist-row py-2.5 px-3 d-flex align-items-center justify-content-between border-bottom bg-white rounded-2 mb-1" data-drone-row="{{ $dIdx }}" data-mandatory="{{ $dItem['mandatory'] ? '1' : '0' }}">
                <div class="d-flex align-items-center gap-2.5 flex-grow-1 text-truncate">
                  <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background:#eff6ff; color:#2563eb;">
                    <i class="bi bi-file-earmark-text"></i>
                  </div>
                  <div class="text-truncate">
                    <div class="ci-name fw-semibold small text-dark text-truncate">
                      <span class="badge bg-light text-primary border me-1" style="font-size:10px;">{{ $dItem['tag'] }}</span>
                      {{ $dItem['name'] }}
                    </div>
                    <div class="ci-meta text-muted" style="font-size:11px;">{{ $dItem['meta'] }} &middot; Not uploaded</div>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                  <select class="form-select form-select-sm doc-req-select py-1 px-2 fw-bold text-center {{ $dItem['mandatory'] ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                          name="drone_doc_req[{{ $dIdx }}]" style="font-size:0.75rem; width:110px; border-radius:6px;">
                    <option value="mandatory" {{ $dItem['mandatory'] ? 'selected' : '' }}>Mandatory</option>
                    <option value="optional" {{ !$dItem['mandatory'] ? 'selected' : '' }}>Optional</option>
                  </select>
                  <input type="file" class="d-none drone-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dxf">
                  <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2.5 btn-upload-drone-item" style="font-size:.75rem;">
                    <i class="bi bi-upload me-1"></i>Upload
                  </button>
                </div>
              </div>
            @endforeach

            <!-- FOLDER 02: FLIGHT LOGS & VOLUMETRIC DELIVERABLES -->
            <div class="s-section-header d-flex align-items-center justify-content-between mt-4 mb-2 px-3 py-2 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <div class="fw-bold text-navy small">
                <i class="fa fa-layer-group me-2 text-info"></i>FOLDER 02 &middot; DRONE FLIGHT LOGS &amp; PHOTOGRAMMETRY DELIVERABLES
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:10px;">6 Deliverables</span>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold" id="btn_add_drone_doc" style="font-size:0.75rem;">
                  <i class="bi bi-plus-circle me-1"></i>+ Add Document
                </button>
              </div>
            </div>

            @foreach($deliverableDocs as $dIdxOffset => $dItem)
              @php $dIdx = $dIdxOffset + 4; @endphp
              <div class="checklist-row py-2.5 px-3 d-flex align-items-center justify-content-between border-bottom bg-white rounded-2 mb-1" data-drone-row="{{ $dIdx }}" data-mandatory="{{ $dItem['mandatory'] ? '1' : '0' }}">
                <div class="d-flex align-items-center gap-2.5 flex-grow-1 text-truncate">
                  <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background:#f1f5f9; color:#64748b;">
                    <i class="bi bi-file-earmark-binary"></i>
                  </div>
                  <div class="text-truncate">
                    <div class="ci-name fw-semibold small text-dark text-truncate">
                      <span class="badge bg-light text-primary border me-1" style="font-size:10px;">{{ $dItem['tag'] }}</span>
                      {{ $dItem['name'] }}
                    </div>
                    <div class="ci-meta text-muted" style="font-size:11px;">{{ $dItem['meta'] }} &middot; Not uploaded</div>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
                  <select class="form-select form-select-sm doc-req-select py-1 px-2 fw-bold text-center {{ $dItem['mandatory'] ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                          name="drone_doc_req[{{ $dIdx }}]" style="font-size:0.75rem; width:110px; border-radius:6px;">
                    <option value="mandatory" {{ $dItem['mandatory'] ? 'selected' : '' }}>Mandatory</option>
                    <option value="optional" {{ !$dItem['mandatory'] ? 'selected' : '' }}>Optional</option>
                  </select>
                  <input type="file" class="d-none drone-file-input" data-row-idx="{{ $dIdx }}" accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dxf">
                  <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2.5 btn-upload-drone-item" style="font-size:.75rem;">
                    <i class="bi bi-upload me-1"></i>Upload
                  </button>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- STEP 4: PROJECT HANDLING TEAM (EXACT LEASE STEP 6 PARITY) -->
        <!-- ========================================================= -->
        @if($step === 4)
          <!-- SUMMARY / TEAM READINESS BANNER -->
          <div class="s-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s-banner-icon-badge">
                  <i class="bi bi-people-fill"></i>
                </div>
                <div>
                  <div class="s-banner-title fw-bold">Statutory Flight Crew &amp; Photogrammetry Personnel Assignment</div>
                  <div class="s-banner-desc">Designate DGCA certified pilots, safety observers, and photogrammetry analysts responsible for this survey.</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge s-team-pill"><i class="bi bi-people-fill me-1"></i> Multi-User Allocation</span>
                <span class="badge s-count-pill" id="drone_handler_count_banner">2 Members Allocated</span>
              </div>
            </div>
          </div>

          <!-- TABLE CONTAINER CARD -->
          <div class="s-table-card mb-3">
            <div class="s-table-header-bar d-flex align-items-center justify-content-between px-3 py-2.5">
              <div class="d-flex align-items-center gap-2">
                <span class="s-table-title fw-bold">
                  <i class="bi bi-person-lines-fill me-1.5 text-primary"></i>Assigned Flight Personnel
                </span>
                <span class="badge s-badge-counter rounded-pill" id="drone_handler_count_badge">2 members</span>
              </div>
              <button type="button" class="btn s-btn-add-person" id="btn_add_drone_handler">
                <i class="bi bi-person-plus-fill me-1.5"></i>+ Add Person
              </button>
            </div>

            <div class="table-responsive">
              <table class="table s-table align-middle mb-0" id="drone_handlers_table">
                <thead>
                  <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th style="width: 28%;"><i class="bi bi-person me-1"></i>Person Name <span class="text-danger">*</span></th>
                    <th style="width: 28%;"><i class="bi bi-briefcase me-1"></i>Role / Designation <span class="text-danger">*</span></th>
                    <th><i class="bi bi-card-text me-1"></i>Notes &amp; Responsibilities</th>
                    <th style="width: 70px;" class="text-center"><i class="bi bi-gear me-1"></i>Action</th>
                  </tr>
                </thead>
                <tbody id="drone_handlers_tbody">
                  <tr>
                    <td class="text-center fw-bold row-num">1</td>
                    <td>
                      <div class="input-group s-input-group">
                        <span class="input-group-text s-addon"><i class="bi bi-person"></i></span>
                        <input type="text" name="handlers[0][person_name]" class="form-control s-input fw-semibold" value="{{ $draft['handlers'][0]['person_name'] ?? 'S. Karthik' }}" placeholder="e.g. S. Karthik" required>
                      </div>
                    </td>
                    <td>
                      <div class="input-group s-input-group">
                        <span class="input-group-text s-addon"><i class="bi bi-award"></i></span>
                        <input type="text" name="handlers[0][role]" class="form-control s-input" list="role_suggestions" value="{{ $draft['handlers'][0]['role'] ?? 'DGCA Certified Drone Pilot' }}" placeholder="e.g. Drone Pilot" required>
                      </div>
                    </td>
                    <td>
                      <input type="text" name="handlers[0][notes]" class="form-control s-input" style="border-radius:8px !important;" value="{{ $draft['handlers'][0]['notes'] ?? 'Autonomous grid flight execution & safety compliance' }}" placeholder="Responsibilities">
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler disabled" title="Primary pilot cannot be removed">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td class="text-center fw-bold row-num">2</td>
                    <td>
                      <div class="input-group s-input-group">
                        <span class="input-group-text s-addon"><i class="bi bi-person"></i></span>
                        <input type="text" name="handlers[1][person_name]" class="form-control s-input fw-semibold" value="{{ $draft['handlers'][1]['person_name'] ?? 'M. Vignesh' }}" placeholder="e.g. M. Vignesh">
                      </div>
                    </td>
                    <td>
                      <div class="input-group s-input-group">
                        <span class="input-group-text s-addon"><i class="bi bi-layers"></i></span>
                        <input type="text" name="handlers[1][role]" class="form-control s-input" list="role_suggestions" value="{{ $draft['handlers'][1]['role'] ?? 'Photogrammetry & 3D Analyst' }}" placeholder="e.g. GIS Analyst">
                      </div>
                    </td>
                    <td>
                      <input type="text" name="handlers[1][notes]" class="form-control s-input" style="border-radius:8px !important;" value="{{ $draft['handlers'][1]['notes'] ?? 'Image stitching, GCP georeferencing & cut/fill volume calculation' }}" placeholder="Responsibilities">
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler" title="Remove person">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Role Suggestions Datalist -->
            <datalist id="role_suggestions">
              <option value="DGCA Certified Drone Pilot"></option>
              <option value="Photogrammetry & 3D Analyst"></option>
              <option value="GIS & Mapping Specialist"></option>
              <option value="Ground Control Station (GCS) Operator"></option>
              <option value="Flight Safety & Airspace Observer"></option>
              <option value="Volumetric Calculation Specialist"></option>
              <option value="Field Demarcation Officer"></option>
            </datalist>
          </div>

          <!-- PRO TIP BANNER -->
          <div class="s-notice-card mt-3 mb-0 p-3">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-lightbulb-fill s-notice-icon text-warning"></i>
              <div class="s-notice-text">
                <strong>Pro Tip:</strong> You can allocate multiple specialists across different project functions. All designated personnel will be indexed on the official statutory certificate and final review dossier.
              </div>
            </div>
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- STEP 5: PAYMENT DETAILS & BILLING LEDGER (EXACT LEASE 7)  -->
        <!-- ========================================================= -->
        @if($step === 5)
          @php
            $pv = (float)($draft['product_value'] ?? 50000);
            $pa = (float)($draft['paid_amount'] ?? 30000);
            $pe = max(0, $pv - $pa);
            $st = $draft['payment_status'] ?? ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial'));
          @endphp

          <!-- SUMMARY / LEDGER READINESS BANNER -->
          <div class="s-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s-banner-icon-badge">
                  <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div>
                  <div class="s-banner-title fw-bold">Statutory Drone Ledger &amp; Fee Settlement</div>
                  <div class="s-banner-desc">Real-time fee computation, flight advance tracking, and transaction status recording.</div>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge s-banner-status-pill" id="banner_status_pill">
                  @if($st === 'paid')
                    <i class="bi bi-check-circle-fill me-1.5"></i> Fully Settled
                  @elseif($st === 'partial')
                    <i class="bi bi-clock-history me-1.5"></i> Partial Advance
                  @else
                    <i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment
                  @endif
                </span>
              </div>
            </div>
          </div>

          <!-- REAL-TIME FINANCIAL 3-BENTO KPI METRIC CARDS -->
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <div class="s-kpi-card s-kpi-card-contract">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label">Volumetric Survey Value</span>
                  <div class="s-kpi-icon-badge s-kpi-icon-contract"><i class="bi bi-receipt-cutoff"></i></div>
                </div>
                <div class="s-kpi-value text-navy" id="disp_drone_val">₹ {{ number_format($pv, 2) }}</div>
                <div class="s-kpi-subtext">Flight &amp; 3D photogrammetry quotation</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="s-kpi-card s-kpi-card-paid">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label text-success">Advance / Paid Amount</span>
                  <div class="s-kpi-icon-badge s-kpi-icon-paid"><i class="bi bi-check-circle-fill"></i></div>
                </div>
                <div class="s-kpi-value text-success" id="disp_drone_paid">₹ {{ number_format($pa, 2) }}</div>
                <div class="s-kpi-subtext">Deposit received from client</div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="s-kpi-card s-kpi-card-pending">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label text-warning-emphasis">Pending Balance Due</span>
                  <div class="s-kpi-icon-badge s-kpi-icon-pending"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <div class="s-kpi-value text-danger" id="disp_drone_pending">₹ {{ number_format($pe, 2) }}</div>
                <div class="s-kpi-subtext">Auto-calculated outstanding balance</div>
              </div>
            </div>
          </div>

          <!-- INPUT FORM CARD -->
          <div class="s-section-card mb-3">
            <div class="row gx-3 gy-3 s-form-row">
              <div class="col-md-4">
                <label class="form-label s-label">
                  Product / Service Fee (₹) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon fw-bold">₹</span>
                  <input type="number" step="0.01" min="0" name="product_value" id="field_drone_val" class="form-control s-input fw-bold" placeholder="0.00" value="{{ $pv }}" required>
                </div>
                <div class="form-text-hint s-hint">Total quoted fee for aerial mission and photogrammetry.</div>
              </div>

              <div class="col-md-4">
                <label class="form-label s-label">
                  Advance / Paid Amount (₹) <span class="text-danger ms-1">*</span>
                </label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon fw-bold text-success">₹</span>
                  <input type="number" step="0.01" min="0" name="paid_amount" id="field_drone_paid" class="form-control s-input fw-bold text-success" placeholder="0.00" value="{{ $pa }}" required>
                </div>
                <div class="form-text-hint s-hint">Amount deposited via bank transfer or cheque.</div>
              </div>

              <div class="col-md-4">
                <label class="form-label s-label">Pending Balance Due (₹)</label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon text-muted">₹</span>
                  <input type="number" step="0.01" name="pending_amount" id="field_drone_pending" class="form-control s-input bg-light fw-bold text-danger" placeholder="0.00" readonly value="{{ number_format($pe, 2, '.', '') }}">
                </div>
                <div class="form-text-hint s-hint">Calculated automatically (Value &minus; Paid).</div>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">
                  Settlement Status <span class="text-danger ms-1">*</span>
                </label>
                <select class="form-select s-input" style="border-radius:8px !important;" name="payment_status" id="field_drone_status">
                  <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>Pending (Full Balance Due)</option>
                  <option value="partial" {{ $st === 'partial' ? 'selected' : '' }}>Partial Advance Received</option>
                  <option value="paid" {{ $st === 'paid' ? 'selected' : '' }}>Paid (Fully Settled)</option>
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label s-label">Transaction Reference / Receipt Notes</label>
                <div class="input-group s-input-group">
                  <span class="input-group-text s-addon"><i class="bi bi-receipt"></i></span>
                  <input type="text" class="form-control s-input" name="payment_notes" placeholder="e.g. Bank transfer ref / Cheque details" value="{{ $draft['payment_notes'] ?? 'Cheque #881902 cleared - advance paid for flight and volumetric modeling' }}">
                </div>
              </div>
            </div>
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- STEP 6: PREVIEW & FINAL SCRUTINY (EXACT LEASE STEP 8)     -->
        <!-- ========================================================= -->
        @if($step === 6)
          @php
            $pv = (float)($draft['product_value'] ?? 50000);
            $pa = (float)($draft['paid_amount'] ?? 30000);
            $pe = max(0, $pv - $pa);
            $st = $draft['payment_status'] ?? ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial'));
            $customerObj = !empty($draft['customer_id']) ? $customers->firstWhere('id', $draft['customer_id']) : $customers->first();
            $handlers = $draft['handlers'] ?? [
              ['person_name' => 'S. Karthik', 'role' => 'DGCA Certified Drone Pilot', 'notes' => 'Autonomous grid flight execution & safety compliance'],
              ['person_name' => 'M. Vignesh', 'role' => 'Photogrammetry & 3D Analyst', 'notes' => 'Image stitching, GCP georeferencing & cut/fill volume calculation']
            ];
            $volCbm = (float)($draft['extracted_volume_cbm'] ?? 18450);
          @endphp

          <!-- EXECUTIVE AUDIT & READINESS BANNER -->
          <div class="s-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s-banner-icon-badge" style="background:#dcfce7; color:#10b981; width:44px; height:44px; border-radius:12px; font-size:20px; display:inline-flex; align-items:center; justify-content:center;">
                  <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                  <div class="s-banner-title fw-bold" style="font-size:1.05rem;">Statutory Drone Photogrammetric Dossier Audit &amp; Final Scrutiny</div>
                  <div class="s-banner-desc text-muted" style="font-size:0.83rem;">Cross-examine DGCA flight parameters, photogrammetric volumetric calculations, flight crew roster, and financial settlement ledger before submission.</div>
                </div>
              </div>
              <span class="badge s-banner-status-pill px-3 py-2 fw-semibold" style="background:#10b981; font-size:0.8rem;">
                <i class="bi bi-shield-lock-fill me-1.5"></i>Ready for Final Submission
              </span>
            </div>
          </div>

          <!-- 4-BENTO EXECUTIVE METRIC OVERVIEW -->
          <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
              <div class="s-kpi-card h-100" style="border-left:4px solid #3b82f6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label">Applicant Entity</span>
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:rgba(59,130,246,0.12); color:#3b82f6;">
                    <i class="bi bi-building" style="font-size:13px;"></i>
                  </div>
                </div>
                <div class="s-kpi-value text-navy text-truncate" title="{{ $draft['applicant_name'] ?? ($customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd')) }}">
                  {{ $draft['applicant_name'] ?? ($customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd')) }}
                </div>
                <div class="s-kpi-subtext text-truncate">
                  <i class="bi bi-geo-alt me-1"></i>{{ $draft['location'] ?? 'Salem / Semmandapatti' }}
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="s-kpi-card h-100" style="border-left:4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label">Extracted Volume</span>
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:rgba(16,185,129,0.12); color:#10b981;">
                    <i class="bi bi-box-seam" style="font-size:13px;"></i>
                  </div>
                </div>
                <div class="s-kpi-value text-success">{{ number_format($volCbm, 2) }} CBM</div>
                <div class="s-kpi-subtext">
                  <i class="bi bi-pin-map me-1"></i>Lease Area: {{ $draft['lease_area'] ?? '3.85 Ha' }}
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="s-kpi-card h-100" style="border-left:4px solid {{ $pe > 0 ? '#ef4444' : '#10b981' }} !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label">Financial Settlement</span>
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:rgba(239,68,68,0.12); color:#ef4444;">
                    <i class="bi bi-cash-stack" style="font-size:13px;"></i>
                  </div>
                </div>
                <div class="s-kpi-value {{ $pe > 0 ? 'text-danger' : 'text-success' }}">₹ {{ number_format($pe, 2) }}</div>
                <div class="s-kpi-subtext">
                  Total Fee: ₹ {{ number_format($pv, 2) }} &bull; {{ ucfirst($st) }}
                </div>
              </div>
            </div>
            <div class="col-sm-6 col-lg-3">
              <div class="s-kpi-card h-100" style="border-left:4px solid #8b5cf6 !important;">
                <div class="d-flex align-items-center justify-content-between mb-1">
                  <span class="s-kpi-label">Flight Hardware &amp; UAV</span>
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:rgba(139,92,246,0.12); color:#8b5cf6;">
                    <i class="bi bi-airplane-engines" style="font-size:13px;"></i>
                  </div>
                </div>
                <div class="s-kpi-value text-navy text-truncate" title="{{ $draft['drone_model'] ?? 'DJI Matrice 300 RTK' }}">
                  {{ $draft['drone_model'] ?? 'DJI Matrice 300 RTK' }}
                </div>
                <div class="s-kpi-subtext">
                  <i class="bi bi-arrow-up-right me-1"></i>{{ $draft['altitude_meters'] ?? 120 }}m AGL &bull; {{ $draft['gsd_cm_px'] ?? 2.5 }} cm/px
                </div>
              </div>
            </div>
          </div>

          <!-- SCRUTINY PANELS WITH QUICK-JUMP [EDIT STEP X] BUTTONS -->
          <div class="d-flex flex-column gap-3 mb-4">

            <!-- PANEL 1: APPLICANT IDENTITY & CONCESSION -->
            <div class="s-section-panel">
              <div class="s-panel-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="s-banner-icon-badge" style="width:34px; height:34px; font-size:16px; background:#eff6ff; color:#2563eb; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-person-badge"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-navy mb-0" style="font-size:14px;">1. Applicant Identity &amp; Quarry Extent</h6>
                    <small class="text-muted" style="font-size:11px;">Client enterprise profile, concession parameters, and contact channels</small>
                  </div>
                </div>
                <a href="{{ route('drone-survey.step', 1) }}" class="btn btn-sm s-btn-edit">
                  <i class="bi bi-pencil-square me-1"></i>Edit Step 1
                </a>
              </div>
              <div class="p-3">
                <div class="row g-2.5">
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Request No</span>
                      <span class="s-tile-value font-monospace text-primary">{{ $draft['survey_no'] ?? 'DRN-2026-0026' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Applicant Entity</span>
                      <span class="s-tile-value text-truncate" title="{{ $draft['applicant_name'] ?? ($customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd')) }}">
                        {{ $draft['applicant_name'] ?? ($customerObj?->company_name ?: ($customerObj?->customer_name ?: 'Kaveri Granites Pvt Ltd')) }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Customer / MIMAS ID</span>
                      <span class="s-tile-value font-monospace">{{ $customerObj?->mimas_no ?: 'TN-MMS-SLM-0088' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Primary Contact Phone</span>
                      <span class="s-tile-value">{{ $draft['primary_phone'] ?? ($customerObj?->mobile_num ?? '9840123456') }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Secondary Contact</span>
                      <span class="s-tile-value">{{ $draft['secondary_phone'] ?? ($customerObj?->secondary_mobile_num ?? 'N/A') }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Quarry Site Location</span>
                      <span class="s-tile-value text-truncate" title="{{ $draft['location'] ?? 'Salem / Semmandapatti' }}">{{ $draft['location'] ?? 'Salem / Semmandapatti' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Associated Concession</span>
                      <span class="s-tile-value">
                        @if(!empty($draft['lease_application_id']))
                          Lease #{{ $draft['lease_application_id'] }}
                        @else
                          Standalone Aerial Mission
                        @endif
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Lease Area Extent</span>
                      <span class="s-tile-value">{{ $draft['lease_area'] ?? '3.85 Hectares' }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PANEL 2: FLIGHT MISSION & HARDWARE SPECS (8-TILE BALANCED 4x2 MATRIX) -->
            <div class="s-section-panel">
              <div class="s-panel-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="s-banner-icon-badge" style="width:34px; height:34px; font-size:16px; background:#eff6ff; color:#0284c7; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-airplane-engines"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-navy mb-0" style="font-size:14px;">2. Flight Operations &amp; DGCA Aerial Specs</h6>
                    <small class="text-muted" style="font-size:11px;">DGCA DigitalSky mission flight parameters, remote pilot credentials, and sensor resolution</small>
                  </div>
                </div>
                <a href="{{ route('drone-survey.step', 2) }}" class="btn btn-sm s-btn-edit">
                  <i class="bi bi-pencil-square me-1"></i>Edit Step 2
                </a>
              </div>
              <div class="p-3">
                <div class="row g-2.5">
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Flight Execution Date</span>
                      <span class="s-tile-value">{{ $draft['flight_date'] ?? date('Y-m-d') }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Drone Hardware Model</span>
                      <span class="s-tile-value text-truncate" title="{{ $draft['drone_model'] ?? 'DJI Matrice 300 RTK' }}">{{ $draft['drone_model'] ?? 'DJI Matrice 300 RTK' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Drone UIN Registration No</span>
                      <span class="s-tile-value font-monospace text-primary text-truncate">{{ $draft['drone_uin_no'] ?? 'UIN-TN-DRN-2026-9901' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Certified Remote Pilot</span>
                      <span class="s-tile-value text-truncate">{{ $draft['drone_pilot_name'] ?? 'S. Karthik' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Pilot RPC License</span>
                      <span class="s-tile-value font-monospace">{{ $draft['pilot_rpc_no'] ?? 'RPC #DRN-TN-8821' }}</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">Flight Altitude (AGL)</span>
                      <span class="s-tile-value">{{ $draft['altitude_meters'] ?? 120 }}m Above Ground</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile">
                      <span class="s-tile-label">GSD Ground Resolution</span>
                      <span class="s-tile-value">{{ $draft['gsd_cm_px'] ?? 2.5 }} cm / pixel</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #10b981;">
                      <span class="s-tile-label">DGCA Airspace Clearance</span>
                      <span class="s-tile-value text-success" style="font-size:0.82rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Green Zone (DigitalSky)
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PANEL 3: DELIVERABLES & STATUTORY DEEDS REGISTRY (EXECUTIVE FOLDERS ARCHITECTURE) -->
            <div class="s-section-panel">
              <div class="s-panel-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="s-banner-icon-badge" style="width:34px; height:34px; font-size:16px; background:#dcfce7; color:#10b981; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-folder-check"></i>
                  </div>
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                      <h6 class="fw-bold text-navy mb-0" style="font-size:14px;">3. Volumetric Deliverables &amp; Statutory Registry</h6>
                      <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size:11px;">
                        Extracted: {{ number_format($volCbm, 2) }} CBM
                      </span>
                    </div>
                    <small class="text-muted" style="font-size:11px;">Statutory deeds, aerial photogrammetry deliverables, and DSM volumetric extraction models</small>
                  </div>
                </div>
                <a href="{{ route('drone-survey.step', 3) }}" class="btn btn-sm s-btn-edit">
                  <i class="bi bi-pencil-square me-1"></i>Edit Step 3
                </a>
              </div>
              <div class="p-3">
                <!-- Mini Volumetric Spec Strip -->
                <div class="row g-2.5 mb-3">
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #10b981;">
                      <span class="s-tile-label">Extracted Volume</span>
                      <span class="s-tile-value text-success fw-bold" style="font-size:1.05rem;">{{ number_format($volCbm, 2) }} CBM</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #3b82f6;">
                      <span class="s-tile-label">Processing Engine</span>
                      <span class="s-tile-value text-truncate" title="{{ $draft['deliverable_files_path'] ?? 'Pix4Dmapper & Cloud Suite' }}">
                        {{ $draft['deliverable_files_path'] ?? 'Pix4Dmapper & Cloud Photogrammetry Suite' }}
                      </span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #06b6d4;">
                      <span class="s-tile-label">Volumetric Method</span>
                      <span class="s-tile-value">3D DSM Differencing</span>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #8b5cf6;">
                      <span class="s-tile-label">Statutory Compliance</span>
                      <span class="s-tile-value text-truncate">TNMMCR 1959 Rule 36</span>
                    </div>
                  </div>
                </div>

                <!-- 2-Card Folder Registry Grid -->
                <div class="row g-3">
                  <!-- Folder 01: Requisition Deeds -->
                  <div class="col-lg-6">
                    <div class="s-folder-box h-100">
                      <div class="s-folder-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                          <div class="s-folder-icon s-folder-blue">
                            <i class="fa fa-folder-open"></i>
                          </div>
                          <div>
                            <span class="fw-bold text-navy" style="font-size:0.86rem;">Folder 01: Requisition Deeds</span>
                            <div class="text-muted" style="font-size:0.72rem;">Applicant statutory documents</div>
                          </div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">4 Documents</span>
                      </div>
                      <div class="p-2.5">
                        <div class="d-flex flex-column gap-1.5">
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">01</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">1. Request Letter</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Mandatory</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">02</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">2. Combined FMB &amp; Lease Map</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Mandatory</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">03</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">3. Approved Mining Plan (MP Copy)</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Mandatory</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">04</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">4. Environmental Clearance (EC) Copy</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Mandatory</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Folder 02: Photogrammetry Deliverables -->
                  <div class="col-lg-6">
                    <div class="s-folder-box h-100">
                      <div class="s-folder-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                          <div class="s-folder-icon s-folder-emerald">
                            <i class="fa fa-layer-group"></i>
                          </div>
                          <div>
                            <span class="fw-bold text-navy" style="font-size:0.86rem;">Folder 02: Photogrammetry Deliverables</span>
                            <div class="text-muted" style="font-size:0.72rem;">UAV datasets &amp; volumetric models</div>
                          </div>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">6 Deliverables</span>
                      </div>
                      <div class="p-2.5">
                        <div class="d-flex flex-column gap-1.5">
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">05</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">5. Drone Flight Log &amp; RPC Certificate</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">PDF</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">06</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">6. Ground Control Points (GCP) Coordinates</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">CSV / XLS</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">07</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">7. High-Res Orthomosaic Map (GeoTIFF)</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">GeoTIFF</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">08</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">8. DSM / DTM Elevation Contours</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">CAD / TIF</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">09</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">9. 3D Dense Point Cloud File</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size:10px;">LAS</span>
                          </div>
                          <div class="s-folder-item d-flex align-items-center justify-content-between p-2 rounded">
                            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                              <span class="badge bg-light text-primary border" style="font-size:9.5px;">10</span>
                              <span class="text-truncate fw-semibold" style="font-size:0.82rem; color:var(--min-text-dark, #0F1E4D);">10. Volumetric Excavation &amp; Stockpile Report</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:10px;">Mandatory</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- PANEL 4: ASSIGNED CREW -->
            <div class="s-section-panel">
              <div class="s-panel-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="s-banner-icon-badge" style="width:34px; height:34px; font-size:16px; background:#fef3c7; color:#d97706; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-navy mb-0" style="font-size:14px;">4. Assigned Flight Crew &amp; Photogrammetry Personnel</h6>
                    <small class="text-muted" style="font-size:11px;">Designated remote pilot in-charge and photogrammetry processing specialists</small>
                  </div>
                </div>
                <a href="{{ route('drone-survey.step', 4) }}" class="btn btn-sm s-btn-edit">
                  <i class="bi bi-pencil-square me-1"></i>Edit Step 4
                </a>
              </div>
              <div class="p-3">
                <div class="row g-2.5">
                  @foreach($handlers as $hIdx => $h)
                    <div class="col-md-6">
                      <div class="s-crew-card p-3 rounded-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                          <div class="s-crew-avatar">
                            <i class="bi bi-person-fill"></i>
                          </div>
                          <div>
                            <div class="fw-bold text-navy" style="font-size:14px;">{{ $h['person_name'] ?? ($h['name'] ?? 'Personnel') }}</div>
                            <div class="text-muted" style="font-size:11.5px; line-height:1.4;">{{ $h['notes'] ?? 'Flight execution & photogrammetry processing' }}</div>
                          </div>
                        </div>
                        <span class="badge s-team-pill flex-shrink-0 ms-2" style="font-size:11px;">{{ $h['role'] ?? 'Specialist' }}</span>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>

            <!-- PANEL 5: FINANCIAL LEDGER -->
            <div class="s-section-panel">
              <div class="s-panel-header">
                <div class="d-flex align-items-center gap-2">
                  <div class="s-banner-icon-badge" style="width:34px; height:34px; font-size:16px; background:#dcfce7; color:#10b981; border-radius:8px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="bi bi-cash-stack"></i>
                  </div>
                  <div>
                    <h6 class="fw-bold text-navy mb-0" style="font-size:14px;">5. Financial Settlement &amp; Billing Ledger</h6>
                    <small class="text-muted" style="font-size:11px;">Service quotation fee, collected advance remittance, and outstanding balance</small>
                  </div>
                </div>
                <a href="{{ route('drone-survey.step', 5) }}" class="btn btn-sm s-btn-edit">
                  <i class="bi bi-pencil-square me-1"></i>Edit Step 5
                </a>
              </div>
              <div class="p-3">
                <div class="row g-2.5">
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #3b82f6;">
                      <span class="s-tile-label">Agreed Quoted Fee</span>
                      <span class="s-tile-value text-navy" style="font-size:1.1rem;">₹ {{ number_format($pv, 2) }}</span>
                      <small class="text-muted" style="font-size:10.5px;">Drone survey package</small>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #10b981;">
                      <span class="s-tile-label">Collected Advance</span>
                      <span class="s-tile-value text-success" style="font-size:1.1rem;">₹ {{ number_format($pa, 2) }}</span>
                      <small class="text-muted" style="font-size:10.5px;">Paid remittance</small>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid {{ $pe > 0 ? '#ef4444' : '#10b981' }};">
                      <span class="s-tile-label">Pending Balance Due</span>
                      <span class="s-tile-value {{ $pe > 0 ? 'text-danger' : 'text-success' }}" style="font-size:1.1rem;">₹ {{ number_format($pe, 2) }}</span>
                      <small class="text-muted" style="font-size:10.5px;">{{ $pe > 0 ? 'Outstanding collection' : 'Balanced (₹0 Due)' }}</small>
                    </div>
                  </div>
                  <div class="col-sm-6 col-lg-3">
                    <div class="s-data-tile" style="border-left:3px solid #8b5cf6;">
                      <span class="s-tile-label">Settlement Status</span>
                      <div class="mt-1">
                        @php
                          $sPillClass = match(strtolower($st)) {
                            'paid'    => 'bg-success text-white',
                            'partial' => 'bg-warning text-dark',
                            default   => 'bg-danger text-white'
                          };
                        @endphp
                        <span class="badge {{ $sPillClass }} px-2 py-1 text-uppercase" style="font-size:11px;">
                          {{ $st }}
                        </span>
                      </div>
                      <small class="text-muted" style="font-size:10.5px;">Commercial billing state</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <!-- PRE-SUBMISSION STATUTORY DECLARATION CHECKBOX -->
          <div class="s-declaration-card mb-4 p-3.5">
            <div class="d-flex align-items-start gap-3">
              <div class="s-declaration-icon-badge flex-shrink-0">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="fw-bold text-navy mb-1" style="font-size:13.5px;">Statutory DGCA Drone Survey &amp; Mining Compliance Declaration</h6>
                <p class="text-muted mb-2" style="font-size:12px; line-height:1.5;">
                  I hereby certify that all unmanned aerial flight mission data, DGCA pilot credentials, RTK ground control benchmark coordinates, and volumetric earthwork calculations have been audited and conform to the Aircraft Rules, DGCA UAS guidelines, and Rule 36 of the Tamil Nadu Minor Mineral Concession Rules 1959.
                </p>
                <div class="form-check">
                  <input class="form-check-input mt-1" type="checkbox" id="statutory_declaration_drone" required checked>
                  <label class="form-check-label fw-bold text-navy small" for="statutory_declaration_drone">
                    I have verified all flight parameters, photogrammetric outputs, and financial ledgers, and authorize final submission.
                  </label>
                </div>
              </div>
            </div>
          </div>
        @endif

        <!-- ========================================================= -->
        <!-- WIZARD ACTIONS DOCK (EXACT LEASE STEP 1 PARITY)           -->
        <!-- ========================================================= -->
        <div class="wizard-actions s-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3">
          <a href="{{ $step === 1 ? route('drone-survey.index') : route('drone-survey.step', $step - 1) }}" class="btn btn-outline-secondary s-btn-cancel">
            <i class="bi bi-arrow-left me-1"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}
          </a>

          <div class="d-flex align-items-center gap-2">
            @if($step < 6)
              <button type="button" class="btn s-btn-draft px-3" id="btn_drone_save_draft">
                <i class="bi bi-save me-1"></i> Save Draft &amp; Exit
              </button>
            @endif

            @if(!auth()->check() || auth()->user()->can('drone.create') || auth()->user()->can('drone.manage') || auth()->user()->can('drone.edit') || auth()->user()->hasRole(['Admin', 'Super Admin', 'Officer', 'Staff']))
              <button type="submit" class="btn {{ $step === 6 ? 's-btn-finish' : 's-btn-submit' }} px-4 btn-drone-continue" id="btn_drone_main_submit">
                <span>{{ $step === 6 ? 'Finish & Save Survey' : 'Save & Continue' }}</span>
                <i class="bi {{ $step === 6 ? 'bi-check2-circle' : 'bi-arrow-right' }} ms-1"></i>
              </button>
            @endif
          </div>
        </div>

        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Save Draft & Exit handler
  const btnSaveDraft = document.getElementById('btn_drone_save_draft');
  if (btnSaveDraft) {
    btnSaveDraft.addEventListener('click', function() {
      const exitInput = document.getElementById('save_draft_exit');
      if (exitInput) exitInput.value = '1';
      document.querySelectorAll('#droneWizardForm [required]').forEach(el => el.removeAttribute('required'));
      document.getElementById('droneWizardForm').submit();
    });
  }

  // Pre-submission confirmation check on Step 6
  const chkDeclaration = document.getElementById('statutory_declaration_drone');
  const btnSubmit = document.getElementById('btn_drone_main_submit');
  if (chkDeclaration && btnSubmit && @json($step === 6)) {
    btnSubmit.addEventListener('click', function(e) {
      if (!chkDeclaration.checked) {
        e.preventDefault();
        alert('Please confirm the statutory declaration before submitting the survey.');
        chkDeclaration.focus();
      }
    });
  }

  // Step 3: Document Upload & View Handlers
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
          let viewBtn = slot.querySelector('.btn-view-drone-item');
          if (!viewBtn) {
            viewBtn = document.createElement('a');
            viewBtn.className = 'btn btn-sm btn-outline-info py-1 px-2.5 btn-view-drone-item me-1';
            viewBtn.style.fontSize = '.75rem';
            viewBtn.target = '_blank';
            viewBtn.rel = 'noopener noreferrer';
            viewBtn.title = 'View document';
            viewBtn.innerHTML = '<i class="bi bi-eye"></i> View';
            const btnUpload = slot.querySelector('.btn-upload-drone-item');
            slot.insertBefore(viewBtn, btnUpload);
          }
          viewBtn.href = blobUrl;

          const btnUpload = slot.querySelector('.btn-upload-drone-item');
          if (btnUpload) {
            btnUpload.className = 'btn btn-sm btn-outline-secondary py-1 px-2.5 btn-upload-drone-item';
            btnUpload.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Change';
          }
        }

        updateDroneDocProgress();

        // Background server upload
        const formData = new FormData();
        formData.append('file', file);
        formData.append('document_name', row.querySelector('.ci-name')?.textContent.trim() || file.name);
        formData.append('_token', '{{ csrf_token() }}');
        fetch('{{ route('drone-survey.upload') }}', {
          method: 'POST',
          body: formData
        }).then(r => r.json()).then(res => {
          if (res.success && res.file_url) {
            const liveView = row.querySelector('.btn-view-drone-item');
            if (liveView) liveView.href = res.file_url;
          }
        }).catch(err => {
          console.warn('Drone file server background upload warning:', err);
        });
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

  // Step 3: Add Custom Document button
  const btnAddDroneDoc = document.getElementById('btn_add_drone_doc');
  if (btnAddDroneDoc) {
    btnAddDroneDoc.addEventListener('click', function() {
      const docName = prompt('Enter Drone Survey Deliverable Document Name:');
      if (!docName || !docName.trim()) return;
      const container = document.getElementById('drone_checklist_container');
      const count = container.querySelectorAll('.checklist-row').length + 1;
      const row = document.createElement('div');
      row.className = 'checklist-row py-2.5 px-3 d-flex align-items-center justify-content-between border-bottom bg-white rounded-2 mb-1';
      row.setAttribute('data-mandatory', '0');
      row.innerHTML = `
        <div class="d-flex align-items-center gap-2.5 flex-grow-1 text-truncate">
          <div class="ci-icon rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px; height:36px; background:#f1f5f9; color:#64748b;">
            <i class="bi bi-file-earmark-plus"></i>
          </div>
          <div class="text-truncate">
            <div class="ci-name fw-semibold small text-dark text-truncate">
              <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
              ${count}. ${docName.trim()}
            </div>
            <div class="ci-meta text-muted" style="font-size:11px;">Custom deliverable &middot; Not uploaded</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 ms-3 flex-shrink-0 action-slot">
          <select class="form-select form-select-sm doc-req-select py-1 px-2 fw-bold text-center border-secondary-subtle text-muted bg-light"
                  name="drone_custom_doc_req_${count}" style="font-size:0.75rem; width:110px; border-radius:6px;">
            <option value="mandatory">Mandatory</option>
            <option value="optional" selected>Optional</option>
          </select>
          <input type="file" class="d-none drone-file-input" accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dxf">
          <button type="button" class="btn btn-sm btn-outline-navy py-1 px-2.5 btn-upload-drone-item" style="font-size:.75rem;">
            <i class="bi bi-upload me-1"></i>Upload
          </button>
        </div>
      `;
      container.appendChild(row);
      updateDroneDocProgress();
      bindDroneDocReqListeners();
    });
  }

  function bindDroneDocReqListeners() {
    document.querySelectorAll('#drone_checklist_container .doc-req-select').forEach(function(sel) {
      sel.onchange = function() {
        const row = this.closest('.checklist-row');
        const isMandatory = this.value === 'mandatory';
        if (row) {
          row.setAttribute('data-mandatory', isMandatory ? '1' : '0');
        }
        if (isMandatory) {
          this.className = 'form-select form-select-sm doc-req-select py-1 px-2 fw-bold text-center border-danger-subtle text-danger bg-danger-subtle';
        } else {
          this.className = 'form-select form-select-sm doc-req-select py-1 px-2 fw-bold text-center border-secondary-subtle text-muted bg-light';
        }
      };
    });
  }
  bindDroneDocReqListeners();

  // Step 4: Handlers Dynamic Add & Remove
  const btnAddHandler = document.getElementById('btn_add_drone_handler');
  const tbodyHandlers = document.getElementById('drone_handlers_tbody');

  function reindexDroneRows() {
    if (!tbodyHandlers) return;
    const rows = tbodyHandlers.querySelectorAll('tr');
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
    const badge = document.getElementById('drone_handler_count_badge');
    const banner = document.getElementById('drone_handler_count_banner');
    if (badge) badge.textContent = `${rows.length} member${rows.length > 1 ? 's' : ''}`;
    if (banner) banner.textContent = `${rows.length} Member${rows.length > 1 ? 's' : ''} Allocated`;
  }

  if (btnAddHandler && tbodyHandlers) {
    btnAddHandler.addEventListener('click', function() {
      const count = tbodyHandlers.querySelectorAll('tr').length;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="text-center fw-bold row-num">${count + 1}</td>
        <td>
          <div class="input-group s-input-group">
            <span class="input-group-text s-addon"><i class="bi bi-person"></i></span>
            <input type="text" name="handlers[${count}][person_name]" class="form-control s-input fw-semibold" placeholder="e.g. S. Kumaran" required>
          </div>
        </td>
        <td>
          <div class="input-group s-input-group">
            <span class="input-group-text s-addon"><i class="bi bi-briefcase"></i></span>
            <input type="text" name="handlers[${count}][role]" class="form-control s-input" list="role_suggestions" placeholder="e.g. Flight Observer" required>
          </div>
        </td>
        <td>
          <input type="text" name="handlers[${count}][notes]" class="form-control s-input" style="border-radius:8px !important;" placeholder="e.g. Battery management & safety coordination">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-sm btn-outline-danger btn-remove-drone-handler" title="Remove person">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      `;
      tbodyHandlers.appendChild(tr);
      reindexDroneRows();
    });

    tbodyHandlers.addEventListener('click', function(e) {
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

  // Step 5: Payment math calculation
  const fVal = document.getElementById('field_drone_val');
  const fPaid = document.getElementById('field_drone_paid');
  const fPending = document.getElementById('field_drone_pending');
  const fStatus = document.getElementById('field_drone_status');
  const dVal = document.getElementById('disp_drone_val');
  const dPaid = document.getElementById('disp_drone_paid');
  const dPending = document.getElementById('disp_drone_pending');
  const bannerPill = document.getElementById('banner_status_pill');

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

    if (bannerPill && fStatus) {
      if (fStatus.value === 'paid') {
        bannerPill.style.background = '#dcfce7';
        bannerPill.style.color = '#166534';
        bannerPill.style.borderColor = '#86efac';
        bannerPill.innerHTML = '<i class="bi bi-check-circle-fill me-1.5"></i> Fully Settled';
      } else if (fStatus.value === 'partial') {
        bannerPill.style.background = '#fff7ed';
        bannerPill.style.color = '#c2410c';
        bannerPill.style.borderColor = '#fdba74';
        bannerPill.innerHTML = '<i class="bi bi-clock-history me-1.5"></i> Partial Advance';
      } else {
        bannerPill.style.background = '#fee2e2';
        bannerPill.style.color = '#991b1b';
        bannerPill.style.borderColor = '#fca5a5';
        bannerPill.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment';
      }
    }
  }

  if (fVal && fPaid) {
    fVal.addEventListener('input', recalcDronePayment);
    fPaid.addEventListener('input', recalcDronePayment);
  }

  // Step 1: Customer Unique ID Lookup
  const mimasSearchInput = document.getElementById('mimas_search_input');
  const btnLookupMimas = document.getElementById('btn_lookup_mimas');
  const feedbackBox = document.getElementById('mimas_feedback_box');

  function applyDroneCustomerAutofill(c) {
    if (!c) return;
    const fCustId = document.getElementById('drone_customer_id');
    if (fCustId && c.id) fCustId.value = c.id;
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

    const fPrimaryPhone = document.getElementById('field_drone_primary_phone');
    const fSecondaryPhone = document.getElementById('field_drone_secondary_phone');
    if (fPrimaryPhone) fPrimaryPhone.value = c.mobile_num || '';
    if (fSecondaryPhone) fSecondaryPhone.value = c.secondary_mobile_num || '';

    document.querySelectorAll('.auto-filled-field').forEach(el => {
      el.classList.add('field-autofilled');
      setTimeout(() => el.classList.remove('field-autofilled'), 3000);
    });

    if (feedbackBox) {
      feedbackBox.style.display = 'block';
      feedbackBox.innerHTML = `
        <div class="alert alert-success py-2 px-3 mb-0 small d-flex align-items-center justify-content-between">
          <div>
            <i class="bi bi-check-circle-fill me-1.5 text-success"></i>
            <strong>Customer Profile Loaded:</strong> ${c.company_name || c.customer_name}
            &middot; <span class="text-muted">${c.district_name || 'District'}</span>
            &middot; <span class="badge bg-success-subtle text-success border border-success ms-1">Customer ID: ${c.mimas_no || c.id}</span>
          </div>
          <span class="badge bg-success text-white">Autofilled</span>
        </div>
      `;
    }
  }

  function performDroneMimasLookup() {
    const val = mimasSearchInput ? mimasSearchInput.value.trim() : '';
    if (!val) {
      if (feedbackBox) {
        feedbackBox.style.display = 'block';
        feedbackBox.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please enter or select a Customer Unique ID first.</div>';
      }
      return;
    }

    if (btnLookupMimas) {
      btnLookupMimas.disabled = true;
      btnLookupMimas.innerHTML = '<i class="bi bi-arrow-repeat fa-spin me-1"></i> Searching...';
    }
    if (feedbackBox) feedbackBox.style.display = 'none';

    fetch('/customers/lookup-mimas/' + encodeURIComponent(val))
      .then(res => res.json())
      .then(res => {
        if (btnLookupMimas) {
          btnLookupMimas.disabled = false;
          btnLookupMimas.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Fetch Details';
        }
        if (res.status === 1 && res.data) {
          applyDroneCustomerAutofill(res.data);
        } else {
          if (feedbackBox) {
            feedbackBox.style.display = 'block';
            feedbackBox.innerHTML = `<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="bi bi-info-circle me-1"></i> ${res.message || 'No customer found.'}</div>`;
          }
        }
      })
      .catch(err => {
        if (btnLookupMimas) {
          btnLookupMimas.disabled = false;
          btnLookupMimas.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i> Fetch Details';
        }
        if (feedbackBox) {
          feedbackBox.style.display = 'block';
          feedbackBox.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small"><i class="bi bi-x-circle-fill me-1"></i> Customer not found. You can enter details manually below.</div>';
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

<!-- EXACT SCOPED CSS MATCHING LEASE APPLICATION -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-drone-wizard)
     ========================================================================== */
  #gtms-drone-wizard {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */
  #gtms-drone-wizard .step-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 22px;
  }

  #gtms-drone-wizard .step-progress::before {
    content: "";
    position: absolute;
    top: 16px;
    left: 24px;
    right: 24px;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
  }

  #gtms-drone-wizard .sp-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    background: transparent;
  }

  #gtms-drone-wizard .sp-step .sp-label {
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 6px;
    line-height: 1.2;
    color: #64748b;
  }

  #gtms-drone-wizard .sp-step.active .sp-label {
    color: #0F1E4D;
  }

  #gtms-drone-wizard .sp-step .circ {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.80rem;
    font-weight: 700;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    color: #64748b;
    line-height: 1;
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .sp-step.done .circ {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
  }

  #gtms-drone-wizard .sp-step.active .circ {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
  }

  /* MAIN CARD */
  #gtms-drone-wizard .s-main-card {
    border-radius: 14px;
    padding: 26px 30px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
  }

  @media (max-width: 768px) {
    #gtms-drone-wizard .s-main-card {
      padding: 20px 16px;
    }
  }

  #gtms-drone-wizard .s-header-block {
    margin-bottom: 20px;
  }

  #gtms-drone-wizard .s-eyebrow {
    color: var(--orange, #ea580c);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
    margin-bottom: 0;
  }

  #gtms-drone-wizard .s-time-tag {
    font-size: 0.73rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.2;
  }

  #gtms-drone-wizard .s-title {
    font-weight: 800;
    font-size: 1.25rem;
    color: #0f172a;
    line-height: 1.3;
    margin-top: 6px;
    margin-bottom: 5px;
  }

  #gtms-drone-wizard .s-subtitle {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0;
  }

  /* LOOKUP CARD - LIGHT */
  #gtms-drone-wizard .s-lookup-card {
    background: #f0f7ff;
    border: 1.5px solid #bfdbfe;
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 22px;
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-lookup-label {
    font-size: 0.88rem;
    color: #1e40af;
    line-height: 1.2;
  }

  #gtms-drone-wizard .s-lookup-badge {
    background-color: #2563eb;
    color: #ffffff;
    font-size: 0.70rem;
    padding: 3px 7px;
    border-radius: 5px;
    font-weight: 600;
  }

  #gtms-drone-wizard .s-lookup-desc {
    color: #475569;
    font-size: 0.80rem;
    line-height: 1.45;
  }

  #gtms-drone-wizard .s-lookup-input-group .s-lookup-addon {
    width: 42px;
    height: 42px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-right: none !important;
    border-radius: 8px 0 0 8px;
    color: #2563eb;
  }

  #gtms-drone-wizard .s-lookup-input-group .s-lookup-field {
    height: 42px;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    border-right: none !important;
    border-radius: 0;
    font-size: 0.88rem;
    padding: 8px 12px;
    color: #0f172a;
  }

  #gtms-drone-wizard .s-btn-fetch {
    background-color: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    font-size: 0.84rem;
    font-weight: 700;
    height: 42px;
    border-radius: 0 8px 8px 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 18px;
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-btn-fetch:hover {
    background-color: #081233;
    border-color: #081233;
    color: #ffffff;
  }

  /* SECTION PANELS - LIGHT */
  #gtms-drone-wizard .s-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 18px;
    transition: border-color 0.2s ease;
  }

  #gtms-drone-wizard .s-section-card:hover {
    border-color: #cbd5e1;
  }

  #gtms-drone-wizard .s-section-header {
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

  #gtms-drone-wizard .s-label {
    font-size: 0.80rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.25;
    display: flex;
    align-items: center;
  }

  #gtms-drone-wizard .s-form-row {
    --bs-gutter-x: 1rem;
    --bs-gutter-y: 1.25rem;
  }

  #gtms-drone-wizard .s-opt-tag {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: normal;
  }

  #gtms-drone-wizard .s-hint {
    font-size: 0.72rem;
    color: #64748b;
    line-height: 1.4;
    margin-top: 6px;
    margin-bottom: 2px;
  }

  /* INPUT GROUPS - LIGHT */
  #gtms-drone-wizard .s-input-group {
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10);
    border-radius: 8px;
  }

  #gtms-drone-wizard .s-input-group .s-addon {
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

  #gtms-drone-wizard .s-input-group .s-input {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    border-radius: 0 8px 8px 0 !important;
    font-size: 0.86rem;
    height: 40px;
    padding: 8px 12px;
    color: #0f172a;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-drone-wizard .s-input-group:focus-within .s-addon,
  #gtms-drone-wizard .s-input-group:focus-within .s-input {
    border-color: #0F1E4D !important;
  }

  #gtms-drone-wizard .s-input-group:focus-within .s-addon {
    color: #0F1E4D !important;
  }

  /* NOTICE CARD - LIGHT */
  #gtms-drone-wizard .s-notice-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 16px;
    margin-top: 18px;
  }

  #gtms-drone-wizard .s-notice-icon {
    color: #1d4ed8;
    font-size: 1.15rem;
    line-height: 1;
    flex-shrink: 0;
  }

  #gtms-drone-wizard .s-notice-text {
    font-size: 0.79rem;
    color: #1e3a8a;
    line-height: 1.45;
  }

  /* BANNER CARD (STEPS 2, 4, 5, 6) */
  #gtms-drone-wizard .s-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
  }

  #gtms-drone-wizard .s-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #e0e7ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
  }

  #gtms-drone-wizard .s-banner-title {
    font-size: 0.88rem;
    color: #0F1E4D;
    margin-bottom: 2px;
  }

  #gtms-drone-wizard .s-banner-desc {
    font-size: 0.76rem;
    color: #64748b;
    line-height: 1.35;
  }

  #gtms-drone-wizard .s-team-pill {
    background: #e0e7ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    font-weight: 600;
    font-size: 0.76rem;
    padding: 6px 14px;
    border-radius: 20px;
  }

  #gtms-drone-wizard .s-count-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.76rem;
    padding: 6px 12px;
    border-radius: 8px;
  }

  #gtms-drone-wizard .s-banner-status-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 9999px;
  }

  /* TABLE CARD (STEP 4) */
  #gtms-drone-wizard .s-table-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
  }

  #gtms-drone-wizard .s-table-header-bar {
    background: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
  }

  #gtms-drone-wizard .s-table-title {
    font-size: 0.88rem;
    color: #1e293b;
  }

  #gtms-drone-wizard .s-badge-counter {
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 0.74rem;
    padding: 4px 10px;
  }

  #gtms-drone-wizard .s-btn-add-person {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    border: none;
    font-weight: 600;
    font-size: 0.80rem;
    padding: 6px 14px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.18);
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-btn-add-person:hover {
    background: linear-gradient(135deg, #0a1435 0%, #172e6e 100%);
    color: #ffffff;
    transform: translateY(-1px);
  }

  #gtms-drone-wizard .s-table thead th {
    background: #f8fafc;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    padding: 10px 14px;
    border-bottom: 1.5px solid #e2e8f0;
  }

  #gtms-drone-wizard .s-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  /* 3-BENTO KPI CARDS (STEP 5 & 6) */
  #gtms-drone-wizard .s-kpi-card {
    border-radius: 12px;
    padding: 16px 18px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-drone-wizard .s-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.08);
  }

  #gtms-drone-wizard .s-kpi-card-contract {
    background: #f8fafc;
    border-left: 4px solid #0F1E4D !important;
  }

  #gtms-drone-wizard .s-kpi-card-paid {
    background: #f0fdf4;
    border-left: 4px solid #10b981 !important;
  }

  #gtms-drone-wizard .s-kpi-card-pending {
    background: #fff7ed;
    border-left: 4px solid #f97316 !important;
  }

  #gtms-drone-wizard .s-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #64748b;
  }

  #gtms-drone-wizard .s-kpi-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  #gtms-drone-wizard .s-kpi-icon-contract {
    background: #e2e8f0;
    color: #0F1E4D;
  }

  #gtms-drone-wizard .s-kpi-icon-paid {
    background: #dcfce7;
    color: #10b981;
  }

  #gtms-drone-wizard .s-kpi-icon-pending {
    background: #ffedd5;
    color: #f97316;
  }

  #gtms-drone-wizard .s-kpi-value {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
    margin: 6px 0 4px;
  }

  #gtms-drone-wizard .s-kpi-subtext {
    font-size: 11.5px;
    color: #64748b;
  }

  /* REVIEW TILES (STEP 6) */
  #gtms-drone-wizard .s-section-panel {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
  }

  #gtms-drone-wizard .s-panel-header {
    background: #f8fafc;
    padding: 12px 18px;
    border-bottom: 1.5px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
  }

  #gtms-drone-wizard .s-folder-box {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-folder-box:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
  }

  #gtms-drone-wizard .s-folder-header {
    background: #f8fafc;
    padding: 10px 14px;
    border-bottom: 1px solid #edf2f7;
  }

  #gtms-drone-wizard .s-folder-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  #gtms-drone-wizard .s-folder-blue {
    background: rgba(59, 130, 246, 0.12);
    color: #2563eb;
  }

  #gtms-drone-wizard .s-folder-emerald {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
  }

  #gtms-drone-wizard .s-folder-item {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    transition: background 0.15s ease;
  }

  #gtms-drone-wizard .s-folder-item:hover {
    background: #f1f5f9;
  }

  #gtms-drone-wizard .s-crew-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    transition: all 0.18s ease;
  }

  #gtms-drone-wizard .s-crew-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
  }

  #gtms-drone-wizard .s-crew-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(15, 30, 77, 0.08);
    color: #0F1E4D;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    border: 1.5px solid rgba(15, 30, 77, 0.15);
    flex-shrink: 0;
  }

  #gtms-drone-wizard .s-declaration-card {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-left: 5px solid #10b981;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
  }

  #gtms-drone-wizard .s-declaration-icon-badge {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  #gtms-drone-wizard .s-data-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
    height: 100%;
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  #gtms-drone-wizard .s-tile-label {
    font-size: 0.70rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  #gtms-drone-wizard .s-tile-value {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0F1E4D;
  }

  #gtms-drone-wizard .s-btn-edit {
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 6px;
    padding: 4px 10px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #0F1E4D;
    transition: all 0.18s ease;
  }

  #gtms-drone-wizard .s-btn-edit:hover {
    background: #0F1E4D;
    color: #ffffff;
    border-color: #0F1E4D;
  }

  #gtms-drone-wizard .s-multi-badge {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.69rem;
    padding: 3px 7px;
    border-radius: 4px;
    font-weight: 600;
  }

  /* ACTIONS - EXACT LEASE APPLICATION MATCH */
  #gtms-drone-wizard .s-actions {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid #e2e8f0;
  }

  #gtms-drone-wizard .s-btn-cancel {
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

  #gtms-drone-wizard .s-btn-cancel:hover {
    border-color: #ef4444;
    color: #ef4444;
    background: #fef2f2;
  }

  #gtms-drone-wizard .s-btn-draft {
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

  #gtms-drone-wizard .s-btn-draft:hover {
    background: #0F1E4D;
    color: #ffffff;
  }

  #gtms-drone-wizard .s-btn-submit {
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

  #gtms-drone-wizard .s-btn-submit:hover {
    background: #081233;
    border-color: #081233;
    color: #ffffff;
  }

  #gtms-drone-wizard .s-btn-finish {
    background: #16a34a;
    border: 1.5px solid #16a34a;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 700;
    border-radius: 8px;
    height: 40px;
    padding: 0 22px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 6px rgba(22, 163, 74, 0.25);
    transition: all 0.2s ease;
  }

  #gtms-drone-wizard .s-btn-finish:hover {
    background: #15803d;
    border-color: #15803d;
    color: #ffffff;
  }

  #gtms-drone-wizard .field-autofilled {
    background-color: #f0fdf4 !important;
    border-color: #86efac !important;
    transition: all 0.4s ease;
  }

  /* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"])
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-drone-wizard .s-main-card {
    background-color: #202020 !important;
    border-color: #2d3139 !important;
    color: #e2e8f0 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-title {
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-subtitle {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-time-tag {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-lookup-card {
    background: #181c26 !important;
    border-color: #2a3d66 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-lookup-label {
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-lookup-desc {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-lookup-input-group .s-lookup-addon {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-lookup-input-group .s-lookup-field {
    background-color: #161717 !important;
    border-color: #374151 !important;
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-section-card,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-table-card,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-section-panel {
    background: #1a1b20 !important;
    border-color: #2d3139 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-section-header,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-table-header-bar,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-panel-header {
    background: #161717 !important;
    color: #f8fafc !important;
    border-bottom-color: #262930 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-label {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-input-group .s-addon {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-right: none !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-input-group .s-input {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-left: none !important;
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-input-group:focus-within .s-addon,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-input-group:focus-within .s-input {
    border-color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-banner-card {
    background: #181c26 !important;
    border-color: #2a3d66 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-data-tile {
    background: #161717 !important;
    border-color: #2d3139 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-tile-value {
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-btn-cancel {
    background: #161717 !important;
    border-color: #374151 !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-btn-draft {
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
    background: transparent !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-btn-draft:hover {
    background: #1e3a8a !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-folder-box {
    background: #1a1b20 !important;
    border-color: #2d3139 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-folder-header {
    background: #161717 !important;
    border-bottom-color: #262930 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-folder-item {
    background: #161717 !important;
    border-color: #262930 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-folder-item:hover {
    background: #21242d !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-folder-item span {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-crew-card {
    background: #161717 !important;
    border-color: #2d3139 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-crew-avatar {
    background: rgba(96, 165, 250, 0.15) !important;
    color: #60a5fa !important;
    border-color: rgba(96, 165, 250, 0.3) !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-declaration-card {
    background: #1a1b20 !important;
    border-color: #2d3139 !important;
    border-left-color: #10b981 !important;
  }

  body[data-theme-version="dark"] #gtms-drone-wizard .s-declaration-card h6,
  body[data-theme-version="dark"] #gtms-drone-wizard .s-declaration-card label {
    color: #f8fafc !important;
  }
</style>
@endpush
@endsection
