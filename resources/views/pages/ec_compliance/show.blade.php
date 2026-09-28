@extends('layouts.app')
@section('title', 'EC Compliance Dossier — ' . $comp->compliance_no)
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <a href="{{ route('ec-compliance.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Register
          </a>
          <span class="badge bg-primary fs-6">{{ $comp->compliance_no }}</span>
          @php
            $st = strtolower(trim($comp->status));
            $cStyle = match(true) {
              in_array($st, ['completed']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
              in_array($st, ['uploaded_to_parivesh', 'report_prepared', 'lab_analysed']) => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
              in_array($st, ['call not picked', 'client not responding', 'rejected']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
              in_array($st, ['archived']) => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
              in_array($st, ['draft']) => 'background:#f8fafc; color:#64748b; border:1px solid #cbd5e1;',
              default => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
            };
          @endphp
          <span class="badge fs-6" style="{{ $cStyle }}">{{ ucwords(str_replace('_', ' ', $comp->status)) }}</span>
          @if($comp->parivesh_acknowledgement_no)
            <span class="badge bg-success font-monospace fs-6">
              <i class="bi bi-check2-circle me-1"></i>Parivesh: {{ $comp->parivesh_acknowledgement_no }}
            </span>
          @endif
        </div>
        <h3 class="fw-bold text-navy mb-0">{{ $comp->project_name ?: 'Environmental Clearance Half-Yearly Compliance Dossier' }}</h3>
        @if($comp->status_notes)
          <div class="mt-2 text-muted small"><i class="fa fa-comment-dots text-warning me-1"></i><strong>Status Note:</strong> {{ $comp->status_notes }}</div>
        @endif
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalUpdateCompStatus">
          <i class="bi bi-tag me-1"></i> Update Status
        </button>
        <button onclick="window.print()" class="btn btn-outline-secondary">
          <i class="bi bi-printer me-1"></i> Print Compliance Dossier
        </button>
        <a href="{{ route('ec-compliance.step', ['step' => 1, 'resume' => $comp->id]) }}" class="btn btn-navy">
          <i class="bi bi-pencil-square me-1"></i> Edit Filing
        </a>
        <a href="{{ route('dgps-survey.step', 1) }}" 
           class="btn btn-sm" 
           style="background: linear-gradient(135deg, #312e81 0%, #6366f1 100%); color: #fff; border: none;"
           title="Initiate DGPS Boundary Pillar Survey for this quarry site">
          <i class="bi bi-geo-alt-fill me-1"></i> Start DGPS Survey
        </a>
      </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0F1E4D !important;">
          <small class="text-muted text-uppercase fw-semibold">Client / Company</small>
          <h5 class="fw-bold text-navy mt-1 mb-1">{{ $comp->customer?->company_name ?: ($comp->customer?->customer_name ?: 'N/A') }}</h5>
          <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $comp->primary_contact_person ?: ($comp->customer?->customer_name ?: '—') }} &middot; {{ $comp->primary_phone ?: ($comp->customer?->mobile_num ?: '—') }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0284c7 !important;">
          <small class="text-muted text-uppercase fw-semibold">Compliance Period</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">{{ $comp->compliance_period }}</h5>
          <small class="text-muted">Due: {{ $comp->submission_due_date ? $comp->submission_due_date->format('d M Y') : 'June 1st / Dec 1st' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #10b981 !important;">
          <small class="text-muted text-uppercase fw-semibold">4 Regulatory Pillars</small>
          <h5 class="fw-bold text-success mt-1 mb-1">{{ $comp->documents->count() }} Attached Files</h5>
          <small class="text-muted">Docs + NABL Lab + Report + Parivesh</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #f59e0b !important;">
          <small class="text-muted text-uppercase fw-semibold">Compliance Service Ledger</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">₹{{ number_format($comp->product_value, 2) }}</h5>
          <small class="{{ $comp->pending_amount > 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
            {{ $comp->pending_amount > 0 ? '₹' . number_format($comp->pending_amount, 2) . ' Pending' : 'Fully Settled' }}
          </small>
        </div>
      </div>
    </div>

    {{-- Main Content Grid: 4 Regulatory Pillars --}}
    <div class="row g-4">
      <div class="col-lg-8">
        {{-- PILLAR 1: 1. DOCUMENTS (19 Statutory Items) --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-folder-open text-primary me-2"></i> 1. Statutory Documents Checklist (19 Items)
            </h5>
            @php
              $p1Docs = $comp->documents->where('folder_category', 'documents');
            @endphp
            <span class="badge bg-light text-navy border">{{ $p1Docs->count() }} Attached</span>
          </div>
          <div class="card-body p-0">
            @php
              $pillar1Items = [
                '1. 500m Letter / Certificate',
                '2. 300m Letter / Certificate',
                '3. Consent to Operate (CTO) — TNPCB',
                '4. Registered Lease Deed Agreement',
                '5. Explosive License & Magazine Certificate',
                '6. Quarry Workers Insurance Policy',
                '7. Newspaper Advertisement (English & Tamil)',
                '8. Quarry Mandatory Name Board Photo',
                '9. Greenbelt Plantation & Barbed Wire Fencing Photos',
                '10. Labour Rest Shed & Sanitation Toilet Photos',
                '11. First Aid Box Facility Photo',
                '12. RO Drinking Water Facility Photo',
                '13. Haul Road Water Sprinkling Facility Photo',
                '14. Safety Equipment PPE Kit Distribution Photo',
                '15. Corporate Social Responsibility (CSR) Photos',
                '16. Corporate Environment Responsibility (CER) Photos',
                '17. Last Mineral Transit Permit / Dispatch Details',
                '18. Tarpaulin Covered Transport Trucks Photo',
                '19. CCTV Camera & Security Installation Photo'
              ];
            @endphp
            <div class="list-group list-group-flush" style="max-height:480px; overflow-y:auto;">
              @foreach($pillar1Items as $item)
                @php
                  $docMatch = $p1Docs->firstWhere('document_name', $item);
                @endphp
                <div class="list-group-item py-2 px-3 d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2 text-truncate">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $docMatch ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width:28px; height:28px; font-size:12px;">
                      <i class="bi {{ $docMatch ? 'bi-check-lg' : 'bi-file-earmark' }}"></i>
                    </div>
                    <span class="small fw-semibold text-dark text-truncate">{{ $item }}</span>
                  </div>
                  <div class="flex-shrink-0 ms-2">
                    @if($docMatch && $docMatch->file_path)
                      <a href="{{ asset('storage/' . $docMatch->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size:11px;">
                        <i class="bi bi-eye"></i> View
                      </a>
                    @else
                      <span class="badge bg-light text-muted border py-0" style="font-size:10px;">Pending</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- PILLAR 2: 2. SITE ANALYSIS STUDY (4 NABL Accredited Laboratory Tests) --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-flask text-warning me-2"></i> 2. Site Analysis Study (NABL Laboratory Tests)
            </h5>
            @php
              $p2Docs = $comp->documents->where('folder_category', 'site_analysis');
            @endphp
            <span class="badge bg-light text-navy border">{{ $p2Docs->count() }} / 4 Reports</span>
          </div>
          <div class="card-body p-0">
            @php
              $pillar2Items = [
                '1. Air Quality Monitoring Report'   => 'NABL ambient air quality analysis (PM10, PM2.5, SO2, NOx)',
                '2. Noise Level Monitoring Report'   => 'Day and Night equivalent noise level monitoring (dB(A) Leq)',
                '3. Soil Sample Analysis Report'     => 'Physico-chemical profile, heavy metals & soil fertility analysis',
                '4. Water Sample Test Report'        => 'Groundwater & surface runoff quality test against IS 10500 standards'
              ];
            @endphp
            <div class="list-group list-group-flush">
              @foreach($pillar2Items as $tName => $tMeta)
                @php
                  $tMatch = $p2Docs->firstWhere('document_name', $tName);
                @endphp
                <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $tMatch ? 'bg-success text-white' : 'bg-warning-subtle text-warning' }}" style="width:34px; height:34px;">
                      <i class="bi {{ $tMatch ? 'bi-check-lg' : 'bi-droplet-half' }}"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-navy">{{ $tName }}</div>
                      <div class="text-muted small">{{ $tMeta }}</div>
                    </div>
                  </div>
                  <div>
                    @if($tMatch && $tMatch->file_path)
                      <a href="{{ asset('storage/' . $tMatch->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-1 px-3">
                        <i class="bi bi-eye me-1"></i> View Test Report
                      </a>
                    @else
                      <span class="badge bg-light text-muted border">Pending Lab Testing</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- PILLAR 3: 3. REPORT PREPARATION --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-file-alt text-info me-2"></i> 3. Statutory Compliance Report
            </h5>
            @php
              $p3Docs = $comp->documents->where('folder_category', 'report');
            @endphp
            <span class="badge bg-light text-navy border">{{ $p3Docs->count() }} / 3 Documents</span>
          </div>
          <div class="card-body p-0">
            @php
              $pillar3Items = [
                '1. Front Page / Cover Sheet'                   => 'Official project title page with SEIAA EC reference and quarry credentials',
                '2. Covering Letter to MoEFCC / SEIAA'         => 'Formal transmittal covering letter to Regional Office MoEFCC and SEIAA-TN',
                '3. Comprehensive EC-Compliance Report'         => 'Condition-wise point-by-point statutory compliance verification report'
              ];
            @endphp
            <div class="list-group list-group-flush">
              @foreach($pillar3Items as $rName => $rMeta)
                @php
                  $rMatch = $p3Docs->firstWhere('document_name', $rName);
                @endphp
                <div class="list-group-item p-3 d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 {{ $rMatch ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width:34px; height:34px;">
                      <i class="bi {{ $rMatch ? 'bi-check-lg' : 'bi-file-text' }}"></i>
                    </div>
                    <div>
                      <div class="fw-bold text-navy">{{ $rName }}</div>
                      <div class="text-muted small">{{ $rMeta }}</div>
                    </div>
                  </div>
                  <div>
                    @if($rMatch && $rMatch->file_path)
                      <a href="{{ asset('storage/' . $rMatch->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-1 px-3">
                        <i class="bi bi-eye me-1"></i> View Document
                      </a>
                    @else
                      <span class="badge bg-light text-muted border">In Draft</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        {{-- PILLAR 4: 4. UPLOADING REPORT (MoEFCC Parivesh Portal) --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-cloud-upload-alt text-success me-2"></i> 4. Uploading Report (MoEFCC Parivesh Portal)
            </h5>
            @if($comp->parivesh_acknowledgement_no)
              <span class="badge bg-success">Uploaded &amp; Synced</span>
            @else
              <span class="badge bg-light text-muted border">Pending Upload</span>
            @endif
          </div>
          <div class="card-body p-3">
            <div class="row g-3 align-items-center">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="text-muted small fw-semibold text-uppercase">Parivesh Acknowledgement No:</span>
                  <div class="h5 fw-bold text-navy font-monospace mt-1 mb-0">{{ $comp->parivesh_acknowledgement_no ?: 'Pending Submission' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-light rounded-3 border">
                  <span class="text-muted small fw-semibold text-uppercase">Portal Submission Date:</span>
                  <div class="h5 fw-bold text-dark mt-1 mb-0">{{ $comp->parivesh_uploaded_date ? $comp->parivesh_uploaded_date->format('d M Y') : 'Not submitted yet' }}</div>
                </div>
              </div>
            </div>
            @php
              $pariveshDoc = $comp->documents->firstWhere('folder_category', 'parivesh_upload');
            @endphp
            @if($pariveshDoc && $pariveshDoc->file_path)
              <div class="mt-3 p-3 bg-success-subtle rounded-3 border border-success-subtle d-flex align-items-center justify-content-between">
                <div>
                  <i class="bi bi-file-earmark-check-fill text-success fs-4 me-2"></i>
                  <span class="fw-bold text-navy">Official MoEFCC Parivesh Acknowledgement Receipt</span>
                </div>
                <a href="{{ asset('storage/' . $pariveshDoc->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success">
                  <i class="bi bi-eye me-1"></i> View Receipt PDF
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Sidebar Cards: Handlers & Payments --}}
      <div class="col-lg-4">
        {{-- Statutory EC Linkage & Certificate --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-link-45deg text-primary me-2"></i> Statutory EC Linkage
            </h6>
            @if($comp->ec_certificate_file)
              <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2"></i> File Attached</span>
            @endif
          </div>
          <div class="card-body p-3">
            <div class="mb-3">
              <span class="text-muted small fw-semibold text-uppercase">Primary Contact Person:</span>
              <div class="fw-bold text-dark mt-1">
                {{ $comp->primary_contact_person ?: ($comp->customer?->customer_name ?: '—') }}
                <span class="text-muted fw-normal">({{ $comp->primary_phone ?: ($comp->customer?->mobile_num ?: '—') }})</span>
              </div>
            </div>
            @if($comp->secondary_contact_person || $comp->secondary_phone)
              <div class="mb-3">
                <span class="text-muted small fw-semibold text-uppercase">Secondary Contact Person:</span>
                <div class="fw-bold text-dark mt-1">
                  {{ $comp->secondary_contact_person ?: '—' }}
                  <span class="text-muted fw-normal">({{ $comp->secondary_phone ?: '—' }})</span>
                </div>
              </div>
            @endif
            <div class="mb-3">
              <span class="text-muted small fw-semibold text-uppercase">Linked Environment Project:</span>
              <div class="fw-bold text-dark mt-1">
                {{ $comp->environment_project_name ?: ($comp->environmentProject?->project_name ?: 'Standalone Compliance Filing') }}
              </div>
            </div>
            <div>
              <span class="text-muted small fw-semibold text-uppercase">Prior EC Certificate:</span>
              @if($comp->ec_certificate_file)
                <div class="mt-2 p-2 bg-light rounded border d-flex align-items-center justify-content-between">
                  <div class="text-truncate me-2 small">
                    <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                    <span class="fw-semibold text-dark">{{ $comp->ec_certificate_name ?: basename($comp->ec_certificate_file) }}</span>
                  </div>
                  <a href="{{ asset('storage/' . $comp->ec_certificate_file) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:11px;">
                    <i class="bi bi-eye"></i> View
                  </a>
                </div>
              @else
                <div class="text-muted small mt-1">No prior certificate document attached.</div>
              @endif
            </div>
          </div>
        </div>

        {{-- Handling Team --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-people-fill text-primary me-2"></i> Compliance Handling Team
            </h6>
            <span class="badge bg-light text-dark border">{{ $comp->handlers->count() }} Persons</span>
          </div>
          <div class="card-body p-3">
            @forelse($comp->handlers as $handler)
              <div class="d-flex align-items-start justify-content-between py-2 border-bottom">
                <div>
                  <div class="fw-bold text-dark">{{ $handler->name }}</div>
                  <div class="badge bg-light text-primary border" style="font-size:11px;">{{ $handler->role }}</div>
                  @if($handler->notes)
                    <div class="small text-muted mt-1">{{ $handler->notes }}</div>
                  @endif
                </div>
              </div>
            @empty
              <p class="text-muted small mb-0">No compliance handling personnel assigned.</p>
            @endforelse
          </div>
        </div>

        {{-- Financial & Billing Ledger --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-receipt text-success me-2"></i> Financial &amp; Billing Ledger
            </h6>
            @php
              $pBadge = match($comp->payment_status) {
                'paid'    => 'bg-success',
                'partial' => 'bg-warning text-dark',
                default   => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
            @endphp
            <span class="badge {{ $pBadge }}">{{ ucfirst($comp->payment_status ?: 'pending') }}</span>
          </div>
          <div class="card-body p-3">
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Quoted Service Fee:</span>
              <strong class="text-navy">₹{{ number_format($comp->product_value, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Paid Amount:</span>
              <strong class="text-success">₹{{ number_format($comp->paid_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Pending Balance:</span>
              <strong class="text-danger">₹{{ number_format($comp->pending_amount, 2) }}</strong>
            </div>
            @if($comp->payments->isNotEmpty() && $comp->payments->first()->notes)
              <div class="mt-3 p-2 bg-light rounded small">
                <span class="text-muted fw-semibold">Payment Notes / Ref:</span>
                <div class="text-dark">{{ $comp->payments->first()->notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Workflow Promotion: Stage 6 → 7 (DGPS Survey) --}}
    <div class="row mb-4">
      <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius:12px; background: linear-gradient(135deg, #f0f4ff 0%, #ede9fe 100%); border-left: 4px solid #6366f1 !important;">
          <div class="card-body d-flex align-items-center justify-content-between py-3 px-4">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px; height:48px; background:#6366f1; color:#fff;">
                <i class="bi bi-geo-alt-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-0" style="color:#312e81;">Next Stage: DGPS Boundary Pillar Survey</h6>
                <p class="text-muted small mb-0">Establish GPS-verified boundary coordinates for quarry lease area demarcation using differential GNSS equipment.</p>
              </div>
            </div>
            <a href="{{ route('dgps-survey.step', 1) }}" class="btn btn-sm px-4 shadow-sm" style="background:#6366f1; color:#fff;">
              <i class="bi bi-arrow-right me-1"></i> Start DGPS Survey
            </a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Dynamic Update EC Compliance Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalUpdateCompStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalUpdateCompStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalUpdateCompStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #059669 50%, #10B981 100%);
  }
  #modalUpdateCompStatus .status-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(15, 30, 77, 0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #0F1E4D;
    font-size: 1.25rem;
    flex-shrink: 0;
  }
  #modalUpdateCompStatus .preset-status-btn {
    font-size: 0.76rem;
    font-weight: 500;
    padding: 0.32rem 0.65rem;
    border-radius: 8px;
    transition: all 0.15s ease-in-out;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    cursor: pointer;
  }
  #modalUpdateCompStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalUpdateCompStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalUpdateCompStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalUpdateCompStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalUpdateCompStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalUpdateCompStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalUpdateCompStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalUpdateCompStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalUpdateCompStatus" tabindex="-1" aria-labelledby="modalUpdateCompStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formCompShowUpdateStatus" action="{{ route('ec-compliance.status', $comp->id) }}">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-clipboard-check"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalUpdateCompStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update EC Compliance Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i>{{ $comp->compliance_no }}
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;">
                  <i class="fa fa-building me-1 opacity-50"></i>{{ $comp->customer?->company_name ?: ($comp->customer?->customer_name ?: 'Applicant') }}
                </span>
              </div>
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        {{-- Modal Body --}}
        <div class="modal-body p-4 pt-3">
          
          {{-- Live Status Badge Preview Card --}}
          <div class="p-3 mb-3 rounded-3 border" style="background:#f8fafc; border-color:#e2e8f0 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-uppercase fw-bold text-muted" style="font-size:0.68rem; letter-spacing:0.05em;">
                <i class="fa fa-eye me-1 text-primary"></i>Live Badge Preview
              </span>
              <span class="text-muted" style="font-size:0.7rem;">Real-time dossier appearance</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span id="comp_show_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                {{ ucwords(str_replace('_', ' ', $comp->status)) }}
              </span>
              <span class="text-muted small ms-auto" id="comp_show_live_status_category_hint" style="font-size:0.72rem;">Current Status</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="comp_show_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="comp_show_status_input" class="form-control border-start-0 ps-1"
                     list="comp_show_status_suggestions" value="{{ $comp->status }}" placeholder="e.g. documents_collected, completed, call not picked..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="comp_show_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="comp_show_status_suggestions">
              <option value="draft">Draft</option>
              <option value="documents_collected">Documents Collected</option>
              <option value="lab_analysed">Lab Analysed (NABL)</option>
              <option value="report_prepared">Report Prepared</option>
              <option value="uploaded_to_parivesh">Uploaded to Parivesh</option>
              <option value="completed">Completed</option>
              <option value="call not picked">Call Not Picked</option>
              <option value="client not responding">Client Not Responding</option>
              <option value="site sampling pending">Site Sampling Pending</option>
              <option value="client review pending">Client Review Pending</option>
              <option value="payment pending">Payment Pending</option>
              <option value="archived">Archived</option>
            </datalist>
          </div>

          {{-- Categorized Preset Pills --}}
          <div class="mb-3">
            {{-- Workflow Milestones --}}
            <div class="mb-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-diagram-project me-1 text-primary"></i>Standard Workflow Stages
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="comp_show_group_workflow_presets">
                <button type="button" class="preset-status-btn" data-value="draft">
                  <i class="fa fa-file-pen text-secondary"></i>Draft
                </button>
                <button type="button" class="preset-status-btn" data-value="documents_collected">
                  <i class="fa fa-folder-open text-info"></i>Docs Collected
                </button>
                <button type="button" class="preset-status-btn" data-value="lab_analysed">
                  <i class="fa fa-vial-virus text-primary"></i>Lab Analysed
                </button>
                <button type="button" class="preset-status-btn" data-value="report_prepared">
                  <i class="fa fa-file-waveform text-info"></i>Report Prepared
                </button>
                <button type="button" class="preset-status-btn" data-value="uploaded_to_parivesh">
                  <i class="fa fa-cloud-arrow-up text-primary"></i>Parivesh Uploaded
                </button>
                <button type="button" class="preset-status-btn" data-value="completed">
                  <i class="fa fa-circle-check text-success"></i>Completed
                </button>
                <button type="button" class="preset-status-btn" data-value="archived">
                  <i class="fa fa-box-archive text-muted"></i>Archived
                </button>
              </div>
            </div>

            {{-- Operational Delays & Follow-ups --}}
            <div>
              <div class="d-flex align-items-center justify-content-between mb-1">
                <span class="fw-semibold text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                  <i class="fa fa-phone-slash me-1 text-danger"></i>Operational Follow-ups & Delays
                </span>
              </div>
              <div class="d-flex flex-wrap gap-1" id="comp_show_group_operational_presets">
                <button type="button" class="preset-status-btn" data-value="call not picked">
                  <i class="fa fa-phone-slash text-danger"></i>Call Not Picked
                </button>
                <button type="button" class="preset-status-btn" data-value="client not responding">
                  <i class="fa fa-user-clock text-warning"></i>Client Not Responding
                </button>
                <button type="button" class="preset-status-btn" data-value="site sampling pending">
                  <i class="fa fa-droplet text-info"></i>Sampling Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="client review pending">
                  <i class="fa fa-user-check text-warning"></i>Client Review
                </button>
                <button type="button" class="preset-status-btn" data-value="payment pending">
                  <i class="fa fa-receipt text-secondary"></i>Payment Pending
                </button>
              </div>
            </div>
          </div>

          {{-- Follow-up Remarks & Call Log --}}
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="comp_show_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Follow-up Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="comp_show_notes_counter" style="font-size:0.72rem;">{{ strlen($comp->status_notes ?? '') }} / 1000</span>
            </div>
            <textarea name="status_notes" id="comp_show_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter follow-up remarks, NABL lab sampling status, monitoring schedule, client logs..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;">{{ $comp->status_notes }}</textarea>

            {{-- Quick Chip Inserts for Notes --}}
            <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
              <span class="text-muted small me-1" style="font-size:0.7rem;"><i class="fa fa-bolt me-1 text-warning"></i>Quick log:</span>
              <button type="button" class="quick-note-chip" data-text="Called applicant; line was busy. Scheduled follow-up.">+ Call Busy</button>
              <button type="button" class="quick-note-chip" data-text="Air & water environmental sampling scheduled for next week.">+ Sampling Scheduled</button>
              <button type="button" class="quick-note-chip" data-text="Awaiting certified NABL laboratory test results.">+ Lab Results Awaited</button>
              <button type="button" class="quick-note-chip" data-text="Parivesh half-yearly compliance filing confirmed.">+ Parivesh Filed</button>
            </div>
          </div>

        </div>

        {{-- Modal Footer --}}
        <div class="modal-footer border-0 pt-0 pb-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
          <div class="text-muted small" style="font-size:0.72rem;">
            <kbd style="background:#e2e8f0; color:#475569; padding:2px 5px; border-radius:4px; font-size:0.68rem;">Ctrl</kbd> + <kbd style="background:#e2e8f0; color:#475569; padding:2px 5px; border-radius:4px; font-size:0.68rem;">Enter</kbd> to save
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" style="font-weight:500; font-size:0.85rem;">Cancel</button>
            <button type="submit" class="btn btn-save-status shadow-sm" id="comp_show_btn_submit_status">
              <i class="fa fa-floppy-disk me-1"></i> Update Status
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modalCompShowStatus = document.getElementById('modalUpdateCompStatus');
  const formCompShowStatus = document.getElementById('formCompShowUpdateStatus');
  const inputStatus = document.getElementById('comp_show_status_input');
  const textareaNotes = document.getElementById('comp_show_status_notes');
  const liveBadge = document.getElementById('comp_show_live_status_badge');
  const liveCategoryHint = document.getElementById('comp_show_live_status_category_hint');
  const notesCounter = document.getElementById('comp_show_notes_counter');
  const btnClear = document.getElementById('comp_show_btn_clear_status');
  const btnSubmit = document.getElementById('comp_show_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalUpdateCompStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalUpdateCompStatus .quick-note-chip');

  function renderCompShowLiveStatus(rawStatus) {
    if (!liveBadge) return;
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Draft';
    
    liveBadge.textContent = formatted;

    if (['completed', 'approved', 'active'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Completed';
    } 
    else if (['documents_collected', 'lab_analysed', 'report_prepared', 'uploaded_to_parivesh'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Active Stage';
    } 
    else if (['call not picked', 'client not responding'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Alert: Follow-up Required';
    } 
    else if (['site sampling pending', 'client review pending', 'payment pending'].includes(s)) {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Action: Pending Step';
    } 
    else if (['draft', 'archived'].includes(s)) {
      liveBadge.style.background = '#f1f5f9';
      liveBadge.style.color = '#475569';
      liveBadge.style.border = '1px solid #cbd5e1';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Standard';
    } 
    else {
      liveBadge.style.background = '#fef3c7';
      liveBadge.style.color = '#92400e';
      liveBadge.style.border = '1px solid #fcd34d';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Custom Status';
    }

    presetButtons.forEach(btn => {
      if (btn.dataset.value.toLowerCase() === s) {
        btn.classList.add('active-preset');
      } else {
        btn.classList.remove('active-preset');
      }
    });
  }

  function updateCompShowNotesCounter() {
    if (textareaNotes && notesCounter) {
      notesCounter.textContent = textareaNotes.value.length + ' / 1000';
    }
  }

  if (inputStatus) {
    renderCompShowLiveStatus(inputStatus.value);
    inputStatus.addEventListener('input', function() {
      renderCompShowLiveStatus(this.value);
    });
  }

  if (modalCompShowStatus) {
    modalCompShowStatus.addEventListener('shown.bs.modal', function() {
      if (inputStatus) inputStatus.focus();
    });
  }

  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderCompShowLiveStatus('');
    });
  }

  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      if (inputStatus) {
        inputStatus.value = this.dataset.value;
        renderCompShowLiveStatus(this.dataset.value);
        inputStatus.focus();
      }
    });
  });

  noteChips.forEach(chip => {
    chip.addEventListener('click', function() {
      const textToAppend = this.dataset.text;
      if (textareaNotes) {
        if (textareaNotes.value.trim() === '') {
          textareaNotes.value = textToAppend;
        } else {
          textareaNotes.value = textareaNotes.value.trim() + ' ' + textToAppend;
        }
        updateCompShowNotesCounter();
        textareaNotes.focus();
      }
    });
  });

  if (textareaNotes) {
    textareaNotes.addEventListener('input', updateCompShowNotesCounter);
  }

  if (formCompShowStatus) {
    formCompShowStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (btnSubmit) btnSubmit.click();
      }
    });

    formCompShowStatus.addEventListener('submit', function() {
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
      }
    });
  }
});
</script>
@endsection
