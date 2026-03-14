<?php

declare(strict_types=1);

namespace App\Services\World\Service;

use App\Services\World\Domain\DTOs\WorldDateDto;
use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarDayOfWeek;
use App\Services\World\Domain\WorldCalendarMonth;
use App\Services\World\Service\Exceptions\InvalidWorldDateDtoException;
use App\Services\World\Service\Exceptions\UnconfiguredWorldCalendarException;
use App\Services\World\Service\Interfaces\WorldCalendarServiceInterface;

class WorldCalendarService implements WorldCalendarServiceInterface
{
    private const int FIRST_YEAR = 0;
    private const int FIRST_TIMESTAMP = 1;
    private const int FIRST_DAY_OF_MONTH = 1;
    private const int FIRST_HOUR_OF_DAY = 1;

    /**
     * @throws UnconfiguredWorldCalendarException
     */
    public function timeStampToWorldDate(int $timestamp, World $world): WorldDateDto
    {
        [$hoursInDay, $daysOfWeek, $months] = self::extractConfiguredCalendar($world);

        $hoursElapsed = $timestamp - self::FIRST_TIMESTAMP;
        $totalDaysElapsed = intdiv(max(0, $hoursElapsed), $hoursInDay);
        $hourOfDay = ($hoursElapsed % $hoursInDay) + self::FIRST_HOUR_OF_DAY;
        $daysPerYear = self::calculateDaysPerYear($months);
        $year = intdiv($totalDaysElapsed, $daysPerYear) + self::FIRST_YEAR;
        $dayOfYear = $totalDaysElapsed % $daysPerYear;
        $dayOfWeek = $daysOfWeek[$totalDaysElapsed % count($daysOfWeek)];
        [$month, $dayOfMonth] = self::resolveMonthForDayOfYear($months, $dayOfYear);

        return new WorldDateDto(
            dayOfWeek: $dayOfWeek,
            month: $month,
            dayOfMonth: $dayOfMonth,
            hourOfDay: $hourOfDay,
            year: $year,
        );
    }

    /**
     * @throws InvalidWorldDateDtoException
     * @throws UnconfiguredWorldCalendarException
     */
    public function worldDateToTimestamp(WorldDateDto $worldDate, World $world): int
    {
        [$hoursInDay, $daysOfWeek, $months] = self::extractConfiguredCalendar($world);

        $hourOfDay = $worldDate->getHourOfDay();
        if ($hourOfDay < self::FIRST_HOUR_OF_DAY || $hourOfDay > $hoursInDay) {
            throw new InvalidWorldDateDtoException($world->getSlug(), 'hourOfDay is outside the configured range for this world.');
        }

        $monthIndex = self::findMonthIndex($months, $worldDate->getMonth());
        if ($monthIndex === null) {
            throw new InvalidWorldDateDtoException($world->getSlug(), 'month is not part of this world calendar.');
        }

        $dayOfWeekIndex = self::findDayOfWeekIndex($daysOfWeek, $worldDate->getDayOfWeek());
        if ($dayOfWeekIndex === null) {
            throw new InvalidWorldDateDtoException($world->getSlug(), 'dayOfWeek is not part of this world calendar.');
        }

        $dayOfMonth = $worldDate->getDayOfMonth();
        if ($dayOfMonth < self::FIRST_DAY_OF_MONTH || $dayOfMonth > $worldDate->getMonth()->getNumberOfDays()) {
            throw new InvalidWorldDateDtoException($world->getSlug(), 'dayOfMonth is outside the configured range for the provided month.');
        }

        $daysElapsedBeforeMonth = self::calculateDaysElapsedBeforeMonth($months, $monthIndex);
        $daysPerYear = self::calculateDaysPerYear($months);
        $totalDaysElapsed = ($worldDate->getYear() * $daysPerYear) + $daysElapsedBeforeMonth + ($dayOfMonth - self::FIRST_DAY_OF_MONTH);
        $expectedDayOfWeekIndex = $totalDaysElapsed % count($daysOfWeek);

        if ($expectedDayOfWeekIndex !== $dayOfWeekIndex) {
            throw new InvalidWorldDateDtoException($world->getSlug(), 'dayOfWeek does not match the provided year, month, and dayOfMonth.');
        }

        return ($totalDaysElapsed * $hoursInDay) + ($hourOfDay - self::FIRST_HOUR_OF_DAY) + self::FIRST_TIMESTAMP;
    }

