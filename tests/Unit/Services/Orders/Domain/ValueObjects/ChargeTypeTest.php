<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\ValueObjects\ChargeType;

dataset('charge_types', function () {
    yield 'service_charge' => ['service_charge', 'Service charge'];
    yield 'delivery_charge' => ['delivery_charge', 'Delivery charge'];
});

test('make from handle', function (string $handle, string $name) {
    $chargeType = ChargeType::makeFromHandle($handle);

    self::assertSame($handle, $chargeType->getHandle());
    self::assertSame($name, $chargeType->getName());
})->with('charge_types');

test('unknown charge type is rejected', function () {
    $this->expectException(UnknownPaymentStatusException::class);

    ChargeType::makeFromHandle('discount');
});
