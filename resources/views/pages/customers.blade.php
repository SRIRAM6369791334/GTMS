@extends('layouts.app')
@section('title', 'Customers Directory')
@section('main_content')
<style>
/* ==========================================================================
   GTMS CUSTOMERS DIRECTORY - UI/UX PRO MAX ENTERPRISE DESIGN SYSTEM
   High contrast, seamless dark mode, ergonomic spacing, modern typography
   ========================================================================== */
:root {
    --cust-surface: #ffffff;
    --cust-surface-subtle: #f8fafc;
    --cust-surface-hover: #f1f5f9;
    --cust-border: #e2e8f0;
    --cust-border-subtle: #f1f5f9;
    --cust-text-primary: #0f172a;
    --cust-text-secondary: #334155;
    --cust-text-muted: #64748b;
    --cust-text-subtle: #94a3b8;
    --cust-hover-bg: #f8fbff;
    --cust-brand: #0F1E4D;
    --cust-brand-hover: #1B3A8C;
    --cust-brand-tint: #eff6ff;
    --cust-chip-bg: #f1f5f9;
    --cust-chip-text: #334155;
    --cust-th-bg: #f8fafc;
    --cust-input-bg: #ffffff;
    --cust-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
    --cust-shadow-md: 0 4px 14px -2px rgba(15, 23, 42, 0.07);
    --cust-shadow-lg: 0 12px 28px -4px rgba(15, 23, 42, 0.1);
}

body[data-theme-version="dark"] {
    --cust-surface: #202020;
    --cust-surface-subtle: #161717;
    --cust-surface-hover: #2a2d34;
    --cust-border: #2d3139;
    --cust-border-subtle: #24272f;
    --cust-text-primary: #f8fafc;
    --cust-text-secondary: #cbd5e1;
    --cust-text-muted: #94a3b8;
    --cust-text-subtle: #64748b;
    --cust-hover-bg: #272a33;
    --cust-brand: #3b82f6;
    --cust-brand-hover: #60a5fa;
    --cust-brand-tint: rgba(59, 130, 246, 0.15);
    --cust-chip-bg: #262932;
    --cust-chip-text: #cbd5e1;
    --cust-th-bg: #191a1d;
    --cust-input-bg: #161717;
    --cust-shadow-sm: none;
    --cust-shadow-md: none;
    --cust-shadow-lg: 0 12px 30px rgba(0, 0, 0, 0.5);
}

body[data-theme-version="dark"] .cust-panel {
    background: #202020 !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] .cust-panel-head {
    background: #202020 !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] .cust-table-toolbar {
    background: #191a1d !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] #customersTable {
    background: #202020 !important;
}
body[data-theme-version="dark"] #customersTable thead th {
    background: #191a1d !important;
    color: #94a3b8 !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] #customersTable tbody tr {
    background: #202020 !important;
}
body[data-theme-version="dark"] #customersTable tbody td {
    background: #202020 !important;
    color: #f8fafc !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] #customersTable tbody tr:hover td {
    background: #272a33 !important;
}
body[data-theme-version="dark"] .cust-dt-footer {
    background: #191a1d !important;
    border-color: #2B2B2B !important;
}
body[data-theme-version="dark"] .cust-phone-primary a {
    color: #f8fafc !important;
}

/* Page Header & Breadcrumbs */
.cust-page-header {
    margin-bottom: 1.5rem;
}
.cust-breadcrumb {
    font-size: 0.8rem;
    font-weight: 500;
    margin-bottom: 0.35rem;
}
.cust-breadcrumb a {
    color: var(--cust-text-muted);
    text-decoration: none;
    transition: color 0.18s ease;
}
.cust-breadcrumb a:hover {
    color: var(--cust-brand);
}
.cust-breadcrumb .breadcrumb-item.active {
    color: var(--cust-text-primary);
    font-weight: 600;
}
.cust-page-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--cust-text-primary);
    letter-spacing: -0.025em;
    margin: 0;
}
.cust-page-subtitle {
    font-size: 0.82rem;
    color: var(--cust-text-muted);
    margin: 0.2rem 0 0 0;
}

/* Modern KPI Metric Cards */
.cust-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.15rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1200px) {
    .cust-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 576px) {
    .cust-kpi-grid {
        grid-template-columns: 1fr;
    }
}

.cust-kpi-card {
    background: var(--cust-surface);
    border: 1px solid var(--cust-border);
    border-radius: 14px;
    padding: 1.15rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.1rem;
    box-shadow: var(--cust-shadow-sm);
    transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.cust-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--cust-shadow-md);
    border-color: rgba(59, 130, 246, 0.3);
}

.cust-kpi-icon {
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
.cust-kpi-card:hover .cust-kpi-icon {
    transform: scale(1.05);
}

.cust-kpi-icon.icon-customers {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
}
.cust-kpi-icon.icon-active {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.cust-kpi-icon.icon-pending {
    background: #fffbeb;
    color: #d97706;
    border: 1px solid #fde68a;
}
.cust-kpi-icon.icon-leases {
    background: #f5f3ff;
    color: #7c3aed;
    border: 1px solid #ddd6fe;
}

body[data-theme-version="dark"] .cust-kpi-icon.icon-customers {
    background: rgba(59, 130, 246, 0.16);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.3);
}
body[data-theme-version="dark"] .cust-kpi-icon.icon-active {
    background: rgba(16, 185, 129, 0.16);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
}
body[data-theme-version="dark"] .cust-kpi-icon.icon-pending {
    background: rgba(245, 158, 11, 0.16);
    color: #fbbf24;
    border-color: rgba(245, 158, 11, 0.3);
}
body[data-theme-version="dark"] .cust-kpi-icon.icon-leases {
    background: rgba(139, 92, 246, 0.16);
    color: #a78bfa;
    border-color: rgba(139, 92, 246, 0.3);
}

.cust-kpi-label {
    font-size: 0.74rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--cust-text-muted);
    margin-bottom: 0.2rem;
}
.cust-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--cust-text-primary);
    line-height: 1.1;
    letter-spacing: -0.02em;
}

