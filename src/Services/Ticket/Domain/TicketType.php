<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Domain\Event;
use App\Services\Orders\Domain\Product;
use App\Services\Ticket\Infrastructure\TicketTypeRepository;
use App\Validator as CustomAssert;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketTypeRepository::class)]
class TicketType extends AbstractDomainEntity
{

    /**
     * @param string $name
     * @param string $slug
     * @param string $description
     * @param int $priceCentessimal
     * @param Collection<int, TicketTypeFormat> $ticketTypeFormats
     * @param Event $event
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column]
        #[CustomAssert\TicketTypeSlugUniqueWithinEventConstraint]
        private readonly string $slug,

        #[ORM\Column(length: 255)]
        private string $description,

        #[ORM\Column(type: 'integer')]
        private int $priceCentessimal,

        #[ORM\OneToMany(targetEntity: TicketTypeFormat::class, mappedBy: 'ticketType', cascade: ['persist', 'refresh', 'refresh'])]
        private Collection $ticketTypeFormats,

        #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'ticketTypes')]
        private readonly Event $event,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getTicketTypeFormats(): Collection
    {
        return $this->ticketTypeFormats;
    }

    public function getSlug(): string
    {
        return $this->slug;
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

    public function getPriceCentessimal(): int
    {
        return $this->priceCentessimal;
    }

    public function setPriceCentessimal(int $priceCentessimal): void
    {
        $this->priceCentessimal = $priceCentessimal;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getPriceDecimal(): float {
        return $this->priceCentessimal/100;
    }
    public function getProduct(): Product {
        return $this->product;
    }


}
