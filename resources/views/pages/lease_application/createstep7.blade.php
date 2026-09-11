@extends('layouts.app')
@section('title', 'Step7 - Preview')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">

    <div class="wizard-wrap" style="max-width: 920px;">

      <div class="step-progress">
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">MIMAS</div></div>
        <div class="sp-step active"><div class="circ">7</div><div class="sp-label">Preview</div></div>
      </div>

      <div class="wizard-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
          <div>
            <div class="wc-eyebrow">Step 7 of 7</div>
            <h4 class="mb-0">Application Preview &amp; Verification</h4>
            <div class="wc-sub">Verify all details entered across previous steps before final submission.</div>
          </div>
          <div>
            <span class="badge bg-primary px-3 py-2" style="font-size: 0.82rem;">
              <i class="bi bi-shield-check me-1"></i> Pre-Submission Review
            </span>
          </div>
        </div>

        <!-- 1. APPLICANT & BASIC INFO (FROM STEP 1) -->
        <div class="card-panel mt-3 mb-3" style="background:var(--navy-soft); border:1px solid #bfdbfe; border-radius:10px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="color:var(--navy); font-weight:700; margin-bottom: 0;">
              <i class="bi bi-person-lines-fill me-2"></i>1. Applicant &amp; Entity Information (Step 1)
            </h6>
            <a href="/step1" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 1</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-4"><span class="text-muted">Company / Quarry Name:</span> <br><b>{{ $previewData['company_name'] ?? 'N/A' }}</b></div>
            <div class="col-md-4"><span class="text-muted">Representative Name:</span> <br><b>{{ $previewData['client_name'] ?? 'N/A' }}</b></div>
            <div class="col-md-4"><span class="text-muted">MIMAS Identifier:</span> <br><b class="text-primary">{{ $previewData['mimas_no'] ?? 'N/A' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">District:</span> <br><b>{{ $previewData['district_name'] ?? 'N/A' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Aadhaar Number:</span> <br><b>{{ $previewData['aadhaar_no'] ?? 'N/A' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">PAN Number:</span> <br><b>{{ $previewData['pan'] ?? 'N/A' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">GSTIN:</span> <br><b>{{ $previewData['gstin'] ?? 'Not provided' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Applicant Mobile:</span> <br><b>{{ $previewData['mobile_num'] ?? 'N/A' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Applicant Email:</span> <br><b>{{ $previewData['email'] ?? 'Not provided' }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Quarry Area Extent:</span> <br><b>{{ $previewData['extent_display'] ?? 'Not specified' }}</b></div>
            <div class="col-md-8 mt-2"><span class="text-muted">Registered Business Address:</span> <br><b>{{ $previewData['address'] ?? 'Not provided' }}</b></div>
          </div>
        </div>

        <!-- 2. CONTACT PERSON (FROM STEP 2) -->
        <div class="card-panel mb-3" style="background:#f8f9fa; border:1px solid #e2e8f0; border-radius:10px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:700; margin-bottom: 0;">
              <i class="bi bi-person-check-fill me-2" style="color:#0284c7;"></i>2. Authorized Contact Person (Step 2)
            </h6>
            <a href="/step2" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 2</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-6"><span class="text-muted">Contact Person Name:</span> <br><b>{{ $previewData['contact_person'] ?? 'N/A' }}</b></div>
            <div class="col-md-6"><span class="text-muted">Official Mobile for Alerts:</span> <br><b>+91 {{ $previewData['contact_mobile'] ?? 'N/A' }}</b></div>
          </div>
        </div>

        <!-- 3. CATEGORY & MINERAL (FROM STEP 3) -->
        <div class="card-panel mb-3" style="background:#f8f9fa; border:1px solid #e2e8f0; border-radius:10px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:700; margin-bottom: 0;">
              <i class="bi bi-tags-fill me-2" style="color:#64748b;"></i>3. Lease Category &amp; Mining Details (Step 3)
            </h6>
            <a href="/step3" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 3</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-6"><span class="text-muted">Selected Lease Category:</span> <br><b>{{ $previewData['category_code'] ?? 'N/A' }}</b> &mdash; {{ $previewData['category_name'] ?? 'N/A' }}</div>
            <div class="col-md-3"><span class="text-muted">Mineral Type:</span> <br><b>{{ $previewData['mineral_name'] ?? 'Not specified' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Lease Validity Period:</span> <br><b>{{ $previewData['lease_period'] ?? '5 Years' }}</b></div>
          </div>
        </div>

        <!-- 4. FOLDERS & DOCUMENTS (FROM STEP 4 & 5) -->
        <div class="card-panel mb-3" style="background:#f8f9fa; border:1px solid #e2e8f0; border-radius:10px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:700; margin-bottom: 0;">
              <i class="bi bi-folder-check me-2" style="color:#16a34a;"></i>4. Folders &amp; Document Checklist (Step 5)
            </h6>
            <a href="/step5" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 5</a>
          </div>
          <div class="row g-2 mb-2" style="font-size: 0.85rem;">
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small">1. Documents Folder (#7):</span> <br>
                <b>{{ $previewData['f7_count'] ?? 0 }} / 9 Files Attached</b>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small">2. Lease Application (#8):</span> <br>
                <b>{{ $previewData['f8_count'] ?? 0 }} / 7 Files Attached</b>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small">3. Plan Files (#9):</span> <br>
                <b>{{ $previewData['f9_count'] ?? 0 }} / 3 Files Attached</b>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="font-size: 0.85rem;">
            <div>
              <span class="text-muted">Total Upload Progress:</span> 
              <b>{{ $previewData['uploaded_count'] }} of 19 Items Uploaded</b>
            </div>
            <div>
              @if($previewData['uploaded_count'] >= 19)
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> All 19 Files Verified &amp; Attached across 3 Folders</span>
              @elseif($previewData['uploaded_count'] > 0)
                <span class="badge bg-primary"><i class="bi bi-file-earmark-check me-1"></i> {{ $previewData['uploaded_count'] }} Files Uploaded (Remaining {{ 19 - $previewData['uploaded_count'] }} draft copies will generate on submit)</span>
              @else
                <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> 0 of 19 Uploaded (Draft placeholders will be generated on submit)</span>
              @endif
            </div>
          </div>
          @if(!empty($previewData['uploaded_docs']) && count($previewData['uploaded_docs']) > 0)
          <div class="mt-3 pt-2 border-top">
            <small class="text-muted fw-bold d-block mb-1">Attached Files from Step 5:</small>
            <div class="d-flex flex-wrap gap-2">
              @foreach($previewData['uploaded_docs'] as $docItem => $doc)
                <span class="badge bg-light text-dark border py-1 px-2" style="font-size:0.75rem;">
                  <i class="bi bi-file-earmark-check text-success me-1"></i> Item #{{ $docItem }}: {{ $doc['file_name'] ?? 'file' }}
                </span>
              @endforeach
            </div>
          </div>
          @endif
        </div>

        <!-- 5. MIMAS CREDENTIALS (FROM STEP 6) -->
        <div class="card-panel mb-4" style="background:#f8f9fa; border:1px solid #e2e8f0; border-radius:10px;">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 style="font-weight:700; margin-bottom: 0;">
              <i class="bi bi-key-fill me-2" style="color:#d97706;"></i>5. MIMAS Portal Credentials (Step 6)
            </h6>
            <a href="/step6" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 6</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-3"><span class="text-muted">User ID:</span> <br><b>{{ $previewData['mimas_user_id'] ?? 'Not Entered' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Password:</span> <br><b class="text-muted">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</b> <span class="badge bg-success-subtle text-success border border-success ms-1" style="font-size:0.65rem;">Encrypted AES-256</span></div>
            <div class="col-md-3"><span class="text-muted">Registered Email:</span> <br><b>{{ $previewData['mimas_email'] ?? 'Not Entered' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Registered Contact:</span> <br><b>{{ $previewData['mimas_contact'] ?? 'Not Entered' }}</b></div>
          </div>
        </div>

        <!-- SUBMIT CONFIRMATION BANNER -->
        <div class="card-panel mt-4 mb-0" style="background:var(--green-soft); border:none; border-radius:10px;">
          <div class="d-flex gap-2">
            <i class="bi bi-check-circle fs-5" style="color:var(--green);"></i>
            <div style="font-size:.82rem; color:#0f4c27;">
              <b>Ready for Submission:</b> You are about to submit the final application. Once submitted, it will be assigned an official Application Number (LA-{{ date('Y') }}-NNNN), moved to the scrutiny queue, and all documents will be compiled into the secure compliance dossier.
            </div>
          </div>
        </div>

        <div class="wizard-actions mt-4">
          <a href="/step6" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back to Step 6</a>
          @can('application.create')
          <form action="{{ route('application.submit') }}" method="POST" class="d-inline" id="final_submit_form">
            @csrf
            <input type="hidden" name="mimas_no" value="{{ $previewData['mimas_no'] ?? '' }}">
            <input type="hidden" name="category_code" value="{{ $previewData['category_code'] ?? '' }}">
            <button type="submit" id="btn_final_submit" class="btn btn-green px-4">
              Submit Final Application <i class="bi bi-check-lg ms-1"></i>
            </button>
          </form>
          @endcan
        </div>

      </div>
    </div>

  </div>
</div>

@endsection
