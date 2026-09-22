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
        <button type="button" class="btn btn-primary btn-sm me-2" onclick="window.print();">
          <i class="fa fa-print me-1"></i> Print Certificate
        </button>
        @if($certificate->certificate_file && file_exists(public_path($certificate->certificate_file)))
          <a href="{{ asset($certificate->certificate_file) }}" target="_blank" class="btn btn-success btn-sm">
            <i class="fa fa-download me-1"></i> Download PDF
          </a>
        @endif
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
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                  {{ ucfirst($certificate->status) }}
                </span>
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
      </div>
    </div>

  </div>
</div>
@endsection
