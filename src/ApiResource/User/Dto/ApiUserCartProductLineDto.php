<?php

declare(strict_types=1);
namespace App\ApiResource\User\Dto;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\User\Providers\ApiUserCartProductLineProvider;
use App\ApiResource\User\StateProcessors\ApiUserCartProductLineStateProcessor;
use App\Services\User\Domain\UserCart\UserCartProductLine;

#[ApiResource(
    uriTemplate: '/cart/{cartId}/productLines/{productLineId}',
    operations: [
        new Get(),
        new Patch(),
    ],
    uriVariables: [
        'cartId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserCartDto::class
        ),
        'productLineId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserCartProductLineDto::class
        ),
    ],
    stateless: false,
    security: 'is_granted("current_user_owns_cart_line", object)',
    provider: ApiUserCartProductLineProvider::class,
    processor: ApiUserCartProductLineStateProcessor::class
)]
class ApiUserCartProductLineDto
{
    private function __construct(
        public readonly int $id,
        public int $userId,
        public string $productVariantSku,
        public readonly string $productVariantName,
        public readonly int $productVariantPriceCentessimal,
        public int $quantity,
        public readonly int $lineTotalCentessimal
    ) {}

    public static function makeFromCartProductLine(UserCartProductLine $userCartProductLine): ApiUserCartProductLineDto {
        return new static(
            id: $userCartProductLine->getId(),
            userId: $userCartProductLine->getUserCart()->getUser()->getId(),
            productVariantSku: $userCartProductLine->getProductVariant()->getSku(),
            productVariantName: $userCartProductLine->getProductVariant()->getProductVariantName(),
            productVariantPriceCentessimal: $userCartProductLine->getProductVariant()->getPreTaxPriceCentessimal(),
            quantity: $userCartProductLine->getQuantity(),
            lineTotalCentessimal: $userCartProductLine->getPretaxTotalCentessimal()
        );
    }
}
