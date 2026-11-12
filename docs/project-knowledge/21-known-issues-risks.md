# 21 — Known Issues & Risks Register

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Security items are summarised here; full detail is in [18-security-audit.md](18-security-audit.md). Performance and quality are in [19](19-performance-code-quality-audit.md).
> "Status" here is the evidence level, not a fix status. **Nothing has been fixed. This audit changed no code.**

| ID | Issue | Area | Severity | Evidence | Status |
|---|---|---|---|---|---|
| K1 | Unauthenticated chunk upload API with path traversal and extension bypass | Security | Critical | `routes/web.php:633-640`, `ChunkedUploadService::assembleFile` | VERIFIED code |
| K2 | Plaintext user passwords (`show_password`) returned in JSON | Security | Critical | `UserController:123,257`, `User::$hidden` | VERIFIED |
| K3 | Merge to `main` = unattended production deploy + auto-migration (no backup on that path, errors swallowed) | Ops | High | `deploy.yml`, `docker-compose.yml` watchtower, `entrypoint.sh` | VERIFIED |
| K4 | Uploaded file links may 404 in Docker, because Nginx sends `/uploads/*` to `index.php` | Functional | High | `docker/nginx.conf` location order; `asset($doc->file_path)` in 15 views | INFERRED — verify on the NAS |
| K5 | Lease number generators and lease `branch_id = Branch::first()` interact with BranchScope. Non-admin users in other branches may get duplicate `application_no` errors, and may not see leases they created | Functional / multi-tenancy | High | `CustomerController.php:183,1568,2522-2560`, `BranchScope` | INFERRED from code |
| K6 | `GTMS-YYYY-NNNN` produced by three generators (lease-derived, mining sequence, master sequence); master creation overwrites `lease.common_id` | Data integrity | High | `CustomerController@moveToMining`, `MiningController::generateMiningAppNumber`, `MasterPipelineService::createOrGetMasterProject` | VERIFIED |
| K7 | Production code writes placeholder values as if real (DGPS completed / verified on create, random field book, fixed instrument serial; drone RPC / UIN; EC compliance random PARIVESH and fixed NABL numbers; EC cert PARIVESH number; PPT fee 25,000 marked paid; default MIMAS password; default ₹1.5 L invoice line) | Data integrity | High | see [12](12-business-logic.md) R-SURVEY, R-ENV.5, R-LEASE.9 | VERIFIED |
| K8 | Migrations wrote placeholder MIMAS numbers and Aadhaar numbers into existing customer rows; check production for `TN-MMS-CUST-####` / `9876-5432-####` values | Data integrity | High | `2026_09_07_050811`, `2026_09_10_102535` | VERIFIED (migration); production impact UNKNOWN |
| K9 | Plaintext third-party portal passwords; PII (Aadhaar / PAN) exposed via lookup API | Security / privacy | High | [18](18-security-audit.md) S3, S4 | VERIFIED |
| K10 | Missing permission checks on the master pipeline and invoice seal | Security | High | S5 | VERIFIED |
| K11 | The module "move" buttons and the master pipeline do not update each other, so a project's stage can disagree between them | Design | High | [13](13-business-workflows.md) W9 | VERIFIED |
| K12 | Lease approval possible without validation; no segregation of duties | Process | Medium | `approveApplication` | VERIFIED |
| K13 | Mining → environment move has no approval gate; `advanceStage` accepts any value | Process | Medium | `MiningController` | VERIFIED |
| K14 | Free-text status updates (env / PPT / EC cert / EC compliance) bypass workflow rules and break KPI counters | Process | Medium | `updateStatus` methods | VERIFIED |
| K15 | Pipeline can never reach `pipeline_status = completed` | Functional | Medium | `MasterPipelineService::forwardStage` | VERIFIED |
| K16 | `DELETE /notifications/clear-all` is shadowed by `/notifications/{id}` | Functional | Medium | `routes/web.php:66-67` | INFERRED (routing order) |
| K17 | Notifications auto-seeded with demo content for every new user in production | Data quality | Medium | `NotificationController@index` | VERIFIED |
| K18 | Overpayment accepted; receipt customer not cross-checked with the application customer; no receipt void | Finance | Medium | `PaymentCollectionController@store` | VERIFIED |
| K19 | Money duplicated in three places (application row, `application_payments`, receipts) | Finance | Medium | [07](07-database-schema.md) J.4 | VERIFIED |
| K20 | Uploaded files are not in the backup scripts; DB rollback is not coupled to image rollback | Ops | Medium | `nas-backup.sh`, `nas-rollback.sh` | VERIFIED |
| K21 | Tests run against MySQL `gtms_data` via `.env`; no CI coverage of feature tests | Quality / safety | Medium | `phpunit.xml`, `deploy.yml` | VERIFIED |
| K22 | Branch delete hard-deletes its users | Admin | Medium | `BranchController@destroy` | VERIFIED |
| K23 | Customer unique indexes include soft-deleted rows while validation ignores them, so the DB error surfaces as a 500 | Functional | Low | migrations vs `Rule::unique(...)->whereNull('deleted_at')` | INFERRED |
| K24 | Hard-coded IDs (module 1, folders 7/8/9, doc fields 1–19, mineral / category 1) | Maintainability | Medium | [19](19-performance-code-quality-audit.md) Q4 | VERIFIED |
| K25 | No scheduler: lease / EC expiry alerts promised by schema comments do not exist | Feature gap | Low | `routes/console.php` | VERIFIED |
| K26 | 12 dormant tables / models and unused views / permissions | Maintainability | Low | [04](04-module-documentation.md) | VERIFIED |
| K27 | Legacy inventory module with incorrect price arithmetic | Functional | Low | `ProductController@store` | VERIFIED |
| K28 | `CHUNKS_STORAGE_PATH` only configures an unused `chunks` disk; the upload engine hard-codes its path | Config | Informational | `config/filesystems.php:53`, `ChunkedUploadService::getChunkDirectory` | VERIFIED |
| K29 | Earlier AI-generated docs in `docs/` (00–23, function catalog, database-analysis) may be stale; `database-analysis/00_ARCHIVE_AND_OUTDATED_WARNING.md` says so for that folder | Knowledge | Informational | files | VERIFIED |

## Unknowns that need the client or live access
1. Is the NAS deployment live, and with how many users and branches? Is the GHCR image public or private?
2. Do the production `customers` rows still contain the migration placeholder MIMAS / Aadhaar values?
3. Does downloading files work in production (K4)?
4. Which statuses do staff actually use in the free-text status fields?
5. Is the legacy inventory module (products / categories / units) used at all?
6. Are the dormant tables (mineral stock, boundary points, production schedules, PPT agendas) planned features?
7. Business intent of `converted` quotations, overpayments, refunds, and receipt cancellation.
8. Should the master pipeline replace the module "move" buttons, or the reverse?
