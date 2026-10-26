@extends('layouts.app')
@section('title', 'Users Management')
@section('main_content')

<style>
/* ==========================================================================
   GTMS USER DIRECTORY & ACCESS CONTROL DESIGN TOKENS (UI/UX PRO MAX)
   ========================================================================== */
:root {
    --usr-surface: #ffffff;
    --usr-surface-subtle: #f8fafc;
    --usr-surface-elevated: #ffffff;
    --usr-border: #e2e8f0;
    --usr-border-subtle: #edf2f7;
    --usr-text-primary: #0f172a;
    --usr-text-secondary: #475569;
    --usr-text-muted: #94a3b8;
    --usr-accent: #0F1E4D;
    --usr-accent-hover: #1e3a8a;
    --usr-accent-light: #eff6ff;
    --usr-accent-subtle: rgba(15, 30, 77, 0.06);
    --usr-chip-bg: #f1f5f9;
    --usr-chip-border: #e2e8f0;
    --usr-card-shadow: 0 4px 18px -2px rgba(15, 30, 77, 0.05), 0 2px 6px -1px rgba(15, 30, 77, 0.03);
    --usr-table-header-bg: #f8fafc;
    --usr-row-hover: #f8fafc;
    --usr-input-bg: #ffffff;
    --usr-input-border: #cbd5e1;

    /* Status Accents */
    --usr-emerald: #10b981;
    --usr-emerald-light: #ecfdf5;
    --usr-amber: #f59e0b;
    --usr-amber-light: #fffbeb;
    --usr-rose: #ef4444;
    --usr-rose-light: #fef2f2;
    --usr-indigo: #6366f1;
    --usr-indigo-light: #eef2ff;
}

body[data-theme-version="dark"] {
    --usr-surface: #181818;
    --usr-surface-subtle: #202020;
    --usr-surface-elevated: #242424;
    --usr-border: #282828;
    --usr-border-subtle: #222222;
    --usr-text-primary: #f8fafc;
    --usr-text-secondary: #cbd5e1;
    --usr-text-muted: #64748b;
    --usr-accent: #3b82f6;
    --usr-accent-hover: #60a5fa;
    --usr-accent-light: rgba(59, 130, 246, 0.12);
    --usr-accent-subtle: rgba(59, 130, 246, 0.1);
    --usr-chip-bg: rgba(255, 255, 255, 0.05);
    --usr-chip-border: rgba(255, 255, 255, 0.08);
    --usr-card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
    --usr-table-header-bg: #1e1e1e;
    --usr-row-hover: rgba(59, 130, 246, 0.04);
    --usr-input-bg: #1e1e1e;
    --usr-input-border: #333333;

    /* Dark Status Accents */
    --usr-emerald-light: rgba(16, 185, 129, 0.15);
    --usr-amber-light: rgba(245, 158, 11, 0.15);
    --usr-rose-light: rgba(239, 68, 68, 0.15);
    --usr-indigo-light: rgba(99, 102, 241, 0.15);
}

/* ==========================================================================
   TOP BREADCRUMB & HEADER BAR
   ========================================================================== */
.usr-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.usr-breadcrumb-nav .breadcrumb {
    margin-bottom: 0.35rem;
    background: transparent;
    padding: 0;
    font-size: 0.8125rem;
}

.usr-breadcrumb-nav .breadcrumb-item a {
    color: var(--usr-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}

.usr-breadcrumb-nav .breadcrumb-item a:hover {
    color: var(--usr-accent);
}

.usr-breadcrumb-nav .breadcrumb-item.active {
    color: var(--usr-text-secondary);
    font-weight: 500;
}

.usr-breadcrumb-nav .breadcrumb-item + .breadcrumb-item::before {
    color: var(--usr-text-muted);
    content: "›";
    font-size: 1.1rem;
    line-height: 1;
    vertical-align: -1px;
}

.usr-page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--usr-text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
    letter-spacing: -0.02em;
}

.usr-page-title i {
    color: var(--usr-accent);
    font-size: 1.35rem;
}

.usr-page-subtitle {
    font-size: 0.875rem;
    color: var(--usr-text-muted);
    margin: 0.2rem 0 0 0;
}

