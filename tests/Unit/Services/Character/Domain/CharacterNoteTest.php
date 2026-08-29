<?php

declare(strict_types=1);

use App\Services\Character\Domain\Character;
use App\Services\Character\Domain\CharacterNote;

test('construct', function () {
    $character = $this->createStub(Character::class);
    $characterNote = new CharacterNote(
        character: $character,
        subject: 'The observatory',
        body: 'The western door is sealed.',
        id: 42,
    );

    self::assertSame(expected: $character, actual: $characterNote->getCharacter());
    self::assertSame(expected: 'The observatory', actual: $characterNote->getSubject());
    self::assertSame(expected: 'The western door is sealed.', actual: $characterNote->getBody());
    self::assertSame(expected: 42, actual: $characterNote->getId());
});

test('set character updates character', function () {
    $characterNote = new CharacterNote(
        character: $this->createStub(Character::class),
        subject: 'The observatory',
        body: 'The western door is sealed.',
    );
    $character = $this->createStub(Character::class);

    $characterNote->setCharacter(character: $character);

    self::assertSame(expected: $character, actual: $characterNote->getCharacter());
});

test('set subject updates subject', function () {
    $characterNote = new CharacterNote(
        character: $this->createStub(Character::class),
        subject: 'The observatory',
        body: 'The western door is sealed.',
    );

    $characterNote->setSubject(subject: 'The western door');

    self::assertSame(expected: 'The western door', actual: $characterNote->getSubject());
});

test('set body updates body', function () {
    $characterNote = new CharacterNote(
        character: $this->createStub(Character::class),
        subject: 'The observatory',
        body: 'The western door is sealed.',
    );

    $characterNote->setBody(body: 'The seal has been broken.');

    self::assertSame(expected: 'The seal has been broken.', actual: $characterNote->getBody());
});
