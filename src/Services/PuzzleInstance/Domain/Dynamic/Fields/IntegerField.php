<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<int> */
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
    public function setValue(mixed $value): void
    {
        $this->guard($value);
        $this->value = (int) $value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY;
    }

    /**
     * @param mixed $value
     * @return void
     * @throws DynamicFieldInvalidValueException
     */
    public function guard(mixed $value): void
    {
        if (!is_numeric($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        if (!is_integer($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
    }
}
