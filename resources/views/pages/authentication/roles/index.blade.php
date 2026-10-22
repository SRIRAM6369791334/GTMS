@extends('layouts.app')
@section('title', 'Roles & RBAC Permissions')
@section('main_content')

<style>
/* ==========================================================================
   GTMS ROLES & RBAC ENTERPRISE DESIGN TOKENS (UI/UX PRO MAX)
   ========================================================================== */
:root {
    --ro-surface: #ffffff;
    --ro-surface-subtle: #f8fafc;
    --ro-surface-elevated: #ffffff;
    --ro-border: #e2e8f0;
    --ro-border-subtle: #edf2f7;
    --ro-text-primary: #0f172a;
    --ro-text-secondary: #475569;
    --ro-text-muted: #94a3b8;
    --ro-accent: #0F1E4D;
    --ro-accent-hover: #1e3a8a;
    --ro-accent-light: #eff6ff;
    --ro-accent-subtle: rgba(15, 30, 77, 0.06);
    --ro-chip-bg: #f1f5f9;
    --ro-chip-border: #e2e8f0;
    --ro-card-shadow: 0 4px 18px -2px rgba(15, 30, 77, 0.05), 0 2px 6px -1px rgba(15, 30, 77, 0.03);
    --ro-table-header-bg: #f8fafc;
    --ro-row-hover: #f8fafc;
    --ro-input-bg: #ffffff;
    --ro-input-border: #cbd5e1;
}

body[data-theme-version="dark"] {
    --ro-surface: #181818;
    --ro-surface-subtle: #202020;
    --ro-surface-elevated: #242424;
    --ro-border: #282828;
    --ro-border-subtle: #222222;
    --ro-text-primary: #f8fafc;
    --ro-text-secondary: #cbd5e1;
    --ro-text-muted: #64748b;
    --ro-accent: #3b82f6;
    --ro-accent-hover: #60a5fa;
    --ro-accent-light: rgba(59, 130, 246, 0.12);
    --ro-accent-subtle: rgba(59, 130, 246, 0.1);
    --ro-chip-bg: rgba(255, 255, 255, 0.05);
    --ro-chip-border: rgba(255, 255, 255, 0.08);
    --ro-card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
    --ro-table-header-bg: #1e1e1e;
    --ro-row-hover: rgba(59, 130, 246, 0.04);
    --ro-input-bg: #1e1e1e;
    --ro-input-border: #333333;
}

/* ==========================================================================
   TOP BREADCRUMB & HEADER BAR
   ========================================================================== */
.ro-top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.ro-breadcrumb-nav .breadcrumb {
    margin-bottom: 0.35rem;
    background: transparent;
    padding: 0;
    font-size: 0.8125rem;
}

.ro-breadcrumb-nav .breadcrumb-item a {
    color: var(--ro-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}

.ro-breadcrumb-nav .breadcrumb-item a:hover {
    color: var(--ro-accent);
}

.ro-breadcrumb-nav .breadcrumb-item.active {
    color: var(--ro-text-secondary);
    font-weight: 500;
}

.ro-breadcrumb-nav .breadcrumb-item + .breadcrumb-item::before {
    color: var(--ro-text-muted);
    content: "›";
    font-size: 1.1rem;
    line-height: 1;
    vertical-align: -1px;
}

.ro-page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--ro-text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.625rem;
    letter-spacing: -0.02em;
}

.ro-page-title i {
    color: var(--ro-accent);
    font-size: 1.35rem;
}

.ro-page-subtitle {
    font-size: 0.85rem;
    color: var(--ro-text-secondary);
    margin-top: 0.2rem;
    margin-bottom: 0;
}

.btn-ro-primary {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 10px;
    padding: 0.625rem 1.25rem;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 14px rgba(15, 30, 77, 0.25);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

body[data-theme-version="dark"] .btn-ro-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.35);
}

.btn-ro-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 30, 77, 0.35);
    color: #ffffff !important;
}

/* ==========================================================================
   BENTO KPI METRIC CARDS
   ========================================================================== */
