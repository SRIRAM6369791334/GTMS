# 📘 GTMS Enterprise — Page-Wise CRUD, Form & Architecture Blueprint
**Version:** 1.0.4 Enterprise Edition  
**Framework:** Laravel 11 / PHP 8.2+ / MySQL / Spatie Permission RBAC / DataTables  
**Document Purpose:** Exhaustive manual audit of every Blade template, form element, field attribute, validation rule, data saving mechanism, and underlying architectural safeguards across all 84 Blade views.

---

## 📑 Table of Contents
1. [Authentication & System Status](#1-authentication--system-status)
2. [Executive Cockpit & Dashboard](#2-executive-cockpit--dashboard)
3. [Users Management (`/user`)](#3-users-management-user)
4. [Roles & RBAC Permission Matrix (`/roles`)](#4-roles--rbac-permission-matrix-roles)
5. [Departments & Regional Branches (`/branch`)](#5-departments--regional-branches-branch)
6. [Customer Directory & 360 Tracking (`/customers`, `/customer-tracking`)](#6-customer-directory--360-tracking)
7. [Master Masters (Categories, Units, Products, Stock)](#7-master-tables-categories-units-products-stock)
8. [Statutory Lease Application Wizard (Steps 1–8 & Dossier)](#8-statutory-lease-application-wizard-steps-18--dossier)
9. [Mining Plan Portal](#9-mining-plan-portal)
10. [Environment Clearance (EC Unified & B1/B2)](#10-environment-clearance-ec-unified--b1b2)
11. [Technical Operations: DGPS & Drone Surveys](#11-technical-operations-dgps--drone-surveys)
12. [Presentation & Compliance (PPT, EC Certificate, Half-Yearly)](#12-presentation--compliance-ppt-ec-certificate-half-yearly)
13. [Accounts & Financial Management (`/accounts/*`)](#13-accounts--financial-management-accounts)
14. [Cross-Departmental Master Pipeline & Real-Time Notifications](#14-cross-departmental-master-pipeline--real-time-notifications)
15. [Underlying Enterprise Safeguards & "Things You Might Miss"](#15-underlying-enterprise-safeguards--things-you-might-miss)

---

## 1. Authentication & System Status

### 1.1 Login Page
* **Route:** `GET /login` or `GET /` (`login`)
* **Blade File:** `resources/views/pages/login.blade.php`
* **Controller Action:** `App\Http\Controllers\AuthController@showLogin`
* **Form Action & Method:** `POST /login` (`login.post`) | Throttled `throttle:15,1`
* **Form Fields:**
  | Field Name | Type | Label | Options / Rules | Required |
  | :--- | :--- | :--- | :--- | :--- |
  | `_token` | hidden | CSRF Token | Automatic session token | Yes |
  | `email` | text | Email / Personnel ID | Canonical Email (`admin@gtms.com`) or User Code (`LUK_001`) | Yes |
  | `password` | password | Password | Plain text input, verified via `Hash::check()` | Yes |
  | `remember` | checkbox | Remember Me | 30-day session persistent cookie | No |
* **Data Saving / Authentication Method:**
  * Handled by `AuthController@login`.
  * Checks if input is email or `user_code`. Queries `User::where('email', $login)->orWhere('user_code', $login)->first()`.
  * **Critical Safeguard:** Checks `$user->status == 1`. Inactive accounts are immediately rejected with error: *"Your account has been deactivated. Please contact your system administrator."*
  * Uses `Auth::login($user, $remember)` and `request()->session()->regenerate()`.
  * Redirects authenticated user to `route('dashboard')`.

### 1.2 System Version & Auto-Deploy Health Status
* **Route:** `GET /system/version` (`system.version`)
* **Response:** Pure JSON Status (`app: GTMS`, `status: online`, `build: v1.0.3-second-verification-success`, `verified_by: Okay verifed`, `auto_deploy_status: 100_PERCENT_AUTONOMOUS`).

---

## 2. Executive Cockpit & Dashboard

* **Route:** `GET /dashboard` (`dashboard`)
* **Permission Gate:** `permission:dashboard.view`
* **Blade File:** `resources/views/pages/index.blade.php`
* **Controller Action:** Inline Closure in `routes/web.php`
* **CRUD Capabilities:** Read-only Analytics & High-Level Aggregation Cockpit.
* **Aggregated Data Streams:**
  1. **Statutory Application Pipeline:** Active counts, pending validation scrutiny, approved certifications, archived files.
  2. **Technical Operations:** DGPS demarcation total & completed, Drone surveys scheduled & ready, Environment projects, EC compliance cycles.
  3. **Financial Matrix:** Total billed amount, total payments collected, outstanding customer dues, recovery rate percentage.
  4. **Activity Logs:** Recent 6 system events with user avatar, timestamp, and audit trail.
  5. **District Heatmap:** Application distribution across Tamil Nadu's 38 administrative districts.
* **UI Components:** Bento KPI cards, dynamic Apex/Chart.js graphs, preloader suppression logic, and instant departmental jump navigation.

---

## 3. Users Management (`/user`)

* **Route:** `GET /user` (`user.index`)
* **Permission Gate:** `permission:users.view`
* **Blade Files:**
  * Listing: `resources/views/pages/authentication/users/index.blade.php`
  * Modal: `resources/views/pages/authentication/users/createuser.blade.php`
* **JavaScript / AJAX:** `public/js/ajax/user.js`
* **Controller:** `App\Http\Controllers\UserController`

### 3.1 Forms & Fields Breakdown

#### A. Add User Form (`#userForm`)
* **Trigger:** Modal `#userModal`
* **Action:** `POST /useradd` (`useradd`) | Middleware: `permission:users.create`
* **Enctype:** `multipart/form-data` (Supports avatar image upload)
* **Form Fields:**
  | Field Name | Type | Label | Validations / Rules | Required |
  | :--- | :--- | :--- | :--- | :--- |
  | `_token` | hidden | CSRF Token | Valid session token | Yes |
  | `name` | text | Full Personnel Name | `string|max:255` | Yes |
  | `email` | email | Official Email | `email|unique:users,email` | Yes |
  | `user_code` | text | Personnel Code | `nullable|string|max:50|unique:users,user_code` | Optional (Auto `LUK_xxx`) |
  | `password` | password | Security Password | `min:6` | Yes |
  | `role_id` | select | Assigned Security Role | `exists:roles,id` | Yes |
  | `branch_id` | select | Assigned Branch Office | `exists:branches,id` | Yes |
  | `mobile_num` | tel | Contact Mobile | `nullable|digits_between:10,15` | Optional |
  | `status` | select | Operational Status | `in:0,1` (1=Active, 0=Inactive) | Yes |
  | `image` | file | Profile Avatar | `image|mimes:jpeg,png,jpg,gif,webp|max:2048` | Optional |

#### B. Edit User Form (`#usereditForm`)
* **Trigger:** Modal `#usereditModal`
* **Action:** `POST /useredit` (`useredit`) | Middleware: `permission:users.edit`
* **Form Fields:**
  * `id` (hidden, required, `exists:users,id`)
  * `name`, `email`, `user_code`, `password` (nullable - keeps existing if blank), `role_id`, `branch_id`, `mobile_num`, `status`, `image`.

#### C. Delete User Action
* **Action:** `POST /userdelete` (`userdelete`) | Middleware: `permission:users.delete`
* **Parameter:** `id` (integer, `exists:users,id`)

### 3.2 Data Saving & Security Logic
1. **Multi-Tenancy & Scope:**
   * Super Admin / Admin: Sees and assigns all users statewide.
   * Non-SuperAdmin: Directory filtered to `id = auth()->id() OR user_id = auth()->id()` (can only view and create subordinate staff).
2. **Anti-Escalation Locks:**
   * Non-superadmins cannot assign `Super Admin` or `Admin` roles.
   * Non-superadmins can only assign roles created by their account and within their permission subset.
   * Self-Escalation Lock: An officer cannot edit their own account to change their own role.
3. **Statutory Record Integrity Lock:**
   * Checks `customers`, `lease_applications`, `mining_applications`, `environment_projects`, `dgps_surveys`, `drone_surveys`, `ec_certificates` for `created_by = $user->id`. If any exist, deletion is completely blocked; account must be set to `status = 0` (Inactive) instead.
4. **File Lifecycle:**
   * Uploaded avatar stored in `public/uploads/users/`.
   * On user edit or delete, old image is automatically unlinked (`unlink()`) from disk.
5. **Dual Role Sync:** Updates `users.role_id` column and calls `$user->syncRoles([$role->name])` to sync Spatie `model_has_roles`.

---

## 4. Roles & RBAC Permission Matrix (`/roles`)

* **Route:** `GET /roles` (`roles.index`)
* **Permission Gate:** `permission:roles.view`
* **Blade Files:**
  * Listing: `resources/views/pages/authentication/roles/index.blade.php`
  * Modal: `resources/views/pages/authentication/roles/createrole.blade.php`
* **JavaScript / AJAX:** `public/js/ajax/role.js`
* **Controller:** `App\Http\Controllers\RolesController`

### 4.1 Forms & Fields Breakdown

#### A. Add Role Form (`#roleForm`)
* **Action:** `POST /roleadd` (`roleadd`) | Middleware: `permission:roles.create`
* **Form Fields:**
  | Field Name | Type | Label | Validations / Rules | Required |
  | :--- | :--- | :--- | :--- | :--- |
  | `_token` | hidden | CSRF Token | Session token | Yes |
  | `name` | text | Role Title | `string|max:255|unique:roles,name` | Yes |
  | `permissions[]` | checkbox array | Granted Permissions | Array of permission names existing in `permissions` table | Optional |

#### B. Edit Role Form (`#roleeditForm`)
* **Action:** `POST /roleupdate` (`roleupdate`) | Middleware: `permission:roles.edit`
* **AJAX Population:** `GET /roles/{id}/permissions` loads role data and pre-checks active permission checkboxes.
* **Form Fields:** `id` (hidden, required), `name`, `permissions[]`.

#### C. Delete Role Action
* **Action:** `POST /roledelete` (`roledelete`) | Middleware: `permission:roles.delete`
* **Parameter:** `id` (required, `exists:roles,id`)

### 4.2 Data Saving & Safeguards
1. **18 Statutory Modules Matrix:** Groups permissions by module: `dashboard`, `branch`, `roles`, `users`, `customer`, `application`, `mining`, `environment`, `ec_certificate`, `ec_compliance`, `ppt`, `dgps`, `drone`, `category`, `product`, `unit`, `account`, `notification`.
2. **Core Role Protection:** `Admin` and `Super Admin` roles cannot be deleted, cannot be renamed, and automatically receive 100% full permissions sync.
3. **Orphan Account Protection:** A role cannot be deleted if active users are currently assigned to it (`$assignedUsersCount > 0`).
4. **Cache Invalidation:** Always triggers `app()[PermissionRegistrar::class]->forgetCachedPermissions()` on create, update, and delete.

---

## 5. Departments & Regional Branches (`/branch`)

* **Route:** `GET /branch` (`branch.index`)
* **Permission Gate:** `permission:branch.view`
* **Blade Files:**
  * Listing: `resources/views/pages/authentication/branch/index.blade.php`
  * Modal: `resources/views/pages/authentication/branch/creatbranch.blade.php`
* **JavaScript / AJAX:** `public/js/ajax/branch.js`
* **Controller:** `App\Http\Controllers\BranchController`

### 5.1 Forms & Fields Breakdown

#### A. Add Branch Form (`#branchForm`)
* **Action:** `POST /branchadd` (`branchadd`) | Middleware: `permission:branch.create`
* **Form Fields:**
  | Field Name | Type | Label | Validations / Rules | Required |
  | :--- | :--- | :--- | :--- | :--- |
  | `_token` | hidden | CSRF Token | Session token | Yes |
  | `branch_name` | text | Department / Office Name | `string|max:255` | Yes |
  | `contact_person`| text | Officer-in-Charge | `string|max:255` | Yes |
  | `mobile` | tel | Official Contact Phone | `digits_between:10,15` | Yes |
  | `address` | textarea| Postal Address | `string` | Yes |
  | `city` | text | City / Revenue Region | `nullable|string|max:255` | Optional |
  | `state` | text | State Jurisdiction | `nullable|string|max:255` | Optional |
  | `pincode` | text | Postal PIN Code | `nullable|digits:6` (Strict 6 digits) | Optional |

#### B. Edit Branch Form (`#brancheditForm`)
* **Action:** `POST /branchedit` (`branchedit`) | Middleware: `permission:branch.edit`
* **Form Fields:** `id` (hidden, required), `branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode`, `status` (`in:0,1`).

#### C. Delete Branch Action
* **Action:** `POST /branchdelete` (`branchdelete`) | Middleware: `permission:branch.delete`
* **Parameter:** `id` (required, `exists:branches,id`)

### 5.2 Data Saving & Safeguards
1. **Last Branch Lock:** System will not allow deleting the last remaining branch (`Branch::count() <= 1`).
2. **Self-Branch Lock:** An authenticated officer cannot delete the branch their own user account belongs to.
3. **Sole Admin Branch Lock:** Cannot delete a branch if it hosts the only remaining Admin account.
4. **Personnel Safety Lock:** Cannot delete a department while user accounts are assigned to it (must reassign staff first).

---

## 6. Customer Directory & 360 Tracking

* **Routes:**
  * Listing: `GET /customers` (`customers.index`)
  * 360 Show: `GET /customers/{slug}` (`customers.show`)
  * Tracking Hub: `GET /customer-tracking` (`customer-tracking.index`)
  * Proforma Invoice: `GET /customer-tracking/{customer}/proforma-invoice`
  * Tax Invoice: `GET /customer-tracking/{customer}/tax-invoice`
  * Lookup API: `GET /customers/lookup-mimas/{mimas_no}`
* **Permission Gate:** `permission:customer.view`
* **Blade Files:**
  * `resources/views/pages/customers.blade.php`
  * `resources/views/pages/customer_show.blade.php`
  * `resources/views/pages/customer_tracking/index.blade.php`
  * `resources/views/pages/customer_tracking/proforma_invoice.blade.php`
  * `resources/views/pages/customer_tracking/tax_invoice.blade.php`
* **JavaScript / AJAX:** `public/js/ajax/customer.js`
* **Controllers:** `CustomerDirectoryController`, `CustomerTrackingController`

### 6.1 Forms & Fields Breakdown

#### A. Add / Edit Customer Modal (`#customerModal`, `#customerEditModal`)
* **Actions:** `POST /customeradd` (`customeradd`) & `POST /customeredit` (`customeredit`)
* **Form Fields:**
  | Field Name | Type | Label | Validations / Rules | Required |
  | :--- | :--- | :--- | :--- | :--- |
  | `customer_name` | text | Individual Applicant | `string|max:255` | Yes |
  | `company_name` | text | Enterprise / Firm Name | `string|max:255` | Yes |
  | `mimas_no` | text | MIMAS Portal Number | `nullable|string|max:100|unique:customers,mimas_no` | Optional |
  | `mobile_num` | tel | Primary Phone | `digits_between:10,15` | Yes |
  | `email` | email | Official Email | `nullable|email` | Optional |
  | `district_id` | select | Revenue District | `exists:districts,id` | Yes |
  | `mineral_id` | select | Target Mineral | `exists:minerals,id` | Yes |
  | `pan` | text | Income Tax PAN | `nullable|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/` | Optional |
  | `aadhaar_no` | text | Aadhaar Number | `nullable|string|max:20` | Optional |
  | `gstin` | text | GSTIN Identifier | `nullable|string|size:15` | Optional |
  | `area` | text | Quarry Area Extent | Accepts Decimal (`14.20`) or Revenue (`1-50-00`) | Optional |
  | `address` | textarea| Registered Office Address| `string` | Yes |
  | `status` | select | Compliance Status | `in:0,1` | Yes |

#### B. Delete Customer Action
* **Action:** `POST /customerdelete` (`customerdelete`) | Middleware: `permission:customer.delete`

---

## 7. Master Tables (Categories, Units, Products, Stock)

### 7.1 Categories Master
* **Route:** `GET /category` (`category.index`) | Actions: `POST /categoryadd`, `POST /categoryedit`, `POST /categorydelete`
* **Blade Files:** `pages/master/category/index.blade.php`, `createcat.blade.php`
* **Fields:** `category_name` (text, req), `status` (select 1/0).

### 7.2 Units Master
* **Route:** `GET /unit` (`unit.index`) | Actions: `POST /unitadd`, `POST /unitedit`, `POST /unitdelete`
* **Blade Files:** `pages/master/unit/index.blade.php`, `createunit.blade.php`
* **Fields:** `unit_name` (text, req), `status` (select 1/0).

### 7.3 Products & Product Stock
* **Routes:** `GET /product` & `GET /productstock`
* **Blade Files:** `pages/master/product/*`, `pages/master/productstock/*`
* **Fields:** `product_name`, `category_id`, `unit_id`, `price`, `stock_quantity`, `movement_type` (inflow/outflow).

---

## 8. Statutory Lease Application Wizard (Steps 1–8 & Dossier)

* **Controller:** `App\Http\Controllers\CustomerController`
* **Permission Gates:**
  * View Hub & Dossier: `permission:application.view`
  * Wizard Steps (1-8): `permission:application.create`
  * Scrutiny & Approval: `permission:application.edit`

### 8.1 Multi-Step Wizard Form Breakdown

| Step | URL | Blade View | Primary Fields Captured | Saving Method |
| :--- | :--- | :--- | :--- | :--- |
| **Step 1** | `/step1` | `createstep1.blade.php` | `customer_id`, `district_id`, `mineral_id`, `applicant_type_id`, `nature_of_work_id`, `application_date`, `area` (Hectares / Revenue) | `POST /step1` (`CustomerController@saveStep1`) -> Creates or updates `LeaseApplication` in `status = draft`. |
| **Step 2** | `/step2` | `createstep2.blade.php` | `mimas_username`, `mimas_password` (with visual decrypted toggle and copy-to-clipboard) | `POST /step2` -> Encrypts password using `Crypt::encryptString()` into `mimas_credentials`. |
| **Step 3** | `/step3` | `createstep3.blade.php` | `lease_category_id` (Rule 19/22 tiles) OR custom `other_rule_name` text input | `POST /step3` -> Syncs `lease_categories` table; preserves custom rule name. |
| **Step 4** | `/step4` | `createstep4.blade.php` | Document Folder Taxonomy selection (Review of 41 folders: Identity, Revenue, Technical) | `GET /step4` -> Prepares checklist structures. |
| **Step 5** | `/step5` | `createstep5.blade.php` | Document uploads per folder, requirement toggle, file replacement, multi-file ZIP generation | `POST /step5/upload`, `/step5/remove`, `/step5/requirement` -> Uses Chunked Uploader or AJAX multipart. Stores in `public/uploads/lease_documents/`. |
| **Step 6** | `/step6` | `createstep6.blade.php` | Statutory Challan No, Challan Date, Bank Name, Branch, Challan Remitted Amount, Head of Account | `POST /step6` -> Saves into `application_payments` table. |
| **Step 7** | `/step7` | `createstep7.blade.php` | Taluk, Village, SF Numbers list, Extent in Hectares / Ares / Sq. Meters, Classification (Govt/Pvt) | `POST /step7` -> Populates `lease_survey_numbers` table. |
| **Step 8** | `/step8` | `createstep8.blade.php` | Executive Review of all 7 prior steps, decrypted password preview, Statutory Terms confirmation checkbox | `POST /application/submit` -> Transitions application from `draft` to `under_scrutiny`. Generates master tracking ID. |

### 8.2 Application Scrutiny Dossier (`/viewapplication`)
* **Blade File:** `pages/lease_application/viewapplication.blade.php`
* **Workflow Actions:**
  * Document Status Update: `POST /application/document/{id}/status` (`approved`, `rejected`, `reupload_requested`).
  * Final Scrutiny Approval: `POST /application/{id}/approve` -> Moves status to `approved`.
  * Application Rejection: `POST /application/{id}/reject`.
  * Move to Mining Plan: `POST /application/{id}/move-to-mining` -> Automatically transitions application and provisions initial Mining Plan dossier.
  * PDF Statutory Report: `GET /application/{id}/report` -> Generates official stamp report via `report_pdf.blade.php`.

---

## 9. Mining Plan Portal

* **Routes:** `GET /miningplan`, `GET /newapplication`, `GET /document`, `GET /projectfolder`, `GET /process`
* **Permission Gates:** `permission:mining.view`, `permission:mining.create`, `permission:mining.edit`
* **Blade Files:**
  * `pages/mining-portal/index.blade.php`
  * `pages/mining-portal/newapplication.blade.php`
  * `pages/mining-portal/document.blade.php`
  * `pages/mining-portal/projectfolder.blade.php`
  * `pages/mining-portal/process.blade.php`
* **Controller:** `App\Http\Controllers\MiningController`
* **Form & Data Saving Methods:**
  * **New Mining Application:** `POST /newapplication` -> Captures Letter of Intent (LOI) date, Targeted Annual Production capacity, 5-Year mining scheme, GPS boundary coordinates. Auto-saves drafts via `POST /newapplication/autosave`.
  * **Mining Documents:** `POST /mining-portal/upload-document` and `POST /mining-portal/remove-document`.
  * **Stage Advancement:** `POST /mining/application/{id}/stage` -> Transitions through stages (`scrutiny` -> `inspection` -> `presentation` -> `approved`).
  * **Move to Environment:** `POST /mining/application/{id}/move-to-environment` -> Hands over project to Environment Clearance portal.

---

## 10. Environment Clearance (EC Unified & B1/B2)

* **Routes:** `GET /eviron`, `GET /eviron/create`, `GET /eviron/{id}`, `GET /environment-b2`
* **Permission Gates:** `permission:environment.view`, `permission:environment.b2.create`, `permission:environment.b2.review`
* **Blade Files:** `pages/eviron/*`, `pages/enviro_b1/*`, `pages/enviro_b2/*`
* **Controllers:** `EnverionsoneController`, `EnvironmentalB2Controller`
* **Category Routing:**
  * **B1 - Subcategory 1 (SC1):** Terms of Reference (TOR) granted, Baseline EIA environmental studies.
  * **B1 - Subcategory 2 (SC2):** Public Hearing proceedings, Final Environmental Impact Assessment report.
  * **B2:** Direct appraisal (Quarries < 5 Hectares).
* **Workflows & Actions:**
  * Project Creation: `POST /eviron` (Captures SEIAA proposal number, quarry classification, EIA consultant credentials).
  * Document Upload & Review: `POST /eviron/{id}/documents/{document}/upload` and `review`.
  * Submit to PPT: `POST /eviron/{id}/submit-sc1-ppt`, `submit-sc2-ppt`, `submit-b2-ppt` -> Hands project over to Presentation Department.

---

## 11. Technical Operations: DGPS & Drone Surveys

### 11.1 DGPS Survey Portal
* **Routes:** `GET /dgps-survey`, `GET /dgps-survey/step/{step}`, `GET /dgps-survey/{id}`
* **Blade Files:** `pages/dgps_survey/index.blade.php`, `show.blade.php`, `wizard.blade.php`
* **Controller:** `DgpsSurveyController`
* **Form Fields:** Survey No, Field Surveyor Name, Demarcation Date, Rover GPS Coordinates table (Latitude, Longitude, Northing, Easting, Pillar Nos), Boundary Area output, Base Station calibration report upload.
* **Saving Method:** Multi-step wizard saving to `dgps_surveys`, `dgps_points`, and `dgps_documents`.

### 11.2 Drone Topography Survey
* **Routes:** `GET /drone-survey`, `GET /drone-survey/step/{step}`, `GET /drone-survey/{id}`
* **Blade Files:** `pages/drone_survey/index.blade.php`, `show.blade.php`, `wizard.blade.php`
* **Controller:** `DroneSurveyController`
* **Form Fields:** Drone Pilot DGCA License No, Flight Date, Altitude, GCP Points count, Orthomosaic GeoTIFF upload, Digital Elevation Model (DEM) file, Surface Contours (.dxf/.shp), Volume extraction calculation.

---

## 12. Presentation & Compliance (PPT, EC Certificate, Half-Yearly)

### 12.1 PPT Department
* **Routes:** `GET /ppt-department`, `GET /ppt-department/step/{step}`, `GET /ppt-department/{id}`
* **Blade Files:** `pages/ppt_department/*` | Controller: `PptDepartmentController`
* **Actions:** `POST /ppt-department` (Meeting schedule, SEAC committee agenda, Presentation Slide deck .pptx/.pdf upload), `POST /ppt-department/{id}/approve-stage` (Meeting Minutes & Approval).

### 12.2 EC Certificate Issuance
* **Routes:** `GET /ec-certificate`, `GET /ec-certificate/{id}`
* **Blade Files:** `pages/ec_certificate/*` | Controller: `EcCertificateController`
* **Actions:** EC Order No, Date of Issuance, Validity Period (Years), Annual Permitted Mining Capacity, Special Conditions Annexure upload.

### 12.3 EC Compliance (Half-Yearly Monitoring)
* **Routes:** `GET /ec-compliance`, `GET /ec-compliance/{id}`
* **Blade Files:** `pages/ec_compliance/*` | Controller: `EcComplianceController`
* **Actions:** Period Selection (June/December cycle), Air/Water/Noise Environmental Lab reports upload, Greenbelt plantation progress, MoEFCC / IRO submission acknowledgement receipt.

---

## 13. Accounts & Financial Management (`/accounts/*`)

* **Controllers:** `QuotationController`, `PaymentCollectionController`, `PaymentReceiptController`, `CustomerLedgerController`, `FinancialReportController`, `InvoiceSealController`
* **Permission Gates:** `permission:account.view`, `permission:account.create`, `permission:account.edit`, `permission:account.delete`

### 13.1 Quotations Module (`/accounts/quotations`)
* **Blades:** `index.blade.php`, `create.blade.php`, `edit.blade.php`, `show.blade.php`, `print.blade.php`
* **Form Fields:** `customer_id`, `quotation_date`, `valid_until`, `bank_account_id`, Dynamic line items (`service_name`, `sac_code`, `rate`, `qty`, `taxable_amount`, `cgst_rate`, `sgst_rate`, `igst_rate`, `total_amount`), customer concessions lookup.
* **Saving Method:** `POST /accounts/quotations` -> Writes to `quotations` and `quotation_items` in a database transaction (`DB::transaction`).

### 13.2 Payment Collection & Official Receipts (`/accounts/payments`, `/accounts/receipts`)
* **Blades:** `payments/create.blade.php`, `receipts/index.blade.php`, `receipts/print.blade.php`
* **Form Fields:** `customer_id`, `amount_received`, `payment_date`, `payment_mode` (NEFT, RTGS, Cheque, UPI, Cash), `reference_no`, `bank_account_id`, `allocations` (against quotations/invoices).
* **Receipt Output:** High-fidelity printable money receipt with amount in words, digital seal, and barcode tracking.

### 13.3 Customer Ledger & Statements (`/accounts/ledger`)
* **Blades:** `ledger/index.blade.php`, `ledger/show.blade.php`, `ledger/print.blade.php`
* **Capabilities:** Real-time running balance calculation (Debit = Billed Invoices, Credit = Payments Collected, Net Due Balance). Date-range filterable and printable statement of account.

### 13.4 Digital Seal & Signature Management
* **Modal:** `pages/accounts/partials/invoice_seal_modal.blade.php`
* **Actions:** `POST /accounts/invoices/seal-upload` & `POST /accounts/invoices/seal-remove`. Supports transparent PNG seal overlays or "Blank Space Mode" for wet physical stamping.

---

## 14. Cross-Departmental Master Pipeline & Real-Time Notifications

### 14.1 Universal Master Application Pipeline (`/master-pipeline`)
* **Routes:** `GET /master-pipeline`, `GET /master-pipeline/timeline/{id}`, `POST /master-pipeline/forward/{id}`, `POST /master-pipeline/return/{id}`, `POST /master-pipeline/resubmit/{id}`
* **Blade Files:** `pages/master_pipeline/index.blade.php`, `timeline.blade.php`
* **Controller:** `MasterPipelineController`
* **Functionality:** Central nervous system of GTMS. Tracks a single statutory project across all 7 departments (`Lease` -> `Mining` -> `Environment` -> `DGPS` -> `Drone` -> `PPT` -> `EC`).
* **Bidirectional Movement:** Allows downstream departments to return an application to an upstream department with formal scrutiny remarks, and tracks revision resubmissions.

### 14.2 Real-Time Notifications Engine (`/notifications`)
* **Route:** `GET /notifications` (`notifications.index`)
* **Blade File:** `pages/notifications/index.blade.php`
* **Controller:** `NotificationController`
* **Actions:** `POST /notifications/{id}/mark-as-read`, `mark-all-read`, `DELETE /notifications/clear-all`.

---

## 15. Underlying Enterprise Safeguards & "Things You Might Miss"

### 1. Resilient Chunked Upload Engine (`gtms-chunk-uploader.js`)
* Files over 10MB (such as 100MB CAD drawings, GIS Shapefiles, and Drone GeoTIFFs) bypass standard PHP POST limits by using `/api/upload/chunk/*`.
* Uploads slices in 2MB chunks with MD5 verification and assembles on server disk, ensuring zero packet drops on unstable field connections.

### 2. Multi-Tenancy Branch Scoping (`MultiTenancyBranchScope`)
* Every operational model (`Customer`, `LeaseApplication`, `MiningApplication`, `DgpsSurvey`, etc.) implements `belongsTo(Branch::class)`.
* Non-admin users are automatically scoped so they cannot query, modify, or view records belonging to other regional branches.

### 3. Password Decryption & State Security
* Department portal credentials (e.g., MIMAS mining login) are stored encrypted via Laravel's AES-256-CBC cipher (`Crypt::encryptString()`).
* In forms, they are never serialized in plaintext; decrypted dynamically for authorized scrutiny officers with click-to-copy protection.

### 4. Zero Unhandled 500 Principle
* Every AJAX submission returns structured JSON: `{ status: 1|0, message: "...", data: {...} }`.
* Database deletions enforce pre-deletion integrity checks so foreign key errors are caught before triggering SQL exceptions.

---
*End of GTMS Page-Wise CRUD, Form & Architecture Blueprint.*
