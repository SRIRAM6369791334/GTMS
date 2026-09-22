@extends('layouts.app')
@section('title', isset($application) ? 'Process Flow — ' . $application->application_no : 'Mining Process Applications')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ========================================================================= --}}
            {{-- CASE 1: NO ID PROVIDED -> SHOW TABLE OF ALL APPLICATIONS (LIKE /application) --}}
            {{-- ========================================================================= --}}
            @if(!isset($application))
                <div class="row page-titles mb-3">
                    <div class="col-lg-6">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('miningplan.index') }}">Mining Portal</a></li>
                            <li class="breadcrumb-item active"><a href="javascript:void(0)">Process Workflow</a></li>
                        </ol>
                    </div>
                    <div class="col-lg-6 text-end">
                        @can('mining.create')
                            <a href="{{ route('newapplication') }}" class="btn btn-navy">
                                <i class="fa fa-plus me-1"></i>New Application
                            </a>
                        @endcan
                    </div>
                </div>

                <div class="card border shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Stage 6.1 – 6.6 Process Flow &amp; Validation Applications</h5>
                            <span class="text-muted small">Select any application to review documents, validate files, and manage the statutory approval loop.</span>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            Total: {{ $applications->total() }} In Process
                        </span>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">#</th>
                                        <th>Application No</th>
                                        <th>Client / Enterprise</th>
                                        <th>MIMAS Details</th>
                                        <th>Nature of Work</th>
                                        <th>District</th>
                                        <th>Current Stage</th>
                                        <th>Validation Status</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        @php
                                            $valDocs = $app->documents->where('status', 'validated')->count();
                                            $flaggedDocs = $app->documents->where('status', 'revision_required')->count();
                                            $upDocs = $app->documents->where('status', 'uploaded')->count();
                                            $totalDocs = $app->documents->count();
                                        @endphp
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('process', ['id' => $app->id]) }}" class="fw-bold text-primary text-decoration-none font-monospace">
                                                    {{ $app->application_no }}
                                                </a>
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    <span class="badge bg-light text-dark border font-monospace" style="font-size: 11px;" title="Universal Common Tracking ID">
                                                        <i class="bi bi-fingerprint text-primary me-1"></i>{{ $app->common_id ?? ('GTMS-' . date('Y', strtotime($app->created_at ?? 'now')) . '-' . str_pad($app->id, 4, '0', STR_PAD_LEFT)) }}
                                                    </span>
                                                    @if($app->lease_application_id)
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 10px;" title="Promoted from Lease Application">
                                                            <i class="fa fa-link me-1"></i>Lease #{{ $app->leaseApplication->application_no ?? $app->lease_application_id }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $app->customer->company_name ?? $app->customer->customer_name }}</div>
                                                <div class="text-muted" style="font-size:11px;">
                                                    <i class="bi bi-telephone text-primary me-1"></i>{{ $app->customer?->mobile_num ?? 'N/A' }}
                                                    @if(!empty($app->customer?->secondary_mobile_num))
                                                        <span class="badge" style="font-size:0.65rem; background:#e0e7ff; color:#4338ca; border:1px solid #c7d2fe; padding:1px 3px; margin-left:3px;">Alt</span>
                                                        <span class="text-secondary">{{ $app->customer->secondary_mobile_num }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($app->customer?->mimas_number))
                                                    <span class="fw-bold text-dark d-block" style="font-size: 12px;">
                                                        <i class="fa fa-id-badge text-primary me-1"></i>{{ $app->customer->mimas_number }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">&mdash;</span>
                                                @endif
                                                @if(!empty($app->customer?->mimas_status))
                                                    @php
                                                        $mStatus = strtolower(trim($app->customer->mimas_status));
                                                        $mBadgeClass = match(true) {
                                                            str_contains($mStatus, 'approv') => 'bg-success-subtle text-success border border-success-subtle',
                                                            str_contains($mStatus, 'reject') => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                            str_contains($mStatus, 'pend') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                            str_contains($mStatus, 'appl') || str_contains($mStatus, 'submit') => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                            default => 'bg-info-subtle text-info border border-info-subtle',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $mBadgeClass }} mt-1" style="font-size: 10px;">
                                                        {{ $app->customer->mimas_status }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                    {{ $app->natureOfWork->name ?? 'Mining Plan' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $app->district->name ?? 'N/A' }}</div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-navy border px-2 py-1">
                                                    Stage {{ $app->stage }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($flaggedDocs > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                        <i class="bi bi-exclamation-triangle me-1"></i>{{ $flaggedDocs }} Flagged
                                                    </span>
                                                @elseif($valDocs == $totalDocs && $totalDocs > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                        <i class="bi bi-check2-all me-1"></i>All Validated
                                                    </span>
                                                @elseif($upDocs > 0)
                                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                        {{ $upDocs }} Ready for Review
                                                    </span>
                                                @else
                                                    <span class="badge bg-light text-muted border">Upload In Progress</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($app->status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($app->status === 'draft')
                                                    <span class="badge bg-secondary">Draft</span>
                                                @elseif($app->status === 'scrutiny')
                                                    <span class="badge bg-warning text-dark">In Scrutiny</span>
                                                @else
                                                    <span class="badge bg-info">{{ ucfirst($app->status) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <a href="{{ route('process', ['id' => $app->id]) }}" class="btn btn-sm btn-info text-white shadow-sm fw-semibold">
                                                    <i class="bi bi-diagram-3 me-1"></i>View Process &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="bi bi-diagram-3 fs-1 d-block mb-2 text-secondary"></i>
                                                No mining applications found.
                                                <div class="mt-3">
                                                    <a href="{{ route('newapplication') }}" class="btn btn-navy btn-sm">
                                                        <i class="fa fa-plus me-1"></i>Register First Application
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($applications->hasPages())
                        <div class="card-footer bg-white border-top py-3">
                            {{ $applications->links() }}
                        </div>
                    @endif
                </div>

            {{-- ========================================================================= --}}
            {{-- CASE 2: ID PROVIDED -> SHOW FULL PROCESS WORKFLOW & VALIDATION LOOP --}}
            {{-- ========================================================================= --}}
            @else
                <main class="page">
                    <!-- Top Breadcrumb & Switcher Header -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <div>
                            <div class="breadcrumb-min mb-1">
                                <a href="{{ route('process') }}" class="text-primary text-decoration-none fw-semibold">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Process Index
                                </a> 
                                &nbsp;/&nbsp; <span class="text-muted">{{ $application->application_no }}</span>
                            </div>
                            <h1 class="h3 fw-bold mt-1 mb-0">
                                {{ $application->customer->company_name ?? $application->customer->customer_name }}
                                <span class="badge bg-primary fs-6 fw-normal ms-2">{{ $application->natureOfWork->name ?? 'Mining Application' }}</span>
                            </h1>
                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                <span class="badge bg-navy text-white px-3 py-1 fw-bold" style="font-size: .82rem; letter-spacing: 0.03em;">
                                    <i class="bi bi-fingerprint me-1 text-warning"></i> Universal Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y', strtotime($application->created_at ?? 'now')) . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
                                </span>
                                @if($application->customer?->mimas_no)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: .8rem;" title="Customer Unique ID">
                                        <i class="fa fa-fingerprint me-1"></i> Cust ID: <b>{{ $application->customer->mimas_no }}</b>
                                    </span>
                                @endif
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: .8rem;">
                                    Mining App No: <b>{{ $application->application_no }}</b>
                                </span>
                                @if($application->lease_application_id)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: .8rem;">
                                        <i class="fa fa-link me-1"></i> Origin: Lease #{{ $application->leaseApplication->application_no ?? $application->lease_application_id }}
                                    </span>
                                @endif
                                @if($application->customer?->mimas_number)
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: .8rem;" title="MIMAS Number">
                                        <i class="fa fa-id-badge text-primary me-1"></i> MIMAS No: <b>{{ $application->customer->mimas_number }}</b>
                                    </span>
                                @endif
                                @if($application->customer?->mimas_status)
                                    @php
                                        $topMStatus = strtolower(trim($application->customer->mimas_status));
                                        $topMBadgeClass = match(true) {
                                            str_contains($topMStatus, 'approv') => 'bg-success-subtle text-success border border-success-subtle',
                                            str_contains($topMStatus, 'reject') => 'bg-danger-subtle text-danger border border-danger-subtle',
                                            str_contains($topMStatus, 'pend') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                            str_contains($topMStatus, 'appl') || str_contains($topMStatus, 'submit') => 'bg-primary-subtle text-primary border border-primary-subtle',
                                            default => 'bg-info-subtle text-info border border-info-subtle',
                                        };
                                    @endphp
                                    <span class="badge {{ $topMBadgeClass }} px-2 py-1" style="font-size: .8rem;" title="MIMAS Status">
                                        <i class="fa fa-tag me-1"></i> MIMAS Status: <b>{{ $application->customer->mimas_status }}</b>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Switcher Dropdown & Actions -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if(isset($allApplications) && $allApplications->count() > 1)
                                <div class="d-flex align-items-center gap-1">
                                    <span class="small text-muted d-none d-md-inline">Switch Application:</span>
                                    <select class="form-select form-select-sm" style="min-width: 220px;" onchange="if(this.value) window.location.href='/process?id='+this.value;">
                                        @foreach($allApplications as $optApp)
                                            <option value="{{ $optApp->id }}" {{ $optApp->id == $application->id ? 'selected' : '' }}>
                                                {{ $optApp->application_no }} &mdash; {{ $optApp->customer->company_name ?? $optApp->customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            @if($application->status === 'draft' || $application->stage === '6.1')
                                <a href="{{ route('newapplication', ['resume' => $application->id]) }}" class="btn btn-sm text-white shadow-sm" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); font-weight: 500;">
                                    <i class="fa fa-play me-1"></i>Resume / Edit Application
                                </a>
                            @endif
                            @php
                                $linkedEnv = \App\Models\EnvironmentProject::where('mining_application_id', $application->id)->first();
                            @endphp
                            @if($linkedEnv)
                                <a href="{{ route('environment-b2.show', $linkedEnv) }}" class="btn btn-sm btn-outline-success">
                                    <i class="fa fa-leaf me-1"></i>Environment: {{ $linkedEnv->project_code }} ↗
                                </a>
                            @endif
                            <a href="{{ route('projectfolder', ['id' => $application->id]) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-folder2 me-1"></i>Folder Dossier
                            </a>
                            <a href="{{ route('document', ['id' => $application->id]) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-upload me-1"></i>Manage Files
                            </a>
                        </div>
                    </div>

                    {{-- PARENT LEASE APPLICATION INFORMATION CARD (IF TRANSITIONED FROM LEASE) --}}
                    @if($application->leaseApplication)
                    <div class="card border mb-4 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="p-2 rounded bg-primary-subtle text-primary"><i class="fa fa-id-card"></i></span>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">Parent Lease Application Information (Origin: {{ $application->leaseApplication->application_no }})</h6>
                                    <small class="text-muted">Previous application details and statutory land concession parameters</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('viewapplication', ['id' => $application->leaseApplication->id]) }}" target="_blank" class="btn btn-sm btn-navy">
                                    <i class="fa fa-file-contract me-1"></i> View Original Lease Dossier ↗
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-3 bg-light-subtle">
                            <div class="row g-3" style="font-size: 0.85rem;">
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Universal Common ID</span>
                                    <strong class="text-primary font-monospace">{{ $application->common_id }}</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Customer Unique ID</span>
                                    <strong class="text-dark font-monospace">{{ $application->customer?->mimas_no ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">MIMAS Number</span>
                                    <strong class="text-dark font-monospace">
                                        @if(!empty($application->customer?->mimas_number))
                                            <i class="fa fa-id-badge text-primary me-1"></i>{{ $application->customer->mimas_number }}
                                        @else
                                            <span class="text-muted fst-italic">Not assigned</span>
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">MIMAS Status</span>
                                    @if(!empty($application->customer?->mimas_status))
                                        @php
                                            $cardMStatus = strtolower(trim($application->customer->mimas_status));
                                            $cardMBadgeClass = match(true) {
                                                str_contains($cardMStatus, 'approv') => 'bg-success-subtle text-success border border-success-subtle',
                                                str_contains($cardMStatus, 'reject') => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                str_contains($cardMStatus, 'pend') => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                                                str_contains($cardMStatus, 'appl') || str_contains($cardMStatus, 'submit') => 'bg-primary-subtle text-primary border border-primary-subtle',
                                                default => 'bg-info-subtle text-info border border-info-subtle',
                                            };
                                        @endphp
                                        <span class="badge {{ $cardMBadgeClass }}" style="font-size: 11px;">
                                            {{ $application->customer->mimas_status }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Not specified</span>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Original Lease App No</span>
                                    <strong class="text-dark font-monospace">{{ $application->leaseApplication->application_no }}</strong>
                                    @if($application->leaseApplication->go_number)
                                        <div class="text-muted small">GO: {{ $application->leaseApplication->go_number }}</div>
                                    @endif
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Lease Status</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="fa fa-check-circle me-1"></i> {{ ucfirst($application->leaseApplication->status) }}
                                    </span>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Lease Period</span>
                                    <strong class="text-dark">{{ $application->leaseApplication->lease_period_years ?? 5 }} Years</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Quarry Extent</span>
                                    <strong class="text-dark">{{ $application->area_extent_ha ? ($application->area_extent_ha . ' Ha') : 'N/A' }}</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Survey &amp; Sub-div Nos</span>
                                    <strong class="text-dark">{{ $application->survey_numbers_text ?: 'SF.No 1' }}</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Location</span>
                                    <strong class="text-dark">{{ $application->village ?? '-' }}, {{ $application->taluk ?? '-' }} ({{ $application->district->name ?? 'N/A' }})</strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Mineral Concession</span>
                                    <strong class="text-primary">
                                        @if($application->minerals && $application->minerals->isNotEmpty())
                                            {{ $application->minerals->pluck('name')->implode(', ') }}
                                        @elseif($application->mineral)
                                            {{ $application->mineral->name }}
                                        @else
                                            N/A
                                        @endif
                                        @if(!empty($application->other_mineral_name))
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1" style="font-size:.7rem;">Other: {{ $application->other_mineral_name }}</span>
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Primary Contact</span>
                                    <strong class="text-dark">{{ $application->leaseApplication->contact_person ?? ($application->customer?->customer_name ?? 'N/A') }}</strong>
                                    <div class="text-muted small"><i class="fa fa-phone me-1"></i>{{ $application->leaseApplication->contact_mobile ?? ($application->customer?->mobile_num ?? 'N/A') }}</div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <span class="text-muted d-block small">Secondary / Site Contact</span>
                                    @if(!empty($application->leaseApplication->secondary_contact_person) || !empty($application->leaseApplication->secondary_contact_mobile) || !empty($application->customer?->secondary_contact_person) || !empty($application->customer?->secondary_mobile_num))
                                        <strong class="text-dark">{{ $application->leaseApplication->secondary_contact_person ?? ($application->customer?->secondary_contact_person ?? 'N/A') }}</strong>
                                        <div class="text-muted small"><i class="fa fa-phone me-1 text-indigo"></i>{{ $application->leaseApplication->secondary_contact_mobile ?? ($application->customer?->secondary_mobile_num ?? 'N/A') }}</div>
                                    @else
                                        <span class="text-muted fst-italic">Not provided</span>
                                    @endif
                                </div>
                            </div>

                            @php
                                $clonedDocs = $application->documents->whereNotNull('file_path');
                            @endphp
                            @if($clonedDocs->count() > 0)
                            <div class="mt-3 pt-3 border-top">
                                <span class="text-uppercase fw-bold text-muted d-block mb-2" style="font-size: .72rem; letter-spacing: .06em;">
                                    <i class="fa fa-paperclip me-1 text-primary"></i> Attached Statutory Files ({{ $clonedDocs->count() }} Available):
                                </span>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($clonedDocs as $cd)
                                        <a href="{{ asset($cd->file_path) }}" target="_blank" class="badge bg-white text-dark border p-2 text-decoration-none shadow-sm d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <span>{{ $cd->document_name }}</span>
                                            <i class="bi bi-box-arrow-up-right text-muted ms-1" style="font-size:10px;"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- 6.1 – 6.6 Process Flow Track -->
                    @php
                        $stage = $application->stage ?? '6.1';
                    @endphp
                    <div class="surface p-3 p-lg-4 mb-4 rounded-3 border shadow-sm bg-white">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <h6 class="fw-bold small text-uppercase text-secondary mb-0">Process Flow (Stages 6.1 &ndash; 6.6)</h6>
                            <span class="badge bg-light text-navy border">Current Stage: {{ $stage }}</span>
                        </div>

                        <div class="row g-2 text-center">
                            {{-- 6.1 Upload & Store --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 {{ $stage === '6.1' ? 'border-primary shadow-sm bg-primary-subtle' : ($stage > '6.1' ? 'bg-light border-success' : 'bg-light text-muted') }}">
                                    <div class="fw-bold fs-6">6.1</div>
                                    <div class="fw-semibold small">Upload &amp; Store</div>
                                    <div class="mt-2">
                                        @if($stage > '6.1')
                                            <span class="badge bg-success"><i class="bi bi-check2"></i> Done</span>
                                        @elseif($stage === '6.1')
                                            <span class="badge bg-primary">In Progress</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 6.2 Validate Data --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 {{ $stage === '6.2' ? 'border-primary shadow-sm bg-warning-subtle' : ($stage > '6.2' ? 'bg-light border-success' : 'bg-light text-muted') }}">
                                    <div class="fw-bold fs-6">6.2</div>
                                    <div class="fw-semibold small">Validate Data</div>
                                    <div class="mt-2">
                                        @if($stage > '6.2')
                                            <span class="badge bg-success"><i class="bi bi-check2"></i> Validated</span>
                                        @elseif($stage === '6.2')
                                            <span class="badge bg-warning text-dark">In Scrutiny</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Waiting</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 6.3 Approve Data --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 {{ $stage === '6.3' ? 'border-primary shadow-sm bg-success-subtle' : ($stage > '6.3' ? 'bg-light border-success' : 'bg-light text-muted') }}">
                                    <div class="fw-bold fs-6">6.3</div>
                                    <div class="fw-semibold small">Approve Data</div>
                                    <div class="mt-2">
                                        @if($stage > '6.3')
                                            <span class="badge bg-success"><i class="bi bi-check2"></i> Approved</span>
                                        @elseif($stage === '6.3')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 6.4 Generate Reports --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 {{ $stage === '6.4' ? 'border-primary shadow-sm bg-info-subtle' : ($stage > '6.4' ? 'bg-light border-success' : 'bg-light text-muted') }}">
                                    <div class="fw-bold fs-6">6.4</div>
                                    <div class="fw-semibold small">Generate Reports</div>
                                    <div class="mt-2">
                                        @if($stage > '6.4')
                                            <span class="badge bg-success"><i class="bi bi-check2"></i> Done</span>
                                        @elseif($stage === '6.4')
                                            <span class="badge bg-info">Ready</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 6.5 Archive & Backup --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 {{ $stage === '6.5' ? 'border-primary shadow-sm bg-purple-subtle' : ($stage > '6.5' ? 'bg-light border-success' : 'bg-light text-muted') }}">
                                    <div class="fw-bold fs-6">6.5</div>
                                    <div class="fw-semibold small">Archive &amp; Backup</div>
                                    <div class="mt-2">
                                        @if($stage >= '6.5')
                                            <span class="badge bg-success"><i class="bi bi-check2"></i> Archived</span>
                                        @else
                                            <span class="badge bg-light text-muted border">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 6.6 Logout --}}
                            <div class="col-6 col-md-2">
                                <div class="p-3 rounded-3 border h-100 bg-light text-muted">
                                    <div class="fw-bold fs-6">6.6</div>
                                    <div class="fw-semibold small">Session Close</div>
                                    <div class="mt-2">
                                        <span class="badge bg-light text-muted border">Logout</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Flowchart Validation Loop Status Box -->
                    @php
                        $totalDocs = count($documents);
                        $isAllValidated = ($totalDocs > 0 && $validatedCount === $totalDocs);
                        $hasFlags = ($rejectedCount > 0);
                    @endphp

                    @if($isAllValidated)
                        <div class="card border-success shadow-sm mb-4" style="background:#f0fdf4;">
                            <div class="card-body p-4 text-center">
                                <div class="text-success fs-1 mb-2">🏆</div>
                                <h4 class="fw-bold text-success mb-1">Validation Succeeded &mdash; Mining Plan Completed!</h4>
                                <p class="text-muted small mb-3">All statutory checklist documents have been reviewed and validated by the Mines Department.</p>
                                
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <form method="POST" action="{{ route('mining.application.stage', $application->id) }}">
                                        @csrf
                                        <input type="hidden" name="stage" value="6.3">
                                        <button type="submit" class="btn btn-success"><i class="bi bi-patch-check me-1"></i>Approve Data (Stage 6.3)</button>
                                    </form>
                                    <form method="POST" action="{{ route('mining.application.stage', $application->id) }}">
                                        @csrf
                                        <input type="hidden" name="stage" value="6.4">
                                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-cloud-upload me-1"></i>Final MIMAS Upload &amp; Reports (Stage 6.4)</button>
                                    </form>
                                    @can('mining.edit')
                                        @if(!$linkedEnv)
                                            <form method="POST" action="{{ route('mining.application.moveToEnvironment', $application->id) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="category" value="B2">
                                                <button type="submit" class="btn btn-navy text-white">
                                                    <i class="bi bi-tree me-1"></i>Advance to Environment Clearance (B2)
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('environment-b2.show', $linkedEnv) }}" class="btn btn-navy text-white">
                                                <i class="bi bi-tree me-1"></i>View Environment Project ({{ $linkedEnv->project_code }})
                                            </a>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @elseif($hasFlags)
                        <div class="card border-danger shadow-sm mb-4" style="background:#fef2f2;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-3 rounded-circle bg-danger-subtle text-danger fs-3">
                                        <i class="bi bi-exclamation-octagon-fill"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold text-danger mb-1">Validation Loop Triggered: Correction Required</h5>
                                        <p class="text-muted small mb-0">
                                            <strong>{{ $rejectedCount }}</strong> document(s) were flagged by the Mines Department. Review the feedback, re-upload corrected files, and revalidate.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card border-primary shadow-sm mb-4" style="background:#f8fafc;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded bg-primary-subtle text-primary fs-4"><i class="bi bi-arrow-repeat"></i></div>
                                    <div>
                                        <div class="fw-bold small text-dark">Stage 6.2 Scrutiny &amp; Validation Loop</div>
                                        <div class="text-muted small">
                                            {{ $validatedCount }} Validated &middot; {{ $uploadedCount }} Uploaded &middot; {{ $rejectedCount }} Flagged &middot; {{ $pendingCount }} Pending
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('mining.application.stage', $application->id) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="stage" value="6.2">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-search me-1"></i>Mark in Scrutiny
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Validation Checklist Queue -->
                    <div class="surface p-3 p-lg-4 rounded-3 border shadow-sm bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <h2 class="h5 fw-bold mb-0 text-dark">Mines Department Validation Queue</h2>
                                <span class="text-muted small">Review each checklist document and mark as Validated or Flag for Revision.</span>
                            </div>
                            <span class="badge bg-secondary px-3 py-2">
                                Total {{ $totalDocs }} items
                            </span>
                        </div>

                        @if(count($documents) === 0)
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">No documents generated yet for this application.</p>
                            </div>
                        @else
                            <div class="d-flex flex-column gap-2">
                                @foreach($documents as $doc)
                                    <div class="p-3 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#f8fafc;">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-2 rounded" style="background:{{ $doc->status === 'validated' ? '#dcfce7; color:#166534;' : ($doc->status === 'revision_required' ? '#fee2e2; color:#991b1b;' : ($doc->status === 'uploaded' ? '#e0f2fe; color:#0369a1;' : '#f1f5f9; color:#64748b;')) }}">
                                                @if($doc->status === 'validated')
                                                    <i class="fa fa-check-circle fs-5"></i>
                                                @elseif($doc->status === 'revision_required')
                                                    <i class="fa fa-times-circle fs-5"></i>
                                                @elseif($doc->status === 'uploaded')
                                                    <i class="fa fa-file-text fs-5"></i>
                                                @else
                                                    <i class="fa fa-clock-o fs-5"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold small text-dark">{{ $doc->document_name }}</div>
                                                <div class="text-muted" style="font-size:11.5px;">
                                                    Folder: <strong>{{ $doc->folder->name ?? 'General' }}</strong> &middot; 
                                                    @if($doc->file_name)
                                                        File: <span class="text-primary">{{ $doc->file_name }}</span> &middot; 
                                                        Uploaded {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y') : '' }}
                                                    @else
                                                        <span class="text-danger">File not yet uploaded</span>
                                                    @endif
                                                </div>
                                                @if($doc->review_note)
                                                    <div class="badge bg-light text-danger border mt-1" style="font-size:11px;">
                                                        Note: {{ $doc->review_note }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            @if($doc->status === 'validated')
                                                <span class="badge bg-success px-3 py-2">Passed</span>
                                            @elseif($doc->status === 'revision_required')
                                                <span class="badge bg-danger px-3 py-2">Flagged</span>
                                            @elseif($doc->status === 'uploaded')
                                                <span class="badge bg-info text-white px-3 py-2">Ready to Validate</span>
                                            @else
                                                <span class="badge bg-light text-muted border px-3 py-2">Pending Upload</span>
                                            @endif

                                            @if($doc->file_path)
                                                <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View Document">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endif

                                            @can('mining.edit')
                                                @if($doc->file_name)
                                                    <!-- Pass Action -->
                                                    <form method="POST" action="{{ route('mining.document.validate', $doc->id) }}" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="action" value="pass">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Pass / Validate">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>

                                                    <!-- Reject / Flag Action -->
                                                    <button type="button" class="btn btn-sm btn-outline-danger btn-flag-mining-doc" 
                                                            data-doc-name="{{ $doc->document_name }}"
                                                            data-action-url="{{ route('mining.document.validate', $doc->id) }}"
                                                            title="Flag for Correction">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                @endif
                                            @endcan
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Single Dynamic Reject / Flag Modal -->
                            <div class="modal fade" id="modalRejectMiningDoc" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <form method="POST" id="formRejectMiningDoc" action="" class="modal-content">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">
                                        <div class="modal-header">
                                            <h5 class="modal-title h6 fw-bold">Flag for Correction: <span id="spanRejectDocName" class="text-primary"></span></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label small fw-semibold">Review / Correction Note: <span class="text-danger">*</span></label>
                                            <textarea name="review_note" id="textRejectReviewNote" class="form-control" rows="3" required placeholder="e.g. Scan unreadable, figures mismatch, or missing endorsement stamp"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Flag for Re-upload</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- UNIFIED LIFECYCLE TRACKING & AUDIT TRAIL -->
                    <div class="surface p-4 mt-4 rounded-3 border shadow-sm bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history text-primary me-2"></i>Unified Lifecycle Tracking &amp; Audit Trail
                            </h6>
                            <span class="badge bg-light text-muted border font-monospace">Common ID: {{ $application->common_id ?? 'GTMS-REF' }}</span>
                        </div>
                        @if(isset($activityLogs) && $activityLogs->count() > 0)
                            <div class="timeline-stream ps-3 border-start border-2 border-primary-subtle ms-2">
                                @foreach($activityLogs as $log)
                                    <div class="timeline-node mb-3 position-relative ps-3">
                                        <div class="position-absolute rounded-circle {{ $log->loggable_type === 'lease_application' ? 'bg-secondary' : 'bg-primary' }}" style="width:10px; height:10px; left:-18px; top:5px;"></div>
                                        <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-1">
                                            <strong class="text-dark small">
                                                @if($log->loggable_type === 'lease_application')
                                                    <span class="badge bg-secondary-subtle text-secondary me-1">Lease Phase</span>
                                                @else
                                                    <span class="badge bg-primary-subtle text-primary me-1">Mining Phase</span>
                                                @endif
                                                {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                            </strong>
                                            <span class="text-muted" style="font-size:.72rem;">{{ $log->created_at ? $log->created_at->format('d M Y, h:i A') : '' }}</span>
                                        </div>
                                        <p class="text-muted small mb-0 mt-1">{{ $log->description }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-3 text-muted small">No audit events recorded yet.</div>
                        @endif
                    </div>
                </main>
            @endif

        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-flag-mining-doc', function(e) {
        e.preventDefault();
        var docName = $(this).data('doc-name');
        var actionUrl = $(this).data('action-url');
        $('#spanRejectDocName').text(docName);
        $('#formRejectMiningDoc').attr('action', actionUrl);
        $('#textRejectReviewNote').val('');
        var modalEl = document.getElementById('modalRejectMiningDoc');
        if (modalEl) {
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    });
});
</script>
@endsection
