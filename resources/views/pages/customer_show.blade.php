@extends('layouts.app')
@section('title', ($customer->company_name ?? $customer->customer_name) . ' — Enterprise Dossier')
@section('main_content')

<style>
/* ============================================================
   GTMS CUSTOMER 360° ENTERPRISE DOSSIER — UI/UX PRO MAX DESIGN SYSTEM
   High contrast, seamless dark mode, ergonomic spacing, squircle avatars
   ============================================================ */
:root {
    --dos-bg: #f8fafc;
    --dos-surface: #ffffff;
    --dos-surface-subtle: #f8fafc;
    --dos-surface-hover: #f1f5f9;
    --dos-border: #e2e8f0;
    --dos-border-subtle: #f1f5f9;
    --dos-text-primary: #0f172a;
    --dos-text-secondary: #334155;
    --dos-text-muted: #64748b;
    --dos-text-subtle: #94a3b8;
    --dos-hover: #f8fbff;
    --dos-brand: #0F1E4D;
    --dos-brand-hover: #1B3A8C;
    --dos-accent: #2563eb;
    --dos-chip-bg: #f1f5f9;
    --dos-chip-text: #334155;
    --dos-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
    --dos-shadow-md: 0 4px 14px -2px rgba(15, 23, 42, 0.07);
    --dos-shadow-lg: 0 12px 28px -4px rgba(15, 23, 42, 0.1);
}

body[data-theme-version="dark"] {
    --dos-bg: #161717;
    --dos-surface: #202020;
    --dos-surface-subtle: #191a1d;
    --dos-surface-hover: #2a2d34;
    --dos-border: #2d3139;
    --dos-border-subtle: #24272f;
    --dos-text-primary: #f8fafc;
    --dos-text-secondary: #cbd5e1;
    --dos-text-muted: #94a3b8;
    --dos-text-subtle: #64748b;
    --dos-hover: #272a33;
    --dos-brand: #2563eb;
    --dos-brand-hover: #3b82f6;
    --dos-accent: #60a5fa;
    --dos-chip-bg: #262932;
    --dos-chip-text: #cbd5e1;
    --dos-shadow-sm: none;
    --dos-shadow-md: none;
    --dos-shadow-lg: 0 12px 30px rgba(0, 0, 0, 0.5);
}

.dossier-shell {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    color: var(--dos-text-secondary);
}

/* Header Action Buttons (Eliminating hot pink & invisible dark buttons) */
.btn-dos-primary {
    background: #0F1E4D !important;
    border: 1px solid #0F1E4D !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.82rem;
    height: 38px;
    border-radius: 9px;
    padding: 0 1.15rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
    text-decoration: none;
}
.btn-dos-primary:hover {
    background: #1B3A8C !important;
    border-color: #1B3A8C !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.3);
}
body[data-theme-version="dark"] .btn-dos-primary {
    background: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}
body[data-theme-version="dark"] .btn-dos-primary:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.45);
}

.btn-dos-ghost {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    color: var(--dos-text-secondary) !important;
    font-weight: 600;
    font-size: 0.82rem;
    height: 38px;
    border-radius: 9px;
    padding: 0 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.18s ease;
    white-space: nowrap;
    text-decoration: none;
}
.btn-dos-ghost:hover {
    background: var(--dos-hover) !important;
    border-color: #cbd5e1 !important;
    color: var(--dos-accent) !important;
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .btn-dos-ghost:hover {
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
}

.btn-dos-tracking {
    background: var(--dos-surface) !important;
    border: 1px solid #bfdbfe !important;
    color: #2563eb !important;
    font-weight: 600;
    font-size: 0.82rem;
    height: 38px;
    border-radius: 9px;
    padding: 0 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.18s ease;
    white-space: nowrap;
    text-decoration: none;
}
.btn-dos-tracking:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
}
body[data-theme-version="dark"] .btn-dos-tracking {
    background: rgba(59, 130, 246, 0.12) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
    color: #60a5fa !important;
}
body[data-theme-version="dark"] .btn-dos-tracking:hover {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
}

.btn-dos-edit {
    background: var(--dos-surface) !important;
    border: 1px solid #fde68a !important;
    color: #b45309 !important;
    font-weight: 600;
    font-size: 0.82rem;
    height: 38px;
    border-radius: 9px;
    padding: 0 1rem;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    transition: all 0.18s ease;
    white-space: nowrap;
    text-decoration: none;
}
.btn-dos-edit:hover {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #d97706 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
}
body[data-theme-version="dark"] .btn-dos-edit {
    background: rgba(245, 158, 11, 0.12) !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
    color: #fbbf24 !important;
}
body[data-theme-version="dark"] .btn-dos-edit:hover {
    background: #d97706 !important;
    color: #ffffff !important;
    border-color: #d97706 !important;
}

/* Top Breadcrumb Header */
.dossier-header-bar {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 16px;
    padding: 1.15rem 1.4rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--dos-shadow-sm);
}
.dossier-header-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--dos-text-primary) !important;
    letter-spacing: -0.025em;
    margin: 0;
}

/* Profile Sidebar Card */
.profile-sidebar-card {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 18px;
    box-shadow: var(--dos-shadow-sm);
    overflow: hidden;
}
.profile-header-banner {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 60%, #3b82f6 100%);
    height: 72px;
    position: relative;
}
body[data-theme-version="dark"] .profile-header-banner {
    background: linear-gradient(135deg, #1e293b 0%, #1e3a8a 60%, #2563eb 100%);
}
.profile-avatar-wrapper {
    position: relative;
    margin-top: -38px;
    margin-bottom: 12px;
    display: inline-block;
}
.profile-avatar-squircle {
    width: 74px;
    height: 74px;
    border-radius: 18px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.65rem;
    letter-spacing: 0.03em;
    border: 3.5px solid var(--dos-surface);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.25);
}
body[data-theme-version="dark"] .profile-avatar-squircle {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-color: #202020;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
}

