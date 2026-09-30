@extends('layouts.app')

@section('title', 'Quotations Management • GTMS')

@section('main_content')
@push('styles')
    @include('pages.accounts.partials.theme')
@endpush

<style>
    /* ─── UI-UX-PRO-MAX: FinTech Enterprise High-Precision Spacing & Alignment ─── */

    /* Page Header & CTAs */
    .page-titles {
        margin-bottom: 1.25rem !important;
    }
    .header-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #EFF6FF;
        color: #1E3A8A;
        border: 1.5px solid #BFDBFE;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .btn-create-quote {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%) !important;
        border: none !important;
        color: #FFFFFF !important;
        font-weight: 700;
        font-size: 0.86rem;
        padding: 8px 18px;
        border-radius: 8px;
        height: 38px;
        box-shadow: 0 4px 14px rgba(15, 30, 77, 0.22);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-create-quote:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(15, 30, 77, 0.32);
        color: #FFFFFF !important;
    }

    /* Bento-Style KPI Metric Cards (Uniform Height & Spacing) */
    .card-kpi-pro {
        border-radius: 12px;
        border: 1.5px solid #CBD5E1;
        background: #FFFFFF;
        padding: 16px 18px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .card-kpi-pro:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.1);
        border-color: #94A3B8;
    }
    .card-kpi-pro::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .kpi-border-blue::before { background: linear-gradient(90deg, #1D4ED8, #3B82F6); }
    .kpi-border-purple::before { background: linear-gradient(90deg, #6D28D9, #8B5CF6); }
    .kpi-border-emerald::before { background: linear-gradient(90deg, #047857, #10B981); }
    .kpi-border-amber::before { background: linear-gradient(90deg, #B45309, #F59E0B); }

    .kpi-eyebrow {
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        font-weight: 800;
        color: #1E293B;
        margin-bottom: 2px;
        text-transform: uppercase;
        line-height: 1.2;
    }
    .kpi-value {
        font-size: 1.55rem;
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: -0.02em;
        margin-bottom: 0;
    }
    .kpi-subtext {
        font-size: 0.78rem;
        font-weight: 600;
        color: #334155;
        margin-top: 4px;
        line-height: 1.2;
    }
    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bg-blue-subtle { background-color: #EFF6FF; border: 1px solid #BFDBFE; }
    .bg-purple-subtle { background-color: #F5F3FF; border: 1px solid #DDD6FE; }
    .bg-emerald-subtle { background-color: #ECFDF5; border: 1px solid #A7F3D0; }
    .bg-amber-subtle { background-color: #FFFBEB; border: 1px solid #FDE68A; }
    .text-purple { color: #6D28D9 !important; }
    .text-emerald { color: #047857 !important; }
    .text-amber { color: #B45309 !important; }

    /* Quick Status Segmented Filter Tabs */
    .quotation-status-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        margin-bottom: 1.1rem !important;
    }
    .status-tab-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 14px;
        border-radius: 9999px;
        background-color: #FFFFFF;
        border: 1.5px solid #CBD5E1;
        color: #0F172A;
        font-size: 0.82rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        line-height: 1.2;
    }
    .status-tab-pill:hover {
        background-color: #F8FAFC;
        border-color: #94A3B8;
        color: #0F172A;
        transform: translateY(-1px);
    }
    .status-tab-pill.active {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
        border-color: #0F1E4D;
        color: #FFFFFF;
        box-shadow: 0 3px 10px rgba(15, 30, 77, 0.22);
    }
    .status-tab-pill .status-tab-count {
        padding: 2px 7px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 800;
        background-color: #E2E8F0;
        color: #0F172A;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }
    .status-tab-pill.active .status-tab-count {
        background-color: rgba(255, 255, 255, 0.28);
        color: #FFFFFF;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .dot-draft { background-color: #64748B; }
    .dot-sent { background-color: #0284C7; }
    .dot-accepted { background-color: #10B981; }
    .dot-converted { background-color: #8B5CF6; }
    .dot-rejected { background-color: #EF4444; }

    /* Filter Form Container & Laser-Aligned Controls */
    .filter-card-pro {
        border-radius: 12px;
        border: 1.5px solid #CBD5E1;
        background: #FFFFFF;
        box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.05);
        margin-bottom: 1.25rem !important;
    }
    .filter-card-pro .form-label {
        color: #0F172A !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        margin-bottom: 4px !important;
        display: block;
    }
    .filter-card-pro .form-control,
    .filter-card-pro .form-select {
        border: 1.5px solid #CBD5E1;
        color: #0F172A;
        font-weight: 600;
        height: 38px !important;
        font-size: 0.86rem;
        border-radius: 7px;
    }
    .filter-card-pro .form-control:focus,
    .filter-card-pro .form-select:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
    }
    .filter-btn-submit {
        height: 38px !important;
        font-weight: 700;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
    }
    .filter-btn-reset {
        height: 38px !important;
        width: 38px !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        border: 1.5px solid #CBD5E1;
        background-color: #FFFFFF;
        color: #0F172A;
        flex-shrink: 0;
    }
    .filter-btn-reset:hover {
        background-color: #F1F5F9;
        border-color: #94A3B8;
        color: #0F172A;
    }

    /* Select2 Alignment Fix (Strict 38px Height & Centering) */
    .filter-card-pro .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid #CBD5E1 !important;
        border-radius: 7px !important;
        display: flex !important;
        align-items: center !important;
        padding: 0 10px !important;
    }
    .filter-card-pro .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        color: #0F172A !important;
        font-weight: 600 !important;
        font-size: 0.86rem !important;
        padding-left: 0 !important;
    }
    .filter-card-pro .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }

    /* Table & Container Styles (Pixel-Perfect Fixed Layout) */
    .table-card-pro {
        border-radius: 12px;
        border: 1.5px solid #CBD5E1;
        background: #FFFFFF;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .table-quotations {
        table-layout: fixed;
        width: 100%;
        min-width: 1060px;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .table-quotations thead th {
        background-color: #F1F5F9 !important;
        color: #0F172A !important;
        font-weight: 800 !important;
        font-size: 0.78rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-bottom: 2px solid #CBD5E1 !important;
        padding: 11px 12px !important;
        vertical-align: middle !important;
        white-space: nowrap;
        line-height: 1.3;
    }
    .table-quotations tbody td {
        padding: 11px 12px !important;
        vertical-align: middle !important;
        font-size: 0.86rem !important;
        border-bottom: 1px solid #E2E8F0 !important;
        line-height: 1.35;
    }
    .table-quotations tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-quotations tbody tr:hover {
        background-color: #F8FAFC !important;
    }

    /* Quotation Number & Copy Button */
    .quote-code-container {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        line-height: 1;
    }
    .quotation-code-link {
        font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, monospace;
        font-size: 0.9rem;
        font-weight: 800;
        color: #0F1E4D;
        text-decoration: none;
        transition: color 0.15s ease;
        line-height: 1;
    }
    .quotation-code-link:hover {
        color: #2563EB;
        text-decoration: underline;
    }
    .btn-copy-code {
        background: transparent;
        border: none;
        padding: 0;
        width: 20px;
        height: 20px;
        font-size: 0.75rem;
        color: #64748B;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .btn-copy-code:hover {
        color: #0F1E4D;
        background-color: #E2E8F0;
    }

    /* High-Contrast Semantic Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.76rem;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }
    .status-dot-indicator {
        width: 6.5px;
        height: 6.5px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .status-pill-draft {
        background-color: #F1F5F9;
        color: #0F172A;
        border: 1.5px solid #64748B;
    }
    .status-pill-sent {
        background-color: #E0F2FE;
        color: #0369A1;
        border: 1.5px solid #0284C7;
    }
    .status-pill-accepted {
        background-color: #DCFCE7;
        color: #15803D;
        border: 1.5px solid #16A34A;
    }
    .status-pill-converted {
        background-color: #EDE9FE;
        color: #6D28D9;
        border: 1.5px solid #7C3AED;
    }
    .status-pill-rejected {
        background-color: #FEE2E2;
        color: #B91C1C;
        border: 1.5px solid #DC2626;
    }

    /* Semantic Action Buttons (30px Size, Strict Centering) */
    .action-btn-group {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        justify-content: center;
        margin: 0 auto;
    }
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 7px;
        font-size: 0.84rem;
        transition: all 0.16s ease;
        border: 1.5px solid transparent;
        text-decoration: none;
        cursor: pointer;
        padding: 0;
        line-height: 1;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.12);
    }
    .btn-action-view {
        background-color: #EFF6FF;
        color: #1D4ED8;
        border-color: #93C5FD;
    }
    .btn-action-view:hover {
        background-color: #1D4ED8;
        color: #FFFFFF;
        border-color: #1D4ED8;
    }
    .btn-action-print {
        background-color: #F5F3FF;
        color: #6D28D9;
        border-color: #C4B5FD;
    }
    .btn-action-print:hover {
        background-color: #6D28D9;
        color: #FFFFFF;
        border-color: #6D28D9;
    }
    .btn-action-edit {
        background-color: #FFFBEB;
        color: #B45309;
        border-color: #FCD34D;
    }
    .btn-action-edit:hover {
        background-color: #B45309;
        color: #FFFFFF;
        border-color: #B45309;
    }
    .btn-action-delete {
        background-color: #FEF2F2;
        color: #B91C1C;
        border-color: #FCA5A5;
    }
    .btn-action-delete:hover {
        background-color: #DC2626;
        color: #FFFFFF;
        border-color: #DC2626;
    }

    /* Micro-Chips */
    .chip-micro {
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        line-height: 1.2;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <!-- Page Title & Actions Header -->
        <div class="row page-titles mb-4 align-items-center">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="mb-0 fw-bold" style="color: #0F172A;">
                            <i class="fas fa-file-invoice-dollar text-primary me-2"></i>Quotations Management
                        </h4>
                        <span class="header-badge-pill">
                            <i class="fas fa-shield-alt"></i> Official Proposals
                        </span>
                    </div>
                    <p class="mb-0" style="color: #334155; font-size: 0.86rem; font-weight: 500;">
                        Prepare, track, and generate statutory mining consultancy proposals & printable A4 schedules.
                    </p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0 me-3 d-none d-sm-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: #334155; font-weight: 600;">Home</a></li>
                    <li class="breadcrumb-item" style="color: #334155; font-weight: 600;">Accounts</li>
                    <li class="breadcrumb-item active text-primary fw-bold">Quotations</li>
                </ol>
                @can('account.create')
                <a href="{{ route('accounts.quotations.create') }}" class="btn btn-create-quote">
                    <i class="fas fa-plus"></i> Create Quotation
                </a>
                @endcan
            </div>
        </div>

        <!-- Feedback Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Bento-Style KPI Metric Cards Grid -->
        <div class="row g-3 mb-4">
            <!-- Card 1: Total Quotations -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-blue">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Total Quotations</div>
                            <h3 class="kpi-value mb-0" style="color: #0F1E4D;">{{ number_format($stats['total_count']) }}</h3>
                            <div class="kpi-subtext">
                                <i class="fas fa-layer-group me-1 text-primary"></i>All-time issued proposals
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-blue-subtle text-primary">
                            <i class="fas fa-file-invoice fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Pipeline Value -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-purple">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Total Pipeline Value</div>
                            <h3 class="kpi-value mb-0 financial-number" style="color: #4C1D95;">₹ {{ number_format($stats['total_amount'], 2) }}</h3>
                            <div class="kpi-subtext">
                                <i class="fas fa-coins me-1 text-purple"></i>Gross quoted with tax
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-purple-subtle text-purple">
                            <i class="fas fa-calculator fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Accepted / Converted -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-emerald">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Accepted / Converted</div>
                            <h3 class="kpi-value text-emerald mb-0 financial-number">{{ number_format($stats['accepted_count']) }}</h3>
                            <div class="kpi-subtext text-emerald">
                                <i class="fas fa-check-circle me-1"></i>₹ {{ number_format($stats['accepted_amount'], 2) }}
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-emerald-subtle text-emerald">
                            <i class="fas fa-handshake fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Draft / In Scrutiny -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-amber">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Draft / In Scrutiny</div>
                            <h3 class="kpi-value text-amber mb-0 financial-number">{{ number_format($stats['pending_count']) }}</h3>
                            <div class="kpi-subtext text-amber">
                                <i class="fas fa-hourglass-half me-1"></i>₹ {{ number_format($stats['pending_amount'], 2) }}
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-amber-subtle text-amber">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Status Segmented Filter Tabs -->
        <div class="quotation-status-tabs">
            <a href="{{ route('accounts.quotations.index', request()->except(['status', 'page'])) }}"
               class="status-tab-pill {{ !request()->filled('status') ? 'active' : '' }}">
                <span>All Proposals</span>
                <span class="status-tab-count">{{ number_format($stats['total_count']) }}</span>
            </a>
            <a href="{{ route('accounts.quotations.index', array_merge(request()->except(['page']), ['status' => 'draft'])) }}"
               class="status-tab-pill {{ request('status') === 'draft' ? 'active' : '' }}">
                <span class="status-dot dot-draft"></span>
                <span>Draft</span>
                <span class="status-tab-count">{{ number_format($statusCounts['draft'] ?? 0) }}</span>
            </a>
            <a href="{{ route('accounts.quotations.index', array_merge(request()->except(['page']), ['status' => 'sent'])) }}"
               class="status-tab-pill {{ request('status') === 'sent' ? 'active' : '' }}">
                <span class="status-dot dot-sent"></span>
                <span>Sent</span>
                <span class="status-tab-count">{{ number_format($statusCounts['sent'] ?? 0) }}</span>
            </a>
            <a href="{{ route('accounts.quotations.index', array_merge(request()->except(['page']), ['status' => 'accepted'])) }}"
               class="status-tab-pill {{ request('status') === 'accepted' ? 'active' : '' }}">
                <span class="status-dot dot-accepted"></span>
                <span>Accepted</span>
                <span class="status-tab-count">{{ number_format($statusCounts['accepted'] ?? 0) }}</span>
            </a>
            <a href="{{ route('accounts.quotations.index', array_merge(request()->except(['page']), ['status' => 'converted'])) }}"
               class="status-tab-pill {{ request('status') === 'converted' ? 'active' : '' }}">
                <span class="status-dot dot-converted"></span>
                <span>Converted</span>
                <span class="status-tab-count">{{ number_format($statusCounts['converted'] ?? 0) }}</span>
            </a>
            <a href="{{ route('accounts.quotations.index', array_merge(request()->except(['page']), ['status' => 'rejected'])) }}"
               class="status-tab-pill {{ request('status') === 'rejected' ? 'active' : '' }}">
                <span class="status-dot dot-rejected"></span>
                <span>Rejected</span>
                <span class="status-tab-count">{{ number_format($statusCounts['rejected'] ?? 0) }}</span>
            </a>
        </div>

        <!-- Filter & Search Command Bar -->
        <div class="card filter-card-pro">
            <div class="card-body p-3">
                <div class="d-md-none mb-2">
                    <button class="btn btn-sm btn-outline-secondary w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#quotationFilterCollapse" aria-expanded="false" aria-controls="quotationFilterCollapse">
                        <span class="fw-bold" style="color: #0F172A;"><i class="fas fa-filter me-1 text-primary"></i> Advanced Filter Options</span>
                        <i class="fas fa-chevron-down text-dark"></i>
                    </button>
                </div>
                <div class="collapse d-md-block" id="quotationFilterCollapse">
                    <form action="{{ route('accounts.quotations.index') }}" method="GET" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="fas fa-search me-1 text-primary"></i>Search Keyword
                            </label>
                            <input type="text" name="search" class="form-control" placeholder="Quotation no, client, quarry, phone..." value="{{ request('search', request('q')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="fas fa-building me-1 text-primary"></i>Client / Entity
                            </label>
                            <select name="customer_id" id="filter_customer_id" class="form-select select2-customer">
                                <option value="">-- All Clients --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }} {{ $c->company_name ? "({$c->company_name})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="fas fa-tag me-1 text-primary"></i>Status
                            </label>
                            <select name="status" class="form-select">
                                <option value="">-- All Statuses --</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent to Client</option>
                                <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>Accepted</option>
                                <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>Converted to Job</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected / Void</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="far fa-calendar-alt me-1 text-primary"></i>From Date
                            </label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from', request('start_date')) }}">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-navy flex-fill filter-btn-submit">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('accounts.quotations.index') }}" class="filter-btn-reset" title="Reset All Filters" data-bs-toggle="tooltip">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Active Filter Chips (P0.5) -->
        @include('pages.accounts.partials.filter_chips', ['route' => 'accounts.quotations.index', 'customers' => $customers])

        <!-- Quotations Table Card -->
        <div class="card table-card-pro">
            <div class="card-header bg-white py-3 px-3 px-md-4 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold" style="color: #0F172A; font-size: 1rem;">Issued Quotations Directory</h5>
                        <small style="color: #334155; font-weight: 500;">Multi-service quarry consultancy estimates & official fee proposals</small>
                    </div>
                </div>
                <span class="badge" style="background-color: #F1F5F9; color: #0F172A; border: 1.5px solid #94A3B8; font-weight: 800; font-size: 0.78rem; padding: 6px 12px;">
                    Showing {{ $quotations->firstItem() ?? 0 }} to {{ $quotations->lastItem() ?? 0 }} of {{ $quotations->total() }} Records
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-sticky table-quotations mb-0">
                        <thead>
                            <tr>
                                <th style="width: 48px; text-align: center;">#</th>
                                <th style="width: 195px; text-align: left;">Quotation No & Date</th>
                                <th style="width: 245px; text-align: left;">Client & Entity</th>
                                <th style="width: 235px; text-align: left;">Quarry Location / Extent</th>
                                <th style="width: 165px; text-align: right;">Total Value (₹)</th>
                                <th style="width: 122px; text-align: center;">Status</th>
                                <th style="width: 150px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotations as $quotation)
                            <tr>
                                <td style="text-align: center; font-weight: 700; color: #475569;">
                                    {{ $loop->iteration + ($quotations->currentPage() - 1) * $quotations->perPage() }}
                                </td>
                                <td>
                                    <div class="quote-code-container">
                                        <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="quotation-code-link">
                                            {{ $quotation->quotation_number }}
                                        </a>
                                        <button type="button" class="btn-copy-code" onclick="copyQuotationCode('{{ $quotation->quotation_number }}', this)" title="Copy Number" data-bs-toggle="tooltip">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="d-flex align-items-center gap-1" style="color: #1E293B; font-size: 0.8rem; font-weight: 600; margin-top: 3px;">
                                        <i class="far fa-calendar-alt text-primary"></i>
                                        <span>{{ $quotation->created_at->format('d-M-Y') }}</span>
                                        <span class="badge ms-1" style="background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-size: 0.7rem; font-weight: 700; padding: 2px 6px;" title="Validity Period">
                                            {{ $quotation->validity_days }}d valid
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold" style="font-size: 0.92rem; color: #0F172A; line-height: 1.25;">
                                        {{ $quotation->company_name ?: $quotation->customer_name }}
                                    </div>
                                    @if($quotation->company_name && $quotation->customer_name && $quotation->company_name !== $quotation->customer_name)
                                        <div style="color: #475569; font-size: 0.8rem; font-weight: 600; margin-top: 2px;">Attn: {{ $quotation->customer_name }}</div>
                                    @endif
                                    <div class="d-flex flex-wrap align-items-center gap-2" style="margin-top: 3px;">
                                        @if($quotation->phone)
                                            <a href="tel:{{ $quotation->phone }}" class="text-decoration-none" style="color: #1E3A8A; font-weight: 700; font-size: 0.82rem;" title="Call Client">
                                                <i class="fas fa-phone-alt me-1 text-primary"></i>{{ $quotation->phone }}
                                            </a>
                                        @endif
                                        @if($quotation->gst_number)
                                            <span class="chip-micro" style="background-color: #F8FAFC; color: #0F172A; border: 1px solid #CBD5E1; font-weight: 700;">GST: {{ $quotation->gst_number }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #0F172A; font-size: 0.88rem; line-height: 1.25;">
                                        {{ $quotation->quarry_name ?: ($quotation->village ? $quotation->village . ' Quarry' : 'Quarry Concession') }}
                                    </div>
                                    <div class="d-flex align-items-center gap-1" style="color: #334155; font-size: 0.8rem; font-weight: 600; margin-top: 2px;">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        <span>
                                            @if($quotation->survey_numbers) S.F. {{ $quotation->survey_numbers }} &bull; @endif
                                            @if($quotation->village) {{ $quotation->village }}, @endif
                                            {{ $quotation->district?->name ?? 'Tamil Nadu' }}
                                        </span>
                                    </div>
                                    @if($quotation->area_extent_ha || $quotation->mineral_name)
                                    <div class="d-flex align-items-center gap-1" style="margin-top: 3px;">
                                        @if($quotation->area_extent_ha)
                                            <span class="chip-micro" style="background-color: #F8FAFC; color: #0F172A; border: 1px solid #CBD5E1; font-weight: 700;">
                                                <i class="fas fa-vector-square text-primary me-1"></i>{{ number_format($quotation->area_extent_ha, 4) }} Ha
                                            </span>
                                        @endif
                                        @if($quotation->mineral_name)
                                            <span class="chip-micro" style="background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; font-weight: 700;">
                                                {{ $quotation->mineral_name }}
                                            </span>
                                        @endif
                                    </div>
                                    @endif
                                </td>
                                <td style="text-align: right;" class="financial-number">
                                    <div class="fw-bolder financial-number" style="font-size: 1.02rem; color: #0F1E4D; line-height: 1.2;">
                                        ₹ {{ number_format($quotation->total_amount, 2) }}
                                    </div>
                                    <div class="financial-number" style="color: #334155; font-size: 0.8rem; font-weight: 700; margin-top: 2px;">
                                        Sub: ₹ {{ number_format($quotation->subtotal, 2) }}
                                        @if($quotation->tax_amount > 0)
                                            <span style="color: #1E40AF; font-weight: 800;">(+18% GST)</span>
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-end" style="margin-top: 3px;">
                                        <span class="chip-micro" style="background-color: #F8FAFC; color: #0F172A; border: 1px solid #CBD5E1; font-weight: 700;">
                                            {{ $quotation->items->count() }} {{ \Illuminate\Support\Str::plural('Service', $quotation->items->count()) }}
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    @php
                                        $badgeConfig = match($quotation->status) {
                                            'sent' => ['class' => 'status-pill-sent', 'label' => 'Sent to Client', 'dot' => '#0284C7'],
                                            'accepted' => ['class' => 'status-pill-accepted', 'label' => 'Accepted', 'dot' => '#16A34A'],
                                            'converted' => ['class' => 'status-pill-converted', 'label' => 'Converted', 'dot' => '#7C3AED'],
                                            'rejected' => ['class' => 'status-pill-rejected', 'label' => 'Rejected', 'dot' => '#DC2626'],
                                            default => ['class' => 'status-pill-draft', 'label' => 'Draft', 'dot' => '#475569'],
                                        };
                                    @endphp
                                    <span class="status-pill {{ $badgeConfig['class'] }}">
                                        <span class="status-dot-indicator" style="background-color: {{ $badgeConfig['dot'] }};"></span>
                                        {{ $badgeConfig['label'] }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <div class="action-btn-group">
                                        <a href="{{ route('accounts.quotations.show', $quotation->id) }}"
                                           class="btn-action btn-action-view"
                                           title="View Details"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('accounts.quotations.print', $quotation->id) }}"
                                           target="_blank"
                                           class="btn-action btn-action-print"
                                           title="Print Standalone A4"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @can('account.edit')
                                        <a href="{{ route('accounts.quotations.edit', $quotation->id) }}"
                                           class="btn-action btn-action-edit"
                                           title="Edit Quotation"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        @endcan
                                        @can('account.delete')
                                        <button type="button"
                                                class="btn-action btn-action-delete"
                                                title="Delete Quotation"
                                                data-bs-toggle="tooltip"
                                                onclick="confirmDeleteQuotation('{{ $quotation->id }}', '{{ $quotation->quotation_number }}')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <form id="delete-form-{{ $quotation->id }}" action="{{ route('accounts.quotations.destroy', $quotation->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="p-0">
                                    @include('pages.accounts.partials.empty_state', [
                                        'icon' => 'fas fa-file-invoice',
                                        'title' => 'No Quotations Found',
                                        'desc' => 'There are no quotation records matching your search or filter parameters.',
                                        'resetRoute' => route('accounts.quotations.index'),
                                        'createRoute' => route('accounts.quotations.create'),
                                        'createLabel' => 'Generate First Quotation',
                                        'createPermission' => 'account.create'
                                    ])
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Responsive Mobile Card View (< 768px) --}}
                <div class="d-md-none p-3">
                    @forelse($quotations as $quotation)
                        <div class="account-mobile-card" style="border: 1.5px solid #CBD5E1;">
                            <div class="account-mobile-card-header">
                                <div>
                                    <div class="quote-code-container">
                                        <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="quotation-code-link">
                                            {{ $quotation->quotation_number }}
                                        </a>
                                        <button type="button" class="btn-copy-code" onclick="copyQuotationCode('{{ $quotation->quotation_number }}', this)">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                    <div style="color: #1E293B; font-size: 0.78rem; font-weight: 600; margin-top: 3px;">
                                        <i class="far fa-calendar-alt text-primary me-1"></i>{{ $quotation->created_at->format('d-M-Y') }} &bull; {{ $quotation->validity_days }}d valid
                                    </div>
                                </div>
                                @php
                                    $badgeConfig = match($quotation->status) {
                                        'sent' => ['class' => 'status-pill-sent', 'label' => 'Sent', 'dot' => '#0284C7'],
                                        'accepted' => ['class' => 'status-pill-accepted', 'label' => 'Accepted', 'dot' => '#16A34A'],
                                        'converted' => ['class' => 'status-pill-converted', 'label' => 'Converted', 'dot' => '#7C3AED'],
                                        'rejected' => ['class' => 'status-pill-rejected', 'label' => 'Rejected', 'dot' => '#DC2626'],
                                        default => ['class' => 'status-pill-draft', 'label' => 'Draft', 'dot' => '#475569'],
                                    };
                                @endphp
                                <span class="status-pill {{ $badgeConfig['class'] }}">
                                    <span class="status-dot-indicator" style="background-color: {{ $badgeConfig['dot'] }};"></span>
                                    {{ $badgeConfig['label'] }}
                                </span>
                            </div>
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label" style="color: #334155; font-weight: 700;">Client / Firm:</span>
                                <span class="account-mobile-card-value text-truncate fw-bold" style="max-width:210px; color: #0F172A;">{{ $quotation->company_name ?: $quotation->customer_name }}</span>
                            </div>
                            @if($quotation->quarry_name || $quotation->village)
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label" style="color: #334155; font-weight: 700;">Location:</span>
                                <span class="account-mobile-card-value text-truncate" style="max-width:210px; color: #1E293B; font-weight: 600;">{{ $quotation->quarry_name ?: $quotation->village }}</span>
                            </div>
                            @endif
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label" style="color: #334155; font-weight: 700;">Total Value:</span>
                                <span class="account-mobile-card-value financial-number fs-6 fw-bolder" style="color: #0F1E4D;">₹ {{ number_format($quotation->total_amount, 2) }}</span>
                            </div>
                            <div class="account-mobile-card-actions">
                                <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="btn-action btn-action-view" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('accounts.quotations.print', $quotation->id) }}" target="_blank" class="btn-action btn-action-print" title="Print">
                                    <i class="fas fa-print"></i>
                                </a>
                                @can('account.edit')
                                <a href="{{ route('accounts.quotations.edit', $quotation->id) }}" class="btn-action btn-action-edit" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                @endcan
                                @can('account.delete')
                                <button type="button" class="btn-action btn-action-delete" title="Delete" onclick="confirmDeleteQuotation('{{ $quotation->id }}', '{{ $quotation->quotation_number }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endcan
                            </div>
                        </div>
                    @empty
                        @include('pages.accounts.partials.empty_state', [
                            'icon' => 'fas fa-file-invoice',
                            'title' => 'No Quotations Found',
                            'desc' => 'There are no quotation records matching your search or filter parameters.',
                            'resetRoute' => route('accounts.quotations.index'),
                            'createRoute' => route('accounts.quotations.create'),
                            'createLabel' => 'Generate First Quotation',
                            'createPermission' => 'account.create'
                        ])
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($quotations->hasPages())
                <div class="p-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2" style="background-color: #FAFAFA;">
                    <div class="small fw-bold" style="color: #0F172A;">
                        Showing page {{ $quotations->currentPage() }} of {{ $quotations->lastPage() }} ({{ $quotations->total() }} total entries)
                    </div>
                    <div>
                        {{ $quotations->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2 on Client Dropdown
        if (window.jQuery && jQuery.fn.select2 && $('#filter_customer_id').length) {
            $('#filter_customer_id').select2({
                placeholder: '-- All Clients --',
                allowClear: true,
                width: '100%'
            });
        }

        // Initialize Bootstrap 5 Tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (el) {
                return new bootstrap.Tooltip(el);
            });
        }
    });

    // Copy Quotation Number to Clipboard with visual feedback
    function copyQuotationCode(code, btn) {
        if (!navigator.clipboard) {
            var tempInput = document.createElement("input");
            tempInput.value = code;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            showCopiedFeedback(btn);
            return;
        }
        navigator.clipboard.writeText(code).then(function () {
            showCopiedFeedback(btn);
        });
    }

    function showCopiedFeedback(btn) {
        var originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        if (typeof toastr !== 'undefined') {
            toastr.options = { timeOut: 1800, positionClass: 'toast-bottom-right' };
            toastr.success('Quotation number copied to clipboard!');
        }
        setTimeout(function () {
            btn.innerHTML = originalHtml;
        }, 1800);
    }

    // SweetAlert2 Confirmation Dialog for Deletion
    function confirmDeleteQuotation(id, quoteNo) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete Quotation?',
                text: 'Are you sure you want to remove quotation ' + quoteNo + '? This will also soft-delete its line items.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC2626',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        } else {
            if (confirm('Are you sure you want to delete quotation ' + quoteNo + '?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    }
</script>
@endpush
@endsection
