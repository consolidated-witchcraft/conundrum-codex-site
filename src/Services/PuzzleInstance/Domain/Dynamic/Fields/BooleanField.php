<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class BooleanField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'bool';
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?bool
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY;
    }

    /**
     * @param float|array|bool|int|string $value
     * @return void
     * @throws DynamicFieldInvalidValueException
     */
    public function guard(mixed $value): void
    {
        if (!is_bool($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value)
            );
        }
    }
}
