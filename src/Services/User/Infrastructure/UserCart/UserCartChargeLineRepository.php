<?php

declare(strict_types=1);

namespace App\Services\User\Infrastructure\UserCart;

use App\Services\User\Domain\UserCart\UserCartCharge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCartCharge>
 */
class UserCartChargeLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCartCharge::class);
    }
}