/* Main Panel Container */
.cust-panel {
    background: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    border-radius: 16px !important;
    box-shadow: var(--cust-shadow-sm) !important;
    overflow: hidden;
    margin-bottom: 2rem;
}
.cust-panel-head {
    padding: 1.15rem 1.4rem;
    border-bottom: 1px solid var(--cust-border);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    background: var(--cust-surface);
}
.cust-panel-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--cust-text-primary);
    margin: 0;
}
.cust-panel-subtitle {
    font-size: 0.8rem;
    color: var(--cust-text-muted);
    margin: 0.2rem 0 0 0;
}
.cust-badge-count {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #dbeafe;
    font-size: 0.74rem;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
}
body[data-theme-version="dark"] .cust-badge-count {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.3);
}

/* Integrated Toolbar & Filter Bar */
.cust-table-toolbar {
    padding: 0.85rem 1.4rem;
    border-bottom: 1px solid var(--cust-border);
    background: var(--cust-surface-subtle);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
}
.cust-toolbar-left {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.65rem;
    flex: 1 1 auto;
}
.cust-toolbar-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

/* Quick Status Tabs (Linear / Stripe Segmented Style) */
.cust-quick-tabs {
    display: inline-flex;
    align-items: center;
    background: var(--cust-surface);
    border: 1px solid var(--cust-border);
    border-radius: 9px;
    padding: 3px;
    gap: 3px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
body[data-theme-version="dark"] .cust-quick-tabs {
    background: #191a1d;
    border-color: #2B2B2B;
}
.cust-tab-pill {
    border: none;
    background: transparent;
    color: var(--cust-text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    padding: 5px 11px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.16s ease;
    white-space: nowrap;
    line-height: 1.2;
}
.cust-tab-pill:hover {
    color: var(--cust-text-primary);
    background: var(--cust-hover-bg);
}
.cust-tab-pill.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.28);
}
body[data-theme-version="dark"] .cust-tab-pill.active {
    background: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
}
.cust-tab-pill .tab-count {
    background: rgba(0, 0, 0, 0.07);
    color: inherit;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 9999px;
}
.cust-tab-pill.active .tab-count {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
}
.cust-tab-pill .tab-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.cust-tab-pill .tab-dot.dot-active {
    background: #10b981;
}
.cust-tab-pill .tab-dot.dot-pending {
    background: #f59e0b;
}

.cust-toolbar-divider {
    width: 1px;
    height: 24px;
    background: var(--cust-border);
    margin: 0 2px;
}

/* Search Box */
.cust-search-box {
    position: relative;
    width: 280px;
    min-width: 210px;
    max-width: 340px;
    flex: 0 1 280px;
}
.cust-search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--cust-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}
.cust-search-input {
    background-color: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-primary) !important;
    border-radius: 8px !important;
    padding: 0.45rem 2rem 0.45rem 2.25rem !important;
    font-size: 0.82rem !important;
    height: 38px;
    width: 100%;
    transition: all 0.18s ease;
}
.cust-search-input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    outline: none;
}
.cust-search-clear {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: var(--cust-text-muted);
    cursor: pointer;
    padding: 0;
    font-size: 0.85rem;
    line-height: 1;
}
.cust-search-clear:hover {
    color: #ef4444;
}

.cust-filter-select {
    background-color: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-primary) !important;
    border-radius: 8px !important;
    padding: 0.45rem 2rem 0.45rem 0.8rem !important;
    font-size: 0.82rem !important;
    font-weight: 500 !important;
    height: 38px;
    width: auto !important;
    min-width: 135px;
    max-width: 165px;
    flex: 0 0 auto !important;
    transition: all 0.18s ease;
}
.cust-filter-select:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    outline: none;
}

.cust-btn-reset {
    background-color: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-secondary) !important;
    border-radius: 8px !important;
    height: 38px;
    padding: 0 0.95rem;
    font-size: 0.82rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    flex: 0 0 auto !important;
    transition: all 0.18s ease;
    cursor: pointer;
    white-space: nowrap;
}
.cust-btn-reset:hover {
    background-color: var(--cust-hover-bg) !important;
    border-color: #cbd5e1 !important;
    color: var(--cust-brand) !important;
}

.cust-entries-label {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin: 0;
    font-size: 0.8rem;
    color: var(--cust-text-muted);
    font-weight: 500;
}
.cust-page-select {
    background-color: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-primary) !important;
    border-radius: 8px !important;
    padding: 0.35rem 1.75rem 0.35rem 0.65rem !important;
    font-size: 0.82rem !important;
    height: 38px;
    min-width: 72px;
    width: auto !important;
    flex: 0 0 auto !important;
    font-weight: 600;
}

.btn-cust-add {
    background: #0F1E4D !important;
    border: 1px solid #0F1E4D !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.84rem;
    height: 38px;
    border-radius: 8px;
    padding: 0 1.15rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    white-space: nowrap;
}
.btn-cust-add:hover {
    background: #1B3A8C !important;
    border-color: #1B3A8C !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.3);
}
body[data-theme-version="dark"] .btn-cust-add {
    background: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}
