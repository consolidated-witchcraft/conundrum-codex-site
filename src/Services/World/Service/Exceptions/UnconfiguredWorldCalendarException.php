<?php

declare(strict_types=1);

namespace App\Services\World\Service\Exceptions;

class UnconfiguredWorldCalendarException extends AbstractWorldServiceException
{
    private const string MESSAGE_PATTERN = 'World "%s" must have a positive hoursInDay, at least one month, and at least one day of week to convert timestamps.';

    public function __construct(string $worldSlug, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE_PATTERN, $worldSlug), $code, $previous);
    }
}
