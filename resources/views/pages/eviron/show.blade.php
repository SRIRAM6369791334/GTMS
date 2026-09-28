@extends('layouts.app')
@section('title', $project->project_code . ' — Environment Clearance')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<style>
.admin-tab {
  cursor: pointer;
  transition: all 0.2s ease;
}
.admin-tab:hover {
  transform: translateY(-1px);
}
.table-action-group {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
}
.btn-action-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.88rem;
  transition: all 0.2s ease;
  border: 1px solid transparent;
  text-decoration: none;
  cursor: pointer;
  padding: 0;
}
.btn-action-icon:hover {
  transform: translateY(-1px);
}
</style>

<div class="content-body default-height">
  <div class="container-fluid">

    {{-- Breadcrumb & Project Switcher --}}
    <div class="row page-titles align-items-center mb-3">
      <div class="col-md-6">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance</a></li>
          <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $project->project_code }}</a></li>
        </ol>
      </div>
      <div class="col-md-6 text-end">
        @if($allProjects->isNotEmpty())
        <div class="d-inline-flex align-items-center gap-2">
          <label class="small text-muted mb-0">Switch Project:</label>
          <select class="form-select form-select-sm w-auto" onchange="if(this.value) window.location.href='{{ route('eviron.index') }}/' + this.value;">
            @foreach($allProjects as $p)
              <option value="{{ $p->id }}" {{ $project->id === $p->id ? 'selected' : '' }}>
                {{ $p->project_code }} — {{ Str::limit($p->project_name, 25) }} ({{ $p->category_badge }})
              </option>
            @endforeach
          </select>
        </div>
        @endif
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('info'))
      <div class="alert alert-info alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-info-circle me-2"></i>{{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert">
        <i class="fa fa-circle-exclamation me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- ================= PROJECT SUMMARY CARD ================= --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px;">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1">
              <h4 class="mb-0 fw-bold" style="color:#0F1E4D; font-family:'Sora',sans-serif;">
                {{ $project->project_code }}
              </h4>
              <span class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:.82rem;">
                Category: {{ $project->category_badge }}
              </span>
              @php
                $st = strtolower(trim($project->status));
                $stStyle = match(true) {
                  in_array($st, ['approved', 'completed']) => 'background:#ecfdf5; color:#15803d; border:1px solid #86efac;',
                  in_array($st, ['validation', 'reported']) => 'background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;',
                  in_array($st, ['call not picked', 'client not responding', 'rejected']) => 'background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;',
                  in_array($st, ['draft']) => 'background:#f8fafc; color:#64748b; border:1px solid #cbd5e1;',
                  in_array($st, ['archived']) => 'background:#f5f3ff; color:#7c3aed; border:1px solid #ddd6fe;',
                  default => 'background:#fefce8; color:#a16207; border:1px solid #fef08a;'
                };
              @endphp
              <span class="badge" style="{{ $stStyle }} font-size:.82rem;">
                {{ ucwords(str_replace('_', ' ', $project->status)) }}
              </span>
            </div>
            <div class="text-muted small">
              <strong>Applicant:</strong> {{ $project->customer?->company_name ?: ($project->customer?->customer_name ?: 'Applicant') }}
              &bull; <strong>Project:</strong> {{ $project->project_name }}
              &bull; <strong>District:</strong> {{ $project->district?->name ?? '—' }}
              @if($project->location) &bull; <strong>Location:</strong> {{ $project->location }} @endif
            </div>
            @if($project->sub_category_label)
            <div class="mt-2 text-primary small fw-semibold">
              <i class="fa fa-layer-group me-1"></i> {{ $project->sub_category_label }}
            </div>
            @endif
            @if($project->status_notes)
            <div class="mt-2 p-2 bg-light rounded text-muted small border">
              <i class="fa fa-comment-dots text-warning me-1"></i> <strong>Status Note:</strong> {{ $project->status_notes }}
            </div>
            @endif
          </div>

          {{-- Stage Action Buttons --}}
          <div class="d-flex gap-2 align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpdateStatus">
              <i class="fa fa-pen me-1"></i> Update Status
            </button>
            @if($project->category === 'B1')
              {{-- B1 Sequential Statutory Stage Buttons --}}
              @if($project->b1_stage === 'sc1_prep')
                <form method="POST" action="{{ route('eviron.submitSc1ToPpt', $project->id) }}" onsubmit="return confirm('Submit TOR (ToR & Mining Documents) to PPT Department for Stage 1 ToR Presentation?');">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-paper-plane me-1"></i> Submit TOR to PPT Department (Stage 1 Gate)
                  </button>
                </form>
              @elseif($project->b1_stage === 'sc1_ppt_review')
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2">
                  <i class="fa fa-spinner fa-spin me-1"></i> Awaiting PPT ToR Approval
                </span>
                @if($project->ppt_stage_1_id)
                  <a href="{{ route('ppt-department.show', $project->ppt_stage_1_id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-tv me-1"></i> View PPT Dossier
                  </a>
                @endif
              @elseif($project->b1_stage === 'sc2_prep')
                <form method="POST" action="{{ route('eviron.submitSc2ToPpt', $project->id) }}" onsubmit="return confirm('Submit ETA (EIA Study & TNPCB Submission) to PPT Department for Stage 2 Final EC Presentation?');">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-paper-plane me-1"></i> Submit ETA to PPT Department (Stage 2 Gate)
                  </button>
                </form>
              @elseif($project->b1_stage === 'sc2_ppt_review')
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle p-2">
                  <i class="fa fa-spinner fa-spin me-1"></i> Awaiting Final EC Approval
                </span>
                @if($project->ppt_stage_2_id)
                  <a href="{{ route('ppt-department.show', $project->ppt_stage_2_id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-tv me-1"></i> View Final EC Dossier
                  </a>
                @endif
              @elseif($project->b1_stage === 'completed' || $project->status === 'approved')
                @if($project->ecCertificates?->isNotEmpty())
                  <a href="{{ route('ec-certificate.show', $project->ecCertificates->first()->id) }}" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">
                    <i class="fa fa-certificate me-1"></i> View Issued EC Certificate ({{ $project->ecCertificates->first()->ec_ref_no }})
                  </a>
                @else
                  <a href="{{ route('ec-certificate.step', 1) }}?project_id={{ $project->id }}" class="btn btn-success btn-sm">
                    <i class="fa fa-certificate me-1"></i> Issue EC Certificate
                  </a>
                @endif
              @endif
            @else
              {{-- Category B2 & Legacy Action Buttons --}}
              @if($project->ecCertificates?->isNotEmpty())
                <a href="{{ route('ec-certificate.show', $project->ecCertificates->first()->id) }}" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">
                  <i class="fa fa-certificate me-1"></i> View Issued EC Certificate ({{ $project->ecCertificates->first()->ec_ref_no }})
                </a>
              @elseif($project->status === 'draft')
                <form method="POST" action="{{ route('eviron.status', $project->id) }}">
                  @csrf
                  <input type="hidden" name="status" value="validation">
                  <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fa fa-arrow-right me-1"></i> Send to Validation (6.2)
                  </button>
                </form>
              @elseif($project->status === 'validation')
                <form method="POST" action="{{ route('eviron.status', $project->id) }}">
                  @csrf
                  <input type="hidden" name="status" value="approved">
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-check-circle me-1"></i> Approve Application (6.3)
                  </button>
                </form>
              @elseif($project->status === 'approved')
                <a href="{{ route('ec-certificate.step', 1) }}?project_id={{ $project->id }}" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">
                  <i class="fa fa-certificate me-1"></i> Issue EC Certificate
                </a>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ================= CATEGORY B1 STATUTORY LIFECYCLE STEPPER ================= --}}
    @if($project->category === 'B1')
    <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px; border-left: 5px solid #2563eb !important;">
      <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <div>
          <span class="badge bg-primary me-2">Category B1 Statutory Lifecycle</span>
          <span class="small fw-bold text-navy">2-Stage Sequential Process Flow with PPT Department Approval Gates</span>
        </div>
        <span class="badge bg-light text-navy border">
          Current State: {{ strtoupper(str_replace('_', ' ', $project->b1_stage ?? 'sc1_prep')) }}
        </span>
      </div>
      <div class="card-body py-3">
        @php
          $b1Stage = $project->b1_stage ?? 'sc1_prep';
          $isStep1Done = in_array($b1Stage, ['sc1_ppt_review', 'sc2_prep', 'sc2_ppt_review', 'completed']);
          $isStep1Active = ($b1Stage === 'sc1_prep');

          $isStep2Done = in_array($b1Stage, ['sc2_prep', 'sc2_ppt_review', 'completed']);
          $isStep2Active = ($b1Stage === 'sc1_ppt_review');

          $isStep3Done = in_array($b1Stage, ['sc2_ppt_review', 'completed']);
          $isStep3Active = ($b1Stage === 'sc2_prep');

          $isStep4Done = ($b1Stage === 'completed');
          $isStep4Active = ($b1Stage === 'sc2_ppt_review');
        @endphp
        <div class="row g-2 align-items-center">
          {{-- Step 1 --}}
          <div class="col-md-3">
            <div class="p-3 rounded-3 border {{ $isStep1Active ? 'bg-primary text-white border-primary shadow-sm' : ($isStep1Done ? 'bg-light text-success border-success' : 'bg-light text-muted') }}">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-circle {{ $isStep1Active ? 'bg-white text-primary' : ($isStep1Done ? 'bg-success text-white' : 'bg-secondary text-white') }} px-2 py-1">
                  @if($isStep1Done)<i class="fa fa-check"></i>@else 1 @endif
                </span>
                <span class="fw-bold small {{ $isStep1Active ? 'text-white' : ($isStep1Done ? 'text-success' : 'text-dark') }}">Stage 1: TOR Intake</span>
              </div>
              <div class="small {{ $isStep1Active ? 'text-white-50' : 'text-muted' }}" style="font-size:0.78rem;">
                ToR &amp; Mining Documents (5 Folders)
              </div>
            </div>
          </div>

          {{-- Step 2 --}}
          <div class="col-md-3">
            <div class="p-3 rounded-3 border {{ $isStep2Active ? 'bg-warning text-dark border-warning shadow-sm' : ($isStep2Done ? 'bg-light text-success border-success' : 'bg-light text-muted') }}">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-circle {{ $isStep2Active ? 'bg-dark text-white' : ($isStep2Done ? 'bg-success text-white' : 'bg-secondary text-white') }} px-2 py-1">
                  @if($isStep2Done)<i class="fa fa-check"></i>@else 2 @endif
                </span>
                <span class="fw-bold small {{ $isStep2Active ? 'text-dark' : ($isStep2Done ? 'text-success' : 'text-dark') }}">PPT Stage 1 Gate</span>
              </div>
              <div class="small {{ $isStep2Active ? 'text-dark' : 'text-muted' }}" style="font-size:0.78rem;">
                ToR Presentation &amp; SEAC Review
              </div>
            </div>
          </div>

          {{-- Step 3 --}}
          <div class="col-md-3">
            <div class="p-3 rounded-3 border {{ $isStep3Active ? 'bg-primary text-white border-primary shadow-sm' : ($isStep3Done ? 'bg-light text-success border-success' : 'bg-light text-muted') }}">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-circle {{ $isStep3Active ? 'bg-white text-primary' : ($isStep3Done ? 'bg-success text-white' : 'bg-secondary text-white') }} px-2 py-1">
                  @if($isStep3Done)<i class="fa fa-check"></i>@else 3 @endif
                </span>
                <span class="fw-bold small {{ $isStep3Active ? 'text-white' : ($isStep3Done ? 'text-success' : 'text-dark') }}">Stage 2: ETA Unlocked</span>
              </div>
              <div class="small {{ $isStep3Active ? 'text-white-50' : 'text-muted' }}" style="font-size:0.78rem;">
                EIA Study &amp; TNPCB Submission (6 Folders)
              </div>
            </div>
          </div>

          {{-- Step 4 --}}
          <div class="col-md-3">
            <div class="p-3 rounded-3 border {{ $isStep4Active ? 'bg-warning text-dark border-warning shadow-sm' : ($isStep4Done ? 'bg-light text-success border-success' : 'bg-light text-muted') }}">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-circle {{ $isStep4Active ? 'bg-dark text-white' : ($isStep4Done ? 'bg-success text-white' : 'bg-secondary text-white') }} px-2 py-1">
                  @if($isStep4Done)<i class="fa fa-check"></i>@else 4 @endif
                </span>
                <span class="fw-bold small {{ $isStep4Active ? 'text-dark' : ($isStep4Done ? 'text-success' : 'text-dark') }}">PPT Stage 2 Gate &amp; EC</span>
              </div>
              <div class="small {{ $isStep4Active ? 'text-dark' : 'text-muted' }}" style="font-size:0.78rem;">
                Final EC Presentation &amp; Clearance
              </div>
            </div>
          </div>
        </div>

        @if($b1Stage === 'sc1_ppt_review' && $project->pptStage1)
          <div class="alert alert-warning py-2 px-3 mt-3 mb-0 d-flex align-items-center justify-content-between" role="alert">
            <div class="small">
              <i class="fa fa-info-circle me-1"></i>
              <strong>Stage 1 In Progress:</strong> This application is currently under review in the PPT Department (Dossier: <strong>{{ $project->pptStage1->application_no }}</strong>).
              Once PPT Department approves the ToR Presentation, ETA will automatically unlock here with 6 new document folders.
            </div>
            <a href="{{ route('ppt-department.show', $project->pptStage1->id) }}" class="btn btn-sm btn-navy text-nowrap ms-2" style="background:#0F1E4D; color:#fff;">
              <i class="fa fa-tv me-1"></i> Go to PPT Review
            </a>
          </div>
        @elseif($b1Stage === 'sc2_ppt_review' && $project->pptStage2)
          <div class="alert alert-warning py-2 px-3 mt-3 mb-0 d-flex align-items-center justify-content-between" role="alert">
            <div class="small">
              <i class="fa fa-info-circle me-1"></i>
              <strong>Stage 2 In Progress:</strong> Final EIA documentation has been submitted to the PPT Department (Dossier: <strong>{{ $project->pptStage2->application_no }}</strong>).
              Once PPT Department approves the Final EC Presentation, this Environment Clearance project will be fully marked as Approved!
            </div>
            <a href="{{ route('ppt-department.show', $project->pptStage2->id) }}" class="btn btn-sm btn-navy text-nowrap ms-2" style="background:#0F1E4D; color:#fff;">
              <i class="fa fa-tv me-1"></i> Go to PPT Review
            </a>
          </div>
        @elseif($b1Stage === 'completed')
          <div class="alert alert-success py-2 px-3 mt-3 mb-0 d-flex align-items-center justify-content-between" role="alert">
            <div class="small">
              <i class="fa fa-check-circle me-1"></i>
              <strong>Category B1 Lifecycle Complete!</strong> Both Stage 1 (ToR) and Stage 2 (Final EC) presentations have been approved by the PPT Department. The project is fully cleared for EC Certificate issuance.
            </div>
            @if(!$project->ecCertificates?->isNotEmpty())
              <a href="{{ route('ec-certificate.step', 1) }}?project_id={{ $project->id }}" class="btn btn-sm btn-success text-nowrap ms-2">
                <i class="fa fa-certificate me-1"></i> Issue EC Certificate
              </a>
            @endif
          </div>
        @endif
      </div>
    </div>
    @endif

    {{-- ================= 6-STAGE PROCESS FLOW BAR ================= --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius:14px;">
      <div class="card-header bg-white py-2 border-bottom">
        <span class="small fw-bold text-muted text-uppercase">Process Flow (Lifecycle Stages)</span>
      </div>
      <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
          @php
            $stages = [
              1 => ['code' => '6.1', 'name' => 'Upload & Store Data', 'icon' => 'fa-upload'],
              2 => ['code' => '6.2', 'name' => 'Validate Data', 'icon' => 'fa-check-double'],
              3 => ['code' => '6.3', 'name' => 'Approve Data', 'icon' => 'fa-circle-check'],
              4 => ['code' => '6.4', 'name' => 'Generate Reports', 'icon' => 'fa-chart-pie'],
              5 => ['code' => '6.5', 'name' => 'Archive & Backup', 'icon' => 'fa-box-archive'],
              6 => ['code' => '6.6', 'name' => 'Complete / Exit', 'icon' => 'fa-door-open'],
            ];
          @endphp
          @foreach($stages as $stNum => $st)
            @php
              $isDone = $processStage > $stNum;
              $isCurrent = $processStage === $stNum;
            @endphp
            <div class="d-flex align-items-center gap-2 p-2 rounded {{ $isCurrent ? 'bg-navy text-white' : ($isDone ? 'bg-light text-success' : 'text-muted') }}" style="{{ $isCurrent ? 'background:#0F1E4D !important;' : '' }}">
              <span class="badge rounded-circle {{ $isCurrent ? 'bg-warning text-dark' : ($isDone ? 'bg-success text-white' : 'bg-secondary text-white') }} px-2 py-1">
                @if($isDone)<i class="fa fa-check"></i>@else{{ $st['code'] }}@endif
              </span>
              <span class="small fw-semibold">{{ $st['name'] }}</span>
            </div>
            @if($stNum < 6)
              <i class="fa fa-chevron-right text-muted small d-none d-lg-inline"></i>
            @endif
          @endforeach
        </div>
      </div>
    </div>

    {{-- ================= HANDLING TEAM & FINANCIAL LEDGER CARDS ================= --}}
    <div class="row g-3 mb-4">
      {{-- Project Handling Team Card --}}
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-navy" style="color:#0F1E4D;">
              <i class="fa fa-users me-2 text-primary"></i> Project Handling Team
            </h6>
            <span class="badge bg-secondary-subtle text-secondary rounded-pill">
              {{ $project->handlers->count() }} members
            </span>
          </div>
          <div class="card-body p-3">
            @if($project->handlers->isNotEmpty())
              <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-0" style="font-size:0.85rem;">
                  <thead class="bg-light">
                    <tr>
                      <th style="width:35px;" class="text-center">#</th>
                      <th>Person Name</th>
                      <th>Role / Designation</th>
                      <th>Notes</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($project->handlers as $idx => $handler)
                      <tr>
                        <td class="text-center fw-bold">{{ $idx + 1 }}</td>
                        <td class="fw-semibold text-navy">{{ $handler->name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $handler->role }}</span></td>
                        <td class="text-muted small">{{ $handler->notes ?: '—' }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-center py-3 text-muted small">
                <i class="fa fa-user-clock fa-2x mb-2 text-secondary opacity-50"></i>
                <div>No handling team members assigned yet.</div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Financial & Billing Ledger Card --}}
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
          <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-bold text-navy" style="color:#0F1E4D;">
              <i class="fa fa-receipt me-2 text-success"></i> Financial &amp; Billing Ledger
            </h6>
            @php
              $pStatus = $project->payment_status ?: 'pending';
              $badgeClass = match($pStatus) {
                'paid' => 'bg-success text-white',
                'partial' => 'bg-warning text-dark',
                default => 'bg-danger text-white',
              };
            @endphp
            <span class="badge {{ $badgeClass }} px-2 py-1 text-uppercase" style="font-size:0.75rem;">
              {{ ucfirst($pStatus) }}
            </span>
          </div>
          <div class="card-body p-3">
            <div class="row g-2 mb-3">
              <div class="col-4">
                <div class="p-2 rounded bg-light border text-center">
                  <div class="text-muted small" style="font-size:11px;">Quotation</div>
                  <div class="fw-bold text-navy">₹ {{ number_format((float)$project->product_value, 2) }}</div>
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 rounded border text-center" style="background:#f0fdf4;">
                  <div class="text-success small" style="font-size:11px;">Paid</div>
                  <div class="fw-bold text-success">₹ {{ number_format((float)$project->paid_amount, 2) }}</div>
                </div>
              </div>
              <div class="col-4">
                <div class="p-2 rounded border text-center" style="background:#fff7ed;">
                  <div class="text-danger small" style="font-size:11px;">Pending</div>
                  <div class="fw-bold text-danger">₹ {{ number_format((float)$project->pending_amount, 2) }}</div>
                </div>
              </div>
            </div>
            @if($project->payments->isNotEmpty())
              <small class="text-muted d-block mb-1">Payment Transactions ({{ $project->payments->count() }}):</small>
              <ul class="list-group list-group-flush small" style="font-size:0.8rem;">
                @foreach($project->payments as $pmt)
                  <li class="list-group-item px-0 py-1 d-flex justify-content-between align-items-center">
                    <span>{{ $pmt->created_at->format('d M Y') }} &bull; {{ $pmt->notes ?: 'Payment recorded' }}</span>
                    <span class="fw-bold text-success">₹ {{ number_format((float)$pmt->paid_amount, 2) }}</span>
                  </li>
                @endforeach
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ================= DYNAMIC FOLDER TABS & CHECKLIST ================= --}}
    <main class="admin-content">

      {{-- Tab Buttons --}}
      <div class="admin-tabs mb-4">
        @php
          $folderPalettes = [
            'Documents'                  => ['#1d4ed8', '#eff6ff'],
            'Documents (ToR Letter)'     => ['#1d4ed8', '#eff6ff'],
            'Site Photographs'           => ['#059669', '#ecfdf5'],
            'Baseline Study'             => ['#059669', '#ecfdf5'],
            'Report'                     => ['#d97706', '#fef3c7'],
            'Draft (12 Chapters)'        => ['#7c3aed', '#f5f3ff'],
            'GIS & Maps'                 => ['#4f46e5', '#eef2ff'],
            'Signed Reports'             => ['#0284c7', '#e0f2fe'],
            'TNPCB Draft Submission'     => ['#b45309', '#fef3c7'],
            'Final EIA Report'           => ['#be185d', '#fce7f3'],
            'Uploading File'             => ['#0d9488', '#ccfbf1'],
            'PARIVESH Acknowledgements'  => ['#be185d', '#fce7f3'],
          ];
        @endphp
        @foreach($folders as $idx => $folder)
          @php
            $fPalette = $folderPalettes[$folder->name] ?? ['#0F1E4D', '#f1f5f9'];
            $docs = $documentsByFolder[$folder->id] ?? collect();
            $approvedCount = $docs->where('status', 'approved')->count();
          @endphp
          <div class="admin-tab {{ $idx === 0 ? 'active' : '' }}" data-target="folder-tab-{{ $folder->id }}" style="--tab-color:{{ $fPalette[0] }}; --tab-tint:{{ $fPalette[1] }};">
            <span class="badge-num" style="background:{{ $fPalette[0] }}; color:#fff;">{{ $idx + 1 }}</span>
            {{ $folder->name }}
            <span class="cnt">{{ $docs->count() }}</span>
          </div>
        @endforeach
      </div>

      {{-- Tab Panels --}}
      @foreach($folders as $idx => $folder)
        @php
          $fPalette = $folderPalettes[$folder->name] ?? ['#0F1E4D', '#f1f5f9'];
          $docs = $documentsByFolder[$folder->id] ?? collect();
          $approvedCount = $docs->where('status', 'approved')->count();
        @endphp
        <div class="admin-panel tab-panel mb-4" id="folder-tab-{{ $folder->id }}" style="{{ $idx > 0 ? 'display:none;' : '' }}">
          <div class="panel-head d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-0">{{ $idx + 1 }} &middot; {{ $folder->name }}</h5>
              <p class="sub mb-0">Checklist items for {{ $folder->name }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
              @can('environment.b2.upload')
              <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 btn-open-add-enviro-doc"
                      data-folder-id="{{ $folder->id }}"
                      data-folder-name="{{ $folder->name }}"
                      data-bs-toggle="modal" data-bs-target="#modalAddEnviroDoc">
                <i class="bi bi-plus-circle me-1"></i>Add Document
              </button>
              @endcan
              <span class="panel-progress-chip fw-bold" style="font-size:.82rem; color:{{ $fPalette[0] }};">
                {{ $approvedCount }} / {{ $docs->count() }} Approved
              </span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-admin align-middle mb-0">
              <thead>
                <tr>
                  <th style="width:36px;">#</th>
                  <th>Document Name</th>
                  <th style="width:115px;">Requirement</th>
                  <th>Status</th>
                  <th>Uploaded File</th>
                  <th>Uploaded At</th>
                  <th class="text-end" style="min-width:110px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($docs as $dIdx => $doc)
                @php
                  $isMandatory = $doc->documentField ? (bool)$doc->documentField->required : false;
                  $statusBadgeClass = match($doc->status) {
                    'approved'          => 'bg-success text-white',
                    'validated'         => 'bg-info text-white',
                    'uploaded'          => 'bg-primary text-white',
                    'revision_required' => 'bg-danger text-white',
                    default             => 'bg-secondary text-white',
                  };
                @endphp
                <tr class="checklist-row" data-mandatory="{{ $isMandatory ? '1' : '0' }}">
                  <td class="text-muted small">{{ $dIdx + 1 }}</td>
                  <td>
                    <span class="row-mod-dot" style="background:{{ $fPalette[0] }};"></span>
                    <span class="doc-name font-w600">{{ $doc->document_name }}</span>
                    @if($doc->review_note)
                      <div class="doc-hint text-danger mt-1 small">
                        <i class="fa fa-info-circle me-1"></i> {{ $doc->review_note }}
                      </div>
                    @endif
                  </td>
                  <td>
                    <select class="form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center {{ $isMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }}"
                            style="font-size:0.75rem; width:105px; border-radius:6px;"
                            data-doc-id="{{ $doc->id }}">
                      <option value="mandatory" {{ $isMandatory ? 'selected' : '' }}>Mandatory</option>
                      <option value="optional" {{ !$isMandatory ? 'selected' : '' }}>Optional</option>
                    </select>
                  </td>
                  <td>
                    <span class="badge {{ $statusBadgeClass }}" style="font-size:0.75rem;">
                      {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                    </span>
                  </td>
                  <td>
                    @if($doc->file_path)
                      <a href="{{ route('eviron.documents.download', $doc->id) }}" class="text-primary font-w600">
                        <i class="fa fa-download me-1"></i> {{ Str::limit($doc->file_name ?: 'Download', 28) }}
                      </a>
                    @else
                      <span class="text-muted small">— Not uploaded —</span>
                    @endif
                  </td>
                  <td class="small text-muted">
                    {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : '—' }}
                  </td>
                  <td class="text-end">
                    <div class="table-action-group">
                      {{-- View Button (Opens in separate page) --}}
                      @if($doc->file_path)
                      <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" rel="noopener noreferrer"
                         class="btn-action-icon" style="background:#eff6ff; color:#0284c7; border-color:#bae6fd;"
                         title="View Document in separate page">
                        <i class="fa fa-eye"></i>
                      </a>
                      @endif

                      {{-- Upload Button (triggers shared modal) --}}
                      @can('environment.b2.upload')
                      <button type="button" class="btn-action-icon" style="background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe;"
                              data-bs-toggle="modal" data-bs-target="#modalUploadDoc"
                              data-doc-name="{{ $doc->document_name }}"
                              data-action="{{ route('eviron.documents.upload', [$project->id, $doc->id]) }}"
                              title="Upload File">
                        <i class="fa fa-upload"></i>
                      </button>
                      @endcan

                      {{-- Review Button (triggers shared modal) --}}
                      @can('environment.b2.review')
                      @if($doc->file_path)
                      <button type="button" class="btn-action-icon" style="background:#ecfdf5; color:#15803d; border-color:#86efac;"
                              data-bs-toggle="modal" data-bs-target="#modalReviewDoc"
                              data-doc-name="{{ $doc->document_name }}"
                              data-review-note="{{ $doc->review_note }}"
                              data-action="{{ route('eviron.documents.review', [$project->id, $doc->id]) }}"
                              title="Review Document">
                        <i class="fa fa-check"></i>
                      </button>
                      @endif
                      @endcan
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center py-4 text-muted">
                    No document checklist items found in this folder.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      @endforeach

    </main>

  </div>
</div>

{{-- ================= SHARED MODALS (OUTSIDE TABLE TO PREVENT BACKDROP FREEZE) ================= --}}

{{-- Shared Upload Modal --}}
<div class="modal fade" id="modalUploadDoc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formUploadDoc" method="POST" enctype="multipart/form-data" action="">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="upload_modal_title">Upload Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Select File (PDF, DOCX, JPG, PNG, max 25MB) *</label>
          <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.kml,.zip">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">Upload File</button>
      </div>
    </form>
  </div>
</div>

{{-- Shared Review Modal --}}
<div class="modal fade" id="modalReviewDoc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formReviewDoc" method="POST" action="">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="review_modal_title">Review Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Review Decision *</label>
          <select class="form-select" name="status" required>
            <option value="approved">Approve Document</option>
            <option value="revision_required">Request Revision / Reject</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Review Remarks (Optional)</label>
          <textarea class="form-control" name="review_note" id="review_modal_note" rows="3" placeholder="Add remarks or reasons if requesting revision"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">Save Review</button>
      </div>
    </form>
  </div>
</div>

{{-- Shared Add Custom Document Modal --}}
<div class="modal fade" id="modalAddEnviroDoc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="formAddEnviroDoc" method="POST" enctype="multipart/form-data" action="{{ route('eviron.documents.add', $project->id) }}">
      @csrf
      <input type="hidden" name="folder_id" id="add_doc_folder_id" value="">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="add_doc_modal_title"><i class="bi bi-plus-circle me-2 text-primary"></i>Add Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Target Folder</label>
          <input type="text" id="add_doc_folder_name_display" class="form-control" readonly style="background:#f8fafc;">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Document Title / Name *</label>
          <input type="text" name="document_name" class="form-control" placeholder="e.g. Additional Site Photograph, Revised Map" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Select File (PDF, DOCX, JPG, PNG, KML, max 25MB) *</label>
          <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.kml,.zip">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-navy btn-sm" style="background:#0F1E4D; color:#fff;">Attach &amp; Add Document</button>
      </div>
    </form>
  </div>
</div>

{{-- Dynamic Update Project Status Modal (UI/UX Pro Max Enhanced) --}}
<style>
  #modalUpdateStatus .modal-content {
    border-radius: 16px;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(15, 30, 77, 0.25);
    overflow: hidden;
  }
  #modalUpdateStatus .modal-header {
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 1.25rem 1.5rem 1rem;
    position: relative;
  }
  #modalUpdateStatus .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #0F1E4D 0%, #3B82F6 50%, #10B981 100%);
  }
  #modalUpdateStatus .status-icon-wrapper {
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
  #modalUpdateStatus .preset-status-btn {
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
  #modalUpdateStatus .preset-status-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
  }
  #modalUpdateStatus .preset-status-btn.active-preset {
    background: #0F1E4D !important;
    color: #ffffff !important;
    border-color: #0F1E4D !important;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
  }
  #modalUpdateStatus .preset-status-btn.active-preset i {
    color: #ffffff !important;
  }
  #modalUpdateStatus .quick-note-chip {
    font-size: 0.72rem;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px dashed #cbd5e1;
    background: #f8fafc;
    color: #475569;
    cursor: pointer;
    transition: all 0.12s ease;
  }
  #modalUpdateStatus .quick-note-chip:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #0F1E4D;
  }
  #modalUpdateStatus .form-control:focus {
    border-color: #0F1E4D;
    box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12);
  }
  #modalUpdateStatus .btn-save-status {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    border: none;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 1.4rem;
    border-radius: 8px;
    transition: all 0.2s ease;
  }
  #modalUpdateStatus .btn-save-status:hover {
    background: linear-gradient(135deg, #162a6b 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
  }
