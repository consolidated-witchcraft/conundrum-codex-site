<?php

namespace App\Services\Payment\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;

readonly class PaymentStatus implements \Stringable
{
    public const string UNPAID_STATUS_HANDLE = 'unpaid';

    public const string PARTIALLY_PAID_STATUS_HANDLE = 'partially_paid';

    public const string PROCESSING_STATUS_HANDLE = 'processing';

    public const string DECLINED_STATUS_HANDLE = 'declined';

    public const string PAID_STATUS_HANDLE = 'paid';

    public const array KNOWN_PAYMENT_STATUSES = [
        self::UNPAID_STATUS_HANDLE => 'Unpaid',
        self::PARTIALLY_PAID_STATUS_HANDLE => 'Partially Paid',
        self::PROCESSING_STATUS_HANDLE => 'Processing',
        self::DECLINED_STATUS_HANDLE => 'Declined',
        self::PAID_STATUS_HANDLE => 'Paid',
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

    public function isUnpaid(): bool {
        return self::UNPAID_STATUS_HANDLE === $this->handle;
    }

    public function isPartiallyPaid(): bool {
        return self::PARTIALLY_PAID_STATUS_HANDLE === $this->handle;
    }

    public function isProcessing(): bool {
        return self::PROCESSING_STATUS_HANDLE === $this->handle;
    }

    public function isDeclined(): bool {
        return self::DECLINED_STATUS_HANDLE === $this->handle;
    }

    public function isPaid(): bool {
        return self::PAID_STATUS_HANDLE === $this->handle;
    }
}
