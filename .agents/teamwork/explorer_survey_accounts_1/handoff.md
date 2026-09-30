# Handoff Report — explorer_survey_accounts_1

**To:** orchestrator_3 (`ecb0a4ee-1d25-4637-a1fb-552edc53b301`)  
**From:** explorer_survey_accounts_1  
**Working Directory:** `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1`  
**Date:** 2026-09-29T05:54:00Z  
**Type:** Hard Handoff (Investigation & Survey Complete)  

---

## 1. Observation

1. **53 Database Migrations Executed:**
   Direct execution of `php artisan migrate:status` confirmed 53 migrations have successfully run against database `gtms_data`. Key payment migrations include:
   - `2026_09_22_000002_add_payment_fields_to_applications_tables.php` (created `application_payments` table lines 50–64; added payment columns to `lease_applications` and `mining_applications`).
   - `2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php` (added `product_value`, `paid_amount`, `pending_amount`, `payment_status` to `environment_projects`, `ec_certificates`, `ppt_applications`, `dgps_surveys`, `drone_surveys`).
   - `2026_09_23_000002_create_ec_compliances_tables.php` (created `ec_compliances` table with matching payment columns).

2. **Schema & Models of 7 Statutory Tables + EC Compliance:**
   - `lease_applications` (`app/Models/LeaseApplication.php`): columns `product_value`, `paid_amount`, `pending_amount` (`DECIMAL(12,2)` default `0.00`), `payment_status` (`ENUM('pending','partial','paid')`).
   - `mining_applications` (`app/Models/MiningApplication.php`): same 4 payment columns.
   - `environment_projects` (`app/Models/EnvironmentProject.php`): same 4 payment columns; has `payments(): HasMany` relation to `ApplicationPayment`.
   - `ppt_applications` (`app/Models/PptApplication.php`): same 4 payment columns; has `payments(): HasMany` relation.
   - `dgps_surveys` (`app/Models/DgpsSurvey.php`): same 4 payment columns; has `payments(): HasMany` relation.
   - `drone_surveys` (`app/Models/DroneSurvey.php`): same 4 payment columns; has `payments(): HasMany` relation.
   - `ec_certificates` (`app/Models/EcCertificate.php`): same 4 payment columns; has `payments(): HasMany` relation.
   - `ec_compliances` (`app/Models/EcCompliance.php`): same 4 payment columns + `payment_notes` (`TEXT`).

3. **`application_payments` Polymorphic Structure:**
   - Table created in `2026_09_22_000002_add_payment_fields_to_applications_tables.php` lines 50–64.
   - Model `app/Models/ApplicationPayment.php` lines 1–41.
   - Current schema contains: `id`, `application_type` (`VARCHAR(50)` indexed), `application_id` (`BIGINT UNSIGNED` indexed), `payable_type` (`VARCHAR(255)`), `payable_id` (`BIGINT UNSIGNED`), `product_value`, `paid_amount`, `pending_amount`, `payment_status`, `notes`.
   - Current controller persistence: In `MiningController.php` (lines 359–372) and `CustomerController.php` (lines 771–784), `ApplicationPayment` is maintained as a 1-to-1 aggregate summary via `updateOrCreate()`. It does NOT store individual payment receipts, dates, bank reference numbers, or transaction histories.

4. **Non-Existence of `customer_quarry_concessions` Table:**
   - Codebase grep for `customer_quarry_concessions` returned 0 results.
   - Database table audit confirmed GTMS has 64 tables.
   - In `CustomerTrackingController.php` (lines 660–730 and 995–1040), quarry concessions are resolved dynamically from `Customer` relationships (`$customer->leaseApplications`), where each `LeaseApplication` carries the concession attributes: `village`, `taluk`, `district_id`, `area_extent_ha`, `surveyNumbers` (`lease_survey_numbers`), and `minerals` (`lease_application_minerals`).

5. **Existing Invoice Precedents & SAC Codes:**
   - In `CustomerTrackingController.php` (lines 1450–1600), methods `proformaInvoice()` and `taxInvoice()` already define domain SAC codes: `998341` (Lease & DGPS), `998343` (Mining Plan), `998349` (EC & Compliance), `998311` (PPT), `998342` (Drone Photogrammetry) and standard 18% GST (CGST 9% + SGST 9%).

6. **RBAC & Sidebar Structure:**
   - `routes/web.php` lines 108–150 enforce Spatie permissions via `->middleware('permission:<name>')`.
   - `resources/views/layouts/sidebar.blade.php` organizes menu items with `@can` and `@canany` directives.

