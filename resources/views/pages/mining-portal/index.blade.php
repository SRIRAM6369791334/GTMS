@extends('layouts.app')
@section('title', 'Mining Portal — Applications')
@section('main_content')

<style>
/* ==========================================================================
   MINING PORTAL DIRECTORY — ENTERPRISE DESIGN SYSTEM (UI/UX PRO MAX)
   Scoped CSS Variables for 100% Light & Dark Mode Harmonization
   ========================================================================== */
:root {
    --mining-surface: #FFFFFF;
    --mining-surface-subtle: #F8FAFC;
    --mining-border: #E2E8F0;
    --mining-border-subtle: #F1F5F9;
    --mining-text-primary: #0F172A;
    --mining-text-secondary: #475569;
    --mining-text-muted: #64748B;
    --mining-table-header-bg: #F8FAFC;
    --mining-row-hover: #F1F5F9;
    --mining-accent-blue: #2563EB;
    --mining-shadow-card: 0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
    --mining-shadow-hover: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
}

body[data-theme-version="dark"] {
    --mining-surface: #121826;
    --mining-surface-subtle: #1A2234;
    --mining-border: #232F48;
    --mining-border-subtle: #1E293B;
    --mining-text-primary: #F8FAFC;
    --mining-text-secondary: #94A3B8;
    --mining-text-muted: #64748B;
    --mining-table-header-bg: #161F33;
    --mining-row-hover: #1A243B;
    --mining-accent-blue: #3B82F6;
    --mining-shadow-card: 0 2px 8px rgba(0, 0, 0, 0.35);
    --mining-shadow-hover: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
}

/* Base Wrapper */
.mining-directory-container {
    padding-bottom: 2.5rem;
}

/* ==========================================================================
   1. EXECUTIVE COCKPIT HEADER SUITE
   ========================================================================== */
.mining-header-suite {
    background: var(--mining-surface);
    border: 1px solid var(--mining-border);
    border-radius: 16px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--mining-shadow-card);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
}

.mining-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--mining-text-muted);
    margin-bottom: 0.35rem;
}

.mining-breadcrumb a {
    color: var(--mining-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}

.mining-breadcrumb a:hover {
    color: var(--mining-accent-blue);
}

.mining-breadcrumb-divider {
    font-size: 0.75rem;
    opacity: 0.6;
}

.mining-title-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mining-brand-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1E40AF 100%);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    box-shadow: 0 6px 16px rgba(15, 30, 77, 0.25);
    flex-shrink: 0;
}

body[data-theme-version="dark"] .mining-brand-icon {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
}

.mining-heading {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--mining-text-primary);
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.mining-badge-version {
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB;
    border: 1px solid rgba(37, 99, 235, 0.2);
}

body[data-theme-version="dark"] .mining-badge-version {
    background: rgba(96, 165, 250, 0.15);
    color: #93C5FD;
    border-color: rgba(96, 165, 250, 0.3);
}

.mining-subtext {
    font-size: 0.84375rem;
    color: var(--mining-text-muted);
    margin: 0.25rem 0 0 0;
}

.btn-mining-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
    color: #FFFFFF !important;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
    white-space: nowrap;
}

.btn-mining-primary:hover {
    background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%);
    box-shadow: 0 6px 18px rgba(15, 30, 77, 0.35);
    transform: translateY(-1px);
    color: #FFFFFF !important;
}

body[data-theme-version="dark"] .btn-mining-primary {
    background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
}

body[data-theme-version="dark"] .btn-mining-primary:hover {
    background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.45);
}

/* ==========================================================================
   2. BENTO KPI METRIC CARDS
   ========================================================================== */
.mining-kpi-card {
    background: var(--mining-surface);
    border: 1px solid var(--mining-border);
    border-radius: 14px;
    padding: 1.25rem 1.35rem;
    box-shadow: var(--mining-shadow-card);
    position: relative;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.mining-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--mining-shadow-hover);
    border-color: rgba(37, 99, 235, 0.3);
}

.mining-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--kpi-accent-gradient);
}

.mining-kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    background: var(--kpi-bg);
    color: var(--kpi-color);
}

.mining-kpi-info {
    flex: 1;
    min-width: 0;
}

.mining-kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--mining-text-muted);
    margin-bottom: 0.25rem;
}

.mining-kpi-value {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--mining-text-primary);
    line-height: 1.2;
    margin: 0;
    letter-spacing: -0.02em;
}

