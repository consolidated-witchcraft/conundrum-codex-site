<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Domain\Exceptions\DuplicateCampaignCharacterException;
use App\Services\Character\Domain\Character;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\User;

test('construct', function () {
    $campaign = $this->createStub(Campaign::class);
    $user = $this->createStub(User::class);

    $membership = new CampaignMembership(
        campaign: $campaign,
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
        id: 42,
    );

    self::assertSame(expected: $campaign, actual: $membership->getCampaign());
    self::assertSame(expected: $user, actual: $membership->getUser());
    self::assertSame(expected: CampaignMembershipTypeEnum::PLAYER, actual: $membership->getType());
    self::assertSame(expected: 42, actual: $membership->getId());
});

test('set type updates membership type', function () {
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
    );

    $membership->setType(type: CampaignMembershipTypeEnum::NOTE_TAKER);

    self::assertSame(expected: CampaignMembershipTypeEnum::NOTE_TAKER, actual: $membership->getType());
});

test('campaign characters default to an empty collection', function () {
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
    );

    self::assertCount(expectedCount: 0, haystack: $membership->getCampaignCharacters());
});

test('a character can only be added to a campaign membership once', function () {
    $user = $this->createStub(User::class);
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $character = new Character(
        name: 'Seraphina Vale',
        user: $user,
        class: 'Wizard',
        race: 'Human',
        portrait: $this->createStub(ManagedImage::class),
    );
    $membership->addCharacter(character: $character);

    expect(fn () => $membership->addCharacter(character: $character))
        ->toThrow(DuplicateCampaignCharacterException::class);
});

test('activating a character deactivates the previous active character', function () {
    $user = $this->createStub(User::class);
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $firstCharacter = new Character(
        name: 'Seraphina Vale',
        user: $user,
        class: 'Wizard',
        race: 'Human',
        portrait: $this->createStub(ManagedImage::class),
    );
    $secondCharacter = new Character(
        name: 'Bramble',
        user: $user,
        class: 'Rogue',
        race: 'Halfling',
        portrait: $this->createStub(ManagedImage::class),
    );
    $firstCampaignCharacter = $membership->addCharacter(character: $firstCharacter, active: true);
    $secondCampaignCharacter = $membership->addCharacter(character: $secondCharacter);

    $membership->activateCharacter(campaignCharacter: $secondCampaignCharacter);

    self::assertFalse(condition: $firstCampaignCharacter->isActive());
    self::assertTrue(condition: $secondCampaignCharacter->isActive());
});

test('different users may each have an active character in one campaign', function () {
    $campaign = $this->createStub(Campaign::class);
    $firstUser = $this->createStub(User::class);
    $secondUser = $this->createStub(User::class);
    $firstMembership = new CampaignMembership(
        campaign: $campaign,
        user: $firstUser,
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $secondMembership = new CampaignMembership(
        campaign: $campaign,
        user: $secondUser,
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $firstCharacter = new Character(
        name: 'Seraphina Vale',
        user: $firstUser,
        class: 'Wizard',
        race: 'Human',
        portrait: $this->createStub(ManagedImage::class),
    );
    $secondCharacter = new Character(
        name: 'Bramble',
        user: $secondUser,
        class: 'Rogue',
        race: 'Halfling',
        portrait: $this->createStub(ManagedImage::class),
    );

    $firstCampaignCharacter = $firstMembership->addCharacter(character: $firstCharacter, active: true);
    $secondCampaignCharacter = $secondMembership->addCharacter(character: $secondCharacter, active: true);

    self::assertTrue(condition: $firstCampaignCharacter->isActive());
    self::assertTrue(condition: $secondCampaignCharacter->isActive());
});
