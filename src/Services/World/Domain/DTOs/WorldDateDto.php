<?php

declare(strict_types=1);

namespace App\Services\World\Domain\DTOs;

use App\Services\World\Domain\WorldCalendarDayOfWeek;
use App\Services\World\Domain\WorldCalendarMonth;

class WorldDateDto
{
    public function __construct(
        private readonly WorldCalendarDayOfWeek $dayOfWeek,
        private readonly WorldCalendarMonth $month,
        private readonly int $dayOfMonth,
        private readonly int $hourOfDay,
        private readonly int $year,
    ) {
    }

    public function getDayOfWeek(): WorldCalendarDayOfWeek
    {
        return $this->dayOfWeek;
    }

    public function getMonth(): WorldCalendarMonth
    {
        return $this->month;
    }

    public function getDayOfMonth(): int
    {
        return $this->dayOfMonth;
    }

    public function getHourOfDay(): int
    {
        return $this->hourOfDay;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
