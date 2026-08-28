<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class InvalidWorldEventTitleLengthException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event title length is invalid. %d characters given.';

    public function __construct(int $titleLength, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $titleLength),
            $code,
            $previous
        );
    }
}
