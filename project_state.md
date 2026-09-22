# GTMS — Project State & Memory

## Current Phase: PHASE 5.21 — GLOBAL PAGINATION UI RESOLUTION & BOOTSTRAP 5 STANDARDIZATION (COMPLETED & VERIFIED) ✅
- **Status:** Resolved global pagination layout defect where Laravel 12 defaulted to unstyled Tailwind CSS templates, rendering gigantic raw SVG arrow chevrons (`<` and `>`) and stacked mobile/desktop pagination controls. Configured `Paginator::useBootstrapFive()` in `AppServiceProvider.php`, published and customized clean, responsive `bootstrap-5.blade.php` template with flexbox space-between layout (`w-100`, zero bottom margins), added universal `.pagination` and `.dataTables_paginate` CSS polish with GTMS Navy branding (`#0F1E4D`) in `app.blade.php`, standardized all pagination containers across `eviron/index.blade.php`, `ec_certificate/index.blade.php`, `enviro_b2/index.blade.php`, and mining portal index views, and appended `withQueryString()` to all paginated controller queries. Tested and verified via CLI rendering tests.
- **Last Updated:** 2026-09-21
- **Delivered Capabilities & Fixes:**
  - **Component 1 — Global Paginator Registration (`AppServiceProvider.php`):**
    - Added `Paginator::useBootstrapFive()` to `boot()` method, globally switching Laravel pagination rendering from default Tailwind SVG template to Bootstrap 5 markup.
  - **Component 2 — Vendor Pagination Template Polish (`resources/views/vendor/pagination/`):**
    - `bootstrap-5.blade.php`: Configured responsive flexbox navbar (`d-flex flex-wrap align-items-center justify-content-between gap-2 w-100`), clean `Showing X to Y of Z results` left indicator, and rounded `.pagination` control buttons with `‹` and `›` chevrons.
    - `default.blade.php`: Included `pagination::bootstrap-5` for backward safety.
  - **Component 3 — Universal Pagination & DataTables Harmonization in Theme Layout (`app.blade.php`):**
    - Added `.pagination .page-item .page-link` styling with GTMS Navy theme (`#0F1E4D` active state, `#eff6ff` hover state, 34x34px minimum dimension, 6px border radius).
    - Harmonized `.dataTables_wrapper .dataTables_paginate` buttons to matching 34x34px compact dimensions with GTMS Navy active state (`#0F1E4D`), replacing oversized 50px template buttons on `/application`, `/customers`, and other DataTables views.
    - Hardened SVG defensive limit (`max-width: 16px; max-height: 16px;`) so pagination arrows never render oversize regardless of template fallback.
  - **Component 4 — Blade View Container & Query String Standardization:**
    - Standardized card footer container (`card-footer bg-white border-top py-3`) across `/eviron`, `/ec-certificate`, and `/enviro-b2` tables.
    - Added `->withQueryString()` to `EnvironmentalB2Controller.php` and `MiningController.php` (index, projectFolder, process).

## Previous Phase: PHASE 5.20 — CUSTOMER 360 TRACKING PORTAL & NORMALIZED UNIVERSAL SEARCH ARCHITECTURE (COMPLETED & VERIFIED) ✅
- **Status:** Created enterprise Customer 360 Tracking Portal (`/customer-tracking` and `/customer-tracking/{slug}`) connecting all 4 regulatory modules (Lease Applications, Mining Plans, Environment Clearances, EC Certificates) in a unified lifecycle dossier with 5-pillar metric cards, 5-stage interactive stepper, and centralized Document Vault. Implemented bidirectional search normalization across both AJAX autocomplete and form query endpoints—supporting unformatted digits (`323268689898`), dashed (`3232-6868-9898`), spaced (`3232 6868 9898`), and partial inputs. Resolved relationship exception (`minerals`), enforced 100% English coding (0 Tamil characters in views, controllers, or JS), and verified with PHP CLI test suites.
- **Last Updated:** 2026-09-18
- **Delivered Capabilities & Fixes:**
  - **Component 1 — Universal Search Normalization (`CustomerTrackingController.php` & `index.blade.php`):**
    - Root cause of Aadhaar format bug: Aadhaar is stored in MySQL as `XXXX-XXXX-XXXX` (`3232-6868-9898`). Naive SQL `LIKE '%323268689898%'` in the live autocomplete `search()` returned 0 rows because of hyphens.
    - Implemented normalization: Strips non-digits (`$cleanDigits = preg_replace('/[^0-9]/', '', $q)`), queries with `REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') LIKE '%$cleanDigits%'`, and generates standard 4-4-4 dashed and spaced variations for indexed lookups.
    - Normalized mobile lookup (matching last 10 digits, stripping `+91`, 0, spaces, and hyphens) across primary and secondary contacts.
    - Normalized alphanumeric search for MIMAS numbers and PAN.
    - Live autocomplete dropdown now renders customer name, company, district, masked Aadhaar badge (`3232 **** 9898`), and phone number with instant click-to-dossier navigation.
  - **Component 2 — Customer 360 Tracking Portal UI/UX Pro Max (`customer_tracking/index.blade.php`):**
    - 5-column balanced KPI bar (`Total Customers`, `Lease Apps`, `Mining Plans`, `Env Clearances`, `EC Certificates`) with soft pastel icons.
    - 5-stage lifecycle stepper (`Profile Registered` -> `Lease Application` -> `Mining Plan` -> `Environment Clearance` -> `EC Certificate Issued`) with horizontal connector bars.
    - 4-pillar module cards with applicant metadata, survey numbers, district, status badges, and direct action links.
    - Enterprise Document Vault with filter chips (`All`, `Lease`, `Mining`, `Environment`, `EC`) and search bar.
    - Official print view (`window.print()`) with print stylesheet.
  - **Component 3 — Bug Fixes & Code Cleanliness:**
    - Fixed 500 error: Removed undefined `minerals` relation on `Customer` model; added eager loading for `leaseApplications.district` and `leaseApplications.mineral`.
    - Added missing `ecCertificates()` `hasMany` relationship on `Customer` model.
    - Removed all Tamil words from views, scripts, controllers, and comments (100% clean English).

## Previous Phase: PHASE 5.19 — LEASE APPLICATION TO MINING PLAN TRANSITION & RESUME DOSSIER WORKFLOW (COMPLETED & VERIFIED) ✅
  - **Component 1 — Lease Applications Table & Dynamic Modal (`CustomerController.php` & `customer.blade.php`):**
    - Eager loaded `miningApplications` in `CustomerController@index` to eliminate N+1 queries.
    - Added contextual action buttons: `[ ⛏️ Mining Plan ]` linking to `/projectfolder?id={mining_id}` if already promoted, or `[ 🚀 Move to Mining ]` triggering transition modal if approved and pending.
    - Implemented single dynamic `#modalMoveToMiningDynamic` modal outside the table loop with data attributes, preventing DOM bloat and backdrop collisions.
  - **Component 2 — Mining Intake Resumption & Document Deduplication (`MiningController.php` & `newapplication.blade.php`):**
    - `newApplication()` passes carried documents via `$prefillData['existing_docs']` for both `?resume={id}` and `?lease_id={id}`.
    - Step 6 Document Upload Checklist dynamically renders auto-imported lease files with green badge pills (`[ Attached ]`), direct view links, and replace buttons.
    - Renders carried custom documents directly in statutory folder containers.
    - `data-has-existing="1"` integrated with client-side progress bars, folder pill counters, and Step 7 summary counters.
    - `store()` handler performs in-place document updates on resumption without creating duplicate `MiningDocument` records.
  - **Component 3 — Process Flow Header & Environment Transition (`process.blade.php` & `routes/web.php`):**
    - Header quick action toolbar includes `[ ▶ Resume / Edit Application ]` button for draft / Stage 6.1 applications linking to `/newapplication?resume={id}`.
    - Header displays `[ Environment: {project_code} ↗ ]` when linked Environment Project exists.
    - Process Flow Stage 6.3/6.4 validation success card includes `[ Advance to Environment Clearance (B2) ]` button triggering `moveToEnvironment`.
  - **Component 4 — Automated Test Suite & Zero Non-English Text (`MiningPlanTransitionTest.php`):**
    - 6 new feature tests with 35 assertions passing cleanly against MySQL database:
      1. Approved lease can transition to mining plan with cloned files.
      2. Moving lease twice is idempotent and redirects safely.
      3. Universal Common ID is preserved across lease and mining records.
      4. Resuming mining application prefills and updates without duplicate documents.
      5. All 5 mining routes render HTTP 200 (`/miningplan`, `/newapplication`, `/projectfolder`, `/document`, `/process`).
      6. Codebase contains zero Tamil characters.

