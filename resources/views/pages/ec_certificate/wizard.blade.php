@extends('layouts.app')
@section('title', 'EC Certificate Issuance — Step ' . $step . ' of 6')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<style>
.ec-wizard-header {
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 2px 4px rgba(15, 30, 77, 0.04);
}
.ec-stepper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  margin: 1.5rem 0 2rem 0;
}
.ec-stepper::before {
  content: '';
  position: absolute;
  top: 18px;
  left: 30px;
  right: 30px;
  height: 2px;
  background: #e2e8f0;
  z-index: 1;
}
.ec-step-item {
  position: relative;
  z-index: 2;
  text-align: center;
  flex: 1;
}
.ec-step-circle {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: #ffffff;
  border: 2px solid #cbd5e1;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 0.9rem;
  transition: all 0.2s ease;
}
.ec-step-item.active .ec-step-circle {
  background: #0F1E4D;
  border-color: #0F1E4D;
  color: #ffffff;
  box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
}
.ec-step-item.done .ec-step-circle {
  background: #059669;
  border-color: #059669;
  color: #ffffff;
}
.ec-step-label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #64748b;
  margin-top: 6px;
  display: block;
}
.ec-step-item.active .ec-step-label {
  color: #0F1E4D;
  font-weight: 700;
}
.ec-step-item.done .ec-step-label {
  color: #059669;
}
.ec-certificate-preview-paper {
  background: #fff;
  border: 2px solid #cbd5e1;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  padding: 2.5rem;
  border-radius: 8px;
  position: relative;
  font-family: 'Times New Roman', serif;
  color: #1a202c;
}
.ec-watermark {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-30deg);
  font-size: 5rem;
  font-weight: 900;
  color: rgba(15, 30, 77, 0.04);
  pointer-events: none;
  text-transform: uppercase;
  letter-spacing: 10px;
  white-space: nowrap;
}
@media print {
  body * { visibility: hidden; }
  .ec-certificate-preview-paper, .ec-certificate-preview-paper * { visibility: visible; }
  .ec-certificate-preview-paper { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
}
</style>

@php
  $labels = [
    1 => 'Parivesh Details',
    2 => 'Upload EC PDF',
    3 => 'View / Print',
    4 => 'Store Documents',
    5 => 'Communicate',
    6 => 'Preview & Issue',
  ];
  $s1 = $draft['step1'] ?? [];
  $s2 = $draft['step2'] ?? [];
  $s5 = $draft['step5'] ?? [];
@endphp

<div class="content-body default-height">
  <div class="container-fluid">

    {{-- Breadcrumb & Top Bar --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-md-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('ec-certificate.index') }}">EC Certificates</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">Issuance Wizard (Step {{ $step }}/6)</a></li>
        </ol>
      </div>
      <div class="col-md-6 text-end">
        <a href="{{ route('ec-certificate.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="fa fa-arrow-left me-1"></i> Back to Certificate Register
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
    @if(isset($errors) && $errors->any())
      <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
        <ul class="mb-0">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- ================= 6-STEP PROGRESS BAR ================= --}}
    @php
      $completedSteps = [
        1 => !empty($draft['step1']['completed']),
        2 => !empty($draft['step2']),
        3 => !empty($draft['step3']['preview_verified']),
        4 => !empty($draft['step4']['storage_confirmed']),
        5 => !empty($draft['step5']['recipient_email']),
        6 => false,
      ];
      $maxAllowed = $maxUnlockedStep ?? 1;
    @endphp
    <div class="ec-wizard-header p-3 mb-4">
      <div class="ec-stepper">
        @foreach($labels as $num => $lbl)
          @php
            $isDone = !empty($completedSteps[$num]);
            $isActive = $num === $step;
            $isNavigable = $isActive || $isDone || ($num <= $maxAllowed);
          @endphp
          <div class="ec-step-item {{ $isActive ? 'active' : ($isDone ? 'done' : '') }}">
            @if($isNavigable)
              <a href="{{ route('ec-certificate.step', $num) }}" class="text-decoration-none">
                <div class="ec-step-circle">
                  @if($isDone && !$isActive) <i class="fa fa-check"></i> @else {{ $num }} @endif
                </div>
                <span class="ec-step-label">{{ $lbl }}</span>
              </a>
            @else
              <div class="text-decoration-none text-muted" style="cursor:not-allowed; opacity:0.65;">
                <div class="ec-step-circle">
                  {{ $num }}
                </div>
                <span class="ec-step-label">{{ $lbl }}</span>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>

    {{-- ================= MAIN WIZARD CARD ================= --}}
    <div class="card border-0 shadow-sm mb-5">
      <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
          <span class="badge bg-navy text-white px-2 py-1 mb-1" style="background:#0F1E4D;">Step {{ $step }} of 6</span>
          <h5 class="card-title mb-0 fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
            @if($step === 1) 1. Parivesh Portal Approval Details
            @elseif($step === 2) 2. Download &amp; Upload EC Certificate PDF
            @elseif($step === 3) 3. View &amp; Print Official EC Certificate
            @elseif($step === 4) 4. Store Documents in Project Repository
            @elseif($step === 5) 5. Communicate EC Grant / Decision to Applicant
            @else 6. Verification Summary &amp; Official Issuance
            @endif
          </h5>
        </div>
        @if($selectedProject)
          <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:0.85rem;">
            Project: {{ $selectedProject->project_code }} ({{ $selectedProject->category_badge }})
          </span>
        @endif
      </div>

      <div class="card-body p-4">

        {{-- ========================================================= --}}
        {{-- STEP 1: PARIVESH APPROVAL DETAILS                         --}}
        {{-- ========================================================= --}}
        @if($step === 1)
        <form method="POST" action="{{ route('ec-certificate.saveStep', 1) }}">
          @csrf

          {{-- Quick Customer & Project Select --}}
          <div class="row g-3 mb-4">
            <div class="col-md-12">
              <div class="p-3 rounded" style="background:#f0f7ff; border:1px solid #bfdbfe;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <label class="form-label fw-bold text-primary mb-0">
                    <i class="fa fa-folder-open me-1"></i> Select Approved Environment Project *
                  </label>
                  <span class="badge bg-primary text-white">Dynamic Binding</span>
                </div>
                <select class="form-select border-primary" name="environment_project_id" id="ec_project_select" required onchange="window.location.href='{{ route('ec-certificate.step', 1) }}?project_id=' + this.value;">
                  <option value="">-- Choose Project --</option>
                  @foreach($approvedProjects as $p)
                    <option value="{{ $p->id }}"
                      data-client="{{ $p->customer?->company_name ?: ($p->customer?->customer_name ?: $p->contact_name) }}"
                      data-code="{{ $p->project_code }}"
                      data-district="{{ $p->district?->name }}"
                      data-mimas="{{ $p->customer?->mimas_no }}"
                      @selected($selectedProject && $selectedProject->id === $p->id)>
                      {{ $p->project_code }} — {{ $p->project_name }} ({{ $p->customer?->company_name ?: $p->customer?->customer_name }}) [{{ $p->category_badge }}]
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="row g-3">
            {{-- EC Reference Number --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">EC Certificate Reference No. *</label>
              <input type="text" class="form-control" name="ec_ref_no" required
                value="{{ old('ec_ref_no', $s1['ec_ref_no'] ?? '') }}"
                placeholder="e.g. SEIAA-TN/EC/2026/0001">
              <small class="text-muted">Official State Environmental Clearance identifier</small>
            </div>

            {{-- Parivesh Application No --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Parivesh Portal Application Reference *</label>
              <input type="text" class="form-control" name="parivesh_app_no" required
                value="{{ old('parivesh_app_no', $s1['parivesh_app_no'] ?? '') }}"
                placeholder="e.g. SIA/TN/MIN/10001/2026">
              <small class="text-muted">MoEFCC / Parivesh national tracking number</small>
            </div>

            {{-- Applicant Name --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Applicant / Company Name *</label>
              <input type="text" class="form-control" name="applicant_name" id="field_applicant_name" required
                value="{{ old('applicant_name', $s1['applicant_name'] ?? '') }}"
                placeholder="Full applicant or company legal name">
            </div>

            {{-- Approval / Issue Date --}}
            <div class="col-md-3">
              <label class="form-label fw-semibold">Date of Issuance *</label>
              <input type="date" class="form-control" name="issue_date" required
                value="{{ old('issue_date', $s1['issue_date'] ?? date('Y-m-d')) }}">
            </div>

            {{-- Validity Years --}}
            <div class="col-md-3">
              <label class="form-label fw-semibold">Validity Period (Years) *</label>
              <input type="number" class="form-control" name="validity_years" min="1" max="30" required
                value="{{ old('validity_years', $s1['validity_years'] ?? 5) }}">
            </div>

            {{-- Communication Type --}}
            <div class="col-md-6">
              <label class="form-label fw-semibold">Clearance Classification / Decision *</label>
              <select class="form-select" name="communication_type" required>
                <option value="Grant" @selected(($s1['communication_type'] ?? 'Grant') === 'Grant')>EC Grant Letter (Full Clearance)</option>
                <option value="ToR" @selected(($s1['communication_type'] ?? '') === 'ToR')>Terms of Reference (ToR Letter)</option>
                <option value="Rejection" @selected(($s1['communication_type'] ?? '') === 'Rejection')>Rejection Letter</option>
              </select>
            </div>

            {{-- Conditions Summary --}}
            <div class="col-md-12">
              <label class="form-label fw-semibold">Environmental Conditions &amp; Safeguards Summary</label>
              <textarea class="form-control" name="conditions_summary" rows="3" placeholder="Enter standard or specific environmental mitigation safeguards...">{{ old('conditions_summary', $s1['conditions_summary'] ?? '') }}</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            <button type="submit" class="btn btn-navy px-4" style="background:#0F1E4D; color:#fff;">
              Save &amp; Continue to Upload (Step 2) <i class="fa fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        {{-- ========================================================= --}}
        {{-- STEP 2: DOWNLOAD / UPLOAD EC CERTIFICATE                  --}}
        {{-- ========================================================= --}}
        @elseif($step === 2)
        <form method="POST" action="{{ route('ec-certificate.saveStep', 2) }}" enctype="multipart/form-data">
          @csrf

          <div class="row g-4">
            {{-- Parivesh Reference Guidance Card --}}
            <div class="col-lg-5">
              <div class="card h-100 border p-3 bg-light">
                <h6 class="fw-bold text-navy mb-2"><i class="fa fa-info-circle me-1 text-primary"></i> Parivesh Verification Check</h6>
                <p class="small text-muted mb-3">Ensure the uploaded Environmental Clearance copy contains the official SEIAA digital seal and QR validation code.</p>
                <ul class="list-unstyled small mb-3">
                  <li class="mb-2"><i class="fa fa-check text-success me-2"></i> <strong>Parivesh Ref:</strong> {{ $s1['parivesh_app_no'] ?? 'SIA/TN/MIN/...' }}</li>
                  <li class="mb-2"><i class="fa fa-check text-success me-2"></i> <strong>EC Ref:</strong> {{ $s1['ec_ref_no'] ?? 'SEIAA-TN/EC/...' }}</li>
                  <li class="mb-2"><i class="fa fa-check text-success me-2"></i> <strong>Applicant:</strong> {{ $s1['applicant_name'] ?? 'Applicant' }}</li>
                  <li class="mb-2"><i class="fa fa-check text-success me-2"></i> <strong>Validity:</strong> {{ $s1['validity_years'] ?? 5 }} Years from issue</li>
                </ul>
                <div class="alert alert-info py-2 small mb-0">
                  <i class="fa fa-lightbulb me-1"></i> If the signed copy is already on disk, it will be automatically archived into the project repository in Step 4.
                </div>
              </div>
            </div>

            {{-- File Upload Dropzone --}}
            <div class="col-lg-7">
              <div class="p-4 border rounded text-center" style="background:#f8fafc; border-style:dashed !important; border-width:2px !important; border-color:#cbd5e1 !important;">
                <i class="fa fa-cloud-arrow-up fa-3x text-primary mb-3"></i>
                <h5 class="fw-bold mb-1">Upload Environmental Clearance Certificate (PDF)</h5>
                <p class="text-muted small mb-3">Drag &amp; drop or click to upload the official signed certificate (PDF, max 25MB)</p>
                <input type="file" name="certificate_file" class="form-control w-75 mx-auto" accept=".pdf,.doc,.docx,.jpg,.png">

                @if(!empty($s2['file_name']))
                  <div class="mt-3 p-3 bg-white border rounded text-start d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                      <i class="fa fa-file-pdf text-danger fa-2x"></i>
                      <div>
                        <div class="fw-bold small">{{ $s2['file_name'] }}</div>
                        <div class="text-muted" style="font-size:0.75rem;">
                          Size: {{ number_format(($s2['file_size'] ?? 102400) / 1024, 1) }} KB &bull; Verified &bull; {{ $s2['uploaded_at'] ?? now()->format('d M Y') }}
                        </div>
                      </div>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                      <i class="fa fa-check me-1"></i> Attached
                    </span>
                  </div>
                @endif
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.step', 1) }}" class="btn btn-outline-secondary px-4">
              <i class="fa fa-arrow-left me-1"></i> Back to Step 1
            </a>
            <button type="submit" class="btn btn-navy px-4" style="background:#0F1E4D; color:#fff;">
              Save &amp; Continue to View/Print (Step 3) <i class="fa fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        {{-- ========================================================= --}}
        {{-- STEP 3: VIEW & PRINT OFFICIAL EC CERTIFICATE              --}}
        {{-- ========================================================= --}}
        @elseif($step === 3)
        <form method="POST" action="{{ route('ec-certificate.saveStep', 3) }}">
          @csrf

          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="small text-muted"><i class="fa fa-certificate text-warning me-1"></i> Official Government Document Preview</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="window.print();">
              <i class="fa fa-print me-1"></i> Print / Save as PDF
            </button>
          </div>

          {{-- Official Certificate Paper Document --}}
          <div class="ec-certificate-preview-paper mb-4">
            <div class="ec-watermark">GOVERNMENT OF TAMIL NADU</div>

            <div class="text-center pb-3 border-bottom mb-4">
              <h4 class="fw-bold mb-1" style="letter-spacing:1px;">STATE ENVIRONMENT IMPACT ASSESSMENT AUTHORITY</h4>
              <h6 class="text-muted mb-0">Government of Tamil Nadu &bull; Panagal Building, Saidapet, Chennai - 600 015</h6>
              <div class="badge bg-dark text-white mt-2 px-3 py-1">ENVIRONMENTAL CLEARANCE (EC)</div>
            </div>

            <div class="row g-2 mb-3 small">
              <div class="col-6">
                <strong>Letter No:</strong> {{ $s1['ec_ref_no'] ?? 'SEIAA-TN/EC/2026/0001' }}
              </div>
              <div class="col-6 text-end">
                <strong>Date:</strong> {{ date('d F Y', strtotime($s1['issue_date'] ?? date('Y-m-d'))) }}
              </div>
              <div class="col-6">
                <strong>Parivesh Proposal No:</strong> {{ $s1['parivesh_app_no'] ?? 'SIA/TN/MIN/10001/2026' }}
              </div>
              <div class="col-6 text-end">
                <strong>Validity:</strong> {{ $s1['validity_years'] ?? 5 }} Years (Exp: {{ date('d F Y', strtotime('+'.($s1['validity_years'] ?? 5).' years')) }})
              </div>
            </div>

            <div class="p-3 bg-light rounded mb-4" style="font-size:0.92rem; line-height:1.6;">
              <p class="mb-2"><strong>To:</strong><br>
                M/s {{ $s1['applicant_name'] ?? 'Authorized Applicant' }}<br>
                {{ $selectedProject?->location ?? 'Quarry Concession Site' }}, District: {{ $selectedProject?->district?->name ?? 'Tamil Nadu' }}.
              </p>
              <p class="mb-2">
                <strong>Subject:</strong> Grant of Environmental Clearance for the proposed {{ $selectedProject?->project_name ?? 'Mining Quarry Project' }} under Category {{ $selectedProject?->category_badge ?? 'B2' }} of EIA Notification 2006.
              </p>
              <p class="mb-0 text-muted" style="font-size:0.85rem;">
                {{ $s1['conditions_summary'] ?? 'Clearance is granted subject to strict environmental management plan implementation, ground water safeguards, and continuous air quality monitoring.' }}
              </p>
            </div>

            <div class="row align-items-end pt-4 mt-4 border-top">
              <div class="col-6">
                <div class="p-2 border rounded d-inline-block bg-white text-center" style="width:100px;">
                  <i class="fa fa-qrcode fa-3x text-dark"></i>
                  <div style="font-size:0.65rem;" class="mt-1">SEIAA DIGITAL VERIFIED</div>
                </div>
              </div>
              <div class="col-6 text-end">
                <div class="fw-bold">Member Secretary</div>
                <div class="small text-muted">SEIAA - Tamil Nadu</div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.step', 2) }}" class="btn btn-outline-secondary px-4">
              <i class="fa fa-arrow-left me-1"></i> Back to Step 2
            </a>
            <button type="submit" class="btn btn-navy px-4" style="background:#0F1E4D; color:#fff;">
              Continue to Store Documents (Step 4) <i class="fa fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        {{-- ========================================================= --}}
        {{-- STEP 4: STORE DOCUMENTS IN PROJECT REPOSITORY             --}}
        {{-- ========================================================= --}}
        @elseif($step === 4)
        <form method="POST" action="{{ route('ec-certificate.saveStep', 4) }}">
          @csrf

          <div class="alert alert-info py-2 mb-4">
            <i class="fa fa-check-circle me-1"></i> The Environmental Clearance certificate and its associated technical documents will be stored in the following secure digital folders:
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <div class="card p-3 border h-100" style="border-left: 4px solid #0F1E4D !important;">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="rounded-circle p-2 bg-light text-navy"><i class="fa fa-certificate fa-2x" style="color:#0F1E4D;"></i></div>
                  <div>
                    <h6 class="fw-bold mb-0">Folder: EC Certificate &amp; Statutory Grants</h6>
                    <small class="text-muted">Target Path: public/uploads/ec_certificates/{{ $selectedProject?->project_code }}/</small>
                  </div>
                </div>
                <div class="d-flex justify-content-between small text-muted mt-2 pt-2 border-top">
                  <span>Status: <strong class="text-success">Allocated &amp; Ready</strong></span>
                  <span>Access: <strong>Restricted Admin</strong></span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card p-3 border h-100" style="border-left: 4px solid #059669 !important;">
                <div class="d-flex align-items-center gap-3 mb-2">
                  <div class="rounded-circle p-2 bg-light text-success"><i class="fa fa-folder-tree fa-2x text-success"></i></div>
                  <div>
                    <h6 class="fw-bold mb-0">Folder: Final EIA &amp; Environmental Reports</h6>
                    <small class="text-muted">Linked to Project Code: {{ $selectedProject?->project_code }}</small>
                  </div>
                </div>
                <div class="d-flex justify-content-between small text-muted mt-2 pt-2 border-top">
                  <span>Status: <strong class="text-success">Synchronized</strong></span>
                  <span>Module: <strong>Environment Clearance</strong></span>
                </div>
              </div>
            </div>
          </div>

          <div class="form-check p-3 bg-light rounded border mb-4">
            <input class="form-check-input ms-0 me-2" type="checkbox" checked id="chk_auto_archive" required>
            <label class="form-check-label fw-semibold" for="chk_auto_archive">
              Confirm automatic linkage of this certificate to the project audit trail and client document dossier.
            </label>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.step', 3) }}" class="btn btn-outline-secondary px-4">
              <i class="fa fa-arrow-left me-1"></i> Back to Step 3
            </a>
            <button type="submit" class="btn btn-navy px-4" style="background:#0F1E4D; color:#fff;">
              Continue to Communication (Step 5) <i class="fa fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        {{-- ========================================================= --}}
        {{-- STEP 5: COMMUNICATE TO APPLICANT                          --}}
        {{-- ========================================================= --}}
        @elseif($step === 5)
        <form method="POST" action="{{ route('ec-certificate.saveStep', 5) }}">
          @csrf

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Communication Type *</label>
              <select class="form-select" name="communication_type" required>
                <option value="Grant" @selected(($s1['communication_type'] ?? 'Grant') === 'Grant')>Official EC Grant Letter</option>
                <option value="ToR" @selected(($s1['communication_type'] ?? '') === 'ToR')>Terms of Reference (ToR) Communication</option>
                <option value="Rejection" @selected(($s1['communication_type'] ?? '') === 'Rejection')>Rejection / Non-Approval Notice</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Applicant Contact Email *</label>
              <input type="email" class="form-control" name="recipient_email" required
                value="{{ old('recipient_email', $s5['recipient_email'] ?? ($selectedProject?->customer?->email ?: 'applicant@example.com')) }}">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Mobile (SMS &amp; WhatsApp Alerts)</label>
              <input type="text" class="form-control" name="recipient_phone"
                value="{{ old('recipient_phone', $s5['recipient_phone'] ?? ($selectedProject?->customer?->mobile_num ?: $selectedProject?->contact_phone)) }}" placeholder="10-digit mobile number">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Delivery Options</label>
              <div class="pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" name="send_sms_alert" value="1" id="chk_sms" checked>
                  <label class="form-check-label" for="chk_sms">SMS / WhatsApp Alert</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" checked id="chk_email" disabled>
                  <label class="form-check-label" for="chk_email">Email Attachment</label>
                </div>
              </div>
            </div>

            <div class="col-md-12">
              <label class="form-label fw-semibold">Dispatch Note &amp; Remarks</label>
              <textarea class="form-control" name="communication_note" rows="4">{{ old('communication_note', $s5['communication_note'] ?? 'Dear Applicant, Your Environmental Clearance for '.$selectedProject?->project_name.' has been formally granted and approved by SEIAA. The signed certificate has been deposited into your compliance dossier.') }}</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.step', 4) }}" class="btn btn-outline-secondary px-4">
              <i class="fa fa-arrow-left me-1"></i> Back to Step 4
            </a>
            <button type="submit" class="btn btn-navy px-4" style="background:#0F1E4D; color:#fff;">
              Continue to Final Review (Step 6) <i class="fa fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        {{-- ========================================================= --}}
        {{-- STEP 6: PREVIEW & FINAL ISSUANCE                          --}}
        {{-- ========================================================= --}}
        @else
        <form method="POST" action="{{ route('ec-certificate.saveStep', 6) }}">
          @csrf

          <div class="alert alert-success py-3 mb-4 d-flex align-items-center gap-3">
            <i class="fa fa-shield-check fa-2x text-success"></i>
            <div>
              <h6 class="fw-bold mb-1">Verification Complete &bull; Ready for Official Issuance</h6>
              <p class="small mb-0">Review the consolidated details below. Submitting will register the Environmental Clearance certificate, advance the project lifecycle, and record the statutory audit log.</p>
            </div>
          </div>

          {{-- 4 Metric Cards --}}
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <div class="p-3 border rounded bg-light">
                <span class="small text-muted">Applicant</span>
                <h6 class="fw-bold mb-0 mt-1">{{ $s1['applicant_name'] ?? 'Applicant' }}</h6>
                <small class="text-muted">{{ $selectedProject?->customer?->mimas_no ?? 'No MIMAS' }}</small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 border rounded bg-light">
                <span class="small text-muted">Project &amp; District</span>
                <h6 class="fw-bold mb-0 mt-1">{{ $selectedProject?->project_code ?? 'ENV-PROJECT' }}</h6>
                <small class="text-muted">{{ $selectedProject?->district?->name ?? 'District' }}</small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 border rounded bg-light">
                <span class="small text-muted">EC Reference No.</span>
                <h6 class="fw-bold mb-0 mt-1 text-primary">{{ $s1['ec_ref_no'] ?? 'SEIAA-TN/EC/...' }}</h6>
                <small class="text-muted">Parivesh: {{ $s1['parivesh_app_no'] ?? '—' }}</small>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-3 border rounded bg-light">
                <span class="small text-muted">Validity &amp; Expiry</span>
                <h6 class="fw-bold mb-0 mt-1 text-success">{{ $s1['validity_years'] ?? 5 }} Years</h6>
                <small class="text-muted">Valid till: {{ date('d M Y', strtotime('+'.($s1['validity_years'] ?? 5).' years')) }}</small>
              </div>
            </div>
          </div>

          {{-- Verification Checklist --}}
          <div class="card border p-3 mb-4">
            <h6 class="fw-bold mb-3"><i class="fa fa-list-check me-2 text-primary"></i> Statutory Issuance Checklist</h6>
            <div class="row g-2">
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Parivesh Approval Recorded</div>
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Certificate Attachment Verified</div>
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Official SEIAA Letterhead Previewed</div>
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Digital Document Folder Linked</div>
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Applicant Communication Configured</div>
              <div class="col-md-6"><i class="fa fa-check-circle text-success me-2"></i> Audit Trail Entry Ready</div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('ec-certificate.step', 5) }}" class="btn btn-outline-secondary px-4">
              <i class="fa fa-arrow-left me-1"></i> Back to Step 5
            </a>
            <button type="submit" class="btn btn-success px-5 fw-bold" style="background:#059669; border-color:#059669;">
              <i class="fa fa-check-double me-1"></i> Confirm &amp; Finalize EC Certificate
            </button>
          </div>
        </form>
        @endif

      </div>
    </div>

  </div>
</div>
@endsection
