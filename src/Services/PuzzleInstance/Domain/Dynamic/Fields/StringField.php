<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldUnsafeValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Traits\ValidatesStringsTrait;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<string> */
class StringField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{
    private const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = 'string';

    use ValidatesStringsTrait;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public static function getAcceptedScalarTypeDescription(): string
    {
        return self::ACCEPTED_SCALAR_TYPE_DESCRIPTION;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY;
    }

    public function guard(mixed $value): void
    {
        if (!is_string($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        $this->guardString($this->getName(), $value);
    }
}
