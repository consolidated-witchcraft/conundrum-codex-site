<?php

declare(strict_types=1);

use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartCharge;

test('construct', function () {
    $userCart = $this->createStub(UserCart::class);
    $userCartCharge = new UserCartCharge(
        userCart: $userCart,
        chargeType: ChargeType::makeFromHandle('delivery_charge'),
        preTaxAmountCentessimal: 200,
        id: 12,
    );

    self::assertSame($userCart, $userCartCharge->getUserCart());
    self::assertSame('delivery_charge', $userCartCharge->getChargeType()->getHandle());
    self::assertSame(200, $userCartCharge->getPretaxAmountCentessimal());
    self::assertSame(2.0, $userCartCharge->getPreTaxAmountDecimal());
    self::assertSame(12, $userCartCharge->getId());
});
