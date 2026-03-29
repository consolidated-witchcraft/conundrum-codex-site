#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="${ROOT_DIR}/../dnd-puzzles-environment/docker-compose.yml"

bash "${ROOT_DIR}/tests/bootstrap/generate-schema-snapshot.sh"
bash "${ROOT_DIR}/tests/bootstrap/reset-test-db.sh"

set +e
docker compose --file="${COMPOSE_FILE}" exec \
    -e APP_ENV=test \
    dnd-puzzles-app \
    ./vendor/bin/pest --testsuite=ApplicationLayerApi
TEST_EXIT_CODE=$?
set -e

if [[ ${TEST_EXIT_CODE} -eq 0 ]]; then
    echo "API tests passed."
else
    echo "API tests failed with exit code ${TEST_EXIT_CODE}."
fi

exit "${TEST_EXIT_CODE}"
