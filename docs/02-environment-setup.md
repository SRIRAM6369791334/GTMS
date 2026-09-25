# GTMS Environment Setup & Configuration Guide

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Target Platform:** Laravel 12.x / PHP 8.2 / MariaDB / Apache (XAMPP Environment)  
**Operating Systems Supported:** Windows 10/11 (Primary District Office Standard), Ubuntu 22.04+ LTS (Server Deployment)  
**Authoritative Source:** Codebase Configuration & Operational Runbook  
**Document Number:** `02` of `23`  

---

## 1. System Requirements & Hardware Prerequisites

GTMS manages large-scale statutory dossiers, high-resolution geodetic maps, AutoCAD `.dwg` boundary files, drone orthomosaics, and multi-megabyte PDF application attachments. Deployment environments must meet or exceed the following specifications:

### 1.1 Hardware Specifications

| Component | Minimum Specification (Developer Workstation) | Recommended Specification (District Office Server) | Enterprise Concurrency (State Data Center) |
| :--- | :--- | :--- | :--- |
| **Processor (CPU)** | Intel Core i5 / AMD Ryzen 5 (4 Cores, 2.5 GHz) | Intel Core i7 / Xeon (8 Cores, 3.2 GHz+) | Intel Xeon Gold / AMD EPYC (16+ Cores) |
| **System Memory (RAM)** | 8 GB DDR4 | 16 GB DDR4/DDR5 | 32 GB – 64 GB ECC RAM |
| **Storage (Disk)** | 256 GB SSD (SATA III) | 512 GB NVMe PCIe SSD | 1 TB – 2 TB NVMe RAID 10 Array |
| **Network Interface** | 100 Mbps Ethernet / Wi-Fi | 1 Gbps Gigabit LAN | 10 Gbps Redundant Uplink |
| **Power Backup** | Laptop Battery | 1 KVA Uninterruptible Power Supply (UPS) | Dual Redundant UPS + Generator Backup |

### 1.2 Software Stack & Runtime Dependencies

* **Operating System:** Windows 10/11 Pro (64-bit) or Ubuntu Linux 22.04 LTS.
* **Local Server Stack:** XAMPP for Windows version 8.2.12 or standalone Apache/MariaDB installation.
* **PHP Engine:** PHP 8.2.x (Verified: PHP 8.2.12 ZTS Visual C++ 2019 x64).
* **Database Engine:** MariaDB 10.4.32+ or MySQL 8.0.35+ with InnoDB support.
* **Package Managers:**
  - PHP: Composer 2.7.x or higher (`composer -V`).
  - Node.js: Node.js 18.x or 20.x LTS (`node -v`) with NPM 9.x+ (`npm -v`).
* **Web Browser:** Google Chrome 110+, Microsoft Edge 110+, or Mozilla Firefox 115+.

---

## 2. PHP 8.2 Configuration & Performance Tuning

Quarry regulatory filings require uploading multi-page environmental reports and high-resolution CAD surveys. The default `php.ini` shipped with XAMPP restricts uploads to 2 MB, which will cause immediate `413 Payload Too Large` or `UploadException` errors.

### 2.1 Mandatory PHP Extensions
Open `C:\xampp\php\php.ini` (Windows) or `/etc/php/8.2/fpm/php.ini` (Linux) and ensure the following extensions are enabled (remove the leading semicolon `;`):

```ini
extension=bcmath
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=tokenizer
extension=xml
extension=zip
```

### 2.2 Critical `php.ini` Directives for GTMS
Adjust the following directives to accommodate CAD drawings, drone orthomosaics, and bulk file uploads:

```ini
; ==============================================================================
; GTMS Enterprise PHP 8.2 Tuning Parameters
; ==============================================================================

; Maximum allowed size for uploaded files. Set to 128M to handle CAD/Orthomosaics.
upload_max_filesize = 128M

; Must be equal to or greater than upload_max_filesize.
post_max_size = 128M

; Maximum memory a script may consume. Prevents memory exhaustion during PDF generation.
memory_limit = 512M

; Maximum execution time of each script, in seconds.
max_execution_time = 300

; Maximum amount of time each script may spend parsing request data.
max_input_time = 300

; Maximum number of concurrent input variables (form fields). Important for complex wizards.
max_input_vars = 5000

; Ensure default timezone matches Indian Standard Time (IST)
date.timezone = "Asia/Kolkata"
```

