<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Orders\Infrastructure\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductStock::class)]
#[UniqueEntity(fields: ['product'], message: 'There is already a product stock object for this product.')]
class ProductStock extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\OneToOne(targetEntity: Product::class, mappedBy: 'productStock', cascade: ['persist', 'remove'], fetch: 'EAGER')]
        private readonly Product $product,
        #[ORM\Column(type: 'integer', nullable: false)]
        private int $initialStockLevel,

        #[ORM\Column(type: 'integer', nullable: false)]
        private int $currentStockLevel,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getProduct(): Product
    {
        return $this->product;
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
