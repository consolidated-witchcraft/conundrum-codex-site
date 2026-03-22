<?php

namespace App\Services\SemanticMarkup\Domain\Enums;

enum PersonEntityRelationshipTypeEnum: string
{
    case MENTOR_OF = 'mentor_of';

    case PARENT_OF = 'parent_of';

    case CHILD_OF = 'child_of';

    case SPOUSE_OF = 'spouse_of';

    case ENEMY_OF = 'enemy_of';

    case FRIEND_OF = 'friend_of';

    case ASSOCIATE_OF = 'known_associate_of';

    case COLLEAGUE_OF = 'colleague_of';
}
