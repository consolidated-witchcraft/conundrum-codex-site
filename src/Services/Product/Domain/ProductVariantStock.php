<?php

namespace App\Services\Product\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Product\Domain\Exceptions\InsufficientStockException;
use App\Services\Product\Infrastructure\ProductVariantRepository;
use App\Services\Product\Infrastructure\ProductVariantStockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductVariantStockRepository::class)]
class ProductVariantStock extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'integer')]
        private int $stockLevel,

        #[ORM\OneToMany(targetEntity: ProductVariant::class, mappedBy: 'productVariantStock')]
        #[ORM\JoinColumn(nullable: false)]
        private Collection $productVariants = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function increaseStockLevel(int $stockToAdd): void {
        $this->stockLevel += $stockToAdd;
    }

    /**
     * @throws InsufficientStockException
     */
    public function decreaseStockLevel(int $stockToRemove): void {
        if ($stockToRemove > $this->stockLevel) {
            throw new InsufficientStockException(
                $this->productVariants->first()->getSku(),
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

    /**
     * @return Collection<int, ProductVariant>
     */
    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    /**
     * @param Collection<int, ProductVariant> $productVariants
     * @return void
     */
    public function setProductVariants(Collection $productVariants): void
    {
        $this->productVariants = $productVariants;
    }

}
