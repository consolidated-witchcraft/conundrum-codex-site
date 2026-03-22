<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Service;

use App\Services\SemanticMarkup\Domain\Nodes\Interfaces\SemanticNodeInterface;

final readonly class ParseResult
{
    /**
     * @param list<SemanticNodeInterface> $nodes
     * @param list<ParseError> $errors
     */
    public function __construct(
        public array $nodes,
        public array $errors,
    ) {}
}
