@extends('layouts.app')
@section('title', 'Mining Portal')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <main class="page" style="max-width:960px;">
                <div class="page-head">
                    <div>
                        <span class="eyebrow"><i class="bi bi-signpost-split"></i> Intake</span>
                        <h1>Register a new mining application</h1>
                        <p>Follow the six stages below — each one narrows the file structure that gets created for this
                            client.</p>
                    </div>
                </div>

                <!-- Stepper -->
                <div class="surface p-3 p-lg-4 mb-3">
                    <div class="flow-stepper">
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">1</div>
                                <div class="lbl">Client Name</div>
                            </div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">2</div>
                                <div class="lbl">District Wise</div>
                            </div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">3</div>
                                <div class="lbl">Minerals</div>
                            </div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">4</div>
                                <div class="lbl">Plans</div>
                            </div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">5</div>
                                <div class="lbl">Folders</div>
                            </div>
                        </div>
                        <div class="flow-connector"></div>
                        <div class="flow-step">
                            <div class="node">
                                <div class="circle">6</div>
                                <div class="lbl">Preview</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="surface p-4 p-lg-5">

                    <!-- STEP 1: CLIENT NAME -->
                    <div class="wizard-pane">
                        <span class="small-caps-label">Step 1 · Client</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">Who is this application for?</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Client / Firm name</label>
                                <input type="text" class="form-control" placeholder="e.g. Sri Bala Traders"
                                    value="Sri Bala Traders">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Applicant type</label>
                                <select class="form-select">
                                    <option>Individual</option>
                                    <option selected>Partnership firm</option>
                                    <option>Private limited company</option>
                                    <option>Trust / Society</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Mobile number</label>
                                <input type="text" class="form-control" placeholder="10-digit mobile number">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email address</label>
                                <input type="email" class="form-control" placeholder="name@company.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Registered address</label>
                                <textarea class="form-control" rows="2" placeholder="Full postal address"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: DISTRICT WISE -->
                    <div class="wizard-pane d-none">
                        <span class="small-caps-label">Step 2 · Location</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">Which district does the site fall under?</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">District</label>
                                <select class="form-select">
                                    <option>Coimbatore</option>
                                    <option selected>Salem</option>
                                    <option>Namakkal</option>
                                    <option>Erode</option>
                                    <option>Tiruppur</option>
                                    <option>Dindigul</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Taluk</label>
                                <input type="text" class="form-control" placeholder="e.g. Mettur">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Village</label>
                                <input type="text" class="form-control" placeholder="Village name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Survey number(s)</label>
                                <input type="text" class="form-control" placeholder="e.g. 112/3A, 112/3B">
                            </div>
                        </div>
                        <div class="mt-3 p-3 rounded-3 d-flex align-items-center gap-2" style="background:var(--navy-100);">
                            <i class="bi bi-info-circle text-navy" style="color:var(--navy-800)"></i>
                            <span class="small" style="color:var(--navy-800);">This district assignment determines the
                                routing officer and the regional storage bucket for this file.</span>
                        </div>
                    </div>

                    <!-- STEP 3: MINERALS -->
                    <div class="wizard-pane d-none">
                        <span class="small-caps-label">Step 3 · Mineral</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">Select the mineral this application covers</h2>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m1"><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m1"><i
                                        class="bi bi-gem me-2"></i>Rough Stone</label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m2" checked><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m2"><i
                                        class="bi bi-gem me-2"></i>Gravel</label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m3"><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m3"><i
                                        class="bi bi-gem me-2"></i>Granite</label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m4"><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m4"><i
                                        class="bi bi-gem me-2"></i>Lime Stone</label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m5"><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m5"><i
                                        class="bi bi-gem me-2"></i>Fire Clay</label>
                            </div>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="mineral" id="m6"><label
                                    class="btn btn-outline-navy w-100 text-start py-2" for="m6"><i
                                        class="bi bi-three-dots me-2"></i>Others</label>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: PLANS -->
                    <div class="wizard-pane d-none">
                        <span class="small-caps-label">Step 4 · Plan type</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">Which plan is being submitted?</h2>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="cursor:pointer;">
                                <input type="radio" name="plan" checked>
                                <div>
                                    <div class="fw-semibold small">Mining Plan</div>
                                    <div class="text-muted" style="font-size:11.5px;">First-time plan for a new lease area
                                    </div>
                                </div>
                            </label>
                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="cursor:pointer;">
                                <input type="radio" name="plan">
                                <div>
                                    <div class="fw-semibold small">Revised Mining Plan</div>
                                    <div class="text-muted" style="font-size:11.5px;">Amendment to an existing approved
                                        plan</div>
                                </div>
                            </label>
                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="cursor:pointer;">
                                <input type="radio" name="plan">
                                <div>
                                    <div class="fw-semibold small">Modified Mining Plan</div>
                                    <div class="text-muted" style="font-size:11.5px;">Change of scope within the same
                                        lease</div>
                                </div>
                            </label>
                            <label class="d-flex align-items-center gap-3 p-3 rounded-3 border" style="cursor:pointer;">
                                <input type="radio" name="plan">
                                <div>
                                    <div class="fw-semibold small">Scheme of Mining Plan</div>
                                    <div class="text-muted" style="font-size:11.5px;">Combined scheme for group / cluster
                                        leases</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- STEP 5: FOLDERS -->
                    <div class="wizard-pane d-none">
                        <span class="small-caps-label">Step 5 · Folders</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">This is the folder set we'll create</h2>
                        <p class="text-muted small mb-3">Based on the answers above, six folders will be created for <b>Sri
                                Bala Traders</b>. You'll upload files against this checklist on the next screen.</p>
                        <div class="row g-3">
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-log);">
                                    <div class="fic" style="background:var(--f-log-bg); color:var(--f-log);"><i
                                            class="fa fa-clone"></i></div>
                                    <h3>Field Log Data</h3>
                                    <div class="count">4 document types</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-docs);">
                                    <div class="fic" style="background:var(--f-docs-bg); color:var(--f-docs);"><i
                                            class="fa fa-folder-open"></i></div>
                                    <h3>Documents</h3>
                                    <div class="count">22 document types</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-photos);">
                                    <div class="fic" style="background:var(--f-photos-bg); color:var(--f-photos);"><i
                                            class="fa fa-file-image"></i></div>
                                    <h3>Site Photos</h3>
                                    <div class="count">Photographs</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-report);">
                                    <div class="fic" style="background:var(--f-report-bg); color:var(--f-report);"><i
                                            class="fa fa-pencil-square"></i></div>
                                    <h3>Report</h3>
                                    <div class="count">3 document types</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-plan);">
                                    <div class="fic" style="background:var(--f-plan-bg); color:var(--f-plan);"><i
                                            class="fa fa-hourglass"></i></div>
                                    <h3>Plan</h3>
                                    <div class="count">5 document types</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="folder-card" style="--c:var(--f-others);">
                                    <div class="fic" style="background:var(--f-others-bg); color:var(--f-others);"><i
                                            class="fa fa-pie-chart"></i></div>
                                    <h3>Others</h3>
                                    <div class="count">DGPS &amp; drone data</div>
                                </div>
                            </div>
                        </div>
                    </div>

                                        <!-- STEP 6: PREVIEW -->
                    <div class="wizard-pane d-none">
                        <span class="small-caps-label">Step 6 � Preview</span>
                        <h2 class="h5 fw-bold mt-1 mb-3">Application Summary</h2>
                        <div class="card-panel mt-4 mb-4" style="background:var(--navy-soft); border:none; padding:15px; border-radius:8px;">
                            <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="bi bi-person-lines-fill me-2"></i>Summary Details</h6>
                            <div class="row g-2" style="font-size: 0.85rem;">
                                <div class="col-md-4"><span class="text-muted">Client Name:</span> <br><b>Sri Bala Traders</b></div>
                                <div class="col-md-4"><span class="text-muted">District:</span> <br><b>Coimbatore</b></div>
                                <div class="col-md-4"><span class="text-muted">Mineral:</span> <br><b>Rough Stone</b></div>
                                <div class="col-md-4 mt-2"><span class="text-muted">Plan Type:</span> <br><b>Mining Plan</b></div>
                                <div class="col-md-4 mt-2"><span class="text-muted">Folders Created:</span> <br><span class="badge bg-success">6 Folders</span></div>
                            </div>
                        </div>
                        <div class="card-panel mb-0" style="background:var(--green-soft);border:none; padding:12px; border-radius:6px; color:#0f4c27;"><i class="bi bi-check-circle"></i> <span style="font-size:.78rem">Please review the details above. Clicking Continue will finalize the application creation.</span></div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button class="btn btn-outline-navy btn-prev"><i class="bi bi-arrow-left me-1"></i>Back</button>
                        @can('mining.create')
                        <div class="d-flex gap-2">
                            <button class="btn btn-light border">Save as draft</button>
                            <button class="btn btn-navy btn-next">Continue<i class="bi bi-arrow-right ms-1"></i></button>
                        </div>
                        @endcan
                    </div>
                </div>
            </main>


        </div>
    </div>




@endsection
