@extends('layouts.app')

@section('title', 'Official Money Receipts & Payment Vouchers - GTMS Accounts')

@push('styles')
@include('pages.accounts.partials.theme')
<style>
    .kpi-card {
        border-radius: 12px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }
    .receipts-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        vertical-align: middle;
    }
    .receipts-table td {
        font-size: 0.875rem;
        vertical-align: middle;
    }
    .btn-navy {
        background-color: #0F1E4D;
        color: #ffffff;
        border: none;
    }
    .btn-navy:hover {
        background-color: #1a2f6c;
        color: #ffffff;
    }
    .mode-badge {
        font-size: 0.725rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }
    .mode-cash { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .mode-neft { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .mode-cheque { background-color: #fefce8; color: #a16207; border: 1px solid #fef08a; }
    .mode-upi { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .mode-other { background-color: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
</style>
@endpush

@section('main_content')
<div class="content-body default-height">
<div class="container-fluid py-3 px-4">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small">
                    <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item active text-primary" aria-current="page">Accounts &amp; Financials</li>
                </ol>
            </nav>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="fas fa-receipt text-primary me-2"></i>Official Money Receipts &amp; Vouchers
            </h4>
            <div class="text-muted small">Sequential payment receipt vouchers, cross-application transaction records &amp; audit trails.</div>
        </div>
        <div class="mt-2 mt-sm-0">
            @can('account.create')
            <a href="{{ route('accounts.payments.create') }}" class="btn btn-navy shadow-sm">
                <i class="fas fa-cash-register me-1"></i> Collect Payment Desk
            </a>
            @endcan
            <a href="{{ route('accounts.quotations.index') }}" class="btn btn-outline-primary ms-2 shadow-sm">
                <i class="fas fa-file-invoice me-1"></i> Quotations
            </a>
            <button type="button" class="btn btn-outline-success ms-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#receiptsExportModal">
                <i class="fas fa-file-export me-1"></i> Export
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Collections (All-Time)</div>
                        <h4 class="mb-0 fw-bold text-dark mt-1 financial-number">₹ {{ number_format($totalAmountCollected, 2) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light text-primary p-3">
                        <i class="fas fa-wallet fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Collections This Month (MTD)</div>
                        <h4 class="mb-0 fw-bold text-success mt-1 financial-number">₹ {{ number_format($monthToDateCollected, 2) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light text-success p-3">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-xl-4">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Official Receipts Issued</div>
                        <h4 class="mb-0 fw-bold text-primary mt-1 financial-number">{{ number_format($totalReceiptsCount) }}</h4>
                    </div>
                    <div class="rounded-circle bg-light text-primary p-3">
                        <i class="fas fa-file-invoice-dollar fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="d-md-none mb-2">
                <button class="btn btn-sm btn-outline-secondary w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#receiptsFilterCollapse" aria-expanded="false" aria-controls="receiptsFilterCollapse">
                    <span><i class="fas fa-filter me-1 text-primary"></i> Filter & Search Options</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="collapse d-md-block" id="receiptsFilterCollapse">
                <form method="GET" action="{{ route('accounts.receipts.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1 fw-semibold">Search Keywords</label>
                        <input type="text" name="q" class="form-control form-control-sm"
                               placeholder="Receipt #, UTR, Bank, Customer..." value="{{ request('q', request('search')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1 fw-semibold">Customer</label>
                        <select name="customer_id" id="receipts_customer_id" class="form-select form-select-sm select2-customer">
                            <option value="">-- All Clients --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->company_name ?: $c->customer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">Payment Mode</label>
                        <select name="payment_mode" class="form-select form-select-sm">
                            <option value="">-- All Modes --</option>
                            <option value="Cash" {{ request('payment_mode') == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="NEFT/RTGS" {{ request('payment_mode') == 'NEFT/RTGS' ? 'selected' : '' }}>NEFT / RTGS</option>
                            <option value="Cheque" {{ request('payment_mode') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="UPI/GPay" {{ request('payment_mode') == 'UPI/GPay' ? 'selected' : '' }}>UPI / GPay</option>
                            <option value="Other" {{ request('payment_mode') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">From Date</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', request('date_from')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">To Date</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', request('date_to')) }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                        <button type="submit" class="btn btn-sm btn-navy">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        @if(request()->anyFilled(['q', 'search', 'customer_id', 'payment_mode', 'application_type', 'start_date', 'end_date', 'date_from', 'date_to']))
                            <a href="{{ route('accounts.receipts.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Filter Chips (P0.5) -->
    @include('pages.accounts.partials.filter_chips', ['route' => 'accounts.receipts.index', 'customers' => $customers])

    <!-- Receipts Table Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-list text-primary me-2"></i>Receipt Vouchers Ledger
            </h5>
            <span class="badge bg-light text-muted border">{{ $receipts->total() }} Records</span>
        </div>
            <div class="table-responsive d-none d-md-block">
                <table class="table receipts-table table-sticky align-middle mb-0 table-hover">
                    <thead>
                        <tr>
                            <th style="width: 14%;">Receipt Voucher #</th>
                            <th style="width: 10%;">Date</th>
                            <th style="width: 22%;">Client / Concession</th>
                            <th style="width: 16%;">Statutory Service Ref</th>
                            <th style="width: 12%;">Mode &amp; Ref</th>
                            <th style="width: 12%; text-align: right;">Amount Paid (₹)</th>
                            <th style="width: 14%; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            @php
                                $modeClass = match($receipt->payment_mode) {
                                    'Cash'      => 'mode-cash',
                                    'NEFT/RTGS' => 'mode-neft',
                                    'Cheque'    => 'mode-cheque',
                                    'UPI/GPay'  => 'mode-upi',
                                    default     => 'mode-other'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="fw-bold text-primary text-decoration-none">
                                        {{ $receipt->receipt_number }}
                                    </a>
                                    <div class="small text-muted" style="font-size: 0.725rem;">
                                        By {{ $receipt->creator->name ?? 'System' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold">
                                        {{ $receipt->transaction_date ? $receipt->transaction_date->format('d M Y') : '-' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $receipt->customer->company_name ?: ($receipt->customer->customer_name ?? 'N/A') }}
                                    </div>
                                    <div class="small text-muted">
                                        @if($receipt->customer?->customer_name && $receipt->customer?->company_name)
                                             {{ $receipt->customer->customer_name }} &bull;
                                        @endif
                                        {{ $receipt->customer->mobile_num ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-light text-dark border small fw-normal">
                                            {{ ucfirst(str_replace('_', ' ', $receipt->application_type ?: 'General')) }}
                                        </span>
                                        @if(isset($installmentMap[$receipt->id]))
                                            <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.68rem;" title="{{ $installmentMap[$receipt->id]['label'] }}">
                                                <i class="fas fa-layer-group me-1"></i>Inst. {{ $installmentMap[$receipt->id]['installment'] }} of {{ $installmentMap[$receipt->id]['total'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold text-secondary small mt-1 text-truncate" style="max-width: 180px;" title="{{ $receipt->application_reference }}">
                                        {{ $receipt->application_reference }}
                                    </div>
                                </td>
                                <td>
                                    <span class="mode-badge {{ $modeClass }}">
                                        {{ $receipt->payment_mode }}
                                    </span>
                                    @if($receipt->reference_number)
                                        <div class="small text-muted mt-1 text-truncate" style="max-width: 140px;" title="{{ $receipt->reference_number }}">
                                            Ref: {{ $receipt->reference_number }}
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end financial-number">
                                    <div class="fw-bold text-success fs-6">
                                        ₹ {{ number_format($receipt->amount_paid, 2) }}
                                    </div>
                                    @if($receipt->balance_due > 0)
                                        <span class="badge bg-danger-subtle text-danger financial-number" style="font-size: 0.7rem;">
                                            Due: ₹ {{ number_format($receipt->balance_due, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-success-subtle text-success" style="font-size: 0.7rem;">
                                            Settled
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($receipt->application_type && $receipt->application_id)
                                            <button type="button" class="btn btn-outline-info btn-view-app-history"
                                                    data-type="{{ $receipt->application_type }}"
                                                    data-id="{{ $receipt->application_id }}"
                                                    data-ref="{{ $receipt->application_reference }}"
                                                    data-client="{{ $receipt->customer->company_name ?: ($receipt->customer->customer_name ?? 'Client') }}"
                                                    title="View All Installments & Receipts for this Application">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-outline-secondary" title="View Voucher Overview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <div class="btn-group">
                                            <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank" class="btn btn-outline-primary" title="Print Tax Invoice (TI)">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Choose Invoice Format">
                                                <span class="visually-hidden">Toggle</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li>
                                                    <a class="dropdown-item py-1" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank">
                                                        <i class="fas fa-receipt text-success me-2"></i> Tax Invoice (TI)
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-1" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'proforma']) }}" target="_blank">
                                                        <i class="fas fa-file-alt text-warning me-2"></i> Proforma Invoice (PI)
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-0">
                                    @include('pages.accounts.partials.empty_state', [
                                        'icon' => 'fas fa-receipt',
                                        'title' => 'No Payment Receipts Found',
                                        'desc' => 'No money receipts match the selected filters or search keywords.',
                                        'resetRoute' => route('accounts.receipts.index'),
                                        'createRoute' => route('accounts.payments.create'),
                                        'createLabel' => 'Collect First Payment',
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
                @forelse($receipts as $receipt)
                    <div class="account-mobile-card">
                        <div class="account-mobile-card-header">
                            <div>
                                <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $receipt->receipt_number }}
                                </a>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    <i class="far fa-calendar-alt me-1"></i>{{ $receipt->transaction_date ? $receipt->transaction_date->format('d M Y') : '-' }} &bull; {{ $receipt->creator->name ?? 'System' }}
                                </div>
                            </div>
                            @php
                                $modeBadge = match($receipt->payment_mode) {
                                    'Cash' => 'bg-success-subtle text-success border border-success',
                                    'NEFT/RTGS' => 'bg-primary-subtle text-primary border border-primary',
                                    'Cheque' => 'bg-warning-subtle text-warning-emphasis border border-warning',
                                    'UPI/GPay' => 'bg-info-subtle text-info border border-info',
                                    default => 'bg-secondary-subtle text-secondary'
                                };
                            @endphp
                            <span class="badge {{ $modeBadge }}">{{ $receipt->payment_mode }}</span>
                        </div>
                        <div class="account-mobile-card-row">
                            <span class="account-mobile-card-label">Client / Firm:</span>
                            <span class="account-mobile-card-value text-truncate" style="max-width:210px;">{{ $receipt->customer->company_name ?: ($receipt->customer->customer_name ?? 'N/A') }}</span>
                        </div>
                        @if($receipt->application_type)
                        <div class="account-mobile-card-row">
                            <span class="account-mobile-card-label">Statutory Service:</span>
                            <span class="account-mobile-card-value text-truncate" style="max-width:210px;">
                                {{ ucfirst(str_replace('_', ' ', $receipt->application_type)) }}
                                @if(isset($installmentMap[$receipt->id]))
                                    <span class="badge bg-primary-subtle text-primary border ms-1" style="font-size:0.68rem;">
                                        Inst. {{ $installmentMap[$receipt->id]['installment'] }}/{{ $installmentMap[$receipt->id]['total'] }}
                                    </span>
                                @endif
                            </span>
                        </div>
                        @endif
                        <div class="account-mobile-card-row">
                            <span class="account-mobile-card-label">Amount Paid:</span>
                            <span class="account-mobile-card-value text-success financial-number fs-6">₹ {{ number_format($receipt->amount_paid, 2) }}</span>
                        </div>
                        @if($receipt->balance_due > 0)
                        <div class="account-mobile-card-row">
                            <span class="account-mobile-card-label">Balance Due:</span>
                            <span class="account-mobile-card-value text-danger financial-number small">₹ {{ number_format($receipt->balance_due, 2) }}</span>
                        </div>
                        @endif
                        <div class="account-mobile-card-actions">
                            @if($receipt->application_type && $receipt->application_id)
                                <button type="button" class="btn btn-sm btn-outline-info btn-view-app-history"
                                        data-type="{{ $receipt->application_type }}"
                                        data-id="{{ $receipt->application_id }}"
                                        data-ref="{{ $receipt->application_reference }}"
                                        data-client="{{ $receipt->customer->company_name ?: ($receipt->customer->customer_name ?? 'Client') }}">
                                    <i class="fas fa-history me-1"></i> History
                                </button>
                            @endif
                            <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <div class="btn-group">
                                <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-print me-1"></i> Print
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Choose Invoice Format">
                                    <span class="visually-hidden">Toggle</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item py-1" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank">
                                            <i class="fas fa-receipt text-success me-2"></i> Tax Invoice (TI)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-1" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'proforma']) }}" target="_blank">
                                            <i class="fas fa-file-alt text-warning me-2"></i> Proforma Invoice (PI)
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    @include('pages.accounts.partials.empty_state', [
                        'icon' => 'fas fa-receipt',
                        'title' => 'No Payment Receipts Found',
                        'desc' => 'No money receipts match the selected filters or search keywords.',
                        'resetRoute' => route('accounts.receipts.index'),
                        'createRoute' => route('accounts.payments.create'),
                        'createLabel' => 'Collect First Payment',
                        'createPermission' => 'account.create'
                    ])
                @endforelse
            </div>

            <!-- Pagination Bar -->
            @if($receipts->hasPages())
                <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Showing {{ $receipts->firstItem() }} to {{ $receipts->lastItem() }} of {{ $receipts->total() }} receipts
                    </div>
                    <div>
                        {{ $receipts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     APPLICATION PAYMENT & INSTALLMENTS HISTORY MODAL
═══════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="appPaymentHistoryModal" tabindex="-1" aria-labelledby="appHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header border-0 py-3 px-4" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%); color: #FFFFFF;">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="appHistoryModalLabel" style="color: #FFFFFF !important;">
                        <i class="fas fa-layer-group text-warning me-2"></i> Application Installments &amp; Receipt History
                    </h5>
                    <div class="small opacity-75 mt-0.5 text-white-50" id="modal_app_subtitle">Loading application ledger...</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Financial Summary Strip --}}
                <div class="row g-2 mb-3 p-3 rounded-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Total Agreed Value</div>
                        <div class="fw-bold fs-6 text-dark" id="modal_hist_val">₹ 0.00</div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Total Received</div>
                        <div class="fw-bold fs-6 text-success" id="modal_hist_paid">₹ 0.00</div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Balance Due</div>
                        <div class="fw-bold fs-6 text-danger" id="modal_hist_balance">₹ 0.00</div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Settlement Status</div>
                        <div id="modal_hist_status"><span class="badge bg-secondary">Loading</span></div>
                    </div>
                </div>

                {{-- Loader --}}
                <div id="modal_hist_loader" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="small ms-2 fw-semibold text-muted">Retrieving statutory installment records...</span>
                </div>

                {{-- Installments Table --}}
                <div id="modal_hist_table_wrap" style="display: none; max-height: 280px; overflow-y: auto;">
                    <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                        <thead style="background: #F1F5F9; color: #475569; font-size: 0.72rem; text-transform: uppercase;">
                            <tr>
                                <th class="ps-2 py-2">Installment #</th>
                                <th>Date</th>
                                <th>Receipt Voucher #</th>
                                <th>Mode &amp; Ref</th>
                                <th class="text-end">Amount Paid</th>
                                <th class="text-end">Balance After</th>
                                <th class="text-center pe-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="modal_hist_tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex justify-content-between align-items-center">
                <div class="small text-muted" id="modal_hist_footer_count">0 Installment(s) found</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal">Close</button>
                    @can('account.create')
                    <a href="{{ route('accounts.payments.create') }}" class="btn btn-navy btn-sm" id="modal_hist_collect_btn">
                        <i class="fas fa-plus me-1"></i> Collect Next Installment
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.accounts.partials.export_modal', [
    'id' => 'receiptsExportModal',
    'title' => 'Official Money Receipts Ledger',
    'tableSelector' => 'table.receipts-table',
    'filename' => 'GTMS_Payment_Receipts_' . date('Ymd')
])
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.select2 && $('#receipts_customer_id').length) {
            $('#receipts_customer_id').select2({
                placeholder: '-- All Clients --',
                allowClear: true,
                width: '100%'
            });
        }

        function formatInr(val) {
            return Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Handle Application Payment History Modal Trigger
        const appModalEl = document.getElementById('appPaymentHistoryModal');
        const appModal = appModalEl ? new bootstrap.Modal(appModalEl) : null;

        document.querySelectorAll('.btn-view-app-history').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const appType   = this.getAttribute('data-type');
                const appId     = this.getAttribute('data-id');
                const appRef    = this.getAttribute('data-ref') || 'Application';
                const clientName= this.getAttribute('data-client') || 'Client';

                if (!appType || !appId || !appModal) return;

                document.getElementById('modal_app_subtitle').textContent = clientName + ' • ' + appRef;
                document.getElementById('modal_hist_loader').style.display = 'block';
                document.getElementById('modal_hist_table_wrap').style.display = 'none';
                document.getElementById('modal_hist_tbody').innerHTML = '';

                appModal.show();

                const url = '{{ url("accounts/receipts/application-history") }}/' + encodeURIComponent(appType) + '/' + encodeURIComponent(appId);
                fetch(url, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('modal_hist_loader').style.display = 'none';
                    if (!data.success) {
                        alert('Could not load application payment history.');
                        return;
                    }

                    const app = data.application;
                    document.getElementById('modal_hist_val').textContent     = '₹ ' + formatInr(app.product_value);
                    document.getElementById('modal_hist_paid').textContent    = '₹ ' + formatInr(app.total_paid);
                    document.getElementById('modal_hist_balance').textContent = '₹ ' + formatInr(app.pending_amount);

                    let statusBadge = '<span class="badge bg-warning-subtle text-warning-emphasis border">Pending</span>';
                    if (app.payment_status === 'paid' || app.pending_amount <= 0) {
                        statusBadge = '<span class="badge bg-success-subtle text-success border">Settled</span>';
                    } else if (app.payment_status === 'partial') {
                        statusBadge = '<span class="badge bg-primary-subtle text-primary border">Partial</span>';
                    }
                    document.getElementById('modal_hist_status').innerHTML = statusBadge;
                    document.getElementById('modal_hist_footer_count').textContent = data.installments.length + ' Installment Receipt(s) recorded';

                    const collectBtn = document.getElementById('modal_hist_collect_btn');
                    if (collectBtn) {
                        collectBtn.href = '{{ route("accounts.payments.create") }}?application_type=' + encodeURIComponent(app.type) + '&application_id=' + encodeURIComponent(app.id);
                    }

                    const tbody = document.getElementById('modal_hist_tbody');
                    tbody.innerHTML = '';
                    if (data.installments.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-3 text-muted">No payment records found.</td></tr>';
                    } else {
                        data.installments.forEach(inst => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="ps-2 py-2 fw-bold text-dark">
                                    <span class="badge bg-light text-dark border">${inst.installment_label}</span>
                                </td>
                                <td>${inst.formatted_date}</td>
                                <td>
                                    <a href="${inst.view_url}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                        <i class="fas fa-receipt me-1"></i>${inst.receipt_number}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border">${inst.payment_mode}</span>
                                    ${inst.reference_number ? `<br><small class="text-muted font-monospace" style="font-size:0.68rem;">Ref: ${inst.reference_number}</small>` : ''}
                                </td>
                                <td class="text-end fw-bold text-success">₹ ${formatInr(inst.amount_paid)}</td>
                                <td class="text-end ${inst.balance_due > 0 ? 'text-danger fw-semibold' : 'text-success fw-bold'}">
                                    ${inst.balance_due > 0 ? '₹ ' + formatInr(inst.balance_due) : '<span class="badge bg-success-subtle text-success">Settled</span>'}
                                </td>
                                <td class="text-center pe-2">
                                    <div class="btn-group btn-group-sm">
                                        <a href="${inst.view_url}" target="_blank" class="btn btn-outline-secondary py-0 px-2" style="font-size: 0.72rem; height: 24px;" title="View Voucher">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="${inst.print_url}" target="_blank" class="btn btn-outline-primary py-0 px-2" style="font-size: 0.72rem; height: 24px;" title="Print Voucher">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                    document.getElementById('modal_hist_table_wrap').style.display = 'block';
                })
                .catch(err => {
                    document.getElementById('modal_hist_loader').style.display = 'none';
                    console.error('Failed to load application history:', err);
                    alert('Network error while retrieving application history.');
                });
            });
        });
    });
</script>
@endpush
