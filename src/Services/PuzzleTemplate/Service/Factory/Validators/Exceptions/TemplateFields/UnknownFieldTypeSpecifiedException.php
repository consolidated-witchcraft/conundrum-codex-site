<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class UnknownFieldTypeSpecifiedException extends AbstractTemplateDefinitionException
{
    private const string ERROR_MESSAGE_PATTERN = "The An unknown field type '%s' was discovered in the template.json for module '%s'";

    public function __construct(string $unknownFieldType, string $moduleName, int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::ERROR_MESSAGE_PATTERN, $unknownFieldType, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
