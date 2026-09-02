@extends('layouts.app')
@section('title', 'Documents')
@section('main_content')
    <div class="content-body default-height">
        <div class="container-fluid">

            <main class="page">
                <div class="page-head">
                    <div>
                        <span class="eyebrow"><i class="bi bi-folder2-open"></i> Sri Bala Traders · MDG-2026-0472</span>
                        <h1>Manage folder files</h1>
                        <p>Upload against the checklist required for a Mining Plan application.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Folder tab rail -->
                    <div class="col-lg-3">
                        <div class="surface p-2">
                            <div class="folder-tab active" data-target="panel-log" data-name="Field Log Data">
                                <div class="fic" style="background:var(--f-log-bg); color:var(--f-log);"><i
                                        class="fa fa-clone"></i></div>
                                <div>
                                    <div class="t">1 · Field Log Data</div>
                                    <div class="s">4 of 4 uploaded</div>
                                </div>
                            </div>
                            <div class="folder-tab" data-target="panel-docs" data-name="Documents">
                                <div class="fic" style="background:var(--f-docs-bg); color:var(--f-docs);"><i
                                        class="fa fa-folder-open"></i></div>
                                <div>
                                    <div class="t">2 · Documents</div>
                                    <div class="s">15 of 26 uploaded</div>
                                </div>
                            </div>
                            <div class="folder-tab" data-target="panel-photos" data-name="Site Photos">
                                <div class="fic" style="background:var(--f-photos-bg); color:var(--f-photos);"><i
                                        class="fa fa-file-image"></i></div>
                                <div>
                                    <div class="t">3 · Site Photos</div>
                                    <div class="s">6 photos uploaded</div>
                                </div>
                            </div>
                            <div class="folder-tab" data-target="panel-report" data-name="Report">
                                <div class="fic" style="background:var(--f-report-bg); color:var(--f-report);"><i
                                        class="fa fa-pencil-square"></i></div>
                                <div>
                                    <div class="t">4 · Report</div>
                                    <div class="s">1 of 3 uploaded</div>
                                </div>
                            </div>
                            <div class="folder-tab" data-target="panel-plan" data-name="Plan">
                                <div class="fic" style="background:var(--f-plan-bg); color:var(--f-plan);"><i
                                        class="fa fa-hourglass"></i></div>
                                <div>
                                    <div class="t">5 · Plan</div>
                                    <div class="s">3 of 5 uploaded</div>
                                </div>
                            </div>
                            <div class="folder-tab" data-target="panel-others" data-name="Others">
                                <div class="fic" style="background:var(--f-others-bg); color:var(--f-others);"><i
                                        class="fa fa-pie-chart"></i></div>
                                <div>
                                    <div class="t">6 · Others</div>
                                    <div class="s">DGPS &amp; drone data</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel -->
                    <div class="col-lg-9">
                        <div class="surface p-3 p-lg-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold mb-0" id="activeFolderTitle">Field Log Data</h2>
                                <span class="chip chip-ok">4/4 complete</span>
                            </div>

                            <div class="dropzone mb-4">
                                <i class="bi bi-cloud-arrow-up fs-3 d-block mb-2" style="color:var(--navy-700);"></i>
                                <div class="fw-semibold small mb-1">Drag &amp; drop files, or click to browse</div>
                                <div class="small dz-status">PDF, JPG, DWG, KML up to 25 MB each</div>
                            </div>

                            <!-- PANEL: Field Log Data -->
                            <div class="folder-panel" id="panel-log">
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-log-bg);color:var(--f-log);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Observation Sheet</div>
                                        <div class="meta">observation_sheet.pdf · 1.2 MB · uploaded 2 Aug</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-log-bg);color:var(--f-log);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Data Sheet</div>
                                        <div class="meta">data_sheet_v2.xlsx · 340 KB · uploaded 2 Aug</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-log-bg);color:var(--f-log);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Preparation Sheet</div>
                                        <div class="meta">prep_sheet.pdf · 890 KB · uploaded 3 Aug</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-log-bg);color:var(--f-log);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Way Points</div>
                                        <div class="meta">waypoints.kml · 62 KB · uploaded 3 Aug</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                            </div>

                            <!-- PANEL: Documents -->
                            <div class="folder-panel d-none" id="panel-docs">
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Application</div>
                                        <div class="meta">application_form.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Tender Gazette</div>
                                        <div class="meta">tender_gazette.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Precise Area Communication Letter</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Previous Approval Letter</div>
                                        <div class="meta">prev_approval.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Previous Approved Mining Plan</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Environmental Clearance</div>
                                        <div class="meta">ec_certificate.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Proceeding Letter</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Lease Deed Agreement</div>
                                        <div class="meta">lease_deed.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">TNPCB Certificate</div>
                                        <div class="meta">tnpcb_cert.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--danger-bg);color:var(--danger);"><i
                                            class="fa fa-times"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Permit Letter</div>
                                        <div class="meta">Rejected — scan unreadable</div>
                                    </div><span class="chip chip-danger">Rejected</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Existing Pit Letter</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">NOC Letter</div>
                                        <div class="meta">noc_letter.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">SEAC Minutes</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">SEIAA Minutes</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-docs-bg);color:var(--f-docs);"><i
                                            class="fa fa-check"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Fmb</div>
                                        <div class="meta">fmb_sketch.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>

                            </div>

                            <!-- PANEL: Site Photos -->
                            <div class="folder-panel d-none" id="panel-photos">
                                <div class="row g-2 mb-2">
                                    <div class="col-4">
                                        <div class="rounded-3"
                                            style="height:90px;background:linear-gradient(135deg,var(--f-photos-bg),var(--ink-100));display:flex;align-items:center;justify-content:center;color:var(--f-photos);">
                                            <i class="fa fa-file-image"></i>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-3"
                                            style="height:90px;background:linear-gradient(135deg,var(--f-photos-bg),var(--ink-100));display:flex;align-items:center;justify-content:center;color:var(--f-photos);">
                                            <i class="fa fa-file-image"></i>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="rounded-3"
                                            style="height:90px;background:linear-gradient(135deg,var(--f-photos-bg),var(--ink-100));display:flex;align-items:center;justify-content:center;color:var(--f-photos);">
                                            <i class="fa fa-file-image"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-photos-bg);color:var(--f-photos);"><i
                                            class="fa fa-file-image"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Site Photographs (6 files)</div>
                                        <div class="meta">site_photo_01–06.jpg · uploaded 3 Aug</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                            </div>

                            <!-- PANEL: Report -->
                            <div class="folder-panel d-none" id="panel-report">
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-report-bg);color:var(--f-report);"><i
                                            class="fa fa-file-pdf"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Covering Letter</div>
                                        <div class="meta">covering_letter.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Text</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Photocopy</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                            </div>

                            <!-- PANEL: Plan -->
                            <div class="folder-panel d-none" id="panel-plan">
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-plan-bg);color:var(--f-plan);"><i
                                            class="fa fa-check-circle"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Kml</div>
                                        <div class="meta">boundary.kml</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-plan-bg);color:var(--f-plan);"><i
                                            class="fa fa-check-circle"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">5-Plates</div>
                                        <div class="meta">five_plates.pdf</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--f-plan-bg);color:var(--f-plan);"><i
                                            class="fa fa-check-circle"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Plan</div>
                                        <div class="meta">mining_plan.dwg</div>
                                    </div><span class="chip chip-ok">Uploaded</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Reserves Table</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Source Files</div>
                                        <div class="meta">Not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                            </div>

                            <!-- PANEL: Others -->
                            <div class="folder-panel d-none" id="panel-others">
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">DGPS Data</div>
                                        <div class="meta">Optional · not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                                <div class="doc-row">
                                    <div class="dic" style="background:var(--warn-bg);color:var(--warn);"><i
                                            class="fa fa-clock"></i></div>
                                    <div class="flex-grow-1">
                                        <div class="name">Drone Data</div>
                                        <div class="meta">Optional · not uploaded yet</div>
                                    </div><span class="chip chip-warn">Pending</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>



@endsection
