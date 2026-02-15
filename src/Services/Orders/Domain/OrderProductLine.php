<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Orders\Infrastructure\OrderProductLineRepository;
use App\Services\Products\Domain\Product;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderProductLineRepository::class)]
class OrderProductLine extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'productOrderLines')]

        private Order $order,

        #[ORM\ManyToOne(targetEntity: Product::class)]
        private Product $product,

        #[ORM\Column(type: 'integer')]
        private int $quantity,
        ? int $id = null
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

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
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
        return $this->getProduct()->getPreTaxPriceCentessimal() * $this->getQuantity();
    }

    public function getTotalDecimal():float {
        return $this->getTotalCentessimal()/100;
    }
}
