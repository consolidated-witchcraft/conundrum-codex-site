<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<array<array-key, mixed>> */
class ClueArrayField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'array<string, string>';
    }

    /** @return array<array-key, mixed>|null */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY;
    }

    /**
     * @param mixed $value
     * @return void
     * @throws DynamicFieldInvalidValueException
     */
    public function guard(mixed $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: $this->getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        foreach ($value as $item) {
            if ($item !== null && !is_array($item)) {
                throw new DynamicFieldInvalidValueException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }
}
