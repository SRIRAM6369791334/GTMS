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
                                    <span class="badge bg-light text-dark border small fw-normal">
                                        {{ ucfirst($receipt->application_type ?: 'General') }}
                                    </span>
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
                                        <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-outline-secondary" title="View Voucher Overview">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-outline-primary" title="Print Official Voucher (A4/A5)">
                                            <i class="fas fa-print"></i>
                                        </a>
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
                            <span class="account-mobile-card-value text-truncate" style="max-width:210px;">{{ ucfirst(str_replace('_', ' ', $receipt->application_type)) }}</span>
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
                            <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-eye me-1"></i> View
                            </a>
                            <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-print me-1"></i> Print
                            </a>
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
    });
</script>
@endpush
