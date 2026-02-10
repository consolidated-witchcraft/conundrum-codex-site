<?php

namespace App\Services\Events\Infrastructure;

use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Domain\TicketType;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Organisation>
 */
class OrganisationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organisation::class);
    }

    /**
     * @param User $user
     * @return Organisation[]
     */
    public function getOrganisationsByManager(User $user, bool $includeDeactivated = false): array {
        $qb = $this->createQueryBuilder('o')
            ->select('o')
            ->leftJoin('o.managers', 'om')
            ->where('o.owner = :user')
            ->orWhere(
                'om = :user'
            )
            ->setParameter('user', $user);
        if (!$includeDeactivated) {
            $qb->andWhere('o.deactivationDate is NULL');
        }
        return $qb->getQuery()->getResult();

    }
}
