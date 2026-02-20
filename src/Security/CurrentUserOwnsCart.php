<?php

namespace App\Security;

use ApiPlatform\Metadata\ApiResource;
use App\ApiResource\User\Dto\ApiUserCartDto;
use App\ApiResource\User\Dto\ApiUserDto;
use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CurrentUserOwnsCart extends Voter
{
    public const string REQUESTED_PERMISSION = 'current_user_owns_cart';

    public function __construct(
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::REQUESTED_PERMISSION) {
            return false;
        }
        if (!$subject instanceof ApiUserCartDto) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if ($currentUser instanceof User) {
            return $currentUser->getId() === $subject->userId;
        }

        return false;

    }
}
