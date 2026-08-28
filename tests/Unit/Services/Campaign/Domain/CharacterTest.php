<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Character;
use App\Services\ManagedFiles\Domain\ManagedFile;

test('construct', function () {
    $membership = $this->createStub(CampaignMembership::class);
    $avatar = $this->createStub(ManagedFile::class);
    $characterSheet = $this->createStub(ManagedFile::class);

    $character = new Character(
        campaignMembership: $membership,
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $avatar,
        characterSheet: $characterSheet,
        id: 42,
    );

    self::assertSame(expected: $membership, actual: $character->getCampaignMembership());
    self::assertSame(expected: 'Seraphina Vale', actual: $character->getName());
    self::assertSame(
        expected: 'A scholar of forgotten constellations.',
        actual: $character->getDescription(),
    );
    self::assertSame(expected: $avatar, actual: $character->getAvatar());
    self::assertSame(expected: $characterSheet, actual: $character->getCharacterSheet());
    self::assertSame(expected: 42, actual: $character->getId());
});

test('character sheet defaults to null', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
    );

    self::assertNull(actual: $character->getCharacterSheet());
});

test('set name updates name', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
    );

    $character->setName(name: 'Seraphina North');

    self::assertSame(expected: 'Seraphina North', actual: $character->getName());
});

test('set description updates description', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
    );

    $character->setDescription(description: 'Keeper of the celestial archive.');

    self::assertSame(expected: 'Keeper of the celestial archive.', actual: $character->getDescription());
});

test('set avatar updates avatar', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
    );
    $avatar = $this->createStub(ManagedFile::class);

    $character->setAvatar(avatar: $avatar);

    self::assertSame(expected: $avatar, actual: $character->getAvatar());
});

test('set character sheet updates character sheet', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
    );
    $characterSheet = $this->createStub(ManagedFile::class);

    $character->setCharacterSheet(characterSheet: $characterSheet);

    self::assertSame(expected: $characterSheet, actual: $character->getCharacterSheet());
});

test('set character sheet removes character sheet', function () {
    $character = new Character(
        campaignMembership: $this->createStub(CampaignMembership::class),
        name: 'Seraphina Vale',
        description: 'A scholar of forgotten constellations.',
        avatar: $this->createStub(ManagedFile::class),
        characterSheet: $this->createStub(ManagedFile::class),
    );

    $character->setCharacterSheet(characterSheet: null);

    self::assertNull(actual: $character->getCharacterSheet());
});
