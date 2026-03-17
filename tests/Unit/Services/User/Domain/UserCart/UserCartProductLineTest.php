<?php

declare(strict_types=1);

use App\Services\Product\Domain\ProductVariant;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartProductLine;

test('construct, mutate quantity and serialize', function () {
    $userCart = $this->createStub(UserCart::class);
    $productVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getPreTaxPriceCentessimal' => 750,
        'getProductVariantName' => 'Event Ticket',
        'getSku' => 'EVENT.TICKET',
    ]);
    $userCartProductLine = new UserCartProductLine(
        userCart: $userCart,
        productVariant: $productVariant,
        quantity: 2,
        id: 14,
    );

    self::assertSame($userCart, $userCartProductLine->getUserCart());
    self::assertSame($productVariant, $userCartProductLine->getProductVariant());
    self::assertSame(2, $userCartProductLine->getQuantity());
    self::assertSame(1500, $userCartProductLine->getPretaxTotalCentessimal());
    self::assertSame(15.0, $userCartProductLine->getPretaxTotalDecimal());

    $userCartProductLine->setQuantity(3);

    self::assertSame(3, $userCartProductLine->getQuantity());
    self::assertSame(2250, $userCartProductLine->getPretaxTotalCentessimal());
    self::assertSame([
        'cartLineId' => 14,
        'name' => 'Event Ticket',
        'productSku' => 'EVENT.TICKET',
        'preTaxPriceCentessimal' => 2250,
        'quantity' => 3,
        'createdAt' => $userCartProductLine->getCreatedAt(),
        'updatedAt' => $userCartProductLine->getUpdatedAt(),
    ], $userCartProductLine->jsonSerialize());
});