.profile-entity-name {
    font-size: 1.08rem;
    font-weight: 800;
    color: var(--dos-text-primary) !important;
    margin-bottom: 3px;
    line-height: 1.3;
}
.profile-rep-name {
    font-size: 0.8rem;
    color: var(--dos-text-muted);
}

/* Pulsing Status Badges */
.status-blip-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.status-blip-badge.active {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}
body[data-theme-version="dark"] .status-blip-badge.active {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}
.status-blip-badge.pending {
    background: #fffbeb !important;
    color: #d97706 !important;
    border: 1px solid #fde68a !important;
}
body[data-theme-version="dark"] .status-blip-badge.pending {
    background: rgba(245, 158, 11, 0.15) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}
.status-blip-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    position: relative;
    display: inline-block;
}
.status-blip-dot.dot-active {
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: status-pulse-green 2s infinite;
}
.status-blip-dot.dot-pending {
    background: #f59e0b;
    box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
    animation: status-pulse-amber 2s infinite;
}

@keyframes status-pulse-green {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
@keyframes status-pulse-amber {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.6); }
    70% { transform: scale(1); box-shadow: 0 0 0 5px rgba(245, 158, 11, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

/* Info Chip Card & Badges */
.info-chip-card {
    background: var(--dos-surface-subtle) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 10px;
    padding: 0.65rem 0.85rem;
    margin-bottom: 0.5rem;
    transition: all 0.18s ease;
}
.info-chip-card:hover {
    border-color: #cbd5e1;
}
body[data-theme-version="dark"] .info-chip-card:hover {
    border-color: #3b82f6;
}

.dos-section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dos-text-muted);
    display: block;
    margin-bottom: 0.45rem;
}

.code-badge {
    font-family: 'SF Mono', Consolas, Monaco, monospace;
    font-size: 0.76rem;
    font-weight: 700;
    color: var(--dos-text-primary) !important;
    background: var(--dos-chip-bg) !important;
    border: 1px solid var(--dos-border) !important;
    padding: 2px 7px;
    border-radius: 5px;
    letter-spacing: 0.04em;
    white-space: nowrap;
}

/* KPI Summary Cards */
.kpi-metric-card {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 16px;
    padding: 1.15rem 1.25rem;
    box-shadow: var(--dos-shadow-sm);
    height: 100%;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}
.kpi-metric-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--dos-shadow-md);
    border-color: rgba(59, 130, 246, 0.3) !important;
}
.kpi-metric-label {
    font-size: 0.72rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--dos-text-muted);
    margin-bottom: 0.2rem;
}
.kpi-metric-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--dos-text-primary) !important;
    line-height: 1.1;
    letter-spacing: -0.02em;
}
.kpi-icon-box {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.kpi-icon-box.icon-leases {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}
body[data-theme-version="dark"] .kpi-icon-box.icon-leases {
    background: rgba(59, 130, 246, 0.16);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.3);
}

.kpi-icon-box.icon-mining {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
body[data-theme-version="dark"] .kpi-icon-box.icon-mining {
    background: rgba(16, 185, 129, 0.16);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
}

.kpi-icon-box.icon-ec {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}
body[data-theme-version="dark"] .kpi-icon-box.icon-ec {
    background: rgba(139, 92, 246, 0.16);
    color: #a78bfa;
    border-color: rgba(139, 92, 246, 0.3);
}

.kpi-icon-box.icon-stock {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
body[data-theme-version="dark"] .kpi-icon-box.icon-stock {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.3);
}

/* Module Navigation Segmented Control */
.segmented-tabs-wrapper {
    background: var(--dos-surface-subtle) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 12px;
    padding: 4px;
    display: flex;
    gap: 4px;
    overflow-x: auto;
    scrollbar-width: none;
}
.segmented-tabs-wrapper::-webkit-scrollbar { display: none; }

.segmented-tab-btn {
    border: none;
    background: transparent;
    padding: 7px 14px;
    border-radius: 8px;
    font-size: 0.81rem;
    font-weight: 600;
    color: var(--dos-text-muted);
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}
.segmented-tab-btn:hover {
    color: var(--dos-text-primary);
    background: var(--dos-hover);
}
.segmented-tab-btn.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}
body[data-theme-version="dark"] .segmented-tab-btn.active {
    background: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
}
.segmented-tab-btn .tab-count-pill {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 999px;
    background: var(--dos-chip-bg);
    color: var(--dos-text-secondary);
}
.segmented-tab-btn.active .tab-count-pill {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}

/* Modern Minimalist Tables */
.dossier-table-panel {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 18px;
    box-shadow: var(--dos-shadow-sm);
    overflow: hidden;
}
.dossier-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}
.dossier-table thead th {
    background: var(--dos-surface-subtle) !important;
    color: var(--dos-text-muted) !important;
    font-size: 0.72rem;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.05em;
    padding: 0.85rem 1rem;
    border-top: none;
    border-bottom: 1px solid var(--dos-border) !important;
    white-space: nowrap;
    vertical-align: middle;
}
.dossier-table tbody td {
    padding: 0.85rem 1rem;
    vertical-align: middle;
    border-bottom: 1px solid var(--dos-border-subtle) !important;
    font-size: 0.84rem;
    color: var(--dos-text-primary) !important;
    background: transparent !important;
    transition: background-color 0.15s ease;
}
.dossier-table tbody tr:hover td {
    background-color: var(--dos-hover) !important;
}
.dossier-table tbody tr:last-child td {
    border-bottom: none !important;
}

