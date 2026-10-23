#!/bin/bash
# ==============================================================================
# GTMS Production Synology NAS Deployment Script
# Architecture: Outbound GHCR Pull -> Safety Backup -> Update -> Health Check -> Migration
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$SCRIPT_DIR"

echo "===================================================================="
echo " GTMS Production Deployment Script (Synology NAS Edition)"
echo " Started at: $(date '+%Y-%m-%d %H:%M:%S')"
echo "===================================================================="

# 1. Verify environment configuration file
if [ ! -f ".env" ]; then
    echo "ERROR: Production .env file not found in $SCRIPT_DIR!"
    echo "Please copy .env.docker.example to .env and configure all required secrets."
    exit 1
fi

# Load environment variables safely
# shellcheck disable=SC1091
set -a
source .env
set +a

# 2. Determine target image tag (CLI argument takes precedence over .env)
TARGET_TAG="${1:-${GTMS_IMAGE_TAG:-main}}"
IMAGE_NAME="ghcr.io/sriram6369791334/gtms"
TARGET_IMAGE="${IMAGE_NAME}:${TARGET_TAG}"
DATA_ROOT="${GTMS_DATA_DIR:-/volume1/docker/gtms-data}"

echo "Target Deployment Image : $TARGET_IMAGE"
echo "Persistent Data Root    : $DATA_ROOT"
echo "Application Port        : ${APP_PORT:-8000}"

# 3. Verify NAS persistent data directories
echo "Verifying NAS persistent volume directories..."
for dir in mysql redis storage uploads chunks nginx-temp php-temp backups; do
    target_path="${DATA_ROOT}/${dir}"
    if [ ! -d "$target_path" ]; then
        echo "Creating directory: $target_path"
        mkdir -p "$target_path"
    fi
done

# Ensure backup directory has write permissions
chmod 770 "${DATA_ROOT}/backups" 2>/dev/null || true

# 4. Optional GHCR Authentication (if token provided in .env)
if [ -n "${GHCR_TOKEN:-}" ]; then
    echo "Authenticating to ghcr.io with provided read-only token..."
    echo "$GHCR_TOKEN" | docker login ghcr.io -u "${GHCR_USERNAME:-sriram6369791334}" --password-stdin
fi

# 5. Pull the published target image from GHCR
echo "Pulling latest image: $TARGET_IMAGE..."
docker pull "$TARGET_IMAGE"

# 6. Capture current running image tag for safe rollback
CURRENT_TAG="none"
if docker ps --format '{{.Image}}' | grep -q "$IMAGE_NAME"; then
    CURRENT_TAG=$(docker ps --format '{{.Image}}' | grep "$IMAGE_NAME" | head -n 1 | awk -F: '{print $2}')
    echo "Currently active image tag: $CURRENT_TAG"
    echo "$CURRENT_TAG" > .previous_known_good_tag
fi

# 7. Pre-Migration Database Safety Backup
# Check if db container is running
if docker compose ps --services --filter "status=running" | grep -q "^db$"; then
    echo "Database container is running. Initiating pre-migration safety backup..."
    TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
    BACKUP_FILE="${DATA_ROOT}/backups/gtms_pre_deploy_${TIMESTAMP}.sql"
    
    # Run mysqldump inside container safely
    docker compose exec -T db mysqldump \
        -u root \
        -p"${DOCKER_DB_ROOT_PASSWORD}" \
        --single-transaction \
        --quick \
        "${DB_DATABASE:-gtms_data}" > "$BACKUP_FILE" 2>/dev/null || true

    # CRITICAL: Verify backup exists and is non-empty
    if [ -s "$BACKUP_FILE" ]; then
        BACKUP_SIZE=$(wc -c < "$BACKUP_FILE" | tr -d ' ')
        echo "Safety backup verified successfully: $BACKUP_FILE ($BACKUP_SIZE bytes)"
    else
        echo "FATAL: Pre-migration database backup failed or is 0 bytes!"
        echo "Aborting deployment to protect database integrity."
        exit 1
    fi
else
    echo "Database container not yet running (First deployment). Skipping pre-migration dump."
fi

# 8. Start Infrastructure Services (MySQL & Redis)
echo "Ensuring infrastructure services (db, redis) are running..."
docker compose up -d db redis

# Wait for database container to report healthy status
echo "Waiting for MySQL database engine to become healthy..."
MAX_WAIT=60
WAITED=0
until [ "$(docker inspect -f '{{.State.Health.Status}}' gtms_db 2>/dev/null || echo 'starting')" = "healthy" ]; do
    sleep 3
    WAITED=$((WAITED + 3))
    if [ $WAITED -ge $MAX_WAIT ]; then
        echo "ERROR: MySQL container failed to report healthy status within ${MAX_WAIT}s."
        docker compose logs --tail=30 db
        exit 1
    fi
    echo "Waiting for MySQL health check... (${WAITED}s/${MAX_WAIT}s)"
done
echo "MySQL is healthy and accepting connections."

# 9. Update Application and Queue Containers
echo "Updating app and queue services to $TARGET_IMAGE..."
export GTMS_IMAGE_TAG="$TARGET_TAG"
docker compose up -d --no-deps app queue

# 10. Post-Deployment Application Health Check
HEALTH_URL="http://127.0.0.1:${APP_PORT:-8000}/up"
echo "Verifying application health check at $HEALTH_URL..."
HEALTH_RETRIES=20
HEALTH_OK=false

for i in $(seq 1 $HEALTH_RETRIES); do
    if curl -s -f -m 5 "$HEALTH_URL" > /dev/null 2>&1; then
        echo "Health check PASSED on attempt $i (HTTP 200 OK)!"
        HEALTH_OK=true
        break
    fi
    echo "Health check waiting... attempt $i/$HEALTH_RETRIES"
    sleep 3
done

if [ "$HEALTH_OK" != "true" ]; then
    echo "FATAL: Application health check failed at $HEALTH_URL after deployment!"
    echo "--- Application Container Logs ---"
    docker compose logs --tail=50 app
    
    if [ "$CURRENT_TAG" != "none" ] && [ "$CURRENT_TAG" != "$TARGET_TAG" ]; then
        echo "Initiating automatic rollback to known-good image: $CURRENT_TAG..."
        export GTMS_IMAGE_TAG="$CURRENT_TAG"
        docker compose up -d --no-deps app queue
        echo "Rollback to $CURRENT_TAG completed."
    fi
    exit 1
fi

# 11. Run Database Migrations in Controlled Fashion
echo "Running database migrations..."
docker compose exec -T app php artisan migrate --force

# 12. Clear and Optimize Laravel Caches
echo "Optimizing application performance caches..."
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan optimize

# 13. Record Deployment State
echo "$TARGET_TAG" > known_good_image.txt
echo "===================================================================="
echo " GTMS Deployment COMPLETED SUCCESSFULLY!"
echo " Active Image Tag : $TARGET_TAG"
echo " Access URL       : ${APP_URL:-http://localhost:8000}"
echo " Finished at      : $(date '+%Y-%m-%d %H:%M:%S')"
echo "===================================================================="
