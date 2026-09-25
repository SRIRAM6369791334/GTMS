# 05 — HTTP Controllers Specification & Audit

**Document Version:** 1.0.0  
**Target Codebase:** `c:\xampp\htdocs\GTMS\gtms`  
**Classification:** Technical Architecture & Method-by-Method Audit  
**Author:** Spec Miner Survey 2 (Backend Logic Specialist)  
**Total Controllers Audited:** 20  
**Total Methods Audited:** 121  

---

## 1. Executive Summary & Controller Architecture

The GTMS (Granite / Mining Tracking Management System) application adopts a **Monolithic MVC Architecture with Controller-Centric Business Logic**. Rather than delegating logic to decoupled Service or Action layers, domain state transitions, file processing, transactional rollbacks, polymorphic ledger updates, and dynamic view rendering are executed directly within the HTTP Controller layer.

### 1.1 Global Controller Characteristics
1. **Base Class Inheritance:** All controllers extend `App\Http\Controllers\Controller`, which is an empty abstract base class.
2. **Database Transactions:** High-impact multi-table transitions (`CustomerController@submit`, `CustomerController@moveToMining`, `EnverionsoneController@store`, `MiningController@store`, `EcCertificateController@store`, `PptDepartmentController@store`, `DgpsSurveyController@store`, `EcComplianceController@store`) wrap execution inside atomic `DB::transaction(...)` or `DB::beginTransaction() / DB::commit() / DB::rollBack()`.
3. **Dual Response Pattern:** Wizard and CRUD endpoints dynamically inspect `$request->ajax()` or `$request->wantsJson()`: returning JSON (`{ status: 1, message: '...', redirect: '...' }`) for modal/stepper interactions, while falling back to standard HTTP 302 redirects with session flash messages (`with('success', ...)`).
4. **Polymorphic Handlers & Payments:** Common operational controllers (`CustomerController`, `MiningController`, `EnverionsoneController`, `EcCertificateController`, `PptDepartmentController`, `DgpsSurveyController`, `EcComplianceController`) synchronize team allocation via `ApplicationHandler` and billing ledgers via `ApplicationPayment`.

---

## 2. Exhaustive Method-by-Method Controller Directory

Below is the complete, function-by-function audit of all 20 controllers residing in `app/Http/Controllers/`.

```
================================================================================
INDEX OF AUDITED CONTROLLERS:
1.  AuthController.php               (Lines 1–64)     - 3 Methods
2.  BranchController.php             (Lines 1–82)     - 4 Methods
3.  CategoryController.php           (Lines 1–81)     - 4 Methods
4.  Controller.php                   (Lines 1–9)      - Base Abstract Class
5.  CustomerController.php           (Lines 1–1851)   - 26 Methods
6.  CustomerDirectoryController.php  (Lines 1–397)    - 6 Methods
7.  CustomerTrackingController.php   (Lines 1–1117)   - 11 Methods
8.  DgpsSurveyController.php         (Lines 1–296)    - 6 Methods
9.  DroneSurveyController.php        (Lines 1–28)     - 2 Methods
10. EcCertificateController.php      (Lines 1–531)    - 6 Methods
11. EcComplianceController.php       (Lines 1–371)    - 6 Methods
12. EnverionsoneController.php       (Lines 1–624)    - 15 Methods
13. EnvironmentalB2Controller.php    (Lines 1–325)    - 8 Methods
14. MiningController.php             (Lines 1–998)    - 11 Methods
15. PptDepartmentController.php      (Lines 1–338)    - 7 Methods
16. ProductController.php            (Lines 1–90)     - 2 Methods
17. ProductStockController.php       (Lines 1–18)     - 1 Method
18. RolesController.php              (Lines 1–130)    - 5 Methods
19. UnitController.php               (Lines 1–16)     - 1 Method
20. UserController.php               (Lines 1–163)    - 4 Methods
================================================================================
```

---

### 1. AuthController (`app/Http/Controllers/AuthController.php`)
Manages authentication lifecycle, guest guards, dual email/user-code identification, and session token invalidation.

#### Method 1.1: `showLogin()`
- **HTTP Verb & URI:** `GET /` & `GET /login`
- **Route Name:** `login`
- **Middleware:** `web`, `guest`
- **Inputs:** None.
- **Database Queries:** None (checks `Auth::check()`).
- **View Rendered:** `pages.login`
- **View Parameters:** None.
- **Side-Effects:** If authenticated, redirects to intended dashboard (`redirect()->intended('/dashboard')`).

#### Method 1.2: `login(Request $request)`
- **HTTP Verb & URI:** `POST /login`
- **Route Name:** `login.post`
- **Middleware:** `web`, `guest`
- **Inputs:** `email` (string, required), `password` (string, required), `remember` (boolean, optional).
- **Database Queries:**
  - `User::where('email', $credentials['email'])->orWhere('user_code', $credentials['email'])->first()`
- **View Parameters:** N/A (redirects).
- **Side-Effects:**
  - Validates user existence and active status (`$user->status == 1`).
  - Calls `Auth::attempt(...)`.
  - Regenerates session (`$request->session()->regenerate()`).
  - Returns back with validation errors on inactive account or invalid credentials.

#### Method 1.3: `logout(Request $request)`
- **HTTP Verb & URI:** `POST /logout`
- **Route Name:** `logout`
- **Middleware:** `web`, `auth`
- **Inputs:** Current authenticated session.
- **Database Queries:** None.
- **Side-Effects:**
  - Invokes `Auth::logout()`.
  - Invalidate session (`$request->session()->invalidate()`).
  - Regenerates CSRF token (`$request->session()->regenerateToken()`).
  - Redirects to `/` with success flash message.

---

### 2. BranchController (`app/Http/Controllers/BranchController.php`)
Maintains operational company branches and departmental locations.

#### Method 2.1: `index()`
- **HTTP Verb & URI:** `GET /branch`
- **Route Name:** `branch.index`
- **Middleware:** `web`, `auth`, `permission:branch.view`
- **Inputs:** None.
- **Database Queries:** `Branch::get()`
- **View Rendered:** `pages.authentication.branch.index`
- **View Parameters:** `branches` (Collection of `Branch`).

#### Method 2.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /branchadd`
- **Route Name:** `branchadd`
- **Middleware:** `web`, `auth`, `permission:branch.create`
- **Inputs:** `branch_name` (required), `contact_person` (required), `mobile` (required), `address` (required), `city`, `state`, `pincode`.
- **Database Queries:** Inserts new row into `branches`.
- **View Parameters:** N/A (JSON response).
- **Side-Effects:** Returns JSON `{ status: 1, message: 'Branch Added Successfully', data: $branch }`.

#### Method 2.3: `update(Request $request)`
- **HTTP Verb & URI:** `POST /branchedit`
- **Route Name:** `branchedit`
- **Middleware:** `web`, `auth`, `permission:branch.edit`
- **Inputs:** `id`, `branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode`, `status`.
- **Database Queries:** `Branch::find($request->id)` and updates attributes.
- **View Parameters:** N/A (JSON response).
- **Side-Effects:** Returns JSON `{ status: 1, message: 'Branch Updated Successfully', data: $branch }`.

#### Method 2.4: `destroy(Request $request)`
- **HTTP Verb & URI:** `POST /branchdelete`
- **Route Name:** `branchdelete`
- **Middleware:** `web`, `auth`, `permission:branch.delete`
- **Inputs:** `id` (required).
- **Database Queries:** `Branch::find($request->id)->delete()`.
- **View Parameters:** N/A (JSON response).
- **Side-Effects:** Returns JSON `{ status: 1, message: 'Branch Deleted Successfully', data: $branch }`.

