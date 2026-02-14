<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class PaymentStatusType extends Type
{
    public const string NAME = 'payment_status';

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
     * @return PaymentStatus|mixed
     * @throws \App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return PaymentStatus::makeFromHandle($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof PaymentStatus) {
            throw new UnknownPaymentStatusException("Trying to map an instance of paymentstatus that is not a paymentstatus type object.");
        }
        return $value->getHandle();
    }
}
