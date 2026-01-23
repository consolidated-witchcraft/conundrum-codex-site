<?php

declare(strict_types=1);

namespace App\Services\Files\Infrastructure;

use App\Services\Files\Domain\ManagedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

}