---

### 3. CategoryController (`app/Http/Controllers/CategoryController.php`)
Product and mineral commercial categorization controller.

#### Method 3.1: `index()`
- **HTTP Verb & URI:** `GET /category`
- **Route Name:** `category.index`
- **Middleware:** `web`, `auth`, `permission:category.view`
- **Database Queries:** `Category::all()`
- **View Rendered:** `pages.master.category.index`
- **View Parameters:** `category`.

#### Method 3.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /categoryadd`
- **Route Name:** `categoryadd`
- **Middleware:** `web`, `auth`, `permission:category.create`
- **Inputs:** `cat_code` (unique:categories,cat_code), `cat_name` (unique:categories,cat_name).
- **Database Queries:** `Category::create(...)`
- **Side-Effects:** Returns JSON response with created model.

#### Method 3.3: `update(Request $request)`
- **HTTP Verb & URI:** `POST /categoryedit`
- **Route Name:** `categoryedit`
- **Middleware:** `web`, `auth`, `permission:category.edit`
- **Inputs:** `id`, `cat_code`, `cat_name`.
- **Database Queries:** `Category::find(...)` with unique exception for current ID.
- **Side-Effects:** Updates `cat_code` and `cat_name`, returns JSON.

#### Method 3.4: `destroy(Request $request)`
- **HTTP Verb & URI:** `POST /categorydelete`
- **Route Name:** `categorydelete`
- **Middleware:** `web`, `auth`, `permission:category.delete`
- **Inputs:** `id`.
- **Database Queries:** Soft flag update: `$category->delete_status = 1; $category->save();`.
- **Side-Effects:** Returns JSON `{ status: 1, message: 'Category Deleted Successfully' }`.

---

### 4. Controller (`app/Http/Controllers/Controller.php`)
- **Type:** Abstract base class.
- **Methods:** None. Serves as root for framework extensibility.

---

### 5. CustomerController (`app/Http/Controllers/CustomerController.php`)
The primary regulatory engine governing the 8-step Lease Application intake wizard, document uploads, validation scrutiny, approval transitions, and cross-module promotion to Mining Plan.

#### Method 5.1: `index()`
- **HTTP Verb & URI:** `GET /application`
- **Route Name:** `application.index`
- **Middleware:** `web`, `auth`, `permission:application.view`
- **Database Queries:**
  - Eager loads `LeaseApplication::with(['customer', 'district', 'category', 'mineral', 'minerals', 'documents', 'miningApplications'])->latest()->get()`.
  - Calculates real-time KPI counts: `total`, `under_validation` (`submitted`, `under_scrutiny`, `validated`), `approved`, `needs_review` (`draft`, `revision_required`).
- **View Rendered:** `pages.lease_application.customer`
- **View Parameters:** `applications`, `kpis`.

#### Method 5.2: `step1()`
- **HTTP Verb & URI:** `GET /step1`
- **Route Name:** `step1`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries:** Active districts, customer lookup list, active minerals, reads session `lease_draft`.
- **View Rendered:** `pages.lease_application.createstep1`
- **View Parameters:** `districts`, `customers`, `minerals`, `draft`.

#### Method 5.3: `saveStep1(Request $request)`
- **HTTP Verb & URI:** `POST /step1`
- **Route Name:** `step1.save`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `customer_id`, `mimas_no`, `client_name`, `secondary_contact_person`, `company_name`, `district_id`, `mineral_ids` (array), `mineral_id`, `other_mineral_name`, `mobile_num`, `secondary_mobile_num`, `email`, `pan`, `aadhaar_no`, `gstin`, `area`, `address`, `action`/`exit`.
- **Database Queries:**
  - Resolves or creates `Customer` (with soft-delete recovery via `withTrashed()`).
  - Auto-creates or updates draft in `lease_applications` with monotonic draft number `LA-DRAFT-YYYY-NNNN`.
  - Syncs `minerals()` pivot table.
- **Side-Effects:** Saves `$draft` into session `lease_draft`, returns JSON redirecting to `/step2` or `/application`.

#### Method 5.4: `step2()`
- **HTTP Verb & URI:** `GET /step2`
- **Route Name:** `step2`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **View Rendered:** `pages.lease_application.createstep2`
- **View Parameters:** `draft`.

#### Method 5.5: `saveStep2(Request $request)`
- **HTTP Verb & URI:** `POST /step2`
- **Route Name:** `step2.save`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `contact_person`, `contact_mobile`, `secondary_contact_person`, `secondary_contact_mobile`, `mimas_user_id`, `mimas_password`, `mimas_email`, `mimas_contact`.
- **Side-Effects:**
  - Checks password placeholder `__UNCHANGED__` to preserve encrypted password.
  - Updates `LeaseApplication` contact fields.
  - Upserts `MimasCredential` linked to `lease_application_id`.
  - Updates session `lease_draft['step2']` and `lease_draft['step6']`.
  - Returns JSON redirect to `/step3`.

#### Method 5.6: `step3()`
- **HTTP Verb & URI:** `GET /step3`
- **Route Name:** `step3`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries:** `LeaseCategory::where('status', 1)->orderBy('code')->get()`.
- **View Rendered:** `pages.lease_application.createstep3`
- **View Parameters:** `categories`, `draft`.

#### Method 5.7: `saveStep3(Request $request)`
- **HTTP Verb & URI:** `POST /step3`
- **Route Name:** `step3.save`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `category_id` (required, exists in `lease_categories`).
- **Side-Effects:** Updates `LeaseApplication` category, updates session `lease_draft`, returns JSON redirect to `/step4`.

#### Method 5.8: `step4()`
- **HTTP Verb & URI:** `GET /step4`
- **Route Name:** `step4`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **View Rendered:** `pages.lease_application.createstep4`
- **View Parameters:** `draft`, `categoryName`.

#### Method 5.9: `step5(Request $request)`
- **HTTP Verb & URI:** `GET /step5`
- **Route Name:** `step5`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries:** Preloads milestone uploaded documents from `LeaseDocument` for current draft.
- **View Rendered:** `pages.lease_application.createstep5`
- **View Parameters:** `draft`, `uploadedDocs`.

#### Method 5.10: `uploadDocument(Request $request)`
- **HTTP Verb & URI:** `POST /step5/upload`
- **Route Name:** `step5.upload`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `file` (max 25MB), `doc_item`, `folder_id`, `doc_name`, `is_mandatory`.
- **Side-Effects:**
  - Uploads file to `public/uploads/lease_applications/{app_no}/`.
  - Upserts `LeaseDocument` row linked to `lease_application_id` with `status = 'uploaded'`.
  - Appends document record to session `lease_draft['uploaded_docs']`.
  - Returns JSON containing `file_url`, `file_name`, and `file_size`.

#### Method 5.11: `step6()`
- **HTTP Verb & URI:** `GET /step6`
- **Route Name:** `step6`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries:** Queries `ApplicationHandler` where `application_type = 'lease'`.
- **View Rendered:** `pages.lease_application.createstep6`
- **View Parameters:** `draft`, `handlers`.

