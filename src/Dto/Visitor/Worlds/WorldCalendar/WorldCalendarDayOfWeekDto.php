<?php

namespace App\Dto\Visitor\Worlds\WorldCalendar;

use App\Services\World\Domain\WorldCalendarDayOfWeek;

class WorldCalendarDayOfWeekDto
{
    public function __construct(
        public ? string $name = null,
        public ? string $abbreviation = null,
        public ? int $id = null
    ) {}

    public static function makeFromWorldCalendarDayOfWeek(WorldCalendarDayOfWeek $dayOfWeek): static {
        return new static(
            name: $dayOfWeek->getName(),
            abbreviation: $dayOfWeek->getAbbreviation(),
            id: $dayOfWeek->getId()
        );
    }
}
