@extends('layouts.app')
@section('title', 'Departments & Regional Branches')
@section('main_content')

<style>
/* =========================================================================
   BRANCH & DEPARTMENT DIRECTORY — UI/UX PRO MAX THEME ENGINE
   ========================================================================= */
:root {
    --br-surface: #ffffff;
    --br-surface-subtle: #f8fafc;
    --br-surface-card: #ffffff;
    --br-border: #e2e8f0;
    --br-border-subtle: #f1f5f9;
    --br-text-primary: #0f172a;
    --br-text-muted: #64748b;
    --br-hover: #f8fafc;
    --br-accent: #2563eb;
    --br-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
    --br-shadow-md: 0 4px 14px -2px rgba(0, 0, 0, 0.07);
}

body[data-theme-version="dark"] {
    --br-surface: #1e1e1e;
    --br-surface-subtle: #242424;
    --br-surface-card: #1c1c1c;
    --br-border: #2e2e2e;
    --br-border-subtle: #272727;
    --br-text-primary: #f8fafc;
    --br-text-muted: #94a3b8;
    --br-hover: #262626;
    --br-accent: #3b82f6;
    --br-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --br-shadow-md: 0 4px 14px -2px rgba(0, 0, 0, 0.4);
}

/* Breadcrumb & Header Bar */
.branch-header-bar {
    background: var(--br-surface) !important;
    border: 1px solid var(--br-border) !important;
    border-radius: 16px;
    padding: 1.15rem 1.4rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--br-shadow-sm);
}

.branch-breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--br-text-muted);
    margin-bottom: 0.35rem;
}
.branch-breadcrumb-nav a {
    color: var(--br-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}
.branch-breadcrumb-nav a:hover {
    color: var(--br-accent);
}
.branch-breadcrumb-sep {
    font-size: 0.65rem;
    opacity: 0.5;
}

.branch-header-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--br-text-primary) !important;
    letter-spacing: -0.025em;
    margin: 0;
}
.branch-header-subtitle {
    font-size: 0.82rem;
    color: var(--br-text-muted);
    margin: 0.2rem 0 0 0;
}

/* Primary Action Button */
.btn-branch-primary {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%) !important;
    color: #ffffff !important;
    border: none !important;
    font-weight: 600;
    font-size: 0.84rem;
    height: 40px;
    border-radius: 10px;
    padding: 0 1.25rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.25);
    white-space: nowrap;
}
.btn-branch-primary:hover {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(37, 99, 235, 0.35);
}
body[data-theme-version="dark"] .btn-branch-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.3);
}

/* Top Bento KPI Metric Cards */
.branch-kpi-card {
    background: var(--br-surface) !important;
    border: 1px solid var(--br-border) !important;
    border-radius: 16px;
    padding: 1.15rem 1.25rem;
    box-shadow: var(--br-shadow-sm);
    height: 100%;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}
.branch-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--br-shadow-md);
    border-color: rgba(59, 130, 246, 0.3) !important;
}
.branch-kpi-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--br-text-muted);
    margin-bottom: 0.25rem;
}
.branch-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--br-text-primary) !important;
    line-height: 1.1;
    letter-spacing: -0.02em;
}
.branch-kpi-sub {
    font-size: 0.72rem;
    color: var(--br-text-muted);
    margin-top: 0.25rem;
}
.branch-kpi-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.branch-kpi-icon.icon-blue {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}
body[data-theme-version="dark"] .branch-kpi-icon.icon-blue {
    background: rgba(59, 130, 246, 0.16);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.3);
}

.branch-kpi-icon.icon-green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
body[data-theme-version="dark"] .branch-kpi-icon.icon-green {
    background: rgba(16, 185, 129, 0.16);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
}

.branch-kpi-icon.icon-amber {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
body[data-theme-version="dark"] .branch-kpi-icon.icon-amber {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.3);
}

.branch-kpi-icon.icon-purple {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}
body[data-theme-version="dark"] .branch-kpi-icon.icon-purple {
    background: rgba(139, 92, 246, 0.16);
    color: #a78bfa;
    border-color: rgba(139, 92, 246, 0.3);
}

