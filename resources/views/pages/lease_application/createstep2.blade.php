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

    <form id="step2_form" method="POST" action="{{ route('step2.save') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Contact Person Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="field_contact_person" name="contact_person" placeholder="Full name" value="{{ $draft['step2']['contact_person'] ?? $draft['step1']['client_name'] ?? '' }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:.83rem; background:#fff;">+91</span>
            <input type="text" class="form-control" id="field_contact_mobile" name="contact_mobile" placeholder="10-digit mobile number" value="{{ $draft['step2']['contact_mobile'] ?? $draft['step1']['mobile_num'] ?? '' }}" required maxlength="10">
          </div>
          <div class="form-text-hint mt-1">OTP verification required before final submission.</div>
        </div>
      </div>
    </form>

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="/step1" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <div class="d-flex gap-2">
        <button type="button" id="btn_save_later_step2" class="btn btn-outline-primary px-3">
          <i class="fa fa-save me-1"></i> Save &amp; Continue Later
        </button>
        @can('application.create')
        <button type="button" id="btn_save_step2" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
        @endcan
      </div>
    </div>
  </div>

</div>

        </div>
  </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Save & Continue Later
  $('#btn_save_later_step2').on('click', function() {
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    var formData = $('#step2_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step2.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Validation failed.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
        }
        alert(msg);
      }
    });
  });

  $('#btn_save_step2').on('click', function() {
    var btn = $(this);
    var person = $('#field_contact_person').val().trim();
    var mobile = $('#field_contact_mobile').val().trim();
    
    if (!person) { alert('Contact Person Name is required.'); return; }
    if (!mobile || mobile.length < 10) { alert('Valid 10-digit mobile number is required.'); return; }
    
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    
    $.ajax({
      url: '{{ route("step2.save") }}',
      type: 'POST',
      data: $('#step2_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step3';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('Save & Continue <i class="bi bi-arrow-right"></i>');
        var msg = 'Validation failed.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
        }
        alert(msg);
      }
    });
  });
});
</script>
@endsection
