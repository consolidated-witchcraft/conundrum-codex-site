<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\World\Domain\Exceptions\InvalidWorldCalendarMonthNumberOfDaysException;
use App\Services\World\Domain\Exceptions\OverlappingWorldCalendarMonthDeltaException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'world_calendar_month')]
#[ORM\UniqueConstraint(name: 'world_calendar_month_slug_unique', columns: ['slug'])]
#[ORM\UniqueConstraint(name: 'world_calendar_month_settings_delta_unique', columns: ['world_calendar_settings_id', 'delta'])]
#[UniqueEntity(fields: ['slug'])]
#[UniqueEntity(fields: ['worldCalendarSettings', 'delta'])]
class WorldCalendarMonth extends AbstractDomainEntity
{
    public const int MINIMUM_NUMBER_OF_DAYS = 1;
    public const int MAXIMUM_NUMBER_OF_DAYS = 50;

    /**
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     * @throws OverlappingWorldCalendarMonthDeltaException
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: WorldCalendarSettings::class, inversedBy: 'worldCalendarMonths')]
        #[ORM\JoinColumn(nullable: false)]
        private WorldCalendarSettings $worldCalendarSettings,

        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $slug,

        #[ORM\Column(type: 'integer')]
        private int $delta,

        #[ORM\Column(type: 'integer')]
        private int $numberOfDays,

        ?int $id = null,
    ) {
        parent::__construct($id);
        $this->worldCalendarSettings->assertMonthDeltaAvailable($this->delta, $this);
        $this->assertValidNumberOfDays($this->numberOfDays);
    }

    public function getWorldCalendarSettings(): WorldCalendarSettings
    {
        return $this->worldCalendarSettings;
    }

    /**
     * @throws OverlappingWorldCalendarMonthDeltaException
     */
    public function setWorldCalendarSettings(WorldCalendarSettings $worldCalendarSettings): void
    {
        $worldCalendarSettings->assertMonthDeltaAvailable($this->delta, $this);
        $this->worldCalendarSettings = $worldCalendarSettings;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getDelta(): int
    {
        return $this->delta;
    }

    /**
     * @throws OverlappingWorldCalendarMonthDeltaException
     */
    public function setDelta(int $delta): void
    {
        $this->worldCalendarSettings->assertMonthDeltaAvailable($delta, $this);
        $this->delta = $delta;
    }

    public function getNumberOfDays(): int
    {
        return $this->numberOfDays;
    }

    /**
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     */
    public function setNumberOfDays(int $numberOfDays): void
    {
        $this->assertValidNumberOfDays($numberOfDays);
        $this->numberOfDays = $numberOfDays;
    }

    /**
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     */
    private function assertValidNumberOfDays(int $numberOfDays): void
    {
        if (
            $numberOfDays < self::MINIMUM_NUMBER_OF_DAYS
            || $numberOfDays > self::MAXIMUM_NUMBER_OF_DAYS
        ) {
            throw new InvalidWorldCalendarMonthNumberOfDaysException($numberOfDays);
        }
    }
}
