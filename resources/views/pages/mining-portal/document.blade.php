@extends('layouts.app')
@section('title', 'Documents — ' . ($application->application_no ?? 'Mining Dossier'))
@section('main_content')

<style>
/* CSS Variables matching GTMS Executive Design System */
:root {
    --doc-surface: #ffffff;
    --doc-surface-subtle: #f8fafc;
    --doc-border: #e2e8f0;
    --doc-border-subtle: #f1f5f9;
    --doc-text-primary: #0f172a;
    --doc-text-secondary: #475569;
    --doc-text-muted: #94a3b8;
    --doc-navy-gradient: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    --doc-shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.03);
    --doc-shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06), 0 2px 4px rgba(15, 23, 42, 0.04);
}

body[data-theme-version="dark"] {
    --doc-surface: #1e222d;
    --doc-surface-subtle: #161922;
    --doc-border: rgba(255, 255, 255, 0.1);
    --doc-border-subtle: rgba(255, 255, 255, 0.05);
    --doc-text-primary: #f8fafc;
    --doc-text-secondary: #cbd5e1;
    --doc-text-muted: #64748b;
    --doc-navy-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    --doc-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
    --doc-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
}

/* Panel Containers */
.doc-panel {
    background: var(--doc-surface);
    border: 1px solid var(--doc-border);
    border-radius: 14px;
    box-shadow: var(--doc-shadow-sm);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Executive Header Bar */
.doc-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
}

.doc-btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--doc-text-secondary) !important;
    background: var(--doc-surface-subtle);
    border: 1px solid var(--doc-border);
    border-radius: 7px;
    text-decoration: none !important;
    transition: all 0.15s ease;
}

.doc-btn-back:hover {
    color: var(--doc-text-primary) !important;
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(59, 130, 246, 0.4);
}

body[data-theme-version="dark"] .doc-btn-back:hover {
    background: rgba(255, 255, 255, 0.08);
}

.doc-app-crumb-badge {
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

body[data-theme-version="dark"] .doc-app-crumb-badge {
    background: #1e3a8a !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
}

.doc-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--doc-text-primary);
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.doc-sub {
    font-size: 0.84rem;
    color: var(--doc-text-secondary);
    margin: 0;
}

/* Meta Chips */
.doc-meta-chip {
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: var(--doc-surface-subtle);
    color: var(--doc-text-secondary);
    border: 1px solid var(--doc-border);
    white-space: nowrap;
}

.doc-meta-chip strong {
    color: var(--doc-text-primary);
    font-weight: 700;
}

.doc-meta-chip.chip-cust {
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb;
    border-color: rgba(59, 130, 246, 0.25);
}

body[data-theme-version="dark"] .doc-meta-chip.chip-cust {
    background: rgba(59, 130, 246, 0.18);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.35);
}

/* Action Buttons */
.doc-btn-outline-subtle {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 0.78rem;
    font-weight: 600;
    padding: 0 0.85rem;
    height: 38px;
    border-radius: 8px;
    background: var(--doc-surface-subtle);
    color: var(--doc-text-secondary) !important;
    border: 1px solid var(--doc-border);
    text-decoration: none !important;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.doc-btn-outline-subtle:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--doc-text-primary) !important;
    border-color: rgba(59, 130, 246, 0.4);
}

body[data-theme-version="dark"] .doc-btn-outline-subtle:hover {
    background: rgba(255, 255, 255, 0.08);
}

.doc-btn-workflow {
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%) !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.78rem;
    padding: 0 0.95rem;
    height: 38px;
    border-radius: 8px;
    border: none !important;
    box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    text-decoration: none !important;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    white-space: nowrap;
}

.doc-btn-workflow:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(79, 70, 229, 0.4);
    color: #ffffff !important;
}

/* App Switcher */
.doc-app-switcher-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: var(--doc-surface-subtle);
    border: 1px solid var(--doc-border);
    border-radius: 8px;
    padding: 0 0.5rem 0 0.75rem;
    height: 38px;
    box-sizing: border-box;
    transition: border-color 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
}

.doc-app-switcher-wrap:hover {
    border-color: rgba(59, 130, 246, 0.4);
}

.doc-app-switcher-wrap:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    background: var(--doc-surface);
}

.doc-app-switcher-label {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--doc-text-secondary);
    white-space: nowrap !important;
    margin: 0;
    padding: 0;
    line-height: 1;
    user-select: none;
    flex-shrink: 0;
}

