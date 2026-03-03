<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use App\Services\User\Domain\User;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;
use App\Validator as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class UserEditDto
{
    final public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[CustomAssert\EmailAddressIsNotBlockedConstraint]
        public ? string $emailAddress = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ? string $userName = null,

        public bool $acceptedCookies = false,

        public bool $profilePublic = false,

        public ?UserPreferencesEditDto $userPreferences = null,

        public ? string $avatar = null
    ) {}


    public static function makeFromUser(User $user): static {
        return new static(
            emailAddress: $user->getEmail(),
            userName: $user->getUsername(),
            acceptedCookies: $user->getHasAcceptedCookies(),
            profilePublic: $user->isProfilePublic(),
            userPreferences: UserPreferencesEditDto::makeFromUserPreferences($user->getUserPreferences()),
        );
    }
}
