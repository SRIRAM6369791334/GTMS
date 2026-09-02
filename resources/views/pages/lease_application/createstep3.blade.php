@extends('layouts.app')
@section('title', 'Step3')
@section('main_content')

<div class="content-body default-height">
        <div class="container-fluid">

<div class="wizard-wrap" style="max-width:860px;">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
    <div class="sp-step active"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 3 of 7</div>
    <h4>Category Under Rule</h4>
    <div class="wc-sub">Select the rule this lease falls under. This determines which documents will be required in Step 5.</div>

    <div class="row g-2">
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">MDCC</div><div class="opt-desc">Mining Dues Clearance Certificate</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 12 (2-A)(a)</div><div class="opt-desc">Renewal of quarry lease</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 19 (1)</div><div class="opt-desc">Grant of quarry lease</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 19 (2)(a)</div><div class="opt-desc">Quarry lease — government land</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 19-A</div><div class="opt-desc">Quarry lease — private land</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 36-F</div><div class="opt-desc">Transport permit related lease</div></div></div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile selected">
          <div class="opt-radio"></div>
          <div><div class="opt-title">Rule 44</div><div class="opt-desc">Quarrying of minor minerals — selected</div></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="opt-tile"><div class="opt-radio"></div><div><div class="opt-title">Rule 7</div><div class="opt-desc">General mining lease conditions</div></div></div>
      </div>
    </div>

    <div class="wizard-actions">
      <a href="/step2" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <a href="/step4" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</div>
        </div>
</div>
@endsection
