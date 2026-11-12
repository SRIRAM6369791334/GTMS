# 04 — Module Documentation

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Workflows with diagrams: [13-business-workflows.md](13-business-workflows.md). Rules: [12-business-logic.md](12-business-logic.md). Routes: [09-routes-api-documentation.md](09-routes-api-documentation.md).
>
> Tags: **VERIFIED** = read in code; **PARTIALLY VERIFIED** = key paths read, remainder inferred from structure; **INFERRED** = deduced, not confirmed; **UNKNOWN**.

Each module card uses this structure: what it is · why · who · data · steps · tables · controllers · statuses · failure paths · completion · dependents · limits / impact areas.

---

## M1. Authentication & Session — VERIFIED
* **What:** Login by email *or* `user_code`, with optional remember-me, plus logout. `AuthController` (`showLogin`, `login`, `logout`).
* **Who:** All staff. **Data:** `users`, `sessions` (Redis in prod).
* **Steps:** `GET /` or `/login` → `POST /login` (throttle 15/min) → validate → lookup user by `email OR user_code` → reject if `status != 1` → `Auth::attempt` on the email → regenerate session → redirect to `/dashboard`.
* **Failure paths:** "No account found…" (reveals that the account does not exist), "account is inactive" (reveals status before the password is checked), "Incorrect password".
* **Limits:** No password reset UI (the `password_reset_tokens` table exists but no route uses it). No 2FA. No lockout beyond the throttle.

## M2. Dashboard — VERIFIED
* `GET /dashboard` closure in `routes/web.php:70-210`, `permission:dashboard.view`, view `pages/index.blade.php`.
* Shows lease + mining KPIs (active, pending, approved, archived = soft-deleted), DGPS / drone counts, environment / EC compliance totals, 5 recent leases, 3+3 recent surveys, 6 activity logs, top 4 districts, and a financial snapshot from `application_payments` (paid, pending, recovery %).
* **Impact areas:** Any change to status names breaks these counters, because the literals `under_scrutiny`, `scrutiny`, `completed` and `deliverables_ready` are hard-coded here.

## M3. Administration: Users, Roles, Branches — VERIFIED
* **Controllers:** `UserController`, `RolesController`, `BranchController`. **Views:** `pages/authentication/{users,roles,branch}/*`. **JS:** `public/js/ajax/{user,role,branch}.js`.
* **Data:** `users`, `roles` (+`created_by`), `permissions`, `model_has_roles`, `role_has_permissions`, `branches`.
* **Rules:** Non-admin users only see and assign roles they created, whose permissions are a subset of their own. They only assign users to their own branch. Nobody can change their own role. The last Admin cannot be deleted. A user with statutory records cannot be deleted (they can be deactivated). Branch delete is blocked for the last branch, for the deleter's own branch, and for the branch holding the only Admin. Otherwise branch delete **hard-deletes every user in the branch** (`BranchController@destroy`).
* **Codes:** `user_code` is auto-generated as `LUK_###` when blank.
* **Risk:** Plaintext password stored in `users.show_password` (see [18-security-audit.md](18-security-audit.md) S2).

## M4. Master data (legacy inventory): Category, Product, Product Stock, Unit — VERIFIED
* `CategoryController` (CRUD via AJAX), `ProductController` (index + store), `ProductStockController@index`, `UnitController@index`. Tables: `categories`, `products`, `product_stocks`, `units`.
* **Observation:** This is an inventory / POS-style module (barcode `PRO_###`, MRP, GST, three discount columns). It is unrelated to the mining domain and probably left over from the template project (INFERRED). `ProductController@store` writes the computed discounted price into `mrp` and the computed amount into `cast_per`. `ProductStock.total_stock` is computed from an unsaved model. `public/js/ajax/unit.js` is empty, and there is no unit create endpoint even though `createunit.blade.php` exists.

