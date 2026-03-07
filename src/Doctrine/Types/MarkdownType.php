<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\InvalidDoctrineCustomTypeValueException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MarkdownType extends Type
{
    public const string NAME = 'markdown';

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
     * @throws InvalidDoctrineCustomTypeValueException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): string {
        $this->validateValue('retrieve', $value);
        return $value;
    }

    /**
     * @param non-empty-string $value
     * @return non-empty-string
     * @throws InvalidDoctrineCustomTypeValueException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $this->validateValue('store', $value);
        return $value;
    }

    /**
     * @throws InvalidDoctrineCustomTypeValueException
     */
    private function validateValue(string $operation, mixed $value): void {
        if (!is_string($value) || strlen($value) === 0) {
            throw new InvalidDoctrineCustomTypeValueException(
                $operation,
                $value,
            );
        }
    }
}
