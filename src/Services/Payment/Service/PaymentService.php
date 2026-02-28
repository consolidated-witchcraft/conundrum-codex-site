<?php

declare(strict_types=1);

namespace App\Services\Payment\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\PaymentReceipt;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Infrastructure\PaymentReceiptRepository;
use App\Services\Payment\Infrastructure\PaymentRepository;
use App\Services\Payment\Service\Interfaces\PaymentServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService extends AbstractDomainEntityService implements PaymentServiceInterface
{

    private const string RECEIPT_REFERENCE_PATTERN = "%s_RECEIPT_%d";

    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly PaymentReceiptRepository $paymentReceiptRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }
    public function getPaymentByReference(string $paymentReference): ?Payment
    {
        return $this->paymentRepository->findOneBy(['paymentReference' => $paymentReference]);
    }

    public function savePayment(Payment $orderPayment, bool $flush = true): Payment
    {
        $this->entityManager->persist($orderPayment);
        if ($flush) {
            $this->flush();
        }
        return $orderPayment;
    }

    public function generatePaymentsForOrder(Order $order): ArrayCollection
    {
        $payments = new ArrayCollection();
        $payment = new Payment(
            name: "Total",
            paymentAmountCentessimal: $order->getTotalPostTaxCentessimal(),
            paymentReference: $this->generateNewHumanReadablePaymentReferenceForOrder($order),
            order: $order,
            paymentStatus: PaymentStatus::makeFromHandle('unpaid'),
        );
        $payments->add($payment);
        return $payments;
    }

    private function generateNewHumanReadablePaymentReferenceForOrder(Order $order, int $attempts = 1): string {
        $paymentReference = sprintf("%s_PAYMENT_%d", $order->getOrderReference(), $attempts);
        if ($this->getPaymentByReference($paymentReference) !== null) {
            return $this->generateNewHumanReadablePaymentReferenceForOrder($order, $attempts +1);
        }
        return $paymentReference;
    }

    public function markPaymentUnpaid(Payment $payment, bool $flush = true): Payment
    {
        $payment->markUnpaid();
        $this->entityManager->persist($payment);
        if ($flush) {
            $this->flush();
        }
        return $payment;
    }


    public function markPaymentProcessing(Payment $payment, bool $flush = true): Payment
    {
        $payment->markProcessing();
        $this->entityManager->persist($payment);
        if ($flush) {
            $this->flush();
        }
        return $payment;
    }

    public function markPaymentDeclined(Payment $payment, bool $flush = true): Payment
    {
        $payment->markDeclined();
        $this->entityManager->persist($payment);
        if ($flush) {
            $this->flush();
        }
        return $payment;
    }

    public function markPaymentPaid(Payment $payment, bool $flush = true): Payment
    {
        $payment->markPaid();
        $this->entityManager->persist($payment);
        if ($flush) {
            $this->flush();
        }
        return $payment;
    }

    public function generateReceiptForPayment(Payment $payment, bool $flush = true): PaymentReceipt
    {
        $receipt = new PaymentReceipt(
            receiptReference: $this->generateReceiptReferenceForPayment($payment),
            payment: $payment,
            amountCentessimal: $payment->getPaymentAmountCentessimal(),
            receiptDate: new \DateTimeImmutable(),
        );
        $payment->addReceipt($receipt);
        $this->entityManager->persist($receipt);
        $this->entityManager->persist($payment);
        if ($flush) {
            $this->flush();
        }
        return $receipt;
    }

    private function generateReceiptReferenceForPayment(Payment $payment, int $attempts = 1): string {
        $candidateReference = sprintf(self::RECEIPT_REFERENCE_PATTERN, $payment->getPaymentReference(), $attempts);
        if ( $this->getReceiptByReference($candidateReference) !== null) {
            return $this->generateReceiptReferenceForPayment($payment, $attempts + 1);
        }
        return $candidateReference;
    }

    public function getReceiptByReference(string $receiptReference): ?PaymentReceipt
    {
        return $this->paymentReceiptRepository->findByReceiptReference($receiptReference);
    }
}
