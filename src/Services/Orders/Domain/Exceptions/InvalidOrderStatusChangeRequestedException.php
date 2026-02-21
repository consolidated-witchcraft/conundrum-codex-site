<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain\Exceptions;

class InvalidOrderStatusChangeRequestedException extends AbstractOrderDomainException
{

    private const string MESSAGE_KEY = "Invalid Order Status change requested for Order Reference %s. Current Status = %s, Requested Status = %s.";
    public function __construct(string $orderReference, string $currentStatus, string $requestedStatus, int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_KEY, $orderReference, $currentStatus, $requestedStatus);
        parent::__construct($message, $code, $previous);
    }
}
