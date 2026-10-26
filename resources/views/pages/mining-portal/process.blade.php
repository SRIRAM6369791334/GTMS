@extends('layouts.app')
@section('title', isset($application) ? 'Process Flow — ' . $application->application_no : 'Mining Process Applications')
@section('main_content')

<style>
/* ==========================================================================
   GTMS MINING PROCESS WORKFLOW & STATUTORY VALIDATION — DESIGN SYSTEM
   Standard: High-density SaaS ergonomics, dual light/dark mode parity,
   calibrated fixed-width table layout (0 sideways horizontal scrolling),
   high-contrast badges, and interactive 6-stage statutory pipeline.
   ========================================================================== */

:root {
    --pr-surface: #ffffff;
    --pr-surface-subtle: #f8fafc;
    --pr-border: #e2e8f0;
    --pr-border-subtle: #f1f5f9;
    --pr-text-primary: #0f172a;
    --pr-text-secondary: #475569;
    --pr-text-muted: #64748b;
    --pr-table-header-bg: #f8fafc;
    --pr-hover-bg: rgba(59, 130, 246, 0.04);
    --pr-card-bg: #ffffff;
    --pr-card-border: #e2e8f0;
    --pr-navy-gradient: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    --pr-blue-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    --pr-emerald-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --pr-purple-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    --pr-amber-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --pr-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.06), 0 1px 2px rgba(15, 23, 42, 0.04);
    --pr-shadow-md: 0 4px 12px -2px rgba(15, 23, 42, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.05);
}

body[data-theme-version="dark"] {
    --pr-surface: #181818;
    --pr-surface-subtle: #1f1f1f;
    --pr-border: #282828;
    --pr-border-subtle: #222222;
    --pr-text-primary: #f1f5f9;
    --pr-text-secondary: #94a3b8;
    --pr-text-muted: #64748b;
    --pr-table-header-bg: #1e1e1e;
    --pr-hover-bg: rgba(59, 130, 246, 0.08);
    --pr-card-bg: #1c2438;
    --pr-card-border: #2d3748;
    --pr-navy-gradient: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    --pr-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --pr-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
}

/* Panel Containers */
.pr-panel {
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 14px;
    box-shadow: var(--pr-shadow-sm);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Cockpit Header */
.pr-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
}

.pr-brand-squircle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--pr-navy-gradient);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
    flex-shrink: 0;
}

.pr-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--pr-text-primary);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.pr-sub {
    font-size: 0.84rem;
    color: var(--pr-text-secondary);
    margin: 0;
}

.pr-btn-cta {
    background: var(--pr-navy-gradient);
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

.pr-btn-cta:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 30, 77, 0.3);
    color: #ffffff !important;
}

/* Bento KPI Grid */
.pr-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
    .pr-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 576px) {
    .pr-kpi-grid { grid-template-columns: 1fr; }
}

.pr-kpi-card {
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 12px;
    padding: 1.1rem 1.25rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--pr-shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
}

.pr-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--pr-shadow-md);
    border-color: rgba(59, 130, 246, 0.35);
}

.pr-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
}

.pr-kpi-card.kpi-cobalt::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.pr-kpi-card.kpi-purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
.pr-kpi-card.kpi-amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.pr-kpi-card.kpi-emerald::before { background: linear-gradient(90deg, #10b981, #34d399); }

.pr-kpi-squircle {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.kpi-cobalt .pr-kpi-squircle { background: rgba(59, 130, 246, 0.12); color: #3b82f6; }
.kpi-purple .pr-kpi-squircle { background: rgba(139, 92, 246, 0.12); color: #8b5cf6; }
.kpi-amber .pr-kpi-squircle { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
.kpi-emerald .pr-kpi-squircle { background: rgba(16, 185, 129, 0.12); color: #10b981; }

.pr-kpi-meta { flex: 1; min-width: 0; }
.pr-kpi-label {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--pr-text-secondary);
    margin-bottom: 0.15rem;
}

.pr-kpi-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--pr-text-primary);
    line-height: 1.2;
    margin-bottom: 0.15rem;
}

.pr-kpi-sub {
    font-size: 0.73rem;
    color: var(--pr-text-muted);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* Badges for Mining Portal & Lifecycle Pipeline (High contrast & immune to vendor CSS) */
.pr-chip-portal {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    padding: 0.2rem 0.55rem !important;
    border-radius: 5px !important;
    background: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fca5a5 !important;
    display: inline-block !important;
    line-height: 1.2 !important;
}

body[data-theme-version="dark"] .pr-chip-portal {
    background: rgba(220, 38, 38, 0.25) !important;
    color: #fca5a5 !important;
    border-color: rgba(220, 38, 38, 0.45) !important;
}

.pr-chip-pipeline {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.05em !important;
    padding: 0.2rem 0.55rem !important;
    border-radius: 5px !important;
    background: #dbeafe !important;
    color: #1e40af !important;
    border: 1px solid #bfdbfe !important;
    display: inline-block !important;
    line-height: 1.2 !important;
}

body[data-theme-version="dark"] .pr-chip-pipeline {
    background: rgba(37, 99, 235, 0.25) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, 0.45) !important;
}

/* Toolbar & Filter Tabs */
.pr-toolbar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.9rem 1.25rem;
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 12px;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}

.pr-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.8rem;
    font-size: 0.77rem;
    font-weight: 600;
    border-radius: 8px;
    border: 1px solid var(--pr-border);
    background: var(--pr-surface-subtle);
    color: var(--pr-text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pr-filter-pill:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--pr-text-primary);
}

body[data-theme-version="dark"] .pr-filter-pill:hover {
    background: rgba(255, 255, 255, 0.08);
}

.pr-filter-pill.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}

body[data-theme-version="dark"] .pr-filter-pill.active {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
}

.pr-filter-pill.pill-flagged:hover {
    border-color: #ef4444;
    color: #ef4444;
}

.pr-filter-pill.pill-flagged.active {
    background: #dc2626 !important;
    color: #ffffff !important;
    border-color: #dc2626 !important;
}

.pr-search-wrap {
    position: relative;
    flex: 1 1 240px;
    min-width: 220px;
}

.pr-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--pr-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

.pr-search-input {
    width: 100%;
    height: 38px;
    padding: 0.4rem 2rem 0.4rem 2.3rem;
    font-size: 0.83rem;
    background: var(--pr-surface-subtle);
    border: 1px solid var(--pr-border);
    border-radius: 8px;
    color: var(--pr-text-primary);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.pr-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    background: var(--pr-surface);
}

.pr-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--pr-text-muted);
    font-size: 0.85rem;
    cursor: pointer;
    display: none;
    padding: 0;
}

.pr-btn-reset {
    height: 38px;
    padding: 0 0.85rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--pr-text-secondary);
    background: var(--pr-surface-subtle);
    border: 1px solid var(--pr-border);
    border-radius: 8px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pr-btn-reset:hover {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border-color: rgba(239, 68, 68, 0.3);
}

/* 9-Column Fixed Table (Strictly ZERO Horizontal / Sideways Scrolling) */
.pr-table-wrap {
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100% !important;
    border-radius: 12px;
}

.pr-table {
    width: 100% !important;
    max-width: 100% !important;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.8rem;
    table-layout: fixed !important;
}

