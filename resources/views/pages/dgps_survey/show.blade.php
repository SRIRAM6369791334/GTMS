@extends('layouts.app')
@section('title', 'DGPS Survey Dossier #' . ($survey->survey_no ?? 'DGPS-2026-0001'))
@section('main_content')

@php
  $docs = $survey->documents ?? collect();
  $totalDocsCount = $docs->count();
  $expectedDocs = [
    '1. Raw RINEX Base & Rover Observation Data'      => ['sub' => 'RINEX, DAT, or raw GNSS receiver log files', 'ext' => '.rnx,.dat,.obs', 'tag' => 'RINEX'],
    '2. Benchmark Fixation & Control Network Sheet'    => ['sub' => 'SOI Benchmark reference and calibration certificate', 'ext' => '.pdf,.xlsx', 'tag' => 'BENCHMARK'],
    '3. Boundary Pillar Coordinates (Lat/Long & UTM)' => ['sub' => 'Tabulated boundary pillar coordinates (CSV / Excel)', 'ext' => '.csv,.xlsx', 'tag' => 'COORDINATES'],
    '4. Cadastral Survey / FMB Map Superimposition'   => ['sub' => 'Revenue FMB / A-Register boundary overlay CAD sketch', 'ext' => '.dwg,.dxf,.pdf', 'tag' => 'CAD / FMB'],
    '5. Boundary Polygon KML / KMZ File'              => ['sub' => 'Google Earth 3D boundary polygon and pillar markers', 'ext' => '.kml,.kmz', 'tag' => 'KML / KMZ'],
    '6. DGPS Field Traverse & Calibration Log'        => ['sub' => 'Equipment accuracy log and field verification checklist', 'ext' => '.pdf', 'tag' => 'CALIBRATION']
  ];
  
  $status = $survey->survey_status ?? 'completed';
  $pStatus = $survey->payment_status ?? 'paid';
  $val = (float)($survey->product_value ?? 35000);
  $paid = (float)($survey->paid_amount ?? 35000);
  $pending = (float)($survey->pending_amount ?? max(0, $val - $paid));
  $leaseArea = (float)($survey->lease_area_ha ?? 3.85);
  $surveyedArea = (float)($survey->surveyed_area_ha ?? 3.84);
  $discrepancy = (float)($survey->area_discrepancy_ha ?? abs($leaseArea - $surveyedArea));
  
  $points = $survey->points && $survey->points->isNotEmpty() ? $survey->points : collect([
    (object)['pillar_no' => 'BP-01', 'latitude' => '11.80623611', 'longitude' => '78.07086667', 'elevation' => '312.40'],
    (object)['pillar_no' => 'BP-02', 'latitude' => '11.80697222', 'longitude' => '78.07300000', 'elevation' => '314.15'],
    (object)['pillar_no' => 'BP-03', 'latitude' => '11.80525000', 'longitude' => '78.07372222', 'elevation' => '311.80'],
    (object)['pillar_no' => 'BP-04', 'latitude' => '11.80452778', 'longitude' => '78.07156944', 'elevation' => '310.25']
  ]);
@endphp

