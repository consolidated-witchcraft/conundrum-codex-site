<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Infrastructure\Casebook\Repository;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueTypeValueObject;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractValueObjectRepository<CasebookSubjectClueTypeValueObject>
 */
class CasebookSubjectClueTypeRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectClueTypeValueObject::class);
    }
}
