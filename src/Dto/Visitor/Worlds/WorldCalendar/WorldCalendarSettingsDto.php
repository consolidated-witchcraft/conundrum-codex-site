<?php

namespace App\Dto\Visitor\Worlds\WorldCalendar;

use App\Services\World\Domain\World;
use App\Services\World\Domain\WorldCalendarSettings;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class WorldCalendarSettingsDto
{
    public function __construct(
        public ? int $hoursInDay = null,
        public Collection $daysOfWeek = new ArrayCollection(),
        public Collection $months = new ArrayCollection(),
    ) {}

    public static function makeFromWorldCalendarSettings(WorldCalendarSettings $worldCalendarSettings): WorldCalendarSettingsDto {
        $daysOfWeek = new ArrayCollection();
        foreach ($worldCalendarSettings->getDaysOfWeek() as $day) {
            $daysOfWeek->add(WorldCalendarDayOfWeekDto::makeFromWorldCalendarDayOfWeek($day));
        }

        $months = new ArrayCollection();
        foreach ($worldCalendarSettings->getWorldCalendarMonths() as $month) {
            $months->add(WorldCalendarMonthDto::makeFromWorldCalendarMonth($month));
        }

        return new static(
            hoursInDay: $worldCalendarSettings->getHoursInDay(),
            daysOfWeek: $daysOfWeek,
            months: $months
        );
    }

}
