<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions;

class MissingRequiredFieldException extends AbstractTemplateDefinitionException
{
    private const string ERROR_MESSAGE_PATTERN = "The required field '%s' is missing from the json for the module named '%s.";

    public function __construct(string $missingFieldName, string $moduleName, ?int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::ERROR_MESSAGE_PATTERN, $missingFieldName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
