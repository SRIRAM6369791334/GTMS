@extends('layouts.app')
@section('title', 'Folders - Step 4')
@push('styles')
<link href="/css/gtms-lease.css?v=3" rel="stylesheet">
@endpush

@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step4-page" class="lz-page">

      <!-- ========================================================= -->
      <!-- STEPPER PROGRESS BAR (PARITY WITH STEPS 1 - 3)           -->
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
        <div class="sp-step done">
          <div class="circ"><i class="bi bi-check-lg"></i></div>
          <div class="sp-label">Category</div>
        </div>
        <div class="sp-step active">
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

      @php
        $uploads = $draft['uploaded_docs'] ?? [];
        $f7Count = 0; $f8Count = 0; $f9Count = 0;
        foreach ($uploads as $item => $doc) {
          $fId = $doc['folder_id'] ?? null;
          if ($fId == 7) $f7Count++;
          elseif ($fId == 8) $f8Count++;
          elseif ($fId == 9) $f9Count++;
          else {
            $it = (int)$item;
            if ($it >= 1 && $it <= 9) $f7Count++;
            elseif ($it >= 10 && $it <= 16) $f8Count++;
            elseif ($it >= 17 && $it <= 19) $f9Count++;
          }
        }
        $f7Pct = round(($f7Count / 9) * 100);
        $f8Pct = round(($f8Count / 7) * 100);
        $f9Pct = round(($f9Count / 3) * 100);
        $totalUploaded = $f7Count + $f8Count + $f9Count;
        $totalRequired = 19;
        $overallPct = round(($totalUploaded / $totalRequired) * 100);
      @endphp

      <!-- ========================================================= -->
      <!-- MAIN STEP 4 WIZARD CARD                                   -->
      <!-- ========================================================= -->
      <div class="card s4-main-card lz-card">

        <!-- HEADER BLOCK -->
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 s4-header-block lz-head">
          <div>
            <div class="s4-eyebrow lz-eyebrow">STEP 4 OF 8 &bull; REGULATORY DOCUMENT REPOSITORY</div>
            <h4 class="s4-title lz-title">Regulatory Document Folders</h4>
            <div class="s4-subtitle lz-subtitle">
              Your statutory documents are partitioned into three regulatory dossiers according to Tamil Nadu Minor Mineral Concession Rules. Review your folder readiness or jump directly into the document checklist.
            </div>
          </div>
          <div class="s4-time-tag lz-time">
            <i class="bi bi-clock-history me-1"></i>Est. ~1 min overview
          </div>
        </div>

        <!-- OVERALL PROGRESS SUMMARY BANNER -->
        <div class="s4-summary-banner mb-4">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <span class="s4-summary-badge">
                <i class="bi bi-folder-check me-1"></i>Dossier Readiness
              </span>
              <span class="s4-summary-text">
                <strong>{{ $totalUploaded }}</strong> of <strong>{{ $totalRequired }}</strong> total documents uploaded
              </span>
            </div>
            <div class="s4-summary-pct">
              {{ $overallPct }}% Complete
            </div>
          </div>
          <div class="progress s4-summary-progress">
            <div class="progress-bar s4-summary-bar" role="progressbar" style="width: {{ $overallPct }}%;" aria-valuenow="{{ $overallPct }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>

        <!-- ========================================================= -->
        <!-- 3 FOLDER CARDS GRID                                       -->
        <!-- ========================================================= -->
        <div class="row g-3 s4-folder-grid">

          <!-- FOLDER 1: STATUTORY DOCUMENTS -->
          <div class="col-lg-4 col-md-6">
            <a href="{{ route('step5') }}" class="s4-folder-link text-decoration-none">
              <div class="s4-folder-card s4-f1 {{ $f7Pct == 100 ? 'is-complete' : ($f7Count > 0 ? 'is-progress' : '') }}">
                <div class="s4-card-top d-flex align-items-start justify-content-between mb-3">
                  <div class="s4-icon-wrap s4-icon-blue">
                    <i class="bi bi-folder2-open"></i>
                  </div>
                  <span class="s4-folder-no">FOLDER 01</span>
                </div>

                <div class="s4-card-body">
                  <h5 class="s4-folder-title">1. Statutory Documents</h5>
                  <p class="s4-folder-desc">
                    Revenue Patta, FMB sketch, Chitta, Adangal, VAO certificate &amp; revenue NOCs.
                  </p>

                  <div class="s4-doc-chips mb-3">
                    <span class="s4-chip">Patta &amp; Chitta</span>
                    <span class="s4-chip">FMB Sketch</span>
                    <span class="s4-chip">VAO NOC</span>
                  </div>

                  <div class="s4-progress-wrap mb-2">
                    <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                      <span class="s4-count-label">Files Uploaded</span>
                      <span class="s4-count-num"><strong>{{ $f7Count }}</strong> / 9</span>
                    </div>
                    <div class="progress s4-card-progress">
                      <div class="progress-bar s4-bar-blue" role="progressbar" style="width: {{ $f7Pct }}%;" aria-valuenow="{{ $f7Pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>

                <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                  <span class="badge {{ $f7Pct == 100 ? 's4-badge-complete' : ($f7Count > 0 ? 's4-badge-progress' : 's4-badge-pending') }}">
                    <i class="bi {{ $f7Pct == 100 ? 'bi-check-circle-fill' : ($f7Count > 0 ? 'bi-hourglass-split' : 'bi-circle') }} me-1"></i>
                    {{ $f7Pct == 100 ? 'Complete' : ($f7Count > 0 ? $f7Count . '/9 Saved' : 'Pending') }}
                  </span>
                  <span class="s4-action-text">
                    Open Folder <i class="bi bi-arrow-right ms-1"></i>
                  </span>
                </div>
              </div>
            </a>
          </div>

          <!-- FOLDER 2: LEASE APPLICATION -->
          <div class="col-lg-4 col-md-6">
            <a href="{{ route('step5') }}" class="s4-folder-link text-decoration-none">
              <div class="s4-folder-card s4-f2 {{ $f8Pct == 100 ? 'is-complete' : ($f8Count > 0 ? 'is-progress' : '') }}">
                <div class="s4-card-top d-flex align-items-start justify-content-between mb-3">
                  <div class="s4-icon-wrap s4-icon-indigo">
                    <i class="bi bi-briefcase-fill"></i>
                  </div>
                  <span class="s4-folder-no">FOLDER 02</span>
                </div>

                <div class="s4-card-body">
                  <h5 class="s4-folder-title">2. Lease Application</h5>
                  <p class="s4-folder-desc">
                    Mining application form, affidavits, clearances, ID proofs &amp; compliance forms.
                  </p>

                  <div class="s4-doc-chips mb-3">
                    <span class="s4-chip">Form 1-A</span>
                    <span class="s4-chip">Affidavits</span>
                    <span class="s4-chip">ID &amp; Tax Proof</span>
                  </div>

                  <div class="s4-progress-wrap mb-2">
                    <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                      <span class="s4-count-label">Files Uploaded</span>
                      <span class="s4-count-num"><strong>{{ $f8Count }}</strong> / 7</span>
                    </div>
                    <div class="progress s4-card-progress">
                      <div class="progress-bar s4-bar-indigo" role="progressbar" style="width: {{ $f8Pct }}%;" aria-valuenow="{{ $f8Pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>

                <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                  <span class="badge {{ $f8Pct == 100 ? 's4-badge-complete' : ($f8Count > 0 ? 's4-badge-progress' : 's4-badge-pending') }}">
                    <i class="bi {{ $f8Pct == 100 ? 'bi-check-circle-fill' : ($f8Count > 0 ? 'bi-hourglass-split' : 'bi-circle') }} me-1"></i>
                    {{ $f8Pct == 100 ? 'Complete' : ($f8Count > 0 ? $f8Count . '/7 Saved' : 'Pending') }}
                  </span>
                  <span class="s4-action-text">
                    Open Folder <i class="bi bi-arrow-right ms-1"></i>
                  </span>
                </div>
              </div>
            </a>
          </div>

          <!-- FOLDER 3: MINING & SURVEY PLANS -->
          <div class="col-lg-4 col-md-6">
            <a href="{{ route('step5') }}" class="s4-folder-link text-decoration-none">
              <div class="s4-folder-card s4-f3 {{ $f9Pct == 100 ? 'is-complete' : ($f9Count > 0 ? 'is-progress' : '') }}">
                <div class="s4-card-top d-flex align-items-start justify-content-between mb-3">
                  <div class="s4-icon-wrap s4-icon-emerald">
                    <i class="bi bi-geo-alt-fill"></i>
                  </div>
                  <span class="s4-folder-no">FOLDER 03</span>
                </div>

                <div class="s4-card-body">
                  <h5 class="s4-folder-title">3. Mining &amp; Survey Plans</h5>
                  <p class="s4-folder-desc">
                    Approved 5-year progressive mining plan, DGPS survey &amp; geo-referenced maps.
                  </p>

                  <div class="s4-doc-chips mb-3">
                    <span class="s4-chip">5-Yr Mine Plan</span>
                    <span class="s4-chip">DGPS Chart</span>
                    <span class="s4-chip">Drone Ortho</span>
                  </div>

                  <div class="s4-progress-wrap mb-2">
                    <div class="d-flex justify-content-between align-items-center s4-meta-line mb-1">
                      <span class="s4-count-label">Files Uploaded</span>
                      <span class="s4-count-num"><strong>{{ $f9Count }}</strong> / 3</span>
                    </div>
                    <div class="progress s4-card-progress">
                      <div class="progress-bar s4-bar-emerald" role="progressbar" style="width: {{ $f9Pct }}%;" aria-valuenow="{{ $f9Pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>

                <div class="s4-card-footer d-flex align-items-center justify-content-between pt-2">
                  <span class="badge {{ $f9Pct == 100 ? 's4-badge-complete' : ($f9Count > 0 ? 's4-badge-progress' : 's4-badge-pending') }}">
                    <i class="bi {{ $f9Pct == 100 ? 'bi-check-circle-fill' : ($f9Count > 0 ? 'bi-hourglass-split' : 'bi-circle') }} me-1"></i>
                    {{ $f9Pct == 100 ? 'Complete' : ($f9Count > 0 ? $f9Count . '/3 Saved' : 'Pending') }}
                  </span>
                  <span class="s4-action-text">
                    Open Folder <i class="bi bi-arrow-right ms-1"></i>
                  </span>
                </div>
              </div>
            </a>
          </div>

        </div>

        <!-- ========================================================= -->
        <!-- STATUTORY CATEGORY NOTICE CARD                            -->
        <!-- ========================================================= -->
        <div class="s4-notice-card mt-4 mb-4 lz-notice">
          <div class="d-flex align-items-start gap-3 p-3">
            <div class="s4-notice-icon lz-notice-icon">
              <i class="bi bi-info-circle-fill"></i>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <span class="s4-notice-title lz-notice-title">Document Checklist Configured for</span>
                <span class="badge s4-rule-pill"><i class="bi bi-bookmark-check me-1"></i>TNMIR {{ $categoryName }}</span>
              </div>
              <div class="s4-notice-desc lz-notice-text">
                Based on your selected category, <strong>16 statutory filings</strong> and <strong>3 survey plans</strong> (19 total) are structured into these folders. All uploads will be verified against Tamil Nadu Minor Mineral Concession Rules (1959).
              </div>
            </div>
          </div>
        </div>

        <!-- ========================================================= -->
        <!-- WIZARD ACTIONS FOOTER                                     -->
        <!-- ========================================================= -->
        <div class="s4-actions d-flex justify-content-between align-items-center flex-wrap gap-3 pt-3 lz-actions">
          <a href="{{ route('step3') }}" class="s4-btn-back lz-btn lz-btn-ghost">
            <i class="bi bi-arrow-left me-1"></i>
            <span>Back to Step 3</span>
          </a>

          @can('application.create')
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('application.index') }}" class="s4-btn-draft lz-btn lz-btn-outline">
              <i class="bi bi-floppy me-2"></i>
              <span>Save Draft &amp; Exit</span>
            </a>
            <a href="{{ route('step5') }}" class="s4-btn-submit lz-btn lz-btn-primary">
              <span>Open Documents</span>
              <i class="bi bi-arrow-right ms-1"></i>
            </a>
          </div>
          @endcan
        </div>

      </div>

    </div>
  </div>
