<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class DieRollField extends AbstractDynamicField implements DynamicPuzzleFieldInterface
{
    public const string ACCEPTED_SCALAR_TYPE_DESCRIPTION = 'array';

    public static function getAcceptedScalarTypeDescription(): string
    {
        return self::ACCEPTED_SCALAR_TYPE_DESCRIPTION;
    }

    public function getValue(): array
    {
        return $this->value ?? [];
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
    public function guard(float|array|bool|int|string $value): void
    {
        if (!is_array($value)) {
            throw new DynamicFieldValueMismatchException(
                fieldName: $this->getName(),
                acceptedType: 'array',
                actualType: gettype($value),
            );
        }
    }
}
