<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Infrastructure\ProductOrderLineRepository;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductOrderLineRepository::class)]
class ProductOrderLine extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'productOrderLines')]
        private Order $order,

        #[ORM\OneToOne(targetEntity: Product::class)]
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
}
