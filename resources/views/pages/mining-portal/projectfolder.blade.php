@extends('layouts.app')
@section('title', isset($application) ? 'Folders — ' . $application->application_no : 'Project Folders & Statutory Dossiers')
@section('main_content')

<style>
/* ==========================================================================
   GTMS PROJECT FOLDERS & STATUTORY DOSSIERS — DESIGN TOKENS & SYSTEM
   ui-ux-pro-max standard: High-density SaaS ergonomics, dark mode parity,
   bento KPI metrics, 7-column table without horizontal sideways scrolling.
   ========================================================================== */

:root {
    --pf-surface: #ffffff;
    --pf-surface-subtle: #f8fafc;
    --pf-border: #e2e8f0;
    --pf-border-subtle: #f1f5f9;
    --pf-text-primary: #0f172a;
    --pf-text-secondary: #475569;
    --pf-text-muted: #64748b;
    --pf-table-header-bg: #f8fafc;
    --pf-hover-bg: rgba(59, 130, 246, 0.04);
    --pf-card-bg: #ffffff;
    --pf-card-border: #e2e8f0;
    --pf-navy-gradient: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    --pf-purple-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    --pf-emerald-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --pf-amber-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --pf-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
    --pf-shadow-md: 0 4px 12px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.05);
}

body[data-theme-version="dark"] {
    --pf-surface: #181818;
    --pf-surface-subtle: #1f1f1f;
    --pf-border: #282828;
    --pf-border-subtle: #222222;
    --pf-text-primary: #f1f5f9;
    --pf-text-secondary: #94a3b8;
    --pf-text-muted: #64748b;
    --pf-table-header-bg: #1e1e1e;
    --pf-hover-bg: rgba(59, 130, 246, 0.08);
    --pf-card-bg: #1c2438;
    --pf-card-border: #2d3748;
    --pf-navy-gradient: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    --pf-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --pf-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
}

/* Base card & layout containers */
.pf-panel {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 14px;
    box-shadow: var(--pf-shadow-sm);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Cockpit Header Suite */
.pf-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
}

.pf-brand-squircle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--pf-navy-gradient);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
    flex-shrink: 0;
}

.pf-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--pf-text-primary);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.pf-sub {
    font-size: 0.84rem;
    color: var(--pf-text-secondary);
    margin: 0;
}

.pf-version-pill {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: rgba(59, 130, 246, 0.12);
    color: #3b82f6;
    border: 1px solid rgba(59, 130, 246, 0.25);
    padding: 0.15rem 0.55rem;
    border-radius: 9999px;
    vertical-align: middle;
}

.pf-btn-cta {
    background: var(--pf-navy-gradient);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 0.6rem 1.25rem;
    border-radius: 10px;
    border: none;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    text-decoration: none;
}

.pf-btn-cta:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 30, 77, 0.3);
    color: #ffffff !important;
}

/* 4 Bento KPI Metric Cards */
.pf-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
    .pf-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .pf-kpi-grid {
        grid-template-columns: 1fr;
    }
}

.pf-kpi-card {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--pf-shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
}

.pf-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--pf-shadow-md);
    border-color: rgba(59, 130, 246, 0.35);
}

.pf-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.pf-kpi-card.kpi-cobalt::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.pf-kpi-card.kpi-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.pf-kpi-card.kpi-emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }
.pf-kpi-card.kpi-purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

.pf-kpi-squircle {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.kpi-cobalt .pf-kpi-squircle { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
.kpi-amber .pf-kpi-squircle { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.kpi-emerald .pf-kpi-squircle { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.kpi-purple .pf-kpi-squircle { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }

.pf-kpi-meta {
    flex: 1;
    min-width: 0;
}

.pf-kpi-label {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--pf-text-secondary);
    margin-bottom: 0.15rem;
}

.pf-kpi-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--pf-text-primary);
    line-height: 1.2;
    margin-bottom: 0.15rem;
}

.pf-kpi-sub {
    font-size: 0.73rem;
    color: var(--pf-text-muted);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* Quick Status Segmented Filter Tabs */
.pf-tabs-dock {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem;
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 10px;
    margin-bottom: 1rem;
    overflow-x: auto;
}

.pf-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.4rem 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--pf-text-secondary);
    background: transparent;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pf-tab-btn:hover {
    color: var(--pf-text-primary);
    background: rgba(0, 0, 0, 0.04);
}

body[data-theme-version="dark"] .pf-tab-btn:hover {
    background: rgba(255, 255, 255, 0.06);
}

.pf-tab-btn.active {
    background: var(--pf-navy-gradient);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

.pf-tab-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
    color: inherit;
}

.pf-tab-btn.active .pf-tab-badge {
    background: rgba(255, 255, 255, 0.25);
    color: #ffffff;
}

/* SaaS Multi-Filter Toolbar */
.pf-toolbar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1.25rem;
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}

.pf-search-wrap {
    position: relative;
    flex: 1 1 240px;
    min-width: 220px;
}

.pf-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--pf-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

.pf-search-input {
    width: 100%;
    height: 38px;
    padding: 0.4rem 2rem 0.4rem 2.3rem;
    font-size: 0.83rem;
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 8px;
    color: var(--pf-text-primary);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.pf-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    background: var(--pf-surface);
}

.pf-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--pf-text-muted);
    font-size: 0.85rem;
    cursor: pointer;
    display: none;
    padding: 0;
}

.pf-select {
    height: 38px;
    padding: 0.4rem 2rem 0.4rem 0.75rem;
    font-size: 0.82rem;
    background-color: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 8px;
    color: var(--pf-text-primary);
    cursor: pointer;
    min-width: 140px;
    flex-shrink: 0;
}

.pf-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.pf-btn-reset {
    height: 38px;
    padding: 0 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--pf-text-secondary);
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pf-btn-reset:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.3);
}

/* Executive 8-Column Data Table */
.pf-table-wrap {
    overflow-x: auto;
    border-radius: 12px;
}

.pf-table {
    width: 100% !important;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.82rem;
    table-layout: fixed !important;
}

.pf-table thead th {
    background: var(--pf-table-header-bg);
    color: var(--pf-text-secondary);
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.7rem 0.5rem;
    border-bottom: 1px solid var(--pf-border);
    border-top: none;
    vertical-align: middle;
    white-space: nowrap;
}

.pf-table tbody td {
    padding: 0.65rem 0.5rem;
    border-bottom: 1px solid var(--pf-border-subtle);
    color: var(--pf-text-primary);
    vertical-align: middle;
    background: transparent;
    transition: background-color 0.15s ease;
}

