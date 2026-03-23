<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Infrastructure;

use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImageTypeFormatMap>
 */
class ImageTypeFormatMapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTypeFormatMap::class);
    }

    public function findOneByManagedImageType(ManagedImageTypeEnum $managedImageType): ?ImageTypeFormatMap
    {
        $result = $this->createQueryBuilder('imageTypeFormatMap')
            ->where('imageTypeFormatMap.managedImageType = :managedImageType')
            ->setParameter('managedImageType', $managedImageType)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof ImageTypeFormatMap ? $result : null;
    }
}
