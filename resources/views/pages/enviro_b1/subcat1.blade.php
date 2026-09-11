@extends('layouts.app')
@section('title', 'Sub Category 1')
@section('main_content')
<link href="css/style1.css" rel="stylesheet">

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard · Sub Category 1</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Site & Mining Documentation</a></li>
                </ol>
            </div>
            <!-- row -->

              <main class="admin-content">

      <!-- ================= FOLDER TABS ================= -->
      <div class="admin-tabs">
        <div class="admin-tab active" data-target="tab-1" style="--tab-color:var(--c-documents); --tab-tint:#eef1fb;"><span class="badge-num" style="background:var(--c-documents);">1</span>Documents <span class="cnt">7</span></div>
        <div class="admin-tab" data-target="tab-2" style="--tab-color:var(--c-report); --tab-tint:#e9f7ef;"><span class="badge-num" style="background:var(--c-report);">2</span>Report <span class="cnt">11</span></div>
        <div class="admin-tab" data-target="tab-3" style="--tab-color:var(--c-gis); --tab-tint:#f2ecfa;"><span class="badge-num" style="background:var(--c-gis);">3</span>GIS <span class="cnt">1</span></div>
        <div class="admin-tab" data-target="tab-4" style="--tab-color:var(--c-upload); --tab-tint:#fdf1e2;"><span class="badge-num" style="background:var(--c-upload);">4</span>Upload Signed Reports <span class="cnt">1</span></div>
        <div class="admin-tab" data-target="tab-5" style="--tab-color:var(--c-parivesh); --tab-tint:#fbe9f0;"><span class="badge-num" style="background:var(--c-parivesh);">5</span>PARIVESH Registration <span class="cnt">4</span></div>
      </div>

      <!-- ================= TAB 1: DOCUMENTS ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-1">
        <div class="panel-head">
          <div><h5>1 &middot; Documents</h5><p class="sub">Site ownership &amp; statutory documents</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-documents);">0 / 7 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">500m Radius Letter</span><div class="doc-hint">Radius clearance letter for the mining site</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">Existing Pit Letter</span><div class="doc-hint">Declaration of existing pit status</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">Approved Mining Plan Book</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">Approved Letter</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">300m Radius VAO Statement</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">NOC Letters</span><div class="doc-hint">No-objection certificates from relevant departments</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-documents);"></span><span class="doc-name">Others</span><div class="doc-hint">PAN, email, phone, Aadhaar verification, ToR prep list, ToR observation sheet</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 2: REPORT ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-2" style="display:none;">
        <div class="panel-head">
          <div><h5>2 &middot; Report</h5><p class="sub">Pre-feasibility &amp; ToR preparation reports</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-report);">0 / 11 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Location Details</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Covering Letter</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Front Page</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Form-1</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Pre-feasibility Report</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Request ToR</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Executive Summary</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Affidavit</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Checklist</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">Checklist for B1 Category</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-report);"></span><span class="doc-name">PPT</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 3: GIS ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-3" style="display:none;">
        <div class="panel-head">
          <div><h5>3 &middot; GIS</h5><p class="sub">Geo-spatial site data</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-gis);">0 / 1 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-gis);"></span><span class="doc-name">GIS Data</span><div class="doc-hint">Shapefiles / KML geo-boundary data</div></td><td><span class="badge-status uploaded" data-cyclable="1" data-state="uploaded"><i class="bi bi-cloud-check"></i> Uploaded</span></td><td>Today, 10:12 AM</td><td>S. Priya</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button> <button class="row-action-btn"><i class="fa fa-ellipsis-v"></i></button></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 4: UPLOAD SIGNED REPORTS ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-4" style="display:none;">
        <div class="panel-head">
          <div><h5>4 &middot; Upload Signed Reports</h5><p class="sub">Digitally / physically signed final reports</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-upload);">0 / 1 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-upload);"></span><span class="doc-name">Upload Signed Reports</span><div class="doc-hint">Countersigned PDF of the report bundle</div></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ================= TAB 5: PARIVESH ================= -->
      <div class="admin-panel tab-panel mb-4" id="tab-5" style="display:none;">
        <div class="panel-head">
          <div><h5>5 &middot; PARIVESH Online Registration</h5><p class="sub">Portal credentials &amp; application submission</p></div>
          <span class="panel-progress-chip fw-bold" style="font-size:.78rem; color:var(--c-parivesh);">2 / 4 approved</span>
        </div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th style="width:36px;"><input type="checkbox"></th><th>Document</th><th>Status</th><th>Updated</th><th>Uploaded By</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Note Pad</span><div class="doc-hint">User ID &amp; password for PARIVESH login</div></td><td><span class="badge-status approved" data-cyclable="1" data-state="approved"><i class="fa fa-check-circle"></i> Approved</span></td><td>2 days ago</td><td>R. Kannan</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
              <tr><td><input type="checkbox" checked></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Common Application Form</span></td><td><span class="badge-status approved" data-cyclable="1" data-state="approved"><i class="fa fa-check-circle"></i> Approved</span></td><td>2 days ago</td><td>R. Kannan</td><td class="text-end"><button class="row-action-btn"><i class="fa fa-eye"></i></button></td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Form 1</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
              <tr><td><input type="checkbox"></td><td><span class="row-mod-dot" style="background:var(--c-parivesh);"></span><span class="doc-name">Payment Receipt</span></td><td><span class="badge-status pending" data-cyclable="1" data-state="pending"><i class="fa fa-hourglass-half"></i> Pending</span></td><td>—</td><td>—</td><td class="text-end">@can('environment.create')<button class="row-action-btn"><i class="fa fa-upload"></i></button>@endcan</td></tr>
            </tbody>
          </table>
        </div>
      </div>



    </main>



        </div>
    </div>




@endsection

n
