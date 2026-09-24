@extends('layouts.app')
@section('title', 'Customer 360 Tracking Portal')

@section('main_content')
<style>
/* ─── UI-UX PRO MAX ENTERPRISE DESIGN TOKENS ─────────────────────────────── */
:root {
    --ct-primary: #0F1E4D;
    --ct-primary-hover: #1E3A8A;
    --ct-accent-blue: #2563EB;
    --ct-accent-cyan: #0284C7;
    --ct-accent-emerald: #10B981;
    --ct-accent-amber: #F59E0B;
    --ct-accent-purple: #7C3AED;
    --ct-dark: #0F172A;
    --ct-slate: #334155;
    --ct-muted: #64748B;
    --ct-light: #F8FAFC;
    --ct-border: #E2E8F0;
    --ct-border-light: rgba(226, 232, 240, 0.8);
    --ct-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
    --ct-card-hover-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.09), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
}

/* Page Layout & Container Polish */
.ct-page-header {
    background: #FFFFFF;
    border: 1px solid var(--ct-border);
    border-radius: 16px;
    padding: 18px 24px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.ct-brand-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1E40AF 100%);
    color: #FFFFFF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
    flex-shrink: 0;
}

/* Breadcrumbs Override */
.breadcrumb-item, .breadcrumb-item a {
    color: var(--ct-muted) !important;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}
.breadcrumb-item a:hover {
    color: var(--ct-accent-blue) !important;
}
.breadcrumb-item.active {
    color: var(--ct-dark) !important;
    font-weight: 600 !important;
}
.breadcrumb-item + .breadcrumb-item::before {
    color: #CBD5E1 !important;
}

/* Global 5-Column KPI Metric Cards */
.ct-kpi-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
}
@media (max-width: 1200px) {
    .ct-kpi-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 768px) {
    .ct-kpi-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 576px) {
    .ct-kpi-grid { grid-template-columns: 1fr; }
}

