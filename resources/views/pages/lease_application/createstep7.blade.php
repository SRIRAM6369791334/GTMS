@extends('layouts.app')
@section('title', 'Step7 - Preview')
@section('main_content')

<div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap" style="max-width: 900px;">

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
    <div class="wc-eyebrow">Step 7 of 7</div>
    <h4>Application Preview</h4>
    <div class="wc-sub">Review all details before submitting the final lease application.</div>

    <div class="card-panel mt-4 mb-4" style="background:var(--navy-soft); border:none;">
      <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="bi bi-person-lines-fill me-2"></i>1. Application & Basic Info</h6>
      <div class="row g-2" style="font-size: 0.85rem;">
        <div class="col-md-4"><span class="text-muted">Client Name:</span> <br><b>R. Kumaresan</b></div>
        <div class="col-md-4"><span class="text-muted">District:</span> <br><b>Coimbatore</b></div>
        <div class="col-md-4"><span class="text-muted">Survey No:</span> <br><b>124/2A</b></div>
        <div class="col-md-4 mt-2"><span class="text-muted">Village / Taluk:</span> <br><b>Mettupalayam</b></div>
        <div class="col-md-4 mt-2"><span class="text-muted">Area Extent:</span> <br><b>2.5 Hectares</b></div>
      </div>
    </div>

    <div class="card-panel mb-4" style="background:#f8f9fa; border:1px solid #e9ecef;">
      <h6 style="font-weight:600; margin-bottom: 12px;"><i class="bi bi-tags-fill me-2" style="color:#6c757d;"></i>2. Category Details</h6>
      <div class="row g-2" style="font-size: 0.85rem;">
        <div class="col-md-6"><span class="text-muted">Mineral Type:</span> <br><b>Rough Stone & Gravel</b></div>
        <div class="col-md-6"><span class="text-muted">Lease Period:</span> <br><b>5 Years</b></div>
      </div>
    </div>

    <div class="card-panel mb-4" style="background:#f8f9fa; border:1px solid #e9ecef;">
      <h6 style="font-weight:600; margin-bottom: 12px;"><i class="bi bi-folder-check me-2" style="color:#6c757d;"></i>3. Folders & Documents</h6>
      <div class="row g-2" style="font-size: 0.85rem;">
        <div class="col-md-6"><span class="text-muted">Uploaded Documents:</span> <br><b>Mining Plan, DGPS, Chitta, Adangal</b></div>
        <div class="col-md-6"><span class="text-muted">Status:</span> <br><span class="badge bg-success">All Mandatory Files Verified</span></div>
      </div>
    </div>

    <div class="card-panel mb-4" style="background:#f8f9fa; border:1px solid #e9ecef;">
      <h6 style="font-weight:600; margin-bottom: 12px;"><i class="bi bi-key-fill me-2" style="color:#6c757d;"></i>4. MIMAS Details</h6>
      <div class="row g-2" style="font-size: 0.85rem;">
        <div class="col-md-6"><span class="text-muted">User ID:</span> <br><b>mimas_user_id</b></div>
        <div class="col-md-6"><span class="text-muted">Email ID:</span> <br><b>name@example.com</b></div>
      </div>
    </div>

    <div class="card-panel mt-4 mb-0" style="background:var(--green-soft); border:none;">
      <div class="d-flex gap-2">
        <i class="bi bi-check-circle" style="color:var(--green);"></i>
        <div style="font-size:.78rem; color:#0f4c27;">
          You are about to submit the final application. Once submitted, it will be moved to the processing queue.
        </div>
      </div>
    </div>

    <div class="wizard-actions mt-4">
      <a href="/step6" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back to Edit</a>
      <a href="/application" class="btn btn-green px-4">Submit Final Application <i class="bi bi-check-lg"></i></a>
    </div>
  </div>
</div>

        </div>
</div>

@endsection