#### Method 5.12: `saveStep6(Request $request)`
- **HTTP Verb & URI:** `POST /step6`
- **Route Name:** `step6.save`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `handlers` (array of `name`, `role`, `notes`).
- **Side-Effects:** Deletes old handlers and inserts clean records in `application_handlers`. Redirects to `/step7`.

#### Method 5.13: `step7()`
- **HTTP Verb & URI:** `GET /step7`
- **Route Name:** `step7`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries:** Preloads existing payment values from `LeaseApplication`.
- **View Rendered:** `pages.lease_application.createstep7`
- **View Parameters:** `draft`, `payment`.

#### Method 5.14: `saveStep7(Request $request)`
- **HTTP Verb & URI:** `POST /step7`
- **Route Name:** `step7.save`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `product_value`, `paid_amount`, `payment_status`.
- **Side-Effects:**
  - Calculates `pending_amount = max(0, product_value - paid_amount)`.
  - Updates `LeaseApplication` financial columns.
  - Upserts polymorphic row in `application_payments`. Redirects to `/step8`.

#### Method 5.15: `step8()`
- **HTTP Verb & URI:** `GET /step8`
- **Route Name:** `step8`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Side-Effects:** Executes `buildPreviewData($draft)` assembling full summary.
- **View Rendered:** `pages.lease_application.createstep8`
- **View Parameters:** `draft`, `previewData`.

#### Method 5.16: `resumeDraft($id)`
- **HTTP Verb & URI:** `GET /application/{id}/resume`
- **Route Name:** `application.resume`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Inputs:** `id` (Lease Application ID).
- **Database Queries:** Eager loads customer, district, category, minerals, credentials, documents, handlers.
- **Side-Effects:** Re-populates `session('lease_draft')` with persisted DB state and redirects directly to `/step` matching `current_step`.

#### Method 5.17: `buildPreviewData(array $draft)` (Private)
- Aggregates relational entities (Customer, District, Category, Minerals, Documents, Handlers, Payments) for review presentation.

#### Method 5.18: `submit(Request $request)`
- **HTTP Verb & URI:** `POST /application/submit`
- **Route Name:** `application.submit`
- **Middleware:** `web`, `auth`, `permission:application.create`
- **Database Queries / Transactions:**
  - Atomic `DB::transaction(...)`.
  - Generates official application number `LA-YYYY-NNNN` using `generateOfficialAppNumber()` with row lock.
  - Updates draft to official: sets `status = 'under_scrutiny'`, assigns `common_id = 'GTMS-YYYY-NNNN'`.
  - Creates 19 statutory `LeaseDocument` rows (marking uploaded vs pending).
  - Copies draft upload directory to official upload directory.
  - Writes audit trail in `ActivityLog`.
- **Side-Effects:** Forgets session `lease_draft`. If `move_to_mining == 1`, calls `moveToMining`. Returns JSON redirect to `/application`.

#### Method 5.19: `viewApplication(Request $request)`
- **HTTP Verb & URI:** `GET /viewapplication`
- **Route Name:** `viewapplication`
- **Middleware:** `web`, `auth`, `permission:application.view`
- **Inputs:** `id` (optional).
- **Database Queries:** Loads complete application aggregate with ordered documents and 20 recent activity logs.
- **View Rendered:** `pages.lease_application.viewapplication`
- **View Parameters:** `application`, `activityLogs`.

#### Method 5.20: `validateApplication(Request $request, $id)`
- **HTTP Verb & URI:** `POST /application/{id}/validate`
- **Route Name:** `application.validate`
- **Middleware:** `web`, `auth`, `permission:application.edit`
- **Inputs:** `action` (`pass` or `fail`), `remarks`.
- **Side-Effects:**
  - If `pass`: sets `status = 'validated'`, auto-upgrades all uploaded documents to `validated`.
  - If `fail`: sets `status = 'revision_required'`.
  - Writes `ActivityLog`.

#### Method 5.21: `updateDocumentStatus(Request $request, $id)`
- **HTTP Verb & URI:** `POST /application/document/{id}/status`
- **Route Name:** `application.document.status`
- **Middleware:** `web`, `auth`, `permission:application.edit`
- **Inputs:** `status` (`pending`, `uploaded`, `validated`, `approved`, `revision_required`), `note`.
- **Side-Effects:** Updates `LeaseDocument`, logs audit in `ActivityLog`, returns JSON with progress counters.

#### Method 5.22: `approveApplication(Request $request, $id)`
- **HTTP Verb & URI:** `POST /application/{id}/approve`
- **Route Name:** `application.approve`
- **Middleware:** `web`, `auth`, `permission:application.edit`
- **Side-Effects:** Updates `status = 'approved'`, logs activity, unlocks transition to Mining Plan.

#### Method 5.23: `rejectApplication(Request $request, $id)`
- **HTTP Verb & URI:** `POST /application/{id}/reject`
- **Route Name:** `application.reject`
- **Middleware:** `web`, `auth`, `permission:application.edit`
- **Inputs:** `reason` (required).
- **Side-Effects:** Sets `status = 'revision_required'`, `current_step = 5`, logs activity.

#### Method 5.24: `generateReport($id)`
- **HTTP Verb & URI:** `GET /application/{id}/report`
- **Route Name:** `application.report`
- **Middleware:** `web`, `auth`, `permission:application.view`
- **Side-Effects:** Renders `pages.lease_application.report_pdf`, logs activity, returns HTML stream with attachment headers.

#### Method 5.25: `logActivity(int $referenceId, string $action, string $description)` (Private)
- Inserts `ActivityLog` row with IP, User Agent, User ID, and timestamp.

#### Method 5.26: `moveToMining(Request $request, $id)`
- **HTTP Verb & URI:** `POST /application/{id}/move-to-mining`
- **Route Name:** `application.moveToMining`
- **Middleware:** `web`, `auth`, `permission:application.edit`
- **Inputs:** `id` (Lease Application ID).
- **Side-Effects:**
  - Idempotency check: returns existing link if already promoted.
  - Carries `common_id` (`GTMS-YYYY-XXXX`).
  - Creates `MiningApplication` with application number `MP-YYYY-XXXX`.
  - Clones physical uploaded files from `public/uploads/lease_applications/{LA}/` to `public/uploads/mining/{MP}/`.
  - Creates corresponding `MiningDocument` records in Folders 2 and 5.
  - Logs activities on both Lease and Mining logs. Returns redirect to `/process?id={mining_id}`.

#### Methods 5.27–5.29: Helpers
- `generateDraftAppNumber()`: Row-locked atomic generator for `LA-DRAFT-YYYY-NNNN`.
- `generateOfficialAppNumber()`: Row-locked atomic generator for `LA-YYYY-NNNN`.
- `formatFileSize(int $bytes)`: Converts bytes to human-readable B / KB / MB.

---

### 6. CustomerDirectoryController (`app/Http/Controllers/CustomerDirectoryController.php`)
Manages the central customer/quarry applicant register, unique identifier validation, input normalization, and cross-application lookup APIs.

#### Method 6.1: `index()`
- **HTTP Verb & URI:** `GET /customers`
- **Route Name:** `customers.index`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **Database Queries:** Eager loads customers with district, mineral, creator; computes KPI counts (`totalCustomers`, `activeCustomers`, `pendingCustomers`, `totalLeases`).
- **View Rendered:** `pages.customers`
- **View Parameters:** `customers`, `districts`, `minerals`, KPIs.

