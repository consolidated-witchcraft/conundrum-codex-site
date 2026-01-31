<?php

namespace App\Services\PuzzleTemplate\Service;

use App\Services\PuzzleTemplate\Domain\PuzzleCategory;

interface PuzzleCategoryServiceInterface
{
    /**
     * @return PuzzleCategory[]
     */
    public function getAllCategories(): iterable;

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory;
    public function saveCategory(PuzzleCategory $category): PuzzleCategory;

}
