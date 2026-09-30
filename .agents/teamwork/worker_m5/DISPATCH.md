# Dispatch — worker_m5

## Milestone 5: Automated Feature Test Suite, Zero Regression & Migration Verification

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Project Specs: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md
- Existing Models & Migrations: `Quotation`, `QuotationItem`, `PaymentReceipt`, `Customer`, `ApplicationPayment`, `LeaseApplication`, `MiningApplication`, `DgpsSurvey`, etc.
- Existing Test Patterns: `tests/Feature/ApplicationHandlersAndPaymentsTest.php`, `tests/Feature/CustomerTrackingFilterTest.php`, `tests/Feature/UserManagementAndAuthTest.php`, `tests/Feature/PptDgpsAndEcComplianceTest.php`

* Expected Output:
1. Test Suite Implementation:
   - Create `tests/Feature/AccountsModuleTest.php` using `DatabaseTransactions` on connection `gtms_data`.
   - Comprehensive test cases covering all 6 requirements (R1–R6):
     * **Quotation Management (R1)**:
       - Quotation creation with valid line items, automated subtotal and 18% GST calculation.
       - Quotation validation (rejection when missing customer or invalid line items).
       - Quotation listing with search/status filters.
       - Quotation show and high-fidelity standalone A4 print view rendering (HTTP 200, contains GTMS insignia, quotation number, total in words).
       - Customer concessions AJAX lookup (`accounts.quotations.customer-concessions`).
     * **Payment Collection & Atomic Auto-Synchronization (R2)**:
       - Pending dues resolver (`accounts.payments.customer-dues`) returns active statutory dues across modules.
       - Recording payment for `mining_applications`: atomically updates `paid_amount`, `pending_amount`, `payment_status` on `mining_applications`, updates polymorphic `application_payments`, and inserts `payment_receipts`.
       - Recording payment for `dgps_surveys` (or `lease_applications`): verifies atomic sync across all three tables.
       - General retainer payment without statutory application: verifies receipt creation without corrupting application tables.
     * **Official Receipt Voucher Generation & Printing (R3)**:
       - Generates sequential receipt numbers (`GTMS/REC/{YYYY}/{0001}`).
       - Standalone printable A4/A5 voucher view rendering (HTTP 200, official headers, balance due, amount in words, authorized signatory seal).
       - Receipts listing with payment mode and date filtering.
     * **Customer Financial Ledger & Statement of Account (R4)**:
       - Customer ledger index shows customer directory with total debits, credits, and net balances.
       - Customer dossier (`accounts.ledger.show`) displays chronological debits and credits with accurate running balances.
       - Printable Customer Statement (`accounts.ledger.print`) renders clean standalone A4 document with net dues in words and digital seal.
     * **Centralized Financial Reports & Streamed CSV Export (R5)**:
       - Reports index renders 4 KPI summary cards (Total Collected, MTD Collected, Total Outstanding, Quotations Issued).
       - Multi-parametric filter tests (filtering by date range, customer, application type, and payment mode).
       - Streamed CSV export endpoint (`accounts.reports.export-csv`) returns HTTP 200 with `text/csv` headers, UTF-8 BOM, valid CSV headers, and accurate matching data rows.
     * **Spatie RBAC Permission Gating (R6)**:
       - Authenticated user with `account.view` can access view/print routes, but receives 403 Forbidden on `accounts.quotations.create` or `accounts.payments.create`.
       - Authenticated user with `account.create` can create quotations and record payments.
       - Authenticated user without `account.*` permissions receives 403 on accounts routes.
       - Unauthenticated guest is redirected to login.
       - Sidebar contains Accounts navigation for authorized users.
2. Test Execution & Verification:
   - Run `php artisan test --filter=AccountsModuleTest` and verify 100% passing tests with 0 failures.
   - Run existing test suites:
     * `php artisan test --filter=UserManagementAndAuthTest`
     * `php artisan test --filter=CustomerTrackingFilterTest`
     * `php artisan test --filter=PptDgpsAndEcComplianceTest`
     * `php artisan test --filter=ApplicationHandlersAndPaymentsTest`
     Verify zero regressions across all suites.
   - Verify migration rollback and re-run:
     * `php artisan migrate:rollback --step=2`
     * `php artisan migrate`
     Verify 100% clean execution without foreign key or constraint errors.
3. Handoff Report:
   - Write comprehensive report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m5\handoff.md` with full test output logs.

* Constraints:
- Exclusive write ownership of `tests/Feature/AccountsModuleTest.php`.
- Follow PHPUnit / Laravel 12 conventions.
- Do not modify core application logic unless fixing a verified bug discovered during testing.

* Validation Criteria:
- All tests in `AccountsModuleTest` pass.
- All existing tests pass.
- Clean migration rollback and re-migration.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A forensic auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## 2026-09-29T06:51:11Z
Received dispatch assignment for Milestone 5 (Automated Feature Test Suite, Zero Regression & Migration Verification).
Target: tests/Feature/AccountsModuleTest.php
Verification: All feature test suites pass, clean migration rollback and re-run.

