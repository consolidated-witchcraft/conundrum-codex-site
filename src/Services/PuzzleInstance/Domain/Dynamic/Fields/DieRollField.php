<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

/** @extends AbstractDynamicField<array{x: int, d: int, p?: int|null}> */
class DieRollField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{
    private const array KNOWN_SUBFIELDS = [
        'x',
        'd',
        'p'
    ];
    public const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = "array{'x': int, 'd': int, 'p': ?int}";

    public static function getAcceptedScalarTypeDescription(): string
    {
        return self::ACCEPTED_SCALAR_TYPE_DESCRIPTION;
    }

    /** @return array{x: int, d: int, p?: int|null}|null */
    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY;
    }

    /** @throws DynamicFieldInvalidValueException */
    public function guard(mixed $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldInvalidValueException(
                fieldName: $this->getName(),
                acceptedType: 'array',
                actualType: gettype($value),
            );
        }
        foreach ($value as $key => $item) {
            if (!in_array($key, self::KNOWN_SUBFIELDS)) {
                throw new DynamicFieldInvalidValueException(
                    fieldName: $this->getName(),
                    acceptedType: self::ACCEPTED_SCALAR_TYPE_DESCRIPTION,
                    actualType: gettype($value),
                );
            }

            if (!is_numeric($item)) {
                throw new DynamicFieldInvalidValueException(
                    fieldName: $this->getName(),
                    acceptedType: self::ACCEPTED_SCALAR_TYPE_DESCRIPTION,
                    actualType: gettype($item),
                );
            }
        }
    }
}
