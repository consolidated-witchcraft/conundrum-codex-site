<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Dynamic;

class DynamicPuzzleFieldDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $type,
        private string|array|null $value = null
    ) {}

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
        return $this->type;
    }

    /**
     * @return string|string[]|null
     */
    public function getValue(): string|array|null {
        if (null === $this->value) {
            return null;
        }
        if (is_array($this->value)) {
            if (array_is_list($this->value)) {
                return array_values($this->value);
            }

            return $this->value;
        }
        return $this->value;
    }

    /**
     * @param string|array<string>|null $value
     * @return void
     */
    public function setValue(string|array|null|DynamicPuzzleFieldDto $value): void {
        if ($value instanceof DynamicPuzzleFieldDto) {
            $this->value = $value->getValue();
        } else {
            $this->value = $value;
        }

    }

    public function clearValue(): void {
        $this->value = null;
    }

    public function hasValue(): bool {
        return $this->value !== null;
    }

}
