<?php

namespace App\Dto\Visitor\Checkout;

use App\Dto\Visitor\User\PostalAddressDto;
use App\Dto\Visitor\User\PostalAddressFieldDto;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;

class DeliveryInformationDto
{
    public function __construct(
        public ? string $receiptEmail,
        public ? PostalAddressFieldDto $deliveryAddress = null,
        public ? PostalAddressFieldDto $billingAddress = null,
    ) {
    }


    public static function makeFromUser(User $user): static {
        $defaultShippingAddress = null;
        $defaultBillingAddress = null;
        foreach ($user->getPostalAddresses() as $address) {
            if ($address->isDefaultShippingAddress()) {
                $defaultShippingAddress = $address;
            }
            if ($address->isDefaultBillingAddress()) {
                $defaultBillingAddress = $address;
            }
        }
        return new self(
            receiptEmail: $user->getEmail(),
            deliveryAddress:   $defaultShippingAddress ? PostalAddressFieldDto::makeFromAddress($defaultBillingAddress) : null,
            billingAddress:   $defaultBillingAddress ? PostalAddressFieldDto::makeFromAddress($defaultBillingAddress) : null,
        );
    }
}
