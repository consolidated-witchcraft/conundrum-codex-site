<?php

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\AbstractDynamicFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\DynamicPuzzleFieldDtoInterface;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class BooleanFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'bool';
    }

    /**
     * @inheritDoc
     */
    public function getValue(): bool
    {
        return $this->value ?? false;
    }

    /**
     * @inheritDoc
     */
    public function setValue(bool|array|int|string $value): void
    {
        if (!is_bool($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value)
            );
        }
    }
}
