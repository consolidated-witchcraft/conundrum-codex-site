<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderChargeLine;
use App\Services\Orders\Domain\OrderProductLine;
use App\Services\Orders\Domain\OrderReceipt;
use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Orders\Infrastructure\OrderReceiptRepository;
use App\Services\Orders\Infrastructure\OrderRepository;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Service\PaymentServiceInterface;
use App\Services\Tax\Service\Interfaces\TaxServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderService extends AbstractDomainEntityService implements OrderServiceInterface
{
    //Pattern  #ORDER_{userId}_{date}_{number}
    private const string ORDER_REFERENCE_PATTERN = 'ORDER_%d_%s_%d';

    private const string URL_SAFE_DATE_FORMAT  = 'Y-m-d_H-i';
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderReceiptRepository $orderReceiptRepository,
        private readonly TaxServiceInterface $taxService,
        private readonly PaymentServiceInterface $paymentService,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function saveOrder(Order $order): Order
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        return $order;
    }

    /**
     * @throws UnknownPaymentStatusException
     * @throws UnknownOrderStatusException
     */
    public function convertCartToOrder(UserCart $userCart, bool $flush = true): Order
    {
        $productOrderLines = new ArrayCollection();
        $chargeLines = new ArrayCollection();
        $payments = new ArrayCollection();
        $orderTaxes = new ArrayCollection();
        $orderReceipts = new ArrayCollection();
        $order = new Order(
            orderReference: $this->generateNewOrderReferenceForUser($userCart->getUser()),
            orderStatus: OrderStatus::makeFromHandle(OrderStatus::INVOICED_STATUS_HANDLE),
            productOrderLines: $productOrderLines,
            chargeLines: $chargeLines,
            orderPayments: $payments,
            orderReceipts: $orderReceipts,
            user: $userCart->getUser(),
            orderTaxes: $orderTaxes,
        );

        foreach ($userCart->getUserCartProductLines() as $cartLine) {
            $orderLine = new OrderProductLine(
                order: $order,
                productVariant: $cartLine->getProductVariant(),
                quantity: $cartLine->getQuantity()
            );
            $productOrderLines->add($orderLine);
        }
        $order->setChargeLines($this->generateChargeLinesForOrder($order));
        $order->setOrderTaxes($this->taxService->generateTaxesForOrder($order));
        $order->setOrderPayments($this->paymentService->generatePaymentsForOrder($order));
        $userCart->clearCart();
        $this->entityManager->persist($order);
        $this->entityManager->persist($userCart);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $order;
    }


    /**
     * @param Order $order
     * @return ArrayCollection<int, OrderChargeLine>
     * @throws \App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException
     */
    private function generateChargeLinesForOrder(Order $order): ArrayCollection {
        $chargeLines = new ArrayCollection();
        $orderCharge = new OrderChargeLine(
            chargeType: ChargeType::makeFromHandle('service_charge'),
            name: 'Service charge',
            order: $order,
            priceCentessimal: 100,
        );
        $chargeLines->add($orderCharge);
        return $chargeLines;
    }

    public function getOrderByReference(string $orderReference): ?Order
    {
        return $this->orderRepository->findByOrderReference($orderReference);
    }

    public function generateNewOrderReferenceForUser(User $user, int $attempts = 1): string
    {
        $date = new \DateTime();

        $candidateReference = sprintf(
            self::ORDER_REFERENCE_PATTERN,
            $user->getId(),
            $date->format(self::URL_SAFE_DATE_FORMAT),
            $attempts,
        );
        $existingOrder = $this->orderRepository->findByOrderReference($candidateReference);
        if ($existingOrder !== null) {
            return $this->generateNewOrderReferenceForUser($user, $attempts + 1);
        }
        return $candidateReference;
    }

    public function generateReceiptForOrder(Order $order): OrderReceipt
    {
        $date = new \DateTimeImmutable();
        $receipt = new OrderReceipt(
            receiptReference: $this->generateNewReceiptReferenceForOrder($order, $date),
            order: $order,
            amountCentessimal: $order->getTotalPostTaxCentessimal(),
            receiptDate: $date
        );
        $this->entityManager->persist($receipt);
        $this->entityManager->flush();
        return $receipt;
    }

    private function generateNewReceiptReferenceForOrder(Order $order, \DateTimeImmutable $date, int $attempts = 1): string {
        $candidateReference = sprintf(
            '%s_RECEIPT_%s_%d',
            $order->getOrderReference(),
            $date->format(self::URL_SAFE_DATE_FORMAT),
            $attempts
        );
        if ($this->orderReceiptRepository->findOneBy(['receiptReference' => $candidateReference]) !== null) {
            return $this->generateNewReceiptReferenceForOrder($order, $date, $attempts + 1);
        }
        return $candidateReference;
    }
}
