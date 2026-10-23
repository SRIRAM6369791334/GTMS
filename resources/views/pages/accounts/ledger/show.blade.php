@extends('layouts.app')

@section('title', 'Customer Financial Dossier • ' . ($customer->company_name ?: $customer->customer_name) . ' • GTMS')

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
        padding: 12px 14px;
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .badge-debit {
        background-color: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .badge-credit {
        background-color: #ECFDF5;
        color: #047857;
        border: 1px solid #A7F3D0;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
    }
</style>
@endpush

@section('main_content')

<div class="content-body">
    <div class="container-fluid">
        <!-- Breadcrumb & Top Bar -->
        <div class="row page-titles mb-3">
            <div class="col-sm-6 p-md-0 d-flex align-items-center">
                <div class="welcome-text">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1 text-muted small">
                            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('accounts.ledger.index') }}" class="text-decoration-none text-muted">Customer Ledger</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Client Dossier</li>
                        </ol>
                    </nav>
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-file-invoice text-primary me-2"></i>{{ $customer->company_name ?: $customer->customer_name }}
                    </h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
                <a href="{{ route('accounts.ledger.print', array_merge(['customer' => $customer->slug ?: $customer->id], request()->only(['from_date', 'to_date']))) }}" target="_blank" class="btn btn-outline-dark shadow-sm">
                    <i class="fas fa-print me-1"></i> Print Statement
                </a>
                <button type="button" class="btn btn-outline-success shadow-sm" data-bs-toggle="modal" data-bs-target="#ledgerDossierExportModal">
                    <i class="fas fa-file-export me-1"></i> Export Dossier
                </button>
                @can('account.create')
                <a href="{{ route('accounts.payments.create', ['customer_id' => $customer->id]) }}" class="btn btn-navy shadow-sm">
                    <i class="fas fa-cash-register me-1"></i> Collect Payment
                </a>
                <a href="{{ route('accounts.quotations.create', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> New Quotation
                </a>
                @endcan
                <a href="{{ route('accounts.ledger.index') }}" class="btn btn-light border shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Directory
                </a>
            </div>
        </div>

        <!-- Customer 360 Profile Banner -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-7">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="background: var(--ct-navy); width: 56px; height: 56px;">
                                <i class="fas fa-building fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $customer->company_name ?: $customer->customer_name }}</h5>
                                <div class="text-muted small mb-2">
                                    @if($customer->customer_name && $customer->company_name)
                                        <span class="me-3"><i class="fas fa-user text-muted me-1"></i>Contact: <strong>{{ $customer->customer_name }}</strong></span>
                                    @endif
                                    @if($customer->mobile_num)
                                        <span class="me-3"><i class="fas fa-phone text-muted me-1"></i>{{ $customer->mobile_num }}</span>
                                    @endif
                                    @if($customer->email)
                                        <span class="me-3"><i class="fas fa-envelope text-muted me-1"></i>{{ $customer->email }}</span>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    @if($customer->mimas_no || $customer->mimas_number)
                                        <span class="badge bg-light text-dark border"><i class="fas fa-id-card text-primary me-1"></i>MIMAS: <strong>{{ $customer->mimas_no ?: $customer->mimas_number }}</strong></span>
                                    @endif
                                    @if($customer->gstin)
                                        <span class="badge bg-light text-dark border">GSTIN: <strong>{{ $customer->gstin }}</strong></span>
                                    @endif
                                    @if($customer->pan)
                                        <span class="badge bg-light text-dark border">PAN: <strong>{{ $customer->pan }}</strong></span>
                                    @endif
                                    @if($customer->district)
                                        <span class="badge bg-light text-dark border"><i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $customer->district->name ?? $customer->district->district_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 border-start-lg ps-lg-4">
                        <div class="text-muted small mb-1"><i class="fas fa-map-pin me-1"></i>Registered Concession Address:</div>
                        <div class="text-dark small fw-semibold">{{ $customer->address ?: 'Concession address on file' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid var(--ct-accent-blue) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Billed (Debits)</div>
                                <div class="fs-4 fw-bold mt-1 text-dark financial-number">&#8377; {{ number_format($allTimeBilled, 2) }}</div>
                                <div class="small text-muted mt-1">
                                    @if($fromDate || $toDate)
                                        Period: &#8377; {{ number_format($periodBilled, 2) }}
                                    @else
                                        Total statutory quotations
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #eff6ff; width: 48px; height: 48px;">
                                <i class="fas fa-file-invoice fs-5 text-primary"></i>
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
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Received (Credits)</div>
                                <div class="fs-4 fw-bold mt-1 text-success financial-number">&#8377; {{ number_format($allTimeReceived, 2) }}</div>
                                <div class="small text-muted mt-1">
                                    @if($fromDate || $toDate)
                                        Period: &#8377; {{ number_format($periodReceived, 2) }}
                                    @else
                                        Verified money receipts
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; width: 48px; height: 48px;">
                                <i class="fas fa-check-circle fs-5 text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid {{ $netBalance > 0 ? 'var(--ct-accent-red)' : 'var(--ct-accent-emerald)' }} !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Net Balance Due</div>
                                <div class="fs-4 fw-bold mt-1 {{ $netBalance > 0 ? 'text-danger' : 'text-success' }} financial-number">
                                    &#8377; {{ number_format($netBalance, 2) }}
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $netBalance > 0 ? 'Outstanding payable' : 'Fully settled account' }}
                                </div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: {{ $netBalance > 0 ? '#fef2f2' : '#ecfdf5' }}; width: 48px; height: 48px;">
                                <i class="fas {{ $netBalance > 0 ? 'fa-exclamation-triangle text-danger' : 'fa-check text-success' }} fs-5"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi border-0 shadow-sm h-100" style="background: #ffffff; border-left: 4px solid var(--ct-navy) !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Ledger Records</div>
                                <div class="fs-4 fw-bold mt-1 text-dark financial-number">{{ $transactions->count() }}</div>
                                <div class="small text-muted mt-1">Itemized entries on ledger</div>
                            </div>
                            <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #f1f5f9; width: 48px; height: 48px;">
                                <i class="fas fa-list-ol fs-5 text-dark"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Range Filter Toolbar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-body p-3">
                <form action="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-4 col-sm-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted">From Date</span>
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate) }}">
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted">To Date</span>
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate) }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-navy flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filter Period
                        </button>
                        @if($fromDate || $toDate)
                        <a href="{{ route('accounts.ledger.show', $customer->slug ?: $customer->id) }}" class="btn btn-sm btn-outline-secondary" title="Reset Period">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Chronological Ledger Transaction Table -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-history text-primary me-2"></i>Itemized Financial Ledger &amp; Running Balance
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2">
                    {{ $transactions->count() }} Transactions Recorded
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover ledger-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 110px;">Date</th>
                                <th style="width: 140px;">Type</th>
                                <th>Reference</th>
                                <th>Particulars / Description</th>
                                <th class="text-end" style="width: 130px;">Debit (&#8377; Billed)</th>
                                <th class="text-end" style="width: 130px;">Credit (&#8377; Paid)</th>
                                <th class="text-end" style="width: 150px;">Running Balance</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($fromDate && $openingBalance != 0)
                                <tr class="table-light">
                                     <td class="text-muted fw-semibold">{{ date('d-M-Y', strtotime($fromDate)) }}</td>
                                     <td><span class="badge bg-secondary">Opening</span></td>
                                     <td class="text-muted font-monospace">—</td>
                                     <td class="fw-semibold text-dark">Opening Balance (Brought Forward)</td>
                                     <td class="text-end text-muted">—</td>
                                     <td class="text-end text-muted">—</td>
                                     <td class="text-end fw-bold text-dark financial-number">&#8377; {{ number_format($openingBalance, 2) }}</td>
                                     <td class="text-center text-muted">—</td>
                                 </tr>
                            @endif

                            @forelse($transactions as $tx)
                                <tr>
                                     <td class="text-muted fw-semibold">
                                         {{ $tx->date->format('d-M-Y') }}
                                     </td>
                                     <td>
                                         @if($tx->type === 'quotation')
                                             <span class="badge-debit"><i class="fas fa-file-invoice me-1"></i> Quotation</span>
                                         @else
                                             <span class="badge-credit"><i class="fas fa-receipt me-1"></i> Receipt</span>
                                         @endif
                                     </td>
                                     <td>
                                         <span class="font-monospace fw-bold text-dark">{{ $tx->ref_no }}</span>
                                         @if($tx->type === 'payment' && !empty($tx->installment_no))
                                             <span class="badge bg-primary-subtle text-primary border ms-1" style="font-size: 0.7rem;">
                                                 Inst. {{ $tx->installment_no }}/{{ $tx->total_installments }}
                                             </span>
                                         @endif
                                     </td>
                                     <td>
                                         <div class="fw-semibold text-dark">{{ $tx->description }}</div>
                                         <div class="small text-muted">{{ $tx->module }}</div>
                                     </td>
                                     <td class="text-end fw-semibold financial-number {{ $tx->debit > 0 ? 'text-primary' : 'text-muted' }}">
                                         {{ $tx->debit > 0 ? '₹ ' . number_format($tx->debit, 2) : '—' }}
                                     </td>
                                     <td class="text-end fw-semibold financial-number {{ $tx->credit > 0 ? 'text-success' : 'text-muted' }}">
                                         {{ $tx->credit > 0 ? '₹ ' . number_format($tx->credit, 2) : '—' }}
                                     </td>
                                     <td class="text-end fw-bold financial-number {{ $tx->running_balance > 0 ? 'text-danger' : 'text-success' }}">
                                         &#8377; {{ number_format($tx->running_balance, 2) }}
                                     </td>
                                     <td class="text-center">
                                         <div class="d-inline-flex align-items-center gap-1">
                                             <a href="{{ $tx->print_url }}" target="_blank" class="btn btn-xs btn-outline-secondary" title="Print Document">
                                                 <i class="fas fa-print"></i>
                                             </a>
                                             <a href="{{ $tx->view_url }}" class="btn btn-xs btn-outline-primary" title="View Details">
                                                 <i class="fas fa-eye"></i>
                                             </a>
                                             @if($tx->type === 'payment' && !empty($tx->application_type) && !empty($tx->application_id))
                                                 <button type="button" 
                                                         class="btn btn-xs btn-outline-info btn-view-app-history"
                                                         data-type="{{ $tx->application_type }}"
                                                         data-id="{{ $tx->application_id }}"
                                                         data-ref="{{ $tx->application_ref }}"
                                                         data-client="{{ $customer->company_name ?: ($customer->customer_name ?? 'Client') }}"
                                                         title="View Installment History & Chain">
                                                     <i class="fas fa-history"></i>
                                                 </button>
                                             @endif
                                         </div>
                                     </td>
                                </tr>
                            @empty
                                <tr>
                                     <td colspan="8" class="text-center py-5">
                                         <div class="py-3">
                                             <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                             <h6 class="text-dark fw-bold">No Transactions Recorded in this Period</h6>
                                             <p class="text-muted small mb-0">Quotations issued or payments collected will automatically appear here chronologically.</p>
                                         </div>
                                     </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light border-top">
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end text-dark">Period Totals:</td>
                                <td class="text-end text-primary financial-number">&#8377; {{ number_format($periodBilled, 2) }}</td>
                                <td class="text-end text-success financial-number">&#8377; {{ number_format($periodReceived, 2) }}</td>
                                <td class="text-end financial-number {{ $netBalance > 0 ? 'text-danger' : 'text-success' }} fs-6">
                                    &#8377; {{ number_format($netBalance, 2) }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statutory Applications Dues Resolver -->
        @if(count($applicationDues) > 0)
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>Active Statutory Applications &amp; Outstanding Dues
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2">
                    {{ count($applicationDues) }} Applications Linked
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Statutory Module</th>
                                <th>Application Reference</th>
                                <th class="text-end">Quoted Value</th>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">Pending Amount</th>
                                <th class="text-center">Installments</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 180px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applicationDues as $due)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $due->module }}</td>
                                    <td><span class="font-monospace text-secondary fw-semibold">{{ $due->ref }}</span></td>
                                    <td class="text-end fw-semibold text-dark financial-number">&#8377; {{ number_format($due->total, 2) }}</td>
                                    <td class="text-end fw-semibold text-success financial-number">&#8377; {{ number_format($due->paid, 2) }}</td>
                                    <td class="text-end fw-bold financial-number {{ $due->pending > 0 ? 'text-danger' : 'text-success' }}">
                                        &#8377; {{ number_format($due->pending, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($due->receipts_count > 0)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size:0.75rem;">
                                                <i class="fas fa-receipt me-1"></i> {{ $due->receipts_count }} Paid
                                            </span>
                                        @else
                                            <span class="badge bg-light text-primary border px-2 py-1" style="font-size:0.75rem;">
                                                0 Paid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($due->pending <= 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-check-circle me-1"></i> Paid</span>
                                        @elseif($due->paid > 0)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1"><i class="fas fa-hourglass-half me-1"></i> Partial</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-exclamation-circle me-1"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex justify-content-center align-items-center gap-1">
                                            @if($due->receipts_count > 0)
                                                <button type="button" 
                                                        class="btn btn-xs btn-outline-info btn-view-app-history"
                                                        data-type="{{ $due->app_type }}"
                                                        data-id="{{ $due->app_id }}"
                                                        data-ref="{{ $due->ref }}"
                                                        data-client="{{ $customer->company_name ?: ($customer->customer_name ?? 'Client') }}"
                                                        title="View Installment History & Chain">
                                                    <i class="fas fa-history me-1"></i> History
                                                </button>
                                            @endif
                                            @if($due->pending > 0)
                                                @can('account.create')
                                                <a href="{{ route('accounts.payments.create', ['customer_id' => $customer->id, 'application_type' => $due->app_type, 'application_id' => $due->app_id]) }}" class="btn btn-xs btn-navy" title="Collect Installment #{{ $due->next_installment_no }}">
                                                    <i class="fas fa-cash-register me-1"></i> Collect #{{ $due->next_installment_no }}
                                                </a>
                                                @endcan
                                            @else
                                                <span class="text-muted small"><i class="fas fa-check text-success me-1"></i> Settled</span>
                                            @endif
                                        </div>
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

{{-- Application Payment & Installment Chain Modal --}}
<div class="modal fade" id="appPaymentHistoryModal" tabindex="-1" aria-labelledby="appPaymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0F1E4D 0%, #1A327E 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="appPaymentHistoryModalLabel">
                        <i class="fas fa-history text-warning me-2"></i>Application Payment &amp; Installment Chain
                    </h5>
                    <div class="text-white-50 small mt-1" id="modal_app_subtitle">Loading application records...</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Financial Snapshot Cards --}}
                <div class="row g-2 mb-3 text-center p-3 rounded" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Total Agreed Value</div>
                        <div class="fw-bold fs-6 text-dark" id="modal_hist_val">&#8377; 0.00</div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Total Received</div>
                        <div class="fw-bold fs-6 text-success" id="modal_hist_paid">&#8377; 0.00</div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="text-muted small fw-semibold text-uppercase" style="font-size:0.68rem;">Balance Due</div>
                        <div class="fw-bold fs-6 text-danger" id="modal_hist_balance">&#8377; 0.00</div>
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
    'id' => 'ledgerDossierExportModal',
    'title' => 'Customer Financial Dossier - ' . ($customer->company_name ?: $customer->customer_name),
    'tableSelector' => 'table.ledger-table',
    'filename' => 'GTMS_Ledger_' . ($customer->slug ?: 'customer') . '_' . date('Ymd')
])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function formatInr(val) {
            return Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

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

                const endpoint = `{{ url('/accounts/receipts/application-history') }}/${encodeURIComponent(appType)}/${encodeURIComponent(appId)}`;

                fetch(endpoint, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network error ' + res.status);
                    return res.json();
                })
                .then(data => {
                    document.getElementById('modal_hist_loader').style.display = 'none';
                    document.getElementById('modal_hist_table_wrap').style.display = 'block';

                    document.getElementById('modal_hist_val').textContent     = '₹ ' + formatInr(data.product_value);
                    document.getElementById('modal_hist_paid').textContent    = '₹ ' + formatInr(data.paid_amount);
                    document.getElementById('modal_hist_balance').textContent = '₹ ' + formatInr(data.pending_amount);

                    const statusWrap = document.getElementById('modal_hist_status');
                    if (data.pending_amount <= 0) {
                        statusWrap.innerHTML = '<span class="badge bg-success-subtle text-success border border-success"><i class="fas fa-check-circle me-1"></i> Fully Settled</span>';
                    } else if (data.paid_amount > 0) {
                        statusWrap.innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning"><i class="fas fa-hourglass-half me-1"></i> Partial (${data.total_installments} Inst.)</span>`;
                    } else {
                        statusWrap.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger"><i class="fas fa-clock me-1"></i> Pending</span>';
                    }

                    const collectBtn = document.getElementById('modal_hist_collect_btn');
                    if (collectBtn) {
                        if (data.pending_amount > 0) {
                            collectBtn.href = `{{ route('accounts.payments.create') }}?customer_id={{ $customer->id }}&application_type=${encodeURIComponent(appType)}&application_id=${encodeURIComponent(appId)}`;
                            collectBtn.style.display = 'inline-flex';
                            collectBtn.innerHTML = `<i class="fas fa-cash-register me-1"></i> Collect Installment #${data.next_installment_no}`;
                        } else {
                            collectBtn.style.display = 'none';
                        }
                    }

                    const tbody = document.getElementById('modal_hist_tbody');
                    const receipts = data.installments || [];
                    document.getElementById('modal_hist_footer_count').textContent = receipts.length + ' Installment(s) recorded';

                    if (receipts.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-3">No payment vouchers generated yet for this application.</td></tr>`;
                        return;
                    }

                    let rowsHtml = '';
                    receipts.forEach(r => {
                        rowsHtml += `
                            <tr>
                                <td class="ps-2 fw-bold text-primary font-monospace">
                                    <span class="badge bg-primary-subtle text-primary border me-1">#${r.installment_no}</span>
                                </td>
                                <td class="text-muted">${r.transaction_date || '-'}</td>
                                <td>
                                    <a href="{{ url('/accounts/receipts') }}/${r.id}" class="fw-bold text-dark text-decoration-none font-monospace">
                                        ${r.receipt_number}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border me-1">${r.payment_mode || 'Cash'}</span>
                                    ${r.reference_number ? `<span class="text-muted small font-monospace">(${r.reference_number})</span>` : ''}
                                </td>
                                <td class="text-end fw-bold text-success font-monospace">₹ ${formatInr(r.amount_paid)}</td>
                                <td class="text-end text-muted font-monospace">${r.balance_after !== null ? '₹ ' + formatInr(r.balance_after) : '—'}</td>
                                <td class="text-center pe-2">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('/accounts/receipts') }}/${r.id}" class="btn btn-xs btn-outline-secondary" title="View Voucher">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ url('/accounts/receipts') }}/${r.id}/print" target="_blank" class="btn btn-xs btn-outline-primary" title="Print Official Voucher">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    tbody.innerHTML = rowsHtml;
                })
                .catch(err => {
                    document.getElementById('modal_hist_loader').style.display = 'none';
                    alert('Unable to load installment schedule: ' + err.message);
                });
            });
        });
    });
</script>
@endpush
