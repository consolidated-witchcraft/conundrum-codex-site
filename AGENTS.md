# Coding Standards
## General
- Use PHP 8.4+ features
- Follow PSR-12 coding standards
- Write tests for all new features
- Use Symfony best practices
- Always use strict_types
- Obey all rules in ./codingstandards/
- Within coding standards, the words MUST, MUST NOT, SHOULD, SHOULD NOT and MAY are to be interpreted as described in RFC 2119.

## Expertise
- Symfony
- PHP 8.4+

## Container Usage
- All project commands MUST be run inside the Docker application container unless explicitly instructed otherwise.
- Do not run project PHP, Composer, Symfony, or Pest commands directly on the host machine.
- Use the `dnd-puzzles-app` service from `../dnd-puzzles-environment/docker-compose.yml`.

### Testing
- All tests MUST be run inside the Docker container.
- The default full test command is:
  `docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app ./vendor/bin/pest`
- Targeted test runs MUST use the same container pattern, for example:
  `docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app ./vendor/bin/pest tests/Unit/Services/World`
- If a test command is needed, prefer Pest directly over wrappers unless explicitly instructed otherwise.

### PHP And Symfony Commands
- Run PHP commands inside the Docker container, for example:
  `docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app php bin/console`
- Run Composer commands inside the Docker container, for example:
  `docker compose --file=../dnd-puzzles-environment/docker-compose.yml exec dnd-puzzles-app composer install`

### Verification
- After making code changes, run the smallest relevant test set first inside the container.
- If broader verification is needed, run the full Pest suite inside the container.
- Any test result obtained outside the container SHOULD be treated as non-authoritative.
