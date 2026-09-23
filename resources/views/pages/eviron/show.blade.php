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
                $statusColors = [
                  'draft'      => ['#64748b', '#f1f5f9', '#e2e8f0'],
                  'validation' => ['#b45309', '#fef3c7', '#fde68a'],
                  'approved'   => ['#15803d', '#ecfdf5', '#86efac'],
                  'reported'   => ['#1d4ed8', '#eff6ff', '#bfdbfe'],
                  'archived'   => ['#7c3aed', '#f5f3ff', '#ddd6fe'],
                ];
                $stCol = $statusColors[$project->status] ?? ['#64748b', '#f1f5f9', '#e2e8f0'];
              @endphp
              <span class="badge" style="background:{{ $stCol[1] }}; color:{{ $stCol[0] }}; border:1px solid {{ $stCol[2] }}; font-size:.82rem;">
                {{ ucfirst($project->status) }}
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
          </div>

          {{-- Stage Action Buttons --}}
          <div class="d-flex gap-2 align-items-center">
            @if($project->category === 'B1')
              {{-- B1 Sequential Statutory Stage Buttons --}}
              @if($project->b1_stage === 'sc1_prep')
                <form method="POST" action="{{ route('eviron.submitSc1ToPpt', $project->id) }}" onsubmit="return confirm('Submit SC1 (ToR & Mining Documents) to PPT Department for Stage 1 ToR Presentation?');">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-paper-plane me-1"></i> Submit SC1 to PPT Department (Stage 1 Gate)
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
                <form method="POST" action="{{ route('eviron.submitSc2ToPpt', $project->id) }}" onsubmit="return confirm('Submit SC2 (EIA Study & TNPCB Submission) to PPT Department for Stage 2 Final EC Presentation?');">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-paper-plane me-1"></i> Submit SC2 to PPT Department (Stage 2 Gate)
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
                <span class="fw-bold small {{ $isStep1Active ? 'text-white' : ($isStep1Done ? 'text-success' : 'text-dark') }}">Stage 1: SC1 Intake</span>
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
                <span class="fw-bold small {{ $isStep3Active ? 'text-white' : ($isStep3Done ? 'text-success' : 'text-dark') }}">Stage 2: SC2 Unlocked</span>
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
              Once PPT Department approves the ToR Presentation, Sub Category 2 (SC2) will automatically unlock here with 6 new document folders.
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
                  <th>Status</th>
                  <th>Uploaded File</th>
                  <th>Uploaded At</th>
                  <th class="text-end" style="min-width:110px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($docs as $dIdx => $doc)
                @php
                  $statusBadgeClass = match($doc->status) {
                    'approved'          => 'bg-success text-white',
                    'validated'         => 'bg-info text-white',
                    'uploaded'          => 'bg-primary text-white',
                    'revision_required' => 'bg-danger text-white',
                    default             => 'bg-secondary text-white',
                  };
                @endphp
                <tr>
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
                  <td colspan="6" class="text-center py-4 text-muted">
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
  }
});
</script>
@endsection
