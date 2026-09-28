@extends('layouts.app')
@section('title', 'PPT Application Dossier — ' . $ppt->application_no)
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <a href="{{ route('ppt-department.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Register
          </a>
          <span class="badge bg-primary fs-6">{{ $ppt->application_no }}</span>
          @php
            $st = strtolower(trim($ppt->status));
            $stStyle = match(true) {
              in_array($st, ['approved', 'presented']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
              in_array($st, ['agenda_scheduled'])     => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
              in_array($st, ['call not picked', 'client not responding', 'rejected']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
              in_array($st, ['archived'])             => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
              in_array($st, ['draft'])                => 'background:#f8fafc; color:#64748b; border:1px solid #cbd5e1;',
              default                                 => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
            };
          @endphp
          <span class="badge fs-6" style="{{ $stStyle }}">{{ ucwords(str_replace('_', ' ', $ppt->status)) }}</span>
        </div>
        <h3 class="fw-bold text-navy mb-0">{{ $ppt->project_name ?: 'SEAC / SEIAA Presentation Dossier' }}</h3>
        @if($ppt->status_notes)
          <div class="mt-2 text-muted small"><i class="fa fa-comment-dots text-warning me-1"></i><strong>Status Note:</strong> {{ $ppt->status_notes }}</div>
        @endif
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUpdatePptStatus">
          <i class="bi bi-tag me-1"></i> Update Status
        </button>
        <button onclick="window.print()" class="btn btn-outline-secondary">
          <i class="bi bi-printer me-1"></i> Print Dossier
        </button>
        <a href="{{ route('ppt-department.step', ['step' => 1, 'resume' => $ppt->id]) }}" class="btn btn-navy">
          <i class="bi bi-pencil-square me-1"></i> Edit Application
        </a>
      </div>
    </div>

    {{-- Linked Environment Clearance Project Banner --}}
    @if($ppt->environmentProject)
      <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; border-left: 5px solid #2563eb !important; background: #f8faff;">
        <div class="card-body p-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary">Linked Environment Clearance Project</span>
                <span class="badge {{ $ppt->presentation_stage === 'tor_presentation' ? 'bg-info text-white' : 'bg-success text-white' }}">
                  {{ $ppt->presentation_stage === 'tor_presentation' ? 'Stage 1: ToR Presentation Gate' : 'Stage 2: Final EC Presentation Gate' }}
                </span>
                <span class="fw-bold text-navy">{{ $ppt->environmentProject->project_code }}</span>
              </div>
              <div class="small text-muted">
                <strong>Project:</strong> {{ $ppt->environmentProject->project_name }}
                &bull; <strong>Current EC Sub Category:</strong> {{ $ppt->environmentProject->sub_category }}
                &bull; <strong>B1 Stage:</strong> {{ strtoupper(str_replace('_', ' ', $ppt->environmentProject->b1_stage)) }}
              </div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('eviron.show', $ppt->environmentProject->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-folder-open me-1"></i> View EC Project Dossier
              </a>

              @if($ppt->status !== 'approved')
                <form method="POST" action="{{ route('ppt-department.approveStage', $ppt->id) }}" onsubmit="return confirm('Approve this presentation and advance the linked Environment Clearance project?');">
                  @csrf
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-check-circle me-1"></i>
                    @if($ppt->presentation_stage === 'tor_presentation')
                      Approve ToR &amp; Unlock ETA Folders
                    @else
                      Approve Final EC &amp; Complete Project
                    @endif
                  </button>
                </form>
              @else
                <span class="badge bg-success py-2 px-3">
                  <i class="fa fa-check-circle me-1"></i> Presentation Approved
                </span>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

    {{-- 4 Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0F1E4D !important;">
          <small class="text-muted text-uppercase fw-semibold">Client / Company</small>
          <h5 class="fw-bold text-navy mt-1 mb-1">{{ $ppt->customer?->company_name ?: ($ppt->customer?->customer_name ?: 'N/A') }}</h5>
          <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ppt->customer?->customer_name ?: '—' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0284c7 !important;">
          <small class="text-muted text-uppercase fw-semibold">District &amp; Mineral</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">{{ $ppt->district?->name ?: 'N/A' }}</h5>
          <small class="text-muted"><i class="bi bi-gem me-1"></i>{{ $ppt->mineral?->name ?: 'Minor Mineral' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #10b981 !important;">
          <small class="text-muted text-uppercase fw-semibold">Statutory Folders</small>
          <h5 class="fw-bold text-success mt-1 mb-1">{{ $ppt->documents->count() }} / 11 Folders</h5>
          <small class="text-muted">Parivesh / SEAC Checklist</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #f59e0b !important;">
          <small class="text-muted text-uppercase fw-semibold">Financial Ledger</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">₹{{ number_format($ppt->product_value, 2) }}</h5>
          <small class="{{ $ppt->pending_amount > 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
            {{ $ppt->pending_amount > 0 ? '₹' . number_format($ppt->pending_amount, 2) . ' Pending' : 'Fully Settled' }}
          </small>
        </div>
      </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">
      {{-- 11 Statutory Folders Section --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-folder-open text-primary me-2"></i> 11 Statutory Presentation Folders
            </h5>
            <span class="badge bg-light text-navy border">{{ $ppt->documents->count() }} Attached Files</span>
          </div>
          <div class="card-body p-0">
            @php
              $foldersList = [
                1 => ['Documents', 'PARIVESH online uploading documents', 'fa-file-alt'],
                2 => ['EDS & EDS Reply', 'EDS and EDS reply communications', 'fa-comments'],
                3 => ['Demand Note', 'SPCB payment receipt and comments', 'fa-file-invoice-dollar'],
                4 => ['File No', 'Schedule, PPT, Radius KML, NOC and CER', 'fa-folder-open'],
                5 => ['SEAC Agenda', 'SEAC committee agenda documents', 'fa-users'],
                6 => ['SEAC Minutes', 'ADS and ADS reply if any', 'fa-clipboard-list'],
                7 => ['ADS', 'ADS reply documents and PPT presentation', 'fa-file-alt'],
                8 => ['CER Affidavit', 'Corporate Environment Responsibility affidavit', 'fa-balance-scale'],
                9 => ['SEIAA Agenda', 'State Environmental Impact Assessment Agenda', 'fa-users'],
                10 => ['SEIAA Minutes', 'Committee minutes and appraisal decision', 'fa-clipboard-check'],
                11 => ['Environmental Clearance', 'Final EC Granted Order', 'fa-leaf']
              ];
            @endphp

            <div class="list-group list-group-flush">
              @foreach($foldersList as $fId => [$fName, $fDesc, $fIcon])
                @php
                  $folderDocs = $ppt->documents->where('folder_id', $fId);
                @endphp
                <div class="list-group-item p-3">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width:32px; height:32px; background:#eff6ff;">
                        <i class="fa {{ $fIcon }} small"></i>
                      </div>
                      <div>
                        <span class="fw-bold text-navy">{{ $fId }}. {{ $fName }}</span>
                        <div class="text-muted small">{{ $fDesc }}</div>
                      </div>
                    </div>
                    <div>
                      @if($folderDocs->isNotEmpty())
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                          <i class="bi bi-check-circle me-1"></i>{{ $folderDocs->count() }} Attached
                        </span>
                      @else
                        <span class="badge bg-light text-muted border">Pending Upload</span>
                      @endif
                    </div>
                  </div>

                  @if($folderDocs->isNotEmpty())
                    <div class="bg-light p-2 rounded-2 mt-2">
                      @foreach($folderDocs as $doc)
                        <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                          <div class="d-flex align-items-center gap-2 text-truncate">
                            <i class="bi bi-paperclip text-muted"></i>
                            <span class="small fw-semibold text-truncate">{{ $doc->document_name }}</span>
                            @if($doc->file_size)
                              <span class="badge bg-white text-muted border py-0" style="font-size:10px;">{{ round($doc->file_size / 1024) }} KB</span>
                            @endif
                          </div>
                          <div>
                            @if($doc->file_path)
                              <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size:11px;">
                                <i class="bi bi-eye"></i> View
                              </a>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Sidebar Cards: Handlers & Payments --}}
      <div class="col-lg-4">
        {{-- Technical Handling Team --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-people-fill text-primary me-2"></i> Project Handling Team
            </h6>
            <span class="badge bg-light text-dark border">{{ $ppt->handlers->count() }} Persons</span>
          </div>
          <div class="card-body p-3">
            @forelse($ppt->handlers as $handler)
              <div class="d-flex align-items-start justify-content-between py-2 border-bottom">
                <div>
                  <div class="fw-bold text-dark">{{ $handler->name }}</div>
                  <div class="badge bg-light text-primary border" style="font-size:11px;">{{ $handler->role }}</div>
                  @if($handler->notes)
                    <div class="small text-muted mt-1">{{ $handler->notes }}</div>
                  @endif
                </div>
              </div>
            @empty
              <p class="text-muted small mb-0">No technical handling personnel assigned.</p>
            @endforelse
          </div>
        </div>

        {{-- Financial & Billing Ledger --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-receipt text-success me-2"></i> Financial &amp; Billing Ledger
            </h6>
            @php
              $pBadge = match($ppt->payment_status) {
                'paid'    => 'bg-success',
                'partial' => 'bg-warning text-dark',
                default   => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
            @endphp
            <span class="badge {{ $pBadge }}">{{ ucfirst($ppt->payment_status ?: 'pending') }}</span>
          </div>
          <div class="card-body p-3">
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Quoted Service Fee:</span>
              <strong class="text-navy">₹{{ number_format($ppt->product_value, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Paid Amount:</span>
              <strong class="text-success">₹{{ number_format($ppt->paid_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Pending Balance:</span>
              <strong class="text-danger">₹{{ number_format($ppt->pending_amount, 2) }}</strong>
            </div>
            @if($ppt->payments->isNotEmpty() && $ppt->payments->first()->notes)
              <div class="mt-3 p-2 bg-light rounded small">
                <span class="text-muted fw-semibold">Payment Notes / Ref:</span>
                <div class="text-dark">{{ $ppt->payments->first()->notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Dynamic Update PPT Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalUpdatePptStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalUpdatePptStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalUpdatePptStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #3B82F6 50%, #8B5CF6 100%);
  }
  #modalUpdatePptStatus .status-icon-wrapper {
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
  #modalUpdatePptStatus .preset-status-btn {
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
  #modalUpdatePptStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalUpdatePptStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalUpdatePptStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalUpdatePptStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalUpdatePptStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalUpdatePptStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalUpdatePptStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalUpdatePptStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalUpdatePptStatus" tabindex="-1" aria-labelledby="modalUpdatePptStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formPptShowUpdateStatus" action="{{ route('ppt-department.status', $ppt->id) }}">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-person-chalkboard"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalUpdatePptStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update PPT Application Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i>{{ $ppt->application_no }}
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;">
                  <i class="fa fa-building me-1 opacity-50"></i>{{ $ppt->customer?->company_name ?: ($ppt->customer?->customer_name ?: 'Applicant') }}
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
              <span class="text-muted" style="font-size:0.7rem;">Real-time dossier appearance</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span id="ppt_show_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                {{ ucwords(str_replace('_', ' ', $ppt->status)) }}
              </span>
              <span class="text-muted small ms-auto" id="ppt_show_live_status_category_hint" style="font-size:0.72rem;">Current Status</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="ppt_show_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="ppt_show_status_input" class="form-control border-start-0 ps-1"
                     list="ppt_show_status_suggestions" value="{{ $ppt->status }}" placeholder="e.g. presented, agenda_scheduled, call not picked..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="ppt_show_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="ppt_show_status_suggestions">
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
              <div class="d-flex flex-wrap gap-1" id="ppt_show_group_workflow_presets">
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
              <div class="d-flex flex-wrap gap-1" id="ppt_show_group_operational_presets">
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
              <label for="ppt_show_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Follow-up Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="ppt_show_notes_counter" style="font-size:0.72rem;">{{ strlen($ppt->status_notes ?? '') }} / 1000</span>
            </div>
            <textarea name="status_notes" id="ppt_show_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter follow-up remarks, meeting outcomes, presentation revisions, client call details..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;">{{ $ppt->status_notes }}</textarea>

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
            <button type="submit" class="btn btn-save-status shadow-sm" id="ppt_show_btn_submit_status">
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
  const modalPptShowStatus = document.getElementById('modalUpdatePptStatus');
  const formPptShowStatus = document.getElementById('formPptShowUpdateStatus');
  const inputStatus = document.getElementById('ppt_show_status_input');
  const textareaNotes = document.getElementById('ppt_show_status_notes');
  const liveBadge = document.getElementById('ppt_show_live_status_badge');
  const liveCategoryHint = document.getElementById('ppt_show_live_status_category_hint');
  const notesCounter = document.getElementById('ppt_show_notes_counter');
  const btnClear = document.getElementById('ppt_show_btn_clear_status');
  const btnSubmit = document.getElementById('ppt_show_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalUpdatePptStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalUpdatePptStatus .quick-note-chip');

  function renderPptShowLiveStatus(rawStatus) {
    if (!liveBadge) return;
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Draft';
    
    liveBadge.textContent = formatted;

    if (['approved', 'completed', 'active'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Approved';
    } 
    else if (['agenda_scheduled', 'presented'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Active Stage';
    } 
    else if (['rejected', 'call not picked', 'client not responding'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Alert: Action Required';
    } 
    else if (['presentation review pending', 'rescheduled', 'payment pending'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Action: Pending Step';
    } 
    else if (['draft', 'archived'].includes(s)) {
      liveBadge.style.background = '#f1f5f9';
      liveBadge.style.color = '#475569';
      liveBadge.style.border = '1px solid #cbd5e1';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Standard';
    } 
    else {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Custom Status';
    }

    presetButtons.forEach(btn => {
      if (btn.dataset.value.toLowerCase() === s) {
        btn.classList.add('active-preset');
      } else {
        btn.classList.remove('active-preset');
      }
    });
  }

  function updatePptShowNotesCounter() {
    if (textareaNotes && notesCounter) {
      notesCounter.textContent = textareaNotes.value.length + ' / 1000';
    }
  }

  if (inputStatus) {
    renderPptShowLiveStatus(inputStatus.value);
    inputStatus.addEventListener('input', function() {
      renderPptShowLiveStatus(this.value);
    });
  }

  if (modalPptShowStatus) {
    modalPptShowStatus.addEventListener('shown.bs.modal', function() {
      if (inputStatus) inputStatus.focus();
    });
  }

  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderPptShowLiveStatus('');
    });
  }

  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      if (inputStatus) {
        inputStatus.value = this.dataset.value;
        renderPptShowLiveStatus(this.dataset.value);
        inputStatus.focus();
      }
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
        updatePptShowNotesCounter();
        textareaNotes.focus();
      }
    });
  });

  if (textareaNotes) {
    textareaNotes.addEventListener('input', updatePptShowNotesCounter);
  }

  if (formPptShowStatus) {
    formPptShowStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (btnSubmit) btnSubmit.click();
      }
    });

    formPptShowStatus.addEventListener('submit', function() {
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
      }
    });
  }
});
</script>
@endsection
