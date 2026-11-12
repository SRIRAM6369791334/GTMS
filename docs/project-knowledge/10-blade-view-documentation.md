# 10 — Blade View Documentation

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). JS: [11-javascript-frontend-analysis.md](11-javascript-frontend-analysis.md). Routes: [09-routes-api-documentation.md](09-routes-api-documentation.md).
> Depth: **PARTIALLY VERIFIED.** All 100 views were inventoried mechanically (line count, `<form>` count, AJAX call count, DataTable initialisations, Bootstrap modal count, `@extends`, `{!! !!}` raw output, `view()` references). The ≈96,000 lines were **not** read line by line. Lines quoted below were read.

## 1. Layout system — VERIFIED
* `layouts/app.blade.php` (636 lines) is the master shell. It loads ~20 template CSS files, includes `layouts/header.blade.php` (543 lines, top bar + notification bell + logout form) and `layouts/sidebar.blade.php` (727 lines; menu items gated by `@can` / permissions — INFERRED from size and purpose, not read in full), and the footer. It sets `<meta name="csrf-token">` (line 13), loads the JS stack (lines 546-629) and includes `layouts/partials/open_with_modal.blade.php`.
* `layouts/master.blade.php` is a one-line `@extends('layouts.app')` alias, used only by `master_pipeline/*`.
* Standalone, layout-free views (for printing or the guest screen): `login`, `lease_application/report_pdf`, `accounts/*/print`, `accounts/ledger/print`, `customer_tracking/{proforma_invoice,tax_invoice}`.
* Pagination uses `vendor/pagination/bootstrap-5` (`Paginator::useBootstrapFive()`); the other 8 published pagination views are unused.

## 2. View ↔ controller map and size inventory — VERIFIED
`f` = `<form>` tags · `ajax` = `$.ajax` / `fetch` / `$.post` / `$.get` calls · `dt` = DataTable inits · `m` = modals.

