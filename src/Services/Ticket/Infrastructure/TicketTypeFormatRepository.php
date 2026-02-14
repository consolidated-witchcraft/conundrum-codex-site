<?php

declare(strict_types=1);

namespace App\Services\Ticket\Infrastructure;

use App\Services\Ticket\Domain\TicketTypeFormat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TicketTypeFormat>
 */
class TicketTypeFormatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketTypeFormat::class);
    }

}
