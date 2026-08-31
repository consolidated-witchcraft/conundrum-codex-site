<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/** @extends Voter<string, CampaignInvitation> */
class CampaignInvitationVoter extends Voter
{
    public const string ACCEPT = 'accept_campaign_invitation';
    public const string REVOKE = 'revoke_campaign_invitation';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof CampaignInvitation && in_array(needle: $attribute, haystack: [self::ACCEPT, self::REVOKE], strict: true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof CampaignInvitation || !$token->getUser() instanceof User) {
            return false;
        }

        $gamesMasterId = $subject->getCampaign()->getGamesMaster()?->getId();

        return match ($attribute) {
            self::ACCEPT => $subject->getEmail() === $token->getUser()->getUserIdentifier(),
            self::REVOKE => null !== $gamesMasterId && $gamesMasterId === $token->getUser()->getId(),
            default => false,
        };
    }
}
