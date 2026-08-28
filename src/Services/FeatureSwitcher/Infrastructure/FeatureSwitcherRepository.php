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

    final public function findByHandle(string $handle):  ? FeatureSwitcher {
        $result = $this->createQueryBuilder('v')
            ->where('v.handle = :handle')
            ->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult();

        return $result instanceof FeatureSwitcher ? $result : null;
    }

    /**
     * @return Collection<int, FeatureSwitcher>
     */
    public function findAllFeatureSwitchers(): Collection {
        $results = $this->findAll();
        return new ArrayCollection($results);
    }

}