.mining-kpi-sub {
    font-size: 0.75rem;
    color: var(--mining-text-secondary);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* Individual KPI Themes */
.mining-kpi-total {
    --kpi-accent-gradient: linear-gradient(90deg, #2563EB 0%, #60A5FA 100%);
    --kpi-bg: rgba(37, 99, 235, 0.1);
    --kpi-color: #2563EB;
}
body[data-theme-version="dark"] .mining-kpi-total {
    --kpi-bg: rgba(96, 165, 250, 0.15);
    --kpi-color: #93C5FD;
}

.mining-kpi-scrutiny {
    --kpi-accent-gradient: linear-gradient(90deg, #D97706 0%, #FBBF24 100%);
    --kpi-bg: rgba(217, 119, 6, 0.1);
    --kpi-color: #D97706;
}
body[data-theme-version="dark"] .mining-kpi-scrutiny {
    --kpi-bg: rgba(251, 191, 36, 0.15);
    --kpi-color: #FCD34D;
}

.mining-kpi-approved {
    --kpi-accent-gradient: linear-gradient(90deg, #059669 0%, #34D399 100%);
    --kpi-bg: rgba(5, 150, 105, 0.1);
    --kpi-color: #059669;
}
body[data-theme-version="dark"] .mining-kpi-approved {
    --kpi-bg: rgba(52, 211, 153, 0.15);
    --kpi-color: #6EE7B7;
}

.mining-kpi-draft {
    --kpi-accent-gradient: linear-gradient(90deg, #7C3AED 0%, #A78BFA 100%);
    --kpi-bg: rgba(124, 58, 237, 0.1);
    --kpi-color: #7C3AED;
}
body[data-theme-version="dark"] .mining-kpi-draft {
    --kpi-bg: rgba(167, 139, 250, 0.15);
    --kpi-color: #C4B5FD;
}

/* ==========================================================================
   3. SAAS DIRECTORY PANEL & TOOLBAR
   ========================================================================== */
.mining-directory-card {
    background: var(--mining-surface);
    border: 1px solid var(--mining-border);
    border-radius: 16px;
    box-shadow: var(--mining-shadow-card);
    overflow: hidden;
    margin-top: 1.5rem;
}

.mining-directory-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--mining-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--mining-surface-subtle);
}

.mining-directory-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--mining-text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

/* Segmented Quick Filter Tabs */
.mining-quick-tabs {
    display: inline-flex;
    background: var(--mining-surface);
    border: 1px solid var(--mining-border);
    padding: 3px;
    border-radius: 10px;
    gap: 3px;
}

.mining-tab-pill {
    background: transparent;
    border: none;
    color: var(--mining-text-secondary);
    font-size: 0.8125rem;
    font-weight: 600;
    padding: 0.45rem 0.85rem;
    border-radius: 7px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
}

.mining-tab-pill:hover {
    color: var(--mining-text-primary);
    background: var(--mining-surface-subtle);
}

.mining-tab-pill.active {
    background: #0F1E4D;
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

body[data-theme-version="dark"] .mining-tab-pill.active {
    background: #2563EB;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}

.mining-tab-count {
    font-size: 0.6875rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
}

.mining-tab-pill.active .mining-tab-count {
    background: rgba(255, 255, 255, 0.25);
    color: #FFFFFF;
}

body[data-theme-version="dark"] .mining-tab-count {
    background: rgba(255, 255, 255, 0.1);
}

/* Toolbar Controls */
.mining-toolbar {
    padding: 1.15rem 1.5rem;
    border-bottom: 1px solid var(--mining-border);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    background: var(--mining-surface);
}

.mining-search-wrap {
    position: relative;
    flex: 1 1 260px;
    min-width: 220px;
}

.mining-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--mining-text-muted);
    font-size: 0.875rem;
    pointer-events: none;
}

.mining-search-input {
    width: 100%;
    padding: 0.55rem 2.25rem 0.55rem 2.35rem;
    font-size: 0.875rem;
    border-radius: 9px;
    border: 1px solid var(--mining-border);
    background: var(--mining-surface-subtle);
    color: var(--mining-text-primary);
    transition: all 0.15s ease;
}

.mining-search-input:focus {
    outline: none;
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    background: var(--mining-surface);
}

.mining-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--mining-text-muted);
    cursor: pointer;
    padding: 0.2rem;
    display: none;
    font-size: 0.8rem;
}

.mining-search-clear:hover {
    color: var(--mining-text-primary);
}

.mining-filter-select {
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 0.55rem 2rem 0.55rem 0.85rem;
    border-radius: 9px;
    border: 1px solid var(--mining-border);
    background: var(--mining-surface-subtle);
    color: var(--mining-text-primary);
    cursor: pointer;
    min-width: 140px;
    max-width: 185px;
    flex: 0 0 auto;
    transition: all 0.15s ease;
}

.mining-filter-select:focus {
    outline: none;
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.btn-mining-reset {
    font-size: 0.8125rem;
    font-weight: 600;
    padding: 0.55rem 0.95rem;
    border-radius: 9px;
    border: 1px solid var(--mining-border);
    background: var(--mining-surface-subtle);
    color: var(--mining-text-secondary);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    flex: 0 0 auto;
}

.btn-mining-reset:hover {
    background: var(--mining-border);
    color: var(--mining-text-primary);
}

/* ==========================================================================
   4. DATA TABLES & ROW ERGONOMICS
   Streamlined 7-Column Layout: Fits 100% on Screen with ZERO Sideways Scroll
   ========================================================================== */
.mining-table-wrap {
    padding: 0;
    overflow-x: auto;
}

#miningTable {
    margin: 0 !important;
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
}

#miningTable thead th {
    background: var(--mining-table-header-bg) !important;
    color: var(--mining-text-muted) !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 0.85rem 0.9rem !important;
    border-bottom: 1px solid var(--mining-border) !important;
    border-top: none !important;
    white-space: nowrap;
}

