<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderChargeLine;
use App\Services\Orders\Domain\ValueObjects\ChargeType;

test('construct and setters update state', function () {
    $order = $this->createStub(Order::class);
    $orderChargeLine = new OrderChargeLine(
        chargeType: ChargeType::makeFromHandle('service_charge'),
        name: 'Booking fee',
        order: $order,
        priceCentessimal: 250,
        id: 8,
    );

    self::assertSame('service_charge', $orderChargeLine->getChargeType()->getHandle());
    self::assertSame('Booking fee', $orderChargeLine->getName());
    self::assertSame($order, $orderChargeLine->getOrder());
    self::assertSame(250, $orderChargeLine->getPriceCentessimal());
    self::assertSame(2.5, $orderChargeLine->getPriceDecimal());
    self::assertSame(8, $orderChargeLine->getId());

    $orderChargeLine->setChargeType(ChargeType::makeFromHandle('delivery_charge'));
    $orderChargeLine->setName('Postage');

    self::assertSame('delivery_charge', $orderChargeLine->getChargeType()->getHandle());
    self::assertSame('Postage', $orderChargeLine->getName());
});
