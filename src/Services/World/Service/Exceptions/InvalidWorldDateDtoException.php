<?php

declare(strict_types=1);

namespace App\Services\World\Service\Exceptions;

class InvalidWorldDateDtoException extends AbstractWorldServiceException
{
    private const string MESSAGE_PATTERN = 'WorldDateDto is invalid for world "%s": %s';

    public function __construct(string $worldSlug, string $reason, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $worldSlug, $reason), $code, $previous);
    }
}
