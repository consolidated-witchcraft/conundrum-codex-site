<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Checkout;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;

class UserCartDto
{
    public function __construct(
        public readonly User $user,
        public array $userCartLines
    ) {}

    public static function makeFromUserCart(UserCart $cart): static {
        $cartLines = [];
        foreach ($cart->getUserCartLines() as $delta => $cartLine) {
            $cartLines[$delta] = UserCartLineDto::makeFromUserCartLine($cartLine);
        }
        return new static(
            user: $cart->getUser(),
            userCartLines: $cartLines,
        );
    }
}