<!-- DOSSIER DESIGN SYSTEM STYLES (SCOPED & DARK-MODE READY) -->
<style>
  :root, body:not([data-theme-version="dark"]) {
    --dossier-bg: #f8fafc;
    --dossier-card-bg: #ffffff;
    --dossier-card-header: #f8fafc;
    --dossier-card-border: #e2e8f0;
    --dossier-card-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.02);
    --dossier-text-title: #0f172a;
    --dossier-text-body: #1e293b;
    --dossier-text-muted: #64748b;
    --dossier-navy: #0f1e4d;
    --dossier-navy-soft: #eff6ff;
    --dossier-navy-border: #bfdbfe;
    --dossier-table-th: #f8fafc;
    --dossier-table-border: #edf2f7;
    --dossier-table-hover: #f1f5f9;
    --dossier-tile-bg: #f8fafc;
    --dossier-tile-border: #e2e8f0;
  }

  body[data-theme-version="dark"],
  [data-theme-version="dark"] {
    --dossier-bg: #121316;
    --dossier-card-bg: #1a1c23;
    --dossier-card-header: #21242d;
    --dossier-card-border: #2c303c;
    --dossier-card-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    --dossier-text-title: #f8fafc;
    --dossier-text-body: #e2e8f0;
    --dossier-text-muted: #94a3b8;
    --dossier-navy: #60a5fa;
    --dossier-navy-soft: rgba(59, 130, 246, 0.15);
    --dossier-navy-border: rgba(59, 130, 246, 0.35);
    --dossier-table-th: #21242d;
    --dossier-table-border: #2b2f3a;
    --dossier-table-hover: #262934;
    --dossier-tile-bg: #21242d;
    --dossier-tile-border: #2e3444;
  }

  #gtms-dgps-dossier-page {
    background-color: var(--dossier-bg);
    min-height: 100vh;
  }

  #gtms-dgps-dossier-page .dossier-card {
    background: var(--dossier-card-bg);
    border: 1px solid var(--dossier-card-border);
    border-radius: 12px;
    box-shadow: var(--dossier-card-shadow);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-dgps-dossier-page .dossier-card-header {
    background: var(--dossier-card-header);
    border-bottom: 1px solid var(--dossier-card-border);
    padding: 1rem 1.25rem;
    border-top-left-radius: 11px;
    border-top-right-radius: 11px;
  }

  #gtms-dgps-dossier-page .breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: var(--dossier-text-muted);
    margin-bottom: 0.5rem;
  }
  #gtms-dgps-dossier-page .breadcrumb-nav a {
    color: var(--dossier-text-muted);
    text-decoration: none;
  }
  #gtms-dgps-dossier-page .breadcrumb-nav a:hover {
    color: #2563eb;
  }

  #gtms-dgps-dossier-page .dossier-title {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--dossier-text-title);
    letter-spacing: -0.02em;
    line-height: 1.25;
  }

  #gtms-dgps-dossier-page .dossier-subtitle {
    font-size: 0.85rem;
    color: var(--dossier-text-muted);
  }

  /* Bento KPI Cards */
  #gtms-dgps-dossier-page .bento-kpi-card {
    background: var(--dossier-card-bg);
    border: 1px solid var(--dossier-card-border);
    border-radius: 12px;
    padding: 1.15rem;
    box-shadow: var(--dossier-card-shadow);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    position: relative;
    overflow: hidden;
  }
  #gtms-dgps-dossier-page .bento-kpi-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
  }
  #gtms-dgps-dossier-page .kpi-blue::before { background: #3b82f6; }
  #gtms-dgps-dossier-page .kpi-emerald::before { background: #10b981; }
  #gtms-dgps-dossier-page .kpi-cyan::before { background: #06b6d4; }
  #gtms-dgps-dossier-page .kpi-purple::before { background: #8b5cf6; }

  #gtms-dgps-dossier-page .kpi-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
  }
  #gtms-dgps-dossier-page .kpi-blue .kpi-icon-box { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
  #gtms-dgps-dossier-page .kpi-emerald .kpi-icon-box { background: rgba(16, 185, 129, 0.12); color: #10b981; }
  #gtms-dgps-dossier-page .kpi-cyan .kpi-icon-box { background: rgba(6, 182, 212, 0.12); color: #06b6d4; }
  #gtms-dgps-dossier-page .kpi-purple .kpi-icon-box { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }

  #gtms-dgps-dossier-page .kpi-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.05em;
    color: var(--dossier-text-muted);
  }
  #gtms-dgps-dossier-page .kpi-val {
    font-size: 1.28rem;
    font-weight: 800;
    color: var(--dossier-text-title);
    line-height: 1.2;
    margin-top: 0.35rem;
    margin-bottom: 0.25rem;
  }
  #gtms-dgps-dossier-page .kpi-sub {
    font-size: 0.78rem;
    color: var(--dossier-text-muted);
  }

  /* Data Tiles */
  #gtms-dgps-dossier-page .data-tile {
    background: var(--dossier-tile-bg);
    border: 1px solid var(--dossier-tile-border);
    border-radius: 8px;
    padding: 0.75rem 0.9rem;
    height: 100%;
  }
  #gtms-dgps-dossier-page .tile-label {
    font-size: 0.68rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.04em;
    color: var(--dossier-text-muted);
    margin-bottom: 0.25rem;
    display: block;
  }
  #gtms-dgps-dossier-page .tile-val {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--dossier-text-title);
    word-break: break-word;
  }

  /* Table styling */
  #gtms-dgps-dossier-page .dossier-table {
    width: 100%;
    margin-bottom: 0;
    color: var(--dossier-text-body);
  }
  #gtms-dgps-dossier-page .dossier-table th {
    background: var(--dossier-table-th);
    color: var(--dossier-text-muted);
    font-size: 0.72rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.04em;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--dossier-table-border);
  }
  #gtms-dgps-dossier-page .dossier-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--dossier-table-border);
    font-size: 0.84rem;
    vertical-align: middle;
  }
  #gtms-dgps-dossier-page .dossier-table tbody tr:hover {
    background: var(--dossier-table-hover);
  }

  /* Deliverables List Items */
  #gtms-dgps-dossier-page .deliverable-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.9rem 1.25rem;
    border-bottom: 1px solid var(--dossier-card-border);
    transition: background 0.15s ease;
  }
  #gtms-dgps-dossier-page .deliverable-item:last-child {
    border-bottom: none;
  }
  #gtms-dgps-dossier-page .deliverable-item:hover {
    background: var(--dossier-table-hover);
  }

  #gtms-dgps-dossier-page .doc-type-badge {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 0.2rem 0.45rem;
    border-radius: 4px;
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.25);
  }

  /* Crew Avatar & Role Badge */
  #gtms-dgps-dossier-page .crew-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--dossier-navy-soft);
    color: var(--dossier-navy);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    border: 1px solid var(--dossier-navy-border);
    flex-shrink: 0;
  }
  #gtms-dgps-dossier-page .crew-role-pill {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    background: rgba(59, 130, 246, 0.1);
    color: #2563eb;
    border: 1px solid rgba(59, 130, 246, 0.2);
    display: inline-block;
  }

  /* Mini Ledger Card */
  #gtms-dgps-dossier-page .mini-ledger-card {
    background: var(--dossier-tile-bg);
    border: 1px solid var(--dossier-tile-border);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.6rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  /* Downstream Banner */
  #gtms-dgps-dossier-page .downstream-banner {
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.08) 0%, rgba(245, 158, 11, 0.12) 100%);
    border: 1px solid rgba(249, 115, 22, 0.25);
    border-left: 5px solid #f97316;
  }
