<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;

interface DynamicPuzzleFieldInterface
{

    public static function getAcceptedScalarTypeDescription(): string;
    public function getName(): string;

    public function getLabel(): string;

    public function getValue(): mixed;

    public function getType(): string;

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void;

    public function clearValue(): void;

    /** @throws DynamicFieldInvalidValueException */
    public function guard(mixed $value): void;
}
