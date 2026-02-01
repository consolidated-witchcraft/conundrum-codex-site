<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class BooleanArrayFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'bool[]';
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function setValue(array|bool|int|string $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        foreach ($value as $item) {
            if (!is_bool($item)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }
}
