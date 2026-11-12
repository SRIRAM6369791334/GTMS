# 18 — Security Audit

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). This is a static code review only. **No exploit was attempted, and no request was sent to any running instance.** "Exploitability" lines are reasoned from code.
> Severity scale: Critical / High / Medium / Low / Informational.

## Summary
| ID | Finding | Severity | Status |
|---|---|---|---|
| S1 | Unauthenticated chunk-upload API with path traversal and unrestricted extension → arbitrary file write under `public/` (possible remote code execution) | **Critical** | VERIFIED (code); exploit path INFERRED |
| S2 | Plaintext user passwords in `users.show_password`, returned in JSON | **Critical** | VERIFIED |
| S3 | Third-party portal passwords (PARIVESH, email) stored plaintext and exposed via lookup JSON / views; MIMAS password plaintext in session | High | VERIFIED |
| S4 | PII exposure: Aadhaar, PAN returned by `lookupByMimas` to anyone with `customer.view` or `environment.view` | High | VERIFIED |
| S5 | Missing authorization on `/master-pipeline/*` and `/accounts/invoices/seal-*` | High | VERIFIED |
| S6 | Uploaded documents are publicly downloadable without authentication (Apache / XAMPP); SVG / XML uploads allowed (stored XSS when opened from the same origin) | High | VERIFIED (code) / INFERRED (serving) |
| S7 | Auto-deploy of `main` to production with auto-migrate; Watchtower on the `:latest` tag with docker.sock; CI runs one trivial test | High | VERIFIED |
| S8 | Invoice seal upload accepts SVG and is global, not tied to any user | Medium | VERIFIED |
| S9 | IDOR / no branch isolation on document endpoints (find by id, fuzzy `LIKE` name match on remove) | Medium | VERIFIED |
| S10 | Stored XSS via `{!! addslashes($value) !!}` inside `<script>` in lease step 1 | Medium | VERIFIED |
| S11 | Login reveals account existence and inactive status (user enumeration) | Medium | VERIFIED |
| S12 | State-changing GET (`create-from-lease`) | Medium | VERIFIED |
| S13 | Exception messages echoed to clients (chunk API) | Low | VERIFIED |
| S14 | Hard-coded seed credentials and placeholder MIMAS password in code | Medium | VERIFIED |
| S15 | Missing security headers (no CSP / HSTS); `X-XSS-Protection` deprecated; app served over plain HTTP on port 8000 (TLS UNKNOWN) | Low | VERIFIED (config) |
| S16 | Public `/system/version` discloses PHP / Laravel versions | Low | VERIFIED |
| S17 | Branch delete hard-deletes all users in the branch | Medium (integrity) | VERIFIED |
| S18 | `$guarded = []` on `User` (mass-assignment foot-gun) | Low | VERIFIED |
| S19 | Free-text status updates bypass workflow rules | Medium (integrity) | VERIFIED |
| S20 | Tests point at the `gtms_data` MySQL database | Medium (data safety) | VERIFIED |

## Details

### S1 — Chunk upload: unauthenticated arbitrary file write (Critical)
* **Evidence:** `routes/web.php:633-640` (outside `Route::middleware('auth')`); `ChunkUploadController@init` validates `target_module` only as `nullable|string|max:50`; `ChunkedUploadService::assembleFile` builds `public_path('uploads/' . trim($module, '/') . '/REF-'.$refId)`; `initUpload` keeps the original extension (`Str::slug($basename) . '.' . strtolower($extension)`).
* **Attack:** A guest first loads `/login` to obtain a session and CSRF token, then calls `init` with `file_name=x.php` and `target_module=../<dir>`, uploads the chunk and calls `assemble`. The file lands in a web-reachable directory outside `uploads/`. Apache (XAMPP) executes `.php` anywhere under `public/`. Nginx executes any `*.php` outside the `/uploads` regex location. The file name is returned in the JSON (`file_path`).
* **Also:** Tokens have no owner check (`user_id` is stored but never compared). The 50 GB limit, disk exhaustion and the 5% purge lottery allow denial of service by disk filling.
* **Fix direction (not implemented):** move the routes inside `auth`; whitelist `target_module`; reject `..` and absolute paths; enforce an extension allow-list at `init` and again at `assemble`; bind the token to `Auth::id()`; store outside `public/` and serve through an authorised controller.

### S2 — Plaintext user passwords (Critical)
`UserController@store` / `@update` set `$user->show_password = $request->password` (lines 123, 257). The migration `2026_07_08_070939_*` adds the column. `User::$hidden` lists only `password` and `remember_token`, so `response()->json(['data' => $user])` returns the plaintext password to the browser after every create or edit. The seeder writes a default password into this column too. Anyone with DB, backup or log access reads every password. **Fix direction:** drop the column after confirming nobody depends on it, add it to `$hidden` immediately, and force password resets.

