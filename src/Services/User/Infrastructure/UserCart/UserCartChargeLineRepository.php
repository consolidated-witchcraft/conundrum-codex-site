<?php

namespace App\Services\User\Infrastructure\UserCart;

use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartChargeLine;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCartChargeLine>
 */
class UserCartChargeLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCartChargeLine::class);
    }
}
