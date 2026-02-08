<?php

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\EventRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $title,

        #[ORM\Column]
        private bool $invitationOnly,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $owner,

        #[ORM\Column]
        private \DateTimeInterface $startDate,

        #[ORM\Column]
        private \DateTimeInterface $endDate,

        #[ORM\OneToOne(targetEntity: TicketType::class, inversedBy: 'event')]
        #[ORM\JoinColumn(nullable: false)]
        private Collection $ticketTypes,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isInvitationOnly(): bool
    {
        return $this->invitationOnly;
    }

    public function setInvitationOnly(bool $invitationOnly): void
    {
        $this->invitationOnly = $invitationOnly;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getTicketTypes(): Collection
    {
        return $this->ticketTypes;
    }

    public function setTicketTypes(Collection $ticketTypes): void
    {
        $this->ticketTypes = $ticketTypes;
    }
}
