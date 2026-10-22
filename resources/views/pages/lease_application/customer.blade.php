@extends('layouts.app')
@section('title', 'Lease Applications Management')
@section('main_content')

<style>
/* ==========================================================================
   LEASE APPLICATIONS DIRECTORY — DESIGN TOKENS & SAAS ARCHITECTURE
   ========================================================================== */
:root {
    --app-surface: #FFFFFF;
    --app-surface-subtle: #F8FAFC;
    --app-border: #E2E8F0;
    --app-border-subtle: #F1F5F9;
    --app-text-primary: #0F172A;
    --app-text-secondary: #475569;
    --app-text-muted: #94A3B8;
    --app-table-header-bg: #F8FAFC;
    --app-row-hover: rgba(37, 99, 235, 0.03);
    --app-primary: #0F1E4D;
    --app-primary-hover: #1E3A8A;
    --app-accent-blue: #2563EB;
    --app-shadow-card: 0 4px 14px 0 rgba(15, 23, 42, 0.05);
    --app-shadow-hover: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
}

body[data-theme-version="dark"] {
    --app-surface: #121826;
    --app-surface-subtle: #1A2234;
    --app-border: #232F48;
    --app-border-subtle: #1E293B;
    --app-text-primary: #F8FAFC;
    --app-text-secondary: #CBD5E1;
    --app-text-muted: #64748B;
    --app-table-header-bg: #151D2E;
    --app-row-hover: rgba(59, 130, 246, 0.08);
    --app-primary: #3B82F6;
    --app-primary-hover: #60A5FA;
    --app-accent-blue: #60A5FA;
    --app-shadow-card: 0 4px 14px 0 rgba(0, 0, 0, 0.35);
    --app-shadow-hover: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
}

/* Base Wrapper */
.app-directory-container {
    padding-bottom: 2.5rem;
}

/* ==========================================================================
   1. EXECUTIVE COCKPIT HEADER SUITE
   ========================================================================== */
.app-header-suite {
    background: var(--app-surface);
    border: 1px solid var(--app-border);
    border-radius: 16px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--app-shadow-card);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
}

.app-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--app-text-muted);
    margin-bottom: 0.35rem;
}

.app-breadcrumb a {
    color: var(--app-text-muted);
    text-decoration: none;
    transition: color 0.15s ease;
}

.app-breadcrumb a:hover {
    color: var(--app-accent-blue);
}

.app-breadcrumb-divider {
    font-size: 0.75rem;
    opacity: 0.6;
}

.app-title-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.app-brand-icon {
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

body[data-theme-version="dark"] .app-brand-icon {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
}

.app-heading {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--app-text-primary);
    margin: 0;
    letter-spacing: -0.02em;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

.app-badge-version {
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

body[data-theme-version="dark"] .app-badge-version {
    background: rgba(96, 165, 250, 0.15);
    color: #93C5FD;
    border-color: rgba(96, 165, 250, 0.3);
}

.app-subtext {
    font-size: 0.875rem;
    color: var(--app-text-secondary);
    margin: 0.25rem 0 0 0;
}

/* Primary Action Button */
.btn-app-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
    color: #FFFFFF !important;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.75rem 1.35rem;
    border-radius: 10px;
    border: none;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.btn-app-primary:hover {
    background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
    transform: translateY(-1px);
    color: #FFFFFF !important;
}

body[data-theme-version="dark"] .btn-app-primary {
    background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%);
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
}

body[data-theme-version="dark"] .btn-app-primary:hover {
    background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.45);
}

/* ==========================================================================
   2. BENTO KPI METRIC CARDS
   ========================================================================== */
.app-kpi-card {
    background: var(--app-surface);
    border: 1px solid var(--app-border);
    border-radius: 14px;
    padding: 1.25rem 1.35rem;
    box-shadow: var(--app-shadow-card);
    position: relative;
    overflow: hidden;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    height: 100%;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.app-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--app-shadow-hover);
    border-color: rgba(37, 99, 235, 0.3);
}

.app-kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--kpi-accent-gradient);
}

.app-kpi-icon {
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

.app-kpi-info {
    flex: 1;
    min-width: 0;
}

.app-kpi-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--app-text-muted);
    margin-bottom: 0.25rem;
}

.app-kpi-value {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--app-text-primary);
    line-height: 1.2;
    margin: 0;
    letter-spacing: -0.02em;
}