/* Main Panel & Segmented Status Tabs */
.branch-panel {
    background: var(--br-surface) !important;
    border: 1px solid var(--br-border) !important;
    border-radius: 16px;
    box-shadow: var(--br-shadow-sm);
    overflow: hidden;
}
.branch-panel-header {
    background: var(--br-surface-subtle) !important;
    border-bottom: 1px solid var(--br-border) !important;
    padding: 1.1rem 1.4rem;
}

.branch-quick-tabs {
    display: inline-flex;
    align-items: center;
    background: rgba(0, 0, 0, 0.04);
    padding: 3px;
    border-radius: 10px;
    gap: 3px;
    border: 1px solid var(--br-border);
}
body[data-theme-version="dark"] .branch-quick-tabs {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.08);
}
.branch-tab-pill {
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 7px;
    border: none;
    background: transparent;
    color: var(--br-text-muted);
    cursor: pointer;
    transition: all 0.18s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.branch-tab-pill:hover {
    color: var(--br-text-primary);
}
.branch-tab-pill.active {
    background: var(--br-surface) !important;
    color: var(--br-accent) !important;
    font-weight: 700;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
body[data-theme-version="dark"] .branch-tab-pill.active {
    background: #2a2a2a !important;
    color: #60a5fa !important;
}
.branch-tab-count {
    font-size: 0.68rem;
    padding: 1px 6px;
    border-radius: 10px;
    background: rgba(0, 0, 0, 0.06);
}
body[data-theme-version="dark"] .branch-tab-count {
    background: rgba(255, 255, 255, 0.1);
}

/* Integrated SaaS Toolbar */
.branch-table-toolbar {
    padding: 1rem 1.4rem;
    background: var(--br-surface);
    border-bottom: 1px solid var(--br-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.branch-search-box {
    position: relative;
    min-width: 260px;
    flex: 1 1 260px;
    max-width: 380px;
}
.branch-search-box .search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--br-text-muted);
    font-size: 0.82rem;
    pointer-events: none;
}
.branch-search-input {
    width: 100%;
    height: 38px;
    padding-left: 34px;
    padding-right: 32px;
    border-radius: 9px;
    border: 1px solid var(--br-border);
    background: var(--br-surface-subtle);
    color: var(--br-text-primary);
    font-size: 0.82rem;
    transition: all 0.18s ease;
}
.branch-search-input:focus {
    border-color: var(--br-accent);
    background: var(--br-surface);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    outline: none;
}
body[data-theme-version="dark"] .branch-search-input {
    background: #171717;
    border-color: #333333;
}
body[data-theme-version="dark"] .branch-search-input:focus {
    background: #1f1f1f;
    border-color: #3b82f6;
}
.branch-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--br-text-muted);
    font-size: 0.75rem;
    cursor: pointer;
    display: none;
    padding: 3px;
}
.branch-search-clear:hover {
    color: var(--br-text-primary);
}

.branch-select-filter {
    height: 38px;
    border-radius: 9px;
    border: 1px solid var(--br-border);
    background: var(--br-surface-subtle);
    color: var(--br-text-primary);
    font-size: 0.82rem;
    font-weight: 500;
    padding: 0 0.85rem;
    cursor: pointer;
    min-width: 140px;
    transition: all 0.18s ease;
}
.branch-select-filter:focus {
    border-color: var(--br-accent);
    outline: none;
}
body[data-theme-version="dark"] .branch-select-filter {
    background: #171717;
    border-color: #333333;
    color: #f8fafc;
}

.btn-branch-reset {
    height: 38px;
    border-radius: 9px;
    border: 1px solid var(--br-border);
    background: var(--br-surface-subtle);
    color: var(--br-text-muted);
    font-size: 0.8rem;
    font-weight: 600;
    padding: 0 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.18s ease;
}
.btn-branch-reset:hover {
    background: var(--br-hover);
    color: var(--br-text-primary);
    border-color: #cbd5e1;
}
body[data-theme-version="dark"] .btn-branch-reset {
    background: #171717;
    border-color: #333333;
}

