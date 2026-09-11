@extends('layouts.app')
@section('title', 'Enviro B2')
@section('main_content')

  <link href="css/style1.css" rel="stylesheet">

    <div class="content-body default-height">
        <div class="container-fluid">

             <main class="admin-content">

      <!-- ================= KPI STATS ================= -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="top-row"><span class="ic" style="background:var(--c-documents);"><i class="fa fa-clone"></i></span></div>
            <div class="value">39</div>
            <div class="lbl">Total Checklist Items</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="top-row"><span class="ic" style="background:var(--ok);"><i class="bi fa fa-check-circle"></i></span></div>
            <div class="value">21</div>
            <div class="lbl">Approved Documents</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="top-row"><span class="ic" style="background:var(--warn);"><i class="fa fa-hourglass-half"></i></div>
            <div class="value">14</div>
            <div class="lbl">Pending Review</div>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="stat-card">
            <div class="top-row"><span class="ic" style="background:var(--c-parivesh);"><i class="fa fa-paper-plane"></i></span></div>
            <div class="value">4</div>
            <div class="lbl">PARIVESH Submissions</div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- ================= QUICK ACCESS FOLDERS ================= -->
        <div class="col-lg-7">
          <div class="admin-panel mb-4">
            <div class="panel-head">
              <div><h5>Sub Category 1 &middot; Site &amp; Mining Documentation</h5><p class="sub">5 folders &middot; 24 checklist items</p></div>
              @can('environment.view')
              <a href="{{ route('environstage1') }}" class="panel-link">Open <i class="bi bi-arrow-right"></i></a>
              @endcan
            </div>
            <div class="p-3">
              <div class="row g-2">
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Documents</span><span class="text-muted">0/7</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-documents);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Report</span><span class="text-muted">0/11</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-report);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>GIS</span><span class="text-muted">1/1</span></div><div class="progress-slim"><div class="bar" style="width:100%; background:var(--c-gis);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Upload Signed Reports</span><span class="text-muted">0/1</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-upload);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>PARIVESH Registration</span><span class="text-muted">2/4</span></div><div class="progress-slim"><div class="bar" style="width:50%; background:var(--c-parivesh);"></div></div></div>
              </div>
            </div>
          </div>

          <div class="admin-panel">
            <div class="panel-head">
              <div><h5>Sub Category 2 &middot; EIA &amp; TNPCB Submission</h5><p class="sub">6 folders &middot; 15 checklist items</p></div>
              @can('environment.view')
              <a href="{{ route('environstage2') }}" class="panel-link">Open <i class="fa fa-arrow-right"></i></a>
              @endcan
            </div>

            <div class="p-3">
              <div class="row g-2">
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>ToR Letter</span><span class="text-muted">1/1</span></div><div class="progress-slim"><div class="bar" style="width:100%; background:var(--c-documents);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Baseline Study</span><span class="text-muted">2/4</span></div><div class="progress-slim"><div class="bar" style="width:50%; background:var(--c-report);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Draft (12 Chapters)</span><span class="text-muted">0/2</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-gis);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>TNPCB Submission</span><span class="text-muted">1/4</span></div><div class="progress-slim"><div class="bar" style="width:25%; background:var(--c-upload);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Final EIA Report</span><span class="text-muted">0/3</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-parivesh);"></div></div></div>
                <div class="col-6"><div class="d-flex justify-content-between small mb-1"><span>Uploading File</span><span class="text-muted">0/1</span></div><div class="progress-slim"><div class="bar" style="width:0%; background:var(--c-file);"></div></div></div>
              </div>
            </div>
          </div>
        </div>

        <!-- ================= RECENT ACTIVITY ================= -->
        <div class="col-lg-5">
          <div class="admin-panel h-100">
            <div class="panel-head">
              <div><h5>Recent Activity</h5><p class="sub">Latest actions across both folders</p></div>
              <a href="activity-log.html" class="panel-link">View all</a>
            </div>
            <div class="p-3">
              <div class="d-flex gap-3 mb-3">
                <span class="ic" style="width:34px;height:34px;font-size:.9rem;background:var(--ok-tint); color:var(--ok);"><i class="fa fa-check-circle" style="font-size:.9rem;padding: 10px"></i></span>
                <div><div class="fw-bold" style="font-size:14px;">GIS Data approved</div><div class="text-muted" style="font-size:.76rem;">Sub Category 1 &middot; 2 hours ago</div></div>
              </div>
              <div class="d-flex gap-3 mb-3">
                <span class="ic" style="width:34px;height:34px;font-size:.9rem;background:#e8ecfb; color:var(--c-documents);"><i class="fa fa-cloud-upload" style="font-size:.9rem;padding: 10px"></i></span>
                <div><div class="fw-bold" style="font-size:14px;">ToR Letter uploaded</div><div class="text-muted" style="font-size:.76rem;">Sub Category 2 &middot; 5 hours ago</div></div>
              </div>
              <div class="d-flex gap-3 mb-3">
                <span class="ic" style="width:34px;height:34px;font-size:.9rem;background:var(--warn-tint); color:var(--warn);"><i class="fa fa-hourglass-half" style="font-size:.9rem;padding: 10px"></i></span>
                <div><div class="fw-bold" style="font-size:14px;">Baseline Study pending review</div><div class="text-muted" style="font-size:.76rem;">Sub Category 2 &middot; Yesterday</div></div>
              </div>
              <div class="d-flex gap-3 mb-3">
                <span class="ic" style="width:34px;height:34px;font-size:.9rem;background:var(--danger-tint); color:var(--danger);"><i class="fa fa-paper-plane" style="font-size:.9rem;padding: 10px"></i></span>
                <div><div class="fw-bold" style="font-size:14px;">Payment Receipt sent back for correction</div><div class="text-muted" style="font-size:.76rem;">Sub Category 1 &middot; 2 days ago</div></div>
              </div>
              <div class="d-flex gap-3">
                <span class="ic" style="width:34px;height:34px;font-size:.9rem;background:#f2ecfa; color:var(--c-gis);"><i class="fa fa-globe-americas" style="font-size:.9rem;padding: 10px"></i></span>
                <div><div class="fw-bold" style="font-size:14px;">GIS Data uploaded</div><div class="text-muted" style="font-size:.76rem;">Sub Category 1 &middot; 3 days ago</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>

        </div>
    </div>

@endsection
