<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Service;

use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

final readonly class ParseError
{
    public function __construct(
        public string $message,
        public SourceSpan $span,
        public ?string $raw = null,
    ) {}
}