.doc-app-switcher-select {
    border: none !important;
    background: transparent !important;
    color: var(--doc-text-primary) !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    font-family: inherit !important;
    padding: 0 1.6rem 0 0.35rem !important;
    min-width: 240px;
    max-width: 340px;
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

body[data-theme-version="dark"] .doc-app-switcher-select {
    color: #f1f5f9 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
}

.doc-app-switcher-select option {
    background-color: var(--doc-surface) !important;
    color: var(--doc-text-primary) !important;
    font-size: 0.8rem;
    padding: 0.4rem;
}

/* Left Sidebar Rail */
.doc-rail-header {
    padding: 0.65rem 0.85rem;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--doc-text-muted);
    border-bottom: 1px solid var(--doc-border-subtle);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.doc-rail-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.65rem 0.85rem;
    border-radius: 9px;
    text-decoration: none !important;
    color: var(--doc-text-secondary);
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.15s ease;
    margin-bottom: 0.25rem;
}

.doc-rail-item:hover {
    background: rgba(0, 0, 0, 0.04);
    color: var(--doc-text-primary);
}

body[data-theme-version="dark"] .doc-rail-item:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
}

.doc-rail-item.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.25);
}

body[data-theme-version="dark"] .doc-rail-item.active {
    background: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35);
}

.doc-rail-icon {
    font-size: 1rem;
    margin-right: 0.5rem;
    color: #3b82f6;
}

.doc-rail-item.active .doc-rail-icon {
    color: #ffffff !important;
}

.doc-rail-badge {
    font-size: 0.69rem !important;
    font-weight: 700 !important;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 6px !important;
    line-height: 1.2 !important;
}

.doc-rail-item .doc-rail-badge.badge-done {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac;
}

.doc-rail-item .doc-rail-badge.badge-partial {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #7dd3fc;
}

.doc-rail-item .doc-rail-badge.badge-pending {
    background: var(--doc-surface-subtle) !important;
    color: var(--doc-text-muted) !important;
    border: 1px solid var(--doc-border);
}

.doc-rail-item.active .doc-rail-badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
}

/* Right Checklist Panel Header */
.doc-panel-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    padding-bottom: 0.85rem;
    margin-bottom: 1rem;
    border-bottom: 1px solid var(--doc-border);
}

/* Checklist Filter / Search Bar */
.doc-filter-bar {
    display: flex;
    align-items: center;
    gap: 0.65rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.doc-search-wrap {
    position: relative;
    flex: 1 1 220px;
}

.doc-search-icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--doc-text-muted);
    font-size: 0.85rem;
    pointer-events: none;
}

.doc-search-input {
    width: 100%;
    height: 38px;
    padding: 0.4rem 2rem 0.4rem 2.3rem;
    font-size: 0.82rem;
    background: var(--doc-surface-subtle);
    border: 1px solid var(--doc-border);
    border-radius: 8px;
    color: var(--doc-text-primary);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.doc-search-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    background: var(--doc-surface);
}

.doc-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.75rem;
    font-size: 0.76rem;
    font-weight: 600;
    border-radius: 7px;
    border: 1px solid var(--doc-border);
    background: var(--doc-surface-subtle);
    color: var(--doc-text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.doc-filter-pill:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--doc-text-primary);
}

body[data-theme-version="dark"] .doc-filter-pill:hover {
    background: rgba(255, 255, 255, 0.08);
}

.doc-filter-pill.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
}

body[data-theme-version="dark"] .doc-filter-pill.active {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #3b82f6 !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
}

/* Document Item Card */
.doc-card-item {
    background: var(--doc-surface-subtle);
    border: 1px solid var(--doc-border);
    border-radius: 10px;
    padding: 0.9rem 1.15rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.85rem;
    transition: all 0.15s ease;
}

.doc-card-item:hover {
    border-color: rgba(59, 130, 246, 0.4);
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
}

