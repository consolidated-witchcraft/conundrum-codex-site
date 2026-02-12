<?php

namespace App\Services\User\Infrastructure\UserCart;

use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCartLine>
 */
class UserCartLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCartLine::class);
    }
}
