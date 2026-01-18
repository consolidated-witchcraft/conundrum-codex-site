<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleInstance;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use App\Services\Puzzle\Infrastructure\PuzzleInstanceRepository;
use App\Services\Puzzle\Service\Exceptions\SlugGenerationFailureException;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PuzzleTemplateService implements PuzzleTemplateServiceInterface
{
    private const string ALLOWABLE_SLUG_CHARACTER_PATTERN  = '/[^a-z_]/';

    /**
     * @param PuzzleTemplateRegistryInterface $templateRegistry
     * @param PuzzleCategoryRepository $categoryRepository
     * @param CasebookRepository $casebookRepository
     * @param PuzzleInstanceRepository $instanceRepository
     */
    public function __construct(
        private readonly PuzzleTemplateRegistryInterface $templateRegistry,
        private readonly PuzzleCategoryRepository $categoryRepository,
        private readonly CasebookRepository $casebookRepository,
        private readonly PuzzleInstanceRepository $instanceRepository,
    ) {}


    /**
     * @return PuzzleTemplate[]
     */
    public function getTemplates(): iterable
    {
        return $this->templateRegistry->getTemplates();
    }

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate
    {
        return $this->templateRegistry->getTemplate($categorySlug);
    }

    /**
     * @return PuzzleCategory[]
     */
    public function getAllCategories(): array {
        return $this->categoryRepository->findAll();
    }

    /**
     * @return ArrayCollection<int, PuzzleTemplate>
     */
    public function getTemplatesByCategory(PuzzleCategory $category): ArrayCollection
    {
        $collection = new ArrayCollection();
        foreach ($this->getTemplates() as $template) {
            foreach ($template->getCategories() as $puzzleCategory) {
                if ($puzzleCategory->getHandle() === $category->getHandle()) {
                    if (!$collection->contains($template)) {
                        $collection->add($template);
                    }
                }
            }
        }
        return $collection;
    }

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory
    {
        return $this->categoryRepository->findByHandle($categorySlug);
    }

    /**
     * @throws \Exception
     */
    public function generatePuzzleSlug(string $puzzleName, string $puzzleClass): string
    {
        $slug = strtolower($puzzleName);
        if (str_starts_with($slug, 'the ')) {
            $slug = substr($slug, 4);
        }
        $slug = str_replace(" ", "_", $slug);
        $slug = preg_replace(self::ALLOWABLE_SLUG_CHARACTER_PATTERN, '', $slug);
        if (!is_string($slug)) {
            throw new SlugGenerationFailureException(sprintf("Unable to generate slug for puzzle '%s'", $puzzleName));
        }


        switch ($puzzleClass) {
            case Casebook::class:
                return $this->casebookRepository->decollideInstanceCode($slug);
            case PuzzleInstance::class:
                return $this->instanceRepository->decollideInstanceCode($slug);
        }


        return $slug;
    }
}
