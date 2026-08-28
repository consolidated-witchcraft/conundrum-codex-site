<?php

declare(strict_types=1);

use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;
use App\Services\PuzzleTemplate\Domain\Exceptions\MismappedPuzzleTemplateException;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeTemplate = function (string $slug): PuzzleTemplate {
        return new PuzzleTemplate(
            slug: $slug,
            title: 'Casebook Template',
            hidden: false,
            createdAt: new DateTimeImmutable('2026-03-16 12:00:00'),
            description: 'Template description',
            categories: new ArrayCollection(),
            authorEmail: 'author@example.com',
            static: true,
            elementName: 'casebook-element',
            staticCreateRoute: 'create_route',
            staticEditRoute: 'edit_route',
            staticPlayRoute: 'play_route',
        );
    };
});

test('construct and setters update state', function () {
    $game = $this->createStub(Game::class);
    $updatedGame = $this->createStub(Game::class);
    $casebook = new Casebook(
        name: 'Casebook',
        slug: 'casebook-1',
        game: $game,
        brief: 'Initial brief',
        id: 20,
    );
    $suspectType = new CasebookSubjectType('Suspect', 'suspect');
    $locationType = new CasebookSubjectType('Location', 'location');
    $subjectOne = new CasebookSubject(
        name: 'The Alchemist',
        description: 'A suspicious figure.',
        casebook: $casebook,
        casebookSubjectType: $suspectType,
        casebookSubjectClues: new ArrayCollection(),
        casebookSubjectNotes: new ArrayCollection(),
    );
    $subjectTwo = new CasebookSubject(
        name: 'Baker',
        description: 'A witness.',
        casebook: $casebook,
        casebookSubjectType: $suspectType,
        casebookSubjectClues: new ArrayCollection(),
        casebookSubjectNotes: new ArrayCollection(),
    );
    $subjectThree = new CasebookSubject(
        name: 'Abbey',
        description: 'A location.',
        casebook: $casebook,
        casebookSubjectType: $locationType,
        casebookSubjectClues: new ArrayCollection(),
        casebookSubjectNotes: new ArrayCollection(),
    );

    $casebook->addCasebookSubject($subjectOne)
        ->addCasebookSubject($subjectTwo)
        ->addCasebookSubject($subjectThree);

    self::assertSame('casebook-1', $casebook->getSlug());
    self::assertSame('Initial brief', $casebook->getBrief());
    self::assertSame('Casebook', $casebook->getName());
    self::assertSame($game, $casebook->getGame());
    self::assertSame('casebook-1', $casebook->getInstanceCode());
    self::assertSame('Initial brief', $casebook->getDescription());
    self::assertSame(Casebook::TEMPLATE_SLUG, $casebook->getTemplateSlug());
    self::assertCount(3, $casebook->getCasebookSubjects());
    self::assertSame(20, $casebook->getId());

    $casebook->setSlug('casebook-2');
    $casebook->setBrief('Updated brief');
    $casebook->setName('Updated Casebook');
    $casebook->setInstanceCode('casebook-3');
    $casebook->setGame($updatedGame);
    $casebook->setPublicationDate(new DateTimeImmutable('2026-03-15 12:00:00'));

    self::assertSame('casebook-3', $casebook->getSlug());
    self::assertSame('Updated brief', $casebook->getBrief());
    self::assertSame('Updated Casebook', $casebook->getName());
    self::assertSame($updatedGame, $casebook->getGame());
    self::assertTrue($casebook->isPublished());
    self::assertSame(['Baker', 'The Alchemist'], array_values(array_map(
        static fn (CasebookSubject $subject): string => $subject->getName(),
        $casebook->getSubjectsByTypeHandle('suspect')->toArray()
    )));
    self::assertSame(['The Alchemist'], array_values(array_map(
        static fn (CasebookSubject $subject): string => $subject->getName(),
        $casebook->getSubjectsByTypeHandleAndInitial('suspect', 'a')->toArray()
    )));
});

test('template access requires correct mapping', function () {
    $casebook = new Casebook(
        name: 'Casebook',
        slug: 'casebook-1',
        game: $this->createStub(Game::class),
        brief: 'Initial brief',
    );

    $this->expectException(PuzzleTemplateNotMappedException::class);

    $casebook->getTemplate();
});

test('mismapped template is rejected', function () {
    $casebook = new Casebook(
        name: 'Casebook',
        slug: 'casebook-1',
        game: $this->createStub(Game::class),
        brief: 'Initial brief',
    );

    $this->expectException(MismappedPuzzleTemplateException::class);

    $casebook->setTemplate(($this->makeTemplate)('other-template'));
});

test('casebook template can be set and returned', function () {
    $casebook = new Casebook(
        name: 'Casebook',
        slug: 'casebook-1',
        game: $this->createStub(Game::class),
        brief: 'Initial brief',
    );
    $template = ($this->makeTemplate)('casebook');

    $casebook->setTemplate($template);

    self::assertSame($template, $casebook->getTemplate());
});
