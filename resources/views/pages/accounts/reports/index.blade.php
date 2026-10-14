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
                <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#reportsExportModal">
                    <i class="fas fa-file-export me-1"></i> Export Report
                </button>
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

        <!-- Visual Analytics & Multi-Dimensional Comparison Suite -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-chart-bar text-primary me-2"></i>Financial Visual Analytics &amp; Comparison Suite
                    </h5>
                    <span class="text-muted small">Interactive bar charts across time periods (weekly, yearly) and multi-dimensional comparisons (district, collector, module).</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                        <i class="fas fa-bolt me-1"></i> Real-time Aggregation
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn_toggle_analytics_view" title="Toggle Compact vs Expanded Studio">
                        <i class="fas fa-expand-arrows-alt me-1"></i> <span id="btn_toggle_analytics_text">Expanded Studio</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-4" style="background: #FAFCFF;">
                <!-- ROW 1: TIME-SERIES VELOCITY (Week-wise & Year-on-Year YoY) -->
                <div class="row g-3 mb-4">
                    <!-- Chart 1: Week-wise Collections -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-calendar-week text-primary me-2"></i>Weekly Collection Velocity
                                    </h6>
                                    <small class="text-muted">Last active weeks revenue intake trend</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="week">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_week" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 2: Year-on-Year / Monthly Comparison -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-calendar-alt text-success me-2"></i>Year-on-Year Growth ({{ $analytics['yoy']['prev_year'] }} vs {{ $analytics['yoy']['curr_year'] }})
                                    </h6>
                                    <small class="text-muted">Month-by-month comparative performance</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="yoy">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_yoy" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROW 2: COMPARISON ANALYTICS (District-wise & User/Collector-wise) -->
                <div class="row g-3 mb-4">
                    <!-- Chart 3: District-wise Revenue -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-map-marked-alt text-danger me-2"></i>District Revenue Comparison
                                    </h6>
                                    <small class="text-muted">Concession revenue across mining territories</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="district">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_district" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 4: Staff & Collector Performance -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-user-check text-warning me-2"></i>Staff &amp; Collector Ranking
                                    </h6>
                                    <small class="text-muted">Money receipts recorded by officer</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="user">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_user" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROW 3: CHANNEL & MODULE DYNAMICS (Statutory Module & Payment Instrument) -->
                <div class="row g-3">
                    <!-- Chart 5: Statutory Service Module Breakdown -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-file-invoice text-info me-2"></i>Statutory Module Distribution
                                    </h6>
                                    <small class="text-muted">Lease, Mining, Environment, Drone, etc.</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="module">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_module" style="min-height: 250px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart 6: Payment Instrument Share -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100 bg-white" style="border-radius: 10px;">
                            <div class="card-header bg-transparent py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-credit-card text-secondary me-2"></i>Payment Instrument Share
                                    </h6>
                                    <small class="text-muted">NEFT/RTGS, UPI, Cash, Cheque breakdown</small>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-view-more-chart" data-chart="mode">
                                    <i class="fas fa-expand me-1"></i> View More
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div id="chart_summary_mode" style="min-height: 250px;"></div>
                            </div>
                        </div>
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
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#reportsExportModal" title="Export Filtered Report">
                                <i class="fas fa-file-export"></i>
                            </button>
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

