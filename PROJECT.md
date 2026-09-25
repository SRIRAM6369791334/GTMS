# Project: GTMS Technical Knowledge-Transfer Documentation Suite

## Architecture
- **Framework:** Laravel 12.62.0 on PHP 8.2.12 ZTS (MySQL / MariaDB: `gtms_data`)
- **System Style:** Monolithic MVC with Controller-Centric Business Logic and Multi-Tenancy via `BranchScope`.
- **Documentation System:** 24-file production-grade technical documentation suite in `docs/00-project-overview.md` through `docs/23-developer-onboarding.md`, legacy archive notice in `docs/database-analysis/00_ARCHIVE_AND_OUTDATED_WARNING.md`, and root `README.md`.

## Feature Inventory
| # | Feature / Deliverable | Description | Milestone | Status | Source |
|---|---|---|---|---|---|
| 1 | `docs/00-project-overview.md` | Executive summary, business domain, regulatory context, and module matrix | Pre-existing | DONE | Survey |
| 2 | `docs/01-architecture.md` | C4 Context/Container diagrams, MVC lifecycle, data handoffs, file cloning | M1 | DONE | Worker M1 |
| 3 | `docs/02-environment-setup.md` | Local setup (XAMPP/PHP 8.2), categorized `.env` reference with zero secrets | M1 | DONE | Worker M1 |
| 4 | `docs/03-database.md` | Complete 64-table dictionary, column schemas, types, nullability, keys, migrations | M1 | DONE | Worker M1 / Remediate |
| 5 | `docs/04-models.md` | All 47 models, casts, fillable attributes, boot hooks, scopes, relationships | M1 | DONE | Worker M1 |
| 6 | `docs/05-controllers.md` | All 20 controllers, method-by-method audit, inputs, queries, view parameters | M2 | DONE | Spec Miner 2 |
| 7 | `docs/06-routes.md` | Complete catalog of 121 routes, HTTP verbs, middleware, permissions | M2 | DONE | Spec Miner 2 |
| 8 | `docs/07-form-requests-validation.md` | Input validation catalog, regex masks (Aadhaar, PAN, GSTIN), sanitization | M2 | DONE | Spec Miner 2 |
| 9 | `docs/08-services-business-logic.md` | State machines (6.1-6.5, B1 2-stage PPT gates), Indian currency words & GST math | M2 | DONE | Spec Miner 2 |
| 10 | `docs/09-authentication-authorization.md` | Spatie RBAC (38 permissions, 3 roles), `BranchScope` multi-tenancy, `Gate::before` | M3 | DONE | Worker M3 |
| 11 | `docs/10-frontend.md` | Blade hierarchy, DataTables (`datatables.init.js`), SweetAlert2, Toastr, AJAX | M3 | DONE | Worker M3 |
| 12 | `docs/11-api.md` | AJAX/JSON endpoints, search autocomplete, MIMAS lookups, document status updates | M3 | DONE | Worker M3 |
| 13 | `docs/12-jobs-queues-events.md` | Database queue config (`jobs`, `failed_jobs`), Artisan commands, scheduler | M3 | DONE | Worker M3 |
| 14 | `docs/13-middleware-security.md` | Middleware pipeline, CSRF, `Crypt` credential encryption, `show_password` issue | M3 | DONE | Worker M3 |
| 15 | `docs/14-file-storage.md` | Upload layout (`public/uploads/...`), MIME validation, cross-module cloning | M3 | DONE | Worker M3 |
| 16 | `docs/15-integrations.md` | MIMAS state portal credentials handling, 38 districts master synchronization | M3 | DONE | Worker M3 |
| 17 | `docs/16-testing.md` | PHPUnit test audit (50 tests, 388 assertions, 48 pass / 2 label sensitivity), rollbacks | M4 | DONE | Worker M4 / Remediate |
| 18 | `docs/17-deployment.md` | Production deployment guide, Apache/Nginx web server setup, permissions, cache | M4 | DONE | Worker M4 |
| 19 | `docs/18-feature-map.md` | End-to-end matrix: Feature → Route → Controller → Model → Table → View | M4 | DONE | Worker M4 |
| 20 | `docs/19-data-flows.md` | Sequence diagrams for Lease → Mining → EC → PPT → Surveys → Compliance | M4 | DONE | Worker M4 / Remediate |
| 21 | `docs/20-error-handling.md` | Exception handling, transaction boundaries (`DB::transaction`), audit logging | M4 | DONE | Worker M4 |
| 22 | `docs/21-glossary.md` | Domain glossary (MIMAS, ToR, EIA, SEIAA, DEAC, RQP, FMB, Patta, Seigniorage) | M5 | DONE | Worker M5 |
| 23 | `docs/22-unknowns-risks.md` | Technical risk register, dead code (`EnvironmentalProject`), double columns | M5 | DONE | Worker M5 |
| 24 | `docs/23-developer-onboarding.md` | 7-Day Day-by-Day immersion guide for a developer taking over cold | M5 | DONE | Worker M5 |
| 25 | `docs/database-analysis/00_ARCHIVE_AND_OUTDATED_WARNING.md` | Legacy archive notice & discrepancy matrix (22 outdated files vs code reality) | Pre-existing | DONE | Blueprint |
| 26 | Root `README.md` | Rewritten root README introducing GTMS and indexing the 24-file docs suite | M5 | DONE | Worker M5 / Remediate |
| 27 | Full-Suite Verification & Audit | Multi-agent review, empirical challenger checks, forensic audit gating | M6 | DONE | Orchestrator Gate |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|---|---|---|---|
| M1 | Architecture & System Foundation | `docs/01` to `docs/04` | Survey Complete | **DONE** |
| M2 | HTTP Layer, Validation & Business Logic | `docs/05` to `docs/08` | Survey Complete | **DONE** |
| M3 | Security, Access Control, Frontend & Infrastructure | `docs/09` to `docs/15` | M1, M2 | **DONE** |
| M4 | QA, Ops, Features & Data Flows | `docs/16` to `docs/20` | M1, M2, M3 | **DONE** |
| M5 | Domain Knowledge, Risk Register, Onboarding & Root README | `docs/21` to `docs/23`, `README.md` | M1..M4 | **DONE** |
| M6 | Final Full-Suite Verification & Forensic Audit Gate | Review, Challenge & Forensic Audit Gate | M1..M5 | **DONE** (PASS) |

## Interface Contracts
- **Documentation Standards:**
  - Written in clean Markdown with semantic headers, tables, code blocks, and Mermaid diagrams where applicable.
  - Zero raw secrets or credentials (`[REDACTED]` only).
  - All claims backed by concrete file paths and line number references from the application source code.
  - Symmetrical links and cross-references between related documentation files.
- **File System Boundaries:**
  - Application source code (`app/*`, `routes/*`, `resources/*`, `database/*`) is IMMUTABLE and READ-ONLY.
  - Deliverables must only write to `docs/*.md` and root `README.md`.
  - Agent coordination and state files strictly inside `.agents/teamwork/<agent_dir>/`.

## Code Layout
- `docs/` — Production technical documentation suite (24 files).
- `docs/database-analysis/00_ARCHIVE_AND_OUTDATED_WARNING.md` — Archive notice.
- `README.md` — Project root entry point.
- `.agents/teamwork/` — Agent coordination workspaces.
