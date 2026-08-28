<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain\Exceptions;

class OrderPaymentInvalidStatusChangeException extends AbstractOrderDomainException
{
    private const string MESSAGE_KEY = "An invalid attempt at changing a payment status was prevented. Order ID: %s, Payment ID: %s, Current payment status: %s, Attempted status: %s";
    public function __construct(
        ?int $orderId,
        string $paymentReference,
        string $currentStatusHandle,
        string $attemptedStatusHandle,
        int $code = 0,
        ? \Throwable $previous = null
    ) {
        $message = sprintf(
            self::MESSAGE_KEY,
            $orderId === null ? 'unpersisted' : (string) $orderId,
            $paymentReference,
            $currentStatusHandle,
            $attemptedStatusHandle
        );
        parent :: __construct($message, $code, $previous);
    }
}
