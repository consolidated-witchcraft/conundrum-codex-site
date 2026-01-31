<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class InvalidTemplateFieldRouteException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The route '%s' specified in '%s' could not be found in the application router";

    public function __construct(mixed $routeName, string $moduleName, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $routeName, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
