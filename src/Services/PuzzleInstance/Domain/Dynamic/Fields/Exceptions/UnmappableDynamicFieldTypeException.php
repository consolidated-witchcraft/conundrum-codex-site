<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions;

class UnmappableDynamicFieldTypeException extends \Exception
{
    private const string MESSAGE_PATTERN = "Could not map unknown dynamic field type '%s'.";

    public function __construct(string $unknownDynamicFieldType, int $code = 0, ?\Throwable $previous = null) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $unknownDynamicFieldType), $code, $previous);
    }
}
