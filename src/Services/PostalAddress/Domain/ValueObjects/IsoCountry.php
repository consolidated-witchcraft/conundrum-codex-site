<?php

namespace App\Services\PostalAddress\Domain\ValueObjects;

readonly class IsoCountry implements \Stringable
{
    public function __construct(
        private string $isoCountryCode,
        private string $alphaThree,
        private string $name,
        private int $num
    ) {}

    public function getIsoCountryCode(): string
    {
        return $this->isoCountryCode;
    }

    public function getAlphaThree(): string
    {
        return $this->alphaThree;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNum(): int
    {
        return $this->num;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
