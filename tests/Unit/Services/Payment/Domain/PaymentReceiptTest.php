<?php

declare(strict_types=1);

use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;

test('construct and set payment', function () {
    $payment = $this->createStub(Payment::class);
    $updatedPayment = $this->createStub(Payment::class);
    $receiptDate = new DateTimeImmutable('2026-03-16 12:00:00');

    $paymentReceipt = new PaymentReceipt(
        receiptReference: 'RECEIPT-123',
        payment: $payment,
        amountCentessimal: 1234,
        receiptDate: $receiptDate,
        id: 17,
    );

    self::assertSame('RECEIPT-123', $paymentReceipt->getReceiptReference());
    self::assertSame($payment, $paymentReceipt->getPayment());
    self::assertSame(1234, $paymentReceipt->getAmountCentessimal());
    self::assertSame(12.34, $paymentReceipt->getAmountDecimal());
    self::assertSame($receiptDate, $paymentReceipt->getReceiptDate());
    self::assertSame(17, $paymentReceipt->getId());

    $paymentReceipt->setPayment($updatedPayment);

    self::assertSame($updatedPayment, $paymentReceipt->getPayment());
});
