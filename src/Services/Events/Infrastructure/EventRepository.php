<?php

namespace App\Services\Events\Infrastructure;

use App\Services\Events\Domain\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[]
     */
    public function getUpcomingEvents(): array {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e')
            ->where($qb->expr()->gt('e.startDate', ':now'));
        $qb->setParameter('now', new \DateTime());
        return $qb->getQuery()->getResult();
    }
}
