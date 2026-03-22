<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain\Enums;

enum FactionEntityRelationTypeEnum: string
{
    case MEMBER_OF = 'member_of';

    case LEADER_OF = 'leader_of';

    case FOUNDER_OF = 'founder_of';

    case ALLY_OF = 'ally_of';

    case ENEMY_OF = 'enemy_of';

}
