@extends('layouts.app')
@section('title', 'B2 Project — '.$project->project_code)
@section('main_content')
<div class="content-body default-height"><div class="container-fluid">
  <div class="d-flex justify-content-between align-items-start mb-4">
    <div>
      <a href="{{ route('environment-b2.index') }}" class="text-muted small"><i class="fa fa-arrow-left"></i> All B2 projects</a>
      <h3 class="mb-1">{{ $project->project_name }}</h3>
      <p class="mb-0 text-muted">
        <strong>{{ $project->project_code }}</strong> · 
        {{ $project->customer?->customer_name ?: $project->contact_name }} · 
        <span class="badge badge-info">B2</span> · 
        {{ $project->district?->name ?: $project->location }}
      </p>
    </div>
    <div class="d-flex gap-2">
      @if(in_array($project->status, ['approved', 'reported']))
        <a href="{{ route('ec-certificate.step', ['step' => 1, 'project_id' => $project->id]) }}" class="btn btn-success">
          <i class="fa fa-certificate me-1"></i> Issue EC Certificate
        </a>
      @endif

      @can('environment.b2.review')
      <form method="POST" action="{{ route('environment-b2.status', $project) }}" class="d-flex gap-2">
        @csrf
        <select name="status" class="form-select form-select-sm">
          <option value="draft" @selected($project->status==='draft')>Draft / Upload</option>
          <option value="validation" @selected($project->status==='validation')>Validate Data</option>
          <option value="approved" @selected($project->status==='approved')>Approve Data</option>
          <option value="reported" @selected($project->status==='reported')>Generate Reports</option>
          <option value="archived" @selected($project->status==='archived')>Archive & Backup</option>
        </select>
        <button class="btn btn-sm btn-primary">Move Stage</button>
      </form>
      @endcan
    </div>
  </div>

  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif 
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <small class="text-muted">Total Checklist Items</small>
          <h3 class="mb-0">{{ $summary['total'] }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <small class="text-muted">Uploaded / In Progress</small>
          <h3 class="mb-0">{{ $summary['uploaded'] }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <small class="text-muted">Approved Documents</small>
          <h3 class="mb-0 text-success">{{ $summary['approved'] }}</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      @foreach($folders as $folder => $documents)
      <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-3">
          <strong><i class="fa fa-folder text-warning me-2"></i>{{ $loop->iteration }}. {{ $folder }}</strong>
          <span class="badge badge-outline-primary">
            {{ $documents->where('status','approved')->count() }} / {{ $documents->count() }} approved
          </span>
        </div>
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead>
              <tr>
                <th>Required Document</th>
                <th>Status</th>
                <th>Uploaded File</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($documents as $document)
              <tr>
                <td>
                  <strong>{{ $document->document_name }}</strong>
                  @if($document->review_note)
                    <br><small class="text-danger"><i class="fa fa-info-circle"></i> {{ $document->review_note }}</small>
                  @endif
                </td>
                <td>
                  <span class="badge badge-{{ $document->status === 'approved' ? 'success' : ($document->status === 'revision_required' ? 'danger' : ($document->status === 'pending' ? 'secondary' : 'warning')) }}">
                    {{ str_replace('_',' ', ucfirst($document->status)) }}
                  </span>
                </td>
                <td>
                  @if($document->file_path)
                    <a href="{{ route('environment-b2.documents.download', $document) }}" class="text-primary font-w600">
                      <i class="fa fa-download me-1"></i>{{ $document->file_name ?: 'Download File' }}
                    </a>
                    <br><small class="text-muted">{{ optional($document->uploaded_at)->format('d M Y H:i') }}</small>
                  @else 
                    <span class="text-muted">Not uploaded</span>
                  @endif
                </td>
                <td class="text-end">
                  @if($document->file_path)
                  <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info me-1" title="View Document in separate page">
                    <i class="fa fa-eye"></i> View
                  </a>
                  @endif
                  @can('environment.b2.upload')
                  <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#upload{{ $document->id }}">
                    <i class="fa fa-upload me-1"></i>Upload
                  </button>
                  @endcan 
                  @can('environment.b2.review')
                  @if($document->file_path)
                  <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#review{{ $document->id }}">
                    <i class="fa fa-check me-1"></i>Review
                  </button>
                  @endif 
                  @endcan
                </td>
              </tr>

              <!-- Upload Modal -->
              <div class="modal fade" id="upload{{ $document->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('environment-b2.documents.upload', $document) }}">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title">Upload: {{ $document->document_name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <label class="form-label">Select Document File</label>
                      <input class="form-control" type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.kml,.kmz,.dwg">
                      <small class="text-muted">PDF, Images, Office files, GIS KML or ZIP; max 25 MB.</small>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button class="btn btn-primary">Upload File</button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Review Modal -->
              <div class="modal fade" id="review{{ $document->id }}" tabindex="-1">
                <div class="modal-dialog">
                  <form class="modal-content" method="POST" action="{{ route('environment-b2.documents.review', $document) }}">
                    @csrf
                    <div class="modal-header">
                      <h5 class="modal-title">Review: {{ $document->document_name }}</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <label class="form-label">Validation Decision</label>
                      <select name="status" class="form-select" required>
                        <option value="validated" @selected($document->status === 'validated')>Validated</option>
                        <option value="approved" @selected($document->status === 'approved')>Approved</option>
                        <option value="revision_required" @selected($document->status === 'revision_required')>Revision Required (Flag for re-upload)</option>
                      </select>
                      <label class="form-label mt-3">Review Notes / Remarks</label>
                      <textarea class="form-control" name="review_note" rows="3" placeholder="Enter remarks or correction instructions...">{{ $document->review_note }}</textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button class="btn btn-success">Save Decision</button>
                    </div>
                  </form>
                </div>
              </div>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endforeach
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><strong>Process Flow Lifecycle</strong></div>
        <div class="card-body">
          <ol class="mb-0 ps-3">
            <li class="mb-1 text-success">Client / Applicant Profile</li>
            <li class="mb-1 text-success">Sub-category: B2 Clearance</li>
            <li class="mb-1 text-success">Document Preparation (6 Folders)</li>
            <li class="mb-1 {{ $summary['uploaded'] > 0 ? 'text-success' : 'text-muted' }}">Upload & Store Data ({{ $summary['uploaded'] }}/{{ $summary['total'] }})</li>
            <li class="mb-1 {{ $project->status === 'validation' || $project->status === 'approved' ? 'text-success font-w600' : 'text-muted' }}">Validate Data</li>
            <li class="mb-1 {{ $project->status === 'approved' || $project->status === 'reported' ? 'text-success font-w600' : 'text-muted' }}">Approve Data ({{ $summary['approved'] }} Approved)</li>
            <li class="mb-1 {{ $project->status === 'reported' ? 'text-success font-w600' : 'text-muted' }}">Generate Reports & EC Issuance</li>
            <li class="mb-1 {{ $project->status === 'archived' ? 'text-success font-w600' : 'text-muted' }}">Archive & Backup</li>
          </ol>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header"><strong>Activity Audit Trail</strong></div>
        <div class="card-body" style="max-height: 420px; overflow-y: auto;">
          @forelse($activities as $activity)
          <div class="mb-3 border-bottom pb-2">
            <strong class="text-primary">{{ $activity->action }}</strong>
            <p class="mb-1 small">{{ $activity->description }}</p>
            <small class="text-muted"><i class="fa fa-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}</small>
          </div>
          @empty 
            <span class="text-muted">No activity logged yet.</span>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div></div>
@endsection
