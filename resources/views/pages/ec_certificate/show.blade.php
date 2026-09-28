@extends('layouts.app')
@section('title', $certificate->ec_ref_no . ' — Official Environmental Clearance Certificate')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<style>
.ec-certificate-view-paper {
  background: #ffffff;
  border: 2px solid #0F1E4D;
  border-radius: 12px;
  padding: 40px;
  position: relative;
  box-shadow: 0 4px 20px rgba(0,0,0,0.06);
  font-family: 'Times New Roman', Times, serif;
  color: #1e293b;
}
.ec-watermark {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-30deg);
  font-size: 3.5rem;
  font-weight: 900;
  color: rgba(15, 30, 77, 0.04);
  pointer-events: none;
  white-space: nowrap;
  letter-spacing: 6px;
}
@media print {
  body * { visibility: hidden; }
  .ec-certificate-view-paper, .ec-certificate-view-paper * { visibility: visible; }
  .ec-certificate-view-paper { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
}
</style>

<div class="content-body default-height">
  <div class="container-fluid">

    {{-- Breadcrumb & Title --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-md-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item"><a href="{{ route('ec-certificate.index') }}">EC Certificates</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $certificate->ec_ref_no }}</a></li>
        </ol>
      </div>
      <div class="col-md-6 text-end">
        <a href="{{ route('ec-certificate.index') }}" class="btn btn-outline-secondary btn-sm me-2">
          <i class="fa fa-arrow-left me-1"></i> Back to Register
        </a>
        <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalUpdateCertStatus">
          <i class="fa fa-tag me-1"></i> Update Status
        </button>
        <button type="button" class="btn btn-primary btn-sm me-2" onclick="window.print();">
          <i class="fa fa-print me-1"></i> Print Certificate
        </button>
        @if($certificate->certificate_file && file_exists(public_path($certificate->certificate_file)))
          <a href="{{ asset($certificate->certificate_file) }}" target="_blank" class="btn btn-success btn-sm">
            <i class="fa fa-download me-1"></i> Download PDF
          </a>
        @endif
        <a href="{{ route('ec-compliance.step', 1) }}" 
           class="btn btn-sm ms-2" 
           style="background: linear-gradient(135deg, #0F1E4D 0%, #10b981 100%); color: #fff; border: none;"
           title="Initiate Half-Yearly Compliance Report linked to this EC Certificate">
          <i class="fa fa-clipboard-check me-1"></i> Start Half-Yearly Compliance
        </a>
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

    <div class="row g-4">
      {{-- Main Certificate Document Card --}}
      <div class="col-lg-8">
        <div class="ec-certificate-view-paper">
          <div class="ec-watermark">GOVERNMENT OF TAMIL NADU</div>

          {{-- Header Letterhead --}}
          <div class="text-center pb-3 border-bottom mb-4">
            <h4 class="fw-bold mb-1" style="letter-spacing:1px; color:#0F1E4D;">STATE ENVIRONMENT IMPACT ASSESSMENT AUTHORITY</h4>
            <h6 class="text-muted mb-0">Government of Tamil Nadu &bull; 3rd Floor, Panagal Building, Saidapet, Chennai - 600 015</h6>
            <div class="badge bg-dark text-white mt-2 px-3 py-1 font-monospace" style="font-size:0.85rem;">ENVIRONMENTAL CLEARANCE (EC) ORDER</div>
          </div>

          {{-- Reference Metadata Grid --}}
          <div class="row g-2 mb-3 small">
            <div class="col-6">
              <strong>Order Letter No:</strong> {{ $certificate->ec_ref_no }}
            </div>
            <div class="col-6 text-end">
              <strong>Date of Issue:</strong> {{ $certificate->issue_date ? $certificate->issue_date->format('d F Y') : '—' }}
            </div>
            <div class="col-6">
              <strong>PARIVESH Proposal No:</strong> {{ $certificate->parivesh_app_no ?: '—' }}
            </div>
            <div class="col-6 text-end">
              <strong>Validity:</strong> {{ $certificate->validity_years }} Years (Expires: {{ $certificate->expiry_date ? $certificate->expiry_date->format('d F Y') : '—' }})
            </div>
          </div>

          {{-- Certificate Body --}}
          <div class="p-3 bg-light rounded mb-4" style="font-size:0.95rem; line-height:1.7;">
            <p class="mb-2"><strong>To:</strong><br>
              <strong>{{ $certificate->applicant_name }}</strong><br>
              @if($certificate->environmentProject?->location)
                {{ $certificate->environmentProject->location }}<br>
              @endif
              District: {{ $certificate->environmentProject?->district?->name ?? 'Tamil Nadu' }}.
            </p>
            <p class="mb-2">
              <strong>Subject:</strong> Grant of Environmental Clearance for the proposed <strong>{{ $certificate->environmentProject?->project_name ?? 'Mining Quarry Project' }}</strong> under Category {{ $certificate->environmentProject?->category_badge ?? 'B2' }} of EIA Notification 2006.
            </p>
            <p class="mb-0 text-muted" style="font-size:0.88rem;">
              {{ $certificate->conditions_summary ?: 'Environmental Clearance is granted subject to strict environmental management plan implementation, ground water safeguards, greenbelt maintenance, and continuous air quality monitoring.' }}
            </p>
          </div>

          {{-- Stamp and Signature --}}
          <div class="row align-items-end pt-4 mt-4 border-top">
            <div class="col-6">
              <div class="p-2 border rounded d-inline-block bg-white text-center" style="width:110px;">
                <i class="fa fa-qrcode fa-3x text-dark"></i>
                <div style="font-size:0.65rem;" class="mt-1 fw-bold">SEIAA VERIFIED</div>
              </div>
            </div>
            <div class="col-6 text-end">
              <div class="fw-bold fs-6">Member Secretary</div>
              <div class="small text-muted">SEIAA - Tamil Nadu</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Sidebar Project & Dossier Recap --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
          <div class="card-header bg-white border-bottom py-3">
            <h6 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
              <i class="fa fa-folder-tree me-1 text-primary"></i> Linked Project Dossier
            </h6>
          </div>
          <div class="card-body">
            @if($certificate->environmentProject)
              <div class="mb-3">
                <div class="small text-muted">Project Code</div>
                <div class="fw-bold fs-6" style="color:#0F1E4D;">{{ $certificate->environmentProject->project_code }}</div>
              </div>
              <div class="mb-3">
                <div class="small text-muted">Project Name</div>
                <div class="fw-semibold">{{ $certificate->environmentProject->project_name }}</div>
              </div>
              <div class="mb-3">
                <div class="small text-muted">Category</div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  {{ $certificate->environmentProject->category_badge }}
                </span>
                @if($certificate->environmentProject->sub_category_label)
                  <div class="text-muted small mt-1">{{ $certificate->environmentProject->sub_category_label }}</div>
                @endif
              </div>
              <div class="mb-3">
                <div class="small text-muted">Applicant / Entity</div>
                <div class="fw-semibold">{{ $certificate->applicant_name }}</div>
              </div>
              <div class="mb-3">
                <div class="small text-muted">Certificate Status</div>
                @php
                  $st = strtolower(trim($certificate->status));
                  $certStyle = match(true) {
                    in_array($st, ['active']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
                    in_array($st, ['call not picked', 'revoked', 'expired']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                    in_array($st, ['surrendered']) => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
                    default => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
                  };
                @endphp
                <span class="badge" style="{{ $certStyle }} font-size:0.82rem;">
                  {{ ucwords(str_replace('_', ' ', $certificate->status)) }}
                </span>
                @if($certificate->status_notes)
                  <div class="mt-2 text-muted small p-2 bg-light rounded border">
                    <i class="fa fa-comment-dots text-warning me-1"></i><strong>Note:</strong> {{ $certificate->status_notes }}
                  </div>
                @endif
              </div>
              <a href="{{ route('eviron.show', $certificate->environmentProject->id) }}" class="btn btn-navy btn-sm w-100 mt-2" style="background:#0F1E4D; color:#fff;">
                <i class="fa fa-external-link-alt me-1"></i> Open Project Folders
              </a>
            @else
              <div class="text-muted small">No direct environment project linked.</div>
            @endif
          </div>
        </div>

        {{-- Verification Status Card --}}
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
          <div class="card-header bg-white border-bottom py-3">
            <h6 class="card-title mb-0 fw-bold" style="color:#0F1E4D;">
              <i class="fa fa-shield-halved me-1 text-success"></i> Regulatory Authenticity
            </h6>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0 small text-muted">
              <li class="mb-2"><i class="fa fa-check text-success me-2"></i> SEIAA Tamil Nadu Approved Order</li>
              <li class="mb-2"><i class="fa fa-check text-success me-2"></i> PARIVESH Central Portal Logged</li>
              <li class="mb-2"><i class="fa fa-check text-success me-2"></i> Digital Seal &amp; QR Validation Active</li>
              <li class="mb-0"><i class="fa fa-check text-success me-2"></i> Statutory Safeguards Acknowledged</li>
            </ul>
          </div>
        </div>

        {{-- Workflow Promotion: Start EC Half-Yearly Compliance --}}
        <div class="card border-0 shadow-sm mt-4" style="border-radius:12px; border-left: 4px solid #10b981 !important;">
          <div class="card-body text-center py-4">
            <div class="mb-2"><i class="fa fa-clipboard-check fa-2x text-success"></i></div>
            <h6 class="fw-bold" style="color:#0F1E4D;">Next Step: EC Half-Yearly Compliance</h6>
            <p class="text-muted small mb-3">Submit statutory environmental monitoring report to SEIAA as mandated every 6 months during the EC validity period.</p>
            <a href="{{ route('ec-compliance.step', 1) }}" class="btn btn-success btn-sm px-4 shadow-sm">
              <i class="fa fa-arrow-right me-1"></i> Start Compliance Filing
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Dynamic Update EC Certificate Status Modal --}}
{{-- Dynamic Update EC Certificate Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalUpdateCertStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalUpdateCertStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalUpdateCertStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #10B981 50%, #059669 100%);
  }
  #modalUpdateCertStatus .status-icon-wrapper {
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
  #modalUpdateCertStatus .preset-status-btn {
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
  #modalUpdateCertStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalUpdateCertStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalUpdateCertStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalUpdateCertStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalUpdateCertStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalUpdateCertStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalUpdateCertStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalUpdateCertStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalUpdateCertStatus" tabindex="-1" aria-labelledby="modalUpdateCertStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formCertShowUpdateStatus" action="{{ route('ec-certificate.status', $certificate->id) }}">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-stamp"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalUpdateCertStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update EC Certificate Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i>{{ $certificate->certificate_no }}
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;">
                  <i class="fa fa-building me-1 opacity-50"></i>{{ $certificate->customer?->company_name ?: ($certificate->customer?->customer_name ?: 'Applicant') }}
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
              <span id="cert_show_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                {{ ucwords(str_replace('_', ' ', $certificate->status)) }}
              </span>
              <span class="text-muted small ms-auto" id="cert_show_live_status_category_hint" style="font-size:0.72rem;">Current Status</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="cert_show_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="cert_show_status_input" class="form-control border-start-0 ps-1"
                     list="cert_show_status_suggestions" value="{{ $certificate->status }}" placeholder="e.g. active, expired, dispatch pending..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="cert_show_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="cert_show_status_suggestions">
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
              <div class="d-flex flex-wrap gap-1" id="cert_show_group_workflow_presets">
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
              <div class="d-flex flex-wrap gap-1" id="cert_show_group_operational_presets">
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
              <label for="cert_show_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Handover Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="cert_show_notes_counter" style="font-size:0.72rem;">{{ strlen($certificate->status_notes ?? '') }} / 1000</span>
            </div>
            <textarea name="status_notes" id="cert_show_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter courier dispatch tracking, client signature handover, follow-up call notes..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;">{{ $certificate->status_notes }}</textarea>

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
            <button type="submit" class="btn btn-save-status shadow-sm" id="cert_show_btn_submit_status">
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
  const modalCertShowStatus = document.getElementById('modalUpdateCertStatus');
  const formCertShowStatus = document.getElementById('formCertShowUpdateStatus');
  const inputStatus = document.getElementById('cert_show_status_input');
  const textareaNotes = document.getElementById('cert_show_status_notes');
  const liveBadge = document.getElementById('cert_show_live_status_badge');
  const liveCategoryHint = document.getElementById('cert_show_live_status_category_hint');
  const notesCounter = document.getElementById('cert_show_notes_counter');
  const btnClear = document.getElementById('cert_show_btn_clear_status');
  const btnSubmit = document.getElementById('cert_show_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalUpdateCertStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalUpdateCertStatus .quick-note-chip');

  function renderCertShowLiveStatus(rawStatus) {
    if (!liveBadge) return;
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Active';
    
    liveBadge.textContent = formatted;

    if (['active', 'client collected', 'approved'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Active';
    } 
    else if (['renewal under process', 'dispatch pending'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: In Progress';
    } 
    else if (['expired', 'surrendered', 'revoked', 'call not picked'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Alert: Status Flagged';
    } 
    else if (['original copy pending'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Action: Pending Step';
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

  function updateCertShowNotesCounter() {
    if (textareaNotes && notesCounter) {
      notesCounter.textContent = textareaNotes.value.length + ' / 1000';
    }
  }

  if (inputStatus) {
    renderCertShowLiveStatus(inputStatus.value);
    inputStatus.addEventListener('input', function() {
      renderCertShowLiveStatus(this.value);
    });
  }

  if (modalCertShowStatus) {
    modalCertShowStatus.addEventListener('shown.bs.modal', function() {
      if (inputStatus) inputStatus.focus();
    });
  }

  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderCertShowLiveStatus('');
    });
  }

  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      if (inputStatus) {
        inputStatus.value = this.dataset.value;
        renderCertShowLiveStatus(this.dataset.value);
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
        updateCertShowNotesCounter();
        textareaNotes.focus();
      }
    });
  });

  if (textareaNotes) {
    textareaNotes.addEventListener('input', updateCertShowNotesCounter);
  }

  if (formCertShowStatus) {
    formCertShowStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (btnSubmit) btnSubmit.click();
      }
    });

    formCertShowStatus.addEventListener('submit', function() {
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
      }
    });
  }
});
</script>
@endsection
