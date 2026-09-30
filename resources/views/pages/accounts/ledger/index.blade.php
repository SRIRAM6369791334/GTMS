@extends('layouts.app')

@section('title', 'Customer Financial Ledger • GTMS Accounts')

@push('styles')
@include('pages.accounts.partials.theme')
<style>
    .ledger-table thead th {
        background-color: #F8FAFC;
        color: #334155;
        font-weight: 600;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--ct-border);
        padding: 12px 14px;
    }
    .ledger-table tbody td {
        padding: 14px;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .badge-balance-settled {
        background-color: #DCFCE7;
        color: #15803D;
        border: 1px solid #BBF7D0;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 6px 10px;
        border-radius: 6px;
    }
    .badge-balance-due {
        background-color: #FEE2E2;
        color: #B91C1C;
        border: 1px solid #FECACA;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 6px 10px;
        border-radius: 6px;
    }
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
                        <i class="fas fa-book-open text-primary me-2"></i>Customer Financial Ledger
                    </h4>
                    <span class="text-muted small">Single-pane financial dossiers, chronological debits &amp; credits, and real-time balance reconciliation.</span>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
                @can('account.create')
                <a href="{{ route('accounts.payments.create') }}" class="btn btn-navy shadow-sm">
                    <i class="fas fa-cash-register me-1"></i> Collect Payment
                </a>
                @endcan
                <a href="{{ route('accounts.reports.index') }}" class="btn btn-outline-primary shadow-sm">
                    <i class="fas fa-chart-line me-1"></i> Reports
                </a>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0F1E4D 0%, #1A327E 100%);">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-white-50 text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Customer Accounts</div>
                                <div class="fs-3 fw-bold mt-1 text-white financial-number">{{ number_format($totalCustomersCount) }}</div>
                                <div class="small text-white-50 mt-1">Active client dossiers</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.12); width: 54px; height: 54px;">
                                <i class="fas fa-users fs-4 text-white"></i>
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
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Billed (Quotations)</div>
                                <div class="fs-3 fw-bold mt-1 text-dark financial-number">&#8377; {{ number_format($overallTotalBilled, 2) }}</div>
                                <div class="small text-muted mt-1">All commercial quotations</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #eff6ff; width: 54px; height: 54px;">
                                <i class="fas fa-file-invoice-dollar fs-4 text-primary"></i>
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
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Received (Receipts)</div>
                                <div class="fs-3 fw-bold mt-1 text-success financial-number">&#8377; {{ number_format($overallTotalReceived, 2) }}</div>
                                <div class="small text-muted mt-1">Verified receipts collected</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; width: 54px; height: 54px;">
                                <i class="fas fa-hand-holding-usd fs-4 text-success"></i>
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
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Net Outstanding Receivables</div>
                                <div class="fs-3 fw-bold mt-1 text-danger financial-number">&#8377; {{ number_format($overallTotalOutstanding, 2) }}</div>
                                <div class="small text-muted mt-1">Statewide pending balance</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #fef2f2; width: 54px; height: 54px;">
                                <i class="fas fa-exclamation-circle fs-4 text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form action="{{ route('accounts.ledger.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-9 col-sm-8">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Search by customer name, company name, mobile, email, or MIMAS ID..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-4 d-flex gap-2">
                        <button type="submit" class="btn btn-navy flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Search
                        </button>
                        @if(request('search'))
                        <a href="{{ route('accounts.ledger.index') }}" class="btn btn-outline-secondary" title="Clear Filters">
                            <i class="fas fa-redo"></i>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Active Filter Chips (P0.5) -->
        @include('pages.accounts.partials.filter_chips', ['route' => 'accounts.ledger.index'])

        <!-- Customer Directory Table -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-list text-primary me-2"></i>Customer Directory &amp; Ledger Balances
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2">
                    Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} Customers
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover table-sticky ledger-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Customer &amp; Company</th>
                                <th>Location / District</th>
                                <th class="text-end">Total Billed</th>
                                <th class="text-end">Total Received</th>
                                <th class="text-end">Net Outstanding Balance</th>
                                <th class="text-center" style="width: 220px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $index => $customer)
                                @php
                                    $billed = (float) ($customer->quotations_sum_total_amount ?? 0);
                                    $received = (float) ($customer->payment_receipts_sum_amount_paid ?? 0);
                                    $balance = round($billed - $received, 2);
                                @endphp
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $customers->firstItem() + $index }}</td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $customer->company_name ?: $customer->customer_name }}</div>
                                        <div class="text-muted small">
                                            @if($customer->company_name && $customer->customer_name)
                                                <i class="fas fa-user text-muted me-1"></i>{{ $customer->customer_name }} &bull;
                                            @endif
                                            @if($customer->mobile_num)
                                                <i class="fas fa-phone-alt text-muted me-1"></i>{{ $customer->mobile_num }}
                                            @endif
                                            @if($customer->mimas_no || $customer->mimas_number)
                                                &bull; <span class="badge bg-light text-secondary border font-monospace">MIMAS: {{ $customer->mimas_no ?: $customer->mimas_number }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->district)
                                            <span class="badge bg-light text-dark border"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $customer->district->name ?? $customer->district->district_name }}</span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-dark financial-number">
                                        &#8377; {{ number_format($billed, 2) }}
                                    </td>
                                    <td class="text-end fw-semibold text-success financial-number">
                                        &#8377; {{ number_format($received, 2) }}
                                    </td>
                                    <td class="text-end financial-number">
                                        @if($balance <= 0)
                                            <span class="badge-balance-settled">
                                                <i class="fas fa-check-circle me-1"></i> Settled (&#8377; 0.00)
                                            </span>
                                        @else
                                            <span class="badge-balance-due">
                                                <i class="fas fa-exclamation-triangle me-1"></i> &#8377; {{ number_format($balance, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" class="btn btn-navy" title="View Customer Dossier">
                                                <i class="fas fa-folder-open me-1"></i> Dossier
                                            </a>
                                            <a href="{{ route('accounts.ledger.print', $customer->slug ?: $customer->id) }}" target="_blank" class="btn btn-outline-secondary" title="Print Statement of Account">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @can('account.create')
                                            <a href="{{ route('accounts.payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-success" title="Collect Payment">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-0">
                                        @include('pages.accounts.partials.empty_state', [
                                            'icon' => 'fas fa-book-open',
                                            'title' => 'No Customer Accounts Found',
                                            'desc' => 'No customers match the entered search keyword or district filter.',
                                            'resetRoute' => route('accounts.ledger.index')
                                        ])
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Responsive Mobile Card View (< 768px) --}}
                <div class="d-md-none p-3">
                    @forelse($customers as $customer)
                        @php
                            $billed = (float) ($customer->quotations_sum_total_amount ?? 0);
                            $received = (float) ($customer->payment_receipts_sum_amount_paid ?? 0);
                            $balance = round($billed - $received, 2);
                        @endphp
                        <div class="account-mobile-card">
                            <div class="account-mobile-card-header">
                                <div>
                                    <a href="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $customer->company_name ?: $customer->customer_name }}
                                    </a>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        @if($customer->district)
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $customer->district->name ?? $customer->district->district_name }}
                                        @endif
                                        @if($customer->mobile_num)
                                            &bull; <i class="fas fa-phone-alt me-1"></i>{{ $customer->mobile_num }}
                                        @endif
                                    </div>
                                </div>
                                @if($balance <= 0)
                                    <span class="badge bg-success-subtle text-success border border-success">Settled</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Due</span>
                                @endif
                            </div>
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Total Billed:</span>
                                <span class="account-mobile-card-value financial-number">&#8377; {{ number_format($billed, 2) }}</span>
                            </div>
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Total Received:</span>
                                <span class="account-mobile-card-value text-success financial-number">&#8377; {{ number_format($received, 2) }}</span>
                            </div>
                            <div class="account-mobile-card-row">
                                <span class="account-mobile-card-label">Outstanding Balance:</span>
                                <span class="account-mobile-card-value text-danger financial-number fs-6">&#8377; {{ number_format(max(0, $balance), 2) }}</span>
                            </div>
                            <div class="account-mobile-card-actions">
                                <a href="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" class="btn btn-sm btn-navy">
                                    <i class="fas fa-folder-open me-1"></i> Dossier
                                </a>
                                <a href="{{ route('accounts.ledger.print', $customer->slug ?: $customer->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-print me-1"></i> Statement
                                </a>
                            </div>
                        </div>
                    @empty
                        @include('pages.accounts.partials.empty_state', [
                            'icon' => 'fas fa-book-open',
                            'title' => 'No Customer Accounts Found',
                            'desc' => 'No customers match the entered search keyword or district filter.',
                            'resetRoute' => route('accounts.ledger.index')
                        ])
                    @endforelse
                </div>
            </div>
            @if($customers->hasPages())
                <div class="card-footer bg-white py-3 px-4 border-top d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        Page {{ $customers->currentPage() }} of {{ $customers->lastPage() }}
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
