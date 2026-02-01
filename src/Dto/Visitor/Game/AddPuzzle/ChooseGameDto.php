<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\AddPuzzle;

use App\Services\Game\Domain\Game;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;

class ChooseGameDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Groups(['basic'])]
        public string $template,

        #[Groups(['basic'])]
        public ? Game $game = null,

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
