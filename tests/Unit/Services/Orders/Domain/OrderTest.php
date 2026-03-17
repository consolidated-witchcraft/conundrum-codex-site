<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\InvalidOrderStatusChangeRequestedException;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderChargeLine;
use App\Services\Orders\Domain\OrderProductLine;
use App\Services\Orders\Domain\OrderTax;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeOrder = function (array $overrides = []): Order {
        return new Order(
            orderReference: $overrides['orderReference'] ?? 'ORDER-123',
            orderStatus: $overrides['orderStatus'] ?? OrderStatus::makeFromHandle('invoiced'),
            productOrderLines: $overrides['productOrderLines'] ?? new ArrayCollection(),
            chargeLines: $overrides['chargeLines'] ?? new ArrayCollection(),
            orderPayments: $overrides['orderPayments'] ?? new ArrayCollection(),
            user: $overrides['user'] ?? $this->createStub(User::class),
            orderTaxes: $overrides['orderTaxes'] ?? new ArrayCollection(),
            deliveryAddress: $overrides['deliveryAddress'] ?? null,
            billingAddress: $overrides['billingAddress'] ?? null,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct and setters update state', function () {
    $deliveryAddress = $this->createStub(PostalAddress::class);
    $billingAddress = $this->createStub(PostalAddress::class);
    $user = $this->createStub(User::class);
    $order = ($this->makeOrder)([
        'deliveryAddress' => $deliveryAddress,
        'billingAddress' => $billingAddress,
        'user' => $user,
        'id' => 44,
    ]);

    self::assertSame('ORDER-123', $order->getOrderReference());
    self::assertSame('invoiced', $order->getOrderStatus()->getHandle());
    self::assertSame($deliveryAddress, $order->getDeliveryAddress());
    self::assertSame($billingAddress, $order->getBillingAddress());
    self::assertSame($user, $order->getUser());
    self::assertSame(44, $order->getId());
});

test('totals are calculated from line items and taxes', function () {
    $productLines = new ArrayCollection([
        $this->createConfiguredStub(OrderProductLine::class, ['getTotalCentessimal' => 1000]),
        $this->createConfiguredStub(OrderProductLine::class, ['getTotalCentessimal' => 500]),
    ]);
    $chargeLines = new ArrayCollection([
        $this->createConfiguredStub(OrderChargeLine::class, ['getPriceCentessimal' => 200]),
        $this->createConfiguredStub(OrderChargeLine::class, ['getPriceCentessimal' => 100]),
    ]);
    $orderTaxes = new ArrayCollection([
        $this->createConfiguredStub(OrderTax::class, ['getAmountCentessimal' => 300]),
    ]);
    $order = ($this->makeOrder)([
        'productOrderLines' => $productLines,
        'chargeLines' => $chargeLines,
        'orderTaxes' => $orderTaxes,
    ]);

    self::assertSame(1500, $order->getProductLineTotalPreTaxCentessimal());
    self::assertSame(15.0, $order->getProductLineTotalPreTaxDecimal());
    self::assertSame(300, $order->getChargeLineTotalPreTaxCentessimal());
    self::assertSame(3.0, $order->getChargeLineTotalPreTaxDecimal());
    self::assertSame(300, $order->getTotalTaxCentessimal());
    self::assertSame(3.0, $order->getTotalTaxDecimal());
    self::assertSame(2100, $order->getTotalPostTaxCentessimal());
    self::assertSame(21.0, $order->getTotalPostTaxDecimal());
});

test('order payment status reflects payments', function () {
    $paidPayment = $this->createConfiguredStub(Payment::class, [
        'getPaymentStatus' => PaymentStatus::makeFromHandle('paid'),
    ]);
    $processingPayment = $this->createConfiguredStub(Payment::class, [
        'getPaymentStatus' => PaymentStatus::makeFromHandle('processing'),
    ]);

    $fullyPaidOrder = ($this->makeOrder)([
        'orderPayments' => new ArrayCollection([$paidPayment]),
    ]);
    $partiallyPaidOrder = ($this->makeOrder)([
        'orderPayments' => new ArrayCollection([$paidPayment, $processingPayment]),
    ]);
    $unpaidOrder = ($this->makeOrder)();

    self::assertSame('paid', $fullyPaidOrder->getOrderPaymentStatus()->getHandle());
    self::assertSame('partially_paid', $partiallyPaidOrder->getOrderPaymentStatus()->getHandle());
    self::assertSame('paid', $unpaidOrder->getOrderPaymentStatus()->getHandle());
});

test('mark order paid updates status when all payments are paid', function () {
    $paidPayment = $this->createConfiguredStub(Payment::class, [
        'getPaymentStatus' => PaymentStatus::makeFromHandle('paid'),
    ]);
    $order = ($this->makeOrder)([
        'orderPayments' => new ArrayCollection([$paidPayment]),
    ]);

    $order->markOrderPaid();

    self::assertSame('paid', $order->getOrderStatus()->getHandle());
});

test('mark order paid rejects invalid status change', function () {
    $paidPayment = $this->createConfiguredStub(Payment::class, [
        'getPaymentStatus' => PaymentStatus::makeFromHandle('paid'),
    ]);
    $order = ($this->makeOrder)([
        'orderStatus' => OrderStatus::makeFromHandle('shipped'),
        'orderPayments' => new ArrayCollection([$paidPayment]),
    ]);

    $this->expectException(InvalidOrderStatusChangeRequestedException::class);

    $order->markOrderPaid();
});
