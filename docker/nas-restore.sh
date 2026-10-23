#!/bin/bash
# ==============================================================================
# GTMS Synology NAS Database Restore Utility
# Restores a specified SQL backup file into the production gtms_data database
# Usage: ./docker/nas-restore.sh <path-to-backup.sql>
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$SCRIPT_DIR"

if [ $# -lt 1 ]; then
    echo "Usage: $0 <path-to-sql-backup-file>"
    echo "Available backups:"
    ls -lh "${GTMS_DATA_DIR:-/volume1/docker/gtms-data}/backups/"*.sql 2>/dev/null || echo "No backups found."
    exit 1
fi

BACKUP_FILE="$1"

if [ ! -f "$BACKUP_FILE" ]; then
    echo "ERROR: Backup file $BACKUP_FILE not found!"
    exit 1
fi

if [ ! -f ".env" ]; then
    echo "ERROR: .env file not found in $SCRIPT_DIR!"
    exit 1
fi

# shellcheck disable=SC1091
set -a
source .env
set +a

echo "===================================================================="
echo " WARNING: RESTORING DATABASE WILL OVERWRITE CURRENT DATA IN ${DB_DATABASE:-gtms_data}!"
echo " Target File: $BACKUP_FILE"
echo "===================================================================="

read -p "Type 'RESTORE' to confirm: " -r CONFIRM
if [ "$CONFIRM" != "RESTORE" ]; then
    echo "Restore cancelled by user."
    exit 0
fi

echo "Putting application in maintenance mode..."
docker compose exec -T app php artisan down || true

echo "Restoring database from $BACKUP_FILE..."
docker compose exec -T db mysql \
    -u root \
    -p"${DOCKER_DB_ROOT_PASSWORD}" \
    "${DB_DATABASE:-gtms_data}" < "$BACKUP_FILE"

echo "Clearing application cache..."
docker compose exec -T app php artisan optimize:clear

echo "Bringing application back online..."
docker compose exec -T app php artisan up

echo "Database restoration completed successfully!"
