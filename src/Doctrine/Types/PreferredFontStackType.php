<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UnmappableObjectException;
use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;
use App\Services\User\Domain\ValueObjects\Rarity;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class PreferredFontStackType extends Type
{
    public const string NAME = 'preferred_font_stack';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }


    /**
     * @throws UnmappableObjectException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return $value !== '' ? PreferredFontStack::makeFromKey($value): PreferredFontStack::makeFromKey('regular');
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof PreferredFontStack) {
            throw new UnmappableObjectException(
                fieldType: $this->getName(),
                expectedObject: PreferredFontStack::class,
                actualValue: $value,
            );
        }
        return $value->getKey();
    }
}
