<?php

declare(strict_types=1);

namespace App\Services\SemanticMarkup\Domain;

use App\Services\SemanticMarkup\Domain\Enums\EventEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\FactionEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\FactTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\ItemEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\LocationEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\PersonEntityRelationshipTypeEnum;

readonly class SemanticRegistry
{
    public const string ENTITY_KIND_PERSON = 'person';

    public const string ENTITY_KIND_FACTION = 'faction';

    public const string ENTITY_KIND_LOCATION = 'location';

    public const string ENTITY_KIND_ITEM = 'item';

    public const string ENTITY_KIND_EVENT = 'event';

    /**
     * @return array<string, list<EventEntityRelationTypeEnum|FactionEntityRelationTypeEnum|ItemEntityRelationTypeEnum|LocationEntityRelationTypeEnum|PersonEntityRelationshipTypeEnum>>
     */
    public function getEntityKinds(): array
    {
        return [
            self::ENTITY_KIND_PERSON => [
                PersonEntityRelationshipTypeEnum::MENTOR_OF,
                PersonEntityRelationshipTypeEnum::PARENT_OF,
                PersonEntityRelationshipTypeEnum::CHILD_OF,
                PersonEntityRelationshipTypeEnum::SPOUSE_OF,
                PersonEntityRelationshipTypeEnum::ENEMY_OF,
                PersonEntityRelationshipTypeEnum::FRIEND_OF,
                PersonEntityRelationshipTypeEnum::ASSOCIATE_OF,
                PersonEntityRelationshipTypeEnum::COLLEAGUE_OF
            ],
            self::ENTITY_KIND_FACTION => [
                FactionEntityRelationTypeEnum::MEMBER_OF,
                FactionEntityRelationTypeEnum::LEADER_OF,
                FactionEntityRelationTypeEnum::FOUNDER_OF,
                FactionEntityRelationTypeEnum::ALLY_OF,
                FactionEntityRelationTypeEnum::ENEMY_OF,
            ],
            self::ENTITY_KIND_LOCATION => [
                LocationEntityRelationTypeEnum::BORN_IN,
                LocationEntityRelationTypeEnum::DIED_IN,
                LocationEntityRelationTypeEnum::LOCATED_IN,
                LocationEntityRelationTypeEnum::RESIDED_IN,
                LocationEntityRelationTypeEnum::VISITED,
            ],
            self::ENTITY_KIND_ITEM => [
                ItemEntityRelationTypeEnum::OWNS,
                ItemEntityRelationTypeEnum::WIELDS,
                ItemEntityRelationTypeEnum::CARRIES,
            ],
            self::ENTITY_KIND_EVENT => [
                EventEntityRelationTypeEnum::PARTICIPATED_IN,
                EventEntityRelationTypeEnum::WITNESSED,
                EventEntityRelationTypeEnum::CAUSED,
            ],
        ];
    }

    /**
     * @return list<FactTypeEnum>
     */
    public function getFactTypes(): array
    {
        return [
            FactTypeEnum::ALIAS,
            FactTypeEnum::TITLE,
            FactTypeEnum::TRAIT,
            FactTypeEnum::SECRET,
            FactTypeEnum::DESCRIPTION,
        ];
    }
}
