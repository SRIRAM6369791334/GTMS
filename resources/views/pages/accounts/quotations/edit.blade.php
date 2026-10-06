@extends('layouts.app')

@section('title', 'Edit Quotation ' . $quotation->quotation_number . ' • GTMS')

@section('main_content')
@push('styles')
    @include('pages.accounts.partials.theme')
@endpush

<style>
    /* ─── GTMS EXECUTIVE FINTECH FORM SYSTEM ─── */
    .quotation-wrapper {
        max-width: 1400px;
        margin: 0 auto;
    }

    .bento-card {
        background: #FFFFFF;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .card-accent-navy { border-top: 3px solid #0F1E4D; }
    .card-accent-blue { border-top: 3px solid #1E3A8A; }
    .card-accent-emerald { border-top: 3px solid #059669; }
    .card-accent-sky { border-top: 3px solid #0284C7; }

    /* Step Navigation Stepper */
    .form-stepper {
        display: flex;
        align-items: center;
        gap: 16px;
        background: #FFFFFF;
        padding: 10px 20px;
        border-radius: 10px;
        border: 1px solid #E2E8F0;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .stepper-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748B;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .stepper-item:hover { color: #1E3A8A; }
    .stepper-item.active { color: #0F1E4D; }
    .stepper-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.76rem;
        font-weight: 800;
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #CBD5E1;
    }
    .stepper-item.active .stepper-num {
        background: #0F1E4D;
        color: #FFFFFF;
        border-color: #0F1E4D;
    }
    .stepper-separator {
        color: #CBD5E1;
        font-size: 0.85rem;
    }

    /* Section Step Pill */
    .step-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background-color: #EFF6FF;
        color: #1E3A8A;
        border: 1px solid #BFDBFE;
    }

    /* Subsection Header Labels */
    .form-subsection-title {
        font-size: 0.74rem;
        font-weight: 800;
        color: #1E3A8A;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border-bottom: 1px solid #F1F5F9;
        padding-bottom: 6px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* ── UNIFORM 42px FORM CONTROLS & ALIGNMENT ── */
    .form-label {
        font-size: 0.72rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        color: #475569 !important;
        margin-bottom: 6px !important;
        display: block !important;
    }
    .form-control, .form-select {
        height: 42px !important;
        line-height: 1.5 !important;
        border: 1px solid #CBD5E1 !important;
        color: #0F172A !important;
        font-weight: 500 !important;
        font-size: 0.88rem !important;
        border-radius: 8px !important;
        padding: 8px 12px !important;
        background-color: #FFFFFF !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1E3A8A !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12) !important;
        color: #0F172A !important;
    }
    .form-control::placeholder {
        color: #94A3B8 !important;
        font-size: 0.84rem !important;
    }
    textarea.form-control {
        height: auto !important;
        min-height: 80px !important;
    }
    .input-group .form-control {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }
    .input-group .input-group-text {
        height: 42px !important;
        border: 1px solid #CBD5E1 !important;
        border-left: none !important;
        border-top-right-radius: 8px !important;
        border-bottom-right-radius: 8px !important;
        background-color: #F8FAFC !important;
        color: #475569 !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
    }

    /* Monospace Voucher Badge */
    .mono-voucher-badge {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.86rem;
        font-weight: 700;
        color: #0F1E4D;
        background-color: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 6px;
        padding: 3px 9px;
        display: inline-flex;
        align-items: center;
    }

    /* Select2 Height & Border Harmonization */
    .select2-container .select2-selection--single {
        height: 42px !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #0F172A !important;
        font-weight: 500 !important;
        font-size: 0.88rem !important;
        padding-left: 0 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 8px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #1E3A8A !important;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.12) !important;
    }

    /* ─── ULTRA-CLEAN TABLE STYLING & PERFECT ALIGNMENT ─── */
    .table-items {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-items thead th {
        background-color: #F8FAFC;
        color: #1E293B;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-top: none;
        border-bottom: 2px solid #CBD5E1;
        padding: 10px 12px;
        white-space: nowrap;
    }
    .table-items tbody tr.item-main-row td {
        padding: 10px 8px 4px 8px;
        vertical-align: middle;
        border-top: 1px solid #E2E8F0;
        border-bottom: none;
    }
    .table-items tbody tr.item-desc-row td {
        padding: 0 8px 12px 8px;
        vertical-align: middle;
        border-top: none;
        border-bottom: 1px solid #E2E8F0;
    }
    .table-items tbody tr:hover td {
        background-color: #F8FAFC;
    }

    .table-items .form-control,
    .table-items .form-select {
        height: 38px !important;
        font-size: 0.84rem !important;
        padding: 6px 10px !important;
        border-radius: 6px !important;
    }

    .item-sac {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-weight: 700;
        font-size: 0.82rem;
        background-color: #F8FAFC;
        text-align: center;
    }
    .item-subtotal-box {
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 6px 12px;
        background-color: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.90rem;
        color: #0F172A;
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    .desc-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #F8FAFC;
        border: 1px dashed #CBD5E1;
        border-radius: 6px;
        padding: 4px 10px;
    }
    .desc-prefix {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
        white-space: nowrap;
    }
    .desc-input {
        height: 30px !important;
        border: none !important;
        background: transparent !important;
        padding: 2px 6px !important;
        font-size: 0.80rem !important;
        color: #334155 !important;
        width: 100% !important;
        box-shadow: none !important;
    }
    .desc-input:focus {
        background: #FFFFFF !important;
        border-radius: 4px !important;
    }

    .row-num-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.74rem;
        font-weight: 800;
        background: #EFF6FF;
        color: #1E3A8A;
        border: 1px solid #BFDBFE;
    }
    .btn-row-delete {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #FECACA;
        background-color: #FEF2F2;
        color: #DC2626;
        transition: all 0.15s ease;
    }
    .btn-row-delete:hover {
        background-color: #DC2626;
        color: #FFFFFF;
        border-color: #DC2626;
    }

    /* Quick Preset Pills */
    .quick-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.74rem;
        font-weight: 600;
        background: #F1F5F9;
        color: #334155;
        border: 1px solid #CBD5E1;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .quick-pill:hover {
        background: #E2E8F0;
        color: #0F172A;
        border-color: #94A3B8;
    }
    .quick-pill.active {
        background: #EFF6FF;
        color: #1E3A8A;
        border-color: #1E3A8A;
        font-weight: 700;
    }

    /* Calculation & Endorsement Summary Cards */
    .endorsement-box {
        background: #FFFFFF;
        border: 1px solid #BFDBFE;
        border-left: 4px solid #1E3A8A;
        border-radius: 10px;
        padding: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 1px 3px rgba(30, 58, 138, 0.04);
    }
    .calculation-box {
        background-color: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 7px 0;
        font-size: 0.86rem;
        color: #475569;
    }
    .calc-row.total-row {
        border-top: 2px dashed #CBD5E1;
        margin-top: 10px;
        padding-top: 14px;
        font-weight: 800;
        font-size: 1.35rem;
        color: #0F1E4D;
    }
    .live-amount-words {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0F172A;
        font-style: italic;
        line-height: 1.45;
        margin-top: 8px;
    }

    /* Sticky Floating Action Bar */
    .sticky-floating-bar {
        position: sticky;
        bottom: 12px;
        z-index: 100;
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid #CBD5E1;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.15), 0 4px 10px -2px rgba(15, 23, 42, 0.05);
        padding: 12px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        backdrop-filter: blur(8px);
    }

    /* Executive Signatory Badge */
    .signatory-card {
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        padding: 8px 14px;
        height: 42px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="content-body pb-5">
    <div class="container-fluid quotation-wrapper">
        <!-- Page Title & Navigation Bar -->
        <div class="row page-titles mb-3">
            <div class="col-lg-6 col-md-12 p-md-0 d-flex align-items-center">
                <div class="welcome-text">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb mb-0 py-0" style="font-size: 0.78rem;">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accounts.quotations.index') }}" class="text-decoration-none text-muted">Quotations</a></li>
                            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Edit {{ $quotation->quotation_number }}</li>
                        </ol>
                    </nav>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                        <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-edit text-primary"></i>
                            <span>Edit Quotation Dossier:</span>
                            <span class="mono-voucher-badge">{{ $quotation->quotation_number }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 p-md-0 justify-content-lg-end mt-3 mt-lg-0 d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('accounts.quotations.index') }}" class="btn btn-light border shadow-sm fw-semibold" style="color: #334155; font-size: 0.84rem;">
                    <i class="fas fa-arrow-left me-1"></i> Directory
                </a>
                <a href="{{ route('accounts.quotations.show', $quotation->id) }}" class="btn btn-light border shadow-sm fw-semibold text-primary" style="font-size: 0.84rem;">
                    <i class="fas fa-eye me-1"></i> Dossier View
                </a>
                <a href="{{ route('accounts.quotations.print', $quotation->id) }}" target="_blank" class="btn btn-navy shadow-sm fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-print me-1"></i> A4 Printout
                </a>
            </div>
        </div>

        <!-- Section Navigation Stepper Bar -->
        <div class="form-stepper">
            <a href="#section-client" class="stepper-item active">
                <span class="stepper-num">1</span>
                <span>Client & Concession</span>
            </a>
            <span class="stepper-separator">&rarr;</span>
            <a href="#section-services" class="stepper-item">
                <span class="stepper-num">2</span>
                <span>Services Scope & Schedule</span>
            </a>
            <span class="stepper-separator">&rarr;</span>
            <a href="#section-terms" class="stepper-item">
                <span class="stepper-num">3</span>
                <span>Terms & Exclusions</span>
            </a>
        </div>

        <!-- Validation Errors Display -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm" role="alert">
                <div class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle text-danger fs-5"></i>
                    <span>Please correct the following errors before updating the quotation:</span>
                </div>
                <ul class="mb-0 ps-3 small mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('accounts.quotations.update', $quotation->id) }}" method="POST" id="quotationForm" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Section 1: Client & Concession Selection Bento Card -->
            <div class="bento-card card-accent-blue" id="section-client">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="step-badge">Step 1</span>
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-user-tie text-primary me-2"></i>Client Profile & Quarry Concession Coordinates
                        </h5>
                    </div>
                    <span class="badge bg-light text-dark border px-2 py-1 small">
                        Ref No: <strong>{{ $quotation->quotation_number }}</strong>
                    </span>
                </div>
                <div class="card-body p-4 bg-white">
                    <!-- Client & Concession Dropdowns -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-building text-primary me-1"></i>Select Registered Client <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="customer_select" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">-- Choose Client Profile --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ (old('customer_id', $quotation->customer_id) == $c->id) ? 'selected' : '' }}>
                                        {{ $c->customer_name }} {{ $c->company_name ? "({$c->company_name})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-mountain text-primary me-1"></i>Linked Quarry Concession / Lease Application
                            </label>
                            <select name="lease_application_id" id="lease_application_select" class="form-select">
                                <option value="">-- Select Concession or Enter Manually Below --</option>
                                @if($quotation->leaseApplication)
                                    <option value="{{ $quotation->lease_application_id }}" selected>
                                        {{ $quotation->quarry_name ?: 'Concession #' . $quotation->lease_application_id }} (Current Linked Concession)
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <!-- Client Details Snapshot Fields -->
                    <div class="form-subsection-title">
                        <i class="fas fa-id-card"></i> Client Particulars (Included on Official Letterhead)
                    </div>
                    <!-- Row 1: Contact Person, Company, GSTIN (4 + 4 + 4 = 12) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Contact Person / Client Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name', $quotation->customer_name) }}" placeholder="Client Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Firm / Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $quotation->company_name) }}" placeholder="M/s. Company Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Client GSTIN</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control text-uppercase" value="{{ old('gst_number', $quotation->gst_number) }}" placeholder="33ABCDE1234F1Z5">
                        </div>
                    </div>
                    <!-- Row 2: Phone, Email, Billing Address (3 + 3 + 6 = 12) -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Mobile / Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $quotation->phone) }}" placeholder="+91 94432 XXXXX">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $quotation->email) }}" placeholder="client@example.com" autocomplete="new-password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Registered Billing Address</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address', $quotation->address) }}" placeholder="Door No, Street, Town/District, Pincode">
                        </div>
                    </div>

                    <!-- Quarry Concession Snapshot Fields -->
                    <div class="form-subsection-title">
                        <i class="fas fa-layer-group"></i> Quarry Concession & Statutory Location Details
                    </div>
                    <!-- Row 1: Quarry Name, Mineral, Area Extent (6 + 3 + 3 = 12) -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Quarry / Site Name</label>
                            <input type="text" name="quarry_name" id="quarry_name" class="form-control" value="{{ old('quarry_name', $quotation->quarry_name) }}" placeholder="e.g. Chinnagoundanur Rough Stone Quarry">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Mineral Name</label>
                            <input type="text" name="mineral_name" id="mineral_name" class="form-control" value="{{ old('mineral_name', $quotation->mineral_name) }}" placeholder="Rough Stone">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Area Extent (Ha)</label>
                            <div class="input-group">
                                <input type="number" step="0.0001" min="0" name="area_extent_ha" id="area_extent_ha" class="form-control" value="{{ old('area_extent_ha', $quotation->area_extent_ha) }}" placeholder="4.5000">
                                <span class="input-group-text">Ha</span>
                            </div>
                        </div>
                    </div>
                    <!-- Row 2: District, Taluk, Village, Survey Nos (3 + 3 + 3 + 3 = 12) -->
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Revenue District</label>
                            <select name="district_id" id="district_id" class="form-select">
                                <option value="">-- Select District --</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->id }}" {{ (old('district_id', $quotation->district_id) == $d->id) ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Taluk</label>
                            <input type="text" name="taluk" id="taluk" class="form-control" value="{{ old('taluk', $quotation->taluk) }}" placeholder="e.g. Sankari">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Village / Panchayat</label>
                            <input type="text" name="village" id="village" class="form-control" value="{{ old('village', $quotation->village) }}" placeholder="e.g. Chinnagoundanur">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Survey Field Nos (S.F. Nos)</label>
                            <input type="text" name="survey_numbers" id="survey_numbers" class="form-control" value="{{ old('survey_numbers', $quotation->survey_numbers) }}" placeholder="e.g. 102/1A, 102/1B, 103/2">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Dynamic Multi-Service Line Items Bento Card -->
            <div class="bento-card card-accent-navy" id="section-services">
                <div class="card-header bg-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="step-badge">Step 2</span>
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-tasks text-primary me-2"></i>Statutory Services Scope & Commercial Schedule
                        </h5>
                    </div>

                    <!-- Quick Catalog Insertion Bar -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle fw-semibold" type="button" id="quickCatalogDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bolt text-warning me-1"></i> Quick-Add from Catalog
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="quickCatalogDropdown" style="min-width: 320px; max-height: 350px; overflow-y: auto;">
                                <li class="dropdown-header small text-uppercase fw-bold text-muted">Statutory Mining Services</li>
                                @foreach($services as $s)
                                    <li>
                                        <a class="dropdown-item py-2 quick-add-service" href="javascript:void(0);"
                                           data-name="{{ $s['service_name'] }}"
                                           data-sac="{{ $s['sac_code'] }}"
                                           data-unit="{{ $s['unit'] }}"
                                           data-rate="{{ $s['unit_rate'] }}"
                                           data-desc="{{ $s['description'] }}">
                                            <div class="fw-bold text-dark small">{{ $s['service_name'] }}</div>
                                            <div class="text-muted" style="font-size: 0.74rem;">
                                                ₹ {{ number_format($s['unit_rate'], 2) }} / {{ $s['unit'] }} &bull; SAC: {{ $s['sac_code'] }}
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <button type="button" class="btn btn-sm btn-navy fw-semibold" id="btnAddItem">
                            <i class="fas fa-plus me-1"></i> Add Custom Item
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-items" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 4%; text-align: center;">#</th>
                                    <th style="width: 40%;">Statutory Service Specification</th>
                                    <th style="width: 11%; text-align: center;">SAC Code</th>
                                    <th style="width: 10%; text-align: center;">Qty / Extent</th>
                                    <th style="width: 10%; text-align: center;">Unit</th>
                                    <th style="width: 11%; text-align: right;">Unit Rate (₹)</th>
                                    <th style="width: 10%; text-align: right;">Line Total (₹)</th>
                                    <th style="width: 4%; text-align: center;">Act</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody">
                                @php
                                    $itemsToLoop = old('items', $quotation->items->map(function($i) {
                                        return [
                                            'service_name' => $i->service_name,
                                            'sac_code'     => $i->sac_code,
                                            'description'  => $i->description,
                                            'quantity'     => (float)$i->quantity,
                                            'unit'         => $i->unit,
                                            'unit_rate'    => (float)$i->unit_rate,
                                            'subtotal'     => (float)$i->subtotal,
                                        ];
                                    })->toArray());

                                    if (empty($itemsToLoop)) {
                                        $itemsToLoop = [
                                            [
                                                'service_name' => 'DGPS Demarcation & Boundary Survey',
                                                'sac_code'     => '998334',
                                                'description'  => 'Precision DGPS boundary demarcation, pillar fixing, geo-referencing and cadastral overlay map preparation.',
                                                'quantity'     => 1,
                                                'unit'         => 'Ha',
                                                'unit_rate'    => 35000.00,
                                                'subtotal'     => 35000.00,
                                            ]
                                        ];
                                    }
                                @endphp

                                @foreach($itemsToLoop as $index => $item)
                                <!-- Row 1: Primary Parameters (Uniform 38px height) -->
                                <tr class="item-main-row" data-index="{{ $index }}">
                                    <td class="text-center" rowspan="2" style="vertical-align: middle;">
                                        <span class="row-num-badge">{{ $loop->iteration }}</span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="items[{{ $index }}][service_name]" class="form-control item-service-name fw-bold text-dark" value="{{ $item['service_name'] ?? '' }}" placeholder="Service Name" required>
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Switch from Catalog"></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 300px; max-height: 250px; overflow-y: auto;">
                                                <li class="dropdown-header small text-uppercase fw-bold text-muted">Select Standard Service</li>
                                                @foreach($services as $s)
                                                    <li>
                                                        <a class="dropdown-item small py-2 pick-service" href="javascript:void(0);"
                                                           data-name="{{ $s['service_name'] }}"
                                                           data-sac="{{ $s['sac_code'] }}"
                                                           data-unit="{{ $s['unit'] }}"
                                                           data-rate="{{ $s['unit_rate'] }}"
                                                           data-desc="{{ $s['description'] }}">
                                                            <strong class="text-dark">{{ $s['service_name'] }}</strong>
                                                            <div class="text-muted" style="font-size:0.74rem;">₹ {{ number_format($s['unit_rate'], 2) }} / {{ $s['unit'] }} &bull; SAC: {{ $s['sac_code'] }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][sac_code]" class="form-control item-sac" value="{{ $item['sac_code'] ?? '998341' }}" placeholder="998341">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" class="form-control item-qty text-center fw-bold financial-number" value="{{ $item['quantity'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $index }}][unit]" class="form-select item-unit">
                                            <option value="Ha" {{ ($item['unit'] ?? '') === 'Ha' ? 'selected' : '' }}>Ha</option>
                                            <option value="Nos" {{ ($item['unit'] ?? '') === 'Nos' ? 'selected' : '' }}>Nos</option>
                                            <option value="Job" {{ ($item['unit'] ?? '') === 'Job' ? 'selected' : '' }}>Job</option>
                                            <option value="Survey" {{ ($item['unit'] ?? '') === 'Survey' ? 'selected' : '' }}>Survey</option>
                                            <option value="Year" {{ ($item['unit'] ?? '') === 'Year' ? 'selected' : '' }}>Year</option>
                                            <option value="Month" {{ ($item['unit'] ?? '') === 'Month' ? 'selected' : '' }}>Month</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_rate]" class="form-control item-rate text-end fw-semibold financial-number" value="{{ $item['unit_rate'] ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <input type="hidden" class="item-subtotal-input" value="{{ number_format($item['subtotal'] ?? 0, 2, '.', '') }}">
                                        <div class="item-subtotal-box">
                                            ₹ <span class="item-subtotal-text ms-1">{{ number_format($item['subtotal'] ?? 0, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center" rowspan="2" style="vertical-align: middle;">
                                        <button type="button" class="btn-row-delete" title="Remove line item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <!-- Row 2: Secondary Scope Deliverables Details (Spans cleanly under parameters) -->
                                <tr class="item-desc-row" data-index="{{ $index }}">
                                    <td colspan="6">
                                        <div class="desc-wrapper">
                                            <span class="desc-prefix"><i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> Scope &amp; Deliverables:</span>
                                            <input type="text" name="items[{{ $index }}][description]" class="desc-input item-desc" value="{{ $item['description'] ?? '' }}" placeholder="Technical deliverables, statutory compliance scope, cadastral specifications...">
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Financial Calculation Summary Footer -->
                    <div class="card-footer bg-light p-4 border-top">
                        <div class="row g-4 align-items-stretch">
                            <!-- Amount in Words FinTech Box -->
                            <div class="col-lg-6">
                                <div class="endorsement-box">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: #1E3A8A;">
                                            <i class="fas fa-coins text-warning"></i>
                                            <span>Statutory Endorsement in Words (Indian Currency)</span>
                                        </div>
                                        <div class="live-amount-words" id="amountInWordsDisplay">
                                            Rupees Zero Only
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-3 pt-2 border-top" style="font-size: 0.78rem; color: #475569;">
                                        <i class="fas fa-shield-alt text-success fs-6"></i>
                                        <span>Statutory Goods & Services Tax (CGST 9% + SGST 9%) computed automatically.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Commercial Totals Box -->
                            <div class="col-lg-6">
                                <div class="calculation-box">
                                    <div class="calc-row">
                                        <span class="fw-semibold"><i class="fas fa-calculator me-1 text-primary"></i> Total Services Subtotal:</span>
                                        <span class="fw-bold text-dark fs-6 financial-number" id="displaySubtotal">₹ 0.00</span>
                                    </div>
                                    <div class="calc-row flex-wrap">
                                        <span class="d-flex align-items-center gap-2">
                                            <i class="fas fa-percent me-1 text-info"></i> GST Rate:
                                            <select name="tax_rate" id="tax_rate_select" class="form-select form-select-sm d-none" style="width: auto;">
                                                <option value="18.00" {{ old('tax_rate', $quotation->tax_rate) == '18.00' ? 'selected' : '' }}>18%</option>
                                                <option value="0.00" {{ old('tax_rate', $quotation->tax_rate) === '0.00' ? 'selected' : '' }}>0%</option>
                                                <option value="5.00" {{ old('tax_rate', $quotation->tax_rate) === '5.00' ? 'selected' : '' }}>5%</option>
                                                <option value="12.00" {{ old('tax_rate', $quotation->tax_rate) === '12.00' ? 'selected' : '' }}>12%</option>
                                            </select>
                                            <!-- Interactive GST Rate Pills -->
                                            <span class="quick-pill gst-pill {{ (old('tax_rate', $quotation->tax_rate) == '18.00') ? 'active' : '' }}" data-rate="18.00">18% Standard</span>
                                            <span class="quick-pill gst-pill {{ (old('tax_rate', $quotation->tax_rate) === '0.00') ? 'active' : '' }}" data-rate="0.00">0% Exempt</span>
                                            <span class="quick-pill gst-pill {{ (old('tax_rate', $quotation->tax_rate) === '5.00') ? 'active' : '' }}" data-rate="5.00">5%</span>
                                            <span class="quick-pill gst-pill {{ (old('tax_rate', $quotation->tax_rate) === '12.00') ? 'active' : '' }}" data-rate="12.00">12%</span>
                                        </span>
                                        <span class="badge bg-light text-dark border fw-bold" id="displayTaxRateBreakdown">CGST 9% + SGST 9%</span>
                                    </div>
                                    <div class="calc-row">
                                        <span class="fw-semibold"><i class="fas fa-file-invoice-dollar me-1 text-info"></i> Total Statutory GST:</span>
                                        <span class="fw-semibold text-dark fs-6 financial-number" id="displayTaxAmount">₹ 0.00</span>
                                    </div>
                                    <div class="calc-row total-row">
                                        <span><i class="fas fa-receipt me-1 text-primary"></i> Total Quotation Value:</span>
                                        <span class="financial-number text-primary" id="displayGrandTotal">₹ 0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Statutory Terms, Conditions & Validity Bento Card -->
            <div class="bento-card card-accent-sky" id="section-terms">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="step-badge">Step 3</span>
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="fas fa-file-contract text-primary me-2"></i>Commercial Terms & Statutory Exclusions
                        </h5>
                    </div>
                </div>
                <div class="card-body p-4 bg-white">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">Offer Validity Period</label>
                            <div class="input-group mb-2">
                                <input type="number" min="1" max="365" name="validity_days" id="validity_days_input" class="form-control" value="{{ old('validity_days', $quotation->validity_days) }}" required>
                                <span class="input-group-text">Days</span>
                            </div>
                            <!-- Quick Validity Presets -->
                            <div class="d-flex gap-1 flex-wrap">
                                <span class="quick-pill validity-pill {{ old('validity_days', $quotation->validity_days) == 15 ? 'active' : '' }}" data-days="15">15 Days</span>
                                <span class="quick-pill validity-pill {{ old('validity_days', $quotation->validity_days) == 30 ? 'active' : '' }}" data-days="30">30 Days</span>
                                <span class="quick-pill validity-pill {{ old('validity_days', $quotation->validity_days) == 45 ? 'active' : '' }}" data-days="45">45 Days</span>
                                <span class="quick-pill validity-pill {{ old('validity_days', $quotation->validity_days) == 60 ? 'active' : '' }}" data-days="60">60 Days</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Proposal Lifecycle Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status', $quotation->status) === 'draft' ? 'selected' : '' }}>Draft (Internal Review)</option>
                                <option value="sent" {{ old('status', $quotation->status) === 'sent' ? 'selected' : '' }}>Sent to Client</option>
                                <option value="accepted" {{ old('status', $quotation->status) === 'accepted' ? 'selected' : '' }}>Accepted / Confirmed</option>
                                <option value="converted" {{ old('status', $quotation->status) === 'converted' ? 'selected' : '' }}>Converted to Job</option>
                                <option value="rejected" {{ old('status', $quotation->status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Authorized Signatory / Branch</label>
                            <div class="signatory-card">
                                <i class="fas fa-user-check text-success fs-5"></i>
                                <div class="text-truncate">
                                    <span class="fw-bold text-dark d-block text-truncate" style="font-size: 0.84rem;">Dr. S. Karuppannan, M.Sc., Ph.D.</span>
                                    <span class="text-muted d-block text-truncate" style="font-size: 0.72rem;">Managing Partner &amp; Recognized Qualified Person (RQP)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Payment Terms & Milestones</label>
                                <div class="d-flex gap-1">
                                    <span class="quick-pill" onclick="setPaymentPreset('50-30-20')">50-30-20</span>
                                    <span class="quick-pill" onclick="setPaymentPreset('50-50')">50-50</span>
                                    <span class="quick-pill" onclick="setPaymentPreset('100')">100% Adv</span>
                                </div>
                            </div>
                            <textarea name="payment_terms" id="payment_terms_textarea" rows="4" class="form-control">{{ old('payment_terms', $quotation->payment_terms) }}</textarea>
                            <div class="form-text small text-muted">Standard 3-stage milestone breakdown per statutory consultancy agreement.</div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0">Statutory Government Exclusions</label>
                                <div class="d-flex gap-1">
                                    <span class="quick-pill" onclick="setExclusionsPreset('standard')">Standard</span>
                                    <span class="quick-pill" onclick="setExclusionsPreset('strict')">Strict Govt Fees</span>
                                </div>
                            </div>
                            <textarea name="exclusions" id="exclusions_textarea" rows="4" class="form-control">{{ old('exclusions', $quotation->exclusions) }}</textarea>
                            <div class="form-text small text-muted">Statutory exclusions payable by client via government treasury challans.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Internal Remarks & Workflow Instructions (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Any special notes or concession history...">{{ old('notes', $quotation->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Elevated Sticky Floating Action Bar -->
            <div class="sticky-floating-bar">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-light text-dark border px-2 py-1 small">
                        <i class="fas fa-file-invoice text-primary me-1"></i>Revised Proposal Total
                    </span>
                    <span class="fw-bold fs-5 text-dark financial-number" id="floatingBarTotal">₹ 0.00</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('accounts.quotations.index') }}" class="btn btn-light border fw-semibold">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-navy px-4 fw-semibold shadow-sm" id="submitQuotationBtn">
                        <i class="fas fa-check-circle me-1"></i> Save & Update Quotation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const customerSelect = document.getElementById('customer_select');
    const concessionSelect = document.getElementById('lease_application_select');
    const itemsTbody = document.getElementById('itemsTbody');
    const btnAddItem = document.getElementById('btnAddItem');
    const taxRateSelect = document.getElementById('tax_rate_select');

    let loadedConcessions = [];
    let rowIndex = {{ count($itemsToLoop) }};

    // Services Catalog Data
    const catalogServices = @json($services);

    // 1. Customer Concession Resolver
    function handleCustomerChange(customerId) {
        if (!customerId) {
            if (concessionSelect) {
                concessionSelect.innerHTML = '<option value="">-- Select Concession or Enter Manually Below --</option>';
            }
            return;
        }

        fetch('{{ url("accounts/quotations/customer-concessions") }}/' + customerId, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.customer) {
                const c = data.customer;
                document.getElementById('customer_name').value = c.customer_name || '';
                document.getElementById('company_name').value = c.company_name || c.customer_name || '';
                document.getElementById('phone').value = c.phone || '';
                document.getElementById('email').value = c.email || '';
                document.getElementById('gst_number').value = c.gst_number || '';
                document.getElementById('address').value = c.address || '';
                if (c.district_id) {
                    document.getElementById('district_id').value = c.district_id;
                }

                // Populate Concessions
                loadedConcessions = data.concessions || [];
                concessionSelect.innerHTML = '<option value="">-- Select Concession or Enter Manually Below --</option>';

                loadedConcessions.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = (c.quarry_name || 'Concession #' + c.id) + ' (' + (c.village ? c.village + ', ' : '') + (c.district_name || '') + ')';
                    concessionSelect.appendChild(opt);
                });

                // If exactly one concession exists, select it automatically
                if (loadedConcessions.length === 1) {
                    concessionSelect.value = loadedConcessions[0].id;
                    fillConcessionData(loadedConcessions[0]);
                }
            }
        })
        .catch(err => console.error('Error fetching concessions:', err));
    }

    // Initialize Select2 on customer dropdown without recursive event triggers
    if (window.jQuery && jQuery.fn.select2 && $('#customer_select').length) {
        $('#customer_select').select2({
            placeholder: '-- Choose Client Profile --',
            allowClear: true,
            width: '100%'
        }).on('select2:select select2:clear', function () {
            handleCustomerChange(this.value);
        });
    }

    // Native fallback if Select2 is not used
    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            if (window.jQuery && jQuery.fn.select2 && $(this).data('select2')) {
                return; // Handled by select2 event
            }
            handleCustomerChange(this.value);
        });
    }

    if (concessionSelect) {
        concessionSelect.addEventListener('change', function () {
            const selectedId = parseInt(this.value);
            const found = loadedConcessions.find(c => c.id === selectedId);
            if (found) {
                fillConcessionData(found);
            }
        });
    }

    function fillConcessionData(concession) {
        document.getElementById('quarry_name').value = concession.quarry_name || '';
        document.getElementById('taluk').value = concession.taluk || '';
        document.getElementById('village').value = concession.village || '';
        document.getElementById('survey_numbers').value = concession.survey_numbers || '';
        document.getElementById('area_extent_ha').value = concession.area_extent_ha || '';
        document.getElementById('mineral_name').value = concession.minerals || concession.mineral_name || '';
        if (concession.district_id) {
            document.getElementById('district_id').value = concession.district_id;
        }
    }

    // 2. Add Service Line Item Row Function
    function addServiceRow(serviceData = null) {
        const sName = serviceData ? escapeHtml(serviceData.service_name) : '';
        const sSac  = serviceData ? escapeHtml(serviceData.sac_code) : '998341';
        const sUnit = serviceData ? escapeHtml(serviceData.unit) : 'Job';
        const sRate = serviceData ? parseFloat(serviceData.unit_rate).toFixed(2) : '0.00';
        const sDesc = serviceData ? escapeHtml(serviceData.description) : '';

        let catalogOptionsHtml = '';
        catalogServices.forEach(s => {
            catalogOptionsHtml += `
                <li>
                    <a class="dropdown-item small py-2 pick-service" href="javascript:void(0);"
                       data-name="${escapeHtml(s.service_name)}"
                       data-sac="${escapeHtml(s.sac_code)}"
                       data-unit="${escapeHtml(s.unit)}"
                       data-rate="${s.unit_rate}"
                       data-desc="${escapeHtml(s.description)}">
                        <strong class="text-dark">${escapeHtml(s.service_name)}</strong>
                        <div class="text-muted" style="font-size:0.74rem;">₹ ${parseFloat(s.unit_rate).toFixed(2)} / ${escapeHtml(s.unit)} &bull; SAC: ${escapeHtml(s.sac_code)}</div>
                    </a>
                </li>
            `;
        });

        const currentCount = itemsTbody.querySelectorAll('.item-main-row').length + 1;

        // Create Main Parameters Row
        const trMain = document.createElement('tr');
        trMain.className = 'item-main-row';
        trMain.setAttribute('data-index', rowIndex);
        trMain.innerHTML = `
            <td class="text-center" rowspan="2" style="vertical-align: middle;">
                <span class="row-num-badge">${currentCount}</span>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="text" name="items[${rowIndex}][service_name]" class="form-control item-service-name fw-bold text-dark" value="${sName}" placeholder="Service Name" required>
                    <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Switch from Catalog"></button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 300px; max-height: 250px; overflow-y: auto;">
                        <li class="dropdown-header small text-uppercase fw-bold text-muted">Select Standard Service</li>
                        ${catalogOptionsHtml}
                    </ul>
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][sac_code]" class="form-control item-sac" value="${sSac}" placeholder="998341">
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="form-control item-qty text-center fw-bold financial-number" value="1" required>
            </td>
            <td>
                <select name="items[${rowIndex}][unit]" class="form-select item-unit">
                    <option value="Ha" ${sUnit === 'Ha' ? 'selected' : ''}>Ha</option>
                    <option value="Nos" ${sUnit === 'Nos' ? 'selected' : ''}>Nos</option>
                    <option value="Job" ${sUnit === 'Job' ? 'selected' : ''}>Job</option>
                    <option value="Survey" ${sUnit === 'Survey' ? 'selected' : ''}>Survey</option>
                    <option value="Year" ${sUnit === 'Year' ? 'selected' : ''}>Year</option>
                    <option value="Month" ${sUnit === 'Month' ? 'selected' : ''}>Month</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_rate]" class="form-control item-rate text-end fw-semibold financial-number" value="${sRate}" required>
            </td>
            <td>
                <input type="hidden" class="item-subtotal-input" value="${sRate}">
                <div class="item-subtotal-box">
                    ₹ <span class="item-subtotal-text ms-1">${formatIndianNumber(sRate)}</span>
                </div>
            </td>
            <td class="text-center" rowspan="2" style="vertical-align: middle;">
                <button type="button" class="btn-row-delete" title="Remove line item">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;

        // Create Description Row
        const trDesc = document.createElement('tr');
        trDesc.className = 'item-desc-row';
        trDesc.setAttribute('data-index', rowIndex);
        trDesc.innerHTML = `
            <td colspan="6">
                <div class="desc-wrapper">
                    <span class="desc-prefix"><i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> Scope &amp; Deliverables:</span>
                    <input type="text" name="items[${rowIndex}][description]" class="desc-input item-desc" value="${sDesc}" placeholder="Technical deliverables, statutory compliance scope, cadastral specifications...">
                </div>
            </td>
        `;

        itemsTbody.appendChild(trMain);
        itemsTbody.appendChild(trDesc);
        rowIndex++;
        updateRowNumbers();
        recalculateTotals();
    }

    function updateRowNumbers() {
        itemsTbody.querySelectorAll('.item-main-row').forEach((row, idx) => {
            const badge = row.querySelector('.row-num-badge');
            if (badge) badge.textContent = idx + 1;
        });
    }

    if (btnAddItem) {
        btnAddItem.addEventListener('click', function () {
            addServiceRow();
        });
    }

    // Quick-Add from Top Catalog Menu
    document.querySelectorAll('.quick-add-service').forEach(btn => {
        btn.addEventListener('click', function () {
            const serviceData = {
                service_name: this.getAttribute('data-name'),
                sac_code: this.getAttribute('data-sac'),
                unit: this.getAttribute('data-unit'),
                unit_rate: this.getAttribute('data-rate'),
                description: this.getAttribute('data-desc')
            };
            addServiceRow(serviceData);
        });
    });

    // 3. Delegate Dynamic Actions (Pick Service, Remove Row, Input Changes)
    itemsTbody.addEventListener('click', function (e) {
        // Remove button
        const removeBtn = e.target.closest('.btn-row-delete');
        if (removeBtn) {
            const mainRows = itemsTbody.querySelectorAll('.item-main-row');
            if (mainRows.length > 1) {
                const mainRow = removeBtn.closest('tr');
                const rowIdx = mainRow.getAttribute('data-index');
                const descRow = itemsTbody.querySelector(`.item-desc-row[data-index="${rowIdx}"]`);
                if (mainRow) mainRow.remove();
                if (descRow) descRow.remove();
                updateRowNumbers();
                recalculateTotals();
            } else {
                alert('At least one statutory service line item is required.');
            }
            return;
        }

        // Pick from catalog dropdown inside table
        const pickItem = e.target.closest('.pick-service');
        if (pickItem) {
            const mainRow = pickItem.closest('.item-main-row');
            const rowIdx = mainRow.getAttribute('data-index');
            const descRow = itemsTbody.querySelector(`.item-desc-row[data-index="${rowIdx}"]`);

            mainRow.querySelector('.item-service-name').value = pickItem.getAttribute('data-name');
            mainRow.querySelector('.item-sac').value = pickItem.getAttribute('data-sac');
            mainRow.querySelector('.item-unit').value = pickItem.getAttribute('data-unit');
            mainRow.querySelector('.item-rate').value = pickItem.getAttribute('data-rate');
            if (descRow) {
                descRow.querySelector('.item-desc').value = pickItem.getAttribute('data-desc');
            }
            recalculateTotals();
        }
    });

    itemsTbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-rate')) {
            recalculateTotals();
        }
    });

    // GST Rate Interactive Pills
    document.querySelectorAll('.gst-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.gst-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const rate = this.getAttribute('data-rate');
            if (taxRateSelect) {
                taxRateSelect.value = rate;
            }
            recalculateTotals();
        });
    });

    // Validity Days Interactive Pills
    document.querySelectorAll('.validity-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.validity-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const days = this.getAttribute('data-days');
            document.getElementById('validity_days_input').value = days;
        });
    });

    // 4. Live Calculation Engine
    function recalculateTotals() {
        let subtotal = 0;
        const mainRows = itemsTbody.querySelectorAll('.item-main-row');

        mainRows.forEach(row => {
            const qtyInput = row.querySelector('.item-qty');
            const rateInput = row.querySelector('.item-rate');
            const subtotalInput = row.querySelector('.item-subtotal-input');
            const subtotalText = row.querySelector('.item-subtotal-text');

            const qty = parseFloat(qtyInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const lineSubtotal = Math.round(qty * rate * 100) / 100;

            subtotal += lineSubtotal;
            if (subtotalInput) {
                subtotalInput.value = lineSubtotal.toFixed(2);
            }
            if (subtotalText) {
                subtotalText.textContent = formatIndianNumber(lineSubtotal.toFixed(2));
            }
        });

        const activeGstPill = document.querySelector('.gst-pill.active');
        const taxRate = parseFloat(activeGstPill ? activeGstPill.getAttribute('data-rate') : (taxRateSelect ? taxRateSelect.value : 18.00)) || 0;
        const taxAmount = Math.round(subtotal * (taxRate / 100) * 100) / 100;
        const grandTotal = subtotal + taxAmount;

        const subtotalFormatted = '₹ ' + formatIndianNumber(subtotal.toFixed(2));
        const taxFormatted = '₹ ' + formatIndianNumber(taxAmount.toFixed(2));
        const totalFormatted = '₹ ' + formatIndianNumber(grandTotal.toFixed(2));

        document.getElementById('displaySubtotal').textContent = subtotalFormatted;
        document.getElementById('displayTaxAmount').textContent = taxFormatted;
        document.getElementById('displayGrandTotal').textContent = totalFormatted;
        document.getElementById('floatingBarTotal').textContent = totalFormatted;

        if (taxRate === 18.00) {
            document.getElementById('displayTaxRateBreakdown').textContent = 'CGST 9% + SGST 9%';
        } else if (taxRate === 0) {
            document.getElementById('displayTaxRateBreakdown').textContent = 'Exempt / Zero Tax';
        } else {
            document.getElementById('displayTaxRateBreakdown').textContent = `GST @ ${taxRate}%`;
        }

        // Update Amount in Words
        document.getElementById('amountInWordsDisplay').textContent = convertNumberToWords(grandTotal);
    }

    function formatIndianNumber(x) {
        x = x.toString();
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

    // P0.3 & P0.4: Form Submit Loading State & Double-Click Prevention
    const quotationForm = document.getElementById('quotationForm');
    const submitBtn = document.getElementById('submitQuotationBtn');
    if (quotationForm && submitBtn) {
        let isSubmitting = false;
        quotationForm.addEventListener('submit', function (e) {
            if (!quotationForm.checkValidity()) {
                return;
            }
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Updating Quotation...';
        });
    }

    // Initial calculation on load
    recalculateTotals();
});

// Quick Helper Functions for Presets
function setPaymentPreset(type) {
    const textarea = document.getElementById('payment_terms_textarea');
    if (type === '50-30-20') {
        textarea.value = "1. 50% Mobilization advance along with confirmed work order.\n2. 30% upon preparation & submission of draft statutory mining / environmental documentation.\n3. 20% upon final statutory clearance, presentation approval & dispatch of statutory order copies.";
    } else if (type === '50-50') {
        textarea.value = "1. 50% Mobilization advance along with confirmed work order.\n2. 50% upon final statutory documentation clearance and dispatch of official government order copies.";
    } else if (type === '100') {
        textarea.value = "1. 100% Full statutory retainer payment along with confirmed work order.";
    }
}

function setExclusionsPreset(type) {
    const textarea = document.getElementById('exclusions_textarea');
    if (type === 'standard') {
        textarea.value = "1. Government statutory scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans.\n2. In-person client representation before statutory committees if required.";
    } else if (type === 'strict') {
        textarea.value = "1. All official government statutory scrutiny fees, district collectorate DMF levies, TNPCB application challans, and SEAC presentation levies are strictly client direct liabilities payable directly through government portals.";
    }
}
</script>
@endpush
@endsection
