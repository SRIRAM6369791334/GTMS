# GTMS Database Schema Dictionary & Migration Chronology

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Database Schema:** `gtms_data`  
**Database Engine:** MariaDB 10.4+ / MySQL 8.0+ (InnoDB Storage Engine)  
**Character Set & Collation:** `utf8mb4` / `utf8mb4_unicode_ci`  
**Total Tables:** 64 Tables  
**Total Migrations:** 48 Migrations Executed  
**Authoritative Source:** Codebase Migration Audit & Physical Database Schema Inspection  
**Document Number:** `03` of `23`  

---

## 1. Database Architecture Overview

The `gtms_data` database is a relational, multi-tenant enterprise data store architected to enforce statutory mining compliance for the state of Tamil Nadu. The schema enforces strict data integrity constraints, relational foreign keys with cascading/restrict rules, and comprehensive audit tracking.

### Key Architectural Characteristics
1. **Module-Partitioned Document Tables:** To avoid catastrophic table-locking contention during high-volume document ingestion, document attachments are segregated across dedicated tables (`lease_documents`, `mining_documents`, `environment_documents`, `ppt_documents`, `dgps_documents`, `drone_documents`, `ec_compliance_documents`) rather than a single polymorphic table.
2. **Universal Customer 360 Aggregate Root:** The `customers` table anchors all statutory and commercial activities. Every departmental application directly references `customer_id`.
3. **Universal Common Identifier (`common_id`):** An indexed, persistent tracking code formatted as `GTMS-{YEAR}-{SEQUENCE}` spans across `lease_applications` and `mining_applications`.
4. **Universal Polymorphic Financial & Personnel Ledgers:** Shared commercial accounting (`application_payments`) and personnel assignment (`application_handlers`) attach polymorphically across all departmental application types.
5. **Soft Deletion Architecture:** All primary operational aggregates implement `deleted_at` timestamps (`SoftDeletes`) to preserve legally mandated regulatory audit trails.

---

## 2. Chronological 48-Migration Audit Table

The following table records the complete chronological sequence of all 48 database migrations defining the GTMS schema:

| # | Migration File Name | Target Tables | Purpose & Schema Modifications |
| :--- | :--- | :--- | :--- |
| 1 | `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` | Creates core Laravel user authentication, password resets, and database session store. |
| 2 | `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` | Creates database-backed caching and concurrency locking tables. |
| 3 | `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` | Creates database queue worker and failure tracking infrastructure. |
| 4 | `2026_07_06_113339_create_permission_tables.php` | `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` | Creates Spatie Laravel-Permission role-based access control (RBAC) schema. |
| 5 | `2026_07_06_114649_add_role_id_and_branch_id_to_users_table.php` | `users` | Adds `role_id` and `branch_id` foreign keys to `users`. |
| 6 | `2026_07_07_093905_create_branches_table.php` | `branches` | Creates district branch offices master table. |
| 7 | `2026_07_08_070939_add_image_userid_mobile_showpass_to_users_table.php` | `users` | Adds `image`, `user_id`, `mobile_num`, and `show_password` to `users`. |
| 8 | `2026_07_08_075906_add_status_to_users_table.php` | `users` | Adds `status` tinyint column to `users`. |
| 9 | `2026_07_08_080930_change_user_id_column_in_users_table.php` | `users` | Adjusts `user_id` column formatting in `users`. |
| 10 | `2026_07_08_081111_add_user_code_to_users_table.php` | `users` | Adds `user_code` string column to `users`. |
| 11 | `2026_07_09_062952_create_categories_table.php` | `categories` | Creates legacy template product categories table. |
| 12 | `2026_07_09_064613_add_delete_status_to_categories_table.php` | `categories` | Adds `delete_status` flag to `categories`. |
| 13 | `2026_07_09_102044_create_products_table.php` | `products` | Creates legacy template products catalog table. |
| 14 | `2026_07_09_103917_add_branch_id_to_products_table.php` | `products` | Adds `branch_id` foreign key to `products`. |
| 15 | `2026_07_14_071431_create_product_stocks_table.php` | `product_stocks` | Creates legacy inventory stock level table. |
| 16 | `2026_07_17_114522_make_bar_code_nullable_in_products_table.php` | `products` | Relaxes `bar_code` nullability on `products`. |
| 17 | `2026_07_20_071331_create_units_table.php` | `units` | Creates unit of measurement master table. |
| 18 | `2026_07_20_080432_add_delete_status_to_units_table.php` | `units` | Adds `delete_status` flag to `units`. |
| 19 | `2026_08_07_000001_create_environmental_b2_tables.php` | `environmental_projects`, `environmental_documents`, `environmental_activities` | Initial B2 prototype tables (subsequently superseded by modern environment schema). |
| 20 | `2026_09_04_000001_create_gtms_master_tables.php` | `districts`, `minerals`, `lease_categories`, `plan_types`, `applicant_types`, `modules`, `folders`, `document_fields` | Creates core statutory master data registries. |
| 21 | `2026_09_04_000002_create_customers_table.php` | `customers` | Creates central Customer 360 profile registry with soft deletes. |
| 22 | `2026_09_04_000003_create_lease_module_tables.php` | `lease_applications`, `lease_survey_numbers`, `mimas_credentials`, `lease_documents` | Creates 7-step statutory lease application module tables. |
| 23 | `2026_09_04_000004_create_mining_module_tables.php` | `mining_applications`, `mining_boundary_points`, `mining_production_schedules`, `mining_documents` | Creates 6-stage mining plan preparation and approval module tables. |
| 24 | `2026_09_04_000005_create_environment_and_ec_module_tables.php` | `environment_projects`, `environment_documents`, `ec_certificates` | Creates production Environmental Clearance (EC) Category B1/B2 schema. |
| 25 | `2026_09_04_000006_create_ppt_and_survey_module_tables.php` | `ppt_applications`, `ppt_agendas`, `ppt_documents`, `dgps_surveys`, `dgps_points`, `dgps_documents`, `drone_surveys`, `drone_documents` | Creates presentation committee dossiers, DGPS boundary surveys, and drone surveys. |
| 26 | `2026_09_04_000007_create_mineral_stock_and_audit_tables.php` | `mineral_stockpiles`, `mineral_stock_entries`, `mineral_dispatches`, `project_flows`, `activity_logs`, `archived_activity_logs`, `notifications` | Creates quarry stockpile inventory, workflow flow tracking, and audit trail tables. |
| 27 | `2026_09_04_000008_add_slug_to_customers_table.php` | `customers` | Adds unique SEO `slug` to `customers`. |
| 28 | `2026_09_07_050811_add_mimas_no_to_customers_table.php` | `customers` | Adds unique `mimas_no` identifier to `customers`. |
| 29 | `2026_09_10_000001_update_lease_application_status_enum.php` | `lease_applications` | Expands `status` enum on `lease_applications`. |
| 30 | `2026_09_10_102535_add_aadhaar_no_to_customers_table.php` | `customers` | Adds unique `aadhaar_no` (12-digit) column to `customers`. |
| 31 | `2026_09_15_115323_create_nature_of_works_table.php` | `nature_of_works` | Creates statutory nature of mining works master. |
| 32 | `2026_09_15_115352_add_nature_of_work_id_to_mining_applications_table.php` | `mining_applications` | Adds `nature_of_work_id` foreign key to `mining_applications`. |
| 33 | `2026_09_15_115733_add_nature_of_work_id_to_document_fields_table.php` | `document_fields` | Adds `nature_of_work_id` foreign key to `document_fields`. |
| 34 | `2026_09_15_130804_make_minerals_and_plans_nullable_in_mining_applications.php` | `mining_applications` | Relaxes nullability on `mineral_id` and `plan_type_id` on `mining_applications`. |
| 35 | `2026_09_15_162914_create_mining_application_minerals_table.php` | `mining_application_minerals` | Creates multi-mineral selection pivot table for mining applications. |
| 36 | `2026_09_16_095500_add_common_id_to_lease_and_mining_tables.php` | `lease_applications`, `mining_applications` | Adds indexed `common_id` string and backfills historical applications. |
| 37 | `2026_09_16_104641_add_secondary_contact_to_customers_and_leases.php` | `customers`, `lease_applications` | Adds secondary contact person and secondary mobile phone numbers. |
| 38 | `2026_09_16_120500_add_mimas_number_and_status_to_customers_table.php` | `customers` | Adds `mimas_number` and `mimas_status` to `customers`. |
| 39 | `2026_09_16_122500_create_lease_application_minerals_and_other_column.php` | `lease_application_minerals`, `lease_applications` | Creates multi-mineral pivot for lease applications and adds `other_mineral_name`. |
| 40 | `2026_09_16_145912_add_other_mineral_name_to_mining_applications.php` | `mining_applications` | Adds `other_mineral_name` column to `mining_applications`. |
| 41 | `2026_09_17_000001_seed_environment_clearance_master_data.php` | Master Data | Data migration seeding B1/B2 folder checklists and regulatory document fields. |
| 42 | `2026_09_18_001_add_sub_category_to_environment_projects.php` | `environment_projects` | Adds `sub_category` enum ('SC1', 'SC2') to `environment_projects`. |
| 43 | `2026_09_22_000001_create_application_handlers_table.php` | `application_handlers` | Creates universal polymorphic application personnel assignment table. |
| 44 | `2026_09_22_000002_add_payment_fields_to_applications_tables.php` | `lease_applications`, `mining_applications`, `application_payments` | Adds commercial payment columns to Lease & Mining, creates `application_payments`. |
| 45 | `2026_09_23_000001_add_payment_fields_to_remaining_applications_tables.php` | `environment_projects`, `ec_certificates`, `ppt_applications`, `dgps_surveys`, `drone_surveys` | Extends commercial payment columns to Environment, EC, PPT, DGPS, and Drone tables. |
| 46 | `2026_09_23_000002_create_ec_compliances_tables.php` | `ec_compliances`, `ec_compliance_documents` | Creates EC half-yearly compliance monitoring tables and 4-pillar document registry. |
| 47 | `2026_09_23_164025_add_b1_stages_to_environment_and_ppt_tables.php` | `environment_projects`, `ppt_applications` | Implements sequential B1 2-stage state machine (`b1_stage`, `ppt_stage_1_id`, `ppt_stage_2_id`). |
| 48 | `2026_09_24_000001_make_pan_nullable_in_customers_table.php` | `customers` | Relaxes `pan` column to nullable on `customers` table. |

