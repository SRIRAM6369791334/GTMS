# 02 — Folder & File Inventory

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Counts come from `git ls-files` at commit `5f3169a` (1,788 tracked files). `vendor/` and `node_modules/` are not tracked.

## 1. Classified inventory

| Class | Path(s) | Files | Role | Analysis depth |
|---|---|---|---|---|
| Backend — controllers | `app/Http/Controllers/*.php`, `app/Http/Controllers/Accounts/*.php` | 29 | All HTTP logic (fat controllers) | Method inventory 100%; bodies read for all workflow/security paths (see §3) |
| Backend — models | `app/Models/*.php`, `Scopes/BranchScope.php`, `Traits/BelongsToBranch.php` | 60 | Eloquent models, branch multi-tenancy | Structure extracted 100%; key models read in full |
| Backend — services | `app/Services/ChunkedUploadService.php`, `MasterPipelineService.php` | 2 | Upload engine, master pipeline | Read in full |
| Backend — jobs | `app/Jobs/*.php` | 3 | Post-upload media processing | Read (signatures, side effects) |
| Backend — provider / bootstrap | `app/Providers/AppServiceProvider.php`, `bootstrap/app.php`, `bootstrap/providers.php` | 3 | Gate::before admin bypass, middleware aliases | Read in full |
| Routes | `routes/web.php` (642 lines), `routes/console.php` | 2 | All 218 routes, plus the dashboard closure | Read in full |
| Config | `config/*.php` | 11 | Framework defaults + `permission.php` | Skimmed (framework defaults) |
| Database — migrations | `database/migrations` | 72 | Schema source of truth | Extracted 100% |
| Database — seeders | `database/seeders` | 10 | Roles/permissions, master data, sample data | `DatabaseSeeder`, `RolePermissionSeeder` read; others listed |
| Database — factories | `database/factories/UserFactory.php` | 1 | Test factory | Listed |
| Frontend — Blade | `resources/views/layouts` (6), `resources/views/pages` (85), `resources/views/vendor/pagination` (9) | 100 | All UI | Inventoried 100% (size, forms, AJAX, modals, DataTables); not read line by line |
| Frontend — custom JS | `public/js/ajax/*.js` (7), `public/js/gtms-*.js` (2), `public/js/{admin,app,custom,settings,dlabnav-init}.js` | 14 | CRUD AJAX, uploader, open-with | Endpoints extracted; uploader read in part |
| Frontend — template JS | `public/js/plugins-init/*.js` (22), `public/js/dashboard/*.js` (4), `public/js/custom.min.js` | 27 | Admin-template plugin initialisers | Role noted only (third-party template) |
| Frontend — CSS/SCSS | `public/css` (4), `public/scss` (147) | 151 | Template styles | Role noted only |
| Assets | `public/icons` (339), `public/images` (208), `public/assets` (2), `favicon.ico`, `robots.txt` | 551 | Images / icons | Not analysed |
| Vendor (front, committed) | `public/vendor` (213) | 213 | Third-party JS/CSS libraries | Not analysed (third-party) |
| Vite sources | `resources/js/app.js`, `resources/js/bootstrap.js`, `resources/css/app.css`, `vite.config.js` | 4 | Laravel skeleton Vite entry — **not loaded by any layout** | Read |
| Tests — PHPUnit | `tests/Feature` (38), `tests/Unit` (1), `tests/TestCase.php` | 40 | ≈430 test methods | Names/config read; not executed |
| Tests — E2E | `tests/e2e/*.mjs|*.js` (8), `tests/e2e/screenshots/{baseline,after}` (221 PNG) | 229 | Playwright smoke + screenshot capture | Listed |
| Deployment | `Dockerfile`, `docker-compose.yml`, `.dockerignore`, `docker/*` (8), `.github/workflows/deploy.yml`, `.env.example`, `.env.docker.example` | 14 | Image build, NAS runtime, CI | Read in full (secrets not copied) |
| Docs (pre-existing) | `docs/00…23-*.md`, `docs/GTMS_100_PERCENT_CODEBASE_FUNCTION_CATALOG.{md,html}`, `docs/database-analysis/*` (23) | 49 | Earlier AI-generated docs; `database-analysis/00_ARCHIVE_AND_OUTDATED_WARNING.md` marks that folder outdated | Not trusted as evidence; this library supersedes them |
| Root notes | `README.md`, `PROJECT.md`, `project_state.md` (425 KB), `lessons_learned.md` (181 KB), `task.md`, `walkthrough.md`, `performance_metrics.md`, `UI_AUDIT_REPORT.md`, `UI_CHANGELOG.md`, `PLAYWRIGHT_TEST_REPORT.md`, `TEST_DATA_SAFETY_PROTOCOL.md` | 11 | Session logs / protocols from earlier AI work | Skimmed headings only |
| AI agent workspace | `.agents/teamwork/**` | 160 | Notes from earlier multi-agent sessions (auditor/challenger/explorer folders) | Role noted; excluded from the Docker image by `.dockerignore` |
| Scratch scripts | `scratch/*.php` | 6 | Ad-hoc bootstrap scripts that log in as the first Admin and query customers | Read one (`test_kaveri.php`). **Included in the Docker image** (not in `.dockerignore`), but outside `public/`, so not web-reachable |
| Generated / cache | `storage/**/.gitignore`, `bootstrap/cache/.gitignore` | 10 | Placeholders only | — |

