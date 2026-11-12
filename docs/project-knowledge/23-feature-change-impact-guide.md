# 23 — Feature Change Impact Guide

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Use this before accepting or estimating any client requirement.
> **Ground rule:** `main` deploys itself to production within about 5 minutes and runs migrations automatically ([20](20-testing-deployment-configuration.md) §5). Every change therefore ends in a plan approved by a human **before** any code is written or merged.

## Part A — 12-step impact-analysis procedure

1. **Capture the requirement verbatim.** Record the client's words, the requester, the date, and the business reason. List what is ambiguous as questions. Do not resolve ambiguity by guessing.
2. **Classify the change.** Pick one or more: new field · new validation · new status / transition · new document type · new module · report / print change · permission change · integration · data correction · bug fix. Note whether it touches money, credentials, PII or statutory documents (these need extra review).
3. **Locate the module(s).** Use the module cards (Part B, and [04](04-module-documentation.md)) to name the controller, views, models, tables and routes involved. Use [15](15-cross-module-dependencies.md) to list the downstream readers (tracking, ledger, reports, dashboard, master pipeline).
4. **Trace the current behaviour end to end.** Follow user action → view / JS → route ([09](09-routes-api-documentation.md)) → middleware / permission → controller method → models (BranchScope!) → tables → response. Read the actual code. These docs are a map, not a substitute.
5. **Check data impact.** Does the change need a schema change ([07](07-database-schema.md))? Are there existing rows that would violate a new rule? Are status values stored as free text that must be migrated? Remember that migrations run automatically in production. Every migration must be additive, reversible and safe on live data. Plan a backup ([20](20-testing-deployment-configuration.md) §5.2).
6. **Check cross-module side effects.** Look for literal status strings, `application_type` literals, numbering generators, `common_id` / master links, file paths copied between modules, and money columns mirrored in `application_payments` and receipts. Search the whole repository (PHP **and** Blade) for every literal you change.
7. **Check permissions and branch isolation.** Which permission gates the route? Is a new permission needed (seeder plus existing roles)? Does the new query run under `BranchScope`? Does any lookup by ID bypass branch isolation? Admin / Super Admin bypass everything through `Gate::before`.
8. **Check security and privacy.** File uploads (extension, storage root, public exposure), credentials, PII, mass assignment, XSS in Blade (`{!! !!}`), CSRF, and authorisation on every alias route ([18](18-security-audit.md)).
9. **Check the UI and JS contract.** Wizard session keys, step views, the JSON shape expected by the page scripts (`status` / `redirect`, `success`), print views, DataTables and dashboard counters.
10. **Define tests.** Name the existing feature tests in `tests/Feature` that cover the area, and the new tests needed (happy path, validation failure, permission denied, other-branch user, regression on dependent modules). Run them only against a disposable database ([20](20-testing-deployment-configuration.md) §1).
11. **Plan the rollout and rollback.** Feature-branch work only, never a direct push to `main`. Decide the migration order, the data back-fill, a manual `nas-deploy.sh` with backup versus Watchtower auto-deploy, a verification checklist on the NAS, and a rollback (image tag plus DB restore point).
12. **Write the change plan and get approval.** Produce a short plan: requirement, affected files / functions / tables, risks found (with IDs from [21](21-known-issues-risks.md)), test list, rollout and rollback, effort estimate, open questions. **Stop and wait for explicit approval.** Only after approval does implementation start.

## Part B — Module change-readiness cards
For each module: functionality · workflow · key files / functions · tables · dependencies · validations · rules · limits · impact areas.

### Lease application
* **Function / workflow:** 8-step wizard → submit → validate / approve / reject → move to mining ([13](13-business-workflows.md) W1).
* **Files:** `app/Http/Controllers/CustomerController.php` (`saveStep1..7`, `uploadDocument`, `submit`, `validateApplication`, `approveApplication`, `rejectApplication`, `moveToMining`, `generate*AppNumber`); `resources/views/pages/lease_application/*`.
* **Tables:** lease_applications, lease_application_minerals, lease_categories, lease_documents, mimas_credentials, application_handlers / payments, customers, activity_logs.
* **Validations:** step-specific inline rules (see [05](05-controller-analysis.md)).
* **Rules:** R-LEASE, R-CUST, R-ID.
* **Limits:** session-based draft; hard-coded folder / doc IDs; `Branch::first()`.
* **Impact areas:** customer directory, mining creation (document cloning), master pipeline (`createOrGetMasterProject` reads lease fields), tracking / invoices, dashboard counters, lease report view.

