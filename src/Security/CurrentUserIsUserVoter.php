<?php

namespace App\Security;

use ApiPlatform\Metadata\ApiResource;
use App\ApiResource\User\Dto\ApiUserDto;
use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentUserIsUserVoter extends Voter
{
    public const string REQUESTED_PERMISSION = 'current_user_is_user';

    public function __construct(
        private readonly UserService $userService
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::REQUESTED_PERMISSION) {
            return false;
        }
        if (!$subject instanceof ApiUserDto && !$subject instanceof User) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if ($subject instanceof User) {
            return $currentUser->getUserIdentifier() === $subject->getUserIdentifier();
        }
        
        if ($subject instanceof ApiUserDto) {
            return $currentUser->getId() === $subject->getId();
        }

        return false;

    }
}
