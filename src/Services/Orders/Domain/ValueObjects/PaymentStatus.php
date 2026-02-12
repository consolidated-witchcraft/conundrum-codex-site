<?php

namespace App\Services\Orders\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;

readonly class PaymentStatus
{
    public const array KNOWN_PAYMENT_STATUSES = [
        'unpaid' => 'unpaid',
        'processing' => 'processing',
        'declined' => 'declined',
        'paid' => 'paid',
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
    }
}