{{-- Detailed Chart Deep-Dive Modal (P3.5) --}}
<div class="modal fade" id="detailedChartModal" tabindex="-1" aria-labelledby="detailedChartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0F1E4D 0%, #1A327E 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="detailedChartModalLabel">
                        <i class="fas fa-chart-line text-warning me-2"></i><span id="modal_chart_title">Executive Analytics Deep-Dive</span>
                    </h5>
                    <div class="text-white-50 small mt-1" id="modal_chart_subtitle">Detailed comparative breakdown &amp; metrics</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                {{-- Metric Controls Bar --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 pb-3 border-bottom gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-semibold text-muted text-uppercase" style="letter-spacing: 0.5px;">Display Metric:</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Metric Switcher">
                            <button type="button" class="btn btn-navy active btn-metric-switch" data-metric="amount">
                                <i class="fas fa-rupee-sign me-1"></i> Revenue Amount (₹)
                            </button>
                            <button type="button" class="btn btn-outline-navy btn-metric-switch" data-metric="count">
                                <i class="fas fa-list-ol me-1"></i> Receipt Count (#)
                            </button>
                        </div>
                    </div>
                    <div id="modal_chart_stat_badge">
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">Loading...</span>
                    </div>
                </div>

                {{-- Standalone Large Interactive Chart --}}
                <div id="modal_chart_render" style="min-height: 360px;"></div>

                {{-- Itemized Tabular Breakdown --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="fas fa-table text-primary me-2"></i>Itemized Tabular Breakdown &amp; Performance Metrics
                        </h6>
                        <span class="badge bg-light text-muted border" id="modal_chart_table_count">0 Rows</span>
                    </div>
                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;" id="modal_chart_table">
                            <thead class="table-light sticky-top" style="font-size: 0.76rem; text-transform: uppercase;">
                                <tr id="modal_chart_table_thead"></tr>
                            </thead>
                            <tbody id="modal_chart_table_tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="small text-muted" id="modal_chart_footer_count">
                    GTMS Financial Intelligence Engine
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success btn-sm" id="btn_modal_export_csv">
                        <i class="fas fa-file-csv me-1"></i> Export Data (CSV)
                    </button>
                    <button type="button" class="btn btn-light btn-sm fw-semibold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.accounts.partials.export_modal', [
    'id' => 'reportsExportModal',
    'title' => 'Financial Reports & Reconciliation',
    'tableSelector' => 'table.report-table',
    'filename' => 'GTMS_Financial_Report_' . date('Ymd'),
    'serverCsvUrl' => route('accounts.reports.export-csv', request()->all())
])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/apexchart/apexchart.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.jQuery && jQuery.fn.select2 && $('#reports_customer_id').length) {
            $('#reports_customer_id').select2({
                placeholder: 'All Customers',
                allowClear: true,
                width: '100%'
            });
        }

        // Global Analytics Data injected from FinancialReportController
        const analyticsData = @json($analytics);
        const summaryCharts = {};

        function formatInr(val) {
            return Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function formatCompactInr(val) {
            const num = Number(val || 0);
            if (num >= 10000000) return '₹ ' + (num / 10000000).toFixed(2) + ' Cr';
            if (num >= 100000) return '₹ ' + (num / 100000).toFixed(2) + ' L';
            if (num >= 1000) return '₹ ' + (num / 1000).toFixed(1) + ' k';
            return '₹ ' + num.toFixed(0);
        }

        function renderSummaryCharts() {
            // 1. Week-wise Bar Chart
            const weekOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Weekly Revenue', data: analyticsData.week.amounts }],
                colors: ['#0F1E4D'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: analyticsData.week.labels },
                yaxis: {
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_week')) {
                summaryCharts.week = new ApexCharts(document.getElementById('chart_summary_week'), weekOpts);
                summaryCharts.week.render();
            }

            // 2. Year-on-Year YoY Comparison Bar Chart
            const yoyOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [
                    { name: String(analyticsData.yoy.prev_year), data: analyticsData.yoy.prev_series },
                    { name: String(analyticsData.yoy.curr_year), data: analyticsData.yoy.curr_series }
                ],
                colors: ['#94A3B8', '#10B981'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: analyticsData.yoy.months },
                yaxis: {
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_yoy')) {
                summaryCharts.yoy = new ApexCharts(document.getElementById('chart_summary_yoy'), yoyOpts);
                summaryCharts.yoy.render();
            }

            // 3. District-wise Revenue Horizontal Bar Chart
            const distOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Territory Revenue', data: analyticsData.district.amounts }],
                colors: ['#EF4444'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '50%' } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: analyticsData.district.labels,
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_district')) {
                summaryCharts.district = new ApexCharts(document.getElementById('chart_summary_district'), distOpts);
                summaryCharts.district.render();
            }

            // 4. Staff/Collector Performance Bar Chart
            const userOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Collections Logged', data: analyticsData.user.amounts }],
                colors: ['#F59E0B'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: analyticsData.user.labels },
                yaxis: {
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_user')) {
                summaryCharts.user = new ApexCharts(document.getElementById('chart_summary_user'), userOpts);
                summaryCharts.user.render();
            }

            // 5. Statutory Module Distribution
            const moduleOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Module Intake', data: analyticsData.module.amounts }],
                colors: ['#06B6D4'],
                plotOptions: { bar: { borderRadius: 4, horizontal: true, barHeight: '55%' } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: analyticsData.module.labels,
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_module')) {
                summaryCharts.module = new ApexCharts(document.getElementById('chart_summary_module'), moduleOpts);
                summaryCharts.module.render();
            }

            // 6. Payment Instrument Share
            const modeOpts = {
                chart: { type: 'bar', height: 260, toolbar: { show: false } },
                series: [{ name: 'Mode Intake', data: analyticsData.mode.amounts }],
                colors: ['#8B5CF6'],
                plotOptions: { bar: { borderRadius: 4, columnWidth: '45%' } },
                dataLabels: { enabled: false },
                xaxis: { categories: analyticsData.mode.labels },
                yaxis: {
                    labels: {
                        formatter: function (v) { return formatCompactInr(v); }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) { return '₹ ' + formatInr(v); }
                    }
                }
            };
            if (document.getElementById('chart_summary_mode')) {
                summaryCharts.mode = new ApexCharts(document.getElementById('chart_summary_mode'), modeOpts);
                summaryCharts.mode.render();
            }
        }

        // =========================================================================
        // VIEW MORE MODAL LOGIC (Deep-Dive Standalone Charts & Tables)
        // =========================================================================
        let modalChartInstance = null;
        let activeChartKey = 'week';
        let activeMetric = 'amount';
        let modalBootstrap = null;

        function getModalBootstrap() {
            if (!modalBootstrap) {
                const modalEl = document.getElementById('detailedChartModal');
                if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    modalBootstrap = new bootstrap.Modal(modalEl);
                }
            }
            return modalBootstrap;
        }

        function setupViewMoreHandlers() {
            document.querySelectorAll('.btn-view-more-chart').forEach(btn => {
                btn.addEventListener('click', function () {
                    activeChartKey = this.getAttribute('data-chart') || 'week';
                    activeMetric = 'amount';
                    openDetailedChartModal(activeChartKey, activeMetric);
                });
            });

            // Metric Switcher buttons inside modal
            document.querySelectorAll('.btn-metric-switch').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-metric-switch').forEach(b => {
                        b.classList.remove('btn-navy', 'active');
                        b.classList.add('btn-outline-navy');
                    });
                    this.classList.remove('btn-outline-navy');
                    this.classList.add('btn-navy', 'active');

                    activeMetric = this.getAttribute('data-metric') || 'amount';
                    renderModalContent(activeChartKey, activeMetric);
                });
            });

            // CSV Export inside modal
            const exportCsvBtn = document.getElementById('btn_modal_export_csv');
            if (exportCsvBtn) {
                exportCsvBtn.addEventListener('click', function () {
                    exportModalTableToCsv(activeChartKey);
                });
            }
        }

        function openDetailedChartModal(chartKey, metric) {
            const bsModal = getModalBootstrap();
            if (bsModal) {
                bsModal.show();
            } else if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                $('#detailedChartModal').modal('show');
            } else {
                return;
            }

            // Reset metric buttons to 'amount'
            document.querySelectorAll('.btn-metric-switch').forEach(b => {
                if (b.getAttribute('data-metric') === 'amount') {
                    b.classList.remove('btn-outline-navy');
                    b.classList.add('btn-navy', 'active');
                } else {
                    b.classList.remove('btn-navy', 'active');
                    b.classList.add('btn-outline-navy');
                }
            });

            // Allow modal transition to finish before rendering ApexChart
            setTimeout(() => {
                renderModalContent(chartKey, metric);
            }, 300);
        }

        function renderModalContent(chartKey, metric) {
            const chartTitles = {
                week: { title: 'Weekly Revenue Intake Velocity', sub: 'Last active weeks intake trend, receipt counts, and ticket averages' },
                yoy: { title: 'Year-on-Year Growth Analysis (' + analyticsData.yoy.prev_year + ' vs ' + analyticsData.yoy.curr_year + ')', sub: 'Monthly comparative revenue intake and growth metrics' },
                district: { title: 'Territory / District Revenue Analytics', sub: 'Concession revenue distribution across mining districts' },
                user: { title: 'Staff & Collector Performance Ranking', sub: 'Money receipts and revenue logged per staff member' },
                module: { title: 'Statutory Service Department Breakdown', sub: 'Revenue performance across statutory mining modules' },
                mode: { title: 'Payment Instrument Distribution', sub: 'Collections breakdown across Cash, NEFT/RTGS, UPI, and Cheque' }
            };

            const meta = chartTitles[chartKey] || { title: 'Analytics', sub: 'Overview' };
            document.getElementById('modal_chart_title').textContent = meta.title;
            document.getElementById('modal_chart_subtitle').textContent = meta.sub;

            // Destroy prior modal chart instance
            if (modalChartInstance) {
                modalChartInstance.destroy();
                modalChartInstance = null;
            }

            const container = document.getElementById('modal_chart_render');
            container.innerHTML = '';

            // Build Options based on chartKey and metric
            let modalOpts = getModalChartOptions(chartKey, metric);
            modalChartInstance = new ApexCharts(container, modalOpts);
            modalChartInstance.render();

            // Populate Table Breakdown
            populateModalTable(chartKey);
        }

        function getModalChartOptions(chartKey, metric) {
            const isAmount = (metric === 'amount');
            let series = [];
            let categories = [];
            let colors = ['#0F1E4D'];

            if (chartKey === 'week') {
                categories = analyticsData.week.labels;
                series = [{
                    name: isAmount ? 'Weekly Revenue (₹)' : 'Receipt Count',
                    data: isAmount ? analyticsData.week.amounts : analyticsData.week.counts
                }];
                colors = ['#0F1E4D'];
                const total = analyticsData.week.amounts.reduce((a, b) => a + b, 0);
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-primary-subtle text-primary border px-3 py-2">8-Week Total: ₹ ${formatInr(total)}</span>`;
            } else if (chartKey === 'yoy') {
                categories = analyticsData.yoy.months;
                if (isAmount) {
                    series = [
                        { name: String(analyticsData.yoy.prev_year), data: analyticsData.yoy.prev_series },
                        { name: String(analyticsData.yoy.curr_year), data: analyticsData.yoy.curr_series }
                    ];
                } else {
                    series = [
                        { name: String(analyticsData.yoy.prev_year) + ' Count', data: analyticsData.yoy.table.map(r => r.prev_count) },
                        { name: String(analyticsData.yoy.curr_year) + ' Count', data: analyticsData.yoy.table.map(r => r.curr_count) }
                    ];
                }
                colors = ['#94A3B8', '#10B981'];
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-success-subtle text-success border px-3 py-2">Current Year (${analyticsData.yoy.curr_year}): ₹ ${formatInr(analyticsData.yoy.curr_total)}</span>`;
            } else if (chartKey === 'district') {
                categories = analyticsData.district.labels;
                series = [{
                    name: isAmount ? 'Revenue (₹)' : 'Receipts Count',
                    data: isAmount ? analyticsData.district.amounts : analyticsData.district.counts
                }];
                colors = ['#EF4444'];
                const total = analyticsData.district.amounts.reduce((a, b) => a + b, 0);
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-danger-subtle text-danger border px-3 py-2">Territories Total: ₹ ${formatInr(total)}</span>`;
            } else if (chartKey === 'user') {
                categories = analyticsData.user.labels;
                series = [{
                    name: isAmount ? 'Collected (₹)' : 'Receipts Count',
                    data: isAmount ? analyticsData.user.amounts : analyticsData.user.counts
                }];
                colors = ['#F59E0B'];
                const total = analyticsData.user.amounts.reduce((a, b) => a + b, 0);
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis border px-3 py-2">Team Total: ₹ ${formatInr(total)}</span>`;
            } else if (chartKey === 'module') {
                categories = analyticsData.module.labels;
                series = [{
                    name: isAmount ? 'Revenue (₹)' : 'Receipts Count',
                    data: isAmount ? analyticsData.module.amounts : analyticsData.module.counts
                }];
                colors = ['#06B6D4'];
                const total = analyticsData.module.amounts.reduce((a, b) => a + b, 0);
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-info-subtle text-info border px-3 py-2">Services Total: ₹ ${formatInr(total)}</span>`;
            } else if (chartKey === 'mode') {
                categories = analyticsData.mode.labels;
                series = [{
                    name: isAmount ? 'Revenue (₹)' : 'Receipts Count',
                    data: isAmount ? analyticsData.mode.amounts : analyticsData.mode.counts
                }];
                colors = ['#8B5CF6'];
                const total = analyticsData.mode.amounts.reduce((a, b) => a + b, 0);
                document.getElementById('modal_chart_stat_badge').innerHTML = `<span class="badge bg-secondary-subtle text-secondary border px-3 py-2">Settlements Total: ₹ ${formatInr(total)}</span>`;
            }

            return {
                chart: {
                    type: (chartKey === 'district' || chartKey === 'module') ? 'bar' : 'bar',
                    height: 360,
                    toolbar: { show: true, tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false } }
                },
                series: series,
                colors: colors,
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: (chartKey === 'district' || chartKey === 'module'),
                        columnWidth: '50%',
                        barHeight: '55%'
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: categories,
                    labels: {
                        formatter: function (v) {
                            return (chartKey === 'district' || chartKey === 'module') && isAmount ? formatCompactInr(v) : v;
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (v) {
                            return isAmount && !(chartKey === 'district' || chartKey === 'module') ? formatCompactInr(v) : v;
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (v) {
                            return isAmount ? '₹ ' + formatInr(v) : v + ' Receipts';
                        }
                    }
                }
            };
        }

        function populateModalTable(chartKey) {
            const thead = document.getElementById('modal_chart_table_thead');
            const tbody = document.getElementById('modal_chart_table_tbody');
            thead.innerHTML = '';
            tbody.innerHTML = '';

            let rows = [];
            let headers = [];

            if (chartKey === 'week') {
                headers = ['Week Label', 'Date Period', 'Total Revenue (₹)', 'Receipt Count', 'Avg. per Receipt (₹)'];
                rows = analyticsData.week.table.map(r => [
                    `<span class="fw-bold text-primary font-monospace">${r.label}</span>`,
                    `<span class="text-muted">${r.range}</span>`,
                    `<span class="fw-bold text-dark font-monospace">₹ ${formatInr(r.amount)}</span>`,
                    `<span class="badge bg-light text-dark border">${r.count}</span>`,
                    `<span class="text-muted font-monospace">₹ ${formatInr(r.avg_ticket)}</span>`
                ]);
            } else if (chartKey === 'yoy') {
                headers = ['Month', `${analyticsData.yoy.prev_year} Intake`, `${analyticsData.yoy.curr_year} Intake`, 'Net Difference', 'Growth %', `${analyticsData.yoy.curr_year} Receipts`];
                rows = analyticsData.yoy.table.map(r => {
                    const isPositive = r.diff >= 0;
                    return [
                        `<span class="fw-bold text-dark">${r.month_name}</span>`,
                        `<span class="text-muted font-monospace">₹ ${formatInr(r.previous)}</span>`,
                        `<span class="fw-bold text-dark font-monospace">₹ ${formatInr(r.current)}</span>`,
                        `<span class="font-monospace fw-semibold ${isPositive ? 'text-success' : 'text-danger'}">${isPositive ? '+' : ''}₹ ${formatInr(r.diff)}</span>`,
                        `<span class="badge ${isPositive ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger'}">${isPositive ? '+' : ''}${r.growth_pct}%</span>`,
                        `<span class="badge bg-light text-dark border">${r.curr_count}</span>`
                    ];
                });
            } else if (chartKey === 'district') {
                headers = ['Territory / District', 'Total Revenue (₹)', 'Receipt Count', 'Revenue Share (%)', 'Avg. Ticket (₹)'];
                rows = analyticsData.district.table.map(r => [
                    `<span class="fw-bold text-dark"><i class="fas fa-map-marker-alt text-danger me-1"></i>${r.district_name}</span>`,
                    `<span class="fw-bold text-dark font-monospace">₹ ${formatInr(r.amount)}</span>`,
                    `<span class="badge bg-light text-dark border">${r.count}</span>`,
                    `<div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                            <div class="progress-bar bg-danger" style="width: ${r.share_pct}%;"></div>
                        </div>
                        <span class="small font-monospace fw-semibold">${r.share_pct}%</span>
                    </div>`,
                    `<span class="text-muted font-monospace">₹ ${formatInr(r.avg_ticket)}</span>`
                ]);
            } else if (chartKey === 'user') {
                headers = ['Staff / Collector', 'Staff Code', 'Total Collected (₹)', 'Receipt Count', 'Contribution (%)', 'Avg. Ticket (₹)'];
                rows = analyticsData.user.table.map(r => [
                    `<span class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i>${r.staff_name}</span>`,
                    `<span class="font-monospace text-muted small">${r.staff_code}</span>`,
                    `<span class="fw-bold text-success font-monospace">₹ ${formatInr(r.amount)}</span>`,
                    `<span class="badge bg-light text-dark border">${r.count}</span>`,
                    `<div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                            <div class="progress-bar bg-warning" style="width: ${r.share_pct}%;"></div>
                        </div>
                        <span class="small font-monospace fw-semibold">${r.share_pct}%</span>
                    </div>`,
                    `<span class="text-muted font-monospace">₹ ${formatInr(r.avg_ticket)}</span>`
                ]);
            } else if (chartKey === 'module') {
                headers = ['Statutory Module', 'Type Code', 'Total Revenue (₹)', 'Applications / Receipts', 'Share (%)', 'Avg. Ticket (₹)'];
                rows = analyticsData.module.table.map(r => [
                    `<span class="fw-bold text-dark">${r.module_name}</span>`,
                    `<span class="badge bg-light text-secondary border font-monospace">${r.module_type}</span>`,
                    `<span class="fw-bold text-dark font-monospace">₹ ${formatInr(r.amount)}</span>`,
                    `<span class="badge bg-light text-dark border">${r.count}</span>`,
                    `<div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                            <div class="progress-bar bg-info" style="width: ${r.share_pct}%;"></div>
                        </div>
                        <span class="small font-monospace fw-semibold">${r.share_pct}%</span>
                    </div>`,
                    `<span class="text-muted font-monospace">₹ ${formatInr(r.avg_ticket)}</span>`
                ]);
            } else if (chartKey === 'mode') {
                headers = ['Payment Instrument', 'Total Collected (₹)', 'Receipts Count', 'Share (%)', 'Avg. per Transaction (₹)'];
                rows = analyticsData.mode.table.map(r => [
                    `<span class="fw-bold text-dark">${r.mode_name}</span>`,
                    `<span class="fw-bold text-dark font-monospace">₹ ${formatInr(r.amount)}</span>`,
                    `<span class="badge bg-light text-dark border">${r.count}</span>`,
                    `<div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px; width: 70px;">
                            <div class="progress-bar bg-purple" style="width: ${r.share_pct}%; background-color:#8B5CF6;"></div>
                        </div>
                        <span class="small font-monospace fw-semibold">${r.share_pct}%</span>
                    </div>`,
                    `<span class="text-muted font-monospace">₹ ${formatInr(r.avg_ticket)}</span>`
                ]);
            }

            // Render Header
            let thHtml = '';
            headers.forEach(h => {
                thHtml += `<th>${h}</th>`;
            });
            thead.innerHTML = thHtml;

            // Render Rows
            let tbHtml = '';
            rows.forEach(r => {
                tbHtml += '<tr>';
                r.forEach(c => {
                    tbHtml += `<td>${c}</td>`;
                });
                tbHtml += '</tr>';
            });
            tbody.innerHTML = tbHtml || `<tr><td colspan="${headers.length}" class="text-center text-muted py-3">No records found.</td></tr>`;
            document.getElementById('modal_chart_table_count').textContent = rows.length + ' Rows';
            document.getElementById('modal_chart_footer_count').textContent = 'Showing ' + rows.length + ' itemized entries for ' + chartKey.toUpperCase();
        }

        function exportModalTableToCsv(chartKey) {
            const table = document.getElementById('modal_chart_table');
            if (!table) return;

            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll('td, th');
                for (let j = 0; j < cols.length; j++) {
                    let cleanText = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/₹/g, '').trim();
                    cleanText = cleanText.replace(/"/g, '""');
                    row.push('"' + cleanText + '"');
                }
                csv.push(row.join(','));
            }

            const csvString = '\uFEFF' + csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `GTMS_${chartKey.toUpperCase()}_Analytics_${new Date().toISOString().slice(0,10)}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // =========================================================================
        // EXPANDED STUDIO TOGGLE
        // =========================================================================
        function setupStudioViewToggle() {
            const toggleBtn = document.getElementById('btn_toggle_analytics_view');
            const textEl = document.getElementById('btn_toggle_analytics_text');
            if (!toggleBtn || !textEl) return;

            let isExpanded = false;

            toggleBtn.addEventListener('click', function () {
                isExpanded = !isExpanded;
                const chartCards = document.querySelectorAll('#chart_summary_week, #chart_summary_yoy, #chart_summary_district, #chart_summary_user, #chart_summary_module, #chart_summary_mode');

                if (isExpanded) {
                    textEl.textContent = 'Compact View';
                    toggleBtn.classList.remove('btn-outline-secondary');
                    toggleBtn.classList.add('btn-navy');

                    // Resize charts for studio view
                    Object.values(summaryCharts).forEach(chart => {
                        if (chart) chart.updateOptions({ chart: { height: 350 } });
                    });
                } else {
                    textEl.textContent = 'Expanded Studio';
                    toggleBtn.classList.remove('btn-navy');
                    toggleBtn.classList.add('btn-outline-secondary');

                    // Reset chart heights
                    Object.values(summaryCharts).forEach(chart => {
                        if (chart) chart.updateOptions({ chart: { height: 260 } });
                    });
                }
            });
        }

        // =========================================================================
        // INITIALIZE CHARTS ON LOAD
        // =========================================================================
        let initRetries = 0;
        function initChartsWhenReady() {
            if (typeof ApexCharts === 'undefined') {
                initRetries++;
                if (initRetries < 30) {
                    setTimeout(initChartsWhenReady, 100);
                } else {
                    console.warn('ApexCharts library could not be loaded.');
                }
                return;
            }
            renderSummaryCharts();
            setupViewMoreHandlers();
            setupStudioViewToggle();
        }

        initChartsWhenReady();
    });
</script>
@endpush