#### Method 6.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /customeradd`
- **Route Name:** `customeradd`
- **Middleware:** `web`, `auth`, `permission:customer.create`
- **Validation & Sanitization:** Normalizes 12-digit Aadhaar to `XXXX-XXXX-XXXX`, validates regex `/^[0-9]{4}[ -]?[0-9]{4}[ -]?[0-9]{4}$/`, checks uniqueness ignoring soft-deleted rows. Uppercases PAN and GSTIN.
- **Side-Effects:** If soft-deleted record matches, restores and updates it; otherwise creates new `Customer`. Returns JSON.

#### Method 6.3: `show(Request $request, $slug)`
- **HTTP Verb & URI:** `GET /customers/{slug}`
- **Route Name:** `customers.show`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **Database Queries:** Queries customer by `slug` or `id`, eager loading all statutory relations across 7 modules and stockpile dispatches.
- **Side-Effects:** Returns JSON for AJAX modal or renders `pages.customer_show` for full web page.

#### Method 6.4: `update(Request $request)`
- **HTTP Verb & URI:** `POST /customeredit`
- **Route Name:** `customeredit`
- **Middleware:** `web`, `auth`, `permission:customer.edit`
- **Validation & Sanitization:** Validates rules ignoring current ID. Checks for collisions against archived records.
- **Side-Effects:** Updates `Customer` attributes, returns JSON response.

#### Method 6.5: `lookupByMimas(Request $request, $mimas_no)`
- **HTTP Verb & URI:** `GET /customers/lookup-mimas/{mimas_no}`
- **Route Name:** `customers.lookup.mimas`
- **Middleware:** `web`, `auth`
- **Database Queries:** Universal lookup matching `mimas_no`, `id`, `slug`, `mimas_number`, `company_name`, `customer_name`, clean-digit normalized `mobile_num`, and clean-digit `aadhaar_no`.
- **Side-Effects:** Returns JSON `{ status: 1, data: { ...customer fields... } }` used for instant autofill across all 7 statutory wizards.

#### Method 6.6: `destroy(Request $request)`
- **HTTP Verb & URI:** `POST /customerdelete`
- **Route Name:** `customerdelete`
- **Middleware:** `web`, `auth`, `permission:customer.delete`
- **Integrity Guard:** Checks active foreign dependencies across `leaseApplications`, `miningApplications`, `environmentProjects`, `dgpsSurveys`, and `stockpiles`. Aborts with 422 if dependencies exist.
- **Side-Effects:** Soft-deletes `Customer` (`delete()`), returns JSON.

---

### 7. CustomerTrackingController (`app/Http/Controllers/CustomerTrackingController.php`)
Enterprise Customer 360 Tracking Hub, multi-faceted filtering, live autocomplete search, and commercial Proforma / Tax invoice generation.

#### Method 7.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /customer-tracking`
- **Route Name:** `customer-tracking.index`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **Inputs:** `q`, `district_id`, `app_type`, `date_from`, `date_to`.
- **Database Queries:** Queries `Customer` with relation counts across all 8 modules, applying multi-faceted filter scoping.
- **View Rendered:** `pages.customer_tracking.index`
- **View Parameters:** `customer`, `query`, `districts`, `appTypes`, `stats`, `customersList`, `dossierData`.

#### Method 7.2: `show($customer)`
- **HTTP Verb & URI:** `GET /customer-tracking/{customer}`
- **Route Name:** `customer-tracking.show`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **Side-Effects:** Resolves customer via `resolveCustomer($customer)` and builds comprehensive dossier data.

#### Method 7.3: `search(Request $request)`
- **HTTP Verb & URI:** `GET /customer-tracking/search`
- **Route Name:** `customer-tracking.search`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **Inputs:** `q` (minimum 2 characters).
- **Database Queries:** Normalized bidirectional search matching raw digits, hyphens, spaces, PAN, MIMAS, mobile (+91), and child application numbers. Returns top 8 autocomplete candidates.

#### Method 7.4: `findCustomerByUniversalQuery(string $q)` (Private)
- Direct single-entity resolver for query strings matching primary identifiers.

#### Method 7.5: `buildCustomerDossier(Customer $customer)` (Private)
- Assembles full 5-pillar lifecycle metrics, 4 module overview cards, all application handlers, payments, and 8-module Document Vault.

#### Method 7.6: `proformaInvoice($customer)`
- **HTTP Verb & URI:** `GET /customer-tracking/{customer}/proforma-invoice`
- **Route Name:** `customer-tracking.proforma-invoice`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **View Rendered:** `pages.customer_tracking.proforma_invoice`
- **View Parameters:** Output from `buildInvoiceData($customer, 'proforma')`.

#### Method 7.7: `taxInvoice($customer)`
- **HTTP Verb & URI:** `GET /customer-tracking/{customer}/tax-invoice`
- **Route Name:** `customer-tracking.tax-invoice`
- **Middleware:** `web`, `auth`, `permission:customer.view`
- **View Rendered:** `pages.customer_tracking.tax_invoice`
- **View Parameters:** Output from `buildInvoiceData($customer, 'tax')`.

#### Method 7.8: `resolveCustomer($customer)` (Protected)
- Model binder resolving customer from instance, slug, numeric ID, MIMAS number, or normalized 12-digit Aadhaar.

#### Method 7.9: `buildInvoiceData(Customer $customer, string $type)` (Protected)
- Aggregates active application fees across all 7 modules, maps SAC codes (`998341`, `998342`, `998343`, `998349`, `998311`), computes CGST (9%), SGST (9%), Grand Total, and formats currency words.

#### Methods 7.10–7.11: `numberToWords(float $number)` & `convertTwoDigits` (Protected/Private)
- Converts numbers into formal Indian statutory words (Crores, Lakhs, Thousands, Hundreds, Rupees, and Paise).

---

### 8. DgpsSurveyController (`app/Http/Controllers/DgpsSurveyController.php`)
DGPS differential GPS field survey management, boundary coordinate sheets, and RINEX data repository.

#### Method 8.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /dgps-survey`
- **Route Name:** `dgps-survey.index`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **Database Queries:** Queries `DgpsSurvey::with(['customer', 'leaseApplication', 'documents'])`, filters by `search` and `status`, calculates real KPI counts (`totalRequests`, `fieldSurveyCount`, `reportsReadyCount`, `gtmUploadedCount`).
- **View Rendered:** `pages.dgps_survey.index`.

#### Method 8.2: `wizard(int $step, Request $request)`
- **HTTP Verb & URI:** `GET /dgps-survey/step/{step}`
- **Route Name:** `dgps-survey.step`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **Inputs:** `step` (1–8), `resume` (optional ID).
- **View Rendered:** `pages.dgps_survey.wizard`
- **View Parameters:** `step`, `draft`, `customers`, `leaseApps`.

#### Method 8.3: `saveStep(int $step, Request $request)`
- **HTTP Verb & URI:** `POST /dgps-survey/step/{step}`
- **Route Name:** `dgps-survey.saveStep`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **Side-Effects:** Saves wizard state to session `dgps_wizard`, calculates financial balances, redirects to next step.

#### Method 8.4: `store(Request $request)`
- **HTTP Verb & URI:** `POST /dgps-survey`
- **Route Name:** `dgps-survey.store`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **Database Queries / Transactions:**
  - Wraps execution in `DB::beginTransaction()`.
  - Creates `DgpsSurvey` with generated survey number `DGPS-YYYY-NNNN`.
  - Persists handlers to `application_handlers` (`application_type = 'dgps'`).
  - Persists payments to `application_payments` (`application_type = 'dgps'`).
  - Forgets session `dgps_wizard`.
