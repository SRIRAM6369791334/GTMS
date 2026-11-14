<!-- GTMS Intelligent "Open With..." File Dispatcher Modal
     Element IDs are used by public/js/gtms-open-with.js; keep them stable. -->
<div class="modal fade gow-modal" id="gtmsOpenWithModal" tabindex="-1" aria-labelledby="gtmsOpenWithModalLabel" aria-hidden="true" style="z-index: 1070;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content gow-content">

      <!-- Header: file-type tile, title, extension badge, close -->
      <div class="gow-header">
        <div id="gtmsOpenWithHeaderIconBox" class="gow-type-tile" aria-hidden="true">
          <i id="gtmsOpenWithHeaderIcon" class="bi bi-file-earmark"></i>
        </div>
        <div class="gow-head-text">
          <div class="gow-title-row">
            <h5 class="gow-title" id="gtmsOpenWithModalLabel">Open with…</h5>
            <span id="gtmsOpenWithExtBadge" class="gow-ext-badge">FILE</span>
          </div>
          <p class="gow-subtitle">Choose how you want to open this document</p>
        </div>
        <button type="button" class="gow-close" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="gow-body">

        <!-- File summary -->
        <div class="gow-file">
          <div class="gow-file-top">
            <span class="gow-eyebrow">Attached document</span>
            <span class="gow-size" id="gtmsOpenWithFileSize">--</span>
          </div>
          <div class="gow-doc-name" id="gtmsOpenWithDocName" title="Document name">Document Title</div>
          <div class="gow-file-name">
            <i class="bi bi-paperclip" aria-hidden="true"></i>
            <span id="gtmsOpenWithFileName">filename.ext</span>
          </div>
        </div>

        <!-- STATE 1: choose an action -->
        <div id="gtmsOpenWithChoose">
          <!-- Actions (filled by gtms-open-with.js) -->
          <div class="gow-eyebrow gow-section-label" id="gtmsOpenWithOptionsLabel">Choose an action</div>
          <div id="gtmsOpenWithOptionsList" class="gow-options" role="list" aria-labelledby="gtmsOpenWithOptionsLabel">
            <!-- Dynamically populated -->
          </div>

          <!-- Remember choice -->
          <div class="gow-remember">
            <div class="form-check m-0">
              <input class="form-check-input" type="checkbox" id="gtmsRememberChoice">
              <label class="form-check-label" for="gtmsRememberChoice">
                Remember my choice for this file type
              </label>
            </div>
            <a href="javascript:void(0)" id="gtmsResetChoicesBtn" class="gow-reset d-none">
              <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i> Reset preference
            </a>
          </div>
        </div>

        <!-- STATE 2: "tap to open" (desktop apps). Browsers cannot launch a
             desktop program themselves, so the file is downloaded and the user
             taps it once in the browser's download list. -->
        <div id="gtmsOpenWithReady" class="gow-ready" hidden aria-live="polite">
          <div class="gow-ready-head">
            <div class="gow-ready-check" aria-hidden="true"><i class="bi bi-check-lg"></i></div>
            <div>
              <div class="gow-ready-title">Download started</div>
              <div class="gow-ready-sub">Now tap the file once to open it in <b id="gtmsReadyApp">your app</b>.</div>
            </div>
          </div>

          <ol class="gow-steps">
            <li>
              <span class="gow-step-no">1</span>
              <div>
                Open your browser's downloads:
                <span class="gow-kbd-row">
                  <span class="gow-pill"><i class="bi bi-download" aria-hidden="true"></i> icon at the top-right</span>
                  <span>or press</span>
                  <kbd class="gow-kbd" id="gtmsReadyShortcut">Ctrl + J</kbd>
                </span>
              </div>
            </li>
            <li>
              <span class="gow-step-no">2</span>
              <div>Tap <b class="gow-file-chip" id="gtmsReadyFile">file</b> — it opens in <b id="gtmsReadyApp2">your app</b>.</div>
            </li>
          </ol>

          <details class="gow-tip">
            <summary><i class="bi bi-lightning-charge" aria-hidden="true"></i> Open automatically next time</summary>
            <div class="gow-tip-body">
              In the downloads list, click <b>⋮</b> next to this file and choose
              <b>"Always open files of this type"</b>. Make sure <b id="gtmsReadyApp3">your app</b>
              is the default app for <b id="gtmsReadyExt">.file</b> files in Windows
              (right-click a file → Open with → Choose another app → Always).
            </div>
          </details>
        </div>
      </div>

      <!-- Footer -->
      <div class="gow-footer" id="gtmsOpenWithFooterChoose">
        <button type="button" class="gow-btn gow-btn-ghost" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="gtmsOpenWithDirectDownload" download class="gow-btn gow-btn-outline">
          <i class="bi bi-download" aria-hidden="true"></i> Download file
        </a>
      </div>
      <div class="gow-footer" id="gtmsOpenWithFooterReady" hidden>
        <button type="button" class="gow-btn gow-btn-ghost" id="gtmsReadyBack">
          <i class="bi bi-arrow-left" aria-hidden="true"></i> Other options
        </button>
        <button type="button" class="gow-btn gow-btn-ghost" id="gtmsReadyAgain">
          <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Download again
        </button>
        <button type="button" class="gow-btn gow-btn-primary" data-bs-dismiss="modal">Done</button>
      </div>

    </div>
  </div>
