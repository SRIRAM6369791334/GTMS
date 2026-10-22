@extends('layouts.app')
@section('title', 'Documents - Step 5')
@section('main_content')

@php
  $allAllowedExt = '.pdf,.png,.jpg,.jpeg,.webp,.svg,.gif,.bmp,.zip,.rar,.7z,.tar,.gz,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.csv,.rtf,.kml,.kmz,.xml,.dwg,.dxf,.mp4,.webm,.mkv,.avi,.mov';
  $uploadedDocs = $uploadedDocs ?? session('lease_draft.uploaded_docs', []);
  $docReqs = $draft['doc_requirements'] ?? session('lease_draft.doc_requirements', []);
  $uploadedCount = count($uploadedDocs);
  $customCount = count(array_filter($uploadedDocs, fn($d) => !empty($d['is_custom'])));
  $totalCount = 19 + $customCount;
  $percent = $totalCount > 0 ? min(100, round(($uploadedCount / $totalCount) * 100)) : 0;
@endphp

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step5-page">
      <div class="wizard-wrap">
        @if(!empty($isSubmittedDossier))
        <div class="alert alert-info d-flex align-items-center justify-content-between mb-3 shadow-sm rounded-3 py-2 px-3" style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af;">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-folder-check fs-4 text-primary"></i>
            <div>
              <strong>Document Vault &mdash; Application #{{ $draft['application_no'] ?? 'LA-Dossier' }}</strong>
              <div class="small text-muted">Active dossier mode: Any statutory documents or survey plans uploaded here are directly synchronized with your official submitted application.</div>
            </div>
          </div>
          <a href="{{ route('viewapplication', ['id' => $draft['application_id']]) }}" class="btn btn-navy btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Return to Dossier
          </a>
        </div>
        @else
        <div class="step-progress">
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
          <div class="sp-step active"><div class="circ">5</div><div class="sp-label">Documents</div></div>
          <div class="sp-step"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
          <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
          <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
        </div>
        @endif

        <div class="card s5-main-card wizard-card">
          @if(!empty($isSubmittedDossier))
            <div class="s5-eyebrow text-uppercase text-primary fw-bold">Active Application Dossier</div>
            <h4 class="s5-title">Statutory Documents &amp; Digital Plans Vault</h4>
            <div class="s5-subtitle">Upload missing documents or replace files for Application <b>#{{ $draft['application_no'] ?? '' }}</b>.</div>
          @else
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 s5-header-block mb-3">
              <div>
                <div class="s5-eyebrow">STEP 5 OF 8 &bull; REGULATORY REPOSITORY</div>
                <h4 class="s5-title">Regulatory Checklist &amp; Survey Plans</h4>
                <div class="s5-subtitle">Upload all required statutory documents, affidavits, and digital survey plans across the 3 folders.</div>
              </div>
              <div class="s5-time-tag">
                <i class="bi bi-clock-history me-1"></i>Est. ~5-8 mins
              </div>
            </div>
          @endif

          <!-- Overall Progress Header -->
          <div class="s5-summary-banner mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
              <div class="d-flex align-items-center gap-2">
                <span class="s5-summary-badge">
                  <i class="bi bi-folder-check me-1"></i>OVERALL COMPLETION
                </span>
                <span class="s5-summary-label">Mandatory Dossier Checklist Progress</span>
              </div>
              <div class="d-flex align-items-baseline gap-1">
                <span class="s5-counter-text text-primary" id="counter_text">{{ $uploadedCount }} / {{ $totalCount }}</span>
                <span class="s5-counter-sub">uploaded</span>
              </div>
            </div>
            <div class="progress s5-summary-progress">
              <div class="progress-bar s5-summary-bar" id="doc_progress_bar" role="progressbar" style="width:{{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>

        <!-- Interactive Dropzone -->
        <div class="dropzone" id="dropzone_box" style="cursor: pointer; display:none; border: 2px dashed #93c5fd; background: #f8fbff; transition: all 0.2s;">
          <i class="bi bi-cloud-arrow-up" style="font-size: 2.2rem; color: #2563eb;"></i>
          <div class="dz-title" style="font-weight: 700; color: #1e3a8a;">Drag &amp; drop files here, or click to browse</div>
          <div class="dz-sub text-muted">Upload authentic PDF, Images, Videos, ZIP Archives, Word, Excel, PPT, or KML files from your computer</div>
          <div class="mt-3">
            <button type="button" class="btn btn-sm btn-navy px-3" id="btn_select_files">
              <i class="bi bi-folder2-open me-1"></i> Browse Files to Upload
            </button>
          </div>
          <input type="file" id="real_file_input" multiple style="display:none;" accept="{{ $allAllowedExt }}">
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
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4 s5-folder-heading-wrap">
          <div class="s5-folder-heading">
            <i class="bi bi-folder2-open text-primary me-2"></i>
            <span>1. Documents folder</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm s5-btn-add-doc btn-open-lease-add-doc"
                    data-folder-id="7" data-folder-name="1. Documents folder">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <span class="badge s5-folder-pill" id="badge_folder_7">Folder ID: #7 &middot; <span class="folder-items-count" id="count_folder_7">{{ $f7Total }}</span> Items</span>
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
              $isItemMandatory = isset($docReqs[$item['id']]) ? ($docReqs[$item['id']] === 'mandatory') : $item['mandatory'];
              $fileExt = strtolower(pathinfo($doc['file_name'] ?? ($doc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $isZip = in_array($fileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="7" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}" data-mandatory="{{ $isItemMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $item['id'] }}" data-folder="7" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $isUp ? ($isZip ? 'background:#fef3c7;' : '') : '' }}">
                @if($isUp)
                  @if($isZip)
                    <i class="bi bi-file-earmark-zip text-warning"></i>
                  @else
                    <i class="bi bi-check-lg" style="color:var(--green);"></i>
                  @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isItemMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $item['id'] }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isItemMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isItemMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                @if($isUp)
                  <span class="badge-status uploaded">Uploaded</span>
                  @php
                    $docUrl = !empty($doc['file_path']) ? asset($doc['file_path']) : (!empty($doc['draft_path']) ? asset($doc['draft_path']) : '#');
                  @endphp
                  <a href="{{ $docUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $isZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $doc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $item['name'] }}" data-is-zip="{{ $isZip ? '1' : '0' }}">
                    <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                  <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this document"><i class="bi bi-trash3"></i></button>
                @else
                  <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.72rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Upload file"><i class="bi bi-upload me-1"></i> Upload</button>
                @endif
              </div>
            </div>
          @endforeach

          @foreach($f7Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isCustMandatory = isset($docReqs[$cKey]) ? ($docReqs[$cKey] === 'mandatory') : (!empty($cDoc['is_mandatory']));
              $cDocUrl = !empty($cDoc['file_path']) ? asset($cDoc['file_path']) : (!empty($cDoc['draft_path']) ? asset($cDoc['draft_path']) : '#');
              $cFileExt = strtolower(pathinfo($cDoc['file_name'] ?? ($cDoc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $cIsZip = in_array($cFileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="7" data-is-custom="1" data-mandatory="{{ $isCustMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $cKey }}" data-folder="7" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $cIsZip ? 'background:#fef3c7;' : 'background:var(--green-soft, #e6f4ea);' }}">
                @if($cIsZip)
                  <i class="bi bi-file-earmark-zip text-warning"></i>
                @else
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isCustMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $cKey }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isCustMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isCustMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                <span class="badge-status uploaded">Uploaded</span>
                <a href="{{ $cDocUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $cIsZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $cDoc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $cDoc['doc_name'] ?? 'Custom Document' }}" data-is-zip="{{ $cIsZip ? '1' : '0' }}">
                  <i class="bi {{ $cIsZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this custom document"><i class="bi bi-trash3"></i></button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- FOLDER 2: LEASE APPLICATION FOLDER -->
        @php
          $f8Custom = array_filter($uploadedDocs, fn($d) => !empty($d['is_custom']) && isset($d['folder_id']) && $d['folder_id'] == 8);
          $f8Total = 7 + count($f8Custom);
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4 s5-folder-heading-wrap">
          <div class="s5-folder-heading">
            <i class="bi bi-briefcase-fill me-2" style="color:#4f46e5;"></i>
            <span>2. Lease Application folder</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm s5-btn-add-doc btn-open-lease-add-doc"
                    data-folder-id="8" data-folder-name="2. Lease Application folder">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <span class="badge s5-folder-pill" id="badge_folder_8">Folder ID: #8 &middot; <span class="folder-items-count" id="count_folder_8">{{ $f8Total }}</span> Items</span>
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
              $isItemMandatory = isset($docReqs[$item['id']]) ? ($docReqs[$item['id']] === 'mandatory') : $item['mandatory'];
              $fileExt = strtolower(pathinfo($doc['file_name'] ?? ($doc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $isZip = in_array($fileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="8" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}" data-mandatory="{{ $isItemMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $item['id'] }}" data-folder="8" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $isUp ? ($isZip ? 'background:#fef3c7;' : '') : '' }}">
                @if($isUp)
                  @if($isZip)
                    <i class="bi bi-file-earmark-zip text-warning"></i>
                  @else
                    <i class="bi bi-check-lg" style="color:var(--green);"></i>
                  @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isItemMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $item['id'] }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isItemMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isItemMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                @if($isUp)
                  <span class="badge-status uploaded">Uploaded</span>
                  @php
                    $docUrl = !empty($doc['file_path']) ? asset($doc['file_path']) : (!empty($doc['draft_path']) ? asset($doc['draft_path']) : '#');
                  @endphp
                  <a href="{{ $docUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $isZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $doc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $item['name'] }}" data-is-zip="{{ $isZip ? '1' : '0' }}">
                    <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                  <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this document"><i class="bi bi-trash3"></i></button>
                @else
                  <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.72rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Upload file"><i class="bi bi-upload me-1"></i> Upload</button>
                @endif
              </div>
            </div>
          @endforeach

          @foreach($f8Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isCustMandatory = isset($docReqs[$cKey]) ? ($docReqs[$cKey] === 'mandatory') : (!empty($cDoc['is_mandatory']));
              $cDocUrl = !empty($cDoc['file_path']) ? asset($cDoc['file_path']) : (!empty($cDoc['draft_path']) ? asset($cDoc['draft_path']) : '#');
              $cFileExt = strtolower(pathinfo($cDoc['file_name'] ?? ($cDoc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $cIsZip = in_array($cFileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="8" data-is-custom="1" data-mandatory="{{ $isCustMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $cKey }}" data-folder="8" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $cIsZip ? 'background:#fef3c7;' : 'background:var(--green-soft, #e6f4ea);' }}">
                @if($cIsZip)
                  <i class="bi bi-file-earmark-zip text-warning"></i>
                @else
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isCustMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $cKey }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isCustMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isCustMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                <span class="badge-status uploaded">Uploaded</span>
                <a href="{{ $cDocUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $cIsZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $cDoc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $cDoc['doc_name'] ?? 'Custom Document' }}" data-is-zip="{{ $cIsZip ? '1' : '0' }}">
                  <i class="bi {{ $cIsZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this custom document"><i class="bi bi-trash3"></i></button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- FOLDER 3: PLAN FOLDER -->
        @php
          $f9Custom = array_filter($uploadedDocs, fn($d) => !empty($d['is_custom']) && isset($d['folder_id']) && $d['folder_id'] == 9);
          $f9Total = 3 + count($f9Custom);
        @endphp
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4 s5-folder-heading-wrap">
          <div class="s5-folder-heading">
            <i class="bi bi-geo-alt-fill me-2" style="color:#059669;"></i>
            <span>3. Plan folder</span>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm s5-btn-add-doc btn-open-lease-add-doc"
                    data-folder-id="9" data-folder-name="3. Plan folder">
              <i class="bi bi-plus-circle me-1"></i>Add Document
            </button>
            <span class="badge s5-folder-pill" id="badge_folder_9">Folder ID: #9 &middot; <span class="folder-items-count" id="count_folder_9">{{ $f9Total }}</span> Items</span>
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
              $isItemMandatory = isset($docReqs[$item['id']]) ? ($docReqs[$item['id']] === 'mandatory') : $item['mandatory'];
              $fileExt = strtolower(pathinfo($doc['file_name'] ?? ($doc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $isZip = in_array($fileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row {{ $isUp ? 'up' : '' }}" data-doc-item="{{ $item['id'] }}" data-folder="9" data-default-name="{{ $item['default'] }}" data-default-size="{{ $item['size'] }}" data-mandatory="{{ $isItemMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $item['id'] }}" data-folder="9" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $isUp ? ($isZip ? 'background:#fef3c7;' : '') : '' }}">
                @if($isUp)
                  @if($isZip)
                    <i class="bi bi-file-earmark-zip text-warning"></i>
                  @else
                    <i class="bi bi-check-lg" style="color:var(--green);"></i>
                  @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isItemMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $item['id'] }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isItemMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isItemMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                @if($isUp)
                  <span class="badge-status uploaded">Uploaded</span>
                  @php
                    $docUrl = !empty($doc['file_path']) ? asset($doc['file_path']) : (!empty($doc['draft_path']) ? asset($doc['draft_path']) : '#');
                  @endphp
                  <a href="{{ $docUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $isZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $doc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $item['name'] }}" data-is-zip="{{ $isZip ? '1' : '0' }}">
                    <i class="bi {{ $isZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                  <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this document"><i class="bi bi-trash3"></i></button>
                @else
                  <button type="button" class="btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item" style="font-size:.72rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Upload file"><i class="bi bi-upload me-1"></i> Upload</button>
                @endif
              </div>
            </div>
          @endforeach

          @foreach($f9Custom as $cKey => $cDoc)
            @php
              $dispSize = is_numeric($cDoc['file_size']) ? ($cDoc['file_size'] >= 1048576 ? (round($cDoc['file_size']/1048576, 1) . ' MB') : (round($cDoc['file_size']/1024, 0) . ' KB')) : ($cDoc['file_size'] ?? 'Attached');
              $isCustMandatory = isset($docReqs[$cKey]) ? ($docReqs[$cKey] === 'mandatory') : (!empty($cDoc['is_mandatory']));
              $cDocUrl = !empty($cDoc['file_path']) ? asset($cDoc['file_path']) : (!empty($cDoc['draft_path']) ? asset($cDoc['draft_path']) : '#');
              $cFileExt = strtolower(pathinfo($cDoc['file_name'] ?? ($cDoc['draft_path'] ?? ''), PATHINFO_EXTENSION));
              $cIsZip = in_array($cFileExt, ['zip', 'rar', '7z', 'tar', 'gz']);
            @endphp
            <div class="checklist-row up" data-doc-item="{{ $cKey }}" data-folder="9" data-is-custom="1" data-mandatory="{{ $isCustMandatory ? '1' : '0' }}">
              <input type="file" class="row-file-input d-none" data-doc-item="{{ $cKey }}" data-folder="9" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="{{ $cIsZip ? 'background:#fef3c7;' : 'background:var(--green-soft, #e6f4ea);' }}">
                @if($cIsZip)
                  <i class="bi bi-file-earmark-zip text-warning"></i>
                @else
                  <i class="bi bi-check-lg" style="color:var(--green);"></i>
                @endif
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select {{ $isCustMandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light' }} me-1"
                        data-doc-item="{{ $cKey }}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" {{ $isCustMandatory ? 'selected' : '' }}>Mandatory</option>
                  <option value="optional" {{ !$isCustMandatory ? 'selected' : '' }}>Optional</option>
                </select>
                <span class="badge-status uploaded">Uploaded</span>
                <a href="{{ $cDocUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="{{ $cIsZip ? 'Download / View ZIP Archive' : 'View Document' }}" data-file-name="{{ $cDoc['file_name'] ?? '' }}" data-file-size="{{ $dispSize }}" data-doc-name="{{ $cDoc['doc_name'] ?? 'Custom Document' }}" data-is-zip="{{ $cIsZip ? '1' : '0' }}">
                  <i class="bi {{ $cIsZip ? 'bi-file-earmark-zip' : 'bi-eye' }}"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this custom document"><i class="bi bi-trash3"></i></button>
              </div>
            </div>
          @endforeach
        </div>

        <div class="wizard-actions s5-actions d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
          @if(!empty($isSubmittedDossier))
            <a href="{{ route('viewapplication', ['id' => $draft['application_id'] ?? 1]) }}" class="s5-btn-back"><i class="bi bi-arrow-left me-1"></i> Cancel &amp; Back to Dossier</a>
            <div class="d-flex gap-2">
              <a href="{{ route('viewapplication', ['id' => $draft['application_id'] ?? 1]) }}" class="s5-btn-submit">
                <i class="bi bi-check-circle me-1"></i> Return to Application Dossier
              </a>
            </div>
          @else
            <a href="{{ route('step4') }}" class="s5-btn-back"><i class="bi bi-arrow-left me-1"></i> Back to Step 4</a>
            @can('application.create')
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('application.index') }}" class="s5-btn-draft">
                <i class="bi bi-floppy me-2"></i> Save Draft &amp; Exit
              </a>
              <a href="{{ route('step6') }}" class="s5-btn-submit" id="btn_continue_mimas">
                <span>Save &amp; Continue</span>
                <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
            @endcan
          @endif
        </div>
      </div>
    </div>
    </div>
  </div>
</div>

<!-- Modal: Add Custom Document -->
<div class="modal fade s5-custom-modal" id="modalAddCustomLeaseDoc" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
    <div class="modal-content border-0 shadow-lg s5-modal-content">
      <!-- MODAL HEADER -->
      <div class="modal-header s5-modal-header border-0">
        <div class="d-flex align-items-center gap-3">
          <div class="s5-modal-icon-badge">
            <i class="bi bi-file-earmark-plus-fill"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 1.15rem;">Add Custom Document</h5>
            <small class="text-white-50" style="font-size: 0.76rem;">Upload additional statutory proof, certificate, or survey dataset</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_add_lease_custom_doc">
        <div class="modal-body p-4 s5-modal-body">
          <!-- TARGET FOLDER BANNER -->
          <div class="mb-3">
            <label class="form-label s5-modal-label">Destination Folder</label>
            <div class="s5-modal-target-folder d-flex align-items-center justify-content-between p-2.5 rounded-3">
              <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                <div class="s5-folder-mini-icon">
                  <i class="bi bi-folder2-open text-primary"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                  <input type="text" class="form-control-plaintext s5-folder-name-input fw-bold py-0" id="lease_custom_folder_name" readonly value="1. Documents folder">
                </div>
              </div>
              <span class="badge s5-folder-target-badge flex-shrink-0">
                <i class="bi bi-pin-angle me-1"></i>Active Folder
              </span>
              <input type="hidden" id="lease_custom_folder_id" value="">
            </div>
          </div>

          <!-- DOCUMENT NAME / TITLE -->
          <div class="mb-3">
            <label for="lease_custom_doc_name" class="form-label s5-modal-label">
              Document Name / Title <span class="text-danger">*</span>
            </label>
            <div class="input-group s5-modal-input-group">
              <span class="input-group-text s5-modal-addon"><i class="bi bi-file-text"></i></span>
              <input type="text" class="form-control s5-modal-input" id="lease_custom_doc_name" placeholder="e.g. Property Tax Receipt, VAO Certificate, Family Tree, Site Photos" required>
            </div>
            <div class="s5-field-hint mt-1.5"><i class="bi bi-info-circle me-1"></i>Specify clear statutory title for this document.</div>
          </div>

          <!-- REQUIREMENT RULE -->
          <div class="mb-3">
            <label for="lease_custom_doc_status" class="form-label s5-modal-label">Requirement Rule</label>
            <div class="input-group s5-modal-input-group">
              <span class="input-group-text s5-modal-addon"><i class="bi bi-shield-check"></i></span>
              <select class="form-select s5-modal-input" id="lease_custom_doc_status">
                <option value="0" selected>Optional Document (Conditional verification)</option>
                <option value="1">Mandatory Document (Required for submission)</option>
              </select>
            </div>
            <div class="s5-field-hint mt-1.5"><i class="bi bi-shield-exclamation me-1"></i>Mandatory documents block progression until verified file is attached.</div>
          </div>

          <!-- FILE UPLOAD DROPZONE -->
          <div class="mb-1">
            <label for="lease_custom_doc_file" class="form-label s5-modal-label">
              File Upload <span class="text-danger">*</span>
            </label>
            <div class="s5-modal-upload-box" id="modal_upload_dropzone">
              <input type="file" class="s5-modal-file-input" id="lease_custom_doc_file" accept="{{ $allAllowedExt }}" required>
              <div class="s5-upload-content text-center py-3 px-3">
                <div class="s5-upload-icon-circle mb-2">
                  <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
                <div class="s5-upload-prompt fw-bold mb-1">
                  <span class="text-primary s5-browse-link">Choose file to upload</span> or drag &amp; drop here
                </div>
                <div class="s5-selected-file-name text-truncate fw-semibold d-none" id="lease_custom_selected_filename"></div>
                <div class="s5-upload-allowed-types mt-1">
                  Supports PDF, Images, Videos, Word, Excel, CAD, KML &amp; ZIP up to 100 MB
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MODAL FOOTER -->
        <div class="modal-footer s5-modal-footer px-4 py-3 d-flex justify-content-between align-items-center">
          <button type="button" class="btn s5-modal-btn-cancel" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1.5"></i>Cancel
          </button>
          <button type="submit" class="btn s5-modal-btn-submit" id="btn_submit_lease_custom_doc">
            <i class="bi bi-cloud-arrow-up-fill me-1.5"></i>Upload &amp; Add Document
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Custom ZIP / Archive Download Modal -->
<div class="modal fade" id="modalZipFilePreview" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%); color: #fff; padding: 1.5rem 1.5rem 1.25rem;">
        <div class="d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px);">
            <i class="bi bi-file-earmark-zip text-warning" style="font-size: 1.75rem;"></i>
          </div>
          <div>
            <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 1.1rem;">Compressed ZIP Archive</h5>
            <small class="text-white-50" style="font-size: 0.78rem;">Statutory Archive Package</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4" style="background: #f8fafc;">
        <div class="p-3 rounded-3 mb-3" style="background: #ffffff; border: 1px solid #e2e8f0;">
          <div class="d-flex align-items-start gap-2 mb-2">
            <i class="bi bi-info-circle-fill text-primary mt-1"></i>
            <div style="font-size: 0.82rem; color: #334155; line-height: 1.45;">
              Web browsers cannot preview compressed ZIP files directly. You can download this archive package to your PC to extract and view all enclosed documents and survey datasets.
            </div>
          </div>
          <hr style="margin: 0.75rem 0; opacity: 0.15;">
          <div class="d-flex flex-column gap-1" style="font-size: 0.8rem;">
            <div class="d-flex justify-content-between">
              <span class="text-muted">Document Slot:</span>
              <span class="fw-semibold text-dark text-end" id="zipModalDocName">Document Name</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Archive File:</span>
              <span class="fw-bold text-navy text-truncate ms-2" id="zipModalFileName" style="max-width: 250px;">package.zip</span>
            </div>
            <div class="d-flex justify-content-between">
              <span class="text-muted">Package Size:</span>
              <span class="badge bg-light text-dark border" id="zipModalFileSize">-- MB</span>
            </div>
          </div>
        </div>

        <div class="d-flex flex-column gap-2">
          <a href="#" id="zipModalDownloadBtn" download class="btn btn-navy w-100 py-2 d-flex align-items-center justify-content-center gap-2 shadow-sm" style="font-size: 0.9rem; font-weight: 600;">
            <i class="bi bi-cloud-arrow-down-fill fs-5"></i> Download ZIP to Your PC
          </a>
          <button type="button" class="btn btn-outline-secondary w-100 py-2" data-bs-dismiss="modal" style="font-size: 0.85rem;">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step5-page)
     ========================================================================== */
  #gtms-step5-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */
  #gtms-step5-page .step-progress {
    margin-bottom: 22px;
  }

  #gtms-step5-page .step-progress .sp-label {
    font-size: 0.72rem;
    font-weight: 700;
    margin-top: 6px;
    line-height: 1.2;
  }

  #gtms-step5-page .step-progress .circ {
    width: 32px;
    height: 32px;
    font-size: 0.80rem;
    line-height: 1;
  }

  /* MAIN CARD */
  #gtms-step5-page .s5-main-card {
    border-radius: 14px;
    padding: 26px 30px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
  }

  @media (max-width: 768px) {
    #gtms-step5-page .s5-main-card {
      padding: 20px 16px;
    }
  }

  #gtms-step5-page .s5-header-block {
    margin-bottom: 20px;
  }

  #gtms-step5-page .s5-eyebrow {
    color: var(--orange, #ea580c);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
    margin-bottom: 0;
  }

  #gtms-step5-page .s5-time-tag {
    font-size: 0.73rem;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    padding: 3px 8px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    line-height: 1.2;
  }

  #gtms-step5-page .s5-title {
    font-weight: 800;
    font-size: 1.25rem;
    color: #0f172a;
    line-height: 1.3;
    margin-top: 6px;
    margin-bottom: 5px;
  }

  #gtms-step5-page .s5-subtitle {
    font-size: 0.83rem;
    color: #64748b;
    line-height: 1.5;
    margin-bottom: 0;
  }

  /* SUMMARY READINESS BANNER */
  #gtms-step5-page .s5-summary-banner {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 20px;
  }

  #gtms-step5-page .s5-summary-badge {
    background: #0F1E4D;
    color: #ffffff;
    font-size: 0.70rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    letter-spacing: 0.02em;
    text-transform: uppercase;
  }

  #gtms-step5-page .s5-summary-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #334155;
  }

  #gtms-step5-page .s5-counter-text {
    font-size: 1.25rem;
    font-weight: 800;
    color: #0F1E4D;
    line-height: 1;
  }

  #gtms-step5-page .s5-counter-sub {
    font-size: 0.72rem;
    color: #64748b;
    font-weight: 600;
  }

  #gtms-step5-page .s5-summary-progress {
    height: 8px;
    background-color: #e2e8f0;
    border-radius: 999px;
    overflow: hidden;
  }

  #gtms-step5-page .s5-summary-bar {
    background: linear-gradient(90deg, #0F1E4D 0%, #2563eb 100%);
    border-radius: 999px;
    transition: width 0.4s ease;
  }

  /* FOLDER HEADINGS */
  #gtms-step5-page .s5-folder-heading-wrap {
    margin-top: 24px;
    margin-bottom: 10px;
    padding-bottom: 6px;
    border-bottom: 1px solid #f1f5f9;
  }

  #gtms-step5-page .s5-folder-heading {
    font-size: 0.82rem;
    font-weight: 700;
    color: #0F1E4D;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: flex;
    align-items: center;
  }

  #gtms-step5-page .s5-btn-add-doc {
    font-size: 0.74rem;
    font-weight: 600;
    color: #0F1E4D;
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    border-radius: 6px;
    padding: 3px 10px;
    transition: all 0.2s ease;
  }

  #gtms-step5-page .s5-btn-add-doc:hover {
    background: #f1f5f9;
    border-color: #0F1E4D;
    color: #0F1E4D;
  }

  #gtms-step5-page .s5-folder-pill {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 6px;
  }

  /* CHECKLIST BOX & ROWS */
  #gtms-step5-page .folder-checklist-box {
    margin-bottom: 12px;
  }

  #gtms-step5-page .checklist-row {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    padding: 11px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  #gtms-step5-page .checklist-row:hover {
    border-color: #cbd5e1;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
  }

  #gtms-step5-page .checklist-row.row-dragover {
    border: 2px dashed #2563eb !important;
    background: #eff6ff !important;
  }

  #gtms-step5-page .checklist-row.up {
    border-color: #cbd5e1;
  }

  #gtms-step5-page .ci-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
    margin-right: 12px;
    background: #f8fafc;
    color: #64748b;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
  }

  #gtms-step5-page .ci-name {
    font-size: 0.88rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.3;
    margin-bottom: 2px;
  }

  #gtms-step5-page .ci-meta {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.3;
  }

  /* REQUIREMENT DROPDOWN */
  #gtms-step5-page .doc-req-select {
    font-size: 0.73rem;
    font-weight: 600;
    height: 28px;
    border-radius: 6px;
    padding: 2px 22px 2px 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: none !important;
    outline: none !important;
  }

  #gtms-step5-page .doc-req-select:focus {
    box-shadow: 0 0 0 2px rgba(15, 30, 77, 0.15) !important;
  }

  /* ROW ACTION BUTTONS */
  #gtms-step5-page .checklist-row .btn-upload-item {
    height: 28px;
    font-size: 0.74rem;
    font-weight: 600;
    padding: 0 10px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 1.5px solid #cbd5e1;
    color: #0F1E4D;
    background: #ffffff;
    transition: all 0.15s ease-in-out;
  }

  #gtms-step5-page .checklist-row .btn-upload-item:hover {
    background: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff !important;
  }

  #gtms-step5-page .checklist-row .btn-upload-item:hover * {
    color: #ffffff !important;
  }

  /* RESUME BUTTON (Paused Chunked Upload) - Light Mode */
  #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item,
  #gtms-step5-page .checklist-row .btn-upload-item.btn-warning {
    background-color: #fffbeb !important;
    border: 1.5px solid #f59e0b !important;
    color: #b45309 !important;
    font-weight: 600 !important;
  }

  #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item *,
  #gtms-step5-page .checklist-row .btn-upload-item.btn-warning * {
    color: #b45309 !important;
  }

  #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item:hover,
  #gtms-step5-page .checklist-row .btn-upload-item.btn-warning:hover {
    background-color: #0F1E4D !important;
    border-color: #0F1E4D !important;
    color: #ffffff !important;
  }

  #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item:hover *,
  #gtms-step5-page .checklist-row .btn-upload-item.btn-warning:hover * {
    color: #ffffff !important;
  }

  /* Discard Button (Paused Chunked Upload) - Light Mode */
  #gtms-step5-page .checklist-row .btn-discard-paused-item {
    height: 28px;
    width: 30px;
    font-size: 0.80rem;
    padding: 0;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #fecdd3;
    color: #e11d48;
    background: #fff1f2;
    transition: all 0.15s ease-in-out;
  }

  #gtms-step5-page .checklist-row .btn-discard-paused-item:hover {
    background: #e11d48;
    border-color: #e11d48;
    color: #ffffff !important;
  }

  #gtms-step5-page .checklist-row .btn-discard-paused-item:hover * {
    color: #ffffff !important;
  }

  #gtms-step5-page .checklist-row .btn-view-item {
    height: 28px;
    width: 30px;
    font-size: 0.80rem;
    padding: 0;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #bfdbfe;
    color: #1d4ed8;
    background: #eff6ff;
    transition: all 0.15s ease-in-out;
  }

  #gtms-step5-page .checklist-row .btn-view-item:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
    color: #ffffff;
  }

  #gtms-step5-page .checklist-row .btn-remove-item {
    height: 28px;
    width: 30px;
    font-size: 0.80rem;
    padding: 0;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid #fecaca;
    color: #dc2626;
    background: #fef2f2;
    transition: all 0.15s ease-in-out;
  }

  #gtms-step5-page .checklist-row .btn-remove-item:hover {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff;
  }

  #gtms-step5-page .badge-status.uploaded {
    height: 28px;
    font-size: 0.70rem;
    font-weight: 700;
    border-radius: 6px;
    padding: 0 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }

  /* FOOTER ACTIONS */
  #gtms-step5-page .s5-actions {
    border-top: 1px solid #e2e8f0;
    padding-top: 18px;
    margin-top: 24px;
  }

  #gtms-step5-page .s5-btn-back {
    font-size: 0.84rem;
    font-weight: 600;
    color: #0F1E4D;
    border: 1.5px solid #cbd5e1;
    background: #ffffff;
    border-radius: 8px;
    padding: 9px 18px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
  }

  #gtms-step5-page .s5-btn-back:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0F1E4D;
  }

  #gtms-step5-page .s5-btn-draft {
    font-size: 0.84rem;
    font-weight: 600;
    color: #0F1E4D;
    border: 1.5px solid #0F1E4D;
    background: transparent;
    border-radius: 8px;
    padding: 9px 18px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  #gtms-step5-page .s5-btn-draft:hover {
    background: #0F1E4D;
    color: #ffffff;
  }

  #gtms-step5-page .s5-btn-submit {
    background-color: #0F1E4D;
    border: 1.5px solid #0F1E4D;
    color: #ffffff;
    font-size: 0.86rem;
    font-weight: 700;
    border-radius: 8px;
    padding: 9px 24px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.20);
  }

  #gtms-step5-page .s5-btn-submit:hover {
    background-color: #081233;
    border-color: #081233;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.25);
  }

  /* ==========================================================================
     DARK MODE STYLES (Strictly Scoped to body[data-theme-version="dark"])
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step5-page .s5-main-card {
    background: #1e1e1e !important;
    border: 1px solid #2e3440 !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-title {
    color: #f8fafc !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-subtitle {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-time-tag {
    background: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  /* SUMMARY BANNER - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .s5-summary-banner {
    background: #18191a !important;
    border-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-summary-badge {
    background: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-summary-label {
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-counter-text {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-counter-sub {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-summary-progress {
    background-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-summary-bar {
    background: linear-gradient(90deg, #1d4ed8 0%, #3b82f6 100%) !important;
  }

  /* FOLDER HEADINGS - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .s5-folder-heading-wrap {
    border-bottom-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-folder-heading {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-add-doc {
    background: #18191a !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-add-doc:hover {
    background: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-folder-pill {
    background: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  /* CHECKLIST ROWS - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row {
    background: #18191a !important;
    border-color: #2b2d30 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25) !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row:hover {
    border-color: #3e4451 !important;
    background: #1c1d1f !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row.up {
    border-color: #2b2d30 !important;
    background: #18191a !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row.row-dragover {
    border: 2px dashed #3b82f6 !important;
    background: #13233a !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .ci-icon {
    background: #25282a !important;
    color: #94a3b8 !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .ci-name {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .ci-meta {
    color: #94a3b8 !important;
  }

  /* REQUIREMENT SELECT - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .doc-req-select.border-danger-subtle {
    background-color: #450a0a !important;
    color: #fca5a5 !important;
    border-color: #7f1d1d !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .doc-req-select.border-secondary-subtle {
    background-color: #25282a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  /* ACTION BUTTONS - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item {
    background: #18191a !important;
    border-color: #3b82f6 !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item:hover * {
    color: #ffffff !important;
  }

  /* Resume Button (Paused Chunked Upload) - Dark Mode */
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item,
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-warning {
    background-color: rgba(245, 158, 11, 0.15) !important;
    border-color: rgba(245, 158, 11, 0.4) !important;
    color: #fbbf24 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item *,
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-warning * {
    color: #fbbf24 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item:hover,
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-warning:hover {
    background-color: #f59e0b !important;
    border-color: #f59e0b !important;
    color: #0f172a !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-resume-item:hover *,
  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-upload-item.btn-warning:hover * {
    color: #0f172a !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-discard-paused-item {
    background: rgba(244, 63, 94, 0.12) !important;
    border-color: rgba(244, 63, 94, 0.35) !important;
    color: #fb7185 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-discard-paused-item:hover {
    background: #e11d48 !important;
    border-color: #e11d48 !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-discard-paused-item:hover * {
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-view-item {
    background: #172554 !important;
    border-color: #1e3a8a !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-view-item:hover {
    background: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-remove-item {
    background: #450a0a !important;
    border-color: #7f1d1d !important;
    color: #f87171 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .checklist-row .btn-remove-item:hover {
    background: #dc2626 !important;
    border-color: #dc2626 !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .badge-status.uploaded {
    background: #062c20 !important;
    color: #34d399 !important;
    border-color: #059669 !important;
  }

  /* ACTIONS - DARK */
  body[data-theme-version="dark"] #gtms-step5-page .s5-actions {
    border-top-color: #2b2d30 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-back {
    background-color: #18191a !important;
    color: #cbd5e1 !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-back:hover {
    background-color: #25282a !important;
    color: #ffffff !important;
    border-color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-draft {
    color: #60a5fa !important;
    border-color: #3b82f6 !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-draft:hover {
    background-color: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-submit {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    box-shadow: 0 2px 10px rgba(37, 99, 235, 0.35) !important;
  }

  body[data-theme-version="dark"] #gtms-step5-page .s5-btn-submit:hover {
    background-color: #1d4ed8 !important;
    border-color: #1d4ed8 !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.45) !important;
  }

  /* MODALS DARK MODE PARITY */
  body[data-theme-version="dark"] .modal-content {
    background-color: #1e1e1e !important;
    border: 1px solid #2e3440 !important;
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .modal-header {
    border-bottom-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] .modal-body {
    background-color: #18191a !important;
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .modal-footer {
    background-color: #18191a !important;
    border-top-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] .modal-body .form-control,
  body[data-theme-version="dark"] .modal-body .form-select {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .modal-body .form-label {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .modal-body .form-text {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] .modal-body .input-group-text {
    background-color: #25282a !important;
    border-color: #3b4252 !important;
    color: #94a3b8 !important;
  }

  /* ==========================================================================
     CUSTOM DOCUMENT MODAL STYLING (.s5-custom-modal)
     ========================================================================== */
  .s5-custom-modal .s5-modal-content {
    border-radius: 16px !important;
    overflow: hidden;
    border: 1px solid rgba(15, 30, 77, 0.12) !important;
    box-shadow: 0 20px 45px -12px rgba(15, 30, 77, 0.28) !important;
  }

  .s5-custom-modal .s5-modal-header {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%) !important;
    padding: 1.25rem 1.5rem !important;
    border-bottom: none !important;
  }

  .s5-custom-modal .s5-modal-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #60a5fa;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.2);
    flex-shrink: 0;
  }

  .s5-custom-modal .s5-modal-body {
    background-color: #ffffff !important;
    padding: 1.5rem 1.5rem 1.25rem !important;
  }

  .s5-custom-modal .s5-modal-label {
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    color: #334155 !important;
    margin-bottom: 0.35rem !important;
    letter-spacing: 0.01em;
  }

  .s5-custom-modal .s5-modal-target-folder {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0.65rem 0.85rem;
    transition: all 0.2s ease;
  }

  .s5-custom-modal .s5-folder-mini-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #e0e7ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }

  .s5-custom-modal .s5-folder-name-input {
    font-weight: 700 !important;
    font-size: 0.9rem !important;
    color: #1e293b !important;
    border: none !important;
    background: transparent !important;
    outline: none !important;
    padding: 0 !important;
    box-shadow: none !important;
  }

  .s5-custom-modal .s5-folder-target-badge {
    background: #e0e7ff !important;
    color: #3730a3 !important;
    font-weight: 600 !important;
    font-size: 0.72rem !important;
    padding: 5px 9px !important;
    border-radius: 6px !important;
    letter-spacing: 0.02em;
  }

  .s5-custom-modal .s5-modal-input-group {
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }

  .s5-custom-modal .s5-modal-addon {
    background: #f8fafc !important;
    border: 1.5px solid #cbd5e1 !important;
    border-right: none !important;
    color: #64748b !important;
    font-size: 0.95rem;
    padding: 0.55rem 0.85rem;
    border-top-left-radius: 8px !important;
    border-bottom-left-radius: 8px !important;
  }

  .s5-custom-modal .s5-modal-input {
    border: 1.5px solid #cbd5e1 !important;
    font-size: 0.88rem !important;
    color: #1e293b !important;
    padding: 0.55rem 0.85rem !important;
    border-top-right-radius: 8px !important;
    border-bottom-right-radius: 8px !important;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  .s5-custom-modal .s5-modal-input:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
  }

  .s5-custom-modal .s5-field-hint {
    font-size: 0.74rem;
    color: #64748b;
    line-height: 1.35;
  }

  .s5-custom-modal .s5-modal-upload-box {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    position: relative;
    cursor: pointer;
    transition: all 0.2s ease;
    overflow: hidden;
  }

  .s5-custom-modal .s5-modal-upload-box:hover {
    border-color: #2563eb;
    background: #eff6ff;
  }

  .s5-custom-modal .s5-modal-file-input {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 5;
  }

  .s5-custom-modal .s5-upload-icon-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
  }

  .s5-custom-modal .s5-upload-prompt {
    font-size: 0.86rem;
    color: #334155;
  }

  .s5-custom-modal .s5-browse-link {
    color: #2563eb;
    text-decoration: underline;
    text-underline-offset: 3px;
    font-weight: 600;
  }

  .s5-custom-modal .s5-selected-file-name {
    font-size: 0.8rem;
    color: #047857;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    padding: 4px 12px;
    border-radius: 6px;
    display: inline-block;
    max-width: 90%;
  }

  .s5-custom-modal .s5-upload-allowed-types {
    font-size: 0.72rem;
    color: #94a3b8;
  }

  .s5-custom-modal .s5-modal-footer {
    background: #f8fafc !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 1rem 1.5rem !important;
  }

  .s5-custom-modal .s5-modal-btn-cancel {
    background: #ffffff !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 0.86rem !important;
    padding: 0.55rem 1.25rem !important;
    border-radius: 8px !important;
    transition: all 0.15s ease;
  }

  .s5-custom-modal .s5-modal-btn-cancel:hover {
    background: #f1f5f9 !important;
    color: #1e293b !important;
    border-color: #94a3b8 !important;
  }

  .s5-custom-modal .s5-modal-btn-submit {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
    border: none !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 0.86rem !important;
    padding: 0.55rem 1.35rem !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
    transition: all 0.15s ease;
  }

  .s5-custom-modal .s5-modal-btn-submit:hover {
    background: linear-gradient(135deg, #172554 0%, #1d4ed8 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35) !important;
  }

  /* --------------------------------------------------------------------------
     DARK MODE PARITY FOR .s5-custom-modal
     -------------------------------------------------------------------------- */
  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-content {
    background-color: #1a1b1d !important;
    border: 1px solid #2e3440 !important;
    box-shadow: 0 20px 45px -12px rgba(0, 0, 0, 0.7) !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-body {
    background-color: #18191a !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-label {
    color: #e2e8f0 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-target-folder {
    background: #25282a !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-folder-mini-icon {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #818cf8 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-folder-name-input {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-folder-target-badge {
    background: rgba(99, 102, 241, 0.2) !important;
    color: #a5b4fc !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-addon {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-input {
    background-color: #161717 !important;
    border-color: #3b4252 !important;
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-input:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-field-hint {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-upload-box {
    border-color: #3b4252 !important;
    background: #161717 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-upload-box:hover {
    border-color: #3b82f6 !important;
    background: rgba(59, 130, 246, 0.08) !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-upload-icon-circle {
    background: rgba(37, 99, 235, 0.2) !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-upload-prompt {
    color: #e2e8f0 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-browse-link {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-selected-file-name {
    color: #34d399 !important;
    background: rgba(16, 185, 129, 0.15) !important;
    border-color: rgba(52, 211, 153, 0.3) !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-upload-allowed-types {
    color: #64748b !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-footer {
    background: #18191a !important;
    border-top-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-btn-cancel {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] .s5-custom-modal .s5-modal-btn-cancel:hover {
    background: #32373d !important;
    color: #ffffff !important;
    border-color: #64748b !important;
  }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Purge legacy chunk tokens from old chunk sizes
  try {
    for (let i = localStorage.length - 1; i >= 0; i--) {
      const k = localStorage.key(i);
      if (k && k.startsWith('gtms_chk_') && !k.startsWith('gtms_chk_v2_')) {
        localStorage.removeItem(k);
      }
    }
  } catch (e) {}

  // Detect paused/unfinished chunked upload sessions from previous page loads and render Resume indicator
  document.querySelectorAll('.checklist-row:not(.up)').forEach(async function (row) {
    const docItem = row.getAttribute('data-doc-item');
    if (!docItem) return;

    let foundKey = null;
    let foundToken = null;
    for (let i = 0; i < localStorage.length; i++) {
      const k = localStorage.key(i);
      if (k && k.startsWith('gtms_chk_v2_' + docItem + '_')) {
        foundKey = k;
        foundToken = localStorage.getItem(k);
        break;
      }
    }

    if (foundToken) {
      try {
        const statusRes = await fetch(`/api/upload/chunk/status?upload_token=${foundToken}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (statusRes.ok) {
          const statusData = await statusRes.json();
          if (statusData.success && (statusData.status === 'uploading' || statusData.status === 'pending')) {
            const uploadedChunks = statusData.uploaded_chunks_count || 0;
            const totalChunks = statusData.total_chunks || 1;
            const chunkSize = statusData.chunk_size || (5 * 1024 * 1024);
            const uploadedMB = ((uploadedChunks * chunkSize) / 1048576).toFixed(1);
            const totalMB = (statusData.total_size / 1048576).toFixed(1);
            const pct = totalChunks > 0 ? Math.min(99, Math.round((uploadedChunks / totalChunks) * 100)) : 0;

            const metaDiv = row.querySelector('.ci-meta');
            if (metaDiv) {
              const pauseMsg = pct > 0
                ? `<span class="text-warning fw-semibold"><i class="bi bi-pause-circle me-1"></i> Upload paused at ${pct}% (${uploadedMB} / ${totalMB} MB)</span>`
                : `<span class="text-warning fw-semibold"><i class="bi bi-pause-circle me-1"></i> Upload paused at 0% (0.0 / ${totalMB} MB)</span>`;
              metaDiv.innerHTML = pauseMsg;
            }

            const uploadBtn = row.querySelector('.btn-upload-item');
            if (uploadBtn) {
              uploadBtn.className = 'btn btn-sm btn-warning py-0 px-2 btn-upload-item btn-resume-item fw-semibold';
              uploadBtn.innerHTML = `<i class="bi bi-play-circle-fill me-1"></i> Resume (${pct}%)`;
              uploadBtn.title = `Resume upload for ${statusData.file_name} from ${pct}%`;

              let discardBtn = row.querySelector('.btn-discard-paused-item');
              if (!discardBtn) {
                discardBtn = document.createElement('button');
                discardBtn.type = 'button';
                discardBtn.className = 'btn btn-sm btn-outline-danger py-0 px-2 btn-discard-paused-item ms-1';
                discardBtn.style.fontSize = '.78rem';
                discardBtn.style.height = '26px';
                discardBtn.style.display = 'inline-flex';
                discardBtn.style.alignItems = 'center';
                discardBtn.style.justifyContent = 'center';
                discardBtn.title = 'Discard paused upload and delete temporary chunks';
                discardBtn.setAttribute('data-upload-token', foundToken);
                discardBtn.setAttribute('data-storage-key', foundKey);
                discardBtn.innerHTML = '<i class="bi bi-trash3"></i>';
                uploadBtn.parentNode.appendChild(discardBtn);
              }
            }
          } else if (statusData.status === 'completed' || !statusData.success) {
            localStorage.removeItem(foundKey);
          }
        } else {
          localStorage.removeItem(foundKey);
        }
      } catch (e) {
        localStorage.removeItem(foundKey);
      }
    }
  });

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
  const btnSelectFiles = document.getElementById('btn_select_files');

  const archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];

  function isArchiveFile(urlOrName) {
    if (!urlOrName) return false;
    const clean = urlOrName.split('?')[0].toLowerCase();
    const ext = clean.substring(clean.lastIndexOf('.') + 1);
    return archiveExtensions.includes(ext);
  }

  function markRowUploaded(row, fileName, fileSize, fileUrl) {
    row.classList.add('up');
    const isZip = isArchiveFile(fileName);

    const existingProg = row.querySelector('.upload-progress-wrap');
    if (existingProg) existingProg.remove();

    const iconDiv = row.querySelector('.ci-icon');
    if (iconDiv) {
      iconDiv.innerHTML = isZip
        ? '<i class="bi bi-file-earmark-zip text-warning"></i>'
        : '<i class="bi bi-check-lg" style="color:var(--green);"></i>';
      iconDiv.style.background = isZip ? '#fef3c7' : 'var(--green-soft, #e6f4ea)';
    }

    const metaDiv = row.querySelector('.ci-meta');
    if (metaDiv) {
      metaDiv.innerHTML = `${fileName} &middot; ${fileSize}`;
    }

    let badge = row.querySelector('.badge-status');
    if (badge) {
      badge.className = 'badge-status uploaded';
      badge.textContent = 'Uploaded';
    } else {
      badge = document.createElement('span');
      badge.className = 'badge-status uploaded';
      badge.textContent = 'Uploaded';
      const actionCol = row.querySelector('.d-flex.align-items-center.gap-1');
      if (actionCol) actionCol.appendChild(badge);
    }

    const uploadBtn = row.querySelector('.btn-upload-item');
    if (fileUrl) {
      let viewBtn = row.querySelector('.btn-view-item');
      if (!viewBtn) {
        viewBtn = document.createElement('a');
        viewBtn.className = 'btn btn-sm btn-outline-info py-0 px-2 btn-view-item';
        viewBtn.style.fontSize = '.78rem';
        viewBtn.style.height = '26px';
        viewBtn.style.display = 'inline-flex';
        viewBtn.style.alignItems = 'center';
        viewBtn.style.justifyContent = 'center';
        viewBtn.target = '_blank';
        viewBtn.rel = 'noopener noreferrer';
        if (uploadBtn) {
          uploadBtn.parentNode.insertBefore(viewBtn, uploadBtn);
        }
      }
      viewBtn.href = fileUrl;
      viewBtn.title = isZip ? 'Download / View ZIP Archive' : 'View Document';
      viewBtn.setAttribute('data-file-name', fileName);
      viewBtn.setAttribute('data-file-size', fileSize);
      viewBtn.setAttribute('data-doc-name', row.querySelector('.ci-name')?.textContent?.trim() || 'Attached Document');
      viewBtn.setAttribute('data-is-zip', isZip ? '1' : '0');
      viewBtn.innerHTML = isZip ? '<i class="bi bi-file-earmark-zip"></i>' : '<i class="bi bi-eye"></i>';
    }

    if (uploadBtn) {
      uploadBtn.className = 'btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item';
      uploadBtn.style.fontSize = '.78rem';
      uploadBtn.style.height = '26px';
      uploadBtn.style.display = 'inline-flex';
      uploadBtn.style.alignItems = 'center';
      uploadBtn.style.justifyContent = 'center';
      uploadBtn.disabled = false;
      uploadBtn.title = 'Change / Re-upload file';
      uploadBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i>';

      // Ensure any duplicate remove buttons are cleaned up
      const existingRemoveBtns = row.querySelectorAll('.btn-remove-item');
      for (let i = 1; i < existingRemoveBtns.length; i++) {
        existingRemoveBtns[i].remove();
      }

      let removeBtn = existingRemoveBtns.length > 0 ? existingRemoveBtns[0] : null;
      if (!removeBtn) {
        removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item';
        removeBtn.style.fontSize = '.78rem';
        removeBtn.style.height = '26px';
        removeBtn.style.display = 'inline-flex';
        removeBtn.style.alignItems = 'center';
        removeBtn.style.justifyContent = 'center';
        removeBtn.title = 'Remove this document';
        removeBtn.innerHTML = '<i class="bi bi-trash3"></i>';
        uploadBtn.parentNode.insertBefore(removeBtn, uploadBtn.nextSibling);
      } else {
        removeBtn.className = 'btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item';
        removeBtn.style.fontSize = '.78rem';
        removeBtn.style.height = '26px';
        removeBtn.style.display = 'inline-flex';
        removeBtn.style.alignItems = 'center';
        removeBtn.style.justifyContent = 'center';
        removeBtn.title = 'Remove this document';
        removeBtn.innerHTML = '<i class="bi bi-trash3"></i>';
      }
      row.querySelectorAll('.btn-discard-paused-item').forEach(btn => btn.remove());
    }
  }

  function markRowRemoved(row, isCustom) {
    if (isCustom) {
      row.style.transition = 'all 0.3s ease';
      row.style.opacity = '0';
      row.style.transform = 'translateX(20px)';
      setTimeout(() => {
        const folderId = row.getAttribute('data-folder');
        row.remove();
        const countSpan = document.getElementById('count_folder_' + folderId);
        if (countSpan) {
          countSpan.textContent = Math.max(0, (parseInt(countSpan.textContent) || 1) - 1);
        }
        updateOverallProgress();
      }, 300);
      return;
    }

    row.classList.remove('up');

    const prog = row.querySelector('.upload-progress-wrap');
    if (prog) prog.remove();

    const iconDiv = row.querySelector('.ci-icon');
    if (iconDiv) {
      iconDiv.innerHTML = '<i class="bi bi-file-earmark"></i>';
      iconDiv.style.background = '';
    }

    const metaDiv = row.querySelector('.ci-meta');
    if (metaDiv) {
      metaDiv.innerHTML = 'Not uploaded';
    }

    const badge = row.querySelector('.badge-status');
    if (badge) badge.remove();

    const viewBtn = row.querySelector('.btn-view-item');
    if (viewBtn) viewBtn.remove();

    row.querySelectorAll('.btn-remove-item').forEach(btn => btn.remove());
    row.querySelectorAll('.btn-discard-paused-item').forEach(btn => btn.remove());

    const uploadBtn = row.querySelector('.btn-upload-item');
    if (uploadBtn) {
      uploadBtn.className = 'btn btn-sm btn-outline-navy py-0 px-2 btn-upload-item';
      uploadBtn.style.fontSize = '.72rem';
      uploadBtn.style.height = '26px';
      uploadBtn.style.display = 'inline-flex';
      uploadBtn.style.alignItems = 'center';
      uploadBtn.style.justifyContent = 'center';
      uploadBtn.disabled = false;
      uploadBtn.title = 'Upload file';
      uploadBtn.innerHTML = '<i class="bi bi-upload me-1"></i> Upload';
    }

    const fileInput = row.querySelector('.row-file-input');
    if (fileInput) {
      fileInput.value = '';
    }

    updateOverallProgress();
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

  // Global Sequential Upload Queue Manager
  let isUploadActive = false;
  const globalUploadQueue = [];

  function setRowQueuedUI(row, file) {
    const btn = row.querySelector('.btn-upload-item');
    const flexGrowDiv = row.querySelector('.flex-grow-1');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-grow spinner-grow-sm me-1 text-warning" role="status" style="width:10px;height:10px;"></span> Queued';
    }
    let progressWrap = row.querySelector('.upload-progress-wrap');
    if (!progressWrap && flexGrowDiv) {
      progressWrap = document.createElement('div');
      progressWrap.className = 'upload-progress-wrap mt-1';
      flexGrowDiv.appendChild(progressWrap);
    }
    if (progressWrap) {
      const fileSizeMB = (file.size / 1048576).toFixed(1);
      progressWrap.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px;">
          <span class="text-warning-emphasis fw-semibold">
            <i class="bi bi-hourglass-split me-1 text-warning"></i> In Queue: Waiting for previous upload to complete... (${fileSizeMB} MB)
          </span>
          <span class="badge bg-warning-subtle text-warning border border-warning-subtle font-monospace">Queued</span>
        </div>
        <div class="progress" style="height: 6px; border-radius: 3px; background: #fef3c7;">
          <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 100%;"></div>
        </div>
      `;
    }
  }

  function uploadFileToServer(row, file) {
    return new Promise((resolve, reject) => {
      const existingIdx = globalUploadQueue.findIndex(item => item.row === row);
      if (existingIdx !== -1) {
        globalUploadQueue[existingIdx].file = file;
        globalUploadQueue[existingIdx].resolve = resolve;
        globalUploadQueue[existingIdx].reject = reject;
        setRowQueuedUI(row, file);
        return;
      }

      if (!isUploadActive) {
        isUploadActive = true;
        executeUpload(row, file)
          .then(resolve)
          .catch(reject)
          .finally(() => processNextUploadInQueue());
      } else {
        setRowQueuedUI(row, file);
        globalUploadQueue.push({ row, file, resolve, reject });
      }
    });
  }

  function processNextUploadInQueue() {
    if (globalUploadQueue.length > 0) {
      const next = globalUploadQueue.shift();
      executeUpload(next.row, next.file)
        .then(next.resolve)
        .catch(next.reject)
        .finally(() => processNextUploadInQueue());
    } else {
      isUploadActive = false;
    }
  }

  // Upload single file with live progress (MB/MB and %) and auto-chunking for large files (>20MB)
  async function executeUpload(row, file) {
    const docItem = row.getAttribute('data-doc-item');
    const folderId = row.getAttribute('data-folder');
    const isMandatory = row.getAttribute('data-mandatory') || '0';
    const btn = row.querySelector('.btn-upload-item');
    const origHtml = btn ? btn.innerHTML : '';
    const flexGrowDiv = row.querySelector('.flex-grow-1');

    if (btn) {
      btn.disabled = true;
    }

    let progressWrap = row.querySelector('.upload-progress-wrap');
    if (!progressWrap && flexGrowDiv) {
      progressWrap = document.createElement('div');
      progressWrap.className = 'upload-progress-wrap mt-1';
      flexGrowDiv.appendChild(progressWrap);
    }

    const fileSize = file.size;

    function updateProgressUI(currentBytes, totalBytes, statusText) {
      const curMB = (currentBytes / 1048576).toFixed(1);
      const totMB = (totalBytes / 1048576).toFixed(1);
      const pct = Math.min(99, Math.round((currentBytes / totalBytes) * 100));

      if (btn) {
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status" style="width:11px; height:11px;"></span> ${curMB} / ${totMB} MB (${pct}%)`;
      }

      if (progressWrap) {
        progressWrap.innerHTML = `
          <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px;">
            <span class="text-primary fw-semibold"><i class="bi bi-cloud-arrow-up me-1"></i> ${statusText || 'Uploading:'} <strong>${curMB} MB</strong> / ${totMB} MB</span>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace">${pct}%</span>
          </div>
          <div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: ${pct}%;"></div>
          </div>
        `;
      }
    }

    updateProgressUI(0, fileSize, 'Preparing upload:');

    const isVideo = /\.(mp4|webm|mkv|avi|mov)$/i.test(file.name);
    const CHUNK_THRESHOLD = isVideo ? 2 * 1024 * 1024 : 5 * 1024 * 1024; // 2MB for video, 5MB for other files

    try {
      if (fileSize > CHUNK_THRESHOLD) {
        // CHUNKED UPLOAD PIPELINE (for videos, CAD, & massive 10GB+ datasets)
        let CHUNK_SIZE = 5 * 1024 * 1024; // 5 MB default for responsive saves
        if (fileSize > 1024 * 1024 * 1024) {
          CHUNK_SIZE = 25 * 1024 * 1024; // 25 MB chunks for > 1GB (cuts request count by 60%)
        } else if (fileSize > 200 * 1024 * 1024) {
          CHUNK_SIZE = 15 * 1024 * 1024; // 15 MB chunks for 200MB - 1GB
        }

        const totalChunks = Math.ceil(fileSize / CHUNK_SIZE);
        const storageKey = 'gtms_chk_v2_' + docItem + '_' + encodeURIComponent(file.name) + '_' + fileSize + '_' + CHUNK_SIZE;

        // Auto-Purge any abandoned upload session for this slot with a different file name or size
        for (let i = localStorage.length - 1; i >= 0; i--) {
          const k = localStorage.key(i);
          if (k && k.startsWith('gtms_chk_v2_' + docItem + '_') && k !== storageKey) {
            const abandonedToken = localStorage.getItem(k);
            if (abandonedToken) {
              fetch('/api/upload/chunk/cancel', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ upload_token: abandonedToken })
              }).catch(e => console.warn('Purge abandoned upload notice:', e));
            }
            localStorage.removeItem(k);
          }
        }

        let uploadToken = null;
        let startingChunk = 0;

        // Check if an existing unfinished upload session exists in localStorage (Resumable Upload)
        const savedToken = localStorage.getItem(storageKey);
        if (savedToken) {
          try {
            const statusRes = await fetch(`/api/upload/chunk/status?upload_token=${savedToken}`, {
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (statusRes.ok) {
              const statusData = await statusRes.json();
              if (statusData.success && (statusData.status === 'uploading' || statusData.status === 'pending') && statusData.chunk_size === CHUNK_SIZE && statusData.total_chunks === totalChunks) {
                uploadToken = savedToken;
                const missing = statusData.missing_chunks || [];
                startingChunk = missing.length > 0 ? missing[0] : 0;
                const resumedBytes = Math.min(fileSize, startingChunk * CHUNK_SIZE);
                updateProgressUI(resumedBytes, fileSize, `Resuming from chunk ${startingChunk + 1}/${totalChunks}:`);
              } else {
                localStorage.removeItem(storageKey);
              }
            } else {
              localStorage.removeItem(storageKey);
            }
          } catch (chkErr) {
            localStorage.removeItem(storageKey);
          }
        }

        if (!uploadToken) {
          const initRes = await fetch('/api/upload/chunk/init', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
              file_name: file.name,
              total_size: fileSize,
              chunk_size: CHUNK_SIZE,
              total_chunks: totalChunks,
              mime_type: file.type || 'application/octet-stream',
              target_module: 'lease_applications'
            })
          });
          const initData = await initRes.json();
          if (!initData.success) {
            throw new Error(initData.message || 'Chunk upload initialization failed');
          }
          uploadToken = initData.upload_token;
          localStorage.setItem(storageKey, uploadToken);
        }

        // Parallel Concurrent Chunk Uploader (3 workers in parallel for 3x - 4x speedup)
        const CONCURRENCY = 3;
        const chunkLoadedBytes = {};
        for (let i = 0; i < startingChunk; i++) {
          chunkLoadedBytes[i] = Math.min(CHUNK_SIZE, fileSize - i * CHUNK_SIZE);
        }
        let nextChunkIndex = startingChunk;
        let uploadError = null;

        function refreshOverallProgress() {
          let totalUploadedSoFar = 0;
          for (let i = 0; i < totalChunks; i++) {
            totalUploadedSoFar += (chunkLoadedBytes[i] || 0);
          }
          const finishedChunks = Object.keys(chunkLoadedBytes).filter(k => {
            const expected = (k == totalChunks - 1) ? (fileSize - k * CHUNK_SIZE) : CHUNK_SIZE;
            return chunkLoadedBytes[k] >= expected;
          }).length;
          const displayIdx = Math.min(totalChunks, finishedChunks + 1);
          updateProgressUI(totalUploadedSoFar, fileSize, `Chunk ${displayIdx}/${totalChunks}:`);
        }

        async function uploadWorker() {
          while (nextChunkIndex < totalChunks && !uploadError) {
            const chunkIdx = nextChunkIndex++;
            const start = chunkIdx * CHUNK_SIZE;
            const end = Math.min(fileSize, start + CHUNK_SIZE);
            const chunkBlob = file.slice(start, end);
            const currentChunkBytes = end - start;

            let chunkSucceeded = false;
            let lastChunkErr = null;
            const maxChunkRetries = 3;

            for (let attempt = 1; attempt <= maxChunkRetries; attempt++) {
              if (uploadError) break;
              try {
                await new Promise((resolve, reject) => {
                  const xhr = new XMLHttpRequest();
                  xhr.open('POST', '/api/upload/chunk/upload', true);
                  xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                  xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                  xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                      chunkLoadedBytes[chunkIdx] = e.loaded;
                      refreshOverallProgress();
                    }
                  };

                  xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                      try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success) {
                          chunkLoadedBytes[chunkIdx] = currentChunkBytes;
                          refreshOverallProgress();
                          resolve(res);
                        } else {
                          reject(new Error(res.message || 'Chunk failed'));
                        }
                      } catch (pe) {
                        reject(new Error('Invalid response for chunk ' + (chunkIdx + 1)));
                      }
                    } else {
                      if (xhr.status === 404) {
                        try { localStorage.removeItem(storageKey); } catch(e) {}
                      }
                      reject(new Error('Chunk upload server error: HTTP ' + xhr.status));
                    }
                  };

                  xhr.onerror = function () {
                    reject(new Error('Network error uploading chunk ' + (chunkIdx + 1)));
                  };

                  const fd = new FormData();
                  fd.append('upload_token', uploadToken);
                  fd.append('chunk_index', chunkIdx);
                  fd.append('chunk', chunkBlob, file.name);
                  xhr.send(fd);
                });

                chunkSucceeded = true;
                break;
              } catch (cErr) {
                lastChunkErr = cErr;
                if (attempt < maxChunkRetries) {
                  await new Promise(r => setTimeout(r, attempt * 1000));
                }
              }
            }

            if (!chunkSucceeded && !uploadError) {
              uploadError = lastChunkErr || new Error('Chunk ' + (chunkIdx + 1) + ' failed after retries');
              throw uploadError;
            }
          }
        }

        const workerPool = [];
        const activeWorkers = Math.min(CONCURRENCY, totalChunks - startingChunk);
        for (let w = 0; w < activeWorkers; w++) {
          workerPool.push(uploadWorker());
        }
        await Promise.all(workerPool);
        if (uploadError) throw uploadError;

        if (btn) {
          btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" style="width:11px;height:11px;"></span> Assembling...';
        }
        if (progressWrap) {
          progressWrap.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:11px;">
              <span class="text-success fw-semibold"><i class="bi bi-gear-wide-connected me-1"></i> Assembling file on server...</span>
              <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace">100%</span>
            </div>
            <div class="progress" style="height: 6px; border-radius: 3px; background: #e2e8f0;">
              <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%;"></div>
            </div>
          `;
        }

        const assembleRes = await fetch('/api/upload/chunk/assemble', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            upload_token: uploadToken,
            target_module: 'lease_applications'
          })
        });
        const assembleData = await assembleRes.json();
        if (!assembleData.success && !assembleData.file_path) {
          try { localStorage.removeItem(storageKey); } catch(e) {}
          throw new Error(assembleData.message || 'File assembly failed on server');
        }

        const finalFd = new FormData();
        finalFd.append('upload_token', uploadToken);
        finalFd.append('doc_item', docItem);
        finalFd.append('folder_id', folderId);
        finalFd.append('is_mandatory', isMandatory);
        finalFd.append('_token', '{{ csrf_token() }}');

        const finalRes = await fetch('{{ route('step5.upload') }}', {
          method: 'POST',
          body: finalFd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
        const finalData = await finalRes.json();
        if (finalData.status !== 1) {
          throw new Error(finalData.message || 'Failed to save document record');
        }

        localStorage.removeItem(storageKey);

        markRowUploaded(row, finalData.file_name, finalData.file_size, finalData.file_url);
        updateOverallProgress(finalData.uploaded, finalData.total, finalData.percent);
        return true;

      } else {
        // DIRECT XHR UPLOAD WITH REAL LIVE PROGRESS (<= 20 MB)
        await new Promise((resolve, reject) => {
          const xhr = new XMLHttpRequest();
          xhr.open('POST', '{{ route('step5.upload') }}', true);
          xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

          xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
              updateProgressUI(e.loaded, e.total, 'Uploading:');
            }
          };

          xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 300) {
              try {
                const data = JSON.parse(xhr.responseText);
                if (data.status === 1) {
                  markRowUploaded(row, data.file_name, data.file_size, data.file_url);
                  updateOverallProgress(data.uploaded, data.total, data.percent);
                  resolve(data);
                } else {
                  reject(new Error(data.message || 'Upload failed'));
                }
              } catch (pe) {
                reject(new Error('Invalid response from server'));
              }
            } else {
              reject(new Error('Server error: HTTP ' + xhr.status));
            }
          };

          xhr.onerror = function () {
            reject(new Error('Network error during upload'));
          };

          const formData = new FormData();
          formData.append('file', file);
          formData.append('doc_item', docItem);
          formData.append('folder_id', folderId);
          formData.append('is_mandatory', isMandatory);
          formData.append('_token', '{{ csrf_token() }}');
          xhr.send(formData);
        });

        return true;
      }

    } catch (err) {
      console.error(err);
      try { localStorage.removeItem(storageKey); } catch(e) {}
      if (progressWrap) progressWrap.remove();
      showSweetAlert('error', 'Upload Error', err.message || 'An error occurred during upload.');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = origHtml;
      }
      return false;
    }
  }

  // Handle Document Removal
  document.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.btn-remove-item');
    if (removeBtn) {
      e.stopPropagation();
      const row = removeBtn.closest('.checklist-row');
      if (!row) return;

      const docItem = row.getAttribute('data-doc-item');
      const folderId = row.getAttribute('data-folder');
      const isCustom = row.getAttribute('data-is-custom') === '1';

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Remove Document?',
          text: 'Are you sure you want to remove this attached file?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc2626',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, Remove It',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            executeRemoveDoc(row, docItem, folderId, isCustom);
          }
        });
      } else {
        if (confirm('Are you sure you want to remove this attached file?')) {
          executeRemoveDoc(row, docItem, folderId, isCustom);
        }
      }
    }
  });

  function executeRemoveDoc(row, docItem, folderId, isCustom) {
    // If the document is currently in the upload queue waiting to be uploaded, dequeue it cleanly
    const qIdx = globalUploadQueue.findIndex(item => item.row === row);
    if (qIdx !== -1) {
      const dequeued = globalUploadQueue.splice(qIdx, 1)[0];
      if (dequeued && dequeued.reject) {
        dequeued.reject(new Error('Upload cancelled: document removed from queue'));
      }
    }

    const removeBtn = row.querySelector('.btn-remove-item');
    if (removeBtn) {
      removeBtn.disabled = true;
      removeBtn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>';
    }

    fetch('{{ route('step5.remove') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        doc_item: docItem,
        folder_id: folderId
      })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 1) {
        markRowRemoved(row, isCustom);
        if (typeof toastr !== 'undefined') {
          toastr.success(data.message || 'Document removed successfully');
        }
      } else {
        showSweetAlert('error', 'Removal Failed', data.message || 'Failed to remove document.');
        if (removeBtn) {
          removeBtn.disabled = false;
          removeBtn.innerHTML = '<i class="bi bi-trash3"></i>';
        }
      }
    })
    .catch(err => {
      console.error(err);
      showSweetAlert('error', 'Error', err.message || 'Error occurred while removing document.');
      if (removeBtn) {
        removeBtn.disabled = false;
        removeBtn.innerHTML = '<i class="bi bi-trash3"></i>';
      }
    });
  }

  // Handle Discard of Paused / Unfinished Upload
  document.addEventListener('click', function (e) {
    const discardBtn = e.target.closest('.btn-discard-paused-item');
    if (discardBtn) {
      e.stopPropagation();
      const row = discardBtn.closest('.checklist-row');
      if (!row) return;

      const token = discardBtn.getAttribute('data-upload-token');
      const storageKey = discardBtn.getAttribute('data-storage-key');
      const isCustom = row.getAttribute('data-is-custom') === '1';

      function performDiscard() {
        discardBtn.disabled = true;
        discardBtn.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:10px;height:10px;"></span>';

        fetch('/api/upload/chunk/cancel', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ upload_token: token })
        })
        .finally(() => {
          if (storageKey) {
            try { localStorage.removeItem(storageKey); } catch (e) {}
          }
          discardBtn.remove();
          markRowRemoved(row, isCustom);
          if (typeof toastr !== 'undefined') {
            toastr.success('Paused upload discarded and temporary chunks purged.');
          }
        });
      }

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Discard Paused Upload?',
          text: 'This will purge all temporary chunks from the server and reset this document slot.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc2626',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, Discard',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            performDiscard();
          }
        });
      } else {
        if (confirm('Discard paused upload and purge temporary files?')) {
          performDiscard();
        }
      }
    }
  });

  // Row Upload / Change button handler — Triggers the specific row's file input
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-upload-item');
    if (btn) {
      e.stopPropagation();
      const row = btn.closest('.checklist-row');
      if (row) {
        let input = row.querySelector('.row-file-input');
        if (!input) {
          input = document.createElement('input');
          input.type = 'file';
          input.className = 'row-file-input d-none';
          input.setAttribute('accept', '{{ $allAllowedExt }}');
          row.appendChild(input);
        }
        input.value = '';
        input.click();
      }
    }
  });

  // Direct change handler for per-row file inputs
  document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('row-file-input')) {
      const input = e.target;
      const row = input.closest('.checklist-row');
      if (row && input.files && input.files[0]) {
        uploadFileToServer(row, input.files[0]);
      }
    }
  });

  // Direct drag & drop on individual checklist rows
  function attachRowDragAndDrop(row) {
    row.addEventListener('dragover', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.add('row-dragover');
    });
    row.addEventListener('dragleave', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('row-dragover');
    });
    row.addEventListener('drop', function (e) {
      e.preventDefault();
      e.stopPropagation();
      this.classList.remove('row-dragover');
      if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        uploadFileToServer(this, e.dataTransfer.files[0]);
      }
    });
  }

  document.querySelectorAll('.checklist-row').forEach(attachRowDragAndDrop);

  // Intercept View button if file is a ZIP or archive
  document.addEventListener('click', function(e) {
    const viewBtn = e.target.closest('.btn-view-item');
    if (!viewBtn) return;

    const href = viewBtn.getAttribute('href');
    const isZip = viewBtn.getAttribute('data-is-zip') === '1' || isArchiveFile(href) || isArchiveFile(viewBtn.getAttribute('data-file-name'));

    if (isZip && href && href !== '#') {
      e.preventDefault();
      e.stopPropagation();

      const fileName = viewBtn.getAttribute('data-file-name') || href.split('/').pop() || 'archive.zip';
      const docName = viewBtn.getAttribute('data-doc-name') || viewBtn.closest('.checklist-row')?.querySelector('.ci-name')?.textContent?.trim() || 'Attached File';
      const fileSize = viewBtn.getAttribute('data-file-size') || 'ZIP Archive';

      document.getElementById('zipModalFileName').textContent = decodeURIComponent(fileName);
      document.getElementById('zipModalDocName').textContent = docName;
      document.getElementById('zipModalFileSize').textContent = fileSize;
      const dlBtn = document.getElementById('zipModalDownloadBtn');
      dlBtn.href = href;
      dlBtn.setAttribute('download', decodeURIComponent(fileName));

      const modalEl = document.getElementById('modalZipFilePreview');
      if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
      }
    }
  });

  // Handle change of doc-req-select (Mandatory vs Optional)
  document.addEventListener('change', function (e) {
    if (e.target && e.target.classList.contains('doc-req-select')) {
      const select = e.target;
      const val = select.value;
      const docItem = select.getAttribute('data-doc-item');
      const row = select.closest('.checklist-row');
      const isMandatory = val === 'mandatory';

      if (row) {
        row.setAttribute('data-mandatory', isMandatory ? '1' : '0');
      }

      if (isMandatory) {
        select.className = 'form-select form-select-sm doc-req-select border-danger-subtle text-danger bg-danger-subtle me-1';
      } else {
        select.className = 'form-select form-select-sm doc-req-select border-secondary-subtle text-muted bg-light me-1';
      }

      fetch('{{ route('step5.requirement') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          doc_item: docItem,
          requirement: val
        })
      }).catch(err => console.log('Requirement save notice:', err));
    }
  });

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
    // Prioritize un-uploaded mandatory rows first, then optional un-uploaded rows
    const pendingMandatory = Array.from(document.querySelectorAll('.checklist-row[data-mandatory="1"]:not(.up)'));
    const pendingOptional = Array.from(document.querySelectorAll('.checklist-row[data-mandatory="0"]:not(.up)'));
    const pendingRows = [...pendingMandatory, ...pendingOptional];

    if (pendingRows.length === 0) {
      showSweetAlert('info', 'Checklist Complete', 'All checklist items are already uploaded!');
      return;
    }

    const files = Array.from(fileList);
    for (let i = 0; i < files.length && i < pendingRows.length; i++) {
      await uploadFileToServer(pendingRows[i], files[i]);
    }
  }

  // Track custom document modal upload state for safe abort / confirmation
  let isCustomDocUploading = false;
  let customDocUploadToken = null;
  let activeCustomDocXhrs = [];
  let isCustomDocAborted = false;
  let customDocAbortController = null;
  window.__setCustomDocUploading = function (val) { isCustomDocUploading = val; };

  // Open Add Document Modal (Lease Application)
  document.querySelectorAll('.btn-open-lease-add-doc').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      const folderId = this.getAttribute('data-folder-id');
      const folderName = this.getAttribute('data-folder-name');

      isCustomDocUploading = false;
      customDocUploadToken = null;
      activeCustomDocXhrs = [];
      isCustomDocAborted = false;
      customDocAbortController = null;

      document.getElementById('lease_custom_folder_id').value = folderId;
      document.getElementById('lease_custom_folder_name').value = folderName;
      document.getElementById('lease_custom_doc_name').value = '';
      document.getElementById('lease_custom_doc_status').value = '0';
      document.getElementById('lease_custom_doc_file').value = '';
      const selectedNameEl = document.getElementById('lease_custom_selected_filename');
      if (selectedNameEl) {
        selectedNameEl.classList.add('d-none');
        selectedNameEl.textContent = '';
      }

      const submitBtn = document.getElementById('btn_submit_lease_custom_doc');
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill me-1.5"></i>Upload &amp; Add Document';
      }

      const modalEl = document.getElementById('modalAddCustomLeaseDoc');
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    });
  });

  // Intercept modal close during active upload & require confirmation to abort
  const modalCustomDocEl = document.getElementById('modalAddCustomLeaseDoc');
  if (modalCustomDocEl) {
    modalCustomDocEl.addEventListener('hide.bs.modal', function (e) {
      if (isCustomDocUploading) {
        e.preventDefault();
        e.stopPropagation();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Upload in Progress!',
            html: '<p class="mb-2">A file upload is currently running in this folder.</p><p class="text-danger fw-semibold mb-0">Are you sure you want to cancel and abort the upload?</p>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#475569',
            confirmButtonText: '<i class="bi bi-x-circle me-1"></i> Yes, Cancel Upload',
            cancelButtonText: '<i class="bi bi-play-circle me-1"></i> Continue Uploading',
            reverseButtons: true,
            allowOutsideClick: false
          }).then(async (result) => {
            if (result.isConfirmed) {
              isCustomDocAborted = true;
              isCustomDocUploading = false;

              // Abort active XHRs immediately
              activeCustomDocXhrs.forEach(x => {
                try { x.abort(); } catch (err) {}
              });
              activeCustomDocXhrs = [];

              if (customDocAbortController) {
                try { customDocAbortController.abort(); } catch (err) {}
              }

              // Notify server to cancel chunk session and purge temporary data
              if (customDocUploadToken) {
                const tokenToCancel = customDocUploadToken;
                customDocUploadToken = null;
                try {
                  fetch('/api/upload/chunk/cancel', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}',
                      'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ upload_token: tokenToCancel })
                  });
                } catch (cErr) {}
              }

              // Reset submit button & input fields
              const submitBtn = document.getElementById('btn_submit_lease_custom_doc');
              if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill me-1.5"></i>Upload &amp; Add Document';
              }
              const fileInput = document.getElementById('lease_custom_doc_file');
              if (fileInput) fileInput.value = '';
              const selectedNameEl = document.getElementById('lease_custom_selected_filename');
              if (selectedNameEl) {
                selectedNameEl.classList.add('d-none');
                selectedNameEl.textContent = '';
              }

              // Safely hide modal now that upload is aborted
              const modalInstance = bootstrap.Modal.getInstance(modalCustomDocEl);
              if (modalInstance) {
                modalInstance.hide();
              }

              if (typeof toastr !== 'undefined') {
                toastr.info('Upload was cancelled.');
              }
            }
          });
        } else {
          if (confirm('Upload is in progress. Are you sure you want to cancel the upload?')) {
            isCustomDocAborted = true;
            isCustomDocUploading = false;
            activeCustomDocXhrs.forEach(x => { try { x.abort(); } catch (err) {} });
            activeCustomDocXhrs = [];
            const modalInstance = bootstrap.Modal.getInstance(modalCustomDocEl);
            if (modalInstance) modalInstance.hide();
          }
        }
      }
    });
  }

  // Display selected filename in custom document modal dropzone
  const customFileInput = document.getElementById('lease_custom_doc_file');
  if (customFileInput) {
    customFileInput.addEventListener('change', function () {
      const selectedNameEl = document.getElementById('lease_custom_selected_filename');
      if (!selectedNameEl) return;
      if (this.files && this.files.length > 0) {
        selectedNameEl.textContent = 'Selected: ' + this.files[0].name;
        selectedNameEl.classList.remove('d-none');
      } else {
        selectedNameEl.textContent = '';
        selectedNameEl.classList.add('d-none');
      }
    });
  }

  // Submit Add Document Modal
  const formAddLeaseCustomDoc = document.getElementById('form_add_lease_custom_doc');
  if (formAddLeaseCustomDoc) {
    formAddLeaseCustomDoc.addEventListener('submit', function (e) {
      e.preventDefault();

      const folderId = document.getElementById('lease_custom_folder_id').value;
      const folderName = document.getElementById('lease_custom_folder_name').value;
      const docName = document.getElementById('lease_custom_doc_name').value.trim();
      const isMandatory = document.getElementById('lease_custom_doc_status').value === '1';
      const fileInput = document.getElementById('lease_custom_doc_file');
      const file = fileInput.files[0];

      if (!docName) {
        showSweetAlert('warning', 'Document Name Required', 'Please specify a Document Name before uploading.');
        return;
      }
      if (!file) {
        showSweetAlert('warning', 'File Required', 'Please select a document file to upload.');
        return;
      }

      const submitBtn = document.getElementById('btn_submit_lease_custom_doc');
      const origHtml = submitBtn.innerHTML;
      submitBtn.disabled = true;

      // Mark upload active for modal protection
      isCustomDocUploading = true;
      isCustomDocAborted = false;
      customDocUploadToken = null;
      activeCustomDocXhrs = [];
      customDocAbortController = new AbortController();

      const fileSize = file.size;

      (async () => {
        try {
          let uploadToken = null;
          if (fileSize > 20 * 1024 * 1024) {
            let CHUNK_SIZE = 10 * 1024 * 1024;
            if (fileSize > 1024 * 1024 * 1024) {
              CHUNK_SIZE = 25 * 1024 * 1024;
            } else if (fileSize > 200 * 1024 * 1024) {
              CHUNK_SIZE = 15 * 1024 * 1024;
            }
            const totalChunks = Math.ceil(fileSize / CHUNK_SIZE);

            const initRes = await fetch('/api/upload/chunk/init', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
              },
              signal: customDocAbortController.signal,
              body: JSON.stringify({
                file_name: file.name,
                total_size: fileSize,
                chunk_size: CHUNK_SIZE,
                total_chunks: totalChunks,
                mime_type: file.type || 'application/octet-stream',
                target_module: 'lease_applications'
              })
            });
            const initData = await initRes.json();
            if (!initData.success) throw new Error(initData.message || 'Chunk initialization failed');
            uploadToken = initData.upload_token;
            customDocUploadToken = uploadToken;

            if (isCustomDocAborted) return;

            const CONCURRENCY = 3;
            const customLoadedBytes = {};
            let nextCustomIndex = 0;
            let customError = null;

            function refreshCustomProgress() {
              if (isCustomDocAborted) return;
              let totalUploadedSoFar = 0;
              for (let i = 0; i < totalChunks; i++) {
                totalUploadedSoFar += (customLoadedBytes[i] || 0);
              }
              const curMB = (totalUploadedSoFar / 1048576).toFixed(1);
              const totMB = (fileSize / 1048576).toFixed(1);
              const pct = Math.min(99, Math.round((totalUploadedSoFar / fileSize) * 100));
              submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> ${curMB} / ${totMB} MB (${pct}%)`;
            }

            async function customWorker() {
              while (nextCustomIndex < totalChunks && !customError) {
                if (isCustomDocAborted) return;
                const i = nextCustomIndex++;
                if (i >= totalChunks) break;
                const start = i * CHUNK_SIZE;
                const end = Math.min(fileSize, start + CHUNK_SIZE);
                const chunkBlob = file.slice(start, end);
                const currentChunkBytes = end - start;

                let chunkOk = false;
                let lastErr = null;
                const maxRetries = 3;

                for (let attempt = 1; attempt <= maxRetries; attempt++) {
                  if (customError || isCustomDocAborted) break;
                  try {
                    await new Promise((resolve, reject) => {
                      if (isCustomDocAborted) {
                        reject(new Error('Upload aborted'));
                        return;
                      }
                      const xhr = new XMLHttpRequest();
                      activeCustomDocXhrs.push(xhr);

                      xhr.open('POST', '/api/upload/chunk/upload', true);
                      xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                      xhr.upload.onprogress = (pe) => {
                        if (pe.lengthComputable) {
                          customLoadedBytes[i] = pe.loaded;
                          refreshCustomProgress();
                        }
                      };

                      xhr.onload = () => {
                        activeCustomDocXhrs = activeCustomDocXhrs.filter(x => x !== xhr);
                        if (xhr.status >= 200 && xhr.status < 300) {
                          try {
                            const res = JSON.parse(xhr.responseText);
                            if (res.success) {
                              customLoadedBytes[i] = currentChunkBytes;
                              refreshCustomProgress();
                              resolve(res);
                            } else reject(new Error(res.message || 'Chunk failed'));
                          } catch (pe) {
                            reject(new Error('Invalid response'));
                          }
                        } else reject(new Error('HTTP ' + xhr.status));
                      };

                      xhr.onerror = () => {
                        activeCustomDocXhrs = activeCustomDocXhrs.filter(x => x !== xhr);
                        reject(new Error('Upload error'));
                      };

                      xhr.onabort = () => {
                        activeCustomDocXhrs = activeCustomDocXhrs.filter(x => x !== xhr);
                        reject(new Error('Upload aborted'));
                      };

                      const fd = new FormData();
                      fd.append('upload_token', uploadToken);
                      fd.append('chunk_index', i);
                      fd.append('chunk', chunkBlob, file.name);
                      xhr.send(fd);
                    });

                    chunkOk = true;
                    break;
                  } catch (cErr) {
                    lastErr = cErr;
                    if (isCustomDocAborted) break;
                    if (attempt < maxRetries) {
                      await new Promise(r => setTimeout(r, attempt * 1000));
                    }
                  }
                }

                if (!chunkOk && !customError && !isCustomDocAborted) {
                  customError = lastErr || new Error('Chunk ' + (i + 1) + ' failed');
                  throw customError;
                }
              }
            }

            const cWorkers = [];
            for (let w = 0; w < Math.min(CONCURRENCY, totalChunks); w++) {
              cWorkers.push(customWorker());
            }
            await Promise.all(cWorkers);
            if (isCustomDocAborted) return;
            if (customError) throw customError;

            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Assembling...';
            const assembleRes = await fetch('/api/upload/chunk/assemble', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
              },
              signal: customDocAbortController.signal,
              body: JSON.stringify({
                upload_token: uploadToken,
                target_module: 'lease_applications'
              })
            });
            const assembleData = await assembleRes.json();
            if (!assembleData.success && !assembleData.file_path) {
              throw new Error(assembleData.message || 'Assembly failed');
            }
          }

          if (isCustomDocAborted) return;

          const formData = new FormData();
          if (uploadToken) {
            formData.append('upload_token', uploadToken);
          } else {
            formData.append('file', file);
          }
          formData.append('doc_name', docName);
          formData.append('folder_id', folderId);
          formData.append('is_mandatory', isMandatory ? '1' : '0');
          formData.append('_token', '{{ csrf_token() }}');

          const uploadRes = await fetch('{{ route('step5.upload') }}', {
            method: 'POST',
            body: formData,
            signal: customDocAbortController ? customDocAbortController.signal : undefined,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
          if (isCustomDocAborted) return;
          const data = await uploadRes.json();

          isCustomDocUploading = false;
          customDocUploadToken = null;
          activeCustomDocXhrs = [];

          submitBtn.disabled = false;
          submitBtn.innerHTML = origHtml;

        if (data.status === 1) {
          const modalEl = document.getElementById('modalAddCustomLeaseDoc');
          const modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();

          const targetContainer = document.getElementById('folder_container_' + folderId);
          if (targetContainer) {
            const isZip = isArchiveFile(data.file_name);
            const rowDiv = document.createElement('div');
            rowDiv.className = 'checklist-row up';
            rowDiv.setAttribute('data-doc-item', data.doc_item);
            rowDiv.setAttribute('data-folder', folderId);
            rowDiv.setAttribute('data-is-custom', '1');
            rowDiv.setAttribute('data-mandatory', data.is_mandatory ? '1' : '0');

            rowDiv.innerHTML = `
              <input type="file" class="row-file-input d-none" data-doc-item="${data.doc_item}" data-folder="${folderId}" accept="{{ $allAllowedExt }}">
              <div class="ci-icon" style="${isZip ? 'background:#fef3c7;' : 'background:var(--green-soft, #e6f4ea);'}">
                <i class="bi ${isZip ? 'bi-file-earmark-zip text-warning' : 'bi-check-lg'}" style="${isZip ? '' : 'color:var(--green);'}"></i>
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
              <div class="d-flex align-items-center gap-1 ms-2 flex-shrink-0">
                <select class="form-select form-select-sm doc-req-select ${data.is_mandatory ? 'border-danger-subtle text-danger bg-danger-subtle' : 'border-secondary-subtle text-muted bg-light'} me-1"
                        data-doc-item="${data.doc_item}"
                        style="width: auto; min-width: 95px; font-size: .72rem; padding: 2px 20px 2px 6px; height: 26px; font-weight: 600; cursor: pointer;"
                        title="Set requirement status (Mandatory or Optional)">
                  <option value="mandatory" ${data.is_mandatory ? 'selected' : ''}>Mandatory</option>
                  <option value="optional" ${!data.is_mandatory ? 'selected' : ''}>Optional</option>
                </select>
                <span class="badge-status uploaded">Uploaded</span>
                <a href="${data.file_url}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-info py-0 px-2 btn-view-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="${isZip ? 'Download / View ZIP Archive' : 'View Document'}" data-file-name="${data.file_name}" data-file-size="${data.file_size}" data-doc-name="${data.doc_name}" data-is-zip="${isZip ? '1' : '0'}">
                  <i class="bi ${isZip ? 'bi-file-earmark-zip' : 'bi-eye'}"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-upload-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Change / Re-upload file"><i class="bi bi-arrow-repeat"></i></button>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-remove-item" style="font-size:.78rem; height:26px; display:inline-flex; align-items:center; justify-content:center;" title="Remove this custom document"><i class="bi bi-trash3"></i></button>
              </div>
            `;
            targetContainer.appendChild(rowDiv);
            attachRowDragAndDrop(rowDiv);
          }

          // Increment folder count
          const countSpan = document.getElementById('count_folder_' + folderId);
          if (countSpan) {
            countSpan.textContent = (parseInt(countSpan.textContent) || 0) + 1;
          }

          updateOverallProgress(data.uploaded, data.total, data.percent);

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Document Added',
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
      } catch (err) {
        isCustomDocUploading = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = origHtml;
        if (isCustomDocAborted) {
          return;
        }
        console.error(err);
        showSweetAlert('error', 'Upload Error', err.message || 'An unexpected error occurred during custom document upload.');
      }
    })();
  });
}

  // Mandatory Document Validation on Continue to Step 6
  const btnContinueMimas = document.getElementById('btn_continue_mimas');
  if (btnContinueMimas) {
    btnContinueMimas.addEventListener('click', function (e) {
      if (isUploadActive || globalUploadQueue.length > 0) {
        e.preventDefault();
        e.stopPropagation();
        showSweetAlert('warning', 'Upload in Progress', 'Documents are currently being uploaded! Please wait until uploads finish before proceeding to the next step.');
        return;
      }

      // Paused / Incomplete Upload Validation on Continue to Step 6
      const pausedDiscardBtns = document.querySelectorAll('.checklist-row .btn-discard-paused-item');
      if (pausedDiscardBtns.length > 0) {
        e.preventDefault();
        e.stopPropagation();

        const pausedDocNames = [];
        pausedDiscardBtns.forEach(pBtn => {
          const pRow = pBtn.closest('.checklist-row');
          const pName = pRow?.querySelector('.ci-name')?.textContent?.trim() || 'Document';
          pausedDocNames.push(pName);
        });

        const pausedHtml = '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-warning fw-bold"><i class="bi bi-pause-circle-fill me-1"></i> ' + pausedDocNames.length + ' Document(s) Have Paused / Incomplete Uploads:</p><ul class="mb-2 ps-3 text-muted" style="max-height:160px; overflow-y:auto;">' +
          pausedDocNames.map(name => '<li>' + name + '</li>').join('') +
          '</ul><p class="text-muted small mb-0">Please complete the upload using <strong>Resume</strong> or click the <strong>Trash (Delete)</strong> button to discard the paused file before proceeding to the next step.</p></div>';

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Incomplete Uploads Found!',
            html: pausedHtml,
            confirmButtonColor: '#0F1E4D',
            confirmButtonText: '<i class="bi bi-check2-circle me-1"></i> OK, I Will Complete or Discard'
          });
        } else {
          alert('Please complete paused upload(s) or discard them before proceeding.');
        }
        return;
      }

      const missingMandatory = [];
      document.querySelectorAll('.checklist-row[data-mandatory="1"]').forEach(row => {
        if (!row.classList.contains('up')) {
          const docName = row.querySelector('.ci-name')?.textContent.trim() || 'Mandatory Statutory Document';
          missingMandatory.push(docName);
        }
      });

      if (missingMandatory.length > 0) {
        e.preventDefault();
        const missingHtml = '<div class="text-start" style="font-size:0.85rem;"><p class="mb-2 text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> ' + missingMandatory.length + ' Mandatory Document(s) Not Uploaded:</p><ul class="mb-2 ps-3 text-muted" style="max-height:160px; overflow-y:auto;">' +
          missingMandatory.map(name => '<li>' + name + '</li>').join('') +
          '</ul><p class="text-muted small mb-0">Statutory guidelines require these documents to be attached. Do you want to stay and upload them now, or proceed with draft?</p></div>';

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Mandatory Documents Missing!',
            html: missingHtml,
            showCancelButton: true,
            confirmButtonColor: '#0F1E4D',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-arrow-right-circle me-1"></i> Proceed Anyway (Save Draft)',
            cancelButtonText: '<i class="bi bi-upload me-1"></i> Stay & Upload'
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = btnContinueMimas.getAttribute('href');
            }
          });
        } else {
          if (confirm('Warning: ' + missingMandatory.length + ' mandatory documents are missing. Proceed anyway?')) {
            window.location.href = btnContinueMimas.getAttribute('href');
          }
        }
      }
    });
  }

  // Navigation Guard: Prevent accidental exit, tab close, or navigation while uploads are active
  window.addEventListener('beforeunload', function (e) {
    if (isUploadActive || globalUploadQueue.length > 0) {
      e.preventDefault();
      e.returnValue = 'Upload in progress! Leaving this page will cancel your ongoing uploads.';
      return e.returnValue;
    }
  });

  document.addEventListener('click', function (e) {
    const navLink = e.target.closest('a[href]:not([href^="#"]):not([target="_blank"]):not([download])');
    if (navLink && (isUploadActive || globalUploadQueue.length > 0)) {
      e.preventDefault();
      e.stopPropagation();
      showSweetAlert('warning', 'Upload in Progress', 'Documents are currently being uploaded! Please wait until uploads finish before leaving this page.');
    }
  });
});
</script>

@endsection
