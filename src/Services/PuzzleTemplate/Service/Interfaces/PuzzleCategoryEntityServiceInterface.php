<?php

namespace App\Services\PuzzleTemplate\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;

interface PuzzleCategoryEntityServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @return PuzzleCategory[]
     */
    public function getAllCategories(): iterable;

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory;
    public function saveCategory(PuzzleCategory $category): PuzzleCategory;

}