.pf-table tbody tr {
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

.pf-table tbody tr:hover td {
    background-color: var(--pf-hover-bg);
}

/* Table Badges & Cell Typography */
.pf-idx-badge {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    color: var(--pf-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
}

.pf-app-link {
    font-weight: 700;
    color: #2563eb !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: color 0.15s ease;
}

.pf-app-link:hover {
    color: #1d4ed8 !important;
    text-decoration: underline;
}

.pf-common-id-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.69rem;
    font-weight: 600;
    background: rgba(15, 30, 77, 0.08);
    color: var(--pf-text-secondary);
    border: 1px solid rgba(15, 30, 77, 0.15);
    padding: 0.1rem 0.45rem;
    border-radius: 5px;
    display: inline-block;
    margin-top: 0.2rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pf-common-id-chip {
    background: rgba(96, 165, 250, 0.12);
    color: #93c5fd;
    border-color: rgba(96, 165, 250, 0.25);
}

.pf-lease-link {
    font-size: 0.69rem;
    font-weight: 600;
    color: #059669 !important;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.25);
    padding: 0.1rem 0.45rem;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.2rem;
    white-space: nowrap;
}

.pf-lease-link:hover {
    background: rgba(16, 185, 129, 0.2);
    color: #047857 !important;
}

/* Client avatar & info */
.pf-avatar-squircle {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: var(--pf-navy-gradient);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    flex-shrink: 0;
}

.pf-client-name {
    font-weight: 700;
    color: var(--pf-text-primary);
    line-height: 1.25;
    margin-bottom: 0.15rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 160px;
}

.pf-client-phone {
    font-size: 0.72rem;
    color: var(--pf-text-muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.pf-mimas-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.25rem;
    white-space: nowrap;
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb;
    border: 1px solid rgba(59, 130, 246, 0.25);
}

body[data-theme-version="dark"] .pf-mimas-chip {
    background: rgba(59, 130, 246, 0.15);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.3);
}

/* Location Tag */
.pf-loc-tag {
    font-weight: 600;
    color: var(--pf-text-primary);
    display: flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.pf-loc-sub {
    font-size: 0.72rem;
    color: var(--pf-text-muted);
    margin-top: 0.15rem;
    line-height: 1.25;
}

/* Mineral & Work */
.pf-mineral-badge {
    font-size: 0.76rem;
    font-weight: 700;
    color: var(--pf-text-primary);
    display: flex;
    align-items: center;
    gap: 0.35rem;
    white-space: nowrap;
}

.pf-now-badge {
    font-size: 0.69rem;
    font-weight: 700;
    color: #4f46e5;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.22);
    padding: 0.12rem 0.45rem;
    border-radius: 5px;
    display: inline-block;
    margin-top: 0.2rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pf-now-badge {
    color: #a5b4fc;
    background: rgba(99, 102, 241, 0.18);
    border-color: rgba(99, 102, 241, 0.35);
}

/* Folder Progress Bar */
.pf-progress-wrap {
    min-width: 135px;
}

.pf-progress-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.72rem;
    margin-bottom: 0.3rem;
}

.pf-progress-files {
    font-weight: 700;
    color: var(--pf-text-primary);
}

.pf-progress-pct {
    font-weight: 600;
    color: var(--pf-text-muted);
}

.pf-progress-track {
    height: 6px;
    border-radius: 9999px;
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    overflow: hidden;
}

.pf-progress-fill {
    height: 100%;
    border-radius: 9999px;
    background: linear-gradient(90deg, #3b82f6, #2563eb);
    transition: width 0.4s ease;
}

.pf-progress-fill.complete {
    background: linear-gradient(90deg, #10b981, #059669);
}

/* Stage & Status */
.pf-stage-pill {
    font-size: 0.72rem;
    font-weight: 700;
    color: #2563eb;
    background: rgba(37, 99, 235, 0.1);
    border: 1px solid rgba(37, 99, 235, 0.2);
    padding: 0.15rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 0.25rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pf-stage-pill {
    color: #93c5fd;
    background: rgba(37, 99, 235, 0.2);
    border-color: rgba(37, 99, 235, 0.35);
}

.pf-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    white-space: nowrap;
}

.pf-status-badge.status-approved {
    background: rgba(16, 185, 129, 0.12);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
}

.pf-status-badge.status-draft {
    background: rgba(139, 92, 246, 0.12);
    color: #7c3aed;
    border: 1px solid rgba(139, 92, 246, 0.25);
}

.pf-status-badge.status-scrutiny {
    background: rgba(245, 158, 11, 0.12);
    color: #d97706;
    border: 1px solid rgba(245, 158, 11, 0.25);
}

body[data-theme-version="dark"] .pf-status-badge.status-approved {
    color: #34d399;
    background: rgba(16, 185, 129, 0.2);
}

body[data-theme-version="dark"] .pf-status-badge.status-draft {
    color: #c4b5fd;
    background: rgba(139, 92, 246, 0.2);
}

body[data-theme-version="dark"] .pf-status-badge.status-scrutiny {
    color: #fbbf24;
    background: rgba(245, 158, 11, 0.2);
}

.pf-pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    animation: pf-pulse 2s infinite;
}

.status-approved .pf-pulse-dot { background-color: #10b981; }
.status-draft .pf-pulse-dot { background-color: #8b5cf6; }
.status-scrutiny .pf-pulse-dot { background-color: #f59e0b; }

@keyframes pf-pulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.35); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}

/* Action button */
.pf-btn-view-folders {
    background: var(--pf-emerald-gradient);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    white-space: nowrap;
}

.pf-btn-view-folders:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
    color: #ffffff !important;
}

/* Pagination & Info Footer */
.pf-footer-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--pf-border);
    flex-wrap: wrap;
    gap: 1rem;
}

.pf-footer-info {
    font-size: 0.78rem;
    color: var(--pf-text-muted);
}

/* ==========================================================================
   CASE 2: DOSSIER VIEW (SPECIFIC ID PASSED)
   ========================================================================== */
.pf-dossier-header {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 14px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--pf-shadow-sm);
}

.pf-dossier-recap {
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.pf-recap-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--pf-text-secondary);
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.pf-recap-val {
    font-weight: 700;
    color: var(--pf-text-primary);
    font-size: 0.88rem;
}

.pf-folder-card {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: var(--pf-shadow-sm);
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    text-decoration: none;
    color: inherit;
}

.pf-folder-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--pf-shadow-md);
    color: inherit;
}

.pf-folder-icon-box {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-bottom: 0.85rem;
}

.pf-folder-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--pf-text-primary);
    margin-bottom: 0.25rem;
}

.pf-folder-sub {
    font-size: 0.78rem;
    color: var(--pf-text-secondary);
    margin-bottom: 1rem;
    flex: 1;
}

.pf-banner-stage {
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: var(--pf-shadow-sm);
}

/* ========================================================================= */
/* CASE 2: DOSSIER WORKSPACE CSS */
/* ========================================================================= */
.pf-dossier-hero {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: var(--pf-shadow-sm);
    margin-bottom: 1.25rem;
}

.pf-dossier-tabs {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.35rem;
    background: var(--pf-surface-subtle);
    border: 1px solid var(--pf-border);
    border-radius: 10px;
    margin-bottom: 1.25rem;
    overflow-x: auto;
}

.pf-dossier-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1.1rem;
    border-radius: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--pf-text-secondary);
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none;
}

.pf-dossier-tab-btn:hover {
    color: var(--pf-text-primary);
    background: rgba(59, 130, 246, 0.08);
}

.pf-dossier-tab-btn.active {
    background: var(--pf-surface);
    color: #2563eb;
    border-color: var(--pf-border);
    box-shadow: var(--pf-shadow-sm);
    font-weight: 700;
}

body[data-theme-version="dark"] .pf-dossier-tab-btn.active {
    background: #1e293b;
    color: #60a5fa;
    border-color: #334155;
}

.pf-folder-nav-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    margin-bottom: 1.25rem;
}

.pf-folder-nav-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    padding: 0.6rem 1rem;
    border-radius: 10px;
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    color: var(--pf-text-primary);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
    text-decoration: none !important;
}

.pf-folder-nav-pill:hover {
    border-color: #3b82f6;
    color: #2563eb;
    transform: translateY(-1px);
}

