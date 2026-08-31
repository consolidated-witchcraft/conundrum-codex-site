<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, PostalAddress>
 */
class PostalAddressVoter extends Voter
{

    public const string OWNS_ADDRESS = 'owns_address';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::OWNS_ADDRESS) {
            return false;
        }

        if (!$subject instanceof PostalAddress) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if (!$subject instanceof PostalAddress) {
            return false;
        }

        return $user->getUserIdentifier() === $subject->getUser()->getUserIdentifier();

    }
}
