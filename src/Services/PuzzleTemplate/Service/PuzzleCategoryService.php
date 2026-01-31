<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service;

use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Infrastructure\PuzzleCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class PuzzleCategoryService implements PuzzleCategoryServiceInterface
{
    public function __construct(
        private readonly PuzzleCategoryRepository $repository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getAllCategories(): iterable
    {
        return $this->repository->findAll();
    }

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory
    {
        return $this->repository->findByHandle($categorySlug);
    }

    public function saveCategory(PuzzleCategory $category): PuzzleCategory
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }
}
