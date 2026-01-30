<?php

namespace App\ApiResource\PuzzleInstance\Static\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\PuzzleInstance\Static\Casebook\Providers\CasebookProvider;
use App\Services\Puzzle\Domain\Casebook\Casebook;

#[ApiResource(
    uriTemplate: '/puzzles/instances/static/casebook/{instanceCode}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: ApiCasebookDto::class
        )
    ],
    stateless: false,
    provider: CasebookProvider::class
)]
readonly class ApiCasebookDto
{
    final public function __construct(
        public string $instanceCode,
        public string $name,
        public string $brief
    ) {}

    public static function makeFromInstance(Casebook $casebook): self {
        return new static(
            instanceCode: $casebook->getInstanceCode(),
            name: $casebook->getName(),
            brief: $casebook->getBrief()
        );
    }
}
