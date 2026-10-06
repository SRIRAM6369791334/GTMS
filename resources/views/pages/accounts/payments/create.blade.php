@extends('layouts.app')

@section('title', 'Record Customer Payment • GTMS Accounts')

@push('styles')
    @include('pages.accounts.partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        /* ═══════════════════════════════════════════════════════════════════
           GTMS FINTECH PAYMENT VOUCHER TERMINAL (ZOHO / STRIPE INSPIRED)
           Single centered luxury financial voucher with zero scroll friction.
        ═══════════════════════════════════════════════════════════════════ */

        .payment-page-container {
            max-width: 1080px;
            margin: 0 auto;
            padding-bottom: 2.5rem;
        }

        /* ── Master Financial Voucher Card ── */
        .voucher-card {
            background: #FFFFFF;
            border-radius: 14px;
            border: 1px solid #E2E8F0;
            border-top: 4px solid #0F1E4D;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
            overflow: hidden;
        }

        .voucher-header {
            padding: 20px 26px 16px;
            background: #FFFFFF;
            border-bottom: 1px solid #F1F5F9;
        }

        .voucher-body {
            padding: 24px 26px;
        }

        .voucher-section-divider {
            border-top: 1px solid #F1F5F9;
            margin: 20px 0;
        }

        /* ── Form Controls & Typography ── */
        .form-label-fintech {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-control-fintech, .form-select-fintech {
            position: relative;
            height: 42px;
            line-height: 1.5;
            border: 1px solid #CBD5E1;
            color: #0F172A;
            font-weight: 500;
            font-size: 0.88rem;
            border-radius: 8px;
            padding: 8px 12px;
            background-color: #FFFFFF;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            width: 100%;
        }

        /* ── Critical Guard: Prevent input[type="date"] picker from escaping to main-wrapper ── */
        input[type="date"] {
            position: relative !important;
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            position: static !important;
            cursor: pointer !important;
            background: initial !important;
            color: initial !important;
            opacity: 0.7 !important;
            padding: 0 !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
        }

        .form-control-fintech:focus, .form-select-fintech:focus {
            border-color: #1E3A8A;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
            color: #0F172A;
            outline: none;
        }

        /* ── Select2 Customization ── */
        select#customer_selector { display: none !important; }
        .select2-container { width: 100% !important; }
        .select2-container .select2-selection--single {
            height: 44px !important;
            border: 1px solid #CBD5E1 !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
            display: flex !important;
            align-items: center !important;
            background-color: #FFFFFF !important;
            transition: all 0.15s ease;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            color: #0F172A !important;
            font-weight: 600 !important;
            font-size: 0.88rem !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1E3A8A !important;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border: 1.5px solid #CBD5E1 !important;
            border-radius: 8px !important;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12) !important;
            z-index: 1050;
        }
        .select2-results__option {
            padding: 8px 14px !important;
            font-size: 0.88rem !important;
            color: #0F172A !important;
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #0F1E4D !important;
            color: #FFFFFF !important;
        }

        /* ── Customer Capsule Dossier ── */
        .customer-dossier-capsule {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 14px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            animation: fadeIn 0.2s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .client-avatar-badge {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
            color: #FFFFFF;
            font-weight: 800;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ── Hero Amount Input ── */
        .hero-amount-box {
            position: relative;
        }
        .hero-amount-group {
            display: flex;
            align-items: stretch;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            overflow: hidden;
            background: #FFFFFF;
            transition: all 0.15s ease;
        }
        .hero-amount-group:focus-within {
            border-color: #1E3A8A;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12);
        }
        .hero-amount-prefix {
            background: #F8FAFC;
            border-right: 1px solid #CBD5E1;
            color: #0F1E4D;
            font-weight: 800;
            font-size: 1.4rem;
            padding: 0 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .hero-amount-input {
            border: none !important;
            font-size: 1.45rem !important;
            font-weight: 800 !important;
            color: #0F1E4D !important;
            padding: 8px 14px !important;
            width: 100%;
            outline: none;
            letter-spacing: 0.02em;
        }
        .hero-amount-input::placeholder {
            color: #CBD5E1;
            font-weight: 600;
        }

        /* ── Preset Chips ── */
        .preset-chip {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #334155;
            cursor: pointer;
            transition: all 0.12s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .preset-chip:hover:not(.disabled) {
            background: #0F1E4D;
            color: #FFFFFF;
            border-color: #0F1E4D;
        }
        .preset-chip.disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* ── Live Indian Words Preview ── */
        .words-preview-strip {
            font-size: 0.78rem;
            font-weight: 600;
            color: #1E3A8A;
            font-style: italic;
            margin-top: 6px;
            line-height: 1.4;
            min-height: 20px;
        }

        /* ── Payment Method Horizontal Pills ── */
        .mode-pills-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
        }
        .mode-pill-btn {
            height: 42px;
            padding: 6px 4px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            text-align: center;
            cursor: pointer;
            transition: all 0.14s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            white-space: nowrap;
        }
        .mode-pill-btn:hover {
            border-color: #1E3A8A;
            color: #0F1E4D;
            background: #F8FAFC;
        }
        .mode-pill-btn.active {
            background: #0F1E4D;
            color: #FFFFFF;
            border-color: #0F1E4D;
            box-shadow: 0 2px 6px rgba(15, 30, 77, 0.15);
        }

        /* ── Dues Allocation Table ── */
        .dues-table-box {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            overflow: hidden;
            background: #FFFFFF;
        }
        .dues-table-header-bar {
            padding: 10px 14px;
            background: #F8FAFC;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }
        .table-voucher {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table-voucher thead th {
            background-color: #F8FAFC;
            color: #475569;
            font-size: 0.70rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 9px 12px;
            border-bottom: 1px solid #E2E8F0;
            white-space: nowrap;
        }
        .table-voucher tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            font-size: 0.84rem;
            color: #1E293B;
            border-bottom: 1px solid #F1F5F9;
        }
        .table-voucher tbody tr {
            cursor: pointer;
            transition: background-color 0.12s ease;
        }
        .table-voucher tbody tr:hover td {
            background-color: #F8FAFC;
        }
        .table-voucher tbody tr.selected td {
            background-color: #EFF6FF !important;
        }
        .table-voucher tbody tr.selected {
            border-left: 3px solid #1E3A8A;
        }

        /* Module Icons */
        .module-icon-sm {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
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

        /* Quick Pills / Filters */
        .tab-filter-btn {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 14px;
            border: 1px solid #CBD5E1;
            background: #FFFFFF;
            color: #475569;
            cursor: pointer;
            transition: all 0.12s ease;
        }
        .tab-filter-btn:hover { background: #F1F5F9; color: #0F172A; }
        .tab-filter-btn.active {
            background: #0F1E4D;
            color: #FFFFFF;
            border-color: #0F1E4D;
        }

        /* Status Dot Pills */
        .status-dot-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.68rem;
            font-weight: 700;
        }
        .status-dot-pill .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }
        .status-pending-pill { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .status-pending-pill .dot { background: #DC2626; }
        .status-settled-pill { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .status-settled-pill .dot { background: #059669; }

        /* Narration Tags */
        .narration-tag {
            font-size: 0.70rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            color: #475569;
            cursor: pointer;
            transition: all 0.12s ease;
            display: inline-block;
        }
        .narration-tag:hover {
            background: #EFF6FF;
            color: #1E3A8A;
            border-color: #93C5FD;
        }

        /* ── Primary Action Button ── */
        .btn-issue-receipt {
            background: #0F1E4D;
            color: #FFFFFF;
            border: 1px solid #0F1E4D;
            font-weight: 700;
            font-size: 0.92rem;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.18s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
        }
        .btn-issue-receipt:hover {
            background: #1A327E;
            border-color: #1A327E;
            color: #FFFFFF;
            box-shadow: 0 6px 16px rgba(15, 30, 77, 0.25);
            transform: translateY(-1px);
        }
        .btn-issue-receipt:disabled {
            background: #94A3B8 !important;
            border-color: #94A3B8 !important;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .tabular-nums {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }
    </style>
@endpush

@section('main_content')
<div class="content-body">
    <div class="container-fluid payment-page-container">

        {{-- ── Top Navigation Bar ────────────────────────────────────────── --}}
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 py-0 text-muted" style="font-size: 0.76rem;">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts.receipts.index') }}" class="text-decoration-none text-muted">Accounts</a></li>
                        <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Record Payment</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice-dollar" style="color: #0F1E4D;"></i>
                    <span>Record Customer Payment</span>
                </h4>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('accounts.receipts.index') }}" class="btn btn-outline-dark btn-sm fw-semibold shadow-sm"
                   style="border-color: #CBD5E1; color: #0F172A; height: 36px; display: inline-flex; align-items: center; font-size: 0.8rem;">
                    <i class="fas fa-receipt me-1.5" style="color: #0F1E4D;"></i> Receipts Directory
                </a>
                <a href="{{ route('accounts.quotations.index') }}" class="btn btn-outline-primary btn-sm fw-semibold shadow-sm"
                   style="border-color: #BFDBFE; color: #1E3A8A; height: 36px; display: inline-flex; align-items: center; font-size: 0.8rem;">
                    <i class="fas fa-file-invoice me-1.5"></i> Quotations
                </a>
            </div>
        </div>

        {{-- ── Flash Notifications ──────────────────────────────────────── --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 py-2 px-3" role="alert"
                 style="background-color: #ECFDF5; color: #065F46; border-left: 4px solid #059669 !important; border-radius: 8px;">
                <i class="fas fa-check-circle me-2 text-success"></i> <strong>Success:</strong> {{ session('success') }}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3 py-2 px-3" role="alert"
                 style="background-color: #FEF2F2; color: #991B1B; border-left: 4px solid #DC2626 !important; border-radius: 8px;">
                <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i> Submission Failed:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════════════
             FINANCIAL VOUCHER CARD (SINGLE COMPACT CONTAINER)
        ═══════════════════════════════════════════════════════════════════ --}}
        <div class="voucher-card">
            <form action="{{ route('accounts.payments.store') }}" method="POST" id="paymentForm">
                @csrf
                <input type="hidden" name="customer_id"      id="form_customer_id"      value="{{ old('customer_id', $selectedCustomerId) }}">
                <input type="hidden" name="application_type" id="form_application_type" value="{{ old('application_type', $selectedAppType ?: 'general') }}">
                <input type="hidden" name="application_id"   id="form_application_id"   value="{{ old('application_id', $selectedAppId) }}">

                {{-- ── SECTION 1: Customer Intake & Hero Amount Received ────── --}}
                <div class="voucher-header">
                    <div class="row g-4 align-items-start">
                        {{-- Left Column: Customer Selector & Capsule --}}
                        <div class="col-lg-6">
                            <label class="form-label-fintech" for="customer_selector">
                                <span>Customer / Quarry Client <span class="text-danger">*</span></span>
                                <span class="badge bg-light text-dark border fw-bold" style="font-size: 0.68rem; border-color: #CBD5E1 !important;" id="customer_counter_badge">
                                    {{ count($customers) }} Clients
                                </span>
                            </label>
                            <select id="customer_selector" style="width: 100%;">
                                <option value="">-- Search client name, company, or mobile --</option>
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

                            {{-- Compact Customer Capsule Dossier --}}
                            <div id="customer_dossier_banner" class="customer-dossier-capsule" style="display:none;">
                                <div class="d-flex align-items-center gap-2 text-truncate">
                                    <div class="client-avatar-badge" id="banner_avatar">C</div>
                                    <div class="text-truncate">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.86rem;" id="banner_client_name">-</div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            <span id="banner_mobile">-</span> • <span id="banner_gstin">GST: Unregistered</span>
                                            <a href="#" id="link_view_ledger" target="_blank" class="fw-bold text-decoration-none ms-1" style="color: #1E3A8A;">
                                                Ledger ↗
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end text-nowrap">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.75rem;" id="banner_total_due">
                                        ₹ 0.00 Due
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Right Column: Hero Amount Received & Words --}}
                        <div class="col-lg-6">
                            <div class="hero-amount-box">
                                <label class="form-label-fintech" for="amount_paid">
                                    <span>Amount Received (₹) <span class="text-danger">*</span></span>
                                    <span class="text-muted fw-semibold" style="text-transform: none; font-size: 0.70rem;">Indian Rupee (INR)</span>
                                </label>
                                <div class="hero-amount-group shadow-sm">
                                    <div class="hero-amount-prefix">₹</div>
                                    <input type="number" step="0.01" min="0.01"
                                           name="amount_paid" id="amount_paid"
                                           class="hero-amount-input tabular-nums @error('amount_paid') is-invalid @enderror"
                                           value="{{ old('amount_paid') }}" placeholder="0.00" required>
                                </div>
                                @error('amount_paid')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

                                {{-- Preset Chips --}}
                                <div class="d-flex gap-1.5 mt-1.5 flex-wrap" id="quick_presets_row">
                                    <button type="button" class="preset-chip disabled" id="btn_preset_full" data-pct="100">
                                        <i class="fas fa-check-double text-success"></i> 100% Full Dues
                                    </button>
                                    <button type="button" class="preset-chip disabled" id="btn_preset_half" data-pct="50">
                                        <i class="fas fa-percentage text-info"></i> 50% Milestone
                                    </button>
                                    <button type="button" class="preset-chip disabled" id="btn_preset_25" data-pct="25">
                                        <i class="fas fa-adjust text-warning"></i> 25% Token
                                    </button>
                                    <button type="button" class="preset-chip" id="btn_preset_clear">
                                        <i class="fas fa-times text-danger"></i> Clear
                                    </button>
                                </div>

                                {{-- Live Indian Words Preview --}}
                                <div class="words-preview-strip" id="calc_amount_words">
                                    <span style="color: #94A3B8; font-weight: normal; font-style: normal;">Enter amount above to preview in words…</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="voucher-body">
                    {{-- ── SECTION 2: Inline Payment Details Row ──────────────── --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label-fintech" for="transaction_date">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="transaction_date" id="transaction_date"
                                   class="form-control-fintech @error('transaction_date') is-invalid @enderror"
                                   value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                            @error('transaction_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4 col-sm-6">
                            <label class="form-label-fintech">Payment Method <span class="text-danger">*</span></label>
                            <div class="mode-pills-row">
                                <div class="mode-pill-btn active" data-mode="NEFT/RTGS" id="pill_mode_neft">
                                    <i class="fas fa-university"></i> NEFT
                                </div>
                                <div class="mode-pill-btn" data-mode="UPI/GPay" id="pill_mode_upi">
                                    <i class="fas fa-qrcode"></i> UPI
                                </div>
                                <div class="mode-pill-btn" data-mode="Cheque" id="pill_mode_cheque">
                                    <i class="fas fa-money-check"></i> Cheque
                                </div>
                                <div class="mode-pill-btn" data-mode="Cash" id="pill_mode_cash">
                                    <i class="fas fa-coins"></i> Cash
                                </div>
                            </div>
                            {{-- Hidden native select to preserve 100% backend compatibility --}}
                            <select name="payment_mode" id="payment_mode" class="d-none" required>
                                <option value="NEFT/RTGS" {{ old('payment_mode', 'NEFT/RTGS') == 'NEFT/RTGS' ? 'selected' : '' }}>NEFT/RTGS</option>
                                <option value="UPI/GPay"  {{ old('payment_mode') == 'UPI/GPay'  ? 'selected' : '' }}>UPI/GPay</option>
                                <option value="Cheque"    {{ old('payment_mode') == 'Cheque'    ? 'selected' : '' }}>Cheque</option>
                                <option value="Cash"      {{ old('payment_mode') == 'Cash'      ? 'selected' : '' }}>Cash</option>
                                <option value="Other"     {{ old('payment_mode') == 'Other'     ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-2 col-sm-6" id="bank_name_col">
                            <label class="form-label-fintech" for="bank_name">Depository / Bank</label>
                            <input type="text" name="bank_name" id="bank_name"
                                   class="form-control-fintech @error('bank_name') is-invalid @enderror"
                                   value="{{ old('bank_name') }}"
                                   placeholder="e.g. SBI, HDFC">
                            @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3 col-sm-6" id="ref_number_col">
                            <label class="form-label-fintech" for="reference_number">UTR / Cheque Ref #</label>
                            <input type="text" name="reference_number" id="reference_number"
                                   class="form-control-fintech @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number') }}"
                                   placeholder="e.g. UTR20260929001">
                            @error('reference_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div id="cash_mode_notice" class="small mb-3 fw-semibold" style="display:none; font-size: 0.76rem; color: #059669 !important;">
                        <i class="fas fa-check-circle me-1"></i> Cash mode active — bank routing and UTR clearance references are not required.
                    </div>

                    {{-- ── SECTION 3: Outstanding Invoices & Dues Allocation ──── --}}
                    <div class="mb-4">
                        <div class="dues-table-box">
                            <div class="dues-table-header-bar">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold text-dark" style="font-size: 0.82rem;">
                                        <i class="fas fa-list-check me-1" style="color: #0F1E4D;"></i> Unpaid Statutory Dues &amp; Invoices
                                    </span>
                                    <span id="dues_badge_counter" class="badge bg-white border text-dark fw-bold" style="font-size: 0.68rem; border-color: #CBD5E1 !important;">
                                        0 Applications
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="d-flex gap-1">
                                        <button type="button" class="tab-filter-btn active" data-filter="all" id="tab_filter_all">All (<span id="count_all">0</span>)</button>
                                        <button type="button" class="tab-filter-btn" data-filter="pending" id="tab_filter_pending">Pending (<span id="count_pending">0</span>)</button>
                                        <button type="button" class="tab-filter-btn" data-filter="settled" id="tab_filter_settled">Settled (<span id="count_settled">0</span>)</button>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" id="btn_general_advance"
                                            style="border-color: #FCD34D; background-color: #FFFBEB; font-size: 0.72rem; padding: 3px 10px; height: 28px;">
                                        <i class="fas fa-coins text-warning me-1"></i> General Deposit
                                    </button>
                                </div>
                            </div>

                            {{-- Loader --}}
                            <div id="dues_loader" class="text-center py-4" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                <span class="small ms-2 fw-semibold text-muted">Retrieving customer statutory records…</span>
                            </div>

                            {{-- Empty State (No client selected) --}}
                            <div id="dues_empty_state" class="text-center py-4 text-muted">
                                <i class="fas fa-hand-holding-usd fa-2x mb-2" style="color: #CBD5E1;"></i>
                                <div class="fw-bold" style="color: #0F172A; font-size: 0.86rem;">No Client Selected</div>
                                <div class="small" style="font-size: 0.76rem;">Select a quarry client from the search box above to load pending statutory fees.</div>
                            </div>

                            {{-- Table of Dues --}}
                            <div id="dues_main_container" style="display:none; max-height: 260px; overflow-y: auto;">
                                <table class="table-voucher">
                                    <thead>
                                        <tr>
                                            <th style="width: 4%;"></th>
                                            <th style="width: 38%;">Service Scope &amp; Concession</th>
                                            <th style="width: 22%;">Dossier Reference</th>
                                            <th style="width: 14%;" class="text-end">Agreed Fee</th>
                                            <th style="width: 14%;" class="text-end">Due Balance</th>
                                            <th style="width: 8%;" class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dues_cards_container">
                                        {{-- Populated dynamically --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Selected Target Dossier Indicator --}}
                        <div class="d-flex justify-content-between align-items-center mt-2 px-1 text-muted small" style="font-size: 0.74rem;">
                            <div>
                                <span class="fw-semibold">Allocating To:</span>
                                <strong class="text-dark ms-1" id="disp_target_ref">Direct Payment on Account (General Advance)</strong>
                                <span id="disp_target_service" style="display:none;">General</span>
                                <span id="disp_target_concession" style="display:none;"></span>
                                <span id="disp_target_status" style="display:none;">General</span>
                                <span id="disp_target_value" style="display:none;">₹ 0.00</span>
                                <span id="disp_target_pending" style="display:none;">₹ 0.00</span>
                            </div>
                            <div>
                                <span class="fw-semibold">Remaining Due:</span>
                                <span class="fw-bold ms-1" id="calc_remaining_balance">₹ 0.00</span>
                            </div>
                        </div>
                    </div>

                    {{-- ── SECTION 4: Footer & Execution ──────────────────────── --}}
                    <div class="voucher-section-divider"></div>

                    <div class="row align-items-center g-3">
                        {{-- Narration with suggestion tags --}}
                        <div class="col-lg-6">
                            <label class="form-label-fintech" for="notes">
                                <span>Narration / Remarks</span>
                                <span class="text-muted fw-normal" style="text-transform: none; font-size: 0.70rem;">Optional</span>
                            </label>
                            <input type="text" name="notes" id="notes"
                                   class="form-control-fintech @error('notes') is-invalid @enderror"
                                   value="{{ old('notes') }}"
                                   placeholder="e.g. 50% Advance towards statutory processing…">
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror

                            <div class="d-flex gap-1 mt-1.5 flex-wrap">
                                <span class="narration-tag" data-text="50% Advance Payment">+ 50% Advance</span>
                                <span class="narration-tag" data-text="Final milestone settlement">+ Final Settlement</span>
                                <span class="narration-tag" data-text="DGPS survey charges">+ DGPS Fee</span>
                                <span class="narration-tag" data-text="Statutory Retainer Deposit">+ Retainer</span>
                            </div>
                        </div>

                        {{-- Action Button & Auto-Print --}}
                        <div class="col-lg-6 text-lg-end">
                            <div class="d-inline-flex flex-column align-items-lg-end gap-2 w-100">
                                <div class="form-check d-inline-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input" type="checkbox" id="check_auto_print" checked style="width: 15px; height: 15px;">
                                    <label class="form-check-label small fw-semibold text-muted" for="check_auto_print" style="font-size: 0.76rem;">
                                        <i class="fas fa-print text-primary me-1"></i> Auto-open printable receipt voucher in new tab
                                    </label>
                                </div>
                                <div class="d-flex align-items-center justify-content-lg-end gap-2 w-100">
                                    <a href="{{ route('accounts.receipts.index') }}" class="btn btn-outline-dark btn-sm fw-semibold"
                                       style="border-color: #CBD5E1; color: #475569; height: 42px; display: inline-flex; align-items: center; padding: 0 16px; font-size: 0.82rem;">
                                        Cancel
                                    </a>
                                    <button type="button" class="btn-issue-receipt" id="btn_submit_payment">
                                        <i class="fas fa-check-circle"></i> Save &amp; Issue Receipt
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>{{-- /voucher-body --}}
            </form>
        </div>{{-- /voucher-card --}}

    </div>{{-- /payment-page-container --}}
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     CONFIRMATION MODAL (LIGHTWEIGHT CERTIFIED DIALOG)
═══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="confirmPaymentModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);">
                <h5 class="modal-title fw-bold fs-6 text-white" id="confirmModalLabel" style="color: #FFFFFF !important;">
                    <i class="fas fa-shield-alt me-2 text-warning"></i> Confirm Payment Collection
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.88rem;">
                    <tbody>
                        <tr>
                            <td class="text-muted fw-bold ps-0" width="36%">Client / Customer</td>
                            <td class="fw-bold text-dark" id="modal_conf_client">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold ps-0">Allocated Dossier</td>
                            <td class="fw-bold" style="color: #1E3A8A;" id="modal_conf_app">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold ps-0">Amount Received</td>
                            <td class="fw-bold text-success fs-5 tabular-nums" id="modal_conf_amount">₹ 0.00</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold ps-0">In Words</td>
                            <td class="fst-italic fw-semibold small text-dark" id="modal_conf_words">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold ps-0">Payment Method</td>
                            <td class="fw-bold text-dark" id="modal_conf_mode">-</td>
                        </tr>
                        <tr>
                            <td class="text-muted fw-bold ps-0">Transaction Date</td>
                            <td class="fw-bold text-dark" id="modal_conf_date">-</td>
                        </tr>
                        <tr id="modal_conf_ref_row">
                            <td class="text-muted fw-bold ps-0">Bank &amp; Ref</td>
                            <td class="tabular-nums fw-bold text-dark" id="modal_conf_ref">-</td>
                        </tr>
                    </tbody>
                </table>

                <div class="p-2.5 px-3 mt-3 mb-0 rounded-3 small fw-semibold" style="background: #F0F7FF; border: 1px solid #BFDBFE; color: #1E3A8A; font-size: 0.76rem;">
                    <i class="fas fa-info-circle me-1 text-primary"></i>
                    This transaction atomically updates the statutory application ledger and generates a certified sequential money receipt voucher.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light px-3 fw-semibold" data-bs-dismiss="modal" style="border: 1px solid #CBD5E1; color: #475569;">
                    <i class="fas fa-arrow-left me-1"></i> Edit
                </button>
                <button type="button" class="btn-issue-receipt px-4" id="btn_execute_submit">
                    <i class="fas fa-check-circle me-1"></i> Confirm &amp; Issue Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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

    // Payment mode & bank elements
    const paymentModeSelect = document.getElementById('payment_mode');
    const bankNameCol       = document.getElementById('bank_name_col');
    const refNumCol         = document.getElementById('ref_number_col');
    const bankNameInput     = document.getElementById('bank_name');
    const refNumInput       = document.getElementById('reference_number');
    const cashNotice        = document.getElementById('cash_mode_notice');

    let allLoadedDues       = [];
    let currentFilter       = 'all';
    let selectedItemPending = 0;
    let selectedCustomerName= '';

    const initialCustomerId = "{{ $selectedCustomerId }}";
    const initialAppType    = "{{ $selectedAppType }}";
    const initialAppId      = "{{ $selectedAppId }}";

    /* ── 3. Module Metadata ─── */
    const moduleIcons = {
        lease:               { icon: 'fa-file-contract',   cls: 'icon-box-lease',      name: 'Lease Application' },
        lease_application:   { icon: 'fa-file-contract',   cls: 'icon-box-lease',      name: 'Lease Application' },
        mining:              { icon: 'fa-mountain',        cls: 'icon-box-mining',     name: 'Mining Plan' },
        mining_application:  { icon: 'fa-mountain',        cls: 'icon-box-mining',     name: 'Mining Plan' },
        environment:         { icon: 'fa-leaf',            cls: 'icon-box-eviron',     name: 'Environment Clearance' },
        environment_project: { icon: 'fa-leaf',            cls: 'icon-box-eviron',     name: 'Environment Clearance' },
        ec:                  { icon: 'fa-award',           cls: 'icon-box-ec',         name: 'EC Certificate' },
        ec_certificate:      { icon: 'fa-award',           cls: 'icon-box-ec',         name: 'EC Certificate' },
        ppt:                 { icon: 'fa-briefcase',       cls: 'icon-box-ppt',        name: 'PPT Department' },
        ppt_application:     { icon: 'fa-briefcase',       cls: 'icon-box-ppt',        name: 'PPT Department' },
        dgps:                { icon: 'fa-satellite-dish',  cls: 'icon-box-dgps',       name: 'DGPS Survey' },
        dgps_survey:         { icon: 'fa-satellite-dish',  cls: 'icon-box-dgps',       name: 'DGPS Survey' },
        drone:               { icon: 'fa-paper-plane',     cls: 'icon-box-drone',      name: 'Drone Survey' },
        drone_survey:        { icon: 'fa-paper-plane',     cls: 'icon-box-drone',      name: 'Drone Survey' },
        ec_compliance:       { icon: 'fa-clipboard-check', cls: 'icon-box-compliance', name: 'EC Compliance' },
        general:             { icon: 'fa-coins',           cls: 'icon-box-general',    name: 'General Advance' }
    };

    /* ── 4. Customer Selection Handler ─── */
    function handleCustomerSelection(customerId) {
        formCustomerId.value = customerId;

        if (!customerId) {
            bannerBox.style.display       = 'none';
            duesContainer.style.display   = 'none';
            duesEmptyState.style.display  = 'block';
            duesBadgeCounter.textContent  = '0 Applications';
            setGeneralAdvance();
            return;
        }

        const selectEl = document.getElementById('customer_selector');
        const selectedOpt = selectEl.options[selectEl.selectedIndex];
        if (selectedOpt) {
            selectedCustomerName = selectedOpt.getAttribute('data-company') || selectedOpt.getAttribute('data-name') || 'Client';
            const mobile = selectedOpt.getAttribute('data-mobile') || 'No phone';
            const gstin  = selectedOpt.getAttribute('data-gstin')  || 'Unregistered';
            const slug   = selectedOpt.getAttribute('data-slug')   || customerId;

            document.getElementById('banner_client_name').textContent = selectedCustomerName;
            document.getElementById('banner_mobile').textContent      = mobile;
            document.getElementById('banner_gstin').textContent       = 'GST: ' + gstin;
            document.getElementById('banner_total_due').textContent   = 'Loading dues…';
            document.getElementById('banner_avatar').textContent      = selectedCustomerName.charAt(0).toUpperCase();
            linkLedger.href = '{{ url("/accounts/ledger") }}/' + slug;

            bannerBox.style.display = 'flex';
        }

        loadCustomerDues(customerId);
    }

    $('#customer_selector').on('select2:select select2:clear change', function () {
        handleCustomerSelection(this.value);
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

            // Summary metrics for Dossier Capsule
            const s = data.summary;
            document.getElementById('banner_total_due').textContent = '₹ ' + formatIndianNumber(s.total_pending) + ' Due';
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

    /* ── 6. Render Application Rows ───────────────────────────── */
    function renderCards() {
        cardsContainer.innerHTML = '';

        let filtered = allLoadedDues.filter(item => {
            if (currentFilter === 'pending' && item.pending_amount <= 0) return false;
            if (currentFilter === 'settled' && item.pending_amount > 0) return false;
            return true;
        });

        if (filtered.length === 0) {
            cardsContainer.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-3 fw-semibold text-muted" style="font-size: 0.8rem;">
                        No statutory records found for the selected filter.
                    </td>
                </tr>`;
            return;
        }

        filtered.forEach(d => {
            const meta = moduleIcons[d.application_type] || moduleIcons['general'];

            let statusPillCls = 'status-pending-pill', badgeTxt = 'Pending';
            if (d.payment_status === 'paid' || d.pending_amount <= 0) {
                statusPillCls = 'status-settled-pill'; badgeTxt = 'Settled';
            } else if (d.payment_status === 'partial') {
                statusPillCls = 'status-pending-pill'; badgeTxt = 'Partial';
            }

            const isSelected = (formAppType.value === d.application_type && formAppId.value == d.application_id);

            const tr = document.createElement('tr');
            tr.id = `card_${d.application_type}_${d.application_id}`;
            if (isSelected) tr.classList.add('selected');

            tr.innerHTML = `
                <td class="text-center">
                    <input type="radio" name="dues_row_radio" class="form-check-input" ${isSelected ? 'checked' : ''} style="cursor: pointer;">
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="module-icon-sm ${meta.cls}">
                            <i class="fas ${meta.icon}"></i>
                        </div>
                        <div>
                            <div class="fw-bold" style="color: #0F172A; font-size: 0.84rem;">${escapeHtml(d.service_label)}</div>
                            <div class="text-muted" style="font-size: 0.70rem;">
                                ${d.concession_info ? '<i class="fas fa-map-marker-alt text-danger me-1"></i>' + escapeHtml(d.concession_info) : 'Quarry Area'}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-dark border fw-bold" style="font-family: monospace; font-size: 0.72rem; border-color: #CBD5E1 !important;">
                        # ${escapeHtml(d.reference_number)}
                    </span>
                </td>
                <td class="text-end tabular-nums fw-semibold" style="color: #0F172A; font-size: 0.82rem;">
                    ₹ ${formatIndianNumber(d.product_value)}
                </td>
                <td class="text-end tabular-nums fw-bold ${d.pending_amount > 0 ? 'text-danger' : 'text-success'}" style="font-size: 0.84rem;">
                    ₹ ${formatIndianNumber(d.pending_amount)}
                </td>
                <td class="text-center">
                    <span class="status-dot-pill ${statusPillCls}">
                        <span class="dot"></span> ${badgeTxt}
                    </span>
                </td>`;

            // Row click event
            tr.addEventListener('click', function () {
                selectApplication({
                    application_type: d.application_type,
                    application_id:   d.application_id,
                    service_label:    d.service_label,
                    reference_number: d.reference_number,
                    concession_info:  d.concession_info,
                    product_value:    parseFloat(d.product_value) || 0,
                    pending_amount:   parseFloat(d.pending_amount) || 0,
                    status_text:      badgeTxt
                });
            });

            cardsContainer.appendChild(tr);
        });
    }

    /* ── 7. Filter Tabs Listeners ──────────────────────────────── */
    document.querySelectorAll('.tab-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter');
            renderCards();
        });
    });

    /* ── 8. Selection Logic ────────────────────────────────────── */
    function selectApplication(item) {
        formAppType.value   = item.application_type;
        formAppId.value     = item.application_id;
        selectedItemPending = item.pending_amount || 0;

        dispService.textContent    = item.service_label;
        dispRef.textContent        = item.service_label + ' • #' + item.reference_number;
        dispConcession.textContent = item.concession_info || 'Quarry Area';
        dispValue.textContent      = '₹ ' + formatIndianNumber(item.product_value);
        dispPending.textContent    = '₹ ' + formatIndianNumber(item.pending_amount);
        dispStatus.textContent     = item.status_text || (item.pending_amount > 0 ? 'Pending' : 'Settled');

        // Pre-fill amount
        if (item.pending_amount > 0) {
            amountInput.value = item.pending_amount.toFixed(2);
        } else {
            amountInput.value = '';
        }

        updatePresetPillsState();
        recalculateBalance();

        // Update row highlights & radio
        document.querySelectorAll('#dues_cards_container tr').forEach(r => {
            r.classList.remove('selected');
            const radio = r.querySelector('input[type="radio"]');
            if (radio) radio.checked = false;
        });

        const activeRow = document.getElementById(`card_${item.application_type}_${item.application_id}`);
        if (activeRow) {
            activeRow.classList.add('selected');
            const radio = activeRow.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }
    }

    /* ── 9. General Advance Mode ───────────────────────────────── */
    btnGeneralAdvance.addEventListener('click', setGeneralAdvance);

    function setGeneralAdvance() {
        formAppType.value   = 'general';
        formAppId.value     = '';
        selectedItemPending = 0;

        dispService.textContent    = 'General Advance';
        dispRef.textContent        = 'Direct Payment on Account (General Advance)';
        dispConcession.textContent = 'Unassigned';
        dispValue.textContent      = '₹ 0.00';
        dispPending.textContent    = '₹ 0.00';
        dispStatus.textContent     = 'General';

        document.querySelectorAll('#dues_cards_container tr').forEach(r => {
            r.classList.remove('selected');
            const radio = r.querySelector('input[type="radio"]');
            if (radio) radio.checked = false;
        });

        updatePresetPillsState();
        recalculateBalance();
    }

    /* ── 10. Quick Preset Pills ────────────────────────────────── */
    function updatePresetPillsState() {
        const hasPending = selectedItemPending > 0;
        document.querySelectorAll('.preset-chip[data-pct]').forEach(btn => {
            if (hasPending) {
                btn.classList.remove('disabled');
            } else {
                btn.classList.add('disabled');
            }
        });
    }

    document.querySelectorAll('.preset-chip[data-pct]').forEach(btn => {
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

    /* ── 11. Narration Suggestions Tags ────────────────────────── */
    document.querySelectorAll('.narration-tag').forEach(tag => {
        tag.addEventListener('click', function () {
            const notesEl = document.getElementById('notes');
            const snippet = this.getAttribute('data-text');
            if (notesEl.value.trim().length > 0) {
                notesEl.value += '; ' + snippet;
            } else {
                notesEl.value = snippet;
            }
        });
    });

    /* ── 12. Amount & Balance Real-Time Recalculation ──────────── */
    amountInput.addEventListener('input', recalculateBalance);

    function recalculateBalance() {
        const val = parseFloat(amountInput.value) || 0;

        // Indian words preview
        if (val <= 0) {
            calcWords.innerHTML = '<span style="color: #94A3B8; font-weight: normal; font-style: normal;">Enter amount above to preview in words…</span>';
        } else {
            calcWords.innerHTML = '<strong style="color: #1E3A8A;"><i class="fas fa-shield-alt me-1 text-success"></i> In Words:</strong> ' + convertNumberToWords(val);
        }

        // Remaining balance preview
        if (selectedItemPending > 0) {
            const rem = selectedItemPending - val;
            if (rem <= 0) {
                calcRemaining.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i> ₹ 0.00 (Full Settlement)</span>';
            } else {
                calcRemaining.innerHTML = `<span class="text-danger fw-bold">₹ ${formatIndianNumber(rem)}</span>`;
            }
        } else {
            calcRemaining.innerHTML = '<span class="text-muted fw-semibold">₹ 0.00 (General)</span>';
        }
    }

    /* ── 13. Payment Mode Pills & Dynamic Field Visibility ─────── */
    const modePills = document.querySelectorAll('.mode-pill-btn');
    modePills.forEach(pill => {
        pill.addEventListener('click', function () {
            modePills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const mode = this.getAttribute('data-mode');
            paymentModeSelect.value = mode;
            handlePaymentModeChange();
        });
    });

    function handlePaymentModeChange() {
        const mode = paymentModeSelect.value;

        // Sync active pill visual
        modePills.forEach(p => {
            if (p.getAttribute('data-mode') === mode) {
                p.classList.add('active');
            } else {
                p.classList.remove('active');
            }
        });

        if (mode === 'Cash') {
            bankNameCol.style.display = 'none';
            refNumCol.style.display   = 'none';
            bankNameInput.disabled    = true;
            refNumInput.disabled      = true;
            bankNameInput.value       = '';
            refNumInput.value         = '';
            if (cashNotice) cashNotice.style.display = 'block';
        } else if (mode === 'UPI/GPay') {
            bankNameCol.style.display = 'none';
            refNumCol.style.display   = 'block';
            refNumCol.className       = 'col-md-5 col-sm-12';
            bankNameInput.disabled    = true;
            refNumInput.disabled      = false;
            refNumInput.placeholder   = 'e.g. UPI Ref / Google Pay ID';
            if (cashNotice) cashNotice.style.display = 'none';
        } else {
            bankNameCol.style.display = 'block';
            refNumCol.style.display   = 'block';
            bankNameCol.className     = 'col-md-2 col-sm-6';
            refNumCol.className       = 'col-md-3 col-sm-6';
            bankNameInput.disabled    = false;
            refNumInput.disabled      = false;
            bankNameInput.placeholder = 'e.g. SBI, HDFC';
            refNumInput.placeholder   = 'e.g. UTR20260929001';
            if (cashNotice) cashNotice.style.display = 'none';
        }
    }

    paymentModeSelect.addEventListener('change', handlePaymentModeChange);
    handlePaymentModeChange();

    /* ── 14. Confirmation Modal & Submit ────────────────────────── */
    document.getElementById('btn_submit_payment').addEventListener('click', function () {
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
        document.getElementById('modal_conf_app').textContent    = dispRef.textContent;
        document.getElementById('modal_conf_amount').textContent = '₹ ' + formatIndianNumber(amount);
        document.getElementById('modal_conf_words').textContent  = convertNumberToWords(amount);
        document.getElementById('modal_conf_mode').textContent   = paymentModeSelect.value;
        document.getElementById('modal_conf_date').textContent   = document.getElementById('transaction_date').value;

        const refVal = document.getElementById('reference_number').value.trim();
        const bankVal = document.getElementById('bank_name').value.trim();
        if (refVal || bankVal) {
            document.getElementById('modal_conf_ref').textContent = (bankVal ? bankVal + ' • ' : '') + (refVal || 'N/A');
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
            document.getElementById('btn_submit_payment').click();
        }
        if (e.key === 'Escape') {
            setGeneralAdvance();
        }
    });

    /* ── 16. Auto-Open Receipt Print on Redirect ─────────────── */
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
        handleCustomerSelection($('#customer_selector').val());
    }
    if (amountInput.value) {
        recalculateBalance();
    }
});
</script>
@endpush
