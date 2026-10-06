#!/bin/sh
set -e

# ==============================================================================
# GTMS Synology NAS Entrypoint Script
# Synchronizes PUID/PGID with host DSM permissions and initializes storage
# ==============================================================================

# Align www-data user to Synology NAS host PUID/PGID (defaults to 1026:100)
PUID=${PUID:-1026}
PGID=${PGID:-100}

CURRENT_UID=$(id -u www-data 2>/dev/null || echo "82")
CURRENT_GID=$(id -g www-data 2>/dev/null || echo "82")

if [ "$PUID" != "$CURRENT_UID" ] || [ "$PGID" != "$CURRENT_GID" ]; then
    echo "Aligning www-data identity: UID $CURRENT_UID -> $PUID, GID $CURRENT_GID -> $PGID for Synology DSM..."
    
    # Update group GID
    if command -v groupmod >/dev/null 2>&1; then
        groupmod -g "$PGID" www-data || true
    else
        if getent group "$PGID" >/dev/null 2>&1; then
            EXISTING_GROUP=$(getent group "$PGID" | cut -d: -f1)
            addgroup www-data "$EXISTING_GROUP" 2>/dev/null || true
        else
            sed -i -e "s/^www-data:x:[0-9]*/www-data:x:$PGID/" /etc/group 2>/dev/null || true
        fi
    fi

    # Update user UID
    if command -v usermod >/dev/null 2>&1; then
        usermod -u "$PUID" -g "$PGID" www-data || true
    else
        sed -i -e "s/^www-data:x:[0-9]*:[0-9]*/www-data:x:$PUID:$PGID/" /etc/passwd 2>/dev/null || true
    fi
    echo "Identity aligned successfully: $(id www-data 2>/dev/null || echo "UID $PUID, GID $PGID")"
fi

# Load custom php.ini if target directory exists
if [ -f /var/www/html/docker/php.ini ] && [ -d /usr/local/etc/php/conf.d ]; then
    cp /var/www/html/docker/php.ini /usr/local/etc/php/conf.d/99-gtms.ini
fi

# Ensure storage, public/uploads, chunks, and temp directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/storage/app/chunks \
         /var/www/html/public/uploads \
         /var/lib/nginx/tmp/client_body \
         /tmp/php_uploads \
         /var/www/html/bootstrap/cache

# Apply ownership to runtime and staging folders
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/public/uploads \
    /var/www/html/bootstrap/cache \
    /var/lib/nginx/tmp \
    /tmp/php_uploads 2>/dev/null || true

# Apply 775 permissions on storage, public/uploads, and chunks directories
chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/public/uploads \
    /var/www/html/storage/app/chunks \
    /var/www/html/bootstrap/cache \
    /var/lib/nginx/tmp \
    /tmp/php_uploads 2>/dev/null || true

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
    echo "Warning: APP_KEY is empty. Generating key..."
    php artisan key:generate --force
fi

# Create storage symlink if it doesn't exist
php artisan storage:link || true

# Wait for DB connection if DB_HOST is set
if [ "$DB_CONNECTION" = "mysql" ] && [ -n "$DB_HOST" ]; then
    echo "Checking MySQL connection on $DB_HOST:$DB_PORT..."
    max_tries=30
    count=0
    until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Exception \$e) { exit(1); }" > /dev/null 2>&1; do
        count=$((count + 1))
        if [ $count -gt $max_tries ]; then
            echo "MySQL did not become ready in time, continuing startup..."
            break
        fi
        echo "Waiting for database ($count/$max_tries)..."
        sleep 2
    done
    echo "Database check completed."
fi

# Execute main process passed to entrypoint (supervisord or artisan queue)
exec "$@"