/* Primary Action Button */
.usr-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff !important;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.65rem 1.35rem;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 14px rgba(15, 30, 77, 0.25);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    text-decoration: none;
}

body[data-theme-version="dark"] .usr-btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
}

.usr-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 30, 77, 0.35);
    color: #ffffff !important;
}

/* ==========================================================================
   BENTO KPI METRIC CARDS
   ========================================================================== */
.usr-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.75rem;
}

@media (max-width: 1200px) {
    .usr-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .usr-kpi-grid { grid-template-columns: 1fr; }
}

.usr-kpi-card {
    background: var(--usr-surface);
    border: 1px solid var(--usr-border);
    border-radius: 14px;
    padding: 1.25rem 1.4rem;
    box-shadow: var(--usr-card-shadow);
    display: flex;
    align-items: center;
    gap: 1.15rem;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.usr-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px -4px rgba(15, 30, 77, 0.08);
    border-color: rgba(15, 30, 77, 0.2);
}

body[data-theme-version="dark"] .usr-kpi-card:hover {
    box-shadow: 0 10px 26px -4px rgba(0, 0, 0, 0.6);
    border-color: rgba(59, 130, 246, 0.3);
}

.usr-kpi-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: transparent;
}

.usr-kpi-card.kpi-total::before { background: linear-gradient(90deg, #0F1E4D, #3b82f6); }
.usr-kpi-card.kpi-active::before { background: linear-gradient(90deg, #059669, #10b981); }
.usr-kpi-card.kpi-inactive::before { background: linear-gradient(90deg, #d97706, #f59e0b); }
.usr-kpi-card.kpi-roles::before { background: linear-gradient(90deg, #7c3aed, #a855f7); }

.usr-kpi-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}

.kpi-total .usr-kpi-icon-wrap {
    background: var(--usr-accent-light);
    color: var(--usr-accent);
}

.kpi-active .usr-kpi-icon-wrap {
    background: var(--usr-emerald-light);
    color: var(--usr-emerald);
}

.kpi-inactive .usr-kpi-icon-wrap {
    background: var(--usr-amber-light);
    color: var(--usr-amber);
}

.kpi-roles .usr-kpi-icon-wrap {
    background: var(--usr-indigo-light);
    color: var(--usr-indigo);
}

.usr-kpi-content {
    flex: 1;
    min-width: 0;
}

.usr-kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--usr-text-muted);
    margin-bottom: 0.2rem;
}

.usr-kpi-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--usr-text-primary);
    line-height: 1.15;
    letter-spacing: -0.02em;
}

.usr-kpi-meta {
    font-size: 0.75rem;
    color: var(--usr-text-secondary);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* ==========================================================================
   MAIN DASHBOARD PANEL & TOOLBAR
   ========================================================================== */
.usr-main-panel {
    background: var(--usr-surface);
    border: 1px solid var(--usr-border);
    border-radius: 16px;
    box-shadow: var(--usr-card-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
}

.usr-panel-header {
    padding: 1.35rem 1.5rem;
    border-bottom: 1px solid var(--usr-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--usr-surface);
}

.usr-panel-title-area h5 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--usr-text-primary);
    margin: 0;
}

.usr-panel-title-area p {
    font-size: 0.8125rem;
    color: var(--usr-text-muted);
    margin: 0.15rem 0 0 0;
}

/* Quick Filter Tabs */
.usr-quick-tabs {
    display: inline-flex;
    background: var(--usr-surface-subtle);
    padding: 4px;
    border-radius: 10px;
    border: 1px solid var(--usr-border);
    gap: 4px;
}

.usr-tab-pill {
    padding: 0.4rem 0.85rem;
    border-radius: 7px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--usr-text-secondary);
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.usr-tab-pill:hover {
    color: var(--usr-text-primary);
}

.usr-tab-pill.active {
    background: var(--usr-surface);
    color: var(--usr-accent);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

body[data-theme-version="dark"] .usr-tab-pill.active {
    background: var(--usr-surface-elevated);
    color: var(--usr-accent);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
}

.usr-tab-badge {
    padding: 0.1rem 0.45rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    background: var(--usr-chip-bg);
    border: 1px solid var(--usr-chip-border);
    color: var(--usr-text-muted);
}

.usr-tab-pill.active .usr-tab-badge {
    background: var(--usr-accent-light);
    border-color: transparent;
    color: var(--usr-accent);
}

/* Toolbar & Multi-Filter Grid */
.usr-toolbar {
    padding: 1rem 1.5rem;
    background: var(--usr-surface-subtle);
    border-bottom: 1px solid var(--usr-border);
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    justify-content: space-between;
}

.usr-toolbar-left {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
    flex: 1;
    min-width: 280px;
}

.usr-toolbar-right {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: center;
}

/* Search Input Box */
.usr-search-box {
    position: relative;
    min-width: 260px;
    flex: 1;
    max-width: 360px;
}

.usr-search-box i.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--usr-text-muted);
    font-size: 0.875rem;
    pointer-events: none;
}

.usr-search-input {
    width: 100%;
    height: 38px;
    padding: 0 2.25rem 0 2.25rem;
    background: var(--usr-input-bg);
    border: 1px solid var(--usr-input-border);
    border-radius: 9px;
    font-size: 0.8125rem;
    color: var(--usr-text-primary);
    transition: all 0.15s ease;
}

.usr-search-input:focus {
    outline: none;
    border-color: var(--usr-accent);
    box-shadow: 0 0 0 3px var(--usr-accent-subtle);
}

.usr-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--usr-text-muted);
    cursor: pointer;
    font-size: 0.85rem;
    display: none;
    padding: 2px 4px;
}

