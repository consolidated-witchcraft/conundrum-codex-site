<?php

namespace App\Services\PuzzleTemplate\Domain;

use App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions\PuzzleTemplateConfigOptionDefinitionInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\NonStaticConfigurationAttemptException;
use App\Services\PuzzleTemplate\Domain\Exceptions\RoutelessStaticConfigurationAttemptException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

readonly class PuzzleTemplate
{
    /**
     * @param non-empty-string $slug
     * @param non-empty-string $title
     * @param bool $hidden
     * @param \DateTimeImmutable $createdAt
     * @param non-empty-string $description
     * @param ArrayCollection<int, PuzzleCategory> $categories
     * @param non-empty-string $authorEmail
     * @param bool $static
     * @param string $elementName
     * @param PuzzleCredit[] $credits
     * @param array<int, PuzzleTemplateConfigOptionDefinitionInterface> $configuration
     * @param non-empty-string|null $staticCreateRoute
     * @param non-empty-string|null $staticEditRoute
     * @param non-empty-string|null $staticPlayRoute
     */
    public function __construct(
        private string $slug,
        private string $title,
        private bool $hidden,
        private \DateTimeImmutable $createdAt,
        private string $description,
        private Collection $categories,
        private string $authorEmail,
        private bool $static,
        private string $elementName,
        private array $credits = [],
        private array  $configuration = [],
        private ? string $staticCreateRoute = null,
        private ? string $staticEditRoute = null,
        private ? string $staticPlayRoute = null,
    ) {
    }


    /**
     * @return Collection<int, PuzzleCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return PuzzleCredit[]
     */
    public function getCredits(): array
    {
        return $this->credits;
    }

    /**
     * @return non-empty-string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return non-empty-string
     */
    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    /**
     * @return non-empty-string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return iterable<int, PuzzleCategory>
     */
    public function getPuzzleCategories(): iterable
    {
        return $this->categories;
    }

    /**
     * @return non-empty-string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return PuzzleTemplateConfigOptionDefinitionInterface[]
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isStatic(): bool {
        return $this->static;
    }

    public function isHidden(): bool {
        return $this->hidden;
    }
    /**
     * @throws RoutelessStaticConfigurationAttemptException
     * @throws NonStaticConfigurationAttemptException
     */
    public function getStaticCreateRoute(): ? string {
        $this->validateStatic();
        return $this->staticCreateRoute;
    }

    /**
     * @throws RoutelessStaticConfigurationAttemptException
     * @throws NonStaticConfigurationAttemptException
     */
    public function getStaticEditRoute(): ?string
    {
        $this->validateStatic();
        return $this->staticEditRoute;
    }

    /**
     * @throws RoutelessStaticConfigurationAttemptException
     * @throws NonStaticConfigurationAttemptException
     */
    public function getStaticPlayRoute(): ? string {
        $this->validateStatic();
        return $this->staticPlayRoute;
    }

    public function getElementName(): string {
        return $this->elementName;
    }

    /**
     * @return void
     * @throws NonStaticConfigurationAttemptException
     * @throws RoutelessStaticConfigurationAttemptException
     */
    private function validateStatic(): void {
        if (!$this->isStatic()) {
            throw new NonStaticConfigurationAttemptException("This template is dynamic, and must be configured using the standard configuration route");
        } elseif ($this->staticCreateRoute === null || $this->staticEditRoute === null || $this->staticPlayRoute === null) {
            throw new RoutelessStaticConfigurationAttemptException("This template is static, but a static configuration route has not been provided.");
        }
    }
}
