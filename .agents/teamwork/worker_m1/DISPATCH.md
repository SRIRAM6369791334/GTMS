# Dispatch — worker_m1

## Milestone 1: Database Foundation, Eloquent Models, Seeders & RBAC

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Project Architecture & Specs: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md
- Survey Findings:
  * c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\survey_report.md
  * c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\survey_report.md

* Expected Output:
1. Database Migrations:
   - `database/migrations/2026_09_29_000001_create_accounts_quotations_table.php`:
     * Table `quotations`: `id`, `quotation_number` (unique string, e.g. GTMS/QTN/2026/001), `customer_id` (foreign key to `customers`, restrict on delete), `lease_application_id` (nullable foreign key to `lease_applications`, null on delete), snapshot client fields (`customer_name`, `company_name`, `phone`, `email`, `gst_number`, `address`), snapshot quarry concession fields (`quarry_name`, `district_id` nullable fk, `taluk`, `village`, `survey_numbers`, `area_extent_ha`, `mineral_name`), financial fields (`subtotal` decimal 12,2 default 0, `tax_rate` decimal 5,2 default 18.00, `tax_amount` decimal 12,2 default 0, `total_amount` decimal 12,2 default 0), terms (`validity_days` int default 30, `payment_terms` text nullable, `exclusions` text nullable, `notes` text nullable), `status` (enum: `draft`, `sent`, `accepted`, `rejected`, `converted` default `draft`), `created_by` (nullable fk to `users`), timestamps, softDeletes (optional).
     * Table `quotation_items`: `id`, `quotation_id` (foreign key to `quotations`, cascade on delete), `service_name` (string), `sac_code` (string nullable), `description` (text nullable), `quantity` (decimal 10,2 default 1), `unit` (string default 'Nos'), `unit_rate` (decimal 12,2 default 0), `subtotal` (decimal 12,2 default 0), timestamps.
   - `database/migrations/2026_09_29_000002_create_accounts_payment_receipts_table.php`:
     * Table `payment_receipts`: `id`, `receipt_number` (unique string, e.g. GTMS/REC/2026/001), `customer_id` (foreign key to `customers`, restrict on delete), `application_type` (string: `lease`, `mining`, `environment`, `ppt`, `dgps`, `drone`, `ec_certificate`, `ec_compliance`, `general`), `application_id` (bigint unsigned nullable), `amount_paid` (decimal 12,2 default 0), `balance_due` (decimal 12,2 default 0), `payment_mode` (enum: `Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`, `Other`), `bank_name` (string nullable), `reference_number` (string nullable), `transaction_date` (date), `notes` (text nullable), `created_by` (nullable fk to `users`), timestamps.
2. Eloquent Models:
   - `app/Models/Quotation.php`: fillables, casts (`subtotal`, `tax_rate`, `tax_amount`, `total_amount` to float/decimal), relationships (`customer(): BelongsTo`, `leaseApplication(): BelongsTo`, `district(): BelongsTo`, `items(): HasMany`, `creator(): BelongsTo`).
   - `app/Models/QuotationItem.php`: fillables, casts (`quantity`, `unit_rate`, `subtotal` to float/decimal), relationship (`quotation(): BelongsTo`).
   - `app/Models/PaymentReceipt.php`: fillables, casts (`amount_paid`, `balance_due` to float/decimal, `transaction_date` to date), relationships (`customer(): BelongsTo`, `creator(): BelongsTo`, polymorphic or dynamic accessor for `application`).
   - In `app/Models/Customer.php`, add `quotations(): HasMany` and `paymentReceipts(): HasMany`.
3. RBAC Seeding & Controller Mapping:
   - In `database/seeders/RolePermissionSeeder.php`, register permissions:
     * `account.view`
     * `account.create`
     * `account.edit`
     * `account.delete`
     Assign these permissions to `Admin` (all 4) and `Officer` / relevant staff roles.
   - In `app/Http/Controllers/RolesController.php`, add `'account' => 'Accounts & Financials'` to the permission group mapping array.
4. Verification & Clean Execution:
   - Execute `php artisan migrate` to verify both migrations run cleanly.
   - Execute `php artisan db:seed --class=RolePermissionSeeder` to verify permission seeding.
   - Test rollback via `php artisan migrate:rollback --step=2` and re-migrate `php artisan migrate` to ensure 100% clean rollback without foreign key constraint errors.

* Constraints:
- Exclusive write ownership of the files listed above. Do not touch unrelated files.
- All code must follow GTMS conventions and Laravel 12 syntax.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m1\handoff.md`.
- Include command outputs and verification proofs in your handoff.

* Validation Criteria:
- `php artisan migrate` succeeds.
- Rollback and re-migration succeed.
- Models instantiate cleanly with relations.
- Spatie permissions `account.*` exist in database.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A forensic auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## 2026-09-29T05:54:24Z
You are worker_m1.
Your working directory is: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m1
Your detailed dispatch assignment is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m1\DISPATCH.md
Authoritative User Request is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
Master Project Specification is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md

