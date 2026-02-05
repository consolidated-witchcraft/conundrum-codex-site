<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

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

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getType(): string
    {
        return ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY;
    }

    /**
     * @param float|array|bool|int|string $value
     * @return void
     * @throws DynamicFieldValueMismatchException
     */
    public function guard(mixed $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: 'array',
                actualType: gettype($value),
            );
        }
        foreach ($value as $key => $item) {
            if (!in_array($key, self::KNOWN_SUBFIELDS)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: 'array[x,d,p]',
                    actualType: gettype($value),
                );
            }

            if (!is_int($item)) {
                throw new DynamicFieldValueMismatchException(
                    fieldName: $this->getName(),
                    acceptedType: 'array[x,d,p]',
                    actualType: gettype($item),
                );
            }
        }
    }
}
