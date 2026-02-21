<?php

namespace App\Services\Orders\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Domain\OrderReceipt;
use App\Services\Payment\Domain\Payment;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;

interface OrderServiceInterface extends DomainEntityServiceInterface
{
    public function saveOrder(Order $order): Order;

    public function convertCartToOrder(UserCart $userCart): Order;

    public function getOrderByReference(string $orderReference): ?Order;

    public function generateNewOrderReferenceForUser(User $user, int $attempts = 1): string;

    public function generateReceiptForOrder(Order $order): OrderReceipt;
}
