@extends('layouts.app')
@section('title', 'Category Under Rule - Step 3')
@section('main_content')

<div class="content-body default-height">
        <div class="container-fluid">

<div class="wizard-wrap" style="max-width:860px;">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
    <div class="sp-step active"><div class="circ">3</div><div class="sp-label">Category</div></div>
    <div class="sp-step"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
    <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
    <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
  </div>

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 3 of 8</div>
    <h4>Category Under Rule</h4>
    <div class="wc-sub">Select the rule this lease falls under. This determines which documents will be required in Step 5.</div>

    <form id="step3_form" method="POST" action="{{ route('step3.save') }}">
      @csrf
      <div class="row g-2">
        @forelse($categories as $cat)
        <div class="col-md-6">
          <label class="opt-tile-label w-100" for="cat_{{ $cat->id }}">
            <input type="radio" name="category_id" id="cat_{{ $cat->id }}" value="{{ $cat->id }}" class="d-none cat-radio" {{ (isset($draft['step3']['category_id']) && $draft['step3']['category_id'] == $cat->id) ? 'checked' : '' }}>
            <div class="opt-tile" id="tile_{{ $cat->id }}">
              <div class="opt-radio"></div>
              <div>
                <div class="opt-title">{{ $cat->code }}</div>
                <div class="opt-desc">{{ $cat->name }}</div>
              </div>
            </div>
          </label>
        </div>
        @empty
        <div class="col-12">
          <div class="alert alert-warning py-3 text-center">
            <i class="fa fa-exclamation-triangle me-2"></i> No active lease categories found in database. Please contact system administrator to configure mining categories.
          </div>
        </div>
        @endforelse
      </div>
    </form>

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="{{ route('step2') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      @can('application.create')
      <div class="d-flex gap-2">
        <button type="button" id="btn_save_later_step3" class="btn btn-outline-primary px-3">
          <i class="fa fa-save me-1"></i> Save &amp; Continue Later
        </button>
        <button type="button" id="btn_save_step3" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
      </div>
      @endcan
    </div>
  </div>
</div>
        </div>
</div>
@endsection

@section('scripts')
<style>
  .opt-tile-label { cursor: pointer; display: block; }
  .cat-radio:checked + .opt-tile { border-color: var(--navy) !important; background: var(--navy-soft) !important; }
  .cat-radio:checked + .opt-tile .opt-radio { background: var(--navy); border-color: var(--navy); }
  .cat-radio:checked + .opt-tile .opt-radio::after { content: ''; display: block; width: 8px; height: 8px; border-radius: 50%; background: #fff; margin: 3px auto; }
</style>
<script>
$(document).ready(function() {
  // Visual selection toggle
  $('.cat-radio').on('change', function() {
    $('.opt-tile').removeClass('selected');
    $(this).closest('label').find('.opt-tile').addClass('selected');
  });
  // Pre-select on load
  $('.cat-radio:checked').closest('label').find('.opt-tile').addClass('selected');

  // Save & Continue Later
  $('#btn_save_later_step3').on('click', function() {
    var selected = $('input[name="category_id"]:checked').val();
    if (!selected) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Category Selection Required',
          text: 'Please select a category/rule before saving draft.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.warning('Please select a category/rule before saving draft.');
      } else {
        alert('Please select a category/rule before saving draft.');
      }
      return;
    }

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');

    var formData = $('#step3_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step3.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (typeof toastr !== 'undefined') toastr.success('Draft category saved.');
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Failed to save category draft.';
        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg);
        }
      }
    });
  });

  $('#btn_save_step3').on('click', function() {
    var selected = $('input[name="category_id"]:checked').val();
    if (!selected) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'Category Selection Required',
          text: 'Please select a category/rule.',
          confirmButtonColor: '#0F1E4D'
        });
      } else if (typeof toastr !== 'undefined') {
        toastr.warning('Please select a category/rule.');
      } else {
        alert('Please select a category/rule.');
      }
      return;
    }
    
    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving...');
    
    $.ajax({
      url: '{{ route("step3.save") }}',
      type: 'POST',
      data: $('#step3_form').serialize(),
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        if (res.status === 1) {
          window.location.href = res.redirect || '/step4';
        }
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('Save & Continue <i class="bi bi-arrow-right"></i>');
        var msg = 'Failed to save category selection.';
        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: msg,
            confirmButtonColor: '#0F1E4D'
          });
        } else if (typeof toastr !== 'undefined') {
          toastr.error(msg);
        } else {
          alert(msg);
        }
      }
    });
  });
});
</script>
@endsection
