{{-- ⚠️ DEPRECATED: This file is no longer active. MIMAS credentials merged into Step 2 (createstep2.blade.php). Route /step6 now renders createstep7.blade.php as the Review & Submit page. Kept for reference only. --}}
@extends('layouts.app')
@section('title', 'MIMAS Credentials - Step 6')
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

        @php
          $hasSavedPass = !empty($draft['step6']['mimas_password']);
        @endphp

        <form id="mimas_form" method="POST" action="{{ route('step6.save') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">User ID <span class="text-danger">*</span></label>
              <input type="text" name="mimas_user_id" id="field_mimas_user_id" class="form-control" placeholder="Enter MIMAS User ID" value="{{ $draft['step6']['mimas_user_id'] ?? $draft['step1']['mimas_no'] ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" name="mimas_password" id="field_mimas_password" class="form-control" placeholder="{{ $hasSavedPass ? '•••••••••••• (Stored securely — enter new password to update)' : 'Enter your MIMAS portal password' }}" value="{{ $hasSavedPass ? '__UNCHANGED__' : '' }}" required autocomplete="new-password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <button class="btn btn-outline-secondary d-flex align-items-center justify-content-center px-3" type="button" id="btn_toggle_password" title="Show / Hide Password" style="background:#fff; border:1px solid #ced4da; border-left:none; border-top-left-radius: 0; border-bottom-left-radius: 0; cursor:pointer;">
                  <!-- Eye Open SVG -->
                  <svg id="svg_eye_open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#475569" viewBox="0 0 16 16">
                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                  </svg>
                  <!-- Eye Slashed SVG -->
                  <svg id="svg_eye_closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#2563eb" viewBox="0 0 16 16" style="display:none;">
                    <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"/>
                    <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"/>
                    <path d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"/>
                  </svg>
                </button>
              </div>
              <div class="form-text-hint mt-1"><i class="bi bi-shield-lock text-success me-1"></i> Stored encrypted with AES-256. Plaintext is never exposed in browser source.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email ID <span class="text-danger">*</span></label>
              <input type="email" name="mimas_email" id="field_mimas_email" class="form-control" placeholder="name@example.com" value="{{ $draft['step6']['mimas_email'] ?? $draft['step1']['email'] ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Number <span class="text-danger">*</span></label>
              <input type="tel" name="mimas_contact" id="field_mimas_contact" class="form-control" placeholder="10-digit mobile number" value="{{ $draft['step6']['mimas_contact'] ?? $draft['step1']['mobile_num'] ?? '' }}" required maxlength="10">
            </div>
          </div>
        </form>

        <div class="card-panel mt-4 mb-0" style="background:var(--green-soft); border:none;">
          <div class="d-flex gap-2">
            <i class="bi bi-check-circle" style="color:var(--green);"></i>
            <div style="font-size:.78rem; color:#0f4c27;">
              This is the last step before final review. On submit, your application moves to <b>Upload &amp; Store</b>, the first stage of the review workflow.
            </div>
          </div>
        </div>

        <div class="wizard-actions d-flex justify-content-between align-items-center">
          <a href="{{ route('step5') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
          @can('application.create')
          <div class="d-flex gap-2">
            <button type="button" id="btn_save_later_step6" class="btn btn-outline-primary px-3">
              <i class="fa fa-save me-1"></i> Save &amp; Continue Later
            </button>
            <button type="button" id="btn_save_step6" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
          </div>
          @endcan
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Prevent native form submit (e.g. Enter key) from rendering raw JSON
  $('#mimas_form').on('submit', function(e) {
    e.preventDefault();
    $('#btn_save_step6').trigger('click');
  });

  // Clear masked placeholder on focus so user can type a new password cleanly
  $('#field_mimas_password').on('focus', function() {
    if ($(this).val() === '__UNCHANGED__') {
      $(this).val('');
    }
  });

  // Numeric-only input restriction on contact
  $('#field_mimas_contact').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
  });

  // Toggle password visibility (Eye Icon)
  $('#btn_toggle_password').on('click', function(e) {
    e.preventDefault();
    var passInput = $('#field_mimas_password');
    if (passInput.prop('type') === 'password') {
      passInput.prop('type', 'text');
      $('#svg_eye_open').hide();
      $('#svg_eye_closed').show();
    } else {
      passInput.prop('type', 'password');
      $('#svg_eye_closed').hide();
      $('#svg_eye_open').show();
    }
  });

  // Save & Continue Later (exit = 1)
  $('#btn_save_later_step6').on('click', function() {
    var form = document.getElementById('mimas_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    var formData = $('#mimas_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step6.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (typeof toastr !== 'undefined') toastr.success('MIMAS credentials saved.');
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Failed to save MIMAS details.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof toastr !== 'undefined') toastr.error(msg);
        else alert(msg);
      }
    });
  });

  $('#btn_save_step6').on('click', function() {
    var form = document.getElementById('mimas_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    $.ajax({
      url: '{{ route("step6.save") }}',
      type: 'POST',
      data: $('#mimas_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step7';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('Save & Continue <i class="bi bi-arrow-right"></i>');
        var msg = 'Failed to save MIMAS details.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof toastr !== 'undefined') toastr.error(msg);
        else alert(msg);
      }
    });
  });
});
</script>
@endsection
