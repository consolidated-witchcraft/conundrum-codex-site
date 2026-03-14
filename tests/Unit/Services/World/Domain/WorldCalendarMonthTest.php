<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\InvalidWorldCalendarMonthNumberOfDaysException;
use App\Services\World\Domain\Exceptions\OverlappingWorldCalendarMonthDeltaException;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarMonth;
use App\Services\World\Domain\WorldCalendarSettings;

beforeEach(function () {
    $this->makeWorld = function (array $overrides = []): World {
        return new World(
            name: $overrides['name'] ?? 'Test World',
            description: $overrides['description'] ?? 'A test world',
            slug: $overrides['slug'] ?? 'test-world',
            creator: $overrides['creator'] ?? $this->createStub(User::class),
        );
    };
});

test('construct', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $month = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    );

    self::assertSame($settings, $month->getWorldCalendarSettings());
    self::assertSame('Hammer', $month->getName());
    self::assertSame('hammer', $month->getAbbreviation());
    self::assertSame(1, $month->getDelta());
    self::assertSame(30, $month->getNumberOfDays());
});

test('setters update state', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $month = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    );

    $month->setName('Anvil');
    $month->setAbbreviation('anvil');
    $month->setDelta(2);
    $month->setNumberOfDays(31);

    self::assertSame('Anvil', $month->getName());
    self::assertSame('anvil', $month->getAbbreviation());
    self::assertSame(2, $month->getDelta());
    self::assertSame(31, $month->getNumberOfDays());
});

test('set delta rejects overlap within settings', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $firstMonth = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    );
    $secondMonth = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Anvil',
        abbreviation: 'anvil',
        delta: 2,
        numberOfDays: 31,
    );

    $settings->addWorldCalendarMonth($firstMonth);
    $settings->addWorldCalendarMonth($secondMonth);

    $this->expectException(OverlappingWorldCalendarMonthDeltaException::class);
    $secondMonth->setDelta(1);
});

test('construct rejects number of days below minimum', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();

    $this->expectException(InvalidWorldCalendarMonthNumberOfDaysException::class);

    new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 0,
    );
});

test('construct rejects number of days above maximum', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();

    $this->expectException(InvalidWorldCalendarMonthNumberOfDaysException::class);

    new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 51,
    );
});

test('set number of days rejects values outside allowed range', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $month = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    );

    $this->expectException(InvalidWorldCalendarMonthNumberOfDaysException::class);
    $month->setNumberOfDays(0);
});
