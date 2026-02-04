<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\ApiPuzzleInstanceDtoInterface;
use App\ApiResource\PuzzleTemplate\Dto\PuzzleTemplateDto;
use App\Security\GameManagerVoter;
use App\Services\PuzzleInstance\Domain\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\PuzzleInstance\Domain\Dynamic\Providers\DynamicPuzzleInstanceProvider;
use App\Services\PuzzleInstance\Domain\Dynamic\PuzzleInstance;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;


#[ApiResource(
    uriTemplate: '/puzzles/instances/dynamic/{templateSlug}/{instanceCode}',
    security: 'is_granted("play_instance", object)',
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
            fromClass: ApiDynamicPuzzleInstanceDto::class
        )
    ],
    stateless: false,
    provider: DynamicPuzzleInstanceProvider::class
)]
class ApiDynamicPuzzleInstanceDto implements ApiPuzzleInstanceDtoInterface
{
    public function __construct(
        public string $gameSlug,
        public string $puzzleName,
        public string $puzzleDescription,
        public array $config,
        public string $instanceCode,
        public \DateTimeInterface $publicationDate
    ) {}

    public static function makeFromPuzzleInstance(DynamicPuzzleInstanceInterface $puzzleInstance): ApiDynamicPuzzleInstanceDto {
        return new static(
            gameSlug: $puzzleInstance->getGame()->getSlug(),
            puzzleName: $puzzleInstance->getName(),
            puzzleDescription: $puzzleInstance->getDescription(),
            config: $puzzleInstance->getConfig(),
            instanceCode: $puzzleInstance->getInstanceCode(),
            publicationDate: $puzzleInstance->getPublicationDate()
        );
    }

    public function getInstanceCode(): string
    {
        return $this->instanceCode;
    }

    public function getGameSlug(): string
    {
        return $this->gameSlug;
    }
}
