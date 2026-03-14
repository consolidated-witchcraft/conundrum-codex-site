<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class TooFewWorldCalendarDaysOfWeekException extends AbstractWorldDomainException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('A world calendar must contain at least 3 days of week.', $code, $previous);
    }
}
