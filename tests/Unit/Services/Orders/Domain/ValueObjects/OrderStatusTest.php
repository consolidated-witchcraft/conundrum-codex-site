<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;

dataset('order_statuses', function () {
    yield 'invoiced' => ['invoiced', 'Invoiced'];
    yield 'paid' => ['paid', 'Paid'];
    yield 'shipped' => ['shipped', 'Shipped'];
    yield 'delivered' => ['delivered', 'Delivered'];
    yield 'refunded' => ['refunded', 'Refunded'];
    yield 'canceled' => ['canceled', 'Canceled'];
    yield 'closed' => ['closed', 'Closed'];
});

test('make from handle', function (string $handle, string $name) {
    $orderStatus = OrderStatus::makeFromHandle($handle);

    self::assertSame($handle, $orderStatus->getHandle());
    self::assertSame($name, $orderStatus->getName());
})->with('order_statuses');

test('unknown order status is rejected', function () {
    $this->expectException(UnknownOrderStatusException::class);

    OrderStatus::makeFromHandle('draft');
});
