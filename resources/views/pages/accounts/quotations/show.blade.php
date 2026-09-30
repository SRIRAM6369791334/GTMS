@extends('layouts.app')

@section('title', 'Quotation ' . $quotation->quotation_number . ' • GTMS')

@section('main_content')
@push('styles')
    @include('pages.accounts.partials.theme')
@endpush

<style>
    /* ─── GTMS SWISS MODERNISM 2.0 / WCAG AAA HIGH-CONTRAST TOKENS ─── */
    .bento-card {
        background: #FFFFFF;
        border-radius: 12px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 10px -2px rgba(15, 23, 42, 0.05), 0 1px 3px rgba(15, 23, 42, 0.03);
        transition: box-shadow 0.2s ease;
    }
    .bento-card:hover {
        box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.08);
    }
    .card-accent-navy { border-top: 3px solid #0F1E4D; }
    .card-accent-blue { border-top: 3px solid #1E3A8A; }
    .card-accent-emerald { border-top: 3px solid #059669; }
    .card-accent-sky { border-top: 3px solid #0284C7; }
    .card-accent-amber { border-top: 3px solid #D97706; }

    /* Metadata Hierarchy */
    .meta-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .meta-value {
        font-size: 0.94rem;
        font-weight: 600;
        color: #0F172A;
        line-height: 1.45;
    }
    .meta-value a {
        color: #1E3A8A;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .meta-value a:hover {
        color: #0F1E4D;
        text-decoration: underline;
    }

    /* Monospace & Value Badges */
    .mono-voucher-badge {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.88rem;
        font-weight: 700;
        color: #0F172A;
        background-color: #F1F5F9;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        padding: 3px 8px;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.02em;
    }
    .sac-code-pill {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.82rem;
        font-weight: 700;
        color: #0F172A !important;
        background-color: #F1F5F9 !important;
        border: 1px solid #CBD5E1 !important;
        border-radius: 5px;
        padding: 3px 8px;
        display: inline-block;
    }

    /* Status Pill with Indicator Dot */
    .status-indicator-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-draft { background-color: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
    .status-draft .status-dot { background-color: #D97706; }
    .status-sent { background-color: #E0F2FE; color: #075985; border: 1px solid #BAE6FD; }
    .status-sent .status-dot { background-color: #0284C7; }
    .status-accepted { background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
    .status-accepted .status-dot { background-color: #059669; }
    .status-converted { background-color: #EDE9FE; color: #5B21B6; border: 1px solid #DDD6FE; }
    .status-converted .status-dot { background-color: #7C3AED; }
    .status-rejected { background-color: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
    .status-rejected .status-dot { background-color: #DC2626; }

    /* Services Table */
    .table-line-items {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-line-items thead th {
        background-color: #F8FAFC;
        color: #1E293B;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-top: none;
        border-bottom: 2px solid #CBD5E1;
        padding: 12px 16px;
        white-space: nowrap;
    }
    .table-line-items tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.88rem;
        color: #1E293B;
        border-bottom: 1px solid #E2E8F0;
    }
    .table-line-items tbody tr:hover td {
        background-color: #F8FAFC;
    }
    .item-iteration-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #F1F5F9;
        color: #475569;
        font-weight: 700;
        font-size: 0.75rem;
        border: 1px solid #CBD5E1;
    }

    /* Endorsement Banner (Amount in Words) */
    .endorsement-box {
        background: linear-gradient(135deg, #F0F7FF 0%, #FFFFFF 100%);
        border: 1px solid #BFDBFE;
        border-left: 4px solid #1E3A8A;
        border-radius: 10px;
        padding: 18px 20px;
        box-shadow: 0 1px 4px rgba(30, 58, 138, 0.04);
    }
    .endorsement-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #1E3A8A;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .endorsement-words {
        font-size: 1.02rem;
        font-weight: 700;
        color: #0F172A;
        font-style: italic;
        line-height: 1.45;
        letter-spacing: 0.01em;
    }

    /* Totals Summary Box */
    .totals-summary-box {
        background-color: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 10px;
        padding: 18px 20px;
    }
    .totals-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 0.86rem;
        color: #475569;
    }
    .totals-row-value {
        font-weight: 600;
        color: #0F172A;
    }
    .totals-divider {
        border-top: 2px dashed #94A3B8;
        margin: 12px 0 10px;
    }
    .totals-grand-label {
        font-size: 0.98rem;
        font-weight: 800;
        color: #0F172A;
    }
    .totals-grand-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0F1E4D;
    }

    /* Milestone Steps List */
    .milestone-step-item {
        display: flex;
        gap: 14px;
        padding: 12px 14px;
        border-radius: 8px;
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        margin-bottom: 10px;
    }
    .milestone-step-badge {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #0284C7;
        color: #FFFFFF;
        font-weight: 700;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);
    }
    .milestone-step-text {
        font-size: 0.88rem;
        color: #1E293B;
        line-height: 1.45;
        font-weight: 500;
    }

    /* Exclusions Alert Callout */
    .exclusions-callout {
        background-color: #FFFBEB;
        border: 1px solid #FDE68A;
        border-left: 4px solid #D97706;
        border-radius: 8px;
        padding: 16px 18px;
    }

    /* Copy Button Micro-interaction */
    .btn-copy-quote {
        background-color: #F1F5F9;
        border: 1px solid #CBD5E1;
        color: #475569;
        padding: 3px 8px;
        border-radius: 5px;
        font-size: 0.78rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .btn-copy-quote:hover {
        background-color: #E2E8F0;
        color: #0F172A;
        border-color: #94A3B8;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <!-- Breadcrumb & Top Executive Action Bar -->
        <div class="row page-titles mb-4">
            <div class="col-lg-6 col-md-12 p-md-0 d-flex align-items-center">
                <div class="welcome-text">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb mb-0 py-0" style="font-size: 0.78rem;">
                            <li class="breadcrumb-item"><a href="{{ route('accounts.quotations.index') }}" class="text-decoration-none text-muted">Accounts</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accounts.quotations.index') }}" class="text-decoration-none text-muted">Quotations</a></li>
                            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">{{ $quotation->quotation_number }}</li>
                        </ol>
                    </nav>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                        <h4 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="fas fa-file-invoice-dollar text-primary"></i>
                            <span>Quotation:</span>
                            <span class="mono-voucher-badge">{{ $quotation->quotation_number }}</span>
                        </h4>

                        <!-- Copy Quotation Number Button -->
                        <button type="button" class="btn btn-copy-quote" onclick="copyQuotationNum('{{ $quotation->quotation_number }}', this)" title="Copy Quotation Number">
                            <i class="fas fa-copy me-1"></i><span>Copy</span>
                        </button>

                        @php
                            $status = $quotation->status ?? 'draft';
                            $statusConfig = match($status) {
                                'sent'      => ['class' => 'status-sent', 'label' => 'Sent to Client'],
                                'accepted'  => ['class' => 'status-accepted', 'label' => 'Accepted / Confirmed'],
                                'converted' => ['class' => 'status-converted', 'label' => 'Converted to Job'],
                                'rejected'  => ['class' => 'status-rejected', 'label' => 'Rejected'],
                                default     => ['class' => 'status-draft', 'label' => 'Draft (Internal Review)'],
                            };
                        @endphp
                        <span class="status-indicator-pill {{ $statusConfig['class'] }}">
                            <span class="status-dot"></span>
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    <!-- Meta Subtitle -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2" style="font-size: 0.82rem; color: #475569;">
                        <span><i class="far fa-calendar-alt me-1 text-primary"></i> Generated: <strong class="text-dark">{{ $quotation->created_at->format('d-M-Y h:i A') }}</strong></span>
                        <span>&bull;</span>
                        <span><i class="fas fa-building me-1 text-primary"></i> Branch: <strong class="text-dark">{{ $quotation->branch?->branch_name ?? 'Head Office (Salem)' }}</strong></span>
                        @if($quotation->creator)
                            <span>&bull;</span>
                            <span><i class="fas fa-user-edit me-1 text-primary"></i> Prepared by: <strong class="text-dark">{{ $quotation->creator->name }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="col-lg-6 col-md-12 p-md-0 justify-content-lg-end mt-3 mt-lg-0 d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('accounts.quotations.index') }}" class="btn btn-light border shadow-sm fw-semibold" style="color: #334155; font-size: 0.84rem;">
                    <i class="fas fa-arrow-left me-1"></i> Back to Directory
                </a>
                <a href="{{ route('accounts.quotations.print', $quotation->id) }}" target="_blank" class="btn btn-navy shadow-sm fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-print me-1"></i> Standalone A4 Print / PDF
                </a>
                @can('account.edit')
                <a href="{{ route('accounts.quotations.edit', $quotation->id) }}" class="btn btn-outline-warning border shadow-sm fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-edit me-1"></i> Edit Quotation
                </a>
                @endcan
            </div>
        </div>

        <!-- Success & Error Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 text-success fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle me-2 text-danger fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Row 1: Bento Profile Snapshots (Client & Quarry Concession) -->
        <div class="row g-4 mb-4">
            <!-- Client & Commercial Profile -->
            <div class="col-lg-6">
                <div class="bento-card card-accent-blue h-100">
                    <div class="py-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center rounded-top">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-building text-primary me-2"></i>Client & Commercial Profile
                        </h6>
                        @if($quotation->customer)
                            <a href="{{ route('customers.show', $quotation->customer->slug ?? $quotation->customer_id) }}" class="small fw-semibold text-decoration-none" style="color: #1E3A8A;">
                                View Full Profile <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                    <div class="p-4 bg-white rounded-bottom">
                        <!-- Company Name Display -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="meta-label">
                                <i class="far fa-id-badge text-primary"></i> Company / Commercial Firm Name
                            </div>
                            <div class="meta-value fs-5 fw-bold" style="color: #0F1E4D;">
                                {{ $quotation->company_name ?: ($quotation->customer?->company_name ?: $quotation->customer_name) }}
                            </div>
                        </div>

                        <!-- Client Metadata Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-user text-primary"></i> Contact Person
                                </div>
                                <div class="meta-value">{{ $quotation->customer_name ?: 'N/A' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-phone-alt text-primary"></i> Contact Phone
                                </div>
                                <div class="meta-value">
                                    @if($quotation->phone)
                                        <a href="tel:{{ $quotation->phone }}">
                                            <i class="fas fa-phone-volume me-1 text-success small"></i>{{ $quotation->phone }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-envelope text-primary"></i> Email Address
                                </div>
                                <div class="meta-value text-break">
                                    @if($quotation->email)
                                        <a href="mailto:{{ $quotation->email }}">
                                            <i class="far fa-envelope-open me-1 text-info small"></i>{{ $quotation->email }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-file-invoice text-primary"></i> Client GSTIN
                                </div>
                                <div class="meta-value">
                                    @if($quotation->gst_number)
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-family: monospace; font-size: 0.82rem;">
                                            <i class="fas fa-check-circle text-success me-1"></i>{{ $quotation->gst_number }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border px-2 py-1">Not Registered</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Registered Billing Address (Crisp High-Contrast Slate) -->
                        <div class="pt-2 border-top">
                            <div class="meta-label">
                                <i class="fas fa-map-marker-alt text-primary"></i> Registered Billing Address
                            </div>
                            <div class="meta-value fw-normal mt-1" style="color: #1E293B; line-height: 1.45;">
                                <i class="fas fa-map-pin me-1 text-secondary opacity-75"></i>
                                {{ $quotation->address ?: 'Concession Mining Area, Tamil Nadu' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quarry Concession Particulars -->
            <div class="col-lg-6">
                <div class="bento-card card-accent-emerald h-100">
                    <div class="py-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center rounded-top">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-mountain text-success me-2"></i>Quarry Concession Snapshot
                        </h6>
                        @if($quotation->leaseApplication)
                            <span class="badge bg-info-subtle text-info border border-info-subtle fw-bold px-2 py-1">
                                <i class="fas fa-link me-1"></i>Ref: {{ $quotation->leaseApplication->application_no ?: 'LA #' . $quotation->leaseApplication->id }}
                            </span>
                        @endif
                    </div>
                    <div class="p-4 bg-white rounded-bottom">
                        <!-- Quarry Concession Title -->
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="meta-label">
                                <i class="fas fa-layer-group text-success"></i> Quarry Concession Title
                            </div>
                            <div class="meta-value fs-5 fw-bold" style="color: #0F172A;">
                                {{ $quotation->quarry_name ?: ($quotation->village ? $quotation->village . ' Quarry' : 'Quarry Concession') }}
                            </div>
                        </div>

                        <!-- Quarry Metadata Grid -->
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-map-signs text-success"></i> Survey Field Nos (S.F. Nos)
                                </div>
                                <div class="meta-value">
                                    @if($quotation->survey_numbers)
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-family: monospace; font-size: 0.85rem;">
                                            <i class="fas fa-map-pin text-primary me-1"></i>{{ $quotation->survey_numbers }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-vector-square text-success"></i> Sanctioned Extent
                                </div>
                                <div class="meta-value">
                                    @if($quotation->area_extent_ha)
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                            <i class="fas fa-chart-area text-success me-1"></i>{{ number_format($quotation->area_extent_ha, 4) }} Ha
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-map-marked-alt text-success"></i> Village & Taluk
                                </div>
                                <div class="meta-value" style="color: #1E293B;">
                                    {{ $quotation->village ?: 'N/A' }}{{ $quotation->taluk ? ', ' . $quotation->taluk : '' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="meta-label">
                                    <i class="fas fa-landmark text-success"></i> Revenue District
                                </div>
                                <div class="meta-value" style="color: #1E293B;">
                                    {{ $quotation->district?->name ?? 'Tamil Nadu' }}
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="meta-label">
                                    <i class="fas fa-gem text-success"></i> Target Mineral / Geological Formation
                                </div>
                                <div class="meta-value fw-semibold" style="color: #0F172A;">
                                    <i class="fas fa-circle-notch text-success small me-1"></i>
                                    {{ $quotation->mineral_name ?: 'Rough Stone / Multi-Coloured Granite' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Statutory Services Scope & Commercial Schedule Table Card -->
        <div class="bento-card card-accent-navy mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="fas fa-list-ol text-primary me-2"></i>Statutory Services Scope & Commercial Schedule
                </h5>
                <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 0.8rem;">
                    <i class="fas fa-cubes text-primary me-1"></i>{{ $quotation->items->count() }} Line {{ \Illuminate\Support\Str::plural('Item', $quotation->items->count()) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-line-items">
                        <thead>
                            <tr>
                                <th style="width: 5%; text-align: center;">#</th>
                                <th style="width: 35%;">Service Description & Statutory Scope</th>
                                <th style="width: 12%; text-align: center;">SAC Code</th>
                                <th style="width: 12%; text-align: center;">Qty / Extent</th>
                                <th style="width: 8%; text-align: center;">Unit</th>
                                <th style="width: 13%; text-align: right;">Unit Rate (₹)</th>
                                <th style="width: 15%; text-align: right;">Subtotal (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($quotation->items as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="item-iteration-pill">{{ $loop->iteration }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $item->service_name }}</div>
                                    @if($item->description)
                                        <div class="text-muted small mt-1" style="font-size: 0.81rem; line-height: 1.4; color: #475569 !important;">
                                            {{ $item->description }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="sac-code-pill">{{ $item->sac_code ?: '998341' }}</span>
                                </td>
                                <td class="text-center fw-bold text-dark financial-number">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="text-center text-muted fw-semibold" style="color: #475569 !important;">
                                    {{ $item->unit ?: 'Nos' }}
                                </td>
                                <td style="text-align: right;" class="text-dark fw-semibold financial-number">
                                    ₹ {{ number_format($item->unit_rate, 2) }}
                                </td>
                                <td style="text-align: right;" class="fw-bold text-dark financial-number fs-6">
                                    ₹ {{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                                    No line items specified for this quotation.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Financial Calculation Summary Footnote -->
                <div class="p-4 bg-white border-top">
                    <div class="row g-4 align-items-center">
                        <!-- Total Quotation Value in Words FinTech Box -->
                        <div class="col-lg-7">
                            <div class="endorsement-box h-100">
                                <div class="endorsement-label">
                                    <i class="fas fa-coins text-warning"></i>
                                    <span>Total Quotation Value in Words (Indian Currency)</span>
                                </div>
                                <div class="endorsement-words">
                                    "{{ $quotation->amount_in_words }}"
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top border-light" style="font-size: 0.78rem; color: #475569;">
                                    <i class="fas fa-shield-alt text-success"></i>
                                    <span>Inclusive of statutory 18.00% Goods & Services Tax (CGST 9% + SGST 9%)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Commercial Summary Totals Breakdown Box -->
                        <div class="col-lg-5">
                            <div class="totals-summary-box">
                                <div class="totals-row">
                                    <span><i class="fas fa-calculator me-1 text-primary"></i> Services Net Subtotal:</span>
                                    <span class="totals-row-value financial-number">₹ {{ number_format($quotation->subtotal, 2) }}</span>
                                </div>
                                <div class="totals-row">
                                    <span><i class="fas fa-percent me-1 text-info"></i> Central Tax (CGST @ 9.00%):</span>
                                    <span class="totals-row-value financial-number">₹ {{ number_format($quotation->tax_amount / 2, 2) }}</span>
                                </div>
                                <div class="totals-row">
                                    <span><i class="fas fa-percent me-1 text-info"></i> State Tax (SGST @ 9.00%):</span>
                                    <span class="totals-row-value financial-number">₹ {{ number_format($quotation->tax_amount / 2, 2) }}</span>
                                </div>
                                <div class="totals-divider"></div>
                                <div class="d-flex justify-content-between align-items-center pt-1">
                                    <span class="totals-grand-label">
                                        <i class="fas fa-receipt me-1 text-primary"></i> Grand Total:
                                    </span>
                                    <span class="totals-grand-value financial-number">
                                        ₹ {{ number_format($quotation->total_amount, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Statutory Milestones, Validity & Exclusions Cards -->
        <div class="row g-4 mb-4">
            <!-- Payment Milestones & Validity -->
            <div class="col-lg-6">
                <div class="bento-card card-accent-sky h-100">
                    <div class="py-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center rounded-top">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-tasks text-info me-2"></i>Milestone Payment Terms & Offer Validity
                        </h6>
                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold" style="font-size: 0.8rem;">
                            <i class="far fa-clock me-1 text-primary"></i>Valid: {{ $quotation->validity_days }} Days
                        </span>
                    </div>
                    <div class="p-4 bg-white rounded-bottom">
                        <div class="meta-label mb-2">
                            <i class="fas fa-stream text-primary"></i> Milestone Payment Schedule
                        </div>

                        @php
                            $termsRaw = $quotation->payment_terms ?: "1. 50% Mobilization advance along with confirmed work order.\n2. 30% upon preparation & submission of draft statutory mining / environmental documentation.\n3. 20% upon final statutory clearance & dispatch of statutory order copies.";
                            $termsLines = array_filter(array_map('trim', explode("\n", $termsRaw)));
                        @endphp

                        <div class="mt-2">
                            @foreach($termsLines as $index => $line)
                                @php
                                    // Strip leading numbering like '1.', '2.' if present
                                    $cleanLine = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                                @endphp
                                <div class="milestone-step-item">
                                    <div class="milestone-step-badge">{{ $index + 1 }}</div>
                                    <div class="milestone-step-text">
                                        {{ $cleanLine }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statutory Exclusions & Compliance Remarks -->
            <div class="col-lg-6">
                <div class="bento-card card-accent-amber h-100">
                    <div class="py-3 px-4 border-bottom bg-white d-flex justify-content-between align-items-center rounded-top">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-shield-alt text-warning me-2"></i>Statutory Exclusions & Remarks
                        </h6>
                    </div>
                    <div class="p-4 bg-white rounded-bottom">
                        <div class="meta-label mb-2">
                            <i class="fas fa-balance-scale text-warning"></i> Government Statutory Exclusions
                        </div>

                        <div class="exclusions-callout mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                                <div class="small fw-semibold" style="color: #0F172A; line-height: 1.5;">
                                    {{ $quotation->exclusions ?: "Government statutory scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans." }}
                                </div>
                            </div>
                        </div>

                        @if($quotation->notes)
                        <div class="pt-2 border-top">
                            <div class="meta-label mb-1">
                                <i class="fas fa-sticky-note text-secondary"></i> Internal Remarks / Workflow Notes
                            </div>
                            <div class="p-3 bg-light rounded border small text-dark fw-medium" style="line-height: 1.45;">
                                {{ $quotation->notes }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Connected Payment Receipts & Collections (FinTech Ledger Integration) -->
        @if($quotation->receipts && $quotation->receipts->count() > 0)
        <div class="bento-card card-accent-navy mb-4">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="fas fa-receipt text-success me-2"></i>Linked Payment Receipts & Collections
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">
                    {{ $quotation->receipts->count() }} Recorded {{ \Illuminate\Support\Str::plural('Receipt', $quotation->receipts->count()) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-line-items mb-0">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Receipt Voucher</th>
                                <th style="width: 15%;">Payment Date</th>
                                <th style="width: 15%;">Payment Mode</th>
                                <th style="width: 20%;">Instrument / Ref No</th>
                                <th style="width: 15%; text-align: right;">Amount (₹)</th>
                                <th style="width: 10%; text-align: center;">Status</th>
                                <th style="width: 10%; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quotation->receipts as $receipt)
                            <tr>
                                <td>
                                    <span class="mono-voucher-badge">{{ $receipt->receipt_number }}</span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    {{ $receipt->payment_date ? \Carbon\Carbon::parse($receipt->payment_date)->format('d-M-Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-uppercase">
                                        {{ $receipt->payment_mode ?: 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    {{ $receipt->reference_number ?: 'N/A' }}
                                </td>
                                <td style="text-align: right;" class="fw-bold text-success financial-number">
                                    ₹ {{ number_format($receipt->amount, 2) }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        Settled
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Receipt Dossier">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    /**
     * Copy Quotation Number to Clipboard with Feedback
     */
    function copyQuotationNum(num, btnElement) {
        if (!navigator.clipboard) {
            // Fallback for non-https or older browser environments
            const tempInput = document.createElement("input");
            tempInput.value = num;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);
            showCopyFeedback(btnElement);
            return;
        }

        navigator.clipboard.writeText(num).then(() => {
            showCopyFeedback(btnElement);
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    }

    function showCopyFeedback(btnElement) {
        const originalHtml = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fas fa-check text-success me-1"></i><span class="text-success fw-bold">Copied!</span>';
        btnElement.classList.add('bg-white', 'border-success');
        
        setTimeout(() => {
            btnElement.innerHTML = originalHtml;
            btnElement.classList.remove('bg-white', 'border-success');
        }, 1800);
    }
</script>
@endpush
@endsection
