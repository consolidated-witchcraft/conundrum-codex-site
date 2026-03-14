#! /bin/bash

echo "Running test suite"
echo "Clearing Cache"
docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app bin/console cache:clear --env="test"
echo "Exporting Schema"
docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app ./vendor/bin/pest
