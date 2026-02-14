<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service;

use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookRepository;
use App\Services\PuzzleInstance\Infrastructure\PuzzleInstanceRepository;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Exceptions\SlugGenerationFailureException;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleCategoryEntityServiceInterface;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateRegistryInterface;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PuzzleTemplateService implements PuzzleTemplateServiceInterface
{
    private const string ALLOWABLE_SLUG_CHARACTER_PATTERN  = '/[^a-z_]/';

    /**
     * @param PuzzleTemplateRegistryInterface $templateRegistry
     * @param PuzzleCategoryEntityServiceInterface $puzzleCategoryService
     * @param CasebookRepository $casebookRepository
     * @param PuzzleInstanceRepository $instanceRepository
     */
    public function __construct(
        private readonly PuzzleTemplateRegistryInterface      $templateRegistry,
        private readonly PuzzleCategoryEntityServiceInterface $puzzleCategoryService,
        private readonly CasebookRepository                   $casebookRepository,
        private readonly PuzzleInstanceRepository             $instanceRepository,
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
        return $this->puzzleCategoryService->getAllCategories();
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
        return $this->puzzleCategoryService->getCategoryBySlug($categorySlug);
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

    public function saveCategory(PuzzleCategory $category): PuzzleCategory
    {
        return $this->puzzleCategoryService->saveCategory($category);
    }
}
