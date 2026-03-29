# Database Standards
- When attempting to alter schema you MUST use ./bin/console doctrine:migrations:diff to generate the initial migration.
- You MUST NOT attempt to create a raw migration yourself.
- You MAY alter a migration you have generated this way ONLY after asking for explicit permission and explaining the reason the raw migration cannot work alone.
  - When requesting permission, summarise your proposed changes. 
- When creating a new entity for a service, be sure to check that you've added that directory to config/packages/doctrine.yaml so that your new entities are detected by doctrine.
- E.G, for the Foo service's domain entities, you would need to add

```yaml
    Foo:
        type: attribute
        is_bundle: false
        dir: '%kernel.project_dir%/src/Services/Foo/Domain'
        prefix: 'App\Services\Foo\Domain'
        alias: Foo
    ```
