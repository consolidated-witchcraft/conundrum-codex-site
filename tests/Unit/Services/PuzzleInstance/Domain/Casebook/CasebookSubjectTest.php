<?php

declare(strict_types=1);

use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectNote;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeCasebook = function (): Casebook {
        return new Casebook(
            name: 'Casebook',
            slug: 'casebook',
            brief: 'Brief',
        );
    };
});

test('construct and setters update state', function () {
    $casebook = ($this->makeCasebook)();
    $updatedCasebook = ($this->makeCasebook)();
    $type = new CasebookSubjectType('Suspect', 'suspect');
    $updatedType = new CasebookSubjectType('Location', 'location');
    $revealedClue = $this->createConfiguredStub(CasebookSubjectClue::class, [
        'getRevealedDate' => new DateTimeImmutable('2026-03-16 12:00:00'),
    ]);
    $hiddenClue = $this->createConfiguredStub(CasebookSubjectClue::class, [
        'getRevealedDate' => null,
    ]);
    $notes = new ArrayCollection([$this->createStub(CasebookSubjectNote::class)]);
    $image = $this->createStub(ManagedImage::class);
    $subject = new CasebookSubject(
        name: 'The Alchemist',
        description: 'A suspicious figure.',
        casebook: $casebook,
        casebookSubjectType: $type,
        casebookSubjectClues: new ArrayCollection([$revealedClue, $hiddenClue]),
        casebookSubjectNotes: $notes,
        id: 16,
    );

    self::assertSame('The Alchemist', $subject->getName());
    self::assertSame('A suspicious figure.', $subject->getDescription());
    self::assertSame($casebook, $subject->getCasebook());
    self::assertSame($type, $subject->getCasebookSubjectType());
    self::assertCount(2, $subject->getCasebookSubjectClues());
    self::assertCount(1, $subject->getRevealedCasebookSubjectClues());
    self::assertSame($notes, $subject->getCasebookSubjectNotes());
    self::assertNull($subject->getRevealedDate());
    self::assertSame(16, $subject->getId());

    $subject->setName('Updated Name');
    $subject->setDescription('Updated description.');
    $subject->setCasebook($updatedCasebook);
    $subject->setCasebookSubjectType($updatedType);
    $subject->setCasebookSubjectNotes(new ArrayCollection());
    $subject->setCasebookSubjectImageFile($image);
    $subject->markRevealed();

    self::assertSame('Updated Name', $subject->getName());
    self::assertSame('Updated description.', $subject->getDescription());
    self::assertSame($updatedCasebook, $subject->getCasebook());
    self::assertSame($updatedType, $subject->getCasebookSubjectType());
    self::assertCount(0, $subject->getCasebookSubjectNotes());
    self::assertSame($image, $subject->getCasebookSubjectImageFile());
    self::assertTrue($subject->isRevealed());

    $subject->unreveal();
    self::assertFalse($subject->isRevealed());
});
