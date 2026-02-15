<?php

namespace App\Services\Products\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Products\Domain\Exceptions\InsufficientStockException;

class ProductVariantStock extends AbstractDomainEntity
{
    public function __construct(
        private readonly ProductVariant $productVariant,
        private int $stockLevel,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function increaseStockLevel(int $stockToAdd): void {
        $this->stockLevel += $stockToAdd;
    }

    public function decreaseStockLevel(int $stockToRemove): void {
        if ($stockToRemove > $this->stockLevel) {
            throw new InsufficientStockException(
                $this->productVariant->getSku(),
                $this->getStockLevel(),
                $stockToRemove
            );
        }
        $this->stockLevel -= $stockToRemove;
    }

    public function getStockLevel(): int {
        return $this->stockLevel;
    }

    public function setStockLevel(int $stockLevel): void {
        $this->stockLevel = $stockLevel;
    }


}
