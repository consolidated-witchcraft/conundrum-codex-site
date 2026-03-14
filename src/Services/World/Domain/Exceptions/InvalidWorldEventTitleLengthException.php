<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldEvent;

class InvalidWorldEventTitleLengthException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event title must be between %d and %d characters. %d given.';

    public function __construct(int $titleLength, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::MESSAGE_PATTERN,
                WorldEvent::MINIMUM_TEXT_LENGTH,
                WorldEvent::MAXIMUM_TITLE_LENGTH,
                $titleLength
            ),
            $code,
            $previous
        );
    }
}