/* Disciplined Table Layout */
.branch-table-wrap {
    background: var(--br-surface);
}
.branch-table {
    margin-bottom: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
}
.branch-table thead th {
    background: var(--br-surface-subtle) !important;
    color: var(--br-text-muted) !important;
    font-size: 0.72rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 0.85rem 1rem;
    border-top: none;
    border-bottom: 1px solid var(--br-border) !important;
    white-space: nowrap;
    vertical-align: middle;
}
.branch-table tbody td {
    padding: 0.9rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--br-border-subtle) !important;
    font-size: 0.84rem;
    color: var(--br-text-primary) !important;
    background: transparent !important;
    transition: background-color 0.15s ease;
}
.branch-table tbody tr {
    border-left: 3px solid transparent;
    transition: all 0.16s ease;
}
.branch-table tbody tr:hover {
    border-left: 3px solid var(--br-accent) !important;
}
.branch-table tbody tr:hover td {
    background-color: var(--br-hover) !important;
}
.branch-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* Index Pill */
.branch-idx-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 7px;
    background: var(--br-surface-subtle);
    border: 1px solid var(--br-border);
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--br-text-muted);
}

/* Department Squircle Avatar */
.branch-avatar-squircle {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.92rem;
    letter-spacing: 0.03em;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    flex-shrink: 0;
}
body[data-theme-version="dark"] .branch-avatar-squircle {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}

.branch-name-text {
    font-weight: 700;
    color: var(--br-text-primary) !important;
    font-size: 0.9rem;
    line-height: 1.25;
}
.branch-city-pill {
    display: inline-flex;
    align-items: center;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 1px 7px;
    border-radius: 5px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563eb;
    border: 1px solid rgba(37, 99, 235, 0.2);
    margin-top: 3px;
    width: fit-content;
}
body[data-theme-version="dark"] .branch-city-pill {
    background: rgba(59, 130, 246, 0.14);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.28);
}

.branch-tel-link {
    font-weight: 600;
    color: var(--br-text-primary);
    text-decoration: none;
    font-size: 0.82rem;
    transition: color 0.15s ease;
    display: inline-flex;
    align-items: center;
}
.branch-tel-link:hover {
    color: var(--br-accent);
}

.branch-address-wrap {
    font-size: 0.82rem;
    line-height: 1.4;
}
.branch-addr-line {
    color: var(--br-text-primary);
    font-weight: 500;
}
.branch-loc-sub {
    font-size: 0.75rem;
    color: var(--br-text-muted);
    margin-top: 2px;
}

/* Status Blip Badge & Keyframe Halos */
.status-blip-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.76rem;
    font-weight: 700;
    white-space: nowrap;
}
/* Status Badge with Live Pulse Halo */
.branch-table .badge-success, .status-blip-badge.active {
    background: rgba(16, 185, 129, 0.1) !important;
    color: #059669 !important;
    border: 1px solid rgba(16, 185, 129, 0.25) !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    padding: 4px 10px 4px 8px !important;
    border-radius: 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    white-space: nowrap !important;
}
.branch-table .badge-success::before, .status-blip-dot.dot-active {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: branch-pulse-green 2s infinite;
}
body[data-theme-version="dark"] .branch-table .badge-success,
body[data-theme-version="dark"] .status-blip-badge.active {
    background: rgba(16, 185, 129, 0.16) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}

.branch-table .badge-danger, .status-blip-badge.inactive {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #dc2626 !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    padding: 4px 10px 4px 8px !important;
    border-radius: 12px !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    white-space: nowrap !important;
}
.branch-table .badge-danger::before, .status-blip-dot.dot-inactive {
    content: '';
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ef4444;
}
body[data-theme-version="dark"] .branch-table .badge-danger,
body[data-theme-version="dark"] .status-blip-badge.inactive {
    background: rgba(239, 68, 68, 0.16) !important;
    color: #f87171 !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
}

@keyframes branch-pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

