#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
COMPOSE_FILE="${ROOT_DIR}/../conundrum-codex-environment/docker-compose.yml"
SCHEMA_FILE="${ROOT_DIR}/tests/bootstrap/schema.sql"

if [[ ! -f "${SCHEMA_FILE}" ]]; then
    echo "Missing schema snapshot at ${SCHEMA_FILE}" >&2
    exit 1
fi

TEST_DB_SUFFIX="${TEST_TOKEN:-}"
TEST_DB_NAME="dnd_puzzles_test${TEST_DB_SUFFIX}"

docker compose --file="${COMPOSE_FILE}" exec \
    dnd-puzzles-db \
    sh -lc 'dropdb --if-exists --force --username="$POSTGRES_USER" '"${TEST_DB_NAME}"' && createdb --username="$POSTGRES_USER" '"${TEST_DB_NAME}"''

docker compose --file="${COMPOSE_FILE}" exec \
    -T dnd-puzzles-db \
    sh -lc 'psql --set ON_ERROR_STOP=1 --username="$POSTGRES_USER" --dbname="'"${TEST_DB_NAME}"'"' < "${SCHEMA_FILE}"

echo "Reset test database ${TEST_DB_NAME} from ${SCHEMA_FILE}"
