<?php

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\OrganisationRepository;
use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrganisationRepository::class)]
#[UniqueEntity('handle')]
class Organisation extends AbstractDomainEntity
{

    /**
     * @param string $name
     * @param string $handle
     * @param User $owner
     * @param Collection<int, Event> $events
     * @param Collection<int, User> $managers
     * @param \DateTimeInterface|null $deactivationDate
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(unique: true)]
        private string $handle,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedFile $organisationLogo,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $owner,

        #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: Event::class)]
        private Collection $events,

        #[ORM\OneToMany(mappedBy: 'organisation', targetEntity: User::class)]
        private Collection $managers,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $deactivationDate = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getOrganisationLogo(): ManagedFile
    {
        return $this->organisationLogo;
    }

    public function setOrganisationLogo(ManagedFile $organisationLogo): void
    {
        $this->organisationLogo = $organisationLogo;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getAllEvents(): Collection
    {
        return $this->events;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getUpcomingEvents(): Collection
    {
        $criteria = new Criteria();
        $eb = Criteria::expr();
        $criteria->where($eb->gt('endDate', new \DateTime()));
        $criteria->orderBy(array("endDate" => Order::Ascending));
        return $this->events;
    }

    public function setEvents(Collection $events): void
    {
        $this->events = $events;
    }

    public function isActive(): bool
    {
        return $this->deactivationDate === null;
    }

    public function deactivate(): void {
        $this->deactivationDate = new \DateTime();
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
