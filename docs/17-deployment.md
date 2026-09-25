# GTMS — Production Deployment Guide & Server Architecture

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** District Mining Office Management & Statutory Regulatory ERP  
**State / Region:** Tamil Nadu, India  
**Target Architecture:** Monolithic Laravel 12 Enterprise Application  
**Authoritative Source:** Codebase Inspection & Production Infrastructure Standards  
**Document Number:** `17` of `23`

---

## 1. Executive Summary & Runtime Specifications

The **GTMS (Granite / Mining Tracking Management System)** is an enterprise resource planning system designed to manage high-volume statutory filings, multi-megabyte CAD/GIS boundary drawings, and synchronized revenue databases across 38 Tamil Nadu districts.

Deploying GTMS in production requires a hardened Linux environment (Ubuntu 22.04 LTS / Debian 12 / RHEL 9) or a managed Windows Server (IIS / Enterprise XAMPP) capable of handling concurrent statutory intake, PDF document compilation, and background queue workers.

### Server Runtime Matrix

| Runtime Layer | Supported Specification | Recommended Production Setup |
|---|---|---|
| **Operating System** | Linux (Ubuntu 22.04 LTS / Debian 12) or Windows Server | Ubuntu Server 22.04 LTS (x86_64) |
| **PHP Runtime** | PHP 8.2.0 – 8.3.x (CLI & FPM) | PHP 8.2.12 FPM with OPcache |
| **Web Server** | Nginx 1.24+ or Apache 2.4+ (with `mod_rewrite`) | Nginx 1.24+ as Reverse Proxy / Front-End Server |
| **Database Engine** | MySQL 8.0+ or MariaDB 10.11+ | MySQL 8.0 Enterprise (`gtms_data`), InnoDB Engine |
| **Asset Bundler** | Node.js 18.x / 20.x & NPM 10.x | Node.js 20 LTS (Vite 7.0.7 build pipeline) |
| **Process Manager** | Supervisor or Systemd | Supervisor 4.2+ (Queue Workers) |
| **Cron Daemon** | Standard Linux Cron | System crontab running `php artisan schedule:run` |

### Required PHP Extensions
The PHP 8.2 installation must have the following extensions active:
* `pdo_mysql` (Database communication)
* `bcmath` (Precision financial and decimal acreage computations)
* `ctype`, `tokenizer`, `xml`, `dom` (Framework core & template parsing)
* `curl` (External state portal communications)
* `fileinfo` (Strict MIME verification for statutory document uploads)
* `mbstring` (Multi-byte character handling)
* `openssl` (AES-256 credential and session encryption)
* `gd` or `imagick` (User avatar resizing and image thumbnail generation)
* `zip` (Document export and batch packaging)

---

## 2. Directory Permissions & File System Layout

The GTMS application stores uploaded documents directly on the local filesystem under `public/uploads/` partitioned by statutory module. The web server process (`www-data` on Debian/Ubuntu, `nginx`/`apache` on RHEL) must possess read and write permissions to specific operational directories.

### Required Directory Permissions

```bash
# 1. Set global project ownership
sudo chown -R www-data:www-data /var/www/gtms

# 2. Set directory permissions to 755 and file permissions to 644
sudo find /var/www/gtms -type d -exec chmod 755 {} \;
sudo find /var/www/gtms -type f -exec chmod 644 {} \;

# 3. Grant write permissions to writable paths (775)
sudo chmod -R 775 /var/www/gtms/storage
sudo chmod -R 775 /var/www/gtms/bootstrap/cache
sudo chmod -R 775 /var/www/gtms/public/uploads

# 4. Enforce group inheritance on uploads and storage
sudo chmod -R g+s /var/www/gtms/storage
sudo chmod -R g+s /var/www/gtms/public/uploads
```

### Storage Directory Structure (`public/uploads/...`)
Ensure the following directory tree exists prior to traffic cutover:
* `public/uploads/lease_applications/` (Lease documents, revenue records, FMB sketches)
* `public/uploads/mining/` (Mining plan draft plates, progressive mine closure plans)
* `public/uploads/environment/` (Form-1, Form-2, baseline EMP, EIA dockets)
* `public/uploads/ec_certificates/` (Issued SEIAA environmental clearance certificates)
* `public/uploads/ppt/` (DEAC presentation decks, PowerPoint summaries)
* `public/uploads/dgps/` (DGPS CAD drawings, boundary pillar tables, KML files)
* `public/uploads/drone/` (Orthomosaics, flight logs, 3D volumetric reports)
* `public/uploads/compliance/` (Half-yearly compliance reports, NABL test results)
* `public/uploads/users/` (User avatars)

