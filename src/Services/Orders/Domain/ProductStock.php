<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Orders\Infrastructure\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductStock::class)]
#[UniqueEntity(fields: ['product'], message: 'There is already a product stock object for this product.')]
class ProductStock extends AbstractDomainEntity
{
    /**
     * @param int $initialStockLevel
     * @param int $currentStockLevel
     * @param Collection<int, Product> $products
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'integer', nullable: false)]
        private int $initialStockLevel,

        #[ORM\Column(type: 'integer', nullable: false)]
        private int $currentStockLevel,

        #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'productStock', cascade: ['persist', 'remove', 'refresh'], fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: false)]
        private Collection $products = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function addProduct(Product $product): void {
        $this->products->add($product);
        $product->setProductStock($this);
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getInitialStockLevel(): int {
        return $this->initialStockLevel;
    }

    public function getCurrentStockLevel(): int {
        return $this->currentStockLevel;
    }

    public function setInitialStockLevel(int $initialStockLevel): void {
        $this->initialStockLevel = $initialStockLevel;
    }

    public function decreaseCurrentStockLevel(int $quantity): void {
        $this->currentStockLevel -= $quantity;
    }

    public function increaseCurrentStockLevel(int $quantity): void {
        $this->currentStockLevel += $quantity;
    }

    public function setCurrentStockLevel(int $currentStockLevel): void {
        $this->currentStockLevel = $currentStockLevel;
    }
}
