<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Domain\Event;
use App\Services\Orders\Domain\Product;
use App\Services\Ticket\Infrastructure\TicketTypeFormatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketTypeFormatRepository::class)]
class TicketTypeFormat extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column]
        private readonly string $name,

        #[ORM\Column(unique: true)]
        private readonly string $slug,

        #[ORM\ManyToOne(targetEntity: TicketType::class, inversedBy: "ticketTypeFormats")]
        private ? TicketType $ticketType = null,

        #[ORM\OneToOne(targetEntity: Product::class)]
        private ? Product $product = null,
        ? int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setTicketType(TicketType $ticketType): void {
        $this->ticketType = $ticketType;
    }
    public function getTicketType(): ?TicketType
    {
        return $this->ticketType;
    }

    public function setProduct(Product $product): void {
        $this->product = $product;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }


}
