<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekAbbreviationException;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekNameException;
use App\Services\World\Domain\Exceptions\OverlappingWorldCalendarMonthDeltaException;
use App\Services\World\Domain\Exceptions\TooFewWorldCalendarDaysOfWeekException;
use App\Services\World\Domain\Exceptions\TooManyWorldCalendarDaysOfWeekException;
use App\Services\World\Domain\Exceptions\TooManyWorldCalendarMonthsException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WorldCalendarSettings extends AbstractDomainEntity
{

    public const int MAXIMUM_DAYS_PER_WEEK = 50;
    public const int MINIMUM_DAYS_PER_WEEK = 3;
    public const int MAXIMUM_MONTHS_PER_YEAR = 50;

    /**
     * @throws TooManyWorldCalendarMonthsException
     */
    public function __construct(
        #[ORM\OneToOne(targetEntity: World::class, inversedBy: 'worldCalendarSettings')]
        #[ORM\JoinColumn(nullable: false)]
        private World $world,

        #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 24])]
        private int $hoursInDay = 24,

        #[ORM\OneToMany(targetEntity: WorldCalendarMonth::class, mappedBy: 'worldCalendarSettings', cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
        private Collection $worldCalendarMonths = new ArrayCollection(),

        #[ORM\OneToMany(targetEntity: WorldCalendarDayOfWeek::class, mappedBy: 'worldCalendarSettings', cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
        private Collection $daysOfWeek = new ArrayCollection(),

        ?int $id = null,
    ) {
        parent::__construct($id);
        $this->assertMonthLimitNotExceeded();
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function setWorld(World $world): void
    {
        $this->world = $world;
    }

    public function getHoursInDay(): int
    {
        return $this->hoursInDay;
    }

    public function setHoursInDay(int $hoursInDay): void
    {
        $this->hoursInDay = $hoursInDay;
    }

    /**
     * @return Collection<int, WorldCalendarMonth>
     */
    public function getWorldCalendarMonths(): Collection
    {
        return $this->worldCalendarMonths;
    }

    /**
     * @return Collection<int, WorldCalendarDayOfWeek>
     */
    public function getDaysOfWeek(): Collection
    {
        return $this->daysOfWeek;
    }

    /**
     * @throws OverlappingWorldCalendarMonthDeltaException
     * @throws TooManyWorldCalendarMonthsException
     */
    public function addWorldCalendarMonth(WorldCalendarMonth $worldCalendarMonth): void
    {
        if ($this->worldCalendarMonths->contains($worldCalendarMonth)) {
            return;
        }

        $this->assertMonthLimitNotExceeded(1);
        $this->assertMonthDeltaAvailable($worldCalendarMonth->getDelta(), $worldCalendarMonth);
        $this->worldCalendarMonths->add($worldCalendarMonth);
        $worldCalendarMonth->setWorldCalendarSettings($this);
        $this->sortWorldCalendarMonths();
    }

    public function removeWorldCalendarMonth(WorldCalendarMonth $worldCalendarMonth): void
    {
        $this->worldCalendarMonths->removeElement($worldCalendarMonth);
        $this->sortWorldCalendarMonths();
    }

    /**
     * @param Collection<int, WorldCalendarMonth> $worldCalendarMonths
     * @throws OverlappingWorldCalendarMonthDeltaException
     * @throws TooManyWorldCalendarMonthsException
     */
    public function setWorldCalendarMonths(Collection $worldCalendarMonths): void
    {
        $this->assertMonthLimitNotExceeded($worldCalendarMonths->count() - $this->worldCalendarMonths->count());

        $seenDeltas = [];
        foreach ($worldCalendarMonths as $worldCalendarMonth) {
            if (in_array($worldCalendarMonth->getDelta(), $seenDeltas, true)) {
                throw new OverlappingWorldCalendarMonthDeltaException($worldCalendarMonth->getDelta());
            }
            $seenDeltas[] = $worldCalendarMonth->getDelta();
        }

        $this->worldCalendarMonths = new ArrayCollection(self::sortWorldCalendarMonthArray($worldCalendarMonths->toArray()));

        foreach ($this->worldCalendarMonths as $worldCalendarMonth) {
            $worldCalendarMonth->setWorldCalendarSettings($this);
        }
    }

    /**
     * @throws OverlappingWorldCalendarMonthDeltaException
     */
    public function assertMonthDeltaAvailable(int $delta, ?WorldCalendarMonth $ignoreMonth = null): void
    {
        foreach ($this->worldCalendarMonths as $worldCalendarMonth) {
            if ($ignoreMonth !== null && $worldCalendarMonth === $ignoreMonth) {
                continue;
            }

            if ($worldCalendarMonth->getDelta() === $delta) {
                throw new OverlappingWorldCalendarMonthDeltaException($delta);
            }
        }
    }

    /**
     * @throws TooManyWorldCalendarMonthsException
     */
    private function assertMonthLimitNotExceeded(int $additionalMonths = 0): void
    {
        if (($this->worldCalendarMonths->count() + $additionalMonths) > self::MAXIMUM_MONTHS_PER_YEAR) {
            throw new TooManyWorldCalendarMonthsException();
        }
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     * @throws TooManyWorldCalendarDaysOfWeekException
     */
    public function addDayOfWeek(WorldCalendarDayOfWeek $dayOfWeek): void
    {
        if ($this->daysOfWeek->contains($dayOfWeek)) {
            return;
        }

        $this->assertDaysOfWeekMaximumNotExceeded(1);
        $this->assertDayOfWeekNameAvailable($dayOfWeek->getName(), $dayOfWeek);
        $this->assertDayOfWeekAbbreviationAvailable($dayOfWeek->getAbbreviation(), $dayOfWeek);
        $this->daysOfWeek->add($dayOfWeek);
        $dayOfWeek->setWorldCalendarSettings($this);
    }

    public function removeDayOfWeek(WorldCalendarDayOfWeek $dayOfWeek): void
    {
        $this->daysOfWeek->removeElement($dayOfWeek);
    }

    /**
     * @param Collection<int, WorldCalendarDayOfWeek> $daysOfWeek
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     * @throws TooFewWorldCalendarDaysOfWeekException
     * @throws TooManyWorldCalendarDaysOfWeekException
     */
    public function setDaysOfWeek(Collection $daysOfWeek): void
    {
        $count = $daysOfWeek->count();
        if ($count < self::MINIMUM_DAYS_PER_WEEK) {
            throw new TooFewWorldCalendarDaysOfWeekException();
        }
        if ($count > self::MAXIMUM_DAYS_PER_WEEK) {
            throw new TooManyWorldCalendarDaysOfWeekException();
        }

        $seenNames = [];
        $seenAbbreviations = [];

        foreach ($daysOfWeek as $dayOfWeek) {
            if (in_array($dayOfWeek->getName(), $seenNames, true)) {
                throw new DuplicateWorldCalendarDayOfWeekNameException($dayOfWeek->getName());
            }
            if (in_array($dayOfWeek->getAbbreviation(), $seenAbbreviations, true)) {
                throw new DuplicateWorldCalendarDayOfWeekAbbreviationException($dayOfWeek->getAbbreviation());
            }

            $seenNames[] = $dayOfWeek->getName();
            $seenAbbreviations[] = $dayOfWeek->getAbbreviation();
        }

        $this->daysOfWeek = new ArrayCollection();

        foreach ($daysOfWeek as $dayOfWeek) {
            $this->addDayOfWeek($dayOfWeek);
        }
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     */
    public function assertDayOfWeekNameAvailable(string $name, ?WorldCalendarDayOfWeek $ignoreDay = null): void
    {
        foreach ($this->daysOfWeek as $dayOfWeek) {
            if ($ignoreDay !== null && $dayOfWeek === $ignoreDay) {
                continue;
            }

            if ($dayOfWeek->getName() === $name) {
                throw new DuplicateWorldCalendarDayOfWeekNameException($name);
            }
        }
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     */
    public function assertDayOfWeekAbbreviationAvailable(string $abbreviation, ?WorldCalendarDayOfWeek $ignoreDay = null): void
    {
        foreach ($this->daysOfWeek as $dayOfWeek) {
            if ($ignoreDay !== null && $dayOfWeek === $ignoreDay) {
                continue;
            }

            if ($dayOfWeek->getAbbreviation() === $abbreviation) {
                throw new DuplicateWorldCalendarDayOfWeekAbbreviationException($abbreviation);
            }
        }
    }

    /**
     * @throws TooManyWorldCalendarDaysOfWeekException
     */
    private function assertDaysOfWeekMaximumNotExceeded(int $additionalDays = 0): void
    {
        if (($this->daysOfWeek->count() + $additionalDays) > self::MAXIMUM_DAYS_PER_WEEK) {
            throw new TooManyWorldCalendarDaysOfWeekException();
        }
    }

    private function sortWorldCalendarMonths(): void
    {
        $this->worldCalendarMonths = new ArrayCollection(
            self::sortWorldCalendarMonthArray($this->worldCalendarMonths->toArray())
        );
    }

    /**
     * @param array<int, WorldCalendarMonth> $worldCalendarMonths
     * @return array<int, WorldCalendarMonth>
     */
    private static function sortWorldCalendarMonthArray(array $worldCalendarMonths): array
    {
        usort(
            $worldCalendarMonths,
            static fn (WorldCalendarMonth $leftMonth, WorldCalendarMonth $rightMonth): int => $leftMonth->getDelta() <=> $rightMonth->getDelta()
        );

        return array_values($worldCalendarMonths);
    }
}
