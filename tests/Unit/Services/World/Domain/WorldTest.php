<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\InvalidWorldEventDescriptionLengthException;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTimestampException;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTitleLengthException;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldEvent;
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
    self::assertCount(0, $world->getWorldEvents());
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

test('add world event', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );
    $worldEvent = new WorldEvent(
        world: $world,
        timestamp: 123456789,
        title: 'A world event',
        description: 'A detailed world event description.',
    );

    $world->addWorldEvent($worldEvent);

    self::assertCount(1, $world->getWorldEvents());
    self::assertTrue($world->getWorldEvents()->contains($worldEvent));
    self::assertSame($world, $worldEvent->getWorld());
    self::assertSame(123456789, $worldEvent->getTimestamp());
    self::assertSame('A world event', $worldEvent->getTitle());
    self::assertSame('A detailed world event description.', $worldEvent->getDescription());
});

test('set world event properties', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );
    $worldEvent = new WorldEvent(
        world: $world,
        timestamp: 123456789,
        title: 'A world event',
        description: 'A detailed world event description.',
    );

    $worldEvent->setTimestamp(987654321);
    $worldEvent->setTitle('An updated world event');
    $worldEvent->setDescription('An updated detailed world event description.');

    self::assertSame(987654321, $worldEvent->getTimestamp());
    self::assertSame('An updated world event', $worldEvent->getTitle());
    self::assertSame('An updated detailed world event description.', $worldEvent->getDescription());
});

test('world event rejects non-positive timestamp', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );

    $this->expectException(InvalidWorldEventTimestampException::class);

    new WorldEvent(
        world: $world,
        timestamp: 0,
        title: 'A world event',
        description: 'A detailed world event description.',
    );
});

test('world event rejects blank title', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );

    $this->expectException(InvalidWorldEventTitleLengthException::class);

    new WorldEvent(
        world: $world,
        timestamp: 123456789,
        title: '   ',
        description: 'A detailed world event description.',
    );
});

test('world event rejects blank description', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );

    $this->expectException(InvalidWorldEventDescriptionLengthException::class);

    new WorldEvent(
        world: $world,
        timestamp: 123456789,
        title: 'A world event',
        description: '   ',
    );
});

test('world event rejects too-long description', function () {
    $world = new World(
        name: 'Test World',
        description: 'A test world',
        slug: 'test-world',
        creator: $this->createStub(User::class),
    );

    $this->expectException(InvalidWorldEventDescriptionLengthException::class);

    new WorldEvent(
        world: $world,
        timestamp: 123456789,
        title: 'A world event',
        description: str_repeat('a', 5001),
    );
});
