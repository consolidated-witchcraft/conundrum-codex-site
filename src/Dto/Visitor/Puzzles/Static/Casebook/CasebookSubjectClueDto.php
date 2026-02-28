<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueTypeValueObject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;
use Doctrine\Common\Collections\ArrayCollection;

class CasebookSubjectClueDto
{
    final public function __construct(
        public ? string                             $title = null,
        public ? string                             $body = null,
        public ? CasebookSubjectClueTypeValueObject $type = null,
        public ? int                                $id = null
    ) {
    }

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $casebookSubjectClue): CasebookSubjectClueDto {
        return new static(
            title: $casebookSubjectClue->getTitle(),
            body: $casebookSubjectClue->getBody(),
            type: $casebookSubjectClue->getType(),
            id: $casebookSubjectClue->getId()
        );
    }

    public function isBlank(): bool {
        return ($this->title === null && $this->body === null);
    }
}
