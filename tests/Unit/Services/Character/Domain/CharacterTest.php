<?php

declare(strict_types=1);

use App\Services\Character\Domain\Character;
use App\Services\Character\Domain\CharacterNote;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeCharacter = function (): Character {
        return new Character(
            name: 'Seraphina Vale',
            user: $this->createStub(User::class),
            class: 'Wizard',
            race: 'Human',
            portrait: $this->createStub(ManagedImage::class),
        );
    };
});

test('construct', function () {
    $user = $this->createStub(User::class);
    $portrait = $this->createStub(ManagedImage::class);

    $character = new Character(
        name: 'Seraphina Vale',
        user: $user,
        class: 'Wizard',
        race: 'Human',
        portrait: $portrait,
        biography: 'Keeper of the celestial archive.',
        id: 42,
    );

    self::assertSame(expected: 'Seraphina Vale', actual: $character->getName());
    self::assertSame(expected: $user, actual: $character->getUser());
    self::assertSame(expected: 'Wizard', actual: $character->getClass());
    self::assertSame(expected: 'Human', actual: $character->getRace());
    self::assertSame(expected: $portrait, actual: $character->getPortrait());
    self::assertFalse(condition: $character->isNpc());
    self::assertSame(expected: 'Keeper of the celestial archive.', actual: $character->getBiography());
    self::assertSame(expected: 42, actual: $character->getId());
    self::assertCount(expectedCount: 0, haystack: $character->getCharacterNotes());
});

test('biography defaults to null', function () {
    $character = ($this->makeCharacter)();

    self::assertNull(actual: $character->getBiography());
});

test('set name updates name', function () {
    $character = ($this->makeCharacter)();

    $character->setName(name: 'Seraphina North');

    self::assertSame(expected: 'Seraphina North', actual: $character->getName());
});

test('set user updates user', function () {
    $character = ($this->makeCharacter)();
    $user = $this->createStub(User::class);

    $character->setUser(user: $user);

    self::assertSame(expected: $user, actual: $character->getUser());
});

test('set class updates class', function () {
    $character = ($this->makeCharacter)();

    $character->setClass(class: 'Chronurgy Wizard');

    self::assertSame(expected: 'Chronurgy Wizard', actual: $character->getClass());
});

test('set race updates race', function () {
    $character = ($this->makeCharacter)();

    $character->setRace(race: 'High Elf');

    self::assertSame(expected: 'High Elf', actual: $character->getRace());
});

test('mark as npc marks character as an npc', function () {
    $character = ($this->makeCharacter)();

    $character->markAsNpc();

    self::assertTrue(condition: $character->isNpc());
});

test('unmark as npc marks character as a player character', function () {
    $character = ($this->makeCharacter)();
    $character->markAsNpc();

    $character->unmarkAsNpc();

    self::assertFalse(condition: $character->isNpc());
});

test('set portrait updates portrait', function () {
    $character = ($this->makeCharacter)();
    $portrait = $this->createStub(ManagedImage::class);

    $character->setPortrait(portrait: $portrait);

    self::assertSame(expected: $portrait, actual: $character->getPortrait());
});

test('set biography updates and removes biography', function () {
    $character = ($this->makeCharacter)();

    $character->setBiography(biography: 'A revised history.');
    self::assertSame(expected: 'A revised history.', actual: $character->getBiography());

    $character->setBiography(biography: null);
    self::assertNull(actual: $character->getBiography());
});

test('add character note adds it once and synchronises its character', function () {
    $character = ($this->makeCharacter)();
    $otherCharacter = ($this->makeCharacter)();
    $characterNote = new CharacterNote(
        character: $otherCharacter,
        subject: 'The observatory',
        body: 'The western door is sealed.',
    );

    $character->addCharacterNote(characterNote: $characterNote);
    $character->addCharacterNote(characterNote: $characterNote);

    self::assertSame(expected: [$characterNote], actual: $character->getCharacterNotes()->toArray());
    self::assertSame(expected: $character, actual: $characterNote->getCharacter());
});

test('remove character note removes it', function () {
    $character = ($this->makeCharacter)();
    $characterNote = new CharacterNote(
        character: $character,
        subject: 'The observatory',
        body: 'The western door is sealed.',
    );
    $character->addCharacterNote(characterNote: $characterNote);

    $character->removeCharacterNote(characterNote: $characterNote);

    self::assertCount(expectedCount: 0, haystack: $character->getCharacterNotes());
});
