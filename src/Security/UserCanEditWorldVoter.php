<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\User\Domain\User;
use App\Services\World\Domain\World;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserCanEditWorldVoter extends Voter
{
    public const string CONFIGURE_WORLD_ACTION = 'configure_world';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::CONFIGURE_WORLD_ACTION) {
            return false;
        }

        if (!$subject instanceof World) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($user instanceof User && $subject instanceof World) {
            switch ($attribute) {
                case self::CONFIGURE_WORLD_ACTION:
                    return $subject->getCreator()->getUserIdentifier() === $user->getUserIdentifier();
                default:
                    return false;
            }

        }
    }
}