</div>
@endsection

@section('scripts')
<!-- SCOPED CSS FOR STEP 4: ZERO IMPACT ON OTHER PAGES, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step4-page)
     ========================================================================== */
  #gtms-step4-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */

/* MAIN CARD */

/* SUMMARY READINESS BANNER */
  #gtms-step4-page .s4-summary-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
  }

  #gtms-step4-page .s4-summary-badge {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 0.70rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
  }

  #gtms-step4-page .s4-summary-text {
    font-size: 0.82rem;
    color: #334155;
  }

  #gtms-step4-page .s4-summary-pct {
    font-size: 0.84rem;
    font-weight: 700;
    color: #0F1E4D;
  }

  #gtms-step4-page .s4-summary-progress {
    height: 7px;
    background-color: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
  }

  #gtms-step4-page .s4-summary-bar {
    background: linear-gradient(90deg, #0F1E4D 0%, #2563eb 100%);
    border-radius: 999px;
    transition: width 0.4s ease;
  }

  /* FOLDER GRID & CARDS */
  #gtms-step4-page .s4-folder-grid {
    margin-bottom: 8px;
  }

  #gtms-step4-page .s4-folder-link {
    display: block;
    color: inherit;
  }

  /* Equal-height folder cards: the link wrapper must fill the column so the
     card's height:100% applies, and the progress + footer sit at the bottom. */
  #gtms-step4-page .s4-folder-link {
    display: block;
    height: 100%;
  }
  #gtms-step4-page .s4-card-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
  }
  #gtms-step4-page .s4-progress-wrap {
    margin-top: auto;
  }

  #gtms-step4-page .s4-folder-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 18px 18px 16px;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
  }

  #gtms-step4-page .s4-folder-card:hover {
    transform: translateY(-2px);
    border-color: #0F1E4D;
    box-shadow: 0 8px 24px rgba(15, 30, 77, 0.09);
  }

  #gtms-step4-page .s4-folder-card.is-complete {
    border-color: #10b981;
  }

  #gtms-step4-page .s4-folder-card.is-progress {
    border-color: #93c5fd;
  }

  /* FOLDER ICONS */
  #gtms-step4-page .s4-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    transition: transform 0.2s ease;
  }

  #gtms-step4-page .s4-folder-card:hover .s4-icon-wrap {
    transform: scale(1.06);
  }

  #gtms-step4-page .s4-icon-blue {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }

  #gtms-step4-page .s4-icon-indigo {
    background-color: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
  }

  #gtms-step4-page .s4-icon-emerald {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }

  #gtms-step4-page .s4-folder-no {
    font-size: 0.68rem;
    font-weight: 700;
    color: #94a3b8;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    padding: 3px 7px;
    border-radius: 5px;
    letter-spacing: 0.04em;
  }

  /* CARD TYPOGRAPHY */
  #gtms-step4-page .s4-folder-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
    line-height: 1.3;
  }

  #gtms-step4-page .s4-folder-desc {
    font-size: 0.77rem;
    color: #64748b;
    line-height: 1.45;
    min-height: 44px;
    margin-bottom: 12px;
  }

  /* DOCUMENT CHIPS */
  #gtms-step4-page .s4-doc-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
  }

  #gtms-step4-page .s4-chip {
    font-size: 0.68rem;
    font-weight: 500;
    background-color: #f1f5f9;
    color: #475569;
    padding: 2px 7px;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
  }

  /* CARD PROGRESS */
  #gtms-step4-page .s4-progress-wrap {
    margin-top: auto;
  }

  #gtms-step4-page .s4-meta-line {
    font-size: 0.74rem;
  }

  #gtms-step4-page .s4-count-label {
    color: #64748b;
  }

  #gtms-step4-page .s4-count-num {
    color: #0f172a;
  }

  #gtms-step4-page .s4-card-progress {
    height: 6px;
    background-color: #f1f5f9;
    border-radius: 999px;
    overflow: hidden;
  }

  #gtms-step4-page .s4-bar-blue {
    background-color: #2563eb;
    border-radius: 999px;
  }

  #gtms-step4-page .s4-bar-indigo {
    background-color: #4f46e5;
    border-radius: 999px;
  }

  #gtms-step4-page .s4-bar-emerald {
    background-color: #059669;
    border-radius: 999px;
  }

  /* CARD FOOTER & STATUS BADGES */
  #gtms-step4-page .s4-card-footer {
    border-top: 1px solid #f1f5f9;
    margin-top: 10px;
  }

  #gtms-step4-page .s4-badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  #gtms-step4-page .s4-badge-progress {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  #gtms-step4-page .s4-badge-complete {
    background-color: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
    font-size: 0.70rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  #gtms-step4-page .s4-action-text {
    font-size: 0.75rem;
    font-weight: 600;
    color: #0F1E4D;
    transition: all 0.2s ease;
  }

  #gtms-step4-page .s4-folder-card:hover .s4-action-text {
    color: #2563eb;
    transform: translateX(2px);
  }

  /* STATUTORY NOTICE CARD - LIGHT */

