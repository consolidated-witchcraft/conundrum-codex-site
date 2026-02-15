<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\UnmappableObjectException;
use App\Services\Ticket\Domain\ValueObjects\TicketFormat;
use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TicketFormatType extends Type
{
    public const string NAME = 'ticket_format';

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
        return TicketFormat::makeFromHandle($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof TicketFormat) {
            throw new UnmappableObjectException(
                fieldType: self::NAME,
                expectedObject: TicketFormat::class,
                actualValue: $value,
            );
        }
        return $value->getHandle();

    }
}
