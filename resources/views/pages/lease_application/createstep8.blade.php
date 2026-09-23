@extends('layouts.app')
@section('title', 'Review & Final Submit - Step 8')
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
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Payment</div></div>
        <div class="sp-step active"><div class="circ">8</div><div class="sp-label">Review</div></div>
      </div>

      <div class="wizard-card shadow-sm border rounded-3 p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="wc-eyebrow text-uppercase fw-bold text-primary small">Step 8 of 8</div>
            <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Application Scrutiny &amp; Final Review</h4>
            <div class="wc-sub text-muted small">Please verify all applicant metadata, legal categories, 19 statutory documents, assigned handling personnel, and payment details before submitting to the scrutiny department.</div>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-shield-check me-1"></i> Pre-Submission Scrutiny
          </span>
        </div>

        <!-- 1. APPLICANT & FIRM IDENTITY -->
        <div class="card-panel mb-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-person-badge-fill me-2 text-primary"></i>1. Applicant &amp; Entity Identity (Step 1 &amp; 2)
            </h6>
            <a href="{{ route('step1') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 1</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-3"><span class="text-muted">Client / Authorized Person:</span> <br><b class="text-navy">{{ $previewData['client_name'] }}</b></div>
            <div class="col-md-3"><span class="text-muted">Firm / Company Name:</span> <br><b class="text-navy">{{ $previewData['company_name'] }}</b></div>
            <div class="col-md-3"><span class="text-muted">Customer Unique ID:</span> <br><b class="text-primary">{{ $previewData['mimas_no'] }}</b></div>
            <div class="col-md-3"><span class="text-muted">Aadhaar Identification:</span> <br><b>{{ $previewData['aadhaar_no'] }}</b></div>
            <div class="col-md-3 mt-2"><span class="text-muted">Primary Mobile:</span> <br><b>{{ $previewData['mobile_num'] }}</b></div>
            <div class="col-md-3 mt-2"><span class="text-muted">Secondary Mobile / Person:</span> <br><b>{{ $previewData['secondary_mobile_num'] ? ($previewData['secondary_contact_person'] . ' (' . $previewData['secondary_mobile_num'] . ')') : 'Not provided' }}</b></div>
            <div class="col-md-3 mt-2"><span class="text-muted">Permanent Account Number (PAN):</span> <br><b>{{ $previewData['pan'] }}</b></div>
            <div class="col-md-3 mt-2"><span class="text-muted">GST Identification:</span> <br><b>{{ $previewData['gstin'] }}</b></div>
          </div>
        </div>

        <!-- 2. QUARRY LOCATION & MINERAL DETAILS -->
        <div class="card-panel mb-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-geo-alt-fill me-2 text-danger"></i>2. Quarry Location &amp; Mineral Scope (Step 1 &amp; 3)
            </h6>
            <a href="{{ route('step3') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 3</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-3"><span class="text-muted">Jurisdiction District:</span> <br><b>{{ $previewData['district_name'] }}</b></div>
            <div class="col-md-3"><span class="text-muted">Quarry Area Extent:</span> <br><b>{{ $previewData['extent_display'] }}</b></div>
            <div class="col-md-3"><span class="text-muted">Mineral Type:</span> <br><b>{{ $previewData['mineral_name'] ?? 'Not specified' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Statutory Category:</span> <br><b>{{ $previewData['category_name'] }} ({{ $previewData['category_code'] }})</b></div>
          </div>
        </div>

        <!-- 3. PROJECT HANDLING PERSONS (FROM STEP 6) -->
        <div class="card-panel mb-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-people-fill me-2 text-primary"></i>3. Project Handling Personnel &amp; Team (Step 6)
            </h6>
            <a href="{{ route('step6') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 6</a>
          </div>
          @php
            $handlers = $previewData['handlers'] ?? [];
          @endphp
          @if(!empty($handlers) && count($handlers) > 0)
            <div class="table-responsive">
              <table class="table table-sm table-bordered bg-white mb-0" style="font-size:0.82rem;">
                <thead class="bg-light text-navy">
                  <tr>
                    <th style="width:40px;" class="text-center">#</th>
                    <th style="width:25%;">Person Name</th>
                    <th style="width:25%;">Role / Designation</th>
                    <th>Notes &amp; Responsibilities</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($handlers as $hIdx => $handler)
                    <tr>
                      <td class="text-center fw-bold text-muted">{{ $hIdx + 1 }}</td>
                      <td class="fw-semibold text-navy"><i class="bi bi-person-fill text-primary me-1"></i> {{ $handler['name'] }}</td>
                      <td><span class="badge bg-secondary-subtle text-secondary border py-1 px-2">{{ $handler['role'] }}</span></td>
                      <td class="text-muted">{{ !empty($handler['notes']) ? $handler['notes'] : 'No specific notes recorded' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-muted small fst-italic">No dedicated handling personnel assigned yet.</div>
          @endif
        </div>

        <!-- 4. PAYMENT & FINANCIAL SETTLEMENT (FROM STEP 7) -->
        <div class="card-panel mb-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-cash-stack me-2 text-success"></i>4. Payment &amp; Billing Ledger (Step 7)
            </h6>
            <a href="{{ route('step7') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 7</a>
          </div>
          @php
            $pay = $previewData['payment'] ?? ['product_value' => 0, 'paid_amount' => 0, 'pending_amount' => 0, 'payment_status' => 'pending'];
            $st = $pay['payment_status'] ?? 'pending';
          @endphp
          <div class="row g-2 text-center" style="font-size:0.85rem;">
            <div class="col-md-3">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small d-block">Product Value</span>
                <b class="text-navy fs-6">₹ {{ number_format($pay['product_value'], 2) }}</b>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-2 border rounded bg-white">
                <span class="text-success small d-block">Paid Amount</span>
                <b class="text-success fs-6">₹ {{ number_format($pay['paid_amount'], 2) }}</b>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-2 border rounded bg-white">
                <span class="text-danger small d-block">Pending Balance</span>
                <b class="text-danger fs-6">₹ {{ number_format($pay['pending_amount'], 2) }}</b>
              </div>
            </div>
            <div class="col-md-3">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small d-block">Settlement Status</span>
                @if($st === 'paid')
                  <span class="badge bg-success text-white py-1 px-3 mt-1 rounded-pill">🟢 Paid</span>
                @elseif($st === 'partial')
                  <span class="badge bg-warning text-dark py-1 px-3 mt-1 rounded-pill">🟡 Partial</span>
                @else
                  <span class="badge bg-danger text-white py-1 px-3 mt-1 rounded-pill">🔴 Pending</span>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- 5. FOLDERS & 19 STATUTORY DOCUMENTS (FROM STEP 4 & 5) -->
        <div class="card-panel mb-3 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-folder-check me-2 text-success"></i>5. Statutory Document Folders (Step 5)
            </h6>
            <a href="{{ route('step5') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 5</a>
          </div>
          <div class="row g-2 mb-2 text-center" style="font-size: 0.85rem;">
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small d-block">Folder 1: Documents (#7)</span>
                <b>{{ $previewData['f7_count'] ?? 0 }} / 9 Files Attached</b>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small d-block">Folder 2: Lease Application (#8)</span>
                <b>{{ $previewData['f8_count'] ?? 0 }} / 7 Files Attached</b>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-2 border rounded bg-white">
                <span class="text-muted small d-block">Folder 3: Plan Files (#9)</span>
                <b>{{ $previewData['f9_count'] ?? 0 }} / 3 Files Attached</b>
              </div>
            </div>
          </div>

          @php
            $customCount = count(array_filter($previewData['uploaded_docs'] ?? [], fn($d) => !empty($d['is_custom'])));
            $totalExpected = 19 + $customCount;
          @endphp
          <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
            <div>
              <span class="text-muted">Total Statutory Uploads:</span> 
              <b>{{ $previewData['uploaded_count'] }} of {{ $totalExpected }} Items Attached</b>
            </div>
            <div>
              @if($previewData['uploaded_count'] >= $totalExpected)
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> All {{ $totalExpected }} Files Attached</span>
              @elseif($previewData['uploaded_count'] > 0)
                <span class="badge bg-primary"><i class="bi bi-file-earmark-check me-1"></i> {{ $previewData['uploaded_count'] }} of {{ $totalExpected }} Files Attached</span>
              @else
                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> 0 of {{ $totalExpected }} Uploaded</span>
              @endif
            </div>
          </div>
        </div>

        <!-- 6. MIMAS CREDENTIALS (FROM STEP 2) -->
        <div class="card-panel mb-4 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e2e8f0;">
          <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <h6 class="fw-bold mb-0 text-navy">
              <i class="bi bi-key-fill me-2 text-warning"></i>6. MIMAS Portal Credentials (Step 2)
            </h6>
            <a href="{{ route('step2') }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:0.75rem;"><i class="bi bi-pencil"></i> Edit Step 2</a>
          </div>
          <div class="row g-2" style="font-size: 0.85rem;">
            <div class="col-md-3"><span class="text-muted">Portal User ID:</span> <br><b>{{ $previewData['mimas_user_id'] ?? 'Not Entered' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Password:</span> <br><b class="text-muted">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</b> <span class="badge bg-success-subtle text-success border border-success ms-1" style="font-size:0.65rem;">Encrypted AES-256</span></div>
            <div class="col-md-3"><span class="text-muted">Registered Email:</span> <br><b>{{ $previewData['mimas_email'] ?? 'Not Entered' }}</b></div>
            <div class="col-md-3"><span class="text-muted">Registered Contact:</span> <br><b>{{ $previewData['mimas_contact'] ?? 'Not Entered' }}</b></div>
          </div>
        </div>

        <!-- SUBMIT CONFIRMATION BANNER -->
        <div class="card-panel mt-3 mb-0 p-3 rounded-3" style="background:#ecfdf5; border:1px solid #a7f3d0;">
          <div class="d-flex gap-2">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div style="font-size:.82rem; color:#065f46;">
              <b>Ready for Submission:</b> You are about to submit the complete dossier. Once submitted, it will be assigned an official Application Number (LA-{{ date('Y') }}-NNNN), moved to the scrutiny queue, and team allocations and payment ledger will be locked in the official dossier.
            </div>
          </div>
        </div>

        <!-- WIZARD ACTION BUTTONS -->
        <div class="wizard-actions mt-4 d-flex justify-content-between align-items-center pt-3 border-top">
          <a href="{{ route('step7') }}" class="btn btn-outline-navy btn-sm px-3"><i class="bi bi-arrow-left me-1"></i> Back to Payment</a>
          @can('application.create')
          <form action="{{ route('application.submit') }}" method="POST" class="d-inline" id="final_submit_form">
            @csrf
            <input type="hidden" name="mimas_no" value="{{ $previewData['mimas_no'] ?? '' }}">
            <input type="hidden" name="category_code" value="{{ $previewData['category_code'] ?? '' }}">
            <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
              <button type="submit" id="btn_final_submit" class="btn btn-green px-4 fw-semibold">
                Submit Final Application <i class="bi bi-check-lg ms-1"></i>
              </button>
              <button type="submit" name="move_to_mining" value="1" id="btn_submit_move_mining" class="btn btn-navy px-3" title="Save Lease and immediately initiate Mining Plan under same Common ID">
                <i class="bi bi-rocket-takeoff me-1 text-warning"></i> Submit &amp; Move to Mining Plan
              </button>
            </div>
          </form>
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
  $('#final_submit_form').on('submit', function(e) {
    var submitter = e.originalEvent && e.originalEvent.submitter;
    if (submitter && submitter.id === 'btn_submit_move_mining') {
      $('#btn_submit_move_mining').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Creating & Moving to Mining...');
      $('#btn_final_submit').prop('disabled', true);
    } else {
      $('#btn_final_submit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Submitting Application...');
      $('#btn_submit_move_mining').prop('disabled', true);
    }
  });
});
</script>
@endsection
