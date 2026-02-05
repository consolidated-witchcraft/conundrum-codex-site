<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

abstract class AbstractDynamicField implements \JsonSerializable
{
    protected mixed $value;

    public function __construct(
        private readonly string $name,
        private readonly string $label,
        mixed $value = null
    ) {
        if ($value !== null) {
            $this->guard($value);
        }
        $this->value = $value;
    }

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

    public function setValue(mixed $value): void {
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
