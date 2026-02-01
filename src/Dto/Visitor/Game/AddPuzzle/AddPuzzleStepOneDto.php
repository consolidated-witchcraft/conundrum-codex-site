<?php

namespace App\Dto\Visitor\Game\AddPuzzle;

readonly class AddPuzzleStepOneDto
{
    public function __construct(
        public string $templateSlug,
        public string $gameSlug,
        public string $puzzleName,
        public string $puzzleDescription,
    ) {}
}
