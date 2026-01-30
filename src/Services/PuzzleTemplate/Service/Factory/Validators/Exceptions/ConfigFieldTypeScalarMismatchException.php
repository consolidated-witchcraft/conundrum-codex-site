<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions;

class ConfigFieldTypeScalarMismatchException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The field type '%s' defined in template '%s' is invalid. Its expected type is %s, its actual type is %s";
    public function __construct(string $fieldName, string $expectedType, string $actualType, string $moduleName, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $fieldName, $moduleName, $expectedType, $actualType);
        parent::__construct($message, $code, $previous);
    }
}
