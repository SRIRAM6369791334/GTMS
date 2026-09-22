@extends('layouts.app')
@section('title', 'Documents — ' . ($application->application_no ?? 'Mining Dossier'))
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(!$application)
                <div class="card p-5 text-center">
                    <h4 class="text-muted">No Application Selected</h4>
                    <p>Select a mining application to view and manage its documents.</p>
                    <div><a href="{{ route('miningplan.index') }}" class="btn btn-navy">Back to List</a></div>
                </div>
            @else
                <main class="page">
                    <div class="page-head d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <span class="eyebrow"><i class="bi bi-folder2-open"></i> {{ $application->application_no }} &middot; {{ $application->customer->company_name ?? $application->customer->customer_name }}</span>
                            <h1 class="h3 fw-bold mt-1">Manage Folder Files &amp; Uploads</h1>
                            <p class="text-muted small mb-0">Upload documents against the statutory checklist required for <strong>{{ $application->natureOfWork->name ?? 'Mining Application' }}</strong>.</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('projectfolder', ['id' => $application->id]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Folder Dossier
                            </a>
                            <a href="{{ route('process', ['id' => $application->id]) }}" class="btn btn-navy">
                                <i class="bi bi-diagram-3 me-1"></i>Stage 6 Process Flow
                            </a>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Left Folder Tab Rail -->
                        <div class="col-lg-3">
                            <div class="surface p-2 rounded-3 border shadow-sm bg-white">
                                <div class="p-2 border-bottom mb-2 text-muted fw-bold small text-uppercase">
                                    <i class="bi bi-folder me-1"></i>Folders Checklist
                                </div>
                                <div class="d-flex flex-column gap-1">
                                    @foreach($folders as $folder)
                                        @php
                                            $fStat = $folderCounts[$folder->id] ?? ['total' => 0, 'uploaded' => 0];
                                            $isActive = ($activeFolder && $activeFolder->id == $folder->id);
                                        @endphp
                                        <a href="{{ route('document', ['id' => $application->id, 'folder' => $folder->id]) }}" 
                                           class="d-flex align-items-center justify-content-between p-2 rounded text-decoration-none {{ $isActive ? 'bg-primary text-white' : 'text-dark hover-bg-light' }}"
                                           style="transition: background 0.15s ease;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa fa-folder{{ $isActive ? '-open' : '' }} {{ $isActive ? 'text-white' : 'text-primary' }}"></i>
                                                <span class="small fw-semibold">{{ $folder->name }}</span>
                                            </div>
                                            <span class="badge {{ $isActive ? 'bg-white text-primary' : ($fStat['uploaded'] >= $fStat['total'] && $fStat['total'] > 0 ? 'bg-success' : 'bg-light text-muted border') }}" style="font-size:11px;">
                                                {{ $fStat['uploaded'] }}/{{ $fStat['total'] }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Document Checklist Panel -->
                        <div class="col-lg-9">
                            <div class="surface p-3 p-lg-4 rounded-3 border shadow-sm bg-white">
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <h2 class="h5 fw-bold mb-0 text-dark">{{ $activeFolder->name ?? 'Folder' }}</h2>
                                        <span class="text-muted small">
                                            {{ count($documents) }} checklist items assigned for {{ $application->natureOfWork->name ?? 'this work' }}
                                        </span>
                                    </div>
                                    @php
                                        $uploadedInFolder = $documents->whereIn('status', ['uploaded', 'validated', 'approved'])->count();
                                        $totalInFolder = count($documents);
                                    @endphp
                                    <span class="badge {{ $uploadedInFolder >= $totalInFolder && $totalInFolder > 0 ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2">
                                        {{ $uploadedInFolder }} / {{ $totalInFolder }} Uploaded
                                    </span>
                                </div>

                                @if(count($documents) === 0)
                                    <div class="p-4 text-center text-muted">
                                        <i class="bi bi-info-circle fs-3 d-block mb-2 text-secondary"></i>
                                        <p class="mb-0">No statutory document checklist required under <strong>{{ $activeFolder->name }}</strong> for <strong>{{ $application->natureOfWork->name ?? 'this nature of work' }}</strong>.</p>
                                    </div>
                                @else
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($documents as $doc)
                                            <div class="doc-row p-3 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#f8fafc;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="p-2 rounded" style="background:{{ $doc->status === 'validated' || $doc->status === 'approved' ? '#dcfce7; color:#166534;' : ($doc->status === 'uploaded' ? '#e0f2fe; color:#0369a1;' : ($doc->status === 'revision_required' ? '#fee2e2; color:#991b1b;' : '#f1f5f9; color:#64748b;')) }}">
                                                        @if($doc->status === 'validated' || $doc->status === 'approved')
                                                            <i class="fa fa-check-circle fs-5"></i>
                                                        @elseif($doc->status === 'uploaded')
                                                            <i class="fa fa-file-pdf-o fs-5"></i>
                                                        @elseif($doc->status === 'revision_required')
                                                            <i class="fa fa-exclamation-triangle fs-5"></i>
                                                        @else
                                                            <i class="fa fa-clock-o fs-5"></i>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold small text-dark">{{ $doc->document_name }}</div>
                                                        <div class="text-muted" style="font-size:11.5px;">
                                                            @if($doc->file_name)
                                                                <span class="text-primary">{{ $doc->file_name }}</span> &middot; 
                                                                {{ round(($doc->file_size ?? 0) / 1024, 1) }} KB &middot; 
                                                                Uploaded {{ $doc->uploaded_at ? $doc->uploaded_at->format('d M Y, h:i A') : '' }}
                                                            @else
                                                                <span class="text-secondary">File upload pending</span>
                                                            @endif
                                                        </div>
                                                        @if($doc->review_note)
                                                            <div class="badge bg-danger-subtle text-danger border border-danger-subtle mt-1" style="font-size:11px;">
                                                                Review Note: {{ $doc->review_note }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center gap-2">
                                                    @if($doc->status === 'validated' || $doc->status === 'approved')
                                                        <span class="badge bg-success">Validated</span>
                                                    @elseif($doc->status === 'uploaded')
                                                        <span class="badge bg-info">Uploaded</span>
                                                    @elseif($doc->status === 'revision_required')
                                                        <span class="badge bg-danger">Revision Required</span>
                                                    @else
                                                        <span class="badge bg-light text-muted border">Pending</span>
                                                    @endif

                                                    <!-- Upload Button Form -->
                                                    <form method="POST" action="{{ route('mining.document.upload') }}" enctype="multipart/form-data" class="d-inline-flex align-items-center form-mining-upload">
                                                        @csrf
                                                        <input type="hidden" name="mining_application_id" value="{{ $application->id }}">
                                                        <input type="hidden" name="document_id" value="{{ $doc->id }}">
                                                        <label class="btn btn-sm btn-outline-primary mb-0 btn-upload-label" style="cursor:pointer;" title="Choose and upload file">
                                                            <i class="bi bi-upload me-1"></i><span class="upload-text">{{ $doc->file_name ? 'Re-upload' : 'Upload' }}</span>
                                                            <input type="file" name="file" class="d-none input-mining-file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.kml,.kmz,.zip,.dwg,.dxf">
                                                        </label>
                                                    </form>

                                                    @if($doc->file_path)
                                                        <a href="{{ asset($doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View uploaded file">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </main>
            @endif

        </div>
    </div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('change', '.input-mining-file', function() {
        var file = this.files[0];
        if (!file) return;

        var maxSize = 25 * 1024 * 1024; // 25MB
        if (file.size > maxSize) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Too Large',
                    text: 'The selected file size is ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB. Maximum allowed size is 25 MB.',
                    confirmButtonColor: '#0F1E4D'
                });
            } else {
                alert('The selected file size is ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB. Maximum allowed size is 25 MB.');
            }
            $(this).val('');
            return;
        }

        var form = $(this).closest('form');
        var label = form.find('.btn-upload-label');
        label.addClass('disabled').css('pointer-events', 'none');
        label.find('.upload-text').html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Uploading...');
        form.submit();
    });
});
</script>
@endsection
