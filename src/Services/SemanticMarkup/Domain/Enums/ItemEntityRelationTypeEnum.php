<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Enums;

enum ItemEntityRelationTypeEnum: string
{
    case OWNS = 'owns';

    case WIELDS = 'wields';

    case CARRIES = 'carries';
}