*After updating `php.ini`, restart Apache from the XAMPP Control Panel or via `sudo systemctl restart apache2`.*

---

## 3. Web Server & Database Engine Configuration

### 3.1 Apache VirtualHost Configuration (Windows XAMPP)
To serve GTMS over a clean local domain (e.g. `http://gtms.local`), configure Apache as follows:

1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and append:

```apache
<VirtualHost *:80>
    ServerName gtms.local
    ServerAlias www.gtms.local
    DocumentRoot "C:/xampp/htdocs/GTMS/gtms/public"

    <Directory "C:/xampp/htdocs/GTMS/gtms/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/gtms-error.log"
    CustomLog "logs/gtms-access.log" combined
</VirtualHost>
```

2. Open `C:\Windows\System32\drivers\etc\hosts` in Administrator mode and add:
```text
127.0.0.1   gtms.local
```

3. Ensure Apache module `mod_rewrite` is enabled in `C:\xampp\apache\conf\httpd.conf`:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

### 3.2 MariaDB / MySQL Configuration
GTMS requires standard UTF8mb4 full unicode support and adequate connection capacity.

1. Open `C:\xampp\mysql\bin\my.ini` and verify:
```ini
[mysqld]
default-storage-engine=INNODB
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
max_allowed_packet=128M
max_connections=500
sql_mode=NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES
```

2. Create the GTMS database through the MySQL Command Line:
```sql
CREATE DATABASE gtms_data 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;
```

---

## 4. Categorized `.env` Environment Reference Guide

The `.env` configuration file controls runtime environment variables. In adherence to strict security standards, **all passwords, keys, and tokens are redacted (`[REDACTED]`)**.

### 4.1 Master `.env` Configuration File

```dotenv
# ==============================================================================
# 1. CORE APPLICATION CONFIGURATION
# ==============================================================================
# The application display name used across browser titles, invoices, and notification emails.
APP_NAME="GTMS"

# Application environment: 'local' for development, 'staging', or 'production'.
APP_ENV=local

# 32-character AES-256 base64 application key used for encrypting cookies, sessions,
# and credentials. Generated via 'php artisan key:generate'.
APP_KEY=[REDACTED]

# Debug mode: true displays full stack traces. MUST BE false in production.
APP_DEBUG=true

# Canonical root URL of the application. Used for generating asset URLs and email links.
APP_URL=http://localhost:8002

# Localization settings. System operations run on Indian Standard Time.
APP_TIMEZONE=Asia/Kolkata
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

# Maintenance mode driver ('file' or 'cache').
APP_MAINTENANCE_DRIVER=file

# ==============================================================================
# 2. SECURITY & CRYPTOGRAPHY
# ==============================================================================
# Bcrypt hashing cost factor for user passwords. Default: 12 rounds.
BCRYPT_ROUNDS=12

# ==============================================================================
# 3. LOGGING SYSTEM
# ==============================================================================
# Log driver channel: 'stack', 'single', 'daily', or 'syslog'.
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
# Minimum severity level to log: 'debug', 'info', 'notice', 'warning', 'error'.
LOG_LEVEL=debug

# ==============================================================================
# 4. PRIMARY RELATIONAL DATABASE CONFIGURATION
# ==============================================================================
# Default database connection driver: 'mysql' or 'mariadb'.
DB_CONNECTION=mysql

# Database host address. In local XAMPP environments, use 127.0.0.1.
DB_HOST=127.0.0.1

# MySQL / MariaDB standard TCP port.
DB_PORT=3306

# Primary database schema name.
DB_DATABASE=gtms_data

# Database connection user account.
DB_USERNAME=root

# Database connection password. Strictly redacted in documentation.
DB_PASSWORD=[REDACTED]

# Character encoding and collation.
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# ==============================================================================
# 5. STATEFUL STORES: SESSIONS, CACHE, QUEUES & STORAGE
# ==============================================================================
# HTTP session storage driver: 'database' stores active sessions in the `sessions` table,
# avoiding external Redis requirements in on-premise XAMPP environments.
SESSION_DRIVER=database

# Session expiration lifetime in minutes. Default: 120 minutes (2 hours).
SESSION_LIFETIME=120

# Encrypt session payloads in database storage.
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache store: 'database' caches queries and tags in `cache` and `cache_locks` tables.
CACHE_STORE=database

# Queue driver: 'database' processes background jobs via `jobs` and `failed_jobs`.
QUEUE_CONNECTION=database

# Event broadcasting driver. Default: 'log' for local development.
BROADCAST_CONNECTION=log

# Default filesystem disk: 'local' maps to storage/app/ and public/uploads/.
FILESYSTEM_DISK=local

# ==============================================================================
# 6. REDIS CONFIGURATION (OPTIONAL / UNUSED IN ON-PREMISE XAMPP)
# ==============================================================================
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=[REDACTED]
REDIS_PORT=6379

# ==============================================================================
# 7. NOTIFICATION & EMAIL CONFIGURATION
# ==============================================================================
# Mailer driver: 'log' outputs emails to storage/logs/laravel.log during development.
# In production, change to 'smtp'.
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=[REDACTED]
MAIL_PASSWORD=[REDACTED]
MAIL_FROM_ADDRESS="no-reply@gtms.gov.in"
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================================================
# 8. CLOUD OBJECT STORAGE (OPTIONAL AWS S3 DRIVER)
# ==============================================================================
AWS_ACCESS_KEY_ID=[REDACTED]
AWS_SECRET_ACCESS_KEY=[REDACTED]
AWS_DEFAULT_REGION=ap-south-1
AWS_BUCKET=[REDACTED]
AWS_USE_PATH_STYLE_ENDPOINT=false

# ==============================================================================
# 9. FRONTEND ASSET COMPILATION (VITE)
# ==============================================================================
VITE_APP_NAME="${APP_NAME}"
```

