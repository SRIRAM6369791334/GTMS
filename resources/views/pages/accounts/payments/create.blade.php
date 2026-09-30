@extends('layouts.app')

@section('title', 'Payment Collection Desk - GTMS Accounts')

@push('styles')
{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    /* ─── UI-UX-PRO-MAX HIGH CONTRAST TOKENS ───────────────────── */
    :root {
        --color-navy: #0F1E4D;
        --color-navy-light: #1E3A8A;
        --color-navy-dark: #0A1435;
        --color-slate-dark: #0F172A;
        --color-slate-body: #1E293B;
        --color-slate-muted: #475569;
        --color-emerald: #059669;
        --color-amber: #D97706;
        --color-rose: #DC2626;
        --color-border-clean: #CBD5E1;
        --color-bg-subtle: #F8FAFC;
    }

    /* Force all text in this view to high contrast */
    .content-body {
        color: var(--color-slate-body);
    }
    .text-muted {
        color: var(--color-slate-muted) !important;
    }

    /* ─── Select2 Clean Styling (No duplicate raw select) ──────── */
    select#customer_selector {
        display: none !important;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 46px;
        border: 1.5px solid var(--color-border-clean);
        border-radius: 9px;
        padding: 5px 14px;
        font-size: 0.95rem;
        background-color: #FFFFFF;
        transition: all 0.18s ease;
        box-shadow: 0 1px 2px rgba(15,23,42,0.05);
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 34px;
        color: var(--color-slate-dark);
        font-weight: 600;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px;
        right: 12px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--color-navy-light);
        box-shadow: 0 0 0 3px rgba(30,58,138,0.18);
        outline: none;
    }
    .select2-dropdown {
        border: 1.5px solid var(--color-border-clean);
        border-radius: 9px;
        box-shadow: 0 12px 28px rgba(15,23,42,0.15);
        z-index: 1050;
    }
    .select2-results__option {
        padding: 9px 14px;
        font-size: 0.9rem;
        color: var(--color-slate-dark);
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--color-navy);
        color: #FFFFFF;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 8px 12px;
        border-radius: 7px;
        border: 1px solid var(--color-border-clean);
        outline: none;
    }

    /* ─── Customer Dossier Banner ──────────────────────────────── */
    .customer-dossier-banner {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 55%, #1E3A5F 100%) !important;
        border-radius: 11px;
        padding: 14px 18px;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(15,30,77,0.18);
        animation: bannerReveal 0.2s ease-out;
    }
    @keyframes bannerReveal {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .customer-dossier-banner * {
        color: #FFFFFF;
    }
    .customer-dossier-banner .btn-ledger {
        background: #FFFFFF !important;
        color: #0F1E4D !important;
        font-weight: 700;
        font-size: 0.76rem;
        border-radius: 6px;
        padding: 3px 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        text-decoration: none;
    }
    .customer-dossier-banner .btn-ledger:hover {
        background: #F1F5F9 !important;
    }
    .banner-meta-tag {
        display: inline-flex;
        align-items: center;
        background: rgba(255,255,255,0.14);
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 0.76rem;
        color: #E2E8F0 !important;
    }
    .banner-meta-tag * {
        color: #E2E8F0 !important;
    }
    .banner-kpi-pill {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 7px;
        padding: 6px 10px;
        text-align: center;
    }

    /* ─── Application Cards ────────────────────────────────────── */
    .app-card {
        border-radius: 9px;
        border: 1.5px solid var(--color-border-clean);
        background: #FFFFFF;
        transition: all 0.16s ease-in-out;
        cursor: pointer;
        padding: 11px 14px;
        margin-bottom: 9px;
    }
    .app-card:hover {
        border-color: #93C5FD;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(30,58,138,0.08);
    }
    .app-card.selected {
        border-color: #2563EB !important;
        background: #EFF6FF !important;
        box-shadow: 0 0 0 2px #2563EB, 0 6px 16px rgba(37,99,235,0.12);
    }
    .app-card.status-pending { border-left: 5px solid var(--color-rose); }
    .app-card.status-partial { border-left: 5px solid var(--color-amber); }
    .app-card.status-paid    { border-left: 5px solid var(--color-emerald); }

    /* Module Icon Badges */
    .module-icon-box {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .icon-box-lease      { background: #FEF3C7; color: #B45309; }
    .icon-box-mining     { background: #FFE4E6; color: #BE123C; }
    .icon-box-eviron     { background: #D1FAE5; color: #047857; }
    .icon-box-ec         { background: #DBEAFE; color: #1D4ED8; }
    .icon-box-ppt        { background: #EDE9FE; color: #6D28D9; }
    .icon-box-dgps       { background: #FCE7F3; color: #BE185D; }
    .icon-box-drone      { background: #E0F2FE; color: #0369A1; }
    .icon-box-compliance { background: #DCFCE7; color: #15803D; }
    .icon-box-general    { background: #F1F5F9; color: #475569; }

    /* ─── Dues Filter Tabs & Search ────────────────────────────── */
    .filter-tab-pill {
        appearance: none;
        -webkit-appearance: none;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid var(--color-border-clean) !important;
        background: #FFFFFF;
        color: var(--color-slate-muted);
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none !important;
        box-shadow: none !important;
    }
    .filter-tab-pill:hover {
        background: #F1F5F9;
        color: var(--color-slate-dark);
    }
    .filter-tab-pill.active {
        background: var(--color-navy) !important;
        color: #FFFFFF !important;
        border-color: var(--color-navy) !important;
    }

    #dues_cards_container {
        max-height: 380px;
        overflow-y: auto;
        padding-right: 4px;
        scrollbar-width: thin;
    }
    #dues_cards_container::-webkit-scrollbar { width: 5px; }
    #dues_cards_container::-webkit-scrollbar-thumb {
        background-color: var(--color-border-clean);
        border-radius: 4px;
    }

    /* ─── Right Form: Compact Viewport Design ──────────────────── */
    .payment-form-card {
        border-radius: 12px;
        border: 1.5px solid var(--color-border-clean);
        background: #FFFFFF;
        box-shadow: 0 4px 16px rgba(15,23,42,0.06);
    }
    @media (min-width: 992px) {
        .payment-right-sticky {
            position: sticky;
            top: 76px;
        }
    }

    /* Target Application Mini Banner */
    .target-app-mini {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A5F 100%);
        color: #FFFFFF;
        border-radius: 9px;
        padding: 10px 14px;
    }
    .target-app-mini * {
        color: #FFFFFF;
    }

    /* Amount & Calculation Box */
    .amount-calc-box {
        background: #F8FAFC;
        border: 1px solid var(--color-border-clean);
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.8rem;
    }
    .amount-calc-box .words-text {
        font-size: 0.8rem;
        color: var(--color-navy-light);
        font-weight: 600;
        font-style: italic;
    }

    /* Quick Preset Pills */
    .quick-preset-pill {
        appearance: none;
        -webkit-appearance: none;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 14px;
        border: 1px solid var(--color-border-clean) !important;
        background: #FFFFFF;
        color: var(--color-slate-body);
        cursor: pointer;
        transition: all 0.12s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        outline: none !important;
        box-shadow: none !important;
    }
    .quick-preset-pill:hover {
        background: var(--color-navy);
        color: #FFFFFF !important;
        border-color: var(--color-navy) !important;
    }

    /* Input Group Icons: Transparent clean border */
    .clean-input-group .input-group-text {
        background-color: #F8FAFC !important;
        border: 1.5px solid var(--color-border-clean) !important;
        color: var(--color-navy) !important;
        font-size: 0.88rem !important;
        padding: 0 11px !important;
    }
    .clean-input-group .form-control,
    .clean-input-group .form-select {
        border: 1.5px solid var(--color-border-clean) !important;
        font-size: 0.9rem;
    }
    .clean-input-group .form-control:focus,
    .clean-input-group .form-select:focus {
        border-color: var(--color-navy-light) !important;
        box-shadow: 0 0 0 3px rgba(30,58,138,0.15) !important;
    }

    /* Narration Suggestion Chips */
    .narration-chip {
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 10px;
        border: 1px dashed var(--color-border-clean);
        background: #FFFFFF;
        color: var(--color-slate-muted);
        cursor: pointer;
        transition: all 0.12s ease;
    }
    .narration-chip:hover {
        background: #F1F5F9;
        color: var(--color-slate-dark);
        border-color: var(--color-navy);
    }

    /* Submit Button */
    .btn-record-main {
        background: var(--color-navy);
        color: #FFFFFF;
        border: none;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 10px 16px;
        border-radius: 9px;
        transition: all 0.18s ease;
        box-shadow: 0 4px 12px rgba(15,30,77,0.22);
    }
    .btn-record-main:hover {
        background: #1A2F6C;
        color: #FFFFFF;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(15,30,77,0.3);
    }

    /* Tabular numeric alignment */
    .tabular-nums {
        font-variant-numeric: tabular-nums;
    }

    /* Bottom spacing guard */
    .content-body.default-height {
        padding-bottom: 70px;
    }
</style>
@endpush

@section('main_content')
<div class="content-body default-height">
<div class="container-fluid py-3 px-4">

    {{-- ── Top Navigation Header ───────────────────────────────────────── --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.receipts.index') }}" class="text-decoration-none text-muted">Accounts</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Payment Collection Desk</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-cash-register text-primary me-2"></i>Payment Collection Desk
            </h4>
            <div class="small" style="color: #475569;">
                Record client statutory payments, auto-synchronize dossiers &amp; generate official money receipt vouchers.
            </div>
        </div>
        <div class="mt-2 mt-sm-0 d-flex gap-2">
            <a href="{{ route('accounts.receipts.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm fw-semibold">
                <i class="fas fa-receipt me-1"></i> Receipts Directory
            </a>
            <a href="{{ route('accounts.quotations.index') }}" class="btn btn-outline-primary btn-sm shadow-sm fw-semibold">
                <i class="fas fa-file-invoice me-1"></i> Quotations
            </a>
            <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-dark btn-sm shadow-sm fw-semibold">
                <i class="fas fa-chart-line me-1"></i> Reports
            </a>
        </div>
    </div>

    {{-- ── Flash Notifications ────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Submission Failed:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Keyboard Shortcuts Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="small fw-semibold" style="color: #334155;">
            <i class="fas fa-arrow-circle-right text-primary me-1"></i> <strong>Step 1:</strong> Select a quarry operator below to view active applications and record payment.
        </div>
        <div>
            <span class="badge bg-white text-dark border px-2 py-1 shadow-sm" style="font-size:0.75rem; color:#334155 !important;">
                <kbd style="background:#0F1E4D; color:#FFFFFF; padding:2px 5px; border-radius:4px;">Ctrl</kbd> + <kbd style="background:#0F1E4D; color:#FFFFFF; padding:2px 5px; border-radius:4px;">Enter</kbd> Review &amp; Submit &nbsp;|&nbsp; <kbd style="background:#475569; color:#FFFFFF; padding:2px 5px; border-radius:4px;">Esc</kbd> Reset to General
            </span>
        </div>
    </div>

    <div class="row g-3 align-items-start">

        {{-- ══════════════════════════════════════════════════════════════════
             LEFT COLUMN — Customer Profile + Applications & Outstanding Dues
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="col-lg-7">

            {{-- 1. Client Selection Card --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:12px; border: 1.5px solid #CBD5E1;">
                <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark fs-6">
                        <i class="fas fa-user-check text-primary me-2"></i>1. Select Client / Quarry Owner
                    </h5>
                    <span class="badge bg-light text-dark border small fw-semibold" id="customer_counter_badge">
                        {{ count($customers) }} Registered Clients
                    </span>
                </div>
                <div class="card-body p-3">
                    <label class="form-label fw-bold small text-dark mb-1">
                        Search Client by Name, Company, or Mobile Number:
                    </label>
                    <select id="customer_selector" style="width: 100%;">
                        <option value="">-- Type to search client name or phone --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}"
                                data-name="{{ $c->customer_name }}"
                                data-company="{{ $c->company_name }}"
                                data-mobile="{{ $c->mobile_num }}"
                                data-gstin="{{ $c->gstin }}"
                                data-slug="{{ $c->slug }}"
                                {{ (old('customer_id', $selectedCustomerId) == $c->id) ? 'selected' : '' }}>
                                {{ $c->company_name ? $c->company_name . ' (' . $c->customer_name . ')' : $c->customer_name }}
                                @if($c->mobile_num) • {{ $c->mobile_num }} @endif
                            </option>
                        @endforeach
                    </select>

                    {{-- Customer Dossier Banner --}}
                    <div id="customer_dossier_banner" class="customer-dossier-banner mt-3" style="display:none;">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <div class="fs-6 fw-bold text-white d-flex align-items-center gap-2">
                                    <span id="banner_client_name">-</span>
                                    <span class="badge bg-success" style="font-size:0.7rem;">Verified Client</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-1.5">
                                    <span class="banner-meta-tag">
                                        <i class="fas fa-phone-alt me-1 text-info"></i>
                                        <span id="banner_mobile">-</span>
                                    </span>
                                    <span class="banner-meta-tag">
                                        <i class="fas fa-id-card me-1 text-warning"></i>
                                        <span id="banner_gstin">GST: Unregistered</span>
                                    </span>
                                </div>
                            </div>
                            <div class="text-sm-end">
                                <div class="small opacity-75">Net Outstanding Receivables</div>
                                <div class="fs-4 fw-bold tabular-nums text-white" id="banner_total_due">₹ 0.00</div>
                                <a href="#" id="link_view_ledger" target="_blank" class="btn-ledger mt-1 d-inline-block">
                                    <i class="fas fa-book-open me-1"></i> Customer Ledger →
                                </a>
                            </div>
                        </div>

                        <hr class="my-2.5 border-white opacity-25">

                        <div class="row g-2">
                            <div class="col-4">
                                <div class="banner-kpi-pill">
                                    <div class="small opacity-75" style="font-size:0.72rem;">APPLICATIONS</div>
                                    <div class="fw-bold fs-6 tabular-nums" id="banner_apps_count">0</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="banner-kpi-pill">
                                    <div class="small opacity-75" style="font-size:0.72rem;">TOTAL BILLED</div>
                                    <div class="fw-bold fs-6 tabular-nums" id="banner_total_billed">₹ 0.00</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="banner-kpi-pill">
                                    <div class="small opacity-75" style="font-size:0.72rem;">TOTAL RECEIVED</div>
                                    <div class="fw-bold fs-6 tabular-nums" style="color: #34D399 !important;" id="banner_total_received">₹ 0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Statutory Applications & Dues Section --}}
            <div class="card border-0 shadow-sm mb-3" style="border-radius:12px; border: 1.5px solid #CBD5E1; overflow:hidden;">
                <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
                    <h5 class="mb-0 fw-bold text-dark fs-6">
                        <i class="fas fa-file-invoice-dollar text-primary me-2"></i>2. Statutory Applications &amp; Outstanding Dues
                    </h5>
                    <span id="dues_badge_counter" class="badge bg-secondary fw-semibold">0 Applications</span>
                </div>

                <div class="card-body p-3">
                    {{-- Loading State --}}
                    <div id="dues_loader" class="text-center py-4" style="display:none;">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="small mt-2 fw-semibold" style="color: #475569;">Querying records across Lease, Mining, EC, PPT, DGPS, and Drone surveys…</div>
                    </div>

                    {{-- Empty State (No client chosen) --}}
                    <div id="dues_empty_state" class="text-center py-4">
                        <div class="mb-2">
                            <div class="d-inline-flex p-3 rounded-circle bg-light text-primary">
                                <i class="fas fa-hand-holding-usd fa-2x"></i>
                            </div>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Client Selected Yet</h6>
                        <p class="small mb-0 mx-auto" style="max-width: 380px; color: #475569 !important;">
                            Choose a quarry client from the search box above to load active statutory dossiers, concessions, and pending payment schedules.
                        </p>
                    </div>

                    {{-- Applications Container --}}
                    <div id="dues_main_container" style="display:none;">
                        {{-- Controls: Tab filters & Search input --}}
                        <div class="row g-2 mb-2.5 align-items-center">
                            <div class="col-md-7 d-flex gap-1 flex-wrap">
                                <button type="button" class="filter-tab-pill active" data-filter="all" id="tab_filter_all">
                                    All (<span id="count_all">0</span>)
                                </button>
                                <button type="button" class="filter-tab-pill" data-filter="pending" id="tab_filter_pending">
                                    Pending Dues (<span id="count_pending">0</span>)
                                </button>
                                <button type="button" class="filter-tab-pill" data-filter="settled" id="tab_filter_settled">
                                    Settled (<span id="count_settled">0</span>)
                                </button>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group input-group-sm clean-input-group">
                                    <span class="input-group-text bg-white text-muted"><i class="fas fa-search"></i></span>
                                    <input type="text" id="app_search_input" class="form-control" placeholder="Filter by ref, survey, village…">
                                </div>
                            </div>
                        </div>

                        {{-- Application Cards Scrollable List --}}
                        <div id="dues_cards_container"></div>

                        {{-- Pagination / Showing counter --}}
                        <div class="d-flex justify-content-between align-items-center mt-2 px-1 small fw-semibold" style="color: #475569;" id="cards_footer_info">
                            <span id="showing_apps_text">Showing applications</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" id="btn_load_all_cards" style="display:none;">
                                Load all applications →
                            </button>
                        </div>

                        {{-- Direct General Advance Banner --}}
                        <div class="mt-2.5 p-2.5 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="small text-dark d-block">
                                    <i class="fas fa-coins text-warning me-1"></i> Collect as General Account Advance
                                </strong>
                                <span class="small" style="color: #475569;">Not linked to a specific statutory step? Collect as client retainer deposit.</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btn_general_advance">
                                <i class="fas fa-layer-group me-1"></i> General Deposit
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── 3. Recent Receipts Quick View ──────────────────────────────── --}}
            <div class="card border-0 shadow-sm" style="border-radius:12px; border: 1.5px solid #CBD5E1;">
                <div class="card-header bg-white py-2.5 px-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold text-dark fs-6">
                        <i class="fas fa-history text-secondary me-2"></i>Recent Payment Receipts
                    </h6>
                    <a href="{{ route('accounts.receipts.index') }}" class="btn btn-link btn-sm text-decoration-none p-0 fw-bold">
                        View Receipts Directory →
                    </a>
                </div>
                <div class="card-body p-2">
                    @forelse($recentReceipts as $rr)
                        <div class="d-flex justify-content-between align-items-center p-2 border-bottom" style="border-color: #F1F5F9 !important;">
                            <div>
                                <div class="fw-bold small text-dark">
                                    {{ $rr->receipt_number }}
                                    <span class="badge bg-light text-dark border ms-1" style="font-size:0.68rem;">{{ $rr->payment_mode }}</span>
                                </div>
                                <div class="small" style="font-size:0.75rem; color:#475569;">
                                    {{ $rr->customer->company_name ?? ($rr->customer->customer_name ?? 'N/A') }}
                                    &middot; {{ \Carbon\Carbon::parse($rr->transaction_date)->format('d M Y') }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-success tabular-nums small">₹ {{ number_format($rr->amount_paid, 2) }}</span>
                                <a href="{{ route('accounts.receipts.show', $rr->id) }}" class="btn btn-outline-secondary btn-sm py-0 px-2 fw-semibold" style="font-size:0.75rem;">
                                    View
                                </a>
                                <a href="{{ route('accounts.receipts.print', $rr->id) }}" target="_blank" class="btn btn-outline-primary btn-sm py-0 px-2" style="font-size:0.75rem;">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 small" style="color: #64748B;">
                            <i class="fas fa-receipt me-1"></i> No receipts recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- /col-lg-7 --}}

        {{-- ══════════════════════════════════════════════════════════════════
             RIGHT COLUMN — Payment Collection Desk Form (Compact & Direct)
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="col-lg-5">
            <div class="payment-right-sticky">
                <div class="payment-form-card">
                    <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark fs-6">
                            <i class="fas fa-money-check-alt text-primary me-2"></i>3. Payment Collection Details
                        </h5>
                        <span class="badge bg-light text-primary border small fw-bold" id="collection_mode_badge">Direct Entry</span>
                    </div>

                    <div class="card-body p-3">
                        <form action="{{ route('accounts.payments.store') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="customer_id"      id="form_customer_id"      value="{{ old('customer_id', $selectedCustomerId) }}">
                            <input type="hidden" name="application_type" id="form_application_type" value="{{ old('application_type', $selectedAppType ?: 'general') }}">
                            <input type="hidden" name="application_id"   id="form_application_id"   value="{{ old('application_id', $selectedAppId) }}">

                            {{-- Target Application Dossier Banner --}}
                            <div class="target-app-mini mb-2.5 shadow-sm" id="target_app_card">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <span class="badge bg-light text-dark fw-bold mb-1" style="font-size:0.68rem;" id="disp_target_service">General Advance / Retainer</span>
                                        <h6 class="fw-bold mb-0 text-white fs-6" id="disp_target_ref">Direct Payment on Account</h6>
                                        <div class="small opacity-75" style="font-size:0.75rem;" id="disp_target_concession">Unassigned / General Quarry Account</div>
                                    </div>
                                    <span class="badge bg-warning text-dark fw-bold" style="font-size:0.7rem;" id="disp_target_status">General</span>
                                </div>
                                <hr class="my-1.5 border-white opacity-25">
                                <div class="d-flex justify-content-between small" style="font-size:0.78rem;">
                                    <span>Agreed: <strong class="tabular-nums" id="disp_target_value">₹ 0.00</strong></span>
                                    <span>Pending: <strong class="tabular-nums text-warning" id="disp_target_pending">₹ 0.00</strong></span>
                                </div>
                            </div>

                            {{-- Amount to Collect Input --}}
                            <div class="mb-2">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center mb-1">
                                    <span>Amount to Collect (₹) <span class="text-danger">*</span></span>
                                    <span class="fw-semibold" style="font-size:0.75rem; color:#475569;">Indian Rupee (₹)</span>
                                </label>
                                <div class="input-group clean-input-group shadow-sm">
                                    <span class="input-group-text fw-bold">₹</span>
                                    <input type="number" step="0.01" min="0.01"
                                           name="amount_paid" id="amount_paid"
                                           class="form-control fw-bold text-primary tabular-nums @error('amount_paid') is-invalid @enderror"
                                           value="{{ old('amount_paid') }}" placeholder="0.00" required>
                                </div>
                                @error('amount_paid')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                {{-- Quick Presets Pills --}}
                                <div class="d-flex gap-1 mt-1.5 flex-wrap" id="quick_presets_row">
                                    <button type="button" class="quick-preset-pill" id="btn_preset_full" data-pct="100">
                                        <i class="fas fa-check-double text-success"></i> Pay Full
                                    </button>
                                    <button type="button" class="quick-preset-pill" id="btn_preset_half" data-pct="50">
                                        <i class="fas fa-percentage text-info"></i> Pay 50%
                                    </button>
                                    <button type="button" class="quick-preset-pill" id="btn_preset_25" data-pct="25">
                                        <i class="fas fa-adjust text-warning"></i> Pay 25%
                                    </button>
                                    <button type="button" class="quick-preset-pill" id="btn_preset_clear">
                                        <i class="fas fa-times text-danger"></i> Clear
                                    </button>
                                </div>

                                {{-- Real-Time Balance & Words Calculation Box --}}
                                <div class="amount-calc-box mt-1.5">
                                    <div class="d-flex justify-content-between align-items-center mb-0.5">
                                        <span class="fw-bold" style="font-size:0.72rem; color:#475569;">REMAINING DUE AFTER PAYMENT:</span>
                                        <span class="fw-bold tabular-nums" id="calc_remaining_balance">₹ 0.00</span>
                                    </div>
                                    <div class="words-text" id="calc_amount_words">
                                        <span style="color:#64748B; font-weight:normal;">Enter amount above to preview in words…</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Mode & Transaction Date --}}
                            <div class="row g-2 mb-2 clean-input-group">
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold small text-dark mb-1">
                                        Payment Mode <span class="text-danger">*</span>
                                    </label>
                                    <select name="payment_mode" id="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror" required>
                                        <option value="NEFT/RTGS" {{ old('payment_mode', 'NEFT/RTGS') == 'NEFT/RTGS' ? 'selected' : '' }}>NEFT / RTGS</option>
                                        <option value="UPI/GPay"  {{ old('payment_mode') == 'UPI/GPay'  ? 'selected' : '' }}>UPI / GPay / QR</option>
                                        <option value="Cheque"    {{ old('payment_mode') == 'Cheque'    ? 'selected' : '' }}>Cheque</option>
                                        <option value="Cash"      {{ old('payment_mode') == 'Cash'      ? 'selected' : '' }}>Cash</option>
                                        <option value="Other"     {{ old('payment_mode') == 'Other'     ? 'selected' : '' }}>Other Mode</option>
                                    </select>
                                    @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold small text-dark mb-1">
                                        Payment Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="transaction_date" id="transaction_date"
                                           class="form-control @error('transaction_date') is-invalid @enderror"
                                           value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                                    @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Bank Details Row --}}
                            <div class="row g-2 mb-2 clean-input-group" id="bank_details_row">
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold small text-dark mb-1">Issuing / Depository Bank</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-university"></i></span>
                                        <input type="text" name="bank_name" id="bank_name"
                                               class="form-control @error('bank_name') is-invalid @enderror"
                                               value="{{ old('bank_name') }}" placeholder="e.g. State Bank of India">
                                    </div>
                                    @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold small text-dark mb-1">UTR / Cheque Ref No</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-receipt"></i></span>
                                        <input type="text" name="reference_number" id="reference_number"
                                               class="form-control @error('reference_number') is-invalid @enderror"
                                               value="{{ old('reference_number') }}" placeholder="e.g. UTR20260929001">
                                    </div>
                                    @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Narration with Suggestion Chips --}}
                            <div class="mb-2.5">
                                <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center mb-1">
                                    <span>Payment Narration / Notes</span>
                                    <span class="fw-normal" style="font-size:0.75rem; color:#64748B;">Optional</span>
                                </label>
                                <textarea name="notes" id="notes" rows="1" style="min-height:36px; resize:vertical;"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Enter transaction remarks or statutory details…">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror

                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    <span class="narration-chip" data-text="50% Advance Payment for statutory processing">+ 50% Advance</span>
                                    <span class="narration-chip" data-text="Final settlement towards milestone completion">+ Final Settlement</span>
                                    <span class="narration-chip" data-text="Payment received towards DGPS survey charges">+ DGPS Demarcation</span>
                                </div>
                            </div>

                            {{-- Action Submit Buttons --}}
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-record-main shadow" id="btn_review_payment">
                                    <i class="fas fa-check-circle me-1.5"></i> Review &amp; Record Payment
                                </button>
                                <a href="{{ route('accounts.receipts.index') }}" class="btn btn-light py-1.5 text-muted small">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>{{-- /payment-right-sticky --}}
        </div>{{-- /col-lg-5 --}}

    </div>{{-- /row --}}
</div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     CONFIRM PAYMENT VOUCHER MODAL
══════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:14px; overflow:hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);">
                <h5 class="modal-title fw-bold fs-6 text-white" id="confirmModalLabel" style="color: #FFFFFF !important;">
                    <i class="fas fa-shield-alt me-2 text-warning"></i> Confirm Payment Collection
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="small fw-bold ps-0" width="38%" style="color:#475569;">Customer / Client</td>
                                <td class="fw-bold text-dark fs-6" id="modal_conf_client">-</td>
                            </tr>
                            <tr>
                                <td class="small fw-bold ps-0" style="color:#475569;">Target Dossier</td>
                                <td class="fw-bold text-primary" id="modal_conf_app">-</td>
                            </tr>
                            <tr>
                                <td class="small fw-bold ps-0" style="color:#475569;">Amount to Collect</td>
                                <td class="fw-bold text-success fs-5 tabular-nums" id="modal_conf_amount">₹ 0.00</td>
                            </tr>
                            <tr>
                                <td class="small fw-bold ps-0" style="color:#475569;">Amount in Words</td>
                                <td class="fst-italic fw-semibold text-dark small" id="modal_conf_words">-</td>
                            </tr>
                            <tr>
                                <td class="small fw-bold ps-0" style="color:#475569;">Payment Mode</td>
                                <td class="fw-bold text-dark" id="modal_conf_mode">-</td>
                            </tr>
                            <tr>
                                <td class="small fw-bold ps-0" style="color:#475569;">Transaction Date</td>
                                <td class="fw-bold text-dark" id="modal_conf_date">-</td>
                            </tr>
                            <tr id="modal_conf_ref_row">
                                <td class="small fw-bold ps-0" style="color:#475569;">Bank &amp; UTR Ref</td>
                                <td class="tabular-nums fw-bold text-dark" id="modal_conf_ref">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="form-check mt-3 p-2.5 bg-light rounded border">
                    <input class="form-check-input ms-1" type="checkbox" id="check_auto_print" checked>
                    <label class="form-check-label small fw-bold text-dark ms-2" for="check_auto_print">
                        <i class="fas fa-print text-primary me-1"></i> Auto-open printable Money Receipt voucher in a new tab
                    </label>
                </div>

                <div class="p-2.5 px-3 mt-3 mb-0 rounded-3 small fw-semibold" style="background:#F0F7FF; border:1px solid #BFDBFE; color:#1E3A8A;">
                    <i class="fas fa-info-circle me-1 text-primary"></i>
                    Recording this payment atomically updates the application ledger and issues a certified sequential receipt voucher.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light px-3 fw-semibold" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-1"></i> Back to Edit
                </button>
                <button type="button" class="btn btn-record-main px-4" id="btn_execute_submit">
                    <i class="fas fa-check-circle me-1"></i> Confirm &amp; Issue Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── 1. Select2 Initialization ─────────────────────────────── */
    $('#customer_selector').select2({
        placeholder: '🔍 Type customer name, company, or mobile number…',
        allowClear: true,
        width: '100%'
    });

    /* ── 2. DOM Elements & State ──────────────────────────────── */
    const formCustomerId    = document.getElementById('form_customer_id');
    const formAppType       = document.getElementById('form_application_type');
    const formAppId         = document.getElementById('form_application_id');
    const amountInput       = document.getElementById('amount_paid');
    const btnGeneralAdvance = document.getElementById('btn_general_advance');
    const duesContainer     = document.getElementById('dues_main_container');
    const duesEmptyState    = document.getElementById('dues_empty_state');
    const duesLoader        = document.getElementById('dues_loader');
    const cardsContainer    = document.getElementById('dues_cards_container');
    const duesBadgeCounter  = document.getElementById('dues_badge_counter');
    const appSearchInput    = document.getElementById('app_search_input');
    const bannerBox         = document.getElementById('customer_dossier_banner');
    const linkLedger        = document.getElementById('link_view_ledger');

    // Display fields
    const dispService       = document.getElementById('disp_target_service');
    const dispRef           = document.getElementById('disp_target_ref');
    const dispConcession    = document.getElementById('disp_target_concession');
    const dispStatus        = document.getElementById('disp_target_status');
    const dispValue         = document.getElementById('disp_target_value');
    const dispPending       = document.getElementById('disp_target_pending');
    const calcWords         = document.getElementById('calc_amount_words');
    const calcRemaining     = document.getElementById('calc_remaining_balance');

    let allLoadedDues       = [];
    let currentFilter       = 'all';
    let selectedItemPending = 0;
    let selectedCustomerName= '';

    const initialCustomerId = "{{ $selectedCustomerId }}";
    const initialAppType    = "{{ $selectedAppType }}";
    const initialAppId      = "{{ $selectedAppId }}";

    /* ── 3. Module Metadata (FontAwesome Strict / No Emojis) ─── */
    const moduleIcons = {
        lease_application:   { icon: 'fa-file-contract',  cls: 'icon-box-lease',      name: 'Lease Application' },
        mining_application:  { icon: 'fa-mountain',       cls: 'icon-box-mining',     name: 'Mining Plan' },
        environment_project: { icon: 'fa-leaf',           cls: 'icon-box-eviron',     name: 'Environment Clearance' },
        ec_certificate:      { icon: 'fa-award',          cls: 'icon-box-ec',         name: 'EC Certificate' },
        ppt_application:     { icon: 'fa-briefcase',      cls: 'icon-box-ppt',        name: 'PPT Department' },
        dgps_survey:         { icon: 'fa-satellite-dish', cls: 'icon-box-dgps',       name: 'DGPS Survey' },
        drone_survey:        { icon: 'fa-paper-plane',    cls: 'icon-box-drone',      name: 'Drone Survey' },
        ec_compliance:       { icon: 'fa-clipboard-check',cls: 'icon-box-compliance', name: 'EC Compliance' },
        general:             { icon: 'fa-coins',          cls: 'icon-box-general',    name: 'General Advance' }
    };

    /* ── 4. Customer Selection Listener ───────────────────────── */
    $('#customer_selector').on('change', function () {
        const customerId = this.value;
        formCustomerId.value = customerId;

        if (!customerId) {
            bannerBox.style.display       = 'none';
            duesContainer.style.display   = 'none';
            duesEmptyState.style.display  = 'block';
            duesBadgeCounter.textContent  = '0 Applications';
            setGeneralAdvance();
            return;
        }

        const selectedOpt = this.options[this.selectedIndex];
        selectedCustomerName = selectedOpt.getAttribute('data-company') || selectedOpt.getAttribute('data-name') || 'Client';
        const mobile = selectedOpt.getAttribute('data-mobile') || 'Not on file';
        const gstin  = selectedOpt.getAttribute('data-gstin')  || 'Unregistered';
        const slug   = selectedOpt.getAttribute('data-slug')   || customerId;

        // Populate Banner
        document.getElementById('banner_client_name').textContent = selectedCustomerName;
        document.getElementById('banner_mobile').textContent      = mobile;
        document.getElementById('banner_gstin').textContent       = 'GST: ' + gstin;
        document.getElementById('banner_total_due').textContent   = '…';
        linkLedger.href = '{{ url("/accounts/ledger") }}/' + slug;
        bannerBox.style.display = 'block';

        loadCustomerDues(customerId);
    });

    /* ── 5. Fetch Customer Dues via AJAX ──────────────────────── */
    function loadCustomerDues(customerId) {
        duesEmptyState.style.display = 'none';
        duesContainer.style.display  = 'none';
        duesLoader.style.display     = 'block';

        fetch('{{ url("accounts/payments/customer-dues") }}/' + customerId, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            duesLoader.style.display = 'none';

            if (!data.success) {
                alert('Failed to retrieve customer dues.');
                return;
            }

            allLoadedDues = data.dues || [];
            duesContainer.style.display = 'block';

            // Summary metrics
            const s = data.summary;
            document.getElementById('banner_total_due').textContent      = '₹ ' + formatIndianNumber(s.total_pending);
            document.getElementById('banner_apps_count').textContent     = allLoadedDues.length;
            document.getElementById('banner_total_billed').textContent    = '₹ ' + formatIndianNumber(s.total_product_value);
            document.getElementById('banner_total_received').textContent  = '₹ ' + formatIndianNumber(s.total_paid);
            duesBadgeCounter.textContent = allLoadedDues.length + ' Applications';

            // Counts for filter pills
            const pendingCount = allLoadedDues.filter(d => d.pending_amount > 0).length;
            const settledCount = allLoadedDues.filter(d => d.pending_amount <= 0).length;
            document.getElementById('count_all').textContent     = allLoadedDues.length;
            document.getElementById('count_pending').textContent = pendingCount;
            document.getElementById('count_settled').textContent = settledCount;

            renderCards();

            // Auto-select initial or highest pending due
            if (initialAppType && initialAppId) {
                const target = allLoadedDues.find(d => d.application_type === initialAppType && d.application_id == initialAppId);
                if (target) selectApplication(target);
            } else if (allLoadedDues.length > 0) {
                const firstPending = allLoadedDues.find(d => d.pending_amount > 0) || allLoadedDues[0];
                selectApplication(firstPending);
            } else {
                setGeneralAdvance();
            }
        })
        .catch(err => {
            duesLoader.style.display = 'none';
            console.error('Dues fetch error:', err);
            alert('Failed to load application dues.');
        });
    }

    /* ── 6. Render Application Cards with Filter & Search ───────── */
    function renderCards() {
        cardsContainer.innerHTML = '';
        const searchQ = (appSearchInput.value || '').trim().toLowerCase();

        // Filter list
        let filtered = allLoadedDues.filter(item => {
            // Tab filter
            if (currentFilter === 'pending' && item.pending_amount <= 0) return false;
            if (currentFilter === 'settled' && item.pending_amount > 0) return false;

            // Search filter
            if (searchQ) {
                const matchRef  = (item.reference_number || '').toLowerCase().includes(searchQ);
                const matchLbl  = (item.service_label || '').toLowerCase().includes(searchQ);
                const matchInfo = (item.concession_info || '').toLowerCase().includes(searchQ);
                return matchRef || matchLbl || matchInfo;
            }
            return true;
        });

        if (filtered.length === 0) {
            cardsContainer.innerHTML = `
                <div class="text-center py-4 text-muted bg-light rounded-3 my-2 fw-semibold">
                    <i class="fas fa-search me-1"></i> No applications found matching the selected filter.
                </div>`;
            document.getElementById('showing_apps_text').textContent = 'Showing 0 of ' + allLoadedDues.length + ' applications';
            return;
        }

        // Limit rendering for ultra-fast performance on 400+ applications
        const displayLimit = 25;
        const visibleItems = filtered.slice(0, displayLimit);

        visibleItems.forEach(d => {
            const meta = moduleIcons[d.application_type] || moduleIcons['general'];

            let statusCls = 'status-pending', badgeBg = 'bg-danger text-white', badgeTxt = 'Pending';
            if (d.payment_status === 'paid' || d.pending_amount <= 0) {
                statusCls = 'status-paid'; badgeBg = 'bg-success text-white'; badgeTxt = 'Settled';
            } else if (d.payment_status === 'partial') {
                statusCls = 'status-partial'; badgeBg = 'bg-warning text-dark'; badgeTxt = 'Partial';
            }

            const card = document.createElement('div');
            card.className = `app-card ${statusCls}`;
            card.id = `card_${d.application_type}_${d.application_id}`;
            card.innerHTML = `
                <div class="d-flex align-items-start gap-2.5">
                    <div class="module-icon-box ${meta.cls}">
                        <i class="fas ${meta.icon}"></i>
                    </div>
                    <div class="flex-grow-1 min-width-0">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <span class="fw-bold text-dark small d-block">${escapeHtml(d.service_label)}</span>
                                <div class="small fw-semibold" style="font-size:0.75rem; color:#475569;">
                                    <i class="fas fa-hashtag me-1 text-primary"></i><strong>${escapeHtml(d.reference_number)}</strong>
                                    ${d.concession_info ? ' &bull; <i class="fas fa-map-marker-alt text-danger me-1"></i>' + escapeHtml(d.concession_info) : ''}
                                </div>
                            </div>
                            <span class="badge ${badgeBg} rounded-pill fw-bold" style="font-size:0.68rem;">${badgeTxt}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <div class="small tabular-nums">
                                <span style="color:#64748B;">Agreed: </span><span class="fw-bold text-dark">₹ ${formatIndianNumber(d.product_value)}</span>
                                <span class="ms-3" style="color:#64748B;">Due: </span>
                                <span class="fw-bold ${d.pending_amount > 0 ? 'text-danger' : 'text-success'}">
                                    ₹ ${formatIndianNumber(d.pending_amount)}
                                </span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary btn-select-card py-0 px-2 shadow-sm fw-bold"
                                    style="font-size:0.75rem;"
                                    data-type="${escapeHtml(d.application_type)}"
                                    data-id="${d.application_id}"
                                    data-label="${escapeHtml(d.service_label)}"
                                    data-ref="${escapeHtml(d.reference_number)}"
                                    data-info="${escapeHtml(d.concession_info)}"
                                    data-val="${d.product_value}"
                                    data-pending="${d.pending_amount}"
                                    data-status="${badgeTxt}">
                                <i class="fas fa-check me-1"></i> Select
                            </button>
                        </div>
                    </div>
                </div>`;

            cardsContainer.appendChild(card);
        });

        document.getElementById('showing_apps_text').textContent =
            `Showing ${visibleItems.length} of ${filtered.length} applications` + (filtered.length !== allLoadedDues.length ? ` (filtered from ${allLoadedDues.length})` : '');
    }

    /* ── 7. Filter Tabs & Search Listeners ─────────────────────── */
    document.querySelectorAll('.filter-tab-pill').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.filter-tab-pill').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            renderCards();
        });
    });

    appSearchInput.addEventListener('input', function () {
        renderCards();
    });

    /* ── 8. Card Click & Selection Logic ───────────────────────── */
    cardsContainer.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-select-card') || e.target.closest('.app-card');
        if (!btn) return;

        const targetBtn = btn.classList.contains('btn-select-card') ? btn : btn.querySelector('.btn-select-card');
        if (!targetBtn) return;

        selectApplication({
            application_type: targetBtn.dataset.type,
            application_id:   targetBtn.dataset.id,
            service_label:    targetBtn.dataset.label,
            reference_number: targetBtn.dataset.ref,
            concession_info:  targetBtn.dataset.info,
            product_value:    parseFloat(targetBtn.dataset.val) || 0,
            pending_amount:   parseFloat(targetBtn.dataset.pending) || 0,
            status_text:      targetBtn.dataset.status
        });
    });

    function selectApplication(item) {
        formAppType.value   = item.application_type;
        formAppId.value     = item.application_id;
        selectedItemPending = item.pending_amount || 0;

        dispService.textContent    = item.service_label;
        dispRef.textContent        = item.reference_number;
        dispConcession.textContent = item.concession_info || 'Quarry Concession Area';
        dispValue.textContent      = '₹ ' + formatIndianNumber(item.product_value);
        dispPending.textContent    = '₹ ' + formatIndianNumber(item.pending_amount);
        dispStatus.textContent     = item.status_text || (item.pending_amount > 0 ? 'Pending' : 'Settled');

        document.getElementById('collection_mode_badge').textContent = 'Linked to Dossier';

        // Pre-fill amount to collect
        if (item.pending_amount > 0) {
            amountInput.value = item.pending_amount.toFixed(2);
        } else {
            amountInput.value = '';
        }
        recalculateBalance();

        // Highlight selected card
        document.querySelectorAll('.app-card').forEach(c => c.classList.remove('selected'));
        const activeCard = document.getElementById(`card_${item.application_type}_${item.application_id}`);
        if (activeCard) {
            activeCard.classList.add('selected');
            activeCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    /* ── 9. General Advance Retainer Mode ──────────────────────── */
    btnGeneralAdvance.addEventListener('click', setGeneralAdvance);

    function setGeneralAdvance() {
        formAppType.value   = 'general';
        formAppId.value     = '';
        selectedItemPending = 0;

        dispService.textContent    = 'General Advance / Retainer';
        dispRef.textContent        = 'Direct Payment on Account';
        dispConcession.textContent = 'Unassigned / Quarry Owner General Deposit';
        dispValue.textContent      = '₹ 0.00';
        dispPending.textContent    = '₹ 0.00';
        dispStatus.textContent     = 'General Deposit';

        document.getElementById('collection_mode_badge').textContent = 'General Advance';
        document.querySelectorAll('.app-card').forEach(c => c.classList.remove('selected'));

        recalculateBalance();
    }

    /* ── 10. Quick Preset Amount Pills ─────────────────────────── */
    document.querySelectorAll('.quick-preset-pill[data-pct]').forEach(btn => {
        btn.addEventListener('click', function () {
            if (selectedItemPending <= 0) return;
            const pct = parseInt(this.dataset.pct) / 100;
            const computedVal = +(selectedItemPending * pct).toFixed(2);
            amountInput.value = computedVal;
            recalculateBalance();
        });
    });

    document.getElementById('btn_preset_clear').addEventListener('click', function () {
        amountInput.value = '';
        recalculateBalance();
    });

    /* ── 11. Narration Suggestions Chips ───────────────────────── */
    document.querySelectorAll('.narration-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const notesEl = document.getElementById('notes');
            const snippet = this.getAttribute('data-text');
            if (notesEl.value.trim().length > 0) {
                notesEl.value += '; ' + snippet;
            } else {
                notesEl.value = snippet;
            }
        });
    });

    /* ── 12. Amount & Remaining Balance Real-Time Recalculation ── */
    amountInput.addEventListener('input', recalculateBalance);

    function recalculateBalance() {
        const val = parseFloat(amountInput.value) || 0;

        // Update Indian words
        if (val <= 0) {
            calcWords.innerHTML = '<span style="color:#64748B; font-weight:normal;">Enter amount above to preview in words…</span>';
        } else {
            calcWords.innerHTML = '<strong style="color:#1E3A8A;">In Words:</strong> ' + convertNumberToWords(val);
        }

        // Update Remaining Due
        if (selectedItemPending > 0) {
            const rem = selectedItemPending - val;
            if (rem <= 0) {
                calcRemaining.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ₹ 0.00 (Full Settlement)</span>';
            } else {
                calcRemaining.innerHTML = `<span class="text-danger fw-bold">₹ ${formatIndianNumber(rem)} (Pending Balance)</span>`;
            }
        } else {
            calcRemaining.innerHTML = '<span class="fw-semibold" style="color:#64748B;">₹ 0.00 (Direct Retainer)</span>';
        }
    }

    /* ── 13. Payment Mode & Bank Row Toggle ────────────────────── */
    const paymentModeSelect = document.getElementById('payment_mode');
    const bankDetailsRow    = document.getElementById('bank_details_row');

    paymentModeSelect.addEventListener('change', function () {
        if (this.value === 'Cash') {
            bankDetailsRow.style.opacity = '0.35';
        } else {
            bankDetailsRow.style.opacity = '1';
        }
    });

    /* ── 14. Confirmation Modal Review ─────────────────────────── */
    document.getElementById('btn_review_payment').addEventListener('click', function () {
        const amount = parseFloat(amountInput.value) || 0;
        if (!formCustomerId.value) {
            alert('Please select a quarry client first from the search box.');
            return;
        }
        if (amount <= 0) {
            alert('Please enter a valid payment collection amount (greater than ₹ 0).');
            amountInput.focus();
            return;
        }

        document.getElementById('modal_conf_client').textContent = selectedCustomerName || 'Client';
        document.getElementById('modal_conf_app').textContent    = dispRef.textContent + ' (' + dispService.textContent + ')';
        document.getElementById('modal_conf_amount').textContent = '₹ ' + formatIndianNumber(amount);
        document.getElementById('modal_conf_words').textContent  = convertNumberToWords(amount);
        document.getElementById('modal_conf_mode').textContent   = paymentModeSelect.value;
        document.getElementById('modal_conf_date').textContent   = document.getElementById('transaction_date').value;

        const refVal = document.getElementById('reference_number').value.trim();
        const bankVal = document.getElementById('bank_name').value.trim();
        if (refVal || bankVal) {
            document.getElementById('modal_conf_ref').textContent = (bankVal ? bankVal + ' &bull; ' : '') + (refVal || 'N/A');
            document.getElementById('modal_conf_ref_row').style.display = '';
        } else {
            document.getElementById('modal_conf_ref_row').style.display = 'none';
        }

        const modal = new bootstrap.Modal(document.getElementById('confirmPaymentModal'));
        modal.show();
    });

    document.getElementById('btn_execute_submit').addEventListener('click', function () {
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Recording Payment…';

        // Check if auto-print checkbox is enabled
        const autoPrint = document.getElementById('check_auto_print').checked;
        if (autoPrint) {
            sessionStorage.setItem('gtms_auto_print_receipt', '1');
        }

        document.getElementById('paymentForm').submit();
    });

    /* ── 15. Keyboard Shortcuts ────────────────────────────────── */
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('btn_review_payment').click();
        }
        if (e.key === 'Escape') {
            setGeneralAdvance();
        }
    });

    /* ── 16. Auto-Open Receipt Print on Redirect (if requested) ── */
    if (sessionStorage.getItem('gtms_auto_print_receipt') === '1') {
        sessionStorage.removeItem('gtms_auto_print_receipt');
        @if(session('receipt_id'))
            window.open('{{ url("accounts/receipts") }}/{{ session("receipt_id") }}/print', '_blank');
        @endif
    }

    /* ── 17. Number Formatting & Indian Currency Helpers ──────── */
    function formatIndianNumber(x) {
        x = Number(x).toFixed(2).toString();
        const afterPoint = x.indexOf('.') > 0 ? x.substring(x.indexOf('.')) : '';
        let beforePoint = Math.floor(parseFloat(x)).toString();
        const lastThree = beforePoint.substring(beforePoint.length - 3);
        const otherNumbers = beforePoint.substring(0, beforePoint.length - 3);
        if (otherNumbers !== '') {
            beforePoint = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + "," + lastThree;
        } else {
            beforePoint = lastThree;
        }
        return beforePoint + afterPoint;
    }

    function convertNumberToWords(amount) {
        amount = Math.round(amount * 100) / 100;
        const words = [
            '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'
        ];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        function underThousand(n) {
            let str = '';
            if (n >= 100) {
                str += words[Math.floor(n / 100)] + ' Hundred ';
                n %= 100;
            }
            if (n > 0) {
                if (str !== '') str += 'and ';
                if (n < 20) {
                    str += words[n] + ' ';
                } else {
                    str += tens[Math.floor(n / 10)] + ' ';
                    if (n % 10 > 0) str += words[n % 10] + ' ';
                }
            }
            return str.trim();
        }

        const num = Math.floor(amount);
        const decimal = Math.round((amount - num) * 100);

        if (num === 0 && decimal === 0) return 'Rupees Zero Only';

        const crore = Math.floor(num / 10000000);
        let rem = num % 10000000;
        const lakh = Math.floor(rem / 100000);
        rem %= 100000;
        const thousand = Math.floor(rem / 1000);
        rem %= 1000;
        const hundredAndBelow = rem;

        let out = '';
        if (crore > 0) out += underThousand(crore) + ' Crore ';
        if (lakh > 0) out += underThousand(lakh) + ' Lakh ';
        if (thousand > 0) out += underThousand(thousand) + ' Thousand ';
        if (hundredAndBelow > 0) out += underThousand(hundredAndBelow) + ' ';

        out = 'Rupees ' + out.trim();
        if (decimal > 0) {
            out += ' and ' + underThousand(decimal) + ' Paise';
        }
        return out + ' Only';
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    /* ── 18. Initial Trigger if customer is preselected ────────── */
    if ($('#customer_selector').val()) {
        $('#customer_selector').trigger('change');
    }
    if (amountInput.value) {
        recalculateBalance();
    }
});
</script>
@endpush
