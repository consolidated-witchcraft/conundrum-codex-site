<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<list<int>> */
class IntegerArrayField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'int[]';
    }

    /**
     * @return ?int[]
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY;
    }

    public function setValue(mixed $value): void {
        $this->guard($value);
        $values = [];
        foreach ($value as $valueItem) {
            $values[] = (int) $valueItem;
        }
        $this->value = $values;
    }

    /** @throws DynamicFieldInvalidValueException */
    public function guard(mixed $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        foreach ($value as $item) {
            if (!is_numeric($item)) {
                throw new DynamicFieldInvalidValueException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }
}
