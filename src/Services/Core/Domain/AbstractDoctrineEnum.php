<?php

declare(strict_types=1);

namespace App\Services\Core\Domain;

use App\Services\Core\Domain\Exceptions\UnknownValueObjectKeyException;

abstract class AbstractDoctrineEnum implements \Stringable
{
    protected const array KNOWN_KEYS = [];

    final private function __construct(
        private readonly string $key,
        private readonly string $label,
    ) {}

    /**
     * @throws UnknownValueObjectKeyException
     */
    public static function makeFromKey(string $key): static {

        if (!array_key_exists($key, static::KNOWN_KEYS)) {
            throw new UnknownValueObjectKeyException(
                valueObjectType: static::class,
                key: $key
            );
        }
        $label = static::KNOWN_KEYS[$key];
        if (!is_string($label)) {
            throw new \LogicException(sprintf('%s::KNOWN_KEYS values must be strings.', static::class));
        }

        return new static(
            key: $key,
            label: $label,
        );
    }

    public function getKey(): string {
        return $this->key;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function getClassSafeLabel(): string {
        return strtolower($this->label);
    }

    public function __toString(): string
    {
        return $this->key;
    }
}
