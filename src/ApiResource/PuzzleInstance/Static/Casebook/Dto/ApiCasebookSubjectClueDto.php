<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Static\Casebook\Dto;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;

class ApiCasebookSubjectClueDto
{
    final public function __construct(
        public string $title,
        public string $body,
        public string $type,
        public string $typeLabel,
        public ? \DateTimeInterface $updatedAt = null,
        public ? \DateTimeInterface $revealedDate = null,
        public ? int $id = null,
    ) {}

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $clue): static {
        return new static(
            title: $clue->getTitle(),
            body: $clue->getBody(),
            type: $clue->getType()->getHandle(),
            typeLabel: $clue->getType()->getLabel(),
            updatedAt: $clue->getUpdatedAt(),
            revealedDate: $clue->getRevealedDate(),
            id: $clue->getId()
        );
    }
}
