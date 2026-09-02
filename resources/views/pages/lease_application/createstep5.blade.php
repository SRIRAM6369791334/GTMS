@extends('layouts.app')
@section('title', 'Step5')
@section('main_content')


<div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap" style="max-width:900px;">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
    <div class="sp-step active"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <div class="wc-eyebrow">Step 5 of 7</div>
        <h4>Upload Documents</h4>
        <div class="wc-sub mb-0">Upload each file against its checklist item. Accepted formats: PDF, JPG, PNG (max 10MB each).</div>
      </div>
      <div class="text-end">
        <div style="font-weight:800; font-size:1.1rem; color:var(--navy);">6 / 16</div>
        <div style="font-size:.7rem; color:var(--muted);">uploaded</div>
      </div>
    </div>
    <div class="progress-thin mt-2 mb-4"><div class="progress-bar" style="width:37%"></div></div>

    <div class="dropzone">
      <i class="bi bi-cloud-arrow-up"></i>
      <div class="dz-title">Drag &amp; drop files here, or click to browse</div>
      <div class="dz-sub">Files will be auto-matched to the correct checklist item where possible</div>
    </div>

    <div class="mb-2" style="font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Documents folder</div>
    <div class="checklist-row up">
      <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
      <div class="flex-grow-1"><div class="ci-name">8. Land Document</div><div class="ci-meta">land_document.pdf &middot; 1.2 MB</div></div>
      <span class="badge-status uploaded">Uploaded</span>
      <i class="bi bi-three-dots-vertical text-muted"></i>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">9. Consent (If Applicable)</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">10. Adangal &amp; A-register</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status mandatory">Mandatory</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row up">
      <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
      <div class="flex-grow-1"><div class="ci-name">11. Patta &amp; Encumbrance Certificate</div><div class="ci-meta">patta_encumbrance.pdf &middot; 890 KB</div></div>
      <span class="badge-status uploaded">Uploaded</span>
      <i class="bi bi-three-dots-vertical text-muted"></i>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">12. Work Order</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">13. Gazette</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">14. Recommendation Letter</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">15. Mineral Management System – Application</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status mandatory">Mandatory</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">16. Challan downloaded from Mimas</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>

    <div class="mt-3 mb-2" style="font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em;">Lease Application folder</div>
    <div class="checklist-row up">
      <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
      <div class="flex-grow-1"><div class="ci-name">1. Lease application – signed, FMB, Plan</div><div class="ci-meta">lease_signed_fmb.pdf &middot; 2.1 MB</div></div>
      <span class="badge-status verified">Verified</span>
      <i class="bi bi-three-dots-vertical text-muted"></i>
    </div>
    <div class="checklist-row up">
      <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
      <div class="flex-grow-1"><div class="ci-name">2. Affidavit – Income Tax</div><div class="ci-meta">affidavit_income_tax.pdf &middot; 410 KB</div></div>
      <span class="badge-status uploaded">Uploaded</span>
      <i class="bi bi-three-dots-vertical text-muted"></i>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">3. IT returns (If Applicable)</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">4. Affidavit – Mining Due</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status mandatory">Mandatory</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row up">
      <div class="ci-icon"><i class="bi bi-check-lg"></i></div>
      <div class="flex-grow-1"><div class="ci-name">5. Affidavit – Mining Lease</div><div class="ci-meta">affidavit_mining_lease.pdf &middot; 380 KB</div></div>
      <span class="badge-status uploaded">Uploaded</span>
      <i class="bi bi-three-dots-vertical text-muted"></i>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">6. Affidavit – 1.5 meter depth</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>
    <div class="checklist-row">
      <div class="ci-icon"><i class="bi bi-file-earmark"></i></div>
      <div class="flex-grow-1"><div class="ci-name">7. Affidavit – Hill Areas</div><div class="ci-meta">Not uploaded</div></div>
      <span class="badge-status pending">Pending</span>
      <button class="btn btn-sm btn-outline-navy py-0 px-2" style="font-size:.7rem;">Upload</button>
    </div>

    <div class="wizard-actions">
      <a href="/step4" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <a href="/step6" class="btn btn-navy px-4">Continue to MIMAS <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>


        </div>
</div>

@endsection
