@extends('layouts.app')
@section('title', 'PPT Application Dossier — ' . $ppt->application_no)
@section('main_content')
<div class="content-body default-height">
  <div class="container-fluid">
    {{-- Top Action Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <a href="{{ route('ppt-department.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Register
          </a>
          <span class="badge bg-primary fs-6">{{ $ppt->application_no }}</span>
          @php
            $stClass = match($ppt->status) {
              'approved', 'presented' => 'bg-success',
              'agenda_scheduled'     => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
              'archived'             => 'bg-secondary',
              default                => 'bg-info-subtle text-info border border-info-subtle'
            };
          @endphp
          <span class="badge {{ $stClass }} fs-6">{{ ucwords(str_replace('_', ' ', $ppt->status)) }}</span>
        </div>
        <h3 class="fw-bold text-navy mb-0">{{ $ppt->project_name ?: 'SEAC / SEIAA Presentation Dossier' }}</h3>
      </div>
      <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary">
          <i class="bi bi-printer me-1"></i> Print Dossier
        </button>
        <a href="{{ route('ppt-department.step', ['step' => 1, 'resume' => $ppt->id]) }}" class="btn btn-navy">
          <i class="bi bi-pencil-square me-1"></i> Edit Application
        </a>
      </div>
    </div>

    {{-- 4 Metric Cards --}}
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0F1E4D !important;">
          <small class="text-muted text-uppercase fw-semibold">Client / Company</small>
          <h5 class="fw-bold text-navy mt-1 mb-1">{{ $ppt->customer?->company_name ?: ($ppt->customer?->customer_name ?: 'N/A') }}</h5>
          <small class="text-muted"><i class="bi bi-person me-1"></i>{{ $ppt->customer?->customer_name ?: '—' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #0284c7 !important;">
          <small class="text-muted text-uppercase fw-semibold">District &amp; Mineral</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">{{ $ppt->district?->name ?: 'N/A' }}</h5>
          <small class="text-muted"><i class="bi bi-gem me-1"></i>{{ $ppt->mineral?->name ?: 'Minor Mineral' }}</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #10b981 !important;">
          <small class="text-muted text-uppercase fw-semibold">Statutory Folders</small>
          <h5 class="fw-bold text-success mt-1 mb-1">{{ $ppt->documents->count() }} / 11 Folders</h5>
          <small class="text-muted">Parivesh / SEAC Checklist</small>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-left: 4px solid #f59e0b !important;">
          <small class="text-muted text-uppercase fw-semibold">Financial Ledger</small>
          <h5 class="fw-bold text-dark mt-1 mb-1">₹{{ number_format($ppt->product_value, 2) }}</h5>
          <small class="{{ $ppt->pending_amount > 0 ? 'text-danger fw-semibold' : 'text-success fw-semibold' }}">
            {{ $ppt->pending_amount > 0 ? '₹' . number_format($ppt->pending_amount, 2) . ' Pending' : 'Fully Settled' }}
          </small>
        </div>
      </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">
      {{-- 11 Statutory Folders Section --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-navy">
              <i class="fa fa-folder-open text-primary me-2"></i> 11 Statutory Presentation Folders
            </h5>
            <span class="badge bg-light text-navy border">{{ $ppt->documents->count() }} Attached Files</span>
          </div>
          <div class="card-body p-0">
            @php
              $foldersList = [
                1 => ['Documents', 'PARIVESH online uploading documents', 'fa-file-alt'],
                2 => ['EDS & EDS Reply', 'EDS and EDS reply communications', 'fa-comments'],
                3 => ['Demand Note', 'SPCB payment receipt and comments', 'fa-file-invoice-dollar'],
                4 => ['File No', 'Schedule, PPT, Radius KML, NOC and CER', 'fa-folder-open'],
                5 => ['SEAC Agenda', 'SEAC committee agenda documents', 'fa-users'],
                6 => ['SEAC Minutes', 'ADS and ADS reply if any', 'fa-clipboard-list'],
                7 => ['ADS', 'ADS reply documents and PPT presentation', 'fa-file-alt'],
                8 => ['CER Affidavit', 'Corporate Environment Responsibility affidavit', 'fa-balance-scale'],
                9 => ['SEIAA Agenda', 'State Environmental Impact Assessment Agenda', 'fa-users'],
                10 => ['SEIAA Minutes', 'Committee minutes and appraisal decision', 'fa-clipboard-check'],
                11 => ['Environmental Clearance', 'Final EC Granted Order', 'fa-leaf']
              ];
            @endphp

            <div class="list-group list-group-flush">
              @foreach($foldersList as $fId => [$fName, $fDesc, $fIcon])
                @php
                  $folderDocs = $ppt->documents->where('folder_id', $fId);
                @endphp
                <div class="list-group-item p-3">
                  <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                      <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width:32px; height:32px; background:#eff6ff;">
                        <i class="fa {{ $fIcon }} small"></i>
                      </div>
                      <div>
                        <span class="fw-bold text-navy">{{ $fId }}. {{ $fName }}</span>
                        <div class="text-muted small">{{ $fDesc }}</div>
                      </div>
                    </div>
                    <div>
                      @if($folderDocs->isNotEmpty())
                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                          <i class="bi bi-check-circle me-1"></i>{{ $folderDocs->count() }} Attached
                        </span>
                      @else
                        <span class="badge bg-light text-muted border">Pending Upload</span>
                      @endif
                    </div>
                  </div>

                  @if($folderDocs->isNotEmpty())
                    <div class="bg-light p-2 rounded-2 mt-2">
                      @foreach($folderDocs as $doc)
                        <div class="d-flex align-items-center justify-content-between py-1 border-bottom border-light">
                          <div class="d-flex align-items-center gap-2 text-truncate">
                            <i class="bi bi-paperclip text-muted"></i>
                            <span class="small fw-semibold text-truncate">{{ $doc->document_name }}</span>
                            @if($doc->file_size)
                              <span class="badge bg-white text-muted border py-0" style="font-size:10px;">{{ round($doc->file_size / 1024) }} KB</span>
                            @endif
                          </div>
                          <div>
                            @if($doc->file_path)
                              <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size:11px;">
                                <i class="bi bi-eye"></i> View
                              </a>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      {{-- Sidebar Cards: Handlers & Payments --}}
      <div class="col-lg-4">
        {{-- Technical Handling Team --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-navy">
              <i class="bi bi-people-fill text-primary me-2"></i> Project Handling Team
            </h6>
            <span class="badge bg-light text-dark border">{{ $ppt->handlers->count() }} Persons</span>
          </div>
          <div class="card-body p-3">
            @forelse($ppt->handlers as $handler)
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
              <p class="text-muted small mb-0">No technical handling personnel assigned.</p>
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
              $pBadge = match($ppt->payment_status) {
                'paid'    => 'bg-success',
                'partial' => 'bg-warning text-dark',
                default   => 'bg-danger-subtle text-danger border border-danger-subtle'
              };
            @endphp
            <span class="badge {{ $pBadge }}">{{ ucfirst($ppt->payment_status ?: 'pending') }}</span>
          </div>
          <div class="card-body p-3">
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Quoted Service Fee:</span>
              <strong class="text-navy">₹{{ number_format($ppt->product_value, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Paid Amount:</span>
              <strong class="text-success">₹{{ number_format($ppt->paid_amount, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
              <span class="text-muted">Pending Balance:</span>
              <strong class="text-danger">₹{{ number_format($ppt->pending_amount, 2) }}</strong>
            </div>
            @if($ppt->payments->isNotEmpty() && $ppt->payments->first()->notes)
              <div class="mt-3 p-2 bg-light rounded small">
                <span class="text-muted fw-semibold">Payment Notes / Ref:</span>
                <div class="text-dark">{{ $ppt->payments->first()->notes }}</div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
