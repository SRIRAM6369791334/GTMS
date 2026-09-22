@extends('layouts.app')
@section('title', 'Customers Directory')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<style>
/* ==========================================================================
   CUSTOMERS DIRECTORY - UI/UX PRO MAX THEME POLISH
   ========================================================================== */
.page-titles .breadcrumb-item.active a,
.page-titles .breadcrumb-item.active {
    color: var(--navy-950, #0A1638) !important;
    font-weight: 700;
}

/* Button Navy matching GTMS brand */
.btn-navy {
    background: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    font-weight: 600;
}
.btn-navy:hover {
    background: #1B3A8C !important;
    border-color: #1B3A8C !important;
    color: #ffffff !important;
}

/* Table Action Buttons Group */
.table-action-group {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    flex-wrap: nowrap;
    white-space: nowrap;
}

.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    padding: 0;
}

/* View Button - Soft Navy/Indigo */
.btn-action-view {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #bfdbfe;
}
.btn-action-view:hover {
    background: #1d4ed8;
    color: #ffffff;
    border-color: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(29, 78, 216, 0.25);
}

/* Edit Button - Soft Gold/Amber */
.btn-action-edit {
    background: #fef3c7;
    color: #b45309;
    border-color: #fde68a;
}
.btn-action-edit:hover {
    background: #d97706;
    color: #ffffff;
    border-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
}

/* Delete Button - Soft Red/Danger */
.btn-action-delete {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}
.btn-action-delete:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
}

/* Aadhaar badge matching official identity palette */
.badge-aadhaar {
    background: #e0f2fe !important;
    color: #0369a1 !important;
    border: 1px solid #bae6fd !important;
    font-weight: 600;
}

