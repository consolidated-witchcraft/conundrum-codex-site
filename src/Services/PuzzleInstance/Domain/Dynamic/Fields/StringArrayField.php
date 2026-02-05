<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class StringArrayField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string
    {
        return 'string[]';
    }

    /**
     * @return string[]
     */
    public function getValue(): array
    {
        return $this->value ?? [];
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY;
    }

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
            if (!is_string($item)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: $this->getAcceptedScalarTypeDescription(),
                    actualType: sprintf("%s[]", gettype($item)),
                );
            }
        }
    }

    public function setValue(mixed $value): void {
        $this->guard($value);
        $this->value = array_map(function ($item) {
            return AbstractDynamicField::xssCleanString($item);
        }, $value);
    }
}
