<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Enums;

enum EventEntityRelationTypeEnum: string
{
    case PARTICIPATED_IN = 'participated_in';

    case WITNESSED = 'witnessed';

    case CAUSED = 'caused';
}
