#!/bin/sh
set -e

# Set permissions for storage and bootstrap cache
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

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

# Execute main process passed to entrypoint (supervisord)
exec "$@"
