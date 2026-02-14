<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Doctrine\Common\Collections\ArrayCollection;

interface PuzzleTemplateServiceInterface
{
    /**
     * @return iterable<PuzzleTemplate>
     */
    public function getTemplates(): iterable;

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate;

    /**
     * @return PuzzleCategory[]
     */
    public function getAllCategories(): array;

    /**
     * @return ArrayCollection<int, PuzzleTemplate>
     */
    public function getTemplatesByCategory(PuzzleCategory $category): ArrayCollection;

    public function getCategoryBySlug(string $categorySlug): ? PuzzleCategory;

    public function generatePuzzleSlug(string $puzzleName, string $puzzleClass): string;

    public function saveCategory(PuzzleCategory $category): PuzzleCategory;
}
