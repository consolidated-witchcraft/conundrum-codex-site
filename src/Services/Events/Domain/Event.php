<?php

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\EventRepository;
use App\Services\Files\Domain\ManagedFile;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[UniqueEntity(fields: 'eventSlug', message: 'Event with this slug already exists')]
class Event extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $title,

        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private string $eventSlug,

        #[ORM\Column(type: 'string', length: 2048)]
        private string $description,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        private ManagedFile $heroImage,

        #[ORM\OneToOne(targetEntity: PostalAddress::class)]
        private ? PostalAddress $location,

        #[ORM\Column]
        private bool $prebookOnly,

        #[ORM\ManyToOne(targetEntity: Organisation::class, inversedBy: 'events')]
        private Organisation $organisation,

        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $startDate,

        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $endDate,

        #[ORM\OneToMany(targetEntity: TicketType::class, mappedBy: 'event')]
        #[ORM\JoinColumn(nullable: false)]
        private Collection $ticketTypes = new ArrayCollection(),
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getLocation(): ?PostalAddress
    {
        return $this->location;
    }

    public function setLocation(?PostalAddress $location): void
    {
        $this->location = $location;
    }

    public function isPrebookOnly(): bool
    {
        return $this->prebookOnly;
    }

    public function setPrebookOnly(bool $prebookOnly): void
    {
        $this->prebookOnly = $prebookOnly;
    }

    public function getEventSlug(): string
    {
        return $this->eventSlug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getHeroImage(): ManagedFile
    {
        return $this->heroImage;
    }

    public function setHeroImage(ManagedFile $heroImage): void
    {
        $this->heroImage = $heroImage;
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
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
