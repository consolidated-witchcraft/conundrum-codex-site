<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignCharacter;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Domain\Exceptions\CharacterOwnerDoesNotMatchCampaignMembershipException;
use App\Services\Character\Domain\Character;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\User;

beforeEach(function () {
    $this->makeCampaign = static fn (string $slug): Campaign => new Campaign(
        name: 'The Lost Observatory',
        slug: $slug,
        description: 'A campaign among the stars.',
    );

    $this->makeCharacter = function (User $user): Character {
        return new Character(
            name: 'Seraphina Vale',
            user: $user,
            class: 'Wizard',
            race: 'Human',
            portrait: $this->createStub(ManagedImage::class),
        );
    };
});

test('construct associates a character with a campaign membership', function () {
    $user = $this->createStub(User::class);
    $character = ($this->makeCharacter)($user);
    $membership = new CampaignMembership(
        campaign: ($this->makeCampaign)('the-lost-observatory'),
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
    );

    $campaignCharacter = new CampaignCharacter(
        campaignMembership: $membership,
        character: $character,
        id: 42,
    );

    self::assertSame(expected: $membership, actual: $campaignCharacter->getCampaignMembership());
    self::assertSame(expected: $character, actual: $campaignCharacter->getCharacter());
    self::assertFalse(condition: $campaignCharacter->isActive());
    self::assertSame(expected: 42, actual: $campaignCharacter->getId());
    self::assertTrue(condition: $membership->getCampaignCharacters()->contains($campaignCharacter));
    self::assertTrue(condition: $character->getCampaignCharacters()->contains($campaignCharacter));
});

test('construct rejects a character owned by another user', function () {
    $membership = new CampaignMembership(
        campaign: ($this->makeCampaign)('the-lost-observatory'),
        user: $this->createStub(User::class),
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $character = ($this->makeCharacter)($this->createStub(User::class));

    expect(fn () => new CampaignCharacter(
        campaignMembership: $membership,
        character: $character,
    ))->toThrow(CharacterOwnerDoesNotMatchCampaignMembershipException::class);
});

test('a character may belong to several campaigns', function () {
    $user = $this->createStub(User::class);
    $character = ($this->makeCharacter)($user);
    $firstMembership = new CampaignMembership(
        campaign: ($this->makeCampaign)('the-lost-observatory'),
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
    );
    $secondMembership = new CampaignMembership(
        campaign: ($this->makeCampaign)('the-sunken-library'),
        user: $user,
        type: CampaignMembershipTypeEnum::PLAYER,
    );

    new CampaignCharacter(campaignMembership: $firstMembership, character: $character);
    new CampaignCharacter(campaignMembership: $secondMembership, character: $character);

    self::assertCount(expectedCount: 2, haystack: $character->getCampaignCharacters());
});
