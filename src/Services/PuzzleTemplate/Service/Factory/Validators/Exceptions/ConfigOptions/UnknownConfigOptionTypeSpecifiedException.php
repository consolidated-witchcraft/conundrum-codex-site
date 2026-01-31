<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class UnknownConfigOptionTypeSpecifiedException extends AbstractTemplateDefinitionException
{
    private const string ERROR_MESSAGE_PATTERN = "An unknown subfield type '%s' was discovered in the config option named '%s' in module '%s'";

    public function __construct(string $unknownFieldType, string $configOptionName, string $moduleName, ?int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::ERROR_MESSAGE_PATTERN, $unknownFieldType, $configOptionName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
