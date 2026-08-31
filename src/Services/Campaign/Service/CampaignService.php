<?php

declare(strict_types=1);

namespace App\Services\Campaign\Service;

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Infrastructure\CampaignInvitationRepository;
use App\Services\Campaign\Infrastructure\CampaignRepository;
use App\Services\Campaign\Service\Interfaces\CampaignServiceInterface;
use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class CampaignService extends AbstractDomainEntityService implements CampaignServiceInterface
{
    public function __construct(
        private readonly CampaignRepository $campaignRepository,
        private readonly CampaignInvitationRepository $invitationRepository,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct(entityManager: $entityManager);
    }

    public function findBySlug(string $slug): ?Campaign { return $this->campaignRepository->findOneBySlug(slug: $slug); }
    public function saveInvitation(CampaignInvitation $invitation): CampaignInvitation { $this->entityManager->persist($invitation); $this->entityManager->flush(); return $invitation; }
    public function findInvitationByCode(string $code): ?CampaignInvitation { return $this->invitationRepository->findOutstandingByCode(code: $code); }
    public function findInvitationByCodeAndEmail(string $code, string $email): ?CampaignInvitation { return $this->invitationRepository->findOutstandingByCodeAndEmail(code: $code, email: $email); }
    public function getOutstandingInvitationsForCampaign(Campaign $campaign): array { return $this->invitationRepository->findOutstandingForCampaign(campaign: $campaign); }
    public function getOutstandingInvitationsForUser(User $user): array { return $this->invitationRepository->findOutstandingForUser(user: $user); }
    public function findInvitationsByEmail(string $email): array { return $this->invitationRepository->findByEmail(email: $email); }
    public function getExpiredInvitations(): array { return $this->invitationRepository->findExpired(); }

    public function deleteInvitation(CampaignInvitation $invitation, bool $flush = true): void
    {
        $this->entityManager->remove($invitation);
        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function redeemInvitation(CampaignInvitation $invitation, User $user): CampaignMembership
    {
        $membership = new CampaignMembership(campaign: $invitation->getCampaign(), user: $user, type: CampaignMembershipTypeEnum::PLAYER);
        $invitation->setUser(user: $user);
        $invitation->markUsed();
        $invitation->getCampaign()->addMembership(membership: $membership);
        $user->addCampaignMembership(membership: $membership);
        $this->entityManager->persist($membership);
        $this->entityManager->persist($invitation);
        $this->entityManager->flush();

        return $membership;
    }
}