.ct-kpi-card {
    background: #FFFFFF;
    border: 1px solid var(--ct-border);
    border-top: 3px solid transparent;
    border-radius: 14px;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: var(--ct-card-shadow);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
    position: relative;
    overflow: hidden;
}
.ct-kpi-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ct-card-hover-shadow);
}
.ct-kpi-card.kpi-customers { border-top-color: #4F46E5; }
.ct-kpi-card.kpi-leases { border-top-color: #0284C7; }
.ct-kpi-card.kpi-mining { border-top-color: #D97706; }
.ct-kpi-card.kpi-env { border-top-color: #10B981; }
.ct-kpi-card.kpi-ec { border-top-color: #7C3AED; }

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.ct-kpi-card:hover .kpi-icon {
    transform: scale(1.06);
}
.kpi-blue { background: #EEF2FF; color: #4F46E5; }
.kpi-sky { background: #F0F9FF; color: #0284C7; }
.kpi-amber { background: #FEF3C7; color: #D97706; }
.kpi-emerald { background: #ECFDF5; color: #059669; }
.kpi-purple { background: #F5F3FF; color: #7C3AED; }

.kpi-info { min-width: 0; flex-grow: 1; }
.kpi-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    color: var(--ct-muted);
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.kpi-value {
    font-size: 26px;
    font-weight: 800;
    color: var(--ct-dark);
    margin: 0;
    line-height: 1.1;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.5px;
}
.kpi-subtext {
    font-size: 11px;
    color: #94A3B8;
    margin-top: 3px;
    font-weight: 500;
}

/* Hero Universal Search Section */
.ct-hero-card {
    background: radial-gradient(135% 120% at 85% 15%, #1E3A8A 0%, #0F172A 65%, #020617 100%);
    border-radius: 20px;
    padding: 38px 28px 30px;
    color: #FFFFFF;
    position: relative;
    box-shadow: 0 12px 30px -6px rgba(15, 30, 77, 0.35);
    overflow: visible;
}
.ct-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #BAE6FD;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 12px;
    backdrop-filter: blur(6px);
}
.ct-search-box {
    position: relative;
    max-width: 860px;
    margin: 0 auto;
}
.ct-search-input {
    height: 56px;
    font-size: 15px;
    font-weight: 500;
    border-radius: 14px;
    padding-left: 52px;
    padding-right: 230px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: #FFFFFF;
    color: #0F172A;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.ct-search-input:focus {
    border-color: #38BDF8;
    box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.35), 0 10px 25px rgba(0, 0, 0, 0.2);
    outline: none;
}
.ct-search-icon {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    color: #0284C7;
    font-size: 20px;
    pointer-events: none;
}
.ct-search-actions {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    gap: 8px;
}
.ct-kbd {
    background: #F1F5F9;
    color: #64748B;
    border: 1px solid #CBD5E1;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
    line-height: 1;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}
@media (max-width: 768px) {
    .ct-kbd { display: none; }
    .ct-search-input { padding-right: 155px; }
}
.ct-search-clear {
    color: #94A3B8;
    cursor: pointer;
    font-size: 20px;
    display: none;
    transition: color 0.15s ease;
    line-height: 1;
}
.ct-search-clear:hover {
    color: #EF4444;
}
.ct-search-btn {
    height: 42px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    padding: 0 20px;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    border: none;
    color: #FFFFFF;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    transition: all 0.2s ease;
}
.ct-search-btn:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #1E40AF 100%);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.45);
    color: #FFFFFF;
    transform: translateY(-1px);
}
.ct-search-btn:active {
    transform: translateY(0);
}

/* Glassmorphism Multi-Filter Bar */
.ct-filter-bar {
    max-width: 860px;
    margin: 20px auto 0;
    padding: 18px 22px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.18);
    border-radius: 16px;
    backdrop-filter: blur(12px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}
.ct-filter-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ct-filter-select, .ct-filter-input {
    height: 42px;
    font-size: 13.5px;
    font-weight: 500;
    border-radius: 10px;
    background-color: #FFFFFF !important;
    color: #0F172A !important;
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.ct-filter-select:focus, .ct-filter-input:focus {
    border-color: #38BDF8 !important;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.35) !important;
    outline: none;
}
.ct-btn-filter {
    height: 42px;
    font-size: 13.5px;
    font-weight: 600;
    background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
    border: 1px solid #38BDF8;
    color: #FFFFFF;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
    transition: all 0.2s ease;
}
.ct-btn-filter:hover {
    background: linear-gradient(135deg, #0369A1 0%, #075985 100%);
    box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4);
    color: #FFFFFF;
    transform: translateY(-1px);
}
.ct-btn-reset {
    height: 42px;
    font-size: 13.5px;
    font-weight: 500;
    border-radius: 10px;
    color: #FFFFFF;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
    padding: 0 16px;
}
.ct-btn-reset:hover {
    background: rgba(255, 255, 255, 0.22);
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.5);
}

/* Quick Search Pill Bar */
.ct-quick-pills-bar {
    max-width: 860px;
    margin: 14px auto 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.quick-search-pill {
    background: rgba(255, 255, 255, 0.12) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    color: #FFFFFF !important;
    font-weight: 500;
    font-size: 12px;
    padding: 5px 14px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
}
.quick-search-pill:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    border-color: rgba(255, 255, 255, 0.5) !important;
    transform: translateY(-1px);
}

/* Autocomplete Dropdown */
.ct-dropdown {
    position: absolute;
    top: 64px;
    left: 0;
    right: 0;
    background: #FFFFFF;
    border-radius: 16px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
    border: 1px solid #CBD5E1;
    z-index: 1050;
    max-height: 420px;
    overflow-y: auto;
    display: none;
}
.ct-dropdown-item {
    padding: 14px 18px;
    border-bottom: 1px solid #F1F5F9;
    cursor: pointer;
    transition: background 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ct-dropdown-item:last-child {
    border-bottom: none;
}
.ct-dropdown-item:hover, .ct-dropdown-item.active {
    background: #F8FAFC;
}

/* Active Filter Summary Bar */
.ct-active-filters-card {
    background: #FFFFFF;
    border: 1px solid var(--ct-border);
    border-radius: 14px;
    padding: 12px 18px;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
}
.ct-active-filter-chip {
    background: #EFF6FF;
    border: 1px solid #BFDBFE;
    color: #1E40AF;
    font-size: 12.5px;
    padding: 4px 12px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Customer Grid Cards */
.ct-customer-card {
    background: #FFFFFF;
    border: 1px solid var(--ct-border);
    border-radius: 16px;
    padding: 20px;
    box-shadow: var(--ct-card-shadow);
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.ct-customer-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--ct-card-hover-shadow);
    border-color: #CBD5E1;
}

.ct-avatar-gradient {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 700;
    color: #FFFFFF;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
.ct-avatar-0 { background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%); }
.ct-avatar-1 { background: linear-gradient(135deg, #059669 0%, #10B981 100%); }
.ct-avatar-2 { background: linear-gradient(135deg, #D97706 0%, #F59E0B 100%); }
.ct-avatar-3 { background: linear-gradient(135deg, #7C3AED 0%, #8B5CF6 100%); }

.ct-badge-unique-id {
    background: #FEF3C7;
    color: #92400E;
    font-weight: 700;
    border: 1px solid #FDE68A;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
}

/* Module Count Chips */
.ct-module-chip {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: 1px solid transparent;
}
.ct-module-active-lease { background: #EFF6FF; color: #1E40AF; border-color: #BFDBFE; }
.ct-module-active-mining { background: #FEF3C7; color: #92400E; border-color: #FDE68A; }
.ct-module-active-env { background: #ECFDF5; color: #065F46; border-color: #A7F3D0; }
.ct-module-active-ec { background: #F5F3FF; color: #6D28D9; border-color: #DDD6FE; }
.ct-module-active-ppt { background: #FDF2F8; color: #9D174D; border-color: #FBCFE8; }
.ct-module-active-survey { background: #F0FDF4; color: #15803D; border-color: #BBF7D0; }
.ct-module-inactive { background: #F8FAFC; color: #94A3B8; border-color: #E2E8F0; }

.ct-btn-track {
    background: linear-gradient(135deg, #0F1E4D 0%, #1E40AF 100%);
    color: #FFFFFF;
    border: none;
    border-radius: 10px;
    padding: 9px 16px;
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.15);
    transition: all 0.2s ease;
    text-decoration: none;
}
.ct-btn-track:hover {
    background: linear-gradient(135deg, #1E40AF 0%, #2563EB 100%);
    color: #FFFFFF;
    box-shadow: 0 6px 14px rgba(30, 64, 175, 0.28);
    transform: translateY(-1px);
}

/* Empty State Styling */
.ct-empty-state-card {
    background: #FFFFFF;
    border: 1px solid var(--ct-border);
    border-radius: 18px;
    padding: 50px 24px;
    text-align: center;
    box-shadow: var(--ct-card-shadow);
}
.ct-empty-icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #EFF6FF;
    color: #2563EB;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    margin-bottom: 20px;
    box-shadow: 0 0 0 8px rgba(37, 99, 235, 0.08);
}

/* Customer Meta Badges (Dossier Mode) */
.ct-meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #F8FAFC;
    border: 1px solid var(--ct-border);
    border-radius: 8px;
    font-size: 13px;
    color: var(--ct-slate);
    transition: border-color 0.15s ease, background 0.15s ease;
}
.ct-meta-pill strong {
    color: var(--ct-dark);
    font-weight: 600;
}
.ct-meta-pill:hover {
    background: #FFFFFF;
    border-color: #CBD5E1;
}

/* Modern Segmented 5-Stage Stepper */
.ct-stepper-container {
    padding: 24px 10px 10px;
}
.ct-stepper-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
}
.ct-step-node {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 145px;
    position: relative;
    z-index: 2;
}
.ct-step-bar-connector {
    flex-grow: 1;
    height: 4px;
    background: #E2E8F0;
    margin: 22px 8px 0;
    position: relative;
    z-index: 1;
    border-radius: 2px;
}
.ct-step-bar-connector.completed {
    background: #10B981;
}
.ct-step-bar-connector.in_progress {
    background: linear-gradient(90deg, #10B981 0%, #1E40AF 100%);
}
.ct-step-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    background: #FFFFFF;
    border: 2px solid #CBD5E1;
    color: var(--ct-muted);
    transition: all 0.25s ease;
}
.ct-step-node.completed .ct-step-circle {
    background: #10B981;
    border-color: #10B981;
    color: #FFFFFF;
    box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.2);
}
.ct-step-node.in_progress .ct-step-circle {
    background: #1E40AF;
    border-color: #1E40AF;
    color: #FFFFFF;
    box-shadow: 0 0 0 6px rgba(30, 64, 175, 0.25);
}
.ct-step-node.ready .ct-step-circle {
    background: #F59E0B;
    border-color: #F59E0B;
    color: #FFFFFF;
    box-shadow: 0 0 0 5px rgba(245, 158, 11, 0.2);
}
.ct-step-title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ct-dark);
    margin-bottom: 2px;
}
.ct-step-code {
    font-size: 12px;
    color: var(--ct-muted);
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 135px;
}

/* 4-Pillar Application Cards */
.ct-pillar-card {
    border-radius: 16px;
    border: 1px solid var(--ct-border);
    background: #FFFFFF;
    box-shadow: var(--ct-card-shadow);
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.ct-pillar-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--ct-card-hover-shadow);
}
.ct-pillar-header {
    padding: 18px 22px;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ct-pillar-body {
    padding: 20px 22px;
    flex-grow: 1;
}
.ct-pillar-footer {
    padding: 16px 22px;
    border-top: 1px solid #F1F5F9;
    background: #FAFAFC;
    border-radius: 0 0 16px 16px;
}
.ct-field-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 0;
    border-bottom: 1px dashed #F1F5F9;
    font-size: 13.5px;
}
.ct-field-row:last-child {
    border-bottom: none;
}
.ct-field-label {
    color: var(--ct-muted);
    font-weight: 500;
}
.ct-field-val {
    color: var(--ct-dark);
    font-weight: 600;
    text-align: right;
}

/* Consolidated Document Vault Table */
.ct-doc-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.ct-doc-table thead th {
    background: #F8FAFC;
    color: #475569;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 14px 20px;
    border-bottom: 2px solid var(--ct-border);
}
.ct-doc-table tbody tr {
    transition: background-color 0.15s ease;
}
.ct-doc-table tbody tr:hover {
    background-color: #F8FAFC !important;
}
.ct-doc-table tbody td {
    padding: 14px 20px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}

/* Document Filter Chips */
.ct-filter-chip {
    border: 1px solid #CBD5E1;
    background: #FFFFFF;
    color: #475569;
    border-radius: 10px;
    padding: 7px 16px;
    font-size: 13px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.15s ease;
    cursor: pointer;
}
.ct-filter-chip:hover {
    background: #F1F5F9;
    color: var(--ct-dark);
}
.ct-filter-chip.active {
    background: var(--ct-primary);
    border-color: var(--ct-primary);
    color: #FFFFFF;
    font-weight: 600;
}
.ct-filter-chip .chip-count {
    background: #F1F5F9;
    color: #475569;
    padding: 1px 7px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
}
.ct-filter-chip.active .chip-count {
    background: rgba(255, 255, 255, 0.25);
    color: #FFFFFF;
}

/* Status Badges */
.ct-badge-success {
    background: #ECFDF5;
    color: #065F46;
    border: 1px solid #A7F3D0;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ct-badge-primary {
    background: #EFF6FF;
    color: #1E40AF;
    border: 1px solid #BFDBFE;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ct-badge-warning {
    background: #FEF3C7;
    color: #92400E;
    border: 1px solid #FDE68A;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ct-badge-neutral {
    background: #F8FAFC;
    color: var(--ct-muted);
    border: 1px solid var(--ct-border);
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Print CSS */
@media print {
    body * { visibility: hidden; }
    .print-dossier-area, .print-dossier-area * { visibility: visible; }
    .print-dossier-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: #FFFFFF;
    }
    .no-print, .header, .dlabnav, .footer, .ct-hero-card, .btn, .breadcrumb {
        display: none !important;
    }
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">
        <!-- Page Title & Navigation Header -->
        <div class="ct-page-header mb-4 no-print">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bi bi-house-door me-1"></i>Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Customers</a></li>
                        <li class="breadcrumb-item active">Customer 360 Tracking</li>
                    </ol>
                    <div class="d-flex align-items-center gap-3">
                        <div class="ct-brand-icon">
                            <i class="bi bi-radar"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                Customer 360 Tracking Portal
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-11 fw-bold rounded-pill">v2.4 Enterprise</span>
                            </h4>
                            <p class="text-muted mb-0 fs-13">Unified Statutory Dossier, Multi-Module Audit & Regulatory Compliance</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-end align-self-center mt-3 mt-md-0">
                    @if($customer)
                        <a href="{{ route('customer-tracking.proforma-invoice', $customer->slug ?? $customer->id) }}" target="_blank" class="btn btn-sm text-white me-1 shadow-sm rounded-pill px-3" style="background:#0F1E4D;">
                            <i class="bi bi-file-earmark-text me-1 text-warning"></i> Proforma Invoice
                        </a>
                        <a href="{{ route('customer-tracking.tax-invoice', $customer->slug ?? $customer->id) }}" target="_blank" class="btn btn-success btn-sm me-1 shadow-sm rounded-pill px-3">
                            <i class="bi bi-receipt me-1"></i> Tax Invoice
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-primary btn-sm me-1 shadow-sm rounded-pill px-3">
                            <i class="bi bi-printer me-1"></i> Print Dossier
                        </button>
                        <a href="{{ route('customer-tracking.index') }}" class="btn btn-light border text-dark btn-sm shadow-sm rounded-pill px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Global KPI Metrics Bar (5 Balanced Equal Columns) -->
        <div class="ct-kpi-grid mb-4 no-print">
            <div class="ct-kpi-card kpi-customers">
                <div class="kpi-icon kpi-blue"><i class="fa fa-users"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">Total Customers</span>
                    <h4 class="kpi-value">{{ number_format($stats['total_customers'] ?? 0) }}</h4>
                    <span class="kpi-subtext">Registered Licensees</span>
                </div>
            </div>
            <div class="ct-kpi-card kpi-leases">
                <div class="kpi-icon kpi-sky"><i class="fa fa-file-contract"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">Lease Apps</span>
                    <h4 class="kpi-value">{{ number_format($stats['total_leases'] ?? 0) }}</h4>
                    <span class="kpi-subtext">Mining Concessions</span>
                </div>
            </div>
            <div class="ct-kpi-card kpi-mining">
                <div class="kpi-icon kpi-amber"><i class="fa fa-mountain"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">Mining Plans</span>
                    <h4 class="kpi-value">{{ number_format($stats['total_mining'] ?? 0) }}</h4>
                    <span class="kpi-subtext">Approved Schemes</span>
                </div>
            </div>
            <div class="ct-kpi-card kpi-env">
                <div class="kpi-icon kpi-emerald"><i class="fa fa-leaf"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">Env Clearances</span>
                    <h4 class="kpi-value">{{ number_format($stats['total_env'] ?? 0) }}</h4>
                    <span class="kpi-subtext">SEIAA B1 / B2 Projects</span>
                </div>
            </div>
            <div class="ct-kpi-card kpi-ec">
                <div class="kpi-icon kpi-purple"><i class="fa fa-certificate"></i></div>
                <div class="kpi-info">
                    <span class="kpi-label">EC Certificates</span>
                    <h4 class="kpi-value">{{ number_format($stats['total_ec_certs'] ?? 0) }}</h4>
                    <span class="kpi-subtext">Statutory Orders Granted</span>
                </div>
            </div>
        </div>

        <!-- Hero Universal Search & Multi-Faceted Filter Section -->
        <div class="row mb-4 no-print">
            <div class="col-12">
                <div class="ct-hero-card text-center">
                    <div class="ct-hero-badge">
                        <i class="bi bi-shield-lock-fill"></i> Centralized Statutory Tracking Engine
                    </div>
                    <h3 class="fw-bold mb-1 text-white">
                        Customer 360 Dossier & Application Tracking
                    </h3>
                    <p class="text-white-50 mb-3 fs-14">
                        Search instantly by Customer Unique ID, 12-digit Aadhaar, Phone Numbers, Customer Name, Company / Quarry Name, or Application Reference.
                    </p>

                    <!-- Search & Multi-Filter Form -->
                    <form action="{{ route('customer-tracking.index') }}" method="GET" id="trackingSearchForm">
                        <div class="ct-search-box">
                            <i class="bi bi-search ct-search-icon"></i>
                            <input 
                                type="text" 
                                name="q" 
                                id="universalSearchInput" 
                                class="form-control ct-search-input" 
                                placeholder="Enter Customer Unique ID, Aadhaar (12 digits), Mobile, Customer Name, Quarry, or App No..." 
                                value="{{ $query ?? '' }}"
                                autocomplete="off"
                                autofocus
                            >
                            <div class="ct-search-actions">
                                <kbd class="ct-kbd" title="Shortcut to focus search">Ctrl + K</kbd>
                                <i class="bi bi-x-circle-fill ct-search-clear" id="searchClearBtn" title="Clear search"></i>
                                <button type="submit" class="ct-search-btn">
                                    <i class="bi bi-radar"></i> Track Dossier
                                </button>
                            </div>

                            <!-- Live Autocomplete Suggestion Dropdown -->
                            <div class="ct-dropdown text-start" id="searchResultsDropdown"></div>
                        </div>

                        <!-- Multi-Faceted Filter Controls Bar (Spacious 2-Row Layout) -->
                        <div class="ct-filter-bar text-start">
                            <div class="row g-3">
                                <!-- Row 1: District & Application Type (Full 50% width each to avoid truncation) -->
                                <div class="col-lg-6 col-md-6">
                                    <label class="ct-filter-label" for="filterDistrict">
                                        <i class="bi bi-geo-alt-fill text-warning"></i> District:
                                    </label>
                                    <select name="district_id" id="filterDistrict" class="form-select ct-filter-select">
                                        <option value="">All Districts (All Tamil Nadu)</option>
                                        @foreach($districts as $d)
                                            <option value="{{ $d->id }}" {{ (string)($districtId ?? '') === (string)$d->id ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <label class="ct-filter-label" for="filterAppType">
                                        <i class="bi bi-folder-check text-info"></i> Application Type:
                                    </label>
                                    <select name="app_type" id="filterAppType" class="form-select ct-filter-select">
                                        <option value="">All Application Types</option>
                                        @foreach($appTypes as $key => $label)
                                            <option value="{{ $key }}" {{ ($appType ?? '') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Row 2: Date Range and Action Buttons -->
                                <div class="col-lg-4 col-md-4">
                                    <label class="ct-filter-label" for="filterDateFrom">
                                        <i class="bi bi-calendar-event text-success"></i> Created From:
                                    </label>
                                    <input type="date" name="date_from" id="filterDateFrom" class="form-control ct-filter-input" value="{{ $dateFrom ?? '' }}">
                                </div>

                                <div class="col-lg-4 col-md-4">
                                    <label class="ct-filter-label" for="filterDateTo">
                                        <i class="bi bi-calendar-check text-success"></i> Created To:
                                    </label>
                                    <input type="date" name="date_to" id="filterDateTo" class="form-control ct-filter-input" value="{{ $dateTo ?? '' }}">
                                </div>

                                <div class="col-lg-4 col-md-4 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn ct-btn-filter flex-grow-1" title="Apply filter criteria">
                                        <i class="bi bi-funnel-fill"></i> Filter Applications
                                    </button>
                                    @if(!empty($isFiltered) || !empty($query))
                                        <a href="{{ route('customer-tracking.index') }}" class="btn ct-btn-reset" title="Reset all filters">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Quick Filter Search Pills -->
                    <div class="ct-quick-pills-bar">
                        <span class="text-white-50 fs-12 fw-semibold me-1"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Quick Search:</span>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="TN-MMS"><i class="bi bi-shield-check text-warning"></i> Customer Unique ID</button>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="Quarry"><i class="bi bi-building text-info"></i> Company / Quarry</button>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="Phone"><i class="bi bi-telephone text-success"></i> Phone Numbers</button>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="Aadhaar"><i class="bi bi-person-vcard text-light"></i> Aadhaar No</button>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="ENV-B2"><i class="bi bi-leaf text-success"></i> Env Project</button>
                        <button type="button" class="btn btn-sm quick-search-pill" data-query="MP-"><i class="bi bi-hammer text-warning"></i> Mining Plan</button>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($isFiltered))
            <!-- Active Filter Summary Bar -->
            <div class="ct-active-filters-card mb-4 no-print">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-bold text-dark fs-13">
                            <i class="bi bi-funnel text-primary me-1"></i>Active Criteria:
                        </span>
                        @if(!empty($query))
                            <span class="ct-active-filter-chip">
                                <strong>Search:</strong> "{{ $query }}"
                            </span>
                        @endif
                        @if(!empty($districtId))
                            @php $activeDist = $districts->firstWhere('id', $districtId); @endphp
                            <span class="ct-active-filter-chip">
                                <strong>District:</strong> {{ $activeDist?->name ?? $districtId }}
                            </span>
                        @endif
                        @if(!empty($appType))
                            <span class="ct-active-filter-chip">
                                <strong>Module:</strong> {{ $appTypes[$appType] ?? $appType }}
                            </span>
                        @endif
                        @if(!empty($dateFrom) || !empty($dateTo))
                            <span class="ct-active-filter-chip">
                                <strong>Created:</strong> {{ $dateFrom ?: 'Beginning' }} to {{ $dateTo ?: 'Today' }}
                            </span>
                        @endif
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold fs-12 ms-1">
                            <i class="bi bi-check-circle me-1"></i> {{ $customersList->total() }} Customer{{ $customersList->total() !== 1 ? 's' : '' }} Matched
                        </span>
                    </div>
                    <a href="{{ route('customer-tracking.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="bi bi-x-circle me-1"></i> Clear All Filters
                    </a>
                </div>
            </div>
        @endif

        @if(!$customer)
            <!-- If no customer selected yet: Recent Active Customers Grid & Guides -->
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">
                            @if(!empty($isFiltered))
                                <i class="bi bi-funnel-fill text-primary me-2"></i>Filtered Customers ({{ $customersList->total() }} found)
                            @else
                                <i class="bi bi-clock-history text-primary me-2"></i>Recent Active Customers
                            @endif
                        </h5>
                        <span class="text-muted fs-13">Select a customer below or use search & filters above</span>
                    </div>
                </div>

                @forelse($customersList as $idx => $rc)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="ct-customer-card">
                        <div>
                            <!-- Header Row: Avatar, Name, Company -->
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <div class="ct-avatar-gradient ct-avatar-{{ $rc->id % 4 }}">
                                    {{ strtoupper(substr($rc->customer_name, 0, 2)) }}
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <h6 class="fw-bold text-truncate mb-0 text-dark fs-15">{{ ucwords(strtolower($rc->customer_name)) }}</h6>
                                        <span class="badge bg-light text-muted border fs-10 text-uppercase">
                                            {{ $rc->company_name ? 'Corporate' : 'Individual' }}
                                        </span>
                                    </div>
                                    <small class="text-muted text-truncate d-block mt-1 fs-13">
                                        <i class="bi bi-building me-1 text-secondary"></i>{{ $rc->company_name ?: 'Individual Licensee' }}
                                    </small>
                                </div>
                            </div>

                            <!-- Contact & Location Strip -->
                            <div class="bg-light p-2 rounded-3 border mb-3 fs-12">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-muted">
                                        <i class="bi bi-telephone-fill me-1 text-success"></i>
                                        <a href="tel:{{ $rc->mobile_num }}" class="text-decoration-none text-dark fw-semibold">{{ $rc->mobile_num }}</a>
                                    </span>
                                    @if($rc->secondary_mobile_num)
                                        <span class="text-muted" title="Secondary / Site In-charge">
                                            <i class="bi bi-telephone me-1 text-info"></i>
                                            <a href="tel:{{ $rc->secondary_mobile_num }}" class="text-decoration-none text-muted">{{ $rc->secondary_mobile_num }}</a>
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="ct-badge-unique-id py-1 px-2">
                                        <i class="bi bi-shield-check"></i> {{ $rc->mimas_no ?: ('CUST-' . str_pad($rc->id, 4, '0', STR_PAD_LEFT)) }}
                                    </span>
                                    <span class="badge bg-white text-dark border">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $rc->district?->name ?: 'Tamil Nadu' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Statutory Modules Breakdown Matrix -->
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                <span class="ct-module-chip {{ $rc->lease_applications_count > 0 ? 'ct-module-active-lease' : 'ct-module-inactive' }}">
                                    Lease: <strong>{{ $rc->lease_applications_count }}</strong>
                                </span>
                                <span class="ct-module-chip {{ $rc->mining_applications_count > 0 ? 'ct-module-active-mining' : 'ct-module-inactive' }}">
                                    Mining: <strong>{{ $rc->mining_applications_count }}</strong>
                                </span>
                                <span class="ct-module-chip {{ $rc->environment_projects_count > 0 ? 'ct-module-active-env' : 'ct-module-inactive' }}">
                                    Env: <strong>{{ $rc->environment_projects_count }}</strong>
                                </span>
                                <span class="ct-module-chip {{ $rc->ec_certificates_count > 0 ? 'ct-module-active-ec' : 'ct-module-inactive' }}">
                                    EC: <strong>{{ $rc->ec_certificates_count }}</strong>
                                </span>
                                <span class="ct-module-chip {{ ($rc->ppt_applications_count ?? 0) > 0 ? 'ct-module-active-ppt' : 'ct-module-inactive' }}">
                                    PPT: <strong>{{ $rc->ppt_applications_count ?? 0 }}</strong>
                                </span>
                                <span class="ct-module-chip {{ (($rc->dgps_surveys_count ?? 0) + ($rc->drone_surveys_count ?? 0)) > 0 ? 'ct-module-active-survey' : 'ct-module-inactive' }}">
                                    Surveys: <strong>{{ ($rc->dgps_surveys_count ?? 0) + ($rc->drone_surveys_count ?? 0) }}</strong>
                                </span>
                            </div>
                        </div>

                        <!-- Card Action CTA -->
                        <div>
                            <a href="{{ route('customer-tracking.show', $rc->slug ?? $rc->id) }}" class="ct-btn-track">
                                <i class="bi bi-radar"></i> Track Applications & Dossier &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="ct-empty-state-card">
                        <div class="ct-empty-icon-circle">
                            <i class="bi bi-search"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No Customers Found</h4>
                        <p class="text-muted mx-auto mb-4" style="max-width: 520px;">
                            We couldn't find any customer records matching the active search query or filter criteria. Try searching by Customer Unique ID, mobile number, or clear your filters.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('customer-tracking.index') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset All Filters
                            </a>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-people me-1"></i> View Customer Directory
                            </a>
                        </div>
                    </div>
                </div>
                @endforelse

                @if($customersList instanceof \Illuminate\Pagination\LengthAwarePaginator && $customersList->hasPages())
                    <div class="col-12 d-flex justify-content-center mt-3">
                        {{ $customersList->links() }}
                    </div>
                @endif
            </div>

            <!-- Search Guide / Lookup Tips Card -->
            <div class="row mt-2 mb-4">
                <div class="col-12">
                    <div class="card bg-white border shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-info-circle-fill text-primary"></i> Search Intelligence & Pro Tips:
                            </h6>
                            <div class="row g-3 fs-13 text-muted">
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 rounded bg-light border h-100">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-upc-scan text-info me-1"></i> Customer Unique ID:</strong>
                                        Instant lookup by master registration identifier (e.g. <code>TN-MMS-SLM-001</code> or <code>CUST-0001</code>).
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 rounded bg-light border h-100">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-geo-alt-fill text-warning me-1"></i> District & App Type:</strong>
                                        Filter across 38 Tamil Nadu districts and 8 statutory modules simultaneously.
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 rounded bg-light border h-100">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-telephone-fill text-success me-1"></i> Dual Phone Numbers:</strong>
                                        Matches across both Primary Mobile and Secondary / Site In-charge numbers.
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <div class="p-3 rounded bg-light border h-100">
                                        <strong class="text-dark d-block mb-1"><i class="bi bi-building-fill text-primary me-1"></i> Company / Quarry:</strong>
                                        Full-text search across registered business entities, quarry projects, taluk, and village.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <!-- CUSTOMER 360 DOSSIER (PRINTABLE INSPECTION AREA)                    -->
            <!-- ═══════════════════════════════════════════════════════════════════ -->
            <div class="print-dossier-area">

                <!-- 1. Customer Master Profile Header Card -->
                <div class="card border mb-4 shadow-sm" style="border-radius: 18px;">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8 col-md-7 d-flex align-items-start gap-4">
                                <div class="ct-avatar-gradient ct-avatar-0 shadow-sm flex-shrink-0" style="width: 64px; height: 64px; font-size: 26px; border-radius: 16px;">
                                    {{ strtoupper(substr($customer->customer_name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h4 class="fw-bold text-dark mb-0">{{ ucwords(strtolower($customer->customer_name)) }}</h4>
                                        <span class="badge bg-primary text-white px-2 py-1 fs-12 rounded-pill">
                                            <i class="bi bi-{{ $customer->company_name ? 'buildings' : 'person' }} me-1"></i>
                                            {{ $customer->company_name ? 'Corporate / Firm' : 'Individual Licensee' }}
                                        </span>
                                        @if($customer->status)
                                            <span class="ct-badge-success">
                                                <i class="bi bi-check-circle-fill"></i> Active Customer
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-muted mb-3 fs-14">
                                        <i class="bi bi-building me-1"></i>{{ $customer->company_name ?: 'Individual Licensee' }}
                                        @if($customer->address)
                                            &bull; <i class="bi bi-geo-alt me-1"></i>{{ $customer->address }}
                                        @endif
                                    </p>

                                    <!-- Key Identifiers Strip -->
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="ct-badge-unique-id py-1 px-3">
                                            <i class="bi bi-shield-check"></i> Unique ID: <strong>{{ $customer->mimas_no ?: ('CUST-' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)) }}</strong>
                                        </span>
                                        @if($customer->aadhaar_no)
                                            <span class="ct-meta-pill">
                                                <i class="bi bi-person-vcard text-muted"></i> Aadhaar: <strong>{{ substr($customer->aadhaar_no, 0, 4) . ' **** ' . substr($customer->aadhaar_no, -4) }}</strong>
                                            </span>
                                        @endif
                                        @if($customer->pan)
                                            <span class="ct-meta-pill">
                                                <i class="bi bi-credit-card-2-front text-muted"></i> PAN: <strong>{{ strtoupper($customer->pan) }}</strong>
                                            </span>
                                        @endif
                                        @if($customer->mobile_num)
                                            <span class="ct-meta-pill">
                                                <i class="bi bi-telephone text-success"></i> <a href="tel:{{ $customer->mobile_num }}" class="text-decoration-none text-dark"><strong>{{ $customer->mobile_num }}</strong></a>
                                            </span>
                                        @endif
                                        @if($customer->secondary_mobile_num)
                                            <span class="ct-meta-pill">
                                                <i class="bi bi-telephone-plus text-info"></i> <a href="tel:{{ $customer->secondary_mobile_num }}" class="text-decoration-none text-dark">Sec: <strong>{{ $customer->secondary_mobile_num }}</strong></a>
                                            </span>
                                        @endif
                                        @if($customer->email)
                                            <span class="ct-meta-pill">
                                                <i class="bi bi-envelope text-primary"></i> <strong>{{ $customer->email }}</strong>
                                            </span>
                                        @endif
                                        <a href="{{ route('customer-tracking.proforma-invoice', $customer->slug ?? $customer->id) }}" target="_blank" class="ct-meta-pill text-decoration-none bg-white border-primary" style="color:#0F1E4D;">
                                            <i class="bi bi-file-earmark-text text-primary"></i> <strong>Proforma Invoice &nearr;</strong>
                                        </a>
                                        <a href="{{ route('customer-tracking.tax-invoice', $customer->slug ?? $customer->id) }}" target="_blank" class="ct-meta-pill text-decoration-none bg-white border-success" style="color:#059669;">
                                            <i class="bi bi-receipt text-success"></i> <strong>Tax Invoice &nearr;</strong>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-5 mt-3 mt-md-0 border-start ps-md-4">
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="fs-12 text-uppercase text-muted fw-bold mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-geo text-primary"></i> Quarry Location & Extent
                                    </div>
                                    <div class="ct-field-row py-1">
                                        <span class="ct-field-label">District:</span>
                                        <span class="ct-field-val">{{ $customer->district?->name ?: ($dossierData['leaseApp']?->district?->name ?? 'N/A') }}</span>
                                    </div>
                                    <div class="ct-field-row py-1">
                                        <span class="ct-field-label">Mineral:</span>
                                        <span class="ct-field-val">{{ $customer->mineral?->name ?: ($dossierData['leaseApp']?->mineral?->name ?? 'Rough Stone / Gravel') }}</span>
                                    </div>
                                    <div class="ct-field-row py-1">
                                        <span class="ct-field-label">Quarry Extent:</span>
                                        <span class="ct-field-val text-success">
                                            {{ $dossierData['leaseApp']?->area_extent_ha ? number_format($dossierData['leaseApp']->area_extent_ha, 2) . ' Ha' : ($customer->area ? number_format($customer->area, 2) . ' Ha' : '—') }}
                                        </span>
                                    </div>
                                    <div class="ct-field-row py-1">
                                        <span class="ct-field-label">Village / Taluk:</span>
                                        <span class="ct-field-val text-truncate" style="max-width: 150px;">
                                            {{ $dossierData['leaseApp']?->village ?: ($customer->address ?: '—') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Universal 5-Stage Lifecycle Stepper -->
                <div class="card border mb-4 shadow-sm" style="border-radius: 18px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-diagram-3-fill text-primary"></i> Universal 5-Stage Lifecycle Tracking
                            </h5>
                            <small class="text-muted">End-to-End Progression: Lease Application &rarr; Mining Plan &rarr; Environment Clearance &rarr; EC Certificate &rarr; Mine Opening / PPT</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-13 fw-bold text-muted">Overall Progress:</span>
                            <div class="progress" style="width: 140px; height: 10px; border-radius: 8px;">
                                <div 
                                    class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                    role="progressbar" 
                                    style="width: {{ $dossierData['progressPercent'] }}%;" 
                                    aria-valuenow="{{ $dossierData['progressPercent'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100"
                                ></div>
                            </div>
                            <span class="badge bg-success fs-12 px-2 py-1 rounded-pill">{{ $dossierData['progressPercent'] }}% Complete</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="ct-stepper-container">
                            <div class="ct-stepper-row">
                                @php $totalSteps = count($dossierData['stepper']); @endphp
                                @foreach($dossierData['stepper'] as $stepIdx => $st)
                                    <div class="ct-step-node {{ $st['status'] }}">
                                        <div class="ct-step-circle">
                                            @if($st['status'] === 'completed')
                                                <i class="bi bi-check-lg"></i>
                                            @elseif($st['status'] === 'in_progress')
                                                <span>{{ $stepIdx }}</span>
                                            @elseif($st['status'] === 'ready')
                                                <i class="bi bi-arrow-right"></i>
                                            @else
                                                <span>{{ $stepIdx }}</span>
                                            @endif
                                        </div>
                                        <div class="ct-step-title">{{ $st['name'] }}</div>
                                        <div class="ct-step-code" title="{{ $st['app_no'] }}">{{ $st['app_no'] }}</div>
                                        <div>
                                            @if($st['status'] === 'completed')
                                                <span class="ct-badge-success">Completed</span>
                                            @elseif($st['status'] === 'in_progress')
                                                <span class="ct-badge-primary">In Progress</span>
                                            @elseif($st['status'] === 'ready')
                                                <span class="ct-badge-warning">Ready</span>
                                            @else
                                                <span class="ct-badge-neutral">Pending</span>
                                            @endif
                                        </div>
                                        @if($st['date'])
                                            <div class="text-muted mt-1" style="font-size: 11px;">{{ $st['date'] }}</div>
                                        @endif
                                    </div>
                                    @if($stepIdx < $totalSteps)
                                        <div class="ct-step-bar-connector {{ $st['status'] === 'completed' ? 'completed' : ($st['status'] === 'in_progress' ? 'in_progress' : '') }}"></div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. 4-Pillar Application Detail Cards (2x2 Grid) -->
                <div class="row g-4 mb-4">
                    <!-- Pillar 1: Lease Application -->
                    <div class="col-lg-6">
                        <div class="ct-pillar-card">
                            <div class="ct-pillar-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kpi-icon kpi-blue" style="width: 40px; height: 40px; font-size: 17px;">
                                        <i class="bi bi-file-earmark-text-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">1. Lease Application</h6>
                                        <small class="text-muted">Mining Lease Sanction & G.O. Order</small>
                                    </div>
                                </div>
                                @if($dossierData['leaseApp'])
                                    @php
                                        $leaseStatus = strtolower($dossierData['leaseApp']->status ?? 'draft');
                                        $leaseBadge = match($leaseStatus) {
                                            'approved' => 'ct-badge-success',
                                            'draft' => 'ct-badge-warning',
                                            default => 'ct-badge-primary',
                                        };
                                    @endphp
                                    <span class="{{ $leaseBadge }}">{{ ucfirst($leaseStatus) }}</span>
                                @else
                                    <span class="ct-badge-neutral">Not Applied</span>
                                @endif
                            </div>
                            <div class="ct-pillar-body">
                                @if($dossierData['leaseApp'])
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-hash me-1"></i>Application No:</span>
                                        <span class="ct-field-val"><span class="badge bg-light text-dark border font-monospace">{{ $dossierData['leaseApp']->application_no }}</span></span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-qr-code me-1"></i>Common ID:</span>
                                        <span class="ct-field-val font-monospace text-primary">{{ $dossierData['leaseApp']->common_id ?: '—' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-file-text me-1"></i>G.O. Order No:</span>
                                        <span class="ct-field-val">{{ $dossierData['leaseApp']->go_number ?: 'Pending Issuance' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-clock-history me-1"></i>Lease Period:</span>
                                        <span class="ct-field-val">{{ $dossierData['leaseApp']->lease_period_years ? $dossierData['leaseApp']->lease_period_years . ' Years' : '5 Years' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-paperclip me-1"></i>Documents Attached:</span>
                                        <span class="ct-field-val">
                                            <span class="badge bg-light text-dark border">{{ $dossierData['leaseApp']->documents->count() }} Files</span>
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-file-earmark-x text-muted" style="font-size: 34px;"></i>
                                        <p class="mb-2 mt-2 fs-13">No lease application registered for this customer.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ct-pillar-footer">
                                @if($dossierData['leaseApp'])
                                    <a href="{{ route('viewapplication') }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-arrow-up-right-circle"></i> Open Lease Application &nearr;
                                    </a>
                                @else
                                    <a href="{{ route('step1') }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-plus-lg"></i> Start New Lease Application
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 2: Mining Plan -->
                    <div class="col-lg-6">
                        <div class="ct-pillar-card">
                            <div class="ct-pillar-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kpi-icon kpi-amber" style="width: 40px; height: 40px; font-size: 17px;">
                                        <i class="bi bi-hammer"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">2. Mining Plan</h6>
                                        <small class="text-muted">Approved Mining Plan & Scheme of Mining</small>
                                    </div>
                                </div>
                                @if($dossierData['miningApp'])
                                    @php
                                        $miningStatus = strtolower($dossierData['miningApp']->status ?? 'draft');
                                        $miningBadge = match($miningStatus) {
                                            'approved' => 'ct-badge-success',
                                            'draft' => 'ct-badge-warning',
                                            default => 'ct-badge-primary',
                                        };
                                    @endphp
                                    <span class="{{ $miningBadge }}">{{ ucfirst($miningStatus) }}</span>
                                @else
                                    <span class="ct-badge-neutral">Not Started</span>
                                @endif
                            </div>
                            <div class="ct-pillar-body">
                                @if($dossierData['miningApp'])
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-hash me-1"></i>Plan App No:</span>
                                        <span class="ct-field-val"><span class="badge bg-light text-dark border font-monospace">{{ $dossierData['miningApp']->application_no }}</span></span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-gear me-1"></i>Nature of Work:</span>
                                        <span class="ct-field-val">{{ $dossierData['miningApp']->natureOfWork?->name ?: 'Fresh Grant' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-person-badge me-1"></i>RQP Engineer:</span>
                                        <span class="ct-field-val">{{ $dossierData['miningApp']->rqp_name ?: 'Recognized Qualified Person' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-file-check me-1"></i>Approval Order No:</span>
                                        <span class="ct-field-val">{{ $dossierData['miningApp']->approval_order_no ?: 'Under Review' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-clock-history me-1"></i>Validity Period:</span>
                                        <span class="ct-field-val">{{ $dossierData['miningApp']->validity_years ? $dossierData['miningApp']->validity_years . ' Years' : '5 Years' }}</span>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-hammer text-muted" style="font-size: 34px;"></i>
                                        <p class="mb-2 mt-2 fs-13">Mining plan has not been submitted yet.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ct-pillar-footer">
                                @if($dossierData['miningApp'])
                                    <a href="{{ route('miningplan.index') }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-arrow-up-right-circle"></i> Open Mining Plan &nearr;
                                    </a>
                                @else
                                    <a href="{{ route('newapplication') }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-plus-lg"></i> Create Mining Plan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 3: Environment Clearance -->
                    <div class="col-lg-6">
                        <div class="ct-pillar-card">
                            <div class="ct-pillar-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kpi-icon kpi-emerald" style="width: 40px; height: 40px; font-size: 17px;">
                                        <i class="bi bi-leaf-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">3. Environment Clearance</h6>
                                        <small class="text-muted">SEIAA / MoEFCC B1/B2 Project Clearance</small>
                                    </div>
                                </div>
                                @if($dossierData['envProj'])
                                    <span class="ct-badge-{{ $dossierData['envProj']->status === 'approved' ? 'success' : 'primary' }}">
                                        {{ ucfirst($dossierData['envProj']->status ?? 'Active') }}
                                    </span>
                                @else
                                    <span class="ct-badge-neutral">Not Initiated</span>
                                @endif
                            </div>
                            <div class="ct-pillar-body">
                                @if($dossierData['envProj'])
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-hash me-1"></i>Project Code:</span>
                                        <span class="ct-field-val"><span class="badge bg-light text-success border font-monospace">{{ $dossierData['envProj']->project_code }}</span></span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-tag me-1"></i>Category:</span>
                                        <span class="ct-field-val">
                                            <span class="badge bg-light text-dark border fw-bold">
                                                {{ $dossierData['envProj']->category }} {{ $dossierData['envProj']->sub_category ? '— ' . $dossierData['envProj']->sub_category : '' }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-card-heading me-1"></i>Project Title:</span>
                                        <span class="ct-field-val text-truncate" style="max-width: 230px;">
                                            {{ $dossierData['envProj']->project_name ?: 'Quarry Project Clearance' }}
                                        </span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-paperclip me-1"></i>EC Documents:</span>
                                        <span class="ct-field-val">
                                            <span class="badge bg-light text-dark border">{{ $dossierData['envProj']->documents->count() }} Files</span>
                                        </span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-megaphone me-1"></i>Public Hearing:</span>
                                        <span class="ct-field-val">
                                            {{ $dossierData['envProj']->public_hearing_date ? $dossierData['envProj']->public_hearing_date->format('d M Y') : 'Not Applicable (B2)' }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-leaf text-muted" style="font-size: 34px;"></i>
                                        <p class="mb-2 mt-2 fs-13">Environment clearance project not initiated yet.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ct-pillar-footer">
                                @if($dossierData['envProj'])
                                    <a href="{{ route('eviron.show', $dossierData['envProj']->id) }}" class="btn btn-success btn-sm w-100 fw-semibold text-white d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-arrow-up-right-circle"></i> Open EC Dossier &nearr;
                                    </a>
                                @else
                                    <a href="{{ route('eviron.create') }}" class="btn btn-success btn-sm w-100 fw-semibold text-white d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-plus-lg"></i> Start New EC Project
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pillar 4: EC Certificate & Compliance -->
                    <div class="col-lg-6">
                        <div class="ct-pillar-card">
                            <div class="ct-pillar-header">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="kpi-icon kpi-purple" style="width: 40px; height: 40px; font-size: 17px;">
                                        <i class="bi bi-patch-check-fill"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">4. EC Certificate & Compliance</h6>
                                        <small class="text-muted">Statutory Clearance Certificate & Expiry Tracking</small>
                                    </div>
                                </div>
                                @if($dossierData['ecCert'])
                                    <span class="ct-badge-{{ $dossierData['ecValidity']['badge'] ?? 'success' }}">
                                        {{ $dossierData['ecCert']->status === 'active' ? 'Active & Valid' : ucfirst($dossierData['ecCert']->status) }}
                                    </span>
                                @else
                                    <span class="ct-badge-neutral">Awaiting Grant</span>
                                @endif
                            </div>
                            <div class="ct-pillar-body">
                                @if($dossierData['ecCert'])
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-award me-1"></i>EC Reference No:</span>
                                        <span class="ct-field-val"><span class="badge bg-light text-dark border font-monospace">{{ $dossierData['ecCert']->ec_ref_no }}</span></span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-cloud-check me-1"></i>PARIVESH App No:</span>
                                        <span class="ct-field-val font-monospace text-info">{{ $dossierData['ecCert']->parivesh_app_no ?: '—' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-calendar-check me-1"></i>Issue Date:</span>
                                        <span class="ct-field-val">{{ $dossierData['ecCert']->issue_date ? $dossierData['ecCert']->issue_date->format('d M Y') : '—' }}</span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-shield-check me-1"></i>Validity Status:</span>
                                        <span class="ct-field-val text-{{ $dossierData['ecValidity']['badge'] ?? 'success' }}">
                                            {{ $dossierData['ecValidity']['text'] ?? 'Valid' }}
                                        </span>
                                    </div>
                                    <div class="ct-field-row">
                                        <span class="ct-field-label"><i class="bi bi-file-earmark-pdf me-1"></i>Official Certificate:</span>
                                        <span class="ct-field-val">
                                            @if($dossierData['ecCert']->certificate_file)
                                                <span class="badge bg-success text-white"><i class="bi bi-file-pdf me-1"></i>PDF Ready</span>
                                            @else
                                                <span class="text-muted">Not Uploaded</span>
                                            @endif
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted">
                                        <i class="bi bi-award text-muted" style="font-size: 34px;"></i>
                                        <p class="mb-2 mt-2 fs-13">EC Certificate has not been issued yet.</p>
                                    </div>
                                @endif
                            </div>
                            <div class="ct-pillar-footer">
                                @if($dossierData['ecCert'])
                                    <div class="d-flex gap-2">
                                        @if($dossierData['ecCert']->certificate_file)
                                            <a href="{{ asset($dossierData['ecCert']->certificate_file) }}" target="_blank" class="btn btn-success btn-sm w-100 fw-semibold text-white d-flex align-items-center justify-content-center gap-1 rounded-pill py-2 shadow-sm">
                                                <i class="bi bi-download"></i> Download PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('ec-certificate.show', $dossierData['ecCert']->id) }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-1 rounded-pill py-2 shadow-sm">
                                            <i class="bi bi-eye"></i> View Details &nearr;
                                        </a>
                                    </div>
                                @else
                                    <a href="{{ route('ec-certificate.step', 1) }}" class="btn btn-primary btn-sm w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 rounded-pill py-2 shadow-sm">
                                        <i class="bi bi-plus-lg"></i> Issue EC Certificate
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Consolidated Document Vault Card -->
                <div class="card border mb-4 shadow-sm" style="border-radius: 18px;">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="card-title fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <i class="bi bi-archive-fill text-primary"></i> Consolidated Document Vault
                            </h5>
                            <small class="text-muted">Centralized repository of all uploaded documents across Lease, Mining, and Environment modules</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Document Search Filter -->
                            <div class="input-group input-group-sm" style="width: 240px;">
                                <span class="input-group-text bg-light border"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="docVaultFilterInput" class="form-control border" placeholder="Search files...">
                            </div>
                            <span class="badge bg-primary fs-12 px-3 py-2 rounded-pill">
                                {{ $dossierData['allDocuments']->count() }} Files Total
                            </span>
                        </div>
                    </div>

                    <!-- Category Tab Chips -->
                    <div class="bg-light px-4 py-2 border-bottom no-print">
                        <div class="d-flex flex-wrap gap-2" id="docVaultTabs">
                            <button type="button" class="ct-filter-chip active" data-module="all">
                                All Documents <span class="chip-count">{{ $dossierData['allDocuments']->count() }}</span>
                            </button>
                            <button type="button" class="ct-filter-chip" data-module="lease">
                                Lease <span class="chip-count">{{ $dossierData['allDocuments']->where('module_code', 'lease')->count() }}</span>
                            </button>
                            <button type="button" class="ct-filter-chip" data-module="mining">
                                Mining Plan <span class="chip-count">{{ $dossierData['allDocuments']->where('module_code', 'mining')->count() }}</span>
                            </button>
                            <button type="button" class="ct-filter-chip" data-module="environment">
                                Environment <span class="chip-count">{{ $dossierData['allDocuments']->where('module_code', 'environment')->count() }}</span>
                            </button>
                            <button type="button" class="ct-filter-chip" data-module="ec">
                                EC Certificate <span class="chip-count">{{ $dossierData['allDocuments']->where('module_code', 'ec')->count() }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="ct-doc-table" id="docVaultTable">
                                <thead>
                                    <tr>
                                        <th class="ps-4" style="width: 50px;">#</th>
                                        <th>Document Title & Filename</th>
                                        <th>Module</th>
                                        <th>Folder / Category</th>
                                        <th>File Size</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4 no-print" style="width: 140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dossierData['allDocuments'] as $idx => $doc)
                                    <tr class="doc-row" data-module="{{ $doc['module_code'] }}" data-name="{{ strtolower($doc['name'] . ' ' . $doc['file_name']) }}">
                                        <td class="ps-4 text-muted fw-bold">{{ $idx + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="p-2 rounded bg-light border text-danger flex-shrink-0">
                                                    <i class="bi bi-file-earmark-pdf-fill fs-18"></i>
                                                </div>
                                                <div>
                                                    <strong class="text-dark d-block fs-13">{{ $doc['name'] }}</strong>
                                                    <small class="text-muted font-monospace">{{ $doc['file_name'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($doc['module_code'] === 'lease')
                                                <span class="ct-badge-primary">Lease</span>
                                            @elseif($doc['module_code'] === 'mining')
                                                <span class="ct-badge-warning">Mining Plan</span>
                                            @elseif($doc['module_code'] === 'environment')
                                                <span class="ct-badge-success">Environment</span>
                                            @else
                                                <span class="badge bg-purple text-white">EC Certificate</span>
                                            @endif
                                        </td>
                                        <td class="text-muted fs-13">{{ $doc['folder'] }}</td>
                                        <td class="text-muted fs-13 font-monospace">{{ $doc['file_size'] }}</td>
                                        <td class="text-muted fs-13">{{ $doc['date'] }}</td>
                                        <td>
                                            <span class="ct-badge-{{ $doc['status'] === 'approved' || $doc['status'] === 'active' || $doc['status'] === 'validated' ? 'success' : 'neutral' }}">
                                                {{ ucfirst($doc['status'] ?: 'Uploaded') }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 no-print">
                                            @if($doc['file_path'])
                                                <a href="{{ asset($doc['file_path']) }}" target="_blank" class="btn btn-sm btn-light border text-primary fw-medium px-3 shadow-sm rounded-pill">
                                                    <i class="bi bi-eye me-1"></i> View / Download &nearr;
                                                </a>
                                            @else
                                                <span class="text-muted fs-12">No File</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="bi bi-folder-x fs-32 d-block mb-2"></i>
                                            No documents uploaded yet for this customer.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End printable dossier area -->
        @endif

    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('universalSearchInput');
    const searchDropdown = document.getElementById('searchResultsDropdown');
    const clearBtn = document.getElementById('searchClearBtn');
    let debounceTimer = null;

    // Toggle clear button
    function checkClearBtn() {
        if (searchInput && searchInput.value.trim().length > 0) {
            clearBtn.style.display = 'block';
        } else if (clearBtn) {
            clearBtn.style.display = 'none';
        }
    }
    checkClearBtn();

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            checkClearBtn();
            searchDropdown.style.display = 'none';
            searchInput.focus();
        });
    }

    // Keyboard shortcut: Ctrl + K or '/' to focus search
    document.addEventListener('keydown', function(e) {
        if (searchInput && ((e.ctrlKey && e.key === 'k') || (e.key === '/' && document.activeElement !== searchInput))) {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });

    // Debounced Live AJAX Autocomplete
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            checkClearBtn();
            const query = this.value.trim();

            clearTimeout(debounceTimer);
            if (query.length < 2) {
                searchDropdown.style.display = 'none';
                searchDropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                searchDropdown.innerHTML = `
                    <div class="p-3 text-center text-muted fs-13">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        Searching customer database...
                    </div>
                `;
                searchDropdown.style.display = 'block';

                fetch(`{{ route('customer-tracking.search') }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.results || data.results.length === 0) {
                        searchDropdown.innerHTML = `
                            <div class="p-4 text-center text-muted fs-13">
                                <i class="bi bi-info-circle fs-20 d-block mb-1 text-secondary"></i>
                                No matching customers found for "<strong>${query}</strong>".
                                <div class="mt-1 fs-12 text-muted">Try searching by 10-digit mobile, Customer Unique ID, or Aadhaar.</div>
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    data.results.forEach((c, idx) => {
                        const avatarClass = 'ct-avatar-' + (idx % 4);
                        html += `
                            <div class="ct-dropdown-item" onclick="window.location.href='${c.url}'">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="ct-avatar-gradient ${avatarClass}" style="width: 40px; height: 40px; font-size: 15px; border-radius: 10px;">
                                        ${c.name.substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14">${c.name}</div>
                                        <div class="text-muted fs-12 d-flex flex-wrap align-items-center gap-1 mt-1">
                                            <span>${c.company || 'Individual'}</span>
                                            <span>&bull;</span>
                                            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i>${c.district}</span>
                                            ${c.aadhaar ? `<span>&bull;</span><span class="badge bg-light text-secondary border"><i class="bi bi-person-vcard me-1"></i>${c.aadhaar}</span>` : ''}
                                            ${c.mobile ? `<span>&bull;</span><span class="badge bg-light text-muted border"><i class="bi bi-telephone me-1"></i>${c.mobile}</span>` : ''}
                                            ${c.secondary_mobile ? `<span>&bull;</span><span class="badge bg-light text-muted border"><i class="bi bi-telephone-plus me-1"></i>Sec: ${c.secondary_mobile}</span>` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="ct-badge-unique-id mb-1 py-1 px-2 fs-11">
                                        <i class="bi bi-shield-check"></i> ${c.unique_id || c.mimas_no || 'No ID'}
                                    </span>
                                    <div class="fs-11 text-muted">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">${c.active_stage}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    searchDropdown.innerHTML = html;
                })
                .catch(err => {
                    console.error('Tracking search error:', err);
                    searchDropdown.innerHTML = `
                        <div class="p-3 text-center text-danger fs-13">
                            <i class="bi bi-exclamation-triangle me-1"></i> An error occurred while searching.
                        </div>
                    `;
                });
            }, 250);
        });
    }

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        if (searchInput && searchDropdown && !searchInput.contains(e.target) && !searchDropdown.contains(e.target)) {
            searchDropdown.style.display = 'none';
        }
    });

    // Quick Search Pills interaction
    document.querySelectorAll('.quick-search-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            const val = this.getAttribute('data-query');
            if (val === 'Phone') {
                searchInput.value = '';
                searchInput.placeholder = 'Type primary or secondary 10-digit mobile number...';
                searchInput.focus();
                return;
            }
            if (val === 'Quarry') {
                searchInput.value = '';
                searchInput.placeholder = 'Type company or quarry project name...';
                searchInput.focus();
                return;
            }
            if (val === 'Aadhaar') {
                searchInput.value = '';
                searchInput.placeholder = 'Type 12-digit Aadhaar number (with or without spaces/hyphens)...';
                searchInput.focus();
                return;
            }
            searchInput.value = val;
            checkClearBtn();
            searchInput.focus();
            searchInput.dispatchEvent(new Event('input'));
        });
    });

    // Document Vault Tab Filtering
    const filterTabs = document.querySelectorAll('.ct-filter-chip');
    const docRows = document.querySelectorAll('.doc-row');
    const docSearchInput = document.getElementById('docVaultFilterInput');

    function applyDocFilter() {
        const activeTab = document.querySelector('.ct-filter-chip.active');
        const selectedModule = activeTab ? activeTab.getAttribute('data-module') : 'all';
        const searchVal = docSearchInput ? docSearchInput.value.toLowerCase().trim() : '';

        docRows.forEach(row => {
            const rowModule = row.getAttribute('data-module');
            const rowName = row.getAttribute('data-name');

            const matchesModule = (selectedModule === 'all' || rowModule === selectedModule);
            const matchesSearch = (!searchVal || rowName.includes(searchVal));

            if (matchesModule && matchesSearch) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (filterTabs.length > 0) {
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                filterTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                applyDocFilter();
            });
        });
    }

    if (docSearchInput) {
        docSearchInput.addEventListener('input', applyDocFilter);
    }
});
</script>
@endsection
@endsection
