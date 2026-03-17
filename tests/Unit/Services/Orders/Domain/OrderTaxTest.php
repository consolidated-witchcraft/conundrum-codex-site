<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderTax;

test('construct', function () {
    $order = $this->createStub(Order::class);
    $orderTax = new OrderTax(
        taxRatePublicReference: 'GB-VAT-20',
        taxRateName: 'VAT 20%',
        taxRateHandle: 'vat_standard',
        order: $order,
        amountCentessimal: 300,
        id: 4,
    );

    self::assertSame('GB-VAT-20', $orderTax->getTaxRatePublicReference());
    self::assertSame('VAT 20%', $orderTax->getTaxRateName());
    self::assertSame('vat_standard', $orderTax->getTaxRateHandle());
    self::assertSame($order, $orderTax->getOrder());
    self::assertSame(300, $orderTax->getAmountCentessimal());
    self::assertSame(3.0, $orderTax->getAmountDecimal());
    self::assertSame(4, $orderTax->getId());
});
