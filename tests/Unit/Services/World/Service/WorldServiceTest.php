<?php

declare(strict_types=1);

use App\Services\User\Domain\User;
use App\Services\World\Domain\DTOs\WorldDateDto;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarDayOfWeek;
use App\Services\World\Domain\WorldCalendarMonth;
use App\Services\World\Infrastructure\WorldRepository;
use App\Services\World\Service\Exceptions\InvalidWorldDateDtoException;
use App\Services\World\Service\Exceptions\UnconfiguredWorldCalendarException;
use App\Services\World\Service\WorldCalendarService;
use App\Services\World\Service\WorldService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    $this->makeWorld = function (): World {
        return new World(
            name: 'Test World',
            description: 'A test world',
            slug: 'test-world',
            creator: $this->createStub(User::class),
        );
    };

    $this->makeWorldService = function (): WorldService {
        return new WorldService(
            new WorldCalendarService(),
            $this->createStub(WorldRepository::class),
            $this->createStub(EntityManagerInterface::class),
        );
    };
});

test('timestamp to world date resolves first hour of first year', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $settings->setHoursInDay(24);
    $settings->setDaysOfWeek(new ArrayCollection([
        new WorldCalendarDayOfWeek($settings, 'Moonday', 'Mon'),
        new WorldCalendarDayOfWeek($settings, 'Starday', 'Sta'),
        new WorldCalendarDayOfWeek($settings, 'Sunday', 'Sun'),
    ]));
    $settings->setWorldCalendarMonths(new ArrayCollection([
        new WorldCalendarMonth($settings, 'Hammer', 'hammer', 2, 30),
        new WorldCalendarMonth($settings, 'Anvil', 'anvil', 1, 20),
    ]));
    $worldService = ($this->makeWorldService)();

    $worldDate = $worldService->timeStampToWorldDate(1, $world);

    self::assertInstanceOf(WorldDateDto::class, $worldDate);
    self::assertSame('Moonday', $worldDate->getDayOfWeek()->getName());
    self::assertSame('Anvil', $worldDate->getMonth()->getName());
    self::assertSame(1, $worldDate->getDayOfMonth());
    self::assertSame(1, $worldDate->getHourOfDay());
    self::assertSame(0, $worldDate->getYear());
});

test('timestamp to world date rolls through months and years', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $settings->setHoursInDay(24);
    $settings->setDaysOfWeek(new ArrayCollection([
        new WorldCalendarDayOfWeek($settings, 'Moonday', 'Mon'),
        new WorldCalendarDayOfWeek($settings, 'Starday', 'Sta'),
        new WorldCalendarDayOfWeek($settings, 'Sunday', 'Sun'),
    ]));
    $settings->setWorldCalendarMonths(new ArrayCollection([
        new WorldCalendarMonth($settings, 'Firstmonth', 'firstmonth', 1, 2),
        new WorldCalendarMonth($settings, 'Secondmonth', 'secondmonth', 2, 3),
    ]));
    $worldService = ($this->makeWorldService)();

    $worldDate = $worldService->timeStampToWorldDate(121, $world);

    self::assertSame('Sunday', $worldDate->getDayOfWeek()->getName());
    self::assertSame('Firstmonth', $worldDate->getMonth()->getName());
    self::assertSame(1, $worldDate->getDayOfMonth());
    self::assertSame(1, $worldDate->getHourOfDay());
    self::assertSame(1, $worldDate->getYear());
});