- **Side-Effects:** Redirects to `dgps-survey.show`.

#### Method 8.5: `show($id)`
- **HTTP Verb & URI:** `GET /dgps-survey/{id}`
- **Route Name:** `dgps-survey.show`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **View Rendered:** `pages.dgps_survey.show`
- **View Parameters:** `survey` (with customer, lease application, handlers, payments, documents).

#### Method 8.6: `uploadDocument(Request $request)`
- **HTTP Verb & URI:** `POST /dgps-survey/upload`
- **Route Name:** `dgps-survey.upload`
- **Middleware:** `web`, `auth`, `permission:dgps.view`
- **Inputs:** `file` (max 50MB), `survey_id`, `document_name`.
- **Side-Effects:** Stores file in `public/uploads/dgps/`, inserts `SurveyDocument`, returns JSON.

---

### 9. DroneSurveyController (`app/Http/Controllers/DroneSurveyController.php`)
UAV aerial photogrammetry and 3D volumetric computation tracking.

#### Method 9.1: `index()`
- **HTTP Verb & URI:** `GET /drone-survey`
- **Route Name:** `drone-survey.index`
- **Middleware:** `web`, `auth`, `permission:drone.view`
- **View Rendered:** `pages.drone_survey.index`.

#### Method 9.2: `wizard(int $step, Request $request)`
- **HTTP Verb & URI:** `GET /drone-survey/step/{step}`
- **Route Name:** `drone-survey.step`
- **Middleware:** `web`, `auth`, `permission:drone.view`
- **Inputs:** `step` (1–8).
- **Database Queries:** Active customers and districts.
- **View Rendered:** `pages.drone_survey.wizard`
- **View Parameters:** `step`, `draft`, `customers`, `districts`.

---

### 10. EcCertificateController (`app/Http/Controllers/EcCertificateController.php`)
State Environmental Impact Assessment Authority (SEIAA) certificate issuance state machine and authentic printable order generator.

#### Method 10.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /ec-certificate`
- **Route Name:** `ec-certificate.index`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries:** Eager loads certificates with project and customer; computes KPIs (`total_projects`, `approved`, `ready_download`, `issued`, `communicated`).
- **View Rendered:** `pages.ec_certificate.index`
- **View Parameters:** `certificates`, `kpis`.

#### Method 10.2: `show(int $id)`
- **HTTP Verb & URI:** `GET /ec-certificate/{id}`
- **Route Name:** `ec-certificate.show`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **View Rendered:** `pages.ec_certificate.show` (Authentic official letterhead view with QR validation seal).
- **View Parameters:** `certificate`.

#### Method 10.3: `wizard(int $step, Request $request)`
- **HTTP Verb & URI:** `GET /ec-certificate/step/{step}`
- **Route Name:** `ec-certificate.step`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Step Gating:** Enforces `$this->getMaxUnlockedStep($draft)` to prevent deep-linking skips.
- **View Rendered:** `pages.ec_certificate.wizard`.

#### Method 10.4: `getMaxUnlockedStep(array $draft)` (Protected)
- Inspects session `ec_wizard` for completion keys (`step1.completed`, `step2`, `step3.preview_verified`, `step4.storage_confirmed`, `step5.recipient_email`) to return maximum permissible step (1–8).

#### Method 10.5: `saveStep(Request $request, int $step)`
- **HTTP Verb & URI:** `POST /ec-certificate/step/{step}`
- **Route Name:** `ec-certificate.saveStep`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Side-Effects:** Symmetrically guarded by `$maxUnlockedStep`. Handles file uploads to `uploads/ec_certificates/{project_code}/`, unlinking superseded files. For Step 8, delegates to `store()`.

#### Method 10.6: `store(Request $request)`
- **HTTP Verb & URI:** `POST /ec-certificate`
- **Route Name:** `ec-certificate.store`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries / Transactions:**
  - Wraps execution in `DB::transaction(...)`.
  - Calculates `expiry_date = Carbon::parse(issue_date)->addYears(validity_years)`.
  - Upserts `EcCertificate` row (`ec_ref_no` unique).
  - Persists handlers to `application_handlers` (`application_type = 'ec'`).
  - Persists payment to `application_payments` (`application_type = 'ec'`).
  - Sets linked `EnvironmentProject` status to `approved`.
  - Writes audit in `ActivityLog` and clears session `ec_wizard`.
- **Side-Effects:** Redirects to `ec-certificate.index`.

---

### 11. EcComplianceController (`app/Http/Controllers/EcComplianceController.php`)
Statutory half-yearly environmental compliance monitoring, NABL lab test tracking, and MoEFCC Parivesh portal submission.

#### Method 11.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /ec-compliance`
- **Route Name:** `ec-compliance.index`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries:** Queries `EcCompliance` with relations; computes KPIs (`totalCount`, `labStageCount`, `uploadedCount`, `completeCount`).
- **View Rendered:** `pages.ec_compliance.index`.

#### Method 11.2: `wizard(int $step, Request $request)`
- **HTTP Verb & URI:** `GET /ec-compliance/step/{step}`
- **Route Name:** `ec-compliance.step`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **View Rendered:** `pages.ec_compliance.wizard`
- **View Parameters:** `step`, `draft`, `customers`, `districts`, `minerals`, `envProjects`, `ecCertificates`, statutory checklist (`docs19`, `labs4`, `report3`).

#### Method 11.3: `saveStep(int $step, Request $request)`
- **HTTP Verb & URI:** `POST /ec-compliance/step/{step}`
- **Route Name:** `ec-compliance.saveStep`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Side-Effects:** Saves wizard state to session `ec_compliance_wizard`, redirects to next step.

#### Method 11.4: `store(Request $request)`
- **HTTP Verb & URI:** `POST /ec-compliance`
- **Route Name:** `ec-compliance.store`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries / Transactions:**
  - Wraps execution in `DB::transaction(...)`.
  - Creates `EcCompliance` with sequence number `HYC-YYYY-NNNN`.
  - Persists handlers in `application_handlers` (`application_type = 'ec_compliance'`).
  - Persists payment in `application_payments` (`application_type = 'ec_compliance'`).
  - Clears session `ec_compliance_wizard`.
- **Side-Effects:** Redirects to `ec-compliance.show`.

#### Method 11.5: `show($id)`
- **HTTP Verb & URI:** `GET /ec-compliance/{id}`
- **Route Name:** `ec-compliance.show`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **View Rendered:** `pages.ec_compliance.show`.

#### Method 11.6: `uploadDocument(Request $request)`
- **HTTP Verb & URI:** `POST /ec-compliance/upload`
- **Route Name:** `ec-compliance.upload`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Inputs:** `file` (max 50MB), `compliance_id`, `folder_category`, `document_name`.
- **Side-Effects:** Moves file to `public/uploads/compliance/`, creates `EcComplianceDocument`, returns JSON.

---

### 12. EnverionsoneController (`app/Http/Controllers/EnverionsoneController.php`)
Unified Environment Clearance Controller governing both Category B1 (Sequential 2-Stage ToR/EIA with PPT Gates) and Category B2 (Direct 6 Folders).

#### Method 12.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /eviron`
- **Route Name:** `eviron.index`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries:** Queries `EnvironmentProject` with KPIs (`total`, `b1_count`, `b2_count`, `approved`, `in_progress`, `doc_uploaded`, `doc_approved`, `ec_issued`).
- **View Rendered:** `pages.eviron.index`.

