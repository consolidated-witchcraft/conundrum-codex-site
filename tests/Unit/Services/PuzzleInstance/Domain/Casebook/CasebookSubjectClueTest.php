<?php

declare(strict_types=1);

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;

test('construct and setters update state', function () {
    $type = new CasebookSubjectClueType('Lead', 'lead');
    $updatedType = new CasebookSubjectClueType('Evidence', 'evidence');
    $subject = $this->createStub(CasebookSubject::class);
    $updatedSubject = $this->createStub(CasebookSubject::class);
    $clue = new CasebookSubjectClue(
        title: 'Broken window',
        body: 'There are signs of forced entry.',
        type: $type,
        casebookSubject: $subject,
        id: 24,
    );

    self::assertSame('Broken window', $clue->getTitle());
    self::assertSame('There are signs of forced entry.', $clue->getBody());
    self::assertSame($type, $clue->getType());
    self::assertSame($subject, $clue->getCasebookSubject());
    self::assertNull($clue->getRevealedDate());
    self::assertSame(24, $clue->getId());

    $clue->setTitle('Updated title');
    $clue->setBody('Updated body.');
    $clue->setType($updatedType);
    $clue->setCasebookSubject($updatedSubject);
    $clue->reveal();

    self::assertSame('Updated title', $clue->getTitle());
    self::assertSame('Updated body.', $clue->getBody());
    self::assertSame($updatedType, $clue->getType());
    self::assertSame($updatedSubject, $clue->getCasebookSubject());
    self::assertInstanceOf(DateTimeImmutable::class, $clue->getRevealedDate());
});
