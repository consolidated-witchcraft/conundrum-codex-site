<?php

declare(strict_types=1);

namespace App\Dto\EventManager;

use App\Dto\Visitor\User\PostalAddressFieldDto;
use App\Services\Events\Domain\Organisation;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\User\Domain\User;

use Symfony\Component\Validator\Constraints as Assert;
class OrganisationDto
{
    public function __construct(
        public readonly User $owner,

        #[Assert\NotBlank]
        public ? string $name = null,
        #[Assert\NotBlank]
        public ? string $handle = null,

        public ? PostalAddressFieldDto $postalAddress = null,

        public ? PostalAddressFieldDto $invoiceAddress = null,
        #[Assert\NotBlank]
        public iterable $managers = [],
    ) {}

    public static function makeFromOrganisation(Organisation $organisation): OrganisationDto {
        return new static(
            owner: $organisation->getOwner(),
            name: $organisation->getName(),
            handle: $organisation->getHandle(),
            postalAddress: PostalAddressFieldDto::makeFromAddress($organisation->getPostalAddress()),
            invoiceAddress: PostalAddressFieldDto::makeFromAddress($organisation->getInvoiceAddress()),
            managers: $organisation->getManagers()
        );
    }
}