.ro-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.125rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 991px) {
    .ro-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 575px) {
    .ro-kpi-grid {
        grid-template-columns: 1fr;
    }
}

.ro-kpi-card {
    background: var(--ro-surface);
    border: 1px solid var(--ro-border);
    border-radius: 14px;
    padding: 1.15rem 1.25rem;
    box-shadow: var(--ro-card-shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    position: relative;
    overflow: hidden;
}

.ro-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.08);
    border-color: rgba(59, 130, 246, 0.3);
}

.ro-kpi-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.icon-blue { background: rgba(37, 99, 235, 0.12); color: #2563eb; }
.icon-emerald { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.icon-amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.icon-purple { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }

.ro-kpi-info {
    min-width: 0;
    flex: 1;
}

.ro-kpi-label {
    font-size: 0.775rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--ro-text-secondary);
    margin-bottom: 0.25rem;
}

.ro-kpi-value {
    font-size: 1.55rem;
    font-weight: 800;
    color: var(--ro-text-primary);
    line-height: 1.1;
    letter-spacing: -0.03em;
}

.ro-kpi-subtext {
    font-size: 0.75rem;
    color: var(--ro-text-muted);
    margin-top: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ==========================================================================
   MAIN DATA PANEL & TOOLBAR
   ========================================================================== */
.ro-main-card {
    background: var(--ro-surface);
    border: 1px solid var(--ro-border);
    border-radius: 16px;
    box-shadow: var(--ro-card-shadow);
    overflow: hidden;
}

.ro-panel-header {
    padding: 1.15rem 1.35rem;
    border-bottom: 1px solid var(--ro-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.875rem;
    background: var(--ro-surface);
}

.ro-panel-title-wrap {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.ro-panel-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ro-text-primary);
    margin: 0;
    letter-spacing: -0.01em;
}

.ro-panel-tag {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 20px;
    background: var(--ro-accent-subtle);
    color: var(--ro-accent);
    border: 1px solid rgba(59, 130, 246, 0.2);
}

/* Quick Filter Segmented Tabs */
.ro-quick-tabs {
    display: flex;
    align-items: center;
    background: var(--ro-surface-subtle);
    border: 1px solid var(--ro-border);
    border-radius: 10px;
    padding: 3px;
    gap: 3px;
}

.ro-tab-pill {
    padding: 0.35rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--ro-text-secondary);
    border: none;
    background: transparent;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.ro-tab-pill:hover {
    color: var(--ro-text-primary);
    background: rgba(255, 255, 255, 0.4);
}

body[data-theme-version="dark"] .ro-tab-pill:hover {
    background: rgba(255, 255, 255, 0.05);
}

.ro-tab-pill.active {
    background: var(--ro-accent);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

/* SaaS Toolbar Row */
.ro-table-toolbar {
    padding: 0.875rem 1.35rem;
    background: var(--ro-surface-subtle);
    border-bottom: 1px solid var(--ro-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.ro-toolbar-left {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex: 1;
    min-width: 280px;
    max-width: 420px;
}

.ro-search-wrap {
    position: relative;
    width: 100%;
}

.ro-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ro-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

.ro-search-input {
    width: 100%;
    padding: 0.45rem 2.2rem 0.45rem 2.25rem !important;
    font-size: 0.84rem !important;
    height: 38px !important;
    border-radius: 9px !important;
    border: 1px solid var(--ro-input-border) !important;
    background: var(--ro-input-bg) !important;
    color: var(--ro-text-primary) !important;
    transition: all 0.15s ease;
}

.ro-search-input:focus {
    border-color: var(--ro-accent) !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
}

.ro-search-clear {
    position: absolute;
    right: 0.7rem;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--ro-text-muted);
    font-size: 1.1rem;
    cursor: pointer;
    line-height: 1;
    padding: 0;
}

.ro-search-clear:hover {
    color: var(--ro-text-primary);
}

.ro-toolbar-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.ro-filter-select {
    height: 38px;
    padding: 0.4rem 2rem 0.4rem 0.75rem;
    font-size: 0.825rem;
    border-radius: 8px;
    border: 1px solid var(--ro-input-border);
    background-color: var(--ro-input-bg);
    color: var(--ro-text-primary);
    cursor: pointer;
    outline: none;
}

.ro-btn-reset {
    height: 38px;
    padding: 0 0.85rem;
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid var(--ro-border);
    background: var(--ro-surface);
    color: var(--ro-text-secondary);
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
    transition: all 0.15s ease;
}

.ro-btn-reset:hover {
    background: var(--ro-surface-subtle);
    color: var(--ro-text-primary);
    border-color: var(--ro-text-muted);
}

/* ==========================================================================
   IMMERSIVE DATA TABLE
   ========================================================================== */
.ro-table-wrap {
    padding: 0;
    margin: 0;
}

#example10 {
    margin-bottom: 0 !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    width: 100% !important;
}

#example10 thead th {
    background: var(--ro-table-header-bg);
    color: var(--ro-text-secondary);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.85rem 1.15rem;
    border-bottom: 1px solid var(--ro-border) !important;
    border-top: none !important;
    white-space: nowrap;
}

#example10 tbody td {
    padding: 1rem 1.15rem;
    font-size: 0.85rem;
    color: var(--ro-text-primary);
    border-bottom: 1px solid var(--ro-border);
    vertical-align: middle;
    background: var(--ro-surface);
    transition: background 0.12s ease;
}

#example10 tbody tr:hover td {
    background: var(--ro-row-hover);
}

