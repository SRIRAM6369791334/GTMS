@extends('layouts.app')
@section('title', 'Drone Volumetric Survey Dossier — ' . $survey->survey_no)
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <a href="{{ route('drone-survey.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Register
          </a>
          <span class="badge bg-primary fs-6" style="background-color: #f97316 !important;">{{ $survey->survey_no }}</span>
          @php
            $sBadge = match($survey->survey_status) {
              'completed'   => 'bg-success',
              'processing'  => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
              default       => 'bg-info-subtle text-info border border-info-subtle'
            };
          @endphp
          <span class="badge {{ $sBadge }} fs-6">{{ ucwords(str_replace('_', ' ', $survey->survey_status ?: 'scheduled')) }}</span>
        </div>
        <h3 class="fw-bold text-navy mb-0">Drone Volumetric Survey & Photogrammetry Dossier</h3>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary">
          <i class="bi bi-printer me-1"></i> Print Dossier
        </button>
        <a href="{{ route('drone-survey.step', ['step' => 1, 'resume' => $survey->id]) }}" class="btn btn-navy" style="background-color: #0F1E4D; color: white;">
          <i class="bi bi-pencil-square me-1"></i> Edit Survey
        </a>
      </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #f97316 !important;">
          <small class="text-muted text-uppercase fw-semibold">Client / Company</small>
          <h5 class="fw-bold text-navy mt-1 mb-1" style="color: #7c2d12;">{{ $survey->customer?->company_name ?: ($survey->customer?->customer_name ?: 'N/A') }}</h5>
          <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $survey->location ?: 'Quarry Site' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #ea580c !important;">
          <small class="text-muted text-uppercase fw-semibold">Survey Extent & Date</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">{{ $survey->lease_area ? number_format($survey->lease_area, 2) . ' Ha' : '3.85 Ha' }}</h5>
          <small class="text-muted"><i class="bi bi-calendar me-1"></i>{{ $survey->flight_date ? \Carbon\Carbon::parse($survey->flight_date)->format('d M Y') : 'N/A' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #c2410c !important;">
          <small class="text-muted text-uppercase fw-semibold">Volumetric Data</small>
          <h5 class="fw-bold mt-1 mb-1" style="color: #c2410c;">{{ $survey->extracted_volume_cbm ? number_format($survey->extracted_volume_cbm, 2) : '0.00' }} CBM</h5>
          <small class="text-muted">Extracted Volume</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #9a3412 !important;">
          <small class="text-muted text-uppercase fw-semibold">Survey Fee Ledger</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">₹{{ number_format($survey->product_value, 2) }}</h5>
          <small class="{{ $survey->pending_amount > 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
            {{ $survey->pending_amount > 0 ? '₹' . number_format($survey->pending_amount, 2) . ' Pending' : 'Fully Settled' }}
          </small>
        </div>
      </div>
    </div>

    {{-- Content Grid --}}
    <div class="row g-4">
      <div class="col-lg-8">
        {{-- Flight & Equipment Info --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold" style="color: #7c2d12;">
              <i class="bi bi-airplane text-warning me-2"></i> Flight & Equipment Specifications
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              <div class="col-md-4">
                <small class="text-muted text-uppercase">Drone Model</small>
                <div class="fw-bold text-dark">{{ $survey->drone_model ?: 'DJI Phantom 4 RTK' }}</div>
              </div>
              <div class="col-md-4">
                <small class="text-muted text-uppercase">Drone UIN</small>
                <div class="fw-bold text-dark">{{ $survey->drone_uin_no ?: 'N/A' }}</div>
              </div>
              <div class="col-md-4">
                <small class="text-muted text-uppercase">Flight Altitude</small>
                <div class="fw-bold text-dark">{{ $survey->altitude_meters ?: '120' }} Meters AGL</div>
              </div>
              <div class="col-md-4">
                <small class="text-muted text-uppercase">Pilot Name</small>
                <div class="fw-bold text-dark">{{ $survey->drone_pilot_name ?: 'DGCA Pilot' }}</div>
              </div>
              <div class="col-md-4">
                <small class="text-muted text-uppercase">Pilot RPC No.</small>
                <div class="fw-bold text-dark">{{ $survey->pilot_rpc_no ?: 'N/A' }}</div>
              </div>
              <div class="col-md-4">
                <small class="text-muted text-uppercase">GSD (Resolution)</small>
                <div class="fw-bold text-dark">{{ $survey->gsd_cm_px ?: '2.5' }} cm/px</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Deliverables & Documents --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold" style="color: #7c2d12;">
              <i class="bi bi-folder-check text-warning me-2"></i> Photogrammetry Deliverables
            </h5>
            <span class="badge bg-light text-navy border">{{ $survey->documents->count() }} Attached</span>
          </div>
          <div class="card-body p-0">
            @php
              $expectedDocs = [
                '1. Orthomosaic Map (GeoTIFF)'       => 'High-resolution stitched aerial map',
                '2. Digital Surface Model (DSM)'     => 'Elevation model for topographic analysis',
                '3. 3D Point Cloud (LAS)'            => 'Dense point cloud for structural analysis',
                '4. Volumetric Excavation Report'    => 'PDF report with cut/fill volume calculations',
                '5. Contour Map (DXF)'               => 'AutoCAD compatible 1m interval contours',
                '6. Flight Log & KML Boundary'       => 'Telemetry data and boundary extent'
              ];
            @endphp

            <div class="list-group list-group-flush">
              @foreach($expectedDocs as $dName => $dMeta)
                @php
                  $matchDoc = $survey->documents->firstWhere('document_name', $dName);
                @endphp
                <div class="list-group-item p-3">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center {{ $matchDoc ? 'bg-success text-white' : 'bg-light text-muted' }}" style="width:34px; height:34px;">
                        <i class="bi {{ $matchDoc ? 'bi-check-lg' : 'bi-file-earmark' }}"></i>
                      </div>
                      <div>
                        <span class="fw-bold text-dark">{{ $dName }}</span>
                        <div class="text-muted small">{{ $dMeta }}</div>
                      </div>
                    </div>
                    <div>
                      @if($matchDoc && $matchDoc->file_path)
                        <a href="{{ asset('storage/' . $matchDoc->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-1 px-3">
                          <i class="bi bi-eye me-1"></i> View Document
                        </a>
                      @else
                        <span class="badge bg-light text-muted border">Pending</span>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Sidebar Cards: Handlers & Payments --}}
      <div class="col-lg-4">
        {{-- Technical Survey Team --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold" style="color: #7c2d12;">
              <i class="bi bi-people-fill text-warning me-2"></i> Survey Team &amp; Handlers
            </h6>
            <span class="badge bg-light text-dark border">{{ $survey->handlers->count() }} Persons</span>
          </div>
          <div class="card-body p-3">
            @forelse($survey->handlers as $handler)
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
              <p class="text-muted small mb-0">No field survey personnel assigned.</p>
            @endforelse
          </div>
        </div>

        {{-- Financial & Billing Ledger --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold" style="color: #7c2d12;">
              <i class="bi bi-receipt text-success me-2"></i> Financial &amp; Billing Ledger
            </h6>
            @php
              $pBadge = match($survey->payment_status) {
                'paid'    => 'bg-success',
                'partial' => 'bg-warning text-dark',
                default   => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
            @endphp
            <span class="badge {{ $pBadge }}">{{ ucfirst($survey->payment_status ?: 'pending') }}</span>
          </div>
          <div class="card-body p-3">
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Quoted Survey Fee:</span>
              <strong class="text-dark">₹{{ number_format($survey->product_value, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Paid Amount:</span>
              <strong class="text-success">₹{{ number_format($survey->paid_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Pending Balance:</span>
              <strong class="text-danger">₹{{ number_format($survey->pending_amount, 2) }}</strong>
            </div>
            @if($survey->payments->isNotEmpty() && $survey->payments->first()->notes)
              <div class="mt-3 p-2 bg-light rounded small">
                <span class="text-muted fw-semibold">Payment Notes / Ref:</span>
                <div class="text-dark">{{ $survey->payments->first()->notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
