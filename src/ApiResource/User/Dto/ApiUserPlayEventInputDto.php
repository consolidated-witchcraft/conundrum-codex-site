<?php

declare(strict_types=1);

namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use App\ApiResource\User\StateProcessors\ApiUserPlayEventStateProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    uriTemplate: '/user/{userId}/playEvents',
    operations: [
        new Post(
            itemUriTemplate: '/user/{userId}/playEvents/{playEventId}',
        ),
    ],
    uriVariables: [
        'userId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserDto::class
        ),
    ],
    stateless: false,
    processor: ApiUserPlayEventStateProcessor::class,
)]
class ApiUserPlayEventInputDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['instance_start', 'instance_win', 'instance_fail'])]
        public string $type,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $puzzleInstanceCode,

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $puzzleTemplateSlug,
    ) {
    }
}