</div>

<style>
/* ==========================================================================
   OPEN WITH MODAL (gow-*)
   Own classes so the global .modal-header/.btn-secondary theme rules
   (secondary = pink in this theme) cannot wash it out.
   ========================================================================== */
.gow-modal {
  --gow-navy: #0F1E4D;
  --gow-blue: #2563EB;
  --gow-text: #0F172A;
  --gow-text-2: #475569;
  --gow-text-3: #64748B;
  --gow-surface: #FFFFFF;
  --gow-surface-2: #F8FAFC;
  --gow-border: #E2E8F0;
  --gow-border-2: #CBD5E1;
  --gow-tile-bg: #EFF6FF;
  --gow-tile-fg: #2563EB;
}
body[data-theme-version="dark"] .gow-modal {
  --gow-text: #E2E8F0;
  --gow-text-2: #A8B4C8;
  --gow-text-3: #8391A9;
  --gow-surface: #111A2E;
  --gow-surface-2: #16213A;
  --gow-border: #24314F;
  --gow-border-2: #33446A;
}

.gow-modal .modal-dialog { max-width: 500px; }
@media (max-width: 575.98px) {
  .gow-modal .modal-dialog { margin: 0.75rem; }
}

.gow-modal .gow-content {
  background: var(--gow-surface) !important;
  border: 1px solid var(--gow-border) !important;
  border-radius: 18px !important;
  box-shadow: 0 24px 48px rgba(15, 30, 77, 0.22) !important;
  overflow: hidden;
  color: var(--gow-text);
}

/* Header */
.gow-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 20px 16px 22px;
  border-bottom: 1px solid var(--gow-border);
  background: var(--gow-surface);
}
.gow-type-tile {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.45rem;
  background: var(--gow-tile-bg);
  color: var(--gow-tile-fg);
  box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
}
.gow-head-text { flex: 1; min-width: 0; }
.gow-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.gow-title {
  font-size: 1.12rem;
  font-weight: 700;
  color: var(--gow-text) !important;
  margin: 0;
  letter-spacing: -0.01em;
}
.gow-ext-badge {
  display: inline-flex;
  align-items: center;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  padding: 3px 8px;
  border-radius: 999px;
  background: var(--gow-tile-bg);
  color: var(--gow-tile-fg);
  border: 1px solid rgba(15, 23, 42, 0.06);
}
.gow-subtitle {
  margin: 3px 0 0;
  font-size: 0.82rem;
  color: var(--gow-text-3);
}
.gow-close {
  width: 36px;
  height: 36px;
  flex-shrink: 0;
  align-self: flex-start;
  border: 0;
  border-radius: 10px;
  background: transparent;
  color: var(--gow-text-3);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
}
.gow-close:hover { background: var(--gow-surface-2); color: var(--gow-text); }
.gow-close:active { transform: scale(0.92); }
.gow-close:focus-visible { outline: 2px solid var(--gow-blue); outline-offset: 2px; }

