@extends('layouts.app')

@section('title', 'Financial Reports & Transaction Reconciliation • GTMS Accounts')

@push('styles')
@include('pages.accounts.partials.theme')
<style>
    .report-table thead th {
        background-color: #F8FAFC;
        color: #334155;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--ct-border);
        padding: 12px 14px;
    }
    .report-table tbody td {
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .badge-mode {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 600;
    }
    .badge-cash { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .badge-neft { background-color: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-cheque { background-color: #fefce8; color: #a16207; border: 1px solid #fef08a; }
    .badge-upi { background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .badge-other { background-color: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; }
</style>
@endpush

@section('main_content')

<div class="content-body">
    <div class="container-fluid">
        <!-- Page Title & Top Actions -->
        <div class="row page-titles mb-4">
            <div class="col-sm-6 p-md-0 d-flex align-items-center">
                <div class="welcome-text">
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-chart-line text-primary me-2"></i>Financial Reports &amp; Reconciliation
                    </h4>
                    <span class="text-muted small">Centralized financial dashboard, multi-parametric statutory filtering &amp; audit-ready CSV exports.</span>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
                <a href="{{ route('accounts.reports.export-csv', request()->all()) }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-file-csv me-1"></i> Export Streamed CSV
                </a>
                @can('account.create')
                <a href="{{ route('accounts.payments.create') }}" class="btn btn-navy shadow-sm">
                    <i class="fas fa-cash-register me-1"></i> Collect Payment
                </a>
                @endcan
                <a href="{{ route('accounts.ledger.index') }}" class="btn btn-outline-primary shadow-sm">
                    <i class="fas fa-book-open me-1"></i> Customer Ledger
                </a>
            </div>
        </div>

        <!-- KPI Summary Cards Grid -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0F1E4D 0%, #1A327E 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Collected (All-Time)</div>
                                <div class="fs-4 fw-bold mt-1 text-white financial-number">&#8377; {{ number_format($kpis['total_collected'], 2) }}</div>
                                <div class="small text-white-50 mt-1">Total verified revenue</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.12); width: 50px; height: 50px;">
                                <i class="fas fa-coins fs-4 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid var(--ct-accent-emerald) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Month-to-Date (MTD)</div>
                                <div class="fs-4 fw-bold mt-1 text-success financial-number">&#8377; {{ number_format($kpis['mtd_collected'], 2) }}</div>
                                <div class="small text-muted mt-1">Collections for {{ date('F Y') }}</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; width: 50px; height: 50px;">
                                <i class="fas fa-calendar-check fs-4 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid var(--ct-accent-red) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Outstanding Receivables</div>
                                <div class="fs-4 fw-bold mt-1 text-danger financial-number">&#8377; {{ number_format($kpis['total_outstanding'], 2) }}</div>
                                <div class="small text-muted mt-1">Pending statutory dues</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #fef2f2; width: 50px; height: 50px;">
                                <i class="fas fa-hand-holding-usd fs-4 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid var(--ct-accent-blue) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Quotations Pipeline</div>
                                <div class="fs-4 fw-bold mt-1 text-primary financial-number">&#8377; {{ number_format($kpis['quotations_value'], 2) }}</div>
                                <div class="small text-muted mt-1">{{ $kpis['quotations_count'] }} quotations issued</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #eff6ff; width: 50px; height: 50px;">
                                <i class="fas fa-file-invoice fs-4 text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Realization & Collection Efficiency Progress (P3.2) -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                @php
                    $totalDemand = (float) $kpis['total_collected'] + (float) $kpis['total_outstanding'];
                    $realizationPct = $totalDemand > 0 ? min(100, round(($kpis['total_collected'] / $totalDemand) * 100, 1)) : 100;
                @endphp
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                    <div>
                        <span class="fw-bold text-dark small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="fas fa-chart-pie text-primary me-1"></i> Revenue Realization &amp; Collection Efficiency
                        </span>
                        <span class="badge bg-success-subtle text-success ms-2">{{ $realizationPct }}% Realized</span>
                    </div>
                    <div class="small fw-semibold">
                        <span class="text-success me-3"><i class="fas fa-circle fa-xs me-1"></i> Collected: ₹ {{ number_format($kpis['total_collected'], 2) }}</span>
                        <span class="text-danger"><i class="fas fa-circle fa-xs me-1"></i> Outstanding: ₹ {{ number_format($kpis['total_outstanding'], 2) }}</span>
                    </div>
                </div>
                <div class="progress" style="height: 10px; border-radius: 6px; background-color: #FEE2E2;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" role="progressbar"
                         style="width: {{ $realizationPct }}%; border-radius: 6px;" aria-valuenow="{{ $realizationPct }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>

        <!-- Multi-Parametric Filter Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="fas fa-sliders-h text-primary me-2"></i>Multi-Parametric Transaction Filters
                </h6>
                <div class="d-flex gap-2">
                    <div class="d-md-none">
                        <button class="btn btn-xs btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#reportsFilterCollapse" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Filters
                        </button>
                    </div>
                    @if(request()->hasAny(['from_date', 'to_date', 'start_date', 'end_date', 'customer_id', 'application_type', 'payment_mode', 'search', 'q']))
                        <a href="{{ route('accounts.reports.index') }}" class="btn btn-xs btn-outline-secondary">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                    @endif
                </div>
            </div>
            <div class="collapse d-md-block" id="reportsFilterCollapse">
                <div class="card-body p-4">
                    <form action="{{ route('accounts.reports.index') }}" method="GET" class="row g-3">
                        <!-- Date Range -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted">From Date</label>
                            <input type="date" name="from_date" class="form-control form-control-sm" value="{{ request('from_date', request('start_date')) }}">
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted">To Date</label>
                            <input type="date" name="to_date" class="form-control form-control-sm" value="{{ request('to_date', request('end_date')) }}">
                        </div>

                        <!-- Customer Dropdown -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted">Customer / Entity</label>
                            <select name="customer_id" id="reports_customer_id" class="form-select form-select-sm select2-customer">
                                <option value="">All Customers</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->company_name ?: $c->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Application Type -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted">Statutory Module</label>
                            <select name="application_type" class="form-select form-select-sm">
                                <option value="">All Modules</option>
                                <option value="lease" {{ request('application_type') === 'lease' ? 'selected' : '' }}>Lease Applications</option>
                                <option value="mining" {{ request('application_type') === 'mining' ? 'selected' : '' }}>Mining Plans</option>
                                <option value="environment" {{ request('application_type') === 'environment' ? 'selected' : '' }}>Environmental Clearance</option>
                                <option value="ppt" {{ request('application_type') === 'ppt' ? 'selected' : '' }}>PPT Department</option>
                                <option value="dgps" {{ request('application_type') === 'dgps' ? 'selected' : '' }}>DGPS Survey</option>
                                <option value="drone" {{ request('application_type') === 'drone' ? 'selected' : '' }}>Drone Survey</option>
                                <option value="ec_certificate" {{ request('application_type') === 'ec_certificate' ? 'selected' : '' }}>EC Certificates</option>
                                <option value="ec_compliance" {{ request('application_type') === 'ec_compliance' ? 'selected' : '' }}>EC Compliance</option>
                            </select>
                        </div>

                        <!-- Payment Mode -->
                        <div class="col-md-3 col-sm-6">
                            <label class="form-label small fw-bold text-muted">Payment Mode</label>
                            <select name="payment_mode" class="form-select form-select-sm">
                                <option value="">All Payment Modes</option>
                                <option value="Cash" {{ request('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="Cheque" {{ request('payment_mode') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="NEFT/RTGS" {{ request('payment_mode') === 'NEFT/RTGS' ? 'selected' : '' }}>NEFT / RTGS</option>
                                <option value="UPI/GPay" {{ request('payment_mode') === 'UPI/GPay' ? 'selected' : '' }}>UPI / GPay</option>
                                <option value="Other" {{ request('payment_mode') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Keyword Search -->
                        <div class="col-md-6 col-sm-6">
                            <label class="form-label small fw-bold text-muted">Keyword Search</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by receipt no, UTR, cheque ref, bank name, or customer name..." value="{{ request('search', request('q')) }}">
                        </div>

                        <!-- Filter Actions -->
                        <div class="col-md-3 col-sm-12 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-sm btn-navy flex-grow-1">
                                <i class="fas fa-filter me-1"></i> Apply Filters
                            </button>
                            <a href="{{ route('accounts.reports.export-csv', request()->all()) }}" class="btn btn-sm btn-outline-success" title="Export Filtered CSV">
                                <i class="fas fa-file-csv"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Active Filter Chips (P0.5) -->
        @include('pages.accounts.partials.filter_chips', ['route' => 'accounts.reports.index', 'customers' => $customers])

        <!-- Filtered Transactions Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-receipt text-primary me-2"></i>Payment Collection Audit Trail
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2">
                    Showing {{ $receipts->firstItem() ?? 0 }} to {{ $receipts->lastItem() ?? 0 }} of {{ $receipts->total() }} Records
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-sticky report-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Receipt No</th>
                                <th style="width: 105px;">Date</th>
                                <th>Customer &amp; Company</th>
                                <th>Module &amp; Reference</th>
                                <th>Payment Mode &amp; Ref</th>
                                <th class="text-end" style="width: 130px;">Amount Paid</th>
                                <th class="text-end" style="width: 130px;">Balance Due</th>
                                <th>Recorded By</th>
                                <th class="text-center" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($receipts as $receipt)
                                @php
                                    $modeClass = match(strtolower($receipt->payment_mode ?? '')) {
                                        'cash' => 'badge-cash',
                                        'neft/rtgs', 'neft', 'rtgs' => 'badge-neft',
                                        'cheque' => 'badge-cheque',
                                        'upi/gpay', 'upi' => 'badge-upi',
                                        default => 'badge-other',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                            {{ $receipt->receipt_number }}
                                        </a>
                                    </td>
                                    <td class="text-muted fw-semibold">
                                        {{ $receipt->transaction_date ? $receipt->transaction_date->format('d-M-Y') : ($receipt->created_at ? $receipt->created_at->format('d-M-Y') : '—') }}
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            @if($receipt->customer)
                                                <a href="{{ route('accounts.ledger.show', $receipt->customer->slug ?: $receipt->customer->id) }}" class="text-dark text-decoration-none">
                                                    {{ $receipt->customer->company_name ?: $receipt->customer->customer_name }}
                                                </a>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </div>
                                        @if($receipt->customer?->customer_name && $receipt->customer?->company_name)
                                            <div class="small text-muted">{{ $receipt->customer->customer_name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border mb-1">
                                            {{ $receipt->application_type ? ucfirst(str_replace('_', ' ', $receipt->application_type)) : 'General' }}
                                        </span>
                                        <div class="small font-monospace text-muted">{{ $receipt->application_reference }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-mode {{ $modeClass }} mb-1 d-inline-block">
                                            {{ $receipt->payment_mode ?? 'Direct' }}
                                        </span>
                                        @if($receipt->reference_number)
                                            <div class="small font-monospace text-muted">Ref: {{ $receipt->reference_number }}</div>
                                        @endif
                                        @if($receipt->bank_name)
                                            <div class="small text-muted">{{ $receipt->bank_name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6 financial-number">
                                        &#8377; {{ number_format((float) $receipt->amount_paid, 2) }}
                                    </td>
                                    <td class="text-end fw-semibold financial-number {{ $receipt->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                                        {{ $receipt->balance_due > 0 ? '₹ ' . number_format((float) $receipt->balance_due, 2) : 'Settled' }}
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $receipt->creator?->name ?? 'System' }}</div>
                                        @if($receipt->branch)
                                            <div class="small text-muted">{{ $receipt->branch->name ?? $receipt->branch->branch_name }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Receipt">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-outline-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-0">
                                        @include('pages.accounts.partials.empty_state', [
                                            'icon' => 'fas fa-receipt',
                                            'title' => 'No Financial Transactions Found',
                                            'desc' => 'No payment receipts match your current filter criteria.',
                                            'resetRoute' => route('accounts.reports.index')
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
                                        {{ $receipt->transaction_date ? $receipt->transaction_date->format('d-M-Y') : ($receipt->created_at ? $receipt->created_at->format('d-M-Y') : '—') }} &bull; {{ $receipt->creator?->name ?? 'System' }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border">{{ $receipt->payment_mode ?? 'Direct' }}</span>
                            </div>
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Customer:</span>
                                <span class="account-mobile-card-value text-truncate" style="max-width:210px;">{{ $receipt->customer ? ($receipt->customer->company_name ?: $receipt->customer->customer_name) : 'N/A' }}</span>
                            </div>
                            @if($receipt->application_type)
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Module:</span>
                                <span class="account-mobile-card-value text-truncate" style="max-width:210px;">{{ ucfirst(str_replace('_', ' ', $receipt->application_type)) }}</span>
                            </div>
                            @endif
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Amount Paid:</span>
                                <span class="account-mobile-card-value text-success financial-number fs-6">₹ {{ number_format((float) $receipt->amount_paid, 2) }}</span>
                            </div>
                            @if($receipt->balance_due > 0)
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Balance Due:</span>
                                <span class="account-mobile-card-value text-danger financial-number small">₹ {{ number_format((float) $receipt->balance_due, 2) }}</span>
                            </div>
                            @endif
                            <div class="account-mobile-card-actions">
                                <a href="{{ route('accounts.receipts.show', $receipt->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-print me-1"></i> Print
                                </a>
                            </div>
                        </div>
                    @empty
                        @include('pages.accounts.partials.empty_state', [
                            'icon' => 'fas fa-receipt',
                            'title' => 'No Financial Transactions Found',
                            'desc' => 'No payment receipts match your current filter criteria.',
                            'resetRoute' => route('accounts.reports.index')
                        ])
                    @endforelse
                </div>
            </div>
            @if($receipts->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Page {{ $receipts->currentPage() }} of {{ $receipts->lastPage() }}
                    </div>
                    <div>
                        {{ $receipts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.select2 && $('#reports_customer_id').length) {
            $('#reports_customer_id').select2({
                placeholder: 'All Customers',
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>
@endpush
