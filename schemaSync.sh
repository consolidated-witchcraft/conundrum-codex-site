#! /bin/bash

for i in "$@"; do
  case $i in
    -e=*|--mode=*)
      MODE="${i#*=}"
      shift # past argument=value
      ;;
    -*|--*)
      echo "Unknown option $i"
      exit 1
      ;;
    *)
      ;;
  esac
done

if [[ "$MODE" == "prod" ]]
then
    echo "Running in production"
echo "Clearing Cache"
  ./bin/console cache:clear
  echo "Exporting Schema"
  ./bin/console api:openapi:export --yaml --output=swagger_docs.yaml
else
  echo "Running in development"
  echo "Clearing Cache"
  docker compose --file=../conundrum-codex-environment/docker-compose.yml exec dnd-puzzles-app bin/console cache:clear
  echo "Exporting Schema"
  docker compose --file=../conundrum-codex-environment/docker-compose.yml exec dnd-puzzles-app bin/console api:openapi:export --yaml --output=swagger_docs.yaml
fi

echo "Syncing Schema"
npx openapi-typescript ./swagger_docs.yaml -o ./resources/typescript/schema/schema.d.ts
