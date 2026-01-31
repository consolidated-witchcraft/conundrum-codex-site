<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class ConfigOptionTypeInvalidPatternException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The field definition '%s' in template '%s' is invalid: %s";
    public function __construct(string $fieldName, string $moduleName, string $infoMessage, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $fieldName, $moduleName, $infoMessage);
        parent::__construct($message, $code, $previous);
    }
}
