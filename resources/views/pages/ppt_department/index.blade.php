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
                    @if($app->presentation_stage)
                      <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:10px;">
                        {{ $app->presentation_stage === 'tor_presentation' ? 'ToR Gate' : 'Final EC Gate' }}
                      </span>
                    @endif
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
                      $st = strtolower(trim($app->status));
                      $badgeStyle = match(true) {
                        in_array($st, ['approved', 'presented']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
                        in_array($st, ['agenda_scheduled'])     => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
                        in_array($st, ['call not picked', 'client not responding', 'rejected']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                        in_array($st, ['archived'])             => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
                        in_array($st, ['draft'])                => 'background:#f8fafc; color:#64748b; border:1px solid #cbd5e1;',
                        default                                 => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
                      };
                    @endphp
                    <button type="button" class="btn btn-sm p-0 border-0 bg-transparent text-start btn-update-status"
                            data-bs-toggle="modal"
                            data-bs-target="#modalPptIndexUpdateStatus"
                            data-action="{{ route('ppt-department.status', $app->id) }}"
                            data-code="{{ $app->application_no }}"
                            data-client="{{ $app->customer?->company_name ?: ($app->customer?->customer_name ?: 'Applicant') }}"
                            data-status="{{ $app->status }}"
                            data-notes="{{ $app->status_notes ?? '' }}"
                            title="Click to update status">
                      <span class="badge shadow-sm" style="{{ $badgeStyle }} font-size:0.75rem; cursor:pointer;">
                        {{ ucwords(str_replace('_', ' ', $app->status)) }}
                        <i class="fa fa-pen ms-1 opacity-50" style="font-size:0.65rem;"></i>
                      </span>
                    </button>
                    @if($app->status_notes)
                      <div class="text-muted small mt-1 text-truncate" style="max-width:130px; font-size:0.7rem;" title="{{ $app->status_notes }}">
                        <i class="fa fa-comment-dots text-warning me-1"></i>{{ $app->status_notes }}
                      </div>
                    @endif
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
                      <button type="button" class="btn btn-outline-warning"
                              data-bs-toggle="modal"
                              data-bs-target="#modalPptIndexUpdateStatus"
                              data-action="{{ route('ppt-department.status', $app->id) }}"
                              data-code="{{ $app->application_no }}"
                              data-client="{{ $app->customer?->company_name ?: ($app->customer?->customer_name ?: 'Applicant') }}"
                              data-status="{{ $app->status }}"
                              data-notes="{{ $app->status_notes ?? '' }}"
                              title="Change Status">
                        <i class="bi bi-tag"></i>
                      </button>
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

{{-- Dynamic Shared Index Update Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalPptIndexUpdateStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalPptIndexUpdateStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalPptIndexUpdateStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #3B82F6 50%, #8B5CF6 100%);
  }
  #modalPptIndexUpdateStatus .status-icon-wrapper {
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
  #modalPptIndexUpdateStatus .preset-status-btn {
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
  #modalPptIndexUpdateStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalPptIndexUpdateStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalPptIndexUpdateStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalPptIndexUpdateStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalPptIndexUpdateStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalPptIndexUpdateStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalPptIndexUpdateStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalPptIndexUpdateStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalPptIndexUpdateStatus" tabindex="-1" aria-labelledby="modalPptIndexUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formPptIndexUpdateStatus" action="">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-person-chalkboard"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalPptIndexUpdateStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update PPT Application Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i><span id="modal_ppt_code_text">--</span>
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;" id="modal_ppt_client_text">
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
              <span id="ppt_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                Draft
              </span>
              <span class="text-muted small ms-auto" id="ppt_live_status_category_hint" style="font-size:0.72rem;">Standard Stage</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="ppt_index_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="ppt_index_status_input" class="form-control border-start-0 ps-1"
                     list="ppt_index_status_suggestions" placeholder="e.g. presented, agenda_scheduled, call not picked..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="ppt_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="ppt_index_status_suggestions">
              <option value="draft">Draft</option>
              <option value="agenda_scheduled">Agenda Scheduled</option>
              <option value="presented">Presented</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="call not picked">Call Not Picked</option>
              <option value="client not responding">Client Not Responding</option>
              <option value="presentation review pending">Presentation Review Pending</option>
              <option value="rescheduled">Rescheduled</option>
              <option value="payment pending">Payment Pending</option>
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
              <div class="d-flex flex-wrap gap-1" id="ppt_group_workflow_presets">
                <button type="button" class="preset-status-btn" data-value="draft">
                  <i class="fa fa-file-pen text-secondary"></i>Draft
                </button>
                <button type="button" class="preset-status-btn" data-value="agenda_scheduled">
                  <i class="fa fa-calendar-check text-info"></i>Agenda Scheduled
                </button>
                <button type="button" class="preset-status-btn" data-value="presented">
                  <i class="fa fa-person-chalkboard text-primary"></i>Presented
                </button>
                <button type="button" class="preset-status-btn" data-value="approved">
                  <i class="fa fa-circle-check text-success"></i>Approved
                </button>
                <button type="button" class="preset-status-btn" data-value="rejected">
                  <i class="fa fa-circle-xmark text-danger"></i>Rejected
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
              <div class="d-flex flex-wrap gap-1" id="ppt_group_operational_presets">
                <button type="button" class="preset-status-btn" data-value="call not picked">
                  <i class="fa fa-phone-slash text-danger"></i>Call Not Picked
                </button>
                <button type="button" class="preset-status-btn" data-value="client not responding">
                  <i class="fa fa-user-clock text-warning"></i>Client Not Responding
                </button>
                <button type="button" class="preset-status-btn" data-value="presentation review pending">
                  <i class="fa fa-magnifying-glass-chart text-info"></i>Review Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="rescheduled">
                  <i class="fa fa-arrows-rotate text-warning"></i>Rescheduled
                </button>
                <button type="button" class="preset-status-btn" data-value="payment pending">
                  <i class="fa fa-receipt text-secondary"></i>Payment Pending
                </button>
              </div>
            </div>
          </div>

          {{-- Follow-up Remarks & Call Log --}}
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="ppt_index_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Follow-up Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="ppt_notes_counter" style="font-size:0.72rem;">0 / 1000</span>
            </div>
            <textarea name="status_notes" id="ppt_index_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter follow-up remarks, meeting outcomes, presentation revisions, client call details..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;"></textarea>

            {{-- Quick Chip Inserts for Notes --}}
            <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
              <span class="text-muted small me-1" style="font-size:0.7rem;"><i class="fa fa-bolt me-1 text-warning"></i>Quick log:</span>
              <button type="button" class="quick-note-chip" data-text="Called applicant; line was busy. Follow-up scheduled.">+ Call Busy</button>
              <button type="button" class="quick-note-chip" data-text="SEAC committee requested presentation slide revisions.">+ Slide Revisions</button>
              <button type="button" class="quick-note-chip" data-text="Presentation meeting rescheduled to next agenda cycle.">+ Meeting Rescheduled</button>
              <button type="button" class="quick-note-chip" data-text="Client confirmed presentation attendance and draft slides.">+ Slides Confirmed</button>
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
            <button type="submit" class="btn btn-save-status shadow-sm" id="ppt_btn_submit_status">
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
  const modalPptIndexStatus = document.getElementById('modalPptIndexUpdateStatus');
  const formPptIndexStatus = document.getElementById('formPptIndexUpdateStatus');
  const inputStatus = document.getElementById('ppt_index_status_input');
  const textareaNotes = document.getElementById('ppt_index_status_notes');
  const liveBadge = document.getElementById('ppt_live_status_badge');
  const liveCategoryHint = document.getElementById('ppt_live_status_category_hint');
  const notesCounter = document.getElementById('ppt_notes_counter');
  const btnClear = document.getElementById('ppt_btn_clear_status');
  const btnSubmit = document.getElementById('ppt_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalPptIndexUpdateStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalPptIndexUpdateStatus .quick-note-chip');

  function renderLiveStatus(rawStatus) {
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Draft';
    
    liveBadge.textContent = formatted;

    if (['approved', 'completed', 'active'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      liveCategoryHint.textContent = 'Milestone: Approved';
    } 
    else if (['agenda_scheduled', 'presented'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      liveCategoryHint.textContent = 'Milestone: Active Stage';
    } 
    else if (['rejected', 'call not picked', 'client not responding'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      liveCategoryHint.textContent = 'Alert: Action Required';
    } 
    else if (['presentation review pending', 'rescheduled', 'payment pending'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      liveCategoryHint.textContent = 'Action: Pending Step';
    } 
    else if (['draft', 'archived'].includes(s)) {
      liveBadge.style.background = '#f1f5f9';
      liveBadge.style.color = '#475569';
      liveBadge.style.border = '1px solid #cbd5e1';
      liveCategoryHint.textContent = 'Milestone: Standard';
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

  if (modalPptIndexStatus) {
    modalPptIndexStatus.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        formPptIndexStatus.action = btn.dataset.action || '';
        document.getElementById('modal_ppt_code_text').textContent = btn.dataset.code || '--';
        document.getElementById('modal_ppt_client_text').textContent = btn.dataset.client || '--';
        inputStatus.value = btn.dataset.status || '';
        textareaNotes.value = btn.dataset.notes || '';
        renderLiveStatus(inputStatus.value);
        updateNotesCounter();
      }
    });

    modalPptIndexStatus.addEventListener('shown.bs.modal', function() {
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

  if (formPptIndexStatus) {
    formPptIndexStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        btnSubmit.click();
      }
    });

    formPptIndexStatus.addEventListener('submit', function() {
      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
    });
  }
});
</script>
@endsection
