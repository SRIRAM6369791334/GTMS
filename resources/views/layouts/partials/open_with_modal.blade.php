<!-- GTMS Intelligent "Open With..." File Dispatcher Modal -->
<div class="modal fade" id="gtmsOpenWithModal" tabindex="-1" aria-labelledby="gtmsOpenWithModalLabel" aria-hidden="true" style="z-index: 1070;">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
      
      <!-- Modal Header with GTMS Navy Gradient -->
      <div class="modal-header border-0 pb-0" style="background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%); color: #fff; padding: 1.35rem 1.5rem 1.15rem;">
        <div class="d-flex align-items-center gap-3">
          <div id="gtmsOpenWithHeaderIconBox" class="rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 46px; height: 46px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(4px); flex-shrink: 0;">
            <i id="gtmsOpenWithHeaderIcon" class="bi bi-box-arrow-up-right text-warning" style="font-size: 1.55rem;"></i>
          </div>
          <div>
            <div class="d-flex align-items-center gap-2">
              <h5 class="modal-title fw-bold text-white mb-0" id="gtmsOpenWithModalLabel" style="font-size: 1.15rem; letter-spacing: -0.2px;">Open With...</h5>
              <span id="gtmsOpenWithExtBadge" class="badge bg-warning text-dark fw-bold text-uppercase px-2 py-0.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">FILE</span>
            </div>
            <small class="text-white-50" style="font-size: 0.8rem;">Select how you want to open this document</small>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body p-4" style="background: #f8fafc;">
        
        <!-- File Info Summary Box -->
        <div class="p-3 rounded-3 mb-3" style="background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="text-muted small fw-medium text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Attached Document</span>
            <span class="badge bg-light text-secondary border px-2 py-1" id="gtmsOpenWithFileSize" style="font-size: 0.75rem;">-- KB</span>
          </div>
          <div class="fw-bold text-dark text-truncate mb-1" id="gtmsOpenWithDocName" style="font-size: 0.95rem;" title="Document Name">Document Title</div>
          <div class="d-flex align-items-center gap-2 small text-muted">
            <i class="bi bi-paperclip"></i>
            <span class="text-truncate font-monospace" id="gtmsOpenWithFileName" style="max-width: 380px; font-size: 0.8rem;">filename.ext</span>
          </div>
        </div>

        <!-- Dynamic Action Options Container -->
        <div class="mb-3">
          <label class="form-label text-muted small fw-bold text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">
            Choose Application or Action:
          </label>
          <div id="gtmsOpenWithOptionsList" class="d-flex flex-column gap-2">
            <!-- Dynamically populated by gtms-open-with.js -->
          </div>
        </div>

        <!-- Remember Choice & Direct Download Footer -->
        <div class="pt-2 border-top">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <div class="form-check form-check-inline m-0">
              <input class="form-check-input" type="checkbox" id="gtmsRememberChoice" style="cursor: pointer;">
              <label class="form-check-label text-muted small user-select-none" for="gtmsRememberChoice" style="cursor: pointer; font-size: 0.78rem;">
                Remember my choice for this file type
              </label>
            </div>
            <a href="javascript:void(0)" id="gtmsResetChoicesBtn" class="small text-danger text-decoration-none d-none" style="font-size: 0.75rem;">
              <i class="bi bi-arrow-counterclockwise"></i> Reset preference
            </a>
          </div>

          <div class="d-flex gap-2">
            <a href="#" id="gtmsOpenWithDirectDownload" download class="btn btn-sm btn-outline-secondary w-100 py-1.5 d-flex align-items-center justify-content-center gap-1.5" style="font-size: 0.82rem; font-weight: 500;">
              <i class="bi bi-cloud-arrow-down"></i> Just Download File
            </a>
            <button type="button" class="btn btn-sm btn-light border w-auto px-3 py-1.5" data-bs-dismiss="modal" style="font-size: 0.82rem;">
              Cancel
            </button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
/* GTMS Open With Interactive Action Cards */
.gtms-open-option-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.85rem 1rem;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 12px;
  cursor: pointer;
  text-decoration: none !important;
  color: #1e293b;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.gtms-open-option-card:hover {
  border-color: #2563eb;
  background: #f0f7ff;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
  color: #0f1e4d;
}

.gtms-open-option-card.recommended {
  border-color: #93c5fd;
  background: #f8fbff;
}

.gtms-open-option-card.recommended::before {
  content: "RECOMMENDED";
  position: absolute;
  top: 4px;
  right: 8px;
  font-size: 0.62rem;
  font-weight: 700;
  color: #2563eb;
  letter-spacing: 0.4px;
}

.gtms-open-option-icon {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}

.gtms-open-option-card:hover .gtms-open-option-icon {
  transform: scale(1.08);
}

.gtms-open-option-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: #0f1e4d;
  margin-bottom: 2px;
}

.gtms-open-option-desc {
  font-size: 0.77rem;
  color: #64748b;
  line-height: 1.3;
}

.gtms-open-option-arrow {
  color: #94a3b8;
  font-size: 1.1rem;
  transition: all 0.2s ease;
}

.gtms-open-option-card:hover .gtms-open-option-arrow {
  color: #2563eb;
  transform: translateX(3px);
}
</style>
