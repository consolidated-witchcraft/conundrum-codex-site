<?php

declare(strict_types=1);
namespace App\ApiResource\User\Dto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\User\Providers\ApiUserCartProductLineProvider;
use App\ApiResource\User\StateProcessors\ApiUserCartProductLineStateProcessor;
use App\Services\User\Domain\UserCart\UserCartProductLine;

#[ApiResource(
    uriTemplate: '/cart/{cartId}/productLines/new',
    operations: [
        new Post(),
    ],
    uriVariables: [
        'cartId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserCartDto::class
        ),
    ],
    stateless: false,
    processor: ApiUserCartProductLineStateProcessor::class
)]
class ApiUserCartProductLineInputDto
{
    public function __construct(
        public string $productVariantSku,
        public int $quantity,
    ) {}

    public static function makeFromCartProductLine(UserCartProductLine $userCartProductLine): ApiUserCartProductLineInputDto {
        return new static(
            productVariantSku: $userCartProductLine->getProductVariant()->getSku(),
            quantity: $userCartProductLine->getQuantity()
        );
    }
}
