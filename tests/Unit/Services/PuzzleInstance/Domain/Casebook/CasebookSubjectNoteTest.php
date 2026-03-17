<?php

declare(strict_types=1);

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectNote;
use App\Services\User\Domain\User;

test('construct and setters update state', function () {
    $subject = $this->createStub(CasebookSubject::class);
    $user = $this->createStub(User::class);
    $note = new CasebookSubjectNote(
        title: 'Observation',
        body: 'Important detail.',
        createdBy: $user,
        casebookSubject: $subject,
        id: 10,
    );

    self::assertSame('Observation', $note->getTitle());
    self::assertSame('Important detail.', $note->getBody());
    self::assertSame($user, $note->getCreatedBy());
    self::assertSame($subject, $note->getCasebookSubject());
    self::assertSame(10, $note->getId());

    $note->setTitle('Updated observation');
    $note->setBody('Updated detail.');

    self::assertSame('Updated observation', $note->getTitle());
    self::assertSame('Updated detail.', $note->getBody());
});
