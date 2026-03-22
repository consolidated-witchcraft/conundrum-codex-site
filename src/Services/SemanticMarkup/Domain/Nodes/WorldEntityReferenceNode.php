<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Nodes;

use App\Services\SemanticMarkup\Domain\Nodes\Enums\SemanticNodeTypeEnum;
use App\Services\SemanticMarkup\Domain\Nodes\Interfaces\SemanticNodeInterface;
use App\Services\SemanticMarkup\Domain\ValueObjects\SourceSpan;

final readonly class WorldEntityReferenceNode extends AbstractSemanticNode implements SemanticNodeInterface
{
    public function __construct(
        string $raw,
        SourceSpan $sourceSpan,
        public string $label,
        public string $kind,
        public ?string $predicate = null,
        public ?string $revealedDate = null,
        public ?string $slug = null,
    ) {
        parent::__construct($raw, $sourceSpan);
    }

    public function getNodeType(): SemanticNodeTypeEnum {
        return SemanticNodeTypeEnum::ENTITY_REFERENCE;
    }
}