/* Document Squircle Icons */
.doc-icon-squircle {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.item-validated .doc-icon-squircle {
    background: #dcfce7 !important;
    color: #166534 !important;
    border: 1px solid #86efac;
}

.item-uploaded .doc-icon-squircle {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #7dd3fc;
}

.item-flagged .doc-icon-squircle {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border: 1px solid #fca5a5;
}

.item-pending .doc-icon-squircle {
    background: var(--doc-surface) !important;
    color: #64748b !important;
    border: 1px solid var(--doc-border);
}

body[data-theme-version="dark"] .item-validated .doc-icon-squircle {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
}

body[data-theme-version="dark"] .item-uploaded .doc-icon-squircle {
    background: rgba(2, 132, 199, 0.2) !important;
    color: #7dd3fc !important;
    border-color: rgba(2, 132, 199, 0.35) !important;
}

body[data-theme-version="dark"] .item-flagged .doc-icon-squircle {
    background: rgba(220, 38, 38, 0.2) !important;
    color: #fca5a5 !important;
    border-color: rgba(220, 38, 38, 0.35) !important;
}

body[data-theme-version="dark"] .item-pending .doc-icon-squircle {
    background: rgba(255, 255, 255, 0.06) !important;
    color: #94a3b8 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
}

/* Document Status Badges */
.doc-status-badge {
    font-size: 0.72rem !important;
    font-weight: 700 !important;
    padding: 0.25rem 0.65rem !important;
    border-radius: 6px !important;
    white-space: nowrap !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

.doc-status-badge.badge-validated {
    background: #dcfce7 !important;
    color: #15803d !important;
    border: 1px solid #86efac !important;
}

.doc-status-badge.badge-uploaded {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #7dd3fc !important;
}

.doc-status-badge.badge-flagged {
    background: #fee2e2 !important;
    color: #b91c1c !important;
    border: 1px solid #fca5a5 !important;
}

.doc-status-badge.badge-pending {
    background: var(--doc-surface) !important;
    color: #64748b !important;
    border: 1px solid var(--doc-border) !important;
}

body[data-theme-version="dark"] .doc-status-badge.badge-validated {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
}

body[data-theme-version="dark"] .doc-status-badge.badge-uploaded {
    background: rgba(2, 132, 199, 0.2) !important;
    color: #7dd3fc !important;
    border-color: rgba(2, 132, 199, 0.35) !important;
}

body[data-theme-version="dark"] .doc-status-badge.badge-flagged {
    background: rgba(220, 38, 38, 0.2) !important;
    color: #fca5a5 !important;
    border-color: rgba(220, 38, 38, 0.35) !important;
}

body[data-theme-version="dark"] .doc-status-badge.badge-pending {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

/* Action Buttons for Items */
.doc-btn-upload {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.76rem;
    font-weight: 600;
    padding: 0.35rem 0.75rem;
    border-radius: 7px;
    background: rgba(59, 130, 246, 0.08);
    color: #2563eb !important;
    border: 1px solid rgba(59, 130, 246, 0.3);
    cursor: pointer;
    transition: all 0.15s ease;
    white-space: nowrap;
}

.doc-btn-upload:hover {
    background: #2563eb;
    color: #ffffff !important;
    border-color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
}

body[data-theme-version="dark"] .doc-btn-upload {
    background: rgba(59, 130, 246, 0.18);
    color: #93c5fd !important;
    border-color: rgba(59, 130, 246, 0.35);
}

body[data-theme-version="dark"] .doc-btn-upload:hover {
    background: #3b82f6;
    color: #ffffff !important;
}

.doc-btn-view {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 7px;
    background: var(--doc-surface);
    color: var(--doc-text-secondary) !important;
    border: 1px solid var(--doc-border);
    text-decoration: none !important;
    transition: all 0.15s ease;
}

.doc-btn-view:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--doc-text-primary) !important;
    border-color: rgba(59, 130, 246, 0.4);
}

body[data-theme-version="dark"] .doc-btn-view:hover {
    background: rgba(255, 255, 255, 0.08);
}
</style>

<div class="content-body default-height">
    <div class="container-fluid">

        {{-- Session Alerts --}}
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

        @if(!$application)
            <div class="doc-panel p-5 text-center">
                <div class="fs-1 text-muted mb-2"><i class="bi bi-folder-x"></i></div>
                <h4 class="fw-bold text-dark">No Application Selected</h4>
                <p class="text-muted small">Select a mining application to view and manage its statutory document checklist.</p>
                <div><a href="{{ route('process') }}" class="btn btn-navy">Back to Process Registry</a></div>
            </div>
        @else

            <!-- Executive Dossier Header Bar -->
            <div class="doc-panel doc-header-bar">
                <div style="flex: 1 1 300px;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <a href="{{ route('projectfolder', ['id' => $application->id]) }}" class="doc-btn-back">
                            <i class="bi bi-arrow-left"></i> Folder Dossier
                        </a>
                        <span class="text-muted small">/</span>
                        <span class="doc-app-crumb-badge">{{ $application->application_no }}</span>
                    </div>

                    <h1 class="doc-title mb-1 d-flex align-items-center flex-wrap gap-2">
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
                        <span class="badge text-white px-3 py-1 font-monospace fw-bold" style="background: var(--doc-navy-gradient); font-size: .8rem;">
                            <i class="bi bi-fingerprint me-1 text-warning"></i> Universal Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y', strtotime($application->created_at ?? 'now')) . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
                        </span>

                        @if($application->customer?->mimas_no)
                            <span class="doc-meta-chip chip-cust" title="Customer Unique ID">
                                <i class="bi bi-person-badge"></i> Cust ID: <strong>{{ $application->customer->mimas_no }}</strong>
                            </span>
                        @endif

                        <span class="doc-meta-chip">
                            <i class="bi bi-file-earmark-text"></i> Mining App: <strong>{{ $application->application_no }}</strong>
                        </span>

                        <span class="doc-meta-chip">
                            <i class="bi bi-folder-check text-primary"></i> Total Uploaded: <strong>{{ $uploadedAppDocs ?? 0 }} / {{ $totalAppDocs ?? 0 }}</strong>
                        </span>
                    </div>
                </div>

                <!-- App Switcher & Navigation Buttons -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(isset($allApplications) && $allApplications->count() > 1)
                        <div class="doc-app-switcher-wrap">
                            <label class="doc-app-switcher-label" for="selectDocSwitchApp">
                                <i class="bi bi-arrow-left-right text-primary"></i>
                                <span>Switch App:</span>
                            </label>
                            <select id="selectDocSwitchApp" class="doc-app-switcher-select" onchange="if(this.value) window.location.href='/document?id='+this.value;">
                                @foreach($allApplications as $optApp)
                                    <option value="{{ $optApp->id }}" {{ $optApp->id == $application->id ? 'selected' : '' }}>
                                        {{ $optApp->application_no }} &mdash; {{ $optApp->customer->company_name ?? $optApp->customer->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <a href="{{ route('projectfolder', ['id' => $application->id]) }}" class="doc-btn-outline-subtle">
                        <i class="bi bi-folder2"></i> Folder Dossier
                    </a>

                    <a href="{{ route('process', ['id' => $application->id]) }}" class="doc-btn-workflow">
                        <i class="bi bi-diagram-3"></i> Stage 6 Process Flow &rarr;
                    </a>
                </div>
            </div>

            <!-- Main Layout: Folder Rail (Left) + Document Checklist (Right) -->
            <div class="row g-3">
                <!-- Left Folder Tab Rail -->
                <div class="col-lg-3">
                    <div class="doc-panel p-2">
                        <div class="doc-rail-header">
                            <span><i class="bi bi-folder2-open text-primary me-1"></i> Statutory Folders</span>
                            <span class="badge bg-light text-muted border font-monospace">{{ $folders->count() }}</span>
                        </div>
                        <div class="p-1">
                            @foreach($folders as $folder)
                                @php
                                    $fStat = $folderCounts[$folder->id] ?? ['total' => 0, 'uploaded' => 0];
                                    $isActive = ($activeFolder && $activeFolder->id == $folder->id);
                                @endphp
                                <a href="{{ route('document', ['id' => $application->id, 'folder' => $folder->id]) }}" 
                                   class="doc-rail-item {{ $isActive ? 'active' : '' }}">
                                    <div class="d-flex align-items-center min-w-0">
                                        <i class="bi bi-folder{{ $isActive ? '-fill' : '' }} doc-rail-icon"></i>
                                        <span class="text-truncate">{{ $folder->name }}</span>
                                    </div>
                                    @if($fStat['uploaded'] >= $fStat['total'] && $fStat['total'] > 0)
                                        <span class="doc-rail-badge badge-done" title="Completed">
                                            {{ $fStat['uploaded'] }}/{{ $fStat['total'] }}
                                        </span>
                                    @elseif($fStat['uploaded'] > 0)
                                        <span class="doc-rail-badge badge-partial" title="In Progress">
                                            {{ $fStat['uploaded'] }}/{{ $fStat['total'] }}
                                        </span>
                                    @else
                                        <span class="doc-rail-badge badge-pending" title="Pending">
                                            {{ $fStat['uploaded'] }}/{{ $fStat['total'] }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Document Checklist Panel -->
                <div class="col-lg-9">
                    <div class="doc-panel p-3 p-lg-4">
                        @php
                            $uploadedInFolder = $documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count();
                            $totalInFolder = count($documents);
                            $validatedInFolder = $documents->whereIn('status', ['validated', 'approved'])->count();
                            $flaggedInFolder = $documents->where('status', 'revision_required')->count();
                            $pendingInFolder = $totalInFolder - $uploadedInFolder;
                        @endphp

                        <!-- Header Bar for Active Folder -->
                        <div class="doc-panel-header">
                            <div>
                                <h2 class="h5 fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-folder2-open text-primary"></i> {{ $activeFolder->name ?? 'Folder' }}
                                </h2>
                                <span class="text-muted small">
                                    {{ $totalInFolder }} statutory checklist items assigned for {{ $application->natureOfWork->name ?? 'Mining Plan' }}
                                </span>
                            </div>

                            <div>
                                @if($uploadedInFolder >= $totalInFolder && $totalInFolder > 0)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fw-bold font-monospace" style="font-size:0.8rem;">
                                        <i class="bi bi-check2-all me-1"></i> All {{ $totalInFolder }} Uploaded
                                    </span>
                                @else
                                    <span class="badge px-3 py-2 font-monospace fw-bold" style="background:#fef3c7; color:#b45309; border:1px solid #fcd34d; font-size:0.8rem;">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> {{ $uploadedInFolder }} / {{ $totalInFolder }} Uploaded
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Filter Toolbar -->
                        <div class="doc-filter-bar">
                            <div class="doc-search-wrap">
                                <i class="bi bi-search doc-search-icon"></i>
                                <input type="text" id="inputSearchChecklist" class="doc-search-input" placeholder="Search checklist documents in this folder...">
                            </div>
                            <div class="d-flex gap-1 flex-wrap">
                                <button type="button" class="doc-filter-pill active" data-filter="all">All ({{ $totalInFolder }})</button>
                                <button type="button" class="doc-filter-pill" data-filter="uploaded">Uploaded ({{ $uploadedInFolder }})</button>
                                <button type="button" class="doc-filter-pill" data-filter="pending">Pending ({{ $pendingInFolder }})</button>
                                @if($flaggedInFolder > 0)
                                    <button type="button" class="doc-filter-pill" data-filter="flagged">Flagged ({{ $flaggedInFolder }})</button>
                                @endif
                            </div>
                        </div>

                        @if(count($documents) === 0)
                            <div class="p-5 text-center text-muted">
                                <div class="fs-1 text-secondary mb-2"><i class="bi bi-folder-check"></i></div>
                                <h6 class="fw-bold text-dark">No documents required</h6>
                                <p class="small text-muted mb-0">No statutory document checklist required under <strong>{{ $activeFolder->name }}</strong> for <strong>{{ $application->natureOfWork->name ?? 'this nature of work' }}</strong>.</p>
                            </div>
                        @else
                            <div class="d-flex flex-column gap-2" id="listFolderDocs">
                                @foreach($documents as $doc)
                                    @php
                                        $dStatus = $doc->status ?? 'pending';
                                        $itemClass = match($dStatus) {
                                            'validated', 'approved' => 'item-validated',
                                            'uploaded' => 'item-uploaded',
                                            'revision_required' => 'item-flagged',
                                            default => 'item-pending',
                                        };
                                        $filterTag = match($dStatus) {
                                            'validated', 'approved', 'uploaded' => 'uploaded',
                                            'revision_required' => 'flagged',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <div class="doc-card-item {{ $itemClass }}" 
                                         data-doc-name="{{ strtolower($doc->document_name) }}"
                                         data-doc-file="{{ strtolower($doc->file_name ?? '') }}"
                                         data-filter-status="{{ $filterTag }}">
                                        
                                        <div class="d-flex align-items-center gap-3 min-w-0">
                                            <div class="doc-icon-squircle">
                                                @if($dStatus === 'validated' || $dStatus === 'approved')
                                                    <i class="bi bi-check-circle-fill"></i>
                                                @elseif($dStatus === 'uploaded')
                                                    <i class="bi bi-file-earmark-check-fill"></i>
                                                @elseif($dStatus === 'revision_required')
                                                    <i class="bi bi-exclamation-octagon-fill"></i>
                                                @else
                                                    <i class="bi bi-hourglass-split"></i>
                                                @endif
                                            </div>

                                            <div class="min-w-0">
                                                <div class="fw-bold small text-dark text-truncate">{{ $doc->document_name }}</div>
                                                <div class="text-muted" style="font-size:11.5px;">
                                                    @if($doc->file_name)
                                                        <span class="text-primary fw-semibold">{{ $doc->file_name }}</span> &middot; 
                                                        <span>{{ round(($doc->file_size ?? 0) / 1024, 1) }} KB</span> &middot; 
                                                        <span>Uploaded {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : '' }}</span>
                                                    @else
                                                        <span class="text-muted"><i class="bi bi-clock me-1"></i>File upload pending</span>
                                                    @endif
                                                </div>
                                                @if($doc->review_note)
                                                    <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1 text-wrap text-start" style="font-size:11px;">
                                                        <i class="bi bi-chat-left-text me-1"></i> Note: {{ $doc->review_note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                            @if($dStatus === 'validated' || $dStatus === 'approved')
                                                <span class="doc-status-badge badge-validated">
                                                    <i class="bi bi-check-circle-fill"></i> Validated
                                                </span>
                                            @elseif($dStatus === 'uploaded')
                                                <span class="doc-status-badge badge-uploaded">
                                                    <i class="bi bi-file-earmark-arrow-up-fill"></i> Uploaded
                                                </span>
                                            @elseif($dStatus === 'revision_required')
                                                <span class="doc-status-badge badge-flagged">
                                                    <i class="bi bi-exclamation-octagon-fill"></i> Flagged
                                                </span>
                                            @else
                                                <span class="doc-status-badge badge-pending">
                                                    <i class="bi bi-hourglass-split"></i> Pending
                                                </span>
                                            @endif

                                            <!-- Upload Form -->
                                            <form method="POST" action="{{ route('mining.document.upload') }}" enctype="multipart/form-data" class="d-inline-flex align-items-center form-mining-upload m-0">
                                                @csrf
                                                <input type="hidden" name="mining_application_id" value="{{ $application->id }}">
                                                <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                                <label class="doc-btn-upload mb-0 btn-upload-label" title="Choose and upload file">
                                                    <i class="bi bi-upload"></i>
                                                    <span class="upload-text">{{ $doc->file_name ? 'Re-upload' : 'Upload' }}</span>
                                                    <input type="file" name="file" class="d-none input-mining-file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.kml,.kmz,.zip,.dwg,.dxf">
                                                </label>
                                            </form>

                                            @if($doc->file_path)
                                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="doc-btn-view" title="View uploaded file">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @endif

    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // -------------------------------------------------------------
    // Client-side real-time search & status filtering for checklist
    // -------------------------------------------------------------
    var currentFilter = 'all';

    function filterChecklistItems() {
        var query = ($('#inputSearchChecklist').val() || '').toLowerCase().trim();

        $('.doc-card-item').each(function() {
            var $item = $(this);
            var docName = $item.data('doc-name') || '';
            var docFile = $item.data('doc-file') || '';
            var docStatus = $item.data('filter-status') || '';

            var matchQ = (query === '' || docName.indexOf(query) > -1 || docFile.indexOf(query) > -1);
            var matchStatus = (currentFilter === 'all' || docStatus === currentFilter);

            if (matchQ && matchStatus) {
                $item.show();
            } else {
                $item.hide();
            }
        });
    }

    $('#inputSearchChecklist').on('input', function() {
        filterChecklistItems();
    });

    $('.doc-filter-pill').on('click', function() {
        $('.doc-filter-pill').removeClass('active');
        $(this).addClass('active');
        currentFilter = $(this).data('filter');
        filterChecklistItems();
    });

    // -------------------------------------------------------------
    // File upload handler with size verification
    // -------------------------------------------------------------
    $(document).on('change', '.input-mining-file', function() {
        var file = this.files[0];
        if (!file) return;

        var maxSize = 25 * 1024 * 1024; // 25MB
        if (file.size > maxSize) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Too Large',
                    text: 'The selected file size is ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB. Maximum allowed size is 25 MB.',
                    confirmButtonColor: '#0F1E4D'
                });
            } else {
                alert('The selected file size is ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB. Maximum allowed size is 25 MB.');
            }
            $(this).val('');
            return;
        }

        var form = $(this).closest('form');
        var label = form.find('.btn-upload-label');
        label.addClass('disabled').css('pointer-events', 'none');
        label.find('.upload-text').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Uploading...');
        form.submit();
    });
});
</script>
@endsection
