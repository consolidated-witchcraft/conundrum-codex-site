<?php

declare(strict_types=1);

namespace App\Services\Orders\Infrastructure;

use App\Services\Orders\Domain\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByOrderReference(string $orderReference): ?Order {

        return $this->createQueryBuilder('o')
            ->where('o.orderReference = :orderReference')
            ->setParameter('orderReference', $orderReference)
            ->getQuery()
            ->getOneOrNullResult();

    }
}
