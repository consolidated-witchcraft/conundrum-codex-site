<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Enums\CampaignStatusEnum;

test('construct', function () {
    $startDate = new DateTimeImmutable(datetime: '2026-07-18 10:00:00');

    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
        status: CampaignStatusEnum::ACTIVE,
        startDate: $startDate,
        id: 42,
    );

    self::assertSame(expected: 'The Lost Observatory', actual: $campaign->getName());
    self::assertSame(expected: 'the-lost-observatory', actual: $campaign->getSlug());
    self::assertSame(expected: 'A campaign among the stars.', actual: $campaign->getDescription());
    self::assertSame(expected: CampaignStatusEnum::ACTIVE, actual: $campaign->getStatus());
    self::assertSame(expected: $startDate, actual: $campaign->getStartDate());
    self::assertSame(expected: 42, actual: $campaign->getId());
});

test('status defaults to draft', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );

    self::assertSame(expected: CampaignStatusEnum::DRAFT, actual: $campaign->getStatus());
});

test('memberships default to an empty collection', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );

    self::assertCount(expectedCount: 0, haystack: $campaign->getMemberships());
});

test('add membership adds a membership once', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );
    $membership = $this->createStub(CampaignMembership::class);

    $campaign->addMembership(membership: $membership);
    $campaign->addMembership(membership: $membership);

    self::assertSame(expected: [$membership], actual: $campaign->getMemberships()->toArray());
});

test('remove membership removes a membership', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );
    $membership = $this->createStub(CampaignMembership::class);
    $campaign->addMembership(membership: $membership);

    $campaign->removeMembership(membership: $membership);

    self::assertCount(expectedCount: 0, haystack: $campaign->getMemberships());
});

test('set name updates name', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );

    $campaign->setName(name: 'The Found Observatory');

    self::assertSame(expected: 'The Found Observatory', actual: $campaign->getName());
});

test('set description updates description', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );

    $campaign->setDescription(description: 'A completed campaign among the stars.');

    self::assertSame(
        expected: 'A completed campaign among the stars.',
        actual: $campaign->getDescription(),
    );
});

test('mark started activates campaign and records start date', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
    );
    $earliestStartDate = new DateTimeImmutable();

    $campaign->markStarted();

    $latestStartDate = new DateTimeImmutable();
    self::assertSame(expected: CampaignStatusEnum::ACTIVE, actual: $campaign->getStatus());
    self::assertGreaterThanOrEqual(expected: $earliestStartDate, actual: $campaign->getStartDate());
    self::assertLessThanOrEqual(expected: $latestStartDate, actual: $campaign->getStartDate());
});

test('mark draft changes status to draft', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
        status: CampaignStatusEnum::ACTIVE,
    );

    $campaign->markDraft();

    self::assertSame(expected: CampaignStatusEnum::DRAFT, actual: $campaign->getStatus());
});

test('mark completed changes status to completed', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
        status: CampaignStatusEnum::ACTIVE,
    );

    $campaign->markCompleted();

    self::assertSame(expected: CampaignStatusEnum::COMPLETED, actual: $campaign->getStatus());
});

test('mark archived changes status to archived', function () {
    $campaign = new Campaign(
        name: 'The Lost Observatory',
        slug: 'the-lost-observatory',
        description: 'A campaign among the stars.',
        status: CampaignStatusEnum::COMPLETED,
    );

    $campaign->markArchived();

    self::assertSame(expected: CampaignStatusEnum::ARCHIVED, actual: $campaign->getStatus());
});
