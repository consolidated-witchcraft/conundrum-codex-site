<?php

declare(strict_types=1);

namespace App\Services\Payment\Service;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\Payment;
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
}
