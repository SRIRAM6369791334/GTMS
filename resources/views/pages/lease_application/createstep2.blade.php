@extends('layouts.app')
@section('title', 'Step2')
@section('main_content')

  <div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step active"><div class="circ">2</div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 2 of 7</div>
    <h4>Basic Information</h4>
    <div class="wc-sub">Who should we contact about this application? This person will receive SMS/email updates on status changes.</div>

    <form>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Contact Person Name *</label>
          <input type="text" class="form-control" placeholder="Full name">
        </div>
        <div class="col-md-6">
          <label class="form-label">Mobile Number *</label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:.83rem; background:#fff;">+91</span>
            <input type="text" class="form-control" placeholder="10-digit mobile number">
          </div>
          <div class="form-text-hint mt-1">OTP verification required before final submission.</div>
        </div>
      </div>
    </form>

    <div class="wizard-actions">
      <a href="/step1" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <a href="/step3" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>

        </div>
  </div>



@endsection
