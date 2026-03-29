<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service;

use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateRegistryInterface;

class PuzzleTemplateRegistry implements PuzzleTemplateRegistryInterface
{
    /**
     * @param PuzzleTemplate[] $templates
     */
    public function __construct(private array $templates) {

    }

    /**
     * @return PuzzleTemplate[]
     */

    public function getTemplates(bool $includeHidden = false): array
    {
        if ($includeHidden) {
            return $this->templates;
        } else {
            return array_filter($this->templates, fn (PuzzleTemplate $template): bool => !$template->isHidden());
        }

    }

    public function getTemplate(string $slug): ?PuzzleTemplate
    {
        foreach ($this->templates as $template) {
            if ($template->getSlug() === $slug) {
                return $template;
            }
        }
        return null;
    }

    /**
     * @return PuzzleTemplate[]
     */
    public function getStaticTemplates(): array
    {
        return array_filter($this->getTemplates(), function (PuzzleTemplate $template) {
            return $template->isStatic();
        });
    }
}
