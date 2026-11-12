# 01 — Technology Stack

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Evidence files are named for each line.

## Backend — VERIFIED

| Component | Version / detail | Evidence |
|---|---|---|
| Language | PHP `^8.2`; Docker image `php:8.2-fpm-alpine`; CI uses PHP 8.2 | `composer.json`, `Dockerfile`, `.github/workflows/deploy.yml` |
| Framework | Laravel `^12.0`, using the slim Laravel 11+ bootstrap (`bootstrap/app.php` with `withRouting` / `withMiddleware`) | `composer.json`, `bootstrap/app.php` |
| Authorization | `spatie/laravel-permission ^6.25`; middleware aliases `role`, `permission`, `role_or_permission` | `bootstrap/app.php`, `config/permission.php` |
| REPL | `laravel/tinker` | `composer.json` |
| Dev tools | pint, pail, sail, collision, mockery, faker, PHPUnit `^11.5.50` | `composer.json` |
| Database (prod) | MySQL 8.0 (`utf8mb4_unicode_ci`, 4 GB InnoDB buffer pool) | `docker-compose.yml` |
| Database (local) | MySQL `gtms_data` under XAMPP. Tests are configured against `mysql` / `gtms_data` | `phpunit.xml` |
| Cache / session / queue (prod) | Redis 7 via `phpredis` | `docker-compose.yml` |
| Cache / session / queue (default `.env.example`) | database driver, `DB_CONNECTION=sqlite` (example only) | `.env.example` |
| Queue worker | `php artisan queue:work redis --queue=uploads,default --timeout=3600 --tries=3` | `docker-compose.yml` service `queue`; `docker/supervisord.conf` (disabled program) |
| Web server (prod) | Nginx + PHP-FPM under supervisord in one container | `docker/nginx.conf`, `docker/supervisord.conf` |
| Web server (local) | Apache via XAMPP (`public/.htaccess`) | path `C:\xampp\htdocs\GTMS\gtms` |
| PHP runtime limits | `upload_max_filesize=100M`, `post_max_size=120M`, `memory_limit=512M`, `max_execution_time=300`, opcache with `validate_timestamps=0` | `docker/php.ini` |

**Not used:** Laravel Sanctum/Passport (no API tokens), Form Request classes (all validation is inline `$request->validate`), Policies, Events/Listeners, Mail, Laravel Notifications classes (the `notifications` table is written directly), Scheduler (`routes/console.php` only has the default `inspire` command). No PDF library is installed — "PDF" reports are rendered as HTML (`CustomerController@generateReport`).

## Frontend — VERIFIED

| Component | Detail | Evidence |
|---|---|---|
| Rendering | Server-side Blade; every page extends `layouts.app` (master pipeline pages extend `layouts.master`, which itself extends `layouts.app`) | `resources/views/**` |
| UI kit | A purchased Bootstrap admin template ("deznav"/Dlab-style: `public/vendor/deznav`, `dlabnav-init.js`, `custom.min.js`) | `public/vendor`, `public/js` |
| JS libraries (static, `public/vendor`) | jQuery (global bundle), Bootstrap, bootstrap-select, DataTables + Responsive, SweetAlert2, Toastr, ApexCharts, Chart.js, Peity, Owl Carousel, counter/waypoint, select2, dropzone, smartwizard, jquery-steps, moment, nouislider, metismenu | `resources/views/layouts/app.blade.php:546-629` |
| Icons | Bootstrap Icons 1.11.3 from the jsDelivr CDN (external dependency) | `layouts/app.blade.php:38` |
| Custom JS | `public/js/gtms-chunk-uploader.js` (resumable uploader), `public/js/gtms-open-with.js` ("Open with" modal), `public/js/ajax/*.js` (CRUD modals) | — |
| Vite | `vite ^7`, `laravel-vite-plugin ^2`, `tailwindcss ^4` configured for `resources/css/app.css` and `resources/js/app.js`. **No layout calls `@vite`**, so the Vite bundle is built but never loaded (INFERRED from grep: no `@vite` in `resources/views`). | `vite.config.js`, `package.json` |
| E2E | `@playwright/test ^1.64.1`; `tests/e2e/smoke.spec.js` plus screenshot-capture scripts | `playwright.config.js` |

## Infrastructure — VERIFIED from files; live state UNKNOWN

* GitHub Actions → GHCR image `ghcr.io/<owner>/gtms:main` and `:<sha>`.
* Synology NAS host, Docker Compose services: `app`, `queue`, `redis`, `db`, `watchtower` (`containrrr/watchtower:latest`, poll interval 300 s).
* Operator scripts: `docker/nas-deploy.sh` (pull, backup, up, health check, rollback, migrate), `nas-backup.sh`, `nas-restore.sh`, `nas-rollback.sh`.
* Optional binary: FFmpeg, detected at runtime by `GenerateVideoThumbnailJob::detectFfmpegBinary`. It is not installed by the Dockerfile, so the job falls back to a placeholder thumbnail (INFERRED).

## External systems referenced (no API integration)

PARIVESH (national environmental clearance portal), MIMAS (Tamil Nadu mineral management portal), Google Docs Viewer / Google Earth / Google Maps / Autodesk Viewer (opened in a new tab by `gtms-open-with.js`). GTMS stores credentials and reference numbers for these portals but **never calls them**. See [17-integrations-background-jobs.md](17-integrations-background-jobs.md).
