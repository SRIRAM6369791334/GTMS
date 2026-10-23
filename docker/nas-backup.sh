#!/bin/bash
# ==============================================================================
# GTMS Synology NAS Database Backup Utility
# Creates a verified, compressed SQL dump of the production gtms_data database
# ==============================================================================

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$SCRIPT_DIR"

if [ ! -f ".env" ]; then
    echo "ERROR: .env file not found in $SCRIPT_DIR!"
    exit 1
fi

# shellcheck disable=SC1091
set -a
source .env
set +a

DATA_ROOT="${GTMS_DATA_DIR:-/volume1/docker/gtms-data}"
BACKUP_DIR="${DATA_ROOT}/backups"
mkdir -p "$BACKUP_DIR"

TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
BACKUP_FILE="${BACKUP_DIR}/gtms_backup_${TIMESTAMP}.sql"

echo "Starting database backup for ${DB_DATABASE:-gtms_data}..."
docker compose exec -T db mysqldump \
    -u root \
    -p"${DOCKER_DB_ROOT_PASSWORD}" \
    --single-transaction \
    --quick \
    "${DB_DATABASE:-gtms_data}" > "$BACKUP_FILE"

if [ -s "$BACKUP_FILE" ]; then
    SIZE=$(wc -c < "$BACKUP_FILE" | tr -d ' ')
    echo "Backup SUCCESS: $BACKUP_FILE created (${SIZE} bytes)."
else
    echo "Backup FAILED: Backup file is empty or was not created!"
    rm -f "$BACKUP_FILE"
    exit 1
fi
