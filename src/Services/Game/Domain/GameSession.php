<?php

namespace App\Services\Game\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Game\Infrastructure\GameSessionRepository;
use App\Services\ManagedImages\Domain\ManagedImage;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GameSessionRepository::class)]
class GameSession extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $sessionDate,

        #[ORM\ManyToOne(inversedBy: 'gameSessions')]
        #[ORM\JoinColumn(nullable: false)]
        private Game $game,

        #[ORM\Column(length: 255)]
        private string $title,

        #[Groups(['extended'])]
        #[ORM\OneToOne(targetEntity: ManagedImage::class)]
        #[ORM\JoinColumn(nullable: true)]
        private ?ManagedImage $sessionImage = null,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ? string $description = null,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ?\DateTimeInterface $cancellationDate = null,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getSessionImage(): ?ManagedImage
    {
        return $this->sessionImage;
    }

    public function setSessionImage(?ManagedImage $sessionImage): void
    {
        $this->sessionImage = $sessionImage;
    }


    public function getSessionDate(): \DateTimeInterface
    {
        return $this->sessionDate;
    }

    public function setSessionDate(\DateTimeInterface $sessionDate): void
    {
        $this->sessionDate = $sessionDate;
    }

    public function getCancellationDate(): ?\DateTimeInterface
    {
        return $this->cancellationDate;
    }


    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): void
    {
        $this->game = $game;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function cancel(): void {
        $this->cancellationDate = new \DateTimeImmutable();
    }

    public function isCancelled(): bool {
        return $this->cancellationDate !== null;
    }
}