body[data-theme-version="dark"] .btn-cust-add:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.45);
}

/* Table Architecture & Seamless Container */
.cust-table-responsive {
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    width: 100% !important;
    overflow-x: auto;
    position: relative;
}
.cust-dt-table-wrap {
    width: 100%;
    overflow-x: auto;
    position: relative;
}
#customersTable {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    margin: 0 !important;
}
#customersTable thead th {
    background: var(--cust-th-bg) !important;
    color: var(--cust-text-muted) !important;
    font-size: 0.74rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    padding: 0.85rem 1rem !important;
    border-top: none !important;
    border-bottom: 1px solid var(--cust-border) !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
}
#customersTable tbody tr {
    transition: all 0.16s ease;
    border-left: 3px solid transparent;
}
#customersTable tbody tr:hover {
    border-left: 3px solid #3b82f6 !important;
}
#customersTable tbody td {
    padding: 0.85rem 1rem !important;
    border-bottom: 1px solid var(--cust-border-subtle) !important;
    vertical-align: middle !important;
    color: var(--cust-text-primary) !important;
    font-size: 0.84rem !important;
    background: transparent !important;
    transition: background-color 0.15s ease;
}
#customersTable tbody tr:last-child td {
    border-bottom: none !important;
}
#customersTable tbody tr:hover td {
    background: var(--cust-hover-bg) !important;
}

/* Table Column Width Utilities */
.col-th-num { width: 44px !important; max-width: 44px; text-align: center; }
.col-th-cust { width: 33% !important; min-width: 250px; }
.col-th-dist { width: 12% !important; min-width: 105px; }
.col-th-phone { width: 16% !important; min-width: 135px; }
.col-th-tax { width: 16% !important; min-width: 145px; }
.col-th-status { width: 11% !important; min-width: 95px; text-align: center; }
.col-th-action { width: 12% !important; min-width: 105px; text-align: right; }

/* Squircle Avatar (Clean 11px Curvature) */
.cust-avatar-squircle {
    width: 38px;
    height: 38px;
    border-radius: 11px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.82rem;
    flex-shrink: 0;
    letter-spacing: 0.03em;
    box-shadow: 0 2px 5px rgba(15, 30, 77, 0.18);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
body[data-theme-version="dark"] .cust-avatar-squircle {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    box-shadow: 0 2px 7px rgba(0, 0, 0, 0.45);
    border-color: rgba(255, 255, 255, 0.1);
}

.cust-name-link {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--cust-text-primary) !important;
    text-decoration: none;
    line-height: 1.3;
    transition: color 0.15s ease;
}
.cust-name-link:hover {
    color: var(--cust-brand) !important;
}
.cust-subinfo {
    font-size: 0.74rem;
    color: var(--cust-text-muted);
    margin-top: 3px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    line-height: 1.3;
}
.cust-meta-dot {
    color: var(--cust-text-subtle);
    font-size: 0.6rem;
    margin: 0 2px;
}
.cust-contact-person {
    color: var(--cust-text-muted);
    font-size: 0.74rem;
}
.cust-email-text {
    color: var(--cust-text-subtle);
    font-size: 0.73rem;
    max-width: 170px;
}

/* Badges & Chips */
.cust-badge-id {
    background: var(--cust-chip-bg) !important;
    color: var(--cust-chip-text) !important;
    border: 1px solid var(--cust-border) !important;
    font-family: monospace;
    font-weight: 600;
    font-size: 0.7rem;
    padding: 1px 5px;
    border-radius: 4px;
    white-space: nowrap;
}
.cust-badge-mimas {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #bfdbfe !important;
    font-weight: 600;
    font-size: 0.7rem;
    padding: 1px 6px;
    border-radius: 4px;
    white-space: nowrap;
}
body[data-theme-version="dark"] .cust-badge-mimas {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60a5fa !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
}

.cust-badge-mimas-number {
    background: #f0fdf4 !important;
    color: #16a34a !important;
    border: 1px solid #bbf7d0 !important;
    font-weight: 600;
    font-size: 0.7rem;
    padding: 1px 6px;
    border-radius: 4px;
    white-space: nowrap;
}
body[data-theme-version="dark"] .cust-badge-mimas-number {
    background: rgba(22, 163, 74, 0.15) !important;
    color: #4ade80 !important;
    border-color: rgba(22, 163, 74, 0.3) !important;
}

.cust-badge-contacts {
    background: #eff6ff !important;
    color: #2563eb !important;
    border: 1px solid #dbeafe !important;
    font-size: 0.68rem !important;
    font-weight: 600 !important;
    border-radius: 9999px !important;
    padding: 1px 7px !important;
}
body[data-theme-version="dark"] .cust-badge-contacts {
    background: rgba(59, 130, 246, 0.15) !important;
    color: #60a5fa !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
}

/* District Clean Pill */
.cust-loc-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.81rem;
    font-weight: 500;
    color: var(--cust-text-secondary);
    background: var(--cust-chip-bg);
    border: 1px solid var(--cust-border);
    padding: 2px 8px;
    border-radius: 6px;
    white-space: nowrap;
}

