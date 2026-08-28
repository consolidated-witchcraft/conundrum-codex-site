<?php

declare(strict_types=1);

namespace App\Services\PostalAddress\Domain;

use App\Dto\Visitor\User\PostalAddressDto;
use App\Dto\Visitor\User\PostalAddressFieldDto;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;
use App\Services\PostalAddress\Infrastructure\PostalAddressRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostalAddressRepository::class)]
class PostalAddress extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'postalAddresses')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly User $user,

        #[ORM\Column(length: 255)]
        private string $label,

        #[ORM\Column(length: 255)]
        private string $addressLineOne,

        #[ORM\Column(length: 255)]
        private string $townCity,

        #[ORM\Column(length: 255)]
        private string $postalZip,

        #[ORM\Column(length: 255)]
        private string $countryCode,

        #[ORM\Column(length: 255)]
        private string $countyProvince,

        #[ORM\Column]
        private bool $isDefaultBillingAddress = false,

        #[ORM\Column]
        private bool $isDefaultShippingAddress = false,

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

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getCountyProvince(): string
    {
        return $this->countyProvince;
    }

    public function setCountyProvince(string $countyProvince): void
    {
        $this->countyProvince = $countyProvince;
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

    public function isDefaultBillingAddress(): bool
    {
        return $this->isDefaultBillingAddress;
    }

    public function setDefaultBillingAddress(bool $isDefaultBillingAddress): void {
        $this->isDefaultBillingAddress = $isDefaultBillingAddress;
    }

    public function isDefaultShippingAddress(): bool {
        return $this->isDefaultShippingAddress;
    }

    public function setDefaultShippingAddress(bool $isDefaultShippingAddress): void {
        $this->isDefaultShippingAddress = $isDefaultShippingAddress;
    }

    public static function makeFromDto(PostalAddressDto $dto): self {
        $address = $dto->postalAddress;
        if ($address === null) {
            throw new \InvalidArgumentException('A postal address is required.');
        }

        return new self(
            user: $dto->user,
            label: self::requireField($address->label, 'label'),
            addressLineOne: self::requireField($address->addressLineOne, 'address line one'),
            townCity: self::requireField($address->townCity, 'town/city'),
            postalZip: self::requireField($address->postalZip, 'postal/ZIP code'),
            countryCode: self::requireField($address->countryCode, 'country code'),
            countyProvince: self::requireField($address->countyProvince, 'county/province'),
            addressLineTwo: $address->addressLineTwo,
            id: $dto->id
        );
    }

    public static function makeFromPostalAddressFieldDto(User $user, PostalAddressFieldDto $dto): self {
        return new self(
            user: $user,
            label: self::requireField($dto->label, 'label'),
            addressLineOne: self::requireField($dto->addressLineOne, 'address line one'),
            townCity: self::requireField($dto->townCity, 'town/city'),
            postalZip: self::requireField($dto->postalZip, 'postal/ZIP code'),
            countryCode: self::requireField($dto->countryCode, 'country code'),
            countyProvince: self::requireField($dto->countyProvince, 'county/province'),
            addressLineTwo: $dto->addressLineTwo,
            latitude: $dto->latitude,
            longitude: $dto->longitude,
            id: $dto->id
        );
    }

    private static function requireField(?string $value, string $field): string
    {
        if ($value === null || trim($value) === '') {
            throw new \InvalidArgumentException(sprintf('Postal address %s is required.', $field));
        }

        return $value;
    }
}
