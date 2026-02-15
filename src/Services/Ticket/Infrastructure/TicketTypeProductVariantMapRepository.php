<?php

declare(strict_types=1);

namespace App\Services\Ticket\Infrastructure;

use App\Services\Ticket\Domain\TicketTypeProductVariantMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketTypeProductVariantMap>
 */
class TicketTypeProductVariantMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketTypeProductVariantMap::class);
    }

}
