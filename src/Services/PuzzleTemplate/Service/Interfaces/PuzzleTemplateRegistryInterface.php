<?php

namespace App\Services\PuzzleTemplate\Service\Interfaces;

use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;

interface PuzzleTemplateRegistryInterface
{
    /**
     * @return array<PuzzleTemplate>
     */
    public function getTemplates(bool $includeHidden = false): array;

    public function getTemplate(string $slug): ?PuzzleTemplate;

    /**
     * @return PuzzleTemplate[]
     */
    public function getStaticTemplates(): array;
}
