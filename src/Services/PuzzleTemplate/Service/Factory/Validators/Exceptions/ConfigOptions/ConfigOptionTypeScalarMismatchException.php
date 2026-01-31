<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class ConfigOptionTypeScalarMismatchException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The field '%s' defined in module '%s' is invalid. Its expected type is %s. Its actual type is %s.";
    public function __construct(string $fieldName, string $expectedType, string $actualType, string $moduleName, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $fieldName, $moduleName, $expectedType, $actualType);
        parent::__construct($message, $code, $previous);
    }
}
