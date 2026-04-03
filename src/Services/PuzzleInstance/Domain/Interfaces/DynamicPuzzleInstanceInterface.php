<?php

namespace App\Services\PuzzleInstance\Domain\Interfaces;

use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;

interface DynamicPuzzleInstanceInterface extends PuzzleInstanceInterface
{
    /**
     * @return array<string, DynamicPuzzleFieldDto>|array<int, DynamicPuzzleFieldDto>
     */
    public function getConfig(): array;

    /**
     * @param array<string, DynamicPuzzleFieldDto>|array<int, DynamicPuzzleFieldDto> $config
     */
    public function setConfig(array $config): void;
}
