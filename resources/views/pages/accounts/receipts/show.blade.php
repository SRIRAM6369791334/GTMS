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
                <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                    <i class="fas fa-print me-1"></i> Print Voucher (A4/A5)
                </a>
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
                        <span class="badge bg-warning text-dark fw-bold mb-2 px-3 py-1">OFFICIAL MONEY RECEIPT</span>
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
                <a href="{{ route('accounts.receipts.print', $receipt->id) }}" target="_blank" class="btn btn-navy btn-sm">
                    <i class="fas fa-print me-1"></i> Print / Save as PDF
                </a>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
