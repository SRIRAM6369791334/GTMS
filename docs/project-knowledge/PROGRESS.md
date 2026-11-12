# GTMS Knowledge Library — Audit Progress Tracker

Started: 2026-11-12. Branch: `claude/relaxed-mendel-8828d9`. Scope: read-only audit; only files under `docs/project-knowledge/` are created.

## Phase status
- [x] Phase 1 Discovery — stack = Laravel 12 / PHP 8.2 / spatie/laravel-permission 6 / MySQL 8 / Redis 7 / Docker + GHCR + Watchtower
- [x] routes/web.php read in full (642 lines)
- [x] Controllers: 29/29 inventoried; workflow/security paths read (see 02 §3 for depth per file)
- [x] Models 60/60 structure; migrations 72/72 column-level
- [x] Views 100/100 inventoried (not line-by-line); custom JS 14/14 endpoints
- [x] Docs written: 00-23 + PROJECT_MASTER_INDEX (2026-11-12)

## Final coverage (real counts)
- Tracked files: 1,788. First-party files inspected (app, routes, bootstrap, database, views, custom JS, deployment): 286 + 14 deployment/config files.
- Controllers 29 · models 58 (+scope, trait) · services 2 · jobs 3 · tables 71 · migrations 72 · routes 218 (+/up) · Blade views 100 · first-party JS 14 (+29 template JS) · modules 18 · documented workflows 10 (W0-W10).
- Not analysed: public/vendor (213), public/scss (147), public/icons (339), public/images (208), template plugin JS (27), e2e screenshots (221), .agents (160), root session-log markdown, lockfiles, 8 sample seeders; views not read line by line.
- No tests executed, no DB connected, no application file modified.

## Running findings (raw, to be moved into docs)
- `api/upload/chunk/*` routes are declared OUTSIDE the `auth` group (routes/web.php:633-640).
- `master-pipeline/*` and `accounts/invoices/seal-*` routes have only `auth`, no `permission:` middleware.
- `DELETE /notifications/{id}` declared before `DELETE /notifications/clear-all` (routes/web.php:66-67) — route shadowing.
- `POST /notifications/seed-demo` reachable by any authenticated user.
- Dashboard is a 140-line closure in routes/web.php:70-210.
- Many duplicate alias routes for upload/remove (hyphen vs underscore variants).
- CI (`.github/workflows/deploy.yml`) runs only `--testsuite=Unit` (1 example test); 38 Feature tests not run in CI.
- Container entrypoint runs `php artisan migrate --force || true` on every app start (docker/entrypoint.sh).
- Watchtower uses `containrrr/watchtower:latest`, mounts docker.sock, polls GHCR every 300 s; images tagged `main` + SHA.
- users.show_password stores the plaintext password (UserController store/update) and User::$hidden does not hide it; JSON responses return `data => $user`.
- parivesh_password / email_password columns (customers, environment_projects, ppt_applications, ec_certificates) stored without `encrypted` cast; CustomerDirectoryController@lookupByMimas returns them in JSON.
- Chunk upload: `target_module` concatenated into public_path('uploads/'.module) with no whitelist → path traversal; extension preserved (Str::slug(basename).ext) → .php can be written under public/. Route unauthenticated.
- InvoiceSealController accepts SVG, stores in public/uploads/invoice_seals, any authenticated user.
- Migrations 2026_09_07 / 2026_09_10 write placeholder MIMAS / Aadhaar numbers into existing customer rows.
- BranchController@destroy hard-deletes all users of a branch.
- ProductController@store stores computed values into wrong columns (cast_per/mrp).
- MasterPipelineService::forwardStage always sets pipeline_status=in_progress; unknown department names pass isValidForwardTransition (rank 0 → true); payload=request->all() lets caller set department record ids.
- Phase status: migrations 72/72 extracted; models 60/60 structure-extracted.
- CustomerController (lease): draft state in session `lease_draft` (incl. plaintext MIMAS password); DB draft row LA-DRAFT-YYYY-NNNN created at step1; submit() renumbers to LA-YYYY-NNNN, status under_scrutiny. Lease branch_id = Branch::first() (CustomerController.php:183,1568) not the user's branch → BranchScope may hide records from non-admin users of other branches; app-number generators (2522-2560) are subject to BranchScope (no withoutGlobalScopes) → possible duplicate application_no for non-admin users.
- Lease statuses used: draft, under_scrutiny, validated, revision_required, approved (+enum leftovers submitted/rejected/expired). approveApplication accepts under_scrutiny OR validated (skips validation). No separate approve permission (application.edit).
- moveToMining requires status=approved; common_id derived from LA number (GTMS-YYYY-NNNN) — separate from MasterPipelineService::generateMasterId sequence → two GTMS id generators can disagree; master overwrites lease.common_id.
- submit() uses hard-coded folder ids 7/8/9 and document_field_id 1..19 (CustomerController ~1747) → depends on seed order.
- generateReport returns HTML (not PDF) with mimasCredentials loaded.
- Controllers inspected so far: Auth, Unit, ProductStock, Category, Product, Branch, User, Roles, Notification, ChunkUpload, InvoiceSeal, MasterPipeline (+Service), Customer (key methods).
- Docs written so far: 00-08 (2026-11-12).
- Docs written: 00-14.
- Docs written: 00-19.
- Docs written: 00-22.
