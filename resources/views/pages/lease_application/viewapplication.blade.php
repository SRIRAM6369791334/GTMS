@extends('layouts.app')
@section('title', 'Lease Application Dossier #' . ($application->application_no ?? 'LA-2026-0001'))
@section('main_content')

@php
  $docs = $application->documents ?? collect();
  $regUploaded = $docs->whereIn('folder_id', [7, 8])->whereNotNull('file_path')->count();
  $regTotal = 16;
  $planUploaded = $docs->where('folder_id', 9)->whereNotNull('file_path')->count();
  $planTotal = 3;
  $totalUploaded = $docs->whereNotNull('file_path')->count();
  $validatedCount = $docs->where('status', 'validated')->count();
  $allValidated = ($totalUploaded > 0 && $validatedCount >= $totalUploaded);
  $mimas = $application->mimasCredentials ? $application->mimasCredentials->first() : null;
  $sortedDocs = $docs->sortBy(function($d) {
      $num = preg_replace('/\D/', '', explode('.', $d->document_name)[0] ?? '');
      return $num !== '' ? (int)$num : 99999;
  });
  $stageDuration = $application->updated_at
      ? $application->updated_at->diffForHumans(null, true)
      : ($application->created_at ? $application->created_at->diffForHumans(null, true) : 'Active');
  $status = $application->status ?? 'under_scrutiny';
  $isApproved = ($status === 'approved');
  $isValidated = in_array($status, ['validated', 'approved']);
  $isRevision = ($status === 'revision_required');
  $linkedMining = $application->miningApplications ? $application->miningApplications->first() : null;
  $pStatus = $application->payment_status ?? 'pending';

  $mineralNames = $application->minerals && $application->minerals->isNotEmpty()
      ? $application->minerals->pluck('name')->toArray()
      : ($application->mineral ? [$application->mineral->name] : []);
  if (!empty($application->other_mineral_name)) {
      $mineralNames[] = 'Other: ' . $application->other_mineral_name;
  }
  $mineralDisplay = !empty($mineralNames) ? implode(', ', $mineralNames) : 'Not specified';
  $survey = $application->surveyNumbers ? $application->surveyNumbers->first() : null;
  $registeredAddress = $application->customer?->address ?? ($application->taluk ?? null);
  $commonId = $application->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT));
  $allAllowedExt = '.pdf,.png,.jpg,.jpeg,.webp,.svg,.gif,.bmp,.zip,.rar,.7z,.tar,.gz,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.csv,.rtf,.kml,.kmz,.xml,.dwg,.dxf,.mp4,.webm,.mkv,.avi,.mov';
@endphp

<!-- DOSSIER BOOK DESIGN SYSTEM STYLES (UI/UX PRO MAX STANDARD) -->
<style>
  :root, body:not([data-theme-version="dark"]) {
    --dossier-bg: #f8fafc;
    --dossier-card-bg: #ffffff;
    --dossier-card-header: #f8fafc;
    --dossier-card-border: #e2e8f0;
    --dossier-card-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.02);
    --dossier-card-hover: 0 8px 24px -4px rgba(15, 23, 42, 0.08);

    --dossier-text-title: #0f172a;
    --dossier-text-body: #1e293b;
    --dossier-text-muted: #64748b;
    --dossier-text-dim: #94a3b8;

    --dossier-navy: #0f1e4d;
    --dossier-navy-soft: #eff6ff;
    --dossier-navy-border: #bfdbfe;

    --dossier-table-th: #f8fafc;
    --dossier-table-stripe: #fcfdfe;
    --dossier-table-border: #edf2f7;
    --dossier-table-hover: #f1f5f9;

    --dossier-chip-bg: #f1f5f9;
    --dossier-chip-border: #cbd5e1;
    --dossier-chip-text: #334155;

    --dossier-action-btn-bg: #ffffff;
    --dossier-action-btn-border: #cbd5e1;
    --dossier-action-btn-color: #334155;
    --dossier-action-btn-hover: #f1f5f9;

    --dossier-book-spine: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    --dossier-book-ribbon: #d97706;
  }

  body[data-theme-version="dark"],
  [data-theme-version="dark"] {
    --dossier-bg: #121316;
    --dossier-card-bg: #1a1c23;
    --dossier-card-header: #21242d;
    --dossier-card-border: #2c303c;
    --dossier-card-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    --dossier-card-hover: 0 10px 30px rgba(0, 0, 0, 0.6);

    --dossier-text-title: #f8fafc;
    --dossier-text-body: #e2e8f0;
    --dossier-text-muted: #94a3b8;
    --dossier-text-dim: #64748b;

    --dossier-navy: #60a5fa;
    --dossier-navy-soft: rgba(59, 130, 246, 0.15);
    --dossier-navy-border: rgba(59, 130, 246, 0.35);

    --dossier-table-th: #21242d;
    --dossier-table-stripe: #1d2028;
    --dossier-table-border: #2b2f3a;
    --dossier-table-hover: #262934;

    --dossier-chip-bg: #222631;
    --dossier-chip-border: #373d4d;
    --dossier-chip-text: #e2e8f0;

    --dossier-action-btn-bg: #222631;
    --dossier-action-btn-border: #373d4d;
    --dossier-action-btn-color: #e2e8f0;
    --dossier-action-btn-hover: #2c313f;

    --dossier-book-spine: linear-gradient(135deg, #0b1329 0%, #172554 100%);
    --dossier-book-ribbon: #f59e0b;
  }

  /* Book Shell Container */
  .dossier-book-shell {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--dossier-text-body);
  }

  /* Executive Book Banner */
  .dossier-book-masthead {
    background: var(--dossier-card-bg);
    border: 1px solid var(--dossier-card-border);
    border-radius: 16px;
    padding: 1.5rem 1.75rem;
    box-shadow: var(--dossier-card-shadow);
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }
  .dossier-book-masthead::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 6px;
    /* background: var(--dossier-book-spine); */
  }

  .dossier-badge-crest {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    background: var(--dossier-navy-soft);
    color: var(--dossier-navy);
    border: 1px solid var(--dossier-navy-border);
  }

  .dossier-title-main {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--dossier-text-title);
    letter-spacing: -0.02em;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .dossier-title-sub {
    font-size: 0.92rem;
    color: var(--dossier-text-muted);
    font-weight: 500;
  }

  /* Quick Action Icon Toolbar */
  .dossier-toolbar-actions {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-wrap: wrap;
  }

  .btn-dossier-tool {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--dossier-action-btn-border);
    background: var(--dossier-action-btn-bg);
    color: var(--dossier-action-btn-color);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  }
  .btn-dossier-tool:hover {
    background: var(--dossier-action-btn-hover);
    color: var(--dossier-navy);
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }
  .btn-dossier-tool.active-primary {
    background: #0f1e4d;
    border-color: #0f1e4d;
    color: #ffffff;
  }
  .btn-dossier-tool.active-primary:hover {
    background: #1e3a8a;
    border-color: #1e3a8a;
    color: #ffffff;
  }

  /* Meta Chip Items */
  .dossier-meta-bar {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.75rem;
    font-size: 0.8rem;
  }

  .dossier-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.22rem 0.65rem;
    background: var(--dossier-chip-bg);
    border: 1px solid var(--dossier-chip-border);
    color: var(--dossier-chip-text);
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 500;
  }

  .status-stamp-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }
  .status-stamp-pill.under-scrutiny {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #b45309;
  }
  .status-stamp-pill.validated {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
  }
  .status-stamp-pill.approved {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #047857;
  }
  .status-stamp-pill.revision {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
  }
  .blip-pulse {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    animation: blipAnim 2s infinite ease-in-out;
  }
  @keyframes blipAnim {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.35; transform: scale(0.8); }
  }

  /* 1-Click Copy Interaction Buttons */
  .btn-copy-chip {
    width: 22px;
    height: 22px;
    border-radius: 5px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid transparent;
    color: var(--dossier-text-dim);
    font-size: 0.75rem;
    cursor: pointer;
    padding: 0;
    margin-left: 0.35rem;
    transition: all 0.15s ease;
    vertical-align: middle;
  }
  .btn-copy-chip:hover {
    background: var(--dossier-chip-bg);
    border-color: var(--dossier-chip-border);
    color: var(--dossier-navy);
    transform: scale(1.1);
  }
  .btn-copy-chip.copied {
    color: #10b981 !important;
    border-color: #10b981 !important;
  }

  /* KPI Executive Metrics Cards */
  .dossier-kpi-card {
    background: var(--dossier-card-bg);
    border: 1px solid var(--dossier-card-border);
    border-radius: 14px;
    padding: 1.15rem 1.25rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 126px;
    box-shadow: var(--dossier-card-shadow);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
  }
  .dossier-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--dossier-card-hover);
    border-color: var(--dossier-navy-border);
  }
  .dossier-kpi-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }
  .dossier-kpi-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--dossier-text-muted);
    letter-spacing: 0.01em;
    line-height: 1.3;
    margin: 0;
  }
  .dossier-kpi-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    transition: transform 0.2s ease;
  }
  .dossier-kpi-card:hover .dossier-kpi-icon {
    transform: scale(1.08);
  }
  .dossier-kpi-icon.icon-teal {
    background: #e6fffa;
    color: #0d9488;
    border: 1px solid #b2f5ea;
  }
  .dossier-kpi-icon.icon-purple {
    background: #f3e8ff;
    color: #7c3aed;
    border: 1px solid #e9d5ff;
  }
  .dossier-kpi-icon.icon-green {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
  }
  .dossier-kpi-icon.icon-orange {
    background: #fff7ed;
    color: #ea580c;
    border: 1px solid #fed7aa;
  }
  .dossier-kpi-value {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--dossier-text-title);
    line-height: 1.15;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.02em;
    margin: 0.15rem 0 0.55rem 0;
  }
  .dossier-kpi-total {
    font-size: 1rem;
    font-weight: 500;
    color: var(--dossier-text-dim);
    margin-left: 2px;
  }
  .dossier-kpi-foot {
    display: flex;
    align-items: center;
    margin-top: auto;
  }
  .dossier-kpi-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.22rem 0.65rem;
    border-radius: 999px;
    line-height: 1.2;
  }
  .dossier-kpi-pill.pill-success {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }
  .dossier-kpi-pill.pill-warning {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
  }
  .dossier-kpi-pill.pill-neutral {
    background: var(--dossier-chip-bg);
    color: var(--dossier-text-muted);
    border: 1px solid var(--dossier-chip-border);
  }

  /* Book Chapters & Panel Styling */
  .card-panel {
    background: var(--dossier-card-bg);
    border: 1px solid var(--dossier-card-border);
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--dossier-card-shadow);
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
  }
  .card-panel:hover {
    border-color: var(--dossier-card-border);
    box-shadow: var(--dossier-card-hover);
  }

  .chapter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.85rem;
    border-bottom: 1px solid var(--dossier-card-border);
    flex-wrap: wrap;
  }
  .chapter-title-group {
    display: flex;
    align-items: center;
    gap: 0.65rem;
  }
  .chapter-tag {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: var(--dossier-navy-soft);
    color: var(--dossier-navy);
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px solid var(--dossier-navy-border);
  }
  .panel-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dossier-text-title);
    margin: 0;
    letter-spacing: -0.01em;
  }
  .panel-sub {
    font-size: 0.78rem;
    color: var(--dossier-text-muted);
    margin-top: 0.15rem;
    margin-bottom: 0;
  }

  /* Data Cells Grid */
  .data-cell {
    background: var(--dossier-table-stripe);
    border: 1px solid var(--dossier-table-border);
    border-radius: 10px;
    padding: 0.85rem 1rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.18s ease;
  }
  .data-cell:hover {
    background: var(--dossier-table-hover);
    border-color: var(--dossier-chip-border);
  }
  .data-cell-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--dossier-text-muted);
    margin-bottom: 0.35rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .data-cell-value {
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--dossier-text-title);
    word-break: break-word;
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  .data-cell-sub {
    font-size: 0.75rem;
    color: var(--dossier-text-muted);
    margin-top: 0.25rem;
  }

  /* Process Flow Stepper */
  .flow-stepper {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    position: relative;
    padding: 0.5rem 0;
    overflow-x: auto;
  }
  .flow-stepper::before {
    content: '';
    position: absolute;
    top: 24px;
    left: 5%;
    right: 5%;
    height: 3px;
    background: var(--dossier-card-border);
    z-index: 1;
  }
  .fstep {
    flex: 1;
    text-align: center;
    position: relative;
    z-index: 2;
    min-width: 110px;
  }
  .fstep .circ {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--dossier-card-bg);
    border: 2px solid var(--dossier-card-border);
    color: var(--dossier-text-muted);
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    transition: all 0.25s ease;
    box-shadow: 0 0 0 4px var(--dossier-card-bg);
  }
  .fstep.done .circ {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
  }
  .fstep.active .circ {
    background: #f59e0b;
    border-color: #f59e0b;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.25);
  }
  .fstep .flabel {
    font-size: 0.78rem;
    font-weight: 700;
    margin-top: 0.5rem;
    color: var(--dossier-text-title);
  }
  .fstep .fsub {
    font-size: 0.7rem;
    color: var(--dossier-text-muted);
  }

  /* Document Checklist Filter Tabs */
  .doc-filter-pills {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
  }
  .doc-filter-btn {
    border: 1px solid var(--dossier-card-border);
    background: var(--dossier-card-bg);
    color: var(--dossier-text-muted);
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .doc-filter-btn:hover {
    background: var(--dossier-table-hover);
    color: var(--dossier-text-title);
  }
  .doc-filter-btn.active {
    background: var(--dossier-navy-soft);
    border-color: var(--dossier-navy-border);
    color: var(--dossier-navy);
  }

  /* Document Table */
  .doc-table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
  }
  .doc-table thead th {
    background: var(--dossier-table-th);
    color: var(--dossier-text-muted);
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    padding: 0.75rem 0.85rem;
    border-bottom: 1px solid var(--dossier-card-border);
  }
  .doc-table tbody tr {
    transition: background 0.15s ease;
  }
  .doc-table tbody tr:hover {
    background: var(--dossier-table-hover);
  }
  .doc-table tbody td {
    padding: 0.75rem 0.85rem;
    border-bottom: 1px solid var(--dossier-table-border);
    color: var(--dossier-text-body);
  }

  /* Pure Icon Action Buttons for Document Rows */
  .btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--dossier-action-btn-border);
    background: var(--dossier-action-btn-bg);
    color: var(--dossier-action-btn-color);
    font-size: 0.85rem;
    cursor: pointer;
    padding: 0;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    line-height: 1;
  }
  .btn-action-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.12);
  }
  .btn-action-icon.btn-view:hover {
    background: #eff6ff;
    border-color: #3b82f6;
    color: #2563eb;
  }
  .btn-action-icon.btn-valid:hover {
    background: #ecfdf5;
    border-color: #10b981;
    color: #059669;
  }
  .btn-action-icon.btn-flag:hover {
    background: #fef2f2;
    border-color: #ef4444;
    color: #dc2626;
  }
  .btn-action-icon.btn-undo:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #475569;
  }
  .btn-action-icon.btn-upload {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1d4ed8;
  }
  .btn-action-icon.btn-upload:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
  }
  .btn-action-icon.btn-resume {
    background: #fefce8;
    border-color: #fde047;
    color: #ca8a04;
  }
  .btn-action-icon.btn-resume:hover {
    background: #eab308;
    border-color: #ca8a04;
    color: #0f172a;
  }
  .btn-action-icon.btn-discard {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
  }
  .btn-action-icon.btn-discard:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
  }

  /* Activity Timeline */
  .timeline {
    position: relative;
    padding-left: 1.5rem;
  }
  .timeline::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 6px;
    bottom: 6px;
    width: 2px;
    background: var(--dossier-card-border);
  }
  .tl-item {
    position: relative;
    margin-bottom: 1.1rem;
  }
  .tl-item::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--dossier-navy);
    border: 2px solid var(--dossier-card-bg);
  }
  .tl-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--dossier-text-title);
  }
  .tl-desc {
    font-size: 0.75rem;
    color: var(--dossier-text-muted);
    margin-top: 0.15rem;
  }
  .tl-meta {
    font-size: 0.7rem;
    color: var(--dossier-text-dim);
    margin-top: 0.2rem;
  }

  /* Custom Print Optimization */
  @media print {
    .dossier-toolbar-actions,
    .btn-dossier-tool,
    .btn-action-icon,
    .btn-copy-chip,
    .nav-header,
    .header,
    .dlabnav,
    .footer {
      display: none !important;
    }
    .content-body {
      margin: 0 !important;
      padding: 0 !important;
    }
    .card-panel {
      box-shadow: none !important;
      border: 1px solid #ccc !important;
      break-inside: avoid;
    }
  }

  /* ==========================================================================
     TOOLTIP PERFECTION: SOLID OPAQUE HIGH-CONTRAST (PREVENT THEME TEXT OVERLAP)
     ========================================================================== */
  .tooltip {
    z-index: 1090 !important;
    pointer-events: none !important;
  }
  .tooltip .tooltip-inner {
    background-color: #0f172a !important; /* 100% Solid Deep Slate in Light Mode */
    color: #ffffff !important;
    opacity: 1 !important;
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    padding: 0.32rem 0.65rem !important;
    border-radius: 6px !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    white-space: nowrap !important;
  }
  body[data-theme-version="dark"] .tooltip .tooltip-inner {
    background-color: #1e293b !important; /* Solid Slate-800 in Dark Mode */
    color: #f8fafc !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.65) !important;
  }
  .bs-tooltip-top .tooltip-arrow::before,
  .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow::before {
    border-top-color: #0f172a !important;
  }
  .bs-tooltip-bottom .tooltip-arrow::before,
  .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow::before {
    border-bottom-color: #0f172a !important;
  }
  .bs-tooltip-end .tooltip-arrow::before,
  .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before {
    border-right-color: #0f172a !important;
  }
  .bs-tooltip-start .tooltip-arrow::before,
  .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow::before {
    border-left-color: #0f172a !important;
  }
  body[data-theme-version="dark"] .bs-tooltip-top .tooltip-arrow::before,
  body[data-theme-version="dark"] .bs-tooltip-auto[data-popper-placement^="top"] .tooltip-arrow::before {
    border-top-color: #1e293b !important;
  }
  body[data-theme-version="dark"] .bs-tooltip-bottom .tooltip-arrow::before,
  body[data-theme-version="dark"] .bs-tooltip-auto[data-popper-placement^="bottom"] .tooltip-arrow::before {
    border-bottom-color: #1e293b !important;
  }
  body[data-theme-version="dark"] .bs-tooltip-end .tooltip-arrow::before,
  body[data-theme-version="dark"] .bs-tooltip-auto[data-popper-placement^="right"] .tooltip-arrow::before {
    border-right-color: #1e293b !important;
  }
  body[data-theme-version="dark"] .bs-tooltip-start .tooltip-arrow::before,
  body[data-theme-version="dark"] .bs-tooltip-auto[data-popper-placement^="left"] .tooltip-arrow::before {
    border-left-color: #1e293b !important;
  }

  /* ==========================================================================
     DATA VALUE TEXT & BADGE CONTRAST PERFECTION (LIGHT & DARK THEMES)
     ========================================================================== */
  /* High-Contrast Data Values */
  .dossier-book-shell .data-value-text,
  .dossier-book-shell .data-cell-value {
    color: var(--dossier-text-title) !important;
    font-weight: 700 !important;
  }
  .dossier-book-shell .data-cell-value span {
    color: inherit;
  }

  /* Neutral / Not Uploaded Badge Normalization */
  .dossier-book-shell .badge.bg-secondary-subtle,
  .dossier-book-shell .badge.text-secondary {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
    border-color: #cbd5e1 !important;
    font-size: 0.73rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.65rem !important;
    border-radius: 20px !important;
  }
  body[data-theme-version="dark"] .dossier-book-shell .badge.bg-secondary-subtle,
  body[data-theme-version="dark"] .dossier-book-shell .badge.text-secondary {
    background-color: rgba(148, 163, 184, 0.12) !important;
    color: #cbd5e1 !important;
    border-color: rgba(148, 163, 184, 0.28) !important;
  }

  /* Folder Column Badge (bg-light text-dark) Normalization */
  .dossier-book-shell .badge.bg-light.text-dark {
    background-color: #f1f5f9 !important;
    color: #334155 !important;
    border-color: #e2e8f0 !important;
    font-size: 0.72rem !important;
    font-weight: 600 !important;
    padding: 0.3rem 0.6rem !important;
    border-radius: 6px !important;
  }
  body[data-theme-version="dark"] .dossier-book-shell .badge.bg-light.text-dark {
    background-color: #21242d !important;
    color: #cbd5e1 !important;
    border-color: #2c303c !important;
  }

  /* Uploaded Badge (bg-warning-subtle) */
  .dossier-book-shell .badge.bg-warning-subtle {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fde68a !important;
    font-size: 0.73rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.65rem !important;
    border-radius: 20px !important;
  }
  body[data-theme-version="dark"] .dossier-book-shell .badge.bg-warning-subtle {
    background-color: rgba(245, 158, 11, 0.14) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.3) !important;
  }

  /* Validated Badge (bg-success-subtle) */
  .dossier-book-shell .badge.bg-success-subtle {
    background-color: #dcfce7 !important;
    color: #166534 !important;
    border-color: #bbf7d0 !important;
    font-size: 0.73rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.65rem !important;
    border-radius: 20px !important;
  }
  body[data-theme-version="dark"] .dossier-book-shell .badge.bg-success-subtle {
    background-color: rgba(16, 185, 129, 0.14) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.3) !important;
  }

  /* Revision Needed Badge (bg-danger-subtle) */
  .dossier-book-shell .badge.bg-danger-subtle {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #fecaca !important;
    font-size: 0.73rem !important;
    font-weight: 600 !important;
    padding: 0.35rem 0.65rem !important;
    border-radius: 20px !important;
  }
  body[data-theme-version="dark"] .dossier-book-shell .badge.bg-danger-subtle {
    background-color: rgba(239, 68, 68, 0.14) !important;
    color: #f87171 !important;
    border-color: rgba(239, 68, 68, 0.3) !important;
  }

  body[data-theme-version="dark"] .btn-action-icon.btn-resume {
    background: rgba(234, 179, 8, 0.15);
    border-color: rgba(234, 179, 8, 0.4);
    color: #facc15;
  }
  body[data-theme-version="dark"] .btn-action-icon.btn-resume:hover {
    background: #eab308;
    border-color: #eab308;
    color: #0f172a;
  }
  body[data-theme-version="dark"] .btn-action-icon.btn-discard {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.4);
    color: #f87171;
  }
  body[data-theme-version="dark"] .btn-action-icon.btn-discard:hover {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff;
  }

  /* Compact Custom SweetAlert Gate for Step 6.3 Move to Mining */
  .gtms-gate-swal-compact {
    width: 390px !important;
    max-width: 90vw !important;
    padding: 1.15rem 1.15rem 0.95rem !important;
    border-radius: 14px !important;
    box-shadow: 0 15px 35px -5px rgba(15, 30, 77, 0.2) !important;
    border: 1px solid rgba(15, 30, 77, 0.1) !important;
    font-family: inherit !important;
  }
  .gtms-gate-swal-compact .swal2-icon {
    font-size: 9.5px !important;
    width: 5em !important;
    height: 5em !important;
    margin: 0.6rem auto 0.75rem !important;
    border-width: 0.25em !important;
    box-sizing: content-box !important;
  }
  .gtms-gate-swal-compact .swal2-icon .swal2-icon-content {
    font-size: 1.6rem !important;
  }
  .gtms-gate-swal-compact .swal2-icon.swal2-success [class^='swal2-success-circular-line'] {
    background: transparent !important;
  }
  .gtms-gate-swal-compact .swal2-icon.swal2-success .swal2-success-fix {
    background: transparent !important;
  }
  .gtms-gate-swal-compact .swal2-title {
    font-size: 1.12rem !important;
    padding: 0 !important;
    margin: 0 0 0.65rem !important;
    font-weight: 700 !important;
    color: #0F1E4D !important;
  }
  .gtms-gate-swal-compact .swal2-html-container {
    margin: 0 !important;
    padding: 0 !important;
    overflow: visible !important;
  }
  .gtms-gate-swal-compact .swal2-actions {
    margin: 0.75rem 0 0.15rem !important;
    gap: 0.5rem !important;
  }
  .gtms-gate-swal-compact .swal2-confirm {
    background-color: #0F1E4D !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.45rem 1.5rem !important;
    font-weight: 600 !important;
    font-size: 0.84rem !important;
    box-shadow: 0 4px 10px rgba(15, 30, 77, 0.18) !important;
    transition: all 0.2s ease !important;
  }
  .gtms-gate-swal-compact .swal2-confirm:hover {
    background-color: #1e3a8a !important;
    transform: translateY(-1px) !important;
  }
  body[data-theme-version="dark"] .gtms-gate-swal-compact {
    background: #182234 !important;
    color: #e2e8f0 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
  }
  body[data-theme-version="dark"] .gtms-gate-swal-compact .swal2-title {
    color: #f8fafc !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body .rounded {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #e2e8f0 !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body .text-muted {
    color: #94a3b8 !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body strong {
    color: #f8fafc !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body .text-primary {
    color: #60a5fa !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body .text-success {
    color: #34d399 !important;
  }
  body[data-theme-version="dark"] .gtms-compact-gate-body .text-dark {
    color: #f8fafc !important;
  }
