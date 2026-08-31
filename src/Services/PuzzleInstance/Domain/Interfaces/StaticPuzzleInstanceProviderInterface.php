<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Interfaces;


interface StaticPuzzleInstanceProviderInterface extends PuzzleInstanceProviderInterface
{
    public function providesTemplateInstances(): string;

    public function getInstance(string $instanceCode): ? PuzzleInstanceInterface;
}
