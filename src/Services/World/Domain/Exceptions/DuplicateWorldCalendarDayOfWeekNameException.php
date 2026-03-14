<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class DuplicateWorldCalendarDayOfWeekNameException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'A world calendar day of week with name "%s" already exists for these calendar settings.';

    public function __construct(string $name, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $name),
            $code,
            $previous
        );
    }
}
