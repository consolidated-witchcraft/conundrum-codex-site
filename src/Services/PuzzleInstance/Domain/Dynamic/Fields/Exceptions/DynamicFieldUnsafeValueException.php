<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions;

class DynamicFieldUnsafeValueException extends \Exception
{
    private const string MESSAGE_PATTERN = "Attempting to set invalid value for fieldName %s. The field may only contain %s. URLs are also forbidden";

    public function __construct(
        string $fieldName,
        string $acceptedPattern,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $fieldName, $acceptedPattern), $code, $previous);
    }
}
