<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

abstract class AbstractDynamicField implements \JsonSerializable
{

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

    public function setValue(bool|array|int|float|string $value): void {
        $this->guard($value);
        $this->value = $value;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'value' => $this->value,
            'type' => $this->getType()
        ];
    }
}
