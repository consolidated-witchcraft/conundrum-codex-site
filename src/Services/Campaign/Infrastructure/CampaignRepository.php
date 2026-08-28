<?php

declare(strict_types=1);

namespace App\Services\Campaign\Infrastructure;

use App\Services\Campaign\Domain\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Campaign>
 */
class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    public function findOneBySlug(string $slug): ? Campaign {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }
}
