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
          <small class="text-muted">Master register of B1 (TOR &amp; ETA) and B2 clearances</small>
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
                $st = strtolower(trim($proj->status));
                $sc = match(true) {
                  in_array($st, ['approved', 'completed']) => ['#15803d', '#ecfdf5', '#86efac'],
                  in_array($st, ['validation', 'reported']) => ['#1d4ed8', '#eff6ff', '#bfdbfe'],
                  in_array($st, ['call not picked', 'client not responding', 'rejected']) => ['#b91c1c', '#fef2f2', '#fecaca'],
                  in_array($st, ['draft']) => ['#64748b', '#f8fafc', '#cbd5e1'],
                  in_array($st, ['archived']) => ['#7c3aed', '#f5f3ff', '#ddd6fe'],
                  default => ['#a16207', '#fefce8', '#fef08a']
                };
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
                  <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-start btn-update-status"
                          data-bs-toggle="modal"
                          data-bs-target="#modalIndexUpdateStatus"
                          data-action="{{ route('eviron.status', $proj->id) }}"
                          data-code="{{ $proj->project_code }}"
                          data-client="{{ $proj->customer?->company_name ?: ($proj->customer?->customer_name ?: 'Applicant') }}"
                          data-status="{{ $proj->status }}"
                          data-notes="{{ $proj->status_notes ?? '' }}"
                          title="Click to change status">
                    <span class="badge shadow-sm" style="background:{{ $sc[1] }}; color:{{ $sc[0] }}; border:1px solid {{ $sc[2] }}; font-size:0.75rem; cursor:pointer;">
                      {{ ucwords(str_replace('_', ' ', $proj->status)) }}
                      <i class="fa fa-pen ms-1 opacity-50" style="font-size:0.65rem;"></i>
                    </span>
                  </button>
                  @if($proj->status_notes)
                    <div class="text-muted small mt-1 text-truncate" style="max-width:130px; font-size:0.7rem;" title="{{ $proj->status_notes }}">
                      <i class="fa fa-comment-dots text-warning me-1"></i>{{ $proj->status_notes }}
                    </div>
                  @endif
                </td>
                <td class="text-muted small">{{ $proj->created_at->format('d M Y') }}</td>
                <td class="text-end pe-4">
                  <div class="table-action-group">
                    {{-- Quick Change Status --}}
                    <button type="button" class="btn-action-icon btn-update-status"
                            style="background:#fef3c7; color:#b45309; border-color:#fde68a;"
                            data-bs-toggle="modal"
                            data-bs-target="#modalIndexUpdateStatus"
                            data-action="{{ route('eviron.status', $proj->id) }}"
                            data-code="{{ $proj->project_code }}"
                            data-client="{{ $proj->customer?->company_name ?: ($proj->customer?->customer_name ?: 'Applicant') }}"
                            data-status="{{ $proj->status }}"
                            data-notes="{{ $proj->status_notes ?? '' }}"
                            title="Change Status">
                      <i class="fa fa-pen"></i>
                    </button>

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

