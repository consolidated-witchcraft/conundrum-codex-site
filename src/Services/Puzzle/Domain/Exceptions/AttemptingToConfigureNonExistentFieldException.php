<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Exceptions;

class AttemptingToConfigureNonExistentFieldException extends \Exception
{

    private const string MESSAGE_PATTERN ="Attempting to populate field name form data %s that does not exist on the dynamic DTO. Error %s";
    public function __construct(
        string $fieldName,
        string $message = "",
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $fieldName, $message), $code, $previous);
    }
}