    /**
     * @param array<int, WorldCalendarDayOfWeek> $daysOfWeek
     * @return array<int, WorldCalendarDayOfWeek>
     */
    private static function normalizeDaysOfWeek(array $daysOfWeek): array
    {
        return array_values($daysOfWeek);
    }

    /**
     * @return array{0: int, 1: array<int, WorldCalendarDayOfWeek>, 2: array<int, WorldCalendarMonth>}
     * @throws UnconfiguredWorldCalendarException
     */
    private static function extractConfiguredCalendar(World $world): array
    {
        $calendarSettings = $world->getWorldCalendarSettings();
        $hoursInDay = $calendarSettings->getHoursInDay();
        $daysOfWeek = self::normalizeDaysOfWeek($calendarSettings->getDaysOfWeek()->toArray());
        $months = array_values($calendarSettings->getWorldCalendarMonths()->toArray());

        if ($hoursInDay < self::FIRST_TIMESTAMP || count($daysOfWeek) === 0 || count($months) === 0) {
            throw new UnconfiguredWorldCalendarException($world->getSlug());
        }

        return [$hoursInDay, $daysOfWeek, $months];
    }

    /**
     * @param array<int, WorldCalendarMonth> $months
     */
    private static function calculateDaysPerYear(array $months): int
    {
        $daysPerYear = 0;

        foreach ($months as $month) {
            $daysPerYear += $month->getNumberOfDays();
        }

        return $daysPerYear;
    }

    /**
     * @param array<int, WorldCalendarMonth> $months
     * @return array{0: WorldCalendarMonth, 1: int}
     */
    private static function resolveMonthForDayOfYear(array $months, int $dayOfYear): array
    {
        $daysTraversed = 0;

        foreach ($months as $month) {
            $daysBeforeMonth = $daysTraversed;
            $daysTraversed += $month->getNumberOfDays();

            if ($dayOfYear < $daysTraversed) {
                return [$month, ($dayOfYear - $daysBeforeMonth) + self::FIRST_DAY_OF_MONTH];
            }
        }

        $lastMonth = $months[array_key_last($months)];

        return [$lastMonth, $lastMonth->getNumberOfDays()];
    }

    /**
     * @param array<int, WorldCalendarMonth> $months
     */
    private static function findMonthIndex(array $months, WorldCalendarMonth $monthToFind): ?int
    {
        foreach ($months as $monthIndex => $month) {
            if ($month === $monthToFind) {
                return $monthIndex;
            }
        }

        return null;
    }

    /**
     * @param array<int, WorldCalendarDayOfWeek> $daysOfWeek
     */
    private static function findDayOfWeekIndex(array $daysOfWeek, WorldCalendarDayOfWeek $dayOfWeekToFind): ?int
    {
        foreach ($daysOfWeek as $dayOfWeekIndex => $dayOfWeek) {
            if ($dayOfWeek === $dayOfWeekToFind) {
                return $dayOfWeekIndex;
            }
        }

        return null;
    }

    /**
     * @param array<int, WorldCalendarMonth> $months
     */
    private static function calculateDaysElapsedBeforeMonth(array $months, int $targetMonthIndex): int
    {
        $daysElapsedBeforeMonth = 0;

        for ($monthIndex = 0; $monthIndex < $targetMonthIndex; $monthIndex++) {
            $daysElapsedBeforeMonth += $months[$monthIndex]->getNumberOfDays();
        }

        return $daysElapsedBeforeMonth;
    }
}