#miningTable tbody td {
    padding: 0.85rem 0.9rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid var(--mining-border-subtle) !important;
    color: var(--mining-text-secondary) !important;
    font-size: 0.84375rem !important;
    background: transparent !important;
}

#miningTable tbody tr {
    transition: all 0.15s ease;
    border-left: 3px solid transparent;
}

#miningTable tbody tr:hover {
    background-color: var(--mining-row-hover) !important;
    border-left: 3px solid #3B82F6 !important;
}

/* Application No & Common ID */
.mining-ref-link {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.875rem;
    font-weight: 700;
    color: #2563EB !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    transition: color 0.15s ease;
    white-space: nowrap;
}

.mining-ref-link:hover {
    color: #1D4ED8 !important;
    text-decoration: underline;
}

body[data-theme-version="dark"] .mining-ref-link {
    color: #60A5FA !important;
}

.mining-id-chip {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.6875rem;
    font-weight: 600;
    padding: 0.12rem 0.45rem;
    border-radius: 5px;
    background: var(--mining-surface-subtle);
    border: 1px solid var(--mining-border);
    color: var(--mining-text-muted);
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

.mining-lease-link {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.71875rem;
    font-weight: 600;
    padding: 0.12rem 0.45rem;
    border-radius: 5px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB;
    border: 1px solid rgba(37, 99, 235, 0.2);
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.mining-lease-link:hover {
    background: #2563EB;
    color: #FFFFFF !important;
}

body[data-theme-version="dark"] .mining-lease-link {
    background: rgba(96, 165, 250, 0.12);
    color: #93C5FD;
    border-color: rgba(96, 165, 250, 0.3);
}

/* Client Identity Group */
.mining-client-group {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mining-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
    color: #FFFFFF;
    font-size: 0.8125rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: 0.05em;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

body[data-theme-version="dark"] .mining-avatar {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
}

.mining-client-info {
    min-width: 0;
}

.mining-client-name {
    font-weight: 700;
    color: var(--mining-text-primary);
    font-size: 0.875rem;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 190px;
}

.mining-client-meta {
    font-size: 0.75rem;
    color: var(--mining-text-muted);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
}

.mining-mimas-chip {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.6875rem;
    font-weight: 700;
    padding: 0.1rem 0.4rem;
    border-radius: 5px;
    background: var(--mining-surface-subtle);
    border: 1px solid var(--mining-border);
    color: var(--mining-text-primary);
    display: inline-flex;
    align-items: center;
    white-space: nowrap;
}

/* Location Pills */
.mining-district-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--mining-text-primary);
    white-space: nowrap;
}

.mining-sub-location {
    font-size: 0.71875rem;
    color: var(--mining-text-muted);
    margin-top: 0.2rem;
}

/* Mineral & Scheme */
.mining-mineral-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-weight: 700;
    color: var(--mining-text-primary);
    font-size: 0.8125rem;
    white-space: nowrap;
}

.mining-work-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.6875rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB;
    border: 1px solid rgba(37, 99, 235, 0.2);
    white-space: nowrap;
}

body[data-theme-version="dark"] .mining-work-badge {
    background: rgba(96, 165, 250, 0.12);
    color: #93C5FD;
    border-color: rgba(96, 165, 250, 0.25);
}

.mining-plan-type {
    font-size: 0.71875rem;
    color: var(--mining-text-muted);
}

/* Stage & Status Stack */
.mining-stage-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.71875rem;
    font-weight: 700;
    padding: 0.18rem 0.55rem;
    border-radius: 9999px;
    background: var(--mining-surface-subtle);
    border: 1px solid var(--mining-border);
    color: var(--mining-text-primary);
    white-space: nowrap;
}

.mining-stage-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #2563EB;
    display: inline-block;
}

.mining-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.71875rem;
    font-weight: 700;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.mining-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
    position: relative;
}

@keyframes status-pulse-green {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
    70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

@keyframes status-pulse-amber {
    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
    70% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

@keyframes status-pulse-violet {
    0% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.5); }
    70% { box-shadow: 0 0 0 6px rgba(124, 58, 237, 0); }
    100% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
}

.mining-status-approved {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
}
.mining-status-approved .mining-status-dot {
    background: #10B981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    animation: status-pulse-green 2s infinite;
}

.mining-status-scrutiny {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.25);
}
.mining-status-scrutiny .mining-status-dot {
    background: #F59E0B;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    animation: status-pulse-amber 2s infinite;
}

.mining-status-draft {
    background: rgba(124, 58, 237, 0.1);
    color: #7C3AED;
    border: 1px solid rgba(124, 58, 237, 0.25);
}
.mining-status-draft .mining-status-dot {
    background: #7C3AED;
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.25);
    animation: status-pulse-violet 2s infinite;
}

.mining-status-rejected {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.25);
}
.mining-status-rejected .mining-status-dot {
    background: #EF4444;
}

body[data-theme-version="dark"] .mining-status-approved {
    background: rgba(16, 185, 129, 0.15);
    color: #34D399;
}
body[data-theme-version="dark"] .mining-status-scrutiny {
    background: rgba(245, 158, 11, 0.15);
    color: #FBBF24;
}
body[data-theme-version="dark"] .mining-status-draft {
    background: rgba(124, 58, 237, 0.15);
    color: #A78BFA;
}
body[data-theme-version="dark"] .mining-status-rejected {
    background: rgba(239, 68, 68, 0.15);
    color: #F87171;
}

