<?php

namespace App\Services\PuzzleTemplate\Domain\ConfigOptionDefinitions;

interface PuzzleTemplateConfigOptionDefinitionInterface
{
    public function getConfigName(): string;

    public function getLabel(): string;

    public function getType(): string;

    public function getHelpText(): ? string;
}