---

## 2. Logic Chain

1. **Premise:** The Accounts Module requires: (a) Quotations, (b) Centralized payment collection with atomic synchronization across statutory application tables and `application_payments`, (c) Money receipt voucher generation with unique sequential numbers, (d) Customer ledger statements, (e) Reports & export, and (f) Spatie RBAC.
2. **From Observation 3:** `application_payments` acts strictly as an application-level summary balance. Recording individual money receipts with bank reference numbers, payment modes, and chronological installment history directly in `application_payments` would either destroy the 1-to-1 aggregate structure relied on by existing application wizards or fail to store vital receipt attributes.
3. **Therefore:** A dedicated `payment_receipts` table must be introduced. When a payment receipt is recorded, an atomic database transaction (`DB::transaction`) updates the statutory application table (`paid_amount`, `pending_amount`, `payment_status`), synchronizes `application_payments`, and inserts a new immutable `payment_receipts` voucher.
4. **From Observation 4:** `customer_quarry_concessions` does not exist in the database. Concessions are currently derived from `lease_applications`. However, quotations are frequently issued *before* a lease application is officially registered (prospect/quote stage).
5. **Therefore:** Quotations must store concession metadata fields directly (`quarry_name`, `district_id`, `taluk`, `village`, `survey_numbers`, `area_extent_ha`, `mineral_name`) alongside optional foreign keys `lease_application_id` and `mining_application_id`. When creating a quotation for an existing client, an AJAX endpoint will populate these fields from their existing `leaseApplications`.
6. **From Observation 1 & 6:** To preserve zero-regression standards across the 13 feature test suites, new migrations must use `restrictOnDelete` on `customers` and `cascadeOnDelete` on `quotation_items`, with all 4 Spatie permissions (`account.view`, `account.create`, `account.edit`, `account.delete`) seeded.

---

## 3. Caveats

1. **Pre-existing Legacy Prototype Tables:** Tables `environmental_projects`, `environmental_documents`, and `environmental_activities` from migration `2026_08_07_000001` are legacy prototypes superseded by `environment_projects`. The Accounts Module must strictly link to `environment_projects`.
2. **`ec_compliances` Table Inclusion:** While the dispatch prompt specifically named 7 statutory tables, `ec_compliances` is an 8th active statutory table in GTMS carrying identical commercial payment columns (`product_value`, `paid_amount`, `pending_amount`, `payment_status`). We have fully cataloged it in `survey_report.md` so the payment resolver and customer ledger can optionally resolve EC compliance dues.
3. **Stand-alone Concession Master Table:** While we have provided the full DDL specification for an optional `customer_quarry_concessions` table, introducing it is optional since existing GTMS features rely on `lease_applications` as the canonical concession aggregate.

---

## 4. Conclusion

The GTMS database is structurally mature and well-indexed, making the Accounts Module integration straightforward and non-destructive.  
- 3 new database tables are specified and ready for implementation:
  1. `quotations` (Quotation header with customer/concession snapshot, GST calculation, terms, status)
  2. `quotation_items` (Dynamic multi-service line items with SAC codes, units, rates)
  3. `payment_receipts` (Official money receipt vouchers with payment modes, bank references, sequential numbering `GTMS/REC/YYYY/XXXX`, and remaining balance tracking)
- Atomic synchronization between `payment_receipts`, the 7 statutory application tables, and `application_payments` is fully mapped with exact mathematical formulas and pessimistic locking (`lockForUpdate()`).
- All findings, DDL specifications, and domain catalogs have been documented in:  
  `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\survey_report.md`.

---

## 5. Verification Method

To independently verify the facts and citations reported:
1. Verify migrations status:  
   `php artisan migrate:status`
2. Verify payment columns on statutory tables:  
   `php artisan tinker --execute="echo json_encode(array_keys(Schema::getColumnListing('lease_applications')));"`
3. Verify `application_payments` schema:  
   `php artisan tinker --execute="echo json_encode(Schema::getColumnListing('application_payments'));"`
4. Verify non-existence of `customer_quarry_concessions`:  
   `php artisan tinker --execute="var_dump(Schema::hasTable('customer_quarry_concessions'));"` (evaluates to `false`).
5. Inspect `app/Http/Controllers/CustomerTrackingController.php` lines 1450–1600 to confirm SAC codes and GST calculation algorithms.
6. Verify existing test suites continue to pass:  
   `php artisan test --filter=ApplicationHandlersAndPaymentsTest`
