@extends('layouts.app')
@section('title', 'Documents - Step 5')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap" style="max-width:860px;">
      <div class="step-progress">
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
        <div class="sp-step active"><div class="circ">5</div><div class="sp-label">Documents</div></div>
        <div class="sp-step"><div class="circ">6</div><div class="sp-label">Review</div></div>
      </div>

      <div class="wizard-card">
        <div class="wc-eyebrow">Step 5 of 6</div>
        <h4>Regulatory Checklist &amp; Survey Plans</h4>
        <div class="wc-sub">Upload all required statutory documents, affidavits, and digital survey plans across the 3 folders.</div>

        @php
          $uploadedDocs = $uploadedDocs ?? session('lease_draft.uploaded_docs', []);
          $uploadedCount = count($uploadedDocs);
          $customCount = count(array_filter($uploadedDocs, fn($d) => !empty($d['is_custom'])));
          $totalCount = 19 + $customCount;
          $percent = $totalCount > 0 ? min(100, round(($uploadedCount / $totalCount) * 100)) : 0;
        @endphp

        <!-- Overall Progress Header -->
        <div class="d-flex justify-content-between align-items-center mb-1 mt-3">
          <div style="font-size:.78rem; font-weight:700; color:var(--navy);">OVERALL DOCUMENT COMPLETION</div>
          <div class="d-flex align-items-baseline gap-1">
            <span class="fs-4 fw-bold text-primary" id="counter_text">{{ $uploadedCount }} / {{ $totalCount }}</span>
            <div style="font-size:.7rem; color:var(--muted);">uploaded</div>
          </div>
        </div>
        <div class="progress-thin mt-2 mb-4">
          <div class="progress-bar" id="doc_progress_bar" style="width:{{ $percent }}%"></div>
        </div>

        <!-- Interactive Dropzone -->
        <div class="dropzone" id="dropzone_box" style="cursor: pointer; display:none; border: 2px dashed #93c5fd; background: #f8fbff; transition: all 0.2s;">
          <i class="bi bi-cloud-arrow-up" style="font-size: 2.2rem; color: #2563eb;"></i>
          <div class="dz-title" style="font-weight: 700; color: #1e3a8a;">Drag &amp; drop files here, or click to browse</div>
          <div class="dz-sub text-muted">Upload authentic PDF, PNG, JPG, or KML files from your computer</div>
          <div class="mt-3">
            <button type="button" class="btn btn-sm btn-navy px-3" id="btn_select_files">
              <i class="bi bi-folder2-open me-1"></i> Browse Files to Upload
            </button>
          </div>
          <input type="file" id="real_file_input" multiple style="display:none;" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml">
          <input type="file" id="individual_file_input" style="display:none;" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml">
        </div>

        <div id="upload_success_alert" class="alert {{ $uploadedCount >= $totalCount ? 'd-flex' : 'd-none' }} align-items-center gap-2 mt-3 mb-3" style="background:#e6f4ea; border:1px solid #b7e1cd; color:#137333; font-size:.88rem; border-radius:8px;">
          <i class="bi bi-check-circle-fill fs-5 text-success"></i>
          <div><strong>All Documents &amp; Plan Files Uploaded!</strong> All regulatory documents and survey plans are in place. You can proceed to Review &amp; Launch.</div>
        </div>

        <!-- FOLDER 1: DOCUMENTS FOLDER -->
        @php
          $f7Custom = array_filter($uploadedDocs, fn($d) => !empty($d['is_custom']) && isset($d['folder_id']) && $d['folder_id'] == 7);
          $f7Total = 9 + count($f7Custom);
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-folder-open me-1"></i> 1. Documents folder
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-open-lease-add-doc"
                    data-folder-id="7" data-folder-name="1. Documents folder" style="font-size:.75rem;">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <small class="badge bg-light text-muted" id="badge_folder_7" style="font-size:.75rem;">Folder ID: #7 &middot; <span class="folder-items-count" id="count_folder_7">{{ $f7Total }}</span> Items</small>
          </div>
        </div>

        <div class="folder-checklist-box" id="folder_container_7">
          @php
            $folder7Items = [
              ['id' => 1, 'name' => '1. Land Document', 'default' => 'land_document_title.pdf', 'size' => '1.2 MB', 'mandatory' => true],
              ['id' => 2, 'name' => '2. Consent (If Applicable)', 'default' => 'landowner_consent_deed.pdf', 'size' => '1.1 MB', 'mandatory' => false],
              ['id' => 3, 'name' => '3. Adangal & A-register', 'default' => 'adangal_a_register_record.pdf', 'size' => '2.4 MB', 'mandatory' => true],
              ['id' => 4, 'name' => '4. Patta & Encumbrance Certificate', 'default' => 'patta_chitta_certificate.pdf', 'size' => '890 KB', 'mandatory' => true],
              ['id' => 5, 'name' => '5. Work Order', 'default' => 'work_order_approval.pdf', 'size' => '680 KB', 'mandatory' => false],
              ['id' => 6, 'name' => '6. Gazette', 'default' => 'district_gazette_notification.pdf', 'size' => '1.5 MB', 'mandatory' => false],
              ['id' => 7, 'name' => '7. Recommendation Letter', 'default' => 'ad_mines_recommendation_letter.pdf', 'size' => '820 KB', 'mandatory' => false],
              ['id' => 8, 'name' => '8. Mineral Management System – Application', 'default' => 'mimas_portal_application.pdf', 'size' => '1.9 MB', 'mandatory' => true],
              ['id' => 9, 'name' => '9. Challan downloaded from Mimas', 'default' => 'mimas_treasury_challan.pdf', 'size' => '832 KB', 'mandatory' => false],
            ];
          @endphp

          @foreach($folder7Items as $item)
            @php
              $doc = $uploadedDocs[$item['id']] ?? null;
              $isUp = !empty($doc);
              $dispSize = $doc ? (is_numeric($doc['file_size']) ? ($doc['file_size'] >= 1048576 ? (round($doc['file_size']/1048576, 1) . ' MB') : (round($doc['file_size']/1024, 0) . ' KB')) : $doc['file_size']) : $item['size'];
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="7" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}">
              <div class="ci-icon">
                @if($isUp)
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @else
                  <i class="bi bi-file-earmark"></i>
                @endif
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">{{ $item['name'] }}</div>
                <div class="ci-meta">
                  @if($isUp)
                    {{ $doc['file_name'] }} &middot; {{ $dispSize }}
                  @else
                    Not uploaded
                  @endif
                </div>
              </div>
              @if($isUp)
                <span class="badge-status uploaded">Uploaded</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
              @else
                <span class="badge-status {{ $item['mandatory'] ? 'mandatory' : 'pending' }}">{{ $item['mandatory'] ? 'Mandatory' : 'Pending' }}</span>
                <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.7rem;">Upload</button>
              @endif
            </div>
          @endforeach

          @foreach($f7Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isMandatory = !empty($cDoc['is_mandatory']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="7" data-is-custom="1">
              <div class="ci-icon" style="background:var(--green-soft, #e6f4ea);">
                <i class="bi bi-check-lg" style="color:var(--green);"></i>
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">
                  <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
                  {{ $cDoc['doc_name'] ?? 'Custom Document' }}
                </div>
                <div class="ci-meta">
                  <span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> {{ $cDoc['file_name'] }}</span> &middot; {{ $dispSize }}
                </div>
              </div>
              <span class="badge-status {{ $isMandatory ? 'mandatory' : 'pending' }}">{{ $isMandatory ? 'Mandatory' : 'Optional' }}</span>
              <span class="badge-status uploaded">Uploaded</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
            </div>
          @endforeach
        </div>

        <!-- FOLDER 2: LEASE APPLICATION FOLDER -->
        @php
          $f8Custom = array_filter($uploadedDocs, fn($d) => !empty($d['is_custom']) && isset($d['folder_id']) && $d['folder_id'] == 8);
          $f8Total = 7 + count($f8Custom);
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-folder me-1"></i> 2. Lease Application folder
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-open-lease-add-doc"
                    data-folder-id="8" data-folder-name="2. Lease Application folder" style="font-size:.75rem;">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <small class="badge bg-light text-muted" id="badge_folder_8" style="font-size:.75rem;">Folder ID: #8 &middot; <span class="folder-items-count" id="count_folder_8">{{ $f8Total }}</span> Items</small>
          </div>
        </div>

        <div class="folder-checklist-box" id="folder_container_8">
          @php
            $folder8Items = [
              ['id' => 10, 'name' => '10. Lease application – signed, FMB, Plan', 'default' => 'signed_lease_application.pdf', 'size' => '2.1 MB', 'mandatory' => true],
              ['id' => 11, 'name' => '11. Affidavit – Income Tax', 'default' => 'affidavit_income_tax.pdf', 'size' => '410 KB', 'mandatory' => true],
              ['id' => 12, 'name' => '12. IT returns (If Applicable)', 'default' => 'it_returns_assessment.pdf', 'size' => '1.3 MB', 'mandatory' => false],
              ['id' => 13, 'name' => '13. Affidavit – Mining Due', 'default' => 'affidavit_mining_dues.pdf', 'size' => '840 KB', 'mandatory' => true],
              ['id' => 14, 'name' => '14. Affidavit – Mining Lease', 'default' => 'affidavit_mining_lease.pdf', 'size' => '380 KB', 'mandatory' => true],
              ['id' => 15, 'name' => '15. Affidavit – 1.5 meter depth', 'default' => 'affidavit_depth_safety.pdf', 'size' => '520 KB', 'mandatory' => false],
              ['id' => 16, 'name' => '16. Affidavit – Hill Areas', 'default' => 'affidavit_hill_areas.pdf', 'size' => '470 KB', 'mandatory' => false],
            ];
          @endphp

          @foreach($folder8Items as $item)
            @php
              $doc = $uploadedDocs[$item['id']] ?? null;
              $isUp = !empty($doc);
              $dispSize = $doc ? (is_numeric($doc['file_size']) ? ($doc['file_size'] >= 1048576 ? (round($doc['file_size']/1048576, 1) . ' MB') : (round($doc['file_size']/1024, 0) . ' KB')) : $doc['file_size']) : $item['size'];
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="8" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}">
              <div class="ci-icon">
                @if($isUp)
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @else
                  <i class="bi bi-file-earmark"></i>
                @endif
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">{{ $item['name'] }}</div>
                <div class="ci-meta">
                  @if($isUp)
                    {{ $doc['file_name'] }} &middot; {{ $dispSize }}
                  @else
                    Not uploaded
                  @endif
                </div>
              </div>
              @if($isUp)
                <span class="badge-status uploaded">Uploaded</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
              @else
                <span class="badge-status {{ $item['mandatory'] ? 'mandatory' : 'pending' }}">{{ $item['mandatory'] ? 'Mandatory' : 'Pending' }}</span>
                <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.7rem;">Upload</button>
              @endif
            </div>
          @endforeach

          @foreach($f8Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isMandatory = !empty($cDoc['is_mandatory']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="8" data-is-custom="1">
              <div class="ci-icon" style="background:var(--green-soft, #e6f4ea);">
                <i class="bi bi-check-lg" style="color:var(--green);"></i>
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">
                  <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
                  {{ $cDoc['doc_name'] ?? 'Custom Document' }}
                </div>
                <div class="ci-meta">
                  <span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> {{ $cDoc['file_name'] }}</span> &middot; {{ $dispSize }}
                </div>
              </div>
              <span class="badge-status {{ $isMandatory ? 'mandatory' : 'pending' }}">{{ $isMandatory ? 'Mandatory' : 'Optional' }}</span>
              <span class="badge-status uploaded">Uploaded</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
            </div>
          @endforeach
        </div>

        <!-- FOLDER 3: PLAN FOLDER -->
        @php
          $f9Custom = array_filter($uploadedDocs, fn($d) => !empty($d['is_custom']) && isset($d['folder_id']) && $d['folder_id'] == 9);
          $f9Total = 3 + count($f9Custom);
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-inbox me-1"></i> 3. Plan folder
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 btn-open-lease-add-doc"
                    data-folder-id="9" data-folder-name="3. Plan folder" style="font-size:.75rem;">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <small class="badge bg-light text-muted" id="badge_folder_9" style="font-size:.75rem;">Folder ID: #9 &middot; <span class="folder-items-count" id="count_folder_9">{{ $f9Total }}</span> Items</small>
          </div>
        </div>

        <div class="folder-checklist-box" id="folder_container_9">
          @php
            $folder9Items = [
              ['id' => 17, 'name' => '17. Plan Source File', 'default' => 'plan_source_file.pdf', 'size' => '3.2 MB', 'mandatory' => true],
              ['id' => 18, 'name' => '18. KML File', 'default' => 'quarry_boundary.kml', 'size' => '240 KB', 'mandatory' => true],
              ['id' => 19, 'name' => '19. Plan PDF', 'default' => 'quarry_plan_layout.pdf', 'size' => '4.1 MB', 'mandatory' => true],
            ];
          @endphp

          @foreach($folder9Items as $item)
            @php
              $doc = $uploadedDocs[$item['id']] ?? null;
              $isUp = !empty($doc);
              $dispSize = $doc ? (is_numeric($doc['file_size']) ? ($doc['file_size'] >= 1048576 ? (round($doc['file_size']/1048576, 1) . ' MB') : (round($doc['file_size']/1024, 0) . ' KB')) : $doc['file_size']) : $item['size'];
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="9" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}">
              <div class="ci-icon">
                @if($isUp)
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @else
                  <i class="bi bi-file-earmark"></i>
                @endif
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">{{ $item['name'] }}</div>
                <div class="ci-meta">
                  @if($isUp)
                    {{ $doc['file_name'] }} &middot; {{ $dispSize }}
                  @else
                    Not uploaded
                  @endif
                </div>
              </div>
              @if($isUp)
                <span class="badge-status uploaded">Uploaded</span>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
              @else
                <span class="badge-status {{ $item['mandatory'] ? 'mandatory' : 'pending' }}">{{ $item['mandatory'] ? 'Mandatory' : 'Pending' }}</span>
                <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.7rem;">Upload</button>
              @endif
            </div>
          @endforeach

          @foreach($f9Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isMandatory = !empty($cDoc['is_mandatory']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="9" data-is-custom="1">
              <div class="ci-icon" style="background:var(--green-soft, #e6f4ea);">
                <i class="bi bi-check-lg" style="color:var(--green);"></i>
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">
                  <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
                  {{ $cDoc['doc_name'] ?? 'Custom Document' }}
                </div>
                <div class="ci-meta">
                  <span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> {{ $cDoc['file_name'] }}</span> &middot; {{ $dispSize }}
                </div>
              </div>
              <span class="badge-status {{ $isMandatory ? 'mandatory' : 'pending' }}">{{ $isMandatory ? 'Mandatory' : 'Optional' }}</span>
              <span class="badge-status uploaded">Uploaded</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
            </div>
          @endforeach
        </div>

        <div class="wizard-actions d-flex justify-content-between align-items-center mt-4">
          <a href="{{ route('step4') }}" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
          @can('application.create')
          <div class="d-flex gap-2">
            <a href="{{ route('application.index') }}" class="btn btn-outline-primary px-3">
              <i class="fa fa-save me-1"></i> Save Draft &amp; Continue Later
            </a>
            <a href="{{ route('step6') }}" id="btn_continue_mimas" class="btn {{ $uploadedCount >= $totalCount ? 'btn-green shadow-sm' : 'btn-navy' }} px-4">Continue to Review <i class="bi bi-arrow-right"></i></a>
          </div>
          @endcan
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD CUSTOM DOCUMENT (LEASE APPLICATION) -->
<!-- ========================================== -->
<div class="modal fade" id="modalAddCustomLeaseDoc" tabindex="-1" aria-labelledby="modalAddCustomLeaseDocLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-navy text-white py-3">
        <h5 class="modal-title fs-6 fw-bold" id="modalAddCustomLeaseDocLabel">
          <i class="bi bi-plus-circle me-2 text-warning"></i>Add Document to Folder
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddCustomLeaseDoc">
        <div class="modal-body p-4">
          <!-- Folder Information (Locked) -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Target Folder</label>
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0"><i class="fa fa-folder text-primary"></i></span>
              <input type="text" class="form-control bg-light border-start-0 fw-semibold text-navy" id="lease_custom_folder_name" readonly value="">
            </div>
            <input type="hidden" id="lease_custom_folder_id" value="">
          </div>

          <!-- Document Name -->
          <div class="mb-3">
            <label for="lease_custom_doc_name" class="form-label small fw-bold text-navy mb-1">
              Document Name <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="lease_custom_doc_name" placeholder="e.g. Property Tax Receipt, VAO Certificate, Family Tree" required>
            <div class="form-text text-muted" style="font-size:11px;">Specify clear title for this document.</div>
          </div>

          <!-- Status (Mandatory / Optional) -->
          <div class="mb-3">
            <label for="lease_custom_doc_status" class="form-label small fw-bold text-navy mb-1">
              Status <span class="text-danger">*</span>
            </label>
            <select class="form-select" id="lease_custom_doc_status" required>
              <option value="1">Mandatory (Required for Scrutiny)</option>
              <option value="0" selected>Optional (Supporting Document)</option>
            </select>
            <div class="form-text text-muted" style="font-size:11px;">Mark whether this document is statutory mandatory or optional supporting proof.</div>
          </div>

          <!-- File Upload Option -->
          <div class="mb-2">
            <label for="lease_custom_doc_file" class="form-label small fw-bold text-navy mb-1">
              File Upload <span class="text-danger">*</span>
            </label>
            <input type="file" class="form-control" id="lease_custom_doc_file" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml,.txt,.doc,.docx" required>
            <div class="form-text text-muted" style="font-size:11px;">Supports PDF, Images, KML, CAD (DWG), and Office documents up to 25 MB.</div>
          </div>
        </div>
        <div class="modal-footer bg-light px-4 py-2 border-top">
          <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-sm btn-navy px-3" id="btn_submit_lease_custom_doc">
            <i class="bi bi-cloud-arrow-up me-1"></i>Upload &amp; Add Document
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  function showSweetAlert(icon, title, text) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: icon,
        title: title,
        text: text,
        confirmButtonColor: '#0F1E4D'
      });
    } else if (typeof toastr !== 'undefined') {
      toastr[icon === 'error' ? 'error' : (icon === 'success' ? 'success' : 'warning')](text);
    } else {
      alert(text);
    }
  }

  const counterText = document.getElementById('counter_text');
  const progressBar = document.getElementById('doc_progress_bar');
  const successAlert = document.getElementById('upload_success_alert');
  const continueBtn = document.getElementById('btn_continue_mimas');
  const dropzoneBox = document.getElementById('dropzone_box');
  const realFileInput = document.getElementById('real_file_input');
  const individualFileInput = document.getElementById('individual_file_input');
  const btnSelectFiles = document.getElementById('btn_select_files');

  let currentTargetRow = null;

  function markRowUploaded(row, fileName, fileSize) {
    row.classList.add('up');
    const iconDiv = row.querySelector('.ci-icon');
    if (iconDiv) {
      iconDiv.innerHTML = '<i class="bi bi-check-lg" style="color:var(--green);"></i>';
      iconDiv.style.background = 'var(--green-soft, #e6f4ea)';
    }

    const metaDiv = row.querySelector('.ci-meta');
    if (metaDiv) {
      metaDiv.innerHTML = `${fileName} &middot; ${fileSize}`;
    }

    const badge = row.querySelector('.badge-status');
    if (badge) {
      badge.className = 'badge-status uploaded';
      badge.textContent = 'Uploaded';
    }

    const uploadBtn = row.querySelector('.btn-upload-item');
    if (uploadBtn) {
      uploadBtn.className = 'btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item';
      uploadBtn.style.fontSize = '.7rem';
      uploadBtn.disabled = false;
      uploadBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Change';
    }
  }

  function updateOverallProgress(uploadedCount, totalCount, percent) {
    totalCount = totalCount || document.querySelectorAll('.checklist-row').length;
    if (typeof uploadedCount === 'undefined') {
      uploadedCount = document.querySelectorAll('.checklist-row.up').length;
    }
    if (typeof percent === 'undefined') {
      percent = totalCount > 0 ? Math.round((uploadedCount / totalCount) * 100) : 0;
    }

    if (counterText) {
      counterText.textContent = `${uploadedCount} / ${totalCount}`;
    }

    if (progressBar) {
      progressBar.style.width = percent + '%';
    }

    if (uploadedCount >= totalCount) {
      if (successAlert) {
        successAlert.classList.remove('d-none');
        successAlert.classList.add('d-flex');
      }
      if (continueBtn) {
        continueBtn.className = 'btn btn-green px-4 shadow-sm';
        continueBtn.innerHTML = 'Continue to Review <i class="bi bi-arrow-right ms-1"></i>';
      }
    }
  }

  // Upload single file via AJAX
  function uploadFileToServer(row, file) {
    const docItem = row.getAttribute('data-doc-item');
    const folderId = row.getAttribute('data-folder');
    const btn = row.querySelector('.btn-upload-item');
    const origHtml = btn ? btn.innerHTML : '';

    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" style="width:12px; height:12px;"></span> Uploading...';
    }

    const formData = new FormData();
    formData.append('file', file);
    formData.append('doc_item', docItem);
    formData.append('folder_id', folderId);
    formData.append('_token', '{{ csrf_token() }}');

    return fetch('{{ route('step5.upload') }}', {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 1) {
        markRowUploaded(row, data.file_name, data.file_size);
        updateOverallProgress(data.uploaded, data.total, data.percent);
        return true;
      } else {
        showSweetAlert('error', 'Upload Failed', data.message || 'Upload failed for item #' + docItem);
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
        return false;
      }
    })
    .catch(err => {
      console.error(err);
      showSweetAlert('error', 'Upload Error', err.message || 'An unexpected error occurred during upload.');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = origHtml;
      }
      return false;
    });
  }

  // Row Upload / Change button handler
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-upload-item');
    if (btn) {
      e.stopPropagation();
      currentTargetRow = btn.closest('.checklist-row');
      const docItem = currentTargetRow.getAttribute('data-doc-item');

      if (docItem === '18') {
        individualFileInput.setAttribute('accept', '.kml,.xml');
      } else {
        individualFileInput.setAttribute('accept', '.pdf,.png,.jpg,.jpeg');
      }
      individualFileInput.value = '';
      individualFileInput.click();
    }
  });

  if (individualFileInput) {
    individualFileInput.addEventListener('change', function () {
      if (this.files && this.files[0] && currentTargetRow) {
        uploadFileToServer(currentTargetRow, this.files[0]);
      }
    });
  }

  // Validate mandatory documents before continuing to MIMAS
  if (continueBtn) {
    continueBtn.addEventListener('click', function (e) {
      const uploaded = parseInt(counterText.innerText.split('/')[0].trim()) || 0;
      if (uploaded === 0) {
        e.preventDefault();
        showSweetAlert('warning', 'Mandatory Documents Required', 'Please upload at least the mandatory regulatory documents before continuing to Review.');
      }
    });
  }

  // Dropzone click & drag handlers
  if (btnSelectFiles) {
    btnSelectFiles.addEventListener('click', function (e) {
      e.stopPropagation();
      realFileInput.click();
    });
  }

  if (dropzoneBox) {
    dropzoneBox.addEventListener('click', function () {
      realFileInput.click();
    });

    dropzoneBox.addEventListener('dragover', function (e) {
      e.preventDefault();
      this.style.borderColor = '#2563eb';
      this.style.background = '#eff6ff';
    });

    dropzoneBox.addEventListener('dragleave', function () {
      this.style.borderColor = '#93c5fd';
      this.style.background = '#f8fbff';
    });

    dropzoneBox.addEventListener('drop', async function (e) {
      e.preventDefault();
      this.style.borderColor = '#93c5fd';
      this.style.background = '#f8fbff';
      if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        await processBatchUpload(e.dataTransfer.files);
      }
    });
  }

  if (realFileInput) {
    realFileInput.addEventListener('change', async function () {
      if (this.files && this.files.length > 0) {
        await processBatchUpload(this.files);
      }
    });
  }

  async function processBatchUpload(fileList) {
    const pendingRows = Array.from(document.querySelectorAll('.checklist-row:not(.up)'));
    if (pendingRows.length === 0) {
      showSweetAlert('info', 'Checklist Complete', 'All checklist items are already uploaded!');
      return;
    }

    const files = Array.from(fileList);
    for (let i = 0; i < files.length && i < pendingRows.length; i++) {
      await uploadFileToServer(pendingRows[i], files[i]);
    }
  }

  // Open Add Document Modal (Lease Application)
  document.querySelectorAll('.btn-open-lease-add-doc').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const folderId = this.getAttribute('data-folder-id');
      const folderName = this.getAttribute('data-folder-name');

      document.getElementById('lease_custom_folder_id').value = folderId;
      document.getElementById('lease_custom_folder_name').value = folderName;
      document.getElementById('lease_custom_doc_name').value = '';
      document.getElementById('lease_custom_doc_status').value = '0';
      document.getElementById('lease_custom_doc_file').value = '';

      const modalEl = document.getElementById('modalAddCustomLeaseDoc');
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    });
  });

  // Submit Add Document Modal (Lease Application)
  const formCustomLeaseDoc = document.getElementById('formAddCustomLeaseDoc');
  if (formCustomLeaseDoc) {
    formCustomLeaseDoc.addEventListener('submit', function (e) {
      e.preventDefault();
      const folderId = document.getElementById('lease_custom_folder_id').value;
      const folderName = document.getElementById('lease_custom_folder_name').value;
      const docName = document.getElementById('lease_custom_doc_name').value.trim();
      const isMandatory = document.getElementById('lease_custom_doc_status').value === '1';
      const fileInput = document.getElementById('lease_custom_doc_file');
      const submitBtn = document.getElementById('btn_submit_lease_custom_doc');

      if (!docName) {
        showSweetAlert('warning', 'Document Name Required', 'Please specify a Document Name before uploading.');
        return;
      }
      if (!fileInput.files || !fileInput.files[0]) {
        showSweetAlert('warning', 'File Required', 'Please select a document file to upload.');
        return;
      }

      const file = fileInput.files[0];
      const origHtml = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading...';

      const formData = new FormData();
      formData.append('file', file);
      formData.append('doc_name', docName);
      formData.append('folder_id', folderId);
      formData.append('is_mandatory', isMandatory ? '1' : '0');
      formData.append('_token', '{{ csrf_token() }}');

      fetch('{{ route('step5.upload') }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = origHtml;

        if (data.status === 1) {
          const targetContainer = document.getElementById('folder_container_' + folderId);
          if (targetContainer) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'checklist-row up';
            rowDiv.setAttribute('data-doc-item', data.doc_item);
            rowDiv.setAttribute('data-folder', folderId);
            rowDiv.setAttribute('data-is-custom', '1');

            rowDiv.innerHTML = `
              <div class="ci-icon" style="background:var(--green-soft, #e6f4ea);">
                <i class="bi bi-check-lg" style="color:var(--green);"></i>
              </div>
              <div class="flex-grow-1">
                <div class="ci-name">
                  <span class="badge bg-light text-primary border me-1" style="font-size:10px;">Custom</span>
                  ${data.doc_name}
                </div>
                <div class="ci-meta">
                  <span class="text-success fw-semibold"><i class="bi bi-paperclip"></i> ${data.file_name}</span> &middot; ${data.file_size}
                </div>
              </div>
              <span class="badge-status ${data.is_mandatory ? 'mandatory' : 'pending'}">${data.is_mandatory ? 'Mandatory' : 'Optional'}</span>
              <span class="badge-status uploaded">Uploaded</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.7rem;"><i class="bi bi-arrow-repeat"></i> Change</button>
            `;
            targetContainer.appendChild(rowDiv);
          }

          // Increment folder count
          const countSpan = document.getElementById('count_folder_' + folderId);
          if (countSpan) {
            countSpan.textContent = (parseInt(countSpan.textContent) || 0) + 1;
          }

          // Update overall progress
          updateOverallProgress(data.uploaded, data.total, data.percent);

          // Close modal
          const modalEl = document.getElementById('modalAddCustomLeaseDoc');
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Document Added!',
              text: 'Document "' + data.doc_name + '" added successfully to ' + folderName + '.',
              confirmButtonColor: '#0F1E4D',
              timer: 2000,
              showConfirmButton: false
            });
          } else if (typeof toastr !== 'undefined') {
            toastr.success('Document "' + data.doc_name + '" added successfully to ' + folderName);
          }
        } else {
          showSweetAlert('error', 'Upload Failed', data.message || 'Custom document upload failed.');
        }
      })
      .catch(err => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = origHtml;
        console.error(err);
        showSweetAlert('error', 'Upload Error', err.message || 'An unexpected error occurred during custom document upload.');
      });
    });
  }
});
</script>

@endsection