## M5. Customer Directory — VERIFIED
* **What:** The master record of quarry operators (individual / company). **Controller:** `CustomerDirectoryController` (`index`, `store`, `show` by slug, `update`, `lookupByMimas`, `destroy`). **Views:** `pages/customers.blade.php`, `pages/customer_show.blade.php`. **JS:** `public/js/ajax/customer.js`.
* **Data:** `customers` (MIMAS no. unique among non-deleted rows, Aadhaar unique, PAN, GSTIN, area string, portal passwords, `slug` auto-generated in `Customer::booted`).
* **Rules:** `mimas_no` is required and unique, Aadhaar must match `^\d{4}[ -]?\d{4}[ -]?\d{4}$`, the secondary mobile must differ from the primary. Delete is a soft delete, blocked if the customer has leases, mining, environment or PPT records (`destroy`).
* **Dependents:** Every module (FK `customer_id`, `restrictOnDelete`). The lease wizard and environment creation **also create customers implicitly** (match by id → MIMAS → Aadhaar, or by name + phone) and **restore soft-deleted customers** (`CustomerController@saveStep1`).

## M6. Customer Tracking (360° dossier) & Invoices — PARTIALLY VERIFIED
* `CustomerTrackingController` (2,109 lines): `index` (KPIs, filters), `search` (universal query by MIMAS / name / phone / application no.), `show`, `proformaInvoice`, `taxInvoice`, helpers `buildCustomerDossier`, `getCustomerApplicationsCatalog`, `buildInvoiceData`, `numberToWords`.
* Read-only aggregation across all module tables. The invoices are rendered HTML (`pages/customer_tracking/{proforma_invoice,tax_invoice}.blade.php`) and are **not persisted** — there is no invoice table or invoice number sequence (INFERRED from schema: no invoice table exists).
* **Note:** When a customer has no application with a real value, `buildInvoiceData` emits a default "Preparation of Mining Plan" line of ₹1,50,000 with SAC 998343 (`CustomerTrackingController.php:~1890-1930`).

## M7. Lease Application (Quarry Lease) — VERIFIED (key paths)
* **What:** An 8-step wizard that captures a quarry lease application, its MIMAS portal credentials, rule category, documents (19 standard items in 3 folders plus custom items), payment and handlers. It is then validated, approved, and promoted to Mining.
* **Controller:** `CustomerController` (despite the name). **Views:** `pages/lease_application/createstep1..8`, `customer.blade.php` (list), `viewapplication.blade.php` (dossier), `report_pdf.blade.php`.
* **Steps:** step1 customer + minerals (creates DB draft `LA-DRAFT-YYYY-NNNN`) → step2 contact + MIMAS credentials (`mimas_credentials`, password `encrypted` cast) → step3 category / rule (may create a custom `lease_categories` row) → step4 summary → step5 documents (direct or chunked upload; `step5.requirement` toggles "required") → step6 payment / MIMAS → step7 handlers → step8 preview → `POST /application/submit`.
* **Submit:** One transaction renumbers the draft to `LA-YYYY-NNNN`, sets status `under_scrutiny`, upserts `mimas_credentials`, `application_payments` and `application_handlers`, copies draft files into `public/uploads/lease_applications/<app_no>/`, upserts 19 `lease_documents` rows, and logs activity.
* **Review:** `POST /application/{id}/validate` (pass → `validated`, all uploaded docs → `validated`; fail → `revision_required`), `/approve` (requires `validated` or `under_scrutiny` → `approved`, `current_step 9`), `/reject` (→ `revision_required`, step 5), `/document/{id}/status`, `/document/{id}/upload`, `/report` (HTML).
* **Promotion:** `POST /application/{id}/move-to-mining` → requires `approved` → creates `mining_applications` `MP-<common suffix>`, `stage 6.1`, `status draft`; clones validated documents.
* **Tables:** `lease_applications`, `lease_application_minerals`, `lease_categories`, `lease_documents`, `mimas_credentials`, `lease_survey_numbers` (read only), `application_handlers`, `application_payments`, `activity_logs`.
* **Limits:** Wizard state lives in the session — one draft per browser session. Branch is forced to `Branch::first()`.

