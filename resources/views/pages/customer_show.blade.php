@extends('layouts.app')
@section('title', ($customer->company_name ?? $customer->customer_name) . ' — Enterprise Dossier')
@section('main_content')

<style>
    /* ============================================================
       GTMS EXECUTIVE DOSSIER — UI/UX PRO MAX DESIGN SYSTEM
       ============================================================ */
    :root {
        --dossier-bg: #f8fafc;
        --dossier-card: #ffffff;
        --dossier-border: #e2e8f0;
        --dossier-ink-dark: #0f172a;
        --dossier-ink-body: #334155;
        --dossier-ink-muted: #64748b;
        --dossier-brand: #2563eb;
        --dossier-brand-soft: #eff6ff;
        --dossier-success: #10b981;
        --dossier-success-soft: #ecfdf5;
        --dossier-warning: #f59e0b;
        --dossier-warning-soft: #fffbeb;
        --dossier-purple: #8b5cf6;
        --dossier-purple-soft: #f5f3ff;
    }

    .dossier-shell {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--dossier-ink-body);
    }

    /* Top Breadcrumb Header */
    .dossier-header-bar {
        background: #ffffff;
        border: 1px solid var(--dossier-border);
        border-radius: 16px;
        padding: 1rem 1.4rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Profile Sidebar Card */
    .profile-sidebar-card {
        background: #ffffff;
        border: 1px solid var(--dossier-border);
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03), 0 2px 4px -2px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .profile-header-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #6366f1 100%);
        height: 80px;
        position: relative;
    }

    .profile-avatar-wrapper {
        position: relative;
        margin-top: -45px;
        margin-bottom: 12px;
        display: inline-block;
    }

    .profile-avatar-circle {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: linear-gradient(145deg, #0f172a, #1e293b);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.85rem;
        letter-spacing: 1px;
        border: 4px solid #ffffff;
        box-shadow: 0 8px 16px -4px rgba(15, 23, 42, 0.25);
    }

    .status-blip-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .status-blip-badge.active {
        background: var(--dossier-success-soft);
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .status-blip-badge.pending {
        background: var(--dossier-warning-soft);
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .status-blip-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .info-chip-card {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 12px;
        padding: 0.75rem 0.9rem;
        margin-bottom: 0.6rem;
        transition: all 0.2s ease;
    }
    .info-chip-card:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    }

    .code-badge {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.8rem;
        font-weight: 700;
        color: #1e293b;
        background: #e2e8f0;
        padding: 2px 8px;
        border-radius: 6px;
        letter-spacing: 0.05em;
    }

    /* KPI Summary Cards */
    .kpi-metric-card {
        background: #ffffff;
        border: 1px solid var(--dossier-border);
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        height: 100%;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
    }
    .kpi-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    /* Module Navigation Segmented Control */
    .segmented-tabs-wrapper {
        background: #f1f5f9;
        border: 1px solid var(--dossier-border);
        border-radius: 14px;
        padding: 5px;
        display: flex;
        gap: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .segmented-tabs-wrapper::-webkit-scrollbar { display: none; }

    .segmented-tab-btn {
        border: none;
        background: transparent;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 7px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .segmented-tab-btn:hover {
        color: #1e293b;
        background: rgba(255,255,255,0.6);
    }
    .segmented-tab-btn.active {
        background: #ffffff !important;
        color: #0f172a !important;
        font-weight: 700;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    }
    .segmented-tab-btn .tab-count-pill {
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
    }
    .segmented-tab-btn.active .tab-count-pill {
        background: #eff6ff;
        color: #2563eb;
    }

    /* Modern Minimalist Tables */
    .dossier-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .dossier-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.06em;
        padding: 0.85rem 1rem;
        border-top: none;
        border-bottom: 1px solid var(--dossier-border);
        white-space: nowrap;
    }
    .dossier-table tbody td {
        padding: 0.95rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: #1e293b;
    }
    .dossier-table tbody tr:hover {
        background-color: #fafcff;
    }
    .dossier-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Clean Empty State */
    .empty-state-box {
        text-align: center;
        padding: 3.5rem 1.5rem;
    }
    .empty-state-icon {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
</style>

<div class="content-body default-height dossier-shell">
    <div class="container-fluid">
        
        <!-- TOP BREADCRUMB HEADER -->
        <div class="dossier-header-bar d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 p-0 bg-transparent" style="font-size: .8rem;">
                        <li class="breadcrumb-item">
                            <a href="{{ route('customers.index') }}" class="text-primary text-decoration-none fw-semibold">
                                <i class="fa fa-users me-1"></i> Customer Directory
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-muted fw-bold" aria-current="page">
                            {{ $customer->slug }}
                        </li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.01em;">
                        {{ $customer->company_name ?? $customer->customer_name }}
                    </h4>
                    <span class="code-badge">CUST-{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</span>
                    @if($customer->mimas_no)
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-bold" title="Universal MIMAS ID"><i class="fa fa-fingerprint me-1"></i>{{ $customer->mimas_no }}</span>
                    @endif
                    @if($customer->status == 1)
                        <span class="status-blip-badge active"><span class="status-blip-dot"></span>Active</span>
                    @else
                        <span class="status-blip-badge pending"><span class="status-blip-dot"></span>Under Validation</span>
                    @endif
                </div>
            </div>
            <div>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary px-3 py-2 fw-semibold rounded-pill">
                    <i class="fa fa-arrow-left me-1"></i> Back to Directory
                </a>
            </div>
        </div>

        <!-- SPLIT-SCREEN LAYOUT -->
        <div class="row g-4">

            <!-- ============================================================= -->
            <!-- LEFT COLUMN: STICKY CUSTOMER PROFILE DOSSIER (col-xl-3 col-lg-4) -->
            <!-- ============================================================= -->
            <div class="col-xl-3 col-lg-4">
                <div class="profile-sidebar-card sticky-top" style="top: 85px;">
                    <!-- Banner -->
                    <div class="profile-header-banner"></div>
                    
                    <!-- Avatar & Titles -->
                    <div class="px-4 pb-3 text-center border-bottom">
                        <div class="profile-avatar-wrapper">
                            <div class="profile-avatar-circle">
                                {{ strtoupper(substr($customer->company_name ?? $customer->customer_name, 0, 2)) }}
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">
                            {{ $customer->company_name ?? $customer->customer_name }}
                        </h5>
                        <div class="text-muted small mb-2 d-flex align-items-center justify-content-center gap-1">
                            <i class="fa fa-user-tie text-secondary"></i>
                            <span>{{ $customer->customer_name }}</span>
                        </div>
                    </div>

                    <!-- Profile Metadata List -->
                    <div class="p-3">
                        
                        <!-- Legal & Tax Identifiers -->
                        <div class="mb-3">
                            <span class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: .68rem; letter-spacing: .08em;">
                                Tax & Business Identifiers
                            </span>
                            <div class="info-chip-card d-flex justify-content-between align-items-center mb-2" style="background:#eff6ff;border:1px solid #bfdbfe;">
                                <span class="text-primary fw-bold small"><i class="fa fa-fingerprint me-1"></i> MIMAS Number</span>
                                <span class="badge bg-primary text-white fw-bold" style="letter-spacing:0.04em;">{{ $customer->mimas_no ?? 'N/A' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small">PAN Number</span>
                                <span class="code-badge">{{ $customer->pan ?? 'N/A' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-id-card me-1 text-info"></i> Aadhaar Number</span>
                                <span class="code-badge" style="font-size:.78rem;color:#0369a1;background:#e0f2fe;">{{ $customer->aadhaar_no ?? 'N/A' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small">GSTIN Number</span>
                                <span class="code-badge" style="font-size:.74rem;">{{ $customer->gstin ?? 'Not Registered' }}</span>
                            </div>
                        </div>

                        <!-- Contact & Jurisdiction -->
                        <div class="mb-3">
                            <span class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: .68rem; letter-spacing: .08em;">
                                Contact & Jurisdiction
                            </span>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-map-marker-alt me-1 text-danger"></i> District</span>
                                <span class="fw-bold text-dark small">{{ $customer->district->name ?? 'Unassigned' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-phone me-1 text-primary"></i> Mobile</span>
                                <a href="tel:{{ $customer->mobile_num }}" class="fw-bold text-primary text-decoration-none small">
                                    {{ $customer->mobile_num }}
                                </a>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-envelope me-1 text-info"></i> Email</span>
                                <span class="small text-truncate" style="max-width: 130px;" title="{{ $customer->email }}">
                                    {{ $customer->email ?? 'No email' }}
                                </span>
                            </div>
                        </div>

                        <!-- Registered Office Address -->
                        <div class="mb-3">
                            <span class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: .68rem; letter-spacing: .08em;">
                                Registered Business Address
                            </span>
                            <div class="p-2 rounded bg-light border text-muted small lh-sm">
                                <i class="fa fa-building me-1 text-secondary"></i>
                                {{ $customer->address ?? 'No physical office address registered.' }}
                            </div>
                        </div>

                        <!-- Footprint Summary -->
                        <div class="pt-2 border-top">
                            <div class="row g-2 text-center" style="font-size: .78rem;">
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light border">
                                        <div class="fw-bold fs-6 text-dark">{{ $totalLeases }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Leases</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light border">
                                        <div class="fw-bold fs-6 text-dark">{{ $totalMiningPlans }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Mining</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 rounded bg-light border">
                                        <div class="fw-bold fs-6 text-dark">{{ $customer->stockpiles->count() }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Stockpiles</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center text-muted" style="font-size: .72rem;">
                            Profile Slug: <span class="font-monospace text-dark">{{ $customer->slug }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================= -->
            <!-- RIGHT COLUMN: KPI CARDS + 6 MODULE TABS (col-xl-9 col-lg-8)   -->
            <!-- ============================================================= -->
            <div class="col-xl-9 col-lg-8">

                <!-- 4 TOP KPI METRIC CARDS -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="kpi-metric-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted fw-semibold text-uppercase d-block" style="font-size: .72rem; letter-spacing:.05em;">Total Leases</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.65rem;">{{ $totalLeases }}</h3>
                                </div>
                                <div class="kpi-icon-box" style="background: var(--dossier-brand-soft); color: var(--dossier-brand);">
                                    <i class="fa fa-file-signature"></i>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">Registered Quarry Leases</small>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="kpi-metric-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted fw-semibold text-uppercase d-block" style="font-size: .72rem; letter-spacing:.05em;">Mining Plans</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.65rem;">
                                        {{ $approvedMiningPlans }}
                                        <span class="text-muted fs-6 fw-normal">/ {{ $totalMiningPlans }}</span>
                                    </h3>
                                </div>
                                <div class="kpi-icon-box" style="background: #f0fdf4; color: #16a34a;">
                                    <i class="fa fa-mountain"></i>
                                </div>
                            </div>
                            <small class="text-success fw-semibold" style="font-size:.75rem;">
                                <i class="fa fa-check-circle me-1"></i> {{ $approvedMiningPlans }} Approved Plans
                            </small>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="kpi-metric-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted fw-semibold text-uppercase d-block" style="font-size: .72rem; letter-spacing:.05em;">Active ECs</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.65rem;">{{ $activeEcCount }}</h3>
                                </div>
                                <div class="kpi-icon-box" style="background: var(--dossier-purple-soft); color: var(--dossier-purple);">
                                    <i class="fa fa-leaf"></i>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">SEIAA Environment Clearances</small>
                        </div>
                    </div>

                    <div class="col-sm-6 col-xl-3">
                        <div class="kpi-metric-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted fw-semibold text-uppercase d-block" style="font-size: .72rem; letter-spacing:.05em;">Pithead Stock</span>
                                    <h3 class="fw-bold text-dark mb-0 mt-1" style="font-size: 1.65rem;">
                                        {{ number_format($currentStockpileCbm, 0) }}
                                        <span class="fs-6 fw-normal text-muted">CBM</span>
                                    </h3>
                                </div>
                                <div class="kpi-icon-box" style="background: var(--dossier-warning-soft); color: var(--dossier-warning);">
                                    <i class="fa fa-cubes"></i>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">Extracted Available Balance</small>
                        </div>
                    </div>
                </div>

                <!-- 6 MODULE SEGMENTED NAVIGATION TABS -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 18px; overflow:hidden;">
                    
                    <!-- Segmented Control Nav Header -->
                    <div class="p-3 bg-white border-bottom">
                        <div class="segmented-tabs-wrapper" id="customerDossierTabs" role="tablist">
                            <button class="segmented-tab-btn active" id="tab-leases-btn" data-bs-toggle="pill" data-bs-target="#tab-leases" type="button" role="tab">
                                <i class="fa fa-file-contract"></i>
                                <span>Lease Applications</span>
                                <span class="tab-count-pill">{{ $customer->leaseApplications->count() }}</span>
                            </button>
                            
                            <button class="segmented-tab-btn" id="tab-mining-btn" data-bs-toggle="pill" data-bs-target="#tab-mining" type="button" role="tab">
                                <i class="fa fa-mountain"></i>
                                <span>Mining Plans</span>
                                <span class="tab-count-pill">{{ $customer->miningApplications->count() }}</span>
                            </button>

                            <button class="segmented-tab-btn" id="tab-environment-btn" data-bs-toggle="pill" data-bs-target="#tab-environment" type="button" role="tab">
                                <i class="fa fa-leaf"></i>
                                <span>Environment & EC</span>
                                <span class="tab-count-pill">{{ $customer->environmentProjects->count() }}</span>
                            </button>

                            <button class="segmented-tab-btn" id="tab-ppt-btn" data-bs-toggle="pill" data-bs-target="#tab-ppt" type="button" role="tab">
                                <i class="fa fa-chalkboard-teacher"></i>
                                <span>PPT Department</span>
                                <span class="tab-count-pill">{{ $customer->pptApplications->count() }}</span>
                            </button>

                            <button class="segmented-tab-btn" id="tab-surveys-btn" data-bs-toggle="pill" data-bs-target="#tab-surveys" type="button" role="tab">
                                <i class="fa fa-satellite-dish"></i>
                                <span>DGPS & Drone Surveys</span>
                                <span class="tab-count-pill">{{ $customer->dgpsSurveys->count() + $customer->droneSurveys->count() }}</span>
                            </button>

                            <button class="segmented-tab-btn" id="tab-stockpile-btn" data-bs-toggle="pill" data-bs-target="#tab-stockpile" type="button" role="tab">
                                <i class="fa fa-truck-moving"></i>
                                <span>Stockpile & Logistics</span>
                                <span class="tab-count-pill">{{ $customer->stockpiles->count() }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- TAB CONTENT PANES -->
                    <div class="card-body p-0">
                        <div class="tab-content" id="customerDossierTabsContent">

                            <!-- ========================================================= -->
                            <!-- TAB 1: LEASE APPLICATIONS                                 -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade show active p-3" id="tab-leases" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Quarry Lease Applications</h6>
                                        <small class="text-muted">Statutory leases under TN Minor Mineral Concession Rules with Survey Numbers</small>
                                    </div>
                                    <span class="badge bg-light text-muted border">{{ $customer->leaseApplications->count() }} Records</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>App No / Order</th>
                                                <th>Category</th>
                                                <th>Mineral</th>
                                                <th>Location</th>
                                                <th>Area (Ha)</th>
                                                <th>Survey & Sub-div Nos</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->leaseApplications as $lease)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-primary font-monospace">{{ $lease->application_no }}</span>
                                                    @if($lease->go_number)
                                                        <div class="text-muted small" style="font-size: .72rem;">GO: {{ $lease->go_number }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $lease->category->name ?? 'Standard Rule' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary">
                                                        <i class="fa fa-gem me-1"></i> {{ $lease->mineral->name ?? 'Unassigned' }}
                                                    </span>
                                                </td>
                                                <td>{{ $lease->village ?? '-' }}, {{ $lease->taluk ?? '-' }}</td>
                                                <td class="fw-bold text-dark font-monospace">{{ number_format($lease->area_extent_ha, 2) }} Ha</td>
                                                <td>
                                                    @forelse($lease->surveyNumbers as $sNo)
                                                        <span class="code-badge me-1 mb-1 d-inline-block">{{ $sNo->survey_no }}</span>
                                                    @empty
                                                        <span class="text-muted small">None attached</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @if($lease->status === 'approved')
                                                        <span class="status-blip-badge active"><span class="status-blip-dot"></span>Approved</span>
                                                    @elseif($lease->status === 'rejected')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                                    @else
                                                        <span class="status-blip-badge pending"><span class="status-blip-dot"></span>{{ ucfirst($lease->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-file-contract"></i></div>
                                                        <h6 class="fw-bold text-dark mb-1">No Lease Applications Found</h6>
                                                        <p class="text-muted small mb-0">This client does not have any active or historical lease applications logged.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 2: MINING PLANS                                       -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-mining" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Approved Mining Plans & Schedules</h6>
                                        <small class="text-muted">Stages 6.1–6.6 validation, Recognized Qualified Person (RQP), boundary coordinates</small>
                                    </div>
                                    <span class="badge bg-light text-muted border">{{ $customer->miningApplications->count() }} Plans</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>Plan No</th>
                                                <th>Plan Type</th>
                                                <th>Mineral</th>
                                                <th>Current Stage</th>
                                                <th>RQP Details</th>
                                                <th>Boundary Pillars</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->miningApplications as $plan)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark font-monospace">{{ $plan->application_no }}</span>
                                                    <div class="text-muted small" style="font-size: .72rem;">Validity: {{ $plan->validity_years }} Years</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info border border-info">{{ $plan->planType->name ?? 'Standard Plan' }}</span>
                                                </td>
                                                <td>{{ $plan->mineral->name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-primary text-white px-2 py-1">Stage {{ $plan->stage }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $plan->rqp_name ?? 'Not Assigned' }}</div>
                                                    <small class="text-muted" style="font-size:.72rem;">{{ $plan->rqp_reg_no ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="code-badge">{{ $plan->boundaryPoints->count() }} Pillars</span>
                                                </td>
                                                <td>
                                                    @if($plan->status === 'approved')
                                                        <span class="status-blip-badge active"><span class="status-blip-dot"></span>Approved</span>
                                                    @else
                                                        <span class="status-blip-badge pending"><span class="status-blip-dot"></span>{{ ucfirst($plan->status) }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-mountain"></i></div>
                                                        <h6 class="fw-bold text-dark mb-1">No Mining Plans Registered</h6>
                                                        <p class="text-muted small mb-0">No mining plans or production schedules have been filed under this customer.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 3: ENVIRONMENT & EC CLEARANCES                        -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-environment" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Environmental Clearance & SEIAA Certificates</h6>
                                        <small class="text-muted">B1/B2 category projects, public hearing records, and certificate validity alerts</small>
                                    </div>
                                    <span class="badge bg-light text-muted border">{{ $customer->environmentProjects->count() }} Projects</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>Project Code</th>
                                                <th>Category</th>
                                                <th>Project Name</th>
                                                <th>SEIAA Certificate Ref</th>
                                                <th>Validity Window</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->environmentProjects as $env)
                                            <tr>
                                                <td><span class="font-monospace fw-bold text-dark">{{ $env->project_code }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $env->category === 'B1' ? 'danger' : 'success' }}-subtle text-{{ $env->category === 'B1' ? 'danger' : 'success' }} border border-{{ $env->category === 'B1' ? 'danger' : 'success' }}">
                                                        Category {{ $env->category }}
                                                    </span>
                                                </td>
                                                <td class="fw-semibold text-dark">{{ $env->project_name }}</td>
                                                <td>
                                                    @forelse($env->ecCertificates as $ec)
                                                        <span class="code-badge text-success">{{ $ec->ec_ref_no }}</span>
                                                    @empty
                                                        <span class="text-muted small">Pending SEIAA Grant</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @forelse($env->ecCertificates as $ec)
                                                        <div class="small">
                                                            {{ $ec->issue_date ? $ec->issue_date->format('d M Y') : '-' }} to 
                                                            <b class="text-dark">{{ $ec->expiry_date ? $ec->expiry_date->format('d M Y') : '-' }}</b>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info border border-info">{{ ucfirst($env->status) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-leaf"></i></div>
                                                        <h6 class="fw-bold text-dark mb-1">No Environmental Clearances</h6>
                                                        <p class="text-muted small mb-0">No EC projects or SEIAA certificates attached to this customer profile.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 4: PPT DEPARTMENT PRESENTATIONS                       -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-ppt" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">SEAC / SEIAA Presentation Agendas</h6>
                                        <small class="text-muted">Committee hearing schedules, agenda numbers, and meeting outcomes</small>
                                    </div>
                                    <span class="badge bg-light text-muted border">{{ $customer->pptApplications->count() }} Agendas</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>App No</th>
                                                <th>Project Name</th>
                                                <th>Committee</th>
                                                <th>Meeting & Item No</th>
                                                <th>Meeting Date</th>
                                                <th>Hearing Outcome</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->pptApplications as $ppt)
                                            <tr>
                                                <td><span class="font-monospace fw-bold text-dark">{{ $ppt->application_no }}</span></td>
                                                <td class="fw-semibold text-dark">{{ $ppt->project_name }}</td>
                                                <td>
                                                    @forelse($ppt->agendas as $agenda)
                                                        <span class="badge bg-secondary text-white">{{ $agenda->committee_type }}</span>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @forelse($ppt->agendas as $agenda)
                                                        <span class="code-badge">M-{{ $agenda->meeting_no }} / #{{ $agenda->item_no }}</span>
                                                    @empty
                                                        <span class="text-muted small">Not Scheduled</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @forelse($ppt->agendas as $agenda)
                                                        <span class="small">{{ $agenda->meeting_date ? $agenda->meeting_date->format('d M Y') : '-' }}</span>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @forelse($ppt->agendas as $agenda)
                                                        <span class="badge bg-{{ $agenda->outcome === 'Recommended' ? 'success' : 'warning' }}-subtle text-{{ $agenda->outcome === 'Recommended' ? 'success' : 'warning' }} border">
                                                            {{ $agenda->outcome ?? 'Awaiting MoM' }}
                                                        </span>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ ucfirst($ppt->status) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                                        <h6 class="fw-bold text-dark mb-1">No PPT Department Hearings</h6>
                                                        <p class="text-muted small mb-0">No committee presentation hearings are logged for this client.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 5: DGPS & DRONE SURVEYS                               -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-surveys" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Field & Drone Volumetric Surveys</h6>
                                        <small class="text-muted">Ground Control Points (GCP) and aerial volume extraction measurements</small>
                                    </div>
                                </div>

                                <!-- DGPS Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary text-white"><i class="fa fa-satellite-dish me-1"></i> DGPS Ground Surveys</span>
                                        <span class="text-muted small">({{ $customer->dgpsSurveys->count() }} surveys)</span>
                                    </div>
                                    <div class="table-responsive border rounded-3">
                                        <table class="table dossier-table">
                                            <thead>
                                                <tr>
                                                    <th>Survey No</th>
                                                    <th>Field Book</th>
                                                    <th>Lease Area</th>
                                                    <th>Surveyed Area</th>
                                                    <th>Discrepancy</th>
                                                    <th>Control Points</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customer->dgpsSurveys as $dgps)
                                                <tr>
                                                    <td><span class="font-monospace fw-bold text-primary">{{ $dgps->survey_no }}</span></td>
                                                    <td>{{ $dgps->field_book_no ?? '-' }}</td>
                                                    <td>{{ number_format($dgps->lease_area_ha, 2) }} Ha</td>
                                                    <td class="fw-bold text-success">{{ number_format($dgps->surveyed_area_ha, 2) }} Ha</td>
                                                    <td>
                                                        <span class="badge bg-{{ $dgps->area_discrepancy_ha > 0 ? 'danger' : 'light' }} text-{{ $dgps->area_discrepancy_ha > 0 ? 'white' : 'dark' }} border">
                                                            {{ number_format($dgps->area_discrepancy_ha, 2) }} Ha
                                                        </span>
                                                    </td>
                                                    <td><span class="code-badge">{{ $dgps->points->count() }} GCPs</span></td>
                                                    <td><span class="status-blip-badge active"><span class="status-blip-dot"></span>{{ ucfirst($dgps->survey_status) }}</span></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-3 text-muted small">No DGPS ground surveys logged.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Drone Section -->
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-info text-white"><i class="fa fa-paper-plane me-1"></i> Drone Aerial Surveys</span>
                                        <span class="text-muted small">({{ $customer->droneSurveys->count() }} flights)</span>
                                    </div>
                                    <div class="table-responsive border rounded-3">
                                        <table class="table dossier-table">
                                            <thead>
                                                <tr>
                                                    <th>Survey No</th>
                                                    <th>Flight Date</th>
                                                    <th>Pilot & Drone UIN</th>
                                                    <th>Altitude / GSD</th>
                                                    <th>Extracted Volume</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customer->droneSurveys as $drone)
                                                <tr>
                                                    <td><span class="font-monospace fw-bold text-info">{{ $drone->survey_no }}</span></td>
                                                    <td>{{ $drone->flight_date ? $drone->flight_date->format('d M Y') : '-' }}</td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $drone->drone_pilot_name ?? 'Unassigned' }}</div>
                                                        <small class="text-muted" style="font-size: .72rem;">{{ $drone->drone_uin_no ?? '-' }}</small>
                                                    </td>
                                                    <td>{{ $drone->altitude_meters ? $drone->altitude_meters . 'm' : '-' }} ({{ $drone->gsd_cm_px ? $drone->gsd_cm_px . ' cm' : '-' }})</td>
                                                    <td>
                                                        <span class="fw-bold text-primary fs-6 font-monospace">{{ number_format($drone->extracted_volume_cbm, 2) }}</span>
                                                        <span class="text-muted small">CBM</span>
                                                    </td>
                                                    <td><span class="status-blip-badge active"><span class="status-blip-dot"></span>{{ ucfirst($drone->survey_status) }}</span></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-3 text-muted small">No drone volumetric flights logged.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 6: MINERAL STOCKPILE & LOGISTICS                      -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-stockpile" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">Quarry Pithead Stockpiles & Seigniorage Tracking</h6>
                                        <small class="text-muted">Extracted mineral inventory, permitted annual quota tracking, and truck passes</small>
                                    </div>
                                </div>

                                @forelse($customer->stockpiles as $stockpile)
                                <div class="card border rounded-3 mb-3 p-3" style="background: #fafcff; border-color: #e2e8f0 !important;">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-3 border-end">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Mineral Site</span>
                                            <h5 class="fw-bold text-primary mb-0">{{ $stockpile->mineral->name ?? 'Gravel / Stone' }}</h5>
                                            <span class="code-badge mt-1 d-inline-block">Stockpile #{{ $stockpile->id }}</span>
                                        </div>
                                        <div class="col-md-3 border-end">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Pithead Stock</span>
                                            <h4 class="fw-bold text-success mb-0 font-monospace">{{ number_format($stockpile->current_stock_cbm, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h4>
                                        </div>
                                        <div class="col-md-3 border-end">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Permitted Annual Quota</span>
                                            <h5 class="fw-bold text-dark mb-0 font-monospace">{{ number_format($stockpile->annual_permitted_quota, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h5>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Total Dispatched</span>
                                            <h5 class="fw-bold text-info mb-0 font-monospace">{{ number_format($stockpile->total_dispatched_cbm, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive border rounded-3">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>Dispatch Date</th>
                                                <th>Vehicle Number</th>
                                                <th>Driver Name</th>
                                                <th>Destination</th>
                                                <th>Quantity (CBM)</th>
                                                <th>Seigniorage Fee</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($stockpile->dispatches as $dispatch)
                                            <tr>
                                                <td>{{ $dispatch->dispatch_date ? $dispatch->dispatch_date->format('d M Y H:i') : '-' }}</td>
                                                <td>
                                                    <span class="code-badge">{{ $dispatch->vehicle_number }}</span>
                                                    @if($dispatch->challan_no)
                                                        <div class="text-muted small" style="font-size:.72rem;">Challan: {{ $dispatch->challan_no }}</div>
                                                    @endif
                                                </td>
                                                <td>{{ $dispatch->driver_name ?? '-' }}</td>
                                                <td>{{ $dispatch->destination ?? '-' }}</td>
                                                <td class="fw-bold text-primary font-monospace">{{ number_format($dispatch->quantity, 2) }}</td>
                                                <td class="fw-bold text-success font-monospace">₹ {{ number_format($dispatch->seigniorage_fee_inr, 2) }}</td>
                                                <td><span class="status-blip-badge active"><span class="status-blip-dot"></span>{{ ucfirst($dispatch->status) }}</span></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-3 text-muted small">No mineral truck dispatches recorded yet.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @empty
                                <div class="empty-state-box">
                                    <div class="empty-state-icon"><i class="fa fa-truck-loading"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">No Stockpiles Configured</h6>
                                    <p class="text-muted small mb-0">This quarry operator does not have any active mineral stockpiles or dispatch logs.</p>
                                </div>
                                @endforelse
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
