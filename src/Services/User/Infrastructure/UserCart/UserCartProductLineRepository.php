<?php

namespace App\Services\User\Infrastructure\UserCart;

use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCartProductLine>
 */
class UserCartProductLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCartProductLine::class);
    }
}
