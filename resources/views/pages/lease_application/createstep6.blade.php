@extends('layouts.app')
@section('title', 'Project Handling Persons - Step 6')
@section('main_content')

<div class="content-body default-height">
  <div class="container-fluid">
    <div id="gtms-step6-page">
      <div class="wizard-wrap">

        <!-- 8-STEP PROGRESS BAR (Exact Native Stepper Parity with Steps 1-5) -->
        <div class="step-progress">
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Application</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Basic &amp; MIMAS</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Category</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Folders</div></div>
          <div class="sp-step done"><div class="circ"><i class="bi bi-check-lg"></i></div><div class="sp-label">Documents</div></div>
          <div class="sp-step active"><div class="circ">6</div><div class="sp-label">Handlers</div></div>
          <div class="sp-step"><div class="circ">7</div><div class="sp-label">Payment</div></div>
          <div class="sp-step"><div class="circ">8</div><div class="sp-label">Review</div></div>
        </div>

        <div class="card s6-main-card wizard-card">
          <!-- CARD HEADER -->
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
              <div class="s6-eyebrow text-uppercase fw-bold text-primary">Step 6 of 8 &bull; Project Execution Team</div>
              <h4 class="s6-title mb-1">Project Handling Team &amp; In-Charge Persons</h4>
              <div class="s6-subtitle">Assign field officers, surveyors, documentation coordinators, and technical team members responsible for executing this mining lease application.</div>
            </div>
            <span class="badge s6-team-pill">
              <i class="bi bi-people-fill me-1.5"></i>Multi-User Allocation
            </span>
          </div>

          <!-- SUMMARY / TEAM READINESS BANNER -->
          <div class="s6-banner-card mb-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="s6-banner-icon-badge">
                  <i class="bi bi-person-badge-fill"></i>
                </div>
                <div>
                  <div class="s6-banner-title fw-bold">Statutory Personnel Assignment</div>
                  <div class="s6-banner-desc text-muted">Designate internal team members, empanelled RQP consultants, and field surveyors for regulatory liaison.</div>
                </div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge s6-count-pill" id="handler_count_banner">1 Member Allocated</span>
              </div>
            </div>
          </div>

          <form id="handlers_form" method="POST" action="{{ route('step6.save') }}">
            @csrf

            <!-- TABLE CONTAINER CARD -->
            <div class="s6-table-card mb-3">
              <div class="s6-table-header-bar d-flex align-items-center justify-content-between px-3 py-2.5">
                <div class="d-flex align-items-center gap-2 m-3">
                  <span class="s6-table-title fw-bold">
                    <i class="bi bi-person-lines-fill me-1.5 text-primary"></i>Assigned Personnel
                  </span>
                  <span class="badge s6-badge-counter rounded-pill" id="handler_count_badge">1 member</span>
                </div>
                <button type="button" class="btn s6-btn-add-person" id="btn_add_handler">
                  <i class="bi bi-person-plus-fill me-1.5"></i>+ Add Person
                </button>
              </div>

              <div class="table-responsive">
                <table class="table s6-table align-middle mb-0" id="handlers_table">
                  <thead>
                    <tr>
                      <th style="width: 55px;" class="text-center">#</th>
                      <th style="width: 28%;">Person Name <span class="text-danger">*</span></th>
                      <th style="width: 28%;">Role / Designation <span class="text-danger">*</span></th>
                      <th>Notes &amp; Responsibilities</th>
                      <th style="width: 75px;" class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody id="handlers_table_body">
                    {{-- Dynamically populated rows --}}
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Role Autocomplete Suggestions Datalist -->
            <datalist id="role_suggestions">
              <option value="DGPS Surveyor"></option>
              <option value="Mining Geologist"></option>
              <option value="Documentation Coordinator"></option>
              <option value="Revenue Liaison Officer"></option>
              <option value="RQP Consultant (Mining Plan)"></option>
              <option value="Environmental Consultant (EC)"></option>
              <option value="Drone Survey Pilot"></option>
              <option value="Field Inspection Officer"></option>
              <option value="Legal &amp; Statutory Advisor"></option>
              <option value="Accounts &amp; Billing In-Charge"></option>
            </datalist>

            <!-- PRO TIP CALLOUT BOX -->
            <div class="s6-tip-box d-flex align-items-start gap-2.5 p-3 rounded-3 mb-4">
              <div class="s6-tip-icon flex-shrink-0">
                <i class="bi bi-lightbulb-fill"></i>
              </div>
              <div class="s6-tip-content">
                <strong>Pro Tip:</strong> You can select a standard role from the suggestions or type any custom designation (e.g. <em>DGPS Surveyor</em>, <em>Mining Geologist</em>, <em>Revenue Liaison</em>, <em>RQP Consultant</em>). All allocated personnel are permanently archived with this application's audit dossier.
              </div>
            </div>

            <!-- WIZARD ACTIONS BAR -->
            <div class="s6-actions d-flex justify-content-between align-items-center pt-3 border-top">
              <a href="{{ route('step5') }}" class="btn s6-btn-back">
                <i class="bi bi-arrow-left me-2"></i>Back to Documents
              </a>
              <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn s6-btn-draft" id="btn_save_draft_exit">
                  <i class="bi bi-floppy me-2"></i>Save Draft &amp; Exit
                </button>
                <button type="submit" class="btn s6-btn-continue" id="btn_submit_step6">
                  Continue to Payment <i class="bi bi-arrow-right ms-2"></i>
                </button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<!-- SCOPED CSS FOR STEP 6: ZERO CSS LEAKAGE, 100% LIGHT & DARK MODE COMPLIANT -->
