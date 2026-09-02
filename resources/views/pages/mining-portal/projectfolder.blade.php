@extends('layouts.app')
@section('title', 'Project Folders')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <main class="page">
                <div class="page-head">
                    <div>
                        <span class="eyebrow"><i class="bi bi-folder2-open"></i> Application MDG-2026-0472</span>
                        <h1>Sri Bala Traders — Gravel, Mining Plan</h1>
                        <p>Salem District · Created 3 Aug 2026 · Last activity 2 hours ago</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="/newapplication" class="btn btn-outline-navy"><i class="bi bi-pencil me-1"></i>Edit
                            details</a>
                        {{-- <a href="process-flow.html" class="btn btn-navy"><i class="bi bi-play-fill me-1"></i>Continue process</a> --}}
                    </div>
                </div>

                <!-- Client / District / Mineral / Plan chips (steps 1-4 recap) -->
                <div class="surface p-3 mb-4">
                    <div class="row g-3 text-center text-md-start">
                        <div class="col-6 col-md-3">
                            <div class="small-caps-label mb-1"><i class="bi bi-person-fill"></i> Client</div>
                            <div class="fw-semibold">Sri Bala Traders</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small-caps-label mb-1"><i class="bi bi-geo-alt-fill"></i> District</div>
                            <div class="fw-semibold">Salem</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small-caps-label mb-1"><i class="bi bi-gem"></i> Mineral</div>
                            <div class="fw-semibold">Gravel</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="small-caps-label mb-1"><i class="bi bi-clipboard-check"></i> Plan</div>
                            <div class="fw-semibold">Mining Plan</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 fw-bold mb-0">Folder structure</h2>
                    <span class="small text-muted">63% of required files uploaded</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-log);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-log-bg); color:var(--f-log);"><i
                                            class="fa fa-clone"></i></div>
                                    <span class="chip chip-ok">4/4</span>
                                </div>
                                <h3>1 · Field Log Data</h3>
                                <div class="count">Observation sheet, Data Sheet, Preparation Sheet, Way Points</div>
                                <div class="prog"><span style="width:100%; background:var(--f-log);"></span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-docs);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-docs-bg); color:var(--f-docs);"><i
                                            class="fa fa-folder-open"></i></div>
                                    <span class="chip chip-warn">15/26</span>
                                </div>
                                <h3>2 · Documents</h3>
                                <div class="count">Application, Lease Deed, TNPCB Certificate, Patta, GST Certificate +21
                                    more</div>
                                <div class="prog"><span style="width:58%; background:var(--f-docs);"></span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-photos);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-photos-bg); color:var(--f-photos);"><i
                                            class="fa fa-file-image"></i></div>
                                    <span class="chip chip-ok">6 files</span>
                                </div>
                                <h3>3 · Site Photos</h3>
                                <div class="count">Site photographs from field survey</div>
                                <div class="prog"><span style="width:100%; background:var(--f-photos);"></span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-report);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-report-bg); color:var(--f-report);"><i
                                            class="fa fa-pencil-square"></i></div>
                                    <span class="chip chip-danger">1/3</span>
                                </div>
                                <h3>4 · Report</h3>
                                <div class="count">Covering Letter, Text, Photocopy</div>
                                <div class="prog"><span style="width:33%; background:var(--f-report);"></span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-plan);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-plan-bg); color:var(--f-plan);"><i
                                            class="fa fa-hourglass"></i></div>
                                    <span class="chip chip-warn">3/5</span>
                                </div>
                                <h3>5 · Plan</h3>
                                <div class="count">Kml, 5-Plates, Plan, Reserves Table, Source Files</div>
                                <div class="prog"><span style="width:60%; background:var(--f-plan);"></span></div>
                            </div>
                        </a>
                    </div>
                    <div class="col-sm-6 col-lg-4">
                        <a href="/document" class="text-decoration-none text-reset">
                            <div class="folder-card" style="--c:var(--f-others);">
                                <div class="d-flex justify-content-between">
                                    <div class="fic" style="background:var(--f-others-bg); color:var(--f-others);"><i
                                            class="fa fa-pie-chart"></i></div>
                                    <span class="chip chip-navy">Optional</span>
                                </div>
                                <h3>6 · Others</h3>
                                <div class="count">DGPS data &amp; drone data</div>
                                <div class="prog"><span style="width:0%; background:var(--f-others);"></span></div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="surface p-3 p-lg-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="ic"
                            style="width:44px;height:44px;border-radius:11px;background:var(--gold-100);color:var(--gold-600);display:flex;align-items:center;justify-content:center;font-size:19px;">
                            <i class="fa fa-bars"></i>
                        </div>
                        <div>
                            <div class="fw-semibold small">All required folders are ≥60% complete</div>
                            <div class="text-muted" style="font-size:12px;">You can move this application to validation
                                once Report reaches 100%.</div>
                        </div>
                    </div>
                    <a href="process-flow.html" class="btn btn-navy">Send for validation <i
                            class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </main>


        </div>
    </div>





@endsection
