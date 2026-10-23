#!/bin/bash
# ==============================================================================
# GTMS Synology NAS Rollback Utility
# Rolls back app and queue containers to a specified or previous known-good tag
# Usage: ./docker/nas-rollback.sh [target-image-tag]
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

# Determine rollback target
if [ $# -ge 1 ]; then
    ROLLBACK_TAG="$1"
elif [ -f ".previous_known_good_tag" ]; then
    ROLLBACK_TAG=$(cat .previous_known_good_tag | tr -d ' \n\r')
elif [ -f "known_good_image.txt" ]; then
    ROLLBACK_TAG=$(cat known_good_image.txt | tr -d ' \n\r')
else
    echo "ERROR: No target tag specified and no previous known-good image found!"
    echo "Usage: $0 <image-tag-or-commit-sha>"
    exit 1
fi

echo "===================================================================="
echo " GTMS Container Rollback"
echo " Rollback Target Tag: $ROLLBACK_TAG"
echo " NOTE: Rolling back container images does NOT automatically reverse"
echo "       database schema migrations that have already run."
echo "       If schema changes are incompatible, use nas-restore.sh to restore"
echo "       the pre-migration database backup."
echo "===================================================================="

export GTMS_IMAGE_TAG="$ROLLBACK_TAG"

echo "Pulling image tag $ROLLBACK_TAG..."
docker pull "ghcr.io/sriram6369791334/gtms:${ROLLBACK_TAG}"

echo "Recreating app and queue containers with tag $ROLLBACK_TAG..."
docker compose up -d --no-deps app queue

echo "Verifying application health..."
HEALTH_URL="http://127.0.0.1:${APP_PORT:-8000}/up"
for i in $(seq 1 15); do
    if curl -s -f "$HEALTH_URL" > /dev/null 2>&1; then
        echo "Health check PASSED on rollback (HTTP 200 OK)!"
        echo "$ROLLBACK_TAG" > known_good_image.txt
        echo "Rollback successfully completed!"
        exit 0
    fi
    sleep 2
done

echo "WARNING: Health check did not succeed within 30s after rollback."
docker compose logs --tail=30 app
exit 1
