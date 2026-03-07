<?php

declare(strict_types=1);

namespace App\Doctrine\Types\Exceptions;

class InvalidDoctrineCustomTypeValueException extends \Exception
{
    private const string MESSAGE_PATTERN = "Could not %s value '%s'";

    public function construct(
        string $operation,
        string $value,
        int $code = 0,
        ?\Throwable $previous = null
    ): void
    {
        $message = sprintf(self::MESSAGE_PATTERN, $operation, $value);
        parent::__construct($message, $code, $previous);
    }
}
