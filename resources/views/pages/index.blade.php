@extends('layouts.app')
@section('title', 'Dashboard')
@section('main_content')

<style>
    .stat-card-link {
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .stat-card-link .stat-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .stat-card-link:hover .stat-card {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: #cbd5e1;
    }
    .quick-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.45rem 0.9rem;
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 30px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ink-700);
        text-decoration: none !important;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .quick-action-pill:hover {
        background: #ffffff;
        border-color: var(--gold-500);
        color: var(--navy-950);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(11, 43, 94, 0.08);
    }
    .quick-action-pill i {
        font-size: 0.9rem;
    }
    .activity-feed {
        position: relative;
        padding-left: 20px;
    }
    .activity-feed::before {
        content: '';
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 6px;
        width: 2px;
        background: #e2e8f0;
    }
    .activity-item {
        position: relative;
        padding-bottom: 1.15rem;
    }
    .activity-item:last-child {
        padding-bottom: 0;
    }
    .activity-dot {
        position: absolute;
        left: -20px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid var(--navy-800);
        box-shadow: 0 0 0 2px #e2e8f0;
    }
    .activity-dot.dot-ok {
        border-color: var(--ok);
    }
    .activity-dot.dot-danger {
        border-color: var(--danger);
    }
    .activity-dot.dot-warn {
        border-color: var(--warn);
    }
    .survey-badge {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }
    .survey-badge-dgps {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .survey-badge-drone {
        background: #ede9fe;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }
</style>

<div class="content-body default-height">
    <div class="container-fluid">
        <main class="page">
            
            <!-- Dynamic Welcome & Action Header -->
            <div class="page-head mb-3">
                <div>
                    <span class="eyebrow"><i class="bi bi-geo-alt"></i> {{ $regionName }}</span>
                    <h1>{{ $greeting }}, {{ Auth::user()->name ?? 'Officer' }}.</h1>
                    <p>
                        @if($pendingCount > 0)
                            {{ $pendingCount }} {{ \Illuminate\Support\Str::plural('application', $pendingCount) }} awaiting action across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @else
                            All applications and technical surveys are currently active across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @endif
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @can('application.create')
                    <a href="{{ route('step1') }}" class="btn btn-gold px-3 shadow-sm d-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg me-1"></i>
                        <span>New Application</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Quick Action Workflow Ribbon -->
            <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-4 text-nowrap">
                <span class="small fw-semibold text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;"><i class="bi bi-lightning-charge me-1"></i> Quick Launch:</span>
                @can('application.create')
                <a href="{{ route('step1') }}" class="quick-action-pill">
                    <i class="bi bi-file-earmark-plus text-primary"></i> Lease App
                </a>
                @endcan
                @can('dgps.create')
                <a href="{{ route('dgps-survey.step', ['step' => 1]) }}" class="quick-action-pill">
                    <i class="bi bi-geo-fill text-info"></i> DGPS Survey
                </a>
                @endcan
                @can('drone.create')
                <a href="{{ route('drone-survey.step', ['step' => 1]) }}" class="quick-action-pill">
                    <i class="bi bi-airplane-fill" style="color:#7c3aed;"></i> Drone Survey
                </a>
                @endcan
                @can('environment.b2.create')
                <a href="{{ route('eviron.create') }}" class="quick-action-pill">
                    <i class="bi bi-tree-fill text-success"></i> Env Clearance
                </a>
                @endcan
                @can('ec_compliance.create')
                <a href="{{ route('ec-compliance.step', ['step' => 1]) }}" class="quick-action-pill">
                    <i class="bi bi-clipboard2-check text-warning"></i> EC Compliance
                </a>
                @endcan
            </div>

            <!-- Primary KPI Stat Cards (Lease & Mining Applications) -->
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Active Applications">
                        <div class="surface stat-card">
                            <div class="ic" style="background:var(--navy-100); color:var(--navy-800);"><i class="fa fa-folder"></i></div>
                            <div>
                                <div class="val">{{ $activeCount }}</div>
                                <div class="lbl">Active Applications</div>
                                <div class="delta text-success">Updated just now</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Pending Applications">
                        <div class="surface stat-card">
                            <div class="ic" style="background:var(--warn-bg); color:var(--warn);"><i class="fa fa-hourglass-half"></i></div>
                            <div>
                                <div class="val">{{ $pendingCount }}</div>
                                <div class="lbl">Pending Validation</div>
                                <div class="delta text-warning-emphasis">Awaiting review</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Approved Applications">
                        <div class="surface stat-card">
                            <div class="ic" style="background:var(--ok-bg); color:var(--ok);"><i class="fa fa-check-circle"></i></div>
                            <div>
                                <div class="val">{{ $approvedCount }}</div>
                                <div class="lbl">Approved &amp; Verified</div>
                                <div class="delta text-success">All time record</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Archived Applications">
                        <div class="surface stat-card">
                            <div class="ic" style="background:var(--f-others-bg); color:var(--f-others);"><i class="fa fa-cloud-upload"></i></div>
                            <div>
                                <div class="val">{{ $archivedCount }}</div>
                                <div class="lbl">Archived &amp; Backed Up</div>
                                <div class="delta" style="color:var(--ink-500);">Safely secured</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Technical Operations & Surveys Execution Matrix (New Rich Operations Strip) -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('dgps-survey.index') }}" class="stat-card-link" title="View DGPS Surveys">
                        <div class="surface stat-card" style="border-left: 3px solid #0284c7;">
                            <div class="ic" style="background:#e0f2fe; color:#0284c7;"><i class="bi bi-pin-map-fill"></i></div>
                            <div>
                                <div class="val">{{ $dgpsTotal }}</div>
                                <div class="lbl">DGPS Demarcation</div>
                                <div class="delta text-success"><i class="bi bi-check2-circle"></i> {{ $dgpsCompleted }} Completed</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('drone-survey.index') }}" class="stat-card-link" title="View Drone Surveys">
                        <div class="surface stat-card" style="border-left: 3px solid #7c3aed;">
                            <div class="ic" style="background:#f3e8ff; color:#7c3aed;"><i class="bi bi-airplane-fill"></i></div>
                            <div>
                                <div class="val">{{ $droneTotal }}</div>
                                <div class="lbl">Drone Aerial Topo</div>
                                <div class="delta" style="color:#7c3aed;"><i class="bi bi-folder-check"></i> {{ $droneReady }} Reports Ready</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('eviron.index') }}" class="stat-card-link" title="View Environmental Projects">
                        <div class="surface stat-card" style="border-left: 3px solid #16a34a;">
                            <div class="ic" style="background:#dcfce7; color:#16a34a;"><i class="bi bi-tree-fill"></i></div>
                            <div>
                                <div class="val">{{ $envProjectsTotal }}</div>
                                <div class="lbl">Env Clearances</div>
                                <div class="delta text-success"><i class="bi bi-shield-check"></i> B1 / B2 Active Projects</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('ec-compliance.index') }}" class="stat-card-link" title="View EC Compliances">
                        <div class="surface stat-card" style="border-left: 3px solid var(--gold-500);">
                            <div class="ic" style="background:var(--gold-100); color:var(--gold-600);"><i class="bi bi-clipboard-data-fill"></i></div>
                            <div>
                                <div class="val">{{ $ecComplianceTotal }}</div>
                                <div class="lbl">EC Compliance</div>
                                <div class="delta" style="color:var(--gold-600);"><i class="bi bi-clock-history"></i> Half-Yearly Cycles</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Row 1: Recent Applications & Portfolio Breakdown Chart -->
            <div class="row g-3 mb-4">
                <!-- Recent applications -->
                <div class="col-lg-8">
                    <div class="surface p-3 p-lg-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">Recent Applications</h2>
                                <span class="small text-muted">Mining lease and quarry permit pipeline</span>
                            </div>
                            <a href="{{ route('application.index') }}" class="small text-decoration-none fw-semibold">View all ({{ $approvedCount + $activeCount }}) →</a>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-clean align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>District</th>
                                        <th>Mineral</th>
                                        <th>Plan Type</th>
                                        <th>Stage</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplications as $app)
                                    <tr>
                                        <td><span class="fw-semibold">{{ $app->client }}</span></td>
                                        <td>{{ $app->district }}</td>
                                        <td>{{ $app->mineral }}</td>
                                        <td>{{ $app->plan_type }}</td>
                                        <td><span class="chip chip-{{ $app->status_class }}"><i class="bi bi-{{ $app->icon }}"></i> {{ $app->stage }}</span></td>
                                        <td class="text-end">
                                            @if(isset($app->id))
                                                <a href="{{ route('viewapplication', ['id' => $app->id]) }}" class="btn btn-sm btn-outline-navy">Open</a>
                                            @else
                                                <a href="{{ route('application.index') }}" class="btn btn-sm btn-outline-navy">Open</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No recent applications found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Applications Breakdown Chart -->
                <div class="col-lg-4">
                    <div class="surface p-3 p-lg-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">Operations Breakdown</h2>
                                <span class="small text-muted">Statutory project distribution</span>
                            </div>
                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">{{ array_sum($chartData['data']) }} Total</span>
                        </div>
                        <div class="flex-grow-1" style="position: relative; min-height: 250px;">
                            <canvas id="appBreakdownChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Recent Field Surveys & Live Activity Stream -->
            <div class="row g-3 mb-4">
                <!-- Recent Field Surveys (DGPS & Drone) -->
                <div class="col-lg-7">
                    <div class="surface p-3 p-lg-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">Recent Field &amp; Geospatial Surveys</h2>
                                <span class="small text-muted">Latest DGPS demarcations &amp; aerial photogrammetry</span>
                            </div>
                            <div class="dropdown">
                                <a href="{{ route('dgps-survey.index') }}" class="small text-decoration-none fw-semibold">All Surveys →</a>
                            </div>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="table table-clean align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Survey Ref</th>
                                        <th>Client / Location</th>
                                        <th>Service Type</th>
                                        <th>Status</th>
                                        <th class="text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSurveys as $survey)
                                    <tr>
                                        <td>
                                            <span class="fw-bold font-monospace text-dark" style="font-size: 0.8rem;">{{ $survey->survey_no }}</span>
                                            <div class="text-muted" style="font-size: 0.7rem;">{{ $survey->date }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-truncate" style="max-width: 160px;">{{ $survey->client }}</div>
                                            <div class="text-muted small text-truncate" style="max-width: 160px;"><i class="bi bi-geo-alt"></i> {{ $survey->location }}</div>
                                        </td>
                                        <td>
                                            @if($survey->type === 'DGPS Demarcation')
                                                <span class="survey-badge survey-badge-dgps"><i class="bi bi-pin-map me-1"></i> DGPS</span>
                                            @else
                                                <span class="survey-badge survey-badge-drone"><i class="bi bi-airplane me-1"></i> Drone</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="chip chip-{{ $survey->status_class }}">
                                                <i class="bi {{ $survey->status_class === 'ok' ? 'bi-check2' : 'bi-clock' }}"></i>
                                                {{ $survey->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $survey->url }}" class="btn btn-sm btn-outline-navy">Inspect</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No field survey records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Live System Audit & Activity Log -->
                <div class="col-lg-5">
                    <div class="surface p-3 p-lg-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">System Audit &amp; Activity Trail</h2>
                                <span class="small text-muted">Real-time actions &amp; officer verification</span>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.72rem;">Live Log</span>
                        </div>
                        <div class="activity-feed flex-grow-1 overflow-y-auto pe-1" style="max-height: 330px;">
                            @forelse($recentActivities as $act)
                            <div class="activity-item">
                                <span class="activity-dot {{ str_contains($act->badge_class, 'ok') ? 'dot-ok' : (str_contains($act->badge_class, 'danger') ? 'dot-danger' : 'dot-warn') }}"></span>
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <span class="chip {{ $act->badge_class }} px-2 py-0" style="font-size: 0.68rem;">{{ $act->action }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">{{ $act->time_ago }}</span>
                                </div>
                                <div class="small fw-semibold text-dark mb-1" style="font-size: 0.82rem; line-height: 1.3;">
                                    {{ $act->description }}
                                </div>
                                <div class="text-muted d-flex align-items-center gap-1" style="font-size: 0.72rem;">
                                    <i class="bi bi-person-circle"></i> {{ $act->user }}
                                </div>
                            </div>
                            @empty
                            <div class="text-center text-muted py-4">No recent activity logged.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Financial Revenue Health & District Geospatial Snapshot -->
            <div class="row g-3 mb-2">
                <!-- Financial Collection Health -->
                <div class="col-12 col-lg-7">
                    <div class="surface p-3 p-lg-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">Financial Revenue &amp; Recovery Health</h2>
                                <span class="small text-muted">Statutory fee collection &amp; outstanding balances</span>
                            </div>
                            <span class="chip chip-ok"><i class="bi bi-shield-check"></i> {{ $recoveryRate }}% Collection Rate</span>
                        </div>

                        <!-- Progress Recovery Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>Collected ({{ $recoveryRate }}%)</span>
                                <span>Pending ({{ 100 - $recoveryRate }}%)</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px; background-color: var(--warn-bg);">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $recoveryRate }}%" aria-valuenow="{{ $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $recoveryRate }}%" aria-valuenow="{{ 100 - $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="p-3 rounded-3" style="background:var(--paper); border: 1px solid var(--ink-100);">
                                    <div class="small text-muted mb-1"><i class="bi bi-receipt text-primary me-1"></i> Total Invoiced</div>
                                    <div class="fw-bold fs-5" style="font-family:'Sora';">₹{{ number_format($totalBilled, 2) }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Combined ledger</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 rounded-3" style="background:var(--ok-bg); border: 1px solid #bbf7d0;">
                                    <div class="small text-success mb-1"><i class="bi bi-cash-stack me-1"></i> Paid Realized</div>
                                    <div class="fw-bold fs-5 text-success" style="font-family:'Sora';">₹{{ number_format($totalPaid, 2) }}</div>
                                    <div class="text-success" style="font-size: 0.7rem;">Verified payments</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 rounded-3" style="background:var(--warn-bg); border: 1px solid #fde68a;">
                                    <div class="small text-warning-emphasis mb-1"><i class="bi bi-hourglass-split me-1"></i> Pending Dues</div>
                                    <div class="fw-bold fs-5 text-warning-emphasis" style="font-family:'Sora';">₹{{ number_format($totalPending, 2) }}</div>
                                    <div class="text-warning-emphasis" style="font-size: 0.7rem;">Awaiting collection</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- District Footprint Snapshot -->
                <div class="col-12 col-lg-5">
                    <div class="surface p-3 p-lg-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h2 class="h6 fw-bold mb-0">Jurisdiction District Footprint</h2>
                                <span class="small text-muted">Field operations &amp; site distribution</span>
                            </div>
                            <span class="small text-muted">{{ max(1, $districtStats->count()) }} Active Hubs</span>
                        </div>
                        <div class="row g-3 text-center">
                            @forelse($districtStats as $stat)
                            <div class="col-6">
                                <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-center" style="background:var(--paper); border: 1px solid var(--ink-100);">
                                    <div class="fw-bold fs-4 text-navy" style="font-family:'Sora';">{{ $stat->total }}</div>
                                    <div class="small fw-semibold text-dark">{{ $stat->name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Mining &amp; Leases</div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><div class="p-3 rounded-3 text-muted text-center">No district data available</div></div>
                            @endforelse
                            <div class="col-6">
                                <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-center" style="background:var(--paper); border: 1px solid var(--ink-100);">
                                    <div class="fw-bold fs-4 text-primary" style="font-family:'Sora';">108</div>
                                    <div class="small fw-semibold text-dark">Salem Sector 4</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">DGPS Boundary Hub</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 h-100 d-flex flex-column justify-content-center" style="background:var(--paper); border: 1px solid var(--ink-100);">
                                    <div class="fw-bold fs-4" style="font-family:'Sora'; color:#7c3aed;">116</div>
                                    <div class="small fw-semibold text-dark">Quarry Sector 4</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Drone Flight Grid</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var canvasEl = document.getElementById('appBreakdownChart');
    if (!canvasEl) return;
    
    var ctx = canvasEl.getContext('2d');
    var chartData = {!! json_encode($chartData) !!};
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    '#0284C7', // Sky Blue for DGPS
                    '#7C3AED', // Purple for Drone
                    '#16A34A', // Green for Environment
                    '#0B2B5E', // Navy for Lease
                    '#D4AF37'  // Gold for Mining
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 10,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' records';
                        }
                    }
                }
            },
            cutout: '68%'
        }
    });
});
</script>

@endsection
