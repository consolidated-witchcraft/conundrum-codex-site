<?php

declare(strict_types=1);

namespace App\Services\Product\Infrastructure;

use App\Services\Product\Domain\ProductVariantStock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductVariantStock>
 */
class ProductVariantStockRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariantStock::class);
    }

}
