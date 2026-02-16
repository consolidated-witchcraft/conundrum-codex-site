<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UnmappableObjectException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\ValueObjects\ChargeType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ChargeTypeType extends Type
{
    public const string NAME = 'charge_type';

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
     * @return ChargeType
     * @throws UnknownPaymentStatusException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ChargeType
    {
        return ChargeType::makeFromHandle($value);
    }

    /**
     * @throws UnmappableObjectException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string {
        if (!$value instanceof ChargeType) {
            throw new UnmappableObjectException(
                fieldType: $this->getName(),
                expectedObject: ChargeType::class,
                actualValue: $value,
            );
        }
        return $value->getHandle();
    }
}
