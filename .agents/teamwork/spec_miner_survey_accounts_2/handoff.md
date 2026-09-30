# Handoff Report — spec_miner_survey_accounts_2

## 1. Observation
- **Routes & Middleware**: Inspected `routes/web.php` (lines 1–351). All authenticated routes reside within `Route::middleware('auth')->group(...)`. Controller methods are gated using Spatie permissions via `Route::middleware('permission:<module>.<action>')` or inline `->middleware('permission:...')`.
- **Payment Handling**: Inspected existing statutory controllers:
  - `app/Http/Controllers/CustomerController.php` (lines 762–785 & 1212–1233): Updates `lease_applications` table (`product_value`, `paid_amount`, `pending_amount`, `payment_status`) and synchronizes with `ApplicationPayment::updateOrCreate(['application_type' => 'lease', 'application_id' => ...], [...])`.
  - `app/Http/Controllers/MiningController.php` (lines 350–372): Updates `mining_applications` table and syncs `ApplicationPayment::updateOrCreate(['application_type' => 'mining', ...])`.
  - `app/Http/Controllers/PptDepartmentController.php` (lines 191–246), `DgpsSurveyController.php` (lines 235–246), `DroneSurveyController.php` (lines 205–218): Utilize `DB::beginTransaction()`, persist statutory models and `ApplicationPayment::create([...])`, followed by `DB::commit()` and `catch (\Exception $e) { DB::rollBack(); }`.
- **Spatie RBAC Setup**:
  - `database/seeders/RolePermissionSeeder.php` (lines 20–280): Configures Spatie permissions with `<module>.<action>` format (e.g. `customer.view`, `mining.create`, `branch.delete`). Guard is `'web'`.
  - Roles defined: `Admin` (receives all permissions via `syncPermissions(Permission::all())`), `Staff` (view permissions), and `Officer` (view, create, edit, manage).
- **Statutory Schema**:
  - `database/migrations/2026_09_22_000002_add_payment_fields_to_applications_tables.php` and `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`: Confirmed all 7 statutory tables (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`, plus `ec_compliances`) have `product_value`, `paid_amount`, `pending_amount`, `payment_status`.
  - `app/Models/Customer.php` (lines 99–142): Has direct relationships to all 7 statutory models (`leaseApplications`, `miningApplications`, `environmentProjects`, `pptApplications`, `dgpsSurveys`, `droneSurveys`, `ecCertificates`, `ecCompliances`).
- **UI & Layout Conventions**:
  - `resources/views/layouts/sidebar.blade.php`: Line 115 provides the exact insertion point for the `<li class="has-arrow">` Accounts navigation section gated by `@can('account.view')`.
  - `resources/views/pages/customer_tracking/proforma_invoice.blade.php`: Contains high-fidelity A4 printable styling (`.page-sheet`, `@media print`, `.no-print-bar`), GTMS insignia header, and currency-in-words formatting algorithms (`CustomerTrackingController.php:1642–1701`).
- **Composer Dependencies**:
  - `composer.json` (lines 8–13): GTMS runs Laravel 12 and `spatie/laravel-permission`. There is no third-party spreadsheet package installed, confirming CSV export must be implemented via native PHP streaming (`response()->streamDownload` / `fputcsv`).

## 2. Logic Chain
1. *From* the inspection of `routes/web.php` and existing permission names in `RolePermissionSeeder.php`, *it follows that* the Accounts module routes must be placed under an `accounts` URL prefix, named `accounts.*`, and protected by `account.view`, `account.create`, `account.edit`, and `account.delete`.
2. *From* the inspection of `ApplicationPayment` and statutory controllers, *it follows that* `ApplicationPayment` stores only the aggregate status of an application, not individual installment receipts. Thus, recording payments requires creating a dedicated `receipt_vouchers` table while atomically locking and updating both the target statutory table and the polymorphic `application_payments` row within `DB::transaction(...)`.
3. *From* the inspection of `Customer.php` relationships across the 7 statutory models, *it follows that* an AJAX endpoint `getCustomerPendingDues` can query all 7 modules where `pending_amount > 0` to power the centralized payment collection interface.
4. *From* the inspection of `CustomerTrackingController` invoice generators and `proforma_invoice.blade.php`, *it follows that* Quotation generation (R1) and Receipt Voucher printing (R3) can replicate the existing CSS print engine, Indian number-to-words algorithm, and GTMS insignia tokens.
5. *From* `composer.json` containing only native Laravel without Excel packages, *it follows that* R5 CSV export should be implemented with zero-dependency `fputcsv` streaming with a UTF-8 BOM (`\xEF\xBB\xBF`) for Microsoft Excel compatibility.

## 3. Caveats
- No new database tables currently exist for quotations or receipt vouchers. Dedicated migrations (`create_quotations_table`, `create_receipt_vouchers_table`) must be executed during the build phase.
- Some legacy customer records may have null company names or addresses; quotation generation and receipt rendering logic must incorporate null-coalescing fallbacks (`$customer->company_name ?: $customer->customer_name`).

## 4. Conclusion
The codebase is fully analyzed and architecturally primed for the Accounts module implementation. Detailed technical findings, route definitions, atomic transaction flows, schema definitions, and feature tables have been compiled into `survey_report.md`. The implementation requires 4 controllers (`QuotationController`, `PaymentCollectionController`, `CustomerLedgerController`, `AccountReportController`), 2 migrations, 4 Spatie permissions, and a feature test suite (`AccountsModuleTest`).

## 5. Verification Method
- **Inspect Survey Report**: Read `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\survey_report.md` to review the route tables, schema definitions, and feature matrix.
- **Inspect Code Citations**:
  - `routes/web.php:34-349` for routing patterns.
  - `database/seeders/RolePermissionSeeder.php:25-133` for RBAC configuration.
  - `app/Http/Controllers/CustomerController.php:762-785` and `PptDepartmentController.php:191-246` for payment persistence and transaction management.
  - `resources/views/layouts/sidebar.blade.php:115` for sidebar integration placement.
- **Invalidation Conditions**: If any existing statutory application table lacks `product_value`, `paid_amount`, or `pending_amount` columns, or if `RolePermissionSeeder.php` uses a non-dot permission format, this analysis would be invalidated.
