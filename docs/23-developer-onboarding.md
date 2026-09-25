# GTMS — 7-Day Developer Onboarding & Cold Takeover Guide

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Document Number:** `23` of `23`  
**Classification:** Engineering Onboarding & Technical Runbook  
**Target Audience:** Incoming Full-Stack Laravel Engineers taking over GTMS cold  
**Authoritative Source:** Codebase Architecture, Database Schemas, Test Suites, and Operational Pipelines  
**Status:** Approved Operational Manual  

---

## 1. Welcome to GTMS

Welcome to the **Granite / Mining Tracking Management System (GTMS)** engineering team. This manual is designed for an experienced Laravel developer taking over the codebase cold—without direct access to previous developers or architects.

GTMS is an enterprise statutory compliance and workflow management ERP built for Tamil Nadu mining, quarry, and environmental regulatory operations. It tracks the complete lifecycle of mineral concessions across multiple state and federal departments:
1. **Lease Application Management** (8-Step Statutory Wizard, DoGM / District Collectorate)
2. **Mining Plan Preparation & Approval** (6 Sequential Stages, Process Flow 6.1 – 6.6)
3. **Environmental Clearance (EC)** (Category B1 2-Stage ToR/EIA & Category B2 6-Folder Workflow)
4. **PPT Department** (SEAC / DEAC Presentation Gates & Committee Approvals)
5. **DGPS Boundary Survey** (Differential GPS Sub-Centimeter Geo-Coordinates & Boundary Polygons)
6. **Drone Survey Tracking** (Aerial Photogrammetry, 3D DEM, Orthomosaic, DGCA Compliance)
7. **EC Half-Yearly Compliance Monitoring** (Statutory 6-Month Environmental Audit Filings)
8. **Customer 360 Dossier & Universal Invoicing** (GST Invoicing with Indian Numbering Words)

### Architectural Mindset Before Touching Code
1. **Monolithic MVC with Controller-Centric Business Logic:** GTMS does not use an abstract Service/Repository layer. Business logic, transactions, and validation live directly in Controllers and Model lifecycle hooks.
2. **Dedicated Document Tables:** Unlike generic polymorphic attachment systems, GTMS maintains dedicated tables per module (`lease_documents`, `mining_documents`, etc.) to prevent database locks and ensure inter-departmental legal isolation.
3. **Customer as Central Operational Hub:** The `Customer` model is the primary aggregate. All statutory applications link back to `customer_id`.
4. **Zero-Assumption Integrity:** Never assume client parameters or statutory requirements. All filings are bound by the Tamil Nadu Minor Mineral Concession Rules, 1959 (TNMMCR) and EIA Notification, 2006.

---

## 2. Day 1: System Boot, Environment Setup & Architectural Topology

### Objective
Get GTMS running locally on your workstation, execute migrations and seeders, compile frontend assets, and verify authentication.

### 2.1 System Prerequisites
* **Operating System:** Windows 10/11 (XAMPP environment) or Linux (Ubuntu 22.04 LTS).
* **PHP Runtime:** PHP 8.2+ (verified on PHP 8.2.12 CLI x64).
  * Required Extensions: `pdo_mysql`, `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `gd`.
* **Database Server:** MySQL 8.0+ or MariaDB 10.4+ (Database: `gtms_data`, UTF8MB4).
* **Node.js & NPM:** Node.js 18.x or 20.x with NPM.
* **Composer:** Composer 2.x.

### 2.2 Step-by-Step Local Setup Commands

```bash
# 1. Navigate to project root
cd c:\xampp\htdocs\GTMS\gtms

# 2. Install PHP backend dependencies
composer install

# 3. Create environment configuration
copy .env.example .env

# 4. Generate unique application encryption key
php artisan key:generate
```

### 2.3 Database Configuration (`.env`)
Open `.env` and verify database and driver settings (never commit secrets):
```ini
APP_NAME="GTMS"
APP_ENV=local
APP_KEY=[GENERATED_BY_ARTISAN]
APP_DEBUG=true
APP_TIMEZONE="Asia/Kolkata"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gtms_data
DB_USERNAME=root
DB_PASSWORD=[REDACTED]

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
```

### 2.4 Initialize Database & Seed Master Data
Create database `gtms_data` in phpMyAdmin or MySQL CLI, then execute:
```bash
# Run all 48 database migrations
php artisan migrate

# Seed foundational roles, 38 TN districts, minerals, lease categories, and nature of works
php artisan db:seed
```

### 2.5 Compile Frontend Assets
```bash
# Install Node dependencies
npm install

