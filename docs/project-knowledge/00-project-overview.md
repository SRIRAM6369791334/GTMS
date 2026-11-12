# 00 — Project Overview

> Part of the GTMS knowledge library. Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md).
> Audit date: 2026-11-12. Audited commit: `5f3169a` on branch `claude/relaxed-mendel-8828d9` (identical to `main` at audit start).
> Method: read-only reverse engineering of the repository. No database was connected to; no code was executed.

## 1. What GTMS is — VERIFIED (code + seed data)

GTMS is an internal, staff-operated web application for a Tamil Nadu mining consultancy. The default bank account seeded by migration `2026_10_22_123500_create_bank_accounts_table.php` is named "Geo Technical Mining Solutions", and the default quotation signatory in `2026_10_22_154630_add_authorized_signatory_to_quotations_table.php` is the consultancy principal. The acronym GTMS is used throughout (`GTMS-YYYY-NNNN` master IDs, `GTMS/QTN/…` quotation numbers, `GTMS/REC/…` receipt numbers).

The consultancy prepares and tracks statutory paperwork for quarry/mine operators ("customers") through a chain of government processes:

1. **Quarry lease application** (Tamil Nadu Minor Mineral Concession Rules; MIMAS state portal credentials are stored).
2. **Land surveys** — DGPS boundary demarcation and drone topography / volumetric surveys.
3. **Mining plan** preparation (stages 6.1–6.5, RQP, plan types: fresh / revised / modified / scheme).
4. **Environmental clearance** (Category B1 with ToR → EIA/ETA sub-stages, or Category B2) submitted on the national PARIVESH portal.
5. **PPT department** — presentations before SEAC / SEIAA committees.
6. **EC certificate** issuance record and **half-yearly EC compliance** returns.
7. **Accounts** — quotations, payment collection, receipts, customer ledger, reports, proforma / tax invoices.
8. **Master pipeline** — a universal project record (`master_projects`) that links all of the above under one ID and logs forward / return / resubmit movements between departments.

There is **no customer-facing portal**. All users are internal staff authenticated with email or user code (`AuthController@login`). A `customers.user_id` column exists with the comment "Future customer portal user link" but nothing uses it.

## 2. Who uses it — PARTIALLY VERIFIED

Roles seeded by `database/seeders/RolePermissionSeeder.php`: **Super Admin**, **Admin**, **Staff**, **Officer**. Admin and Super Admin bypass every permission check through `Gate::before` in `app/Providers/AppServiceProvider.php`. Further custom roles can be created at runtime by any user holding `roles.create`. Departments are modelled as **branches** (`branches` table, UI label "Department / Branch"). See [14-user-roles-permissions.md](14-user-roles-permissions.md).

## 3. Deployment status — VERIFIED from repository, live state UNKNOWN

* Every push to `main` builds a Docker image and publishes it to GHCR (`.github/workflows/deploy.yml`).
* `docker-compose.yml` targets a **Synology NAS** (`/volume1/docker/gtms-data`) running app, queue worker, MySQL 8, Redis 7 and **Watchtower**, which polls GHCR every 300 s and replaces the running app and queue containers automatically.
* The container entrypoint runs `php artisan migrate --force` on every start (`docker/entrypoint.sh`).
* Result: **every merge to `main` reaches production within about five minutes, including database migrations, with no human gate.** Treat `main` as production. See [20-testing-deployment-configuration.md](20-testing-deployment-configuration.md).
* `GET /system/version` (public, no auth) reports build tag `v1.0.3-second-verification-success`.

Whether the NAS deployment is live and serving real client data could not be verified from the repository (UNKNOWN). Recent commit messages ("verify watchtower auto-deployment") indicate it is live.

## 4. Scale of the codebase — VERIFIED (counts from `git ls-files`)

| Area | Count |
|---|---|
| Tracked files | 1,788 |
| Controllers (incl. base `Controller`) | 29 (`app/Http/Controllers`, `app/Http/Controllers/Accounts`) |
| Controller lines | ≈19,400 (largest: `CustomerController` 2,589; `CustomerTrackingController` 2,109; `MiningController` 1,495) |
| Eloquent models | 58 + 1 scope + 1 trait |
| Services / Jobs | 2 / 3 |
| Migrations | 72 (≈3,900 lines) |
| Database tables created by migrations | 71 (incl. 9 framework tables and 5 Spatie permission tables) |
| Route declarations | 218 in `routes/web.php` + `/up` health route |
| Blade views | 100 (≈96,000 lines; largest `mining-portal/newapplication.blade.php` 7,229 lines) |
| First-party JS files | 43 in `public/js` (most are admin-template plugin initialisers) |
| PHPUnit tests | 38 Feature + 1 Unit files, ≈430 test methods |

## 5. Headline risks (detail in [18-security-audit.md](18-security-audit.md), [21-known-issues-risks.md](21-known-issues-risks.md))

1. **Critical** — chunked-upload API (`/api/upload/chunk/*`) is outside the `auth` middleware, and the `target_module` parameter is concatenated into a `public/` path. This allows unauthenticated file writes, potentially a web shell.
2. **Critical** — user passwords are stored in plaintext in `users.show_password` and returned in JSON.
3. **High** — third-party portal passwords (PARIVESH, e-mail, MIMAS draft session) are stored and returned in plaintext. Aadhaar and PAN numbers are returned by lookup APIs.
4. **High** — merges to `main` reach production automatically with no human gate. CI runs only one example unit test.
5. **High** — create paths write placeholder data (serial numbers, random PARIVESH numbers, default "completed/verified/paid" statuses) into real records.
6. **High** — the GTMS-YYYY-NNNN ID is generated in three different ways, and they can disagree.

## 6. Readiness for client change requests

Status: **PARTIALLY READY**. The structure, data model, routes and main workflows are documented with evidence. Several areas are only partially verified: about 96 k lines of Blade/JS were inventoried but not read line by line, no live DB was inspected, and no tests were run. Follow [23-feature-change-impact-guide.md](23-feature-change-impact-guide.md) before changing anything.
