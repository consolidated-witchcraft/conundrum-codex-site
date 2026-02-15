<?php

namespace App\Doctrine\Types\Exceptions;

class UnmappableObjectException extends \Exception
{
    private const string MESSAGE_PATTERN = "Attempting to map an object of type %s to field %s, which only accepts %s.";
    public function __construct(
        string $fieldType,
        string $expectedObject,
        mixed $actualValue,
        int $code = 0,
        ?\Throwable $previous = null
    ) {

        $valueString = is_object($actualValue) ? get_class($actualValue) : gettype($actualValue);
        $message = sprintf(self::MESSAGE_PATTERN, $valueString, $fieldType, $expectedObject);
        parent::__construct($message, $code, $previous);
    }
}
