<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Character\Domain\Character;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Character>
 */
class UserOwnsCharacterVoter extends Voter
{
    public const string MANAGE = 'user_owns_character';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE && $subject instanceof Character;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User || !$subject instanceof Character) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $userId = $user->getId();

        return $userId !== null && $userId === $subject->getUser()->getId();
    }
}
