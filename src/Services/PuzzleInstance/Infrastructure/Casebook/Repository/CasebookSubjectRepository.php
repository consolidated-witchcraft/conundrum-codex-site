<?php

namespace App\Services\PuzzleInstance\Infrastructure\Casebook\Repository;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasebookSubject>
 */
class CasebookSubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubject::class);
    }

}
