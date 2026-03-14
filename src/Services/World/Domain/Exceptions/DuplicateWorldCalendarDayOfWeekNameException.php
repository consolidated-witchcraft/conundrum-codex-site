<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class DuplicateWorldCalendarDayOfWeekNameException extends AbstractWorldDomainException
{
    public function __construct(string $name, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('A world calendar day of week with name "%s" already exists for these calendar settings.', $name),
            $code,
            $previous
        );
    }
}
