<?php

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\User\Providers\ApiUserProvider;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    uriTemplate: '/user/{userId}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'userId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserDto::class
        ),
    ],
    stateless: false,
    security: 'is_granted("current_user_is_user", object)',
    provider: ApiUserProvider::class
)]
class ApiUserDto
{
    public function __construct(
        public readonly int $id,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public static function makeFromUser(UserInterface $user): static {
        return new static(
            id: $user->getId(),
        );
    }
}
