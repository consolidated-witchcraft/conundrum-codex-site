<?php

declare(strict_types=1);

namespace App\Services\PostalAddress\Domain\ValueObjects;

class IsoCountryList
{
    /**
     * @param array<string, IsoCountry> $countryList
     */
    public function __construct(
        private array $countryList
    ) {}

    /**
     * @return array<string, IsoCountry>
     */
    public function getCountryList(): array {
        return $this->countryList;
    }
}
