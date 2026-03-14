<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\World\Domain\Exceptions\InvalidWorldEventDescriptionLengthException;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTimestampException;
use App\Services\World\Domain\Exceptions\InvalidWorldEventTitleLengthException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WorldEvent extends AbstractDomainEntity
{
    public const int MINIMUM_TIMESTAMP = 1;
    public const int MINIMUM_TEXT_LENGTH = 1;
    public const int MAXIMUM_TITLE_LENGTH = 255;
    public const int MAXIMUM_DESCRIPTION_LENGTH = 5000;

    /**
     * @throws InvalidWorldEventDescriptionLengthException
     * @throws InvalidWorldEventTimestampException
     * @throws InvalidWorldEventTitleLengthException
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: World::class, inversedBy: 'worldEvents')]
        #[ORM\JoinColumn(nullable: false)]
        private World $world,

        #[ORM\Column(type: 'bigint')]
        private int $timestamp,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_TITLE_LENGTH)]
        private string $title,

        #[ORM\Column(type: 'string', length: self::MAXIMUM_DESCRIPTION_LENGTH)]
        private string $description,

        ?int $id = null,
    ) {
        parent::__construct($id);
        $this->assertValidTimestamp($this->timestamp);
        $this->assertValidTitle($this->title);
        $this->assertValidDescription($this->description);
    }

    public function getWorld(): World
    {
        return $this->world;
    }

    public function setWorld(World $world): void
    {
        $this->world = $world;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @throws InvalidWorldEventTimestampException
     */
    public function setTimestamp(int $timestamp): void
    {
        $this->assertValidTimestamp($timestamp);
        $this->timestamp = $timestamp;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @throws InvalidWorldEventTitleLengthException
     */
    public function setTitle(string $title): void
    {
        $this->assertValidTitle($title);
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @throws InvalidWorldEventDescriptionLengthException
     */
    public function setDescription(string $description): void
    {
        $this->assertValidDescription($description);
        $this->description = $description;
    }

    /**
     * @throws InvalidWorldEventTimestampException
     */
    private function assertValidTimestamp(int $timestamp): void
    {
        if ($timestamp < self::MINIMUM_TIMESTAMP) {
            throw new InvalidWorldEventTimestampException($timestamp);
        }
    }

    /**
     * @throws InvalidWorldEventTitleLengthException
     */
    private function assertValidTitle(string $title): void
    {
        $titleLength = self::measureTrimmedTextLength($title);
        if ($titleLength < self::MINIMUM_TEXT_LENGTH || $titleLength > self::MAXIMUM_TITLE_LENGTH) {
            throw new InvalidWorldEventTitleLengthException($titleLength);
        }
    }

    /**
     * @throws InvalidWorldEventDescriptionLengthException
     */
    private function assertValidDescription(string $description): void
    {
        $descriptionLength = self::measureTrimmedTextLength($description);
        if ($descriptionLength < self::MINIMUM_TEXT_LENGTH || $descriptionLength > self::MAXIMUM_DESCRIPTION_LENGTH) {
            throw new InvalidWorldEventDescriptionLengthException($descriptionLength);
        }
    }

    private static function measureTrimmedTextLength(string $text): int
    {
        return mb_strlen(trim($text));
    }
}
