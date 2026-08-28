<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Exceptions;

class InvalidWorldEventDescriptionLengthException extends AbstractWorldDomainException
{
    private const string MESSAGE_PATTERN = 'World event description length is invalid. %d characters given.';

    public function __construct(int $descriptionLength, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $descriptionLength),
            $code,
            $previous
        );
    }
}