/* Phone Numbers */
.cust-phone-primary {
    font-size: 0.84rem;
    font-weight: 600;
    line-height: 1.3;
}
.cust-phone-link {
    color: var(--cust-text-primary) !important;
    text-decoration: none;
    transition: color 0.15s ease;
}
.cust-phone-link:hover {
    color: #2563eb !important;
}
body[data-theme-version="dark"] .cust-phone-link {
    color: #f8fafc !important;
}
.cust-phone-secondary {
    font-size: 0.73rem;
    color: var(--cust-text-muted);
    margin-top: 3px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.cust-badge-alt {
    background: var(--cust-chip-bg);
    color: var(--cust-chip-text);
    border: 1px solid var(--cust-border);
    font-size: 0.64rem;
    font-weight: 700;
    padding: 1px 4px;
    border-radius: 3px;
    text-transform: uppercase;
}

/* Tax & Statutory Badges */
.cust-tax-pill {
    display: inline-flex;
    align-items: center;
    font-size: 0.72rem;
    border-radius: 5px;
    overflow: hidden;
    white-space: nowrap;
    border: 1px solid var(--cust-border);
}
.cust-tax-pill .tax-tag {
    background: var(--cust-chip-bg);
    color: var(--cust-text-muted);
    font-weight: 700;
    font-size: 0.64rem;
    padding: 1px 5px;
    border-right: 1px solid var(--cust-border);
    letter-spacing: 0.04em;
}
.cust-tax-pill .tax-val {
    background: var(--cust-surface);
    color: var(--cust-text-secondary);
    font-family: 'SF Mono', Consolas, Monaco, monospace;
    font-weight: 600;
    padding: 1px 6px;
}
body[data-theme-version="dark"] .cust-tax-pill .tax-val {
    background: #191a1d;
    color: #cbd5e1;
}
.cust-pill-aadhaar {
    background: #f0f9ff !important;
    color: #0284c7 !important;
    border-color: #bae6fd !important;
    font-weight: 600;
    padding: 1px 7px;
}
body[data-theme-version="dark"] .cust-pill-aadhaar {
    background: rgba(14, 165, 233, 0.15) !important;
    color: #38bdf8 !important;
    border-color: rgba(14, 165, 233, 0.3) !important;
}
.cust-tax-empty {
    font-size: 0.75rem;
    color: var(--cust-text-subtle);
    display: inline-flex;
    align-items: center;
}

/* Status Pill with Pulsing Glow Dot */
.cust-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 9px;
    border-radius: 9999px;
    white-space: nowrap;
}
.status-active {
    background: #ecfdf5 !important;
    color: #059669 !important;
    border: 1px solid #a7f3d0 !important;
}
body[data-theme-version="dark"] .status-active {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
}
.status-pending {
    background: #fffbeb !important;
    color: #d97706 !important;
    border: 1px solid #fde68a !important;
}
body[data-theme-version="dark"] .status-pending {
    background: rgba(245, 158, 11, 0.15) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
}

.status-pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    position: relative;
    display: inline-block;
}
.status-pulse-dot.dot-active {
    background: #10b981;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: status-pulse-green 2s infinite;
}
.status-pulse-dot.dot-pending {
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

/* SLEEK ACTION DOCK (Enterprise Grouped Controls) */
.cust-action-dock {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    background: var(--cust-surface-subtle);
    border: 1px solid var(--cust-border);
    border-radius: 8px;
    padding: 3px;
    gap: 3px;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
}
body[data-theme-version="dark"] .cust-action-dock {
    background: #191a1d;
    border-color: #2B2B2B;
}
.dock-btn {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.76rem;
    color: var(--cust-text-secondary);
    background: transparent;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.16s ease;
    padding: 0;
}
body[data-theme-version="dark"] .dock-btn {
    color: #cbd5e1;
}
.dock-view:hover {
    background: #0F1E4D;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .dock-view:hover {
    background: #2563eb;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.45);
}
.dock-edit:hover {
    background: #d97706;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .dock-edit:hover {
    background: #d97706;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.45);
}
.dock-delete:hover {
    background: #dc2626;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .dock-delete:hover {
    background: #dc2626;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.45);
}

/* ==========================================================================
   DATATABLES CONTROLS & PAGINATION HARMONIZATION
   ========================================================================== */
.dataTables_wrapper {
    position: relative;
    padding: 0;
    clear: both;
}
/* Hide legacy DataTables length and filter since custom toolbar handles it */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    display: none !important;
}

/* Dedicated Bottom Pagination Bar */
.cust-dt-footer {
    padding: 0.65rem 1rem !important;
    border-top: 1px solid var(--cust-border) !important;
    background: var(--cust-surface-subtle) !important;
}
.cust-dt-footer .dataTables_info {
    padding: 0 !important;
    margin: 0 !important;
    color: var(--cust-text-muted) !important;
    font-size: 0.78rem !important;
    font-weight: 500 !important;
}
.cust-panel .dataTables_paginate,
.cust-dt-footer .dataTables_paginate {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    justify-content: flex-end !important;
}