.app-kpi-sub {
    font-size: 0.75rem;
    color: var(--app-text-secondary);
    margin-top: 0.2rem;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* KPI Accent Variants */
.kpi-total {
    --kpi-accent-gradient: linear-gradient(90deg, #3B82F6 0%, #1D4ED8 100%);
    --kpi-bg: rgba(59, 130, 246, 0.1);
    --kpi-color: #2563EB;
}
.kpi-validation {
    --kpi-accent-gradient: linear-gradient(90deg, #F59E0B 0%, #D97706 100%);
    --kpi-bg: rgba(245, 158, 11, 0.1);
    --kpi-color: #D97706;
}
.kpi-approved {
    --kpi-accent-gradient: linear-gradient(90deg, #10B981 0%, #059669 100%);
    --kpi-bg: rgba(16, 185, 129, 0.1);
    --kpi-color: #059669;
}
.kpi-review {
    --kpi-accent-gradient: linear-gradient(90deg, #EF4444 0%, #DC2626 100%);
    --kpi-bg: rgba(239, 68, 68, 0.1);
    --kpi-color: #DC2626;
}

body[data-theme-version="dark"] .kpi-total {
    --kpi-bg: rgba(59, 130, 246, 0.15);
    --kpi-color: #60A5FA;
}
body[data-theme-version="dark"] .kpi-validation {
    --kpi-bg: rgba(245, 158, 11, 0.15);
    --kpi-color: #FBBF24;
}
body[data-theme-version="dark"] .kpi-approved {
    --kpi-bg: rgba(16, 185, 129, 0.15);
    --kpi-color: #34D399;
}
body[data-theme-version="dark"] .kpi-review {
    --kpi-bg: rgba(239, 68, 68, 0.15);
    --kpi-color: #F87171;
}

/* ==========================================================================
   3. SAAS DIRECTORY PANEL & TOOLBAR
   ========================================================================== */
.app-directory-card {
    background: var(--app-surface);
    border: 1px solid var(--app-border);
    border-radius: 16px;
    box-shadow: var(--app-shadow-card);
    overflow: hidden;
    margin-top: 1.5rem;
}

.app-directory-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--app-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--app-surface-subtle);
}

.app-directory-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: var(--app-text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.65rem;
}

/* Segmented Quick Filter Tabs */
.app-quick-tabs {
    display: inline-flex;
    background: var(--app-surface);
    border: 1px solid var(--app-border);
    padding: 3px;
    border-radius: 10px;
    gap: 3px;
}

.app-tab-pill {
    background: transparent;
    border: none;
    color: var(--app-text-secondary);
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

.app-tab-pill:hover {
    color: var(--app-text-primary);
    background: var(--app-surface-subtle);
}

.app-tab-pill.active {
    background: #0F1E4D;
    color: #FFFFFF !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

body[data-theme-version="dark"] .app-tab-pill.active {
    background: #2563EB;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}

.app-tab-count {
    font-size: 0.6875rem;
    font-weight: 700;
    padding: 0.15rem 0.45rem;
    border-radius: 9999px;
    background: rgba(0, 0, 0, 0.08);
}

.app-tab-pill.active .app-tab-count {
    background: rgba(255, 255, 255, 0.25);
    color: #FFFFFF;
}

body[data-theme-version="dark"] .app-tab-count {
    background: rgba(255, 255, 255, 0.1);
}

/* Toolbar Controls */
.app-toolbar {
    padding: 1.15rem 1.5rem;
    border-bottom: 1px solid var(--app-border);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
    background: var(--app-surface);
}

.app-search-wrap {
    position: relative;
    flex: 1 1 260px;
    min-width: 220px;
}

.app-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--app-text-muted);
    font-size: 0.875rem;
    pointer-events: none;
}

.app-search-input {
    width: 100%;
    padding: 0.55rem 2.25rem 0.55rem 2.35rem;
    font-size: 0.875rem;
    border-radius: 9px;
    border: 1px solid var(--app-border);
    background: var(--app-surface-subtle);
    color: var(--app-text-primary);
    transition: all 0.15s ease;
}

.app-search-input:focus {
    outline: none;
    background: var(--app-surface);
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.app-search-clear {
    position: absolute;
    right: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--app-text-muted);
    cursor: pointer;
    padding: 0.2rem;
    display: none;
    font-size: 0.8rem;
}

.app-search-clear:hover {
    color: var(--app-text-primary);
}

.app-filter-select {
    font-size: 0.8125rem;
    font-weight: 500;
    padding: 0.55rem 2rem 0.55rem 0.85rem;
    border-radius: 9px;
    border: 1px solid var(--app-border);
    background: var(--app-surface-subtle);
    color: var(--app-text-primary);
    cursor: pointer;
    min-width: 145px;
    max-width: 190px;
    flex: 0 0 auto;
    transition: all 0.15s ease;
}

.app-filter-select:focus {
    outline: none;
    border-color: #2563EB;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.btn-app-reset {
    font-size: 0.8125rem;
    font-weight: 600;
    padding: 0.55rem 0.95rem;
    border-radius: 9px;
    border: 1px solid var(--app-border);
    background: var(--app-surface-subtle);
    color: var(--app-text-secondary);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: all 0.15s ease;
    flex: 0 0 auto;
}

.btn-app-reset:hover {
    background: var(--app-border);
    color: var(--app-text-primary);
}

/* ==========================================================================
   4. DATA TABLES & ROW ERGONOMICS
   ========================================================================== */
.app-table-wrap {
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
    background: var(--app-table-header-bg) !important;
    color: var(--app-text-muted) !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 0.85rem 0.75rem !important;
    border-bottom: 1px solid var(--app-border) !important;
    border-top: none !important;
    white-space: nowrap;
}

#example10 tbody td {
    padding: 0.85rem 0.75rem !important;
    vertical-align: middle !important;
    border-bottom: 1px solid var(--app-border-subtle) !important;
    color: var(--app-text-secondary) !important;
    font-size: 0.84375rem !important;
    background: transparent !important;
}

#example10 tbody tr {
    transition: all 0.15s ease;
    border-left: 3px solid transparent;
}

#example10 tbody tr:hover {
    background-color: var(--app-row-hover) !important;
    border-left: 3px solid #3B82F6 !important;
}

/* Application No Monospace Badge */
.app-ref-link {
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 0.875rem;
    font-weight: 700;
    color: #2563EB !important;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    transition: color 0.15s ease;
}

.app-ref-link:hover {
    color: #1D4ED8 !important;
    text-decoration: underline;
}

body[data-theme-version="dark"] .app-ref-link {
    color: #60A5FA !important;
}

body[data-theme-version="dark"] .app-ref-link:hover {
    color: #93C5FD !important;
}

.app-extent-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.71875rem;
    font-weight: 600;
    padding: 0.15rem 0.45rem;
    border-radius: 6px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB;
}

body[data-theme-version="dark"] .app-extent-chip {
    background: rgba(96, 165, 250, 0.12);
    color: #93C5FD;
}

.app-mineral-chip {
    font-size: 0.65rem;
    font-weight: 600;
    padding: 0.18rem 0.45rem;
    border-radius: 6px;
    background: rgba(15, 30, 77, 0.06);
    color: var(--app-text-secondary);
    border: 1px solid var(--app-border);
}

body[data-theme-version="dark"] .app-mineral-chip {
    background: rgba(255, 255, 255, 0.06);
    color: var(--app-text-secondary);
    border-color: var(--app-border);
}