#### Method 12.2: `create(Request $request)`
- **HTTP Verb & URI:** `GET /eviron/create`
- **Route Name:** `eviron.create`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Inputs:** `step`, `category`, `sub_category`.
- **View Rendered:** `pages.eviron.create` (Category B1 presents exclusively Sub Category 1 intake).

#### Method 12.3: `store(Request $request)`
- **HTTP Verb & URI:** `POST /eviron`
- **Route Name:** `eviron.store`
- **Middleware:** `web`, `auth`, `permission:environment.b2.create`
- **Validation:** B1 enforces `sub_category = 'SC1'`.
- **Database Queries / Transactions:**
  - `DB::transaction(...)`.
  - Atomic code generation: `ENV-{CAT}-{YEAR}-{SEQ}` with row lock.
  - Resolves or creates `Customer`.
  - Creates `EnvironmentProject` with `b1_stage = 'sc1_prep'` (for B1).
  - Persists polymorphic handlers and payments.
  - Generates document checklist slots via `autoGenerateDocumentSlots`.
  - Logs `ActivityLog`.
- **Side-Effects:** Redirects to `eviron.show`.

#### Method 12.4: `show(int $id)`
- **HTTP Verb & URI:** `GET /eviron/{id}`
- **Route Name:** `eviron.show`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries:** Loads project, dynamic folders (`project->folder_names`), documents grouped by folder, PPT stage relations.
- **View Rendered:** `pages.eviron.show` (Renders 4-stage B1 visual stepper or 6-folder B2 view).

#### Method 12.5: `uploadDocument(Request $request, int $id, int $document)`
- **HTTP Verb & URI:** `POST /eviron/{id}/documents/{document}/upload`
- **Route Name:** `eviron.documents.upload`
- **Middleware:** `web`, `auth`, `permission:environment.b2.upload`
- **Inputs:** `file` (max 25MB).
- **Side-Effects:** Saves file to `public/storage/environment/{project_code}/`, updates `EnvironmentDocument` status to `uploaded`.

#### Method 12.6: `addDocument(Request $request, int $id)`
- **HTTP Verb & URI:** `POST /eviron/{id}/documents/add`
- **Route Name:** `eviron.documents.add`
- **Middleware:** `web`, `auth`, `permission:environment.b2.upload`
- **Inputs:** `folder_id`, `document_name`, `file`.
- **Side-Effects:** Creates ad-hoc custom `EnvironmentDocument` row with `document_field_id = null`.

#### Method 12.7: `reviewDocument(Request $request, int $id, int $document)`
- **HTTP Verb & URI:** `POST /eviron/{id}/documents/{document}/review`
- **Route Name:** `eviron.documents.review`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Inputs:** `status` (`approved`, `revision_required`), `review_note`.
- **Side-Effects:** Updates document status, logs review timestamp and user.

#### Method 12.8: `updateStatus(Request $request, int $id)`
- **HTTP Verb & URI:** `POST /eviron/{id}/status`
- **Route Name:** `eviron.status`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Side-Effects:** Updates project status (`draft`, `validation`, `approved`, `reported`, `archived`), auto-upgrades uploaded documents to `validated` upon approval.

#### Method 12.9: `downloadDocument(int $document)`
- **HTTP Verb & URI:** `GET /eviron/documents/{document}/download`
- **Route Name:** `eviron.documents.download`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Side-Effects:** Validates disk file existence before triggering `Storage::disk('public')->download(...)`.

#### Methods 12.10–12.11: `index1()` & `index2()`
- **HTTP Verb & URI:** `GET /environstage1` & `GET /environstage2`
- **Route Name:** `environstage1` & `environstage2`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Side-Effects:** Backward-compatible redirect handlers routing legacy stage URLs to unified `eviron.show`.

#### Method 12.12: `submitSc1ToPpt(int $id)`
- **HTTP Verb & URI:** `POST /eviron/{id}/submit-sc1-ppt`
- **Route Name:** `eviron.submitSc1ToPpt`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Side-Effects:**
  - Creates linked `PptApplication` with `presentation_stage = 'tor_presentation'`.
  - Sets project `ppt_stage_1_id`, transitions `b1_stage = 'sc1_ppt_review'`, `status = 'validation'`.
  - Logs `sc1_submitted_to_ppt` in `ActivityLog`.

#### Method 12.13: `submitSc2ToPpt(int $id)`
- **HTTP Verb & URI:** `POST /eviron/{id}/submit-sc2-ppt`
- **Route Name:** `eviron.submitSc2ToPpt`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Side-Effects:**
  - Creates linked `PptApplication` with `presentation_stage = 'final_ec_presentation'`.
  - Sets project `ppt_stage_2_id`, transitions `b1_stage = 'sc2_ppt_review'`, `status = 'validation'`.
  - Logs `sc2_submitted_to_ppt` in `ActivityLog`.

#### Methods 12.14–12.15: `autoGenerateDocumentSlots` & `generateSlots`
- Dynamically iterates over statutory folder definitions for project category/sub-category and creates pending `EnvironmentDocument` placeholder slots without duplication.

---

### 13. EnvironmentalB2Controller (`app/Http/Controllers/EnvironmentalB2Controller.php`)
Dedicated Category B2 direct clearance workflow and dossier management.

#### Method 13.1: `index()`
- **HTTP Verb & URI:** `GET /environment-b2`
- **Route Name:** `environment-b2.index`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Database Queries:** Queries `EnvironmentProject::where('category', 'B2')` with KPIs.
- **View Rendered:** `pages.enviro_b2.index`.

#### Method 13.2: `wizard(int $step)`
- **HTTP Verb & URI:** `GET /environment-b2/step/{step}`
- **Route Name:** `environment-b2.step`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **View Rendered:** `pages.enviro_b2.wizard`.

#### Method 13.3: `store(Request $request)`
- **HTTP Verb & URI:** `POST /environment-b2`
- **Route Name:** `environment-b2.store`
- **Middleware:** `web`, `auth`, `permission:environment.b2.create`
- **Database Queries / Transactions:**
  - `DB::transaction(...)`.
  - Resolves Customer.
  - Generates code `ENV-B2-YYYY-NNNN`.
  - Creates project and auto-generates 6-folder B2 document checklist (`module_id = 3`, `sort_order <= 6`).
- **Side-Effects:** Redirects to `environment-b2.show`.

#### Method 13.4: `show(EnvironmentProject $project)`
- **HTTP Verb & URI:** `GET /environment-b2/{project}`
- **Route Name:** `environment-b2.show`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **View Rendered:** `pages.enviro_b2.show`.

#### Method 13.5: `upload(Request $request, EnvironmentDocument $document)`
- **HTTP Verb & URI:** `POST /environment-b2/documents/{document}/upload`
- **Route Name:** `environment-b2.documents.upload`
- **Middleware:** `web`, `auth`, `permission:environment.b2.upload`
- **Side-Effects:** Moves file to `public/uploads/environment-b2/{project_code}/`, unlinks old file, updates document row, logs activity.

#### Method 13.6: `review(Request $request, EnvironmentDocument $document)`
- **HTTP Verb & URI:** `POST /environment-b2/documents/{document}/review`
- **Route Name:** `environment-b2.documents.review`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Inputs:** `status` (`validated`, `approved`, `revision_required`), `review_note`.
- **Side-Effects:** Updates document status and review note.

