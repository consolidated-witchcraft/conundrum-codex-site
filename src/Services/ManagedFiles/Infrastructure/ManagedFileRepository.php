<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Infrastructure;

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\User\Domain\User;
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

    /** @return iterable<ManagedFile> */
    public function findOrphans(): iterable {
        return $this->createQueryBuilder('f')
            ->select('f')
            ->where('f.isOrphaned = :is_orphaned')
            ->setParameter('is_orphaned', true)
            ->getQuery()
            ->getResult();
    }

    /** @return iterable<ManagedFile> */
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

    public function findOneByFileHashAndUploader(string $fileHash, User $uploader): ?ManagedFile
    {
        $result = $this->createQueryBuilder('f')
            ->where('f.fileHash = :fileHash')
            ->andWhere('f.uploader = :uploader')
            ->setParameter('fileHash', $fileHash)
            ->setParameter('uploader', $uploader)
            ->orderBy('f.updatedAt', 'DESC')
            ->addOrderBy('f.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof ManagedFile ? $result : null;
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

        /** @var list<array<string, mixed>> $results */
        return array_values(array_map(static function (array $result): array {
            $uploaderId = $result['uploaderId'] ?? null;
            $path = $result['path'] ?? null;
            $fileHash = $result['fileHash'] ?? null;
            $duplicateCount = $result['duplicateCount'] ?? null;

            if (
                (!is_int($uploaderId) && (!is_string($uploaderId) || !ctype_digit($uploaderId)))
                || !is_string($path)
                || !is_string($fileHash)
                || (!is_int($duplicateCount) && (!is_string($duplicateCount) || !ctype_digit($duplicateCount)))
            ) {
                throw new \UnexpectedValueException('Doctrine returned an invalid managed-file duplicate group.');
            }

            return [
                'uploaderId' => (int) $uploaderId,
                'path' => $path,
                'fileHash' => $fileHash,
                'duplicateCount' => (int) $duplicateCount,
            ];
        }, $results));
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
