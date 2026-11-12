# 20 — Testing, Deployment & Configuration

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). No command in this section was executed during the audit. Every statement comes from reading files.

## 1. Automated tests — VERIFIED (from files)
* **PHPUnit** (`phpunit.xml`): suites `Unit` and `Feature`. The env forces `DB_CONNECTION=mysql`, `DB_DATABASE=gtms_data`, `CACHE_STORE=array`, `SESSION_DRIVER=array`, `QUEUE_CONNECTION=sync`, `MAIL_MAILER=array`, `BCRYPT_ROUNDS=4`. **`DB_HOST`, `DB_USERNAME` and `DB_PASSWORD` come from `.env`**, so tests run against whatever MySQL the developer's `.env` points to, in the database named `gtms_data`.
* 38 feature test files (~430 test methods). 37 use `DatabaseTransactions` (rollback per test); `ExampleTest` has `RefreshDatabase` commented out. Topics: accounts, handlers and payments, branch management, custom category rule, multi-tenancy branch scope, chunked upload pipeline, customer directory CRUD, tracking filters and invoices, document requirement toggle, drone / DGPS step 5, environmental clearance, invoice seal, master pipeline (controller / service / workflow / schema), MIMAS password view, mining plan transition, notifications, PPT / DGPS / EC compliance, privilege escalation, quarry area format, roles and permissions audit, step-4 folder view, step-5 uploads, phone numbers, user management and auth, plus several "Challenger … Stress / Adversarial" files.
* `tests/Unit/ExampleTest.php` is the only unit test.
* Tests were **not run** in this audit, so their pass / fail state is UNKNOWN.
* **E2E:** Playwright (`playwright.config.js`, testDir `tests/e2e`, chromium). `smoke.spec.js` is only a setup check. The `.mjs` scripts capture screenshots (`tests/e2e/screenshots/{baseline,after}`, 221 PNGs).
* `TEST_DATA_SAFETY_PROTOCOL.md` is a procedural protocol (8-stage test-data lifecycle) written for earlier AI sessions. It is not enforced by tooling.

**Safety rule for contributors:** do not run `php artisan test` while `.env` points at a database holding real data. Point `DB_DATABASE` at a disposable database through a `.env.testing` file (none exists in the repo).

## 2. CI pipeline — VERIFIED (`.github/workflows/deploy.yml`)
Trigger: push to `main`. Permissions: none by default.

| Job | Steps |
|---|---|
| validate-and-test | checkout → `composer validate --strict` → PHP 8.2 → Node 20, `npm ci && npm run build` → `composer install` → `php -l` over app / config / routes / tests → copy `.env.example`, key:generate, `route:list` → `php artisan test --testsuite=Unit` |
| build-and-publish (needs job 1) | buildx → login GHCR with `GITHUB_TOKEN` → build & push `ghcr.io/<owner>/gtms:main` and `:<sha>` with GHA cache |
There are no feature tests, no static analysis, no dependency audit and no PR workflow (only `main` pushes are built).

## 3. Container image — VERIFIED (`Dockerfile`)
Stage 1 `node:20-alpine` builds Vite assets (unused at runtime, see [01](01-technology-stack.md)). Stage 2 `php:8.2-fpm-alpine` + nginx + supervisor + mysql-client. Extensions: pdo_mysql, mbstring, bcmath, xml, ctype, fileinfo, gd, zip, opcache, redis. `composer install --no-dev`. The whole repo is copied, minus `.dockerignore` (excludes `.git`, `.github`, `.agents`, `vendor`, `node_modules`, uploads, `.env*`, logs). **Included:** `scratch/`, `tests/`, `docs/`, the root `*.md` files.

## 4. Runtime (Synology NAS) — VERIFIED from files, live state UNKNOWN
* `docker-compose.yml` services: `app` (port `${APP_PORT:-8000}:80`, healthcheck `curl /up`), `queue`, `redis` (AOF, 2 GB, volatile-lru), `db` (MySQL 8, no host port, native-password auth), `watchtower` (polls every 300 s, cleans up, scoped to `gtms_app` and `gtms_queue`).
* `docker/entrypoint.sh`: aligns `www-data` with PUID / PGID (Synology 1026:100), creates directories, chowns, deletes compiled views and bootstrap cache, fails if `APP_KEY` is empty in production, runs `storage:link`, waits for MySQL (30 tries), then **for the app container runs `php artisan migrate --force || true` and `optimize:clear`**.
* `docker/nginx.conf`: unlimited body size, long timeouts, a special location for upload paths (see [16](16-file-upload-storage-system.md) §4), basic headers.
* `docker/php.ini`: 100 MB upload / 120 MB post, 512 MB memory, opcache without timestamp validation.

## 5. Deployment procedures
1. **Automatic (default):** merge to `main` → CI → GHCR `:main` → Watchtower pulls within ~5 min → recreates app + queue → the entrypoint migrates. **There is no pre-migration backup on this path.**
2. **Manual:** `docker/nas-deploy.sh [tag]`: checks `.env` → GHCR login → pull → records the previous tag → **mysqldump pre-migration backup (aborts if empty)** → db / redis up → app / queue up → health check (auto-rollback of the image on failure) → `migrate --force` → `optimize`.
3. **Rollback:** `docker/nas-rollback.sh [tag]` re-pulls and recreates app / queue. **The database schema is not rolled back.** Restore: `docker/nas-restore.sh <file.sql>` into `gtms_data`.
4. **Backups:** `docker/nas-backup.sh`, MySQL dump only. Uploaded files are not included.

## 6. Configuration keys (names only; values intentionally omitted)
`.env.docker.example`: GTMS_IMAGE_TAG, GTMS_DATA_DIR, APP_PORT, APP_NAME, APP_ENV, APP_KEY, APP_DEBUG, APP_URL, PUID, PGID, DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DOCKER_DB_USER, DOCKER_DB_PASSWORD, DOCKER_DB_ROOT_PASSWORD, SESSION_DRIVER, QUEUE_CONNECTION, CACHE_STORE, REDIS_HOST, REDIS_PORT, REDIS_CLIENT, CHUNKS_STORAGE_PATH, GHCR_USERNAME, GHCR_TOKEN.
`.env.example` is the Laravel default (sqlite, database session / queue / cache, `APP_DEBUG=true`). **It cannot run this app as-is**, because the migrations are MySQL-specific.
`config/permission.php` is the Spatie default (not modified, INFERRED from size). `CHUNKS_STORAGE_PATH` feeds a `chunks` disk root in `config/filesystems.php:53`, but `ChunkedUploadService::getChunkDirectory` hard-codes `storage_path('app/chunks/…')` and never uses that disk (VERIFIED). Changing the variable therefore has no effect on the upload engine.

## 7. Local development (XAMPP) — INFERRED
The project lives at `C:\xampp\htdocs\GTMS\gtms` and is served by Apache through `public/.htaccess`, with a MySQL database `gtms_data`. `composer dev` (serve + queue:listen + pail + vite) is defined in `composer.json`.
