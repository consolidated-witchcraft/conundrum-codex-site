<?php

namespace App\Services\SemanticMarkup\Domain\Nodes;

use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

abstract readonly class AbstractSemanticNode
{

    public function __construct(
        private string $raw,
        private SourceSpan $sourceSpan,
    ) {}

    final public function getSourceSpan(): SourceSpan
    {
        return $this->sourceSpan;
    }

    final public function getRaw(): string
    {
        return $this->raw;
    }
}
