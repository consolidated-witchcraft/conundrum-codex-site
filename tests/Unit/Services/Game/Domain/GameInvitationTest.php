<?php

declare(strict_types=1);

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeInvitation = function (array $overrides = []): GameInvitation {
        return new GameInvitation(
            invitationCode: $overrides['invitationCode'] ?? 'INV-123',
            email: $overrides['email'] ?? 'invitee@example.com',
            invitationMessage: $overrides['invitationMessage'] ?? 'Join the game',
            game: $overrides['game'] ?? $this->createStub(Game::class),
            expiresAt: $overrides['expiresAt'] ?? new DateTimeImmutable('2026-03-20'),
            user: $overrides['user'] ?? null,
            dateUsed: $overrides['dateUsed'] ?? null,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $game = $this->createStub(Game::class);
    $user = $this->createStub(User::class);
    $expiresAt = new DateTimeImmutable('2026-03-20');
    $dateUsed = new DateTimeImmutable('2026-03-18');

    $invitation = ($this->makeInvitation)([
        'game' => $game,
        'user' => $user,
        'expiresAt' => $expiresAt,
        'dateUsed' => $dateUsed,
        'id' => 12,
    ]);

    self::assertSame('INV-123', $invitation->getInvitationCode());
    self::assertSame('invitee@example.com', $invitation->getEmail());
    self::assertSame('Join the game', $invitation->getInvitationMessage());
    self::assertSame($game, $invitation->getGame());
    self::assertSame($user, $invitation->getUser());
    self::assertSame($expiresAt, $invitation->getExpiresAt());
    self::assertTrue($invitation->isUsed());
    self::assertSame(12, $invitation->getId());
});
