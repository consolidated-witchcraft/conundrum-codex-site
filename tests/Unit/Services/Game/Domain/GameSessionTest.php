<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Game\Domain;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameSession;
use PHPUnit\Framework\TestCase;

final class GameSessionTest extends TestCase
{
    public function testItExposesAndUpdatesState(): void
    {
        $sessionDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $game = $this->createStub(Game::class);

        $session = new GameSession(
            sessionDate: $sessionDate,
            game: $game,
            title: 'Session 1',
            description: 'Intro session'
        );

        self::assertSame($sessionDate, $session->getSessionDate());
        self::assertSame($game, $session->getGame());
        self::assertSame('Session 1', $session->getTitle());
        self::assertSame('Intro session', $session->getDescription());
        self::assertNull($session->getCancellationDate());

        $newDate = new \DateTime('2024-02-01 19:30:00');
        $newGame = $this->createStub(Game::class);

        $session->setSessionDate($newDate);
        $session->setGame($newGame);
        $session->setTitle('Session 2');
        $session->setDescription('Follow-up');

        self::assertSame($newDate, $session->getSessionDate());
        self::assertSame($newGame, $session->getGame());
        self::assertSame('Session 2', $session->getTitle());
        self::assertSame('Follow-up', $session->getDescription());
    }

    public function testCancelSetsCancellationDate(): void
    {
        $sessionDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $game = $this->createStub(Game::class);
        $session = new GameSession(
            sessionDate: $sessionDate,
            game: $game,
            title: 'Session 1',
            description: 'Intro session'
        );

        $beforeCancel = new \DateTimeImmutable();
        $session->cancel();

        $cancellationDate = $session->getCancellationDate();
        self::assertNotNull($cancellationDate);
        self::assertInstanceOf(\DateTimeImmutable::class, $cancellationDate);
        self::assertGreaterThanOrEqual($beforeCancel->getTimestamp(), $cancellationDate->getTimestamp());
    }

    public function testIsCancelled(): void {
        $sessionDate = new \DateTimeImmutable('2024-01-01 10:00:00');
        $game = $this->createStub(Game::class);

        $session = new GameSession(
            sessionDate: $sessionDate,
            game: $game,
            title: 'Session 1',
            description: 'Intro session'
        );
        self::assertFalse($session->isCancelled());
        $session->cancel();
        self::assertTrue($session->isCancelled());
    }
}
