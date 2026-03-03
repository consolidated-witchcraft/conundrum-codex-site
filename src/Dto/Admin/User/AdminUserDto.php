<?php

namespace App\Dto\Admin\User;

use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Validator as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 */
class AdminUserDto
{

    /**
     * @param string|null $email
     * @param string|null $username
     * @param Collection<int, UserFeat> $feats
     * @param Collection<int, Role>|null $roles
     * @param string|null $plainPassword
     * @param UserAccountType|null $userAccountType
     * @param bool $acceptedCookies
     * @param bool $profilePublic
     */
    final public function __construct(
        #[Assert\Email]
        #[Assert\NotBlank]
        #[CustomAssert\EmailAddressIsNotBlockedConstraint]
        public ? string $email = null,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        public ? string $username = null,

        public Collection $feats = new ArrayCollection(),
        public ? Collection $roles = null,

        #[Assert\Type('string')]
        #[Assert\Length(min: 8, max: 255)]
        public ? string $plainPassword = null,

        public ? UserAccountType $userAccountType = null,

        public bool $acceptedCookies = false,

        public bool $profilePublic = false,
    ) {
    }

    /**
     * @param User $user
     * @return self
     */
    public static function makeFromUser(User $user): self {
        return new static(
            email: $user->getEmail(),
            username: $user->getUsername(),
            feats: $user->getFeats(),
            roles: $user->getHydratedRoles(),
            userAccountType: $user->getUserAccountType(),
            acceptedCookies: $user->getHasAcceptedCookies(),
            profilePublic: $user->isProfilePublic()
        );
    }
}
