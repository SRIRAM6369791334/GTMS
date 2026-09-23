@extends('layouts.app')
@section('title', 'DGPS Land Survey Register')
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles mb-3">
      <div class="col-lg-6 col-12 mb-2 mb-lg-0">
        <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-map-marker-alt me-2"></i>DGPS Department — Land Survey</h4>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">DGPS Survey</a></li>
        </ol>
      </div>
      <div class="col-lg-6 col-12 text-lg-end">
        @can('dgps.create')
        <a href="{{ route('dgps-survey.step', 1) }}" class="btn btn-navy shadow-sm">
          <i class="fa fa-plus me-1"></i> New DGPS Survey
        </a>
        @endcan
      </div>
    </div>

    {{-- Live KPI Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #0F1E4D !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">Total Surveys</p>
                <h3 class="mb-0 fw-bold text-navy">{{ number_format($totalRequests) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0e7ff; color:#0F1E4D;">
                <i class="fa fa-map-marker-alt fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Field Survey Active</p>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($fieldSurveyCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#fef3c7; color:#d97706;">
                <i class="fa fa-satellite fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Reports Ready</p>
                <h3 class="mb-0 fw-bold text-success">{{ number_format($reportsReadyCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#dcfce7; color:#15803d;">
                <i class="fa fa-file-alt fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 h-100" style="border-left: 4px solid #0284c7 !important;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <p class="text-muted small fw-semibold text-uppercase mb-1">GTM Uploaded</p>
                <h3 class="mb-0 fw-bold text-info">{{ number_format($gtmUploadedCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0f2fe; color:#0284c7;">
                <i class="fa fa-cloud-upload-alt fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Master Surveys Register --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0 fw-bold text-navy">DGPS Survey Requests Register</h5>
        <form method="GET" action="{{ route('dgps-survey.index') }}" class="d-flex flex-wrap align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 240px;">
            <input type="text" name="search" class="form-control" placeholder="Search survey, client..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
          </div>
          <select name="status" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="field" {{ request('status') === 'field' ? 'selected' : '' }}>Field Survey</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="uploaded" {{ request('status') === 'uploaded' ? 'selected' : '' }}>GTM Uploaded</option>
          </select>
          @if(request('search') || request('status'))
            <a href="{{ route('dgps-survey.index') }}" class="btn btn-sm btn-outline-danger" title="Clear Filters"><i class="fa fa-times"></i></a>
          @endif
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="width:100%">
            <thead class="table-light">
              <tr>
                <th class="ps-3" style="width:60px;">S No</th>
                <th>Survey No.</th>
                <th>Client / Entity</th>
                <th>Location / Village</th>
                <th>Extent</th>
                <th>Survey Status</th>
                <th>Report Status</th>
                <th>Payment</th>
                <th class="text-end pe-3" style="min-width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($surveys as $survey)
                <tr>
                  <td class="ps-3 fw-semibold text-muted">{{ $surveys->firstItem() ? $surveys->firstItem() + $loop->index : $loop->iteration }}</td>
                  <td>
                    <span class="fw-bold text-navy">{{ $survey->survey_no }}</span>
                    @if($survey->leaseApplication)
                      <div class="small text-muted font-monospace">Lease #{{ $survey->leaseApplication->application_no }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $survey->customer?->company_name ?: ($survey->customer?->customer_name ?: 'N/A') }}</div>
                    @if($survey->customer?->mimas_no)
                      <small class="badge bg-light text-secondary border font-monospace">{{ $survey->customer->mimas_no }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ $survey->location ?: '—' }}</div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      {{ $survey->area_hectares ? number_format($survey->area_hectares, 2) . ' Ha' : '—' }}
                    </span>
                  </td>
                  <td>
                    @php
                      $sBadge = match($survey->survey_status) {
                        'completed'   => 'bg-success',
                        'in_progress' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                        default       => 'bg-primary-subtle text-primary border border-primary-subtle'
                      };
                    @endphp
                    <span class="badge {{ $sBadge }}">{{ ucwords(str_replace('_', ' ', $survey->survey_status ?: 'scheduled')) }}</span>
                  </td>
                  <td>
                    @php
                      $rBadge = match($survey->report_status) {
                        'verified'  => 'bg-success',
                        'generated' => 'bg-info-subtle text-info border border-info-subtle',
                        default     => 'bg-secondary'
                      };
                    @endphp
                    <span class="badge {{ $rBadge }}">{{ ucwords(str_replace('_', ' ', $survey->report_status ?: 'pending')) }}</span>
                  </td>
                  <td>
                    @php
                      $pStatus = $survey->payment_status ?: 'pending';
                      $pClass = match($pStatus) {
                        'paid'    => 'badge bg-success',
                        'partial' => 'badge bg-warning text-dark',
                        default   => 'badge bg-danger-subtle text-danger border border-danger-subtle'
                      };
                    @endphp
                    <span class="{{ $pClass }}">{{ ucfirst($pStatus) }}</span>
                    @if($survey->pending_amount > 0)
                      <div class="small text-danger fw-semibold">₹{{ number_format($survey->pending_amount, 2) }} due</div>
                    @endif
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('dgps-survey.show', $survey->id) }}" class="btn btn-outline-primary" title="View Dossier">
                        <i class="bi bi-eye"></i> View
                      </a>
                      <a href="{{ route('dgps-survey.step', ['step' => 1, 'resume' => $survey->id]) }}" class="btn btn-outline-secondary" title="Resume / Edit Survey">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fa fa-map-marker-alt fa-3x mb-3 text-muted opacity-50"></i>
                    <p class="mb-2">No DGPS Survey records found.</p>
                    @can('dgps.create')
                    <a href="{{ route('dgps-survey.step', 1) }}" class="btn btn-sm btn-navy">
                      <i class="fa fa-plus me-1"></i> Start New Survey
                    </a>
                    @endcan
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($surveys->hasPages())
        <div class="card-footer bg-white border-top py-3">
          {{ $surveys->links('vendor.pagination.bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
