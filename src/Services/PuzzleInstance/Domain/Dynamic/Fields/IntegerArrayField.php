<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class IntegerArrayField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'int[]';
    }

    /**
     * @return int[]
     */
    public function getValue(): array
    {
        return $this->value ?? [];
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY;
    }

    public function setValue(array|bool|int|float|string $value): void {
        $this->guard($value);
        $values = [];
        foreach ($value as $valueItem) {
            $values[] = (int) $valueItem;
        }
        $this->value = $values;
    }

    /**
     * @param float|array|bool|int|string $value
     * @return void
     * @throws DynamicFieldValueMismatchException
     */
    public function guard(float|array|bool|int|string $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        foreach ($value as $item) {
            if (!is_numeric($item)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }
}
