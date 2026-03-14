<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldEvent;

class InvalidWorldEventTimestampException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event timestamp must be at least %d. %d given.';

    public function __construct(int $timestamp, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, WorldEvent::MINIMUM_TIMESTAMP, $timestamp),
            $code,
            $previous
        );
    }
}