## Previous Phase: PHASE 5.18 — ENVIRONMENT CLEARANCE & EC CERTIFICATE NAVIGATION FLOW HARDENING & DEDICATED SHOW ROUTE (COMPLETED & VERIFIED) ✅
- **Status:** Resolved all navigation link mismatches, random wizard step jumping, and table action discrepancies across Environment Clearance and EC Certificate modules. Added dedicated read-only certificate view route (`/ec-certificate/{id}`), enforced symmetrical wizard stepper gating (GET `wizard` and POST `saveStep`), added server-side deep-link guards, cross-project session draft isolation, upload directory path sanitization (`preg_replace('/[^a-zA-Z0-9_-]/', '_', ...)`), superceded file unlinking, disambiguated "View Certificate" vs "Issue Certificate" action buttons, and verified circular navigation via 23/23 passing automated feature tests (97 assertions) and Playwright live browser QA with screenshots. 100% English coding enforced (0 Tamil characters verified via automated test). Completed full 3-round SWE Light multi-agent review with Victory Audit.
- **Last Updated:** 2026-09-18
- **Delivered Capabilities & Fixes:**
  - **Component 1 — Dedicated Certificate View Route & Show Template (`routes/web.php`, `EcCertificateController.php`, `ec_certificate/show.blade.php`):**
    - Created `GET /ec-certificate/{id}` (`ec-certificate.show`) route and controller method.
    - Designed authentic Government of Tamil Nadu / SEIAA official letterhead certificate view with watermark, order letter number, proposal ID, validity dates, terms and conditions, digital QR seal, and Member Secretary signature block.
    - Added clean browser print capability (`window.print()`) with print-optimized CSS (`@media print`), direct PDF download link, and linked project dossier sidebar with one-click navigation back to `/eviron/{id}`.
  - **Component 2 — Stepper Navigation Gating & Symmetrical Route Protection (`wizard.blade.php` & `EcCertificateController.php`):**
    - Eliminated erratic random step jumps: uncompleted future steps in the 6-step header are rendered as non-clickable, disabled indicators (`cursor: not-allowed; opacity: 0.65`).
    - Only completed steps (`$isDone`) and the active step (`$isActive`) retain active navigation links.
    - Symmetrical route guards: Both GET (`wizard`) and POST (`saveStep`) routes enforce `$maxUnlockedStep`. Direct deep links or forged POST submissions to uncompleted steps gracefully redirect to the earliest incomplete step.
    - Cross-project draft isolation: Switching project in Step 1 flushes all downstream draft artifacts (`step2`..`step6`) preventing draft contamination across projects.
  - **Component 3 — Safe Upload Directory Handling & Exception Resilience (`EcCertificateController.php`):**
    - Upload directories strictly sanitize project codes (`preg_replace('/[^a-zA-Z0-9_-]/', '_', ...)`) and create directories via `File::ensureDirectoryExists($destPath, 0755, true)`.
    - Automatically unlinks superseded draft files upon replacement.
    - Wraps file moves and database transactions in `try ... catch (\Throwable $e)` with error logging and user-friendly redirects.
    - Defensive input clamping on `validity_years` (1..30) and `communication_type`.
  - **Component 4 — Action Button & Cross-Module Disambiguation (`ec_certificate/index.blade.php`, `eviron/index.blade.php`, `eviron/show.blade.php`):**
    - In `/ec-certificate` master register: "View Certificate" eye icon now opens the authentic certificate view (`/ec-certificate/{id}`) instead of launching the creation wizard at Step 3.
    - In `/eviron` master register: row actions contextually render purple `[View Issued EC Certificate]` linking to `/ec-certificate/{id}` if a certificate is already granted, or green `[Issue EC Certificate]` linking to `/ec-certificate/step/1` if the project is approved and pending issuance.
    - In `/eviron/{id}` project dossier: summary card displays `[ 🌟 View Issued EC Certificate ({ec_ref_no}) ]` when already granted, eliminating duplicate certificate creation flows.
  - **Component 5 — Controller & UI Resilience (`EnverionsoneController.php` & `eviron/create.blade.php`):**
    - Replaced lexicographical project code `max()` with concurrency-safe `CAST(SUBSTRING_INDEX(...) AS UNSIGNED) DESC`.
    - Added disk existence validation in `downloadDocument()` to prevent 500 crashes on missing files.
    - Added auto-generation of document slots in `show()` for older projects with 0 slots.
    - Added interactive visual highlight toggle (`#eff6ff` blue tint with `#0F1E4D` navy border) on B1 Sub Category 1 vs Sub Category 2 selection pills and declared `customerSelect` safely in DOM script.
  - **Component 6 — Verification & Test Suite (`EnvironmentClearanceTest.php`):**
    - 23 Feature Tests passing with 97 assertions against MySQL database.
    - Includes automated zero-Tamil character verification test scanning all PHP, JS, and CSS files in `app/`, `resources/`, and `routes/`.

## Previous Phase: PHASE 5.17 — ENVIRONMENT CLEARANCE & EC CERTIFICATE UI/UX PRO MAX & MULTI-STEP WIZARD (COMPLETED & VERIFIED) ✅
- **Status:** Resolved all static information and UI design inconsistencies across `/eviron`, `/eviron/create`, and `/ec-certificate/step/1` through `step/6`. Integrated dynamic category intake (B1 with SC1/SC2 toggle vs B2 Direct EC), Customer Unique ID instant lookup, dynamic SEIAA certificate generation, and persistent 6-step state machine with zero regressions. All 8 requested URLs tested and verified with HTTP 200/compile success. 100% English coding enforced.
- **Last Updated:** 2026-09-18
- **Delivered Capabilities & Fixes:**
  - **Component 1 — EC Certificate Multi-Step State Machine (`EcCertificateController.php` & `routes/web.php`):**
    - Implemented `saveStep()` controller handler with session persistence (`session('ec_wizard')`) for Steps 1 through 6.
    - Deterministic, dynamic reference numbering generator `SEIAA-TN/EC/{YEAR}/{SEQUENCE}` and `SIA/TN/MIN/{ID}/{YEAR}` eliminating fake `rand()` values.
    - Graceful fallback for direct deep-link access (`/ec-certificate/step/2` through `step/6` automatically initialize dynamic project context without breaking).
  - **Component 2 — EC Certificate Wizard UI/UX Pro Max (`wizard.blade.php`):**
    - Replaced dummy static placeholders with functional interactive stages:
      - Step 1: Project selector with dynamic customer binding, reference numbers, validity calculator, and communication classification.
      - Step 2: Drag & drop certificate PDF uploader with file validation, metadata preview, and Parivesh checklist.
      - Step 3: Authentic Government of Tamil Nadu / SEIAA official certificate preview paper with digital seal, QR validation code, environmental terms, and print view.
      - Step 4: Digital document repository and physical folder allocation cards.
      - Step 5: Communication dispatch console with recipient email, mobile alerts, and editable dispatch note.
      - Step 6: Executive review summary with 4-card metric recap, 5-point verification checklist, and final issuance button.
  - **Component 3 — EC Certificates Register UI/UX Pro Max (`ec_certificate/index.blade.php`):**
    - Upgraded KPI widgets to GTMS `.dossier-kpi-card` standard (Approved, Issued, Ready to Download, Parivesh Synced).
    - Harmonized theme palette to GTMS Deep Navy (`#0F1E4D`), replacing outdated pink `.btn-info` with `.btn-navy`.
    - Integrated live search filter and clean `.table-action-group` icon buttons.
  - **Component 4 — Environment Clearance Intake & Landing UI/UX (`eviron/create.blade.php` & `eviron/index.blade.php`):**
    - Added Customer Unique ID / MIMAS instant lookup search card with datalist and autofill.
    - Styled Category cards with interactive focus borders, badge pills, and dynamic B1 sub-category toggle (SC1 ToR vs SC2 EIA).
    - Upgraded `/eviron` landing page with dynamic project KPIs, category filters, and progress bars.
  - **Zero Non-English Text in Code:**
    - Purged all non-English text across controllers, models, migrations, views, and notification messages.

