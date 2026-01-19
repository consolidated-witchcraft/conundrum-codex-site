<?php

namespace App\Services\Puzzle\Domain\Interfaces;

use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\PuzzleTemplate;

interface DynamicPuzzleInstanceInterface extends PuzzleInstanceInterface
{
    /**
     * @return array<string, DynamicPuzzleFieldDto>
     */
    public function getConfig(): array;

    /**
     * @param array<string, DynamicPuzzleFieldDto> $config
     */
    public function setConfig(array $config): void;
}
