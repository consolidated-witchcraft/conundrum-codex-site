#! /bin/bash

echo "Running in production"

echo "Pulling git"
git pull

echo "Rebuilding Authoritative container"
composer dump-autoload --no-dev --classmap-authoritative

echo "Clearing Cache"
./bin/console cache:clear

echo "Exporting Schema"
./bin/console api:openapi:export --yaml --output=swagger_docs.yaml

echo "Syncing Schema"
npx openapi-typescript ./swagger_docs.yaml -o ./resources/typescript/schema/schema.d.ts

echo "Running Build"
npm run build

echo "All done. Now restart PHP"
