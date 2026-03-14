<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarSettings;

test('construct', function () {
    $settings = new WorldCalendarSettings(new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    ));

    self::assertInstanceOf(WorldCalendarSettings::class, $settings);
    self::assertSame(24, $settings->getHoursInDay());
});

test('set hours in day', function () {
    $settings = new WorldCalendarSettings(new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    ));

    $settings->setHoursInDay(30);

    self::assertSame(30, $settings->getHoursInDay());
});