/* Client Identity Cell */
.app-client-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.app-avatar-squircle {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    background: linear-gradient(135deg, #0F1E4D 0%, #2563EB 100%);
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 3px 8px rgba(15, 30, 77, 0.15);
}

body[data-theme-version="dark"] .app-avatar-squircle {
    background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 100%);
}

.app-client-name {
    font-weight: 700;
    color: var(--app-text-primary);
    font-size: 0.875rem;
    line-height: 1.3;
}

.app-client-meta {
    font-size: 0.75rem;
    color: var(--app-text-secondary);
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.4rem;
    margin-top: 0.2rem;
}

.app-alt-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.1rem 0.35rem;
    border-radius: 4px;
    background: rgba(99, 102, 241, 0.12);
    color: #4F46E5;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

body[data-theme-version="dark"] .app-alt-badge {
    background: rgba(129, 140, 248, 0.15);
    color: #A5B4FC;
    border-color: rgba(129, 140, 248, 0.3);
}

/* District & Category Chips */
.app-district-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.3rem 0.65rem;
    border-radius: 7px;
    background: var(--app-surface-subtle);
    border: 1px solid var(--app-border);
    color: var(--app-text-secondary);
}

.app-cat-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.55rem;
    border-radius: 6px;
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB;
    border: 1px solid rgba(37, 99, 235, 0.2);
}

body[data-theme-version="dark"] .app-cat-badge {
    background: rgba(96, 165, 250, 0.12);
    color: #93C5FD;
    border-color: rgba(96, 165, 250, 0.25);
}

/* Document Vault Progress Pill */
.app-vault-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.35rem 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.15s ease;
    border: 1px solid var(--app-border);
}

.app-vault-pill.has-docs {
    background: rgba(16, 185, 129, 0.08);
    color: #059669;
    border-color: rgba(16, 185, 129, 0.25);
}

.app-vault-pill.no-docs {
    background: var(--app-surface-subtle);
    color: var(--app-text-muted);
}

.app-vault-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    border-color: #2563EB;
    color: #2563EB;
}

body[data-theme-version="dark"] .app-vault-pill.has-docs {
    background: rgba(16, 185, 129, 0.15);
    color: #34D399;
    border-color: rgba(16, 185, 129, 0.3);
}

/* Status Badges with Pulse Halos */
.app-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    letter-spacing: 0.02em;
    white-space: nowrap;
}

.app-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
    position: relative;
}

.app-status-approved {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.25);
}
.app-status-approved .app-status-dot {
    background: #10B981;
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    animation: status-pulse-green 2s infinite;
}

.app-status-validation {
    background: rgba(245, 158, 11, 0.1);
    color: #D97706;
    border: 1px solid rgba(245, 158, 11, 0.25);
}
.app-status-validation .app-status-dot {
    background: #F59E0B;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    animation: status-pulse-amber 2s infinite;
}

.app-status-review {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626;
    border: 1px solid rgba(239, 68, 68, 0.25);
}
.app-status-review .app-status-dot {
    background: #EF4444;
}

.app-status-draft {
    background: rgba(124, 58, 237, 0.1);
    color: #7C3AED;
    border: 1px solid rgba(124, 58, 237, 0.25);
}
.app-status-draft .app-status-dot {
    background: #8B5CF6;
}

@keyframes status-pulse-green {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.5); }
    70% { box-shadow: 0 0 0 5px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

@keyframes status-pulse-amber {
    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
    70% { box-shadow: 0 0 0 5px rgba(245, 158, 11, 0); }
    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}

body[data-theme-version="dark"] .app-status-approved {
    background: rgba(16, 185, 129, 0.15);
    color: #34D399;
}
body[data-theme-version="dark"] .app-status-validation {
    background: rgba(245, 158, 11, 0.15);
    color: #FBBF24;
}
body[data-theme-version="dark"] .app-status-review {
    background: rgba(239, 68, 68, 0.15);
    color: #F87171;
}
body[data-theme-version="dark"] .app-status-draft {
    background: rgba(124, 58, 237, 0.15);
    color: #A78BFA;
}

/* Action Buttons Dock */
.app-action-dock {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    white-space: nowrap;
}

.btn-dock-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    min-height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 0.85rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    flex-shrink: 0;
    border: 1px solid transparent;
}

.btn-dock-icon i {
    line-height: 1;
    display: inline-block;
}

.btn-dock-resume i {
    margin-left: 1.5px;
}

.btn-dock-icon:hover {
    transform: translateY(-2px);
}

.btn-dock-icon:active {
    transform: translateY(0);
}

.btn-dock-view {
    background: rgba(37, 99, 235, 0.08);
    color: #2563EB !important;
    border: 1px solid rgba(37, 99, 235, 0.22);
}

.btn-dock-view:hover {
    background: #2563EB;
    color: #FFFFFF !important;
    border-color: #2563EB;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
}

body[data-theme-version="dark"] .btn-dock-view {
    background: rgba(59, 130, 246, 0.15);
    color: #93C5FD !important;
    border-color: rgba(59, 130, 246, 0.32);
}

body[data-theme-version="dark"] .btn-dock-view:hover {
    background: #3B82F6;
    color: #FFFFFF !important;
    border-color: #3B82F6;
}

.btn-dock-move {
    background: #0F1E4D;
    color: #F59E0B !important;
    border: 1px solid rgba(245, 158, 11, 0.25);
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
}

.btn-dock-move:hover {
    background: #1E3A8A;
    color: #FCD34D !important;
    border-color: rgba(245, 158, 11, 0.5);
    box-shadow: 0 4px 10px rgba(15, 30, 77, 0.35);
}

