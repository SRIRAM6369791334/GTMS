# Dispatch — worker_m2

## Milestone 2: Quotation Generation Engine & High-Fidelity Print Layout (R1)

* Input:
- Authoritative User Request: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md (under header ## 2026-09-29T05:33:01Z)
- Project Root: c:\xampp\htdocs\GTMS\gtms
- Project Specs: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\orchestrator_3\PROJECT.md
- UI & Print Findings: c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_3\survey_report.md
- Reference Print Template: `resources/views/pages/customer_tracking/tax_invoice.blade.php`
- Corporate Assets in `public/images/invoices/`: `gtms_pi_banner.png`, `gtms_logo.png`, `gtms_stamp.png`

* Expected Output:
1. Controller `app/Http/Controllers/Accounts/QuotationController.php`:
   - Enforce Spatie permissions (`account.view` for index/show/print, `account.create` for create/store, `account.edit` for edit/update, `account.delete` for destroy).
   - `index(Request $request)`: Filterable list by customer, status, date range, search query; pagination.
   - `create()`: Preload customers, districts, default statutory services catalog with unit rates & SAC codes.
   - `store(Request $request)`: Validate inputs (customer, concession fields, items array). Calculate item subtotals, tax rate (18% default), tax amount, total amount. Generate sequential `quotation_number` (`GTMS/QTN/{YYYY}/{0001}`). Save `Quotation` and `QuotationItem` rows inside `DB::transaction`.
   - `show(Quotation $quotation)`: Detailed view of quotation, line items, customer profile, and concession snapshot.
   - `edit(Quotation $quotation)`: Edit form for draft/sent quotations.
   - `update(Request $request, Quotation $quotation)`: Update quotation and re-sync line items inside `DB::transaction`.
   - `destroy(Quotation $quotation)`: Soft-delete/delete quotation and its items.
   - `print(Quotation $quotation)`: Render standalone A4 print/PDF layout.
   - `getCustomerConcessions(Customer $customer)`: AJAX JSON endpoint returning customer profile (`customer_name`, `company_name`, `phone`, `email`, `gst_number`, `address`) and an array of quarry concessions loaded from `$customer->leaseApplications` (`id`, `quarry_name`, `district_id`, `district_name`, `taluk`, `village`, `survey_numbers`, `area_extent_ha`, `minerals`).
2. Views:
   - `resources/views/pages/accounts/quotations/index.blade.php`:
     * Extends `layouts.app`.
     * Data table with Quotation No, Date, Customer, Quarry/Concession, Total Amount, Status badge (`draft`, `sent`, `accepted`, `rejected`, `converted`), Actions (View, Edit, Print, Delete).
     * Filter bar and "Create Quotation" button.
   - `resources/views/pages/accounts/quotations/create.blade.php`:
     * Extends `layouts.app`.
     * Customer selector dropdown with AJAX auto-population of client details (name, company, phone, email, GST, address).
     * Concession selector (populated from customer's lease applications or manual entry) with quarry name, district, taluk, village, survey numbers, extent in Ha.
     * Dynamic Line Items table: Add Line Item, Remove Line Item, service dropdown / text, SAC code, quantity, unit (Ha, Nos, Survey, Month), unit rate, auto-calculated line subtotal.
     * Real-time calculation of Subtotal, 18% GST (CGST 9% + SGST 9%), and Grand Total.
     * Terms & conditions, validity days (default 30), payment milestones text, exclusions (government statutory challan exclusions).
   - `resources/views/pages/accounts/quotations/edit.blade.php`:
     * Extends `layouts.app`, allows editing quotation data and dynamic line items.
   - `resources/views/pages/accounts/quotations/show.blade.php`:
     * Extends `layouts.app`, clean administrative summary card with print button and item breakdown.
   - `resources/views/pages/accounts/quotations/print.blade.php`:
     * Standalone HTML5 layout (does NOT extend `layouts.app`).
     * Floating action bar `.no-print-bar` with Back button and Print button (`onclick="window.print()"`).
     * `@media print` CSS for `@page { size: A4 portrait; margin: 10mm; }`.
     * Corporate header using `gtms_pi_banner.png` or `gtms_logo.png` with official address (`1/237, AR Complex, Salem Main Road, Meyyanur, Salem – 636 004, Tamil Nadu`), GSTIN `33ABCDE1234F1Z5`, PAN, Contact details.
     * Quotation Reference Number (e.g. `GTMS/QTN/2026/0001`), Date, Validity period.
     * Two-column box: Client Details (M/s, GST, Address) vs Quarry Concession Details (Quarry Name, Village, Taluk, District, S.F. Nos, Extent in Ha).
     * Tabular Line Items Breakdown: S.No, Service Description, SAC Code, Qty/Area, Unit, Rate (Rs.), Subtotal (Rs.).
     * Summary box: Subtotal, CGST (9%), SGST (9%), Total Quotation Value.
     * Total Amount in Words (Indian Rupees, e.g. "Rupees One Lakh Eighteen Thousand Only").
     * Payment Terms & Statutory Exclusions notes.
     * Bank coordinates (State Bank of India, Meyyanur Branch, Salem).
     * Authorized Signatory Zone with `gtms_stamp.png` and `Dr. S. Karuppannan, M.Sc., Ph.D. / Managing Partner & RQP`.
3. Routes in `routes/web.php`:
   - Register the accounts quotation routes cleanly under `Route::middleware(['auth'])->prefix('accounts')->name('accounts.')->group(...)`.

* Constraints:
- Exclusive write ownership of the files listed above.
- Follow Laravel 12, Bootstrap 5, and GTMS conventions.
- Write your completion handoff report to `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m2\handoff.md`.

* Validation Criteria:
- Quotation creation, validation, storage, and retrieval execute with zero 500 errors.
- Dynamic line item subtotals and 18% GST compute accurately.
- Print view renders cleanly without sidebar/footer clutter.

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A forensic auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

## 2026-09-29T06:05:00Z
Task received from parent (ecb0a4ee-1d25-4637-a1fb-552edc53b301):
Implement Milestone 2: Quotation Generation Engine & High-Fidelity Print Layout (R1).

