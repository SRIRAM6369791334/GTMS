@extends('layouts.app')
@section('title', 'B2 Workflow Step '.$step)
@section('main_content')
<link href="{{ asset('css/style1.css') }}" rel="stylesheet">
@php
$labels = ['Applicant','B2 Category','Folders','Documents','Validation','Approval','Handling Team','Payment','Preview'];
@endphp
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
    <div class="wc-eyebrow">Step {{ $step }} of 9 &middot; Environment Clearance B2</div>

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

        {{-- CUSTOMER UNIQUE ID LOOKUP CARD --}}
        <div class="card p-3 mb-3" style="background:#f0f7ff; border:2px dashed #93c5fd; border-radius:14px;">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <label class="form-label fw-bold mb-0 text-primary" style="font-size:0.92rem;">
              <i class="fa fa-fingerprint me-1"></i> Customer Unique ID Lookup
            </label>
            <span class="badge bg-primary text-white"><i class="fa fa-bolt me-1"></i> Instant Autofill</span>
          </div>
          <p class="text-muted small mb-2">Enter or select the applicant's Customer Unique ID (e.g. MIMAS number or Customer ID). The system will automatically retrieve and populate all registered profile information.</p>
          
          <div class="input-group">
            <span class="input-group-text bg-white border-primary"><i class="fa fa-search text-primary"></i></span>
            <input type="text" id="mimas_search_input" class="form-control text-uppercase fw-bold border-primary" 
                   placeholder="Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)" list="mimas_datalist" autocomplete="off">
            <button class="btn btn-primary px-3 fw-bold" type="button" id="btn_lookup_mimas">
              <i class="fa fa-sync-alt me-1"></i> Fetch Details
            </button>
          </div>
          
          <datalist id="mimas_datalist">
            @if(isset($customers))
              @foreach($customers as $c)
                <option value="{{ $c->mimas_no }}">{{ $c->company_name }} ({{ $c->customer_name }})</option>
              @endforeach
            @endif
          </datalist>

          <div id="mimas_feedback_box" class="mt-2" style="display:none;"></div>
        </div>

        <input type="hidden" name="customer_id" id="field_customer_id" value="{{ old('customer_id') }}">

        <div class="row g-3">

          <div class="col-md-6">
            <label class="form-label">Client / Applicant Name *</label>
            <input class="form-control auto-filled-field" name="client_name" id="client_name" required placeholder="e.g. R. Kumaresan">
          </div>

          <div class="col-md-6">
            <label class="form-label">Project / Quarry Name *</label>
            <input class="form-control auto-filled-field" name="project_name" id="project_name" required placeholder="e.g. Rough Stone Quarry Project">
          </div>

          <div class="col-md-6">
            <label class="form-label">District *</label>
            <select class="form-select auto-filled-field" name="district_id" id="district_id" required>
              <option value="">Select district</option>
              @foreach($districts as $d)
                <option value="{{ $d->id }}">{{ $d->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Contact Number *</label>
            <input class="form-control auto-filled-field" name="contact_phone" id="contact_phone" required placeholder="10-digit mobile number">
          </div>

          <div class="col-md-6">
            <label class="form-label">Contact Email</label>
            <input class="form-control auto-filled-field" type="email" name="contact_email" id="contact_email" placeholder="applicant@example.com">
          </div>

          <div class="col-12">
            <label class="form-label">Quarry Location / Address</label>
            <input class="form-control auto-filled-field" name="location" id="location" placeholder="e.g. SF No. 124/1, Village, Taluk">
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
      @php
        $folders = [
          ['Documents','Statutory and site records','fa-file-alt'],
          ['Site Photographs','DGPS, fencing and greenbelt','fa-camera'],
          ['Report','Reports, forms and checklist','fa-file-contract'],
          ['GIS','GIS data & Boundary','fa-globe'],
          ['Signed Reports','Final signed reports','fa-cloud-upload-alt'],
          ['PARIVESH','Online registration documents','fa-desktop']
        ];
      @endphp
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
      @php
        $items = ['500m Radius Letter','Existing Pit Letter','Approved Mining Plan Book','DGPS Photograph','Pre-feasibility Report','GIS Data','Signed Reports','Common Application Form','Payment Receipt'];
      @endphp
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
        <a href="{{ route('environment-b2.step', 7) }}" class="btn btn-primary px-4">Continue to Handling Team <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 7)
      <h4>Project Handling Team &amp; In-Charge Persons</h4>
      <div class="wc-sub">Assign field officers, EIA coordinators, and project technical coordinators responsible for this B2 project.</div>

      <div class="card p-3 my-3 bg-light border-0 rounded-3">
        <div class="table-responsive">
          <table class="table table-bordered align-middle bg-white mb-0" style="font-size:0.85rem;">
            <thead class="bg-light">
              <tr>
                <th style="width:50px;" class="text-center">#</th>
                <th style="width:30%;">Person Name</th>
                <th style="width:30%;">Role / Designation</th>
                <th>Notes &amp; Responsibilities</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td class="text-center fw-bold">1</td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. Ramesh Kumar (EIA Coordinator)"></td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. EIA Coordinator / Field Officer"></td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. Site inspection, greenbelt verification"></td>
              </tr>
              <tr>
                <td class="text-center fw-bold">2</td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. Priya Sundaram"></td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. Documentation Specialist"></td>
                <td><input type="text" class="form-control form-control-sm" placeholder="e.g. PARIVESH online dossier submission"></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-panel mt-3 mb-0" style="background:#f0f9ff;border:none">
        <i class="fa fa-info-circle text-primary"></i> <span style="font-size:.82rem">Handling team members are archived with this project dossier for statutory audit traceability.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 6) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 8) }}" class="btn btn-primary px-4">Continue to Payment <i class="fa fa-arrow-right"></i></a>
      </div>

    @elseif($step === 8)
      <h4>Financial &amp; Billing Ledger</h4>
      <div class="wc-sub">Record agreed statutory clearance fee, advance receipts, and compute pending balance.</div>

      @php
        $latestPv = (float)($latestProject->product_value ?? 0);
        $latestPa = (float)($latestProject->paid_amount ?? 0);
        $latestPe = max(0, $latestPv - $latestPa);
      @endphp
      <div class="row g-3 my-2">
        <div class="col-md-4">
          <div class="p-3 rounded-3 border" style="background:#f8fafc; border-left: 4px solid #0F1E4D !important;">
            <div class="text-muted small fw-semibold text-uppercase">Service / Product Value</div>
            <div class="h4 fw-bold mb-0 text-navy mt-1">₹ {{ number_format($latestPv, 2) }}</div>
            <small class="text-muted" style="font-size:11px;">Agreed B2 fee quotation</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded-3 border" style="background:#f0fdf4; border-left: 4px solid #10b981 !important;">
            <div class="text-success small fw-semibold text-uppercase">Paid Amount</div>
            <div class="h4 fw-bold mb-0 text-success mt-1">₹ {{ number_format($latestPa, 2) }}</div>
            <small class="text-muted" style="font-size:11px;">Advance received</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded-3 border" style="background:#fff7ed; border-left: 4px solid #f97316 !important;">
            <div class="text-warning-emphasis small fw-semibold text-uppercase">Pending Balance Due</div>
            <div class="h4 fw-bold mb-0 text-danger mt-1">₹ {{ number_format($latestPe, 2) }}</div>
            <small class="text-muted" style="font-size:11px;">Auto-calculated outstanding</small>
          </div>
        </div>
      </div>
      <div class="card-panel mt-3 mb-0" style="background:var(--navy-soft);border:none">
        <i class="fa fa-receipt text-primary"></i> <span style="font-size:.82rem">Payment settlements update real-time across the GTMS customer billing ledger.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 7) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.step', 9) }}" class="btn btn-primary px-4">Preview &amp; Summary <i class="fa fa-arrow-right"></i></a>
      </div>

    @else
      <h4>Data Preview &amp; Summary</h4>
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
            <div class="col-md-4 mt-2"><span class="text-muted">Quoted Value:</span> <br><b>₹ {{ number_format((float)$latestProject->product_value, 2) }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Paid Amount:</span> <br><b class="text-success">₹ {{ number_format((float)$latestProject->paid_amount, 2) }}</b></div>
            <div class="col-md-4 mt-2"><span class="text-muted">Pending Balance:</span> <br><b class="text-danger">₹ {{ number_format((float)$latestProject->pending_amount, 2) }}</b></div>
          </div>
        </div>
      @endif
      <div class="card-panel mt-4 mb-0" style="background:var(--green-soft);border:none">
        <i class="fa fa-check-circle text-success"></i> <span style="font-size:.82rem">B2 Process Setup verified. You can manage all document uploads and billing from the Project Dossier.</span>
      </div>
      <div class="wizard-actions mt-4 d-flex justify-content-between">
        <a href="{{ route('environment-b2.step', 8) }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        <a href="{{ route('environment-b2.index') }}" class="btn btn-success px-4">Finish &amp; View Applications <i class="fa fa-check"></i></a>
      </div>
    @endif

  </div>
</div></div></div>

<style>
.field-autofilled {
  background-color: #ecfdf5 !important;
  border-color: #10b981 !important;
  transition: background-color 0.4s ease, border-color 0.4s ease;
}
</style>

@if($step === 1)
<script>
document.addEventListener('DOMContentLoaded', function() {
  function applyCustomerAutofill(c) {
    if (!c) return;
    const clientNameInput = document.getElementById('client_name');
    const projectNameInput = document.getElementById('project_name');
    const contactPhoneInput = document.getElementById('contact_phone');
    const contactEmailInput = document.getElementById('contact_email');
    const districtSelect = document.getElementById('district_id');
    const locationInput = document.getElementById('location');
    const customerIdInput = document.getElementById('field_customer_id');

    if (customerIdInput) customerIdInput.value = c.id || '';
    if (clientNameInput) clientNameInput.value = c.customer_name || '';
    if (projectNameInput) {
      const entity = c.company_name || c.customer_name || 'Mining';
      projectNameInput.value = entity + ' Quarry Project';
    }
    if (contactPhoneInput) contactPhoneInput.value = c.mobile_num || '';
    if (contactEmailInput) contactEmailInput.value = c.email || '';
    if (districtSelect && c.district_id) districtSelect.value = c.district_id;
    if (locationInput) locationInput.value = c.address || '';

    // Visual highlight on auto-filled fields
    document.querySelectorAll('.auto-filled-field').forEach(el => {
      el.classList.add('field-autofilled');
      setTimeout(() => el.classList.remove('field-autofilled'), 3000);
    });

    const feedbackBox = document.getElementById('mimas_feedback_box');
    if (feedbackBox) {
      feedbackBox.style.display = 'block';
      feedbackBox.innerHTML = `
        <div class="alert alert-success py-2 px-3 mb-0 small d-flex align-items-center justify-content-between">
          <div>
            <i class="fa fa-check-circle me-1 text-success"></i>
            <strong>Customer Profile Loaded:</strong> ${c.company_name || c.customer_name}
            &middot; <span class="text-muted">${c.district_name || 'District'}</span>
            &middot; <span class="badge bg-success-subtle text-success border border-success ms-1">Customer ID: ${c.mimas_no || c.id}</span>
          </div>
          <span class="badge bg-success text-white">All details populated</span>
        </div>
      `;
    }
  }

  function performMimasLookup() {
    const searchInput = document.getElementById('mimas_search_input');
    const btn = document.getElementById('btn_lookup_mimas');
    const feedbackBox = document.getElementById('mimas_feedback_box');
    const val = searchInput ? searchInput.value.trim() : '';

    if (!val) {
      if (feedbackBox) {
        feedbackBox.style.display = 'block';
        feedbackBox.innerHTML = '<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-exclamation-triangle me-1"></i> Please enter or select a Customer Unique ID first.</div>';
      }
      return;
    }

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Searching...';
    }
    if (feedbackBox) feedbackBox.style.display = 'none';

    fetch('/customers/lookup-mimas/' + encodeURIComponent(val))
      .then(res => res.json())
      .then(res => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (res.status === 1 && res.data) {
          applyCustomerAutofill(res.data);
        } else {
          if (feedbackBox) {
            feedbackBox.style.display = 'block';
            feedbackBox.innerHTML = `<div class="alert alert-warning py-2 px-3 mb-0 small"><i class="fa fa-info-circle me-1"></i> ${res.message || 'No customer found.'}</div>`;
          }
        }
      })
      .catch(err => {
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="fa fa-sync-alt me-1"></i> Fetch Details';
        }
        if (feedbackBox) {
          feedbackBox.style.display = 'block';
          feedbackBox.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-0 small"><i class="fa fa-times-circle me-1"></i> Customer not found. You can enter details manually below.</div>';
        }
      });
  }

  const btnLookup = document.getElementById('btn_lookup_mimas');
  const searchInput = document.getElementById('mimas_search_input');

  if (btnLookup) btnLookup.addEventListener('click', performMimasLookup);
  if (searchInput) {
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performMimasLookup();
      }
    });
    searchInput.addEventListener('change', function() {
      if (this.value.trim().length >= 3) {
        performMimasLookup();
      }
    });
  }
});
</script>
@endif
@endsection