## Previous Phase: PHASE 5.16 — LEASE APPLICATION & MINING PLAN COMPREHENSIVE HARDENING (12 ISSUES RESOLVED & VERIFIED) ✅
- **Status:** Completed comprehensive system audit and resolved all 12 identified functional, concurrency, and UI/UX issues across Lease Applications and Mining Plan modules with zero regressions. Verified via 16/16 automated test suite and live Playwright browser QA.
- **Last Updated:** 2026-09-17
- **Delivered Fixes & Capabilities:**
  - **Component 1 — Mining Concurrency & Unified Identifiers (`MiningController.php` & `newapplication.blade.php`):**
    - Implemented atomic application number generation `generateMiningAppNumber()` using transaction row locking `lockForUpdate()` across active and soft-deleted records.
    - Standardized application prefix to `MP-` (e.g. `MP-2026-0012`) eliminating prefix divergence between direct intake and lease promotion.
    - Automatically generate and assign Universal Common ID `GTMS-{YEAR}-{SEQUENCE}` on direct mining intake. Backfilled legacy mining records.
    - Updated applicant summary alerts in `newapplication.blade.php` to display `MP-` and `GTMS-` tracking IDs.
  - **Component 2 — Lease Wizard Step 2 Password Resilience & Table UI (`createstep2.blade.php` & `customer.blade.php`):**
    - Fixed masked password blanking issue on blur: added `data-has-saved="1"` and `blur` event handler to restore `__UNCHANGED__` if user leaves password field empty, preventing HTML5 `required` validation lockout.
    - Purged invalid nested `<button>` tag inside `<a>` in `customer.blade.php`, upgraded to clean `.btn-navy` button with `{{ route('step1') }}`.
    - Added `min-width: 140px; white-space: nowrap;` to Actions column header and flex container in table cells.
    - Added dedicated visual action for applications requiring revisions (`[ ⚠️ Revisions → ]`) and aligned draft resume button styling.
  - **Component 3 — Scrutiny Dossier Modal & SweetAlert2 Upgrade (`viewapplication.blade.php`):**
    - Upgraded all remaining legacy `alert(...)` calls in document validation and flagging workflows to GTMS Navy themed SweetAlert2 dialogs (`Swal.fire`).
    - Resolved Bootstrap modal memory leak by replacing `new bootstrap.Modal(...)` with `bootstrap.Modal.getOrCreateInstance(...)`.
    - Converted hardcoded `/application` and `/process?id=...` paths to Laravel route helpers.
  - **Component 4 — Document Upload UX & Dynamic Process Modal (`document.blade.php`, `projectfolder.blade.php`, `process.blade.php`):**
    - Added server-side MIME type and 25MB validation in `MiningController@uploadDocument`.
    - Added client-side file size validation (<= 25MB), upload spinner feedback, and allowed file extension filters in `document.blade.php`.
    - Refactored 25 duplicate modal dialogs inside `@foreach` loop in `process.blade.php` into a single dynamic `#modalRejectMiningDoc` powered by data attributes, reducing DOM overhead and eliminating backdrop collisions.
    - Updated quick-switcher in `projectfolder.blade.php` to use route helper.

## Previous Phase: PHASE 5.15 — MINING PLAN ACTION BUTTONS UI/UX PRO MAX & SOFT-DELETE SUBMIT INTEGRITY (COMPLETED & VERIFIED) ✅
- **Status:** Resolved SQL constraint collision during application submission and upgraded `/miningplan` action buttons into an enterprise-grade toolbar.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Action Buttons UI/UX Pro Max (`index.blade.php`):**
    - Symmetrical 32px height across all action items in `.mining-action-group` with 6px spacing and `nowrap`.
    - Compact, gradient-styled `[ ▶ Resume ]` pill button (`#7c3aed` to `#6d28d9`) for draft applications.
    - Dedicated, color-coded 32x32px rounded action icon buttons:
      - 📁 **Folders**: Sky Blue (`#eff6ff`, border `#bfdbfe`, icon `#1d4ed8`) linking to `/projectfolder?id=X`.
      - ☁️ **Files**: Warm Amber (`#fef3c7`, border `#fde68a`, icon `#b45309`) linking to `/document?id=X`.
      - 🔀 **Process**: Indigo/Navy (`#eef2ff`, border `#c7d2fe`, icon `#4338ca`) linking to `/process?id=X`.
    - Responsive `min-width: 190px` on Actions `<th>` preventing button wrapping.
    - Initialized Bootstrap tooltips on all action buttons.
  - **Soft-Delete Unique Key Collision & Submit Fix (`CustomerController.php` & `Customer.php`):**
    - Root cause: Customer #1 ("Sri Bala Traders", `TN-MMS-SLM-001`) was soft-deleted. Standard `Customer::find()` and `where('mimas_no', ...)` returned `null`, triggering `Customer::create()`. The model's slug count check excluded soft-deleted records, picking `sri-bala-traders` which collided with the database's unique key `customers_slug_unique`.
    - Upgraded customer resolution in `submit()`, `saveStep1()`, `buildPreviewData()`, `MiningController@store()`, and `lookupByMimas()` to use `Customer::withTrashed()`.
    - Added automatic restoration (`$customer->restore()`) when an active statutory application is submitted for that customer.
    - Upgraded `Customer::booted()` slug generator to check `static::withTrashed()->where('slug', $slug)->exists()` inside a `while` loop, guaranteeing monotonic uniqueness (`base-2`, `base-3`) across active and soft-deleted rows.
    - Resolved syntax error in `CustomerController@saveStep1`. Customer #1 restored in DB.

## Previous Phase: PHASE 5.14 — MIMAS NUMBER & DYNAMIC STATUS ACROSS 4 PAGES (COMPLETED & VERIFIED) ✅
- **Status:** Rendered MIMAS Number and exact user-typed MIMAS Status badge across all 4 requested pages (`/application`, `/miningplan`, `/process`, `/projectfolder`).
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Page 1 (`/application` - `customer.blade.php`):** Added `MIMAS Details` column showing `mimas_number` and dynamic color badge reflecting user-typed text.
  - **Page 2 (`/miningplan` - `mining-portal/index.blade.php`):** Added `MIMAS Details` column with 10-column table alignment.
  - **Page 3 (`/process` - `mining-portal/process.blade.php`):** Added `MIMAS Details` column in index table, top chips badge bar, and Parent Lease card in detail view (`/process?id=X`).
  - **Page 4 (`/projectfolder` - `mining-portal/projectfolder.blade.php`):** Added `MIMAS Details` column in index table, header badges, and dedicated sub-row in applicant recap card (`/projectfolder?id=X`).
  - **Dynamic Badge Styling:** Auto-detects status sentiment (Approved -> green, Pending -> warning/amber, Applied -> blue, Rejected -> red, default -> info) while strictly rendering 100% of user-typed text.

## Previous Phase: PHASE 5.13 — LEASE-TO-MINING INTAKE RESUME & COMPLETE WORKFLOW (COMPLETED & VERIFIED) ✅
- **Status:** Integrated seamless cross-module resume workflow between Lease Applications and Mining Portal. Applications moved from Lease Application or in draft state display a distinctive "Lease #LA-XXXX" origin badge and an intuitive `[ ▶ Resume ]` action button in the `/miningplan` master list. Clicking Resume navigates to `/newapplication?resume={id}` with a contextual informational banner and all customer details, contacts, district, extent, survey numbers, and mineral concessions auto-filled. Allows applicants to manually fill missing mining-specific requirements (Nature of Work, Plan Type, RQP, etc.) and updates existing records upon submission without duplication.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Master Table Origin & Resume UI (`index.blade.php`):**
    - Applications originated from Lease Application display `<span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="fa fa-link me-1"></i>Lease #LA-XXXX</span>`.
    - Every draft or migrated application features a prominent royal purple `[ ▶ Resume ]` button linking to `/newapplication?resume={id}`.
    - Multi-mineral badges and custom "Other: [name]" badges accurately displayed in table column.
  - **Pre-filled Mining Intake Wizard (`newapplication.blade.php` & `MiningController.php`):**
    - `MiningController@newApplication`: Accepts `Request $request` and assembles `$prefillData` from `MiningApplication` or `LeaseApplication`.
    - Contextual banner at the top of the wizard informs applicant that Lease Application data and statutory files have been auto-fetched.
    - Step 1: Pre-fills Client Name, Company Name, Customer Unique ID (`mimas_no`), Primary & Secondary mobile numbers, contacts, PAN, Aadhaar, GSTIN, Address.
    - Step 2: Pre-selects Nature of Work.
    - Step 3: Pre-checks multi-selected mineral checkboxes and auto-fills `other_mineral_name` with custom name if "Others" was chosen.
    - Step 4: Pre-selects District, Area Extent (Ha), Taluk, Village, and Survey Numbers text.
    - `MiningController@store`: Detects `mining_app_id` hidden input to perform clean `update(...)` rather than duplicating the record upon completion.
  - **Live Browser Verification:** Verified via Playwright end-to-end testing with screenshot confirming auto-fill banner, client profile, survey extents, and pre-checked minerals.

