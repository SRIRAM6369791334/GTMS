# 12 — Business Logic (Rules Catalogue)

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Workflows: [13-business-workflows.md](13-business-workflows.md).
> Every rule cites the code that enforces it. Nothing here is inferred from names alone. A rule tagged **(code-only)** has no DB constraint behind it. **Rules not found in code are not invented.** Where a business rule would be expected but does not exist, it is listed under "Absent rules".

## R-ID — Numbering schemes (VERIFIED)
| Entity | Format | Generator | Concurrency |
|---|---|---|---|
| Lease draft | `LA-DRAFT-YYYY-NNNN` | `CustomerController::generateDraftAppNumber` | `lockForUpdate` on the max row; **subject to BranchScope** |
| Lease official | `LA-YYYY-NNNN` | `generateOfficialAppNumber` (excludes drafts) | same |
| Mining | `MP-YYYY-NNNN` (also scans legacy `MDG-`) + common ID `GTMS-YYYY-NNNN` | `MiningController::generateMiningAppNumber` | lock |
| Mining from lease | `MP-<lease common suffix>` (`-A` suffix if taken) | `CustomerController@moveToMining` | exists check |
| Environment | `ENV-B1|B2-YYYY-NNNN` | `EnverionsoneController@store`, `MiningController@moveToEnvironment` | max + exists loop |
| PPT | `PPT-YYYY-NNNN` | `PptDepartmentController@store` (`withoutGlobalScopes()->count()+1` + exists loop); `EnverionsoneController@submit*` (`PptApplication::count()+1`, **no exists loop, branch-scoped**) | weak |
| EC compliance | `HYC-YYYY-NNNN` | `count()+1` | weak |
| DGPS | `DGPS-YYYY-NNNN` | `count()+1` (no exists loop) | weak |
| Drone | `DRN-YYYY-NNNN` | `count()+1` + exists loop | weak |
| EC certificate | user-entered `ec_ref_no` (unique) | `EcCertificateController@store` (`updateOrCreate`) | — |
| Master project | `GTMS-YYYY-NNNN` | `MasterPipelineService::generateMasterId` (withoutGlobalScopes, withTrashed, lock) | strong |
| Quotation | `GTMS/QTN/YYYY/NNNN` | `QuotationController::generateQuotationNumber` | withoutGlobalScopes |
| Receipt | `GTMS/REC/YYYY/NNNN` | `PaymentReceipt::generateReceiptNumber` | withoutGlobalScopes |
| User code | `LUK_###` when blank | `UserController@store` | — |
| Product barcode | `PRO_###` | `ProductController@store` | — |

**Rule conflict:** `GTMS-YYYY-NNNN` is produced by three independent generators (lease-derived, mining sequence, master sequence) plus the back-fill migration `2026_09_16_095500`. See [21](21-known-issues-risks.md) K6.

## R-CUST — Customer identity (VERIFIED)
1. `mimas_no` is required and unique among active customers (`CustomerDirectoryController@store`).
2. Aadhaar: 12 digits, optional separators, unique among active customers. PAN is optional in the directory but **required in lease step 1** (`saveStep1`: `pan` required, `aadhaar_no` required).
3. Secondary mobile ≠ primary mobile (both forms).
4. Implicit creation / reuse: the lease wizard matches an existing customer by `customer_id` → `mimas_no` → `aadhaar_no` (including soft-deleted rows, which are then **restored**). It only fills empty secondary-contact and area fields on the existing customer (`CustomerController@saveStep1`, `@submit`). Environment creation matches by `customer_id` or `customer_name` (+ phone) and otherwise creates a customer (`EnverionsoneController@store`).
5. Soft delete is blocked when the customer has lease, mining, environment or PPT records (`CustomerDirectoryController@destroy`).

## R-LEASE — Lease application (VERIFIED)
1. Status path: `draft` → (`submit`) `under_scrutiny` → (`validate pass`) `validated` | (`validate fail` / `reject`) `revision_required` → (`approve`) `approved`.
2. `approve` is allowed from `validated` **or** `under_scrutiny` (`approveApplication`). The UI message says "must be validated", but the code does not require it.
3. Validation pass marks every lease document that has a file as `validated`, with `reviewed_by` and `reviewed_at` (`validateApplication`).
4. Reject requires a reason and sets `current_step = 5` (back to documents).
5. Move to Mining requires status `approved` and is idempotent (an existing linked mining application blocks it).
6. Area may be decimal hectares or `H.A.SqM` (e.g. `1.90.57`). `parseAreaExtentToFloat` converts `a.b.c` → `a + b/100 + c/10000` ha.
7. Mineral: multi-select. The first mineral becomes `mineral_id`; if none is selected, `mineral_id` defaults to **1** (`saveStep1`). "Other" mineral is a free-text name.
8. Category: if "Other", a custom `lease_categories` row is created (`is_custom = true`, land_type `Both`) (`saveStep3`).
9. MIMAS password: blank or `__UNCHANGED__` keeps the previous value, otherwise falls back to a hard-coded default placeholder (`saveStep2`). This is a business-data hazard: a placeholder may be stored as the customer's portal password.

