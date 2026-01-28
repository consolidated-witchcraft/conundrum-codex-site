<?php

namespace App\Services\BugReport\Infrastructure;

use App\Services\BugReport\Domain\BugReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BugReport>
 */
class BugReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugReport::class);
    }

    /**
     * @return BugReport[]
     */
    public function findUnactioned(): array {
        $qb  = $this->createQueryBuilder('br');
        return $qb->where($qb->expr()->isNull('br.actionedAt'))
            ->andWhere($qb->expr()->isNull('br.closedAt'))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array<mixed> $results
     * @return array<BugReport>
     */
    public static function mapArrayResult(array $results): array {
        $mappedResults = [];
        foreach ($results as $result) {
            if ($result instanceof BugReport) {
                $mappedResults[] = $result;
            }
        }
        return $mappedResults;
    }
}
