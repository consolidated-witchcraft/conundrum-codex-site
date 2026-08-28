<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Dto\Visitor\Worlds\WorldCalendar\WorldCalendarMonthDto;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\World\Domain\Exceptions\InvalidWorldCalendarMonthNumberOfDaysException;
use App\Services\World\Domain\Exceptions\OverlappingWorldCalendarMonthDeltaException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'world_calendar_month')]
#[ORM\UniqueConstraint(name: 'world_calendar_month_abbreviation_unique', columns: ['abbreviation'])]
#[ORM\UniqueConstraint(name: 'world_calendar_month_settings_delta_unique', columns: ['world_calendar_settings_id', 'delta'])]
#[UniqueEntity(fields: ['abbreviation'])]
#[UniqueEntity(fields: ['worldCalendarSettings', 'delta'])]
class WorldCalendarMonth extends AbstractDomainEntity
{
    public const int MINIMUM_NUMBER_OF_DAYS = 1;
    public const int MAXIMUM_NUMBER_OF_DAYS = 50;
    private const int MAXIMUM_NAME_LENGTH = 255;
    private const int MAXIMUM_ABBREVIATION_LENGTH = 3;

    /**
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     * @throws OverlappingWorldCalendarMonthDeltaException
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: WorldCalendarSettings::class, inversedBy: 'worldCalendarMonths')]
        #[ORM\JoinColumn(nullable: false)]
        private WorldCalendarSettings $worldCalendarSettings,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_NAME_LENGTH)]
        private string $name,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_ABBREVIATION_LENGTH, unique: true)]
        private string $abbreviation,

        #[ORM\Column(type: 'integer')]
        private int $delta,

        #[ORM\Column(type: 'integer')]
        private int $numberOfDays,

        ?int $id = null,
    ) {
        parent::__construct($id);
        $this->worldCalendarSettings->assertMonthDeltaAvailable($this->delta, $this);
        self::assertValidNumberOfDays($this->numberOfDays);
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

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
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
        self::assertValidNumberOfDays($numberOfDays);
        $this->numberOfDays = $numberOfDays;
    }

    /**
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     */
    private static function assertValidNumberOfDays(int $numberOfDays): void
    {
        if (
            $numberOfDays < self::MINIMUM_NUMBER_OF_DAYS
            || $numberOfDays > self::MAXIMUM_NUMBER_OF_DAYS
        ) {
            throw new InvalidWorldCalendarMonthNumberOfDaysException($numberOfDays);
        }
    }

    /**
     * @throws OverlappingWorldCalendarMonthDeltaException
     * @throws InvalidWorldCalendarMonthNumberOfDaysException
     */
    public static function makeFromDto(WorldCalendarSettings $worldCalendarSettings, WorldCalendarMonthDto $dto): self {
        if ($dto->name === null || $dto->abbreviation === null || $dto->delta === null || $dto->numberOfDays === null) {
            throw new \InvalidArgumentException('A calendar month requires a name, abbreviation, delta, and number of days.');
        }

        return new self(
            worldCalendarSettings: $worldCalendarSettings,
            name: $dto->name,
            abbreviation: $dto->abbreviation,
            delta: $dto->delta,
            numberOfDays: $dto->numberOfDays,
            id: $dto->id
        );
    }
}
