<?php

declare(strict_types=1);

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\OrganisationRepository;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PostalAddress\Domain\PostalAddress;
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
     * @param ManagedImage $organisationLogo
     * @param User $owner
     * @param Collection<int, Event> $events
     * @param Collection<int, User> $managers
     * @param PostalAddress $postalAddress
     * @param PostalAddress $invoiceAddress
     * @param \DateTimeInterface|null $deactivationDate
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(unique: true)]
        private string $handle,

        #[ORM\OneToOne(targetEntity: ManagedImage::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedImage $organisationLogo,

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private User $owner,

        #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'organisation', cascade: ['persist'], orphanRemoval: true)]
        private Collection $events,

        #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'organisation')]
        private Collection $managers,

        #[ORM\OneToOne(targetEntity: PostalAddress::class, cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
        #[ORM\JoinColumn(nullable: false)]
        private PostalAddress $postalAddress,

        #[ORM\OneToOne(targetEntity: PostalAddress::class, cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
        #[ORM\JoinColumn(nullable: false)]
        private PostalAddress $invoiceAddress,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $deactivationDate = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getInvoiceAddress(): PostalAddress
    {
        return $this->invoiceAddress;
    }

    public function setInvoiceAddress(PostalAddress $invoiceAddress): void
    {
        $this->invoiceAddress = $invoiceAddress;
    }

    public function getPostalAddress(): PostalAddress
    {
        return $this->postalAddress;
    }

    public function setPostalAddress(PostalAddress $postalAddress): void
    {
        $this->postalAddress = $postalAddress;
    }

    public function getOrganisationLogo(): ManagedImage
    {
        return $this->organisationLogo;
    }

    public function setOrganisationLogo(ManagedImage $organisationLogo): void
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

    /** @param Collection<int, Event> $events */
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

    /** @return Collection<int, User> */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    /** @param Collection<int, User> $managers */
    public function setManagers(Collection $managers): void
    {
        $this->managers = $managers;
    }

}
