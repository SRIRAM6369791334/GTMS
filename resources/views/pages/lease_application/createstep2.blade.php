@extends('layouts.app')
@section('title', 'Basic Info & MIMAS Details - Step 2')
@section('main_content')

  <div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step active"><div class="circ">2</div><div class="sp-label">Basic &amp; MIMAS</div></div>
    <div class="sp-step"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">Review</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 2 of 6</div>
    <h4>Basic Information &amp; MIMAS Details</h4>
    <div class="wc-sub">Who should we contact about this application? Enter representative contacts and official Mineral Management System (MIMAS) credentials.</div>

    <form id="step2_form" method="POST" action="{{ route('step2.save') }}">
      @csrf
      <div class="row g-3">
        <div class="col-12">
          <div class="fw-bold text-dark small text-uppercase" style="letter-spacing:0.04em;">
            <i class="fa fa-user-tie text-primary me-1"></i> Primary Contact (Authorized Representative)
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Contact Person Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="field_contact_person" name="contact_person" placeholder="Full name" value="{{ $draft['step2']['contact_person'] ?? $draft['step1']['client_name'] ?? '' }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Primary Mobile Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:.83rem; background:#fff;">+91</span>
            <input type="tel" class="form-control" id="field_contact_mobile" name="contact_mobile" placeholder="10-digit mobile number" value="{{ $draft['step2']['contact_mobile'] ?? $draft['step1']['mobile_num'] ?? '' }}" required maxlength="15" pattern="[0-9]{10,15}">
          </div>
          <div class="form-text-hint mt-1">Primary SMS &amp; status notifications will be sent to this number.</div>
        </div>

        <div class="col-12 mt-4 pt-3 border-top">
          <div class="fw-bold text-dark small text-uppercase" style="letter-spacing:0.04em;">
            <i class="fa fa-user-shield text-indigo me-1"></i> Secondary Contact (Quarry Manager / Site In-charge) <span class="badge bg-light text-muted border fw-normal text-none ms-1">Optional</span>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label">Secondary Contact Person Name</label>
          <input type="text" class="form-control" id="field_secondary_contact_person" name="secondary_contact_person" placeholder="e.g. Quarry Manager / Supervisor" value="{{ $draft['step2']['secondary_contact_person'] ?? $draft['step1']['secondary_contact_person'] ?? '' }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Secondary Mobile Number</label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:.83rem; background:#fff;">+91</span>
            <input type="tel" class="form-control" id="field_secondary_contact_mobile" name="secondary_contact_mobile" placeholder="10-digit alternate mobile number" value="{{ $draft['step2']['secondary_contact_mobile'] ?? $draft['step1']['secondary_mobile_num'] ?? '' }}" maxlength="15" pattern="[0-9]{10,15}">
          </div>
          <div class="form-text-hint mt-1">Site operational &amp; emergency alerts will be routed here.</div>
        </div>

        <!-- MIMAS REGISTRATION DETAILS (MERGED FROM STEP 6) -->
        <div class="col-12 mt-4 pt-3 border-top">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="fw-bold text-dark small text-uppercase" style="letter-spacing:0.04em;">
              <i class="fa fa-key text-warning me-1"></i> MIMAS Portal Registration Details
            </div>
            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1" style="font-size: 11px;">
              <i class="bi bi-shield-lock me-1"></i>Official Government Portal
            </span>
          </div>
          <div class="text-muted small">Store the applicant's Mineral Management System (MIMAS) login so the office can retrieve the challan and track status directly.</div>
        </div>

        @php
          $hasSavedPass = !empty($draft['step6']['mimas_password']) || !empty($draft['step2']['mimas_password']);
        @endphp

        <div class="col-md-6">
          <label class="form-label">MIMAS User ID <span class="text-danger">*</span></label>
          <input type="text" name="mimas_user_id" id="field_mimas_user_id" class="form-control" placeholder="Enter MIMAS User ID" 
                 value="{{ $draft['step6']['mimas_user_id'] ?? $draft['step2']['mimas_user_id'] ?? $draft['step1']['mimas_no'] ?? '' }}" required>
          <div class="form-text-hint mt-1">Applicant login ID on the state mining portal.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">MIMAS Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="password" name="mimas_password" id="field_mimas_password" class="form-control" 
                   data-has-saved="{{ $hasSavedPass ? '1' : '0' }}"
                   placeholder="{{ $hasSavedPass ? '•••••••••••• (Stored securely — enter new password to update)' : 'Enter your MIMAS portal password' }}" 
                   value="{{ $hasSavedPass ? '__UNCHANGED__' : '' }}" required autocomplete="new-password" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
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
          <label class="form-label">MIMAS Email ID <span class="text-danger">*</span></label>
          <input type="email" name="mimas_email" id="field_mimas_email" class="form-control" placeholder="name@example.com" 
                 value="{{ $draft['step6']['mimas_email'] ?? $draft['step2']['mimas_email'] ?? $draft['step1']['email'] ?? '' }}" required>
          <div class="form-text-hint mt-1">Official email registered in the MIMAS portal.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">MIMAS Contact Number <span class="text-danger">*</span></label>
          <input type="tel" name="mimas_contact" id="field_mimas_contact" class="form-control" placeholder="10-digit mobile number" 
                 value="{{ $draft['step6']['mimas_contact'] ?? $draft['step2']['mimas_contact'] ?? $draft['step1']['mobile_num'] ?? '' }}" required maxlength="10">
          <div class="form-text-hint mt-1">Mobile registered for MIMAS OTP and status alerts.</div>
        </div>
      </div>
    </form>

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="{{ route('step1') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      @can('application.create')
      <div class="d-flex gap-2">
        <button type="button" id="btn_save_later_step2" class="btn btn-outline-primary px-3">
          <i class="fa fa-save me-1"></i> Save &amp; Continue Later
        </button>
        <button type="button" id="btn_save_step2" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
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
  // Clear masked placeholder on focus so user can type a new password cleanly
  $('#field_mimas_password').on('focus', function() {
    if ($(this).val() === '__UNCHANGED__') {
      $(this).val('');
    }
  }).on('blur', function() {
    // If applicant blurred without entering anything and has a saved password, restore placeholder
    if ($(this).data('has-saved') == '1' && $(this).val().trim() === '') {
      $(this).val('__UNCHANGED__');
    }
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

  // Numeric-only input restriction on mobiles
  $('#field_contact_mobile, #field_secondary_contact_mobile, #field_mimas_contact').on('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
  });

  // Auto-sync MIMAS contact with primary mobile if empty
  $('#field_contact_mobile').on('blur', function() {
    var val = $(this).val().trim();
    if (val && !$('#field_mimas_contact').val().trim()) {
      $('#field_mimas_contact').val(val);
    }
  });

  // Save & Continue Later
  $('#btn_save_later_step2').on('click', function() {
    var form = document.getElementById('step2_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    var formData = $('#step2_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step2.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (typeof toastr !== 'undefined') {
          toastr.success('Draft saved successfully.');
        }
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Validation failed.';
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            html: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg.replace(/<br>/g, '\n'));
        }
      }
    });
  });

  $('#btn_save_step2').on('click', function() {
    var form = document.getElementById('step2_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    var btn = $(this);
    var person = $('#field_contact_person').val().trim();
    var mobile = $('#field_contact_mobile').val().trim();
    var mimasUserId = $('#field_mimas_user_id').val().trim();
    var mimasEmail = $('#field_mimas_email').val().trim();
    
    if (!person) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Contact Name Required',
          text: 'Authorized Contact Person Name is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('Contact Person Name is required.');
      else alert('Contact Person Name is required.');
      return;
    }
    if (!mobile || mobile.length < 10) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Mobile Number Required',
          text: 'Valid 10-digit primary mobile number is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('Valid 10-digit primary mobile number is required.');
      else alert('Valid 10-digit primary mobile number is required.');
      return;
    }
    if (!mimasUserId) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'MIMAS User ID Required',
          text: 'MIMAS Portal User ID is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('MIMAS User ID is required.');
      else alert('MIMAS User ID is required.');
      $('#field_mimas_user_id').focus();
      return;
    }
    if (!mimasEmail) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'MIMAS Email Required',
          text: 'MIMAS Registered Email ID is required.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') toastr.error('MIMAS Email ID is required.');
      else alert('MIMAS Email ID is required.');
      $('#field_mimas_email').focus();
      return;
    }
    
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
          msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
        }
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Validation Failed',
            html: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg.replace(/<br>/g, '\n'));
        }
      }
    });
  });
});
</script>
@endsection
