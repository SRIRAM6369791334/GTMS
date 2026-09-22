@extends('layouts.app')
@section('title', 'Sub Category 1 — Site & Mining Documentation')
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">

<div class="content-body default-height">
    <div class="container-fluid">

        <div class="row page-titles align-items-center">
            <div class="col-md-6">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('eviron.index') }}">Environment Clearance B1</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Sub Category 1</a></li>
                </ol>
            </div>
            <div class="col-md-6 text-end">
                @if($allProjects->isNotEmpty())
                <form method="GET" action="{{ route('environstage1') }}" class="d-inline-flex align-items-center gap-2">
                    <label class="small text-muted mb-0">Active Project:</label>
                    <select name="project_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        @foreach($allProjects as $p)
                        <option value="{{ $p->id }}" @selected($project && $project->id === $p->id)>
                            {{ $p->project_code }} — {{ $p->project_name }}
                        </option>
                        @endforeach
                    </select>
                </form>
                @endif
            </div>
        </div>

        @if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger py-2">{{ session('error') }}</div>@endif

        <main class="admin-content">

            <!-- ================= FOLDER TABS ================= -->
            <div class="admin-tabs">
                @php
                    $tabColors = [
                        'Documents' => ['var(--c-documents)', '#eef1fb'],
                        'Report' => ['var(--c-report)', '#e9f7ef'],
                        'GIS & Maps' => ['var(--c-gis)', '#f2ecfa'],
                        'Signed Reports' => ['var(--c-upload)', '#fdf1e2'],
                        'PARIVESH Acknowledgements' => ['var(--c-parivesh)', '#fbe9f0'],
                    ];
                @endphp
                @foreach($folders as $idx => $folder)
                @php
                    $fColor = $tabColors[$folder->name] ?? ['#007bff', '#eef1fb'];
                    $docs = $documentsByFolder[$folder->id] ?? collect();
                @endphp
                <div class="admin-tab {{ $idx === 0 ? 'active' : '' }}" data-target="tab-{{ $folder->id }}" style="--tab-color:{{ $fColor[0] }}; --tab-tint:{{ $fColor[1] }};">
                    <span class="badge-num" style="background:{{ $fColor[0] }};">{{ $idx + 1 }}</span>
                    {{ $folder->name }} <span class="cnt">{{ $docs->count() }}</span>
                </div>
                @endforeach
            </div>

            <!-- ================= FOLDER TAB PANELS ================= -->
            @foreach($folders as $idx => $folder)
            @php
                $fColor = $tabColors[$folder->name] ?? ['#007bff', '#eef1fb'];
                $docs = $documentsByFolder[$folder->id] ?? collect();
                $approvedCount = $docs->where('status', 'approved')->count();
            @endphp
            <div class="admin-panel tab-panel mb-4" id="tab-{{ $folder->id }}" style="{{ $idx > 0 ? 'display:none;' : '' }}">
                <div class="panel-head d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ $idx + 1 }} &middot; {{ $folder->name }}</h5>
                        <p class="sub mb-0">Documents checklist for {{ $folder->name }}</p>
                    </div>
                    <span class="panel-progress-chip fw-bold" style="font-size:.82rem; color:{{ $fColor[0] }};">
                        {{ $approvedCount }} / {{ $docs->count() }} approved
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:36px;"><input type="checkbox"></th>
                                <th>Document</th>
                                <th>Status</th>
                                <th>Uploaded File</th>
                                <th>Uploaded At</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($docs as $doc)
                            <tr>
                                <td><input type="checkbox"></td>
                                <td>
                                    <span class="row-mod-dot" style="background:{{ $fColor[0] }};"></span>
                                    <span class="doc-name font-w600">{{ $doc->document_name }}</span>
                                    @if($doc->review_note)
                                        <div class="doc-hint text-danger"><i class="fa fa-info-circle"></i> {{ $doc->review_note }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'revision_required' ? 'danger' : ($doc->status === 'pending' ? 'secondary' : 'warning')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($doc->file_path)
                                        <a href="{{ route('environment-b2.documents.download', $doc) }}" class="text-primary font-w600">
                                            <i class="fa fa-download me-1"></i>{{ $doc->file_name ?: 'Download' }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : '—' }}</td>
                                <td class="text-end">
                                    @can('environment.b2.upload')
                                    <button class="btn btn-xs btn-outline-primary" data-bs-toggle="modal" data-bs-target="#b1UploadModal{{ $doc->id }}" title="Upload Document">
                                        <i class="fa fa-upload"></i>
                                    </button>
                                    @endcan
                                    @can('environment.b2.review')
                                    @if($doc->file_path)
                                    <button class="btn btn-xs btn-outline-success" data-bs-toggle="modal" data-bs-target="#b1ReviewModal{{ $doc->id }}" title="Review Document">
                                        <i class="fa fa-check"></i>
                                    </button>
                                    @endif
                                    @endcan
                                </td>
                            </tr>

                            <!-- Upload Modal -->
                            <div class="modal fade" id="b1UploadModal{{ $doc->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('environment-b2.documents.upload', $doc) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Upload: {{ $doc->document_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">File to upload</label>
                                            <input type="file" name="file" class="form-control" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.kml,.kmz,.dwg">
                                            <small class="text-muted">Supports PDF, office files, images, KML maps; max 25 MB.</small>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-primary">Upload Now</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Review Modal -->
                            <div class="modal fade" id="b1ReviewModal{{ $doc->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" method="POST" action="{{ route('environment-b2.documents.review', $doc) }}">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Review: {{ $doc->document_name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Decision</label>
                                            <select name="status" class="form-select" required>
                                                <option value="validated" @selected($doc->status === 'validated')>Validated</option>
                                                <option value="approved" @selected($doc->status === 'approved')>Approved</option>
                                                <option value="revision_required" @selected($doc->status === 'revision_required')>Revision Required</option>
                                            </select>
                                            <label class="form-label mt-3">Notes / Remarks</label>
                                            <textarea class="form-control" name="review_note" rows="3">{{ $doc->review_note }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success">Save Decision</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">No documents required for this folder.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach

        </main>

    </div>
</div>
@endsection
