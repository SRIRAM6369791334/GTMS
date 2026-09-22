@extends('layouts.app')
@section('title', 'Environment Clearance — All Applications')
@section('main_content')

<style>
/* Scoped UI/UX Pro Max Styles for Environment Clearance */
.dossier-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 1.15rem 1.25rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 126px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05), 0 1px 2px rgba(15, 23, 42, 0.02);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}
.dossier-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    border-color: #cbd5e1;
}
.dossier-kpi-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.45rem;
}
.dossier-kpi-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
    letter-spacing: 0.01em;
    line-height: 1.3;
    margin: 0;
}
.dossier-kpi-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.dossier-kpi-card:hover .dossier-kpi-icon {
    transform: scale(1.05);
}
.dossier-kpi-value {
    font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 1.6rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.15;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    letter-spacing: -0.02em;
    margin: 0.15rem 0 0.55rem 0;
}
.dossier-kpi-denom {
    font-size: 0.95rem;
    font-weight: 500;
    color: #94a3b8;
    margin-left: 2px;
}
.dossier-kpi-foot {
    display: flex;
    align-items: center;
    margin-top: auto;
}
.dossier-kpi-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.22rem 0.65rem;
    border-radius: 999px;
    line-height: 1.2;
}
.dossier-kpi-pill.pill-success {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
}
.dossier-kpi-pill.pill-warning {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}
.dossier-kpi-pill.pill-neutral {
    background: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
}

.breadcrumb-item.active {
    color: #0F1E4D !important;
    font-weight: 600;
}
.btn-filter-toggle {
    border: 1px solid #cbd5e1 !important;
    color: #475569 !important;
    background: #ffffff !important;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.25rem 0.65rem;
    transition: all 0.2s ease;
}
.btn-filter-toggle:hover {
    background: #f8fafc !important;
    color: #0F1E4D !important;
    border-color: #0F1E4D !important;
}
.btn-filter-toggle.active {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
}

/* Action Group Buttons */
.table-action-group {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
    white-space: nowrap;
}
.btn-action-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.88rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    padding: 0;
}
.btn-action-icon:hover {
    transform: translateY(-1px);
}
</style>