.usr-search-clear:hover {
    color: var(--usr-text-primary);
}

/* Filter Selects */
.usr-filter-select {
    height: 38px;
    padding: 0 2rem 0 0.85rem;
    background: var(--usr-input-bg);
    border: 1px solid var(--usr-input-border);
    border-radius: 9px;
    font-size: 0.8125rem;
    color: var(--usr-text-primary);
    cursor: pointer;
    transition: all 0.15s ease;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
}

.usr-filter-select:focus {
    outline: none;
    border-color: var(--usr-accent);
    box-shadow: 0 0 0 3px var(--usr-accent-subtle);
}

.usr-btn-reset {
    height: 38px;
    padding: 0 0.85rem;
    background: var(--usr-surface);
    border: 1px solid var(--usr-input-border);
    border-radius: 9px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--usr-text-secondary);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.usr-btn-reset:hover {
    background: var(--usr-surface-subtle);
    color: var(--usr-text-primary);
    border-color: var(--usr-text-muted);
}

/* ==========================================================================
   DATATABLES OVERRIDES & TABLE STYLING
   ========================================================================== */
.usr-table-wrap {
    padding: 0;
    overflow-x: auto;
}

#example10 {
    margin: 0 !important;
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

#example10 thead th {
    background: var(--usr-table-header-bg) !important;
    color: var(--usr-text-muted) !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 1rem 1.25rem !important;
    border-bottom: 1px solid var(--usr-border) !important;
    border-top: none !important;
    white-space: nowrap;
}

#example10 tbody td {
    padding: 1rem 1.25rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid var(--usr-border-subtle) !important;
    color: var(--usr-text-secondary) !important;
    font-size: 0.875rem !important;
    background: transparent !important;
}

#example10 tbody tr {
    transition: background-color 0.15s ease;
}

#example10 tbody tr:hover {
    background-color: var(--usr-row-hover) !important;
}

/* Avatar Squircle */
.usr-avatar-cell {
    display: flex;
    align-items: center;
    gap: 0.85rem;
}

.usr-avatar-squircle {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0F1E4D 0%, #2563eb 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.15);
    overflow: hidden;
    border: 2px solid var(--usr-surface);
}

.usr-avatar-squircle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.usr-info-name {
    font-weight: 700;
    color: var(--usr-text-primary);
    font-size: 0.9rem;
    line-height: 1.2;
    margin-bottom: 0.15rem;
}

.usr-info-sub {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.775rem;
    color: var(--usr-text-muted);
}

.usr-code-badge {
    background: var(--usr-chip-bg);
    border: 1px solid var(--usr-chip-border);
    color: var(--usr-text-secondary);
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 5px;
    font-family: monospace;
}

