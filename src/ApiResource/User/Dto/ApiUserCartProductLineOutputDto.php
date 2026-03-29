<?php

declare(strict_types=1);
namespace App\ApiResource\User\Dto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\User\Providers\ApiUserCartProductLineProvider;
use App\ApiResource\User\StateProcessors\ApiUserCartProductLineStateProcessor;
use App\Services\User\Domain\UserCart\UserCartProductLine;

#[ApiResource(
    uriTemplate: '/cart/{cartId}/productLines',
    operations: [
        new GetCollection(
            uriVariables: [
                'cartId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartDto::class
                ),
            ],
            security: 'is_granted("current_user_owns_cart", object)'
        ),
        new Get(
            uriTemplate: '/cart/{cartId}/productLines/{productLineId}',
            uriVariables: [
                'cartId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartDto::class
                ),
                'productLineId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartProductLineOutputDto::class
                ),
            ],
            security: 'is_granted("current_user_owns_cart_line", object)'
        ),
        new Patch(
            uriTemplate: '/cart/{cartId}/productLines/{productLineId}',
            uriVariables: [
                'cartId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartDto::class
                ),
                'productLineId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartProductLineOutputDto::class
                ),
            ],
            security: 'is_granted("current_user_owns_cart_line", object)'
        ),
        new Delete(
            uriTemplate: '/cart/{cartId}/productLines/{productLineId}',
            uriVariables: [
                'cartId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartDto::class
                ),
                'productLineId' => new Link(
                    fromProperty: 'id',
                    fromClass: ApiUserCartProductLineOutputDto::class
                ),
            ],
            security: 'is_granted("current_user_owns_cart_line", object)',),
    ],

    stateless: false,
    provider: ApiUserCartProductLineProvider::class,
    processor: ApiUserCartProductLineStateProcessor::class
)]
class ApiUserCartProductLineOutputDto
{
    private function __construct(
        public int $userId,
        public string $productVariantSku,
        public readonly string $productVariantName,
        public readonly int $productVariantPriceCentessimal,
        public int $quantity,
        public int $lineTotalCentessimal,
        public ? int $id = null,
    ) {}

    public static function makeFromCartProductLine(UserCartProductLine $userCartProductLine): ApiUserCartProductLineOutputDto {
        return new static(
            userId: $userCartProductLine->getUserCart()->getUser()->getId(),
            productVariantSku: $userCartProductLine->getProductVariant()->getSku(),
            productVariantName: $userCartProductLine->getProductVariant()->getProductVariantName(),
            productVariantPriceCentessimal: $userCartProductLine->getProductVariant()->getPreTaxPriceCentessimal(),
            quantity: $userCartProductLine->getQuantity(),
            lineTotalCentessimal: $userCartProductLine->getPretaxTotalCentessimal(),
            id: $userCartProductLine->getId()
        );
    }
}
