<?php

namespace App\Services\Abuse\Infrastructure;

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbuseReport>
 */
class AbuseReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbuseReport::class);
    }

    /**
     * @return AbuseReport[]
     */
    public function findUnactioned(): array {
        $qb = $this->createQueryBuilder('ar');
        $qb->where($qb->expr()->isNull('ar.checkedDate'));
        return $qb->getQuery()->getResult();
    }

    /**
     * @return AbuseReport[]
     */
    public function getAbuseReportsForReportedUser(User $user, bool $checkedOnly = true): array {
        $qb = $this->createQueryBuilder('ar');

        $qb->where($qb->expr()->eq('ar.reportedUser', ':user'));

        if ($checkedOnly) {
            $qb->andWhere($qb->expr()->isNotNull('ar.checkedDate'))
                ->andWhere($qb->expr()->isNotNull('ar.confirmedDate'));
        }
        $qb->setParameter('user', $user);
        return self::mapArrayResults($qb->getQuery()->getArrayResult());
    }

    /**
     * @return AbuseReport[]
     */
    public function getAbuseReportsByUser(User $user): array {
        $qb = $this->createQueryBuilder('ar');
        $qb
            ->where($qb->expr()->eq('ar.reportingUser', ':user'))
            ->andWhere($qb->expr()->isNotNull('ar.checkedDate'))
            ->andWhere($qb->expr()->isNotNull('ar.confirmedDate'));
        $qb->setParameter('user', $user);
        return self::mapArrayResults($qb->getQuery()->getArrayResult());
    }

    /**
     * @param array<mixed> $results
     * @return array<AbuseReport>
     */
    private static function mapArrayResults(array $results): array {
        $mappedResults = [];
        foreach ($results as $result) {
            if ($result instanceof AbuseReport) {
                $mappedResults[] = $result;
            }
        }
        return $mappedResults;
    }
}
