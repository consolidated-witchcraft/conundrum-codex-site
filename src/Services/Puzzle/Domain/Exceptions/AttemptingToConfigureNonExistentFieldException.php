<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Exceptions;

class AttemptingToConfigureNonExistentFieldException extends \Exception
{

    private const string MESSAGE_PATTERN ="Attempting to populate field name form data %s that does not exist on the dynamic DTO";
    public function __construct(
        string $fieldName,
        $message = "",
        $code = 0,
        $previous = null
    ) {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $fieldName), $code, $previous);
    }
}
