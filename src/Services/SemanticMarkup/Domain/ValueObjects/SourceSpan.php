<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\ValueObjects;

final readonly class SourceSpan
{
    public function __construct(
        public int $start,
        public int $end,
    ) {}
}
