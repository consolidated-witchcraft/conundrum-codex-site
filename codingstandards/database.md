# Database Standards
- When attempting to alter schema you MUST use ./bin/console doctrine:migrations:diff to generate the initial migration.
- You MUST NOT attempt to create a raw migration yourself.
- You MAY alter a migration you have generated this way ONLY after asking for explicit permission and explaining the reason the raw migration cannot work alone.
