@extends('layouts.app')
@section('title', 'Payment Details - Step 7')
@push('styles')
<link href="/css/gtms-lease.css?v=3" rel="stylesheet">
@endpush

@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step7-page" class="lz-page">
      <div class="wizard-wrap">

        <!-- 8-STEP PROGRESS BAR (Exact Native Stepper Parity with Steps 1-6) -->
        <div class="step-progress">
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Handlers</div></div>
          <div class="sp-step active"><div class="circ">7</div><div class="sp-label">Payment</div></div>
          <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
        </div>

        <div class="card s7-main-card wizard-card lz-card">
          <!-- CARD HEADER -->
          <div class="lz-head">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
              <div class="s7-eyebrow lz-eyebrow"><i class="bi bi-cash-coin"></i> Step 7 of 8 &bull; Financial Settlement</div>
              <span class="lz-time"><i class="bi bi-journal-text"></i> Billing Ledger</span>
            </div>
            <h4 class="s7-title lz-title">Payment &amp; Financial Settlement</h4>
            <div class="s7-subtitle lz-subtitle">Record the application value, advance received, balance due and settlement status.</div>
          </div>

          @php
            $pv = (float)($payment['product_value'] ?? 0);
            $pa = (float)($payment['paid_amount'] ?? 0);
            $pe = max(0, $pv - $pa);
            $st = $payment['payment_status'] ?? ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial'));
            // Free (fee waived): stored value is 0; show the quoted (waived) amount in the form
            $isFree = $st === 'free';
            $waiverReason = $payment['waiver_reason'] ?? '';
            if ($isFree) {
              $pv = (float)($payment['waived_amount'] ?? 0);
              $pa = 0;
              $pe = 0;
            }
          @endphp

          <!-- STATUS BANNER: one line summary, pill colour follows the status -->
          <div class="s7-banner" data-status="{{ $st }}" id="s7_banner">
            <div class="s7-banner-icon" aria-hidden="true">
              <i class="bi bi-credit-card-2-front-fill"></i>
            </div>
            <div class="s7-banner-text">
              <div class="s7-banner-title">Statutory Ledger &amp; Fee Calculation</div>
              <div class="s7-banner-desc">Fees, balance due and settlement status update live as you type.</div>
            </div>
            <span class="s7-status-pill" id="banner_status_pill">
              @if($st === 'free')
                <i class="bi bi-gift-fill"></i> Free (Waived)
              @elseif($st === 'paid')
                <i class="bi bi-check-circle-fill"></i> Fully Paid
              @elseif($st === 'partial')
                <i class="bi bi-clock-history"></i> Part Payment
              @else
                <i class="bi bi-exclamation-circle-fill"></i> Pending Payment
              @endif
            </span>
          </div>

          <!-- LIVE KPI CARDS -->
          <div class="row g-3 s7-kpi-row">
            <div class="col-md-4">
              <div class="s7-kpi s7-kpi-contract">
                <div class="s7-kpi-head">
                  <span class="s7-kpi-icon" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></span>
                  <span class="s7-kpi-label" id="kpi_contract_label">Contract Value</span>
                </div>
                <div class="s7-kpi-value" id="card_disp_product_value">₹ {{ number_format($pv, 2) }}</div>
                <div class="s7-kpi-sub">Total quoted service value</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="s7-kpi s7-kpi-paid">
                <div class="s7-kpi-head">
                  <span class="s7-kpi-icon" aria-hidden="true"><i class="bi bi-check-circle-fill"></i></span>
                  <span class="s7-kpi-label">Paid / Advance</span>
                </div>
                <div class="s7-kpi-value" id="card_disp_paid_amount">₹ {{ number_format($pa, 2) }}</div>
                <div class="s7-kpi-sub">Received via Cash / Bank / NEFT</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="s7-kpi s7-kpi-pending">
                <div class="s7-kpi-head">
                  <span class="s7-kpi-icon" aria-hidden="true"><i class="bi bi-hourglass-split"></i></span>
                  <span class="s7-kpi-label">Balance Due</span>
                </div>
                <div class="s7-kpi-value" id="card_disp_pending_amount">₹ {{ number_format($pe, 2) }}</div>
                <div class="s7-kpi-sub">Remaining balance to collect</div>
              </div>
            </div>
          </div>

          <!-- PAYMENT FORM -->
          <form id="payment_form" method="POST" action="{{ route('step7.save') }}" novalidate>
            @csrf

            <div class="s7-section lz-section">
              <div class="s7-section-head lz-section-head">
                <span class="s7-section-title"><i class="bi bi-currency-rupee"></i> Fee Entry &amp; Settlement</span>
                <span class="s7-tag">Statutory Accounting</span>
              </div>

              <!-- ROW 1: AMOUNTS (labels one line each so the three inputs line up) -->
              <div class="row g-3 lz-row">
                <div class="col-md-4">
                  <label class="lz-label" for="field_product_value">Contract Value (₹) <span class="text-danger">*</span></label>
                  <div class="input-group lz-group">
                    <span class="input-group-text lz-addon s7-addon-navy" aria-hidden="true"><i class="bi bi-currency-rupee"></i></span>
                    <input type="number" step="0.01" min="0" name="product_value" id="field_product_value"
                           class="form-control lz-input s7-amount" placeholder="0.00" value="{{ $pv > 0 ? $pv : '' }}" required inputmode="decimal">
                  </div>
                  <div class="lz-hint">Total agreed fee or quotation.</div>
                </div>

                <div class="col-md-4">
                  <label class="lz-label" for="field_paid_amount">Paid Amount (₹) <span class="text-danger">*</span></label>
                  <div class="input-group lz-group">
                    <span class="input-group-text lz-addon s7-addon-green" aria-hidden="true"><i class="bi bi-currency-rupee"></i></span>
                    <input type="number" step="0.01" min="0" name="paid_amount" id="field_paid_amount"
                           class="form-control lz-input s7-amount s7-amount-green" placeholder="0.00" value="{{ $pa > 0 ? $pa : '' }}" required inputmode="decimal">
                  </div>
                  <div class="s7-presets" role="group" aria-label="Quick fill paid amount">
                    <button type="button" class="s7-preset" id="btn_quick_full" title="Set paid equal to contract value">100%</button>
                    <button type="button" class="s7-preset" id="btn_quick_half" title="Set paid to 50% advance">50%</button>
                    <button type="button" class="s7-preset" id="btn_quick_zero" title="Set paid to 0">Clear</button>
                  </div>
                </div>

                <div class="col-md-4">
                  <label class="lz-label" for="field_pending_amount">Pending Balance (₹) <span class="s7-auto">Auto</span></label>
                  <div class="input-group lz-group s7-readonly">
                    <span class="input-group-text lz-addon s7-addon-red" aria-hidden="true"><i class="bi bi-currency-rupee"></i></span>
                    <input type="number" step="0.01" name="pending_amount_display" id="field_pending_amount"
                           class="form-control lz-input s7-amount s7-amount-red" placeholder="0.00" value="{{ $pe }}" readonly tabindex="-1">
                  </div>
                  <div class="lz-hint">Contract value minus paid.</div>
                </div>
              </div>

              <div class="s7-divider"></div>

              <!-- ROW 2: STATUS + LIVE ASSESSMENT (both have a label, so tops align) -->
              <div class="row g-3 lz-row">
                <div class="col-md-6">
                  <label class="lz-label" for="field_payment_status">Payment Settlement Status <span class="text-danger">*</span></label>
                  <select name="payment_status" id="field_payment_status" class="form-select lz-select s7-status-select" required>
                    <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>Pending — full balance due</option>
                    <option value="partial" {{ $st === 'partial' ? 'selected' : '' }}>Partial — part payment received</option>
                    <option value="paid" {{ $st === 'paid' ? 'selected' : '' }}>Paid — fully cleared</option>
                    <option value="free" {{ $st === 'free' ? 'selected' : '' }}>Free — fee waived (no charge)</option>
                  </select>
                  <div class="lz-hint">Set automatically from the amounts; you can override it.</div>
                </div>

                <div class="col-md-6">
                  <span class="lz-label">Settlement Assessment</span>
                  <div class="s7-feedback" id="status_feedback_box" data-status="{{ $st }}" aria-live="polite">
                    <div class="s7-feedback-icon" id="status_feedback_icon" aria-hidden="true">
                      <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="s7-feedback-title" id="status_feedback_text">Full Payment Outstanding</div>
                    <span class="s7-feedback-badge" id="status_feedback_badge">Pending</span>
                  </div>
                </div>
              </div>
              <!-- FEE WAIVER (only when status = Free). The reason is stored with
                   the application and shown in the dossier, report and invoice. -->
              <div class="s7-waiver" id="s7_waiver" {{ $isFree ? '' : 'hidden' }}>
                <div class="s7-waiver-head">
                  <span class="s7-waiver-icon" aria-hidden="true"><i class="bi bi-gift-fill"></i></span>
                  <div>
                    <div class="s7-waiver-title">Fee waiver</div>
                    <div class="s7-waiver-sub">No amount will be charged or invoiced for this application.</div>
                  </div>
                </div>
                <label class="lz-label" for="field_waiver_reason">Reason for waiving the fee <span class="text-danger">*</span></label>
                <textarea name="waiver_reason" id="field_waiver_reason" class="form-control lz-textarea" rows="3" maxlength="1000"
                          placeholder="e.g. Government / panchayat project, long-standing client, approved by MD on 12-Nov-2026">{{ $waiverReason }}</textarea>
                <span class="error-text waiver_reason_error"></span>
                <div class="s7-waiver-foot">
                  <span class="lz-hint m-0"><i class="bi bi-shield-check"></i> Kept permanently, even after the application is completed.</span>
                  <span class="lz-hint m-0" id="waiver_count">0 / 1000</span>
                </div>
              </div>
            </div>

            <!-- PRO TIP -->
            <div class="s7-tip">
              <i class="bi bi-lightbulb-fill s7-tip-icon" aria-hidden="true"></i>
              <div>
                <strong>Pro tip:</strong> These entries appear in <strong>Chapter VI (Financial &amp; Billing Ledger)</strong>
                of the Executive Dossier. You can take an advance now and record milestone payments later during scrutiny.
              </div>
            </div>

            <!-- WIZARD ACTIONS BAR -->
            <div class="s7-actions d-flex justify-content-between align-items-center pt-3 border-top lz-actions">
              <a href="{{ route('step6') }}" class="btn s7-btn-back lz-btn lz-btn-ghost">
                <i class="bi bi-arrow-left me-2"></i>Back to Handlers
              </a>
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn s7-btn-draft lz-btn lz-btn-outline" id="btn_save_draft_exit">
                  <i class="bi bi-floppy me-2"></i>Save Draft &amp; Exit
                </button>
                <button type="submit" class="btn s7-btn-continue lz-btn lz-btn-primary" id="btn_submit_step7">
                  Continue to Review <i class="bi bi-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- SCOPED CSS FOR STEP 7: ZERO CSS LEAKAGE, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step7-page)
     ========================================================================== */
  #gtms-step7-page {
    --s7-primary: #0F1E4D;
    --s7-primary-hover: #162a6b;
    --s7-surface: #ffffff;
    --s7-surface-subtle: #f8fafc;
    --s7-border: #e2e8f0;
    --s7-border-focus: #0F1E4D;
    --s7-text-dark: #0f172a;
    --s7-text-muted: #64748b;
    --s7-navy-text: #0F1E4D;
    --s7-emerald: #10b981;
    --s7-amber: #f59e0b;
    --s7-rose: #ef4444;
  }

  /* Wizard Wrap & Card */
  #gtms-step7-page .wizard-wrap {
    max-width: 1040px;
    margin: 0 auto;
  }

