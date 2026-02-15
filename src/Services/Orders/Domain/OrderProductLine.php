<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Orders\Infrastructure\OrderProductLineRepository;
use App\Services\Product\Domain\ProductVariant;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderProductLineRepository::class)]
class OrderProductLine extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'productOrderLines')]

        private Order          $order,

        #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
        private ProductVariant $productVariant,

        #[ORM\Column(type: 'integer')]
        private int            $quantity,
        ? int                  $id = null
    ) {
        parent::__construct($id);
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProductVariant(): ProductVariant
    {
        return $this->productVariant;
    }

    public function setProductVariant(ProductVariant $productVariant): void
    {
        $this->productVariant = $productVariant;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getTotalCentessimal(): int {
        return $this->getProductVariant()->getPreTaxPriceCentessimal() * $this->getQuantity();
    }

    public function getTotalDecimal(): float {
        return $this->getTotalCentessimal()/100;
    }
}
