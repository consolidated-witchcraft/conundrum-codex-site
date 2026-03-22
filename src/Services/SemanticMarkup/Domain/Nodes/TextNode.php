<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Nodes;

use App\Services\SemanticMarkup\Domain\Nodes\Enums\SemanticNodeTypeEnum;
use App\Services\SemanticMarkup\Domain\Nodes\Interfaces\SemanticNodeInterface;
use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

final readonly class TextNode extends AbstractSemanticNode implements SemanticNodeInterface
{
    public function __construct(
        string $raw,
        SourceSpan $sourceSpan,
        public string $text
    ) {
        parent::__construct(
            raw: $raw,
            sourceSpan: $sourceSpan
        );
    }

    public function getNodeType(): SemanticNodeTypeEnum
    {
        return SemanticNodeTypeEnum::TEXT;
    }
}
