@extends('layouts.app')
@section('title', 'Step5 - Upload Documents')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap" style="max-width:900px;">

      <div class="step-progress">
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
        <div class="sp-step active"><div class="circ">5</div><div class="sp-label">Documents</div></div>
        <div class="sp-step"><div class="circ">6</div><div class="sp-label">MIMAS</div></div>
        <div class="sp-step"><div class="circ">7</div><div class="sp-label">Preview</div></div>
      </div>

      <div class="wizard-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <div class="wc-eyebrow">Step 5 of 7</div>
            <h4>Upload Documents</h4>
            <div class="wc-sub mb-1">Upload each file against its checklist item across all 3 folders. Accepted formats: PDF, KML, JPG, PNG (max 10MB each).</div>
            <div class="d-flex align-items-center gap-2 mt-1">
              <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size:0.75rem;">
                <i class="fa fa-database me-1"></i> Milestone Auto-Save Active
              </span>
              <span class="text-muted" style="font-size:0.75rem;">Uploaded files are permanently saved to database. You can exit &amp; resume anytime without data loss.</span>
            </div>
          </div>
          @php
            $uploadedDocs = $uploadedDocs ?? session('lease_draft.uploaded_docs', []);
            $uploadedCount = count($uploadedDocs);
            $totalCount = 19;
            $percent = $totalCount > 0 ? round(($uploadedCount / $totalCount) * 100) : 0;
          @endphp
          <div class="text-end">
            <div id="counter_text" style="font-weight:800; font-size:1.1rem; color:var(--navy);">{{ $uploadedCount }} / {{ $totalCount }}</div>
            <div style="font-size:.7rem; color:var(--muted);">uploaded</div>
          </div>
        </div>
        <div class="progress-thin mt-2 mb-4">
          <div class="progress-bar" id="doc_progress_bar" style="width:{{ $percent }}%"></div>
        </div>

        <!-- Interactive Dropzone -->
        <div class="dropzone" id="dropzone_box" style="cursor: pointer; border: 2px dashed #93c5fd; background: #f8fbff; transition: all 0.2s;">
          <i class="bi bi-cloud-arrow-up" style="font-size: 2.2rem; color: #2563eb;"></i>
          <div class="dz-title" style="font-weight: 700; color: #1e3a8a;">Drag &amp; drop files here, or click to browse</div>
          <div class="dz-sub text-muted">Auto-matches uploaded files to corresponding checklist items</div>
          <div class="mt-3">
            <button type="button" class="btn btn-sm btn-navy px-3 me-2" id="btn_select_files">
              <i class="bi bi-folder2-open me-1"></i> Browse Files
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary px-3" id="btn_upload_all_trigger">
              <i class="bi bi-cloud-check-fill me-1 text-primary"></i> <span id="btn_upload_all_text">{{ $uploadedCount >= $totalCount ? 'All Files Uploaded' : 'Upload All Remaining (' . ($totalCount - $uploadedCount) . ' Files)' }}</span>
            </button>
          </div>
          <input type="file" id="real_file_input" multiple style="display:none;" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml">
          <input type="file" id="individual_file_input" style="display:none;" accept=".pdf,.png,.jpg,.jpeg,.kml,.xml">
        </div>

        <div id="upload_success_alert" class="alert {{ $uploadedCount >= $totalCount ? 'd-flex' : 'd-none' }} align-items-center gap-2 mt-3 mb-3" style="background:#e6f4ea; border:1px solid #b7e1cd; color:#137333; font-size:.88rem; border-radius:8px;">
          <i class="bi bi-check-circle-fill fs-5 text-success"></i>
          <div><strong>All 19 Documents &amp; Plan Files Uploaded!</strong> All regulatory documents and survey plans are in place. You can proceed to MIMAS registration.</div>
        </div>

        <!-- FOLDER 1: DOCUMENTS FOLDER -->
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-folder-open me-1"></i> 1. Documents folder
          </div>
          <small class="badge bg-light text-muted" style="font-size:.75rem;">Folder ID: #7 &middot; 9 Items</small>
        </div>

        @php
          $folder7Items = [
            ['id' => 8,  'name' => '8. Land Document', 'default' => 'land_document_title.pdf', 'size' => '1.2 MB', 'mandatory' => true],
            ['id' => 9,  'name' => '9. Consent (If Applicable)', 'default' => 'landowner_consent_deed.pdf', 'size' => '1.1 MB', 'mandatory' => false],
            ['id' => 10, 'name' => '10. Adangal & A-register', 'default' => 'adangal_a_register_record.pdf', 'size' => '2.4 MB', 'mandatory' => true],
            ['id' => 11, 'name' => '11. Patta & Encumbrance Certificate', 'default' => 'patta_chitta_certificate.pdf', 'size' => '890 KB', 'mandatory' => true],
            ['id' => 12, 'name' => '12. Work Order', 'default' => 'work_order_approval.pdf', 'size' => '680 KB', 'mandatory' => false],
            ['id' => 13, 'name' => '13. Gazette', 'default' => 'district_gazette_notification.pdf', 'size' => '1.5 MB', 'mandatory' => false],
            ['id' => 14, 'name' => '14. Recommendation Letter', 'default' => 'ad_mines_recommendation_letter.pdf', 'size' => '820 KB', 'mandatory' => false],
            ['id' => 15, 'name' => '15. Mineral Management System – Application', 'default' => 'mimas_portal_application.pdf', 'size' => '1.9 MB', 'mandatory' => true],
            ['id' => 16, 'name' => '16. Challan downloaded from Mimas', 'default' => 'mimas_treasury_challan.pdf', 'size' => '832 KB', 'mandatory' => false],
          ];
        @endphp

        @foreach($folder7Items as $item)
          @php
            $doc = $uploadedDocs[$item['id']] ?? null;
            $isUp = !empty($doc);
            $dispSize = $doc ? (is_numeric($doc['file_size']) ? (round($doc['file_size']/1048576, 1) . ' MB') : $doc['file_size']) : $item['size'];
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

        <!-- FOLDER 2: LEASE APPLICATION FOLDER -->
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-folder me-1"></i> 2. Lease Application folder
          </div>
          <small class="badge bg-light text-muted" style="font-size:.75rem;">Folder ID: #8 &middot; 7 Items</small>
        </div>

        @php
          $folder8Items = [
            ['id' => 1, 'name' => '1. Lease application – signed, FMB, Plan', 'default' => 'signed_lease_application.pdf', 'size' => '2.1 MB', 'mandatory' => true],
            ['id' => 2, 'name' => '2. Affidavit – Income Tax', 'default' => 'affidavit_income_tax.pdf', 'size' => '410 KB', 'mandatory' => true],
            ['id' => 3, 'name' => '3. IT returns (If Applicable)', 'default' => 'it_returns_assessment.pdf', 'size' => '1.3 MB', 'mandatory' => false],
            ['id' => 4, 'name' => '4. Affidavit – Mining Due', 'default' => 'affidavit_mining_dues.pdf', 'size' => '840 KB', 'mandatory' => true],
            ['id' => 5, 'name' => '5. Affidavit – Mining Lease', 'default' => 'affidavit_mining_lease.pdf', 'size' => '380 KB', 'mandatory' => true],
            ['id' => 6, 'name' => '6. Affidavit – 1.5 meter depth', 'default' => 'affidavit_depth_safety.pdf', 'size' => '520 KB', 'mandatory' => false],
            ['id' => 7, 'name' => '7. Affidavit – Hill Areas', 'default' => 'affidavit_hill_areas.pdf', 'size' => '470 KB', 'mandatory' => false],
          ];
        @endphp

        @foreach($folder8Items as $item)
          @php
            $doc = $uploadedDocs[$item['id']] ?? null;
            $isUp = !empty($doc);
            $dispSize = $doc ? (is_numeric($doc['file_size']) ? (round($doc['file_size']/1048576, 1) . ' MB') : $doc['file_size']) : $item['size'];
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

        <!-- FOLDER 3: PLAN FOLDER -->
        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
          <div style="font-size:.72rem; font-weight:700; color:var(--navy); text-transform:uppercase; letter-spacing:.05em;">
            <i class="fa fa-inbox me-1"></i> 3. Plan folder
          </div>
          <small class="badge bg-light text-muted" style="font-size:.75rem;">Folder ID: #9 &middot; 3 Items (Source, KML, PDF)</small>
        </div>

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
            $dispSize = $doc ? (is_numeric($doc['file_size']) ? (round($doc['file_size']/1048576, 1) . ' MB') : $doc['file_size']) : $item['size'];
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

        <div class="wizard-actions d-flex justify-content-between align-items-center mt-4">
          <a href="/step4" class="btn btn-outline-navy btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
          <div class="d-flex gap-2">
            <a href="/application" class="btn btn-outline-primary px-3">
              <i class="fa fa-save me-1"></i> Save Draft &amp; Continue Later
            </a>
            @can('application.create')
            <a href="/step6" id="btn_continue_mimas" class="btn {{ $uploadedCount >= $totalCount ? 'btn-green shadow-sm' : 'btn-navy' }} px-4">Continue to MIMAS <i class="bi bi-arrow-right"></i></a>
            @endcan
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const counterText = document.getElementById('counter_text');
  const progressBar = document.getElementById('doc_progress_bar');
  const successAlert = document.getElementById('upload_success_alert');
  const continueBtn = document.getElementById('btn_continue_mimas');
  const dropzoneBox = document.getElementById('dropzone_box');
  const realFileInput = document.getElementById('real_file_input');
  const individualFileInput = document.getElementById('individual_file_input');
  const btnSelectFiles = document.getElementById('btn_select_files');
  const btnUploadAll = document.getElementById('btn_upload_all_trigger');
  const btnUploadAllText = document.getElementById('btn_upload_all_text');

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

    if (btnUploadAllText) {
      if (uploadedCount >= totalCount) {
        btnUploadAllText.textContent = 'All Files Uploaded';
      } else {
        btnUploadAllText.textContent = `Upload All Remaining (${totalCount - uploadedCount} Files)`;
      }
    }

    if (uploadedCount >= totalCount) {
      if (successAlert) {
        successAlert.classList.remove('d-none');
        successAlert.classList.add('d-flex');
      }
      if (continueBtn) {
        continueBtn.className = 'btn btn-green px-4 shadow-sm';
        continueBtn.innerHTML = 'Continue to MIMAS <i class="bi bi-arrow-right-circle-fill ms-1"></i>';
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
        alert(data.message || 'Upload failed for item #' + docItem);
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = origHtml;
        }
        return false;
      }
    })
    .catch(err => {
      console.error(err);
      alert('Upload error: ' + err.message);
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

  // Upload All Remaining Handler (uploads placeholders sequentially via real AJAX)
  if (btnUploadAll) {
    btnUploadAll.addEventListener('click', async function (e) {
      e.stopPropagation();
      const pendingRows = Array.from(document.querySelectorAll('.checklist-row:not(.up)'));
      if (pendingRows.length === 0) {
        alert('All documents are already uploaded!');
        return;
      }

      this.disabled = true;
      this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Uploading Remaining...';

      for (const row of pendingRows) {
        const docItem = row.getAttribute('data-doc-item');
        const defaultName = row.getAttribute('data-default-name') || ('document_' + docItem + '.pdf');
        const isKml = (docItem === '18');
        const dummyContent = isKml
          ? '<?xml version="1.0" encoding="UTF-8"?><kml xmlns="http://www.opengis.net/kml/2.2"><Document><name>Quarry Boundary</name></Document></kml>'
          : '%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF';
        const dummyType = isKml ? 'application/xml' : 'application/pdf';
        const blob = new Blob([dummyContent], { type: dummyType });
        const file = new File([blob], defaultName, { type: dummyType });

        await uploadFileToServer(row, file);
      }

      this.disabled = false;
      this.innerHTML = '<i class="bi bi-cloud-check-fill me-1 text-primary"></i> <span id="btn_upload_all_text">All Files Uploaded</span>';
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
      alert('All checklist items are already uploaded!');
      return;
    }

    const files = Array.from(fileList);
    for (let i = 0; i < files.length && i < pendingRows.length; i++) {
      await uploadFileToServer(pendingRows[i], files[i]);
    }
  }
});
</script>

@endsection