</style>

<div class="content-body default-height dossier-book-shell">
  <div class="container-fluid">
    <div class="content">

      {{-- Flash Feedback Alerts --}}
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm rounded-3" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium">{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('warning'))
      <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
        <div class="fw-medium">{{ session('warning') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3 shadow-sm rounded-3" role="alert">
        <i class="bi bi-x-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium">{{ session('error') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      @endif

      {{-- Promotional Mining Plan Notice --}}
      @if($linkedMining)
      <div class="alert alert-success d-flex justify-content-between align-items-center mb-3 shadow-sm rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0; color:#065f46;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle p-2 d-flex align-items-center justify-content-center" style="background:#d1fae5; width:40px; height:40px;">
            <i class="fa fa-mountain text-success fs-5"></i>
          </div>
          <div>
            <strong class="d-block" style="font-size:0.92rem;">Promoted to Mining Plan Domain</strong>
            <div class="small mt-0" style="color:#047857;">
              Universal Common ID: <b>{{ $linkedMining->common_id }}</b> &middot; Mining App No: <b>{{ $linkedMining->application_no }}</b> &middot; Stage: <b>Stage {{ $linkedMining->stage ?? '6.1' }}</b>
            </div>
          </div>
        </div>
        <a href="{{ route('process', ['id' => $linkedMining->id]) }}" class="btn btn-success btn-sm px-3 shadow-sm">
          Open Mining Plan <i class="bi bi-arrow-right ms-1"></i>
        </a>
      </div>
      @endif

      <!-- EXECUTIVE BOOK MASTHEAD (COVER & SUMMARY) -->
      <div class="dossier-book-masthead">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
          <div>
            <div class="dossier-badge-crest mb-2">
              <i class="bi bi-shield-shaded"></i> Government of Tamil Nadu &middot; Dept of Geology &amp; Mining
            </div>
            <div class="dossier-title-main">
              <span>Lease Dossier #{{ $application->application_no ?? 'LA-2026-0001' }}</span>
              <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->application_no }}" data-copy-label="Application Number" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Application Number">
                <i class="bi bi-clipboard"></i>
              </button>
              <span class="dossier-title-sub ms-1">&mdash; {{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'Applicant Firm') }}</span>
              @if(!empty($application->customer?->company_name) || !empty($application->customer?->customer_name))
              <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer?->company_name ?? $application->customer?->customer_name }}" data-copy-label="Firm Name" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Firm Name">
                <i class="bi bi-clipboard"></i>
              </button>
              @endif
            </div>

            <!-- Meta Data Chips with 1-Click Copy -->
            <div class="dossier-meta-bar">
              @php
                $statusSlug = strtolower(str_replace(' ', '-', $application->status ?? 'under_scrutiny'));
                if ($statusSlug === 'approved') $pillClass = 'approved';
                elseif (in_array($statusSlug, ['validated'])) $pillClass = 'validated';
                elseif (in_array($statusSlug, ['revision_required', 'rejected'])) $pillClass = 'revision';
                else $pillClass = 'under-scrutiny';
              @endphp
              <span class="status-stamp-pill {{ $pillClass }}">
                <span class="blip-pulse"></span>
                {{ ucfirst(str_replace('_', ' ', $application->status ?? 'Under Scrutiny')) }}
              </span>

              <span class="dossier-chip">
                <i class="bi bi-fingerprint text-warning"></i>
                <span>Common ID: <b>{{ $commonId }}</b></span>
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $commonId }}" data-copy-label="Common ID" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Common ID">
                  <i class="bi bi-clipboard"></i>
                </button>
              </span>

              <span class="dossier-chip">
                <i class="bi bi-geo-alt text-primary"></i>
                <span>{{ $application->district->name ?? 'District N/A' }}</span>
              </span>

              <span class="dossier-chip">
                <i class="bi bi-gem text-info"></i>
                <span>{{ $mineralDisplay }}</span>
              </span>

              <span class="dossier-chip">
                <i class="bi bi-calendar3 text-secondary"></i>
                <span>Filed {{ $application->created_at ? $application->created_at->format('d M Y') : date('d M Y') }}</span>
              </span>
            </div>
          </div>

          <!-- Quick Action Icon Toolbar (No Clunky Text Buttons) -->
          <div class="dossier-toolbar-actions">
            @if(isset($application->id))
            <a href="{{ route('application.report', $application->id) }}" target="_blank" class="btn-dossier-tool" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Download Official PDF Report">
              <i class="bi bi-file-earmark-pdf text-danger"></i>
            </a>
            @endif

            <button type="button" id="btn_print_official_report" class="btn-dossier-tool" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Print Official Scrutiny Report (PDF)">
              <i class="bi bi-printer text-secondary"></i>
            </button>

            <button type="button" id="btn_copy_dossier_summary" class="btn-dossier-tool" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy Complete Executive Summary">
              <i class="bi bi-clipboard-check text-success"></i>
            </button>

            <a href="{{ route('viewapplication', ['id' => $application->id]) }}" class="btn-dossier-tool" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh &amp; Sync Dossier">
              <i class="bi bi-arrow-clockwise text-primary"></i>
            </a>

            @if($linkedMining)
              <a href="{{ route('process', ['id' => $linkedMining->id]) }}" class="btn-dossier-tool" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Open Linked Mining Plan: {{ $linkedMining->common_id }}">
                <i class="fa fa-mountain text-success"></i>
              </a>
            @else
              @can('application.edit')
              <button type="button" class="btn-dossier-tool btn-trigger-move-mining" id="btn_masthead_move_mining" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Promote to Mining Plan Domain">
                <i class="bi bi-rocket-takeoff text-warning"></i>
              </button>
              @endcan
            @endif

            @can('application.create')
            <a href="{{ route('step1') }}" class="btn-dossier-tool active-primary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="New Application Wizard">
              <i class="bi bi-plus-lg"></i>
            </a>
            @endcan
          </div>
        </div>
      </div>

      <!-- EXECUTIVE KPI METRICS ROW -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Regulatory Documents</span>
              <div class="dossier-kpi-icon icon-teal"><i class="fa fa-folder"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $regUploaded }} <span class="dossier-kpi-total">/ {{ $regTotal }}</span>
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $regUploaded >= $regTotal ? 'pill-success' : 'pill-warning' }}">
                <i class="bi {{ $regUploaded >= $regTotal ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                {{ $regUploaded >= $regTotal ? 'Complete' : ($regTotal - $regUploaded) . ' Pending' }}
              </span>
            </div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Plan Files (KML / PDF)</span>
              <div class="dossier-kpi-icon icon-purple"><i class="fa fa-cloud-upload"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $planUploaded }} <span class="dossier-kpi-total">/ {{ $planTotal }}</span>
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $planUploaded >= $planTotal ? 'pill-success' : 'pill-warning' }}">
                <i class="bi {{ $planUploaded >= $planTotal ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                {{ $planUploaded >= $planTotal ? 'Complete' : ($planTotal - $planUploaded) . ' Pending' }}
              </span>
            </div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">Validated Files</span>
              <div class="dossier-kpi-icon icon-green"><i class="bi bi-shield-check"></i></div>
            </div>
            <div class="dossier-kpi-value" id="kpi-validated-count">{{ $validatedCount }} <span class="dossier-kpi-total">/ {{ $totalUploaded }}</span></div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill {{ $allValidated ? 'pill-success' : 'pill-warning' }}" id="kpi-validated-sub">
                <i class="bi {{ $allValidated ? 'bi-check-circle-fill' : 'bi-clock-fill' }}"></i>
                <span id="kpi-validated-text">{{ $allValidated ? 'All Validated' : ($totalUploaded > 0 ? ($totalUploaded - $validatedCount) . ' Awaiting Scrutiny' : 'No Files Uploaded') }}</span>
              </span>
            </div>
          </div>
        </div>

        <div class="col-6 col-lg-3">
          <div class="dossier-kpi-card h-100">
            <div class="dossier-kpi-head">
              <span class="dossier-kpi-label">In Current Stage</span>
              <div class="dossier-kpi-icon icon-orange"><i class="bi bi-hourglass-split"></i></div>
            </div>
            <div class="dossier-kpi-value">
              {{ $stageDuration }}
            </div>
            <div class="dossier-kpi-foot">
              <span class="dossier-kpi-pill pill-neutral">
                <i class="bi bi-stopwatch"></i> SLA: 10 days
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- CHAPTER I: APPLICANT IDENTITY & LEGAL ENTITY DOSSIER -->
      <div class="card-panel">
        <div class="chapter-header">
          <div class="chapter-title-group">
            <span class="chapter-tag">Chapter I</span>
            <div>
              <h5 class="panel-title"><i class="fa fa-id-card text-primary me-2"></i>Statutory Applicant &amp; Legal Entity Profile</h5>
              <div class="panel-sub">Verified corporate credentials, tax identifiers, and official representation</div>
            </div>
          </div>
          <span class="dossier-chip">
            <i class="fa fa-database text-primary"></i> Application ID: #{{ $application->id }}
          </span>
        </div>

        <div class="row g-3">
          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Client / Firm Name</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'N/A') }}</span>
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer?->company_name ?? $application->customer?->customer_name }}" data-copy-label="Client / Firm" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Firm Name"><i class="bi bi-clipboard"></i></button>
              </div>
              <div class="data-cell-sub">Proprietor: {{ $application->customer?->customer_name ?? 'N/A' }}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Customer Unique ID (MIMAS)</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->customer?->mimas_no ?? 'N/A' }}</span>
                @if(!empty($application->customer?->mimas_no))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer->mimas_no }}" data-copy-label="MIMAS ID" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy MIMAS ID"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Mining Directorate Account</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Aadhaar (UIDAI)</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->customer?->aadhaar_no ?? 'N/A' }}</span>
                @if(!empty($application->customer?->aadhaar_no))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer->aadhaar_no }}" data-copy-label="Aadhaar Number" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Aadhaar Number"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Individual Identity Proof</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Income Tax PAN</div>
              <div class="data-cell-value">
                <span class="data-value-text font-monospace">{{ $application->customer?->pan ?? 'N/A' }}</span>
                @if(!empty($application->customer?->pan))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer->pan }}" data-copy-label="PAN Number" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy PAN"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Tax Assessment Identifier</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">GSTIN (15 Digits)</div>
              <div class="data-cell-value">
                <span class="data-value-text font-monospace">{{ $application->customer?->gstin ?? 'Not Registered' }}</span>
                @if(!empty($application->customer?->gstin))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer->gstin }}" data-copy-label="GSTIN" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy GSTIN"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Goods &amp; Services Tax</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Primary Communication</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A') }}</span>
                @php $mob = $application->contact_mobile ?? ($application->customer?->mobile_num ?? ''); @endphp
                @if(!empty($mob))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $mob }}" data-copy-label="Primary Mobile" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Mobile"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">{{ $application->contact_person ?? ($application->customer?->customer_name ?? 'Representative') }}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Official Email</div>
              <div class="data-cell-value text-truncate">
                <span class="data-value-text">{{ $application->customer?->email ?? 'N/A' }}</span>
                @if(!empty($application->customer?->email))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->customer->email }}" data-copy-label="Email" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Email"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Proceedings &amp; Digital Notices</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Site / Secondary Contact</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? 'N/A') }}</span>
                @php $secMob = $application->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? ''); @endphp
                @if(!empty($secMob))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $secMob }}" data-copy-label="Secondary Mobile" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Secondary Mobile"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">{{ $application->secondary_contact_person ?? ($application->customer?->secondary_contact_person ?? 'Quarry In-charge') }}</div>
            </div>
          </div>

          @if(!empty($registeredAddress))
          <div class="col-12">
            <div class="data-cell py-2 px-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-geo-alt-fill text-danger"></i>
                  <span class="text-muted small"><strong>Registered Official Address:</strong></span>
                  <span class="data-value-text fw-medium small">{{ $registeredAddress }}</span>
                </div>
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $registeredAddress }}" data-copy-label="Address" data-bs-toggle="tooltip" data-bs-placement="left" title="Copy Address"><i class="bi bi-clipboard"></i></button>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>

      <!-- CHAPTER II: REVENUE CADASTRAL & CONCESSION EXTENT -->
      <div class="card-panel">
        <div class="chapter-header">
          <div class="chapter-title-group">
            <span class="chapter-tag">Chapter II</span>
            <div>
              <h5 class="panel-title"><i class="bi bi-compass text-success me-2"></i>Geo-Spatial Cadastral &amp; Concession Extent</h5>
              <div class="panel-sub">Revenue jurisdiction, survey field parcel boundaries, and statutory land extent</div>
            </div>
          </div>
          <span class="dossier-chip">
            <i class="bi bi-rulers text-success"></i> Extent: {{ $application->area_extent_ha ? $application->area_extent_ha . ' Ha' : 'N/A' }}
          </span>
        </div>

        <div class="row g-3">
          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">District Mining Office</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->district->name ?? 'N/A' }}</span>
                @if(!empty($application->district->name))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->district->name }}" data-copy-label="District" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy District"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Deputy Director / AD Jurisdiction</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Taluk &amp; Revenue Village</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->village ?: ($application->taluk ?: 'N/A') }}</span>
              </div>
              <div class="data-cell-sub">Taluk: {{ $application->taluk ?: 'N/A' }}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Total Extent (Hectares)</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->area_extent_ha ? $application->area_extent_ha . ' Ha' : 'N/A' }}</span>
                @if(!empty($application->area_extent_ha))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $application->area_extent_ha }}" data-copy-label="Extent (Ha)" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Extent"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">{{ $application->area_extent_acres ? '(' . $application->area_extent_acres . ')' : 'Metric Standard' }}</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Survey Field Number (S.F. No)</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $survey ? $survey->survey_no : 'SF.No 1' }}</span>
                @php $sNo = $survey ? $survey->survey_no : 'SF.No 1'; @endphp
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $sNo }}" data-copy-label="Survey Number" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Survey Number"><i class="bi bi-clipboard"></i></button>
              </div>
              <div class="data-cell-sub">{{ $survey->classification ?? 'Patta Land' }} &middot; {{ $survey->pattadar_name ?? $application->contact_person }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- CHAPTER III: MINERAL CLASSIFICATION & STATUTORY RULES -->
      <div class="card-panel">
        <div class="chapter-header">
          <div class="chapter-title-group">
            <span class="chapter-tag">Chapter III</span>
            <div>
              <h5 class="panel-title"><i class="bi bi-book-half text-warning me-2"></i>Mineral Concession, Applicable Rules &amp; Lease Tenure</h5>
              <div class="panel-sub">Classification under TNMMCR 1959 / MMDR Act and statutory concession duration</div>
            </div>
          </div>
          <span class="dossier-chip">
            <i class="bi bi-hourglass-bottom text-warning"></i> Tenure: {{ $application->lease_period_years ?? 5 }} Years
          </span>
        </div>

        <div class="row g-3">
          <div class="col-md-4 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Mineral Concession</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $mineralDisplay }}</span>
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $mineralDisplay }}" data-copy-label="Mineral" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Mineral"><i class="bi bi-clipboard"></i></button>
              </div>
              <div class="data-cell-sub">Minor / Major Mineral Concession Scope</div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Lease Category &amp; Rule Code</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->category->code ?? 'N/A' }}</span>
                @if(!empty($application->other_category_rule) || ($application->category && $application->category->is_custom))
                  <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle ms-1" style="font-size: .65rem;">Custom Rule</span>
                @endif
              </div>
              <div class="data-cell-sub text-truncate" title="{{ $application->other_category_rule ?: ($application->category->name ?? 'Standard Rule') }}">
                {{ $application->other_category_rule ?: ($application->category->name ?? 'Standard Rule') }}
              </div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Concession Effective Term</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $application->lease_period_years ?? 5 }} Years</span>
              </div>
              <div class="data-cell-sub">
                {{ $application->start_date ? \Carbon\Carbon::parse($application->start_date)->format('d M Y') : '01 Oct ' . date('Y') }} &ndash;
                {{ $application->end_date ? \Carbon\Carbon::parse($application->end_date)->format('d M Y') : '30 Sep ' . (date('Y') + 5) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- CHAPTER IV: REGULATORY ACCESS & MIMAS CREDENTIALS -->
      <div class="card-panel">
        <div class="chapter-header">
          <div class="chapter-title-group">
            <span class="chapter-tag">Chapter IV</span>
            <div>
              <h5 class="panel-title"><i class="bi bi-key-fill text-indigo me-2" style="color:#6366f1;"></i>Regulatory Portal &amp; MIMAS Access Credentials</h5>
              <div class="panel-sub">Encrypted authentication tokens and regulatory portal status</div>
            </div>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
            <i class="bi bi-check-circle-fill me-1"></i> {{ ucfirst($mimas->portal_status ?? 'Verified') }}
          </span>
        </div>

        <div class="row g-3">
          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">MIMAS User ID</div>
              <div class="data-cell-value font-monospace">
                <span class="data-value-text">{{ $mimas->user_id ?? ($application->customer?->mimas_no ?? 'N/A') }}</span>
                @php $mUser = $mimas->user_id ?? ($application->customer?->mimas_no ?? ''); @endphp
                @if(!empty($mUser))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $mUser }}" data-copy-label="MIMAS User ID" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy User ID"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">Mines Directorate Portal Login</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">MIMAS Password</div>
              <div class="data-cell-value">
                <span id="mimas_pass_masked" class="font-monospace" style="letter-spacing: 2px;">••••••••••••</span>
                <span id="mimas_pass_plain" class="font-monospace" style="display: none; color: var(--dossier-text-title);">{{ $mimas ? $mimas->password : 'N/A' }}</span>
                @if($mimas && $mimas->password)
                  <button type="button" class="btn-copy-chip ms-1" id="btn_toggle_view_pass" title="Show / Hide Password" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa fa-eye" id="icon_view_pass"></i>
                  </button>
                  <button type="button" class="btn-copy-chip" id="btn_copy_view_pass" data-clipboard-text="{{ $mimas->password }}" title="Copy Password" data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <i class="fa fa-copy"></i>
                  </button>
                @endif
              </div>
              <div class="data-cell-sub">Encrypted Credential</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">MIMAS Ack No</div>
              <div class="data-cell-value font-monospace">
                <span class="data-value-text">{{ $mimas->mimas_ack_no ?? 'ACK-MMS-' . date('Y') . '-1379' }}</span>
                @php $ack = $mimas->mimas_ack_no ?? ('ACK-MMS-' . date('Y') . '-1379'); @endphp
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $ack }}" data-copy-label="Ack No" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Ack No"><i class="bi bi-clipboard"></i></button>
              </div>
              <div class="data-cell-sub">Online Acknowledgement Slip</div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6">
            <div class="data-cell">
              <div class="data-cell-label">Portal Phone / Notification</div>
              <div class="data-cell-value">
                <span class="data-value-text">{{ $mimas->contact_number ?? ($application->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A')) }}</span>
                @php $mPhone = $mimas->contact_number ?? ($application->contact_mobile ?? ($application->customer?->mobile_num ?? '')); @endphp
                @if(!empty($mPhone))
                <button type="button" class="btn-copy-chip btn-copy-val" data-copy-text="{{ $mPhone }}" data-copy-label="Portal Phone" data-bs-toggle="tooltip" data-bs-placement="right" title="Copy Portal Phone"><i class="bi bi-clipboard"></i></button>
                @endif
              </div>
              <div class="data-cell-sub">SMS Dispatch &amp; OTP Alerts</div>
            </div>
          </div>
        </div>
      </div>

      <!-- TEAM ALLOCATION & FINANCIAL SUMMARY ROW -->
      <div class="row g-3 mb-4">
        <!-- CHAPTER V: TECHNICAL PROJECT HANDLERS -->
        <div class="col-lg-7">
          <div class="card-panel h-100 mb-0">
            <div class="chapter-header">
              <div class="chapter-title-group">
                <span class="chapter-tag">Chapter V</span>
                <div>
                  <h5 class="panel-title"><i class="bi bi-people-fill text-primary me-2"></i>Project Handling Team</h5>
                  <div class="panel-sub">Assigned technical personnel and field coordinators</div>
                </div>
              </div>
              <span class="dossier-chip">
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
                        <td class="fw-semibold" style="color:var(--dossier-navy);">
                          <i class="bi bi-person-badge text-primary me-1"></i> {{ $handler->name }}
                        </td>
                        <td><span class="badge bg-secondary-subtle text-secondary border py-1 px-2">{{ $handler->role }}</span></td>
                        <td class="text-muted small">{{ $handler->notes ?: '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-muted small fst-italic py-3 text-center">
                <i class="bi bi-info-circle me-1"></i> No technical coordinators assigned to this application.
              </div>
            @endif
          </div>
        </div>

        <!-- CHAPTER VI: FINANCIAL & BILLING LEDGER -->
        <div class="col-lg-5">
          <div class="card-panel h-100 mb-0">
            <div class="chapter-header">
              <div class="chapter-title-group">
                <span class="chapter-tag">Chapter VI</span>
                <div>
                  <h5 class="panel-title"><i class="bi bi-cash-stack text-success me-2"></i>Financial &amp; Billing Ledger</h5>
                  <div class="panel-sub">Settlement status and balance tracking</div>
                </div>
              </div>
              @if($pStatus === 'paid')
                <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟢 Fully Paid</span>
              @elseif($pStatus === 'partial')
                <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟡 Partial Payment</span>
              @else
                <span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🔴 Pending Full Due</span>
              @endif
            </div>

            <div class="row g-2 text-center mt-2">
              <div class="col-4">
                <div class="p-2 border rounded" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
                  <span class="text-muted d-block small">Product Value</span>
                  <b class="d-block mt-1" style="color:var(--dossier-navy); font-size:0.92rem;">₹ {{ number_format((float)($application->product_value ?? 50000), 2) }}</b>
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 border rounded" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
                  <span class="text-success d-block small">Paid Amount</span>
                  <b class="text-success d-block mt-1" style="font-size:0.92rem;">₹ {{ number_format((float)($application->paid_amount ?? 0), 2) }}</b>
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 border rounded" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
                  <span class="text-danger d-block small">Pending Balance</span>
                  <b class="text-danger d-block mt-1" style="font-size:0.92rem;">₹ {{ number_format((float)($application->pending_amount ?? 50000), 2) }}</b>
                </div>
              </div>
            </div>

            <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between text-muted small">
              <span><i class="bi bi-clock-history me-1"></i> Status: <strong class="text-capitalize" style="color:var(--dossier-text-title);">{{ $pStatus }}</strong></span>
              <span><i class="bi bi-shield-check text-success me-1"></i> Recorded in Ledger</span>
            </div>
          </div>
        </div>
      </div>

      <!-- STATUTORY PROCESS FLOW STEPPER (STAGES 6.1 – 6.5) -->
      <div class="card-panel" id="section_scrutiny_lifecycle">
        <div class="chapter-header">
          <div class="chapter-title-group">
            <span class="chapter-tag">Stages 6.1 – 6.5</span>
            <div>
              <h5 class="panel-title">Statutory Scrutiny &amp; Verification Lifecycle</h5>
              <div class="panel-sub">Sequential departmental progress from data ingest to official archival</div>
            </div>
          </div>
          <div class="progress-thin flex-grow-1 mx-4 d-none d-md-block" style="max-width:180px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $isApproved ? '80%' : ($isValidated ? '60%' : '35%') }}; height: 6px; border-radius: 999px;"></div>
          </div>
        </div>

        <div class="flow-stepper mt-3">
          <!-- Step 1: Upload & Store -->
          <div class="fstep done">
            <div class="circ"><i class="bi bi-check-lg"></i></div>
            <div class="flabel">6.1 Upload &amp; Store</div>
            <div class="fsub">Completed</div>
          </div>

          <!-- Step 2: Validate Data -->
          <div class="fstep {{ $isValidated ? 'done' : ($isRevision ? 'active' : 'active') }}">
            <div class="circ">
              @if($isValidated) <i class="bi bi-check-lg"></i> @else 2 @endif
            </div>
            <div class="flabel">6.2 Validate Data</div>
            <div class="fsub">
              @if($isRevision)
                <span class="text-danger fw-bold">Revision Required</span>
              @elseif($isValidated)
                Completed
              @else
                In Scrutiny
              @endif
            </div>
          </div>

          <!-- Step 3: Approve Data -->
          <div class="fstep {{ $isApproved ? 'done' : ($isValidated ? 'active' : '') }}" id="step_6_3_approve">
            <div class="circ">
              @if($isApproved) <i class="bi bi-check-lg"></i> @else 3 @endif
            </div>
            <div class="flabel">6.3 Approve Data</div>
            <div class="fsub">
              @if($isApproved)
                Approved
              @elseif($isValidated)
                Ready for Approval
              @else
                Pending
              @endif
            </div>
          </div>

          <!-- Step 4: Generate Reports -->
          <div class="fstep {{ $isApproved ? 'active' : '' }}">
            <a href="{{ isset($application->id) ? route('application.report', $application->id) : 'javascript:void(0)' }}" target="_blank" style="text-decoration:none; color:inherit;">
              <div class="circ">4</div>
              <div class="flabel">6.4 Generate Reports</div>
              <div class="fsub">{{ $isApproved ? 'Ready to Download' : 'Pending' }}</div>
            </a>
          </div>

          <!-- Step 5: Archive & Backup -->
          <!-- <div class="fstep">
            <a href="javascript:void(0)" style="text-decoration:none; color:inherit;">
              <div class="circ">5</div>
              <div class="flabel">6.5 Archive &amp; Backup</div>
              <div class="fsub">Pending</div>
            </a>
          </div> -->
        </div>

        <!-- Dynamic Context Alert -->
        @if($isApproved)
        <div class="alert d-flex align-items-center gap-2 mt-3 mb-0 rounded-3" style="background:#e6f4ea; border:1px solid #b7e1cd; color:#137333; font-size:.82rem;">
          <i class="bi bi-check-circle-fill fs-6 text-success"></i>
          <div><strong>Application Fully Approved &amp; Verified!</strong> Compliance report is ready for export and archival.</div>
        </div>
        @elseif($isValidated)
        <div class="alert d-flex align-items-center gap-2 mt-3 mb-0 rounded-3" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; font-size:.82rem;">
          <i class="bi bi-shield-check fs-6 text-primary"></i>
          <div><strong>All 19 Documents Validated!</strong> Scrutiny is passed. Authorized officer can approve this application.</div>
        </div>
        @elseif($isRevision)
        <div class="alert d-flex align-items-center gap-2 mt-3 mb-0 rounded-3" style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; font-size:.82rem;">
          <i class="bi bi-exclamation-triangle-fill fs-6 text-danger"></i>
          <div><strong>Revision Required:</strong> Applicant has been notified to re-upload flagged documents.</div>
        </div>
        @else
        <div class="alert d-flex align-items-center gap-2 mt-3 mb-0 rounded-3" style="background:#fffbeb; border:1px solid #fde68a; color:#b45309; font-size:.82rem;">
          <i class="bi bi-info-circle fs-6"></i>
          <div>Application is currently <b>Under Scrutiny</b>. Review attached documents and mark validation or request correction.</div>
        </div>
        @endif
      </div>

      <!-- MAIN CONTENT ROW: CHAPTER VII DOCUMENT CHECKLIST & SIDEBAR -->
      <div class="row g-3">
        <div class="col-lg-8">
          <!-- CHAPTER VII: REGULATORY CHECKLIST & SURVEY PLANS VAULT -->
          <div class="card-panel">
            <div class="chapter-header">
              <div class="chapter-title-group">
                <span class="chapter-tag">Chapter VII</span>
                <div>
                  <h5 class="panel-title">Statutory Regulatory Checklist &amp; Plans Vault</h5>
                  <div class="panel-sub mb-0">{{ $docs->count() }} total items across Documents, Lease Application &amp; Plan folders</div>
                </div>
              </div>
              @can('application.edit')
              <!-- <a href="{{ route('step5', ['id' => $application->id]) }}" class="btn-dossier-tool" data-bs-toggle="tooltip" title="Open Bulk Document Vault">
                <i class="bi bi-cloud-arrow-up text-primary"></i>
              </a> -->
              @endcan
            </div>

            <!-- Filter Tabs -->
            <div class="doc-filter-pills">
              <button type="button" class="doc-filter-btn active" data-filter="all">All ({{ $docs->count() }})</button>
              <button type="button" class="doc-filter-btn" data-filter="7">1. Documents ({{ $docs->where('folder_id', 7)->count() }})</button>
              <button type="button" class="doc-filter-btn" data-filter="8">2. Lease App ({{ $docs->where('folder_id', 8)->count() }})</button>
              <button type="button" class="doc-filter-btn" data-filter="9">3. Plans &amp; KML ({{ $docs->where('folder_id', 9)->count() }})</button>
              <button type="button" class="doc-filter-btn" data-filter="uploaded">Uploaded ({{ $totalUploaded }})</button>
              <button type="button" class="doc-filter-btn" data-filter="pending">Pending ({{ $docs->count() - $totalUploaded }})</button>
            </div>

            <div class="table-responsive">
              <table class="table doc-table mb-0 align-middle">
                <thead>
                  <tr>
                    <th style="width:40px;">#</th>
                    <th>Document</th>
                    <th>Folder</th>
                    <th>Status</th>
                    <th class="text-end" style="min-width: 170px;">Scrutiny Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @if($sortedDocs->count() > 0)
                    @foreach($sortedDocs as $doc)
                    @php
                      $hasFile = !empty($doc->file_path);
                    @endphp
                    <tr id="doc-row-{{ $doc->id }}" class="doc-row-item" data-folder="{{ $doc->folder_id }}" data-uploaded="{{ $hasFile ? '1' : '0' }}" data-doc-id="{{ $doc->id }}" data-doc-field-id="{{ $doc->document_field_id }}" data-doc-name="{{ $doc->document_name }}">
                      <td class="text-muted fw-bold">{{ $loop->iteration }}</td>
                      <td>
                        <span class="file-chip d-inline-flex align-items-center gap-2">
                          @php
                            $rowExt = strtolower(pathinfo($doc->file_path ?? $doc->file_name ?? '', PATHINFO_EXTENSION));
                          @endphp
                          @if(in_array($rowExt, ['zip', 'rar', '7z', 'tar', 'gz']))
                            <i class="bi bi-file-earmark-zip text-warning fs-5"></i>
                          @elseif(in_array($rowExt, ['mp4', 'webm', 'mkv', 'avi', 'mov']))
                            <i class="bi bi-file-earmark-play text-info fs-5"></i>
                          @elseif(in_array($rowExt, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp']) || str_contains($doc->file_type ?? '', 'image'))
                            <i class="bi bi-file-earmark-image text-primary fs-5"></i>
                          @elseif(in_array($rowExt, ['kml', 'kmz']) || str_contains($doc->file_name ?? '', '.kml'))
                            <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                          @elseif(in_array($rowExt, ['doc', 'docx']))
                            <i class="bi bi-file-earmark-word text-primary fs-5"></i>
                          @elseif(in_array($rowExt, ['xls', 'xlsx', 'csv']))
                            <i class="bi bi-file-earmark-excel text-success fs-5"></i>
                          @elseif(in_array($rowExt, ['ppt', 'pptx']))
                            <i class="bi bi-file-earmark-ppt text-danger fs-5"></i>
                          @elseif($doc->file_path)
                            <i class="bi bi-file-earmark-pdf text-danger fs-5"></i>
                          @else
                            <i class="bi bi-file-earmark-x text-muted fs-5"></i>
                          @endif
                          <span class="doc-name-text fw-medium" style="color:var(--dossier-text-title);">{{ $doc->document_name }}</span>
                        </span>
                        @if($doc->file_path && $doc->file_name)
                          <div class="text-muted ps-4 small doc-file-subtitle" style="font-size:.72rem;">
                            <span class="text-secondary"><i class="bi bi-paperclip"></i> {{ $doc->file_name }}</span> &middot; {{ number_format(($doc->file_size ?? 1024) / 1024, 1) }} KB
                          </div>
                        @else
                          <div class="text-muted ps-4 small doc-file-subtitle" style="font-size:.72rem; color:#b45309 !important;">
                            <i class="bi bi-dash-circle me-1"></i> Not uploaded by applicant
                          </div>
                        @endif
                        @if($doc->review_note)
                          <div class="ps-4 mt-1" style="font-size:.72rem; color:#dc2626;" id="doc-note-{{ $doc->id }}">
                            <i class="bi bi-chat-left-dots-fill me-1"></i> Note: {{ $doc->review_note }}
                          </div>
                        @else
                          <div class="ps-4 mt-1 d-none" style="font-size:.72rem; color:#dc2626;" id="doc-note-{{ $doc->id }}"></div>
                        @endif
                        <div class="upload-progress-container ps-4 mt-1" id="doc-progress-{{ $doc->id }}"></div>
                      </td>
                      <td><span class="badge bg-light text-dark border">{{ $doc->folder->name ?? 'Folder #' . $doc->folder_id }}</span></td>
                      <td id="doc-badge-col-{{ $doc->id }}">
                        @if(!$doc->file_path)
                          <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                            <i class="bi bi-dash-circle me-1"></i> Not Uploaded
                          </span>
                        @elseif($doc->status === 'validated' || $doc->status === 'approved')
                          <span class="badge bg-success-subtle text-success border border-success-subtle">
                            <i class="bi bi-check-circle-fill me-1"></i> Validated
                          </span>
                        @elseif($doc->status === 'revision_required')
                          <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Revision Needed
                          </span>
                        @else
                          <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                            <i class="bi bi-clock me-1"></i> Uploaded
                          </span>
                        @endif
                      </td>
                      <td class="text-end" id="doc-actions-col-{{ $doc->id }}">
                        <div class="d-inline-flex align-items-center gap-1 justify-content-end">
                          @if($doc->file_path)
                            @php
                              $fileExt = strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION));
                              $isZip = in_array($fileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
                              $dispSize = is_numeric($doc->file_size) ? ($doc->file_size >= 1048576 ? round($doc->file_size/1048576, 1).' MB' : round($doc->file_size/1024, 0).' KB') : ($doc->file_size ?? 'Archive');
                            @endphp
                            <!-- Pure Icon View Button with Tooltip -->
                            <a href="{{ asset($doc->file_path) }}" 
                               target="_blank" 
                               class="btn-action-icon btn-view btn-doc-view" 
                               data-bs-toggle="tooltip"
                               title="{{ $isZip ? 'Download / View ZIP Archive' : 'View Document' }}"
                               data-file-path="{{ asset($doc->file_path) }}"
                               data-file-name="{{ $doc->file_name }}"
                               data-file-size="{{ $dispSize }}"
                               data-doc-name="{{ $doc->document_name }}"
                               data-is-zip="{{ $isZip ? '1' : '0' }}">
                              <i class="bi {{ $isZip ? 'bi-file-earmark-zip text-warning' : 'bi-eye' }}"></i>
                              <span class="visually-hidden">{{ $isZip ? 'View ZIP' : 'View' }}</span>
                            </a>

                            @can('application.edit')
                              <!-- Pure Icon Replace Button with Tooltip -->
                              <label class="btn-action-icon btn-replace mb-0 cursor-pointer btn-dossier-upload-label" data-bs-toggle="tooltip" title="Replace / Re-upload File">
                                <i class="bi bi-arrow-repeat"></i>
                                <input type="file" 
                                       class="d-none input-dossier-file" 
                                       data-doc-id="{{ $doc->id }}" 
                                       data-doc-name="{{ $doc->document_name }}" 
                                       data-upload-url="{{ route('application.document.upload', $doc->id) }}"
                                       accept="{{ $allAllowedExt }}">
                              </label>

                              @if($doc->status !== 'validated')
                                <!-- Pure Icon Mark Valid Button -->
                                <button type="button" 
                                        class="btn-action-icon btn-valid btn-doc-action" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-status="validated" 
                                        data-bs-toggle="tooltip"
                                        title="Mark as Valid">
                                  <i class="bi bi-check2"></i>
                                </button>
                                <!-- Pure Icon Flag Button -->
                                <button type="button" 
                                        class="btn-action-icon btn-flag btn-flag-doc-modal" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-doc-name="{{ $doc->document_name }}" 
                                        data-bs-toggle="tooltip"
                                        title="Flag for Revision">
                                  <i class="bi bi-flag"></i>
                                </button>
                              @else
                                <!-- Pure Icon Undo Button -->
                                <button type="button" 
                                        class="btn-action-icon btn-undo btn-doc-action" 
                                        data-doc-id="{{ $doc->id }}" 
                                        data-status="uploaded" 
                                        data-bs-toggle="tooltip"
                                        title="Undo Validation">
                                  <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                              @endif
                            @endcan
                          @else
                            @can('application.edit')
                              <!-- Pure Icon Upload Button for Missing Documents -->
                              <label class="btn-action-icon btn-upload mb-0 cursor-pointer btn-dossier-upload-label" data-bs-toggle="tooltip" title="Upload Document Directly">
                                <i class="bi bi-cloud-arrow-up"></i>
                                <input type="file" 
                                       class="d-none input-dossier-file" 
                                       data-doc-id="{{ $doc->id }}" 
                                       data-doc-name="{{ $doc->document_name }}" 
                                       data-upload-url="{{ route('application.document.upload', $doc->id) }}"
                                       accept="{{ $allAllowedExt }}">
                              </label>
                            @else
                              <a href="{{ route('step5', ['id' => $application->id]) }}" class="btn-action-icon btn-view" data-bs-toggle="tooltip" title="Upload in Document Vault">
                                <i class="bi bi-upload"></i>
                              </a>
                            @endcan
                          @endif
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">No documents attached to this application.</td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
              <small class="text-muted">Showing {{ $docs->count() }} statutory checklist slots &middot; {{ $totalUploaded }} uploaded</small>
              <!-- <a href="{{ route('step5', ['id' => $application->id]) }}" class="fw-semibold text-decoration-none" style="font-size:.82rem; color:var(--dossier-navy);">
                Upload all remaining in Vault <i class="bi bi-arrow-right"></i>
              </a> -->
            </div>
          </div>

          <!-- WORKFLOW ACTION PANEL (Process Flow 6.2 & 6.3) -->
          <div class="card-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
              <div class="panel-title mb-0">
                @if($isApproved)
                  Application Completed &amp; Approved
                @elseif($isValidated)
                  Validation Passed &mdash; Ready for Final Approval
                @else
                  Ready to Scrutinise &amp; Move Forward?
                @endif
              </div>
              <div class="panel-sub mb-0">
                @if($isApproved)
                  All regulatory steps are completed. You can download the full compliance dossier.
                @elseif($isValidated)
                  Click Approve to lock document sets and grant official approval.
                @else
                  Validating checks all {{ $docs->count() }} attached files. If any document is incomplete, send back for revision.
                @endif
              </div>
            </div>

            @can('application.edit')
            <div class="d-flex gap-2 align-items-center flex-wrap">
              @if(!$isApproved && !$isValidated)
                <!-- Validate Pass Button -->
                <form action="{{ route('application.validate', $application->id) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="action" value="pass">
                  <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-shield-check"></i> Pass Validation
                  </button>
                </form>

                <!-- Reject / Revision Button -->
                <button type="button" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#revisionModal">
                  <i class="bi bi-arrow-counterclockwise"></i> Review &amp; Correct
                </button>

                <!-- Move to Mining Button (Gated) -->
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 btn-trigger-move-mining d-inline-flex align-items-center gap-1" title="Promote to Mining Plan Domain (Requires Step 6.3 Approval)">
                  <i class="bi bi-rocket-takeoff text-warning"></i> Move to Mining Plan
                </button>
              @elseif($isValidated && !$isApproved)
                <!-- Approve Button -->
                <form action="{{ route('application.approve', $application->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-success btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill"></i> Approve Application
                  </button>
                </form>

                <button type="button" class="btn btn-outline-danger btn-sm px-3 d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#revisionModal">
                  <i class="bi bi-arrow-counterclockwise"></i> Request Revision
                </button>

                <!-- Move to Mining Button (Gated) -->
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 btn-trigger-move-mining d-inline-flex align-items-center gap-1" title="Promote to Mining Plan Domain (Requires Step 6.3 Approval)">
                  <i class="bi bi-rocket-takeoff text-warning"></i> Move to Mining Plan
                </button>
              @elseif($isApproved)
                <a href="{{ route('application.report', $application->id) }}" target="_blank" class="btn btn-success btn-sm px-3 shadow-sm d-inline-flex align-items-center gap-1">
                  <i class="bi bi-file-earmark-pdf"></i> Download Report
                </a>
                @if($linkedMining)
                  <a href="{{ route('process', ['id' => $linkedMining->id]) }}" class="btn btn-outline-success btn-sm px-3 d-inline-flex align-items-center gap-1">
                    <i class="fa fa-mountain"></i> Open Mining Plan ({{ $linkedMining->common_id }})
                  </a>
                @else
                  <button type="button" class="btn btn-navy btn-sm px-3 btn-trigger-move-mining d-inline-flex align-items-center gap-1">
                    <i class="bi bi-rocket-takeoff text-warning me-1"></i> Move to Mining Plan
                  </button>
                @endif
              @endif
            </div>
            @endcan
          </div>
        </div>

        <!-- RIGHT SIDEBAR: FOLDERS, ACTIVITY TIMELINE, MIMAS QUICK CARD -->
        <div class="col-lg-4">
          <!-- Folder Summary Card -->
          <div class="card-panel">
            <div class="panel-title mb-1"><i class="fa fa-folder-open text-primary me-2"></i>Folders Vault Summary</div>
            <div class="panel-sub mb-3">Document counts grouped by statutory directory</div>

            <div class="p-2 border rounded mb-2 d-flex align-items-center gap-3" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; background:var(--dossier-navy-soft); color:var(--dossier-navy);">
                <i class="fa fa-folder-open"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold small" style="color:var(--dossier-text-title);">1. Documents</div>
                @php $f7Total = $docs->where('folder_id', 7)->count(); $f7Up = $docs->where('folder_id', 7)->whereNotNull('file_path')->count(); @endphp
                <div class="text-muted small" style="font-size:0.72rem;">{{ $f7Up }} / {{ $f7Total }} uploaded</div>
              </div>
              <span class="badge {{ $f7Up >= $f7Total && $f7Total > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }}" style="font-size:0.7rem;">
                {{ $f7Up >= $f7Total && $f7Total > 0 ? 'Complete' : 'Pending' }}
              </span>
            </div>

            <div class="p-2 border rounded mb-2 d-flex align-items-center gap-3" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; background:var(--dossier-navy-soft); color:var(--dossier-navy);">
                <i class="fa fa-folder"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold small" style="color:var(--dossier-text-title);">2. Lease Application</div>
                @php $f8Total = $docs->where('folder_id', 8)->count(); $f8Up = $docs->where('folder_id', 8)->whereNotNull('file_path')->count(); @endphp
                <div class="text-muted small" style="font-size:0.72rem;">{{ $f8Up }} / {{ $f8Total }} uploaded</div>
              </div>
              <span class="badge {{ $f8Up >= $f8Total && $f8Total > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }}" style="font-size:0.7rem;">
                {{ $f8Up >= $f8Total && $f8Total > 0 ? 'Complete' : 'Pending' }}
              </span>
            </div>

            <div class="p-2 border rounded mb-0 d-flex align-items-center gap-3" style="background:var(--dossier-table-stripe); border-color:var(--dossier-table-border) !important;">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:36px; height:36px; background:var(--dossier-navy-soft); color:var(--dossier-navy);">
                <i class="fa fa-inbox"></i>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold small" style="color:var(--dossier-text-title);">3. Plan &amp; KML</div>
                @php $f9Total = $docs->where('folder_id', 9)->count(); $f9Up = $docs->where('folder_id', 9)->whereNotNull('file_path')->count(); @endphp
                <div class="text-muted small" style="font-size:0.72rem;">{{ $f9Up }} / {{ $f9Total }} uploaded</div>
              </div>
              <span class="badge {{ $f9Up >= $f9Total && $f9Total > 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning-emphasis border border-warning-subtle' }}" style="font-size:0.7rem;">
                {{ $f9Up >= $f9Total && $f9Total > 0 ? 'Complete' : 'Pending' }}
              </span>
            </div>
          </div>

          <!-- CHAPTER VIII: ACTIVITY TIMELINE & AUDIT TRAIL -->
          <div class="card-panel">
            <div class="chapter-header">
              <div class="chapter-title-group">
                <span class="chapter-tag">Chapter VIII</span>
                <div>
                  <h5 class="panel-title">Audit Trail &amp; Chronology</h5>
                  <div class="panel-sub mb-0">Automatic scrutiny events logging</div>
                </div>
              </div>
            </div>

            <div class="timeline">
              @if(isset($activityLogs) && $activityLogs->count() > 0)
                @foreach($activityLogs as $log)
                <div class="tl-item">
                  <div class="tl-title">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</div>
                  <div class="tl-desc">{{ $log->description }}</div>
                  <div class="tl-meta">
                    {{ $log->created_at ? $log->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'Recently' }} 
                    &middot; {{ $log->user->name ?? 'System' }}
                  </div>
                </div>
                @endforeach
              @else
                <div class="tl-item">
                  <div class="tl-title">Application Submitted</div>
                  <div class="tl-desc">Initial lease dossier registered into system.</div>
                  <div class="tl-meta">{{ $application->created_at ? $application->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A') : 'Recently' }} &middot; System</div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal for Review & Correct (Send for Revision) -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--dossier-card-bg); color:var(--dossier-text-body); border:1px solid var(--dossier-card-border);">
      <form action="{{ route('application.reject', $application->id) }}" method="POST">
        @csrf
        <div class="modal-header border-bottom" style="border-color:var(--dossier-card-border) !important;">
          <h5 class="modal-title text-danger"><i class="bi bi-arrow-counterclockwise me-1"></i> Send Back for Revision</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p class="small text-muted mb-3">Please specify the reasons for rejecting or requesting revision. This will be logged in the audit trail.</p>
          <div class="mb-3">
            <label class="form-label fw-bold small">Reason for Revision <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" rows="3" placeholder="e.g. FMB sketch is not clear, Adangal copy is outdated, etc." required style="background:var(--dossier-table-stripe); color:var(--dossier-text-body); border-color:var(--dossier-card-border);"></textarea>
          </div>
        </div>
        <div class="modal-footer border-top" style="border-color:var(--dossier-card-border) !important;">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger btn-sm">Submit Revision Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal for Flagging Single Document -->
<div class="modal fade" id="flagDocModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--dossier-card-bg); color:var(--dossier-text-body); border:1px solid var(--dossier-card-border);">
      <div class="modal-header border-bottom" style="border-color:var(--dossier-card-border) !important;">
        <h5 class="modal-title text-danger"><i class="bi bi-flag-fill me-1"></i> Flag Document for Revision</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-2">Specify why this document requires correction or re-upload:</p>
        <div class="fw-bold mb-2" id="flagDocModalTitle" style="color:var(--dossier-text-title);">Document Name</div>
        <input type="hidden" id="flagDocId" value="">
        <div class="mb-3">
          <label class="form-label small fw-bold">Correction Note / Reason <span class="text-danger">*</span></label>
          <textarea id="flagDocNote" class="form-control" rows="3" placeholder="e.g. Incomplete scan, missing seal/signature, illegible document..." style="background:var(--dossier-table-stripe); color:var(--dossier-text-body); border-color:var(--dossier-card-border);"></textarea>
        </div>
      </div>
      <div class="modal-footer border-top" style="border-color:var(--dossier-card-border) !important;">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger btn-sm" id="btnSubmitFlagDoc">Confirm &amp; Flag</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Move to Mining Plan -->
<div class="modal fade" id="modalMoveToMining" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="background:var(--dossier-card-bg); color:var(--dossier-text-body);">
      <form id="formMoveToMining" action="{{ route('application.moveToMining', $application->id) }}" method="POST">
        @csrf
        <div class="modal-header text-white" style="background:#0F1E4D;">
          <h5 class="modal-title text-white"><i class="bi bi-rocket-takeoff me-2 text-warning"></i> Move to Mining Plan Domain</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <div class="p-3 mb-3 rounded" style="background:var(--dossier-navy-soft); border:1px solid var(--dossier-navy-border);">
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Universal Common ID:</span>
              <strong class="font-monospace text-primary">{{ $commonId }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Current Lease App No:</span>
              <strong class="font-monospace" style="color:var(--dossier-text-title);">{{ $application->application_no }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-1">
              <span class="text-muted small">Applicant / Firm:</span>
              <strong style="color:var(--dossier-text-title);">{{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? 'Applicant') }}</strong>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted small">Quarry Extent:</span>
              <strong style="color:var(--dossier-text-title);">{{ $application->area_extent_ha ? ($application->area_extent_ha . ' Ha') : 'N/A' }}</strong>
            </div>
          </div>
          <p class="small text-muted mb-2">
            Moving this application to the <b>Mining Plan</b> domain will:
          </p>
          <ul class="small text-muted mb-0 ps-3">
            <li class="mb-1">Retain the <b>exact same Universal Common ID</b> across the entire system.</li>
            <li class="mb-1">Auto-carry applicant, district, survey numbers, and mineral data.</li>
            <li class="mb-1">Auto-clone all verified statutory files (Patta, FMB, KML, Drawings) into Mining Folders #8 (Documents) &amp; #11 (Plan).</li>
            <li>Initialize <b>Stage 6.1 (Data Ingest)</b> in the Mining Portal.</li>
          </ul>
        </div>
        <div class="modal-footer" style="background:var(--dossier-table-th); border-top:1px solid var(--dossier-card-border);">
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" id="btnSubmitMoveMining" class="btn btn-navy btn-sm px-3">
            <i class="bi bi-rocket-takeoff me-1"></i> Proceed &amp; Move to Mining Plan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom ZIP / Archive Download Modal -->
<div class="modal fade" id="modalZipFilePreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background:var(--dossier-card-bg); color:var(--dossier-text-body);">
      <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%); color: #fff; padding: 1.5rem 1.5rem 1.25rem;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px);">
            <i class="bi bi-file-earmark-zip text-warning" style="font-size: 1.75rem;"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 1.1rem;">Compressed ZIP Archive</h5>
            <small class="text-white-50" style="font-size: 0.78rem;">Statutory Archive Package</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4" style="background: var(--dossier-card-bg);">
        <div class="p-3 rounded-3 mb-3" style="background: var(--dossier-table-stripe); border: 1px solid var(--dossier-card-border);">
          <div class="d-flex align-items-start gap-2 mb-2">
            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
            <div style="font-size: 0.82rem; color: var(--dossier-text-body); line-height: 1.45;">
              Web browsers cannot preview compressed ZIP files directly. You can download this archive package to your PC to extract and view all enclosed documents and survey datasets.
            </div>
          </div>
          <hr style="margin: 0.75rem 0; opacity: 0.15;">
          <div class="d-flex flex-column gap-1" style="font-size: 0.8rem;">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Document Slot:</span>
              <span class="fw-semibold text-end" id="zipModalDocName" style="color:var(--dossier-text-title);">Document Name</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Archive File:</span>
              <span class="fw-bold font-monospace text-truncate ms-2" id="zipModalFileName" style="max-width: 250px; color:var(--dossier-navy);">package.zip</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Package Size:</span>
              <span class="badge bg-light text-dark border" id="zipModalFileSize">-- MB</span>
            </div>
          </div>
        </div>

        <div class="d-flex flex-column gap-2">
          <a href="#" id="zipModalDownloadBtn" download class="btn btn-navy w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size: 0.9rem; font-weight: 600;">
            <i class="bi bi-cloud-arrow-down-fill fs-5"></i> Download ZIP to Your PC
          </a>
          <button type="button" class="btn btn-outline-secondary w-100 py-2" data-bs-dismiss="modal" style="font-size: 0.85rem;">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
jQuery(document).ready(function($) {
  // CSRF Setup
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // Initialize all Bootstrap Tooltips
  function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl, {
        trigger: 'hover',
        delay: { show: 120, hide: 40 },
        boundary: document.body,
        fallbackPlacements: ['right', 'bottom', 'top', 'left']
      });
    });
  }
  initTooltips();

  // 1-Click Copy Value Interaction
  $(document).on('click', '.btn-copy-val', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var btn = $(this);

    // Dismiss tooltip on click immediately so it doesn't linger
    try {
      var tt = bootstrap.Tooltip.getInstance(this);
      if (tt) tt.hide();
    } catch (err) {}

    var textToCopy = btn.attr('data-copy-text');
    if (!textToCopy) {
      textToCopy = btn.closest('.data-cell').find('.data-value-text').text().trim();
    }
    if (!textToCopy) return;

    var label = btn.attr('data-copy-label') || 'Text';

    var performSuccess = function() {
      var icon = btn.find('i');
      var origClass = icon.attr('class');
      icon.attr('class', 'bi bi-check2 text-success');
      btn.addClass('copied');
      
      if (typeof toastr !== 'undefined') {
        toastr.success('Copied ' + label + ' to clipboard!');
      }

      setTimeout(function() {
        icon.attr('class', origClass);
        btn.removeClass('copied');
      }, 1500);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(textToCopy).then(performSuccess).catch(function() {
        fallbackCopy(textToCopy, performSuccess);
      });
    } else {
      fallbackCopy(textToCopy, performSuccess);
    }
  });

  function fallbackCopy(text, cb) {
    var temp = $('<input>');
    $('body').append(temp);
    temp.val(text).select();
    document.execCommand('copy');
    temp.remove();
    if (cb) cb();
  }

  // Print Official Scrutiny Report (PDF) via hidden iframe
  var isReportPrinting = false;
  function triggerOfficialReportPrint() {
    if (isReportPrinting) return;
    isReportPrinting = true;

    var btn = $('#btn_print_official_report');
    var originalHtml = btn.html();
    btn.html('<span class="spinner-border spinner-border-sm text-secondary" style="width:13px; height:13px; border-width:2px;"></span>');

    var reportUrl = "{{ route('application.report', $application->id) }}";

    var iframe = document.getElementById('reportPrintFrame');
    if (!iframe) {
      iframe = document.createElement('iframe');
      iframe.id = 'reportPrintFrame';
      iframe.name = 'reportPrintFrame';
      iframe.style.position = 'fixed';
      iframe.style.right = '0';
      iframe.style.bottom = '0';
      iframe.style.width = '0';
      iframe.style.height = '0';
      iframe.style.border = '0';
      iframe.style.visibility = 'hidden';
      document.body.appendChild(iframe);
    }

    var cleanup = function() {
      btn.html(originalHtml);
      isReportPrinting = false;
    };

    var timeoutHandle = setTimeout(cleanup, 10000);

    iframe.onload = function() {
      setTimeout(function() {
        try {
          cleanup();
          clearTimeout(timeoutHandle);
          iframe.contentWindow.focus();
          iframe.contentWindow.print();
        } catch (err) {
          cleanup();
          clearTimeout(timeoutHandle);
          window.open(reportUrl + '?autoprint=1', '_blank');
        }
      }, 500);
    };

    iframe.onerror = function() {
      cleanup();
      clearTimeout(timeoutHandle);
      window.open(reportUrl + '?autoprint=1', '_blank');
    };

    iframe.src = reportUrl;
  }

  $('#btn_print_official_report').on('click', function(e) {
    e.preventDefault();
    var t = (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) ? bootstrap.Tooltip.getInstance(this) : null;
    if (t) t.hide();
    this.blur();
    triggerOfficialReportPrint();
  });

  // Intercept Ctrl+P / Cmd+P to print official report instead of web page
  $(document).on('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.keyCode === 80)) {
      e.preventDefault();
      triggerOfficialReportPrint();
    }
  });

  // Copy Full Executive Dossier Summary
  $('#btn_copy_dossier_summary').on('click', function(e) {
    e.preventDefault();
    var lines = [
      '====================================================',
      'GTMS LEASE APPLICATION DOSSIER — EXECUTIVE SUMMARY',
      '====================================================',
      'Application No: {{ $application->application_no }}',
      'Universal Common ID: {{ $commonId }}',
      'Status: {{ ucfirst(str_replace("_", " ", $application->status ?? "Under Scrutiny")) }}',
      'Client / Firm: {{ $application->customer?->company_name ?? ($application->customer?->customer_name ?? "N/A") }}',
      'Proprietor: {{ $application->customer?->customer_name ?? "N/A" }}',
      'District: {{ $application->district->name ?? "N/A" }}',
      'Taluk / Village: {{ $application->village ?: ($application->taluk ?: "N/A") }}',
      'Quarry Extent: {{ $application->area_extent_ha ? ($application->area_extent_ha . " Ha") : "N/A" }} {{ $application->area_extent_acres ? "(" . $application->area_extent_acres . ")" : "" }}',
      'Survey Field No: {{ $survey ? $survey->survey_no : "SF.No 1" }}',
      'Mineral Concession: {{ $mineralDisplay }}',
      'Category / Rule: {{ $application->category->code ?? "N/A" }} ({{ $application->other_category_rule ?: ($application->category->name ?? "Standard") }})',
      'Tenure: {{ $application->lease_period_years ?? 5 }} Years',
      'Primary Contact: {{ $application->contact_person ?? ($application->customer?->customer_name ?? "N/A") }} ({{ $application->contact_mobile ?? ($application->customer?->mobile_num ?? "N/A") }})',
      'Customer MIMAS ID: {{ $application->customer?->mimas_no ?? "N/A" }}',
      'PAN: {{ $application->customer?->pan ?? "N/A" }} | GSTIN: {{ $application->customer?->gstin ?? "N/A" }}',
      'Payment Status: {{ ucfirst($pStatus) }}',
      'Uploaded Documents: {{ $totalUploaded }} / {{ $docs->count() }} ({{ $validatedCount }} Validated)',
      '===================================================='
    ];
    var summaryText = lines.join('\n');

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(summaryText).then(function() {
        if (typeof toastr !== 'undefined') toastr.success('Full Dossier Summary copied to clipboard!');
      });
    } else {
      fallbackCopy(summaryText, function() {
        if (typeof toastr !== 'undefined') toastr.success('Full Dossier Summary copied to clipboard!');
      });
    }
  });

  // Filter Tabs for Document Checklist
  $('.doc-filter-btn').on('click', function() {
    var filter = $(this).data('filter');
    $('.doc-filter-btn').removeClass('active');
    $(this).addClass('active');

    if (filter === 'all') {
      $('.doc-row-item').show();
    } else if (filter === 'uploaded') {
      $('.doc-row-item').hide();
      $('.doc-row-item[data-uploaded="1"]').show();
    } else if (filter === 'pending') {
      $('.doc-row-item').hide();
      $('.doc-row-item[data-uploaded="0"]').show();
    } else {
      $('.doc-row-item').hide();
      $('.doc-row-item[data-folder="' + filter + '"]').show();
    }
  });

  // Mark Valid or Undo Click
  $(document).on('click', '.btn-doc-action', function(e) {
    e.preventDefault();
    var btn = $(this);
    var docId = btn.data('doc-id');
    var targetStatus = btn.data('status');
    var origHtml = btn.html();

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span>');

    $.ajax({
      url: '/application/document/' + docId + '/status',
      type: 'POST',
      data: {
        status: targetStatus
      },
      success: function(res) {
        if (res.status === 1) {
          updateDocRowUI(docId, targetStatus);
          if (res.total_uploaded !== undefined) {
            $('#kpi-validated-count').html(res.total_validated + ' <span class="dossier-kpi-total">/ ' + res.total_uploaded + '</span>');
            if (res.total_validated >= res.total_uploaded && res.total_uploaded > 0) {
              $('#kpi-validated-sub').removeClass('pill-warning').addClass('pill-success').css('color', '');
              $('#kpi-validated-sub i').attr('class', 'bi bi-check-circle-fill');
              $('#kpi-validated-text').text('All Validated');
            } else {
              $('#kpi-validated-sub').removeClass('pill-success').addClass('pill-warning').css('color', '');
              $('#kpi-validated-sub i').attr('class', 'bi bi-clock-fill');
              $('#kpi-validated-text').text((res.total_uploaded - res.total_validated) + ' Awaiting Scrutiny');
            }
          }
          if (typeof toastr !== 'undefined') {
            if (targetStatus === 'validated') {
              toastr.success('Document marked as Validated');
            } else {
              toastr.info('Document status updated to ' + targetStatus);
            }
          }
        } else {
          showNotify('error', 'Action Failed', res.message || 'Action failed.');
          btn.prop('disabled', false).html(origHtml);
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html(origHtml);
        var msg = 'Failed to update document status.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          showNotify('error', 'Status Error', msg);
        }
      }
    });
  });

  // Flag Document Modal Open
  $(document).on('click', '.btn-flag-doc-modal', function(e) {
    e.preventDefault();
    var docId = $(this).data('doc-id');
    var docName = $(this).data('doc-name') || $(this).closest('tr').find('.doc-name-text').text().trim();
    $('#flagDocId').val(docId);
    $('#flagDocModalTitle').text(docName);
    $('#flagDocNote').val('');
    var flagModal = new bootstrap.Modal(document.getElementById('flagDocModal'));
    flagModal.show();
  });

  // Submit Flag Note via AJAX
  $('#btnSubmitFlagDoc').on('click', function(e) {
    e.preventDefault();
    var docId = $('#flagDocId').val();
    var note = $('#flagDocNote').val().trim();

    if (!note) {
      if (typeof toastr !== 'undefined') toastr.warning('Please enter a reason or note for flagging.');
      else alert('Please enter a reason or note for flagging.');
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Submitting...');

    $.ajax({
      url: '/application/document/' + docId + '/status',
      type: 'POST',
      data: {
        status: 'revision_required',
        review_note: note
      },
      success: function(res) {
        btn.prop('disabled', false).text('Confirm & Flag');
        var flagModalEl = document.getElementById('flagDocModal');
        var modalInstance = bootstrap.Modal.getInstance(flagModalEl);
        if (modalInstance) modalInstance.hide();

        updateDocRowUI(docId, 'revision_required', note);
        if (res.total_uploaded !== undefined) {
          $('#kpi-validated-count').html(res.total_validated + ' <span class="dossier-kpi-total">/ ' + res.total_uploaded + '</span>');
          $('#kpi-validated-sub').removeClass('pill-success').addClass('pill-warning').css('color', '');
          $('#kpi-validated-sub i').attr('class', 'bi bi-clock-fill');
          $('#kpi-validated-text').text((res.total_uploaded - res.total_validated) + ' Awaiting Scrutiny');
        }
        if (typeof toastr !== 'undefined') {
          toastr.warning('Document flagged for revision');
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).text('Confirm & Flag');
        var msg = 'Failed to flag document.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          showNotify('error', 'Flag Error', msg);
        }
      }
    });
  });

  function showNotify(icon, title, text) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#0F1E4D'
      });
    } else if (typeof toastr !== 'undefined') {
      toastr[icon === 'error' ? 'error' : (icon === 'warning' ? 'warning' : 'info')](text, title);
    } else {
      alert(title + ': ' + text);
    }
  }

  // Toggle MIMAS Password Reveal in View Application
  $('#btn_toggle_view_pass').on('click', function(e) {
    e.preventDefault();
    var masked = $('#mimas_pass_masked');
    var plain = $('#mimas_pass_plain');
    var icon = $('#icon_view_pass');
    if (plain.is(':visible')) {
      plain.hide();
      masked.show();
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      masked.hide();
      plain.show();
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  $('#btn_copy_view_pass').on('click', function(e) {
    e.preventDefault();
    var pass = $(this).attr('data-clipboard-text');
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(pass).then(function() {
        if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
        else alert('MIMAS Password copied to clipboard!');
      });
    } else {
      fallbackCopy(pass, function() {
        if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
        else alert('MIMAS Password copied to clipboard!');
      });
    }
  });

  // Intercept clicks on ZIP archive view buttons
  $(document).on('click', '.btn-doc-view', function(e) {
    var btn = $(this);
    var href = btn.attr('href') || btn.data('file-path') || '';
    var isZip = btn.attr('data-is-zip') === '1' || /\.(zip|rar|7z|tar|gz)(\?.*)?$/i.test(href);

    if (isZip) {
      e.preventDefault();
      var fileName = btn.attr('data-file-name') || href.split('/').pop() || 'archive.zip';
      var docName = btn.attr('data-doc-name') || btn.closest('tr').find('.doc-name-text').text().trim() || 'Attached Document';
      var fileSize = btn.attr('data-file-size') || 'ZIP Archive';

      $('#zipModalFileName').text(decodeURIComponent(fileName));
      $('#zipModalDocName').text(docName);
      $('#zipModalFileSize').text(fileSize);

      var dlBtn = $('#zipModalDownloadBtn');
      dlBtn.attr('href', href);
      dlBtn.attr('download', decodeURIComponent(fileName));

      var zipModalEl = document.getElementById('modalZipFilePreview');
      if (zipModalEl) {
        var zipModal = bootstrap.Modal.getOrCreateInstance(zipModalEl);
        zipModal.show();
      }
    }
  });

  // ==========================================================================
  // RESILIENT CHUNKED & DIRECT UPLOAD ENGINE (STEP 5 PARITY STANDARD)
  // Supports large videos, massive datasets, ZIPs, CAD, and resumable sessions
  // ==========================================================================

  // 1. Detect paused / unfinished chunked upload sessions from previous page loads and render Resume / Discard UI
  async function initDossierPausedUploads() {
    $('.doc-row-item').each(async function() {
      var row = $(this);
      var docId = row.data('doc-id');
      var docFieldId = row.data('doc-field-id');
      if (!docId) return;

      var foundKey = null;
      var foundToken = null;
      for (var i = 0; i < localStorage.length; i++) {
        var k = localStorage.key(i);
        if (k && (k.startsWith('gtms_dossier_chk_v2_' + docId + '_') || (docFieldId && k.startsWith('gtms_chk_v2_' + docFieldId + '_')))) {
          foundKey = k;
          foundToken = localStorage.getItem(k);
          break;
        }
      }

      if (foundToken) {
        try {
          var statusRes = await fetch('/api/upload/chunk/status?upload_token=' + encodeURIComponent(foundToken), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          if (statusRes.ok) {
            var statusData = await statusRes.json();
            if (statusData.success && (statusData.status === 'uploading' || statusData.status === 'pending')) {
              var uploadedChunks = statusData.uploaded_chunks_count || 0;
              var totalChunks = statusData.total_chunks || 1;
              var chunkSize = statusData.chunk_size || (5 * 1024 * 1024);
              var uploadedMB = ((uploadedChunks * chunkSize) / 1048576).toFixed(1);
              var totalMB = (statusData.total_size / 1048576).toFixed(1);
              var pct = totalChunks > 0 ? Math.min(99, Math.round((uploadedChunks / totalChunks) * 100)) : 0;

              var isReupload = row.attr('data-uploaded') === '1';
              var newFileName = statusData.file_name || 'file';
              var safeName = $('<div>').text(newFileName).html();
              var newExt = (newFileName.indexOf('.') !== -1 ? newFileName.split('.').pop() : '').toLowerCase();

              var isZip = ['zip', 'rar', '7z', 'tar', 'gz'].includes(newExt);
              var isVideo = ['mp4', 'webm', 'mkv', 'avi', 'mov'].includes(newExt);
              var isImg = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp'].includes(newExt);
              var isGeo = ['kml', 'kmz'].includes(newExt);
              var isWord = ['doc', 'docx'].includes(newExt);
              var isExcel = ['xls', 'xlsx', 'csv'].includes(newExt);
              var isPpt = ['ppt', 'pptx'].includes(newExt);

              var tempIconClass = 'bi-file-earmark-pdf text-danger';
              if (isZip) tempIconClass = 'bi-file-earmark-zip text-warning';
              else if (isVideo) tempIconClass = 'bi-file-earmark-play text-info';
              else if (isImg) tempIconClass = 'bi-file-earmark-image text-primary';
              else if (isGeo) tempIconClass = 'bi-geo-alt-fill text-success';
              else if (isWord) tempIconClass = 'bi-file-earmark-word text-primary';
              else if (isExcel) tempIconClass = 'bi-file-earmark-excel text-success';
              else if (isPpt) tempIconClass = 'bi-file-earmark-ppt text-danger';

              // 1. Temporarily show incoming file icon in place of old icon
              var fileChip = row.find('.file-chip');
              fileChip.find('i').not('.pause-temp-icon').hide();
              fileChip.find('.pause-temp-icon').remove();
              fileChip.prepend('<i class="bi ' + tempIconClass + ' fs-5 pause-temp-icon"></i>');

              // 2. Hide old subtitle and show pending replacement file information
              row.find('.doc-file-subtitle').hide();
              var progContainer = $('#doc-progress-' + docId);
              if (progContainer.length) {
                var pauseText = pct > 0
                  ? 'Upload paused at ' + pct + '% (' + uploadedMB + ' / ' + totalMB + ' MB)'
                  : 'Upload paused at 0% (0.0 / ' + totalMB + ' MB)';
                var prefixLabel = isReupload ? 'Re-uploading:' : 'Uploading:';
                progContainer.html(
                  '<div class="pause-pending-info text-muted small" style="font-size:0.72rem;">' +
                    '<span class="text-warning fw-semibold"><i class="bi bi-arrow-repeat me-1"></i> ' + prefixLabel + '</span> ' +
                    '<span class="text-secondary"><i class="bi bi-paperclip"></i> ' + safeName + '</span> &middot; ' + totalMB + ' MB' +
                  '</div>' +
                  '<div class="text-warning fw-semibold small pause-indicator mt-1" style="font-size:0.72rem;">' +
                    '<i class="bi bi-pause-circle me-1"></i> ' + pauseText +
                  '</div>'
                );
              }

              // 3. Hide ALL other action buttons in the cell, show ONLY Resume and Discard
              var actionsGroup = $('#doc-actions-col-' + docId).find('.d-inline-flex');
              if (actionsGroup.length && !actionsGroup.find('.btn-dossier-resume').length) {
                actionsGroup.children().hide();

                var resumeBtnHtml = '<button type="button" class="btn-action-icon btn-resume btn-dossier-resume" data-doc-id="' + docId + '" data-bs-toggle="tooltip" title="Resume Upload (' + pct + '%)"><i class="bi bi-play-circle-fill"></i><span class="visually-hidden">Resume (' + pct + '%)</span></button>';
                var discardBtnHtml = '<button type="button" class="btn-action-icon btn-discard btn-dossier-discard ms-1" data-doc-id="' + docId + '" data-upload-token="' + foundToken + '" data-storage-key="' + foundKey + '" data-bs-toggle="tooltip" title="Discard Paused Upload"><i class="bi bi-trash3"></i><span class="visually-hidden">Discard</span></button>';
                actionsGroup.prepend(resumeBtnHtml + discardBtnHtml);
                initTooltips();
              }
            } else if (statusData.status === 'completed' || !statusData.success) {
              localStorage.removeItem(foundKey);
            }
          } else {
            localStorage.removeItem(foundKey);
          }
        } catch (e) {
          localStorage.removeItem(foundKey);
        }
      }
    });
  }

  initDossierPausedUploads();

  // 2. Resume Button Handler: triggers hidden file input
  $(document).on('click', '.btn-dossier-resume', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var docId = $(this).data('doc-id');
    var fileInput = $('#doc-row-' + docId).find('.input-dossier-file');
    if (fileInput.length) {
      fileInput.trigger('click');
    }
  });

  // 3. Discard Button Handler: cancels upload session and purges server chunks
  $(document).on('click', '.btn-dossier-discard', function(e) {
    e.preventDefault();
    e.stopPropagation();
    var btn = $(this);
    var docId = btn.data('doc-id');
    var token = btn.data('upload-token');
    var storageKey = btn.data('storage-key');
    var row = $('#doc-row-' + docId);

    function performDossierDiscard() {
      btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>');

      fetch('/api/upload/chunk/cancel', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ upload_token: token })
      })
      .finally(function() {
        if (storageKey) {
          try { localStorage.removeItem(storageKey); } catch (e) {}
        }
        // Remove pause temporary elements and restore previous state
        row.find('.pause-temp-icon, .pause-pending-info, .pause-indicator').remove();
        row.find('.file-chip i').show();
        row.find('.doc-file-subtitle').show();
        row.find('.btn-dossier-resume').remove();
        btn.remove();
        // Restore all previous action buttons for this row
        row.find('#doc-actions-col-' + docId + ' .d-inline-flex').children().show();
        if (typeof toastr !== 'undefined') {
          toastr.success('Paused upload discarded and previous state restored.');
        }
      });
    }

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Discard Paused Upload?',
        text: 'This will purge all temporary chunks from the server and reset this document slot.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Discard',
        cancelButtonText: 'Cancel'
      }).then(function(result) {
        if (result.isConfirmed) {
          performDossierDiscard();
        }
      });
    } else {
      if (confirm('Discard paused upload and purge temporary files?')) {
        performDossierDiscard();
      }
    }
  });

  // In-Place Dynamic DOM Updater after Successful Document Upload (No Page Reload)
  function markDossierRowUploaded(docId, res, fileObj, uploadUrl, acceptExt) {
    var row = $('#doc-row-' + docId);
    if (!row.length) return;

    row.attr('data-uploaded', '1');

    var fileName = (res && res.file_name) || (fileObj ? fileObj.name : 'Uploaded File');
    var fileUrl = (res && res.file_path) || '#';
    var fileSize = (res && res.file_size) || '';
    if (!fileSize && fileObj && fileObj.size) {
      fileSize = fileObj.size >= 1048576 
        ? (fileObj.size / 1048576).toFixed(1) + ' MB'
        : (fileObj.size / 1024).toFixed(0) + ' KB';
    }
    var docName = row.data('doc-name') || '';

    var ext = (fileName.indexOf('.') !== -1 ? fileName.split('.').pop() : '').toLowerCase();
    var isZip = ['zip', 'rar', '7z', 'tar', 'gz'].includes(ext);
    var isVideo = ['mp4', 'webm', 'mkv', 'avi', 'mov'].includes(ext);
    var isImg = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'bmp'].includes(ext);
    var isGeo = ['kml', 'kmz'].includes(ext);
    var isWord = ['doc', 'docx'].includes(ext);
    var isExcel = ['xls', 'xlsx', 'csv'].includes(ext);
    var isPpt = ['ppt', 'pptx'].includes(ext);

    var iconClass = 'bi-file-earmark-pdf text-danger';
    if (isZip) iconClass = 'bi-file-earmark-zip text-warning';
    else if (isVideo) iconClass = 'bi-file-earmark-play text-info';
    else if (isImg) iconClass = 'bi-file-earmark-image text-primary';
    else if (isGeo) iconClass = 'bi-geo-alt-fill text-success';
    else if (isWord) iconClass = 'bi-file-earmark-word text-primary';
    else if (isExcel) iconClass = 'bi-file-earmark-excel text-success';
    else if (isPpt) iconClass = 'bi-file-earmark-ppt text-danger';

    // 1. Update file type icon
    var fileChip = row.find('.file-chip');
    fileChip.find('i').remove();
    fileChip.prepend('<i class="bi ' + iconClass + ' fs-5"></i>');

    // 2. Update subtitle with paperclip, filename and filesize
    var subtitleDiv = row.find('.doc-file-subtitle');
    var safeFileName = $('<div>').text(fileName).html();
    var safeFileSize = $('<div>').text(fileSize).html();
    var safeDocName = $('<div>').text(docName).html();

    if (subtitleDiv.length) {
      subtitleDiv.attr('style', 'font-size:.72rem;')
        .html('<span class="text-secondary"><i class="bi bi-paperclip"></i> ' + safeFileName + '</span> &middot; ' + safeFileSize);
    } else {
      fileChip.after('<div class="text-muted ps-4 small doc-file-subtitle" style="font-size:.72rem;"><span class="text-secondary"><i class="bi bi-paperclip"></i> ' + safeFileName + '</span> &middot; ' + safeFileSize + '</div>');
    }

    // 3. Clear progress container
    $('#doc-progress-' + docId).empty();

    // 4. Update status badge
    $('#doc-badge-col-' + docId).html(
      '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">' +
        '<i class="bi bi-clock me-1"></i> Uploaded' +
      '</span>'
    );

    // 5. Update actions buttons (Pure icons: View, Replace, Mark Valid, Flag)
    var viewBtn = '<a href="' + fileUrl + '" target="_blank" class="btn-action-icon btn-view btn-doc-view" data-bs-toggle="tooltip" title="' + (isZip ? 'Download / View ZIP Archive' : 'View Document') + '" data-file-path="' + fileUrl + '" data-file-name="' + safeFileName + '" data-file-size="' + safeFileSize + '" data-doc-name="' + safeDocName + '" data-is-zip="' + (isZip ? '1' : '0') + '"><i class="bi ' + (isZip ? 'bi-file-earmark-zip text-warning' : 'bi-eye') + '"></i><span class="visually-hidden">' + (isZip ? 'View ZIP' : 'View') + '</span></a>';
    var replaceBtn = '<label class="btn-action-icon btn-replace mb-0 cursor-pointer btn-dossier-upload-label" data-bs-toggle="tooltip" title="Replace / Re-upload File"><i class="bi bi-arrow-repeat"></i><input type="file" class="d-none input-dossier-file" data-doc-id="' + docId + '" data-doc-name="' + safeDocName + '" data-upload-url="' + uploadUrl + '" accept="' + acceptExt + '"></label>';
    var validBtn = '<button type="button" class="btn-action-icon btn-valid btn-doc-action" data-doc-id="' + docId + '" data-status="validated" data-bs-toggle="tooltip" title="Mark as Valid"><i class="bi bi-check2"></i></button>';
    var flagBtn = '<button type="button" class="btn-action-icon btn-flag btn-flag-doc-modal" data-doc-id="' + docId + '" data-doc-name="' + safeDocName + '" data-bs-toggle="tooltip" title="Flag for Revision"><i class="bi bi-flag"></i></button>';

    $('#doc-actions-col-' + docId).find('.d-inline-flex').html(viewBtn + replaceBtn + validBtn + flagBtn);

    // 6. Update Filter Tab badges & Footer stats
    var totalUploaded = $('.doc-row-item[data-uploaded="1"]').length;
    var totalAll = $('.doc-row-item').length;
    var totalPending = totalAll - totalUploaded;
    $('.doc-filter-btn[data-filter="uploaded"]').text('Uploaded (' + totalUploaded + ')');
    $('.doc-filter-btn[data-filter="pending"]').text('Pending (' + totalPending + ')');
    $('.doc-filter-pills').closest('.card-body').find('small.text-muted').html('Showing ' + totalAll + ' statutory checklist slots &middot; ' + totalUploaded + ' uploaded');

    // 7. Update Top KPI card if elements exist
    if (res && res.total_uploaded !== undefined) {
      $('#kpi-validated-count').find('.dossier-kpi-total').text('/ ' + res.total_uploaded);
    }

    // 8. Re-initialize tooltips
    initTooltips();

    // 9. SweetAlert success notification (No page reload!)
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: 'Uploaded Successfully',
        text: (res && res.message) || (docName + ' has been uploaded and attached to the dossier.'),
        confirmButtonColor: '#0F1E4D',
        timer: 3000,
        timerProgressBar: true
      });
    } else if (typeof toastr !== 'undefined') {
      toastr.success((res && res.message) || 'Document uploaded successfully!');
    }
  }

  // 4. In-Place Dossier Document Upload / Replace (Chunked & Direct Engine)
  $(document).on('change', '.input-dossier-file', async function() {
    var fileInput = this;
    if (!fileInput.files || fileInput.files.length === 0) return;

    var file = fileInput.files[0];
    var docId = $(fileInput).data('doc-id');
    var uploadUrl = $(fileInput).data('upload-url');
    var acceptExt = $(fileInput).attr('accept') || '*';
    var row = $('#doc-row-' + docId);
    var docFieldId = row.data('doc-field-id');
    var labelBtn = $(fileInput).closest('label');
    var originalHtml = labelBtn.html();
    var progressContainer = $('#doc-progress-' + docId);
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    row.find('.btn-dossier-resume, .btn-dossier-discard, .pause-indicator, .pause-temp-icon, .pause-pending-info').remove();
    row.find('.file-chip i').show();
    row.find('.doc-file-subtitle').show();
    row.find('#doc-actions-col-' + docId + ' .d-inline-flex').children().show();
    labelBtn.addClass('disabled').css('pointer-events', 'none');

    var fileSize = file.size;

    function updateProgressUI(currentBytes, totalBytes, statusText) {
      var curMB = (currentBytes / 1048576).toFixed(1);
      var totMB = (totalBytes / 1048576).toFixed(1);
      var pct = Math.min(99, Math.round((currentBytes / totalBytes) * 100));

      labelBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" style="width:11px; height:11px;"></span> <span style="font-size:.72rem;">' + pct + '%</span>');

      if (progressContainer.length) {
        progressContainer.html(
          '<div class="upload-progress-wrap mt-1">' +
            '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px;">' +
              '<span class="text-primary fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i> ' + (statusText || 'Uploading:') + ' <strong>' + curMB + ' MB</strong> / ' + totMB + ' MB</span>' +
              '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">' + pct + '%</span>' +
            '</div>' +
            '<div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">' +
              '<div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: ' + pct + '%;"></div>' +
            '</div>' +
          '</div>'
        );
      }
    }

    updateProgressUI(0, fileSize, 'Preparing upload:');

    // Auto-chunk any video file > 2MB and any other file > 5MB
    var isVideo = /\.(mp4|webm|mkv|avi|mov)$/i.test(file.name);
    var CHUNK_THRESHOLD = isVideo ? 2 * 1024 * 1024 : 5 * 1024 * 1024;

    try {
      if (fileSize > CHUNK_THRESHOLD) {
        // CHUNKED UPLOAD PIPELINE (for videos, CAD, & massive datasets)
        var CHUNK_SIZE = 5 * 1024 * 1024; // 5 MB chunks for responsive saves & quick chunk commits
        if (fileSize > 1024 * 1024 * 1024) {
          CHUNK_SIZE = 25 * 1024 * 1024; // 25 MB chunks for > 1GB
        } else if (fileSize > 200 * 1024 * 1024) {
          CHUNK_SIZE = 15 * 1024 * 1024; // 15 MB chunks for 200MB - 1GB
        }

        var totalChunks = Math.ceil(fileSize / CHUNK_SIZE);
        var storageKey = 'gtms_dossier_chk_v2_' + docId + '_' + encodeURIComponent(file.name) + '_' + fileSize + '_' + CHUNK_SIZE;

        // Auto-Purge any abandoned upload session for this document slot
        for (var i = localStorage.length - 1; i >= 0; i--) {
          var k = localStorage.key(i);
          var isCurrentDoc = (k && k.startsWith('gtms_dossier_chk_v2_' + docId + '_')) || (docFieldId && k && k.startsWith('gtms_chk_v2_' + docFieldId + '_'));
          if (isCurrentDoc && k !== storageKey) {
            var abandonedToken = localStorage.getItem(k);
            if (abandonedToken) {
              fetch('/api/upload/chunk/cancel', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ upload_token: abandonedToken })
              }).catch(function(e) { console.warn('Purge abandoned upload:', e); });
            }
            localStorage.removeItem(k);
          }
        }

        var uploadToken = null;
        var startingChunk = 0;

        // Check if an existing unfinished upload session exists in localStorage
        var savedToken = localStorage.getItem(storageKey);
        if (!savedToken && docFieldId) {
          var altKey = 'gtms_chk_v2_' + docFieldId + '_' + encodeURIComponent(file.name) + '_' + fileSize + '_' + CHUNK_SIZE;
          var altToken = localStorage.getItem(altKey);
          if (altToken) {
            savedToken = altToken;
            storageKey = altKey;
          }
        }

        if (savedToken) {
          try {
            var statusRes = await fetch('/api/upload/chunk/status?upload_token=' + encodeURIComponent(savedToken), {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (statusRes.ok) {
              var statusData = await statusRes.json();
              if (statusData.success && (statusData.status === 'uploading' || statusData.status === 'pending') && statusData.chunk_size === CHUNK_SIZE && statusData.total_chunks === totalChunks) {
                uploadToken = savedToken;
                var missing = statusData.missing_chunks || [];
                startingChunk = missing.length > 0 ? missing[0] : 0;
                var resumedBytes = Math.min(fileSize, startingChunk * CHUNK_SIZE);
                updateProgressUI(resumedBytes, fileSize, 'Resuming from chunk ' + (startingChunk + 1) + '/' + totalChunks + ':');
              } else {
                localStorage.removeItem(storageKey);
              }
            } else {
              localStorage.removeItem(storageKey);
            }
          } catch (chkErr) {
            localStorage.removeItem(storageKey);
          }
        }

        if (!uploadToken) {
          var initRes = await fetch('/api/upload/chunk/init', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              file_name: file.name,
              total_size: fileSize,
              chunk_size: CHUNK_SIZE,
              total_chunks: totalChunks,
              mime_type: file.type || 'application/octet-stream',
              target_module: 'lease_applications'
            })
          });
          var initData = await initRes.json();
          if (!initData.success) {
            throw new Error(initData.message || 'Chunk upload initialization failed');
          }
          uploadToken = initData.upload_token;
          localStorage.setItem(storageKey, uploadToken);
        }

        // Parallel Concurrent Chunk Uploader (3 workers)
        var CONCURRENCY = 3;
        var chunkLoadedBytes = {};
        for (var c = 0; c < startingChunk; c++) {
          chunkLoadedBytes[c] = Math.min(CHUNK_SIZE, fileSize - c * CHUNK_SIZE);
        }
        var nextChunkIndex = startingChunk;
        var uploadError = null;

        function refreshOverallProgress() {
          var totalUploadedSoFar = 0;
          for (var idx = 0; idx < totalChunks; idx++) {
            totalUploadedSoFar += (chunkLoadedBytes[idx] || 0);
          }
          var finishedChunks = Object.keys(chunkLoadedBytes).filter(function(key) {
            var expected = (key == totalChunks - 1) ? (fileSize - key * CHUNK_SIZE) : CHUNK_SIZE;
            return chunkLoadedBytes[key] >= expected;
          }).length;
          var displayIdx = Math.min(totalChunks, finishedChunks + 1);
          updateProgressUI(totalUploadedSoFar, fileSize, 'Chunk ' + displayIdx + '/' + totalChunks + ':');
        }

        async function dossierUploadWorker() {
          while (nextChunkIndex < totalChunks && !uploadError) {
            var chunkIdx = nextChunkIndex++;
            var start = chunkIdx * CHUNK_SIZE;
            var end = Math.min(fileSize, start + CHUNK_SIZE);
            var chunkBlob = file.slice(start, end);
            var currentChunkBytes = end - start;

            var chunkSucceeded = false;
            var lastChunkErr = null;
            var maxChunkRetries = 3;

            for (var attempt = 1; attempt <= maxChunkRetries; attempt++) {
              if (uploadError) break;
              try {
                await new Promise(function(resolve, reject) {
                  var xhr = new XMLHttpRequest();
                  xhr.open('POST', '/api/upload/chunk/upload', true);
                  xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                  xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                      chunkLoadedBytes[chunkIdx] = e.loaded;
                      refreshOverallProgress();
                    }
                  };

                  xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                      try {
                        var res = JSON.parse(xhr.responseText);
                        if (res.success) {
                          chunkLoadedBytes[chunkIdx] = currentChunkBytes;
                          refreshOverallProgress();
                          resolve(res);
                        } else {
                          reject(new Error(res.message || 'Chunk failed'));
                        }
                      } catch (pe) {
                        reject(new Error('Invalid response for chunk ' + (chunkIdx + 1)));
                      }
                    } else {
                      if (xhr.status === 404) {
                        try { localStorage.removeItem(storageKey); } catch(e) {}
                      }
                      reject(new Error('Chunk upload server error: HTTP ' + xhr.status));
                    }
                  };

                  xhr.onerror = function() {
                    reject(new Error('Network error uploading chunk ' + (chunkIdx + 1)));
                  };

                  var fd = new FormData();
                  fd.append('upload_token', uploadToken);
                  fd.append('chunk_index', chunkIdx);
                  fd.append('chunk', chunkBlob, file.name);
                  xhr.send(fd);
                });

                chunkSucceeded = true;
                break;
              } catch (cErr) {
                lastChunkErr = cErr;
                if (attempt < maxChunkRetries) {
                  await new Promise(function(r) { setTimeout(r, attempt * 1000); });
                }
              }
            }

            if (!chunkSucceeded && !uploadError) {
              uploadError = lastChunkErr || new Error('Chunk ' + (chunkIdx + 1) + ' failed after retries');
              throw uploadError;
            }
          }
        }

        var workerPool = [];
        var activeWorkers = Math.min(CONCURRENCY, totalChunks - startingChunk);
        for (var w = 0; w < activeWorkers; w++) {
          workerPool.push(dossierUploadWorker());
        }
        await Promise.all(workerPool);
        if (uploadError) throw uploadError;

        labelBtn.html('<span class="spinner-border spinner-border-sm me-1" style="width:11px;height:11px;"></span> Assembling...');
        if (progressContainer.length) {
          progressContainer.html(
            '<div class="upload-progress-wrap mt-1">' +
              '<div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px;">' +
                '<span class="text-success fw-semibold"><i class="bi bi-gear-wide-connected me-1"></i> Assembling file on server...</span>' +
                '<span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">100%</span>' +
              '</div>' +
              '<div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">' +
                '<div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%;"></div>' +
              '</div>' +
            '</div>'
          );
        }

        var assembleRes = await fetch('/api/upload/chunk/assemble', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            upload_token: uploadToken,
            target_module: 'lease_applications'
          })
        });
        var assembleData = await assembleRes.json();
        if (!assembleData.success && !assembleData.file_path) {
          try { localStorage.removeItem(storageKey); } catch(e) {}
          throw new Error(assembleData.message || 'File assembly failed on server');
        }

        var finalFd = new FormData();
        finalFd.append('upload_token', uploadToken);
        finalFd.append('_token', csrfToken);

        var finalRes = await fetch(uploadUrl, {
          method: 'POST',
          body: finalFd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        });
        var finalData = await finalRes.json();
        if (finalData.status !== 1 && !finalData.success) {
          throw new Error(finalData.message || 'Failed to save document record');
        }

        localStorage.removeItem(storageKey);

        // Instant in-place DOM update without page reload
        markDossierRowUploaded(docId, finalData, file, uploadUrl, acceptExt);

      } else {
        // DIRECT XHR UPLOAD WITH REAL LIVE PROGRESS (<= 5 MB)
        await new Promise(function(resolve, reject) {
          var xhr = new XMLHttpRequest();
          xhr.open('POST', uploadUrl, true);
          xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
          xhr.setRequestHeader('Accept', 'application/json');

          xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
              updateProgressUI(e.loaded, e.total, 'Uploading:');
            }
          };

          xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
              try {
                var res = JSON.parse(xhr.responseText);
                if (res.status === 1 || res.success) {
                  // Instant in-place DOM update without page reload
                  markDossierRowUploaded(docId, res, file, uploadUrl, acceptExt);
                  resolve(res);
                } else {
                  reject(new Error(res.message || 'Upload failed'));
                }
              } catch (pe) {
                reject(new Error('Invalid response from server'));
              }
            } else {
              reject(new Error('Server error: HTTP ' + xhr.status));
            }
          };

          xhr.onerror = function() {
            reject(new Error('Network error during upload'));
          };

          var formData = new FormData();
          formData.append('_token', csrfToken);
          formData.append('file', file);
          xhr.send(formData);
        });
      }
    } catch (err) {
      labelBtn.removeClass('disabled').css('pointer-events', '').html(originalHtml);
      fileInput.value = '';
      if (progressContainer.length) {
        progressContainer.html('<div class="text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i> ' + (err.message || 'Upload failed') + '</div>');
      }
      showNotify('error', 'Upload Error', err.message || 'Upload failed. Please try again.');
    }
  });

  // 5. Verification Gate: Step 6.3 (Approve Data) Must Be Completed Before Moving to Mining
  function triggerStep63ApprovalAlert() {
    if (typeof Swal === 'undefined') {
      alert('Approval Required: Step 6.3 (Approve Data) must be approved before moving this lease application to Mining Plan.');
      return;
    }

    var alertHtml = '' +
      '<div class="gtms-compact-gate-body text-start mt-1">' +
        '<div class="p-2 px-3 rounded mb-2" style="background:#fffbeb; border:1px solid #fde68a; color:#92400e; font-size:0.84rem; line-height:1.45;">' +
          '<i class="bi bi-shield-exclamation text-warning me-1 fw-bold"></i> ' +
          'Please complete <strong>Step 6.3 (Approve Data)</strong> first. Only approved applications can be moved to the Mining Plan domain.' +
        '</div>' +
        '<div class="d-flex align-items-center justify-content-between px-1 text-muted" style="font-size:0.75rem;">' +
          '<span>Current Status: <strong class="text-dark">{{ ucfirst(str_replace("_", " ", $application->status ?? "Under Scrutiny")) }}</strong></span>' +
          '<span class="badge bg-warning text-dark border border-warning" style="font-size:0.7rem; font-weight:600;">Step 6.3 Pending</span>' +
        '</div>' +
      '</div>';

    Swal.fire({
      icon: 'warning',
      title: 'Approval Required',
      html: alertHtml,
      confirmButtonText: 'Understood',
      customClass: {
        popup: 'gtms-gate-swal-compact'
      }
    });
  }
  window.triggerStep63ApprovalAlert = triggerStep63ApprovalAlert;

  // 6. Success Alert: Lease Application Transitioned to Mining Plan
  function triggerMiningTransitionSuccessAlert(commonId, miningAppNo, clonedCount) {
    if (typeof Swal === 'undefined') {
      alert('Moved to Mining Plan: ' + (miningAppNo || '') + ' (Common ID: ' + (commonId || '') + '). Reloading page...');
      window.location.reload();
      return;
    }

    var alertHtml = '' +
      '<div class="gtms-compact-gate-body text-start mt-1">' +
        '<div class="p-2 px-3 rounded mb-2" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; font-size:0.83rem; line-height:1.45;">' +
          '<i class="bi bi-check-circle-fill text-success me-1"></i> ' +
          'Lease Application successfully transitioned to <strong>Mining Plan</strong> domain queue.' +
        '</div>' +
        '<div class="p-2 px-3 rounded mb-2" style="background:var(--dossier-table-stripe); border:1px solid var(--dossier-card-border); font-size:0.8rem;">' +
          '<div class="d-flex justify-content-between mb-1">' +
            '<span class="text-muted">Universal Common ID:</span>' +
            '<strong class="font-monospace text-primary">' + (commonId || '{{ $commonId }}') + '</strong>' +
          '</div>' +
          '<div class="d-flex justify-content-between mb-1">' +
            '<span class="text-muted">Mining Application No:</span>' +
            '<strong class="font-monospace text-success">' + (miningAppNo || 'Mining Plan') + '</strong>' +
          '</div>' +
          '<div class="d-flex justify-content-between">' +
            '<span class="text-muted">Verified Documents Cloned:</span>' +
            '<strong class="text-dark">' + (clonedCount || 0) + ' Files</strong>' +
          '</div>' +
        '</div>' +
        '<div class="text-muted text-center" style="font-size:0.75rem;">' +
          '<i class="bi bi-info-circle me-1"></i> You will remain on this Lease Dossier. Page will reload to update status.' +
        '</div>' +
      '</div>';

    Swal.fire({
      icon: 'success',
      title: 'Moved to Mining Plan',
      html: alertHtml,
      confirmButtonText: 'Understood',
      allowOutsideClick: false,
      customClass: {
        popup: 'gtms-gate-swal-compact'
      }
    }).then(function() {
      window.location.reload();
    });
  }
  window.triggerMiningTransitionSuccessAlert = triggerMiningTransitionSuccessAlert;

  @if(session('mining_transition_success'))
    $(function() {
      triggerMiningTransitionSuccessAlert(
        "{{ session('mining_transition_success.common_id') }}",
        "{{ session('mining_transition_success.mining_app_no') }}",
        "{{ session('mining_transition_success.cloned_count') }}"
      );
    });
  @endif

  // Intercept any click on Move to Mining buttons (toolbar or chapter actions)
  $(document).on('click', '.btn-trigger-move-mining', function(e) {
    e.preventDefault();
    e.stopPropagation();

    // Dismiss any active tooltip on this button
    var t = (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) ? bootstrap.Tooltip.getInstance(this) : null;
    if (t) t.hide();
    this.blur();

    var isApproved = {{ $isApproved ? 'true' : 'false' }};
    if (!isApproved) {
      triggerStep63ApprovalAlert();
      return false;
    }

    // If approved, check if any paused / incomplete uploads exist
    var pausedBtns = $('.btn-dossier-discard');
    if (pausedBtns.length > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Incomplete Uploads Found!',
          html: '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-warning fw-bold"><i class="bi bi-pause-circle-fill me-1"></i> ' + pausedBtns.length + ' Document(s) Have Paused / Incomplete Uploads:</p><p class="text-muted small mb-0">Please complete the upload using <strong>Resume</strong> or click the <strong>Trash (Delete)</strong> button to discard the paused file before proceeding.</p></div>',
          confirmButtonColor: '#0F1E4D',
          confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> OK, I Will Complete or Discard'
        });
      } else {
        alert('Please complete paused upload(s) or discard them before proceeding.');
      }
      return false;
    }

    // Open Move to Mining Modal
    var modalEl = document.getElementById('modalMoveToMining');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    }
  });

  // Guard modal open event directly
  $('#modalMoveToMining').on('show.bs.modal', function(e) {
    var isApproved = {{ $isApproved ? 'true' : 'false' }};
    if (!isApproved) {
      e.preventDefault();
      triggerStep63ApprovalAlert();
      return false;
    }
  });

  // Handle Move to Mining submission via AJAX
  $(document).on('submit', '#formMoveToMining', function(e) {
    e.preventDefault();

    var isApproved = {{ $isApproved ? 'true' : 'false' }};
    if (!isApproved) {
      var modalEl = document.getElementById('modalMoveToMining');
      if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
      }
      triggerStep63ApprovalAlert();
      return false;
    }

    // Check paused uploads
    var pausedBtns = $('.btn-dossier-discard');
    if (pausedBtns.length > 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Incomplete Uploads Found!',
          html: '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-warning fw-bold"><i class="bi bi-pause-circle-fill me-1"></i> ' + pausedBtns.length + ' Document(s) Have Paused / Incomplete Uploads:</p><p class="text-muted small mb-0">Please complete the upload using <strong>Resume</strong> or click the <strong>Trash (Delete)</strong> button to discard the paused file before proceeding.</p></div>',
          confirmButtonColor: '#0F1E4D',
          confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> OK, I Will Complete or Discard'
        });
      } else {
        alert('Please complete paused upload(s) or discard them before proceeding.');
      }
      return false;
    }

    var $btn = $('#btnSubmitMoveMining');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Transitioning...');

    var formAction = $(this).attr('action');
    var formData = $(this).serialize();

    $.ajax({
      url: formAction,
      type: 'POST',
      data: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      success: function(resp) {
        $btn.prop('disabled', false).html(origHtml);
        var modalEl = document.getElementById('modalMoveToMining');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        }

        triggerMiningTransitionSuccessAlert(
          resp.common_id,
          resp.mining_app_no,
          resp.cloned_count
        );
      },
      error: function(xhr) {
        $btn.prop('disabled', false).html(origHtml);
        var modalEl = document.getElementById('modalMoveToMining');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
        }

        var errorMsg = 'Failed to transition application to Mining Plan.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }

        if (xhr.status === 422) {
          triggerStep63ApprovalAlert();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Action Failed',
            text: errorMsg,
            confirmButtonColor: '#0F1E4D',
            customClass: {
              popup: 'gtms-gate-swal-compact'
            }
          });
        }
      }
    });
  });

  // Incomplete / Paused Upload Interceptor on Status Actions
  $(document).on('click', '#btn_approve_action, #btn_reject_action', function(e) {
    var pausedBtns = $('.btn-dossier-discard');
    if (pausedBtns.length > 0) {
      e.preventDefault();
      e.stopPropagation();

      var pausedHtml = '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-warning fw-bold"><i class="bi bi-pause-circle-fill me-1"></i> ' + pausedBtns.length + ' Document(s) Have Paused / Incomplete Uploads:</p><p class="text-muted small mb-0">Please complete the upload using <strong>Resume</strong> or click the <strong>Trash (Delete)</strong> button to discard the paused file before proceeding.</p></div>';

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Incomplete Uploads Found!',
          html: pausedHtml,
          confirmButtonColor: '#0F1E4D',
          confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> OK, I Will Complete or Discard'
        });
      } else {
        alert('Please complete paused upload(s) or discard them before proceeding.');
      }
    }
  });

  function updateDocRowUI(docId, newStatus, note) {
    var badgeCol = $('#doc-badge-col-' + docId);
    var actionsCol = $('#doc-actions-col-' + docId);
    var noteEl = $('#doc-note-' + docId);

    if (newStatus === 'validated') {
      badgeCol.html('<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check-circle-fill me-1"></i> Validated</span>');
      var viewBtn = actionsCol.find('.btn-view').prop('outerHTML') || '';
      var replaceBtn = actionsCol.find('.btn-replace').prop('outerHTML') || '';
      actionsCol.find('.d-inline-flex').html(
        viewBtn + replaceBtn +
        '<button type="button" class="btn-action-icon btn-undo btn-doc-action" data-doc-id="' + docId + '" data-status="uploaded" data-bs-toggle="tooltip" title="Undo Validation"><i class="bi bi-arrow-counterclockwise"></i></button>'
      );
      initTooltips();
    } else if (newStatus === 'revision_required') {
      badgeCol.html('<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle-fill me-1"></i> Revision Needed</span>');
      var viewBtn = actionsCol.find('.btn-view').prop('outerHTML') || '';
      var replaceBtn = actionsCol.find('.btn-replace').prop('outerHTML') || '';
      actionsCol.find('.d-inline-flex').html(
        viewBtn + replaceBtn +
        '<button type="button" class="btn-action-icon btn-valid btn-doc-action" data-doc-id="' + docId + '" data-status="validated" data-bs-toggle="tooltip" title="Mark as Valid"><i class="bi bi-check2"></i></button>'
      );
      if (note) {
        noteEl.removeClass('d-none').html('<i class="bi bi-chat-left-dots-fill me-1"></i> Note: ' + $('<div>').text(note).html());
      }
      initTooltips();
    } else if (newStatus === 'uploaded') {
      badgeCol.html('<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="bi bi-clock me-1"></i> Uploaded</span>');
      var viewBtn = actionsCol.find('.btn-view').prop('outerHTML') || '';
      var replaceBtn = actionsCol.find('.btn-replace').prop('outerHTML') || '';
      var docName = $('#doc-row-' + docId).find('.doc-name-text').text().trim();
      actionsCol.find('.d-inline-flex').html(
        viewBtn + replaceBtn +
        '<button type="button" class="btn-action-icon btn-valid btn-doc-action" data-doc-id="' + docId + '" data-status="validated" data-bs-toggle="tooltip" title="Mark as Valid"><i class="bi bi-check2"></i></button>' +
        '<button type="button" class="btn-action-icon btn-flag btn-flag-doc-modal" data-doc-id="' + docId + '" data-doc-name="' + $('<div>').text(docName).html() + '" data-bs-toggle="tooltip" title="Flag for Revision"><i class="bi bi-flag"></i></button>'
      );
      initTooltips();
    }
  }
});
</script>
@endsection
