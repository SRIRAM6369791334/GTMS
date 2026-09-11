@extends('layouts.app')
@section('title', 'DGPS Survey Workflow')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php($labels=['Survey Request','Field Survey','Data Processing','Survey Report','GTM Upload', 'Preview'])
@php($steps=[['DGPS Survey Request',['Request Letter','Lease Area Details','Approved Mining Plan','EC Copy'],'fa-file-alt'],['DGPS Field Survey',['Establish Control Points','DGPS / Total Station Survey','Boundary Survey','Area Calculation'],'fa-satellite'],['Data Processing',['Point Processing','Area Computation','Map Preparation'],'fa-cogs'],['DGPS Survey Report',['Survey Report','KML / CSV Files','Survey Map / Sketch'],'fa-file-contract'],['Report Upload in GTM Portal',['Upload completed DGPS survey report and files'],'fa-cloud-upload-alt'],['Preview',['Review submitted data','Confirm completion'],'fa-eye']])
<div class="content-body default-height"><div class="container-fluid"><div class="wizard-wrap"><div class="step-progress">@foreach($labels as $number=>$label)<div class="sp-step {{ $number+1 < $step ? 'done' : ($number+1 === $step ? 'active' : '') }}"><div class="circ">@if($number+1 < $step)<i class="bi bi-check-lg"></i>@else{{ $number+1 }}@endif</div><div class="sp-label">{{ $label }}</div></div>@endforeach</div><div class="wizard-card"><div class="wc-eyebrow">DGPS Department &mdash; Survey &middot; Sub Process {{ $step }} of 6</div><h4>{{ $steps[$step-1][0] }}</h4><div class="wc-sub">Complete the listed DGPS survey activities before continuing to the next stage.</div>
@if($step===1)<div class="row g-3"><div class="col-md-6"><label class="form-label">Survey Request No. *</label><input class="form-control" placeholder="DGPS-2026-0031"></div><div class="col-md-6"><label class="form-label">Client / Applicant *</label><input class="form-control" placeholder="Applicant name"></div><div class="col-md-6"><label class="form-label">Lease Area</label><input class="form-control" placeholder="Area in hectares"></div><div class="col-md-6"><label class="form-label">Location</label><input class="form-control" placeholder="District / village"></div></div>@endif
@if($step===2)<div class="row g-3"><div class="col-md-6"><label class="form-label">Survey Date</label><input class="form-control" type="date"></div><div class="col-md-6"><label class="form-label">Survey Team</label><input class="form-control" placeholder="Assigned survey team"></div></div>@endif
@if($step===3)<div class="dropzone"><i class="bi bi-geo-alt"></i><div class="dz-title">Process collected DGPS field points</div><div class="dz-sub">Prepare coordinates, area calculations and survey map</div></div>@endif
@if($step===4)<div class="card-panel" style="min-height:190px;background:#f8fafc"><div class="text-center pt-3"><i class="fa fa-map" style="font-size:3.5rem;color:var(--navy)"></i><h5 class="mt-3">DGPS Survey Report</h5><p class="text-muted small">Survey report, KML / CSV files and map sketch.</p><button type="button" class="btn btn-outline-navy btn-sm">View Report</button></div></div>@endif
@if($step===5)<div class="card-panel" style="background:var(--green-soft);border:none"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Output: DGPS Survey Completed and report uploaded in GTM portal.</span></div>@endif
@if($step===6)
<div class="card-panel mt-4 mb-4" style="background:var(--navy-soft); border:none;">
  <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="bi bi-card-checklist me-2"></i>DGPS Survey Summary</h6>
  <div class="row g-2" style="font-size: 0.85rem;">
    <div class="col-md-6"><span class="text-muted">Client / Applicant:</span> <br><b>Applicant name</b></div>
    <div class="col-md-6"><span class="text-muted">Survey Request No:</span> <br><b>DGPS-2026-0031</b></div>
    <div class="col-md-6 mt-2"><span class="text-muted">Lease Area:</span> <br><b>2.5 Hectares</b></div>
    <div class="col-md-6 mt-2"><span class="text-muted">Status:</span> <br><span class="badge bg-success">All Stages Completed</span></div>
  </div>
</div>
<div class="card-panel mb-0" style="background:var(--green-soft);border:none"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Please confirm to finalize the DGPS Survey process.</span></div>
@endif
<div class="mt-4">@foreach($steps[$step-1][1] as $item)<div class="checklist-row"><div class="ci-icon"><i class="bi bi-check-lg"></i></div><div class="flex-grow-1"><div class="ci-name">{{ $loop->iteration }}. {{ $item }}</div><div class="ci-meta">{{ $step < 6 ? 'To be completed' : 'Completed' }}</div></div><span class="badge-status {{ $step < 6 ? 'pending' : 'verified' }}">{{ $step < 6 ? 'Pending' : 'Completed' }}</span></div>@endforeach</div><div class="wizard-actions"><a href="{{ $step === 1 ? route('dgps-survey.index') : route('dgps-survey.step',$step-1) }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> {{ $step===1?'Cancel':'Back' }}</a>@can('dgps.create')<a href="{{ $step === 6 ? route('dgps-survey.index') : route('dgps-survey.step',$step+1) }}" class="btn {{ $step===6?'btn-green':'btn-navy' }} px-4">{{ $step===6?'Finish Process':'Save & Continue' }} <i class="bi bi-arrow-right"></i></a>@endcan</div></div></div></div></div>

@endsection
