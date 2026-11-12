# 06 — Model Analysis

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Schema: [07-database-schema.md](07-database-schema.md). Relationships: [08-database-relationships.md](08-database-relationships.md).
> Source: `app/Models/*.php` (58 models + `Scopes/BranchScope.php` + `Traits/BelongsToBranch.php`). Structure was extracted for all 60 files; the core files were read in full. All tags are VERIFIED unless marked otherwise.

## 1. Cross-cutting behaviour

### BelongsToBranch + BranchScope (multi-tenancy)
* `BelongsToBranch::bootBelongsToBranch()` adds `BranchScope` as a global scope. On `creating`, it fills `branch_id` from the logged-in user when the field is empty.
* `BranchScope::apply()`: when a user is logged in, **and** `user.branch_id` is not empty, **and** `user.role_id !== 1`, **and** the user has neither role `Admin` nor `Super Admin`, it adds `WHERE <table>.branch_id = user.branch_id`.
* Models using it (12): `Customer`, `LeaseApplication`, `MiningApplication`, `EnvironmentProject`, `PptApplication`, `EcCompliance`, `DgpsSurvey`, `DroneSurvey`, `MasterProject`, `MineralStockpile`, `PaymentReceipt`, `Quotation`.
* **Not scoped:** `EcCertificate`, every document model (`LeaseDocument`, `MiningDocument`, `EnvironmentDocument`, `PptDocument`, `DgpsDocument`, `DroneDocument`, `EcComplianceDocument`), `ApplicationHandler`, `ApplicationPayment`, `ActivityLog`, `Notification` (filtered manually by notifiable), `ChunkedUpload`.
* Consequence: document endpoints that load by document ID (`EnvironmentDocument::find($docId)` and similar) are not branch-isolated (see [18-security-audit.md](18-security-audit.md) S9).

### SoftDeletes (17 models)
Customer, DgpsSurvey, DroneSurvey, EcCertificate, EcCompliance, EcComplianceDocument, EnvironmentDocument, EnvironmentProject, LeaseApplication, LeaseDocument, MasterProject, MiningApplication, MiningDocument, PaymentReceipt, PptApplication, PptDocument, Quotation. The dashboard "Archived" KPI counts `onlyTrashed()` leases and mining applications.

### Mass assignment
`$guarded = []` on `User`, `Branch`, `Product`, `ProductStock`, `Unit`, `EnvironmentalProject/Document/Activity`. The other models declare `$fillable` (not copied here). `User` being fully unguarded matters, because controllers assign fields explicitly but any future `User::create($request->all())` would allow setting `role_id` / `branch_id`.

### Polymorphic-by-convention tables
`application_handlers` and `application_payments` hold both a string pair (`application_type`, `application_id`) **and** Laravel morph columns (`handlerable_*`, `payable_*`). Model relations use the string pair with a type filter:

| Model | `application_type` literal |
|---|---|
| LeaseApplication | `lease` |
| MiningApplication | `mining` |
| EnvironmentProject | `environment` |
| PptApplication | `ppt` |
| DgpsSurvey | `dgps` |
| DroneSurvey | `drone` |
| EcCertificate | `ec_certificate` |
| EcCompliance | `ec_compliance` (handlers / payments; `PaymentCollectionController` also accepts `ec`) |

`payment_receipts.application_type` uses the same vocabulary, plus `general` and `ec`.

## 2. Model catalogue

