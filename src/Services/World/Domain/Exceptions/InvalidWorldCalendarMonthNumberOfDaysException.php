<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class InvalidWorldCalendarMonthNumberOfDaysException extends AbstractWorldDomainException
{
    public function __construct(int $numberOfDays, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('World calendar month numberOfDays must be between 1 and 50. %d given.', $numberOfDays),
            $code,
            $previous
        );
    }
}