</style>

<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateStatusLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" id="formShowUpdateStatus" action="{{ route('eviron.status', $project->id) }}">
        @csrf
        
        {{-- Modal Header --}}
        <div class="modal-header align-items-start">
          <div class="d-flex align-items-center gap-3">
            <div class="status-icon-wrapper">
              <i class="fa fa-pen-to-square"></i>
            </div>
            <div>
              <h5 class="modal-title fw-bold mb-1" id="modalUpdateStatusLabel" style="color:#0F1E4D; font-size:1.15rem;">
                Update Project Status
              </h5>
              <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge font-monospace px-2 py-1" style="background:#f1f5f9; color:#0F1E4D; border:1px solid #e2e8f0; font-size:0.75rem;">
                  <i class="fa fa-hashtag me-1 opacity-50"></i>{{ $project->project_code }}
                </span>
                <span class="text-muted small fw-medium text-truncate" style="max-width:260px; font-size:0.75rem;">
                  <i class="fa fa-building me-1 opacity-50"></i>{{ $project->customer?->company_name ?: ($project->customer?->customer_name ?: 'Applicant') }}
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
              <span id="show_live_status_badge" class="badge px-3 py-2 fw-bold shadow-sm" style="font-size:0.85rem; border-radius:8px;">
                {{ ucwords(str_replace('_', ' ', $project->status)) }}
              </span>
              <span class="text-muted small ms-auto" id="show_live_status_category_hint" style="font-size:0.72rem;">Current Status</span>
            </div>
          </div>

          {{-- Status Input & Suggestions --}}
          <div class="mb-3">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="show_status_input" class="form-label fw-bold small text-dark mb-0">
                Status Value <span class="text-danger">*</span>
              </label>
              <span class="text-muted" style="font-size:0.72rem;">Type custom or click below</span>
            </div>
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0 text-muted" style="border-color:#cbd5e1;">
                <i class="fa fa-tag"></i>
              </span>
              <input type="text" name="status" id="show_status_input" class="form-control border-start-0 ps-1"
                     list="show_status_suggestions" value="{{ $project->status }}" placeholder="e.g. call not picked, validation, approved..."
                     required autocomplete="off" style="border-color:#cbd5e1; font-weight:500;">
              <button class="btn btn-outline-secondary border-start-0 bg-white text-muted" type="button" id="show_btn_clear_status" title="Clear input" style="border-color:#cbd5e1;">
                <i class="fa fa-xmark"></i>
              </button>
            </div>
            <datalist id="show_status_suggestions">
              <option value="draft">Draft</option>
              <option value="validation">Validation</option>
              <option value="approved">Approved</option>
              <option value="reported">Reported</option>
              <option value="call not picked">Call Not Picked</option>
              <option value="client not responding">Client Not Responding</option>
              <option value="site inspection pending">Site Inspection Pending</option>
              <option value="documents pending">Documents Pending</option>
              <option value="waiting for patta">Waiting for Patta</option>
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
              <div class="d-flex flex-wrap gap-1" id="show_group_workflow_presets">
                <button type="button" class="preset-status-btn" data-value="draft">
                  <i class="fa fa-file-pen text-secondary"></i>Draft
                </button>
                <button type="button" class="preset-status-btn" data-value="validation">
                  <i class="fa fa-magnifying-glass text-info"></i>Validation
                </button>
                <button type="button" class="preset-status-btn" data-value="approved">
                  <i class="fa fa-circle-check text-success"></i>Approved
                </button>
                <button type="button" class="preset-status-btn" data-value="reported">
                  <i class="fa fa-file-invoice text-primary"></i>Reported
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
              <div class="d-flex flex-wrap gap-1" id="show_group_operational_presets">
                <button type="button" class="preset-status-btn" data-value="call not picked">
                  <i class="fa fa-phone-slash text-danger"></i>Call Not Picked
                </button>
                <button type="button" class="preset-status-btn" data-value="client not responding">
                  <i class="fa fa-user-clock text-warning"></i>Client Not Responding
                </button>
                <button type="button" class="preset-status-btn" data-value="site inspection pending">
                  <i class="fa fa-map-location-dot text-primary"></i>Site Inspection Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="documents pending">
                  <i class="fa fa-folder-open text-warning"></i>Documents Pending
                </button>
                <button type="button" class="preset-status-btn" data-value="waiting for patta">
                  <i class="fa fa-file-signature text-secondary"></i>Waiting for Patta
                </button>
              </div>
            </div>
          </div>

          {{-- Follow-up Remarks & Call Log --}}
          <div class="mb-2">
            <div class="d-flex align-items-center justify-content-between mb-1">
              <label for="show_status_notes" class="form-label fw-bold small text-dark mb-0">
                Status Remarks & Follow-up Notes <span class="text-muted fw-normal">(Optional)</span>
              </label>
              <span class="text-muted small" id="show_notes_counter" style="font-size:0.72rem;">{{ strlen($project->status_notes ?? '') }} / 1000</span>
            </div>
            <textarea name="status_notes" id="show_status_notes" class="form-control" rows="3" maxlength="1000"
                      placeholder="Enter follow-up remarks, client call logs, reason for delay, next follow-up date..."
                      style="border-color:#cbd5e1; font-size:0.86rem; line-height:1.5;">{{ $project->status_notes }}</textarea>

            {{-- Quick Chip Inserts for Notes --}}
            <div class="mt-2 d-flex flex-wrap align-items-center gap-1">
              <span class="text-muted small me-1" style="font-size:0.7rem;"><i class="fa fa-bolt me-1 text-warning"></i>Quick log:</span>
              <button type="button" class="quick-note-chip" data-text="Called applicant; line was busy. Scheduled follow-up.">+ Call Busy</button>
              <button type="button" class="quick-note-chip" data-text="Client requested 2 working days to submit pending documents.">+ Need 2 Days</button>
              <button type="button" class="quick-note-chip" data-text="Site inspection postponed due to weather conditions.">+ Inspection Postponed</button>
              <button type="button" class="quick-note-chip" data-text="Parivesh government portal under scheduled maintenance.">+ Portal Down</button>
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
            <button type="submit" class="btn btn-save-status shadow-sm" id="show_btn_submit_status">
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
  const modalShowStatus = document.getElementById('modalUpdateStatus');
  const formShowStatus = document.getElementById('formShowUpdateStatus');
  const inputStatus = document.getElementById('show_status_input');
  const textareaNotes = document.getElementById('show_status_notes');
  const liveBadge = document.getElementById('show_live_status_badge');
  const liveCategoryHint = document.getElementById('show_live_status_category_hint');
  const notesCounter = document.getElementById('show_notes_counter');
  const btnClear = document.getElementById('show_btn_clear_status');
  const btnSubmit = document.getElementById('show_btn_submit_status');
  const presetButtons = document.querySelectorAll('#modalUpdateStatus .preset-status-btn');
  const noteChips = document.querySelectorAll('#modalUpdateStatus .quick-note-chip');

  function renderShowLiveStatus(rawStatus) {
    if (!liveBadge) return;
    const s = (rawStatus || '').trim().toLowerCase();
    const formatted = s ? s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'Draft';
    
    liveBadge.textContent = formatted;

    if (['approved', 'completed', 'active', 'verified', 'passed'].includes(s)) {
      liveBadge.style.background = '#dcfce7';
      liveBadge.style.color = '#166534';
      liveBadge.style.border = '1px solid #86efac';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: Approved';
    } 
    else if (['validation', 'uploaded', 'presented', 'agenda_scheduled', 'uploaded_to_parivesh'].includes(s)) {
      liveBadge.style.background = '#dbeafe';
      liveBadge.style.color = '#1e40af';
      liveBadge.style.border = '1px solid #93c5fd';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Milestone: In Progress';
    } 
    else if (['call not picked', 'client not responding', 'rejected', 'revision_required', 'expired', 'surrendered', 'revoked'].includes(s)) {
      liveBadge.style.background = '#fee2e2';
      liveBadge.style.color = '#991b1b';
      liveBadge.style.border = '1px solid #fca5a5';
      if (liveCategoryHint) liveCategoryHint.textContent = 'Alert: Follow-up Required';
    } 
    else if (['site inspection pending', 'documents pending', 'waiting for patta'].includes(s)) {
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

  function updateShowNotesCounter() {
    if (textareaNotes && notesCounter) {
      notesCounter.textContent = textareaNotes.value.length + ' / 1000';
    }
  }

  if (inputStatus) {
    renderShowLiveStatus(inputStatus.value);
    inputStatus.addEventListener('input', function() {
      renderShowLiveStatus(this.value);
    });
  }

  if (modalShowStatus) {
    modalShowStatus.addEventListener('shown.bs.modal', function() {
      if (inputStatus) inputStatus.focus();
    });
  }

  if (btnClear && inputStatus) {
    btnClear.addEventListener('click', function() {
      inputStatus.value = '';
      inputStatus.focus();
      renderShowLiveStatus('');
    });
  }

  presetButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      if (inputStatus) {
        inputStatus.value = this.dataset.value;
        renderShowLiveStatus(this.dataset.value);
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
        updateShowNotesCounter();
        textareaNotes.focus();
      }
    });
  });

  if (textareaNotes) {
    textareaNotes.addEventListener('input', updateShowNotesCounter);
  }

  if (formShowStatus) {
    formShowStatus.addEventListener('keydown', function(e) {
      if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        if (btnSubmit) btnSubmit.click();
      }
    });

    formShowStatus.addEventListener('submit', function() {
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
      }
    });
  }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Dynamic Folder Tabs Switcher
  const tabs = document.querySelectorAll('.admin-tab');
  const panels = document.querySelectorAll('.tab-panel');

  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      tabs.forEach(t => t.classList.remove('active'));
      panels.forEach(p => p.style.display = 'none');

      this.classList.add('active');
      const targetId = this.dataset.target;
      const targetPanel = document.getElementById(targetId);
      if (targetPanel) {
        targetPanel.style.display = 'block';
      }
    });
  });

  // Dynamic Shared Upload Modal Binding
  const modalUpload = document.getElementById('modalUploadDoc');
  if (modalUpload) {
    modalUpload.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        document.getElementById('formUploadDoc').action = btn.dataset.action;
        document.getElementById('upload_modal_title').textContent = 'Upload: ' + (btn.dataset.docName || 'Document');
      }
    });
  }

  // Dynamic Add Custom Document Modal Binding
  const modalAddEnviro = document.getElementById('modalAddEnviroDoc');
  if (modalAddEnviro) {
    modalAddEnviro.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        document.getElementById('add_doc_folder_id').value = btn.dataset.folderId || '';
        document.getElementById('add_doc_folder_name_display').value = btn.dataset.folderName || '';
        document.getElementById('add_doc_modal_title').innerHTML = '<i class="bi bi-plus-circle me-2 text-primary"></i>Add Document to ' + (btn.dataset.folderName || 'Folder');
      }
    });
  }

  // Dynamic Shared Review Modal Binding
  const modalReview = document.getElementById('modalReviewDoc');
  if (modalReview) {
    modalReview.addEventListener('show.bs.modal', function(e) {
      const btn = e.relatedTarget;
      if (btn) {
        document.getElementById('formReviewDoc').action = btn.dataset.action;
        document.getElementById('review_modal_title').textContent = 'Review: ' + (btn.dataset.docName || 'Document');
        document.getElementById('review_modal_note').value = btn.dataset.reviewNote || '';
      }
    });
  // Dynamic Document Requirement (Mandatory / Optional) Toggle
  document.querySelectorAll('.doc-req-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
      const row = this.closest('tr');
      const isMandatory = this.value === 'mandatory';
      if (row) {
        row.setAttribute('data-mandatory', isMandatory ? '1' : '0');
      }
      if (isMandatory) {
        this.className = 'form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center border-danger-subtle text-danger bg-danger-subtle';
      } else {
        this.className = 'form-select form-select-sm doc-req-select py-0 px-2 fw-bold text-center border-secondary-subtle text-muted bg-light';
      }
    });
  });
});
</script>
@endsection
