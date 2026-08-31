<?php

declare(strict_types=1);

namespace App\Services\Game\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a game with this slug. Please choose another one.')]
#[ORM\Index(name: 'gm_id', columns: ['games_master_id'])]
class Game extends AbstractDomainEntity
{
    private const int MAX_UPCOMING_SESSIONS = 5;
    /**
     * @var Collection<int, GameInvitation>
     */
    #[ORM\OneToMany(targetEntity: GameInvitation::class, mappedBy: 'game', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gameInvitations;

    /**
     * @var Collection<int, PuzzleInstance>
     */
    #[ORM\OneToMany(targetEntity: PuzzleInstance::class, mappedBy: 'game', fetch: 'EAGER', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $dynamicPuzzleInstances;

    /**
    * @var Collection<int, User> $players
     */
    #[Groups(['extended'])]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'games', fetch: 'EAGER')]
    private Collection $players;

    /**
     * @var Collection<int, GameSession>
     */
    #[Groups(['extended'])]
    #[ORM\OneToMany(targetEntity: GameSession::class, mappedBy: 'game', orphanRemoval: true)]
    private Collection $gameSessions;

    /**
     * @param string $name
     * @param string $slug
     * @param string $description
     * @param User $gamesMaster
     * @param \DateTimeInterface|null $archivedDate
     * @param ManagedImage|null $heroImageFile
     * @param Collection|null $staticPuzzleInstances
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 255)]
        #[Groups(['basic'])]
        private string $name,

        #[Groups(['basic'])]
        #[ORM\Column(type: 'non_empty_string', length: 255, unique: true)]
        private string $slug,

        #[Groups(['extended'])]
        #[ORM\Column(type: 'non_empty_string', length: 1024)]
        private string $description,

        #[ORM\ManyToOne(inversedBy: 'gamesMastered')]
        #[ORM\JoinColumn(nullable: false)]
        #[Groups(['extended'])]
        private User $gamesMaster,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $archivedDate = null,

        #[Groups(['extended'])]
        #[ORM\OneToOne(targetEntity: ManagedImage::class)]
        private ?ManagedImage $heroImageFile = null,

        /**
         * @var Collection<int, StaticPuzzleInstanceInterface>
         */
        private ? Collection $staticPuzzleInstances = null,

        ?int $id = null
    ) {
        parent::__construct($id);
        $this->gameInvitations = new ArrayCollection();
        $this->dynamicPuzzleInstances = new ArrayCollection();
        $this->players = new ArrayCollection();

        if ($this->staticPuzzleInstances === null) {
            $this->staticPuzzleInstances = new ArrayCollection();
        }
        $this->gameSessions = new ArrayCollection();

    }

    public function getHeroImageFile(): ?ManagedImage
    {
        return $this->heroImageFile;
    }

    public function setHeroImageFile(ManagedImage $heroImageFile): void
    {
        $this->heroImageFile = $heroImageFile;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

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

    public function getGamesMaster(): User
    {
        return $this->gamesMaster;
    }

    public function setGamesMaster(User $gamesMaster): void
    {
        $this->gamesMaster = $gamesMaster;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(User $player): void
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
        }
    }

    public function removePlayer(User $player): void
    {
        $this->players->removeElement($player);
    }

    /**
     * @return Collection<int, GameInvitation>
     */
    public function getGameInvitations(): Collection
    {
        return $this->gameInvitations;
    }

    /**
     * @return Collection<int, PuzzleInstance>
     */
    public function getDynamicPuzzleInstances(): Collection
    {
        return $this->dynamicPuzzleInstances;
    }

    /**
     * @param ArrayCollection<int, StaticPuzzleInstanceInterface> $staticPuzzles
     * @return void
     */
    public function setStaticPuzzleInstances(ArrayCollection $staticPuzzles): void
    {
        $this->staticPuzzleInstances = $staticPuzzles;
    }

    /**
     * @return Collection<int, StaticPuzzleInstanceInterface>
     */
    public function getStaticPuzzleInstances(): Collection {
        return $this->staticPuzzleInstances ?? new ArrayCollection();
    }

    /**
     * @return Collection<int, PuzzleInstanceInterface>
     */
    public function getPuzzleInstances(): Collection {
        /**
         * @var ArrayCollection<int, PuzzleInstanceInterface> $puzzlesInstances
         */
        $puzzlesInstances = new ArrayCollection();
        foreach ($this->getDynamicPuzzleInstances() as $instance) {
            $puzzlesInstances->add($instance);
        }

        foreach ($this->getStaticPuzzleInstances() as $instance) {
            $puzzlesInstances->add($instance);
        }
        return $puzzlesInstances;
    }

    /**
     * @return Collection<int, PuzzleInstanceInterface>
     */
    public function getPublishedPuzzleInstances(): Collection {
        return $this->getPuzzleInstances()->filter(function ($instance) {
            return $instance->isPublished();
        });
    }

    public function getArchivedDate(): ?\DateTimeInterface {
        return $this->archivedDate;
    }

    public function archive(): void {
        $this->archivedDate = new \DateTime();
    }

    public function unArchive(): void {
        $this->archivedDate = null;
    }

    public function isArchived(): bool {
        return $this->archivedDate !== null;
    }

    /**
     * @return Collection<int, GameSession>
     */
    public function getGameSessions(): Collection
    {
        return $this->gameSessions;
    }

    /** @return Collection<int, GameSession> */
    public function getUpcomingGameSessions(): Collection {
        $now = new \DateTime();
        $filteredSessions = $this->gameSessions->filter(function (GameSession $gameSession) use ($now) {
            return $gameSession->getSessionDate() >= $now;
        });
        $criteria = (new Criteria(accessRawFieldValues: true))
            ->orderBy(array("sessionDate" => Order::Ascending))
            ->setMaxResults(self::MAX_UPCOMING_SESSIONS);
        return $filteredSessions->matching($criteria);
    }

    public function addGameSession(GameSession $gameSession): static
    {
        if (!$this->gameSessions->contains($gameSession)) {
            $this->gameSessions->add($gameSession);
            $gameSession->setGame($this);
        }

        return $this;
    }

    public function removeGameSession(GameSession $gameSession): static
    {
        $this->gameSessions->removeElement($gameSession);

        return $this;
    }
}
