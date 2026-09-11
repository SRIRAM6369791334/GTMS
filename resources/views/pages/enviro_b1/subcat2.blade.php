@extends('layouts.app')
@section('title', 'Sub Category 2')
@section('main_content')
 <link href="css/style1.css" rel="stylesheet">


    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="row page-titles">
                <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard · Sub Category 1</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Sub Category 2 Table</a></li>
                </ol>
            </div>
            <!-- row -->
            <main class="admin-content">

      <!-- ================= FOLDER TABS ================= -->
      <div class="admin-tabs">
        <div class="admin-tab active" data-target="tab-1" style="--tab-color:var(--c-documents); --tab-tint:#eef1fb;"><span class="badge-num" style="background:var(--c-documents);">1</span>ToR Letter <span class="cnt">1</span></div>
        <div class="admin-tab" data-target="tab-2" style="--tab-color:var(--c-report); --tab-tint:#e9f7ef;"><span class="badge-num" style="background:var(--c-report);">2</span>Baseline Study <span class="cnt">4</span></div>
        <div class="admin-tab" data-target="tab-3" style="--tab-color:var(--c-gis); --tab-tint:#f2ecfa;"><span class="badge-num" style="background:var(--c-gis);">3</span>Draft (12 Ch.) <span class="cnt">2</span></div>
        <div class="admin-tab" data-target="tab-4" style="--tab-color:var(--c-upload); --tab-tint:#fdf1e2;"><span class="badge-num" style="background:var(--c-upload);">4</span>TNPCB Submission <span class="cnt">4</span></div>
        <div class="admin-tab" data-target="tab-5" style="--tab-color:var(--c-parivesh); --tab-tint:#fbe9f0;"><span class="badge-num" style="background:var(--c-parivesh);">5</span>Final EIA Report <span class="cnt">3</span></div>
        <div class="admin-tab" data-target="tab-6" style="--tab-color:var(--c-file); --tab-tint:#e3f6f3;"><span class="badge-num" style="background:var(--c-file);">6</span>Uploading File <span class="cnt">1</span></div>
      </div>

      <!-- ================= TAB 1: TOR LETTER ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-1">
        <div class="panel-head">
          <div><h5>1 &middot; Documents (ToR Letter)</h5><p class="sub">Terms of Reference correspondence</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-documents);">1 / 1 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">ToR Letter</span><div class="doc-hint">Terms of Reference issued for the project</div></td><td><span class="badge-status approved" data-cyclable="1" data-state="approved"><i class="fa fa-check-circle"></i> Approved</span></td><td>4 days ago</td><td>S. Priya</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 2: BASELINE STUDY ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-2" style="display:none;">
        <div class="panel-head">
          <div><h5>2 &middot; Baseline Study</h5><p class="sub">Baseline data collection by EIA experts</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-report);">0 / 4 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Air Quality Monitoring</span></td><td><span class="badge-status uploaded" data-cyclable="1" data-state="uploaded"><i class="bi bi-cloud-check"></i> Uploaded</span></td><td>Yesterday</td><td>Dr. Meena</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Noise Monitoring</span></td><td><span class="badge-status uploaded" data-cyclable="1" data-state="uploaded"><i class="bi bi-cloud-check"></i> Uploaded</span></td><td>Yesterday</td><td>Dr. Meena</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Water Quality Monitoring</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Soil Study</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 3: DRAFT ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-3" style="display:none;">
        <div class="panel-head">
          <div><h5>3 &middot; Draft (12 Chapters)</h5><p class="sub">Draft EIA report preparation</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-gis);">0 / 2 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-gis);"></span><span class="doc-name">Draft Preparation — 12 Chapters</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-gis);"></span><span class="doc-name">ToR Compliance</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 4: TNPCB SUBMISSION ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-4" style="display:none;">
        <div class="panel-head">
          <div><h5>4 &middot; TNPCB Draft Submission</h5><p class="sub">Draft EIA, executive summary &amp; public hearing</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-upload);">0 / 4 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-upload);"></span><span class="doc-name">Draft EIA &amp; Executive Summary</span><div class="doc-hint">Prepared in Tamil &amp; English</div></td><td><span class="badge-status uploaded" data-cyclable="1" data-state="uploaded"><i class="bi bi-cloud-check"></i> Uploaded</span></td><td>3 days ago</td><td>S. Priya</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-upload);"></span><span class="doc-name">Public Hearing PPT</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-upload);"></span><span class="doc-name">Public Opinion Poll</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-upload);"></span><span class="doc-name">Recording of Queries</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 5: FINAL EIA REPORT ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-5" style="display:none;">
        <div class="panel-head">
          <div><h5>5 &middot; Final EIA Report</h5><p class="sub">Final report with action plan &amp; EMP</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-parivesh);">0 / 3 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Final EIA Report — 12 Chapters</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Integrating Public Queries</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Action Plan + EMP</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 6: UPLOADING FILE ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-6" style="display:none;">
        <div class="panel-head">
          <div><h5>6 &middot; Uploading File</h5><p class="sub">Final online submission</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-file);">0 / 1 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-file);"></span><span class="doc-name">Uploading File</span><div class="doc-hint">Online submission on the PARIVESH Portal</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>



    </main>



        </div>
    </div>



@endsection