# Compile production bundles via Vite
npm run build
```

### 2.6 Boot Server & Verify First Login
```bash
php artisan serve
```
* Visit `http://127.0.0.1:8000/login` in your browser.
* Log in using default administrator seed credentials:
  * **Email:** `admin@gtms.test`
  * **Password:** `admin123` (or seeded admin credential in local environment)
* Verify access to `/dashboard`.

### Day 1 Reading Assignment
* `docs/00-project-overview.md` — Project mission, domain context, and module matrix.
* `docs/01-architecture.md` — High-level MVC flow and cross-module boundaries.
* `docs/02-environment-setup.md` — Deep dive into environment variables and drivers.

---

## 3. Day 2: Data Models, Schema Dictionary & Customer 360 Core

### Objective
Master the 64 database tables, 47 Eloquent models, `Customer` 360 aggregate, and the `BranchScope` multi-tenancy model.

### 3.1 The Central Aggregate: `Customer`
Open `app/Models/Customer.php`. Key architectural observations:
* **Unique Slugs with Soft-Delete Protection:**
  Notice `booted()` creating hook (lines 47-58):
  ```php
  while (static::withTrashed()->where('slug', $slug)->exists()) {
      $slug = "{$base}-" . (++$i);
  }
  ```
  It checks `withTrashed()` to guarantee that newly generated slugs never collide with soft-deleted rows.
* **The 7 Child Statutory Relationships:**
  `Customer` holds 1:N relationships to all modules:
  * `$customer->leaseApplications()`
  * `$customer->miningApplications()`
  * `$customer->environmentProjects()`
  * `$customer->ecCertificates()`
  * `$customer->pptApplications()`
  * `$customer->dgpsSurveys()`
  * `$customer->droneSurveys()`
  * `$customer->ecCompliances()`

### 3.2 Multi-Tenancy Architecture (`BranchScope`)
Open `app/Models/Scopes/BranchScope.php` and `app/Models/Traits/BelongsToBranch.php`.
* Non-admin users (`role_id !== 1`) are globally scoped to their assigned `branch_id`.
* The `BelongsToBranch` trait automatically populates `branch_id = Auth::user()->branch_id` during record creation.
* 8 core statutory models implement this trait. (Note: `Customer` is intentionally unscoped as a global client hub; see `docs/22-unknowns-risks.md`).

### 3.3 Hands-On Exercise
Open `php artisan tinker` and run:
```php
// 1. Inspect total models
App\Models\Customer::count();
App\Models\District::count(); // Should be 38

// 2. Test Customer slug creation
$c = App\Models\Customer::create([
    'customer_name' => 'Demo Quarry Operator',
    'company_name'  => 'Salem Blue Metals Ltd',
    'mimas_no'      => 'TN-MMS-SLM-9999',
    'mobile_num'    => '9876543210',
    'district_id'   => 1,
    'status'        => 1
]);
echo $c->slug; // Should be "salem-blue-metals-ltd"

// 3. Clean up test record
$c->forceDelete();
```

### Day 2 Reading Assignment
* `docs/03-database.md` — Complete 64-table database dictionary and foreign key map.
* `docs/04-models.md` — Complete breakdown of all 47 Eloquent models, casts, and hooks.
* `docs/09-authentication-authorization.md` — Spatie roles, permissions, and `BranchScope`.

---

## 4. Day 3: Lease Application 8-Step Wizard & File Storage Architecture

### Objective
Understand the 8-step statutory lease intake wizard in `CustomerController.php`, the 19-folder checklist, session draft persistence, and physical file cloning (`moveToMining`).

### 4.1 The 8-Step Intake Flow
Navigate through `resources/views/pages/lease_application/`:
1. **Step 1:** Applicant entity selection, company profile, and primary/secondary contacts.
2. **Step 2:** Concession jurisdiction: district, taluk, village, S.F. numbers, extent, and minerals.
3. **Step 3:** Revenue land categorization (Patta, Poramboke, Government land).
4. **Step 4:** Folder overview (3 statutory folders: Documents, Lease Application, Plan).
5. **Step 5:** 19-item dynamic upload checklist (`createstep5.blade.php`).
6. **Step 6:** Handlers assignment and encrypted MIMAS portal credentials.
7. **Step 7:** Comprehensive application review and draft verification.
8. **Step 8:** Final submission wrapped in `DB::transaction()` generating official `LA-{YEAR}-{SEQUENCE}`.

