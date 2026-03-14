<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class OverlappingWorldCalendarMonthDeltaException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'A world calendar month with delta %d already exists for these calendar settings.';

    public function __construct(int $delta, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $delta),
            $code,
            $previous
        );
    }
}
