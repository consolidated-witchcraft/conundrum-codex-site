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
    public static function makeFromKey(string $key): self {

        if (!array_key_exists($key, static::KNOWN_KEYS)) {
            throw new UnknownValueObjectKeyException(
                valueObjectType: static::class,
                key: $key
            );
        }
        return new static(
            key: $key,
            label: static::KNOWN_KEYS[$key],
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
