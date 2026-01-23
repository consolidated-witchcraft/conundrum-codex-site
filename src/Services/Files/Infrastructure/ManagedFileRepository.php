<?php

declare(strict_types=1);

namespace App\Services\Files\Infrastructure;

use App\Services\Files\Domain\ManagedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ManagedFile>
 */
class ManagedFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManagedFile::class);
    }

    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('f')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('f.createdAt', 'ASC');

        return new Paginator($qb->getQuery());
    }

    public function getFilesCount(): int {
        $result = $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return (int) $result;
    }
}
