<?php

declare(strict_types = 1);
namespace App\Dto\Visitor\Events;

use App\Services\User\Domain\User;
use Symfony\Component\Validator\Constraints as Assert;
class PersonalDetailsDto
{
    public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        public ? string $customerEmail = null,
        #[Assert\NotBlank]
        public ? string $customerName = null,
    ) {}

    public static function makeFromUser(User $user): PersonalDetailsDto {
        return new static(
            customerEmail: $user->getEmail(),
            customerName: $user->getUsername(),
        );
    }
}
