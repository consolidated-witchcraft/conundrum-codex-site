<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Casebook;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectTypeRepository::class)]
class CasebookSubjectType extends AbstractValueObject
{
    public static function hasDescription(): bool
    {
        return false;
    }
}
