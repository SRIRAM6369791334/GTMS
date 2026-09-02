@extends('layouts.app')
@section('title', 'Process Flow')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <main class="page">
                <div class="page-head">
                    <div>
                        <span class="eyebrow"><i class="bi bi-diagram-3"></i> Stage 6 · Process Flow</span>
                        <h1>Move this application through validation</h1>
                        <p>Current stage: <b>Validate Data</b> — 2 items were flagged and sent back for correction.</p>
                    </div>
                </div>

                <!-- 6.1 – 6.6 track -->
                <div class="surface p-3 p-lg-4 mb-4">
                    <div class="pf-track">
                        <div class="pf-card">
                            <div class="num">6.1</div>
                            <h4><i class="bi bi-cloud-arrow-up me-1" style="color:var(--navy-700);"></i>Upload &amp; Store
                            </h4>
                            <p>All files placed in their respective folders.</p>
                            <span class="chip chip-ok mt-2"><i class="bi bi-check2"></i> Done</span>
                        </div>
                        <div class="pf-arrow"><i class="bi bi-chevron-right"></i></div>
                        <div class="pf-card current">
                            <div class="num">6.2</div>
                            <h4><i class="bi bi-check2-square me-1" style="color:var(--gold-600);"></i>Validate Data</h4>
                            <p>Check &amp; validate every uploaded document.</p>
                            <span class="chip chip-warn mt-2"><i class="bi bi-hourglass-split"></i> In progress</span>
                        </div>
                        <div class="pf-arrow"><i class="bi bi-chevron-right"></i></div>
                        <div class="pf-card">
                            <div class="num">6.3</div>
                            <h4><i class="bi bi-patch-check me-1" style="color:var(--ink-300);"></i>Approve Data</h4>
                            <p>Approve and verify the validated file.</p>
                            <span class="chip chip-navy mt-2">Waiting</span>
                        </div>
                        <div class="pf-arrow"><i class="bi bi-chevron-right"></i></div>
                        <div class="pf-card">
                            <div class="num">6.4</div>
                            <h4><i class="bi bi-bar-chart me-1" style="color:var(--ink-300);"></i>Generate Reports</h4>
                            <p>Generate, view and download reports.</p>
                            <span class="chip chip-navy mt-2">Waiting</span>
                        </div>
                        <div class="pf-arrow"><i class="bi bi-chevron-right"></i></div>
                        <div class="pf-card">
                            <div class="num">6.5</div>
                            <h4><i class="bi bi-cloud-check me-1" style="color:var(--ink-300);"></i>Archive &amp; Backup
                            </h4>
                            <p>Archive data and take a backup copy.</p>
                            <span class="chip chip-navy mt-2">Waiting</span>
                        </div>
                        <div class="pf-arrow"><i class="bi bi-chevron-right"></i></div>
                        <div class="pf-card">
                            <div class="num">6.6</div>
                            <h4><i class="bi bi-box-arrow-right me-1" style="color:var(--ink-300);"></i>Logout</h4>
                            <p>Sign out from the system.</p>
                            <span class="chip chip-navy mt-2">Waiting</span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Validation queue -->
                    <div class="col-lg-7">
                        <div class="surface p-3 p-lg-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="h6 fw-bold mb-0">Validation checklist</h2>
                                <span class="chip chip-warn">2 flagged</span>
                            </div>
                            <div class="doc-row">
                                <div class="dic" style="background:var(--ok-bg);color:var(--ok);"><i
                                        class="fa fa-check"></i></div>
                                <div class="flex-grow-1">
                                    <div class="name">Field Log Data</div>
                                    <div class="meta">All 4 files validated</div>
                                </div><span class="chip chip-ok">Passed</span>
                            </div>
                            <div class="doc-row">
                                <div class="dic" style="background:var(--danger-bg);color:var(--danger);"><i
                                        class="fa fa-times"></i></div>
                                <div class="flex-grow-1">
                                    <div class="name">Permit Letter</div>
                                    <div class="meta">Scan unreadable — needs re-upload</div>
                                </div><span class="chip chip-danger">Rejected</span>
                            </div>
                            <div class="doc-row">
                                <div class="dic" style="background:var(--danger-bg);color:var(--danger);"><i
                                        class="fa fa-exclamation-triangle"></i></div>
                                <div class="flex-grow-1">
                                    <div class="name">Reserves Table</div>
                                    <div class="meta">Figures don't match the Plan sheet</div>
                                </div><span class="chip chip-danger">Rejected</span>
                            </div>
                            <div class="doc-row">
                                <div class="dic" style="background:var(--ok-bg);color:var(--ok);"><i
                                        class="fa fa-check-circle"></i></div>
                                <div class="flex-grow-1">
                                    <div class="name">Report</div>
                                    <div class="meta">Covering letter validated</div>
                                </div><span class="chip chip-ok">Passed</span>
                            </div>

                            <!-- NO -> Review & correct loop -->
                            <div class="mt-4 p-3 rounded-3 d-flex align-items-start gap-3"
                                style="background:var(--danger-bg); border:1px solid #f0c9c9;">
                                <i class="bi bi-arrow-repeat fs-5" style="color:var(--danger);"></i>
                                <div>
                                    <div class="fw-semibold small" style="color:var(--danger);">No — send back for
                                        correction</div>
                                    <div class="small" style="color:#7a2f2f;">2 documents failed validation. They've been
                                        routed back to the applicant with review notes; the stage will re-run once corrected
                                        files are re-uploaded.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button class="btn btn-outline-navy"><i
                                        class="bi bi-arrow-counterclockwise me-1"></i>Request re-upload</button>
                                <button class="btn btn-info" disabled><i class="bi bi-check2 me-1"></i>Approve data
                                    (Yes)</button>
                            </div>
                        </div>
                    </div>

                    <!-- Activity / audit log -->
                    <div class="col-lg-5">
                        <div class="surface p-3 p-lg-4 h-100">
                            <h2 class="h6 fw-bold mb-3"><i class="bi bi-clock-history me-1"></i>Activity &amp; audit trail
                            </h2>
                            <ul class="list-unstyled m-0 d-flex flex-column gap-3">
                                <li class="d-flex gap-2">
                                    <div class="text-mono small text-muted" style="width:52px;flex-shrink:0;">10:42</div>
                                    <div class="small"><b>System</b> rejected <i>Permit Letter</i> — unreadable scan.
                                    </div>
                                </li>
                                <li class="d-flex gap-2">
                                    <div class="text-mono small text-muted" style="width:52px;flex-shrink:0;">10:31</div>
                                    <div class="small"><b>R. Kannan</b> validated Field Log Data (4 files).</div>
                                </li>
                                <li class="d-flex gap-2">
                                    <div class="text-mono small text-muted" style="width:52px;flex-shrink:0;">09:58</div>
                                    <div class="small"><b>R. Kannan</b> validated Report → Covering Letter.</div>
                                </li>
                                <li class="d-flex gap-2">
                                    <div class="text-mono small text-muted" style="width:52px;flex-shrink:0;">09:20</div>
                                    <div class="small"><b>Applicant</b> uploaded 15 files to the Documents folder.</div>
                                </li>
                                <li class="d-flex gap-2">
                                    <div class="text-mono small text-muted" style="width:52px;flex-shrink:0;">Yesterday
                                    </div>
                                    <div class="small"><b>System</b> created application MDG-2026-0472.</div>
                                </li>
                            </ul>
                            <hr class="my-3">
                            <div class="d-flex align-items-center gap-2 small text-muted"><i
                                    class="bi bi-shield-check"></i> Every action on this file is logged and attributed to a
                                user for audit purposes.</div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>


@endsection
