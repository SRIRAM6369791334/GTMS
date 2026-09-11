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
        {{-- Fallback static tiles if no categories in DB --}}
        @foreach([
          ['id' => 1, 'code' => 'MDCC', 'name' => 'Mining Dues Clearance Certificate'],
          ['id' => 2, 'code' => 'Rule 12 (2-A)(a)', 'name' => 'Renewal of quarry lease'],
          ['id' => 3, 'code' => 'Rule 19 (1)', 'name' => 'Grant of quarry lease'],
          ['id' => 4, 'code' => 'Rule 19 (2)(a)', 'name' => 'Quarry lease — government land'],
          ['id' => 5, 'code' => 'Rule 19-A', 'name' => 'Quarry lease — private land'],
          ['id' => 6, 'code' => 'Rule 36-F', 'name' => 'Transport permit related lease'],
          ['id' => 7, 'code' => 'Rule 44', 'name' => 'Quarrying of minor minerals'],
          ['id' => 8, 'code' => 'Rule 7', 'name' => 'General mining lease conditions'],
        ] as $cat)
        <div class="col-md-6">
          <label class="opt-tile-label w-100" for="cat_{{ $cat['id'] }}">
            <input type="radio" name="category_id" id="cat_{{ $cat['id'] }}" value="{{ $cat['id'] }}" class="d-none cat-radio">
            <div class="opt-tile" id="tile_{{ $cat['id'] }}">
              <div class="opt-radio"></div>
              <div>
                <div class="opt-title">{{ $cat['code'] }}</div>
                <div class="opt-desc">{{ $cat['name'] }}</div>
              </div>
            </div>
          </label>
        </div>
        @endforeach
        @endforelse
      </div>
    </form>

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="/step2" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      <div class="d-flex gap-2">
        <button type="button" id="btn_save_later_step3" class="btn btn-outline-primary px-3">
          <i class="fa fa-save me-1"></i> Save &amp; Continue Later
        </button>
        @can('application.create')
        <button type="button" id="btn_save_step3" class="btn btn-navy px-4">Save &amp; Continue <i class="bi bi-arrow-right"></i></button>
        @endcan
      </div>
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
    if (!selected) { alert('Please select a category/rule before saving draft.'); return; }

    var btn = $(this);
    btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Saving Draft...');

    var formData = $('#step3_form').serialize() + '&exit=1';
    $.ajax({
      url: '{{ route("step3.save") }}',
      type: 'POST',
      data: formData,
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      success: function(res) {
        window.location.href = res.redirect || '/application';
      },
      error: function(xhr) {
        btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Save &amp; Continue Later');
        alert('Failed to save category draft.');
      }
    });
  });

  $('#btn_save_step3').on('click', function() {
    var selected = $('input[name="category_id"]:checked').val();
    if (!selected) { alert('Please select a category/rule.'); return; }
    
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
        alert('Failed to save category selection.');
      }
    });
  });
});
</script>
@endsection