| View (`resources/views/…`) | Lines | f | ajax | dt | m | Rendered by |
|---|---|---|---|---|---|---|
| pages/login | 632 | 1 | 0 | 0 | 1 | AuthController@showLogin |
| pages/index (dashboard) | 1154 | 0 | 0 | 0 | 0 | route closure `/dashboard` |
| pages/notifications/index | 1033 | 5 | 5 | 0 | 0 | NotificationController@index |
| pages/authentication/users/index (+ createuser partial 436) | 1099 | 0(+2) | 0 | 0 | 0(+2) | UserController@index — AJAX via `public/js/ajax/user.js` |
| pages/authentication/roles/index (+ createrole 573) | 1011 | 0(+2) | 0 | 0 | 0(+2) | RolesController@index — `ajax/role.js` |
| pages/authentication/branch/index (+ creatbranch 345) | 945 | 0(+2) | 0 | 0 | 0(+2) | BranchController@index — `ajax/branch.js` |
| pages/master/{category,product,productstock,unit}/index (+ create partials) | 82–116 | — | — | — | — | Category / Product / ProductStock / Unit controllers |
| pages/customers | 1794 | 2 | 0 | 0 | 3 | CustomerDirectoryController@index — `ajax/customer.js` |
| pages/customer_show | 1702 | 1 | 0 | 0 | 0 | CustomerDirectoryController@show |
| pages/customer_tracking/index | 4314 | 2 | 1 | 0 | 2 | CustomerTrackingController@index/show/search |
| pages/customer_tracking/proforma_invoice · tax_invoice | 1143 · 853 | 1 | 0 | 0 | 0 | CustomerTrackingController |
| pages/lease_application/customer (list) | 1815 | 1 | 0 | 3 | 1 | CustomerController@index |
| pages/lease_application/createstep1…8 | 1407, 1102, 943, 955, **3430**, 872, 1286, 2007 | 1 each (step4: 0) | 3,2,2,0,**13**,1,1,0 | 0 | step5: 2, step8: 2 | CustomerController@step1..step8 |
| pages/lease_application/viewapplication | 3707 | 4 | 11 | 0 | 4 | CustomerController@viewApplication |
| pages/lease_application/report_pdf | 1190 | 0 | 0 | 0 | 0 | CustomerController@generateReport |
| pages/mining-portal/index | 1666 | 0 | 0 | 3 | 0 | MiningController@index |
| pages/mining-portal/newapplication | **7229** | 2 | 2 | 0 | 1 | MiningController@newApplication |
| pages/mining-portal/projectfolder | 2263 | 1 | 0 | 3 | 0 | MiningController@projectFolder |
| pages/mining-portal/document | 1172 | 1 | 2 | 0 | 0 | MiningController@Document |
| pages/mining-portal/process | 2495 | 6 | 0 | 0 | 1 | MiningController@Process |
| pages/eviron/index · create · show | 929 · 1606 · 2439 | 2 · 1 · 5 | 0 · 2 · 3 | 0 | 1 · 0 · 6 | EnverionsoneController |
| pages/enviro_b2/index · show · wizard | 151 · 226 · 894 | 0 · 3 · 1 | 0 · 0 · 3 | 0 | 0 · 2 · 0 | EnvironmentalB2Controller (legacy) |
| pages/enviro_b1/index · subcat1 · subcat2 | 124 · 196 · 197 | — | — | — | — | **Unreferenced** (index1 / index2 now redirect) |
| pages/ppt_department/index · show · wizard | 697 · 925 · 1248 | 2 · 2 · 1 | 0 · 2 · 3 | 0 | 1 · 1 · 0 | PptDepartmentController |
| pages/ec_certificate/index · show · wizard | 825 · 904 · 1409 | 2 · 1 · 6 | 0 · 2 · 3 | 0 | 1 · 1 · 0 | EcCertificateController |
| pages/ec_compliance/index · show · wizard | 687 · 1314 · 1495 | 2 · 1 · 1 | 0 · 2 · 3 | 0 | 1 · 1 · 0 | EcComplianceController |
| pages/dgps_survey/index · show · wizard | 220 · 1112 · 3219 | 1 · 0 · 1 | 0 · 2 · 3 | 0 | 0 | DgpsSurveyController |
| pages/drone_survey/index · show · wizard | 246 · 1510 · 3115 | 1 · 1 · 1 | 0 · 2 · 3 | 0 | 0 · 1 · 0 | DroneSurveyController |
| pages/accounts/quotations/index · create · edit · show · print | 1067 · 1892 · 1857 · 919 · 599 | 2 · 1 · 1 · 0 · 0 | 0 · 1 · 1 · 0 · 0 | 0 | 0 | QuotationController |
| pages/accounts/payments/create | 1627 | 1 | 1 | 0 | 1 | PaymentCollectionController@create |
| pages/accounts/receipts/index · show · print | 668 · 386 · 1319 | 1 · 0 · 0 | 1 · 0 · 0 | 0 | 1 · 0 · 0 | PaymentReceiptController |
| pages/accounts/ledger/index · show · print | 354 · 656 · 477 | 1 · 1 · 0 | 0 · 1 · 0 | 0 | 0 · 1 · 0 | CustomerLedgerController |
| pages/accounts/reports/index | 1269 | 1 | 0 | 0 | 1 | FinancialReportController@index |
| pages/accounts/partials/* (bank_account_modal, bank_account_selector, empty_state, export_modal, filter_chips, invoice_seal_modal, theme) | 44–638 | — | seal modal 2, bank modal 1 | — | — | `@include` by the accounts pages |
| pages/master_pipeline/index · timeline | 596 · 1098 | 1 · 3 | 0 | 0 | 0 · 3 | MasterPipelineController |
| welcome | — | — | — | — | — | **Unreferenced** (Laravel skeleton) |
| pages/master/productstock/createproductstock | 0 | — | — | — | — | **Empty file** |

## 3. Patterns — PARTIALLY VERIFIED
* **Wizards** (lease, mining, DGPS, drone, PPT, EC certificate, EC compliance, environment create) post each step via AJAX and receive `{status, redirect}` JSON, or post a normal form and receive a redirect. The server keeps the state in the session.
* **Document folders**: show pages render folder tabs from `folders` / `document_fields` and per-document upload / remove / review buttons that call the module's upload / remove endpoints. Large files go through `GtmsChunkUploader` and are then attached with the returned `upload_token`.
* **Modals**: Bootstrap 5 modals for CRUD (users, roles, branches, customers), status-change dialogs (`updateStatus`, return-for-correction), the "Open with" chooser, the seal upload and the bank account picker.
* **DataTables**: only on the lease list and the mining index / project folder (3 each). Other lists are server-rendered and some use Laravel pagination.
* **Output escaping**: almost all output is `{{ }}`. Raw output occurs in three places (VERIFIED):
  * `lease_application/createstep1.blade.php:93` — `el.value = '{!! addslashes($value) !!}'` inside a `<script>`. `addslashes` does not escape `<` / `</script>`, so a stored customer field containing `</script><script>…` breaks out (stored XSS; Medium).
  * `index.blade.php:1089` — `{!! json_encode($chartData) !!}` (numeric data; Low).
  * `accounts/quotations/print.blade.php:397` — `{!! nl2br(e($quotation->address)) !!}` (escaped first; safe).
* **Sensitive data in views**: 66 references to `parivesh_password` / `email_password` / `mimas_password` across views. The UI deliberately displays third-party portal passwords (feature-tested by `tests/Feature/MimasPasswordViewTest.php`).

## 4. Impact notes for UI changes
* Wizard step views are very large single files with inline `<script>` blocks. A change to a field normally touches the step view, the controller `saveStepN` validation, the session key, the `store` / `submit` mapping, the show view and often `CustomerTrackingController::buildCustomerDossier`.
* Status labels and colours are computed in views and in the dashboard closure from literal status strings. Search all of `resources/views` for the literal before renaming a status.