/* Stepper Bar */

/* Header Eyebrow & Badges */

/* Readiness Banner: Executive Light Palette (Matches Step 6) */

/* Financial 3-Bento KPI Cards */

#gtms-step7-page .text-navy {
    color: var(--s7-primary) !important;
  }

/* Form Section: Integrated Container with Perfect Padding */

/* Single-Border Input Group (Prevents Focus Split Defect) */

/* Quick Presets Buttons */

/* Select Box */

/* Feedback Box (Aligned with Select Height) */

/* Pro Tip Box */

/* Actions Bar & Buttons */

/* Back Button: Clean Slate Border (Eliminates pink theme artifact) */

/* Draft Button */

/* Continue Button */

/* ==========================================================================
     DARK MODE PARITY (Strictly Scoped to body[data-theme-version="dark"] #gtms-step7-page)
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step7-page {
    --s7-surface: #1e293b;
    --s7-surface-subtle: #0f172a;
    --s7-border: #334155;
    --s7-border-focus: #38bdf8;
    --s7-text-dark: #f8fafc;
    --s7-text-muted: #94a3b8;
    --s7-navy-text: #f1f5f9;
  }

/* Dark Stepper */

/* Dark Header */

/* Dark Banner */

/* Dark KPI Cards */

body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-contract .text-navy {
    color: #38bdf8 !important;
  }

