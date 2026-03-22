<?php

namespace App\Services\SemanticMarkup\Domain\Nodes\Enums;

enum SemanticNodeTypeEnum: string
{
    case ENTITY_REFERENCE = 'entity_reference';
    case FACT = 'fact';

    case TEXT = 'text';
}
