@extends('layouts.app')
@section('title', isset($application) ? 'Folders — ' . $application->application_no : 'Project Folders Index')
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
                            <li class="breadcrumb-item active"><a href="javascript:void(0)">Project Folders</a></li>
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
                            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-folder2-open text-primary me-2"></i>Project Folders &mdash; Client Applications Index</h5>
                            <span class="text-muted small">Select any application to open its 6-folder statutory dossier and manage files.</span>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            Total: {{ $applications->total() }} Applications
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
                                        <th>Folder Upload Progress</th>
                                        <th>Stage</th>
                                        <th>Status</th>
                                        <th class="text-end pe-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($applications as $app)
                                        @php
                                            $totalD = $app->documents->count();
                                            $uploadedD = $app->documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count();
                                            $pct = $totalD > 0 ? round(($uploadedD / $totalD) * 100) : 0;
                                        @endphp
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="fw-bold text-primary text-decoration-none">
                                                    {{ $app->application_no }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $app->customer->company_name ?? $app->customer->customer_name }}</div>
                                                <div class="text-muted" style="font-size:11px;">{{ $app->customer->mobile_num ?? '' }}</div>
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
                                                @if($app->taluk)<div class="text-muted" style="font-size:11px;">{{ $app->taluk }}</div>@endif
                                            </td>
                                            <td style="min-width: 180px;">
                                                <div class="d-flex justify-content-between small mb-1">
                                                    <span class="fw-semibold text-dark">{{ $uploadedD }}/{{ $totalD }} files</span>
                                                    <span class="text-muted">{{ $pct }}%</span>
                                                </div>
                                                <div class="progress" style="height:6px;">
                                                    <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ $pct }}%;"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-navy border">Stage {{ $app->stage }}</span>
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
                                                <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="btn btn-sm btn-success text-white shadow-sm fw-semibold">
                                                    <i class="fa fa-folder-open me-1"></i>View Folders &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
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
            {{-- CASE 2: ID PROVIDED -> SHOW FULL APPLICATION DOSSIER & 6 FOLDERS --}}
            {{-- ========================================================================= --}}
            @else
                <main class="page">
                    <!-- Top Breadcrumb & Switcher Header -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
                        <div>
                            <div class="breadcrumb-min mb-1">
                                <a href="{{ route('projectfolder') }}" class="text-primary text-decoration-none fw-semibold">
                                    <i class="bi bi-arrow-left me-1"></i>Back to Folders Index
                                </a> 
                                &nbsp;/&nbsp; <span class="text-muted">{{ $application->application_no }}</span>
                            </div>
                            <h1 class="h3 fw-bold mt-1 mb-0">
                                {{ $application->customer->company_name ?? $application->customer->customer_name }}
                                <span class="badge bg-primary fs-6 fw-normal ms-2">{{ $application->natureOfWork->name ?? 'Mining Application' }}</span>
                            </h1>
                            <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                <span class="badge bg-navy text-white px-3 py-1 fw-bold" style="font-size: .82rem;">
                                    <i class="bi bi-fingerprint me-1 text-warning"></i> Universal Common ID: <b>{{ $application->common_id ?? ('GTMS-' . date('Y', strtotime($application->created_at ?? 'now')) . '-' . str_pad($application->id, 4, '0', STR_PAD_LEFT)) }}</b>
                                </span>
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: .8rem;">
                                    Mining App No: <b>{{ $application->application_no }}</b>
                                </span>
                                @if($application->lease_application_id)
                                    <a href="{{ route('viewapplication', ['id' => $application->lease_application_id]) }}" target="_blank" class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 text-decoration-none" style="font-size: .8rem;">
                                        <i class="fa fa-link me-1"></i> Origin: Lease #{{ $application->leaseApplication->application_no ?? $application->lease_application_id }} ↗
                                    </a>
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
                                    <select class="form-select form-select-sm" style="min-width: 220px;" onchange="if(this.value) window.location.href='{{ route('projectfolder') }}?id='+this.value;">
                                        @foreach($allApplications as $optApp)
                                            <option value="{{ $optApp->id }}" {{ $optApp->id == $application->id ? 'selected' : '' }}>
                                                {{ $optApp->application_no }} &mdash; {{ $optApp->customer->company_name ?? $optApp->customer->customer_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <a href="{{ route('process', ['id' => $application->id]) }}" class="btn btn-navy btn-sm">
                                <i class="bi bi-diagram-3 me-1"></i>Process Workflow
                            </a>
                        </div>
                    </div>

                    <!-- Client / District / Mineral / Plan recap chips -->
                    <div class="surface p-3 mb-4 rounded-3 border shadow-sm" style="background:#f8fafc;">
                        <div class="row g-3 text-center text-md-start">
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="bi bi-person-fill text-primary"></i> Client / Firm</div>
                                <div class="fw-bold text-dark">{{ $application->customer->company_name ?? $application->customer->customer_name }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="bi bi-geo-alt-fill text-danger"></i> District &amp; Taluk</div>
                                <div class="fw-bold text-dark">{{ $application->district->name ?? 'N/A' }} {{ $application->taluk ? '('.$application->taluk.')' : '' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="bi bi-diagram-3-fill text-info"></i> Nature of Work</div>
                                <div class="fw-bold text-dark">{{ $application->natureOfWork->name ?? 'Mining Application' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="bi bi-gem text-success"></i> Mineral(s) / Plan</div>
                                <div class="fw-bold text-dark">
                                    {{ $application->minerals && $application->minerals->isNotEmpty() ? $application->minerals->pluck('name')->implode(', ') : ($application->mineral->name ?? 'Standard Scope') }}
                                    @if($application->planType)
                                        &middot; <span class="badge bg-light text-dark border">{{ $application->planType->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <hr class="my-2" style="opacity: 0.1;">
                        <div class="row g-3 text-center text-md-start">
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="fa fa-id-badge text-primary"></i> MIMAS Number</div>
                                <div class="fw-bold text-dark font-monospace">
                                    {{ $application->customer?->mimas_number ?? 'Not assigned' }}
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="fa fa-tag text-info"></i> MIMAS Status</div>
                                <div>
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
                                        <span class="badge {{ $cardMBadgeClass }} fw-bold">{{ $application->customer->mimas_status }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Not specified</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="bi bi-telephone text-primary"></i> Contact Mobile</div>
                                <div class="fw-bold text-dark">{{ $application->customer?->mobile_num ?? 'N/A' }}</div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="small-caps-label text-muted mb-1"><i class="fa fa-fingerprint text-secondary"></i> Customer Unique ID</div>
                                <div class="fw-bold text-dark font-monospace">{{ $application->customer?->mimas_no ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- TEAM ALLOCATION & PAYMENT CARDS ROW -->
                    <div class="row g-3 mb-4">
                        <!-- Team Handlers -->
                        <div class="col-lg-7">
                            <div class="card p-3 rounded-3 border bg-white h-100 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <div class="fw-bold text-navy small"><i class="bi bi-people-fill text-primary me-2"></i>Project Handling Team</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Assigned technical personnel and field coordinators</div>
                                    </div>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size:0.75rem;">
                                        {{ $application->handlers ? $application->handlers->count() : 0 }} Members Assigned
                                    </span>
                                </div>
                                @if($application->handlers && $application->handlers->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless align-middle mb-0" style="font-size:0.83rem;">
                                            <thead class="text-muted border-bottom" style="font-size:0.75rem;">
                                                <tr>
                                                    <th style="width:30px;">#</th>
                                                    <th>Name</th>
                                                    <th>Role / Designation</th>
                                                    <th>Notes</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($application->handlers as $hIndex => $handler)
                                                    <tr>
                                                        <td class="text-muted fw-bold">{{ $hIndex + 1 }}</td>
                                                        <td class="fw-semibold text-navy"><i class="bi bi-person-badge text-primary me-1"></i> {{ $handler->name }}</td>
                                                        <td><span class="badge bg-secondary-subtle text-secondary border py-1 px-2">{{ $handler->role }}</span></td>
                                                        <td class="text-muted small">{{ $handler->notes ?: '—' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-muted small fst-italic py-2"><i class="bi bi-info-circle me-1"></i> No project handling personnel assigned to this application.</div>
                                @endif
                            </div>
                        </div>

                        <!-- Payment & Billing Summary -->
                        <div class="col-lg-5">
                            <div class="card p-3 rounded-3 border bg-white h-100 shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div>
                                        <div class="fw-bold text-navy small"><i class="bi bi-cash-stack text-success me-2"></i>Financial &amp; Billing Ledger</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Settlement status and balance tracking</div>
                                    </div>
                                    @php
                                        $pStatus = $application->payment_status ?? 'pending';
                                    @endphp
                                    @if($pStatus === 'paid')
                                        <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟢 Fully Paid</span>
                                    @elseif($pStatus === 'partial')
                                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:0.75rem;">🟡 Partial Payment</span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.75rem;">🔴 Pending Full Due</span>
                                    @endif
                                </div>

                                <div class="row g-2 text-center mt-1">
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-light">
                                            <span class="text-muted d-block" style="font-size:0.7rem;">Product Value</span>
                                            <b class="text-navy" style="font-size:0.85rem;">₹ {{ number_format((float)($application->product_value ?? 0), 2) }}</b>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-light">
                                            <span class="text-muted d-block" style="font-size:0.7rem;">Paid Amount</span>
                                            <b class="text-success" style="font-size:0.85rem;">₹ {{ number_format((float)($application->paid_amount ?? 0), 2) }}</b>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 border rounded bg-light">
                                            <span class="text-muted d-block" style="font-size:0.7rem;">Pending Balance</span>
                                            <b class="text-danger" style="font-size:0.85rem;">₹ {{ number_format((float)($application->pending_amount ?? 0), 2) }}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-0">Statutory Folder Structure</h2>
                            <p class="text-muted small mb-0">Checklist items generated as per <strong>{{ $application->natureOfWork->name ?? 'Domain' }}</strong> requirements</p>
                        </div>
                        <span class="badge {{ $overallPercent >= 100 ? 'bg-success' : 'bg-secondary' }} px-3 py-2 fs-6">
                            {{ $uploadedDocs }} of {{ $totalDocs }} files uploaded ({{ $overallPercent }}%)
                        </span>
                    </div>

                    <!-- 6 Folder Cards Grid -->
                    <div class="row g-3 mb-4">
                        @foreach($folders as $folder)
                            @php
                                $stats = $folderStats[$folder->id] ?? ['total' => 0, 'uploaded' => 0, 'percent' => 0];
                                $isFieldLog = str_contains(strtolower($folder->name), 'field');
                                $isDocs = str_contains(strtolower($folder->name), 'doc');
                                $isPhotos = str_contains(strtolower($folder->name), 'photo');
                                $isReport = str_contains(strtolower($folder->name), 'report');
                                $isPlan = str_contains(strtolower($folder->name), 'plan');
                                
                                $color = '#3b82f6';
                                $icon = 'fa-folder-open';
                                if ($isFieldLog) { $color = '#2563eb'; $icon = 'fa-clone'; }
                                elseif ($isDocs) { $color = '#7c3aed'; $icon = 'fa-file-text-o'; }
                                elseif ($isPhotos) { $color = '#0891b2'; $icon = 'fa-camera'; }
                                elseif ($isReport) { $color = '#d97706'; $icon = 'fa-pencil-square'; }
                                elseif ($isPlan) { $color = '#059669'; $icon = 'fa-map-o'; }
                                else { $color = '#dc2626'; $icon = 'fa-cubes'; }
                            @endphp

                            <div class="col-sm-6 col-lg-4">
                                <a href="{{ route('document', ['id' => $application->id, 'folder' => $folder->id]) }}" class="text-decoration-none text-reset">
                                    <div class="folder-card p-3 rounded-3 border h-100 shadow-sm" style="background:#ffffff; border-top: 4px solid {{ $color }} !important; transition: transform 0.15s ease-in-out;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="fic p-2 rounded" style="background:{{ $color }}15; color:{{ $color }};">
                                                <i class="fa {{ $icon }}"></i>
                                            </div>
                                            @if($stats['total'] == 0)
                                                <span class="badge bg-light text-muted border">Optional</span>
                                            @elseif($stats['uploaded'] >= $stats['total'])
                                                <span class="badge bg-success"><i class="bi bi-check2"></i> {{ $stats['uploaded'] }}/{{ $stats['total'] }}</span>
                                            @elseif($stats['uploaded'] > 0)
                                                <span class="badge bg-warning text-dark">{{ $stats['uploaded'] }}/{{ $stats['total'] }}</span>
                                            @else
                                                <span class="badge bg-light text-danger border">0/{{ $stats['total'] }}</span>
                                            @endif
                                        </div>

                                        <h3 class="h6 fw-bold mb-1">{{ $folder->name }}</h3>
                                        <div class="text-muted small text-truncate mb-3" style="font-size:12px;">
                                            @if($stats['total'] > 0)
                                                {{ $stats['total'] }} document types required
                                            @else
                                                Optional annexure attachments
                                            @endif
                                        </div>

                                        <div class="progress" style="height:6px; background:#e2e8f0; border-radius:3px;">
                                            <div class="progress-bar" style="width: {{ $stats['percent'] }}%; background-color: {{ $color }};"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Workflow Action Banner -->
                    <div class="surface p-3 p-lg-4 rounded-3 border shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3" style="background:#f8fafc;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 bg-primary-subtle text-primary fs-4">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">Stage 6.1 Upload &amp; Store &rarr; Stage 6.2 Validate Data</div>
                                <div class="text-muted small">
                                    Upload required checklist documents across all folders, then move the dossier to Mines Department review.
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('process', ['id' => $application->id]) }}" class="btn btn-navy px-4">
                            Send for Validation <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </main>
            @endif

        </div>
    </div>

@endsection
