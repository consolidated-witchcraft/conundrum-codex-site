<?php

namespace App\Dto\Visitor\Game\AddPuzzle;

use Symfony\Component\Validator\Constraints as Assert;

readonly class AddPuzzleStepOneDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $templateSlug,
        #[Assert\NotBlank]
        public string $gameSlug,
        #[Assert\NotBlank]
        public string $puzzleName,
        #[Assert\NotBlank]
        public string $puzzleDescription,
    ) {}
}
