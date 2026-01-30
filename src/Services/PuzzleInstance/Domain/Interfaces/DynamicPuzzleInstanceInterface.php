<?php

namespace App\Services\PuzzleInstance\Domain\Interfaces;

use App\Dto\Visitor\PuzzleInstance\Dynamic\DynamicPuzzleFieldDto;

interface DynamicPuzzleInstanceInterface extends PuzzleInstanceInterface
{
    /**
     * @return DynamicPuzzleFieldDto[]
     */
    public function getConfig(): array;

    /**
     * @param DynamicPuzzleFieldDto[] $config
     */
    public function setConfig(array $config): void;
}
