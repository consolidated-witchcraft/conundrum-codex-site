<?php

namespace App\Services\PostalAddress\Service;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;

interface PostalAddressEntityServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @return array<string, IsoCountry>
     */
    public function getCountryCodeList(): array;

    public function save(PostalAddress $postalAddress): PostalAddress;

    public function delete(PostalAddress $postalAddress): void;

    public function find(int $id): ? PostalAddress;
}