---

## 3. Complete 64-Table Database Schema Dictionary

### Group 1: Core Framework, Authentication & Background Queues (8 Tables)

#### 1. `users`
* **Domain:** Core Authentication & User Accounts.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Unique system user ID. |
| `user_code` | `VARCHAR(255)` | Yes | `NULL` | None | Internal employee or consultant ID code. |
| `user_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Self-referencing parent/creator user ID mapped to `users.id`. |
| `name` | `VARCHAR(255)` | No | None | None | Full legal name of the user. |
| `email` | `VARCHAR(255)` | No | None | `UNIQUE` | User email address used for login. |
| `email_verified_at` | `TIMESTAMP` | Yes | `NULL` | None | Verification timestamp. |
| `password` | `VARCHAR(255)` | No | None | None | Bcrypt-hashed password. |
| `show_password` | `VARCHAR(255)` | Yes | `NULL` | None | Plaintext password preview *(SECURITY CONCERN: Deprecated)*. |
| `image` | `VARCHAR(255)` | Yes | `NULL` | None | Relative path to user avatar profile photo. |
| `mobile_num` | `VARCHAR(255)` | Yes | `NULL` | None | Primary mobile contact phone number. |
| `role_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Direct role mapping to `roles.id`. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Tenant branch mapping to `branches.id`. |
| `status` | `TINYINT` | No | `1` | None | Account status: 1 = Active, 0 = Inactive. |
| `remember_token` | `VARCHAR(100)` | Yes | `NULL` | None | "Remember me" session authentication token. |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record update timestamp. |

#### 2. `password_reset_tokens`
* **Domain:** Authentication Security.
* **Schema:** `email` (`VARCHAR(255)`, `PRIMARY`), `token` (`VARCHAR(255)`), `created_at` (`TIMESTAMP`, nullable).

#### 3. `sessions`
* **Domain:** HTTP Session Store.
* **Schema:** `id` (`VARCHAR(255)`, `PRIMARY`), `user_id` (`BIGINT UNSIGNED`, nullable, `INDEX`), `ip_address` (`VARCHAR(45)`, nullable), `user_agent` (`TEXT`, nullable), `payload` (`LONGTEXT`), `last_activity` (`INT`, `INDEX`).

#### 4. `cache`
* **Domain:** Key-Value Cache.
* **Schema:** `key` (`VARCHAR(255)`, `PRIMARY`), `value` (`MEDIUMTEXT`), `expiration` (`INT`).

#### 5. `cache_locks`
* **Domain:** Atomic Distributed Locks.
* **Schema:** `key` (`VARCHAR(255)`, `PRIMARY`), `owner` (`VARCHAR(255)`), `expiration` (`INT`).

#### 6. `jobs`
* **Domain:** Asynchronous Queue Engine.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `queue` (`VARCHAR(255)`, `INDEX`), `payload` (`LONGTEXT`), `attempts` (`TINYINT UNSIGNED`), `reserved_at` (`INT UNSIGNED`, nullable), `available_at` (`INT UNSIGNED`), `created_at` (`INT UNSIGNED`).

#### 7. `job_batches`
* **Domain:** Batch Job Orchestration.
* **Schema:** `id` (`VARCHAR(255)`, `PRIMARY`), `name` (`VARCHAR(255)`), `total_jobs` (`INT`), `pending_jobs` (`INT`), `failed_jobs` (`INT`), `failed_job_ids` (`LONGTEXT`), `options` (`MEDIUMTEXT`, nullable), `cancelled_at` (`INT`, nullable), `created_at` (`INT`), `finished_at` (`INT`, nullable).

#### 8. `failed_jobs`
* **Domain:** Queue Exception Logging.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `uuid` (`VARCHAR(255)`, `UNIQUE`), `connection` (`TEXT`), `queue` (`TEXT`), `payload` (`LONGTEXT`), `exception` (`LONGTEXT`), `failed_at` (`TIMESTAMP`, default `CURRENT_TIMESTAMP`).

---

### Group 2: Spatie Role-Based Access Control & Multi-Tenancy (6 Tables)

#### 9. `permissions`
* **Domain:** Granular RBAC Permissions.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`), `guard_name` (`VARCHAR(255)`), `created_at`, `updated_at`. `UNIQUE(name, guard_name)`.

#### 10. `roles`
* **Domain:** System Security Roles.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`), `guard_name` (`VARCHAR(255)`), `created_at`, `updated_at`. `UNIQUE(name, guard_name)`.

