<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class DuplicateWorldCalendarDayOfWeekAbbreviationException extends AbstractWorldDomainException
{
    public function __construct(string $abbreviation, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('A world calendar day of week with abbreviation "%s" already exists for these calendar settings.', $abbreviation),
            $code,
            $previous
        );
    }
}
