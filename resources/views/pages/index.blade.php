@extends('layouts.app')
@section('title', 'Dashboard')
@section('main_content')

<style>
    /* ==========================================================================
       GTMS EXECUTIVE DASHBOARD (UI-UX-PRO-MAX STANDARD)
       ========================================================================== */

    /* Dashboard Header & Metadata */
    .dash-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .dash-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #b45309;
        background: #fef3c7;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        border: 1px solid #fde68a;
        margin-bottom: 0.5rem;
    }
    .dash-title {
        font-family: 'Sora', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
    }
    .dash-subtitle {
        color: var(--ink-500);
        font-size: 0.88rem;
        margin: 0;
    }
    .dash-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.45rem 0.95rem;
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 30px;
        font-size: 0.78rem;
        color: var(--ink-700);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .live-dot-pulse {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseLive 2s infinite;
    }
    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Quick Action Workflow Ribbon */
    .quick-launch-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.35rem;
        margin-bottom: 1.5rem;
        scrollbar-width: thin;
    }
    .quick-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.42rem 0.85rem;
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ink-700);
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .quick-action-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        color: var(--navy-950);
    }
    .quick-action-pill.pill-lease:hover { border-color: #3b82f6; background: rgba(59, 130, 246, 0.05); }
    .quick-action-pill.pill-dgps:hover { border-color: #0284c7; background: rgba(2, 132, 199, 0.05); }
    .quick-action-pill.pill-drone:hover { border-color: #7c3aed; background: rgba(124, 58, 237, 0.05); }
    .quick-action-pill.pill-env:hover { border-color: #16a34a; background: rgba(22, 163, 74, 0.05); }
    .quick-action-pill.pill-ec:hover { border-color: #d97706; background: rgba(217, 119, 6, 0.05); }

    /* Primary & Technical Stat Cards */
    .stat-card-link {
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .dash-stat-card {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dash-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        border-color: rgba(59, 130, 246, 0.35);
    }
    .stat-ic-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .dash-stat-card:hover .stat-ic-box {
        transform: scale(1.06);
    }
    .stat-info {
        flex-grow: 1;
        min-width: 0;
    }
    .stat-num {
        font-family: 'Sora', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.15;
        color: var(--navy-950);
        margin-bottom: 0.15rem;
    }
    .stat-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--ink-500);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.25rem;
    }
    .stat-trend {
        font-size: 0.71rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Operations Matrix Cards */
    .dash-tech-card {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dash-tech-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .dash-tech-card.card-dgps { border-left: 4px solid #0284c7; }
    .dash-tech-card.card-drone { border-left: 4px solid #7c3aed; }
    .dash-tech-card.card-env { border-left: 4px solid #16a34a; }
    .dash-tech-card.card-ec { border-left: 4px solid #d97706; }

    /* Surface Panel Container */
    .dash-panel {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .panel-title {
        font-family: 'Sora', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0;
    }
    .panel-subtitle {
        font-size: 0.75rem;
        color: var(--ink-500);
        margin: 0;
    }

    /* Modern Table Design */
    .dash-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .dash-table th {
        font-size: 0.69rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ink-500);
        background: transparent;
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid var(--ink-100);
        white-space: nowrap;
    }
    .dash-table td {
        padding: 0.85rem 0.85rem;
        font-size: 0.82rem;
        color: var(--ink-700);
        border-bottom: 1px solid var(--ink-100);
        vertical-align: middle;
    }
    .dash-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .dash-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.03);
    }
    .dash-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Client Avatar Initial Chip */
    .client-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .client-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.06);
    }

    /* Chip Badges */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        line-height: 1.2;
    }
    .chip-ok {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .chip-warn {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .chip-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .chip-navy {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .survey-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .survey-badge-dgps {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .survey-badge-drone {
        background: #f3e8ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }

    /* Action Button in Tables */
    .btn-table-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.28rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid var(--ink-100);
        background: var(--paper-raised);
        color: var(--navy-950);
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .btn-table-action:hover {
        background: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }

    /* Chart Container & Doughnut Center Overlay */
    .chart-container-rel {
        position: relative;
        flex-grow: 1;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-donut-center {
        position: absolute;
        top: 44%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    .chart-center-val {
        font-family: 'Sora', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--navy-950);
        line-height: 1;
    }
    .chart-center-lbl {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--ink-500);
        letter-spacing: 0.05em;
        margin-top: 0.2rem;
    }

    /* Activity Timeline */
    .activity-feed-modern {
        position: relative;
        padding-left: 22px;
    }
    .activity-feed-modern::before {
        content: '';
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 7px;
        width: 2px;
        background: var(--ink-100);
    }
    .activity-item-modern {
        position: relative;
        padding-bottom: 1.25rem;
    }
    .activity-item-modern:last-child {
        padding-bottom: 0;
    }
    .activity-dot-modern {
        position: absolute;
        left: -22px;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--paper-raised);
        border: 2px solid #3b82f6;
        box-shadow: 0 0 0 2px var(--paper);
    }
    .activity-dot-modern.dot-ok { border-color: #10b981; }
    .activity-dot-modern.dot-danger { border-color: #ef4444; }
    .activity-dot-modern.dot-warn { border-color: #f59e0b; }

    /* Financial Cards Scoped */
    .fin-metric-box {
        padding: 1rem 1.15rem;
        border-radius: 10px;
        border: 1px solid var(--ink-100);
        background: var(--paper);
        transition: transform 0.15s ease;
    }
    .fin-metric-box:hover {
        transform: translateY(-1px);
    }
    .fin-invoiced {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .fin-paid {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .fin-pending {
        background: #fffbeb;
        border-color: #fde68a;
    }

    /* District Snapshot Cards */
    .district-stat-card {
        padding: 0.95rem;
        border-radius: 10px;
        background: var(--paper);
        border: 1px solid var(--ink-100);
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.15s ease, border-color 0.15s ease;
    }
    .district-stat-card:hover {
        transform: translateY(-2px);
        border-color: #3b82f6;
    }

    /* Empty state display */
    .empty-state-wrap {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--ink-500);
    }

    /* ==========================================================================
       DARK MODE SPECIALIZED OVERRIDES
       ========================================================================== */
    body[data-theme-version="dark"] .dash-eyebrow {
        background: rgba(245, 158, 11, 0.12);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    body[data-theme-version="dark"] .client-avatar {
        background: #2a2d36;
        color: #cbd5e1;
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .dash-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }
    body[data-theme-version="dark"] .chip-ok {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border-color: rgba(34, 197, 94, 0.25);
    }
    body[data-theme-version="dark"] .chip-warn {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    body[data-theme-version="dark"] .chip-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.25);
    }
    body[data-theme-version="dark"] .chip-navy {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.25);
    }
    body[data-theme-version="dark"] .survey-badge-dgps {
        background: rgba(2, 132, 199, 0.15);
        color: #38bdf8;
        border-color: rgba(2, 132, 199, 0.25);
    }
    body[data-theme-version="dark"] .survey-badge-drone {
        background: rgba(124, 58, 237, 0.15);
        color: #c084fc;
        border-color: rgba(124, 58, 237, 0.25);
    }
    body[data-theme-version="dark"] .fin-invoiced {
        background: rgba(59, 130, 246, 0.06);
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .fin-paid {
        background: rgba(34, 197, 94, 0.08);
        border-color: rgba(34, 197, 94, 0.2);
    }
    body[data-theme-version="dark"] .fin-pending {
        background: rgba(245, 158, 11, 0.08);
        border-color: rgba(245, 158, 11, 0.2);
    }
    body[data-theme-version="dark"] .activity-dot-modern {
        box-shadow: 0 0 0 2px #202020;
    }
    body[data-theme-version="dark"] .dash-panel .text-dark,
    body[data-theme-version="dark"] .fin-metric-box .text-dark,
    body[data-theme-version="dark"] .district-stat-card .text-dark,
    body[data-theme-version="dark"] .dash-stat-card .text-dark,
    body[data-theme-version="dark"] .dash-table .text-dark,
    body[data-theme-version="dark"] .dash-meta-badge .text-dark {
        color: #f8fafc !important;
    }
    body[data-theme-version="dark"] .dash-panel .text-navy {
        color: #60a5fa !important;
    }
    body[data-theme-version="dark"] .district-stat-card {
        background: #1a1a24;
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .badge.bg-light.text-dark {
        background: #2a2d36 !important;
        color: #e2e8f0 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body[data-theme-version="dark"] .btn-table-action {
        background: #202020;
        border-color: #3b4252;
        color: #cbd5e1;
    }
    body[data-theme-version="dark"] .btn-table-action:hover {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }
</style>

<div class="content-body default-height">
    <div class="container-fluid">
        <main class="page">
            
            <!-- Modern Dynamic Welcome Header -->
            <div class="dash-head">
                <div>
                    <span class="dash-eyebrow">
                        <i class="bi bi-shield-check"></i> {{ $regionName }}
                    </span>
                    <h1 class="dash-title">
                        {{ $greeting }}, {{ Auth::user()->name ?? 'Officer' }} 👋
                    </h1>
                    <p class="dash-subtitle">
                        @if($pendingCount > 0)
                            <span class="fw-semibold text-warning-emphasis">{{ $pendingCount }}</span> {{ \Illuminate\Support\Str::plural('application', $pendingCount) }} awaiting statutory action across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @else
                            All applications and technical geospatial surveys are operational across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @endif
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="dash-meta-badge">
                        <span class="live-dot-pulse"></span>
                        <span class="fw-semibold text-dark">System Online</span>
                        <span class="text-muted">|</span>
                        <i class="bi bi-calendar3 text-muted"></i>
                        <span>{{ date('D, d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Workflow Ribbon -->
            <div class="quick-launch-bar">
                <span class="small fw-bold text-muted text-uppercase me-2" style="font-size: 0.7rem; letter-spacing: 0.06em;">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Launch:
                </span>
                @can('application.create')
                <a href="{{ route('step1') }}" class="quick-action-pill pill-lease">
                    <i class="bi bi-file-earmark-plus text-primary"></i>
                    <span>Lease Application</span>
                </a>
                @endcan
                @can('dgps.create')
                <a href="{{ route('dgps-survey.step', ['step' => 1]) }}" class="quick-action-pill pill-dgps">
                    <i class="bi bi-pin-map-fill" style="color: #0284c7;"></i>
                    <span>DGPS Survey</span>
                </a>
                @endcan
                @can('drone.create')
                <a href="{{ route('drone-survey.step', ['step' => 1]) }}" class="quick-action-pill pill-drone">
                    <i class="bi bi-airplane-fill" style="color: #7c3aed;"></i>
                    <span>Drone Survey</span>
                </a>
                @endcan
                @can('environment.b2.create')
                <a href="{{ route('eviron.create') }}" class="quick-action-pill pill-env">
                    <i class="bi bi-tree-fill text-success"></i>
                    <span>Env Clearance</span>
                </a>
                @endcan
                @can('ec_compliance.create')
                <a href="{{ route('ec-compliance.step', ['step' => 1]) }}" class="quick-action-pill pill-ec">
                    <i class="bi bi-clipboard2-check text-warning"></i>
                    <span>EC Compliance</span>
                </a>
                @endcan
            </div>

            <!-- Primary KPI Stat Cards (Lease & Mining Applications) -->
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Active Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $activeCount }}</div>
                                <div class="stat-label">Active Applications</div>
                                <div class="stat-trend text-success"><i class="bi bi-check2-circle"></i> Operational</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Pending Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $pendingCount }}</div>
                                <div class="stat-label">Pending Validation</div>
                                <div class="stat-trend text-warning-emphasis"><i class="bi bi-clock"></i> Review Needed</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Approved Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(34, 197, 94, 0.12); color: #16a34a;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $approvedCount }}</div>
                                <div class="stat-label">Approved &amp; Verified</div>
                                <div class="stat-trend text-success"><i class="bi bi-shield-check"></i> All-Time Safe</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Archived Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(148, 163, 184, 0.15); color: #64748b;">
                                <i class="bi bi-archive-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $archivedCount }}</div>
                                <div class="stat-label">Archived Files</div>
                                <div class="stat-trend text-muted"><i class="bi bi-lock-fill"></i> Secured</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Technical Operations & Surveys Execution Matrix -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('dgps-survey.index') }}" class="stat-card-link" title="View DGPS Surveys">
                        <div class="dash-tech-card card-dgps">
                            <div class="stat-ic-box" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                                <i class="bi bi-pin-map-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $dgpsTotal }}</div>
                                <div class="stat-label">DGPS Demarcation</div>
                                <div class="stat-trend text-success"><i class="bi bi-check2-circle"></i> {{ $dgpsCompleted }} Completed</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('drone-survey.index') }}" class="stat-card-link" title="View Drone Surveys">
                        <div class="dash-tech-card card-drone">
                            <div class="stat-ic-box" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                                <i class="bi bi-airplane-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $droneTotal }}</div>
                                <div class="stat-label">Drone Aerial Topo</div>
                                <div class="stat-trend" style="color:#7c3aed;"><i class="bi bi-folder-check"></i> {{ $droneReady }} Reports Ready</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('eviron.index') }}" class="stat-card-link" title="View Environmental Projects">
                        <div class="dash-tech-card card-env">
                            <div class="stat-ic-box" style="background: rgba(22, 163, 74, 0.12); color: #16a34a;">
                                <i class="bi bi-tree-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $envProjectsTotal }}</div>
                                <div class="stat-label">Env Clearances</div>
                                <div class="stat-trend text-success"><i class="bi bi-shield-check"></i> B1 / B2 Active</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('ec-compliance.index') }}" class="stat-card-link" title="View EC Compliances">
                        <div class="dash-tech-card card-ec">
                            <div class="stat-ic-box" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">
                                <i class="bi bi-clipboard-data-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $ecComplianceTotal }}</div>
                                <div class="stat-label">EC Compliance</div>
                                <div class="stat-trend text-warning-emphasis"><i class="bi bi-clock-history"></i> Half-Yearly Cycles</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Row 1: Recent Applications & Operations Breakdown Chart -->
            <div class="row g-3 mb-4">
                <!-- Recent applications -->
                <div class="col-lg-8">
                    <div class="dash-panel d-flex flex-column">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">Recent Applications</h2>
                                <p class="panel-subtitle">Mining lease and quarry permit pipeline</p>
                            </div>
                            <a href="{{ route('application.index') }}" class="small text-decoration-none fw-semibold">
                                View all ({{ $approvedCount + $activeCount }}) →
                            </a>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="dash-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>District</th>
                                        <th>Mineral</th>
                                        <th>Plan Type</th>
                                        <th>Stage</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplications as $app)
                                    @php
                                        $words = explode(' ', trim($app->client));
                                        $initials = '';
                                        foreach(array_slice($words, 0, 2) as $w) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                        }
                                        if(empty($initials)) $initials = 'LA';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="client-cell">
                                                <div class="client-avatar">{{ $initials }}</div>
                                                <div>
                                                    <span class="fw-semibold text-dark">{{ $app->client }}</span>
                                                    @if(isset($app->id))
                                                        <div class="text-muted" style="font-size: 0.7rem;">Ref #LA-{{ str_pad($app->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="fw-medium">{{ $app->district }}</span></td>
                                        <td><span class="badge bg-light text-dark border">{{ $app->mineral }}</span></td>
                                        <td><span class="small text-muted">{{ $app->plan_type }}</span></td>
                                        <td>
                                            <span class="chip chip-{{ $app->status_class }}">
                                                <i class="bi bi-{{ $app->icon }}"></i> {{ $app->stage }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            @if(isset($app->id))
                                                <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn-table-action">
                                                    <span>Open</span>
                                                    <i class="bi bi-arrow-right-short"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('application.index') }}" class="btn-table-action">
                                                    <span>Open</span>
                                                    <i class="bi bi-arrow-right-short"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-2 text-muted opacity-50 d-block mb-2"></i>
                                            No recent applications found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Applications Breakdown Chart -->
                <div class="col-lg-4">
                    <div class="dash-panel d-flex flex-column">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">Operations Breakdown</h2>
                                <p class="panel-subtitle">Statutory project distribution</p>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                                {{ array_sum($chartData['data']) }} Total
                            </span>
                        </div>
                        <div class="chart-container-rel">
                            <canvas id="appBreakdownChart"></canvas>
                            <div class="chart-donut-center">
                                <div class="chart-center-val">{{ array_sum($chartData['data']) }}</div>
                                <div class="chart-center-lbl">Operations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Recent Field Surveys & Live Activity Stream -->
            <div class="row g-3 mb-4">
                <!-- Recent Field Surveys (DGPS & Drone) -->
                <div class="col-lg-7">
                    <div class="dash-panel d-flex flex-column">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">Recent Field &amp; Geospatial Surveys</h2>
                                <p class="panel-subtitle">Latest DGPS demarcations &amp; aerial photogrammetry</p>
                            </div>
                            <a href="{{ route('dgps-survey.index') }}" class="small text-decoration-none fw-semibold">
                                All Surveys →
                            </a>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="dash-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Survey Ref</th>
                                        <th>Client / Location</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th class="text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSurveys as $survey)
                                    <tr>
                                        <td>
                                            <span class="fw-bold font-monospace text-dark" style="font-size: 0.8rem;">{{ $survey->survey_no }}</span>
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $survey->date }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-truncate" style="max-width: 170px;">{{ $survey->client }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 170px;"><i class="bi bi-geo-alt"></i> {{ $survey->location }}</div>
                                        </td>
                                        <td>
                                            @if($survey->type === 'DGPS Demarcation')
                                                <span class="survey-badge survey-badge-dgps"><i class="bi bi-pin-map me-1"></i> DGPS</span>
                                            @else
                                                <span class="survey-badge survey-badge-drone"><i class="bi bi-airplane me-1"></i> Drone</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="chip chip-{{ $survey->status_class }}">
                                                <i class="bi {{ $survey->status_class === 'ok' ? 'bi-check2' : 'bi-clock' }}"></i>
                                                {{ $survey->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $survey->url }}" class="btn-table-action">
                                                <span>Inspect</span>
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state-wrap py-2">
                                                <i class="bi bi-geo-alt fs-2 text-muted opacity-50 d-block mb-2"></i>
                                                <div class="fw-semibold text-dark">No Active Field Surveys</div>
                                                <div class="small text-muted">Demarcations and drone flights will appear here as field missions start.</div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Live System Audit & Activity Log -->
                <div class="col-lg-5">
                    <div class="dash-panel d-flex flex-column">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">System Audit &amp; Activity Trail</h2>
                                <p class="panel-subtitle">Real-time actions &amp; officer verification</p>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">
                                Live Log
                            </span>
                        </div>
                        <div class="activity-feed-modern flex-grow-1 overflow-y-auto pe-1" style="max-height: 330px;">
                            @forelse($recentActivities as $act)
                            <div class="activity-item-modern">
                                <span class="activity-dot-modern {{ str_contains($act->badge_class, 'ok') ? 'dot-ok' : (str_contains($act->badge_class, 'danger') ? 'dot-danger' : 'dot-warn') }}"></span>
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="chip {{ $act->badge_class }} px-2 py-0" style="font-size: 0.68rem;">{{ $act->action }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $act->time_ago }}</span>
                                </div>
                                <div class="small fw-semibold text-dark mb-1" style="font-size: 0.82rem; line-height: 1.35;">
                                    {{ $act->description }}
                                </div>
                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-person-circle"></i> {{ $act->user }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-activity fs-2 text-muted opacity-50 d-block mb-2"></i>
                                No recent activity logged.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Financial Revenue Health & District Geospatial Snapshot -->
            <div class="row g-3 mb-2">
                <!-- Financial Collection Health -->
                <div class="col-12 col-lg-7">
                    <div class="dash-panel">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">Financial Revenue &amp; Recovery Health</h2>
                                <p class="panel-subtitle">Statutory fee collection &amp; outstanding balances</p>
                            </div>
                            <span class="chip chip-ok"><i class="bi bi-shield-check"></i> {{ $recoveryRate }}% Collection Rate</span>
                        </div>

                        <!-- Progress Recovery Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span class="fw-semibold">Realized Collection ({{ $recoveryRate }}%)</span>
                                <span>Outstanding Pending ({{ 100 - $recoveryRate }}%)</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px; background-color: rgba(245, 158, 11, 0.2);">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $recoveryRate }}%" aria-valuenow="{{ $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $recoveryRate }}%" aria-valuenow="{{ 100 - $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-invoiced">
                                    <div class="small text-muted mb-1"><i class="bi bi-receipt text-primary me-1"></i> Total Invoiced</div>
                                    <div class="fw-bold fs-5 text-dark" style="font-family:'Sora';">₹{{ number_format($totalBilled, 2) }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Combined ledger</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-paid">
                                    <div class="small text-success mb-1"><i class="bi bi-cash-stack me-1"></i> Paid Realized</div>
                                    <div class="fw-bold fs-5 text-success" style="font-family:'Sora';">₹{{ number_format($totalPaid, 2) }}</div>
                                    <div class="text-success" style="font-size: 0.7rem;">Verified payments</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-pending">
                                    <div class="small text-warning-emphasis mb-1"><i class="bi bi-hourglass-split me-1"></i> Pending Dues</div>
                                    <div class="fw-bold fs-5 text-warning-emphasis" style="font-family:'Sora';">₹{{ number_format($totalPending, 2) }}</div>
                                    <div class="text-warning-emphasis" style="font-size: 0.7rem;">Awaiting collection</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- District Footprint Snapshot -->
                <div class="col-12 col-lg-5">
                    <div class="dash-panel">
                        <div class="panel-header">
                            <div>
                                <h2 class="panel-title">Jurisdiction District Footprint</h2>
                                <p class="panel-subtitle">Field operations &amp; site distribution</p>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">
                                {{ max(1, $districtStats->count()) }} Active Hubs
                            </span>
                        </div>
                        <div class="row g-3 text-center">
                            @forelse($districtStats as $stat)
                            <div class="col-sm-4">
                                <div class="district-stat-card">
                                    <div class="fw-bold fs-4 text-navy" style="font-family:'Sora';">{{ $stat->total }}</div>
                                    <div class="small fw-semibold text-dark">{{ $stat->name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Mining &amp; Leases</div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="district-stat-card text-muted">No district data available</div>
                            </div>
                            @endforelse
                            <div class="col-sm-4">
                                <div class="district-stat-card">
                                    <div class="fw-bold fs-4 text-primary" style="font-family:'Sora';">108</div>
                                    <div class="small fw-semibold text-dark">Salem Sector 4</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">DGPS Boundary Hub</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="district-stat-card">
                                    <div class="fw-bold fs-4" style="font-family:'Sora'; color:#7c3aed;">116</div>
                                    <div class="small fw-semibold text-dark">Quarry Sector 4</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Drone Flight Grid</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var canvasEl = document.getElementById('appBreakdownChart');
    if (!canvasEl) return;
    
    var ctx = canvasEl.getContext('2d');
    var chartData = {!! json_encode($chartData) !!};
    
    // Theme awareness
    var isDark = document.body.getAttribute('data-theme-version') === 'dark';
    var sliceBorderColor = isDark ? '#202020' : '#ffffff';
    var legendTextColor = isDark ? '#cbd5e1' : '#475569';
    
    var breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    '#0284C7', // Sky Blue for DGPS
                    '#7C3AED', // Purple for Drone
                    '#16A34A', // Green for Environment
                    '#0B2B5E', // Navy for Lease
                    '#D4AF37'  // Gold for Mining
                ],
                borderWidth: 2,
                borderColor: sliceBorderColor,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        color: legendTextColor,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' records';
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Observer to re-tune chart border and legend on theme toggle
    var themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-theme-version') {
                var currentDark = document.body.getAttribute('data-theme-version') === 'dark';
                breakdownChart.data.datasets[0].borderColor = currentDark ? '#202020' : '#ffffff';
                breakdownChart.options.plugins.legend.labels.color = currentDark ? '#cbd5e1' : '#475569';
                breakdownChart.update();
            }
        });
    });
    themeObserver.observe(document.body, { attributes: true, attributeFilter: ['data-theme-version'] });
});
</script>

@endsection
