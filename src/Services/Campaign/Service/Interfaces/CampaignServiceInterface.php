<?php

declare(strict_types=1);

namespace App\Services\Campaign\Service\Interfaces;

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\User\Domain\User;

interface CampaignServiceInterface
{
    public function flush(): void;
    public function findBySlug(string $slug): ?Campaign;
    public function saveInvitation(CampaignInvitation $invitation): CampaignInvitation;
    public function findInvitationByCode(string $code): ?CampaignInvitation;
    public function findInvitationByCodeAndEmail(string $code, string $email): ?CampaignInvitation;
    /** @return list<CampaignInvitation> */
    public function getOutstandingInvitationsForCampaign(Campaign $campaign): array;
    /** @return list<CampaignInvitation> */
    public function getOutstandingInvitationsForUser(User $user): array;
    /** @return list<CampaignInvitation> */
    public function findInvitationsByEmail(string $email): array;
    /** @return list<CampaignInvitation> */
    public function getExpiredInvitations(): array;
    public function deleteInvitation(CampaignInvitation $invitation, bool $flush = true): void;
    public function redeemInvitation(CampaignInvitation $invitation, User $user): CampaignMembership;
}
