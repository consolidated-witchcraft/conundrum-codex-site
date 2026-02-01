<?php

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos\Exceptions\DynamicFieldValueMismatchException;

class StringFieldDto extends AbstractDynamicFieldDto implements DynamicPuzzleFieldDtoInterface
{
    private const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = 'string';

    public function __construct(
        string $name,
        string $label,
        ? string $value = null
    ) {
        parent::__construct($name, $label, $value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(bool|string|int|array $value): void
    {
        if (!is_string($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: self::getAcceptedScalarTypeDescription(),
                actualType: gettype($value),
            );
        }
        $this->value = $value;
    }

    public static function getAcceptedScalarTypeDescription(): string
    {
        return self::ACCEPTED_SCALAR_TYPE_DESCRIPTION;
    }
}
