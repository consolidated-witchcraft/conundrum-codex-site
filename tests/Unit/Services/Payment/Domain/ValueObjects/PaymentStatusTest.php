<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;

dataset('payment_statuses', function () {
    yield 'unpaid' => ['unpaid', 'Unpaid'];
    yield 'partially_paid' => ['partially_paid', 'Partially Paid'];
    yield 'processing' => ['processing', 'Processing'];
    yield 'declined' => ['declined', 'Declined'];
    yield 'paid' => ['paid', 'Paid'];
});

test('make from handle', function (string $handle, string $name) {
    $paymentStatus = PaymentStatus::makeFromHandle($handle);

    self::assertSame($handle, $paymentStatus->getHandle());
    self::assertSame($name, $paymentStatus->getName());
    self::assertSame($name, (string) $paymentStatus);
})->with('payment_statuses');

test('status helpers reflect handle', function () {
    self::assertTrue(PaymentStatus::makeFromHandle('unpaid')->isUnpaid());
    self::assertTrue(PaymentStatus::makeFromHandle('partially_paid')->isPartiallyPaid());
    self::assertTrue(PaymentStatus::makeFromHandle('processing')->isProcessing());
    self::assertTrue(PaymentStatus::makeFromHandle('declined')->isDeclined());
    self::assertTrue(PaymentStatus::makeFromHandle('paid')->isPaid());
});

test('unknown status is rejected', function () {
    $this->expectException(UnknownPaymentStatusException::class);

    PaymentStatus::makeFromHandle('refunded');
});
