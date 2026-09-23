@extends('layouts.app')
@section('title', 'Payment Details - Step 7')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap">

      <!-- 8-STEP PROGRESS BAR -->
      <div class="step-progress">
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Handlers</div></div>
        <div class="sp-step active"><div class="circ">7</div><div class="sp-label">Payment</div></div>
        <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
      </div>

      <div class="wizard-card shadow-sm border rounded-3 p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="wc-eyebrow text-uppercase fw-bold text-primary small">Step 7 of 8</div>
            <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Payment &amp; Financial Settlement</h4>
            <div class="wc-sub text-muted small">Record the overall application value, advance payments received, balance due, and statutory settlement status.</div>
          </div>
          <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-cash-stack me-1"></i> Billing Ledger
          </span>
        </div>

        @php
          $pv = (float)($payment['product_value'] ?? 0);
          $pa = (float)($payment['paid_amount'] ?? 0);
          $pe = max(0, $pv - $pa);
          $st = $payment['payment_status'] ?? ($pa <= 0 ? 'pending' : ($pe <= 0 ? 'paid' : 'partial'));
        @endphp

        <!-- Real-Time Financial Metric Cards -->
        <div class="row g-3 my-2">
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
              <div class="text-muted small fw-semibold text-uppercase">Product / Contract Value</div>
              <div class="h4 fw-bold mb-0 text-navy mt-1" id="card_disp_product_value">₹ {{ number_format($pv, 2) }}</div>
              <small class="text-muted" style="font-size:11px;">Total quoted service value</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
              <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
              <div class="h4 fw-bold mb-0 text-success mt-1" id="card_disp_paid_amount">₹ {{ number_format($pa, 2) }}</div>
              <small class="text-muted" style="font-size:11px;">Received via Cash / Bank / NEFT</small>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
              <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
              <div class="h4 fw-bold mb-0 text-danger mt-1" id="card_disp_pending_amount">₹ {{ number_format($pe, 2) }}</div>
              <small class="text-muted" style="font-size:11px;">Remaining balance to collect</small>
            </div>
          </div>
        </div>

        <form id="payment_form" method="POST" action="{{ route('step7.save') }}">
          @csrf

          <div class="card p-3 my-3 bg-light border-0 rounded-3">
            <div class="row g-3">
              <!-- Product Value -->
              <div class="col-md-4">
                <label class="form-label fw-bold text-navy small mb-1">
                  Product / Service Value (₹) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-white fw-bold text-navy">₹</span>
                  <input type="number" step="0.01" min="0" name="product_value" id="field_product_value"
                         class="form-control fw-bold" placeholder="0.00" value="{{ $pv > 0 ? $pv : '' }}" required>
                </div>
                <div class="form-text text-muted" style="font-size:11px;">Enter the total agreed fee or departmental quotation.</div>
              </div>

              <!-- Paid Amount -->
              <div class="col-md-4">
                <label class="form-label fw-bold text-navy small mb-1">
                  Paid Amount (₹) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-white fw-bold text-success">₹</span>
                  <input type="number" step="0.01" min="0" name="paid_amount" id="field_paid_amount"
                         class="form-control fw-bold text-success" placeholder="0.00" value="{{ $pa > 0 ? $pa : '' }}" required>
                </div>
                <div class="form-text text-muted" style="font-size:11px;">Amount paid by applicant (enter 0 if unpaid).</div>
              </div>

              <!-- Pending Amount (Auto-Calculated) -->
              <div class="col-md-4">
                <label class="form-label fw-bold text-navy small mb-1">
                  Pending Balance (₹) <span class="text-muted">(Auto-Calculated)</span>
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light fw-bold text-danger">₹</span>
                  <input type="number" step="0.01" name="pending_amount_display" id="field_pending_amount"
                         class="form-control bg-light fw-bold text-danger" placeholder="0.00" value="{{ $pe }}" readonly>
                </div>
                <div class="form-text text-muted" style="font-size:11px;">Calculated automatically as Value minus Paid.</div>
              </div>

              <!-- Payment Status -->
              <div class="col-md-6 mt-3">
                <label class="form-label fw-bold text-navy small mb-1">
                  Payment Settlement Status <span class="text-danger">*</span>
                </label>
                <select name="payment_status" id="field_payment_status" class="form-select fw-semibold" required>
                  <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>🔴 Pending (Full Balance Due)</option>
                  <option value="partial" {{ $st === 'partial' ? 'selected' : '' }}>🟡 Partial (Part Payment Received)</option>
                  <option value="paid" {{ $st === 'paid' ? 'selected' : '' }}>🟢 Paid (Fully Cleared)</option>
                </select>
                <div class="form-text text-muted" style="font-size:11px;">Status adapts automatically based on amounts or can be overridden manually.</div>
              </div>

              <!-- Status Badge Feedback -->
              <div class="col-md-6 mt-3 d-flex align-items-center">
                <div class="p-3 w-100 rounded-2 d-flex align-items-center justify-content-between" id="status_feedback_box" style="background:#f1f5f9;">
                  <div>
                    <div class="small fw-semibold text-muted">Settlement Assessment</div>
                    <div class="fw-bold" id="status_feedback_text" style="color:#0F1E4D;">Pending Payment</div>
                  </div>
                  <span class="badge px-3 py-2 rounded-pill fs-6" id="status_feedback_badge" style="background:#e2e8f0; color:#475569;">
                    Pending
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Wizard Actions -->
          <div class="wizard-actions d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('step6') }}" class="btn btn-outline-secondary btn-sm px-3">
              <i class="bi bi-arrow-left me-1"></i> Back to Handlers
            </a>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btn_save_draft_exit">
                <i class="fa fa-save me-1"></i> Save Draft &amp; Exit
              </button>
              <button type="submit" class="btn btn-navy btn-sm px-4 fw-semibold" id="btn_submit_step7">
                Continue to Review <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
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

  const form = document.getElementById('payment_form');
  const btnSaveExit = document.getElementById('btn_save_draft_exit');
  const btnSubmit = document.getElementById('btn_submit_step7');

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
    if (currentStatus === 'paid') {
      feedbackText.textContent = 'Account Fully Settled (Zero Balance)';
      feedbackBadge.textContent = 'Fully Paid';
      feedbackBadge.className = 'badge bg-success text-white px-3 py-2 rounded-pill fs-6';
      feedbackBox.style.background = '#ecfdf5';
      feedbackBox.style.border = '1px solid #a7f3d0';
    } else if (currentStatus === 'partial') {
      feedbackText.textContent = 'Part Payment Received (Balance Remaining)';
      feedbackBadge.textContent = 'Partial Payment';
      feedbackBadge.className = 'badge bg-warning text-dark px-3 py-2 rounded-pill fs-6';
      feedbackBox.style.background = '#fffbeb';
      feedbackBox.style.border = '1px solid #fde68a';
    } else {
      feedbackText.textContent = 'Full Payment Outstanding (Pending)';
      feedbackBadge.textContent = 'Pending Payment';
      feedbackBadge.className = 'badge bg-danger text-white px-3 py-2 rounded-pill fs-6';
      feedbackBox.style.background = '#fef2f2';
      feedbackBox.style.border = '1px solid #fecaca';
    }
  }

  inputProduct.addEventListener('input', () => calculatePayment(false));
  inputPaid.addEventListener('input', () => calculatePayment(false));
  selectStatus.addEventListener('change', () => updateStatusUI());

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
        btnSubmit.innerHTML = `Continue to Review <i class="bi bi-arrow-right ms-1"></i>`;
      }
    })
    .catch(err => {
      console.error(err);
      toastr.error('Network or server error while saving data.');
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = `Continue to Review <i class="bi bi-arrow-right ms-1"></i>`;
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