### S3 — Third-party credentials (High)
* `customers.parivesh_password`, `customers.email_password`, and the same columns on `environment_projects`, `ppt_applications` and `ec_certificates`, have no `encrypted` cast (only `MimasCredential::password` is encrypted).
* `CustomerDirectoryController@lookupByMimas` returns them in JSON (lines ~371-372).
* `EnverionsoneController@submit*ToPpt` copies them into new PPT rows.
* Views display them (66 references).
* The lease wizard keeps the MIMAS password in plaintext in the session (`lease_draft.step2/step6`; Redis in prod, `SESSION_ENCRYPT=false` in `.env.example`).

### S4 — PII exposure (High)
`lookupByMimas` returns PAN, Aadhaar, phone, email and address. It is reachable through `/customers/lookup-mimas/{mimas_no}` (`customer.view`) and `/eviron/api/customer-lookup/{mimas_no}` (`environment.view`). Aadhaar numbers are stored unmasked (`customers.aadhaar_no`). Under Indian law (Aadhaar Act / DPDP Act 2023) this needs a lawful basis and masking (legal applicability is outside this audit's scope).

### S5 — Missing authorization (High)
* `/master-pipeline/*`: any authenticated user (including view-only Staff) can forward, return or resubmit, and can overwrite department record statuses through `updateDepartmentRecordStatus`. `forward` passes `$request->all()` as the payload, so a caller can set `dgps_survey_id` / `record_id` and attach arbitrary record IDs to a master project.
* `/accounts/invoices/seal-*`: any user can replace the seal printed on every invoice and receipt.

### S6 — Public files and active content (High)
Every module stores files under `public/` or `storage/app/public` (symlinked). No authorisation applies to direct URLs. SVG and XML are allowed, and an SVG opened directly on the GTMS origin runs script (stored XSS). See [16](16-file-upload-storage-system.md) §4 for the Nginx behaviour.

### S7 — Supply chain / deployment (High)
* No human approval between merge and production. Migrations run automatically, and failures are swallowed (`migrate --force || true`).
* `containrrr/watchtower:latest` with `/var/run/docker.sock` gives a full host-level container controller.
* GitHub Actions are pinned by SHA (good). Only `--testsuite=Unit` (one example test) runs in CI. The 38 feature tests do not gate deploys.
* The GHCR token is optional in compose. If the image is public, anyone can pull the full application code (visibility UNKNOWN).

### S8–S20 (summary)
* **S8** `InvoiceSealController@upload`: `mimes:...,svg`, stored at a public URL, no permission.
* **S9** `EnverionsoneController@removeDocument` loads `EnvironmentDocument::find($docId)` with no project or branch check, and falls back to `document_name LIKE %input%`. The same pattern recurs in the other modules' `removeDocument`. Document models carry no `BelongsToBranch`.
* **S10** `createstep1.blade.php:93`.
* **S11** `AuthController@login` returns distinct messages for unknown and inactive accounts; `throttle:15,1` is the only brute-force control.
* **S12** `Route::match(['get','post'], '/master-pipeline/create-from-lease/{leaseId}')`.
* **S13** `ChunkUploadController` returns `'...: ' . $e->getMessage()`, which can include filesystem paths.
* **S14** `RolePermissionSeeder` creates known accounts with fixed passwords. `CustomerController@saveStep2` falls back to a fixed MIMAS password string.
* **S15** `docker/nginx.conf` sets X-Frame-Options, nosniff and X-XSS-Protection only.
* **S16** `routes/web.php:35-46`.
* **S17** `BranchController@destroy`.
* **S18** `app/Models/User.php`.
* **S19** `updateStatus` in environment / PPT / EC certificate / EC compliance accepts `required|string|max:100`, and mining `advanceStage` accepts unvalidated input.
* **S20** `phpunit.xml` sets `DB_CONNECTION=mysql`, `DB_DATABASE=gtms_data`. 37 feature tests use `DatabaseTransactions`, so they write into whatever database `DB_HOST` points to (rolled back). Files created by tests under `public/uploads` are not rolled back (INFERRED).

## Positive controls observed
CSRF on all POSTs (web group); `throttle` on login; session regeneration on login and logout; Spatie permissions on most module routes; delegated-admin subset checks; `lockForUpdate` on payment collection and master ID generation; `escapeshellarg` in the FFmpeg exec; `MimasCredential` encrypted cast; MySQL and Redis without host ports in compose; SHA-pinned GitHub Actions; `APP_KEY` required in production by the entrypoint.
