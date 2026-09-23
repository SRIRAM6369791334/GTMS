@extends('layouts.app')
@section('title', 'Project Handling Persons - Step 6')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div class="wizard-wrap">

      <!-- 8-STEP PROGRESS BAR -->
      <div class="step-progress">
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic Info</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
        <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
        <div class="sp-step active"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
        <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
        <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
      </div>

      <div class="wizard-card shadow-sm border rounded-3 p-4 bg-white">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="wc-eyebrow text-uppercase fw-bold text-primary small">Step 6 of 8</div>
            <h4 class="fw-bold mb-1" style="color:#0F1E4D;">Project Handling Team &amp; In-Charge Persons</h4>
            <div class="wc-sub text-muted small">Assign field officers, surveyors, documentation coordinators, and technical team members responsible for executing this mining lease application.</div>
          </div>
          <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
            <i class="bi bi-people-fill me-1"></i> Multi-User Allocation
          </span>
        </div>

        <form id="handlers_form" method="POST" action="{{ route('step6.save') }}">
          @csrf

          <div class="d-flex align-items-center justify-content-between my-3 pb-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <span class="fw-bold text-navy"><i class="bi bi-person-lines-fill me-1 text-primary"></i> Assigned Personnel</span>
              <span class="badge bg-secondary-subtle text-secondary rounded-pill" id="handler_count_badge">0 members</span>
            </div>
            <button type="button" class="btn btn-sm btn-navy px-3 rounded-2" id="btn_add_handler">
              <i class="bi bi-person-plus-fill me-1 text-warning"></i> + Add Person
            </button>
          </div>

          <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle" id="handlers_table" style="border-color:#e2e8f0;">
              <thead class="bg-light text-navy" style="font-size:0.85rem;">
                <tr>
                  <th style="width: 50px;" class="text-center">#</th>
                  <th style="width: 28%;">Person Name <span class="text-danger">*</span></th>
                  <th style="width: 28%;">Role / Designation <span class="text-danger">*</span></th>
                  <th>Notes &amp; Responsibilities</th>
                  <th style="width: 70px;" class="text-center">Action</th>
                </tr>
              </thead>
              <tbody id="handlers_table_body">
                {{-- Dynamically populated rows --}}
              </tbody>
            </table>
          </div>

          <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 rounded-2 small" style="background:#f0f9ff; border:1px solid #bae6fd; color:#0369a1;">
            <i class="bi bi-info-circle-fill text-primary fs-6"></i>
            <div><strong>Tip:</strong> You can type custom roles manually (e.g. <em>DGPS Surveyor</em>, <em>Mining Geologist</em>, <em>Revenue Liaison</em>, <em>RQP Consultant</em>). All team members will be archived with this dossier.</div>
          </div>

          <!-- Wizard Actions -->
          <div class="wizard-actions d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <a href="{{ route('step5') }}" class="btn btn-outline-secondary btn-sm px-3">
              <i class="bi bi-arrow-left me-1"></i> Back to Documents
            </a>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-primary btn-sm px-3" id="btn_save_draft_exit">
                <i class="fa fa-save me-1"></i> Save Draft &amp; Exit
              </button>
              <button type="submit" class="btn btn-navy btn-sm px-4 fw-semibold" id="btn_submit_step6">
                Continue to Payment <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tableBody = document.getElementById('handlers_table_body');
  const btnAdd = document.getElementById('btn_add_handler');
  const countBadge = document.getElementById('handler_count_badge');
  const form = document.getElementById('handlers_form');
  const btnSaveExit = document.getElementById('btn_save_draft_exit');
  const btnSubmit = document.getElementById('btn_submit_step6');

  // Existing saved handlers from PHP
  const existingHandlers = @json($handlers ?? []);

  function updateRowNumbers() {
    const rows = tableBody.querySelectorAll('tr.handler-row');
    rows.forEach((row, index) => {
      const idxCell = row.querySelector('.row-index');
      if (idxCell) idxCell.textContent = index + 1;

      // Update input names for indexed array submission
      const nameInput = row.querySelector('.input-name');
      const roleInput = row.querySelector('.input-role');
      const notesInput = row.querySelector('.input-notes');

      if (nameInput) nameInput.name = `handlers[${index}][name]`;
      if (roleInput) roleInput.name = `handlers[${index}][role]`;
      if (notesInput) notesInput.name = `handlers[${index}][notes]`;
    });

    countBadge.textContent = `${rows.length} ${rows.length === 1 ? 'member' : 'members'}`;
  }

  function addHandlerRow(data = {}) {
    const row = document.createElement('tr');
    row.className = 'handler-row';
    row.innerHTML = `
      <td class="text-center fw-bold text-muted row-index"></td>
      <td>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="bi bi-person text-primary"></i></span>
          <input type="text" class="form-control input-name" placeholder="Enter full name" value="${data.name || ''}" required>
        </div>
      </td>
      <td>
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white"><i class="bi bi-briefcase text-secondary"></i></span>
          <input type="text" class="form-control input-role" placeholder="e.g. Surveyor, Mining Engineer..." value="${data.role || ''}" required>
        </div>
      </td>
      <td>
        <input type="text" class="form-control form-control-sm input-notes" placeholder="e.g. Site inspection, DGPS coordinates mapping..." value="${data.notes || ''}">
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-outline-danger btn-sm p-1 rounded-2 btn-remove-row" title="Remove person">
          <i class="bi bi-trash3-fill"></i>
        </button>
      </td>
    `;

    tableBody.appendChild(row);
    updateRowNumbers();

    // Auto-focus name field on new manual addition
    if (!data.name) {
      const nameInp = row.querySelector('.input-name');
      if (nameInp) nameInp.focus();
    }
  }

  // Populate initial rows
  if (Array.isArray(existingHandlers) && existingHandlers.length > 0) {
    existingHandlers.forEach(h => addHandlerRow(h));
  } else {
    // Add 1 default empty row
    addHandlerRow();
  }

  // Add Handler Button Click
  btnAdd.addEventListener('click', function () {
    addHandlerRow();
  });

  // Remove Handler Row
  tableBody.addEventListener('click', function (e) {
    const btnRemove = e.target.closest('.btn-remove-row');
    if (btnRemove) {
      const row = btnRemove.closest('tr');
      const rows = tableBody.querySelectorAll('tr.handler-row');
      if (rows.length <= 1) {
        // Clear inputs instead of deleting only row
        row.querySelector('.input-name').value = '';
        row.querySelector('.input-role').value = '';
        row.querySelector('.input-notes').value = '';
        toastr.info('Cleared the row. At least one row is retained.');
        return;
      }
      row.remove();
      updateRowNumbers();
    }
  });

  // AJAX Submission Helper
  function submitStep6(isExit = false) {
    if (!form.reportValidity()) return;

    btnSubmit.disabled = true;
    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span> Saving...`;

    const formData = new FormData(form);
    if (isExit) {
      formData.append('exit', '1');
    }

    fetch("{{ route('step6.save') }}", {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      },
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 1) {
        toastr.success(data.message || 'Handlers saved successfully!');
        window.location.href = data.redirect || "{{ route('step7') }}";
      } else {
        toastr.error(data.message || 'Error saving team information.');
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = `Continue to Payment <i class="bi bi-arrow-right ms-1"></i>`;
      }
    })
    .catch(err => {
      console.error(err);
      toastr.error('Network or server error while saving data.');
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = `Continue to Payment <i class="bi bi-arrow-right ms-1"></i>`;
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitStep6(false);
  });

  btnSaveExit.addEventListener('click', function () {
    submitStep6(true);
  });
});
</script>
@endsection
