<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Orders\Domain\Order;
use App\Services\Orders\Infrastructure\OrderRepository;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderService implements OrderServiceInterface
{

    private const string ORDER_REFERENCE_PATTERN = "O%d%s%d";
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}
    public function saveOrder(Order $order): Order
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        return $order;
    }

    public function convertCartToOrder(UserCart $userCart): Order
    {
        $productOrderLines = new ArrayCollection();
        $newOrderReference = $this->generateNewOrderReference($userCart->getUser());
        /*
        $order = new Order(
            orderReference:
        );*/
    }

    private function generateNewOrderReference(User $user, int $attempts = 1): string {
        $date = new \DateTime();

        $candidateOrderReference = sprintf(self::ORDER_REFERENCE_PATTERN, $user->getId(), $date->format('YmdHi'), $attempts);
        $existingOrder = $this->orderRepository->findByOrderReference($candidateOrderReference);
        dd($existingOrder);
    }
}
