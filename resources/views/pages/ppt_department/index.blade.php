@extends('layouts.app')
@section('title', 'PPT Department — Online Domain Applications')
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles mb-3">
      <div class="col-lg-6 col-12 mb-2 mb-lg-0">
        <h4 class="mb-1 text-navy fw-bold"><i class="fas fa-landmark me-2"></i>PPT Department — Online Domain</h4>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">PPT Department</a></li>
        </ol>
      </div>
      <div class="col-lg-6 col-12 text-lg-end">
        @can('ppt.create')
        <a href="{{ route('ppt-department.step', 1) }}" class="btn btn-navy shadow-sm">
          <i class="fa fa-plus me-1"></i> New PPT Application
        </a>
        @endcan
      </div>
    </div>

    {{-- Live KPI Statistic Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #0F1E4D !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">Total Applications</p>
                <h3 class="mb-0 fw-bold text-navy">{{ number_format($totalCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0e7ff; color:#0F1E4D;">
                <i class="fa fa-folder-open fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #f59e0b !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">Under Validation</p>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($validationCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#fef3c7; color:#d97706;">
                <i class="fa fa-hourglass-half fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #10b981 !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">Approved &amp; Presented</p>
                <h3 class="mb-0 fw-bold text-success">{{ number_format($approvedCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#dcfce7; color:#15803d;">
                <i class="fa fa-check-circle fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #64748b !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">Archived</p>
                <h3 class="mb-0 fw-bold text-secondary">{{ number_format($archivedCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#f1f5f9; color:#475569;">
                <i class="fa fa-archive fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Master Applications Card & Filters --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0 fw-bold text-navy">PPT Department Applications Register</h5>
        <form method="GET" action="{{ route('ppt-department.index') }}" class="d-flex flex-wrap align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 240px;">
            <input type="text" name="search" class="form-control" placeholder="Search application, client..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
          </div>
          <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="validation" {{ request('status') === 'validation' ? 'selected' : '' }}>Under Validation</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
          </select>
          @if(request('search') || request('status'))
            <a href="{{ route('ppt-department.index') }}" class="btn btn-sm btn-outline-danger" title="Clear Filters"><i class="fa fa-times"></i></a>
          @endif
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="width:100%">
            <thead class="table-light">
              <tr>
                <th class="ps-3" style="width:60px;">S No</th>
                <th>Application No.</th>
                <th>Client / Entity</th>
                <th>District &amp; Location</th>
                <th>Mineral</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Payment</th>
                <th class="text-end pe-3" style="min-width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($applications as $app)
                <tr>
                  <td class="ps-3 fw-semibold text-muted">{{ $applications->firstItem() ? $applications->firstItem() + $loop->index : $loop->iteration }}</td>
                  <td>
                    <span class="fw-bold text-navy">{{ $app->application_no }}</span>
                    @if($app->project_name)
                      <div class="small text-muted text-truncate" style="max-width:220px;" title="{{ $app->project_name }}">{{ $app->project_name }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $app->customer?->company_name ?: ($app->customer?->customer_name ?: 'N/A') }}</div>
                    @if($app->customer?->mimas_no)
                      <small class="badge bg-light text-secondary border font-monospace">{{ $app->customer->mimas_no }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ $app->district?->name ?: 'N/A' }}</div>
                    <small class="text-muted">{{ $app->taluk_village ?: '—' }}</small>
                  </td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $app->mineral?->name ?: 'General' }}</span>
                  </td>
                  <td>
                    @php
                      $docCount = $app->documents ? $app->documents->count() : 0;
                    @endphp
                    <span class="badge {{ $docCount >= 11 ? 'bg-success' : 'bg-info-subtle text-info border border-info-subtle' }}">
                      {{ $docCount }} / 11 files
                    </span>
                  </td>
                  <td>
                    @php
                      $st = $app->status;
                      $badgeClass = match($st) {
                        'approved', 'presented' => 'bg-success',
                        'agenda_scheduled'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                        'archived'             => 'bg-secondary',
                        default                => 'bg-primary-subtle text-primary border border-primary-subtle'
                      };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ ucwords(str_replace('_', ' ', $st)) }}</span>
                  </td>
                  <td>
                    @php
                      $pStatus = $app->payment_status ?: 'pending';
                      $pClass = match($pStatus) {
                        'paid'    => 'badge bg-success',
                        'partial' => 'badge bg-warning text-dark',
                        default   => 'badge bg-danger-subtle text-danger border border-danger-subtle'
                      };
                    @endphp
                    <span class="{{ $pClass }}">{{ ucfirst($pStatus) }}</span>
                    @if($app->pending_amount > 0)
                      <div class="small text-danger fw-semibold">₹{{ number_format($app->pending_amount, 2) }} due</div>
                    @endif
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('ppt-department.show', $app->id) }}" class="btn btn-outline-primary" title="View Dossier">
                        <i class="bi bi-eye"></i> View
                      </a>
                      <a href="{{ route('ppt-department.step', ['step' => 1, 'resume' => $app->id]) }}" class="btn btn-outline-secondary" title="Resume / Edit Application">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fa fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
                    <p class="mb-2">No PPT Department applications found.</p>
                    @can('ppt.create')
                    <a href="{{ route('ppt-department.step', 1) }}" class="btn btn-sm btn-navy">
                      <i class="fa fa-plus me-1"></i> Start New Application
                    </a>
                    @endcan
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($applications->hasPages())
        <div class="card-footer bg-white border-top py-3">
          {{ $applications->links('vendor.pagination.bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