/* Dark Form Section */

/* Dark Input Group */

/* Dark Preset Buttons */

/* Dark Select Box */

/* Dark Tip Box */

/* Dark Actions & Buttons */


  /* ==========================================================================
     STEP 7 — PAYMENT (redesign). Shared pieces (card, head, inputs, buttons)
     come from gtms-lease.css; only payment-specific parts live here.
     ========================================================================== */
  #gtms-step7-page {
    --s7-green: #15803D;
    --s7-green-bg: #F0FDF4;
    --s7-green-bd: #BBF7D0;
    --s7-amber: #B45309;
    --s7-amber-bg: #FFFBEB;
    --s7-amber-bd: #FDE68A;
    --s7-red: #B91C1C;
    --s7-red-bg: #FEF2F2;
    --s7-red-bd: #FECACA;
    --s7-navy: #0F1E4D;
    --s7-navy-bg: #EEF2FF;
  }
  body[data-theme-version="dark"] #gtms-step7-page {
    --s7-green: #4ADE80;  --s7-green-bg: rgba(34,197,94,.10);  --s7-green-bd: rgba(34,197,94,.30);
    --s7-amber: #FBBF24;  --s7-amber-bg: rgba(245,158,11,.10); --s7-amber-bd: rgba(245,158,11,.30);
    --s7-red:   #F87171;  --s7-red-bg:   rgba(239,68,68,.10);  --s7-red-bd:   rgba(239,68,68,.30);
    --s7-navy:  #93C5FD;  --s7-navy-bg:  rgba(59,130,246,.12);
  }

  /* Status banner */
  #gtms-step7-page .s7-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    margin-bottom: 20px;
    border-radius: 12px;
    background: var(--lz-surface-2);
    border: 1px solid var(--lz-border);
  }
  #gtms-step7-page .s7-banner-icon {
    width: 42px; height: 42px; flex-shrink: 0;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.15rem;
    background: var(--s7-navy-bg); color: var(--s7-navy);
  }
  #gtms-step7-page .s7-banner-text { flex: 1; min-width: 0; }
  #gtms-step7-page .s7-banner-title { font-weight: 700; font-size: 0.92rem; color: var(--lz-text); }
  #gtms-step7-page .s7-banner-desc { font-size: 0.8rem; color: var(--lz-text-3); margin-top: 2px; }
  #gtms-step7-page .s7-status-pill {
    display: inline-flex; align-items: center; gap: 6px;
    flex-shrink: 0;
    font-size: 0.78rem; font-weight: 700;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid transparent;
    white-space: nowrap;
    transition: background-color .2s ease, color .2s ease, border-color .2s ease;
  }
  #gtms-step7-page .s7-banner[data-status="pending"] .s7-status-pill { background: var(--s7-red-bg);   color: var(--s7-red);   border-color: var(--s7-red-bd); }
  #gtms-step7-page .s7-banner[data-status="partial"] .s7-status-pill { background: var(--s7-amber-bg); color: var(--s7-amber); border-color: var(--s7-amber-bd); }
  #gtms-step7-page .s7-banner[data-status="paid"]    .s7-status-pill { background: var(--s7-green-bg); color: var(--s7-green); border-color: var(--s7-green-bd); }
  @media (max-width: 575.98px) {
    #gtms-step7-page .s7-banner { flex-wrap: wrap; }
    #gtms-step7-page .s7-status-pill { margin-left: 56px; }
  }

  /* KPI cards: identical structure, white surface, coloured top edge */
  #gtms-step7-page .s7-kpi-row { margin-bottom: 20px; }
  #gtms-step7-page .s7-kpi {
    --kc: var(--s7-navy); --kbg: var(--s7-navy-bg);
    height: 100%;
    padding: 16px 18px;
    border-radius: 12px;
    background: var(--lz-surface);
    border: 1px solid var(--lz-border);
    border-top: 3px solid var(--kc);
    transition: transform .2s ease, box-shadow .2s ease;
  }
  #gtms-step7-page .s7-kpi:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,30,77,.08); }
  #gtms-step7-page .s7-kpi-paid    { --kc: var(--s7-green); --kbg: var(--s7-green-bg); }
  #gtms-step7-page .s7-kpi-pending { --kc: var(--s7-red);   --kbg: var(--s7-red-bg); }
  #gtms-step7-page .s7-kpi-head { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
  #gtms-step7-page .s7-kpi-icon {
    width: 32px; height: 32px; flex-shrink: 0;
    border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.95rem;
    background: var(--kbg); color: var(--kc);
  }
  #gtms-step7-page .s7-kpi-label {
    font-size: 0.72rem; font-weight: 700; letter-spacing: .05em; text-transform: uppercase;
    color: var(--lz-text-3);
  }
  #gtms-step7-page .s7-kpi-value {
    font-size: 1.55rem; font-weight: 800; line-height: 1.15; letter-spacing: -.01em;
    color: var(--kc);
    font-variant-numeric: tabular-nums;
    transition: color .2s ease;
  }
  #gtms-step7-page .s7-kpi-contract .s7-kpi-value { color: var(--lz-text); }
  #gtms-step7-page .s7-kpi-sub { font-size: 0.78rem; color: var(--lz-text-3); margin-top: 4px; }
  #gtms-step7-page .s7-kpi-value.s7-bump { animation: s7Bump .35s ease; }
  @keyframes s7Bump { 40% { transform: scale(1.04); } }

  /* Section header */
  #gtms-step7-page .s7-section-head { justify-content: space-between; }
  #gtms-step7-page .s7-section-title { display: inline-flex; align-items: center; gap: 8px; }
  #gtms-step7-page .s7-section-title i { color: var(--lz-navy); }
  #gtms-step7-page .s7-tag {
    font-size: 0.7rem; font-weight: 600;
    color: var(--lz-text-2);
    background: var(--lz-surface-2);
    border: 1px solid var(--lz-border);
    border-radius: 999px;
    padding: 3px 10px;
    white-space: nowrap;
  }

  /* Amount inputs */
  #gtms-step7-page .s7-amount { font-weight: 700; font-variant-numeric: tabular-nums; }
  #gtms-step7-page .s7-amount-green { color: var(--s7-green) !important; }
  #gtms-step7-page .s7-amount-red { color: var(--s7-red) !important; }
  #gtms-step7-page .lz-group .s7-addon-navy  { color: var(--s7-navy);  background: var(--s7-navy-bg); }
  #gtms-step7-page .lz-group .s7-addon-green { color: var(--s7-green); background: var(--s7-green-bg); }
  #gtms-step7-page .lz-group .s7-addon-red   { color: var(--s7-red);   background: var(--s7-red-bg); }
  #gtms-step7-page .s7-readonly .form-control { background: var(--lz-surface-2) !important; cursor: default; }
  #gtms-step7-page .s7-readonly:focus-within { box-shadow: none; transform: none; }
  #gtms-step7-page .s7-auto {
    font-size: 0.66rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase;
    color: var(--lz-text-3);
    background: var(--lz-surface-2);
    border: 1px solid var(--lz-border);
    border-radius: 999px;
    padding: 0 7px;
    line-height: 1.5;
  }
  #gtms-step7-page .lz-row .lz-label { min-height: 20px; }
  /* hide number spinners: amounts are typed, presets fill them */
  #gtms-step7-page .s7-amount::-webkit-outer-spin-button,
  #gtms-step7-page .s7-amount::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
  #gtms-step7-page .s7-amount { -moz-appearance: textfield; }

  /* Quick presets: one segmented control, never wraps */
  #gtms-step7-page .s7-presets {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-top: 8px;
  }
  #gtms-step7-page .s7-preset {
    min-height: 34px;
    padding: 0 8px;
    font-size: 0.78rem; font-weight: 600;
    white-space: nowrap;
    color: var(--lz-text-2);
    background: var(--lz-surface);
    border: 1.5px solid var(--lz-border-strong);
    border-radius: 8px;
    cursor: pointer;
    transition: background-color .15s ease, color .15s ease, border-color .15s ease, transform .15s ease;
  }
  #gtms-step7-page .s7-preset:hover { border-color: var(--s7-green); color: var(--s7-green); background: var(--s7-green-bg); transform: translateY(-1px); }
  #gtms-step7-page .s7-preset:active { transform: scale(.96); }
  #gtms-step7-page .s7-preset:focus-visible { outline: 2px solid #2563EB; outline-offset: 2px; }
  #gtms-step7-page #btn_quick_zero:hover { border-color: var(--s7-red); color: var(--s7-red); background: var(--s7-red-bg); }

  #gtms-step7-page .s7-divider { height: 1px; background: var(--lz-border); margin: 20px 0 18px; }

  /* Status select */
  #gtms-step7-page .s7-status-select { font-weight: 600; color: var(--lz-text); }

  /* Live assessment: same 44px height as the select so the row lines up */
  #gtms-step7-page .s7-feedback {
    --fc: var(--s7-red); --fbg: var(--s7-red-bg); --fbd: var(--s7-red-bd);
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 44px;
    padding: 6px 8px 6px 10px;
    border-radius: 10px;
    background: var(--fbg);
    border: 1.5px solid var(--fbd);
    transition: background-color .25s ease, border-color .25s ease;
  }
  #gtms-step7-page .s7-feedback[data-status="partial"] { --fc: var(--s7-amber); --fbg: var(--s7-amber-bg); --fbd: var(--s7-amber-bd); }
  #gtms-step7-page .s7-feedback[data-status="paid"]    { --fc: var(--s7-green); --fbg: var(--s7-green-bg); --fbd: var(--s7-green-bd); }
  #gtms-step7-page .s7-feedback-icon { color: var(--fc); font-size: 1.1rem; line-height: 1; flex-shrink: 0; }
  #gtms-step7-page .s7-feedback-icon i { display: block; animation: s7Pop .3s ease; }
  @keyframes s7Pop { from { transform: scale(.6); opacity: 0; } to { transform: none; opacity: 1; } }
  #gtms-step7-page .s7-feedback-title { flex: 1; min-width: 0; font-size: 0.88rem; font-weight: 700; color: var(--lz-text); }
  #gtms-step7-page .s7-feedback-badge {
    flex-shrink: 0;
    font-size: 0.72rem; font-weight: 700; letter-spacing: .03em; text-transform: uppercase;
    color: #fff;
    background: var(--fc);
    border-radius: 999px;
    padding: 4px 10px;
  }
  body[data-theme-version="dark"] #gtms-step7-page .s7-feedback-badge { color: #0B1120; }

  /* Pro tip */
  #gtms-step7-page .s7-tip {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-top: 20px;
    padding: 14px 16px;
    border-radius: 12px;
    background: var(--s7-amber-bg);
    border: 1px solid var(--s7-amber-bd);
    border-left: 4px solid #F59E0B;
    font-size: 0.84rem;
    line-height: 1.55;
    color: var(--lz-text-2);
  }
  #gtms-step7-page .s7-tip strong { color: var(--lz-text); }
  #gtms-step7-page .s7-tip-icon { color: #D97706; font-size: 1.1rem; line-height: 1.4; flex-shrink: 0; }

  @media (prefers-reduced-motion: reduce) {
    #gtms-step7-page .s7-kpi-value.s7-bump,
    #gtms-step7-page .s7-feedback-icon i { animation: none; }
  }

  /* ---- Free (fee waived) ---- */
  #gtms-step7-page { --s7-violet: #6D28D9; --s7-violet-bg: #F5F3FF; --s7-violet-bd: #DDD6FE; }
  body[data-theme-version="dark"] #gtms-step7-page { --s7-violet: #C4B5FD; --s7-violet-bg: rgba(139,92,246,.12); --s7-violet-bd: rgba(139,92,246,.35); }
  #gtms-step7-page .s7-banner[data-status="free"] .s7-status-pill { background: var(--s7-violet-bg); color: var(--s7-violet); border-color: var(--s7-violet-bd); }
  #gtms-step7-page .s7-feedback[data-status="free"] { --fc: var(--s7-violet); --fbg: var(--s7-violet-bg); --fbd: var(--s7-violet-bd); }
  #gtms-step7-page .s7-preset:disabled { opacity: .45; cursor: not-allowed; transform: none; background: var(--lz-surface-2); color: var(--lz-text-3); border-color: var(--lz-border); }

  #gtms-step7-page .s7-waiver {
    margin-top: 18px;
    padding: 16px;
    border-radius: 12px;
    background: var(--s7-violet-bg);
    border: 1.5px dashed var(--s7-violet-bd);
    animation: s7Reveal .25s ease both;
  }
  #gtms-step7-page .s7-waiver[hidden] { display: none; }
  @keyframes s7Reveal { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }
  #gtms-step7-page .s7-waiver-head { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
  #gtms-step7-page .s7-waiver-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    background: var(--s7-violet); color: #fff; font-size: 1rem;
  }
  #gtms-step7-page .s7-waiver-title { font-weight: 700; color: var(--s7-violet); font-size: 0.92rem; }
  #gtms-step7-page .s7-waiver-sub { font-size: 0.8rem; color: var(--lz-text-2); }
  #gtms-step7-page .s7-waiver textarea { background: var(--lz-surface); }
  #gtms-step7-page .s7-waiver textarea:focus { border-color: var(--s7-violet); box-shadow: 0 0 0 3px rgba(109,40,217,.15); }
  #gtms-step7-page .s7-waiver-foot { display: flex; justify-content: space-between; gap: 8px; flex-wrap: wrap; margin-top: 6px; }
  #gtms-step7-page .s7-waiver-foot i { color: var(--s7-violet); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const inputProduct = document.getElementById('field_product_value');
  const inputPaid = document.getElementById('field_paid_amount');
  const inputPending = document.getElementById('field_pending_amount');
  const selectStatus = document.getElementById('field_payment_status');

  const cardProduct = document.getElementById('card_disp_product_value');
  const cardPaid = document.getElementById('card_disp_paid_amount');
  const cardPending = document.getElementById('card_disp_pending_amount');

  const feedbackText = document.getElementById('status_feedback_text');
  const feedbackBadge = document.getElementById('status_feedback_badge');
  const feedbackBox = document.getElementById('status_feedback_box');
  const feedbackIcon = document.getElementById('status_feedback_icon');
  const bannerStatusPill = document.getElementById('banner_status_pill');

  const form = document.getElementById('payment_form');
  const btnSaveExit = document.getElementById('btn_save_draft_exit');
  const btnSubmit = document.getElementById('btn_submit_step7');

  const btnQuickFull = document.getElementById('btn_quick_full');
  const btnQuickHalf = document.getElementById('btn_quick_half');
  const btnQuickZero = document.getElementById('btn_quick_zero');

  function formatCurrency(val) {
    return '₹ ' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  const waiverBox = document.getElementById('s7_waiver');
  const waiverReason = document.getElementById('field_waiver_reason');
  const waiverCount = document.getElementById('waiver_count');
  const contractLabel = document.getElementById('kpi_contract_label');
  const presetBtns = [btnQuickFull, btnQuickHalf, btnQuickZero].filter(Boolean);

  // Free (fee waived): paid locked at 0, nothing pending, reason required
  function applyFreeMode() {
    const isFree = selectStatus.value === 'free';
    if (waiverBox) waiverBox.hidden = !isFree;
    if (waiverReason) waiverReason.required = isFree;
    inputPaid.readOnly = isFree;            // readonly (not disabled) so it is still submitted
    inputPaid.closest('.lz-group').classList.toggle('s7-readonly', isFree);
    if (isFree) inputPaid.value = '0.00';
    presetBtns.forEach(b => { b.disabled = isFree; });
    if (contractLabel) contractLabel.textContent = isFree ? 'Quoted · Waived' : 'Contract Value';
    if (isFree && waiverReason && !waiverReason.value.trim()) {
      setTimeout(() => waiverReason.focus(), 50);
    }
  }

  function updateWaiverCount() {
    if (waiverReason && waiverCount) waiverCount.textContent = waiverReason.value.length + ' / 1000';
  }

  function calculatePayment(isManualStatusChange = false) {
    const isFree = selectStatus.value === 'free';
    const productVal = parseFloat(inputProduct.value) || 0;
    const paidVal = isFree ? 0 : (parseFloat(inputPaid.value) || 0);
    const pendingVal = isFree ? 0 : Math.max(0, productVal - paidVal);

    inputPending.value = pendingVal.toFixed(2);

    // Update KPI values; briefly "bump" the ones that changed
    [[cardProduct, productVal], [cardPaid, paidVal], [cardPending, pendingVal]].forEach(function (pair) {
      const el = pair[0], txt = formatCurrency(pair[1]);
      if (el.textContent !== txt) {
        el.textContent = txt;
        el.classList.remove('s7-bump'); void el.offsetWidth; el.classList.add('s7-bump');
      }
    });

    // Amount edits never switch a waived application back to a paid status
    if (!isManualStatusChange && !isFree) {
      if (paidVal <= 0 || productVal <= 0) {
        selectStatus.value = 'pending';
      } else if (pendingVal <= 0 && productVal > 0) {
        selectStatus.value = 'paid';
      } else {
        selectStatus.value = 'partial';
      }
    }

    updateStatusUI();
  }

  function updateStatusUI() {
    const currentStatus = selectStatus.value;
    // Colours live in CSS ([data-status] on the box and banner); JS only swaps text + icon
    const STATUS = {
      paid:    { text: 'Account fully settled',    badge: 'Paid',    icon: 'bi-check-circle-fill',       pill: 'Fully Paid' },
      partial: { text: 'Part payment received',    badge: 'Partial', icon: 'bi-clock-history',           pill: 'Part Payment' },
      pending: { text: 'Full payment outstanding', badge: 'Pending', icon: 'bi-exclamation-circle-fill', pill: 'Pending Payment' },
      free:    { text: 'Fee waived — no charge',   badge: 'Free',    icon: 'bi-gift-fill',               pill: 'Free (Waived)' }
    };
    const s = STATUS[currentStatus] || STATUS.pending;

    feedbackText.textContent = s.text;
    feedbackBadge.textContent = s.badge;
    feedbackBox.setAttribute('data-status', currentStatus);
    feedbackBox.removeAttribute('style');
    if (feedbackIcon) feedbackIcon.innerHTML = '<i class="bi ' + s.icon + '"></i>';
    if (bannerStatusPill) bannerStatusPill.innerHTML = '<i class="bi ' + s.icon + '"></i> ' + s.pill;
    const banner = document.getElementById('s7_banner');
    if (banner) banner.setAttribute('data-status', currentStatus);
  }

  // Quick Preset Actions
  if (btnQuickFull) {
    btnQuickFull.addEventListener('click', function () {
      const productVal = parseFloat(inputProduct.value) || 0;
      inputPaid.value = productVal > 0 ? productVal.toFixed(2) : '0.00';
      calculatePayment(false);
    });
  }

  if (btnQuickHalf) {
    btnQuickHalf.addEventListener('click', function () {
      const productVal = parseFloat(inputProduct.value) || 0;
      inputPaid.value = productVal > 0 ? (productVal / 2).toFixed(2) : '0.00';
      calculatePayment(false);
    });
  }

  if (btnQuickZero) {
    btnQuickZero.addEventListener('click', function () {
      inputPaid.value = '0.00';
      calculatePayment(false);
    });
  }

  inputProduct.addEventListener('input', () => calculatePayment(false));
  inputPaid.addEventListener('input', () => calculatePayment(false));
  selectStatus.addEventListener('change', () => {
    const leavingFree = selectStatus.dataset.prev === 'free' && selectStatus.value !== 'free';
    selectStatus.dataset.prev = selectStatus.value;
    applyFreeMode();
    // Leaving Free: let the amounts decide the status again.
    // Otherwise keep the status the user picked (manual override).
    calculatePayment(!leavingFree);
  });
  if (waiverReason) {
    waiverReason.addEventListener('input', () => {
      updateWaiverCount();
      if (waiverReason.value.trim().length >= 5) setFieldError(waiverReason, '');
    });
  }

  function setFieldError(el, msg) {
    const box = form.querySelector('.' + el.name + '_error');
    if (box) box.textContent = msg;
    el.classList.toggle('is-invalid', !!msg);
  }

  // Run initial calculation
  selectStatus.dataset.prev = selectStatus.value;
  applyFreeMode();
  updateWaiverCount();
  calculatePayment(selectStatus.value === 'free');

  // AJAX Submission Helper
  function submitStep7(isExit = false) {
    // Waiver reason is required for Free; show the message under the field
    if (selectStatus.value === 'free' && waiverReason && waiverReason.value.trim().length < 5) {
      setFieldError(waiverReason, 'Please enter the reason for waiving the fee (at least 5 characters).');
      waiverReason.focus();
      waiverReason.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }
    if (window.GTMSValidate) {
      let firstBad = null;
      [inputProduct, inputPaid].forEach(el => {
        const bad = el.value.trim() === '' || isNaN(parseFloat(el.value)) || parseFloat(el.value) < 0;
        el.classList.toggle('is-invalid', bad);
        if (bad && !firstBad) firstBad = el;
      });
      if (firstBad) { firstBad.focus(); return; }
    } else if (!form.reportValidity()) {
      return;
    }

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Saving...`;

    const formData = new FormData(form);
    if (isExit) {
      formData.append('exit', '1');
    }

    fetch("{{ route('step7.save') }}", {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 1) {
        toastr.success(data.message || 'Payment details saved successfully!');
        window.location.href = data.redirect || "{{ route('step8') }}";
      } else if (data.errors && window.GTMSValidate && GTMSValidate.showErrors(form, data.errors)) {
        // field errors are shown under each field
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = `Continue to Review <i class="bi bi-arrow-right ms-2"></i>`;
      } else {
        toastr.error(data.message || 'Error saving payment information.');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = `Continue to Review <i class="bi bi-arrow-right ms-2"></i>`;
      }
    })
    .catch(err => {
      console.error(err);
      toastr.error('Network or server error while saving data.');
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = `Continue to Review <i class="bi bi-arrow-right ms-2"></i>`;
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitStep7(false);
  });

  btnSaveExit.addEventListener('click', function () {
    submitStep7(true);
  });
});
</script>
@endsection
