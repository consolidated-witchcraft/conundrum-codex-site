<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions;

readonly class PuzzleTemplatePuzzleTemplateConfigOptionDefinition implements PuzzleTemplateConfigOptionDefinitionInterface
{
    public function __construct(
        private string $configName,
        private string $label,
        private string $type,
        private ? string $helpText = null

    ) {
    }

    public function getConfigName(): string
    {
        return $this->configName;
    }
    public function getLabel(): string
    {
        return $this->label;
    }
    public function getType(): string
    {
        return $this->type;
    }

    public function getHelpText(): ? string
    {
        return $this->helpText;
    }
}
