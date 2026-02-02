<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
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
    public function getValue(): bool
    {
        return $this->value ?? false;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY;
    }

    /**
     * @param float|array|bool|int|string $value
     * @return void
     * @throws DynamicFieldValueMismatchException
     */
    public function guard(float|array|bool|int|string $value): void
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
