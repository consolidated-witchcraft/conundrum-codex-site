<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Campaign\Domain\Campaign;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, Campaign> */
class CampaignManagerVoter extends Voter
{
    public const string MANAGE = 'manage_campaign';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::MANAGE === $attribute && $subject instanceof Campaign;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Campaign || !$token->getUser() instanceof User) {
            return false;
        }

        $gamesMaster = $subject->getGamesMaster();

        return $gamesMaster instanceof User
            && null !== $gamesMaster->getId()
            && $gamesMaster->getId() === $token->getUser()->getId();
    }
}
