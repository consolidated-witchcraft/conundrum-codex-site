<?php

namespace App\Services\Orders\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException;

class OrderStatus
{
    public const string INVOICED_STATUS_HANDLE = 'invoiced';

    public const string PAID_STATUS_HANDLE = 'paid';

    public const string SHIPPED_STATUS_HANDLE = 'shipped';

    public const string DELIVERED_STATUS_HANDLE = 'delivered';

    public const string REFUNDED_STATUS_HANDLE = 'refunded';

    public const string CANCELED_STATUS_HANDLE = 'canceled';

    public const string CLOSED_STATUS_HANDLE = 'closed';

    public const array KNOWN_ORDER_STATUSES = [
        self::INVOICED_STATUS_HANDLE => 'Invoiced',
        self::PAID_STATUS_HANDLE => 'Paid',
        self::SHIPPED_STATUS_HANDLE => 'Shipped',
        self::DELIVERED_STATUS_HANDLE => 'Delivered',
        self::REFUNDED_STATUS_HANDLE => 'Refunded',
        self::CANCELED_STATUS_HANDLE => 'Canceled',
        self::CLOSED_STATUS_HANDLE => 'Closed',
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

    public static function makeFromHandle(string $handle): OrderStatus {
        if (!array_key_exists($handle, self::KNOWN_ORDER_STATUSES)) {
            throw new UnknownOrderStatusException($handle);
        }
        return new self($handle, self::KNOWN_ORDER_STATUSES[$handle]);
    }
}
