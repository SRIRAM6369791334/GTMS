# Dispatch — worker_m3

## Milestone 3: Centralized Payment Collection Engine & Official Money Receipt Vouchers (R2 & R3)

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Project Specs: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md
- Survey Findings:
  * c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\survey_report.md
  * c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\spec_miner_survey_accounts_2\survey_report.md
  * c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_3\survey_report.md
- Existing Models: `PaymentReceipt`, `ApplicationPayment`, `Customer`, `LeaseApplication`, `MiningApplication`, `EnvironmentProject`, `PptApplication`, `DgpsSurvey`, `DroneSurvey`, `EcCertificate`, `EcCompliance`.
- Corporate Stationery Assets: `public/images/invoices/gtms_pi_banner.png`, `public/images/invoices/gtms_logo.png`, `public/images/invoices/gtms_stamp.png`.

* Expected Output:
1. Controllers:
   - `app/Http/Controllers/Accounts/PaymentCollectionController.php`:
     * Enforce Spatie permissions (`account.create` for create/store, `account.view` for getCustomerPendingDues).
     * `create(Request $request)`: Payment collection desk view. Preloads customers, supports pre-selected `customer_id` via query string.
     * `getCustomerPendingDues(Customer $customer)`: AJAX JSON endpoint returning outstanding dues across all 7 statutory modules (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`, plus `ec_compliances`). For each application with positive pending_amount or unpaid status, return: `application_type`, `application_id`, `reference_number` / `application_number`, `service_label`, `concession_info` (village, taluk, survey nos), `product_value`, `paid_amount`, `pending_amount`, `payment_status`.
     * `store(Request $request)`: Validates payment inputs (`customer_id`, `application_type`, `application_id`, `amount_paid`, `payment_mode`, `bank_name`, `reference_number`, `transaction_date`, `notes`).
       Inside `DB::transaction()`:
       - Uses `lockForUpdate()` on target application row.
       - Computes: `new_paid = previous_paid + amount_paid`, `new_pending = max(0, product_value - new_paid)`, `new_status = new_pending <= 0 ? 'paid' : 'partial'`.
       - Updates application model columns: `paid_amount`, `pending_amount`, `payment_status`.
       - Synchronizes `ApplicationPayment` table via `updateOrCreate`.
       - Generates sequential receipt voucher number via `PaymentReceipt::generateReceiptNumber()` (`GTMS/REC/{YYYY}/{0001}`).
       - Creates immutable `PaymentReceipt` record with snapshot customer, application details, `amount_paid`, `balance_due`, `previous_paid`, payment mode, bank/ref, officer timestamp (`created_by`).
       - Redirects with success flash message to `accounts.receipts.show` with direct print option.
   - `app/Http/Controllers/Accounts/PaymentReceiptController.php`:
     * Enforce Spatie permissions (`account.view`).
     * `index(Request $request)`: Filterable list of all receipt vouchers (filter by customer, payment mode, date range, search query; pagination).
     * `show(PaymentReceipt $receipt)`: Dedicated receipt voucher overview in GTMS theme.
     * `print(PaymentReceipt $receipt)`: High-fidelity standalone printable voucher view (supports A4 and A5 printable voucher layouts).
2. Blade Views:
   - `resources/views/pages/accounts/payments/create.blade.php`:
     * Extends `layouts.app`.
     * Dynamic Payment Collection Desk with Customer Selector.
     * Dynamic Outstanding Dues Panel: When a customer is selected, AJAX fetches and displays all active statutory applications across all 7 modules with badges for payment status and pending dues. Clicking "Select Application" populates the payment form automatically.
     * Payment submission form: Target application info card, Amount Received, Payment Mode (Cash, Cheque, NEFT/RTGS, UPI/GPay, Other), Bank Name, UTR / Cheque Ref Number, Payment Date, Notes.
   - `resources/views/pages/accounts/receipts/index.blade.php`:
     * Extends `layouts.app`.
     * List of all official money receipt vouchers with receipt number, customer, application reference, amount paid, payment mode, transaction date, collector/officer, and actions (View, Print).
   - `resources/views/pages/accounts/receipts/show.blade.php`:
     * Extends `layouts.app`.
     * Formal receipt detail page with print action button.
   - `resources/views/pages/accounts/receipts/print.blade.php`:
     * Standalone HTML5 layout (does NOT extend `layouts.app`).
     * Floating `.no-print-bar` with Back button and Print button (`window.print()`).
     * `@media print` rules for `@page { size: A4 portrait; margin: 10mm; }` and `@page voucher { size: A5 landscape; margin: 8mm; }`.
     * Official GTMS corporate header (`gtms_pi_banner.png` or `gtms_logo.png`), ISO certification, corporate address, GSTIN, PAN.
     * Official Money Receipt title, receipt voucher number (e.g. `GTMS/REC/2026/0001`), transaction date.
     * Received From: Customer Name, Company, GSTIN, Mobile, Concession location.
     * Payment Details: Amount Received in figures and Indian Currency Words (e.g. "Rupees Fifty Thousand Only"), Payment Mode, Bank & Reference/UTR.
     * Application Account: Target Statutory Service, Application No, S.F. No, Village, Total Product Value, Previously Paid, Current Paid, Balance Due.
     * Authorized Signatory Zone with `gtms_stamp.png` and officer designation.
3. Routes:
   - In `routes/web.php`, register under prefix `accounts` with name prefix `accounts.`:
     * `payments.create` (GET `/accounts/payments/create`)
     * `payments.store` (POST `/accounts/payments`)
     * `payments.customer-dues` (GET `/accounts/payments/customer-dues/{customer}`)
     * `receipts.index` (GET `/accounts/receipts`)
     * `receipts.show` (GET `/accounts/receipts/{receipt}`)
     * `receipts.print` (GET `/accounts/receipts/{receipt}/print`)

* Constraints:
- Exclusive write ownership of the files listed above.
- Strict atomic database transactions (`DB::transaction`) for all payment mutations.
- Follow Laravel 12 and GTMS standards.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m3\handoff.md`.

* Validation Criteria:
- Cross-application dues resolver accurately inspects all statutory modules.
- Payment recording atomically synchronizes module tables, `application_payments`, and `payment_receipts`.
- Receipt vouchers generate sequential numbers and print cleanly in standalone A4/A5 view.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A forensic auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## 2026-09-29T06:17:00Z
You are worker_m3.
Your working directory is: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m3
Your detailed dispatch assignment is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m3\DISPATCH.md
Authoritative User Request is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
Master Project Specification is in: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md

* Input:
- Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md first.
- Read c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m3\DISPATCH.md for complete instructions.

* Expected Output:
- Implement Milestone 3 (Centralized Payment Collection Engine & Receipt Vouchers):
  1. Create `app/Http/Controllers/Accounts/PaymentCollectionController.php` (with `create()`, `getCustomerPendingDues()`, `store()`).
  2. Create `app/Http/Controllers/Accounts/PaymentReceiptController.php` (with `index()`, `show()`, `print()`).
  3. Create Blade views:
     - `resources/views/pages/accounts/payments/create.blade.php` (Payment desk with dynamic dues resolver)
     - `resources/views/pages/accounts/receipts/index.blade.php`
     - `resources/views/pages/accounts/receipts/show.blade.php`
     - `resources/views/pages/accounts/receipts/print.blade.php` (standalone printable A4/A5 voucher)
  4. Register payment and receipt routes in `routes/web.php` under prefix `accounts`.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m3\handoff.md`.
- Send a completion message via send_message to orchestrator_3.

* Constraints:
- Exclusive write ownership of the files listed above.
- Strict atomic database transactions (`DB::transaction`) for all payment mutations.
- Follow Laravel 12 and GTMS standards.

* Validation Criteria:
- Clean controller logic, zero unhandled exceptions, atomic cross-table synchronization with `application_payments`, professional standalone printable voucher layout.

