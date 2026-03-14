<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarSettings;

test('construct', function () {
    $creator = $this->createStub(User::class);

    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $creator,
    );

    self::assertSame('Test World', $world->getName());
    self::assertSame('A test world', $world->getDescription());
    self::assertSame('test-world', $world->getSlug());
    self::assertSame($creator, $world->getCreator());
    self::assertInstanceOf(WorldCalendarSettings::class, $world->getWorldCalendarSettings());
});

test('set world calendar settings', function () {
    $creator = $this->createStub(User::class);
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $creator,
    );
    $settings = new WorldCalendarSettings($world);

    $world->setWorldCalendarSettings($settings);

    self::assertSame($settings, $world->getWorldCalendarSettings());
    self::assertSame($world, $settings->getWorld());
});
