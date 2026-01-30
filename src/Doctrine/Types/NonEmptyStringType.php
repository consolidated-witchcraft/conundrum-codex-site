<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\InvalidEmailException;
use App\Doctrine\Types\Exceptions\InvalidNonEmptyStringException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class NonEmptyStringType extends Type
{
    public const string NAME = 'non_empty_string';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param non-empty-string $value
     * @return non-empty-string
     * @throws InvalidEmailException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): string {
        self::validateNonEmptyString($value);
        return $value;
    }

    /**
     * @param non-empty-string $value
     * @return non-empty-string
     * @throws InvalidEmailException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        self::validateNonEmptyString($value);
        return $value;
    }

    /**
     * @throws InvalidNonEmptyStringException
     */
    public static function validateNonEmptyString(mixed $value): void {
        if (!is_string($value) || strlen($value) === 0 ) {
            throw new InvalidNonEmptyStringException($value);
        }
    }

    /**
     * @throws InvalidNonEmptyStringException
     * @param mixed $value
     *
     * @return non-empty-string
     */
    public static function mapNonEmptyString(mixed $value): string {
        self::validateNonEmptyString($value);
        return $value;
    }
}
