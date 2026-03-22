<?php

declare(strict_types=1);
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameSession;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeGame = function (): Game {
        return new Game(
            name: 'Test Game',
            slug: 'test-game',
            description: 'A game description',
            gamesMaster: $this->createStub(User::class)
        );
    };
});

test('it exposes and updates state', function () {
    $gamesMaster = $this->createStub(User::class);
    $game = new Game(
        name: 'Test Game',
        slug: 'test-game',
        description: 'Initial description',
        gamesMaster: $gamesMaster
    );

    self::assertSame('Test Game', $game->getName());
    self::assertSame('test-game', $game->getSlug());
    self::assertSame('Initial description', $game->getDescription());
    self::assertSame($gamesMaster, $game->getGamesMaster());
    self::assertNull($game->getHeroImageFile());

    $newGamesMaster = $this->createStub(User::class);
    $heroImage = $this->createStub(ManagedImage::class);

    $game->setName('Updated Game');
    $game->setSlug('updated-game');
    $game->setDescription('Updated description');
    $game->setGamesMaster($newGamesMaster);
    $game->setHeroImageFile($heroImage);

    self::assertSame('Updated Game', $game->getName());
    self::assertSame('updated-game', $game->getSlug());
    self::assertSame('Updated description', $game->getDescription());
    self::assertSame($newGamesMaster, $game->getGamesMaster());
    self::assertSame($heroImage, $game->getHeroImageFile());
});

test('players can be added and removed', function () {
    $game = ($this->makeGame)();
    $playerA = $this->createStub(User::class);
    $playerB = $this->createStub(User::class);

    $game->addPlayer($playerA);
    $game->addPlayer($playerA);
    $game->addPlayer($playerB);

    self::assertCount(2, $game->getPlayers());

    $game->removePlayer($playerA);
    self::assertCount(1, $game->getPlayers());
    self::assertTrue($game->getPlayers()->contains($playerB));
});

test('archive lifecycle', function () {
    $game = ($this->makeGame)();

    self::assertFalse($game->isArchived());
    self::assertNull($game->getArchivedDate());

    $game->archive();

    self::assertTrue($game->isArchived());
    self::assertNotNull($game->getArchivedDate());

    $game->unArchive();

    self::assertFalse($game->isArchived());
    self::assertNull($game->getArchivedDate());
});

test('puzzle instance collections', function () {
    $game = ($this->makeGame)();

    $dynamicPublished = $this->createStub(PuzzleInstanceInterface::class);
    $dynamicPublished->method('isPublished')->willReturn(true);

    $dynamicUnpublished = $this->createStub(PuzzleInstanceInterface::class);
    $dynamicUnpublished->method('isPublished')->willReturn(false);

    $game->getDynamicPuzzleInstances()->add($dynamicPublished);
    $game->getDynamicPuzzleInstances()->add($dynamicUnpublished);

    $staticPublished = $this->createStub(StaticPuzzleInstanceInterface::class);
    $staticPublished->method('isPublished')->willReturn(true);

    $game->setStaticPuzzleInstances(new ArrayCollection([$staticPublished]));

    $allInstances = $game->getPuzzleInstances();
    self::assertCount(3, $allInstances);
    self::assertTrue($allInstances->contains($dynamicPublished));
    self::assertTrue($allInstances->contains($dynamicUnpublished));
    self::assertTrue($allInstances->contains($staticPublished));

    $published = $game->getPublishedPuzzleInstances();
    self::assertCount(2, $published);
    self::assertTrue($published->contains($dynamicPublished));
    self::assertTrue($published->contains($staticPublished));
    self::assertFalse($published->contains($dynamicUnpublished));
});

test('adding game session sets owning side', function () {
    $game = ($this->makeGame)();
    $otherGame = ($this->makeGame)();
    $session = new GameSession(
        sessionDate: new \DateTimeImmutable('2024-01-01 10:00:00'),
        game: $otherGame,
        title: 'Session 1',
        description: 'Intro session'
    );

    $game->addGameSession($session);

    self::assertTrue($game->getGameSessions()->contains($session));
    self::assertSame($game, $session->getGame());
});

test('upcoming game sessions filters past sessions', function () {
    $game = ($this->makeGame)();
    $pastSession = new GameSession(
        sessionDate: new \DateTimeImmutable('2000-01-01 10:00:00'),
        game: $game,
        title: 'Past Session',
        description: 'Already happened'
    );
    $futureSession = new GameSession(
        sessionDate: new \DateTimeImmutable('2999-01-01 10:00:00'),
        game: $game,
        title: 'Future Session',
        description: 'Coming up'
    );

    $game->addGameSession($pastSession);
    $game->addGameSession($futureSession);

    $upcoming = $game->getUpcomingGameSessions();
    self::assertCount(1, $upcoming);
    self::assertTrue($upcoming->contains($futureSession));
    self::assertFalse($upcoming->contains($pastSession));
});
