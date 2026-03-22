<?php

declare(strict_types=1);

use App\Services\SemanticMarkup\Domain\Enums\EventEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\FactionEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\FactTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\ItemEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\LocationEntityRelationTypeEnum;
use App\Services\SemanticMarkup\Domain\Enums\PersonEntityRelationshipTypeEnum;
use App\Services\SemanticMarkup\Domain\SemanticRegistry;

test('entity kinds are represented by backed enums', function () {
    $registry = new SemanticRegistry();

    self::assertSame([
        SemanticRegistry::ENTITY_KIND_PERSON => [
            PersonEntityRelationshipTypeEnum::MENTOR_OF,
            PersonEntityRelationshipTypeEnum::PARENT_OF,
            PersonEntityRelationshipTypeEnum::CHILD_OF,
            PersonEntityRelationshipTypeEnum::SPOUSE_OF,
            PersonEntityRelationshipTypeEnum::ENEMY_OF,
            PersonEntityRelationshipTypeEnum::FRIEND_OF,
            PersonEntityRelationshipTypeEnum::ASSOCIATE_OF,
            PersonEntityRelationshipTypeEnum::COLLEAGUE_OF,
        ],
        SemanticRegistry::ENTITY_KIND_FACTION => [
            FactionEntityRelationTypeEnum::MEMBER_OF,
            FactionEntityRelationTypeEnum::LEADER_OF,
            FactionEntityRelationTypeEnum::FOUNDER_OF,
            FactionEntityRelationTypeEnum::ALLY_OF,
            FactionEntityRelationTypeEnum::ENEMY_OF,
        ],
        SemanticRegistry::ENTITY_KIND_LOCATION => [
            LocationEntityRelationTypeEnum::BORN_IN,
            LocationEntityRelationTypeEnum::DIED_IN,
            LocationEntityRelationTypeEnum::LOCATED_IN,
            LocationEntityRelationTypeEnum::RESIDED_IN,
            LocationEntityRelationTypeEnum::VISITED,
        ],
        SemanticRegistry::ENTITY_KIND_ITEM => [
            ItemEntityRelationTypeEnum::OWNS,
            ItemEntityRelationTypeEnum::WIELDS,
            ItemEntityRelationTypeEnum::CARRIES,
        ],
        SemanticRegistry::ENTITY_KIND_EVENT => [
            EventEntityRelationTypeEnum::PARTICIPATED_IN,
            EventEntityRelationTypeEnum::WITNESSED,
            EventEntityRelationTypeEnum::CAUSED,
        ],
    ], $registry->getEntityKinds());
});

test('fact types are represented by enums', function () {
    $registry = new SemanticRegistry();

    self::assertSame([
        FactTypeEnum::ALIAS,
        FactTypeEnum::TITLE,
        FactTypeEnum::TRAIT,
        FactTypeEnum::SECRET,
        FactTypeEnum::DESCRIPTION,
    ], $registry->getFactTypes());
});
