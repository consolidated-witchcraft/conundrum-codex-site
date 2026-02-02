<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class StringField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{
    private const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = 'string';

    public function __construct(
        string $name,
        string $label,
        ? string $value = null
    ) {
        parent::__construct($name, $label, $value);
    }

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

    public function guard(float|array|bool|int|string $value): void
    {
        if (!is_string($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
    }
}