body[data-theme-version="dark"] .btn-dock-move {
    background: #1E293B;
    color: #FBBF24 !important;
    border-color: rgba(245, 158, 11, 0.4);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

body[data-theme-version="dark"] .btn-dock-move:hover {
    background: #2563EB;
    color: #FFFFFF !important;
    border-color: #2563EB;
}

.btn-dock-mining {
    background: rgba(124, 58, 237, 0.08);
    color: #7C3AED !important;
    border: 1px solid rgba(124, 58, 237, 0.25);
}

.btn-dock-mining:hover {
    background: #7C3AED;
    color: #FFFFFF !important;
    border-color: #7C3AED;
    box-shadow: 0 4px 10px rgba(124, 58, 237, 0.3);
}

body[data-theme-version="dark"] .btn-dock-mining {
    background: rgba(139, 92, 246, 0.15);
    color: #C4B5FD !important;
    border-color: rgba(139, 92, 246, 0.35);
}

body[data-theme-version="dark"] .btn-dock-mining:hover {
    background: #8B5CF6;
    color: #FFFFFF !important;
    border-color: #8B5CF6;
}

.btn-dock-resume {
    background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%);
    color: #FFFFFF !important;
    border: none;
    box-shadow: 0 2px 6px rgba(109, 40, 217, 0.25);
}

.btn-dock-resume:hover {
    background: linear-gradient(135deg, #6D28D9 0%, #5B21B6 100%);
    box-shadow: 0 4px 12px rgba(109, 40, 217, 0.4);
    color: #FFFFFF !important;
}

body[data-theme-version="dark"] .btn-dock-resume {
    background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
    box-shadow: 0 2px 8px rgba(139, 92, 246, 0.35);
}

body[data-theme-version="dark"] .btn-dock-resume:hover {
    background: linear-gradient(135deg, #A78BFA 0%, #8B5CF6 100%);
    box-shadow: 0 4px 12px rgba(139, 92, 246, 0.5);
    color: #FFFFFF !important;
}

.btn-dock-revision {
    background: rgba(239, 68, 68, 0.1);
    color: #DC2626 !important;
    border: 1px solid rgba(239, 68, 68, 0.25);
}

.btn-dock-revision:hover {
    background: #DC2626;
    color: #FFFFFF !important;
    border-color: #DC2626;
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
}

body[data-theme-version="dark"] .btn-dock-revision {
    background: rgba(239, 68, 68, 0.18);
    color: #F87171 !important;
    border-color: rgba(239, 68, 68, 0.35);
}

body[data-theme-version="dark"] .btn-dock-revision:hover {
    background: #EF4444;
    color: #FFFFFF !important;
    border-color: #EF4444;
}

/* DataTables Footer Pagination */
.app-dt-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid var(--app-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    background: var(--app-surface-subtle);
}

.dataTables_info {
    font-size: 0.8125rem !important;
    color: var(--app-text-muted) !important;
    padding: 0 !important;
}

.dataTables_paginate {
    display: flex !important;
    align-items: center !important;
    gap: 0.3rem !important;
}

.paginate_button {
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.75rem !important;
    border-radius: 7px !important;
    border: 1px solid var(--app-border) !important;
    background: var(--app-surface) !important;
    color: var(--app-text-secondary) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.paginate_button:hover {
    background: var(--app-border) !important;
    color: var(--app-text-primary) !important;
}

.paginate_button.current {
    background: #0F1E4D !important;
    color: #FFFFFF !important;
    border-color: #0F1E4D !important;
}

body[data-theme-version="dark"] .paginate_button.current {
    background: #2563EB !important;
    border-color: #2563EB !important;
}

/* Move to Mining Modal Overrides */
#modalMoveToMiningDynamic .modal-content {
    background: var(--app-surface) !important;
    border: 1px solid var(--app-border) !important;
    border-radius: 16px;
    overflow: hidden;
}

#modalMoveToMiningDynamic .modal-body {
    color: var(--app-text-secondary);
}

.app-modal-details {
    background: var(--app-surface-subtle);
    border: 1px solid var(--app-border);
    border-radius: 12px;
    padding: 1rem 1.15rem;
}

.app-modal-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.35rem 0;
    font-size: 0.84375rem;
    border-bottom: 1px dashed var(--app-border-subtle);
}

.app-modal-row:last-child {
    border-bottom: none;
}

body[data-theme-version="dark"] .app-modal-details #dynCommonId {
    color: #60A5FA !important;
}
</style>

