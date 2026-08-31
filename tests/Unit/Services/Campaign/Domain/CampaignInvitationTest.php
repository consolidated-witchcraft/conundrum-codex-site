<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeCampaignInvitation = function (array $overrides = []): CampaignInvitation {
        return new CampaignInvitation(
            invitationCode: $overrides['invitationCode'] ?? 'campaign-code',
            email: $overrides['email'] ?? 'player@example.com',
            invitationMessage: $overrides['invitationMessage'] ?? 'Join my campaign',
            campaign: $overrides['campaign'] ?? $this->createStub(Campaign::class),
            expiresAt: $overrides['expiresAt'] ?? new DateTimeImmutable('+1 day'),
            user: $overrides['user'] ?? null,
            usedAt: $overrides['usedAt'] ?? null,
            id: $overrides['id'] ?? null,
        );
    };
});

test('constructs a campaign invitation', function () {
    $campaign = $this->createStub(Campaign::class);
    $user = $this->createStub(User::class);
    $expiresAt = new DateTimeImmutable('+1 day');

    $invitation = ($this->makeCampaignInvitation)(overrides: [
        'campaign' => $campaign,
        'user' => $user,
        'expiresAt' => $expiresAt,
        'id' => 42,
    ]);

    self::assertSame(expected: 'campaign-code', actual: $invitation->getInvitationCode());
    self::assertSame(expected: 'player@example.com', actual: $invitation->getEmail());
    self::assertSame(expected: 'Join my campaign', actual: $invitation->getInvitationMessage());
    self::assertSame(expected: $campaign, actual: $invitation->getCampaign());
    self::assertSame(expected: $user, actual: $invitation->getUser());
    self::assertSame(expected: $expiresAt, actual: $invitation->getExpiresAt());
    self::assertSame(expected: 42, actual: $invitation->getId());
});

test('marks an invitation as used', function () {
    $invitation = ($this->makeCampaignInvitation)();

    $invitation->markUsed();

    self::assertTrue(condition: $invitation->isUsed());
});

test('revokes an invitation by expiring it', function () {
    $invitation = ($this->makeCampaignInvitation)();
    $beforeRevocation = new DateTimeImmutable();

    $invitation->revoke();

    self::assertTrue(condition: $invitation->getExpiresAt() <= new DateTimeImmutable());
    self::assertTrue(condition: $invitation->getExpiresAt() >= $beforeRevocation);
});