## 2. Unusual locations holding custom code

* `routes/web.php:70-210` — the whole dashboard (KPI queries) is a route closure, not a controller.
* `database/migrations/2026_09_07_*`, `2026_09_10_102535_*`, `2026_09_16_095500_*`, `2026_09_17_000001_*`, `2026_10_22_123500_*`, `2026_10_26_*` — migrations that also **write data** (placeholder MIMAS/Aadhaar values, common IDs, environment folders and document fields, the default bank account, `roles.created_by = 1`, `customers.branch_id` = first branch).
* `app/Http/Controllers/NotificationController.php:220` — demo notification seeding runs inside a production controller.
* `app/Http/Controllers/EnverionsoneController.php` — note the misspelt class name; this is the main environment-clearance controller.

## 3. First-party files actually inspected

| Group | Files | Depth |
|---|---|---|
| Controllers | 29/29 | All method signatures. Bodies read in full: Auth, Unit, ProductStock, Category, Product, Branch, User, Roles, Notification, ChunkUpload, InvoiceSeal, MasterPipeline. Key methods read: Customer, Mining, Enverionsone, PptDepartment, EcCertificate, EcCompliance, DgpsSurvey, DroneSurvey, CustomerDirectory, CustomerTracking, Accounts/*. Read by targeted extraction only: EnvironmentalB2, CustomerLedger, FinancialReport, PaymentReceipt |
| Services | 2/2 | Full |
| Jobs | 3/3 | Partial (signatures, side effects) |
| Models | 60/60 | Relations, scopes, traits, table names; full read of User, Role, BranchScope, BelongsToBranch, MimasCredential |
| Migrations | 72/72 | Column-level extraction |
| Seeders | 2/10 read, 10/10 listed | — |
| Routes | 2/2 | Full |
| Views | 100/100 inventoried | Size, forms, AJAX, modals, `{!! !!}` usage, view references; selected lines read |
| Custom JS | 14/14 | Endpoints / key behaviour |
| Deployment | 14/14 | Full |

Files **not analysed**, and why: `public/vendor/**`, `public/scss/**`, `public/icons/**`, `public/images/**`, `public/js/plugins-init/**`, `public/js/dashboard/**`, `custom.min.js` (third-party template or binary assets); `tests/e2e/screenshots/**` (images); `.agents/**` and the root `*.md` session logs (AI work notes, not system behaviour); `composer.lock` and `package-lock.json` (lockfiles); the eight seeders not read in full (sample data).
