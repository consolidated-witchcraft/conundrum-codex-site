<?php

declare(strict_types=1);

use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;

test('construct and setters update state', function () {
    $game = $this->createStub(Game::class);
    $updatedGame = $this->createStub(Game::class);
    $config = [new StringField('code', 'Code', 'LoremIpsum')];
    $publicationDate = new DateTimeImmutable('2026-03-16 12:00:00');
    $puzzleInstance = new PuzzleInstance(
        instanceCode: 'INSTANCE-1',
        name: 'Puzzle Instance',
        description: 'Puzzle description',
        game: $game,
        templateSlug: 'test-template',
        publicationDate: $publicationDate,
        config: $config,
        id: 29,
    );

    self::assertSame('INSTANCE-1', $puzzleInstance->getInstanceCode());
    self::assertSame('Puzzle Instance', $puzzleInstance->getName());
    self::assertSame('Puzzle description', $puzzleInstance->getDescription());
    self::assertSame($game, $puzzleInstance->getGame());
    self::assertSame('test-template', $puzzleInstance->getTemplateSlug());
    self::assertSame($publicationDate, $puzzleInstance->getPublicationDate());
    self::assertSame($config, $puzzleInstance->getConfig());
    self::assertSame('LoremIpsum', $puzzleInstance->getConfigValue('code'));
    self::assertSame(29, $puzzleInstance->getId());

    $puzzleInstance->setInstanceCode('INSTANCE-2');
    $puzzleInstance->setName('Updated Puzzle');
    $puzzleInstance->setDescription('Updated description');
    $puzzleInstance->setGame($updatedGame);
    $puzzleInstance->setConfig([]);

    self::assertSame('INSTANCE-2', $puzzleInstance->getInstanceCode());
    self::assertSame('Updated Puzzle', $puzzleInstance->getName());
    self::assertSame('Updated description', $puzzleInstance->getDescription());
    self::assertSame($updatedGame, $puzzleInstance->getGame());
    self::assertSame([], $puzzleInstance->getConfig());
    self::assertNull($puzzleInstance->getConfigValue('code'));
    self::assertTrue($puzzleInstance->isPublished());
});

test('template must be mapped before access', function () {
    $puzzleInstance = new PuzzleInstance(
        instanceCode: 'INSTANCE-1',
        name: 'Puzzle Instance',
        description: 'Puzzle description',
        game: $this->createStub(Game::class),
        templateSlug: 'test-template',
    );

    $this->expectException(PuzzleTemplateNotMappedException::class);

    $puzzleInstance->getTemplate();
});

test('template can be set and returned', function () {
    $template = $this->createStub(PuzzleTemplate::class);
    $puzzleInstance = new PuzzleInstance(
        instanceCode: 'INSTANCE-1',
        name: 'Puzzle Instance',
        description: 'Puzzle description',
        game: $this->createStub(Game::class),
        templateSlug: 'test-template',
    );

    $puzzleInstance->setTemplate($template);

    self::assertSame($template, $puzzleInstance->getTemplate());
});
