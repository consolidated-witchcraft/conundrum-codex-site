#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
COMPOSE_FILE="${ROOT_DIR}/../dnd-puzzles-environment/docker-compose.yml"
SCHEMA_FILE="${ROOT_DIR}/tests/bootstrap/schema.sql"

if [[ ! -f "${SCHEMA_FILE}" ]]; then
    echo "Missing schema snapshot at ${SCHEMA_FILE}" >&2
    exit 1
fi

TEST_DB_SUFFIX="${TEST_TOKEN:-}"
TEST_DB_NAME="dnd-puzzles_test${TEST_DB_SUFFIX}"

docker compose --file="${COMPOSE_FILE}" exec \
    dnd-puzzles-db \
    sh -lc 'mariadb --no-defaults -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "DROP DATABASE IF EXISTS \`'"${TEST_DB_NAME}"'\`; CREATE DATABASE \`'"${TEST_DB_NAME}"'\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"'

docker compose --file="${COMPOSE_FILE}" exec \
    -T dnd-puzzles-db \
    sh -lc 'mariadb --no-defaults -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "'"${TEST_DB_NAME}"'"' < "${SCHEMA_FILE}"

echo "Reset test database ${TEST_DB_NAME} from ${SCHEMA_FILE}"
