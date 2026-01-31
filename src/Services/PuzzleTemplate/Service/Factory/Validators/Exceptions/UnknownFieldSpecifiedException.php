<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions;

class UnknownFieldSpecifiedException extends AbstractTemplateDefinitionException
{
    private const string ERROR_MESSAGE_PATTERN = "An unknown field named '%s' was discovered in the template.json for module '%s'";

    public function __construct(string $unknownFieldName, string $moduleName, ?int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::ERROR_MESSAGE_PATTERN, $unknownFieldName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
