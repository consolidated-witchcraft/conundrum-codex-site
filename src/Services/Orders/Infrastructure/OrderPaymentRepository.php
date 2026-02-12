<?php

declare(strict_types=1);

namespace App\Services\Orders\Infrastructure;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Domain\OrderPayment;
use App\Services\Orders\Domain\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderPayment>
 */
class OrderPaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderPayment::class);
    }
}