/* Clean Empty State */
.empty-state-box {
    text-align: center;
    padding: 3.5rem 1.5rem;
}
.empty-state-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: var(--dos-surface-subtle);
    border: 1px solid var(--dos-border);
    color: var(--dos-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}
.empty-state-title {
    font-weight: 700;
    color: var(--dos-text-primary);
    margin-bottom: 0.35rem;
    font-size: 1rem;
}

/* Extra Chip & Component Helpers */
.dos-chip-id {
    background: rgba(37, 99, 235, 0.08) !important;
    border: 1px solid rgba(37, 99, 235, 0.2) !important;
}
body[data-theme-version="dark"] .dos-chip-id {
    background: rgba(59, 130, 246, 0.12) !important;
    border-color: rgba(59, 130, 246, 0.28) !important;
}
.dos-chip-success {
    background: rgba(16, 185, 129, 0.08) !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
}
body[data-theme-version="dark"] .dos-chip-success {
    background: rgba(16, 185, 129, 0.12) !important;
    border-color: rgba(16, 185, 129, 0.28) !important;
}
.dos-chip-aadhaar {
    font-family: 'SF Mono', Consolas, Monaco, monospace;
    font-size: 0.78rem;
    font-weight: 700;
    color: #0284c7 !important;
    background: #e0f2fe !important;
    border: 1px solid #bae6fd !important;
    padding: 2px 7px;
    border-radius: 5px;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
body[data-theme-version="dark"] .dos-chip-aadhaar {
    color: #38bdf8 !important;
    background: rgba(14, 165, 233, 0.15) !important;
    border-color: rgba(14, 165, 233, 0.3) !important;
}
.profile-address-card {
    background: var(--dos-surface-subtle) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 10px;
    color: var(--dos-text-primary) !important;
    padding: 0.65rem 0.85rem;
    font-size: 0.82rem;
    line-height: 1.45;
}
.profile-footprint-box {
    background: var(--dos-surface-subtle) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 10px;
    padding: 0.6rem 0.4rem;
    text-align: center;
}
.profile-footprint-value {
    font-weight: 800;
    font-size: 1.05rem;
    color: var(--dos-text-primary) !important;
    line-height: 1.2;
}
.partner-contact-item {
    background: var(--dos-surface-subtle) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 10px;
    padding: 0.65rem 0.85rem;
    margin-bottom: 0.45rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-decoration: none;
    transition: all 0.18s ease;
}
.partner-contact-item:hover {
    border-color: #3b82f6 !important;
    background: var(--dos-hover) !important;
}
.partner-contact-name {
    font-weight: 700;
    font-size: 0.82rem;
    color: var(--dos-text-primary) !important;
}
.dossier-sub-card {
    background: var(--dos-surface) !important;
    border: 1px solid var(--dos-border) !important;
    border-radius: 14px;
    overflow: hidden;
}
.dossier-sub-card-header {
    background: var(--dos-surface-subtle) !important;
    border-bottom: 1px solid var(--dos-border) !important;
    padding: 0.6rem 0.85rem;
    color: var(--dos-text-primary) !important;
}
.dossier-nested-table-wrap {
    border: 1px solid var(--dos-border) !important;
    border-radius: 12px;
    overflow: hidden;
    background: var(--dos-surface) !important;
}
.btn-dos-action {
    background: var(--dos-surface-subtle);
    border: 1px solid var(--dos-border);
    color: var(--dos-text-primary);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.18s ease;
    text-decoration: none;
}
.btn-dos-action:hover {
    background: var(--dos-hover);
    color: #3b82f6;
    border-color: #3b82f6;
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
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h4 class="dossier-header-title mb-0">
                        {{ $customer->company_name ?? $customer->customer_name }}
                    </h4>
                    <span class="code-badge">CUST-{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</span>
                    @if($customer->mimas_no)
                        <span class="code-badge" style="color: #2563eb !important;" title="Customer Unique ID"><i class="fa fa-fingerprint me-1"></i>{{ $customer->mimas_no }}</span>
                    @endif
                    @if($customer->mimas_number)
                        <span class="code-badge" style="color: #059669 !important;" title="MIMAS Number"><i class="fa fa-id-badge me-1"></i>{{ $customer->mimas_number }}</span>
                    @endif
                    @if($customer->status == 1)
                        <span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>Active</span>
                    @else
                        <span class="status-blip-badge pending"><span class="status-blip-dot dot-pending"></span>Under Validation</span>
                    @endif
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @can('customer.edit')
                <a href="{{ route('customers.index') }}#edit-{{ $customer->id }}" class="btn btn-sm btn-dos-edit" title="Edit in Customer Directory">
                    <i class="fa fa-edit me-1"></i> Edit Profile
                </a>
                @endcan
                @can('application.create')
                <a href="{{ route('step1') }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-dos-primary shadow-sm">
                    <i class="fa fa-plus me-1"></i> New Lease Application
                </a>
                @endcan
                <a href="{{ route('customer-tracking.show', $customer->slug ?? $customer->id) }}" class="btn btn-sm btn-dos-tracking" title="Open in 4-Tab Customer Tracking Hub">
                    <i class="fa fa-chart-line me-1"></i> Tracking 360
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-sm btn-dos-ghost">
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
                    <div class="px-4 pb-3 text-center border-bottom" style="border-color: var(--dos-border) !important;">
                        <div class="profile-avatar-wrapper">
                            <div class="profile-avatar-squircle">
                                {{ strtoupper(substr($customer->company_name ?? $customer->customer_name, 0, 2)) }}
                            </div>
                        </div>
                        <h5 class="profile-entity-name mb-1">
                            {{ $customer->company_name ?? $customer->customer_name }}
                        </h5>
                        <div class="profile-rep-name d-flex align-items-center justify-content-center gap-1">
                            <i class="fa fa-user-tie text-secondary"></i>
                            <span>{{ $customer->customer_name }}</span>
                        </div>
                    </div>

                    <!-- Profile Metadata List -->
                    <div class="p-3">
                        
                        <!-- Legal & Tax Identifiers -->
                        <div class="mb-3">
                            <span class="dos-section-label">
                                Tax & Business Identifiers
                            </span>
                            <div class="info-chip-card dos-chip-id d-flex justify-content-between align-items-center mb-2">
                                <span class="text-primary fw-bold small"><i class="fa fa-fingerprint me-1"></i> Customer Unique ID</span>
                                <span class="badge bg-primary text-white fw-bold" style="letter-spacing:0.04em;">{{ $customer->mimas_no ?? 'N/A' }}</span>
                            </div>
                            @if($customer->mimas_number)
                            <div class="info-chip-card dos-chip-success d-flex justify-content-between align-items-center mb-2">
                                <span class="text-success fw-bold small"><i class="fa fa-id-badge me-1"></i> MIMAS Number</span>
                                <span class="badge bg-success text-white fw-bold" style="letter-spacing:0.04em;">{{ $customer->mimas_number }}</span>
                            </div>
                            @endif
                            @if($customer->mimas_status)
                            <div class="info-chip-card d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small"><i class="fa fa-info-circle me-1 text-primary"></i> MIMAS Status</span>
                                <span class="code-badge">{{ $customer->mimas_status }}</span>
                            </div>
                            @endif
                            <div class="info-chip-card d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">PAN Number</span>
                                <span class="code-badge">{{ $customer->pan ?? 'N/A' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small"><i class="fa fa-id-card me-1 text-info"></i> Aadhaar Number</span>
                                <span class="dos-chip-aadhaar">{{ $customer->aadhaar_no ?? 'N/A' }}</span>
                            </div>
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small">GSTIN Number</span>
                                <span class="code-badge" style="font-size:.74rem;">{{ $customer->gstin ?? 'Not Registered' }}</span>
                            </div>
                        </div>

                        <!-- Contact & Jurisdiction -->
                        <div class="mb-3">
                            <span class="dos-section-label">
                                Contact & Jurisdiction
                            </span>
                            <div class="info-chip-card d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small"><i class="fa fa-map-marker-alt me-1 text-danger"></i> District</span>
                                <span class="fw-bold small" style="color: var(--dos-text-primary);">{{ $customer->district?->name ?? 'Unassigned' }}</span>
                            </div>
                            <div class="info-chip-card mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small"><i class="fa fa-phone me-1 text-primary"></i> Primary Contact</span>
                                    <a href="tel:{{ $customer->mobile_num }}" class="fw-bold text-primary text-decoration-none small">
                                        {{ $customer->mobile_num }}
                                    </a>
                                </div>
                                <div class="text-muted text-end" style="font-size:.72rem;">
                                    {{ $customer->customer_name }}
                                </div>
                            </div>
                            @if($customer->secondary_mobile_num)
                            <div class="info-chip-card mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small"><i class="fa fa-phone-alt me-1 text-indigo"></i> Secondary Contact</span>
                                    <a href="tel:{{ $customer->secondary_mobile_num }}" class="fw-bold text-decoration-none small" style="color: var(--dos-text-primary);">
                                        {{ $customer->secondary_mobile_num }}
                                    </a>
                                </div>
                                @if($customer->secondary_contact_person)
                                <div class="text-muted text-end" style="font-size:.72rem;">
                                    {{ $customer->secondary_contact_person }}
                                </div>
                                @endif
                            </div>
                            @endif
                            <div class="info-chip-card d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fa fa-envelope me-1 text-info"></i> Email</span>
                                <span class="small text-truncate" style="max-width: 130px; color: var(--dos-text-primary);" title="{{ $customer->email }}">
                                    {{ $customer->email ?? 'No email' }}
                                </span>
                            </div>
                        </div>

                        <!-- Registered Office Address -->
                        <div class="mb-3">
                            <span class="dos-section-label">
                                Registered Business Address
                            </span>
                            <div class="profile-address-card">
                                <i class="fa fa-building me-1 text-secondary"></i>
                                {{ $customer->address ?? 'No physical office address registered.' }}
                            </div>
                        </div>

                        <!-- Footprint Summary -->
                        <div class="pt-2 border-top" style="border-color: var(--dos-border) !important;">
                            <div class="row g-2 text-center" style="font-size: .78rem;">
                                <div class="col-4">
                                    <div class="profile-footprint-box">
                                        <div class="profile-footprint-value">{{ $totalLeases }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Leases</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="profile-footprint-box">
                                        <div class="profile-footprint-value">{{ $totalMiningPlans }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Mining</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="profile-footprint-box">
                                        <div class="profile-footprint-value">{{ $customer->stockpiles->count() }}</div>
                                        <small class="text-muted" style="font-size:.68rem;">Stockpiles</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 text-center text-muted" style="font-size: .72rem;">
                            Profile Slug: <span class="font-monospace" style="color: var(--dos-text-primary);">{{ $customer->slug }}</span>
                        </div>
                    </div>
                </div>

                @if(!empty($companyPartners) && $companyPartners->isNotEmpty())
                <div class="dossier-sub-card mt-3 shadow-sm">
                    <div class="dossier-sub-card-header d-flex align-items-center justify-content-between">
                        <span class="fw-bold small" style="color: var(--dos-text-primary);"><i class="fa fa-users text-primary me-1"></i> Company Partners</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $companyPartners->count() }}</span>
                    </div>
                    <div class="p-3">
                        <small class="text-muted d-block mb-2" style="font-size: 0.72rem;">Other contacts under <strong>{{ $customer->company_name }}</strong>:</small>
                        <div>
                            @foreach($companyPartners as $partner)
                            <a href="{{ route('customers.show', $partner->slug ?? $partner->id) }}" class="partner-contact-item">
                                <div>
                                    <div class="partner-contact-name">{{ $partner->customer_name }}</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">
                                        <i class="fa fa-phone text-success me-1"></i>{{ $partner->mobile_num }}
                                        &bull; <span class="text-secondary">{{ $partner->mimas_no }}</span>
                                    </div>
                                </div>
                                <i class="fa fa-arrow-right text-muted" style="font-size: 0.75rem;"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
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
                                    <div class="kpi-metric-label">Total Leases</div>
                                    <h3 class="kpi-metric-value mb-0 mt-1">{{ $totalLeases }}</h3>
                                </div>
                                <div class="kpi-icon-box icon-leases">
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
                                    <div class="kpi-metric-label">Mining Plans</div>
                                    <h3 class="kpi-metric-value mb-0 mt-1">
                                        {{ $approvedMiningPlans }}
                                        <span class="text-muted fs-6 fw-normal">/ {{ $totalMiningPlans }}</span>
                                    </h3>
                                </div>
                                <div class="kpi-icon-box icon-mining">
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
                                    <div class="kpi-metric-label">Active ECs</div>
                                    <h3 class="kpi-metric-value mb-0 mt-1">{{ $activeEcCount }}</h3>
                                </div>
                                <div class="kpi-icon-box icon-ec">
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
                                    <div class="kpi-metric-label">Pithead Stock</div>
                                    <h3 class="kpi-metric-value mb-0 mt-1">
                                        {{ number_format($currentStockpileCbm, 0) }}
                                        <span class="fs-6 fw-normal text-muted">CBM</span>
                                    </h3>
                                </div>
                                <div class="kpi-icon-box icon-stock">
                                    <i class="fa fa-cubes"></i>
                                </div>
                            </div>
                            <small class="text-muted" style="font-size:.75rem;">Extracted Available Balance</small>
                        </div>
                    </div>
                </div>

                <!-- 6 MODULE SEGMENTED NAVIGATION TABS -->
                <div class="dossier-table-panel mb-4">
                    
                    <!-- Segmented Control Nav Header -->
                    <div class="p-3 border-bottom" style="background: var(--dos-surface); border-color: var(--dos-border) !important;">
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
                                <span class="tab-count-pill">{{ $customer->environmentProjects->count() + ($customer->ecCompliances ? $customer->ecCompliances->count() : 0) }}</span>
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
                    <div class="card-body p-0" style="background: var(--dos-surface);">
                        <div class="tab-content" id="customerDossierTabsContent">

                            <!-- ========================================================= -->
                            <!-- TAB 1: LEASE APPLICATIONS                                 -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade show active p-3" id="tab-leases" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">Quarry Lease Applications</h6>
                                        <small class="text-muted">Statutory leases under TN Minor Mineral Concession Rules with Survey Numbers</small>
                                    </div>
                                    <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">{{ $customer->leaseApplications->count() }} Records</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>App No &amp; Common ID</th>
                                                <th>Category</th>
                                                <th>Mineral</th>
                                                <th>Location</th>
                                                <th>Area (Ha)</th>
                                                <th>Survey &amp; Sub-div Nos</th>
                                                <th>Status</th>
                                                <th class="text-end" style="min-width: 140px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->leaseApplications as $lease)
                                             <tr>
                                                <td>
                                                    <span class="fw-bold text-primary font-monospace">{{ $lease->application_no }}</span>
                                                    <div class="mt-1">
                                                        <span class="code-badge font-monospace" style="font-size: .68rem;" title="Universal Common ID">
                                                            <i class="fa fa-fingerprint text-primary me-1"></i>{{ $lease->common_id ?? ('GTMS-' . date('Y', strtotime($lease->created_at ?? 'now')) . '-' . str_pad($lease->id, 4, '0', STR_PAD_LEFT)) }}
                                                        </span>
                                                    </div>
                                                    @if($lease->go_number)
                                                        <div class="text-muted small" style="font-size: .72rem;">GO: {{ $lease->go_number }}</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="code-badge">{{ $lease->category?->name ?? 'Standard Rule' }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $leaseMinerals = $lease->minerals && $lease->minerals->isNotEmpty()
                                                            ? $lease->minerals->pluck('name')->toArray()
                                                            : ($lease->mineral ? [$lease->mineral->name] : []);
                                                        if (!empty($lease->other_mineral_name)) {
                                                            $leaseMinerals[] = 'Other: ' . $lease->other_mineral_name;
                                                        }
                                                    @endphp
                                                    @if(!empty($leaseMinerals))
                                                        @foreach($leaseMinerals as $mName)
                                                            <span class="badge bg-primary-subtle text-primary border border-primary me-1 mb-1 d-inline-block">
                                                                <i class="fa fa-gem me-1"></i> {{ $mName }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td>{{ $lease->village ?? '-' }}, {{ $lease->taluk ?? '-' }}</td>
                                                <td class="fw-bold font-monospace" style="color: var(--dos-text-primary);">{{ number_format($lease->area_extent_ha, 2) }} Ha</td>
                                                <td>
                                                    @forelse($lease->surveyNumbers as $sNo)
                                                        <span class="code-badge me-1 mb-1 d-inline-block">{{ $sNo->survey_no }}</span>
                                                    @empty
                                                        <span class="text-muted small">None attached</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    @if($lease->status === 'approved')
                                                        <span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>Approved</span>
                                                    @elseif($lease->status === 'rejected')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                                    @else
                                                        <span class="status-blip-badge pending"><span class="status-blip-dot dot-pending"></span>{{ ucfirst($lease->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @php
                                                        $miningLinked = $lease->miningApplications ? $lease->miningApplications->first() : null;
                                                    @endphp
                                                    <div class="d-inline-flex gap-1 align-items-center justify-content-end">
                                                        <a href="{{ route('viewapplication', ['id' => $lease->id]) }}" class="btn-dos-action" title="View Lease Dossier">
                                                            <i class="fa fa-eye"></i> View
                                                        </a>
                                                        @if($miningLinked)
                                                            <a href="/process?id={{ $miningLinked->id }}" class="btn-dos-action" style="color: #059669;" title="Open Mining Plan ({{ $miningLinked->common_id }})">
                                                                <i class="fa fa-mountain me-1 text-success"></i> Plan ↗
                                                            </a>
                                                        @else
                                                            @can('application.edit')
                                                            <form action="{{ route('application.moveToMining', $lease->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-dos-primary py-1 px-2" style="font-size: 0.76rem;" title="Move to Mining Plan" onclick="return confirm('Promote Lease {{ $lease->application_no }} to Mining Plan domain under Common ID {{ $lease->common_id }}?');">
                                                                    <i class="fa fa-rocket me-1"></i> Move
                                                                </button>
                                                            </form>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-file-contract"></i></div>
                                                        <h6 class="empty-state-title">No Lease Applications Found</h6>
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
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">Approved Mining Plans & Schedules</h6>
                                        <small class="text-muted">Stages 6.1–6.6 validation, Recognized Qualified Person (RQP), boundary coordinates</small>
                                    </div>
                                    <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">{{ $customer->miningApplications->count() }} Plans</span>
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
                                                <th class="text-end" style="min-width: 120px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->miningApplications as $plan)
                                            <tr>
                                                <td>
                                                    <span class="fw-bold font-monospace" style="color: var(--dos-text-primary);">{{ $plan->application_no }}</span>
                                                    <div class="text-muted small" style="font-size: .72rem;">Validity: {{ $plan->validity_years }} Years</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info border border-info">{{ $plan->planType?->name ?? 'Standard Plan' }}</span>
                                                </td>
                                                <td>
                                                    @php
                                                        $planMinerals = $plan->minerals && $plan->minerals->isNotEmpty()
                                                            ? $plan->minerals->pluck('name')->toArray()
                                                            : ($plan->mineral ? [$plan->mineral->name] : []);
                                                    @endphp
                                                    @if(!empty($planMinerals))
                                                        @foreach($planMinerals as $mName)
                                                            <span class="badge bg-primary-subtle text-primary border border-primary me-1 mb-1 d-inline-block">
                                                                <i class="fa fa-gem me-1"></i> {{ $mName }}
                                                            </span>
                                                        @endforeach
                                                    @else
                                                        <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary text-white px-2 py-1">Stage {{ $plan->stage }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold" style="color: var(--dos-text-primary);">{{ $plan->rqp_name ?? 'Not Assigned' }}</div>
                                                    <small class="text-muted" style="font-size:.72rem;">{{ $plan->rqp_reg_no ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    <span class="code-badge">{{ $plan->boundaryPoints->count() }} Pillars</span>
                                                </td>
                                                <td>
                                                    @if($plan->status === 'approved')
                                                        <span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>Approved</span>
                                                    @else
                                                        <span class="status-blip-badge pending"><span class="status-blip-dot dot-pending"></span>{{ ucfirst($plan->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <a href="/process?id={{ $plan->id }}" class="btn-dos-action" title="Open Mining Plan Dossier">
                                                        <i class="fa fa-mountain me-1 text-primary"></i> Open Plan ↗
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-mountain"></i></div>
                                                        <h6 class="empty-state-title">No Mining Plans Registered</h6>
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
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">Environmental Clearance & SEIAA Certificates</h6>
                                        <small class="text-muted">B1/B2 category projects, public hearing records, and certificate validity alerts</small>
                                    </div>
                                    <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">{{ $customer->environmentProjects->count() }} Projects</span>
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
                                                <th class="text-end" style="min-width: 120px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->environmentProjects as $env)
                                            <tr>
                                                <td><span class="font-monospace fw-bold" style="color: var(--dos-text-primary);">{{ $env->project_code }}</span></td>
                                                <td>
                                                    <span class="badge bg-{{ $env->category === 'B1' ? 'danger' : 'success' }}-subtle text-{{ $env->category === 'B1' ? 'danger' : 'success' }} border border-{{ $env->category === 'B1' ? 'danger' : 'success' }}">
                                                        Category {{ $env->category }}
                                                    </span>
                                                </td>
                                                <td class="fw-semibold" style="color: var(--dos-text-primary);">{{ $env->project_name }}</td>
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
                                                            <b style="color: var(--dos-text-primary);">{{ $ec->expiry_date ? $ec->expiry_date->format('d M Y') : '-' }}</b>
                                                        </div>
                                                    @empty
                                                        <span class="text-muted small">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info border border-info">{{ ucfirst($env->status) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('environment-b2.show', $env->id) }}" class="btn-dos-action" title="View Environmental Project">
                                                        <i class="fa fa-eye me-1 text-primary"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-leaf"></i></div>
                                                        <h6 class="empty-state-title">No Environmental Clearances</h6>
                                                        <p class="text-muted small mb-0">No EC projects or SEIAA certificates attached to this customer profile.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- EC Half-Yearly Compliance Monitoring Section -->
                                <div class="mt-4 pt-3 border-top" style="border-color: var(--dos-border) !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-success text-white"><i class="fa fa-calendar-check me-1"></i> EC Half-Yearly Compliance</span>
                                                <span class="text-muted small">({{ $customer->ecCompliances ? $customer->ecCompliances->count() : 0 }} records)</span>
                                            </div>
                                            <small class="text-muted">MoEFCC / SEIAA half-yearly compliance filings, PARIVESH submissions &amp; monitoring</small>
                                        </div>
                                        @can('ec_compliance.create')
                                        <a href="{{ route('ec-compliance.step', 1) }}?customer_id={{ $customer->id }}" class="btn btn-sm btn-dos-action" style="color: #059669;">
                                            <i class="fa fa-plus me-1"></i> New Compliance
                                        </a>
                                        @endcan
                                    </div>

                                    <div class="table-responsive dossier-nested-table-wrap">
                                        <table class="table dossier-table">
                                            <thead>
                                                <tr>
                                                    <th>Compliance No</th>
                                                    <th>Period &amp; Year</th>
                                                    <th>Project Name</th>
                                                    <th>PARIVESH Ref</th>
                                                    <th>Submission / Due</th>
                                                    <th>Status</th>
                                                    <th class="text-end" style="min-width: 110px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customer->ecCompliances ?? [] as $comp)
                                                <tr>
                                                    <td>
                                                        <span class="font-monospace fw-bold text-success">{{ $comp->compliance_no }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="code-badge">{{ $comp->compliance_period }} {{ $comp->compliance_year }}</span>
                                                    </td>
                                                    <td class="fw-semibold" style="color: var(--dos-text-primary);">
                                                        {{ $comp->project_name ?? $comp->environmentProject?->project_name ?? '-' }}
                                                    </td>
                                                    <td>
                                                        @if($comp->parivesh_acknowledgement_no)
                                                            <span class="code-badge text-primary">{{ $comp->parivesh_acknowledgement_no }}</span>
                                                        @elseif($comp->parivesh_app_no)
                                                            <span class="code-badge">{{ $comp->parivesh_app_no }}</span>
                                                        @else
                                                            <span class="text-muted small">Pending Upload</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($comp->submission_date)
                                                            <span class="text-success small fw-semibold"><i class="fa fa-check-circle me-1"></i> {{ $comp->submission_date->format('d M Y') }}</span>
                                                        @elseif($comp->submission_due_date)
                                                            <span class="text-danger small fw-semibold"><i class="fa fa-clock me-1"></i> Due: {{ $comp->submission_due_date->format('d M Y') }}</span>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($comp->status === 'submitted' || $comp->status === 'approved')
                                                            <span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>{{ ucfirst($comp->status) }}</span>
                                                        @else
                                                            <span class="status-blip-badge pending"><span class="status-blip-dot dot-pending"></span>{{ ucfirst($comp->status) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('ec-compliance.show', $comp->id) }}" class="btn-dos-action" title="View Compliance Dossier">
                                                            <i class="fa fa-eye me-1 text-success"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-3 text-muted small">No EC half-yearly compliance reports logged for this customer.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 4: PPT DEPARTMENT PRESENTATIONS                       -->
                            <!-- ========================================================= -->
                            <div class="tab-pane fade p-3" id="tab-ppt" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">SEAC / SEIAA Presentation Agendas</h6>
                                        <small class="text-muted">Committee hearing schedules, agenda numbers, and meeting outcomes</small>
                                    </div>
                                    <span class="badge" style="background: var(--dos-chip-bg); color: var(--dos-text-muted); border: 1px solid var(--dos-border);">{{ $customer->pptApplications->count() }} Agendas</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table dossier-table">
                                        <thead>
                                            <tr>
                                                <th>App No</th>
                                                <th>Project Name</th>
                                                <th>Committee</th>
                                                <th>Meeting &amp; Item No</th>
                                                <th>Meeting Date</th>
                                                <th>Hearing Outcome</th>
                                                <th>Status</th>
                                                <th class="text-end" style="min-width: 120px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($customer->pptApplications as $ppt)
                                            <tr>
                                                <td><span class="font-monospace fw-bold" style="color: var(--dos-text-primary);">{{ $ppt->application_no }}</span></td>
                                                <td class="fw-semibold" style="color: var(--dos-text-primary);">{{ $ppt->project_name }}</td>
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
                                                    <span class="code-badge">{{ ucfirst($ppt->status) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <a href="{{ route('ppt-department.show', $ppt->id) }}" class="btn-dos-action" title="View PPT Hearing">
                                                        <i class="fa fa-eye me-1 text-primary"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8">
                                                    <div class="empty-state-box">
                                                        <div class="empty-state-icon"><i class="fa fa-chalkboard-teacher"></i></div>
                                                        <h6 class="empty-state-title">No PPT Department Hearings</h6>
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
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">Field &amp; Drone Volumetric Surveys</h6>
                                        <small class="text-muted">Ground Control Points (GCP) and aerial volume extraction measurements</small>
                                    </div>
                                </div>

                                <!-- DGPS Section -->
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-primary text-white"><i class="fa fa-satellite-dish me-1"></i> DGPS Ground Surveys</span>
                                        <span class="text-muted small">({{ $customer->dgpsSurveys->count() }} surveys)</span>
                                    </div>
                                    <div class="table-responsive dossier-nested-table-wrap">
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
                                                    <th class="text-end" style="min-width: 90px;">Action</th>
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
                                                    <td><span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>{{ ucfirst($dgps->survey_status) }}</span></td>
                                                    <td class="text-end">
                                                        <a href="{{ route('dgps-survey.show', $dgps->id) }}" class="btn-dos-action" title="View DGPS Survey">
                                                            <i class="fa fa-satellite-dish me-1 text-primary"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-3 text-muted small">No DGPS ground surveys logged.</td>
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
                                    <div class="table-responsive dossier-nested-table-wrap">
                                        <table class="table dossier-table">
                                            <thead>
                                                <tr>
                                                    <th>Survey No</th>
                                                    <th>Flight Date</th>
                                                    <th>Pilot &amp; Drone UIN</th>
                                                    <th>Altitude / GSD</th>
                                                    <th>Extracted Volume</th>
                                                    <th>Status</th>
                                                    <th class="text-end" style="min-width: 90px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($customer->droneSurveys as $drone)
                                                <tr>
                                                    <td><span class="font-monospace fw-bold text-info">{{ $drone->survey_no }}</span></td>
                                                    <td>{{ $drone->flight_date ? $drone->flight_date->format('d M Y') : '-' }}</td>
                                                    <td>
                                                        <div class="fw-semibold" style="color: var(--dos-text-primary);">{{ $drone->drone_pilot_name ?? 'Unassigned' }}</div>
                                                        <small class="text-muted" style="font-size: .72rem;">{{ $drone->drone_uin_no ?? '-' }}</small>
                                                    </td>
                                                    <td>{{ $drone->altitude_meters ? $drone->altitude_meters . 'm' : '-' }} ({{ $drone->gsd_cm_px ? $drone->gsd_cm_px . ' cm' : '-' }})</td>
                                                    <td>
                                                        <span class="fw-bold text-primary fs-6 font-monospace">{{ number_format($drone->extracted_volume_cbm, 2) }}</span>
                                                        <span class="text-muted small">CBM</span>
                                                    </td>
                                                    <td><span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>{{ ucfirst($drone->survey_status) }}</span></td>
                                                    <td class="text-end">
                                                        <a href="{{ route('drone-survey.show', $drone->id) }}" class="btn-dos-action" title="View Drone Flight">
                                                            <i class="fa fa-paper-plane me-1 text-info"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-3 text-muted small">No drone volumetric flights logged.</td>
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
                                        <h6 class="fw-bold mb-0" style="color: var(--dos-text-primary);">Quarry Pithead Stockpiles &amp; Seigniorage Tracking</h6>
                                        <small class="text-muted">Extracted mineral inventory, permitted annual quota tracking, and truck passes</small>
                                    </div>
                                </div>

                                @forelse($customer->stockpiles as $stockpile)
                                <div class="dossier-sub-card mb-3 p-3">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-3 border-end" style="border-color: var(--dos-border) !important;">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Mineral Site</span>
                                            <h5 class="fw-bold text-primary mb-0">{{ $stockpile->mineral?->name ?? 'Gravel / Stone' }}</h5>
                                            <span class="code-badge mt-1 d-inline-block">Stockpile #{{ $stockpile->id }}</span>
                                        </div>
                                        <div class="col-md-3 border-end" style="border-color: var(--dos-border) !important;">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Pithead Stock</span>
                                            <h4 class="fw-bold text-success mb-0 font-monospace">{{ number_format($stockpile->current_stock_cbm, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h4>
                                        </div>
                                        <div class="col-md-3 border-end" style="border-color: var(--dos-border) !important;">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Permitted Annual Quota</span>
                                            <h5 class="fw-bold mb-0 font-monospace" style="color: var(--dos-text-primary);">{{ number_format($stockpile->annual_permitted_quota, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h5>
                                        </div>
                                        <div class="col-md-3">
                                            <span class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: .68rem;">Total Dispatched</span>
                                            <h5 class="fw-bold text-info mb-0 font-monospace">{{ number_format($stockpile->total_dispatched_cbm, 2) }} <span class="fs-6 fw-normal text-muted">CBM</span></h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive dossier-nested-table-wrap">
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
                                                <td style="color: var(--dos-text-primary);">{{ $dispatch->driver_name ?? '-' }}</td>
                                                <td style="color: var(--dos-text-primary);">{{ $dispatch->destination ?? '-' }}</td>
                                                <td class="fw-bold text-primary font-monospace">{{ number_format($dispatch->quantity, 2) }}</td>
                                                <td class="fw-bold text-success font-monospace">₹ {{ number_format($dispatch->seigniorage_fee_inr, 2) }}</td>
                                                <td><span class="status-blip-badge active"><span class="status-blip-dot dot-active"></span>{{ ucfirst($dispatch->status) }}</span></td>
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
                                    <h6 class="empty-state-title">No Stockpiles Configured</h6>
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
