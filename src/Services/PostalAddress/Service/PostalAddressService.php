<?php

declare(strict_types=1);

namespace App\Services\PostalAddress\Service;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountryList;
use App\Services\PostalAddress\Infrastructure\PostalAddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class PostalAddressService implements PostalAddressServiceInterface
{
    /**
     * @param array<string, IsoCountry> $countryCodeList
     */
    public function __construct(
        private readonly IsoCountryList $isoCountryList,
        private readonly EntityManagerInterface $entityManager,
        private readonly PostalAddressRepository $postalAddressRepository,
    ) {

    }
    public function getCountryCodeList(): array
    {
        return $this->isoCountryList->getCountryList();
    }

    public function save(PostalAddress $postalAddress): PostalAddress
    {
        $this->entityManager->persist($postalAddress);
        $this->entityManager->flush();
        return $postalAddress;
    }

    public function find(int $id): ?PostalAddress
    {
        return $this->postalAddressRepository->find($id);
    }

    public function delete(PostalAddress $postalAddress): void
    {
        $this->entityManager->remove($postalAddress);
        $this->entityManager->flush();
    }
}
