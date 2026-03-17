<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTimestampException;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTitleLengthException;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldEvent;

beforeEach(function () {
    $this->makeWorld = function (): World {
        return new World(
            name: 'Test World',
            description: 'A test world',
            slug: 'test-world',
            creator: $this->createStub(User::class),
        );
    };
});

test('construct and setters update state', function () {
    $world = ($this->makeWorld)();
    $updatedWorld = ($this->makeWorld)();
    $worldEvent = new WorldEvent(
        world: $world,
        timestamp: 123,
        title: 'Arrival',
        description: 'The heroes arrive in town.',
        id: 13,
    );

    self::assertSame($world, $worldEvent->getWorld());
    self::assertSame(123, $worldEvent->getTimestamp());
    self::assertSame('Arrival', $worldEvent->getTitle());
    self::assertSame('The heroes arrive in town.', $worldEvent->getDescription());
    self::assertSame(13, $worldEvent->getId());

    $worldEvent->setWorld($updatedWorld);
    $worldEvent->setTimestamp(456);
    $worldEvent->setTitle('Departure');
    $worldEvent->setDescription('The heroes leave town.');

    self::assertSame($updatedWorld, $worldEvent->getWorld());
    self::assertSame(456, $worldEvent->getTimestamp());
    self::assertSame('Departure', $worldEvent->getTitle());
    self::assertSame('The heroes leave town.', $worldEvent->getDescription());
});

test('invalid values are rejected', function () {
    $world = ($this->makeWorld)();

    $this->expectException(InvalidWorldEventTimestampException::class);

    new WorldEvent(
        world: $world,
        timestamp: 0,
        title: 'Arrival',
        description: 'The heroes arrive in town.',
    );
});

test('blank title is rejected by setter', function () {
    $worldEvent = new WorldEvent(
        world: ($this->makeWorld)(),
        timestamp: 123,
        title: 'Arrival',
        description: 'The heroes arrive in town.',
    );

    $this->expectException(InvalidWorldEventTitleLengthException::class);

    $worldEvent->setTitle('   ');
});
