<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use Symfony\Component\Validator\Constraints as Assert;

class CasebookDto
{
    final public function __construct(
        public readonly string $puzzleName,

        #[Assert\Length(min: 10, max:1024)]
        public ? string $heroImage = null,

        #[Assert\NotBlank()]
        #[Assert\Length(min: 10, max:1024)]
        public ? string $brief = null
    ) {}

    public static function makeFromCasebook(Casebook $casebook): static {
        return new static(
            puzzleName: $casebook->getName()
        );
    }
}
