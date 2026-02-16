<?php

namespace App\Services\Orders\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;

readonly class ChargeType
{
    public const array KNOWN_CHARGE_TYPES = [
        'service_charge' => 'Service charge',
        'delivery_charge' => 'Delivery charge',
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

    public static function makeFromHandle(string $handle): ChargeType {
        if (!array_key_exists($handle, self::KNOWN_CHARGE_TYPES)) {
            throw new UnknownPaymentStatusException($handle);
        }
        return new self($handle, self::KNOWN_CHARGE_TYPES[$handle]);
    }
}
