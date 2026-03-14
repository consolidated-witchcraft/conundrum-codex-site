<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekAbbreviationException;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekNameException;
use App\Services\World\Domain\Exceptions\OverlappingWorldCalendarMonthDeltaException;
use App\Services\World\Domain\Exceptions\TooFewWorldCalendarDaysOfWeekException;
use App\Services\World\Domain\Exceptions\TooManyWorldCalendarDaysOfWeekException;
use App\Services\World\Domain\WorldCalendarDayOfWeek;
use App\Services\World\Domain\Exceptions\TooManyWorldCalendarMonthsException;
use App\Services\World\Domain\WorldCalendarMonth;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarSettings;

beforeEach(function () {
    $this->makeSettings = function (): WorldCalendarSettings {
        return new WorldCalendarSettings(new World(
            name: 'Test World',
            description: 'A test world',
            slug: 'test-world',
            creator: $this->createStub(User::class),
        ));
    };
});

test('construct', function () {
    $settings = ($this->makeSettings)();

    self::assertInstanceOf(WorldCalendarSettings::class, $settings);
    self::assertSame(24, $settings->getHoursInDay());
    self::assertCount(0, $settings->getWorldCalendarMonths());
    self::assertCount(0, $settings->getDaysOfWeek());
});

test('set hours in day', function () {
    $settings = ($this->makeSettings)();

    $settings->setHoursInDay(30);

    self::assertSame(30, $settings->getHoursInDay());
});

test('add world calendar month', function () {
    $settings = ($this->makeSettings)();
    $month = new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    );

    $settings->addWorldCalendarMonth($month);

    self::assertCount(1, $settings->getWorldCalendarMonths());
    self::assertTrue($settings->getWorldCalendarMonths()->contains($month));
});

test('world calendar months are stored sorted by delta', function () {
    $settings = ($this->makeSettings)();

    $settings->addWorldCalendarMonth(new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 2,
        numberOfDays: 30,
    ));
    $settings->addWorldCalendarMonth(new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Anvil',
        abbreviation: 'anvil',
        delta: 1,
        numberOfDays: 31,
    ));

    $months = $settings->getWorldCalendarMonths()->toArray();

    self::assertSame('Anvil', $months[0]->getName());
    self::assertSame('Hammer', $months[1]->getName());
});

test('add world calendar month rejects overlapping delta', function () {
    $settings = ($this->makeSettings)();
    $settings->addWorldCalendarMonth(new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Hammer',
        abbreviation: 'hammer',
        delta: 1,
        numberOfDays: 30,
    ));

    $this->expectException(OverlappingWorldCalendarMonthDeltaException::class);

    $settings->addWorldCalendarMonth(new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Anvil',
        abbreviation: 'anvil',
        delta: 1,
        numberOfDays: 31,
    ));
});

test('add world calendar month rejects more than fifty months', function () {
    $settings = ($this->makeSettings)();

    for ($monthNumber = 1; $monthNumber <= 50; $monthNumber++) {
        $settings->addWorldCalendarMonth(new WorldCalendarMonth(
            worldCalendarSettings: $settings,
            name: sprintf('Month %d', $monthNumber),
            abbreviation: sprintf('month-%d', $monthNumber),
            delta: $monthNumber,
            numberOfDays: 30,
        ));
    }

    $this->expectException(TooManyWorldCalendarMonthsException::class);

    $settings->addWorldCalendarMonth(new WorldCalendarMonth(
        worldCalendarSettings: $settings,
        name: 'Month 51',
        abbreviation: 'month-51',
        delta: 51,
        numberOfDays: 30,
    ));
});

test('add day of week', function () {
    $settings = ($this->makeSettings)();
    $day = new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Mon',
    );

    $settings->addDayOfWeek($day);

    self::assertCount(1, $settings->getDaysOfWeek());
    self::assertTrue($settings->getDaysOfWeek()->contains($day));
});

test('add day of week rejects duplicate name', function () {
    $settings = ($this->makeSettings)();
    $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Mon',
    ));

    $this->expectException(DuplicateWorldCalendarDayOfWeekNameException::class);

    $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Sta',
    ));
});

test('add day of week rejects duplicate abbreviation', function () {
    $settings = ($this->makeSettings)();
    $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Moonday',
        abbreviation: 'Mon',
    ));

    $this->expectException(DuplicateWorldCalendarDayOfWeekAbbreviationException::class);

    $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Starday',
        abbreviation: 'Mon',
    ));
});

test('add day of week rejects more than fifty days', function () {
    $settings = ($this->makeSettings)();

    for ($dayNumber = 1; $dayNumber <= 50; $dayNumber++) {
        $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
            worldCalendarSettings: $settings,
            name: sprintf('Day %d', $dayNumber),
            abbreviation: sprintf('D%02d', $dayNumber % 100),
        ));
    }

    $this->expectException(TooManyWorldCalendarDaysOfWeekException::class);

    $settings->addDayOfWeek(new WorldCalendarDayOfWeek(
        worldCalendarSettings: $settings,
        name: 'Day 51',
        abbreviation: 'X51',
    ));
});

test('set days of week rejects fewer than three days', function () {
    $settings = ($this->makeSettings)();

    $this->expectException(TooFewWorldCalendarDaysOfWeekException::class);

    $settings->setDaysOfWeek(new \Doctrine\Common\Collections\ArrayCollection([
        new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Moonday', abbreviation: 'Mon'),
        new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Starday', abbreviation: 'Sta'),
    ]));
});

test('set days of week accepts three unique days', function () {
    $settings = ($this->makeSettings)();

    $settings->setDaysOfWeek(new \Doctrine\Common\Collections\ArrayCollection([
        new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Moonday', abbreviation: 'Mon'),
        new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Starday', abbreviation: 'Sta'),
        new WorldCalendarDayOfWeek(worldCalendarSettings: $settings, name: 'Sunway', abbreviation: 'Sun'),
    ]));

    self::assertCount(3, $settings->getDaysOfWeek());
});
