<?php

declare(strict_types=1);

namespace App\Services\World\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use App\Services\World\Infrastructure\WorldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(
    repositoryClass: WorldRepository::class
)]
#[ORM\Index(name: 'name_idx', columns: ['name'])]
#[UniqueEntity("slug")]
class World extends AbstractDomainEntity
{

    /**
     * @param string $name
     * @param string $description
     * @param string $slug
     * @param User $creator
     * @param Collection<int, User> $players
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'markdown', length: 2198)]
        private string $description,

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private readonly string $slug,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'worldsCreated')]
        private User $creator,

        #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'worldsPlayed')]
        private Collection $players = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->worldCalendarSettings = new WorldCalendarSettings($this);
    }

    #[ORM\OneToOne(mappedBy: 'world', targetEntity: WorldCalendarSettings::class, cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
    private ?WorldCalendarSettings $worldCalendarSettings = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): void
    {
        $this->creator = $creator;
    }

    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function setPlayers(Collection $players): void
    {
        $this->players = $players;
    }

    public function getWorldCalendarSettings(): WorldCalendarSettings
    {
        if ($this->worldCalendarSettings === null) {
            $this->worldCalendarSettings = new WorldCalendarSettings($this);
        }

        return $this->worldCalendarSettings;
    }

    public function setWorldCalendarSettings(WorldCalendarSettings $worldCalendarSettings): void
    {
        $this->worldCalendarSettings = $worldCalendarSettings;
        $worldCalendarSettings->setWorld($this);
    }

    public function isFullyConfigured(): bool {

        //TODO - Put in a test here to prevent a world from having its basic configuration edited if it's not fully configured.
        return false;
    }
}
