<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Enums;

enum LocationEntityRelationTypeEnum: string
{
    case BORN_IN = 'born_in';

    case DIED_IN = 'died_in';

    case LOCATED_IN = 'located_in';

    case RESIDED_IN = 'resided_in';

    case VISITED = 'visited';
}
