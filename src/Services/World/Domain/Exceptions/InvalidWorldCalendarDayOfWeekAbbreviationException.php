<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class InvalidWorldCalendarDayOfWeekAbbreviationException extends AbstractWorldDomainException
{
    public function __construct(string $abbreviation, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('World calendar day of week abbreviation must be between 1 and 3 characters. "%s" given.', $abbreviation),
            $code,
            $previous
        );
    }
}