---

## 3. Web Server Configurations

### 3.1 Hardened Nginx Configuration
Create `/etc/nginx/sites-available/gtms.conf`:

```nginx
server {
    listen 80;
    server_name gtms.example.gov.in;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name gtms.example.gov.in;

    root /var/www/gtms/public;
    index index.php index.html;

    # SSL Certificate Configuration
    ssl_certificate /etc/ssl/certs/gtms.crt;
    ssl_certificate_key /etc/ssl/private/gtms.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Maximum file upload size (Critical for 50MB CAD and Drone Orthomosaics)
    client_max_body_size 100M;
    client_body_timeout 120s;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    charset utf-8;

    # Front Controller Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    # PHP-FPM Execution
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 180s;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2|dwg|dxf|kml)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    access_log /var/log/nginx/gtms_access.log;
    error_log /var/log/nginx/gtms_error.log error;
}
```

Enable configuration and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/gtms.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### 3.2 Apache Configuration
If deploying on Apache 2.4, enable the required modules:
```bash
sudo a2enmod rewrite headers ssl
```

Create `/etc/apache2/sites-available/gtms.conf`:

```apache
<VirtualHost *:80>
    ServerName gtms.example.gov.in
    Redirect permanent / https://gtms.example.gov.in/
</VirtualHost>

<VirtualHost *:443>
    ServerName gtms.example.gov.in
    DocumentRoot /var/www/gtms/public

    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/gtms.crt
    SSLCertificateKeyFile /etc/ssl/private/gtms.key

    <Directory /var/www/gtms/public>
        Options -MultiViews -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Limit Request Body to 100MB
    LimitRequestBody 104857600

    # Security Headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"

    ErrorLog ${APACHE_LOG_DIR}/gtms_error.log
    CustomLog ${APACHE_LOG_DIR}/gtms_access.log combined
</VirtualHost>
```

#### Apache `.htaccess` Verification
The project's `public/.htaccess` already incorporates front-controller rewrite rules and header passes:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 4. Production Environment Configuration (`.env`)

The `.env` file must be created from `.env.example` and locked down. Never commit `.env` into version control.

### Categorized Production `.env` Blueprint

```dotenv
# ==============================================================================
# APPLICATION IDENTIFICATION & ENVIRONMENT
# ==============================================================================
APP_NAME="Granite / Mining Tracking Management System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://gtms.example.gov.in
APP_TIMEZONE="Asia/Kolkata"
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

# Cryptographic Application Key (Generated via: php artisan key:generate)
APP_KEY=[REDACTED_BASE64_32_BYTE_SECRET]

# Maintenance Mode Driver
APP_MAINTENANCE_DRIVER=file

# ==============================================================================
# DATABASE CONNECTION (MySQL 8.0+ / MariaDB 10.11+)
# ==============================================================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gtms_data
DB_USERNAME=gtms_db_user
DB_PASSWORD=[REDACTED_DATABASE_PASSWORD]

# ==============================================================================
# SESSION & CACHE CONFIGURATION
# ==============================================================================
# Production uses database driver for persistent clustering support
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true

CACHE_STORE=database
CACHE_PREFIX=gtms_cache

# ==============================================================================
# QUEUE & BACKGROUND PROCESSING
# ==============================================================================
QUEUE_CONNECTION=database

# ==============================================================================
# LOGGING CONFIGURATION
# ==============================================================================
LOG_CHANNEL=daily
LOG_LEVEL=warning
LOG_DAILY_DAYS=30

# ==============================================================================
# MAIL DRIVER (Notifications & Audit Escalations)
# ==============================================================================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=[REDACTED_SMTP_USERNAME]
MAIL_PASSWORD=[REDACTED_SMTP_PASSWORD]
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@gtms.example.gov.in"
MAIL_FROM_NAME="${APP_NAME}"

# ==============================================================================
# FILESYSTEM STORAGE
# ==============================================================================
FILESYSTEM_DISK=local
```

