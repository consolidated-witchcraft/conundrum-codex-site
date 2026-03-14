<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldCalendarSettings;

class TooFewWorldCalendarDaysOfWeekException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'A world calendar must contain at least %d days of week.';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, WorldCalendarSettings::MINIMUM_DAYS_PER_WEEK),
            $code,
            $previous
        );
    }
}
