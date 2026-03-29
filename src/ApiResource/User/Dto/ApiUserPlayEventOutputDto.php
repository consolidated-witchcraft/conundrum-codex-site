<?php

declare(strict_types=1);

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\ApiResource\User\Providers\ApiUserPlayEventProvider;
use App\Services\PlayEvent\Domain\PlayEvent;

#[ApiResource(
    uriTemplate: '/user/{userId}/playEvents/{playEventId}',
    operations: [
        new Get(
            uriVariables: [
                'userId' => new Link(
                    fromClass: ApiUserPlayEventOutputDto::class,
                    identifiers: ['userId']
                ),
                'playEventId' => new Link(
                    fromClass: ApiUserPlayEventOutputDto::class,
                    identifiers: ['id']
                ),
            ],
        ),
        new GetCollection(
            uriTemplate: '/user/{userId}/playEvents',
            itemUriTemplate: '/user/{userId}/playEvents/{playEventId}',
            uriVariables: [
                'userId' => new Link(
                    fromClass: ApiUserPlayEventOutputDto::class,
                    identifiers: ['userId']
                ),
            ],
        ),
    ],
    stateless: false,
    provider: ApiUserPlayEventProvider::class,
)]
class ApiUserPlayEventOutputDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public readonly int $id,
        public readonly int $userId,
        public readonly string $type,
        public readonly string $puzzleInstanceCode,
        public readonly string $puzzleTemplateSlug,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public static function makeFromPlayEvent(PlayEvent $playEvent): self
    {
        return new self(
            id: $playEvent->getId() ?? 0,
            userId: $playEvent->getUser()->getId(),
            type: $playEvent->getType()->value,
            puzzleInstanceCode: $playEvent->getPuzzleInstanceCode(),
            puzzleTemplateSlug: $playEvent->getPuzzleTemplateSlug(),
            createdAt: $playEvent->getCreatedAt(),
            updatedAt: $playEvent->getUpdatedAt(),
        );
    }
}