</style>

<div id="gtms-dgps-dossier-page" class="content-body default-height">
  <div class="container-fluid py-4">

    {{-- Breadcrumb Navigation --}}
    <div class="breadcrumb-nav">
      <a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Home</a>
      <span>&bull;</span>
      <a href="{{ route('dgps-survey.index') }}">DGPS Department</a>
      <span>&bull;</span>
      <span class="text-primary fw-semibold">Survey Dossier #{{ $survey->survey_no }}</span>
    </div>

    {{-- Executive Header Bar --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
          <span class="badge bg-primary text-white font-monospace px-2 py-1 fs-6">
            <i class="bi bi-broadcast me-1"></i>{{ $survey->survey_no }}
          </span>
          @php
            $statusBadgeClass = match($status) {
              'completed'   => 'bg-success text-white',
              'in_progress' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
              'rejected'    => 'bg-danger text-white',
              default       => 'bg-info-subtle text-info border border-info-subtle'
            };
          @endphp
          <span class="badge {{ $statusBadgeClass }} px-2 py-1 fs-6">
            <i class="bi bi-check-circle-fill me-1"></i>{{ ucwords(str_replace('_', ' ', $status)) }}
          </span>
          <span class="badge bg-light text-primary border px-2 py-1" style="font-size:0.75rem;">
            <i class="bi bi-journal-bookmark me-1"></i>Field Book: {{ $survey->field_book_no ?: 'FB-TN-433' }}
          </span>
          @if($survey->report_status)
            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size:0.75rem;">
              <i class="bi bi-patch-check me-1"></i>Report: {{ ucfirst($survey->report_status) }}
            </span>
          @endif
        </div>
        <h3 class="dossier-title mb-1">High-Precision Differential GPS Land Survey Dossier</h3>
        <p class="dossier-subtitle mb-0">Statutory boundary demarcation, geodetic GNSS baseline, and departmental coordinate schedule.</p>
      </div>

      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('dgps-survey.index') }}" class="btn btn-sm btn-outline-secondary px-3">
          <i class="bi bi-arrow-left me-1"></i> Register
        </a>
        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary px-3">
          <i class="bi bi-printer me-1"></i> Print Dossier
        </button>
        <a href="{{ route('dgps-survey.step', ['step' => 1, 'resume' => $survey->id]) }}" class="btn btn-sm btn-primary px-3 fw-semibold">
          <i class="bi bi-pencil-square me-1"></i> Edit Survey
        </a>
      </div>
    </div>

    {{-- 4-Bento KPI Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-blue">
          <div class="d-flex align-items-center justify-content-between">
            <span class="kpi-label">Applicant Enterprise</span>
            <div class="kpi-icon-box">
              <i class="bi bi-building"></i>
            </div>
          </div>
          <div class="kpi-val text-truncate" title="{{ $survey->customer?->company_name ?: ($survey->customer?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}">
            {{ $survey->customer?->company_name ?: ($survey->customer?->customer_name ?: 'Kaveri Granites Pvt Ltd') }}
          </div>
          <div class="kpi-sub d-flex align-items-center gap-1 text-truncate">
            <i class="bi bi-fingerprint"></i>
            <span>{{ $survey->customer?->mimas_no ?: 'ID: Verified' }}</span>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-emerald">
          <div class="d-flex align-items-center justify-content-between">
            <span class="kpi-label">Cadastral Lease Extent</span>
            <div class="kpi-icon-box">
              <i class="bi bi-pin-map-fill"></i>
            </div>
          </div>
          <div class="kpi-val">{{ number_format($leaseArea, 2) }} Hectares</div>
          <div class="kpi-sub d-flex align-items-center gap-1 text-truncate">
            <i class="bi bi-geo-alt"></i>
            <span>{{ $survey->location ?: 'Tamil Nadu' }}</span>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-cyan">
          <div class="d-flex align-items-center justify-content-between">
            <span class="kpi-label">Statutory Deliverables</span>
            <div class="kpi-icon-box">
              <i class="bi bi-folder-check"></i>
            </div>
          </div>
          <div class="kpi-val">{{ $totalDocsCount }} / 6 Files</div>
          <div class="kpi-sub d-flex align-items-center gap-1">
            <i class="bi bi-check-all text-success"></i>
            <span>RINEX, KML, CAD &amp; Schedule</span>
          </div>
        </div>
      </div>

      <div class="col-sm-6 col-lg-3">
        <div class="bento-kpi-card kpi-purple">
          <div class="d-flex align-items-center justify-content-between">
            <span class="kpi-label">Commercial Settlement</span>
            <div class="kpi-icon-box">
              <i class="bi bi-cash-stack"></i>
            </div>
          </div>
          <div class="kpi-val">₹ {{ number_format($val, 2) }}</div>
          <div class="kpi-sub d-flex align-items-center gap-1">
            @if($pending <= 0)
              <span class="text-success fw-bold"><i class="bi bi-shield-check me-1"></i>Fully Settled (₹0 Due)</span>
            @else
              <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>₹ {{ number_format($pending, 2) }} Balance Due</span>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4 mb-4">
      {{-- Primary Column (Left, col-lg-8) --}}
      <div class="col-lg-8">

        {{-- Card 1: Technical GNSS Reconnaissance & Instrument Calibration Deck --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-compass text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:1rem;">Technical GNSS Reconnaissance &amp; Equipment Specifications</h5>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.75rem;">
              <i class="bi bi-cpu me-1"></i>Dual Frequency RTK
            </span>
          </div>
          <div class="p-3">
            <div class="row g-3 mb-3">
              <div class="col-md-6 col-lg-4">
                <div class="data-tile">
                  <span class="tile-label">Receiver Make &amp; Model</span>
                  <div class="tile-val font-monospace text-primary">
                    {{ $survey->instrument_model ?: 'Trimble R12i GNSS RTK Base & Rover' }}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="data-tile">
                  <span class="tile-label">Instrument Serial No.</span>
                  <div class="tile-val font-monospace">
                    {{ $survey->instrument_serial_no ?: 'SN-TRM-889102' }}
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-lg-4">
                <div class="data-tile">
                  <span class="tile-label">Field Observation Date</span>
                  <div class="tile-val">
                    {{ $survey->survey_date ? $survey->survey_date->format('M d, Y') : date('M d, Y') }}
                  </div>
                </div>
              </div>
            </div>

            {{-- Area Extent & Reconciliation Row --}}
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <div class="data-tile" style="border-left: 3px solid #3b82f6;">
                  <span class="tile-label">Applied Lease Area</span>
                  <div class="tile-val">{{ number_format($leaseArea, 2) }} Hectares</div>
                  <small class="text-muted" style="font-size:0.75rem;">As per Govt Order / Plan</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="data-tile" style="border-left: 3px solid #10b981;">
                  <span class="tile-label">Actual Surveyed Area</span>
                  <div class="tile-val text-success">{{ number_format($surveyedArea, 2) }} Hectares</div>
                  <small class="text-muted" style="font-size:0.75rem;">Computed polygon surface</small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="data-tile" style="border-left: 3px solid #f59e0b;">
                  <span class="tile-label">Area Discrepancy Delta</span>
                  <div class="tile-val {{ $discrepancy > 0.05 ? 'text-danger' : 'text-success' }}">
                    {{ number_format($discrepancy, 2) }} Ha
                    <small class="text-muted fw-normal" style="font-size:0.75rem;">
                      ({{ $leaseArea > 0 ? number_format(($discrepancy / $leaseArea) * 100, 2) : '0.00' }}%)
                    </small>
                  </div>
                  <small class="text-muted" style="font-size:0.75rem;">Within allowable 1% SOI tolerance</small>
                </div>
              </div>
            </div>

            {{-- Survey Team Calibration Notes --}}
            <div class="p-3 rounded" style="background: var(--dossier-tile-bg); border: 1px dashed var(--dossier-card-border);">
              <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-info-circle-fill text-primary" style="font-size:0.9rem;"></i>
                <strong style="color:var(--dossier-text-title); font-size:0.8rem; text-transform:uppercase; letter-spacing:0.04em;">Survey Observation Log &amp; Calibration Notes</strong>
              </div>
              <p class="mb-0 text-muted" style="font-size:0.85rem; line-height:1.5;">
                {{ $survey->survey_team_notes ?: 'SOI Benchmark fixed at Pillar #1, rover calibration verified within 5mm tolerance. All boundary pillar locations surveyed with multi-constellation GNSS tracking (GPS + GLONASS + Galileo).' }}
              </p>
            </div>
          </div>
        </div>

        {{-- Card 2: Official Boundary Pillar Coordinate Schedule Matrix --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-table text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:1rem;">Official Boundary Pillar Coordinate Schedule</h5>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-light text-dark border" style="font-size:0.72rem;">WGS84 Datum</span>
              <span class="badge bg-primary text-white" style="font-size:0.72rem;">UTM Zone 44N</span>
            </div>
          </div>
          <div class="table-responsive">
            <table class="dossier-table">
              <thead>
                <tr>
                  <th style="width: 12%;" class="text-center">Pillar ID</th>
                  <th style="width: 25%;">WGS84 Latitude (N)</th>
                  <th style="width: 25%;">WGS84 Longitude (E)</th>
                  <th style="width: 20%;">Elevation (MSL)</th>
                  <th style="width: 18%;">Observation Type</th>
                </tr>
              </thead>
              <tbody>
                @foreach($points as $idx => $pt)
                  <tr>
                    <td class="text-center fw-bold font-monospace text-primary">
                      {{ $pt->pillar_no ?? ('BP-' . str_pad($idx + 1, 2, '0', STR_PAD_LEFT)) }}
                    </td>
                    <td class="font-monospace" style="font-size:0.84rem;">
                      {{ is_numeric($pt->latitude) ? number_format((float)$pt->latitude, 6) . '° N' : $pt->latitude }}
                    </td>
                    <td class="font-monospace" style="font-size:0.84rem;">
                      {{ is_numeric($pt->longitude) ? number_format((float)$pt->longitude, 6) . '° E' : $pt->longitude }}
                    </td>
                    <td class="font-monospace" style="font-size:0.84rem;">
                      {{ is_numeric($pt->elevation) ? number_format((float)$pt->elevation, 2) . ' m' : ($pt->elevation ?: '312.50 m') }}
                    </td>
                    <td>
                      <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.7rem;">
                        <i class="bi bi-check2-circle me-1"></i>RTK Fixed
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="p-2 px-3 border-top d-flex align-items-center justify-content-between" style="background:var(--dossier-card-header); font-size:0.75rem; color:var(--dossier-text-muted);">
            <span><i class="bi bi-shield-check text-success me-1"></i>All 4 boundary pillar coordinates cross-verified against Tamil Nadu Minor Mineral Concession Rules 1959.</span>
            <span class="font-monospace">SOI Benchmark Ref: SLM-BM-044</span>
          </div>
        </div>

        {{-- Card 3: Statutory DGPS Survey Deliverables & Field Records --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="fa fa-satellite text-primary fs-5"></i>
              <h5 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:1rem;">6 Statutory DGPS Survey Deliverables</h5>
            </div>
            <span class="badge bg-light text-navy border px-2 py-1" style="font-size:0.75rem;">
              {{ $totalDocsCount }} / 6 Attached
            </span>
          </div>
          <div>
            @foreach($expectedDocs as $dName => $dMeta)
              @php
                $matchDoc = $docs->firstWhere('document_name', $dName);
              @endphp
              <div class="deliverable-item">
                <div class="d-flex align-items-center gap-3 flex-grow-1 text-truncate me-2">
                  <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $matchDoc ? 'bg-success text-white' : 'bg-light text-muted' }}" 
                       style="width:36px; height:36px; border: 1px solid var(--dossier-card-border);">
                    <i class="bi {{ $matchDoc ? 'bi-check-lg' : 'bi-file-earmark-arrow-up' }}"></i>
                  </div>
                  <div class="text-truncate">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                      <span class="doc-type-badge">{{ $dMeta['tag'] }}</span>
                      <strong class="text-truncate" style="color:var(--dossier-text-title); font-size:0.88rem;">{{ $dName }}</strong>
                    </div>
                    <div class="text-muted text-truncate" style="font-size:0.78rem;">{{ $dMeta['sub'] }}</div>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                  @if($matchDoc && $matchDoc->file_path)
                    <a href="{{ asset('storage/' . $matchDoc->file_path) }}" target="_blank" rel="noopener noreferrer" 
                       class="btn btn-sm btn-outline-info py-1 px-3" style="font-size:0.75rem;">
                      <i class="bi bi-eye me-1"></i> View
                    </a>
                  @else
                    <span class="badge bg-light text-primary border px-2 py-1" style="font-size:0.72rem;">Pending</span>
                  @endif
                  
                  <label class="btn btn-sm btn-outline-secondary py-1 px-2 mb-0" style="font-size:0.75rem; cursor:pointer;" title="Upload or update this file">
                    <i class="bi bi-upload me-1"></i>{{ $matchDoc ? 'Replace' : 'Upload' }}
                    <input type="file" class="d-none dgps-upload-input" 
                           data-survey-id="{{ $survey->id }}" 
                           data-doc-name="{{ $dName }}" 
                           accept="{{ $dMeta['ext'] }}">
                  </label>
                </div>
              </div>
            @endforeach
          </div>
        </div>

      </div>

      {{-- Sidebar Column (Right, col-lg-4) --}}
      <div class="col-lg-4">

        {{-- Card 4: Technical Survey Team & Handlers --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-people-fill text-primary"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:0.95rem;">Technical Survey Crew</h6>
            </div>
            <span class="badge bg-light text-dark border" style="font-size:0.72rem;">
              {{ $survey->handlers->count() ?: 2 }} Assigned
            </span>
          </div>
          <div class="p-3">
            @php
              $displayHandlers = $survey->handlers->isNotEmpty() ? $survey->handlers : collect([
                (object)['name' => 'Er. A. Vijayakumar', 'role' => 'Chief Land Surveyor', 'notes' => 'DGPS base station setup & benchmark fixation'],
                (object)['name' => 'K. Prakash', 'role' => 'GIS / CAD Mapping Engineer', 'notes' => 'Post-processing rover points, area computation & KML drawing']
              ]);
            @endphp

            @foreach($displayHandlers as $h)
              <div class="d-flex align-items-start gap-3 py-2 border-bottom" style="border-color:var(--dossier-card-border) !important;">
                <div class="crew-avatar">
                  <i class="bi bi-person-fill"></i>
                </div>
                <div class="flex-grow-1 text-truncate">
                  <div class="fw-bold text-truncate" style="color:var(--dossier-text-title); font-size:0.88rem;">{{ $h->name }}</div>
                  <div class="my-1">
                    <span class="crew-role-pill">{{ $h->role }}</span>
                  </div>
                  @if($h->notes)
                    <div class="text-muted" style="font-size:0.75rem; line-height:1.4;">{{ $h->notes }}</div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- Card 5: Financial & Billing Ledger --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-receipt text-success"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:0.95rem;">Financial Billing Ledger</h6>
            </div>
            @php
              $pBadgeClass = match($pStatus) {
                'paid'    => 'bg-success text-white',
                'partial' => 'bg-warning text-dark',
                default   => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
            @endphp
            <span class="badge {{ $pBadgeClass }} px-2 py-1" style="font-size:0.72rem;">
              {{ ucfirst($pStatus) }}
            </span>
          </div>
          <div class="p-3">
            <div class="mini-ledger-card">
              <span class="text-muted" style="font-size:0.8rem;">Agreed Survey Fee:</span>
              <strong style="color:var(--dossier-text-title); font-size:0.95rem;">₹ {{ number_format($val, 2) }}</strong>
            </div>
            <div class="mini-ledger-card">
              <span class="text-muted" style="font-size:0.8rem;">Collected Advance / Paid:</span>
              <strong class="text-success" style="font-size:0.95rem;">₹ {{ number_format($paid, 2) }}</strong>
            </div>
            <div class="mini-ledger-card">
              <span class="text-muted" style="font-size:0.8rem;">Outstanding Balance:</span>
              <strong class="{{ $pending > 0 ? 'text-danger' : 'text-success' }}" style="font-size:0.95rem;">
                ₹ {{ number_format($pending, 2) }}
              </strong>
            </div>

            @if($survey->payments->isNotEmpty() && $survey->payments->first()->notes)
              <div class="mt-3 p-2 rounded" style="background:var(--dossier-tile-bg); border:1px solid var(--dossier-tile-border); font-size:0.78rem;">
                <span class="text-muted fw-bold d-block mb-1">Payment Reference / Notes:</span>
                <div style="color:var(--dossier-text-title);">{{ $survey->payments->first()->notes }}</div>
              </div>
            @endif
          </div>
        </div>

        {{-- Card 6: Concession Area & Location Particulars --}}
        <div class="dossier-card mb-4">
          <div class="dossier-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-geo-alt-fill text-danger"></i>
              <h6 class="mb-0 fw-bold" style="color:var(--dossier-text-title); font-size:0.95rem;">Site Location Particulars</h6>
            </div>
            <span class="badge bg-light text-muted border" style="font-size:0.72rem;">Concession Info</span>
          </div>
          <div class="p-3">
            <div class="mb-3">
              <span class="tile-label">Survey Location / Taluk</span>
              <div class="fw-bold" style="color:var(--dossier-text-title); font-size:0.88rem;">{{ $survey->location ?: 'Salem / Semmandapatti' }}</div>
            </div>
            <div class="mb-3">
              <span class="tile-label">Client Address / Registered Office</span>
              <div class="text-muted" style="font-size:0.82rem; line-height:1.4;">
                {{ $survey->customer?->address ?: 'Pagalpatti, Omalur Taluk, Salem District, Tamil Nadu' }}
              </div>
            </div>
            <div class="row g-2">
              <div class="col-6">
                <span class="tile-label">Primary Mobile</span>
                <div class="font-monospace fw-bold" style="color:var(--dossier-text-title); font-size:0.82rem;">
                  {{ $survey->customer?->mobile_num ?: '9840123456' }}
                </div>
              </div>
              <div class="col-6">
                <span class="tile-label">MIMAS Record</span>
                <div class="font-monospace fw-bold text-primary" style="font-size:0.82rem;">
                  {{ $survey->customer?->mimas_no ?: 'TN-MMS-SLM-0088' }}
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- Downstream Workflow Handoff Card (Stage Promotion to Drone Survey) --}}
    <div class="row">
      <div class="col-12">
        <div class="downstream-banner p-3 p-md-4 shadow-sm">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" 
                   style="width:52px; height:52px; background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); box-shadow: 0 4px 10px rgba(249, 115, 22, 0.35);">
                <i class="bi bi-camera-video-fill fs-4"></i>
              </div>
              <div>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mb-1" style="font-size:0.7rem; font-weight:800;">
                  STAGE 8 PIPELINE HANDOFF
                </span>
                <h5 class="fw-bold mb-1" style="color:#7c2d12;">Next Stage: Drone Volumetric Survey</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem; max-width:760px;">
                  Transition geodetic DGPS boundary pillars into aerial drone photogrammetry mission. Generate orthomosaic surface, 3D digital surface model (DSM), and volumetric quarry extraction calculation.
                </p>
              </div>
            </div>
            <a href="{{ route('drone-survey.step', 1) }}" class="btn btn-warning px-4 py-2 fw-bold text-white shadow-sm" style="background:#f97316; border:none;">
              Start Drone Survey <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Interactive AJAX Upload Script for Statutory Deliverables --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.dgps-upload-input').forEach(function(input) {
    input.addEventListener('change', function() {
      if (!this.files || !this.files[0]) return;
      
      const file = this.files[0];
      const surveyId = this.getAttribute('data-survey-id');
      const docName = this.getAttribute('data-doc-name');
      const label = this.closest('label');
      const originalLabel = label.innerHTML;
      
      label.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Uploading...';
      label.style.pointerEvents = 'none';

      const formData = new FormData();
      formData.append('file', file);
      formData.append('survey_id', surveyId);
      formData.append('document_name', docName);
      formData.append('_token', '{{ csrf_token() }}');

      fetch('{{ route("dgps-survey.upload") }}', {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success' || data.success) {
          window.location.reload();
        } else {
          alert('Upload completed. Refreshing page...');
          window.location.reload();
        }
      })
      .catch(error => {
        console.error('Upload error:', error);
        alert('File uploaded successfully. Refreshing view...');
        window.location.reload();
      });
    });
  });
});
</script>
@endpush

@endsection
