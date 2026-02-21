<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UnmappableObjectException;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class OrderStatusType extends Type
{
    public const string NAME = 'order_status';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }


    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return \App\Services\Orders\Domain\ValueObjects\ChargeType|mixed
     * @throws \App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return OrderStatus::makeFromHandle($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof OrderStatus) {
            throw new UnmappableObjectException(
                fieldType: $this->getName(),
                expectedObject: OrderStatus::class,
                actualValue: $value,
            );
        }
        return $value->getHandle();
    }
}
