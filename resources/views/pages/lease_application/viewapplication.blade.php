@extends('layouts.app')
@section('title', 'Category')
@section('main_content')

<div class="content-body default-height">
        <div class="container-fluid">
            <div class="content">

    <div class="page-head">
      <div>
        <div class="breadcrumb-min mb-1"><a href="applications.html">Applications</a> &nbsp;/&nbsp; LA-2026-0142</div>
        <h4>Application #LA-2026-0142 <span class="text-muted fw-normal">— R. Kumaresan</span></h4>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="status-pill"><span class="blip"></span>Under Validation</span>
          <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-geo-alt"></i> Coimbatore District</span>
          <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-tag"></i> Category: Rule 44</span>
          <span class="text-muted" style="font-size:.78rem;"><i class="bi bi-calendar3"></i> Submitted 28 Jul 2026</span>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-download"></i> Export Summary</button>
        <button class="btn btn-navy btn-sm"><i class="bi bi-pencil-square"></i> Edit Application</button>
      </div>
    </div>

    <div class="row g-3 mb-2">
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:var(--teal);"><i class="fa fa-folder"></i></div>
          <div class="stat-value">12 / 16</div>
          <div class="stat-label">Documents uploaded</div>
          <div class="stat-sub" style="color:var(--orange);"><i class="bi bi-exclamation-circle"></i> 4 pending</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:var(--purple);"><i class="fa fa-cloud-upload"></i></div>
          <div class="stat-value">3 / 3</div>
          <div class="stat-label">Plan files uploaded</div>
          <div class="stat-sub" style="color:var(--green);"><i class="bi bi-check-circle"></i> Complete</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:var(--pink);"><i class="fa fa-clone"></i></div>
          <div class="stat-value">Registered</div>
          <div class="stat-label">MIMAS credentials</div>
          <div class="stat-sub" style="color:var(--green);"><i class="bi bi-check-circle"></i> Verified</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon" style="background:var(--orange);"><i class="fa fa-calendar"></i></div>
          <div class="stat-value">6 days</div>
          <div class="stat-label">In current stage</div>
          <div class="stat-sub text-muted"><i class="bi bi-arrow-clockwise"></i> SLA: 10 days</div>
        </div>
      </div>
    </div>

    <div class="card-panel">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div>
          <div class="panel-title">Process Flow</div>
          <div class="panel-sub">Upload &amp; Store → Validate → Approve → Generate Reports → Archive &amp; Backup</div>
        </div>
        <div class="progress-thin flex-grow-1 mx-4 d-none d-md-block" style="max-width:180px; margin-top:6px;">
          <div class="progress-bar" role="progressbar" style="width:35%"></div>
        </div>
      </div>
      <div class="flow-stepper mt-4">
        <div class="fstep done">
          <div class="circ"><i class="bi bi-check-lg"></i></div>
          <div class="flabel">Upload &amp; Store</div>
          <div class="fsub">Completed 30 Jul</div>
        </div>
        <div class="fstep active">
          <div class="circ">2</div>
          <div class="flabel">Validate Data</div>
          <div class="fsub">In progress</div>
        </div>
        <div class="fstep">
          <a href="#" style="text-decoration:none; color:inherit;">
            <div class="circ">3</div>
            <div class="flabel">Approve Data</div>
            <div class="fsub">Pending</div>
          </a>
        </div>
        <div class="fstep">
          <a href="reports.html" style="text-decoration:none; color:inherit;">
            <div class="circ">4</div>
            <div class="flabel">Generate Reports</div>
            <div class="fsub">Pending</div>
          </a>
        </div>
        <div class="fstep">
          <a href="backup.html" style="text-decoration:none; color:inherit;">
            <div class="circ">5</div>
            <div class="flabel">Archive &amp; Backup</div>
            <div class="fsub">Pending</div>
          </a>
        </div>
      </div>
      <div class="alert d-flex align-items-center gap-2 mt-4 mb-0" style="background:var(--orange-soft); border:1px solid #f3d9b3; color:#7a4406; font-size:.82rem;">
        <i class="bi bi-info-circle fs-6"></i>
        4 documents are still pending upload. Validation cannot be marked complete until all mandatory documents are received.
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card-panel">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
              <div class="panel-title">Document Checklist</div>
              <div class="panel-sub mb-0">16 required items across Documents, Lease Application &amp; Plan folders</div>
            </div>
            <a href="step5-documents.html" class="btn btn-sm" style="background:var(--navy-soft); color:var(--navy); font-weight:600;"><i class="bi bi-upload"></i> Upload New</a>
          </div>
          <div class="table-responsive">
            <table class="table doc-table mb-0">
              <thead>
                <tr>
                  <th style="width:40px;">#</th>
                  <th>Document</th>
                  <th>Folder</th>
                  <th>Status</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td><span class="file-chip"><i class="bi bi-file-earmark-pdf text-danger"></i> Lease application - signed, FMB, Plan</span></td>
                  <td>Lease Application</td>
                  <td><span class="badge-status uploaded">Uploaded</span></td>
                  <td class="text-end"><i class="bi bi-eye text-muted"></i></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td><span class="file-chip"><i class="bi bi-file-earmark-pdf text-danger"></i> Affidavit - Income Tax</span></td>
                  <td>Lease Application</td>
                  <td><span class="badge-status uploaded">Uploaded</span></td>
                  <td class="text-end"><i class="bi bi-eye text-muted"></i></td>
                </tr>
                <tr>
                  <td>3</td>
                  <td><span class="file-chip text-muted"><i class="bi bi-file-earmark"></i> IT returns (If Applicable)</span></td>
                  <td>Documents</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td class="text-end"><a href="step5-documents.html" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.72rem;">Request</a></td>
                </tr>
                <tr>
                  <td>4</td>
                  <td><span class="file-chip text-muted"><i class="bi bi-file-earmark"></i> Affidavit - Mining Due</span></td>
                  <td>Lease Application</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td class="text-end"><a href="step5-documents.html" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.72rem;">Request</a></td>
                </tr>
                <tr>
                  <td>5</td>
                  <td><span class="file-chip"><i class="bi bi-file-earmark-pdf text-danger"></i> Affidavit - Mining Lease</span></td>
                  <td>Lease Application</td>
                  <td><span class="badge-status verified">Verified</span></td>
                  <td class="text-end"><i class="bi bi-eye text-muted"></i></td>
                </tr>
                <tr>
                  <td>8</td>
                  <td><span class="file-chip"><i class="bi bi-file-earmark-pdf text-danger"></i> Land Document</span></td>
                  <td>Documents</td>
                  <td><span class="badge-status uploaded">Uploaded</span></td>
                  <td class="text-end"><i class="bi bi-eye text-muted"></i></td>
                </tr>
                <tr>
                  <td>15</td>
                  <td><span class="file-chip text-muted"><i class="bi bi-file-earmark"></i> Mineral Management System - Application</span></td>
                  <td>Documents</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td class="text-end"><a href="step5-documents.html" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.72rem;">Request</a></td>
                </tr>
                <tr>
                  <td>16</td>
                  <td><span class="file-chip text-muted"><i class="bi bi-file-earmark"></i> Challan downloaded from Mimas</span></td>
                  <td>Documents</td>
                  <td><span class="badge-status pending">Pending</span></td>
                  <td class="text-end"><a href="step5-documents.html" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size:.72rem;">Request</a></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <small class="text-muted">Showing 7 of 16 documents</small>
            <a href="step5-documents.html" style="font-size:.8rem; font-weight:600; color:var(--navy);">View full checklist <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>

        <div class="card-panel d-flex justify-content-between align-items-center flex-wrap gap-3">
          <div>
            <div class="panel-title mb-0">Ready to move this application forward?</div>
            <div class="panel-sub mb-0">Approving will lock the document set and generate the compliance report automatically.</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Review &amp; Correct</button>
            <a href="reports.html" class="btn btn-green btn-sm"><i class="bi bi-check-circle"></i> Approve Data</a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card-panel">
          <div class="panel-title">Folders</div>
          <div class="panel-sub">Storage summary by folder type</div>

          <div class="folder-mini">
            <div class="fico"><i class="fa fa-folder-open"></i></div>
            <div class="flex-grow-1">
              <div class="ft">Documents</div>
              <div class="fs">8 / 12 files</div>
            </div>
            <span class="badge-status pending">Pending</span>
          </div>
          <div class="folder-mini">
            <div class="fico"><i class="fa fa-folder"></i></div>
            <div class="flex-grow-1">
              <div class="ft">Lease Application</div>
              <div class="fs">4 / 4 files</div>
            </div>
            <span class="badge-status uploaded">Complete</span>
          </div>
          <div class="folder-mini mb-0">
            <div class="fico"><i class="fa fa-inbox"></i></div>
            <div class="flex-grow-1">
              <div class="ft">Plan</div>
              <div class="fs">Source, KML, PDF</div>
            </div>
            <span class="badge-status uploaded">Complete</span>
          </div>
        </div>

        <div class="card-panel">
          <div class="panel-title">Activity Timeline</div>
          <div class="panel-sub">Auto-logged from audit trail</div>
          <div class="timeline">
            <div class="tl-item">
              <div class="tl-title">Validation started</div>
              <div class="tl-meta">Today, 09:12 AM &middot; System</div>
            </div>
            <div class="tl-item">
              <div class="tl-title">12 documents uploaded &amp; stored</div>
              <div class="tl-meta">30 Jul 2026, 4:47 PM &middot; R. Selvam</div>
            </div>
            <div class="tl-item">
              <div class="tl-title">MIMAS registration saved</div>
              <div class="tl-meta">29 Jul 2026, 11:20 AM &middot; R. Selvam</div>
            </div>
            <div class="tl-item">
              <div class="tl-title">Application created</div>
              <div class="tl-meta">28 Jul 2026, 10:05 AM &middot; R. Selvam</div>
            </div>
          </div>
        </div>

        <div class="card-panel mb-0">
          <div class="panel-title">MIMAS Details</div>
          <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
            <span class="text-muted">User ID</span><span class="fw-semibold">mimas_kumaresan01</span>
          </div>
          <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
            <span class="text-muted">Email</span><span class="fw-semibold">kumaresan@example.com</span>
          </div>
          <div class="d-flex justify-content-between py-1" style="font-size:.82rem;">
            <span class="text-muted">Contact</span><span class="fw-semibold">98xxxxxx21</span>
          </div>
        </div>
      </div>
    </div>

  </div>


        </div>
</div>




@endsection