#### 11. `model_has_permissions`
* **Domain:** Direct Permission Overrides.
* **Schema:** `permission_id` (`BIGINT UNSIGNED`, `FK -> permissions.id`), `model_type` (`VARCHAR(255)`), `model_id` (`BIGINT UNSIGNED`). `PRIMARY(permission_id, model_id, model_type)`.

#### 12. `model_has_roles`
* **Domain:** User-Role Assignments.
* **Schema:** `role_id` (`BIGINT UNSIGNED`, `FK -> roles.id`), `model_type` (`VARCHAR(255)`), `model_id` (`BIGINT UNSIGNED`). `PRIMARY(role_id, model_id, model_type)`.

#### 13. `role_has_permissions`
* **Domain:** Role-Permission Matrix.
* **Schema:** `permission_id` (`BIGINT UNSIGNED`, `FK -> permissions.id`), `role_id` (`BIGINT UNSIGNED`, `FK -> roles.id`). `PRIMARY(permission_id, role_id)`.

#### 14. `branches`
* **Domain:** District Office Multi-Tenancy.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `branch_name` (`VARCHAR(255)`), `contact_person` (`VARCHAR(255)`, nullable), `mobile` (`VARCHAR(255)`, nullable), `address` (`TEXT`, nullable), `city` (`VARCHAR(255)`, nullable), `state` (`VARCHAR(255)`, nullable), `country` (`VARCHAR(255)`, nullable), `pincode` (`VARCHAR(255)`, nullable), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

---

### Group 3: Statutory Master Data Registries (9 Tables)