### 4.2 Comprehensive `.env` Variable Dictionary

| Variable Key | Required | Default Value | Valid Options | Operational Description |
| :--- | :--- | :--- | :--- | :--- |
| `APP_NAME` | Yes | `GTMS` | Free string | Human-readable system title rendered on headers and invoice headers. |
| `APP_ENV` | Yes | `local` | `local`, `testing`, `production` | Dictates error verbosity and framework caching behaviors. |
| `APP_KEY` | Yes | Generated | 32-char Base64 string | Encrypts session data, cookies, and `mimas_credentials.password`. |
| `APP_DEBUG` | Yes | `true` | `true`, `false` | Enables ignition error screens. Must be `false` on live district servers. |
| `APP_URL` | Yes | `http://localhost:8002` | Valid URI | Base URL for generating canonical asset hyperlinks and action paths. |
| `APP_TIMEZONE` | Yes | `Asia/Kolkata` | Valid IANA Timezone | Forces all `created_at` timestamps to match Indian Standard Time. |
| `DB_CONNECTION` | Yes | `mysql` | `mysql`, `mariadb`, `sqlite` | Relational database driver. |
| `DB_HOST` | Yes | `127.0.0.1` | IP / Hostname | Database server network location. |
| `DB_PORT` | Yes | `3306` | Port Number | TCP port for MariaDB/MySQL. |
| `DB_DATABASE` | Yes | `gtms_data` | String | Target MySQL schema. |
| `DB_USERNAME` | Yes | `root` | String | Database user credential. |
| `DB_PASSWORD` | Yes | `[REDACTED]` | String | Database password (blank by default on local XAMPP). |
| `SESSION_DRIVER`| Yes | `database` | `file`, `database`, `redis` | Controls where browser sessions are serialized. |
| `CACHE_STORE` | Yes | `database` | `file`, `database`, `redis` | Target backend for application cache. |
| `QUEUE_CONNECTION`| Yes| `database` | `sync`, `database`, `redis` | Driver used to queue deferred jobs and notifications. |
| `FILESYSTEM_DISK`| Yes | `local` | `local`, `public`, `s3` | Root filesystem disk. GTMS stores uploads under `public/uploads/`. |
| `MAIL_MAILER` | Yes | `log` | `log`, `smtp`, `sendmail` | Outgoing email transport mechanism. |

