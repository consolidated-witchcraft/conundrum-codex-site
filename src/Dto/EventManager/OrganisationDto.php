<?php

declare(strict_types=1);

namespace App\Dto\EventManager;

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
        #[Assert\NotBlank]
        public array $managers = [],
    ) {}
}
