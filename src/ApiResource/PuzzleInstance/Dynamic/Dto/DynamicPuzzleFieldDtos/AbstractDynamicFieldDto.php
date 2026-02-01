<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleFieldDtos;

abstract class AbstractDynamicFieldDto
{

    protected  const string TYPE = '';

    public function getType(): string
    {
        return self::TYPE;
    }

    public function __construct(
        private readonly string $name,
        private readonly string $label,
        protected mixed $value
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function clearValue(): void
    {
        $this->value = null;
    }
}
