<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields;

/** @template TValue */
abstract class AbstractDynamicField implements \JsonSerializable
{
    /** @var TValue|null */
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

    /** @return array{name: string, label: string, value: TValue|null, type: string} */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'value' => $this->value,
            'type' => $this->getType()
        ];
    }

    /** @phpstan-assert TValue $value */
    abstract public function guard(mixed $value): void;

    abstract public function getType(): string;

}
