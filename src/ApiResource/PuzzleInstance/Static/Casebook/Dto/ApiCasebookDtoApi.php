<?php

namespace App\ApiResource\PuzzleInstance\Static\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\ApiPuzzleInstanceDtoInterface;
use App\ApiResource\PuzzleInstance\Static\Casebook\Providers\CasebookProvider;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;

#[ApiResource(
    uriTemplate: '/puzzles/instances/static/casebook/{instanceCode}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: ApiCasebookDtoApi::class
        )
    ],
    stateless: false,
    security: 'is_granted("play_instance", object)',
    provider: CasebookProvider::class
)]
readonly class ApiCasebookDtoApi implements ApiPuzzleInstanceDtoInterface
{
    final public function __construct(
        public string $gameSlug,
        public string $instanceCode,
        public string $name,
        public string $brief
    ) {}

    public static function makeFromInstance(Casebook $casebook): self {
        return new static(
            gameSlug: $casebook->getGame()->getSlug(),
            instanceCode: $casebook->getInstanceCode(),
            name: $casebook->getName(),
            brief: $casebook->getBrief()
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
