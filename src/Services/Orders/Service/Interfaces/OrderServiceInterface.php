<?php

namespace App\Services\Orders\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Orders\Domain\Order;
use App\Services\User\Domain\UserCart\UserCart;

interface OrderServiceInterface extends DomainServiceInterface
{
    public function saveOrder(Order $order): Order;

    public function convertCartToOrder(UserCart $userCart): Order;
}
