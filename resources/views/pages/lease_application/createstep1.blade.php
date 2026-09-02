@extends('layouts.app')
@section('title', 'Category')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">


            <div class="wizard-wrap">

  <div class="step-progress">
    <div class="sp-step active"><div class="circ">1</div><div class="sp-label">Application</div></div>
    <div class="sp-step"><div class="circ">2</div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 1 of 7</div>
    <h4>Lease Application</h4>
    <div class="wc-sub">Start by entering the applicant's basic identity. You can edit these details any time before submission.</div>

    <form>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Client Name *</label>
          <input type="text" class="form-control" placeholder="e.g. R. Kumaresan">
          <div class="form-text-hint mt-1">As it appears on the applicant's ID proof.</div>
        </div>
        <div class="col-md-6">
          <label class="form-label">District *</label>
          <select class="form-select">
            <option selected disabled>Select district</option>
            <option>Coimbatore</option>
            <option>Salem</option>
            <option>Madurai</option>
            <option>Tiruchirappalli</option>
            <option>Erode</option>
          </select>
        </div>
      </div>

      <div class="card-panel mt-4 mb-0" style="background:var(--navy-soft); border:none;">
        <div class="d-flex gap-2">
          <i class="bi bi-info-circle" style="color:var(--navy);"></i>
          <div style="font-size:.78rem; color:#33447a;">
            An <b>application number</b> will be auto-generated once you save this step (format: LA-YYYY-NNNN). You'll use it to track the application through validation, approval and archiving.
          </div>
        </div>
      </div>
    </form>

    <div class="wizard-actions">
      <a href="/application" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Cancel</a>
      <a href="/step2" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>

  {{-- <div class="text-center mt-3" style="font-size:.72rem; color:var(--muted);">All fields marked * are mandatory as per District Mining Office guidelines.</div> --}}
</div>



        </div>
    </div>




@endsection


