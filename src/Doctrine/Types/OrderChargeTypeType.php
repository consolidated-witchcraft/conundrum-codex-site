<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\ValueObjects\OrderChargeType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class OrderChargeTypeType extends Type
{
    public const string NAME = 'order_charge_type';

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
     * @return OrderChargeType|mixed
     * @throws UnknownPaymentStatusException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return OrderChargeType::makeFromHandle($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof OrderChargeType) {
            throw new UnknownPaymentStatusException("Trying to map an instance of order_charge_type that is not a OrderChargeType type object.");
        }
        return $value->getHandle();
    }
}
