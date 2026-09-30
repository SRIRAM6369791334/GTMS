# Project: GTMS Accounts & Financial Management Module

## Architecture
The Accounts & Financial Management Module integrates commercial billing, quotation generation, cross-module statutory payment collection, official money receipts, customer financial ledgers, and centralized financial reporting into GTMS.

### Key Components:
1. **Quotation Engine**: Manages commercial proposals before or during statutory processing, linking customers and quarry concessions, computing multi-service line items with SAC codes, and rendering standalone A4 printable documents with Indian numbering currency text.
2. **Centralized Payment Collection Engine**: Cross-application dues resolver that queries all 7 statutory modules (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`, plus `ec_compliances`), displays outstanding amounts, and executes atomic DB transactions updating target application tables, the polymorphic `application_payments` summary table, and creating immutable receipt vouchers.
3. **Receipt Voucher Engine**: Generates sequential receipts (`GTMS/REC/YYYY/XXXX`), captures transaction metadata (mode, bank, reference, date, notes), and renders dedicated A4/A5 vouchers.
4. **Customer Financial Ledger**: Single-pane customer dossier tracking chronological debits (Quotations) and credits (Receipts/Payments), calculating live net balances, and generating formal statement printouts.
5. **Financial Reports & Streamed Export**: High-level financial KPIs (Total Collected, MTD Collected, Total Receivables, Total Quotations) with multi-parametric filtering and native streamed CSV exports.
6. **Spatie RBAC & UI Integration**: Sidebar navigation integrated into `resources/views/layouts/sidebar.blade.php`, protected by `account.view`, `account.create`, `account.edit`, and `account.delete` permissions.

---

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Accounts DB Migrations & Schema | Tables for `quotations`, `quotation_items`, `payment_receipts` | M1 | Survey (DONE) |
| 2 | Eloquent Models & Relationships | Models for `Quotation`, `QuotationItem`, `PaymentReceipt`, relation to Customer & applications | M1 | Survey (DONE) |
| 3 | Spatie RBAC Permissions & Seeding | Seed `account.view`, `account.create`, `account.edit`, `account.delete` into Spatie permissions & RolesController | M1 | Survey (DONE) |
| 4 | Client & Concession Selection | Auto-populate customer profile and quarry concession metadata via AJAX | M2 | R1 (DONE) |
| 5 | Dynamic Quotation Line Items | Multi-service line items with unit rates, quantities/areas, SAC codes, and automated totals | M2 | R1 (DONE) |
| 6 | Statutory Terms & Conditions | Configurable validity periods, milestone payment terms, and government challan exclusions | M2 | R1 (DONE) |
| 7 | High-Fidelity A4 Quotation Print | Standalone A4 print/PDF layout with GTMS insignia, ref number, total in words, authorized signatory zone | M2 | R1 (DONE) |
| 8 | Customer Pending Dues Resolver | Resolver displaying outstanding balances across all 7 statutory modules | M3 | R2 (DONE) |
| 9 | Payment Collection Desk | Payment collection interface with payment modes (Cash, Cheque, NEFT/RTGS, UPI/GPay), bank ref, transaction dates | M3 | R2 (DONE) |
| 10 | Atomic DB Cross-Table Sync | Atomic transaction updating module table, polymorphic `application_payments`, and inserting `payment_receipts` | M3 | R2 (DONE) |
| 11 | Sequential Money Receipt Voucher | Sequential receipt numbers (`GTMS/REC/YYYY/XXXX`) with customer, application ref, balance due, payment details | M3 | R3 (DONE) |
| 12 | Printable A4/A5 Voucher View | Standalone print view with GTMS banner, official seal, officer timestamp, ready for distribution | M3 | R3 (DONE) |
| 13 | Customer Financial Ledger Dossier | Chronological ledger of debits (Quotations) and credits (Receipts), calculating live net balances | M4 | R4 (DONE) |
| 14 | Printable Customer Statement PDF | Standalone printable Customer Statement layout for audits and reminders | M4 | R4 (DONE) |
| 15 | Centralized Financial Reports KPI | Dashboard with KPI summary cards (Total Collected all-time & MTD, Total Outstanding, Quotations Issued) | M4 | R5 (DONE) |
| 16 | Multi-Parametric Filter & CSV Export | Filter by date range, customer, application type, payment mode; native streamed CSV export | M4 | R5 (DONE) |
| 17 | Sidebar Navigation Integration | Accounts group in `sidebar.blade.php` with MetisMenu and Spatie permission gating | M4 | R6 (DONE) |
| 18 | Automated Feature Test Suite | `tests/Feature/AccountsModuleTest.php` covering R1-R6 with 100% pass | M5 | Acceptance (IN_PROGRESS) |
| 19 | Zero-Regression & Migration Rollback | Clean migration rollback/re-run and verification against existing test suites | M5 | Acceptance (PLANNED) |

---

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Database Foundation & RBAC | Migrations, Models, Spatie Permissions & Seeders | none | DONE |
| M2 | Quotation Engine & Print Layout | Quotation CRUD, line items, AJAX autofill, A4 print template | M1 | DONE |
| M3 | Payment Collection & Receipts | Pending dues resolver, atomic DB sync, receipt voucher generation & A4/A5 print | M1 | DONE |
| M4 | Customer Ledger, Reports & UI | Customer ledger, statements, KPI reports, CSV export, sidebar navigation | M2, M3 | DONE |
| M5 | Test Suite & Forensic Audit Gate | `AccountsModuleTest.php`, regression test verification, Reviewers, Challengers & Auditor | M4 | IN_PROGRESS |

---

## Interface Contracts
### Quotation Engine ↔ Database
- Model `Quotation`: `id`, `quotation_number`, `customer_id`, `lease_application_id` (nullable), `customer_name`, `company_name`, `phone`, `email`, `gst_number`, `address`, `quarry_name`, `district_id`, `taluk`, `village`, `survey_numbers`, `area_extent_ha`, `mineral_name`, `subtotal`, `tax_rate`, `tax_amount`, `total_amount`, `validity_days`, `payment_terms`, `exclusions`, `notes`, `status` (`draft`, `sent`, `accepted`, `rejected`, `converted`), `branch_id`, `created_by`.
- Model `QuotationItem`: `id`, `quotation_id`, `service_name`, `sac_code`, `description`, `quantity`, `unit`, `unit_rate`, `subtotal`.

### Payment Collection ↔ Application Sync Contract
- Method: `PaymentCollectionController@store`
- Request parameters: `customer_id`, `application_type` (`lease`, `mining`, `environment`, `ppt`, `dgps`, `drone`, `ec_certificate`, `ec_compliance`, `general`), `application_id`, `amount_paid`, `payment_mode` (`Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`, `Other`), `bank_name`, `reference_number`, `transaction_date`, `notes`.
- Database Transaction:
  1. Lock application row with `lockForUpdate()`.
  2. Compute new `paid_amount = current_paid + amount_paid`, `pending_amount = max(0, product_value - new_paid_amount)`.
  3. Determine `payment_status = pending_amount <= 0 ? 'paid' : (new_paid_amount > 0 ? 'partial' : 'pending')`.
  4. Update application table.
  5. Synchronize `application_payments` via `updateOrCreate(['application_type' => ..., 'application_id' => ...], ['product_value' => ..., 'paid_amount' => new_paid, 'pending_amount' => new_pending, 'payment_status' => new_status])`.
  6. Generate sequential `receipt_number`: `GTMS/REC/{YYYY}/{0001}`.
  7. Insert `PaymentReceipt`: stores snapshot of customer, application ref, amount paid, balance due after payment, payment mode, bank/ref details, and `created_by`.

### Reports Export Contract
- Native streamed response (`Symfony\Component\HttpFoundation\StreamedResponse`) with `text/csv` headers.
- Filter criteria applied directly to query before chunking or streaming.

---

## Code Layout
- Migrations:
  - `database/migrations/2026_09_29_000001_create_accounts_quotations_table.php` (DONE)
  - `database/migrations/2026_09_29_000002_create_accounts_payment_receipts_table.php` (DONE)
- Models:
  - `app/Models/Quotation.php` (DONE)
  - `app/Models/QuotationItem.php` (DONE)
  - `app/Models/PaymentReceipt.php` (DONE)
  - `app/Models/Customer.php` (DONE - relations added)
- Controllers:
  - `app/Http/Controllers/Accounts/QuotationController.php` (DONE)
  - `app/Http/Controllers/Accounts/PaymentCollectionController.php` (DONE)
  - `app/Http/Controllers/Accounts/PaymentReceiptController.php` (DONE)
  - `app/Http/Controllers/Accounts/CustomerLedgerController.php` (DONE)
  - `app/Http/Controllers/Accounts/FinancialReportController.php` (DONE)
- Routes:
  - Inside `routes/web.php` under prefix `accounts` with middleware `['auth', 'permission:account.view']` (DONE)
- Seeders:
  - `database/seeders/RolePermissionSeeder.php` (DONE)
  - `app/Http/Controllers/RolesController.php` (DONE)
- Views:
  - `resources/views/pages/accounts/quotations/index.blade.php` (DONE)
  - `resources/views/pages/accounts/quotations/create.blade.php` (DONE)
  - `resources/views/pages/accounts/quotations/edit.blade.php` (DONE)
  - `resources/views/pages/accounts/quotations/show.blade.php` (DONE)
  - `resources/views/pages/accounts/quotations/print.blade.php` (DONE)
  - `resources/views/pages/accounts/payments/create.blade.php` (DONE)
  - `resources/views/pages/accounts/receipts/index.blade.php` (DONE)
  - `resources/views/pages/accounts/receipts/show.blade.php` (DONE)
  - `resources/views/pages/accounts/receipts/print.blade.php` (DONE)
  - `resources/views/pages/accounts/ledger/index.blade.php` (DONE)
  - `resources/views/pages/accounts/ledger/show.blade.php` (DONE)
  - `resources/views/pages/accounts/ledger/print.blade.php` (DONE)
  - `resources/views/pages/accounts/reports/index.blade.php` (DONE)
  - `resources/views/layouts/sidebar.blade.php` (DONE)
- Test Suite:
  - `tests/Feature/AccountsModuleTest.php` (M5 IN_PROGRESS)