## M8. Mining Plan — VERIFIED (key paths)
* **Controller:** `MiningController`. **Views:** `pages/mining-portal/{index,newapplication,projectfolder,document,process}`.
* **Create:** a 9-step client-side wizard (`newapplication`, 7,229-line view) with `POST /newapplication/autosave` (draft row, `current_step`) and `POST /newapplication` (`store`). Mining numbering `MP-YYYY-NNNN` (also looks at the legacy `MDG-`), with common ID `GTMS-YYYY-NNNN` from the same sequence (`generateMiningAppNumber`). A created plan starts at `stage 6.2`, `status scrutiny`.
* **Nature of work** (`nature_of_works`, e.g. Fresh / Mining Plan / Others) drives the required document fields (`document_fields.nature_of_work_id`).
* **Stage control:** `POST /mining/application/{id}/stage` maps 6.1→draft, 6.2→scrutiny, 6.3→approved, 6.4→approved, 6.5→archived. Input is not validated.
* **Documents:** upload / remove (6 duplicate route aliases), `validate` (validated / revision_required).
* **Promotion:** `POST /mining/application/{id}/move-to-environment` creates `environment_projects` `ENV-<B1|B2>-YYYY-NNNN` (default B2), clones documents and creates slots. **No status gate** (INFERRED from code: no `approved` check in `moveToEnvironment`).
* **Tables:** `mining_applications`, `mining_application_minerals`, `mining_documents`, `nature_of_works`, `plan_types`, `applicant_types`, `application_*`. Unused: `mining_boundary_points`, `mining_production_schedules`.

## M9. Environmental Clearance (B1 / B2) — VERIFIED (key paths)
* **Controllers:** `EnverionsoneController` (unified, current) and `EnvironmentalB2Controller` (legacy B2 routes kept for backward compatibility). **Views:** `pages/eviron/{index,create,show}`, legacy `pages/enviro_b2/*`, unreferenced `pages/enviro_b1/*`.
* **Create:** `POST /eviron` → category B1 (sub-category TOR / ETA, legacy SC1 / SC2) or B2. Code `ENV-<cat>-YYYY-NNNN`. May create a customer. Stores the PARIVESH / e-mail passwords on the project and copies them to the customer. Generates document slots from folders of module id 1 (`generateSlots`).
* **B1 lifecycle:** `b1_stage` `sc1_prep` → `submit-sc1-ppt` (creates PPT `tor_presentation`, `b1_stage sc1_ppt_review`, status `validation`) → PPT approval → `sub_category ETA`, `b1_stage sc2_prep`, status `draft` → `submit-sc2-ppt` (PPT `final_ec_presentation`, `sc2_ppt_review`) → PPT approval → `b1_stage completed`, status `approved`.
* **B2 lifecycle:** `submit-b2-ppt` (PPT `b2_presentation`, status `validation`) → PPT approval → `approved`.
* **Other actions:** `POST /eviron/{id}/status` (any string ≤100; `approved` auto-validates uploaded docs), document upload / add / remove / review (`approved` / `revision_required`), download through the `public` disk.
* **Auto-created PPT records** get a hard-coded fee of ₹25,000 marked `paid` (`EnverionsoneController.php:778-783` and equivalents).

## M10. PPT Department (SEAC / SEIAA presentations) — VERIFIED (key paths)
* `PptDepartmentController`: index, show, a 9-step wizard (`step/{1..9}` with session `ppt_wizard`), `store` (`PPT-YYYY-NNNN`, status `agenda_scheduled`), upload / remove, `updateStatus` (free string), `approvePresentation`.
* **Approval side effects** on the linked environment project are described in M9. `ppt_agendas` (committee, meeting no., outcome) exists but is unused.

## M11. EC Certificate — VERIFIED (key paths)
* `EcCertificateController`: index, show, a stepped wizard (`getMaxUnlockedStep` gates steps; session `ec_wizard`), `store` (`updateOrCreate` by `ec_ref_no`, status `active`, **sets the environment project to `approved`**), upload / remove, `updateStatus` (free string).
* Fills `parivesh_app_no` with a generated placeholder `SIA/TN/MIN/<10000+project id>/<year>` when blank.
* The model is **not** branch-scoped.