#example10 tbody tr:last-child td {
    border-bottom: none;
}

/* Left accent hover indicator */
#example10 tbody tr {
    border-left: 3px solid transparent;
    transition: all 0.15s ease;
}

#example10 tbody tr:hover {
    border-left: 3px solid var(--ro-accent) !important;
}

/* S No Pill */
.role-idx-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: var(--ro-chip-bg);
    border: 1px solid var(--ro-chip-border);
    color: var(--ro-text-muted);
    font-size: 0.775rem;
    font-weight: 700;
}

/* Squircle Role Avatar & Identity */
.role-cell-wrap {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.role-avatar-squircle {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.2);
}

body[data-theme-version="dark"] .role-avatar-squircle {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
}

.role-avatar-squircle.avatar-core {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%);
}

.role-avatar-squircle.avatar-custom {
    background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
}

.role-meta-info {
    min-width: 0;
}

.role-name-text {
    font-size: 0.925rem;
    font-weight: 700;
    color: var(--ro-text-primary);
    line-height: 1.25;
    margin-bottom: 0.2rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.4rem;
}

.badge-system-core {
    background: rgba(16, 185, 129, 0.12);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
    font-size: 0.6875rem;
    font-weight: 700;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    letter-spacing: 0.02em;
    display: inline-flex;
    align-items: center;
}

.badge-custom-role {
    background: rgba(139, 92, 246, 0.12);
    color: #8b5cf6;
    border: 1px solid rgba(139, 92, 246, 0.3);
    font-size: 0.6875rem;
    font-weight: 600;
    padding: 0.2rem 0.5rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
}

.role-user-count-chip {
    font-size: 0.725rem;
    font-weight: 500;
    color: var(--ro-text-secondary);
    display: inline-flex;
    align-items: center;
    background: var(--ro-chip-bg);
    border: 1px solid var(--ro-chip-border);
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
}

/* Permission Matrix Cell */
.perm-cell-content {
    min-width: 260px;
    max-width: 460px;
}

.role-pct-pill {
    font-size: 0.7rem;
    font-weight: 700;
    background: var(--ro-chip-bg);
    border: 1px solid var(--ro-chip-border);
    color: var(--ro-text-secondary);
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
}

.role-module-chips-wrap {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.35rem;
    margin-top: 0.35rem;
}

.role-module-micro-chip {
    font-size: 0.6875rem;
    font-weight: 600;
    background: var(--ro-surface-subtle);
    border: 1px solid var(--ro-border);
    color: var(--ro-text-secondary);
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
    letter-spacing: 0.01em;
}

