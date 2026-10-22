@extends('layouts.app')
@section('title', 'Payment Details - Step 7')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step7-page">
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

        <div class="card s7-main-card wizard-card">
          <!-- CARD HEADER -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
              <div class="s7-eyebrow text-uppercase fw-bold text-primary mb-1">Step 7 of 8 &bull; Financial Settlement &amp; Billing Ledger</div>
              <h4 class="s7-title mb-1.5">Payment &amp; Financial Settlement</h4>
              <div class="s7-subtitle">Record the overall application value, advance payments received, balance due, and statutory settlement status.</div>
            </div>
            <span class="badge s7-ledger-pill">
              <i class="bi bi-cash-stack me-1.5"></i>Billing Ledger
            </span>
          </div>

          @php
            $pv = (float)($payment['product_value'] ?? 0);
            $pa = (float)($payment['paid_amount'] ?? 0);
            $pe = max(0, $pv - $pa);
            $st = $payment['payment_status'] ?? ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial'));
          @endphp

          <!-- SUMMARY / LEDGER READINESS BANNER -->
          <div class="s7-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s7-banner-icon-badge">
                  <i class="bi bi-credit-card-2-front-fill"></i>
                </div>
                <div>
                  <div class="s7-banner-title fw-bold">Statutory Ledger &amp; Fee Calculation</div>
                  <div class="s7-banner-desc">Real-time fee computation, balance due tracking, and transaction status recording for regulatory compliance.</div>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge s7-banner-status-pill" id="banner_status_pill">
                  @if($st === 'paid')
                    <i class="bi bi-check-circle-fill me-1.5"></i> Fully Paid
                  @elseif($st === 'partial')
                    <i class="bi bi-clock-history me-1.5"></i> Part Payment
                  @else
                    <i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment
                  @endif
                </span>
              </div>
            </div>
          </div>

          <!-- REAL-TIME FINANCIAL 3-BENTO KPI METRIC CARDS -->
          <div class="row g-3 mb-4">
            <!-- Metric 1: Product / Contract Value -->
            <div class="col-md-4">
              <div class="s7-kpi-card s7-kpi-contract">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s7-kpi-label">Product / Contract Value</span>
                  <div class="s7-kpi-icon-badge s7-kpi-icon-contract">
                    <i class="bi bi-receipt-cutoff"></i>
                  </div>
                </div>
                <div class="s7-kpi-value text-navy" id="card_disp_product_value">₹ {{ number_format($pv, 2) }}</div>
                <div class="s7-kpi-subtext">Total quoted service value</div>
              </div>
            </div>

            <!-- Metric 2: Paid Amount -->
            <div class="col-md-4">
              <div class="s7-kpi-card s7-kpi-paid">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s7-kpi-label text-success">Advance / Paid Amount</span>
                  <div class="s7-kpi-icon-badge s7-kpi-icon-paid">
                    <i class="bi bi-check-circle-fill"></i>
                  </div>
                </div>
                <div class="s7-kpi-value text-success" id="card_disp_paid_amount">₹ {{ number_format($pa, 2) }}</div>
                <div class="s7-kpi-subtext">Received via Cash / Bank / NEFT</div>
              </div>
            </div>

            <!-- Metric 3: Pending Balance Due -->
            <div class="col-md-4">
              <div class="s7-kpi-card s7-kpi-pending">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s7-kpi-label text-danger">Pending Balance Due</span>
                  <div class="s7-kpi-icon-badge s7-kpi-icon-pending">
                    <i class="bi bi-hourglass-split"></i>
                  </div>
                </div>
                <div class="s7-kpi-value text-danger" id="card_disp_pending_amount">₹ {{ number_format($pe, 2) }}</div>
                <div class="s7-kpi-subtext">Remaining balance to collect</div>
              </div>
            </div>
          </div>

          <!-- PAYMENT FORM -->
          <form id="payment_form" method="POST" action="{{ route('step7.save') }}">
            @csrf

            <!-- FORM SECTION CARD -->
            <div class="s7-form-section mb-4">
              <div class="s7-form-section-header d-flex align-items-center justify-content-between">
                <span class="s7-form-section-title">
                  <i class="bi bi-currency-rupee me-1.5 text-primary"></i>Fee Entry &amp; Settlement Assessment
                </span>
                <span class="badge s7-badge-status-tag">Statutory Accounting</span>
              </div>

              <div class="s7-form-section-body">
                <!-- ROW 1: AMOUNTS -->
                <div class="row g-4 mb-4">
                  <!-- Product Value -->
                  <div class="col-md-4">
                    <label class="form-label s7-label mb-2">
                      Product / Service Value (₹) <span class="text-danger">*</span>
                    </label>
                    <div class="s7-input-group">
                      <span class="s7-input-addon s7-addon-navy">
                        <i class="bi bi-currency-rupee"></i>
                      </span>
                      <input type="number" step="0.01" min="0" name="product_value" id="field_product_value"
                             class="form-control s7-input fw-bold" placeholder="0.00" value="{{ $pv > 0 ? $pv : '' }}" required>
                    </div>
                    <div class="s7-field-hint mt-2">Enter the total agreed fee or quotation.</div>
                  </div>

                  <!-- Paid Amount -->
                  <div class="col-md-4">
                    <label class="form-label s7-label mb-2">
                      Paid Amount (₹) <span class="text-danger">*</span>
                    </label>
                    <div class="s7-input-group">
                      <span class="s7-input-addon s7-addon-success">
                        <i class="bi bi-currency-rupee"></i>
                      </span>
                      <input type="number" step="0.01" min="0" name="paid_amount" id="field_paid_amount"
                             class="form-control s7-input fw-bold text-success" placeholder="0.00" value="{{ $pa > 0 ? $pa : '' }}" required>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2 flex-wrap gap-1">
                      <span class="s7-field-hint">Amount paid by applicant.</span>
                      <div class="s7-quick-presets d-flex align-items-center gap-2">
                        <button type="button" class="btn s7-preset-btn" id="btn_quick_full" title="Set paid equal to product value">100% Full</button>
                        <button type="button" class="btn s7-preset-btn" id="btn_quick_half" title="Set paid to 50% advance">50% Advance</button>
                        <button type="button" class="btn s7-preset-btn" id="btn_quick_zero" title="Set paid to 0">Clear (₹0)</button>
                      </div>
                    </div>
                  </div>

                  <!-- Pending Amount (Auto-Calculated) -->
                  <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                      <label class="form-label s7-label mb-0">Pending Balance (₹)</label>
                      <span class="badge s7-auto-tag">Auto-Calculated</span>
                    </div>
                    <div class="s7-input-group s7-group-readonly">
                      <span class="s7-input-addon s7-addon-danger">
                        <i class="bi bi-currency-rupee"></i>
                      </span>
                      <input type="number" step="0.01" name="pending_amount_display" id="field_pending_amount"
                             class="form-control s7-input s7-input-readonly fw-bold text-danger" placeholder="0.00" value="{{ $pe }}" readonly>
                    </div>
                    <div class="s7-field-hint mt-2">Calculated as Contract Value minus Paid.</div>
                  </div>
                </div>

                <!-- ROW 2: STATUS & ASSESSMENT (PERFECT VERTICAL ALIGNMENT) -->
                <div class="row g-4 align-items-center">
                  <!-- Payment Status Selection -->
                  <div class="col-md-6">
                    <label class="form-label s7-label mb-2">
                      Payment Settlement Status <span class="text-danger">*</span>
                    </label>
                    <div class="s7-select-wrap">
                      <select name="payment_status" id="field_payment_status" class="form-select s7-select fw-semibold" required>
                        <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>🔴 Pending (Full Balance Due)</option>
                        <option value="partial" {{ $st === 'partial' ? 'selected' : '' }}>🟡 Partial (Part Payment Received)</option>
                        <option value="paid" {{ $st === 'paid' ? 'selected' : '' }}>🟢 Paid (Fully Cleared)</option>
                      </select>
                    </div>
                    <div class="s7-field-hint mt-2">Status adapts automatically based on amounts or can be overridden.</div>
                  </div>

                  <!-- Real-Time Status Assessment Feedback Box -->
                  <div class="col-md-6">
                    <div class="s7-assessment-wrapper">
                      <div class="s7-feedback-box d-flex align-items-center justify-content-between" id="status_feedback_box">
                        <div class="d-flex align-items-center gap-3">
                          <div class="s7-feedback-icon" id="status_feedback_icon">
                            <i class="bi bi-clock-history"></i>
                          </div>
                          <div>
                            <div class="s7-feedback-sub">Settlement Assessment</div>
                            <div class="s7-feedback-title fw-bold" id="status_feedback_text">Pending Payment</div>
                          </div>
                        </div>
                        <span class="badge s7-feedback-badge px-3 py-2 rounded-pill" id="status_feedback_badge">
                          Pending
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <!-- PRO TIP CALLOUT BOX -->
            <div class="s7-tip-box d-flex align-items-center gap-3 mb-4">
              <div class="s7-tip-icon flex-shrink-0">
                <i class="bi bi-lightbulb-fill"></i>
              </div>
              <div class="s7-tip-content">
                <strong>Pro Tip:</strong> All financial entries recorded here are automatically reflected in <strong>Chapter VI (Financial &amp; Billing Ledger)</strong> of the official Executive Dossier and statutory reports. You can collect advance fees now and record progressive milestone payments later during scrutiny.
              </div>
            </div>

            <!-- WIZARD ACTIONS BAR -->
            <div class="s7-actions d-flex justify-content-between align-items-center pt-3 border-top">
              <a href="{{ route('step6') }}" class="btn s7-btn-back">
                <i class="bi bi-arrow-left me-2"></i>Back to Handlers
              </a>
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn s7-btn-draft" id="btn_save_draft_exit">
                  <i class="bi bi-floppy me-2"></i>Save Draft &amp; Exit
                </button>
                <button type="submit" class="btn s7-btn-continue" id="btn_submit_step7">
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

  #gtms-step7-page .s7-main-card {
    background: var(--s7-surface);
    border: 1px solid var(--s7-border);
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
  }

  /* Stepper Bar */
  #gtms-step7-page .step-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 28px;
    padding: 0 12px;
  }

  #gtms-step7-page .step-progress::before {
    content: "";
    position: absolute;
    top: 18px;
    left: 36px;
    right: 36px;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
  }

  #gtms-step7-page .sp-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: transparent;
  }

  #gtms-step7-page .sp-step .circ {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 700;
    background: #ffffff;
    border: 2px solid #cbd5e1;
    color: #64748b;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .sp-step.done .circ {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    font-size: 15px;
  }

  #gtms-step7-page .sp-step.active .circ {
    background: var(--s7-primary);
    border-color: var(--s7-primary);
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
  }

  #gtms-step7-page .sp-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
  }

  #gtms-step7-page .sp-step.active .sp-label {
    color: var(--s7-primary);
    font-weight: 700;
  }

  #gtms-step7-page .sp-step.done .sp-label {
    color: #10b981;
    font-weight: 600;
  }

  /* Header Eyebrow & Badges */
  #gtms-step7-page .s7-eyebrow {
    font-size: 11.5px;
    letter-spacing: 0.08em;
    color: #0284c7 !important;
  }

  #gtms-step7-page .s7-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--s7-navy-text);
  }

  #gtms-step7-page .s7-subtitle {
    font-size: 13px;
    color: var(--s7-text-muted);
    line-height: 1.45;
  }

  #gtms-step7-page .s7-ledger-pill {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    font-weight: 700;
    font-size: 12px;
    padding: 7px 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
  }

  /* Readiness Banner: Executive Light Palette (Matches Step 6) */
  #gtms-step7-page .s7-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
  }

  #gtms-step7-page .s7-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #e0e7ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  #gtms-step7-page .s7-banner-title {
    font-size: 14.5px;
    color: #0F1E4D;
    margin-bottom: 2px;
  }

  #gtms-step7-page .s7-banner-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
  }

  #gtms-step7-page .s7-banner-status-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
  }

  /* Financial 3-Bento KPI Cards */
  #gtms-step7-page .s7-kpi-card {
    border-radius: 12px;
    padding: 18px 20px;
    border: 1px solid var(--s7-border);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-step7-page .s7-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px -2px rgba(15, 23, 42, 0.08);
  }

  #gtms-step7-page .s7-kpi-contract {
    background: #f8fafc;
    border-left: 4px solid var(--s7-primary) !important;
  }

  #gtms-step7-page .s7-kpi-paid {
    background: #f0fdf4;
    border-left: 4px solid #10b981 !important;
  }

  #gtms-step7-page .s7-kpi-pending {
    background: #fff7ed;
    border-left: 4px solid #f97316 !important;
  }

  #gtms-step7-page .s7-kpi-label {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--s7-text-muted);
  }

  #gtms-step7-page .s7-kpi-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  #gtms-step7-page .s7-kpi-icon-contract {
    background: #e2e8f0;
    color: var(--s7-primary);
  }

  #gtms-step7-page .s7-kpi-icon-paid {
    background: #dcfce7;
    color: #10b981;
  }

  #gtms-step7-page .s7-kpi-icon-pending {
    background: #ffedd5;
    color: #f97316;
  }

  #gtms-step7-page .s7-kpi-value {
    font-size: 22px;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 4px;
    font-variant-numeric: tabular-nums;
  }

  #gtms-step7-page .text-navy {
    color: var(--s7-primary) !important;
  }

  #gtms-step7-page .s7-kpi-subtext {
    font-size: 11.5px;
    color: var(--s7-text-muted);
  }

  /* Form Section: Integrated Container with Perfect Padding */
  #gtms-step7-page .s7-form-section {
    background: #ffffff;
    border: 1px solid var(--s7-border);
    border-radius: 12px;
    overflow: hidden;
  }

  #gtms-step7-page .s7-form-section-header {
    background: #f8fafc;
    border-bottom: 1px solid var(--s7-border);
    padding: 14px 20px;
  }

  #gtms-step7-page .s7-form-section-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--s7-navy-text);
  }

  #gtms-step7-page .s7-badge-status-tag {
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 11px;
    border-radius: 6px;
    padding: 4px 10px;
  }

  #gtms-step7-page .s7-form-section-body {
    padding: 24px;
  }

  #gtms-step7-page .s7-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--s7-navy-text);
  }

  #gtms-step7-page .s7-auto-tag {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    background: #e2e8f0;
    padding: 3px 8px;
    border-radius: 4px;
  }

  /* Single-Border Input Group (Prevents Focus Split Defect) */
  #gtms-step7-page .s7-input-group {
    display: flex;
    align-items: stretch;
    height: 44px;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    overflow: hidden;
    background: #ffffff;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-input-group:focus-within {
    border-color: var(--s7-border-focus);
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }

  #gtms-step7-page .s7-group-readonly {
    background: #f8fafc;
    border-color: #e2e8f0;
  }

  #gtms-step7-page .s7-group-readonly:focus-within {
    border-color: #cbd5e1;
    box-shadow: none;
  }

  #gtms-step7-page .s7-input-addon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    background: #f8fafc;
    border-right: 1.5px solid #cbd5e1;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
  }

  #gtms-step7-page .s7-addon-navy {
    color: var(--s7-primary);
  }

  #gtms-step7-page .s7-addon-success {
    color: #10b981;
    background: #f0fdf4;
  }

  #gtms-step7-page .s7-addon-danger {
    color: #ef4444;
    background: #fef2f2;
  }

  #gtms-step7-page .s7-input {
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
    padding: 8px 14px;
    font-size: 14.5px;
    font-weight: 700;
    color: var(--s7-text-dark);
    width: 100%;
    height: 100%;
  }

  #gtms-step7-page .s7-input:focus {
    outline: none !important;
  }

  #gtms-step7-page .s7-input-readonly {
    color: #dc2626 !important;
    cursor: default;
  }

  #gtms-step7-page .s7-field-hint {
    font-size: 11.5px;
    color: var(--s7-text-muted);
    line-height: 1.35;
  }

  /* Quick Presets Buttons */
  #gtms-step7-page .s7-preset-btn {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 9px;
    border-radius: 6px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    transition: all 0.15s ease;
  }

  #gtms-step7-page .s7-preset-btn:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
  }

  /* Select Box */
  #gtms-step7-page .s7-select {
    height: 44px;
    border: 1.5px solid #cbd5e1;
    border-radius: 9px;
    padding: 8px 14px;
    font-size: 13.5px;
    color: var(--s7-text-dark);
    background-color: #ffffff;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-select:focus {
    border-color: var(--s7-border-focus);
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }

  /* Feedback Box (Aligned with Select Height) */
  #gtms-step7-page .s7-assessment-wrapper {
    margin-top: 28px;
  }

  #gtms-step7-page .s7-feedback-box {
    height: 60px;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 10px;
    padding: 10px 18px;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-feedback-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
  }

  #gtms-step7-page .s7-feedback-sub {
    color: var(--s7-text-muted);
    font-size: 10.5px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 700;
  }

  #gtms-step7-page .s7-feedback-title {
    font-size: 13.5px;
    color: var(--s7-navy-text);
    line-height: 1.25;
  }

  #gtms-step7-page .s7-feedback-badge {
    font-size: 12px;
    font-weight: 700;
  }

  /* Pro Tip Box */
  #gtms-step7-page .s7-tip-box {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-left: 4px solid #f59e0b;
    border-radius: 10px;
    padding: 14px 18px;
  }

  #gtms-step7-page .s7-tip-icon {
    font-size: 20px;
    color: #d97706;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  #gtms-step7-page .s7-tip-content {
    font-size: 12.5px;
    line-height: 1.5;
    color: #78350f;
  }

  /* Actions Bar & Buttons */
  #gtms-step7-page .s7-actions {
    border-color: var(--s7-border) !important;
  }

  /* Back Button: Clean Slate Border (Eliminates pink theme artifact) */
  #gtms-step7-page .s7-btn-back {
    background: #ffffff;
    color: #334155;
    border: 1.5px solid #cbd5e1 !important;
    font-size: 13.5px;
    font-weight: 600;
    padding: 9px 20px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-btn-back:hover {
    background: #f1f5f9;
    border-color: #94a3b8 !important;
    color: #0f172a;
  }

  /* Draft Button */
  #gtms-step7-page .s7-btn-draft {
    background: #ffffff;
    color: var(--s7-primary);
    border: 1.5px solid #cbd5e1;
    font-size: 13.5px;
    font-weight: 600;
    padding: 9px 20px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-btn-draft:hover {
    background: #f8fafc;
    border-color: var(--s7-primary);
    color: var(--s7-primary-hover);
  }

  /* Continue Button */
  #gtms-step7-page .s7-btn-continue {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    border: 1px solid #0F1E4D;
    font-size: 13.5px;
    font-weight: 700;
    padding: 9px 24px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    transition: all 0.2s ease;
  }

  #gtms-step7-page .s7-btn-continue:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.3);
    transform: translateY(-1px);
  }

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

  body[data-theme-version="dark"] #gtms-step7-page .s7-main-card {
    background: var(--s7-surface);
    border-color: var(--s7-border);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
  }

  /* Dark Stepper */
  body[data-theme-version="dark"] #gtms-step7-page .step-progress::before {
    background: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-step .circ {
    background: #0f172a;
    border-color: #334155;
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-step.done .circ {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-step.active .circ {
    background: #38bdf8;
    border-color: #38bdf8;
    color: #0f172a;
    box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.25);
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-label {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-step.active .sp-label {
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .sp-step.done .sp-label {
    color: #34d399;
  }

  /* Dark Header */
  body[data-theme-version="dark"] #gtms-step7-page .s7-title {
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-subtitle {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-ledger-pill {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
    border-color: rgba(16, 185, 129, 0.3);
  }

  /* Dark Banner */
  body[data-theme-version="dark"] #gtms-step7-page .s7-banner-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-banner-title {
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-banner-desc {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-banner-status-pill {
    background: #38bdf8;
    color: #0f172a;
  }

  /* Dark KPI Cards */
  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-card {
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-contract {
    background: #0f172a;
    border-left: 4px solid #38bdf8 !important;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-contract .text-navy {
    color: #38bdf8 !important;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-icon-contract {
    background: #1e293b;
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-paid {
    background: #0f172a;
    border-left: 4px solid #10b981 !important;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-icon-paid {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-pending {
    background: #0f172a;
    border-left: 4px solid #f97316 !important;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-icon-pending {
    background: rgba(249, 115, 22, 0.15);
    color: #fb923c;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-label {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-kpi-subtext {
    color: #64748b;
  }

  /* Dark Form Section */
  body[data-theme-version="dark"] #gtms-step7-page .s7-form-section {
    background: #1e293b;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-form-section-header {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-form-section-title {
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-badge-status-tag {
    background: #334155;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-label {
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-auto-tag {
    background: #334155;
    color: #94a3b8;
  }

  /* Dark Input Group */
  body[data-theme-version="dark"] #gtms-step7-page .s7-input-group {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-input-group:focus-within {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-group-readonly {
    background: #1e293b;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-input-addon {
    background: #1e293b;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-addon-navy {
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-addon-success {
    color: #34d399;
    background: rgba(16, 185, 129, 0.15);
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-addon-danger {
    color: #f87171;
    background: rgba(239, 68, 68, 0.15);
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-input {
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-input-readonly {
    color: #f87171 !important;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-field-hint {
    color: #94a3b8;
  }

  /* Dark Preset Buttons */
  body[data-theme-version="dark"] #gtms-step7-page .s7-preset-btn {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-preset-btn:hover {
    background: #38bdf8;
    border-color: #38bdf8;
    color: #0f172a;
  }

  /* Dark Select Box */
  body[data-theme-version="dark"] #gtms-step7-page .s7-select {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-select:focus {
    border-color: #38bdf8;
    box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
  }

  /* Dark Tip Box */
  body[data-theme-version="dark"] #gtms-step7-page .s7-tip-box {
    background: #1e2238;
    border-color: #4338ca;
    border-left: 4px solid #818cf8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-tip-icon {
    color: #a5b4fc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-tip-content {
    color: #e0e7ff;
  }

  /* Dark Actions & Buttons */
  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-back {
    background: #0f172a;
    border-color: #334155 !important;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-back:hover {
    background: #1e293b;
    border-color: #64748b !important;
    color: #ffffff;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-draft {
    background: #0f172a;
    border-color: #334155;
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-draft:hover {
    background: #1e293b;
    border-color: #38bdf8;
    color: #7dd3fc;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-continue {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border-color: #3b82f6;
    color: #ffffff;
  }

  body[data-theme-version="dark"] #gtms-step7-page .s7-btn-continue:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
  }
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

  function calculatePayment(isManualStatusChange = false) {
    const productVal = parseFloat(inputProduct.value) || 0;
    const paidVal = parseFloat(inputPaid.value) || 0;
    const pendingVal = Math.max(0, productVal - paidVal);

    inputPending.value = pendingVal.toFixed(2);

    cardProduct.textContent = formatCurrency(productVal);
    cardPaid.textContent = formatCurrency(paidVal);
    cardPending.textContent = formatCurrency(pendingVal);

    if (!isManualStatusChange) {
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
    const isDark = document.body.getAttribute('data-theme-version') === 'dark';

    if (currentStatus === 'paid') {
      feedbackText.textContent = 'Account Fully Settled';
      feedbackBadge.textContent = 'Fully Paid';
      feedbackBadge.className = 'badge bg-success text-white px-3 py-2 rounded-pill fs-6';
      if (feedbackIcon) feedbackIcon.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i>';
      if (bannerStatusPill) bannerStatusPill.innerHTML = '<i class="bi bi-check-circle-fill me-1.5"></i> Fully Paid';

      if (isDark) {
        feedbackBox.style.background = 'rgba(16, 185, 129, 0.12)';
        feedbackBox.style.border = '1px solid rgba(16, 185, 129, 0.3)';
      } else {
        feedbackBox.style.background = '#ecfdf5';
        feedbackBox.style.border = '1px solid #a7f3d0';
      }
    } else if (currentStatus === 'partial') {
      feedbackText.textContent = 'Part Payment Received';
      feedbackBadge.textContent = 'Partial Payment';
      feedbackBadge.className = 'badge bg-warning text-dark px-3 py-2 rounded-pill fs-6';
      if (feedbackIcon) feedbackIcon.innerHTML = '<i class="bi bi-clock-history text-warning"></i>';
      if (bannerStatusPill) bannerStatusPill.innerHTML = '<i class="bi bi-clock-history me-1.5"></i> Part Payment';

      if (isDark) {
        feedbackBox.style.background = 'rgba(245, 158, 11, 0.12)';
        feedbackBox.style.border = '1px solid rgba(245, 158, 11, 0.3)';
      } else {
        feedbackBox.style.background = '#fffbeb';
        feedbackBox.style.border = '1px solid #fde68a';
      }
    } else {
      feedbackText.textContent = 'Full Payment Outstanding';
      feedbackBadge.textContent = 'Pending Payment';
      feedbackBadge.className = 'badge bg-danger text-white px-3 py-2 rounded-pill fs-6';
      if (feedbackIcon) feedbackIcon.innerHTML = '<i class="bi bi-exclamation-circle-fill text-danger"></i>';
      if (bannerStatusPill) bannerStatusPill.innerHTML = '<i class="bi bi-exclamation-circle-fill me-1.5"></i> Pending Payment';

      if (isDark) {
        feedbackBox.style.background = 'rgba(239, 68, 68, 0.12)';
        feedbackBox.style.border = '1px solid rgba(239, 68, 68, 0.3)';
      } else {
        feedbackBox.style.background = '#fef2f2';
        feedbackBox.style.border = '1px solid #fecaca';
      }
    }
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
    updateStatusUI();
  });

  // Run initial calculation
  calculatePayment(false);

  // AJAX Submission Helper
  function submitStep7(isExit = false) {
    if (!form.reportValidity()) return;

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
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 1) {
        toastr.success(data.message || 'Payment details saved successfully!');
        window.location.href = data.redirect || "{{ route('step8') }}";
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
