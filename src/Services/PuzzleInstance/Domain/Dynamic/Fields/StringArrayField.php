<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Traits\ValidatesStringsTrait;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<list<string>> */
class StringArrayField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{
    use ValidatesStringsTrait;
    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'string[]';
    }

    /**
     * @return list<string>|null
     */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY;
    }

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
            if (!is_string($item)) {
                throw new DynamicFieldInvalidValueException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
            $this->guardString(
                fieldName: $this->getName(),
                value: $item
            );
        }
    }
}
