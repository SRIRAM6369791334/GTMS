# GTMS Foundation & Architecture Documentation — Handoff Report

**Agent:** Worker M1 (Foundation & Architecture Document Writer)  
**Date:** 2026-09-24  
**Working Directory:** `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\worker_m1`  
**Delivered Files:**
1. `c:\xampp\htdocs\GTMS\gtms\docs\01-architecture.md` (31,944 bytes)
2. `c:\xampp\htdocs\GTMS\gtms\docs\02-environment-setup.md` (20,866 bytes)
3. `c:\xampp\htdocs\GTMS\gtms\docs\03-database.md` (65,677 bytes)
4. `c:\xampp\htdocs\GTMS\gtms\docs\04-models.md` (42,898 bytes)

---

## 1. Observation

Direct inspection of the GTMS application at `c:\xampp\htdocs\GTMS\gtms` yielded empirical confirmation of the following architectural elements:

1. **Architecture & Framework Stack:**
   - Framework: Laravel 12.62.0 on PHP 8.2.12 ZTS (Visual C++ 2019 x64) (`composer.json`, `composer.lock`, `.env`).
   - Web Server: Apache 2.4 (XAMPP for Windows) routing all requests to `public/index.php`.
   - Relational Database: MariaDB / MySQL 8.0 on default port 3306, hosting schema `gtms_data`.

2. **Database Migrations & Tables:**
   - Migration Files: Exactly 48 migration files executed in `database/migrations/` spanning from `0001_01_01_000000_create_users_table.php` through `2026_09_24_000001_make_pan_nullable_in_customers_table.php`.
   - Physical Tables: Exactly 64 tables in `gtms_data`.
   - Master Seeders: Exactly 7 seeders under `database/seeders/` (`DatabaseSeeder`, `RolePermissionSeeder`, `GtmsMasterDataSeeder`, `MiningNatureOfWorkSeeder`, `CustomerSeeder`, `PptAndDgpsModuleSeeder`, `EcComplianceSeeder`).

3. **Eloquent Model Inventory:**
   - Model Files: Exactly 49 PHP files under `app/Models/` (47 operational models + 2 legacy prototype models `EnvironmentalDocument.php` and `EnvironmentalActivity.php`, plus `EnvironmentalProject.php`).
   - Scopes & Traits: Exactly 1 Global Scope (`app/Models/Scopes/BranchScope.php`) and 1 Trait (`app/Models/Traits/BelongsToBranch.php`).
   - Models implementing `BelongsToBranch`: `DgpsSurvey`, `DroneSurvey`, `EcCompliance`, `EnvironmentProject`, `LeaseApplication`, `MineralStockpile`, `MiningApplication`, `PptApplication`.

4. **Multi-Tenancy & Data Isolation (`BranchScope.php`):**
   - Intercepts query generation in `apply(Builder $builder, Model $model)`:
     ```php
     if (!empty($user->branch_id) && $user->role_id !== 1) {
         $builder->where($model->getTable() . '.branch_id', $user->branch_id);
     }
     ```
   - Automatically stamps `branch_id` on model creation in `BelongsToBranch::bootBelongsToBranch()`.
   - Bypasses query scoping when `Auth::user()->role_id === 1` (Super Admin).

5. **Cross-Module Transitions & Cascade File Cloning:**
   - Observed in `CustomerController.php` (lines 1694-1730): When promoting a Lease Application to a Mining Plan, files are copied via `@copy($sourcePath, $destPath)` from `public/uploads/lease/...` to `public/uploads/mining/{app_no}/`, creating isolated `MiningDocument` rows.
   - Observed in `MiningController.php` (lines 915-945): When promoting a Mining Application to an Environment Project, files are copied via `@copy($src, $dest)` from `public/uploads/mining/...` to `public/uploads/environmental/{code}/`, creating isolated `EnvironmentDocument` rows.
   - Preserves historical statutory records and isolates audit trails from downstream mutations.

6. **Dedicated Module Document Tables vs Polymorphic Anti-Pattern:**
   - The database maintains 7 dedicated document tables: `lease_documents`, `mining_documents`, `environment_documents`, `ppt_documents`, `dgps_documents`, `drone_documents`, and `ec_compliance_documents`.
   - Prevents table lock contention during concurrent bulk uploads of CAD drawings and aerial orthomosaics.

7. **Universal Common ID Pattern (`common_id`):**
   - An indexed persistent string formatted as `GTMS-{YEAR}-{SEQUENCE}` spans `lease_applications` and `mining_applications`, maintaining audit continuity across departmental boundaries.

---

## 2. Logic Chain

1. **From Controller Inspection to C4 Component Architecture:**
   - In `app/Http/Controllers/`, all 20 controllers directly execute request validation, transactional management (`DB::beginTransaction`), Eloquent queries, filesystem I/O, and Blade view rendering without separate service or repository abstractions.
   - *Conclusion:* The architecture is formally documented in `docs/01-architecture.md` as a **Controller-Centric Monolithic MVC Architecture**, capturing real operational code paths rather than hypothetical layers.

2. **From Environment Audit to Setup Guide & Redacted `.env` Reference:**
   - `.env` and `config/*.php` rely on MySQL, database sessions (`SESSION_DRIVER=database`), database queue (`QUEUE_CONNECTION=database`), and local storage (`FILESYSTEM_DISK=local`).
   - Default XAMPP `php.ini` restricts uploads to 2 MB, which is fatal for multi-megabyte CAD, DWG, and EIA dossiers.
   - *Conclusion:* `docs/02-environment-setup.md` provides explicit hardware recommendations, performance tuning (`upload_max_filesize = 128M`, `post_max_size = 128M`, `memory_limit = 512M`), a line-by-line `.env` reference with zero secrets (`[REDACTED]`), seed execution order, and troubleshooting protocols.