### 4.2 File Storage Layout (`public/uploads/...`)
* Lease applications store physical files in:
  `public/uploads/lease_applications/{application_no}/`
* Uploaded files are recorded in the `lease_documents` table with `folder_id`, `document_field_id`, and `file_path`.
* Validation permits `pdf,png,jpg,jpeg,kml,xml,txt,doc,docx,dwg,dxf` up to 25MB.

### 4.3 The Cross-Module Promotion Pattern (`moveToMining`)
Inspect `CustomerController.php:1694-1732`:
* When an approved lease moves to the Mining Department:
  1. Creates target directory `public/uploads/mining/{miningAppNo}/`.
  2. Iterates over `$lease->documents` and physically copies files via `@copy($sourcePath, $destPath)`.
  3. Maps KML and boundary plans into Folder 5 (Plan) and revenue documents (Patta, Adangal) into Folder 2 (Documents).
  4. Creates corresponding `MiningDocument` records with `status = 'validated'`.
  5. Preserves the Universal Common ID (`GTMS-{YEAR}-{SEQUENCE}`).

### 4.4 Hands-On Exercise
1. In browser, visit `/application/step1`.
2. Enter an applicant, advance to Step 2, enter survey numbers (`101/1, 101/2`), extent (`2.50.0 Ha`), and select mineral `Rough Stone`.
3. In Step 5, upload a test PDF to "Patta" and a test KML to "KML File".
4. Complete Step 8 and submit. Inspect `public/uploads/lease_applications/LA-2026-XXXX/` to confirm files are on disk.
5. In `/application`, click `[ Move to Mining ]`. Verify that the files are cloned into `public/uploads/mining/MP-2026-XXXX/`.

### Day 3 Reading Assignment
* `docs/05-controllers.md` — Controller method breakdown (`CustomerController`).
* `docs/07-form-requests-validation.md` — Validation rules, regex masks, and boundary rules.
* `docs/14-file-storage.md` — Directory structure, permissions, and file cloning.

---

## 5. Day 4: Mining Plan Lifecycle (Process 6.1 – 6.6) & Multi-Mineral Pivot

### Objective
Master the 6 sequential stages of Mining Plan approval in `MiningController.php`, 8 Natures of Work, and the multi-mineral pivot table.

### 5.1 The 6 Sequential Stages
Inspect `resources/views/pages/mining-portal/process.blade.php`:
* **Stage 6.1 (Intake):** Plan type, RQP details, 5-year production schedule, bench parameters.
* **Stage 6.2 (Validation):** Field inspection report and checklist validation.
* **Stage 6.3 (Technical Scrutiny):** Engineering review of stripping ratio and reserve calculations.
* **Stage 6.4 (Client Review):** Client sign-off and portal credential confirmation.
* **Stage 6.5 (Payment):** Fee collection, challan attachments, and ledger update.
* **Stage 6.6 (Approval):** Order issuance, proceeding number, and transition to Environment.

### 5.2 Multi-Mineral Pivot Table Architecture
Quarries often extract multiple minerals (e.g., Rough Stone AND Gravel):
* Table: `mining_application_minerals` (`mining_application_id`, `mineral_id`).
* Model Relationship: `$miningApp->minerals(): BelongsToMany`.
* Backward-Compatibility Anchor: `mining_applications.mineral_id` is preserved and populated with the primary mineral (`$mineralIds[0]`) to ensure legacy single-mineral queries never fail.

### 5.3 Resumption Workflow (`?resume={id}`)
When an application is moved from Lease, it opens `/newapplication?resume={id}`:
* Loads carried parameters into form fields with green visual feedback (`.field-autofilled`).
* The operator only needs to fill mining-specific fields (Plan Type, RQP, Nature of Work, Production Schedule).

### 5.4 Hands-On Exercise
1. Open `/miningplan` and find the resumed application created on Day 3.
2. Complete Stage 6.1: Assign RQP `Er. M. Senthil Kumar`, set Plan Type `Mining Plan`, and set Year 1 production to `50,000 CBM`.
3. In Stage 6.2, click `[ ✓ Pass Validation ]` to bulk validate attachments.
4. Advance through Stages 6.3 to 6.6. Verify status transitions in `mining_applications.stage`.

### Day 4 Reading Assignment
* `docs/08-services-business-logic.md` — State machines 6.1–6.6 and transition rules.
* `docs/18-feature-map.md` — End-to-end mapping from feature to table.
* `docs/19-data-flows.md` — Sequence diagrams for Lease-to-Mining transition.

