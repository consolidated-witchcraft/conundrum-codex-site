<?php

declare(strict_types=1);

namespace App\Services\PostalAddress\Domain\ValueObjects;

class IsoCountryList
{
    /**
     * @param IsoCountry[] $countryList
     */
    public function __construct(
        private array $countryList
    ) {}

    /**
     * @return IsoCountry[]
     */
    public function getCountryList(): array {
        return $this->countryList;
    }
}
