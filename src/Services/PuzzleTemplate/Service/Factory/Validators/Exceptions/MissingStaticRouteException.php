<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions;

class MissingStaticRouteException extends AbstractTemplateDefinitionException
{
    private const string MESSAGE_PATTERN = "The template '%s' is marked as static, but has no static routes defined.";

    public function __construct(string $moduleName, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $moduleName);
        parent::__construct($message, $code, $previous);
    }
}
