<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class DuplicateWorldCalendarDayOfWeekAbbreviationException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'A world calendar day of week with abbreviation "%s" already exists for these calendar settings.';

    public function __construct(string $abbreviation, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $abbreviation),
            $code,
            $previous
        );
    }
}
