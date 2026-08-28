<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Dto\Visitor\Worlds\WorldCalendar\WorldCalendarDayOfWeekDto;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekAbbreviationException;
use App\Services\World\Domain\Exceptions\DuplicateWorldCalendarDayOfWeekNameException;
use App\Services\World\Domain\Exceptions\InvalidWorldCalendarDayOfWeekAbbreviationException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\Table(name: 'world_calendar_day_of_week')]
#[ORM\UniqueConstraint(name: 'world_calendar_day_of_week_settings_name_unique', columns: ['world_calendar_settings_id', 'name'])]
#[ORM\UniqueConstraint(name: 'world_calendar_day_of_week_settings_abbreviation_unique', columns: ['world_calendar_settings_id', 'abbreviation'])]
#[UniqueEntity(fields: ['worldCalendarSettings', 'name'])]
#[UniqueEntity(fields: ['worldCalendarSettings', 'abbreviation'])]
class WorldCalendarDayOfWeek extends AbstractDomainEntity
{
    public const int MINIMUM_ABBREVIATION_LENGTH = 1;
    public const int MAXIMUM_ABBREVIATION_LENGTH = 4;
    private const int MAXIMUM_NAME_LENGTH = 255;

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     * @throws InvalidWorldCalendarDayOfWeekAbbreviationException
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: WorldCalendarSettings::class, inversedBy: 'daysOfWeek')]
        #[ORM\JoinColumn(nullable: false)]
        private WorldCalendarSettings $worldCalendarSettings,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_NAME_LENGTH)]
        private string $name,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_ABBREVIATION_LENGTH)]
        private string $abbreviation,

        ?int $id = null,
    ) {
        parent::__construct($id);
        self::assertValidAbbreviation($this->abbreviation);
        $this->worldCalendarSettings->assertDayOfWeekNameAvailable($this->name, $this);
        $this->worldCalendarSettings->assertDayOfWeekAbbreviationAvailable($this->abbreviation, $this);
    }

    public function getWorldCalendarSettings(): WorldCalendarSettings
    {
        return $this->worldCalendarSettings;
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     */
    public function setWorldCalendarSettings(WorldCalendarSettings $worldCalendarSettings): void
    {
        $worldCalendarSettings->assertDayOfWeekNameAvailable($this->name, $this);
        $worldCalendarSettings->assertDayOfWeekAbbreviationAvailable($this->abbreviation, $this);
        $this->worldCalendarSettings = $worldCalendarSettings;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     */
    public function setName(string $name): void
    {
        $this->worldCalendarSettings->assertDayOfWeekNameAvailable($name, $this);
        $this->name = $name;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws InvalidWorldCalendarDayOfWeekAbbreviationException
     */
    public function setAbbreviation(string $abbreviation): void
    {
        self::assertValidAbbreviation($abbreviation);
        $this->worldCalendarSettings->assertDayOfWeekAbbreviationAvailable($abbreviation, $this);
        $this->abbreviation = $abbreviation;
    }

    /**
     * @throws InvalidWorldCalendarDayOfWeekAbbreviationException
     */
    private static function assertValidAbbreviation(string $abbreviation): void
    {
        if (
            mb_strlen($abbreviation) < self::MINIMUM_ABBREVIATION_LENGTH
            || mb_strlen($abbreviation) > self::MAXIMUM_ABBREVIATION_LENGTH
        ) {
            throw new InvalidWorldCalendarDayOfWeekAbbreviationException($abbreviation);
        }
    }

    /**
     * @throws DuplicateWorldCalendarDayOfWeekAbbreviationException
     * @throws DuplicateWorldCalendarDayOfWeekNameException
     * @throws InvalidWorldCalendarDayOfWeekAbbreviationException
     */
    public static function makeFromDto(WorldCalendarSettings $worldCalendarSettings, WorldCalendarDayOfWeekDto $dto): self {
        if ($dto->name === null || $dto->abbreviation === null) {
            throw new \InvalidArgumentException('A calendar day requires a name and abbreviation.');
        }

        return new self(
            worldCalendarSettings: $worldCalendarSettings,
            name: $dto->name,
            abbreviation: $dto->abbreviation,
            id: $dto->id
        );
    }
}
