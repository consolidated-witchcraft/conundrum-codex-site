<?php

namespace App\Services\Payment\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;

readonly class PaymentStatus implements \Stringable
{
    public const array KNOWN_PAYMENT_STATUSES = [
        'unpaid' => 'Unpaid',
        'partially_paid' => 'Partially Paid',
        'processing' => 'Processing',
        'declined' => 'Declined',
        'paid' => 'Paid',
    ];

    private function __construct(
        private string $handle,
        private string $name
    ) {}

    public function getHandle(): string {
        return $this->handle;
    }

    public function getName(): string {
        return $this->name;
    }

    public static function makeFromHandle(string $handle): PaymentStatus {
        if (!array_key_exists($handle, self::KNOWN_PAYMENT_STATUSES)) {
            throw new UnknownPaymentStatusException($handle);
        }
        return new self($handle, self::KNOWN_PAYMENT_STATUSES[$handle]);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