## Previous Phase: PHASE 5.12 — CROSS-MODULE AUDIT & 10 SIDE-EFFECTS RESOLUTION (COMPLETED & VERIFIED) ✅
- **Status:** Resolved all 10 identified cross-module side-effects stemming from 6-step consolidation, multi-mineral pivot, and folder reordering.
- **Last Updated:** 2026-09-16
- **Delivered Fixes:**
  - **Fix #1 (Critical):** Draft badge out-of-bounds `Draft (Step 7/6)` fixed in `customer.blade.php` line 203 by capping with `min(6, (int)($app->current_step ?? 1))`. Database normalization script updated all legacy drafts to `current_step = 6`.
  - **Fix #2 (Critical):** Resume draft MIMAS password in `CustomerController@resumeDraft` populated with `'__UNCHANGED__'` placeholder so Step 2 renders masked password indicator.
  - **Fix #3 (Critical):** Mining `process.blade.php` line 294 updated from single `$application->mineral->name` to multi-mineral aware rendering with `$application->minerals` pivot and `other_mineral_name`.
  - **Fix #4 (Critical):** Created migration `2026_09_16_145912_add_other_mineral_name_to_mining_applications.php`, updated `MiningApplication` `$fillable`, and updated `moveToMining()` to preserve custom mineral names.
  - **Fix #5 (Important):** Verified old draft uploaded documents folder mapping logic.
  - **Fix #6 (Important):** `viewapplication.blade.php` custom document sort order fixed to fallback to 99999 so custom items appear at the end of the folder instead of Row 0.
  - **Fix #7 (Important):** Added conditional "Specify Other Mineral Name" input box to Mining Intake Step 3 with automatic show/hide JS toggle, step validation gating, and Step 7 preview formatting.
  - **Fix #8 (Important):** Upgraded all 12 browser `alert(...)` calls in Mining Intake wizard to SweetAlert2 (`Swal.fire`) with `#0F1E4D` GTMS Navy branding.
  - **Fix #9 (Low):** Corrected browser tab title in `createstep7.blade.php` from "Step 7" to "Step 6".
  - **Fix #10 (Low):** Added formal deprecation notice comment to dead file `createstep6.blade.php`.
  - **Bug Fix in MiningController:** Fixed `Data too long for column 'pan'` SQL error when generating temporary customer records during manual mining submission.

