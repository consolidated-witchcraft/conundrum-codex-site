<?php

namespace App\Services\Orders\Domain\Exceptions;

class OrderPaymentInvalidStatusChangeException extends AbstractOrderDomainException
{
    private const string MESSAGE_KEY = "An invalid attempt at changing a payment status was prevented. Order ID: %d, Payment ID: %d, Current payment status: %s, Attempted status: %s";
    public function __construct(
        int $orderId,
        int $paymentId,
        string $currentStatusHandle,
        string $attemptedStatusHandle,
        int $code = 0,
        \Throwable $previous = null
    ) {
        $message = sprintf(
            self::MESSAGE_KEY,
            $orderId,
            $paymentId,
            $currentStatusHandle,
            $attemptedStatusHandle
        );
        parent :: __construct($message, $code, $previous);
    }
}
