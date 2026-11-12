# 19 — Performance & Code-Quality Audit

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Static review; no profiling was run. Severity: Critical / High / Medium / Low / Informational.

## Performance
| ID | Finding | Evidence | Severity |
|---|---|---|---|
| P1 | Unpaginated list queries with heavy eager loads (all leases with customer, district, category, mineral, minerals, documents, miningApplications; all mining applications with 8 relations) | `CustomerController@index`, `MiningController@index` (`->get()`) | High as data grows |
| P2 | Dashboard closure runs ≈20 separate count / sum queries on every load, with no caching | `routes/web.php:70-210` | Medium |
| P3 | Notification index loads **all** of the user's notifications into memory to compute KPIs (`->get()` then collection filters) | `NotificationController@index` | Low |
| P4 | Customer tracking dossier aggregates every module per customer in PHP (≈840-line `buildCustomerDossier`, ≈365-line `getCustomerApplicationsCatalog`) | `CustomerTrackingController` | Medium (PARTIALLY VERIFIED) |
| P5 | `Schema::hasColumn` calls at runtime inside pipeline transactions (one information_schema query per call) | `MasterPipelineService::associateDepartmentRecord`, `updateDepartmentRecordStatus` | Low |
| P6 | Number generation by `count()+1` / `ORDER BY CAST(SUBSTRING_INDEX(...))` cannot use indexes and races under concurrency | see [12](12-business-logic.md) R-ID | Medium |
| P7 | Huge Blade files (7,229 / 4,314 / 3,707 / 3,430 lines) with inline JS; ~25 template JS / CSS files on every page, plus unused demo plugin initialisers | views, `layouts/app.blade.php` | Medium (page weight) |
| P8 | `opcache.validate_timestamps=0` is right for immutable images; any in-container hot-patch would need an FPM restart | `docker/php.ini` | Informational |
| P9 | Good: chunk assembly streams at about 64 KB memory; checksum via `hash_file` | `ChunkedUploadService` | — |
| P10 | MySQL `innodb-buffer-pool-size=4G` and Redis `maxmemory 2gb` assume a NAS with ≥8 GB RAM | `docker-compose.yml` | Informational |

## Code quality
| ID | Finding | Evidence | Severity |
|---|---|---|---|
| Q1 | Fat controllers: business rules, file I/O, numbering and cross-module promotion inside controllers of 1,000–2,600 lines; no service / domain layer except two classes | `app/Http/Controllers` | High (maintainability) |
| Q2 | Large copy-paste duplication: upload / remove / handler / payment blocks repeated in 8+ controllers with small differences (e.g. the extension lists differ) | grep `allowedExtensions` | High |
| Q3 | No Form Requests, Policies, enums or constants for statuses; status literals scattered across PHP and Blade | — | High |
| Q4 | Hard-coded IDs (module 1, folders 7/8/9, document fields 1–19, mineral 1, category 1, `Auth::id() ?? 1`, branch `?? 1`) | CustomerController, EnverionsoneController, MiningController | High |
| Q5 | Placeholder / demo data written by production code paths (random PARIVESH numbers, fixed serials, 25,000 fee, demo notifications, default ₹1.5 L invoice line) | see [12](12-business-logic.md), [21](21-known-issues-risks.md) | High (data quality) |
| Q6 | Duplicate / legacy routes and controllers (EnvironmentalB2Controller vs EnverionsoneController; 6 alias routes per upload; 3 routes for one mining document page) | [09](09-routes-api-documentation.md) R5-R7 | Medium |
| Q7 | Dead code / schema: 12 unused tables and models, unused views (`enviro_b1/*`, `welcome`, empty `createproductstock`), empty `unit.js`, Vite bundle never loaded, unused permissions | [04](04-module-documentation.md) | Medium |
| Q8 | Misnamed classes: `CustomerController` = lease applications; `EnverionsoneController` (typo); method `Document`, `Process` (PascalCase) | — | Low |
| Q9 | Silent failure: `@unlink`, `@copy`, `try { … } catch { Log::warning }` around activity logs and job dispatch; `migrate --force || true` | many | Medium |
| Q10 | Inconsistent JSON contracts (`status: 1/0` vs `success: true/false`) | controllers | Low |
| Q11 | Two parallel progression mechanisms (module "move" buttons vs master pipeline) | [13](13-business-workflows.md) W9 | High (design) |
| Q12 | Corrupted log string literals ("PPT Documents t found") from an earlier bulk find-and-replace | `app/Jobs/VerifyFileChecksumJob.php:48-51` | Low |
| Q13 | Legacy inventory module (products / categories / units) with wrong arithmetic in `ProductController@store` | [04](04-module-documentation.md) M4 | Low |
| Q14 | Tests are numerous (~430 methods) but not run in CI, and they run against the real MySQL schema | [20](20-testing-deployment-configuration.md) | Medium |
