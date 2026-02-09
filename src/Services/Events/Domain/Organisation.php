<?php

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\OrganisationRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[UniqueEntity('handle')]
class Organisation extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column]
        private bool $active,

        #[ORM\Column(unique: true)]
        private string $handle,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $owner,

        #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Event::class)]
        private Collection $events,

        #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: User::class)]
        private Collection $managers,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function setEvents(Collection $events): void
    {
        $this->events = $events;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function setManagers(Collection $managers): void
    {
        $this->managers = $managers;
    }

}
