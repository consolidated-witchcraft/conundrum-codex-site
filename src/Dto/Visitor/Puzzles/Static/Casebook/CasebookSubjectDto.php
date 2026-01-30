<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;
use Doctrine\Common\Collections\Collection;

class CasebookSubjectDto
{
    /**
     * @param Casebook $casebook
     * @param CasebookSubjectType|null $type
     * @param CasebookSubjectClueDto[] $clues
     * @param string|null $name
     * @param string|null $description
     * @param string|null $image
     * @param bool|null $revealed
     */
   final public function __construct(
        public readonly Casebook $casebook,
        public ? CasebookSubjectType $type = null,
        public array $clues = [],
        public ? string $name = null,
        public ? string $description = null,
        public ? string $image = null,
        public ? bool $revealed = false,
    ) {}

    public static function makeFromCasebookSubject(CasebookSubject $casebookSubject): self {
        return new self(
            casebook: $casebookSubject->getCasebook(),
            type: $casebookSubject->getCasebookSubjectType(),
            clues: self::mapClues($casebookSubject->getCasebookSubjectClues()),
            name: $casebookSubject->getName(),
            description: $casebookSubject->getDescription(),
            image: $casebookSubject->getCasebookSubjectImageFile()->getPath(),
            revealed: $casebookSubject->isRevealed(),
        );
    }

    /**
     * @param Collection<int, CasebookSubjectClue> $clues
     *
     * @return CasebookSubjectClueDto[]
     */
    private static function mapClues(Collection $clues): array {

        $arrayClues = [];
        foreach ($clues as $clue) {
            $arrayClues[] = CasebookSubjectClueDto::makeFromCasebookSubjectClue($clue);
        }
        return $arrayClues;

    }
}
