<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Events\Domain\Organisation;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, Organisation>
 */
class EventManagerVoter extends Voter
{

    public const string MANAGE_EVENTS_ACTION = 'manage_events';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::MANAGE_EVENTS_ACTION) {
            return false;
        }

        if (!$subject instanceof Organisation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        if ($user instanceof User && $subject instanceof Organisation) {
            $organisationOwner = $subject->getOwner();

            if ($user->getUserIdentifier() === $organisationOwner->getUserIdentifier()) {
                return true;
            }
            foreach ($subject->getManagers() as $eventManager) {
                if ($user->getUserIdentifier() === $organisationOwner->getUserIdentifier()) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
}
