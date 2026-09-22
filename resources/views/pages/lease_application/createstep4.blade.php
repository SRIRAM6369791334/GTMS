@extends('layouts.app')
@section('title', 'Folders - Step 4')
@section('main_content')
<div class="content-body default-height">
        <div class="container-fluid">

            <div class="wizard-wrap" style="max-width:860px;">

  <div class="step-progress">
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
    <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
    <div class="sp-step active"><div class="circ">4</div><div class="sp-label">Folders</div></div>
    <div class="sp-step"><div class="circ">5</div><div class="sp-label">Documents</div></div>
    <div class="sp-step"><div class="circ">6</div><div class="sp-label">Review</div></div>
  </div>

  @php
    $uploads = $draft['uploaded_docs'] ?? [];
    $f7Count = 0; $f8Count = 0; $f9Count = 0;
    foreach ($uploads as $item => $doc) {
      $fId = $doc['folder_id'] ?? null;
      if ($fId == 7) $f7Count++;
      elseif ($fId == 8) $f8Count++;
      elseif ($fId == 9) $f9Count++;
      else {
        $it = (int)$item;
        if ($it >= 1 && $it <= 9) $f7Count++;
        elseif ($it >= 10 && $it <= 16) $f8Count++;
        elseif ($it >= 17 && $it <= 19) $f9Count++;
      }
    }
    $f7Pct = round(($f7Count / 9) * 100);
    $f8Pct = round(($f8Count / 7) * 100);
    $f9Pct = round(($f9Count / 3) * 100);
  @endphp

  <div class="wizard-card">
    <div class="wc-eyebrow">Step 4 of 6</div>
    <h4>Folders</h4>
    <div class="wc-sub">Your documents are organised into three regulatory folders. Click open documents to view and upload files against the checklist.</div>

    <div class="row g-3">
      <div class="col-md-4">
        <a href="{{ route('step5') }}" class="text-decoration-none">
          <div class="folder-tile {{ $f7Pct == 100 ? 'border-success' : '' }}">
            <div class="fico text-primary"><i class="fa fa-folder-open"></i></div>
            <div class="ftitle">1. Documents</div>
            <div class="fmeta">{{ $f7Count }} / 9 files uploaded</div>
            <div class="fprogress"><div class="bar bg-primary" style="width:{{ $f7Pct }}%"></div></div>
            <span class="badge {{ $f7Pct == 100 ? 'bg-success text-white' : ($f7Count > 0 ? 'bg-info text-white' : 'badge-status pending') }}">
              {{ $f7Pct == 100 ? 'Complete' : ($f7Count > 0 ? $f7Count . '/9 Saved' : 'Pending') }}
            </span>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="{{ route('step5') }}" class="text-decoration-none">
          <div class="folder-tile {{ $f8Pct == 100 ? 'border-success' : '' }}">
            <div class="fico text-primary"><i class="fa fa-folder"></i></div>
            <div class="ftitle">2. Lease Application</div>
            <div class="fmeta">{{ $f8Count }} / 7 files uploaded</div>
            <div class="fprogress"><div class="bar bg-primary" style="width:{{ $f8Pct }}%"></div></div>
            <span class="badge {{ $f8Pct == 100 ? 'bg-success text-white' : ($f8Count > 0 ? $f8Count . '/7 Saved' : 'Pending') }}">
              {{ $f8Pct == 100 ? 'Complete' : ($f8Count > 0 ? $f8Count . '/7 Saved' : 'Pending') }}
            </span>
          </div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="{{ route('step5') }}" class="text-decoration-none">
          <div class="folder-tile {{ $f9Pct == 100 ? 'border-success' : '' }}">
            <div class="fico text-primary"><i class="fa fa-inbox"></i></div>
            <div class="ftitle">3. Plan</div>
            <div class="fmeta">{{ $f9Count }} / 3 files uploaded</div>
            <div class="fprogress"><div class="bar bg-primary" style="width:{{ $f9Pct }}%"></div></div>
            <span class="badge {{ $f9Pct == 100 ? 'bg-success text-white' : ($f9Count > 0 ? $f9Count . '/3 Saved' : 'Pending') }}">
              {{ $f9Pct == 100 ? 'Complete' : ($f9Count > 0 ? $f9Count . '/3 Saved' : 'Pending') }}
            </span>
          </div>
        </a>
      </div>
    </div>

    <div class="card-panel mt-4 mb-0" style="background:var(--navy-soft); border:none;">
      <div class="d-flex gap-2">
        <i class="bi bi-lightbulb" style="color:var(--navy);"></i>
        <div style="font-size:.78rem; color:#33447a;">
          Based on your <b>{{ $categoryName }}</b> category, 16 regulatory documents and 3 survey plans (19 total) are organized across these 3 folders.
        </div>
      </div>
    </div>

    <div class="wizard-actions d-flex justify-content-between align-items-center">
      <a href="{{ route('step3') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
      @can('application.create')
      <div class="d-flex gap-2">
        <a href="{{ route('application.index') }}" class="btn btn-outline-primary px-3"><i class="fa fa-save me-1"></i> Save Draft &amp; Exit</a>
        <a href="{{ route('step5') }}" class="btn btn-navy px-4">Open Documents <i class="bi bi-arrow-right"></i></a>
      </div>
      @endcan
    </div>
  </div>
</div>

  </div>
</div>
@endsection
