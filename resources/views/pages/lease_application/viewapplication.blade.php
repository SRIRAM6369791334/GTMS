@extends('layouts.app')
@section('title', 'Lease Application Dossier')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="content">

      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('warning'))
      <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
        <div>{{ session('warning') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
        <div>{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      <div class="page-head">
        <div>
          <div class="breadcrumb-min mb-1"><a href="{{ route('application.index') }}">Applications</a> &nbsp;/&nbsp; {{ $application->application_no ?? 'LA-2026-0001' }}</div>
          <h4>Application #{{ $application->application_no ?? 'N/A' }} <span class="text-muted fw-normal">— {{ $application->customer?->company_name ?? $application->customer?->customer_name ?? 'N/A' }}</span></h4>
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="status-pill"><span class="blip"></span>{{ ucfirst(str_replace('_', ' ', $application->status ?? 'Under Scrutiny')) }}</span>
            <span class="badge bg-navy text-white px-2 py-1" style="font-size:.78rem;">
              <i class="bi bi-fingerprint me-1 text-warning"></i> Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
            </span>
            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-geo-alt"></i> {{ $application->district->name ?? 'District N/A' }}</span>
            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-tag"></i> Category: {{ $application->category->code ?? 'N/A' }}</span>
            <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-calendar3"></i> Submitted {{ $application->created_at ? $application->created_at->format('d M Y') : date('d M Y') }}</span>
          </div>
        </div>
        <div class="d-flex gap-2 flex-wrap align-items-center">
          @php
            $linkedMining = $application->miningApplications ? $application->miningApplications->first() : null;
          @endphp
          @if($linkedMining)
            <a href="{{ route('process', ['id' => $linkedMining->id]) }}" class="btn btn-outline-success btn-sm">
              <i class="fa fa-mountain me-1"></i> Mining Plan: {{ $linkedMining->common_id ?? $linkedMining->application_no }} ↗
            </a>
          @else
            @can('application.edit')
            <button type="button" class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#modalMoveToMining">
              <i class="bi bi-rocket-takeoff me-1"></i> Move to Mining Plan
            </button>
            @endcan
          @endif
          @if(isset($application->id))
          <a href="{{ route('application.report', $application->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-download"></i> Export Summary
          </a>
          @endif
          @can('application.create')
          <a href="{{ route('step1') }}" class="btn btn-navy btn-sm"><i class="bi bi-pencil-square"></i> New Application</a>
          @endcan
        </div>
      </div>

      @if($linkedMining)
      <div class="alert alert-success d-flex justify-content-between align-items-center mb-3 mt-2 shadow-sm" style="border-radius:12px; background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill fs-5 text-success"></i>
          <div>
            <strong>This Lease Application has been promoted to Mining Plan Domain!</strong>
            <div class="small mt-1" style="color:#047857 !important;">
              Universal Common ID: <b>{{ $linkedMining->common_id }}</b> &middot; Mining Application No: <b>{{ $linkedMining->application_no }}</b> &middot; Stage: <b>Stage {{ $linkedMining->stage ?? '6.1' }}</b>
            </div>
          </div>
        </div>
        <a href="{{ route('process', ['id' => $linkedMining->id]) }}" class="btn btn-success btn-sm px-3">
          Open Mining Plan Dossier <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      @endif

      <!-- PRE-CALCULATE DYNAMIC COUNTS -->
      @php
        $docs = $application->documents ?? collect();
        $regUploaded = $docs->whereIn('folder_id', [7, 8])->whereNotNull('file_path')->count();
        $regTotal = 16;
        $planUploaded = $docs->where('folder_id', 9)->whereNotNull('file_path')->count();
        $planTotal = 3;
        $totalUploaded = $docs->whereNotNull('file_path')->count();
        $validatedCount = $docs->where('status', 'validated')->count();
        $mimas = $application->mimasCredentials ? $application->mimasCredentials->first() : null;
        $sortedDocs = $docs->sortBy(function($d) {
            $num = preg_replace('/\D/', '', explode('.', $d->document_name)[0] ?? '');
            return $num !== '' ? (int)$num : 99999;
        });
        $stageDuration = $application->updated_at
            ? $application->updated_at->diffForHumans(null, true)
            : ($application->created_at ? $application->created_at->diffForHumans(null, true) : 'Active');
      @endphp

      <!-- DOSSIER KPI METRICS STYLES (UI/UX PRO MAX) -->
      <style>
        .dossier-kpi-card {
          background: #ffffff;
          border: 1px solid #e2e8f0;
          border-radius: 14px;
          padding: 1.15rem 1.25rem;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          min-height: 126px;
          box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.02);
          transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
          position: relative;
        }
        .dossier-kpi-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
          border-color: #cbd5e1;
        }
        .dossier-kpi-head {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 0.5rem;
          margin-bottom: 0.5rem;
        }
        .dossier-kpi-label {
          font-size: 0.78rem;
          font-weight: 600;
          color: #64748b;
          letter-spacing: 0.01em;
          line-height: 1.3;
          margin: 0;
        }
        .dossier-kpi-icon {
          width: 38px;
          height: 38px;
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.05rem;
          flex-shrink: 0;
          transition: transform 0.2s ease;
        }
        .dossier-kpi-card:hover .dossier-kpi-icon {
          transform: scale(1.05);
        }
        .dossier-kpi-icon.icon-teal {
          background: #e6fffa;
          color: #0d9488;
          border: 1px solid #b2f5ea;
        }
        .dossier-kpi-icon.icon-purple {
          background: #f3e8ff;
          color: #7c3aed;
          border: 1px solid #e9d5ff;
        }
        .dossier-kpi-icon.icon-green {
          background: #ecfdf5;
          color: #059669;
          border: 1px solid #a7f3d0;
        }
        .dossier-kpi-icon.icon-orange {
          background: #fff7ed;
          color: #ea580c;
          border: 1px solid #fed7aa;
        }
        .dossier-kpi-value {
          font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
          font-size: 1.6rem;
          font-weight: 700;
          color: #0f172a;
          line-height: 1.15;
          font-variant-numeric: tabular-nums;
          white-space: nowrap;
          letter-spacing: -0.02em;
          margin: 0.15rem 0 0.55rem 0;
        }
        .dossier-kpi-total {
          font-size: 1rem;
          font-weight: 500;
          color: #94a3b8;
          margin-left: 2px;
        }
        .dossier-kpi-foot {
          display: flex;
          align-items: center;
          margin-top: auto;
        }
        .dossier-kpi-pill {
          display: inline-flex;
          align-items: center;
          gap: 0.35rem;
          font-size: 0.72rem;
          font-weight: 600;
          padding: 0.22rem 0.65rem;
          border-radius: 999px;
          line-height: 1.2;
        }
        .dossier-kpi-pill.pill-success {
          background: #ecfdf5;
          color: #047857;
          border: 1px solid #a7f3d0;
        }
        .dossier-kpi-pill.pill-warning {
          background: #fffbeb;
          color: #b45309;
          border: 1px solid #fde68a;
        }
        .dossier-kpi-pill.pill-neutral {
          background: #f8fafc;
          color: #475569;
          border: 1px solid #e2e8f0;
        }
      </style>

      <!-- DYNAMIC KPI METRICS ROW -->
      <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Regulatory Documents</span>
              <div class="dossier-kpi-icon icon-teal"><i class="fa fa-folder"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $regUploaded }} <span class="dossier-kpi-total">/ {{ $regTotal }}</span>
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $regUploaded >= $regTotal ? 'pill-success' : 'pill-warning' }}">
                <i class="bi {{ $regUploaded >= $regTotal ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                {{ $regUploaded >= $regTotal ? 'Complete' : ($regTotal - $regUploaded) . ' Pending' }}
              </span>
            </div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Plan Files (KML / PDF)</span>
              <div class="dossier-kpi-icon icon-purple"><i class="fa fa-cloud-upload"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $planUploaded }} <span class="dossier-kpi-total">/ {{ $planTotal }}</span>
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $planUploaded >= $planTotal ? 'pill-success' : 'pill-warning' }}">
                <i class="bi {{ $planUploaded >= $planTotal ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                {{ $planUploaded >= $planTotal ? 'Complete' : ($planTotal - $planUploaded) . ' Pending' }}
              </span>
            </div>
          </div>
        </div>

        @php
          $allValidated = ($totalUploaded > 0 && $validatedCount >= $totalUploaded);
        @endphp
        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Validated Files</span>
              <div class="dossier-kpi-icon icon-green"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="dossier-kpi-value" id="kpi-validated-count">{{ $validatedCount }} <span class="dossier-kpi-total">/ {{ $totalUploaded }}</span></div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $allValidated ? 'pill-success' : 'pill-warning' }}" id="kpi-validated-sub">
                <i class="bi {{ $allValidated ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                <span id="kpi-validated-text">{{ $allValidated ? 'All Validated' : ($totalUploaded > 0 ? ($totalUploaded - $validatedCount) . ' Awaiting Scrutiny' : 'No Files Uploaded') }}</span>
              </span>
            </div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">In Current Stage</span>
              <div class="dossier-kpi-icon icon-orange"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $stageDuration }}
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill pill-neutral">
                <i class="bi bi-stopwatch"></i> SLA: 10 days
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- LEASE & APPLICANT RECORD OVERVIEW CARD -->
      <div class="card-panel mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
          <div>
            <div class="panel-title mb-0"><i class="fa fa-id-card text-primary me-2"></i>Lease &amp; Applicant Information</div>
            <div class="panel-sub mb-0">Live record populated directly from database</div>
          </div>
          <span class="badge bg-light text-dark border px-2 py-1">
            <i class="fa fa-database me-1 text-primary"></i> Application ID: #{{ $application->id }}
          </span>
        </div>

        <div class="row g-3" style="font-size: 0.85rem;">
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Client / Firm</span>
            <strong class="text-dark">{{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'Deleted / Unassigned') }}</strong>
            @if($application->customer?->trashed())
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: .65rem;">Deleted</span>
            @endif
            <div class="text-muted small">{{ $application->customer?->customer_name ?? '' }}</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Mineral Type</span>
            <strong class="text-primary">
              @php
                $mineralNames = $application->minerals && $application->minerals->isNotEmpty()
                    ? $application->minerals->pluck('name')->toArray()
                    : ($application->mineral ? [$application->mineral->name] : []);
                if (!empty($application->other_mineral_name)) {
                    $mineralNames[] = 'Other: ' . $application->other_mineral_name;
                }
              @endphp
              {{ !empty($mineralNames) ? implode(', ', $mineralNames) : 'Not specified' }}
            </strong>
            <div class="text-muted small">Major / Minor Mineral</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">District &amp; Taluk</span>
            <strong class="text-dark">{{ $application->district->name ?? 'N/A' }}</strong>
            <div class="text-muted small">{{ $application->taluk ?: 'Taluk not specified' }}</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Quarry Extent</span>
            <strong class="text-dark">{{ $application->area_extent_ha ? $application->area_extent_ha . ' Ha' : 'N/A' }}</strong>
            <div class="text-muted small">Survey: {{ $application->surveyNumbers->pluck('survey_no')->join(', ') ?: 'SF.No 1' }}</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Lease Category</span>
            <strong class="text-dark">{{ $application->category->code ?? 'N/A' }}</strong>
            <div class="text-muted small text-truncate" title="{{ $application->category->name ?? '' }}">{{ $application->category->name ?? 'N/A' }}</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Lease Period</span>
            <strong class="text-dark">{{ $application->lease_period_years ?? 5 }} Years</strong>
            <div class="text-muted small">
              {{ $application->start_date ? \Carbon\Carbon::parse($application->start_date)->format('Y') : date('Y') }} &ndash;
              {{ $application->end_date ? \Carbon\Carbon::parse($application->end_date)->format('Y') : (date('Y') + 5) }}
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Primary Contact</span>
            <strong class="text-dark">{{ $application->contact_person ?? ($application->customer?->customer_name ?? 'N/A') }}</strong>
            <div class="text-muted small"><i class="fa fa-phone me-1"></i>{{ $application->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A') }}</div>
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Secondary / Site Contact</span>
            @if(!empty($application->secondary_contact_person) || !empty($application->secondary_contact_mobile) || !empty($application->customer?->secondary_contact_person) || !empty($application->customer?->secondary_mobile_num))
              <strong class="text-dark">{{ $application->secondary_contact_person ?? ($application->customer?->secondary_contact_person ?? 'N/A') }}</strong>
              <div class="text-muted small"><i class="fa fa-phone me-1 text-indigo"></i>{{ $application->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? 'N/A') }}</div>
            @else
              <span class="text-muted fst-italic">Not provided</span>
            @endif
          </div>
          <div class="col-md-3 col-sm-6">
            <span class="text-muted d-block small">Identity &amp; Tax IDs</span>
            <div class="text-dark fw-semibold">Customer Unique ID: <span class="text-primary">{{ $application->customer?->mimas_no ?? 'N/A' }}</span></div>
            <div class="text-muted small">Aadhaar: {{ $application->customer?->aadhaar_no ?? 'N/A' }} &middot; PAN: {{ $application->customer?->pan ?? 'N/A' }}</div>
          </div>
        </div>
      </div>

      <!-- PROCESS FLOW STEPPER (6.1 - 6.6) -->
      @php
        $status = $application->status ?? 'under_scrutiny';
        $isApproved = ($status === 'approved');
        $isValidated = in_array($status, ['validated', 'approved']);
        $isRevision = ($status === 'revision_required');
      @endphp

      <div class="card-panel">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
          <div>
            <div class="panel-title">Process Flow (Stages 6.1 – 6.6)</div>
            <div class="panel-sub">Upload &amp; Store → Validate Data → Approve Data → Generate Reports → Archive &amp; Backup</div>
          </div>
          <div class="progress-thin flex-grow-1 mx-4 d-none d-md-block" style="max-width:180px; margin-top:6px;">
            <div class="progress-bar" role="progressbar" style="width: {{ $isApproved ? '80%' : ($isValidated ? '60%' : '35%') }}"></div>
          </div>
        </div>
        <div class="flow-stepper mt-4">
          <!-- Step 1: Upload & Store -->
          <div class="fstep done">
            <div class="circ"><i class="bi bi-check-lg"></i></div>
            <div class="flabel">6.1 Upload &amp; Store</div>
            <div class="fsub">Completed</div>
          </div>

          <!-- Step 2: Validate Data -->
          <div class="fstep {{ $isValidated ? 'done' : ($isRevision ? 'active' : 'active') }}">
            <div class="circ">
              @if($isValidated) <i class="bi bi-check-lg"></i> @else 2 @endif
            </div>
            <div class="flabel">6.2 Validate Data</div>
            <div class="fsub">
              @if($isRevision)
                <span class="text-danger fw-bold">Revision Required</span>
              @elseif($isValidated)
                Completed
              @else
                In Scrutiny
              @endif
            </div>
          </div>

          <!-- Step 3: Approve Data -->
          <div class="fstep {{ $isApproved ? 'done' : ($isValidated ? 'active' : '') }}">
            <div class="circ">
              @if($isApproved) <i class="bi bi-check-lg"></i> @else 3 @endif
            </div>
            <div class="flabel">6.3 Approve Data</div>
            <div class="fsub">
              @if($isApproved)
                Approved
              @elseif($isValidated)
                Ready for Approval
              @else
                Pending
              @endif
            </div>
          </div>

          <!-- Step 4: Generate Reports -->
          <div class="fstep {{ $isApproved ? 'active' : '' }}">
            <a href="{{ isset($application->id) ? route('application.report', $application->id) : 'javascript:void(0)' }}" target="_blank" style="text-decoration:none; color:inherit;">
              <div class="circ">4</div>
              <div class="flabel">6.4 Generate Reports</div>
              <div class="fsub">{{ $isApproved ? 'Ready to Download' : 'Pending' }}</div>
            </a>
          </div>

          <!-- Step 5: Archive & Backup -->
          <div class="fstep">
            <a href="javascript:void(0)" style="text-decoration:none; color:inherit;">
              <div class="circ">5</div>
              <div class="flabel">6.5 Archive &amp; Backup</div>
              <div class="fsub">Pending</div>
            </a>
          </div>
        </div>

        <!-- Dynamic Context Alert -->
        @if($isApproved)
        <div class="alert d-flex align-items-center gap-2 mt-4 mb-0" style="background:#e6f4ea; border:1px solid #b7e1cd; color:#137333; font-size:.82rem;">
          <i class="bi bi-check-circle-fill fs-6 text-success"></i>
          <div><strong>Application Fully Approved &amp; Verified!</strong> Compliance report is ready for export and archival.</div>
        </div>
        @elseif($isValidated)
        <div class="alert d-flex align-items-center gap-2 mt-4 mb-0" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:.82rem;">
          <i class="bi bi-shield-check fs-6 text-primary"></i>
          <div><strong>All 19 Documents Validated!</strong> Scrutiny is passed. Authorized officer can approve this application.</div>
        </div>
        @elseif($isRevision)
        <div class="alert d-flex align-items-center gap-2 mt-4 mb-0" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; font-size:.82rem;">
          <i class="bi bi-exclamation-triangle-fill fs-6 text-danger"></i>
          <div><strong>Revision Required:</strong> Applicant has been notified to re-upload flagged documents.</div>
        </div>
        @else
        <div class="alert d-flex align-items-center gap-2 mt-4 mb-0" style="background:var(--orange-soft); border:1px solid #f3d9b3; color:#7a4406; font-size:.82rem;">
          <i class="bi bi-info-circle fs-6"></i>
          <div>Application is currently <b>Under Scrutiny</b>. Review attached documents and mark validation or request correction.</div>
        </div>
        @endif
      </div>

      <!-- MAIN CONTENT ROW: DOCUMENT CHECKLIST & SIDEBAR -->
      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card-panel">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div>
                <div class="panel-title">Document Checklist</div>
                <div class="panel-sub mb-0">{{ $docs->count() }} total items across Documents, Lease Application &amp; Plan folders</div>
              </div>
              @can('application.edit')
              <a href="{{ route('step5') }}" class="btn btn-sm" style="background:var(--navy-soft); color:var(--navy); font-weight:600;"><i class="bi bi-upload"></i> Upload / Edit Files</a>
              @endcan
            </div>
            <div class="table-responsive">
              <table class="table doc-table mb-0 align-middle">
                <thead>
                  <tr>
                    <th style="width:40px;">#</th>
                    <th>Document</th>
                    <th>Folder</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 220px;">Scrutiny &amp; Action</th>
                  </tr>
                </thead>
                <tbody>
                  @if($sortedDocs->count() > 0)
                    @foreach($sortedDocs as $doc)
                    <tr id="doc-row-{{ $doc->id }}">
                      <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                      <td>
                        <span class="file-chip">
                          @if(str_contains($doc->file_type ?? '', 'image'))
                            <i class="bi bi-file-earmark-image text-primary"></i>
                          @elseif(str_contains($doc->file_name ?? '', '.kml'))
                            <i class="bi bi-geo-alt-fill text-success"></i>
                          @elseif($doc->file_path)
                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                          @else
                            <i class="bi bi-file-earmark-x text-muted"></i>
                          @endif
                          <span class="doc-name-text">{{ $doc->document_name }}</span>
                        </span>
                        @if($doc->file_path && $doc->file_name)
                          <div class="text-muted ps-4" style="font-size:.72rem;">
                            <span class="text-secondary"><i class="bi bi-paperclip"></i> {{ $doc->file_name }}</span> &middot; {{ number_format(($doc->file_size ?? 1024) / 1024, 1) }} KB
                          </div>
                        @else
                          <div class="text-muted ps-4" style="font-size:.72rem; color:#b45309 !important;">
                            <i class="bi bi-dash-circle me-1"></i> Not uploaded by applicant
                          </div>
                        @endif
                        @if($doc->review_note)
                          <div class="ps-4 mt-1" style="font-size:.72rem; color:#dc2626;" id="doc-note-{{ $doc->id }}">
                            <i class="bi bi-chat-left-dots-fill me-1"></i> Note: {{ $doc->review_note }}
                          </div>
                        @else
                          <div class="ps-4 mt-1 d-none" style="font-size:.72rem; color:#dc2626;" id="doc-note-{{ $doc->id }}"></div>
                        @endif
                      </td>
                      <td><span class="badge bg-light text-dark border">{{ $doc->folder->name ?? 'Folder #' . $doc->folder_id }}</span></td>
                      <td id="doc-badge-col-{{ $doc->id }}">
                        @if(!$doc->file_path)
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                            <i class="bi bi-dash-circle me-1"></i> Not Uploaded
                          </span>
                        @elseif($doc->status === 'validated' || $doc->status === 'approved')
                          <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-circle-fill me-1"></i> Validated
                          </span>
                        @elseif($doc->status === 'revision_required')
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Revision Needed
                          </span>
                        @else
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                            <i class="bi bi-clock me-1"></i> Uploaded
                          </span>
                        @endif
                      </td>
                      <td class="text-end" id="doc-actions-col-{{ $doc->id }}">
                        <div class="d-inline-flex align-items-center gap-1">
                          @if($doc->file_path)
                            <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:.75rem;" title="View Attached File">
                              <i class="bi bi-eye"></i> View
                            </a>

                            @can('application.edit')
                              @if($doc->status !== 'validated')
                                <button type="button" 
                                        class="btn btn-sm btn-success py-0 px-2 btn-doc-action" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-status="validated" 
                                        title="Mark as Valid"
                                        style="font-size:.75rem;">
                                  <i class="bi bi-check2"></i> Mark Valid
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger py-0 px-2 btn-flag-doc-modal" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-doc-name="{{ $doc->document_name }}" 
                                        title="Flag for Revision"
                                        style="font-size:.75rem;">
                                  <i class="bi bi-flag"></i> Flag
                                </button>
                              @else
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary py-0 px-2 btn-doc-action" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-status="uploaded" 
                                        title="Undo Validation"
                                        style="font-size:.75rem;">
                                  <i class="bi bi-arrow-counterclockwise"></i> Undo
                                </button>
                              @endif
                            @endcan
                          @else
                            <a href="{{ route('step5') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.75rem;" title="Upload in Step 5">
                              <i class="bi bi-upload"></i> Upload
                            </a>
                          @endif
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="5" class="text-center text-muted py-3">No documents attached.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <small class="text-muted">Showing {{ $docs->count() }} checklist slots &middot; {{ $totalUploaded }} uploaded</small>
              <a href="{{ route('step5') }}" style="font-size:.8rem; font-weight:600; color:var(--navy);">Upload all remaining <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>

          <!-- WORKFLOW ACTION PANEL (Process Flow 6.2 & 6.3) -->
          <div class="card-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <div class="panel-title mb-0">
                @if($isApproved)
                  Application Completed &amp; Approved
                @elseif($isValidated)
                  Validation Passed &mdash; Ready for Final Approval
                @else
                  Ready to Scrutinise &amp; Move Forward?
                @endif
              </div>
              <div class="panel-sub mb-0">
                @if($isApproved)
                  All regulatory steps are completed. You can download the full compliance dossier.
                @elseif($isValidated)
                  Click Approve to lock document sets and grant official approval.
                @else
                  Validating checks all {{ $docs->count() }} attached files. If any document is incomplete, send back for revision.
                @endif
              </div>
            </div>

            @can('application.edit')
            <div class="d-flex gap-2">
              @if(!$isApproved && !$isValidated)
                <!-- Validate Pass Button -->
                <form action="{{ route('application.validate', $application->id) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="action" value="pass">
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-shield-check me-1"></i> Pass Validation
                  </button>
                </form>

                <!-- Reject / Revision Button -->
                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#revisionModal">
                  <i class="bi bi-arrow-counterclockwise me-1"></i> Review &amp; Correct
                </button>
              @elseif($isValidated && !$isApproved)
                <!-- Approve Button -->
                <form action="{{ route('application.approve', $application->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-green btn-sm">
                    <i class="bi bi-check-circle-fill me-1"></i> Approve Application
                  </button>
                </form>

                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#revisionModal">
                  <i class="bi bi-arrow-counterclockwise me-1"></i> Request Revision
                </button>
              @elseif($isApproved)
                <a href="{{ route('application.report', $application->id) }}" target="_blank" class="btn btn-success btn-sm">
                  <i class="bi bi-file-earmark-pdf me-1"></i> Download Report
                </a>
                @if($linkedMining)
                  <a href="/process?id={{ $linkedMining->id }}" class="btn btn-outline-success btn-sm">
                    <i class="fa fa-mountain me-1"></i> Open Mining Plan ({{ $linkedMining->common_id }}) ↗
                  </a>
                @else
                  <button type="button" class="btn btn-navy btn-sm" data-bs-toggle="modal" data-bs-target="#modalMoveToMining">
                    <i class="bi bi-rocket-takeoff me-1"></i> Move to Mining Plan
                  </button>
                @endif
              @endif
            </div>
            @endcan
          </div>
        </div>

        <!-- RIGHT SIDEBAR: FOLDERS, ACTIVITY TIMELINE, MIMAS DETAILS -->
        <div class="col-lg-4">
          <!-- Folders Summary -->
          <div class="card-panel">
            <div class="panel-title">Folders</div>
            <div class="panel-sub">Storage summary by folder type</div>

            <div class="folder-mini">
              <div class="fico"><i class="fa fa-folder-open"></i></div>
              <div class="flex-grow-1">
                <div class="ft">1. Documents</div>
                <div class="fs">{{ $docs->where('folder_id', 7)->whereNotNull('file_path')->count() }} / 9 uploaded</div>
              </div>
              <span class="badge-status {{ $docs->where('folder_id', 7)->whereNotNull('file_path')->count() >= 9 ? 'uploaded' : 'pending' }}">
                {{ $docs->where('folder_id', 7)->whereNotNull('file_path')->count() >= 9 ? 'Complete' : 'Pending' }}
              </span>
            </div>

            <div class="folder-mini">
              <div class="fico"><i class="fa fa-folder"></i></div>
              <div class="flex-grow-1">
                <div class="ft">2. Lease Application</div>
                <div class="fs">{{ $docs->where('folder_id', 8)->whereNotNull('file_path')->count() }} / 7 uploaded</div>
              </div>
              <span class="badge-status {{ $docs->where('folder_id', 8)->whereNotNull('file_path')->count() >= 7 ? 'uploaded' : 'pending' }}">
                {{ $docs->where('folder_id', 8)->whereNotNull('file_path')->count() >= 7 ? 'Complete' : 'Pending' }}
              </span>
            </div>

            <div class="folder-mini mb-0">
              <div class="fico"><i class="fa fa-inbox"></i></div>
              <div class="flex-grow-1">
                <div class="ft">3. Plan</div>
                <div class="fs">{{ $docs->where('folder_id', 9)->whereNotNull('file_path')->count() }} / 3 uploaded</div>
              </div>
              <span class="badge-status {{ $docs->where('folder_id', 9)->whereNotNull('file_path')->count() >= 3 ? 'uploaded' : 'pending' }}">
                {{ $docs->where('folder_id', 9)->whereNotNull('file_path')->count() >= 3 ? 'Complete' : 'Pending' }}
              </span>
            </div>
          </div>

          <!-- Activity Timeline (Real from activity_logs) -->
          <div class="card-panel">
            <div class="panel-title">Activity Timeline</div>
            <div class="panel-sub">Auto-logged from audit trail</div>
            <div class="timeline">
              @if(isset($activityLogs) && $activityLogs->count() > 0)
                @foreach($activityLogs as $log)
                <div class="tl-item">
                  <div class="tl-title">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</div>
                  <div class="text-muted" style="font-size: .74rem;">{{ $log->description }}</div>
                  <div class="tl-meta">
                    {{ $log->created_at ? $log->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'Recently' }} 
                    &middot; {{ $log->user->name ?? 'Super Admin' }}
                  </div>
                </div>
                @endforeach
              @else
                <div class="tl-item">
                  <div class="tl-title">Application Submitted</div>
                  <div class="tl-meta">{{ $application->created_at ? $application->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'Recently' }} &middot; System</div>
                </div>
              @endif
            </div>
          </div>

          <!-- MIMAS Details -->
          <div class="card-panel mb-0">
            <div class="panel-title">MIMAS Details</div>
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">User ID</span>
              <span class="fw-semibold">{{ $mimas->user_id ?? ($application->customer?->mimas_no ?? 'N/A') }}</span>
            </div>
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">Email</span>
              <span class="fw-semibold">{{ $mimas->email ?? ($application->customer?->email ?? 'N/A') }}</span>
            </div>
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">Primary Contact</span>
              <span class="fw-semibold">{{ $mimas->contact_number ?? ($application->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A')) }}</span>
            </div>
            @if(!empty($application->secondary_contact_mobile) || !empty($application->customer?->secondary_mobile_num))
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">Secondary Mobile</span>
              <span class="fw-semibold" style="color:#4f46e5;">{{ $application->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? 'N/A') }}</span>
            </div>
            @endif
            @if($mimas && $mimas->mimas_ack_no)
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">MIMAS Ack No</span>
              <span class="badge bg-light text-primary border">{{ $mimas->mimas_ack_no }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
              <span class="text-muted">Portal Status</span>
              <span class="badge bg-success-subtle text-success border border-success-subtle">{{ ucfirst($mimas->portal_status ?? 'Verified') }}</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal for Review & Correct (Send for Revision) -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('application.reject', $application->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title text-danger"><i class="bi bi-arrow-counterclockwise me-1"></i> Send Back for Revision</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-3">Please specify the reasons for rejecting or requesting revision. This will be logged in the audit trail.</p>
          <div class="mb-3">
            <label class="form-label fw-bold">Reason for Revision <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" rows="3" placeholder="e.g. FMB sketch is not clear, Adangal copy is outdated, etc." required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm">Submit Revision Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for Flagging Single Document -->
<div class="modal fade" id="flagDocModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger"><i class="bi bi-flag-fill me-1"></i> Flag Document for Revision</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-2">Specify why this document requires correction or re-upload:</p>
        <div class="fw-bold mb-2 text-dark" id="flagDocModalTitle">Document Name</div>
        <input type="hidden" id="flagDocId" value="">
        <div class="mb-3">
          <label class="form-label small fw-bold">Correction Note / Reason <span class="text-danger">*</span></label>
          <textarea id="flagDocNote" class="form-control" rows="3" placeholder="e.g. Incomplete scan, missing seal/signature, illegible document..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-sm" id="btnSubmitFlagDoc">Confirm &amp; Flag</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Move to Mining Plan -->
<div class="modal fade" id="modalMoveToMining" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <form action="{{ route('application.moveToMining', $application->id) }}" method="POST">
        @csrf
        <div class="modal-header text-white" style="background:#0F1E4D;">
          <h5 class="modal-title text-white"><i class="bi bi-rocket-takeoff me-2 text-warning"></i> Move to Mining Plan Domain</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="p-3 mb-3 rounded" style="background:#eff6ff; border:1px solid #bfdbfe;">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Universal Common ID:</span>
              <strong class="text-primary font-monospace">{{ $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Current Lease App No:</span>
              <strong class="text-dark font-monospace">{{ $application->application_no }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Applicant / Firm:</span>
              <strong class="text-dark">{{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'Applicant') }}</strong>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted small">Quarry Extent:</span>
              <strong class="text-dark">{{ $application->area_extent_ha ? ($application->area_extent_ha . ' Ha') : 'N/A' }}</strong>
            </div>
          </div>
          <p class="small text-muted mb-2">
            Moving this application to the <b>Mining Plan</b> domain will:
          </p>
          <ul class="small text-muted mb-0 ps-3">
            <li class="mb-1">Retain the <b>exact same Universal Common ID</b> across the entire system.</li>
            <li class="mb-1">Auto-carry applicant, district, survey numbers, and mineral data.</li>
            <li class="mb-1">Auto-clone all verified statutory files (Patta, FMB, KML, Drawings) into Mining Folders #2 &amp; #5.</li>
            <li>Initialize <b>Stage 6.1 (Data Ingest)</b> in the Mining Portal.</li>
          </ul>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-navy btn-sm px-3">
            <i class="bi bi-rocket-takeoff me-1"></i> Proceed &amp; Move to Mining Plan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
jQuery(document).ready(function($) {
  // CSRF Setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Mark Valid or Undo Click
  $(document).on('click', '.btn-doc-action', function(e) {
    e.preventDefault();
    var btn = $(this);
    var docId = btn.data('doc-id');
    var targetStatus = btn.data('status');
    var origHtml = btn.html();

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

    $.ajax({
      url: '/application/document/' + docId + '/status',
      type: 'POST',
      data: {
        status: targetStatus
      },
      success: function(res) {
        if (res.status === 1) {
          updateDocRowUI(docId, targetStatus);
          if (res.total_uploaded !== undefined) {
            $('#kpi-validated-count').html(res.total_validated + ' <span class="dossier-kpi-total">/ ' + res.total_uploaded + '</span>');
            if (res.total_validated >= res.total_uploaded && res.total_uploaded > 0) {
              $('#kpi-validated-sub').removeClass('pill-warning').addClass('pill-success').css('color', '');
              $('#kpi-validated-sub i').attr('class', 'bi bi-check-circle-fill');
              $('#kpi-validated-text').text('All Validated');
            } else {
              $('#kpi-validated-sub').removeClass('pill-success').addClass('pill-warning').css('color', '');
              $('#kpi-validated-sub i').attr('class', 'bi bi-clock-fill');
              $('#kpi-validated-text').text((res.total_uploaded - res.total_validated) + ' Awaiting Scrutiny');
            }
          }
          if (typeof toastr !== 'undefined') {
            if (targetStatus === 'validated') {
              toastr.success('Document marked as Validated');
            } else {
              toastr.info('Document status updated to ' + targetStatus);
            }
          }
        } else {
          showNotify('error', 'Action Failed', res.message || 'Action failed.');
          btn.prop('disabled', false).html(origHtml);
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html(origHtml);
        var msg = 'Failed to update document status.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          showNotify('error', 'Update Error', msg);
        }
      }
    });
  });

  // Open Flag Modal
  $(document).on('click', '.btn-flag-doc-modal', function(e) {
    e.preventDefault();
    var docId = $(this).data('doc-id');
    var docName = $(this).data('doc-name');
    $('#flagDocId').val(docId);
    $('#flagDocModalTitle').text(docName);
    $('#flagDocNote').val('');
    var modalEl = document.getElementById('flagDocModal');
    if (modalEl) {
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    }
  });

  // Submit Flag
  $('#btnSubmitFlagDoc').on('click', function() {
    var docId = $('#flagDocId').val();
    var note = $('#flagDocNote').val().trim();
    if (!note) {
      showNotify('warning', 'Note Required', 'Please enter a reason or note for revision.');
      $('#flagDocNote').focus();
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).text('Flagging...');

    $.ajax({
      url: '/application/document/' + docId + '/status',
      type: 'POST',
      data: {
        status: 'revision_required',
        note: note
      },
      success: function(res) {
        btn.prop('disabled', false).text('Confirm & Flag');
        var modalEl = document.getElementById('flagDocModal');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        updateDocRowUI(docId, 'revision_required', note);
        if (res.total_uploaded !== undefined) {
          $('#kpi-validated-count').html(res.total_validated + ' <span class="dossier-kpi-total">/ ' + res.total_uploaded + '</span>');
          $('#kpi-validated-sub').removeClass('pill-success').addClass('pill-warning').css('color', '');
          $('#kpi-validated-sub i').attr('class', 'bi bi-clock-fill');
          $('#kpi-validated-text').text((res.total_uploaded - res.total_validated) + ' Awaiting Scrutiny');
        }
        if (typeof toastr !== 'undefined') {
          toastr.warning('Document flagged for revision');
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).text('Confirm & Flag');
        var msg = 'Failed to flag document.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          showNotify('error', 'Flag Error', msg);
        }
      }
    });
  });

  function showNotify(icon, title, text) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#0F1E4D'
      });
    } else if (typeof toastr !== 'undefined') {
      toastr[icon === 'error' ? 'error' : (icon === 'warning' ? 'warning' : 'info')](text, title);
    } else {
      alert(title + ': ' + text);
    }
  }

  function updateDocRowUI(docId, newStatus, note) {
    var badgeCol = $('#doc-badge-col-' + docId);
    var actionsCol = $('#doc-actions-col-' + docId);
    var noteEl = $('#doc-note-' + docId);

    if (newStatus === 'validated') {
      badgeCol.html('<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle-fill me-1"></i> Validated</span>');
      var viewBtn = actionsCol.find('a[target="_blank"]').prop('outerHTML') || '';
      actionsCol.find('.d-inline-flex').html(
        viewBtn +
        '<button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-doc-action" data-doc-id="' + docId + '" data-status="uploaded" title="Undo Validation" style="font-size:.75rem;"><i class="bi bi-arrow-counterclockwise"></i> Undo</button>'
      );
    } else if (newStatus === 'revision_required') {
      badgeCol.html('<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle-fill me-1"></i> Revision Needed</span>');
      var viewBtn = actionsCol.find('a[target="_blank"]').prop('outerHTML') || '';
      actionsCol.find('.d-inline-flex').html(
        viewBtn +
        '<button type="button" class="btn btn-sm btn-success py-0 px-2 btn-doc-action" data-doc-id="' + docId + '" data-status="validated" title="Mark as Valid" style="font-size:.75rem;"><i class="bi bi-check2"></i> Mark Valid</button>'
      );
      if (note) {
        noteEl.removeClass('d-none').html('<i class="bi bi-chat-left-dots-fill me-1"></i> Note: ' + $('<div>').text(note).html());
      }
    } else if (newStatus === 'uploaded') {
      badgeCol.html('<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-clock me-1"></i> Uploaded</span>');
      var viewBtn = actionsCol.find('a[target="_blank"]').prop('outerHTML') || '';
      var docName = $('#doc-row-' + docId).find('.doc-name-text').text().trim();
      actionsCol.find('.d-inline-flex').html(
        viewBtn +
        '<button type="button" class="btn btn-sm btn-success py-0 px-2 btn-doc-action" data-doc-id="' + docId + '" data-status="validated" title="Mark as Valid" style="font-size:.75rem;"><i class="bi bi-check2"></i> Mark Valid</button>' +
        '<button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-flag-doc-modal" data-doc-id="' + docId + '" data-doc-name="' + $('<div>').text(docName).html() + '" title="Flag for Revision" style="font-size:.75rem;"><i class="bi bi-flag"></i> Flag</button>'
      );
    }
  }
});
</script>
@endsection
