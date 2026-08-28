<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain\Enums;

enum CampaignMembershipTypeEnum: string
{
    case PLAYER = 'player';
    case GAME_MASTER = 'game_master';
    case NOTE_TAKER = 'note_taker';
    case SECONDARY_GAME_MASTER = 'secondary_game_master';
}
