# Dispatch — worker_m4

## Milestone 4: Customer Financial Ledger, Comprehensive Reports, CSV Export & Sidebar Integration (R4, R5, R6)

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Project Specs: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md
- UI & Layout Survey: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_3\survey_report.md
- Existing Models & Migrations: `Quotation`, `PaymentReceipt`, `Customer`, `ApplicationPayment`
- Corporate Print Assets: `public/images/invoices/gtms_pi_banner.png`, `public/images/invoices/gtms_logo.png`, `public/images/invoices/gtms_stamp.png`

* Expected Output:
1. Controllers:
   - `app/Http/Controllers/Accounts/CustomerLedgerController.php`:
     * Enforce Spatie permission `account.view`.
     * `index(Request $request)`: Customer directory with search and pagination, showing each customer's Total Billed (Quotations), Total Received (Receipts), Net Outstanding Balance, and quick action to view dossier.
     * `show(Request $request, Customer $customer)`: Single-pane customer dossier. Resolves customer by ID or slug. Supports date filtering (`from_date`, `to_date`). Queries Debits (`Quotation` records for customer) and Credits (`PaymentReceipt` records for customer), merges chronologically, computes running balance, and outputs summary KPIs (Total Billed, Total Received, Net Balance Due).
     * `printStatement(Request $request, Customer $customer)`: Standalone printable Statement of Account with GTMS branding, statement period, itemized transactions, running balance, net dues in words, and authorized signatory zone.
   - `app/Http/Controllers/Accounts/FinancialReportController.php`:
     * Enforce Spatie permission `account.view`.
     * `index(Request $request)`: Financial reports dashboard with:
       - 4 KPI summary cards: Total Collected (All-time), Total Collected (Month-to-Date), Total Outstanding Receivables (sum of pending amounts across statutory modules), and Total Quotations Value & Count.
       - Multi-parametric filter bar: date range (`from_date`, `to_date`), customer (`customer_id`), application module (`application_type`), payment mode (`payment_mode`), search query.
       - Detailed paginated transaction table with customer, application reference, payment mode, bank ref, date, amount paid, balance due.
       - Export CSV action button.
     * `exportCsv(Request $request)`: Streamed CSV download using `Symfony\Component\HttpFoundation\StreamedResponse`. Applies identical filter criteria, writes CSV headers (`Receipt No`, `Date`, `Customer`, `Company`, `Application Type`, `Application Ref`, `Payment Mode`, `Bank Name`, `Reference / UTR`, `Amount Paid`, `Balance Due`, `Recorded By`), streams rows safely with zero memory bloat.
2. Sidebar Integration:
   - In `resources/views/layouts/sidebar.blade.php`:
     Inject the 'Accounts' navigation group right after Drone Survey (line 115) per GTMS navigation standards, protected by `@canany(['account.view', 'account.create'])`, with submenu links to Quotations, Collect Payment, Payment Receipts, Customer Ledger, and Financial Reports with appropriate `@can` checks.
3. Blade Views:
   - `resources/views/pages/accounts/ledger/index.blade.php`: Customer directory with ledger summaries, search, and action links.
   - `resources/views/pages/accounts/ledger/show.blade.php`: Single-pane customer financial dossier with KPI cards, date range filter, chronological ledger table with running balances, and "Print Statement" button.
   - `resources/views/pages/accounts/ledger/print.blade.php`: Standalone A4 printable statement matching official GTMS letterhead, client details, statement period, chronological transactions, running balance, net balance due, and official seal.
   - `resources/views/pages/accounts/reports/index.blade.php`: Centralized financial reports dashboard with 4 KPI cards, multi-parametric filter form, detailed transactions table, pagination, and "Export CSV" button.
4. Routes:
   - In `routes/web.php`, register under prefix `accounts` with name prefix `accounts.`:
     * `ledger.index` (GET `/accounts/ledger`)
     * `ledger.show` (GET `/accounts/ledger/{customer}`)
     * `ledger.print` (GET `/accounts/ledger/{customer}/print`)
     * `reports.index` (GET `/accounts/reports`)
     * `reports.export-csv` (GET `/accounts/reports/export-csv`)

* Constraints:
- Exclusive write ownership of the files listed above.
- Follow Laravel 12, Bootstrap 5, and GTMS design conventions.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m4\handoff.md`.

* Validation Criteria:
- Customer ledger calculates debits, credits, and running balance accurately.
- Statement print renders standalone without layout clutter.
- Financial reports filter accurately across date, customer, module, and payment mode.
- Streamed CSV export downloads cleanly with valid CSV syntax.
- Sidebar displays Accounts menu under proper RBAC permissions.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A forensic auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## 2026-09-29T06:30:36Z
You are worker_m4.
Your working directory is: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m4
Your detailed dispatch assignment is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m4\DISPATCH.md
Authoritative User Request is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
Master Project Specification is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md

* Input:
- Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md first.
- Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m4\DISPATCH.md for complete instructions.

* Expected Output:
- Implement Milestone 4 (Customer Financial Ledger, Reports, CSV Export & UI Sidebar Integration):
  1. Create `app/Http/Controllers/Accounts/CustomerLedgerController.php` (with `index()`, `show()`, `printStatement()`).
  2. Create `app/Http/Controllers/Accounts/FinancialReportController.php` (with `index()`, `exportCsv()`).
  3. Create Blade views:
     - `resources/views/pages/accounts/ledger/index.blade.php`
     - `resources/views/pages/accounts/ledger/show.blade.php`
     - `resources/views/pages/accounts/ledger/print.blade.php` (standalone printable statement)
     - `resources/views/pages/accounts/reports/index.blade.php`
  4. Inject 'Accounts' menu group in `resources/views/layouts/sidebar.blade.php` under `@canany(['account.view', 'account.create'])`.
  5. Register ledger and reports routes in `routes/web.php` under prefix `accounts`.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m4\handoff.md`.
- Send a completion message via send_message to orchestrator_3.

