<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeOrder = function (): Order {
        return new Order(
            orderReference: 'ORDER-123',
            orderStatus: OrderStatus::makeFromHandle('invoiced'),
            productOrderLines: new ArrayCollection(),
            chargeLines: new ArrayCollection(),
            orderPayments: new ArrayCollection(),
            user: $this->createStub(User::class),
            orderTaxes: new ArrayCollection(),
            id: 77,
        );
    };

    $this->makePayment = function (array $overrides = []): Payment {
        return new Payment(
            name: $overrides['name'] ?? 'Card Payment',
            paymentAmountCentessimal: $overrides['paymentAmountCentessimal'] ?? 1250,
            paymentReference: $overrides['paymentReference'] ?? 'PAY-123',
            order: $overrides['order'] ?? ($this->makeOrder)(),
            paymentReceipts: $overrides['paymentReceipts'] ?? new ArrayCollection(),
            paymentStatus: $overrides['paymentStatus'] ?? null,
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct defaults to unpaid status', function () {
    $payment = ($this->makePayment)(['id' => 22]);

    self::assertSame('Card Payment', $payment->getName());
    self::assertSame(1250, $payment->getPaymentAmountCentessimal());
    self::assertSame(12.5, $payment->getPaymentAmountDecimal());
    self::assertSame('PAY-123', $payment->getPaymentReference());
    self::assertSame('unpaid', $payment->getPaymentStatus()->getHandle());
    self::assertSame(22, $payment->getId());
});

test('add receipt attaches it to the payment', function () {
    $payment = ($this->makePayment)();
    $receipt = new PaymentReceipt(
        receiptReference: 'RECEIPT-123',
        payment: $this->createStub(Payment::class),
        amountCentessimal: 1250,
        receiptDate: new DateTimeImmutable('2026-03-16 12:00:00'),
    );

    $payment->addReceipt($receipt);

    self::assertCount(1, $payment->getPaymentReceipts());
    self::assertSame($payment, $receipt->getPayment());
});

test('payment status transitions follow valid flow', function () {
    $payment = ($this->makePayment)();

    $payment->markProcessing();
    self::assertSame('processing', $payment->getPaymentStatus()->getHandle());

    $payment->markPaid();
    self::assertSame('paid', $payment->getPaymentStatus()->getHandle());

    $payment->markUnpaid();
    self::assertSame('unpaid', $payment->getPaymentStatus()->getHandle());
});

test('payment can be declined from processing', function () {
    $payment = ($this->makePayment)();

    $payment->markProcessing();
    $payment->markDeclined();

    self::assertSame('declined', $payment->getPaymentStatus()->getHandle());
});

test('invalid payment status transition is rejected', function () {
    $payment = ($this->makePayment)([
        'paymentStatus' => PaymentStatus::makeFromHandle('paid'),
    ]);

    $this->expectException(OrderPaymentInvalidStatusChangeException::class);

    $payment->markProcessing();
});
