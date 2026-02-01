<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions;

class DynamicFieldValueMismatchException extends \Exception
{
    private const string MESSAGE_PATTERN = "Attempting to set invalid value for fieldName %s. The only accepted value(s) for this field are %s. The value set is %s";

    public function __construct(
        string $fieldName,
        string $acceptedType,
        string $actualType,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $fieldName, $acceptedType, $actualType), $code, $previous);
    }
}
