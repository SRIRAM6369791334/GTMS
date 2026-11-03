@extends('layouts.master')

@section('title', 'Universal Master Projects Pipeline • GTMS')

@section('main_content')
<style>
    /* Scoped GTMS Master Pipeline Executive Design System */
    .gtms-pipeline-header {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(15, 30, 77, 0.05);
    }
    .kpi-bento-card {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -2px rgba(15, 30, 77, 0.04);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-bento-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -4px rgba(15, 30, 77, 0.09);
        border-color: #cbd5e1;
    }
    .kpi-bento-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
    }
    .kpi-bento-total::before { background: linear-gradient(90deg, #0F1E4D, #2563EB); }
    .kpi-bento-active::before { background: linear-gradient(90deg, #2563EB, #60A5FA); }
    .kpi-bento-returned::before { background: linear-gradient(90deg, #DC2626, #F87171); }
    .kpi-bento-completed::before { background: linear-gradient(90deg, #059669, #34D399); }

    .kpi-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .kpi-icon-total { background: #0F1E4D; color: #ffffff; }
    .kpi-icon-active { background: #eff6ff; color: #2563EB; border: 1px solid #bfdbfe; }
    .kpi-icon-returned { background: #fef2f2; color: #DC2626; border: 1px solid #fecaca; }
    .kpi-icon-completed { background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0; }

    /* Departmental Linkage Micro-Pills */
    .pill-dept {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 5px;
        font-size: 0.69rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .pill-dept-la { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .pill-dept-la:hover { background: #dbeafe; color: #1e3a8a; }
    .pill-dept-dgps { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .pill-dept-dgps:hover { background: #d1fae5; color: #065f46; }
    .pill-dept-drone { background: #ecfeff; color: #0e7490; border: 1px solid #a5f3fc; }
    .pill-dept-drone:hover { background: #cffafe; color: #155e75; }
    .pill-dept-mining { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .pill-dept-mining:hover { background: #fef3c7; color: #92400e; }
    .pill-dept-env { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
    .pill-dept-env:hover { background: #ccfbf1; color: #115e59; }
    .pill-dept-ppt { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
    .pill-dept-ppt:hover { background: #ede9fe; color: #5b21b6; }

    /* Stage Indicators */
    .stage-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 0.73rem;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
        white-space: nowrap;
    }
    .stage-pill .stage-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    /* Neutral Slate Buttons */
    .btn-slate {
        background-color: #f8fafc;
        color: #334155 !important;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-slate:hover {
        background-color: #f1f5f9;
        color: #0f172a !important;
        border-color: #94a3b8;
    }
    .btn-navy-primary {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
        color: #ffffff !important;
        border: 1px solid #0F1E4D;
        font-weight: 600;
        border-radius: 7px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(15, 30, 77, 0.15);
    }
    .btn-navy-primary:hover {
        background: linear-gradient(135deg, #172B68 0%, #2563EB 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(15, 30, 77, 0.25);
        transform: translateY(-1px);
    }

    /* Master ID Chip */
    .master-id-pill {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-weight: 700;
        font-size: 0.79rem;
        color: #0F1E4D;
        background: #f1f5f9;
        padding: 3px 6px;
        border-radius: 5px;
        border: 1px solid #e2e8f0;
        letter-spacing: -0.01em;
        text-decoration: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }
    .master-id-pill:hover {
        color: #2563EB;
        background: #e2e8f0;
        border-color: #cbd5e1;
    }

    /* Table refinement */
    .master-pipeline-table {
        margin-bottom: 0;
        width: 100%;
    }
    .master-pipeline-table th {
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 10px 10px;
        font-size: 0.69rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .master-pipeline-table td {
        padding: 10px 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.81rem;
    }
    .master-pipeline-table tr:hover td {
        background-color: #f8fafc;
    }
    .tr-returned td {
        background-color: #fff5f5 !important;
    }
    .tr-returned:hover td {
        background-color: #fee2e2 !important;
    }
</style>

<div class="content-body">
    <div class="container-fluid px-3 px-md-4">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #bbf7d0; background-color: #f0fdf4; color: #166534;">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('alert') || session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #fed7aa; background-color: #fffbeb; color: #9a3412;">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                <div class="flex-grow-1">
                    <strong>Notice:</strong> {{ session('alert') ?? session('warning') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
                <i class="bi bi-x-octagon-fill fs-4 me-3 text-danger"></i>
                <div class="flex-grow-1">
                    <strong>Error:</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="gtms-pipeline-header mb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge px-3 py-2 text-white" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%); border-radius: 8px; font-weight: 700; letter-spacing: 0.05em;">
                            <i class="bi bi-diagram-3-fill me-1"></i> MASTER WORKFLOW
                        </span>
                        <span class="badge px-2.5 py-1 border" style="background: #ecfdf5; color: #047857; font-size: 0.75rem; border-color: #a7f3d0 !important;">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i> Compartmentalized & Audit-Logged
                        </span>
                        <span class="badge px-2.5 py-1" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.75rem;">
                            <i class="bi bi-check2-all me-1"></i> 5 Statutory Clearances
                        </span>
                    </div>
                    <h2 class="h3 fw-bold text-dark mb-1">Universal Master Projects Pipeline</h2>
                    <p class="text-muted small mb-0">Single-pane-of-glass coordination across Lease, Survey (DGPS & Drone), Mining Plan, Environmental Clearance (SEIAA), and PPT departments.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('master-pipeline.index') }}" class="btn btn-slate btn-sm d-inline-flex align-items-center gap-1.5 px-3 py-2">
                        <i class="bi bi-arrow-clockwise fs-6"></i> Refresh Pipeline
                    </a>
                </div>
            </div>
        </div>

        {{-- Top KPI Bento Cards --}}
        <div class="row g-3 mb-4">
            {{-- Total Projects --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-bento-card kpi-bento-total h-100">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #64748b;">Total Projects</span>
                                <h3 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">{{ number_format($kpis['total_projects']) }}</h3>
                                <div class="text-muted small mt-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-collection text-secondary"></i> All registered master files
                                </div>
                            </div>
                            <div class="kpi-icon-wrap kpi-icon-total">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-bento-card kpi-bento-active h-100">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #2563eb;">In Progress</span>
                                <h3 class="fw-bold mb-0 text-primary mt-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">{{ number_format($kpis['in_progress']) }}</h3>
                                <div class="text-muted small mt-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-arrow-repeat text-primary"></i> Advancing through stages
                                </div>
                            </div>
                            <div class="kpi-icon-wrap kpi-icon-active">
                                <i class="bi bi-clock-history"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Returned For Correction --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-bento-card kpi-bento-returned h-100" style="{{ $kpis['returned_for_correction'] > 0 ? 'background: #fffafa;' : '' }}">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #dc2626;">Returned For Correction</span>
                                <h3 class="fw-bold mb-0 text-danger mt-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">{{ number_format($kpis['returned_for_correction']) }}</h3>
                                <div class="text-danger small mt-1 fw-semibold d-flex align-items-center gap-1">
                                    <i class="bi bi-exclamation-circle-fill"></i> Action required by upstream
                                </div>
                            </div>
                            <div class="kpi-icon-wrap kpi-icon-returned">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Completed --}}
            <div class="col-xl-3 col-sm-6">
                <div class="card kpi-bento-card kpi-bento-completed h-100">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.06em; color: #059669;">Completed</span>
                                <h3 class="fw-bold mb-0 text-success mt-1" style="font-size: 1.85rem; letter-spacing: -0.02em;">{{ number_format($kpis['completed']) }}</h3>
                                <div class="text-muted small mt-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-patch-check-fill text-success"></i> Statutory clearances granted
                                </div>
                            </div>
                            <div class="kpi-icon-wrap kpi-icon-completed">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="card-body p-3.5">
                <form method="GET" action="{{ route('master-pipeline.index') }}" class="row g-2.5 align-items-end">
                    <div class="col-lg-4 col-md-6">
                        <label class="form-label small text-muted fw-bold mb-1">Search Keywords</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Master ID, Client, Village, Survey No..." value="{{ $search }}" style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small text-muted fw-bold mb-1">Branch</label>
                        <select name="branch_id" class="form-select form-control" style="border-radius: 8px;">
                            <option value="">All Branches</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ (string)$branchId === (string)$b->id ? 'selected' : '' }}>
                                    {{ $b->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small text-muted fw-bold mb-1">Current Stage</label>
                        <select name="stage" class="form-select form-control" style="border-radius: 8px;">
                            <option value="">All Stages</option>
                            @foreach($stages as $key => $lbl)
                                <option value="{{ $key }}" {{ $stage === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <label class="form-label small text-muted fw-bold mb-1">Pipeline Status</label>
                        <select name="status" class="form-select form-control" style="border-radius: 8px;">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $key => $lbl)
                                <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-3 col-sm-6 d-flex gap-2">
                        <button type="submit" class="btn btn-navy-primary w-100 d-inline-flex align-items-center justify-content-center gap-1.5" style="height: 40px;">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                        @if($search || $branchId || $stage || $status)
                            <a href="{{ route('master-pipeline.index') }}" class="btn btn-slate d-inline-flex align-items-center justify-content-center" style="height: 40px; width: 44px; padding: 0;" title="Reset filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Master Projects Data Table Card --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <span>Registered Master Projects</span>
                        <span class="badge bg-light text-dark border px-2 py-0.5" style="font-size: 0.75rem; font-weight: 700;">
                            {{ $projects->total() }} Total
                        </span>
                    </h5>
                    <span class="text-muted small">Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of {{ $projects->total() }} projects</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge px-2.5 py-1 border" style="background: #ecfdf5; color: #047857; font-size: 0.75rem; border-color: #a7f3d0 !important; font-weight: 600;">
                        <i class="bi bi-shield-check text-success me-1"></i> Zero Cross-Contamination
                    </span>
                    <span class="badge px-2.5 py-1" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 0.75rem; font-weight: 600;">
                        <i class="bi bi-clock-history me-1"></i> Real-time Movement Audit
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table master-pipeline-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="min-width: 130px;">Master Project ID</th>
                                <th style="min-width: 135px;">Client / Company</th>
                                <th style="min-width: 130px;">Concession Parcel</th>
                                <th style="min-width: 140px;">Departments</th>
                                <th style="min-width: 110px;">Current Stage</th>
                                <th style="min-width: 105px;">Pipeline Status</th>
                                <th style="min-width: 100px;">Last Movement</th>
                                <th class="text-end pe-3" style="min-width: 115px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $p)
                                <tr class="{{ $p->isReturnedForCorrection() ? 'tr-returned' : '' }}">
                                    <td class="ps-3">
                                        <a href="{{ route('master-pipeline.timeline', $p->id) }}" class="master-id-pill d-inline-block">
                                            {{ $p->master_application_id }}
                                        </a>
                                        <div class="text-muted mt-0.5" style="font-size: 0.7rem;">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $p->created_at ? $p->created_at->format('d M Y') : 'N/A' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 140px;" title="{{ $p->customer?->customer_name }}">{{ $p->customer?->customer_name ?? 'N/A' }}</div>
                                        @if($p->customer?->company_name)
                                            <div class="text-muted small text-truncate" style="max-width: 140px; font-size: 0.72rem;" title="{{ $p->customer->company_name }}">{{ $p->customer->company_name }}</div>
                                        @endif
                                        @if($p->customer?->mobile_num)
                                            <div class="text-muted" style="font-size: 0.7rem;"><i class="bi bi-telephone-fill me-1 text-primary"></i>{{ $p->customer->mobile_num }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 135px;">
                                            {{ $p->village ?? '—' }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-1 mt-0.5">
                                            @if($p->area_extent_ha)
                                                <span class="badge bg-light text-dark border" style="font-size: 0.68rem; padding: 1px 4px;">{{ $p->area_extent_ha }} Ha</span>
                                            @endif
                                            @if($p->mineral_name)
                                                <span class="badge px-1 py-0.5" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-size: 0.68rem;">{{ $p->mineral_name }}</span>
                                            @endif
                                        </div>
                                        @if($p->survey_numbers)
                                            <div class="text-muted text-truncate mt-0.5" style="font-size: 0.7rem; max-width: 135px;" title="S.F. Nos: {{ $p->survey_numbers }}">
                                                <i class="bi bi-geo-alt-fill text-muted me-0.5"></i>S.F.: {{ $p->survey_numbers }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-1" style="max-width: 155px;">
                                            {{-- Lease Application Badge --}}
                                            @if($p->leaseApplication)
                                                <span class="pill-dept pill-dept-la" title="Lease: {{ $p->leaseApplication->application_no ?? 'LA-'.$p->leaseApplication->id }}">
                                                    LA-{{ $p->leaseApplication->id }}
                                                </span>
                                            @endif

                                            {{-- DGPS Survey Badge --}}
                                            @if($p->dgpsSurvey)
                                                <span class="pill-dept pill-dept-dgps" title="DGPS: {{ $p->dgpsSurvey->application_number ?? 'DGPS-'.$p->dgpsSurvey->id }}">
                                                    DGPS-{{ $p->dgpsSurvey->id }}
                                                </span>
                                            @endif

                                            {{-- Drone Survey Badge --}}
                                            @if($p->droneSurvey)
                                                <span class="pill-dept pill-dept-drone" title="Drone: {{ $p->droneSurvey->application_number ?? 'DRN-'.$p->droneSurvey->id }}">
                                                    DRN-{{ $p->droneSurvey->id }}
                                                </span>
                                            @endif

                                            {{-- Mining Plan Badge --}}
                                            @if($p->miningApplication)
                                                <span class="pill-dept pill-dept-mining" title="Mining: {{ $p->miningApplication->application_number ?? 'MP-'.$p->miningApplication->id }}">
                                                    MP-{{ $p->miningApplication->id }}
                                                </span>
                                            @endif

                                            {{-- Environmental Clearance Badge --}}
                                            @if($p->environmentProject)
                                                <span class="pill-dept pill-dept-env" title="Environment: {{ $p->environmentProject->project_code ?? 'ENV-'.$p->environmentProject->id }}">
                                                    ENV-{{ $p->environmentProject->id }}
                                                </span>
                                            @endif

                                            {{-- PPT Department Badge --}}
                                            @if($p->pptApplication)
                                                <span class="pill-dept pill-dept-ppt" title="PPT: {{ $p->pptApplication->application_no ?? 'PPT-'.$p->pptApplication->id }}">
                                                    PPT-{{ $p->pptApplication->id }}
                                                </span>
                                            @endif

                                            @if(!$p->lease_application_id && !$p->dgps_survey_id && !$p->drone_survey_id && !$p->mining_application_id && !$p->environment_project_id && !$p->ppt_application_id)
                                                <span class="text-muted small" style="font-size: 0.72rem;">None linked</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        @php
                                            $stageDots = [
                                                'lease'       => '#2563EB',
                                                'survey_dgps' => '#059669',
                                                'survey_drone'=> '#0891B2',
                                                'mining'      => '#D97706',
                                                'environment' => '#0F766E',
                                                'ppt'         => '#7C3AED',
                                                'completed'   => '#16A34A',
                                            ];
                                            $dotColor = $stageDots[$p->current_stage] ?? '#64748B';
                                        @endphp
                                        <div class="stage-pill">
                                            <span class="stage-dot" style="background-color: {{ $dotColor }};"></span>
                                            <span>{{ $p->stage_label }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        @php $badge = $p->status_badge; @endphp
                                        <span class="badge {{ $badge['class'] }} px-2 py-1 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem; border-radius: 5px;">
                                            <i class="bi {{ $badge['icon'] }}"></i> {{ $badge['label'] }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($p->latestMovement)
                                            <div class="small fw-bold text-dark text-truncate" style="font-size: 0.76rem; max-width: 105px;" title="{{ $p->latestMovement->action_label }}">
                                                {{ $p->latestMovement->action_label }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                <i class="bi bi-clock me-0.5"></i>{{ $p->latestMovement->created_at->diffForHumans() }}
                                            </div>
                                        @else
                                            <span class="text-muted small" style="font-size: 0.72rem;">Initial</span>
                                        @endif
                                    </td>

                                    <td class="text-end pe-3">
                                        <a href="{{ route('master-pipeline.timeline', $p->id) }}" class="btn btn-sm btn-navy-primary d-inline-flex align-items-center gap-1 px-2.5 py-1" style="font-size: 0.75rem; border-radius: 6px; white-space: nowrap;">
                                            <i class="bi bi-compass"></i> Timeline <i class="bi bi-chevron-right" style="font-size: 0.65rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <div class="py-4">
                                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-light text-muted mb-3" style="width: 64px; height: 64px; font-size: 1.8rem;">
                                                <i class="bi bi-folder-x"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark">No Master Projects Found</h6>
                                            <p class="small text-muted mb-0">Try adjusting your keyword search, stage, or branch filter criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($projects->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="text-muted small">
                        Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} results
                    </div>
                    <div>
                        {{ $projects->links() }}
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
