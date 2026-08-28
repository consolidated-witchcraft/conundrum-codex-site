<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Character;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
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

test('character defaults to null', function () {
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
    );

    self::assertNull(actual: $membership->getCharacter());
});

test('set character updates character', function () {
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $character = $this->createStub(Character::class);

    $membership->setCharacter(character: $character);

    self::assertSame(expected: $character, actual: $membership->getCharacter());
});

test('remove character removes character', function () {
    $character = $this->createStub(Character::class);
    $membership = new CampaignMembership(
        campaign: $this->createStub(Campaign::class),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
        character: $character,
    );

    $membership->removeCharacter();

    self::assertNull(actual: $membership->getCharacter());
});