### Mining plan
* **Files:** `MiningController.php` (`store`, `autoSaveStep`, `uploadDocument`, `advanceStage`, `moveToEnvironment`, `generateMiningAppNumber`); `views/pages/mining-portal/*` (newapplication 7,229 lines).
* **Tables:** mining_applications, mining_application_minerals, mining_documents, nature_of_works, plan_types, applicant_types, document_fields.
* **Rules:** R-MINING. **Limits:** unvalidated stage; no gate to environment.
* **Impact:** environment creation, DGPS / drone links, master pipeline, payments, tracking.

### Environmental clearance (B1 / B2)
* **Files:** `EnverionsoneController.php` (`store`, `show`, upload / remove / review, `updateStatus`, `submitSc1ToPpt`, `submitSc2ToPpt`, `submitB2ToPpt`, `generateSlots`); legacy `EnvironmentalB2Controller.php`; `views/pages/eviron/*`, `views/pages/enviro_b2/*`.
* **Tables:** environment_projects, environment_documents, folders / document_fields (module 1), customers.
* **Rules:** R-ENV. **Limits:** free-text status; legacy duplicate controller; public-disk storage.
* **Impact:** PPT (auto-created records), EC certificate, EC compliance, tracking, payments.

### PPT department
* **Files:** `PptDepartmentController.php` (`store`, `approvePresentation`, `updateStatus`); `views/pages/ppt_department/*`.
* **Tables:** ppt_applications, ppt_documents. **Rules:** R-PPT. **Impact:** the environment project state machine.

### EC certificate / EC compliance
* **Files:** `EcCertificateController.php`, `EcComplianceController.php`; views `ec_certificate/*`, `ec_compliance/*`.
* **Tables:** ec_certificates (not branch-scoped), ec_compliances, ec_compliance_documents.
* **Limits:** placeholder numbers on create; no expiry automation.
* **Impact:** environment status, tracking, payments.

### DGPS / Drone surveys
* **Files:** `DgpsSurveyController.php`, `DroneSurveyController.php`; views `dgps_survey/*`, `drone_survey/*`.
* **Tables:** dgps_surveys, dgps_documents, drone_surveys, drone_documents.
* **Limits:** created as completed / deliverables_ready; no transition endpoints.
* **Impact:** dashboard, master pipeline stage 2, tracking, payments.

### Customer directory / tracking
* **Files:** `CustomerDirectoryController.php`, `CustomerTrackingController.php`; views `customers`, `customer_show`, `customer_tracking/*`.
* **Rules:** R-CUST. **Limits:** PII exposure in lookup; invoices not stored.
* **Impact:** every module (FK restrict), all accounts screens.

### Accounts
* **Files:** `Accounts/*Controller.php`; `PaymentReceipt::generateReceiptNumber`, `Quotation::calculateTotals`; views `accounts/*`.
* **Tables:** quotations, quotation_items, bank_accounts, payment_receipts, application_payments, the application money columns.
* **Rules:** R-PAY. **Limits:** no void / refund; overpayment allowed; money duplicated.
* **Impact:** ledger, reports, CSV export, dashboard financial snapshot, tracking invoices, print views (seal).

### Master pipeline
* **Files:** `MasterPipelineService.php`, `MasterPipelineController.php`; views `master_pipeline/*`.
* **Tables:** master_projects, pipeline_movements, notifications, the department tables (status writes).
* **Rules:** R-PIPE. **Limits:** no permissions; never reaches completed; independent of the module move buttons.
* **Impact:** all department records' status, notifications.

### Administration (users / roles / branches)
* **Files:** `UserController`, `RolesController`, `BranchController`, `RolePermissionSeeder`, `AppServiceProvider`, `BranchScope`.
* **Rules:** delegated-admin subset rules ([14](14-user-roles-permissions.md) §4).
* **Impact:** every route (permissions), every branch-scoped query.

### Uploads
* **Files:** `ChunkedUploadService`, `ChunkUploadController`, jobs, `public/js/gtms-chunk-uploader.js`, each module's `uploadDocument` / `removeDocument`.
* **Impact:** security (S1), storage roots, Nginx rules, backups.

## Part C — Quick checklists

**Adding a field to a wizard:** migration (nullable, additive) → model `$fillable` / `$casts` → step view input + inline JS → `saveStepN` validation + session key → `store` / `submit` mapping → show / dossier view → tracking / print views if displayed → feature test.

**Adding or renaming a status:** search PHP + Blade for the literal → controllers' transition guards → dashboard closure counters → index KPIs → MasterPipelineService `updateDepartmentRecordStatus` → badge / colour helpers in views → data migration for existing rows.

**Adding a document type:** a `document_fields` row (seed via migration) under the correct folder / module (watch the hard-coded IDs) → slot generation (`generateSlots` / submit lists) → upload allow-list → show-view folder tab.

**Adding a permission:** seeder plus a data migration to grant it to existing roles → route middleware on **every alias route** → sidebar `@can` → the `RolesController` module label map.
