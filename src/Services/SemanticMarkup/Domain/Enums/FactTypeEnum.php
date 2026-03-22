<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Enums;

enum FactTypeEnum: string
{
    case ALIAS = 'alias';
    case TITLE = 'title';
    case TRAIT = 'trait';

    case SECRET = 'secret';
    case DESCRIPTION = 'description';

}
