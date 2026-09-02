@extends('layouts.app')
@section('title', 'Step6')
@section('main_content')

<div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
    <div class="sp-step active"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 6 of 7</div>
    <h4>MIMAS Registration Details</h4>
    <div class="wc-sub">Store the applicant's Mineral Management System (MIMAS) login so the office can retrieve the challan and track status directly.</div>

    <form>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">User ID *</label>
          <input type="text" class="form-control" placeholder="mimas_user_id">
        </div>
        <div class="col-md-6">
          <label class="form-label">Password *</label>
          <input type="password" class="form-control" placeholder="••••••••">
          <div class="form-text-hint mt-1"><i class="bi bi-lock"></i> Stored encrypted, visible only to authorised officers.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email ID *</label>
          <input type="email" class="form-control" placeholder="name@example.com">
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact Number *</label>
          <input type="text" class="form-control" placeholder="10-digit mobile number">
        </div>
      </div>
    </form>

    <div class="card-panel mt-4 mb-0" style="background:var(--green-soft); border:none;">
      <div class="d-flex gap-2">
        <i class="bi bi-check-circle" style="color:var(--green);"></i>
        <div style="font-size:.78rem; color:#0f4c27;">
          This is the last step. On submit, your application moves to <b>Upload &amp; Store</b>, the first stage of the review workflow.
        </div>
      </div>
    </div>

    <div class="wizard-actions">
      <a href="/step5" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <a href="/step7" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>

        </div>
</div>

@endsection
