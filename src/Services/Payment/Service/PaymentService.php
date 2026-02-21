<?php

declare(strict_types=1);

namespace App\Services\Payment\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Infrastructure\PaymentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class PaymentService extends AbstractDomainEntityService implements PaymentServiceInterface
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
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
            paymentReference: $this->generateNewHumanReadablePaymentReferenceForOrder($order),
            name: "Total",
            paymentAmountCentessimal: $order->getTotalPostTaxCentessimal(),
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
}
