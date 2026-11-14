# GTMS design system (as built on the Lease module)

## Global layer — loaded on every `layouts.app` page

Wired in `resources/views/layouts/app.blade.php` (do not add again per page):

| File | What it gives |
|---|---|
| `public/css/gtms-ui.css` | Tokens (`--gt-*`, light + dark), card/button/input/table/dropdown polish, button ripple + loading spinner, input focus ring + label tint, `.gt-shake`, SweetAlert dialog theme (`.gt-swal`, `.gt-swal-danger`), compact toasts (no page dimming), **half-step utilities** (`mb-1.5`, `gap-2.5`, `p-3.5` … Bootstrap lacks them), header title ellipsis, validation styles (`.gt-v-hint`, `.error-text`, `.input-group.gt-v-invalid`), reduced-motion |
| `public/js/gtms-ui.js` | `GTMS.toast(icon, text, ms)`, `GTMS.confirm(msg, {title, confirmText, danger}) → Promise<bool>`; converts `onclick="return confirm('…')"` / `onsubmit=…` and `[data-confirm]` into SweetAlert; submit-button loading state; flash messages (`session('success'|'error'|'warning'|'info'|'status')` + `$errors`) as toasts and removes the duplicate inline alert |
| `public/js/gtms-validate.js` | Field rules via `data-v`, see below. `GTMSValidate.validateForm(form)`, `.showErrors(form, errors)`, `.check(el)`, `.prepare(root)` |
| `layouts/partials/open_with_modal.blade.php` + `public/js/gtms-open-with.js` | "Open with…" file dispatcher (view in tab / desktop app / download), remembered choice per extension, "Tap to open" screen for desktop apps |

Flash data is passed as `window.GTMS_FLASH` in the layout.

## Tokens

