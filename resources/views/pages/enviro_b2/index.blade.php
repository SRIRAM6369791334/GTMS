@extends('layouts.app')
@section('title', 'Environment Clearance B2 Applications')
@section('main_content')
<div class="content-body default-height"><div class="container-fluid">
  <div class="row page-titles">
    <div class="col-lg-6">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Environment Clearance</a></li>
        <li class="breadcrumb-item active"><a href="javascript:void(0)">B2 Applications</a></li>
      </ol>
    </div>
    <div class="col-lg-6 text-end">
      @can('environment.b2.create')
      <a href="{{ route('environment-b2.step', 1) }}" class="btn btn-rounded btn-info">
        <span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i></span>New B2 Application
      </a>
      @endcan
    </div>
  </div>

  <div class="row">
    <div class="col-xl-3 col-lg-3 col-sm-6">
      <div class="widget-stat card">
        <div class="card-body p-4">
          <div class="media ai-icon">
            <span class="me-3 bgl-primary text-primary"><i class="fa fa-folder-open fa-2x"></i></span>
            <div class="media-body">
              <p class="mb-1">Total B2 Applications</p>
              <h4 class="mb-0">{{ $kpis['total'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-sm-6">
      <div class="widget-stat card">
        <div class="card-body p-4">
          <div class="media ai-icon">
            <span class="me-3 bgl-warning text-warning"><i class="fa fa-hourglass-half fa-2x"></i></span>
            <div class="media-body">
              <p class="mb-1">Under Validation</p>
              <h4 class="mb-0">{{ $kpis['validation'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-sm-6">
      <div class="widget-stat card">
        <div class="card-body p-4">
          <div class="media ai-icon">
            <span class="me-3 bgl-success text-success"><i class="fa fa-check-circle fa-2x"></i></span>
            <div class="media-body">
              <p class="mb-1">Approved</p>
              <h4 class="mb-0">{{ $kpis['approved'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-lg-3 col-sm-6">
      <div class="widget-stat card">
        <div class="card-body p-4">
          <div class="media ai-icon">
            <span class="me-3 bgl-danger text-danger"><i class="fa fa-archive fa-2x"></i></span>
            <div class="media-body">
              <p class="mb-1">Archived</p>
              <h4 class="mb-0">{{ $kpis['archived'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">B2 Clearance Applications</h4>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table id="b2ClearanceTable" class="display table table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>S No</th>
                  <th>Application No.</th>
                  <th>Client / Applicant</th>
                  <th>Project / Location</th>
                  <th>Category</th>
                  <th>Documents</th>
                  <th>Process Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($applications as $app)
                @php
                  $totalDocs = $app->documents->count();
                  $uploadedDocs = $app->documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count();
                @endphp
                <tr>
                  <td>{{ $loop->iteration }}</td>
                  <td><strong>{{ $app->project_code }}</strong></td>
                  <td>{{ $app->customer?->customer_name ?: $app->contact_name }}</td>
                  <td>{{ $app->project_name }} · {{ $app->district?->name ?: $app->location }}</td>
                  <td><span class="badge badge-info">B2</span></td>
                  <td>
                    <a href="{{ route('environment-b2.show', $app) }}" class="text-primary font-w600">
                      {{ $uploadedDocs }} / {{ $totalDocs }} files
                    </a>
                  </td>
                  <td>
                    <span class="badge badge-{{ $app->status === 'approved' ? 'success' : ($app->status === 'archived' ? 'secondary' : ($app->status === 'validation' ? 'warning' : 'primary')) }}">
                      {{ ucfirst($app->status) }}
                    </span>
                  </td>
                  <td>{{ $app->created_at ? $app->created_at->format('d M Y') : '—' }}</td>
                  <td>
                    <a href="{{ route('environment-b2.show', $app) }}" class="btn btn-sm btn-primary">View Project</a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    <i class="fa fa-folder-open fa-2x mb-2 d-block text-muted"></i>
                    No B2 applications found. Click <strong>New B2 Application</strong> to start.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($applications->hasPages())
          <div class="card-footer bg-white border-top py-3 mt-3">
            {{ $applications->links() }}
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div></div>
@endsection
