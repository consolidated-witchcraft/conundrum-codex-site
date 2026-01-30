<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Domain;

readonly class PuzzleCredit
{
    public function __construct(
        public string $name,
        public string $created,
        public ? string $email = null

    ) {

    }
}
