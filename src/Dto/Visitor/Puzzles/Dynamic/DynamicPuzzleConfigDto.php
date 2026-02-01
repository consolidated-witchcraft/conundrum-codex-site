<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Dynamic;

use App\Dto\Visitor\Puzzles\Dynamic\Exceptions\NonExistentOffsetSetGetException;
use App\Services\PuzzleTemplate\Domain\Exceptions\AttemptingToConfigureNonExistentFieldException;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;

use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Validator as CustomAssert;

/**
 * @implements \ArrayAccess<string, mixed>
 */
class DynamicPuzzleConfigDto implements \ArrayAccess
{
    /**
     * @param array<string, DynamicPuzzleFieldDto> $fields
     */
    final private function __construct(
        #[CustomAssert\PuzzleTemplateFieldsConstraint]
        private array $fields
    ) {
    }

    private function fieldExists(mixed $key): bool {
        if (is_string($key) ||is_int($key)) {
            return isset($this->fields[$key]);
        }
        return false;
    }

    public function __isset(string $name) : bool {
        if (!$this->fieldExists($name)) {
            return false;
        }
        return $this->fields[$name]->hasValue();
    }

    public function __get(string $name): mixed
    {
        if ($this->fieldExists($name)) {
            return $this->fields[$name]->getValue();
        }
        return null;
    }

    public function __set(string $name, mixed $value): void {
        if ($this->fieldExists($name) && is_string($value)) {
            $this->fields[$name]->setValue($value);
        }
    }

    public static function makeFromTemplateDefinition(PuzzleTemplate $template): DynamicPuzzleConfigDto {
        return new static(self::mapConfiguration($template->getConfiguration()));
    }

    /**
     * @param array<int, ConfigOptionDefinition> $configuration
     * @return array<string, DynamicPuzzleFieldDto>
     */
    private static function mapConfiguration(array $configuration): array {
        $fields = [];
        foreach ($configuration as $configOption) {
            $fields[$configOption->getConfigName()] = new DynamicPuzzleFieldDto(
                name: $configOption->getConfigName(),
                type: $configOption->getType(),
            );
        }
        return $fields;

    }

    public function offsetExists(mixed $offset): bool
    {
        if (is_string($offset) || is_int($offset)) {
            return $this->fieldExists($offset);
        }
        return false;
    }

    /**
     * @throws NonExistentOffsetSetGetException
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (!$this->fieldExists($offset)) {
            throw new NonExistentOffsetSetGetException($offset, "get");
        }
        return $this->fields[$offset]->getValue();
    }

    /**
     * @throws NonExistentOffsetSetGetException
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$this->fieldExists($offset)) {
            throw new NonExistentOffsetSetGetException($offset, "set");
        }
        $this->fields[$offset]->setValue($value);
    }

    /**
     * @throws NonExistentOffsetSetGetException
     */
    public function offsetUnset(mixed $offset): void
    {
        if (!$this->fieldExists($offset)) {
            throw new NonExistentOffsetSetGetException($offset, "unset");
        }
        $this->fields[$offset]->clearValue();
    }

    public function toArray(): array {

        $returnArray = [];
        foreach ($this->fields as  $definition) {
            $returnArray[$definition->getName()] = $definition;
        }
        return $returnArray;
    }

    public function populateFromPuzzleInstance(DynamicPuzzleInstanceInterface $instance): void {
        foreach ($instance->getConfig() as $fieldValue) {
            if (!$this->offsetExists($fieldValue->getName())) {
                throw new AttemptingToConfigureNonExistentFieldException($fieldValue->getName());
            }
            $this->offsetSet($fieldValue->getName(), $fieldValue);

        }
    }
}