## M12. EC Compliance (half-yearly returns) — VERIFIED (key paths)
* `EcComplianceController`: an 8-step wizard (`ec_compliance_wizard`), `store` (`HYC-YYYY-NNNN` via `count()+1`, status `uploaded_to_parivesh`), document folders `documents` / `site_analysis` / `report` / `parivesh_upload`, `updateStatus` (free string).
* Placeholder defaults on create: random PARIVESH app / acknowledgement numbers and a default NABL certificate number.

## M13. DGPS Survey — VERIFIED (key paths)
* `DgpsSurveyController`: a 6-step wizard (`dgps_wizard`), `store` (`DGPS-YYYY-NNNN` via `count()+1`), upload / remove.
* **On create it is immediately `survey_status completed`, `report_status verified`**, with a random field book number and a fixed instrument serial (`DgpsSurveyController@store`). `dgps_points` exists but is unused.

## M14. Drone Survey — VERIFIED (key paths)
* `DroneSurveyController`: a 6-step wizard (`drone_wizard`), `store` (`DRN-YYYY-NNNN`, default `survey_status deliverables_ready`, placeholder pilot RPC / UIN when blank), upload / remove.

## M15. Accounts — VERIFIED (key paths)
* **Quotations** (`QuotationController`): CRUD, print, numbering `GTMS/QTN/YYYY/NNNN`, items, scope sections + milestones (JSON), bank account, customer concessions lookup. Status `draft|sent|accepted|rejected|converted` (set manually). Tax rate defaults to 18%.
* **Payment collection** (`PaymentCollectionController@store`): locks the target application row, adds `amount_paid` to `paid_amount`, recomputes `pending_amount` / `payment_status`, upserts `application_payments`, creates `payment_receipts` `GTMS/REC/YYYY/NNNN`. Overpayment is not blocked.
* **Receipts** (`PaymentReceiptController`): list, show, print, application history JSON.
* **Ledger** (`CustomerLedgerController`): per-customer statement and print.
* **Reports** (`FinancialReportController`): filtered analytics and a CSV export (streamed).
* **Invoice seal** (`InvoiceSealController`): a global seal image in `public/uploads/invoice_seals/custom_seal.*`. The upload / remove / status routes have **no permission check**.
* **Bank accounts:** `GET/POST /accounts/bank-accounts`.

## M16. Master Pipeline — VERIFIED
* `MasterPipelineController` + `MasterPipelineService`. Views `pages/master_pipeline/{index,timeline}`.
* `master_projects` (ID `GTMS-YYYY-NNNN`, `current_stage`, `pipeline_status`, FKs to each department record) and `pipeline_movements` (an append-only log of Initiation, Forward_Transfer, Returned_For_Correction, Resubmitted_Fix).
* Stage ranks: lease 1, surveys 2, mining 3, environment 4, ppt 5, completed 6. Forward must not go to a lower rank. A return requires a reason ≥5 characters and a priority, writes `returned_for_correction` into both department records' status, and notifies users.
* **No permission middleware** on any `master-pipeline/*` route. `create-from-lease` also accepts **GET**.

## M17. Notifications — VERIFIED
* `NotificationController` over the Laravel `notifications` table, accessed through `App\Models\Notification` (`data` JSON: title, message, category, priority, action_url, icon).
* Real producer: `MasterPipelineService::dispatchReturnNotification`.
* **Demo producer:** `index` auto-seeds 6 sample notifications for any user with none. `seed-demo` deletes and re-seeds them.

## M18. Chunked Upload Engine — VERIFIED
* `ChunkUploadController` + `ChunkedUploadService` + jobs. Supports 10 MB chunks, up to 50 GB, SHA-256 per chunk and per file, resume via status, assembly by streaming into `public/uploads/<module>/REF-<id>/`.
* Used by the module upload endpoints through an `upload_token`. See [16-file-upload-storage-system.md](16-file-upload-storage-system.md).

## Dormant / unused schema (no controller or view uses the model) — VERIFIED by grep
`environmental_projects`, `environmental_documents`, `environmental_activities` (August 2026 B2 prototype), `project_flows`, `mineral_stockpiles`, `mineral_stock_entries`, `mineral_dispatches`, `archived_activity_logs`, `mining_boundary_points`, `mining_production_schedules`, `dgps_points`, `ppt_agendas`. Treat them as reserved. Confirm with the client before building on them or dropping them.
