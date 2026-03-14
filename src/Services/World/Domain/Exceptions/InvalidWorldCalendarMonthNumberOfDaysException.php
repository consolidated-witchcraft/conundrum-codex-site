<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldCalendarMonth;

class InvalidWorldCalendarMonthNumberOfDaysException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World calendar month numberOfDays must be between %d and %d. %d given.';

    public function __construct(int $numberOfDays, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::MESSAGE_PATTERN,
                WorldCalendarMonth::MINIMUM_NUMBER_OF_DAYS,
                WorldCalendarMonth::MAXIMUM_NUMBER_OF_DAYS,
                $numberOfDays
            ),
            $code,
            $previous
        );
    }
}
