<?php

namespace App\Dto\Visitor\User;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Symfony\Component\Validator\Constraints as Assert;
class PostalAddressFieldDto
{
    public function __construct(

        #[Assert\NotBlank]
        public ? string $label = null,

        #[Assert\NotBlank]
        public ? string $addressLineOne = null,

        #[Assert\NotBlank]
        public ? string $townCity = null,

        #[Assert\NotBlank]
        public ? string $countyProvince = null,

        #[Assert\NotBlank]
        public ? string $countryCode = null,

        #[Assert\NotBlank]
        public ? string $postalZip = null,

        public ? string $addressLineTwo = null,

        public ? float $latitude = null,

        public ? float $longitude = null,

        public ? int $id = null
    ) {

    }

    public static function makeFromAddress(PostalAddress $address): PostalAddressFieldDto {
        return new static(
            label: $address->getLabel(),
            addressLineOne: $address->getAddressLineOne(),
            townCity: $address->getTownCity(),
            countyProvince: $address->getCountyProvince(),
            countryCode: $address->getCountryCode(),
            postalZip: $address->getPostalZip(),
            addressLineTwo: $address->getAddressLineTwo(),
            latitude: $address->getLatitude(),
            longitude: $address->getLongitude(),
            id: $address->getId()
        );
    }
}
