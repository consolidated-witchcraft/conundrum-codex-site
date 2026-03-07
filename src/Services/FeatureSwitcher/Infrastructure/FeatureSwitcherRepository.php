<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Infrastructure;

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FeatureSwitcher>
 */
class FeatureSwitcherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FeatureSwitcher::class);
    }

    /**
     * @param string $handle
     * @return FeatureSwitcher
     */
    final public function findByHandle(string $handle):  ? FeatureSwitcher {
        return $this->createQueryBuilder('v')
            ->where('v.handle = :handle')
            ->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Collection<FeatureSwitcher>
     */
    public function findAllFeatureSwitchers(): Collection {
        $results = $this->findAll();
        return new ArrayCollection($results);
    }

}
