<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WorldCalendarSettings extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\OneToOne(targetEntity: World::class, inversedBy: 'worldCalendarSettings')]
        #[ORM\JoinColumn(nullable: false)]
        private World $world,

        #[ORM\Column(type: 'integer', nullable: false, options: ['default' => 24])]
        private int $hoursInDay = 24,

        ?int $id = null,
    ) {
        parent::__construct($id);
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
}
