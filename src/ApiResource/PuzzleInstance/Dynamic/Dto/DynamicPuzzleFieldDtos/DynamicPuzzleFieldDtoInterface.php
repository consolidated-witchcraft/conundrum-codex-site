<?php

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

interface DynamicPuzzleFieldDtoInterface
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
    public function setValue(bool|string|int|array $value): void;

    public function clearValue(): void;
}
