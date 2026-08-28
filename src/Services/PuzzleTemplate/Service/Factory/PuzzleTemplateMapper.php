<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory;

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplatePuzzleTemplateConfigOptionDefinition;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;
use App\Services\PuzzleTemplate\Domain\PuzzleCredit;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleCategoryEntityServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

readonly class PuzzleTemplateMapper
{
    public function __construct(
        private PuzzleCategoryEntityServiceInterface $puzzleCategoryService,
    ) {}

    /**
     * @param array{
     *     slug: non-empty-string,
     *     title: non-empty-string,
     *     hidden: bool,
     *     created: string,
     *     description: non-empty-string,
     *     categories: list<string>,
     *     creator: non-empty-string,
     *     static: bool,
     *     elementName: string,
     *     credits?: list<array{name: string, created: string, email?: string|null}>,
     *     configOptions: list<array{configName: string, label: string, type: string, helpText?: string|null}>,
     *     staticCreateRoute?: non-empty-string|null,
     *     staticEditRoute?: non-empty-string|null,
     *     staticPlayRoute?: non-empty-string|null
     * } $templateData
     */
    public function mapTemplate(array $templateData): PuzzleTemplate {
        return new PuzzleTemplate(
            slug: $templateData['slug'],
            title: $templateData['title'],
            hidden: $templateData['hidden'],
            createdAt: new \DateTimeImmutable($templateData['created']),
            description: $templateData['description'],
            categories: $this->mapCategories($templateData['categories']),
            authorEmail: $templateData['creator'],
            static: $templateData['static'],
            elementName: $templateData['elementName'],
            credits: self::mapCredits($templateData['credits'] ?? []),
            configuration: self::mapConfigOptions($templateData['configOptions']),
            staticCreateRoute: $templateData['staticCreateRoute'] ?? null,
            staticEditRoute: $templateData['staticEditRoute'] ?? null,
            staticPlayRoute: $templateData['staticPlayRoute'] ?? null
        );
    }

    /**
     * @param list<array{name: string, created: string, email?: string|null}> $credits
     * @return list<PuzzleCredit>
     */
    public static function mapCredits(array $credits): array {
        $creditArray = [];
        foreach ($credits as $credit) {
            $creditArray[] = new PuzzleCredit(
                name: $credit['name'],
                created: $credit['created'],
                email: $credit['email'] ?? null
            );
        }
        return $creditArray;
    }

    /**
     * @param list<array{configName: string, label: string, type: string, helpText?: string|null}> $configOptionDefinitions
     * @return list<PuzzleTemplatePuzzleTemplateConfigOptionDefinition>
     */
    public static function mapConfigOptions(array $configOptionDefinitions): array {
        $configOptions = [];
        foreach ($configOptionDefinitions as $configOptionDefinition) {
            $configOptions[] = new PuzzleTemplatePuzzleTemplateConfigOptionDefinition(
                configName: $configOptionDefinition['configName'],
                label: $configOptionDefinition['label'],
                type: $configOptionDefinition['type'],
                helpText: $configOptionDefinition['helpText'] ?? null
            );
        }

        return $configOptions;

    }

    /**
     * @param list<string> $categories
     * @return ArrayCollection<int, PuzzleCategory>
     * @throws InvalidHandleException
     */
    public function mapCategories(array $categories): ArrayCollection {
        $categoriesCollection = new ArrayCollection();
        foreach ($categories as $categorySlug) {
            $category = $this->puzzleCategoryService->getCategoryBySlug($categorySlug);
            if (null === $category) {
                $label = self::generateCategoryLabel($categorySlug);
                $category = new PuzzleCategory(
                    label: $label,
                    handle: $categorySlug,
                    description: $label
                );
                $this->puzzleCategoryService->saveCategory($category);
            }
            $categoriesCollection->add($category);
        }
        return $categoriesCollection;
    }

    private static function generateCategoryLabel(string $categorySlug): string {
        $categorySlug = str_replace(search:'_', replace: ' ', subject: $categorySlug);
        $categorySlug = ucwords($categorySlug);
        return str_replace('And', 'and', $categorySlug);
    }
}
