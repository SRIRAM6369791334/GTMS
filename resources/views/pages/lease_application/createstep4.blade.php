@extends('layouts.app')
@section('title', 'Step4')
@section('main_content')
<div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap" style="max-width:860px;">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
    <div class="sp-step active"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 4 of 7</div>
    <h4>Folders</h4>
    <div class="wc-sub">Your documents are organised into three folders. Open a folder to upload files against its checklist.</div>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="folder-tile">
          <div class="fico"><i class="fa fa-folder-open"></i></div>
          <div class="ftitle">1. Documents</div>
          <div class="fmeta">12 required files</div>
          <div class="fprogress"><div class="bar" style="width:0%"></div></div>
          <span class="badge-status pending">Not started</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="folder-tile">
          <div class="fico"><i class="fa fa-folder"></i></div>
          <div class="ftitle">2. Lease Application</div>
          <div class="fmeta">Form + 4 affidavits</div>
          <div class="fprogress"><div class="bar" style="width:0%"></div></div>
          <span class="badge-status pending">Not started</span>
        </div>
      </div>
      <div class="col-md-4">
        <div class="folder-tile">
          <div class="fico"><i class="fa fa-inbox"></i></div>
          <div class="ftitle">3. Plan</div>
          <div class="fmeta">Source, KML, PDF</div>
          <div class="fprogress"><div class="bar" style="width:0%"></div></div>
          <span class="badge-status pending">Not started</span>
        </div>
      </div>
    </div>

    <div class="card-panel mt-4 mb-0" style="background:var(--navy-soft); border:none;">
      <div class="d-flex gap-2">
        <i class="bi bi-lightbulb" style="color:var(--navy);"></i>
        <div style="font-size:.78rem; color:#33447a;">
          Based on your <b>Rule 44</b> category, 16 documents are required in total across these folders. The exact checklist is shown in the next step.
        </div>
      </div>
    </div>

    <div class="wizard-actions">
      <a href="/step3" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <a href="/step5" class="btn btn-navy px-4">Open Documents <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>


        </div>
</div>

@endsection
