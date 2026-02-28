<?php

declare(strict_types=1);

namespace App\Services\Payment\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;
use Doctrine\Common\Collections\ArrayCollection;

interface PaymentServiceInterface extends DomainEntityServiceInterface
{
    public function getPaymentByReference(string $paymentReference): ?Payment;

    public function savePayment(Payment $orderPayment, bool $flush = true): Payment;

    /**
     * @param Order $order
     * @return ArrayCollection<int, Payment>
     * @throws UnknownPaymentStatusException
     */
    public function generatePaymentsForOrder(Order $order): ArrayCollection;

    public function markPaymentUnpaid(Payment $payment, bool $flush = true): Payment;


    public function markPaymentProcessing(Payment $payment, bool $flush = true): Payment;

    public function markPaymentDeclined(Payment $payment, bool $flush = true): Payment;

    public function markPaymentPaid(Payment $payment, bool $flush = true): Payment;

    public function generateReceiptForPayment(Payment $payment, bool $flush = true): PaymentReceipt;

    public function getReceiptByReference(string $receiptReference): ?PaymentReceipt;

}