---

## 6. Day 5: Environmental Clearance (B1 vs B2), PPT Department & EC Certificates

### Objective
Master the unified Environmental Clearance module (`EnverionsoneController.php`), Category B1 2-stage stepper vs Category B2, PPT Department defense gates, and EC Certificate issuance.

### 6.1 Category B1 vs Category B2 Workflows
* **Category B1 (Large / Cluster Quarries > 5 Ha):**
  * Sequential 2-stage state machine:
    * `sc1_prep` (5 Folders) -> `submit-sc1-ppt` -> PPT defense -> ToR approved.
    * `sc2_prep` (6 Folders) -> Public hearing -> `submit-sc2-ppt` -> Final EC approved.
* **Category B2 (Small / Non-Cluster Quarries <= 5 Ha):**
  * 6-folder streamlined workflow without mandatory public consultation.

### 6.2 PPT Department (`PptDepartmentController.php`)
* Manages technical defense before the State Expert Appraisal Committee (SEAC).
* Tracks meeting agenda, minutes, Essential Details Sought (EDS), and Additional Details Sought (ADS) replies across 11 statutory folders.
* Upon committee approval, calls `approve-stage` which unlocks Stage SC2 or marks the project `completed`.

### 6.3 EC Certificate Issuance (`EcCertificateController.php`)
* 6-step gated creation wizard capturing EC Order Number, SEIAA validity dates, annual production ceilings, and conditions.
* Generates printable official orders with letterhead, QR seals, and condition dockets.

### 6.4 Hands-On Exercise
1. Visit `/eviron/create` and create a Category B1 project.
2. Upload Form-1 and PFR in Stage 1, then click `[ Submit to PPT for ToR ]`.
3. Switch to `/ppt-department`, find the dossier, and execute `[ Approve Stage ]`.
4. Return to `/eviron/{id}` and verify that Stage SC2 has unlocked automatically.
5. In `/ec-certificate`, issue an EC Certificate with a 5-year validity.

### Day 5 Reading Assignment
* `docs/15-integrations.md` — External portals (PARIVESH, MIMAS) and district sync.
* `docs/19-data-flows.md` — Detailed sequence flow for Category B1 2-stage lifecycle.
* `docs/20-error-handling.md` — Transaction rollbacks, exceptions, and activity audit logging.

---

## 7. Day 6: Surveys, EC Compliance, Customer 360 Tracking & Commercial Invoicing

### Objective
Master DGPS and Drone surveys, EC Half-Yearly Compliance, the Customer 360 live search engine, and dynamic GST invoicing.

### 7.1 Surveys & Post-EC Compliance
* **DGPS Survey (`DgpsSurveyController.php`):** Captures boundary pillar coordinates (`BP-1`, `BP-2`...) on WGS-84 / UTM projection, storing northing, easting, and elevation.
* **Drone Survey (`DroneSurveyController.php`):** Catalogs DGCA flight logs, orthomosaic TIFFs, 3D meshes, and volumetric cut/fill computations.
* **EC Half-Yearly Compliance (`EcComplianceController.php`):** Tracks statutory 6-month compliance cycles (April-Sept, Oct-March), NABL accredited environmental lab test reports, and PARIVESH acknowledgments.

### 7.2 Customer 360 Live Search Engine
Inspect `CustomerTrackingController.php:356-480`:
* Bidirectional Aadhaar normalization strips dashes and spaces:
  ```php
  $cleanDigits = preg_replace('/[^0-9]/', '', $q);
  ```
* Cross-queries primary and secondary phone numbers, Customer Unique ID, company names, and 7 child modules.
* Masks sensitive Aadhaar digits for display (`9876 **** 1012`).

### 7.3 Dynamic Invoicing Engine
Inspect `CustomerTrackingController.php:826-1050`:
* Dynamically aggregates active projects into **Proforma Invoices** (`/proforma-invoice`) and official **Tax Invoices** (`/tax-invoice`).
* Assigns Services Accounting Codes (`SAC 998341` to `998349`).
* Calculates statutory GST splits (CGST 9% + SGST 9% vs IGST 18%).
* Converts total amount into formal Indian numbering words via `numberToWords()`:
  * Uses `Crore` (10,000,000), `Lakh` (100,000), `Thousand`, `Hundred`, and `Paise`.
  * Example: `"Rupees Two Lakh Thirty-Six Thousand Only"`.

