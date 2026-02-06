<?php

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\PostalAddressRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostalAddressRepository::class)]
class PostalAddress extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $label,

        #[ORM\Column(length: 255)]
        private string $addressLineOne,

        #[ORM\Column(length: 255)]
        private string $townCity,

        #[ORM\Column(length: 255)]
        private string $postalZip,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'postalAddresses')]
        private readonly User $user,

        #[ORM\Column(length: 255, nullable: true)]
        private ? string $addressLineTwo = null,

        #[ORM\Column(nullable: true)]
        private ? float $latitude = null,

        #[ORM\Column(nullable: true)]
        private ? float $longitude = null,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getAddressLineOne(): string
    {
        return $this->addressLineOne;
    }

    public function setAddressLineOne(string $addressLineOne): void
    {
        $this->addressLineOne = $addressLineOne;
    }

    public function getTownCity(): string
    {
        return $this->townCity;
    }

    public function setTownCity(string $townCity): void
    {
        $this->townCity = $townCity;
    }

    public function getPostalZip(): string
    {
        return $this->postalZip;
    }

    public function setPostalZip(string $postalZip): void
    {
        $this->postalZip = $postalZip;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAddressLineTwo(): ?string
    {
        return $this->addressLineTwo;
    }

    public function setAddressLineTwo(?string $addressLineTwo): void
    {
        $this->addressLineTwo = $addressLineTwo;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

}