/* Action Dock */
.branch-action-dock {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 3px 5px;
    border-radius: 8px;
    background: var(--br-surface-subtle);
    border: 1px solid var(--br-border);
}
.dock-btn {
    width: 29px;
    height: 29px;
    border-radius: 6px;
    border: none;
    background: transparent;
    color: var(--br-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    cursor: pointer;
    transition: all 0.16s ease;
    text-decoration: none;
}
.dock-btn.dock-edit:hover {
    background: #d97706;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
}
.dock-btn.dock-delete:hover {
    background: #dc2626;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
}

/* DataTables Footer & Pagination Harmonization */
.branch-dt-footer {
    padding: 0.95rem 1.4rem;
    background: var(--br-surface-subtle);
    border-top: 1px solid var(--br-border);
    font-size: 0.8rem;
    color: var(--br-text-muted);
}
.dataTables_paginate {
    display: flex;
    align-items: center;
    gap: 4px;
    margin: 0 !important;
}
.dataTables_paginate .paginate_button {
    min-width: 32px;
    height: 32px;
    padding: 0 6px !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    border-radius: 7px !important;
    border: 1px solid var(--br-border) !important;
    background: var(--br-surface) !important;
    color: var(--br-text-primary) !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    cursor: pointer;
    transition: all 0.15s ease;
    margin: 0 !important;
}
.dataTables_paginate .paginate_button:hover {
    background: var(--br-hover) !important;
    border-color: var(--br-accent) !important;
    color: var(--br-accent) !important;
}
.dataTables_paginate .paginate_button.current {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}
body[data-theme-version="dark"] .dataTables_paginate .paginate_button.current {
    background: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
}
.dataTables_paginate .paginate_button.disabled {
    opacity: 0.45;
    cursor: not-allowed;
    pointer-events: none;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        <!-- Top Breadcrumb & Action Header Bar -->
        <div class="branch-header-bar d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <nav class="branch-breadcrumb-nav">
                    <a href="{{ url('/') }}"><i class="fa fa-home me-1"></i> Home</a>
                    <span class="branch-breadcrumb-sep"><i class="fa fa-chevron-right"></i></span>
                    <a href="javascript:void(0)">Authentication & Settings</a>
                    <span class="branch-breadcrumb-sep"><i class="fa fa-chevron-right"></i></span>
                    <span class="text-primary fw-bold">Departments</span>
                </nav>
                <h1 class="branch-header-title">Department & Regional Branch Directory</h1>
                <p class="branch-header-subtitle">Central registry of mining administration jurisdictions, regional branch offices, and officer stations</p>
            </div>
            <div>
                @can('branch.create')
                <button type="button" class="btn btn-branch-primary" data-bs-toggle="modal" data-bs-target="#branchModal">
                    <i class="fa fa-plus"></i> Add Department
                </button>
                @endcan
            </div>
        </div>

        <!-- 4 Top Bento KPI Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="branch-kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="branch-kpi-label">Total Departments</div>
                        <div class="branch-kpi-value" id="kpi_total_branches">{{ $totalBranches }}</div>
                        <div class="branch-kpi-sub">Registered regional offices</div>
                    </div>
                    <div class="branch-kpi-icon icon-blue">
                        <i class="fa fa-building"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="branch-kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="branch-kpi-label">Active Offices</div>
                        <div class="branch-kpi-value text-success" id="kpi_active_branches">{{ $activeBranches }}</div>
                        <div class="branch-kpi-sub">Operational jurisdictions</div>
                    </div>
                    <div class="branch-kpi-icon icon-green">
                        <i class="fa fa-circle-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="branch-kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="branch-kpi-label">Inactive / Closed</div>
                        <div class="branch-kpi-value text-amber" id="kpi_inactive_branches">{{ $inactiveBranches }}</div>
                        <div class="branch-kpi-sub">Decommissioned stations</div>
                    </div>
                    <div class="branch-kpi-icon icon-amber">
                        <i class="fa fa-circle-pause"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="branch-kpi-card d-flex align-items-center justify-content-between">
                    <div>
                        <div class="branch-kpi-label">Assigned Personnel</div>
                        <div class="branch-kpi-value text-purple" id="kpi_assigned_users">{{ $assignedUsersCount }}</div>
                        <div class="branch-kpi-sub">Active staff & officers</div>
                    </div>
                    <div class="branch-kpi-icon icon-purple">
                        <i class="fa fa-user-shield"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Listing Panel -->
        <div class="branch-panel">
            <div class="branch-panel-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-16 fw-bold" style="color: var(--br-text-primary);">
                        <i class="fa fa-sitemap text-primary me-2"></i> Registered Departments
                    </span>
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2" style="font-size: 0.72rem;">Statewide</span>
                </div>

                <!-- Segmented Status Tabs -->
                <div class="branch-quick-tabs">
                    <button type="button" class="branch-tab-pill active" data-status="">
                        <span>All</span>
                        <span class="branch-tab-count">{{ $totalBranches }}</span>
                    </button>
                    <button type="button" class="branch-tab-pill" data-status="Active">
                        <span class="status-blip-dot dot-active"></span>
                        <span>Active</span>
                        <span class="branch-tab-count">{{ $activeBranches }}</span>
                    </button>
                    <button type="button" class="branch-tab-pill" data-status="Inactive">
                        <span class="status-blip-dot dot-inactive"></span>
                        <span>Inactive</span>
                        <span class="branch-tab-count">{{ $inactiveBranches }}</span>
                    </button>
                </div>
            </div>

            <!-- Integrated SaaS Toolbar -->
            <div class="branch-table-toolbar">
                <div class="branch-search-box">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" id="branch_table_search" class="branch-search-input" placeholder="Search by department name, contact person, or phone...">
                    <button type="button" id="branch_search_clear" class="branch-search-clear" title="Clear search"><i class="fa fa-times-circle"></i></button>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(isset($distinctCities) && count($distinctCities) > 0)
                    <select id="filter_city" class="branch-select-filter" style="max-width: 175px;">
                        <option value="">All Cities / Regions</option>
                        @foreach($distinctCities as $cityName)
                            <option value="{{ $cityName }}">{{ $cityName }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select id="filter_status" class="branch-select-filter d-none">
                        <option value="">All Statuses</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>

                    <select id="branch_page_length" class="branch-select-filter" style="width: auto; min-width: 105px;">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>

                    <button type="button" id="branch_reset_filters" class="btn-branch-reset" title="Reset all filters">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-responsive branch-table-wrap">
                <table id="example10" class="branch-table display" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 48px;">#</th>
                            <th style="width: 26%;">Department / Branch</th>
                            <th style="width: 18%;">Contact Person</th>
                            <th style="width: 15%;">Phone Number</th>
                            <th style="width: 22%;">Address & Jurisdiction</th>
                            <th class="text-center" style="width: 10%;">Status</th>
                            <th class="text-end pe-3" style="width: 9%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            @php
                                $initials = strtoupper(substr($branch->branch_name, 0, 2));
                                $cityText = $branch->city ? trim($branch->city) : '';
                                $stateText = $branch->state ? trim($branch->state) : '';
                                $pinText = $branch->pincode ? trim($branch->pincode) : '';
                                $locParts = array_filter([$cityText, $stateText, $pinText]);
                            @endphp
                            <tr id="row{{ $branch->id }}">
                                <td class="text-center">
                                    <span class="branch-idx-pill">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="branch-avatar-squircle">
                                            {{ $initials }}
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="branch-name-text">{{ $branch->branch_name }}</span>
                                            @if($branch->city)
                                                <div class="d-flex align-items-center gap-1 mt-1">
                                                    <span class="branch-city-pill">
                                                        <i class="fa fa-location-dot me-1"></i> {{ $branch->city }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($branch->contact_person)
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa fa-user-tie text-muted"></i>
                                            <span class="fw-semibold">{{ $branch->contact_person }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    @if($branch->mobile)
                                        <a href="tel:{{ $branch->mobile }}" class="branch-tel-link">
                                            <i class="fa fa-phone text-primary me-1"></i> {{ $branch->mobile }}
                                        </a>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="branch-address-wrap">
                                        <span class="branch-addr-line">{{ $branch->address ?: 'No address specified' }}</span>
                                        @if(count($locParts) > 0)
                                            <div class="branch-loc-sub">
                                                <i class="fa fa-map-pin text-primary me-1"></i> {{ implode(', ', $locParts) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if ($branch->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="branch-action-dock">
                                        @can('branch.edit')
                                        <button type="button" class="dock-btn dock-edit editbranchBtn" 
                                            data-bs-toggle="modal" data-bs-target="#brancheeditModal"
                                            data-id="{{ $branch->id }}" 
                                            data-name="{{ $branch->branch_name }}" 
                                            data-contact="{{ $branch->contact_person }}" 
                                            data-mobile="{{ $branch->mobile }}" 
                                            data-address="{{ $branch->address }}"
                                            data-city="{{ $branch->city }}" 
                                            data-state="{{ $branch->state }}"
                                            data-pincode="{{ $branch->pincode }}" 
                                            data-status="{{ $branch->status }}"
                                            title="Edit Department">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @endcan

                                        @can('branch.delete')
                                        <button type="button" class="dock-btn dock-delete deletebranchBtn"
                                            data-id="{{ $branch->id }}"
                                            title="Delete Department">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@include('pages.authentication.branch.creatbranch')

@endsection

@section('scripts')
<script src="{{ asset('js/ajax/branch.js') }}"></script>
@endsection
