## Exception Handling
* All exceptions SHOULD be caught
* Exceptions MUST be specific; don't catch or throw generic exceptions
* Exceptions within a service directory MUST extend a base exception within that service
* You MUST NOT catch generic exceptions with catch (Exception $e) or similar constructions.
* If an exception has a message, that message's text SHOULD be set via a private const string called MESSAGE_PATTERN. The message itself is constructed using this pattern and sprintf for any necessary substitutions. For example:
```php
    class FooException extends \AbstractException {

        private const string MESSAGE_PATTERN = "Attempted to provide a value of type '%s' to Foo. Foo only accepts Bar";

        public function __construct(string $valueType, int $code = 0, ?\Throwable $previous = null) {

            parent::__construct(sprintf(self::MESSAGE_PATTERN, $valueType), $code, $previous);
        }
    }
```
* If a service throws an exception, these are where its various exceptions MUST be located
  * src/Services/{service name}/Service/Exceptions for exceptions thrown in the Service layer.
  * src/Services{service name}/Domain/Exceptions for exceptions thrown in the Domain layer
  * src/Services{service name}/Infrastructure/Exceptions for exceptions thrown in the Infrastructure layer
* Application-level exceptions SHOULD be located next to the class that generates the responses. For example:
  * FooController throws FooException. This SHOULD be located in {fooController's path}/Exceptions
  * The Banana ApiResource throws UnripeBananaException. This SHOULD be located in {banana resource's *provider*'s path}/Exception