#### Method 13.7: `updateStatus(Request $request, EnvironmentProject $project)`
- **HTTP Verb & URI:** `POST /environment-b2/{project}/status`
- **Route Name:** `environment-b2.status`
- **Middleware:** `web`, `auth`, `permission:environment.b2.review`
- **Side-Effects:** Updates project status (`draft`, `validation`, `approved`, `reported`, `archived`) with document existence guards.

#### Method 13.8: `download(EnvironmentDocument $document)`
- **HTTP Verb & URI:** `GET /environment-b2/documents/{document}/download`
- **Route Name:** `environment-b2.documents.download`
- **Middleware:** `web`, `auth`, `permission:environment.view`
- **Side-Effects:** Returns file download stream.

---

### 14. MiningController (`app/Http/Controllers/MiningController.php`)
Mining Plan Preparation, statutory 6-folder dossier management, Process Flow Stages 6.1–6.5, and promotion to Environment Clearance.

#### Method 14.1: `index()`
- **HTTP Verb & URI:** `GET /miningplan`
- **Route Name:** `miningplan.index`
- **Middleware:** `web`, `auth`, `permission:mining.view`
- **Database Queries:** Queries `MiningApplication` with relations; computes KPIs (`totalApplications`, `draftCount`, `approvedCount`, `scrutinyCount`).
- **View Rendered:** `pages.mining-portal.index`.

#### Method 14.2: `newApplication(Request $request)`
- **HTTP Verb & URI:** `GET /newapplication`
- **Route Name:** `newapplication`
- **Middleware:** `web`, `auth`, `permission:mining.create`
- **Inputs:** `resume` (Mining Plan ID), `lease_id` (Lease Application ID).
- **Database Queries:** Preloads customers, districts, minerals, nature of works, plan types, applicant types, document folder fields.
- **Side-Effects:** Populates `$prefillData` with carried data, survey numbers, minerals, and existing documents.
- **View Rendered:** `pages.mining-portal.newapplication`.

#### Method 14.3: `store(Request $request)`
- **HTTP Verb & URI:** `POST /newapplication`
- **Route Name:** `newapplication.store`
- **Middleware:** `web`, `auth`, `permission:mining.create`
- **Database Queries / Transactions:**
  - Generates atomic application number `MP-YYYY-NNNN` and `GTMS-YYYY-NNNN` via `generateMiningAppNumber()`.
  - Creates or updates `MiningApplication`.
  - Persists payments to `application_payments` and handlers to `application_handlers`.
  - Syncs multi-mineral pivot table `mining_application_minerals`.
  - Moves uploaded statutory files to `public/uploads/mining/{app_no}/`.
  - Upserts `MiningDocument` records in place without duplication.
  - Handles custom documents uploaded in Step 6.
- **Side-Effects:** Redirects to `projectfolder` with application ID.

#### Method 14.4: `projectFolder(Request $request)`
- **HTTP Verb & URI:** `GET /projectfolder`
- **Route Name:** `projectfolder`
- **Middleware:** `web`, `auth`, `permission:mining.view`
- **Inputs:** `id` (optional).
- **Side-Effects:** If `id` provided, renders single application dossier with folder statistics and progress bars; otherwise renders master table of all applications with folder progress.
- **View Rendered:** `pages.mining-portal.projectfolder`.

#### Method 14.5: `Document(Request $request)`
- **HTTP Verb & URI:** `GET /document`
- **Route Name:** `document`
- **Middleware:** `web`, `auth`, `permission:mining.view`
- **Inputs:** `id` (required), `folder` (optional).
- **View Rendered:** `pages.mining-portal.document` (6-folder file management console).

#### Method 14.6: `uploadDocument(Request $request)`
- **HTTP Verb & URI:** `POST /mining/document/upload`
- **Route Name:** `mining.document.upload`
- **Middleware:** `web`, `auth`, `permission:mining.create`
- **Inputs:** `mining_application_id`, `document_id`, `file` (max 25MB).
- **Side-Effects:** Saves file to `public/uploads/mining/{app_no}/`, updates `MiningDocument` status to `uploaded`.

#### Method 14.7: `Process(Request $request)`
- **HTTP Verb & URI:** `GET /process`
- **Route Name:** `process`
- **Middleware:** `web`, `auth`, `permission:mining.view`
- **Inputs:** `id` (optional).
- **Side-Effects:** If `id` provided, renders Process Flow Scrutiny console with validation statistics and unified lifecycle activity logs; otherwise renders table of all applications.
- **View Rendered:** `pages.mining-portal.process`.

#### Method 14.8: `validateDocument(Request $request, $id)`
- **HTTP Verb & URI:** `POST /mining/document/{id}/validate`
- **Route Name:** `mining.document.validate`
- **Middleware:** `web`, `auth`, `permission:mining.edit`
- **Inputs:** `action` (`pass` or `fail`), `review_note`.
- **Side-Effects:** Updates `MiningDocument` status to `validated` or `revision_required`.

#### Method 14.9: `advanceStage(Request $request, $id)`
- **HTTP Verb & URI:** `POST /mining/application/{id}/stage`
- **Route Name:** `mining.application.stage`
- **Middleware:** `web`, `auth`, `permission:mining.edit`
- **Inputs:** `stage` (`6.1`, `6.2`, `6.3`, `6.4`, `6.5`).
- **Side-Effects:** Updates application stage and maps status (`draft`, `scrutiny`, `approved`, `archived`).

#### Method 14.10: `generateMiningAppNumber()` (Private)
- Row-locked atomic sequence generator querying both `MP-` and legacy `MDG-` records, returning `[$applicationNo, $commonId]`.

#### Method 14.11: `moveToEnvironment(Request $request, $id)`
- **HTTP Verb & URI:** `POST /mining/application/{id}/move-to-environment`
- **Route Name:** `mining.application.moveToEnvironment`
- **Middleware:** `web`, `auth`, `permission:mining.edit`
- **Side-Effects:**
  - Idempotency guard: checks if linked `EnvironmentProject` already exists.
  - Generates atomic code `ENV-{CAT}-{YEAR}-{SEQ}`.
  - Creates `EnvironmentProject` linked via `mining_application_id`.
  - Automatically clones verified statutory documents into `public/uploads/environment-b2/{code}/`.
  - Auto-generates document checklist and logs activities. Redirects to `environment-b2.show`.

---

### 15. PptDepartmentController (`app/Http/Controllers/PptDepartmentController.php`)
District Expert Appraisal Committee presentation gates, presentation agenda management, and statutory clearance approvals.

#### Method 15.1: `index(Request $request)`
- **HTTP Verb & URI:** `GET /ppt-department`
- **Route Name:** `ppt-department.index`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Database Queries:** Queries `PptApplication` with customer, district, mineral, documents; computes KPIs (`totalCount`, `validationCount`, `approvedCount`, `archivedCount`).
- **View Rendered:** `pages.ppt_department.index`.

#### Method 15.2: `wizard(int $step, Request $request)`
- **HTTP Verb & URI:** `GET /ppt-department/step/{step}`
- **Route Name:** `ppt-department.step`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Inputs:** `step` (1–9), `resume` (optional ID).
- **View Rendered:** `pages.ppt_department.wizard`.

#### Method 15.3: `saveStep(int $step, Request $request)`
- **HTTP Verb & URI:** `POST /ppt-department/step/{step}`
- **Route Name:** `ppt-department.saveStep`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Side-Effects:** Persists step to session `ppt_wizard`, redirects to next step.

