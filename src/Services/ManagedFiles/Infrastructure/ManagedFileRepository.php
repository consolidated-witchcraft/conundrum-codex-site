<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Infrastructure;

use App\Services\ManagedFiles\Domain\ManagedFile;
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

    /**
     * @param int $firstResult
     * @param int $maxResults
     * @return ManagedFile[]
     */
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

    /**
     * @param array<int, string> $mimeTypes
     * @return ManagedFile[]
     */
    public function findByMimeTypes(array $mimeTypes): array {
        if ($mimeTypes === []) {
            return [];
        }

        return $this->createQueryBuilder('f')
            ->where('f.mimeType IN (:mimeTypes)')
            ->setParameter('mimeTypes', $mimeTypes)
            ->getQuery()
            ->getResult();
    }

    public function findOrphans(): iterable {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->where('f.isOrphaned = :is_orphaned')
            ->setParameter('is_orphaned', true)
            ->getQuery()
            ->getResult();
    }

    public function findDuplicates(): iterable {
        $qb = $this->createQueryBuilder('f');

        $subQuery = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('duplicate.fileHash')
            ->from(ManagedFile::class, 'duplicate')
            ->where('duplicate.fileHash IS NOT NULL')
            ->groupBy('duplicate.fileHash')
            ->having('COUNT(duplicate.id) > 1');

        return $qb
            ->where($qb->expr()->in('f.fileHash', $subQuery->getDQL()))
            ->orderBy('f.fileHash', 'ASC')
            ->addOrderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<int, array{uploaderId: int, path: string, fileHash: string, duplicateCount: int}>
     */
    public function findDuplicateGroups(): array
    {
        $results = $this->createQueryBuilder('f')
            ->select('IDENTITY(f.uploader) AS uploaderId, f.path AS path, f.fileHash AS fileHash, COUNT(f.id) AS duplicateCount')
            ->where('f.fileHash IS NOT NULL')
            ->groupBy('f.uploader, f.path, f.fileHash')
            ->having('COUNT(f.id) > 1')
            ->orderBy('duplicateCount', 'DESC')
            ->getQuery()
            ->getArrayResult();

        return array_map(static function (array $result): array {
            return [
                'uploaderId' => (int) $result['uploaderId'],
                'path' => (string) $result['path'],
                'fileHash' => (string) $result['fileHash'],
                'duplicateCount' => (int) $result['duplicateCount'],
            ];
        }, $results);
    }

    /**
     * @return ManagedFile[]
     */
    public function findDuplicateGroup(int $uploaderId, string $path, string $fileHash): array
    {
        return $this->createQueryBuilder('f')
            ->where('IDENTITY(f.uploader) = :uploaderId')
            ->andWhere('f.path = :path')
            ->andWhere('f.fileHash = :fileHash')
            ->setParameter('uploaderId', $uploaderId)
            ->setParameter('path', $path)
            ->setParameter('fileHash', $fileHash)
            ->orderBy('f.updatedAt', 'DESC')
            ->addOrderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
