<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class OverlappingWorldCalendarMonthDeltaException extends AbstractWorldDomainException
{
    public function __construct(int $delta, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('A world calendar month with delta %d already exists for these calendar settings.', $delta),
            $code,
            $previous
        );
    }
}
