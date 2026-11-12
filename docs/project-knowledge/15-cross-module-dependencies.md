# 15 — Cross-Module Dependency Map

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Use with [23-feature-change-impact-guide.md](23-feature-change-impact-guide.md).

## 1. Module → module calls and writes — VERIFIED

```mermaid
flowchart LR
  CUSTCTL[CustomerController - Lease] -->|creates/restores| CUST[(customers)]
  ENVCTL[EnverionsoneController] -->|creates/updates passwords| CUST
  CUSTCTL -->|moveToMining creates| MIN[(mining_applications + mining_documents)]
  CUSTCTL -->|submit -> moveToMining optional| CUSTCTL
  MINCTL[MiningController] -->|moveToEnvironment creates| ENV[(environment_projects + environment_documents)]
  ENVCTL -->|submit*ToPpt creates| PPT[(ppt_applications)]
  PPTCTL[PptDepartmentController] -->|approve updates| ENV
  ECCTL[EcCertificateController] -->|store sets approved| ENV
  PAYCTL[PaymentCollectionController] -->|lock + update money| LEASE[(lease)] & MIN & ENV & PPT & DG[(dgps)] & DR[(drone)] & ECC[(ec_certificates)] & HYC[(ec_compliances)]
  MPS[MasterPipelineService] -->|status = returned/in_progress| LEASE & DG & DR & MIN & ENV & PPT
  MPS -->|notifications| NOTIF[(notifications)]
  TRACK[CustomerTrackingController] -->|reads all| LEASE & MIN & ENV & PPT & DG & DR & ECC & HYC
  LEDGER[CustomerLedger / Reports / Receipts] -->|reads| LEASE & MIN & ENV & PPT & DG & DR & ECC & HYC
  DASH[/dashboard closure/] -->|reads| LEASE & MIN & DG & DR & ENV & HYC
  USERCTL[UserController@destroy] -->|checks created_by| CUST & LEASE & MIN & ENV & DG & DR & ECC
```

## 2. Shared components

| Shared item | Used by | Change risk |
|---|---|---|
| `customers` table / `Customer` model | every module, accounts, tracking, dashboard | Very high. FK `restrict` from 11 tables |
| `application_handlers`, `application_payments` + `application_type` literals | lease, mining, env, PPT, DGPS, drone, EC cert, EC compliance, payment collection, dashboard | High. A typo in the type string silently detaches data |
| Money columns on each application table | wizards, PaymentCollection, Ledger, Reports, Tracking, Receipts print, dashboard | High. Duplicated source of truth |
| `modules` / `folders` / `document_fields` | all document-bearing modules; hard-coded IDs in Customer / Enverionsone controllers | High |
| `BelongsToBranch` / `BranchScope` | 12 models | High. Affects every query for non-admin users, including number generators |
| `ChunkedUploadService` + `/api/upload/chunk/*` + `gtms-chunk-uploader.js` | lease step 5, dossier upload, mining, env, PPT, EC, DGPS, drone uploads | High (security) |
| `activity_logs` | lease, environment, PPT, EC cert, mining, dashboard, lease report | Medium. Inconsistent `loggable_type` |
| `notifications` | MasterPipelineService, NotificationController, header bell | Low |
| `InvoiceSealController::getCustomSealUrl()` | quotation / receipt / ledger / invoice print views | Low |
| Status literals (`under_scrutiny`, `scrutiny`, `completed`, `deliverables_ready`, `approved`, `returned_for_correction`) | dashboard, index KPIs, views, MasterPipelineService | High. No central enum |
| `common_id` / `master_project_id` | lease, mining, env, PPT, DGPS, drone, master pipeline | High (three ID generators) |
| Session wizard keys | each module's wizard / saveStep / store | Medium. Changing a key orphans in-progress drafts |
| Layout `layouts/app.blade.php` + `sidebar.blade.php` | all pages | Medium (menu permissions) |
| `public/js/ajax/*.js` JSON contract | admin CRUD screens | Medium |

## 3. Auth and storage dependencies
* Auth: session guard (Redis in prod) → Spatie roles → `Gate::before` → `BranchScope`.
* Storage: `public_path('uploads/...')` (lease, DGPS, B2 legacy, EC certificate, chunk assembly, users, seals) **and** `Storage::disk('public')` (environment unified, PPT, EC compliance). Removal code tries all three roots. The Docker volume persists `public/uploads` and `storage`.

## 4. External dependencies
None at runtime besides the CDN icons and the optional browser-side Google / Autodesk viewers. PARIVESH / MIMAS are data-only references (see [17](17-integrations-background-jobs.md)).
