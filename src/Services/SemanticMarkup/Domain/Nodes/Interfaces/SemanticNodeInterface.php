<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Nodes\Interfaces;

use App\Services\SemanticMarkup\Domain\Nodes\Enums\SemanticNodeTypeEnum;
use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

interface SemanticNodeInterface
{
    public function getNodeType(): SemanticNodeTypeEnum;

    public function getSourceSpan(): SourceSpan;
    public function getRaw(): string;
}
