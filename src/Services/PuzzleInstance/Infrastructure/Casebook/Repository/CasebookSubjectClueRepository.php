<?php

namespace App\Services\PuzzleInstance\Infrastructure\Casebook\Repository;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasebookSubjectClue>
 */
class CasebookSubjectClueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectClue::class);
    }
}
