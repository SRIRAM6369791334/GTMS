@extends('layouts.app')

@section('title', 'Receipt Voucher ' . $receipt->receipt_number . ' - GTMS Accounts')

@push('styles')
@include('pages.accounts.partials.theme')
<style>
    .receipt-container {
        max-width: 900px;
        margin: 0 auto;
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
    .voucher-banner {
        background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 24px;
    }
    .amount-highlight-box {
        background-color: #ecfdf5;
        border: 2px solid #a7f3d0;
        border-radius: 10px;
        padding: 16px;
    }
    .detail-table td, .detail-table th {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
    .words-box {
        background-color: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: #0F1E4D;
    }
</style>
@endpush

@section('main_content')
<div class="content-body default-height">
<div class="container-fluid py-3 px-4">
    <!-- Breadcrumb & Top Bar -->
    <div class="receipt-container mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 text-muted small">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('accounts.receipts.index') }}" class="text-decoration-none text-muted">Receipts</a></li>
                        <li class="breadcrumb-item active text-primary" aria-current="page">{{ $receipt->receipt_number }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-file-invoice text-primary me-2"></i>Official Money Receipt Voucher
                </h4>
            </div>
            <div class="mt-2 mt-sm-0 d-flex gap-2">
                <a href="{{ route('accounts.receipts.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Receipts List
                </a>
                <div class="btn-group shadow-sm">
                    <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-print me-1"></i> Print Tax Invoice
                    </a>
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Choose Invoice Format">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank">
                                <i class="fas fa-receipt text-success me-2"></i> Tax Invoice (TI)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'proforma']) }}" target="_blank">
                                <i class="fas fa-file-alt text-warning me-2"></i> Proforma Invoice (PI)
                            </a>
                        </li>
                    </ul>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#receiptVoucherExportModal">
                    <i class="fas fa-file-export me-1"></i> Export Voucher
                </button>
                @can('account.create')
                <a href="{{ route('accounts.payments.create') }}" class="btn btn-navy btn-sm shadow-sm">
                    <i class="fas fa-plus me-1"></i> New Payment
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="receipt-container mb-4">
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Official Voucher Container -->
    <div class="receipt-container">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <!-- Voucher Header Banner -->
            <div class="voucher-banner">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-1">OFFICIAL MONEY RECEIPT</span>
                            @if($receipt->total_installments > 1)
                                <span class="badge bg-info text-dark fw-bold px-2 py-1">
                                    <i class="fas fa-layer-group me-1"></i> INSTALLMENT #{{ $receipt->installment_number }} OF {{ $receipt->total_installments }}
                                </span>
                            @endif
                        </div>
                        <h3 class="fw-bold mb-0 text-white">{{ $receipt->receipt_number }}</h3>
                        <div class="opacity-75 small mt-1">Granite / Mining Tracking Management System (GTMS) ERP</div>
                    </div>
                    <div class="text-sm-end mt-3 mt-sm-0">
                        <div class="small opacity-75">Date of Issuance</div>
                        <div class="fs-5 fw-bold text-white">
                            {{ $receipt->transaction_date ? $receipt->transaction_date->format('d F Y') : '-' }}
                        </div>
                        <div class="badge bg-success-subtle text-success border border-success-subtle mt-1">
                            <i class="fas fa-check-circle me-1"></i> TRANSACTION SETTLED
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Received From (Payer) & Application Ref Row -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 border-end-md">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Received With Thanks From</h6>
                        <h5 class="fw-bold text-dark mb-1">
                            {{ $receipt->customer->company_name ?: $receipt->customer->customer_name }}
                        </h5>
                        @if($receipt->customer->company_name && $receipt->customer->customer_name)
                            <div class="text-secondary small mb-1">Contact: {{ $receipt->customer->customer_name }}</div>
                        @endif
                        <div class="text-muted small">
                            @if($receipt->customer->mobile_num)
                                <div><i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $receipt->customer->mobile_num }}</div>
                            @endif
                            @if($receipt->customer->gstin)
                                <div><i class="fas fa-id-card me-1 text-secondary"></i> GSTIN: <strong>{{ $receipt->customer->gstin }}</strong></div>
                            @endif
                            @if($receipt->customer->address)
                                <div class="mt-1"><i class="fas fa-map-marker-alt me-1 text-secondary"></i> {{ $receipt->customer->address }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Statutory Service &amp; Application Reference</h6>
                        <div class="p-3 bg-light rounded border">
                            <div class="fw-bold text-primary mb-1">
                                {{ ucfirst($receipt->application_type ?: 'General Payment') }}
                            </div>
                            <div class="text-dark fw-semibold small">
                                Ref: {{ $receipt->application_reference }}
                            </div>
                            @if($receipt->application)
                                @php
                                    $app = $receipt->application;
                                    $concession = array_filter([$app->village ?? null, $app->taluk ?? null, $app->location ?? null]);
                                @endphp
                                @if(!empty($concession))
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-compass me-1"></i> Location: {{ implode(', ', $concession) }}
                                    </div>
                                @endif
                            @endif
                            @if($receipt->quotation)
                                <div class="text-muted small mt-1">
                                    <i class="fas fa-link me-1"></i> Linked Quotation:
                                    <a href="{{ route('accounts.quotations.show', $receipt->quotation->id) }}" class="text-decoration-none">
                                        {{ $receipt->quotation->quotation_number }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Prominent Amount Highlight Callout -->
                <div class="amount-highlight-box mb-4">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="text-success fw-bold text-uppercase small">Amount Received in Cash / Bank</div>
                            <h2 class="mb-0 fw-bold text-success financial-number">
                                ₹ {{ number_format($receipt->amount_paid, 2) }}
                            </h2>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                            <span class="badge bg-white text-dark border px-3 py-2 fs-6">
                                <i class="fas fa-credit-card text-primary me-1"></i> Mode: {{ $receipt->payment_mode }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Amount in Words Box -->
                <div class="words-box mb-4">
                    <div class="text-muted small fw-semibold text-uppercase mb-1">Indian Currency in Words:</div>
                    <div class="fw-bold fs-6">
                        {{ $receipt->amount_in_words }}
                    </div>
                </div>

                <!-- Financial Statement of Account Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered detail-table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Transaction Parameter</th>
                                <th>Details / Audit Values</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-muted" style="width: 35%;">Payment Mode &amp; Method</td>
                                <td class="fw-bold text-dark">{{ $receipt->payment_mode }}</td>
                            </tr>
                            @if($receipt->bank_name)
                            <tr>
                                <td class="fw-semibold text-muted">Issuing / Depository Bank</td>
                                <td>{{ $receipt->bank_name }}</td>
                            </tr>
                            @endif
                            @if($receipt->reference_number)
                            <tr>
                                <td class="fw-semibold text-muted">UTR / Cheque / Reference No</td>
                                <td class="fw-bold text-dark font-monospace">{{ $receipt->reference_number }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="fw-semibold text-muted">Previously Paid on Application</td>
                                <td class="financial-number">₹ {{ number_format($receipt->previous_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Current Amount Collected</td>
                                <td class="fw-bold text-success financial-number">₹ {{ number_format($receipt->amount_paid, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Remaining Balance Due</td>
                                <td class="financial-number">
                                    @if($receipt->balance_due > 0)
                                        <span class="fw-bold text-danger financial-number">₹ {{ number_format($receipt->balance_due, 2) }}</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Fully Settled (₹ 0.00)</span>
                                    @endif
                                </td>
                            </tr>
                            @if($receipt->notes)
                            <tr>
                                <td class="fw-semibold text-muted">Officer Remarks / Narration</td>
                                <td class="text-secondary fst-italic">{{ $receipt->notes }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @if(isset($siblingReceipts) && $siblingReceipts->count() > 1)
                <!-- Complete Application Installment Chain Schedule -->
                <div class="mb-4">
                    <div class="card border rounded-3 overflow-hidden shadow-sm" style="border-color: #CBD5E1 !important;">
                        <div class="card-header py-2.5 px-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <span class="fw-bold text-dark small">
                                <i class="fas fa-layer-group text-primary me-1"></i> Application Payment Schedule &amp; Installment Chain ({{ $siblingReceipts->count() }} Payments Recorded)
                            </span>
                            <span class="badge bg-primary text-white" style="font-size: 0.70rem;">
                                Active: Installment #{{ $receipt->installment_number }}
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" style="font-size: 0.82rem;">
                                <thead class="table-light text-uppercase text-muted" style="font-size: 0.70rem;">
                                    <tr>
                                        <th class="ps-3 py-2">Installment #</th>
                                        <th>Date</th>
                                        <th>Receipt #</th>
                                        <th>Mode</th>
                                        <th class="text-end">Amount Paid</th>
                                        <th class="text-end">Balance After</th>
                                        <th class="text-center pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siblingReceipts as $idx => $sRec)
                                        <tr class="{{ $sRec->id === $receipt->id ? 'table-primary bg-primary-subtle fw-semibold' : '' }}">
                                            <td class="ps-3 py-2">
                                                <span class="badge {{ $sRec->id === $receipt->id ? 'bg-primary text-white' : 'bg-light text-dark border' }}">
                                                    Installment #{{ $idx + 1 }}
                                                </span>
                                                @if($sRec->id === $receipt->id)
                                                    <span class="badge bg-dark text-white ms-1" style="font-size:0.65rem;">Active Voucher</span>
                                                @endif
                                            </td>
                                            <td>{{ $sRec->transaction_date ? $sRec->transaction_date->format('d M Y') : '-' }}</td>
                                            <td>
                                                <a href="{{ route('accounts.receipts.show', $sRec->id) }}" class="fw-bold text-decoration-none {{ $sRec->id === $receipt->id ? 'text-primary' : 'text-dark' }}">
                                                    {{ $sRec->receipt_number }}
                                                </a>
                                            </td>
                                            <td><span class="badge bg-light text-primary border">{{ $sRec->payment_mode }}</span></td>
                                            <td class="text-end text-success fw-bold">₹ {{ number_format($sRec->amount_paid, 2) }}</td>
                                            <td class="text-end {{ $sRec->balance_due > 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $sRec->balance_due > 0 ? '₹ ' . number_format($sRec->balance_due, 2) : 'Settled' }}
                                            </td>
                                            <td class="text-center pe-3">
                                                <div class="btn-group btn-group-sm">
                                                    @if($sRec->id !== $receipt->id)
                                                        <a href="{{ route('accounts.receipts.show', $sRec->id) }}" class="btn btn-outline-secondary py-0 px-2" style="font-size: 0.72rem; height: 24px;" title="View Voucher">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('accounts.receipts.print', $sRec->id) }}" target="_blank" class="btn btn-outline-primary py-0 px-2" style="font-size: 0.72rem; height: 24px;" title="Print Voucher">
                                                        <i class="fas fa-print"></i>
                                                    </a>
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

                <!-- Audit Footnote & Signatory Zone -->
                <div class="row align-items-end mt-4 pt-3 border-top">
                    <div class="col-sm-7">
                        <div class="small text-muted">
                            <div><i class="fas fa-user-shield me-1"></i> Recorded By: <strong>{{ $receipt->creator->name ?? 'System Officer' }}</strong></div>
                            @if($receipt->branch)
                                <div><i class="fas fa-building me-1"></i> Branch: {{ $receipt->branch->name }}</div>
                            @endif
                            <div><i class="fas fa-clock me-1"></i> System Timestamp: {{ $receipt->created_at ? $receipt->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                        <div class="d-inline-block text-center">
                            @if(file_exists(public_path('images/invoices/gtms_stamp.png')))
                                <img src="{{ asset('images/invoices/gtms_stamp.png') }}" alt="GTMS Official Seal" style="max-height: 70px; opacity: 0.9;" class="mb-1">
                            @endif
                            <div class="fw-bold small text-dark">Authorized Signatory</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Accounts Department &bull; GTMS</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Action Bar -->
            <div class="card-footer bg-light border-top py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Computer-generated official receipt voucher issued under GTMS ERP.</span>
                <a href="{{ route('accounts.receipts.print', ['receipt' => $receipt->id, 'type' => 'tax']) }}" target="_blank" class="btn btn-navy btn-sm">
                    <i class="fas fa-print me-1"></i> Print Tax Invoice
                </a>
            </div>
        </div>
    </div>
</div>
</div>

@include('pages.accounts.partials.export_modal', [
    'id' => 'receiptVoucherExportModal',
    'title' => 'Money Receipt Voucher ' . $receipt->receipt_number,
    'tableSelector' => 'table.detail-table',
    'filename' => 'GTMS_Receipt_' . str_replace('/', '_', $receipt->receipt_number)
])
@endsection
