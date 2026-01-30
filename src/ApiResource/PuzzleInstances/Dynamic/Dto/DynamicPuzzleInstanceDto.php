<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstances\Dynamic\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\Puzzles\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\Services\Puzzle\Domain\Interfaces\DynamicPuzzleInstanceInterface;

#[ApiResource(
    uriTemplate: '/puzzles/dynamic/{templateSlug}/{instanceCode}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'templateSlug' => new Link(
            fromProperty: 'templateSlug',
            fromClass: DynamicPuzzleInstanceDto::class
        ),
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: DynamicPuzzleInstanceDto::class
        )
    ],
    stateless: false,
    provider: DynamicPuzzleInstanceProvider::class
)]
class DynamicPuzzleInstanceDto
{
    final public function __construct(
        public readonly string $instanceCode,
    ) {}


    public static function makeFromDynamicPuzzleInstance(DynamicPuzzleInstanceInterface $instance): static {
        return new static(
            instanceCode: $instance->getInstanceCode(),
        );
    }
}
