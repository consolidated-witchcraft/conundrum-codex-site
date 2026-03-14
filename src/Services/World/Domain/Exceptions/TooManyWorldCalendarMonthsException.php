<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class TooManyWorldCalendarMonthsException extends AbstractWorldDomainException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('A world calendar cannot contain more than 50 months.', $code, $previous);
    }
}
