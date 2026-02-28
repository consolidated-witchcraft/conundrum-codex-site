<?php

namespace App\Services\PuzzleInstance\Domain\Casebook;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectClueTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectClueTypeRepository::class)]
class CasebookSubjectClueTypeValueObject extends AbstractValueObject
{
    public static function hasDescription(): bool
    {
        return false;
    }
}
