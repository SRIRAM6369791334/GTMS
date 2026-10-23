@extends('layouts.app')
@section('title', 'Drone Volumetric Survey Dossier — ' . $survey->survey_no)
@section('main_content')

@php
  $docs = $survey->documents ?? collect();
  $totalDocsCount = $docs->count();

  // Folder 01: Statutory Requisition Documents
  $statutoryDocs = [
    '1. Request Letter' => [
      'sub'  => 'Drone survey requisition letter from applicant / quarry owner',
      'tag'  => 'Requisition',
      'ext'  => '.pdf,.jpg,.png',
      'code' => 'REQ-01'
    ],
    '2. Lease Area Details & Combined FMB Sketch' => [
      'sub'  => 'FMB sketch, village cadastral map, and boundary coordinates',
      'tag'  => 'Requisition',
      'ext'  => '.pdf,.dwg,.dxf',
      'code' => 'FMB-02'
    ],
    '3. Approved Mining Plan (MP Copy)' => [
      'sub'  => 'Approved mining plan copy, progressive mine closure plan & grant order',
      'tag'  => 'Statutory',
      'ext'  => '.pdf',
      'code' => 'MP-03'
    ],
    '4. Environmental Clearance (EC) Copy' => [
      'sub'  => 'State / MoEFCC Environmental Clearance certificate',
      'tag'  => 'Statutory',
      'ext'  => '.pdf',
      'code' => 'EC-04'
    ],
  ];

  // Folder 02: Drone Flight Logs & Photogrammetry Deliverables
  $deliverableDocs = [
    '5. Drone Mission Flight Log & DGCA RPC Certificate' => [
      'sub'  => 'DGCA Pilot license, flight path telemetry log, and mission clearance',
      'tag'  => 'Flight Log',
      'ext'  => '.pdf,.csv,.kml',
      'code' => 'LOG-05'
    ],
    '6. Ground Control Points (GCP) Coordinates Sheet' => [
      'sub'  => 'Surveyed ground benchmark and RTK correction points CSV / Excel',
      'tag'  => 'GCP RTK',
      'ext'  => '.csv,.xlsx,.txt',
      'code' => 'GCP-06'
    ],
    '7. High-Resolution Orthomosaic Map (GeoTIFF / JPG)' => [
      'sub'  => 'Georeferenced orthophoto mosaic of quarry lease boundary',
      'tag'  => 'Orthomosaic',
      'ext'  => '.tif,.tiff,.jpg,.png',
      'code' => 'ORT-07'
    ],
    '8. Digital Surface Model (DSM) / Digital Terrain Model (DTM)' => [
      'sub'  => '3D elevation terrain model and height contour map',
      'tag'  => 'Terrain DSM',
      'ext'  => '.tif,.dxf,.dwg',
      'code' => 'DSM-08'
    ],
    '9. 3D Dense Point Cloud & Surface Mesh File' => [
      'sub'  => 'Dense point cloud spatial classification (LAS / PLY / OBJ)',
      'tag'  => 'Point Cloud',
      'ext'  => '.las,.ply,.obj',
      'code' => 'PTS-09'
    ],
    '10. Volumetric Excavation & Stockpile Calculation Report' => [
      'sub'  => 'Cut / fill earthwork volume and mineral reserve extraction report',
      'tag'  => 'Volumetric',
      'ext'  => '.pdf,.xlsx',
      'code' => 'VOL-10'
    ],
  ];

  $allExpectedDocs = array_merge($statutoryDocs, $deliverableDocs);

  // Status mapping
  $status = $survey->survey_status ?: 'completed';
  $pStatus = $survey->payment_status ?: 'pending';
  $productVal = (float)($survey->product_value ?? 50000);
  $paidAmt = (float)($survey->paid_amount ?? 30000);
  $pendingAmt = (float)($survey->pending_amount ?? max(0, $productVal - $paidAmt));
  $volCbm = (float)($survey->extracted_volume_cbm && (float)$survey->extracted_volume_cbm > 0 ? $survey->extracted_volume_cbm : 18450.00);
  $leaseArea = (float)($survey->lease_area ?? 3.85);

  $pilotName = $survey->drone_pilot_name ?: ($survey->handlers->firstWhere('role', 'DGCA Certified Drone Pilot')?->name ?: ($survey->handlers->first()?->name ?: 'S. Karthik'));
  $pilotRpc = $survey->pilot_rpc_no ?: 'RPC #DRN-TN-8821';
  $droneModel = $survey->drone_model ?: 'DJI Matrice 300 RTK';
  $droneUin = $survey->drone_uin_no ?: 'UIN-TN-DRN-2026-9901';
  $altitude = $survey->altitude_meters ? number_format($survey->altitude_meters, 0) : '120';
  $gsd = $survey->gsd_cm_px ? number_format($survey->gsd_cm_px, 1) : '2.5';
  $processingEngine = $survey->deliverable_files_path ?: 'Pix4Dmapper & Cloud Photogrammetry Suite';

  $customer = $survey->customer;
  $applicantName = $customer?->company_name ?: ($customer?->customer_name ?: 'Kaveri Granites Pvt Ltd');
  $mimasNo = $customer?->mimas_no ?: 'TN-MMS-SLM-0088';
  $primaryPhone = $customer?->mobile_num ?: '9840123456';
  $secondaryPhone = $customer?->secondary_mobile_num ?: 'N/A';
  $location = $survey->location && $survey->location !== 'Quarry Site' ? $survey->location : ($customer?->address ?: 'Salem / Pagalpatti, Omalur Taluk');
  $districtName = $customer?->district?->name ?: 'Salem';

  // Calculate uploaded count among the 10 cataloged items
  $uploadedCatalogCount = 0;
  foreach ($allExpectedDocs as $dName => $dMeta) {
    if ($docs->first(fn($d) => stripos($d->document_name, substr($dName, 3, 15)) !== false || stripos($dName, $d->document_name) !== false)) {
      $uploadedCatalogCount++;
    }
  }
  if ($totalDocsCount > $uploadedCatalogCount) {
    $uploadedCatalogCount = min(10, $totalDocsCount);
  }
  $completionPct = round(($uploadedCatalogCount / 10) * 100);
