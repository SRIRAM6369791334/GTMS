# Reference module: Lease Application

Controller: `app/Http/Controllers/CustomerController.php` (wizard steps, submit, dossier, report). Views: `resources/views/pages/lease_application/`. Session draft key: `lease_draft` (`application_id` is set after Step 1 creates the DB draft).

## Pages

| Route | View | Purpose / functions |
|---|---|---|
| `GET /application` (`application.index`) | `customer.blade.php` | Directory: KPI cards, quick status tabs, search (DataTables, mineral kept as hidden text for search), district/category/status filters, page length, view/resume/move-to-mining actions, `#modalMoveToMiningDynamic` |
| `/step1` (+ `step1.save`) | `createstep1` | Customer Unique ID lookup (`customers.lookup.mimas`, autofill), applicant/contact/statutory fields (data-v validation), mineral multi-select + "Others" name, creates customer if new + lease draft (`LA-DRAFT-YYYY-NNNN`); Save & Continue Later |
| `/step2` | `createstep2` | Contacts + MIMAS portal credentials; password eye/copy addon buttons |
| `/step3` | `createstep3` | Lease category tiles (from `lease_categories`, status=1) + "Other rule" |
| `/step4` | `createstep4` | Three folder cards (#7 Documents, #8 Lease Application, #9 Plans) with progress |
| `/step5` (+ `step5.upload/remove/requirement`) | `createstep5` | Checklist per folder, mandatory/optional select, chunked upload (`public/js/gtms-chunk-uploader.js`), `#modalAddCustomLeaseDoc`, `#modalZipFilePreview`, Open-With on view buttons |
| `/step6` | `createstep6` | Handlers table (dynamic rows, role suggestions) |
| `/step7` (+ `step7.save`) | `createstep7` | Payment: contract/paid/pending, presets 100%/50%/Clear, status pending/partial/paid/**free** with waiver reason, live KPI + status box |
| `/step8` → `application.submit` | `createstep8` | Review of all steps, Edit Step links, `#modalSubmitConfirm`, `#modalDocumentPreview`, declaration checkbox, final submit (assigns application number) |
| `/viewapplication?id=` | `viewapplication` | Dossier: masthead toolbar (PDF/Print/Copy Summary/Refresh/Promote/New, labelled), back link, KPIs, chapters I–VIII, MIMAS password eye/copy (disabled when none), scrutiny stepper, document validate/flag (`#flagDocModal`), `#revisionModal`, `#modalMoveToMining`, audit trail |
| `/application/{id}/report` | `report_pdf` (standalone) | Printable dossier; waiver note; Common ID chip no-wrap |
| `/application/{id}/resume`, `validate`, `approve`, `reject`, `move-to-mining` | — | Workflow actions |

## What changed on this module (keep consistent elsewhere)
- All steps: `lz-page` wrapper, `lz-*` classes, duplicate inline CSS stripped, stepper card, equal 44px inputs/buttons.
- Step 1: field validation (`data-v`), errors under fields, real server message on 500, category guard.
- Step 2: password addon buttons 44px.
- Step 4: equal-height folder cards. Step 5: readable "Optional" select. Step 6: wider page, table min-width.
- Step 7: full redesign (banner, KPI cards, fee entry row aligned, segmented presets, status box by `data-status`, Pro tip) + Free waiver.
- Step 8: page 1180px, equal KPI/tile heights, wrap instead of truncate, folder cards aligned.
- Directory: header action right-aligned, KPI 2×2 under 1200px, table column widths, row hover accent, pagination cleanup, extent/mineral chips removed from the list.
- Dossier: labelled toolbar, back link, MIMAS password buttons. Report: waiver note, chip fix.
- Customers page (`/customers`, `CustomerDirectoryController`): add/edit forms validated with `data-v`, server regex rules, no popup errors.
- Global: Open-With modal redesign + "Tap to open", toasts without backdrop, flash → toast.

## Master data the module needs
`districts`, `minerals`, `lease_categories` (8 TNMMCR rules from `GtmsMasterDataSeeder` §3), `folders`/`document_fields` for steps 4–5. If a dropdown/tile list is empty, check master data before blaming the UI.
