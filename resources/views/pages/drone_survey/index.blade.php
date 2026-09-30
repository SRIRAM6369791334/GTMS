@extends('layouts.app')
@section('title', 'Drone Volumetric Survey Register')
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles mb-3">
      <div class="col-lg-6 col-12 mb-2 mb-lg-0">
        <h4 class="mb-1 text-navy fw-bold"><i class="fa fa-paper-plane me-2"></i>Drone Department — Volumetric Survey</h4>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">Drone Survey</a></li>
        </ol>
      </div>
      <div class="col-lg-6 col-12 text-lg-end">
        @can('drone.create')
        <a href="{{ route('drone-survey.step', 1) }}" class="btn btn-navy shadow-sm" style="background-color: #0F1E4D; color: white;">
          <i class="fa fa-plus me-1"></i> New Drone Survey
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Survey Requests</p>
                <h3 class="mb-0 fw-bold text-navy" style="color: #0F1E4D;">{{ sprintf('%02d', $totalRequests) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0e7ff; color:#0F1E4D;">
                <i class="fa fa-paper-plane fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Data Acquisition</p>
                <h3 class="mb-0 fw-bold text-warning">{{ sprintf('%02d', $dataAcquisitionCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#fef3c7; color:#d97706;">
                <i class="fa fa-video fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Deliverables Ready</p>
                <h3 class="mb-0 fw-bold text-success">{{ sprintf('%02d', $deliverablesReadyCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#dcfce7; color:#15803d;">
                <i class="fa fa-map fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">GTMS Uploaded</p>
                <h3 class="mb-0 fw-bold text-info">{{ sprintf('%02d', $gtmsUploadedCount) }}</h3>
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
        <h5 class="card-title mb-0 fw-bold text-navy" style="color: #0F1E4D;">Drone Survey Requests Register</h5>
        <form method="GET" action="{{ route('drone-survey.index') }}" class="d-flex flex-wrap align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 250px;">
            <input type="text" name="search" class="form-control" placeholder="Search survey, client, pilot..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
          </div>
          <select name="status" class="form-select form-select-sm" style="width: 170px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="acquisition" {{ request('status') === 'acquisition' ? 'selected' : '' }}>Acquisition / Flying</option>
            <option value="deliverables_ready" {{ request('status') === 'deliverables_ready' ? 'selected' : '' }}>Deliverables Ready</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed / Uploaded</option>
          </select>
          @if(request('search') || request('status'))
            <a href="{{ route('drone-survey.index') }}" class="btn btn-sm btn-outline-danger" title="Clear Filters"><i class="fa fa-times"></i></a>
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
                <th>Location</th>
                <th>Lease Area</th>
                <th>Survey Status</th>
                <th>Deliverables</th>
                <th>Payment</th>
                <th>Updated</th>
                <th class="text-end pe-3" style="min-width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($surveys as $survey)
                @php
                  $rawStatus = strtolower($survey->survey_status ?? 'scheduled');
                  $sBadge = match($rawStatus) {
                    'completed', 'gtms_uploaded' => 'bg-success',
                    'deliverables_ready'         => 'bg-success',
                    'processing', 'data_processing', 'flying', 'data_acquisition' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                    default                      => 'bg-primary-subtle text-primary border border-primary-subtle'
                  };

                  $deliverableLabel = match($rawStatus) {
                    'completed', 'gtms_uploaded' => 'Completed',
                    'deliverables_ready'         => 'Ready',
                    'processing', 'data_processing', 'flying', 'data_acquisition' => 'In progress',
                    default                      => 'Pending'
                  };

                  $dBadge = match($deliverableLabel) {
                    'Completed', 'Ready' => 'badge bg-success',
                    'In progress'        => 'badge bg-warning text-dark',
                    default              => 'badge bg-secondary'
                  };

                  $pStatus = $survey->payment_status ?: 'pending';
                  $pClass = match($pStatus) {
                    'paid'    => 'badge bg-success',
                    'partial' => 'badge bg-warning text-dark',
                    default   => 'badge bg-danger-subtle text-danger border border-danger-subtle'
                  };

                  $clientName = $survey->customer?->company_name ?: ($survey->customer?->customer_name ?: 'N/A');
                  $location = $survey->location ?: ($survey->leaseApplication?->district?->name ?? ($survey->customer?->district?->name ?? '—'));
                @endphp
                <tr>
                  <td class="ps-3 fw-semibold text-muted">{{ $surveys->firstItem() ? $surveys->firstItem() + $loop->index : $loop->iteration }}</td>
                  <td>
                    <a href="{{ route('drone-survey.show', $survey->id) }}" class="fw-bold text-navy" style="color: #0F1E4D;">
                      {{ $survey->survey_no }}
                    </a>
                    @if($survey->leaseApplication)
                      <div class="small text-muted font-monospace">Lease #{{ $survey->leaseApplication->application_no }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $clientName }}</div>
                    @if($survey->customer?->mimas_no)
                      <small class="badge bg-light text-secondary border font-monospace">{{ $survey->customer->mimas_no }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ $location }}</div>
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">
                      {{ $survey->lease_area ? number_format($survey->lease_area, 2) . ' Ha' : '—' }}
                    </span>
                    @if($survey->extracted_volume_cbm > 0)
                      <div class="small text-muted mt-1"><i class="fa fa-cube me-1 text-warning"></i>{{ number_format($survey->extracted_volume_cbm, 1) }} CBM</div>
                    @endif
                  </td>
                  <td>
                    <span class="badge {{ $sBadge }}">{{ ucwords(str_replace('_', ' ', $survey->survey_status ?: 'scheduled')) }}</span>
                  </td>
                  <td>
                    <span class="{{ $dBadge }}">{{ $deliverableLabel }}</span>
                  </td>
                  <td>
                    <span class="{{ $pClass }}">{{ ucfirst($pStatus) }}</span>
                    @if($survey->pending_amount > 0)
                      <div class="small text-danger fw-semibold">₹{{ number_format($survey->pending_amount, 2) }} due</div>
                    @endif
                  </td>
                  <td>
                    <span class="text-muted small">{{ $survey->updated_at ? $survey->updated_at->format('d M Y') : '—' }}</span>
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      @can('drone.view')
                      <a href="{{ route('drone-survey.show', $survey->id) }}" class="btn btn-outline-primary" title="View Dossier">
                        <i class="bi bi-eye"></i> View
                      </a>
                      @endcan
                      @can('drone.edit')
                      <a href="{{ route('drone-survey.step', ['step' => 1, 'resume' => $survey->id]) }}" class="btn btn-outline-secondary" title="Resume / Edit Survey">
                        <i class="bi bi-pencil"></i>
                      </a>
                      @endcan
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="10" class="text-center py-5 text-muted">
                    <i class="fa fa-paper-plane fa-3x mb-3 text-muted opacity-50"></i>
                    <p class="mb-2">No Drone Survey records found.</p>
                    @can('drone.create')
                    <a href="{{ route('drone-survey.step', 1) }}" class="btn btn-sm btn-navy" style="background-color: #0F1E4D; color: white;">
                      <i class="fa fa-plus me-1"></i> Register First Survey
                    </a>
                    @endcan
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Server-Side Pagination --}}
      @if($surveys->hasPages())
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
          <small class="text-muted">Showing {{ $surveys->firstItem() }} to {{ $surveys->lastItem() }} of {{ $surveys->total() }} surveys</small>
          {{ $surveys->links('vendor.pagination.bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
