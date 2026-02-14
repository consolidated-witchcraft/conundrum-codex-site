<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderChargeLine;
use App\Services\Orders\Domain\OrderProductLine;
use App\Services\Orders\Domain\ValueObjects\OrderChargeType;
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
    public function __construct(
        private readonly OrderRepository         $orderRepository,
        private readonly TaxServiceInterface     $taxService,
        private readonly PaymentServiceInterface $paymentService,
        EntityManagerInterface                   $entityManager
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
     */
    public function convertCartToOrder(UserCart $userCart, bool $flush = true): Order
    {
        $productOrderLines = new ArrayCollection();
        $chargeLines = new ArrayCollection();
        $payments = new ArrayCollection();
        $orderTaxes = new ArrayCollection();

        $order = new Order(
            productOrderLines: $productOrderLines,
            chargeLines: $chargeLines,
            orderPayments: $payments,
            user: $userCart->getUser(),
            orderTaxes: $orderTaxes,
        );

        foreach ($userCart->getUserCartLines() as $cartLine) {
            $orderLine = new OrderProductLine(
                order: $order,
                product: $cartLine->getProduct(),
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
            chargeType: OrderChargeType::makeFromHandle('service_charge'),
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
}
