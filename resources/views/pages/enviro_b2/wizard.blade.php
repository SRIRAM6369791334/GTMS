@extends('layouts.app')
@section('title', 'B2 Workflow Step '.$step)
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php($labels=['Applicant','B2 Category','Folders','Documents','Validation','Approval','Preview'])
<div class="content-body default-height"><div class="container-fluid"><div class="wizard-wrap" style="max-width:920px;">
  <div class="step-progress">
    @foreach($labels as $number => $label)<div class="sp-step {{ $number + 1 < $step ? 'done' : ($number + 1 === $step ? 'active' : '') }}"><div class="circ">@if($number + 1 < $step)<i class="bi bi-check-lg"></i>@else{{ $number + 1 }}@endif</div><div class="sp-label">{{ $label }}</div></div>@endforeach
  </div>
  <div class="wizard-card">
    <div class="wc-eyebrow">Step {{ $step }} of 7 &middot; Environment Clearance B2</div>
    @if($step === 1)
      <h4>Client / Applicant</h4><div class="wc-sub">Enter the applicant and project details for the B2 environment-clearance document process.</div>
      <form><div class="row g-3"><div class="col-md-6"><label class="form-label">Client / Applicant Name *</label><input class="form-control" placeholder="e.g. R. Kumaresan"></div><div class="col-md-6"><label class="form-label">Project / Quarry Name *</label><input class="form-control" placeholder="Project name"></div><div class="col-md-6"><label class="form-label">District *</label><select class="form-select"><option>Select district</option><option>Coimbatore</option><option>Salem</option><option>Madurai</option></select></div><div class="col-md-6"><label class="form-label">Contact Number *</label><input class="form-control" placeholder="10-digit mobile number"></div></div></form>
      <div class="card-panel mt-4 mb-0" style="background:var(--navy-soft);border:none"><i class="bi bi-info-circle"></i> <span style="font-size:.78rem">A B2 reference number and document folder will be created after this step.</span></div>
    @elseif($step === 2)
      <h4>Sub Category: B2</h4><div class="wc-sub">Confirm the environmental-clearance category to load the appropriate document structure.</div>
      <div class="row g-3"><div class="col-md-6"><div class="opt-tile selected"><div class="opt-radio"></div><div><div class="opt-title">B2 Category</div><div class="opt-desc">Environment clearance with B2 category requirements</div></div></div></div><div class="col-md-6"><div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">B1 Category</div><div class="opt-desc">Separate EIA / B1 process</div></div></div></div></div>
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">B2 selected: six folders and 29 checklist items will be shown in the next step.</span></div>
    @elseif($step === 3)
      <h4>Environment Clearance &mdash; B2 Folders</h4><div class="wc-sub">Documents are organised into six folders as required by the B2 process.</div>
      @php($folders=[['Documents','Statutory and site records','fa-file-alt'],['Site Photographs','DGPS, fencing and greenbelt','fa-camera'],['Report','Reports, forms and checklist','fa-file-contract'],['GIS','GIS data','fa-globe'],['Signed Reports','Final signed reports','fa-cloud-upload-alt'],['PARIVESH','Online registration documents','fa-desktop']])
      <div class="row g-3">@foreach($folders as [$name,$detail,$icon])<div class="col-md-4"><div class="folder-tile"><div class="fico"><i class="fa {{ $icon }}"></i></div><div class="ftitle">{{ $loop->iteration }}. {{ $name }}</div><div class="fmeta">{{ $detail }}</div><span class="badge-status pending">Not started</span></div></div>@endforeach</div>
    @elseif($step === 4)
      <h4>Document Preparation & Upload</h4><div class="wc-sub">Prepare each file against the B2 checklist. This is a UI preview; upload actions are intentionally not connected.</div>
      <div class="dropzone"><i class="bi bi-cloud-arrow-up"></i><div class="dz-title">Drag & drop files here, or click to browse</div><div class="dz-sub">PDF, JPG, PNG and Office files &mdash; maximum 10 MB</div></div>
      @php($items=['500m Radius Letter','Existing Pit Letter','Approved Mining Plan Book','DGPS Photograph','Pre-feasibility Report','GIS Data','Signed Reports','Common Application Form','Payment Receipt'])
      <div class="mt-4">@foreach($items as $item)<div class="checklist-row"><div class="ci-icon"><i class="bi bi-file-earmark"></i></div><div class="flex-grow-1"><div class="ci-name">{{ $loop->iteration }}. {{ $item }}</div><div class="ci-meta">Not uploaded</div></div><span class="badge-status {{ $loop->iteration < 4 ? 'mandatory' : 'pending' }}">{{ $loop->iteration < 4 ? 'Mandatory' : 'Pending' }}</span><button type="button" class="btn btn-sm btn-outline-navy py-0 px-2">Upload</button></div>@endforeach</div>
    @elseif($step === 5)
      <h4>Validate Data</h4><div class="wc-sub">Review uploaded documents, check completeness and return files for correction where needed.</div>
      @foreach(['Documents folder','Site photographs','Report & GIS','PARIVESH registration'] as $item)<div class="checklist-row"><div class="ci-icon"><i class="bi bi-file-earmark-check"></i></div><div class="flex-grow-1"><div class="ci-name">{{ $item }}</div><div class="ci-meta">Checklist review pending</div></div><span class="badge-status pending">Pending validation</span><button type="button" class="btn btn-sm btn-outline-navy py-0 px-2">Review</button></div>@endforeach
      <div class="card-panel mt-4 mb-0" style="background:#fff6e6;border:none"><i class="bi bi-arrow-repeat"></i> <span style="font-size:.78rem">If data is not correct, send it back to document preparation for re-upload.</span></div>
    @elseif($step === 6)
      <h4>Approve Data & Generate Reports</h4><div class="wc-sub">Complete the B2 workflow by approving verified data, generating reports and archiving the project.</div>
      <div class="row g-3"><div class="col-md-4"><div class="folder-tile"><div class="fico"><i class="fa fa-check"></i></div><div class="ftitle">Approve Data</div><div class="fmeta">Approved & verified document set</div><span class="badge-status verified">Ready</span></div></div><div class="col-md-4"><div class="folder-tile"><div class="fico"><i class="fa fa-chart-bar"></i></div><div class="ftitle">Generate Reports</div><div class="fmeta">View or download B2 reports</div><span class="badge-status pending">Pending</span></div></div><div class="col-md-4"><div class="folder-tile"><div class="fico"><i class="fa fa-archive"></i></div><div class="ftitle">Archive & Backup</div><div class="fmeta">Store final project records</div><span class="badge-status pending">Pending</span></div></div></div>
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">B2 document-preparation UI process completed.</span></div>
    @else
      <h4>Data Preview</h4><div class="wc-sub">Review all environment-clearance B2 project data before final completion.</div>
      <div class="card-panel mt-4 mb-4" style="background:var(--navy-soft); border:none;">
        <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="bi bi-person-lines-fill me-2"></i>Project Details</h6>
        <div class="row g-2" style="font-size: 0.85rem;">
          <div class="col-md-4"><span class="text-muted">Client Name:</span> <br><b>R. Kumaresan</b></div>
          <div class="col-md-4"><span class="text-muted">Project Name:</span> <br><b>Quarry Project A</b></div>
          <div class="col-md-4"><span class="text-muted">District:</span> <br><b>Coimbatore</b></div>
        </div>
      </div>
      <div class="card-panel mb-4" style="background:#f8f9fa; border:1px solid #e9ecef;">
        <h6 style="font-weight:600; margin-bottom: 12px;"><i class="bi bi-tags-fill me-2" style="color:#6c757d;"></i>Category & Documents</h6>
        <div class="row g-2" style="font-size: 0.85rem;">
          <div class="col-md-6"><span class="text-muted">Selected Category:</span> <br><b>B2 Category</b></div>
          <div class="col-md-6"><span class="text-muted">Documents Verified:</span> <br><span class="badge bg-success">All Approved</span></div>
        </div>
      </div>
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Please confirm to finalize the B2 process.</span></div>
    @endif
    <div class="wizard-actions"><a href="{{ $step === 1 ? route('environment-b2.index') : route('environment-b2.step', $step - 1) }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> {{ $step === 1 ? 'Cancel' : 'Back' }}</a>@can('environment.b2.create')<a href="{{ $step === 7 ? route('environment-b2.index') : route('environment-b2.step', $step + 1) }}" class="btn {{ $step === 7 ? 'btn-green' : 'btn-navy' }} px-4">{{ $step === 7 ? 'Finish Process' : 'Save & Continue' }} <i class="bi bi-arrow-right"></i></a>@endcan</div>

  </div>
</div></div></div>
@endsection
