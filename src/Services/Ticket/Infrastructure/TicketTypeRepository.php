<?php

namespace App\Services\Ticket\Infrastructure;

use App\Services\Ticket\Domain\TicketType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketType>
 */
class TicketTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketType::class);
    }

}