/* Pagination Buttons */
.cust-panel .dataTables_wrapper .dataTables_paginate .paginate_button,
.cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button,
.cust-dt-footer .paginate_button {
    min-width: 28px !important;
    height: 28px !important;
    padding: 0 8px !important;
    margin: 0 1px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
    background: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-primary) !important;
    box-shadow: none !important;
    text-decoration: none !important;
}
.cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button.current,
.cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button.current:hover,
.cust-dt-footer .paginate_button.current,
.cust-dt-footer .paginate_button.current:hover {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 2px 5px rgba(15, 30, 77, 0.25) !important;
}
body[data-theme-version="dark"] .cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button.current,
body[data-theme-version="dark"] .cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button.current:hover,
body[data-theme-version="dark"] .cust-dt-footer .paginate_button.current,
body[data-theme-version="dark"] .cust-dt-footer .paginate_button.current:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
}
.cust-panel .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.disabled):hover,
.cust-panel .dataTables_wrapper .dataTables_paginate span .paginate_button:not(.current):hover,
.cust-dt-footer .paginate_button:not(.current):not(.disabled):hover {
    background: var(--cust-hover-bg) !important;
    border-color: var(--cust-border) !important;
    color: var(--cust-brand) !important;
    transform: translateY(-1px);
}
.cust-panel .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
.cust-panel .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
.cust-dt-footer .paginate_button.disabled {
    background: var(--cust-surface-subtle) !important;
    border-color: var(--cust-border-subtle) !important;
    color: var(--cust-text-subtle) !important;
    opacity: 0.55 !important;
    cursor: not-allowed !important;
    transform: none !important;
}