<div class="content-body default-height">
    <div class="container-fluid app-directory-container">

        <!-- 1. Executive Cockpit Header Suite -->
        <div class="app-header-suite">
            <div>
                <div class="app-breadcrumb">
                    <a href="{{ url('/') }}"><i class="fa fa-home me-1"></i> Home</a>
                    <span class="app-breadcrumb-divider">/</span>
                    <span>Lease Applications</span>
                    <span class="app-breadcrumb-divider">/</span>
                    <span class="text-primary fw-semibold">Application Directory</span>
                </div>
                <div class="app-title-group">
                    <div class="app-brand-icon">
                        <i class="fa fa-file-contract"></i>
                    </div>
                    <div>
                        <h1 class="app-heading">
                            Lease Concession Applications
                            <span class="app-badge-version">v2.4 Enterprise</span>
                        </h1>
                        <p class="app-subtext">Statutory intake, scrutiny verification, documentation compliance, and mining scheme promotions.</p>
                    </div>
                </div>
            </div>
            <div>
                @can('application.create')
                <a href="{{ route('step1') }}" class="btn-app-primary">
                    <i class="fa fa-plus"></i>
                    <span>New Application</span>
                </a>
                @endcan
            </div>
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
            <i class="fa fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
            <i class="fa fa-exclamation-triangle fs-5 text-warning"></i>
            <div>{{ session('warning') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 border-0 shadow-sm" style="border-radius: 12px;" role="alert">
            <i class="fa fa-times-circle fs-5 text-danger"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- 2. Bento KPI Metric Cards -->
        <div class="row g-3 mb-2">
            <!-- Total Applications -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="app-kpi-card kpi-total">
                    <div class="app-kpi-icon">
                        <i class="fa fa-folder-open"></i>
                    </div>
                    <div class="app-kpi-info">
                        <div class="app-kpi-label">Total applications</div>
                        <h3 class="app-kpi-value">{{ $kpis['total'] ?? $applications->count() }}</h3>
                        <div class="app-kpi-sub">
                            <i class="fa fa-layer-group text-primary"></i> Registered Filings
                        </div>
                    </div>
                </div>
            </div>

            <!-- Under Validation -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="app-kpi-card kpi-validation">
                    <div class="app-kpi-icon">
                        <i class="fa fa-clock"></i>
                    </div>
                    <div class="app-kpi-info">
                        <div class="app-kpi-label">Under validation</div>
                        <h3 class="app-kpi-value">{{ $kpis['under_validation'] ?? 0 }}</h3>
                        <div class="app-kpi-sub">
                            <i class="fa fa-hourglass-half text-warning"></i> Official Scrutiny
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="app-kpi-card kpi-approved">
                    <div class="app-kpi-icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="app-kpi-info">
                        <div class="app-kpi-label">Approved</div>
                        <h3 class="app-kpi-value">{{ $kpis['approved'] ?? 0 }}</h3>
                        <div class="app-kpi-sub">
                            <i class="fa fa-certificate text-success"></i> Statutory Orders Granted
                        </div>
                    </div>
                </div>
            </div>

            <!-- Needs Review / Drafts -->
            <div class="col-xl-3 col-lg-3 col-sm-6">
                <div class="app-kpi-card kpi-review">
                    <div class="app-kpi-icon">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="app-kpi-info">
                        <div class="app-kpi-label">Needs review</div>
                        <h3 class="app-kpi-value">{{ $kpis['needs_review'] ?? 0 }}</h3>
                        <div class="app-kpi-sub">
                            <i class="fa fa-edit text-danger"></i> Revisions &amp; In-Progress Drafts
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $distinctDistricts = $applications->pluck('district')->filter()->unique('id')->sortBy('name');
            $distinctCategories = $applications->pluck('category')->filter()->unique('id')->sortBy('name');
            $approvedCount = $applications->where('status', 'approved')->count();
            $underValCount = $applications->whereIn('status', ['submitted', 'under_scrutiny', 'validated'])->count();
            $revReqCount = $applications->where('status', 'revision_required')->count();
            $draftCount = $applications->where('status', 'draft')->count();
        @endphp

        <!-- 3. SaaS Directory Table Panel -->
        <div class="app-directory-card">
            <!-- Directory Header with Quick Filter Tabs -->
            <div class="app-directory-header">
                <h3 class="app-directory-title">
                    <i class="fa fa-list-check text-primary"></i>
                    Application Directory
                </h3>

                <!-- Quick Filter Tabs -->
                <div class="app-quick-tabs">
                    <button type="button" class="app-tab-pill active" data-filter="all">
                        All <span class="app-tab-count">{{ $applications->count() }}</span>
                    </button>
                    <button type="button" class="app-tab-pill" data-filter="approved">
                        Approved <span class="app-tab-count">{{ $approvedCount }}</span>
                    </button>
                    <button type="button" class="app-tab-pill" data-filter="under_validation">
                        Under Validation <span class="app-tab-count">{{ $underValCount }}</span>
                    </button>
                    <button type="button" class="app-tab-pill" data-filter="revision_required">
                        Revision Required <span class="app-tab-count">{{ $revReqCount }}</span>
                    </button>
                    <button type="button" class="app-tab-pill" data-filter="draft">
                        Drafts <span class="app-tab-count">{{ $draftCount }}</span>
                    </button>
                </div>
            </div>

            <!-- Multi-Filter Toolbar -->
            <div class="app-toolbar">
                <!-- Instant Live Search -->
                <div class="app-search-wrap">
                    <i class="fa fa-search app-search-icon"></i>
                    <input type="text" id="app_table_search" class="app-search-input" placeholder="Search application no, applicant, district, mineral...">
                    <button type="button" id="app_search_clear" class="app-search-clear" title="Clear Search">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>

                <!-- District Filter Dropdown -->
                <select id="filter_district" class="app-filter-select" aria-label="Filter by District">
                    <option value="">All Districts</option>
                    @foreach($distinctDistricts as $dist)
                        <option value="{{ $dist->name }}">{{ $dist->name }}</option>
                    @endforeach
                </select>

                <!-- Category Filter Dropdown -->
                <select id="filter_category" class="app-filter-select" aria-label="Filter by Category">
                    <option value="">All Categories</option>
                    @foreach($distinctCategories as $cat)
                        <option value="{{ $cat->code ?? $cat->name }}">{{ $cat->code ?? $cat->name }}</option>
                    @endforeach
                </select>

                <!-- Status Filter Dropdown -->
                <select id="filter_status" class="app-filter-select" aria-label="Filter by Status">
                    <option value="">All Statuses</option>
                    <option value="Approved">Approved</option>
                    <option value="Under Validation">Under Validation</option>
                    <option value="Validated">Validated</option>
                    <option value="Revision Required">Revision Required</option>
                    <option value="Draft">Draft</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <!-- Reset Filters Button -->
                <button type="button" id="btn_reset_filters" class="btn-app-reset" title="Reset All Filters">
                    <i class="fa fa-undo"></i> Reset
                </button>

                <!-- Page Length Dropdown -->
                <div class="ms-auto d-flex align-items-center">
                    <select id="filter_page_length" class="app-filter-select" style="min-width: 110px; max-width: 125px;" aria-label="Rows per page">
                        <option value="10" selected>10 / page</option>
                        <option value="25">25 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>
            </div>

            <!-- Table View -->
            <div class="app-table-wrap">
                <table id="example10" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 44px;">#</th>
                            <th>Application No.</th>
                            <th>Client / Firm</th>
                            <th>MIMAS Details</th>
                            <th>District</th>
                            <th>Category</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th class="text-center" style="white-space: nowrap;">Updated</th>
                            <th class="text-center" style="width: 85px; min-width: 85px; white-space: nowrap;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        @php
                            $uploadedDocsCount = $app->documents->whereNotNull('file_path')->count();
                            $customDocsCount = $app->documents->whereNull('document_field_id')->count();
                            $totalDocsCount = 19 + $customDocsCount;
                            $companyOrName = $app->customer?->company_name ?? ($app->customer?->customer_name ?? 'Applicant / Firm');
                            $initials = strtoupper(substr($companyOrName, 0, 2));
                            $primaryPhone = $app->contact_mobile ?? ($app->customer?->mobile_num ?? 'N/A');
                            $districtName = $app->district->name ?? 'N/A';
                            $categoryCode = $app->category->code ?? ($app->category->name ?? 'MDCC');
                            $mineralName = $app->mineral?->name ?? ($app->minerals->first()?->name ?? '');
                            $statusNormalized = strtolower($app->status);
                        @endphp
                        <tr id="app_row_{{ $app->id }}">
                            <td class="text-center text-muted fw-semibold" style="width: 44px;">{{ $loop->iteration }}</td>
                            <td>
                                <div>
                                    <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="app-ref-link" title="Open Scrutiny Dossier">
                                        <i class="fa fa-file-lines text-primary"></i>
                                        <strong>{{ $app->application_no }}</strong>
                                    </a>
                                </div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    @if($app->area_extent_ha)
                                    <span class="app-extent-chip" title="Quarry Extent">
                                        <i class="fa fa-ruler-combined"></i> {{ $app->area_extent_ha }} Ha
                                    </span>
                                    @endif
                                    @if($mineralName)
                                    <span class="badge app-mineral-chip" title="Target Mineral">
                                        {{ Str::limit($mineralName, 18) }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="app-client-cell">
                                    <div class="app-avatar-squircle">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="app-client-name">
                                            {{ $companyOrName }}
                                            @if($app->customer?->trashed())
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: .65rem;">Deleted</span>
                                            @endif
                                        </div>
                                        <div class="app-client-meta">
                                            <span>
                                                <i class="fa fa-phone text-primary" style="font-size:0.7rem;"></i> {{ $primaryPhone }}
                                                @if(!empty($app->contact_person))
                                                    <span class="text-muted">({{ $app->contact_person }})</span>
                                                @endif
                                            </span>
                                            @if(!empty($app->secondary_contact_mobile) || !empty($app->customer?->secondary_mobile_num))
                                                <span class="app-alt-badge">Alt</span>
                                                <span class="text-muted" style="font-size:0.7rem;">{{ $app->secondary_contact_mobile ?? $app->customer?->secondary_mobile_num }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(!empty($app->customer?->mimas_no) || !empty($app->customer?->mimas_number))
                                    @php
                                        $mimasDisp = $app->customer->mimas_no ?: $app->customer->mimas_number;
                                    @endphp
                                    <span class="fw-bold font-monospace d-block" style="font-size: 0.78125rem; color:var(--app-text-primary);">
                                        <i class="fa fa-id-badge text-primary me-1"></i>{{ $mimasDisp }}
                                    </span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif

                                @if(!empty($app->customer?->mimas_status))
                                    @php
                                        $mStatus = strtolower(trim($app->customer->mimas_status));
                                        $mBadgeClass = match(true) {
                                            str_contains($mStatus, 'approv') => 'bg-success-subtle text-success border border-success-subtle',
                                            str_contains($mStatus, 'reject') => 'bg-danger-subtle text-danger border border-danger-subtle',
                                            str_contains($mStatus, 'pend') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                            str_contains($mStatus, 'appl') || str_contains($mStatus, 'submit') => 'bg-primary-subtle text-primary border border-primary-subtle',
                                            default => 'bg-info-subtle text-info border border-info-subtle',
                                        };
                                    @endphp
                                    <span class="badge {{ $mBadgeClass }} mt-1" style="font-size: 0.65rem;">
                                        {{ $app->customer->mimas_status }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="app-district-pill">
                                    <i class="fa fa-location-dot text-danger" style="font-size:0.75rem;"></i>
                                    {{ $districtName }}
                                </span>
                            </td>
                            <td>
                                <span class="app-cat-badge">
                                    {{ $categoryCode }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="app-vault-pill {{ $uploadedDocsCount > 0 ? 'has-docs' : 'no-docs' }}" title="Inspect Application Document Vault">
                                    <i class="fa fa-folder-open"></i>
                                    <span>{{ $uploadedDocsCount }} / {{ $totalDocsCount }} Uploaded</span>
                                </a>
                            </td>
                            <td>
                                @if($app->status === 'approved')
                                    <span class="app-status-badge app-status-approved">
                                        <span class="app-status-dot"></span> Approved
                                    </span>
                                @elseif($app->status === 'validated')
                                    <span class="app-status-badge app-status-approved">
                                        <span class="app-status-dot"></span> Validated
                                    </span>
                                @elseif($app->status === 'under_scrutiny' || $app->status === 'submitted')
                                    <span class="app-status-badge app-status-validation">
                                        <span class="app-status-dot"></span> Under Validation
                                    </span>
                                @elseif($app->status === 'revision_required')
                                    <span class="app-status-badge app-status-review">
                                        <span class="app-status-dot"></span> Revision Required
                                    </span>
                                @elseif($app->status === 'rejected')
                                    <span class="app-status-badge app-status-review">
                                        <span class="app-status-dot"></span> Rejected
                                    </span>
                                @elseif($app->status === 'draft')
                                    <span class="app-status-badge app-status-draft">
                                        <span class="app-status-dot"></span> Draft (Step {{ min(8, (int)($app->current_step ?? 1)) }}/8)
                                    </span>
                                @else
                                    <span class="app-status-badge app-status-approved">
                                        <span class="app-status-dot"></span> {{ ucwords(str_replace('_', ' ', $app->status)) }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center text-muted small text-nowrap">
                                <i class="fa fa-calendar-alt me-1 text-muted"></i>
                                {{ $app->updated_at ? $app->updated_at->format('d M Y') : $app->created_at->format('d M Y') }}
                            </td>
                            <td class="text-center" style="width: 85px; min-width: 85px; white-space: nowrap;">
                                <div class="app-action-dock justify-content-center">
                                    @if($app->status === 'draft')
                                        @can('application.create')
                                        <a href="{{ route('application.resume', $app->id) }}" 
                                           class="btn-dock-icon btn-dock-resume" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Resume Draft (Step {{ min(8, (int)($app->current_step ?? 1)) }}/8)" 
                                           aria-label="Resume Draft">
                                            <i class="fa fa-play"></i>
                                        </a>
                                        @endcan
                                    @elseif($app->status === 'revision_required')
                                        <a href="{{ route('viewapplication', ['id' => $app->id]) }}" 
                                           class="btn-dock-icon btn-dock-revision" 
                                           data-bs-toggle="tooltip" 
                                           data-bs-placement="top" 
                                           title="Open Scrutiny Dossier to review flagged items" 
                                           aria-label="Review Revisions">
                                            <i class="fa fa-exclamation-triangle"></i>
                                        </a>
                                    @else
                                        @php
                                            $linkedMining = $app->miningApplications ? $app->miningApplications->first() : null;
                                        @endphp
                                        @if($app->status === 'approved')
                                            @if($linkedMining)
                                                <a href="{{ route('projectfolder', ['id' => $linkedMining->id]) }}" 
                                                   class="btn-dock-icon btn-dock-mining" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="View Linked Mining Plan {{ $linkedMining->application_no }}" 
                                                   aria-label="View Mining Plan">
                                                    <i class="fa fa-mountain"></i>
                                                </a>
                                            @else
                                                @can('application.edit')
                                                <button type="button" 
                                                        class="btn-dock-icon btn-dock-move btn-trigger-move-to-mining" 
                                                        data-app-id="{{ $app->id }}"
                                                        data-app-no="{{ $app->application_no }}"
                                                        data-common-id="{{ $app->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($app->id, 4, '0', STR_PAD_LEFT)) }}"
                                                        data-client="{{ $app->customer?->company_name ?? ($app->customer?->customer_name ?? 'Applicant') }}"
                                                        data-extent="{{ $app->area_extent_ha ? ($app->area_extent_ha . ' Ha') : 'N/A' }}"
                                                        data-action="{{ route('application.moveToMining', $app->id) }}"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Promote to Mining Plan Domain" 
                                                        aria-label="Move to Mining">
                                                    <i class="bi bi-rocket-takeoff-fill"></i>
                                                </button>
                                                @endcan
                                            @endif
                                        @endif
                                        @can('application.edit')
                                            <a href="{{ route('viewapplication', ['id' => $app->id]) }}" 
                                               class="btn-dock-icon btn-dock-view" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Open Scrutiny Dossier" 
                                               aria-label="View Scrutiny Dossier">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @elsecan('application.view')
                                            <a href="{{ route('viewapplication', ['id' => $app->id]) }}" 
                                               class="btn-dock-icon btn-dock-view" 
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="View Application" 
                                               aria-label="View Application">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="py-4">
                                    <div class="mb-3">
                                        <span class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%; background: var(--app-surface-subtle); border: 1px solid var(--app-border);">
                                            <i class="fa fa-folder-open fa-2x text-muted opacity-50"></i>
                                        </span>
                                    </div>
                                    <h5 class="text-dark fw-bold mb-1">No Lease Applications Found</h5>
                                    <p class="text-muted small mb-3">There are no applications registered yet or matching your selected filters.</p>
                                    @can('application.create')
                                    <a href="{{ route('step1') }}" class="btn-app-primary">
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
            <div id="app_dt_footer_container"></div>
        </div>

    </div>
</div>

<!-- Dynamic Modal for Move to Mining Plan -->
<div class="modal fade" id="modalMoveToMiningDynamic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formMoveToMiningDynamic" action="" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);">
                    <h5 class="modal-title text-white d-flex align-items-center gap-2">
                        <i class="bi bi-rocket-takeoff text-warning fs-5"></i>
                        <span>Move to Mining Plan Domain</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="app-modal-details mb-3">
                        <div class="app-modal-row">
                            <span class="text-muted small">Universal Common ID:</span>
                            <strong class="text-primary font-monospace" id="dynCommonId"></strong>
                        </div>
                        <div class="app-modal-row">
                            <span class="text-muted small">Current Lease App No:</span>
                            <strong class="font-monospace" style="color:var(--app-text-primary);" id="dynAppNo"></strong>
                        </div>
                        <div class="app-modal-row">
                            <span class="text-muted small">Applicant / Firm:</span>
                            <strong style="color:var(--app-text-primary);" id="dynClient"></strong>
                        </div>
                        <div class="app-modal-row">
                            <span class="text-muted small">Quarry Extent:</span>
                            <strong style="color:var(--app-text-primary);" id="dynExtent"></strong>
                        </div>
                    </div>
                    <p class="small text-muted mb-2 fw-semibold">
                        Moving this application to the <b>Mining Plan</b> domain will:
                    </p>
                    <ul class="small text-muted mb-0 ps-3">
                        <li class="mb-1">Retain the <b>exact same Universal Common ID</b> across the statutory portal.</li>
                        <li class="mb-1">Auto-carry applicant, district, survey numbers, and mineral data.</li>
                        <li class="mb-1">Auto-clone all verified statutory files into Mining Folders #2 &amp; #5.</li>
                        <li>Initialize <b>Stage 6.1 (Draft Intake)</b> in the Mining Portal.</li>
                    </ul>
                </div>
                <div class="modal-footer" style="background: var(--app-surface-subtle); border-top: 1px solid var(--app-border);">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-app-primary btn-sm px-3" style="border-radius: 8px;">
                        <i class="bi bi-rocket-takeoff me-1 text-warning"></i> Proceed &amp; Move to Mining Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // ==========================================================================
    // 1. SAFE DATATABLE INITIALIZATION (CLEAN DOM & FULL RESPONSIVENESS)
    // ==========================================================================
    if ($.fn.DataTable.isDataTable('#example10')) {
        $('#example10').DataTable().destroy();
    }

    function initActionTooltips() {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('#example10 [data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                var existing = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existing) { existing.dispose(); }
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }

    var table = $('#example10').DataTable({
        dom: '<"app-table-responsive"t><"app-dt-footer"ip>',
        searching: true,
        pageLength: 10,
        responsive: false,
        autoWidth: false,
        ordering: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [9] } // Disable sorting on action dock
        ],
        drawCallback: function() {
            initActionTooltips();
        },
        language: {
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            },
            info: 'Showing _START_ to _END_ of _TOTAL_ lease applications',
            infoEmpty: 'No applications found',
            emptyTable: 'No lease applications available in registry'
        }
    });

    initActionTooltips();

    // ==========================================================================
    // 2. LIVE INSTANT SEARCH WITH CLEAR BUTTON
    // ==========================================================================
    $('#app_table_search').on('input', function() {
        var val = $(this).val();
        table.search(val).draw();
        if (val.length > 0) {
            $('#app_search_clear').show();
        } else {
            $('#app_search_clear').hide();
        }
    });

    $('#app_search_clear').on('click', function() {
        $('#app_table_search').val('');
        table.search('').draw();
        $(this).hide();
        $('#app_table_search').focus();
    });

    // Keyboard shortcut (Ctrl + K or /) to focus search
    $(document).on('keydown', function(e) {
        if ((e.ctrlKey && e.key === 'k') || (e.key === '/' && !$(e.target).is('input, textarea, select'))) {
            e.preventDefault();
            $('#app_table_search').focus();
        }
    });

    // ==========================================================================
    // 3. MULTI-FILTER CONTROLS (DISTRICT, CATEGORY, STATUS, PAGE LENGTH)
    // ==========================================================================

    // Quick Filter Tabs
    $('.app-tab-pill').on('click', function() {
        $('.app-tab-pill').removeClass('active');
        $(this).addClass('active');

        var filter = $(this).data('filter');
        if (filter === 'approved') {
            table.column(7).search('Approved').draw();
            $('#filter_status').val('Approved');
        } else if (filter === 'under_validation') {
            table.column(7).search('Validation|Validated|Submitted|Scrutiny', true, false).draw();
            $('#filter_status').val('Under Validation');
        } else if (filter === 'revision_required') {
            table.column(7).search('Revision Required').draw();
            $('#filter_status').val('Revision Required');
        } else if (filter === 'draft') {
            table.column(7).search('Draft').draw();
            $('#filter_status').val('Draft');
        } else {
            table.column(7).search('').draw();
            $('#filter_status').val('');
        }
    });

    // District Filter Dropdown (Column 4)
    $('#filter_district').on('change', function() {
        var val = $(this).val();
        table.column(4).search(val ? val : '').draw();
    });

    // Category Filter Dropdown (Column 5)
    $('#filter_category').on('change', function() {
        var val = $(this).val();
        table.column(5).search(val ? val : '').draw();
    });

    // Status Filter Dropdown (Column 7)
    $('#filter_status').on('change', function() {
        var val = $(this).val();
        if (val === 'Under Validation') {
            table.column(7).search('Validation|Validated|Submitted|Scrutiny', true, false).draw();
        } else {
            table.column(7).search(val ? val : '').draw();
        }

        // Sync with quick tabs
        $('.app-tab-pill').removeClass('active');
        if (val === 'Approved') {
            $('.app-tab-pill[data-filter="approved"]').addClass('active');
        } else if (val === 'Under Validation') {
            $('.app-tab-pill[data-filter="under_validation"]').addClass('active');
        } else if (val === 'Revision Required') {
            $('.app-tab-pill[data-filter="revision_required"]').addClass('active');
        } else if (val === 'Draft') {
            $('.app-tab-pill[data-filter="draft"]').addClass('active');
        } else if (!val) {
            $('.app-tab-pill[data-filter="all"]').addClass('active');
        }
    });

    // Page Length Selector
    $('#filter_page_length').on('change', function() {
        var length = parseInt($(this).val(), 10);
        table.page.len(length).draw();
    });

    // Reset All Filters
    $('#btn_reset_filters').on('click', function() {
        $('#app_table_search').val('');
        $('#app_search_clear').hide();
        $('#filter_district').val('');
        $('#filter_category').val('');
        $('#filter_status').val('');
        $('#filter_page_length').val('10');

        $('.app-tab-pill').removeClass('active');
        $('.app-tab-pill[data-filter="all"]').addClass('active');

        table.search('').columns().search('').page.len(10).draw();
    });

    // ==========================================================================
    // 4. MOVE TO MINING MODAL HOOKS
    // ==========================================================================
    $(document).on('click', '.btn-trigger-move-to-mining', function(e) {
        e.preventDefault();
        var action = $(this).attr('data-action');
        var commonId = $(this).attr('data-common-id');
        var appNo = $(this).attr('data-app-no');
        var client = $(this).attr('data-client');
        var extent = $(this).attr('data-extent');

        $('#formMoveToMiningDynamic').attr('action', action);
        $('#dynCommonId').text(commonId);
        $('#dynAppNo').text(appNo);
        $('#dynClient').text(client);
        $('#dynExtent').text(extent);

        var modalEl = document.getElementById('modalMoveToMiningDynamic');
        if (modalEl) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });
});
</script>
@endsection