<div class="content-body default-height">
  <div class="container-fluid">

    {{-- Breadcrumb & Action Header --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-lg-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">Environment Clearance</a></li>
        </ol>
      </div>
      <div class="col-lg-6 text-end">
        @can('environment.b2.create')
        <a href="{{ route('eviron.create') }}" class="btn btn-navy" style="background:#0F1E4D; color:#fff;">
          <i class="fa fa-plus me-1"></i> New Application
        </a>
        @endcan
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('info'))
      <div class="alert alert-info alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- ================= KPI METRIC CARDS ================= --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Total Applications</span>
            <div class="dossier-kpi-icon" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
              <i class="fa fa-folder-open"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['total'] }}<span class="dossier-kpi-denom"> apps</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-neutral">
              <i class="fa fa-layer-group me-1"></i> B1: {{ $kpis['b1_count'] }} &bull; B2: {{ $kpis['b2_count'] }}
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">In Progress</span>
            <div class="dossier-kpi-icon" style="background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;">
              <i class="fa fa-hourglass-half"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['in_progress'] }}<span class="dossier-kpi-denom"> active</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-warning">
              <i class="fa fa-clock me-1"></i> Draft + Validation
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Approved Projects</span>
            <div class="dossier-kpi-icon" style="background:#ecfdf5; color:#059669; border:1px solid #86efac;">
              <i class="fa fa-check-circle"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['approved'] }}<span class="dossier-kpi-denom"> approved</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-success">
              <i class="fa fa-circle-check me-1"></i> {{ $kpis['doc_approved'] }} docs approved
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">EC Certificates</span>
            <div class="dossier-kpi-icon" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;">
              <i class="fa fa-certificate"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['ec_issued'] }}<span class="dossier-kpi-denom"> issued</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-success">
              <i class="fa fa-award me-1"></i> Grants Recorded
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ================= APPLICATIONS DATA TABLE CARD ================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
      <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
            All Environment Clearance Applications
          </h5>
          <small class="text-muted">Master register of B1 (Sub Category 1 &amp; 2) and B2 clearances</small>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
          {{-- Search Form --}}
          <form method="GET" action="{{ route('eviron.index') }}" class="d-flex align-items-center gap-1">
            @if(request('category'))
              <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="input-group input-group-sm" style="width:230px;">
              <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
              <input type="text" name="search" class="form-control border-start-0" placeholder="Search code, client..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-sm btn-navy" style="background:#0F1E4D; color:#fff;">Search</button>
            @if(request('search'))
              <a href="{{ route('eviron.index', request()->only('category')) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            @endif
          </form>

          {{-- Filter Tabs --}}
          <div class="btn-group btn-group-sm ms-2">
            <a href="{{ route('eviron.index', request()->only('search')) }}"
               class="btn btn-filter-toggle {{ !request('category') ? 'active' : '' }}">
              All ({{ $kpis['total'] }})
            </a>
            <a href="{{ route('eviron.index', array_merge(request()->only('search'), ['category' => 'B1'])) }}"
               class="btn btn-filter-toggle {{ request('category') === 'B1' ? 'active' : '' }}">
              B1 ({{ $kpis['b1_count'] }})
            </a>
            <a href="{{ route('eviron.index', array_merge(request()->only('search'), ['category' => 'B2'])) }}"
               class="btn btn-filter-toggle {{ request('category') === 'B2' ? 'active' : '' }}">
              B2 ({{ $kpis['b2_count'] }})
            </a>
          </div>
        </div>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="table-light text-uppercase text-muted" style="font-size:0.75rem; letter-spacing:0.04em;">
              <tr>
                <th class="ps-4" style="width:40px;">#</th>
                <th>Project Code</th>
                <th>Client / Applicant</th>
                <th>Project Name</th>
                <th>District</th>
                <th>Category</th>
                <th>Documents</th>
                <th>Status</th>
                <th>Created</th>
                <th class="text-end pe-4" style="min-width:130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($allProjects as $idx => $proj)
              @php
                $totalDocs = $proj->documents->count();
                $uploadedDocs = $proj->documents->whereNotNull('file_path')->count();
                $pct = $totalDocs > 0 ? round(($uploadedDocs / $totalDocs) * 100) : 0;
                $statusColors = [
                  'draft'      => ['#64748b', '#f1f5f9', '#e2e8f0'],
                  'validation' => ['#b45309', '#fef3c7', '#fde68a'],
                  'approved'   => ['#15803d', '#ecfdf5', '#86efac'],
                  'reported'   => ['#1d4ed8', '#eff6ff', '#bfdbfe'],
                  'archived'   => ['#7c3aed', '#f5f3ff', '#ddd6fe'],
                ];
                $sc = $statusColors[$proj->status] ?? ['#64748b', '#f1f5f9', '#e2e8f0'];
              @endphp
              <tr>
                <td class="ps-4 text-muted small">{{ $allProjects->firstItem() + $idx }}</td>
                <td>
                  <a href="{{ route('eviron.show', $proj->id) }}" class="fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
                    {{ $proj->project_code }}
                  </a>
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ $proj->customer?->company_name ?: ($proj->customer?->customer_name ?: 'Applicant') }}</div>
                  @if($proj->contact_phone)
                    <div class="text-muted small" style="font-size:0.75rem;">
                      <i class="fa fa-phone me-1 text-muted"></i>{{ $proj->contact_phone }}
                    </div>
                  @endif
                </td>
                <td>
                  <div class="text-dark">{{ Str::limit($proj->project_name, 35) }}</div>
                  @if($proj->location)
                    <div class="text-muted small" style="font-size:0.75rem;">{{ Str::limit($proj->location, 30) }}</div>
                  @endif
                </td>
                <td>{{ $proj->district?->name ?? '—' }}</td>
                <td>
                  @if($proj->category === 'B1')
                    <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">B1</span>
                    @if($proj->sub_category)
                      <span class="badge ms-1" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;">{{ $proj->sub_category }}</span>
                    @endif
                  @else
                    <span class="badge" style="background:#ecfdf5; color:#15803d; border:1px solid #86efac;">B2</span>
                  @endif
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2" style="width:110px;">
                    <div class="progress flex-grow-1" style="height:6px; background:#e2e8f0; border-radius:999px;">
                      <div class="progress-bar" style="width:{{ $pct }}%; background:#0F1E4D;"></div>
                    </div>
                    <span class="text-muted" style="font-size:0.75rem; white-space:nowrap;">{{ $uploadedDocs }}/{{ $totalDocs }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge" style="background:{{ $sc[1] }}; color:{{ $sc[0] }}; border:1px solid {{ $sc[2] }}; font-size:0.75rem;">
                    {{ ucfirst($proj->status) }}
                  </span>
                </td>
                <td class="text-muted small">{{ $proj->created_at->format('d M Y') }}</td>
                <td class="text-end pe-4">
                  <div class="table-action-group">
                    {{-- Open Folders Action --}}
                    <a href="{{ route('eviron.show', $proj->id) }}" class="btn-action-icon" style="background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;" title="Open Project Folders">
                      <i class="fa fa-folder-open"></i>
                    </a>

                    {{-- Issue / View EC Certificate --}}
                    @if($proj->ecCertificates?->isNotEmpty())
                      <a href="{{ route('ec-certificate.show', $proj->ecCertificates->first()->id) }}" class="btn-action-icon" style="background:#f5f3ff; color:#7c3aed; border-color:#ddd6fe;" title="View EC Certificate" aria-label="View EC Certificate">
                        <i class="fa fa-certificate"></i>
                        <span class="visually-hidden">View EC Certificate</span>
                      </a>
                    @elseif($proj->status === 'approved')
                      <a href="{{ route('ec-certificate.step', 1) }}?project_id={{ $proj->id }}" class="btn-action-icon" style="background:#ecfdf5; color:#15803d; border-color:#86efac;" title="Issue EC Certificate" aria-label="Issue EC Certificate">
                        <i class="fa fa-plus-circle"></i>
                        <span class="visually-hidden">Issue EC Certificate</span>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="10" class="text-center py-5">
                  <div class="text-muted mb-3"><i class="fa fa-leaf fa-3x opacity-25"></i></div>
                  <div class="fw-semibold fs-6">No applications found</div>
                  <div class="small text-muted mt-1">Start by creating a new Environment Clearance project.</div>
                  @can('environment.b2.create')
                  <a href="{{ route('eviron.create') }}" class="btn btn-navy btn-sm mt-3" style="background:#0F1E4D; color:#fff;">
                    <i class="fa fa-plus me-1"></i> New Application
                  </a>
                  @endcan
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($allProjects->hasPages())
        <div class="card-footer bg-white border-top py-3">
          {{ $allProjects->links() }}
        </div>
        @endif
      </div>
    </div>

    {{-- ================= RECENT ACTIVITY CARD ================= --}}
    @if($recentActivities->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
      <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
          <i class="fa fa-clock-rotate-left me-2 text-muted"></i>Recent Lifecycle Activity
        </h6>
      </div>
      <div class="card-body p-0">
        <ul class="list-group list-group-flush">
          @foreach($recentActivities as $act)
          <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-4">
            <div class="d-flex align-items-center gap-3">
              <span class="rounded-circle p-1" style="background:#ecfdf5; color:#059669; font-size:0.75rem;">
                <i class="fa fa-circle-dot"></i>
              </span>
              <span class="small fw-semibold text-dark">{{ $act->description ?? $act->action }}</span>
            </div>
            <span class="text-muted small" style="font-size:0.75rem;">{{ $act->created_at?->diffForHumans() }}</span>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
    @endif

  </div>
</div>
@endsection