/* Modal Dark Mode & High Contrast */
.modal-content {
    background: var(--cust-surface) !important;
    border: 1px solid var(--cust-border) !important;
    border-radius: 16px !important;
    box-shadow: var(--cust-shadow-lg) !important;
    color: var(--cust-text-primary) !important;
}
.modal-header {
    border-bottom: 1px solid var(--cust-border) !important;
    padding: 1.15rem 1.4rem !important;
}
.modal-footer {
    border-top: 1px solid var(--cust-border) !important;
    padding: 1rem 1.4rem !important;
}
.modal-title {
    font-size: 1.05rem !important;
    font-weight: 700 !important;
    color: var(--cust-text-primary) !important;
}
.modal .form-label {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    color: var(--cust-text-primary) !important;
    margin-bottom: 0.35rem !important;
}
.modal .form-control,
.modal .form-select {
    background-color: var(--cust-surface-subtle) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-primary) !important;
    border-radius: 8px !important;
    font-size: 0.85rem !important;
    padding: 0.5rem 0.85rem !important;
    transition: all 0.18s ease !important;
}
.modal .form-control:focus,
.modal .form-select:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    outline: none !important;
}
.modal .form-control::placeholder {
    color: var(--cust-text-subtle) !important;
    opacity: 0.8;
}
.modal .input-group-text {
    background-color: var(--cust-surface-subtle) !important;
    border: 1px solid var(--cust-border) !important;
    color: var(--cust-text-muted) !important;
    border-radius: 8px 0 0 8px !important;
}
.modal .form-text {
    font-size: 0.72rem !important;
    color: var(--cust-text-muted) !important;
    margin-top: 0.3rem !important;
}
body[data-theme-version="dark"] .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
body[data-theme-version="dark"] .btn-danger.light {
    background: rgba(239, 68, 68, 0.15) !important;
    color: #f87171 !important;
    border: 1px solid rgba(239, 68, 68, 0.25) !important;
}
body[data-theme-version="dark"] .btn-danger.light:hover {
    background: #dc2626 !important;
    color: #ffffff !important;
}
body[data-theme-version="dark"] .btn-primary {
    background: #2563eb !important;
    border-color: #2563eb !important;
}
body[data-theme-version="dark"] .btn-primary:hover {
    background: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
}
</style>

    <div class="content-body default-height">
        <div class="container-fluid">
            <!-- PAGE HERO HEADER -->
            <div class="cust-page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <nav class="cust-breadcrumb" aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="/dashboard"><i class="fa fa-home me-1"></i>Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
                        </ol>
                    </nav>
                    <h4 class="cust-page-title">Customers Directory</h4>
                    <p class="cust-page-subtitle">Quarry leaseholders, corporate clients, and statutory profile registry</p>
                </div>
                <div>
                    @can('customer.create')
                    <button class="btn btn-cust-add" data-bs-toggle="modal" data-bs-target=".bd-customer-modal-lg">
                        <i class="fa fa-plus"></i>
                        <span>Add Customer</span>
                    </button>
                    @endcan
                </div>
            </div>
            
            <!-- MODERN KPI METRICS -->
            <div class="cust-kpi-grid">
                <div class="cust-kpi-card">
                    <div class="cust-kpi-icon icon-customers">
                        <i class="fa fa-users"></i>
                    </div>
                    <div>
                        <div class="cust-kpi-label">Total Customers</div>
                        <div class="cust-kpi-value">{{ $totalCustomers ?? 0 }}</div>
                    </div>
                </div>
                <div class="cust-kpi-card">
                    <div class="cust-kpi-icon icon-active">
                        <i class="fa fa-user-check"></i>
                    </div>
                    <div>
                        <div class="cust-kpi-label">Active Customers</div>
                        <div class="cust-kpi-value">{{ $activeCustomers ?? 0 }}</div>
                    </div>
                </div>
                <div class="cust-kpi-card">
                    <div class="cust-kpi-icon icon-pending">
                        <i class="fa fa-user-clock"></i>
                    </div>
                    <div>
                        <div class="cust-kpi-label">Inactive / Pending</div>
                        <div class="cust-kpi-value">{{ $pendingCustomers ?? 0 }}</div>
                    </div>
                </div>
                <div class="cust-kpi-card">
                    <div class="cust-kpi-icon icon-leases">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <div>
                        <div class="cust-kpi-label">Total Leases</div>
                        <div class="cust-kpi-value">{{ $totalLeases ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- CUSTOMER DIRECTORY TABLE PANEL -->
            <div class="cust-panel">
                <div class="cust-panel-head">
                    <div>
                        <div class="d-flex align-items-center gap-2.5">
                            <h5 class="cust-panel-title">Customer Registry</h5>
                            <span class="cust-badge-count">{{ $totalCustomers ?? count($customers) }} Records</span>
                        </div>
                        <p class="cust-panel-subtitle">Directory of quarry leaseholders, corporate clients, tax profiles, and state mining identifiers</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <!-- QUICK STATUS SEGMENTED TABS -->
                        <div class="cust-quick-tabs" role="tablist">
                            <button type="button" class="cust-tab-pill active" data-status="">
                                <span>All</span>
                                <span class="tab-count">{{ $totalCustomers ?? count($customers) }}</span>
                            </button>
                            <button type="button" class="cust-tab-pill" data-status="Active">
                                <span class="tab-dot dot-active"></span>
                                <span>Active</span>
                                <span class="tab-count">{{ $activeCustomers ?? 0 }}</span>
                            </button>
                            <button type="button" class="cust-tab-pill" data-status="Under Validation">
                                <span class="tab-dot dot-pending"></span>
                                <span>Under Validation</span>
                                <span class="tab-count">{{ $pendingCustomers ?? 0 }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- INTEGRATED TOOLBAR: SEARCH, FILTERS & LENGTH -->
                <div class="cust-table-toolbar">
                    <div class="cust-toolbar-left">
                        <div class="cust-search-box">
                            <i class="fa fa-search cust-search-icon"></i>
                            <input type="text" id="cust_table_search" class="form-control cust-search-input" placeholder="Search name, phone, PAN, MIMAS...">
                            <button type="button" id="cust_search_clear" class="cust-search-clear" style="display:none;" title="Clear search">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <select id="filter_district" class="form-select cust-filter-select shadow-none">
                            <option value="">All Districts</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->name }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <!-- Hidden select bound to quick tabs and datatables -->
                        <select id="filter_status" class="form-select cust-filter-select shadow-none" style="display:none;">
                            <option value="">All Statuses</option>
                            <option value="Active">Active</option>
                            <option value="Under Validation">Under Validation</option>
                        </select>
                        <button type="button" id="cust_reset_filters" class="btn cust-btn-reset" title="Reset all filters">
                            <i class="fa fa-rotate-left"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                    <div class="cust-toolbar-right">
                        <label class="cust-entries-label">
                            <span>Show</span>
                            <select id="cust_page_length" class="form-select cust-page-select shadow-none">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <span>entries</span>
                        </label>
                    </div>
                </div>

                <div class="cust-table-responsive">
                    <table id="customersTable" class="table table-hover align-middle mb-0 display" style="width:100%">
                        <thead>
                            <tr>
                                <th class="col-th-num text-center">#</th>
                                <th class="col-th-cust">Customer / Company</th>
                                <th class="col-th-dist">District</th>
                                <th class="col-th-phone">Contact Numbers</th>
                                <th class="col-th-tax">Tax & Statutory IDs</th>
                                <th class="col-th-status text-center">Status</th>
                                <th class="col-th-action text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                            <tr>
                                <td class="text-center text-muted fw-semibold" style="font-size: 0.78rem;">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="cust-avatar-squircle">
                                            {{ strtoupper(substr($customer->company_name ?? $customer->customer_name, 0, 2)) }}
                                        </span>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                <a href="{{ route('customers.show', $customer->slug ?? $customer->id) }}" class="cust-name-link text-truncate" title="View 360° Profile">
                                                    {{ $customer->company_name ?? $customer->customer_name }}
                                                </a>
                                                @if(!empty($customer->company_name) && ($companyContactCounts[$customer->company_name] ?? 0) > 1)
                                                    <span class="cust-badge-contacts" title="{{ $companyContactCounts[$customer->company_name] }} contacts linked to {{ $customer->company_name }}">
                                                        <i class="fa fa-users me-1"></i>{{ $companyContactCounts[$customer->company_name] }} Contacts
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="cust-subinfo">
                                                <span class="cust-badge-id" title="Customer Master ID">CUST-{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                <span class="cust-badge-mimas" title="State Mining Unique ID"><i class="fa fa-fingerprint me-1"></i>{{ $customer->mimas_no }}</span>
                                                @if($customer->mimas_number)
                                                    <span class="cust-badge-mimas-number" title="MIMAS Portal No: {{ $customer->mimas_number }} @if($customer->mimas_status)({{ $customer->mimas_status }})@endif">{{ $customer->mimas_number }}</span>
                                                @endif
                                                @if($customer->customer_name && $customer->customer_name !== $customer->company_name)
                                                    <span class="cust-contact-person"><i class="fa fa-user me-1 opacity-50"></i>{{ $customer->customer_name }}</span>
                                                @endif
                                                @if($customer->email)
                                                    <span class="cust-meta-dot">&bull;</span>
                                                    <span class="cust-email-text text-truncate" title="{{ $customer->email }}">{{ $customer->email }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($customer->district && $customer->district->name)
                                        <span class="cust-loc-tag" title="District: {{ $customer->district->name }}">
                                            <i class="fa fa-map-pin me-1 text-primary"></i>
                                            <span>{{ $customer->district->name }}</span>
                                        </span>
                                    @else
                                        <span class="text-muted opacity-35 fs-12">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="cust-phone-primary">
                                        <a href="tel:{{ $customer->mobile_num }}" class="cust-phone-link">
                                            <i class="fa fa-phone me-1.5 opacity-60"></i>{{ $customer->mobile_num }}
                                        </a>
                                    </div>
                                    @if($customer->secondary_mobile_num)
                                        <div class="cust-phone-secondary text-truncate" title="Alternate: {{ $customer->secondary_contact_person ?? 'Secondary contact' }}">
                                            <span class="cust-badge-alt">ALT</span>
                                            <span>{{ $customer->secondary_mobile_num }}</span>
                                            @if($customer->secondary_contact_person)
                                                <span class="text-muted opacity-75" style="font-size: 0.7rem;">({{ $customer->secondary_contact_person }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-1.5">
                                        @if($customer->pan)
                                            <span class="cust-tax-pill cust-pill-pan" title="Permanent Account Number: {{ $customer->pan }}">
                                                <span class="tax-tag">PAN</span>
                                                <span class="tax-val">{{ $customer->pan }}</span>
                                            </span>
                                        @endif
                                        @if($customer->aadhaar_no)
                                            <span class="cust-tax-pill cust-pill-aadhaar" title="Aadhaar UID: {{ $customer->aadhaar_no }}">
                                                <i class="fa fa-id-card me-1 opacity-70"></i>{{ $customer->aadhaar_no }}
                                            </span>
                                        @endif
                                        @if(!$customer->pan && !$customer->aadhaar_no && !$customer->gstin)
                                            <span class="cust-tax-empty">
                                                <i class="fa fa-shield-alt me-1 opacity-40"></i>Not Registered
                                            </span>
                                        @endif
                                    </div>
                                    @if($customer->gstin)
                                        <div class="cust-tax-pill cust-pill-gst mt-1" title="GST Identification Number: {{ $customer->gstin }}">
                                            <span class="tax-tag">GST</span>
                                            <span class="tax-val">{{ $customer->gstin }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($customer->status == 1)
                                        <span class="cust-status-badge status-active">
                                            <span class="status-pulse-dot dot-active"></span>
                                            <span>Active</span>
                                        </span>
                                    @else
                                        <span class="cust-status-badge status-pending">
                                            <span class="status-pulse-dot dot-pending"></span>
                                            <span>Under Validation</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap pe-3">
                                    <div class="cust-action-dock">
                                        <a href="{{ route('customers.show', $customer->slug ?? $customer->id) }}" class="dock-btn dock-view" title="View 360° Profile">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('customer.edit')
                                        <button type="button" class="dock-btn dock-edit editCustomerBtn" 
                                            data-id="{{ $customer->id }}"
                                            data-mimas="{{ $customer->mimas_no }}"
                                            data-mimas-number="{{ $customer->mimas_number }}"
                                            data-mimas-status="{{ $customer->mimas_status }}"
                                            data-name="{{ $customer->customer_name }}"
                                            data-secondary-contact="{{ $customer->secondary_contact_person }}"
                                            data-company="{{ $customer->company_name }}"
                                            data-mobile="{{ $customer->mobile_num }}"
                                            data-secondary-mobile="{{ $customer->secondary_mobile_num }}"
                                            data-email="{{ $customer->email }}"
                                            data-district-id="{{ $customer->district_id }}"
                                            data-pan="{{ $customer->pan }}"
                                            data-aadhaar="{{ $customer->aadhaar_no }}"
                                            data-gstin="{{ $customer->gstin }}"
                                            data-status="{{ $customer->status }}"
                                            data-address="{{ $customer->address }}"
                                            title="Edit Customer">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('customer.delete')
                                        <button type="button" class="dock-btn dock-delete deleteCustomerBtn" 
                                            data-id="{{ $customer->id }}" 
                                            data-name="{{ $customer->company_name ?: $customer->customer_name }}" 
                                            title="Delete Customer">
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

<!-- ========================================================================= -->
<!-- MODAL: ADD CUSTOMER                                                       -->
<!-- ========================================================================= -->
<div class="modal fade bd-customer-modal-lg" tabindex="-1" id="customerModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="customeradd" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-bold">Customer Unique ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-fingerprint text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_no" placeholder="e.g. CUST-001 or TN-MMS-2026/001" required>
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Customer Master ID</div>
                        <span class="text-danger error-text mimas_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_number" placeholder="e.g. TN-MMS-2026/0482">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">State Mining Portal registration number</div>
                        <span class="text-danger error-text mimas_number_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-info-circle text-primary"></i></span>
                            <input type="text" class="form-control" name="mimas_status" placeholder="e.g. Active / Registered">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Input status in State MIMAS portal</div>
                        <span class="text-danger error-text mimas_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Customer / Representative Name (Primary) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Full Name" required>
                        <span class="text-danger error-text customer_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Company <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="company_name" list="existing_companies" placeholder="Type or select existing company..." autocomplete="off">
                        <span class="text-danger error-text company_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="10-digit Mobile" required maxlength="15">
                        <span class="text-danger error-text mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="customer@example.com">
                        <span class="text-danger error-text email_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Contact Person Name <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="secondary_contact_person" placeholder="e.g. Quarry Manager / Supervisor">
                        <span class="text-danger error-text secondary_contact_person_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Mobile Number <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="secondary_mobile_num" placeholder="10-digit Alternate Mobile" maxlength="15">
                        <span class="text-danger error-text secondary_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select class="form-control" name="district_id" required>
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text district_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Under Validation / Inactive</option>
                        </select>
                        <span class="text-danger error-text status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">PAN Number <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control text-uppercase" name="pan" placeholder="AAACS1234F">
                        <span class="text-danger error-text pan_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Aadhaar Number <span class="text-muted small">(Optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-card text-primary"></i></span>
                            <input type="text" class="form-control aadhaar-format" name="aadhaar_no" placeholder="9876-5432-1012" maxlength="14">
                        </div>
                        <span class="text-danger error-text aadhaar_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">GSTIN (15 Digits)</label>
                        <input type="text" class="form-control text-uppercase" name="gstin" placeholder="33AAACS1234F1Z5">
                        <span class="text-danger error-text gstin_error"></span>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Registered Office / Business Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Street, Taluk, Village, Pincode..."></textarea>
                        <span class="text-danger error-text address_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: VIEW CUSTOMER PROFILE                                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fa fa-id-card text-primary"></i>
                    <span>Customer Profile Overview</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-4 text-center border-end p-3">
                        <span class="cust-avatar mx-auto mb-3" id="view_avatar_initials" style="width:72px;height:72px;border-radius:18px;font-size:1.6rem;">
                            CU
                        </span>
                        <h5 class="fw-bold mb-1" id="view_company_title" style="color: var(--cust-text-primary);">-</h5>
                        <p class="text-muted small mb-3" id="view_rep_subtitle">-</p>
                        <div id="view_status_badge"></div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Customer Master ID</small>
                                    <span class="cust-badge-mimas fw-bold" id="view_mimas_no">-</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">MIMAS Number</small>
                                    <div id="view_mimas_number" class="fw-bold font-monospace" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">MIMAS Status</small>
                                    <div id="view_mimas_status" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Primary Contact</small>
                                    <div id="view_rep" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Primary Mobile</small>
                                    <div id="view_mobile" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Secondary Contact</small>
                                    <div id="view_secondary_contact" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Secondary Mobile</small>
                                    <div id="view_secondary_mobile" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Email Address</small>
                                    <div id="view_email" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Home District</small>
                                    <div id="view_district" class="fw-bold" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">PAN Number</small>
                                    <div id="view_pan" class="fw-bold font-monospace" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Aadhaar Number</small>
                                    <div id="view_aadhaar_no" class="fw-bold font-monospace" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">GSTIN</small>
                                    <div id="view_gstin" class="fw-bold font-monospace" style="color: var(--cust-text-primary);">-</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 px-3 rounded-3" style="background: var(--cust-surface-subtle); border: 1px solid var(--cust-border);">
                                    <small class="text-muted d-block" style="font-size: 0.72rem; font-weight: 600; text-transform: uppercase;">Registered Business Address</small>
                                    <div id="view_address" class="fw-normal" style="color: var(--cust-text-primary); font-size: 0.85rem;">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT CUSTOMER                                                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="customeredit" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-bold">Customer Unique ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-fingerprint text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_no" id="edit_mimas_no" required>
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Customer Master ID</div>
                        <span class="text-danger error-text edit_mimas_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_number" id="edit_mimas_number" placeholder="e.g. TN-MMS-2026/0482">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">State Mining Portal registration number</div>
                        <span class="text-danger error-text edit_mimas_number_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-info-circle text-primary"></i></span>
                            <input type="text" class="form-control" name="mimas_status" id="edit_mimas_status" placeholder="e.g. Active / Registered">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Input status in State MIMAS portal</div>
                        <span class="text-danger error-text edit_mimas_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Customer / Representative Name (Primary) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" id="edit_customer_name" required>
                        <span class="text-danger error-text edit_customer_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Company <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="company_name" id="edit_company_name" list="existing_companies" placeholder="Type or select existing company..." autocomplete="off">
                        <span class="text-danger error-text edit_company_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_num" id="edit_mobile_num" required maxlength="15">
                        <span class="text-danger error-text edit_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="edit_email">
                        <span class="text-danger error-text edit_email_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Contact Person Name</label>
                        <input type="text" class="form-control" name="secondary_contact_person" id="edit_secondary_contact_person" placeholder="e.g. Quarry Manager">
                        <span class="text-danger error-text edit_secondary_contact_person_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Mobile Number</label>
                        <input type="text" class="form-control" name="secondary_mobile_num" id="edit_secondary_mobile_num" placeholder="10-digit Alternate Mobile" maxlength="15">
                        <span class="text-danger error-text edit_secondary_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select class="form-control" name="district_id" id="edit_district_id" required>
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text edit_district_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="edit_status">
                            <option value="1">Active</option>
                            <option value="0">Under Validation / Inactive</option>
                        </select>
                        <span class="text-danger error-text edit_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">PAN Number <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control text-uppercase" name="pan" id="edit_pan">
                        <span class="text-danger error-text edit_pan_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Aadhaar Number <span class="text-muted small">(Optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-card text-primary"></i></span>
                            <input type="text" class="form-control aadhaar-format" name="aadhaar_no" id="edit_aadhaar_no" maxlength="14">
                        </div>
                        <span class="text-danger error-text edit_aadhaar_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">GSTIN (15 Digits)</label>
                        <input type="text" class="form-control text-uppercase" name="gstin" id="edit_gstin">
                        <span class="text-danger error-text edit_gstin_error"></span>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Registered Office / Business Address</label>
                        <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
                        <span class="text-danger error-text edit_address_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>

<datalist id="existing_companies">
    @if(!empty($existingCompanies))
        @foreach($existingCompanies as $compName)
            <option value="{{ $compName }}"></option>
        @endforeach
    @endif
</datalist>

@endsection
@section('scripts')
<script src="{{ asset('js/ajax/customer.js') }}"></script>
@endsection
