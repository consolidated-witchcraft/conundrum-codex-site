<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldCalendarDayOfWeek;

class InvalidWorldCalendarDayOfWeekAbbreviationException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World calendar day of week abbreviation must be between %d and %d characters. "%s" given.';

    public function __construct(string $abbreviation, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::MESSAGE_PATTERN,
                WorldCalendarDayOfWeek::MINIMUM_ABBREVIATION_LENGTH,
                WorldCalendarDayOfWeek::MAXIMUM_ABBREVIATION_LENGTH,
                $abbreviation
            ),
            $code,
            $previous
        );
    }
}
