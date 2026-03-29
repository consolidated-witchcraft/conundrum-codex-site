#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
COMPOSE_FILE="${ROOT_DIR}/../dnd-puzzles-environment/docker-compose.yml"
SCHEMA_FILE="${ROOT_DIR}/tests/bootstrap/schema.sql"

docker compose --file="${COMPOSE_FILE}" exec \
    -T dnd-puzzles-db \
    sh -lc 'mariadb-dump --no-defaults -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --no-data "$MYSQL_DATABASE"' \
    > "${SCHEMA_FILE}"

echo "Wrote schema snapshot to ${SCHEMA_FILE}"
