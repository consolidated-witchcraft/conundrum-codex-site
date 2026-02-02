<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class IntegerField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
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
    public function setValue(bool|array|int|float|string $value): void
    {
        $this->guard($value);
        $this->value = (int) $value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY;
    }

    /**
     * @param float|array|bool|int|string $value
     * @return void
     * @throws DynamicFieldValueMismatchException
     */
    public function guard(float|array|bool|int|string $value): void
    {
        if (!is_numeric($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
    }
}
