<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Infrastructure;

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ManagedImage>
 */
class ManagedImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ManagedImage::class);
    }

    /**
     * @param int $firstResult
     * @param int $maxResults
     * @return ManagedImage[]
     */
    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('f')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('f.createdAt', 'ASC');

        return new Paginator($qb->getQuery());
    }

    public function getImagesCount(): int {
        $result = $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return (int) $result;
    }

    public function findOneBySourceImage(ManagedFile $sourceImage): ?ManagedImage
    {
        $result = $this->createQueryBuilder('i')
            ->where('i.sourceImage = :sourceImage')
            ->setParameter('sourceImage', $sourceImage)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof ManagedImage ? $result : null;
    }

    /**
     * @return ManagedImage[]
     */
    public function findAllByDescriptionSuffixAndType(string $descriptionSuffix, ManagedImageTypeEnum $type): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.description LIKE :description')
            ->andWhere('i.type = :type')
            ->setParameter('description', '%'.$descriptionSuffix)
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}