#### 15. `districts`
* **Domain:** Geographic Jurisdictions (38 Tamil Nadu Districts).
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`, `UNIQUE`), `code` (`VARCHAR(10)`, `UNIQUE`), `state` (`VARCHAR(255)`, default `'Tamil Nadu'`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

#### 16. `minerals`
* **Domain:** Statutory Mineral Types.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`, `UNIQUE`), `category` (`ENUM('Major', 'Minor')`, default `'Minor'`), `default_unit` (`ENUM('CBM', 'Tonnes')`, default `'CBM'`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

#### 17. `lease_categories`
* **Domain:** Statutory Concession Categories under TNMMCR.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `code` (`VARCHAR(50)`, `UNIQUE`), `name` (`VARCHAR(255)`), `land_type` (`ENUM('Patta', 'Poramboke', 'Both')`, default `'Both'`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

#### 18. `plan_types`
* **Domain:** Mining Plan Statutory Classifications.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`, `UNIQUE`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

#### 19. `applicant_types`
* **Domain:** Legal Entity Classifications.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`, `UNIQUE`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`. *(Values: Individual, Proprietorship, Partnership, Private Limited, Public Limited, Joint Venture, Trust)*.

#### 20. `modules`
* **Domain:** System Module Registry.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `code` (`VARCHAR(50)`, `UNIQUE`), `name` (`VARCHAR(255)`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`. *(Codes: lease, mining, environment, ppt, dgps, drone)*.

#### 21. `folders`
* **Domain:** Statutory Document Classification Folders.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `module_id` (`BIGINT UNSIGNED`, `FK -> modules.id`), `name` (`VARCHAR(255)`), `sort_order` (`INT`, default `0`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`. `UNIQUE(module_id, name)`.

#### 22. `document_fields`
* **Domain:** Statutory Document Checklists.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `folder_id` (`BIGINT UNSIGNED`, `FK -> folders.id`), `nature_of_work_id` (`BIGINT UNSIGNED`, nullable, `FK -> nature_of_works.id`), `name` (`VARCHAR(255)`), `required` (`TINYINT(1)`, default `1`), `sort_order` (`INT`, default `0`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`.

#### 23. `nature_of_works`
* **Domain:** Mining Plan Scope Classifications.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `name` (`VARCHAR(255)`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`. *(Values: Mining Plan, Scheme of Mining, Modified Mining Plan, Review of Mining Plan)*.

---

### Group 4: Customer 360 Core Operational Aggregate (1 Table)

#### 24. `customers`
* **Domain:** Central Applicant / Quarry Owner Registry.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Unique customer entity ID. |
| `slug` | `VARCHAR(255)` | Yes | `NULL` | `UNIQUE` | URL-safe slug for Customer 360 profile routing. |
| `mimas_no` | `VARCHAR(255)` | Yes | `NULL` | `UNIQUE` | State TN Mines Tenement Portal unique customer ID. |
| `mimas_number` | `VARCHAR(255)` | Yes | `NULL` | None | Secondary MIMAS reference *(Redundant column)*. |
| `mimas_status` | `VARCHAR(255)` | Yes | `NULL` | None | Status of customer on state MIMAS portal. |
| `customer_name` | `VARCHAR(255)` | No | None | None | Primary individual applicant or director name. |
| `secondary_contact_person` | `VARCHAR(255)` | Yes | `NULL` | None | Quarry site in-charge or secondary contact person. |
| `company_name` | `VARCHAR(255)` | Yes | `NULL` | None | Registered legal entity or quarry enterprise name. |
| `mobile_num` | `VARCHAR(255)` | No | None | `INDEX` | Primary phone number. |
| `secondary_mobile_num` | `VARCHAR(255)` | Yes | `NULL` | None | Secondary phone number. |
| `email` | `VARCHAR(255)` | Yes | `NULL` | None | Contact email address. |
| `district_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Jurisdiction mapping to `districts.id`. |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`). |
| `pan` | `VARCHAR(10)` | Yes | `NULL` | None | 10-character Indian Income Tax PAN. |
| `aadhaar_no` | `VARCHAR(20)` | Yes | `NULL` | `UNIQUE` | 12-digit Indian Unique Identification (Aadhaar). |
| `gstin` | `VARCHAR(15)` | Yes | `NULL` | None | 15-character Goods and Services Tax ID. |
| `area` | `DECIMAL(10,2)` | Yes | `NULL` | None | Total cumulative quarry land extent in hectares. |
| `address` | `TEXT` | Yes | `NULL` | None | Registered correspondence address. |
| `status` | `TINYINT` | No | `1` | None | Operational status: 1 = Active, 0 = Inactive. |
| `user_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked system user account (`users.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Staff member who registered the record. |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Record creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record modification timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

---

### Group 5: Lease Application Module (5 Tables)

#### 25. `lease_applications`
* **Domain:** 7-Step Statutory Quarry Concession Application.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `common_id` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Universal Common ID (`GTMS-YYYY-XXXX`). |
| `application_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Module application number (`LA-YYYY-XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Quarry district (`districts.id`). |
| `category_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Statutory land category (`lease_categories.id`). |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`). |
| `other_mineral_name` | `VARCHAR(255)` | Yes | `NULL` | None | Free-text mineral name when "Other" is selected. |
| `taluk` | `VARCHAR(255)` | No | None | None | Revenue administrative taluk. |
| `village` | `VARCHAR(255)` | No | None | None | Revenue village name. |
| `area_extent_ha` | `DECIMAL(10,2)` | No | None | None | Quarry land area in Hectares. |
| `area_extent_acres` | `DECIMAL(10,2)` | Yes | `NULL` | None | Quarry land area in Acres. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `start_date` | `DATE` | Yes | `NULL` | None | Concession start date. |
| `end_date` | `DATE` | Yes | `NULL` | None | Concession expiration date. |
| `lease_period_years` | `INT` | Yes | `NULL` | None | Duration of lease in years (e.g. 5, 10, 20). |
| `contact_person` | `VARCHAR(255)` | Yes | `NULL` | None | Primary authorized contact person. |
| `secondary_contact_person`| `VARCHAR(255)`| Yes | `NULL` | None | Secondary site supervisor. |
| `contact_mobile` | `VARCHAR(255)` | Yes | `NULL` | None | Primary contact mobile number. |
| `secondary_contact_mobile`| `VARCHAR(255)`| Yes | `NULL` | None | Secondary contact mobile number. |
| `current_step` | `INT` | No | `1` | None | Active wizard step in progress (1 through 8). |
| `status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'submitted'`, `'under_validation'`, `'validated'`, `'approved'`, `'rejected'`. |
| `go_number` | `VARCHAR(255)` | Yes | `NULL` | None | Official Government Order (G.O.) grant number. |
| `go_date` | `DATE` | Yes | `NULL` | None | Date of G.O. issuance. |
| `go_file` | `VARCHAR(255)` | Yes | `NULL` | None | Relative file path to G.O. PDF copy. |
| `rejection_note` | `TEXT` | Yes | `NULL` | None | Statutory remarks explaining rejection. |
| `assigned_inspector_id` | `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Assigned DGM field inspector (`users.id`). |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 26. `lease_survey_numbers`
* **Domain:** Cadastral Revenue Boundary Plots.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `lease_application_id` (`BIGINT UNSIGNED`, `FK -> lease_applications.id`), `survey_no` (`VARCHAR(255)`), `extent_ha` (`DECIMAL(10,4)`), `classification` (`VARCHAR(255)`), `pattadar_name` (`VARCHAR(255)`), `created_at`, `updated_at`.

#### 27. `mimas_credentials`
* **Domain:** State TN Mines Portal Credentials.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `lease_application_id` (`BIGINT UNSIGNED`, `FK -> lease_applications.id`), `user_id` (`VARCHAR(255)`), `password` (`TEXT`), `email` (`VARCHAR(255)`, nullable), `contact_number` (`VARCHAR(255)`, nullable), `mimas_ack_no` (`VARCHAR(255)`, nullable), `ack_date` (`DATE`, nullable), `portal_status` (`VARCHAR(255)`, nullable), `created_at`, `updated_at`. *(Note: `password` is AES-256 encrypted using Laravel `Crypt`)*.

#### 28. `lease_documents`
* **Domain:** 19 Statutory Lease Checklist Attachments.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `lease_application_id` (`BIGINT UNSIGNED`, `FK -> lease_applications.id`), `folder_id` (`BIGINT UNSIGNED`, `FK -> folders.id`), `document_field_id` (`BIGINT UNSIGNED`, nullable, `FK -> document_fields.id`), `document_name` (`VARCHAR(255)`), `file_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `file_type` (`VARCHAR(255)`), `file_size` (`BIGINT UNSIGNED`), `status` (`ENUM('uploaded', 'validated', 'rejected')`, default `'uploaded'`), `review_note` (`TEXT`, nullable), `reviewed_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `reviewed_at` (`TIMESTAMP`, nullable), `uploaded_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `uploaded_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`, `deleted_at`.

#### 29. `lease_application_minerals`
* **Domain:** Multi-Mineral Concession Pivot.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `lease_application_id` (`BIGINT UNSIGNED`, `FK -> lease_applications.id`), `mineral_id` (`BIGINT UNSIGNED`, `FK -> minerals.id`), `created_at`, `updated_at`. `UNIQUE(lease_application_id, mineral_id)`.

---

### Group 6: Mining Plan Module (5 Tables)

#### 30. `mining_applications`
* **Domain:** 6-Stage Mining Plan Workflow (Process Flow 6.1 – 6.6).
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `common_id` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Universal Common ID (`GTMS-YYYY-XXXX`). |
| `application_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Mining Plan number (`MP-YYYY-XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease application (`lease_applications.id`). |
| `nature_of_work_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Nature of work (`nature_of_works.id`). |
| `parent_plan_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Previous plan ID if a revision or scheme. |
| `applicant_type_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Entity structure (`applicant_types.id`). |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Quarry district (`districts.id`). |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Primary mineral extracted (`minerals.id`). |
| `other_mineral_name` | `VARCHAR(255)` | Yes | `NULL` | None | Custom mineral designation. |
| `plan_type_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Plan type (`plan_types.id`). |
| `taluk` | `VARCHAR(255)` | No | None | None | Revenue administrative taluk. |
| `village` | `VARCHAR(255)` | No | None | None | Revenue village. |
| `survey_numbers_text` | `TEXT` | Yes | `NULL` | None | Comma-separated revenue survey numbers. |
| `area_extent_ha` | `DECIMAL(10,2)` | No | None | None | Mine area in Hectares. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `start_date` | `DATE` | Yes | `NULL` | None | Plan operational commencement date. |
| `end_date` | `DATE` | Yes | `NULL` | None | Plan operational expiration date. |
| `validity_years` | `INT` | Yes | `5` | None | Statutory validity period in years (default: 5). |
| `stage` | `VARCHAR(255)` | No | `'6.1'` | None | Current workflow stage: `'6.1'` through `'6.6'`. |
| `status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'submitted'`, `'scrutiny'`, `'inspection'`, `'approved'`, `'rejected'`. |
| `rqp_name` | `VARCHAR(255)` | Yes | `NULL` | None | Name of Recognized Qualified Person. |
| `rqp_reg_no` | `VARCHAR(255)` | Yes | `NULL` | None | IBM / State RQP Registration Number. |
| `safety_distance_meters` | `DECIMAL(10,2)` | Yes | `NULL` | None | Statutory buffer distance from road/rail/habitations. |
| `assigned_inspector_id` | `BIGINT UNSIGNED`| Yes | `NULL` | `FOREIGN KEY` | Assigned inspecting officer (`users.id`). |
| `approval_order_no` | `VARCHAR(255)` | Yes | `NULL` | None | Official Mining Plan approval order reference. |
| `approval_date` | `DATE` | Yes | `NULL` | None | Date of Mining Plan approval order. |
| `approval_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to approved Mining Plan signed document. |
| `kml_file_path` | `VARCHAR(255)` | Yes | `NULL` | None | Path to uploaded GIS KML boundary file. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 31. `mining_boundary_points`
* **Domain:** Boundary Pillar Geo-Coordinates.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mining_application_id` (`BIGINT UNSIGNED`, `FK -> mining_applications.id`), `pillar_id` (`VARCHAR(50)`), `latitude` (`DECIMAL(11,8)`), `longitude` (`DECIMAL(11,8)`), `elevation` (`DECIMAL(8,2)`, nullable), `remarks` (`TEXT`, nullable), `created_at`, `updated_at`.

#### 32. `mining_production_schedules`
* **Domain:** 5-Year Progressive Excavation Forecasts.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mining_application_id` (`BIGINT UNSIGNED`, `FK -> mining_applications.id`), `year_number` (`INT`), `production_target` (`DECIMAL(12,2)`), `waste_removal` (`DECIMAL(12,2)`), `created_at`, `updated_at`. `UNIQUE(mining_application_id, year_number)`.

#### 33. `mining_documents`
* **Domain:** Statutory Mining Plan Attachments.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mining_application_id` (`BIGINT UNSIGNED`, `FK -> mining_applications.id`), `folder_id` (`BIGINT UNSIGNED`, `FK -> folders.id`), `document_field_id` (`BIGINT UNSIGNED`, nullable, `FK -> document_fields.id`), `document_name` (`VARCHAR(255)`), `file_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `file_type` (`VARCHAR(255)`), `file_size` (`BIGINT UNSIGNED`), `status` (`ENUM('uploaded', 'validated', 'rejected')`, default `'uploaded'`), `review_note` (`TEXT`, nullable), `reviewed_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `reviewed_at` (`TIMESTAMP`, nullable), `uploaded_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `uploaded_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`, `deleted_at`.

#### 34. `mining_application_minerals`
* **Domain:** Mining Plan Multi-Mineral Pivot.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mining_application_id` (`BIGINT UNSIGNED`, `FK -> mining_applications.id`), `mineral_id` (`BIGINT UNSIGNED`, `FK -> minerals.id`), `created_at`, `updated_at`. `UNIQUE(mining_application_id, mineral_id)`.

---

### Group 7: Environmental Clearance (EC) Module (3 Tables)

#### 35. `environment_projects`
* **Domain:** SEIAA / DEIAA Environmental Clearance Projects.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `project_code` | `VARCHAR(255)` | No | None | `UNIQUE` | Unique project code (`ENV/B1/YYYY/XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `mining_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked approved mining plan (`mining_applications.id`). |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Linked lease application (`lease_applications.id`). |
| `category` | `ENUM('B1', 'B2')`| No | None | None | EIA Statutory Classification. |
| `sub_category` | `ENUM('SC1', 'SC2')`| Yes | `NULL` | None | B1 Sub-category stage. |
| `b1_stage` | `VARCHAR(50)` | Yes | `'sc1_prep'`| None | Sequential B1 lifecycle state. |
| `ppt_stage_1_id` | `BIGINT UNSIGNED` | Yes | `NULL` | None | Link to Stage 1 ToR Presentation dossier. |
| `ppt_stage_2_id` | `BIGINT UNSIGNED` | Yes | `NULL` | None | Link to Stage 2 Final EC Presentation dossier. |
| `project_name` | `VARCHAR(255)` | No | None | None | Official title of environmental project. |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`). |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Site location description. |
| `contact_name` | `VARCHAR(255)` | Yes | `NULL` | None | Project focal point contact name. |
| `contact_phone` | `VARCHAR(255)` | Yes | `NULL` | None | Contact telephone number. |
| `contact_email` | `VARCHAR(255)` | Yes | `NULL` | None | Contact email address. |
| `public_hearing_date` | `DATE` | Yes | `NULL` | None | Date of mandatory public hearing (B1). |
| `public_hearing_minutes_file`| `VARCHAR(255)`| Yes | `NULL` | None | Path to signed public hearing minutes PDF. |
| `status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'documents_pending'`, `'under_review'`, `'approved'`, `'rejected'`. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 36. `environment_documents`
* **Domain:** Environmental Dossier Attachments across 6 Folders.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `environment_project_id` (`BIGINT UNSIGNED`, `FK -> environment_projects.id`), `folder_id` (`BIGINT UNSIGNED`, `FK -> folders.id`), `document_field_id` (`BIGINT UNSIGNED`, nullable, `FK -> document_fields.id`), `document_name` (`VARCHAR(255)`), `file_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `file_type` (`VARCHAR(255)`), `file_size` (`BIGINT UNSIGNED`), `status` (`ENUM('uploaded', 'validated', 'rejected')`, default `'uploaded'`), `review_note` (`TEXT`, nullable), `reviewed_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `reviewed_at` (`TIMESTAMP`, nullable), `uploaded_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `uploaded_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`, `deleted_at`.

#### 37. `ec_certificates`
* **Domain:** Official Granted Environmental Clearances.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `ec_ref_no` (`VARCHAR(255)`, `UNIQUE`), `environment_project_id` (`BIGINT UNSIGNED`, `FK -> environment_projects.id`), `customer_id` (`BIGINT UNSIGNED`, `FK -> customers.id`), `lease_application_id` (`BIGINT UNSIGNED`, nullable, `FK -> lease_applications.id`), `parivesh_app_no` (`VARCHAR(255)`, nullable), `applicant_name` (`VARCHAR(255)`), `issue_date` (`DATE`), `expiry_date` (`DATE`), `validity_years` (`INT`, default `5`), `communication_type` (`ENUM('Grant', 'Rejection', 'ToR')`, default `'Grant'`), `certificate_file` (`VARCHAR(255)`), `conditions_summary` (`TEXT`, nullable), `status` (`ENUM('active', 'expired', 'surrendered')`, default `'active'`), `product_value` (`DECIMAL(12,2)`, default `0.00`), `paid_amount` (`DECIMAL(12,2)`, default `0.00`), `pending_amount` (`DECIMAL(12,2)`, default `0.00`), `payment_status` (`ENUM('pending', 'partial', 'paid')`, default `'pending'`), `created_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `created_at`, `updated_at`, `deleted_at`.

---

### Group 8: PPT Presentation & Technical Appraisal Module (3 Tables)

#### 38. `ppt_applications`
* **Domain:** Committee Technical Appraisal Presentations.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `application_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Presentation application number (`PPT-YYYY-XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `environment_project_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Associated EC project (`environment_projects.id`). |
| `presentation_stage` | `VARCHAR(50)` | No | `'tor_presentation'`| None | Presentation gate: `'tor_presentation'` or `'ec_presentation'`. |
| `project_name` | `VARCHAR(255)` | No | None | None | Presentation project title. |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`). |
| `taluk_village` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative location. |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Subject mineral (`minerals.id`). |
| `status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'agenda_scheduled'`, `'presented'`, `'approved'`, `'rejected'`. |
| `rqp_attending` | `VARCHAR(255)` | Yes | `NULL` | None | Name of RQP defending the presentation. |
| `company_rep_attending` | `VARCHAR(255)` | Yes | `NULL` | None | Authorized quarry company representative. |
| `rep_mobile` | `VARCHAR(255)` | Yes | `NULL` | None | Representative contact mobile number. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 39. `ppt_agendas`
* **Domain:** Committee Meeting Schedules & Minutes of Meeting (MoM).
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `ppt_application_id` (`BIGINT UNSIGNED`, `FK -> ppt_applications.id`), `committee_type` (`ENUM('SEAC', 'SEIAA')`), `meeting_no` (`VARCHAR(255)`), `item_no` (`VARCHAR(255)`), `meeting_date` (`DATE`), `agenda_pdf` (`VARCHAR(255)`, nullable), `mom_pdf` (`VARCHAR(255)`, nullable), `outcome` (`ENUM('recommended', 'deferred', 'rejected')`, nullable), `created_at`, `updated_at`.

#### 40. `ppt_documents`
* **Domain:** Presentation Slides & Supporting Dossiers (11 Folders).
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `ppt_application_id` (`BIGINT UNSIGNED`, `FK -> ppt_applications.id`), `folder_id` (`BIGINT UNSIGNED`, `FK -> folders.id`), `document_field_id` (`BIGINT UNSIGNED`, nullable, `FK -> document_fields.id`), `document_name` (`VARCHAR(255)`), `file_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `file_type` (`VARCHAR(255)`), `file_size` (`BIGINT UNSIGNED`), `status` (`ENUM('uploaded', 'validated', 'rejected')`, default `'uploaded'`), `review_note` (`TEXT`, nullable), `reviewed_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `uploaded_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `created_at`, `updated_at`, `deleted_at`.

---

### Group 9: Survey Modules — DGPS & Drone (5 Tables)

#### 41. `dgps_surveys`
* **Domain:** Differential GPS Cadastral Boundary Fixation.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `survey_no` | `VARCHAR(255)` | No | None | `UNIQUE` | DGPS survey number (`DGPS-YYYY-XXXX`). |
| `field_book_no` | `VARCHAR(255)` | Yes | `NULL` | None | Surveyor field register book reference. |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease application (`lease_applications.id`). |
| `mining_application_id`| `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Associated mining plan (`mining_applications.id`). |
| `lease_area_ha` | `DECIMAL(10,2)` | No | None | None | Concession area as per revenue records. |
| `surveyed_area_ha` | `DECIMAL(10,2)` | No | None | None | Computed area derived from field coordinates. |
| `area_discrepancy_ha` | `DECIMAL(10,2)` | Yes | `NULL` | None | Variance between statutory and surveyed area. |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Geographical site location. |
| `survey_date` | `DATE` | No | None | None | Date of field survey execution. |
| `surveyor_user_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Field surveyor staff account (`users.id`). |
| `survey_team_notes` | `TEXT` | Yes | `NULL` | None | Technical notes on terrain, obstacles, and benchmarks. |
| `instrument_model` | `VARCHAR(255)` | Yes | `NULL` | None | GNSS receiver model (e.g. Trimble R12, Leica GS18). |
| `instrument_serial_no` | `VARCHAR(255)` | Yes | `NULL` | None | Manufacturer serial number of the rover/base. |
| `survey_status` | `ENUM` | No | `'field_done'`| None | Status: `'scheduled'`, `'field_done'`, `'computed'`, `'verified'`. |
| `report_status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'submitted'`, `'approved'`. |
| `gtm_report_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to generated GTM boundary survey PDF. |
| `autocad_dwg_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to raw AutoCAD `.dwg` boundary drawing. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 42. `dgps_points`
* **Domain:** DGPS Ground Control Points & Boundary Pillars.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `dgps_survey_id` (`BIGINT UNSIGNED`, `FK -> dgps_surveys.id`), `pillar_no` (`VARCHAR(50)`), `latitude` (`DECIMAL(11,8)`), `longitude` (`DECIMAL(11,8)`), `elevation` (`DECIMAL(8,2)`), `created_at`, `updated_at`.

#### 43. `dgps_documents`
* **Domain:** DGPS Survey Attachments & Drawings.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `dgps_survey_id` (`BIGINT UNSIGNED`, `FK -> dgps_surveys.id`), `folder_id` (`BIGINT UNSIGNED`, nullable, `FK -> folders.id`), `document_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `status` (`VARCHAR(255)`, default `'uploaded'`), `created_at`, `updated_at`.

#### 44. `drone_surveys`
* **Domain:** UAV Drone Volumetric Audits & Aerial Cartography.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `survey_no` | `VARCHAR(255)` | No | None | `UNIQUE` | Drone survey number (`DRONE-YYYY-XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `lease_application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor lease application (`lease_applications.id`). |
| `mining_application_id`| `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Associated mining plan (`mining_applications.id`). |
| `lease_area` | `DECIMAL(10,2)` | Yes | `NULL` | None | Total quarry land area surveyed. |
| `location` | `VARCHAR(255)` | Yes | `NULL` | None | Geographical site location. |
| `flight_date` | `DATE` | No | None | None | Date of UAV flight execution. |
| `drone_pilot_name` | `VARCHAR(255)` | Yes | `NULL` | None | Certified UAV remote pilot name. |
| `pilot_rpc_no` | `VARCHAR(255)` | Yes | `NULL` | None | DGCA Remote Pilot Certificate (RPC) number. |
| `drone_uin_no` | `VARCHAR(255)` | Yes | `NULL` | None | Unique Identification Number (UIN) on DigitalSky. |
| `drone_model` | `VARCHAR(255)` | Yes | `NULL` | None | Drone hardware model (e.g. DJI Matrice 300 RTK). |
| `altitude_meters` | `DECIMAL(8,2)` | Yes | `NULL` | None | Flight altitude above ground level (AGL). |
| `gsd_cm_px` | `DECIMAL(8,2)` | Yes | `NULL` | None | Ground Sampling Distance (resolution in cm/pixel). |
| `extracted_volume_cbm` | `DECIMAL(12,2)` | Yes | `NULL` | None | Photogrammetrically calculated pit excavation volume. |
| `survey_status` | `ENUM` | No | `'flown'` | None | Status: `'scheduled'`, `'flown'`, `'processing'`, `'completed'`. |
| `deliverable_files_path`| `VARCHAR(255)`| Yes | `NULL` | None | Directory path to GeoTIFF orthomosaics and DEM. |
| `gtms_report_file` | `VARCHAR(255)` | Yes | `NULL` | None | Path to generated volumetric audit PDF report. |
| `product_value` | `DECIMAL(12,2)` | Yes | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | Yes | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 45. `drone_documents`
* **Domain:** Drone Flight Logs & Orthomosaics.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `drone_survey_id` (`BIGINT UNSIGNED`, `FK -> drone_surveys.id`), `folder_id` (`BIGINT UNSIGNED`, nullable, `FK -> folders.id`), `document_name` (`VARCHAR(255)`), `file_path` (`VARCHAR(255)`), `file_size` (`BIGINT UNSIGNED`, nullable), `status` (`VARCHAR(255)`, default `'uploaded'`), `created_at`, `updated_at`.

---

### Group 10: Mineral Stockpile & Quarry Inventory (3 Tables)

#### 46. `mineral_stockpiles`
* **Domain:** Pithead Mineral Stockpiles & Permitted Production Quotas.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `quarry_customer_id` (`BIGINT UNSIGNED`, `FK -> customers.id`), `lease_application_id` (`BIGINT UNSIGNED`, `FK -> lease_applications.id`), `mineral_id` (`BIGINT UNSIGNED`, `FK -> minerals.id`), `branch_id` (`BIGINT UNSIGNED`, nullable, `FK -> branches.id`), `annual_permitted_quota` (`DECIMAL(12,2)`), `current_stock_cbm` (`DECIMAL(12,2)`, default `0.00`), `total_dispatched_cbm` (`DECIMAL(12,2)`, default `0.00`), `unit` (`VARCHAR(50)`, default `'CBM'`), `status` (`TINYINT`, default `1`), `created_at`, `updated_at`. `UNIQUE(lease_application_id, mineral_id)`.

#### 47. `mineral_stock_entries`
* **Domain:** Mined Mineral Additions & Drone Volumetric Reconciliations.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mineral_stockpile_id` (`BIGINT UNSIGNED`, `FK -> mineral_stockpiles.id`), `entry_date` (`DATE`), `quantity` (`DECIMAL(12,2)`), `source_type` (`ENUM('quarry_extraction', 'drone_volume_audit', 'manual_adjustment')`), `verified_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `remarks` (`TEXT`, nullable), `created_at`, `updated_at`.

#### 48. `mineral_dispatches`
* **Domain:** Transit Passes, Mineral Dispatches & Seigniorage Tracking.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `mineral_stockpile_id` (`BIGINT UNSIGNED`, `FK -> mineral_stockpiles.id`), `dispatch_date` (`DATETIME`), `quantity` (`DECIMAL(12,2)`), `vehicle_number` (`VARCHAR(50)`), `driver_name` (`VARCHAR(255)`, nullable), `destination` (`VARCHAR(255)`), `seigniorage_fee_inr` (`DECIMAL(12,2)`), `challan_no` (`VARCHAR(100)`, nullable), `status` (`ENUM('pending', 'dispatched', 'delivered')`, default `'pending'`), `created_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `created_at`, `updated_at`.

---

### Group 11: Workflow Engine, Audit Logging & Notifications (4 Tables)

#### 49. `project_flows`
* **Domain:** Sequential Statutory Stage Transition Registry.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `flowable_type` (`VARCHAR(255)`), `flowable_id` (`BIGINT UNSIGNED`), `step_code` (`VARCHAR(50)`), `step_name` (`VARCHAR(255)`), `status` (`ENUM('pending', 'in_progress', 'completed', 'skipped')`, default `'pending'`), `note` (`TEXT`, nullable), `handled_by` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `handled_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`. `UNIQUE(flowable_type, flowable_id, step_code)`.

#### 50. `activity_logs`
* **Domain:** Comprehensive Immutable System Audit Trail.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `loggable_type` (`VARCHAR(255)`, nullable), `loggable_id` (`BIGINT UNSIGNED`, nullable), `user_id` (`BIGINT UNSIGNED`, nullable, `FK -> users.id`), `action` (`VARCHAR(100)`), `description` (`TEXT`, nullable), `ip_address` (`VARCHAR(45)`, nullable), `user_agent` (`TEXT`, nullable), `old_values` (`JSON`, nullable), `new_values` (`JSON`, nullable), `created_at` (`TIMESTAMP`, default `CURRENT_TIMESTAMP`).

#### 51. `archived_activity_logs`
* **Domain:** Cold Storage Historical Audit Logs.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `loggable_type` (`VARCHAR(255)`, nullable), `loggable_id` (`BIGINT UNSIGNED`, nullable), `user_id` (`BIGINT UNSIGNED`, nullable), `action` (`VARCHAR(100)`), `description` (`TEXT`, nullable), `ip_address` (`VARCHAR(45)`, nullable), `user_agent` (`TEXT`, nullable), `old_values` (`JSON`, nullable), `new_values` (`JSON`, nullable), `created_at` (`TIMESTAMP`, nullable), `archived_at` (`TIMESTAMP`, default `CURRENT_TIMESTAMP`).

#### 52. `notifications`
* **Domain:** In-App User Notifications.
* **Schema:** `id` (`CHAR(36)`, `PRIMARY`, UUID), `type` (`VARCHAR(255)`), `notifiable_type` (`VARCHAR(255)`), `notifiable_id` (`BIGINT UNSIGNED`), `data` (`TEXT`), `read_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`. `INDEX(notifiable_type, notifiable_id)`.

---

### Group 12: Universal Polymorphic Entities (2 Tables)

#### 53. `application_handlers`
* **Domain:** Universal Polymorphic Assigned Personnel Registry.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `application_type` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Application code (`lease`, `mining`, `environment`, `ec`, `dgps`, `drone`, `ppt`). |
| `application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `INDEX` | ID of the target application. |
| `handlerable_type` | `VARCHAR(255)` | Yes | `NULL` | `INDEX` | Polymorphic model class (`nullableMorphs`). |
| `handlerable_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `INDEX` | Polymorphic model ID (`nullableMorphs`). |
| `name` | `VARCHAR(255)` | No | None | None | Assigned person legal name. |
| `role` | `VARCHAR(255)` | No | None | None | Assigned operational role (e.g. Legal Counsel, RQP, Field Officer). |
| `notes` | `TEXT` | Yes | `NULL` | None | Operational instructions or qualifications. |
| `sort_order` | `INT` | No | `0` | None | Display sort sequence. |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |

#### 54. `application_payments`
* **Domain:** Universal Commercial Ledger & Financial Status.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `application_type` | `VARCHAR(50)` | Yes | `NULL` | `INDEX` | Application code (`lease`, `mining`, `environment`, `ec`, `dgps`, `drone`, `ppt`). |
| `application_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `INDEX` | Target application record ID. |
| `payable_type` | `VARCHAR(255)` | Yes | `NULL` | `INDEX` | Polymorphic model class (`nullableMorphs`). |
| `payable_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `INDEX` | Polymorphic model ID (`nullableMorphs`). |
| `product_value` | `DECIMAL(12,2)` | No | `0.00` | None | Total contracted commercial fee for the filing. |
| `paid_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Total amount paid and credited. |
| `pending_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Balance outstanding (`product_value - paid_amount`). |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `notes` | `TEXT` | Yes | `NULL` | None | Payment details, bank transaction IDs, challans. |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |

---

### Group 13: EC Half-Yearly Compliance Module (2 Tables)

#### 55. `ec_compliances`
* **Domain:** Statutory 6-Month Environmental Clearance Monitoring.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `compliance_no` | `VARCHAR(50)` | No | None | `UNIQUE` | Unique compliance number (`ECC-YYYY-XXXX`). |
| `customer_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Owning customer (`customers.id`). |
| `environment_project_id`| `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Precursor EC project (`environment_projects.id`). |
| `ec_certificate_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Governing EC certificate (`ec_certificates.id`). |
| `project_name` | `VARCHAR(255)` | No | None | None | Title of compliance dossier. |
| `district_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | District jurisdiction (`districts.id`). |
| `taluk_village` | `VARCHAR(255)` | Yes | `NULL` | None | Revenue administrative location. |
| `mineral_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Subject mineral (`minerals.id`). |
| `compliance_period` | `VARCHAR(100)` | No | None | `INDEX` | Half-yearly period (e.g. `'April 2026 - September 2026'`). |
| `compliance_year` | `VARCHAR(10)` | No | `'2026'` | None | Compliance calendar year. |
| `submission_due_date` | `DATE` | Yes | `NULL` | None | Statutory filing deadline (e.g. June 1 or December 1). |
| `submission_date` | `DATE` | Yes | `NULL` | None | Actual date submitted to MoEFCC / SEIAA. |
| `parivesh_app_no` | `VARCHAR(100)` | Yes | `NULL` | None | PARIVESH online portal application number. |
| `parivesh_acknowledgement_no`| `VARCHAR(100)`| Yes | `NULL` | None | PARIVESH digital filing acknowledgment number. |
| `parivesh_uploaded_date`| `DATE` | Yes | `NULL` | None | Date successfully submitted on PARIVESH portal. |
| `nabl_lab_name` | `VARCHAR(255)` | Yes | `NULL` | None | NABL accredited testing lab name. |
| `nabl_certificate_no` | `VARCHAR(100)` | Yes | `NULL` | None | Laboratory NABL accreditation certificate number. |
| `monitoring_date` | `DATE` | Yes | `NULL` | None | Date of ambient air, water, noise field sampling. |
| `status` | `ENUM` | No | `'draft'` | None | Status: `'draft'`, `'documents_collected'`, `'lab_analysed'`, `'report_prepared'`, `'uploaded_to_parivesh'`, `'completed'`, `'archived'`. |
| `product_value` | `DECIMAL(12,2)` | No | `0.00` | None | Commercial contracted project fee. |
| `paid_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Amount collected to date. |
| `pending_amount` | `DECIMAL(12,2)` | No | `0.00` | None | Outstanding balance receivable. |
| `payment_status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'partial'`, `'paid'`. |
| `payment_notes` | `TEXT` | Yes | `NULL` | None | Commercial accounting notes. |
| `branch_id` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Office branch (`branches.id`). |
| `created_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Submitting user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

#### 56. `ec_compliance_documents`
* **Domain:** 4-Pillar Statutory Compliance Attachments.
* **Schema Definition:**

| Column Name | SQL Type | Nullable | Default | Key / Index | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto-Inc | `PRIMARY` | Primary record ID. |
| `ec_compliance_id` | `BIGINT UNSIGNED` | No | None | `FOREIGN KEY` | Parent compliance filing (`ec_compliances.id`). |
| `folder_category` | `ENUM` | No | `'documents'`| None | Pillar: `'documents'` (19 files), `'site_analysis'` (4 NABL tests), `'report'` (3 reports), `'parivesh_upload'` (receipt). |
| `document_name` | `VARCHAR(255)` | No | None | None | Official statutory title of document. |
| `file_name` | `VARCHAR(255)` | Yes | `NULL` | None | Physical disk file name. |
| `file_path` | `VARCHAR(255)` | Yes | `NULL` | None | Relative file path in storage. |
| `file_type` | `VARCHAR(20)` | Yes | `NULL` | None | MIME type / file extension (e.g. `pdf`, `jpg`). |
| `file_size` | `BIGINT UNSIGNED` | Yes | `NULL` | None | File size in bytes. |
| `is_mandatory` | `BOOLEAN` | No | `1` | None | Indicates if statutory submission is mandatory. |
| `is_custom` | `BOOLEAN` | No | `0` | None | Indicates ad-hoc supplementary attachment. |
| `status` | `ENUM` | No | `'pending'` | None | Status: `'pending'`, `'uploaded'`, `'verified'`, `'rejected'`. |
| `review_note` | `TEXT` | Yes | `NULL` | None | Compliance officer review note. |
| `uploaded_by` | `BIGINT UNSIGNED` | Yes | `NULL` | `FOREIGN KEY` | Uploading user ID (`users.id`). |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp. |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Update timestamp. |
| `deleted_at` | `TIMESTAMP` | Yes | `NULL` | None | Soft delete timestamp. |

---

### Group 14: Legacy Scaffolding & Prototype Tables (7 Tables)

#### 57. `categories`
* **Domain:** Legacy Retail Product Categories.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `cat_code` (`VARCHAR(255)`, `UNIQUE`), `cat_name` (`VARCHAR(255)`), `delete_status` (`TINYINT`/`INT`, default `0`), `created_at`, `updated_at`.

#### 58. `products`
* **Domain:** Legacy Retail Products Catalog.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `bar_code` (`VARCHAR(255)`, nullable, `UNIQUE`), `pro_name` (`VARCHAR(255)`), `gst` (`DECIMAL(5,2)`, default `0`), `cast_per` (`DECIMAL(10,2)`), `mrp` (`DECIMAL(10,2)`), `unit` (`VARCHAR(255)`), `qty` (`INT`, default `0`), `discount_1` (`DECIMAL(5,2)`, default `0`), `discount_2` (`DECIMAL(5,2)`, default `0`), `discount_3` (`DECIMAL(5,2)`, default `0`), `cat_id` (`BIGINT UNSIGNED`, `FK -> categories.id`), `branch_id` (`BIGINT UNSIGNED`, nullable, `FK -> branches.id`), `delete_status` (`TINYINT`/`INT`, default `0`), `created_at`, `updated_at`.

#### 59. `product_stocks`
* **Domain:** Legacy Retail Stock Quantities.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `product_id` (`BIGINT UNSIGNED`, `FK -> products.id`), `total_stock` (`INT`), `available_stock` (`INT`), `sale_stock` (`INT`), `created_at`, `updated_at`.

#### 60. `units`
* **Domain:** Legacy Units of Measurement.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `units` (`VARCHAR(255)`), `delete_status` (`INT`, default `1`), `created_at`, `updated_at`.

#### 61. `environmental_projects`
* **Domain:** Initial Prototype B2 Environmental Projects *(Superseded by `environment_projects`)*.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `project_code` (`VARCHAR(255)`), `client_name` (`VARCHAR(255)`), `project_name` (`VARCHAR(255)`), `location` (`VARCHAR(255)`), `district` (`VARCHAR(255)`), `contact_name` (`VARCHAR(255)`), `contact_phone` (`VARCHAR(255)`), `contact_email` (`VARCHAR(255)`), `sub_category` (`VARCHAR(255)`), `status` (`VARCHAR(255)`), `validated_at` (`TIMESTAMP`, nullable), `approved_at` (`TIMESTAMP`, nullable), `archived_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`.

#### 62. `environmental_documents`
* **Domain:** Initial Prototype B2 Documents *(Superseded by `environment_documents`)*.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `project_id` (`BIGINT UNSIGNED`, `FK -> environmental_projects.id`), `folder` (`VARCHAR(255)`), `document_name` (`VARCHAR(255)`), `description` (`TEXT`, nullable), `file_path` (`VARCHAR(255)`), `original_name` (`VARCHAR(255)`, nullable), `status` (`VARCHAR(255)`), `review_note` (`TEXT`, nullable), `uploaded_at` (`TIMESTAMP`, nullable), `validated_at` (`TIMESTAMP`, nullable), `approved_at` (`TIMESTAMP`, nullable), `created_at`, `updated_at`.

#### 63. `environmental_activities`
* **Domain:** Initial Prototype B2 Activity Tracking.
* **Schema:** `id` (`BIGINT UNSIGNED`, `PRIMARY`, Auto-Inc), `project_id` (`BIGINT UNSIGNED`, `FK -> environmental_projects.id`), `action` (`VARCHAR(255)`), `details` (`TEXT`, nullable), `created_at`, `updated_at`.

---

### Group 15: Migration Engine (1 Table)

#### 64. `migrations`
* **Domain:** Framework Schema Version Registry.
* **Schema:** `id` (`INT UNSIGNED`, `PRIMARY`, Auto-Inc), `migration` (`VARCHAR(255)`), `batch` (`INT`).

---

## 4. Indexing Strategy, Performance & Relational Integrity

### 4.1 Indexing Strategy
To ensure sub-second response times across 50,000+ quarry documents and multi-year statutory ledgers, the schema enforces composite and specialized indexes:
* **Common ID Indexing:** `common_id` on both `lease_applications` and `mining_applications` enables instant O(log N) cross-departmental correlation.
* **Compound Tenancy Indexes:** High-volume queries filter by `customer_id` and `status`, or `district_id` and `status`. Dedicated composite indexes (`idx_ecc_category`, `[customer_id, status]`, `[district_id, status]`) prevent table scans.
* **Aadhaar & MIMAS Uniqueness:** `customers.aadhaar_no`, `customers.slug`, and `customers.mimas_no` carry unique B-tree indexes, ensuring zero entity duplication.

### 4.2 Relational Integrity & Cascading Rules
* **Master Deletion Protection (`restrictOnDelete`):** Core masters (`districts`, `minerals`, `customers`) are protected by `RESTRICT` foreign key constraints. A district or mineral cannot be deleted if active applications reference it.
* **Sub-Entity Cascades (`cascadeOnDelete`):** Dependent child rows (`lease_survey_numbers`, `mining_boundary_points`, `mining_production_schedules`, `dgps_points`, `ec_compliance_documents`) automatically cascade when a parent entity is deleted.
* **Auditing Safety (`nullOnDelete`):** Reviewer and uploader foreign keys (`reviewed_by`, `uploaded_by`, `assigned_inspector_id`) utilize `NULL ON DELETE` to ensure document history remains intact even if an employee user account is purged.