/* Action Buttons Dock */
.mining-action-dock {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.35rem;
    white-space: nowrap;
}

.btn-dock-resume {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.4rem 0.75rem;
    border-radius: 7px;
    background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
    color: #FFFFFF !important;
    border: none;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(109, 40, 217, 0.25);
    transition: all 0.15s ease;
}

.btn-dock-resume:hover {
    background: linear-gradient(135deg, #6D28D9 0%, #5B21B6 100%);
    box-shadow: 0 4px 10px rgba(109, 40, 217, 0.35);
    transform: translateY(-1px);
    color: #FFFFFF !important;
}

.btn-dock-icon {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.84375rem;
    transition: all 0.15s ease;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
}

.btn-dock-folders {
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB !important;
    border-color: rgba(37, 99, 235, 0.2);
}
.btn-dock-folders:hover {
    background: #2563EB;
    color: #FFFFFF !important;
    border-color: #2563EB;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .btn-dock-folders {
    background: rgba(59, 130, 246, 0.15);
    color: #93C5FD !important;
    border-color: rgba(59, 130, 246, 0.3);
}
body[data-theme-version="dark"] .btn-dock-folders:hover {
    background: #3B82F6;
    color: #FFFFFF !important;
}

.btn-dock-files {
    background: rgba(217, 119, 6, 0.08);
    color: #D97706 !important;
    border-color: rgba(217, 119, 6, 0.2);
}
.btn-dock-files:hover {
    background: #D97706;
    color: #FFFFFF !important;
    border-color: #D97706;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .btn-dock-files {
    background: rgba(245, 158, 11, 0.15);
    color: #FBBF24 !important;
    border-color: rgba(245, 158, 11, 0.3);
}
body[data-theme-version="dark"] .btn-dock-files:hover {
    background: #F59E0B;
    color: #FFFFFF !important;
}

.btn-dock-process {
    background: rgba(15, 30, 77, 0.08);
    color: #0F1E4D !important;
    border-color: rgba(15, 30, 77, 0.2);
}
.btn-dock-process:hover {
    background: #0F1E4D;
    color: #FFFFFF !important;
    border-color: #0F1E4D;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.3);
    transform: translateY(-1px);
}
body[data-theme-version="dark"] .btn-dock-process {
    background: rgba(99, 102, 241, 0.15);
    color: #A5B4FC !important;
    border-color: rgba(99, 102, 241, 0.3);
}
body[data-theme-version="dark"] .btn-dock-process:hover {
    background: #6366F1;
    color: #FFFFFF !important;
}

/* ==========================================================================
   5. CUSTOM DATATABLES FOOTER DOCK & PAGINATION
   ========================================================================== */
.mining-dt-footer {
    padding: 1.15rem 1.5rem;
    border-top: 1px solid var(--mining-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--mining-surface);
}

.mining-dt-info {
    font-size: 0.8125rem;
    color: var(--mining-text-muted);
    font-weight: 500;
}

.mining-dt-paginate {
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.mining-dt-paginate .paginate_button {
    min-width: 34px;
    height: 34px;
    padding: 0 0.65rem;
    border-radius: 8px;
    border: 1px solid var(--mining-border);
    background: var(--mining-surface);
    color: var(--mining-text-secondary) !important;
    font-size: 0.8125rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}

.mining-dt-paginate .paginate_button:hover:not(.disabled):not(.current) {
    background: var(--mining-surface-subtle);
    color: var(--mining-text-primary) !important;
    border-color: #2563EB;
}

.mining-dt-paginate .paginate_button.current {
    background: #0F1E4D !important;
    color: #FFFFFF !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}

body[data-theme-version="dark"] .mining-dt-paginate .paginate_button.current {
    background: #2563EB !important;
    color: #FFFFFF !important;
    border-color: #2563EB !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}

.mining-dt-paginate .paginate_button.disabled {
    opacity: 0.45;
    cursor: not-allowed !important;
}

/* ==========================================================================
   6. UNIVERSAL DARK MODE SCOPED OVERRIDES
   ========================================================================== */
body[data-theme-version="dark"] .content-body .card {
    background-color: var(--mining-surface) !important;
    border-color: var(--mining-border) !important;
}

body[data-theme-version="dark"] .content-body .bg-white {
    background-color: var(--mining-surface) !important;
}

body[data-theme-version="dark"] .content-body .table-light,
body[data-theme-version="dark"] .content-body .table-light th {
    background-color: var(--mining-table-header-bg) !important;
    color: var(--mining-text-muted) !important;
}

body[data-theme-version="dark"] .content-body .text-dark {
    color: var(--mining-text-primary) !important;
}

body[data-theme-version="dark"] .content-body .text-secondary {
    color: var(--mining-text-secondary) !important;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid mining-directory-container">

        <!-- 1. Executive Cockpit Header Suite -->
        <div class="mining-header-suite">
            <div>
                <div class="mining-breadcrumb">
                    <a href="{{ route('dashboard') }}"><i class="fa fa-home me-1"></i> Dashboard</a>
                    <span class="mining-breadcrumb-divider">/</span>
                    <span>Mining Department</span>
                    <span class="mining-breadcrumb-divider">/</span>
                    <span class="text-primary fw-semibold">Mining Applications</span>
                </div>
                <div class="mining-title-group">
                    <div class="mining-brand-icon">
                        <i class="fa fa-mountain"></i>
                    </div>
                    <div>
                        <h1 class="mining-heading">
                            Mining Plan Schemes &amp; Dossiers
                            <span class="mining-badge-version">v2.4 Enterprise</span>
                        </h1>
                        <p class="mining-subtext">Statutory mining scheme preparation, folder dossiers, progression stages 6.1–6.6, and technical file repository.</p>
                    </div>
                </div>
            </div>
            <div>
                @can('mining.create')
                <a href="{{ route('newapplication') }}" class="btn-mining-primary">
                    <i class="fa fa-plus"></i>
                    <span>New Application</span>
                </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10B981 !important; color: #065F46;">
                <i class="bi bi-check-circle-fill me-2 text-success fs-5 align-middle"></i>
                <span class="align-middle fw-medium">{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert" style="background: rgba(37, 99, 235, 0.1); border-left: 4px solid #2563EB !important; color: #1E40AF;">
                <i class="bi bi-info-circle-fill me-2 text-primary fs-5 align-middle"></i>
                <span class="align-middle fw-medium">{{ session('info') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 2. 4 Bento KPI Metric Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="mining-kpi-card mining-kpi-total">
                    <div class="mining-kpi-icon">
                        <i class="bi bi-folder-fill"></i>
                    </div>
                    <div class="mining-kpi-info">
                        <div class="mining-kpi-label">Total Applications</div>
                        <h3 class="mining-kpi-value">{{ $totalApplications }}</h3>
                        <div class="mining-kpi-sub">
                            <i class="fa fa-cubes text-primary" style="font-size:0.7rem;"></i>
                            <span>Registered Mining Schemes</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="mining-kpi-card mining-kpi-scrutiny">
                    <div class="mining-kpi-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="mining-kpi-info">
                        <div class="mining-kpi-label">In Scrutiny / Review</div>
                        <h3 class="mining-kpi-value">{{ $scrutinyCount }}</h3>
                        <div class="mining-kpi-sub">
                            <i class="fa fa-clock text-warning" style="font-size:0.7rem;"></i>
                            <span>Departmental Scrutiny</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="mining-kpi-card mining-kpi-approved">
                    <div class="mining-kpi-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="mining-kpi-info">
                        <div class="mining-kpi-label">Approved &amp; Completed</div>
                        <h3 class="mining-kpi-value">{{ $approvedCount }}</h3>
                        <div class="mining-kpi-sub">
                            <i class="fa fa-certificate text-success" style="font-size:0.7rem;"></i>
                            <span>Statutory Approval Orders</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="mining-kpi-card mining-kpi-draft">
                    <div class="mining-kpi-icon">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div class="mining-kpi-info">
                        <div class="mining-kpi-label">Draft Applications</div>
                        <h3 class="mining-kpi-value">{{ $draftCount }}</h3>
                        <div class="mining-kpi-sub">
                            <i class="fa fa-file-pen text-purple" style="font-size:0.7rem;"></i>
                            <span>In-Progress Intake Drafts</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. SaaS Directory Table Panel -->
        <div class="mining-directory-card">
            <!-- Directory Header with Quick Filter Tabs -->
            <div class="mining-directory-header">
                <h3 class="mining-directory-title">
                    <i class="fa fa-list-check text-primary"></i>
                    Registered Mining Dossiers
                </h3>

                <!-- Quick Filter Tabs -->
                <div class="mining-quick-tabs">
                    <button type="button" class="mining-tab-pill active" data-filter="all">
                        All <span class="mining-tab-count">{{ $totalApplications }}</span>
                    </button>
                    <button type="button" class="mining-tab-pill" data-filter="approved">
                        Approved <span class="mining-tab-count">{{ $approvedCount }}</span>
                    </button>
                    <button type="button" class="mining-tab-pill" data-filter="scrutiny">
                        In Scrutiny <span class="mining-tab-count">{{ $scrutinyCount }}</span>
                    </button>
                    <button type="button" class="mining-tab-pill" data-filter="draft">
                        Drafts <span class="mining-tab-count">{{ $draftCount }}</span>
                    </button>
                </div>
            </div>

            <!-- Multi-Filter Toolbar -->
            <div class="mining-toolbar">
                <!-- Instant Live Search -->
                <div class="mining-search-wrap">
                    <i class="fa fa-search mining-search-icon"></i>
                    <input type="text" id="mining_table_search" class="mining-search-input" placeholder="Search application no, client, district, mineral, stage...">
                    <button type="button" id="mining_search_clear" class="mining-search-clear" title="Clear Search">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>

                <!-- District Dropdown -->
                <select id="filter_district" class="mining-filter-select">
                    <option value="">All Districts</option>
                    @if(isset($districts))
                        @foreach($districts as $d)
                            <option value="{{ $d->name }}">{{ $d->name }}</option>
                        @endforeach
                    @endif
                </select>

                <!-- Nature of Work Dropdown -->
                <select id="filter_now" class="mining-filter-select">
                    <option value="">All Nature of Work</option>
                    @if(isset($natureOfWorks))
                        @foreach($natureOfWorks as $now)
                            <option value="{{ $now->name }}">{{ $now->name }}</option>
                        @endforeach
                    @endif
                </select>

                <!-- Status Dropdown -->
                <select id="filter_status" class="mining-filter-select">
                    <option value="">All Statuses</option>
                    <option value="Approved">Approved</option>
                    <option value="In Scrutiny">In Scrutiny</option>
                    <option value="Draft">Draft</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btn_reset_filters" class="btn-mining-reset" title="Reset all search filters">
                    <i class="fa fa-rotate-left"></i>
                    <span>Reset</span>
                </button>

                <!-- Page Length Dropdown -->
                <div class="ms-auto">
                    <select id="mining_page_length" class="mining-filter-select" style="min-width: 105px; max-width: 120px;">
                        <option value="10" selected>10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>

            <!-- Table View (Streamlined 7-Column Ergonomics: Fits 100% on Screen with ZERO Sideways Scroll) -->
            <div class="mining-table-wrap">
                <table id="miningTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>Application No</th>
                            <th>Client &amp; Licensee</th>
                            <th>Location</th>
                            <th>Mineral &amp; Scheme</th>
                            <th>Stage &amp; Status</th>
                            <th class="text-end" style="min-width: 175px; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        @php
                            $clientName = $app->customer?->company_name ?? ($app->customer?->customer_name ?? 'Applicant');
                            $initials = strtoupper(substr($clientName, 0, 2));
                            $mobileNum = $app->customer?->mobile_num ?? 'N/A';
                            $districtName = $app->district->name ?? 'N/A';
                            $workName = $app->natureOfWork->name ?? 'Mining Plan';
                            if (strcasecmp($workName, 'Others') === 0 && !empty($app->other_nature_of_work_name)) {
                                $workName = 'Others (' . $app->other_nature_of_work_name . ')';
                            }
                            $mNo = $app->customer?->mimas_no ?? $app->customer?->mimas_number;
                            
                            $mineralDisplay = '';
                            if ($app->minerals && $app->minerals->isNotEmpty()) {
                                $mineralDisplay = $app->minerals->pluck('name')->implode(', ');
                            } elseif ($app->mineral) {
                                $mineralDisplay = $app->mineral->name;
                            } else {
                                $mineralDisplay = 'N/A';
                            }
                        @endphp
                        <tr id="mining_row_{{ $app->id }}">
                            <td class="text-muted fw-semibold">{{ $loop->iteration }}</td>
                            <td>
                                <div>
                                    @if($app->status === 'draft')
                                        <a href="{{ route('newapplication', ['resume' => $app->id, 'step' => $app->current_step ?? 1]) }}" class="mining-ref-link" title="Resume Application Form">
                                            <i class="fa fa-file-contract text-warning" style="font-size:0.8rem;"></i>
                                            <span>{{ $app->application_no }}</span>
                                        </a>
                                    @else
                                        <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="mining-ref-link" title="Open Project Folder">
                                            <i class="fa fa-file-contract text-primary" style="font-size:0.8rem;"></i>
                                            <span>{{ $app->application_no }}</span>
                                        </a>
                                    @endif
                                </div>
                                @if($app->common_id)
                                    <div class="mt-1">
                                        <span class="mining-id-chip" title="Universal Common ID">
                                            <i class="fa fa-fingerprint me-1 opacity-75"></i>{{ $app->common_id }}
                                        </span>
                                    </div>
                                @endif
                                @if($app->leaseApplication)
                                    <div class="mt-1">
                                        <a href="{{ route('viewapplication', ['id' => $app->lease_application_id]) }}" class="mining-lease-link" title="Originated from Lease Application {{ $app->leaseApplication->application_no }}">
                                            <i class="fa fa-link me-1"></i>Lease #{{ $app->leaseApplication->application_no }}
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="mining-client-group">
                                    <div class="mining-avatar">{{ $initials }}</div>
                                    <div class="mining-client-info">
                                        <div class="mining-client-name" title="{{ $clientName }}">{{ $clientName }}</div>
                                        <div class="mining-client-meta">
                                            <span><i class="fa fa-phone text-muted me-1" style="font-size:0.7rem;"></i>{{ $mobileNum }}</span>
                                            @if(!empty($mNo))
                                                <span class="mining-mimas-chip" title="MIMAS ID">
                                                    <i class="fa fa-id-badge text-primary me-1"></i>{{ $mNo }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="mining-district-pill">
                                        <i class="fa fa-location-dot text-danger" style="font-size:0.75rem;"></i>
                                        {{ $districtName }}
                                    </span>
                                </div>
                                @if($app->taluk || $app->village)
                                    <div class="mining-sub-location">
                                        {{ $app->taluk }}{{ $app->village ? ', ' . $app->village : '' }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="mining-mineral-badge">
                                    <i class="fa fa-gem text-info me-1" style="font-size:0.75rem;"></i>
                                    {{ $mineralDisplay }}
                                </div>
                                <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                    <span class="mining-work-badge">
                                        {{ $workName }}
                                    </span>
                                    @if($app->planType)
                                        <span class="mining-plan-type">• {{ $app->planType->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <span class="mining-stage-pill" title="Stage {{ $app->stage }}">
                                        <span class="mining-stage-dot"></span>
                                        Stage {{ $app->stage }}
                                    </span>
                                </div>
                                <div>
                                    @if($app->status === 'approved')
                                        <span class="mining-status-badge mining-status-approved">
                                            <span class="mining-status-dot"></span> Approved
                                        </span>
                                    @elseif($app->status === 'scrutiny' || $app->status === 'inspection' || $app->status === 'presentation')
                                        <span class="mining-status-badge mining-status-scrutiny">
                                            <span class="mining-status-dot"></span> In Scrutiny
                                        </span>
                                    @elseif($app->status === 'draft')
                                        <span class="mining-status-badge mining-status-draft">
                                            <span class="mining-status-dot"></span> Draft
                                        </span>
                                    @elseif($app->status === 'rejected')
                                        <span class="mining-status-badge mining-status-rejected">
                                            <span class="mining-status-dot"></span> Rejected
                                        </span>
                                    @else
                                        <span class="mining-status-badge mining-status-approved">
                                            <span class="mining-status-dot"></span> {{ ucfirst($app->status) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-end" style="min-width: 175px; white-space: nowrap;">
                                <div class="mining-action-dock">
                                    @if($app->status === 'draft')
                                        <a href="{{ route('newapplication', ['resume' => $app->id, 'step' => $app->current_step ?? 1]) }}" class="btn-dock-resume" title="Resume Application Form" data-bs-toggle="tooltip">
                                            <i class="fa fa-play" style="font-size: 8px;"></i>
                                            <span>Resume</span>
                                        </a>
                                    @else
                                        <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="btn-dock-icon btn-dock-folders" title="Statutory Folders (6 Folders)" data-bs-toggle="tooltip">
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                        <a href="{{ route('document', ['id' => $app->id]) }}" class="btn-dock-icon btn-dock-files" title="Manage Files &amp; Uploads" data-bs-toggle="tooltip">
                                            <i class="bi bi-cloud-arrow-up"></i>
                                        </a>
                                        <a href="{{ route('process', ['id' => $app->id]) }}" class="btn-dock-icon btn-dock-process" title="Process Flow (Stages 6.1 - 6.6)" data-bs-toggle="tooltip">
                                            <i class="bi bi-diagram-3"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <div class="mb-3">
                                        <span class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%; background: var(--mining-surface-subtle); border: 1px solid var(--mining-border);">
                                            <i class="bi bi-folder-x fa-2x text-muted opacity-50"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-dark fw-bold mb-1">No Mining Applications Found</h5>
                                    <p class="text-muted small mb-3">There are no mining dossiers registered yet or matching your active filters.</p>
                                    @can('mining.create')
                                    <a href="{{ route('newapplication') }}" class="btn-mining-primary">
                                        <i class="fa fa-plus me-1"></i> Create First Application
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom DataTables Footer Dock -->
            <div id="mining_dt_footer_container"></div>
        </div>

    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ==========================================================================
    // 1. SAFE DATATABLE INITIALIZATION (STREAMLINED 7 COLUMNS)
    // ==========================================================================
    if ($.fn.DataTable.isDataTable('#miningTable')) {
        $('#miningTable').DataTable().destroy();
    }

    var miningTable = $('#miningTable').DataTable({
        paging: true,
        pageLength: 10,
        searching: true,
        ordering: true,
        order: [[0, 'asc']],
        info: true,
        responsive: false,
        autoWidth: false,
        dom: 't',
        language: {
            zeroRecords: '<div class="text-center py-5"><i class="fa fa-search fa-2x text-muted opacity-50 mb-2 d-block"></i><span class="text-muted fw-semibold">No matching mining records found</span></div>',
            info: 'Showing _START_ to _END_ of _TOTAL_ applications',
            infoEmpty: 'Showing 0 to 0 of 0 applications',
            infoFiltered: '(filtered from _MAX_ total)',
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        },
        drawCallback: function(settings) {
            renderCustomFooter(this.api());
            initTooltips();
        }
    });

    // ==========================================================================
    // 2. CUSTOM FOOTER DOCK RENDERING
    // ==========================================================================
    function renderCustomFooter(api) {
        var info = api.page.info();
        if (!info) return;

        var totalPages = info.pages || 1;
        var currentPage = info.page; // 0-indexed

        var html = '<div class="mining-dt-footer">';
        html += '<div class="mining-dt-info">';
        if (info.recordsDisplay > 0) {
            html += 'Showing <span class="fw-bold text-dark">' + (info.start + 1) + '</span> to <span class="fw-bold text-dark">' + info.end + '</span> of <span class="fw-bold text-primary">' + info.recordsDisplay + '</span> applications';
            if (info.recordsDisplay !== info.recordsTotal) {
                html += ' <span class="text-muted">(filtered from ' + info.recordsTotal + ' total)</span>';
            }
        } else {
            html += 'Showing 0 to 0 of 0 applications';
        }
        html += '</div>';

        // Pagination buttons
        html += '<div class="mining-dt-paginate">';
        
        // Prev button
        var prevDisabled = currentPage === 0 ? ' disabled' : '';
        html += '<button type="button" class="paginate_button prev' + prevDisabled + '" data-page="' + (currentPage - 1) + '"' + (currentPage === 0 ? ' disabled' : '') + '><i class="fa fa-angle-left"></i></button>';

        // Page numbers
        for (var i = 0; i < totalPages; i++) {
            if (totalPages > 7) {
                if (i !== 0 && i !== totalPages - 1 && Math.abs(i - currentPage) > 2) {
                    if (Math.abs(i - currentPage) === 3) {
                        html += '<span class="px-1 text-muted">...</span>';
                    }
                    continue;
                }
            }
            var activeClass = i === currentPage ? ' current' : '';
            html += '<button type="button" class="paginate_button' + activeClass + '" data-page="' + i + '">' + (i + 1) + '</button>';
        }

        // Next button
        var nextDisabled = (currentPage >= totalPages - 1 || totalPages === 0) ? ' disabled' : '';
        html += '<button type="button" class="paginate_button next' + nextDisabled + '" data-page="' + (currentPage + 1) + '"' + (currentPage >= totalPages - 1 ? ' disabled' : '') + '><i class="fa fa-angle-right"></i></button>';

        html += '</div></div>';

        $('#mining_dt_footer_container').html(html);

        // Bind pagination clicks
        $('#mining_dt_footer_container .paginate_button:not(.disabled)').off('click').on('click', function(e) {
            e.preventDefault();
            var targetPage = parseInt($(this).data('page'));
            if (!isNaN(targetPage)) {
                api.page(targetPage).draw('page');
            }
        });
    }

    // ==========================================================================
    // 3. TOOLBAR CONTROLS & INSTANT SEARCH
    // ==========================================================================
    var searchTimer = null;
    $('#mining_table_search').on('keyup input', function() {
        var query = $(this).val();
        if (query.length > 0) {
            $('#mining_search_clear').show();
        } else {
            $('#mining_search_clear').hide();
        }
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            miningTable.search(query).draw();
        }, 150);
    });

    $('#mining_search_clear').on('click', function() {
        $('#mining_table_search').val('');
        $(this).hide();
        miningTable.search('').draw();
    });

    // District Filter (Column 3: Location)
    $('#filter_district').on('change', function() {
        var val = $(this).val();
        miningTable.column(3).search(val ? val : '', true, false).draw();
    });

    // Nature of Work Filter (Column 4: Mineral & Scheme)
    $('#filter_now').on('change', function() {
        var val = $(this).val();
        miningTable.column(4).search(val ? val : '', true, false).draw();
    });

    // Status Filter (Column 5: Stage & Status)
    $('#filter_status').on('change', function() {
        var val = $(this).val();
        miningTable.column(5).search(val ? val : '', true, false).draw();
        syncTabState(val);
    });

    // Page Length Select
    $('#mining_page_length').on('change', function() {
        var len = parseInt($(this).val());
        miningTable.page.len(len).draw();
    });

    // ==========================================================================
    // 4. SEGMENTED QUICK FILTER TABS
    // ==========================================================================
    $('.mining-tab-pill').on('click', function(e) {
        e.preventDefault();
        $('.mining-tab-pill').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).data('filter');
        var statusSelectVal = '';

        if (filter === 'approved') {
            statusSelectVal = 'Approved';
        } else if (filter === 'scrutiny') {
            statusSelectVal = 'In Scrutiny';
        } else if (filter === 'draft') {
            statusSelectVal = 'Draft';
        } else {
            statusSelectVal = '';
        }

        $('#filter_status').val(statusSelectVal);
        miningTable.column(5).search(statusSelectVal ? statusSelectVal : '', true, false).draw();
    });

    function syncTabState(statusVal) {
        $('.mining-tab-pill').removeClass('active');
        if (!statusVal) {
            $('.mining-tab-pill[data-filter="all"]').addClass('active');
        } else if (statusVal === 'Approved') {
            $('.mining-tab-pill[data-filter="approved"]').addClass('active');
        } else if (statusVal === 'In Scrutiny') {
            $('.mining-tab-pill[data-filter="scrutiny"]').addClass('active');
        } else if (statusVal === 'Draft') {
            $('.mining-tab-pill[data-filter="draft"]').addClass('active');
        }
    }

    // ==========================================================================
    // 5. RESET ALL FILTERS
    // ==========================================================================
    $('#btn_reset_filters').on('click', function() {
        $('#mining_table_search').val('');
        $('#mining_search_clear').hide();
        $('#filter_district').val('');
        $('#filter_now').val('');
        $('#filter_status').val('');
        $('.mining-tab-pill').removeClass('active');
        $('.mining-tab-pill[data-filter="all"]').addClass('active');

        miningTable.search('')
            .column(3).search('')
            .column(4).search('')
            .column(5).search('')
            .draw();
    });

    // ==========================================================================
    // 6. TOOLTIP INITIALIZATION
    // ==========================================================================
    function initTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }

    initTooltips();
});
</script>
@endsection
