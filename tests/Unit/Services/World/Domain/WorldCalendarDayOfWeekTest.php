<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekAbbreviationException;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekNameException;
use App\Services\World\Domain\Exceptions\InvalidWorldCalendarDayOfWeekAbbreviationException;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarDayOfWeek;

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

test('construct', function () {
    $settings = ($this->makeWorld)()->getWorldCalendarSettings();
    $day = new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Mon',
    );

    self::assertSame($settings, $day->getWorldCalendarSettings());
    self::assertSame('Moonday', $day->getName());
    self::assertSame('Mon', $day->getAbbreviation());
});

test('setters update state', function () {
    $settings = ($this->makeWorld)()->getWorldCalendarSettings();
    $day = new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Mon',
    );

    $day->setName('Starday');
    $day->setAbbreviation('Sta');

    self::assertSame('Starday', $day->getName());
    self::assertSame('Sta', $day->getAbbreviation());
});

test('set name rejects duplicates within settings', function () {
    $settings = ($this->makeWorld)()->getWorldCalendarSettings();
    $first = new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Moonday', abbreviation: 'Mon');
    $second = new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Starday', abbreviation: 'Sta');
    $settings->addDayOfWeek($first);
    $settings->addDayOfWeek($second);

    $this->expectException(DuplicateWorldCalendarDayOfWeekNameException::class);
    $second->setName('Moonday');
});

test('set abbreviation rejects duplicates within settings', function () {
    $settings = ($this->makeWorld)()->getWorldCalendarSettings();
    $first = new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Moonday', abbreviation: 'Mon');
    $second = new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Starday', abbreviation: 'Sta');
    $settings->addDayOfWeek($first);
    $settings->addDayOfWeek($second);

    $this->expectException(DuplicateWorldCalendarDayOfWeekAbbreviationException::class);
    $second->setAbbreviation('Mon');
});

test('construct rejects abbreviation longer than three characters', function () {
    $settings = ($this->makeWorld)()->getWorldCalendarSettings();

    $this->expectException(InvalidWorldCalendarDayOfWeekAbbreviationException::class);

    new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Moon',
    );
});
