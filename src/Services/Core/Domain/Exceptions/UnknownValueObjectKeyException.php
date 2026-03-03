<?php

namespace App\Services\Core\Domain\Exceptions;

class UnknownValueObjectKeyException extends \Exception
{
    private const string MESSAGE_PATTERN = "Attempted to build a value object of type '%s' with the unrecognised key '%s'";
    public function __construct(string $valueObjectType, string $key, int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $valueObjectType, $key);
        parent::__construct(
            $message, $code, $previous
        );
    }
}
