<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderProductLine;
use App\Services\Product\Domain\ProductVariant;

test('construct and setters update state', function () {
    $order = $this->createStub(Order::class);
    $productVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getPreTaxPriceCentessimal' => 750,
    ]);
    $updatedOrder = $this->createStub(Order::class);
    $updatedProductVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getPreTaxPriceCentessimal' => 900,
    ]);

    $orderProductLine = new OrderProductLine(
        order: $order,
        productVariant: $productVariant,
        quantity: 3,
        id: 9,
    );

    self::assertSame($order, $orderProductLine->getOrder());
    self::assertSame($productVariant, $orderProductLine->getProductVariant());
    self::assertSame(3, $orderProductLine->getQuantity());
    self::assertSame(2250, $orderProductLine->getTotalCentessimal());
    self::assertSame(22.5, $orderProductLine->getTotalDecimal());
    self::assertSame(9, $orderProductLine->getId());

    $orderProductLine->setOrder($updatedOrder);
    $orderProductLine->setProductVariant($updatedProductVariant);
    $orderProductLine->setQuantity(2);

    self::assertSame($updatedOrder, $orderProductLine->getOrder());
    self::assertSame($updatedProductVariant, $orderProductLine->getProductVariant());
    self::assertSame(2, $orderProductLine->getQuantity());
    self::assertSame(1800, $orderProductLine->getTotalCentessimal());
});
