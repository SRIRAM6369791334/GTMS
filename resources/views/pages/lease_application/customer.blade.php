@extends('layouts.app')
@section('title', 'Lease Applications')
@section('main_content')



    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <div class="col-lg-6">
                    <ol class="breadcrumb">
                        {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li> --}}
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Applications</a></li>
                    </ol>
                </div>
                <div class="col-lg-6 text-end">
                    @can('application.create')
                    <a href="{{ route('step1') }}" class="btn btn-navy text-white shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                        <i class="fa fa-plus me-1"></i> New Application
                    </a>
                    @endcan
                </div>

             </div>

             @if(session('success'))
             <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                 <i class="fa fa-check-circle fs-5 text-success"></i>
                 <div>{{ session('success') }}</div>
                 <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
             </div>
             @endif

             @if(session('warning'))
             <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                 <i class="fa fa-exclamation-triangle fs-5 text-warning"></i>
                 <div>{{ session('warning') }}</div>
                 <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
             </div>
             @endif

             @if(session('error'))
             <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                 <i class="fa fa-times-circle fs-5 text-danger"></i>
                 <div>{{ session('error') }}</div>
                 <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
             </div>
             @endif

             <div class="row">
    <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
						<div class="widget-stat card">
							<div class="card-body p-4">
								<div class="media ai-icon">
									<span class="me-3 bgl-primary text-primary">
										<!-- <i class="ti-user"></i> -->
										<svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user">
											<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
											<circle cx="12" cy="7" r="4"></circle>
										</svg>
									</span>
									<div class="media-body">
										<p class="mb-1">Total applications</p>
										<h4 class="mb-0">{{ $kpis['total'] ?? 0 }}</h4>
									</div>
								</div>
							</div>
						</div>
                    </div>
                    <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                        <div class="widget-stat card">
							<div class="card-body p-4">
								<div class="media ai-icon">
									<span class="me-3 bgl-warning text-warning">
										<svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
											<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
											<polyline points="14 2 14 8 20 8"></polyline>
											<line x1="16" y1="13" x2="8" y2="13"></line>
											<line x1="16" y1="17" x2="8" y2="17"></line>
											<polyline points="10 9 9 9 8 9"></polyline>
										</svg>
									</span>
									<div class="media-body">
										<p class="mb-1">Under validation</p>
										<h4 class="mb-0">{{ $kpis['under_validation'] ?? 0 }}</h4>
									</div>
								</div>
							</div>
						</div>
                    </div>
                    <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                        <div class="widget-stat card">
							<div class="card-body  p-4">
								<div class="media ai-icon">
									<span class="me-3 bgl-success text-success">
										<svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
											<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
											<polyline points="22 4 12 14.01 9 11.01"></polyline>
										</svg>
									</span>
									<div class="media-body">
										<p class="mb-1">Approved</p>
										<h4 class="mb-0">{{ $kpis['approved'] ?? 0 }}</h4>
									</div>
								</div>
							</div>
						</div>
                    </div>
                    <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                        <div class="widget-stat card">
							<div class="card-body p-4">
								<div class="media ai-icon">
									<span class="me-3 bgl-danger text-danger">
										<svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-triangle">
											<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
											<line x1="12" y1="9" x2="12" y2="13"></line>
											<line x1="12" y1="17" x2="12.01" y2="17"></line>
										</svg>
									</span>
									<div class="media-body">
										<p class="mb-1">Needs review</p>
										<h4 class="mb-0">{{ $kpis['needs_review'] ?? 0 }}</h4>
									</div>
								</div>
							</div>
						</div>
                    </div>

             </div>

            <!-- row -->


            <div class="row">
                 <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                         <tr>
                                             <th>S No</th>
                                             <th>Application No.</th>
                                             <th>Client / Firm</th>
                                             <th>MIMAS Details</th>
                                             <th>District</th>
                                             <th>Category</th>
                                             <th>Documents</th>
                                             <th>Status</th>
                                             <th>Updated</th>
                                             <th class="text-end" style="min-width: 140px; white-space: nowrap;">Actions</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         @forelse($applications as $app)
                                         @php
                                             $uploadedDocsCount = $app->documents->whereNotNull('file_path')->count();
                                         @endphp
                                         <tr>
                                             <td>{{ $loop->iteration }}</td>
                                             <td>
                                                 <strong class="text-primary">{{ $app->application_no }}</strong>
                                             </td>
                                             <td>
                                                 <strong>{{ $app->customer?->company_name ?? $app->customer?->customer_name ?? 'Deleted / Unassigned' }}</strong>
                                                 @if($app->customer?->trashed())
                                                     <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size: .65rem;">Deleted</span>
                                                 @endif
                                                 <div class="text-muted small">
                                                     <i class="bi bi-telephone text-primary me-1" style="font-size:0.75rem;"></i>{{ $app->contact_mobile ?? ($app->customer?->mobile_num ?? 'N/A') }}
                                                     @if(!empty($app->contact_person))
                                                         <span class="text-secondary">({{ $app->contact_person }})</span>
                                                     @endif
                                                     @if(!empty($app->secondary_contact_mobile) || !empty($app->customer?->secondary_mobile_num))
                                                         <br>
                                                         <span class="badge" style="font-size:0.65rem; background:#e0e7ff; color:#4338ca; border:1px solid #c7d2fe; padding:1px 4px;">Alt</span>
                                                         <span class="text-secondary" style="font-size:0.75rem;">{{ $app->secondary_contact_mobile ?? $app->customer?->secondary_mobile_num }}</span>
                                                         @if(!empty($app->secondary_contact_person) || !empty($app->customer?->secondary_contact_person))
                                                             <span class="text-muted" style="font-size:0.7rem;">({{ $app->secondary_contact_person ?? $app->customer?->secondary_contact_person }})</span>
                                                         @endif
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
                                             <td><span class="badge badge-outline-secondary">{{ $app->district->name ?? 'N/A' }}</span></td>
                                            <td><span class="badge badge-outline-primary">{{ $app->category->code ?? 'N/A' }}</span></td>
                                            <td>
                                                <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="badge {{ $uploadedDocsCount > 0 ? 'bg-light text-primary border' : 'bg-light text-muted border' }} py-2 px-3 text-decoration-none">
                                                    <i class="fa fa-folder-open me-1"></i> {{ $uploadedDocsCount }} / 19 Uploaded
                                                </a>
                                            </td>
                                            <td>
                                                @if($app->status === 'approved')
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($app->status === 'validated')
                                                    <span class="badge badge-info">Validated</span>
                                                @elseif($app->status === 'under_scrutiny' || $app->status === 'submitted')
                                                    <span class="badge badge-warning">Under Validation</span>
                                                @elseif($app->status === 'revision_required')
                                                    <span class="badge badge-danger">Revision Required</span>
                                                @elseif($app->status === 'rejected')
                                                    <span class="badge badge-danger">Rejected</span>
                                                @elseif($app->status === 'draft')
                                                    <span class="badge badge-warning text-dark border border-warning" style="background:#fff3cd;">
                                                        <i class="fa fa-pencil me-1"></i> Draft (Step {{ min(6, (int)($app->current_step ?? 1)) }}/6)
                                                    </span>
                                                @else
                                                    <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $app->status)) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $app->updated_at ? $app->updated_at->format('d M Y') : $app->created_at->format('d M Y') }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end align-items-center gap-1" style="white-space: nowrap;">
                                                    @if($app->status === 'draft')
                                                        @can('application.create')
                                                        <a href="{{ route('application.resume', $app->id) }}" class="btn btn-sm text-white shadow-sm fw-bold" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;">
                                                            <i class="fa fa-play me-1"></i> Resume Draft &rarr;
                                                        </a>
                                                        @endcan
                                                    @elseif($app->status === 'revision_required')
                                                        <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn btn-sm btn-danger text-white shadow-sm fw-bold" style="border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;" title="Open Scrutiny Dossier to review flagged items">
                                                            <i class="fa fa-exclamation-circle me-1"></i> Revisions &rarr;
                                                        </a>
                                                    @else
                                                        @php
                                                            $linkedMining = $app->miningApplications ? $app->miningApplications->first() : null;
                                                        @endphp
                                                        @if($app->status === 'approved')
                                                            @if($linkedMining)
                                                                <a href="{{ route('projectfolder', ['id' => $linkedMining->id]) }}" class="btn btn-sm text-white shadow-sm" style="background:#6d28d9; border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;" title="View Linked Mining Plan {{ $linkedMining->application_no }}">
                                                                    <i class="fa fa-mountain me-1"></i> Mining Plan
                                                                </a>
                                                            @else
                                                                @can('application.edit')
                                                                <button type="button" class="btn btn-sm btn-navy text-white btn-trigger-move-to-mining shadow-sm" 
                                                                        style="border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;"
                                                                        data-app-id="{{ $app->id }}"
                                                                        data-app-no="{{ $app->application_no }}"
                                                                        data-common-id="{{ $app->common_id ?? ('GTMS-' . date('Y') . '-' . str_pad($app->id, 4, '0', STR_PAD_LEFT)) }}"
                                                                        data-client="{{ $app->customer?->company_name ?? ($app->customer?->customer_name ?? 'Applicant') }}"
                                                                        data-extent="{{ $app->area_extent_ha ? ($app->area_extent_ha . ' Ha') : 'N/A' }}"
                                                                        data-action="{{ route('application.moveToMining', $app->id) }}"
                                                                        title="Promote to Mining Plan Domain">
                                                                    <i class="bi bi-rocket-takeoff me-1 text-warning"></i> Move to Mining
                                                                </button>
                                                                @endcan
                                                            @endif
                                                        @endif
                                                        @can('application.edit')
                                                            @if($app->status !== 'approved')
                                                            <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn btn-sm btn-success text-white" style="border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;" title="Review in Scrutiny Dossier">
                                                                <i class="fa fa-check-circle me-1"></i> View
                                                            </a>
                                                            @else
                                                            <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn btn-sm btn-primary" style="border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;">
                                                                <i class="fa fa-eye me-1"></i> View
                                                            </a>
                                                            @endif
                                                        @elsecan('application.view')
                                                            <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn btn-sm btn-primary" style="border-radius: 6px; padding: 4px 10px; font-size: 0.78rem;">
                                                                <i class="fa fa-eye me-1"></i> View
                                                            </a>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted">
                                                <i class="fa fa-folder-open fa-2x mb-2 d-block text-muted opacity-50"></i>
                                                No lease applications found. Click "New Application" to create one.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Dynamic Modal for Move to Mining Plan -->
    <div class="modal fade" id="modalMoveToMiningDynamic" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form id="formMoveToMiningDynamic" action="" method="POST">
                    @csrf
                    <div class="modal-header text-white" style="background:#0F1E4D;">
                        <h5 class="modal-title text-white"><i class="bi bi-rocket-takeoff me-2 text-warning"></i> Move to Mining Plan Domain</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="p-3 mb-3 rounded" style="background:#eff6ff; border:1px solid #bfdbfe;">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Universal Common ID:</span>
                                <strong class="text-primary font-monospace" id="dynCommonId"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Current Lease App No:</span>
                                <strong class="text-dark font-monospace" id="dynAppNo"></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted small">Applicant / Firm:</span>
                                <strong class="text-dark" id="dynClient"></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">Quarry Extent:</span>
                                <strong class="text-dark" id="dynExtent"></strong>
                            </div>
                        </div>
                        <p class="small text-muted mb-2">
                            Moving this application to the <b>Mining Plan</b> domain will:
                        </p>
                        <ul class="small text-muted mb-0 ps-3">
                            <li class="mb-1">Retain the <b>exact same Universal Common ID</b> across the entire system.</li>
                            <li class="mb-1">Auto-carry applicant, district, survey numbers, and mineral data.</li>
                            <li class="mb-1">Auto-clone all verified statutory files into Mining Folders #2 &amp; #5.</li>
                            <li>Initialize <b>Stage 6.1 (Draft Intake)</b> in the Mining Portal.</li>
                        </ul>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-navy btn-sm px-3">
                            <i class="bi bi-rocket-takeoff me-1"></i> Proceed &amp; Move to Mining Plan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-trigger-move-to-mining').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const commonId = this.getAttribute('data-common-id');
            const appNo = this.getAttribute('data-app-no');
            const client = this.getAttribute('data-client');
            const extent = this.getAttribute('data-extent');

            document.getElementById('formMoveToMiningDynamic').setAttribute('action', action);
            document.getElementById('dynCommonId').textContent = commonId;
            document.getElementById('dynAppNo').textContent = appNo;
            document.getElementById('dynClient').textContent = client;
            document.getElementById('dynExtent').textContent = extent;

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalMoveToMiningDynamic'));
            modal.show();
        });
    });
});
</script>
@endsection


