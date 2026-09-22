@extends('layouts.app')
@section('title', 'B2 Workflow Step '.$step)
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php($labels=['Applicant','B2 Category','Folders','Documents','Validation','Approval','Preview'])
<div class="content-body default-height"><div class="container-fluid"><div class="wizard-wrap" style="max-width:920px;">
  <div class="step-progress">
    @foreach($labels as $number => $label)
      <div class="sp-step {{ $number + 1 < $step ? 'done' : ($number + 1 === $step ? 'active' : '') }}">
        <div class="circ">@if($number + 1 < $step)<i class="fa fa-check"></i>@else{{ $number + 1 }}@endif</div>
        <div class="sp-label">{{ $label }}</div>
      </div>
    @endforeach
  </div>
  <div class="wizard-card">
    <div class="wc-eyebrow">Step {{ $step }} of 7 &middot; Environment Clearance B2</div>

    @if($step === 1)
      <h4>Client / Applicant Information</h4>
      <div class="wc-sub">Enter the applicant and project details for the B2 environment-clearance document process.</div>
      
      @if ($errors->any())
        <div class="alert alert-danger py-2">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('environment-b2.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Existing Customer (Optional)</label>
            <select class="form-select" id="customer_select" name="customer_id">
              <option value="">-- Or Create New Customer Below --</option>
              @foreach($customers as $c)
                <option value="{{ $c->id }}" data-name="{{ $c->customer_name }}" data-phone="{{ $c->mobile_num }}" data-email="{{ $c->email }}" data-district="{{ $c->district_id }}" data-address="{{ $c->address }}">
                  {{ $c->customer_name }} ({{ $c->company_name ?: $c->mimas_no }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Client / Applicant Name *</label>
            <input class="form-control" name="client_name" id="client_name" required placeholder="e.g. R. Kumaresan">
          </div>

          <div class="col-md-6">
            <label class="form-label">Project / Quarry Name *</label>
            <input class="form-control" name="project_name" required placeholder="e.g. Rough Stone Quarry Project">
          </div>

          <div class="col-md-6">
            <label class="form-label">District *</label>
            <select class="form-select" name="district_id" id="district_id" required>
              <option value="">Select district</option>
              @foreach($districts as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Contact Number *</label>
            <input class="form-control" name="contact_phone" id="contact_phone" required placeholder="10-digit mobile number">
          </div>

          <div class="col-md-6">
            <label class="form-label">Contact Email</label>
            <input class="form-control" type="email" name="contact_email" id="contact_email" placeholder="applicant@example.com">
          </div>

          <div class="col-12">
            <label class="form-label">Quarry Location / Address</label>
            <input class="form-control" name="location" id="location" placeholder="e.g. SF No. 124/1, Village, Taluk">
          </div>
        </div>

        <div class="card-panel mt-4 mb-3" style="background:var(--navy-soft);border:none">
          <i class="fa fa-info-circle text-primary"></i> <span style="font-size:.82rem">A unique B2 reference number (ENV-B2-YYYY-XXXX) and 6 structured document folders with 29 checklist items will be generated immediately.</span>
        </div>

        <div class="wizard-actions d-flex justify-content-between">
          <a href="{{ route('environment-b2.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Cancel</a>
          @can('environment.b2.create')
          <button type="submit" class="btn btn-primary px-4">Create Project & Generate Folders <i class="fa fa-arrow-right"></i></button>
          @endcan
        </div>
      </form>

    @elseif($step === 2)
      <h4>Sub Category: B2</h4>
      <div class="wc-sub">Confirm the environmental-clearance category to load the appropriate document structure.</div>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="opt-tile selected">
            <div class="opt-radio"></div>
            <div>
              <div class="opt-title">B2 Category</div>
              <div class="opt-desc">Environment clearance with B2 category requirements (Standard State level)</div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="opt-tile">
            <div class="opt-radio"></div>
            <div>
              <div class="opt-title">B1 Category</div>
              <div class="opt-desc">Separate EIA / Public Hearing / 12 Chapters process</div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none">
        <i class="fa fa-check-circle text-success"></i> <span style="font-size:.82rem">B2 selected: Six folders and complete checklist items are active for this workflow.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 1) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 3) }}" class="btn btn-primary px-4">Continue <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 3)
      <h4>Environment Clearance &mdash; B2 Folders</h4>
      <div class="wc-sub">Documents are organised into six folders as required by the B2 process.</div>
      @php($folders=[['Documents','Statutory and site records','fa-file-alt'],['Site Photographs','DGPS, fencing and greenbelt','fa-camera'],['Report','Reports, forms and checklist','fa-file-contract'],['GIS','GIS data & Boundary','fa-globe'],['Signed Reports','Final signed reports','fa-cloud-upload-alt'],['PARIVESH','Online registration documents','fa-desktop']])
      <div class="row g-3">
        @foreach($folders as [$name,$detail,$icon])
          <div class="col-md-4">
            <div class="folder-tile">
              <div class="fico"><i class="fa {{ $icon }}"></i></div>
              <div class="ftitle">{{ $loop->iteration }}. {{ $name }}</div>
              <div class="fmeta">{{ $detail }}</div>
              <span class="badge-status pending">Configured</span>
            </div>
          </div>
        @endforeach
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 2) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 4) }}" class="btn btn-primary px-4">Continue to Documents <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 4)
      <h4>Document Preparation & Upload</h4>
      <div class="wc-sub">Prepare each file against the B2 checklist. To upload files to a live project dossier, open the project from the B2 list.</div>
      @if($latestProject)
        <div class="alert alert-info d-flex justify-content-between align-items-center">
          <div>
            <strong>Latest Active Project:</strong> {{ $latestProject->project_code }} — {{ $latestProject->project_name }}
          </div>
          <a href="{{ route('environment-b2.show', $latestProject) }}" class="btn btn-sm btn-info">Open Project Dossier</a>
        </div>
      @endif
      <div class="dropzone"><i class="fa fa-cloud-upload-alt fa-3x text-muted mb-2"></i><div class="dz-title">Drag & drop files here, or open Project Dossier</div><div class="dz-sub">PDF, JPG, PNG and Office files &mdash; maximum 25 MB</div></div>
      @php($items=['500m Radius Letter','Existing Pit Letter','Approved Mining Plan Book','DGPS Photograph','Pre-feasibility Report','GIS Data','Signed Reports','Common Application Form','Payment Receipt'])
      <div class="mt-4">
        @foreach($items as $item)
          <div class="checklist-row d-flex align-items-center justify-content-between p-2 border-bottom">
            <div class="d-flex align-items-center">
              <i class="fa fa-file-pdf text-danger me-3 fa-lg"></i>
              <div>
                <div class="ci-name font-w600">{{ $loop->iteration }}. {{ $item }}</div>
                <small class="text-muted">Folder item</small>
              </div>
            </div>
            <span class="badge badge-{{ $loop->iteration < 4 ? 'danger' : 'secondary' }}">{{ $loop->iteration < 4 ? 'Mandatory' : 'Required' }}</span>
          </div>
        @endforeach
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 3) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 5) }}" class="btn btn-primary px-4">Continue <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 5)
      <h4>Validate Data</h4>
      <div class="wc-sub">Review uploaded documents, check completeness and return files for correction where needed.</div>
      @foreach(['Documents folder','Site photographs','Report & GIS','PARIVESH registration'] as $item)
        <div class="checklist-row d-flex align-items-center justify-content-between p-2 border-bottom">
          <div class="d-flex align-items-center">
            <i class="fa fa-check-square text-success me-3 fa-lg"></i>
            <div>
              <div class="ci-name font-w600">{{ $item }}</div>
              <small class="text-muted">Verification workflow</small>
            </div>
          </div>
          <span class="badge badge-warning">Validation Queue</span>
        </div>
      @endforeach
      <div class="card-panel mt-4 mb-0" style="background:#fff6e6;border:none">
        <i class="fa fa-sync-alt text-warning"></i> <span style="font-size:.82rem">If data is not correct, use the "Revision Required" action inside the project dossier to flag items for re-upload.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 4) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 6) }}" class="btn btn-primary px-4">Continue <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 6)
      <h4>Approve Data & Generate Reports</h4>
      <div class="wc-sub">Complete the B2 workflow by approving verified data, generating reports and archiving the project.</div>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="folder-tile">
            <div class="fico"><i class="fa fa-check-circle text-success"></i></div>
            <div class="ftitle">Approve Data</div>
            <div class="fmeta">Approved & verified document set</div>
            <span class="badge-status verified">Ready</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="folder-tile">
            <div class="fico"><i class="fa fa-chart-bar text-primary"></i></div>
            <div class="ftitle">Generate Reports</div>
            <div class="fmeta">View or download B2 reports</div>
            <span class="badge-status pending">Available</span>
          </div>
        </div>
        <div class="col-md-4">
          <div class="folder-tile">
            <div class="fico"><i class="fa fa-archive text-secondary"></i></div>
            <div class="ftitle">Archive & Backup</div>
            <div class="fmeta">Store final project records</div>
            <span class="badge-status pending">Supported</span>
          </div>
        </div>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 5) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 7) }}" class="btn btn-primary px-4">Preview & Summary <i class="fa fa-arrow-right"></i></a>
      </div>

    @else
      <h4>Data Preview & Summary</h4>
      <div class="wc-sub">Review all environment-clearance B2 project requirements before final completion.</div>
      @if($latestProject)
        <div class="card-panel mt-4 mb-4" style="background:var(--navy-soft); border:none;">
          <h6 style="color:var(--navy); font-weight:600; margin-bottom: 12px;"><i class="fa fa-project-diagram me-2"></i>Active Project Summary</h6>
          <div class="row g-2" style="font-size: 0.88rem;">
            <div class="col-md-4"><span class="text-muted">Client:</span> <br><b>{{ $latestProject->customer?->customer_name ?: $latestProject->contact_name }}</b></div>
            <div class="col-md-4"><span class="text-muted">Project Code:</span> <br><b>{{ $latestProject->project_code }}</b></div>
            <div class="col-md-4"><span class="text-muted">District:</span> <br><b>{{ $latestProject->district?->name ?: $latestProject->location }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Category:</span> <br><span class="badge badge-info">B2 Category</span></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Checklist Items:</span> <br><b>{{ $latestProject->documents->count() }} Documents</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Status:</span> <br><span class="badge badge-success">{{ ucfirst($latestProject->status) }}</span></div>
          </div>
        </div>
      @endif
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none">
        <i class="fa fa-check-circle text-success"></i> <span style="font-size:.82rem">B2 Process Setup verified. You can manage all document uploads from the Project Dossier.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 6) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.index') }}" class="btn btn-success px-4">Finish & View Applications <i class="fa fa-check"></i></a>
      </div>
    @endif

  </div>
</div></div></div>

@if($step === 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
  const custSelect = document.getElementById('customer_select');
  if (custSelect) {
    custSelect.addEventListener('change', function() {
      const opt = this.options[this.selectedIndex];
      if (opt && opt.value) {
        document.getElementById('client_name').value = opt.getAttribute('data-name') || '';
        document.getElementById('contact_phone').value = opt.getAttribute('data-phone') || '';
        document.getElementById('contact_email').value = opt.getAttribute('data-email') || '';
        document.getElementById('location').value = opt.getAttribute('data-address') || '';
        const dist = opt.getAttribute('data-district');
        if (dist) {
          document.getElementById('district_id').value = dist;
        }
      }
    });
  }
});
</script>
@endif
@endsection
