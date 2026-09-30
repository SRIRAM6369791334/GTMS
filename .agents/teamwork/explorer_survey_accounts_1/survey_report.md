# GTMS Accounts & Financial Management Module — Comprehensive Codebase & Database Architecture Survey

**Author:** explorer_survey_accounts_1  
**Working Directory:** `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1`  
**Date & Timestamp:** 2026-09-29T05:52:00Z  
**Target Project:** Granite / Mining Tracking Management System (GTMS)  
**Database Schema:** `gtms_data` (MySQL 8.0 / MariaDB 10.4+, InnoDB Engine)  
**Authoritative References:**  
- `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md` (Header `## 2026-09-29T05:33:01Z`)  
- `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_1\DISPATCH.md`  
- `database/migrations/` (53 Migration files verified)  
- `app/Models/` (49 Model files verified)  
- `docs/03-database.md` & `docs/04-models.md`  

---

## 1. Executive Summary & Problem Scope

The GTMS platform is an enterprise-grade ERP built to govern mining statutory compliance, land concessions, and technical field engineering across Tamil Nadu's 38 revenue districts. While GTMS currently possesses operational workflows for 7 core statutory applications (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`) plus statutory EC monitoring (`ec_compliances`), its commercial financial handling is currently split across disparate summary columns on application tables and a universal summary table (`application_payments`).

To fulfill the requirements of **Milestone 7 (Accounts & Financial Management Module)**, GTMS must transition from isolated application-level payment fields to a centralized, double-entry financial architecture supporting:
1. **Dynamic Quotation Generation Engine (R1)** with client/concession auto-population, multi-service SAC itemization, terms & scope configuration, and high-fidelity A4 printable layout.
2. **Centralized Payment Collection Engine (R2)** resolving outstanding customer dues across all 7 statutory modules and atomically synchronizing payment increments into both application tables and `application_payments`.
3. **Official Money Receipt Voucher Generation & Printing (R3)** with sequential voucher numbering (`GTMS/REC/YYYY/XXXX`), remaining balance tracking, and A4/A5 voucher distribution layouts.
4. **Customer Financial Ledger & Statement of Account (R4)** providing chronological debit/credit dossier aggregation and printable account statements.
5. **Comprehensive Financial Transaction Reports & Export (R5)** featuring KPI metrics, multi-parametric filtering, and CSV/Excel export.
6. **Unified UI Integration & Spatie RBAC Permissions (R6)** with `account.view`, `account.create`, `account.edit`, and `account.delete` enforcement.

This survey establishes the complete empirical database ground truth, audits all existing schemas and models, and provides the authoritative migration and relational specifications for the engineering team.

---

## 2. Complete Schemas of the 7 Statutory Tables (Plus EC Compliance)

Across GTMS, commercial fee tracking was standardized via migrations `2026_09_22_000002_add_payment_fields_to_applications_tables.php` and `2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`. Every statutory application maintains 4 standardized commercial attributes:
- `product_value`: `DECIMAL(12,2)` nullable default `0.00` — Contracted commercial project fee.
- `paid_amount`: `DECIMAL(12,2)` nullable default `0.00` — Cumulative amount collected from client.
- `pending_amount`: `DECIMAL(12,2)` nullable default `0.00` — Balance receivable (`product_value - paid_amount`).
- `payment_status`: `ENUM('pending', 'partial', 'paid')` default `'pending'` — Real-time payment state.

Below are the exact schemas, models, and migration citations for each table.

---

### 2.1 `lease_applications`
- **Domain:** 7-Step Statutory Quarry Concession Application (TNMMCR 1959).
- **Migration Sources:**
  - `database/migrations/2026_09_04_000003_create_lease_module_tables.php` (Lines 14–48)
  - `database/migrations/2026_09_10_000001_update_lease_application_status_enum.php`
  - `database/migrations/2026_09_16_095500_add_common_id_to_lease_and_mining_tables.php` (Lines 14–24)
  - `database/migrations/2026_09_16_104641_add_secondary_contact_to_customers_and_leases.php` (Lines 26–36)
  - `database/migrations/2026_09_16_122500_create_lease_application_minerals_and_other_column.php` (Lines 31–36)
  - `database/migrations/2026_09_22_000002_add_payment_fields_to_applications_tables.php` (Lines 15–30)
