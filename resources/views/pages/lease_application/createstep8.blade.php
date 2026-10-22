@extends('layouts.app')
@section('title', 'Review & Final Submit - Step 8')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step8-page">
      <div class="wizard-wrap">

        <!-- 8-STEP PROGRESS BAR (Exact Native Stepper Parity with Steps 1-7) -->
        <div class="step-progress">
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Handlers</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Payment</div></div>
          <div class="sp-step active"><div class="circ">8</div><div class="sp-label">Review</div></div>
        </div>

        <div class="card s8-main-card wizard-card">
          <!-- CARD HEADER -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
              <div class="s8-eyebrow text-uppercase fw-bold text-primary mb-1">Step 8 of 8 &bull; Dossier Scrutiny &amp; Final Submission</div>
              <h4 class="s8-title mb-1">
                <i class="bi bi-clipboard2-check-fill text-primary me-2"></i>Application Scrutiny &amp; Final Review
              </h4>
              <div class="s8-subtitle">Please verify all applicant metadata, legal categories, 19 statutory documents, assigned handling personnel, and payment details before submitting to the scrutiny department.</div>
            </div>
            <span class="badge s8-scrutiny-pill">
              <i class="bi bi-shield-check me-1.5"></i>Pre-Submission Scrutiny
            </span>
          </div>

          @php
            $pay = $previewData['payment'] ?? ['product_value' => 0, 'paid_amount' => 0, 'pending_amount' => 0, 'payment_status' => 'pending'];
            $st = $pay['payment_status'] ?? 'pending';
            $uploadedDocs = $previewData['uploaded_docs'] ?? [];
            $handlers = $previewData['handlers'] ?? [];

            $customF7 = 0;
            $customF8 = 0;
            $customF9 = 0;
            foreach ($uploadedDocs as $k => $d) {
              if (!is_numeric($k) || (int)$k < 1 || (int)$k > 19) {
                $fId = (int)($d['folder_id'] ?? 7);
                if ($fId === 8) $customF8++;
                elseif ($fId === 9) $customF9++;
                else $customF7++;
              }
            }
            $f7Total = 9 + $customF7;
            $f8Total = 7 + $customF8;
            $f9Total = 3 + $customF9;
            $totalExpected = 19 + $customF7 + $customF8 + $customF9;
          @endphp

          <!-- EXECUTIVE AUDIT & READINESS BANNER -->
          <div class="s8-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s8-banner-icon-badge">
                  <i class="bi bi-patch-check-fill"></i>
                </div>
                <div>
                  <div class="s8-banner-title fw-bold">Statutory Dossier Audit &amp; Compliance Verification</div>
                  <div class="s8-banner-desc">Complete multi-step application dossier assembled. Inspect each section below for legal accuracy before generating the official application number.</div>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge s8-banner-status-pill">
                  <i class="bi bi-shield-lock-fill me-1.5"></i>Ready for Final Submission
                </span>
              </div>
            </div>
          </div>

          <!-- 4-BENTO EXECUTIVE METRIC OVERVIEW -->
          <div class="row g-3 mb-4">
            <!-- Bento 1: Applicant & Identity -->
            <div class="col-xl-3 col-md-6">
              <div class="s8-kpi-card s8-kpi-applicant">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s8-kpi-label">Applicant Entity</span>
                  <div class="s8-kpi-icon-badge s8-kpi-icon-blue"><i class="bi bi-person-badge-fill"></i></div>
                </div>
                <div class="s8-kpi-val text-truncate" title="{{ $previewData['client_name'] }}">{{ $previewData['client_name'] }}</div>
                <div class="s8-kpi-sub">
                  <span class="badge s8-chip-blue font-monospace">MIMAS: {{ $previewData['mimas_no'] }}</span>
                </div>
              </div>
            </div>

            <!-- Bento 2: Quarry Location & Mineral -->
            <div class="col-xl-3 col-md-6">
              <div class="s8-kpi-card s8-kpi-location">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s8-kpi-label">Quarry &amp; Mineral</span>
                  <div class="s8-kpi-icon-badge s8-kpi-icon-emerald"><i class="bi bi-geo-alt-fill"></i></div>
                </div>
                <div class="s8-kpi-val text-truncate" title="{{ $previewData['mineral_name'] ?? 'Not specified' }}">{{ $previewData['mineral_name'] ?? 'Not specified' }}</div>
                <div class="s8-kpi-sub">
                  <span class="badge s8-chip-emerald"><i class="bi bi-pin-map-fill me-1"></i>{{ $previewData['district_name'] }} &bull; {{ $previewData['extent_display'] }}</span>
                </div>
              </div>
            </div>

            <!-- Bento 3: Statutory Uploads -->
            <div class="col-xl-3 col-md-6">
              <div class="s8-kpi-card s8-kpi-docs">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s8-kpi-label">Statutory Uploads</span>
                  <div class="s8-kpi-icon-badge s8-kpi-icon-amber"><i class="bi bi-folder-check"></i></div>
                </div>
                <div class="s8-kpi-val">{{ $previewData['uploaded_count'] }} <span class="fs-6 text-muted fw-normal">/ {{ $totalExpected }} Files</span></div>
                <div class="s8-kpi-sub">
                  <span class="badge s8-chip-amber me-1">F7: {{ $previewData['f7_count'] ?? 0 }}/{{ $f7Total }}</span>
                  <span class="badge s8-chip-amber me-1">F8: {{ $previewData['f8_count'] ?? 0 }}/{{ $f8Total }}</span>
                  <span class="badge s8-chip-amber">F9: {{ $previewData['f9_count'] ?? 0 }}/{{ $f9Total }}</span>
                </div>
              </div>
            </div>

            <!-- Bento 4: Settlement & Ledger -->
            <div class="col-xl-3 col-md-6">
              <div class="s8-kpi-card s8-kpi-finance">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <span class="s8-kpi-label">Billing Ledger</span>
                  <div class="s8-kpi-icon-badge s8-kpi-icon-purple"><i class="bi bi-cash-stack"></i></div>
                </div>
                <div class="s8-kpi-val text-success">₹ {{ number_format($pay['paid_amount'], 2) }}</div>
                <div class="s8-kpi-sub d-flex justify-content-between align-items-center">
                  <span class="text-muted small">Due: ₹ {{ number_format($pay['pending_amount'], 2) }}</span>
                  <span class="badge {{ $st === 'paid' ? 'bg-success text-white' : ($st === 'partial' ? 'bg-warning text-dark' : 'bg-danger text-white') }} rounded-pill px-2 py-0.5" style="font-size:10.5px;">
                    {{ ucfirst($st) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- 1. APPLICANT & FIRM IDENTITY -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-blue"><i class="bi bi-person-badge-fill"></i></div>
                <h6 class="s8-panel-title mb-0">1. Applicant &amp; Entity Identity (Step 1 &amp; 2)</h6>
                <span class="badge s8-panel-step-badge">Steps 1 &amp; 2</span>
              </div>
              <a href="{{ route('step1') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 1
              </a>
            </div>
            <div class="s8-panel-body">
              <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Client / Authorized Person</span>
                    <span class="s8-tile-value text-navy">{{ $previewData['client_name'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Firm / Company Name</span>
                    <span class="s8-tile-value text-navy">{{ $previewData['company_name'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Customer Unique ID (MIMAS)</span>
                    <span class="s8-tile-value text-primary font-monospace">{{ $previewData['mimas_no'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Aadhaar Identification</span>
                    <span class="s8-tile-value">{{ $previewData['aadhaar_no'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Primary Mobile</span>
                    <span class="s8-tile-value">
                      <i class="bi bi-telephone-fill text-muted me-1"></i>{{ $previewData['mobile_num'] }}
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Secondary Mobile / Person</span>
                    <span class="s8-tile-value text-truncate" title="{{ $previewData['secondary_mobile_num'] ? ($previewData['secondary_contact_person'] . ' (' . $previewData['secondary_mobile_num'] . ')') : 'Not provided' }}">
                      {{ $previewData['secondary_mobile_num'] ? ($previewData['secondary_contact_person'] . ' (' . $previewData['secondary_mobile_num'] . ')') : 'Not provided' }}
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Permanent Account Number (PAN)</span>
                    <span class="s8-tile-value font-monospace">{{ $previewData['pan'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">GST Identification (GSTIN)</span>
                    <span class="s8-tile-value font-monospace">{{ $previewData['gstin'] }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 2. QUARRY LOCATION & MINERAL SCOPE -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-red"><i class="bi bi-geo-alt-fill"></i></div>
                <h6 class="s8-panel-title mb-0">2. Quarry Location &amp; Mineral Scope (Step 1 &amp; 3)</h6>
                <span class="badge s8-panel-step-badge">Steps 1 &amp; 3</span>
              </div>
              <a href="{{ route('step3') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 3
              </a>
            </div>
            <div class="s8-panel-body">
              <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Jurisdiction District</span>
                    <span class="s8-tile-value"><i class="bi bi-building me-1 text-muted"></i>{{ $previewData['district_name'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Quarry Area Extent</span>
                    <span class="s8-tile-value"><i class="bi bi-rulers me-1 text-muted"></i>{{ $previewData['extent_display'] }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Mineral Type</span>
                    <span class="s8-tile-value text-primary"><i class="bi bi-gem me-1"></i>{{ $previewData['mineral_name'] ?? 'Not specified' }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Statutory Category</span>
                    <span class="s8-tile-value text-truncate" title="{{ $previewData['category_name'] }}{{ !empty($previewData['category_code']) && $previewData['category_code'] !== $previewData['category_name'] ? ' (' . $previewData['category_code'] . ')' : '' }}">
                      {{ $previewData['category_name'] }}{{ !empty($previewData['category_code']) && $previewData['category_code'] !== $previewData['category_name'] ? ' (' . $previewData['category_code'] . ')' : '' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 3. PROJECT HANDLING PERSONNEL & TEAM -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-teal"><i class="bi bi-people-fill"></i></div>
                <h6 class="s8-panel-title mb-0">3. Project Handling Personnel &amp; Team (Step 6)</h6>
                <span class="badge s8-panel-step-badge">Step 6</span>
              </div>
              <a href="{{ route('step6') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 6
              </a>
            </div>
            <div class="s8-panel-body">
              @if(!empty($handlers) && count($handlers) > 0)
                <div class="table-responsive">
                  <table class="table s8-table mb-0">
                    <thead>
                      <tr>
                        <th style="width:50px;" class="text-center">#</th>
                        <th style="width:30%;">Person Name</th>
                        <th style="width:25%;">Role / Designation</th>
                        <th>Notes &amp; Responsibilities</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($handlers as $hIdx => $handler)
                        <tr>
                          <td class="text-center fw-bold text-muted">{{ $hIdx + 1 }}</td>
                          <td class="fw-bold text-navy">
                            <div class="d-flex align-items-center gap-2">
                              <div class="s8-avatar-icon"><i class="bi bi-person-fill"></i></div>
                              <span>{{ $handler['name'] }}</span>
                            </div>
                          </td>
                          <td>
                            <span class="badge s8-role-badge">
                              <i class="bi bi-briefcase-fill me-1"></i>{{ $handler['role'] }}
                            </span>
                          </td>
                          <td class="text-muted">{{ !empty($handler['notes']) ? $handler['notes'] : 'No specific notes recorded' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="s8-empty-notice d-flex align-items-center gap-2">
                  <i class="bi bi-info-circle-fill text-muted"></i>
                  <span>No dedicated handling personnel assigned yet. You can assign project managers or geologists in Step 6.</span>
                </div>
              @endif
            </div>
          </div>

          <!-- 4. PAYMENT & BILLING LEDGER -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-green"><i class="bi bi-cash-stack"></i></div>
                <h6 class="s8-panel-title mb-0">4. Payment &amp; Billing Ledger (Step 7)</h6>
                <span class="badge s8-panel-step-badge">Step 7</span>
              </div>
              <a href="{{ route('step7') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 7
              </a>
            </div>
            <div class="s8-panel-body">
              <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                  <div class="s8-ledger-card s8-ledger-contract">
                    <span class="s8-ledger-sub">Product Value</span>
                    <div class="s8-ledger-amount text-navy">₹ {{ number_format($pay['product_value'], 2) }}</div>
                    <span class="s8-ledger-hint text-muted">Total Application Fee</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-ledger-card s8-ledger-paid">
                    <span class="s8-ledger-sub text-success">Paid Amount</span>
                    <div class="s8-ledger-amount text-success">₹ {{ number_format($pay['paid_amount'], 2) }}</div>
                    <span class="s8-ledger-hint text-muted">Advance Received</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-ledger-card s8-ledger-pending">
                    <span class="s8-ledger-sub text-danger">Pending Balance</span>
                    <div class="s8-ledger-amount text-danger">₹ {{ number_format($pay['pending_amount'], 2) }}</div>
                    <span class="s8-ledger-hint text-muted">Outstanding Dues</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-ledger-card s8-ledger-status">
                    <span class="s8-ledger-sub">Settlement Status</span>
                    <div class="pt-1">
                      @if($st === 'paid')
                        <span class="badge bg-success text-white py-1.5 px-3 rounded-pill fs-7">
                          <i class="bi bi-check-circle-fill me-1"></i> Fully Paid
                        </span>
                      @elseif($st === 'partial')
                        <span class="badge bg-warning text-dark py-1.5 px-3 rounded-pill fs-7">
                          <i class="bi bi-clock-history me-1"></i> Partial Payment
                        </span>
                      @else
                        <span class="badge bg-danger text-white py-1.5 px-3 rounded-pill fs-7">
                          <i class="bi bi-exclamation-circle-fill me-1"></i> Pending Payment
                        </span>
                      @endif
                    </div>
                    <span class="s8-ledger-hint text-muted">Financial Clearance</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 5. FOLDERS & 19 STATUTORY DOCUMENTS -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-amber"><i class="bi bi-folder-check"></i></div>
                <h6 class="s8-panel-title mb-0">5. Statutory Document Folders (Step 5)</h6>
                <span class="badge s8-panel-step-badge">Step 5</span>
              </div>
              <a href="{{ route('step5') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 5
              </a>
            </div>
            <div class="s8-panel-body">
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <div class="s8-folder-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="s8-folder-title"><i class="bi bi-folder2-open text-primary me-1"></i>Folder 1: Documents (#7)</span>
                      <span class="badge s8-chip-blue">{{ $previewData['f7_count'] ?? 0 }} / {{ $f7Total }}</span>
                    </div>
                    <div class="s8-folder-meta text-muted small">KYC, Land Deeds, Gazette, Adangal</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="s8-folder-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="s8-folder-title"><i class="bi bi-folder2-open text-primary me-1"></i>Folder 2: Lease Application (#8)</span>
                      <span class="badge s8-chip-blue">{{ $previewData['f8_count'] ?? 0 }} / {{ $f8Total }}</span>
                    </div>
                    <div class="s8-folder-meta text-muted small">Signed Application &amp; Affidavits</div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="s8-folder-card">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                      <span class="s8-folder-title"><i class="bi bi-folder2-open text-primary me-1"></i>Folder 3: Plan Files (#9)</span>
                      <span class="badge s8-chip-blue">{{ $previewData['f9_count'] ?? 0 }} / {{ $f9Total }}</span>
                    </div>
                    <div class="s8-folder-meta text-muted small">Quarry Plan, KML &amp; Source CAD</div>
                  </div>
                </div>
              </div>

              <!-- STATUTORY AUDIT SUMMARY & REGISTRY TOGGLE -->
              <div class="s8-docs-summary d-flex justify-content-between align-items-center flex-wrap gap-2 pt-3 border-top">
                <div class="d-flex align-items-center gap-2">
                  <span class="text-muted fw-semibold" style="font-size:13px;">Total Statutory Uploads:</span>
                  <span class="fw-bold text-navy" style="font-size:13.5px;">{{ $previewData['uploaded_count'] }} of {{ $totalExpected }} Items Attached</span>
                  @if($previewData['uploaded_count'] >= $totalExpected)
                    <span class="badge bg-success ms-1"><i class="bi bi-check-circle me-1"></i> All {{ $totalExpected }} Files Attached</span>
                  @elseif($previewData['uploaded_count'] > 0)
                    <span class="badge bg-primary ms-1"><i class="bi bi-file-earmark-check me-1"></i> {{ $previewData['uploaded_count'] }} of {{ $totalExpected }} Files Attached</span>
                  @else
                    <span class="badge bg-warning text-dark ms-1"><i class="bi bi-clock me-1"></i> 0 of {{ $totalExpected }} Uploaded</span>
                  @endif
                </div>
                <button type="button" class="btn btn-sm s8-btn-registry" data-bs-toggle="collapse" data-bs-target="#docsRegistryList" aria-expanded="false" aria-controls="docsRegistryList">
                  <i class="bi bi-list-check me-1"></i>Toggle Detailed File Registry <i class="bi bi-chevron-down ms-1"></i>
                </button>
              </div>

              <!-- COLLAPSIBLE DETAILED DOCUMENT REGISTRY -->
              @php
                $standardDocs = [
                  ['item' => 1, 'folder' => 'Folder 1 (#7)', 'name' => '1. Land Document'],
                  ['item' => 2, 'folder' => 'Folder 1 (#7)', 'name' => '2. Consent (If Applicable)'],
                  ['item' => 3, 'folder' => 'Folder 1 (#7)', 'name' => '3. Adangal & A-register'],
                  ['item' => 4, 'folder' => 'Folder 1 (#7)', 'name' => '4. Patta & Encumbrance Certificate'],
                  ['item' => 5, 'folder' => 'Folder 1 (#7)', 'name' => '5. Work Order'],
                  ['item' => 6, 'folder' => 'Folder 1 (#7)', 'name' => '6. Gazette'],
                  ['item' => 7, 'folder' => 'Folder 1 (#7)', 'name' => '7. Recommendation Letter'],
                  ['item' => 8, 'folder' => 'Folder 1 (#7)', 'name' => '8. Mineral Management System - Application'],
                  ['item' => 9, 'folder' => 'Folder 1 (#7)', 'name' => '9. Challan downloaded from Mimas'],
                  ['item' => 10, 'folder' => 'Folder 2 (#8)', 'name' => '10. Lease application - signed, FMB, Plan'],
                  ['item' => 11, 'folder' => 'Folder 2 (#8)', 'name' => '11. Affidavit - Income Tax'],
                  ['item' => 12, 'folder' => 'Folder 2 (#8)', 'name' => '12. IT returns (If Applicable)'],
                  ['item' => 13, 'folder' => 'Folder 2 (#8)', 'name' => '13. Affidavit - Mining Due'],
                  ['item' => 14, 'folder' => 'Folder 2 (#8)', 'name' => '14. Affidavit - Mining Lease'],
                  ['item' => 15, 'folder' => 'Folder 2 (#8)', 'name' => '15. Affidavit - 1.5 meter depth'],
                  ['item' => 16, 'folder' => 'Folder 2 (#8)', 'name' => '16. Affidavit - Hill Areas'],
                  ['item' => 17, 'folder' => 'Folder 3 (#9)', 'name' => '17. Plan Source File'],
                  ['item' => 18, 'folder' => 'Folder 3 (#9)', 'name' => '18. KML File'],
                  ['item' => 19, 'folder' => 'Folder 3 (#9)', 'name' => '19. Plan PDF'],
                ];
              @endphp
              <div class="collapse mt-3" id="docsRegistryList">
                <div class="table-responsive s8-registry-wrapper">
                  <table class="table s8-table mb-0">
                    <thead>
                      <tr>
                        <th style="width:40px;" class="text-center">#</th>
                        <th style="width:20%;">Target Folder</th>
                        <th style="width:45%;">Statutory Document Title</th>
                        <th style="width:35%;">Attachment Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($standardDocs as $sd)
                        @php
                          $isAttached = isset($uploadedDocs[$sd['item']]);
                          $fInfo = $uploadedDocs[$sd['item']] ?? null;
                          $fPath = $fInfo['file_path'] ?? ($fInfo['draft_path'] ?? null);
                          $fUrl = '#';
                          if (!empty($fPath)) {
                            $fUrl = (str_starts_with($fPath, 'http://') || str_starts_with($fPath, 'https://')) ? $fPath : asset(ltrim($fPath, '/'));
                          }
                          $fName = $fInfo['file_name'] ?? 'File Attached';
                          $fExt = strtolower(pathinfo($fName, PATHINFO_EXTENSION));
                          $isModalViewable = in_array($fExt, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']);
                        @endphp
                        <tr>
                          <td class="text-center fw-bold text-muted">{{ $sd['item'] }}</td>
                          <td class="text-muted small">{{ $sd['folder'] }}</td>
                          <td class="fw-semibold text-navy">{{ $sd['name'] }}</td>
                          <td>
                            @if($isAttached)
                              <div class="d-inline-flex align-items-center gap-1.5 flex-wrap">
                                <a href="{{ $fUrl }}" target="_blank" rel="noopener noreferrer" 
                                   class="badge s8-doc-attached s8-doc-link text-truncate d-inline-flex align-items-center gap-1 text-decoration-none" 
                                   style="max-width:250px;" 
                                   title="Click / Touch to open {{ $fName }} in new tab"
                                   data-file-url="{{ $fUrl }}"
                                   data-file-name="{{ $fName }}"
                                   data-file-ext="{{ $fExt }}">
                                  <i class="bi bi-file-earmark-check-fill text-success"></i> 
                                  <span class="text-truncate">{{ $fName }}</span>
                                  <i class="bi bi-box-arrow-up-right s8-doc-open-icon ms-1"></i>
                                </a>
                                <a href="{{ $fUrl }}" target="_blank" rel="noopener noreferrer" 
                                   class="btn btn-xs s8-btn-action-open" 
                                   title="Open {{ $fName }} in new tab">
                                  <i class="bi bi-box-arrow-up-right me-1"></i>Open
                                </a>
                                @if($isModalViewable)
                                  <button type="button" 
                                     class="btn btn-xs s8-btn-action-preview" 
                                     data-file-url="{{ $fUrl }}"
                                     data-file-name="{{ $fName }}"
                                     data-file-ext="{{ $fExt }}"
                                     title="Quick Preview {{ $fName }} in modal">
                                    <i class="bi bi-eye-fill me-1"></i>Preview
                                  </button>
                                @endif
                              </div>
                            @else
                              <span class="badge s8-doc-pending">
                                <i class="bi bi-dash-circle me-1"></i> Not Uploaded
                              </span>
                            @endif
                          </td>
                        </tr>
                      @endforeach

                      @php
                        $customDocs = [];
                        foreach ($uploadedDocs as $cKey => $cItem) {
                          if (!is_numeric($cKey) || (int)$cKey < 1 || (int)$cKey > 19) {
                            $customDocs[$cKey] = $cItem;
                          }
                        }
                      @endphp
                      @foreach($customDocs as $cKey => $cDoc)
                        @php
                          $fPath = $cDoc['file_path'] ?? ($cDoc['draft_path'] ?? null);
                          $fUrl = '#';
                          if (!empty($fPath)) {
                            $fUrl = (str_starts_with($fPath, 'http://') || str_starts_with($fPath, 'https://')) ? $fPath : asset(ltrim($fPath, '/'));
                          }
                          $fName = $cDoc['file_name'] ?? 'Custom Document';
                          $fExt = strtolower(pathinfo($fName, PATHINFO_EXTENSION));
                          $fFolderId = $cDoc['folder_id'] ?? 7;
                          $fFolderLabel = $fFolderId == 8 ? 'Folder 2 (#8)' : ($fFolderId == 9 ? 'Folder 3 (#9)' : 'Folder 1 (#7)');
                          $isModalViewable = in_array($fExt, ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']);
                        @endphp
                        <tr>
                          <td class="text-center fw-bold text-primary"><i class="bi bi-plus-circle-fill"></i></td>
                          <td class="text-muted small">{{ $fFolderLabel }}</td>
                          <td class="fw-semibold text-navy">{{ $cDoc['doc_name'] ?? 'Custom Document' }} <span class="badge bg-secondary-subtle text-muted ms-1" style="font-size:10px;">Custom</span></td>
                          <td>
                            <div class="d-inline-flex align-items-center gap-1.5 flex-wrap">
                              <a href="{{ $fUrl }}" target="_blank" rel="noopener noreferrer" 
                                 class="badge s8-doc-attached s8-doc-link text-truncate d-inline-flex align-items-center gap-1 text-decoration-none" 
                                 style="max-width:250px;" 
                                 title="Click / Touch to open {{ $fName }} in new tab"
                                 data-file-url="{{ $fUrl }}"
                                 data-file-name="{{ $fName }}"
                                 data-file-ext="{{ $fExt }}">
                                <i class="bi bi-file-earmark-check-fill text-success"></i> 
                                <span class="text-truncate">{{ $fName }}</span>
                                <i class="bi bi-box-arrow-up-right s8-doc-open-icon ms-1"></i>
                              </a>
                              <a href="{{ $fUrl }}" target="_blank" rel="noopener noreferrer" 
                                 class="btn btn-xs s8-btn-action-open" 
                                 title="Open {{ $fName }} in new tab">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open
                              </a>
                              @if($isModalViewable)
                                <button type="button" 
                                   class="btn btn-xs s8-btn-action-preview" 
                                   data-file-url="{{ $fUrl }}"
                                   data-file-name="{{ $fName }}"
                                   data-file-ext="{{ $fExt }}"
                                   title="Quick Preview {{ $fName }} in modal">
                                  <i class="bi bi-eye-fill me-1"></i>Preview
                                </button>
                              @endif
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>

          <!-- 6. MIMAS PORTAL CREDENTIALS -->
          <div class="s8-section-panel mb-4">
            <div class="s8-panel-header d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <div class="s8-panel-header-icon s8-icon-yellow"><i class="bi bi-key-fill"></i></div>
                <h6 class="s8-panel-title mb-0">6. MIMAS Portal Credentials (Step 2)</h6>
                <span class="badge s8-panel-step-badge">Step 2</span>
              </div>
              <a href="{{ route('step2') }}" class="btn btn-sm s8-btn-edit">
                <i class="bi bi-pencil-square me-1"></i>Edit Step 2
              </a>
            </div>
            <div class="s8-panel-body">
              <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Portal User ID</span>
                    <span class="s8-tile-value font-monospace text-primary">{{ $previewData['mimas_user_id'] ?? 'Not Entered' }}</span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Portal Password</span>
                    <div class="d-flex align-items-center gap-1 pt-0.5">
                      <span id="preview_pass_masked" class="font-monospace fw-bold text-navy" style="letter-spacing:2px; font-size:14px;">&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;</span>
                      <span id="preview_pass_plain" class="font-monospace fw-bold text-primary" style="display:none; font-size:13px;">{{ $previewData['mimas_password'] ?? 'Not Entered' }}</span>
                      @if(!empty($previewData['mimas_password']) && $previewData['mimas_password'] !== '__UNCHANGED__')
                        <button type="button" class="btn btn-xs s8-btn-cred ms-1" id="btn_toggle_preview_pass" title="Reveal / Conceal Password">
                          <i class="fa fa-eye" id="icon_preview_pass"></i>
                        </button>
                        <button type="button" class="btn btn-xs s8-btn-cred ms-0.5" id="btn_copy_preview_pass" data-clipboard-text="{{ $previewData['mimas_password'] }}" title="Copy Password">
                          <i class="fa fa-copy"></i>
                        </button>
                      @endif
                      <!-- <span class="badge s8-chip-emerald ms-1" style="font-size:10px;">AES-256</span> -->
                    </div>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Registered Portal Email</span>
                    <span class="s8-tile-value text-truncate" title="{{ $previewData['mimas_email'] ?? 'Not Entered' }}">
                      {{ $previewData['mimas_email'] ?? 'Not Entered' }}
                    </span>
                  </div>
                </div>
                <div class="col-lg-3 col-md-6">
                  <div class="s8-data-tile">
                    <span class="s8-tile-label">Registered Contact Number</span>
                    <span class="s8-tile-value">{{ $previewData['mimas_contact'] ?? 'Not Entered' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- PRE-SUBMISSION COMPLIANCE CONFIRMATION BANNER -->
          <div class="s8-declaration-card mb-4">
            <div class="d-flex gap-3">
              <div class="s8-declaration-icon-badge">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="flex-grow-1">
                <div class="s8-declaration-title fw-bold mb-1">Pre-Submission Statutory Declaration &amp; Compliance Acknowledgment</div>
                <div class="s8-declaration-text mb-2">
                  You are about to transmit this complete application dossier to the Official Scrutiny Department. Upon submission, an official immutable Application ID (<span class="font-monospace fw-bold">LA-{{ date('Y') }}-NNNN</span>) will be assigned, the draft will be permanently locked, and designated handling personnel will be assigned for statutory vetting.
                </div>
                <div class="form-check s8-check-confirm">
                  <input class="form-check-input" type="checkbox" id="confirm_scrutiny_ack" checked>
                  <label class="form-check-label fw-semibold" for="confirm_scrutiny_ack">
                    I confirm that all applicant metadata, statutory deeds, quarry extent coordinates, team handlers, and billing records have been verified and audited for submission.
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- WIZARD ACTION BUTTONS -->
          <div class="wizard-actions s8-actions d-flex justify-content-between align-items-center pt-3 border-top">
            <a href="{{ route('step7') }}" class="btn s8-btn-back">
              <i class="bi bi-arrow-left me-2"></i>Back to Payment
            </a>
            @can('application.create')
            <form action="{{ route('application.submit') }}" method="POST" class="d-inline" id="final_submit_form">
              @csrf
              <input type="hidden" name="mimas_no" value="{{ $previewData['mimas_no'] ?? '' }}">
              <input type="hidden" name="category_code" value="{{ $previewData['category_code'] ?? '' }}">
              <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                <button type="button" id="btn_open_submit_modal" class="btn s8-btn-submit">
                  <i class="bi bi-check-circle-fill me-2"></i>Submit Final Application
                </button>
                <!-- Direct submit button accessible for fallback / form dispatch -->
                <button type="submit" id="btn_final_submit" style="display:none;"></button>
              </div>
            </form>
            @endcan
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- SUBMISSION CONFIRMATION MODAL -->
<div class="modal fade" id="modalSubmitConfirm" tabindex="-1" aria-labelledby="modalSubmitConfirmLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content s8-modal-content">
      <div class="modal-header s8-modal-header border-0 pb-0">
        <div class="d-flex align-items-center gap-2">
          <div class="s8-modal-icon-badge">
            <i class="bi bi-shield-check text-success"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-navy" id="modalSubmitConfirmLabel">Confirm Dossier Submission</h5>
            <span class="text-muted small">Application Scrutiny Dispatch</span>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body s8-modal-body py-4">
        <p class="text-muted mb-3" style="font-size:13.5px; line-height:1.5;">
          You are about to submit the complete dossier for <b class="text-navy">{{ $previewData['client_name'] }}</b> (<span class="font-monospace text-primary fw-bold">{{ $previewData['mimas_no'] }}</span>).
        </p>
        <div class="s8-modal-recap p-3 rounded-3 mb-3">
          <div class="d-flex justify-content-between py-1 border-bottom border-light-subtle small">
            <span class="text-muted">Quarry Location:</span>
            <span class="fw-bold text-navy">{{ $previewData['district_name'] }} ({{ $previewData['extent_display'] }})</span>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom border-light-subtle small">
            <span class="text-muted">Statutory Uploads:</span>
            <span class="fw-bold text-navy">{{ $previewData['uploaded_count'] }} of {{ $totalExpected }} Items</span>
          </div>
          <div class="d-flex justify-content-between py-1 border-bottom border-light-subtle small">
            <span class="text-muted">Financial Settlement:</span>
            <span class="fw-bold text-success">₹ {{ number_format($pay['paid_amount'], 2) }} Paid ({{ ucfirst($st) }})</span>
          </div>
          <div class="d-flex justify-content-between py-1 small">
            <span class="text-muted">Assigned Team:</span>
            <span class="fw-bold text-navy">{{ count($handlers) }} Personnel</span>
          </div>
        </div>
        <div class="s8-modal-alert p-2.5 rounded-3 d-flex gap-2">
          <i class="bi bi-info-circle-fill text-warning fs-5 flex-shrink-0"></i>
          <span style="font-size:12px; line-height:1.45; color:var(--s8-text-dark);">
            <b>Note:</b> Once submitted, the dossier will enter the official scrutiny queue and editing will be locked.
          </span>
        </div>
      </div>
      <div class="modal-footer s8-modal-footer border-0 pt-0">
        <button type="button" class="btn s8-btn-modal-cancel" data-bs-dismiss="modal">
          <i class="bi bi-arrow-left me-1"></i>Review Again
        </button>
        <button type="button" class="btn s8-btn-modal-confirm" id="btn_modal_confirm_submit">
          <i class="bi bi-send-check-fill me-1.5"></i>Confirm &amp; Submit Dossier
        </button>
      </div>
    </div>
  </div>
</div>

<!-- DOCUMENT QUICK VIEWER MODAL -->
<div class="modal fade" id="modalDocumentPreview" tabindex="-1" aria-labelledby="modalDocumentPreviewLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
    <div class="modal-content s8-modal-content">
      <div class="modal-header s8-modal-header border-bottom py-3 px-4">
        <div class="d-flex align-items-center gap-2">
          <div class="s8-modal-icon-badge">
            <i class="bi bi-file-earmark-text text-primary"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-navy mb-0" id="modalDocTitle">Document Preview</h5>
            <span class="text-muted small" id="modalDocSub">Statutory Application Attachment</span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <a href="#" target="_blank" rel="noopener noreferrer" id="btn_modal_doc_open_tab" class="btn btn-sm s8-btn-modal-open-tab">
            <i class="bi bi-box-arrow-up-right me-1"></i>Open in New Tab
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body p-3 text-center" id="modalDocBody" style="min-height:480px; max-height:80vh; overflow-y:auto; background:var(--s8-surface-subtle);">
        <div id="modalDocContent" class="d-flex align-items-center justify-content-center" style="min-height:450px;"></div>
      </div>
    </div>
  </div>
</div>

<!-- SCOPED CSS FOR STEP 8: ZERO CSS LEAKAGE, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step8-page)
     ========================================================================== */
  #gtms-step8-page {
    --s8-primary: #0F1E4D;
    --s8-primary-hover: #162a6b;
    --s8-surface: #ffffff;
    --s8-surface-subtle: #f8fafc;
    --s8-border: #e2e8f0;
    --s8-text-dark: #0f172a;
    --s8-text-muted: #64748b;
    --s8-navy-text: #0F1E4D;
    --s8-emerald: #10b981;
    --s8-amber: #f59e0b;
    --s8-rose: #ef4444;
  }

  /* Wizard Wrap & Card */
  #gtms-step8-page .wizard-wrap {
    max-width: 1040px;
    margin: 0 auto;
  }

  #gtms-step8-page .s8-main-card {
    background: var(--s8-surface);
    border: 1px solid var(--s8-border);
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05);
  }

  /* Stepper Bar */
  #gtms-step8-page .step-progress {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 28px;
    padding: 0 12px;
  }

  #gtms-step8-page .step-progress::before {
    content: "";
    position: absolute;
    top: 18px;
    left: 36px;
    right: 36px;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
  }

  #gtms-step8-page .sp-step {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    background: transparent;
  }

  #gtms-step8-page .sp-step .circ {
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

  #gtms-step8-page .sp-step.done .circ {
    background: #10b981;
    border-color: #10b981;
    color: #ffffff;
    font-size: 15px;
  }

  #gtms-step8-page .sp-step.active .circ {
    background: var(--s8-primary);
    border-color: var(--s8-primary);
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
  }

  #gtms-step8-page .sp-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
  }

  #gtms-step8-page .sp-step.active .sp-label {
    color: var(--s8-primary);
    font-weight: 700;
  }

  #gtms-step8-page .sp-step.done .sp-label {
    color: #10b981;
    font-weight: 600;
  }

  /* Header Eyebrow & Badges */
  #gtms-step8-page .s8-eyebrow {
    font-size: 11.5px;
    letter-spacing: 0.08em;
    color: #0284c7 !important;
  }

  #gtms-step8-page .s8-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--s8-navy-text);
  }

  #gtms-step8-page .s8-subtitle {
    font-size: 13px;
    color: var(--s8-text-muted);
    line-height: 1.45;
  }

  #gtms-step8-page .s8-scrutiny-pill {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-weight: 700;
    font-size: 12px;
    padding: 7px 16px;
    border-radius: 9999px;
    display: inline-flex;
    align-items: center;
  }

  /* Executive Audit Banner */
  #gtms-step8-page .s8-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px 20px;
  }

  #gtms-step8-page .s8-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #10b981;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
  }

  #gtms-step8-page .s8-banner-title {
    font-size: 14.5px;
    color: #0F1E4D;
    margin-bottom: 2px;
  }

  #gtms-step8-page .s8-banner-desc {
    font-size: 12px;
    color: #64748b;
    line-height: 1.35;
  }

  #gtms-step8-page .s8-banner-status-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 9999px;
    letter-spacing: 0.02em;
  }

  /* 4-Bento KPI Metric Cards */
  #gtms-step8-page .s8-kpi-card {
    border-radius: 12px;
    padding: 16px 18px;
    border: 1px solid var(--s8-border);
    background: var(--s8-surface);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-step8-page .s8-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
  }

  #gtms-step8-page .s8-kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--s8-text-muted);
  }

  #gtms-step8-page .s8-kpi-val {
    font-size: 18px;
    font-weight: 800;
    color: var(--s8-navy-text);
    margin-bottom: 8px;
    line-height: 1.25;
  }

  #gtms-step8-page .s8-kpi-sub {
    font-size: 12px;
  }

  #gtms-step8-page .s8-kpi-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
  }

  #gtms-step8-page .s8-kpi-icon-blue { background: #e0f2fe; color: #0284c7; }
  #gtms-step8-page .s8-kpi-icon-emerald { background: #ecfdf5; color: #10b981; }
  #gtms-step8-page .s8-kpi-icon-amber { background: #fffbeb; color: #d97706; }
  #gtms-step8-page .s8-kpi-icon-purple { background: #f3e8ff; color: #9333ea; }

  /* Chips & Tags */
  #gtms-step8-page .s8-chip-blue {
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  #gtms-step8-page .s8-chip-emerald {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  #gtms-step8-page .s8-chip-amber {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 6px;
  }

  /* Section Review Panels */
  #gtms-step8-page .s8-section-panel {
    background: var(--s8-surface);
    border: 1px solid var(--s8-border);
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
  }

  #gtms-step8-page .s8-section-panel:hover {
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
  }

  #gtms-step8-page .s8-panel-header {
    background: var(--s8-surface-subtle);
    border-bottom: 1px solid var(--s8-border);
    padding: 13px 20px;
  }

  #gtms-step8-page .s8-panel-header-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
  }

  #gtms-step8-page .s8-icon-blue { background: #e0f2fe; color: #0284c7; }
  #gtms-step8-page .s8-icon-red { background: #fee2e2; color: #ef4444; }
  #gtms-step8-page .s8-icon-teal { background: #ccfbf1; color: #0d9488; }
  #gtms-step8-page .s8-icon-green { background: #dcfce7; color: #16a34a; }
  #gtms-step8-page .s8-icon-amber { background: #fef3c7; color: #d97706; }
  #gtms-step8-page .s8-icon-yellow { background: #fef9c3; color: #ca8a04; }

  #gtms-step8-page .s8-panel-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--s8-navy-text);
  }

  #gtms-step8-page .s8-panel-step-badge {
    background: #e2e8f0;
    color: #475569;
    font-size: 10.5px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
  }

  #gtms-step8-page .s8-panel-body {
    padding: 20px 22px;
  }

  #gtms-step8-page .s8-btn-edit {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
    background: var(--s8-surface);
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 4px 12px;
    transition: all 0.2s ease;
  }

  #gtms-step8-page .s8-btn-edit:hover {
    color: #0F1E4D;
    border-color: #0F1E4D;
    background: #f1f5f9;
  }

  /* Data Tiles (Applicant & Location Sections) */
  #gtms-step8-page .s8-data-tile {
    background: var(--s8-surface-subtle);
    border: 1px solid var(--s8-border);
    border-radius: 10px;
    padding: 12px 16px;
    min-height: 70px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    transition: border-color 0.2s ease;
  }

  #gtms-step8-page .s8-data-tile:hover {
    border-color: #cbd5e1;
  }

  #gtms-step8-page .s8-tile-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--s8-text-muted);
    margin-bottom: 4px;
  }

  #gtms-step8-page .s8-tile-value {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--s8-text-dark);
  }

  /* Table Styling (Handlers & Documents) */
  #gtms-step8-page .s8-table {
    border: 1px solid var(--s8-border);
    border-radius: 8px;
    font-size: 13px;
  }

  #gtms-step8-page .s8-table thead {
    background: var(--s8-surface-subtle);
  }

  #gtms-step8-page .s8-table th {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--s8-text-muted);
    border-bottom: 1px solid var(--s8-border);
    padding: 10px 14px;
  }

  #gtms-step8-page .s8-table td {
    padding: 12px 14px;
    vertical-align: middle;
    border-bottom: 1px solid var(--s8-border);
    color: var(--s8-text-dark);
  }

  #gtms-step8-page .s8-avatar-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e0f2fe;
    color: #0284c7;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  #gtms-step8-page .s8-role-badge {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
  }

  #gtms-step8-page .s8-empty-notice {
    background: var(--s8-surface-subtle);
    border: 1px dashed var(--s8-border);
    border-radius: 8px;
    padding: 16px 20px;
    color: var(--s8-text-muted);
    font-size: 13px;
  }

  /* Payment Ledger Cards (Step 7 Review) */
  #gtms-step8-page .s8-ledger-card {
    border: 1px solid var(--s8-border);
    background: var(--s8-surface-subtle);
    border-radius: 10px;
    padding: 14px 16px;
    text-align: center;
    min-height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  #gtms-step8-page .s8-ledger-sub {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--s8-text-muted);
    margin-bottom: 2px;
  }

  #gtms-step8-page .s8-ledger-amount {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 2px;
  }

  #gtms-step8-page .s8-ledger-hint {
    font-size: 10.5px;
  }

  /* Folder Cards & Detailed Registry */
  #gtms-step8-page .s8-folder-card {
    background: var(--s8-surface-subtle);
    border: 1px solid var(--s8-border);
    border-radius: 10px;
    padding: 12px 16px;
  }

  #gtms-step8-page .s8-folder-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--s8-navy-text);
  }

  #gtms-step8-page .s8-btn-registry {
    background: transparent;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  #gtms-step8-page .s8-btn-registry:hover {
    background: #eff6ff;
    color: #1d4ed8;
    border-color: #93c5fd;
  }

  #gtms-step8-page .s8-registry-wrapper {
    max-height: 340px;
    overflow-y: auto;
    border: 1px solid var(--s8-border);
    border-radius: 8px;
  }

  #gtms-step8-page .s8-doc-attached {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 11.5px;
    font-weight: 600;
    padding: 4px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
  }

  #gtms-step8-page a.s8-doc-link {
    cursor: pointer;
    text-decoration: none;
  }

  #gtms-step8-page a.s8-doc-link:hover {
    background: #d1fae5;
    border-color: #10b981;
    color: #065f46;
    box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
    transform: translateY(-1px);
  }

  #gtms-step8-page a.s8-doc-link:active {
    transform: translateY(0);
  }

  #gtms-step8-page .s8-doc-open-icon {
    font-size: 10px;
    opacity: 0.75;
    transition: opacity 0.2s ease;
  }

  #gtms-step8-page a.s8-doc-link:hover .s8-doc-open-icon {
    opacity: 1;
  }

  #gtms-step8-page .s8-btn-action-open {
    background: #ffffff;
    color: #0F1E4D;
    border: 1px solid #cbd5e1;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 5px;
    line-height: 1.2;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  #gtms-step8-page .s8-btn-action-open:hover {
    background: #0F1E4D;
    color: #ffffff;
    border-color: #0F1E4D;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
  }

  .s8-btn-modal-open-tab {
    background: #ffffff;
    color: #0F1E4D;
    border: 1.5px solid #cbd5e1;
    font-weight: 600;
    font-size: 12.5px;
    padding: 4px 12px;
    border-radius: 6px;
    text-decoration: none;
  }

  .s8-btn-modal-open-tab:hover {
    background: #0F1E4D;
    color: #ffffff;
    border-color: #0F1E4D;
  }

  #gtms-step8-page .s8-btn-action-preview {
    background: #eff6ff;
    color: #2563eb;
    border: 1px solid #bfdbfe;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 5px;
    line-height: 1.2;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    cursor: pointer;
  }

  #gtms-step8-page .s8-btn-action-preview:hover {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
  }

  #gtms-step8-page .s8-doc-pending {
    background: #f8fafc;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
    font-size: 11.5px;
    font-weight: 500;
    padding: 3px 8px;
    border-radius: 6px;
  }

  /* Credentials Reveal & Copy Buttons */
  #gtms-step8-page .s8-btn-cred {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
    border-radius: 4px;
    padding: 3px 6px;
    font-size: 11px;
    line-height: 1;
    transition: all 0.2s ease;
  }

  #gtms-step8-page .s8-btn-cred:hover {
    background: #f1f5f9;
    color: #0F1E4D;
    border-color: #94a3b8;
  }

  /* Pre-Submission Declaration Card */
  #gtms-step8-page .s8-declaration-card {
    background: #f0fdf4;
    border: 1.5px solid #86efac;
    border-radius: 12px;
    padding: 18px 22px;
  }

  #gtms-step8-page .s8-declaration-icon-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #dcfce7;
    color: #16a34a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  #gtms-step8-page .s8-declaration-title {
    font-size: 14px;
    color: #14532d;
  }

  #gtms-step8-page .s8-declaration-text {
    font-size: 12.5px;
    color: #166534;
    line-height: 1.45;
  }

  #gtms-step8-page .s8-check-confirm .form-check-input {
    width: 18px;
    height: 18px;
    margin-top: 2px;
    cursor: pointer;
    border-color: #86efac;
  }

  #gtms-step8-page .s8-check-confirm .form-check-input:checked {
    background-color: #16a34a;
    border-color: #16a34a;
  }

  #gtms-step8-page .s8-check-confirm label {
    font-size: 13px;
    color: #14532d;
    cursor: pointer;
    padding-left: 4px;
  }

  /* Wizard Actions & Buttons */
  #gtms-step8-page .s8-actions {
    border-color: var(--s8-border) !important;
  }

  #gtms-step8-page .s8-btn-back {
    background: #ffffff;
    color: #334155;
    border: 1.5px solid #cbd5e1 !important;
    font-size: 13.5px;
    font-weight: 600;
    padding: 9px 22px;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  #gtms-step8-page .s8-btn-back:hover {
    background: #f1f5f9;
    border-color: #94a3b8 !important;
    color: #0f172a;
  }

  #gtms-step8-page .s8-btn-submit {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border: 1px solid #059669;
    font-size: 14px;
    font-weight: 700;
    padding: 10px 28px;
    border-radius: 8px;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    transition: all 0.2s ease;
  }

  #gtms-step8-page .s8-btn-submit:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
    transform: translateY(-1px);
  }

  /* Modal Styling */
  .s8-modal-content {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
  }

  .s8-modal-icon-badge {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: #ecfdf5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .s8-modal-recap {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
  }

  .s8-modal-alert {
    background: #fffbeb;
    border: 1px solid #fde68a;
  }

  .s8-btn-modal-cancel {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    font-weight: 600;
    font-size: 13px;
    padding: 8px 18px;
    border-radius: 8px;
  }

  .s8-btn-modal-cancel:hover {
    background: #e2e8f0;
    color: #1e293b;
  }

  .s8-btn-modal-confirm {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    border: 1px solid #059669;
    font-weight: 700;
    font-size: 13.5px;
    padding: 8px 22px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
  }

  .s8-btn-modal-confirm:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
  }

  /* ==========================================================================
     DARK MODE PARITY (Strictly Scoped to body[data-theme-version="dark"] #gtms-step8-page)
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step8-page {
    --s8-surface: #1e293b;
    --s8-surface-subtle: #0f172a;
    --s8-border: #334155;
    --s8-text-dark: #f8fafc;
    --s8-text-muted: #94a3b8;
    --s8-navy-text: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-main-card {
    background: var(--s8-surface);
    border-color: var(--s8-border);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
  }

  /* Dark Stepper */
  body[data-theme-version="dark"] #gtms-step8-page .step-progress::before {
    background: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-step .circ {
    background: #0f172a;
    border-color: #334155;
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-step.done .circ {
    background: #059669;
    border-color: #059669;
    color: #ffffff;
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-step.active .circ {
    background: #38bdf8;
    border-color: #38bdf8;
    color: #0f172a;
    box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.25);
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-label {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-step.active .sp-label {
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .sp-step.done .sp-label {
    color: #34d399;
  }

  /* Dark Banner */
  body[data-theme-version="dark"] #gtms-step8-page .s8-banner-card {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-banner-title {
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-banner-desc {
    color: #94a3b8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-banner-icon-badge {
    background: rgba(16, 185, 129, 0.15);
    color: #34d399;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-banner-status-pill {
    background: #38bdf8;
    color: #0f172a;
  }

  /* Dark KPI Bento Cards */
  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-card {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-val {
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-icon-blue { background: rgba(2, 132, 199, 0.18); color: #38bdf8; }
  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-icon-emerald { background: rgba(16, 185, 129, 0.18); color: #34d399; }
  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-icon-amber { background: rgba(245, 158, 11, 0.18); color: #fbbf24; }
  body[data-theme-version="dark"] #gtms-step8-page .s8-kpi-icon-purple { background: rgba(147, 51, 234, 0.18); color: #c084fc; }

  /* Dark Review Panels */
  body[data-theme-version="dark"] #gtms-step8-page .s8-section-panel {
    background: #1e293b;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-panel-header {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-panel-step-badge {
    background: #334155;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-edit {
    background: #1e293b;
    border-color: #475569;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-edit:hover {
    background: #334155;
    border-color: #38bdf8;
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-data-tile {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-data-tile:hover {
    border-color: #475569;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-tile-value {
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-table {
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-table thead {
    background: #0f172a;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-table th,
  body[data-theme-version="dark"] #gtms-step8-page .s8-table td {
    border-color: #334155;
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-role-badge {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-avatar-icon {
    background: rgba(2, 132, 199, 0.2);
    color: #38bdf8;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-ledger-card {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-folder-card {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-folder-title {
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-registry {
    border-color: #3b82f6;
    color: #60a5fa;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-registry:hover {
    background: rgba(59, 130, 246, 0.15);
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-registry-wrapper {
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-doc-attached {
    background: rgba(16, 185, 129, 0.15);
    border-color: rgba(52, 211, 153, 0.35);
    color: #34d399;
  }

  body[data-theme-version="dark"] #gtms-step8-page a.s8-doc-link:hover {
    background: rgba(16, 185, 129, 0.28);
    border-color: #34d399;
    color: #6ee7b7;
    box-shadow: 0 2px 8px rgba(52, 211, 153, 0.25);
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-action-open {
    background: #0f172a;
    color: #38bdf8;
    border-color: #334155;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-action-open:hover {
    background: #38bdf8;
    color: #0f172a;
    border-color: #38bdf8;
  }

  body[data-theme-version="dark"] .s8-btn-modal-open-tab {
    background: #0f172a;
    color: #38bdf8;
    border-color: #334155;
  }

  body[data-theme-version="dark"] .s8-btn-modal-open-tab:hover {
    background: #334155;
    color: #f8fafc;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-action-preview {
    background: rgba(37, 99, 235, 0.18);
    color: #60a5fa;
    border-color: rgba(59, 130, 246, 0.4);
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-action-preview:hover {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-doc-pending {
    background: #0f172a;
    border-color: #334155;
    color: #64748b;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-cred {
    background: #0f172a;
    border-color: #334155;
    color: #cbd5e1;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-cred:hover {
    background: #334155;
    color: #38bdf8;
  }

  /* Dark Pre-Submission Declaration */
  body[data-theme-version="dark"] #gtms-step8-page .s8-declaration-card {
    background: rgba(16, 185, 129, 0.1);
    border-color: rgba(52, 211, 153, 0.4);
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-declaration-icon-badge {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-declaration-title {
    color: #6ee7b7;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-declaration-text {
    color: #a7f3d0;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-check-confirm label {
    color: #d1fae5;
  }

  /* Dark Actions */
  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-back {
    background: #0f172a;
    color: #cbd5e1;
    border-color: #334155 !important;
  }

  body[data-theme-version="dark"] #gtms-step8-page .s8-btn-back:hover {
    background: #334155;
    border-color: #475569 !important;
    color: #f8fafc;
  }

  /* Dark Modal */
  body[data-theme-version="dark"] .s8-modal-content {
    background: #1e293b;
    border-color: #334155;
    color: #f1f5f9;
  }

  body[data-theme-version="dark"] .s8-modal-recap {
    background: #0f172a;
    border-color: #334155;
  }

  body[data-theme-version="dark"] .s8-modal-alert {
    background: rgba(245, 158, 11, 0.12);
    border-color: rgba(245, 158, 11, 0.3);
  }

  body[data-theme-version="dark"] .s8-btn-modal-cancel {
    background: #0f172a;
    color: #cbd5e1;
    border-color: #334155;
  }

  body[data-theme-version="dark"] .s8-btn-modal-cancel:hover {
    background: #334155;
    color: #f8fafc;
  }
</style>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
  // Quick Document Preview Modal Handler
  $(document).on('click', '.s8-btn-action-preview', function(e) {
    e.preventDefault();
    var fileUrl = $(this).attr('data-file-url');
    var fileName = $(this).attr('data-file-name') || 'Document Preview';
    var fileExt = ($(this).attr('data-file-ext') || '').toLowerCase();

    if (!fileUrl || fileUrl === '#' || fileUrl.endsWith('/#')) {
      if (typeof toastr !== 'undefined') toastr.warning('Document file path is not available.');
      else alert('Document file path is not available.');
      return;
    }

    $('#modalDocTitle').text(fileName);
    $('#modalDocSub').text('File type: .' + fileExt.toUpperCase());
    $('#btn_modal_doc_open_tab').attr('href', fileUrl);

    var content = $('#modalDocContent');
    content.empty();

    if (['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'].indexOf(fileExt) !== -1) {
      content.html('<img src="' + fileUrl + '" class="img-fluid rounded shadow-sm" style="max-height:70vh; object-fit:contain;" alt="' + fileName + '">');
    } else if (fileExt === 'pdf') {
      content.html('<iframe src="' + fileUrl + '#toolbar=1&navpanes=0" style="width:100%; height:70vh; border:none; border-radius:8px;"></iframe>');
    } else {
      content.html(
        '<div class="p-4 text-center">' +
          '<i class="bi bi-file-earmark-arrow-down text-primary" style="font-size:3rem;"></i>' +
          '<h6 class="fw-bold text-navy mt-3">' + fileName + '</h6>' +
          '<p class="text-muted small">This file format (.' + fileExt.toUpperCase() + ') is best opened in its native viewer.</p>' +
          '<a href="' + fileUrl + '" target="_blank" class="btn btn-primary btn-sm px-3 mt-2">' +
            '<i class="bi bi-box-arrow-up-right me-1"></i> Open / Download File' +
          '</a>' +
        '</div>'
      );
    }

    var modalEl = document.getElementById('modalDocumentPreview');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.show();
    } else {
      window.open(fileUrl, '_blank');
    }
  });

  // Guard Against Missing File URLs
  $(document).on('click', '.s8-doc-link, .s8-btn-action-open', function(e) {
    var url = $(this).attr('href');
    if (!url || url === '#' || url.endsWith('/#')) {
      e.preventDefault();
      if (typeof toastr !== 'undefined') toastr.warning('Document file path is not available or file was removed.');
      else alert('Document file path is not available or file was removed.');
    }
  });

  // Toggle MIMAS Password Reveal in Step 8 Preview
  $('#btn_toggle_preview_pass').on('click', function(e) {
    e.preventDefault();
    var masked = $('#preview_pass_masked');
    var plain = $('#preview_pass_plain');
    var icon = $('#icon_preview_pass');
    if (plain.is(':visible')) {
      plain.hide();
      masked.show();
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    } else {
      masked.hide();
      plain.show();
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    }
  });

  // Copy Password to Clipboard
  $('#btn_copy_preview_pass').on('click', function(e) {
    e.preventDefault();
    var pass = $(this).attr('data-clipboard-text');
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(pass).then(function() {
        if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
        else alert('MIMAS Password copied to clipboard!');
      });
    } else {
      var temp = $('<input>');
      $('body').append(temp);
      temp.val(pass).select();
      document.execCommand('copy');
      temp.remove();
      if (typeof toastr !== 'undefined') toastr.success('MIMAS Password copied to clipboard!');
      else alert('MIMAS Password copied to clipboard!');
    }
  });

  // Modal Open Trigger with Compliance Check
  $('#btn_open_submit_modal').on('click', function(e) {
    e.preventDefault();
    var ack = $('#confirm_scrutiny_ack').is(':checked');
    if (!ack) {
      if (typeof toastr !== 'undefined') {
        toastr.warning('Please confirm the statutory declaration checkbox before submitting.');
      } else {
        alert('Please confirm the statutory declaration checkbox before submitting.');
      }
      $('#confirm_scrutiny_ack').focus();
      return;
    }

    var modalEl = document.getElementById('modalSubmitConfirm');
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
      var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
      modal.show();
    } else {
      // Fallback if bootstrap modal js not initialized
      $('#btn_final_submit').trigger('click');
    }
  });

  // Confirm in Modal Triggers Actual Submission
  $('#btn_modal_confirm_submit').on('click', function(e) {
    e.preventDefault();
    $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1.5" role="status"></span> Submitting Application...');
    $('#btn_final_submit').trigger('click');
  });

  // Prevent Double Submissions & Show Progress Spinner
  $('#final_submit_form').on('submit', function(e) {
    $('#btn_open_submit_modal').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status"></span> Submitting Dossier...');
  });
});
</script>
@endsection