<style>
  /* ==========================================================================
     LIGHT MODE STYLES (Strictly Scoped to #gtms-step6-page)
     ========================================================================== */
  #gtms-step6-page {
    max-width: 860px;
    margin: 0 auto;
    padding: 12px 10px 48px;
  }

  /* STEPPER PROGRESS TUNING */
  #gtms-step6-page .step-progress {
    margin-bottom: 22px;
  }

  #gtms-step6-page .step-progress .sp-label {
    font-size: 0.70rem;
    font-weight: 600;
    color: #475569;
    letter-spacing: 0.02em;
  }

  #gtms-step6-page .step-progress .sp-step.active .sp-label {
    color: #0F1E4D;
    font-weight: 700;
  }

  #gtms-step6-page .step-progress .sp-step.done .sp-label {
    color: #059669;
  }

  #gtms-step6-page .step-progress .sp-step.done .circ {
    background-color: #059669;
    border-color: #059669;
    color: #ffffff;
  }

  #gtms-step6-page .step-progress .sp-step.active .circ {
    background-color: #0F1E4D;
    border-color: #0F1E4D;
    color: #ffffff;
    box-shadow: 0 0 0 4px rgba(15, 30, 77, 0.15);
  }

  /* MAIN WIZARD CARD */
  #gtms-step6-page .s6-main-card {
    border-radius: 16px;
    border: 1.5px solid #e2e8f0;
    background: #ffffff;
    padding: 28px 32px;
    box-shadow: 0 4px 20px -2px rgba(15, 30, 77, 0.05);
  }

  #gtms-step6-page .s6-eyebrow {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #f59e0b !important;
    margin-bottom: 4px;
  }

  #gtms-step6-page .s6-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0F1E4D;
    letter-spacing: -0.02em;
  }

  #gtms-step6-page .s6-subtitle {
    font-size: 0.82rem;
    color: #64748b;
    line-height: 1.45;
  }

  #gtms-step6-page .s6-team-pill {
    background: #e0e7ff;
    color: #3730a3;
    border: 1px solid #c7d2fe;
    font-weight: 600;
    font-size: 0.76rem;
    padding: 6px 14px;
    border-radius: 20px;
    letter-spacing: 0.02em;
  }

  /* TEAM READINESS BANNER */
  #gtms-step6-page .s6-banner-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 18px;
  }

  #gtms-step6-page .s6-banner-icon-badge {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #e0e7ff;
    color: #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
  }

  #gtms-step6-page .s6-banner-title {
    font-size: 0.88rem;
    color: #0F1E4D;
    margin-bottom: 2px;
  }

  #gtms-step6-page .s6-banner-desc {
    font-size: 0.76rem;
    color: #64748b;
    line-height: 1.35;
  }

  #gtms-step6-page .s6-count-pill {
    background: #0F1E4D;
    color: #ffffff;
    font-weight: 600;
    font-size: 0.76rem;
    padding: 6px 12px;
    border-radius: 8px;
    letter-spacing: 0.02em;
  }

  /* PERSONNEL TABLE CARD */
  #gtms-step6-page .s6-table-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    background: #ffffff;
  }

  #gtms-step6-page .s6-table-header-bar {
    background: #f8fafc;
    border-bottom: 1.5px solid #e2e8f0;
  }

  #gtms-step6-page .s6-table-title {
    font-size: 0.88rem;
    color: #1e293b;
  }

  #gtms-step6-page .s6-badge-counter {
    background: #e2e8f0;
    color: #475569;
    font-weight: 600;
    font-size: 0.74rem;
    padding: 4px 10px;
  }

  #gtms-step6-page .s6-btn-add-person {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    border: none;
    font-weight: 600;
    font-size: 0.80rem;
    padding: 6px 14px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(15, 30, 77, 0.18);
    transition: all 0.2s ease;
  }

  #gtms-step6-page .s6-btn-add-person:hover {
    background: linear-gradient(135deg, #0a1435 0%, #172e6e 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(15, 30, 77, 0.28);
  }

  /* TABLE ROWS & INPUTS */
  #gtms-step6-page .s6-table {
    border-collapse: collapse;
  }

  #gtms-step6-page .s6-table thead th {
    background: #f8fafc;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
    padding: 10px 14px;
    border-bottom: 1.5px solid #e2e8f0;
    border-top: none;
  }

  #gtms-step6-page .s6-table tbody td {
    padding: 10px 14px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }

  #gtms-step6-page .s6-table tbody tr.handler-row:hover {
    background-color: #fafbfd;
  }

  /* SEAMLESS UNIFIED INPUT GROUP */
  #gtms-step6-page .s6-input-group {
    display: flex;
    align-items: center;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    overflow: hidden;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-step6-page .s6-input-group:focus-within {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
  }

  #gtms-step6-page .s6-input-icon {
    padding: 0 10px;
    color: #64748b;
    font-size: 0.90rem;
    background: transparent;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  #gtms-step6-page .s6-table-input {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
    font-size: 0.84rem !important;
    color: #1e293b !important;
    padding: 6px 10px 6px 0 !important;
    height: 34px !important;
    width: 100% !important;
    outline: none !important;
  }

  #gtms-step6-page .s6-table-input::placeholder {
    color: #94a3b8;
    font-size: 0.82rem;
  }

  /* NOTES INPUT */
  #gtms-step6-page .s6-notes-group {
    display: flex;
    align-items: center;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    overflow: hidden;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }

  #gtms-step6-page .s6-notes-group:focus-within {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
  }

  /* ROW INDEX SQUIRCLE */
  #gtms-step6-page .s6-row-index {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    background: #f1f5f9;
    color: #475569;
    font-weight: 700;
    font-size: 0.78rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
  }

  /* ACTION REMOVE BUTTON */
  #gtms-step6-page .s6-btn-remove-row {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1.5px solid #fecdd3;
    background: #fff1f2;
    color: #e11d48;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    transition: all 0.15s ease;
  }

  #gtms-step6-page .s6-btn-remove-row:hover {
    background: #e11d48;
    border-color: #e11d48;
    color: #ffffff;
    transform: scale(1.05);
  }

  /* PRO TIP BOX */
  #gtms-step6-page .s6-tip-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1e40af;
    font-size: 0.80rem;
    line-height: 1.45;
  }

  #gtms-step6-page .s6-tip-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #dbeafe;
    color: #2563eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
  }

  /* WIZARD ACTIONS */
  #gtms-step6-page .s6-actions {
    border-top: 1.5px solid #e2e8f0 !important;
  }

  #gtms-step6-page .s6-btn-back {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #475569;
    font-weight: 600;
    font-size: 0.84rem;
    padding: 8px 18px;
    border-radius: 8px;
    transition: all 0.15s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  #gtms-step6-page .s6-btn-back:hover {
    background: #f1f5f9;
    color: #1e293b;
    border-color: #94a3b8;
  }

  #gtms-step6-page .s6-btn-draft {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #0F1E4D;
    font-weight: 600;
    font-size: 0.84rem;
    padding: 8px 18px;
    border-radius: 8px;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
  }

  #gtms-step6-page .s6-btn-draft:hover {
    background: #f8fafc;
    border-color: #94a3b8;
    color: #0F1E4D;
  }

  #gtms-step6-page .s6-btn-continue {
    background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);
    color: #ffffff;
    border: none;
    font-weight: 600;
    font-size: 0.86rem;
    padding: 8px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
  }

  #gtms-step6-page .s6-btn-continue:hover {
    background: linear-gradient(135deg, #0a1435 0%, #172e6e 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(15, 30, 77, 0.3);
  }

  /* ==========================================================================
     DARK MODE PARITY (100% WCAG AAA Compliant)
     ========================================================================== */
  body[data-theme-version="dark"] #gtms-step6-page .step-progress .sp-label {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .step-progress .sp-step.active .sp-label {
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .step-progress .sp-step.active .circ {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.25) !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-main-card {
    background: #1a1b1d !important;
    border-color: #2e3440 !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5) !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-title {
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-subtitle {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-team-pill {
    background: rgba(99, 102, 241, 0.15) !important;
    color: #a5b4fc !important;
    border-color: rgba(99, 102, 241, 0.3) !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-banner-card {
    background: #18191a !important;
    border-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-banner-icon-badge {
    background: rgba(37, 99, 235, 0.18) !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-banner-title {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-banner-desc {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-count-pill {
    background: #2563eb !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table-card {
    background: #1a1b1d !important;
    border-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table-header-bar {
    background: #212327 !important;
    border-bottom-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table-title {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-badge-counter {
    background: #25282a !important;
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-add-person {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table thead th {
    background: #212327 !important;
    color: #94a3b8 !important;
    border-bottom-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table tbody td {
    border-bottom-color: #25282a !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table tbody tr.handler-row:hover {
    background-color: #1e2024 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-input-group,
  body[data-theme-version="dark"] #gtms-step6-page .s6-notes-group {
    background: #161717 !important;
    border-color: #3b4252 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-input-group:focus-within,
  body[data-theme-version="dark"] #gtms-step6-page .s6-notes-group:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-input-icon {
    color: #94a3b8 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table-input {
    color: #f1f5f9 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-table-input::placeholder {
    color: #64748b !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-row-index {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-remove-row {
    background: rgba(244, 63, 94, 0.12) !important;
    border-color: rgba(244, 63, 94, 0.35) !important;
    color: #fb7185 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-remove-row:hover {
    background: #e11d48 !important;
    border-color: #e11d48 !important;
    color: #ffffff !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-tip-box {
    background: rgba(37, 99, 235, 0.10) !important;
    border-color: rgba(37, 99, 235, 0.25) !important;
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-tip-icon {
    background: rgba(37, 99, 235, 0.2) !important;
    color: #60a5fa !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-actions {
    border-top-color: #2e3440 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-back {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #cbd5e1 !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-back:hover {
    background: #32373d !important;
    color: #ffffff !important;
    border-color: #64748b !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-draft {
    background: #25282a !important;
    border-color: #3b4252 !important;
    color: #93c5fd !important;
  }

  body[data-theme-version="dark"] #gtms-step6-page .s6-btn-draft:hover {
    background: #32373d !important;
    color: #ffffff !important;
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const tableBody = document.getElementById('handlers_table_body');
  const btnAdd = document.getElementById('btn_add_handler');
  const countBadge = document.getElementById('handler_count_badge');
  const countBanner = document.getElementById('handler_count_banner');
  const form = document.getElementById('handlers_form');
  const btnSaveExit = document.getElementById('btn_save_draft_exit');
  const btnSubmit = document.getElementById('btn_submit_step6');

  // Existing saved handlers from PHP
  const existingHandlers = @json($handlers ?? []);

  function updateRowNumbers() {
    const rows = tableBody.querySelectorAll('tr.handler-row');
    rows.forEach((row, index) => {
      const idxCell = row.querySelector('.s6-row-index');
      if (idxCell) idxCell.textContent = index + 1;

      // Update input names for indexed array submission
      const nameInput = row.querySelector('.input-name');
      const roleInput = row.querySelector('.input-role');
      const notesInput = row.querySelector('.input-notes');

      if (nameInput) nameInput.name = `handlers[${index}][name]`;
      if (roleInput) roleInput.name = `handlers[${index}][role]`;
      if (notesInput) notesInput.name = `handlers[${index}][notes]`;
    });

    const memberStr = `${rows.length} ${rows.length === 1 ? 'member' : 'members'}`;
    const allocStr = `${rows.length} ${rows.length === 1 ? 'Member Allocated' : 'Members Allocated'}`;
    if (countBadge) countBadge.textContent = memberStr;
    if (countBanner) countBanner.textContent = allocStr;
  }

  function addHandlerRow(data = {}) {
    const row = document.createElement('tr');
    row.className = 'handler-row';
    row.innerHTML = `
      <td class="text-center">
        <span class="s6-row-index row-index"></span>
      </td>
      <td>
        <div class="s6-input-group">
          <span class="s6-input-icon"><i class="bi bi-person text-primary"></i></span>
          <input type="text" class="form-control s6-table-input input-name" placeholder="Enter full name" value="${data.name || ''}" required autocomplete="off">
        </div>
      </td>
      <td>
        <div class="s6-input-group">
          <span class="s6-input-icon"><i class="bi bi-briefcase text-secondary"></i></span>
          <input type="text" class="form-control s6-table-input input-role" list="role_suggestions" placeholder="e.g. Surveyor, Mining Engineer..." value="${data.role || ''}" required autocomplete="off">
        </div>
      </td>
      <td>
        <div class="s6-notes-group">
          <span class="s6-input-icon"><i class="bi bi-card-text text-muted"></i></span>
          <input type="text" class="form-control s6-table-input input-notes" placeholder="e.g. Site inspection, DGPS mapping, MIMAS liaison..." value="${data.notes || ''}">
        </div>
      </td>
      <td class="text-center">
        <button type="button" class="btn s6-btn-remove-row btn-remove-row" title="Remove person">
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
  if (btnAdd) {
    btnAdd.addEventListener('click', function () {
      addHandlerRow();
    });
  }

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
        if (typeof toastr !== 'undefined') {
          toastr.info('Cleared the row. At least one row is retained.');
        }
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
    const origSubmitHtml = btnSubmit.innerHTML;
    btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1.5"></span> Saving...`;

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
        if (typeof toastr !== 'undefined') {
          toastr.success(data.message || 'Handlers saved successfully!');
        }
        window.location.href = data.redirect || "{{ route('step7') }}";
      } else {
        if (typeof toastr !== 'undefined') {
          toastr.error(data.message || 'Error saving team information.');
        }
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = origSubmitHtml;
      }
    })
    .catch(err => {
      console.error(err);
      if (typeof toastr !== 'undefined') {
        toastr.error('Network or server error while saving data.');
      }
      btnSubmit.disabled = false;
      btnSubmit.innerHTML = origSubmitHtml;
    });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    submitStep6(false);
  });

  if (btnSaveExit) {
    btnSaveExit.addEventListener('click', function () {
      submitStep6(true);
    });
  }
});
</script>
@endsection
