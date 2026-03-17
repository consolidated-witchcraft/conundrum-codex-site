<?php


declare(strict_types=1);

namespace App\Dto\Visitor\Worlds\WorldCalendar;

use App\Services\World\Domain\WorldCalendarMonth;

class WorldCalendarMonthDto
{
    public function __construct(
        public ? string $name = null,
        public ? string $abbreviation = null,
        public ? int $delta = null,
        public ? int $numberOfDays = null,
        public ? int $id = null

    ) {}

    public static function makeFromWorldCalendarMonth(WorldCalendarMonth $month): static {
        return new static(
            name: $month->getName(),
            abbreviation: $month->getAbbreviation(),
            delta: $month->getDelta(),
            numberOfDays: $month->getNumberOfDays(),
        );
    }
}