### 7.4 Hands-On Exercise
1. Visit `/customer-tracking`.
2. In the top search bar, search for a customer by raw 12-digit Aadhaar (without hyphens). Verify instant live autocomplete.
3. Open the Customer 360 Dossier (`/customer-tracking/{slug}`).
4. Click `[ Tax Invoice ]`. Verify the generated PDF/print view, SAC codes, GST split, and the amount written in Indian numbering words.

### Day 6 Reading Assignment
* `docs/10-frontend.md` — Blade layout hierarchy, DataTables, and dynamic handlers.
* `docs/11-api.md` — AJAX search, MIMAS lookup, and document status endpoints.
* `docs/21-glossary.md` — Authoritative domain terminology dictionary.

---

## 8. Day 7: Testing Suite, Security Hardening, Technical Debt & Production Deployment

### Objective
Execute the full automated test suite, review the technical risk register, understand production deployment optimization, and verify developer independence.

### 8.1 Execute Automated Test Suite
Run the test suite using PHPUnit:
```bash
php artisan test
```
* **Expected Result:** 50 tests passing (49 Feature, 1 Unit), 390 assertions, 0 failures.
* **Key Tests to Inspect:**
  * `tests/Feature/CustomerTrackingFilterTest.php` — Multi-faceted search and filter tests.
  * `tests/Feature/EnvironmentClearanceTest.php` — B1/B2 workflow transitions.
  * `tests/Feature/MiningPlanTransitionTest.php` — Cross-module file cloning and validation.

### 8.2 Review Technical Debt & Risk Register
Open and carefully review `docs/22-unknowns-risks.md`:
* **SEC-01:** Plaintext password exposure in `show_password` column. Understand the remediation plan.
* **SCH-01:** Double column ambiguity: `mimas_no` (internal Customer Unique ID) vs `mimas_number` (external state portal registration).
* **CODE-01:** Dead model triad: `EnvironmentalProject`, `EnvironmentalDocument`, and `EnvironmentalActivity` are obsolete stubs. Never use them in new code.

### 8.3 Production Deployment Checklist
When deploying GTMS to production Linux/Nginx servers:
```bash
# 1. Install production dependencies without dev packages
composer install --optimize-autoloader --no-dev

# 2. Cache configurations, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 3. Run production migrations safely
php artisan migrate --force

# 4. Set strict file permissions
chmod -R 775 storage bootstrap/cache public/uploads
chown -R www-data:www-data storage bootstrap/cache public/uploads

# 5. Start background queue worker daemon
php artisan queue:work --queue=default,uploads --sleep=3 --tries=3
```

### Day 7 Reading Assignment
* `docs/12-jobs-queues-events.md` — Queue configuration and background workers.
* `docs/13-middleware-security.md` — CSRF, middleware aliases, and credential protection.
* `docs/16-testing.md` — Test architecture, transaction rollbacks, and coverage.
* `docs/17-deployment.md` — Full production server deployment runbook.
* `docs/22-unknowns-risks.md` — Technical risk register and mitigation roadmap.

---

## 9. Developer Independence Sign-Off Checklist

Before completing your first week and taking full ownership of GTMS, verify that you can perform each of the following tasks without assistance:

- [ ] I can boot a clean GTMS instance locally from scratch using `composer install`, `.env`, `php artisan migrate`, and `db:seed`.
- [ ] I can explain the role of `Customer` as the central aggregate and how 7 statutory modules link to it.
- [ ] I understand how `BranchScope` enforces tenant isolation and which 8 models implement `BelongsToBranch`.
- [ ] I can walk through an 8-step Lease Application intake and verify physical uploads in `public/uploads/lease_applications/`.
- [ ] I understand how `moveToMining` clones physical files and creates `MiningDocument` records.
- [ ] I can advance a Mining Plan through the 6 sequential stages from Stage 6.1 to Stage 6.6.
- [ ] I understand the difference between Category B1 (2-Stage ToR/EIA) and Category B2 (6-Folder) Environmental Clearances.
- [ ] I can trace how PPT Department approvals unlock subsequent environmental stages.
- [ ] I can explain the difference between `mimas_no` (Customer Unique ID) and `mimas_number` (state portal reg).
- [ ] I know why `show_password` is a security risk and how to handle credentials safely using `Crypt::encryptString`.
- [ ] I can run `php artisan test` and verify that all 50 tests pass.
- [ ] I know where to look in `docs/00` through `docs/23` for any architectural, schema, or workflow question.
