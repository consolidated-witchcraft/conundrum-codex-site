<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class InvalidTemplateFieldDateTimeException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The field '%s' defined in template '%s' is not a valid date time string.";
    public function __construct(string $fieldName, string $moduleName, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $fieldName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