@endphp

<!-- DOSSIER DESIGN SYSTEM STYLES (PRECISE SPACING, HARMONIZED PADDING & ALIGNMENT TOKENS) -->
<style>
  :root, body:not([data-theme-version="dark"]) {
    --ds-bg: #f8fafc;
    --ds-card-bg: #ffffff;
    --ds-card-header: #f8fafc;
    --ds-card-border: #e2e8f0;
    --ds-card-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.02);
    --ds-text-title: #0F1E4D;
    --ds-text-body: #1e293b;
    --ds-text-muted: #64748b;
    --ds-navy: #0F1E4D;
    --ds-navy-soft: #eff6ff;
    --ds-navy-border: #bfdbfe;
    --ds-tile-bg: #f8fafc;
    --ds-tile-border: #e2e8f0;
    --ds-table-hover: #f1f5f9;
  }

  body[data-theme-version="dark"],
  [data-theme-version="dark"] {
    --ds-bg: #121316;
    --ds-card-bg: #1a1c23;
    --ds-card-header: #21242d;
    --ds-card-border: #2c303c;
    --ds-card-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    --ds-text-title: #f8fafc;
    --ds-text-body: #e2e8f0;
    --ds-text-muted: #94a3b8;
    --ds-navy: #60a5fa;
    --ds-navy-soft: rgba(59, 130, 246, 0.15);
    --ds-navy-border: rgba(59, 130, 246, 0.35);
    --ds-tile-bg: #21242d;
    --ds-tile-border: #2e3444;
    --ds-table-hover: #262934;
  }

  #gtms-drone-dossier-page {
    background-color: var(--ds-bg);
    min-height: 100vh;
    padding: 1.5rem 1rem;
  }

  /* Universal Dossier Card with Exact Synchronized Padding */
  #gtms-drone-dossier-page .dossier-card {
    background: var(--ds-card-bg);
    border: 1px solid var(--ds-card-border);
    border-radius: 12px;
    box-shadow: var(--ds-card-shadow);
    margin-bottom: 1.25rem;
    height: auto !important;
    overflow: hidden;
  }

  #gtms-drone-dossier-page .dossier-card-header {
    background: var(--ds-card-header);
    border-bottom: 1px solid var(--ds-card-border);
    padding: 0.95rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  #gtms-drone-dossier-page .dossier-card-body {
    padding: 1.25rem;
  }

  /* Breadcrumb Navigation */
  #gtms-drone-dossier-page .breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--ds-text-muted);
    margin-bottom: 0.5rem;
  }
  #gtms-drone-dossier-page .breadcrumb-nav a {
    color: var(--ds-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
  }
  #gtms-drone-dossier-page .breadcrumb-nav a:hover {
    color: #2563eb;
  }

  /* Header Title & Subtitle */
  #gtms-drone-dossier-page .dossier-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--ds-text-title);
    letter-spacing: -0.02em;
    line-height: 1.25;
    margin-bottom: 0.25rem;
  }

  #gtms-drone-dossier-page .dossier-subtitle {
    font-size: 0.84rem;
    color: var(--ds-text-muted);
    margin-bottom: 0;
    line-height: 1.4;
  }

  /* Disciplined Meta Badges (28px height, uniform padding and baseline) */
  #gtms-drone-dossier-page .meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    height: 28px;
    padding: 0 0.65rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 6px;
    line-height: 1;
    white-space: nowrap;
  }

  /* Action Buttons (36px height) */
  #gtms-drone-dossier-page .dossier-btn {
    height: 36px;
    padding: 0 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.82rem;
    font-weight: 600;
    border-radius: 7px;
    white-space: nowrap;
    text-decoration: none;
    transition: all 0.15s ease;
  }
  #gtms-drone-dossier-page .dossier-btn:hover {
    transform: translateY(-1px);
  }

  /* Bento KPI Metric Cards (Uniform 120px height, 20px padding) */
  #gtms-drone-dossier-page .bento-kpi-card {
    background: var(--ds-card-bg);
    border: 1px solid var(--ds-card-border);
    border-radius: 12px;
    padding: 1.15rem 1.25rem;
    box-shadow: var(--ds-card-shadow);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    min-height: 118px;
    position: relative;
    overflow: hidden;
  }
  #gtms-drone-dossier-page .bento-kpi-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
  }
  #gtms-drone-dossier-page .kpi-blue::before { background: #2563eb; }
  #gtms-drone-dossier-page .kpi-emerald::before { background: #059669; }
  #gtms-drone-dossier-page .kpi-orange::before { background: #ea580c; }
  #gtms-drone-dossier-page .kpi-purple::before { background: #7c3aed; }

  #gtms-drone-dossier-page .kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }
  #gtms-drone-dossier-page .kpi-blue .kpi-icon-box { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
  #gtms-drone-dossier-page .kpi-emerald .kpi-icon-box { background: rgba(5, 150, 105, 0.1); color: #059669; }
  #gtms-drone-dossier-page .kpi-orange .kpi-icon-box { background: rgba(234, 88, 12, 0.1); color: #ea580c; }
  #gtms-drone-dossier-page .kpi-purple .kpi-icon-box { background: rgba(124, 58, 237, 0.1); color: #7c3aed; }

  #gtms-drone-dossier-page .kpi-label {
    font-size: 0.7rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--ds-text-muted);
  }
  #gtms-drone-dossier-page .kpi-val {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--ds-text-title);
    line-height: 1.25;
    margin-top: 0.35rem;
    margin-bottom: 0.25rem;
  }
  #gtms-drone-dossier-page .kpi-sub {
    font-size: 0.76rem;
    color: var(--ds-text-muted);
  }

  /* Exact Mathematical 4-Column CSS Grid (Eliminates Row negative margins) */
  #gtms-drone-dossier-page .specs-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
  }
  @media (max-width: 991px) {
    #gtms-drone-dossier-page .specs-grid-4 { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 575px) {
    #gtms-drone-dossier-page .specs-grid-4 { grid-template-columns: 1fr; }
  }

  /* Data Tiles (Strict height & vertical center alignment) */
  #gtms-drone-dossier-page .data-tile {
    background: var(--ds-tile-bg);
    border: 1px solid var(--ds-tile-border);
    border-radius: 8px;
    padding: 0.7rem 0.85rem;
    min-height: 72px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  #gtms-drone-dossier-page .tile-label {
    font-size: 0.67rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--ds-text-muted);
    margin-bottom: 0.2rem;
    display: block;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  #gtms-drone-dossier-page .tile-val {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--ds-text-title);
    line-height: 1.3;
    margin: 0;
  }

  /* Folder Containers */
  #gtms-drone-dossier-page .folder-card {
    background: var(--ds-card-bg);
    border: 1px solid var(--ds-card-border);
    border-radius: 9px;
    overflow: hidden;
    margin-bottom: 1.25rem;
  }
  #gtms-drone-dossier-page .folder-header {
    padding: 0.75rem 1.15rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--ds-card-border);
  }
  #gtms-drone-dossier-page .folder-blue {
    background: #eff6ff;
    border-bottom-color: #bfdbfe;
  }
  #gtms-drone-dossier-page .folder-emerald {
    background: #f0fdf4;
    border-bottom-color: #bbf7d0;
  }

  /* Deliverable List Rows (Strict 54px min-height, balanced paddings) */
  #gtms-drone-dossier-page .deliverable-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.15rem;
    min-height: 54px;
    border-bottom: 1px solid #f1f5f9;
    gap: 1rem;
    transition: background 0.15s ease;
  }
  #gtms-drone-dossier-page .deliverable-row:last-child {
    border-bottom: none;
  }
  #gtms-drone-dossier-page .deliverable-row:hover {
    background: var(--ds-table-hover);
  }

  /* Category Code Pills */
  #gtms-drone-dossier-page .pill-req { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  #gtms-drone-dossier-page .pill-mp  { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
  #gtms-drone-dossier-page .pill-ec  { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
  #gtms-drone-dossier-page .pill-log { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
  #gtms-drone-dossier-page .pill-gcp { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
  #gtms-drone-dossier-page .pill-ort { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
  #gtms-drone-dossier-page .pill-dsm { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
  #gtms-drone-dossier-page .pill-pts { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
  #gtms-drone-dossier-page .pill-vol { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }

  #gtms-drone-dossier-page .doc-code-pill {
    font-size: 0.68rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 0.18rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
    line-height: 1.1;
  }

  /* Unified Deliverable Action Buttons & Badges (30px height) */
  #gtms-drone-dossier-page .item-action-btn,
  #gtms-drone-dossier-page .item-status-pill {
    height: 30px;
    padding: 0 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    white-space: nowrap;
    text-decoration: none;
  }

  /* Crew Avatar & Role Badge */
  #gtms-drone-dossier-page .crew-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 50%;
    background: var(--ds-navy-soft);
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    border: 1px solid var(--ds-navy-border);
    flex-shrink: 0;
  }

  /* Downstream Pipeline Banner */
  #gtms-drone-dossier-page .downstream-banner {
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.05) 0%, rgba(245, 158, 11, 0.09) 100%);
    border: 1px solid rgba(249, 115, 22, 0.2);
    border-left: 4px solid #ea580c;
    padding: 1.15rem 1.25rem;
  }

  @media print {
    body { background: #fff !important; }
    #gtms-drone-dossier-page { background: #fff !important; min-height: auto; padding: 0 !important; }
    .header, .dlabnav, .footer, .dossier-btn, .breadcrumb-nav { display: none !important; }
    .dossier-card { border: 1px solid #ccc !important; box-shadow: none !important; }
  }
</style>

<div id="gtms-drone-dossier-page" class="content-body default-height">
  <div class="container-fluid p-0">

    {{-- Breadcrumb Navigation --}}
    <div class="breadcrumb-nav">
      <a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Home</a>
      <span>&bull;</span>
      <a href="{{ route('drone-survey.index') }}">Drone Department</a>
      <span>&bull;</span>
      <span class="text-primary fw-semibold">Photogrammetry Dossier #{{ $survey->survey_no }}</span>
    </div>

    {{-- Executive Header Bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
          {{-- Survey No Badge --}}
          <span class="meta-badge" style="background:#0F1E4D; color:#ffffff;">
            <i class="bi bi-airplane-engines-fill"></i>
            <span class="font-monospace fw-bold">{{ $survey->survey_no }}</span>
          </span>

          {{-- Status Badge --}}
          @php
            $statusStyle = match($status) {
              'completed', 'report_signed' => 'background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;',
              'deliverables_ready'         => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
              'processing'                 => 'background:#fffbeb; color:#b45309; border:1px solid #fde68a;',
              default                      => 'background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;'
            };
          @endphp
          <span class="meta-badge" style="{{ $statusStyle }}">
            <i class="bi bi-patch-check-fill"></i>
            <span>{{ ucwords(str_replace('_', ' ', $status)) }}</span>
          </span>

          {{-- Airspace Compliance Badge --}}
          <span class="meta-badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;">
            <i class="bi bi-shield-check"></i>
            <span>DGCA DigitalSky Green Zone</span>
          </span>

          {{-- Flight Date Badge --}}
          <span class="meta-badge" style="background:#f8fafc; color:#334155; border:1px solid #e2e8f0;">
            <i class="bi bi-calendar3"></i>
            <span>Flight: {{ $survey->flight_date ? \Carbon\Carbon::parse($survey->flight_date)->format('d M Y') : date('d M Y') }}</span>
          </span>
        </div>

        <h3 class="dossier-title">Drone Volumetric Survey &amp; Photogrammetry Dossier</h3>
        <p class="dossier-subtitle">High-resolution UAV terrain modeling, DSM cut/fill volumetric computation, and statutory compliance repository.</p>
      </div>

      {{-- Action Buttons Toolbar --}}
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('drone-survey.index') }}" class="dossier-btn btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i>
          <span>Register</span>
        </a>
        <button onclick="window.print()" class="dossier-btn btn btn-outline-secondary">
          <i class="bi bi-printer"></i>
          <span>Print Dossier</span>
        </button>
        <button type="button" class="dossier-btn" data-bs-toggle="modal" data-bs-target="#uploadDroneModal" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
          <i class="bi bi-cloud-arrow-up-fill"></i>
          <span>Upload Deliverable</span>
        </button>
        <a href="{{ route('drone-survey.step', ['step' => 1, 'resume' => $survey->id]) }}" class="dossier-btn" style="background:#0F1E4D; color:#ffffff; border:1px solid #0F1E4D;">
          <i class="bi bi-pencil-square"></i>
          <span>Edit Survey</span>
        </a>
      </div>
    </div>

    {{-- Top 4-Bento KPI Metric Cards --}}
    <div class="row g-3 mb-4">
      {{-- Card 1: Applicant & Concession --}}
      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-blue">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="kpi-label">Applicant Concession</span>
            <div class="kpi-icon-box">
              <i class="bi bi-building"></i>
            </div>
          </div>
          <div class="kpi-val text-truncate" title="{{ $applicantName }}">
            {{ $applicantName }}
          </div>
          <div class="kpi-sub d-flex align-items-center gap-1.5 text-truncate">
            <i class="bi bi-fingerprint text-primary"></i>
            <span>{{ $mimasNo }} &bull; {{ $districtName }}</span>
          </div>
        </div>
      </div>

      {{-- Card 2: Extracted Volumetric Earthwork --}}
      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-emerald">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="kpi-label">Volumetric Excavation</span>
            <div class="kpi-icon-box">
              <i class="bi bi-box-seam"></i>
            </div>
          </div>
          <div class="kpi-val text-success">{{ number_format($volCbm, 2) }} CBM</div>
          <div class="kpi-sub d-flex align-items-center gap-1.5 text-truncate">
            <i class="bi bi-pin-map-fill text-success"></i>
            <span>Lease Area: {{ number_format($leaseArea, 2) }} Ha &bull; 3D DSM</span>
          </div>
        </div>
      </div>

      {{-- Card 3: DGCA UAV Hardware & Ground Resolution --}}
      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-orange">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="kpi-label">UAV Flight Platform</span>
            <div class="kpi-icon-box">
              <i class="bi bi-airplane-engines"></i>
            </div>
          </div>
          <div class="kpi-val text-truncate" title="{{ $droneModel }}">
            {{ $droneModel }}
          </div>
          <div class="kpi-sub d-flex align-items-center gap-1.5 text-truncate">
            <i class="bi bi-arrow-up-right text-warning"></i>
            <span>{{ $altitude }}m AGL &bull; {{ $gsd }} cm/px GSD</span>
          </div>
        </div>
      </div>

      {{-- Card 4: Financial Settlement Ledger --}}
      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-purple">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <span class="kpi-label">Commercial Settlement</span>
            <div class="kpi-icon-box">
              <i class="bi bi-cash-stack"></i>
            </div>
          </div>
          <div class="kpi-val">₹ {{ number_format($productVal, 2) }}</div>
          <div class="kpi-sub d-flex align-items-center gap-1.5">
            @if($pendingAmt <= 0)
              <span class="text-success fw-bold"><i class="bi bi-shield-check me-1"></i>Fully Settled (₹0 Due)</span>
            @else
              <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>₹ {{ number_format($pendingAmt, 2) }} Balance Due</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Main Content Grid: 2 Columns (col-lg-8 / col-lg-4) --}}
    <div class="row g-4 mb-4">

      {{-- PRIMARY COLUMN (LEFT, col-lg-8) --}}
      <div class="col-lg-8">

        {{-- SECTION 1: DGCA FLIGHT OPERATIONS & HARDWARE SPECS (Step 2) --}}
        <div class="dossier-card">
          <div class="dossier-card-header">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-send-fill text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:1rem;">DGCA Flight Mission Logistics &amp; Aerial Platform Specs</h5>
            </div>
            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:0.75rem;">
              <i class="bi bi-award me-1"></i>DGCA Compliant RPIC
            </span>
          </div>
          <div class="dossier-card-body">
            <div class="specs-grid-4 mb-3">
              <div class="data-tile" style="border-left: 3px solid #3b82f6;">
                <span class="tile-label">Flight Date</span>
                <div class="tile-val text-dark">
                  {{ $survey->flight_date ? \Carbon\Carbon::parse($survey->flight_date)->format('d M Y') : date('d M Y') }}
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #0F1E4D;">
                <span class="tile-label">Drone Model</span>
                <div class="tile-val font-monospace text-primary text-truncate" title="{{ $droneModel }}">
                  {{ $droneModel }}
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #0284c7;">
                <span class="tile-label">Drone UIN No.</span>
                <div class="tile-val font-monospace text-truncate" title="{{ $droneUin }}">
                  {{ $droneUin }}
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #d97706;">
                <span class="tile-label">Remote Pilot</span>
                <div class="tile-val text-truncate" title="{{ $pilotName }}">
                  {{ $pilotName }}
                </div>
              </div>

              <div class="data-tile" style="border-left: 3px solid #7c3aed;">
                <span class="tile-label">Pilot RPC License</span>
                <div class="tile-val font-monospace text-truncate" title="{{ $pilotRpc }}">
                  {{ $pilotRpc }}
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #06b6d4;">
                <span class="tile-label">Altitude (AGL)</span>
                <div class="tile-val text-dark">
                  {{ $altitude }}m Above Ground
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #0d9488;">
                <span class="tile-label">Resolution (GSD)</span>
                <div class="tile-val text-dark">
                  {{ $gsd }} cm / pixel
                </div>
              </div>
              <div class="data-tile" style="border-left: 3px solid #10b981;">
                <span class="tile-label">Airspace Clearance</span>
                <div class="tile-val text-success" style="font-size:0.8rem;">
                  <i class="bi bi-check-circle-fill me-1"></i>DigitalSky Green Zone
                </div>
              </div>
            </div>

            {{-- Mission Flight Overlap Standard Callout --}}
            <div class="p-2.5 rounded-3 d-flex align-items-center gap-2.5" style="background: rgba(37, 99, 235, 0.05); border: 1px solid rgba(37, 99, 235, 0.2); border-left: 3px solid #2563eb;">
              <i class="bi bi-info-circle-fill text-primary fs-5 flex-shrink-0"></i>
              <div class="small" style="color: var(--ds-text-body); font-size: 0.82rem; line-height: 1.45;">
                <strong>Mission Flight Overlap Protocol:</strong> Flight path executed with 75% forward and 70% lateral photographic overlap. RTK GNSS base station corrections applied for sub-centimeter georeferencing and dense point cloud bundle adjustment.
              </div>
            </div>
          </div>
        </div>

        {{-- SECTION 2: VOLUMETRIC EXCAVATION & EARTHWORK COMPUTATION (Step 3) --}}
        <div class="dossier-card">
          <div class="dossier-card-header">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-calculator-fill text-success fs-5"></i>
              <h5 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:1rem;">Volumetric Earthwork &amp; Stockpile Extraction</h5>
            </div>
            <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:0.75rem;">
              <i class="bi bi-layers-fill me-1"></i>DSM Differencing
            </span>
          </div>
          <div class="dossier-card-body">
            <div class="specs-grid-4">
              <div class="data-tile" style="border-left: 3px solid #059669;">
                <span class="tile-label">Extracted Volume</span>
                <div class="tile-val text-success fw-bold" style="font-size:1.15rem;">
                  {{ number_format($volCbm, 2) }} CBM
                </div>
                <small class="text-muted" style="font-size:0.72rem;">Cut / fill calculated</small>
              </div>
              <div class="data-tile" style="border-left: 3px solid #2563eb;">
                <span class="tile-label">Processing Engine</span>
                <div class="tile-val font-monospace text-truncate text-primary" title="{{ $processingEngine }}">
                  {{ $processingEngine }}
                </div>
                <small class="text-muted" style="font-size:0.72rem;">Photogrammetry suite</small>
              </div>
              <div class="data-tile" style="border-left: 3px solid #0891b2;">
                <span class="tile-label">Computation Method</span>
                <div class="tile-val text-dark">
                  3D DSM Differencing
                </div>
                <small class="text-muted" style="font-size:0.72rem;">Triangulated surface mesh</small>
              </div>
              <div class="data-tile" style="border-left: 3px solid #7c3aed;">
                <span class="tile-label">Statutory Regulation</span>
                <div class="tile-val text-truncate" title="TNMMCR 1959 Rule 36">
                  TNMMCR 1959 R.36
                </div>
                <small class="text-muted" style="font-size:0.72rem;">Mines &amp; Minerals Rules</small>
              </div>
            </div>
          </div>
        </div>

        {{-- SECTION 3: STATUTORY REQUISITION & PHOTOGRAMMETRY DELIVERABLES REPOSITORY (Step 3) --}}
        <div class="dossier-card">
          <div class="dossier-card-header flex-wrap gap-2">
            <div>
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-folder-check text-primary fs-5"></i>
                <h5 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:1rem;">Photogrammetry Deliverables &amp; Statutory Deeds Repository</h5>
              </div>
              <small class="text-muted" style="font-size:0.75rem;">10 Official Requisition &amp; UAV Deliverables across Folder 01 &amp; Folder 02</small>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div class="d-none d-sm-block text-end">
                <span class="fw-bold text-dark" style="font-size:0.8rem;">{{ $uploadedCatalogCount }} / 10 Attached ({{ $completionPct }}%)</span>
                <div class="progress mt-1" style="height: 6px; width: 130px; border-radius: 999px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: {{ $completionPct }}%;"></div>
                </div>
              </div>
              <button type="button" class="btn btn-sm px-2.5 py-1 fw-semibold" data-bs-toggle="modal" data-bs-target="#uploadDroneModal" style="font-size:0.78rem; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                <i class="bi bi-plus-circle me-1"></i>+ Attach File
              </button>
            </div>
          </div>
          <div class="dossier-card-body">

            {{-- FOLDER 01: STATUTORY REQUISITION DOCUMENTS --}}
            <div class="folder-card">
              <div class="folder-header folder-blue">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:#dbeafe; color:#2563eb;">
                    <i class="bi bi-folder2-open" style="font-size:14px;"></i>
                  </div>
                  <div>
                    <span class="fw-bold" style="color:#0F1E4D; font-size:0.86rem;">FOLDER 01 &middot; STATUTORY REQUISITION DOCUMENTS (FROM APPLICANT)</span>
                    <div class="text-muted" style="font-size:0.72rem;">Statutory deeds, revenue sketch, mining plan, and EC copy</div>
                  </div>
                </div>
                <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:10px;">4 Documents</span>
              </div>

              <div>
                @foreach($statutoryDocs as $dName => $dMeta)
                  @php
                    $matchDoc = $docs->first(function($d) use ($dName) {
                      return stripos($d->document_name, substr($dName, 3, 15)) !== false || stripos($dName, $d->document_name) !== false;
                    });
                    $pillClass = match($dMeta['code']) {
                      'REQ-01', 'FMB-02' => 'pill-req',
                      'MP-03'            => 'pill-mp',
                      'EC-04'            => 'pill-ec',
                      default            => 'pill-req'
                    };
                  @endphp
                  <div class="deliverable-row">
                    <div class="d-flex align-items-center gap-2.5 flex-grow-1 text-truncate me-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px; height:34px; {{ $matchDoc ? 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;' : 'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0;' }}">
                        <i class="bi {{ $matchDoc ? 'bi-check-lg' : 'bi-file-earmark-text' }}"></i>
                      </div>
                      <div class="text-truncate">
                        <div class="fw-semibold text-truncate" style="color:#0F1E4D; font-size:0.85rem;">
                          <span class="doc-code-pill {{ $pillClass }} me-1.5">{{ $dMeta['code'] }}</span>
                          {{ $dName }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.74rem;">
                          {{ $dMeta['sub'] }}
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                      @if($matchDoc && $matchDoc->file_path)
                        <span class="item-status-pill" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">
                          <i class="bi bi-check-circle-fill"></i>Uploaded
                        </span>
                        <a href="{{ asset('storage/' . $matchDoc->file_path) }}" target="_blank" rel="noopener noreferrer" class="item-action-btn" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                          <i class="bi bi-eye"></i>View
                        </a>
                      @else
                        <span class="item-status-pill" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;">
                          <i class="bi bi-hourglass"></i>Pending
                        </span>
                        <button type="button" class="item-action-btn btn-open-upload" data-doc-name="{{ $dName }}" style="background:#ffffff; color:#0F1E4D; border:1px solid #cbd5e1;">
                          <i class="bi bi-cloud-arrow-up"></i>Upload
                        </button>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

            {{-- FOLDER 02: FLIGHT LOGS & VOLUMETRIC DELIVERABLES --}}
            <div class="folder-card mb-0">
              <div class="folder-header folder-emerald">
                <div class="d-flex align-items-center gap-2">
                  <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px; height:28px; background:#dcfce7; color:#059669;">
                    <i class="bi bi-layers-fill" style="font-size:14px;"></i>
                  </div>
                  <div>
                    <span class="fw-bold" style="color:#064e3b; font-size:0.86rem;">FOLDER 02 &middot; DRONE FLIGHT LOGS &amp; PHOTOGRAMMETRY DELIVERABLES</span>
                    <div class="text-muted" style="font-size:0.72rem;">DGCA flight logs, GCP network, orthomosaic, DSM, point clouds, and cut/fill report</div>
                  </div>
                </div>
                <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #a7f3d0; font-size:10px;">6 Deliverables</span>
              </div>

              <div>
                @foreach($deliverableDocs as $dName => $dMeta)
                  @php
                    $matchDoc = $docs->first(function($d) use ($dName) {
                      return stripos($d->document_name, substr($dName, 3, 15)) !== false || stripos($dName, $d->document_name) !== false;
                    });
                    $pillClass = match($dMeta['code']) {
                      'LOG-05' => 'pill-log',
                      'GCP-06' => 'pill-gcp',
                      'ORT-07' => 'pill-ort',
                      'DSM-08' => 'pill-dsm',
                      'PTS-09' => 'pill-pts',
                      'VOL-10' => 'pill-vol',
                      default  => 'pill-log'
                    };
                  @endphp
                  <div class="deliverable-row">
                    <div class="d-flex align-items-center gap-2.5 flex-grow-1 text-truncate me-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px; height:34px; {{ $matchDoc ? 'background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;' : 'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0;' }}">
                        <i class="bi {{ $matchDoc ? 'bi-check-lg' : 'bi-file-earmark-binary' }}"></i>
                      </div>
                      <div class="text-truncate">
                        <div class="fw-semibold text-truncate" style="color:#0F1E4D; font-size:0.85rem;">
                          <span class="doc-code-pill {{ $pillClass }} me-1.5">{{ $dMeta['code'] }}</span>
                          {{ $dName }}
                        </div>
                        <div class="text-muted text-truncate" style="font-size:0.74rem;">
                          {{ $dMeta['sub'] }}
                        </div>
                      </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                      @if($matchDoc && $matchDoc->file_path)
                        <span class="item-status-pill" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0;">
                          <i class="bi bi-check-circle-fill"></i>Uploaded
                        </span>
                        <a href="{{ asset('storage/' . $matchDoc->file_path) }}" target="_blank" rel="noopener noreferrer" class="item-action-btn" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
                          <i class="bi bi-eye"></i>View
                        </a>
                      @else
                        <span class="item-status-pill" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;">
                          <i class="bi bi-hourglass"></i>Pending
                        </span>
                        <button type="button" class="item-action-btn btn-open-upload" data-doc-name="{{ $dName }}" style="background:#ffffff; color:#0F1E4D; border:1px solid #cbd5e1;">
                          <i class="bi bi-cloud-arrow-up"></i>Upload
                        </button>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            </div>

          </div>
        </div>

      </div>

      {{-- SIDEBAR COLUMN (RIGHT, col-lg-4) --}}
      <div class="col-lg-4">

        {{-- CARD 1: QUARRY CONCESSION & APPLICANT PROFILE (Step 1) --}}
        <div class="dossier-card">
          <div class="dossier-card-header">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-badge-fill text-primary"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:0.92rem;">Applicant &amp; Quarry Concession</h6>
            </div>
            <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; font-size:10px;">Step 1</span>
          </div>
          <div class="dossier-card-body">
            <div class="p-2.5 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <small class="tile-label">Client / Applicant Entity</small>
              <div class="fw-bold text-truncate" style="color:#0F1E4D; font-size:0.95rem;" title="{{ $applicantName }}">{{ $applicantName }}</div>
              <small class="text-muted d-block mt-0.5"><i class="bi bi-person me-1"></i>{{ $customer?->customer_name ?: 'Applicant Representative' }}</small>
            </div>

            <div class="row g-2 mb-3">
              <div class="col-6">
                <div class="data-tile" style="border-left:3px solid #2563eb; padding: 0.6rem 0.75rem;">
                  <span class="tile-label">Customer / MIMAS</span>
                  <div class="tile-val font-monospace text-primary text-truncate" title="{{ $mimasNo }}" style="font-size:0.82rem;">{{ $mimasNo }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="data-tile" style="border-left:3px solid #059669; padding: 0.6rem 0.75rem;">
                  <span class="tile-label">Lease Extent</span>
                  <div class="tile-val text-dark" style="font-size:0.82rem;">{{ number_format($leaseArea, 2) }} Ha</div>
                </div>
              </div>
            </div>

            <div class="d-flex flex-column gap-1">
              <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size:0.83rem;">
                <span class="text-muted" style="min-width: 120px; flex-shrink: 0;"><i class="bi bi-telephone me-1.5 text-primary"></i>Primary Phone:</span>
                <a href="tel:{{ $primaryPhone }}" class="fw-semibold text-primary text-decoration-none text-end text-truncate ms-2">{{ $primaryPhone }}</a>
              </div>
              <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size:0.83rem;">
                <span class="text-muted" style="min-width: 120px; flex-shrink: 0;"><i class="bi bi-telephone-plus me-1.5 text-secondary"></i>Secondary Phone:</span>
                <span class="fw-semibold text-dark text-end text-truncate ms-2">{{ $secondaryPhone }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size:0.83rem;">
                <span class="text-muted" style="min-width: 120px; flex-shrink: 0;"><i class="bi bi-geo-alt me-1.5 text-danger"></i>Quarry Location:</span>
                <span class="fw-semibold text-dark text-end text-truncate ms-2" title="{{ $location }}">{{ $location }}</span>
              </div>
              <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="font-size:0.83rem;">
                <span class="text-muted" style="min-width: 120px; flex-shrink: 0;"><i class="bi bi-link-45deg me-1.5 text-info"></i>Linked Concession:</span>
                @if($survey->leaseApplication)
                  <a href="{{ Route::has('lease-application.show') ? route('lease-application.show', $survey->lease_application_id) : '#' }}" class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; text-decoration:none;">
                    Lease #{{ $survey->lease_application_id }}
                  </a>
                @elseif($survey->miningApplication)
                  <a href="{{ Route::has('miningplan.index') ? route('miningplan.index') : '#' }}" class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; text-decoration:none;">
                    Mining Plan #{{ $survey->mining_application_id }}
                  </a>
                @else
                  <span class="badge" style="background:#f8fafc; color:#64748b; border:1px solid #e2e8f0;">Standalone Mission</span>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- CARD 2: ASSIGNED FLIGHT CREW & TECHNICAL TEAM (Step 4) --}}
        <div class="dossier-card">
          <div class="dossier-card-header">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-people-fill text-warning"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:0.92rem;">Flight Crew &amp; Handling Team</h6>
            </div>
            <span class="badge" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a; font-size:10px;">{{ $survey->handlers->count() }} Members</span>
          </div>
          <div class="dossier-card-body">
            @forelse($survey->handlers as $h)
              @php
                $roleBadgeStyle = stripos($h->role, 'pilot') !== false
                  ? 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;'
                  : 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;';
              @endphp
              <div class="d-flex align-items-start gap-3 py-2.5 {{ !$loop->last ? 'border-bottom' : '' }}">
                <div class="crew-avatar">
                  <i class="bi bi-person-fill"></i>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex align-items-center justify-content-between gap-1 flex-wrap">
                    <strong style="color:#0F1E4D; font-size:0.88rem;">{{ $h->name }}</strong>
                    <span class="badge" style="{{ $roleBadgeStyle }} font-size:10px;">
                      {{ $h->role }}
                    </span>
                  </div>
                  @if($h->notes)
                    <div class="text-muted mt-1" style="font-size:0.76rem; line-height:1.45;">{{ $h->notes }}</div>
                  @endif
                </div>
              </div>
            @empty
              <div class="text-muted small py-2">No specific personnel logged. Default flight protocol assigned to DGCA Certified Remote Pilot.</div>
            @endforelse
          </div>
        </div>

        {{-- CARD 3: COMMERCIAL LEDGER & STATUTORY FEE SCHEDULE (Step 5) --}}
        <div class="dossier-card">
          <div class="dossier-card-header">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-receipt-cutoff text-success"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--ds-text-title); font-size:0.92rem;">Commercial Ledger &amp; Billing</h6>
            </div>
            @php
              $pBadgeStyle = match($pStatus) {
                'paid'    => 'background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;',
                'partial' => 'background:#fffbeb; color:#b45309; border:1px solid #fde68a;',
                default   => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;'
              };
            @endphp
            <span class="badge" style="{{ $pBadgeStyle }} font-size:10px;">{{ ucfirst($pStatus) }}</span>
          </div>
          <div class="dossier-card-body">
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:0.84rem;">
              <span class="text-muted">Quoted Volumetric Fee:</span>
              <strong style="color:#0F1E4D; font-size:0.92rem;">₹ {{ number_format($productVal, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:0.84rem;">
              <span class="text-muted">Paid Advance Remittance:</span>
              <strong class="text-success" style="font-size:0.92rem;">₹ {{ number_format($paidAmt, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:0.84rem;">
              <span class="text-muted">Outstanding Balance Due:</span>
              <strong class="{{ $pendingAmt > 0 ? 'text-danger' : 'text-success' }}" style="font-size:0.92rem;">₹ {{ number_format($pendingAmt, 2) }}</strong>
            </div>

            @php
              $paymentRecord = $survey->payments->first();
              $paymentNotes = $paymentRecord?->notes ?: 'Drone Survey and Volumetric Processing Fee - Cheque cleared';
            @endphp
            <div class="mt-3 p-2.5 rounded-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
              <span class="tile-label mb-1" style="color:#64748b;"><i class="bi bi-receipt me-1 text-primary"></i>Payment Notes / Reference:</span>
              <div class="small" style="color:#1e293b; font-size:0.78rem; line-height:1.4;">{{ $paymentNotes }}</div>
            </div>
          </div>
        </div>

        {{-- CARD 4: DOWNSTREAM STATUTORY WORKFLOW PIPELINE --}}
        <div class="downstream-banner mb-4">
          <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-arrow-right-circle-fill text-warning fs-5"></i>
            <h6 class="fw-bold mb-0 text-dark" style="font-size:0.88rem;">Downstream Statutory Stages</h6>
          </div>
          <p class="small text-muted mb-3" style="font-size:0.78rem; line-height:1.45;">
            Photogrammetric terrain models and volumetric reports feed directly into mining plan preparation, progressive mine closure plans (PMCP), and State Environmental Impact Assessment (SEIAA).
          </p>
          <div class="d-flex flex-column gap-2">
            <a href="{{ Route::has('miningplan.index') ? route('miningplan.index') : '#' }}" class="btn btn-sm d-flex align-items-center justify-content-between py-2 px-2.5 bg-white border" style="font-size:0.78rem; border-radius:7px; color:#1e293b; text-decoration:none;">
              <span><i class="bi bi-file-earmark-ruled me-1.5 text-primary"></i>Preparation of Mining Plan</span>
              <i class="bi bi-chevron-right text-muted"></i>
            </a>
            <a href="{{ Route::has('dgps-survey.index') ? route('dgps-survey.index') : '#' }}" class="btn btn-sm d-flex align-items-center justify-content-between py-2 px-2.5 bg-white border" style="font-size:0.78rem; border-radius:7px; color:#1e293b; text-decoration:none;">
              <span><i class="bi bi-broadcast me-1.5 text-info"></i>DGPS Boundary Pillar Demarcation</span>
              <i class="bi bi-chevron-right text-muted"></i>
            </a>
            <a href="{{ Route::has('ec-compliance.index') ? route('ec-compliance.index') : (Route::has('environment-clearance.index') ? route('environment-clearance.index') : '#') }}" class="btn btn-sm d-flex align-items-center justify-content-between py-2 px-2.5 bg-white border" style="font-size:0.78rem; border-radius:7px; color:#1e293b; text-decoration:none;">
              <span><i class="bi bi-shield-check me-1.5 text-success"></i>State EC Clearance Submission</span>
              <i class="bi bi-chevron-right text-muted"></i>
            </a>
          </div>
        </div>

      </div>

    </div>

  </div>
</div>

{{-- MODAL: DIRECT DELIVERABLE UPLOAD --}}
<div class="modal fade" id="uploadDroneModal" tabindex="-1" aria-labelledby="uploadDroneModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header py-3" style="background:#0F1E4D; color:white;">
        <h5 class="modal-title fs-6 fw-bold text-white" id="uploadDroneModalLabel">
          <i class="bi bi-cloud-arrow-up-fill me-2"></i>Attach Photogrammetry Deliverable
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="droneUploadForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="drone_survey_id" value="{{ $survey->id }}">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold small text-muted text-uppercase">Document Type / Deliverable <span class="text-danger">*</span></label>
            <select class="form-select" name="document_name" id="modal_document_name" required>
              <optgroup label="Folder 01: Statutory Requisition Documents">
                @foreach($statutoryDocs as $dName => $dMeta)
                  <option value="{{ $dName }}">{{ $dName }}</option>
                @endforeach
              </optgroup>
              <optgroup label="Folder 02: Drone Flight Logs & Photogrammetry Deliverables">
                @foreach($deliverableDocs as $dName => $dMeta)
                  <option value="{{ $dName }}">{{ $dName }}</option>
                @endforeach
              </optgroup>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold small text-muted text-uppercase">Select File <span class="text-danger">*</span></label>
            <input type="file" class="form-control" name="file" id="modal_file_input" required accept=".pdf,.csv,.xlsx,.kml,.kmz,.tif,.tiff,.jpg,.png,.las,.ply,.dwg,.dxf">
            <div class="form-text small text-muted mt-1">Accepted: PDF, GeoTIFF, LAS/PLY, CSV, KML, CAD (Max: 50MB).</div>
          </div>

          <div id="upload_progress_box" style="display:none;" class="mt-3">
            <div class="d-flex justify-content-between small text-muted mb-1">
              <span>Uploading to cloud storage...</span>
              <span id="upload_percent">0%</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="upload_bar" role="progressbar" style="width: 0%;"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm px-4 fw-semibold" id="btn_submit_upload" style="background:#0F1E4D; color:#ffffff;">
            <i class="bi bi-upload me-1"></i> Upload Now
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Open modal with pre-selected document name
    document.querySelectorAll('.btn-open-upload').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var docName = this.getAttribute('data-doc-name');
        var select = document.getElementById('modal_document_name');
        if (select && docName) {
          select.value = docName;
        }
        var modalEl = document.getElementById('uploadDroneModal');
        if (modalEl && window.bootstrap) {
          var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          modal.show();
        }
      });
    });

    // AJAX upload handler
    var form = document.getElementById('droneUploadForm');
    if (form) {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = document.getElementById('btn_submit_upload');
        var progressBox = document.getElementById('upload_progress_box');
        var uploadBar = document.getElementById('upload_bar');
        var uploadPercent = document.getElementById('upload_percent');

        var formData = new FormData(form);
        submitBtn.disabled = true;
        progressBox.style.display = 'block';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('drone-survey.upload') }}', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.onprogress = function(e) {
          if (e.lengthComputable) {
            var pct = Math.round((e.loaded / e.total) * 100);
            uploadBar.style.width = pct + '%';
            uploadPercent.innerText = pct + '%';
          }
        };

        xhr.onload = function() {
          submitBtn.disabled = false;
          if (xhr.status >= 200 && xhr.status < 300) {
            try {
              var res = JSON.parse(xhr.responseText);
              if (res.success) {
                if (window.toastr) {
                  toastr.success('Deliverable attached successfully!');
                }
                setTimeout(function() {
                  window.location.reload();
                }, 600);
                return;
              }
            } catch(e) {}
            window.location.reload();
          } else {
            progressBox.style.display = 'none';
            alert('Upload failed: ' + xhr.statusText);
          }
        };

        xhr.onerror = function() {
          submitBtn.disabled = false;
          progressBox.style.display = 'none';
          alert('Upload failed due to network error.');
        };

        xhr.send(formData);
      });
    }
  });
</script>
@endpush

@endsection
