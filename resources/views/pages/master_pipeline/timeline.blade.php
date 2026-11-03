@extends('layouts.master')

@section('title', '360° Master Project Timeline • ' . $master->master_application_id . ' • GTMS')

@section('main_content')
<style>
    /* Scoped GTMS Master Pipeline Timeline Executive Design System */
    .timeline-banner {
        border-radius: 18px;
        background: linear-gradient(135deg, #0A1435 0%, #0F1E4D 60%, #172B68 100%);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 10px 30px -5px rgba(10, 20, 53, 0.3);
        position: relative;
        overflow: hidden;
    }
    .timeline-banner::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, rgba(37, 99, 235, 0) 70%);
        pointer-events: none;
    }

    /* Action Controls Bar */
    .control-hub-card {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px -2px rgba(15, 30, 77, 0.05);
    }

    /* 5-Stage Stepper Track */
    .pipeline-stepper-container {
        position: relative;
    }
    .pipeline-step-card {
        border-radius: 14px;
        padding: 18px 14px;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        text-align: center;
        height: 100%;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .pipeline-step-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -3px rgba(15, 30, 77, 0.08);
    }

    /* Stepper States */
    .step-completed {
        border-color: #10B981;
        background: #f0fdf4;
    }
    .step-active {
        border-color: #2563EB;
        background: #eff6ff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
    }
    .step-returned {
        border-color: #DC2626;
        background: #fef2f2;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.16);
        animation: pulse-border-red 2s infinite;
    }
    @keyframes pulse-border-red {
        0%, 100% { box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.18); }
        50% { box-shadow: 0 0 0 6px rgba(220, 38, 38, 0.06); }
    }
    .step-upcoming {
        border-color: #e2e8f0;
        background: #f8fafc;
        opacity: 0.95;
    }

    .step-icon-bubble {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin: 0 auto 12px;
        color: #ffffff;
        transition: transform 0.2s ease;
    }
    .step-completed .step-icon-bubble { background: linear-gradient(135deg, #059669 0%, #10B981 100%); }
    .step-active .step-icon-bubble { background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%); }
    .step-returned .step-icon-bubble { background: linear-gradient(135deg, #991b1b 0%, #DC2626 100%); }
    .step-upcoming .step-icon-bubble { background: #94a3b8; color: #ffffff; }

    /* Department Sub-Record Vault Cards */
    .vault-card {
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 16px;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .vault-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -3px rgba(15, 30, 77, 0.07);
        border-color: #cbd5e1;
    }
    .vault-card-la { border-left: 4px solid #1e40af !important; }
    .vault-card-survey { border-left: 4px solid #047857 !important; }
    .vault-card-mining { border-left: 4px solid #b45309 !important; }
    .vault-card-env { border-left: 4px solid #0f766e !important; }
    .vault-card-ppt { border-left: 4px solid #6d28d9 !important; }

    .vault-dept-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .vault-dept-la { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
    .vault-dept-survey { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .vault-dept-mining { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .vault-dept-env { background: #f0fdfa; color: #0f766e; border: 1px solid #99f6e4; }
    .vault-dept-ppt { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }

    /* Custom Non-Clashing Buttons */
    .btn-slate {
        background-color: #f8fafc;
        color: #334155 !important;
        border: 1px solid #cbd5e1;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .btn-slate:hover {
        background-color: #f1f5f9;
        color: #0f172a !important;
        border-color: #94a3b8;
    }
    .btn-navy-primary {
        background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);
        color: #ffffff !important;
        border: 1px solid #0F1E4D;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 6px rgba(15, 30, 77, 0.15);
    }
    .btn-navy-primary:hover {
        background: linear-gradient(135deg, #172B68 0%, #2563EB 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
        transform: translateY(-1px);
    }

    /* Slate Badges for Upcoming */
    .badge-slate {
        background-color: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        font-weight: 600;
    }

    /* Master ID Pill */
    .master-id-banner {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: -0.01em;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 8px;
        padding: 4px 12px;
        display: inline-block;
    }

    /* Audit Trail Table */
    .audit-table th {
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #475569;
        font-weight: 700;
        white-space: nowrap;
    }
    .audit-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .audit-row-return td {
        background-color: #fff5f5 !important;
    }
</style>

<div class="content-body">
    <div class="container-fluid">

        {{-- Flash Notification Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #bbf7d0; background-color: #f0fdf4; color: #166534;">
                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                <div class="flex-grow-1">
                    <strong>Success!</strong> {{ session('success') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('alert') || session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #fed7aa; background-color: #fffbeb; color: #9a3412;">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-warning"></i>
                <div class="flex-grow-1">
                    <strong>Attention Required:</strong> {{ session('alert') ?? session('warning') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 12px; border: 1px solid #fecaca; background-color: #fef2f2; color: #991b1b;">
                <i class="bi bi-x-octagon-fill fs-4 me-3 text-danger"></i>
                <div class="flex-grow-1">
                    <strong>Action Failed:</strong> {{ session('error') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Navigation Breadcrumb Bar --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3.5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 py-1" style="font-size: 0.84rem;">
                    <li class="breadcrumb-item">
                        <a href="{{ route('master-pipeline.index') }}" class="text-primary text-decoration-none fw-semibold">
                            <i class="bi bi-diagram-3 me-1"></i>Master Pipeline
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $master->master_application_id }}</li>
                </ol>
            </nav>
            <div class="d-flex gap-2">
                <a href="{{ route('master-pipeline.index') }}" class="btn btn-slate btn-sm d-inline-flex align-items-center gap-1.5 px-3 py-1.5">
                    <i class="bi bi-arrow-left"></i> Back to Pipeline Directory
                </a>
            </div>
        </div>

        {{-- Top Project Summary Banner --}}
        <div class="timeline-banner p-4 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2.5">
                        <span class="badge bg-white text-dark fw-bold px-3 py-1" style="font-size: 0.78rem; border-radius: 6px;">
                            <i class="bi bi-award-fill text-warning me-1"></i> UNIVERSAL MASTER PROJECT
                        </span>
                        <span class="badge px-2.5 py-1 text-white border border-light-subtle" style="background: rgba(255,255,255,0.15); font-size: 0.78rem; border-radius: 6px;">
                            <i class="bi bi-geo-alt me-1"></i> Branch: {{ $master->branch?->branch_name ?? 'Statewide / HQ' }}
                        </span>
                        @php $statusBadge = $master->status_badge; @endphp
                        <span class="badge {{ $statusBadge['class'] }} px-3 py-1 fw-bold" style="font-size: 0.78rem; border-radius: 6px;">
                            <i class="bi {{ $statusBadge['icon'] }} me-1"></i> {{ $statusBadge['label'] }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <h2 class="h3 fw-bold mb-0 text-white d-inline-block master-id-banner">
                            {{ $master->master_application_id }}
                        </h2>
                    </div>
                    <div class="d-flex flex-wrap gap-3 text-white-50 mt-2" style="font-size: 0.85rem;">
                        <div><i class="bi bi-person-fill text-white me-1"></i><strong class="text-white">{{ $master->customer?->customer_name ?? 'N/A' }}</strong></div>
                        @if($master->customer?->company_name)
                            <div><i class="bi bi-building text-white me-1"></i>{{ $master->customer->company_name }}</div>
                        @endif
                        @if($master->customer?->mobile_num)
                            <div><i class="bi bi-telephone-fill text-white me-1"></i>{{ $master->customer->mobile_num }}</div>
                        @endif
                        <div><i class="bi bi-calendar3 text-white me-1"></i>Initiated: {{ $master->created_at ? $master->created_at->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-3.5 rounded-3" style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.18);">
                        <div class="row g-2.5 text-white" style="font-size: 0.82rem;">
                            <div class="col-6">
                                <span class="text-white-50 d-block small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Concession Location</span>
                                <strong class="text-white">{{ $master->village ?? '—' }}, {{ $master->taluk ?? '—' }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-white-50 d-block small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">District</span>
                                <strong class="text-white">{{ $master->district?->name ?? '—' }}</strong>
                            </div>
                            <div class="col-6 mt-2">
                                <span class="text-white-50 d-block small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Quarry Extent & Mineral</span>
                                <strong class="text-white">{{ $master->area_extent_ha ? $master->area_extent_ha . ' Ha' : 'N/A' }} • {{ $master->mineral_name ?? 'Granite' }}</strong>
                            </div>
                            <div class="col-6 mt-2">
                                <span class="text-white-50 d-block small" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Survey Numbers</span>
                                <strong class="text-white text-truncate d-block" title="{{ $master->survey_numbers }}">{{ $master->survey_numbers ?: '—' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PROMINENT RETURNED FOR CORRECTION ALERT BANNER --}}
        @if($master->isReturnedForCorrection())
            @php
                $latestReturn = $master->movements->first(fn($m) => $m->isReturn());
            @endphp
            <div class="card border-0 mb-4 shadow" style="border-radius: 16px; background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 50%, #dc2626 100%); color: #ffffff; border-left: 8px solid #450a0a !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-3 bg-white text-danger rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 56px; height: 56px; min-width: 56px;">
                                <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                            </div>
                            <div>
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1.5">
                                    <h5 class="fw-bold mb-0 text-white" style="letter-spacing: -0.01em;">FILE RETURNED FOR CORRECTION</h5>
                                    @if($latestReturn)
                                        <span class="badge bg-white text-danger fw-bold text-uppercase px-2.5 py-1" style="font-size: 0.72rem;">
                                            Priority: {{ $latestReturn->priority }}
                                        </span>
                                        @if($latestReturn->mistake_category)
                                            <span class="badge bg-white text-dark px-2.5 py-1" style="font-size: 0.72rem;">
                                                Category: {{ $latestReturn->mistake_category }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <div class="text-white-50 small mb-2.5">
                                    Returned from <strong class="text-white text-uppercase">{{ $latestReturn?->from_department ?? 'Downstream' }}</strong> to <strong class="text-white text-uppercase">{{ $latestReturn?->to_department ?? $master->current_stage }}</strong> on {{ $latestReturn?->created_at?->format('d M Y, h:i A') }} by <strong>{{ $latestReturn?->officer_name ?: 'Scrutiny Officer' }}</strong>
                                </div>
                                <div class="p-3 rounded-3 bg-black bg-opacity-25 border border-white border-opacity-15 text-white" style="font-size: 0.92rem;">
                                    <div><strong>Mistake / Reason:</strong> {{ $latestReturn?->reason ?? 'Corrections required in document / survey data.' }}</div>
                                    @if($latestReturn?->rectification_notes)
                                        <div class="mt-2 pt-2 border-top border-white border-opacity-10 small text-white-50">
                                            <strong class="text-white">Required Fix / Instructions:</strong> {{ $latestReturn->rectification_notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="text-md-end">
                            <button type="button" class="btn btn-light text-danger fw-bold d-inline-flex align-items-center gap-2 shadow px-4 py-2.5" style="border-radius: 10px; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#resubmitFixModal">
                                <i class="bi bi-arrow-clockwise fs-5"></i> Resubmit Fixed File
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- 5-STAGE VISUAL PROGRESS PIPELINE STEPPER --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-signpost-split-fill text-primary"></i>
                        <span>5-Stage Statutory Clearance Pipeline</span>
                    </h5>
                    <span class="text-muted small">Sequential progression across regulatory departments with bidirectional state handling</span>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3" style="font-size: 0.78rem;">
                    <div class="d-inline-flex align-items-center gap-1.5">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #10B981;"></span>
                        <span class="text-muted fw-semibold">Completed</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-1.5">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #2563EB;"></span>
                        <span class="text-muted fw-semibold">Active Stage</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-1.5">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #DC2626;"></span>
                        <span class="text-muted fw-semibold">Returned for Fix</span>
                    </div>
                    <div class="d-inline-flex align-items-center gap-1.5">
                        <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #94A3B8;"></span>
                        <span class="text-muted fw-semibold">Upcoming</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                @php
                    $stageRanks = [
                        'lease'        => 1,
                        'survey_dgps'  => 2,
                        'survey_drone' => 2,
                        'survey'       => 2,
                        'mining'       => 3,
                        'environment'  => 4,
                        'ppt'          => 5,
                        'completed'    => 6,
                    ];

                    $activeRank = $stageRanks[$master->current_stage] ?? 1;

                    $pipelineNodes = [
                        [
                            'key'         => 'lease',
                            'rank'        => 1,
                            'title'       => 'Lease Application',
                            'dept_code'   => 'LA',
                            'app_number'  => $master->leaseApplication?->application_no ?? ($master->lease_application_id ? 'LA-'.$master->lease_application_id : null),
                            'icon'        => 'bi-file-earmark-text-fill',
                            'description' => 'Concession & parcellation',
                        ],
                        [
                            'key'         => 'survey',
                            'rank'        => 2,
                            'title'       => 'DGPS & Drone Survey',
                            'dept_code'   => 'SURVEY',
                            'app_number'  => ($master->dgpsSurvey?->application_number ?? ($master->dgps_survey_id ? 'DGPS-'.$master->dgps_survey_id : null)) ?: ($master->droneSurvey?->application_number ?? ($master->drone_survey_id ? 'DRN-'.$master->drone_survey_id : null)),
                            'icon'        => 'bi-geo-alt-fill',
                            'description' => 'Boundary & photogrammetry',
                        ],
                        [
                            'key'         => 'mining',
                            'rank'        => 3,
                            'title'       => 'Mining Plan',
                            'dept_code'   => 'MP',
                            'app_number'  => $master->miningApplication?->application_number ?? ($master->mining_application_id ? 'MP-'.$master->mining_application_id : null),
                            'icon'        => 'bi-hammer',
                            'description' => 'RQP scheme & production plan',
                        ],
                        [
                            'key'         => 'environment',
                            'rank'        => 4,
                            'title'       => 'Environmental Clearance',
                            'dept_code'   => 'ENV',
                            'app_number'  => $master->environmentProject?->project_code ?? ($master->environment_project_id ? 'ENV-'.$master->environment_project_id : null),
                            'icon'        => 'bi-tree-fill',
                            'description' => 'SEIAA / DEIAA baseline reports',
                        ],
                        [
                            'key'         => 'ppt',
                            'rank'        => 5,
                            'title'       => 'PPT Presentation',
                            'dept_code'   => 'PPT',
                            'app_number'  => $master->pptApplication?->application_no ?? ($master->ppt_application_id ? 'PPT-'.$master->ppt_application_id : null),
                            'icon'        => 'bi-easel-fill',
                            'description' => 'SEAC technical presentation',
                        ],
                    ];
                @endphp

                <div class="row g-3 pipeline-stepper-container">
                    @foreach($pipelineNodes as $idx => $node)
                        @php
                            $isNodeActive = ($activeRank === $node['rank']);
                            $isNodePassed = ($activeRank > $node['rank']) || ($master->pipeline_status === 'completed');
                            $isNodeReturned = $isNodeActive && $master->isReturnedForCorrection();

                            if ($isNodeReturned) {
                                $cardClass = 'step-returned';
                                $badgeClass = 'bg-danger text-white';
                                $stepBadgeClass = 'bg-danger text-white';
                                $statusText = 'Returned For Fix';
                                $statusIcon = 'bi-exclamation-triangle-fill';
                            } elseif ($isNodeActive) {
                                $cardClass = 'step-active';
                                $badgeClass = 'bg-primary text-white';
                                $stepBadgeClass = 'bg-primary text-white';
                                $statusText = 'Active Stage';
                                $statusIcon = 'bi-play-circle-fill';
                            } elseif ($isNodePassed) {
                                $cardClass = 'step-completed';
                                $badgeClass = 'bg-success text-white';
                                $stepBadgeClass = 'bg-success text-white';
                                $statusText = 'Completed';
                                $statusIcon = 'bi-check-circle-fill';
                            } else {
                                $cardClass = 'step-upcoming';
                                $badgeClass = 'badge-slate';
                                $stepBadgeClass = 'badge-slate';
                                $statusText = 'Upcoming';
                                $statusIcon = 'bi-clock';
                            }
                        @endphp

                        <div class="col-lg col-md-4 col-sm-6">
                            <div class="pipeline-step-card {{ $cardClass }}">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge {{ $stepBadgeClass }}" style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.04em;">
                                            STEP {{ $node['rank'] }}
                                        </span>
                                        <span class="badge bg-light text-dark border px-1.5 py-0.5" style="font-size: 0.65rem; font-weight: 700;">
                                            {{ $node['dept_code'] }}
                                        </span>
                                    </div>

                                    <div class="step-icon-bubble">
                                        <i class="bi {{ $node['icon'] }}"></i>
                                    </div>

                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.88rem;">{{ $node['title'] }}</h6>
                                    <div class="text-muted small mb-2.5" style="font-size: 0.72rem; min-height: 24px;">
                                        {{ $node['description'] }}
                                    </div>
                                </div>

                                <div>
                                    <div class="p-1.5 rounded-2 bg-white border mb-2 text-truncate" style="font-size: 0.74rem;">
                                        <span class="text-muted">App #:</span>
                                        <strong class="text-dark">{{ $node['app_number'] ?? 'Pending' }}</strong>
                                    </div>

                                    <span class="badge {{ $badgeClass }} px-2 py-1.5 w-100 d-inline-flex align-items-center justify-content-center gap-1 text-truncate" style="font-size: 0.72rem; border-radius: 6px;">
                                        <i class="bi {{ $statusIcon }}"></i> {{ $statusText }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- WORKFLOW ACTION CONTROLS BAR --}}
        <div class="card border-0 shadow-sm mb-4 control-hub-card">
            <div class="card-body p-3.5">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="p-2.5 rounded-3 bg-light border text-primary" style="font-size: 1.2rem;">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Stage Transition & Scrutiny Controls</h6>
                            <span class="text-muted small">Execute bidirectional movements with immutable audit verification</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        {{-- Return For Correction Button --}}
                        <button type="button" class="btn btn-outline-danger d-inline-flex align-items-center gap-1.5 px-3 py-2" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#returnForCorrectionModal">
                            <i class="bi bi-arrow-counterclockwise fs-6"></i> Return for Correction
                        </button>

                        {{-- Resubmit Fix Button --}}
                        <button type="button" class="btn {{ $master->isReturnedForCorrection() ? 'btn-success text-white shadow-sm' : 'btn-outline-success' }} d-inline-flex align-items-center gap-1.5 px-3 py-2" style="border-radius: 8px; font-weight: 600; font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#resubmitFixModal">
                            <i class="bi bi-arrow-repeat fs-6"></i> Resubmit Fixed File
                        </button>

                        {{-- Forward Stage Button --}}
                        <button type="button" class="btn btn-navy-primary d-inline-flex align-items-center gap-1.5 px-3.5 py-2" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#forwardStageModal">
                            <i class="bi bi-arrow-right-circle-fill fs-6"></i> Forward to Next Department
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- OPERATIONAL BOTTLENECK ANALYSIS & DURATION CARD --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-stopwatch-fill text-primary"></i>
                                <span>Operational Bottleneck & Department Durations</span>
                            </h5>
                            <span class="text-muted small">Cumulative processing time spent per departmental stage</span>
                        </div>
                        <span class="badge px-3 py-1.5 border" style="background: #f1f5f9; color: #0F1E4D; font-size: 0.8rem; border-color: #cbd5e1 !important; font-weight: 700;">
                            Total Lifecycle: {{ $timelineData['total_duration_human'] }}
                        </span>
                    </div>

                    <div class="card-body p-3.5">
                        @if(empty($timelineData['department_metrics']))
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clock-history fs-3 d-block mb-2"></i>
                                <p class="small mb-0">No movements recorded yet. Processing time will appear as files transition.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0" style="font-size: 0.825rem;">
                                    <thead class="table-light text-muted text-uppercase" style="font-size: 0.7rem;">
                                        <tr>
                                            <th>Department Stage</th>
                                            <th>Time Spent</th>
                                            <th>Workload Share</th>
                                            <th>Scrutiny Cycles</th>
                                            <th>Bottleneck Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($timelineData['department_metrics'] as $deptKey => $metric)
                                            @php
                                                $isSlowest = ($timelineData['bottlenecks']['slowest_department'] === $deptKey);
                                                $hasReturns = ($metric['returns_count'] > 0);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong class="text-dark">{{ $metric['label'] }}</strong>
                                                    <div class="text-muted" style="font-size: 0.7rem;">Stage: {{ $deptKey }}</div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-dark">{{ $metric['hours'] }} hrs</span>
                                                    <span class="text-muted small">({{ $metric['days'] }} days)</span>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px; background-color: #f1f5f9;">
                                                            <div class="progress-bar {{ $isSlowest ? 'bg-warning' : 'bg-primary' }}" role="progressbar" style="width: {{ $metric['percentage'] }}%;"></div>
                                                        </div>
                                                        <span class="text-muted fw-semibold" style="font-size: 0.72rem;">{{ $metric['percentage'] }}%</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($hasReturns)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                            <i class="bi bi-arrow-return-left me-1"></i>{{ $metric['returns_count'] }} correction(s)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                            Zero corrections
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($hasReturns && $isSlowest)
                                                        <span class="badge bg-danger text-white"><i class="bi bi-exclamation-octagon-fill me-1"></i>Critical Bottleneck</span>
                                                    @elseif($isSlowest)
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-bottom me-1"></i>Slowest Stage</span>
                                                    @elseif($hasReturns)
                                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Correction Cycle</span>
                                                    @else
                                                        <span class="badge bg-light text-muted border"><i class="bi bi-check me-1 text-success"></i>Normal Pace</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
                    <div class="card-header bg-white py-3.5 px-4 border-bottom">
                        <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success"></i>
                            <span>Zero Cross-Contamination Guarantee</span>
                        </h5>
                        <div class="text-muted small mt-0.5">Strict departmental compartmentalization verified</div>
                    </div>
                    <div class="card-body p-3.5">
                        <ul class="list-group list-group-flush" style="font-size: 0.825rem;">
                            <li class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-123 text-primary"></i>
                                        <strong class="text-dark">Distinct Application Numbers</strong>
                                    </div>
                                    <div class="text-muted small mt-0.5">Each department issues its own dedicated numbering series (<code>LA-</code>, <code>MP-</code>, <code>DGPS-</code>, <code>ENV-</code>, <code>PPT-</code>).</div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Guaranteed</span>
                            </li>
                            <li class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-folder2 text-info"></i>
                                        <strong class="text-dark">Independent Document Vaults</strong>
                                    </div>
                                    <div class="text-muted small mt-0.5">Dedicated document tables & disk folders; cloned on promotion with zero shared pointers.</div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Guaranteed</span>
                            </li>
                            <li class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-cash-stack text-warning"></i>
                                        <strong class="text-dark">Segregated Financial Ledgers</strong>
                                    </div>
                                    <div class="text-muted small mt-0.5">Separate accounting line items, fees, and receipt allocations per department.</div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Guaranteed</span>
                            </li>
                            <li class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-clock-history text-secondary"></i>
                                        <strong class="text-dark">Immutable Audit Trail</strong>
                                    </div>
                                    <div class="text-muted small mt-0.5">All forward transitions and return reasons permanently logged with officer timestamps.</div>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">Guaranteed</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- DEPARTMENT COMPARTMENTALIZATION GRID --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important;">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-grid-3x3-gap-fill text-primary"></i>
                        <span>Departmental Sub-Records & Vault Status</span>
                    </h5>
                    <span class="text-muted small">Direct single-pane-of-glass access to linked departmental records</span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    @foreach($departments as $deptKey => $dept)
                        @php
                            $hasModel = !is_null($dept['model']);
                            $vaultCardBorderClass = match($dept['code']) {
                                'LA'     => 'vault-card-la',
                                'SURVEY' => 'vault-card-survey',
                                'MP'     => 'vault-card-mining',
                                'ENV'    => 'vault-card-env',
                                'PPT'    => 'vault-card-ppt',
                                default  => '',
                            };
                            $deptPillClass = match($dept['code']) {
                                'LA'     => 'vault-dept-la',
                                'SURVEY' => 'vault-dept-survey',
                                'MP'     => 'vault-dept-mining',
                                'ENV'    => 'vault-dept-env',
                                'PPT'    => 'vault-dept-ppt',
                                default  => 'vault-dept-la',
                            };
                        @endphp
                        <div class="col-xl-4 col-md-6">
                            <div class="vault-card {{ $vaultCardBorderClass }} h-100 {{ $hasModel ? 'bg-white' : 'bg-white' }}" style="{{ !$hasModel ? 'border-style: dashed !important; border-color: #cbd5e1;' : '' }}">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="vault-dept-pill {{ $deptPillClass }}">
                                        {{ $dept['code'] }} DEPARTMENT
                                    </span>
                                    <span class="badge {{ $hasModel ? 'bg-success-subtle text-success border border-success-subtle' : 'badge-slate' }}" style="font-size: 0.72rem;">
                                        {{ $hasModel ? 'Linked' : 'Pending' }}
                                    </span>
                                </div>

                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $dept['name'] }}</h6>
                                <div class="text-muted small mb-2.5" style="font-size: 0.76rem;">
                                    Application No: <strong class="text-dark">{{ $dept['no'] }}</strong>
                                </div>

                                <div class="p-2.5 rounded-2 bg-light border mb-2.5" style="font-size: 0.75rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Record Status:</span>
                                        <span class="fw-semibold text-dark">{{ ucfirst(str_replace('_', ' ', (string)$dept['status'])) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Vault Type:</span>
                                        <span class="fw-semibold text-dark">Dedicated Module Table</span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-1">
                                    <span class="text-muted" style="font-size: 0.72rem;">Zero Cross-Contamination</span>
                                    @if($hasModel)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.7rem;">
                                            <i class="bi bi-shield-check"></i> Isolated
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size: 0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Awaiting Forward</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- CHRONOLOGICAL AUDIT LOG TABLE & VISUAL FEED --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: #ffffff; border: 1px solid #e2e8f0 !important; overflow: hidden;">
            <div class="card-header bg-white py-3.5 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i>
                        <span>Immutable Chronological Audit Trail</span>
                    </h5>
                    <span class="text-muted small">Every forward transition, return for correction, and resubmission permanently recorded</span>
                </div>
                <div>
                    <span class="badge px-3 py-1.5 border" style="background: #f1f5f9; color: #0F1E4D; font-size: 0.8rem; border-color: #cbd5e1 !important; font-weight: 700;">
                        {{ $timelineData['movements']->count() }} Movement(s)
                    </span>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table audit-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Timestamp</th>
                                <th>Movement Vector</th>
                                <th>Action Type</th>
                                <th>Priority</th>
                                <th>Category / Reason / Rectification Notes</th>
                                <th>Officer Name & ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timelineData['movements'] as $movement)
                                <tr class="{{ $movement->isReturn() ? 'audit-row-return' : '' }}">
                                    <td class="ps-4 text-nowrap">
                                        <div class="fw-bold text-dark">{{ $movement->created_at ? $movement->created_at->format('d M Y') : 'N/A' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            {{ $movement->created_at ? $movement->created_at->format('h:i:s A') : '' }}
                                            ({{ $movement->created_at?->diffForHumans() }})
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-1.5 fw-semibold">
                                            <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.72rem;">{{ $movement->from_department ?: 'System' }}</span>
                                            <i class="bi {{ $movement->isReturn() ? 'bi-arrow-left text-danger' : 'bi-arrow-right text-primary' }}"></i>
                                            <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.72rem;">{{ $movement->to_department }}</span>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge {{ $movement->action_badge_class }} px-2 py-1" style="font-size: 0.72rem;">
                                            {{ $movement->action_label }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge {{ $movement->priority_badge_class }} text-uppercase px-2 py-1" style="font-size: 0.7rem;">
                                            {{ $movement->priority }}
                                        </span>
                                    </td>

                                    <td>
                                        @if($movement->mistake_category)
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle me-1" style="font-size: 0.7rem;">
                                                {{ $movement->mistake_category }}
                                            </span>
                                        @endif
                                        <span class="text-dark">{{ $movement->reason }}</span>
                                        @if($movement->rectification_notes)
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-1 text-primary"></i><strong>Fix Notes:</strong> {{ $movement->rectification_notes }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="text-nowrap">
                                        <div class="fw-semibold text-dark">{{ $movement->officer_name ?: 'System' }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">User ID: #{{ $movement->officer_id ?? 'N/A' }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No movements recorded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL: FORWARD STAGE --}}
<div class="modal fade" id="forwardStageModal" tabindex="-1" aria-labelledby="forwardStageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <form action="{{ route('master-pipeline.forward', $master->id) }}" method="POST">
                @csrf
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%);">
                    <h5 class="modal-title fw-bold text-white" id="forwardStageModalLabel">
                        <i class="bi bi-arrow-right-circle-fill me-2"></i>Forward Project to Next Department
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info d-flex align-items-center mb-3 py-2 px-3" style="border-radius: 8px; font-size: 0.82rem;">
                        <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                        <div>Current Active Stage: <strong class="text-uppercase">{{ $master->current_stage }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Target Department <span class="text-danger">*</span></label>
                        <select name="to_department" class="form-select form-control" required style="border-radius: 8px;">
                            <option value="">Select Target Department...</option>
                            <option value="survey_dgps">DGPS Land Survey</option>
                            <option value="survey_drone">Drone Photogrammetry Survey</option>
                            <option value="mining">Mining Plan Preparation</option>
                            <option value="environment">Environmental Clearance (EC)</option>
                            <option value="ppt">SEAC PPT Presentation</option>
                            <option value="completed">Clearance Completed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Priority</label>
                        <select name="priority" class="form-select form-control" style="border-radius: 8px;">
                            <option value="normal" selected>Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Forwarding Remarks</label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Enter remarks or instructions for the receiving department..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-slate" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-navy-primary">
                        <i class="bi bi-check2-circle me-1"></i> Forward Stage
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: RETURN FOR CORRECTION --}}
<div class="modal fade" id="returnForCorrectionModal" tabindex="-1" aria-labelledby="returnForCorrectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <form action="{{ route('master-pipeline.return', $master->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold text-white" id="returnForCorrectionModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Return File for Upstream Correction
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-danger d-flex align-items-center mb-3 py-2 px-3" style="border-radius: 8px; font-size: 0.82rem;">
                        <i class="bi bi-shield-exclamation fs-5 me-2"></i>
                        <div>Returning a file will revert the active stage and dispatch urgent in-app notification alerts to upstream officers.</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">From Department (Scrutinizing Department) <span class="text-danger">*</span></label>
                            <select name="from_department" class="form-select form-control" required style="border-radius: 8px;">
                                <option value="{{ $master->current_stage }}" selected>Current: {{ strtoupper($master->current_stage) }}</option>
                                <option value="ppt">PPT Department</option>
                                <option value="environment">Environmental Clearance</option>
                                <option value="mining">Mining Department</option>
                                <option value="survey_dgps">DGPS Survey</option>
                                <option value="survey_drone">Drone Survey</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Return To (Upstream Department) <span class="text-danger">*</span></label>
                            <select name="to_department" class="form-select form-control" required style="border-radius: 8px;">
                                <option value="">Select Upstream Department...</option>
                                <option value="lease">Lease Application Department</option>
                                <option value="survey_dgps">DGPS Survey Department</option>
                                <option value="survey_drone">Drone Survey Department</option>
                                <option value="mining">Mining Plan Department</option>
                                <option value="environment">Environmental Clearance Department</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Mistake Category</label>
                            <select name="mistake_category" class="form-select form-control" style="border-radius: 8px;">
                                <option value="">Select Category...</option>
                                <option value="Boundary Discrepancy">Boundary Discrepancy</option>
                                <option value="Survey Number Mismatch">Survey Number Mismatch</option>
                                <option value="Missing KML / Shapefile">Missing KML / Shapefile</option>
                                <option value="Inadequate Baseline Data">Inadequate Baseline Data</option>
                                <option value="Calculation Error in Production">Calculation Error in Production</option>
                                <option value="Affidavit Incomplete">Affidavit Incomplete</option>
                                <option value="Document Illegible">Document Illegible</option>
                                <option value="Other Technical Defect">Other Technical Defect</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">Priority Level <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityNormal" value="normal">
                                    <label class="form-check-label text-dark small" for="priorityNormal">Normal</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityHigh" value="high" checked>
                                    <label class="form-check-label text-dark small" for="priorityHigh">High</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="priority" id="priorityUrgent" value="urgent">
                                    <label class="form-check-label text-danger fw-bold small" for="priorityUrgent">Urgent</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Mandatory Mistake Reason / Scrutiny Remark <span class="text-danger">* (min 5 chars)</span></label>
                            <textarea name="reason" rows="3" class="form-control" placeholder="Specify precisely what mistake was detected and why the file is being returned..." required minlength="5" style="border-radius: 8px;"></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark small">Rectification Instructions for Upstream Team</label>
                            <textarea name="rectification_notes" rows="2" class="form-control" placeholder="Provide step-by-step guidance on how the defect must be corrected..." style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-slate" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-send-exclamation-fill me-1"></i> Return File with Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL: RESUBMIT FIX --}}
<div class="modal fade" id="resubmitFixModal" tabindex="-1" aria-labelledby="resubmitFixModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
            <form action="{{ route('master-pipeline.resubmit', $master->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold text-white" id="resubmitFixModalLabel">
                        <i class="bi bi-arrow-repeat me-2"></i>Resubmit Rectified File for Scrutiny
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">From Department (Rectifying Department) <span class="text-danger">*</span></label>
                        <select name="from_department" class="form-select form-control" required style="border-radius: 8px;">
                            <option value="{{ $master->current_stage }}" selected>{{ strtoupper($master->current_stage) }} Department</option>
                            <option value="lease">Lease Application</option>
                            <option value="survey_dgps">DGPS Survey</option>
                            <option value="survey_drone">Drone Survey</option>
                            <option value="mining">Mining Plan</option>
                            <option value="environment">Environmental Clearance</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">To Department (Scrutiny Department) <span class="text-danger">*</span></label>
                        <select name="to_department" class="form-select form-control" required style="border-radius: 8px;">
                            <option value="">Select Scrutiny Department...</option>
                            <option value="ppt">PPT Department</option>
                            <option value="environment">Environmental Clearance</option>
                            <option value="mining">Mining Plan</option>
                            <option value="survey_dgps">DGPS Survey</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Rectification Notes / Fix Description <span class="text-danger">*</span></label>
                        <textarea name="rectification_notes" rows="4" class="form-control" placeholder="Describe the corrections made and documents uploaded to resolve the previous issue..." required style="border-radius: 8px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-slate" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i> Resubmit Rectified File
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