#gtms-step4-page .s4-rule-pill {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    font-size: 0.70rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 5px;
  }

/* WIZARD ACTIONS FOOTER */

/* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"] #gtms-step4-page)
     ========================================================================== */

/* SUMMARY BANNER - DARK */
  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-banner {
    background: #18191a !important;
    border-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-badge {
    background: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-text {
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-pct {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-progress {
    background-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-summary-bar {
    background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 100%) !important;
  }

  /* FOLDER CARDS - DARK */
  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-card {
    background: #18191a !important;
    border-color: #2b2d30 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-card:hover {
    border-color: #3b82f6 !important;
    background: #1c1d1f !important;
    box-shadow: 0 8px 24px rgba(37, 99, 235, 0.15) !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-card.is-complete {
    border-color: #10b981 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-card.is-progress {
    border-color: #2563eb !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-icon-blue {
    background-color: #1e293b !important;
    color: #60a5fa !important;
    border-color: #2563eb !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-icon-indigo {
    background-color: #24243e !important;
    color: #818cf8 !important;
    border-color: #4f46e5 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-icon-emerald {
    background-color: #062c20 !important;
    color: #34d399 !important;
    border-color: #059669 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-no {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-title {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-folder-desc {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-chip {
    background-color: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-count-label {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-count-num {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-card-progress {
    background-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-card-footer {
    border-top-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-badge-pending {
    background-color: #422006 !important;
    color: #fde68a !important;
    border-color: #78350f !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-badge-progress {
    background-color: #172554 !important;
    color: #93c5fd !important;
    border-color: #1d4ed8 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-badge-complete {
    background-color: #064e3b !important;
    color: #a7f3d0 !important;
    border-color: #059669 !important;
  }

  body[data-theme-version="dark"] #gtms-step4-page .s4-action-text {
    color: #60a5fa !important;
  }

  /* NOTICE CARD - DARK */

body[data-theme-version="dark"] #gtms-step4-page .s4-rule-pill {
    background-color: #1e293b !important;
    color: #60a5fa !important;
    border-color: #2563eb !important;
  }

/* ACTIONS - DARK */

</style>
@endsection
