<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class StringArrayFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'string[]';
    }

    /**
     * @return string[]
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function setValue(bool|array|int|string $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        foreach ($value as $item) {
            if (!is_string($item)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }
}