/* Avatar styling */
.avatar-chip {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #0F1E4D 0%, #1B3A8C 100%) !important;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.85rem;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.15);
    flex-shrink: 0;
}
</style>

    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col-lg-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Customers</a></li>
                    </ol>
                </div>
            </div>
            
            <!-- KPI STATISTIC WIDGETS -->
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-primary text-primary"><i
                                        class="fa fa-users fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Total Customers</p>
                                    <h4 class="mb-0">{{ $totalCustomers ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-success text-success"><i
                                        class="fa fa-user-check fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Active Customers</p>
                                    <h4 class="mb-0">{{ $activeCustomers ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-warning text-warning"><i
                                        class="fa fa-user-clock fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Inactive / Pending</p>
                                    <h4 class="mb-0">{{ $pendingCustomers ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-danger text-danger"><i
                                        class="fa fa-file-contract fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Total Leases</p>
                                    <h4 class="mb-0">{{ $totalLeases ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CUSTOMER DIRECTORY TABLE -->
            <div class="admin-panel mb-4">
                <div class="panel-head d-flex flex-wrap justify-content-between align-items-center gap-3 p-3 border-bottom">
                    <div>
                        <h5 class="mb-1">Customer Directory</h5>
                        <p class="sub text-muted mb-0">Active quarry operators, leaseholders, and corporate clients in GTMS</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <!-- District Quick Filter -->
                        <div class="d-inline-flex align-items-center">
                            <select id="filter_district" class="form-select form-select-sm shadow-none" style="min-width: 150px; font-size: 0.85rem;">
                                <option value="">All Districts</option>
                                @foreach($districts as $d)
                                    <option value="{{ $d->name }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Status Quick Filter -->
                        <div class="d-inline-flex align-items-center">
                            <select id="filter_status" class="form-select form-select-sm shadow-none" style="min-width: 130px; font-size: 0.85rem;">
                                <option value="">All Statuses</option>
                                <option value="Active">Active</option>
                                <option value="Under Validation">Inactive / Pending</option>
                            </select>
                        </div>
                        @can('customer.create')
                        <button class="btn btn-sm btn-navy text-white fw-bold shadow-sm px-3"
                            data-bs-toggle="modal" data-bs-target=".bd-customer-modal-lg"><i class="fa fa-plus me-1"></i> Add Customer</button>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive p-3">
                    <table id="example10" class="table table-hover align-middle mb-0 display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer / Company</th>
                                <th>District</th>
                                <th>Mobile</th>
                                <th>Tax & Identity (PAN / Aadhaar / GSTIN)</th>
                                <th>Status</th>
                                <th class="text-end" style="min-width: 145px; width: 145px; white-space: nowrap;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar-chip">
                                            {{ strtoupper(substr($customer->company_name ?? $customer->customer_name, 0, 2)) }}
                                        </span>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size:.9rem;">{{ $customer->company_name ?? $customer->customer_name }}</div>
                                            <div class="text-muted" style="font-size:.75rem;">
                                                <span class="badge bg-light text-muted border me-1">CUST-{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" title="Customer Unique ID"><i class="fa fa-fingerprint me-1"></i>{{ $customer->mimas_no }}</span>
                                                @if($customer->mimas_number)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle me-1" title="MIMAS Number: {{ $customer->mimas_number }} @if($customer->mimas_status)({{ $customer->mimas_status }})@endif"><i class="fa fa-id-badge me-1"></i>{{ $customer->mimas_number }}</span>
                                                @endif
                                                {{ $customer->customer_name }} &middot; {{ $customer->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $customer->district->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $customer->mobile_num }}</div>
                                    @if($customer->secondary_mobile_num)
                                        <div class="text-muted mt-1" style="font-size:0.72rem;" title="Secondary Contact: {{ $customer->secondary_contact_person ?? 'Alternate' }}">
                                            <span class="badge bg-light text-secondary border py-0 px-1 me-1">Alt</span>{{ $customer->secondary_mobile_num }}
                                            @if($customer->secondary_contact_person)
                                                <span class="text-truncate d-inline-block text-secondary" style="max-width:110px; vertical-align:bottom;">({{ $customer->secondary_contact_person }})</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mb-1">
                                        <span class="badge bg-light text-dark border" title="PAN">{{ $customer->pan }}</span>
                                        @if($customer->aadhaar_no)
                                            <span class="badge badge-aadhaar" title="Aadhaar"><i class="fa fa-id-card me-1"></i>{{ $customer->aadhaar_no }}</span>
                                        @endif
                                    </div>
                                    @if($customer->gstin)
                                        <div class="text-muted small" style="font-size:.72rem;">GST: {{ $customer->gstin }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->status == 1)
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="fa fa-check-circle me-1"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning">
                                            <i class="fa fa-clock me-1"></i> Under Validation
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <div class="table-action-group">
                                        <a href="{{ route('customers.show', $customer->slug ?? $customer->id) }}" class="btn-action-icon btn-action-view" title="View 360° Profile">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        @can('customer.edit')
                                        <button type="button" class="btn-action-icon btn-action-edit editCustomerBtn" 
                                            data-id="{{ $customer->id }}"
                                            data-mimas="{{ $customer->mimas_no }}"
                                            data-mimas-number="{{ $customer->mimas_number }}"
                                            data-mimas-status="{{ $customer->mimas_status }}"
                                            data-name="{{ $customer->customer_name }}"
                                            data-secondary-contact="{{ $customer->secondary_contact_person }}"
                                            data-company="{{ $customer->company_name }}"
                                            data-mobile="{{ $customer->mobile_num }}"
                                            data-secondary-mobile="{{ $customer->secondary_mobile_num }}"
                                            data-email="{{ $customer->email }}"
                                            data-district-id="{{ $customer->district_id }}"
                                            data-mineral-id="{{ $customer->mineral_id }}"
                                            data-area="{{ $customer->area }}"
                                            data-pan="{{ $customer->pan }}"
                                            data-aadhaar="{{ $customer->aadhaar_no }}"
                                            data-gstin="{{ $customer->gstin }}"
                                            data-status="{{ $customer->status }}"
                                            data-address="{{ $customer->address }}"
                                            title="Edit Customer">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('customer.delete')
                                        <button type="button" class="btn-action-icon btn-action-delete deleteCustomerBtn" 
                                            data-id="{{ $customer->id }}" 
                                            data-name="{{ $customer->company_name ?? $customer->customer_name }}" 
                                            title="Delete Customer">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa fa-folder-open fa-2x mb-2 d-block"></i>
                                    No customers found. Click "Add Customer" to create the first record.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- ========================================================================= -->
<!-- MODAL: ADD CUSTOMER                                                       -->
<!-- ========================================================================= -->
<div class="modal fade bd-customer-modal-lg" tabindex="-1" id="customerModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="customeradd" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-bold">Customer Unique ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-fingerprint text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_no" placeholder="e.g. CUST-001 or TN-MMS-2026/001" required>
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Customer Master ID</div>
                        <span class="text-danger error-text mimas_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_number" placeholder="e.g. TN-MMS-2026/0482">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">State Mining Portal registration number</div>
                        <span class="text-danger error-text mimas_number_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-info-circle text-primary"></i></span>
                            <input type="text" class="form-control" name="mimas_status" placeholder="e.g. Active / Registered">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Input status in State MIMAS portal</div>
                        <span class="text-danger error-text mimas_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Customer / Representative Name (Primary) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" placeholder="Full Name" required>
                        <span class="text-danger error-text customer_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Company / Quarry Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="company_name" placeholder="Company Name" required>
                        <span class="text-danger error-text company_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_num" placeholder="10-digit Mobile" required maxlength="15">
                        <span class="text-danger error-text mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" placeholder="customer@example.com">
                        <span class="text-danger error-text email_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Contact Person Name <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="secondary_contact_person" placeholder="e.g. Quarry Manager / Supervisor">
                        <span class="text-danger error-text secondary_contact_person_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Mobile Number <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="secondary_mobile_num" placeholder="10-digit Alternate Mobile" maxlength="15">
                        <span class="text-danger error-text secondary_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select class="form-control" name="district_id" required>
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text district_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status">
                            <option value="1">Active</option>
                            <option value="0">Under Validation / Inactive</option>
                        </select>
                        <span class="text-danger error-text status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mineral</label>
                        <select class="form-control" name="mineral_id">
                            <option value="">Select Primary Mineral (Optional)</option>
                            @foreach($minerals as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->type ?? 'Major/Minor' }})</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text mineral_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Quarry / Lease Extent Area (Hectares)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-ruler-combined text-primary"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" name="area" placeholder="e.g. 4.50">
                            <span class="input-group-text">Ha</span>
                        </div>
                        <span class="text-danger error-text area_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="pan" placeholder="AAACS1234F" required>
                        <span class="text-danger error-text pan_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-card text-primary"></i></span>
                            <input type="text" class="form-control aadhaar-format" name="aadhaar_no" placeholder="9876-5432-1012" maxlength="14" required>
                        </div>
                        <span class="text-danger error-text aadhaar_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">GSTIN (15 Digits)</label>
                        <input type="text" class="form-control text-uppercase" name="gstin" placeholder="33AAACS1234F1Z5">
                        <span class="text-danger error-text gstin_error"></span>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Registered Office / Business Address</label>
                        <textarea class="form-control" name="address" rows="2" placeholder="Street, Taluk, Village, Pincode..."></textarea>
                        <span class="text-danger error-text address_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Customer</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: VIEW CUSTOMER PROFILE                                              -->
<!-- ========================================================================= -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center border-end p-3">
                        <span class="avatar-chip mx-auto mb-2" id="view_avatar_initials" style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#3350c9,#7a52a8);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.4rem;">
                            CU
                        </span>
                        <h5 class="fw-bold mb-1" id="view_company_title">-</h5>
                        <p class="text-muted small mb-2" id="view_rep_subtitle">-</p>
                        <div id="view_status_badge"></div>
                    </div>
                    <div class="col-md-8 p-3">
                        <div class="row g-2">
                            <div class="col-12"><small class="text-muted d-block">Customer Unique ID</small><span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold" id="view_mimas_no">-</span></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">MIMAS Number</small><b id="view_mimas_number" class="text-dark font-monospace">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">MIMAS Status</small><span class="badge bg-light text-dark border" id="view_mimas_status">-</span></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Primary Contact</small><b id="view_rep">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Primary Mobile</small><b id="view_mobile">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Secondary Contact</small><b id="view_secondary_contact">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Secondary Mobile</small><b id="view_secondary_mobile">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Email Address</small><b id="view_email">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Home District</small><b id="view_district">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Primary Mineral</small><b id="view_mineral">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Quarry Extent (Area)</small><b id="view_area">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">PAN Number</small><b id="view_pan">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">Aadhaar Number</small><b id="view_aadhaar_no">-</b></div>
                            <div class="col-6 mt-2"><small class="text-muted d-block">GSTIN</small><b id="view_gstin">-</b></div>
                            <div class="col-12 mt-2"><small class="text-muted d-block">Registered Business Address</small><span id="view_address">-</span></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL: EDIT CUSTOMER                                                      -->
<!-- ========================================================================= -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="customeredit" class="modal-content">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-bold">Customer Unique ID <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-fingerprint text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_no" id="edit_mimas_no" required>
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Customer Master ID</div>
                        <span class="text-danger error-text edit_mimas_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Number</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-badge text-primary"></i></span>
                            <input type="text" class="form-control text-uppercase fw-semibold" name="mimas_number" id="edit_mimas_number" placeholder="e.g. TN-MMS-2026/0482">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">State Mining Portal registration number</div>
                        <span class="text-danger error-text edit_mimas_number_error"></span>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label fw-semibold">MIMAS Status</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-info-circle text-primary"></i></span>
                            <input type="text" class="form-control" name="mimas_status" id="edit_mimas_status" placeholder="e.g. Active / Registered">
                        </div>
                        <div class="form-text text-muted" style="font-size:0.72rem;">Input status in State MIMAS portal</div>
                        <span class="text-danger error-text edit_mimas_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Customer / Representative Name (Primary) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="customer_name" id="edit_customer_name" required>
                        <span class="text-danger error-text edit_customer_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Company / Quarry Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="company_name" id="edit_company_name" required>
                        <span class="text-danger error-text edit_company_name_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="mobile_num" id="edit_mobile_num" required maxlength="15">
                        <span class="text-danger error-text edit_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" id="edit_email">
                        <span class="text-danger error-text edit_email_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Contact Person Name</label>
                        <input type="text" class="form-control" name="secondary_contact_person" id="edit_secondary_contact_person" placeholder="e.g. Quarry Manager">
                        <span class="text-danger error-text edit_secondary_contact_person_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Secondary Mobile Number</label>
                        <input type="text" class="form-control" name="secondary_mobile_num" id="edit_secondary_mobile_num" placeholder="10-digit Alternate Mobile" maxlength="15">
                        <span class="text-danger error-text edit_secondary_mobile_num_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select class="form-control" name="district_id" id="edit_district_id" required>
                            <option value="">Select District</option>
                            @foreach($districts as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text edit_district_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-control" name="status" id="edit_status">
                            <option value="1">Active</option>
                            <option value="0">Under Validation / Inactive</option>
                        </select>
                        <span class="text-danger error-text edit_status_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Primary Mineral</label>
                        <select class="form-control" name="mineral_id" id="edit_mineral_id">
                            <option value="">Select Primary Mineral (Optional)</option>
                            @foreach($minerals as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->type ?? 'Major/Minor' }})</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text edit_mineral_id_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Quarry / Lease Extent Area (Hectares)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-ruler-combined text-primary"></i></span>
                            <input type="number" step="0.01" min="0" class="form-control" name="area" id="edit_area" placeholder="e.g. 4.50">
                            <span class="input-group-text">Ha</span>
                        </div>
                        <span class="text-danger error-text edit_area_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control text-uppercase" name="pan" id="edit_pan" required>
                        <span class="text-danger error-text edit_pan_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-id-card text-primary"></i></span>
                            <input type="text" class="form-control aadhaar-format" name="aadhaar_no" id="edit_aadhaar_no" maxlength="14" required>
                        </div>
                        <span class="text-danger error-text edit_aadhaar_no_error"></span>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label">GSTIN (15 Digits)</label>
                        <input type="text" class="form-control text-uppercase" name="gstin" id="edit_gstin">
                        <span class="text-danger error-text edit_gstin_error"></span>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label class="form-label">Registered Office / Business Address</label>
                        <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
                        <span class="text-danger error-text edit_address_error"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update Customer</button>
            </div>
        </form>
    </div>
</div>

@endsection
@section('scripts')
<script src="{{ asset('js/ajax/customer.js') }}"></script>
@endsection
