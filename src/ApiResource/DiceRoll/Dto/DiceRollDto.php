<?php

declare(strict_types=1);

namespace App\ApiResource\DiceRoll\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\DiceRoll\Providers\DiceRollProvider;

#[ApiResource(
    uriTemplate: '/dice/{rollState}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'rollState' => new Link(
            fromProperty: 'id',
            fromClass: DiceRollDto::class
        )
    ],
    stateless: false,
    provider: DiceRollProvider::class
)]
readonly class DiceRollDto
{
    public function __construct(
        public string $id,
        public int $dieSides,
        public int $roll,
        public int $total,
        public ?int $bonus
    ) {

    }
}
