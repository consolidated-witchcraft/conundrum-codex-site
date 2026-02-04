<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\AddPuzzle;

use App\Services\Game\Domain\Game;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

class ChoosePuzzleDto
{
    public function __construct(
        #[Groups(['basic'])]
        public ? string $gameSlug = null,

        #[Assert\NotNull]
        public ? string $templateSlug = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 255)]
        #[Groups(['basic'])]
        public ? string $puzzleName = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 1024)]
        #[Groups(['basic'])]
        public ? string $description = null
    ) {}

}
