<?php

declare(strict_types=1);
namespace App\ApiResource\User\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\ApiResource\User\Providers\ApiUserCartProvider;
use App\ApiResource\User\StateProcessors\ApiUserCartProductLineStateProcessor;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;

#[ApiResource(
    uriTemplate: '/cart/{cartId}',
    operations: [
        new Get(),
    ],
    uriVariables: [
        'cartId' => new Link(
            fromProperty: 'id',
            fromClass: ApiUserCartDto::class
        ),
    ],
    stateless: false,
    security: 'is_granted("current_user_owns_cart", object)',
    provider: ApiUserCartProvider::class
)]
class ApiUserCartDto
{
    /**
     * @param int $id
     * @param int $userId
     * @param array<int, string> $cartProductLines
     * @param array<int, ApiUserCartChargeLineDto> $cartChargeLines
     */
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public array $cartProductLines,
        public readonly array $cartChargeLines
    ) {}

    public static function makeFromUser(User $user): ApiUserCartDto {
        $cart = $user->getUserCart();
        return self::makeFromCart($cart);
    }

    public static function makeFromCart(UserCart $cart): ApiUserCartDto {
        $cartChargeLines = [];
        foreach ($cart->getUserCartChargeLines() as $cartChargeLine) {
            $cartChargeLines[] = ApiUserCartChargeLineDto::makeFromCartChargeLine($cartChargeLine);
        }


        return new static(
            id: $cart->getId(),
            userId: $cart->getUser()->getId(),
            cartProductLines: [],
            cartChargeLines: $cartChargeLines
        );
    }
}
