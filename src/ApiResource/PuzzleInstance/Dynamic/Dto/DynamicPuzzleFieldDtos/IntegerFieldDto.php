<?php

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\AbstractDynamicFieldDto;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\DynamicPuzzleFieldDtoInterface;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class IntegerFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'int';
    }

    /**
     * @return int
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setValue(bool|array|int|string $value): void
    {
        if (!is_int($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        $this->value = $value;
    }
}
