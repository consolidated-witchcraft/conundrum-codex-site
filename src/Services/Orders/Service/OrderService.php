<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Exceptions\UnknownOrderStatusException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderChargeLine;
use App\Services\Orders\Domain\OrderProductLine;
use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\Orders\Domain\ValueObjects\OrderStatus;
use App\Services\Orders\Infrastructure\OrderRepository;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;
use App\Services\Payment\Domain\PaymentReceipt;
use App\Services\Payment\Infrastructure\PaymentReceiptRepository;
use App\Services\Payment\Service\Interfaces\PaymentServiceInterface;
use App\Services\Tax\Service\Interfaces\TaxServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderService extends AbstractDomainEntityService implements OrderServiceInterface
{
    //Pattern  #ORDER_{userId}_{date}_{number}
    private const string ORDER_REFERENCE_PATTERN = 'O%s-%s';

    private const string URL_SAFE_DATE_FORMAT  = 'Y-m-d_H-i';
    public function __construct(
        private readonly OrderRepository          $orderRepository,
        private readonly TaxServiceInterface      $taxService,
        private readonly PaymentServiceInterface  $paymentService,
        EntityManagerInterface                    $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function saveOrder(Order $order, bool $flush = true): Order
    {
        $this->entityManager->persist($order);
        if ($flush) {
            $this->flush();
        }
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
        $order = new Order(
            orderReference: $this->generateNewOrderReference(),
            orderStatus: OrderStatus::makeFromHandle(OrderStatus::INVOICED_STATUS_HANDLE),
            productOrderLines: $productOrderLines,
            chargeLines: $chargeLines,
            orderPayments: $payments,
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

    public function generateNewOrderReference(int $attempts = 1): string
    {
        $currentOrderCount = $this->orderRepository->getCurrentOrderCount();

        $nextOrderDigit = $currentOrderCount + $attempts;
        $candidateOrderNumber = str_pad((string) $nextOrderDigit, 20, '0', STR_PAD_LEFT);
        $date = new \DateTime();
        $candidateOrderReference = sprintf(self::ORDER_REFERENCE_PATTERN, $candidateOrderNumber, $date->format(self::URL_SAFE_DATE_FORMAT));
        if ($this->getOrderByReference($candidateOrderReference) !== null) {
            return $this->generateNewOrderReference($attempts + 1);
        }

        return $candidateOrderReference;
    }

    /*
    public function generateReceiptForOrder(Order $order): PaymentReceipt
    {
        $date = new \DateTimeImmutable();
        $receipt = new PaymentReceipt(
            receiptReference: $this->generateNewReceiptReferenceForOrder($order, $date),
            payment: $order,
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
    }*/
}
