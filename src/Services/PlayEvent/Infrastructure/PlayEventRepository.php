<?php

declare(strict_types=1);

namespace App\Services\PlayEvent\Infrastructure;

use App\Services\PlayEvent\Domain\PlayEvent;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlayEvent>
 */
class PlayEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayEvent::class);
    }

    /**
     * @return Collection<int, PlayEvent>
     */
    public function findByUser(User $user): Collection
    {
        return new ArrayCollection(
            $this->createQueryBuilder('playEvent')
                ->andWhere('playEvent.user = :user')
                ->setParameter('user', $user)
                ->orderBy('playEvent.createdAt', 'DESC')
                ->getQuery()
                ->getResult()
        );
    }

    /**
     * @return PlayEvent[]
     */
    public function findAllForUserBetweenDates(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('playEvent')
            ->andWhere('playEvent.user = :user')
            ->andWhere('playEvent.createdAt >= :start')
            ->andWhere('playEvent.createdAt <= :end')
            ->setParameter('user', $user)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('playEvent.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