/* Body */
.gow-body {
  padding: 18px 22px 6px;
  background: var(--gow-surface-2);
}
.gow-eyebrow {
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: var(--gow-text-3);
}
.gow-file {
  background: var(--gow-surface);
  border: 1px solid var(--gow-border);
  border-radius: 12px;
  padding: 14px 16px;
  margin-bottom: 18px;
}
.gow-file-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 6px;
}
.gow-size {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--gow-text-2);
  background: var(--gow-surface-2);
  border: 1px solid var(--gow-border);
  border-radius: 999px;
  padding: 2px 9px;
  white-space: nowrap;
  font-variant-numeric: tabular-nums;
}
.gow-doc-name {
  font-size: 0.98rem;
  font-weight: 700;
  color: var(--gow-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.gow-file-name {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
  font-size: 0.8rem;
  color: var(--gow-text-2);
  min-width: 0;
}
.gow-file-name span {
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.gow-section-label { margin-bottom: 8px; }

.gow-options { display: flex; flex-direction: column; gap: 10px; }

/* Option cards (built in JS) */
.gtms-open-option-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  min-height: 64px;
  padding: 12px 14px;
  background: var(--gow-surface, #fff);
  border: 1.5px solid var(--gow-border, #e2e8f0);
  border-radius: 12px;
  cursor: pointer;
  text-decoration: none !important;
  color: var(--gow-text, #0f172a);
  position: relative;
  transition: border-color 0.15s ease, background-color 0.15s ease, box-shadow 0.2s ease, transform 0.15s ease;
}
.gtms-open-option-card:hover {
  border-color: var(--gow-blue, #2563eb);
  background: #F5F9FF;
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.12);
  transform: translateY(-1px);
  color: var(--gow-text, #0f172a);
}
.gtms-open-option-card:active { transform: scale(0.99); }
.gtms-open-option-card:focus-visible {
  outline: none;
  border-color: var(--gow-blue, #2563eb);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
}
body[data-theme-version="dark"] .gtms-open-option-card:hover { background: rgba(37, 99, 235, 0.12); }
.gtms-open-option-card.recommended {
  border-color: #93C5FD;
  background: #F8FBFF;
}
body[data-theme-version="dark"] .gtms-open-option-card.recommended { background: rgba(37, 99, 235, 0.08); }
.gtms-open-option-card .gow-rec {
  display: inline-block;
  margin-left: 6px;
  font-size: 0.62rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #1D4ED8;
  background: #DBEAFE;
  border-radius: 999px;
  padding: 1px 7px;
  vertical-align: 1px;
}
.gtms-open-option-icon {
  width: 42px;
  height: 42px;
  border-radius: 11px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
  transition: transform 0.2s ease;
}
.gtms-open-option-card:hover .gtms-open-option-icon { transform: scale(1.06); }
.gtms-open-option-title {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--gow-text, #0f172a);
  line-height: 1.3;
}
.gtms-open-option-desc {
  font-size: 0.78rem;
  color: var(--gow-text-3, #64748b);
  line-height: 1.35;
  margin-top: 2px;
}
.gtms-open-option-arrow {
  color: var(--gow-text-3, #94a3b8);
  font-size: 1rem;
  flex-shrink: 0;
  transition: transform 0.2s ease, color 0.15s ease;
}
.gtms-open-option-card:hover .gtms-open-option-arrow { color: var(--gow-blue, #2563eb); transform: translateX(3px); }

/* Remember row */
.gow-remember {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 8px;
  padding: 14px 0 12px;
}
.gow-remember .form-check-label {
  font-size: 0.82rem;
  color: var(--gow-text-2);
  cursor: pointer;
  user-select: none;
}
.gow-remember .form-check-input { cursor: pointer; margin-top: 0.2em; }
.gow-reset {
  font-size: 0.78rem;
  font-weight: 600;
  color: #B91C1C;
  text-decoration: none;
}
.gow-reset:hover { text-decoration: underline; color: #991B1B; }

/* Footer */
.gow-footer {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 14px 22px 18px;
  background: var(--gow-surface-2);
  border-top: 1px solid var(--gow-border);
}
.gow-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  min-height: 42px;
  padding: 0 18px;
  font-size: 0.86rem;
  font-weight: 600;
  border-radius: 10px;
  border: 1.5px solid transparent;
  text-decoration: none;
  cursor: pointer;
  transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
}
.gow-btn:active { transform: scale(0.97); }
.gow-btn:focus-visible { outline: 2px solid var(--gow-blue); outline-offset: 2px; }
.gow-btn-ghost {
  background: var(--gow-surface);
  border-color: var(--gow-border-2);
  color: var(--gow-text-2);
}
.gow-btn-ghost:hover { background: var(--gow-surface-2); color: var(--gow-text); border-color: var(--gow-text-3); }
.gow-btn-outline {
  background: var(--gow-surface);
  border-color: var(--gow-navy);
  color: var(--gow-navy);
}
.gow-btn-outline:hover { background: var(--gow-navy); color: #fff; }
body[data-theme-version="dark"] .gow-btn-outline { color: #93C5FD; border-color: #3B5BA9; }
body[data-theme-version="dark"] .gow-btn-outline:hover { color: #fff; background: #3B5BDB; }
.gow-btn-primary {
  background: var(--gow-navy);
  border-color: var(--gow-navy);
  color: #fff;
  box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2);
}
.gow-btn-primary:hover { background: #1E3A8A; border-color: #1E3A8A; color: #fff; }
.gow-footer[hidden], .gow-ready[hidden], #gtmsOpenWithChoose[hidden] { display: none !important; }
@media (max-width: 575.98px) {
  .gow-footer { flex-direction: column-reverse; }
  .gow-btn { width: 100%; }
}

/* "Tap to open" state */
.gow-ready { padding-bottom: 14px; animation: gowIn 0.25s ease both; }
@keyframes gowIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: none; } }
.gow-ready-head {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border-radius: 12px;
  background: #F0FDF4;
  border: 1px solid #BBF7D0;
  margin-bottom: 14px;
}
body[data-theme-version="dark"] .gow-ready-head { background: rgba(34, 197, 94, 0.1); border-color: rgba(34, 197, 94, 0.3); }
.gow-ready-check {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #16A34A;
  color: #fff;
  font-size: 1.15rem;
  animation: gowPop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1) both;
}
@keyframes gowPop { from { transform: scale(0.4); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.gow-ready-title { font-weight: 700; font-size: 0.95rem; color: #166534; }
body[data-theme-version="dark"] .gow-ready-title { color: #86EFAC; }
.gow-ready-sub { font-size: 0.82rem; color: var(--gow-text-2); margin-top: 2px; }

.gow-steps { list-style: none; margin: 0 0 12px; padding: 0; display: flex; flex-direction: column; gap: 10px; }
.gow-steps li {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  background: var(--gow-surface);
  border: 1px solid var(--gow-border);
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 0.86rem;
  color: var(--gow-text);
  line-height: 1.5;
}
.gow-step-no {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--gow-navy);
  color: #fff;
  font-size: 0.75rem;
  font-weight: 700;
  margin-top: 1px;
}
.gow-kbd-row { display: inline-flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 4px; }
.gow-pill {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 0.78rem; font-weight: 600;
  background: var(--gow-surface-2); border: 1px solid var(--gow-border);
  border-radius: 999px; padding: 2px 9px; color: var(--gow-text-2);
}
.gow-kbd {
  font-family: inherit;
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--gow-text);
  background: var(--gow-surface);
  border: 1px solid var(--gow-border-2);
  border-bottom-width: 3px;
  border-radius: 6px;
  padding: 1px 8px;
  box-shadow: none;
}
.gow-file-chip {
  font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
  font-size: 0.8rem;
  font-weight: 600;
  background: var(--gow-tile-bg);
  color: var(--gow-tile-fg);
  border-radius: 6px;
  padding: 1px 6px;
  overflow-wrap: anywhere;
}
.gow-tip {
  border: 1px dashed var(--gow-border-2);
  border-radius: 10px;
  padding: 10px 14px;
  font-size: 0.82rem;
  color: var(--gow-text-2);
  background: var(--gow-surface);
}
.gow-tip summary { cursor: pointer; font-weight: 600; color: var(--gow-text); list-style: none; }
.gow-tip summary::-webkit-details-marker { display: none; }
.gow-tip summary i { color: #D97706; }
.gow-tip[open] summary { margin-bottom: 6px; }
.gow-tip-body { line-height: 1.55; }
</style>