.role-module-more-chip {
    font-size: 0.6875rem;
    font-weight: 600;
    background: var(--ro-accent-subtle);
    border: 1px solid rgba(59, 130, 246, 0.25);
    color: var(--ro-accent);
    padding: 0.15rem 0.45rem;
    border-radius: 4px;
}

/* Unified Action Dock */
.role-action-dock {
    display: inline-flex;
    align-items: center;
    background: var(--ro-chip-bg);
    border: 1px solid var(--ro-chip-border);
    border-radius: 10px;
    padding: 3px;
    gap: 3px;
}

.dock-btn {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: none;
    background: transparent;
    color: var(--ro-text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
}

.dock-btn:hover {
    transform: scale(1.08);
}

.dock-btn.dock-edit:hover {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
}

.dock-btn.dock-delete:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
}

/* Footer & Pagination Harmonization */
.ro-dt-footer {
    padding: 0.875rem 1.35rem;
    background: var(--ro-surface);
    border-top: 1px solid var(--ro-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.dataTables_info {
    font-size: 0.8125rem !important;
    color: var(--ro-text-secondary) !important;
    padding-top: 0 !important;
}

.dataTables_paginate {
    padding-top: 0 !important;
}

.dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid var(--ro-border) !important;
    background: var(--ro-surface) !important;
    color: var(--ro-text-primary) !important;
    padding: 0.35rem 0.75rem !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    margin: 0 2px !important;
    transition: all 0.15s ease;
}

.dataTables_paginate .paginate_button.current,
.dataTables_paginate .paginate_button.current:hover {
    background: var(--ro-accent) !important;
    border-color: var(--ro-accent) !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

.dataTables_paginate .paginate_button:hover {
    background: var(--ro-surface-subtle) !important;
    border-color: var(--ro-text-muted) !important;
    color: var(--ro-text-primary) !important;
}

.dataTables_paginate .paginate_button.disabled,
.dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
    border-color: var(--ro-border) !important;
    background: var(--ro-surface) !important;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        <!-- Top Breadcrumbs & Header Suite -->
        <div class="ro-top-bar">
            <div class="ro-header-text">
                <nav class="ro-breadcrumb-nav" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fa fa-home me-1"></i> Home</a></li>
                        <li class="breadcrumb-item">Authentication & Settings</li>
                        <li class="breadcrumb-item active" aria-current="page">Roles & Permissions</li>
                    </ol>
                </nav>
                <h1 class="ro-page-title">
                    <i class="fa fa-shield-alt"></i> Security Roles & Access Control
                </h1>
                <p class="ro-page-subtitle">
                    Configure enterprise roles, system administrative rights, and granular module permissions across the GTMS statutory portal.
                </p>
            </div>
            <div class="ro-header-actions">
                @can('roles.create')
                <button type="button" class="btn btn-ro-primary" data-bs-toggle="modal" data-bs-target="#roleModal">
                    <i class="fa fa-plus-circle"></i> Add New Role
                </button>
                @endcan
            </div>
        </div>

        <!-- 4 Bento KPI Metric Cards -->
        <div class="ro-kpi-grid">
            <!-- 1. Total Roles -->
            <div class="ro-kpi-card">
                <div class="ro-kpi-icon-wrap icon-blue">
                    <i class="fa fa-shield-alt"></i>
                </div>
                <div class="ro-kpi-info">
                    <div class="ro-kpi-label">Configured Roles</div>
                    <div class="ro-kpi-value">{{ $totalRoles }}</div>
                    <div class="ro-kpi-subtext">Active security profiles</div>
                </div>
            </div>

            <!-- 2. System Core Roles -->
            <div class="ro-kpi-card">
                <div class="ro-kpi-icon-wrap icon-emerald">
                    <i class="fa fa-lock"></i>
                </div>
                <div class="ro-kpi-info">
                    <div class="ro-kpi-label">System Core Roles</div>
                    <div class="ro-kpi-value">{{ $systemRolesCount }}</div>
                    <div class="ro-kpi-subtext">Full system access (Admin)</div>
                </div>
            </div>

            <!-- 3. Custom Operational Roles -->
            <div class="ro-kpi-card">
                <div class="ro-kpi-icon-wrap icon-amber">
                    <i class="fa fa-user-tag"></i>
                </div>
                <div class="ro-kpi-info">
                    <div class="ro-kpi-label">Operational Roles</div>
                    <div class="ro-kpi-value">{{ $customRolesCount }}</div>
                    <div class="ro-kpi-subtext">Custom delegated scopes</div>
                </div>
            </div>

            <!-- 4. Permission Catalog -->
            <div class="ro-kpi-card">
                <div class="ro-kpi-icon-wrap icon-purple">
                    <i class="fa fa-key"></i>
                </div>
                <div class="ro-kpi-info">
                    <div class="ro-kpi-label">Permission Catalog</div>
                    <div class="ro-kpi-value">{{ $totalPermissions }}</div>
                    <div class="ro-kpi-subtext">18 statutory modules secured</div>
                </div>
            </div>
        </div>

        <!-- Main Roles Data Panel -->
        <div class="row">
            <div class="col-12">
                <div class="ro-main-card">
                    <!-- Panel Header with Segmented Quick Tabs -->
                    <div class="ro-panel-header">
                        <div class="ro-panel-title-wrap">
                            <h2 class="ro-panel-title">Role Matrix Directory</h2>
                            <span class="ro-panel-tag"><i class="fa fa-check-shield me-1"></i> RBAC Engine Active</span>
                        </div>
                        <div class="ro-quick-tabs">
                            <button type="button" class="ro-tab-pill active" data-filter="all">
                                All Roles ({{ $totalRoles }})
                            </button>
                            <button type="button" class="ro-tab-pill" data-filter="system">
                                • System Core ({{ $systemRolesCount }})
                            </button>
                            <button type="button" class="ro-tab-pill" data-filter="custom">
                                • Custom Roles ({{ $customRolesCount }})
                            </button>
                        </div>
                    </div>

                    <!-- Integrated SaaS Toolbar -->
                    <div class="ro-table-toolbar">
                        <div class="ro-toolbar-left">
                            <div class="ro-search-wrap">
                                <i class="fa fa-search ro-search-icon"></i>
                                <input type="text" id="role_table_search" class="ro-search-input" placeholder="Search role name, permissions, access tier...">
                                <button type="button" id="role_search_clear" class="ro-search-clear" title="Clear search" style="display: none;">&times;</button>
                            </div>
                        </div>
                        <div class="ro-toolbar-right">
                            <select id="filter_tier" class="ro-filter-select" title="Filter by Tier">
                                <option value="">All Role Tiers</option>
                                <option value="Super Admin">System Core (Admin)</option>
                                <option value="Custom Role">Custom Operational</option>
                            </select>

                            <select id="filter_coverage" class="ro-filter-select" title="Filter by Coverage">
                                <option value="">All Permission Coverage</option>
                                <option value="All Permissions (Super Admin)">Full Access (100%)</option>
                                <option value="Permissions">Custom Permission Scope</option>
                            </select>

                            <select id="role_page_length" class="ro-filter-select" title="Rows per page">
                                <option value="10" selected>10 per page</option>
                                <option value="25">25 per page</option>
                                <option value="50">50 per page</option>
                                <option value="100">100 per page</option>
                            </select>

                            <button type="button" id="role_reset_filters" class="ro-btn-reset" title="Reset all filters">
                                <i class="fa fa-rotate-left"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Table Responsive Wrapper -->
                    <div class="table-responsive ro-table-wrap">
                        <table id="example10" class="display table" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 50px; text-align: center;">S No</th>
                                    <th style="width: 32%;">Role</th>
                                    <th style="width: 48%;">Permissions Assigned</th>
                                    <th style="width: 12%; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    @php
                                        $isCore = in_array($role->name, ['Admin', 'Super Admin']);
                                        $permCount = $role->permissions->count();
                                        $pct = $totalPermissions > 0 ? round(($permCount / $totalPermissions) * 100) : 0;
                                        $permNames = $role->permissions->pluck('name');
                                        $roleModules = $permNames->map(fn($p) => explode('.', $p)[0])->unique()->take(3);
                                    @endphp
                                    <tr id="row{{ $role->id }}" data-tier="{{ $isCore ? 'system' : 'custom' }}">
                                        <!-- Column 0: Index -->
                                        <td class="text-center">
                                            <span class="role-idx-pill">{{ $loop->iteration }}</span>
                                        </td>

                                        <!-- Column 1: Role & Identity -->
                                        <td>
                                            <div class="role-cell-wrap">
                                                <div class="role-avatar-squircle {{ $isCore ? 'avatar-core' : 'avatar-custom' }}">
                                                    <i class="fa {{ $isCore ? 'fa-shield-alt' : 'fa-user-tag' }}"></i>
                                                </div>
                                                <div class="role-meta-info">
                                                    <div class="role-name-text">
                                                        <span>{{ $role->name }}</span>
                                                        @if($isCore)
                                                            <span class="badge badge-system-core ms-1"><i class="fa fa-shield me-1"></i> Super Admin</span>
                                                        @else
                                                            <span class="badge badge-custom-role ms-1"><i class="fa fa-user-gear me-1"></i> Custom Role</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <span class="role-user-count-chip">
                                                            <i class="fa fa-users me-1"></i> {{ $role->users_count }} Assigned
                                                        </span>
                                                        <span class="text-muted small">Guard: {{ $role->guard_name ?? 'web' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Column 2: Permissions Assigned -->
                                        <td>
                                            <div class="perm-cell-content">
                                                @if($isCore)
                                                    <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                        <i class="fa fa-infinity me-1"></i> All Permissions (Super Admin)
                                                    </span>
                                                    <div class="perm-progress-bar-wrap mt-2">
                                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                                            <span>System Access</span>
                                                            <span class="fw-bold text-success">100% Full Access</span>
                                                        </div>
                                                        <div class="progress" style="height: 5px; background: rgba(16, 185, 129, 0.15); border-radius: 4px;">
                                                            <div class="progress-bar bg-success" style="width: 100%; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1">
                                                            <i class="fa fa-lock me-1"></i> {{ $permCount }} Permissions
                                                        </span>
                                                        <span class="role-pct-pill">{{ $pct }}% Coverage</span>
                                                    </div>
                                                    <div class="perm-progress-bar-wrap mb-2">
                                                        <div class="progress" style="height: 5px; background: var(--ro-border-subtle); border-radius: 4px;">
                                                            <div class="progress-bar bg-primary" style="width: {{ $pct }}%; border-radius: 4px;"></div>
                                                        </div>
                                                    </div>
                                                    <div class="role-module-chips-wrap">
                                                        @foreach($roleModules as $m)
                                                            <span class="role-module-micro-chip">{{ $modules[$m] ?? ucfirst($m) }}</span>
                                                        @endforeach
                                                        @if($permNames->map(fn($p) => explode('.', $p)[0])->unique()->count() > 3)
                                                            <span class="role-module-more-chip">+{{ $permNames->map(fn($p) => explode('.', $p)[0])->unique()->count() - 3 }} more</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted d-block mt-1">
                                                        {{ Str::limit($role->permissions->pluck('name')->implode(', '), 60, '...') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- Column 3: Actions -->
                                        <td class="text-end">
                                            <div class="role-action-dock">
                                                @can('roles.edit')
                                                <button type="button" class="dock-btn dock-edit editBtn" 
                                                    data-id="{{ $role->id }}" 
                                                    data-name="{{ $role->name }}"
                                                    title="Edit Role & Permissions">
                                                    <i class="fa fa-pencil"></i>
                                                </button>
                                                @endcan

                                                @can('roles.delete')
                                                @if(!$isCore)
                                                    <button type="button" class="dock-btn dock-delete deleteBtn"
                                                        data-id="{{ $role->id }}"
                                                        data-name="{{ $role->name }}"
                                                        title="Delete Role">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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

    </div>
</div>

@include('pages.authentication.roles.createrole')

@endsection

@section('scripts')
<script src="{{ asset('js/ajax/role.js') }}"></script>
@endsection
