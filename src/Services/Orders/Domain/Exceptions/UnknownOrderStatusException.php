<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain\Exceptions;

class UnknownOrderStatusException extends AbstractOrderDomainException
{
    private const string MESSAGE_KEY = "Unknown order status %s";
    public function __construct(string $unknownHandle = "", int $code = 0, ?\Throwable $previous = null)
    {
        $message= sprintf(self::MESSAGE_KEY, $unknownHandle);
        parent::__construct($message, $code, $previous);
    }
}
