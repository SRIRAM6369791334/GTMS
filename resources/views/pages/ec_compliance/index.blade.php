@extends('layouts.app')
@section('title', 'EC Half-Yearly Compliance Register')
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles mb-3">
      <div class="col-lg-7 col-12 mb-2 mb-lg-0">
        <h4 class="mb-1 text-navy fw-bold"><i class="fas fa-clipboard-check me-2"></i>Environmental Clearance — Half Yearly Compliance</h4>
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">Half Yearly Compliance</a></li>
        </ol>
      </div>
      <div class="col-lg-5 col-12 text-lg-end">
        @can('environment.view')
        <a href="{{ route('ec-compliance.step', 1) }}" class="btn btn-navy shadow-sm">
          <i class="fa fa-plus me-1"></i> New Compliance Filing
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Total Filings</p>
                <h3 class="mb-0 fw-bold text-navy">{{ number_format($totalCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0e7ff; color:#0F1E4D;">
                <i class="fa fa-file-invoice fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Lab / Site Study Stage</p>
                <h3 class="mb-0 fw-bold text-warning">{{ number_format($labStageCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#fef3c7; color:#d97706;">
                <i class="fa fa-flask fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Parivesh Uploaded</p>
                <h3 class="mb-0 fw-bold text-info">{{ number_format($uploadedCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#e0f2fe; color:#0284c7;">
                <i class="fa fa-cloud-upload-alt fa-lg"></i>
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
                <p class="text-muted small fw-semibold text-uppercase mb-1">Completed &amp; Acknowledged</p>
                <h3 class="mb-0 fw-bold text-success">{{ number_format($completeCount) }}</h3>
              </div>
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#dcfce7; color:#15803d;">
                <i class="fa fa-check-circle fa-lg"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Master Compliance Register Table --}}
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <h5 class="card-title mb-0 fw-bold text-navy">Half Yearly Compliance Filings Register</h5>
        <form method="GET" action="{{ route('ec-compliance.index') }}" class="d-flex flex-wrap align-items-center gap-2">
          <div class="input-group input-group-sm" style="width: 240px;">
            <input type="text" name="search" class="form-control" placeholder="Search compliance no, client..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
          </div>
          <select name="status" class="form-select form-select-sm" style="width: 160px;" onchange="this.form.submit()">
            <option value="">All Stages</option>
            <option value="lab" {{ request('status') === 'lab' ? 'selected' : '' }}>Lab / Site Study</option>
            <option value="uploaded" {{ request('status') === 'uploaded' ? 'selected' : '' }}>Parivesh Uploaded</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
          </select>
          @if(request('search') || request('status'))
            <a href="{{ route('ec-compliance.index') }}" class="btn btn-sm btn-outline-danger" title="Clear Filters"><i class="fa fa-times"></i></a>
          @endif
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="width:100%">
            <thead class="table-light">
              <tr>
                <th class="ps-3" style="width:60px;">S No</th>
                <th>Compliance No.</th>
                <th>Client / Entity</th>
                <th>Compliance Period</th>
                <th>Due Date</th>
                <th>MoEFCC Parivesh Ack</th>
                <th>Stage Status</th>
                <th>Payment</th>
                <th class="text-end pe-3" style="min-width:140px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($compliances as $comp)
                <tr>
                  <td class="ps-3 fw-semibold text-muted">{{ $compliances->firstItem() ? $compliances->firstItem() + $loop->index : $loop->iteration }}</td>
                  <td>
                    <span class="fw-bold text-navy">{{ $comp->compliance_no }}</span>
                    @if($comp->project_name)
                      <div class="small text-muted text-truncate" style="max-width:200px;" title="{{ $comp->project_name }}">{{ $comp->project_name }}</div>
                    @endif
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $comp->customer?->company_name ?: ($comp->customer?->customer_name ?: 'N/A') }}</div>
                    @if($comp->customer?->mimas_no)
                      <small class="badge bg-light text-secondary border font-monospace">{{ $comp->customer->mimas_no }}</small>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-light text-dark border">{{ $comp->compliance_period }}</span>
                  </td>
                  <td>
                    <span class="text-muted small">
                      <i class="bi bi-calendar-event me-1"></i>
                      {{ $comp->submission_due_date ? $comp->submission_due_date->format('d M Y') : '—' }}
                    </span>
                  </td>
                  <td>
                    @if($comp->parivesh_acknowledgement_no)
                      <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">
                        <i class="bi bi-check2 me-1"></i>{{ $comp->parivesh_acknowledgement_no }}
                      </span>
                    @else
                      <span class="badge bg-light text-muted border">Pending Upload</span>
                    @endif
                  </td>
                  <td>
                    @php
                      $cClass = match($comp->status) {
                        'completed'              => 'bg-success',
                        'uploaded_to_parivesh'   => 'bg-info text-white',
                        'lab_analysed', 'report_prepared' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
                        default                  => 'bg-primary-subtle text-primary border border-primary-subtle'
                      };
                    @endphp
                    <span class="badge {{ $cClass }}">{{ ucwords(str_replace('_', ' ', $comp->status)) }}</span>
                  </td>
                  <td>
                    @php
                      $pStatus = $comp->payment_status ?: 'pending';
                      $pClass = match($pStatus) {
                        'paid'    => 'badge bg-success',
                        'partial' => 'badge bg-warning text-dark',
                        default   => 'badge bg-danger-subtle text-danger border border-danger-subtle'
                      };
                    @endphp
                    <span class="{{ $pClass }}">{{ ucfirst($pStatus) }}</span>
                    @if($comp->pending_amount > 0)
                      <div class="small text-danger fw-semibold">₹{{ number_format($comp->pending_amount, 2) }} due</div>
                    @endif
                  </td>
                  <td class="text-end pe-3">
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('ec-compliance.show', $comp->id) }}" class="btn btn-outline-primary" title="View Compliance Dossier">
                        <i class="bi bi-eye"></i> View
                      </a>
                      <a href="{{ route('ec-compliance.step', ['step' => 1, 'resume' => $comp->id]) }}" class="btn btn-outline-secondary" title="Edit / Resume Filing">
                        <i class="bi bi-pencil"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5 text-muted">
                    <i class="fas fa-clipboard-check fa-3x mb-3 text-muted opacity-50"></i>
                    <p class="mb-2">No EC Half-Yearly Compliance filings registered yet.</p>
                    @can('environment.view')
                    <a href="{{ route('ec-compliance.step', 1) }}" class="btn btn-sm btn-navy">
                      <i class="fa fa-plus me-1"></i> Start New Compliance Filing
                    </a>
                    @endcan
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      @if($compliances->hasPages())
        <div class="card-footer bg-white border-top py-3">
          {{ $compliances->links('vendor.pagination.bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
