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
                  @php
                    $st = strtolower(trim($cert->status));
                    $certStyle = match(true) {
                      in_array($st, ['active']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
                      in_array($st, ['call not picked', 'revoked', 'expired']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                      in_array($st, ['surrendered']) => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
                      default => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
                    };
                  @endphp
                  <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-start btn-update-status"
                          data-bs-toggle="modal"
                          data-bs-target="#modalCertIndexUpdateStatus"
                          data-action="{{ route('ec-certificate.status', $cert->id) }}"
                          data-code="{{ $cert->ec_ref_no }}"
                          data-client="{{ $cert->applicant_name }}"
                          data-status="{{ $cert->status }}"
                          data-notes="{{ $cert->status_notes ?? '' }}"
                          title="Click to update status">
                    <span class="badge shadow-sm" style="{{ $certStyle }} font-size:0.75rem; cursor:pointer;">
                      {{ ucwords(str_replace('_', ' ', $cert->status)) }}
                      <i class="fa fa-pen ms-1 opacity-50" style="font-size:0.65rem;"></i>
                    </span>
                  </button>
                  @if($cert->status_notes)
                    <div class="text-muted small mt-1 text-truncate" style="max-width:130px; font-size:0.7rem;" title="{{ $cert->status_notes }}">
                      <i class="fa fa-comment-dots text-warning me-1"></i>{{ $cert->status_notes }}
                    </div>
                  @endif
                </td>
                <td class="text-end pe-4">
                  <div class="table-action-group">
                    {{-- Quick Change Status --}}
                    <button type="button" class="btn-action-icon btn-update-status"
                            style="background:#fef3c7; color:#b45309; border-color:#fde68a;"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCertIndexUpdateStatus"
                            data-action="{{ route('ec-certificate.status', $cert->id) }}"
                            data-code="{{ $cert->ec_ref_no }}"
                            data-client="{{ $cert->applicant_name }}"
                            data-status="{{ $cert->status }}"
                            data-notes="{{ $cert->status_notes ?? '' }}"
                            title="Change Status">
                      <i class="fa fa-pen"></i>
                    </button>

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

{{-- Dynamic Shared Index Update Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalCertIndexUpdateStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalCertIndexUpdateStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalCertIndexUpdateStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #10B981 50%, #059669 100%);
  }
  #modalCertIndexUpdateStatus .status-icon-wrapper {
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
  #modalCertIndexUpdateStatus .preset-status-btn {
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
  #modalCertIndexUpdateStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalCertIndexUpdateStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalCertIndexUpdateStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalCertIndexUpdateStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalCertIndexUpdateStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalCertIndexUpdateStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalCertIndexUpdateStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalCertIndexUpdateStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalCertIndexUpdateStatus" tabindex="-1" aria-labelledby="modalCertIndexUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formCertIndexUpdateStatus" action="">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-stamp"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalCertIndexUpdateStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update EC Certificate Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i><span id="modal_cert_code_text">--</span>
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;" id="modal_cert_client_text">
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
              <span id="cert_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                Active
              </span>
              <span class="text-muted small ms-auto" id="cert_live_status_category_hint" style="font-size:0.72rem;">Milestone: Active</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="cert_index_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="cert_index_status_input" class="form-control border-start-0 ps-1"
                     list="cert_index_status_suggestions" placeholder="e.g. active, expired, dispatch pending..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="cert_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="cert_index_status_suggestions">
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="surrendered">Surrendered</option>
              <option value="revoked">Revoked</option>
              <option value="call not picked">Call Not Picked</option>
              <option value="dispatch pending">Dispatch Pending</option>
              <option value="client collected">Client Collected</option>
              <option value="renewal under process">Renewal Under Process</option>
              <option value="original copy pending">Original Copy Pending</option>
            </datalist>
          </div>

          {{-- Categorized Preset Pills --}}
          <div class="mb-3">
            {{-- Workflow Milestones --}}
            <div class="mb-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-diagram-project me-1 text-primary"></i>Statutory Certificate Stages
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="cert_group_workflow_presets">
                <button type="button" class="preset-status-btn" data-value="active">
                  <i class="fa fa-circle-check text-success"></i>Active
                </button>
                <button type="button" class="preset-status-btn" data-value="expired">
                  <i class="fa fa-calendar-xmark text-danger"></i>Expired
                </button>
                <button type="button" class="preset-status-btn" data-value="surrendered">
                  <i class="fa fa-hand-holding-hand text-warning"></i>Surrendered
                </button>
                <button type="button" class="preset-status-btn" data-value="revoked">
                  <i class="fa fa-ban text-danger"></i>Revoked
                </button>
              </div>
            </div>

            {{-- Operational Delays & Follow-ups --}}
            <div>
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-phone-slash me-1 text-danger"></i>Operational Follow-ups & Handover
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="cert_group_operational_presets">
                <button type="button" class="preset-status-btn" data-value="call not picked">
                  <i class="fa fa-phone-slash text-danger"></i>Call Not Picked
                </button>
                <button type="button" class="preset-status-btn" data-value="dispatch pending">
                  <i class="fa fa-truck-fast text-info"></i>Dispatch Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="client collected">
                  <i class="fa fa-handshake text-success"></i>Client Collected
                </button>
                <button type="button" class="preset-status-btn" data-value="renewal under process">
                  <i class="fa fa-arrows-rotate text-primary"></i>Renewal in Process
                </button>
                <button type="button" class="preset-status-btn" data-value="original copy pending">
                  <i class="fa fa-file-lines text-warning"></i>Original Pending
                </button>
              </div>
            </div>
          </div>

          {{-- Follow-up Remarks & Call Log --}}
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="cert_index_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Handover Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="cert_notes_counter" style="font-size:0.72rem;">0 / 1000</span>
            </div>
            <textarea name="status_notes" id="cert_index_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter courier dispatch tracking, client signature handover, follow-up call notes..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;"></textarea>

            {{-- Quick Chip Inserts for Notes --}}
            <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
              <span class="text-muted small me-1" style="font-size:0.7rem;"><i class="fa fa-bolt me-1 text-warning"></i>Quick log:</span>
              <button type="button" class="quick-note-chip" data-text="Original hardcopy EC certificate dispatched via registered post.">+ Dispatched</button>
              <button type="button" class="quick-note-chip" data-text="Certificate physically handed over to authorized applicant representative.">+ Handed Over</button>
              <button type="button" class="quick-note-chip" data-text="Called client to collect original clearance order; line busy.">+ Call Busy</button>
              <button type="button" class="quick-note-chip" data-text="EC validity renewal application initiated with SEIAA.">+ Renewal Initiated</button>
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
            <button type="submit" class="btn btn-save-status shadow-sm" id="cert_btn_submit_status">
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
  const modalCertIndexStatus = document.getElementById('modalCertIndexUpdateStatus');
  const formCertIndexStatus = document.getElementById('formCertIndexUpdateStatus');
  const inputStatus = document.getElementById('cert_index_status_input');
  const textareaNotes = document.getElementById('cert_index_status_notes');
  const liveBadge = document.getElementById('cert_live_status_badge');
  const liveCategoryHint = document.getElementById('cert_live_status_category_hint');
  const notesCounter = document.getElementById('cert_notes_counter');
  const btnClear = document.getElementById('cert_btn_clear_status');
  const btnSubmit = document.getElementById('cert_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalCertIndexUpdateStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalCertIndexUpdateStatus .quick-note-chip');

  function renderLiveStatus(rawStatus) {
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Active';
    
    liveBadge.textContent = formatted;

    if (['active', 'client collected', 'approved'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      liveCategoryHint.textContent = 'Milestone: Active';
    } 
    else if (['renewal under process', 'dispatch pending'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      liveCategoryHint.textContent = 'Milestone: In Progress';
    } 
    else if (['expired', 'surrendered', 'revoked', 'call not picked'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      liveCategoryHint.textContent = 'Alert: Status Flagged';
    } 
    else if (['original copy pending'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      liveCategoryHint.textContent = 'Action: Pending Step';
    } 
    else {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      liveCategoryHint.textContent = 'Custom Status';
    }

    presetButtons.forEach(btn => {
      if (btn.dataset.value.toLowerCase() === s) {
        btn.classList.add('active-preset');
      } else {
        btn.classList.remove('active-preset');
      }
    });
  }

  function updateNotesCounter() {
    if (textareaNotes && notesCounter) {
      notesCounter.textContent = textareaNotes.value.length + ' / 1000';
    }
  }

  if (modalCertIndexStatus) {
    modalCertIndexStatus.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        formCertIndexStatus.action = btn.dataset.action || '';
        document.getElementById('modal_cert_code_text').textContent = btn.dataset.code || '--';
        document.getElementById('modal_cert_client_text').textContent = btn.dataset.client || '--';
        inputStatus.value = btn.dataset.status || '';
        textareaNotes.value = btn.dataset.notes || '';
        renderLiveStatus(inputStatus.value);
        updateNotesCounter();
      }
    });

    modalCertIndexStatus.addEventListener('shown.bs.modal', function() {
      inputStatus.focus();
    });
  }

  if (inputStatus) {
    inputStatus.addEventListener('input', function() {
      renderLiveStatus(this.value);
    });
  }

  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderLiveStatus('');
    });
  }

  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      inputStatus.value = this.dataset.value;
      renderLiveStatus(this.dataset.value);
      inputStatus.focus();
    });
  });

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

  if (textareaNotes) {
    textareaNotes.addEventListener('input', updateNotesCounter);
  }

  if (formCertIndexStatus) {
    formCertIndexStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        btnSubmit.click();
      }
    });

    formCertIndexStatus.addEventListener('submit', function() {
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
    });
  }
});
</script>
@endsection
