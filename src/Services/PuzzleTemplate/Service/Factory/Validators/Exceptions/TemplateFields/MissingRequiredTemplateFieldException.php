<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class MissingRequiredTemplateFieldException extends AbstractTemplateDefinitionException
{
    public const string ERROR_MESSAGE_PATTERN = "The required field '%s' is missing from the json for template '%s'.";

    public function __construct(string $missingFieldName, string $moduleName, ?int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::ERROR_MESSAGE_PATTERN, $missingFieldName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
