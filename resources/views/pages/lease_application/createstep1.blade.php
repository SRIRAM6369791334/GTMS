@extends('layouts.app')
@section('title', 'Lease Application - Step 1')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">


            <div class="wizard-wrap">

  <div class="step-progress">
    <div class="sp-step active"><div class="circ">1</div><div class="sp-label">Application</div></div>
    <div class="sp-step"><div class="circ">2</div><div class="sp-label">Basic &amp; MIMAS</div></div>
    <div class="sp-step"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
    <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 1 of 8</div>
    <h4>Lease Application</h4>
    <div class="wc-sub">Start by entering the applicant's identity or lookup their Customer Unique ID to auto-fill all registered customer data.</div>

    <!-- CUSTOMER UNIQUE ID LOOKUP CARD -->
    <div class="card p-3 my-3" style="background:#f0f7ff; border:2px dashed #93c5fd; border-radius:14px;">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <label class="form-label fw-bold mb-0 text-primary" style="font-size:0.92rem;">
          <i class="fa fa-fingerprint me-1"></i> Customer Unique ID Lookup
        </label>
        <span class="badge bg-primary text-white"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
      </div>
      <p class="text-muted small mb-2">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
      
      <div class="input-group">
        <span class="input-group-text bg-white border-primary"><i class="fa fa-search text-primary"></i></span>
        <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold border-primary" 
               placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
        <button class="btn btn-primary px-3 fw-bold" type="button" id="btn_lookup_mimas">
          <i class="fa fa-sync-alt me-1"></i> Fetch Details
        </button>
      </div>
      
      <datalist id="mimas_datalist">
        @if(isset($customers))
          @foreach($customers as $c)
            <option value="{{ $c->mimas_no }}">{{ $c->company_name }} ({{ $c->customer_name }})</option>
          @endforeach
        @endif
      </datalist>

      <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
    </div>

    <form id="step1_form" method="POST" action="{{ route('step1.save') }}">
      @csrf
      @if(!empty($draft['step1']))
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          @foreach($draft['step1'] as $key => $value)
            @if(!is_array($value))
              var el = document.getElementById('field_{{ $key }}');
              if (el) el.value = '{{ addslashes($value) }}';
            @endif
          @endforeach
        });
      </script>
      @endif
      <input type="hidden" name="customer_id" id="field_customer_id">
      <input type="hidden" name="mimas_no" id="field_mimas_no">

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Client / Representative Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control auto-filled-field" id="field_client_name" name="client_name" placeholder="e.g. R. Kumaresan" required>
          <div class="form-text-hint mt-1">As it appears on the applicant's official ID proof.</div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Company / Quarry Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control auto-filled-field" id="field_company_name" name="company_name" placeholder="e.g. Sri Bala Traders" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">District <span class="text-danger">*</span></label>
          <select class="form-select auto-filled-field" id="field_district_id" name="district_id" required>
            <option value="" selected disabled>Select District</option>
            @if(isset($districts) && $districts->isNotEmpty())
              @foreach($districts as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
              @endforeach
            @endif
          </select>
        </div>

        <div class="col-12">
          <div class="d-flex align-items-center justify-content-between mb-2 mt-1">
            <label class="form-label mb-0 fw-bold text-dark">
              <i class="bi bi-gem text-primary me-1"></i> Mineral Concession Type(s) <span class="text-danger">*</span>
            </label>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 11px;">
              <i class="bi bi-check2-all me-1"></i>Multi-Select Enabled (Choose one or more)
            </span>
          </div>

          <div class="row g-2" id="minerals_selection_grid">
            @php
              $selectedMineralIds = [];
              if (!empty($draft['step1']['mineral_ids']) && is_array($draft['step1']['mineral_ids'])) {
                  $selectedMineralIds = $draft['step1']['mineral_ids'];
              } elseif (!empty($draft['step1']['mineral_id'])) {
                  $selectedMineralIds = [(int)$draft['step1']['mineral_id']];
              }
            @endphp
            @if(isset($minerals))
              @foreach($minerals as $m)
                @php
                  $isOther = (strtolower($m->name) === 'others' || strtolower($m->name) === 'other');
                  $isChecked = in_array($m->id, $selectedMineralIds);
                @endphp
                <div class="col-6 col-md-3">
                  <input type="checkbox" class="btn-check mineral-checkbox" name="mineral_ids[]"
                         id="mineral_{{ $m->id }}" value="{{ $m->id }}"
                         data-name="{{ $m->name }}"
                         data-is-other="{{ $isOther ? '1' : '0' }}"
                         {{ $isChecked ? 'checked' : '' }}>
                  <label class="btn btn-outline-navy w-100 text-start py-2 px-3 d-flex align-items-center justify-content-between mineral-pill-label" for="mineral_{{ $m->id }}" style="border-radius:8px; cursor:pointer;">
                    <span class="d-flex align-items-center text-truncate">
                      <i class="bi bi-gem me-2 text-primary mineral-gem-icon"></i>
                      <span class="fw-semibold text-truncate" style="font-size:0.87rem;">{{ $m->name }}</span>
                    </span>
                    <span class="badge bg-light text-secondary border badge-select-indicator ms-1" style="font-size: 10px;">
                      <i class="bi bi-check-lg"></i>
                    </span>
                  </label>
                </div>
              @endforeach
            @endif
          </div>

          <!-- Dynamic Manual Entry for "Others" -->
          @php
            $hasOtherChecked = in_array(8, $selectedMineralIds) || !empty($draft['step1']['other_mineral_name']);
          @endphp
          <div id="other_mineral_box" class="mt-2 p-3 rounded-3" style="display: {{ $hasOtherChecked ? 'block' : 'none' }}; background: #f8fafc; border: 1.5px dashed #93c5fd;">
            <label class="form-label fw-bold text-navy small mb-1" for="field_other_mineral_name">
              <i class="bi bi-pencil-square text-primary me-1"></i> Specify Other Mineral Name <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="field_other_mineral_name" name="other_mineral_name" 
                   placeholder="Type manual mineral name (e.g. Quartzite, Silica Sand, Black Granite, Dolomite...)"
                   value="{{ $draft['step1']['other_mineral_name'] ?? '' }}">
            <div class="form-text mt-1 text-muted" style="font-size:11px;">
              <i class="bi bi-info-circle me-1 text-primary"></i> Specify the manual mineral name when "Others" is selected. This will be stored and tracked in your statutory dossier.
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control auto-filled-field" id="field_mobile_num" name="mobile_num" placeholder="10-digit mobile number" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control auto-filled-field" id="field_email" name="email" placeholder="customer@example.com">
        </div>

        <div class="col-md-6">
          <label class="form-label">Secondary Contact Person <span class="text-muted small">(Optional)</span></label>
          <input type="text" class="form-control auto-filled-field" id="field_secondary_contact_person" name="secondary_contact_person" placeholder="e.g. Quarry Manager / Site In-charge">
        </div>

        <div class="col-md-6">
          <label class="form-label">Secondary Mobile Number <span class="text-muted small">(Optional)</span></label>
          <input type="text" class="form-control auto-filled-field" id="field_secondary_mobile_num" name="secondary_mobile_num" placeholder="10-digit Alternate Mobile" maxlength="15">
        </div>

        <div class="col-md-6">
          <label class="form-label">PAN Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control text-uppercase auto-filled-field" id="field_pan" name="pan" placeholder="AAACS1234F" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Aadhaar Number <span class="text-danger">*</span></label>
          <div class="input-group">
            <span class="input-group-text bg-white"><i class="fa fa-id-card text-info"></i></span>
            <input type="text" class="form-control auto-filled-field aadhaar-format" id="field_aadhaar_no" name="aadhaar_no" placeholder="9876-5432-1012" maxlength="14" required>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">GSTIN (15 Digits)</label>
          <input type="text" class="form-control text-uppercase auto-filled-field" id="field_gstin" name="gstin" placeholder="33AAACS1234F1Z5">
        </div>

        <div class="col-md-6">
          <label class="form-label">Quarry Area (Hectares)</label>
          <input type="text" class="form-control auto-filled-field" id="field_area" name="area" placeholder="e.g. 14.20">
        </div>

        <div class="col-md-12">
          <label class="form-label">Registered Business Address</label>
          <textarea class="form-control auto-filled-field" id="field_address" name="address" rows="2" placeholder="Street, Taluk, Village, Pincode..."></textarea>
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

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="{{ route('application.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-lg"></i> Cancel</a>
      @can('application.create')
      <div class="d-flex gap-2">
        <button type="button" id="btn_save_later_step1" class="btn btn-outline-primary px-3">
          <i class="fa fa-save me-1"></i> Save &amp; Continue Later
        </button>
        <button type="button" id="btn_save_step1" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
      </div>
      @endcan
    </div>
  </div>


  {{-- <div class="text-center mt-3" style="font-size:.72rem; color:var(--muted);">All fields marked * are mandatory as per District Mining Office guidelines.</div> --}}
</div>



        </div>
    </div>




@endsection

@section('scripts')
<style>
  .field-autofilled {
    background-color: #f0fdf4 !important;
    border-color: #86efac !important;
    transition: all 0.4s ease;
  }
  .mineral-pill-label {
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    transition: all 0.2s ease-in-out;
  }
  .mineral-pill-label:hover {
    border-color: #0F1E4D;
    background: #f8fafc;
  }
  .btn-check:checked + .mineral-pill-label {
    background-color: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
    box-shadow: 0 3px 8px rgba(15, 30, 77, 0.18);
  }
  .btn-check:checked + .mineral-pill-label .badge-select-indicator {
    background-color: #ffffff !important;
    color: #0F1E4D !important;
    border-color: #ffffff !important;
  }
  .btn-check:checked + .mineral-pill-label i.mineral-gem-icon {
    color: #fbbf24 !important;
  }
</style>
<script>
$(document).ready(function() {
  // Handle mineral checkbox change & Other toggle
  function handleMineralChange() {
    var hasOther = false;
    $('.mineral-checkbox:checked').each(function() {
      if ($(this).data('is-other') == 1 || $(this).data('name').toLowerCase().indexOf('other') !== -1) {
        hasOther = true;
      }
    });

    if (hasOther) {
      $('#other_mineral_box').slideDown(200);
      $('#field_other_mineral_name').focus();
    } else {
      $('#other_mineral_box').slideUp(200);
    }
  }

  $(document).on('change', '.mineral-checkbox', handleMineralChange);

  function performMimasLookup() {
    var mimasNo = $('#mimas_search_input').val().trim();
    if (!mimasNo) {
      $('#mimas_feedback_box').html(
        '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-exclamation-triangle me-1"></i> Please enter or select a Customer Unique ID first.</div>'
      ).show();
      return;
    }

    $('#btn_lookup_mimas').prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Searching...');
    $('#mimas_feedback_box').hide();

    $.ajax({
      url: '/customers/lookup-mimas/' + encodeURIComponent(mimasNo),
      type: 'GET',
      dataType: 'json',
      success: function(res) {
        $('#btn_lookup_mimas').prop('disabled', false).html('<i class="fa fa-sync-alt me-1"></i> Fetch Details');
        if (res.status === 1 && res.data) {
          var c = res.data;
          // Populate form fields
          $('#field_customer_id').val(c.id);
          $('#field_mimas_no').val(c.mimas_no);
          $('#field_client_name').val(c.customer_name);
          $('#field_secondary_contact_person').val(c.secondary_contact_person || '');
          $('#field_company_name').val(c.company_name);
          $('#field_mobile_num').val(c.mobile_num);
          $('#field_secondary_mobile_num').val(c.secondary_mobile_num || '');
          $('#field_email').val(c.email || '');
          $('#field_pan').val(c.pan);
          $('#field_aadhaar_no').val(c.aadhaar_no || '');
          $('#field_gstin').val(c.gstin || '');
          $('#field_area').val(c.area || '');
          $('#field_address').val(c.address || '');

          if (c.district_id) {
            $('#field_district_id').val(c.district_id);
          }
          if (c.mineral_id) {
            $('.mineral-checkbox').prop('checked', false);
            var cb = $('#mineral_' + c.mineral_id);
            if (cb.length) {
              cb.prop('checked', true);
            }
            handleMineralChange();
          }

          // Visual cue on all autofilled fields
          $('.auto-filled-field').addClass('field-autofilled');
          setTimeout(function() {
            $('.auto-filled-field').removeClass('field-autofilled');
          }, 3000);

          // Success feedback card
          $('#mimas_feedback_box').html(
            '<div class="alert alert-success py-2 px-3 mb-0 small d-flex align-items-center justify-content-between">' +
              '<div>' +
                '<i class="fa fa-check-circle me-1 text-success"></i> ' +
                '<strong>Customer Profile Loaded:</strong> ' + (c.company_name || c.customer_name) +
                ' &middot; <span class="text-muted">' + (c.district_name || 'District N/A') + '</span>' +
                ' &middot; <span class="badge bg-success-subtle text-success border border-success ms-1">Customer ID: ' + c.mimas_no + '</span>' +
              '</div>' +
              '<span class="badge bg-success text-white">All details populated</span>' +
            '</div>'
          ).slideDown();

          if (typeof toastr !== 'undefined') {
            toastr.success('Customer details populated from Customer Unique ID #' + c.mimas_no);
          }
        }
      },
      error: function(xhr) {
        $('#btn_lookup_mimas').prop('disabled', false).html('<i class="fa fa-sync-alt me-1"></i> Fetch Details');
        var msg = 'No registered customer found matching Customer Unique ID: "' + mimasNo + '".';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        $('#mimas_feedback_box').html(
          '<div class="alert alert-danger py-2 px-3 mb-0 small">' +
            '<i class="fa fa-times-circle me-1"></i> ' + msg + ' Please verify the number or add a new customer.' +
          '</div>'
        ).slideDown();

        if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        }
      }
    });
  }

  // Trigger on button click
  $('#btn_lookup_mimas').on('click', performMimasLookup);

  // Trigger on Enter key in search box
  $('#mimas_search_input').on('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      performMimasLookup();
    }
  });

  // Trigger on selection change from datalist
  $('#mimas_search_input').on('change', function() {
    var val = $(this).val().trim();
    if (val.length >= 3) {
      performMimasLookup();
    }
  });

  // Auto-convert / mask Aadhaar input: 987654321001 -> 9876-5432-1001
  function maskAadhaar(value) {
    if (!value) return '';
    var digits = value.replace(/\D/g, '').substring(0, 12);
    var parts = [];
    for (var i = 0; i < digits.length; i += 4) {
      parts.push(digits.substring(i, i + 4));
    }
    return parts.join('-');
  }

  $(document).on('input paste keyup', '#field_aadhaar_no, .aadhaar-format', function() {
    var input = this;
    var current = $(input).val();
    var formatted = maskAadhaar(current);
    if (current !== formatted) {
      $(input).val(formatted);
    }
  });

  // Validate mineral selection helper
  function validateMinerals() {
    if ($('.mineral-checkbox:checked').length === 0) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Mineral Type Required',
          text: 'Please select at least one Mineral Type.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.error('Please select at least one Mineral Type.');
      } else {
        alert('Please select at least one Mineral Type.');
      }
      $('html, body').animate({ scrollTop: $('#minerals_selection_grid').offset().top - 100 }, 300);
      return false;
    }

    var isOtherSelected = false;
    $('.mineral-checkbox:checked').each(function() {
      if ($(this).data('is-other') == 1 || $(this).data('name').toLowerCase().indexOf('other') !== -1) {
        isOtherSelected = true;
      }
    });

    if (isOtherSelected && !$('#field_other_mineral_name').val().trim()) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Other Mineral Name Required',
          text: 'Please specify the Other Mineral Name.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.error('Please specify the Other Mineral Name.');
      } else {
        alert('Please specify the Other Mineral Name.');
      }
      $('#field_other_mineral_name').focus();
      return false;
    }

    return true;
  }

  // Save & Continue Later via AJAX (exit = 1)
  $('#btn_save_later_step1').on('click', function() {
    var form = document.getElementById('step1_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    if (!validateMinerals()) {
      return;
    }
    var btn = $(this);
    var mimasSearch = $('#mimas_search_input').val().trim();
    if (mimasSearch && !$('#field_mimas_no').val()) {
      $('#field_mimas_no').val(mimasSearch);
    }
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');
    
    var formData = $('#step1_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step1.save") }}',
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
        var msg = 'Validation failed. Please fill basic details to save draft.';
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

  // Save Step 1 via AJAX
  $('#btn_save_step1').on('click', function() {
    var form = document.getElementById('step1_form');
    if (form && !form.checkValidity()) {
      form.reportValidity();
      return;
    }
    if (!validateMinerals()) {
      return;
    }
    var btn = $(this);
    var mimasSearch = $('#mimas_search_input').val().trim();
    if (mimasSearch && !$('#field_mimas_no').val()) {
      $('#field_mimas_no').val(mimasSearch);
    }
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    
    $.ajax({
      url: '{{ route("step1.save") }}',
      type: 'POST',
      data: $('#step1_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step2';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('Save & Continue <i class="bi bi-arrow-right"></i>');
        var msg = 'Validation failed. Please check all required fields.';
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