{{-- Dynamic Shared Index Update Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalIndexUpdateStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalIndexUpdateStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalIndexUpdateStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #3B82F6 50%, #10B981 100%);
  }
  #modalIndexUpdateStatus .status-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(15, 30, 77, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0F1E4D;
    font-size: 1.25rem;
    flex-shrink: 0;
  }
  #modalIndexUpdateStatus .preset-status-btn {
    font-size: 0.76rem;
    font-weight: 500;
    padding: 0.32rem 0.65rem;
    border-radius: 8px;
    transition: all 0.15s ease-in-out;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
  }
  #modalIndexUpdateStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalIndexUpdateStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalIndexUpdateStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalIndexUpdateStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalIndexUpdateStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalIndexUpdateStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalIndexUpdateStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalIndexUpdateStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalIndexUpdateStatus" tabindex="-1" aria-labelledby="modalIndexUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formIndexUpdateStatus" action="">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-pen-to-square"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalIndexUpdateStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update Project Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i><span id="modal_project_code_text">--</span>
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;" id="modal_client_text">
                  <i class="fa fa-building me-1 opacity-50"></i><span>--</span>
                </span>
              </div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        {{-- Modal Body --}}
        <div class="modal-body p-4 pt-3">
          
          {{-- Live Status Badge Preview Card --}}
          <div class="p-3 mb-3 rounded-3 border" style="background:#f8fafc; border-color:#e2e8f0 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-uppercase fw-bold text-muted" style="font-size:0.68rem; letter-spacing:0.05em;">
                <i class="fa fa-eye me-1 text-primary"></i>Live Badge Preview
              </span>
              <span class="text-muted" style="font-size:0.7rem;">Real-time table appearance</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span id="live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                Draft
              </span>
              <span class="text-muted small ms-auto" id="live_status_category_hint" style="font-size:0.72rem;">Standard Stage</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="index_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="index_status_input" class="form-control border-start-0 ps-1"
                     list="index_status_suggestions" placeholder="e.g. call not picked, validation, approved..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="index_status_suggestions">
              <option value="draft">Draft</option>
              <option value="validation">Validation</option>
              <option value="approved">Approved</option>
              <option value="reported">Reported</option>
              <option value="completed">Completed</option>
              <option value="call not picked">Call Not Picked</option>
              <option value="client not responding">Client Not Responding</option>
              <option value="site inspection pending">Site Inspection Pending</option>
              <option value="documents pending">Documents Pending</option>
              <option value="waiting for patta">Waiting for Patta</option>
              <option value="archived">Archived</option>
            </datalist>
          </div>

          {{-- Categorized Preset Pills --}}
          <div class="mb-3">
            {{-- Workflow Milestones --}}
            <div class="mb-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-diagram-project me-1 text-primary"></i>Standard Workflow Stages
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="group_workflow_presets">
                <button type="button" class="preset-status-btn" data-value="draft">
                  <i class="fa fa-file-pen text-secondary"></i>Draft
                </button>
                <button type="button" class="preset-status-btn" data-value="validation">
                  <i class="fa fa-magnifying-glass text-info"></i>Validation
                </button>
                <button type="button" class="preset-status-btn" data-value="approved">
                  <i class="fa fa-circle-check text-success"></i>Approved
                </button>
                <button type="button" class="preset-status-btn" data-value="reported">
                  <i class="fa fa-file-invoice text-primary"></i>Reported
                </button>
                <button type="button" class="preset-status-btn" data-value="archived">
                  <i class="fa fa-box-archive text-muted"></i>Archived
                </button>
              </div>
            </div>

            {{-- Operational Delays & Follow-ups --}}
            <div>
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-phone-slash me-1 text-danger"></i>Operational Follow-ups & Delays
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="group_operational_presets">
                <button type="button" class="preset-status-btn" data-value="call not picked">
                  <i class="fa fa-phone-slash text-danger"></i>Call Not Picked
                </button>
                <button type="button" class="preset-status-btn" data-value="client not responding">
                  <i class="fa fa-user-clock text-warning"></i>Client Not Responding
                </button>
                <button type="button" class="preset-status-btn" data-value="site inspection pending">
                  <i class="fa fa-map-location-dot text-primary"></i>Site Inspection Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="documents pending">
                  <i class="fa fa-folder-open text-warning"></i>Documents Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="waiting for patta">
                  <i class="fa fa-file-signature text-secondary"></i>Waiting for Patta
                </button>
              </div>
            </div>
          </div>

          {{-- Follow-up Remarks & Call Log --}}
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="index_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Follow-up Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="notes_counter" style="font-size:0.72rem;">0 / 1000</span>
            </div>
            <textarea name="status_notes" id="index_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter follow-up remarks, client call logs, reason for delay, next follow-up date..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;"></textarea>

            {{-- Quick Chip Inserts for Notes --}}
            <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
              <span class="text-muted small me-1" style="font-size:0.7rem;"><i class="fa fa-bolt me-1 text-warning"></i>Quick log:</span>
              <button type="button" class="quick-note-chip" data-text="Called applicant; line was busy. Scheduled follow-up.">+ Call Busy</button>
              <button type="button" class="quick-note-chip" data-text="Client requested 2 working days to submit pending documents.">+ Need 2 Days</button>
              <button type="button" class="quick-note-chip" data-text="Site inspection postponed due to weather conditions.">+ Inspection Postponed</button>
              <button type="button" class="quick-note-chip" data-text="Parivesh government portal under scheduled maintenance.">+ Portal Down</button>
            </div>
          </div>

        </div>

        {{-- Modal Footer --}}
        <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
          <div class="text-muted small" style="font-size:0.72rem;">
            <kbd style="background:#e2e8f0; color:#475569; padding:2px 5px; border-radius:4px; font-size:0.68rem;">Ctrl</kbd> + <kbd style="background:#e2e8f0; color:#475569; padding:2px 5px; border-radius:4px; font-size:0.68rem;">Enter</kbd> to save
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" style="font-weight:500; font-size:0.85rem;">Cancel</button>
            <button type="submit" class="btn btn-save-status shadow-sm" id="btn_submit_status">
              <i class="fa fa-floppy-disk me-1"></i> Update Status
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modalIndexStatus = document.getElementById('modalIndexUpdateStatus');
  const formIndexStatus = document.getElementById('formIndexUpdateStatus');
  const inputStatus = document.getElementById('index_status_input');
  const textareaNotes = document.getElementById('index_status_notes');
  const liveBadge = document.getElementById('live_status_badge');
  const liveCategoryHint = document.getElementById('live_status_category_hint');
  const notesCounter = document.getElementById('notes_counter');
  const btnClear = document.getElementById('btn_clear_status');
  const btnSubmit = document.getElementById('btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalIndexUpdateStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalIndexUpdateStatus .quick-note-chip');

  // Palette mapper matching backend badge logic
  function renderLiveStatus(rawStatus) {
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Draft';
    
    liveBadge.textContent = formatted;

    // Green palette
    if (['approved', 'completed', 'active', 'verified', 'passed'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      liveCategoryHint.textContent = 'Milestone: Approved';
    } 
    // Blue / Indigo palette
    else if (['validation', 'uploaded', 'presented', 'agenda_scheduled', 'uploaded_to_parivesh'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      liveCategoryHint.textContent = 'Milestone: In Progress';
    } 
    // Red / Coral palette
    else if (['call not picked', 'client not responding', 'rejected', 'revision_required', 'expired', 'surrendered', 'revoked'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      liveCategoryHint.textContent = 'Alert: Follow-up Required';
    } 
    // Amber / Warning palette
    else if (['site inspection pending', 'documents pending', 'waiting for patta'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      liveCategoryHint.textContent = 'Action: Pending Step';
    } 
    // Slate / Neutral palette
    else if (['draft', 'archived'].includes(s)) {
      liveBadge.style.background = '#f1f5f9';
      liveBadge.style.color = '#475569';
      liveBadge.style.border = '1px solid #cbd5e1';
      liveCategoryHint.textContent = 'Milestone: Standard';
    } 
    // Custom user-typed status
    else {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      liveCategoryHint.textContent = 'Custom Status';
    }

    // Highlight matching preset button
    presetButtons.forEach(btn => {
      if (btn.dataset.value.toLowerCase() === s) {
        btn.classList.add('active-preset');
      } else {
        btn.classList.remove('active-preset');
      }
    });
  }

  // Update notes character counter
  function updateNotesCounter() {
    if (textareaNotes && notesCounter) {
      const len = textareaNotes.value.length;
      notesCounter.textContent = len + ' / 1000';
    }
  }

  // Populate data on modal open
  if (modalIndexStatus) {
    modalIndexStatus.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        formIndexStatus.action = btn.dataset.action || '';
        document.getElementById('modal_project_code_text').textContent = btn.dataset.code || '--';
        document.getElementById('modal_client_text').textContent = btn.dataset.client || '--';
        inputStatus.value = btn.dataset.status || '';
        textareaNotes.value = btn.dataset.notes || '';
        renderLiveStatus(inputStatus.value);
        updateNotesCounter();
      }
    });

    modalIndexStatus.addEventListener('shown.bs.modal', function() {
      inputStatus.focus();
    });
  }

  // Live input sync
  if (inputStatus) {
    inputStatus.addEventListener('input', function() {
      renderLiveStatus(this.value);
    });
  }

  // Clear button
  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderLiveStatus('');
    });
  }

  // Preset button click
  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      inputStatus.value = this.dataset.value;
      renderLiveStatus(this.dataset.value);
      inputStatus.focus();
    });
  });

  // Note chips click
  noteChips.forEach(chip => {
    chip.addEventListener('click', function() {
      const textToAppend = this.dataset.text;
      if (textareaNotes) {
        if (textareaNotes.value.trim() === '') {
          textareaNotes.value = textToAppend;
        } else {
          textareaNotes.value = textareaNotes.value.trim() + ' ' + textToAppend;
        }
        updateNotesCounter();
        textareaNotes.focus();
      }
    });
  });

  // Textarea input event for counter
  if (textareaNotes) {
    textareaNotes.addEventListener('input', updateNotesCounter);
  }

  // Keyboard shortcut Ctrl + Enter to submit
  if (formIndexStatus) {
    formIndexStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        btnSubmit.click();
      }
    });

    formIndexStatus.addEventListener('submit', function() {
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
    });
  }
});
</script>
@endsection
