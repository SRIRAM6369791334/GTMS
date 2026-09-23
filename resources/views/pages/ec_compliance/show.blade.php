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
            $cClass = match($comp->status) {
              'completed'            => 'bg-success',
              'uploaded_to_parivesh' => 'bg-info text-white',
              default                => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle'
            };
          @endphp
          <span class="badge {{ $cClass }} fs-6">{{ ucwords(str_replace('_', ' ', $comp->status)) }}</span>
          @if($comp->parivesh_acknowledgement_no)
            <span class="badge bg-success font-monospace fs-6">
              <i class="bi bi-check2-circle me-1"></i>Parivesh: {{ $comp->parivesh_acknowledgement_no }}
            </span>
          @endif
        </div>
        <h3 class="fw-bold text-navy mb-0">{{ $comp->project_name ?: 'Environmental Clearance Half-Yearly Compliance Dossier' }}</h3>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary">
          <i class="bi bi-printer me-1"></i> Print Compliance Dossier
        </button>
        <a href="{{ route('ec-compliance.step', ['step' => 1, 'resume' => $comp->id]) }}" class="btn btn-navy">
          <i class="bi bi-pencil-square me-1"></i> Edit Filing
        </a>
      </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0F1E4D !important;">
          <small class="text-muted text-uppercase fw-semibold">Client / Company</small>
          <h5 class="fw-bold text-navy mt-1 mb-1">{{ $comp->customer?->company_name ?: ($comp->customer?->customer_name ?: 'N/A') }}</h5>
          <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $comp->customer?->customer_name ?: '—' }}</small>
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
  </div>
</div>
@endsection
