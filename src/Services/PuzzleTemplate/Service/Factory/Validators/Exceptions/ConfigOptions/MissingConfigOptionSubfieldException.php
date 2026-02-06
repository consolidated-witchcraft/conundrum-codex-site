<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class MissingConfigOptionSubfieldException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "Template '%s' is invalid. The required subfield '%s' is missing";
    public function __construct(
        string $subfieldName,
        string $moduleName,
        int $code = 0,
        ? \Throwable $previous = null
    ) {
        $message = sprintf(self::MESSAGE_PATTERN, $moduleName, $subfieldName);
        parent::__construct($message, $code, $previous);
    }
}
