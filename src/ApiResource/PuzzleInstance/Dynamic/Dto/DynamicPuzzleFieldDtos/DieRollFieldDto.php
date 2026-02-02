<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class DieRollFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{
    public const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = 'array';

    public static function getAcceptedScalarTypeDescription(): string
    {
        return self::ACCEPTED_SCALAR_TYPE_DESCRIPTION;
    }

    public function getValue(): array
    {
        return $this->value ?? [];
    }

    /**
     * @throws DynamicFieldValueMismatchException
     */
    public function setValue(bool|array|int|string $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: 'array',
                actualType: gettype($value),
            );
        }
        $this->value = $value;
    }
}