---

## 5. Build & Caching Optimization Pipeline

To achieve sub-second response times on dashboard and customer tracking lookups, execute the automated optimization pipeline during every deployment:

### Deployment Pipeline Step-by-Step

```bash
# Step 1: Navigate to application root
cd /var/www/gtms

# Step 2: Put application into maintenance mode (with secret bypass)
php artisan down --secret="gtms-deploy-bypass-token" --render="errors.503"

# Step 3: Fetch latest release
git pull origin main

# Step 4: Install PHP production dependencies
composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

# Step 5: Compile front-end assets with Vite
npm ci
npm run build

# Step 6: Execute pending database migrations safely
php artisan migrate --force

# Step 7: Clear old caches
php artisan optimize:clear

# Step 8: Build fresh production cache manifests
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Step 9: Re-link storage if necessary
php artisan storage:link

# Step 10: Restart queue worker processes
sudo supervisorctl restart gtms-worker:*

# Step 11: Bring application back online
php artisan up
```

### Why Each Caching Step Matters
* `config:cache`: Flattens all files in `config/*.php` into a single bootstrap array file (`bootstrap/cache/config.php`), eliminating dozens of disk read operations per request.
* `route:cache`: Serializes all 121 route definitions into a compiled match table (`bootstrap/cache/routes-v7.php`), drastically reducing HTTP request dispatch latency.
* `view:cache`: Pre-compiles all 66 Blade templates into native PHP scripts (`storage/framework/views/`), avoiding on-the-fly Blade parsing during user traffic.
* `event:cache`: Pre-discovers and caches framework events and listeners.

---

## 6. Background Queue Workers & Task Scheduling

### 6.1 Supervisor Worker Configuration
Heavy document processing (e.g. converting and verifying 50MB CAD DWG files) and bulk customer audits run asynchronously via Laravel's database queue.

Create `/etc/supervisor/conf.d/gtms-worker.conf`:

```ini
[program:gtms-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/gtms/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=180
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/var/log/supervisor/gtms_worker.log
stdout_logfile_maxbytes=20MB
stdout_logfile_backups=10
```

Load and start workers:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start gtms-worker:*
```

### 6.2 Linux Cron Schedule
Add Laravel's schedule runner to the web server user's crontab:

```bash
sudo crontab -u www-data -e
```

Add the following entry:
```crontab
* * * * * cd /var/www/gtms && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

---

## 7. Disaster Recovery & Backup Strategy

### 7.1 Automated Nightly Database Dump
Create `/usr/local/bin/gtms-db-backup.sh`:

```bash
#!/usr/bin/env bash
set -e

BACKUP_DIR="/var/backups/gtms/database"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="gtms_data"
DB_USER="gtms_db_user"
DB_PASS="[REDACTED]"

mkdir -p "$BACKUP_DIR"

# Perform compressed mysqldump
mysqldump --single-transaction --quick --routines --triggers \
    -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/gtms_${TIMESTAMP}.sql.gz"

# Retain only last 30 days of backups
find "$BACKUP_DIR" -type f -name "gtms_*.sql.gz" -mtime +30 -exec rm {} \;
```

### 7.2 Storage Directory Synchronisation
Synchronize the `public/uploads/` directory daily to an offsite secure storage bucket (AWS S3 or secondary government storage node) using `rclone` or `rsync`:

```bash
rsync -avz --delete /var/www/gtms/public/uploads/ backup-user@backup-host:/var/backups/gtms/uploads/
```

### 7.3 Disaster Restoration Procedure
To restore a backup into a fresh environment:
1. Recreate database: `mysql -u root -p -e "CREATE DATABASE gtms_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`
2. Restore schema and data: `gunzip < gtms_20260924.sql.gz | mysql -u gtms_db_user -p gtms_data`
3. Restore files: `rsync -avz backup-user@backup-host:/var/backups/gtms/uploads/ /var/www/gtms/public/uploads/`
4. Set permissions: `sudo chown -R www-data:www-data /var/www/gtms/public/uploads`
5. Run optimization pipeline: `php artisan optimize`
