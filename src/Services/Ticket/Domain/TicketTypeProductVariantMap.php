<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Ticket\Domain\ValueObjects\TicketFormat;
use App\Services\Ticket\Infrastructure\TicketTypeProductVariantMapRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketTypeProductVariantMapRepository::class)]
class TicketTypeProductVariantMap extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: TicketType::class, inversedBy: "ticketTypeProductVariantMaps")]
        private TicketType $ticketType,

        #[ORM\OneToOne(targetEntity: ProductVariant::class)]
        private ? ProductVariant $productVariant = null,
        ? int $id = null,
    ) {
        parent::__construct($id);
    }

    public function setTicketType(TicketType $ticketType): void {
        $this->ticketType = $ticketType;
    }
    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }

    public function setProductVariant(ProductVariant $productVariant): void {
        $this->productVariant = $productVariant;
    }

    public function getProductVariant(): ?ProductVariant
    {
        return $this->productVariant;
    }
}