| Model | Table | Traits | Key relations | Notes |
|---|---|---|---|---|
| User | users | HasFactory, Notifiable, HasRoles | role (role_id), branch | `$hidden` = password, remember_token only — **`show_password` not hidden**; `password` cast `hashed` |
| Role | roles | extends Spatie Role | creator, directUsers (users.role_id) | `$fillable` name, guard_name, created_by |
| Branch | branches | — | users, leaseApplications, miningApplications | UI label "Department" |
| Customer | customers | SoftDeletes, BelongsToBranch | district, mineral, user, creator, leaseApplications, miningApplications, environmentProjects, pptApplications, … | Route key = `slug`; `booted()` generates a unique slug on create / update; `getAreaNumericAttribute` |
| District, Mineral, LeaseCategory, PlanType, ApplicantType, NatureOfWork, Module, Folder, DocumentField | lookup tables | — | hasMany into modules | `Folder` → module, documentFields, and the per-module document models |
| LeaseApplication | lease_applications | SoftDeletes, BelongsToBranch | customer, district, category (LeaseCategory), mineral, minerals (pivot `lease_application_minerals`), branch, inspector, creator, documents, surveyNumbers, mimasCredentials, miningApplications, handlers, payments, masterProject | casts area string, money decimal:2, dates |
| LeaseDocument | lease_documents | SoftDeletes | leaseApplication, folder, documentField, reviewer, uploader | |
| LeaseSurveyNumber | lease_survey_numbers | — | leaseApplication | read by MasterPipelineService only |
| MimasCredential | mimas_credentials | — | leaseApplication | `password` cast **`encrypted`**, hidden |
| MiningApplication | mining_applications | SoftDeletes, BelongsToBranch | customer, natureOfWork, leaseApplication, parentPlan / revisedPlans (self), applicantType, district, mineral, minerals pivot, planType, documents, handlers, payments, masterProject | |
| MiningDocument | mining_documents | SoftDeletes | as LeaseDocument | |
| MiningBoundaryPoint, MiningProductionSchedule | — | — | miningApplication | **unused** |
| EnvironmentProject | environment_projects | SoftDeletes, BelongsToBranch | customer, miningApplication, leaseApplication, district, creator, documents, handlers, payments, pptStage1 / pptStage2 (ppt_stage_*_id), masterProject | accessors `sub_category_label`, `folder_names` (decides the slot folders), `category_badge` |
| EnvironmentDocument | environment_documents | SoftDeletes | project, folder, documentField, reviewer, uploader | |
| EnvironmentalProject / Document / Activity | environmental_* | — | — | **unused legacy prototype** |
| PptApplication | ppt_applications | SoftDeletes, BelongsToBranch | customer, environmentProject, district, mineral, creator, agendas, documents, handlers, payments | |
| PptDocument, PptAgenda | ppt_documents, ppt_agendas | — | — | PptAgenda **unused** |
| EcCertificate | ec_certificates | SoftDeletes (**no branch scope**) | environmentProject, customer, leaseApplication, creator, handlers, payments | |
| EcCompliance, EcComplianceDocument | ec_compliances, ec_compliance_documents | SoftDeletes (+BelongsToBranch on EcCompliance) | customer, environmentProject, ecCertificate, district, mineral, documents | |
| DgpsSurvey, DgpsDocument, DgpsPoint | dgps_* | SoftDeletes, BelongsToBranch (survey) | customer, leaseApplication, miningApplication, surveyor, creator, points, documents, masterProject | DgpsPoint **unused** |
| DroneSurvey, DroneDocument | drone_* | SoftDeletes, BelongsToBranch (survey) | customer, lease, mining, creator, documents, masterProject | |
| MasterProject | master_projects | SoftDeletes, BelongsToBranch | customer, branch, district, leaseApplication, dgpsSurvey, droneSurvey, miningApplication, environmentProject, pptApplication, movements, latestMovement, creator | scopes `active`, `returnedForCorrection`, `stage`, `search` |
| PipelineMovement | pipeline_movements | — | masterProject, officer | `metadata` array; helpers isReturn / isForward / isResubmission; badge accessors |
| Quotation, QuotationItem | quotations, quotation_items | SoftDeletes, BelongsToBranch (quotation) | customer, leaseApplication, district, items, bankAccount, creator, branch, receipts | `calculateTotals()`, `amount_in_words` |
| PaymentReceipt | payment_receipts | SoftDeletes, BelongsToBranch | customer, quotation, creator, branch | `generateReceiptNumber()` (withoutGlobalScopes, `GTMS/REC/YYYY/NNNN`); accessors application, application_reference, installment_number, total_installments, amount_in_words |
| BankAccount | bank_accounts | — | quotations, creator | `display_name` |
| ApplicationHandler, ApplicationPayment | application_handlers, application_payments | — | morphTo handlerable / payable | see §1 |
| ActivityLog, ArchivedActivityLog | activity_logs, archived_activity_logs | — | loggable (morphTo), user | `loggable_type` values are inconsistent: `'lease_application'` string vs FQCN `App\Models\EnvironmentProject` |
| Notification | notifications | — | — | Custom model over the Laravel notifications table; accessors title / message / category / action_url / icon / style_config / is_read / time_ago |
| ChunkedUpload, ChunkedUploadPart | chunked_uploads, chunked_upload_parts | — | parts, user / chunkedUpload | helpers getProgressPercent, getMissingChunkIndices, markAsFailed |
| ProjectFlow | project_flows | — | flowable, handler | **unused** |
| MineralStockpile, MineralStockEntry, MineralDispatch | mineral_* | BelongsToBranch (stockpile) | — | **unused** |
| Category, Product, ProductStock, Unit | legacy inventory | — | product→category, branch | see [04](04-module-documentation.md) M4 |
