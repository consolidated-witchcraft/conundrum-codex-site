<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;

test('membership types have stable backed values', function (
    CampaignMembershipTypeEnum $type,
    string $value,
) {
    self::assertSame(expected: $value, actual: $type->value);
})->with([
    'player' => [CampaignMembershipTypeEnum::PLAYER, 'player'],
    'game master' => [CampaignMembershipTypeEnum::GAME_MASTER, 'game_master'],
    'note taker' => [CampaignMembershipTypeEnum::NOTE_TAKER, 'note_taker'],
    'secondary game master' => [CampaignMembershipTypeEnum::SECONDARY_GAME_MASTER, 'secondary_game_master'],
]);