/* Role Badges */
.usr-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.3rem 0.65rem;
    border-radius: 7px;
    font-size: 0.775rem;
    font-weight: 600;
}

.usr-role-admin {
    background: var(--usr-rose-light);
    color: var(--usr-rose);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.usr-role-standard {
    background: var(--usr-indigo-light);
    color: var(--usr-indigo);
    border: 1px solid rgba(99, 102, 241, 0.2);
}

/* Branch Badge */
.usr-branch-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.6rem;
    border-radius: 6px;
    font-size: 0.775rem;
    font-weight: 500;
    background: var(--usr-chip-bg);
    border: 1px solid var(--usr-chip-border);
    color: var(--usr-text-secondary);
}

/* Contact Info */
.usr-contact-text {
    font-size: 0.8125rem;
    color: var(--usr-text-secondary);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

/* Glowing Status Badge */
.usr-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.775rem;
    font-weight: 600;
}

.usr-status-active {
    background: var(--usr-emerald-light);
    color: var(--usr-emerald);
    border: 1px solid rgba(16, 185, 129, 0.25);
}

.usr-status-inactive {
    background: var(--usr-amber-light);
    color: var(--usr-amber);
    border: 1px solid rgba(245, 158, 11, 0.25);
}

.usr-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

.usr-status-active .usr-status-dot {
    background: var(--usr-emerald);
    box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
}

.usr-status-inactive .usr-status-dot {
    background: var(--usr-amber);
}

/* Action Buttons Dock */
.usr-action-dock {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.4rem;
}

.usr-action-btn {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--usr-border);
    background: var(--usr-surface);
    color: var(--usr-text-secondary);
    font-size: 0.8125rem;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.usr-action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
}

.usr-action-btn.btn-edit:hover {
    background: var(--usr-accent-light);
    color: var(--usr-accent);
    border-color: var(--usr-accent);
}

.usr-action-btn.btn-delete:hover {
    background: var(--usr-rose-light);
    color: var(--usr-rose);
    border-color: var(--usr-rose);
}

/* Custom Footer & Pagination */
.usr-dt-footer {
    padding: 1.15rem 1.5rem;
    border-top: 1px solid var(--usr-border);
    background: var(--usr-surface);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    font-size: 0.8125rem;
    color: var(--usr-text-muted);
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
    padding: 0 0.55rem;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    border-radius: 8px !important;
    border: 1px solid var(--usr-border) !important;
    background: var(--usr-surface) !important;
    color: var(--usr-text-secondary) !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    margin: 0 !important;
}

.dataTables_paginate .paginate_button:hover {
    background: var(--usr-surface-subtle) !important;
    color: var(--usr-text-primary) !important;
    border-color: var(--usr-accent) !important;
}

.dataTables_paginate .paginate_button.current {
    background: var(--usr-accent) !important;
    color: #ffffff !important;
    border-color: var(--usr-accent) !important;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.25) !important;
}

body[data-theme-version="dark"] .dataTables_paginate .paginate_button.current {
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.4) !important;
}

.dataTables_paginate .paginate_button.disabled {
    opacity: 0.45 !important;
    cursor: not-allowed !important;
}

