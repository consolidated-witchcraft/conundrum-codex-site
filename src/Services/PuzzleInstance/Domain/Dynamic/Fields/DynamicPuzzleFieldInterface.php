<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;

interface DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string;
    public function getName(): string;

    public function getLabel(): string;

    /**
     * @return null|string|int|bool|array<int, string>|array<int, int>|array<int, bool>
     */
    public function getValue(): null|bool|string|int|array;

    public function getType(): string;

    /**
     * @param string|int|bool|array<int, string>|array<int, int>|array<int, bool>| $value
     * @return void
     */
    public function setValue(bool|string|int|float|array $value): void;

    public function clearValue(): void;

    /**
     * @param bool|string|int|float|array $value
     * @return void
     * @throws DynamicFieldValueMismatchException
     */
    public function guard(bool|string|int|float|array $value): void;
}
