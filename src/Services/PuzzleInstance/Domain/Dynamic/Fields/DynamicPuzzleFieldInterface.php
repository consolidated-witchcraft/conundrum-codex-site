<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;

interface DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string;
    public function getName(): string;

    public function getLabel(): string;

    /**
     * @return null|string|int|bool|array<int, string>|array<int, int>|array<int, bool>
     */
    public function getValue(): mixed;

    public function getType(): string;

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void;

    public function clearValue(): void;

    /**
     * @param bool|string|int|float|array $value
     * @return void
     * @throws DynamicFieldInvalidValueException
     */
    public function guard(mixed $value): void;
}
