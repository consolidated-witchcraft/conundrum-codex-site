<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Symfony\Component\Validator\Constraints as Assert;

class PostalAddressDto
{
    public function __construct(

        public readonly User $user,

        #[Assert\NotNull]
        public ? PostalAddressFieldDto $postalAddress = null,
        public ? int $id = null
    ) {

    }
    public static function makeFromAddress(PostalAddress $address): PostalAddressDto {
        return new static(
            user: $address->getUser(),
            postalAddress: PostalAddressFieldDto::makeFromAddress($address),
        );
    }
}
