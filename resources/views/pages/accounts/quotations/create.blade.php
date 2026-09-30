@extends('layouts.app')

@section('title', 'Generate New Quotation • GTMS')

@section('main_content')
@push('styles')
    @include('pages.accounts.partials.theme')
@endpush

<style>
    .form-section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--ct-navy);
        letter-spacing: 0.3px;
        border-bottom: 2px solid #EDF2F7;
        padding-bottom: 8px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .table-items thead th {
        background-color: #F8FAFC;
        color: #334155;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        border-bottom: 2px solid var(--ct-border);
        padding: 10px 12px;
    }
    .table-items tbody td {
        padding: 8px 10px;
        vertical-align: middle;
    }
    .calculation-box {
        background: #F8FAFC;
        border: 1px solid var(--ct-border);
        border-radius: 10px;
        padding: 18px 20px;
    }
    .calc-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px 0;
        font-size: 0.9rem;
    }
    .calc-row.total-row {
        border-top: 2px dashed #CBD5E1;
        margin-top: 8px;
        padding-top: 10px;
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--ct-navy);
    }
    .live-amount-words {
        background: #FEF3C7;
        border: 1px solid #FDE68A;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.85rem;
        font-style: italic;
        color: #92400E;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <!-- Page Title & Navigation -->
        <div class="row page-titles mb-4">
            <div class="col-sm-6 p-md-0 d-flex align-items-center">
                <div class="welcome-text">
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Generate Statutory Quotation
                    </h4>
                    <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                        Prepare commercial proposal with multi-service line items, auto-calculated GST & concession metadata.
                    </p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center">
                <ol class="breadcrumb mb-0 me-3">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('accounts.quotations.index') }}">Quotations</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
                <a href="{{ route('accounts.quotations.index') }}" class="btn btn-light border shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Validation Errors Display -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm" role="alert">
                <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-2"></i>Please correct the following errors:</div>
                <ul class="mb-0 ps-3 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('accounts.quotations.store') }}" method="POST" id="quotationForm">
            @csrf

            <!-- Section 1: Client & Concession Selection -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-user-tie text-primary me-2"></i>1. Client & Quarry Concession Information
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">
                                Select Registered Client <span class="text-danger">*</span>
                            </label>
                            <select name="customer_id" id="customer_select" class="form-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">-- Choose Client Profile --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->customer_name }} {{ $c->company_name ? "({$c->company_name})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">
                                Selecting a client auto-populates contact details and associated quarry concessions.
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">
                                Linked Quarry Concession / Lease Application
                            </label>
                            <select name="lease_application_id" id="lease_application_select" class="form-select">
                                <option value="">-- Select Concession or Enter Manually Below --</option>
                            </select>
                            <div class="form-text small text-muted">
                                Select an active lease concession to autofill village, taluk, survey numbers & area.
                            </div>
                        </div>
                    </div>

                    <!-- Client Details Snapshot Fields -->
                    <div class="form-section-title">
                        <i class="fas fa-id-card text-muted"></i> Client Snapshot (Included on Official Header)
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Contact Person / Client Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Client Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Firm / Company Name</label>
                            <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name') }}" placeholder="M/s. Company Name">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Mobile / Phone Number</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" placeholder="+91 94432 XXXXX">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="client@example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Client GSTIN</label>
                            <input type="text" name="gst_number" id="gst_number" class="form-control text-uppercase" value="{{ old('gst_number') }}" placeholder="33ABCDE1234F1Z5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Registered Billing Address</label>
                            <input type="text" name="address" id="address" class="form-control" value="{{ old('address') }}" placeholder="Door No, Street, Town/District">
                        </div>
                    </div>

                    <!-- Quarry Concession Snapshot Fields -->
                    <div class="form-section-title">
                        <i class="fas fa-mountain text-muted"></i> Quarry Concession & Statutory Location Details
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Quarry / Site Name</label>
                            <input type="text" name="quarry_name" id="quarry_name" class="form-control" value="{{ old('quarry_name') }}" placeholder="e.g. Chinnagoundanur Rough Stone Quarry">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Revenue District</label>
                            <select name="district_id" id="district_id" class="form-select">
                                <option value="">-- Select District --</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Taluk</label>
                            <input type="text" name="taluk" id="taluk" class="form-control" value="{{ old('taluk') }}" placeholder="e.g. Sankari">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Village / Panchayat</label>
                            <input type="text" name="village" id="village" class="form-control" value="{{ old('village') }}" placeholder="e.g. Chinnagoundanur">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Survey Field Nos (S.F. Nos)</label>
                            <input type="text" name="survey_numbers" id="survey_numbers" class="form-control" value="{{ old('survey_numbers') }}" placeholder="e.g. 102/1A, 102/1B, 103/2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Area Extent (Hectares)</label>
                            <div class="input-group">
                                <input type="number" step="0.0001" min="0" name="area_extent_ha" id="area_extent_ha" class="form-control" value="{{ old('area_extent_ha') }}" placeholder="4.5000">
                                <span class="input-group-text small">Ha</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold small">Mineral Name</label>
                            <input type="text" name="mineral_name" id="mineral_name" class="form-control" value="{{ old('mineral_name') }}" placeholder="e.g. Rough Stone / Granite">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Dynamic Multi-Service Line Items -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-tasks text-primary me-2"></i>2. Statutory Service Line Items & Scope
                    </h5>
                    <button type="button" class="btn btn-sm btn-navy" id="btnAddItem">
                        <i class="fas fa-plus me-1"></i> Add Service Line Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-items mb-0" id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Service Name / Catalog</th>
                                    <th style="width: 10%;">SAC Code</th>
                                    <th style="width: 25%;">Scope / Detailed Description</th>
                                    <th style="width: 10%;">Qty / Extent</th>
                                    <th style="width: 8%;">Unit</th>
                                    <th style="width: 11%; text-align: right;">Unit Rate (₹)</th>
                                    <th style="width: 11%; text-align: right;">Subtotal (₹)</th>
                                    <th style="width: 5%; text-align: center;">Act</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTbody">
                                @php
                                    $oldItems = old('items', [
                                        [
                                            'service_name' => 'DGPS Demarcation & Boundary Survey',
                                            'sac_code' => '998334',
                                            'description' => 'Precision DGPS boundary demarcation, pillar fixing, geo-referencing and cadastral overlay map preparation for statutory lease boundary submission.',
                                            'quantity' => 1,
                                            'unit' => 'Ha',
                                            'unit_rate' => 35000.00,
                                        ]
                                    ]);
                                @endphp

                                @foreach($oldItems as $index => $item)
                                <tr class="item-row" data-index="{{ $index }}">
                                    <td>
                                        <div class="input-group input-group-sm mb-1">
                                            <input type="text" name="items[{{ $index }}][service_name]" class="form-control item-service-name" value="{{ $item['service_name'] ?? '' }}" placeholder="Service Name" required>
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Pick from Catalog"></button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm catalog-dropdown">
                                                @foreach($services as $s)
                                                    <li>
                                                        <a class="dropdown-item small pick-service" href="javascript:void(0);"
                                                           data-name="{{ $s['service_name'] }}"
                                                           data-sac="{{ $s['sac_code'] }}"
                                                           data-unit="{{ $s['unit'] }}"
                                                           data-rate="{{ $s['unit_rate'] }}"
                                                           data-desc="{{ $s['description'] }}">
                                                            <strong>{{ $s['service_name'] }}</strong>
                                                            <div class="text-muted" style="font-size:0.75rem;">₹{{ number_format($s['unit_rate'], 2) }} / {{ $s['unit'] }} &bull; SAC: {{ $s['sac_code'] }}</div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][sac_code]" class="form-control form-control-sm item-sac" value="{{ $item['sac_code'] ?? '998341' }}" placeholder="998341">
                                    </td>
                                    <td>
                                        <textarea name="items[{{ $index }}][description]" rows="2" class="form-control form-control-sm item-desc" placeholder="Scope description...">{{ $item['description'] ?? '' }}</textarea>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity]" class="form-control form-control-sm item-qty text-center" value="{{ $item['quantity'] ?? 1 }}" required>
                                    </td>
                                    <td>
                                        <select name="items[{{ $index }}][unit]" class="form-select form-select-sm item-unit">
                                            <option value="Ha" {{ ($item['unit'] ?? '') === 'Ha' ? 'selected' : '' }}>Ha</option>
                                            <option value="Nos" {{ ($item['unit'] ?? '') === 'Nos' ? 'selected' : '' }}>Nos</option>
                                            <option value="Job" {{ ($item['unit'] ?? '') === 'Job' ? 'selected' : '' }}>Job</option>
                                            <option value="Survey" {{ ($item['unit'] ?? '') === 'Survey' ? 'selected' : '' }}>Survey</option>
                                            <option value="Year" {{ ($item['unit'] ?? '') === 'Year' ? 'selected' : '' }}>Year</option>
                                            <option value="Month" {{ ($item['unit'] ?? '') === 'Month' ? 'selected' : '' }}>Month</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_rate]" class="form-control form-control-sm item-rate text-end" value="{{ $item['unit_rate'] ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm item-subtotal text-end fw-bold bg-light" readonly value="0.00">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financial Calculation Summary Card -->
                <div class="card-footer bg-white p-4 border-top">
                    <div class="row align-items-center">
                        <div class="col-lg-6 mb-3 mb-lg-0">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-coins text-warning me-1"></i> Quoted Amount in Words (Indian Rupees)
                            </label>
                            <div class="live-amount-words" id="amountInWordsDisplay">
                                Rupees Zero Only
                            </div>
                            <div class="text-muted small mt-2">
                                <i class="fas fa-shield-alt text-success me-1"></i> Automatic 18% statutory GST (CGST 9% + SGST 9%) computed per Tamil Nadu state mining regulations.
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="calculation-box">
                                <div class="calc-row">
                                    <span class="text-muted">Total Services Subtotal:</span>
                                    <span class="fw-bold financial-number" id="displaySubtotal">₹ 0.00</span>
                                </div>
                                <div class="calc-row">
                                    <span class="text-muted d-flex align-items-center gap-2">
                                        GST Rate:
                                        <select name="tax_rate" id="tax_rate_select" class="form-select form-select-sm" style="width: auto;">
                                            <option value="18.00" {{ old('tax_rate', '18.00') == '18.00' ? 'selected' : '' }}>18% (Standard GST)</option>
                                            <option value="0.00" {{ old('tax_rate') === '0.00' ? 'selected' : '' }}>0% (Exempt / SEZ)</option>
                                            <option value="5.00" {{ old('tax_rate') === '5.00' ? 'selected' : '' }}>5%</option>
                                            <option value="12.00" {{ old('tax_rate') === '12.00' ? 'selected' : '' }}>12%</option>
                                        </select>
                                    </span>
                                    <span class="fw-semibold text-muted" id="displayTaxRateBreakdown">CGST 9% + SGST 9%</span>
                                </div>
                                <div class="calc-row">
                                    <span class="text-muted">Total Statutory GST:</span>
                                    <span class="fw-semibold text-dark financial-number" id="displayTaxAmount">₹ 0.00</span>
                                </div>
                                <div class="calc-row total-row">
                                    <span>Total Quotation Value:</span>
                                    <span class="financial-number" id="displayGrandTotal">₹ 0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Statutory Terms, Conditions & Validity -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-file-contract text-primary me-2"></i>3. Commercial Terms & Statutory Exclusions
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Offer Validity Period</label>
                            <div class="input-group">
                                <input type="number" min="1" max="365" name="validity_days" class="form-control" value="{{ old('validity_days', 30) }}" required>
                                <span class="input-group-text small">Days</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Initial Proposal Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (Internal Review)</option>
                                <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>Sent to Client</option>
                                <option value="accepted" {{ old('status') === 'accepted' ? 'selected' : '' }}>Accepted / Confirmed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Authorized Signatory / Branch</label>
                            <input type="text" class="form-control bg-light" readonly value="Dr. S. Karuppannan, M.Sc., Ph.D. / Managing Partner & RQP">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Payment Terms & Milestones</label>
                            <textarea name="payment_terms" rows="4" class="form-control small">{{ old('payment_terms', "1. 50% Mobilization advance along with confirmed work order.\n2. 30% upon preparation & submission of draft statutory mining / environmental documentation.\n3. 20% upon final statutory clearance, presentation approval & dispatch of statutory order copies.") }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Statutory Government Exclusions</label>
                            <textarea name="exclusions" rows="4" class="form-control small">{{ old('exclusions', "1. Government statutory scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans.\n2. In-person client representation before statutory committees if required.") }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Internal Notes & Special Instructions (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control small" placeholder="Any special notes or concession history...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 border-top d-flex justify-content-between align-items-center">
                    <a href="{{ route('accounts.quotations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-navy px-4" id="submitQuotationBtn">
                        <i class="fas fa-check-circle me-1"></i> Save & Generate Quotation
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

    // Initialize Select2 on customer dropdown
    if (window.jQuery && jQuery.fn.select2 && $('#customer_select').length) {
        $('#customer_select').select2({
            placeholder: '-- Choose Client Profile --',
            allowClear: true,
            width: '100%'
        }).on('change', function () {
            if (customerSelect) {
                customerSelect.dispatchEvent(new Event('change'));
            }
        });
    }

    let loadedConcessions = [];
    let rowIndex = {{ count($oldItems) }};

    // Services Catalog Data
    const catalogServices = @json($services);

    // 1. Customer Concession Resolver
    if (customerSelect) {
        customerSelect.addEventListener('change', function () {
            const customerId = this.value;
            if (!customerId) {
                concessionSelect.innerHTML = '<option value="">-- Select Concession or Enter Manually Below --</option>';
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

    // 2. Add Service Line Item Row
    if (btnAddItem) {
        btnAddItem.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.setAttribute('data-index', rowIndex);

            let optionsHtml = '';
            catalogServices.forEach(s => {
                optionsHtml += `
                    <li>
                        <a class="dropdown-item small pick-service" href="javascript:void(0);"
                           data-name="${escapeHtml(s.service_name)}"
                           data-sac="${escapeHtml(s.sac_code)}"
                           data-unit="${escapeHtml(s.unit)}"
                           data-rate="${s.unit_rate}"
                           data-desc="${escapeHtml(s.description)}">
                            <strong>${escapeHtml(s.service_name)}</strong>
                            <div class="text-muted" style="font-size:0.75rem;">₹${parseFloat(s.unit_rate).toFixed(2)} / ${escapeHtml(s.unit)} &bull; SAC: ${escapeHtml(s.sac_code)}</div>
                        </a>
                    </li>
                `;
            });

            tr.innerHTML = `
                <td>
                    <div class="input-group input-group-sm mb-1">
                        <input type="text" name="items[${rowIndex}][service_name]" class="form-control item-service-name" placeholder="Service Name" required>
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="Pick from Catalog"></button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm catalog-dropdown">
                            ${optionsHtml}
                        </ul>
                    </div>
                </td>
                <td>
                    <input type="text" name="items[${rowIndex}][sac_code]" class="form-control form-control-sm item-sac" value="998341" placeholder="998341">
                </td>
                <td>
                    <textarea name="items[${rowIndex}][description]" rows="2" class="form-control form-control-sm item-desc" placeholder="Scope description..."></textarea>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" class="form-control form-control-sm item-qty text-center" value="1" required>
                </td>
                <td>
                    <select name="items[${rowIndex}][unit]" class="form-select form-select-sm item-unit">
                        <option value="Ha">Ha</option>
                        <option value="Nos">Nos</option>
                        <option value="Job" selected>Job</option>
                        <option value="Survey">Survey</option>
                        <option value="Year">Year</option>
                        <option value="Month">Month</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_rate]" class="form-control form-control-sm item-rate text-end" value="0.00" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm item-subtotal text-end fw-bold bg-light" readonly value="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Remove item">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;

            itemsTbody.appendChild(tr);
            rowIndex++;
            recalculateTotals();
        });
    }

    // 3. Delegate Dynamic Actions (Pick Service, Remove Row, Input Changes)
    itemsTbody.addEventListener('click', function (e) {
        // Remove button
        const removeBtn = e.target.closest('.btn-remove-row');
        if (removeBtn) {
            const rows = itemsTbody.querySelectorAll('.item-row');
            if (rows.length > 1) {
                removeBtn.closest('.item-row').remove();
                recalculateTotals();
            } else {
                alert('At least one statutory service line item is required.');
            }
            return;
        }

        // Pick from catalog dropdown
        const pickItem = e.target.closest('.pick-service');
        if (pickItem) {
            const row = pickItem.closest('.item-row');
            row.querySelector('.item-service-name').value = pickItem.getAttribute('data-name');
            row.querySelector('.item-sac').value = pickItem.getAttribute('data-sac');
            row.querySelector('.item-unit').value = pickItem.getAttribute('data-unit');
            row.querySelector('.item-rate').value = pickItem.getAttribute('data-rate');
            row.querySelector('.item-desc').value = pickItem.getAttribute('data-desc');
            recalculateTotals();
        }
    });

    itemsTbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-rate')) {
            recalculateTotals();
        }
    });

    if (taxRateSelect) {
        taxRateSelect.addEventListener('change', recalculateTotals);
    }

    // 4. Live Calculation Engine
    function recalculateTotals() {
        let subtotal = 0;
        const rows = itemsTbody.querySelectorAll('.item-row');

        rows.forEach(row => {
            const qtyInput = row.querySelector('.item-qty');
            const rateInput = row.querySelector('.item-rate');
            const subtotalInput = row.querySelector('.item-subtotal');

            const qty = parseFloat(qtyInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const lineSubtotal = Math.round(qty * rate * 100) / 100;

            subtotal += lineSubtotal;
            if (subtotalInput) {
                subtotalInput.value = lineSubtotal.toFixed(2);
            }
        });

        const taxRate = parseFloat(taxRateSelect ? taxRateSelect.value : 18.00) || 0;
        const taxAmount = Math.round(subtotal * (taxRate / 100) * 100) / 100;
        const grandTotal = subtotal + taxAmount;

        document.getElementById('displaySubtotal').textContent = '₹ ' + formatIndianNumber(subtotal.toFixed(2));
        document.getElementById('displayTaxAmount').textContent = '₹ ' + formatIndianNumber(taxAmount.toFixed(2));
        document.getElementById('displayGrandTotal').textContent = '₹ ' + formatIndianNumber(grandTotal.toFixed(2));

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
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Generating Quotation...';
        });
    }

    // Initial calculation on load
    recalculateTotals();
});
</script>
@endpush
@endsection
