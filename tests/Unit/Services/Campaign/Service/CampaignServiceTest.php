<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Infrastructure\CampaignInvitationRepository;
use App\Services\Campaign\Infrastructure\CampaignRepository;
use App\Services\Campaign\Service\CampaignService;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

test('redeeming an invitation creates a player membership and consumes the invitation', function () {
    $campaign = new Campaign(name: 'Lost Observatory', slug: 'lost-observatory', description: 'Among the stars.');
    $user = $this->createMock(User::class);
    $invitation = new CampaignInvitation(
        invitationCode: 'campaign-code',
        email: 'player@example.com',
        invitationMessage: 'Join us',
        campaign: $campaign,
        expiresAt: new DateTimeImmutable('+1 day'),
    );
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->expects(self::exactly(2))->method('persist');
    $entityManager->expects(self::once())->method('flush');
    $service = new CampaignService(
        campaignRepository: $this->createMock(CampaignRepository::class),
        invitationRepository: $this->createMock(CampaignInvitationRepository::class),
        entityManager: $entityManager,
    );

    $membership = $service->redeemInvitation(invitation: $invitation, user: $user);

    self::assertSame(expected: $campaign, actual: $membership->getCampaign());
    self::assertSame(expected: $user, actual: $membership->getUser());
    self::assertSame(expected: CampaignMembershipTypeEnum::PLAYER, actual: $membership->getType());
    self::assertTrue(condition: $invitation->isUsed());
    self::assertSame(expected: $user, actual: $invitation->getUser());
    self::assertTrue(condition: $campaign->getMemberships()->contains($membership));
});