---

## 5. Step-by-Step Local Deployment & Initialization Procedure

Follow this procedural runbook to install and boot a fresh instance of GTMS on a new workstation:

### Step 1: Clone the Codebase
```bash
cd C:\xampp\htdocs\GTMS
git clone <repository-url> gtms
cd gtms
```

### Step 2: Install Composer Dependencies
Install all backend PHP dependencies specified in `composer.json` and `composer.lock`:
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
```

### Step 3: Initialize Environment Configuration
Copy the template configuration file and generate a cryptographically secure application key:
```bash
cp .env.example .env
php artisan key:generate
```
*Verify that `DB_DATABASE=gtms_data` in `.env` matches your target MySQL database.*

### Step 4: Create Database Schema
Open MySQL CLI or phpMyAdmin and execute:
```sql
CREATE DATABASE IF NOT EXISTS gtms_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 5: Execute Database Migrations
Run the 48 chronological migrations to construct all 64 tables:
```bash
php artisan migrate
```

### Step 6: Populate Master Data & Demo Records (Seeders)
Execute the database seeders in exact dependency order:

1. **Execute Core Seeder Suite:**
   ```bash
   php artisan db:seed
   ```
   *This executes `DatabaseSeeder`, which sequentially triggers:*
   - `RolePermissionSeeder`: Spatie roles (Super Admin, Branch Manager, Officer, RQP) and permissions.
   - `GtmsMasterDataSeeder`: 38 Tamil Nadu districts, minerals, lease categories, folders, and document checklists.
   - `MiningNatureOfWorkSeeder`: Natures of work and statutory checklist requirements.
   - `CustomerSeeder`: Baseline quarry owners, Customer 360 records, and contact profiles.

2. **Execute Supplementary Module Seeders:**
   ```bash
   php artisan db:seed --class=PptAndDgpsModuleSeeder
   php artisan db:seed --class=EcComplianceSeeder
   ```
   *Populates presentation agendas, DGPS boundary coordinates, and half-yearly compliance cycles.*

### Step 7: Create Public Storage Link & Upload Directory Hierarchy
1. Link Laravel storage to public root:
   ```bash
   php artisan storage:link
   ```

2. Verify or create the module upload directory tree under `public/uploads/`:
   ```bash
   mkdir -p public/uploads/lease
   mkdir -p public/uploads/mining
   mkdir -p public/uploads/environmental
   mkdir -p public/uploads/ec_certificates
   mkdir -p public/uploads/ppt
   mkdir -p public/uploads/dgps
   mkdir -p public/uploads/drone
   mkdir -p public/uploads/ec_compliance
   ```
   *On Linux, grant write permissions to the web server: `sudo chown -R www-data:www-data public/uploads storage bootstrap/cache`.*

### Step 8: Install Node Modules & Compile Frontend Assets
```bash
npm install
npm run build
```
*For active UI development with hot reloading, use `npm run dev` instead.*

### Step 9: Launch Development Server
```bash
php artisan serve --port=8002
```
Access the application in your browser at `http://127.0.0.1:8002` or `http://localhost:8002`.

---

## 6. Artisan Console Commands Catalog

The following Artisan commands are essential for administering, debugging, and maintaining GTMS:

### 6.1 Database & Migration Management
| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `php artisan migrate` | Executes all pending migrations. | Deployment & updates. |
| `php artisan migrate:status` | Shows status of each migration file. | Verification of schema state. |
| `php artisan migrate:rollback --step=1` | Reverts the last migration batch. | Debugging recent schema edits. |
| `php artisan migrate:fresh --seed` | Drops all tables, re-runs migrations, seeds data. | Resetting local test database. *(NEVER RUN IN PRODUCTION)* |
| `php artisan db:seed --class=<SeederName>` | Executes a specific seeder class. | Seeding individual module test sets. |

