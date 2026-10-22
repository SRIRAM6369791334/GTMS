@extends('layouts.app')
@section('title', 'Step 3 - Category Under Rule | GTMS')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step3-page">

      <!-- ========================================================= -->
      <!-- STEPPER PROGRESS BAR (Native GTMS Stepper)               -->
      <!-- ========================================================= -->
      <div class="step-progress">
        <div class="sp-step done">
          <div class="circ"><i class="bi bi-check-lg"></i></div>
          <div class="sp-label">Application</div>
        </div>
        <div class="sp-step done">
          <div class="circ"><i class="bi bi-check-lg"></i></div>
          <div class="sp-label">Basic &amp; MIMAS</div>
        </div>
        <div class="sp-step active">
          <div class="circ">3</div>
          <div class="sp-label">Category</div>
        </div>
        <div class="sp-step">
          <div class="circ">4</div>
          <div class="sp-label">Folders</div>
        </div>
        <div class="sp-step">
          <div class="circ">5</div>
          <div class="sp-label">Documents</div>
        </div>
        <div class="sp-step">
          <div class="circ">6</div>
          <div class="sp-label">Handlers</div>
        </div>
        <div class="sp-step">
          <div class="circ">7</div>
          <div class="sp-label">Payment</div>
        </div>
        <div class="sp-step">
          <div class="circ">8</div>
          <div class="sp-label">Review</div>
        </div>
      </div>

      <!-- ========================================================= -->
      <!-- MAIN STEP 3 CARD CONTAINER                                -->
      <!-- ========================================================= -->
      <div class="s3-main-card card border-0">

        <!-- HEADER BLOCK -->
        <div class="s3-header-block d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div>
            <div class="s3-eyebrow d-flex align-items-center gap-1.5">
              <i class="bi bi-journal-check"></i>
              <span>STEP 3 OF 8 • STATUTORY LEASE CATEGORY</span>
            </div>
            <h4 class="s3-title">Category Under Rule</h4>
            <div class="s3-subtitle">
              Select the governing Tamil Nadu Minor Mineral Concession Rule (TNMIR). This configures the mandatory checklist and scrutiny workflow in Step 5.
            </div>
          </div>
          <div class="s3-time-tag">
            <i class="bi bi-clock-history me-1"></i>Est. ~1 min
          </div>
        </div>

        <!-- ========================================================= -->
        <!-- MAIN STEP 3 FORM                                          -->
        <!-- ========================================================= -->
        <form id="step3_form" method="POST" action="{{ route('step3.save') }}">
          @csrf

          @php
            $selectedCatId = $draft['step3']['category_id'] ?? null;
            $isOtherSelected = !empty($draft['step3']['is_other']) 
                               || ($selectedCatId === 'other') 
                               || !empty($draft['step3']['other_category_rule'])
                               || (!empty($selectedCatId) && !$categories->contains('id', $selectedCatId));
            $otherRuleValue = $draft['step3']['other_category_rule'] ?? '';
            if (!$otherRuleValue && $selectedCatId && !$categories->contains('id', $selectedCatId)) {
                $matchedCustom = \App\Models\LeaseCategory::find($selectedCatId);
                if ($matchedCustom) {
                    $otherRuleValue = $matchedCustom->name ?: $matchedCustom->code;
                }
            }
          @endphp

          <!-- SECTION: TNMIR LEASE CATEGORIES -->
          <div class="s3-section-card mb-3">
            <div class="s3-section-header d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="d-flex align-items-center">
                <i class="bi bi-diagram-3-fill text-primary me-2"></i>
                <span class="s3-section-title">TNMIR Statutory Lease Categories <span class="text-danger ms-0.5">*</span></span>
              </div>
              <span class="badge s3-rule-badge">
                <i class="bi bi-book me-1"></i>TNMIR Rules 1959
              </span>
            </div>

            <!-- TILES GRID -->
            <div class="row gx-3 gy-3 s3-form-row">
              @forelse($categories as $cat)
              @php
                $isSelected = (!$isOtherSelected && $selectedCatId == $cat->id);
              @endphp
              <div class="col-md-6">
                <label class="opt-tile-label w-100" for="cat_{{ $cat->id }}">
                  <input type="radio" name="category_id" id="cat_{{ $cat->id }}" value="{{ $cat->id }}" 
                         class="d-none cat-radio" {{ $isSelected ? 'checked' : '' }}>
                  <div class="opt-tile s3-tile {{ $isSelected ? 'selected' : '' }}" id="tile_{{ $cat->id }}">
                    <div class="s3-tile-radio opt-radio mt-3">
                      <div class="s3-radio-inner"></div>
                    </div>
                    <div class="s3-tile-content mt-2">
                      <div class="s3-tile-title opt-title">{{ $cat->code }}</div>
                      <div class="s3-tile-desc opt-desc">{{ $cat->name }}</div>
                    </div>
                    <div class="s3-tile-badge">
                      <i class="bi bi-check-lg"></i>
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

              <!-- Other / Custom Rule Option -->
              <div class="col-md-6">
                <label class="opt-tile-label w-100" for="cat_other">
                  <input type="radio" name="category_id" id="cat_other" value="other" class="d-none cat-radio" {{ $isOtherSelected ? 'checked' : '' }}>
                  <div class="opt-tile s3-tile s3-tile-other {{ $isOtherSelected ? 'selected' : '' }}" id="tile_other">
                    <div class="s3-tile-radio opt-radio mt-3">
                      <div class="s3-radio-inner"></div>
                    </div>
                    <div class="s3-tile-content mt-2">
                      <div class="s3-tile-title opt-title">
                        <i class="bi bi-pencil-square text-primary me-1.5"></i>Other Rule
                      </div>
                      <div class="s3-tile-desc opt-desc">Specify custom or special statutory lease rule / GO</div>
                    </div>
                    <div class="s3-tile-badge">
                      <i class="bi bi-check-lg"></i>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Dynamic Manual Entry for "Other Rule" -->
            <div id="other_category_box" class="s3-other-box mt-3 p-3 rounded-3" style="display: {{ $isOtherSelected ? 'block' : 'none' }};">
              <label class="form-label s3-label fw-bold mb-1.5" for="field_other_category_rule">
                <i class="bi bi-pencil-fill text-primary me-1.5"></i>Specify Statutory Rule / Government Order Name <span class="text-danger">*</span>
              </label>
              <div class="input-group s3-input-group">
                <span class="input-group-text s3-addon"><i class="bi bi-journal-text"></i></span>
                <input type="text" class="form-control s3-input" id="field_other_category_rule" name="other_category_rule"
                       placeholder="e.g. Rule 19-B, Rule 38-A Special Concession, G.O. Ms No. 12..."
                       value="{{ $otherRuleValue }}"
                       maxlength="100">
              </div>
              <div class="form-text-hint s3-hint mt-1.5">
                <i class="bi bi-info-circle me-1 text-primary"></i>Enter the statutory rule title. This will be created and saved as the lease rule for this application dossier.
              </div>
            </div>

          </div>

          <!-- SYSTEM NOTICE PANEL -->
          <div class="s3-notice-card mt-3 mb-0 p-3">
            <div class="d-flex align-items-center gap-2.5">
              <i class="bi bi-info-circle-fill s3-notice-icon"></i>
              <div class="s3-notice-text">
                <b>Document Checklist Impact:</b> The rule selected above directly defines which statutory certificates (Revenue Patta, Mining Plan, EC, DGPS survey, Affidavits) will be mandated in Step 5.
              </div>
            </div>
          </div>

        </form>

        <!-- ========================================================= -->
        <!-- WIZARD ACTIONS FOOTER                                     -->
        <!-- ========================================================= -->
        <div class="wizard-actions s3-actions d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 pt-3">
          <a href="{{ route('step2') }}" class="btn s3-btn-back">
            <i class="bi bi-arrow-left me-1"></i> Back to Step 2
          </a>
          @can('application.create')
          <div class="d-flex align-items-center gap-2">
            <button type="button" id="btn_save_later_step3" class="btn s3-btn-draft px-3">
              <i class="bi bi-save me-1"></i> Save &amp; Continue Later
            </button>
            <button type="button" id="btn_save_step3" class="btn s3-btn-submit px-4">
              <span>Save &amp; Continue</span>
              <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </div>
          @endcan
        </div>

      </div>

    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- SCOPED CSS FOR STEP 3: ZERO IMPACT ON OTHER PAGES, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step3-page)
     ========================================================================== */
  #gtms-step3-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */
  #gtms-step3-page .step-progress {
    margin-bottom: 22px;
  }

  #gtms-step3-page .step-progress .sp-label {
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 6px;
    line-height: 1.2;
  }

  #gtms-step3-page .step-progress .circ {
    width: 32px;
    height: 32px;
    font-size: 0.80rem;
    line-height: 1;
  }

  /* MAIN CARD */
  #gtms-step3-page .s3-main-card {
    border-radius: 14px;
    padding: 26px 30px;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
  }

  @media (max-width: 768px) {
    #gtms-step3-page .s3-main-card {
      padding: 20px 16px;
    }
  }

  #gtms-step3-page .s3-header-block {
    margin-bottom: 20px;
  }

  #gtms-step3-page .s3-eyebrow {
    color: var(--orange, #ea580c);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
    margin-bottom: 0;
  }

  #gtms-step3-page .s3-time-tag {
    font-size: 0.73rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.2;
  }

  #gtms-step3-page .s3-title {
    font-weight: 800;
    font-size: 1.25rem;
    color: #0f172a;
    line-height: 1.3;
    margin-top: 6px;
    margin-bottom: 5px;
  }

  #gtms-step3-page .s3-subtitle {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0;
  }

  /* SECTION CARDS - LIGHT */
  #gtms-step3-page .s3-section-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 18px 20px;
    margin-bottom: 18px;
    transition: border-color 0.2s ease;
  }

  #gtms-step3-page .s3-section-card:hover {
    border-color: #cbd5e1;
  }

  #gtms-step3-page .s3-section-header {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    line-height: 1.2;
  }

  #gtms-step3-page .s3-section-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #1e293b;
  }

  #gtms-step3-page .s3-rule-badge {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.69rem;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: 600;
  }

  /* FORM GRID */
  #gtms-step3-page .s3-form-row {
    --bs-gutter-x: 0.85rem;
    --bs-gutter-y: 0.85rem;
  }

  /* CATEGORY TILES - LIGHT */
  #gtms-step3-page .opt-tile-label {
    cursor: pointer;
    display: block;
    margin-bottom: 0;
  }

  #gtms-step3-page .s3-tile {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 10px;
    padding: 13px 14px;
    min-height: 72px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    position: relative;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  #gtms-step3-page .s3-tile:hover {
    border-color: #94a3b8;
    background-color: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.05);
  }

  /* SELECTED STATE */
  #gtms-step3-page .s3-tile.selected {
    border-color: #0F1E4D !important;
    background-color: #f0f7ff !important;
    box-shadow: 0 4px 14px rgba(15, 30, 77, 0.08) !important;
  }

  /* RADIO CIRCLE */
  #gtms-step3-page .s3-tile-radio {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    margin-top: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-radio-inner {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: transparent;
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-tile.selected .s3-tile-radio {
    border-color: #0F1E4D;
    background: #0F1E4D;
  }

  #gtms-step3-page .s3-tile.selected .s3-radio-inner {
    background: #ffffff;
  }

  /* TILE CONTENT */
  #gtms-step3-page .s3-tile-content {
    flex: 1;
    min-width: 0;
  }

  #gtms-step3-page .s3-tile-title {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.25;
    margin-bottom: 2px;
  }

  #gtms-step3-page .s3-tile-desc {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.35;
  }

  #gtms-step3-page .s3-tile.selected .s3-tile-title {
    color: #0F1E4D;
  }

  #gtms-step3-page .s3-tile.selected .s3-tile-desc {
    color: #334155;
  }

  /* TILE CHECKMARK BADGE */
  #gtms-step3-page .s3-tile-badge {
    opacity: 0;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background-color: #0F1E4D;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.70rem;
    flex-shrink: 0;
    transform: scale(0.7);
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-tile.selected .s3-tile-badge {
    opacity: 1;
    transform: scale(1);
  }

  /* OTHER BOX - LIGHT */
  #gtms-step3-page .s3-other-box {
    background: #f8fafc;
    border: 1.5px dashed #93c5fd;
    border-radius: 10px;
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-label {
    font-size: 0.80rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 6px;
    line-height: 1.25;
    display: flex;
    align-items: center;
  }

  #gtms-step3-page .s3-hint {
    font-size: 0.72rem;
    color: #64748b;
    line-height: 1.4;
    margin-top: 6px;
    margin-bottom: 2px;
  }

  /* INPUT GROUPS - LIGHT (SEAMLESS FOCUS - NO INTERNAL SPLIT) */
  #gtms-step3-page .s3-input-group {
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.10);
    border-radius: 8px;
  }

  #gtms-step3-page .s3-input-group .s3-addon {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-right: none !important;
    border-radius: 8px 0 0 8px;
    color: #64748b;
    font-size: 0.88rem;
    width: 38px;
    height: 40px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
  }

  #gtms-step3-page .s3-input-group .s3-input {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-left: none !important;
    border-radius: 0 8px 8px 0;
    font-size: 0.86rem;
    height: 40px;
    padding: 8px 12px;
    color: #0f172a;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-step3-page .s3-input-group:focus-within .s3-addon,
  #gtms-step3-page .s3-input-group:focus-within .s3-input {
    border-color: #0F1E4D !important;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-step3-page .s3-input-group:focus-within .s3-addon {
    color: #0F1E4D !important;
    background-color: #ffffff !important;
  }

  /* SYSTEM NOTICE CARD */
  #gtms-step3-page .s3-notice-card {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 9px;
  }

  #gtms-step3-page .s3-notice-icon {
    font-size: 1.15rem;
    color: #0F1E4D;
    flex-shrink: 0;
  }

  #gtms-step3-page .s3-notice-text {
    font-size: 0.77rem;
    color: #475569;
    line-height: 1.45;
  }

  /* WIZARD ACTIONS FOOTER */
  #gtms-step3-page .s3-actions {
    border-top: 1px solid #e2e8f0;
  }

  #gtms-step3-page .s3-btn-back {
    font-size: 0.84rem;
    font-weight: 600;
    color: #0F1E4D;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    border-radius: 8px;
    padding: 9px 18px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
  }

  #gtms-step3-page .s3-btn-back:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0F1E4D;
  }

  #gtms-step3-page .s3-btn-draft {
    font-size: 0.84rem;
    font-weight: 600;
    color: #0F1E4D;
    border: 1.5px solid #0F1E4D;
    background: transparent;
    border-radius: 8px;
    padding: 9px 18px;
    transition: all 0.2s ease;
  }

  #gtms-step3-page .s3-btn-draft:hover {
    background: #0F1E4D;
    color: #ffffff;
  }

  #gtms-step3-page .s3-btn-submit {
    background-color: #0F1E4D;
    border: 1.5px solid #0F1E4D;
    color: #ffffff;
    font-size: 0.86rem;
    font-weight: 700;
    border-radius: 8px;
    padding: 9px 24px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.20);
  }

  #gtms-step3-page .s3-btn-submit:hover {
    background-color: #081233;
    border-color: #081233;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
  }

  /* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"] #gtms-step3-page)
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step3-page .s3-main-card {
    background: #1e1e1e !important;
    border: 1px solid #2e3440 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-title {
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-subtitle {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-time-tag {
    background: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  /* SECTION CARDS - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-section-card {
    background: #18191a !important;
    border-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-section-card:hover {
    border-color: #3e4451 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-section-header {
    border-bottom-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-section-title {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-rule-badge {
    background-color: #1e293b !important;
    color: #60a5fa !important;
    border-color: #2563eb !important;
  }

  /* TILES - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-tile {
    background: #18191a !important;
    border-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile:hover {
    background: #202225 !important;
    border-color: #3e4451 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile.selected {
    border-color: #3b82f6 !important;
    background: #13233a !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15) !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile-radio {
    border-color: #4b5563 !important;
    background: #18191a !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile.selected .s3-tile-radio {
    border-color: #3b82f6 !important;
    background: #3b82f6 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile.selected .s3-radio-inner {
    background: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile-title {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile-desc {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile.selected .s3-tile-title {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile.selected .s3-tile-desc {
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-tile-badge {
    background-color: #2563eb !important;
    color: #ffffff !important;
  }

  /* OTHER BOX - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-other-box {
    background: #18191a !important;
    border-color: #3b82f6 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-label {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-hint {
    color: #94a3b8 !important;
  }

  /* INPUTS & ADDONS - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group:focus-within {
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18) !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group .s3-addon {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-right: none !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group .s3-input {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    border-left: none !important;
    color: #f8fafc !important;
    box-shadow: none !important;
    outline: none !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group:focus-within .s3-addon,
  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group:focus-within .s3-input {
    border-color: #60a5fa !important;
    box-shadow: none !important;
    outline: none !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-input-group:focus-within .s3-addon {
    color: #60a5fa !important;
    background-color: #161717 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-input::placeholder {
    color: #64748b !important;
  }

  /* NOTICE CARD - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-notice-card {
    background: #18191a !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-notice-icon {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-notice-text {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-notice-text b {
    color: #e2e8f0 !important;
  }

  /* WIZARD ACTIONS - DARK */
  body[data-theme-version="dark"] #gtms-step3-page .s3-actions {
    border-top-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-back {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-back:hover {
    background: #323639 !important;
    border-color: #60a5fa !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-draft {
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-draft:hover {
    background-color: #1e3a5f !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-submit {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step3-page .s3-btn-submit:hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
  }
</style>

<script>
$(document).ready(function() {
  function syncCategoryUI() {
    var selected = $('input[name="category_id"]:checked').val();
    $('.opt-tile').removeClass('selected');
    $('input[name="category_id"]:checked').closest('label').find('.opt-tile').addClass('selected');

    if (selected === 'other') {
      $('#other_category_box').slideDown(200);
      $('#field_other_category_rule').focus();
    } else {
      $('#other_category_box').slideUp(200);
    }
  }

  // Visual selection toggle
  $(document).on('change', '.cat-radio', function() {
    syncCategoryUI();
  });

  // Pre-select visual state on load
  $('.cat-radio:checked').closest('label').find('.opt-tile').addClass('selected');

  function showCategoryAlert(type, title, msg) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: type,
        title: title,
        text: msg,
        confirmButtonColor: '#0F1E4D'
      });
    } else if (typeof toastr !== 'undefined') {
      toastr[type](msg);
    } else {
      alert(msg);
    }
  }

  function validateCategorySelection() {
    var selected = $('input[name="category_id"]:checked').val();
    if (!selected) {
      showCategoryAlert('warning', 'Category Selection Required', 'Please select a category/rule before continuing.');
      return false;
    }

    if (selected === 'other') {
      var otherVal = $('#field_other_category_rule').val().trim();
      if (!otherVal) {
        showCategoryAlert('warning', 'Custom Rule Required', 'Please enter the custom Category / Rule name.');
        $('#field_other_category_rule').focus();
        return false;
      }
    }
    return true;
  }

  // Save & Continue Later
  $('#btn_save_later_step3').on('click', function() {
    if (!validateCategorySelection()) return;

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
        btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save &amp; Continue Later');
        var msg = 'Failed to save category draft.';
        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        showCategoryAlert('error', 'Error', msg);
      }
    });
  });

  // Save & Continue
  $('#btn_save_step3').on('click', function() {
    if (!validateCategorySelection()) return;

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
        btn.prop('disabled', false).html('<span>Save &amp; Continue</span> <i class="bi bi-arrow-right ms-1"></i>');
        var msg = 'Failed to save category selection.';
        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        showCategoryAlert('error', 'Error', msg);
      }
    });
  });
});
</script>
@endsection
