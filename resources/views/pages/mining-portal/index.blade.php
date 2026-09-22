@extends('layouts.app')
@section('title', 'Mining Portal — Applications')
@section('main_content')

<style>
/* Mining Action Buttons UI/UX Pro Max */
.mining-action-group {
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
    font-size: 0.9rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    padding: 0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.btn-action-resume {
    height: 32px;
    padding: 0 11px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: #ffffff !important;
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    border: 1px solid #6d28d9;
    box-shadow: 0 2px 5px rgba(124, 58, 237, 0.25);
    text-decoration: none;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.btn-action-resume:hover {
    background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%);
    box-shadow: 0 4px 10px rgba(124, 58, 237, 0.35);
    transform: translateY(-1px);
    color: #ffffff !important;
}

/* Folders Button - Soft Sky Blue */
.btn-action-folders {
    background: #eff6ff;
    color: #1d4ed8 !important;
    border-color: #bfdbfe;
}
.btn-action-folders:hover {
    background: #1d4ed8;
    color: #ffffff !important;
    border-color: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(29, 78, 216, 0.25);
}

/* Files Button - Warm Amber */
.btn-action-files {
    background: #fef3c7;
    color: #b45309 !important;
    border-color: #fde68a;
}
.btn-action-files:hover {
    background: #d97706;
    color: #ffffff !important;
    border-color: #d97706;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(217, 119, 6, 0.25);
}

/* Process Button - GTMS Navy / Deep Indigo */
.btn-action-process {
    background: #eef2ff;
    color: #4338ca !important;
    border-color: #c7d2fe;
}
.btn-action-process:hover {
    background: #4338ca;
    color: #ffffff !important;
    border-color: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(67, 56, 202, 0.25);
}
</style>

    <div class="content-body default-height">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row page-titles mb-3">
                <div class="col-lg-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Mining Applications</a></li>
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

            <!-- KPI Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6">
                    <div class="card p-3 border shadow-sm rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 bg-primary-subtle text-primary fs-3">
                                <i class="bi bi-folder-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total Applications</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $totalApplications }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card p-3 border shadow-sm rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 bg-warning-subtle text-warning fs-3">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="text-muted small">In Scrutiny / Review</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $scrutinyCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card p-3 border shadow-sm rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 bg-success-subtle text-success fs-3">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Approved &amp; Completed</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $approvedCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card p-3 border shadow-sm rounded-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 rounded-3 bg-secondary-subtle text-secondary fs-3">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Draft Applications</div>
                                <h3 class="fw-bold mb-0 text-dark">{{ $draftCount }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card border shadow-sm rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="fw-bold mb-0 text-dark">Registered Mining Dossiers</h5>
                            <span class="text-muted small">Showing {{ count($applications) }} of {{ $totalApplications }} applications</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>Application No</th>
                                            <th>Client / Firm</th>
                                            <th>MIMAS Details</th>
                                            <th>Nature of Work</th>
                                            <th>District</th>
                                            <th>Mineral &amp; Plan</th>
                                            <th>Stage</th>
                                            <th>Status</th>
                                            <th class="text-end pe-3" style="min-width: 190px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applications as $app)
                                            <tr>
                                                <td class="ps-3 text-muted small">{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="fw-bold text-primary text-decoration-none">
                                                        {{ $app->application_no }}
                                                    </a>
                                                    @if($app->common_id)
                                                        <div style="font-size: 10px;" class="text-muted"><span class="badge bg-light text-secondary border mt-1">{{ $app->common_id }}</span></div>
                                                    @endif
                                                    @if($app->leaseApplication)
                                                        <div style="font-size: 10px;" class="mt-1">
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" title="Originated from Lease Application">
                                                                <i class="fa fa-link me-1"></i>Lease #{{ $app->leaseApplication->application_no }}
                                                            </span>
                                                        </div>
                                                    @endif
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
                                                <td>
                                                    @if($app->minerals && $app->minerals->isNotEmpty())
                                                        <span class="fw-semibold text-dark">{{ $app->minerals->pluck('name')->implode(', ') }}</span>
                                                        @if(!empty($app->other_mineral_name))
                                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1" style="font-size:.65rem;">Other: {{ $app->other_mineral_name }}</span>
                                                        @endif
                                                        @if($app->planType)
                                                            <div class="text-muted" style="font-size:11px;">{{ $app->planType->name }}</div>
                                                        @endif
                                                    @elseif($app->mineral)
                                                        <span class="fw-semibold text-dark">{{ $app->mineral->name }}</span>
                                                        @if(!empty($app->other_mineral_name))
                                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-1" style="font-size:.65rem;">Other: {{ $app->other_mineral_name }}</span>
                                                        @endif
                                                        @if($app->planType)
                                                            <div class="text-muted" style="font-size:11px;">{{ $app->planType->name }}</div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">&mdash;</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-navy border">
                                                        Stage {{ $app->stage }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($app->status === 'approved')
                                                        <span class="badge bg-success">Approved</span>
                                                    @elseif($app->status === 'draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($app->status === 'scrutiny')
                                                        <span class="badge bg-warning text-dark">In Scrutiny</span>
                                                    @elseif($app->status === 'rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                    @else
                                                        <span class="badge bg-info">{{ ucfirst($app->status) }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end pe-3">
                                                    <div class="mining-action-group">
                                                        @if($app->status === 'draft' || $app->lease_application_id)
                                                            <a href="{{ route('newapplication', ['resume' => $app->id]) }}" class="btn-action-resume" title="Resume Application Form" data-bs-toggle="tooltip">
                                                                <i class="fa fa-play" style="font-size: 9px;"></i>
                                                                <span>Resume</span>
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('projectfolder', ['id' => $app->id]) }}" class="btn-action-icon btn-action-folders" title="Statutory Folders (6 Folders)" data-bs-toggle="tooltip">
                                                            <i class="bi bi-folder2-open"></i>
                                                        </a>
                                                        <a href="{{ route('document', ['id' => $app->id]) }}" class="btn-action-icon btn-action-files" title="Manage Files &amp; Uploads" data-bs-toggle="tooltip">
                                                            <i class="bi bi-cloud-arrow-up"></i>
                                                        </a>
                                                        <a href="{{ route('process', ['id' => $app->id]) }}" class="btn-action-icon btn-action-process" title="Process Flow (Stages 6.1 - 6.6)" data-bs-toggle="tooltip">
                                                            <i class="bi bi-diagram-3"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5 text-muted">
                                                    <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                                    No mining applications registered yet.
                                                    <div class="mt-3">
                                                        <a href="{{ route('newapplication') }}" class="btn btn-navy btn-sm">
                                                            <i class="fa fa-plus me-1"></i>Create First Application
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
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
@endsection