#### Method 15.4: `store(Request $request)`
- **HTTP Verb & URI:** `POST /ppt-department`
- **Route Name:** `ppt-department.store`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Database Queries / Transactions:**
  - Wraps in `DB::beginTransaction()`.
  - Creates `PptApplication` with number `PPT-YYYY-NNNN`.
  - Saves handlers and payments to polymorphic tables.
  - Clears session `ppt_wizard`.
- **Side-Effects:** Redirects to `ppt-department.show`.

#### Method 15.5: `show($id)`
- **HTTP Verb & URI:** `GET /ppt-department/{id}`
- **Route Name:** `ppt-department.show`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **View Rendered:** `pages.ppt_department.show`.

#### Method 15.6: `uploadDocument(Request $request)`
- **HTTP Verb & URI:** `POST /ppt-department/upload`
- **Route Name:** `ppt-department.upload`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Inputs:** `file` (max 25MB), `application_id`, `folder_id`, `document_name`.
- **Side-Effects:** Saves to `public/uploads/ppt/`, creates `PptDocument`, returns JSON.

#### Method 15.7: `approvePresentation(Request $request, int $id)`
- **HTTP Verb & URI:** `POST /ppt-department/{id}/approve-stage`
- **Route Name:** `ppt-department.approveStage`
- **Middleware:** `web`, `auth`, `permission:ppt.view`
- **Core Approval Gate Logic:**
  - Sets `PptApplication` status to `approved`.
  - If linked to Category B1 `EnvironmentProject`:
    - Case `tor_presentation`: Advances project to `sub_category = 'SC2'`, `b1_stage = 'sc2_prep'`, `status = 'draft'`. Calls `EnverionsoneController::generateSlots($project)` unlocking the 6 EIA folders.
    - Case `final_ec_presentation`: Advances project to `b1_stage = 'completed'`, `status = 'approved'`, ready for EC Certificate issuance!

---

### 16. ProductController (`app/Http/Controllers/ProductController.php`)
Mineral product master catalog.

#### Method 16.1: `index()`
- **HTTP Verb & URI:** `GET /product`
- **Route Name:** `product.index`
- **Middleware:** `web`, `auth`, `permission:product.view`
- **View Rendered:** `pages.master.product.index` with `product`, `category`, `branch`.

#### Method 16.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /productadd`
- **Route Name:** `productadd`
- **Middleware:** `web`, `auth`, `permission:product.create`
- **Side-Effects:** Creates `Product`, generates barcode `PRO_NNN`, initializes `ProductStock` record, returns JSON.

---

### 17. ProductStockController (`app/Http/Controllers/ProductStockController.php`)
Mineral inventory and stockpile balance register.

#### Method 17.1: `index()`
- **HTTP Verb & URI:** `GET /productstock`
- **Route Name:** `productstock.index`
- **Middleware:** `web`, `auth`, `permission:product.view`
- **View Rendered:** `pages.master.productstock.index` with eager loaded `productstock.product`.

---

### 18. RolesController (`app/Http/Controllers/RolesController.php`)
Spatie RBAC role hierarchy and permission matrix manager.

#### Method 18.1: `index()`
- **HTTP Verb & URI:** `GET /roles`
- **Route Name:** `roles.index`
- **Middleware:** `web`, `auth`, `permission:roles.view`
- **Database Queries:** Queries roles with permissions, all permissions ordered, groups permissions by module prefix.
- **View Rendered:** `pages.authentication.roles.index`.

#### Method 18.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /roleadd`
- **Route Name:** `roleadd`
- **Middleware:** `web`, `auth`, `permission:roles.create`
- **Side-Effects:** Creates `Role`, syncs permissions, clears Spatie cached permissions (`forgetCachedPermissions()`), returns JSON.

#### Method 18.3: `getPermissions($id)`
- **HTTP Verb & URI:** `GET /roles/{id}/permissions`
- **Route Name:** `roles.permissions`
- **Middleware:** `web`, `auth`, `permission:roles.view`
- **Side-Effects:** Returns JSON `{ status: 1, role: $role, permissions: [...] }`.

#### Method 18.4: `update(Request $request)`
- **HTTP Verb & URI:** `POST /roleupdate`
- **Route Name:** `roleupdate`
- **Middleware:** `web`, `auth`, `permission:roles.edit`
- **Side-Effects:** Updates role name, syncs permissions array, flushes permission cache, returns JSON.

#### Method 18.5: `destroy(Request $request)`
- **HTTP Verb & URI:** `POST /roledelete`
- **Route Name:** `roledelete`
- **Middleware:** `web`, `auth`, `permission:roles.delete`
- **Integrity Guard:** Forbids deletion of default administrative roles (`Admin`, `Super Admin`).
- **Side-Effects:** Deletes role, clears permission cache, returns JSON.

---

### 19. UnitController (`app/Http/Controllers/UnitController.php`)
Measurement unit master register (Tonnes, CBM, Acres, Hectares).

#### Method 19.1: `index()`
- **HTTP Verb & URI:** `GET /unit`
- **Route Name:** `unit.index`
- **Middleware:** `web`, `auth`, `permission:unit.view`
- **Database Queries:** `Unit::where('delete_status', 0)->get()`.
- **View Rendered:** `pages.master.unit.index` with `units`.

---

### 20. UserController (`app/Http/Controllers/UserController.php`)
Staff and administrative user management with avatar image processing, User ID code generation, and role synchronization.

#### Method 20.1: `index()`
- **HTTP Verb & URI:** `GET /user`
- **Route Name:** `user.index`
- **Middleware:** `web`, `auth`, `permission:users.view`
- **Database Queries:** `User::with(['role', 'roles', 'branch'])->get()`, `Role::with('permissions')->get()`, `Branch::where('status', 1)->get()`.
- **View Rendered:** `pages.authentication.users.index`.

#### Method 20.2: `store(Request $request)`
- **HTTP Verb & URI:** `POST /useradd`
- **Route Name:** `useradd`
- **Middleware:** `web`, `auth`, `permission:users.create`
- **Inputs:** `name`, `email`, `password`, `role_id`, `branch_id`, `mobile_num`, `image` (max 2MB).
- **Side-Effects:**
  - Hashes password (`bcrypt(...)`).
  - Stores uploaded image in `public/uploads/users/`.
  - Saves user, then generates User Code `LUK_001` based on auto-increment ID.
  - Syncs Spatie role name.
  - Returns JSON with created user.

#### Method 20.3: `update(Request $request)`
- **HTTP Verb & URI:** `POST /useredit`
- **Route Name:** `useredit`
- **Middleware:** `web`, `auth`, `permission:users.edit`
- **Inputs:** `id`, `name`, `email`, `password` (nullable), `role_id`, `branch_id`, `status`, `mobile_num`, `image`.
- **Side-Effects:** Unlinks old image if replaced, updates user attributes, syncs Spatie role, returns JSON.

#### Method 20.4: `destroy(Request $request)`
- **HTTP Verb & URI:** `POST /userdelete`
- **Route Name:** `userdelete`
- **Middleware:** `web`, `auth`, `permission:users.delete`
- **Integrity Guards:**
  - Prevents deleting own account (`auth()->id() == $user->id`).
  - Prevents deleting the last remaining Admin account.
- **Side-Effects:** Unlinks avatar image from disk, deletes user record, returns JSON.

---
*End of Document 05 — HTTP Controllers Specification & Audit*
