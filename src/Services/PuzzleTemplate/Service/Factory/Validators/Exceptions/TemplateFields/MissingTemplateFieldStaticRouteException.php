<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\AbstractTemplateDefinitionException;

class MissingTemplateFieldStaticRouteException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The template '%s' is marked as static, but is missing the '%s' route.";

    public function __construct(string $moduleName, string $routeKey, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $moduleName, $routeKey);
        parent::__construct($message, $code, $previous);
    }
}