## R-MINING — Mining plan (VERIFIED)
1. `store` creates the plan at `stage 6.2`, `status scrutiny`, `current_step 9`.
2. Stage → status map (`advanceStage`): 6.1 draft, 6.2 scrutiny, 6.3 approved, 6.4 approved, 6.5 archived. 6.6 exists in the enum but is unmapped (status unchanged).
3. Nature of work "Mining Plan" makes mineral and plan type relevant. "Others" requires `other_nature_of_work_name` (`store` validation).
4. Document requirements come from `document_fields` filtered by `nature_of_work_id` (or NULL = common).
5. Move to Environment creates the environment project (default B2) and clones documents. **No status precondition.**

## R-ENV — Environmental clearance (VERIFIED)
1. Category B1 requires a sub-category. Legacy SC1 / SC2 are mapped to TOR / ETA (`store`; migration `2026_09_25_000001`).
2. B1 two-gate flow: TOR → PPT `tor_presentation` → on approval `sub_category = ETA`, `b1_stage = sc2_prep`, status `draft` → PPT `final_ec_presentation` → on approval `b1_stage = completed`, status `approved`. `submitSc2ToPpt` requires `sub_category` ∈ {ETA, SC2}.
3. B2 single gate: PPT `b2_presentation` → approval → status `approved`, `b1_stage = completed`.
4. Setting a project's status to `approved` (any casing) upgrades its `uploaded` documents to `validated` (`updateStatus`).
5. Auto-created PPT applications are given `product_value = paid_amount = 25000.00`, `payment_status = paid` (hard-coded in `submitSc1ToPpt`, `submitSc2ToPpt`, `submitB2ToPpt`).
6. Legacy B2 controller rule: `validation` requires at least one uploaded / validated / approved document; `approved` requires at least one approved document (`EnvironmentalB2Controller@updateStatus`). **The unified controller does not enforce this.**
7. Issuing an EC certificate sets the environment project to `approved` (`EcCertificateController@store`).

## R-PPT (VERIFIED)
Approval (`approvePresentation`, or `updateStatus` with `approved`) sets the PPT status to `approved` and applies the R-ENV side effects according to `presentation_stage`.

## R-SURVEY (VERIFIED)
DGPS `store` hard-codes `survey_status = completed`, `report_status = verified`, a random `field_book_no` (`FB-TN-###`) and a fixed `instrument_serial_no`. Drone `store` defaults to `survey_status = deliverables_ready` and placeholder RPC / UIN numbers when blank. These are defaults, not business rules. They mean "created" is indistinguishable from "completed".

## R-PAY — Money (VERIFIED)
1. Each application row carries `product_value`, `paid_amount`, `pending_amount`, `payment_status`.
2. Wizards derive the status: paid ≤ 0 → `pending`; pending ≤ 0 → `paid`; else `partial` (e.g. `EnverionsoneController@store`). DGPS / EC compliance / PPT use `pending == 0 → paid`, `paid > 0 → partial`, else `pending`, and accept a client-supplied `payment_status` override.
3. Payment collection (`PaymentCollectionController@store`): `newPaid = previous + amount`. If `product_value > 0`, then `pending = max(0, product_value - newPaid)`; otherwise `product_value` is set to `newPaid`. Status follows. A receipt records `previous_paid`, `amount_paid` and `balance_due`. **Overpayment is accepted silently.** The receipt's `customer_id` is not checked against the application's customer.
4. `application_payments` is upserted to mirror the application row. Dashboard revenue reads `application_payments`. Ledgers and reports read receipts and application rows (PARTIALLY VERIFIED).
5. Quotation totals: `Quotation::calculateTotals()`, tax rate default 18%, items `quantity × unit_rate`. Quotation status is manual. No code converts a quotation into an application or receipt automatically (`converted` is a label only — INFERRED from the absence of any writer setting it).

## R-PIPE — Master pipeline (VERIFIED)
1. Stage rank order: lease 1 < survey(dgps / drone) 2 < mining 3 < environment 4 < ppt 5 < completed 6. Forward is allowed when `toRank ≥ fromRank`. **Unknown department names are always allowed.**
2. Forward always sets `pipeline_status = in_progress`, even when forwarding to `completed`. Nothing in the code sets `pipeline_status = completed` (grep).
3. A return requires a reason (non-empty; the controller requires ≥5 characters) and a priority ∈ {urgent, high, normal}. It sets the master to `returned_for_correction`, writes `status = returned_for_correction` (or `survey_status`) plus a note into **both** the from- and to-department records, and notifies the target record's creator and the master's creator (else the branch users, else the acting officer).
4. Resubmit sets the master and both records back to `in_progress`.
5. The movement log is append-only. No update or delete path exists.

## R-AUTH — Access (VERIFIED) — see [14](14-user-roles-permissions.md)

## Absent rules (expected but not found in code)
* No lease expiry / renewal processing, even though `end_date` is indexed "for 90/60/30 day renewal expiry alerts" (migration comment). The same applies to the EC certificate `expiry_date`. No scheduler exists.
* No approval segregation (the same `application.edit` permission validates and approves).
* No invoice persistence or GST invoice numbering.
* No refund, void or cancellation of receipts.
* No audit of field-level changes (`activity_logs.old_values` / `new_values` are never written — INFERRED from grep of the `ActivityLog::create` calls, which pass only action / description).
