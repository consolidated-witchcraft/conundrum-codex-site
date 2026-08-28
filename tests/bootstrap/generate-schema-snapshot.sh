#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
COMPOSE_FILE="${ROOT_DIR}/../conundrum-codex-environment/docker-compose.yml"
SCHEMA_FILE="${ROOT_DIR}/tests/bootstrap/schema.sql"

docker compose --file="${COMPOSE_FILE}" exec \
    -T dnd-puzzles-db \
    sh -lc 'pg_dump --schema-only --no-owner --no-privileges --username="$POSTGRES_USER" "$POSTGRES_DB"' \
    > "${SCHEMA_FILE}"

echo "Wrote schema snapshot to ${SCHEMA_FILE}"
