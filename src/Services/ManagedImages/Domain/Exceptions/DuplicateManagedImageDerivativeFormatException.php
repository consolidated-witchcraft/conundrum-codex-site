<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain\Exceptions;

class DuplicateManagedImageDerivativeFormatException extends AbstractManagedImageDomainException
{
    private const string MESSAGE_PATTERN = 'A managed image derivative with format "%s" already exists for this image.';

    public function __construct(string $formatHandle, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf(self::MESSAGE_PATTERN, $formatHandle),
            $code,
            $previous
        );
    }
}
