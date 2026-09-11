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
                    <a href="/step1"><button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                        </span>New Application</button></a>
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
									<span class="me-3 bgl-danger text-danger">
										<svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
											<line x1="12" y1="1" x2="12" y2="23"></line>
											<path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
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
									<span class="me-3 bgl-success text-success">
										<svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-database">
											<ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
											<path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
											<path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
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
                        <div class="card-header d-flex justify-content-end">


                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Application No.</th>
                                            <th>Client / Firm</th>
                                            <th>District</th>
                                            <th>Category</th>
                                            <th>Documents</th>
                                            <th>Status</th>
                                            <th>Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($applications as $app)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong class="text-primary">{{ $app->application_no }}</strong></td>
                                            <td>
                                                <div class="fw-bold">{{ $app->customer->company_name ?? $app->customer->customer_name }}</div>
                                                <small class="text-muted">{{ $app->contact_person }} &middot; {{ $app->contact_mobile }}</small>
                                            </td>
                                            <td>{{ $app->district->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $app->category->code ?? 'N/A' }}</span></td>
                                            <td>
                                                <a href="/viewapplication?id={{ $app->id }}" class="badge bg-info-subtle text-info border border-info-subtle">
                                                    <i class="fa fa-folder-open me-1"></i> {{ $app->documents->count() }} Files
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
                                                        <i class="fa fa-pencil me-1"></i> Draft (Step {{ $app->current_step ?? 1 }}/7)
                                                    </span>
                                                @else
                                                    <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $app->status)) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $app->updated_at ? $app->updated_at->format('d M Y') : $app->created_at->format('d M Y') }}</td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($app->status === 'draft')
                                                    <a href="{{ route('application.resume', $app->id) }}" class="btn btn-sm btn-info text-white shadow-sm fw-bold">
                                                        <i class="fa fa-play me-1"></i> Resume Draft &rarr;
                                                    </a>
                                                    @else
                                                    @can('application.view')
                                                    <a href="/viewapplication?id={{ $app->id }}" class="btn btn-sm btn-primary"><i class="fa fa-eye me-1"></i> View</a>
                                                    @endcan
                                                    @can('application.edit')
                                                    @if($app->status !== 'approved')
                                                    <a href="/viewapplication?id={{ $app->id }}" class="btn btn-sm btn-outline-success" title="Review in Scrutiny Dossier">Scrutiny</a>
                                                    @endif
                                                    @endcan
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">
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




@endsection