.pf-folder-nav-pill.active {
    background: linear-gradient(135deg, #1e3a8a, #2563eb) !important;
    color: #ffffff !important;
    border-color: #1e3a8a !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
}

.pf-folder-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    font-size: 0.72rem;
    font-weight: 700;
    line-height: 1;
    transition: all 0.15s ease;
}

/* Badge Completed (e.g. 4/4) */
.pf-folder-badge.badge-completed {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac !important;
}

/* Badge In Progress / Partial (e.g. 1/28) */
.pf-folder-badge.badge-in-progress {
    background: #dbeafe !important;
    color: #1d4ed8 !important;
    border: 1px solid #93c5fd !important;
}

/* Badge Empty / Zero (e.g. 0/1, 0/4) */
.pf-folder-badge.badge-empty {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

/* Dark theme overrides for folder badges */
body[data-theme-version="dark"] .pf-folder-badge.badge-completed {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.4) !important;
}

body[data-theme-version="dark"] .pf-folder-badge.badge-in-progress {
    background: rgba(59, 130, 246, 0.2) !important;
    color: #93c5fd !important;
    border-color: rgba(59, 130, 246, 0.4) !important;
}

body[data-theme-version="dark"] .pf-folder-badge.badge-empty {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #e2e8f0 !important;
    border-color: rgba(255, 255, 255, 0.18) !important;
}

/* When parent pill is active, badge becomes crisp white */
.pf-folder-nav-pill.active .pf-folder-badge {
    background: #ffffff !important;
    color: #1e3a8a !important;
    border-color: #ffffff !important;
}

/* Requirement Badges */
.pf-req-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    line-height: 1.2;
    white-space: nowrap;
}

.pf-req-badge.optional {
    background: #f1f5f9 !important;
    color: #334155 !important;
    border: 1px solid #cbd5e1 !important;
}

body[data-theme-version="dark"] .pf-req-badge.optional {
    background: rgba(148, 163, 184, 0.15) !important;
    color: #cbd5e1 !important;
    border-color: rgba(148, 163, 184, 0.3) !important;
}

.pf-req-badge.mandatory {
    background: #fef2f2 !important;
    color: #b91c1c !important;
    border: 1px solid #fecaca !important;
}

body[data-theme-version="dark"] .pf-req-badge.mandatory {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #fca5a5 !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
}

/* Folder Filter Buttons */
.btn-filter-status {
    font-size: 0.74rem !important;
    font-weight: 600 !important;
    padding: 0.25rem 0.65rem !important;
    border-radius: 6px !important;
    transition: all 0.15s ease !important;
}

.btn-filter-status.btn-filter-all {
    color: var(--pf-text-secondary) !important;
    background: var(--pf-surface) !important;
    border: 1px solid var(--pf-border) !important;
}

.btn-filter-status.btn-filter-all.active {
    background: #1e293b !important;
    color: #ffffff !important;
    border-color: #1e293b !important;
}

body[data-theme-version="dark"] .btn-filter-status.btn-filter-all.active {
    background: #3b82f6 !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
}

.btn-filter-status.btn-filter-uploaded {
    color: #10b981 !important;
    background: var(--pf-surface) !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
}

.btn-filter-status.btn-filter-uploaded.active {
    background: #10b981 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
}

.btn-filter-status.btn-filter-pending {
    color: #d97706 !important;
    background: var(--pf-surface) !important;
    border: 1px solid rgba(245, 158, 11, 0.3) !important;
}

.btn-filter-status.btn-filter-pending.active {
    background: #f59e0b !important;
    color: #ffffff !important;
    border-color: #f59e0b !important;
}

.pf-doc-card {
    background: var(--pf-surface);
    border: 1px solid var(--pf-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--pf-shadow-sm);
    margin-bottom: 1.5rem;
}

