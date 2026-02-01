<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Service\Exceptions;

class AttemptingToGenerateDtoForStaticPuzzleException extends \Exception
{
    private const string MESSAGE_PATTERN = "Attempting to create a dynamic DTO for static template %s. Static Templates must manage their own data.";
    public function __construct(string $templateName, int $code = 0, ?\Throwable $previous = null) {
       $message = sprintf(self::MESSAGE_PATTERN, $templateName);
       parent::__construct($message, $code, $previous);
    }
}