.pr-table thead th {
    background: var(--pr-table-header-bg);
    color: var(--pr-text-secondary);
    font-size: 0.69rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.65rem 0.35rem;
    border-bottom: 1px solid var(--pr-border);
    border-top: none;
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pr-table tbody td {
    padding: 0.6rem 0.35rem;
    border-bottom: 1px solid var(--pr-border-subtle);
    color: var(--pr-text-primary);
    vertical-align: middle;
    background: transparent;
    transition: background-color 0.15s ease;
    word-break: break-word;
}

.pr-table tbody tr {
    transition: background-color 0.15s ease, border-color 0.15s ease;
}

.pr-table tbody tr:hover td {
    background-color: var(--pr-hover-bg);
}

/* Badges & Chips */
.pr-total-badge {
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    padding: 0.3rem 0.75rem !important;
    border-radius: 6px !important;
    background: #0F1E4D !important;
    color: #ffffff !important;
    border: 1px solid #0F1E4D !important;
    display: inline-flex !important;
    align-items: center !important;
    box-shadow: 0 1px 3px rgba(15, 30, 77, 0.15) !important;
}

body[data-theme-version="dark"] .pr-total-badge {
    background: #1e3a8a !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
}

.pr-idx-badge {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: var(--pr-surface-subtle);
    border: 1px solid var(--pr-border);
    color: var(--pr-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
}

.pr-app-link {
    font-weight: 700;
    color: #2563eb !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

.pr-app-link:hover {
    color: #1d4ed8 !important;
    text-decoration: underline;
}

.pr-common-id-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.69rem;
    font-weight: 600;
    background: rgba(15, 30, 77, 0.08);
    color: var(--pr-text-secondary);
    border: 1px solid rgba(15, 30, 77, 0.15);
    padding: 0.1rem 0.45rem;
    border-radius: 5px;
    display: inline-block;
    margin-top: 0.2rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pr-common-id-chip {
    background: rgba(96, 165, 250, 0.12);
    color: #93c5fd;
    border-color: rgba(96, 165, 250, 0.25);
}

.pr-lease-link {
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

.pr-lease-link:hover {
    background: rgba(16, 185, 129, 0.2);
    color: #047857 !important;
}

.pr-avatar-squircle {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--pr-navy-gradient);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    font-weight: 700;
    flex-shrink: 0;
}

.pr-client-name {
    font-weight: 700;
    color: var(--pr-text-primary);
    line-height: 1.25;
    margin-bottom: 0.15rem;
}

.pr-client-phone {
    font-size: 0.72rem;
    color: var(--pr-text-muted);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.pr-mimas-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.69rem;
    font-weight: 700;
    padding: 0.1rem 0.45rem;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb;
    border: 1px solid rgba(59, 130, 246, 0.25);
}

body[data-theme-version="dark"] .pr-mimas-chip {
    background: rgba(59, 130, 246, 0.18);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.35);
}

.pr-mineral-badge {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--pr-text-primary);
    display: flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.pr-now-badge {
    font-size: 0.68rem;
    font-weight: 700;
    color: #4f46e5;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.25);
    padding: 0.12rem 0.45rem;
    border-radius: 5px;
    display: inline-block;
    margin-top: 0.2rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pr-now-badge {
    color: #a5b4fc;
    background: rgba(99, 102, 241, 0.2);
    border-color: rgba(99, 102, 241, 0.35);
}

.pr-stage-pill {
    font-size: 0.72rem;
    font-weight: 700;
    color: #1d4ed8;
    background: #dbeafe;
    border: 1px solid #93c5fd;
    padding: 0.15rem 0.5rem;
    border-radius: 6px;
    display: inline-block;
    margin-bottom: 0.25rem;
    white-space: nowrap;
}

body[data-theme-version="dark"] .pr-stage-pill {
    color: #93c5fd;
    background: rgba(37, 99, 235, 0.22);
    border-color: rgba(96, 165, 250, 0.35);
}

.pr-val-badge {
    font-size: 0.69rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.pr-val-badge.val-flagged {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fca5a5;
}

.pr-val-badge.val-passed {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}

.pr-val-badge.val-ready {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fcd34d;
}

.pr-val-badge.val-progress {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
}

body[data-theme-version="dark"] .pr-val-badge.val-flagged {
    background: rgba(220, 38, 38, 0.2);
    color: #fca5a5;
    border-color: rgba(220, 38, 38, 0.35);
}

body[data-theme-version="dark"] .pr-val-badge.val-passed {
    background: rgba(16, 185, 129, 0.2);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.35);
}

body[data-theme-version="dark"] .pr-val-badge.val-ready {
    background: rgba(245, 158, 11, 0.2);
    color: #fde047;
    border-color: rgba(245, 158, 11, 0.35);
}

body[data-theme-version="dark"] .pr-val-badge.val-progress {
    background: rgba(255, 255, 255, 0.08);
    color: #e2e8f0;
    border-color: rgba(255, 255, 255, 0.15);
}

.pr-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.2rem 0.55rem;
    border-radius: 9999px;
    white-space: nowrap;
}

.pr-status-pill.status-approved {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}

.pr-status-pill.status-draft {
    background: #ede9fe;
    color: #6d28d9;
    border: 1px solid #c4b5fd;
}

.pr-status-pill.status-scrutiny {
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fcd34d;
}

body[data-theme-version="dark"] .pr-status-pill.status-approved {
    color: #6ee7b7;
    background: rgba(16, 185, 129, 0.2);
    border-color: rgba(16, 185, 129, 0.35);
}

body[data-theme-version="dark"] .pr-status-pill.status-draft {
    color: #c4b5fd;
    background: rgba(139, 92, 246, 0.2);
    border-color: rgba(139, 92, 246, 0.35);
}

body[data-theme-version="dark"] .pr-status-pill.status-scrutiny {
    color: #fde047;
    background: rgba(245, 158, 11, 0.2);
    border-color: rgba(245, 158, 11, 0.35);
}

.pr-pulse-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    animation: pr-pulse 2s infinite;
}

.status-approved .pr-pulse-dot { background-color: #10b981; }
.status-draft .pr-pulse-dot { background-color: #8b5cf6; }
.status-scrutiny .pr-pulse-dot { background-color: #f59e0b; }

@keyframes pr-pulse {
    0% { transform: scale(0.95); opacity: 0.8; }
    50% { transform: scale(1.35); opacity: 1; }
    100% { transform: scale(0.95); opacity: 0.8; }
}

/* Action Button: Modern Indigo Gradient (Eliminates Vendor Pink) */
.pr-btn-workflow {
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.77rem;
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    border: none !important;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    white-space: nowrap;
}

.pr-btn-workflow:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
    color: #ffffff !important;
}

/* ==========================================================================
   CASE 2 DOSSIER SPECIFIC STYLES
   ========================================================================== */
.pr-step-track {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

@media (max-width: 992px) {
    .pr-step-track { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 576px) {
    .pr-step-track { grid-template-columns: repeat(2, 1fr); }
}

.pr-step-node {
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 12px;
    padding: 1rem 0.75rem;
    text-align: center;
    position: relative;
    transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
}

.pr-step-node.active-stage {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.pr-step-node.done-stage {
    border-color: #10b981;
    background: rgba(16, 185, 129, 0.03);
}

.pr-step-num {
    font-size: 1.15rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--pr-text-primary);
}

.pr-step-title {
    font-size: 0.76rem;
    font-weight: 700;
    color: var(--pr-text-secondary);
    margin-bottom: 0.6rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pr-step-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 9999px;
    display: inline-block;
}

.pr-step-node.active-stage .pr-step-badge {
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #93c5fd;
}

.pr-step-node.done-stage .pr-step-badge {
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #86efac;
}

.pr-step-node.pending-stage .pr-step-badge {
    background: var(--pr-surface-subtle);
    color: var(--pr-text-muted);
    border: 1px solid var(--pr-border);
}

body[data-theme-version="dark"] .pr-step-node.active-stage .pr-step-badge {
    background: rgba(37, 99, 235, 0.25);
    color: #93c5fd;
    border-color: rgba(96, 165, 250, 0.35);
}

body[data-theme-version="dark"] .pr-step-node.done-stage .pr-step-badge {
    background: rgba(16, 185, 129, 0.25);
    color: #6ee7b7;
    border-color: rgba(16, 185, 129, 0.35);
}

/* Document Item Card in Validation Queue */
.pr-doc-item {
    background: var(--pr-surface);
    border: 1px solid var(--pr-border);
    border-radius: 10px;
    padding: 0.85rem 1rem;
    margin-bottom: 0.6rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    transition: transform 0.15s ease, border-color 0.15s ease;
}

.pr-doc-item:hover {
    border-color: rgba(59, 130, 246, 0.35);
}

.pr-doc-icon-box {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.doc-passed .pr-doc-icon-box { background: #dcfce7 !important; color: #166534 !important; border: 1px solid #86efac; }
.doc-flagged .pr-doc-icon-box { background: #fee2e2 !important; color: #991b1b !important; border: 1px solid #fca5a5; }
.doc-ready .pr-doc-icon-box { background: #e0f2fe !important; color: #0369a1 !important; border: 1px solid #7dd3fc; }
.doc-pending .pr-doc-icon-box { background: #f1f5f9 !important; color: #64748b !important; border: 1px solid #cbd5e1; }

body[data-theme-version="dark"] .doc-passed .pr-doc-icon-box { background: rgba(16, 185, 129, 0.2) !important; color: #6ee7b7 !important; border-color: rgba(16, 185, 129, 0.35) !important; }
body[data-theme-version="dark"] .doc-flagged .pr-doc-icon-box { background: rgba(220, 38, 38, 0.2) !important; color: #fca5a5 !important; border-color: rgba(220, 38, 38, 0.35) !important; }
body[data-theme-version="dark"] .doc-ready .pr-doc-icon-box { background: rgba(2, 132, 199, 0.2) !important; color: #7dd3fc !important; border-color: rgba(2, 132, 199, 0.35) !important; }
body[data-theme-version="dark"] .doc-pending .pr-doc-icon-box { background: rgba(255, 255, 255, 0.08) !important; color: #94a3b8 !important; border-color: rgba(255, 255, 255, 0.15) !important; }

/* Dossier Navigation & Badges */
.pr-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--pr-text-secondary) !important;
    background: var(--pr-surface-subtle);
    border: 1px solid var(--pr-border);
    border-radius: 7px;
    text-decoration: none !important;
    transition: all 0.15s ease;
}

.pr-btn-back:hover {
    color: var(--pr-text-primary) !important;
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(59, 130, 246, 0.4);
}

body[data-theme-version="dark"] .pr-btn-back:hover {
    background: rgba(255, 255, 255, 0.08);
}

.pr-app-crumb-badge {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    padding: 0.25rem 0.6rem !important;
    border-radius: 6px !important;
    background: #0F1E4D !important;
    color: #ffffff !important;
    border: 1px solid #0F1E4D !important;
    display: inline-block !important;
    letter-spacing: 0.03em;
}

body[data-theme-version="dark"] .pr-app-crumb-badge {
    background: #1e3a8a !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
}

.pr-meta-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: var(--pr-surface-subtle);
    color: var(--pr-text-secondary);
    border: 1px solid var(--pr-border);
    white-space: nowrap;
}

.pr-meta-chip strong {
    color: var(--pr-text-primary);
    font-weight: 700;
}

.pr-meta-chip.chip-cust {
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.25);
}

body[data-theme-version="dark"] .pr-meta-chip.chip-cust {
    background: rgba(59, 130, 246, 0.18);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.35);
}

.pr-meta-chip.chip-cust strong {
    color: #1d4ed8;
}

body[data-theme-version="dark"] .pr-meta-chip.chip-cust strong {
    color: #93c5fd;
}

.pr-btn-outline-subtle {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0 0.85rem;
    height: 38px;
    border-radius: 8px;
    background: var(--pr-surface-subtle);
    color: var(--pr-text-secondary) !important;
    border: 1px solid var(--pr-border);
    text-decoration: none !important;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pr-btn-outline-subtle:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--pr-text-primary) !important;
    border-color: rgba(59, 130, 246, 0.4);
}

body[data-theme-version="dark"] .pr-btn-outline-subtle:hover {
    background: rgba(255, 255, 255, 0.08);
}

.pr-btn-outline-primary-custom {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0 0.85rem;
    height: 38px;
    border-radius: 8px;
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb !important;
    border: 1px solid rgba(59, 130, 246, 0.3);
    text-decoration: none !important;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pr-btn-outline-primary-custom:hover {
    background: #2563eb;
    color: #ffffff !important;
    border-color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
}

body[data-theme-version="dark"] .pr-btn-outline-primary-custom {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd !important;
    border-color: rgba(59, 130, 246, 0.35);
}

body[data-theme-version="dark"] .pr-btn-outline-primary-custom:hover {
    background: #3b82f6;
    color: #ffffff !important;
}

.pr-btn-resume {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0 0.85rem;
    height: 38px;
    border-radius: 8px;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #ffffff !important;
    border: none;
    box-shadow: 0 2px 6px rgba(124, 58, 237, 0.25);
    text-decoration: none !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    white-space: nowrap;
}

.pr-btn-resume:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(124, 58, 237, 0.4);
    color: #ffffff !important;
}

/* Executive App Quick Switcher */
.pr-app-switcher-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: var(--pr-surface-subtle);
    border: 1px solid var(--pr-border);
    border-radius: 8px;
    padding: 0 0.5rem 0 0.75rem;
    height: 38px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.pr-app-switcher-wrap:hover {
    border-color: rgba(59, 130, 246, 0.4);
}

.pr-app-switcher-wrap:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    background: var(--pr-surface);
}

.pr-app-switcher-label {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--pr-text-secondary);
    white-space: nowrap !important;
    margin: 0;
    padding: 0;
    line-height: 1;
    user-select: none;
    flex-shrink: 0;
}

.pr-app-switcher-select {
    border: none !important;
    background: transparent !important;
    color: var(--pr-text-primary) !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    font-family: inherit !important;
    padding: 0 1.6rem 0 0.35rem !important;
    min-width: 250px;
    max-width: 360px;
    height: 100% !important;
    outline: none !important;
    box-shadow: none !important;
    cursor: pointer;
    line-height: 38px !important;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23334155' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 0.25rem center !important;
    background-size: 11px 9px !important;
}

body[data-theme-version="dark"] .pr-app-switcher-select {
    color: #f1f5f9 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
}

.pr-app-switcher-select option {
    background-color: var(--pr-surface) !important;
    color: var(--pr-text-primary) !important;
    font-size: 0.8rem;
    padding: 0.4rem;
}

/* Stage 6.2 Scrutiny Validation Loop Stats */
.pr-val-stat {
    display: inline-flex;
    align-items: center;
    font-size: 0.74rem;
    font-weight: 700;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.pr-val-stat.stat-validated {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac !important;
}

.pr-val-stat.stat-uploaded {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #7dd3fc !important;
}

.pr-val-stat.stat-flagged {
    background: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fca5a5 !important;
}

.pr-val-stat.stat-pending {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

body[data-theme-version="dark"] .pr-val-stat.stat-validated {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
}

body[data-theme-version="dark"] .pr-val-stat.stat-uploaded {
    background: rgba(2, 132, 199, 0.2) !important;
    color: #7dd3fc !important;
    border-color: rgba(2, 132, 199, 0.35) !important;
}

body[data-theme-version="dark"] .pr-val-stat.stat-flagged {
    background: rgba(220, 38, 38, 0.2) !important;
    color: #fca5a5 !important;
    border-color: rgba(220, 38, 38, 0.35) !important;
}

body[data-theme-version="dark"] .pr-val-stat.stat-pending {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

/* Mines Department Validation Queue Filter Buttons */
.pr-doc-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid var(--pr-border);
    background: var(--pr-surface-subtle);
    color: var(--pr-text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.pr-doc-filter-btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--pr-text-primary);
}

body[data-theme-version="dark"] .pr-doc-filter-btn:hover {
    background: rgba(255, 255, 255, 0.08);
}

.pr-doc-filter-btn.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}

body[data-theme-version="dark"] .pr-doc-filter-btn.active {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
}

/* Audit Trail Header Badge & Phase Chips */
.pr-audit-crumb-badge {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-size: 0.74rem !important;
    font-weight: 600 !important;
    padding: 0.25rem 0.65rem !important;
    border-radius: 6px !important;
    background: var(--pr-surface-subtle) !important;
    color: var(--pr-text-secondary) !important;
    border: 1px solid var(--pr-border) !important;
    display: inline-flex !important;
    align-items: center !important;
}

.pr-audit-crumb-badge strong {
    color: var(--pr-text-primary);
    margin-left: 0.35rem;
}

.pr-audit-tag {
    font-size: 0.68rem !important;
    font-weight: 700 !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 5px !important;
    display: inline-block !important;
    margin-right: 0.35rem !important;
}

.pr-audit-tag.tag-lease {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid #cbd5e1 !important;
}

.pr-audit-tag.tag-mining {
    background: #dbeafe !important;
    color: #1e40af !important;
    border: 1px solid #bfdbfe !important;
}

body[data-theme-version="dark"] .pr-audit-tag.tag-lease {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

body[data-theme-version="dark"] .pr-audit-tag.tag-mining {
    background: rgba(37, 99, 235, 0.22) !important;
    color: #93c5fd !important;
    border-color: rgba(96, 165, 250, 0.35) !important;
}

/* Attached Statutory Files Link Chip */
.pr-file-link-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.4rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 7px;
    background: var(--pr-surface-subtle);
    color: var(--pr-text-primary);
    border: 1px solid var(--pr-border);
    text-decoration: none !important;
    transition: all 0.15s ease;
}

.pr-file-link-chip:hover {
    background: rgba(59, 130, 246, 0.08);
    border-color: rgba(59, 130, 246, 0.35);
    color: #2563eb;
}

body[data-theme-version="dark"] .pr-file-link-chip:hover {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd;
}

/* Timeline Stream */
.pr-timeline-stream {
    position: relative;
    padding-left: 1.5rem;
    border-left: 2px solid var(--pr-border);
}

.pr-timeline-node {
    position: relative;
    margin-bottom: 1.25rem;
}

.pr-timeline-dot {
    position: absolute;
    left: -1.95rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid var(--pr-surface);
}

.pr-timeline-dot.phase-lease { background-color: #64748b; }
.pr-timeline-dot.phase-mining { background-color: #3b82f6; }
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ========================================================================= --}}
        {{-- CASE 1: NO ID PROVIDED -> SHOW TABLE OF ALL APPLICATIONS WITH PROCESS STAGES --}}
        {{-- ========================================================================= --}}
        @if(!isset($application))

            @php
                $totalCount = $stats['total'] ?? $applications->total();
                $stage61Count = $stats['stage_61'] ?? $applications->where('stage', '6.1')->count();
                $stage62Count = $stats['stage_62'] ?? $applications->where('stage', '6.2')->count();
                $advancedCount = $stats['stage_advanced'] ?? $applications->where('stage', '>=', '6.3')->count();
            @endphp

            <!-- Cockpit Header -->
            <div class="pr-panel pr-header-bar">
                <div class="d-flex align-items-center gap-3">
                    <div class="pr-brand-squircle">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="pr-chip-portal">MINING PORTAL</span>
                            <span class="text-muted small">&bull;</span>
                            <span class="pr-chip-pipeline">LIFECYCLE PIPELINE</span>
                        </div>
                        <h1 class="pr-title mb-0">Stage 6.1 – 6.6 Process Flow &amp; Statutory Validation</h1>
                        <p class="pr-sub">Monitor end-to-end statutory stages, department scrutiny queues, document review loops, and MIMAS approvals.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @can('mining.create')
                        <a href="{{ route('newapplication') }}" class="pr-btn-cta">
                            <i class="fa fa-plus"></i> New Application
                        </a>
                    @endcan
                </div>
            </div>

            <!-- Bento KPI Metric Cards -->
            <div class="pr-kpi-grid">
                <!-- KPI 1: Total In Process -->
                <div class="pr-kpi-card kpi-cobalt">
                    <div class="pr-kpi-squircle">
                        <i class="bi bi-collection-fill"></i>
                    </div>
                    <div class="pr-kpi-meta">
                        <div class="pr-kpi-label">Total In Workflow</div>
                        <div class="pr-kpi-value font-monospace">{{ $totalCount }}</div>
                        <div class="pr-kpi-sub">
                            <i class="bi bi-check2-circle text-primary"></i> Active tracking
                        </div>
                    </div>
                </div>

                <!-- KPI 2: Stage 6.1 Upload -->
                <div class="pr-kpi-card kpi-purple">
                    <div class="pr-kpi-squircle">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                    </div>
                    <div class="pr-kpi-meta">
                        <div class="pr-kpi-label">Stage 6.1 &bull; Upload</div>
                        <div class="pr-kpi-value font-monospace">{{ $stage61Count }}</div>
                        <div class="pr-kpi-sub">
                            <i class="bi bi-folder text-indigo"></i> Intake compilation
                        </div>
                    </div>
                </div>

                <!-- KPI 3: Stage 6.2 Scrutiny -->
                <div class="pr-kpi-card kpi-amber">
                    <div class="pr-kpi-squircle">
                        <i class="bi bi-search"></i>
                    </div>
                    <div class="pr-kpi-meta">
                        <div class="pr-kpi-label">Stage 6.2 &bull; Scrutiny</div>
                        <div class="pr-kpi-value font-monospace">{{ $stage62Count }}</div>
                        <div class="pr-kpi-sub">
                            <i class="bi bi-arrow-repeat text-warning"></i> Department review loop
                        </div>
                    </div>
                </div>

                <!-- KPI 4: Stage 6.3 - 6.6 Validated -->
                <div class="pr-kpi-card kpi-emerald">
                    <div class="pr-kpi-squircle">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div class="pr-kpi-meta">
                        <div class="pr-kpi-label">Stage 6.3+ &bull; Validated</div>
                        <div class="pr-kpi-value font-monospace">{{ $advancedCount }}</div>
                        <div class="pr-kpi-sub">
                            <i class="bi bi-shield-check text-success"></i> Approved / Advanced
                        </div>
                    </div>
                </div>
            </div>

            <!-- Multi-Filter Toolbar & Search -->
            <div class="pr-toolbar">
                <div class="pr-search-wrap">
                    <i class="bi bi-search pr-search-icon"></i>
                    <input type="text" id="inputSearchProcess" class="pr-search-input" placeholder="Search application no, client, common ID, lease ID, district...">
                    <button type="button" id="btnClearSearch" class="pr-search-clear"><i class="bi bi-x-circle-fill"></i></button>
                </div>

                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <button type="button" class="pr-filter-pill active" data-stage="all">
                        All ({{ $applications->total() }})
                    </button>
                    <button type="button" class="pr-filter-pill" data-stage="6.1">
                        Stage 6.1 (Upload)
                    </button>
                    <button type="button" class="pr-filter-pill" data-stage="6.2">
                        Stage 6.2 (Scrutiny)
                    </button>
                    <button type="button" class="pr-filter-pill" data-stage="approved">
                        Stage 6.3+ (Approved)
                    </button>
                    <button type="button" class="pr-filter-pill pill-flagged" data-stage="flagged">
                        <i class="bi bi-exclamation-triangle me-1"></i> Flagged
                    </button>
                </div>

                <button type="button" id="btnResetAll" class="pr-btn-reset ms-auto">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </div>

            <!-- Table Panel -->
            <div class="pr-panel overflow-hidden mb-4">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--pr-surface-subtle);">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-table text-primary"></i> Applications Statutory Workflow Registry
                        </h6>
                        <small class="text-muted">Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} records</small>
                    </div>
                    <span class="pr-total-badge">
                        <i class="bi bi-collection text-primary me-1"></i> Total: <strong id="prDisplayCount" class="mx-1">{{ $applications->total() }}</strong> In Process
                    </span>
                </div>

                <div class="pr-table-wrap">
                    <table class="pr-table align-middle" id="tableProcessIndex">
                        <thead>
                            <tr>
                                <th style="width: 3%; text-align: center;">#</th>
                                <th style="width: 17%;">Application No &amp; Identifiers</th>
                                <th style="width: 17%;">Client / Enterprise</th>
                                <th style="width: 8%;">MIMAS Details</th>
                                <th style="width: 12%;">Concession &amp; Work</th>
                                <th style="width: 8%;">District</th>
                                <th style="width: 12%;">Stage &amp; Validation</th>
                                <th style="width: 8%;">Status</th>
                                <th style="width: 15%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                @php
                                    $valDocs = $app->documents->where('status', 'validated')->count();
                                    $flaggedDocs = $app->documents->where('status', 'revision_required')->count();
                                    $upDocs = $app->documents->where('status', 'uploaded')->count();
                                    $totalDocs = $app->documents->count();
                                    $appStage = (string)($app->stage ?? '6.1');
                                    $searchWords = [
                                        $app->application_no,
                                        $app->common_id ?? '',
                                        $app->customer->company_name ?? '',
                                        $app->customer->customer_name ?? '',
                                        $app->customer->mobile_num ?? '',
                                        $app->customer->secondary_mobile_num ?? '',
                                        $app->customer->mimas_number ?? '',
                                        $app->customer->mimas_status ?? '',
                                        $app->district->name ?? '',
                                        $app->taluk ?? '',
                                        $app->village ?? '',
                                        $app->leaseApplication->application_no ?? '',
                                        $app->natureOfWork->name ?? 'mining plan',
                                        $app->status ?? 'draft',
                                        'stage ' . $appStage,
                                    ];
                                    if ($app->minerals && $app->minerals->isNotEmpty()) {
                                        $searchWords[] = $app->minerals->pluck('name')->implode(' ');
                                    } elseif ($app->mineral) {
                                        $searchWords[] = $app->mineral->name;
                                    }
                                    $searchString = strtolower(implode(' ', array_filter($searchWords)));
                                @endphp
                                <tr class="pr-row" 
                                    data-stage="{{ $appStage }}" 
                                    data-status="{{ strtolower($app->status ?? 'draft') }}"
                                    data-has-flagged="{{ $flaggedDocs > 0 ? 'yes' : 'no' }}"
                                    data-search="{{ $searchString }}">
                                    
                                    {{-- Col 1: Index --}}
                                    <td class="text-center">
                                        <span class="pr-idx-badge font-monospace">{{ $loop->iteration }}</span>
                                    </td>

                                    {{-- Col 2: Application No & Tracking IDs --}}
                                    <td>
                                        <a href="{{ route('process', ['id' => $app->id]) }}" class="pr-app-link">
                                            <i class="bi bi-file-earmark-text text-primary"></i>{{ $app->application_no }}
                                        </a>
                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                            <span class="pr-common-id-chip" title="Universal Common ID">
                                                <i class="bi bi-fingerprint text-primary me-1"></i>{{ $app->common_id ?? ('GTMS-' . date('Y', strtotime($app->created_at ?? 'now')) . '-' . str_pad($app->id, 4, '0', STR_PAD_LEFT)) }}
                                            </span>
                                            @if($app->lease_application_id)
                                                <a href="{{ route('viewapplication', ['id' => $app->lease_application_id]) }}" class="pr-lease-link" title="Promoted from Lease Concession" target="_blank">
                                                    <i class="fa fa-link"></i> Lease #{{ $app->leaseApplication->application_no ?? $app->lease_application_id }}
                                                </a>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Col 3: Client / Enterprise --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pr-avatar-squircle">
                                                {{ strtoupper(substr($app->customer->company_name ?? $app->customer->customer_name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div style="min-width:0;">
                                                <div class="pr-client-name text-truncate" title="{{ $app->customer->company_name ?? $app->customer->customer_name }}">
                                                    {{ $app->customer->company_name ?? $app->customer->customer_name }}
                                                </div>
                                                <div class="pr-client-phone">
                                                    <i class="bi bi-telephone text-primary"></i>{{ $app->customer?->mobile_num ?? 'N/A' }}
                                                    @if(!empty($app->customer?->secondary_mobile_num))
                                                        <span class="badge bg-light text-primary border font-monospace" style="font-size:0.65rem;" title="Alternate Mobile">Alt: {{ $app->customer->secondary_mobile_num }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Col 4: MIMAS Details --}}
                                    <td>
                                        @if(!empty($app->customer?->mimas_number))
                                            <span class="pr-mimas-chip" title="State Mining Portal MIMAS ID">
                                                <i class="fa fa-id-badge text-primary"></i>{{ $app->customer->mimas_number }}
                                            </span>
                                        @else
                                            <span class="text-muted small">&mdash;</span>
                                        @endif

                                        @if(!empty($app->customer?->mimas_status))
                                            @php
                                                $mStatus = strtolower(trim($app->customer->mimas_status));
                                                $mBadgeStyle = match(true) {
                                                    str_contains($mStatus, 'approv') => 'background:#dcfce7; color:#15803d; border:1px solid #86efac;',
                                                    str_contains($mStatus, 'reject') => 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;',
                                                    str_contains($mStatus, 'pend') => 'background:#fef3c7; color:#b45309; border:1px solid #fcd34d;',
                                                    default => 'background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc;',
                                                };
                                            @endphp
                                            <div class="mt-1">
                                                <span class="badge" style="font-size: 0.68rem; font-weight:700; {{ $mBadgeStyle }}">
                                                    {{ $app->customer->mimas_status }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Col 5: Mineral & Work --}}
                                    <td>
                                        <div class="pr-mineral-badge">
                                            <i class="bi bi-gem text-primary"></i>
                                            <span class="text-truncate">
                                                @if($app->minerals && $app->minerals->isNotEmpty())
                                                    {{ $app->minerals->pluck('name')->implode(', ') }}
                                                @elseif($app->mineral)
                                                    {{ $app->mineral->name }}
                                                @else
                                                    Mineral N/A
                                                @endif
                                            </span>
                                        </div>
                                        <span class="pr-now-badge">
                                            @if(strcasecmp($app->natureOfWork->name ?? '', 'Others') == 0 && !empty($app->other_nature_of_work_name))
                                                Others ({{ $app->other_nature_of_work_name }})
                                            @else
                                                {{ $app->natureOfWork->name ?? 'Mining Plan' }}
                                            @endif
                                        </span>
                                    </td>

                                    {{-- Col 6: District --}}
                                    <td>
                                        <div class="fw-bold text-dark" style="font-size:0.8rem;">
                                            <i class="bi bi-geo-alt text-danger me-1"></i>{{ $app->district->name ?? 'N/A' }}
                                        </div>
                                        @if($app->taluk || $app->village)
                                            <div class="text-muted" style="font-size:0.7rem;">{{ $app->taluk ?? $app->village }}</div>
                                        @endif
                                    </td>

                                    {{-- Col 7: Stage & Validation --}}
                                    <td>
                                        <div>
                                            <span class="pr-stage-pill font-monospace">Stage {{ $app->stage ?? '6.1' }}</span>
                                        </div>
                                        <div>
                                            @if($flaggedDocs > 0)
                                                <span class="pr-val-badge val-flagged">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>{{ $flaggedDocs }} Flagged
                                                </span>
                                            @elseif($valDocs == $totalDocs && $totalDocs > 0)
                                                <span class="pr-val-badge val-passed">
                                                    <i class="bi bi-check2-all"></i>All Validated
                                                </span>
                                            @elseif($upDocs > 0)
                                                <span class="pr-val-badge val-ready">
                                                    <i class="bi bi-eye"></i>{{ $upDocs }} Ready for Review
                                                </span>
                                            @else
                                                <span class="pr-val-badge val-progress">
                                                    <i class="bi bi-hourglass-split"></i>Upload In Progress
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Col 8: Status --}}
                                    <td>
                                        @if($app->status === 'approved')
                                            <span class="pr-status-pill status-approved">
                                                <span class="pr-pulse-dot"></span> Approved
                                            </span>
                                        @elseif($app->status === 'draft')
                                            <span class="pr-status-pill status-draft">
                                                <span class="pr-pulse-dot"></span> Draft
                                            </span>
                                        @elseif($app->status === 'scrutiny')
                                            <span class="pr-status-pill status-scrutiny">
                                                <span class="pr-pulse-dot"></span> In Scrutiny
                                            </span>
                                        @else
                                            <span class="pr-status-pill status-draft">
                                                <span class="pr-pulse-dot"></span> {{ ucfirst($app->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Col 9: Action Button --}}
                                    <td class="text-center">
                                        <a href="{{ route('process', ['id' => $app->id]) }}" class="pr-btn-workflow">
                                            <i class="bi bi-diagram-3"></i> View Workflow &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <div class="mb-2 fs-1 text-secondary"><i class="bi bi-diagram-3"></i></div>
                                        <h6 class="fw-bold text-dark">No mining applications found</h6>
                                        <p class="small text-muted mb-3">Begin by registering your first application or transitioning an approved lease concession.</p>
                                        @can('mining.create')
                                            <a href="{{ route('newapplication') }}" class="pr-btn-cta">
                                                <i class="fa fa-plus"></i> Register First Application
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="trNoMatchingRecords" style="display: none;">
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <div class="mb-2 fs-2 text-secondary"><i class="bi bi-funnel"></i></div>
                                    <h6 class="fw-bold text-dark">No matching applications found</h6>
                                    <p class="small text-muted mb-3">No applications match the selected stage filter or search keyword.</p>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnResetFilterInEmpty">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filters
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($applications->hasPages())
                    <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--pr-surface);">
                        <div class="small text-muted">
                            Showing page {{ $applications->currentPage() }} of {{ $applications->lastPage() }}
                        </div>
                        <div>
                            {{ $applications->links() }}
                        </div>
                    </div>
                @endif
            </div>

        {{-- ========================================================================= --}}
        {{-- CASE 2: ID PROVIDED -> SHOW FULL PROCESS WORKFLOW & VALIDATION LOOP DOSSIER --}}
        {{-- ========================================================================= --}}
        @else

            <!-- Executive Dossier Navigation Header -->
            <div class="pr-panel pr-header-bar">
                <div style="flex: 1 1 300px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="{{ route('process') }}" class="pr-btn-back">
                            <i class="bi bi-arrow-left"></i> Back to Process Registry
                        </a>
                        <span class="text-muted small">/</span>
                        <span class="pr-app-crumb-badge">{{ $application->application_no }}</span>
                    </div>

                    <h1 class="pr-title mb-1 d-flex align-items-center flex-wrap gap-2">
                        <span>{{ $application->customer->company_name ?? $application->customer->customer_name }}</span>
                        <span class="badge bg-primary fs-6 fw-semibold">
                            @if(strcasecmp($application->natureOfWork->name ?? '', 'Others') == 0 && !empty($application->other_nature_of_work_name))
                                Others ({{ $application->other_nature_of_work_name }})
                            @else
                                {{ $application->natureOfWork->name ?? 'Mining Application' }}
                            @endif
                        </span>
                    </h1>

                    <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                        <span class="badge text-white px-3 py-1 font-monospace fw-bold" style="background: var(--pr-navy-gradient); font-size: .8rem;">
                            <i class="bi bi-fingerprint me-1 text-warning"></i> Universal Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y', strtotime($application->created_at ?? 'now')) . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
                        </span>

                        @if($application->customer?->mimas_no)
                            <span class="pr-meta-chip chip-cust" title="Customer Unique ID">
                                <i class="bi bi-person-badge"></i> Cust ID: <strong>{{ $application->customer->mimas_no }}</strong>
                            </span>
                        @endif

                        <span class="pr-meta-chip">
                            <i class="bi bi-file-earmark-text"></i> Mining App: <strong>{{ $application->application_no }}</strong>
                        </span>

                        @if($application->lease_application_id)
                            <a href="{{ route('viewapplication', ['id' => $application->leaseApplication->id ?? $application->lease_application_id]) }}" class="pr-lease-link px-2 py-1" style="font-size: .78rem;" target="_blank">
                                <i class="fa fa-link"></i> Origin: Lease #{{ $application->leaseApplication->application_no ?? $application->lease_application_id }} ↗
                            </a>
                        @endif

                        @if($application->customer?->mimas_number)
                            <span class="pr-mimas-chip px-2 py-1" style="font-size: .78rem;" title="MIMAS Concession Number">
                                <i class="fa fa-id-badge text-primary me-1"></i> MIMAS No: <b>{{ $application->customer->mimas_number }}</b>
                            </span>
                        @endif

                        @if($application->customer?->mimas_status)
                            @php
                                $topMStatus = strtolower(trim($application->customer->mimas_status));
                                $topMBadgeStyle = match(true) {
                                    str_contains($topMStatus, 'approv') => 'background:#dcfce7; color:#15803d; border:1px solid #86efac;',
                                    str_contains($topMStatus, 'reject') => 'background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5;',
                                    str_contains($topMStatus, 'pend') => 'background:#fef3c7; color:#b45309; border:1px solid #fcd34d;',
                                    default => 'background:#e0f2fe; color:#0369a1; border:1px solid #7dd3fc;',
                                };
                            @endphp
                            <span class="badge px-2 py-1" style="font-size: .78rem; font-weight:700; {{ $topMBadgeStyle }}">
                                <i class="fa fa-tag me-1"></i> MIMAS: <b>{{ $application->customer->mimas_status }}</b>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Quick Switcher Dropdown & Nav Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(isset($allApplications) && $allApplications->count() > 1)
                        <div class="pr-app-switcher-wrap">
                            <label class="pr-app-switcher-label" for="selectSwitchApp">
                                <i class="bi bi-arrow-left-right text-primary"></i>
                                <span>Switch App:</span>
                            </label>
                            <select id="selectSwitchApp" class="pr-app-switcher-select" onchange="if(this.value) window.location.href='/process?id='+this.value;">
                                @foreach($allApplications as $optApp)
                                    <option value="{{ $optApp->id }}" {{ $optApp->id == $application->id ? 'selected' : '' }}>
                                        {{ $optApp->application_no }} &mdash; {{ $optApp->customer->company_name ?? $optApp->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if($application->status === 'draft' || $application->stage === '6.1')
                        <a href="{{ route('newapplication', ['resume' => $application->id, 'step' => $application->current_step ?? 1]) }}" class="pr-btn-resume">
                            <i class="bi bi-play-fill me-1"></i>Resume / Edit Application
                        </a>
                    @endif

                    @php
                        $linkedEnv = \App\Models\EnvironmentProject::where('mining_application_id', $application->id)->first();
                    @endphp
                    @if($linkedEnv)
                        <a href="{{ route('environment-b2.show', $linkedEnv) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center" style="height:38px;">
                            <i class="fa fa-leaf me-1"></i>Environment: {{ $linkedEnv->project_code }} ↗
                        </a>
                    @endif

                    <a href="{{ route('projectfolder', ['id' => $application->id]) }}" class="pr-btn-outline-subtle">
                        <i class="bi bi-folder2"></i> Folder Dossier
                    </a>

                    <a href="{{ route('document', ['id' => $application->id]) }}" class="pr-btn-outline-primary-custom">
                        <i class="bi bi-upload"></i> Manage Files
                    </a>
                </div>
            </div>

            {{-- PARENT LEASE APPLICATION INFORMATION CARD --}}
            @if($application->leaseApplication)
                <div class="pr-panel mb-4 overflow-hidden">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--pr-surface-subtle);">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-2 rounded bg-primary-subtle text-primary"><i class="fa fa-id-card"></i></span>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Parent Lease Concession Parameters (Origin: {{ $application->leaseApplication->application_no }})</h6>
                                <small class="text-muted">Prior statutory clearance metadata, survey bounds, and concession agreement</small>
                            </div>
                        </div>
                        <a href="{{ route('viewapplication', ['id' => $application->leaseApplication->id]) }}" target="_blank" class="btn btn-sm btn-navy">
                            <i class="fa fa-file-contract me-1"></i> View Original Lease Dossier ↗
                        </a>
                    </div>

                    <div class="p-3">
                        <div class="row g-3" style="font-size: 0.85rem;">
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Universal Common ID</span>
                                <strong class="text-primary font-monospace">{{ $application->common_id }}</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Customer Unique ID</span>
                                <strong class="text-dark font-monospace">{{ $application->customer?->mimas_no ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">MIMAS Number</span>
                                <strong class="text-dark font-monospace">
                                    @if(!empty($application->customer?->mimas_number))
                                        <i class="fa fa-id-badge text-primary me-1"></i>{{ $application->customer->mimas_number }}
                                    @else
                                        <span class="text-muted fst-italic">Not assigned</span>
                                    @endif
                                </strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">MIMAS Status</span>
                                @if(!empty($application->customer?->mimas_status))
                                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 11px;">
                                        {{ $application->customer->mimas_status }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Not specified</span>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Original Lease App No</span>
                                <strong class="text-dark font-monospace">{{ $application->leaseApplication->application_no }}</strong>
                                @if($application->leaseApplication->go_number)
                                    <div class="text-muted small">GO: {{ $application->leaseApplication->go_number }}</div>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Lease Status</span>
                                <span class="pr-status-pill status-approved">
                                    <i class="fa fa-check-circle me-1"></i> {{ ucfirst($application->leaseApplication->status) }}
                                </span>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Lease Period</span>
                                <strong class="text-dark">{{ $application->leaseApplication->lease_period_years ?? 5 }} Years</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Quarry Extent</span>
                                <strong class="text-dark">{{ $application->area_extent_ha ? ($application->area_extent_ha . ' Ha') : 'N/A' }}</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Survey &amp; Sub-div Nos</span>
                                <strong class="text-dark">{{ $application->survey_numbers_text ?: 'SF.No 1' }}</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Location</span>
                                <strong class="text-dark">{{ $application->village ?? '-' }}, {{ $application->taluk ?? '-' }} ({{ $application->district->name ?? 'N/A' }})</strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Mineral Concession</span>
                                <strong class="text-primary">
                                    @if($application->minerals && $application->minerals->isNotEmpty())
                                        {{ $application->minerals->pluck('name')->implode(', ') }}
                                    @elseif($application->mineral)
                                        {{ $application->mineral->name }}
                                    @else
                                        N/A
                                    @endif
                                    @if(!empty($application->other_mineral_name))
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1" style="font-size:.7rem;">Other: {{ $application->other_mineral_name }}</span>
                                    @endif
                                </strong>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <span class="text-muted d-block small">Primary Contact</span>
                                <strong class="text-dark">{{ $application->leaseApplication->contact_person ?? ($application->customer?->customer_name ?? 'N/A') }}</strong>
                                <div class="text-muted small"><i class="fa fa-phone me-1"></i>{{ $application->leaseApplication->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A') }}</div>
                            </div>
                        </div>

                        @php
                            $clonedDocs = $application->documents->whereNotNull('file_path');
                        @endphp
                        @if($clonedDocs->count() > 0)
                            <div class="mt-3 pt-3 border-top">
                                <span class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: .72rem; letter-spacing: .06em;">
                                    <i class="fa fa-paperclip me-1 text-primary"></i> Attached Statutory Files ({{ $clonedDocs->count() }} Available):
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($clonedDocs as $cd)
                                        <a href="{{ asset($cd->file_path) }}" target="_blank" class="pr-file-link-chip">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <span>{{ $cd->document_name }}</span>
                                            <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size:10px;"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 6-Stage Process Flow Pipeline Stepper -->
            @php
                $stage = (string)($application->stage ?? '6.1');
            @endphp
            <div class="pr-panel p-3 p-lg-4 mb-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <h6 class="fw-bold text-uppercase text-secondary mb-0" style="font-size:0.75rem; letter-spacing:0.05em;">
                        <i class="bi bi-diagram-3 text-primary me-1"></i> Statutory Lifecycle Stepper (Stages 6.1 &ndash; 6.6)
                    </h6>
                    <span class="badge bg-primary text-white font-monospace px-3 py-1">Current Active Stage: {{ $stage }}</span>
                </div>

                <div class="pr-step-track">
                    {{-- 6.1 Upload & Store --}}
                    <div class="pr-step-node {{ $stage === '6.1' ? 'active-stage' : ($stage > '6.1' ? 'done-stage' : 'pending-stage') }}">
                        <div class="pr-step-num font-monospace">6.1</div>
                        <div class="pr-step-title">Upload &amp; Store</div>
                        @if($stage > '6.1')
                            <span class="pr-step-badge"><i class="bi bi-check2"></i> Completed</span>
                        @elseif($stage === '6.1')
                            <span class="pr-step-badge"><i class="bi bi-play-circle-fill"></i> In Progress</span>
                        @else
                            <span class="pr-step-badge">Pending</span>
                        @endif
                    </div>

                    {{-- 6.2 Validate Data --}}
                    <div class="pr-step-node {{ $stage === '6.2' ? 'active-stage' : ($stage > '6.2' ? 'done-stage' : 'pending-stage') }}">
                        <div class="pr-step-num font-monospace">6.2</div>
                        <div class="pr-step-title">Validate Data</div>
                        @if($stage > '6.2')
                            <span class="pr-step-badge"><i class="bi bi-check2"></i> Validated</span>
                        @elseif($stage === '6.2')
                            <span class="pr-step-badge"><i class="bi bi-search"></i> In Scrutiny</span>
                        @else
                            <span class="pr-step-badge">Waiting</span>
                        @endif
                    </div>

                    {{-- 6.3 Approve Data --}}
                    <div class="pr-step-node {{ $stage === '6.3' ? 'active-stage' : ($stage > '6.3' ? 'done-stage' : 'pending-stage') }}">
                        <div class="pr-step-num font-monospace">6.3</div>
                        <div class="pr-step-title">Approve Data</div>
                        @if($stage > '6.3')
                            <span class="pr-step-badge"><i class="bi bi-check2"></i> Approved</span>
                        @elseif($stage === '6.3')
                            <span class="pr-step-badge"><i class="bi bi-patch-check"></i> In Review</span>
                        @else
                            <span class="pr-step-badge">Pending</span>
                        @endif
                    </div>

                    {{-- 6.4 Generate Reports --}}
                    <div class="pr-step-node {{ $stage === '6.4' ? 'active-stage' : ($stage > '6.4' ? 'done-stage' : 'pending-stage') }}">
                        <div class="pr-step-num font-monospace">6.4</div>
                        <div class="pr-step-title">Generate Reports</div>
                        @if($stage > '6.4')
                            <span class="pr-step-badge"><i class="bi bi-check2"></i> Generated</span>
                        @elseif($stage === '6.4')
                            <span class="pr-step-badge"><i class="bi bi-file-earmark-check"></i> Ready</span>
                        @else
                            <span class="pr-step-badge">Pending</span>
                        @endif
                    </div>

                    {{-- 6.5 Archive & Backup --}}
                    <div class="pr-step-node {{ $stage === '6.5' ? 'active-stage' : ($stage > '6.5' ? 'done-stage' : 'pending-stage') }}">
                        <div class="pr-step-num font-monospace">6.5</div>
                        <div class="pr-step-title">Archive &amp; Backup</div>
                        @if($stage >= '6.5')
                            <span class="pr-step-badge"><i class="bi bi-archive-fill"></i> Archived</span>
                        @else
                            <span class="pr-step-badge">Pending</span>
                        @endif
                    </div>

                    {{-- 6.6 Session Close --}}
                    <div class="pr-step-node {{ $stage === '6.6' ? 'active-stage' : 'pending-stage' }}">
                        <div class="pr-step-num font-monospace">6.6</div>
                        <div class="pr-step-title">Session Close</div>
                        <span class="pr-step-badge">Logout</span>
                    </div>
                </div>
            </div>

            <!-- Validation Loop Status & Advance Actions Card -->
            @php
                $totalDocs = count($documents);
                $isAllValidated = ($totalDocs > 0 && $validatedCount === $totalDocs);
                $hasFlags = ($rejectedCount > 0);
            @endphp

            @if($isAllValidated)
                <div class="pr-panel p-4 mb-4 text-center border-success" style="background: rgba(16, 185, 129, 0.05);">
                    <div class="text-success fs-1 mb-2">🏆</div>
                    <h4 class="fw-bold text-success mb-1">Validation Succeeded &mdash; Mining Plan Ready for Advance!</h4>
                    <p class="text-muted small mb-3">All statutory checklist documents have been reviewed and validated by the Mines Department.</p>
                    
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <form method="POST" action="{{ route('mining.application.stage', $application->id) }}">
                            @csrf
                            <input type="hidden" name="stage" value="6.3">
                            <button type="submit" class="btn btn-success fw-semibold"><i class="bi bi-patch-check me-1"></i>Approve Data (Stage 6.3)</button>
                        </form>
                        <form method="POST" action="{{ route('mining.application.stage', $application->id) }}">
                            @csrf
                            <input type="hidden" name="stage" value="6.4">
                            <button type="submit" class="btn btn-navy text-white fw-semibold"><i class="bi bi-cloud-upload me-1"></i>Final MIMAS Upload &amp; Reports (Stage 6.4)</button>
                        </form>
                        @can('mining.edit')
                            @if(!$linkedEnv)
                                <form method="POST" action="{{ route('mining.application.moveToEnvironment', $application->id) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="category" value="B2">
                                    <button type="submit" class="btn btn-navy text-white fw-semibold">
                                        <i class="bi bi-tree me-1"></i>Advance to Environment Clearance (B2)
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('environment-b2.show', $linkedEnv) }}" class="btn btn-navy text-white fw-semibold">
                                    <i class="bi bi-tree me-1"></i>View Environment Project ({{ $linkedEnv->project_code }})
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            @elseif($hasFlags)
                <div class="pr-panel p-4 mb-4 border-danger" style="background: rgba(220, 38, 38, 0.05);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-3">
                            <i class="bi bi-exclamation-octagon-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-danger mb-1">Validation Loop Triggered: Correction Required</h5>
                            <p class="text-muted small mb-0">
                                <strong>{{ $rejectedCount }}</strong> document(s) were flagged by the Mines Department. Review the feedback notes below, re-upload corrected files, and revalidate.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="pr-panel p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded bg-primary-subtle text-primary fs-4"><i class="bi bi-arrow-repeat"></i></div>
                        <div>
                            <div class="fw-bold small text-dark">Stage 6.2 Scrutiny &amp; Statutory Validation Loop</div>
                            <div class="d-flex align-items-center gap-1 flex-wrap mt-1">
                                <span class="pr-val-stat stat-validated"><i class="bi bi-check-circle-fill me-1"></i>{{ $validatedCount }} Validated</span>
                                <span class="pr-val-stat stat-uploaded"><i class="bi bi-file-earmark-arrow-up-fill me-1"></i>{{ $uploadedCount }} Uploaded</span>
                                <span class="pr-val-stat stat-flagged"><i class="bi bi-exclamation-octagon-fill me-1"></i>{{ $rejectedCount }} Flagged</span>
                                <span class="pr-val-stat stat-pending"><i class="bi bi-hourglass-split me-1"></i>{{ $pendingCount }} Pending</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('mining.application.stage', $application->id) }}" class="d-inline">
                            @csrf
                            <input type="hidden" name="stage" value="6.2">
                            <button type="submit" class="pr-btn-outline-primary-custom">
                                <i class="bi bi-search"></i> Mark in Scrutiny (Stage 6.2)
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Mines Department Validation Queue -->
            <div class="pr-panel p-3 p-lg-4 mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <div>
                        <h2 class="h5 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-clipboard2-check text-primary"></i> Mines Department Validation Queue
                        </h2>
                        <span class="text-muted small">Review each statutory checklist document and mark as Validated or Flag for Revision.</span>
                    </div>
                    <span class="badge bg-primary text-white px-3 py-2 font-monospace">
                        Total {{ $totalDocs }} Checklist Items
                    </span>
                </div>

                {{-- Document Search & Filter Toolbar --}}
                <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                    <div style="flex: 1 1 200px;">
                        <input type="text" id="inputFilterDocList" class="form-control form-control-sm" placeholder="Filter checklist documents by name or folder...">
                    </div>
                    <div class="d-flex gap-1 flex-wrap">
                        <button type="button" class="pr-doc-filter-btn active pr-doc-filter" data-doc-status="all">All ({{ $totalDocs }})</button>
                        <button type="button" class="pr-doc-filter-btn pr-doc-filter" data-doc-status="validated">Passed ({{ $validatedCount }})</button>
                        <button type="button" class="pr-doc-filter-btn pr-doc-filter" data-doc-status="uploaded">Ready ({{ $uploadedCount }})</button>
                        <button type="button" class="pr-doc-filter-btn pr-doc-filter" data-doc-status="revision_required">Flagged ({{ $rejectedCount }})</button>
                        <button type="button" class="pr-doc-filter-btn pr-doc-filter" data-doc-status="pending">Pending ({{ $pendingCount }})</button>
                    </div>
                </div>

                @if(count($documents) === 0)
                    <div class="p-4 text-center text-muted">
                        <p class="mb-0">No documents generated yet for this application.</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-1" id="listChecklistDocs">
                        @foreach($documents as $doc)
                            @php
                                $dStatus = $doc->status ?? 'pending';
                                $itemClass = match($dStatus) {
                                    'validated' => 'doc-passed',
                                    'revision_required' => 'doc-flagged',
                                    'uploaded' => 'doc-ready',
                                    default => 'doc-pending',
                                };
                            @endphp
                            <div class="pr-doc-item {{ $itemClass }}" 
                                 data-doc-name="{{ strtolower($doc->document_name . ' ' . ($doc->folder->name ?? '')) }}"
                                 data-status="{{ $dStatus }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="pr-doc-icon-box">
                                        @if($dStatus === 'validated')
                                            <i class="bi bi-check-circle-fill"></i>
                                        @elseif($dStatus === 'revision_required')
                                            <i class="bi bi-exclamation-circle-fill"></i>
                                        @elseif($dStatus === 'uploaded')
                                            <i class="bi bi-file-earmark-arrow-up-fill"></i>
                                        @else
                                            <i class="bi bi-hourglass-split"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold small text-dark">{{ $doc->document_name }}</div>
                                        <div class="text-muted" style="font-size:11.5px;">
                                            Folder: <strong>{{ $doc->folder->name ?? 'General' }}</strong> &middot; 
                                            @if($doc->file_name)
                                                File: <span class="text-primary fw-semibold">{{ $doc->file_name }}</span> &middot; 
                                                Uploaded {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : '' }}
                                            @else
                                                <span class="text-danger">PPT Documents t yet uploaded</span>
                                            @endif
                                        </div>
                                        @if($doc->review_note)
                                            <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1 text-wrap text-start" style="font-size:11px;">
                                                <i class="bi bi-chat-left-text me-1"></i> Note: {{ $doc->review_note }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    @if($dStatus === 'validated')
                                        <span class="pr-val-badge val-passed">Passed</span>
                                    @elseif($dStatus === 'revision_required')
                                        <span class="pr-val-badge val-flagged">Flagged</span>
                                    @elseif($dStatus === 'uploaded')
                                        <span class="pr-val-badge val-ready">Ready to Validate</span>
                                    @else
                                        <span class="pr-val-badge val-progress">Pending Upload</span>
                                    @endif

                                    @if($doc->file_path)
                                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Document">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    @endif

                                    @can('mining.edit')
                                        @if($doc->file_name)
                                            <!-- Pass Action -->
                                            <form method="POST" action="{{ route('mining.document.validate', $doc->id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="action" value="pass">
                                                <button type="submit" class="btn btn-sm btn-success" title="Pass / Validate">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>

                                            <!-- Reject / Flag Action -->
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-flag-mining-doc" 
                                                    data-doc-name="{{ $doc->document_name }}"
                                                    data-action-url="{{ route('mining.document.validate', $doc->id) }}"
                                                    title="Flag for Correction">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Single Dynamic Reject / Flag Modal -->
                    <div class="modal fade" id="modalRejectMiningDoc" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" id="formRejectMiningDoc" action="" class="modal-content">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <div class="modal-header">
                                    <h5 class="modal-title h6 fw-bold">Flag for Correction: <span id="spanRejectDocName" class="text-primary"></span></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <label class="form-label small fw-semibold">Review / Correction Note: <span class="text-danger">*</span></label>
                                    <textarea name="review_note" id="textRejectReviewNote" class="form-control" rows="3" required placeholder="e.g. Scan unreadable, figures mismatch, or missing endorsement stamp"></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Flag for Re-upload</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Unified Lifecycle Tracking & Audit Trail -->
            <div class="pr-panel p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-clock-history text-primary me-2"></i>Unified Lifecycle Tracking &amp; Audit Trail
                    </h6>
                    <span class="pr-audit-crumb-badge">
                        <i class="bi bi-fingerprint text-primary me-1"></i>Common ID: <strong>{{ $application->common_id ?? 'GTMS-REF' }}</strong>
                    </span>
                </div>
                @if(isset($activityLogs) && $activityLogs->count() > 0)
                    <div class="pr-timeline-stream ms-2">
                        @foreach($activityLogs as $log)
                            <div class="pr-timeline-node">
                                <div class="pr-timeline-dot {{ $log->loggable_type === 'lease_application' ? 'phase-lease' : 'phase-mining' }}"></div>
                                <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-1">
                                    <strong class="text-dark small">
                                        @if($log->loggable_type === 'lease_application')
                                            <span class="pr-audit-tag tag-lease">Lease Phase</span>
                                        @else
                                            <span class="pr-audit-tag tag-mining">Mining Phase</span>
                                        @endif
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </strong>
                                    <span class="text-muted" style="font-size:.72rem;">{{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : '' }}</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">{{ $log->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3 text-muted small">No audit events recorded yet.</div>
                @endif
            </div>

        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // -------------------------------------------------------------
    // CASE 1: Real-time search and filtering for Applications Table
    // -------------------------------------------------------------
    var currentStageFilter = 'all';

    function filterProcessTable() {
        var term = ($('#inputSearchProcess').val() || '').toLowerCase().trim();
        var visibleCount = 0;

        $('.pr-row').each(function() {
            var $row = $(this);
            var searchData = String($row.data('search') || '').toLowerCase();
            var rowStage = String($row.data('stage') || '');
            var rowStatus = String($row.data('status') || '').toLowerCase();
            var hasFlagged = $row.data('has-flagged') === 'yes';

            var matchSearch = (term === '' || searchData.indexOf(term) > -1);
            var matchStage = false;

            if (currentStageFilter === 'all') {
                matchStage = true;
            } else if (currentStageFilter === '6.1') {
                matchStage = (rowStage === '6.1');
            } else if (currentStageFilter === '6.2') {
                matchStage = (rowStage === '6.2');
            } else if (currentStageFilter === 'approved') {
                matchStage = (rowStage >= '6.3' || rowStatus === 'approved');
            } else if (currentStageFilter === 'flagged') {
                matchStage = hasFlagged;
            }

            if (matchSearch && matchStage) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });

        // Update header counter display dynamically
        $('#prDisplayCount').text(visibleCount);

        // Toggle zero matching results row
        if (visibleCount === 0) {
            $('#trNoMatchingRecords').show();
        } else {
            $('#trNoMatchingRecords').hide();
        }

        if (term.length > 0) {
            $('#btnClearSearch').show();
        } else {
            $('#btnClearSearch').hide();
        }
    }

    $('#inputSearchProcess').on('input', function() {
        filterProcessTable();
    });

    $('#btnClearSearch').on('click', function() {
        $('#inputSearchProcess').val('');
        filterProcessTable();
    });

    $('.pr-filter-pill').on('click', function() {
        $('.pr-filter-pill').removeClass('active');
        $(this).addClass('active');
        currentStageFilter = String($(this).data('stage') || 'all');
        filterProcessTable();
    });

    $('#btnResetAll, #btnResetFilterInEmpty').on('click', function() {
        $('#inputSearchProcess').val('');
        currentStageFilter = 'all';
        $('.pr-filter-pill').removeClass('active');
        $('.pr-filter-pill[data-stage="all"]').addClass('active');
        filterProcessTable();
    });

    // -------------------------------------------------------------
    // CASE 2: Real-time search & filter for Validation Queue Docs
    // -------------------------------------------------------------
    var currentDocFilter = 'all';

    function filterChecklistDocs() {
        var q = ($('#inputFilterDocList').val() || '').toLowerCase().trim();

        $('.pr-doc-item').each(function() {
            var $item = $(this);
            var docName = $item.data('doc-name') || '';
            var docStatus = $item.data('status') || '';

            var matchQ = (q === '' || docName.indexOf(q) > -1);
            var matchStatus = (currentDocFilter === 'all' || docStatus === currentDocFilter);

            if (matchQ && matchStatus) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }

    $('#inputFilterDocList').on('input', function() {
        filterChecklistDocs();
    });

    $('.pr-doc-filter').on('click', function() {
        $('.pr-doc-filter').removeClass('active');
        $(this).addClass('active');
        currentDocFilter = $(this).data('doc-status');
        filterChecklistDocs();
    });

    // -------------------------------------------------------------
    // CASE 2: Modal handler for Document Rejection / Flagging
    // -------------------------------------------------------------
    $(document).on('click', '.btn-flag-mining-doc', function(e) {
        e.preventDefault();
        var docName = $(this).data('doc-name');
        var actionUrl = $(this).data('action-url');
        $('#spanRejectDocName').text(docName);
        $('#formRejectMiningDoc').attr('action', actionUrl);
        $('#textRejectReviewNote').val('');
        var modalEl = document.getElementById('modalRejectMiningDoc');
        if (modalEl) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });
});
</script>
@endsection