.pf-doc-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--pf-border);
    background: var(--pf-surface-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.pf-doc-table {
    width: 100% !important;
    table-layout: fixed !important;
    border-collapse: separate;
    border-spacing: 0;
}

.pf-doc-table th {
    background: var(--pf-surface-subtle);
    color: var(--pf-text-secondary);
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.75rem 0.85rem;
    border-bottom: 1px solid var(--pf-border);
    white-space: nowrap;
}

.pf-doc-table td {
    padding: 0.8rem 0.85rem;
    border-bottom: 1px solid var(--pf-border);
    color: var(--pf-text-primary);
    font-size: 0.83rem;
    vertical-align: middle;
    word-break: break-word;
}

.pf-doc-table tbody tr:hover {
    background: var(--pf-surface-hover);
}

.pf-doc-icon-sq {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}

.pf-doc-icon-sq.pdf { background: rgba(239, 68, 68, 0.12); color: #ef4444; }
.pf-doc-icon-sq.doc { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
.pf-doc-icon-sq.cad { background: rgba(16, 185, 129, 0.12); color: #10b981; }
.pf-doc-icon-sq.img { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.pf-doc-icon-sq.none { background: var(--pf-surface-subtle); color: var(--pf-text-muted); }

.pf-btn-upload-direct {
    position: relative;
    overflow: hidden;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.pf-btn-upload-direct input[type="file"] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    opacity: 0;
    cursor: pointer;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- CASE 1: NO ID PROVIDED -> MASTER APPLICATIONS DIRECTORY WITH FOLDER PROGRESS --}}
        {{-- ========================================================================= --}}
        @if(!isset($application))

            <!-- 1. Master Cockpit Header Bar -->
            <div class="pf-panel pf-header-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="pf-brand-squircle">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="text-muted small">Dashboard</span>
                            <span class="text-muted small">/</span>
                            <span class="text-muted small">Mining Department</span>
                            <span class="text-muted small">/</span>
                            <span class="text-primary small fw-semibold">Project Folders</span>
                            <span class="pf-version-pill ms-2">v2.4 Enterprise</span>
                        </div>
                        <h1 class="pf-title mb-0">Project Folders &mdash; Client Applications Index</h1>
                        <p class="pf-sub">Select any application to open its 6-folder statutory dossier and manage files.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @can('mining.create')
                        <a href="{{ route('newapplication') }}" class="pf-btn-cta">
                            <i class="fa fa-plus"></i>
                            <span>New Application</span>
                        </a>
                    @endcan
                </div>
            </div>

            <!-- 2. 4 Bento KPI Metric Cards -->
            @php
                $calcTotal = isset($totalApplications) ? $totalApplications : (is_countable($applications) ? count($applications) : $applications->count());
                $calcScrutiny = isset($inScrutinyCount) ? $inScrutinyCount : $applications->whereIn('status', ['scrutiny', 'in_scrutiny', 'review'])->count();
                $calcApproved = isset($approvedCount) ? $approvedCount : $applications->where('status', 'approved')->count();
                $calcDraft = isset($draftCount) ? $draftCount : $applications->where('status', 'draft')->count();
                $calcFiles = isset($totalFilesStored) ? $totalFilesStored : 0;
            @endphp
            <div class="pf-kpi-grid">
                <!-- KPI 1: Total Applications -->
                <div class="pf-kpi-card kpi-cobalt">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Total Applications</div>
                        <div class="pf-kpi-value">{{ $calcTotal }}</div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-layer-group text-primary"></i>
                            <span>Total: {{ $calcTotal }} Applications</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 2: In Scrutiny / Review -->
                <div class="pf-kpi-card kpi-amber">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-hourglass-half"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">In Scrutiny / Review</div>
                        <div class="pf-kpi-value">{{ $calcScrutiny }}</div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-clock text-warning"></i>
                            <span>Departmental Scrutiny</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Approved & Completed -->
                <div class="pf-kpi-card kpi-emerald">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Approved &amp; Completed</div>
                        <div class="pf-kpi-value">{{ $calcApproved }}</div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-certificate text-success"></i>
                            <span>Statutory Approval Orders</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Files Stored / Repository -->
                <div class="pf-kpi-card kpi-purple">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Files Stored / Repository</div>
                        <div class="pf-kpi-value">{{ $calcFiles }}</div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-database text-purple"></i>
                            <span>Checklist File Uploads</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Segmented Quick Status Filter Tabs -->
            <div class="pf-tabs-dock">
                <button type="button" class="pf-tab-btn active" data-filter="">
                    <i class="fa fa-list"></i>
                    <span>All</span>
                    <span class="pf-tab-badge">{{ $calcTotal }}</span>
                </button>
                <button type="button" class="pf-tab-btn" data-filter="approved">
                    <i class="fa fa-check-circle text-success"></i>
                    <span>Approved</span>
                    <span class="pf-tab-badge">{{ $calcApproved }}</span>
                </button>
                <button type="button" class="pf-tab-btn" data-filter="scrutiny">
                    <i class="fa fa-hourglass-half text-warning"></i>
                    <span>In Scrutiny</span>
                    <span class="pf-tab-badge">{{ $calcScrutiny }}</span>
                </button>
                <button type="button" class="pf-tab-btn" data-filter="draft">
                    <i class="fa fa-pencil-alt text-purple"></i>
                    <span>Drafts</span>
                    <span class="pf-tab-badge">{{ $calcDraft }}</span>
                </button>
            </div>

            <!-- 4. Integrated SaaS Multi-Filter Toolbar -->
            <div class="pf-toolbar">
                <div class="pf-search-wrap">
                    <i class="fa fa-search pf-search-icon"></i>
                    <input type="text" id="folder_table_search" class="pf-search-input" placeholder="Search application no, client, district, mineral...">
                    <button type="button" id="folder_search_clear" class="pf-search-clear">&times;</button>
                </div>

                <select id="filter_district" class="pf-select">
                    <option value="">All Districts</option>
                    @if(isset($districts))
                        @foreach($districts as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    @endif
                </select>

                <select id="filter_now" class="pf-select">
                    <option value="">All Nature of Work</option>
                    @if(isset($natureOfWorks))
                        @foreach($natureOfWorks as $now)
                            <option value="{{ $now->name }}">{{ $now->name }}</option>
                        @endforeach
                    @endif
                </select>

                <select id="filter_status" class="pf-select">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="scrutiny">In Scrutiny</option>
                    <option value="approved">Approved</option>
                </select>

                <button type="button" id="btn_reset_filters" class="pf-btn-reset">
                    <i class="fa fa-undo"></i>
                    <span>Reset</span>
                </button>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <select id="filter_page_length" class="pf-select" style="min-width: 105px;">
                        <option value="10">10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>

            <!-- 5. Executive 7-Column High-Density Table Panel -->
            <div class="pf-panel overflow-hidden">
                <div class="pf-table-wrap">
                    <table id="foldersTable" class="pf-table" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width: 38px;" class="text-center">#</th>
                                <th style="width: 21%;">Application No &amp; Scheme</th>
                                <th style="width: 24%;">Client &amp; Licensee</th>
                                <th style="width: 12%;">Location</th>
                                <th style="width: 15%;">Folder Progress</th>
                                <th style="width: 13%;">Stage &amp; Status</th>
                                <th style="width: 15%;" class="text-end pe-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                @php
                                    $totalD = $app->documents ? $app->documents->count() : 0;
                                    $uploadedD = $app->documents ? $app->documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count() : 0;
                                    $pct = $totalD > 0 ? round(($uploadedD / $totalD) * 100) : 0;
                                    $clientName = $app->customer->company_name ?? $app->customer->customer_name ?? 'Client Applicant';
                                    $initials = strtoupper(substr($clientName, 0, 2));
                                    $statusKey = strtolower(trim($app->status ?? 'draft'));
                                @endphp
                                <tr>
                                    <!-- 1. Index -->
                                    <td class="text-center">
                                        <span class="pf-idx-badge">{{ $loop->iteration }}</span>
                                    </td>

                                    <!-- 2. Application No & Linked IDs & Mineral -->
                                    <td>
                                        <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="pf-app-link">
                                            <i class="fa fa-folder text-primary"></i>
                                            <span>{{ $app->application_no }}</span>
                                        </a>
                                        <div>
                                            <span class="pf-common-id-chip" title="Universal Common ID">
                                                <i class="fa fa-fingerprint me-1"></i>{{ $app->common_id ?? ('GTMS-' . date('Y', strtotime($app->created_at ?? 'now')) . '-' . str_pad($app->id, 4, '0', STR_PAD_LEFT)) }}
                                            </span>
                                        </div>
                                        @if($app->lease_application_id)
                                            <div>
                                                <a href="{{ route('viewapplication', ['id' => $app->lease_application_id]) }}" target="_blank" class="pf-lease-link" title="Origin Lease Concession">
                                                    <i class="fa fa-link"></i>
                                                    <span>Lease #{{ $app->leaseApplication->application_no ?? $app->lease_application_id }}</span>
                                                </a>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                            <span class="pf-mineral-badge">
                                                <i class="fa fa-gem text-primary"></i>
                                                <span>{{ $app->minerals && $app->minerals->isNotEmpty() ? $app->minerals->pluck('name')->implode(', ') : ($app->mineral->name ?? 'Standard Scope') }}</span>
                                            </span>
                                            <span class="pf-now-badge">
                                                {{ $app->natureOfWork->name ?? 'Mining Plan' }}
                                            </span>
                                        </div>
                                    </td>

                                    <!-- 3. Client & Licensee -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pf-avatar-squircle">
                                                {{ $initials }}
                                            </div>
                                            <div style="min-width: 0;">
                                                <div class="pf-client-name" title="{{ $clientName }}">
                                                    {{ $clientName }}
                                                </div>
                                                @if(!empty($app->customer?->mobile_num))
                                                    <div class="pf-client-phone">
                                                        <i class="fa fa-phone-alt"></i>
                                                        <span>{{ $app->customer->mobile_num }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($app->customer?->mimas_number))
                                                    <span class="pf-mimas-chip" title="MIMAS Registration Number">
                                                        <i class="fa fa-id-badge"></i>{{ $app->customer->mimas_number }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 4. Location -->
                                    <td>
                                        <div class="pf-loc-tag">
                                            <i class="fa fa-map-marker-alt text-danger"></i>
                                            <span>{{ $app->district->name ?? 'N/A' }}</span>
                                        </div>
                                        @if($app->taluk || $app->village)
                                            <div class="pf-loc-sub">
                                                {{ $app->taluk ?? '' }}{{ $app->taluk && $app->village ? ', ' : '' }}{{ $app->village ?? '' }}
                                            </div>
                                        @endif
                                    </td>

                                    <!-- 5. Folder Upload Progress -->
                                    <td>
                                        <div class="pf-progress-wrap">
                                            <div class="pf-progress-meta">
                                                <span class="pf-progress-files">{{ $uploadedD }}/{{ $totalD }} files</span>
                                                <span class="pf-progress-pct">{{ $pct }}%</span>
                                            </div>
                                            <div class="pf-progress-track">
                                                <div class="pf-progress-fill {{ $pct >= 100 ? 'complete' : '' }}" style="width: {{ $pct }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- 6. Stage & Status -->
                                    <td>
                                        <div>
                                            <span class="pf-stage-pill">
                                                Stage {{ $app->stage ?? '6.1' }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($statusKey === 'approved')
                                                <span class="pf-status-badge status-approved">
                                                    <span class="pf-pulse-dot"></span>
                                                    <span>Approved</span>
                                                </span>
                                            @elseif($statusKey === 'scrutiny' || $statusKey === 'in_scrutiny' || $statusKey === 'review')
                                                <span class="pf-status-badge status-scrutiny">
                                                    <span class="pf-pulse-dot"></span>
                                                    <span>In Scrutiny</span>
                                                </span>
                                            @else
                                                <span class="pf-status-badge status-draft">
                                                    <span class="pf-pulse-dot"></span>
                                                    <span>Draft</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- 7. Action -->
                                    <td class="text-end pe-3">
                                        <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="pf-btn-view-folders" title="Open 6-Folder Statutory Dossier">
                                            <i class="fa fa-folder-open"></i>
                                            <span>View Folders &rarr;</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fa fa-folder-open fs-1 d-block mb-2 text-secondary"></i>
                                        No mining applications found.
                                        <div class="mt-3">
                                            <a href="{{ route('newapplication') }}" class="pf-btn-cta">
                                                <i class="fa fa-plus me-1"></i>Register First Application
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pf-footer-bar">
                    <div id="pf_table_info" class="pf-footer-info">
                        Showing {{ $applications->count() }} registered dossiers
                    </div>
                    <div id="pf_table_pagination" class="d-flex align-items-center gap-1">
                        <!-- DataTables pagination injected here -->
                    </div>
                </div>
            </div>

        {{-- ========================================================================= --}}
        {{-- CASE 2: ID PROVIDED -> SHOW FULL APPLICATION DOSSIER & 6 FOLDERS --}}
        {{-- ========================================================================= --}}
        @else

            <!-- 1. Dossier Header Suite & Switcher -->
            <div class="pf-panel pf-dossier-hero">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <a href="{{ route('projectfolder') }}" class="btn btn-sm btn-outline-secondary px-2 py-1" style="font-size:0.75rem;">
                                <i class="fa fa-arrow-left me-1"></i>Back to Folders Index
                            </a>
                            <span class="text-muted small">/</span>
                            <span class="text-muted small">Mining Application Dossier</span>
                            <span class="text-muted small">/</span>
                            <span class="text-primary small fw-semibold font-monospace">{{ $application->application_no }}</span>
                        </div>
                        <h1 class="pf-title d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span>{{ $application->customer->company_name ?? $application->customer->customer_name }}</span>
                            <span class="pf-now-badge fs-6">
                                @if(strcasecmp($application->natureOfWork->name ?? '', 'Others') == 0 && !empty($application->other_nature_of_work_name))
                                    Others ({{ $application->other_nature_of_work_name }})
                                @else
                                    {{ $application->natureOfWork->name ?? 'Mining Application' }}
                                @endif
                            </span>
                        </h1>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="pf-common-id-chip" style="font-size: 0.78rem;">
                                <i class="fa fa-fingerprint me-1 text-warning"></i> Universal Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y', strtotime($application->created_at ?? 'now')) . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
                            </span>
                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: .8rem;">
                                Mining App No: <b>{{ $application->application_no }}</b>
                            </span>
                            @if($application->lease_application_id)
                                <a href="{{ route('viewapplication', ['id' => $application->lease_application_id]) }}" target="_blank" class="pf-lease-link" style="font-size: .8rem;">
                                    <i class="fa fa-link me-1"></i> Origin: Lease #{{ $application->leaseApplication->application_no ?? $application->lease_application_id }} ↗
                                </a>
                            @endif
                            @if($application->customer?->mimas_number)
                                <span class="pf-mimas-chip" style="font-size: .8rem;" title="MIMAS Number">
                                    <i class="fa fa-id-badge me-1"></i> MIMAS No: <b>{{ $application->customer->mimas_number }}</b>
                                </span>
                            @endif
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: .8rem;">
                                <i class="fa fa-map-marker-alt me-1 text-danger"></i> {{ $application->district->name ?? 'N/A' }} {{ $application->taluk ? '('.$application->taluk.')' : '' }}
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: .8rem;">
                                <i class="fa fa-gem me-1"></i> {{ $application->minerals && $application->minerals->isNotEmpty() ? $application->minerals->pluck('name')->implode(', ') : ($application->mineral->name ?? 'Standard Scope') }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick Switcher Dropdown & Workflow Action -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        @if(isset($allApplications) && $allApplications->count() > 1)
                            <div class="d-flex align-items-center gap-1">
                                <span class="small text-muted d-none d-md-inline">Switch Application:</span>
                                <select class="pf-select" style="min-width: 220px;" onchange="if(this.value) window.location.href='{{ route('projectfolder') }}?id='+this.value;">
                                    @foreach($allApplications as $optApp)
                                        <option value="{{ $optApp->id }}" {{ $optApp->id == $application->id ? 'selected' : '' }}>
                                            {{ $optApp->application_no }} &mdash; {{ $optApp->customer->company_name ?? $optApp->customer->customer_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <a href="{{ route('process', ['id' => $application->id]) }}" class="pf-btn-cta">
                            <i class="fa fa-project-diagram"></i>
                            <span>Process Workflow</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. 4 Bento Metric Overview Cards -->
            <div class="pf-kpi-grid">
                <!-- KPI 1: Total Required Documents -->
                <div class="pf-kpi-card kpi-cobalt">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Statutory Requirements</div>
                        <div class="pf-kpi-value">{{ $totalDocs }} <span style="font-size:0.85rem; font-weight:600; color:var(--pf-text-secondary);">Items</span></div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-layer-group text-primary"></i>
                            <span>Across 6 statutory folders</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 2: Uploaded & Verified -->
                <div class="pf-kpi-card kpi-emerald">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Uploaded &amp; Stored</div>
                        <div class="pf-kpi-value">{{ $uploadedDocs }} <span style="font-size:0.85rem; font-weight:600; color:var(--pf-text-secondary);">/ {{ $totalDocs }} ({{ $overallPercent }}%)</span></div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-cloud-upload-alt text-success"></i>
                            <span>Files securely archived</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Pending Uploads -->
                <div class="pf-kpi-card kpi-amber">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-clock"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Pending Checklist Items</div>
                        <div class="pf-kpi-value">{{ max(0, $totalDocs - $uploadedDocs) }} <span style="font-size:0.85rem; font-weight:600; color:var(--pf-text-secondary);">Remaining</span></div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-exclamation-circle text-warning"></i>
                            <span>Awaiting client/liaison upload</span>
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Compliance Stage -->
                <div class="pf-kpi-card kpi-purple">
                    <div class="pf-kpi-squircle">
                        <i class="fa fa-shield-alt"></i>
                    </div>
                    <div class="pf-kpi-meta">
                        <div class="pf-kpi-label">Compliance Lifecycle</div>
                        <div class="pf-kpi-value" style="font-size:1.15rem;">Stage 6: Scrutiny</div>
                        <div class="pf-kpi-sub">
                            <i class="fa fa-id-card text-info"></i>
                            <span>
                                @if(strcasecmp($application->natureOfWork->name ?? '', 'Others') == 0 && !empty($application->other_nature_of_work_name))
                                    Others ({{ $application->other_nature_of_work_name }})
                                @else
                                    {{ $application->natureOfWork->name ?? 'Mining Application' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Primary Workspace Segmented Mode Switcher -->
            <div class="pf-dossier-tabs">
                <button type="button" class="pf-dossier-tab-btn active" id="tab_btn_docs" onclick="switchDossierMainTab('docs')">
                    <i class="fa fa-folder-open text-primary"></i>
                    <span>Mining Application Documents</span>
                    <span class="badge bg-primary text-white ms-1" style="font-size:0.75rem;">{{ $uploadedDocs }}/{{ $totalDocs }}</span>
                </button>
                <button type="button" class="pf-dossier-tab-btn" id="tab_btn_team" onclick="switchDossierMainTab('team')">
                    <i class="fa fa-users text-secondary"></i>
                    <span>Project Handling Team &amp; Financial Ledger</span>
                    <span class="badge bg-light text-dark border ms-1" style="font-size:0.75rem;">{{ $application->handlers ? $application->handlers->count() : 0 }} Handlers &middot; {{ ucfirst($application->payment_status ?? 'paid') }}</span>
                </button>
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 1: MINING APPLICATION DOCUMENTS WORKSPACE (PRIMARY) -->
            <!-- ========================================================================= -->
            <div id="dossier_section_docs">
                <!-- 6 Statutory Folder Pills Navigation Bar -->
                <div class="pf-folder-nav-bar">
                    @foreach($folders as $fIndex => $folder)
                        @php
                            $fStat = $folderStats[$folder->id] ?? ['total' => 0, 'uploaded' => 0, 'percent' => 0];
                            $isFieldLog = str_contains(strtolower($folder->name), 'field');
                            $isDocs = str_contains(strtolower($folder->name), 'doc');
                            $isPhotos = str_contains(strtolower($folder->name), 'photo');
                            $isReport = str_contains(strtolower($folder->name), 'report');
                            $isPlan = str_contains(strtolower($folder->name), 'plan');
                            
                            $fIcon = 'fa-folder';
                            if ($isFieldLog) { $fIcon = 'fa-clone'; }
                            elseif ($isDocs) { $fIcon = 'fa-file-alt'; }
                            elseif ($isPhotos) { $fIcon = 'fa-camera'; }
                            elseif ($isReport) { $fIcon = 'fa-file-invoice'; }
                            elseif ($isPlan) { $fIcon = 'fa-map'; }
                            else { $fIcon = 'fa-cubes'; }

                            $isActiveFolder = ($activeFolderId == $folder->id || ($loop->first && !$activeFolderId));
                            $badgeClass = 'badge-empty';
                            if ($fStat['uploaded'] >= $fStat['total'] && $fStat['total'] > 0) {
                                $badgeClass = 'badge-completed';
                            } elseif ($fStat['uploaded'] > 0) {
                                $badgeClass = 'badge-in-progress';
                            }
                        @endphp
                        <button type="button" class="pf-folder-nav-pill {{ $isActiveFolder ? 'active' : '' }}" 
                                data-folder-id="{{ $folder->id }}" 
                                onclick="switchDossierFolder({{ $folder->id }})">
                            <i class="fa {{ $fIcon }}"></i>
                            <span>{{ $folder->name }}</span>
                            <span class="pf-folder-badge {{ $badgeClass }}">
                                {{ $fStat['uploaded'] }}/{{ $fStat['total'] }}
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Folder Content Panes -->
                @foreach($folders as $fIndex => $folder)
                    @php
                        $fStat = $folderStats[$folder->id] ?? ['total' => 0, 'uploaded' => 0, 'percent' => 0];
                        $fDocs = $folderStats[$folder->id]['documents'] ?? collect([]);
                        $isActiveFolder = ($activeFolderId == $folder->id || ($loop->first && !$activeFolderId));
                        
                        $isFieldLog = str_contains(strtolower($folder->name), 'field');
                        $isDocs = str_contains(strtolower($folder->name), 'doc');
                        $isPhotos = str_contains(strtolower($folder->name), 'photo');
                        $isReport = str_contains(strtolower($folder->name), 'report');
                        $isPlan = str_contains(strtolower($folder->name), 'plan');
                        
                        $fIcon = 'fa-folder';
                        if ($isFieldLog) { $fIcon = 'fa-clone'; }
                        elseif ($isDocs) { $fIcon = 'fa-file-alt'; }
                        elseif ($isPhotos) { $fIcon = 'fa-camera'; }
                        elseif ($isReport) { $fIcon = 'fa-file-invoice'; }
                        elseif ($isPlan) { $fIcon = 'fa-map'; }
                        else { $fIcon = 'fa-cubes'; }
                    @endphp

                    <div class="pf-folder-content-pane {{ $isActiveFolder ? '' : 'd-none' }}" id="folder_pane_{{ $folder->id }}">
                        <div class="pf-doc-card">
                            <div class="pf-doc-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="pf-brand-squircle" style="width:36px; height:36px; font-size:1rem;">
                                        <i class="fa {{ $fIcon }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="h6 fw-bold mb-0 text-dark">{{ $folder->name }} &mdash; Checklist &amp; Repository</h3>
                                        <span class="text-muted" style="font-size:0.75rem;">
                                            {{ $fStat['total'] }} statutory items &middot; {{ $fStat['uploaded'] }} uploaded ({{ $fStat['percent'] }}% complete)
                                        </span>
                                    </div>
                                </div>

                                <!-- Folder Live Filter Bar -->
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="position-relative" style="width: 220px;">
                                        <input type="text" class="pf-input py-1 px-3 ps-4" style="font-size:0.78rem;" 
                                               placeholder="Search checklist items..." 
                                               onkeyup="filterPaneDocs(this, {{ $folder->id }})">
                                        <i class="fa fa-search text-muted position-absolute" style="left:10px; top:50%; transform:translateY(-50%); font-size:0.72rem;"></i>
                                    </div>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-filter-status btn-filter-all active" onclick="filterPaneStatus(this, {{ $folder->id }}, 'all')">All ({{ $fStat['total'] }})</button>
                                        <button type="button" class="btn btn-filter-status btn-filter-uploaded" onclick="filterPaneStatus(this, {{ $folder->id }}, 'uploaded')">Uploaded ({{ $fStat['uploaded'] }})</button>
                                        <button type="button" class="btn btn-filter-status btn-filter-pending" onclick="filterPaneStatus(this, {{ $folder->id }}, 'pending')">Pending ({{ max(0, $fStat['total'] - $fStat['uploaded']) }})</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Checklist Table -->
                            @if($fDocs->isEmpty())
                                <div class="p-5 text-center text-muted">
                                    <i class="fa fa-folder-open fs-2 mb-2 d-block text-secondary opacity-50"></i>
                                    <p class="mb-0 fw-semibold">No statutory documents configured for {{ $folder->name }} under {{ $application->natureOfWork->name ?? 'this nature of work' }}.</p>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="pf-doc-table table align-middle mb-0" id="doc_table_{{ $folder->id }}">
                                        <thead>
                                            <tr>
                                                <th style="width: 45px;" class="text-center">#</th>
                                                <th style="width: 32%;">Checklist Item / Document Title</th>
                                                <th style="width: 12%;" class="text-center">Requirement</th>
                                                <th style="width: 14%;" class="text-center">Upload Status</th>
                                                <th style="width: 22%;">Stored File Details</th>
                                                <th style="width: 16%;" class="text-end pe-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($fDocs as $dIndex => $doc)
                                                @php
                                                    $ext = strtolower(pathinfo($doc->file_name ?? '', PATHINFO_EXTENSION));
                                                    $iconType = 'none';
                                                    $fileIcon = 'fa-file';
                                                    if (in_array($ext, ['pdf'])) { $iconType = 'pdf'; $fileIcon = 'fa-file-pdf'; }
                                                    elseif (in_array($ext, ['doc', 'docx'])) { $iconType = 'doc'; $fileIcon = 'fa-file-word'; }
                                                    elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) { $iconType = 'img'; $fileIcon = 'fa-file-image'; }
                                                    elseif (in_array($ext, ['dwg', 'dxf', 'kml', 'kmz'])) { $iconType = 'cad'; $fileIcon = 'fa-map'; }
                                                    
                                                    $isUploaded = in_array($doc->status, ['uploaded', 'validated', 'approved']);
                                                @endphp
                                                <tr class="pf-doc-row" data-status="{{ $isUploaded ? 'uploaded' : 'pending' }}" data-name="{{ strtolower($doc->document_name) }}">
                                                    <td class="text-center text-muted fw-bold">{{ $dIndex + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <div class="pf-doc-icon-sq {{ $iconType }}">
                                                                <i class="fa {{ $isUploaded ? $fileIcon : 'fa-file-alt' }}"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark pf-doc-name-text">{{ $doc->document_name }}</div>
                                                                @if($doc->review_note)
                                                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1" style="font-size:0.7rem;">
                                                                        <i class="fa fa-info-circle me-1"></i>Note: {{ $doc->review_note }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($doc->is_required)
                                                            <span class="pf-req-badge mandatory"><i class="fa fa-asterisk text-danger" style="font-size:0.55rem;"></i> Mandatory</span>
                                                        @else
                                                            <span class="pf-req-badge optional"><i class="fa fa-circle text-secondary opacity-50" style="font-size:0.45rem;"></i> Optional</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($doc->status === 'validated' || $doc->status === 'approved')
                                                            <span class="pf-status-badge status-approved"><i class="fa fa-check-circle"></i> Validated</span>
                                                        @elseif($doc->status === 'uploaded')
                                                            <span class="pf-status-badge" style="background:rgba(37,99,235,0.12); color:#2563eb; border:1px solid rgba(37,99,235,0.25);"><i class="fa fa-check"></i> Uploaded</span>
                                                        @elseif($doc->status === 'revision_required')
                                                            <span class="pf-status-badge" style="background:rgba(239,68,68,0.12); color:#dc2626; border:1px solid rgba(239,68,68,0.25);"><i class="fa fa-exclamation-triangle"></i> Revision</span>
                                                        @else
                                                            <span class="pf-status-badge status-scrutiny"><i class="fa fa-clock"></i> Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($doc->file_path && $doc->file_name)
                                                            <div class="d-flex align-items-center gap-2">
                                                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="fw-semibold text-primary font-monospace small text-decoration-none text-truncate" style="max-width:200px;" title="{{ $doc->file_name }}">
                                                                    <i class="fa fa-paperclip me-1"></i>{{ $doc->file_name }}
                                                                </a>
                                                            </div>
                                                            <div class="text-muted" style="font-size:0.72rem;">
                                                                {{ round(($doc->file_size ?? 0) / 1024, 1) }} KB &middot; {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : 'Stored' }}
                                                                @if($doc->uploader) &middot; by {{ $doc->uploader->name }} @endif
                                                            </div>
                                                        @else
                                                            <span class="text-muted fst-italic small"><i class="fa fa-info-circle me-1"></i>No file uploaded yet</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <!-- Direct Auto-Submit Upload Form -->
                                                            <form method="POST" action="{{ route('mining.document.upload') }}" enctype="multipart/form-data" class="d-inline m-0 form-upload-auto">
                                                                @csrf
                                                                <input type="hidden" name="mining_application_id" value="{{ $application->id }}">
                                                                <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                                                <label class="pf-btn-upload-direct {{ $isUploaded ? 'btn btn-sm btn-outline-secondary' : 'btn btn-sm btn-primary' }} mb-0" title="{{ $isUploaded ? 'Replace existing file' : 'Upload file' }}">
                                                                    <i class="fa {{ $isUploaded ? 'fa-sync-alt' : 'fa-upload' }}"></i>
                                                                    <span class="upload-btn-label">{{ $isUploaded ? 'Replace' : 'Upload' }}</span>
                                                                    <input type="file" name="file" onchange="submitDocUpload(this)" class="d-none" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.kml,.kmz,.zip,.dwg,.dxf">
                                                                </label>
                                                            </form>

                                                            @if($doc->file_path)
                                                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-light border px-2 py-1" title="View Document">
                                                                    <i class="fa fa-eye text-primary"></i>
                                                                </a>
                                                                <a href="{{ asset($doc->file_path) }}" download class="btn btn-sm btn-light border px-2 py-1" title="Download Document">
                                                                    <i class="fa fa-download text-secondary"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ========================================================================= -->
            <!-- TAB 2: PROJECT HANDLING TEAM & FINANCIAL LEDGER (SECONDARY) -->
            <!-- ========================================================================= -->
            <div id="dossier_section_team" class="d-none">
                <div class="row g-3 mb-4">
                    <!-- Project Handling Team -->
                    <div class="col-lg-7">
                        <div class="pf-panel p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="fw-bold text-navy small"><i class="fa fa-users text-primary me-2"></i>Project Handling Team</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Assigned technical personnel and field coordinators</div>
                                </div>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
                                    {{ $application->handlers ? $application->handlers->count() : 0 }} Members Assigned
                                </span>
                            </div>
                            @if($application->handlers && $application->handlers->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-sm table-borderless align-middle mb-0" style="font-size:0.83rem;">
                                        <thead class="text-muted border-bottom" style="font-size:0.75rem;">
                                            <tr>
                                                <th style="width:30px;">#</th>
                                                <th>Name</th>
                                                <th>Role / Designation</th>
                                                <th>Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($application->handlers as $hIndex => $handler)
                                                <tr>
                                                    <td class="text-muted fw-bold">{{ $hIndex + 1 }}</td>
                                                    <td class="fw-semibold text-navy"><i class="fa fa-id-card text-primary me-1"></i> {{ $handler->name }}</td>
                                                    <td><span class="badge bg-secondary-subtle text-secondary border py-1 px-2">{{ $handler->role }}</span></td>
                                                    <td class="text-muted small">{{ $handler->notes ?: '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-muted small fst-italic py-3"><i class="fa fa-info-circle me-1"></i> No project handling personnel assigned to this application.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment & Billing Summary -->
                    <div class="col-lg-5">
                        <div class="pf-panel p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="fw-bold text-navy small"><i class="fa fa-wallet text-success me-2"></i>Financial &amp; Billing Ledger</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Settlement status and balance tracking</div>
                                </div>
                                @php
                                    $pStatus = $application->payment_status ?? 'pending';
                                @endphp
                                @if($pStatus === 'paid')
                                    <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">Fully Paid</span>
                                @elseif($pStatus === 'partial')
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:0.75rem;">Partial Payment</span>
                                @else
                                    <span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">Pending Full Due</span>
                                @endif
                            </div>

                            <div class="row g-2 text-center mt-2">
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light">
                                        <span class="text-muted d-block" style="font-size:0.7rem;">Product Value</span>
                                        <b class="text-navy" style="font-size:0.85rem;">₹ {{ number_format((float)($application->product_value ?? 0), 2) }}</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light">
                                        <span class="text-muted d-block" style="font-size:0.7rem;">Paid Amount</span>
                                        <b class="text-success" style="font-size:0.85rem;">₹ {{ number_format((float)($application->paid_amount ?? 0), 2) }}</b>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded bg-light">
                                        <span class="text-muted d-block" style="font-size:0.7rem;">Pending Balance</span>
                                        <b class="text-danger" style="font-size:0.85rem;">₹ {{ number_format((float)($application->pending_amount ?? 0), 2) }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Stage Progression Action Banner -->
            <div class="pf-banner-stage mt-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-primary-subtle text-primary fs-4">
                        <i class="fa fa-project-diagram"></i>
                    </div>
                    <div>
                        <div class="fw-bold small text-dark">Stage 6.1 Upload &amp; Store &rarr; Stage 6.2 Validate Data</div>
                        <div class="text-muted small">
                            Upload required statutory checklist documents across all folders, then transition the dossier for departmental review.
                        </div>
                    </div>
                </div>
                <a href="{{ route('process', ['id' => $application->id]) }}" class="pf-btn-cta px-4">
                    <span>Send for Validation</span>
                    <i class="fa fa-arrow-right"></i>
                </a>
            </div>

        @endif

    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(!isset($application))
        // 1. Destroy any existing DataTables instance on #foldersTable
        if ($.fn.DataTable.isDataTable('#foldersTable')) {
            $('#foldersTable').DataTable().destroy();
        }

        // 2. Initialize DataTables with custom DOM and instant search
        const table = $('#foldersTable').DataTable({
            autoWidth: false,
            responsive: false,
            paging: true,
            pageLength: 10,
            searching: true,
            info: true,
            dom: 't',
            order: [[0, 'asc']],
            language: {
                emptyTable: "No mining plan folders found.",
                zeroRecords: "No matching records found."
            },
            drawCallback: function(settings) {
                const api = this.api();
                const pageInfo = api.page.info();
                
                // Update footer information text
                if (pageInfo.recordsTotal > 0) {
                    $('#pf_table_info').text(
                        'Showing ' + (pageInfo.start + 1) + ' to ' + pageInfo.end + ' of ' + pageInfo.recordsDisplay + ' entries' +
                        (pageInfo.recordsTotal !== pageInfo.recordsDisplay ? ' (filtered from ' + pageInfo.recordsTotal + ' total)' : '')
                    );
                } else {
                    $('#pf_table_info').text('Showing 0 entries');
                }

                // Render modern custom pagination buttons
                renderCustomPagination(pageInfo);
            }
        });

        // 3. Custom Pagination Renderer
        function renderCustomPagination(pageInfo) {
            const $container = $('#pf_table_pagination');
            $container.empty();

            if (pageInfo.pages <= 1) return;

            // Previous Button
            const $prevBtn = $('<button type="button" class="pf-btn-reset px-2 py-1" style="height:32px;"><i class="fa fa-chevron-left"></i></button>')
                .prop('disabled', pageInfo.page === 0)
                .on('click', function() {
                    table.page('previous').draw('page');
                });
            $container.append($prevBtn);

            // Page Number Buttons
            for (let i = 0; i < pageInfo.pages; i++) {
                const isCurrent = i === pageInfo.page;
                const $pageBtn = $('<button type="button" class="pf-tab-btn ' + (isCurrent ? 'active' : '') + '" style="padding:0.25rem 0.65rem; height:32px;">' + (i + 1) + '</button>')
                    .on('click', function() {
                        table.page(i).draw('page');
                    });
                $container.append($pageBtn);
            }

            // Next Button
            const $nextBtn = $('<button type="button" class="pf-btn-reset px-2 py-1" style="height:32px;"><i class="fa fa-chevron-right"></i></button>')
                .prop('disabled', pageInfo.page >= pageInfo.pages - 1)
                .on('click', function() {
                    table.page('next').draw('page');
                });
            $container.append($nextBtn);
        }

        // 4. Live Search Input Binding
        $('#folder_table_search').on('keyup', function() {
            const val = $(this).val();
            table.search(val).draw();
            $('#folder_search_clear').toggle(Boolean(val));
        });

        $('#folder_search_clear').on('click', function() {
            $('#folder_table_search').val('');
            table.search('').draw();
            $(this).hide();
        });

        // 5. Multi-Filter Dropdown Handlers
        // Column indices:
        // 0: #, 1: App No, 2: Client & Licensee, 3: Location & Mineral, 4: Folder Progress, 5: Stage & Status, 6: Action
        $('#filter_district').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });

        $('#filter_now').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });

        $('#filter_status').on('change', function() {
            const status = $(this).val();
            table.column(5).search(status).draw();
            // Sync with tabs
            $('.pf-tab-btn').removeClass('active');
            $('.pf-tab-btn[data-filter="' + status + '"]').addClass('active');
        });

        // 6. Page Length Selector
        $('#filter_page_length').on('change', function() {
            table.page.len(parseInt($(this).val())).draw();
        });

        // 7. Quick Status Tabs Synchronized Filtering
        $('.pf-tab-btn').on('click', function() {
            const filter = $(this).data('filter');
            $('.pf-tab-btn').removeClass('active');
            $(this).addClass('active');

            $('#filter_status').val(filter);
            table.column(5).search(filter ? filter : '').draw();
        });

        // 8. Filter Reset Button
        $('#btn_reset_filters').on('click', function() {
            $('#folder_table_search').val('');
            $('#folder_search_clear').hide();
            $('#filter_district').val('');
            $('#filter_now').val('');
            $('#filter_status').val('');
            $('#filter_page_length').val('10');

            $('.pf-tab-btn').removeClass('active');
            $('.pf-tab-btn[data-filter=""]').addClass('active');

            table.search('').columns().search('').page.len(10).draw();
        });
    @else
        // Case 2: Dossier Workspace Interactive Handlers
        window.switchDossierMainTab = function(tab) {
            if (tab === 'docs') {
                $('#dossier_section_docs').removeClass('d-none');
                $('#dossier_section_team').addClass('d-none');
                $('#tab_btn_docs').addClass('active');
                $('#tab_btn_team').removeClass('active');
            } else {
                $('#dossier_section_docs').addClass('d-none');
                $('#dossier_section_team').removeClass('d-none');
                $('#tab_btn_team').addClass('active');
                $('#tab_btn_docs').removeClass('active');
            }
        };

        window.switchDossierFolder = function(folderId) {
            $('.pf-folder-nav-pill').removeClass('active');
            $('.pf-folder-nav-pill[data-folder-id="' + folderId + '"]').addClass('active');
            $('.pf-folder-content-pane').addClass('d-none');
            $('#folder_pane_' + folderId).removeClass('d-none');
        };

        window.filterPaneDocs = function(input, folderId) {
            const query = $(input).val().toLowerCase().trim();
            $('#folder_pane_' + folderId + ' .pf-doc-row').each(function() {
                const name = $(this).data('name') || '';
                $(this).toggle(name.indexOf(query) !== -1);
            });
        };

        window.filterPaneStatus = function(btn, folderId, status) {
            $(btn).siblings().removeClass('active');
            $(btn).addClass('active');
            
            $('#folder_pane_' + folderId + ' .pf-doc-row').each(function() {
                if (status === 'all') {
                    $(this).show();
                } else {
                    $(this).toggle($(this).data('status') === status);
                }
            });
        };

        window.submitDocUpload = function(input) {
            if (input.files && input.files[0]) {
                const form = $(input).closest('form');
                const label = $(input).siblings('.upload-btn-label');
                label.text('Uploading...');
                $(input).closest('label').addClass('disabled').css('opacity', '0.7');
                form.submit();
            }
        };
    @endif
});
</script>
@endpush
