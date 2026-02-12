<?php

declare(strict_types=1);

namespace App\Services\Orders\Infrastructure;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Domain\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductBySku(string $sku, bool $includeRetired = false): ? Product {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.sku = :sku')
            ->setParameter('sku', $sku);

        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result instanceof Product) {
            if ($includeRetired) {
                return $result;
            }
            return $result->isRetired() ? null : $result;
        }
        return null;
    }
}
