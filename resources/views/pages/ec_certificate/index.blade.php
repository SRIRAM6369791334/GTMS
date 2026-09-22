@extends('layouts.app')
@section('title', 'Environmental Clearance Certificates')
@section('main_content')

<style>
/* Scoped UI/UX Pro Max Styles for EC Certificates Register */
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
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">EC Certificates</a></li>
        </ol>
      </div>
      <div class="col-lg-6 text-end">
        @can('environment.create')
        <a href="{{ route('ec-certificate.step', 1) }}" class="btn btn-navy" style="background:#0F1E4D; color:#fff;">
          <i class="fa fa-plus me-1"></i> Issue EC Certificate
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
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- ================= KPI METRIC CARDS ================= --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Approved Projects</span>
            <div class="dossier-kpi-icon" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">
              <i class="fa fa-folder-check"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['approved'] ?? 0 }}<span class="dossier-kpi-denom"> apps</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-success">
              <i class="fa fa-check me-1"></i> Ready for Certificate
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Certificates Issued</span>
            <div class="dossier-kpi-icon" style="background:#ecfdf5; color:#059669; border:1px solid #86efac;">
              <i class="fa fa-award"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['issued'] ?? 0 }}<span class="dossier-kpi-denom"> active</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-success">
              <i class="fa fa-certificate me-1"></i> Active SEIAA Grants
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Ready to Download</span>
            <div class="dossier-kpi-icon" style="background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;">
              <i class="fa fa-file-pdf"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['ready_download'] ?? 0 }}<span class="dossier-kpi-denom"> files</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-warning">
              <i class="fa fa-download me-1"></i> Signed PDFs Attached
            </span>
          </div>
        </div>
      </div>

      <div class="col-6 col-lg-3">
        <div class="dossier-kpi-card">
          <div class="dossier-kpi-head">
            <span class="dossier-kpi-label">Parivesh Synced</span>
            <div class="dossier-kpi-icon" style="background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;">
              <i class="fa fa-network-wired"></i>
            </div>
          </div>
          <div class="dossier-kpi-value">{{ $kpis['communicated'] ?? 0 }}<span class="dossier-kpi-denom"> synced</span></div>
          <div class="dossier-kpi-foot">
            <span class="dossier-kpi-pill pill-neutral">
              <i class="fa fa-link me-1"></i> Proposal Linked
            </span>
          </div>
        </div>
      </div>
    </div>

    {{-- ================= CERTIFICATES DATA TABLE CARD ================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
      <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
            Environmental Clearance Certificates Register
          </h5>
          <small class="text-muted">Record of statutory EC grants, validity periods, and attached digital certificates</small>
        </div>

        <form method="GET" action="{{ route('ec-certificate.index') }}" class="d-flex align-items-center gap-1">
          <div class="input-group input-group-sm" style="width:250px;">
            <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Search EC Ref or Applicant..." value="{{ request('search') }}">
          </div>
          <button type="submit" class="btn btn-sm btn-navy" style="background:#0F1E4D; color:#fff;">Search</button>
          @if(request('search'))
            <a href="{{ route('ec-certificate.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
          @endif
        </form>
      </div>

      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" style="font-size:0.88rem;">
            <thead class="table-light text-uppercase text-muted" style="font-size:0.75rem; letter-spacing:0.04em;">
              <tr>
                <th class="ps-4" style="width:40px;">#</th>
                <th>EC Reference No.</th>
                <th>Applicant Name</th>
                <th>Project Name</th>
                <th>Parivesh Reference</th>
                <th>Issue Date</th>
                <th>Validity &amp; Expiry</th>
                <th>Status</th>
                <th class="text-end pe-4" style="min-width:130px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($certificates as $idx => $cert)
              <tr>
                <td class="ps-4 text-muted small">{{ $certificates->firstItem() + $idx }}</td>
                <td>
                  <span class="fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
                    {{ $cert->ec_ref_no }}
                  </span>
                  @if($cert->communication_type)
                    <span class="badge ms-1" style="background:#f1f5f9; color:#475569; font-size:0.7rem; border:1px solid #e2e8f0;">{{ $cert->communication_type }}</span>
                  @endif
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ $cert->applicant_name }}</div>
                  @if($cert->customer?->mimas_no)
                    <div class="text-muted small" style="font-size:0.75rem;"><i class="fa fa-fingerprint me-1"></i>{{ $cert->customer->mimas_no }}</div>
                  @endif
                </td>
                <td>
                  <div class="text-dark">{{ Str::limit($cert->environmentProject?->project_name ?: 'EC Project', 32) }}</div>
                  <div class="text-muted small" style="font-size:0.75rem;">{{ $cert->environmentProject?->project_code }}</div>
                </td>
                <td>
                  <code class="small text-primary" style="background:#eff6ff; padding:2px 6px; border-radius:4px; border:1px solid #bfdbfe;">
                    {{ $cert->parivesh_app_no ?: '—' }}
                  </code>
                </td>
                <td class="small">{{ $cert->issue_date ? $cert->issue_date->format('d M Y') : '—' }}</td>
                <td>
                  <div class="small fw-semibold text-dark">{{ $cert->validity_years ?? 5 }} Years</div>
                  <div class="text-muted small" style="font-size:0.75rem;">Exp: {{ $cert->expiry_date ? $cert->expiry_date->format('d M Y') : '—' }}</div>
                </td>
                <td>
                  <span class="badge" style="background:{{ $cert->status === 'active' ? '#ecfdf5' : '#f1f5f9' }}; color:{{ $cert->status === 'active' ? '#15803d' : '#64748b' }}; border:1px solid {{ $cert->status === 'active' ? '#86efac' : '#e2e8f0' }}; font-size:0.75rem;">
                    {{ ucfirst($cert->status) }}
                  </span>
                </td>
                <td class="text-end pe-4">
                  <div class="table-action-group">
                    {{-- View Authentic Certificate Details --}}
                    <a href="{{ route('ec-certificate.show', $cert->id) }}" class="btn-action-icon" style="background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;" title="View Certificate Details">
                      <i class="fa fa-eye"></i>
                    </a>

                    {{-- Download File if Available --}}
                    @if($cert->certificate_file && file_exists(public_path($cert->certificate_file)))
                      <a href="{{ asset($cert->certificate_file) }}" target="_blank" class="btn-action-icon" style="background:#ecfdf5; color:#15803d; border-color:#86efac;" title="Download Official PDF">
                        <i class="fa fa-download"></i>
                      </a>
                    @endif

                    {{-- Link to Parent Environment Project --}}
                    @if($cert->environment_project_id)
                      <a href="{{ route('eviron.show', $cert->environment_project_id) }}" class="btn-action-icon" style="background:#f5f3ff; color:#7c3aed; border-color:#ddd6fe;" title="Open Project Folders">
                        <i class="fa fa-folder-open"></i>
                      </a>
                    @endif
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-5">
                  <div class="text-muted mb-3"><i class="fa fa-certificate fa-3x opacity-25"></i></div>
                  <div class="fw-semibold fs-6">No EC Certificates recorded yet</div>
                  <div class="small text-muted mt-1">Issue the first certificate using the step-by-step wizard.</div>
                  @can('environment.create')
                  <a href="{{ route('ec-certificate.step', 1) }}" class="btn btn-navy btn-sm mt-3" style="background:#0F1E4D; color:#fff;">
                    <i class="fa fa-plus me-1"></i> Issue EC Certificate
                  </a>
                  @endcan
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if($certificates->hasPages())
        <div class="card-footer bg-white border-top py-3">
          {{ $certificates->links() }}
        </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
