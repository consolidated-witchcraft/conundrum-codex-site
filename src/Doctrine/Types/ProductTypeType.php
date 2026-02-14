<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;
use App\Services\Orders\Domain\ValueObjects\ProductType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class ProductTypeType extends Type
{
    public const string NAME = 'product_type';

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
     * @return ProductType|mixed
     * @throws \App\Services\Orders\Domain\Exceptions\UnknownProductTypeException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return ProductType::makeFromHandle($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof ProductType) {
            throw new UnknownProductTypeException("Trying to map an instance of product_type that is not a ProductType type object.");
        }
        return $value->getHandle();
    }
}
