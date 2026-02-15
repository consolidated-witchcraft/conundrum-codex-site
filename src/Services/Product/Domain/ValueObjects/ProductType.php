<?php

declare(strict_types=1);

namespace App\Services\Product\Domain\ValueObjects;

use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;

readonly class ProductType implements \Stringable
{
    public const array KNOWN_PRODUCT_TYPES = [
        'physical_ticket' => 'Physical Ticket',
        'e_ticket' => 'Electronic Ticket',
        'merchandise' => 'Merchandise',
        'subscription' => 'Subscription',
        'misc' => 'Miscellaneous',
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

    public static function makeFromHandle(string $handle): ProductType {
        if (!array_key_exists($handle, self::KNOWN_PRODUCT_TYPES)) {
            throw new UnknownProductTypeException($handle);
        }
        return new self($handle, self::KNOWN_PRODUCT_TYPES[$handle]);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