## Previous Phase: PHASE 5.11 — SWEETALERT2 UPGRADE ACROSS LEASE APPLICATION WIZARD (COMPLETED & VERIFIED) ✅
- **Status:** Replaced all legacy browser `alert(...)` calls across the Lease Application wizard (Steps 1, 2, 3, and 5) with modern, styled SweetAlert2 modal dialogs (`Swal.fire`). Fully verified via Playwright live browser QA with screenshot.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Step 5 (`createstep5.blade.php`):**
    - Single file upload failure: `Swal.fire({ icon: 'error', title: 'Upload Failed', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Single file upload error / network catch: `Swal.fire({ icon: 'error', title: 'Upload Error', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Continue gating validation: `Swal.fire({ icon: 'warning', title: 'Mandatory Documents Required', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Batch upload checklist full: `Swal.fire({ icon: 'info', title: 'Checklist Complete', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Custom document name required: `Swal.fire({ icon: 'warning', title: 'Document Name Required', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Custom document file required: `Swal.fire({ icon: 'warning', title: 'File Required', text: ..., confirmButtonColor: '#0F1E4D' })`.
    - Custom document success: `Swal.fire({ icon: 'success', title: 'Document Added!', text: ..., timer: 2000, showConfirmButton: false })`.
    - Custom document upload failure & error: `Swal.fire({ icon: 'error', ... })`.
  - **Steps 1, 2, and 3:**
    - Step 1: Mineral selection and "Other" mineral name validation prompts upgraded to SweetAlert2 warnings. AJAX validation errors displayed via SweetAlert2 error dialogs.
    - Step 2: Contact Person, Mobile, MIMAS User ID, and MIMAS Email validation alerts upgraded to SweetAlert2 warnings.
    - Step 3: Category/Rule selection validation prompts and draft save errors upgraded to SweetAlert2.
  - **Theme Alignment:** All confirmation buttons styled with GTMS brand Deep Navy `#0F1E4D`.

## Previous Phase: PHASE 5.10 — SEQUENTIAL DOCUMENT NUMBERING (1 TO 19) IN LEASE APPLICATION FOLDERS (COMPLETED & VERIFIED) ✅
- **Status:** Step 5 Document Upload checklist rearranged into natural sequential numbering (1 through 19) following the reordering of Folder 1 (Documents) and Folder 2 (Lease Application). All 19 statutory items, folder counters, draft uploads, submission metadata, and dossier views synchronized.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Folder 1: Documents Folder (#7 · 9 Items):**
    - Item 1: `1. Land Document`
    - Item 2: `2. Consent (If Applicable)`
    - Item 3: `3. Adangal & A-register`
    - Item 4: `4. Patta & Encumbrance Certificate`
    - Item 5: `5. Work Order`
    - Item 6: `6. Gazette`
    - Item 7: `7. Recommendation Letter`
    - Item 8: `8. Mineral Management System – Application`
    - Item 9: `9. Challan downloaded from Mimas`
  - **Folder 2: Lease Application Folder (#8 · 7 Items):**
    - Item 10: `10. Lease application – signed, FMB, Plan`
    - Item 11: `11. Affidavit – Income Tax`
    - Item 12: `12. IT returns (If Applicable)`
    - Item 13: `13. Affidavit – Mining Due`
    - Item 14: `14. Affidavit – Mining Lease`
    - Item 15: `15. Affidavit – 1.5 meter depth`
    - Item 16: `16. Affidavit – Hill Areas`
  - **Folder 3: Plan Folder (#9 · 3 Items):**
    - Item 17: `17. Plan Source File`
    - Item 18: `18. KML File`
    - Item 19: `19. Plan PDF`
  - **CustomerController.php Synchronizations:**
    - `uploadDocument()`: `$docNames` array updated to map 1..9 for Documents, 10..16 for Lease Application, and 17..19 for Plan.
    - `submit()`: `$docsMeta` array updated with folder 7 (doc_item 1..9), folder 8 (doc_item 10..16), and folder 9 (doc_item 17..19).
    - `buildPreviewData()`: Updated folder count fallback ranges (1..9 for Folder 7, 10..16 for Folder 8, 17..19 for Folder 9).
  - **Step 4 Folders Tile Synchronizations (`createstep4.blade.php`):**
    - Dynamic folder tile upload counters accurately classify uploaded items 1..9 under Documents (Folder 1), 10..16 under Lease Application (Folder 2), and 17..19 under Plan (Folder 3).
  - **Dossier & Scrutiny Alignment (`viewapplication.blade.php`):**
    - Because the dossier naturally sorts by `(int)preg_replace('/\D/', '', explode('.', $d->document_name)[0] ?? '99')`, renumbering items 1..19 automatically guarantees that Folder 1 items (1..9) appear first, Folder 2 items (10..16) appear second, and Folder 3 items (17..19) appear third in perfect numerical order.
  - **Bug Fix (`createstep5.blade.php`):**
    - Root cause of `Upload error: btnUploadAllText is not defined`: Removed orphan `if (btnUploadAllText)` block inside `updateOverallProgress()`. The button was deleted during prototype cleanup, but the progress handler still tried to access the undeclared variable, triggering a `ReferenceError` caught by the AJAX error handler. Purged the dead code; file upload and custom document addition now execute smoothly with zero errors.

## Previous Phase: PHASE 5.9 — MULTI-MINERAL TYPE MULTI-SELECT, CONDITIONAL "OTHER" INPUT, AND UNIFIED STEP 2 MIMAS ARCHITECTURE (COMPLETED & VERIFIED) ✅
- **Status:** Step 1 Mineral Type converted to dynamic multi-select with conditional manual entry for "Others" (`other_mineral_name`), Step 6 MIMAS credentials merged directly into Step 2 alongside Primary & Secondary Contacts, and entire wizard streamlined from 7 steps into 6 clean steps (`createstep7.blade.php` now serves as Step 6 Review & Launch). Fully verified via Database Migration, Model Pivot relations, Controllers, Blade Views, and Playwright end-to-end browser QA with screenshots.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Database Architecture & Migration (`2026_09_16_122500_create_lease_application_minerals_and_other_column.php`):**
    - Added `other_mineral_name` (varchar 255 nullable) to `lease_applications` table.
    - Created pivot table `lease_application_minerals` (`lease_application_id`, `mineral_id`) with foreign keys cascading on delete and composite unique index.
    - Updated `LeaseApplication` model: `$fillable` includes `other_mineral_name`, and `minerals(): BelongsToMany` established alongside backward-compatible `mineral(): BelongsTo`.
  - **Step 1 View (`createstep1.blade.php`):**
    - Stepper updated to 6 steps: `Application` ➔ `Basic & MIMAS` ➔ `Category` ➔ `Folders` ➔ `Documents` ➔ `Review`.
    - Mineral Type rendered as accessible multi-select checkbox pills (`mineral_ids[]`) with gem icons and active gold borders.
    - Added dynamic `#other_mineral_box` with input `#field_other_mineral_name` that automatically animates into view whenever "Others" (id=8) is toggled.
    - Wired customer lookup autofill to check selected minerals and unmask other mineral input if needed.
  - **Unified Step 2 View (`createstep2.blade.php`):**
    - Title updated to "Basic Information & MIMAS Details".
    - Displays Primary Authorized Representative contact alongside Secondary / Site In-charge contact.
    - Merged statutory MIMAS Registration Details card: `mimas_user_id`, `mimas_password` (with AES-256 badge and live eye toggle), `mimas_email`, and `mimas_contact`.
    - Sanitized mobile validation patterns to support 10-15 digit phone numbers without form submission blocking.
  - **Steps 3, 4, 5 & 6 View Updates:**
    - Steps 3, 4, and 5 progress indicators updated to 6 steps.
    - **Step 5 Folders Display Reordered:** Aligned folder order with Step 4 tiles and Step 6 summary so **1. Documents folder (Folder #7, 9 items)** displays first, **2. Lease Application folder (Folder #8, 7 items)** displays second, and **3. Plan folder (Folder #9, 3 items)** displays third. Custom document addition targets `#folder_container_7` and `#folder_container_8` verified with zero regressions.
    - Step 5 Continue button directs straight to `{{ route('step6') }}` ("Continue to Review").
    - Step 6 Review (`createstep7.blade.php`): Review header shows `Step 6 of 6 · Application Preview & Verification`. Section 5 updated to "MIMAS Portal Credentials (Step 2)" with direct edit link to Step 2. Back button navigates to Step 5.
    - Displays comma-separated multi-mineral names with `Other: [custom name]`.
  - **Controller & Pipeline Layer (`CustomerController.php`):**
    - `saveStep1()`: Validates `mineral_ids[]` and `other_mineral_name`, stores primary mineral in `mineral_id` for backward compatibility, persists `other_mineral_name`, and syncs `minerals()` pivot.
    - `saveStep2()`: Accepts and validates contacts + MIMAS credentials. Persists to both `$draft['step2']` and `$draft['step6']` for 100% downstream compatibility.
    - `step6()`: Builds preview data and renders review page.
    - `step7()`: Redirects to `route('step6')` for backwards compatibility.
    - `submit()`: Atomically saves multi-minerals in pivot, saves `other_mineral_name`, sets `current_step = 6`, and moves application to scrutiny queue.
    - `moveToMining()`: Seamlessly syncs all selected minerals from `$lease->minerals` pivot to `$miningApp->minerals` pivot.
    - `index()`, `viewApplication()`, and `generateReport()`: Eagerly load `minerals` relation.
  - **Dossier & Reporting Integration:**
    - `viewapplication.blade.php`: Renders all selected minerals and `Other: [name]`.
    - `report_pdf.blade.php`: Official scrutiny dossier renders all selected minerals and `Other: [name]`.
    - `customer_show.blade.php`: Customer 360 profile renders all mineral concession badges in the leases table.
    - `customer.blade.php`: Draft status displays `Draft (Step X/6)`.

## Previous Phase: PHASE 5.8 — SEPARATE MIMAS NUMBER & MIMAS STATUS INPUT ARCHITECTURE IN CUSTOMERS MODULE (COMPLETED & VERIFIED) ✅
- **Status:** Added dedicated `MIMAS Number` and `MIMAS Status` fields in Customers module (`/customers` and `/customers/{slug}`) alongside `Customer Unique ID`. Fully verified via Database Migration, Model Fillables, Controller, AJAX scripts, and live browser QA.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Database Migration (`2026_09_16_120500_add_mimas_number_and_status_to_customers_table.php`):**
    - Added `mimas_number` (varchar 100 nullable) and `mimas_status` (varchar 100 nullable) to `customers` table.
  - **Model & Controller (`Customer.php` & `CustomerDirectoryController.php`):**
    - Added `mimas_number` and `mimas_status` to `$fillable` on `Customer`.
    - Added validation rules in `store()` and `update()` (`nullable|string|max:100`).
    - Added fields to `lookupByMimas()` response.
  - **Blade Views & AJAX Handlers:**
    - **Add Customer Modal (`#customerModal`)**: Formatted top row into 3 columns: `Customer Unique ID *`, `MIMAS Number`, `MIMAS Status` (input box with helper text).
    - **Edit Customer Modal (`#editCustomerModal`)**: Top row matches Add Modal and auto-populates via `customer.js`.
    - **View Customer Modal (`#viewCustomerModal`)**: Displays `Customer Unique ID`, `MIMAS Number`, and `MIMAS Status` badge.
    - **Directory Data Table**: Renders `MIMAS Number` badge with tooltip displaying status alongside `Customer Unique ID`.
    - **Customer 360° Profile (`/customers/{slug}`)**: "Tax & Business Identifiers" sticky card renders `MIMAS Number` chip and `MIMAS Status` pill when present.
  - **Lease Application & Mining Portal**:
    - Retained on hold as per user instruction; ready to be hooked into specified locations once instructed.

## Previous Phase: PHASE 5.7 — "CUSTOMER UNIQUE ID" UNIFIED REBRANDING (COMPLETED & VERIFIED) ✅
- **Status:** Rebranding of "MIMAS Number (Universal Unique ID)" to "Customer Unique ID" fully implemented and verified across Customers Directory (`/customers`), Customer 360° Profile (`/customers/{slug}`), Lease Application Module (`/step1`, `/step7`, `/viewapplication`, `report_pdf`), and Mining Portal Module (`/newapplication`, `/process`).
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Customers Directory (`/customers`):**
    - Add Customer Modal: Label updated to `Customer Unique ID *`, placeholder updated to `e.g. CUST-001 or TN-MMS-2026/001`, helper text updated to reflect unique customer/quarry registration identifier.
    - View Customer Modal: Label updated to `Customer Unique ID`.
    - Edit Customer Modal: Label updated to `Customer Unique ID *`.
    - Directory Table: Fingerprint badge tooltip updated to `Customer Unique ID`.
  - **Customer 360° Profile (`/customers/{slug}`):**
    - Header identity badge tooltip updated to `Customer Unique ID`.
    - "Tax & Business Identifiers" chip updated to `Customer Unique ID`.
  - **Lease Application Wizard & Dossier (`/step1`, `/step7`, `/viewapplication`, `report_pdf`):**
    - Step 1 (`createstep1.blade.php`): Card header updated to `Customer Unique ID Lookup`, helper subtext updated, placeholder updated to `Type or select Customer Unique ID (e.g. TN-MMS-SLM-001)`, AJAX feedback and toastr notifications updated.
    - Step 7 Preview (`createstep7.blade.php`): Identity item line 48 updated to `Customer Unique ID:`.
    - Lease Dossier (`viewapplication.blade.php`): "Lease & Applicant Information" card now renders `Customer Unique ID: [mimas_no]` alongside Aadhaar, PAN, and GSTIN.
    - Official Compliance Report (`report_pdf.blade.php`): Section 1 grid item updated to `Customer Unique ID`.
  - **Mining Portal Intake & Process (`/newapplication`, `/process`):**
    - Step 1 Intake (`newapplication.blade.php`): Card header updated to `Customer Unique ID Lookup`, subtext updated, input placeholder updated to `Type or select Customer Unique ID...`, alert messages updated.
    - Step 7 Review (`newapplication.blade.php`): Application summary card renders `Customer Unique ID` populated via JS.
    - Mining Process Flow (`process.blade.php`): Top identity bar renders `Cust ID: [mimas_no]` badge; "Parent Lease Application Information" card renders `Customer Unique ID` alongside Universal Common ID.
  - **Zero Breaking Changes:**
    - HTML form field names (`name="mimas_no"`) and database column `customers.mimas_no` retained intact to preserve 100% backward compatibility with migrations, APIs, and foreign keys.
    - Government filing credentials (Step 6 MIMAS portal login) remain correctly designated as statutory government portal fields.

## Previous Phase: PHASE 5.6 — DUAL PHONE NUMBER & CONTACT PERSON ARCHITECTURE (COMPLETED & VERIFIED) ✅
- **Status:** Complete dual contact architecture (Primary Mobile + Contact Person alongside Secondary Mobile + Secondary Contact Person) fully implemented and verified across Database, Models, Controllers, APIs, Blade Views, and JS Handlers in Customers Directory (`/customers`), Lease Application Wizard (`/step1`, `/step2`, `/step7`, `/viewapplication`, `/application`, `report_pdf`), and Mining Portal Intake & Process (`/newapplication`, `/process`).
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Database Migration (`2026_09_16_104641_add_secondary_contact_to_customers_and_leases.php`):**
    - `customers`: added `secondary_contact_person` (varchar 255 nullable) and `secondary_mobile_num` (varchar 15 nullable).
    - `lease_applications`: added `secondary_contact_person` (varchar 255 nullable) and `secondary_contact_mobile` (varchar 15 nullable).
  - **Model Fillables:**
    - `Customer.php`: added `secondary_contact_person` and `secondary_mobile_num` to `$fillable`.
    - `LeaseApplication.php`: added `secondary_contact_person` and `secondary_contact_mobile` to `$fillable`.
  - **Controller & API Layer:**
    - `CustomerDirectoryController`: store & update validated with `different:mobile_num` to prevent identical duplicates. Lookup API (`lookupByMimas`) returns secondary contacts.
    - `CustomerController`: `saveStep1`, `saveStep2`, `viewApplication`, `buildPreviewData`, and `submit` persist and reconstruct secondary contacts across sessions and drafts.
    - `MiningController`: `store` validates and persists secondary contacts during intake registration.
  - **Blade Views & UI Polish:**
    - `/customers`: Add, Edit, and View modals feature secondary contact inputs. Table displays primary and secondary numbers with `Alt` badge.
    - `/customers/{slug}` (360° Profile): Sticky sidebar renders primary chip (`tel:` link) and styled secondary chip.
    - Lease Wizard (`/step1`, `/step2`, `/step7`): Step 1 captures secondary contact; Step 2 provides dedicated Authorized Contact & Secondary Contact sections with 10-digit input mask; Step 7 dynamic preview reflects both contacts.
    - Lease Dossier (`/viewapplication`): "Lease & Applicant Information" card and MIMAS card display primary and secondary contacts.
    - Lease Application List (`/application`): Table shows primary phone and secondary phone with `Alt` badge.
    - Official Compliance Report (`/application/{id}/report`): Dossier PDF renders primary and secondary contact details.
    - Mining Portal Intake (`/newapplication`): Step 1 captures and MIMAS lookup autofills secondary contact; Step 7 preview reflects both contacts.
    - Mining Process (`/process`): Table renders primary and secondary numbers; Parent Lease card renders both contacts.

## Previous Phase: PHASE 5.5 — UNIVERSAL GTMS COMMON ID & CROSS-MODULE "MOVE TO MINING PLAN" (COMPLETED & VERIFIED)
- **Status:** Universal GTMS Common ID architecture ("விருப்பம் A") and "Move to Mining Plan" cross-module workflow fully implemented across Database, Models, Controllers, and all 3 specified Blade views (`/step7`, `/viewapplication`, `/customers/{slug}`). Verified via automated CLI test suite and Playwright live browser QA with screenshots.
- **Last Updated:** 2026-09-16
- **Delivered Capabilities:**
  - **Universal Common ID Architecture (`common_id`):**
    - Database migration `2026_09_16_095500_add_common_id_to_lease_and_mining_tables.php` added indexed `common_id` column to both `lease_applications` and `mining_applications`.
    - Automatically backfilled legacy records (e.g. `LA-2026-0001` -> `GTMS-2026-0001`, `LA-2026-0010` -> `GTMS-2026-0010`).
    - Both `LeaseApplication` and `MiningApplication` models updated with `common_id` in `$fillable` and `getDisplayCommonIdAttribute()` helpers.
  - **Seamless Cross-Module Transition Engine (`CustomerController@moveToMining`):**
    - `POST /application/{id}/move-to-mining` promotes approved/submitted Lease records to Mining Plan domain.
    - Preserves identical `common_id` (e.g. `GTMS-2026-0010`).
    - Generates synchronous mining application number `MP-2026-NNNN`.
    - Carries applicant details, mineral concessions, district, taluk, village, land extent (Ha), and SF survey numbers.
    - Automatically clones physical files on disk from `public/uploads/lease_applications/{app_no}/` to `public/uploads/mining/{mp_app_no}/`.
    - Auto-creates `MiningDocument` rows mapping Land Documents/Affidavits to Folder #2 (Documents) and Plans/Drawings/KML to Folder #5 (Plan).
    - Idempotent execution: Prevents duplicate promotions and gracefully redirects to the active mining process.
  - **Interactive Action on All 3 Requested Pages:**
    - **Page 1 (`createstep7.blade.php` - `/step7`):** Displays Common ID badge preview (`GTMS-2026-AUTO`) and secondary action button `[ 🚀 Submit & Move to Mining Plan ]` with loading spinner.
    - **Page 2 (`viewapplication.blade.php` - `/viewapplication?id=11`):** Displays Common ID badge in header, `[ 🚀 Move to Mining Plan ]` button in header & Stage 6.3/6.4 panel with confirmation modal `#modalMoveToMining`, and persistent green banner + `[ ⛏️ Mining Plan: GTMS-2026-XXXX ↗ ]` badge when already moved.
    - **Page 3 (`customer_show.blade.php` - `/customers/demo-company-2`):** Displays "App No & Common ID" column and Action column in Tab 1 Leases table with `[ 🚀 Move to Mining ]` button and linked `[ ⛏️ GTMS-2026-XXXX ↗ ]` badge.
  - **Mining Portal Origin Dossier & Unified Audit Trail (`process.blade.php` & `projectfolder.blade.php`):**
    - Header displays high-contrast `Universal Common ID: GTMS-2026-XXXX`, `Mining App No: MP-2026-XXXX`, and `Origin: Lease #LA-XXXX` badge with direct link to original lease dossier.
    - Displays dedicated **"Parent Lease Application Information"** card rendering all inherited parameters and cloned statutory document attachment pills.
    - Displays **"Unified Lifecycle Tracking & Audit Trail"** rendering both Lease Phase and Mining Phase milestones in a single continuous timeline.

## Previous Phase: PHASE 5.4 — DYNAMIC FOLDER-WISE "+ ADD DOCUMENT" FEATURE (COMPLETED & VERIFIED)
- **Status:** Folder-wise "+ Add Document" button, custom modal dialog (Document Name, Status, File Upload), and dynamic database persistence implemented and verified in both Mining Portal and Lease Application modules.
- **Last Updated:** 2026-09-15

## Previous Phase: PHASE 5.3 — MINING PORTAL INTAKE DOCUMENT UPLOAD STEP (COMPLETED & VERIFIED)
- **Status:** Step 6 "Document Upload" (placed after Step 5 Folders, before Preview) fully implemented and verified.
- **Last Updated:** 2026-09-15
- **Delivered Capabilities:**
  - Stepper updated to 7 steps: 1. Client Info, 2. Nature of Work, 3. Minerals & Plan (branching), 4. District, 5. Folders, 6. Upload Docs, 7. Preview.
  - Document Upload Step (`pane_5`) implemented matching Lease Application Step 5 (`createstep5.blade.php`).

## Previous Phase: PHASE 5.2 — MINING PORTAL MULTI-SELECT MINERALS (COMPLETED & VERIFIED)
- **Status:** Step 3 multi-select minerals configuration fully implemented and verified end-to-end.
- **Last Updated:** 2026-09-15

### What Was Implemented:

#### Phase 4.1 — Form Data Persistence (🔴 Critical) ✅
- Added POST routes for Steps 1-6 in `routes/web.php` (saveStep1-saveStep6 + uploadDocument)
- All step forms now use AJAX POST with `@csrf` and session-based `lease_draft` storage
- Step 2 blade — added `name="contact_person"` and `name="contact_mobile"` attributes
- Step 3 blade — added `<form>`, radio inputs, dynamic categories from `$categories`
- Step 5 blade — added Plan folder (items 17-19: Plan Source File, KML, Plan PDF) → 19 total items
- Step 6 blade — form action, method, CSRF, draft fallbacks, AJAX save
- Step 7 blade — dynamic `$previewData` from session draft (not hardcoded)
- `CustomerController` methods: `saveStep1()` through `saveStep6()` + `uploadDocument()`
- `submit()` reads from `session('lease_draft')` instead of hardcoded values

#### Phase 4.2 — Process Flow Backend (🔴 Critical) ✅
- `validateApplication()` — Process Flow 6.2 (pass/fail with remarks)
- `approveApplication()` — Process Flow 6.3 (locks document set)
- `rejectApplication()` — sends back for revision with reason modal
- `generateReport()` — Process Flow 6.4 (HTML compliance dossier with Print/PDF)
- Dynamic stepper in `viewapplication.blade.php` bound to `$application->status`
- Added `validated` and `revision_required` to lease_applications status enum
- Workflow action panel with context-sensitive buttons (Validate → Approve → Download)

#### Phase 4.3 — Data Corrections (🟡 Important) ✅
- Fixed MDCC name → "Mining Dues Clearance Certificate"
- Fixed Rule 12 code → `Rule 12 (2-A)(a)`
- Added 18 document_fields for Documents folder, 6 for Lease Application, 3 for Plan
- Removed extra "Survey Reports" folder from lease module
- Fixed Step 4 count: "Form + 5 affidavits" (was 4)
- Step 5 now shows all 3 folders with 19 items total

#### Phase 4.4 — ActivityLog Integration (🟡 Important) ✅
- `logActivity()` helper using `loggable_type`/`loggable_id` columns
- Logs: `application_submitted`, `data_validated`, `application_approved`, `report_generated`
- Real timeline displayed in viewapplication sidebar from `activity_logs` table
- `mimasCredential()` HasOne relationship added to LeaseApplication model

### Verification:
- Application LA-2026-0006: Created → Validated → Approved → Report Generated
- 19 physical files on disk across 3 folders (9 Documents + 7 Lease App + 3 Plan)
- 4 audit log entries automatically created
- Report HTML: 12,791 bytes with all applicant, lease, document, and MIMAS data
- Browser verified: Dossier page renders with dynamic stepper, workflow buttons, and timeline

## Summary of Delivered Architecture:
1. **40 Database Tables Active + Extended Enum:**
   - `lease_applications.status` enum now includes `validated` and `revision_required`
   - 27 document_fields seeded across 3 lease folders (Documents: 18, Lease Application: 6, Plan: 3)
   - 8 Lease Categories with corrected codes and names
   - 3 Lease Folders (removed extra "Survey Reports")
2. **Session-Based Draft Persistence:**
   - All wizard steps save to `session('lease_draft')` via AJAX POST
   - Draft cleared after successful submission
   - File uploads stored in `public/uploads/lease_drafts/{draft_id}/` until submission
3. **Complete Process Flow (6.1-6.4):**
   - 6.1 Upload & Store → automatic on submission
   - 6.2 Validate Data → `POST /application/{id}/validate` (pass/fail)
   - 6.3 Approve Data → `POST /application/{id}/approve`
   - 6.4 Generate Reports → `GET /application/{id}/report` (printable HTML dossier)
4. **Security & Data Isolation:** AES-256 encrypted MIMAS passwords, `BelongsToBranch` trait.

## Recent Updates (2026-09-11):
- **Step 7 Dynamic Preview Verification & Cleanup**:
  - Removed all hardcoded fallbacks across `createstep7.blade.php`, `CustomerController.php`, `createstep4.blade.php`, and `createstep6.blade.php`.
  - Preview in Step 7 now strictly and only displays real data entered by the applicant across Steps 1 through 6.
  - Step 1: added Mineral Type selection and auto-binding from MIMAS lookup.
  - Step 4: dynamic category rule name in guidance banner.
  - Step 5 & 7: dynamic 3-folder upload checklist counters and actual file attachment badges.
  - Step 6: manual-only MIMAS credential entry with show/hide password toggle and encrypted persistence.

- **View Application Dossier (`/viewapplication`) Audit & 100% Dynamic Upgrade (COMPLETED & VERIFIED) ✅**:
  - **Identified & Purged Hardcoded Elements**:
    - Replaced static "Complete" / "Verified" KPI badges with dynamic status indicators reflecting real DB document counts (`$regCount >= 16`, `$planCount >= 3`, `$mimas->portal_status`).
    - Replaced hardcoded "19 total items" and "all 19 files" text with dynamic `{{ $docs->count() }}`.
    - Fixed Timezone mismatch (`APP_TIMEZONE=Asia/Kolkata`) which previously caused Carbon to report "4 hours from now".
  - **Added "Lease & Applicant Information" Dossier Card**:
    - Now displays all authentic applicant data entered in Steps 1-6 (Customer, Mineral, District, Taluk, Extent in Ha, Survey Nos, Lease Category, Lease Period, Aadhaar, PAN, GSTIN, Contact info).
  - **Clean 1-19 Document Sorting**:
    - Sorted all attached files numerically (Item 1 through Item 19) with direct file view links to verified files in `public/uploads/lease_applications/{app_no}/`.
  - **Verified via Automated Test**:
    - Compiled view verified with zero errors (HTML length: 62,466 bytes, all fields confirmed).

  - **Scrutiny Dossier Document Status & Honest State Resolution (COMPLETED & VERIFIED) ✅**:
    - **Eliminated Fake File Auto-Generation**: Removed legacy demo logic in `CustomerController@submit` that created fake ~800-byte PDF/KML files and assigned hardcoded initial statuses (`validated` / `uploaded`) when no documents were uploaded.
    - **Honest Document States**: Non-uploaded files now strictly remain `status = 'pending'`, `file_path = null`, showing `"Not uploaded by applicant"` and `"Pending"` badges with a direct `"Upload"` link.
    - **Dual-Level Validation Engine**:
      1. **Bulk Stage Validation (Process Flow 6.2)**: "Pass Validation" button marks the entire application as `validated` and automatically upgrades all real uploaded documents (`whereNotNull('file_path')`) to `status = 'validated'`.
      2. **Granular Per-Document Scrutiny**: Interactive action column with `[✓ Mark Valid]`, `[✗ Flag for Revision]` (with correction note modal), and `[Undo]` buttons powered by real-time AJAX (`POST /application/document/{id}/status`).
    - **Real-Time Audit Trail**: Every document status change or officer note is logged to `activity_logs` in real time with IST timestamps.
    - **Application #7 Reset**: Purged legacy placeholder files on disk and reset all 19 document slots to `pending` to honestly reflect the applicant's upload actions.

- **Lease Application System-Wide Hardening & Bug Fixes (24 Issues Resolved) ✅ (2026-09-11)**:
  - **Milestone 1: Core Architecture, Model & Controller Hardening**:
    - Workflow security: POST routes (`/validate`, `/approve`, `/reject`, `/document/{id}/status`) elevated to `permission:application.edit`.
    - Double-encryption eradicated: `MimasCredential` handles encryption cleanly via model cast `'password' => 'encrypted'`; raw passwords passed directly without redundant `Crypt::encryptString`.
    - Concurrency-safe atomic number generators: `generateDraftAppNumber()` and `generateOfficialAppNumber()` implemented with row locking `FOR UPDATE` and `withTrashed()`.
    - Active draft tracking: `step4()` automatically advances draft `current_step = 4`.
    - Password edit safety: Masked placeholder `__UNCHANGED__` preserves existing credentials without accidental overwrites.
    - Null-safe dossier access: Missing or invalid application IDs gracefully redirect to `/application` with friendly Toastr error notification.
  - **Milestone 2: Wizard Steps 1–4 Hardening**:
    - Browser tab titles corrected across all steps (`Lease Application - Step 1` through `Step 4`).
    - Purged fake hardcoded district and mineral IDs (1-5, 1-8).
    - Browser native validation triggered via `.reportValidity()` before AJAX dispatch.
    - Legacy browser `alert()` replaced with Toastr notifications.
    - Form action buttons wrapped in `@can('application.create')`.
    - Dynamic folder progress calculation in Step 4 based on actual uploaded documents.
  - **Milestone 3: Wizard Steps 5–7 Hardening**:
    - Completely deleted dummy fake PDF generator button and script (`#btn_upload_all_trigger`).
    - Fixed file size display formatting (B, KB, MB) in Step 5.
    - Step 5 gating: Users cannot advance to Step 6 without uploading required documents.
    - Step 6: MIMAS password masked with `__UNCHANGED__` placeholder to prevent plain-text exposure in HTML source; Enter key default submission intercepted.
    - Step 7: Double-submission protection with button disabled state and spinner; misleading "draft placeholders" copy purged.
  - **Milestone 4: Dossier & Compliance Report Hardening**:
    - Fixed KPI badge icons (Approved checkmark, Review alert).
    - Accurate document counts (`count / 19 Uploaded`) in `/application` list.
    - View/Scrutiny buttons unified and guarded with `@can('application.view')` and `@can('application.create')`.
    - Removed dangerous `?? 1` fallback application IDs in `viewapplication.blade.php`.
    - Compliance report PDF purged of hardcoded Coimbatore dummy strings (`Kinathukadavu`, `Vadakkipalayam`, `21.00 Acres`, `Rough Stone & Gravel`, `Rule 44`); all metrics rendered from database models with null-safety.
  - **Milestone 5: Verification & Quality Gate**:
    - Comprehensive verification suite (`test_all_fixes.php`) executed: **14/14 PASSED with ZERO ERRORS**.
    - **Playwright MCP Live Browser QA Testing Executed (100% SUCCESS)**:
      - Authenticated as `admin@gtms.com` via `/login`.
      - Tested `/application` list page: verified KPI cards, dynamic document counters (`0/19`, `1/19`, `19/19`), and unified Dossier/Scrutiny buttons.
      - Tested `/viewapplication?id=6`: executed granular scrutiny (marked item #3 valid via AJAX, undo back to uploaded, flagged with custom revision note modal, saved to audit trail).
      - Tested full multi-step wizard (Steps 1 through 7):
        - Step 1: MIMAS lookup `#TN-MMS-SLM-001` auto-populated Sri Bala Traders data, Salem district, and Gravel mineral without browser alert.
        - Step 2: 10-digit mobile filter verified.
        - Step 3: Rule 44 category selected.
        - Step 4: dynamic folder checklist view verified.
        - Step 5: zero-file gating verified (blocked skipping to Step 6); uploaded real `test_land_document.pdf`; restored `formatFileSize()` helper; dynamic counter updated to `1 / 19`.
        - Step 6: entered password `MimasPass@2026`, verified show/hide eye toggle using jQuery `prop('type')`.
        - Step 7: verified 100% dynamic applicant summary; submitted application with double-click protection.
      - Verified created application `LA-2026-0009`: progressed through Stage 6.2 (Validate Data) and Stage 6.3 (Approve Application).
      - Verified compliance dossier PDF report at `/application/9/report`: rendered authentic data with zero dummy Coimbatore strings.

- **View Application Dossier (`/viewapplication`) KPI Cards UI/UX Pro Max Refactor ✅ (2026-09-11)**:
  - **Root Cause Identified**: Global CSS collision on `.stat-card` in `public/css/style.css` (line 28243 applied `display:flex; align-items:center; gap:.9rem;` globally). This forced the 4 child elements (`.stat-icon`, `.stat-value`, `.stat-label`, `.stat-sub`) into a single cramped horizontal row, causing vertical line-wrapping of numerals (`1 /` above `16`, `1` above `hour`), squeezed labels, and distorted badges as reported in user image `media_1789118121336.png`.
  - **UI/UX Pro Max Redesign Applied**:
    - Replaced conflicted classes with isolated scoped `.dossier-kpi-card` component and styles.
    - Implemented a structured 3-tier vertical hierarchy:
      1. **Header**: Muted label (`0.78rem`, font-weight 600) + accent icon container (38x38px, soft tinted pastel background with border: Teal `#e6fffa`, Purple `#f3e8ff`, Green `#ecfdf5`, Orange `#fff7ed`).
      2. **Primary Metric**: Bold high-contrast typography (`font-family: 'Sora', sans-serif`, `font-size: 1.6rem`, `font-weight: 700`, `font-variant-numeric: tabular-nums`, `white-space: nowrap`) with soft muted denominator (`/ 16`, `/ 3`, `/ 1`).
      3. **Footer**: Micro-pill status chip with icon (`pill-success`, `pill-warning`, `pill-neutral`).
    - Fixed Card 4 icon using Bootstrap icon `bi-hourglass-split`.
    - Enhanced real-time dynamic JavaScript handlers for granular document scrutiny and flag modals to update `#kpi-validated-count` and `#kpi-validated-sub` pills with proper CSS classes and icons.
  - **Multi-Viewport Visual Verification via Playwright MCP**:
    - Desktop (1440x900): Balanced 4-column layout, zero line wrapping, smooth hover elevation.
    - Tablet (992x800): Responsive 4-card row with proportional spacing.
    - Mobile (480x800): Responsive 2-column grid (`col-6`) without content overflow or clipping.

- **Soft-Deleted Customer Relationship Resiliency & Null-Safety Across Modules ✅ (2026-09-11)**:
  - **Root Cause Identified**: User soft-deleted customers via `/customers`. Because `Customer` uses `SoftDeletes`, standard `belongsTo(Customer::class)` relationship excludes soft-deleted records from queries (`whereNull('deleted_at')`). In `customer.blade.php:164`, evaluating `$app->customer->company_name ?? $app->customer->customer_name` threw fatal `ErrorException: Attempt to read property "customer_name" on null` when `$app->customer` evaluated to null.
  - **Relational Architecture Fix**: Added `->withTrashed()` to `belongsTo(Customer::class)` across all 7 module models (`LeaseApplication`, `MiningApplication`, `EnvironmentProject`, `EcCertificate`, `DgpsSurvey`, `DroneSurvey`, `PptApplication`).
  - **Universal Null-Safety & Trashed Badging**:
    - In `customer.blade.php`: Wrapped with null-safe operators (`$app->customer?->...`) and added a clear `Deleted` status badge for audit clarity.
    - In `viewapplication.blade.php`: Fixed header, applicant details card, and MIMAS card with null-safe accessors and trashed indicator.
    - In `report_pdf.blade.php`: Upgraded compliance report generation to null-safe accessors.
  - **Playwright MCP Verification**: Verified `/application`, `/viewapplication?id=9`, and `/application/9/report` load with HTTP 200 and display soft-deleted entities with clean `Deleted` badges without throwing 500 errors.

- **Customers Directory (`/customers`) Action Button Alignment & Theme Palette Polish ✅ (2026-09-11)**:
  - **Root Cause Identified**: 
    - The Action buttons column (`View`, `Edit`, `Delete`) lacked an explicit minimum width (`min-width: 125px`) and a flexbox container with `white-space: nowrap`. Because DataTables auto-calculated column width based on the short header text "Actions" (~60px), the Delete button wrapped down into an awkward second line forming an "L" shape.
    - Harsh, mismatched magenta/pink colors (`#D653C1` from template `.btn-info`) were applied to the View button, "+ Add Customer" buttons, breadcrumb active link, and Aadhaar badge, clashing with the GTMS Deep Navy & Gold palette.
  - **UI/UX Pro Max Refactor Applied**:
    - **Single-Row Action Buttons**: Wrapped buttons in `.table-action-group` (`display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px; white-space: nowrap;`) with `32x32px` rounded-2 geometry, preventing wrapping.
    - **Theme Palette Harmonization**:
      - **View 360° Profile**: Soft Navy/Sky Blue tint (`#eff6ff`, border `#bfdbfe`, icon `#1d4ed8`), hover `#1d4ed8`.
      - **Edit Customer**: Warm Ore-Gold/Amber tint (`#fef3c7`, border `#fde68a`, icon `#b45309`), hover `#d97706`.
      - **Delete Customer**: Soft Rose/Crimson tint (`#fee2e2`, border `#fecaca`, icon `#dc2626`), hover `#dc2626`.
      - **"+ Add Customer" Buttons**: Replaced `.btn-info` with `.btn-navy` (`#0F1E4D`) matching brand styling across the system.
      - **Aadhaar Badge**: Replaced pink `.bg-info-subtle` with official calm blue identity badge (`#e0f2fe`, border `#bae6fd`, text `#0369a1`).
      - **Avatar Chips**: Styled with GTMS gradient (`#0F1E4D` to `#1B3A8C`).
      - **Modal Form Icons**: Replaced `text-info` with `text-primary`.
  - **Playwright MCP Verification**: Verified `/customers` table renders all 3 action buttons in a single horizontal row with smooth hover effects, opens Edit modal with populated values, and navigates to 360° profile cleanly.

## Next Steps:
- Phase 4.5: Security & Backup (spatie/laravel-backup, protected file downloads)
- Mining Plan 6.1-6.6 stage validation workflow binding


