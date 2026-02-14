<?php

declare(strict_types=1);

namespace App\Services\Orders\Infrastructure;

use App\Services\Orders\Domain\OrderTax;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderTax>
 */
class OrderTaxRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderTax::class);
    }
}