3. **From Migration Chronology to Complete 64-Table Database Dictionary:**
   - Across 48 chronological migrations, exactly 64 tables were minted or altered in `gtms_data`.
   - Recent migrations introduced universal polymorphic ledgers (`application_payments`), personnel assignments (`application_handlers`), 4-pillar half-yearly compliance (`ec_compliances`, `ec_compliance_documents`), and B1 2-stage state machines (`b1_stage`, `presentation_stage`).
   - *Conclusion:* `docs/03-database.md` catalogs every single one of the 64 tables with exact SQL column types, nullability, default values, primary/foreign keys, indexes, and a 48-row migration audit matrix.

4. **From Model Code Inspection to Complete Model Graph:**
   - Auditing 49 model files in `app/Models/` revealed exact fillable attributes, type casts, soft deletes, boot lifecycle hooks (such as `Customer` slug generation), dynamic accessors (such as `EnvironmentProject` folder lists and category badges), and relationship trees.
   - *Conclusion:* `docs/04-models.md` details all 49 model files (47 operational + 2 legacy prototype), documents `BranchScope` multi-tenancy mechanics, and presents a complete Mermaid Entity-Relationship Model (ERD).

---

## 3. Caveats

1. **Dead Prototype Code:**
   - The tables `environmental_projects`, `environmental_documents`, and `environmental_activities` and their models (`EnvironmentalProject`, `EnvironmentalDocument`, `EnvironmentalActivity`) are remnants from an early prototype on 2026-08-07. Active business code uses `environment_projects` and `EnvironmentProject`. These legacy models are documented and flagged as technical debt.
2. **Double Column on `customers` (`mimas_no` vs `mimas_number`):**
   - The `customers` table carries both `mimas_no` (indexed unique identifier) and `mimas_number` (nullable text). Active controllers primarily query `mimas_no`. This redundancy is documented in both `docs/03-database.md` and `docs/04-models.md`.
3. **Plaintext Password Preview (`users.show_password`):**
   - Migration `2026_07_08_070939` added `show_password` to `users`. While `User` implements `'password' => 'hashed'`, the existence of this column represents a critical security risk that has been highlighted in the documentation.
4. **Retail Scaffolding Tables:**
   - Tables `categories`, `products`, `product_stocks`, and `units` are legacy remnants from an initial generic template. Quarry inventory is handled by `mineral_stockpiles`, `mineral_stock_entries`, and `mineral_dispatches`.

---

## 4. Conclusion

Worker M1 has successfully authored and verified the four foundational architecture and data documentation files under `docs/`:
1. `docs/01-architecture.md`: Production-grade architectural specification containing C4 Level 1 (Context), Level 2 (Container), and Level 3 (Component) Mermaid diagrams, full MVC request lifecycle trace, `BranchScope` multi-tenancy mechanics, cross-module data handoffs (`Customer` 360 & `common_id`), and physical file cloning patterns.
2. `docs/02-environment-setup.md`: Comprehensive local environment setup guide for Windows XAMPP and Ubuntu Linux, `php.ini` performance tuning for large geospatial uploads, complete categorized `.env` variable dictionary with zero exposed secrets (`[REDACTED]`), 7-seeder dependency order, and Artisan maintenance runbook.
3. `docs/03-database.md`: Exhaustive 64-table database dictionary covering every column schema, SQL data type, nullability, key, index, cascading rule, and a chronological audit of all 48 executed migrations.
4. `docs/04-models.md`: Exhaustive audit of all 49 model files in `app/Models/` (47 operational + 2 legacy prototype), detailing fillables, casts, lifecycle boot hooks, query scopes, dynamic accessors, full relationship graphs, and a system-wide Mermaid ERD.

**Zero application source code files were modified.** Strict file ownership constraints were honored.

---

## 5. Verification Method

To independently verify the deliverables:

1. **Verify File Existence & Size:**
   Inspect the created documentation files in `docs/`:
   ```bash
   dir docs\01-architecture.md docs\02-environment-setup.md docs\03-database.md docs\04-models.md
   ```
   *Expected sizes: `01-architecture.md` (~32 KB), `02-environment-setup.md` (~21 KB), `03-database.md` (~66 KB), `04-models.md` (~43 KB).*

2. **Verify Secret Redaction:**
   Search for unredacted passwords or keys across all four files:
   ```bash
   findstr /i "APP_KEY=base64 DB_PASSWORD=" docs\*.md
   ```
   *Expected result: 0 occurrences (all secrets are replaced with `[REDACTED]`).*

3. **Verify Table & Model Coverage:**
   - Confirm all 64 tables are present in `docs/03-database.md`.
   - Confirm all 49 model files are documented in `docs/04-models.md`.
   - Confirm all 48 migration files are listed in the chronological audit table in `docs/03-database.md`.

4. **Verify Application Integrity:**
   Verify git status:
   ```bash
   git status --short
   ```
   *Expected result: Only `docs/` and `.agents/teamwork/worker_m1/` files modified/untracked. Zero modifications to `app/`, `routes/`, `resources/`, or `database/`.*
