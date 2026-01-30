<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Infrastructure\Casebook\Repository;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractValueObjectRepository<CasebookSubjectType>
 */
class CasebookSubjectTypeRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectType::class);
    }
}
