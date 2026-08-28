<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class InvalidWorldEventTimestampException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event timestamp is invalid. %d given.';

    public function __construct(int $timestamp, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $timestamp),
            $code,
            $previous
        );
    }
}