- **Model File:** `app/Models/LeaseApplication.php` (154 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `common_id` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Universal common ID (`GTMS-YYYY-XXXX`) |
| `application_no` | `VARCHAR(50)` | No | None | `UNIQUE` | Unique lease application number (`LA-YYYY-XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`, cascade delete) |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Jurisdiction district (`districts.id`) |
| `category_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Concession category (`lease_categories.id`) |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`) |
| `other_mineral_name` | `VARCHAR(255)` | Yes | `NULL` | None | Free-text mineral name if "Other" selected |
| `taluk` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative taluk |
| `village` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative village |
| `area_extent_ha` | `DECIMAL(10,2)` | Yes | `NULL` | None | Concession land extent in Hectares |
| `area_extent_acres` | `VARCHAR(100)` | Yes | `NULL` | None | Concession land extent in Acres |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `start_date` | `DATE` | Yes | `NULL` | None | Statutory concession start date |
| `end_date` | `DATE` | Yes | `NULL` | `INDEX` | Statutory concession expiration date |
| `lease_period_years` | `INT` | Yes | `NULL` | None | Duration of lease period in years |
| `contact_person` | `VARCHAR(255)` | Yes | `NULL` | None | Primary authorized contact person |
| `secondary_contact_person`| `VARCHAR(255)`| Yes | `NULL` | None | Secondary site supervisor / representative |
| `contact_mobile` | `VARCHAR(15)` | Yes | `NULL` | None | Primary contact telephone number |
| `secondary_contact_mobile`| `VARCHAR(15)`| Yes | `NULL` | None | Secondary contact telephone number |
| `current_step` | `TINYINT` | No | `1` | None | Active wizard step in progress (1 through 8) |
| `status` | `ENUM(...)` | No | `'draft'` | None | `'draft','submitted','under_scrutiny','validated','approved','rejected','revision_required','expired'` |
| `go_number` | `VARCHAR(100)` | Yes | `NULL` | None | Government Order (G.O.) grant number |
| `go_date` | `DATE` | Yes | `NULL` | None | Official date of G.O. grant |
| `go_file` | `VARCHAR(255)` | Yes | `NULL` | None | Relative file path to G.O. PDF |
| `rejection_note` | `TEXT` | Yes | `NULL` | None | Remarks explaining rejection or revision |
| `assigned_inspector_id` | `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Field inspector user ID (`users.id`) |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Multi-tenancy branch (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `LeaseApplication`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `district()`: `BelongsTo(District::class)`
- `category()`: `BelongsTo(LeaseCategory::class, 'category_id')`
- `mineral()`: `BelongsTo(Mineral::class)`
- `minerals()`: `BelongsToMany(Mineral::class, 'lease_application_minerals')`
- `surveyNumbers()`: `HasMany(LeaseSurveyNumber::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'lease')`
- `miningApplications()`: `HasMany(MiningApplication::class)`
- `environmentProjects()`: `HasMany(EnvironmentProject::class)`
- `dgpsSurveys()`: `HasMany(DgpsSurvey::class)`
- `droneSurveys()`: `HasMany(DroneSurvey::class)`

---

### 2.2 `mining_applications`
- **Domain:** 6-Stage Mining Plan Workflow (Rules 41 & 42 of TNMMCR 1959).
- **Migration Sources:**
  - `database/migrations/2026_09_04_000004_create_mining_module_tables.php` (Lines 14–53)
  - `database/migrations/2026_09_15_115352_add_nature_of_work_id_to_mining_applications_table.php`
  - `database/migrations/2026_09_15_130804_make_minerals_and_plans_nullable_in_mining_applications.php`
  - `database/migrations/2026_09_16_095500_add_common_id_to_lease_and_mining_tables.php` (Lines 26–36)
  - `database/migrations/2026_09_16_145912_add_other_mineral_name_to_mining_applications.php`
  - `database/migrations/2026_09_22_000002_add_payment_fields_to_applications_tables.php` (Lines 32–48)
- **Model File:** `app/Models/MiningApplication.php` (156 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `common_id` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Universal common ID (`GTMS-YYYY-XXXX`) |
| `application_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Mining plan number (`MP-YYYY-XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease application (`lease_applications.id`) |
| `nature_of_work_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Nature of work (`nature_of_works.id`) |
| `parent_plan_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Self-referencing parent plan if revision |
| `applicant_type_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Legal entity type (`applicant_types.id`) |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Quarry district (`districts.id`) |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`) |
| `other_mineral_name` | `VARCHAR(255)` | Yes | `NULL` | None | Custom mineral designation |
| `plan_type_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Mining plan category (`plan_types.id`) |
| `taluk` | `VARCHAR(255)` | No | None | None | Revenue administrative taluk |
| `village` | `VARCHAR(255)` | No | None | None | Revenue administrative village |
| `survey_numbers_text` | `TEXT` | Yes | `NULL` | None | Comma-separated revenue survey numbers |
| `area_extent_ha` | `DECIMAL(10,2)` | No | None | None | Quarry mine area in Hectares |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `start_date` | `DATE` | Yes | `NULL` | None | Plan operational commencement date |
| `end_date` | `DATE` | Yes | `NULL` | None | Plan operational expiration date |
| `validity_years` | `INT` | Yes | `5` | None | Statutory validity period in years |
| `stage` | `VARCHAR(255)` | No | `'6.1'` | None | Current workflow stage (`'6.1'` to `'6.6'`) |
| `status` | `ENUM(...)` | No | `'draft'` | None | `'draft','submitted','scrutiny','inspection','approved','rejected'` |
| `rqp_name` | `VARCHAR(255)` | Yes | `NULL` | None | Name of Recognized Qualified Person |
| `rqp_reg_no` | `VARCHAR(255)` | Yes | `NULL` | None | RQP registration identifier |
| `safety_distance_meters`| `DECIMAL(10,2)`| Yes | `NULL` | None | Buffer distance from public boundaries |
| `assigned_inspector_id` | `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Assigned inspecting officer (`users.id`) |
| `approval_order_no` | `VARCHAR(255)` | Yes | `NULL` | None | Official approval order reference |
| `approval_date` | `DATE` | Yes | `NULL` | None | Official date of approval |
| `approval_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to signed approval order PDF |
| `kml_file_path` | `VARCHAR(255)` | Yes | `NULL` | None | Path to uploaded GIS KML boundary file |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `MiningApplication`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `leaseApplication()`: `BelongsTo(LeaseApplication::class)`
- `district()`: `BelongsTo(District::class)`
- `mineral()`: `BelongsTo(Mineral::class)`
- `minerals()`: `BelongsToMany(Mineral::class, 'mining_application_minerals')`
- `natureOfWork()`: `BelongsTo(NatureOfWork::class)`
- `planType()`: `BelongsTo(PlanType::class)`
- `boundaryPoints()`: `HasMany(MiningBoundaryPoint::class)`
- `productionSchedules()`: `HasMany(MiningProductionSchedule::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'mining')`
- `environmentProjects()`: `HasMany(EnvironmentProject::class)`

---

### 2.3 `environment_projects`
- **Domain:** SEIAA / DEIAA Environmental Clearance Projects (Category B1 / B2).
- **Migration Sources:**
  - `database/migrations/2026_09_04_000005_create_environment_and_ec_module_tables.php` (Lines 14–40)
  - `database/migrations/2026_09_18_001_add_sub_category_to_environment_projects.php`
  - `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php` (Lines 14–39)
  - `database/migrations/2026_09_23_164025_add_b1_stages_to_environment_and_ppt_tables.php` (Lines 15–25)
  - `database/migrations/2026_09_25_000001_migrate_environment_subcategories_to_tor_and_eta.php`
  - `database/migrations/2026_09_25_000002_add_secondary_phone_to_applications_tables.php`
- **Model File:** `app/Models/EnvironmentProject.php` (180 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `project_code` | `VARCHAR(255)` | No | None | `UNIQUE` | Unique project code (`ENV/B1/YYYY/XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `mining_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked mining plan (`mining_applications.id`) |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked lease (`lease_applications.id`) |
| `category` | `ENUM('B1','B2')` | No | None | None | EIA category classification |
| `sub_category` | `VARCHAR(50)` | Yes | `NULL` | None | B1 Sub-category (`'TOR'`, `'ETA'`, `'SC1'`, `'SC2'`) |
| `b1_stage` | `VARCHAR(50)` | Yes | `'sc1_prep'`| None | Sequential B1 lifecycle state |
| `ppt_stage_1_id` | `BIGINT UNSIGNED` | Yes | `NULL` | None | Link to Stage 1 ToR Presentation dossier |
| `ppt_stage_2_id` | `BIGINT UNSIGNED` | Yes | `NULL` | None | Link to Stage 2 Final EC Presentation dossier |
| `project_name` | `VARCHAR(255)` | No | None | None | Official title of environmental project |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`) |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Site location description |
| `contact_name` | `VARCHAR(255)` | Yes | `NULL` | None | Project focal point contact name |
| `secondary_contact_person`| `VARCHAR(255)`| Yes | `NULL` | None | Secondary site contact person |
| `contact_phone` | `VARCHAR(255)` | Yes | `NULL` | None | Primary telephone contact number |
| `secondary_phone` | `VARCHAR(255)` | Yes | `NULL` | None | Secondary telephone contact number |
| `contact_email` | `VARCHAR(255)` | Yes | `NULL` | None | Contact email address |
| `public_hearing_date` | `DATE` | Yes | `NULL` | None | Date of mandatory public hearing (B1) |
| `public_hearing_minutes_file`| `VARCHAR(255)`| Yes | `NULL` | None | Path to signed public hearing minutes PDF |
| `status` | `VARCHAR(100)` | No | `'draft'` | None | Project status (`'draft'`, `'documents_pending'`, etc.) |
| `status_notes` | `TEXT` | Yes | `NULL` | None | Operational notes regarding current status |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `EnvironmentProject`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `miningApplication()`: `BelongsTo(MiningApplication::class)`
- `leaseApplication()`: `BelongsTo(LeaseApplication::class)`
- `district()`: `BelongsTo(District::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'environment')`
- `payments()`: `HasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'environment')`
- `ecCertificates()`: `HasMany(EcCertificate::class)`
- `pptApplications()`: `HasMany(PptApplication::class)`

---

### 2.4 `ppt_applications`
- **Domain:** Committee Technical Appraisal Presentations (SEAC / SEIAA).
- **Migration Sources:**
  - `database/migrations/2026_09_04_000006_create_ppt_and_survey_module_tables.php` (Lines 14–37)
  - `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`
  - `database/migrations/2026_09_23_164025_add_b1_stages_to_environment_and_ppt_tables.php` (Lines 27–34)
  - `database/migrations/2026_09_25_000002_add_secondary_phone_to_applications_tables.php`
  - `database/migrations/2026_09_26_100000_make_status_flexible_across_ec_and_ppt_tables.php`
- **Model File:** `app/Models/PptApplication.php` (92 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `application_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Presentation application number (`PPT-YYYY-XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `environment_project_id`| `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Associated EC project (`environment_projects.id`) |
| `presentation_stage` | `VARCHAR(50)` | No | `'tor_presentation'`| None | Presentation gate (`'tor_presentation'`, `'ec_presentation'`) |
| `project_name` | `VARCHAR(255)` | No | None | None | Presentation project title |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`) |
| `taluk_village` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative location |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`) |
| `status` | `VARCHAR(100)` | No | `'draft'` | None | Status: `'draft'`, `'agenda_scheduled'`, `'presented'`, etc. |
| `status_notes` | `TEXT` | Yes | `NULL` | None | Appraisal committee follow-up notes |
| `rqp_attending` | `VARCHAR(255)` | Yes | `NULL` | None | Name of RQP defending presentation |
| `company_rep_attending`| `VARCHAR(255)`| Yes | `NULL` | None | Company representative attending SEAC |
| `rep_mobile` | `VARCHAR(255)` | Yes | `NULL` | None | Primary representative mobile number |
| `rep_secondary_mobile` | `VARCHAR(255)` | Yes | `NULL` | None | Secondary representative mobile number |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `PptApplication`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `environmentProject()`: `BelongsTo(EnvironmentProject::class)`
- `district()`: `BelongsTo(District::class)`
- `mineral()`: `BelongsTo(Mineral::class)`
- `agendas()`: `HasMany(PptAgenda::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'ppt')`
- `payments()`: `HasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'ppt')`

---

### 2.5 `dgps_surveys`
- **Domain:** Differential GPS Cadastral Boundary Fixation & Pillar Demarcation.
- **Migration Sources:**
  - `database/migrations/2026_09_04_000006_create_ppt_and_survey_module_tables.php` (Lines 62–93)
  - `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`
- **Model File:** `app/Models/DgpsSurvey.php` (100 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `survey_no` | `VARCHAR(255)` | No | None | `UNIQUE` | DGPS survey number (`DGPS-YYYY-XXXX`) |
| `field_book_no` | `VARCHAR(255)` | Yes | `NULL` | None | Surveyor field register book reference |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease (`lease_applications.id`) |
| `mining_application_id`| `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Associated mining plan (`mining_applications.id`) |
| `lease_area_ha` | `DECIMAL(10,2)` | No | None | None | Concession area as per revenue records |
| `surveyed_area_ha` | `DECIMAL(10,2)` | No | None | None | Computed area derived from field coordinates |
| `area_discrepancy_ha` | `DECIMAL(10,2)` | Yes | `NULL` | None | Variance between statutory and surveyed area |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Geographical site location |
| `survey_date` | `DATE` | No | None | None | Date of field survey execution |
| `surveyor_user_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Field surveyor staff account (`users.id`) |
| `survey_team_notes` | `TEXT` | Yes | `NULL` | None | Technical notes on terrain and benchmarks |
| `instrument_model` | `VARCHAR(255)` | Yes | `NULL` | None | GNSS receiver model (Trimble, Leica) |
| `instrument_serial_no` | `VARCHAR(255)` | Yes | `NULL` | None | Manufacturer serial number of instrument |
| `survey_status` | `ENUM(...)` | No | `'field_done'`| None | `'scheduled'`, `'field_done'`, `'computed'`, `'verified'` |
| `report_status` | `ENUM(...)` | No | `'draft'` | None | `'draft'`, `'submitted'`, `'approved'` |
| `gtm_report_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to generated GTM survey report PDF |
| `autocad_dwg_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to raw AutoCAD `.dwg` boundary drawing |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `DgpsSurvey`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `leaseApplication()`: `BelongsTo(LeaseApplication::class)`
- `miningApplication()`: `BelongsTo(MiningApplication::class)`
- `surveyor()`: `BelongsTo(User::class, 'surveyor_user_id')`
- `points()`: `HasMany(DgpsPoint::class)`
- `documents()`: `HasMany(DgpsDocument::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'dgps')`
- `payments()`: `HasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'dgps')`

---

### 2.6 `drone_surveys`
- **Domain:** UAV Aerial Photogrammetry & 3D Pit Excavation Volumetric Audits.
- **Migration Sources:**
  - `database/migrations/2026_09_04_000006_create_ppt_and_survey_module_tables.php` (Lines 111–142)
  - `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`
- **Model File:** `app/Models/DroneSurvey.php` (90 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `survey_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Drone survey number (`DRONE-YYYY-XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease (`lease_applications.id`) |
| `mining_application_id`| `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Associated mining plan (`mining_applications.id`) |
| `lease_area` | `DECIMAL(10,2)` | Yes | `NULL` | None | Total quarry land area surveyed |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Geographical site location |
| `flight_date` | `DATE` | No | None | None | Date of UAV flight execution |
| `drone_pilot_name` | `VARCHAR(255)` | Yes | `NULL` | None | DGCA-certified UAV remote pilot name |
| `pilot_rpc_no` | `VARCHAR(255)` | Yes | `NULL` | None | DGCA Remote Pilot Certificate number |
| `drone_uin_no` | `VARCHAR(255)` | Yes | `NULL` | None | Unique Identification Number on DigitalSky |
| `drone_model` | `VARCHAR(255)` | Yes | `NULL` | None | UAV hardware model (e.g. DJI Matrice 300) |
| `altitude_meters` | `DECIMAL(8,2)` | Yes | `NULL` | None | Flight altitude above ground level (AGL) |
| `gsd_cm_px` | `DECIMAL(8,2)` | Yes | `NULL` | None | Ground Sampling Distance resolution |
| `extracted_volume_cbm` | `DECIMAL(12,2)` | Yes | `NULL` | None | Calculated pit excavation volume in CBM |
| `survey_status` | `ENUM(...)` | No | `'flown'` | None | `'scheduled'`, `'flown'`, `'processing'`, `'completed'` |
| `deliverable_files_path`| `VARCHAR(255)`| Yes | `NULL` | None | Directory path to GeoTIFF orthomosaics & DEM |
| `gtms_report_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to generated volumetric audit PDF |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `DroneSurvey`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `leaseApplication()`: `BelongsTo(LeaseApplication::class)`
- `miningApplication()`: `BelongsTo(MiningApplication::class)`
- `documents()`: `HasMany(DroneDocument::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'drone')`
- `payments()`: `HasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'drone')`

---

### 2.7 `ec_certificates`
- **Domain:** Official Granted Environmental Clearances issued by SEIAA / MoEFCC.
- **Migration Sources:**
  - `database/migrations/2026_09_04_000005_create_environment_and_ec_module_tables.php` (Lines 57–79)
  - `database/migrations/2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php`
  - `database/migrations/2026_09_26_100000_make_status_flexible_across_ec_and_ppt_tables.php`
- **Model File:** `app/Models/EcCertificate.php` (87 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `ec_ref_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Official SEIAA clearance order number |
| `environment_project_id`| `BIGINT UNSIGNED`| No | None | `FOREIGN KEY` | Precursor EC project (`environment_projects.id`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked lease application (`lease_applications.id`) |
| `parivesh_app_no` | `VARCHAR(255)` | Yes | `NULL` | None | PARIVESH online portal application number |
| `applicant_name` | `VARCHAR(255)` | No | None | None | Legal grantee entity name |
| `issue_date` | `DATE` | No | None | None | Date EC certificate was granted |
| `expiry_date` | `DATE` | No | None | None | Date EC certificate expires |
| `validity_years` | `INT` | No | `5` | None | Clearance statutory validity period |
| `communication_type` | `ENUM(...)` | No | `'Grant'` | None | `'Grant'`, `'Rejection'`, `'ToR'` |
| `certificate_file` | `VARCHAR(255)` | No | None | None | Path to signed EC certificate PDF |
| `conditions_summary` | `TEXT` | Yes | `NULL` | None | Specific environmental safeguard conditions |
| `status` | `VARCHAR(100)` | No | `'active'` | None | Status: `'active'`, `'expired'`, `'surrendered'`, etc. |
| `status_notes` | `TEXT` | Yes | `NULL` | None | Operational notes regarding clearance state |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')` | No | `'pending'` | None | Commercial payment status |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

#### Model Relationships in `EcCertificate`:
- `customer()`: `BelongsTo(Customer::class)->withTrashed()`
- `environmentProject()`: `BelongsTo(EnvironmentProject::class)`
- `leaseApplication()`: `BelongsTo(LeaseApplication::class)`
- `handlers()`: `HasMany(ApplicationHandler::class, 'application_id')->where('application_type', 'ec')`
- `payments()`: `HasMany(ApplicationPayment::class, 'application_id')->where('application_type', 'ec')`

---

### 2.8 Complementary 8th Statutory Table: `ec_compliances`
- **Domain:** Statutory 6-Month Environmental Clearance Monitoring & MoEFCC Parivesh Compliance.
- **Migration Sources:**
  - `database/migrations/2026_09_23_000002_create_ec_compliances_tables.php` (Lines 14–56)
  - `database/migrations/2026_09_25_165017_add_project_name_and_ec_certificate_file_to_ec_compliances_table.php`
  - `database/migrations/2026_09_25_170055_add_contact_persons_and_phones_to_ec_compliances_table.php`
- **Model File:** `app/Models/EcCompliance.php` (108 lines)
- **Key Traits:** `HasFactory`, `SoftDeletes`, `BelongsToBranch`

#### Column Schema Definition:
| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID |
| `compliance_no` | `VARCHAR(50)` | No | None | `UNIQUE` | Unique compliance code (`ECC-YYYY-XXXX`) |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`) |
| `primary_contact_person`| `VARCHAR(255)` | Yes | `NULL` | None | Site in-charge contact person |
| `primary_phone` | `VARCHAR(20)` | Yes | `NULL` | None | Primary mobile phone number |
| `secondary_contact_person`| `VARCHAR(255)`| Yes | `NULL` | None | Secondary supervisor contact person |
| `secondary_phone` | `VARCHAR(20)` | Yes | `NULL` | None | Secondary phone number |
| `environment_project_id`| `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Precursor EC project (`environment_projects.id`) |
| `environment_project_name`| `VARCHAR(255)`| Yes | `NULL` | None | Cached EC project title |
| `ec_certificate_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Governing EC certificate (`ec_certificates.id`) |
| `ec_certificate_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to EC certificate file |
| `ec_certificate_name` | `VARCHAR(255)` | Yes | `NULL` | None | Cached EC certificate reference name |
| `project_name` | `VARCHAR(255)` | No | None | None | Title of compliance dossier |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`) |
| `taluk_village` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative location |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Subject mineral (`minerals.id`) |
| `compliance_period` | `VARCHAR(100)` | No | None | `INDEX` | Half-yearly cycle (e.g. `'April 2026 - Sept 2026'`) |
| `compliance_year` | `VARCHAR(10)` | No | `'2026'` | None | Compliance calendar year |
| `submission_due_date` | `DATE` | Yes | `NULL` | None | Statutory filing deadline (June 1 / Dec 1) |
| `submission_date` | `DATE` | Yes | `NULL` | None | Actual submission date to MoEFCC |
| `parivesh_app_no` | `VARCHAR(100)` | Yes | `NULL` | None | PARIVESH portal application number |
| `parivesh_acknowledgement_no`| `VARCHAR(100)`| Yes | `NULL` | None | PARIVESH filing acknowledgment number |
| `parivesh_uploaded_date`| `DATE` | Yes | `NULL` | None | Date uploaded to PARIVESH portal |
| `nabl_lab_name` | `VARCHAR(255)` | Yes | `NULL` | None | Testing lab name (air, water, noise) |
| `nabl_certificate_no` | `VARCHAR(100)` | Yes | `NULL` | None | NABL laboratory accreditation number |
| `monitoring_date` | `DATE` | Yes | `NULL` | None | Date environmental sampling was performed |
| `status` | `ENUM(...)` | No | `'draft'` | None | `'draft','documents_collected','lab_analysed',...` |
| `status_notes` | `TEXT` | Yes | `NULL` | None | Statutory officer remarks |
| `product_value` | `DECIMAL(12,2)` | No | `0.00` | None | Commercial contracted project fee |
| `paid_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Amount collected to date |
| `pending_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Outstanding balance receivable |
| `payment_status` | `ENUM('pending','partial','paid')`| No | `'pending'`| None | Commercial payment status |
| `payment_notes` | `TEXT` | Yes | `NULL` | None | Commercial accounting notes |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Branch office mapping (`branches.id`) |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting operator user ID (`users.id`) |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp |

---

## 3. Analysis of Existing Payment Architecture

### 3.1 Schema of `application_payments` Table
The universal polymorphic payment ledger was established in migration `2026_09_22_000002_add_payment_fields_to_applications_tables.php` (Lines 50–64):

```sql
CREATE TABLE `application_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `application_type` VARCHAR(50) NULL,
  `application_id` BIGINT UNSIGNED NULL,
  `payable_type` VARCHAR(255) NULL,
  `payable_id` BIGINT UNSIGNED NULL,
  `product_value` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `paid_amount` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `pending_amount` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
  `payment_status` ENUM('pending','partial','paid') NOT NULL DEFAULT 'pending',
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_app_type` (`application_type`),
  INDEX `idx_app_id` (`application_id`),
  INDEX `idx_payable_type_id` (`payable_type`, `payable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.2 Eloquent Model: `App\Models\ApplicationPayment`
Located at `app/Models/ApplicationPayment.php` (41 lines):
- **Fillable Attributes:** `application_type`, `application_id`, `payable_type`, `payable_id`, `product_value`, `paid_amount`, `pending_amount`, `payment_status`, `notes`.
- **Casts:** `product_value => decimal:2`, `paid_amount => decimal:2`, `pending_amount => decimal:2`.
- **Polymorphic Relationship:**
  ```php
  public function payable(): MorphTo
  {
      return $this->morphTo();
  }
  ```

### 3.3 Existing Controller Persistence Mechanism
In the existing application wizards (e.g. `MiningController.php` lines 358–372, `CustomerController.php` lines 771–784), `ApplicationPayment` is maintained as a **1-to-1 aggregate balance mirror** using `updateOrCreate`:

```php
ApplicationPayment::updateOrCreate(
    [
        'application_type' => 'mining', // or 'lease', 'environment', 'ppt', 'dgps', 'drone', 'ec'
        'application_id'   => $application->id,
    ],
    [
        'payable_type'   => get_class($application),
        'payable_id'     => $application->id,
        'product_value'  => $prodVal,
        'paid_amount'    => $paidVal,
        'pending_amount' => $pendingVal,
        'payment_status' => $payStatus,
        'notes'          => $notes,
    ]
);
```

### 3.4 Critical Architectural Gap Analysis
1. **Balance Mirror vs. Transaction Ledger:** Currently, `application_payments` only holds the *current balance state* for an application. If a quarry owner makes 3 partial payments (e.g., ₹50,000 advance, ₹40,000 on survey, ₹30,000 on approval), each submission overwrites `paid_amount` and `pending_amount`. There is **no persistent transaction receipt history** recording who collected the payment, which bank reference/cheque/UTR was used, what date the installment was deposited, or the previous balance snapshot.
2. **Missing Receipt Entity:** GTMS currently has no table to store sequential official receipt vouchers (`GTMS/REC/2026/001`) with amount in words, payment mode, bank name, and issuing officer timestamps.
3. **No Direct Customer Foreign Key:** `application_payments` lacks a `customer_id` column, requiring an expensive N+1 polymorphic query chain (`payable->customer_id`) to generate customer-level financial statements.

---

## 4. Customer and Quarry Concession Architecture

### 4.1 Schema of `customers` Table
- **Migration Sources:**
  - `2026_09_04_000002_create_customers_table.php` (Original creation)
  - `2026_09_04_000008_add_slug_to_customers_table.php` (`slug` UNIQUE)
  - `2026_09_07_050811_add_mimas_no_to_customers_table.php` (`mimas_no` UNIQUE)
  - `2026_09_10_102535_add_aadhaar_no_to_customers_table.php` (`aadhaar_no` UNIQUE)
  - `2026_09_16_104641_add_secondary_contact_to_customers_and_leases.php` (`secondary_contact_person`, `secondary_mobile_num`)
  - `2026_09_16_120500_add_mimas_number_and_status_to_customers_table.php` (`mimas_number`, `mimas_status`)
  - `2026_09_24_000001_make_pan_nullable_in_customers_table.php` (`pan` nullable)
- **Model File:** `app/Models/Customer.php` (144 lines)

#### Key Columns on `customers`:
- `id`: `BIGINT UNSIGNED`, Primary Key
- `customer_name`: `VARCHAR(255)`, Representative or individual applicant name
- `company_name`: `VARCHAR(255)`, Quarry firm or enterprise name
- `slug`: `VARCHAR(255)`, URL-safe identifier for customer routing
- `mimas_no`: `VARCHAR(255)`, State TN Mines Tenement Portal unique customer ID (Unique)
- `mobile_num`: `VARCHAR(255)`, Primary contact phone
- `secondary_mobile_num`: `VARCHAR(255)`, Secondary phone
- `email`: `VARCHAR(255)`, Contact email
- `district_id`: `BIGINT UNSIGNED`, FK -> `districts.id`
- `mineral_id`: `BIGINT UNSIGNED`, FK -> `minerals.id`
- `pan`: `VARCHAR(10)`, Indian Income Tax PAN
- `aadhaar_no`: `VARCHAR(20)`, 12-digit Indian Unique Identification (Unique)
- `gstin`: `VARCHAR(15)`, 15-character Goods and Services Tax ID
- `area`: `DECIMAL(10,2)`, Total cumulative quarry extent in Hectares
- `address`: `TEXT`, Registered correspondence address
- `status`: `TINYINT`, 1 = Active, 0 = Inactive

### 4.2 Empirical Ground Truth Regarding `customer_quarry_concessions`
The task dispatch prompts investigation of:
> *"Customer and quarry concession schema (`customers`, `customer_quarry_concessions`) and how they link to applications."*

**Verified Finding:**  
There is **NO physical table named `customer_quarry_concessions`** in the existing 64 tables of the GTMS database.  
Instead, quarry concessions in GTMS are **dynamically represented by `lease_applications`** (and secondary standalone chains in `mining_applications`, `environment_projects`, etc.).

### 4.3 How Quarry Concessions Are Resolved in GTMS
As verified in `CustomerTrackingController.php` (Lines 660–730 and 995–1040), a customer's individual quarry concessions are aggregated through their `leaseApplications` collection:
1. Each `LeaseApplication` record represents one distinct quarry concession site, holding:
   - Location: `village`, `taluk`, `district_id` (joined to `districts.name`)
   - Land Extent: `area_extent_ha` and `area_extent_acres`
   - Cadastral Plots: `surveyNumbers` relationship querying `lease_survey_numbers` table (`survey_no`, `extent_ha`, `pattadar_name`)
   - Concession Mineral: `mineral_id` / `minerals` pivot / `other_mineral_name`
   - Common Identifier: `common_id` (`GTMS-YYYY-XXXX`)
2. For high-volume enterprise clients (e.g. Kaveri Granites with 264 concessions across 8 districts), `CustomerTrackingController` builds an array of `fullCycleChains`:
   - Each chain contains: `ref_no`, `village`, `taluk`, `district_name`, `area_extent_ha`, `mineral_name`, and comma-separated `survey_nos`.
3. If an applicant engages GTMS for direct standalone engineering (e.g. DGPS boundary survey or Drone scan without a precursor lease on file), `CustomerTrackingController` resolves them as `standaloneServices`.

### 4.4 Architectural Design Strategy for Concessions in the Accounts Module
When issuing a Quotation or collecting a Payment, staff must associate the billing with the client's quarry concession. Because clients may request quotations *prior* to filing a statutory lease application (pre-sales phase), the Accounts module must accommodate both existing and prospective concessions:
- **Strategy (Recommended):** Store concession metadata directly on `quotations` (`quarry_name`, `district_id`, `taluk`, `village`, `survey_numbers`, `area_extent_ha`, `mineral_name`), with optional foreign keys `lease_application_id` and `mining_application_id`. When a user selects a customer in the Quotation form, an AJAX call to `/accounts/customers/{id}/concessions` returns all existing concessions from their `leaseApplications` to auto-fill the form, while still permitting manual editing for new prospective concessions.
- **Dedicated Master Option:** If the system architect desires a physical `customer_quarry_concessions` registry, we have formulated the exact migration specification in Section 5.4.

---

## 5. New Database Migration Specifications for Accounts Module

To support Requirements R1 through R6, the Accounts module requires 3 core database tables (`quotations`, `quotation_items`, `payment_receipts`) and an optional concession master table.

---

### 5.1 Migration 1: `create_quotations_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no', 50)->unique()->comment('e.g. GTMS/QTN/2026/001');
            $table->date('quotation_date')->index();
            $table->date('valid_until')->nullable();

            // Client & Concession Relations
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->foreignId('mining_application_id')->nullable()->constrained('mining_applications')->nullOnDelete();

            // Quarry Concession Metadata Snapshot (frozen at quotation issuance)
            $table->string('quarry_name')->nullable()->comment('Quarry site or concession name');
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->string('taluk', 255)->nullable();
            $table->string('village', 255)->nullable();
            $table->string('survey_numbers', 255)->nullable()->comment('S.F. Nos. e.g. 102/1A, 102/1B');
            $table->decimal('area_extent_ha', 10, 4)->nullable()->comment('Quarry land area in Hectares');
            $table->string('mineral_name', 255)->nullable();

            // Financial & Commercial Totals
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->enum('tax_type', ['gst_18', 'exempt', 'none'])->default('gst_18');
            $table->decimal('cgst_rate', 5, 2)->default(9.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00);
            $table->decimal('sgst_rate', 5, 2)->default(9.00);
            $table->decimal('sgst_amount', 12, 2)->default(0.00);
            $table->decimal('igst_rate', 5, 2)->default(0.00);
            $table->decimal('igst_amount', 12, 2)->default(0.00);
            $table->decimal('total_tax', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);

            // Statutory Terms & Exclusions
            $table->text('payment_terms')->nullable()->comment('Milestone payment breakdown');
            $table->text('exclusions')->nullable()->comment('Government statutory challans & seigniorage fees excluded');
            $table->text('notes')->nullable();

            // Status Workflow
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'invoiced', 'expired'])->default('draft')->index();

            // Multi-Tenancy & Audit
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // High-concurrency indices
            $table->index(['customer_id', 'status']);
            $table->index(['district_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
```

---

### 5.2 Migration 2: `create_quotation_items_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();

            $table->string('service_code', 50)->nullable()->comment('e.g. dgps, drone, mining_plan, ec_b2, tnpcb');
            $table->string('service_title', 255)->comment('Official statutory deliverable title');
            $table->text('description')->nullable()->comment('Detailed scope bullet points');
            $table->string('sac_code', 20)->nullable()->default('998343')->comment('Services Accounting Code for GST');
            
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit', 50)->default('Unit')->comment('Ha, Pillars, Flight, Filing, Report');
            $table->decimal('unit_rate', 12, 2)->default(0.00);
            $table->decimal('amount', 12, 2)->default(0.00);

            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['quotation_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
```

---

### 5.3 Migration 3: `create_payment_receipts_table`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 50)->unique()->comment('e.g. GTMS/REC/2026/001');
            $table->date('receipt_date')->index();

            // Client & Target Relations
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();

            // Target Application Reference (Polymorphic & Direct)
            $table->string('application_type', 50)->nullable()->index()->comment('lease, mining, environment, ppt, dgps, drone, ec, ec_compliance');
            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->nullableMorphs('payable'); // payable_type, payable_id

            // Financial Transaction Details
            $table->decimal('amount_paid', 12, 2);
            $table->enum('payment_mode', ['cash', 'cheque', 'neft_rtgs', 'upi', 'demand_draft'])->default('neft_rtgs');
            $table->string('reference_no', 100)->nullable()->comment('Cheque No, UTR Ref, UPI Transaction ID');
            $table->string('bank_name', 150)->nullable();
            $table->date('payment_date');

            // Running Balance Snapshot (for immediate receipt voucher print)
            $table->decimal('previous_paid', 12, 2)->default(0.00);
            $table->decimal('remaining_balance', 12, 2)->default(0.00);
            $table->text('notes')->nullable();

            // Multi-Tenancy & Officer Audit
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete()->comment('Officer collecting the payment');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // High-concurrency indices
            $table->index(['customer_id', 'payment_date']);
            $table->index(['application_type', 'application_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
```

---

### 5.4 Optional Migration 4: `create_customer_quarry_concessions_table`
If a standalone concession table is explicitly implemented:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_quarry_concessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('concession_name', 255)->comment('Quarry Site Name e.g. Omalur Black Granite Pit #2');
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->string('taluk', 255);
            $table->string('village', 255);
            $table->string('survey_numbers', 255)->comment('S.F. Numbers e.g. 104/2B, 105/1');
            $table->decimal('area_extent_ha', 10, 4);
            $table->foreignId('mineral_id')->nullable()->constrained('minerals')->nullOnDelete();
            $table->string('other_mineral_name', 255)->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index(['district_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_quarry_concessions');
    }
};
```

---

## 6. Atomic Payment Synchronization Workflow (R2 & R3)

### 6.1 Transaction Flow & Atomicity
When an officer records a payment via the Centralized Payment Collection interface:
```
[User Form Submit]
       │
       ▼
[DB::transaction Begins]
       │
       ├─► 1. Lock Target Statutory Application record (select for update)
       │      e.g. MiningApplication::lockForUpdate()->find($id)
       │
       ├─► 2. Compute New Financial Metrics:
       │      $newPaidAmount    = $app->paid_amount + $request->amount_paid;
       │      $newPendingAmount = max(0.00, $app->product_value - $newPaidAmount);
       │      $newPaymentStatus = ($newPendingAmount <= 0 && $app->product_value > 0)
       │                          ? 'paid' : ($newPaidAmount > 0 ? 'partial' : 'pending');
       │
       ├─► 3. Atomically Update Statutory Application Table:
       │      $app->update([
       │          'paid_amount'    => $newPaidAmount,
       │          'pending_amount' => $newPendingAmount,
       │          'payment_status' => $newPaymentStatus,
       │      ]);
       │
       ├─► 4. Atomically Update/Create Polymorphic application_payments row:
       │      ApplicationPayment::updateOrCreate(
       │          ['application_type' => $appType, 'application_id' => $app->id],
       │          [
       │              'payable_type'   => get_class($app),
       │              'payable_id'     => $app->id,
       │              'product_value'  => $app->product_value,
       │              'paid_amount'    => $newPaidAmount,
       │              'pending_amount' => $newPendingAmount,
       │              'payment_status' => $newPaymentStatus,
       │              'notes'          => "Collection of ₹{$request->amount_paid} via {$request->payment_mode} (Ref: {$request->reference_no})",
       │          ]
       │      );
       │
       ├─► 5. Insert Official Payment Receipt Record:
       │      $receipt = PaymentReceipt::create([
       │          'receipt_no'        => $generatedReceiptNo, // e.g. GTMS/REC/2026/001
       │          'receipt_date'      => $request->payment_date,
       │          'customer_id'       => $app->customer_id,
       │          'application_type'  => $appType,
       │          'application_id'    => $app->id,
       │          'payable_type'      => get_class($app),
       │          'payable_id'        => $app->id,
       │          'amount_paid'       => $request->amount_paid,
       │          'payment_mode'      => $request->payment_mode,
       │          'reference_no'      => $request->reference_no,
       │          'bank_name'         => $request->bank_name,
       │          'payment_date'      => $request->payment_date,
       │          'previous_paid'     => $app->paid_amount - $request->amount_paid,
       │          'remaining_balance' => $newPendingAmount,
       │          'notes'             => $request->notes,
       │          'received_by'       => Auth::id(),
       │          'branch_id'         => Auth::user()->branch_id ?? null,
       │          'created_by'        => Auth::id(),
       │      ]);
       │
       ▼
[DB::transaction Commits]
       │
       ▼
[Redirect to Receipt Voucher View / Print: route('accounts.receipts.show', $receipt->id)]
```

### 6.2 Sequential Numbering Algorithm (Collision-Resistant)
To comply with the `Substr_Numeric_Sequence_Extraction_Trap` lesson in `lessons_learned.md`:
- Do NOT use hardcoded negative substring lengths.
- Extract the sequential counter using `strrpos` and wrap within an atomic transaction:
```php
public static function generateReceiptNumber(): string
{
    $year = date('Y');
    $prefix = "GTMS/REC/{$year}/";
    
    // Find the latest receipt for current year
    $lastReceipt = PaymentReceipt::where('receipt_no', 'like', "{$prefix}%")
        ->orderByDesc('id')
        ->lockForUpdate()
        ->first();

    if ($lastReceipt) {
        $lastSeq = (int) substr($lastReceipt->receipt_no, strrpos($lastReceipt->receipt_no, '/') + 1);
        $nextSeq = $lastSeq + 1;
    } else {
        $nextSeq = 1;
    }

    $receiptNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

    // Collision check loop
    while (PaymentReceipt::where('receipt_no', $receiptNo)->exists()) {
        $nextSeq++;
        $receiptNo = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    return $receiptNo;
}
```

---

## 7. Quotation Engine Architecture (R1)

### 7.1 Standard Services & SAC Catalog
From our inspection of `CustomerTrackingController.php` (lines 1450–1600), the standard statutory service catalog and GST Services Accounting Codes (SAC) for Tamil Nadu mining engineering are:

| Service Code | Statutory Deliverable Description | SAC Code | Default Pricing Baseline |
| :--- | :--- | :--- | :--- |
| `mining_plan` | Preparation of Mining Plan & Progressive Mine Closure Plan (PMCP) under Rule 41 of TNMMCR 1959 | `998343` | ₹1,20,000 – ₹1,50,000 |
| `lease_filing` | Preparation of Mining Lease Application, Revenue Scrutiny & Statutory MMS Filing | `998341` | ₹50,000 – ₹80,000 |
| `ec_b2` | Form-1, Form-2, PFR & Environmental Management Plan (EMP) for B2 Clearance | `998349` | ₹80,000 – ₹1,00,000 |
| `ec_b1` | EIA Baseline Study, ToR Formulation, Draft 12 Chapters & Public Hearing Dossier | `998349` | ₹2,50,000 – ₹4,00,000 |
| `ppt_defense` | SEAC / SEIAA Technical Appraisal PowerPoint Presentation & Committee Defense | `998311` | ₹40,000 – ₹60,000 |
| `dgps_survey` | DGPS Boundary Survey, Baseline Control Fixation & Pillar Coordinates Demarcation | `998341` | ₹35,000 – ₹50,000 |
| `drone_survey` | Drone UAV Aerial Photogrammetry, 3D Point Cloud & Volumetric Pit Excavation Report | `998342` | ₹50,000 – ₹75,000 |
| `ec_compliance`| Environmental Clearance Half-Yearly Compliance Monitoring, NABL Testing & Parivesh Upload | `998349` | ₹30,000 – ₹45,000 |
| `tnpcb_consent`| TNPCB Consent to Establish (CTE) & Consent to Operate (CTO) Application & Scrutiny | `998349` | ₹45,000 – ₹65,000 |

### 7.2 GST Tax Calculation Rules
- **Intra-State (Tamil Nadu to Tamil Nadu):**
  - CGST @ 9% = `round(subtotal * 0.09, 2)`
  - SGST @ 9% = `round(subtotal * 0.09, 2)`
  - Total Tax = `cgst_amount + sgst_amount`
  - Grand Total = `subtotal + total_tax`
- **Inter-State:**
  - IGST @ 18% = `round(subtotal * 0.18, 2)`
- **Tax Exempt:**
  - Tax = `0.00`, Grand Total = `subtotal`

### 7.3 Indian Currency Number-to-Words Algorithm
In compliance with `lessons_learned.md` item `[Indian_Currency_Number_To_Words_System]`, numbers must be converted using Crores, Lakhs, Thousands, and Hundreds rather than Millions/Billions:
```php
public static function amountInWords(float $number): string
{
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $words = [
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
        15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty',
        50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty',
        90 => 'Ninety'
    ];
    $digits = ['', 'Hundred', 'Thousand', 'Lakh', 'Crore'];
    
    // Split into Crore (10,000,000), Lakh (100,000), Thousand (1,000), Hundred (100)
    $divider = [10000000, 100000, 1000, 100, 1];
    $res = [];
    foreach ($divider as $i => $d) {
        if ($no >= $d) {
            $val = (int)($no / $d);
            $no %= $d;
            if ($val > 0) {
                if ($val < 20) {
                    $chunk = $words[$val];
                } elseif ($val < 100) {
                    $chunk = $words[((int)($val / 10)) * 10] . (($val % 10) ? ' ' . $words[$val % 10] : '');
                } else {
                    $chunk = $words[(int)($val / 100)] . ' Hundred' . (($val % 100) ? ' and ' . self::amountInWords($val % 100) : '');
                }
                $res[] = $chunk . ($digits[4 - $i] ? ' ' . $digits[4 - $i] : '');
            }
        }
    }
    $str = implode(' ', $res) ?: 'Zero';
    $paise = ($decimal > 0) ? ' and ' . ($words[$decimal] ?? $decimal) . ' Paise' : '';
    return 'Rupees ' . $str . $paise . ' Only';
}
```

---

## 8. Customer Financial Ledger & Reporting Architecture (R4 & R5)

### 8.1 Customer Financial Dossier Ledger Logic
To generate the single-pane-of-glass Customer Ledger Statement:
1. **Debits:** All Quotations accepted/invoiced (or statutory applications contracted):
   - Type: `DEBIT (Invoice / Quotation)`
   - Ref: `quotation_no` or application `application_no`
   - Description: Service name / Mining plan preparation
   - Amount: `product_value` or `grand_total`
2. **Credits:** All Payments collected:
   - Type: `CREDIT (Payment Received)`
   - Ref: `receipt_no`
   - Description: Mode + Bank / UTR Ref
   - Amount: `amount_paid`
3. **Chronological Union Query:**
   Merge and order both by `date ASC` to compute running balances:
   `Running Balance = Previous Balance + Debit - Credit`
4. **Summary Card Metrics:**
   - Total Billed / Contracted = $\sum(\text{Debits})$
   - Total Received = $\sum(\text{Credits})$
   - Net Outstanding Due = Total Billed - Total Received

### 8.2 Reports Engine & Export Architecture
The reporting interface must support dynamic filtering:
- **Date Range:** `from_date` to `to_date`
- **Customer:** Filter by `customer_id`
- **Module:** Filter by `application_type` (`lease`, `mining`, `environment`, `ppt`, `dgps`, `drone`, `ec_certificate`, `ec_compliance`)
- **Payment Mode:** Filter by `payment_mode` (`cash`, `cheque`, `neft_rtgs`, `upi`)
- **Exporting:** Stream standard RFC 4180 CSV via `Response::stream()` with headers:
  `Content-Type: text/csv`, `Content-Disposition: attachment; filename="gtms_financial_report_{$date}.csv"`

---

## 9. UI Integration, Sidebar Navigation & Spatie RBAC Permissions (R6)

### 9.1 Spatie Permissions Registration
The system seeder (`RolePermissionSeeder.php`) and RBAC middleware must register the 4 accounts permissions:
1. `account.view` — View quotations, receipt vouchers, customer ledger, and reports.
2. `account.create` — Generate quotations and collect payments.
3. `account.edit` — Update quotation line items, terms, and draft receipts.
4. `account.delete` — Cancel/delete draft quotations.

### 9.2 Sidebar Menu Structure
In `resources/views/layouts/sidebar.blade.php`, insert under the primary operational group:

```html
@canany(['account.view', 'account.create'])
<li>
    <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
        <i class="fas fa-file-invoice-dollar"></i>
        <span class="nav-text">Accounts</span>
    </a>
    <ul aria-expanded="false">
        @can('account.view')
        <li><a href="{{ route('accounts.quotations.index') }}">Quotations</a></li>
        @endcan
        @can('account.create')
        <li><a href="{{ route('accounts.payments.create') }}">Collect Payment</a></li>
        @endcan
        @can('account.view')
        <li><a href="{{ route('accounts.ledger.index') }}">Customer Ledger</a></li>
        <li><a href="{{ route('accounts.reports.index') }}">Financial Reports</a></li>
        @endcan
    </ul>
</li>
@endcanany
```

---

## 10. Data Integrity Risks, Edge Cases & Verification Strategy

| Risk Category | Specific Risk | Mitigation in Proposed Schema |
| :--- | :--- | :--- |
| **Referential Integrity** | Deleting a customer with active quotations or receipts | `restrictOnDelete()` on `quotations.customer_id` and `payment_receipts.customer_id`. |
| **Race Conditions** | Simultaneous payments causing duplicate receipt numbers | Transaction-level pessimistic locking (`lockForUpdate()`) and uniqueness collision avoidance loops. |
| **Out-of-Sync Ledgers** | Payment collected in `payment_receipts` but target statutory table update fails | Atomic `DB::transaction(...)` enclosing application table update, `application_payments` upsert, and `payment_receipts` creation. |
| **Orphaned Line Items** | Quotation deleted leaving disconnected `quotation_items` | `cascadeOnDelete()` on `quotation_items.quotation_id`. |
| **Financial Precision** | Floating point rounding errors causing off-by-one paise discrepancies | Strict `DECIMAL(12,2)` throughout database, cast as `decimal:2` in Eloquent models, and `round(..., 2)` in PHP calculations. |
| **Soft Delete Handoffs** | Customer soft-deleted causing ledger 500 error | `BelongsTo(Customer::class)->withTrashed()` on all accounts models. |

---

## 11. Concrete Schema Matrix Summary

Below is the comparative matrix of the 8 statutory entities and their commercial ledger interfaces:

| Entity Name | Primary Table | Key Application Identifier | Model Class | Payment Status Values | Existing Payments Relation |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Lease Application** | `lease_applications` | `application_no` (`LA-YYYY-XXXX`) | `App\Models\LeaseApplication` | `'pending'`, `'partial'`, `'paid'` | Via `application_type='lease'` |
| **Mining Plan** | `mining_applications` | `application_no` (`MP-YYYY-XXXX`) | `App\Models\MiningApplication` | `'pending'`, `'partial'`, `'paid'` | Via `application_type='mining'` |
| **EC Project** | `environment_projects` | `project_code` (`ENV/B1/...`) | `App\Models\EnvironmentProject` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **SEAC Presentation**| `ppt_applications` | `application_no` (`PPT-YYYY-XXXX`) | `App\Models\PptApplication` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **DGPS Survey** | `dgps_surveys` | `survey_no` (`DGPS-YYYY-XXXX`) | `App\Models\DgpsSurvey` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **Drone Survey** | `drone_surveys` | `survey_no` (`DRONE-YYYY-XXXX`) | `App\Models\DroneSurvey` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **EC Certificate** | `ec_certificates` | `ec_ref_no` (`EC-YYYY-XXXX`) | `App\Models\EcCertificate` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **EC Compliance** | `ec_compliances` | `compliance_no` (`ECC-YYYY-XXXX`) | `App\Models\EcCompliance` | `'pending'`, `'partial'`, `'paid'` | Has `payments()` relation |
| **Universal Ledger** | `application_payments` | Polymorphic compound key | `App\Models\ApplicationPayment` | `'pending'`, `'partial'`, `'paid'` | Polymorphic root |
| **NEW Quotation** | `quotations` | `quotation_no` (`GTMS/QTN/...`) | `App\Models\Quotation` | `'draft'`, `'sent'`, `'accepted'`, etc. | Parent to items |
| **NEW Receipt** | `payment_receipts` | `receipt_no` (`GTMS/REC/...`) | `App\Models\PaymentReceipt` | Cash, Cheque, NEFT, UPI modes | Direct + Polymorphic |

This survey is complete, verified against live source code and database migrations, and immediately actionable for the lead architect and development subagents.