test('world date to timestamp resolves timestamp', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $settings->setHoursInDay(24);
    $settings->setDaysOfWeek(new ArrayCollection([
        new WorldCalendarDayOfWeek($settings, 'Moonday', 'Mon'),
        new WorldCalendarDayOfWeek($settings, 'Starday', 'Sta'),
        new WorldCalendarDayOfWeek($settings, 'Sunday', 'Sun'),
    ]));
    $settings->setWorldCalendarMonths(new ArrayCollection([
        new WorldCalendarMonth($settings, 'Firstmonth', 'firstmonth', 1, 2),
        new WorldCalendarMonth($settings, 'Secondmonth', 'secondmonth', 2, 3),
    ]));
    $worldService = ($this->makeWorldService)();
    $worldDate = new WorldDateDto(
        dayOfWeek: $settings->getDaysOfWeek()->toArray()[2],
        month: $settings->getWorldCalendarMonths()->toArray()[0],
        dayOfMonth: 1,
        hourOfDay: 1,
        year: 1,
    );

    $timestamp = $worldService->worldDateToTimestamp($worldDate, $world);

    self::assertSame(121, $timestamp);
});

test('timestamp and world date conversion round-trips', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $settings->setHoursInDay(24);
    $settings->setDaysOfWeek(new ArrayCollection([
        new WorldCalendarDayOfWeek($settings, 'Moonday', 'Mon'),
        new WorldCalendarDayOfWeek($settings, 'Starday', 'Sta'),
        new WorldCalendarDayOfWeek($settings, 'Sunday', 'Sun'),
    ]));
    $settings->setWorldCalendarMonths(new ArrayCollection([
        new WorldCalendarMonth($settings, 'Firstmonth', 'firstmonth', 1, 2),
        new WorldCalendarMonth($settings, 'Secondmonth', 'secondmonth', 2, 3),
    ]));
    $worldService = ($this->makeWorldService)();
    $originalTimestamp = 73;

    $worldDate = $worldService->timeStampToWorldDate($originalTimestamp, $world);
    $roundTrippedTimestamp = $worldService->worldDateToTimestamp($worldDate, $world);
    $roundTrippedWorldDate = $worldService->timeStampToWorldDate($roundTrippedTimestamp, $world);

    self::assertSame($originalTimestamp, $roundTrippedTimestamp);
    self::assertSame($worldDate->getDayOfWeek(), $roundTrippedWorldDate->getDayOfWeek());
    self::assertSame($worldDate->getMonth(), $roundTrippedWorldDate->getMonth());
    self::assertSame($worldDate->getDayOfMonth(), $roundTrippedWorldDate->getDayOfMonth());
    self::assertSame($worldDate->getHourOfDay(), $roundTrippedWorldDate->getHourOfDay());
    self::assertSame($worldDate->getYear(), $roundTrippedWorldDate->getYear());
});

test('timestamp to world date rejects unconfigured calendars', function () {
    $world = ($this->makeWorld)();
    $worldService = ($this->makeWorldService)();

    $this->expectException(UnconfiguredWorldCalendarException::class);

    $worldService->timeStampToWorldDate(1, $world);
});

test('world date to timestamp rejects mismatched day of week', function () {
    $world = ($this->makeWorld)();
    $settings = $world->getWorldCalendarSettings();
    $settings->setHoursInDay(24);
    $settings->setDaysOfWeek(new ArrayCollection([
        new WorldCalendarDayOfWeek($settings, 'Moonday', 'Mon'),
        new WorldCalendarDayOfWeek($settings, 'Starday', 'Sta'),
        new WorldCalendarDayOfWeek($settings, 'Sunday', 'Sun'),
    ]));
    $settings->setWorldCalendarMonths(new ArrayCollection([
        new WorldCalendarMonth($settings, 'Firstmonth', 'firstmonth', 1, 2),
        new WorldCalendarMonth($settings, 'Secondmonth', 'secondmonth', 2, 3),
    ]));
    $worldService = ($this->makeWorldService)();
    $worldDate = new WorldDateDto(
        dayOfWeek: $settings->getDaysOfWeek()->toArray()[0],
        month: $settings->getWorldCalendarMonths()->toArray()[0],
        dayOfMonth: 1,
        hourOfDay: 1,
        year: 1,
    );

    $this->expectException(InvalidWorldDateDtoException::class);

    $worldService->worldDateToTimestamp($worldDate, $world);
});