/* Hide default DataTables controls since we use custom toolbar */
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_length {
    display: none !important;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        <!-- ==================================================================
             EXECUTIVE COCKPIT HEADER & BREADCRUMBS
             ================================================================== -->
        <div class="usr-top-bar">
            <div class="usr-breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Authentication &amp; Settings</a></li>
                    <li class="breadcrumb-item active">Users Management</li>
                </ol>
                <h1 class="usr-page-title">
                    <i class="fa fa-users"></i> Users Management
                </h1>
                <p class="usr-page-subtitle">
                    Personnel database, role assignments, departmental allocations, and security credentials.
                </p>
            </div>
            <div>
                @can('users.create')
                <button type="button" class="usr-btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="fa fa-plus"></i>
                    <span>Add User</span>
                </button>
                @endcan
            </div>
        </div>

        <!-- ==================================================================
             BENTO KPI METRIC CARDS
             ================================================================== -->
        <div class="usr-kpi-grid">
            <!-- 1. Total Users (PHPUnit Assertion Target) -->
            <div class="usr-kpi-card kpi-total">
                <div class="usr-kpi-icon-wrap">
                    <i class="fa fa-users"></i>
                </div>
                <div class="usr-kpi-content">
                    <div class="usr-kpi-label">Total Users</div>
                    <div class="usr-kpi-value" id="kpi_total_users">{{ $users->count() }}</div>
                    <div class="usr-kpi-meta">
                        <i class="fa fa-id-badge text-primary"></i> Registered Personnel
                    </div>
                </div>
            </div>

            <!-- 2. Active Personnel -->
            <div class="usr-kpi-card kpi-active">
                <div class="usr-kpi-icon-wrap">
                    <i class="fa fa-user-check"></i>
                </div>
                <div class="usr-kpi-content">
                    <div class="usr-kpi-label">Active Personnel</div>
                    <div class="usr-kpi-value" id="kpi_active_users">{{ $users->where('status', 1)->count() }}</div>
                    <div class="usr-kpi-meta text-success">
                        <i class="fa fa-check-circle"></i> Operational &amp; Verified
                    </div>
                </div>
            </div>

            <!-- 3. Inactive Accounts -->
            <div class="usr-kpi-card kpi-inactive">
                <div class="usr-kpi-icon-wrap">
                    <i class="fa fa-user-clock"></i>
                </div>
                <div class="usr-kpi-content">
                    <div class="usr-kpi-label">Inactive / Suspended</div>
                    <div class="usr-kpi-value" id="kpi_inactive_users">{{ $users->where('status', '!=', 1)->count() }}</div>
                    <div class="usr-kpi-meta text-warning">
                        <i class="fa fa-ban"></i> Access Restricted
                    </div>
                </div>
            </div>

            <!-- 4. Roles & Branches Architecture -->
            <div class="usr-kpi-card kpi-roles">
                <div class="usr-kpi-icon-wrap">
                    <i class="fa fa-shield-alt"></i>
                </div>
                <div class="usr-kpi-content">
                    <div class="usr-kpi-label">Security Structure</div>
                    <div class="usr-kpi-value">{{ $role->count() }} <span style="font-size: 1rem; font-weight: 500; color: var(--usr-text-muted);">Roles</span></div>
                    <div class="usr-kpi-meta">
                        <i class="fa fa-building text-info"></i> {{ $branch->count() }} Department Offices
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================================================================
             MAIN USER DIRECTORY PANEL
             ================================================================== -->
        <div class="usr-main-panel">
            <!-- Panel Header & Quick Status Tabs -->
            <div class="usr-panel-header">
                <div class="usr-panel-title-area">
                    <h5>Personnel Directory</h5>
                    <p>Search, filter, and modify user credentials and permission tiers.</p>
                </div>
                <div class="usr-quick-tabs">
                    <button type="button" class="usr-tab-pill active" data-filter="all">
                        <span>All</span>
                        <span class="usr-tab-badge" id="tab_badge_all">{{ $users->count() }}</span>
                    </button>
                    <button type="button" class="usr-tab-pill" data-filter="active">
                        <span>Active</span>
                        <span class="usr-tab-badge" id="tab_badge_active">{{ $users->where('status', 1)->count() }}</span>
                    </button>
                    <button type="button" class="usr-tab-pill" data-filter="inactive">
                        <span>Inactive</span>
                        <span class="usr-tab-badge" id="tab_badge_inactive">{{ $users->where('status', '!=', 1)->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- Multi-Filter Toolbar -->
            <div class="usr-toolbar">
                <div class="usr-toolbar-left">
                    <!-- Instant Live Search -->
                    <div class="usr-search-box">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="user_table_search" class="usr-search-input" placeholder="Search by name, email, or code...">
                        <button type="button" id="user_search_clear" class="usr-search-clear" title="Clear search">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </div>

                    <!-- Role Dropdown Filter -->
                    <select id="filter_role" class="usr-filter-select">
                        <option value="">All Security Roles</option>
                        @foreach ($role as $r)
                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                        @endforeach
                    </select>

                    <!-- Branch / Department Filter -->
                    <select id="filter_branch" class="usr-filter-select">
                        <option value="">All Departments</option>
                        @foreach ($branch as $b)
                            <option value="{{ $b->branch_name }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Dropdown Filter -->
                    <select id="filter_status" class="usr-filter-select">
                        <option value="">All Statuses</option>
                        <option value="Active">Active Only</option>
                        <option value="Inactive">Inactive Only</option>
                    </select>

                    <!-- Reset Filters Button -->
                    <button type="button" id="btn_reset_filters" class="usr-btn-reset" title="Reset all filters">
                        <i class="fa fa-redo"></i>
                        <span>Reset</span>
                    </button>
                </div>

                <div class="usr-toolbar-right">
                    <!-- Rows Per Page -->
                    <select id="filter_page_length" class="usr-filter-select" style="min-width: 100px;">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>

            <!-- Table View -->
            <div class="usr-table-wrap">
                <table id="example10" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Personnel Identification</th>
                            <th>Assigned Role</th>
                            <th>Branch / Department</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th style="width: 90px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr id="user_row_{{ $user->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="usr-avatar-cell">
                                        <div class="usr-avatar-squircle">
                                            @if($user->image && file_exists(public_path('uploads/users/' . $user->image)))
                                                <img src="{{ asset('uploads/users/' . $user->image) }}" alt="{{ $user->name }}">
                                            @else
                                                <span>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="usr-info-name">{{ $user->name }}</div>
                                            <div class="usr-info-sub">
                                                <span class="usr-code-badge">{{ $user->user_code ?? 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $assignedRole = $user->roles->first() ? $user->roles->first()->name : ($user->role ? $user->role->name : 'No Role');
                                    @endphp
                                    @if(in_array($assignedRole, ['Admin', 'Super Admin']))
                                        <span class="usr-role-badge usr-role-admin">
                                            <i class="fa fa-shield-alt"></i> {{ $assignedRole }}
                                        </span>
                                    @else
                                        <span class="usr-role-badge usr-role-standard">
                                            <i class="fa fa-user-tag"></i> {{ $assignedRole }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="usr-branch-badge">
                                        <i class="fa fa-building"></i> {{ $user->branch ? $user->branch->branch_name : 'General' }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->mobile_num)
                                        <span class="usr-contact-text">
                                            <i class="fa fa-phone text-muted"></i> {{ $user->mobile_num }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status == 1)
                                        <span class="usr-status-pill usr-status-active">
                                            <span class="usr-status-dot"></span> Active
                                        </span>
                                    @else
                                        <span class="usr-status-pill usr-status-inactive">
                                            <span class="usr-status-dot"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="usr-action-dock">
                                        @php
                                            $targetIsAdmin = $user->hasRole('Super Admin') || $user->hasRole('Admin');
                                            $canManageTarget = ($isSuperAdmin ?? false) || !$targetIsAdmin;
                                        @endphp

                                        @can('users.edit')
                                        @if($canManageTarget)
                                        <button type="button" class="usr-action-btn btn-edit edituserBtn"
                                            title="Edit User"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-user-code="{{ $user->user_code }}"
                                            data-role="{{ $user->role_id }}"
                                            data-branch="{{ $user->branch_id }}"
                                            data-status="{{ $user->status }}"
                                            data-image="{{ $user->image }}"
                                            data-mobile="{{ $user->mobile_num }}"
                                            data-is-self="{{ $user->id === auth()->id() ? '1' : '0' }}">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>
                                        @else
                                        <span class="usr-action-btn opacity-50" title="Administrator account protected" style="cursor: not-allowed; background: var(--usr-surface-subtle);">
                                            <i class="fa fa-lock text-muted"></i>
                                        </span>
                                        @endif
                                        @endcan

                                        @can('users.delete')
                                        @if($user->id !== auth()->id() && !($user->hasRole('Admin') && $users->where('role_id', $user->role_id)->count() <= 1))
                                            @if($canManageTarget)
                                            <button type="button" class="usr-action-btn btn-delete deleteuserBtn"
                                                title="Delete User"
                                                data-id="{{ $user->id }}">
                                                <i class="fa fa-trash-alt"></i>
                                            </button>
                                            @endif
                                        @endif
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

@include('pages.authentication.users.createuser')
@endsection

@section('scripts')
<script src="{{ asset('js/ajax/user.js') }}"></script>
@endsection