### 6.2 Caching & Optimization Management
| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `php artisan optimize` | Caches routes, configurations, and views. | Mandatory post-deployment step. |
| `php artisan optimize:clear` | Clears all config, route, view, and event caches. | When `.env` or routes are changed. |
| `php artisan config:cache` | Compiles `.env` and `config/*.php` into a single file. | Production performance boost. |
| `php artisan config:clear` | Removes compiled configuration cache. | After editing `.env` parameters. |
| `php artisan route:cache` | Serializes routes for instant routing matching. | Production deployment. |
| `php artisan route:clear` | Clears compiled route cache. | When routes in `routes/web.php` change. |
| `php artisan view:cache` | Precompiles all Blade templates into PHP files. | Production deployment. |
| `php artisan view:clear` | Deletes precompiled Blade files. | Debugging frontend markup. |

### 6.3 Queue & Background Task Management
| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `php artisan queue:work` | Starts processing jobs from the `jobs` table. | Required if asynchronous tasks are enabled. |
| `php artisan queue:listen` | Listens for jobs with automatic reload on code edit. | Local development debugging. |
| `php artisan queue:failed` | Lists all failed queue jobs from `failed_jobs`. | Troubleshooting failed background tasks. |
| `php artisan queue:retry all` | Retries all failed queue jobs. | Recovering from transient errors. |
| `php artisan queue:flush` | Deletes all records from `failed_jobs`. | Purging dead failed queue records. |

### 6.4 Inspection & Utility Commands
| Command | Purpose | When to Use |
| :--- | :--- | :--- |
| `php artisan route:list` | Displays all 121 registered HTTP routes. | Auditing verbs, middleware, and URIs. |
| `php artisan route:list --path=lease` | Filters routes matching a specific URI prefix. | Debugging specific module endpoints. |
| `php artisan storage:link` | Creates symbolic link from `storage/app/public` to `public/storage`. | Mandatory during initial setup. |
| `php artisan about` | Summarizes PHP, Laravel, cache, and database configuration. | System diagnostic reporting. |

---

## 7. Troubleshooting Common Setup & Runtime Issues

### 7.1 "413 Payload Too Large" or Upload Failure on CAD/PDF Files
* **Symptom:** File uploads fail silently or return HTTP 413 when attaching large AutoCAD drawings, KMLs, or EIA reports.
* **Root Cause:** PHP `upload_max_filesize` or `post_max_size` in `php.ini` is set below the file size (default: 2M).
* **Fix:** Open `C:\xampp\php\php.ini`, set `upload_max_filesize = 128M` and `post_max_size = 128M`, and restart Apache.

### 7.2 "SQLSTATE[HY000] [2002] Connection refused" or "Access denied for user 'root'"
* **Symptom:** Application throws database connection exception upon initial boot.
* **Root Cause:** MariaDB service in XAMPP is not running, or credentials in `.env` do not match.
* **Fix:** 
  1. Open XAMPP Control Panel and start MySQL/MariaDB.
  2. Verify `.env` parameters: `DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=gtms_data`.

### 7.3 "Vite manifest not found at ... public/build/manifest.json"
* **Symptom:** Application crashes with `ViteManifestNotFoundException` on Blade rendering.
* **Root Cause:** Frontend assets have not been compiled using Vite.
* **Fix:** Run `npm install` followed by `npm run build`. Alternatively, run `npm run dev` to launch the Vite hot-module development server.

### 7.4 "Class 'ZipArchive' not found" or "Call to undefined function imagecreatefrompng()"
* **Symptom:** Report generation or image processing throws fatal PHP error.
* **Root Cause:** Required PHP extension `zip` or `gd` is disabled in `php.ini`.
* **Fix:** Edit `php.ini`, remove the semicolon `;` from `extension=zip` and `extension=gd`, and restart Apache.

### 7.5 Windows Long Path Limitations (`MAX_PATH` 260 Characters)
* **Symptom:** Node package installation or nested document folder creation fails with `EPERM` or path truncation errors.
* **Root Cause:** Windows legacy 260-character maximum path limit.
* **Fix:** Run PowerShell as Administrator and execute:
  ```powershell
  New-ItemProperty -Path "HKLM:\SYSTEM\CurrentControlSet\Control\FileSystem" -Name "LongPathsEnabled" -Value 1 -PropertyType DWORD -Force
  ```