`--gt-primary #0F1E4D` (navy), `--gt-primary-600 #1E3A8A`, `--gt-primary-500 #2563EB`, `--gt-accent #F28C28`, success `#15803D`, warning `#B45309`, danger `#DC2626`, text `#0F172A / #475569 / #64748B` (muted text must be ≥ #64748B on white; #94A3B8 is only 2.6:1), border `#E2E8F0 / #CBD5E1`, surface `#FFF / #F8FAFC`. Violet for Free/waived: `#6D28D9` on `#F5F3FF`, border `#DDD6FE`.

Theme gotchas: this Dexignlabs theme's `secondary` is **pink** — never use `text-secondary`, `btn-outline-secondary`, `bg-secondary` for neutral UI; use slate tokens. `text-primary` is navy with `!important` and overrides component colours.

## Module layer — `public/css/gtms-lease.css` (`.lz-*`)

Linked per page: `@push('styles') <link href="/css/gtms-lease.css?v=2" rel="stylesheet"> @endpush`. Wrapper gets `.lz-page` (max-width 900px; review/table pages override, e.g. `#gtms-step8-page.lz-page{max-width:1180px}`).

| Class | Use |
|---|---|
| `.step-progress .sp-step(.done/.active) .circ .sp-label` | Stepper card; active step pulses; labels hide on mobile except active |
| `.lz-card` | Main wizard card (rise-in animation) |
| `.lz-head` `.lz-eyebrow` `.lz-time` `.lz-title` `.lz-subtitle` | Card header block (eyebrow is orange) |
| `.lz-section` `.lz-section-head` `.lz-section-title` | Sub-panels |
| `.lz-row` `.lz-label` `.lz-opt` `.lz-hint` | Grid gutters, labels (tint on focus), optional pill, helper text |
| `.lz-group` `.lz-addon` `.lz-input` `.lz-textarea` `.lz-select` | 44px inputs; icon addon + input share one border; focus: blue ring, addon tinted `#EFF6FF` + icon blue |
| `.lz-notice` `.lz-notice-icon` `.lz-notice-title` `.lz-notice-text` | Info callout |
| `.lz-actions` | Footer action bar (stacks on mobile) |
| `.lz-btn` + `.lz-btn-primary` / `.lz-btn-outline` / `.lz-btn-ghost` / `.lz-btn-cancel` | 44px buttons, hover lift, arrow nudge |

Component suffix → lz class map (used by `scripts/apply_lz.php`): main-card→lz-card, header-block→lz-head, eyebrow, time-tag→lz-time, title, subtitle, section-card→lz-section, section-header→lz-section-head, section-title, label, opt-tag/opt-badge→lz-opt, hint/field-hint→lz-hint, form-row→lz-row, input-group→lz-group, addon, input, textarea, select, notice-card→lz-notice, notice-icon, notice-text/notice-desc→lz-notice-text, notice-title, actions, btn-back→ghost, btn-cancel→ghost+cancel, btn-draft→outline, btn-submit/btn-continue→primary.

## Validation

`data-v` rules (client) and the matching server rule:

| data-v | Typing filter / max | Check | Laravel rule |
|---|---|---|---|
| `mobile` | digits, 10 | `^[6-9]\d{9}$` | `['required','regex:/^[6-9][0-9]{9}$/']` |
| `pan` | upper A-Z0-9, 10 | `^[A-Z]{5}[0-9]{4}[A-Z]$` | same regex (uppercase input first) |
| `gstin` | upper A-Z0-9, 15 | `^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$` | same |
| `aadhaar` | digits → `1234-5678-9012` | 12 digits, first 2-9 | `regex:/^[2-9][0-9]{3}[ -]?[0-9]{4}[ -]?[0-9]{4}$/` |
| `name` | letters space . ' - , 100 | ≥2 chars | `regex:/^[A-Za-z][A-Za-z .'\-]*$/` |
| `company` | letters digits & . , ' ( ) / -, 150 | ≥2 | string max |
| `code` (IDs) | upper A-Z0-9 / -, 50 | ≥3 | string max:50 |
| `text` | letters digits space / - . , ( ), 100 | non-empty | `regex:/^[A-Za-z0-9 \/\-.,()]+$/` |
| `email` | no spaces, lower | email | `email` (lowercase input first) |
| `address` | 500 | ≥5 | `max:500` |
| `pincode` | digits, 6 | `^[1-9]\d{5}$` | same |
| `decimal` | digits + dot, 12 | number | `numeric` |

Attributes: `data-v-label="Primary mobile number"` (message text), `data-v-different="mobile_num"`, `required`. Form: `data-validate novalidate`. Error text goes into an existing `.<name>_error` / `.edit_<name>_error` span, else a generated `.gt-v-error` after the input/input-group.

Normalise on the server before validating: `strtoupper` PAN/GSTIN/IDs, `strtolower` email.

## Open-With modal (shared)

Any element with class `.btn-view-item, .btn-doc-view, .btn-view-doc, .doc-btn-view, .btn-view-drone-item, .btn-view-dgps-item, .btn-view-ppt-item, .btn-view-comp-item, .btn-view-lab-item, .btn-view-report-item, .btn-view-receipt` or `[data-open-with]` with an `href`/`data-file-path` opens it. Optional `data-file-name`, `data-doc-name`, `data-file-size` ("752 KB"). Shift/Alt-click forces the prompt. Desktop-app options download once and switch to the "Tap to open" screen (browsers cannot launch apps). Word/Excel/PowerPoint try `ms-word:` etc. protocols.

## Visual rules that came up repeatedly

- 8-pt spacing; inputs/buttons 44px; radius 10–16px; one primary CTA per footer.
- Cards in a row: `height:100%; display:flex; flex-direction:column` and push footers down with `margin-top:auto`.
- Labels that may wrap: give `min-height: 2.6em` so values in a row align. Badges beside labels go *inside* the label (`<label>Pending <span class="s7-auto">Auto</span></label>`), not in a flex row with justify-between.
- Show full values (wrap, `overflow-wrap:anywhere`); clamp to 2 lines only for big headline values.
- Status colours by `data-status` attribute + CSS, not inline styles from JS.
- Hover = translateY(-1/-2px) + shadow; press = scale(.97); 150–250ms; respect reduced motion.
