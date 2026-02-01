<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\ApiResource\PuzzleTemplate\Dto\PuzzleTemplateDto;
use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;

#[ApiResource(
    uriTemplate: '/puzzles/instances/dynamic/{templateSlug}/{instanceCode}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'templateSlug' => new Link(
            fromProperty: 'templateSlug',
            fromClass: PuzzleTemplateDto::class
        ),
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: DynamicPuzzleInstanceDtoOld::class
        )
    ],
    stateless: false,
    provider: DynamicPuzzleInstanceProvider::class
)]
readonly class DynamicPuzzleInstanceDtoOld
{
    /**
     * @param string $instanceCode
     * @param string $name
     * @param string $description
     * @param DynamicPuzzleFieldDto[] $config
     * @param \DateTimeInterface|null $publicationDate
     */
    final public function __construct(
        public string $instanceCode,
        public string $name,
        public string $description,
        public array $config,
        public ?\DateTimeInterface $publicationDate = null,
    ) {}


    public static function makeFromDynamicPuzzleInstance(DynamicPuzzleInstanceInterface $instance): static {
        return new static(
            instanceCode: $instance->getInstanceCode(),
            name: $instance->getName(),
            description: $instance->getDescription(),
            config: $instance->getConfig(),
            publicationDate: $instance->getPublicationDate()
        );
    }
}
