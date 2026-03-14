<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

use App\Services\World\Domain\WorldEvent;

class InvalidWorldEventDescriptionLengthException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event description must be between %d and %d characters. %d given.';

    public function __construct(int $descriptionLength, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                self::MESSAGE_PATTERN,
                WorldEvent::MINIMUM_TEXT_LENGTH,
                WorldEvent::MAXIMUM_DESCRIPTION_LENGTH,
                $descriptionLength
            ),
            $code,
            $previous
        );
    }
}
